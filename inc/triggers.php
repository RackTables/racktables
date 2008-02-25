<?php
/*
*
*  This file is a library of tab triggers for RackTables.
*
*/

// Triggers may be optionally referred by some tabs of a page.
// In case they are defined, the given tab is only displayed if
// the trigger returned true. In certain cases, a key is necessary
// to decide (the 'bypass' hint of a page), and in some cases,
// other data can be used.

// This trigger filters out everything except switches with known-good
// software.
// FIXME: That's a bit of hardcoding at the moment, but
// let's thinks about fixing it later.
function trigger_switchvlans ()
{
	assertUIntArg ('object_id');
	$object_id = $_REQUEST['object_id'];
	$object = getObjectInfo ($object_id);
	if ($object['objtype_id'] != 8)
		return FALSE;
	$values = getAttrValues ($object_id);
	foreach ($values as $record)
	{
		if ($record['id'] != 4) // SW type
			continue;
		// Cisco IOS 12.0
		// Cisco IOS 12.1
		// Cisco IOS 12.2
		if (in_array ($record['key'], array (244, 251, 252)))
			return TRUE;
		else
			return FALSE;
	}
	return FALSE;
}

// SNMP port finder tab trigger. At the moment we decide on showing it
// for pristine switches only. Once a user has begun
// filling the data in, we stop showing the tab.
function trigger_snmpportfinder ()
{
	assertUIntArg ('object_id');
	$object_id = $_REQUEST['object_id'];
	$object = getObjectInfo ($object_id);
	if ($object['objtype_id'] != 8)
		return FALSE;
	$tails = getObjectPortsAndLinks ($object_id);
	if (count ($tails))
		return FALSE;
	return TRUE;
}

// Output "click me" in an empty rackspace.
function trigger_emptyRackspace ()
{
	return (count (readChapter ('RackRow')) == 0);
}

function trigger_lvsconfig ()
{
	assertUIntArg ('object_id');
	return count (getRSPoolsForObject ($_REQUEST['object_id'])) > 0;
}

function trigger_ipv4 ()
{
	assertUIntArg ('object_id');
	$info = getObjectInfo ($_REQUEST['object_id']);
	return in_array ($info['objtype_id'], explode (',', getConfigVar ('IPV4_PERFORMERS')));
}

function trigger_natv4 ()
{
	assertUIntArg ('object_id');
	$info = getObjectInfo ($_REQUEST['object_id']);
	return in_array ($info['objtype_id'], explode (',', getConfigVar ('NATV4_PERFORMERS')));
}

function trigger_poolrscount ()
{
	assertUIntArg ('id');
	$poolInfo = getRSPoolInfo ($_REQUEST['id']);
	return count ($poolInfo['rslist']) > 0;
}

function trigger_autoports ()
{
	assertUIntArg ('object_id');
	if (count (getObjectPortsAndLinks ($_REQUEST['object_id'])) != 0)
		return FALSE;
	$info = getObjectInfo ($_REQUEST['object_id']);
	return count (getAutoPorts ($info['objtype_id'])) != 0;
}

?>
