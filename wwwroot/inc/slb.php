<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

$vs_proto = array (
	'TCP' => 'TCP',
	'UDP' => 'UDP',
	'MARK' => 'MARK',
);

// *********************  Config-generating functions  *********************

// you may override the class name to make using your own triplet class
$triplet_class = 'SLBTriplet';
$parser_class = 'MacroParser';

class SLBTriplet
{
	public $lb;
	public $vs;
	public $rs;
	public $slb;
	public $display_cells;

	public function __construct ($lb_id, $vs_id, $rs_id, $db_row = NULL)
	{
		$this->lb = spotEntity ('object', $lb_id);
		$this->vs = spotEntity ('ipv4vs', $vs_id);
		$this->rs = spotEntity ('ipv4rspool', $rs_id);
		$this->display_cells = array ('lb', 'vs', 'rs');
		if (isset ($db_row))
			$this->slb = $db_row;
		else
		{
			$result = usePreparedSelectBlade
			(
				"SELECT prio, vsconfig, rsconfig FROM IPv4LB WHERE object_id = ? AND vs_id = ? AND rspool_id = ?",
				array ($lb_id, $vs_id, $rs_id)
			);
			if ($row = $result->fetch (PDO::FETCH_ASSOC))
				$this->slb = $row;
			else
				throw new RackTablesError ("SLB triplet not found in the DB");
		}
	}

	static public function getTriplets ($cell)
	{
		if (isset ($cell['ip_bin']) && isset ($cell['vslist']))
			// cell is IPAddress
			return self::getTripletsByIP ($cell['ip_bin']);
		$ret = array();
		switch ($cell['realm'])
		{
			case 'object':
				$db_field = 'object_id';
				$order_fields = 'vs_id';
				$display_cells = array('vs', 'rs');
				break;
			case 'ipv4vs':
				$db_field = 'vs_id';
				$order_fields = 'rspool_id';
				$display_cells = array('rs', 'lb');
				break;
			case 'ipv4rspool':
				$db_field = 'rspool_id';
				$order_fields = 'vs_id';
				$display_cells = array('vs', 'lb');
				break;
			default:
				throw new InvalidArgException ('realm', $cell['realm']);
		}
		$result = usePreparedSelectBlade
		(
			'SELECT object_id, rspool_id, vs_id, prio, vsconfig, rsconfig FROM IPv4LB ' .
			"WHERE `$db_field` = ? ORDER BY $order_fields",
			array ($cell['id'])
		);
		$rows = $result->fetchAll (PDO::FETCH_ASSOC);
		unset ($result);
		global $triplet_class;
		foreach ($rows as $row)
		{
			$row['vsconfig'] = dos2unix ($row['vsconfig']);
			$row['rsconfig'] = dos2unix ($row['rsconfig']);
			$triplet = new $triplet_class ($row['object_id'], $row['vs_id'], $row['rspool_id'], $row);
			$triplet->display_cells = $display_cells;
			$ret[] = $triplet;
		}
		return $ret;
	}

	static public function getTripletsByIP ($ip_bin)
	{
		$ret = array();
		$result = usePreparedSelectBlade ("
SELECT DISTINCT IPv4LB.object_id, IPv4LB.rspool_id, IPv4LB.vs_id, IPv4LB.prio, IPv4LB.vsconfig, IPv4LB.rsconfig
FROM
	IPv4LB INNER JOIN IPv4VS ON IPv4VS.id = IPv4LB.vs_id
	LEFT JOIN IPv4RS USING (rspool_id)
WHERE
	rsip = ? OR vip = ?
ORDER BY
	vs_id
	", array ($ip_bin, $ip_bin)
		);
		$rows = $result->fetchAll (PDO::FETCH_ASSOC);
		unset ($result);
		global $triplet_class;
		foreach ($rows as $row)
		{
			$triplet = new $triplet_class ($row['object_id'], $row['vs_id'], $row['rspool_id'], $row);
			$triplet->display_cells = array ('vs', 'lb', 'rs');
			$ret[] = $triplet;
		}
		return $ret;
	}
}

