<?php
/*
*
*  This file is a library of operation handlers for RackTables.
*
*/

// This function assures that specified argument was passed
// and is a number greater than zero.
function assertUIntArg ($argname, $caller = 'N/A', $allow_zero = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if (!is_numeric ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is not a number (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if ($_REQUEST[$argname] < 0)
	{
		showError ("Parameter '${argname}' is less than zero (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if (!$allow_zero and $_REQUEST[$argname] == 0)
	{
		showError ("Parameter '${argname}' is equal to zero (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
}

// This function assures that specified argument was passed
// and is a non-empty string.
function assertStringArg ($argname, $caller = 'N/A', $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if (!is_string ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is not a string (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if (!$ok_if_empty and empty ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is an empty string (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
}

function assertBoolArg ($argname, $caller = 'N/A', $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if (!is_string ($_REQUEST[$argname]) or $_REQUEST[$argname] != 'on')
	{
		showError ("Parameter '${argname}' is not a string (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if (!$ok_if_empty and empty ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is an empty string (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
}

function assertIPv4Arg ($argname, $caller = 'N/A', $ok_if_empty = FALSE)
{
	assertStringArg ($argname, $caller, $ok_if_empty);
	if (!empty ($_REQUEST[$argname]) and long2ip (ip2long ($_REQUEST[$argname])) !== $_REQUEST[$argname])
	{
		showError ("IPv4 address validation failed for value '" . $_REQUEST[$argname] . "' (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
}

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
		return buildRedirectURL_OK ('NATv4 rule was successfully added.');
	else
		return buildRedirectURL_ERR ($error);
}

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
		return buildRedirectURL_OK ('NATv4 rule was successfully deleted.');
	else
		return buildRedirectURL_ERR ($error);
}

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
		return buildRedirectURL_OK ('NATv4 rule was successfully updated');
	else
		return buildRedirectURL_ERR ($error);
}

function addPortForObject ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	assertStringArg ('port_name', __FUNCTION__, TRUE);
	if (empty ($_REQUEST['port_name']))
		return buildRedirectURL_ERR ('Port name cannot be empty');
	$error = commitAddPort ($_REQUEST['object_id'], $_REQUEST['port_name'], $_REQUEST['port_type_id'], $_REQUEST['port_label'], $_REQUEST['port_l2address']);
	if ($error != '')
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ("Port ${_REQUEST['port_name']} was added successfully");
}

function editPortForObject ()
{
	assertUIntArg ('port_id', __FUNCTION__);
	assertStringArg ('name', __FUNCTION__, TRUE);
	if (empty ($_REQUEST['name']))
		return buildRedirectURL_ERR ('Port name cannot be empty');

	if (isset ($_REQUEST['reservation_comment']) and !empty ($_REQUEST['reservation_comment']))
		$port_rc = '"' . $_REQUEST['reservation_comment'] . '"';
	else
		$port_rc = 'NULL';
	$error = commitUpdatePort ($_REQUEST['port_id'], $_REQUEST['name'], $_REQUEST['label'], $_REQUEST['l2address'], $port_rc);
	if ($error != '')
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ("Port ${_REQUEST['name']} was updated successfully");
}

function delPortFromObject ()
{
	assertUIntArg ('port_id', __FUNCTION__);
	$port_name = $_REQUEST['port_name'];
	$error = delObjectPort ($_REQUEST['port_id']);

	if ($error != '')
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ("Port ${_REQUEST['port_name']} was deleted successfully");
}

function linkPortForObject ()
{
	assertUIntArg ('port_id', __FUNCTION__);
	assertUIntArg ('remote_port_id', __FUNCTION__);
	$object_id = $_REQUEST['object_id'];
	$port_name = $_REQUEST['port_name'];
	$remote_port_name = $_REQUEST['remote_port_name'];
	$remote_object_name = $_REQUEST['remote_object_name'];

	$error = linkPorts ($_REQUEST['port_id'], $_REQUEST['remote_port_id']);
	if ($error != '')
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ("Port $port_name successfully linked with port $remote_port_name at object $remote_object_name");
}

function unlinkPortForObject ()
{
	assertUIntArg ('port_id', __FUNCTION__);

	$error = unlinkPort ($_REQUEST['port_id']);
	if ($error != '')
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ("Port ${_REQUEST['port_name']} was successfully unlinked from ${_REQUEST['remote_port_name']}@${_REQUEST['remote_object_name']}");
}

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
				return buildRedirectURL_ERR ('Cannot process submitted data: unknown format code.');
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
			$result = commitUpdatePort ($port_id, $port['name'], $port['label'], $port['l2address']);
			if ($result == '')
				$updated_count++;
			else
				$error_count++;
		}
	}
	return buildRedirectURL_OK ("Added ${added_count} ports, updated ${updated_count} ports, encountered ${error_count} errors.");
}

function updIPv4Allocation ()
{
	assertIPv4Arg ('ip', __FUNCTION__);
	assertUIntArg ('object_id', __FUNCTION__);
	assertStringArg ('bond_name', __FUNCTION__, TRUE);
	assertStringArg ('bond_type', __FUNCTION__);

	$error = updateBond ($_REQUEST['ip'], $_REQUEST['object_id'], $_REQUEST['bond_name'], $_REQUEST['bond_type']);
	if ($error != '')
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ('allocation updated');
}

function delIPv4Allocation ()
{
	assertIPv4Arg ('ip', __FUNCTION__);
	assertUIntArg ('object_id', __FUNCTION__);

	$error = unbindIpFromObject ($_REQUEST['ip'], $_REQUEST['object_id']);
	if ($error != '')
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ('deallocated');
}

function addIPv4Allocation ()
{
	assertIPv4Arg ('ip', __FUNCTION__);
	assertUIntArg ('object_id', __FUNCTION__);
	assertStringArg ('bond_name', __FUNCTION__, TRUE);
	assertStringArg ('bond_type', __FUNCTION__);

	// Strip masklen.
	$ip = ereg_replace ('/[[:digit:]]+$', '', $_REQUEST['ip']);
	$error = bindIpToObject ($ip, $_REQUEST['object_id'], $_REQUEST['bond_name'], $_REQUEST['bond_type']);
	$address = getIPAddress ($ip);
	if ($address['exists'] and ($address['reserved'] == 'yes' or strlen ($address['name']) > 0))
	{
		$release = getConfigVar ('IPV4_AUTO_RELEASE');
		if ($release >= 1)
			$address['reserved'] = 'no';
		if ($release >= 2)
			$address['name'] = '';
		updateAddress ($ip, $address['name'], $address['reserved']);
	}
	if ($error != '')
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ('allocated');
}

function addIPv4Prefix ()
{
	assertStringArg ('range', __FUNCTION__);
	assertStringArg ('name', __FUNCTION__);

	$is_bcast = isset ($_REQUEST['is_bcast']) ? $_REQUEST['is_bcast'] : 'off';
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	$error = createIPv4Prefix ($_REQUEST['range'], $_REQUEST['name'], $is_bcast == 'on', $taglist);
	if ($error != '')
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ('IPv4 prefix successfully added');
}

function delIPv4Prefix ()
{
	assertUIntArg ('id', __FUNCTION__);
	$error = destroyIPv4Prefix ($_REQUEST['id']);
	if ($error != '')
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ('IPv4 prefix deleted');
}

function editRange ()
{
	assertUIntArg ('id', __FUNCTION__);
	assertStringArg ('name', __FUNCTION__);

	$error = updateRange ($_REQUEST['id'], $_REQUEST['name']);
	if ($error != '')
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ('IPv4 prefix updated');
}

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
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ('IPv4 address updated');
}

function createUser ()
{
	assertStringArg ('username', __FUNCTION__);
	assertStringArg ('realname', __FUNCTION__, TRUE);
	assertStringArg ('password', __FUNCTION__);
	$username = $_REQUEST['username'];
	$password = hash (PASSWORD_HASH, $_REQUEST['password']);
	$result = commitCreateUserAccount ($username, $_REQUEST['realname'], $password);
	if ($result == TRUE)
		return buildRedirectURL_OK ("User account ${username} created.");
	else
		return buildRedirectURL_ERR ("Error creating user account ${username}.");
}

function updateUser ()
{
	assertUIntArg ('user_id', __FUNCTION__);
	assertStringArg ('username', __FUNCTION__);
	assertStringArg ('realname', __FUNCTION__, TRUE);
	assertStringArg ('password', __FUNCTION__);
	$username = $_REQUEST['username'];
	$new_password = $_REQUEST['password'];
	$old_hash = getHashByID ($_REQUEST['user_id']);
	if ($old_hash == NULL)
		return buildRedirectURL_ERR ('getHashByID() failed');
	// Update user password only if provided password is not the same as current password hash.
	if ($new_password != $old_hash)
		$new_password = hash (PASSWORD_HASH, $new_password);
	$result = commitUpdateUserAccount ($_REQUEST['user_id'], $username, $_REQUEST['realname'], $new_password);
	if ($result == TRUE)
		return buildRedirectURL_OK ("User account ${username} updated.");
	else
		return buildRedirectURL_ERR ("Error updating user account ${username}.");
}

function enableUser ()
{
	assertUIntArg ('user_id', __FUNCTION__);
	if (commitEnableUserAccount ($_REQUEST['user_id'], 'yes') == TRUE)
		return buildRedirectURL_OK ('User account enabled.');
	else
		return buildRedirectURL_ERR ('Error enabling user account.');
}

function disableUser ()
{
	assertUIntArg ('user_id', __FUNCTION__);
	if ($_REQUEST['user_id'] == 1)
		$result = FALSE;
	else
		$result = commitEnableUserAccount ($_REQUEST['user_id'], 'no');
	if ($result == TRUE)
		return buildRedirectURL_OK ('User account disabled.');
	else
		return buildRedirectURL_ERR ('Error disabling user account.');
}

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
						showError ('oldCompatTable is invalid', __FUNCTION__);
						break;
				}
	if ($error_count == 0)
		return buildRedirectURL_OK ("${error_count} failures and ${success_count} successfull changes.");
	else
		return buildRedirectURL_ERR ("${error_count} failures and ${success_count} successfull changes.");
}

function updateDictionary ()
{
	assertUIntArg ('chapter_no', __FUNCTION__);
	assertUIntArg ('dict_key', __FUNCTION__);
	assertStringArg ('dict_value', __FUNCTION__);
	if (commitUpdateDictionary ($_REQUEST['chapter_no'], $_REQUEST['dict_key'], $_REQUEST['dict_value']) === TRUE)
		return buildRedirectURL_OK ('Update succeeded.');
	else
		return buildRedirectURL_ERR ('Update failed!');
}

function supplementDictionary ()
{
	assertUIntArg ('chapter_no', __FUNCTION__);
	assertStringArg ('dict_value', __FUNCTION__);
	if (commitSupplementDictionary ($_REQUEST['chapter_no'], $_REQUEST['dict_value']) === TRUE)
		return buildRedirectURL_OK ('Supplement succeeded.');
	else
		return buildRedirectURL_ERR ('Supplement failed!');
}

function reduceDictionary ()
{
	assertUIntArg ('chapter_no', __FUNCTION__);
	assertUIntArg ('dict_key', __FUNCTION__);
	if (commitReduceDictionary ($_REQUEST['chapter_no'], $_REQUEST['dict_key']) === TRUE)
		return buildRedirectURL_OK ('Reduction succeeded.');
	else
		return buildRedirectURL_ERR ('Reduction failed!');
}

function addChapter ()
{
	assertStringArg ('chapter_name', __FUNCTION__);
	if (commitAddChapter ($_REQUEST['chapter_name']) === TRUE)
		return buildRedirectURL_OK ('Chapter was added.');
	else
		return buildRedirectURL_ERR ('Error adding chapter.');
}

function updateChapter ()
{
	assertUIntArg ('chapter_no', __FUNCTION__);
	assertStringArg ('chapter_name', __FUNCTION__);
	if (commitUpdateChapter ($_REQUEST['chapter_no'], $_REQUEST['chapter_name']) === TRUE)
		return buildRedirectURL_OK ('Chapter was updated.');
	else
		return buildRedirectURL_ERR ('Error updating chapter.');
}

function delChapter ()
{
	assertUIntArg ('chapter_no', __FUNCTION__);
	if (commitDeleteChapter ($_REQUEST['chapter_no']))
		return buildRedirectURL_OK ('Chapter was deleted.');
	else
		return buildRedirectURL_ERR ('Error deleting chapter.');
}

function changeAttribute ()
{
	assertUIntArg ('attr_id', __FUNCTION__);
	assertStringArg ('attr_name', __FUNCTION__);
	if (commitUpdateAttribute ($_REQUEST['attr_id'], $_REQUEST['attr_name']))
		return buildRedirectURL_OK ('Rename successful.');
	else
		return buildRedirectURL_ERR ('Error renaming attribute.');
}

function createAttribute ()
{
	assertStringArg ('attr_name', __FUNCTION__);
	assertStringArg ('attr_type', __FUNCTION__);
	if (commitAddAttribute ($_REQUEST['attr_name'], $_REQUEST['attr_type']))
		return buildRedirectURL_OK ("Attribute '${_REQUEST['attr_name']}' created.");
	else
		return buildRedirectURL_ERR ('Error creating attribute.');
}

function deleteAttribute ()
{
	assertUIntArg ('attr_id', __FUNCTION__);
	if (commitDeleteAttribute ($_REQUEST['attr_id']))
		return buildRedirectURL_OK ('Attribute was deleted.');
	else
		return buildRedirectURL_ERR ('Error deleting attribute.');
}

function supplementAttrMap ()
{
	assertUIntArg ('attr_id', __FUNCTION__);
	assertUIntArg ('objtype_id', __FUNCTION__);
	assertUIntArg ('chapter_no', __FUNCTION__);
	if (commitSupplementAttrMap ($_REQUEST['attr_id'], $_REQUEST['objtype_id'], $_REQUEST['chapter_no']) === TRUE)
		return buildRedirectURL_OK ('Supplement succeeded.');
	else
		return buildRedirectURL_ERR ('Supplement failed!');
}

function reduceAttrMap ()
{
	assertUIntArg ('attr_id', __FUNCTION__);
	assertUIntArg ('objtype_id', __FUNCTION__);
	if (commitReduceAttrMap ($_REQUEST['attr_id'], $_REQUEST['objtype_id']) === TRUE)
		return buildRedirectURL_OK ('Reduction succeeded.');
	else
		return buildRedirectURL_ERR ("Reduction failed!");
}

function clearSticker ()
{
	assertUIntArg ('attr_id', __FUNCTION__);
	assertUIntArg ('object_id', __FUNCTION__);
	if (commitResetAttrValue ($_REQUEST['object_id'], $_REQUEST['attr_id']) === TRUE)
		return buildRedirectURL_OK ('Reset succeeded.');
	else
		return buildRedirectURL_ERR ('Reset failed!');
}

function updateObject ()
{
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
		return buildRedirectURL_ERR ('commitUpdateObject() failed');
	// Invalidate thumb cache of all racks objects could occupy.
	foreach (getResidentRacksData ($_REQUEST['object_id'], FALSE) as $rack_id)
		resetThumbCache ($rack_id);
	return buildRedirectURL_OK ('Update done');
}

function updateStickers ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	assertUIntArg ('num_attrs', __FUNCTION__);
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
		return buildRedirectURL_ERR ('One or more update(s) failed!');

	return buildRedirectURL_OK ('Update(s) succeeded.');
}

function useupPort ()
{
	assertUIntArg ('port_id', __FUNCTION__);
	if (commitUseupPort ($_REQUEST['port_id']) === TRUE)
		return buildRedirectURL_OK ('Reservation removed.');
	else
		return buildRedirectURL_ERR ('Error removing reservation!');
}

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
		return buildRedirectURL_ERR ('Update failed with error: ' . $error);

	return buildRedirectURL_OK ('Update succeeded.');
}

function resetUIConfig()
{
	setConfigVar ('default_port_type','24');
	setConfigVar ('MASSCOUNT','15');
	setConfigVar ('MAXSELSIZE','30');
	setConfigVar ('NAMEFUL_OBJTYPES','4,7,8');
	setConfigVar ('ROW_SCALE','2');
	setConfigVar ('PORTS_PER_ROW','12');
	setConfigVar ('IPV4_ADDRS_PER_PAGE','256');
	setConfigVar ('DEFAULT_RACK_HEIGHT','42');
	setConfigVar ('REQUIRE_ASSET_TAG_FOR','4,7,8');
	setConfigVar ('USER_AUTH_SRC','database');
	setConfigVar ('DEFAULT_SLB_VS_PORT','');
	setConfigVar ('DEFAULT_SLB_RS_PORT','');
	setConfigVar ('IPV4_PERFORMERS','1,4,7,8,12,14,445,447');
	setConfigVar ('NATV4_PERFORMERS','4,7,8');
	setConfigVar ('DETECT_URLS','no');
	setConfigVar ('RACK_PRESELECT_THRESHOLD','1');
	setConfigVar ('DEFAULT_IPV4_RS_INSERVICE','no');
	setConfigVar ('AUTOPORTS_CONFIG','4 = 1*33*kvm + 2*24*eth%u;15 = 1*446*kvm');
	setConfigVar ('SHOW_EXPLICIT_TAGS','yes');
	setConfigVar ('SHOW_IMPLICIT_TAGS','yes');
	setConfigVar ('SHOW_AUTOMATIC_TAGS','no');
	setConfigVar ('DEFAULT_OBJECT_TYPE','4');
	setConfigVar ('IPV4_AUTO_RELEASE','1');
	return buildRedirectURL_OK ('Reset complete');
}

// Add single record.
function addRealServer ()
{
	assertUIntArg ('pool_id', __FUNCTION__);
	assertIPv4Arg ('remoteip', __FUNCTION__);
	assertUIntArg ('rsport', __FUNCTION__);
	assertStringArg ('rsconfig', __FUNCTION__, TRUE);
	if (!addRStoRSPool (
		$_REQUEST['pool_id'],
		$_REQUEST['remoteip'],
		$_REQUEST['rsport'],
		getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'),
		$_REQUEST['rsconfig']
	))
		return buildRedirectURL_ERR ('addRStoRSPool() failed');
	else
		return buildRedirectURL_OK ('Real server was successfully added');
}

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
			default:
				return buildRedirectURL_ERR (__FUNCTION__ . ': invalid format requested');
				break;
		}
	}
	if ($nbad == 0 and $ngood > 0)
		return buildRedirectURL_OK ("Successfully added ${ngood} real servers");
	else
		return buildRedirectURL_ERR ("Added ${ngood} real servers and encountered ${nbad} errors");
}

