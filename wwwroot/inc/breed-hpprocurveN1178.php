<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

// Functions for HP Procurve switches 

function hpprocurveN1178ReadLLDPStatus ($input)
{
	$ret = array();
	foreach (explode ("\n", $input) as $line)
	{
		$matches = array();
		switch (TRUE)
		{
		case preg_match ('/^  Local Port.+:(.+)$/', $line, $matches):
			if (empty(trim($matches[1])))
				{
					$ret['current']['local_port'] = 'NULL';
					break;
				}
			else
				{
					$ret['current']['local_port'] = shortenIfName (trim($matches[1]));
					break;
				}
		case preg_match ('/^  PortId.+:(.+)$/', $line, $matches):
			if (empty(trim($matches[1])))
				{
					$ret['current']['remote_port'] = 'NULL';
					break;
				}
			else
				{
					$ret['current']['remote_port'] = trim($matches[1]);
					break;
				}
		case preg_match ('/^  SysName\s+:(.+)?$/', $line, $matches):
			if (empty(trim($matches[1])))
				{
					$ret['current']['sys_name'] = 'NULL';
					break;
				}
			else
				{
					$ret['current']['sys_name'] = trim($matches[1]);
					break;
				}
		case preg_match ('/^  PortDescr\s+:(.+)?$/', $line, $matches):
			if (empty(trim($matches[1])))
				{
					$ret['current']['sys_name'] = 'NULL';
					break;
				}
			else
				{
					$ret['current']['port_descr'] = trim($matches[1]);
				}
			if
				(
					array_key_exists ('current', $ret) &&
					array_key_exists ('local_port', $ret['current']) &&
					array_key_exists ('port_descr', $ret['current']) &&
					array_key_exists ('sys_name', $ret['current']) &&
					array_key_exists ('remote_port', $ret['current'])
				)
				{
					$port = NULL;
					if (preg_match ('/^[a-f0-9]{2} [a-f0-9]{2} [a-f0-9]{2} [a-f0-9]{2} [a-f0-9]{2} [a-f0-9]{2}$/',$ret['current']['remote_port']))
						$port = $ret['current']['port_descr'];
					else
						$port = $ret['current']['remote_port'];
					if (isset ($port))
						{
							$ret[$ret['current']['local_port']][] = array
							(
								'device' => $ret['current']['sys_name'],
								'port' => $port,
							);
						}
				}
				unset ($ret['current']);
		default:
		}
	}
	unset ($ret['current']);
	return $ret;
}


function hpprocurveN1178ReadInterfaceStatus ($text)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $text) as $line)
		{
			switch ($state)
			{
				case 'headerSearch':
					if (preg_match('/\s?Port\s+Type\s+/', $line))
					{
						$name_field_borders = getColumnCoordinates($line, 'Port');
						if (isset ($name_field_borders['from']))
							$state = 'readPort';
					}
					break;
				case 'readPort':
					if ( preg_match('/^[0-9]+/', trim (substr ($line, 0, $name_field_borders['length'])), $matches) )
						$portname = $matches[0];
						if (!isset($portname))
							$portname = NULL;
					if ( preg_match('/^[0-9]+.+/', trim (substr ($line, $name_field_borders['from'] + $name_field_borders['length'] + 1)), $matches) )
						$rest = $matches[0];
						if (!isset($rest))
							$rest = NULL;

					$field_list = preg_split('/\s+/', $rest);
					if (count ($field_list) < 4)
						break;
					list ($type, $delim, $alert, $adm_status, $status_raw, $mode) = $field_list;
						if ($status_raw == 'Up')
							$status = 'up';
						elseif ($status_raw == 'Down')
							$status = 'down';
						elseif ($adm_status == 'No')
							$status = 'disabled';
					if ( preg_match('/([01]+)/', $mode, $matches) )
						$speed = $matches[0];
					if ( preg_match('/([a-zA-Z]+)/', $mode, $matches) )
						$duplex = $matches[0];
					$result[$portname] = array
						(
							'status' => $status,
							'speed' => $speed,
							'duplex' => $duplex,
						);
					break;
			}
		}
	return $result;
}


