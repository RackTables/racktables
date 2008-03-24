<?php
/*
*
*  This file is a library of operation handlers for RackTables.
*
*/

// This function assures that specified argument was passed
// and is a number greater than zero.
function assertUIntArg ($argname, $allow_zero = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing.", __FUNCTION__);
		die();
	}
	if (!is_numeric ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is not a number.", __FUNCTION__);
		die();
	}
	if ($_REQUEST[$argname] < 0)
	{
		showError ("Parameter '${argname}' is less than zero.", __FUNCTION__);
		die();
	}
	if (!$allow_zero and $_REQUEST[$argname] == 0)
	{
		showError ("Parameter '${argname}' is equal to zero.", __FUNCTION__);
		die();
	}
}

// This function assures that specified argument was passed
// and is a non-empty string.
function assertStringArg ($argname, $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing.", __FUNCTION__);
		die();
	}
	if (!is_string ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is not a string.", __FUNCTION__);
		die();
	}
	if (!$ok_if_empty and empty ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is an empty string.", __FUNCTION__);
		die();
	}
}

function assertBoolArg ($argname, $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing.", __FUNCTION__);
		die();
	}
	if (!is_string ($_REQUEST[$argname]) or $_REQUEST[$argname] != 'on')
	{
		showError ("Parameter '${argname}' is not a string.", __FUNCTION__);
		die();
	}
	if (!$ok_if_empty and empty ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is an empty string.", __FUNCTION__);
		die();
	}
}

function assertIPv4Arg ($argname, $ok_if_empty = FALSE)
{
	assertStringArg ($argname, $ok_if_empty);
	if (!empty ($_REQUEST[$argname]) and long2ip (ip2long ($_REQUEST[$argname])) !== $_REQUEST[$argname])
	{
		showError ("IPv4 address validation failed for value '" . $_REQUEST[$argname] . "'", __FUNCTION__);
		die();
	}
}

function addPortForwarding ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('object_id');
	assertIPv4Arg ('localip');
	assertIPv4Arg ('remoteip');
	assertUIntArg ('localport');
	assertUIntArg ('proto');
	assertStringArg ('description', TRUE);
	$object_id = $_REQUEST['object_id'];
	$localip = $_REQUEST['localip'];
	$remoteip = $_REQUEST['remoteip'];
	$localport = $_REQUEST['localport'];
	$remoteport = $_REQUEST['remoteport'];
	$proto = $_REQUEST['proto'];
	$description = $_REQUEST['description'];
	if (empty ($remoteport))
		$remoteport = $localport;

	$retpage="${root}?page=${pageno}&tab=${tabno}&object_id=$object_id";


	$error=newPortForwarding($object_id, $localip, $localport, $remoteip, $remoteport, $proto, $description);

	if ($error == '')
		return "${retpage}&message=".urlencode('Port forwarding successfully added.');
	else
	{
		return "${retpage}&error=".urlencode($error);
	}
	
}

function delPortForwarding ()
{
	global $root, $pageno, $tabno;

	$object_id = $_REQUEST['object_id'];
	$localip = $_REQUEST['localip'];
	$remoteip = $_REQUEST['remoteip'];
	$localport = $_REQUEST['localport'];
	$remoteport = $_REQUEST['remoteport'];
	$proto = $_REQUEST['proto'];

	$retpage="${root}?page=${pageno}&tab=${tabno}&object_id=$object_id";

	$error=deletePortForwarding($object_id, $localip, $localport, $remoteip, $remoteport, $proto);
	if ($error == '')
		return "${retpage}&message=".urlencode('Port forwarding successfully deleted.');
	else
	{
		return "${retpage}&error=".urlencode($error);
	}
	
}