function generateSLBConfig ($triplet_list)
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
# VS (id == %VS_ID%): %VS_NAME%
# RS (id == %RSP_ID%): %RSP_NAME%");

	// group triplets by object_id, vs_id
	$grouped = array();
	foreach ($triplet_list as $triplet)
		$grouped[$triplet->lb['id']][$triplet->vs['id']][] = $triplet;

	foreach ($grouped as $object_id => $subarr)
	{
		$lb = array_first (array_first ($subarr))->lb;
		$lb_parser = clone $gl_parser;
		$lb_parser->addMacro ('LB_ID', $lb['id']);
		$lb_parser->addMacro ('LB_NAME', $lb['name']);
		foreach ($subarr as $vs_id => $triplets)
		{
			$vs = array_first ($triplets)->vs;
			$vs_parser = clone $lb_parser;
			$vs_parser->addMacro ('VS_ID', $vs['id']);
			$vs_parser->addMacro ('VS_NAME', $vs['name']);
			$vs_parser->addMacro ('VIP', $vs['vip']);
			$vs_parser->addMacro ('VPORT', $vs['vport']);
			$vs_parser->addMacro ('IP_VER', (strlen ($vs['vip_bin']) == 16) ? 6 : 4);
			if ($vs['proto'] == 'MARK')
			{
				$vs_parser->addMacro ('PROTO', 'TCP');
				$mark = implode ('', unpack ('N', substr ($vs['vip_bin'], 0, 4)));
				$vs_parser->addMacro ('MARK', $mark);
				$vs_parser->addMacro ('VS_HEADER', "fwmark $mark");
			}
			else
			{
				$vs_parser->addMacro ('VS_HEADER', $vs['vip'] . ' ' . $vs['vport']);
				$vs_parser->addMacro ('PROTO', $vs['proto']);
			}
			$vs_parser->addMacro ('VS_RS_CONF', dos2unix ($vs['rsconfig']));

			$vip_bin = ip_checkparse ($vs_parser->expandMacro ('VIP'));
			if ($vip_bin === FALSE)
				$family_length = 4;
			else
				$family_length = strlen ($vip_bin);

			foreach ($triplets as $triplet)
			{
				$rsp = $triplet->rs;
				$rs_parser = clone $vs_parser;
				$rs_parser->addMacro ('RSP_ID', $rsp['id']);
				$rs_parser->addMacro ('RSP_NAME', $rsp['name']);
				$rs_parser->addMacro ('RSP_VS_CONF', dos2unix ($rsp['vsconfig']));
				$rs_parser->addMacro ('RSP_RS_CONF', dos2unix ($rsp['rsconfig']));
				$rs_parser->addMacro ('VS_VS_CONF', dos2unix ($vs['vsconfig'])); // VS-driven vsconfig has higher priority than RSP-driven

				$rs_parser->addMacro ('PRIO', $triplet->slb['prio']);
				$rs_parser->addMacro ('SLB_VS_CONF', dos2unix ($triplet->slb['vsconfig']));
				$rs_parser->addMacro ('SLB_RS_CONF', dos2unix ($triplet->slb['rsconfig']));

				$ret .= $rs_parser->expand ("
%VS_PREPEND%
virtual_server %VS_HEADER% {
	protocol %PROTO%
	%GLOBAL_VS_CONF%
	%RSP_VS_CONF%
	%VS_VS_CONF%
	%SLB_VS_CONF%
");

				foreach ($rs_parser->getRSList() as $rs_row)
				{
					if ($rs_row['inservice'] != 'yes')
						continue;
					$parser = clone $rs_parser;
					$parser->addMacro ('RS_HEADER',  ($parser->expandMacro ('PROTO') == 'MARK' ? '%RSIP%' : '%RSIP% %RSPORT%'));
					$parser->addMacro ('RSIP', $rs_row['rsip']);
					if (isset ($rs_row['rsport']))
						$parser->addMacro ('RSPORT', $rs_row['rsport']);
					$parser->addMacro ('RS_COMMENT', $rs_row['comment']);
					$parser->addMacro ('RS_RS_CONF', dos2unix ($rs_row['rsconfig']));

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
								$r_parser = clone $parser;
								$r_parser->addMacro ('RSPORT', $rsport);
								$ret .= $r_parser->expand ("
	%RS_PREPEND%
	real_server %RS_HEADER% {
		%GLOBAL_RS_CONF%
		%VS_RS_CONF%
		%RSP_RS_CONF%
		%SLB_RS_CONF%
		%RS_RS_CONF%
	}
");
							}
						}
				}
				$ret .= "}\n";
			}
		}
	}
	return $ret;
}

