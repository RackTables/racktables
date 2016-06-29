<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

// *********************  Config-generating functions  *********************

$parser_class = 'MacroParser';

// Returns array of triplets:
//triplet = array
//(
//	'ports' => $db_port_row,
//	'vips' => $db_ip_row,
//	'object_id' => $object_id,
//	'vs_id' => $vs_id,
//	'rspool_id' => $rspool_id,
//)
function getTriplets ($cell)
{
	$filter_fields = array();
	$order_fields = array();
	switch ($cell['realm'])
	{
		case 'object':
			$filter_fields['object_id'] = $cell['id'];
			$order_fields[] = 'vs_id';
			break;
		case 'ipvs':
			$filter_fields['vs_id'] = $cell['id'];
			$order_fields[] = 'rspool_id';
			break;
		case 'ipv4rspool':
			$filter_fields['rspool_id'] = $cell['id'];
			$order_fields[] = 'vs_id';
			break;
		default:
			throw new InvalidArgException ('realm', $cell['realm']);
	}
	return fetchTripletRows ($filter_fields, $order_fields);
}

function fetchTripletRows ($filter_fields, $order_fields = array())
{
	$order = count ($order_fields) ? "ORDER BY " . implode (',', $order_fields) : '';
	$filter = 'TRUE';
	$params = array();
	foreach ($filter_fields as $key => $value)
	{
		$filter .= " AND `$key` = ?";
		$params[] = $value;
	}

	$triplets = array();
	foreach (array ('ports' => 'VSEnabledPorts', 'vips' => 'VSEnabledIPs') as $key => $table)
	{
		$result = usePreparedSelectBlade ("SELECT * FROM $table WHERE $filter $order", $params);
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
		{
			$data = $row;
			unset ($data['object_id']);
			unset ($data['vs_id']);
			unset ($data['rspool_id']);
			$triplet_key = implode ('-', array ($row['vs_id'], $row['rspool_id'], $row['object_id']));
			if (! isset ($triplets[$triplet_key]))
				$triplets[$triplet_key] = array
				(
					'vips' => array(),
					'ports' => array(),
				);
			$triplets[$triplet_key][$key][] = $data;
			$triplets[$triplet_key]['vs_id'] = $row['vs_id'];
			$triplets[$triplet_key]['object_id'] = $row['object_id'];
			$triplets[$triplet_key]['rspool_id'] = $row['rspool_id'];
		}
		unset ($result);
	}
	return $triplets;
}

function getTriplet ($object_id, $vs_id, $rspool_id)
{
	return array_first (fetchTripletRows (
		array ('object_id' => $object_id, 'vs_id' => $vs_id, 'rspool_id' => $rspool_id)
	));
}