function updPortForwarding ()
{
	global $root, $pageno, $tabno;

	$object_id = $_REQUEST['object_id'];
	$localip = $_REQUEST['localip'];
	$remoteip = $_REQUEST['remoteip'];
	$localport = $_REQUEST['localport'];
	$remoteport = $_REQUEST['remoteport'];
	$proto = $_REQUEST['proto'];
	$description = $_REQUEST['description'];

	$retpage="${root}?page=${pageno}&tab=${tabno}&object_id=$object_id";

	$error=updatePortForwarding($object_id, $localip, $localport, $remoteip, $remoteport, $proto, $description);
	if ($error == '')
		return "${retpage}&message=".urlencode('Port forwarding successfully updated.');
	else
	{
		return "${retpage}&error=".urlencode($error);
	}
	
}

function addPortForObject ()
{
	global $root, $pageno, $tabno;

	$object_id = $_REQUEST['object_id'];
	$port_name = $_REQUEST['port_name'];
	$port_l2address = $_REQUEST['port_l2address'];
	$port_label = $_REQUEST['port_label'];
	$port_type_id = $_REQUEST['port_type_id'];


	if ($port_name == '')
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=".urlencode('Port name cannot be empty');
	else
	{
		$error = commitAddPort ($object_id, $port_name, $port_type_id, $port_label, $port_l2address);
		if ($error != '')
		{
			return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=".urlencode($error);
		}
		else
		{
			return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&message=".urlencode("Port $port_name added successfully");
		}
	}
	
}

function editPortForObject ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('port_id');
	assertUIntArg ('object_id');
	assertStringArg ('name');
	$port_id = $_REQUEST['port_id'];
	$object_id = $_REQUEST['object_id'];
	$port_name = $_REQUEST['name'];
	$port_l2address = $_REQUEST['l2address'];
	$port_label = $_REQUEST['label'];
	if (isset ($_REQUEST['reservation_comment']) and !empty ($_REQUEST['reservation_comment']))
		$port_rc = '"' . $_REQUEST['reservation_comment'] . '"';
	else
		$port_rc = 'NULL';

	if ($port_name == '')
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=".urlencode('Port name cannot be empty');
	else
	{
		$error = commitUpdatePort ($port_id, $port_name, $port_label, $port_l2address, $port_rc);
		if ($error != '')
		{
			return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=".urlencode($error);
		}
		else
		{
			return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&message=".urlencode("Port $port_name updated successfully");
		}
	}
	
}

function delPortFromObject ()
{
	global $root, $pageno, $tabno;

	$port_id = $_REQUEST['port_id'];
	$port_name = $_REQUEST['port_name'];
	$object_id = $_REQUEST['object_id'];
	$error = delObjectPort($port_id);

	if ($error != '')
	{
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=".urlencode($error);
	}
	else
	{
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&message=".urlencode("Port $port_name deleted successfully");
	}
}

function linkPortForObject ()
{
	global $root, $pageno, $tabno;

	$port_id = $_REQUEST['port_id'];
	$remote_port_id = $_REQUEST['remote_port_id'];
	$object_id = $_REQUEST['object_id'];
	$port_name = $_REQUEST['port_name'];
	$remote_port_name = $_REQUEST['remote_port_name'];
	$remote_object_name = $_REQUEST['remote_object_name'];

	$error = linkPorts($port_id, $remote_port_id);
	if ($error != '')
	{
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=".urlencode($error);
	}
	else
	{
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&message=".urlencode("Port $port_name successfully linked with port $remote_port_name at object $remote_object_name");
	}
}

function unlinkPortForObject ()
{
	global $root, $pageno, $tabno;

	$port_id = $_REQUEST['port_id'];
	$object_id = $_REQUEST['object_id'];
	$port_name = $_REQUEST['port_name'];
	$remote_port_name = $_REQUEST['remote_port_name'];
	$remote_object_name = $_REQUEST['remote_object_name'];

	$error = unlinkPort($port_id);
	if ($error != '')
	{
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=".urlencode($error);
	}
	else
	{
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&message=".urlencode("Port $port_name successfully unlinked from port $remote_port_name at object $remote_object_name");
	}
}

