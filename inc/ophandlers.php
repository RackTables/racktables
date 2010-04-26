<?php
/*
*
*  This file is a library of operation handlers for RackTables.
*
*/

$msgcode = array();

function buildWideRedirectURL ($log, $nextpage = NULL, $nexttab = NULL, $moreArgs = array())
{
	global $page, $pageno, $tabno;
	if ($nextpage === NULL)
		$nextpage = $pageno;
	if ($nexttab === NULL)
		$nexttab = $tabno;
	$url = "index.php?page=${nextpage}&tab=${nexttab}";
	if (isset ($page[$nextpage]['bypass']))
		$url .= '&' . $page[$nextpage]['bypass'] . '=' . $_REQUEST[$page[$nextpage]['bypass']];

	if (count($moreArgs)>0)
	{
		foreach($moreArgs as $arg=>$value)
		{
			if (gettype($value) == 'array')
			{
				foreach ($value as $v)
				{
					$url .= '&'.urlencode($arg.'[]').'='.urlencode($v);
				}
			}
			else
				$url .= '&'.urlencode($arg).'='.urlencode($value);
		}
	}

	$_SESSION['log'] = $log;
	return $url;
}

function buildRedirectURL ($callfunc, $status, $log_args = array(), $nextpage = NULL, $nexttab = NULL, $url_args = array())
{
	global $pageno, $tabno, $msgcode;
	if ($nextpage === NULL)
		$nextpage = $pageno;
	if ($nexttab === NULL)
		$nexttab = $tabno;
	return buildWideRedirectURL (oneLiner ($msgcode[$callfunc][$status], $log_args), $nextpage, $nexttab, $url_args);
}

$msgcode['addPortForwarding']['OK'] = 2;
$msgcode['addPortForwarding']['ERR'] = 100;
function addPortForwarding ()
{
	assertUIntArg ('object_id');
	assertIPv4Arg ('localip');
	assertIPv4Arg ('remoteip');
	assertUIntArg ('localport');
	assertStringArg ('proto');
	assertStringArg ('description', TRUE);
	$remoteport = isset ($_REQUEST['remoteport']) ? $_REQUEST['remoteport'] : '';
	if (!strlen ($remoteport))
		$remoteport = $_REQUEST['localport'];

	$error = newPortForwarding
	(
		$_REQUEST['object_id'],
		$_REQUEST['localip'],
		$_REQUEST['localport'],
		$_REQUEST['remoteip'],
		$remoteport,
		$_REQUEST['proto'],
		$_REQUEST['description']
	);

	if ($error == '')
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
}

$msgcode['delPortForwarding']['OK'] = 3;
$msgcode['delPortForwarding']['ERR'] = 100;
function delPortForwarding ()
{
	assertUIntArg ('object_id');
	assertIPv4Arg ('localip');
	assertIPv4Arg ('remoteip');
	assertUIntArg ('localport');
	assertUIntArg ('remoteport');
	assertStringArg ('proto');

	$error = deletePortForwarding
	(
		$_REQUEST['object_id'],
		$_REQUEST['localip'],
		$_REQUEST['localport'],
		$_REQUEST['remoteip'],
		$_REQUEST['remoteport'],
		$_REQUEST['proto']
	);
	if ($error == '')
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
}

$msgcode['updPortForwarding']['OK'] = 4;
$msgcode['updPortForwarding']['ERR'] = 100;
function updPortForwarding ()
{
	assertUIntArg ('object_id');
	assertIPv4Arg ('localip');
	assertIPv4Arg ('remoteip');
	assertUIntArg ('localport');
	assertUIntArg ('remoteport');
	assertStringArg ('proto');
	assertStringArg ('description');

	$error = updatePortForwarding
	(
		$_REQUEST['object_id'],
		$_REQUEST['localip'],
		$_REQUEST['localport'],
		$_REQUEST['remoteip'],
		$_REQUEST['remoteport'],
		$_REQUEST['proto'],
		$_REQUEST['description']
	);
	if ($error == '')
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
}

$msgcode['addPortForObject']['OK'] = 5;
$msgcode['addPortForObject']['ERR1'] = 101;
$msgcode['addPortForObject']['ERR2'] = 100;
function addPortForObject ()
{
	assertUIntArg ('object_id');
	assertStringArg ('port_name', TRUE);
	if (!strlen ($_REQUEST['port_name']))
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	$error = commitAddPort ($_REQUEST['object_id'], $_REQUEST['port_name'], $_REQUEST['port_type_id'], $_REQUEST['port_label'], $_REQUEST['port_l2address']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['port_name']));
}

$msgcode['editPortForObject']['OK'] = 6;
$msgcode['editPortForObject']['ERR1'] = 101;
$msgcode['editPortForObject']['ERR2'] = 100;
function editPortForObject ()
{
	assertUIntArg ('object_id');
	assertUIntArg ('port_id');
	assertUIntArg ('port_type_id');
	// tolerate empty value now to produce custom informative message later
	assertStringArg ('name', TRUE);
	if (!strlen ($_REQUEST['name']))
		return buildRedirectURL (__FUNCTION__, 'ERR1');

	if (isset ($_REQUEST['reservation_comment']) and strlen ($_REQUEST['reservation_comment']))
		$port_rc = '"' . $_REQUEST['reservation_comment'] . '"';
	else
		$port_rc = 'NULL';
	$error = commitUpdatePort ($_REQUEST['object_id'], $_REQUEST['port_id'], $_REQUEST['name'], $_REQUEST['port_type_id'], $_REQUEST['label'], $_REQUEST['l2address'], $port_rc);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['name']));
}

$msgcode['delPortFromObject']['OK'] = 7;
$msgcode['delPortFromObject']['ERR'] = 100;
function delPortFromObject ()
{
	assertUIntArg ('port_id');
	$error = delObjectPort ($_REQUEST['port_id']);

	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['port_name']));
}