class MacroParser
{
	protected $macros; // current macro context
	protected $trace; // recursive macro expansion path

	public function __construct()
	{
		$this->macros = array();
		$this->trace = array();
	}

	// cuts the subsequent defines from $value and stores the $name-$value define
	// if $value is unset, returns immediately
	public function addMacro ($name, $value)
	{
		if (! isset ($value))
			return;
		$new_value = ''; // value without defines
		$macro_deep = 0;
		foreach (explode ("\n", $value) as $line)
		{
			if (! $macro_deep)
			{
				if (preg_match ('/^([A-Za-z_0-9]+)([\?:]?=)(.*)/', $line, $m))
				{
					// found macro definition
					$mname = $m[1];
					$op = $m[2];
					$mvalue = ltrim ($m[3]);
					if (substr ($mvalue, 0, 1) == '`') // quoted define value
					{
						$line = substr ($mvalue, 1);
						$mvalue = '';
						$macro_deep++;
					}
					else
					{
						$mvalue = rtrim ($mvalue);
						if ($op === ':=')
							$this->macros[$mname] = $this->expand ($mvalue);
						elseif ($op === '?=' && '' === $this->expandMacro ($mname))
							$this->macros[$mname] = $mvalue;
						else
							$this->macros[$mname] = $mvalue;
					}
				}
				else
					$new_value .= $line . "\n";
			}

			if ($macro_deep)
			{
				for ($i = 0; $i < strlen ($line); $i++)
				{
					$c = $line[$i];
					if ($c == "'" && 0 == --$macro_deep)
					{
						if ($op === ':=')
							$this->macros[$mname] = $this->expand ($mvalue);
						elseif ($op === '?=' && '' === $this->expandMacro ($mname))
							$this->macros[$mname] = $mvalue;
						else
							$this->macros[$mname] = $mvalue;
						$rest = substr ($line, $i + 1);
						if (preg_match ('/\S/', $rest))
							$new_value .= $rest . "\n";
						break;
					}
					elseif ($c == "`")
						$macro_deep++;
					$mvalue .= $c;
				}
				if ($macro_deep)
					$mvalue .= "\n";
			}
		}
		$this->macros[$name] = substr ($new_value, 0, -1); // trim last \n
	}