function addMultiPorts ()
{
	global $root, $pageno, $tabno;
	// Parse data.
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
				return
					"${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=" .
					urlencode('Cannot process submitted data: unknown format code.');
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
	return
		"${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&message=" .
		urlencode("Added ${added_count} ports, updated ${updated_count} ports, encountered ${error_count} errors.");
}

function editAddressFromObject ()
{
	global $root;

	$ip = $_REQUEST['ip'];
	$object_id = $_REQUEST['object_id'];
	$name = $_REQUEST['bond_name'];
	$type = $_REQUEST['bond_type'];
	$error = updateBond($ip, $object_id, $name, $type);
	if ($error != '')
	{
		return "${root}?page=object&tab=network&object_id=$object_id&error=".urlencode($error);
	}
	else
	{
		return "${root}?page=object&tab=network&object_id=$object_id&message=".urlencode("Interface successfully updated");
	}
}

function delAddressFromObject ()
{
	global $root;

	$ip = $_REQUEST['ip'];
	$object_id = $_REQUEST['object_id'];
	$error = unbindIpFromObject($ip, $object_id);
	if ($error != '')
	{
		return "${root}?page=object&tab=network&object_id=$object_id&error=".urlencode($error);
	}
	else
	{
		return "${root}?page=object&tab=network&object_id=$object_id&message=".urlencode("Interface successfully deleted");
	}
}

function delIpAssignment ()
{
	global $root;

	$ip = $_REQUEST['ip'];
	$object_id = $_REQUEST['object_id'];
	$error = unbindIpFromObject($ip, $object_id);
	if ($error != '')
	{
		return "${root}?page=ipaddress&tab=assignment&ip=$ip&error=".urlencode($error);
	}
	else
	{
		return "${root}?page=ipaddress&tab=assignment&ip=$ip&message=".urlencode("Interface successfully deleted");
	}
}

function editIpAssignment ()
{
	global $root;

	$ip = $_REQUEST['ip'];
	$object_id = $_REQUEST['object_id'];
	$name = $_REQUEST['bond_name'];
	$type = $_REQUEST['bond_type'];
	$error = updateBond($ip, $object_id, $name, $type);

	if ($error != '')
	{
		return "${root}?page=ipaddress&tab=assignment&ip=$ip&error=".urlencode($error);
	}
	else
	{
		return "${root}?page=ipaddress&tab=assignment&ip=$ip&message=".urlencode("Interface successfully updated");
	}
}

function addIpAssignment ()
{
	global $root;

	$ip = $_REQUEST['ip'];
	$object_id = $_REQUEST['object_id'];
	$name = $_REQUEST['bond_name'];
	$type = $_REQUEST['bond_type'];
	$error = bindIpToObject($ip, $object_id, $name, $type);
	if ($error != '')
	{
		return "${root}?page=ipaddress&tab=assignment&ip=$ip&error=".urlencode($error);
	}
	else
	{
		return "${root}?page=ipaddress&tab=assignment&ip=$ip&message=".urlencode("Interface successfully added");
	}
}

function addNewRange ()
{
	global $root, $pageno, $tabno;

	$range = $_REQUEST['range'];
	$name = $_REQUEST['name'];
	$is_bcast = $_REQUEST['is_bcast'];
	$error = addRange($range, $name, $is_bcast == 'on');
	if ($error != '')
	{
		return "${root}?page=${pageno}&tab=${tabno}&error=".urlencode($error);
	}
	else
	{
		return "${root}?page=${pageno}&tab=${tabno}&message=".urlencode("Range successfully added");
	}
}

function editRange ()
{
	global $root, $pageno, $tabno;

	$id = $_REQUEST['id'];
	$name = $_REQUEST['name'];
	$error = updateRange($id, $name);
	if ($error != '')
	{
		return "${root}?page=${pageno}&tab=${tabno}&id=$id&error=".urlencode($error);
	}
	else
	{
		return "${root}?page=${pageno}&tab=${tabno}&id=$id&message=".urlencode("Range updated");
	}

}

