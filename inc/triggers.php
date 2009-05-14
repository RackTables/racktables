<?php
/*
*
*  This file is a library of tab triggers for RackTables.
*
*/

// Triggers may be optionally referred by some tabs of a page.
// In case they are defined, the given tab is only displayed if
// the trigger returned CSS class name. In certain cases, a key is necessary
// to decide (the 'bypass' hint of a page), and in some cases,
// other data can be used.

// This trigger filters out everything except switches with known-good
// software.
// FIXME: That's a bit of hardcoding at the moment, but
// let's thinks about fixing it later.
function trigger_livevlans ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	$object_id = $_REQUEST['object_id'];
	$object = getObjectInfo ($object_id, FALSE);
	if ($object['objtype_id'] != 8)
		return '';
	$values = getAttrValues ($object_id);
	foreach ($values as $record)
	{
		if ($record['id'] != 4) // SW type
			continue;
		// Cisco IOS 12.0
		// Cisco IOS 12.1
		// Cisco IOS 12.2
		if (in_array ($record['key'], array (244, 251, 252)))
			return 'std';
		else
			return '';
	}
	return '';
}

// SNMP port finder tab trigger. At the moment we decide on showing it
// for pristine switches only. Once a user has begun
// filling the data in, we stop showing the tab.
function trigger_snmpportfinder ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	$object_id = $_REQUEST['object_id'];
	$object = getObjectInfo ($object_id);
	if ($object['objtype_id'] != 8)
		return '';
	if (!objectIsPortless ($_REQUEST['object_id']))
		return '';
	return 'attn';
}

function trigger_isloadbalancer ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	return considerConfiguredConstraint ('object', $_REQUEST['object_id'], 'IPV4LB_LISTSRC') ? 'std' : '';
}

function trigger_ipv4 ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	if (count (getObjectIPv4Allocations ($_REQUEST['object_id'])))
		return 'std';
	// Only hide the tab, if there are no addresses allocated.
	return considerConfiguredConstraint ('object', $_REQUEST['object_id'], 'IPV4OBJ_LISTSRC') ? 'std' : '';
}

function trigger_natv4 ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	return considerConfiguredConstraint ('object', $_REQUEST['object_id'], 'IPV4NAT_LISTSRC') ? 'std' : '';
}

function trigger_poolrscount ()
{
	assertUIntArg ('pool_id', __FUNCTION__);
	$poolInfo = spotEntity ('ipv4rspool', $_REQUEST['pool_id']);
	amplifyCell ($poolInfo);
	return count ($poolInfo['rslist']) ? 'std' : '';
}

function trigger_autoports ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	if (!objectIsPortless ($_REQUEST['object_id']))
		return '';
	$info = getObjectInfo ($_REQUEST['object_id'], FALSE);
	return count (getAutoPorts ($info['objtype_id'])) ? 'attn' : '';
}

function trigger_tags ()
{
	global $taglist;
	return count ($taglist) ? 'std' : '';
}

function trigger_passwdchange ()
{
	global $user_auth_src;
	return $user_auth_src == 'database' ? 'std' : '';
}

function trigger_localreports ()
{
	global $localreports;
	return count ($localreports) ? 'std' : '';
}

function trigger_file_editText ()
{
	assertUIntArg ('file_id', __FUNCTION__);
	$fileInfo = getFileInfo ($_REQUEST['file_id']);
	return ($fileInfo['type'] == 'text/plain') ? 'std' : '';
}

?>