	// replaces all macro expansions (like %THIS%) by the results of expandMacro calls
	// Has some formatting logic:
	//  * indent each line of expansion if the expanding line contains only the macro reference
	//  * do not add empty line to the output if the expanding line contains only the macro reference and expands to empty string
	//  * trim last newline of expansion if there already is newline in source string after the macro reference
	public function expand ($text)
	{
		$ret = '';
		$len = strlen ($text);
		$pos = 0;
		$state = 0;
		$lazy = 0;
		while ($pos < $len || $pos == $len && $state == 100)
			switch ($state)
			{
				case 99: // expansion failed, cat it untouched into $ret
					$ret .= '%';
					$pos = $exp_start + 1;
					$state = 0;
					break;
				case 100: // expand from $exp_start to $pos with $e_value
					$pre_blank = TRUE;
					$indent = '';
					$line_start = 0;
					for ($i = $exp_start - 1; $i >= 0; $i--)
						if ($text[$i] == "\n")
						{
							$line_start = $i + 1;
							break;
						}
						elseif ($text[$i] != ' ' && $text[$i] != "\t")
						{
							$pre_blank = FALSE;
							break;
						}
					if ($pre_blank)
						$indent = substr ($text, $line_start, $exp_start - $line_start);

					$after_blank = TRUE;
					$line_end = $len;
					for ($i = $pos; $i < $len; $i++)
						if ($text[$i] == "\n")
						{
							$line_end = $i;
							break;
						}
						elseif ($text[$i] != ' ' && $text[$i] != "\t")
						{
							$after_blank = FALSE;
							break;
						}
					// do actual expansion
					if ($e_value == '')
					{
						// skip entire line if empty expansion was lazy
						if ($lazy)
						{
							$ret = preg_replace ('/.*$/', '', $ret, 1);
							$pos = $line_end + 1;
						}
						// skip newline if expansion is empty and alone
						elseif ($pre_blank && $after_blank)
						{
							$ret = rtrim ($ret, " \t");
							$pos = $line_end + 1;
						}
					}
					else
					{
						// trim last newline of expansion
						if ($after_blank && $e_value != '' && $e_value[strlen ($e_value) - 1] == "\n")
							$e_value = substr ($e_value, 0, -1);
						// indent each line of $e_value
						if ($indent != '')
							$e_value = preg_replace ('/\n(?!$)/', "\n$indent", $e_value);
						$ret .= $e_value;
					}
					$lazy = 0;
					$state = 0;
					break;
				case 0: // initial state, search for %
					if (FALSE === ($exp_start = strpos ($text, '%', $pos)))
					{
						$ret .= substr ($text, $pos);
						$pos = $len;
					}
					else
					{
						$ret .= substr ($text, $pos, $exp_start - $pos);
						$pos = $exp_start + 1;
						$state = 1;
						if ($pos < $len)
						{
							if ($text[$pos] == '{')
							{
								$state = 2;
								$pos++;
							}
							elseif ($text[$pos] == '?')
							{
								$lazy = 1;
								$pos++;
							}
						}
						$mname_begin = $pos;
					}
					break;
				case 1: // simple expansion (%ABC%) ending
					if (preg_match ('/[%\n]/s', $text, $m, PREG_OFFSET_CAPTURE, $pos) && $m[0][0] != "\n")
					{
						$i = $m[0][1];
						$macro_name = substr ($text, $mname_begin, $i - $mname_begin);
						if (preg_match('/^[A-Za-z_0-9]+$/', $macro_name))
						{
							$pos = $i + 1; // skip '%''
							$e_value = $this->expandMacro ($macro_name);
							$state = 100;
							break;
						}
					}
					$state = 99; // rollback expansion
					break;
				case 2: // enhanced expansion (%{ABC}% or %{ABC:[+-]smthng}%) stage 1
					if (preg_match ('/[}:\n]/s', $text, $m, PREG_OFFSET_CAPTURE, $pos) && $m[0][0] != "\n")
					{
						$i = $m[0][1];
						$macro_name = substr ($text, $mname_begin, $i - $mname_begin);
						if (preg_match('/^[A-Za-z_0-9]+$/', $macro_name))
						{
							if ($text[$i] == '}' && $i + 1 < $len && $text[$i + 1] == '%')
							{
								$e_value = $this->expandMacro ($macro_name);
								$pos = $i + 2; // skip '}%'
								$state = 100;
								break;
							}
							elseif ($text[$i] == ':')
							{
								$i++;
								if ($i < $len && ($text[$i] == '+' || $text[$i] == '-'))
								{
									$condition_type = $text[$i];
									$deep = 1;
									$pos = $i + 1;
									$cond_start = $pos;
									$state = 3;
									break;
								}
							}
						}
					}
					$state = 99; // rollback expansion
					break;
				case 3: // conditional expansion (%{ABC:[+-]smthng}%) ending
					if (! preg_match ('/[{}]/', $text, $m, PREG_OFFSET_CAPTURE, $pos))
						$state = 99;
					else
					{
						$i = $m[0][1];
						$pos = $i + 1;
						if ($text[$i] == '{' && $i > 0 && $text[$i - 1] == '%')
						{
							$deep++;
							$pos++; // skip '%{'
						}
						elseif ($text[$i] == '}' && $i + 1 < $len && $text[$i + 1] == '%')
						{
							$deep--;
							$pos++;
							if ($deep == 0)
							{
								$exp = substr ($text, $cond_start, $i - $cond_start);
								$m_value = $this->expandMacro ($macro_name);
								if ($condition_type == '+')
									$e_value = ($m_value == '') ? '' : $this->expand ($exp);
								elseif ($condition_type == '-')
									$e_value = ($m_value != '') ? $m_value : $this->expand ($exp);
								$state = 100;
							}
						}
					}
					break;
				default:
					throw new RackTablesError ("Unexpected state $state in " . __CLASS__ . '::' . __METHOD__ . " FSM", RackTablesError::INTERNAL);
			}

		if ($state != 0)
			$ret .= substr ($text, $exp_start);
		return $ret;
	}

