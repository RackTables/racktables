<?php
/*
*
*  This file contains gateway functions for RackTables.
*  A gateway is an external executable, which provides
*  read-only or read-write access to some external entities.
*  Each gateway accepts its own list of command-line args
*  and then reads its stdin for requests. Each request consists
*  of one line and results in exactly one line of reply.
*  The replies must have the following syntax:
*  OK<space>any text up to the end of the line
*  ERR<space>any text up to the end of the line
*
*/

// translating functions maps
$gwrxlator = array();
$gwrxlator['getcdpstatus']['ios12'] = 'ios12ReadCDPStatus';
$gwrxlator['getlldpstatus'] = array
(
	'xos12' => 'xos12ReadLLDPStatus',
	'vrp53' => 'vrp53ReadLLDPStatus',
);
$gwrxlator['get8021q'] = array
(
	'ios12' => 'ios12ReadVLANConfig',
	'fdry5' => 'fdry5ReadVLANConfig',
	'vrp53' => 'vrp53ReadVLANConfig',
	'nxos4' => 'nxos4Read8021QConfig',
	'xos12' => 'xos12Read8021QConfig',
);

$gwpushxlator = array
(
	'ios12' => 'ios12TranslatePushQueue',
	'fdry5' => 'fdry5TranslatePushQueue',
	'vrp53' => 'vrp53TranslatePushQueue',
	'nxos4' => 'ios12TranslatePushQueue', // employ syntax compatibility
	'xos12' => 'xos12TranslatePushQueue',
);

// This function launches specified gateway with specified
// command-line arguments and feeds it with the commands stored
// in the second arg as array.
// The answers are stored in another array, which is returned
// by this function. In the case when a gateway cannot be found,
// finishes prematurely or exits with non-zero return code,
// a single-item array is returned with the only "ERR" record,
// which explains the reason.
function queryGateway ($gwname, $questions)
{
	$execpath = "./gateways/{$gwname}/main";
	$dspec = array
	(
		0 => array ("pipe", "r"),
		1 => array ("pipe", "w"),
		2 => array ("file", "/dev/null", "a")
	);
	$pipes = array();
	$gateway = proc_open ($execpath, $dspec, $pipes);
	if (!is_resource ($gateway))
		return array ('ERR proc_open() failed in ' . __FUNCTION__);

// Dialogue starts. Send all questions.
	foreach ($questions as $q)
		fwrite ($pipes[0], "$q\n");
	fclose ($pipes[0]);

// Fetch replies.
	$answers = array ();
	while (!feof($pipes[1]))
	{
		$a = fgets ($pipes[1]);
		if (!strlen ($a))
			continue;
		// Somehow I got a space appended at the end. Kick it.
		$answers[] = trim ($a);
	}
	fclose($pipes[1]);

	$retval = proc_close ($gateway);
	if ($retval != 0)
		throw new Exception ("gateway failed with code ${retval}", E_GW_FAILURE);
	if (!count ($answers))
		throw new Exception ('no response from gateway', E_GW_FAILURE);
	if (count ($answers) != count ($questions))
		throw new Exception ('protocol violation', E_GW_FAILURE);
	foreach ($answers as $a)
		if (strpos ($a, 'OK!') !== 0)
			throw new Exception ("subcommand failed with status: ${a}", E_GW_FAILURE);
	return $answers;
}