function hpprocurveN1178ReadMacList ($text)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $text) as $line)
		{
		switch ($state)
			{
			case 'headerSearch':
				if (preg_match('/\s?MAC Address\s+Located on Port\s?/', $line))
					{
					$state = 'readPort_all';
					break;
					}
				elseif (preg_match('/^\s*Status and Counters -\s*Port Address Table -\s*([0-9]+)$/', $line, $portdata))
					{
					$state = 'readPort_single';
					$portname = $portdata[1];
					break;
					}
			case 'readPort_all':
				if (! preg_match ('/^\s*([a-f0-9]{6}\-[a-f0-9]{6})\s*(\S+)$/', trim ($line), $matches))
					break;
				$portname = shortenIfName ($matches[2]);
				$vid = NULL;
				$result[$portname][] = array
					(
						'mac' => implode (":", str_split(str_replace ('-', '', $matches[1]), 2)),
						'vid' => '',
					);
				break;
			case 'readPort_single':
				if (! preg_match ('/^\s*([a-f0-9]{6}\-[a-f0-9]{6})\s*$/', trim ($line), $matches))
					break;
				$vid = NULL;
				$result[$portname][] = array
					(
						'mac' => implode (":", str_split(str_replace ('-', '', $matches[1]), 2)),
						'vid' => '',
					);
				break;

			}
		}
	foreach ($result as $portname => &$maclist)
		sort ($maclist);
	return $result;
}


function hpprocurveN1178Read8021QConfig ($input)
{
	$ret = constructRunning8021QConfig();
	$ret['vlanlist'][] = VLAN_DFL_ID; // HP hides VLAN1 from config text
	$matches = array();
	$vlanlist = array();
	$rawdata = explode ("Status and Counters - VLAN Information - for ports", $input);
	array_shift($rawdata); 

	foreach ($rawdata as $line)
		{
		$port = array(
			'port_id' => '',
			'port_name' => '',
			'vlan_data' => array(),
			'port_mode' => FALSE,
		);
		$port_mode = '';
		foreach (explode ("\n", $line) as $vlans)
			{
			if (preg_match('/^ VLAN ID Name |^\s*-------/', $vlans))
				{
				continue;
				}
			if (preg_match('/^([0-9]+)$/', trim($vlans), $matches))
				{
				$port['port_id'] = $matches[1];
				continue;
				}
			if (preg_match('/^\s+Port name: (.+)$/', $vlans, $matches))
				{
				$port['port_name'] = $matches[1];
				continue;
				}
			if (preg_match ('/^\S*\s*(\d+)\s+(\S+)\s+\S+\s+\S+\s+([T]agged|[U]ntagged).*$/', $vlans, $matches))
				{
				$port['vlan_data'][$matches[1]]['vlan_name'] = $matches[2];
				$port['vlan_data'][$matches[1]]['vlan_mode'] = $matches[3];
				$vlanlist[] = $matches[1];
				}
			}
		// Here we add parsed data into $ret array
		$port_id = $port['port_id'];
		$port_name = $port['port_name'];

		// Port config
		$ret['portconfig'][$port_id][] = array ('type' => 'line-header', 'line' => 'interface ' . $port_id);
		if (!empty($port_name))
			{
			$ret['portconfig'][$port_id][] = array ('type' => 'line-other', 'line' => 'name ' . $port_name);
			}

		// Port data
		$allowed_vlans = array();
		if (array_search('Tagged', array_column($port['vlan_data'], 'vlan_mode')) === FALSE)
			{
			$port_mode = 'access';
			foreach ($port['vlan_data'] as $vid=>$value)
				{
				$allowed_vlans[] = $vid;
				$native = $vid;
				}
			}
		else
			{
			$port_mode = 'trunk';
			foreach ($port['vlan_data'] as $vid=>$value)
				{
				if (preg_match('/\d+/', $vid))
					{
					$allowed_vlans[] = $vid;
					}
				}
			foreach ($port['vlan_data'] as $vid=>$value)
				{
				if (preg_match('/\d+/', $vid) && $value['vlan_mode'] === "Untagged")
					{
					$native = $vid;
					break;
					}
				else
					{
					$native = 0;
					}
				}
			}
		$ret['portdata'][$port_id] = array ('mode' => $port_mode, 'allowed' => $allowed_vlans, 'native' => $native);

		unset($port);
		unset($allowed_vlans);
	}
	// Return de-duplicated and sorted list of vlans
	$ret['vlanlist'] = array_merge($ret['vlanlist'], array_keys(array_flip($vlanlist)));
	sort($ret['vlanlist']);
	unset($vlanlist);
	unset($matches);
	unset($rawdata);
	return $ret;
}


