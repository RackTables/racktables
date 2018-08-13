<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

// functions for HP Procurve switches (N.11 OS)
require_once 'breed-hpprocurveN1178.php';
// functions for Cisco IOS 15 switches
require_once 'breed-ios15.php';

// Read provided output of "show cdp neighbors detail" command and
// return a list of records with (translated) local port name,
// remote device name and (translated) remote port name.
function ios12ReadCDPStatus ($input)
{
	$ret = array();
	foreach (explode ("\n", $input) as $line)
	{
		$matches = array();
		switch (TRUE)
		{
		case preg_match ('/^Device ID:\s*([A-Za-z0-9][A-Za-z0-9\.\-\_]*)/', $line, $matches):
		case preg_match ('/^System Name:\s*([A-Za-z0-9][A-Za-z0-9\.\-\_]*)/', $line, $matches):
			$ret['current']['device'] = $matches[1];
			break;
		case preg_match ('/^Interface: (.+),  ?Port ID \(outgoing port\): (.+)$/', $line, $matches):
			if (array_key_exists ('device', $ret['current']))
				$ret[shortenIfName ($matches[1])][] = array
				(
					'device' => $ret['current']['device'],
					'port' => $matches[2],
				);
			unset ($ret['current']);
			break;
		default:
		}
	}
	unset ($ret['current']);
	return $ret;
}

function ios12ReadLLDPStatus ($input)
{
	$ret = array();
	$got_header = FALSE;
	foreach (explode ("\n", $input) as $line)
	{
		if (preg_match ("/^Device ID/", $line))
			$got_header = TRUE;

		if (!$got_header)
			continue;

		$matches = preg_split ('/\s+/', trim ($line));

		switch (count ($matches))
		{
		case 4:
			// check if $remote_name has port glued - it is known that IOS does not insert whitespace between
			// remote name and port if remote name is too long
			$remote_name_raw = array_shift ($matches);
			if (preg_match ("#^(.+?)((?:Fa|Gi|Te)[0-9/.]+)$#", $remote_name_raw, $rmatches))
			{
				array_unshift ($matches, $rmatches[2]);
				array_unshift ($matches, $rmatches[1]);
				// fall though to 5
			}
			else
				break;
		case 5:
			list ($remote_name, $local_port, $ttl, $caps, $remote_port) = $matches;
			$local_port = shortenIfName ($local_port);
			$ret[$local_port][] = array
			(
				'device' => $remote_name,
				'port' => $remote_port,
			);
			break;
		default:
		}
	}
	return $ret;
}

function xos12ReadLLDPStatus ($input)
{
	$ret = array();
	foreach (explode ("\n", $input) as $line)
	{
		$matches = array();
		switch (TRUE)
		{
		case preg_match ('/^LLDP Port ([[:digit:]]+) detected \d+ neighbor$/', $line, $matches):
			$ret['current']['local_port'] = shortenIfName ($matches[1]);
			break;
		case preg_match ('/^      Port ID     : "(.+)"$/', $line, $matches):
			$ret['current']['remote_port'] = $matches[1];
			break;
		case preg_match ('/^    - System Name: "(.+)"$/', $line, $matches):
			if
			(
				array_key_exists ('current', $ret) &&
				array_key_exists ('local_port', $ret['current']) &&
				array_key_exists ('remote_port', $ret['current'])
			)
				$ret[$ret['current']['local_port']][] = array
				(
					'device' => $matches[1],
					'port' => $ret['current']['remote_port'],
				);
			unset ($ret['current']);
		default:
		}
	}
	unset ($ret['current']);
	return $ret;
}

function xos12ReadInterfaceStatus ($input)
{
	$ret = array();
	foreach (explode ("\n", $input) as $line)
		if (preg_match('/^(\d+|\d:\d+)\s+.*\s+([ED])\s+([AR])\s+/', $line, $m))
		{
			$portname = $m[1];
			if ($m[2] == 'E' && $m[3] == 'A')
				$status = 'up';
			elseif ($m[2] == 'E' && $m[3] == 'R')
				$status = 'down';
			elseif ($m[2] == 'D')
				$status = 'disabled';
			$ret[$portname]['status'] = $status;
			unset ($status);
		}
	return $ret;
}

function xos12ReadMacList ($input)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $input) as $line)
		if (preg_match('/((?:[\da-f]{2}:){5}[\da-f]{2})\s+\S+\((\d{4})\).*\s+(\d+)\s*$/', $line, $m))
		{
			$mac = str_replace (':', '', $m[1]);
			$mac = implode ('.', str_split ($mac, 4));
			$portname = shortenIfName ($m[3]);
			$result[$portname][] = array(
				'mac' => $mac,
				'vid' => intval ($m[2]),
			);
		}
	foreach ($result as $portname => &$maclist)
		usort ($maclist, 'maclist_sort');
	return $result;
}

function vrpReadLLDPStatus ($input)
{
	$ret = array();
	$valid_subtypes = array
	(
		'interfaceName',
		'Interface Name',
		'Interface name',
		'interfaceAlias',
		'Interface Alias',
		'Interface alias',
		'local',
		'Local',
		'Locally assigned',
	);
	foreach (explode ("\n", $input) as $line)
	{
		$matches = array();
		switch (TRUE)
		{
		case preg_match ('/^(.+) has \d+ neighbor(\(s\)|s):$/', $line, $matches):
			$ret['current']['local_port'] = shortenIfName (trim ($matches[1]));
			break;
		case preg_match ('/^Port ?ID ?(?:sub)?type\s*:\s*(.*)$/i', $line, $matches):
			$ret['current']['PortIdSubtype'] = trim ($matches[1]);
			break;
		case preg_match ('/^Port ?ID\s*:\s*(.+)$/i', $line, $matches):
			$ret['current']['PortId'] = trim ($matches[1]);
			break;
		case preg_match ('/^Port description\s*:\s*(.*)$/i', $line, $matches):
			$ret['current']['PortDescription'] = trim ($matches[1]);
			break;
		case preg_match ('/^Sys(?:tem)? ?name\s*:\s*(.+)$/i', $line, $matches):
			if
			(
				array_key_exists ('current', $ret) &&
				array_key_exists ('PortIdSubtype', $ret['current']) &&
				array_key_exists ('local_port', $ret['current'])
			)
			{
				$port = NULL;
				if (array_key_exists ('PortId', $ret['current']) && in_array ($ret['current']['PortIdSubtype'], $valid_subtypes))
					$port = $ret['current']['PortId'];
				elseif (array_key_exists ('PortDescription', $ret['current']) && 'local' == $ret['current']['PortIdSubtype'])
					$port = $ret['current']['PortDescription'];
				if (isset ($port))
					$ret[$ret['current']['local_port']][] = array
					(
						'device' => trim ($matches[1]),
						'port' => $port,
					);
			}
			unset ($ret['current']);
			break;
		default:
		}
	}
	unset ($ret['current']);
	return $ret;
}

function ftos8ReadLLDPStatus ($input)
{
	$ret = array();
	$valid_subtypes = array
	(
		'Interface name (5)',
		'Interface Alias (1)',
		'Locally assigned (7)',
	);
	foreach (explode ("\n", $input) as $line)
	{
		$matches = array();
		switch (TRUE)
		{
		case preg_match ('/^ Local Interface (.+) has \d+ neighbor/', $line, $matches):
			$ret['current']['local_port'] = strtolower (str_replace (' ', '', $matches[1])); # "Gi 0/19" => "gi0/19"
			break;
		case preg_match ('/^    Remote Port Subtype:  (.+)$/', $line, $matches):
			$ret['current']['remote_subtype'] = $matches[1];
			break;
		case preg_match ('/^    Remote Port ID:  (.+)$/i', $line, $matches):
			$ret['current']['remote_port'] = $matches[1];
			break;
		case preg_match ('/^    Remote System Name:  (.+)$/', $line, $matches):
			if
			(
				array_key_exists ('current', $ret) &&
				array_key_exists ('remote_subtype', $ret['current']) &&
				in_array ($ret['current']['remote_subtype'], $valid_subtypes) &&
				array_key_exists ('remote_port', $ret['current']) &&
				array_key_exists ('local_port', $ret['current'])
			)
				$ret[$ret['current']['local_port']][] = array
				(
					'device' => $matches[1],
					'port' => $ret['current']['remote_port'],
				);
			unset ($ret['current']['remote_subtype']);
			unset ($ret['current']['remote_port']);
			break;
		default:
		}
	}
	unset ($ret['current']);
	return $ret;
}

function eos4ReadLLDPStatus ($input)
{
	$ret = array();
	$valid_subtypes = array
	(
		'Interface name (5)',
	);
	foreach (explode ("\n", $input) as $line)
	{
		$matches = array();
		switch (TRUE)
		{
		case preg_match ('/^Interface (.+) detected \d+ LLDP neighbors/', $line, $matches):
			$ret['current']['local_port'] = shortenIfName ($matches[1]);
			break;
		case preg_match ('/^    - Port ID type: (.+)$/', $line, $matches):
			$ret['current']['remote_subtype'] = $matches[1];
			break;
		case preg_match ('/^      Port ID     : "(.+)"$/', $line, $matches):
			$ret['current']['remote_port'] = $matches[1];
			break;
		case preg_match ('/^    - System Name: "(.+)"$/', $line, $matches):
			if
			(
				array_key_exists ('current', $ret) &&
				array_key_exists ('remote_subtype', $ret['current']) &&
				in_array ($ret['current']['remote_subtype'], $valid_subtypes) &&
				array_key_exists ('remote_port', $ret['current']) &&
				array_key_exists ('local_port', $ret['current'])
			)
				$ret[$ret['current']['local_port']][] = array
				(
					'device' => $matches[1],
					'port' => $ret['current']['remote_port'],
				);
			unset ($ret['current']['remote_subtype']);
			unset ($ret['current']['remote_port']);
			break;
		default:
		}
	}
	unset ($ret['current']);
	return $ret;
}

function ros11ReadLLDPStatus ($input)
{
	$ret = array();
	foreach (explode ("\n", $input) as $line)
	{
		switch (1)
		{
		case preg_match ('/^Local port: (.+)$/', $line, $m):
			$ret['current']['local_port'] = shortenIfName ($m[1]);
			break;
		case preg_match ('/^Port ID: (.+)$/', $line, $m):
			$ret['current']['remote_port'] = $m[1];
			break;
		case preg_match ('/^System Name: (.+)$/', $line, $m):
			if
			(
				array_key_exists ('current', $ret) &&
				array_key_exists ('remote_port', $ret['current']) &&
				array_key_exists ('local_port', $ret['current'])
			)
				$ret[$ret['current']['local_port']][] = array
				(
					'device' => $m[1],
					'port' => $ret['current']['remote_port'],
				);
			unset ($ret['current']['remote_port']);
			break;
		default: # NOP
		}
	}
	unset ($ret['current']);
	return $ret;
}

function ios12ReadVLANConfig ($input)
{
	$ret = constructRunning8021QConfig();

	$schema = $ret;
	if (preg_match ('/\nUnable to get configuration. Try again later/s', $input))
		throw new ERetryNeeded ("device is busy. 'show run' did not work");

	global $breedfunc;
	$nextfunc = 'ios12-get8021q-swports';
	foreach (explode ("\n", $input) as $line)
		$nextfunc = $breedfunc[$nextfunc] ($ret, $line);

	// clear $ret from temporary keys created by parser functions
	foreach ($ret as $key => $value)
		if (! isset ($schema[$key]))
			unset ($ret[$key]);
	return $ret;
}

function ios12ScanTopLevel (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^interface ((Ethernet|FastEthernet|GigabitEthernet|TenGigabitEthernet|[Pp]ort-channel)[[:digit:]]+(/[[:digit:]]+)*)$@', $line, $matches)):
		$port_name = shortenIfName ($matches[1]);
		$work['current'] = array ('port_name' => $port_name);
		$work['portconfig'][$port_name][] = array ('type' => 'line-header', 'line' => $line);
		return 'ios12-get8021q-readport'; // switch to interface block reading
	case (preg_match ('/^VLAN Name                             Status    Ports$/', $line, $matches)):
		return 'ios12-get8021q-readvlan';
	default:
		return 'ios12-get8021q-top'; // continue scan
	}
}

function ios12ReadSwitchPortList (&$work, $line)
{
	if (0 < strpos ($line, '! END OF SWITCHPORTS'))
		return 'ios12-get8021q-top';
	if (preg_match ('@^(?:\s*|vdc .*)Name:\s+(\S+)@', $line, $m))
		$work['current_switchport'] = $m[1];
	elseif (preg_match ('@^\s*Switchport:\s+(Enabled)@', $line, $m) && isset ($work['current_switchport']))
	{
		$work['switchports'][] = shortenIfName ($work['current_switchport']);
		unset ($work['current_switchport']);
	}
	return 'ios12-get8021q-swports';
}

function ios12PickSwitchportCommand (&$work, $line)
{
	$port_name = $work['current']['port_name'];
	if ($line == '' || $line[0] != ' ') // end of interface section
	{
		$work['portconfig'][$port_name][] = array ('type' => 'line-header', 'line' => $line);

		// save work, if it makes sense
		if (! isset ($work['switchports']) || ! in_array ($port_name, $work['switchports']))
			$work['current']['mode'] = 'SKIP'; // skip not switched ports
		else
		{
			if (! isset ($work['current']['mode']))
				$work['current']['mode'] = 'access';
		}
		switch (@$work['current']['mode'])
		{
		case 'access':
			if (!array_key_exists ('access vlan', $work['current']))
				$work['current']['access vlan'] = 1;
			$work['portdata'][$port_name] = array
			(
				'mode' => 'access',
				'allowed' => array ($work['current']['access vlan']),
				'native' => $work['current']['access vlan'],
			);
			break;
		case 'trunk':
			if (!array_key_exists ('trunk native vlan', $work['current']))
				$work['current']['trunk native vlan'] = 1;
			if (!array_key_exists ('trunk allowed vlan', $work['current']))
				$work['current']['trunk allowed vlan'] = range (VLAN_MIN_ID, VLAN_MAX_ID);
			// Having configured VLAN as "native" doesn't mean anything
			// as long as it's not listed on the "allowed" line.
			$effective_native = in_array
			(
				$work['current']['trunk native vlan'],
				$work['current']['trunk allowed vlan']
			) ? $work['current']['trunk native vlan'] : 0;
			$work['portdata'][$port_name] = array
			(
				'mode' => 'trunk',
				'allowed' => $work['current']['trunk allowed vlan'],
				'native' => $effective_native,
			);
			break;
		case 'SKIP':
		case 'fex-fabric': // associated port-channel
		case 'IP':
			break;
		default:
			// dot1q-tunnel, dynamic, private-vlan or even none --
			// show in returned config and let user decide, if they
			// want to fix device config or work around these ports
			// by means of VST.
			$work['portdata'][$port_name] = array
			(
				'mode' => 'none',
				'allowed' => array(),
				'native' => 0,
			);
			break;
		}
		unset ($work['current']);
		return 'ios12-get8021q-top';
	}
	// not yet
	$matches = array();
	$line_class = 'line-8021q';
	switch (TRUE)
	{
	case (preg_match ('@^\s+switchport mode (.+)$@', $line, $matches)):
		$work['current']['mode'] = $matches[1];
		break;
	case (preg_match ('@^\s+switchport access vlan (.+)$@', $line, $matches)):
		$work['current']['access vlan'] = $matches[1];
		break;
	case (preg_match ('@^\s+switchport trunk native vlan (.+)$@', $line, $matches)):
		$work['current']['trunk native vlan'] = $matches[1];
		break;
	case (preg_match ('@^\s+switchport trunk allowed vlan add (.+)$@', $line, $matches)):
		$work['current']['trunk allowed vlan'] = array_merge
		(
			$work['current']['trunk allowed vlan'],
			iosParseVLANString ($matches[1])
		);
		break;
	case preg_match ('@^\s+switchport trunk allowed vlan none$@', $line, $matches):
		$work['current']['trunk allowed vlan'] = array();
		break;
	case (preg_match ('@^\s+switchport trunk allowed vlan (.+)$@', $line, $matches)):
		$work['current']['trunk allowed vlan'] = iosParseVLANString ($matches[1]);
		break;
	case preg_match ('@^\s+channel-group @', $line):
	// port-channel subinterface config follows that of the master interface
		$work['current']['mode'] = 'SKIP';
		break;
	case preg_match ('@^\s+ip address @', $line):
	// L3 interface does no switchport functions
		$work['current']['mode'] = 'IP';
		break;
	default: // suppress warning on irrelevant config clause
		$line_class = 'line-other';
	}
	$work['portconfig'][$port_name][] = array ('type' => $line_class, 'line' => $line);
	return 'ios12-get8021q-readport';
}