	// returns the result of expanding the named define, or '' if unset
	public function expandMacro ($name)
	{
		array_push ($this->trace, $name);
		if (isset ($this->macros[$name]))
			$ret = $this->expand ($this->macros[$name]);
		else
			$ret = '';
		array_pop ($this->trace);
		return $ret;
	}

	// you can inherit the parser class and override this method to fill RS list dynamically
	public function getRSList()
	{
		if (isset ($this->macros['RSP_ID']))
			return getRSListInPool ($this->macros['RSP_ID']);
		return array();
	}
}

function buildEntityLVSConfig ($cell)
{
	$ret = "#\n#\n# This configuration has been generated automatically by RackTables\n#\n#\n";
	// slbv2
	if ($cell['realm'] != 'ipv4vs')
		$ret .= generateSLBConfig2 (getTriplets ($cell));
	// slbv1
	if ($cell['realm'] != 'ipvs')
		$ret .= generateSLBConfig (SLBTriplet::getTriplets ($cell));
	return $ret;
}

function buildLVSConfig ($object_id)
{
	return callHook ('buildEntityLVSConfig', spotEntity ('object', $object_id));
}

// *********************  Database functions  *********************

function addRStoRSPool ($pool_id, $rsip_bin, $rsport = 0, $inservice = 'no', $rsconfig = '', $comment = '')
{
	$ret = usePreparedInsertBlade
	(
		'IPv4RS',
		array
		(
			'rspool_id' => $pool_id,
			'rsip' => $rsip_bin,
			'rsport' => ($rsport == '' || $rsport === 0) ? NULL : $rsport,
			'inservice' => $inservice == 'yes' ? 'yes' : 'no',
			'rsconfig' => nullIfEmptyStr ($rsconfig),
			'comment' => nullIfEmptyStr ($comment),
		)
	);
	lastCreated ('iprs', lastInsertID());
	return $ret;
}

function addLBtoRSPool ($pool_id = 0, $object_id = 0, $vs_id = 0, $vsconfig = '', $rsconfig = '', $prio = '')
{
	usePreparedInsertBlade
	(
		'IPv4LB',
		array
		(
			'object_id' => $object_id,
			'rspool_id' => $pool_id,
			'vs_id' => $vs_id,
			'vsconfig' => nullIfEmptyStr ($vsconfig),
			'rsconfig' => nullIfEmptyStr ($rsconfig),
			'prio' => nullIfEmptyStr ($prio),
		)
	);
}

function commitDeleteVS ($id = 0)
{
	releaseFiles ('ipv4vs', $id);
	destroyTagsForEntity ('ipv4vs', $id);
	usePreparedDeleteBlade ('IPv4VS', array ('id' => $id));
}

function commitUpdateRS ($rsid, $rsip_bin, $rsport = 0, $inservice = 'yes', $rsconfig = '', $comment = '')
{
	usePreparedExecuteBlade
	(
		'UPDATE IPv4RS SET rsip=?, rsport=?, inservice=?, rsconfig=?, comment=? WHERE id=?',
		array
		(
			$rsip_bin,
			($rsport == '' || $rsport === 0) ? NULL : $rsport,
			$inservice,
			nullIfEmptyStr ($rsconfig),
			nullIfEmptyStr ($comment),
			$rsid,
		)
	);
}