function addVService ()
{
	assertIPv4Arg ('vip', __FUNCTION__);
	assertUIntArg ('vport', __FUNCTION__);
	assertStringArg ('proto', __FUNCTION__);
	if ($_REQUEST['proto'] != 'TCP' and $_REQUEST['proto'] != 'UDP')
		return buildRedirectURL_ERR (__FUNCTION__ . ': invalid protocol');
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
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ('Virtual service was successfully created');
}

function deleteRealServer ()
{
	assertUIntArg ('id', __FUNCTION__);
	if (!commitDeleteRS ($_REQUEST['id']))
		return buildRedirectURL_ERR ('commitDeleteRS() failed');
	else
		return buildRedirectURL_OK ('Real server was successfully deleted');
}

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
		return buildRedirectURL_ERR ('commitDeleteLB() failed');
	else
		return buildRedirectURL_OK ('Load balancer was successfully deleted');
}

function deleteVService ()
{
	assertUIntArg ('vs_id', __FUNCTION__);
	if (!commitDeleteVS ($_REQUEST['vs_id']))
		return buildRedirectURL_ERR ('commitDeleteVS() failed');
	else
		return buildRedirectURL_OK ('Virtual service was successfully deleted');
}

function updateRealServer ()
{
	assertUIntArg ('rs_id', __FUNCTION__);
	assertIPv4Arg ('rsip', __FUNCTION__);
	assertUIntArg ('rsport', __FUNCTION__);
	assertStringArg ('rsconfig', __FUNCTION__, TRUE);
	if (!commitUpdateRS (
		$_REQUEST['rs_id'],
		$_REQUEST['rsip'],
		$_REQUEST['rsport'],
		$_REQUEST['rsconfig']
	))
		return buildRedirectURL_ERR ('commitUpdateRS() failed');
	else
		return buildRedirectURL_OK ('Real server was successfully updated');
}

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
		return buildRedirectURL_ERR ('commitUpdateLB() failed');
	else
		return buildRedirectURL_OK ('Load balancer info was successfully updated');
}

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
		return buildRedirectURL_ERR ('commitUpdateVS() failed');
	else
		return buildRedirectURL_OK ('Virtual service was successfully updated');
}

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
		return buildRedirectURL_ERR ('addLBtoRSPool() failed');
	else
		return buildRedirectURL_OK ('Load balancer was successfully added');
}

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
		return buildRedirectURL_ERR ($error);
	else
		return buildRedirectURL_OK ('RS pool was successfully created');
}