function ios12PickVLANCommand (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@! END OF VLAN LIST$@', $line)):
		return 'ios12-get8021q-top';
	case (preg_match ('@^([[:digit:]]+) {1,4}.{32} active    @', $line, $matches)):
		$work['vlanlist'][] = $matches[1];
		break;
	default:
	}
	return 'ios12-get8021q-readvlan';
}

// Another finite automata to read a dialect of Foundry configuration.
function fdry5ReadVLANConfig ($input)
{
	$ret = constructRunning8021QConfig();

	global $breedfunc;
	$nextfunc = 'fdry5-get8021q-top';
	foreach (explode ("\n", $input) as $line)
		$nextfunc = $breedfunc[$nextfunc] ($ret, $line);
	return $ret;
}

function fdry5ScanTopLevel (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^vlan ([[:digit:]]+)( name .+)? (by port)$@', $line, $matches)):
		if (!array_key_exists ($matches[1], $work['vlanlist']))
			$work['vlanlist'][] = $matches[1];
		$work['current'] = array ('vlan_id' => $matches[1]);
		return 'fdry5-get8021q-readvlan';
	case (preg_match ('@^interface ethernet ([[:digit:]]+/[[:digit:]]+/[[:digit:]]+)$@', $line, $matches)):
		$port_name = 'e' . $matches[1];
		$work['current'] = array ('port_name' => $port_name);
		$work['portconfig'][$port_name][] = array ('type' => 'line-header', 'line' => $line);
		return 'fdry5-get8021q-readport';
	default:
		return 'fdry5-get8021q-top';
	}
}

function fdry5PickVLANSubcommand (&$work, $line)
{
	if ($line[0] != ' ') // end of VLAN section
	{
		unset ($work['current']);
		return 'fdry5-get8021q-top';
	}
	// not yet
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^ tagged (.+)$@', $line, $matches)):
		// add current VLAN to 'allowed' list of each mentioned port
		foreach (fdry5ParsePortString ($matches[1]) as $port_name)
			if (array_key_exists ($port_name, $work['portdata']))
				$work['portdata'][$port_name]['allowed'][] = $work['current']['vlan_id'];
			else
				$work['portdata'][$port_name] = array
				(
					'mode' => 'trunk',
					'allowed' => array ($work['current']['vlan_id']),
					'native' => 0, // can be updated later
				);
			$work['portdata'][$port_name]['mode'] = 'trunk';
		break;
	case (preg_match ('@^ untagged (.+)$@', $line, $matches)):
		// replace 'native' column of each mentioned port with current VLAN ID
		foreach (fdry5ParsePortString ($matches[1]) as $port_name)
		{
			if (array_key_exists ($port_name, $work['portdata']))
			{
				$work['portdata'][$port_name]['native'] = $work['current']['vlan_id'];
				$work['portdata'][$port_name]['allowed'][] = $work['current']['vlan_id'];
			}
			else
				$work['portdata'][$port_name] = array
				(
					'mode' => 'access',
					'allowed' => array ($work['current']['vlan_id']),
					'native' => $work['current']['vlan_id'],
				);
			// Untagged ports are initially assumed to be access ports, and
			// when this assumption is right, this is the final port mode state.
			// When the port is dual-mode one, this is detected and justified
			// later in "interface" section of config text.
			$work['portdata'][$port_name]['mode'] = 'access';
		}
		break;
	default: // nom-nom
	}
	return 'fdry5-get8021q-readvlan';
}

function fdry5PickInterfaceSubcommand (&$work, $line)
{
	$port_name = $work['current']['port_name'];
	if ($line[0] != ' ') // end of interface section
	{
		$work['portconfig'][$port_name][] = array ('type' => 'line-header', 'line' => $line);
		if (array_key_exists ('dual-mode', $work['current']))
		{
			if (array_key_exists ($port_name, $work['portdata']))
				// update existing record
				$work['portdata'][$port_name]['native'] = $work['current']['dual-mode'];
			else
				// add new
				$work['portdata'][$port_name] = array
				(
					'allowed' => array ($work['current']['dual-mode']),
					'native' => $work['current']['dual-mode'],
				);
			// a dual-mode port is always considered a trunk port
			// (but not in the IronWare's meaning of "trunk") regardless of
			// number of assigned tagged VLANs
			$work['portdata'][$port_name]['mode'] = 'trunk';
		}
		unset ($work['current']);
		return 'fdry5-get8021q-top';
	}
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^ dual-mode( +[[:digit:]]+ *)?$@', $line, $matches)):
		// default VLAN ID for dual-mode command is 1
		$work['current']['dual-mode'] = trim ($matches[1]) != '' ? trim ($matches[1]) : 1;
		break;
	// FIXME: trunk/link-aggregate/ip address pulls port from 802.1Q field
	default: // nom-nom
	}
	$work['portconfig'][$port_name][] = array ('type' => 'line-other', 'line' => $line);
	return 'fdry5-get8021q-readport';
}

# Produce a list of interfaces from a string in the following format:
# ethe 1 ethe 3 ethe 5 to 7 ethe 9
# ethe 1/1 to 1/24 ethe 2/1 to 2/24 ethe 3/1 ethe 3/3 ethe 3/5 to 3/8
# ethe 1/1/1 to 1/1/10 ethe 1/1/12 ethe 1/1/15 to 1/1/20 ethe 2/1/1 to 2/1/24 ethe 3/1/1
function fdry5ParsePortString ($string)
{
	$ret = array();
	$tokens = explode (' ', trim ($string));
	while (count ($tokens))
	{
		$letters = array_shift ($tokens); // "ethe", "to"
		$numbers = array_shift ($tokens); // "x", "x/x", "x/x/x"
		switch ($letters)
		{
		case 'ethe':
			if ($prev_numbers != NULL)
				$ret[] = 'e' . $prev_numbers;
			$prev_numbers = $numbers;
			break;
		case 'to':
			$ret = array_merge ($ret, fdry5GenPortRange ($prev_numbers, $numbers));
			$prev_numbers = NULL; // no action on next token
			break;
		default: // ???
			throw new InvalidArgException ('string', $string, 'format mismatch');
		}
	}
	// flush delayed item
	if ($prev_numbers != NULL)
		$ret[] = 'e' . $prev_numbers;
	return $ret;
}

// Take two indices in form "x", "x/x" or "x/x/x" and return the range of
// ports spanning from the first to the last. The switch software makes it
// easier to perform, because "ethe x/x/x to y/y/y" ranges never cross
// unit/slot boundary (every index except the last remains constant).
function fdry5GenPortRange ($from, $to)
{
	$matches = array();
	if (1 !== preg_match ('@^([[:digit:]]+/)?([[:digit:]]+/)?([[:digit:]]+)$@', $from, $matches))
		return array();
	$prefix = 'e' . $matches[1] . $matches[2];
	$from_idx = $matches[3];
	if (1 !== preg_match ('@^([[:digit:]]+/)?([[:digit:]]+/)?([[:digit:]]+)$@', $to, $matches))
		return array();
	$to_idx = $matches[3];
	for ($i = $from_idx; $i <= $to_idx; $i++)
		$ret[] = $prefix . $i;
	return $ret;
}

# Produce a list of interfaces from a string in the following format:
# gi0/1-5,gi0/7,gi0/9-11,gi0/13,gi0/15,gi0/24
function ros11ParsePortString ($string)
{
	$ret = array();
	foreach (explode (',', $string) as $item)
		if (preg_match ('#^[a-z]+\d+/\d+$#', $item)) # a single interface
			$ret[] = $item;
		elseif (preg_match ('#^([a-z]+\d+/)(\d+)-(\d+)$#', $item, $matches)) # a range
		{
			# Produce a list of interfaces from the given base interface
			# name and upper index.
			if ($matches[3] <= $matches[2])
				throw new InvalidArgException ('string', $string, "format error in '${item}'");
			for ($i = $matches[2]; $i <= $matches[3]; $i++)
				$ret[] = "${matches[1]}{$i}";
		}
		else
			throw new InvalidArgException ('string', $string, "format error in '${item}'");
	return $ret;
}

// an implementation for Huawei syntax
function vrp53ReadVLANConfig ($input)
{
	$ret = constructRunning8021QConfig();

	global $breedfunc;
	$nextfunc = 'vrp53-get8021q-top';
	foreach (explode ("\n", $input) as $line)
		$nextfunc = $breedfunc[$nextfunc] ($ret, $line);
	return $ret;
}

function vrp53ScanTopLevel (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^ vlan batch (.+)$@', $line, $matches)):
		foreach (vrp53ParseVLANString ($matches[1]) as $vlan_id)
			$work['vlanlist'][] = $vlan_id;
		return 'vrp53-get8021q-top';
	case (preg_match ('@^interface ((Ethernet|GigabitEthernet|XGigabitEthernet|Eth-Trunk)([[:digit:]]+(/[[:digit:]]+)*))$@', $line, $matches)):
		$port_name = shortenIfName ($matches[1]);
		$work['current'] = array ('port_name' => $port_name);
		$work['portconfig'][$port_name][] = array ('type' => 'line-header', 'line' => $line);
		return 'vrp53-get8021q-readport';
	default:
		return 'vrp53-get8021q-top';
	}
}

# Produce a list of integers from a string in the following format:
# A B C to D E F to G H to I J to K L ...
function vrp53ParseVLANString ($string)
{
	$string = preg_replace ('/ to /', '-', $string);
	$string = preg_replace ('/ /', ',', $string);
	return iosParseVLANString ($string);
}

function vrp53PickInterfaceSubcommand (&$work, $line)
{
	$port_name = $work['current']['port_name'];
	if ($line[0] == '#') // end of interface section
	{
		$work['portconfig'][$port_name][] = array ('type' => 'line-header', 'line' => $line);
		// Configuration Guide - Ethernet 3.3.4:
		// "By default, the interface type is hybrid."
		if (!array_key_exists ('link-type', $work['current']))
			$work['current']['link-type'] = 'hybrid';
		if (!array_key_exists ('allowed', $work['current']))
			$work['current']['allowed'] = array();
		if (!array_key_exists ('native', $work['current']))
			$work['current']['native'] = 0;
		switch ($work['current']['link-type'])
		{
		case 'access':
			// VRP does not assign access ports to VLAN1 by default,
			// leaving them blocked.
			$work['portdata'][$port_name] =
				$work['current']['native'] ? array
				(
					'allowed' => $work['current']['allowed'],
					'native' => $work['current']['native'],
					'mode' => 'access',
				) : array
				(
					'mode' => 'none',
					'allowed' => array(),
					'native' => 0,
				);
			break;
		case 'trunk':
			$work['portdata'][$port_name] = array
			(
				'allowed' => $work['current']['allowed'],
				'native' => 0,
				'mode' => 'trunk',
			);
			break;
		case 'hybrid':
			$work['portdata'][$port_name] = array
			(
				'allowed' => $work['current']['allowed'],
				'native' => in_array ($work['current']['native'], $work['current']['allowed']) ? $work['current']['native'] : 0,
				'mode' => 'trunk',
			);
			break;
		case 'SKIP':
		default: // dot1q-tunnel ?
		}
		unset ($work['current']);
		return 'vrp53-get8021q-top';
	}
	$matches = array();
	$line_class = 'line-8021q';
	switch (TRUE)
	{
	case (preg_match ('@^ port default vlan ([[:digit:]]+)$@', $line, $matches)):
		$work['current']['native'] = $matches[1];
		if (!array_key_exists ('allowed', $work['current']))
			$work['current']['allowed'] = array();
		break;
	case (preg_match ('@^ port link-type (.+)$@', $line, $matches)):
		$work['current']['link-type'] = $matches[1];
		break;
	case (preg_match ('@^ port trunk allow-pass vlan (.+)$@', $line, $matches)):
		if (!array_key_exists ('allowed', $work['current']))
			$work['current']['allowed'] = array();
		foreach (vrp53ParseVLANString ($matches[1]) as $vlan_id)
			if (!in_array ($vlan_id, $work['current']['allowed']))
				$work['current']['allowed'][] = $vlan_id;
		break;
	case preg_match ('/^\s*eth-trunk \d+/', $line):
		$work['current']['link-type'] = 'SKIP';
		break;
	default: // nom-nom
		$line_class = 'line-other';
	}
	$work['portconfig'][$port_name][] = array('type' => $line_class, 'line' => $line);
	return 'vrp53-get8021q-readport';
}