function delRange ()
{
	global $root, $pageno, $tabno;

	$id = $_REQUEST['id'];
	$error = commitDeleteRange ($id);
	if ($error != '')
	{
		return "${root}?page=${pageno}&tab=${tabno}&error=".urlencode($error);
	}
	else
	{
		return "${root}?page=${pageno}&tab=${tabno}&message=".urlencode("Range deleted");
	}

}

function editAddress ()
{
	global $root, $pageno, $tabno;

	$ip = $_REQUEST['ip'];
	$name = $_REQUEST['name'];
	if (isset ($_REQUEST['reserved']))
		$reserved = $_REQUEST['reserved'];
	else
		$reserved = 'off';
	$error = updateAddress($ip, $name, $reserved=='on'?'yes':'no');
	if ($error != '')
	{
		return "${root}?page=${pageno}&tab=${tabno}&ip=$ip&error=".urlencode($error);
	}
	else
	{
		return "${root}?page=${pageno}&tab=${tabno}&ip=$ip&message=".urlencode("Address updated");
	}

}

function addAddressToObject ()
{
	global $root, $pageno, $tabno;

	assertStringArg ('ip');
	assertUIntArg ('object_id');
	assertStringArg ('name', TRUE);
	assertStringArg ('type');
	// Strip masklen.
	$ip = ereg_replace ('/[[:digit:]]+$', '', $_REQUEST['ip']);
	$object_id = $_REQUEST['object_id'];
	$error = bindIpToObject($ip, $object_id, $_REQUEST['name'], $_REQUEST['type']);
	if ($error != '')
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=".urlencode($error);
	else
		return "${root}?page=$pageno&tab=${tabno}&object_id=${object_id}&message=".
			urlencode("Address ${ip} was added successfully.");
}