function deleteRSPool ()
{
	assertUIntArg ('pool_id', __FUNCTION__);
	if (!commitDeleteRSPool ($_REQUEST['pool_id']))
		return buildRedirectURL_ERR ('commitDeleteRSPool() failed');
	else
		return buildRedirectURL_OK ('RS pool was successfully deleted');
}

function updateRSPool ()
{
	assertUIntArg ('pool_id', __FUNCTION__);
	assertStringArg ('name', __FUNCTION__, TRUE);
	assertStringArg ('vsconfig', __FUNCTION__, TRUE);
	assertStringArg ('rsconfig', __FUNCTION__, TRUE);
	if (!commitUpdateRSPool ($_REQUEST['pool_id'], $_REQUEST['name'], $_REQUEST['vsconfig'], $_REQUEST['rsconfig']))
		return buildRedirectURL_ERR ('commitUpdateRSPool() failed');
	else
		return buildRedirectURL_OK ('RS pool was successfully updated');
}

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
		return buildRedirectURL_OK ($ngood . " real server(s) were successfully (de)activated");
	else
		return buildRedirectURL_ERR ("Encountered ${nbad} errors, (de)activated ${ngood} real servers");
}

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
		return buildRedirectURL_OK ($ngood . " IP address(es) were successfully updated");
	else
		return buildRedirectURL_ERR ("Encountered ${nbad} errors, updated ${ngood} IP address(es)");
}

