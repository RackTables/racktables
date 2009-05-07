<?php
/*
*
*  This file is a library of operation handlers for RackTables.
*
*/

$msgcode = array();

function buildWideRedirectURL ($log, $nextpage = NULL, $nexttab = NULL, $moreArgs = array())
{
	global $root, $page, $pageno, $tabno;
	if ($nextpage === NULL)
		$nextpage = $pageno;
	if ($nexttab === NULL)
		$nexttab = $tabno;
	$url = "${root}?page=${nextpage}&tab=${nexttab}";
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

function buildRedirectURL ($callfunc, $status, $args = array(), $nextpage = NULL, $nexttab = NULL)
{
	global $pageno, $tabno, $msgcode;
	if ($nextpage === NULL)
		$nextpage = $pageno;
	if ($nexttab === NULL)
		$nexttab = $tabno;
	return buildWideRedirectURL (oneLiner ($msgcode[$callfunc][$status], $args), $nextpage, $nexttab);
}

$msgcode['addPortForwarding']['OK'] = 2;
$msgcode['addPortForwarding']['ERR'] = 100;
function addPortForwarding ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	assertIPv4Arg ('localip', __FUNCTION__);
	assertIPv4Arg ('remoteip', __FUNCTION__);
	assertUIntArg ('localport', __FUNCTION__);
	assertStringArg ('proto', __FUNCTION__);
	assertStringArg ('description', __FUNCTION__, TRUE);
	$remoteport = isset ($_REQUEST['remoteport']) ? $_REQUEST['remoteport'] : '';
	if (empty ($remoteport))
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
	assertUIntArg ('object_id', __FUNCTION__);
	assertIPv4Arg ('localip', __FUNCTION__);
	assertIPv4Arg ('remoteip', __FUNCTION__);
	assertUIntArg ('localport', __FUNCTION__);
	assertUIntArg ('remoteport', __FUNCTION__);
	assertStringArg ('proto', __FUNCTION__);

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
	assertUIntArg ('object_id', __FUNCTION__);
	assertIPv4Arg ('localip', __FUNCTION__);
	assertIPv4Arg ('remoteip', __FUNCTION__);
	assertUIntArg ('localport', __FUNCTION__);
	assertUIntArg ('remoteport', __FUNCTION__);
	assertStringArg ('proto', __FUNCTION__);
	assertStringArg ('description', __FUNCTION__);

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
	assertUIntArg ('object_id', __FUNCTION__);
	assertStringArg ('port_name', __FUNCTION__, TRUE);
	if (empty ($_REQUEST['port_name']))
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
	assertUIntArg ('port_id', __FUNCTION__);
	assertUIntArg ('port_type_id', __FUNCTION__);
	// tolerate empty value now to produce custom informative message later
	assertStringArg ('name', __FUNCTION__, TRUE);
	if (empty ($_REQUEST['name']))
		return buildRedirectURL (__FUNCTION__, 'ERR1');

	if (isset ($_REQUEST['reservation_comment']) and !empty ($_REQUEST['reservation_comment']))
		$port_rc = '"' . $_REQUEST['reservation_comment'] . '"';
	else
		$port_rc = 'NULL';
	$error = commitUpdatePort ($_REQUEST['port_id'], $_REQUEST['name'], $_REQUEST['port_type_id'], $_REQUEST['label'], $_REQUEST['l2address'], $port_rc);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['name']));
}

$msgcode['delPortFromObject']['OK'] = 7;
$msgcode['delPortFromObject']['ERR'] = 100;
function delPortFromObject ()
{
	assertUIntArg ('port_id', __FUNCTION__);
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
	assertUIntArg ('port_id', __FUNCTION__);
	assertUIntArg ('remote_port_id', __FUNCTION__);
	assertStringArg ('port_name', __FUNCTION__, TRUE);
	assertStringArg ('remote_port_name', __FUNCTION__, TRUE);
	assertStringArg ('remote_object_name', __FUNCTION__, TRUE);

	$error = linkPorts ($_REQUEST['port_id'], $_REQUEST['remote_port_id']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['port_name'], $_REQUEST['remote_port_name'], $_REQUEST['remote_object_name']));
}

$msgcode['unlinkPortForObject']['OK'] = 9;
$msgcode['unlinkPortForObject']['ERR'] = 100;
function unlinkPortForObject ()
{
	assertUIntArg ('port_id', __FUNCTION__);
	assertStringArg ('port_name', __FUNCTION__, TRUE);
	assertStringArg ('remote_port_name', __FUNCTION__, TRUE);
	assertStringArg ('remote_object_name', __FUNCTION__, TRUE);

	$error = unlinkPort ($_REQUEST['port_id']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['port_name'], $_REQUEST['remote_port_name'], $_REQUEST['remote_object_name']));
}

