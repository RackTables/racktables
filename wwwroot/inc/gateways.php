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
$gwrxlator['getcdpstatus'] = array
(
	'ios12' => 'ios12ReadCDPStatus',
	'nxos4' => 'ios12ReadCDPStatus',
);
$gwrxlator['getlldpstatus'] = array
(
	'ios12' => 'ios12ReadLLDPStatus',
	'xos12' => 'xos12ReadLLDPStatus',
	'vrp53' => 'vrp53ReadLLDPStatus',
	'vrp55' => 'vrp55ReadLLDPStatus',
);
$gwrxlator['get8021q'] = array
(
	'ios12' => 'ios12ReadVLANConfig',
	'fdry5' => 'fdry5ReadVLANConfig',
	'vrp53' => 'vrp53ReadVLANConfig',
	'vrp55' => 'vrp55Read8021QConfig',
	'nxos4' => 'nxos4Read8021QConfig',
	'xos12' => 'xos12Read8021QConfig',
);
$gwrxlator['getportstatus'] = array
(
	'ios12' => 'ciscoReadInterfaceStatus',
	'vrp53' => 'vrpReadInterfaceStatus',
	'vrp55' => 'vrpReadInterfaceStatus',
	'nxos4' => 'ciscoReadInterfaceStatus',
);
$gwrxlator['getmaclist'] = array
(
	'ios12' => 'ios12ReadMacList',
	'vrp53' => 'vrp53ReadMacList',
	'vrp55' => 'vrp55ReadMacList',
	'nxos4' => 'nxos4ReadMacList',
);

$gwrxlator['gethndp']['vrp53'] = 'vrp53ReadHNDPStatus';

$gwpushxlator = array
(
	'ios12' => 'ios12TranslatePushQueue',
	'fdry5' => 'fdry5TranslatePushQueue',
	'vrp53' => 'vrp53TranslatePushQueue',
	'vrp55' => 'vrp55TranslatePushQueue',
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
	$log = emptyLog();
	foreach (explode (';', substr ($data[1], strlen ('OK!'))) as $text)
	{
		if (strpos ($text, 'C!') === 0)
		{
			// gateway-encoded message
			$tmp = explode ('!', $text);
			array_shift ($tmp);
			$code = array_shift ($tmp);
			$log = mergeLogs ($log, oneLiner ($code, $tmp));
		}
		elseif (strpos ($text, 'I!') === 0)
			$log = mergeLogs ($log, oneLiner (62, array (substr ($text, 2)))); // generic gateway success
		elseif (strpos ($text, 'W!') === 0)
			$log = mergeLogs ($log, oneLiner (202, array (substr ($text, 2)))); // generic gateway warning
		else // All improperly formatted messages must be treated as error conditions.
			$log = mergeLogs ($log, oneLiner (166, array (substr ($text, 2)))); // generic gateway error
	}
	return $log;
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
			throw new RTGatewayError ('failed to write to temporary file');
		}
		$command .= " ${name}";
	}
	try
	{
		queryGateway ('sendfile', array ($command));
		foreach ($tmpnames as $name)
			unlink ($name);
	}
	catch (RTGatewayError $e)
	{
		foreach ($tmpnames as $name)
			unlink ($name);
		throw $e;
	}
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
	gwSendFile (str_replace (' ', '+', $endpoints[0]), $handlername, array ($filetext));
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
		963 => 'nxos4',
		964 => 'nxos4',
		1365 => 'nxos4',
		1352 => 'xos12',
		1360 => 'vrp53',
		1361 => 'vrp55',
		1369 => 'vrp55', // VRP versions 5.5 and 5.7 seem to be compatible
		1363 => 'fdry5',
	);
	foreach (getAttrValues ($object_id) as $record)
		if ($record['id'] == 4 and array_key_exists ($record['key'], $breed_by_swcode))
			return $breed_by_swcode[$record['key']];
	return '';
}

function getRunning8021QConfig ($object_id)
{
	$ret = gwRetrieveDeviceConfig ($object_id, 'get8021q');
	// Once there is no default VLAN in the parsed data, it means
	// something else was parsed instead of config text.
	if (!in_array (VLAN_DFL_ID, $ret['vlanlist']))
		throw new RTGatewayError ('communication with device failed');
	return $ret;
}

function setDevice8021QConfig ($object_id, $pseudocode)
{
	require_once 'deviceconfig.php';
	if ('' == $breed = detectDeviceBreed ($object_id))
		throw new RTGatewayError ('device breed unknown');
	global $gwpushxlator;
	// FIXME: this is a perfect place to log intended changes
	gwDeployDeviceConfig ($object_id, $breed, unix2dos ($gwpushxlator[$breed] ($pseudocode)));
}

function gwRetrieveDeviceConfig ($object_id, $command)
{
	require_once 'deviceconfig.php';
	global $gwrxlator;
	if (!array_key_exists ($command, $gwrxlator))
		throw new RTGatewayError ('command unknown');
	$breed = detectDeviceBreed ($object_id);
	if (!array_key_exists ($breed, $gwrxlator[$command]))
		throw new RTGatewayError ('device breed unknown');
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
	return $gwrxlator[$command][$breed] (dos2unix ($configtext));
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