$msgcode['linkPortForObject']['OK'] = 8;
$msgcode['linkPortForObject']['ERR'] = 100;
function linkPortForObject ()
{
	assertUIntArg ('port_id');
	assertUIntArg ('remote_port_id');

	// FIXME: ensure, that at least one of these ports belongs to the current object
	$error = linkPorts ($_REQUEST['port_id'], $_REQUEST['remote_port_id']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	global $sic;
	$local_port_info = getPortInfo ($sic['port_id']);
	$remote_port_info = getPortInfo ($sic['remote_port_id']);
	$remote_object = spotEntity ('object', $remote_port_info['object_id']);
	return buildRedirectURL
	(
		__FUNCTION__,
		'OK',
		array
		(
			$local_port_info['name'],
			$remote_port_info['name'],
			$remote_object['dname'],
		)
	);
}

$msgcode['unlinkPortForObject']['OK'] = 9;
$msgcode['unlinkPortForObject']['ERR'] = 100;
function unlinkPortForObject ()
{
	assertUIntArg ('port_id');
	assertUIntArg ('remote_port_id');

	global $sic;
	$local_port_info = getPortInfo ($sic['port_id']);
	$remote_port_info = getPortInfo ($sic['remote_port_id']);
	$remote_object = spotEntity ('object', $remote_port_info['object_id']);
	$error = unlinkPort ($_REQUEST['port_id']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	return buildRedirectURL
	(
		__FUNCTION__,
		'OK',
		array
		(
			$local_port_info['name'],
			$remote_port_info['name'],
			$remote_object['dname'],
		)
	);
}

$msgcode['addMultiPorts']['OK'] = 10;
$msgcode['addMultiPorts']['ERR'] = 123;
function addMultiPorts ()
{
	assertStringArg ('format');
	assertStringArg ('input');
	assertUIntArg ('port_type');
	assertUIntArg ('object_id');
	$format = $_REQUEST['format'];
	$port_type = $_REQUEST['port_type'];
	$object_id = $_REQUEST['object_id'];
	// Input lines are escaped, so we have to explode and to chop by 2-char
	// \n and \r respectively.
	$lines1 = explode ('\n', $_REQUEST['input']);
	foreach ($lines1 as $line)
	{
		$parts = explode ('\r', $line);
		reset ($parts);
		if (!strlen ($parts[0]))
			continue;
		else
			$lines2[] = rtrim ($parts[0]);
	}
	$ports = array();
	foreach ($lines2 as $line)
	{
		switch ($format)
		{
			case 'fisxii':
				$words = explode (' ', preg_replace ('/[[:space:]]+/', ' ', $line));
				list ($slot, $port) = explode ('/', $words[0]);
				$ports[] = array
				(
					'name' => "e ${slot}/${port}",
					'l2address' => $words[8],
					'label' => "slot ${slot} port ${port}"
				);
				break;
			case 'c3600asy':
				$words = explode (' ', preg_replace ('/[[:space:]]+/', ' ', trim (substr ($line, 3))));
/*
How Async Lines are Numbered in Cisco 3600 Series Routers
http://www.cisco.com/en/US/products/hw/routers/ps274/products_tech_note09186a00801ca70b.shtml

Understanding 16- and 32-Port Async Network Modules
http://www.cisco.com/en/US/products/hw/routers/ps274/products_tech_note09186a00800a93f0.shtml
*/
				$async = $words[0];
				$slot = floor (($async - 1) / 32);
				$octalgroup = floor (($async - 1 - $slot * 32) / 8);
				$cable = $async - $slot * 32 - $octalgroup * 8;
				$og_label[0] = 'async 0-7';
				$og_label[1] = 'async 8-15';
				$og_label[2] = 'async 16-23';
				$og_label[3] = 'async 24-31';
				$ports[] = array
				(
					'name' => "async ${async}",
					'l2address' => '',
					'label' => "slot ${slot} " . $og_label[$octalgroup] . " cable ${cable}"
				);
				break;
			case 'fiwg':
				$words = explode (' ', preg_replace ('/[[:space:]]+/', ' ', $line));
				$ifnumber = $words[0] * 1;
				$ports[] = array
				(
					'name' => "e ${ifnumber}",
					'l2address' => "${words[8]}",
					'label' => "${ifnumber}"
				);
				break;
			case 'ssv1':
				$words = explode (' ', $line);
				if (!strlen ($words[0]) or !strlen ($words[1]))
					continue;
				$ports[] = array
				(
					'name' => $words[0],
					'l2address' => $words[1],
					'label' => ''
				);
				break;
			default:
				return buildRedirectURL (__FUNCTION__, 'ERR');
				break;
		}
	}
	// Create ports, if they don't exist.
	$added_count = $updated_count = $error_count = 0;
	foreach ($ports as $port)
	{
		$port_id = getPortID ($object_id, $port['name']);
		if ($port_id === NULL)
		{
			$result = commitAddPort ($object_id, $port['name'], $port_type, $port['label'], $port['l2address']);
			if ($result == '')
				$added_count++;
			else
				$error_count++;
		}
		else
		{
			$result = commitUpdatePort ($object_id, $port_id, $port['name'], $port_type, $port['label'], $port['l2address']);
			if ($result == '')
				$updated_count++;
			else
				$error_count++;
		}
	}
	return buildRedirectURL (__FUNCTION__, 'OK', array ($added_count, $updated_count, $error_count));
}

$msgcode['updIPv4Allocation']['OK'] = 12;
$msgcode['updIPv4Allocation']['ERR'] = 100;
function updIPv4Allocation ()
{
	assertIPv4Arg ('ip');
	assertUIntArg ('object_id');
	assertStringArg ('bond_name', TRUE);
	assertStringArg ('bond_type');

	$error = updateBond ($_REQUEST['ip'], $_REQUEST['object_id'], $_REQUEST['bond_name'], $_REQUEST['bond_type']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['delIPv4Allocation']['OK'] = 14;
$msgcode['delIPv4Allocation']['ERR'] = 100;
function delIPv4Allocation ()
{
	assertIPv4Arg ('ip');
	assertUIntArg ('object_id');

	$error = unbindIpFromObject ($_REQUEST['ip'], $_REQUEST['object_id']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addIPv4Allocation']['OK'] = 13;
$msgcode['addIPv4Allocation']['ERR1'] = 170;
$msgcode['addIPv4Allocation']['ERR2'] = 100;
function addIPv4Allocation ()
{
	assertIPv4Arg ('ip');
	assertUIntArg ('object_id');
	assertStringArg ('bond_name', TRUE);
	assertStringArg ('bond_type');

	// Strip masklen.
	$ip = preg_replace ('@/[[:digit:]]+$@', '', $_REQUEST['ip']);
	if  (getConfigVar ('IPV4_JAYWALK') != 'yes' and NULL === getIPv4AddressNetworkId ($ip))
		return buildRedirectURL (__FUNCTION__, 'ERR1', array ($ip));
	
	$error = bindIpToObject ($ip, $_REQUEST['object_id'], $_REQUEST['bond_name'], $_REQUEST['bond_type']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($error));
	$address = getIPv4Address ($ip);
	if ($address['reserved'] == 'yes' or strlen ($address['name']))
	{
		$release = getConfigVar ('IPV4_AUTO_RELEASE');
		if ($release >= 1)
			$address['reserved'] = 'no';
		if ($release >= 2)
			$address['name'] = '';
		updateAddress ($ip, $address['name'], $address['reserved']);
	}
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addIPv4Prefix']['OK'] = 23;
$msgcode['addIPv4Prefix']['ERR'] = 100;
$msgcode['addIPv4Prefix']['ERR1'] = 173;
$msgcode['addIPv4Prefix']['ERR2'] = 174;
$msgcode['addIPv4Prefix']['ERR3'] = 175;
$msgcode['addIPv4Prefix']['ERR4'] = 176;
function addIPv4Prefix ()
{
	assertStringArg ('range');
	assertStringArg ('name', TRUE);

	$is_bcast = isset ($_REQUEST['is_bcast']) ? $_REQUEST['is_bcast'] : 'off';
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	global $sic;
	$error = createIPv4Prefix ($_REQUEST['range'], $sic['name'], $is_bcast == 'on', $taglist);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['delIPv4Prefix']['OK'] = 24;
$msgcode['delIPv4Prefix']['ERR'] = 100;
function delIPv4Prefix ()
{
	assertUIntArg ('id');
	$error = destroyIPv4Prefix ($_REQUEST['id']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updIPv4Prefix']['OK'] = 25;
$msgcode['updIPv4Prefix']['ERR'] = 100;
function updIPv4Prefix ()
{
	assertUIntArg ('id');
	assertStringArg ('name', TRUE);
	assertStringArg ('comment', TRUE);
	global $sic;
	if (strlen ($error = updateIPv4Network_real ($sic['id'], $sic['name'], $sic['comment'])))
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['editAddress']['OK'] = 27;
$msgcode['editAddress']['ERR'] = 100;
function editAddress ()
{
	assertIPv4Arg ('ip');
	assertStringArg ('name', TRUE);

	if (isset ($_REQUEST['reserved']))
		$reserved = $_REQUEST['reserved'];
	else
		$reserved = 'off';
	$error = updateAddress ($_REQUEST['ip'], $_REQUEST['name'], $reserved == 'on' ? 'yes' : 'no');
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['createUser']['OK'] = 40;
$msgcode['createUser']['ERR'] = 102;
function createUser ()
{
	assertStringArg ('username');
	assertStringArg ('realname', TRUE);
	assertStringArg ('password');
	$username = $_REQUEST['username'];
	$password = sha1 ($_REQUEST['password']);
	$result = commitCreateUserAccount ($username, $_REQUEST['realname'], $password);
	if ($result != TRUE)
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($username));
	if (isset ($_REQUEST['taglist']))
		produceTagsForLastRecord ('user', $_REQUEST['taglist']);
	return buildRedirectURL (__FUNCTION__, 'OK', array ($username));
}

$msgcode['updateUser']['OK'] = 39;
$msgcode['updateUser']['ERR1'] = 103;
$msgcode['updateUser']['ERR1'] = 104;
function updateUser ()
{
	assertUIntArg ('user_id');
	assertStringArg ('username');
	assertStringArg ('realname', TRUE);
	assertStringArg ('password');
	$username = $_REQUEST['username'];
	$new_password = $_REQUEST['password'];
	if (NULL == ($userinfo = spotEntity ('user', $_REQUEST['user_id'])))
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	// Update user password only if provided password is not the same as current password hash.
	if ($new_password != $userinfo['user_password_hash'])
		$new_password = sha1 ($new_password);
	$result = commitUpdateUserAccount ($_REQUEST['user_id'], $username, $_REQUEST['realname'], $new_password);
	if ($result == TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK', array ($username));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($username));
}

$msgcode['updateDictionary']['OK'] = 51;
$msgcode['updateDictionary']['ERR'] = 109;
function updateDictionary ()
{
	assertUIntArg ('chapter_no');
	assertUIntArg ('dict_key');
	assertStringArg ('dict_value');
	if (commitUpdateDictionary ($_REQUEST['chapter_no'], $_REQUEST['dict_key'], $_REQUEST['dict_value']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['supplementDictionary']['OK'] = 52;
$msgcode['supplementDictionary']['ERR'] = 110;
function supplementDictionary ()
{
	assertUIntArg ('chapter_no');
	assertStringArg ('dict_value');
	if (commitSupplementDictionary ($_REQUEST['chapter_no'], $_REQUEST['dict_value']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['reduceDictionary']['OK'] = 50;
$msgcode['reduceDictionary']['ERR'] = 111;
function reduceDictionary ()
{
	assertUIntArg ('chapter_no');
	assertUIntArg ('dict_key');
	if (commitReduceDictionary ($_REQUEST['chapter_no'], $_REQUEST['dict_key']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['addChapter']['OK'] = 55;
$msgcode['addChapter']['ERR'] = 112;
function addChapter ()
{
	assertStringArg ('chapter_name');
	if (commitAddChapter ($_REQUEST['chapter_name']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['updateChapter']['OK'] = 54;
$msgcode['updateChapter']['ERR'] = 113;
function updateChapter ()
{
	assertUIntArg ('chapter_no');
	assertStringArg ('chapter_name');
	if (commitUpdateChapter ($_REQUEST['chapter_no'], $_REQUEST['chapter_name']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['delChapter']['OK'] = 53;
$msgcode['delChapter']['ERR'] = 114;
function delChapter ()
{
	assertUIntArg ('chapter_no');
	if (commitDeleteChapter ($_REQUEST['chapter_no']))
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['changeAttribute']['OK'] = 46;
$msgcode['changeAttribute']['ERR'] = 115;
function changeAttribute ()
{
	assertUIntArg ('attr_id');
	assertStringArg ('attr_name');
	if (commitUpdateAttribute ($_REQUEST['attr_id'], $_REQUEST['attr_name']))
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['createAttribute']['OK'] = 45;
$msgcode['createAttribute']['ERR'] = 116;
function createAttribute ()
{
	assertStringArg ('attr_name');
	assertStringArg ('attr_type');
	if (commitAddAttribute ($_REQUEST['attr_name'], $_REQUEST['attr_type']))
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['attr_name']));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['deleteAttribute']['OK'] = 47;
$msgcode['deleteAttribute']['ERR'] = 117;
function deleteAttribute ()
{
	assertUIntArg ('attr_id');
	if (commitDeleteAttribute ($_REQUEST['attr_id']))
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['supplementAttrMap']['OK'] = 48;
$msgcode['supplementAttrMap']['ERR1'] = 154;
$msgcode['supplementAttrMap']['ERR2'] = 118;
function supplementAttrMap ()
{
	assertUIntArg ('attr_id');
	assertUIntArg ('objtype_id');
	$attrMap = getAttrMap();
	if ($attrMap[$_REQUEST['attr_id']]['type'] != 'dict')
		$chapter_id = 'NULL';
	else
	{
		assertUIntArg ('chapter_no'); // FIXME: this doesn't fail on 0 (ticket:272)
		if (0 == ($chapter_id = $_REQUEST['chapter_no']))
			return buildRedirectURL (__FUNCTION__, 'ERR1', array ('chapter not selected'));
	}
	if (commitSupplementAttrMap ($_REQUEST['attr_id'], $_REQUEST['objtype_id'], $chapter_id) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR2');
}

$msgcode['reduceAttrMap']['OK'] = 49;
$msgcode['reduceAttrMap']['ERR'] = 119;
function reduceAttrMap ()
{
	assertUIntArg ('attr_id');
	assertUIntArg ('objtype_id');
	if (commitReduceAttrMap ($_REQUEST['attr_id'], $_REQUEST['objtype_id']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['clearSticker']['OK'] = 15;
$msgcode['clearSticker']['ERR'] = 120;
function clearSticker ()
{
	assertUIntArg ('attr_id');
	assertUIntArg ('object_id');
	if (commitResetAttrValue ($_REQUEST['object_id'], $_REQUEST['attr_id']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['updateObjectAllocation']['OK'] = 63;
function updateObjectAllocation ()
{
	assertUIntArg ('object_id');

	if (!isset ($_REQUEST['got_atoms']))
	{
		unset($_GET['page']);
		unset($_GET['tab']);
		unset($_GET['op']);
		unset($_POST['page']);
		unset($_POST['tab']);
		unset($_POST['op']);
		return buildWideRedirectURL (array(), NULL, NULL, array_merge ($_GET, $_POST));
	}
	$object_id = $_REQUEST['object_id'];
	$workingRacksData = array();
	foreach ($_REQUEST['rackmulti'] as $cand_id)
	{
		if (!isset ($workingRacksData[$cand_id]))
		{
			$rackData = spotEntity ('rack', $cand_id);
			amplifyCell ($rackData);
			$workingRacksData[$cand_id] = $rackData;
		}
	}

	foreach ($workingRacksData as &$rd)
		applyObjectMountMask ($rd, $object_id);

	$oldMolecule = getMoleculeForObject ($object_id);
	$changecnt = 0;
	$log = array();
	foreach ($workingRacksData as $rack_id => $rackData)
	{
		$logrecord = processGridForm ($rackData, 'F', 'T', $object_id);
		$log[] = $logrecord;
		if ($logrecord['code'] == 300)
			continue;
		$changecnt++;
		// Reload our working copy after form processing.
		$rackData = spotEntity ('rack', $cand_id);
		amplifyCell ($rackData);
		applyObjectMountMask ($rackData, $object_id);
		$workingRacksData[$rack_id] = $rackData;
	}
	if (!$changecnt)
		return buildRedirectURL (__FUNCTION__, 'OK', $changecnt);
	// Log a record.
	$newMolecule = getMoleculeForObject ($object_id);
	$oc = count ($oldMolecule);
	$nc = count ($newMolecule);
	$omid = $oc ? createMolecule ($oldMolecule) : 'NULL';
	$nmid = $nc ? createMolecule ($newMolecule) : 'NULL';
	global $remote_username;
	$comment = empty ($_REQUEST['comment']) ? 'NULL' : "'${_REQUEST['comment']}'";
	$query =
		"insert into MountOperation(object_id, old_molecule_id, new_molecule_id, user_name, comment) " .
		"values (${object_id}, ${omid}, ${nmid}, '${remote_username}', ${comment})";
	global $dbxlink;
	$result = $dbxlink->query ($query);
	if ($result == NULL)
		$log[] = array ('code' => 500, 'message' => 'SQL query failed during history logging.');
	else
		$log[] = array ('code' => 200, 'message' => 'History logged.');
	return buildWideRedirectURL ($log);
}

$msgcode['updateObject']['OK'] = 16;
$msgcode['updateObject']['ERR'] = 121;
function updateObject ()
{
	assertUIntArg ('num_attrs', TRUE);
	assertUIntArg ('object_id');
	assertUIntArg ('object_type_id');
	assertStringArg ('object_name', TRUE);
	assertStringArg ('object_label', TRUE);
	assertStringArg ('object_barcode', TRUE);
	assertStringArg ('object_asset_no', TRUE);
	if (isset ($_REQUEST['object_has_problems']) and $_REQUEST['object_has_problems'] == 'on')
		$has_problems = 'yes';
	else
		$has_problems = 'no';

	if (commitUpdateObject (
		$_REQUEST['object_id'],
		$_REQUEST['object_name'],
		$_REQUEST['object_label'],
		$_REQUEST['object_barcode'],
		$_REQUEST['object_type_id'],
		$has_problems,
		$_REQUEST['object_asset_no'],
		$_REQUEST['object_comment']
	) !== TRUE)
		return buildRedirectURL (__FUNCTION__, 'ERR');

	// Update optional attributes
	$oldvalues = getAttrValues ($_REQUEST['object_id']);
	$result = array();
	$num_attrs = isset ($_REQUEST['num_attrs']) ? $_REQUEST['num_attrs'] : 0;
	for ($i = 0; $i < $num_attrs; $i++)
	{
		assertUIntArg ("${i}_attr_id");
		$attr_id = $_REQUEST["${i}_attr_id"];

		// Field is empty, delete attribute and move on.
		if (!strlen ($_REQUEST["${i}_value"]))
		{
			commitResetAttrValue ($_REQUEST['object_id'], $attr_id);
			continue;
		}

		// The value could be uint/float, but we don't know ATM. Let SQL
		// server check this and complain.
		assertStringArg ("${i}_value");
		$value = $_REQUEST["${i}_value"];
		switch ($oldvalues[$attr_id]['type'])
		{
			case 'uint':
			case 'float':
			case 'string':
				$oldvalue = $oldvalues[$attr_id]['value'];
				break;
			case 'dict':
				$oldvalue = $oldvalues[$attr_id]['key'];
				break;
			default:
				throw new RuntimeException('Internal structure error');
		}
		if ($value === $oldvalue) // ('' == 0), but ('' !== 0)
			continue;

		// Note if the queries succeed or not, it determines which page they see.
		$result[] = commitUpdateAttrValue ($_REQUEST['object_id'], $attr_id, $value);
	}
	if (in_array (FALSE, $result))
		return buildRedirectURL (__FUNCTION__, 'ERR');

	// Invalidate thumb cache of all racks objects could occupy.
	foreach (getResidentRacksData ($_REQUEST['object_id'], FALSE) as $rack_id)
		resetThumbCache ($rack_id);

	return buildRedirectURL (__FUNCTION__, 'OK');
}


function addMultipleObjects()
{
	$log = emptyLog();
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	$max = getConfigVar ('MASSCOUNT');
	for ($i = 0; $i < $max; $i++)
	{
		if (!isset ($_REQUEST["${i}_object_type_id"]))
		{
			$log = mergeLogs ($log, oneLiner (184, array ($i + 1)));
			break;
		}
		assertUIntArg ("${i}_object_type_id", TRUE);
		assertStringArg ("${i}_object_name", TRUE);
		assertStringArg ("${i}_object_label", TRUE);
		assertStringArg ("${i}_object_asset_no", TRUE);
		assertStringArg ("${i}_object_barcode", TRUE);
		$name = $_REQUEST["${i}_object_name"];

		// It's better to skip silently, than to print a notice.
		if ($_REQUEST["${i}_object_type_id"] == 0)
			continue;
		if (($object_id = commitAddObject
		(
			$name,
			$_REQUEST["${i}_object_label"],
			$_REQUEST["${i}_object_barcode"],
			$_REQUEST["${i}_object_type_id"],
			$_REQUEST["${i}_object_asset_no"],
			$taglist
		)) !== FALSE){
			$info = spotEntity ('object', $object_id);
			// FIXME: employ amplifyCell() instead of calling loader functions directly
			amplifyCell ($info);
			$log = mergeLogs ($log, oneLiner (80, array ('<a href="' . makeHref (array ('page' => 'object', 'tab' => 'default', 'object_id' => $object_id)) . '">' . $info['dname'] . '</a>')));
		}else{
			$log = mergeLogs ($log, oneLiner (185, array ($name)));
		}
	}
	return buildWideRedirectURL ($log);
}

function addLotOfObjects()
{
	$log = emptyLog();
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	assertUIntArg ('global_type_id', TRUE);
	assertStringArg ('namelist', TRUE);
	$global_type_id = $_REQUEST['global_type_id'];
	if ($global_type_id == 0 or !strlen ($_REQUEST['namelist']))
		$log = mergeLogs ($log, oneLiner (186));
	else
	{
		// The name extractor below was stolen from ophandlers.php:addMultiPorts()
		$names1 = explode ('\n', $_REQUEST['namelist']);
		$names2 = array();
		foreach ($names1 as $line)
		{
			$parts = explode ('\r', $line);
			reset ($parts);
			if (!strlen ($parts[0]))
				continue;
			else
				$names2[] = rtrim ($parts[0]);
		}
		foreach ($names2 as $name)
			if (($object_id = commitAddObject ($name, '', '', $global_type_id, '', $taglist)) !== FALSE)
			{
				$info = spotEntity ('object', $object_id);
				amplifyCell ($info);
				$log = mergeLogs ($log, oneLiner (80, array ('<a href="' . makeHref (array ('page' => 'object', 'tab' => 'default', 'object_id' => $object_id)) . '">' . $info['dname'] . '</a>')));
			}
			else
				$log = mergeLogs ($log, oneLiner (185, array ($name)));
	}
	return buildWideRedirectURL ($log);
}

$msgcode['deleteObject']['OK'] = 76;
$msgcode['deleteObject']['ERR'] = 100;
function deleteObject ()
{
	assertUIntArg ('object_id');
	if (NULL === ($oinfo = spotEntity ('object', $_REQUEST['object_id'])))
		return buildRedirectURL (__FUNCTION__, 'ERR', array ('object not found'));

	$racklist = getResidentRacksData ($_REQUEST['object_id'], FALSE);
	$error = commitDeleteObject ($_REQUEST['object_id']);
	foreach ($racklist as $rack_id)
		resetThumbCache ($rack_id);

	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));

	return buildRedirectURL (__FUNCTION__, 'OK', array ($oinfo['dname']));
}

$msgcode['useupPort']['OK'] = 11;
$msgcode['useupPort']['ERR'] = 124;
function useupPort ()
{
	assertUIntArg ('port_id');
	if (commitUseupPort ($_REQUEST['port_id']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['updateUI']['OK'] = 56;
$msgcode['updateUI']['ERR'] = 125;
function updateUI ()
{
	assertUIntArg ('num_vars');

	for ($i = 0; $i < $_REQUEST['num_vars']; $i++)
	{
		assertStringArg ("${i}_varname");
		assertStringArg ("${i}_varvalue", TRUE);
		$varname = $_REQUEST["${i}_varname"];
		$varvalue = $_REQUEST["${i}_varvalue"];

		// If form value = value in DB, don't bother updating DB
		if (!isConfigVarChanged($varname, $varvalue))
			continue;

		// Note if the queries succeed or not, it determines which page they see.
		try {
			setConfigVar ($varname, $varvalue, TRUE);
		} catch (InvalidArgException $e) {
			return buildRedirectURL (__FUNCTION__, 'ERR', array ($e->getMessage()));
		}
	}
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['saveMyPreferences']['OK'] = 56;
$msgcode['saveMyPreferences']['ERR'] = 125;
function saveMyPreferences ()
{
	assertUIntArg ('num_vars');

	for ($i = 0; $i < $_REQUEST['num_vars']; $i++)
	{
		assertStringArg ("${i}_varname");
		assertStringArg ("${i}_varvalue", TRUE);
		$varname = $_REQUEST["${i}_varname"];
		$varvalue = $_REQUEST["${i}_varvalue"];

		// If form value = value in DB, don't bother updating DB
		if (!isConfigVarChanged($varname, $varvalue))
			continue;
		// Note if the queries succeed or not, it determines which page they see.
		try {
			setUserConfigVar ($varname, $varvalue);
		} catch (InvalidArgException $e) {
			return buildRedirectURL (__FUNCTION__, 'ERR', array ($e->getMessage()));
		}
	}
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['resetMyPreference']['OK'] = 56;
$msgcode['resetMyPreference']['ERR'] = 125;
function resetMyPreference ()
{
	assertStringArg ("varname");
	$varname = $_REQUEST["varname"];

	try {
		resetUserConfigVar ($varname);
	} catch (InvalidArgException $e) {
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($e->getMessage()));
	}
	return buildRedirectURL (__FUNCTION__, 'OK');
}




$msgcode['resetUIConfig']['OK'] = 57;
function resetUIConfig()
{
	setConfigVar ('MASSCOUNT','15');
	setConfigVar ('MAXSELSIZE','30');
	setConfigVar ('ROW_SCALE','2');
	setConfigVar ('PORTS_PER_ROW','12');
	setConfigVar ('IPV4_ADDRS_PER_PAGE','256');
	setConfigVar ('DEFAULT_RACK_HEIGHT','42');
	setConfigVar ('DEFAULT_SLB_VS_PORT','');
	setConfigVar ('DEFAULT_SLB_RS_PORT','');
	setConfigVar ('DETECT_URLS','no');
	setConfigVar ('RACK_PRESELECT_THRESHOLD','1');
	setConfigVar ('DEFAULT_IPV4_RS_INSERVICE','no');
	setConfigVar ('AUTOPORTS_CONFIG','4 = 1*33*kvm + 2*24*eth%u;15 = 1*446*kvm');
	setConfigVar ('SHOW_EXPLICIT_TAGS','yes');
	setConfigVar ('SHOW_IMPLICIT_TAGS','yes');
	setConfigVar ('SHOW_AUTOMATIC_TAGS','no');
	setConfigVar ('DEFAULT_OBJECT_TYPE','4');
	setConfigVar ('IPV4_AUTO_RELEASE','1');
	setConfigVar ('SHOW_LAST_TAB', 'no');
	setConfigVar ('EXT_IPV4_VIEW', 'yes');
	setConfigVar ('TREE_THRESHOLD', '25');
	setConfigVar ('IPV4_JAYWALK', 'no');
	setConfigVar ('ADDNEW_AT_TOP', 'yes');
	setConfigVar ('IPV4_TREE_SHOW_USAGE', 'yes');
	setConfigVar ('PREVIEW_TEXT_MAXCHARS', '10240');
	setConfigVar ('PREVIEW_TEXT_ROWS', '25');
	setConfigVar ('PREVIEW_TEXT_COLS', '80');
	setConfigVar ('PREVIEW_IMAGE_MAXPXS', '320');
	setConfigVar ('VENDOR_SIEVE', '');
	setConfigVar ('IPV4LB_LISTSRC', '{$typeid_4}');
	setConfigVar ('IPV4OBJ_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8} or {$typeid_12} or {$typeid_445} or {$typeid_447} or {$typeid_798}');
	setConfigVar ('IPV4NAT_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8} or {$typeid_798}');
	setConfigVar ('ASSETWARN_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8}');
	setConfigVar ('NAMEWARN_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8}');
	setConfigVar ('RACKS_PER_ROW','12');
	setConfigVar ('FILTER_PREDICATE_SIEVE','');
	setConfigVar ('FILTER_DEFAULT_ANDOR','or');
	setConfigVar ('FILTER_SUGGEST_ANDOR','yes');
	setConfigVar ('FILTER_SUGGEST_TAGS','yes');
	setConfigVar ('FILTER_SUGGEST_PREDICATES','yes');
	setConfigVar ('FILTER_SUGGEST_EXTRA','no');
	setConfigVar ('DEFAULT_SNMP_COMMUNITY','public');
	setConfigVar ('IPV4_ENABLE_KNIGHT','yes');
	setConfigVar ('TAGS_TOPLIST_SIZE','50');
	setConfigVar ('TAGS_QUICKLIST_SIZE','20');
	setConfigVar ('TAGS_QUICKLIST_THRESHOLD','50');
	setConfigVar ('ENABLE_MULTIPORT_FORM', 'no');
	setConfigVar ('DEFAULT_PORT_IIF_ID', '1');
	setConfigVar ('DEFAULT_PORT_OIF_IDS', '1=24; 3=1078; 4=1077; 5=1079; 6=1080; 8=1082; 9=1084');
	setConfigVar ('IPV4_TREE_RTR_AS_CELL', 'yes');
	setConfigVar ('PROXIMITY_RANGE', 0);
	setConfigVar ('IPV4_TREE_SHOW_VLAN', 'yes');
	setConfigVar ('DEFAULT_VDOM_ID', '');
	setConfigVar ('DEFAULT_VST_ID', '');
	setConfigVar ('8021Q_PULL_AROUND_CONFLICTS', 'yes');
	setConfigVar ('8021Q_PUSH_AROUND_CONFLICTS', 'yes');
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addRealServer']['OK'] = 34;
$msgcode['addRealServer']['ERR'] = 126;
// Add single record.
function addRealServer ()
{
	assertUIntArg ('pool_id');
	assertIPv4Arg ('remoteip');
	assertStringArg ('rsport', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (!addRStoRSPool (
		$_REQUEST['pool_id'],
		$_REQUEST['remoteip'],
		$_REQUEST['rsport'],
		getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'),
		$_REQUEST['rsconfig']
	))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addRealServers']['OK'] = 37;
$msgcode['addRealServers']['ERR1'] = 131;
$msgcode['addRealServers']['ERR2'] = 127;
// Parse textarea submitted and try adding a real server for each line.
function addRealServers ()
{
	assertUIntArg ('pool_id');
	assertStringArg ('format');
	assertStringArg ('rawtext');
	$rawtext = str_replace ('\r', '', $_REQUEST['rawtext']);
	$ngood = $nbad = 0;
	$rsconfig = '';
	// Keep in mind, that the text will have HTML entities (namely '>') escaped.
	foreach (explode ('\n', $rawtext) as $line)
	{
		if (!strlen ($line))
			continue;
		$match = array ();
		switch ($_REQUEST['format'])
		{
			case 'ipvs_2': // address and port only
				if (!preg_match ('/^  -&gt; ([0-9\.]+):([0-9]+) /', $line, $match))
					continue;
				if (addRStoRSPool ($_REQUEST['pool_id'], $match[1], $match[2], getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), ''))
					$ngood++;
				else
					$nbad++;
				break;
			case 'ipvs_3': // address, port and weight
				if (!preg_match ('/^  -&gt; ([0-9\.]+):([0-9]+) +[a-zA-Z]+ +([0-9]+) /', $line, $match))
					continue;
				if (addRStoRSPool ($_REQUEST['pool_id'], $match[1], $match[2], getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), 'weight ' . $match[3]))
					$ngood++;
				else
					$nbad++;
				break;
			case 'ssv_2': // IP address and port
				if (!preg_match ('/^([0-9\.]+) ([0-9]+)$/', $line, $match))
					continue;
				if (addRStoRSPool ($_REQUEST['pool_id'], $match[1], $match[2], getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), ''))
					$ngood++;
				else
					$nbad++;
				break;
			case 'ssv_1': // IP address
				if (!preg_match ('/^([0-9\.]+)$/', $line, $match))
					continue;
				if (addRStoRSPool ($_REQUEST['pool_id'], $match[1], 0, getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), ''))
					$ngood++;
				else
					$nbad++;
				break;
			default:
				return buildRedirectURL (__FUNCTION__, 'ERR1');
				break;
		}
	}
	if ($nbad == 0 and $ngood > 0)
		return buildRedirectURL (__FUNCTION__, 'OK', array ($ngood));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($ngood, $nbad));
}

$msgcode['addVService']['OK'] = 28;
$msgcode['addVService']['ERR1'] = 132;
$msgcode['addVService']['ERR2'] = 100;
function addVService ()
{
	assertIPv4Arg ('vip');
	assertUIntArg ('vport');
	assertStringArg ('proto');
	if ($_REQUEST['proto'] != 'TCP' and $_REQUEST['proto'] != 'UDP')
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	$error = commitCreateVS
	(
		$_REQUEST['vip'],
		$_REQUEST['vport'],
		$_REQUEST['proto'],
		$_REQUEST['name'],
		$_REQUEST['vsconfig'],
		$_REQUEST['rsconfig'],
		isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array()
	);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['deleteRealServer']['OK'] = 35;
$msgcode['deleteRealServer']['ERR'] = 128;
function deleteRealServer ()
{
	assertUIntArg ('id');
	if (!commitDeleteRS ($_REQUEST['id']))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['deleteLoadBalancer']['OK'] = 19;
$msgcode['deleteLoadBalancer']['ERR'] = 129;
function deleteLoadBalancer ()
{
	assertUIntArg ('object_id');
	assertUIntArg ('pool_id');
	assertUIntArg ('vs_id');
	if (!commitDeleteLB (
		$_REQUEST['object_id'],
		$_REQUEST['pool_id'],
		$_REQUEST['vs_id']
	))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['deleteVService']['OK'] = 29;
$msgcode['deleteVService']['ERR'] = 130;
function deleteVService ()
{
	assertUIntArg ('vs_id');
	if (!commitDeleteVS ($_REQUEST['vs_id']))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateRealServer']['OK'] = 36;
$msgcode['updateRealServer']['ERR'] = 133;
function updateRealServer ()
{
	assertUIntArg ('rs_id');
	assertIPv4Arg ('rsip');
	assertStringArg ('rsport', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (!commitUpdateRS (
		$_REQUEST['rs_id'],
		$_REQUEST['rsip'],
		$_REQUEST['rsport'],
		$_REQUEST['rsconfig']
	))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateLoadBalancer']['OK'] = 20;
$msgcode['updateLoadBalancer']['ERR'] = 134;
function updateLoadBalancer ()
{
	assertUIntArg ('object_id');
	assertUIntArg ('pool_id');
	assertUIntArg ('vs_id');
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (!commitUpdateLB (
		$_REQUEST['object_id'],
		$_REQUEST['pool_id'],
		$_REQUEST['vs_id'],
		$_REQUEST['vsconfig'],
		$_REQUEST['rsconfig']
	))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateVService']['OK'] = 30;
$msgcode['updateVService']['ERR'] = 135;
function updateVService ()
{
	assertUIntArg ('vs_id');
	assertIPv4Arg ('vip');
	assertUIntArg ('vport');
	assertStringArg ('proto');
	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (!commitUpdateVS (
		$_REQUEST['vs_id'],
		$_REQUEST['vip'],
		$_REQUEST['vport'],
		$_REQUEST['proto'],
		$_REQUEST['name'],
		$_REQUEST['vsconfig'],
		$_REQUEST['rsconfig']
	))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addLoadBalancer']['OK'] = 18;
$msgcode['addLoadBalancer']['ERR'] = 137;
function addLoadBalancer ()
{
	assertUIntArg ('pool_id');
	assertUIntArg ('object_id');
	assertUIntArg ('vs_id');
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (!addLBtoRSPool (
		$_REQUEST['pool_id'],
		$_REQUEST['object_id'],
		$_REQUEST['vs_id'],
		$_REQUEST['vsconfig'],
		$_REQUEST['rsconfig']
	))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addRSPool']['OK'] = 31;
$msgcode['addRSPool']['ERR'] = 100;
function addRSPool ()
{
	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	$error = commitCreateRSPool
	(
		$_REQUEST['name'],
		$_REQUEST['vsconfig'],
		$_REQUEST['rsconfig'],
		isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array()
	);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['deleteRSPool']['OK'] = 32;
$msgcode['deleteRSPool']['ERR'] = 138;
function deleteRSPool ()
{
	assertUIntArg ('pool_id');
	if (!commitDeleteRSPool ($_REQUEST['pool_id']))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateRSPool']['OK'] = 33;
$msgcode['updateRSPool']['ERR'] = 139;
function updateRSPool ()
{
	assertUIntArg ('pool_id');
	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (!commitUpdateRSPool ($_REQUEST['pool_id'], $_REQUEST['name'], $_REQUEST['vsconfig'], $_REQUEST['rsconfig']))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateRSInService']['OK'] = 38;
$msgcode['updateRSInService']['ERR'] = 140;
function updateRSInService ()
{
	assertUIntArg ('rscount');
	$pool_id = $_REQUEST['pool_id'];
	$orig = spotEntity ('ipv4rspool', $pool_id);
	amplifyCell ($orig);
	$nbad = $ngood = 0;
	for ($i = 1; $i <= $_REQUEST['rscount']; $i++)
	{
		$rs_id = $_REQUEST["rsid_${i}"];
		if (isset ($_REQUEST["inservice_${i}"]) and $_REQUEST["inservice_${i}"] == 'on')
			$newval = 'yes';
		else
			$newval = 'no';
		if ($newval != $orig['rslist'][$rs_id]['inservice'])
		{
			if (commitSetInService ($rs_id, $newval))
				$ngood++;
			else
				$nbad++;
		}
	}
	if (!$nbad)
		return buildRedirectURL (__FUNCTION__, 'OK', array ($ngood));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($nbad, $ngood));
}

$msgcode['importPTRData']['OK'] = 26;
$msgcode['importPTRData']['ERR'] = 141;
// FIXME: check, that each submitted address belongs to the prefix we
// are operating on.
function importPTRData ()
{
	assertUIntArg ('addrcount');
	$nbad = $ngood = 0;
	for ($i = 0; $i < $_REQUEST['addrcount']; $i++)
	{
		$inputname = "import_${i}";
		if (!isset ($_REQUEST[$inputname]) or $_REQUEST[$inputname] != 'on')
			continue;
		assertIPv4Arg ("addr_${i}");
		assertStringArg ("descr_${i}", TRUE);
		assertStringArg ("rsvd_${i}");
		// Non-existent addresses will not have this argument set in request.
		$rsvd = 'no';
		if ($_REQUEST["rsvd_${i}"] == 'yes')
			$rsvd = 'yes';
		if (updateAddress ($_REQUEST["addr_${i}"], $_REQUEST["descr_${i}"], $rsvd) == '')
			$ngood++;
		else
			$nbad++;
	}
	if (!$nbad)
		return buildRedirectURL (__FUNCTION__, 'OK', array ($ngood));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($nbad, $ngood));
}

$msgcode['generateAutoPorts']['OK'] = 21;
$msgcode['generateAutoPorts']['ERR'] = 142;
function generateAutoPorts ()
{
	global $pageno;
	assertUIntArg ('object_id');
	$info = spotEntity ('object', $_REQUEST['object_id']);
	// Navigate away in case of success, stay at the place otherwise.
	if (executeAutoPorts ($_REQUEST['object_id'], $info['objtype_id']))
		return buildRedirectURL (__FUNCTION__, 'OK', array(), $pageno, 'ports');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['saveEntityTags']['OK'] = 22;
$msgcode['saveEntityTags']['ERR1'] = 143;
$msgcode['saveEntityTags']['ERR2'] = 187;
// Filter out implicit tags before storing the new tag set.
function saveEntityTags ()
{
	global $page, $pageno, $etype_by_pageno;
	if (!isset ($etype_by_pageno[$pageno]) or !isset ($page[$pageno]['bypass']))
		return buildRedirectURL (__FUNCTION__, 'ERR2', array (__FUNCTION__));
	$realm = $etype_by_pageno[$pageno];
	$bypass = $page[$pageno]['bypass'];
	assertUIntArg ($bypass);
	$entity_id = $_REQUEST[$bypass];
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	// Build a chain from the submitted data, minimize it,
	// then wipe existing records and store the new set instead.
	destroyTagsForEntity ($realm, $entity_id);
	// TODO: these actions are very close to what rebuildTagChainForEntity() does,
	// so why not use it?
	$newchain = getExplicitTagsOnly (buildTagChainFromIds ($taglist));
	$n_succeeds = $n_errors = 0;
	foreach ($newchain as $taginfo)
		if (addTagForEntity ($realm, $entity_id, $taginfo['id']))
			$n_succeeds++;
		else
			$n_errors++;
	if ($n_errors)
		return buildRedirectURL (__FUNCTION__, 'ERR1', array ($n_succeeds, $n_errors));
	else
		return buildRedirectURL (__FUNCTION__, 'OK', array ($n_succeeds));
}

$msgcode['destroyTag']['OK'] = 58;
$msgcode['destroyTag']['ERR1'] = 183;
$msgcode['destroyTag']['ERR2'] = 144;
function destroyTag ()
{
	assertUIntArg ('tag_id');
	global $taglist;
	if (!isset ($taglist[$_REQUEST['tag_id']]))
		return buildRedirectURL (__FUNCTION__, 'ERR1', array ($_REQUEST['tag_id']));
	if (($ret = commitDestroyTag ($_REQUEST['tag_id'])) == '')
		return buildRedirectURL (__FUNCTION__, 'OK', array ($taglist[$_REQUEST['tag_id']]['tag']));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR2');
}

$msgcode['createTag']['OK'] = 59;
$msgcode['createTag']['ERR1'] = 145;
$msgcode['createTag']['ERR3'] = 147;
function createTag ()
{
	assertStringArg ('tag_name');
	assertUIntArg ('parent_id', TRUE);
	$tagname = trim ($_REQUEST['tag_name']);
	if (!validTagName ($tagname))
		return buildRedirectURL (__FUNCTION__, 'ERR1', array ($tagname));
	if (($parent_id = $_REQUEST['parent_id']) <= 0)
		$parent_id = 'NULL';
	if (($ret = commitCreateTag ($tagname, $parent_id)) == '')
		return buildRedirectURL (__FUNCTION__, 'OK', array ($tagname));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR3', array ($tagname, $ret));
}

$msgcode['updateTag']['OK'] = 60;
$msgcode['updateTag']['ERR1'] = 145;
$msgcode['updateTag']['ERR2'] = 148;
function updateTag ()
{
	assertUIntArg ('tag_id');
	assertUIntArg ('parent_id', TRUE);
	assertStringArg ('tag_name');
	$tagname = trim ($_REQUEST['tag_name']);
	if (!validTagName ($tagname))
		return buildRedirectURL (__FUNCTION__, 'ERR1', array ($tagname));
	if (($parent_id = $_REQUEST['parent_id']) <= 0)
		$parent_id = 'NULL';
	if (($ret = commitUpdateTag ($_REQUEST['tag_id'], $tagname, $parent_id)) == '')
		return buildRedirectURL (__FUNCTION__, 'OK', array ($tagname));
	// Use old name in the message, cause update failed.
	global $taglist;
	return buildRedirectURL (__FUNCTION__, 'ERR2', array ($taglist[$_REQUEST['tag_id']]['tag'], $ret));
}

$msgcode['rollTags']['OK'] = 67;
$msgcode['rollTags']['ERR'] = 149;
function rollTags ()
{
	assertUIntArg ('row_id');
	assertStringArg ('sum', TRUE);
	assertUIntArg ('realsum');
	if ($_REQUEST['sum'] != $_REQUEST['realsum'])
		return buildRedirectURL (__FUNCTION__, 'ERR');
	// Even if the user requested an empty tag list, don't bail out, but process existing
	// tag chains with "zero" extra. This will make sure, that the stuff processed will
	// have its chains refined to "normal" form.
	$extratags = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	$n_ok = 0;
	// Minimizing the extra chain early, so that tag rebuilder doesn't have to
	// filter out the same tag again and again. It will have own noise to cancel.
	$extrachain = getExplicitTagsOnly (buildTagChainFromIds ($extratags));
	foreach (listCells ('rack', $_REQUEST['row_id']) as $rack)
	{
		if (rebuildTagChainForEntity ('rack', $rack['id'], $extrachain))
			$n_ok++;
		amplifyCell ($rack);
		foreach ($rack['mountedObjects'] as $object_id)
			if (rebuildTagChainForEntity ('object', $object_id, $extrachain))
				$n_ok++;
	}
	return buildRedirectURL (__FUNCTION__, 'OK', array ($n_ok));
}

$msgcode['changeMyPassword']['OK'] = 61;
$msgcode['changeMyPassword']['ERR1'] = 150;
$msgcode['changeMyPassword']['ERR2'] = 151;
$msgcode['changeMyPassword']['ERR3'] = 152;
$msgcode['changeMyPassword']['ERR4'] = 153;
function changeMyPassword ()
{
	global $remote_username, $user_auth_src;
	if ($user_auth_src != 'database')
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	assertStringArg ('oldpassword');
	assertStringArg ('newpassword1');
	assertStringArg ('newpassword2');
	$remote_userid = getUserIDByUsername ($remote_username);
	$userinfo = spotEntity ('user', $remote_userid);
	if ($userinfo['user_password_hash'] != sha1 ($_REQUEST['oldpassword']))
		return buildRedirectURL (__FUNCTION__, 'ERR2');
	if ($_REQUEST['newpassword1'] != $_REQUEST['newpassword2'])
		return buildRedirectURL (__FUNCTION__, 'ERR3');
	if (commitUpdateUserAccount ($remote_userid, $userinfo['user_name'], $userinfo['user_realname'], sha1 ($_REQUEST['newpassword1'])))
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR4');
}

$msgcode['saveRackCode']['OK'] = 43;
$msgcode['saveRackCode']['ERR1'] = 154;
$msgcode['saveRackCode']['ERR2'] = 155;
function saveRackCode ()
{
	assertStringArg ('rackcode');
	// For the test to succeed, unescape LFs, strip CRs.
	$newcode = str_replace ('\r', '', str_replace ('\n', "\n", $_REQUEST['rackcode']));
	$parseTree = getRackCode ($newcode);
	if ($parseTree['result'] != 'ACK')
		return buildRedirectURL (__FUNCTION__, 'ERR1', array ($parseTree['load']));
	saveScript ('RackCodeCache', '');
	if (saveScript ('RackCode', $newcode))
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR2');
}

$msgcode['setPortVLAN']['ERR1'] = 156;
// This handler's context is pre-built, but not authorized. It is assumed, that the
// handler will take existing context and before each commit check authorization
// on the base chain plus necessary context added.
function setPortVLAN ()
{
	assertUIntArg ('portcount');
	$data = getSwitchVLANs ($_REQUEST['object_id']);
	if ($data === NULL)
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	list ($vlanlist, $portlist) = $data;
	// Here we just build up 1 set command for the gateway with all of the ports
	// included. The gateway is expected to filter unnecessary changes silently
	// and to provide a list of responses with either error or success message
	// for each of the rest.
	$nports = $_REQUEST['portcount'];
	$prefix = 'set ';
	$log = emptyLog();
	$setcmd = '';
	for ($i = 0; $i < $nports; $i++)
		if
		(
			!isset ($_REQUEST['portname_' . $i]) ||
			!isset ($_REQUEST['vlanid_' . $i]) ||
			$_REQUEST['portname_' . $i] != $portlist[$i]['portname']
		)
			$log['m'][] = array ('c' => 158, 'a' => array ($i));
		elseif
		(
			$_REQUEST['vlanid_' . $i] == $portlist[$i]['vlanid'] ||
			$portlist[$i]['vlaind'] == 'TRUNK'
		)
			continue;
		else
		{
			$portname = $_REQUEST['portname_' . $i];
			$oldvlanid = $portlist[$i]['vlanid'];
			$newvlanid = $_REQUEST['vlanid_' . $i];
			// Finish the security context and evaluate it.
			$annex = array();
			$annex[] = array ('tag' => '$fromvlan_' . $oldvlanid);
			$annex[] = array ('tag' => '$tovlan_' . $newvlanid);
			if (!permitted (NULL, NULL, NULL, $annex))
			{
				$log['m'][] = array ('c' => 159, 'a' => array ($portname, $oldvlanid, $newvlanid));
				continue;
			}
			$setcmd .= $prefix . $portname . '=' . $newvlanid;
			$prefix = ';';
		}
	// Feed the gateway and interpret its (non)response.
	if ($setcmd != '')
		$log['m'] = array_merge ($log['m'], setSwitchVLANs ($_REQUEST['object_id'], $setcmd));
	else
		$log['m'][] = array ('c' => 201);
	return buildWideRedirectURL ($log);
}

function submitSLBConfig ()
{
	assertUIntArg ('object_id');
	$newconfig = buildLVSConfig ($_REQUEST['object_id']);
	$msglog = gwSendFileToObject ($_REQUEST['object_id'], 'slbconfig', html_entity_decode ($newconfig, ENT_QUOTES, 'UTF-8'));
	return buildWideRedirectURL ($msglog);
}

$msgcode['addRow']['OK'] = 74;
$msgcode['addRow']['ERR'] = 100;
function addRow ()
{
	assertStringArg ('name');

	if (commitAddRow ($_REQUEST['name']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['name']));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($_REQUEST['name']));
}

$msgcode['updateRow']['OK'] = 75;
$msgcode['updateRow']['ERR'] = 100;
function updateRow ()
{
	assertUIntArg ('row_id');
	assertStringArg ('name');

	if (TRUE === commitUpdateRow ($_REQUEST['row_id'], $_REQUEST['name']))
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['name']));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($_REQUEST['name']));
}

$msgcode['deleteRow']['OK'] = 77;
$msgcode['deleteRow']['ERR'] = 100;
function deleteRow ()
{
	assertUIntArg ('row_id');
	$rowinfo = getRackRowInfo ($_REQUEST['row_id']);

	if (TRUE === commitDeleteRow ($_REQUEST['row_id']))
		return buildRedirectURL (__FUNCTION__, 'OK', array ($rowinfo['name']));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($rowinfo['name']));
}

$msgcode['addRack']['OK'] = 65;
$msgcode['addRack']['ERR1'] = 171;
$msgcode['addRack']['ERR2'] = 172;
function addRack ()
{
	assertUIntArg ('row_id');
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	if (isset ($_REQUEST['got_data']))
	{
		assertStringArg ('rack_name');
		assertUIntArg ('rack_height1');
		assertStringArg ('rack_comment', TRUE);

		if (commitAddRack ($_REQUEST['rack_name'], $_REQUEST['rack_height1'], $_REQUEST['row_id'], $_REQUEST['rack_comment'], $taglist) === TRUE)
			return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['rack_name']));
		else
			return buildRedirectURL (__FUNCTION__, 'ERR1', array ($_REQUEST['rack_name']));
	}
	elseif (isset ($_REQUEST['got_mdata']))
	{
		assertUIntArg ('rack_height2');
		assertStringArg ('rack_names', TRUE);
		$log = emptyLog();
		// copy-and-paste from renderAddMultipleObjectsForm()
		$names1 = explode ('\n', $_REQUEST['rack_names']);
		$names2 = array();
		foreach ($names1 as $line)
		{
			$parts = explode ('\r', $line);
			reset ($parts);
			if (!strlen ($parts[0]))
				continue;
			else
				$names2[] = rtrim ($parts[0]);
		}
		global $msgcode;
		foreach ($names2 as $cname)
			if (commitAddRack ($cname, $_REQUEST['rack_height2'], $_REQUEST['row_id'], '', $taglist) === TRUE)
				$log['m'][] = array ('c' => $msgcode[__FUNCTION__]['OK'], 'a' => array ($cname));
			else
				$log['m'][] = array ('c' => $msgcode[__FUNCTION__]['ERR1'], 'a' => array ($cname));
		return buildWideRedirectURL ($log);
	}
	else
		return buildRedirectURL (__FUNCTION__, 'ERR2');
}

$msgcode['deleteRack']['OK'] = 79;
$msgcode['deleteRack']['ERR'] = 100;
$msgcode['deleteRack']['ERR1'] = 206;
function deleteRack ()
{
	assertUIntArg ('rack_id');
	if (NULL == ($rackData = spotEntity ('rack', $_REQUEST['rack_id'])))
		return buildRedirectURL (__FUNCTION__, 'ERR', array ('Rack not found'), 'rackspace', 'default');
	amplifyCell ($rackData);
	if (count ($rackData['mountedObjects']))
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	if (TRUE !== commitDeleteRack ($_REQUEST['rack_id']))
		return buildRedirectURL (__FUNCTION__, 'ERR', array(), 'rackspace', 'default');
	return buildRedirectURL (__FUNCTION__, 'OK', array ($rackData['name']), 'rackspace', 'default');
}

$msgcode['updateRack']['OK'] = 68;
$msgcode['updateRack']['ERR'] = 177;
function updateRack ()
{
	assertUIntArg ('rack_id');
	assertUIntArg ('rack_row_id');
	assertUIntArg ('rack_height');
	assertStringArg ('rack_name');
	assertStringArg ('rack_comment', TRUE);

	resetThumbCache ($_REQUEST['rack_id']);
	if (TRUE === commitUpdateRack ($_REQUEST['rack_id'], $_REQUEST['rack_name'], $_REQUEST['rack_height'], $_REQUEST['rack_row_id'], $_REQUEST['rack_comment']))
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['rack_name']));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['updateRackDesign']['ERR'] = 100;
function updateRackDesign ()
{
	assertUIntArg ('rack_id');
	if (NULL == ($rackData = spotEntity ('rack', $_REQUEST['rack_id'])))
		return buildRedirectURL (__FUNCTION__, 'ERR', array ('Rack not found'), 'rackspace', 'default');
	amplifyCell ($rackData);
	applyRackDesignMask($rackData);
	markupObjectProblems ($rackData);
	$response = processGridForm ($rackData, 'A', 'F');
	return buildWideRedirectURL (array($response));
}

$msgcode['updateRackProblems']['ERR'] = 100;
function updateRackProblems ()
{
	assertUIntArg ('rack_id');
	if (NULL == ($rackData = spotEntity ('rack', $_REQUEST['rack_id'])))
		return buildRedirectURL (__FUNCTION__, 'ERR', array ('Rack not found'), 'rackspace', 'default');
	amplifyCell ($rackData);
	applyRackProblemMask($rackData);
	markupObjectProblems ($rackData);
	$response = processGridForm ($rackData, 'F', 'U');
	return buildWideRedirectURL (array($response));
}

function querySNMPData ()
{
	assertUIntArg ('object_id');
	assertStringArg ('community');
	return doSNMPmining ($_REQUEST['object_id'], $_REQUEST['community']);
}

$msgcode['addFileWithoutLink']['OK'] = 69;
$msgcode['addFileWithoutLink']['ERR'] = 100;
// File-related functions
function addFileWithoutLink ()
{
	assertStringArg ('comment', TRUE);

	// Make sure the file can be uploaded
	if (get_cfg_var('file_uploads') != 1)
		return buildRedirectURL (__FUNCTION__, 'ERR', array ("file uploads not allowed, change 'file_uploads' parameter in php.ini"));

	$fp = fopen($_FILES['file']['tmp_name'], 'rb');
	global $sic;
	// commitAddFile() uses prepared statements
	$error = commitAddFile ($_FILES['file']['name'], $_FILES['file']['type'], $_FILES['file']['size'], $fp, $sic['comment']);
	if (isset ($_REQUEST['taglist']))
		produceTagsForLastRecord ('file', $_REQUEST['taglist']);

	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));

	return buildRedirectURL (__FUNCTION__, 'OK', array ($_FILES['file']['name']));
}

$msgcode['addFileToEntity']['OK'] = 69;
$msgcode['addFileToEntity']['ERR1'] = 187;
$msgcode['addFileToEntity']['ERR2'] = 181;
$msgcode['addFileToEntity']['ERR3'] = 182;
function addFileToEntity ()
{
	global $page, $pageno, $etype_by_pageno;
	if (!isset ($etype_by_pageno[$pageno]) or !isset ($page[$pageno]['bypass']))
		return buildRedirectURL (__FUNCTION__, 'ERR1', array (__FUNCTION__));
	$realm = $etype_by_pageno[$pageno];
	$bypass = $page[$pageno]['bypass'];
	assertUIntArg ($bypass);
	$entity_id = $_REQUEST[$bypass];
	assertStringArg ('comment', TRUE);

	// Make sure the file can be uploaded
	if (get_cfg_var('file_uploads') != 1)
		return buildRedirectURL (__FUNCTION__, 'ERR2');

	$fp = fopen($_FILES['file']['tmp_name'], 'rb');
	global $sic;
	// commitAddFile() uses prepared statements
	$error = commitAddFile ($_FILES['file']['name'], $_FILES['file']['type'], $_FILES['file']['size'], $fp, $sic['comment']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR3', array ($error));

	$error = commitLinkFile (lastInsertID(), $realm, $entity_id);	
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR3', array ($error));

	return buildRedirectURL (__FUNCTION__, 'OK', array ($_FILES['file']['name']));
}

$msgcode['linkFileToEntity']['OK'] = 71;
$msgcode['linkFileToEntity']['ERR1'] = 178;
$msgcode['linkFileToEntity']['ERR2'] = 100;
function linkFileToEntity ()
{
	assertUIntArg ('file_id');
	global $page, $pageno, $etype_by_pageno;
	$entity_type = $etype_by_pageno[$pageno];
	$bypass_name = $page[$pageno]['bypass'];
	assertUIntArg ($bypass_name);

	$fi = spotEntity ('file', $_REQUEST['file_id']);
	if ($fi === NULL)
		return buildRedirectURL (__FUNCTION__, 'ERR1'); // file not found
	$error = commitLinkFile ($_REQUEST['file_id'], $entity_type, $_REQUEST[$bypass_name]);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($error)); // linking failed

	return buildRedirectURL (__FUNCTION__, 'OK', array ($fi['name']));
}

$msgcode['replaceFile']['OK'] = 70;
$msgcode['replaceFile']['ERR1'] = 181;
$msgcode['replaceFile']['ERR2'] = 207;
$msgcode['replaceFile']['ERR3'] = 182;
function replaceFile ()
{
	global $sic;
	assertUIntArg ('file_id');

	// Make sure the file can be uploaded
	if (get_cfg_var('file_uploads') != 1)
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	$shortInfo = spotEntity ('file', $sic['file_id']);

	$fp = fopen($_FILES['file']['tmp_name'], 'rb');
	if ($fp === FALSE)
		return buildRedirectURL (__FUNCTION__, 'ERR2');
	$error = commitReplaceFile ($sic['file_id'], $fp);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR3', array ($error));

	return buildRedirectURL (__FUNCTION__, 'OK', array (htmlspecialchars ($shortInfo['name'])));
}

$msgcode['updateFile']['OK'] = 70;
$msgcode['updateFile']['ERR'] = 100;
function updateFile ()
{
	assertUIntArg ('file_id');
	assertStringArg ('file_name');
	assertStringArg ('file_type');
	assertStringArg ('file_comment', TRUE);
	// prepared statement params below
	global $sic;
	$error = commitUpdateFile ($sic['file_id'], $sic['file_name'], $sic['file_type'], $sic['file_comment']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));

	return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['name']));
}

$msgcode['unlinkFile']['OK'] = 72;
$msgcode['unlinkFile']['ERR'] = 182;
function unlinkFile ()
{
	assertUIntArg ('link_id');
	$error = commitUnlinkFile ($_REQUEST['link_id']);

	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));

	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['deleteFile']['OK'] = 73;
$msgcode['deleteFile']['ERR'] = 100;
function deleteFile ()
{
	assertUIntArg ('file_id');
	$shortInfo = spotEntity ('file', $_REQUEST['file_id']);
	$error = commitDeleteFile ($_REQUEST['file_id']);

	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));

	return buildRedirectURL (__FUNCTION__, 'OK', array (htmlspecialchars ($shortInfo['name'])));
}

$msgcode['updateFileText']['OK'] = 78;
$msgcode['updateFileText']['ERR1'] = 179;
$msgcode['updateFileText']['ERR2'] = 180;
function updateFileText ()
{
	assertUIntArg ('file_id');
	assertStringArg ('mtime_copy');
	assertStringArg ('file_text', TRUE); // it's Ok to save empty
	$shortInfo = spotEntity ('file', $_REQUEST['file_id']);
	if ($shortInfo['mtime'] != $_REQUEST['mtime_copy'])
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	global $sic;
	$error = commitReplaceFile ($sic['file_id'], $sic['file_text']);
	if ($error == '')
		return buildRedirectURL (__FUNCTION__, 'OK', array (htmlspecialchars ($shortInfo['name'])));
	return buildRedirectURL (__FUNCTION__, 'ERR2');
}

$msgcode['addPortInterfaceCompat']['OK'] = 48;
$msgcode['addPortInterfaceCompat']['ERR'] = 110;
function addPortInterfaceCompat ()
{
	assertUIntArg ('iif_id');
	assertUIntArg ('oif_id');
	if (commitSupplementPIC ($_REQUEST['iif_id'], $_REQUEST['oif_id']))
		return buildRedirectURL (__FUNCTION__, 'OK');
	return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['delPortInterfaceCompat']['OK'] = 49;
$msgcode['delPortInterfaceCompat']['ERR'] = 111;
function delPortInterfaceCompat ()
{
	assertUIntArg ('iif_id');
	assertUIntArg ('oif_id');
	if (commitReducePIC ($_REQUEST['iif_id'], $_REQUEST['oif_id']))
		return buildRedirectURL (__FUNCTION__, 'OK');
	return buildRedirectURL (__FUNCTION__, 'ERR');
}

$ifcompatpack = array
(
	'1000cwdm80' => array (1209, 1210, 1211, 1212, 1213, 1214, 1215, 1216),
	'1000dwdm80' => array // ITU channels 20~61
	(
		1217, 1218, 1219, 1220, 1221, 1222, 1223, 1224, 1225, 1226,
		1227, 1228, 1229, 1230, 1231, 1232, 1233, 1234, 1235, 1236,
		1237, 1238, 1239, 1240, 1241, 1242, 1243, 1244, 1245, 1246,
		1247, 1248, 1249, 1250, 1251, 1252, 1253, 1254, 1255, 1256,
		1257, 1258
	),
	'10000dwdm80' => array // same channels for 10GE
	(
		1259, 1260, 1261, 1262, 1263, 1264, 1265, 1266, 1267, 1268,
		1269, 1270, 1271, 1272, 1273, 1274, 1275, 1276, 1277, 1278,
		1279, 1280, 1281, 1282, 1283, 1284, 1285, 1286, 1287, 1288,
		1289, 1290, 1291, 1292, 1293, 1294, 1295, 1296, 1297, 1298,
		1299, 1300
	),
);

$msgcode['addPortInterfaceCompatPack']['OK'] = 44;
$msgcode['addPortInterfaceCompatPack']['ERR'] = 123;
function addPortInterfaceCompatPack ()
{
	assertStringArg ('standard');
	assertUIntArg ('iif_id');
	global $ifcompatpack;
	if (!array_key_exists ($_REQUEST['standard'], $ifcompatpack) or !array_key_exists ($_REQUEST['iif_id'], getPortIIFOptions()))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	$ngood = $nbad = 0;
	foreach ($ifcompatpack[$_REQUEST['standard']] as $oif_id)
		if (commitSupplementPIC ($_REQUEST['iif_id'], $oif_id))
			$ngood++;
		else
			$nbad++;
	return buildRedirectURL (__FUNCTION__, 'OK', array ($nbad, $ngood));
}

$msgcode['delPortInterfaceCompatPack']['OK'] = 44;
$msgcode['delPortInterfaceCompatPack']['ERR'] = 123;
function delPortInterfaceCompatPack ()
{
	assertStringArg ('standard');
	assertUIntArg ('iif_id');
	global $ifcompatpack;
	if (!array_key_exists ($_REQUEST['standard'], $ifcompatpack) or !array_key_exists ($_REQUEST['iif_id'], getPortIIFOptions()))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	$ngood = $nbad = 0;
	foreach ($ifcompatpack[$_REQUEST['standard']] as $oif_id)
		if (commitReducePIC ($_REQUEST['iif_id'], $oif_id))
			$ngood++;
		else
			$nbad++;
	return buildRedirectURL (__FUNCTION__, 'OK', array ($nbad, $ngood));
}

$msgcode['addPortOIFCompat']['OK'] = 48;
$msgcode['addPortOIFCompat']['ERR'] = 110;
function addPortOIFCompat()
{
	assertUIntArg('type1');
	assertUIntArg('type2');
	if (commitSupplementPOIFC($_REQUEST['type1'], $_REQUEST['type2']))
		return buildRedirectURL(__FUNCTION__, 'OK');
	return buildRedirectURL(__FUNCTION__, 'ERR');
}

$msgcode['delPortOIFCompat']['OK'] = 49;
$msgcode['delPortOIFCompat']['ERR'] = 111;
function delPortOIFCompat ()
{
	assertUIntArg('type1');
	assertUIntArg('type2');
	if (commitReducePOIFC ($_REQUEST['type1'], $_REQUEST['type2']))
		return buildRedirectURL (__FUNCTION__, 'OK');
	return buildRedirectURL (__FUNCTION__, 'ERR');

}

$msgcode['add8021QOrder']['OK'] = 48;
$msgcode['add8021QOrder']['ERR'] = 118;
function add8021QOrder ()
{
	assertUIntArg ('vdom_id');
	assertUIntArg ('object_id');
	assertUIntArg ('vst_id');
	global $sic;
	$result = usePreparedInsertBlade
	(
		'VLANSwitch',
		array
		(
			'domain_id' => $sic['vdom_id'],
			'object_id' => $sic['object_id'],
			'template_id' => $sic['vst_id'],
		)
	);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['del8021QOrder']['OK'] = 49;
$msgcode['del8021QOrder']['ERR'] = 119;
function del8021QOrder ()
{
	assertUIntArg ('object_id');
	global $sic;
	$result = usePreparedDeleteBlade ('VLANSwitch', array ('object_id' => $sic['object_id']));
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['addVLANDescription']['OK'] = 48;
$msgcode['addVLANDescription']['ERR1'] = 190;
$msgcode['addVLANDescription']['ERR2'] = 110;
function addVLANDescription ()
{
	assertUIntArg ('vlan_id');
	assertStringArg ('vlan_type', TRUE);
	assertStringArg ('vlan_descr', TRUE);
	global $sic;
	if (!($sic['vlan_id'] >= VLAN_MIN_ID and $sic['vlan_id'] <= VLAN_MAX_ID))
		return buildRedirectURL (__FUNCTION__, 'ERR1', array ($sic['vlan_id']));
	$result = usePreparedInsertBlade
	(
		'VLANDescription',
		array
		(
			'domain_id' => $sic['vdom_id'],
			'vlan_id' => $sic['vlan_id'],
			'vlan_type' => $sic['vlan_type'],
			'vlan_descr' => mb_strlen ($sic['vlan_descr']) ? $sic['vlan_descr'] : NULL
		)
	);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR2');
}

$msgcode['delVLANDescription']['OK'] = 49;
$msgcode['delVLANDescription']['ERR'] = 111;
function delVLANDescription ()
{
	assertUIntArg ('vlan_id');
	global $sic;
	$result = commitReduceVLANDescription ($sic['vdom_id'], $sic['vlan_id']);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['updVLANDescription']['OK'] = 51;
$msgcode['updVLANDescription']['ERR'] = 109;
function updVLANDescription ()
{
	assertUIntArg ('vlan_id');
	assertStringArg ('vlan_type');
	assertStringArg ('vlan_descr', TRUE);
	global $sic;
	$result = commitUpdateVLANDescription
	(
		$sic['vdom_id'],
		$sic['vlan_id'],
		$sic['vlan_type'],
		$sic['vlan_descr']
	);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['createVLANDomain']['OK'] = 48;
$msgcode['createVLANDomain']['ERR'] = 110;
function createVLANDomain ()
{
	assertStringArg ('vdom_descr');
	global $sic;
	$result = usePreparedInsertBlade
	(
		'VLANDomain',
		array
		(
			'description' => $sic['vdom_descr'],
		)
	);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['destroyVLANDomain']['OK'] = 49;
$msgcode['destroyVLANDomain']['ERR'] = 111;
function destroyVLANDomain ()
{
	assertUIntArg ('vdom_id');
	global $sic;
	$result = FALSE !== usePreparedDeleteBlade ('VLANDomain', array ('id' => $sic['vdom_id']));
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['updateVLANDomain']['OK'] = 51;
$msgcode['updateVLANDomain']['ERR'] = 109;
function updateVLANDomain ()
{
	assertUIntArg ('vdom_id');
	assertStringArg ('vdom_descr');
	global $sic;
	$result = commitUpdateVLANDomain ($sic['vdom_id'], $sic['vdom_descr']);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['save8021QPorts']['OK'] = 63;
$msgcode['save8021QPorts']['ERR1'] = 160;
$msgcode['save8021QPorts']['ERR2'] = 109;
function save8021QPorts ()
{
	global $sic, $dbxlink;
	assertUIntArg ('nports');
	assertUIntArg ('mutex_rev');
	if ($sic['nports'] == 1)
	{
		assertStringArg ('pn_0');
		$extra = array ('port_name' => $sic['pn_0']);
	}
	else
		$extra = array();
	$dbxlink->beginTransaction();
	try
	{
		if (NULL === $vswitch = getVLANSwitchInfo ($sic['object_id'], 'FOR UPDATE'))
			throw new InvalidArgException ('object_id', $object_id, 'VLAN domain is not set for this object');
		if ($vswitch['mutex_rev'] != $sic['mutex_rev'])
			throw new RuntimeException ('expired form data');
		$changes = array();
		for ($i = 0; $i < $sic['nports']; $i++)
		{
			assertStringArg ('pn_' . $i);
			assertStringArg ('pm_' . $i);
			// An access port only generates form input for its native VLAN,
			// which we derive allowed VLAN list from.
			$native = isset ($sic['pnv_' . $i]) ? $sic['pnv_' . $i] : 0;
			switch ($sic["pm_${i}"])
			{
			case 'trunk':
#				assertArrayArg ('pav_' . $i);
				$allowed = isset ($sic['pav_' . $i]) ? $sic['pav_' . $i] : array();
				break;
			case 'access':
				if ($native == 'same')
					continue 2;
				assertUIntArg ('pnv_' . $i);
				$allowed = array ($native);
				break;
			default:
				throw new InvalidRequestArgException ("pm_${i}", $_REQUEST["pm_${i}"], 'unknown port mode');
			}
			$changes[$sic['pn_' . $i]] = array
			(
				'mode' => $sic['pm_' . $i],
				'allowed' => $allowed,
				'native' => $native,
			);
		}
		$domain_vlanlist = getDomainVLANs ($vswitch['domain_id']);
		$after = $before = apply8021QOrder ($vswitch['template_id'], getStored8021QConfig ($sic['object_id'], 'desired'));
		$changes = filter8021QChangeRequests
		(
			$domain_vlanlist,
			$before,
			apply8021QOrder ($vswitch['template_id'], $changes)
		);
		foreach ($changes as $port_name => $port)
			$after[$port_name] = $port;
		foreach (produceUplinkPorts ($domain_vlanlist, $after) as $port_name => $port)
			$after[$port_name] = $port;
		$npulled = replace8021QPorts ('desired', $vswitch['object_id'], $before, $after);
	}
	catch (Exception $e)
	{
		$dbxlink->rollBack();
		return buildRedirectURL (__FUNCTION__, 'ERR2', array(), NULL, NULL, $extra);
	}
	if ($npulled)
	{
		$query = $dbxlink->prepare ('UPDATE VLANSwitch SET mutex_rev = mutex_rev + 1, last_edited = NOW() WHERE object_id = ?');
		$query->execute (array ($sic['object_id']));
	}
	$dbxlink->commit();
	return buildRedirectURL (__FUNCTION__, 'OK', array ($npulled), NULL, NULL, $extra);
}

$msgcode['bindVLANtoIPv4']['OK'] = 48;
$msgcode['bindVLANtoIPv4']['ERR'] = 110;
function bindVLANtoIPv4 ()
{
	assertUIntArg ('id'); // network id
	global $sic;
	$result = commitSupplementVLANIPv4 ($sic['vlan_ck'], $sic['id']);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['unbindVLANfromIPv4']['OK'] = 49;
$msgcode['unbindVLANfromIPv4']['ERR'] = 111;
function unbindVLANfromIPv4 ()
{
	assertUIntArg ('id'); // network id
	global $sic;
	$result = commitReduceVLANIPv4 ($sic['vlan_ck'], $sic['id']);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['process8021QSyncRequest']['OK'] = 63;
$msgcode['process8021QSyncRequest']['ERR1'] = 109;
$msgcode['process8021QSyncRequest']['ERR2'] = 141;
function process8021QSyncRequest ()
{
	global $sic, $dbxlink;
	$do_pull = array_key_exists ('do_pull', $sic);
	$do_push = array_key_exists ('do_push', $sic);
	$done = 0;
	$dbxlink->beginTransaction();
	try
	{
		if (NULL === $vswitch = getVLANSwitchInfo ($sic['object_id'], 'FOR UPDATE'))
			throw new InvalidArgException ('object_id', $sic['object_id'], 'VLAN domain is not set for this object');
		$D = getStored8021QConfig ($vswitch['object_id'], 'desired');
		$C = getStored8021QConfig ($vswitch['object_id'], 'cached');
		$R = getRunning8021QConfig ($vswitch['object_id']);
		$plan = get8021QSyncOptions ($vswitch, $D, $C, $R['portdata']);
		// always update cache with new data from switch
		replace8021QPorts ('cached', $vswitch['object_id'], $C, $plan['ok_to_accept']);
		foreach ($plan['ok_to_delete'] as $port_name)
			$done += del8021QPort ($vswitch['object_id'], $port_name);
		foreach ($plan['ok_to_add'] as $port_name => $port)
			$done += add8021QPort ($vswitch['object_id'], $port_name, $port);
		if (count ($plan['ok_to_accept']) + count ($plan['ok_to_delete']) + count ($plan['ok_to_add']))
		{
			$prepared = $dbxlink->prepare ("UPDATE VLANSwitch SET last_cache_update = NOW() WHERE object_id = ?");
			$prepared->execute (array ($vswitch['object_id']));
		}
#dump($plan);
#die;
		$conflict = count ($plan['in_conflict']) > 0;
		if ($do_pull)
		{
			if (!$conflict)
			{
				$done += replace8021QPorts ('desired', $vswitch['object_id'], $D, $plan['to_pull']);
				replace8021QPorts ('cached', $vswitch['object_id'], $C, $plan['to_pull']);
				$prepared = $dbxlink->prepare ("UPDATE VLANSwitch SET mutex_rev = mutex_rev + 1, last_pull_done = NOW() WHERE object_id = ?");
				$prepared->execute (array ($vswitch['object_id']));
			}
			else
			{
				$prepared = $dbxlink->prepare ("UPDATE VLANSwitch SET last_pull_failed = NOW() WHERE object_id = ?");
				$prepared->execute (array ($vswitch['object_id']));
			}
		}
		if ($do_push)
		{
			if (!$conflict)
			{
				$done += exportSwitch8021QConfig ($vswitch, $R['vlanlist'], $R['portdata'], $plan['to_push']);
				// update cache for ports deployed
				replace8021QPorts ('cached', $vswitch['object_id'], $R['portdata'], $plan['to_push']);
				$prepared = $dbxlink->prepare ("UPDATE VLANSwitch SET last_push_done = NOW() WHERE object_id = ?");
				$prepared->execute (array ($vswitch['object_id']));
			}
			else
			{
				$prepared = $dbxlink->prepare ("UPDATE VLANSwitch SET last_push_failed = NOW() WHERE object_id = ?");
				$prepared->execute (array ($vswitch['object_id']));
			}
		}
	}
	catch (Exception $e)
	{
		$dbxlink->rollBack();
dump($e);
die;
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	}
	$dbxlink->commit();
	if (!count ($plan['in_conflict']))
		return buildRedirectURL (__FUNCTION__, 'OK', array ($done));
	return buildRedirectURL (__FUNCTION__, 'ERR2', array (count ($plan['in_conflict']), $done));
}

$msgcode['resolve8021QConflicts']['OK'] = 63;
$msgcode['resolve8021QConflicts']['ERR1'] = 179;
$msgcode['resolve8021QConflicts']['ERR2'] = 109;
function resolve8021QConflicts ()
{
	global $sic, $dbxlink;
	assertUIntArg ('mutex_rev');
	assertUIntArg ('nrows');
	// Divide submitted radio buttons into 3 groups:
	// left (produce and send commands to switch)
	// asis (ignore)
	// right (fetch config from switch and save into database)
	$F = array ('left' => array(), 'right' => array());
	for ($i = 0; $i < $sic['nrows']; $i++)
	{
		if (!array_key_exists ("i_${i}", $sic))
			continue;
		// let's hope other inputs are in place
		switch ($sic["i_${i}"])
		{
		case 'left':
		case 'right':
			$F[$sic["pn_${i}"]] = array
			(
				'mode' => $sic["rm_${i}"],
				'allowed' => $sic["ra_${i}"],
				'native' => $sic["rn_${i}"],
				'decision' => $sic["i_${i}"],
			);
			break;
		default:
			// don't care
		}
	}
	$dbxlink->beginTransaction();
	try
	{
		if (NULL === $vswitch = getVLANSwitchInfo ($sic['object_id'], 'FOR UPDATE'))
			throw new InvalidArgException ('object_id', $sic['object_id'], 'VLAN domain is not set for this object');
		if ($vswitch['mutex_rev'] != $sic['mutex_rev'])
		{
			throw new RuntimeException ('expired form (table data has changed)');
		}
		$D = getStored8021QConfig ($sic['object_id'], 'desired');
		$R = getRunning8021QConfig ($sic['object_id']);
		$ndone = 0;
		foreach ($F as $port_name => $port)
		{
			// for R mutex cannot be emulated, but revision can be
			if (!same8021QConfigs ($port, $R['portdata'][$port_name]))
				throw new RuntimeException ('expired form (switch data has changed)');
			switch ($port['decision'])
			{
			case 'left':
				// D wins, frame R by writing value of R to C
				upd8021QPort ('cached', $vswitch['object_id'], $port_name, $port);
				$ndone++;
				break;
			case 'right': // last_edited = NOW()
				// R wins, cross D up
				upd8021QPort ('cached', $vswitch['object_id'], $port_name, $D[$port_name]);
				$ndone++;
				break;
			}
		}
		if ($ndone)
		{
			$query = $dbxlink->prepare ('UPDATE VLANSwitch SET last_edited = NOW() WHERE object_id = ?');
			$query->execute (array ($vswitch['object_id']));
		}
	}
	catch (RuntimeException $e)
	{
		$dbxlink->rollBack();
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	}
	catch (Exception $e)
	{
		$dbxlink->rollBack();
		return buildRedirectURL (__FUNCTION__, 'ERR2');
	}
	$dbxlink->commit();
	return buildRedirectURL (__FUNCTION__, 'OK', array ($ndone));
}

$msgcode['addVLANSwitchTemplate']['OK'] = 48;
$msgcode['addVLANSwitchTemplate']['ERR'] = 110;
function addVLANSwitchTemplate()
{
	assertStringArg ('vst_descr');
	global $sic;
	$max_local_vlans = NULL;
	if (array_key_exists ('vst_maxvlans', $sic) && mb_strlen ($sic['vst_maxvlans']))
	{
		assertUIntArg ('vst_maxvlans');
		$max_local_vlans = $sic['vst_maxvlans'];
	}
	$result = usePreparedInsertBlade
	(
		'VLANSwitchTemplate',
		array
		(
			'max_local_vlans' => $max_local_vlans,
			'description' => $sic['vst_descr'],
		)
	);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['delVLANSwitchTemplate']['OK'] = 49;
$msgcode['delVLANSwitchTemplate']['ERR'] = 111;
function delVLANSwitchTemplate()
{
	assertUIntArg ('vst_id');
	global $sic;
	$result = FALSE !== usePreparedDeleteBlade ('VLANSwitchTemplate', array ('id' => $sic['vst_id']));
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['updVLANSwitchTemplate']['OK'] = 51;
$msgcode['updVLANSwitchTemplate']['ERR'] = 109;
function updVLANSwitchTemplate()
{
	assertUIntArg ('vst_id');
	assertStringArg ('vst_descr');
	global $sic;
	$max_local_vlans = NULL;
	if (array_key_exists ('vst_maxvlans', $sic) && mb_strlen ($sic['vst_maxvlans']))
	{
		assertUIntArg ('vst_maxvlans');
		$max_local_vlans = $sic['vst_maxvlans'];
	}
	$result = commitUpdateVST ($sic['vst_id'], $max_local_vlans, $sic['vst_descr']);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['addVSTRule']['OK'] = 48;
$msgcode['addVSTRule']['ERR'] = 110;
function addVSTRule()
{
	assertUIntArg ('vst_id');
	assertUIntArg ('rule_no');
	assertPCREArg ('port_pcre');
	assertStringArg ('port_role');
	assertStringArg ('wrt_vlans', TRUE);
	global $sic;
	$result = usePreparedInsertBlade
	(
		'VLANSTRule',
		array
		(
			'vst_id' => $sic['vst_id'],
			'rule_no' => $sic['rule_no'],
			'port_pcre' => $sic['port_pcre'],
			'port_role' => $sic['port_role'],
			'wrt_vlans' => $sic['wrt_vlans'],
		)
	);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['delVSTRule']['OK'] = 49;
$msgcode['delVSTRule']['ERR'] = 111;
function delVSTRule()
{
	assertUIntArg ('vst_id');
	assertUIntArg ('rule_no');
	global $sic;
	$result = FALSE !== usePreparedDeleteBlade
	(
		'VLANSTRule',
		array
		(
			'vst_id' => $sic['vst_id'],
			'rule_no' => $sic['rule_no']
		)
	);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['updVSTRule']['OK'] = 51;
$msgcode['updVSTRule']['ERR'] = 109;
function updVSTRule()
{
	assertUIntArg ('vst_id');
	assertUIntArg ('rule_no');
	assertUIntArg ('new_rule_no');
	assertPCREArg ('port_pcre');
	assertStringArg ('port_role');
	assertStringArg ('wrt_vlans', TRUE);
	global $sic;
	$result = commitUpdateVSTRule
	(
		$sic['vst_id'],
		$sic['rule_no'],
		$sic['new_rule_no'],
		$sic['port_pcre'],
		$sic['port_role'],
		$sic['wrt_vlans']
	);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

?>