function vrp55Read8021QConfig ($input)
{
	$ret = constructRunning8021QConfig();
	$ret['vlanlist'][] = VLAN_DFL_ID; // VRP 5.50 hides VLAN1 from config text

	foreach (explode ("\n", $input) as $line)
	{
		$matches = array();
		// top level
		if (!array_key_exists ('current', $ret))
		{
			switch (TRUE)
			{
			case (preg_match ('@^ vlan batch (.+)$@', $line, $matches)):
				foreach (vrp53ParseVLANString ($matches[1]) as $vlan_id)
					$ret['vlanlist'][] = $vlan_id;
				break;
			case (preg_match ('@^interface ((Ethernet|GigabitEthernet|XGigabitEthernet|Eth-Trunk)([[:digit:]]+(/[[:digit:]]+)*))$@', $line, $matches)):
				$port_name = shortenIfName ($matches[1]);
				$ret['current'] = array
				(
					'port_name' => $port_name,
					'allowed' => array (VLAN_DFL_ID),
					'native' => VLAN_DFL_ID,
				);
				$ret['portconfig'][$port_name][] = array ('type' => 'line-header', 'line' => $line);
				break;
			}
			continue;
		}
		$port_name = $ret['current']['port_name'];
		// inside an interface block
		$line_class = 'line-8021q';
		switch (TRUE)
		{
		case preg_match ('/^ port (link-type )?hybrid /', $line):
			throw new RTGatewayError ("unsupported hybrid link-type for $port_name: ${line}");
		case preg_match ('/^ port link-type (.+)$/', $line, $matches):
			$ret['current']['link-type'] = $matches[1];
			break;
		// Native VLAN is configured differently for each link-type case, but
		// VRP is known to filter off clauses that don't make sense for
		// current link-type. This way any interface section should contain
		// only one kind of "set native" clause (but if this constraint breaks,
		// there is a problem).
		case preg_match ('/^ port (default|trunk pvid) vlan ([[:digit:]]+)$/', $line, $matches):
			$ret['current']['native'] = $matches[2];
			break;
		case preg_match ('/^ port trunk allow-pass vlan (.+)$/', $line, $matches):
			foreach (vrp53ParseVLANString ($matches[1]) as $vlan_id)
				if (!in_array ($vlan_id, $ret['current']['allowed']))
					$ret['current']['allowed'][] = $vlan_id;
			break;
		case preg_match ('/^ undo port trunk allow-pass vlan (.+)$/', $line, $matches):
			$ret['current']['allowed'] = array_diff ($ret['current']['allowed'], vrp53ParseVLANString ($matches[1]));
			break;
		case $line == ' undo portswitch':
		case preg_match ('/^ ip address /', $line):
		case preg_match ('/^ service type /', $line):
			$ret['current']['link-type'] = 'IP';
			break;
		case preg_match ('/^ eth-trunk /', $line):
			$ret['current']['link-type'] = 'SKIP';
			break;
		case substr ($line, 0, 1) == '#': // end of interface section
			$line_class = 'line-header';
			if (!array_key_exists ('link-type', $ret['current']))
				$ret['current']['link-type'] = 'hybrid';
			switch ($ret['current']['link-type'])
			{
			case 'access':
				// In VRP 5.50 an access port has default VLAN ID == 1
				$ret['portdata'][$port_name] =
					$ret['current']['native'] ? array
					(
						'mode' => 'access',
						'allowed' => array ($ret['current']['native']),
						'native' => $ret['current']['native'],
					) : array
					(
						'mode' => 'access',
						'allowed' => array (VLAN_DFL_ID),
						'native' => VLAN_DFL_ID,
					);
				break;
			case 'trunk':
				$ret['portdata'][$port_name] = array
				(
					'mode' => 'trunk',
					'allowed' => $ret['current']['allowed'],
					'native' => in_array ($ret['current']['native'], $ret['current']['allowed']) ? $ret['current']['native'] : 0,
				);
				break;
			case 'IP':
			case 'SKIP':
				break;
			case 'hybrid': // hybrid ports are not supported
			default: // dot1q-tunnel ?
				$ret['portdata'][$port_name] = array
				(
					'mode' => 'none',
					'allowed' => array(),
					'native' => 0,
				);
				break;
			}
			unset ($ret['current']);
			break;
		default: // nom-nom
			$line_class = 'line-other';
		}
		$ret['portconfig'][$port_name][] = array ('type' => $line_class, 'line' => $line);
	}
	return $ret;
}

function vrp85Read8021QConfig ($input)
{
	$ret = constructRunning8021QConfig();
	$ret['vlanlist'][] = VLAN_DFL_ID; // VRP 8+ hides VLAN1 from config text

	$state = 'skip';
	$current = array();

	foreach (explode ("\n", $input) as $line)
	{
		$line = rtrim ($line);
		do switch ($state)
		{
			case 'skip':
				if (preg_match('/^Port\s+.*PVID/i', $line))
					$state = 'ports';
				break;
			case 'ports':
				if (isset ($current['name']))
				{
					if (preg_match('/^\s+(\d.*)/', $line, $m))
						$current['allowed'] .= ' ' . $m[1];
					else
					{
						// port-channel members are displayed in 'display port vlan' with PVID = 0.
						if ($current['native'] >= VLAN_MIN_ID && $current['native'] <= VLAN_MAX_ID)
						{
							// commit $current into portdata
							$data = array
							(
								'mode' => $current['mode'],
								'native' => $current['native'],
								'allowed' => array(),
							);
							$range = trim (preg_replace('/\s+/', ',', $current['allowed']), ',-');
							$data['allowed'] = $range == '' ? array() : iosParseVLANString ($range);
							if ($data['mode'] == 'access')
								$data['allowed'] = array ($current['native']);
							elseif ($data['mode'] == 'trunk')
							{
								if (! in_array ($data['native'], $data['allowed']))
									$data['native'] = 0;
							}
							else
							{
								$data['allowed'] = array();
								$data['native'] = 0;
							}
							$ret['portdata'][$current['name']] = $data;
						}
						$current = array();
					}
				}
				if (preg_match ('/^</', $line))
					$state = 'conf';
				elseif (preg_match ('/^(\S+)\s+(\w+)\s+(\d+)\s+(.*)$/', $line, $m))
				{
					$current['name'] = shortenIfName ($m[1]);
					$current['mode'] = ($m[2] == 'access' || $m[2] == 'trunk') ? $m[2] : 'none';
					$current['native'] = intval ($m[3]);
					$current['allowed'] = $m[4];
				}
				break;
			case 'conf':
				if (preg_match ('/^interface (\S+)$/', $line, $m))
				{
					$current['name'] = shortenIfName ($m[1]);
					$current['lines'] = array (array ('type' => 'line-header', 'line' => $line));
					$state = 'iface';
				}
				elseif (preg_match ('@vlan batch (.+)@', $line, $matches))
					foreach (vrp53ParseVLANString ($matches[1]) as $vlan_id)
						$ret['vlanlist'][] = $vlan_id;
				break;
			case 'iface':
				$line_class = ($line == '#') ? 'line-header' : 'line-other';
				if (preg_match ('/^\s*port (trunk|link-type|default vlan)/', $line))
					$line_class = 'line-8021q';
				$current['lines'][] = array ('type' => $line_class, 'line' => $line);
				if ($line == '#')
				{
					// commit $current into portconfig
					$ret['portconfig'][$current['name']] = $current['lines'];
					$current = array();
					$state = 'conf';
				}
				break;
			default:
				throw new RackTablesError ("Unknown FSM state '$state'", RackTablesError::INTERNAL);
		}
		while (FALSE);
	}

	return $ret;
}

/*
D-Link VLAN info sample:
========================
VID             : 72          VLAN Name       : v72
VLAN Type       : Static      Advertisement   : Disabled
Member Ports    : 1-16,25-28
Static Ports    : 1-16,25-28
Current Tagged Ports   : 25-28
Current Untagged Ports : 1-16
Static Tagged Ports    : 25-28
Static Untagged Ports  : 1-16
Forbidden Ports        :
*/
function dlinkReadVLANConfig ($input)
{
	$ret = constructRunning8021QConfig();

	global $breedfunc;
	$nextfunc = 'dlink-get8021q-top';
	foreach (explode ("\n", $input) as $line)
		$nextfunc = $breedfunc[$nextfunc] ($ret, $line);
	return $ret;
}

function dlinkScanTopLevel (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case preg_match ('@^\s*VID\s*:\s*(\d+)\s+.*name\s*:\s*(.+)$@i', $line, $matches):
		$work['current'] = array
		(
			'vlan_id' => $matches[1],
			'vlan_name' => $matches[2],
			'tagged_ports' => '',
			'untagged_ports' => '',
		);
		return 'dlink-get8021q-pickvlan';
	default:
		return 'dlink-get8021q-top';
	}
}

function dlinkPickVLANCommand (&$work, $line)
{
	switch (TRUE)
	{
	case preg_match ('@END OF VLAN LIST@', $line):
	case trim ($line) === '':
		if (!isset($work['current']))
			break;
		$work['vlanlist'][] = $work['current']['vlan_id'];
		# portlist = range[,range..]
		# range = N[-N]
		foreach (iosParseVLANString ($work['current']['tagged_ports']) as $port_name)
			dlinkStorePortInfo ($work, $port_name, 'trunk', 'trunk');
		foreach (iosParseVLANString ($work['current']['untagged_ports']) as $port_name)
			dlinkStorePortInfo ($work, $port_name, 'access');
		unset ($work['current']);
		return 'dlink-get8021q-top';
	case preg_match ('@current tagged ports\s*:\s*([[:digit:]]+.*)$@i', $line, $matches):
		$work['current']['tagged_ports'] = $matches[1];
		break;
	case preg_match ('@current untagged ports\s*:\s*([[:digit:]]+.*)$@i', $line, $matches):
		$work['current']['untagged_ports'] = $matches[1];
		break;
	}
	return 'dlink-get8021q-pickvlan';
}

function dlinkStorePortInfo (&$work, $port_name, $new_mode, $overwrite_mode = '')
{
	if (! array_key_exists ($port_name, $work['portdata']))
	{
		$work['portdata'][$port_name] = array
		(
			'mode' => $new_mode,
			'allowed' => array ($work['current']['vlan_id']),
			'native' => $work['current']['vlan_id']
		);
		return;
	}
	$work['portdata'][$port_name]['allowed'][] = $work['current']['vlan_id'];
	if ($overwrite_mode !== '')
		$work['portdata'][$port_name]['mode'] = $overwrite_mode;
}

function linuxReadVLANConfig ($input)
{
	$ret = constructRunning8021QConfig();
	$ret['vlanlist'][] = VLAN_DFL_ID;

	foreach (explode ("\n", $input) as $line)
	{
		// 13: vlan11@eth0: <BROADCAST,MULTICAST,UP,LOWER_UP> mtu 1500 qdisc noqueue state UP \    link/ether 00:1e:34:ae:75:21 brd ff:ff:ff:ff:ff:ff
		$matches = array();
		if (! preg_match ('/^[[:digit:]]+:\s+([^\s]+):\s.*\slink\/ether\s/', $line, $matches))
			continue;
		$iface = $matches[1];
		if (preg_match ('/^(eth[[:digit:]]+)\.0*([[:digit:]]+):?$/', $iface, $matches))
			linuxStoreVLANInfo ($ret, 'vlan'.$matches[2], $matches[1], $matches[2]);
		elseif (preg_match('/^vlan0*([[:digit:]]+)\@(.*)$/', $iface, $matches))
			linuxStoreVLANInfo ($ret, 'vlan'.$matches[1], $matches[2], $matches[1]);
		elseif (! array_key_exists ($iface, $ret['portdata']))
			$ret['portdata'][$iface] = array ('mode' => 'access', 'native' => 0, 'allowed' => array());
	}
	return $ret;
}

function linuxStoreVLANInfo (&$ret, $iface, $baseport, $vid)
{
	$ret['vlanlist'][] = $vid;
	if (! array_key_exists ($baseport, $ret['portdata']))
		$ret['portdata'][$baseport] = array ('mode' => 'trunk', 'native' => 0, 'allowed' => array ($vid));
	else
	{
		$ret['portdata'][$baseport]['mode'] = 'trunk';
		$ret['portdata'][$baseport]['allowed'][] = $vid;
	}
	if (! array_key_exists ($iface, $ret['portdata']))
		$ret['portdata'][$iface] = array ('mode' => 'access', 'native' => $vid, 'allowed' => array ($vid));
}

// most of the commands are compatible with IOS12, so are generated by ios12TranslatePushQueue
// Only Nexus-specific commands are generated here (eg., lldp)
function nxos4TranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';

	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'set mode':
			if ($cmd['arg2'] == 'trunk')
			{
				// Some NX-OS platforms interactively ask for a confirmation if the CLI user
				// is trying to overwrite the allowed VLAN list for a port. The differentiative
				// remove syntax works around this problem.
				$ret .= "interface ${cmd['arg1']}\n";
				$ret .= "switchport trunk encapsulation dot1q\n";
				$ret .= "switchport mode ${cmd['arg2']}\n";
				$ret .= "no switchport trunk native vlan\n";
				$ret .= "switchport trunk allowed vlan remove 1-4094\n";
				break;
			}
			// fall-through
		default:
			$ret .= ios12TranslatePushQueue ($dummy_object_id, array ($cmd), $dummy_vlan_names);
			break;
		}
	return $ret;
}

// Get a list of VLAN management pseudo-commands and return a text
// of real vendor-specific commands that implement the work.
// This work is done in two rounds:
// 1. For "add allowed" and "rem allowed" commands detect continuous
//    sequences of VLAN IDs and replace them with ranges of form "A-B",
//    where B>A.
// 2. Iterate over the resulting list and produce real CLI commands.
function ios12TranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
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
		case 'rem allowed':
			$clause = $cmd['opcode'] == 'add allowed' ? 'add' : 'remove';
			$ret .= "interface ${cmd['port']}\n";
			foreach (listToRanges ($cmd['vlans']) as $range)
				$ret .= "switchport trunk allowed vlan ${clause} " .
					($range['from'] == $range['to'] ? $range['to'] : "${range['from']}-${range['to']}") .
					"\n";
			$ret .= "exit\n";
			break;
		case 'set native':
			$ret .= "interface ${cmd['arg1']}\nswitchport trunk native vlan ${cmd['arg2']}\nexit\n";
			break;
		case 'unset native':
			$ret .= "interface ${cmd['arg1']}\nno switchport trunk native vlan ${cmd['arg2']}\nexit\n";
			break;
		case 'set access':
			$ret .= "interface ${cmd['arg1']}\nswitchport access vlan ${cmd['arg2']}\nexit\n";
			break;
		case 'unset access':
			$ret .= "interface ${cmd['arg1']}\nno switchport access vlan\nexit\n";
			break;
		case 'set mode':
			$ret .= "interface ${cmd['arg1']}\n";
			if ($cmd['arg2'] == 'trunk')
				$ret .= "switchport trunk encapsulation dot1q\n";
			$ret .= "switchport mode ${cmd['arg2']}\n";
			if ($cmd['arg2'] == 'trunk')
				$ret .= "no switchport trunk native vlan\nswitchport trunk allowed vlan none\n";
			$ret .= "exit\n";
			break;
		case 'begin configuration':
			$ret .= "configure terminal\n";
			break;
		case 'end configuration':
			$ret .= "end\n";
			break;
		case 'save configuration':
			$ret .= "copy running-config startup-config\n\n";
			break;
		case 'cite':
			$ret .= $cmd['arg1'];
			break;
		// query list
		case 'get8021q':
			$ret .=