// This functions returns an array for VLAN list, and an array for port list (both
// form another array themselves) and another one with MAC address list.
// The ports in the latter array are marked with either VLAN ID or 'trunk'.
// We don't sort the port list, as the gateway is believed to have done this already
// (or at least the underlying switch software ought to). This is important, as the
// port info is transferred to/from form not by names, but by numbers.
function getSwitchVLANs ($object_id = 0)
{
	global $remote_username;
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		throw new Exception ('no management address set', E_GW_FAILURE);
	if (count ($endpoints) > 1)
		throw new Exception ('cannot pick management address', E_GW_FAILURE);
	$hwtype = $swtype = 'unknown';
	foreach (getAttrValues ($object_id) as $record)
	{
		if ($record['name'] == 'SW type' && strlen ($record['o_value']))
			$swtype = str_replace (' ', '+', execGMarker ($record['o_value']));
		if ($record['name'] == 'HW type' && strlen ($record['o_value']))
			$hwtype = str_replace (' ', '+', execGMarker ($record['o_value']));
	}
	$endpoint = str_replace (' ', '+', $endpoints[0]);
	$commands = array
	(
		"connect ${endpoint} ${hwtype} ${swtype} ${remote_username}",
		'listvlans',
		'listports',
		'listmacs'
	);
	$data = queryGateway ('switchvlans', $commands);
	if (strpos ($data[0], 'OK!') !== 0)
		throw new Exception ("gateway failed with status: ${data[0]}.", E_GW_FAILURE);
	// Now we have VLAN list in $data[1] and port list in $data[2]. Let's sort this out.
	$tmp = array_unique (explode (';', substr ($data[1], strlen ('OK!'))));
	if (count ($tmp) == 0)
		throw new Exception ('gateway returned no records', E_GW_FAILURE);
	$vlanlist = array();
	foreach ($tmp as $record)
	{
		list ($vlanid, $vlandescr) = explode ('=', $record);
		$vlanlist[$vlanid] = $vlandescr;
	}
	$portlist = array();
	foreach (explode (';', substr ($data[2], strlen ('OK!'))) as $pair)
	{
		list ($portname, $pair2) = explode ('=', $pair);
		list ($status, $vlanid) = explode (',', $pair2);
		$portlist[] = array ('portname' => $portname, 'status' => $status, 'vlanid' => $vlanid);
	}
	if (count ($portlist) == 0)
		throw new Exception ('gateway returned no records', E_GW_FAILURE);
	$maclist = array();
	foreach (explode (';', substr ($data[3], strlen ('OK!'))) as $pair)
	{
		list ($macaddr, $pair2) = explode ('=', $pair);
		if (!strlen ($pair2))
			continue;
		list ($vlanid, $ifname) = explode ('@', $pair2);
		$maclist[$ifname][$vlanid][] = $macaddr;
	}
	return array ($vlanlist, $portlist, $maclist);
}

function setSwitchVLANs ($object_id = 0, $setcmd)
{
	global $remote_username;
	if ($object_id <= 0)
		return oneLiner (160); // invalid arguments
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		return oneLiner (161); // endpoint not found
	if (count ($endpoints) > 1)
		return oneLiner (162); // can't pick an address
	$hwtype = $swtype = 'unknown';
	foreach (getAttrValues ($object_id) as $record)
	{
		if ($record['name'] == 'SW type' && strlen ($record['o_value']))
			$swtype = strtr (execGMarker ($record['o_value']), ' ', '+');
		if ($record['name'] == 'HW type' && strlen ($record['o_value']))
			$hwtype = strtr (execGMarker ($record['o_value']), ' ', '+');
	}
	$endpoint = str_replace (' ', '+', $endpoints[0]);
	$data = queryGateway
	(
		'switchvlans',
		array ("connect ${endpoint} ${hwtype} ${swtype} ${remote_username}", $setcmd)
	);
	// Finally we can parse the response into message array.
	$log_m = array();
	foreach (explode (';', substr ($data[1], strlen ('OK!'))) as $text)
	{
		if (strpos ($text, 'C!') === 0)
		{
			$tmp = explode ('!', $text);
			array_shift ($tmp);
			$code = array_shift ($tmp);
			$log_m[] = count ($tmp) ? array ('c' => $code, 'a' => $tmp) : array ('c' => $code); // gateway-encoded message
		}
		elseif (strpos ($text, 'I!') === 0)
			$log_m[] = array ('c' => 62, 'a' => array (substr ($text, 2))); // generic gateway success
		elseif (strpos ($text, 'W!') === 0)
			$log_m[] = array ('c' => 202, 'a' => array (substr ($text, 2))); // generic gateway warning
		else // All improperly formatted messages must be treated as error conditions.
			$log_m[] = array ('c' => 166, 'a' => array (substr ($text, 2))); // generic gateway error
	}
	return $log_m;
}

