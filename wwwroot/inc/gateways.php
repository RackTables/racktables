<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

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
$breedfunc = array
(
	'ios12-getcdpstatus-main'  => 'ios12ReadCDPStatus',
	'ios12-getlldpstatus-main' => 'ios12ReadLLDPStatus',
	'ios12-get8021q-main'      => 'ios12ReadVLANConfig',
	'ios12-get8021q-top'       => 'ios12ScanTopLevel',
	'ios12-get8021q-readport'  => 'ios12PickSwitchportCommand',
	'ios12-get8021q-readvlan'  => 'ios12PickVLANCommand',
	'ios12-getportstatus-main' => 'ciscoReadInterfaceStatus',
	'ios12-getmaclist-main'    => 'ios12ReadMacList',
	'ios12-xlatepushq-main'    => 'ios12TranslatePushQueue',
	'ios12-getallconf-main'    => 'ios12SpotConfigText',
	'fdry5-get8021q-main'      => 'fdry5ReadVLANConfig',
	'fdry5-get8021q-top'       => 'fdry5ScanTopLevel',
	'fdry5-get8021q-readvlan'  => 'fdry5PickVLANSubcommand',
	'fdry5-get8021q-readport'  => 'fdry5PickInterfaceSubcommand',
	'fdry5-xlatepushq-main'    => 'fdry5TranslatePushQueue',
	'fdry5-getallconf-main'    => 'fdry5SpotConfigText',
	'vrp53-getlldpstatus-main' => 'vrp5xReadLLDPStatus',
	'vrp53-get8021q-main'      => 'vrp53ReadVLANConfig',
	'vrp53-get8021q-top'       => 'vrp53ScanTopLevel',
	'vrp53-get8021q-readport'  => 'vrp53PickInterfaceSubcommand',
	'vrp53-getportstatus-main' => 'vrpReadInterfaceStatus',
	'vrp53-getmaclist-main'    => 'vrp53ReadMacList',
	'vrp53-xlatepushq-main'    => 'vrp53TranslatePushQueue',
	'vrp53-getallconf-main'    => 'vrp5xSpotConfigText',
	'vrp55-getlldpstatus-main' => 'vrp5xReadLLDPStatus',
	'vrp55-get8021q-main'      => 'vrp55Read8021QConfig',
	'vrp55-getportstatus-main' => 'vrpReadInterfaceStatus',
	'vrp55-getmaclist-main'    => 'vrp55ReadMacList',
	'vrp55-xlatepushq-main'    => 'vrp55TranslatePushQueue',
	'vrp55-getallconf-main'    => 'vrp5xSpotConfigText',
	'nxos4-getcdpstatus-main'  => 'ios12ReadCDPStatus',
	'nxos4-getlldpstatus-main' => 'nxos4ReadLLDPStatus',
	'nxos4-get8021q-main'      => 'nxos4Read8021QConfig',
	'nxos4-get8021q-top'       => 'nxos4ScanTopLevel',
	'nxos4-get8021q-readport'  => 'nxos4PickSwitchportCommand',
	'nxos4-get8021q-readvlan'  => 'nxos4PickVLANCommand',
	'nxos4-getportstatus-main' => 'ciscoReadInterfaceStatus',
	'nxos4-getmaclist-main'    => 'nxos4ReadMacList',
	'nxos4-xlatepushq-main'    => 'nxos4TranslatePushQueue',
	'nxos4-getallconf-main'    => 'nxos4SpotConfigText',
	'dlink-get8021q-main'      => 'dlinkReadVLANConfig',
	'dlink-get8021q-top'       => 'dlinkScanTopLevel',
	'dlink-get8021q-pickvlan'  => 'dlinkPickVLANCommand',
	'dlink-getportstatus-main' => 'dlinkReadInterfaceStatus',
	'dlink-getmaclist-main'    => 'dlinkReadMacList',
	'dlink-xlatepushq-main'    => 'dlinkTranslatePushQueue',
	'linux-get8021q-main'      => 'linuxReadVLANConfig',
	'linux-getportstatus-main' => 'linuxReadInterfaceStatus',
	'linux-getmaclist-main'    => 'linuxReadMacList',
	'linux-xlatepushq-main'    => 'linuxTranslatePushQueue',
	'xos12-getlldpstatus-main' => 'xos12ReadLLDPStatus',
	'xos12-get8021q-main'      => 'xos12Read8021QConfig',
	'xos12-xlatepushq-main'    => 'xos12TranslatePushQueue',
	'xos12-getallconf-main'    => 'xos12SpotConfigText',
	'jun10-get8021q-main'      => 'jun10Read8021QConfig',
	'jun10-xlatepushq-main'    => 'jun10TranslatePushQueue',
	'jun10-getallconf-main'    => 'jun10SpotConfigText',
	'ftos8-xlatepushq-main'    => 'ftos8TranslatePushQueue',
	'ftos8-getlldpstatus-main' => 'ftos8ReadLLDPStatus',
	'ftos8-getmaclist-main'    => 'ftos8ReadMacList',
	'ftos8-getportstatus-main' => 'ftos8ReadInterfaceStatus',
	'ftos8-get8021q-main'      => 'ftos8Read8021QConfig',
	'ftos8-getallconf-main'    => 'ftos8SpotConfigText',
	'air12-xlatepushq-main'    => 'air12TranslatePushQueue',
	'air12-getallconf-main'    => 'ios12SpotConfigText',
	'eos4-getallconf-main'     => 'eos4SpotConfigText',
	'eos4-getmaclist-main'     => 'eos4ReadMacList',
	'eos4-getportstatus-main'  => 'eos4ReadInterfaceStatus',
	'eos4-getlldpstatus-main'  => 'eos4ReadLLDPStatus',
	'eos4-get8021q-main'       => 'eos4Read8021QConfig',
	'eos4-xlatepushq-main'     => 'eos4TranslatePushQueue',
	'ros11-getallconf-main'    => 'ros11SpotConfigText',
	'ros11-xlatepushq-main'    => 'ros11TranslatePushQueue',
	'ros11-getlldpstatus-main' => 'ros11ReadLLDPStatus',
	'ros11-getportstatus-main' => 'ros11ReadInterfaceStatus',
	'ros11-getmaclist-main'    => 'ros11ReadMacList',
	'ros11-get8021q-main'      => 'ros11Read8021QConfig',
	'ros11-get8021q-scantop'   => 'ros11Read8021QScanTop',
	'ros11-get8021q-vlandb'    => 'ros11Read8021QVLANDatabase',
	'ros11-get8021q-readports' => 'ros11Read8021QPorts',
	'iosxr4-xlatepushq-main'    => 'iosxr4TranslatePushQueue',
	'iosxr4-getallconf-main'    => 'iosxr4SpotConfigText',
	'ucs-xlatepushq-main'      => 'ucsTranslatePushQueue',
	'ucs-getinventory-main'    => 'ucsReadInventory',
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
	global $racktables_gwdir;
	$execpath = "${racktables_gwdir}/{$gwname}/main";
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
		throw new RTGatewayError ("gateway failed with code ${retval}");
	if (!count ($answers))
		throw new RTGatewayError ('no response from gateway');
	if (count ($answers) != count ($questions))
		throw new RTGatewayError ('protocol violation');
	foreach ($answers as $a)
		if (strpos ($a, 'OK!') !== 0)
			throw new RTGatewayError ("subcommand failed with status: ${a}");
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
		throw new RTGatewayError ('no management address set');
	if (count ($endpoints) > 1)
		throw new RTGatewayError ('cannot pick management address');
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
		throw new RTGatewayError ("gateway failed with status: ${data[0]}.");
	// Now we have VLAN list in $data[1] and port list in $data[2]. Let's sort this out.
	$tmp = array_unique (explode (';', substr ($data[1], strlen ('OK!'))));
	if (count ($tmp) == 0)
		throw new RTGatewayError ('gateway returned no records');
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
		throw new RTGatewayError ('gateway returned no records');
	$maclist = array();
	foreach (explode (';', substr ($data[3], strlen ('OK!'))) as $pair)
		if (preg_match ('/^([^=]+)=(.+)/', $pair, $m))
		{
			$macaddr = $m[1];
			list ($vlanid, $ifname) = explode ('@', $m[2]);
			$maclist[$ifname][$vlanid][] = $macaddr;
		}
	return array ($vlanlist, $portlist, $maclist);
}