'show interface switchport | incl Name:|Switchport:
! END OF SWITCHPORTS
show run
! END OF CONFIG
show vlan brief
! END OF VLAN LIST
';
			break;
		case 'getcdpstatus':
			$ret .= "show cdp neighbors detail\n";
			break;
		case 'getlldpstatus':
			$ret .= "show lldp neighbors\n";
			break;
		case 'getportstatus':
			$ret .= "show int status\n";
			break;
		case 'getmaclist':
			$ret .= "show mac address-table dynamic\n";
			break;
		case 'getportmaclist':
			$ret .= "show mac address-table dynamic interface {$cmd['arg1']}\n";
			break;
		case 'getallconf':
			$ret .= "show running-config\n";
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function fdry5TranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
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
				$ret .= "vlan ${vlan_id}\ntagged ${cmd['port']}\nexit\n";
			break;
		case 'rem allowed':
			foreach ($cmd['vlans'] as $vlan_id)
				$ret .= "vlan ${vlan_id}\nno tagged ${cmd['port']}\nexit\n";
			break;
		case 'set native':
			$ret .= "interface ${cmd['arg1']}\ndual-mode ${cmd['arg2']}\nexit\n";
			break;
		case 'unset native':
			$ret .= "interface ${cmd['arg1']}\nno dual-mode ${cmd['arg2']}\nexit\n";
			break;
		case 'set access':
			$ret .= "vlan ${cmd['arg2']}\nuntagged ${cmd['arg1']}\nexit\n";
			break;
		case 'unset access':
			$ret .= "vlan ${cmd['arg2']}\nno untagged ${cmd['arg1']}\nexit\n";
			break;
		case 'set mode': // NOP
			break;
		case 'begin configuration':
			$ret .= "conf t\n";
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
			$ret .= "show running-config\n";
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