// Drop a file off RackTables platform. The gateway will catch the file and pass it to the given
// installer script.
function gwSendFile ($endpoint, $handlername, $filetext = array())
{
	global $remote_username;
	$tmpnames = array();
	$endpoint = str_replace (' ', '\ ', $endpoint); // the gateway dispatcher uses read (1) to assign arguments
	$command = "submit ${remote_username} ${endpoint} ${handlername}";
	foreach ($filetext as $text)
	{
		$name = tempnam ('', 'RackTables-sendfile-');
		$tmpnames[] = $name;
		if (FALSE === $name or FALSE === file_put_contents ($name, $text))
		{
			foreach ($tmpnames as $name)
				unlink ($name);
			throw new Exception ('failed to write to temporary file', E_GW_FAILURE);
		}
		$command .= " ${name}";
	}
	$outputlines = queryGateway
	(
		'sendfile',
		array ($command)
	);
	foreach ($tmpnames as $name)
		unlink ($name);
}

// Query something through a gateway and get some text in return. Return that text.
function gwRecvFile ($endpoint, $handlername, &$output)
{
	global $remote_username;
	$tmpfilename = tempnam ('', 'RackTables-sendfile-');
	$endpoint = str_replace (' ', '\ ', $endpoint); // the gateway dispatcher uses read (1) to assign arguments
	$outputlines = queryGateway
	(
		'sendfile',
		array ("submit ${remote_username} ${endpoint} ${handlername} ${tmpfilename}")
	);
	$output = file_get_contents ($tmpfilename);
	unlink ($tmpfilename);
	// Being here means having 'OK!' in the response.
	return oneLiner (66, array ($handlername)); // ignore provided "Ok" text
}

function gwSendFileToObject ($object_id = 0, $handlername, $filetext = '')
{
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		throw new Exception ('no management address set', E_GW_FAILURE);
	if (count ($endpoints) > 1)
		throw new Exception ('cannot pick management address', E_GW_FAILURE);
	gwSendFile (str_replace (' ', '+', $endpoints[0]), $handlername, array ($filetext));
}

function gwRecvFileFromObject ($object_id = 0, $handlername, &$output)
{
	global $remote_username;
	if ($object_id <= 0 or !strlen ($handlername))
		return oneLiner (160); // invalid arguments
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		return oneLiner (161); // endpoint not found
	if (count ($endpoints) > 1)
		return oneLiner (162); // can't pick an address
	$endpoint = str_replace (' ', '+', $endpoints[0]);
	return gwRecvFile ($endpoint, $handlername, $output);
}

function detectDeviceBreed ($object_id)
{
	foreach (getAttrValues ($object_id) as $record)
	{
		if
		(
			$record['name'] == 'SW type' &&
			strlen ($record['o_value']) &&
			preg_match ('/^Cisco IOS 12\./', execGMarker ($record['o_value']))
		)
			return 'ios12';
		if
		(
			$record['name'] == 'SW type' &&
			strlen ($record['o_value']) &&
			preg_match ('/^Cisco NX-OS 4\./', execGMarker ($record['o_value']))
		)
			return 'nxos4';
		if
		(
			$record['id'] == 4 &&
			$record['key'] == 1352
		)
			return 'xos12';
		if
		(
			$record['name'] == 'HW type' &&
			strlen ($record['o_value']) &&
			preg_match ('/^Foundry FastIron GS /', execGMarker ($record['o_value']))
		)
			return 'fdry5';
		if
		(
			$record['name'] == 'HW type' &&
			strlen ($record['o_value']) &&
			preg_match ('/^Huawei Quidway S53/', execGMarker ($record['o_value']))
		)
			return 'vrp53';
	}
	return '';
}

function getRunning8021QConfig ($object_id)
{
	$ret = gwRetrieveDeviceConfig ($object_id, 'get8021q');
	// Once there is no default VLAN in the parsed data, it means
	// something else was parsed instead of config text.
	if (!in_array (VLAN_DFL_ID, $ret['vlanlist']))
		throw new Exception ('communication with device failed', E_GW_FAILURE);
	return $ret;
}

function setDevice8021QConfig ($object_id, $pseudocode)
{
	if ('' == $breed = detectDeviceBreed ($object_id))
		throw new Exception ('device breed unknown', E_GW_FAILURE);
	global $gwpushxlator;
	$do_save = considerConfiguredConstraint (spotEntity ('object', $object_id), '8021Q_WRI_AFTER_CONFT_LISTSRC');
	// FIXME: is it possible to do $pseudocode[] = array ('opcode' => 'save config') instead?
	gwDeployDeviceConfig ($object_id, $breed, unix2dos ($gwpushxlator[$breed] ($pseudocode, $do_save)));
}