// $vport is ignored if $proto == 'MARK'
function commitUpdateVS ($vsid, $vip_bin, $vport = 0, $proto = '', $name = '', $vsconfig = '', $rsconfig = '')
{
	if ($proto != 'MARK' && $vport <= 0)
		throw new InvalidArgException ('vport', $vport);
	if ($proto == '')
		throw new InvalidArgException ('proto', $proto);
	return usePreparedUpdateBlade
	(
		'IPv4VS',
		array
		(
			'vip' => $vip_bin,
			'vport' => ($proto == 'MARK' ? NULL : $vport),
			'proto' => $proto,
			'name' => nullIfEmptyStr ($name),
			'vsconfig' => nullIfEmptyStr ($vsconfig),
			'rsconfig' => nullIfEmptyStr ($rsconfig),
		),
		array ('id' => $vsid)
	);
}

function commitCreateRSPool ($name = '', $vsconfig = '', $rsconfig = '', $tagidlist = array())
{
	usePreparedInsertBlade
	(
		'IPv4RSPool',
		array
		(
			'name' => nullIfEmptyStr ($name),
			'vsconfig' => nullIfEmptyStr ($vsconfig),
			'rsconfig' => nullIfEmptyStr ($rsconfig),
		)
	);
	$new_pool_id = lastInsertID();
	lastCreated ('ipv4rspool', $new_pool_id);
	produceTagsForNewRecord ('ipv4rspool', $tagidlist, $new_pool_id);
	return $new_pool_id;
}

function commitDeleteRSPool ($pool_id = 0)
{
	releaseFiles ('ipv4rspool', $pool_id);
	destroyTagsForEntity ('ipv4rspool', $pool_id);
	usePreparedDeleteBlade ('IPv4RSPool', array ('id' => $pool_id));
}

function commitUpdateSLBDefConf ($data)
{
	saveScript('DefaultVSConfig', $data['vs']);
	saveScript('DefaultRSConfig', $data['rs']);
}

function getSLBDefaults ($do_cache_result = FALSE)
{
	static $ret = array();

	if (! $do_cache_result)
		$ret = array();
	elseif (count ($ret))
		return $ret;

	$ret['vsconfig'] = loadScript ('DefaultVSConfig');
	$ret['rsconfig'] = loadScript ('DefaultRSConfig');
	return $ret;
}

// Return the list of all currently configured load balancers with their pool count.
function getLBList ()
{
	$result = usePreparedSelectBlade
	(
		"select object_id, count(rspool_id) as poolcount " .
		"from IPv4LB group by object_id order by object_id"
	);
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['object_id']] = $row['poolcount'];
	return $ret;
}

function getRSList ()
{
	$result = usePreparedSelectBlade
	(
		"select id, inservice, rsip as rsip_bin, rsport, rspool_id, rsconfig " .
		"from IPv4RS order by rspool_id, IPv4RS.rsip, rsport"
	);
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$row['rsip'] = ip_format ($row['rsip_bin']);
		$row['rsconfig'] = dos2unix ($row['rsconfig']);
		foreach (array ('inservice', 'rsip_bin', 'rsip', 'rsport', 'rspool_id', 'rsconfig') as $cname)
			$ret[$row['id']][$cname] = $row[$cname];
	}
	return $ret;
}

function getRSListInPool ($rspool_id)
{
	$ret = array();
	$query = "select id, inservice, rsip as rsip_bin, rsport, rsconfig, comment from " .
		"IPv4RS where rspool_id = ? order by IPv4RS.rsip, rsport";
	$result = usePreparedSelectBlade ($query, array ($rspool_id));
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$row['rsip'] = ip_format ($row['rsip_bin']);
		$ret[$row['id']] = $row;
	}
	return $ret;
}