$msgcode['addMultiPorts']['OK'] = 10;
$msgcode['addMultiPorts']['ERR'] = 123;
function addMultiPorts ()
{
	assertStringArg ('format', __FUNCTION__);
	assertStringArg ('input', __FUNCTION__);
	assertUIntArg ('port_type', __FUNCTION__);
	assertUIntArg ('object_id', __FUNCTION__);
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
		if (empty ($parts[0]))
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
				$words = explode (' ', ereg_replace ('[[:space:]]+', ' ', $line));
				list ($slot, $port) = explode ('/', $words[0]);
				$ports[] = array
				(
					'name' => "e ${slot}/${port}",
					'l2address' => $words[8],
					'label' => "slot ${slot} port ${port}"
				);
				break;
			case 'c3600asy':
				$words = explode (' ', ereg_replace ('[[:space:]]+', ' ', trim (substr ($line, 3))));
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
				$words = explode (' ', ereg_replace ('[[:space:]]+', ' ', $line));
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
				if (empty ($words[0]) or empty ($words[1]))
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
			$result = commitUpdatePort ($port_id, $port['name'], $port_type, $port['label'], $port['l2address']);
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
	assertIPv4Arg ('ip', __FUNCTION__);
	assertUIntArg ('object_id', __FUNCTION__);
	assertStringArg ('bond_name', __FUNCTION__, TRUE);
	assertStringArg ('bond_type', __FUNCTION__);

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
	assertIPv4Arg ('ip', __FUNCTION__);
	assertUIntArg ('object_id', __FUNCTION__);

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
	assertIPv4Arg ('ip', __FUNCTION__);
	assertUIntArg ('object_id', __FUNCTION__);
	assertStringArg ('bond_name', __FUNCTION__, TRUE);
	assertStringArg ('bond_type', __FUNCTION__);

	// Strip masklen.
	$ip = ereg_replace ('/[[:digit:]]+$', '', $_REQUEST['ip']);
	if  (getConfigVar ('IPV4_JAYWALK') != 'yes' and NULL === getIPv4AddressNetworkId ($ip))
		return buildRedirectURL (__FUNCTION__, 'ERR1', array ($ip));
	
	$error = bindIpToObject ($ip, $_REQUEST['object_id'], $_REQUEST['bond_name'], $_REQUEST['bond_type']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($error));
	$address = getIPv4Address ($ip);
	if ($address['reserved'] == 'yes' or !empty ($address['name']))
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
	assertStringArg ('range', __FUNCTION__);
	assertStringArg ('name', __FUNCTION__, TRUE);

	$is_bcast = isset ($_REQUEST['is_bcast']) ? $_REQUEST['is_bcast'] : 'off';
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	$error = createIPv4Prefix ($_REQUEST['range'], $_REQUEST['name'], $is_bcast == 'on', $taglist);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['delIPv4Prefix']['OK'] = 24;
$msgcode['delIPv4Prefix']['ERR'] = 100;
function delIPv4Prefix ()
{
	assertUIntArg ('id', __FUNCTION__);
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
	assertUIntArg ('id', __FUNCTION__);
	assertStringArg ('name', __FUNCTION__, TRUE);

	$error = updateRange ($_REQUEST['id'], $_REQUEST['name']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['editAddress']['OK'] = 27;
$msgcode['editAddress']['ERR'] = 100;
function editAddress ()
{
	assertIPv4Arg ('ip', __FUNCTION__);
	assertStringArg ('name', __FUNCTION__, TRUE);

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
	assertStringArg ('username', __FUNCTION__);
	assertStringArg ('realname', __FUNCTION__, TRUE);
	assertStringArg ('password', __FUNCTION__);
	$username = $_REQUEST['username'];
	$password = sha1 ($_REQUEST['password']);
	$result = commitCreateUserAccount ($username, $_REQUEST['realname'], $password);
	if ($result == TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK', array ($username));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($username));
}

$msgcode['updateUser']['OK'] = 39;
$msgcode['updateUser']['ERR1'] = 103;
$msgcode['updateUser']['ERR1'] = 104;
function updateUser ()
{
	assertUIntArg ('user_id', __FUNCTION__);
	assertStringArg ('username', __FUNCTION__);
	assertStringArg ('realname', __FUNCTION__, TRUE);
	assertStringArg ('password', __FUNCTION__);
	$username = $_REQUEST['username'];
	$new_password = $_REQUEST['password'];
	if (NULL == ($userinfo = getUserInfo ($_REQUEST['user_id'])))
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

$msgcode['savePortMap']['OK'] = 44;
$msgcode['savePortMap']['ERR'] = 108;
// This function find differences in users's submit and PortCompat table
// and modifies database accordingly.
function savePortMap ()
{
	$ptlist = getPortTypes();
	$oldCompat = getPortCompat();
	$newCompat = array();
	foreach (array_keys ($ptlist) as $leftKey)
		foreach (array_keys ($ptlist) as $rightKey)
			if (isset ($_REQUEST["atom_${leftKey}_${rightKey}"]))
				$newCompat[] = array ('type1' => $leftKey, 'type2' => $rightKey);
	// Build the new matrix from user's submit and compare it to
	// the old matrix. Those pairs which appear on
	// new matrix only, have to be stored in PortCompat table. Those who appear
	// on the old matrix only, should be removed from PortCompat table.
	// Those present in both matrices should be skipped.
	$oldCompatTable = buildPortCompatMatrixFromList ($ptlist, $oldCompat);
	$newCompatTable = buildPortCompatMatrixFromList ($ptlist, $newCompat);
	$error_count = $success_count = 0;
	foreach (array_keys ($ptlist) as $type1)
		foreach (array_keys ($ptlist) as $type2)
			if ($oldCompatTable[$type1][$type2] != $newCompatTable[$type1][$type2])
				switch ($oldCompatTable[$type1][$type2])
				{
					case TRUE: // new value is FALSE
						if (removePortCompat ($type1, $type2) === TRUE)
							$success_count++;
						else
							$error_count++;
						break;
					case FALSE: // new value is TRUE
						if (addPortCompat ($type1, $type2) === TRUE)
							$success_count++;
						else
							$error_count++;
						break;
					default:
						showError ('Internal error: oldCompatTable is invalid', __FUNCTION__);
						break;
				}
	if ($error_count == 0)
		return buildRedirectURL (__FUNCTION__, 'OK', array ($error_count, $success_count));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error_count, $success_count));
}

$msgcode['updateDictionary']['OK'] = 51;
$msgcode['updateDictionary']['ERR'] = 109;
function updateDictionary ()
{
	assertUIntArg ('chapter_no', __FUNCTION__);
	assertUIntArg ('dict_key', __FUNCTION__);
	assertStringArg ('dict_value', __FUNCTION__);
	if (commitUpdateDictionary ($_REQUEST['chapter_no'], $_REQUEST['dict_key'], $_REQUEST['dict_value']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['supplementDictionary']['OK'] = 52;
$msgcode['supplementDictionary']['ERR'] = 110;
function supplementDictionary ()
{
	assertUIntArg ('chapter_no', __FUNCTION__);
	assertStringArg ('dict_value', __FUNCTION__);
	if (commitSupplementDictionary ($_REQUEST['chapter_no'], $_REQUEST['dict_value']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['reduceDictionary']['OK'] = 50;
$msgcode['reduceDictionary']['ERR'] = 111;
function reduceDictionary ()
{
	assertUIntArg ('chapter_no', __FUNCTION__);
	assertUIntArg ('dict_key', __FUNCTION__);
	if (commitReduceDictionary ($_REQUEST['chapter_no'], $_REQUEST['dict_key']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['addChapter']['OK'] = 55;
$msgcode['addChapter']['ERR'] = 112;
function addChapter ()
{
	assertStringArg ('chapter_name', __FUNCTION__);
	if (commitAddChapter ($_REQUEST['chapter_name']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['updateChapter']['OK'] = 54;
$msgcode['updateChapter']['ERR'] = 113;
function updateChapter ()
{
	assertUIntArg ('chapter_no', __FUNCTION__);
	assertStringArg ('chapter_name', __FUNCTION__);
	if (commitUpdateChapter ($_REQUEST['chapter_no'], $_REQUEST['chapter_name']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['delChapter']['OK'] = 53;
$msgcode['delChapter']['ERR'] = 114;
function delChapter ()
{
	assertUIntArg ('chapter_no', __FUNCTION__);
	if (commitDeleteChapter ($_REQUEST['chapter_no']))
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['changeAttribute']['OK'] = 46;
$msgcode['changeAttribute']['ERR'] = 115;
function changeAttribute ()
{
	assertUIntArg ('attr_id', __FUNCTION__);
	assertStringArg ('attr_name', __FUNCTION__);
	if (commitUpdateAttribute ($_REQUEST['attr_id'], $_REQUEST['attr_name']))
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['createAttribute']['OK'] = 45;
$msgcode['createAttribute']['ERR'] = 116;
function createAttribute ()
{
	assertStringArg ('attr_name', __FUNCTION__);
	assertStringArg ('attr_type', __FUNCTION__);
	if (commitAddAttribute ($_REQUEST['attr_name'], $_REQUEST['attr_type']))
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['attr_name']));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['deleteAttribute']['OK'] = 47;
$msgcode['deleteAttribute']['ERR'] = 117;
function deleteAttribute ()
{
	assertUIntArg ('attr_id', __FUNCTION__);
	if (commitDeleteAttribute ($_REQUEST['attr_id']))
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['supplementAttrMap']['OK'] = 48;
$msgcode['supplementAttrMap']['ERR'] = 118;
function supplementAttrMap ()
{
	assertUIntArg ('attr_id', __FUNCTION__);
	assertUIntArg ('objtype_id', __FUNCTION__);
	assertUIntArg ('chapter_no', __FUNCTION__);
	if (commitSupplementAttrMap ($_REQUEST['attr_id'], $_REQUEST['objtype_id'], $_REQUEST['chapter_no']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['reduceAttrMap']['OK'] = 49;
$msgcode['reduceAttrMap']['ERR'] = 119;
function reduceAttrMap ()
{
	assertUIntArg ('attr_id', __FUNCTION__);
	assertUIntArg ('objtype_id', __FUNCTION__);
	if (commitReduceAttrMap ($_REQUEST['attr_id'], $_REQUEST['objtype_id']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['clearSticker']['OK'] = 15;
$msgcode['clearSticker']['ERR'] = 120;
function clearSticker ()
{
	assertUIntArg ('attr_id', __FUNCTION__);
	assertUIntArg ('object_id', __FUNCTION__);
	if (commitResetAttrValue ($_REQUEST['object_id'], $_REQUEST['attr_id']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

function updateObjectAllocation ()
{
	assertUIntArg ('object_id', __FUNCTION__);

	$is_submit = isset ($_REQUEST['got_atoms']);
	$is_update = isset ($_REQUEST['rackmulti'][0]);

	if ($is_submit)
	{
		$object_id = $_REQUEST['object_id'];
		$workingRacksData = getResidentRacksData ($object_id);
		if ($workingRacksData === NULL)
		{
			print_r ($workingRacksData);
			showError ('getResidentRacksData() failed', __FUNCTION__);
			return;
		}
		$workingRacksData = array();
		foreach ($_REQUEST['rackmulti'] as $cand_id)
		{
			if (!isset ($workingRacksData[$cand_id]))
			{
				$rackData = getRackData ($cand_id);
				if ($rackData == NULL)
				{
					showError ('getRackData() failed', __FUNCTION__);
					return;
				}
				$workingRacksData[$cand_id] = $rackData;
			}
		}

		foreach ($workingRacksData as &$rd)
			applyObjectMountMask ($rd, $object_id);

		$oldMolecule = getMoleculeForObject ($object_id);
		$worldchanged = FALSE;
		$log = array();
		foreach ($workingRacksData as $rack_id => $rackData)
		{
			$logrecord = processGridForm ($rackData, 'F', 'T', $object_id);
			$log[] = $logrecord;
		}
		return buildWideRedirectURL($log);
		
	}
	else
	{
		unset($_GET['page']);
		unset($_GET['tab']);
		unset($_GET['op']);
		unset($_POST['page']);
		unset($_POST['tab']);
		unset($_POST['op']);
		return buildWideRedirectURL(array(), NULL, NULL, array_merge($_GET, $_POST));
	}

}

$msgcode['updateObject']['OK'] = 16;
$msgcode['updateObject']['ERR'] = 121;
function updateObject ()
{
	assertUIntArg ('num_attrs', __FUNCTION__);
	assertUIntArg ('object_id', __FUNCTION__);
	assertUIntArg ('object_type_id', __FUNCTION__);
	assertStringArg ('object_name', __FUNCTION__, TRUE);
	assertStringArg ('object_label', __FUNCTION__, TRUE);
	assertStringArg ('object_barcode', __FUNCTION__, TRUE);
	assertStringArg ('object_asset_no', __FUNCTION__, TRUE);
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
	for ($i = 0; $i < $_REQUEST['num_attrs']; $i++)
	{
		assertUIntArg ("${i}_attr_id", __FUNCTION__);
		$attr_id = $_REQUEST["${i}_attr_id"];

		// Field is empty, delete attribute and move on.
		if (empty($_REQUEST["${i}_value"]))
		{
			commitResetAttrValue ($_REQUEST['object_id'], $attr_id);
			continue;
		}

		// The value could be uint/float, but we don't know ATM. Let SQL
		// server check this and complain.
		assertStringArg ("${i}_value", __FUNCTION__);
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
				showError ('Internal structure error', __FUNCTION__);
				die;
		}
		if ($value == $oldvalue)
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
		assertUIntArg ("${i}_object_type_id", __FUNCTION__, TRUE);
		assertStringArg ("${i}_object_name", __FUNCTION__, TRUE);
		assertStringArg ("${i}_object_label", __FUNCTION__, TRUE);
		assertStringArg ("${i}_object_asset_no", __FUNCTION__, TRUE);
		assertStringArg ("${i}_object_barcode", __FUNCTION__, TRUE);
		$name = $_REQUEST["${i}_object_name"];

		// It's better to skip silently, than to print a notice.
		if ($_REQUEST["${i}_object_type_id"] == 0)
			continue;
		if (commitAddObject
		(
			$name,
			$_REQUEST["${i}_object_label"],
			$_REQUEST["${i}_object_barcode"],
			$_REQUEST["${i}_object_type_id"],
			$_REQUEST["${i}_object_asset_no"],
			$taglist
		) === TRUE)
			$log = mergeLogs ($log, oneLiner (80, array ($name)));
		else
			$log = mergeLogs ($log, oneLiner (185, array ($name)));
	}
	return buildWideRedirectURL ($log);
}

function addLotOfObjects()
{
	$log = emptyLog();
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	assertUIntArg ('global_type_id', __FUNCTION__, TRUE);
	assertStringArg ('namelist', __FUNCTION__, TRUE);
	$global_type_id = $_REQUEST['global_type_id'];
	if ($global_type_id == 0 or empty ($_REQUEST['namelist']))
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
			if (empty ($parts[0]))
				continue;
			else
				$names2[] = rtrim ($parts[0]);
		}
		foreach ($names2 as $name)
			if (commitAddObject ($name, '', '', $global_type_id, '', $taglist) === TRUE)
				$log = mergeLogs ($log, oneLiner (80, array ($name)));
			else
				$log = mergeLogs ($log, oneLiner (185, array ($name)));
	}
	return buildWideRedirectURL ($log);
}

$msgcode['deleteObject']['OK'] = 76;
$msgcode['deleteObject']['ERR'] = 100;
function deleteObject ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	if (NULL === ($oinfo = getObjectInfo ($_REQUEST['object_id'])))
		return buildRedirectURL (__FUNCTION__, 'ERR', array ('object not found'));

	$error = commitDeleteObject ($_REQUEST['object_id']);

	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));

	return buildRedirectURL (__FUNCTION__, 'OK', array ($oinfo['dname']));
}

$msgcode['useupPort']['OK'] = 11;
$msgcode['useupPort']['ERR'] = 124;
function useupPort ()
{
	assertUIntArg ('port_id', __FUNCTION__);
	if (commitUseupPort ($_REQUEST['port_id']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['updateUI']['OK'] = 56;
$msgcode['updateUI']['ERR'] = 125;
function updateUI ()
{
	assertUIntArg ('num_vars', __FUNCTION__);
	$error = '';

	for ($i = 0; $i < $_REQUEST['num_vars']; $i++)
	{
		assertStringArg ("${i}_varname", __FUNCTION__);
		assertStringArg ("${i}_varvalue", __FUNCTION__, TRUE);
		$varname = $_REQUEST["${i}_varname"];
		$varvalue = $_REQUEST["${i}_varvalue"];

		// If form value = value in DB, don't bother updating DB
		if ($varvalue == getConfigVar ($varname))
			continue;

		// Note if the queries succeed or not, it determines which page they see.
		$error = setConfigVar ($varname, $varvalue, TRUE);
		if (!empty ($error))
			break;
	}

	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));

	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['resetUIConfig']['OK'] = 57;
function resetUIConfig()
{
	setConfigVar ('default_port_type','24');
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
	setConfigVar ('IPV4OBJ_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8} or {$typeid_12} or {$typeid_445} or {$typeid_447}');
	setConfigVar ('IPV4NAT_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8}');
	setConfigVar ('ASSETWARN_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8}');
	setConfigVar ('NAMEWARN_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8}');
	setConfigVar ('RACKS_PER_ROW','12');
	setConfigVar ('FILTER_PREDICATE_SIEVE','');
	setConfigVar ('FILTER_DEFAULT_ANDOR','or');
	setConfigVar ('FILTER_SUGGEST_ANDOR','yes');
	setConfigVar ('FILTER_SUGGEST_TAGS','yes');
	setConfigVar ('FILTER_SUGGEST_PREDICATES','yes');
	setConfigVar ('FILTER_SUGGEST_EXTRA','no');
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addRealServer']['OK'] = 34;
$msgcode['addRealServer']['ERR'] = 126;
// Add single record.
function addRealServer ()
{
	assertUIntArg ('pool_id', __FUNCTION__);
	assertIPv4Arg ('remoteip', __FUNCTION__);
	assertStringArg ('rsport', __FUNCTION__, TRUE);
	assertStringArg ('rsconfig', __FUNCTION__, TRUE);
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
	assertUIntArg ('pool_id', __FUNCTION__);
	assertStringArg ('format', __FUNCTION__);
	assertStringArg ('rawtext', __FUNCTION__);
	$rawtext = str_replace ('\r', '', $_REQUEST['rawtext']);
	$ngood = $nbad = 0;
	$rsconfig = '';
	// Keep in mind, that the text will have HTML entities (namely '>') escaped.
	foreach (explode ('\n', $rawtext) as $line)
	{
		if (empty ($line))
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
	assertIPv4Arg ('vip', __FUNCTION__);
	assertUIntArg ('vport', __FUNCTION__);
	assertStringArg ('proto', __FUNCTION__);
	if ($_REQUEST['proto'] != 'TCP' and $_REQUEST['proto'] != 'UDP')
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	assertStringArg ('name', __FUNCTION__, TRUE);
	assertStringArg ('vsconfig', __FUNCTION__, TRUE);
	assertStringArg ('rsconfig', __FUNCTION__, TRUE);
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
	assertUIntArg ('id', __FUNCTION__);
	if (!commitDeleteRS ($_REQUEST['id']))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['deleteLoadBalancer']['OK'] = 19;
$msgcode['deleteLoadBalancer']['ERR'] = 129;
function deleteLoadBalancer ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	assertUIntArg ('pool_id', __FUNCTION__);
	assertUIntArg ('vs_id', __FUNCTION__);
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
	assertUIntArg ('vs_id', __FUNCTION__);
	if (!commitDeleteVS ($_REQUEST['vs_id']))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateRealServer']['OK'] = 36;
$msgcode['updateRealServer']['ERR'] = 133;
function updateRealServer ()
{
	assertUIntArg ('rs_id', __FUNCTION__);
	assertIPv4Arg ('rsip', __FUNCTION__);
	assertStringArg ('rsport', __FUNCTION__, TRUE);
	assertStringArg ('rsconfig', __FUNCTION__, TRUE);
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
	assertUIntArg ('object_id', __FUNCTION__);
	assertUIntArg ('pool_id', __FUNCTION__);
	assertUIntArg ('vs_id', __FUNCTION__);
	assertStringArg ('vsconfig', __FUNCTION__, TRUE);
	assertStringArg ('rsconfig', __FUNCTION__, TRUE);
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
	assertUIntArg ('vs_id', __FUNCTION__);
	assertIPv4Arg ('vip', __FUNCTION__);
	assertUIntArg ('vport', __FUNCTION__);
	assertStringArg ('proto', __FUNCTION__);
	assertStringArg ('name', __FUNCTION__, TRUE);
	assertStringArg ('vsconfig', __FUNCTION__, TRUE);
	assertStringArg ('rsconfig', __FUNCTION__, TRUE);
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
	assertUIntArg ('pool_id', __FUNCTION__);
	assertUIntArg ('object_id', __FUNCTION__);
	assertUIntArg ('vs_id', __FUNCTION__);
	assertStringArg ('vsconfig', __FUNCTION__, TRUE);
	assertStringArg ('rsconfig', __FUNCTION__, TRUE);
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
	assertStringArg ('name', __FUNCTION__, TRUE);
	assertStringArg ('vsconfig', __FUNCTION__, TRUE);
	assertStringArg ('rsconfig', __FUNCTION__, TRUE);
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
	assertUIntArg ('pool_id', __FUNCTION__);
	if (!commitDeleteRSPool ($_REQUEST['pool_id']))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateRSPool']['OK'] = 33;
$msgcode['updateRSPool']['ERR'] = 139;
function updateRSPool ()
{
	assertUIntArg ('pool_id', __FUNCTION__);
	assertStringArg ('name', __FUNCTION__, TRUE);
	assertStringArg ('vsconfig', __FUNCTION__, TRUE);
	assertStringArg ('rsconfig', __FUNCTION__, TRUE);
	if (!commitUpdateRSPool ($_REQUEST['pool_id'], $_REQUEST['name'], $_REQUEST['vsconfig'], $_REQUEST['rsconfig']))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateRSInService']['OK'] = 38;
$msgcode['updateRSInService']['ERR'] = 140;
function updateRSInService ()
{
	assertUIntArg ('rscount', __FUNCTION__);
	$pool_id = $_REQUEST['pool_id'];
	$orig = getRSPoolInfo ($pool_id);
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
	assertUIntArg ('addrcount', __FUNCTION__);
	$nbad = $ngood = 0;
	for ($i = 0; $i < $_REQUEST['addrcount']; $i++)
	{
		$inputname = "import_${i}";
		if (!isset ($_REQUEST[$inputname]) or $_REQUEST[$inputname] != 'on')
			continue;
		assertIPv4Arg ("addr_${i}", __FUNCTION__);
		assertStringArg ("descr_${i}", __FUNCTION__, TRUE);
		assertStringArg ("rsvd_${i}", __FUNCTION__);
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
	assertUIntArg ('object_id', __FUNCTION__);
	$info = getObjectInfo ($_REQUEST['object_id'], FALSE);
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
	global $explicit_tags, $implicit_tags, $page, $pageno, $etype_by_pageno;
	if (!isset ($etype_by_pageno[$pageno]) or !isset ($page[$pageno]['bypass']))
		return buildRedirectURL (__FUNCTION__, 'ERR2', array (__FUNCTION__));
	$realm = $etype_by_pageno[$pageno];
	$bypass = $page[$pageno]['bypass'];
	assertUIntArg ($bypass, __FUNCTION__);
	$entity_id = $_REQUEST[$bypass];
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	// Build a chain from the submitted data, minimize it,
	// then wipe existing records and store the new set instead.
	destroyTagsForEntity ($realm, $entity_id);
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
	assertUIntArg ('tag_id', __FUNCTION__);
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
	assertStringArg ('tag_name', __FUNCTION__);
	assertUIntArg ('parent_id', __FUNCTION__, TRUE);
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
	assertUIntArg ('tag_id', __FUNCTION__);
	assertUIntArg ('parent_id', __FUNCTION__, TRUE);
	assertStringArg ('tag_name', __FUNCTION__);
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
	assertUIntArg ('row_id', __FUNCTION__);
	assertStringArg ('sum', __FUNCTION__, TRUE);
	assertUIntArg ('realsum', __FUNCTION__);
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
	foreach (array_keys (listCells ('rack', $_REQUEST['row_id'])) as $rack_id)
	{
		if (rebuildTagChainForEntity ('rack', $rack_id, $extrachain))
			$n_ok++;
		foreach (stuffInRackspace (getRackData ($rack_id)) as $object_id)
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
	assertStringArg ('oldpassword', __FUNCTION__);
	assertStringArg ('newpassword1', __FUNCTION__);
	assertStringArg ('newpassword2', __FUNCTION__);
	$remote_userid = getUserIDByUsername ($remote_username);
	$userinfo = getUserInfo ($remote_userid);
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
	assertUIntArg ('portcount', __FUNCTION__);
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
	assertUIntArg ('object_id', __FUNCTION__);
	$newconfig = buildLVSConfig ($_REQUEST['object_id']);
	$msglog = gwSendFileToObject ($_REQUEST['object_id'], 'slbconfig', html_entity_decode ($newconfig, ENT_QUOTES, 'UTF-8'));
	return buildWideRedirectURL ($msglog);
}

$msgcode['addRow']['OK'] = 74;
$msgcode['addRow']['ERR'] = 100;
function addRow ()
{
	assertStringArg ('name', __FUNCTION__);

	if (commitAddRow ($_REQUEST['name']) === TRUE)
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['name']));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($_REQUEST['name']));
}

$msgcode['updateRow']['OK'] = 75;
$msgcode['updateRow']['ERR'] = 100;
function updateRow ()
{
	assertUIntArg ('row_id', __FUNCTION__);
	assertStringArg ('name', __FUNCTION__);

	if (TRUE === commitUpdateRow ($_REQUEST['row_id'], $_REQUEST['name']))
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['name']));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($_REQUEST['name']));
}

$msgcode['deleteRow']['OK'] = 77;
$msgcode['deleteRow']['ERR'] = 100;
function deleteRow ()
{
	assertUIntArg ('row_id', __FUNCTION__);
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
	assertUIntArg ('row_id', __FUNCTION__);
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	if (isset ($_REQUEST['got_data']))
	{
		assertStringArg ('rack_name', __FUNCTION__);
		assertUIntArg ('rack_height1', __FUNCTION__);
		assertStringArg ('rack_comment', __FUNCTION__, TRUE);

		if (commitAddRack ($_REQUEST['rack_name'], $_REQUEST['rack_height1'], $_REQUEST['row_id'], $_REQUEST['rack_comment'], $taglist) === TRUE)
			return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['rack_name']));
		else
			return buildRedirectURL (__FUNCTION__, 'ERR1', array ($_REQUEST['rack_name']));
	}
	elseif (isset ($_REQUEST['got_mdata']))
	{
		assertUIntArg ('rack_height2', __FUNCTION__);
		assertStringArg ('rack_names', __FUNCTION__, TRUE);
		$log = emptyLog();
		// copy-and-paste from renderAddMultipleObjectsForm()
		$names1 = explode ('\n', $_REQUEST['rack_names']);
		$names2 = array();
		foreach ($names1 as $line)
		{
			$parts = explode ('\r', $line);
			reset ($parts);
			if (empty ($parts[0]))
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
	assertUIntArg ('rack_id', __FUNCTION__);
	$rack_id = $_REQUEST['rack_id'];
	$rackData = getRackData ($rack_id);
	if ($rackData == NULL)
	{
		showError ('getRackData() failed', __FUNCTION__);
		return;
	}
	if (count ($rackData['mountedObjects']) == 0)
	{
		resetThumbCache ($rack_id);
		if (TRUE === commitDeleteRack ($rack_id))
			return buildRedirectURL (__FUNCTION__, 'OK', array ($rackData['name']), 'rackspace', 'default');
		else
			return buildRedirectURL (__FUNCTION__, 'ERR', array(), 'rackspace', 'default');
	}
	else
	{
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	}
}

$msgcode['updateRack']['OK'] = 68;
$msgcode['updateRack']['ERR'] = 177;
function updateRack ()
{
	assertUIntArg ('rack_id', __FUNCTION__);
	assertUIntArg ('rack_row_id', __FUNCTION__);
	assertUIntArg ('rack_height', __FUNCTION__);
	assertStringArg ('rack_name', __FUNCTION__);
	assertStringArg ('rack_comment', __FUNCTION__, TRUE);

	resetThumbCache ($_REQUEST['rack_id']);
	if (TRUE === commitUpdateRack ($_REQUEST['rack_id'], $_REQUEST['rack_name'], $_REQUEST['rack_height'], $_REQUEST['rack_row_id'], $_REQUEST['rack_comment']))
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['rack_name']));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

function updateRackDesign ()
{
	assertUIntArg ('rack_id', __FUNCTION__);
	if (($rackData = getRackData ($_REQUEST['rack_id'])) == NULL)
	{
		showError ('getRackData() failed', __FUNCTION__);
		return;
	}
	applyRackDesignMask($rackData);
	markupObjectProblems ($rackData);
	$response = processGridForm ($rackData, 'A', 'F');
	return buildWideRedirectURL (array($response));
}

function updateRackProblems ()
{
	assertUIntArg ('rack_id', __FUNCTION__);
	if (($rackData = getRackData ($_REQUEST['rack_id'])) == NULL)
	{
		showError ('getRackData() failed', __FUNCTION__);
		return;
	}
	applyRackProblemMask($rackData);
	markupObjectProblems ($rackData);
	$response = processGridForm ($rackData, 'F', 'U');
	return buildWideRedirectURL (array($response));
}

function querySNMPData ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	assertStringArg ('community', __FUNCTION__);
	require 'inc/snmp.php';
	return buildWideRedirectURL (doSNMPmining ($_REQUEST['object_id'], $_REQUEST['community']));
}

$msgcode['addFileWithoutLink']['OK'] = 69;
$msgcode['addFileWithoutLink']['ERR'] = 100;
// File-related functions
function addFileWithoutLink ()
{
	assertStringArg ('comment', __FUNCTION__, TRUE);

	// Make sure the file can be uploaded
	if (get_cfg_var('file_uploads') != 1)
		return buildRedirectURL (__FUNCTION__, 'ERR', array ("file uploads not allowed, change 'file_uploads' parameter in php.ini"));

	$fp = fopen($_FILES['file']['tmp_name'], 'rb');
	$error = commitAddFile ($_FILES['file']['name'], $_FILES['file']['type'], $_FILES['file']['size'], $fp, $_REQUEST['comment']);

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
	assertUIntArg ($bypass, __FUNCTION__);
	$entity_id = $_REQUEST[$bypass];
	assertStringArg ('comment', __FUNCTION__, TRUE);

	// Make sure the file can be uploaded
	if (get_cfg_var('file_uploads') != 1)
		return buildRedirectURL (__FUNCTION__, 'ERR2');

	$fp = fopen($_FILES['file']['tmp_name'], 'rb');
	$error = commitAddFile ($_FILES['file']['name'], $_FILES['file']['type'], $_FILES['file']['size'], $fp, $_REQUEST['comment']);
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
	assertUIntArg ('file_id', __FUNCTION__);
	global $page, $pageno, $etype_by_pageno;
	$entity_type = $etype_by_pageno[$pageno];
	$bypass_name = $page[$pageno]['bypass'];
	assertUIntArg ($bypass_name, __FUNCTION__);

	$fi = getFileInfo ($_REQUEST['file_id']);
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
	assertUIntArg ('file_id', __FUNCTION__);

	// Make sure the file can be uploaded
	if (get_cfg_var('file_uploads') != 1)
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	$shortInfo = getFileInfo ($sic['file_id']);

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
	assertUIntArg ('file_id', __FUNCTION__);
	assertStringArg ('file_name', __FUNCTION__);
	assertStringArg ('file_type', __FUNCTION__);
	assertStringArg ('file_comment', __FUNCTION__, TRUE);
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
	assertUIntArg ('link_id', __FUNCTION__);
	$error = commitUnlinkFile ($_REQUEST['link_id']);

	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));

	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['deleteFile']['OK'] = 73;
$msgcode['deleteFile']['ERR'] = 100;
function deleteFile ()
{
	assertUIntArg ('file_id', __FUNCTION__);
	$shortInfo = getFileInfo ($_REQUEST['file_id']);
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
	assertUIntArg ('file_id', __FUNCTION__);
	assertStringArg ('mtime_copy', __FUNCTION__);
	assertStringArg ('file_text', __FUNCTION__, TRUE); // it's Ok to save empty
	$shortInfo = getFileInfo ($_REQUEST['file_id']);
	if ($shortInfo['mtime'] != $_REQUEST['mtime_copy'])
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	global $sic;
	$error = commitReplaceFile ($sic['file_id'], $sic['file_text']);
	if ($error == '')
		return buildRedirectURL (__FUNCTION__, 'OK', array (htmlspecialchars ($shortInfo['name'])));
	return buildRedirectURL (__FUNCTION__, 'ERR2');
}

?>