function gwRetrieveDeviceConfig ($object_id, $command)
{
	global $gwrxlator;
	if (!array_key_exists ($command, $gwrxlator))
		throw new Exception ('command unknown', E_GW_FAILURE);
	$breed = detectDeviceBreed ($object_id);
	if (!array_key_exists ($breed, $gwrxlator[$command]))
		throw new Exception ('device breed unknown', E_GW_FAILURE);
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		throw new Exception ('no management address set', E_GW_FAILURE);
	if (count ($endpoints) > 1)
		throw new Exception ('cannot pick management address', E_GW_FAILURE);
	$endpoint = str_replace (' ', '\ ', str_replace (' ', '+', $endpoints[0]));
	$tmpfilename = tempnam ('', 'RackTables-deviceconfig-');
	$outputlines = queryGateway
	(
		'deviceconfig',
		array ("${command} ${endpoint} ${breed} ${tmpfilename}")
	);
	$configtext = dos2unix (file_get_contents ($tmpfilename));
	unlink ($tmpfilename);
	// Being here means it was alright.
	return $gwrxlator[$command][$breed] ($configtext);
}

function gwDeployDeviceConfig ($object_id, $breed, $text)
{
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		throw new Exception ('no management address set', E_GW_FAILURE);
	if (count ($endpoints) > 1)
		throw new Exception ('cannot pick management address', E_GW_FAILURE);
	$endpoint = str_replace (' ', '\ ', str_replace (' ', '+', $endpoints[0]));
	$tmpfilename = tempnam ('', 'RackTables-deviceconfig-');
	if (FALSE === file_put_contents ($tmpfilename, $text))
	{
		unlink ($tmpfilename);
		throw new Exception ('failed to write to temporary file', E_GW_FAILURE);
	}
	$outputlines = queryGateway
	(
		'deviceconfig',
		array ("deploy ${endpoint} ${breed} ${tmpfilename}")
	);
	unlink ($tmpfilename);
}

// Read provided output of "show cdp neighbors detail" command and
// return a list of records with (translated) local port name,
// remote device name and (translated) remote port name.
function ios12ReadCDPStatus ($input)
{
	$ret = array();
	$procfunc = 'ios12ScanCDPTopLevel';
	foreach (explode ("\n", $input) as $line)
		$procfunc = $procfunc ($ret, $line);
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
		case preg_match ('/^LLDP Port ([[:digit:]]+) detected 1 neighbor$/', $line, $matches):
			$ret['current']['local_port'] = ios12ShortenIfName ($matches[1]);
			break;
		case preg_match ('/^      Port ID     : "(.+)"$/', $line, $matches):
			$ret['current']['remote_port'] = ios12ShortenIfName ($matches[1]);
			break;
		case preg_match ('/^    - System Name: "(.+)"$/', $line, $matches):
			if
			(
				array_key_exists ('current', $ret) and
				array_key_exists ('local_port', $ret['current']) and
				array_key_exists ('remote_port', $ret['current'])
			)
				$ret[$ret['current']['local_port']] = array
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

function vrp53ReadLLDPStatus ($input)
{
	$ret = array();
	foreach (explode ("\n", $input) as $line)
	{
		$matches = array();
		switch (TRUE)
		{
		case preg_match ('/^(.+) has 1 neighbors:$/', $line, $matches):
			$ret['current']['local_port'] = ios12ShortenIfName ($matches[1]);
			break;
		case preg_match ('/^(PortIdSubtype|PortId): ([^ ]+)/', $line, $matches):
			$ret['current'][$matches[1]] = $matches[2];
			break;
		case preg_match ('/^SysName: (.+)$/', $line, $matches):
			if
			(
				array_key_exists ('current', $ret) and
				array_key_exists ('PortIdSubtype', $ret['current']) and
				($ret['current']['PortIdSubtype'] == 'interfaceAlias' or $ret['current']['PortIdSubtype'] == 'interfaceName') and
				array_key_exists ('PortId', $ret['current']) and
				array_key_exists ('local_port', $ret['current'])
			)
				$ret[$ret['current']['local_port']] = array
				(
					'device' => $matches[1],
					'port' => ios12ShortenIfName ($ret['current']['PortId']),
				);
			unset ($ret['current']);
			break;
		default:
		}
	}
	unset ($ret['current']);
	return $ret;
}

function ios12ReadVLANConfig ($input)
{
	$ret = array
	(
		'vlanlist' => array(),
		'portdata' => array(),
	);
	$procfunc = 'ios12ScanTopLevel';
	foreach (explode ("\n", $input) as $line)
		$procfunc = $procfunc ($ret, $line);
	return $ret;
}

function ios12ScanTopLevel (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^interface ((Ethernet|FastEthernet|GigabitEthernet|TenGigabitEthernet|Port-channel)[[:digit:]]+(/[[:digit:]]+)*)$@', $line, $matches)):
		$work['current'] = array ('port_name' => ios12ShortenIfName ($matches[1]));
		return 'ios12PickSwitchportCommand'; // switch to interface block reading
	case (preg_match ('/^VLAN Name                             Status    Ports$/', $line, $matches)):
		return 'ios12PickVLANCommand';
	default:
		return __FUNCTION__; // continue scan
	}
}