function generateAutoPorts ()
{
	global $pageno;
	assertUIntArg ('object_id', __FUNCTION__);
	$info = getObjectInfo ($_REQUEST['object_id']);
	// Navigate away in case of success, stay at the place otherwise.
	if (executeAutoPorts ($_REQUEST['object_id'], $info['objtype_id']))
		return buildRedirectURL_OK ('Generation complete', $pageno, 'ports');
	else
		return buildRedirectURL_ERR ('executeAutoPorts() failed');
}

// Filter out implicit tags before storing the new tag set.
function saveEntityTags ($realm, $bypass)
{
	global $explicit_tags, $implicit_tags;
	assertUIntArg ($bypass, __FUNCTION__);
	$entity_id = $_REQUEST[$bypass];
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	// Build a chain from the submitted data, minimize it,
	// then wipe existing records and store the new set instead.
	deleteTagsForEntity ($realm, $entity_id);
	$newchain = getExplicitTagsOnly (buildTagChainFromIds ($taglist));
	$n_succeeds = $n_errors = 0;
	foreach ($newchain as $taginfo)
		if (addTagForEntity ($realm, $entity_id, $taginfo['id']))
			$n_succeeds++;
		else
			$n_errors++;
	if ($n_errors)
		return buildRedirectURL_ERR ("Tried chaining ${n_succeeds} tags, but experienced ${n_errors} errors.");
	else
		return buildRedirectURL_OK ("Chained ${n_succeeds} tags");
}

