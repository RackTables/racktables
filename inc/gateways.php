<?
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
		return array ('ERR proc_open() failed in queryGateway()');

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
		showError ('Invalid object_id in getSwitchVLANs()');
		return;
	}
	$objectInfo = getObjectInfo ($object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
	{
		showError ('Can\'t find any mean to reach current object. Please either set FQDN attribute or assign an IP address to the object.');
		return NULL;
	}
	if (count ($endpoints) > 1)
	{
		showError ('More than one IP address is assigned to this object, please configure FQDN attribute.');
		return NULL;
	}
	$hwtype = $swtype = 'unknown';
	foreach (getAttrValues ($object_id) as $record)
	{
		if ($record['name'] == 'SW type' && !empty ($record['value']))
			$swtype = str_replace (' ', '+', $record['value']);
		if ($record['name'] == 'HW type' && !empty ($record['value']))
			$hwtype = str_replace (' ', '+', $record['value']);
	}
	$commands = array
	(
		"connect ${endpoints[0]} $hwtype $swtype ${remote_username}",
		'listvlans',
		'listports',
		'listmacs'
	);
	$data = queryGateway ('switchvlans', $commands);
	if ($data == NULL)
	{
		showError ('Failed to get any response from queryGateway() or the gateway died');
		return NULL;
	}
	if (strpos ($data[0], 'OK!') !== 0)
	{
		showError ("Gateway failure: returned code ${data[0]}.");
		return NULL;
	}
	if (count ($data) != count ($commands))
	{
		showError ("Gateway failure: mailformed reply.");
		return NULL;
	}
	// Now we have VLAN list in $data[1] and port list in $data[2]. Let's sort this out.
	$tmp = array_unique (explode (';', substr ($data[1], strlen ('OK!'))));
	if (count ($tmp) == 0)
	{
		showError ("Gateway succeeded, but returned no VLAN records.");
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
		showError ("Gateway succeeded, but returned no port records.");
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
	$log = array();
	if ($object_id <= 0)
		return array (array ('code' => 'error', 'message' => 'Invalid object_id in setSwitchVLANs()'));
	$objectInfo = getObjectInfo ($object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		return array (array ('code' => 'error', 'message' => 'Can\'t find any mean to reach current object. Please either set FQDN attribute or assign an IP address to the object.'));
	if (count ($endpoints) > 1)
		return array (array ('code' => 'error', 'message' => 'More than one IP address is assigned to this object, please configure FQDN attribute.'));
	$hwtype = $swtype = 'unknown';
	foreach (getAttrValues ($object_id) as $record)
	{
		if ($record['name'] == 'SW type' && !empty ($record['value']))
			$swtype = strtr ($record['value'], ' ', '+');
		if ($record['name'] == 'HW type' && !empty ($record['value']))
			$hwtype = strtr ($record['value'], ' ', '+');
	}
	$data = queryGateway
	(
		'switchvlans',
		array ("connect ${endpoints[0]} $hwtype $swtype ${remote_username}", $setcmd)
	);
	if ($data == NULL)
		return array (array ('code' => 'error', 'message' => 'Failed to get any response from queryGateway() or the gateway died'));
	if (strpos ($data[0], 'OK!') !== 0)
		return array (array ('code' => 'error', 'message' => "Gateway failure: returned code ${data[0]}."));
	if (count ($data) != 2)
		return array (array ('code' => 'error', 'message' => 'Gateway failure: mailformed reply.'));
	// Finally we can parse the response into message array.
	$ret = array();
	foreach (split (';', substr ($data[1], strlen ('OK!'))) as $text)
	{
		if (strpos ($text, 'I!') === 0)
			$code = 'success';
		elseif (strpos ($text, 'W!') === 0)
			$code = 'warning';
		else // All improperly formatted messages must be treated as error conditions.
			$code = 'error';
		$ret[] = array ('code' => $code, 'message' => substr ($text, 2));
	}
	return $ret;
}

?>