function ios12PickSwitchportCommand (&$work, $line)
{
	if ($line[0] != ' ') // end of interface section
	{
		// save work, if it makes sense
		switch (TRUE)
		{
		case $work['current']['ignore']:
			$work['portdata'][$work['current']['port_name']] = array
			(
				'mode' => 'none',
				'allowed' => array(),
				'native' => 0,
			);
			break;
		case 'access' == $work['current']['mode']:
			if (!array_key_exists ('access vlan', $work['current']))
				$work['current']['access vlan'] = 1;
			$work['portdata'][$work['current']['port_name']] = array
			(
				'mode' => 'access',
				'allowed' => array ($work['current']['access vlan']),
				'native' => $work['current']['access vlan'],
			);
			break;
		case 'trunk' == $work['current']['mode']:
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
			$work['portdata'][$work['current']['port_name']] = array
			(
				'mode' => 'trunk',
				'allowed' => $work['current']['trunk allowed vlan'],
				'native' => $effective_native,
			);
			break;
		default:
			// dot1q-tunnel, dynamic, private-vlan or even none --
			// show in returned config and let user decide, if they
			// want to fix device config or work around these ports
			// by means of VST.
			$work['portdata'][$work['current']['port_name']] = array
			(
				'mode' => 'none',
				'allowed' => array(),
				'native' => 0,
			);
			break;
		}
		unset ($work['current']);
		return 'ios12ScanTopLevel';
	}
	// not yet
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^ switchport mode (.+)$@', $line, $matches)):
		$work['current']['mode'] = $matches[1];
		break;
	case (preg_match ('@^ switchport access vlan (.+)$@', $line, $matches)):
		$work['current']['access vlan'] = $matches[1];
		break;
	case (preg_match ('@^ switchport trunk native vlan (.+)$@', $line, $matches)):
		$work['current']['trunk native vlan'] = $matches[1];
		break;
	case (preg_match ('@^ switchport trunk allowed vlan add (.+)$@', $line, $matches)):
		$work['current']['trunk allowed vlan'] = array_merge
		(
			$work['current']['trunk allowed vlan'],
			iosParseVLANString ($matches[1])
		);
		break;
	case (preg_match ('@^ switchport trunk allowed vlan (.+)$@', $line, $matches)):
		$work['current']['trunk allowed vlan'] = iosParseVLANString ($matches[1]);
		break;
	case preg_match ('@^ channel-group @', $line):
	// port-channel subinterface config follows that of the master interface
	case preg_match ('@^ ip address @', $line):
	// L3 interface does no switchport functions
		$work['current']['ignore'] = TRUE;
		break;
	default: // suppress warning on irrelevant config clause
	}
	return __FUNCTION__;
}

function ios12PickVLANCommand (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case ($line == '---- -------------------------------- --------- -------------------------------'):
		// ignore the rest of VLAN table header;
		break;
	case (preg_match ('@! END OF VLAN LIST$@', $line)):
		return 'ios12ScanTopLevel';
	case (preg_match ('@^([[:digit:]]+) {1,4}.{32} active    @', $line, $matches)):
		if (!array_key_exists ($matches[1], $work['vlanlist']))
			$work['vlanlist'][] = $matches[1];
		break;
	default:
	}
	return __FUNCTION__;
}