function saveObjectTags ()
{
	return saveEntityTags ('object', 'object_id');
}

function saveIPv4PrefixTags ()
{
	return saveEntityTags ('ipv4net', 'id');
}

function saveRackTags ()
{
	return saveEntityTags ('rack', 'rack_id');
}

function saveIPv4VSTags ()
{
	return saveEntityTags ('ipv4vs', 'vs_id');
}

function saveIPv4RSPoolTags ()
{
	return saveEntityTags ('ipv4rspool', 'pool_id');
}

function saveUserTags ()
{
	return saveEntityTags ('user', 'user_id');
}

function destroyTag ()
{
	assertUIntArg ('tag_id', __FUNCTION__);
	if (($ret = commitDestroyTag ($_REQUEST['tag_id'])) == '')
		return buildRedirectURL_OK ('Successfully deleted tag.');
	else
		return buildRedirectURL_ERR ("Error deleting tag: '${ret}'");
}

function createTag ()
{
	assertStringArg ('tag_name', __FUNCTION__);
	assertUIntArg ('parent_id', __FUNCTION__, TRUE);
	$tagname = trim ($_REQUEST['tag_name']);
	if (!validTagName ($tagname))
		return buildRedirectURL_ERR ("Invalid tag name '${tagname}'");
	if (tagExistsInDatabase ($tagname))
		return buildRedirectURL_ERR ("Tag '${tagname}' (or similar name) already exists");
	if (($parent_id = $_REQUEST['parent_id']) <= 0)
		$parent_id = 'NULL';
	if (($ret = commitCreateTag ($tagname, $parent_id)) == '')
		return buildRedirectURL_OK ("Created tag '${tagname}'.");
	else
		return buildRedirectURL_ERR ("Could not create tag '${tagname}' because of error '${ret}'");
}