function createUserAccount ()
{
	global $root, $pageno, $tabno;
	assertStringArg ('username');
	assertStringArg ('realname', TRUE);
	assertStringArg ('password');
	$username = $_REQUEST['username'];
	$password = hash (PASSWORD_HASH, $_REQUEST['password']);
	$result = commitCreateUserAccount ($username, $_REQUEST['realname'], $password);
	if ($result == TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("User account ${username} created.");
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Error creating user account ${username}.");
}

function updateUserAccount ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('id');
	assertStringArg ('username');
	assertStringArg ('realname', TRUE);
	assertStringArg ('password');
	// We might be asked to change username, so use user ID only.
	$id = $_REQUEST['id'];
	$username = $_REQUEST['username'];
	$new_password = $_REQUEST['password'];
	$old_hash = getHashByID ($id);
	if ($old_hash == NULL)
	{
		showError ('getHashByID() failed', __FUNCTION__);
		return;
	}
	// Update user password only if provided password is not the same as current password hash.
	if ($new_password != $old_hash)
		$new_password = hash (PASSWORD_HASH, $new_password);
	$result = commitUpdateUserAccount ($_REQUEST['id'], $username, $_REQUEST['realname'], $new_password);
	if ($result == TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("User account ${username} updated.");
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Error updating user account ${username}.");
}

function enableUserAccount ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('id');
	$id = $_REQUEST['id'];
	$result = commitEnableUserAccount ($id, 'yes');
	if ($result == TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("User account enabled.");
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Error enabling user account.");
}

function disableUserAccount ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('id');
	$id = $_REQUEST['id'];
	if ($id == 1)
		$result = FALSE;
	else
		$result = commitEnableUserAccount ($id, 'no');
	if ($result == TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("User account disabled.");
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Error disabling user account.");
}

function revokePermission ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('access_userid', TRUE);
	assertStringArg ('access_page');
	assertStringArg ('access_tab');
	$result = commitRevokePermission
	(
		$_REQUEST['access_userid'],
		$_REQUEST['access_page'],
		$_REQUEST['access_tab']
	);
	if ($result == TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Revoke successfull.");
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Error revoking permission.");
}

function grantPermission ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('access_userid', TRUE);
	assertStringArg ('access_page');
	assertStringArg ('access_tab');
	assertStringArg ('access_value');
	$result = commitGrantPermission
	(
		$_REQUEST['access_userid'],
		$_REQUEST['access_page'],
		$_REQUEST['access_tab'],
		$_REQUEST['access_value']
	);
	if ($result == TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Grant successfull.");
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Error granting permission.");
}

// This function find differences in users's submit and PortCompat table
// and modifies database accordingly.
function savePortMap ()
{
	global $root, $pageno, $tabno;
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
	return
		"${root}?page=${pageno}&tab=${tabno}&" .
		($error_count == 0 ? 'message' : 'error') .
		"=" . urlencode ("${error_count} failures and ${success_count} successfull changes.");
}

function deleteDictWord ()
{
	global $root, $pageno, $tabno;
	return
		"${root}?page=${pageno}&tab=${tabno}&" .
		"error=" . urlencode ('Dragon ate this word!');
}

function updateDictionary ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('chapter_no');
	assertUIntArg ('dict_key');
	assertStringArg ('dict_value');
	if (commitUpdateDictionary ($_REQUEST['chapter_no'], $_REQUEST['dict_key'], $_REQUEST['dict_value']) === TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ('Update succeeded.');
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Update failed!");
}

function supplementDictionary ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('chapter_no');
	assertStringArg ('dict_value');
	if (commitSupplementDictionary ($_REQUEST['chapter_no'], $_REQUEST['dict_value']) === TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ('Supplement succeeded.');
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Supplement failed!");
}

function reduceDictionary ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('chapter_no');
	assertUIntArg ('dict_key');
	if (commitReduceDictionary ($_REQUEST['chapter_no'], $_REQUEST['dict_key']) === TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ('Reduction succeeded.');
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Reduction failed!");
}

function addChapter ()
{
	global $root, $pageno, $tabno;
	assertStringArg ('chapter_name');
	if (commitAddChapter ($_REQUEST['chapter_name']) === TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ('Chapter was added.');
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('Error adding chapter.');
}

function updateChapter ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('chapter_no');
	assertStringArg ('chapter_name');
	if (commitUpdateChapter ($_REQUEST['chapter_no'], $_REQUEST['chapter_name']) === TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ('Chapter was updated.');
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('Error updating chapter.');
}

function delChapter ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('chapter_no');
	if (commitDeleteChapter ($_REQUEST['chapter_no']))
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ('Chapter was deleted.');
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('Error deleting chapter.');
}

function changeAttribute ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('attr_id');
	assertStringArg ('attr_name');
	if (commitUpdateAttribute ($_REQUEST['attr_id'], $_REQUEST['attr_name']))
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ('Rename successful.');
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('Error renaming attribute.');
}

function createAttribute ()
{
	global $root, $pageno, $tabno;
	assertStringArg ('attr_name');
	assertStringArg ('attr_type');
	if (commitAddAttribute ($_REQUEST['attr_name'], $_REQUEST['attr_type']))
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Attribute '${_REQUEST['attr_name']}' created.");
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('Error creating attribute.');
}

function deleteAttribute ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('attr_id');
	if (commitDeleteAttribute ($_REQUEST['attr_id']))
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ('Attribute was deleted.');
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('Error deleting attribute.');
}

function supplementAttrMap ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('attr_id');
	assertUIntArg ('objtype_id');
	assertUIntArg ('chapter_no');
	if (commitSupplementAttrMap ($_REQUEST['attr_id'], $_REQUEST['objtype_id'], $_REQUEST['chapter_no']) === TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ('Supplement succeeded.');
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Supplement failed!");
}

function reduceAttrMap ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('attr_id');
	assertUIntArg ('objtype_id');
	if (commitReduceAttrMap ($_REQUEST['attr_id'], $_REQUEST['objtype_id']) === TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ('Reduction succeeded.');
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Reduction failed!");
}

function resetAttrValue ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('attr_id');
	assertUIntArg ('object_id');
	$object_id = $_REQUEST['object_id'];
	if (commitResetAttrValue ($object_id, $_REQUEST['attr_id']) === TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&message=" . urlencode ('Reset succeeded.');
	else
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=" . urlencode ("Reset failed!");
}