// Another finite automata to read a dialect of Foundry configuration.
function fdry5ReadVLANConfig ($input)
{
	$ret = array
	(
		'vlanlist' => array(),
		'portdata' => array(),
	);
	$procfunc = 'fdry5ScanTopLevel';
	foreach (explode ("\n", $input) as $line)
		$procfunc = $procfunc ($ret, $line);
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
		return 'fdry5PickVLANSubcommand';
	case (preg_match ('@^interface ethernet ([[:digit:]]+/[[:digit:]]+/[[:digit:]]+)$@', $line, $matches)):
		$work['current'] = array ('port_name' => 'e' . $matches[1]);
		return 'fdry5PickInterfaceSubcommand';
	default:
		return __FUNCTION__;
	}
}

function fdry5PickVLANSubcommand (&$work, $line)
{
	if ($line[0] != ' ') // end of VLAN section
	{
		unset ($work['current']);
		return 'fdry5ScanTopLevel';
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
	return __FUNCTION__;
}

function fdry5PickInterfaceSubcommand (&$work, $line)
{
	if ($line[0] != ' ') // end of interface section
	{
		if (array_key_exists ('dual-mode', $work['current']))
		{
			if (array_key_exists ($work['current']['port_name'], $work['portdata']))
				// update existing record
				$work['portdata'][$work['current']['port_name']]['native'] = $work['current']['dual-mode'];
			else
				// add new
				$work['portdata'][$work['current']['port_name']] = array
				(
					'allowed' => array ($work['current']['dual-mode']),
					'native' => $work['current']['dual-mode'],
				);
			// a dual-mode port is always considered a trunk port
			// (but not in the IronWare's meaning of "trunk") regardless of
			// number of assigned tagged VLANs
			$work['portdata'][$work['current']['port_name']]['mode'] = 'trunk';
		}
		unset ($work['current']);
		return 'fdry5ScanTopLevel';
	}
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^ dual-mode( +[[:digit:]]+ *)?$@', $line, $matches)):
		// default VLAN ID for dual-mode command is 1
		$work['current']['dual-mode'] = strlen (trim ($matches[1])) ? trim ($matches[1]) : 1;
		break;
	// FIXME: trunk/link-aggregate/ip address pulls port from 802.1Q field
	default: // nom-nom
	}
	return __FUNCTION__;
}

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
			return array();
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

// an implementation for Huawei syntax
function vrp53ReadVLANConfig ($input)
{
	$ret = array
	(
		'vlanlist' => array(),
		'portdata' => array(),
	);
	$procfunc = 'vrp53ScanTopLevel';
	foreach (explode ("\n", $input) as $line)
		$procfunc = $procfunc ($ret, $line);
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
		return __FUNCTION__;
	case (preg_match ('@^interface ((GigabitEthernet|XGigabitEthernet|Eth-Trunk)([[:digit:]]+(/[[:digit:]]+)*))$@', $line, $matches)):
		$matches[1] = preg_replace ('@^GigabitEthernet(.+)$@', 'gi\\1', $matches[1]);
		$matches[1] = preg_replace ('@^XGigabitEthernet(.+)$@', 'xg\\1', $matches[1]);
		$matches[1] = preg_replace ('@^Eth-Trunk(.+)$@', 'et\\1', $matches[1]);
		$work['current'] = array ('port_name' => $matches[1]);
		return 'vrp53PickInterfaceSubcommand';
	default:
		return __FUNCTION__;
	}
}

function vrp53ParseVLANString ($string)
{
	$string = preg_replace ('/ to /', '-', $string);
	$string = preg_replace ('/ /', ',', $string);
	return iosParseVLANString ($string);
}

function vrp53PickInterfaceSubcommand (&$work, $line)
{
	if ($line[0] == '#') // end of interface section
	{
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
			$work['portdata'][$work['current']['port_name']] =
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
			$work['portdata'][$work['current']['port_name']] = array
			(
				'allowed' => $work['current']['allowed'],
				'native' => 0,
				'mode' => 'trunk',
			);
			break;
		case 'hybrid':
			$work['portdata'][$work['current']['port_name']] = array
			(
				'allowed' => $work['current']['allowed'],
				'native' => $work['current']['native'],
				'mode' => 'trunk',
			);
			break;
		default: // dot1q-tunnel ?
		}
		unset ($work['current']);
		return 'vrp53ScanTopLevel';
	}
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^ port default vlan ([[:digit:]]+)$@', $line, $matches)):
		$work['current']['native'] = $matches[1];
		if (!array_key_exists ('allowed', $work['current']))
			$work['current']['allowed'] = array();
		if (!in_array ($matches[1], $work['current']['allowed']))
			$work['current']['allowed'][] = $matches[1];
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
	// TODO: make sure, that a port with "eth-trunk" clause always ends up in "none" mode
	default: // nom-nom
	}
	return __FUNCTION__;
}