function updateTag ()
{
	assertUIntArg ('tag_id', __FUNCTION__);
	assertUIntArg ('parent_id', __FUNCTION__, TRUE);
	assertStringArg ('tag_name', __FUNCTION__);
	$tagname = trim ($_REQUEST['tag_name']);
	if (!validTagName ($tagname))
		return buildRedirectURL_ERR ("Invalid tag name '${tagname}'");
	if (($parent_id = $_REQUEST['parent_id']) <= 0)
		$parent_id = 'NULL';
	if (($ret = commitUpdateTag ($_REQUEST['tag_id'], $tagname, $parent_id)) == '')
		return buildRedirectURL_OK ("Updated tag '${tagname}'.");
	else
		return buildRedirectURL_ERR ("Could not update tag '${tagname}' because of error '${ret}'");
}

function rollTags ()
{
	assertUIntArg ('row_id', __FUNCTION__);
	assertStringArg ('sum', __FUNCTION__, TRUE);
	assertUIntArg ('realsum', __FUNCTION__);
	$row_id = $_REQUEST['row_id'];
	if ($_REQUEST['sum'] != $_REQUEST['realsum'])
		return buildRedirectURL_ERR ('Turing test failed');
	$racks = getRacksForRow ($row_id);
	// Each time addTagForEntity() fails we assume it was just because of already existing record in its way.
	$newtags = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	$tagstack = getExplicitTagsOnly (buildTagChainFromIds ($newtags));
	$ndupes = $nnew = 0;
	foreach ($tagstack as $taginfo)
		foreach ($racks as $rackInfo)
		{
			if (addTagForEntity ('rack', $rackInfo['id'], $taginfo['id']))
				$nnew++;
			else
				$ndupes++;
			// FIXME: do something likewise for all object inside current rack
		}
	return buildRedirectURL_OK ("${nnew} new records done, ${ndupes} already existed");
}