function updateAttrValues ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('object_id');
	$object_id = $_REQUEST['object_id'];
	$oldvalues = getAttrValues ($object_id);

	assertUIntArg ('num_attrs');
	$num_attrs = $_REQUEST['num_attrs'];
	$result = array();

	for ($i = 0; $i < $num_attrs; $i++)
	{
		assertUIntArg ("${i}_attr_id");
		$attr_id = $_REQUEST["${i}_attr_id"];

		// Field is empty, delete attribute and move on.
		if (empty($_REQUEST["${i}_value"]))
		{
			commitResetAttrValue ($object_id, $attr_id);
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
				showError ('Internal structure error', __FUNCTION__);
				die;
		}
		if ($value == $oldvalue)
			continue;

		// Note if the queries succeed or not, it determines which page they see.
		$result[] = commitUpdateAttrValue ($object_id, $attr_id, $value);
	}

	if (in_array(false, $result))
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=" . urlencode ("Update failed!");

	return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&message=" . urlencode ('Update succeeded.');
}

function useupPort ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('port_id');
	assertUIntArg ('object_id');
	$object_id = $_REQUEST['object_id'];
	if (commitUseupPort ($_REQUEST['port_id']) === TRUE)
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&message=" . urlencode ('Reservation removed.');
	else
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=" . urlencode ("Error removing reservation!");
}

function updateUI ()
{
	global $root, $pageno, $tabno;
	$oldvalues = loadConfigCache();

	assertUIntArg ('num_vars');
	$num_vars = $_REQUEST['num_vars'];
	$error = '';

	for ($i = 0; $i < $num_vars; $i++)
	{
		assertStringArg ("${i}_varname");
		assertStringArg ("${i}_varvalue", TRUE);
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
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Update failed with error: " . $error);

	return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Update succeeded.");
}

function resetUIConfig()
{
	global $root, $pageno, $tabno;
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
	return "${root}?page=${pageno}&tab=default&message=" . urlencode ("Reset complete");
}

// Add single record.
function addRealServer ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('id');
	assertIPv4Arg ('remoteip');
	assertUIntArg ('rsport');
	assertStringArg ('rsconfig', TRUE);
	$pool_id = $_REQUEST['id'];
	if (!addRStoRSPool ($pool_id, $_REQUEST['remoteip'], $_REQUEST['rsport'], getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), $_REQUEST['rsconfig']))
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&error=" . urlencode ('addRStoRSPool() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&message=" . urlencode ("Real server was successfully added");
}

// Parse textarea submitted and try adding a real server for each line.
function addRealServers ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('id');
	assertStringArg ('format');
	assertStringArg ('rawtext');
	$pool_id = $_REQUEST['id'];
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
				if (addRStoRSPool ($pool_id, $match[1], $match[2], getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), ''))
					$ngood++;
				else
					$nbad++;
				break;
			case 'ipvs_3': // address, port and weight
				if (!preg_match ('/^  -&gt; ([0-9\.]+):([0-9]+) +[a-zA-Z]+ +([0-9]+) /', $line, $match))
					continue;
				if (addRStoRSPool ($pool_id, $match[1], $match[2], getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), 'weight ' . $match[3]))
					$ngood++;
				else
					$nbad++;
				break;
			case 'ssv_2': // IP address and port
				if (!preg_match ('/^([0-9\.]+) ([0-9]+)$/', $line, $match))
					continue;
				if (addRStoRSPool ($pool_id, $match[1], $match[2], getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), ''))
					$ngood++;
				else
					$nbad++;
				break;
			default:
				return "${root}?page=${pageno}&tab=${tabno}&id=${pool_id}&error=" . urlencode (__FUNCTION__ . ': invalid format requested');
				break;
		}
	}
	if ($nbad == 0 and $ngood > 0)
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&message=" . urlencode ("Successfully added ${ngood} real servers");
	else
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&error=" . urlencode ("Added ${ngood} real servers and encountered ${nbad} errors");
}