function vrp53TranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'create VLAN':
			$ret .= "vlan ${cmd['arg1']}\nquit\n";
			break;
		case 'destroy VLAN':
			$ret .= "undo vlan ${cmd['arg1']}\n";
			break;
		case 'add allowed':
		case 'rem allowed':
			$clause = $cmd['opcode'] == 'add allowed' ? '' : 'undo ';
			$ret .= "interface ${cmd['port']}\n";
			foreach (listToRanges ($cmd['vlans']) as $range)
				$ret .=  "${clause}port trunk allow-pass vlan " .
					($range['from'] == $range['to'] ? $range['to'] : "${range['from']} to ${range['to']}") .
					"\n";
			$ret .= "quit\n";
			break;
		case 'set native':
		case 'set access':
			$ret .= "interface ${cmd['arg1']}\nport default vlan ${cmd['arg2']}\nquit\n";
			break;
		case 'unset native':
		case 'unset access':
			$ret .= "interface ${cmd['arg1']}\nundo port default vlan\nquit\n";
			break;
		case 'set mode':
			$modemap = array ('access' => 'access', 'trunk' => 'hybrid');
			$ret .= "interface ${cmd['arg1']}\nport link-type " . $modemap[$cmd['arg2']] . "\n";
			if ($cmd['arg2'] == 'hybrid')
				$ret .= "undo port default vlan\nundo port trunk allow-pass vlan all\n";
			$ret .= "quit\n";
			break;
		case 'begin configuration':
			$ret .= "system-view\n";
			break;
		case 'end configuration':
			$ret .= "return\n";
			break;
		case 'save configuration':
			$ret .= "save\nY\n";
			break;
		case 'cite':
			$ret .= $cmd['arg1'];
			break;
		// query list
		case 'get8021q':
			$ret .= "display current-configuration\n";
			break;
		case 'getlldpstatus':
			$ret .= "display lldp neighbor\n";
			break;
		case 'getportstatus':
			$ret .= "display interface brief\n";
			break;
		case 'getmaclist':
			$ret .= "display mac-address dynamic\n";
			break;
		case 'getportmaclist':
			$ret .= "display mac-address dynamic {$cmd['arg1']}\n";
			break;
		case 'getallconf':
			$ret .= "display current-configuration\n";
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function vrp55TranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'create VLAN':
			if ($cmd['arg1'] != 1)
				$ret .= "vlan ${cmd['arg1']}\nquit\n";
			break;
		case 'destroy VLAN':
			if ($cmd['arg1'] != 1)
				$ret .= "undo vlan ${cmd['arg1']}\n";
			break;
		case 'add allowed':
		case 'rem allowed':
			$undo = $cmd['opcode'] == 'add allowed' ? '' : 'undo ';
			$ret .= "interface ${cmd['port']}\n";
			foreach (listToRanges ($cmd['vlans']) as $range)
				$ret .=  "${undo}port trunk allow-pass vlan " .
					($range['from'] == $range['to'] ? $range['to'] : "${range['from']} to ${range['to']}") .
					"\n";
			$ret .= "quit\n";
			break;
		case 'set native':
			$ret .= "interface ${cmd['arg1']}\nport trunk pvid vlan ${cmd['arg2']}\nquit\n";
			break;
		case 'set access':
			$ret .= "interface ${cmd['arg1']}\nport default vlan ${cmd['arg2']}\nquit\n";
			break;
		case 'unset native':
			$ret .= "interface ${cmd['arg1']}\nundo port trunk pvid vlan\nquit\n";
			break;
		case 'unset access':
			$ret .= "interface ${cmd['arg1']}\nundo port default vlan\nquit\n";
			break;
		case 'set mode':
			// VRP 5.50's meaning of "trunk" is much like the one of IOS
			// (unlike the way VRP 5.30 defines "trunk" and "hybrid"),
			// but it is necessary to undo configured VLANs on a port
			// for mode change command to succeed.
			$before = array
			(
				'access' => "undo port trunk allow-pass vlan all\n" .
					"port trunk allow-pass vlan 1\n" .
					"undo port trunk pvid vlan\n",
				'trunk' => "undo port default vlan\n",
			);
			$after = array
			(
				'access' => '',
				'trunk' => "undo port trunk allow-pass vlan 1\n",
			);
			$ret .= "interface ${cmd['arg1']}\n";
			$ret .= $before[$cmd['arg2']];
			$ret .= "port link-type ${cmd['arg2']}\n";
			$ret .= $after[$cmd['arg2']];
			$ret .= "quit\n";
			break;
		case 'begin configuration':
			$ret .= "system-view\n";
			break;
		case 'end configuration':
			$ret .= "return\n";
			break;
		case 'save configuration':
			$ret .= "save\nY\n";
			break;
		case 'cite':
			$ret .= $cmd['arg1'];
			break;
		// query list
		case 'get8021q':
			$ret .= "display current-configuration\n";
			break;
		case 'getlldpstatus':
			$ret .= "display lldp neighbor\n";
			break;
		case 'getportstatus':
			$ret .= "display interface brief\n";
			break;
		case 'getmaclist':
			$ret .= "display mac-address dynamic\n";
			break;
		case 'getportmaclist':
			$ret .= "display mac-address dynamic {$cmd['arg1']}\n";
			break;
		case 'getallconf':
			$ret .= "display current-configuration\n";
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function vrp85TranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'create VLAN':
			if ($cmd['arg1'] != 1)
				$ret .= "vlan ${cmd['arg1']}\nquit\n";
			break;
		case 'destroy VLAN':
			if ($cmd['arg1'] != 1)
				$ret .= "undo vlan ${cmd['arg1']}\n";
			break;
		case 'add allowed':
		case 'rem allowed':
			$undo = $cmd['opcode'] == 'add allowed' ? '' : 'undo ';
			$ret .= "interface ${cmd['port']}\n";
			foreach (listToRanges ($cmd['vlans']) as $range)
				$ret .=  "${undo}port trunk allow-pass vlan " .
					($range['from'] == $range['to'] ? $range['to'] : "${range['from']} to ${range['to']}") .
					"\n";
			$ret .= "quit\n";
			break;
		case 'set native':
			$ret .= "interface ${cmd['arg1']}\nport trunk pvid vlan ${cmd['arg2']}\nquit\n";
			break;
		case 'set access':
			$ret .= "interface ${cmd['arg1']}\nport default vlan ${cmd['arg2']}\nquit\n";
			break;
		case 'unset native':
			$ret .= "interface ${cmd['arg1']}\nundo port trunk pvid vlan\nquit\n";
			break;
		case 'unset access':
			$ret .= "interface ${cmd['arg1']}\nundo port default vlan\nquit\n";
			break;
		case 'set mode':
			// VRP 5.50's meaning of "trunk" is much like the one of IOS
			// (unlike the way VRP 5.30 defines "trunk" and "hybrid"),
			// but it is necessary to undo configured VLANs on a port
			// for mode change command to succeed.
			$before = array
			(
				'access' => "undo port trunk allow-pass vlan all\n" .
					"port trunk allow-pass vlan 1\n" .
					"undo port trunk pvid vlan\n",
				'trunk' => "undo port default vlan\n",
			);
			$after = array
			(
				'access' => '',
				'trunk' => "undo port trunk allow-pass vlan 1\n",
			);
			$ret .= "interface ${cmd['arg1']}\n";
			$ret .= $before[$cmd['arg2']];
			$ret .= "port link-type ${cmd['arg2']}\n";
			$ret .= $after[$cmd['arg2']];
			$ret .= "quit\n";
			break;
		case 'begin configuration':
			$ret .= "system-view immediately\n";
			break;
		case 'end configuration':
			$ret .= "return\n";
			break;
		case 'save configuration':
			$ret .= "save\nY\n";
			break;
		case 'cite':
			$ret .= $cmd['arg1'];
			break;
		// query list
		case 'get8021q':
			$ret .= "display port vlan\n";
			$ret .= "display current-configuration\n";
			break;
		case 'getlldpstatus':
			$ret .= "display lldp neighbor\n";
			break;
		case 'getportstatus':
			$ret .= "display interface brief\n";
			break;
		case 'getmaclist':
			$ret .= "display mac-address dynamic\n";
			break;
		case 'getportmaclist':
			$ret .= "display mac-address dynamic interface {$cmd['arg1']}\n";
			break;
		case 'getallconf':
			$ret .= "display current-configuration\n";
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function xos12TranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'create VLAN':
			$ret .= "create vlan VLAN${cmd['arg1']}\n";
			$ret .= "configure vlan VLAN${cmd['arg1']} tag ${cmd['arg1']}\n";
			break;
		case 'destroy VLAN':
			$ret .= "delete vlan VLAN${cmd['arg1']}\n";
			break;
		case 'add allowed':
			foreach ($cmd['vlans'] as $vlan_id)
			{
				$vlan_name = $vlan_id == 1 ? 'Default' : "VLAN${vlan_id}";
				$ret .= "configure vlan ${vlan_name} add ports ${cmd['port']} tagged\n";
			}
			break;
		case 'rem allowed':
			foreach ($cmd['vlans'] as $vlan_id)
			{
				$vlan_name = $vlan_id == 1 ? 'Default' : "VLAN${vlan_id}";
				$ret .= "configure vlan ${vlan_name} delete ports ${cmd['port']}\n";
			}
			break;
		case 'set native':
			$vlan_name = $cmd['arg2'] == 1 ? 'Default' : "VLAN${cmd['arg2']}";
			$ret .= "configure vlan ${vlan_name} delete ports ${cmd['arg1']}\n";
			$ret .= "configure vlan ${vlan_name} add ports ${cmd['arg1']} untagged\n";
			break;
		case 'unset native':
			$vlan_name = $cmd['arg2'] == 1 ? 'Default' : "VLAN${cmd['arg2']}";
			$ret .= "configure vlan ${vlan_name} delete ports ${cmd['arg1']}\n";
			$ret .= "configure vlan ${vlan_name} add ports ${cmd['arg1']} tagged\n";
			break;
		case 'set access':
			$vlan_name = $cmd['arg2'] == 1 ? 'Default' : "VLAN${cmd['arg2']}";
			$ret .= "configure vlan ${vlan_name} add ports ${cmd['arg1']} untagged\n";
			break;
		case 'unset access':
			$vlan_name = $cmd['arg2'] == 1 ? 'Default' : "VLAN${cmd['arg2']}";
			$ret .= "configure vlan ${vlan_name} delete ports ${cmd['arg1']}\n";
			break;
		case 'set mode':
		case 'begin configuration':
		case 'end configuration':
			break; // NOP
		case 'save configuration':
			$ret .= "save configuration\ny\n";
			break;
		case 'cite':
			$ret .= $cmd['arg1'];
			break;
		// query list
		case 'get8021q':
			$ret .= 'show configuration "vlan"' . "\n";
			break;
		case 'getlldpstatus':
			$ret .= "show lldp neighbors detailed\n";
			break;
		case 'getallconf':
			$ret .= "show configuration\n";
			break;
		case 'getportstatus':
			$ret .= "show ports no-refresh\n";
			break;
		case 'getmaclist':
			$ret .= "show fdb\n";
			break;
		case 'getportmaclist':
			$ret .= "show fdb ports {$cmd['arg1']}\n";
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function jun10TranslatePushQueue ($dummy_object_id, $queue, $vlan_names)
{
	$ret = '';

	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'create VLAN':
			$ret .= "set vlans VLAN${cmd['arg1']} vlan-id ${cmd['arg1']}\n";
			break;
		case 'destroy VLAN':
			if (isset ($vlan_names[$cmd['arg1']]))
				$ret .= "delete vlans " . $vlan_names[$cmd['arg1']] . "\n";
			break;
		case 'add allowed':
		case 'rem allowed':
			$del = ($cmd['opcode'] == 'rem allowed');
			$pre = ($del ? 'delete' : 'set') .
				" interfaces ${cmd['port']} unit 0 family ethernet-switching vlan members";
			if (count ($cmd['vlans']) > VLAN_MAX_ID - VLAN_MIN_ID)
				$ret .= "$pre " . ($del ? '' : 'all') . "\n";
			else
				while (count ($cmd['vlans']))
				{
					$vlan = array_shift ($cmd['vlans']);
					$ret .= "$pre $vlan\n";
					if ($del && isset ($vlan_names[$vlan]))
						$ret .= "$pre ${vlan_names[$vlan]}\n";
				}
			break;
		case 'set native':
			$ret .= "set interfaces ${cmd['arg1']} unit 0 family ethernet-switching native-vlan-id ${cmd['arg2']}\n";
			$pre = "delete interfaces ${cmd['arg1']} unit 0 family ethernet-switching vlan members";
			$vlan = $cmd['arg2'];
			$ret .= "$pre $vlan\n";
			if (isset ($vlan_names[$vlan]))
				$ret .= "$pre ${vlan_names[$vlan]}\n";
			break;
		case 'unset native':
			$ret .= "delete interfaces ${cmd['arg1']} unit 0 family ethernet-switching native-vlan-id\n";
			$pre = "interfaces ${cmd['arg1']} unit 0 family ethernet-switching vlan members";
			$vlan = $cmd['arg2'];
			if (isset ($vlan_names[$vlan]))
				$ret .= "delete $pre ${vlan_names[$vlan]}\n";
			$ret .= "set $pre $vlan\n";
			break;
		case 'set access':
			$ret .= "set interfaces ${cmd['arg1']} unit 0 family ethernet-switching vlan members ${cmd['arg2']}\n";
			break;
		case 'unset access':
			$ret .= "delete interfaces ${cmd['arg1']} unit 0 family ethernet-switching vlan members\n";
			break;
		case 'set mode':
			$ret .= "set interfaces ${cmd['arg1']} unit 0 family ethernet-switching port-mode ${cmd['arg2']}\n";
			break;
		case 'begin configuration':
			$ret .= "configure exclusive\n";
			break;
		case 'end configuration':
			$ret .= "commit\n";
			$ret .= "rollback 0\n"; // discard all changes if commit failed
			break;
		case 'save configuration':
			break; // JunOS can`t apply configuration without saving it
		case 'cite':
			$ret .= $cmd['arg1'];
			break;
		// query list
		case 'get8021q':
			$ret .=
'show vlans detail
# END OF VLAN LIST
show configuration groups
# END OF GROUP LIST
show configuration interfaces
# END OF CONFIG
';
			break;
		case 'getallconf':
			$ret .= "show configuration\n";
			break;
		case 'getlldpstatus':
			$ret .= "show lldp neighbors\n";
			break;
		case 'getportstatus':
			$ret .= "show interfaces terse\n";
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function ftos8TranslatePushQueue ($dummy_object_id, $queue, $vlan_names)
{
	$ret = '';
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'begin configuration':
			$ret .= "configure terminal\n";
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
		case 'getlldpstatus':
			$ret .= "show lldp neighbors detail\n";
			break;
		case 'getportstatus':
			$ret .= "show interfaces status\n";
			break;
		case 'getmaclist':
			$ret .= "show mac-address-table dynamic\n";
			break;
		case 'getportmaclist':
			$ret .= "show mac-address-table dynamic interface {$cmd['arg1']}\n";
			break;
		case 'get8021q':
			$ret .= "show running-config interface\n";
			break;
		case 'create VLAN':
			$ret .= "int vlan ${cmd['arg1']}\nexit\n";
			break;
		case 'destroy VLAN':
			$ret .= "no int vlan ${cmd['arg1']}\n";
			break;
		case 'rem allowed':
			while (count ($cmd['vlans']))
			{
				$vlan = array_shift ($cmd['vlans']);
				$ret .= "int vlan $vlan\n";
				$ret .= "no tagged ${cmd['port']}\n";
				$ret .= "exit\n";
			}
			break;
		case 'add allowed':
			while (count ($cmd['vlans']))
			{
				$vlan = array_shift ($cmd['vlans']);
				$ret .= "int vlan $vlan\n";
				$ret .= "tagged ${cmd['port']}\n";
				$ret .= "exit\n";
			}
			break;
		case 'unset native':
			$ret .= "int vlan ${cmd['arg2']}\n";
			$ret .= "no untagged ${cmd['arg1']}\n";
			$ret .= "tagged ${cmd['arg1']}\n";
			$ret .= "exit\n";
			break;
		case 'unset access':
			$ret .= "int vlan ${cmd['arg2']}\n";
			$ret .= "no untagged ${cmd['arg1']}\n";
			$ret .= "exit\n";
			break;
		case 'set native':
			$ret .= "int vlan ${cmd['arg2']}\n";
			$ret .= "no tagged ${cmd['arg1']}\n";
			$ret .= "untagged ${cmd['arg1']}\n";
			$ret .= "exit\n";
			break;
		case 'set access':
			$ret .= "int vlan ${cmd['arg2']}\n";
			$ret .= "untagged ${cmd['arg1']}\n";
			$ret .= "exit\n";
			break;
		case 'set mode':
			break;
		case 'getallconf':
			$ret .= "show running-config\n";
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function air12TranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'begin configuration':
			$ret .= "configure terminal\n";
			break;
		case 'end configuration':
			$ret .= "end\n";
			break;
		case 'save configuration':
			$ret .= "copy running-config startup-config\n\n";
			break;
		case 'cite':
			$ret .= $cmd['arg1'];
			break;
		case 'getcdpstatus':
			$ret .= "show cdp neighbors detail\n";
			break;
		case 'getallconf':
			$ret .= "show running-config\n";
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function eos4TranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'begin configuration':
			$ret .= "enable\nconfigure terminal\n";
			break;
		case 'end configuration':
			$ret .= "end\n";
			break;
		case 'save configuration':
			$ret .= "copy running-config startup-config\n\n";
			break;
		case 'create VLAN':
			$ret .= "vlan ${cmd['arg1']}\nexit\n";
			break;
		case 'destroy VLAN':
			$ret .= "no vlan ${cmd['arg1']}\n";
			break;
		case 'set access':
			$ret .= "interface ${cmd['arg1']}\nswitchport access vlan ${cmd['arg2']}\nexit\n";
			break;
		case 'unset access':
			$ret .= "interface ${cmd['arg1']}\nno switchport access vlan\nexit\n";
			break;
		case 'set mode':
			$ret .= "interface ${cmd['arg1']}\n";
			$ret .= "switchport mode ${cmd['arg2']}\n";
			if ($cmd['arg2'] == 'trunk')
				$ret .= "no switchport trunk native vlan\nswitchport trunk allowed vlan none\n";
			$ret .= "exit\n";
			break;
		case 'add allowed':
		case 'rem allowed':
			$clause = $cmd['opcode'] == 'add allowed' ? 'add' : 'remove';
			$ret .= "interface ${cmd['port']}\n";
			foreach (listToRanges ($cmd['vlans']) as $range)
				$ret .= "switchport trunk allowed vlan ${clause} " .
					($range['from'] == $range['to'] ? $range['to'] : "${range['from']}-${range['to']}") .
					"\n";
			$ret .= "exit\n";
			break;
		case 'set native':
			$ret .= "interface ${cmd['arg1']}\nswitchport trunk native vlan ${cmd['arg2']}\nexit\n";
			break;
		case 'unset native':
			$ret .= "interface ${cmd['arg1']}\nswitchport trunk native vlan tag\nexit\n";
			break;
		case 'getlldpstatus':
			$ret .= "show lldp neighbors detail\n";
			break;
		case 'getportstatus':
			$ret .= "show interfaces status\n";
			break;
		case 'getmaclist':
			$ret .= "show mac-address-table dynamic\n";
			break;
		case 'getportmaclist':
			$ret .= "show mac-address-table dynamic interface {$cmd['arg1']}\n";
			break;
		case 'cite':
			$ret .= $cmd['arg1'];
			break;
		case 'getallconf':
		case 'get8021q':
			$ret .= "show running-config\n";
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function ros11TranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'begin configuration':
			$ret .= "configure terminal\n";
			break;
		case 'end configuration':
			$ret .= "end\n";
			break;
		case 'save configuration':
			$ret .= "copy running-config startup-config\nY\n";
			break;
		case 'create VLAN':
			$ret .= "vlan database\nvlan ${cmd['arg1']}\nexit\n";
			break;
		case 'destroy VLAN':
			$ret .= "vlan database\nno vlan ${cmd['arg1']}\nexit\n";
			break;
		case 'set access':
			$ret .= "interface ${cmd['arg1']}\nswitchport access vlan ${cmd['arg2']}\nexit\n";
			break;
		case 'unset access':
			$ret .= "interface ${cmd['arg1']}\nno switchport access vlan\nexit\n";
			break;
		case 'set mode':
			$ret .= "interface ${cmd['arg1']}\n";
			$ret .= "switchport mode ${cmd['arg2']}\n";
			if ($cmd['arg2'] == 'trunk')
				$ret .= "no switchport trunk native vlan\nswitchport trunk allowed vlan remove all\n";
			$ret .= "exit\n";
			break;
		case 'add allowed':
		case 'rem allowed':
			$ret .= "interface ${cmd['port']}\n";
			# default VLAN special case
			$ordinary = array();
			foreach ($cmd['vlans'] as $vid)
				if ($vid == VLAN_DFL_ID)
					$ret .= $cmd['opcode'] == 'add allowed' ?
						"no switchport forbidden default-vlan\nswitchport default-vlan tagged\n" :
						"switchport forbidden default-vlan\nno switchport default-vlan tagged\n";
				else
					$ordinary[] = $vid;
			foreach (listToRanges ($ordinary) as $range)
				$ret .= 'switchport trunk allowed vlan ' .
					($cmd['opcode'] == 'add allowed' ? 'add ' : 'remove ') .
					($range['from'] == $range['to'] ? $range['to'] : "${range['from']}-${range['to']}") .
					"\n";
			$ret .= "exit\n";
			break;
		case 'set native':
			$ret .= "interface ${cmd['arg1']}\n";
			# default VLAN special case
			if ($cmd['arg2'] == VLAN_DFL_ID)
				$ret .= "no switchport default-vlan tagged\n";
			else
				$ret .= "switchport trunk native vlan ${cmd['arg2']}\n";
			$ret .= "exit\n";
			break;
		case 'unset native':
			$ret .= "interface ${cmd['arg1']}\n";
			# default VLAN special case
			if ($cmd['arg2'] == VLAN_DFL_ID)
				$ret .= "switchport default-vlan tagged\n";
			else
				# Although a native VLAN is always one of the allowed VLANs in ROS (as seen in the
				# output of "show interfaces switchport"), the config text doesn't display the
				# native VLAN in the list of allowed VLANs. Respectively, setting the current
				# native VLAN as allowed leaves it allowed, but not native any more.
				$ret .= "switchport trunk allowed vlan add ${cmd['arg2']}\n";
			$ret .= "exit\n";
			break;
		case 'getlldpstatus':
			$ret .= "show lldp neighbors detail\n";
			break;
		case 'getportstatus':
			$ret .= "show interfaces status\n";
			break;
		case 'getmaclist':
			$ret .= "show mac address-table dynamic\n";
			break;
		case 'getportmaclist':
			$ret .= "show mac address-table dynamic interface {$cmd['arg1']}\n";
			break;
		case 'cite':
			$ret .= $cmd['arg1'];
			break;
		case 'getallconf':
		case 'get8021q':
			$ret .= "show running-config\n";
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function dlinkTranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'getportstatus':
			$ret .= "show ports\n";
			break;
		case 'getmaclist':
			$ret .= "show fdb\n";
			break;
		case 'getportmaclist':
			$ret .= "show fdb port {$cmd['arg1']}\n";
			break;
		case 'get8021q':
			$ret .= "show vlan\n";
			break;
		case 'cite':
			$ret .= $cmd['arg1'];
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function linuxTranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'getportstatus':
			$ret .= "cd /sys/class/net && for d in $(ls -1d eth* em* p* 2>/dev/null); do sudo /sbin/ethtool \$d; done\n";
			break;
		case 'getmaclist':
			$ret .= "sudo /usr/sbin/arp -an\n";
			break;
		case 'getportmaclist':
			$ret .= "sudo /usr/sbin/arp -ani {$cmd['arg1']}\n";
			break;
		case 'get8021q':
			$ret .= "sudo /sbin/ip -o a\n";
			break;
		case 'cite':
			$ret .= $cmd['arg1'];
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function xos12Read8021QConfig ($input)
{
	$ret = constructRunning8021QConfig();
	$ret['vlanlist'][] = VLAN_DFL_ID;

	foreach (explode ("\n", $input) as $line)
	{
		$matches = array();
		switch (TRUE)
		{
		case (preg_match ('/^create vlan "([[:alnum:]]+)"$/', $line, $matches)):
			if (!preg_match ('/^VLAN[[:digit:]]+$/', $matches[1]))
				throw new RTGatewayError ('unsupported VLAN name ' . $matches[1]);
			break;
		case (preg_match ('/^configure vlan ([[:alnum:]]+) tag ([[:digit:]]+)$/', $line, $matches)):
			if (strtolower ($matches[1]) == 'default')
				throw new RTGatewayError ('default VLAN tag must be 1');
			if ($matches[1] != 'VLAN' . $matches[2])
				throw new RTGatewayError ("VLAN name ${matches[1]} does not match its tag ${matches[2]}");
			$ret['vlanlist'][] = $matches[2];
			break;
		case (preg_match ('/^configure vlan ([[:alnum:]]+) add ports (.+) (tagged|untagged) */', $line, $matches)):
			$submatch = array();
			if ($matches[1] == 'Default')
				$matches[1] = 'VLAN1';
			if (!preg_match ('/^VLAN([[:digit:]]+)$/', $matches[1], $submatch))
				throw new RTGatewayError ('unsupported VLAN name ' . $matches[1]);
			$vlan_id = $submatch[1];
			foreach (iosParseVLANString ($matches[2]) as $port_name)
			{
				if (!array_key_exists ($port_name, $ret['portdata']))
					$ret['portdata'][$port_name] = array
					(
						'mode' => 'trunk',
						'allowed' => array(),
						'native' => 0,
					);
				$ret['portdata'][$port_name]['allowed'][] = $vlan_id;
				if ($matches[3] == 'untagged')
					$ret['portdata'][$port_name]['native'] = $vlan_id;
			}
			break;
		default:
		}
	}
	return $ret;
}

function jun10Read8021QConfig ($input)
{
	$ret = constructRunning8021QConfig();
	$ret['vlannames'][VLAN_DFL_ID] = 'default';

	$lines = explode ("\n", $input);

	// get vlan list
	$vlans = array('default' => 1);
	$names = array();
	while (count ($lines))
	{
		$line = trim (array_shift ($lines));
		if (FALSE !== strpos ($line, '# END OF VLAN LIST'))
			break;
		if (preg_match ('/^VLAN: (.*), 802.1Q Tag: (\d+)/', $line, $m))
		{
			$ret['vlannames'][$m[2]] = $m[1];
			$vlans[$m[1]] = $m[2];
		}
	}
	$ret['vlanlist'] = array_values	($vlans);

	// get config groups list - throw an exception if a group contains ether-switching config
	$current_group = NULL;
	while (count ($lines))
	{
		$line = array_shift ($lines);
		if (FALSE !== strpos ($line, '# END OF GROUP LIST'))
			break;
		elseif (preg_match ('/^(\S+)(?:\s+{|;)$/', $line, $m))
			$current_group = $m[1];
		elseif (isset ($current_group) && preg_match ('/^\s*family ethernet-switching\b/', $line))
			throw new RTGatewayError ("Config-group '$current_group' contains switchport commands, which is not supported");
	}

	// get interfaces config
	$current = array
	(
		'is_range' => FALSE,
		'is_ethernet' => FALSE,
		'name' => NULL,
		'config' => NULL,
		'indent' => NULL,
	);
	while (count ($lines))
	{
		$line = array_shift ($lines);
		$line_class = 'line-other';
		if (preg_match ('/# END OF CONFIG|^(interface-range )?(\S+)\s+{$/', $line, $m)) // line starts with interface name
		{ // found interface section opening, or end-of-file
			if (isset ($current['name']) && $current['is_ethernet'])
			{
				// add previous interface to the results
				if (! isset ($current['config']['mode']))
					$current['config']['mode'] = 'access';
				if (! isset ($current['config']['native']))
					$current['config']['native'] = $current['config']['native'] = 0;
				if (! isset ($current['config']['allowed']))
				{
					if ($current['config']['mode'] == 'access')
						$current['config']['allowed'] = array (1);
					else
						$current['config']['allowed'] = array();
				}
				if (
					$current['config']['mode'] == 'trunk' &&
					$current['config']['native'] != 0 &&
					! in_array ($current['config']['native'], $current['config']['allowed'])
				)
					$current['config']['allowed'][] = $current['config']['native'];
				elseif ($current['config']['mode'] == 'access')
					$current['config']['native'] = $current['config']['allowed'][0];
				$ret['portdata'][$current['name']] = $current['config'];
			}

			if (! empty ($m[2]))
			{ // new interface section begins
				$current['is_ethernet'] = FALSE;
				$current['is_range'] = ! empty ($m[1]);
				$current['name'] = $m[2];
				$current['config'] = array (
					'mode' => NULL,
					'allowed' => NULL,
					'native' => NULL,
					'config' => array(),
				);
				$line_class = 'line-header';
				$current['indent'] = NULL;
			}
		}
		elseif (preg_match ('/^(\s+)family ethernet-switching\b/', $line, $m))
		{
			if ($current['is_range'])
				throw new RTGatewayError ("interface-range '${current['name']}' contains switchport commands, which is not supported");
			$current['is_ethernet'] = TRUE;
			$current['indent'] = $m[1];
		}
		elseif (isset ($current['indent']) && $line == $current['indent'] . '}')
			$current['indent'] = NULL;
		elseif ($current['is_ethernet'] && isset ($current['indent']))
		{
			$line_class = 'line-8021q';
			if (preg_match ('/^\s+port-mode (trunk|access);/', $line, $m))
				$current['config']['mode'] = $m[1];
			elseif (preg_match ('/^\s+native-vlan-id (\d+);/', $line, $m))
				$current['config']['native'] = $m[1];
			elseif (preg_match ('/^\s+members \[?(.*)\]?;$/', $line, $m))
			{
				$members = array();
				foreach (explode (' ', $m[1]) as $item)
				{
					$item = trim ($item);
					if (preg_match ('/^(\d+)(?:-(\d+))?$/', $item, $m))
					{
						if (isset ($m[2]) && $m[2] > $m[1])
							$members = array_merge (range ($m[1], $m[2]), $members);
						else
							$members[] = $m[1];
					}
					elseif (isset ($vlans[$item]))
						$members[] = $vlans[$item];
					elseif ($item == 'all')
						$members = array_merge (range (VLAN_MIN_ID, VLAN_MAX_ID), $members);
				}
				$current['config']['allowed'] = array_unique ($members);
			}
			else
				$line_class = 'line-other';
		}
		if (isset ($current['name']))
		{
			if ($line == '}')
				$line_class = 'line-header';
			$ret['portconfig'][$current['name']][] = array ('type' => $line_class, 'line' => $line);
		}
	}

	return $ret;
}

function ftos8Read8021QConfig ($input)
{
	$ret = constructRunning8021QConfig();

	$iface = NULL;
	foreach (explode ("\n", $input) as $line)
	{
		if (preg_match ('/^interface (\S.*?)\s*$/', $line, $m))
		{
			$iface = array
			(
				'name' => shortenIfName (str_replace (' ', '', $m[1])),
				'lines' => array(),
				'is_switched' => FALSE,
				'vlan' => 1 === preg_match ('/^Vlan (\d+)$/', $m[1], $m2) ? $m2[1] : 0,
			);
		}
		if (isset ($iface))
		{
			$iface['lines'][] = array ('type' => 'line-other', 'line' => $line);

			if ($line == ' switchport')
			{
				$iface['is_switched'] = TRUE;
				# In "no default-vlan disable" mode (active by default) FTOS monitors
				# switchport/VLAN configuration and once a port is removed from all
				# VLANs, the software assigns it to the default VLAN in access mode.
				# In this case every port is guaranteed to belong to at least one VLAN
				# and assuming "access" mode is a reasonable default, but see below.
				$ret['portdata'][$iface['name']] = array
				(
					'allowed' => array (),
					'native' => 0,
					'mode' => 'access',
				);
			}
			elseif ($line == '!')
			{
				$ret['portconfig'][$iface['name']] = $iface['lines'];
				unset ($iface);
			}
			elseif ($iface['vlan'])
			{
				$ret['vlanlist'][] = $iface['vlan'];
				if (preg_match ('/^[ !](un)?tagged (\S+) (\S+)/', $line, $m))
				{
					list ($untagged, $pref, $list) = array ($m[1], $m[2], $m[3]);
					if (preg_match ('#^(\d+/)#', $list, $m))
					{
						$pref .= $m[1];
						$list = substr ($list, strlen ($m[1]));
					}
					foreach (explode (',', $list) as $range)
					{
						$constraints = explode ('-', $range);
						if (count ($constraints) == 1)
							$constraints[] = $constraints[0];
						if ($constraints[0] <= $constraints[1])
							for ($i = $constraints[0]; $i <= $constraints[1]; $i++)
							{
								$if_name = shortenIfName ($pref . $i);
								$ret['portdata'][$if_name]['allowed'][] = $iface['vlan'];
								if ($untagged)
									$ret['portdata'][$if_name]['native'] = $iface['vlan'];
								else
									$ret['portdata'][$if_name]['mode'] = 'trunk';
							}
					}
				}
			}
		}
	}
	# In "default-vlan disable" mode a port can be removed from all VLANs and
	# still remain a switchport without a bridge group. If that was the case,
	# this extra round makes sure all ports without allowed VLANs are "T" ports,
	# because pure "A" mode is defined illegal in RackTables 802.1Q data model.
	foreach (array_keys ($ret['portdata']) as $if_name)
		if (! count ($ret['portdata'][$if_name]['allowed']))
			$ret['portdata'][$if_name]['mode'] = 'trunk';
	return $ret;
}

function eos4BuildSwitchport ($mined)
{
	switch (TRUE)
	{
	case ! array_key_exists ('mode', $mined):
	case $mined['mode'] == 'access':
		if (! array_key_exists ('access', $mined))
			$mined['access'] = VLAN_DFL_ID;
		return array
		(
			'mode' => 'access',
			'allowed' => array ($mined['access']),
			'native' => $mined['access'],
		);
	case $mined['mode'] == 'trunk':
		if (! array_key_exists ('native', $mined))
			$mined['native'] = ! array_key_exists ('allowed', $mined) || in_array (VLAN_DFL_ID, $mined['allowed']) ? VLAN_DFL_ID : 0;
		if (! array_key_exists ('allowed', $mined))
			$mined['allowed'] = range (VLAN_MIN_ID, VLAN_MAX_ID);
		return array
		(
			'mode' => 'trunk',
			'allowed' => $mined['allowed'],
			'native' => $mined['native'],
		);
	case $mined['mode'] == 'none':
		return array
		(
			'mode' => 'none',
			'allowed' => array(),
			'native' => 0,
		);
	default:
		throw new RackTablesError ('malformed switchport data', RackTablesError::INTERNAL);
	}
}

function eos4Read8021QConfig ($input)
{
	$ret = constructRunning8021QConfig();
	$ret['vlanlist'][] = VLAN_DFL_ID;

	foreach (explode ("\n", $input) as $line)
	{
		$matches = array();
		if (! array_key_exists ('current', $ret))
		{
			switch (TRUE)
			{
			case preg_match ('/^vlan ([\d,-]+)$/', $line, $matches):
				foreach (iosParseVLANString ($matches[1]) as $vlan_id)
					if ($vlan_id != VLAN_DFL_ID)
						$ret['vlanlist'][] = $vlan_id;
				break;
			case preg_match ('/^interface ((Ethernet|Port-Channel)\d+)$/', $line, $matches):
				$portname = shortenIfName ($matches[1]);
				$ret['current'] = array
				(
					'port_name' => $portname,
					'mode' => 'access',
					'default1' => TRUE,
				);
				$ret['portconfig'][$portname][] = array ('type' => 'line-header', 'line' => $line);
				break;
			}
			continue;
		}
		# $portname == $ret['current']['port_name']
		switch (TRUE)
		{
			case $line == '   switchport mode dot1q-tunnel':
				throw new RTGatewayError ('unsupported switchport mode for port ' . $ret['current']['portname']);
			case $line == '   no switchport':
				$ret['current']['mode'] = 'none';
				$ret['portconfig'][$portname][] = array ('type' => 'line-other', 'line' => $line);
				break;
			case $line == '   switchport mode trunk':
				$ret['current']['mode'] = 'trunk';
				$ret['portconfig'][$portname][] = array ('type' => 'line-8021q', 'line' => $line);
				break;
			case $line == '   switchport trunk native vlan tag':
				$ret['current']['default1'] = FALSE;
				$ret['portconfig'][$portname][] = array ('type' => 'line-8021q', 'line' => $line);
				break;
			case preg_match ('/^   switchport trunk native vlan (\d+)$/', $line, $matches):
				$ret['current']['native'] = $matches[1];
				$ret['portconfig'][$portname][] = array ('type' => 'line-8021q', 'line' => $line);
				break;
			case $line == '   switchport trunk allowed vlan none':
				$ret['current']['allowed'] = array();
				$ret['portconfig'][$portname][] = array ('type' => 'line-8021q', 'line' => $line);
				break;
			case preg_match ('/^   switchport trunk allowed vlan (\S+)$/', $line, $matches):
				$ret['current']['allowed'] = iosParseVLANString ($matches[1]);
				$ret['portconfig'][$portname][] = array ('type' => 'line-8021q', 'line' => $line);
				break;
			case preg_match ('/^   switchport trunk allowed vlan add (\S+)$/', $line, $matches):
				$ret['current']['allowed'] = array_merge ($ret['current']['allowed'], iosParseVLANString ($matches[1]));
				$ret['portconfig'][$portname][] = array ('type' => 'line-8021q', 'line' => $line);
				break;
			case preg_match ('/^   switchport access vlan (\d+)$/', $line, $matches):
				$ret['current']['access'] = $matches[1];
				$ret['portconfig'][$portname][] = array ('type' => 'line-8021q', 'line' => $line);
				break;
			case $line == '!': # end of interface section
				if (! array_key_exists ('current', $ret))
					break;
				$ret['portdata'][$ret['current']['port_name']] = eos4BuildSwitchport ($ret['current']);
				unset ($ret['current']);
				$ret['portconfig'][$portname][] = array ('type' => 'line-header', 'line' => $line);
				break;
			default:
				$ret['portconfig'][$portname][] = array ('type' => 'line-other', 'line' => $line);
				break;
		}
	}
	unset ($ret['current']);
	return $ret;
}

# ROS 1.1 config file sytax is derived from that of IOS, but has a few configuration
# traits regarding 802.1Q.
#
# In IOS there is one "interface" section for each port with all 802.1Q configuration
# maintained as text lines in the first place. These lines are eventually translated
# into effective configuration of the port. E.g. access and trunk VLAN settings can
# co-exist in IOS, it is switchport mode (set either statically or dynamically) that
# defines which settings are used by the port. Likewise, it is possible to "assign"
# any VLAN to any port regardless if the VLAN itself exists.
#
# In ROS the configuration is maintained in port's effective switchport state in the
# first place, making trunk and access settings mutually exclusive. A VLAN that does
# not exist cannot be assigned to a port. Finally, typically there are multiple
# "interface" sections in the configuration text referring to the same port. A single
# section would typically configure a range of ports with a single configuration line
# as follows:
# * switchport default-vlan tagged
# * switchport forbidden default-vlan
# * switchport mode trunk
# * switchport trunk allowed vlan add (one "interface" section per each VLAN)
# * switchport trunk native vlan (idem)
# * switchport access vlan (idem)
#
# ROS CLI allows configuring a port in access mode without an access VLAN. Such
# configuration is not supported.
function ros11Read8021QConfig ($input)
{
	$ret = constructRunning8021QConfig();
	$ret['vlanlist'][] = VLAN_DFL_ID;

	$nextfunc = 'ros11-get8021q-scantop';
	global $breedfunc;
	foreach (explode ("\n", $input) as $line)
		$nextfunc = $breedfunc[$nextfunc] ($ret, $line);
	# process any clauses buffered by ros11Read8021QPorts()
	foreach ($ret['portdata'] as $portname => $port)
	{
		if (! array_key_exists ('mode', $port))
			throw new RTGatewayError ("unsupported configuration of port ${portname}");
		if
		(
			! array_key_exists ('switchport forbidden default-vlan', $port)
			&& array_key_exists ('switchport default-vlan tagged', $port)
		)
			$ret['portdata'][$portname]['allowed'][] = VLAN_DFL_ID;
		elseif
		(
		 	! $port['native'] # a configured native VLAN preempts untagged default VLAN
			&& ! array_key_exists ('switchport forbidden default-vlan', $port)
			&& ! array_key_exists ('switchport default-vlan tagged', $port)
		)
		{
			$ret['portdata'][$portname]['allowed'][] = VLAN_DFL_ID;
			$ret['portdata'][$portname]['native'] = VLAN_DFL_ID;
		}
		foreach (array ('switchport forbidden default-vlan', 'switchport default-vlan tagged') as $line)
			if (array_key_exists ($line, $port))
			{
				unset ($ret['portdata'][$portname][$line]);
				$work['portconfig'][$portname][] = array ('type' => 'line-8021q', 'line' => $line);
			}
	}
	return $ret;
}

function iosxr4TranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'cite':
			$ret .= $cmd['arg1'];
			break;
		case 'getallconf':
			$ret .= "show running-config\n";
			break;
		case 'getlldpstatus':
			$ret .= "show lldp neighbors\n";
			break;
		case 'getportstatus':
			$ret .= "show interfaces brief\n";
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function ucsTranslatePushQueue ($dummy_object_id, $queue, $dummy_vlan_names)
{
	$ret = '';
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'cite':
			$ret .= $cmd['arg1'];
			break;
		case 'getinventory':
			$ret .= "getmo\n";
			break;
		default:
			throw new InvalidArgException ('opcode', $cmd['opcode']);
		}
	return $ret;
}

function ros11Read8021QScanTop (&$work, $line)
{
	switch (TRUE)
	{
	case $line == 'vlan database':
		return 'ros11-get8021q-vlandb';
	case 1 == preg_match ('@^interface\s+(range\s+)?([a-z0-9/,-]+)$@', $line, $m):
		$ports = ros11ParsePortString ($m[2]);
		$work['current'] = array ('config' => array(), 'ports' => $ports);
		foreach ($ports as $portname)
			$work['portconfig'][$portname][] = array ('type' => 'line-header', 'line' => $line);
		return 'ros11-get8021q-readports';
	default:
		return 'ros11-get8021q-scantop';
	}
}

function ros11Read8021QVLANDatabase (&$work, $line)
{
	if (1 != preg_match ('/^vlan ([-,0-9]+)$/', $line, $m))
		return 'ros11-get8021q-scantop';
	$work['vlanlist'] = array_merge ($work['vlanlist'],  iosParseVLANString ($m[1]));
	return 'ros11-get8021q-vlandb';
}

function ros11Read8021QPorts (&$work, $line)
{
	switch (TRUE)
	{
	case 1 == preg_match ('/^switchport mode ([a-z]+)$/', $line, $m):
		if ($m[1] != 'trunk' && $m[1] != 'access')
			throw new RTGatewayError ("unsupported switchport mode '${m[1]}'");
		$work['current']['config']['mode'] = $m[1];
		$work['current']['config']['allowed'] = array();
		$work['current']['config']['native'] = 0;
		$work['current']['lines'][] = array ('type' => 'line-8021q', 'line' => $line);
		return 'ros11-get8021q-readports';
	case 1 == preg_match ('/^switchport access vlan (\d+)$/', $line, $m):
		$work['current']['config']['mode'] = 'access';
		$work['current']['config']['allowed'] = array ($m[1]);
		$work['current']['config']['native'] = $m[1];
		$work['current']['lines'][] = array ('type' => 'line-8021q', 'line' => $line);
		return 'ros11-get8021q-readports';
	# ROS accepts multiple allowed VLANs per a "allowed vlan add" line, but generates
	# a single "allowed vlan add" line per VLAN on output.
	case 1 == preg_match ('/^switchport trunk allowed vlan add (\d+)$/', $line, $m):
		$work['current']['config']['allowed'] = array ($m[1]);
		$work['current']['lines'][] = array ('type' => 'line-8021q', 'line' => $line);
		return 'ros11-get8021q-readports';
	case 1 == preg_match ('/^switchport trunk native vlan (\d+)$/', $line, $m):
		$work['current']['config']['allowed']= array ($m[1]); # native wasn't in the allowed list
		$work['current']['config']['native']= $m[1];
		$work['current']['lines'][] = array ('type' => 'line-8021q', 'line' => $line);
		return 'ros11-get8021q-readports';
	# "switchport default-vlan tagged" and "switchport forbidden default-vlan" are buffered
	# to be processed only after the complete configuration of each port is collected.
	case $line == 'switchport default-vlan tagged':
	case $line == 'switchport forbidden default-vlan':
		$work['current']['config'][$line] = TRUE;
		$work['current']['lines'][] = array ('type' => 'line-8021q', 'line' => $line);
		return 'ros11-get8021q-readports';
	case $line == 'exit':
		$work['current']['lines'][] = array ('type' => 'line-header', 'line' => $line);
		# Since an "interface" line may stand both for a single interface and
		# an interface range, the result is always a product of two sets.
		foreach ($work['current']['ports'] as $portname)
		{
			# 802.1Q configuration text uses the short form of interface names, other
			# configuration text may use the long form. Translate to merge the latter.
			$work['portconfig'][shortenIfName ($portname)] = array_merge ($work['portconfig'][$portname], $work['current']['lines']);
			foreach ($work['current']['config'] as $param => $val)
				if ($param != 'allowed') # overwrite
					$work['portdata'][$portname][$param] = $val;
				else # initialize and merge
				{
					if (! array_key_exists ('allowed', $work['portdata'][$portname]))
						$work['portdata'][$portname]['allowed'] = array();
					if (count ($val))
						$work['portdata'][$portname]['allowed'][] = current ($val);
				}
		}
		unset ($work['current']);
		return 'ros11-get8021q-scantop';
	default:
		$work['current']['lines'][] = array ('type' => 'line-other', 'line' => $line);
		return 'ros11-get8021q-readports';
	}
}

function foundryReadInterfaceStatus ($text)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $text) as $line)
	{
		switch ($state)
		{
			case 'headerSearch':
				if (preg_match('/^Port\s+Link\s+State/', $line))
				{
					$link_field_borders = getColumnCoordinates($line, 'Link');
					if (isset ($link_field_borders['from']))
						$state = 'readPort';
				}
				break;
			case 'readPort':
				$field_list = preg_split('/\s+/', $line);
				if (count ($field_list) < 5)
					break;
				list ($portname, $status_raw, $stp_state, $duplex, $speed) = $field_list;
				if ($status_raw == 'Up' || $status_raw == 'up')
					$status = 'up';
				elseif ($status_raw == 'Down' || $status_raw == 'down')
					$status = 'down';
				else
					$status = 'disabled';
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

function ciscoReadInterfaceStatus ($text)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $text) as $line)
	{
		switch ($state)
		{
			case 'headerSearch':
				if (preg_match('/^Port\s+Name\s+Status/', $line))
				{
					$name_field_borders = getColumnCoordinates($line, 'Name');
					if (isset ($name_field_borders['from']))
						$state = 'readPort';
				}
				break;
			case 'readPort':
				$portname = trim (substr ($line, 0, $name_field_borders['from']));
				$portname = preg_replace ('/\s+.*/', '', $portname);
				$portname = shortenIfName ($portname);
				$rest = trim (substr ($line, $name_field_borders['from'] + $name_field_borders['length'] + 1));
				$field_list = preg_split('/\s+/', $rest);
				if (count ($field_list) < 4)
					break;
				list ($status_raw, $vlan, $duplex, $speed) = $field_list;
				if ($status_raw == 'connected' || $status_raw == 'up')
					$status = 'up';
				elseif (0 === strpos ($status_raw, 'notconn') || $status_raw == 'down' || $status_raw == 'sfpAbsent')
					$status = 'down';
				else
					$status = 'disabled';
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

function vrpReadInterfaceStatus ($text)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $text) as $line)
	{
		switch ($state)
		{
			case 'headerSearch':
				if (preg_match('/^Interface\s+Phy\w*\s+Protocol/i', $line))
					$state = 'readPort';
				break;
			case 'readPort':
				if (preg_match('/[\$><\]]/', $line))
					break 2;
				$field_list = preg_split('/\s+/', $line);
				if (count ($field_list) < 7)
					break;
				if ($field_list[0] == '')
					array_shift ($field_list);
				list ($portname, $status_raw) = $field_list;
				$portname = preg_replace ('/([a-zA-Z0-9\/:-]+).*/', '$1', $portname);
				$portname = shortenIfName ($portname);

				if ($status_raw == 'up' || $status_raw == 'down')
					$status = $status_raw;
				else
					$status = 'disabled';
				$result[$portname] = array
				(
					'status' => $status,
				);
				break;
		}
	}
	return $result;
}