function setSwitchVLANs ($object_id = 0, $setcmd)
{
	global $remote_username;
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		throw new RTGatewayError ('no management address set');
	if (count ($endpoints) > 1)
		throw new RTGatewayError ('cannot pick management address');
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
	foreach (explode (';', substr ($data[1], strlen ('OK!'))) as $text)
	{
		$message = 'gw: ' . substr ($text, 2);
		if (strpos ($text, 'I!') === 0)
			showSuccess ($message); // generic gateway success
		elseif (strpos ($text, 'W!') === 0)
			showWarning ($message); // generic gateway warning
		elseif (strpos ($text, 'E!') === 0)
			showError ($message); // generic gateway error
		else // All improperly formatted messages must be treated as error conditions.
			showError ('unexpected line from gw: ' . $text);
	}
}

// Drop a file off RackTables platform. The gateway will catch the file and pass it to the given
// installer script.
// On success returns the text string printed by sendfile handler
// On failure throws an exception
function gwSendFile ($endpoint, $handlername, $filetext = array())
{
	$result = '';
	if (! is_array ($filetext))
		throw new InvalidArgException ('filetext', '(suppressed)', 'is not an array');
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
			throw new RTGatewayError ('failed to write to temporary file');
		}
		$command .= " ${name}";
	}
	try
	{
		$answers = queryGateway ('sendfile', array ($command));
		$result = preg_replace ('/^OK!\s*/', '', array_shift ($answers));
		foreach ($tmpnames as $name)
			unlink ($name);
	}
	catch (RTGatewayError $e)
	{
		foreach ($tmpnames as $name)
			unlink ($name);
		throw new RTGatewayError ("Sending $handlername to $endpoint: " . $e->getMessage());
	}
	return $result;
}