function changeMyPassword ()
{
	global $accounts, $remote_username;
	if (getConfigVar ('USER_AUTH_SRC') != 'database')
		return buildRedirectURL_ERR ('Can only change password under DB authentication.');
	assertStringArg ('oldpassword', __FUNCTION__);
	assertStringArg ('newpassword1', __FUNCTION__);
	assertStringArg ('newpassword2', __FUNCTION__);
	if ($accounts[$remote_username]['user_password_hash'] != hash (PASSWORD_HASH, $_REQUEST['oldpassword']))
		return buildRedirectURL_ERR ('Old password doesn\'t match.');
	if ($_REQUEST['newpassword1'] != $_REQUEST['newpassword2'])
		return buildRedirectURL_ERR ('New passwords don\'t match.');
	if (commitUpdateUserAccount ($accounts[$remote_username]['user_id'], $accounts[$remote_username]['user_name'], $accounts[$remote_username]['user_realname'], hash (PASSWORD_HASH, $_REQUEST['newpassword1'])))
		return buildRedirectURL_OK ('Password changed successfully.');
	else
		return buildRedirectURL_ERR ('Password change failed.');
}

function saveRackCode ()
{
	assertStringArg ('rackcode');
	// For the test to succeed, unescape LFs, strip CRs.
	$newcode = str_replace ('\r', '', str_replace ('\n', "\n", $_REQUEST['rackcode']));
	$parseTree = getRackCode ($newcode);
	if ($parseTree['result'] != 'ACK')
		return buildRedirectURL_ERR ('Verification failed: ' . $parseTree['load']);
	saveScript ('RackCodeCache', '');
	if (saveScript ('RackCode', $newcode))
		return buildRedirectURL_OK ('Saved successfully.');
	else
		return buildRedirectURL_ERR ('Save failed.');
}