function nxos4Read8021QConfig ($input)
{
	$ret = array
	(
		'vlanlist' => array(),
		'portdata' => array(),
	);
	$procfunc = 'nxos4ScanTopLevel';
	foreach (explode ("\n", $input) as $line)
		$procfunc = $procfunc ($ret, $line);
	return $ret;
}

function nxos4ScanTopLevel (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^interface ((Ethernet)[[:digit:]]+(/[[:digit:]]+)*)$@', $line, $matches)):
		$matches[1] = preg_replace ('@^Ethernet(.+)$@', 'e\\1', $matches[1]);
		$work['current'] = array ('port_name' => $matches[1]);
		return 'nxos4PickSwitchportCommand';
	case (preg_match ('@^vlan (.+)$@', $line, $matches)):
		foreach (iosParseVLANString ($matches[1]) as $vlan_id)
			$work['vlanlist'][] = $vlan_id;
		return __FUNCTION__;
	default:
		return __FUNCTION__; // continue scan
	}
}

function nxos4PickSwitchportCommand (&$work, $line)
{
	if ($line == '') // end of interface section
	{
		// fill in defaults
		if (!array_key_exists ('mode', $work['current']))
			$work['current']['mode'] = 'access';
		// save work, if it makes sense
		switch ($work['current']['mode'])
		{
		case 'access':
			if (!array_key_exists ('access vlan', $work['current']))
				$work['current']['access vlan'] = 1;
			$work['portdata'][$work['current']['port_name']] = array
			(
				'mode' => 'access',
				'allowed' => array ($work['current']['access vlan']),
				'native' => $work['current']['access vlan'],
			);
			break;
		case 'trunk':
			if (!array_key_exists ('trunk native vlan', $work['current']))
				$work['current']['trunk native vlan'] = 1;
			// FIXME: NX-OS reserves VLANs 3968 through 4047 plus 4094 for itself
			if (!array_key_exists ('trunk allowed vlan', $work['current']))
				$work['current']['trunk allowed vlan'] = range (VLAN_MIN_ID, VLAN_MAX_ID);
			// Having configured VLAN as "native" doesn't mean anything
			// as long as it's not listed on the "allowed" line.
			$effective_native = in_array
			(
				$work['current']['trunk native vlan'],
				$work['current']['trunk allowed vlan']
			) ? $work['current']['trunk native vlan'] : 0;
			$work['portdata'][$work['current']['port_name']] = array
			(
				'mode' => 'trunk',
				'allowed' => $work['current']['trunk allowed vlan'],
				'native' => $effective_native,
			);
			break;
		default:
			// dot1q-tunnel, dynamic, private-vlan, FEX
			$work['portdata'][$work['current']['port_name']] = array
			(
				'mode' => 'none',
				'allowed' => array(),
				'native' => 0,
			);
			// unset (routed), dot1q-tunnel, dynamic, private-vlan --- skip these
		}
		unset ($work['current']);
		return 'nxos4ScanTopLevel';
	}
	// not yet
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^  switchport mode (.+)$@', $line, $matches)):
		$work['current']['mode'] = $matches[1];
		break;
	case (preg_match ('@^  switchport access vlan (.+)$@', $line, $matches)):
		$work['current']['access vlan'] = $matches[1];
		break;
	case (preg_match ('@^  switchport trunk native vlan (.+)$@', $line, $matches)):
		$work['current']['trunk native vlan'] = $matches[1];
		break;
	case (preg_match ('@^  switchport trunk allowed vlan add (.+)$@', $line, $matches)):
		$work['current']['trunk allowed vlan'] = array_merge
		(
			$work['current']['trunk allowed vlan'],
			iosParseVLANString ($matches[1])
		);
		break;
	case (preg_match ('@^  switchport trunk allowed vlan (.+)$@', $line, $matches)):
		$work['current']['trunk allowed vlan'] = iosParseVLANString ($matches[1]);
		break;
	default: // suppress warning on irrelevant config clause
	}
	return __FUNCTION__;
}