function hpprocurveN1178TranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
	$rem_allowed_data = array();
	$rem_tagged_data = array();
	$unset_access_data = array();

	foreach ($queue as $cmd)
	switch ($cmd['opcode'])
	{
	case 'create VLAN':
		$ret .= "vlan ${cmd['arg1']}\nexit\n";
		break;
	case 'destroy VLAN':
		$ret .= "no vlan ${cmd['arg1']}\n";
		break;
	case 'add allowed':
		foreach ($cmd['vlans'] as $vlan_id)
		{
			$ret .= "vlan ${vlan_id} tagged ${cmd['port']}\n";
		}
		///////////////////////////////////////////////////////////////////////////////////////
		// Here is a workaround: remove untagged port for case access->trunk(non-native)
		if
		(
			array_key_exists('port_id', $unset_access_data) &&
			array_key_exists('vlan_id', $unset_access_data) &&
			$unset_access_data['port_id'] === $cmd['port']
		)
		{
			$ret .= "no vlan ${unset_access_data['vlan_id']} untagged ${unset_access_data['port_id']}\n";
			unset($unset_access_data);
		}
		///////////////////////////////////////////////////////////////////////////////////////
		// Here is a workaround: remove port tagged from vlans again for case trunk(non-native)->trunk(non-native)
		if ( $rem_tagged_data )
		{
			foreach ($rem_tagged_data as $port_id=>$vlan_list)
			{
				if ( !empty($port_id) && $port_id === $cmd['port'] )
				{
					foreach ($vlan_list as $key=>$vlan_id)
					{
					if ( isset($vlan_id) )
					$ret .= "no vlan ${vlan_id} tagged ${port_id}\n";
					}
				}
			}
		}
		$rem_tagged_data = array();
		break;
	case 'rem allowed':
		foreach ($cmd['vlans'] as $vlan_id)
		{
			$ret .= "no vlan ${vlan_id} tagged ${cmd['port']}\n";
			///////////////////////////////////////////////////////////////////////////////////////
			// Here is a workaround: we should remove untagged port before 
			// add it as tagged for case access->trunk(non-native)
			// HP L2 switches doesn't allow "orphaned" ports (without tags)
			$rem_allowed_data[$cmd['port']][] = $vlan_id;
			$rem_tagged_data[$cmd['port']][] = $vlan_id;
		}
		break;
	case 'set access':
		$ret .= "vlan ${cmd['arg2']} untagged ${cmd['arg1']}\n";
		///////////////////////////////////////////////////////////////////////////////////////
		// Now remove tagged port for case trunk(non-native)->access
		//file_put_contents ('/var/log/racktables.log', var_export($rem_allowed_data, true), FILE_APPEND | LOCK_EX);
		foreach ($rem_allowed_data as $port_id=>$vlan_list)
		{
			if ( !empty($port_id) && $port_id === $cmd['arg1'] )
			{
			foreach ($vlan_list as $key=>$vlan_id)
				{
				if ( isset($vlan_id) )
					$ret .= "no vlan ${vlan_id} tagged ${cmd['arg1']}\n";
				}
			}
		}
		$rem_allowed_data = array();
		break;
	case 'unset access':
		///////////////////////////////////////////////////////////////////////////////////////
		// Here is a workaround: we should remove untagged port before we
		// add it as tagged, for case access->trunk(non-native)
		// HP L2 switches doesn't allow "orphaned" ports (without tags)
		$unset_access_data['port_id'] = $cmd['arg1'];
		$unset_access_data['vlan_id'] = $cmd['arg2'];
		$ret .= "no vlan ${cmd['arg2']} untagged ${cmd['arg1']}\n";
		break;
	case 'set native':
		$ret .= "vlan ${cmd['arg2']} untagged ${cmd['arg1']}\n";
		///////////////////////////////////////////////////////////////////////////////////////
		// Here is a workaround: we should add tagged port again for case
		// when we remove native but keep it as tagged
		$ret .= "no vlan ${cmd['arg2']} tagged ${cmd['arg1']}\n";
		break;
	case 'unset native': // NOP
		$ret .= "no vlan ${cmd['arg2']} untagged ${cmd['arg1']}\n";
		$ret .= "vlan ${cmd['arg2']} tagged ${cmd['arg1']}\n";
		break;
	case 'set mode': // NOP
		break;
	case 'begin configuration':
		$ret .= "configure\n";
		break;
	case 'end configuration':
		$ret .= "end\n";
		break;
	case 'save configuration':
		$ret .= "write memory\n";
		break;
	case 'cite':
		$ret .= $cmd['arg1'];
		break;
	// query list
	case 'get8021q':
		$ret .= "show vlans ports ethernet all detail\n";
		break;
	case 'getlldpstatus':
		$ret .= "show lldp info remote-device all\n";
		break;
	case 'getallconf':
		$ret .= "show running-config\n";
		break;
	case 'getportstatus':
		$ret .= "show int brief\n";
		break;
	case 'getmaclist':
		$ret .= "show mac-address\n";
		break;
	case 'getportmaclist':
		$ret .= "show mac-address ethernet {$cmd['arg1']}\n";
		break;
	default:
		throw new InvalidArgException ('opcode', $cmd['opcode']);
	}
	return $ret;
}

function hpprocurveN1178SpotConfigText ($input)
{
	return $input;
}