// Query something through a gateway and get some text in return. Return that text.
function gwRecvFile ($endpoint, $handlername, &$output)
{
	global $remote_username;
	$tmpfilename = tempnam ('', 'RackTables-sendfile-');
	$endpoint = str_replace (' ', '\ ', $endpoint); // the gateway dispatcher uses read (1) to assign arguments
	try
	{
		queryGateway ('sendfile', array ("submit ${remote_username} ${endpoint} ${handlername} ${tmpfilename}"));
		$output = file_get_contents ($tmpfilename);
		unlink ($tmpfilename);
	}
	catch (RTGatewayError $e)
	{
		unlink ($tmpfilename);
		throw $e;
	}
	if ($output === FALSE)
		throw new RTGatewayError ('failed to read temporary file');
}

function gwSendFileToObject ($object_id, $handlername, $filetext = '')
{
	if (!mb_strlen ($handlername))
		throw new InvalidArgException ('$handlername');
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		throw new RTGatewayError ('no management address set');
	if (count ($endpoints) > 1)
		throw new RTGatewayError ('cannot pick management address');
	return gwSendFile (str_replace (' ', '+', $endpoints[0]), $handlername, array ($filetext));
}

function gwRecvFileFromObject ($object_id, $handlername, &$output)
{
	if (!mb_strlen ($handlername))
		throw new InvalidArgException ('$handlername');
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		throw new RTGatewayError ('no management address set');
	if (count ($endpoints) > 1)
		throw new RTGatewayError ('cannot pick management address');
	gwRecvFile (str_replace (' ', '+', $endpoints[0]), $handlername, $output);
}