function ios12TranslatePushQueue ($queue, $do_save = FALSE)
{
	$ret = "configure terminal\n";
	$ret .= ciscoCommonTranslator ($queue);
	if ($do_save)
		$ret .= "write memory\n";
	return $ret;
}

function nxos4TranslatePushQueue ($queue, $do_save = FALSE)
{
	$ret = "configure terminal\n";
	$ret .= ciscoCommonTranslator ($queue);
	if ($do_save)
		$ret .= "copy running-config startup-config\n";
	return $ret;
}

// Get a list of VLAN management pseudo-commands and return a text
// of real vendor-specific commands, which implement the work.
// This work is done in two rounds:
// 1. For "add allowed" and "rem allowed" commands detect continuous
//    sequences of VLAN IDs and replace them with ranges of form "A-B",
//    where B>A.
// 2. Iterate over the resulting list and produce real CLI commands.
function ciscoCommonTranslator ($queue)
{
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
			$ret .= "interface ${cmd['arg1']}\nswitchport mode ${cmd['arg2']}\n";
			if ($cmd['arg2'] == 'trunk')
				$ret .= "no switchport trunk native vlan\nswitchport trunk allowed vlan none\n";
			$ret .= "exit\n";
			break;
		}
	$ret .= "end\n";
	return $ret;
}

function fdry5TranslatePushQueue ($queue, $do_save = FALSE)
{
	$ret = "conf t\n";
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
		}
	$ret .= "end\n";
	if ($do_save)
		$ret .= "write memory\n";
	return $ret;
}

function vrp53TranslatePushQueue ($queue, $do_save = FALSE)
{
	$ret = "system-view\n";
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
		}
	$ret .= "return\n";
	if ($do_save)
		$ret .= "save\nY\n";
	return $ret;
}

function xos12TranslatePushQueue ($queue, $do_save = FALSE)
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
		case 'set mode': // NOP
			break;
		}
	if ($do_save)
		$ret .= "save configuration\ny\n";
	return $ret;
}

function ios12ScanCDPTopLevel (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case preg_match ('/^Device ID: (.+)$/', $line, $matches):
		$work['current'] = array ('device' => $matches[1]);
		return 'ios12ScanCDPEntry';
	default:
		return __FUNCTION__; // continue scan
	}
}

function ios12ScanCDPEntry (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case preg_match ('/^Interface: (.+),  Port ID \(outgoing port\): (.+)$/', $line, $matches):
		$work[ios12ShortenIfName ($matches[1])] = array
		(
			'device' => $work['current']['device'],
			'port' => ios12ShortenIfName ($matches[2]),
		);
		unset ($work['current']);
		return 'ios12ScanCDPTopLevel';
	default:
	}
	return __FUNCTION__;
}

function xos12Read8021QConfig ($input)
{
	$ret = array
	(
		'vlanlist' => array (1),
		'portdata' => array(),
	);
	foreach (explode ("\n", $input) as $line)
	{
		$matches = array();
		switch (TRUE)
		{
		case (preg_match ('/^create vlan "([[:alnum:]]+)"$/', $line, $matches)):
			if (!preg_match ('/^VLAN[[:digit:]]+$/', $matches[1]))
				throw new Exception ('unsupported VLAN name ' . $matches[1], E_GW_FAILURE);
			break;
		case (preg_match ('/^configure vlan ([[:alnum:]]+) tag ([[:digit:]]+)$/', $line, $matches)):
			if (strtolower ($matches[1]) == 'default')
				throw new Exception ('default VLAN tag must be 1', E_GW_FAILURE);
			if ($matches[1] != 'VLAN' . $matches[2])
				throw new Exception ("VLAN name ${matches[1]} does not match its tag ${matches[2]}", E_GW_FAILURE);
			$ret['vlanlist'][] = $matches[2];
			break;
		case (preg_match ('/^configure vlan ([[:alnum:]]+) add ports (.+) (tagged|untagged) */', $line, $matches)):
			$submatch = array();
			if ($matches[1] == 'Default')
				$matches[1] = 'VLAN1';
			if (!preg_match ('/^VLAN([[:digit:]]+)$/', $matches[1], $submatch))
				throw new Exception ('unsupported VLAN name ' . $matches[1], E_GW_FAILURE);
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

?>