function addVService ()
{
	global $root, $pageno, $tabno;

	assertIPv4Arg ('vip');
	assertUIntArg ('vport');
	assertStringArg ('proto');
	$proto = $_REQUEST['proto'];
	if ($proto != 'TCP' and $proto != 'UDP')
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode (__FUNCTION__ . ': invalid protocol');
	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	$pool_id = $_REQUEST['id'];
	if (!commitCreateVS ($_REQUEST['vip'], $_REQUEST['vport'], $proto, $_REQUEST['name'], $_REQUEST['vsconfig'], $_REQUEST['rsconfig']))
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('commitCreateVS() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Virtual service was successfully created");
}

function deleteRealServer ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('pool_id');
	assertUIntArg ('id');
	$pool_id = $_REQUEST['pool_id'];
	if (!commitDeleteRS ($_REQUEST['id']))
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&error=" . urlencode ('commitDeleteRS() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&message=" . urlencode ("Real server was successfully deleted");
}

function deleteLoadBalancer ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('object_id');
	assertUIntArg ('pool_id');
	assertUIntArg ('vs_id');
	$pool_id = $_REQUEST['pool_id'];
	if (!commitDeleteLB ($_REQUEST['object_id'], $pool_id, $_REQUEST['vs_id']))
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&error=" . urlencode ('commitDeleteLB() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&message=" . urlencode ("Load balancer was successfully deleted");
}

function deleteVService ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('id');
	if (!commitDeleteVS ($_REQUEST['id']))
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('commitDeleteVS() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Virtual service was successfully deleted");
}

function updateRealServer ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('id');
	assertUIntArg ('rs_id');
	assertIPv4Arg ('rsip');
	assertUIntArg ('rsport');
	assertStringArg ('rsconfig', TRUE);
	// only necessary for generating next URL
	$pool_id = $_REQUEST['id'];
	if (!commitUpdateRS ($_REQUEST['rs_id'], $_REQUEST['rsip'], $_REQUEST['rsport'], $_REQUEST['rsconfig']))
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&error=" . urlencode ('commitUpdateRS() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&message=" . urlencode ("Real server was successfully updated");
}

function updateLoadBalancer ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('object_id');
	assertUIntArg ('pool_id');
	assertUIntArg ('vs_id');
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	$pool_id = $_REQUEST['pool_id'];
	if (!commitUpdateLB ($_REQUEST['object_id'], $pool_id, $_REQUEST['vs_id'], $_REQUEST['vsconfig'], $_REQUEST['rsconfig']))
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&error=" . urlencode ('commitUpdateLB() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&message=" . urlencode ("Load balancer info was successfully updated");
}

function updateVService ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('id');
	assertIPv4Arg ('vip');
	assertUIntArg ('vport');
	assertStringArg ('proto');
	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (!commitUpdateVS ($_REQUEST['id'], $_REQUEST['vip'], $_REQUEST['vport'], $_REQUEST['proto'], $_REQUEST['name'], $_REQUEST['vsconfig'], $_REQUEST['rsconfig']))
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('commitUpdateVS() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Virtual service was successfully updated");
}

function addLoadBalancer ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('pool_id');
	assertUIntArg ('object_id');
	assertUIntArg ('vs_id');
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	$pool_id = $_REQUEST['pool_id'];
	if (!addLBtoRSPool ($pool_id, $_REQUEST['object_id'], $_REQUEST['vs_id'], $_REQUEST['vsconfig'], $_REQUEST['rsconfig']))
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&error=" . urlencode ('addLBtoRSPool() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&message=" . urlencode ("Load balancer was successfully added");
}