function detectDeviceBreed ($object_id)
{
	$breed_by_swcode = array
	(
		251 => 'ios12',
		252 => 'ios12',
		254 => 'ios12',
		963 => 'nxos4', // NX-OS 4.0
		964 => 'nxos4', // NX-OS 4.1
		1365 => 'nxos4', // NX-OS 4.2
		1410 => 'nxos4', // NX-OS 5.0, seems compatible
		1411 => 'nxos4', // NX-OS 5.1
		1643 => 'nxos4', // NX-OS 6.0
		1352 => 'xos12',
		1360 => 'vrp53',
		1361 => 'vrp55',
		1369 => 'vrp55', // VRP versions 5.5 and 5.7 seem to be compatible
		1363 => 'fdry5',
		1367 => 'jun10', # 10S
		1597 => 'jun10', # 10R
		1598 => 'jun10', # 11R
		1599 => 'jun10', # 12R
		1594 => 'ftos8',
		1673 => 'air12', # AIR IOS 12.3
		1674 => 'air12', # AIR IOS 12.4
		1675 => 'eos4',
		1759 => 'iosxr4', # Cisco IOS XR 4.2
		1786 => 'ros11', # Marvell ROS 1.1
		242 => 'linux',
		243 => 'linux',
		1331 => 'linux',
		1332 => 'linux',
		1333 => 'linux',
		1334 => 'linux',
		1395 => 'linux',
		1396 => 'linux',
	);
	for ($i = 225; $i <= 235; $i++)
		$breed_by_swcode[$i] = 'linux';
	for ($i = 418; $i <= 436; $i++)
		$breed_by_swcode[$i] = 'linux';
	for ($i = 1417; $i <= 1422; $i++)
		$breed_by_swcode[$i] = 'linux';
	$breed_by_hwcode = array();
	for ($i = 589; $i <= 637; $i++)
		$breed_by_hwcode[$i] = 'dlink';
	$breed_by_mgmtcode = array (1788 => 'ucs');
	foreach (getAttrValues ($object_id) as $record)
		if ($record['id'] == 4 and array_key_exists ($record['key'], $breed_by_swcode))
			return $breed_by_swcode[$record['key']];
		elseif ($record['id'] == 2 and array_key_exists ($record['key'], $breed_by_hwcode))
			return $breed_by_hwcode[$record['key']];
		elseif ($record['id'] == 30 and array_key_exists ($record['key'], $breed_by_mgmtcode))
			return $breed_by_mgmtcode[$record['key']];
	return '';
}

function validBreedFunction ($breed, $command)
{
	global $breedfunc;
	return array_key_exists ("${breed}-${command}-main", $breedfunc);
}

function assertBreedFunction ($breed, $command)
{
	global $breedfunc;
	if (! validBreedFunction ($breed, $command))
		throw new RTGatewayError ('unsupported command for this breed');
}

function gwRetrieveDeviceConfig ($object_id, $command)
{
	require_once 'deviceconfig.php';
	global $breedfunc;
	$breed = detectDeviceBreed ($object_id);
	assertBreedFunction ($breed, $command);
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		throw new RTGatewayError ('no management address set');
	if (count ($endpoints) > 1)
		throw new RTGatewayError ('cannot pick management address');
	$endpoint = str_replace (' ', '\ ', str_replace (' ', '+', $endpoints[0]));
	$tmpfilename = tempnam ('', 'RackTables-deviceconfig-');
	try
	{
		queryGateway ('deviceconfig', array ("${command} ${endpoint} ${breed} ${tmpfilename}"));
		$configtext = file_get_contents ($tmpfilename);
		unlink ($tmpfilename);
	}
	catch (RTGatewayError $e)
	{
		unlink ($tmpfilename);
		throw $e;
	}
	if ($configtext === FALSE)
		throw new RTGatewayError ('failed to read temporary file');
	// Being here means it was alright.
	return $breedfunc["${breed}-${command}-main"] (dos2unix ($configtext));
}

function gwDeployDeviceConfig ($object_id, $breed, $text)
{
	if ($text == '')
		throw new InvalidArgException ('text', '', 'deploy text is empty');
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		throw new RTGatewayError ('no management address set');
	if (count ($endpoints) > 1)
		throw new RTGatewayError ('cannot pick management address');
	$endpoint = str_replace (' ', '\ ', str_replace (' ', '+', $endpoints[0]));
	$tmpfilename = tempnam ('', 'RackTables-deviceconfig-');
	if (FALSE === file_put_contents ($tmpfilename, $text))
	{
		unlink ($tmpfilename);
		throw new RTGatewayError ('failed to write to temporary file');
	}
	try
	{
		queryGateway ('deviceconfig', array ("deploy ${endpoint} ${breed} ${tmpfilename}"));
		unlink ($tmpfilename);
	}
	catch (RTGatewayError $e)
	{
		unlink ($tmpfilename);
		throw $e;
	}
}

?>