function generateVSSection ($vs_parser)
{
	$ret = $vs_parser->expand (
"	protocol %PROTO%
	%GLOBAL_VS_CONF%
	%RSP_VS_CONF%
	%VS_VS_CONF%
	%PORT_VS_CONF%
	%VIP_VS_CONF%
	%SLB_PORT_VS_CONF%
	%SLB_VIP_VS_CONF%
");
	$rs_count = 0;
	$family_length = ($vs_parser->expandMacro ('IP_VER') == 6) ? 16 : 4;
	foreach ($vs_parser->getRSList() as $rs)
	{
		if ($rs['inservice'] != 'yes')
			continue;
		$parser = clone $vs_parser;
		$parser->addMacro ('RSIP', $rs['rsip']);
		if (isset ($rs['rsport']))
			$parser->addMacro ('RSPORT', $rs['rsport']);
		$parser->addMacro ('RS_COMMENT', $rs['comment']);
		$parser->addMacro ('RS_RS_CONF', $rs['rsconfig']);

		// do not add v6 reals into v4 service and vice versa
		$rsip_bin = ip_checkparse ($parser->expandMacro ('RSIP'));
		if ($rsip_bin !== FALSE && strlen ($rsip_bin) == $family_length)
			foreach (explode (',', $parser->expandMacro ('RSPORT')) as $rsp_token)
			{
				$port_range = explode ('-', $rsp_token);
				if (count ($port_range) < 1)
					throw new InvalidArgException ('RSPORT', $rsp_token, "invalid RS port range");
				if (count ($port_range) < 2)
					$port_range[] = $port_range[0];
				if ($port_range[0] > $port_range[1])
					throw new InvalidArgException ('RSPORT', $rsp_token, "invalid RS port range");

				for ($rsport = $port_range[0]; $rsport <= $port_range[1]; $rsport++)
				{
					$rs_count++;
					$rs_parser = clone $parser;
					$rs_parser->addMacro ('RSPORT', $rsport);
					$ret .= $rs_parser->expand ("
	%RS_PREPEND%
	real_server %RS_HEADER% {
		%GLOBAL_RS_CONF%
		%VS_RS_CONF%
		%RSP_RS_CONF%
		%VIP_RS_CONF%
		%PORT_RS_CONF%
		%SLB_VIP_RS_CONF%
		%SLB_PORT_RS_CONF%
		%RS_RS_CONF%
	}
");
				}
			}
	}
	return $rs_count ? $ret : '';
}

function generateSLBConfig2 ($triplet_list)
{
	$ret = '';

	global $parser_class;
	$gl_parser = new $parser_class;
	$defaults = getSLBDefaults (TRUE);
	$gl_parser->addMacro ('GLOBAL_VS_CONF', dos2unix ($defaults['vsconfig']));
	$gl_parser->addMacro ('GLOBAL_RS_CONF', dos2unix ($defaults['rsconfig']));
	$gl_parser->addMacro ('RSPORT', '%VPORT%');
	$gl_parser->addMacro ('VS_PREPEND',
"# LB (id == %LB_ID%): %LB_NAME%
# VSG (id == %VSG_ID%): %VS_NAME%
# RS (id == %RSP_ID%): %RSP_NAME%");

	// group triplets by object_id, vs_id
	$grouped = array();
	foreach ($triplet_list as $triplet)
		$grouped[$triplet['object_id']][$triplet['vs_id']][] = $triplet;

	foreach ($grouped as $object_id => $subarr)
	{
		$seen_vs_groups = array();
		$lb_parser = clone $gl_parser;
		$lb_cell = spotEntity ('object', $object_id);
		$lb_parser->addMacro ('LB_ID', $lb_cell['id']);
		$lb_parser->addMacro ('LB_NAME', $lb_cell['name']);

		foreach ($subarr as $vs_id => $triplets)
		{
			$vs_parser = clone $lb_parser;
			$vs_cell = spotEntity ('ipvs', $vs_id);
			if (! isset ($vs_cell['ports']) || ! isset ($vs_cell['vips']))
				amplifyCell ($vs_cell);
			$vs_parser->addMacro ('VS_ID', $vs_cell['id']);
			$vs_parser->addMacro ('VSG_ID', $vs_cell['id']);
			$vs_parser->addMacro ('VS_NAME', $vs_cell['name']);
			$vs_parser->addMacro ('VS_RS_CONF', dos2unix ($vs_cell['rsconfig']));

			foreach ($triplets as $triplet)
			{
				$virtual_services = array();
				$tr_parser = clone $vs_parser;
				$rs_cell = spotEntity ('ipv4rspool', $triplet['rspool_id']);
				$tr_parser->addMacro ('RSP_ID', $rs_cell['id']);
				$tr_parser->addMacro ('RSP_NAME', $rs_cell['name']);
				$tr_parser->addMacro ('RSP_VS_CONF', dos2unix ($rs_cell['vsconfig']));
				$tr_parser->addMacro ('RSP_RS_CONF', dos2unix ($rs_cell['rsconfig']));
				$tr_parser->addMacro ('VS_VS_CONF', dos2unix ($vs_cell['vsconfig'])); // VS-driven vsconfig has higher priority than RSP-driven

				foreach ($triplet['ports'] as $port_row)
				{
					$is_mark = ($port_row['proto'] == 'MARK');
					$p_parser = clone $tr_parser;
					$p_parser->addMacro ('VS_HEADER', $is_mark ? 'fwmark %MARK%' : '%VIP% %VPORT%');
					$p_parser->addMacro ('PROTO', $is_mark ? 'TCP' : $port_row['proto']);
					$p_parser->addMacro ($is_mark ? 'MARK' : 'VPORT', $port_row['vport']);
					foreach ($vs_cell['ports'] as $vport)
						if ($vport['vport'] == $port_row['vport'] && $vport['proto'] == $port_row['proto'])
						{
							$p_parser->addMacro ('PORT_VS_CONF', dos2unix ($vport['vsconfig']));
							$p_parser->addMacro ('PORT_RS_CONF', dos2unix ($vport['rsconfig']));
							break;
						}
					$p_parser->addMacro ('SLB_PORT_VS_CONF', dos2unix ($port_row['vsconfig']));
					$p_parser->addMacro ('SLB_PORT_RS_CONF', dos2unix ($port_row['rsconfig']));

					if ($is_mark)
					{
						$p_parser->addMacro ('RS_HEADER', '%RSIP%');
						// find enabled IP families to fill IP_VER
						$seen_families = array();
						foreach ($triplet['vips'] as $ip_row)
						{
							$family_length = strlen ($ip_row['vip']);
							$seen_families[$family_length] = ($family_length == 16 ? 6 : 4);
						}
						if (! $seen_families)
							$seen_families['unknown'] = '';
						foreach ($seen_families as $ip_ver)
						{
							$fam_parser = clone $p_parser;
							if ($ip_ver)
								$fam_parser->addMacro ('IP_VER', $ip_ver);
							if ('' != $vs_config = generateVSSection ($fam_parser))
								$virtual_services["IPv${ip_ver} " . $fam_parser->expandMacro ('VS_HEADER')] = $vs_config;
						}
					}
					else
					{
						$p_parser->addMacro ('RS_HEADER', '%RSIP% %RSPORT%');
						foreach ($triplet['vips'] as $ip_row)
						{
							$ip_parser = clone $p_parser;
							$ip_parser->addMacro ('VIP', ip_format ($ip_row['vip']));
							$ip_parser->addMacro ('IP_VER', (strlen ($ip_row['vip']) == 16) ? 6 : 4);
							$ip_parser->addMacro ('PRIO', $ip_row['prio']);
							foreach ($vs_cell['vips'] as $vip)
								if ($vip['vip'] === $ip_row['vip'])
								{
									$ip_parser->addMacro ('VIP_VS_CONF', dos2unix ($vip['vsconfig']));
									$ip_parser->addMacro ('VIP_RS_CONF', dos2unix ($vip['rsconfig']));
									break;
								}
							$ip_parser->addMacro ('SLB_VIP_VS_CONF', dos2unix ($ip_row['vsconfig']));
							$ip_parser->addMacro ('SLB_VIP_RS_CONF', dos2unix ($ip_row['rsconfig']));
							if ('' != $vs_config = generateVSSection ($ip_parser))
								$virtual_services[$port_row['proto'] . " " . $ip_parser->expandMacro ('VS_HEADER')] = $vs_config;
						} // vips
					}
				} //ports

				// group multiple virtual_services into vs_groups
				$groups = array();
				foreach ($virtual_services as $key => $content)
					$groups[$content][] = preg_replace ('/^(TCP|UDP|IPv[46]?)\s+/', '', $key);
				foreach ($groups as $content => $keys)
				{
					if (NULL !== ($new_content = callHook ('generateSLBConfig_stage2', $content, $keys)))
						$content = $new_content;
					$ret .= $tr_parser->expand ("\n%VS_PREPEND%\n");
					if (count ($keys) == 1)
						$ret .= "virtual_server " . array_first ($keys) . " {\n" . $content . "}\n";
					else
					{
						// come up with the name for new VS group
						$vsg_name = makeUniqueVSGName ($seen_vs_groups, $keys, $vs_cell);
						$seen_vs_groups[$vsg_name] = 1;
						$tr_parser->addMacro ('VSG_NAME', $vsg_name);

						$ret .= $tr_parser->expand ("virtual_server_group %VSG_NAME% {\n");
						foreach ($keys as $vs_header)
							$ret .= "\t$vs_header\n";
						$ret .= "}\n";
						$ret .= $tr_parser->expand ("virtual_server group %VSG_NAME% {\n");
						$ret .= $content . "}\n";
					}
				}
			} // triplets
		} // vs
	} // balancers
	return $ret;
}

function makeUniqueVSGName ($seen_names, $keys, $vs_cell)
{
	$seen_ports = array();
	$seen_marks = array();
	sort ($keys);
	foreach ($keys as $key)
		if (preg_match('/^fwmark\s+(\d+)$/', $key, $m))
			$seen_marks[$m[1]] = $m[1];
		elseif (preg_match('/^[0-9a-fA-F:.]+\s+(\d+)$/', $key, $m))
			$seen_ports[$m[1]] = $m[1];
	$base_name = preg_replace('/\s+/', '_', $vs_cell['name']);

	$vsg_name = NULL;
	if (! isset ($vsg_name) && count ($seen_ports) == 1)
	{
		$cname = $base_name . '_' . array_first ($seen_ports);
		if (! array_key_exists ($cname, $seen_names))
			$vsg_name = $cname;
	}
	if (! isset ($vsg_name) && count ($seen_marks))
	{
		$cname = $base_name . '_fwm' . implode('_fwm', $seen_marks);
		if (! array_key_exists ($cname, $seen_names))
			$vsg_name = $cname;
	}
	if (! isset ($vsg_name))
	{
		$cname = $base_name;
		if (count ($seen_ports))
			$cname .= '_' . implode('_', $seen_ports);
		if (count ($seen_marks))
			$cname .= '_fwm' . implode('_fwm', $seen_marks);
		if (! array_key_exists ($cname, $seen_names))
			$vsg_name = $cname;
		else
		{
			$cname .= '_' . substr (sha1 (serialize ($keys)), 0, 6);
			if (! array_key_exists ($cname, $seen_names))
				$vsg_name = $cname;
		}
	}

	if (! isset ($vsg_name))
		throw new RackTablesError ("Could not produce unique VS group name for ${vs_cell['name']}", RackTablesError::INTERNAL);
	return $vsg_name;
}

// $vs_list is array of VS text configs, indexed by VS headers
// function returns a list of groups. Services with equal configs are grouped together.
// Each group has one or more VS headers in 'keys' subarray, and 'content' field.

function groupVS ($vs_list)
{
	$ret = array();
	$tmp = array();
	foreach ($vs_list as $key => $content)
		$tmp[$content][] = $key;
	foreach ($tmp as $content => $keys)
		$ret[] = array ('keys' => $keys, 'content' => $content);
	return $ret;
}

// returns list item or FALSE
function isPortEnabled ($port, $port_list)
{
	foreach ($port_list as $i_port)
		if ($i_port['proto'] == $port['proto'] && $i_port['vport'] == $port['vport'])
			return $i_port;
	return FALSE;
}

// returns list item or FALSE
function isVIPEnabled ($vip, $vip_list)
{
	foreach ($vip_list as $i_vip)
		if ($i_vip['vip'] === $vip['vip'])
			return $i_vip;
	return FALSE;
}

// returns list of ipv4vs ids that have one of the IPs or fwmarks of group $group_id
function getVSIDsByGroup ($group_id)
{
	$ret = array();
	$vsinfo = spotEntity ('ipvs', $group_id);
	amplifyCell ($vsinfo);
	if (count ($vsinfo['vips']))
	{
		$ips = reduceSubarraysToColumn ($vsinfo['vips'], 'vip');
		$qm = questionMarks (count ($ips));
		$result = usePreparedSelectBlade ("SELECT id FROM IPv4VS WHERE vip IN ($qm) ORDER BY vip", $ips);
		$ret = array_merge ($ret, $result->fetchAll (PDO::FETCH_COLUMN, 0));
		unset ($result);
	}

	$bin_marks = array();
	foreach ($vsinfo['ports'] as $port)
		if ($port['proto'] == 'MARK')
			$bin_marks[] = pack ('N', $port['vport']);
	if (count ($bin_marks))
	{
		$qm = questionMarks (count ($bin_marks));
		$result = usePreparedSelectBlade ("SELECT id FROM IPv4VS WHERE proto = 'MARK' AND vip IN ($qm) ORDER BY vip", $bin_marks);
		$ret = array_merge ($ret, $result->fetchAll (PDO::FETCH_COLUMN, 0));
	}

	return $ret;
}

function concatConfig (&$config, $line)
{
	if ($config != '')
		$config .= "\n";
	$config .= $line;
}

// splits $text to configuration directives (lines)
// each macro declaration is one configuration directive
// empty lines are skipped, trailing spaces are cut.
function tokenizeConfig ($text)
{
	$ret = array();
	$pos = 0;
	$len = strlen ($text);
	$state = 0;
	while ($pos < $len || $state != 0)
		switch ($state)
		{
			case 0:
				if (preg_match ('/[?:]?=`?|\n/s', $text, $m, PREG_OFFSET_CAPTURE, $pos))
				{
					if ($m[0][0] == "\n")
					{
						$ret[] = substr ($text, $pos, $m[0][1] - $pos);
						$pos = $m[0][1] + 1; // skip \n
					}
					else
					{
						$macro_name = substr ($text, $pos, $m[0][1] - $pos);
						if (preg_match('/^[a-zA-Z0-9_]+$/', $macro_name))
						{
							$assignment_start = $pos;
							$pos = $m[0][1] + 1;
							$state = $m[0][0][strlen ($m[0][0]) - 1] == '`' ? 1 : 2;
						}
					}
				}
				else
				{
					$ret[] = substr ($text, $pos);
					break 2;
				}
				break;
			case 1:
				if (FALSE === ($i = strpos ($text, "'", $pos)))
					break 2;
				else
				{
					$ret[$macro_name] = substr ($text, $assignment_start, $i - $assignment_start + 1);
					$pos = $i + 1;
					$state = 0;
				}
				break;
			case 2:
				if (FALSE === ($i = strpos ($text, "\n", $pos)))
				{
					$ret[$macro_name] = substr ($text, $assignment_start);
					break 2;
				}
				else
				{
					$ret[$macro_name] = substr ($text, $assignment_start, $i - $assignment_start);
					$pos = $i + 1;
					$state = 0;
				}
				break;
		}

	return array_diff (array_map ('rtrim', $ret), array (''));
}

// returns array with keys: 'properties' 'ports', 'vips', 'triplets'
function buildVSMigratePlan ($new_vs_id, $vs_id_list)
{
	$ret = array
	(
		'properties' => array ('vsconfig' => '', 'rsconfig' => ''),
		'ports' => array(),
		'vips' => array(),
		'triplets' => array(),
		'messages' => array(), // keys are $old_tr_key
	);
	$config_stat = array('vsconfig' => array(), 'rsconfig' => array());
	$gt = array(); // grouped triplets

	foreach ($vs_id_list as $vs_id)
	{
		$vsinfo = spotEntity ('ipv4vs', $vs_id);

		// create nesessary vips and ports
		if ($vsinfo['proto'] != 'MARK')
		{
			$vip_key = $vsinfo['vip_bin'];
			$port_key = $vsinfo['proto'] . '-' . $vsinfo['vport'];
			$ret['vips'][$vip_key] = array ('vip' => $vsinfo['vip_bin'], 'vsconfig' => '', 'rsconfig' => '');
			$ret['ports'][$port_key] = array ('proto' => $vsinfo['proto'], 'vport' => $vsinfo['vport'], 'vsconfig' => '', 'rsconfig' => '');
		}
		else
		{
			$vip_key = '';
			$mark = implode('', unpack ('N', $vsinfo['vip_bin']));
			$port_key = $vsinfo['proto'] . '-' . $mark;
			$ret['ports'][$port_key] = array ('proto' => $vsinfo['proto'], 'vport' => $mark, 'vsconfig' => '', 'rsconfig' => '');
		}

		// fill triplets
		foreach (SLBTriplet::getTriplets ($vsinfo) as $triplet)
		{
			$tr_key = $triplet->lb['id'] . '-' . $triplet->rs['id'];
			if (! isset ($ret['triplets'][$tr_key]))
				$ret['triplets'][$tr_key] = array
				(
					'object_id' => $triplet->lb['id'],
					'rspool_id' => $triplet->rs['id'],
					'vs_id' => $new_vs_id,
					'vips' => array(),
					'ports' => array(),
				);

			$configs = array
			(
				'vsconfig' => tokenizeConfig ($triplet->vs['vsconfig'] . "\n" . $triplet->slb['vsconfig']),
				'rsconfig' => tokenizeConfig ($triplet->vs['rsconfig'] . "\n" . $triplet->slb['rsconfig']),
			);

			if ($vsinfo['proto'] != 'MARK')
			{
				if (! isset ($ret['triplets'][$tr_key]['ports'][$port_key]))
					$ret['triplets'][$tr_key]['ports'][$port_key] = array ('proto' => $vsinfo['proto'], 'vport' => $vsinfo['vport'], 'vsconfig' => '', 'rsconfig' => '');
				if (! isset ($ret['triplets'][$tr_key]['vips'][$vip_key]))
					$ret['triplets'][$tr_key]['vips'][$vip_key] = array ('vip' => $vsinfo['vip_bin'], 'prio' => NULL, 'vsconfig' => '', 'rsconfig' => '');
				if ('' != $triplet->slb['prio'])
					$ret['triplets'][$tr_key]['vips'][$vip_key]['prio'] = $triplet->slb['prio'];
			}
			else
				$ret['triplets'][$tr_key]['ports'][$port_key] = array ('proto' => $vsinfo['proto'], 'vport' => $mark, 'vsconfig' => '', 'rsconfig' => '');

			$old_tr_key = $tr_key . '-' . $vip_key . '-' . $port_key;
			$gt['all'][$old_tr_key] = $triplet;
			$gt['ports'][$port_key][$old_tr_key] = $triplet;
			$gt['vips'][$vip_key][$old_tr_key] = $triplet;
			$gt['vip_links'][$tr_key][$vip_key][$old_tr_key] = $triplet;
			$gt['port_links'][$tr_key][$port_key][$old_tr_key] = $triplet;

			foreach ($configs as $conf_type => $list)
				foreach ($list as $line)
					$config_stat[$conf_type][$line][$old_tr_key] = $triplet;
		}
	}

	// reduce common config lines and move them from $config_stat into $ret
	foreach ($config_stat as $conf_type => $stat)
		foreach ($stat as $line => $used_in_triplets)
		{
			$added_to_triplets = array();
			$wrong_triplets = array();

			if (! array_diff_key ($gt['all'], $used_in_triplets))
			{
				// line used in all triplets
				concatConfig ($ret['properties'][$conf_type], $line);
				continue;
			}

			foreach ($gt['ports'] as $port_key => $port_triplets)
			{
				$diff = array_diff_key ($port_triplets, $used_in_triplets);
				if (count ($diff) < count ($port_triplets) / 2)
				{
					// line used in most triplets of this port
					$added_to_triplets += $port_triplets;
					$wrong_triplets += $diff;
					concatConfig ($ret['ports'][$port_key][$conf_type], $line);
				}
			}

			foreach ($gt['vips'] as $vip_key => $vip_triplets)
				if (! array_diff_key ($vip_triplets, $used_in_triplets))
					if (count ($vip_triplets) == count (array_diff_key ($vip_triplets, $added_to_triplets)))
					{
						// if none of the $vip_triplets are in $added_to_triplets,
						// line used in all triplets of this vip
						$added_to_triplets += $vip_triplets;
						concatConfig ($ret['vips'][$vip_key][$conf_type], $line);
					}

			foreach ($used_in_triplets as $old_tr_key => $triplet)
			{
				if (isset ($added_to_triplets[$old_tr_key]))
					continue;
				$tr_key = $triplet->lb['id'] . '-' . $triplet->rs['id'];
				if ($triplet->vs['proto'] != 'MARK')
				{
					$vip_key = $triplet->vs['vip_bin'];
					$port_key = $triplet->vs['proto'] . '-' . $triplet->vs['vport'];
				}
				else
				{
					$vip_key = '';
					$port_key = $triplet->vs['proto'] . '-' . implode ('', unpack ('N', $triplet->vs['vip_bin']));
				}
				// if all the triplets for a given port contain line, add it to the ports' config
				if (! array_diff_key ($gt['port_links'][$tr_key][$port_key], $used_in_triplets))
					if (count ($gt['port_links'][$tr_key][$port_key]) == count (array_diff_key ($gt['port_links'][$tr_key][$port_key], $added_to_triplets)))
					{
						$added_to_triplets += $gt['port_links'][$tr_key][$port_key];
						concatConfig ($ret['triplets'][$tr_key]['ports'][$port_key][$conf_type], $line);
					}

				// if all the triplets for a given vip contain line, add it to the vips' config
				if ($vip_key != '')
					if (! array_diff_key ($gt['vip_links'][$tr_key][$vip_key], $used_in_triplets))
						if (count ($gt['vip_links'][$tr_key][$vip_key]) == count (array_diff_key ($gt['vip_links'][$tr_key][$vip_key], $added_to_triplets)))
						{
							$added_to_triplets += $gt['vip_links'][$tr_key][$vip_key];
							concatConfig ($ret['triplets'][$tr_key]['vips'][$vip_key][$conf_type], $line);
						}
			}

			// check for failed-to-insert lines
			foreach (array_diff_key ($used_in_triplets, $added_to_triplets) as $old_tr_key => $unused_triplet)
				$ret['messages'][$old_tr_key][] = "Failed to add $conf_type line '$line'";
			foreach ($wrong_triplets as $old_tr_key => $triplet)
				$ret['messages'][$old_tr_key][] = "Added $conf_type line '$line'";
		} // for $line

	return $ret;
}

function commitDeleteVSG ($id)
{
	releaseFiles ('ipvs', $id);
	destroyTagsForEntity ('ipvs', $id);
	usePreparedDeleteBlade ('VS', array ('id' => $id));
}

// returns an array of object_ids that have links to a given VS
// may be used as 'refcnt'
function getVSLinkedObjects ($vs_id)
{
	$result = usePreparedSelectBlade ("SELECT DISTINCT object_id FROM VSEnabledIPs WHERE vs_id = ? UNION SELECT DISTINCT object_id FROM VSEnabledPorts WHERE vs_id = ?", array ($vs_id, $vs_id));
	return array_unique ($result->fetchAll (PDO::FETCH_COLUMN, 0));
}

// prevent linking two identical ip:port pairs, or two identical fwmarks to object
function addSLBPortLink ($link_row)
{
	global $dbxlink;
	$do_transaction = !isTransactionActive();
	if ($do_transaction)
		$dbxlink->beginTransaction();

	// lock on port
	$result = usePreparedSelectBlade
	(
		"SELECT vs_id, proto, vport FROM VSPorts WHERE proto = ? AND vport = ? FOR UPDATE",
		array ($link_row['proto'], $link_row['vport'])
	);
	unset ($result);

	// if this is a fwmark port, assure it is single link with given tag on that balancer
	if ($link_row['proto'] == 'MARK')
	{
		$result = usePreparedSelectBlade
		(
			"SELECT COUNT(*) FROM VSEnabledPorts WHERE object_id = ? AND proto = ? AND vport = ?",
			array ($link_row['object_id'], $link_row['proto'], $link_row['vport'])
		);
		if (0 < $result->fetchColumn())
		{
			if ($do_transaction)
				$dbxlink->rollBack();
			throw new RTDatabaseError ("Duplicate link of fwmark " . $link_row['vport'] . " to object #" . $link_row['object_id']);
		}
		unset ($result);
	}
	$ret = usePreparedInsertBlade ('VSEnabledPorts', $link_row);
	if ($link_row['proto'] != 'MARK')
	{
		$result = usePreparedSelectBlade
		(
			"SELECT vip FROM VSEnabledPorts vep INNER JOIN VSEnabledIPs vei USING (vs_id, object_id, rspool_id)
			WHERE vep.object_id = ? AND proto = ? AND vport = ?
			GROUP BY vip HAVING COUNT(distinct vip) != COUNT(vip)",
			array ($link_row['object_id'], $link_row['proto'], $link_row['vport'])
		);
		if ($row = $result->fetch (PDO::FETCH_ASSOC, 0))
		{
			unset ($result);
			if ($do_transaction)
				$dbxlink->rollBack();
			throw new RTDatabaseError (sprintf ("Duplicate link of %s [%s]:%s to object #%d", $link_row['proto'], ip_format ($row['vip']), $link_row['vport'], $link_row['object_id']));
		}
	}
	if ($do_transaction)
		$dbxlink->commit();
	return $ret;
}

// prevent linking two identical ip:port pairs to object
function addSLBIPLink ($link_row)
{
	global $dbxlink;
	$do_transaction = !isTransactionActive();
	if ($do_transaction)
		$dbxlink->beginTransaction();

	// lock on vip
	$result = usePreparedSelectBlade
	(
		"SELECT vs_id, vip FROM VSIPs WHERE vip = ? FOR UPDATE",
		array ($link_row['vip'])
	);
	unset ($result);

	$ret = usePreparedInsertBlade ('VSEnabledIPs', $link_row);
	$result = usePreparedSelectBlade
	(
		"SELECT proto, vport FROM VSEnabledPorts vep INNER JOIN VSEnabledIPs vei USING (vs_id, object_id, rspool_id) WHERE vei.object_id = ? AND vip = ? HAVING COUNT(distinct proto,vport) != COUNT(vport)",
		array ($link_row['object_id'], $link_row['vip'])
	);
	if ($row = $result->fetch (PDO::FETCH_ASSOC, 0))
	{
		unset ($result);
		if ($do_transaction)
			$dbxlink->rollBack();
		throw new RTDatabaseError (sprintf ("Duplicate link of %s [%s]:%s to object #%d", $row['proto'], ip_format ($link_row['vip']), $row['vport'], $link_row['object_id']));
	}
	if ($do_transaction)
		$dbxlink->commit();
	return $ret;
}