/*
 D-Link "show ports" output sample
 =================================

 Port   State/          Settings             Connection           Address
        MDI       Speed/Duplex/FlowCtrl  Speed/Duplex/FlowCtrl    Learning
 -----  --------  ---------------------  ---------------------    --------
 1      Enabled   Auto/Disabled          100M/Full/None           Enabled
        Auto
 ...
 26(C)  Enabled   Auto/Disabled          LinkDown                 Enabled
        Auto
 26(F)  Enabled   Auto/Disabled          LinkDown                 Enabled
*/
function dlinkReadInterfaceStatus ($text)
{
	$result = array();
	foreach (preg_split ("/\n\r?/", $text) as $line)
	{
		if (!preg_match ('/^\s*\d+/', $line))
			continue;
		$w = preg_split ('/\s+/', strtolower($line));
		if (count($w) != 5)
			continue;
		$portname = $w[0];
		if ($w[1] != 'enabled')
			$result[$portname] = array ('status'=>'disabled', 'speed'=>0, 'duplex'=>'');
		elseif ($w[3] == 'linkdown')
			$result[$portname] = array ('status'=>'down', 'speed'=>0, 'duplex'=>'');
		else
		{
			$s = explode ('/', $w[3]);
			$result[$portname] = array ('status'=>'up', 'speed'=>$s[0], 'duplex'=>$s[1]);
		}
	}
	return $result;
}