function addRSPool ()
{
	global $root, $pageno, $tabno;

	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (!commitCreateRSPool ($_REQUEST['name'], $_REQUEST['vsconfig'], $_REQUEST['rsconfig']))
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('commitCreateRSPool() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Real server pool was successfully created");
}

function deleteRSPool ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('pool_id');
	if (!commitDeleteRSPool ($_REQUEST['pool_id']))
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('commitDeleteRSPool() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Real server pool was successfully deleted");
}

function updateRSPool ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('pool_id');
	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (!commitUpdateRSPool ($_REQUEST['pool_id'], $_REQUEST['name'], $_REQUEST['vsconfig'], $_REQUEST['rsconfig']))
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('commitUpdateRSPool() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Real server pool was successfully updated");
}

function updateRSInService ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('pool_id');
	assertUIntArg ('rscount');
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
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&message=" . urlencode ($ngood . " real server(s) were successfully (de)activated");
	else
		return "${root}?page=${pageno}&tab=${tabno}&pool_id=${pool_id}&error=" . urlencode ("Encountered ${nbad} errors, (de)activated ${ngood} real servers");
}

function importPTRData ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('id');
	assertUIntArg ('addrcount');
	$nbad = $ngood = 0;
	$id = $_REQUEST['id'];
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
		return "${root}?page=${pageno}&tab=${tabno}&id=${id}&message=" . urlencode ($ngood . " IP address(es) were successfully updated");
	else
		return "${root}?page=${pageno}&tab=${tabno}&id=${id}&error=" . urlencode ("Encountered ${nbad} errors, updated ${ngood} IP address(es)");
}

function generateAutoPorts ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('object_id');
	$object_id = $_REQUEST['object_id'];
	$info = getObjectInfo ($object_id);
	// Navigate away in case of success, stay at the place otherwise.
	if (executeAutoPorts ($object_id, $info['objtype_id']))
		return "${root}?page=${pageno}&tab=ports&object_id=${object_id}&message=" . urlencode ('Generation complete');
	else
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=" . urlencode ('executeAutoPorts() failed');
}

// Filter out implicit tags before storing the new tag set.
function saveEntityTags ($realm, $bypass)
{
	global $root, $pageno, $tabno, $explicit_tags, $implicit_tags;
	assertUIntArg ($bypass);
	$entity_id = $_REQUEST[$bypass];
	// Build a trail from the submitted data, minimize it,
	// then wipe existing records and store the new set instead.
	wipeTags ($realm, $entity_id);
	$newtrail = getExplicitTagsOnly (buildTrailFromIds ($_REQUEST['taglist']));
	$n_succeeds = $n_errors = 0;
	foreach ($newtrail as $taginfo)
	{
		if (useInsertBlade ('TagStorage', array ('target_realm' => "'${realm}'", 'target_id' => $entity_id, 'tag_id' => $taginfo['id'])))
			$n_succeeds++;
		else
			$n_errors++;
	}
	if ($n_errors)
		return "${root}?page=${pageno}&tab=${tabno}&${bypass}=${entity_id}&error=" . urlencode ("Replaced trail with ${n_succeeds} tags, but experienced ${n_errors} errors.");
	else
		return "${root}?page=${pageno}&tab=${tabno}&${bypass}=${entity_id}&message=" . urlencode ("New trail is ${n_succeeds} tags long");
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
	return saveEntityTags ('ipv4vs', 'id');
}

function saveIPv4RSPoolTags ()
{
	return saveEntityTags ('ipv4rspool', 'id');
}

function destroyTag ()
{
	global $root, $pageno, $tabno;
	assertUIntArg ('id');
	if (($ret = commitDestroyTag ($_REQUEST['id'])) == '')
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Successfully deleted tag.");
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Error deleting tag: '${ret}'");
}

function createTag ()
{
	global $root, $pageno, $tabno;
	assertStringArg ('tagname');
	assertUIntArg ('parent_id', TRUE);
	$tagname = trim ($_REQUEST['tagname']);
	if (($parent_id = $_REQUEST['parent_id']) <= 0)
		$parent_id = 'NULL';
	if (($ret = commitCreateTag ($tagname, $parent_id)) == '')
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Created tag '${tagname}'.");
	else
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ("Could not create tag '${tagname}' because of error '${ret}'");
}

?>
