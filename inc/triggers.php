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

// APC "switched rack PDU" stands for a number of part numbers:
// http://www.apc.com/products/family/index.cfm?id=70
$known_APC_SKUs = array
(
	// 100V input
	1151 => 'AP7902J',
	1152 => 'AP7930J',
	1153 => 'AP7932J',
	// 120V input
	1154 => 'AP7900',
	1155 => 'AP7901',
	1156 => 'AP7902',
	1157 => 'AP7930',
	1158 => 'AP7931',
	1159 => 'AP7932',
	// 208V input
	1160 => 'AP7911',
	1161 => 'AP7940',
	1162 => 'AP7941',
	// 208V 3 phases input
	1163 => 'AP7960',
	1164 => 'AP7961',
	1165 => 'AP7968',
	1166 => 'AP7990',
	1167 => 'AP7991',
	1168 => 'AP7998',
	// 230V input
	1137 => 'AP7920',
	1138 => 'AP7921',
	1139 => 'AP7922',
	1140 => 'AP7950',
	1141 => 'AP7951',
	1142 => 'AP7952',
	1143 => 'AP7953',
	1144 => 'AP7954',
	// 400V 3 phases input
	1154 => 'AP7957',
);

// Return 'std', if the object belongs to specified type and has
// specified attribute belonging to the given set of values.
function checkTypeAndAttribute ($object_id, $type_id, $attr_id, $values, $hit = 'std')
{
	$object = spotEntity ('object', $object_id);
	if ($object['objtype_id'] != $type_id)
		return '';
	foreach (getAttrValues ($object_id) as $record)
		if ($record['id'] == $attr_id and in_array ($record['key'], $values))
			return $hit;
	return '';
}

// This trigger filters out everything except switches with known-good
// software.
function trigger_livevlans ()
{
	return checkTypeAndAttribute
	(
		$_REQUEST['object_id'],
		8, // network switch
		4, // SW type
		// Cisco IOS 12.0
		// Cisco IOS 12.1
		// Cisco IOS 12.2
		array (244, 251, 252)
	);
}

// SNMP port finder tab trigger. At the moment we decide on showing it
// for pristine switches/PDUs only. Once a user has begun
// filling the data in, we stop showing the tab.
function trigger_snmpportfinder ()
{

	assertUIntArg ('object_id');
	$object = spotEntity ('object', $_REQUEST['object_id']);
	switch ($object['objtype_id'])
	{
	case 7: // any router
	case 8: // or switch would suffice
		return $object['nports'] ? '' : 'attn';
	case 2: // but only selected PDUs
		if ($object['nports'])
			return '';
		global $known_APC_SKUs;
		return checkTypeAndAttribute
		(
			$object['id'],
			2, // PDU
			2, // HW type
			array_keys ($known_APC_SKUs),
			'attn'
		);
	default:
		return '';
	}
}

function trigger_isloadbalancer ()
{
	assertUIntArg ('object_id');
	return considerConfiguredConstraint (spotEntity ('object', $_REQUEST['object_id']), 'IPV4LB_LISTSRC') ? 'std' : '';
}

function trigger_ipv4 ()
{
	assertUIntArg ('object_id');
	if (count (getObjectIPv4Allocations ($_REQUEST['object_id'])))
		return 'std';
	// Only hide the tab, if there are no addresses allocated.
	return considerConfiguredConstraint (spotEntity ('object', $_REQUEST['object_id']), 'IPV4OBJ_LISTSRC') ? 'std' : '';
}

function trigger_natv4 ()
{
	assertUIntArg ('object_id');
	return considerConfiguredConstraint (spotEntity ('object', $_REQUEST['object_id']), 'IPV4NAT_LISTSRC') ? 'std' : '';
}

function trigger_poolrscount ()
{
	assertUIntArg ('pool_id');
	$poolInfo = spotEntity ('ipv4rspool', $_REQUEST['pool_id']);
	amplifyCell ($poolInfo);
	return count ($poolInfo['rslist']) ? 'std' : '';
}

function trigger_autoports ()
{
	assertUIntArg ('object_id');
	$object = spotEntity ('object', $_REQUEST['object_id']);
	amplifyCell ($object);
	if (count ($object['ports']))
		return '';
	return count (getAutoPorts ($object['objtype_id'])) ? 'attn' : '';
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
	assertUIntArg ('file_id');
	$fileInfo = spotEntity ('file', $_REQUEST['file_id']);
	return ($fileInfo['type'] == 'text/plain') ? 'std' : '';
}

function trigger_rackspace ()
{
	$rackspace = getRackspaceStats();
	if ($rackspace['Racks'] > 0) return 'std';
	return '';
}

// Offer the generic VLAN setup tab for every object, which already
// has a VLAN domain associated or at least can have one (in the latter
// case additionally heat the tab, if no domain is set.
function trigger_vlanconfig ()
{
	if (NULL !== getVLANSwitchInfo ($_REQUEST['object_id']))
		return 'std';
	elseif (considerConfiguredConstraint (spotEntity ('object', $_REQUEST['object_id']), 'VLANSWITCH_LISTSRC'))
		return 'attn';
	else
		return '';
}

// implement similar logic for IPv4 networks
function trigger_ipv4net_vlanconfig ()
{
	$netinfo = spotEntity ('ipv4net', $_REQUEST['id']);
	if (strlen ($netinfo['vlan_ck']))
		return 'std';
	elseif (considerConfiguredConstraint ($netinfo, 'VLANIPV4NET_LISTSRC'))
		return 'attn';
	else
		return '';
}

function trigger_vlanports ()
{
	return NULL !== getVLANSwitchInfo ($_REQUEST['object_id']) ? 'std' : '';
}
?>