/*
 Linux "ethtool" output sample
 =============================

Settings for eth0:
        Supported ports: [ TP ]
        Supported link modes:   10baseT/Half 10baseT/Full
                                100baseT/Half 100baseT/Full
                                1000baseT/Full
        Supports auto-negotiation: Yes
        Advertised link modes:  10baseT/Half 10baseT/Full
                                100baseT/Half 100baseT/Full
                                1000baseT/Full
        Advertised pause frame use: No
        Advertised auto-negotiation: Yes
        Speed: 1000Mb/s
        Duplex: Full
        Port: Twisted Pair
        PHYAD: 2
        Transceiver: internal
        Auto-negotiation: on
        MDI-X: off
        Supports Wake-on: pumbg
        Wake-on: g
        Current message level: 0x00000001 (1)
        Link detected: yes

Settings for eth1:
        Supported ports: [ TP ]
        Supported link modes:   10baseT/Half 10baseT/Full
                                100baseT/Half 100baseT/Full
                                1000baseT/Full
        Supports auto-negotiation: Yes
        Advertised link modes:  10baseT/Half 10baseT/Full
                                100baseT/Half 100baseT/Full
                                1000baseT/Full
        Advertised pause frame use: No
        Advertised auto-negotiation: Yes
        Speed: Unknown!
        Duplex: Unknown! (255)
        Port: Twisted Pair
        PHYAD: 1
        Transceiver: internal
        Auto-negotiation: on
        MDI-X: Unknown
        Supports Wake-on: pumbg
        Wake-on: g
        Current message level: 0x00000001 (1)
        Link detected: no
*/
function linuxReadInterfaceStatus ($text)
{
	$result = array();
	$iface = '';
	$status = 'down';
	$speed = '0';
	$duplex = '';
	foreach (explode ("\n", $text) as $line)
	{
		$m = array();
		if (preg_match ('/^[^\s].* (.*):$/', $line, $m))
		{
			if ($iface !== '')
				$result[$iface] = array ('status' => $status, 'speed' => $speed, 'duplex' => $duplex);
			$iface = $m[1];
			$status = 'down';
			$speed = 0;
			$duplex = '';
		}
		elseif (preg_match ('/^\s*Speed: (.*)$/', $line, $m))
			$speed = $m[1];
		elseif (preg_match ('/^\s*Duplex: (.*)$/', $line, $m))
			$duplex = $m[1];
		elseif (preg_match ('/^\s*Link detected: (.*)$/', $line, $m))
			$status = (($m[1] === 'yes') ? 'up' : 'down');
	}
	if ($iface !== '')
		$result[$iface] = array ('status' => $status, 'speed' => $speed, 'duplex' => $duplex);
	return $result;
}

function ftos8ReadInterfaceStatus ($text)
{
	$result = array();
	$table_schema = array();
	foreach (explode ("\n", $text) as $line)
		if (! count ($table_schema))
		{
			if (preg_match('/^Port\s+Description\s+Status\s+Speed\s+Duplex\b/', $line))
				$table_schema = guessTableStructure ($line);
		}
		else
		{
			$fields = explodeTableLine ($line, $table_schema);
			if (! empty ($fields['Port']) && ! empty ($fields['Speed']) && ! empty ($fields['Duplex']))
			{
				$status = strtolower ($fields['Status']);
				if ($status != 'up' && $status != 'down')
					$status = 'disabled';
				$portname = shortenIfName (str_replace (' ', '', $fields['Port']));
				$result[$portname] = array
				(
					'status' => $status,
					'speed' => $fields['Speed'],
					'duplex' => $fields['Duplex'],
				);
			}
		}
	return $result;
}

function eos4ReadInterfaceStatus ($text)
{
	$result = array();
	$table_schema = array();
	foreach (explode ("\n", $text) as $line)
		if (! count ($table_schema))
		{
			if (preg_match('/^Port\s+Name\s+Status\s+Vlan\s+Duplex\s+Speed\b/', $line))
				$table_schema = guessTableStructure ($line);
		}
		else
		{
			$fields = explodeTableLine ($line, $table_schema);
			if (! empty ($fields['Port']) && ! empty ($fields['Speed']) && ! empty ($fields['Duplex']))
			{
				$status = strtolower ($fields['Status']);
				if ($status == 'connected')
					$status = 'up';
				elseif ($status == 'notconnect')
					$status = 'down';
				else
					$status = 'disabled';
				$result[shortenIfName ($fields['Port'])] = array
				(
					'status' => $status,
					'speed' => $fields['Speed'],
					'duplex' => $fields['Duplex'],
				);
			}
		}
	return $result;
}

function ros11ReadInterfaceStatus ($text)
{
	$ret = array();
	$state = 'headerscan';
	foreach (explode ("\n", $text) as $line)
		switch ($state)
		{
		case 'headerscan':
			if (preg_match ('/^Port\s+Type\s+Duplex\s+Speed\s+Neg\s+ctrl\s+State\s+Pressure Mode\b/', $line))
				$state = 'physical';
			elseif (preg_match ('/^Ch\s+Type\s+Duplex\s+Speed\s+Neg\s+control\s+State\b/', $line))
				$state = 'group';
			break;
		case 'physical':
			if (preg_match ('#^([a-z]+\d+/\d+)\s+\S+\s+(\S+)\s+(\S+)\s+\S+\s+\S+\s+(\S+)\s#', $line, $m))
				$ret[$m[1]] = array
				(
					'status' => strtolower ($m[4]),
					'speed' => $m[3],
					'duplex' => $m[2],
				);
			elseif (substr ($line, 0, 9) != '-------- ') # ruler
				$state = 'headerscan'; # end of first table
			break;
		case 'group':
			if (preg_match ('#^(Po\d+)\s+\S+\s+(\S+)\s+(\S+)\s+\S+\s+\S+\s+(\S.+)\s+$#', $line, $m))
			{
				if ($m[4] != 'Not Present')
					$ret[strtolower ($m[1])] = array
					(
						'status' => strtolower (trim ($m[4])),
						'speed' => $m[3],
						'duplex' => $m[2],
					);
			}
			elseif (substr ($line, 0, 9) != '-------- ') # ruler
				break 2; # end of the last table
			break;
		default:
			throw new RackTablesError ('state error', RackTablesError::INTERNAL);
		}
	return $ret;
}

