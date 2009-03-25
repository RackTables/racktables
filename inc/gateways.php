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
		if (empty ($a))
			continue;
		// Somehow I got a space appended at the end. Kick it.
		$answers[] = trim ($a);
	}
	fclose($pipes[1]);

	$retval = proc_close ($gateway);
	if ($retval != 0)
		return array ("ERR gateway '${gwname}' returned ${retval}");
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
	if ($object_id <= 0)
	{
		showError ('Invalid object_id', __FUNCTION__);
		return;
	}
	$objectInfo = getObjectInfo ($object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
	{
		showError ('Can\'t find any mean to reach current object. Please either set FQDN attribute or assign an IP address to the object.', __FUNCTION__);
		return NULL;
	}
	if (count ($endpoints) > 1)
	{
		showError ('More than one IP address is assigned to this object, please configure FQDN attribute.', __FUNCTION__);
		return NULL;
	}
	$hwtype = $swtype = 'unknown';
	foreach (getAttrValues ($object_id, TRUE) as $record)
	{
		if ($record['name'] == 'SW type' && !empty ($record['value']))
			$swtype = str_replace (' ', '+', $record['value']);
		if ($record['name'] == 'HW type' && !empty ($record['value']))
			$hwtype = str_replace (' ', '+', $record['value']);
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
	if ($data == NULL)
	{
		showError ('Failed to get any response from queryGateway() or the gateway died', __FUNCTION__);
		return NULL;
	}
	if (strpos ($data[0], 'OK!') !== 0)
	{
		showError ("Gateway failure: ${data[0]}.", __FUNCTION__);
		return NULL;
	}
	if (count ($data) != count ($commands))
	{
		showError ("Gateway failure: malformed reply.", __FUNCTION__);
		return NULL;
	}
	// Now we have VLAN list in $data[1] and port list in $data[2]. Let's sort this out.
	$tmp = array_unique (explode (';', substr ($data[1], strlen ('OK!'))));
	if (count ($tmp) == 0)
	{
		showError ("Gateway succeeded, but returned no VLAN records.", __FUNCTION__);
		return NULL;
	}
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
	{
		showError ("Gateway succeeded, but returned no port records.", __FUNCTION__);
		return NULL;
	}
	$maclist = array();
	foreach (explode (';', substr ($data[3], strlen ('OK!'))) as $pair)
	{
		list ($macaddr, $pair2) = explode ('=', $pair);
		if (empty ($pair2))
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
	$objectInfo = getObjectInfo ($object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		return oneLiner (161); // endpoint not found
	if (count ($endpoints) > 1)
		return oneLiner (162); // can't pick an address
	$hwtype = $swtype = 'unknown';
	foreach (getAttrValues ($object_id, TRUE) as $record)
	{
		if ($record['name'] == 'SW type' && !empty ($record['value']))
			$swtype = strtr ($record['value'], ' ', '+');
		if ($record['name'] == 'HW type' && !empty ($record['value']))
			$hwtype = strtr ($record['value'], ' ', '+');
	}
	$endpoint = str_replace (' ', '+', $endpoints[0]);
	$data = queryGateway
	(
		'switchvlans',
		array ("connect ${endpoint} ${hwtype} ${swtype} ${remote_username}", $setcmd)
	);
	if ($data == NULL)
		return oneLiner (163); // unknown gateway failure
	if (strpos ($data[0], 'OK!') !== 0)
		return oneLiner (164, array ($data[0])); // gateway failure
	if (count ($data) != 2)
		return oneLiner (165); // protocol violation
	// Finally we can parse the response into message array.
	$log_m = array();
	foreach (split (';', substr ($data[1], strlen ('OK!'))) as $text)
	{
		if (strpos ($text, 'C!') === 0)
		{
			$tmp = split ('!', $text);
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
// Return a 
function gwSendFile ($endpoint, $handlername, $filetext = '')
{
	global $remote_username;
	$tmpfilename = tempnam ('', 'RackTables-sendfile-');
	$tmpfile = fopen ($tmpfilename, 'wb');
	fwrite ($tmpfile, $filetext);
	fclose ($tmpfile);
	$endpoint = str_replace (' ', '\ ', $endpoint); // the gateway dispatcher uses read (1) to assign arguments
	$outputlines = queryGateway
	(
		'sendfile',
		array ("submit ${remote_username} ${endpoint} ${handlername} ${tmpfilename}")
	);
	unlink ($tmpfilename);
	if ($outputlines == NULL)
		return oneLiner (163); // unknown gateway failure
	if (count ($outputlines) != 1)
		return oneLiner (165); // protocol violation
	if (strpos ($outputlines[0], 'OK!') !== 0)
		return oneLiner (164, array ($outputlines[0])); // gateway failure
	// Being here means having 'OK!' in the response.
	return oneLiner (66, array ($handlername)); // ignore provided "Ok" text
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
	if ($outputlines == NULL)
		return oneLiner (163); // unknown gateway failure
	if (count ($outputlines) != 1)
		return oneLiner (165); // protocol violation
	if (strpos ($outputlines[0], 'OK!') !== 0)
		return oneLiner (164, array ($outputlines[0])); // gateway failure
	// Being here means having 'OK!' in the response.
	return oneLiner (66, array ($handlername)); // ignore provided "Ok" text
}

function gwSendFileToObject ($object_id = 0, $handlername, $filetext = '')
{
	global $remote_username;
	if ($object_id <= 0 or empty ($handlername))
		return oneLiner (160); // invalid arguments
	$objectInfo = getObjectInfo ($object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		return oneLiner (161); // endpoint not found
	if (count ($endpoints) > 1)
		return oneLiner (162); // can't pick an address
	$endpoint = str_replace (' ', '+', $endpoints[0]);
	return gwSendFile ($endpoint, $handlername, $filetext);
}

function gwRecvFileFromObject ($object_id = 0, $handlername, &$output)
{
	global $remote_username;
	if ($object_id <= 0 or empty ($handlername))
		return oneLiner (160); // invalid arguments
	$objectInfo = getObjectInfo ($object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		return oneLiner (161); // endpoint not found
	if (count ($endpoints) > 1)
		return oneLiner (162); // can't pick an address
	$endpoint = str_replace (' ', '+', $endpoints[0]);
	return gwRecvFile ($endpoint, $handlername, $output);
}

?>
