<?php
/*
*
*  This file is a library of operation handlers for RackTables.
*
*/

function addPortForwarding ()
{
	global $root, $pageno, $tabno;

	$object_id = $_REQUEST['object_id'];
	$localip = $_REQUEST['localip'];
	$remoteip = $_REQUEST['remoteip'];
	$localport = $_REQUEST['localport'];
	$remoteport = $_REQUEST['remoteport'];
	$proto = $_REQUEST['proto'];
	$mode = $_REQUEST['mode'];
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
	$mode = $_REQUEST['mode'];

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

	$ip = $_REQUEST['ip'];
	$object_id = $_REQUEST['object_id'];
	$name = $_REQUEST['name'];
	$type = $_REQUEST['type'];
	$error = bindIpToObject($ip, $object_id, $name, $type);
	if ($error != '')
	{
		return "${root}?page=${pageno}&tab=${tabno}&object_id=$object_id&error=".urlencode($error);
	}
	else
	{
		return "${root}?page=$pageno&tab=${tabno}&object_id=$object_id&message=".urlencode("Interface successfully added");
	}
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

// This function processes a submit from the VLAN configuration form.
// It doesn't check any data at the moment, relying on a smart gateway.
// It doesn't even check if the a port already belongs to the VLAN it
// is being requested to be put into. This behaviour implies having a
// smart enough gateway, which unconditionally fetches the current
// configuration and then filters out 'set' requests. The gateway must
// validate port names and VLAN numbers as well. Ouch.
function updateVLANMembership ()
{
	global $root, $pageno, $tabno, $remote_username;
	assertUIntArg ('object_id');
	assertUIntArg ('portcount');
	$object_id = $_REQUEST['object_id'];
	$portcount  = $_REQUEST['portcount'];

	$endpoints = findAllEndpoints ($object_id);
	if (count ($endpoints) == 0)
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=" .
			urlencode ('Can\'t find any mean to reach current object. Please either set FQDN attribute or assign an IP address to the object.');
	elseif (count ($endpoints) > 1)
		return "${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&error=" .
			urlencode ('More than one IP address is assigned to this object, please configure FQDN attribute.');

// Just convert the input and feed it into the gateway.
	$questions = array("connect ${endpoints[0]} hwtype swtype ${remote_username}");
	for ($i = 0; $i < $portcount; $i++)
	{
		if (!isset ($_REQUEST["portname_${i}"]))
			continue;
		if (!isset ($_REQUEST["vlanid_${i}"]))
			continue;
		$portname = $_REQUEST["portname_${i}"];
		$vlanid = $_REQUEST["vlanid_${i}"];
		$questions[] = "set ${portname} ${vlanid}";
	}
	$data = queryGateway
	(
		$tabno,
		$questions
	);
	$error_count = $success_count = 0;
	foreach ($data as $reply)
		if (strncmp ($reply, 'OK!', 3))
			$error_count++;
		else
			$success_count++;
// Generate a message depending on error counter and redirect.
	return
		"${root}?page=${pageno}&tab=${tabno}&object_id=${object_id}&" .
		($error_count == 0 ? 'message' : 'error') .
		"=" . urlencode ("${error_count} failures and ${success_count} successfull changes.");

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
	return "${root}?page=${pageno}&tab=default&message=" . urlencode ("Reset complete");
}

function addRealServer ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('id');
	assertIPv4Arg ('rsip');
	assertUIntArg ('rsport');
	assertStringArg ('rsconfig', TRUE);
	$pool_id = $_REQUEST['id'];
	if (!addRStoRSPool ($pool_id, $_REQUEST['rsip'], $_REQUEST['rsport'], $_REQUEST['rsconfig']))
		return "${root}?page=${pageno}&tab=${tabno}&id=${pool_id}&error=" . urlencode ('addRStoRSPool() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&id=${pool_id}&message=" . urlencode ("Real server was successfully added");
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
		return "${root}?page=${pageno}&tab=${tabno}&id=${pool_id}&error=" . urlencode ('commitDeleteRS() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&id=${pool_id}&message=" . urlencode ("Real server was successfully deleted");
}

function deleteLoadBalancer ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('object_id');
	assertUIntArg ('pool_id');
	$pool_id = $_REQUEST['pool_id'];
	if (!commitDeleteLB ($_REQUEST['object_id'], $pool_id))
		return "${root}?page=${pageno}&tab=${tabno}&id=${pool_id}&error=" . urlencode ('commitDeleteLB() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&id=${pool_id}&message=" . urlencode ("Load balancer was successfully deleted");
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
	assertUIntArg ('pool_id');
	assertIPv4Arg ('rsip');
	assertUIntArg ('rsport');
	assertStringArg ('rsconfig', TRUE);
	// only necessary for generating next URL
	$pool_id = $_REQUEST['pool_id'];
	if (!commitUpdateRS ($_REQUEST['id'], $_REQUEST['rsip'], $_REQUEST['rsport'], $_REQUEST['rsconfig']))
		return "${root}?page=${pageno}&tab=${tabno}&id=${pool_id}&error=" . urlencode ('commitUpdateRS() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&id=${pool_id}&message=" . urlencode ("Real server was successfully updated");
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
		return "${root}?page=${pageno}&tab=${tabno}&id=${pool_id}&error=" . urlencode ('commitUpdateLB() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&id=${pool_id}&message=" . urlencode ("Real server was successfully updated");
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
		return "${root}?page=${pageno}&tab=${tabno}&id=${pool_id}&error=" . urlencode ('addLBtoRSPool() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&id=${pool_id}&message=" . urlencode ("Load balancer was successfully added");
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

	assertUIntArg ('id');
	if (!commitDeleteRSPool ($_REQUEST['id']))
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('commitDeleteRSPool() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Real server pool was successfully deleted");
}

function updateRSPool ()
{
	global $root, $pageno, $tabno;

	assertUIntArg ('id');
	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (!commitUpdateRSPool ($_REQUEST['id'], $_REQUEST['name'], $_REQUEST['vsconfig'], $_REQUEST['rsconfig']))
		return "${root}?page=${pageno}&tab=${tabno}&error=" . urlencode ('commitUpdateRSPool() failed');
	else
		return "${root}?page=${pageno}&tab=${tabno}&message=" . urlencode ("Real server pool was successfully updated");
}

?>