function jun10ReadInterfaceStatus ($input)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $input) as $line)
	{
		$line = trim ($line);
		switch ($state)
		{
			case 'headerSearch':
				if (preg_match('/^Interface\s+Admin\s+Link\s+Proto\s+Local\s+Remote/i', $line))
					$state = 'readPort';
				break;
			case 'readPort':
				if (preg_match('/^{/', $line) || preg_match('/^\S+>/', $line))
					break 2;
				$field_list = preg_split('/\s+/', $line);
				if (count ($field_list) < 3)
					continue;
				$portname = $field_list[0];
				$admin_status = ($field_list[1] == 'up' || $field_list[1] == 'down') ? $field_list[1] : 'disabled';
				$link_status = ($field_list[2] == 'up' || $field_list[2] == 'down') ? $field_list[2] : 'disabled';

				$result[$portname] = array
				(
					'status' => $link_status,
				);
				break;
		}
	}
	return $result;
}

function maclist_sort ($a, $b)
{
	if ($a['vid'] == $b['vid'])
		return 0;
	return ($a['vid'] < $b['vid']) ? -1 : 1;
}

function fdry5ReadMacList ($text)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $text) as $line)
	{
		switch ($state)
		{
			case 'headerSearch':
				if (preg_match('/MAC-Address\s+Port\s+Type\s+Index\s+VLAN/i', $line))
					$state = 'readPort';
				break;
			case 'readPort':
				if (! preg_match ('/([a-f0-9]{4}\.[a-f0-9]{4}\.[a-f0-9]{4})\s+(\S+)\s+\S+\s+\d+\s+(\d+)$/', trim ($line), $matches))
					break;
				$portname = shortenIfName ($matches[2]);
				$result[$portname][] = array
				(
					'mac' => $matches[1],
					'vid' => $matches[3],
				);
				break;
		}
	}
	foreach ($result as $portname => &$maclist)
		usort ($maclist, 'maclist_sort');
	return $result;
}

function ios12ReadMacList ($text)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $text) as $line)
	{
		switch ($state)
		{
			case 'headerSearch':
				if (preg_match('/Vlan\s+Mac Address\s+Type.*Ports?\s*$/i', $line))
					$state = 'readPort';
				break;
			case 'readPort':
				if (! preg_match ('/(\d+)\s+([a-f0-9]{4}\.[a-f0-9]{4}\.[a-f0-9]{4})\s.*?(\S+)$/', trim ($line), $matches))
					break;
				if ($matches[3] == 'Drop') // 802.1X issue - no port name
					break;
				$portname = shortenIfName ($matches[3]);
				$result[$portname][] = array
				(
					'mac' => $matches[2],
					'vid' => $matches[1],
				);
				break;
		}
	}
	foreach ($result as $portname => &$maclist)
		usort ($maclist, 'maclist_sort');
	return $result;
}

function nxos4ReadMacList ($text)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $text) as $line)
	{
		switch ($state)
		{
			case 'headerSearch':
				if (preg_match('/VLAN\s+MAC Address\s+Type\s+age\s+Secure\s+NTFY\s+Ports/i', $line))
					$state = 'readPort';
				break;
			case 'readPort':
				if (! preg_match ('/(\d+)\s+([a-f0-9]{4}\.[a-f0-9]{4}\.[a-f0-9]{4})\s.*?(\S+)$/', trim ($line), $matches))
					break;
				$portname = shortenIfName ($matches[3]);
				$result[$portname][] = array
				(
					'mac' => $matches[2],
					'vid' => $matches[1],
				);
				break;
		}
	}
	foreach ($result as $portname => &$maclist)
		usort ($maclist, 'maclist_sort');
	return $result;
}

function vrp53ReadMacList ($text)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $text) as $line)
	{
		switch ($state)
		{
			case 'headerSearch':
				if (preg_match('/MAC Address\s+VLAN\/VSI\s+Port/i', $line))
					$state = 'readPort';
				break;
			case 'readPort':
				if (! preg_match ('/([a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4})\s+(\d+)\s+(\S+)/', trim ($line), $matches))
					break;
				$portname = shortenIfName ($matches[3]);
				$result[$portname][] = array
				(
					'mac' => str_replace ('-', '.', $matches[1]),
					'vid' => $matches[2],
				);
				break;
		}
	}
	foreach ($result as $portname => &$maclist)
		usort ($maclist, 'maclist_sort');
	return $result;
}

function vrpReadMacList ($text)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $text) as $line)
	{
		switch ($state)
		{
			case 'headerSearch':
				if (preg_match('/^MAC Address\s+VLAN/i', $line))
					$state = 'readPort';
				break;
			case 'readPort':
				if (! preg_match ('/([a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4})\s+(\d+)(?:(?:\s+\S+){2}|\/\S*)\s+(\S+)/', trim ($line), $matches))
					break;
				$portname = shortenIfName ($matches[3]);
				$result[$portname][] = array
				(
					'mac' => str_replace ('-', '.', $matches[1]),
					'vid' => $matches[2],
				);
				break;
		}
	}
	foreach ($result as $portname => &$maclist)
		usort ($maclist, 'maclist_sort');
	return $result;
}

/*
D-Link "show fdb" output sample
===============================

VID  VLAN Name                        MAC Address       Port Type
---- -------------------------------- ----------------- ---- ---------------
1    default                          00-1B-2C-5F-4E-AE 27   Dynamic
99   switch                           00-00-3E-11-B7-52 27   Dynamic
99   switch                           84-C9-B2-36-80-F2 27   Dynamic
*/
function dlinkReadMacList ($text)
{
	$result = array();
	foreach (preg_split ("/\n\r?/", $text) as $line)
	{
		if (! preg_match ('/^\s*\d+\s+/', $line))
			continue;
		$w = preg_split ('/\s+/', $line);
		if (count ($w) != 5)
			continue;
		$result[$w[3]][] = array
		(
			'mac' => $w[2],
			'vid' => $w[0],
		);
	}
	foreach ($result as $portname => &$maclist)
		usort ($maclist, 'maclist_sort');
	return $result;
}

function linuxReadMacList ($text)
{
	$result = array();
	$passed = array();
	foreach (explode ("\n", $text) as $line)
	{
		$m = array();
		if (! preg_match ('/\(([^\s]+)\) at ([^\s]+) \[ether\] on (.*)$/', $line, $m))
			continue;

		// prevent multiple additions
		if (array_key_exists ($m[2].$m[3], $passed))
			continue;
		$passed[$m[2].$m[3]] = 1;

		$result[$m[3]][] = array ('mac' => $m[2], 'vid' => 1);
	}
	foreach ($result as $portname => &$maclist)
		usort ($maclist, 'maclist_sort');
	return $result;
}

function ftos8ReadMacList ($text)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $text) as $line)
		switch ($state)
		{
			case 'headerSearch':
				if (preg_match('/^VlanId\s+Mac Address\s+Type\s+Interface\s+State/', $line))
					$state = 'readPort';
				break;
			case 'readPort':
				if (! preg_match ('/^(\d+)\s+((?:[a-f0-9]{2}:){5}[a-f0-9]{2})\s+Dynamic\s+(\S+ (?:\S+)?)/', trim ($line), $matches))
					break;
				$portname = shortenIfName (str_replace (' ', '', $matches[3]));
				$mac = preg_replace ('/([a-f0-9]{2}):([a-f0-9]{2})/', '$1$2', $matches[2]);
				$result[$portname][] = array
				(
					'mac' => str_replace (':', '.', $mac),
					'vid' => $matches[1],
				);
				break;
		}
	foreach ($result as $portname => &$maclist)
		usort ($maclist, 'maclist_sort');
	return $result;
}

function eos4ReadMacList ($text)
{
	$result = array();
	$seen_header = FALSE;
	foreach (explode ("\n", $text) as $line)
		if (! $seen_header)
			$seen_header = $line == '----    -----------       ----        -----      -----   ---------';
		else
		{
			if (substr ($line, 0, 19) == 'Total Mac Addresses') # end of table
				break;
			if (preg_match ('/^ *(\d+)\s+(\S+)\s+DYNAMIC\s+(\S+)\s/', $line, $m))
				$result[shortenIfName ($m[3])][] = array
				(
					'mac' => $m[2],
					'vid' => $m[1],
				);
		}
	foreach (array_keys ($result) as $portname)
		usort ($result[$portname], 'maclist_sort');
	return $result;
}

function ros11ReadMacList ($text)
{
	$result = array();
	$got_header = FALSE;
	foreach (explode ("\n", $text) as $line)
		if (! $got_header)
			$got_header = (1 == preg_match('/Vlan\s+Mac Address\s+Port\s+Type\b/', $line));
		elseif (preg_match ('/\b(\d+)\s+([a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2})\s+(\S+)\b/', $line, $m))
			$result[shortenIfName ($m[3])][] = array
			(
				'mac' => $m[2],
				'vid' => $m[1],
			);
		elseif (! preg_match ('/^--------/', $line)) # ruler
			break; # end of table
	foreach (array_keys ($result) as $portname)
		usort ($result[$portname], 'maclist_sort');
	return $result;
}

# The types of objects returned are 'NetworkElement', 'EquipmentChassis' and
# 'ComputeBlade', just like their respective UCS classes.
function ucsReadInventory ($text)
{
	# transform plain-text response into array
	$tmp = $replies = array();
	foreach (explode ("\n", $text) as $line)
		if (1 !== preg_match ('/^(OK|ERR)( .+)?$/', $line, $m))
			$tmp[] = $line;
		else
		{
			$replies[] = array ('code' => $m[1], 'text' => $m[2], 'body' => $tmp);
			$tmp = array();
		}
	# validate the array
	if (count ($replies) != 2 && count ($replies) != 1)
		throw new RTGatewayError ('replies count does not match commands count');
	if ($replies[0]['code'] != 'OK')
		throw new RTGatewayError ('UCS login failed');
	if ($replies[1]['code'] != 'OK')
		throw new RTGatewayError ('UCS enumeration failed');
	$ret = array();
	foreach ($replies[1]['body'] as $line)
		switch (1)
		{
		case preg_match ('/^COLUMNS (.+)$/', $line, $m):
			if (! count ($hcols = explode (',', $m[1])))
				throw new RTGatewayError ("UCS format error: '${line}'");
			break;
		case preg_match ('/^ROW (.+)$/', $line, $m):
			if (count ($cols = explode (',', $m[1])) != count ($hcols))
				throw new RTGatewayError ("UCS format error: '${line}'");
			# $hcols and $cols have same array keys
			$tmp = array();
			foreach ($cols as $key => $value)
				$tmp[$hcols[$key]] = $value;
			$ret[] = $tmp;
			break;
		default:
			throw new RTGatewayError ("Unrecognized line: '${line}'");
		}
	return $ret;
}

function ios12SpotConfigText ($input)
{
	if (preg_match ('/\nUnable to get configuration. Try again later/s', $input))
		throw new ERetryNeeded ("device is busy. 'show run' did not work");
	return preg_replace ('/.*?^Current configuration : \d+ bytes$\n(.*)^\S+#\s*\Z/sm', '$1', $input, 1);
}

function nxos4SpotConfigText ($input)
{
	return preg_replace ('/.*?^!Command: show running-config$\n(.*)^\S+#\s*\Z/sm', '$1', $input, 1);
}

function fdry5SpotConfigText ($input)
{
	return $input;
}

function vrpSpotConfigText ($input)
{
	return preg_replace ('/.*?(?:^!(?:Software Version V|Last configuration was)\N*\n)+(.*)^return$.*/sm', '$1', $input, 1);
}

function xos12SpotConfigText ($input)
{
	return preg_replace ('/.*?^(#\n^# Module \N+ configuration.$\n.*)^\S+\.\d+ # /sm', '$1', $input, 1);
}

function jun10SpotConfigText ($input)
{
	return preg_replace ('/.*?^## Last commit: \N*\n(.*)^\S+@\S+>\s*\Z/sm', '$1', $input, 1);
}

function ftos8SpotConfigText ($input)
{
	return preg_replace ('/.*?^! Version [0-9\.]+\n(.*)^end$.*/sm', '$1', $input, 1);
}

function eos4SpotConfigText ($input)
{
	return preg_replace ('/.*?^! device: \N*EOS-\N*$\n(.*)^end$.*/sm', '$1', $input, 1);
}

function ros11SpotConfigText ($input)
{
	return $input;
}

function iosxr4SpotConfigText ($input)
{
	return preg_replace ('/.*?^!! IOS XR Configuration [^\n]*$\n(.*)^\S+#\s*\Z/sm', '$1', $input, 1);
}

function jun10ReadLLDPStatus ($input)
{
	$ret = array();

	$lldp_mode = FALSE;
	foreach (explode ("\n", $input) as $line)
	{
		$line = rtrim ($line);
		if (preg_match ('/^Local Interface.*\s+Chassis Id\s+Port info\s+System Name$/', $line))
			$lldp_mode = TRUE;
		elseif ($line == "")
			$lldp_mode = FALSE;
		elseif ($lldp_mode && preg_match ('/^(\S+).*\s+([0-9a-f:]{17})\s+(.*?)\s+(\S+)\s*$/', $line, $m))
			$ret[shortenIfName ($m[1])][] = array
			(
				'port' => $m[3],
				'device' => $m[4],
			);
	}

	return $ret;
}

function iosxr4ReadLLDPStatus ($input)
{
	$ret = array();

	$lldp_mode = FALSE;
	foreach (explode ("\n", $input) as $line)
	{
		$line = rtrim ($line);
		if (preg_match ('/^Device ID\s+Local Intf\s+Hold-time\s+Capability\s+Port ID$/', $line))
			$lldp_mode = TRUE;
		elseif ($line == "")
			$lldp_mode = FALSE;
		elseif ($lldp_mode && preg_match ('/^(\S+)\s+([^\s\[\]]+)[^\s]*\s+\d+\s+\S+\s+(.*)$/', $line, $m))
		{
			$local_port = shortenIfName ($m[2]);
			$remote_port = $m[3];
			if (!preg_match ('@^bundle-ether\d+$@', $remote_port) || preg_match ('@^bundle-ether\d+$@', $local_port))
				$ret[$local_port][] = array
				(
					'port' => $remote_port,
					'device' => $m[1],
				);
		}
	}

	return $ret;
}

function iosxr4ReadInterfaceStatus ($input)
{
	$result = array();
	$state = 'headerSearch';
	foreach (explode ("\n", $input) as $line)
	{
		switch ($state)
		{
			case 'headerSearch' && preg_match("/^\s*-{10+}\s*$/", $line):
				$state = 'readPort';
				break;
			case 'readPort' && preg_match ("/^\s+([A-Za-z0-9\/]+)\s+([a-z-]+)\s+([a-z-]+)/", $line, $m):
				$portname = shortenIfName ($m[1]);
				$line_state = $m[3];
				if ($line_state == 'up')
					$status = 'up';
				elseif ($line_state == 'down')
					$status = 'down';
				else
					$status = 'disabled';
				$result[$portname] = array
				(
					'status' => $status
				);
				break;
		}
	}
	return $result;
}