// This handler's context is pre-built, but not authorized. It is assumed, that the
// handler will take existing context and before each commit check authorization
// on the base chain plus necessary context added.
function setPortVLAN ()
{
	assertUIntArg ('portcount', __FUNCTION__);
	$data = getSwitchVLANs ($_REQUEST['object_id']);
	if ($data === NULL)
		return buildRedirectURL_ERR ('getSwitchVLANs() failed');
	list ($vlanlist, $portlist) = $data;
	// Here we just build up 1 set command for the gateway with all of the ports
	// included. The gateway is expected to filter unnecessary changes silently
	// and to provide a list of responses with either error or success message
	// for each of the rest.
	$nports = $_REQUEST['portcount'];
	$prefix = 'set ';
	$log = array();
	$setcmd = '';
	for ($i = 0; $i < $nports; $i++)
		if
		(
			!isset ($_REQUEST['portname_' . $i]) ||
			!isset ($_REQUEST['vlanid_' . $i]) ||
			$_REQUEST['portname_' . $i] != $portlist[$i]['portname']
		)
			$log[] = array ('code' => 'error', 'message' => "Ignoring malformed record #${i} in form submit");
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
				$log[] = array ('code' => 'error', 'message' => "Permission denied moving port ${portname} from VLAN${oldvlanid} to VLAN${newvlanid}");
				continue;
			}
			$setcmd .= $prefix . $portname . '=' . $newvlanid;
			$prefix = ';';
		}
	// Feed the gateway and interpret its (non)response.
	if ($setcmd != '')
		$log = array_merge ($log, setSwitchVLANs ($_REQUEST['object_id'], $setcmd));
	else
		$log[] = array ('code' => 'warning', 'message' => 'nothing happened...');
	return buildWideRedirectURL ($log);
}

?>
