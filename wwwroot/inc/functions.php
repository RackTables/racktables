<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*
*
*  This file is a library of computational functions for RackTables.
*
*/

$loclist[0] = 'front';
$loclist[1] = 'interior';
$loclist[2] = 'rear';
$loclist['front'] = 0;
$loclist['interior'] = 1;
$loclist['rear'] = 2;
$template[0] = array (TRUE, TRUE, TRUE);
$template[1] = array (TRUE, TRUE, FALSE);
$template[2] = array (FALSE, TRUE, TRUE);
$template[3] = array (TRUE, FALSE, FALSE);
$template[4] = array (FALSE, TRUE, FALSE);
$template[5] = array (FALSE, FALSE, TRUE);
$templateWidth[0] = 3;
$templateWidth[1] = 2;
$templateWidth[2] = 2;
$templateWidth[3] = 1;
$templateWidth[4] = 1;
$templateWidth[5] = 1;

define ('CHAP_OBJTYPE', 1);
define ('CHAP_PORTTYPE', 2);
// The latter matches both SunOS and Linux-styled formats.
define ('RE_L2_IFCFG', '/^[0-9a-f]{1,2}(:[0-9a-f]{1,2}){5}$/i');
define ('RE_L2_CISCO', '/^[0-9a-f]{4}(\.[0-9a-f]{4}){2}$/i');
define ('RE_L2_HUAWEI', '/^[0-9a-f]{4}(-[0-9a-f]{4}){2}$/i');
define ('RE_L2_SOLID', '/^[0-9a-f]{12}$/i');
define ('RE_L2_IPCFG', '/^[0-9a-f]{2}(-[0-9a-f]{2}){5}$/i');
define ('RE_L2_WWN_COLON', '/^[0-9a-f]{1,2}(:[0-9a-f]{1,2}){7}$/i');
define ('RE_L2_WWN_HYPHEN', '/^[0-9a-f]{2}(-[0-9a-f]{2}){7}$/i');
define ('RE_L2_WWN_SOLID', '/^[0-9a-f]{16}$/i');
define ('RE_IP4_ADDR', '#^[0-9]{1,3}(\.[0-9]{1,3}){3}$#');
define ('RE_IP4_NET', '#^[0-9]{1,3}(\.[0-9]{1,3}){3}/[0-9]{1,2}$#');
define ('E_8021Q_NOERROR', 0);
define ('E_8021Q_VERSION_CONFLICT', 101);
define ('E_8021Q_PULL_REMOTE_ERROR', 102);
define ('E_8021Q_PUSH_REMOTE_ERROR', 103);
define ('E_8021Q_SYNC_DISABLED', 104);
define ('VLAN_MIN_ID', 1);
define ('VLAN_MAX_ID', 4094);
define ('VLAN_DFL_ID', 1);
define ('TAB_REMEMBER_TIMEOUT', 300);

// Entity type by page number mapping is 1:1 atm, but may change later.
$etype_by_pageno = array
(
	'ipv4net' => 'ipv4net',
	'ipv6net' => 'ipv6net',
	'ipv4rspool' => 'ipv4rspool',
	'ipv4vs' => 'ipv4vs',
	'object' => 'object',
	'rack' => 'rack',
	'row' => 'row',
	'location' => 'location',
	'user' => 'user',
	'file' => 'file',
	'vst' => 'vst',
);

// Rack thumbnail image width summands: "front", "interior" and "rear" elements w/o surrounding border.
$rtwidth = array
(
	0 => 9,
	1 => 21,
	2 => 9
);

$location_obj_types = array
(
	1560,
	1561,
	1562
);

// 802.1Q deploy queue titles
$dqtitle = array
(
	'sync_aging' => 'Normal, aging',
	'resync_aging' => 'Failed, aging',
	'sync_ready' => 'Normal, ready for sync',
	'resync_ready' => 'Failed, ready for retry',
	'disabled' => 'Sync disabled',
	'done' => 'Up to date',
);

$wdm_packs = array
(
	'1000cwdm80' => array
	(
		'title' => '1000Base-CWDM80 (8 channels)',
		'iif_ids' => array (3, 4),
		'oif_ids' => array (1209, 1210, 1211, 1212, 1213, 1214, 1215, 1216),
	),
	'1000dwdm80' => array // ITU channels 20~61
	(
		'title' => '1000Base-DWDM80 (42 channels)',
		'iif_ids' => array (3, 4),
		'oif_ids' => array
		(
			1217, 1218, 1219, 1220, 1221, 1222, 1223, 1224, 1225, 1226,
			1227, 1228, 1229, 1230, 1231, 1232, 1233, 1234, 1235, 1236,
			1237, 1238, 1239, 1240, 1241, 1242, 1243, 1244, 1245, 1246,
			1247, 1248, 1249, 1250, 1251, 1252, 1253, 1254, 1255, 1256,
			1257, 1258
		),
	),
	'10000dwdm80' => array // same channels for 10GE
	(
		'title' => '10GBase-ZR-DWDM80 (42 channels)',
		'iif_ids' => array (9, 6, 5, 8, 7),
		'oif_ids' => array
		(
			1259, 1260, 1261, 1262, 1263, 1264, 1265, 1266, 1267, 1268,
			1269, 1270, 1271, 1272, 1273, 1274, 1275, 1276, 1277, 1278,
			1279, 1280, 1281, 1282, 1283, 1284, 1285, 1286, 1287, 1288,
			1289, 1290, 1291, 1292, 1293, 1294, 1295, 1296, 1297, 1298,
			1299, 1300
		),
	),
	'10000dwdm40' => array
	(
		'title' => '10GBase-ER-DWDM40 (42 channels)',
		'iif_ids' => array (9, 6, 5, 8, 7),
		'oif_ids' => array
		(
			1425, 1426, 1427, 1428, 1429, 1430, 1431, 1432, 1433, 1434,
			1435, 1436, 1437, 1438, 1439, 1440, 1441, 1442, 1443, 1444,
			1445, 1446, 1447, 1448, 1449, 1450, 1451, 1452, 1453, 1454,
			1455, 1456, 1457, 1458, 1459, 1460, 1461, 1462, 1463, 1464,
			1465, 1466
		),
	),
);

$log_messages = array(); // messages waiting for displaying

// This function assures that specified argument was passed
// and is a number greater than zero.
function assertUIntArg ($argname, $allow_zero = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, '', 'parameter is missing');
	if (!is_numeric ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is not a number');
	if ($_REQUEST[$argname] < 0)
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is less than zero');
	if (!$allow_zero and $_REQUEST[$argname] == 0)
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is zero');
	return $_REQUEST[$argname];
}

function isInteger ($arg, $allow_zero = FALSE)
{
	if (! is_numeric ($arg))
		return FALSE;
	if (! $allow_zero and ! $arg)
		return FALSE;
	return TRUE;
}

# Make sure the arg is a parsable date, return its UNIX timestamp equivalent
# (or empty string for empty input, when allowed).
function assertDateArg ($argname, $ok_if_empty = FALSE)
{
	if ('' == $arg = assertStringArg ($argname, $ok_if_empty))
		return '';
	try
	{
		return timestampFromDatetimestr ($arg);
	}
	catch (InvalidArgException $e)
	{
		throw convertToIRAE ($e, $argname);
	}
}

// This function assures that specified argument was passed
// and is a non-empty string.
function assertStringArg ($argname, $ok_if_empty = FALSE)
{
	global $sic;
	if (!isset ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, '', 'parameter is missing');
	if (!is_string ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is not a string');
	if (!$ok_if_empty and !strlen ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is an empty string');
	return $sic[$argname];
}

function assertBoolArg ($argname, $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, '', 'parameter is missing');
	if (!is_string ($_REQUEST[$argname]) or $_REQUEST[$argname] != 'on')
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is not a string');
	if (!$ok_if_empty and !strlen ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is an empty string');
	return $_REQUEST[$argname] == TRUE;
}

// function returns binary IP address, or throws an exception
function assertIPArg ($argname)
{
	try
	{
		return ip_parse (assertStringArg ($argname));
	}
	catch (InvalidArgException $e)
	{
		throw convertToIRAE ($e, $argname);
	}
}

// function returns binary IPv4 address, or throws an exception
function assertIPv4Arg ($argname)
{
	try
	{
		return ip4_parse (assertStringArg ($argname));
	}
	catch (InvalidArgException $e)
	{
		throw convertToIRAE ($e, $argname);
	}
}

// function returns binary IPv6 address, or throws an exception
function assertIPv6Arg ($argname)
{
	try
	{
		return ip6_parse (assertStringArg ($argname));
	}
	catch (InvalidArgException $e)
	{
		throw convertToIRAE ($e, $argname);
	}
}

function assertPCREArg ($argname)
{
	$arg = assertStringArg ($argname, TRUE); // empty pattern is Ok
	if (FALSE === @preg_match ($arg, 'test'))
		throw new InvalidRequestArgException($argname, $arg, 'PCRE validation failed');
	return $arg;
}

function isPCRE ($arg)
{
	if (! isset ($arg) or FALSE === @preg_match ($arg, 'test'))
		return FALSE;
	return TRUE;
}

function genericAssertion ($argname, $argtype)
{
	global $sic;
	switch ($argtype)
	{
	case 'string':
		return assertStringArg ($argname);
	case 'string0':
		return assertStringArg ($argname, TRUE);
	case 'uint':
		return assertUIntArg ($argname);
	case 'uint-uint':
		if (! preg_match ('/^([1-9][0-9]*)-([1-9][0-9]*)$/', assertStringArg ($argname), $m))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'illegal format');
		return $m;
	case 'uint0':
		return assertUIntArg ($argname, TRUE);
	case 'inet':
		return assertIPArg ($argname);
	case 'inet4':
		return assertIPv4Arg ($argname);
	case 'inet6':
		return assertIPv6Arg ($argname);
	case 'l2address':
		return assertStringArg ($argname);
	case 'l2address0':
		assertStringArg ($argname, TRUE);
		try
		{
			l2addressForDatabase ($sic[$argname]);
		}
		catch (InvalidArgException $e)
		{
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'malformed MAC/WWN address');
		}
		return $sic[$argname];
		break;
	case 'tag':
		if (!validTagName (assertStringArg ($argname)))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Invalid tag name');
		return $sic[$argname];
	case 'pcre':
		return assertPCREArg ($argname);
	case 'json':
		if (NULL === ($ret = json_decode (assertStringArg ($argname), TRUE)))
			throw new InvalidRequestArgException ($argname, '(omitted)', 'Invalid JSON code received from client');
		return $ret;
	case 'array':
		if (! array_key_exists ($argname, $_REQUEST))
			throw new InvalidRequestArgException ($argname, '(missing argument)');
		if (! is_array ($_REQUEST[$argname]))
			throw new InvalidRequestArgException ($argname, '(omitted)', 'argument is not an array');
		return $_REQUEST[$argname];
	case 'enum/attr_type':
		assertStringArg ($argname);
		if (!in_array ($sic[$argname], array ('uint', 'float', 'string', 'dict','date')))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/vlan_type':
		assertStringArg ($argname);
		// "Alien" type is not valid until the logic is fixed to implement it in full.
		if (!in_array ($sic[$argname], array ('ondemand', 'compulsory')))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/wdmstd':
		assertStringArg ($argname);
		global $wdm_packs;
		if (! array_key_exists ($sic[$argname], $wdm_packs))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/ipproto':
		assertStringArg ($argname);
		global $vs_proto;
		if (!array_key_exists ($sic[$argname], $vs_proto))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/alloc_type':
		assertStringArg ($argname);
		if (!in_array ($sic[$argname], array ('regular', 'shared', 'virtual', 'router')))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/dqcode':
		assertStringArg ($argname);
		global $dqtitle;
		if (! array_key_exists ($sic[$argname], $dqtitle))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/yesno':
		if (! in_array ($sic[$argname], array ('yes', 'no')))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'iif':
		assertUIntArg ($argname);
		if (!array_key_exists ($sic[$argname], getPortIIFOptions()))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'vlan':
	case 'vlan1':
		assertUIntArg ($argname);
		if ($argtype == 'vlan' and $sic[$argname] == VLAN_DFL_ID)
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'default VLAN cannot be changed');
		if ($sic[$argname] > VLAN_MAX_ID or $sic[$argname] < VLAN_MIN_ID)
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'out of valid range');
		return $sic[$argname];
	case 'rackcode/expr':
		if ('' == assertStringArg ($argname, TRUE))
			return array();
		$parse = spotPayload ($sic[$argname], 'SYNT_EXPR');
		if ($parse['result'] != 'ACK')
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'RackCode parsing error');
		return $parse['load'];
	default:
		throw new InvalidArgException ('argtype', $argtype); // comes not from user's input
	}
}

// return HTML form checkbox value (TRUE of FALSE) by name of its input control
function isCheckSet ($input_name, $mode = 'bool')
{
	$value = isset ($_REQUEST[$input_name]) && $_REQUEST[$input_name] == 'on';
	switch ($mode)
	{
		case 'bool' : return $value;
		case 'yesno': return $value ? 'yes' : 'no';
		default: throw new InvalidArgException ('mode', $mode);
	}
}

// Validate and return "bypass" value for the current context, if one is
// defined for it, or NULL otherwise.
function getBypassValue()
{
	global $page, $pageno, $sic;
	if (!array_key_exists ('bypass', $page[$pageno]))
		return NULL;
	if (!array_key_exists ('bypass_type', $page[$pageno]))
		throw new RackTablesError ("Internal structure error at node '${pageno}' (bypass_type is not set)", RackTablesError::INTERNAL);
	return genericAssertion ($page[$pageno]['bypass'], $page[$pageno]['bypass_type']);
}

// fills $args array with the bypass values of specified $pageno which are provided in $_REQUEST
function fillBypassValues ($pageno, &$args)
{
	global $page, $sic;
	if (isset ($page[$pageno]['bypass']))
	{
		$param_name = $page[$pageno]['bypass'];
		if (! array_key_exists ($param_name, $args) && isset ($sic[$param_name]))
			$args[$param_name] = $sic[$param_name];
	}
	if (isset ($page[$pageno]['bypass_tabs']))
		foreach ($page[$pageno]['bypass_tabs'] as $param_name)
			if (! array_key_exists ($param_name, $args) && isset ($sic[$param_name]))
				$args[$param_name] = $sic[$param_name];
}

// Objects of some types should be explicitly shown as
// anonymous (labelless). This function is a single place where the
// decision about displayed name is made.
function setDisplayedName (&$cell)
{
	if ($cell['realm'] == 'object')
	{
		if ($cell['name'] != '')
			$cell['dname'] = $cell['name'];
		else
			$cell['dname'] = '[' . decodeObjectType ($cell['objtype_id'], 'o') . ']';
		// If the object has a container, apply the same logic to the container name
		$cell['container_dname'] = NULL;
		if ($cell['container_id'])
		{
			if ($cell['container_name'] != '')
				$cell['container_dname'] = $cell['container_name'];
			else
				$cell['container_dname'] = '[' . decodeObjectType ($cell['container_objtype_id'], 'o') . ']';
		}
	}
	elseif ($cell['realm'] == 'ipv4vs')
	{
		if ($cell['proto'] == 'MARK')
			$cell['dname'] = "fwmark: " . implode ('', unpack('N', substr ($cell['vip_bin'], 0, 4)));
		else
			$cell['dname'] = $cell['vip'] . ':' . $cell['vport'] . '/' . $cell['proto'];
	}
}

// This function finds height of solid rectangle of atoms, which are all
// assigned to the same object. Rectangle base is defined by specified
// template.
function rectHeight ($rackData, $startRow, $template_idx)
{
	$height = 0;
	// The first met object_id is used to match all the folowing IDs.
	$object_id = 0;
	global $template;
	do
	{
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			// At least one value in template is TRUE, but the following block
			// can meet 'skipped' atoms. Let's ensure we have something after processing
			// the first row.
			if ($template[$template_idx][$locidx])
			{
				if (isset ($rackData[$startRow - $height][$locidx]['skipped']))
					break 2;
				if (isset ($rackData[$startRow - $height][$locidx]['rowspan']))
					break 2;
				if (isset ($rackData[$startRow - $height][$locidx]['colspan']))
					break 2;
				if ($rackData[$startRow - $height][$locidx]['state'] != 'T')
					break 2;
				if ($object_id == 0)
					$object_id = $rackData[$startRow - $height][$locidx]['object_id'];
				if ($object_id != $rackData[$startRow - $height][$locidx]['object_id'])
					break 2;
			}
		}
		// If the first row can't offer anything, bail out.
		if ($height == 0 and $object_id == 0)
			break;
		$height++;
	}
	while ($startRow - $height > 0);
#	echo "for startRow==${startRow} and template==(" . ($template[$template_idx][0] ? 'T' : 'F');
#	echo ', ' . ($template[$template_idx][1] ? 'T' : 'F') . ', ' . ($template[$template_idx][2] ? 'T' : 'F');
#	echo ") height==${height}<br>\n";
	return $height;
}

// This function marks atoms to be avoided by rectHeight() and assigns rowspan/colspan
// attributes.
function markSpan (&$rackData, $startRow, $maxheight, $template_idx)
{
	global $template, $templateWidth;
	$colspan = 0;
	for ($height = 0; $height < $maxheight; $height++)
	{
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			if ($template[$template_idx][$locidx])
			{
				// Add colspan/rowspan to the first row met and mark the following ones to skip.
				// Explicitly show even single-cell spanned atoms, because rectHeight()
				// is expeciting this data for correct calculation.
				if ($colspan != 0)
					$rackData[$startRow - $height][$locidx]['skipped'] = TRUE;
				else
				{
					$colspan = $templateWidth[$template_idx];
					if ($colspan >= 1)
						$rackData[$startRow - $height][$locidx]['colspan'] = $colspan;
					if ($maxheight >= 1)
						$rackData[$startRow - $height][$locidx]['rowspan'] = $maxheight;
				}
			}
		}
	}
	return;
}

// This function sets rowspan/solspan/skipped atom attributes for renderRack()
// What we actually have to do is to find _all_ possible rectangles for each unit
// and then select the widest of those with the maximal square.
function markAllSpans (&$rackData)
{
	for ($i = $rackData['height']; $i > 0; $i--)
		while (markBestSpan ($rackData, $i));
}

// Calculate height of 6 possible span templates (array is presorted by width
// descending) and mark the best (if any).
function markBestSpan (&$rackData, $i)
{
	global $template, $templateWidth;
	for ($j = 0; $j < 6; $j++)
	{
		$height[$j] = rectHeight ($rackData, $i, $j);
		$square[$j] = $height[$j] * $templateWidth[$j];
	}
	// find the widest rectangle of those with maximal height
	$maxsquare = max ($square);
	if (!$maxsquare)
		return FALSE;
	$best_template_index = 0;
	for ($j = 0; $j < 6; $j++)
		if ($square[$j] == $maxsquare)
		{
			$best_template_index = $j;
			$bestheight = $height[$j];
			break;
		}
	// distribute span marks
	markSpan ($rackData, $i, $bestheight, $best_template_index);
	return TRUE;
}

// We can mount 'F' atoms and unmount our own 'T' atoms.
function applyObjectMountMask (&$rackData, $object_id)
{
	for ($unit_no = $rackData['height']; $unit_no > 0; $unit_no--)
		for ($locidx = 0; $locidx < 3; $locidx++)
			switch ($rackData[$unit_no][$locidx]['state'])
			{
				case 'F':
					$rackData[$unit_no][$locidx]['enabled'] = TRUE;
					break;
				case 'T':
					$rackData[$unit_no][$locidx]['enabled'] = ($rackData[$unit_no][$locidx]['object_id'] == $object_id);
					break;
				default:
					$rackData[$unit_no][$locidx]['enabled'] = FALSE;
			}
}

// Design change means transition between 'F' and 'A' and back.
function applyRackDesignMask (&$rackData)
{
	for ($unit_no = $rackData['height']; $unit_no > 0; $unit_no--)
		for ($locidx = 0; $locidx < 3; $locidx++)
			switch ($rackData[$unit_no][$locidx]['state'])
			{
				case 'F':
				case 'A':
					$rackData[$unit_no][$locidx]['enabled'] = TRUE;
					break;
				default:
					$rackData[$unit_no][$locidx]['enabled'] = FALSE;
			}
}

// The same for 'F' and 'U'.
function applyRackProblemMask (&$rackData)
{
	for ($unit_no = $rackData['height']; $unit_no > 0; $unit_no--)
		for ($locidx = 0; $locidx < 3; $locidx++)
			switch ($rackData[$unit_no][$locidx]['state'])
			{
				case 'F':
				case 'U':
					$rackData[$unit_no][$locidx]['enabled'] = TRUE;
					break;
				default:
					$rackData[$unit_no][$locidx]['enabled'] = FALSE;
			}
}

// This function highlights specified object (and removes previous highlight).
function highlightObject (&$rackData, $object_id)
{
	// Also highlight parent objects
	$parents = getEntityRelatives ('parents', 'object', $object_id);
	$parent_ids = array();
	foreach ($parents as $parent)
		$parent_ids[] = $parent['entity_id'];

	for ($unit_no = $rackData['height']; $unit_no > 0; $unit_no--)
		for ($locidx = 0; $locidx < 3; $locidx++)
			if
			(
				$rackData[$unit_no][$locidx]['state'] == 'T' and
				($rackData[$unit_no][$locidx]['object_id'] == $object_id or	in_array($rackData[$unit_no][$locidx]['object_id'], $parent_ids))
			)
				$rackData[$unit_no][$locidx]['hl'] = 'h';
			else
				unset ($rackData[$unit_no][$locidx]['hl']);
}

// This function marks atoms to selected or not depending on their current state.
function markupAtomGrid (&$data, $checked_state)
{
	for ($unit_no = $data['height']; $unit_no > 0; $unit_no--)
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			if (!($data[$unit_no][$locidx]['enabled'] === TRUE))
				continue;
			if ($data[$unit_no][$locidx]['state'] == $checked_state)
				$data[$unit_no][$locidx]['checked'] = ' checked';
			else
				$data[$unit_no][$locidx]['checked'] = '';
		}
}

// This function is almost a clone of processGridForm(), but doesn't save anything to database
// Return value is the changed rack data.
// Here we assume that correct filter has already been applied, so we just
// set or unset checkbox inputs w/o changing atom state.
function mergeGridFormToRack (&$rackData)
{
	$rack_id = $rackData['id'];
	for ($unit_no = $rackData['height']; $unit_no > 0; $unit_no--)
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			if ($rackData[$unit_no][$locidx]['enabled'] != TRUE)
				continue;
			$inputname = "atom_${rack_id}_${unit_no}_${locidx}";
			if (isset ($_REQUEST[$inputname]) and $_REQUEST[$inputname] == 'on')
				$rackData[$unit_no][$locidx]['checked'] = ' checked';
			else
				$rackData[$unit_no][$locidx]['checked'] = '';
		}
}

// wrapper around ip4_mask and ip6_mask
// netmask conversion from length to binary string
// v4/v6 mode is toggled by $is_ipv6 parameter
// Throws exception if $prefix_len is invalid
function ip_mask ($prefix_len, $is_ipv6)
{
	if ($is_ipv6)
		return ip6_mask ($prefix_len);
	else
		return ip4_mask ($prefix_len);
}

// netmask conversion from length to binary string
// Throws exception if $prefix_len is invalid
function ip4_mask ($prefix_len)
{
	static $mask = array
	(
		"\x00\x00\x00\x00", // 0
		"\x80\x00\x00\x00", // 1
		"\xC0\x00\x00\x00", // 2
		"\xE0\x00\x00\x00", // 3
		"\xF0\x00\x00\x00", // 4
		"\xF8\x00\x00\x00", // 5
		"\xFC\x00\x00\x00", // 6
		"\xFE\x00\x00\x00", // 7
		"\xFF\x00\x00\x00", // 8
		"\xFF\x80\x00\x00", // 9
		"\xFF\xC0\x00\x00", // 10
		"\xFF\xE0\x00\x00", // 11
		"\xFF\xF0\x00\x00", // 12
		"\xFF\xF8\x00\x00", // 13
		"\xFF\xFC\x00\x00", // 14
		"\xFF\xFE\x00\x00", // 15
		"\xFF\xFF\x00\x00", // 16
		"\xFF\xFF\x80\x00", // 17
		"\xFF\xFF\xC0\x00", // 18
		"\xFF\xFF\xE0\x00", // 19
		"\xFF\xFF\xF0\x00", // 20
		"\xFF\xFF\xF8\x00", // 21
		"\xFF\xFF\xFC\x00", // 22
		"\xFF\xFF\xFE\x00", // 23
		"\xFF\xFF\xFF\x00", // 24
		"\xFF\xFF\xFF\x80", // 25
		"\xFF\xFF\xFF\xC0", // 26
		"\xFF\xFF\xFF\xE0", // 27
		"\xFF\xFF\xFF\xF0", // 28
		"\xFF\xFF\xFF\xF8", // 29
		"\xFF\xFF\xFF\xFC", // 30
		"\xFF\xFF\xFF\xFE", // 31
		"\xFF\xFF\xFF\xFF", // 32
	);

	if ($prefix_len >= 0 and $prefix_len <= 32)
		return $mask[$prefix_len];
	else
		throw new InvalidArgException ('prefix_len', $prefix_len);
}

// netmask conversion from length to binary string
// Throws exception if $prefix_len is invalid
function ip6_mask ($prefix_len)
{
	static $mask = array
	(
		"\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 0
		"\x80\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 1
		"\xC0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 2
		"\xE0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 3
		"\xF0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 4
		"\xF8\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 5
		"\xFC\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 6
		"\xFE\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 7
		"\xFF\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 8
		"\xFF\x80\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 9
		"\xFF\xC0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 10
		"\xFF\xE0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 11
		"\xFF\xF0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 12
		"\xFF\xF8\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 13
		"\xFF\xFC\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 14
		"\xFF\xFE\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 15
		"\xFF\xFF\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 16
		"\xFF\xFF\x80\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 17
		"\xFF\xFF\xC0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 18
		"\xFF\xFF\xE0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 19
		"\xFF\xFF\xF0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 20
		"\xFF\xFF\xF8\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 21
		"\xFF\xFF\xFC\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 22
		"\xFF\xFF\xFE\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 23
		"\xFF\xFF\xFF\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 24
		"\xFF\xFF\xFF\x80\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 25
		"\xFF\xFF\xFF\xC0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 26
		"\xFF\xFF\xFF\xE0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 27
		"\xFF\xFF\xFF\xF0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 28
		"\xFF\xFF\xFF\xF8\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 29
		"\xFF\xFF\xFF\xFC\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 30
		"\xFF\xFF\xFF\xFE\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 31
		"\xFF\xFF\xFF\xFF\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 32
		"\xFF\xFF\xFF\xFF\x80\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 33
		"\xFF\xFF\xFF\xFF\xC0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 34
		"\xFF\xFF\xFF\xFF\xE0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 35
		"\xFF\xFF\xFF\xFF\xF0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 36
		"\xFF\xFF\xFF\xFF\xF8\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 37
		"\xFF\xFF\xFF\xFF\xFC\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 38
		"\xFF\xFF\xFF\xFF\xFE\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 39
		"\xFF\xFF\xFF\xFF\xFF\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 40
		"\xFF\xFF\xFF\xFF\xFF\x80\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 41
		"\xFF\xFF\xFF\xFF\xFF\xC0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 42
		"\xFF\xFF\xFF\xFF\xFF\xE0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 43
		"\xFF\xFF\xFF\xFF\xFF\xF0\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 44
		"\xFF\xFF\xFF\xFF\xFF\xF8\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 45
		"\xFF\xFF\xFF\xFF\xFF\xFC\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 46
		"\xFF\xFF\xFF\xFF\xFF\xFE\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 47
		"\xFF\xFF\xFF\xFF\xFF\xFF\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 48
		"\xFF\xFF\xFF\xFF\xFF\xFF\x80\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 49
		"\xFF\xFF\xFF\xFF\xFF\xFF\xC0\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 50
		"\xFF\xFF\xFF\xFF\xFF\xFF\xE0\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 51
		"\xFF\xFF\xFF\xFF\xFF\xFF\xF0\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 52
		"\xFF\xFF\xFF\xFF\xFF\xFF\xF8\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 53
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFC\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 54
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFE\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 55
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x00\x00\x00\x00\x00\x00\x00\x00\x00", // 56
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x80\x00\x00\x00\x00\x00\x00\x00\x00", // 57
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xC0\x00\x00\x00\x00\x00\x00\x00\x00", // 58
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xE0\x00\x00\x00\x00\x00\x00\x00\x00", // 59
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF0\x00\x00\x00\x00\x00\x00\x00\x00", // 60
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF8\x00\x00\x00\x00\x00\x00\x00\x00", // 61
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFC\x00\x00\x00\x00\x00\x00\x00\x00", // 62
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFE\x00\x00\x00\x00\x00\x00\x00\x00", // 63
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x00\x00\x00\x00\x00\x00\x00\x00", // 64
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x80\x00\x00\x00\x00\x00\x00\x00", // 65
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xC0\x00\x00\x00\x00\x00\x00\x00", // 66
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xE0\x00\x00\x00\x00\x00\x00\x00", // 67
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF0\x00\x00\x00\x00\x00\x00\x00", // 68
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF8\x00\x00\x00\x00\x00\x00\x00", // 69
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFC\x00\x00\x00\x00\x00\x00\x00", // 70
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFE\x00\x00\x00\x00\x00\x00\x00", // 71
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x00\x00\x00\x00\x00\x00\x00", // 72
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x80\x00\x00\x00\x00\x00\x00", // 73
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xC0\x00\x00\x00\x00\x00\x00", // 74
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xE0\x00\x00\x00\x00\x00\x00", // 75
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF0\x00\x00\x00\x00\x00\x00", // 76
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF8\x00\x00\x00\x00\x00\x00", // 77
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFC\x00\x00\x00\x00\x00\x00", // 78
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFE\x00\x00\x00\x00\x00\x00", // 79
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x00\x00\x00\x00\x00\x00", // 80
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x80\x00\x00\x00\x00\x00", // 81
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xC0\x00\x00\x00\x00\x00", // 82
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xE0\x00\x00\x00\x00\x00", // 83
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF0\x00\x00\x00\x00\x00", // 84
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF8\x00\x00\x00\x00\x00", // 85
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFC\x00\x00\x00\x00\x00", // 86
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFE\x00\x00\x00\x00\x00", // 87
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x00\x00\x00\x00\x00", // 88
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x80\x00\x00\x00\x00", // 89
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xC0\x00\x00\x00\x00", // 90
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xE0\x00\x00\x00\x00", // 91
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF0\x00\x00\x00\x00", // 92
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF8\x00\x00\x00\x00", // 93
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFC\x00\x00\x00\x00", // 94
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFE\x00\x00\x00\x00", // 95
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x00\x00\x00\x00", // 96
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x80\x00\x00\x00", // 97
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xC0\x00\x00\x00", // 98
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xE0\x00\x00\x00", // 99
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF0\x00\x00\x00", // 100
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF8\x00\x00\x00", // 101
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFC\x00\x00\x00", // 102
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFE\x00\x00\x00", // 103
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x00\x00\x00", // 104
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x80\x00\x00", // 105
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xC0\x00\x00", // 106
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xE0\x00\x00", // 107
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF0\x00\x00", // 108
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF8\x00\x00", // 109
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFC\x00\x00", // 110
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFE\x00\x00", // 111
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x00\x00", // 112
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x80\x00", // 113
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xC0\x00", // 114
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xE0\x00", // 115
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF0\x00", // 116
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF8\x00", // 117
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFC\x00", // 118
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFE\x00", // 119
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x00", // 120
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x80", // 121
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xC0", // 122
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xE0", // 123
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF0", // 124
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xF8", // 125
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFC", // 126
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFE", // 127
		"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF", // 128
	);

	if ($prefix_len >= 0 and $prefix_len <= 128)
		return $mask[$prefix_len];
	else
		throw new InvalidArgException ('prefix_len', $prefix_len);
}

// This function looks up 'has_problems' flag for 'T' atoms
// and modifies 'hl' key. May be, this should be better done
// in amplifyCell(). We don't honour 'skipped' key, because
// the function is also used for thumb creation.
function markupObjectProblems (&$rackData)
{
	for ($i = $rackData['height']; $i > 0; $i--)
		for ($locidx = 0; $locidx < 3; $locidx++)
			if ($rackData[$i][$locidx]['state'] == 'T')
			{
				$object = spotEntity ('object', $rackData[$i][$locidx]['object_id']);
				if ($object['has_problems'] == 'yes')
				{
					// Object can be already highlighted.
					if (isset ($rackData[$i][$locidx]['hl']))
						$rackData[$i][$locidx]['hl'] = $rackData[$i][$locidx]['hl'] . 'w';
					else
						$rackData[$i][$locidx]['hl'] = 'w';
				}
			}
}

// Return a uniformly (010203040506 or 0102030405060708) formatted address, if it is present
// in the provided string, an empty string for an empty string or raise an exception.
function l2addressForDatabase ($string)
{
	$string = strtoupper ($string);
	switch (TRUE)
	{
		case ($string == '' or preg_match (RE_L2_SOLID, $string) or preg_match (RE_L2_WWN_SOLID, $string)):
			return $string;
		case (preg_match (RE_L2_IFCFG, $string) or preg_match (RE_L2_WWN_COLON, $string)):
			// reformat output of SunOS ifconfig
			$ret = '';
			foreach (explode (':', $string) as $byte)
				$ret .= (strlen ($byte) == 1 ? '0' : '') . $byte;
			return $ret;
		case (preg_match (RE_L2_CISCO, $string)):
			return str_replace ('.', '', $string);
		case (preg_match (RE_L2_HUAWEI, $string)):
			return str_replace ('-', '', $string);
		case (preg_match (RE_L2_IPCFG, $string) or preg_match (RE_L2_WWN_HYPHEN, $string)):
			return str_replace ('-', '', $string);
		default:
			throw new InvalidArgException ('$string', $string, 'malformed MAC/WWN address');
	}
}

function l2addressFromDatabase ($string)
{
	switch (strlen ($string))
	{
		case 12: // Ethernet
		case 16: // FireWire/Fibre Channel
			$ret = implode (':', str_split ($string, 2));
			break;
		default:
			$ret = $string;
			break;
	}
	return $ret;
}

// The following 2 functions return previous and next rack IDs for
// a given rack ID. The order of racks is the same as in renderRackspace()
// or renderRow().
function getPrevIDforRack ($row_id, $rack_id)
{
	$rackList = listCells ('rack', $row_id);
	doubleLink ($rackList);
	if (isset ($rackList[$rack_id]['prev_key']))
		return $rackList[$rack_id]['prev_key'];
	return NULL;
}

function getNextIDforRack ($row_id, $rack_id)
{
	$rackList = listCells ('rack', $row_id);
	doubleLink ($rackList);
	if (isset ($rackList[$rack_id]['next_key']))
		return $rackList[$rack_id]['next_key'];
	return NULL;
}

// This function finds previous and next array keys for each array key and
// modifies its argument accordingly.
function doubleLink (&$array)
{
	$prev_key = NULL;
	foreach (array_keys ($array) as $key)
	{
		if ($prev_key)
		{
			$array[$key]['prev_key'] = $prev_key;
			$array[$prev_key]['next_key'] = $key;
		}
		$prev_key = $key;
	}
}

function sortTokenize ($a, $b)
{
	$aold='';
	while ($a != $aold)
	{
		$aold=$a;
		$a = preg_replace('/[^a-zA-Z0-9]/',' ',$a);
		$a = preg_replace('/([0-9])([a-zA-Z])/','\\1 \\2',$a);
		$a = preg_replace('/([a-zA-Z])([0-9])/','\\1 \\2',$a);
	}

	$bold='';
	while ($b != $bold)
	{
		$bold=$b;
		$b = preg_replace('/[^a-zA-Z0-9]/',' ',$b);
		$b = preg_replace('/([0-9])([a-zA-Z])/','\\1 \\2',$b);
		$b = preg_replace('/([a-zA-Z])([0-9])/','\\1 \\2',$b);
	}



	$ar = explode(' ', $a);
	$br = explode(' ', $b);
	for ($i=0; $i<count($ar) && $i<count($br); $i++)
	{
		$ret = 0;
		if (is_numeric($ar[$i]) and is_numeric($br[$i]))
			$ret = ($ar[$i]==$br[$i])?0:($ar[$i]<$br[$i]?-1:1);
		else
			$ret = strcasecmp($ar[$i], $br[$i]);
		if ($ret != 0)
			return $ret;
	}
	if ($i<count($ar))
		return 1;
	if ($i<count($br))
		return -1;
	return 0;
}

// This function returns an array of single element of object's FQDN attribute,
// if FQDN is set. The next choice is object's common name, if it looks like a
// hostname. Otherwise an array of all 'regular' IP addresses of the
// object is returned (which may appear 0 and more elements long).
function findAllEndpoints ($object_id, $fallback = '')
{
	foreach (getAttrValues ($object_id) as $record)
		if ($record['id'] == 3 && strlen ($record['value'])) // FQDN
			return array ($record['value']);
	$regular = array();
	foreach (getObjectIPv4AllocationList ($object_id) as $ip_bin => $alloc)
		if ($alloc['type'] == 'regular')
			$regular[] = ip4_format ($ip_bin);
	// FIXME: add IPv6 allocations to this list
	if (!count ($regular) && strlen ($fallback))
		return array ($fallback);
	return $regular;
}

// Some records in the dictionary may be written as plain text or as Wiki
// link in the following syntax:
// 1. word
// 2. [[word URL]] // FIXME: this isn't working
// 3. [[word word word | URL]]
// This function parses the line in $record['value'] and modifies $record:
// $record['o_value'] is set to be the first part of link (word word word)
// $record['a_value'] is the same, but with %GPASS and %GSKIP macros applied
// $record['href'] is set to URL if it is specified in the input value
function parseWikiLink (&$record)
{
	if (! preg_match ('/^\[\[(.+)\]\]$/', $record['value'], $matches))
		$record['o_value'] = $record['value'];
	else
	{
		$s = explode ('|', $matches[1]);
		if (isset ($s[1]))
			$record['href'] = trim ($s[1]);
		$record['o_value'] = trim ($s[0]);
	}
	$record['a_value'] = execGMarker ($record['o_value']);
}

// FIXME: should this be saved as "P-data"?
function execGMarker ($line)
{
	return preg_replace ('/^.+%GSKIP%/', '', preg_replace ('/^(.+)%GPASS%/', '\\1 ', $line));
}

// rackspace usage for a single rack
// (T + W + U) / (height * 3 - A)
function getRSUforRack ($data)
{
	$counter = array ('A' => 0, 'U' => 0, 'T' => 0, 'W' => 0, 'F' => 0);
	for ($unit_no = $data['height']; $unit_no > 0; $unit_no--)
		for ($locidx = 0; $locidx < 3; $locidx++)
			$counter[$data[$unit_no][$locidx]['state']]++;
	return ($counter['T'] + $counter['W'] + $counter['U']) / ($counter['T'] + $counter['W'] + $counter['U'] + $counter['F']);
}

// Same for row.
function getRSUforRow ($rowData)
{
	if (!count ($rowData))
		return 0;
	$counter = array ('A' => 0, 'U' => 0, 'T' => 0, 'W' => 0, 'F' => 0);
	$total_height = 0;
	foreach (array_keys ($rowData) as $rack_id)
	{
		$data = spotEntity ('rack', $rack_id);
		amplifyCell ($data);
		$total_height += $data['height'];
		for ($unit_no = $data['height']; $unit_no > 0; $unit_no--)
			for ($locidx = 0; $locidx < 3; $locidx++)
				$counter[$data[$unit_no][$locidx]['state']]++;
	}
	return ($counter['T'] + $counter['W'] + $counter['U']) / ($counter['T'] + $counter['W'] + $counter['U'] + $counter['F']);
}

// Find any URL in a string and replace it with a clickable link
// Adopted from UrlLinker: https://bitbucket.org/kwi/urllinker/src
function string_insert_hrefs ($s)
{
	if (getConfigVar ('DETECT_URLS') != 'yes')
		return $s;

	$rexProtocol  = '(https?://)?';
	$rexDomain    = '(?:[-a-zA-Z0-9]{1,63}\.)+[a-zA-Z][-a-zA-Z0-9]{1,62}';
	$rexIp        = '(?:[1-9][0-9]{0,2}\.|0\.){3}(?:[1-9][0-9]{0,2}|0)'; // doesn't support IPv6 addresses
	$rexPort      = '(:[0-9]{1,5})?';
	$rexPath      = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
	$rexQuery     = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
	$rexFragment  = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
	$rexUsername  = '[^]\\\\\x00-\x20\"(),:-<>[\x7f-\xff]{1,64}';
	$rexPassword  = $rexUsername; // allow the same characters as in the username
	$rexUrl       = "$rexProtocol(?:($rexUsername)(:$rexPassword)?@)?($rexDomain|$rexIp)($rexPort$rexPath$rexQuery$rexFragment)";
	$rexUrlLinker = "{\\b$rexUrl(?=[?.!,;:\"]?(\s|$))}";

	$html = '';
	$position = 0;
	while (preg_match($rexUrlLinker, $s, $match, PREG_OFFSET_CAPTURE, $position))
	{
		list($url, $urlPosition) = $match[0];

		// Add the text leading up to the URL.
		$html .= substr($s, $position, $urlPosition - $position);

		$protocol    = $match[1][0];
		$username    = $match[2][0];
		$password    = $match[3][0];
		$domain      = $match[4][0];
		$afterDomain = $match[5][0]; // everything following the domain
		$port        = $match[6][0];
		$path        = $match[7][0];

		// Do not permit implicit protocol if a password is specified, as
		// this causes too many errors (e.g. "my email:foo@example.org").
		if (!$protocol && $password)
		{
			$html .= htmlspecialchars($username);

			// Continue text parsing at the ':' following the "username".
			$position = $urlPosition + strlen($username);
			continue;
		}

		if (!$protocol && $username && !$password && !$afterDomain)
		{
			// Looks like an email address.
			$emailUrl = TRUE;
			$completeUrl = "mailto:$url";
			$linkText = $url;
		}
		else
		{
			// Prepend http:// if no protocol specified
			$completeUrl = $protocol ? $url : "http://$url";
			$linkText = "$protocol$domain$port$path";
		}

		$linkHtml = '<a href="' . htmlspecialchars($completeUrl) . '">'
			. htmlspecialchars($linkText)
			. '</a>';

		// It's not an e-mail address, provide an additional link with a new window/tab as the target
		if (!isset($emailUrl))
		 	$linkHtml .= ' [<a href="' . htmlspecialchars($completeUrl) . '" target="_blank">^</a>]';
		unset($emailUrl);

		// Add the hyperlink.
		$html .= $linkHtml;

		// Continue text parsing from after the URL.
		$position = $urlPosition + strlen($url);
	}

	// Add the remainder of the text.
	$html .= substr($s, $position);
	return $html;
}

// Parse AUTOPORTS_CONFIG and return a list of generated pairs (port_type, port_name)
// for the requested object_type_id.
function getAutoPorts ($type_id)
{
	$ret = array();
	$typemap = explode (';', str_replace (' ', '', getConfigVar ('AUTOPORTS_CONFIG')));
	foreach ($typemap as $equation)
	{
		$tmp = explode ('=', $equation);
		if (count ($tmp) != 2)
			continue;
		$objtype_id = $tmp[0];
		if ($objtype_id != $type_id)
			continue;
		$portlist = $tmp[1];
		foreach (explode ('+', $portlist) as $product)
		{
			$tmp = explode ('*', $product);
			if (count ($tmp) > 4 || count ($tmp) < 3)
				continue;
			# format: <number of ports>*<port_type_id>[*<sprintf_name>*<startnumber>]
			$nports = $tmp[0];
			$port_type = $tmp[1];
			$format = $tmp[2];
			$startnum = isset ($tmp[3]) ? $tmp[3] : 0;
			for ($i = 0; $i < $nports; $i++)
				$ret[] = array ('type' => $port_type, 'name' => @sprintf ($format, $i + $startnum));
		}
	}
	return $ret;
}

// Use pre-served trace to traverse the tree, then place given node where it belongs.
function pokeNode (&$tree, $trace, $key, $value, $threshold = 0)
{
	// This function needs the trace to be followed FIFO-way. The fastest
	// way to do so is to use array_push() for putting values into the
	// list and array_shift() for getting them out. This exposed up to 11%
	// performance gain compared to other patterns of array_push/array_unshift/
	// array_reverse/array_pop/array_shift conjunction.
	$myid = array_shift ($trace);
	if (!count ($trace)) // reached the target
	{
		if (!$threshold or ($threshold and $tree[$myid]['kidc'] + 1 < $threshold))
			$tree[$myid]['kids'][$key] = $value;
		// Reset accumulated records once, when the limit is reached, not each time
		// after that.
		if (++$tree[$myid]['kidc'] == $threshold)
			$tree[$myid]['kids'] = array();
	}
	else // not yet
	{
		$self = __FUNCTION__;
		$self ($tree[$myid]['kids'], $trace, $key, $value, $threshold);
	}
}

// Likewise traverse the tree with the trace and return the final node.
function peekNode ($tree, $trace, $target_id)
{
	$self = __FUNCTION__;
	if (NULL === ($next = array_shift ($trace))) // warm
	{
		foreach ($tree as $node)
			if (array_key_exists ('id', $node) and $node['id'] == $target_id) // hot
				return $node;
	}
	else // cold
	{
		foreach ($tree as $node)
			if (array_key_exists ('id', $node) and $node['id'] == $next) // warmer
				return $self ($node['kids'], $trace, $target_id);
	}
	throw new RackTablesError ('inconsistent tree data', RackTablesError::INTERNAL);
}

function treeItemCmp ($a, $b)
{
	return $a['__tree_index'] - $b['__tree_index'];
}

// Build a tree from the item list and return it. Input and output data is
// indexed by item id (nested items in output are recursively stored in 'kids'
// key, which is in turn indexed by id. Functions, which are ready to handle
// tree collapsion/expansion themselves, may request non-zero threshold value
// for smaller resulting tree.
function treeFromList (&$orig_nodelist, $threshold = 0, $return_main_payload = TRUE)
{
	$tree = array();
	$nodelist = $orig_nodelist;

	// index the tree items by their order in $orig_nodelist
	$ti = 0;
	foreach ($nodelist as &$node_ref)
	{
		$node_ref['__tree_index'] = $ti++;
		$node_ref['kidc'] = 0;
		$node_ref['kids'] = array();
	}

	// Array equivalent of traceEntity() function.
	$trace = array();
	do
	{
		$nextpass = FALSE;
		foreach (array_keys ($nodelist) as $nodeid)
		{
			$node = $nodelist[$nodeid];
			$parentid = $node['parent_id'];
			// When adding a node to the working tree, book another
			// iteration, because the new item could make a way for
			// others onto the tree. Also remove any item added from
			// the input list, so iteration base shrinks.
			// First check if we can assign directly.
			if ($parentid == NULL)
			{
				$tree[$nodeid] = $node;
				$trace[$nodeid] = array(); // Trace to root node is empty
				unset ($nodelist[$nodeid]);
				$nextpass = TRUE;
			}
			// Now look if it fits somewhere on already built tree.
			elseif (isset ($trace[$parentid]))
			{
				// Trace to a node is a trace to its parent plus parent id.
				$trace[$nodeid] = $trace[$parentid];
				$trace[$nodeid][] = $parentid;
				pokeNode ($tree, $trace[$nodeid], $nodeid, $node, $threshold);
				// path to any other node is made of all parent nodes plus the added node itself
				unset ($nodelist[$nodeid]);
				$nextpass = TRUE;
			}
		}
	}
	while ($nextpass);
	if (!$return_main_payload)
		return $nodelist;
	// update each input node with its backtrace route
	foreach ($trace as $nodeid => $route)
		$orig_nodelist[$nodeid]['trace'] = $route;
	sortTree ($tree, 'treeItemCmp'); // sort the resulting tree by the order in original list
	return $tree;
}

// Build a tree from the tag list and return everything _except_ the tree.
// IOW, return taginfo items, which have parent_id set and pointing outside
// of the "normal" tree, which originates from the root.
function getOrphanedTags ()
{
	global $taglist;
	return treeFromList ($taglist, 0, FALSE);
}

// removes implicit tags from ['etags'] array and fills ['itags'] array
// Replaces call sequence "getExplicitTagsOnly, getImplicitTags"
function sortEntityTags (&$cell)
{
	global $taglist;
	if (! is_array ($cell['etags']))
		throw new InvalidArgException ('$cell[etags]', $cell['etags']);
	$cell['itags'] = array();
	foreach ($cell['etags'] as $tag_id => $taginfo)
		foreach ($taglist[$tag_id]['trace'] as $parent_id)
		{
			$cell['itags'][$parent_id] = $taglist[$parent_id];
			unset ($cell['etags'][$parent_id]);
		}
}

// Return the list of missing implicit tags.
function getImplicitTags ($oldtags)
{
	global $taglist;
	$tmp = array();
	foreach ($oldtags as $taginfo)
		$tmp = array_merge ($tmp, $taglist[$taginfo['id']]['trace']);
	// don't call array_unique here, it is in the function we will call now
	return buildTagChainFromIds ($tmp);
}

// Minimize the chain: exclude all implicit tags and return the result.
// This function makes use of an external cache with a miss/hit ratio
// about 3/7 (ticket:255).
function getExplicitTagsOnly ($chain)
{
	global $taglist, $tagRelCache;
	$ret = array();
	foreach (array_keys ($chain) as $keyA) // check each A
	{
		$tagidA = $chain[$keyA]['id'];
		// do not include A in result, if A is seen on the trace of any B!=A
		foreach (array_keys ($chain) as $keyB)
		{
			$tagidB = $chain[$keyB]['id'];
			if ($tagidA == $tagidB)
				continue;
			if (!isset ($tagRelCache[$tagidA][$tagidB]))
				$tagRelCache[$tagidA][$tagidB] = in_array ($tagidA, $taglist[$tagidB]['trace']);
			if ($tagRelCache[$tagidA][$tagidB] === TRUE) // A is ancestor of B
				continue 2; // skip this A
		}
		$ret[] = $chain[$keyA];
	}
	return $ret;
}

// Check, if the given tag is present on the chain (will only work
// for regular tags with tag ID set.
function tagOnChain ($taginfo, $tagchain)
{
	if (!isset ($taginfo['id']))
		return FALSE;
	foreach ($tagchain as $test)
		if ($test['id'] == $taginfo['id'])
			return TRUE;
	return FALSE;
}

function tagNameOnChain ($tagname, $tagchain)
{
	foreach ($tagchain as $test)
		if ($test['tag'] == $tagname)
			return TRUE;
	return FALSE;
}

// Return TRUE, if two tags chains differ (order of tags doesn't matter).
// Assume, that neither of the lists contains duplicates.
// FIXME: a faster, than O(x^2) method is possible for this calculation.
function tagChainCmp ($chain1, $chain2)
{
	if (count ($chain1) != count ($chain2))
		return TRUE;
	foreach ($chain1 as $taginfo1)
		if (!tagOnChain ($taginfo1, $chain2))
			return TRUE;
	return FALSE;
}


// returns the subtree of $tagtree representing child tags of $tagid
// returns NULL if error occured
function getTagSubtree ($tagid)
{
	global $tagtree, $taglist;

	$subtree = array ('kids' => $tagtree);
	$trace = $taglist[$tagid]['trace'];
	$trace[] = $tagid;
	while (count ($trace))
	{
		$search_for = array_shift ($trace);
		foreach ($subtree['kids'] as $subtag)
			if ($subtag['id'] == $search_for)
			{
				$subtree = $subtag;
				continue 2;
			}
		return NULL;
	}
	return $subtree;
}

// returns an array of tag ids which have $tagid as its parent (all levels)
function getTagDescendents ($tagid)
{
	$ret = array();
	if ($subtree = getTagSubtree ($tagid))
	{
		$stack = array ($subtree);
		while (count ($stack))
		{
			$subtree = array_pop ($stack);
			foreach ($subtree['kids'] as $subtag)
			{
				$ret[] = $subtag['id'];
				array_push ($stack, $subtag);
			}
		}
	}
	return $ret;
}

function redirectIfNecessary ()
{
	global
		$trigger,
		$pageno,
		$tabno;
	@session_start();
	if
	(
		! isset ($_REQUEST['tab']) and
		isset ($_SESSION['RTLT'][$pageno]) and
		getConfigVar ('SHOW_LAST_TAB') == 'yes' and
		permitted ($pageno, $_SESSION['RTLT'][$pageno]['tabname']) and
		time() - $_SESSION['RTLT'][$pageno]['time'] <= TAB_REMEMBER_TIMEOUT
	)
		redirectUser (buildRedirectURL ($pageno, $_SESSION['RTLT'][$pageno]['tabname']));

	// check if we accidentaly got on a dynamic tab that shouldn't be shown for this object
	if
	(
		isset ($trigger[$pageno][$tabno]) and
		!strlen (call_user_func ($trigger[$pageno][$tabno]))
	)
	{
		$_SESSION['RTLT'][$pageno]['dont_remember'] = 1;
		redirectUser (buildRedirectURL ($pageno, 'default'));
	}
	if (is_array (@$_SESSION['RTLT'][$pageno]) && isset ($_SESSION['RTLT'][$pageno]['dont_remember']))
		unset ($_SESSION['RTLT'][$pageno]['dont_remember']);
	// store the last visited tab name
	if (isset ($_REQUEST['tab']))
		$_SESSION['RTLT'][$pageno] = array ('tabname' => $tabno, 'time' => time());
	session_commit(); // if we are continuing to run, unlock session data
}

function prepareNavigation()
{
	global
		$pageno,
		$tabno;
	$pageno = (isset ($_REQUEST['page'])) ? $_REQUEST['page'] : 'index';

	if (isset ($_REQUEST['tab']))
		$tabno = $_REQUEST['tab'];
	else
		$tabno = 'default';
}

function fixContext ($target = NULL)
{
	global
		$pageno,
		$auto_tags,
		$expl_tags,
		$impl_tags,
		$target_given_tags,
		$user_given_tags,
		$etype_by_pageno,
		$page;

	if ($target !== NULL)
	{
		$target_given_tags = $target['etags'];
		// Don't reset autochain, because auth procedures could push stuff there in.
		// Another important point is to ignore 'user' realm, so we don't infuse effective
		// context with autotags of the displayed account.
		if ($target['realm'] != 'user')
			$auto_tags = array_merge ($auto_tags, $target['atags']);
	}
	elseif (array_key_exists ($pageno, $etype_by_pageno))
	{
		// Each page listed in the map above requires one uint argument.
		$target = spotEntity ($etype_by_pageno[$pageno], getBypassValue());
		$target_given_tags = $target['etags'];
		if ($target['realm'] != 'user')
			$auto_tags = array_merge ($auto_tags, $target['atags']);
	}
	elseif ($pageno == 'ipaddress' && $net = spotNetworkByIP (getBypassValue()))
	{
		// IP addresses inherit context tags from their parent networks
		$target_given_tags = $net['etags'];
		$auto_tags = array_merge ($auto_tags, $net['atags']);
	}
	// Explicit and implicit chains should be normally empty at this point, so
	// overwrite the contents anyway.
	$expl_tags = mergeTagChains ($user_given_tags, $target_given_tags);
	$impl_tags = getImplicitTags ($expl_tags);
}

# Merge e/i/a-tags of the given cell structures into current context, when
# these aren't there yet.
function spreadContext ($extracell)
{
	global
		$auto_tags,
		$expl_tags,
		$impl_tags,
		$target_given_tags,
		$user_given_tags;
	foreach ($extracell['atags'] as $taginfo)
		if (! tagNameOnChain ($taginfo['tag'], $auto_tags))
			$auto_tags[] = $taginfo;
	$target_given_tags = mergeTagChains ($target_given_tags, $extracell['etags']);
	$expl_tags = mergeTagChains ($user_given_tags, $target_given_tags);
	$impl_tags = getImplicitTags ($expl_tags);
}

# return a structure suitable for feeding into restoreContext()
function getContext()
{
	global
		$auto_tags,
		$expl_tags,
		$impl_tags,
		$target_given_tags;
	return array
	(
		'auto_tags' => $auto_tags,
		'expl_tags' => $expl_tags,
		'impl_tags' => $impl_tags,
		'target_given_tags' => $target_given_tags,
	);
}

function restoreContext ($ctx)
{
	global
		$auto_tags,
		$expl_tags,
		$impl_tags,
		$target_given_tags;
	$auto_tags = $ctx['auto_tags'];
	$expl_tags = $ctx['expl_tags'];
	$impl_tags = $ctx['impl_tags'];
	$target_given_tags = $ctx['target_given_tags'];
}

// Take a list of user-supplied tag IDs to build a list of valid taginfo
// records indexed by tag IDs (tag chain).
function buildTagChainFromIds ($tagidlist)
{
	global $taglist;
	$ret = array();
	foreach (array_unique ($tagidlist) as $tag_id)
		if (isset ($taglist[$tag_id]))
			$ret[] = $taglist[$tag_id];
	return $ret;
}

function buildTagIdsFromChain ($tagchain)
{
	$ret = array();
	foreach ($tagchain as $taginfo)
		$ret[] = $taginfo['id'];
	return array_unique ($ret);
}

// Process a given tag tree and return only meaningful branches. The resulting
// (sub)tree will have refcnt leaves on every last branch.
function getObjectiveTagTree ($tree, $realm, $preselect)
{
	$self = __FUNCTION__;
	$ret = array();
	foreach ($tree as $taginfo)
	{
		$subsearch = $self ($taginfo['kids'], $realm, $preselect);
		// If the current node addresses something, add it to the result
		// regardless of how many sub-nodes it features.
		if
		(
			isset ($taginfo['refcnt'][$realm]) or
			count ($subsearch) > 1 or
			in_array ($taginfo['id'], $preselect)
		)
			$ret[] = array
			(
				'id' => $taginfo['id'],
				'tag' => $taginfo['tag'],
				'parent_id' => $taginfo['parent_id'],
				'refcnt' => $taginfo['refcnt'],
				'kids' => $subsearch
			);
		else
			$ret = array_merge ($ret, $subsearch);
	}
	return $ret;
}

// Preprocess tag tree to get only tags which can effectively reduce given filter result,
// than passes shrinked tag tree to getObjectiveTagTree and return its result.
// This makes sense only if andor mode is 'and', otherwise function does not modify tree.
// 'Given filter' is a pair of $entity_list(filter result) and $preselect(filter data).
// 'Effectively' means reduce to non-empty result.
function getShrinkedTagTree ($entity_list, $realm, $preselect)
{
	global $tagtree;
	if ($preselect['andor'] != 'and' || empty($entity_list) && $preselect['is_empty'])
		return getObjectiveTagTree($tagtree, $realm, $preselect['tagidlist']);

	$used_tags = array(); //associative, keys - tag ids, values - taginfos
	foreach ($entity_list as $entity)
	{
		foreach ($entity['etags'] as $etag)
			if (! array_key_exists($etag['id'], $used_tags))
				$used_tags[$etag['id']] = 1;
			else
				$used_tags[$etag['id']]++;

		foreach ($entity['itags'] as $itag)
			if (! array_key_exists($itag['id'], $used_tags))
				$used_tags[$itag['id']] = 0;
	}

	$shrinked_tree = shrinkSubtree($tagtree, $used_tags, $preselect, $realm);
	return getObjectiveTagTree($shrinked_tree, $realm, $preselect['tagidlist']);
}

// deletes item from tag subtree unless it exists in $used_tags and not preselected
function shrinkSubtree ($tree, $used_tags, $preselect, $realm)
{
	$self = __FUNCTION__;

	foreach ($tree as $i => &$item)
	{
		$item['kids'] = $self($item['kids'], $used_tags, $preselect, $realm);
		$item['kidc'] = count($item['kids']);
		if
		(
			! array_key_exists($item['id'], $used_tags) &&
			! in_array($item['id'], $preselect['tagidlist']) &&
			! $item['kidc']
		)
			unset($tree[$i]);
		else {
			if (isset ($used_tags[$item['id']]) && $used_tags[$item['id']])
				$item['refcnt'][$realm] = $used_tags[$item['id']];
			else
				unset($item['refcnt'][$realm]);
		}
	}
	return $tree;
}

// Get taginfo record by tag name, return NULL, if record doesn't exist.
function getTagByName ($target_name)
{
	global $taglist;
	foreach ($taglist as $taginfo)
		if ($taginfo['tag'] == $target_name)
			return $taginfo;
	return NULL;
}

// Merge two chains, filtering dupes out. Return the resulting superset.
function mergeTagChains ($chainA, $chainB)
{
	// $ret = $chainA;
	// Reindex by tag id in any case.
	$ret = array();
	foreach ($chainA as $tag)
		$ret[$tag['id']] = $tag;
	foreach ($chainB as $tag)
		if (!isset ($ret[$tag['id']]))
			$ret[$tag['id']] = $tag;
	return $ret;
}

# Return a list consisting of tag ID of the given tree node and IDs of all
# nodes it contains.
function getTagIDListForNode ($treenode)
{
	$self = __FUNCTION__;
	$ret = array ($treenode['id']);
	foreach ($treenode['kids'] as $item)
		$ret = array_merge ($ret, $self ($item));
	return $ret;
}

function getCellFilter ()
{
	global $sic;
	global $pageno;
	$andor_used = FALSE;
	@session_start();
	// if the page is submitted we get an andor value so we know they are trying to start a new filter or clearing the existing one.
	if (isset($_REQUEST['andor']))
		$andor_used = TRUE;
	if ($andor_used || array_key_exists ('clear-cf', $_REQUEST))
		unset($_SESSION[$pageno]); // delete saved filter

	// otherwise inject saved filter to the $_REQUEST and $sic vars
	elseif (isset ($_SESSION[$pageno]['filter']) and is_array ($_SESSION[$pageno]['filter']) and getConfigVar ('STATIC_FILTER') == 'yes')
		foreach (array('andor', 'cfe', 'cft[]', 'cfp[]', 'nft[]', 'nfp[]') as $param)
		{
			$param = str_replace ('[]', '', $param, $is_array);
			if (! isset ($_REQUEST[$param]) and isset ($_SESSION[$pageno]['filter'][$param]) and (!$is_array or is_array ($_SESSION[$pageno]['filter'][$param])))
			{
				$_REQUEST[$param] = $_SESSION[$pageno]['filter'][$param];
				if (! $is_array)
					$sic[$param] = $_REQUEST[$param];
			}
		}

	$ret = array
	(
		'tagidlist' => array(),
		'tnamelist' => array(),
		'pnamelist' => array(),
		'negatedlist' => array(),
		'andor' => '',
		'text' => '',
		'extratext' => '',
		'expression' => array(),
		'urlextra' => '', // Just put text here and let makeHref call urlencode().
		'is_empty' => TRUE,
	);
	switch (TRUE)
	{
	case (!isset ($_REQUEST['andor'])):
		$andor = getConfigVar ('FILTER_DEFAULT_ANDOR');
		break;
	case ($_REQUEST['andor'] == 'and'):
	case ($_REQUEST['andor'] == 'or'):
		$_SESSION[$pageno]['filter']['andor'] = $_REQUEST['andor'];
		$ret['andor'] = $andor = $_REQUEST['andor'];
		break;
	default:
		showWarning ('Invalid and/or switch value in submitted form');
		return NULL;
	}
	// Both tags and predicates, which don't exist, should be
	// handled somehow. Discard them silently for now.
	global $taglist, $pTable;
	foreach (array ('cft', 'cfp', 'nft', 'nfp') as $param)
		if (isset ($_REQUEST[$param]) and is_array ($_REQUEST[$param]))
		{
			$_SESSION[$pageno]['filter'][$param] = $_REQUEST[$param];
			foreach ($_REQUEST[$param] as $req_key)
			{
				if (strpos ($param, 'ft') !== FALSE)
				{
					// param is a taglist
					if (! isset ($taglist[$req_key]))
						continue;
					$ret['tagidlist'][] = $req_key;
					$ret['tnamelist'][] = $taglist[$req_key]['tag'];
					$text = '{' . $taglist[$req_key]['tag'] . '}';
				}
				else
				{
					// param is a predicate list
					if (! isset ($pTable[$req_key]))
						continue;
					$ret['pnamelist'][] = $req_key;
					$text = '[' . $req_key . ']';
				}
				if (strpos ($param, 'nf') === 0)
				{
					$text = "not $text";
					$ret['negatedlist'][] = $req_key;
				}
				if (! empty ($ret['text']))
				{
					$andor_used = TRUE;
					$ret['text'] .= " $andor ";
				}
				$ret['text'] .= $text;
				$ret['urlextra'] .= '&' . $param . '[]=' . $req_key;
			}
		}
	// Extra text comes from TEXTAREA and is easily screwed by standard escaping function.
	if (isset ($sic['cfe']))
	{
		$_SESSION[$pageno]['filter']['cfe'] = $sic['cfe'];
		// Only consider extra text, when it is a correct RackCode expression.
		$parse = spotPayload ($sic['cfe'], 'SYNT_EXPR');
		if ($parse['result'] == 'ACK')
		{
			$ret['extratext'] = trim ($sic['cfe']);
			$ret['urlextra'] .= '&cfe=' . $ret['extratext'];
		}
	}
	$finaltext = array();
	if (strlen ($ret['text']))
		$finaltext[] = '(' . $ret['text'] . ')';
	if (strlen ($ret['extratext']))
		$finaltext[] = '(' . $ret['extratext'] . ')';
	$andor_used = $andor_used || (count($finaltext) > 1);
	$finaltext = implode (' ' . $andor . ' ', $finaltext);
	if (strlen ($finaltext))
	{
		$ret['is_empty'] = FALSE;
		$parse = spotPayload ($finaltext, 'SYNT_EXPR');
		$ret['expression'] = $parse['result'] == 'ACK' ? $parse['load'] : NULL;
		// It's not quite fair enough to put the blame of the whole text onto
		// non-empty "extra" portion of it, but it's the only user-generated portion
		// of it, thus the most probable cause of parse error.
		if (strlen ($ret['extratext']))
			$ret['extraclass'] = $parse['result'] == 'ACK' ? 'validation-success' : 'validation-error';
	}
	if (! $andor_used)
		$ret['andor'] = getConfigVar ('FILTER_DEFAULT_ANDOR');
	else
		$ret['urlextra'] .= '&andor=' . $ret['andor'];
	return $ret;
}

function buildRedirectURL ($nextpage = NULL, $nexttab = NULL, $moreArgs = array())
{
	global $page, $pageno, $tabno;
	if ($nextpage === NULL)
		$nextpage = $pageno;
	if ($nexttab === NULL)
		$nexttab = $tabno;
	$url = "index.php?page=${nextpage}&tab=${nexttab}";

	if ($nextpage === $pageno)
		fillBypassValues ($nextpage, $moreArgs);
	foreach ($moreArgs as $arg => $value)
		if (is_array ($value))
			foreach ($value as $v)
				$url .= '&' . urlencode ($arg . '[]') . '=' . urlencode ($v);
		elseif ($arg != 'module')
			$url .= '&' . urlencode ($arg) . '=' . urlencode ($value);
	return $url;
}

// store the accumulated message list into he $SESSION array to display them later
function backupLogMessages()
{
	global $log_messages;
	if (! empty ($log_messages))
	{
		@session_start();
		$_SESSION['log'] = $log_messages;
	}
}

function redirectUser ($url)
{
	backupLogMessages();
	header ("Location: " . $url);
	die;
}

function getRackCodeStats ()
{
	global $rackCode;
	$defc = $grantc = $modc = 0;
	foreach ($rackCode as $s)
		switch ($s['type'])
		{
			case 'SYNT_DEFINITION':
				$defc++;
				break;
			case 'SYNT_GRANT':
				$grantc++;
				break;
			case 'SYNT_CTXMOD':
				$modc++;
				break;
			default:
				break;
		}
	$ret = array
	(
		'Definition sentences' => $defc,
		'Grant sentences' => $grantc,
		'Context mod sentences' => $modc
	);
	return $ret;
}

function getRackImageWidth ()
{
	global $rtwidth;
	return 3 + $rtwidth[0] + $rtwidth[1] + $rtwidth[2] + 3;
}

function getRackImageHeight ($units)
{
	return 3 + 3 + $units * 2;
}

// Indicate occupation state of each IP address: none, ordinary or problematic.
function markupIPAddrList (&$addrlist)
{
	foreach (array_keys ($addrlist) as $ip_bin)
	{
		$refc = array
		(
			'shared' => 0,  // virtual
			'virtual' => 0, // loopback
			'regular' => 0, // connected host
			'router' => 0   // connected gateway
		);
		foreach ($addrlist[$ip_bin]['allocs'] as $a)
			$refc[$a['type']]++;
		$nvirtloopback = ($refc['shared'] + $refc['virtual'] + $refc['router'] > 0) ? 1 : 0; // modulus of virtual + shared + router
		$nreserved = ($addrlist[$ip_bin]['reserved'] == 'yes') ? 1 : 0; // only one reservation is possible ever
		$nrealms = $nreserved + $nvirtloopback + $refc['regular']; // last is connected allocation

		if ($nrealms == 1)
			$addrlist[$ip_bin]['class'] = 'trbusy';
		elseif ($nrealms > 1)
			$addrlist[$ip_bin]['class'] = 'trerror';
		elseif (! empty ($addrlist[$ip_bin]['vslist']) or ! empty ($addrlist[$ip_bin]['rsplist']))
			$addrlist[$ip_bin]['class'] = 'trbusy';
		else
			$addrlist[$ip_bin]['class'] = '';
	}
}

// Scan the given address list (returned by scanIPv4Space/scanIPv6Space) and return a list of all routers found.
function findRouters ($addrlist)
{
	$ret = array();
	foreach ($addrlist as $addr)
		foreach ($addr['allocs'] as $alloc)
			if ($alloc['type'] == 'router')
				$ret[] = array
				(
					'id' => $alloc['object_id'],
					'iface' => $alloc['name'],
					'dname' => $alloc['object_name'],
					'ip_bin' => $addr['ip_bin'],
				);
	return $ret;
}

// compare binary IPs (IPv4 are less than IPv6)
// valid return values are: 1, 0, -1
function IPCmp ($ip_binA, $ip_binB)
{
	if (strlen ($ip_binA) !== strlen ($ip_binB))
		return strlen ($ip_binA) < strlen ($ip_binB) ? -1 : 1;
	$ret = strcmp ($ip_binA, $ip_binB);
	$ret = ($ret > 0 ? 1 : ($ret < 0 ? -1 : 0));
	return $ret;
}

// Compare networks. When sorting a tree, the records on the list will have
// distinct base IP addresses.
// valid return values are: 1, 0, -1, -2
// -2 has special meaning: $netA includes $netB
// "The comparison function must return an integer less than, equal to, or greater
// than zero if the first argument is considered to be respectively less than,
// equal to, or greater than the second." (c) PHP manual
function IPNetworkCmp ($netA, $netB)
{
	$ret = IPCmp ($netA['ip_bin'], $netB['ip_bin']);
	if ($ret == 0)
		$ret = $netA['mask'] < $netB['mask'] ? -1 : ($netA['mask'] > $netB['mask'] ? 1 : 0);
	if ($ret == -1 and $netA['ip_bin'] === ($netB['ip_bin'] & $netA['mask_bin']))
		$ret = -2;
	return $ret;
}

function IPNetContainsOrEqual ($netA, $netB)
{
	$res = IPNetworkCmp ($netA, $netB);
	return ($res == -2 || $res == 0);
}

function IPNetContains ($netA, $netB)
{
	return (-2 == IPNetworkCmp ($netA, $netB));
}

function IPNetsIntersect ($netA, $netB)
{
	return ($netA['ip_bin'] & $netB['mask_bin']) === $netB['ip_bin'] ||
		($netB['ip_bin'] & $netA['mask_bin']) === $netA['ip_bin'];
}

function ip_in_range ($ip_bin, $range)
{
	return ($ip_bin & $range['mask_bin']) === $range['ip_bin'];
}

// Modify the given tag tree so, that each level's items are sorted alphabetically.
function sortTree (&$tree, $sortfunc = '')
{
	if (!strlen ($sortfunc))
		return;
	$self = __FUNCTION__;
	usort ($tree, $sortfunc);
	// Don't make a mistake of directly iterating over the items of current level, because this way
	// the sorting will be performed on a _copy_ if each item, not the item itself.
	foreach (array_keys ($tree) as $tagid)
		$self ($tree[$tagid]['kids'], $sortfunc);
}

function iptree_fill (&$netdata)
{
	if (!isset ($netdata['kids']) or !count ($netdata['kids']))
		return;
	// If we really have nested prefixes, they must fit into the tree.
	$worktree = $netdata;
	foreach ($netdata['kids'] as $pfx)
		iptree_embed ($worktree, $pfx);
	$netdata['kids'] = iptree_construct ($worktree);
	$netdata['kidc'] = count ($netdata['kids']);
}

function iptree_construct ($node)
{
	$self = __FUNCTION__;

	if (!isset ($node['right']))
	{
		if (!isset ($node['kids']))
		{
			$node['kids'] = array();
			$node['kidc'] = 0;
			$node['name'] = '';
		}
		return array ($node);
	}
	else
		return array_merge ($self ($node['left']), $self ($node['right']));
}

// returns TRUE if inet_ntop and inet_pton functions exist and support IPv6
function is_inet_avail()
{
	static $ret = NULL;
	if (! isset ($ret))
		$ret = is_callable ('inet_pton') && ! is_callable ('inet_ntop') && defined ('AF_INET6');
	return $ret;
}

function ip_format ($ip_bin)
{
	switch (strlen ($ip_bin))
	{
		case 4:  return ip4_format ($ip_bin);
		case 16: return ip6_format ($ip_bin);
		default: throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	}
}

function ip4_format ($ip_bin)
{
	if (4 == strlen ($ip_bin))
	{
		if (is_inet_avail())
		{
			$ret = @inet_ntop ($ip_bin);
			if ($ret !== FALSE)
				return $ret;
		}
		else
			return implode ('.', unpack ('C*', $ip_bin));
	}
	throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
}

function ip6_format ($ip_bin)
{
	do {
		if (16 != strlen ($ip_bin))
			break;

		if (is_inet_avail())
		{
			$ret = @inet_ntop ($ip_bin);
			if ($ret !== FALSE)
				return $ret;
			break;
		}

		// maybe this is IPv6-to-IPv4 address?
		if (substr ($ip_bin, 0, 12) == "\0\0\0\0\0\0\0\0\0\0\xff\xff")
			return '::ffff:' . implode ('.', unpack ('C*', substr ($ip_bin, 12, 4)));

		$result = array();
		$hole_index = NULL;
		$max_hole_index = NULL;
		$hole_length = 0;
		$max_hole_length = 0;

		for ($i = 0; $i < 8; $i++)
		{
			$unpacked = unpack ('n', substr ($ip_bin, $i * 2, 2));
			$value = array_shift ($unpacked);
			$result[] = dechex ($value & 0xffff);
			if ($value != 0)
			{
				unset ($hole_index);
				$hole_length = 0;
			}
			else
			{
				if (! isset ($hole_index))
					$hole_index = $i;
				if (++$hole_length >= $max_hole_length)
				{
					$max_hole_index = $hole_index;
					$max_hole_length = $hole_length;
				}
			}
		}
		if (isset ($max_hole_index))
		{
			array_splice ($result, $max_hole_index, $max_hole_length, array (''));
			if ($max_hole_index == 0 && $max_hole_length == 8)
				return '::';
			elseif ($max_hole_index == 0)
				return ':' . implode (':', $result);
			elseif ($max_hole_index + $max_hole_length == 8)
				return implode (':', $result) . ':';
		}
		return implode (':', $result);
	} while (FALSE);

	throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
}

function ip_parse ($ip)
{
	if (is_inet_avail())
	{
		if (FALSE !== ($ret = @inet_pton ($ip)))
			return $ret;
	}
	elseif (FALSE !== strpos ($ip, ':'))
		return ip6_parse ($ip);
	else
		return ip4_parse ($ip);

	throw new InvalidArgException ('ip', $ip, "Invalid IP address");
}

function ip4_parse ($ip)
{
	if (is_inet_avail())
	{
		if (FALSE !== ($ret = @inet_pton ($ip)))
			return $ret;
	}
	elseif (FALSE !== ($int = ip2long ($ip)))
		return pack ('N', $int);

	throw new InvalidArgException ('ip', $ip, "Invalid IPv4 address");
}

// returns 16-byte string ip_bin
// throws exception if unable to parse
function ip6_parse ($ip)
{
	do {
		if (is_inet_avail())
		{
			if (FALSE !== ($ret = @inet_pton ($ip)))
				return $ret;
			break;
		}

		if (empty ($ip))
			break;

		$result = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0"; // 16 bytes
		// remove one of double beginning/tailing colons
		if (substr ($ip, 0, 2) == '::')
			$ip = substr ($ip, 1);
		elseif (substr ($ip, -2, 2) == '::')
			$ip = substr ($ip, 0, strlen ($ip) - 1);

		$tokens = explode (':', $ip);
		$last_token = $tokens[count ($tokens) - 1];
		$split = explode ('.', $last_token);
		if (count ($split) == 4)
		{
			$hex_tokens = array();
			$hex_tokens[] = dechex ($split[0] * 256 + $split[1]);
			$hex_tokens[] = dechex ($split[2] * 256 + $split[3]);
			array_splice ($tokens, -1, 1, $hex_tokens);
		}
		if (count ($tokens) > 8)
			break;
		for ($i = 0; $i < count ($tokens); $i++)
		{
			if ($tokens[$i] != '')
			{
				if (! set_word_value ($result, $i, $tokens[$i]))
					break;
			}
			else
			{
				$k = 8; //index in result string (last word)
				for ($j = count ($tokens) - 1; $j > $i; $j--) // $j is an index in $tokens for reverse walk
					if ($tokens[$j] == '')
						break;
					elseif (! set_word_value ($result, --$k, $tokens[$j]))
						break;
				if ($i != $j)
					break; //error, more than 1 '::' range
				break;
			}
		}
		if (! isset ($k) && count ($tokens) != 8)
			break;
		return $result;
	} while (FALSE);

	throw new InvalidArgException ('ip', $ip, "Invalid IPv6 address");
}

function ip_get_arpa ($ip_bin)
{
	switch (strlen ($ip_bin))
	{
		case 4:  return ip4_get_arpa ($ip_bin);
		case 16: return ip6_get_arpa ($ip_bin);
		default: throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	}
}

function ip4_get_arpa ($ip_bin)
{
	$ret = '';
	for ($i = 3; $i >= 0; $i--)
		$ret .= ord($ip_bin[$i]) . '.';
	return $ret . 'in-addr.arpa';
}

function ip6_get_arpa ($ip_bin)
{
	$ret = '';
	$hex = implode ('', unpack('H32', $ip_bin));
	for ($i = 31; $i >= 0; $i--)
		$ret .= $hex[$i] . '.';
	return $ret . 'ip6.arpa';
}

function set_word_value (&$haystack, $nword, $hexvalue)
{
	// check that $hexvalue is like /^[0-9a-fA-F]*$/
	for ($i = 0; $i < strlen ($hexvalue); $i++)
	{
		$char = ord ($hexvalue[$i]);
		if (! ($char >= 0x30 && $char <= 0x39 || $char >= 0x41 && $char <= 0x46 || $char >=0x61 && $char <= 0x66))
			return FALSE;
	}
	$haystack = substr_replace ($haystack, pack ('n', hexdec ($hexvalue)), $nword * 2, 2);
	return TRUE;
}

// returns binary IP or FALSE
function ip_checkparse ($ip)
{
	try
	{
		return ip_parse ($ip);
	}
	catch (InvalidArgException $e)
	{
		return FALSE;
	}
}

// returns binary IP or FALSE
function ip4_checkparse ($ip)
{
	try
	{
		return ip4_parse ($ip);
	}
	catch (InvalidArgException $e)
	{
		return FALSE;
	}
}

// returns binary IP or FALSE
function ip6_checkparse ($ip)
{
	try
	{
		return ip6_parse ($ip);
	}
	catch (InvalidArgException $e)
	{
		return FALSE;
	}
}

function ip4_int2bin ($ip_int)
{
	return pack ('N', $ip_int + 0);
}

function ip4_bin2int ($ip_bin)
{
	if (4 != strlen ($ip_bin))
		throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	$ret = array_first (unpack ('N', $ip_bin));
	if (PHP_INT_SIZE > 4 && $ret < 0)
		$ret = $ret & 0xffffffff;
	return $ret;
}

// Use this function only when you need to export binary ip out of PHP running context (e.g., DB)
// !DO NOT perform arithmetic and bitwise operations with the result of this function!
function ip4_bin2db ($ip_bin)
{
	$ip_int = ip4_bin2int ($ip_bin);
	if ($ip_int < 0)
		return sprintf ('%u', 0x00000000 + $ip_int);
	else
		return $ip_int;
}

function ip_last ($net)
{
	return $net['ip_bin'] | ~$net['mask_bin'];
}

function ip_next ($ip_bin)
{
	$ret = $ip_bin;
	$p = 1;
	for ($i = strlen ($ret) - 1; $i >= 0; $i--)
	{
		$oct = $p + ord ($ret[$i]);
		$ret[$i] = chr ($oct & 0xff);
		if ($oct <= 255 and $oct >= 0)
			break;
	}
	return $ret;
}

function ip_prev ($ip_bin)
{
	$ret = $ip_bin;
	$p = -1;
	for ($i = strlen ($ret) - 1; $i >= 0; $i--)
	{
		$oct = $p + ord ($ret[$i]);
		$ret[$i] = chr ($oct & 0xff);
		if ($oct <= 255 and $oct >= 0)
			break;
	}
	return $ret;
}

function ip4_range_size ($range)
{
	return ip4_mask_size ($range['mask']);
}

function ip4_mask_size ($mask)
{
	switch (TRUE)
	{
		case ($mask > 1 && $mask <= 32):
			return (0x7fffffff >> ($mask - 1)) + 1;
		// constants below are not representable in 32-bit PHP's int type,
		// so they are literally hardcoded and returned as strings on 32-bit architecture.
		case ($mask == 1):
			return 2147483648;
		case ($mask == 0):
			return 4294967296;
		default:
			throw new InvalidArgException ('mask', $mask, 'Invalid IPv4 prefix length');
	}
}

// returns array with keys 'ip', 'ip_bin', 'mask', 'mask_bin'
function constructIPRange ($ip_bin, $mask)
{
	$node = array();
	switch (strlen ($ip_bin))
	{
		case 4: // IPv4
			if ($mask < 0 || $mask > 32)
				throw new InvalidArgException ('mask', $mask, "Invalid v4 prefix length");
			$node['mask_bin'] = ip4_mask ($mask);
			$node['mask'] = $mask;
			$node['ip_bin'] = $ip_bin & $node['mask_bin'];
			$node['ip'] = ip4_format ($node['ip_bin']);
			break;
		case 16: // IPv6
			if ($mask < 0 || $mask > 128)
				throw new InvalidArgException ('mask', $mask, "Invalid v6 prefix length");
			$node['mask_bin'] = ip6_mask ($mask);
			$node['mask'] = $mask;
			$node['ip_bin'] = $ip_bin & $node['mask_bin'];
			$node['ip'] = ip6_format ($node['ip_bin']);
			break;
		default:
			throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	}
	return $node;
}

// Return minimal IP address structure
function constructIPAddress ($ip_bin)
{
	// common v4/v6 part
	$ret = array
	(
		'ip' => ip_format ($ip_bin),
		'ip_bin' => $ip_bin,
		'name' => '',
		'comment' => '',
		'reserved' => 'no',
		'allocs' => array(),
		'vslist' => array(),
		'rsplist' => array(),
	);

	// specific v4 part
	if (strlen ($ip_bin) == 4)
		$ret = array_merge
		(
			$ret,
			array
			(
				'outpf' => array(),
				'inpf' => array(),
			)
		);
	return $ret;
}

function iptree_embed (&$node, $pfx)
{
	$self = __FUNCTION__;

	// hit?
	if (0 == IPNetworkCmp ($node, $pfx))
	{
		$node = $pfx;
		return;
	}
	if ($node['mask'] == $pfx['mask'])
		throw new RackTablesError ('the recurring loop lost control', RackTablesError::INTERNAL);

	// split?
	if (!isset ($node['right']))
	{
		$node['left']  = constructIPRange ($node['ip_bin'], $node['mask'] + 1);
		$node['right'] = constructIPRange (ip_last ($node), $node['mask'] + 1);
	}

	if (IPNetContainsOrEqual ($node['left'], $pfx))
		$self ($node['left'], $pfx);
	elseif (IPNetContainsOrEqual ($node['right'], $pfx))
		$self ($node['right'], $pfx);
	else
		throw new RackTablesError ('cannot decide between left and right', RackTablesError::INTERNAL);
}

function treeApplyFunc (&$tree, $func = '', $stopfunc = '')
{
	if (!strlen ($func))
		return;
	$self = __FUNCTION__;
	foreach (array_keys ($tree) as $key)
	{
		$func ($tree[$key]);
		if (strlen ($stopfunc) and $stopfunc ($tree[$key]))
			continue;
		$self ($tree[$key]['kids'], $func);
	}
}

function nodeIsCollapsed ($node)
{
	return $node['symbol'] == 'node-collapsed';
}

// sets 'addrlist', 'own_addrlist', 'addrc', 'own_addrc' keys of $node
// 'addrc' and 'own_addrc' are sizes of 'addrlist' and 'own_addrlist', respectively
function loadIPAddrList (&$node)
{
	$node['addrlist'] = scanIPSpace (array (array ('first' => $node['ip_bin'], 'last' => ip_last ($node))));

	if (! isset ($node['id']))
		$node['own_addrlist'] = $node['addrlist'];
	else
	{
		if ($node['kidc'] == 0)
			$node['own_addrlist'] = $node['addrlist'];
			//$node['own_addrlist'] = array();
		else
		{
			$node['own_addrlist'] = array();
			// node has childs
			foreach ($node['spare_ranges'] as $mask => $spare_list)
				foreach ($spare_list as $spare_ip)
				{
					$spare_range = constructIPRange ($spare_ip, $mask);
					foreach ($node['addrlist'] as $bin_ip => $addr)
						if (($bin_ip & $spare_range['mask_bin']) == $spare_range['ip_bin'])
							$node['own_addrlist'][$bin_ip] = $addr;
				}
		}
	}
	$node['addrc'] = count ($node['addrlist']);
	$node['own_addrc'] = count ($node['own_addrlist']);
}

// returns the array of structure described by constructIPAddress
function getIPAddress ($ip_bin)
{
	$scanres = scanIPSpace (array (array ('first' => $ip_bin, 'last' => $ip_bin)));
	if (empty ($scanres))
		return constructIPAddress ($ip_bin);
	markupIPAddrList ($scanres);
	return $scanres[$ip_bin];
}

function getIPv4Address ($ip_bin)
{
	if (strlen ($ip_bin) != 4)
		throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	return getIPAddress ($ip_bin);
}

function getIPv6Address ($ip_bin)
{
	if (strlen ($ip_bin) != 16)
		throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	return getIPAddress ($ip_bin);
}

function makeIPTree ($netlist)
{
	// treeFromList() requires parent_id to be correct for an item to get onto the tree,
	// so perform necessary pre-processing to calculate parent_id of each item of $netlist.
	$stack = array();
	foreach ($netlist as $net_id => &$net)
	{
		while (! empty ($stack))
		{
			$top_id = $stack[count ($stack) - 1];
			if (! IPNetContains ($netlist[$top_id], $net)) // unless $net is a child of stack top
				array_pop ($stack);
			else
			{
				$net['parent_id'] = $top_id;
				break;
			}
		}
		if (empty ($stack))
			$net['parent_id'] = NULL;
		array_push ($stack, $net_id);
	}
	unset ($stack);

	$tree = treeFromList ($netlist); // medium call
	return $tree;
}

function prepareIPTree ($netlist, $expanded_id = 0)
{
	$tree = makeIPTree ($netlist);
	// complement the tree before markup to make the spare networks have "symbol" set
	treeApplyFunc ($tree, 'iptree_fill');
	iptree_markup_collapsion ($tree, getConfigVar ('TREE_THRESHOLD'), $expanded_id);
	return $tree;
}

# Traverse IPv4/IPv6 tree and return a list of all networks, which
# exist in DB and don't have any sub-networks.
function getTerminalNetworks ($tree)
{
	$self = __FUNCTION__;
	$ret = array();
	foreach ($tree as $node)
		if ($node['kidc'] == 0 and isset ($node['realm']))
			$ret[] = $node;
		else
			$ret = array_merge ($ret, $self ($node['kids']));
	return $ret;
}

// Check all items of the tree recursively, until the requested target id is
// found. Mark all items leading to this item as "expanded", collapsing all
// the rest, which exceed the given threshold (if the threshold is given).
function iptree_markup_collapsion (&$tree, $threshold = 1024, $target = 0)
{
	$self = __FUNCTION__;
	$ret = FALSE;
	foreach (array_keys ($tree) as $key)
	{
		$here = ($target === 'ALL' or ($target > 0 and isset ($tree[$key]['id']) and $tree[$key]['id'] == $target));
		$below = $self ($tree[$key]['kids'], $threshold, $target);
		$expand_enabled = ($target !== 'NONE');
		if (!$tree[$key]['kidc']) // terminal node
			$tree[$key]['symbol'] = 'spacer';
		elseif ($expand_enabled and $tree[$key]['kidc'] < $threshold)
			$tree[$key]['symbol'] = 'node-expanded-static';
		elseif ($expand_enabled and ($here or $below))
			$tree[$key]['symbol'] = 'node-expanded';
		else
			$tree[$key]['symbol'] = 'node-collapsed';
		$ret = ($ret or $here or $below); // parentheses are necessary for this to be computed correctly
	}
	return $ret;
}

// Convert entity name to human-readable value
function formatEntityName ($name)
{
	switch ($name)
	{
		case 'ipv4net':
			return 'IPv4 Network';
		case 'ipv6net':
			return 'IPv6 Network';
		case 'ipv4rspool':
			return 'IPv4 RS Pool';
		case 'ipv4vs':
			return 'IPv4 Virtual Service';
		case 'object':
			return 'Object';
		case 'rack':
			return 'Rack';
		case 'row':
			return 'Row';
		case 'location':
			return 'Location';
		case 'user':
			return 'User';
	}
	return 'invalid';
}

// Display hrefs for all of a file's parents. If scissors are requested,
// prepend cutting button to each of them.
function serializeFileLinks ($links, $scissors = FALSE)
{
	$comma = '';
	$ret = '';
	foreach ($links as $link_id => $li)
	{
		switch ($li['entity_type'])
		{
			case 'ipv4net':
				$params = "page=ipv4net&id=";
				break;
			case 'ipv6net':
				$params = "page=ipv6net&id=";
				break;
			case 'ipv4rspool':
				$params = "page=ipv4rspool&pool_id=";
				break;
			case 'ipv4vs':
				$params = "page=ipv4vs&vs_id=";
				break;
			case 'object':
				$params = "page=object&object_id=";
				break;
			case 'rack':
				$params = "page=rack&rack_id=";
				break;
			case 'location':
				$params = "page=location&location_id=";
				break;
			case 'user':
				$params = "page=user&user_id=";
				break;
		}
		$ret .= $comma;
		if ($scissors)
		{
			$ret .= "<a href='" . makeHrefProcess(array('op'=>'unlinkFile', 'link_id'=>$link_id)) . "'";
			$ret .= getImageHREF ('cut') . '</a> ';
		}
		$ret .= sprintf("<a href='index.php?%s%s'>%s</a>", $params, $li['entity_id'], $li['name']);
		$comma = '<br>';
	}
	return $ret;
}

// Convert filesize to appropriate unit and make it human-readable
function formatFileSize ($bytes)
{
	// bytes
	if($bytes < 1024) // bytes
		return "${bytes} bytes";

	// kilobytes
	if ($bytes < 1024000)
		return sprintf ("%.1fk", round (($bytes / 1024), 1));

	// megabytes
	return sprintf ("%.1f MB", round (($bytes / 1024000), 1));
}

// Reverse of formatFileSize, it converts human-readable value to bytes
function convertToBytes ($value)
{
	$value = trim($value);
	$last = strtolower($value[strlen($value)-1]);
	switch ($last)
	{
		case 'g':
			$value *= 1024;
		case 'm':
			$value *= 1024;
		case 'k':
			$value *= 1024;
	}

	return $value;
}

// make "A" HTML element
function mkA ($text, $nextpage, $bypass = NULL, $nexttab = NULL)
{
	global $page, $tab;
	if ($text == '')
		throw new InvalidArgException ('text', $text);
	if (! array_key_exists ($nextpage, $page))
		throw new InvalidArgException ('nextpage', $nextpage, 'not found');
	$args = array ('page' => $nextpage);
	if ($nexttab !== NULL)
	{
		if (! array_key_exists ($nexttab, $tab[$nextpage]))
			throw new InvalidArgException ('nexttab', $nexttab, 'not found');
		$args['tab'] = $nexttab;
	}
	if (array_key_exists ('bypass', $page[$nextpage]))
	{
		if ($bypass === NULL)
			throw new InvalidArgException ('bypass', '(NULL)');
		$args[$page[$nextpage]['bypass']] = $bypass;
	}
	return '<a href="' . makeHref ($args) . '">' . $text . '</a>';
}

// make "HREF" HTML attribute
function makeHref ($params = array())
{
	$tmp = array();
	foreach ($params as $key => $value)
	{
		if (is_array ($value))
			$key .= "[]";
		else
			$value = array ($value);
		if (!count ($value))
			$tmp[] = urlencode ($key) . '=';
		else
			foreach ($value as $sub_value)
				$tmp[] = urlencode ($key) . '=' . urlencode ($sub_value);
	}
	return 'index.php?' . implode ('&', $tmp);
}

function makeHrefProcess ($params = array())
{
	global $pageno, $tabno, $page;
	$tmp = array();
	if (! array_key_exists ('page', $params))
		$params['page'] = $pageno;
	if (! array_key_exists ('tab', $params))
		$params['tab'] = $tabno;
	if ($params['page'] === $pageno)
		fillBypassValues ($pageno, $params);
	foreach ($params as $key => $value)
		$tmp[] = urlencode ($key) . '=' . urlencode ($value);
	return '?module=redirect&' . implode ('&', $tmp);
}

function makeHrefForHelper ($helper_name, $params = array())
{
	$ret = '?module=popup&helper=' . $helper_name;
	foreach($params as $key=>$value)
		$ret .= '&'.urlencode($key).'='.urlencode($value);
	return $ret;
}

// Process the given list of records to build data suitable for printNiftySelect()
// (like it was formerly executed by printSelect()). Screen out vendors according
// to VENDOR_SIEVE, if object type ID is provided. However, the OPTGROUP with already
// selected OPTION is protected from being screened.
function cookOptgroups ($recordList, $object_type_id = 0, $existing_value = 0)
{
	$ret = array();
	$therest = array();
	foreach ($recordList as $dict_key => $dict_value)
		if (preg_match ('/^(.*)%(GPASS|GSKIP)%/', $dict_value, $m))
			$ret[$m[1]][$dict_key] = execGMarker ($dict_value);
		else
			$therest[$dict_key] = $dict_value;

	// Always keep "other" OPTGROUP at the SELECT bottom.
	$ret['other'] = $therest;

	if ($object_type_id != 0)
	{
		$screenlist = array();
		foreach (explode (';', getConfigVar ('VENDOR_SIEVE')) as $sieve)
			if (preg_match ("/^([^@]+)(@${object_type_id})?\$/", trim ($sieve), $regs)){
				$screenlist[] = $regs[1];
			}
		foreach (array_keys ($ret) as $vendor)
			if (in_array ($vendor, $screenlist))
			{
				$ok_to_screen = TRUE;
				if ($existing_value)
					foreach (array_keys ($ret[$vendor]) as $recordkey)
						if ($recordkey == $existing_value)
						{
							$ok_to_screen = FALSE;
							break;
						}
				if ($ok_to_screen)
					unset ($ret[$vendor]);
			}
	}
	return $ret;
}

function unix2dos ($text)
{
	return str_replace ("\n", "\r\n", $text);
}

function buildPredicateTable ($parsetree)
{
	$ret = array();
	foreach ($parsetree as $sentence)
		if ($sentence['type'] == 'SYNT_DEFINITION')
			$ret[$sentence['term']] = $sentence['definition'];
	// Now we have predicate table filled in with the latest definitions of each
	// particular predicate met. This isn't as chik, as on-the-fly predicate
	// overloading during allow/deny scan, but quite sufficient for this task.
	return $ret;
}

// Take a list of records and filter against given RackCode expression. Return
// the original list intact, if there was no filter requested, but return an
// empty list, if there was an error.
function filterCellList ($list_in, $expression = array())
{
	if ($expression === NULL)
		return array();
	if (!count ($expression))
		return $list_in;
	$list_out = array();
	foreach ($list_in as $item_key => $item_value)
		if (TRUE === judgeCell ($item_value, $expression))
			$list_out[$item_key] = $item_value;
	return $list_out;
}

function eval_expression ($expr, $tagchain, $ptable, $silent = FALSE)
{
	$self = __FUNCTION__;
	switch ($expr['type'])
	{
		// Return true, if given tag is present on the tag chain.
		case 'LEX_TAG':
		case 'LEX_AUTOTAG':
			foreach ($tagchain as $tagInfo)
				if ($expr['load'] == $tagInfo['tag'])
					return TRUE;
			return FALSE;
		case 'LEX_PREDICATE': // Find given predicate in the symbol table and evaluate it.
			$pname = $expr['load'];
			if (!isset ($ptable[$pname]))
			{
				if (!$silent)
					showWarning ("Predicate '${pname}' is referenced before declaration");
				return NULL;
			}
			return $self ($ptable[$pname], $tagchain, $ptable);
		case 'LEX_TRUE':
			return TRUE;
		case 'LEX_FALSE':
			return FALSE;
		case 'SYNT_NOT_EXPR':
			$tmp = $self ($expr['load'], $tagchain, $ptable);
			if ($tmp === TRUE)
				return FALSE;
			elseif ($tmp === FALSE)
				return TRUE;
			else
				return $tmp;
		case 'SYNT_AND_EXPR': // binary AND
			if (FALSE == $self ($expr['left'], $tagchain, $ptable))
				return FALSE; // early failure
			return $self ($expr['right'], $tagchain, $ptable);
		case 'SYNT_EXPR': // binary OR
			if (TRUE == $self ($expr['left'], $tagchain, $ptable))
				return TRUE; // early success
			return $self ($expr['right'], $tagchain, $ptable);
		default:
			if (!$silent)
				showWarning ("Evaluation error, cannot process expression type '${expr['type']}'");
			return NULL;
			break;
	}
}

// Tell, if the given expression is true for the given entity. Take complete record on input.
function judgeCell ($cell, $expression)
{
	global $pTable;
	return eval_expression
	(
		$expression,
		array_merge
		(
			$cell['etags'],
			$cell['itags'],
			$cell['atags']
		),
		$pTable,
		TRUE
	);
}

function judgeContext ($expression)
{
	global $pTable, $expl_tags, $impl_tags, $auto_tags;
	return eval_expression
	(
		$expression,
		array_merge
		(
			$expl_tags,
			$impl_tags,
			$auto_tags
		),
		$pTable,
		TRUE
	);
}

// Tell, if a constraint from config option permits given record.
// An undefined $cell means current context.
function considerConfiguredConstraint ($cell, $varname)
{
	if (!strlen (getConfigVar ($varname)))
		return TRUE; // no restriction
	global $parseCache;
	if (!isset ($parseCache[$varname]))
		// getConfigVar() doesn't re-read the value from DB because of its
		// own cache, so there is no race condition here between two calls.
		$parseCache[$varname] = spotPayload (getConfigVar ($varname), 'SYNT_EXPR');
	if ($parseCache[$varname]['result'] != 'ACK')
		return FALSE; // constraint set, but cannot be used due to compilation error
	if (isset ($cell))
		return judgeCell ($cell, $parseCache[$varname]['load']);
	else
		return judgeContext ($parseCache[$varname]['load']);
}

// Tell, if the given arbitrary RackCode text addresses the given record
// (an empty text matches any record).
// An undefined $cell means current context.
function considerGivenConstraint ($cell, $filtertext)
{
	if ($filtertext == '')
		return TRUE;
	$parse = spotPayload ($filtertext, 'SYNT_EXPR');
	if ($parse['result'] != 'ACK')
		throw new InvalidRequestArgException ('filtertext', $filtertext, 'RackCode parsing error');
	if (isset ($cell))
		return judgeCell ($cell, $parse['load']);
	else
		return judgeContext ($parse['load']);
}

// Return list of records in the given realm, which conform to
// the given RackCode expression. If the realm is unknown or text
// doesn't validate as a RackCode expression, return NULL.
// Otherwise (successful scan) return a list of all matched
// records, even if the list is empty (array() !== NULL). If the
// text is an empty string, return all found records in the given
// realm.
function scanRealmByText ($realm = NULL, $ftext = '')
{
	switch ($realm)
	{
	case 'object':
	case 'rack':
	case 'user':
	case 'ipv4net':
	case 'ipv6net':
	case 'file':
	case 'ipv4vs':
	case 'ipv4rspool':
	case 'vst':
		if (!strlen ($ftext = trim ($ftext)))
			$fexpr = array();
		else
		{
			$fparse = spotPayload ($ftext, 'SYNT_EXPR');
			if ($fparse['result'] != 'ACK')
				return NULL;
			$fexpr = $fparse['load'];
		}
		return filterCellList (listCells ($realm), $fexpr);
	default:
		throw new InvalidArgException ('$realm', $realm);
	}

}

function getVSTOptions()
{
	$ret = array();
	foreach (listCells ('vst') as $vst)
		$ret[$vst['id']] = niftyString ($vst['description'], 30, FALSE);
	return $ret;
}

# Return an array in the format understood by getNiftySelect() and getOptionTree(),
# so that the options stand for all VLANs grouped by respective VLAN domains, except
# those listed on the "except" array.
function getAllVLANOptions ($except = array())
{
	$ret = array();
	foreach (getVLANDomainStats() as $domain)
		foreach (getDomainVLANs ($domain['id']) as $vlan)
			if (! array_key_exists ($domain['id'], $except) or ! in_array ($vlan['vlan_id'], $except[$domain['id']]))
				$ret[$domain['description']]["${domain['id']}-${vlan['vlan_id']}"] =
					"${vlan['vlan_id']} (${vlan['netc']}) ${vlan['vlan_descr']}";
	return $ret;
}

// Let's have this debug helper here to enable debugging of process.php w/o interface.php.
function dump ($var)
{
	echo '<div align=left><pre>';
	print_r ($var);
	echo '</pre></div>';
}

function getTagChart ($limit = 0, $realm = 'total', $special_tags = array())
{
	global $taglist;
	// first build top-N chart...
	$toplist = array();
	foreach ($taglist as $taginfo)
		if (isset ($taginfo['refcnt'][$realm]))
			$toplist[$taginfo['id']] = $taginfo['refcnt'][$realm];
	arsort ($toplist, SORT_NUMERIC);
	$ret = array();
	$done = 0;
	foreach (array_keys ($toplist) as $tag_id)
	{
		$ret[$tag_id] = $taglist[$tag_id];
		if (++$done == $limit)
			break;
	}
	// ...then make sure, that every item of the special list is shown
	// (using the same sort order)
	$extra = array();
	foreach ($special_tags as $taginfo)
		if (!array_key_exists ($taginfo['id'], $ret))
			$extra[$taginfo['id']] = $taglist[$taginfo['id']]['refcnt'][$realm];
	arsort ($extra, SORT_NUMERIC);
	foreach (array_keys ($extra) as $tag_id)
		$ret[] = $taglist[$tag_id];
	return $ret;
}

function decodeObjectType ($objtype_id, $style = 'r')
{
	static $types = array();
	if (!count ($types))
		$types = array
		(
			'r' => readChapter (CHAP_OBJTYPE),
			'a' => readChapter (CHAP_OBJTYPE, 'a'),
			'o' => readChapter (CHAP_OBJTYPE, 'o')
		);
	return $types[$style][$objtype_id];
}

function isolatedPermission ($p, $t, $cell)
{
	// This function is called from both "file" page and a number of other pages,
	// which have already fixed security context and authorized the user for it.
	// OTOH, it is necessary here to authorize against the current file, which
	// means saving the current context and building a new one.
	global
		$expl_tags,
		$impl_tags,
		$target_given_tags,
		$auto_tags;
	// push current context
	$orig_expl_tags = $expl_tags;
	$orig_impl_tags = $impl_tags;
	$orig_target_given_tags = $target_given_tags;
	$orig_auto_tags = $auto_tags;
	// retarget
	fixContext ($cell);
	// remember decision
	$ret = permitted ($p, $t);
	// pop context
	$expl_tags = $orig_expl_tags;
	$impl_tags = $orig_impl_tags;
	$target_given_tags = $orig_target_given_tags;
	$auto_tags = $orig_auto_tags;
	return $ret;
}

function getPortListPrefs()
{
	$ret = array();
	if (0 >= ($ret['iif_pick'] = getConfigVar ('DEFAULT_PORT_IIF_ID')))
		$ret['iif_pick'] = 1;
	$ret['oif_picks'] = array();
	foreach (explode (';', getConfigVar ('DEFAULT_PORT_OIF_IDS')) as $tmp)
	{
		$tmp = explode ('=', trim ($tmp));
		if (count ($tmp) == 2 and $tmp[0] > 0 and $tmp[1] > 0)
			$ret['oif_picks'][$tmp[0]] = $tmp[1];
	}
	// enforce default value
	if (!array_key_exists (1, $ret['oif_picks']))
		$ret['oif_picks'][1] = 24;
	$ret['selected'] = $ret['iif_pick'] . '-' . $ret['oif_picks'][$ret['iif_pick']];
	return $ret;
}

// Return data for printNiftySelect() with port type options. All OIF options
// for the default IIF will be shown, but only the default OIFs will be present
// for each other IIFs. IIFs, for which there is no default OIF, will not
// be listed.
// This SELECT will be used for the "add new port" form.
function getNewPortTypeOptions()
{
	$ret = array();
	$prefs = getPortListPrefs();
	foreach (getPortInterfaceCompat() as $row)
	{
		if ($row['iif_id'] == $prefs['iif_pick'])
			$optgroup = $row['iif_name'];
		elseif (array_key_exists ($row['iif_id'], $prefs['oif_picks']) and $prefs['oif_picks'][$row['iif_id']] == $row['oif_id'])
			$optgroup = 'other';
		else
			continue;
		if (!array_key_exists ($optgroup, $ret))
			$ret[$optgroup] = array();
		$ret[$optgroup][$row['iif_id'] . '-' . $row['oif_id']] = $row['oif_name'];
	}
	return $ret;
}

// Return a serialized version of VLAN configuration for a port.
// If a native VLAN is defined, print it first. All other VLANs
// are tagged and are listed after a plus sign. When no configuration
// is set for a port, return "default" string.
function serializeVLANPack ($vlanport)
{
	if (!array_key_exists ('mode', $vlanport))
		return 'error';
	switch ($vlanport['mode'])
	{
	case 'none':
		return 'none';
	case 'access':
		$ret = 'A';
		break;
	case 'trunk':
		$ret = 'T';
		break;
	case 'uplink':
		$ret = 'U';
		break;
	case 'downlink':
		$ret = 'D';
		break;
	default:
		return 'error';
	}
	if ($vlanport['native'])
		$ret .= $vlanport['native'];
	$tagged_bits = groupIntsToRanges ($vlanport['allowed'], $vlanport['native']);
	if (count ($tagged_bits))
		$ret .= '+' . implode (', ', $tagged_bits);
	return strlen ($ret) ? $ret : 'default';
}

function groupIntsToRanges ($list, $exclude_value = NULL)
{
	$result = array();
	sort ($list);
	$id_from = $id_to = 0;
	$list[] = -1;
	foreach ($list as $next_id)
		if (!isset ($exclude_value) or $next_id != $exclude_value)
			if ($id_to && $next_id == $id_to + 1)
				$id_to = $next_id; // merge
			else
			{
				if ($id_to)
					$result[] = $id_from == $id_to ? $id_from : "${id_from}-${id_to}"; // flush
				$id_from = $id_to = $next_id; // start next pair
			}
	return $result;
}

// Decode VLAN compound key (which is a string formatted DOMAINID-VLANID) and
// return the numbers as an array of two.
function decodeVLANCK ($string)
{
	$matches = array();
	if (1 != preg_match ('/^([[:digit:]]+)-([[:digit:]]+)$/', $string, $matches))
		throw new InvalidArgException ('VLAN compound key', $string);
	return array ($matches[1], $matches[2]);
}

// Return VLAN name formatted for HTML output (note, that input
// argument comes from database unescaped).
function formatVLANAsOption ($vlaninfo)
{
	$ret = $vlaninfo['vlan_id'];
	if ($vlaninfo['vlan_descr'] != '')
		$ret .= ' ' . niftyString ($vlaninfo['vlan_descr']);
	return $ret;
}

function formatVLANAsLabel ($vlaninfo)
{
	$ret = $vlaninfo['vlan_id'];
	if ($vlaninfo['vlan_descr'] != '')
		$ret .= ' <i>(' . niftyString ($vlaninfo['vlan_descr']) . ')</i>';
	return $ret;
}

function formatVLANAsPlainText ($vlaninfo)
{
	$ret = 'VLAN' . $vlaninfo['vlan_id'];
	if ($vlaninfo['vlan_descr'] != '')
		$ret .= ' (' . niftyString ($vlaninfo['vlan_descr'], 20, FALSE) . ')';
	return $ret;
}

function formatVLANAsHyperlink ($vlaninfo)
{
	return mkA (formatVLANAsRichText ($vlaninfo), 'vlan', $vlaninfo['domain_id'] . '-' . $vlaninfo['vlan_id']);
}

function formatVLANAsRichText ($vlaninfo)
{
	$ret = 'VLAN' . $vlaninfo['vlan_id'];
	$ret .= ' @' . niftyString ($vlaninfo['domain_descr']);
	if ($vlaninfo['vlan_descr'] != '')
		$ret .= ' <i>(' . niftyString ($vlaninfo['vlan_descr']) . ')</i>';
	return $ret;
}

// map interface name
function ios12ShortenIfName ($ifname)
{
	if (preg_match ('@^eth-trunk(\d+)$@i', $ifname, $m))
		return "Eth-Trunk${m[1]}";
	$ifname = preg_replace ('@^(?:[Ee]thernet|Eth)(.+)$@', 'e\\1', $ifname);
	$ifname = preg_replace ('@^FastEthernet(.+)$@', 'fa\\1', $ifname);
	$ifname = preg_replace ('@^(?:GigabitEthernet|GE)(.+)$@', 'gi\\1', $ifname);
	$ifname = preg_replace ('@^TenGigabitEthernet(.+)$@', 'te\\1', $ifname);
	$ifname = preg_replace ('@^port-channel(.+)$@i', 'po\\1', $ifname);
	$ifname = preg_replace ('@^(?:XGigabitEthernet|XGE)(.+)$@', 'xg\\1', $ifname);
	$ifname = preg_replace ('@^LongReachEthernet(.+)$@', 'lo\\1', $ifname);
	$ifname = preg_replace ('@^Management(.+)$@', 'ma\\1', $ifname);
	$ifname = preg_replace ('@^Et(\d.*)$@', 'e\\1', $ifname);
	$ifname = preg_replace ('@^TenGigE(.*)$@', 'te\\1', $ifname); // IOS XR4
	$ifname = preg_replace ('@^Mg(?:mtEth)?(.*)$@', 'mg\\1', $ifname); // IOS XR4
	$ifname = preg_replace ('@^BE(\d+)$@', 'bundle-ether\\1', $ifname); // IOS XR4
	$ifname = strtolower ($ifname);
	$ifname = preg_replace ('/^(e|fa|gi|te|po|xg|lo|ma)\s+(\d.*)/', '$1$2', $ifname);
	return $ifname;
}

# Produce a list of integers from a string in the following format:
# A,B,C-D,E-F,G,H,I-J,K ...
function iosParseVLANString ($string)
{
	$ret = array();
	foreach (explode (',', $string) as $item)
	{
		$matches = array();
		$item = trim ($item, ' ');
		if (preg_match ('/^([[:digit:]]+)$/', $item, $matches))
			$ret[] = $matches[1];
		elseif (preg_match ('/^([[:digit:]]+)-([[:digit:]]+)$/', $item, $matches))
			$ret = array_merge ($ret, range ($matches[1], $matches[2]));
		else
			throw new InvalidArgException ('string', $string, 'format mismatch');
	}
	return $ret;
}

// Scan given array and return the key, which addresses the first item
// with requested column set to given value (or NULL if there is none such).
// Note that 0 and NULL mean completely different things and thus
// require strict checking (=== and !===).
function scanArrayForItem ($table, $scan_column, $scan_value)
{
	foreach ($table as $key => $row)
		if ($row[$scan_column] == $scan_value)
			return $key;
	return NULL;
}

// Return TRUE, if every value of A1 is present in A2 and vice versa,
// regardless of each array's sort order and indexing.
function array_values_same ($a1, $a2)
{
	return !count (array_diff ($a1, $a2)) and !count (array_diff ($a2, $a1));
}

# Reindex provided array of arrays by a column value, which is present in
# each sub-array and is assumed to be unique. Most often, make "id" column in
# a list of cells into the key space.
function reindexById ($input, $column_name = 'id')
{
	$ret = array();
	foreach ($input as $item)
	{
		if (! array_key_exists ($column_name, $item))
			throw new InvalidArgException ('input', '(array)', 'ID column missing');
		if (array_key_exists ($item[$column_name], $ret))
			throw new InvalidArgException ('column_name', $column_name, 'duplicate ID value ' . $item[$column_name]);
		$ret[$item[$column_name]] = $item;
	}
	return $ret;
}

# Given an array consisting of subarrays, each typically with the same set
# of keys, produce a result indexed the same way as the input array, but
# having each subarray replaced with one of the subarray values (using the
# provided subindex name, e.g.:
# array (10 => array ('a' => 'x1', 'b' => 'y1'), 20 => array ('a' => 'x2', 'b' => 'y2'))
# would map to (using subindex 'b'): array (10 => 'y1', 20 => 'y2')
function reduceSubarraysToColumn ($input, $column)
{
	$ret = array();
	foreach ($input as $key => $item)
		if (array_key_exists ($column, $item))
			$ret[$key] = $item[$column];
		else
			throw new InvalidArgException ('input', '(array)', "column '${column}' is not set for subarray at index '${key}'");
	return $ret;
}

// Use the VLAN switch template to set VST role for each port of
// the provided list. Return resulting list.
function apply8021QOrder ($vst_id, $portlist)
{
	$vst = spotEntity ('vst', $vst_id);
	amplifyCell ($vst);
	foreach (array_keys ($portlist) as $port_name)
	{
		foreach ($vst['rules'] as $rule)
			if (preg_match ($rule['port_pcre'], $port_name))
			{
				$portlist[$port_name]['vst_role'] = $rule['port_role'];
				$portlist[$port_name]['wrt_vlans'] = buildVLANFilter ($rule['port_role'], $rule['wrt_vlans']);
				continue 2;
			}
		$portlist[$port_name]['vst_role'] = 'none';
	}
	return $portlist;
}

// return a sequence of ranges for given string form and port role
function buildVLANFilter ($role, $string)
{
	// set base
	switch ($role)
	{
	case 'access': // 1-4094
		$min = VLAN_MIN_ID;
		$max = VLAN_MAX_ID;
		break;
	case 'trunk': // 2-4094
	case 'uplink':
	case 'downlink':
	case 'anymode':
		$min = VLAN_MIN_ID + 1;
		$max = VLAN_MAX_ID;
		break;
	default: // none
		return array();
	}
	if ($string == '') // fast track
		return array (array ('from' => $min, 'to' => $max));
	// transform
	$vlanidlist = array();
	foreach (iosParseVLANString ($string) as $vlan_id)
		if ($min <= $vlan_id and $vlan_id <= $max)
			$vlanidlist[] = $vlan_id;
	return listToRanges ($vlanidlist);
}

// pack set of integers into list of integer ranges
// e.g. (1, 2, 3, 5, 6, 7, 9, 11) => ((1, 3), (5, 7), (9, 9), (11, 11))
// The second argument, when it is different from 0, limits amount of
// items in each generated range.
function listToRanges ($vlanidlist, $limit = 0)
{
	sort ($vlanidlist);
	$ret = array();
	$from = $to = NULL;
	foreach ($vlanidlist as $vlan_id)
		if ($from == NULL)
		{
			if ($limit == 1)
				$ret[] = array ('from' => $vlan_id, 'to' => $vlan_id);
			else
				$from = $to = $vlan_id;
		}
		elseif ($to + 1 == $vlan_id)
		{
			$to = $vlan_id;
			if ($to - $from + 1 == $limit)
			{
				// cut accumulated range and start over
				$ret[] = array ('from' => $from, 'to' => $to);
				$from = $to = NULL;
			}
		}
		else
		{
			$ret[] = array ('from' => $from, 'to' => $to);
			$from = $to = $vlan_id;
		}
	if ($from != NULL)
		$ret[] = array ('from' => $from, 'to' => $to);
	return $ret;
}

// return TRUE, if given VLAN ID belongs to one of filter's ranges
function matchVLANFilter ($vlan_id, $vfilter)
{
	foreach ($vfilter as $range)
		if ($range['from'] <= $vlan_id and $vlan_id <= $range['to'])
			return TRUE;
	return FALSE;
}

function generate8021QDeployOps ($vswitch, $device_vlanlist, $before, $changes)
{
	$domain_vlanlist = getDomainVLANs ($vswitch['domain_id']);
	$employed_vlans = getEmployedVlans ($vswitch['object_id'], $domain_vlanlist);

	// only ignore VLANs, which exist and are explicitly shown as "alien"
	$old_managed_vlans = array();
	foreach ($device_vlanlist as $vlan_id)
		if
		(
			!array_key_exists ($vlan_id, $domain_vlanlist) or
			$domain_vlanlist[$vlan_id]['vlan_type'] != 'alien'
		)
			$old_managed_vlans[] = $vlan_id;
	$old_managed_vlans = array_unique ($old_managed_vlans);

	$ports_to_do = array();
	$ports_to_do_queue1 = array();
	$ports_to_do_queue2 = array();
	$after = $before;
	foreach ($changes as $port_name => $port)
	{
		$changeset = array
		(
			'old_mode' => $before[$port_name]['mode'],
			'old_allowed' => $before[$port_name]['allowed'],
			'old_native' => $before[$port_name]['native'],
			'new_mode' => $port['mode'],
			'new_allowed' => $port['allowed'],
			'new_native' => $port['native'],
		);
		// put the ports with employed vlans first, the others - below them
		if (! count (array_intersect ($changeset['old_allowed'], $employed_vlans)))
			$ports_to_do_queue2[$port_name] = $changeset;
		else
			$ports_to_do_queue1[$port_name] = $changeset;
		$after[$port_name] = $port;
	}
	# Two arrays without common keys get merged with "+" operator just fine,
	# with an important difference from array_merge() in that the latter
	# renumbers numeric keys, and "+" does not. This matters, when port name
	# is a number (like in XOS12 system).
	$ports_to_do = $ports_to_do_queue1 + $ports_to_do_queue2;
	// New VLAN table is a union of:
	// 1. all compulsory VLANs
	// 2. all "current" non-alien allowed VLANs of those ports, which are left
	//    intact (regardless if a VLAN exists in VLAN domain, but looking,
	//    if it is present in device's own VLAN table)
	// 3. all "new" allowed VLANs of those ports, which we do "push" now
	// Like for old_managed_vlans, a VLANs is never listed, only if it
	// exists and belongs to "alien" type.
	$new_managed_vlans = array();
	// We need to count down the number of ports still using specific vlan
	// in order to delete it from device as soon as vlan will be removed from the last port
	// This array tracks port count:
	//  * keys are vlan_id's;
	//  * values are the number of changed ports which were using this vlan in old configuration
	$used_vlans = array();
	// 1
	foreach ($domain_vlanlist as $vlan_id => $vlan)
		if ($vlan['vlan_type'] == 'compulsory')
			$new_managed_vlans[] = $vlan_id;
	// 2
	foreach ($before as $port_name => $port)
		if (!array_key_exists ($port_name, $changes))
			foreach ($port['allowed'] as $vlan_id)
			{
				if (in_array ($vlan_id, $new_managed_vlans))
					continue;
				if
				(
					array_key_exists ($vlan_id, $domain_vlanlist) and
					$domain_vlanlist[$vlan_id]['vlan_type'] == 'alien'
				)
					continue;
				if (in_array ($vlan_id, $device_vlanlist))
					$new_managed_vlans[] = $vlan_id;
			}
		else
			foreach ($port['allowed'] as $vlan_id)
			{
				if (!array_key_exists ($vlan_id, $used_vlans))
					$used_vlans[$vlan_id] = 0;
				$used_vlans[$vlan_id]++;
			}
	// 3
	foreach ($changes as $port)
		foreach ($port['allowed'] as $vlan_id)
			if
			(
				isset ($domain_vlanlist[$vlan_id]) and
				$domain_vlanlist[$vlan_id]['vlan_type'] == 'ondemand' and
				!in_array ($vlan_id, $new_managed_vlans)
			)
				$new_managed_vlans[] = $vlan_id;
	$new_managed_vlans = array_unique ($new_managed_vlans);

	$vlans_to_add = array_diff ($new_managed_vlans, $old_managed_vlans);
	$vlans_to_del = array_diff ($old_managed_vlans, $new_managed_vlans);
	$crq = array();

	// destroy unused VLANs on device
	$deleted_vlans = array_diff ($vlans_to_del, array_keys ($used_vlans));
	foreach ($deleted_vlans as $vlan_id)
		$crq[] = array
		(
			'opcode' => 'destroy VLAN',
			'arg1' => $vlan_id,
		);
	$vlans_to_del = array_diff ($vlans_to_del, $deleted_vlans);

	foreach (sortPortList ($ports_to_do) as $port_name => $port)
	{
		// Before removing each old VLAN as such it is necessary to unassign
		// ports from it (to remove VLAN from each ports' list of "allowed"
		// VLANs). This change in turn requires, that a port's "native"
		// VLAN isn't set to the one being removed from its "allowed" list.
		switch ($port['old_mode'] . '->' . $port['new_mode'])
		{
		case 'trunk->trunk':
			// "old" native is set and differs from the "new" native
			if ($port['old_native'] and $port['old_native'] != $port['new_native'])
				$crq[] = array
				(
					'opcode' => 'unset native',
					'arg1' => $port_name,
					'arg2' => $port['old_native'],
				);
			$vlans_to_remove = array_diff ($port['old_allowed'], $port['new_allowed']);
			$queues = array();
			$queues[] = array_intersect ($employed_vlans, $vlans_to_remove); // remove employed vlans first
			$queues[] = array_diff ($vlans_to_remove, $employed_vlans);// remove other vlans afterwards
			foreach ($queues as $queue)
				if (! empty ($queue))
				{
					$crq[] = array
					(
						'opcode' => 'rem allowed',
						'port' => $port_name,
						'vlans' => $queue,
					);
					foreach ($queue as $vlan_id)
						$used_vlans[$vlan_id]--;
				}
			break;
		case 'access->access':
			if ($port['old_native'] and $port['old_native'] != $port['new_native'])
			{
				$crq[] = array
				(
					'opcode' => 'unset access',
					'arg1' => $port_name,
					'arg2' => $port['old_native'],
				);
				$used_vlans[$port['old_native']]--;
			}
			break;
		case 'access->trunk':
			$crq[] = array
			(
				'opcode' => 'unset access',
				'arg1' => $port_name,
				'arg2' => $port['old_native'],
			);
			$used_vlans[$port['old_native']]--;
			break;
		case 'trunk->access':
			if ($port['old_native'])
				$crq[] = array
				(
					'opcode' => 'unset native',
					'arg1' => $port_name,
					'arg2' => $port['old_native'],
				);
			$vlans_to_remove = $port['old_allowed'];
			$queues = array();
			$queues[] = array_intersect ($employed_vlans, $vlans_to_remove); // remove employed vlans first
			$queues[] = array_diff ($vlans_to_remove, $employed_vlans);// remove other vlans afterwards
			foreach ($queues as $queue)
				if (! empty ($queue))
				{
					$crq[] = array
					(
						'opcode' => 'rem allowed',
						'port' => $port_name,
						'vlans' => $queue,
					);
					foreach ($queue as $vlan_id)
						$used_vlans[$vlan_id]--;
				}
			break;
		default:
			throw new InvalidArgException ('ports_to_do', '(hidden)', 'error in structure');
		}

		// destroy unneeded VLANs on device
		$deleted_vlans = array();
		foreach ($vlans_to_del as $vlan_id)
			if ($used_vlans[$vlan_id] == 0)
			{
				$crq[] = array
				(
					'opcode' => 'destroy VLAN',
					'arg1' => $vlan_id,
				);
				$deleted_vlans[] = $vlan_id;
			}
		$vlans_to_del = array_diff ($vlans_to_del, $deleted_vlans);

		// create new VLANs on device
		$added_vlans = array_intersect ($vlans_to_add, $port['new_allowed']);
		foreach ($added_vlans as $vlan_id)
			$crq[] = array
			(
				'opcode' => 'create VLAN',
				'arg1' => $vlan_id,
			);
		$vlans_to_add = array_diff ($vlans_to_add, $added_vlans);

		// change port mode if needed
		if ($port['old_mode'] != $port['new_mode'])
			$crq[] = array
			(
				'opcode' => 'set mode',
				'arg1' => $port_name,
				'arg2' => $port['new_mode'],
			);

		// Now, when all new VLANs are created (queued), it is safe to assign (queue)
		// ports to the new VLANs.
		switch ($port['old_mode'] . '->' . $port['new_mode'])
		{
		case 'trunk->trunk':
			// For each allowed VLAN, which is present on the "new" list and missing from
			// the "old" one, queue a command to assign current port to that VLAN.
			if (count ($tmp = array_diff ($port['new_allowed'], $port['old_allowed'])))
				$crq[] = array
				(
					'opcode' => 'add allowed',
					'port' => $port_name,
					'vlans' => $tmp,
				);
			// One of the "allowed" VLANs for this port may probably be "native".
			// "new native" is set and differs from "old native"
			if ($port['new_native'] and $port['new_native'] != $port['old_native'])
				$crq[] = array
				(
					'opcode' => 'set native',
					'arg1' => $port_name,
					'arg2' => $port['new_native'],
				);
			break;
		case 'access->access':
			if ($port['new_native'] and $port['new_native'] != $port['old_native'])
				$crq[] = array
				(
					'opcode' => 'set access',
					'arg1' => $port_name,
					'arg2' => $port['new_native'],
				);
			break;
		case 'access->trunk':
			if (count ($port['new_allowed']))
				$crq[] = array
				(
					'opcode' => 'add allowed',
					'port' => $port_name,
					'vlans' => $port['new_allowed'],
				);
			if ($port['new_native'])
				$crq[] = array
				(
					'opcode' => 'set native',
					'arg1' => $port_name,
					'arg2' => $port['new_native'],
				);
			break;
		case 'trunk->access':
			$crq[] = array
			(
				'opcode' => 'set access',
				'arg1' => $port_name,
				'arg2' => $port['new_native'],
			);
			break;
		default:
			throw new InvalidArgException ('ports_to_do', '(hidden)', 'error in structure');
		}
	}

	// add the rest of VLANs to device (compulsory VLANs)
	foreach ($vlans_to_add as $vlan_id)
		$crq[] = array
		(
			'opcode' => 'create VLAN',
			'arg1' => $vlan_id,
		);

	return $crq;
}

function exportSwitch8021QConfig
(
	$vswitch,
	$device_vlanlist,
	$before,
	$changes,
	$vlan_names
)
{
	$crq = generate8021QDeployOps ($vswitch, $device_vlanlist, $before, $changes);
	if (count ($crq))
	{
		array_unshift ($crq, array ('opcode' => 'begin configuration'));
		$crq[] = array ('opcode' => 'end configuration');
		if (considerConfiguredConstraint (spotEntity ('object', $vswitch['object_id']), '8021Q_WRI_AFTER_CONFT_LISTSRC'))
			$crq[] = array ('opcode' => 'save configuration');
		setDevice8021QConfig ($vswitch['object_id'], $crq, $vlan_names);
	}
	return count ($crq);
}

// filter list of changed ports to cancel changes forbidden by VST and domain
function filter8021QChangeRequests
(
	$domain_vlanlist,
	$before,  // current saved configuration of all ports
	$changes  // changed ports with VST markup
)
{
	$domain_immune_vlans = array();
	foreach ($domain_vlanlist as $vlan_id => $vlan)
		if ($vlan['vlan_type'] == 'alien')
			$domain_immune_vlans[] = $vlan_id;
	$ret = array();
	foreach ($changes as $port_name => $port)
	{
		// VST violation ?
		if (!goodModeForVSTRole ($port['mode'], $port['vst_role']))
			continue; // ignore change request
		// find and cancel any changes regarding immune VLANs
		switch ($port['mode'])
		{
		case 'access':
			foreach ($domain_immune_vlans as $immune)
				// Reverting an attempt to set an access port from
				// "normal" VLAN to immune one (or vice versa) requires
				// special handling, becase the calling function has
				// discarded the old contents of 'allowed' for current port.
				if
				(
					$before[$port_name]['native'] == $immune or
					$port['native'] == $immune
				)
				{
					$port['native'] = $before[$port_name]['native'];
					$port['allowed'] = array ($port['native']);
					// Such reversal happens either once or never for an
					// access port.
					break;
				}
			break;
		case 'trunk':
			foreach ($domain_immune_vlans as $immune)
				if (in_array ($immune, $before[$port_name]['allowed'])) // was allowed before
				{
					if (!in_array ($immune, $port['allowed']))
						$port['allowed'][] = $immune; // restore
					if ($before[$port_name]['native'] == $immune) // and was native
						$port['native'] = $immune; // also restore
				}
				else // wasn't
				{
					if (in_array ($immune, $port['allowed']))
						unset ($port['allowed'][array_search ($immune, $port['allowed'])]); // cancel
					if ($port['native'] == $immune)
						$port['native'] = $before[$port_name]['native'];
				}
			break;
		default:
			throw new InvalidArgException ('mode', $port['mode']);
		}
		// save work
		$ret[$port_name] = $port;
	}
	return $ret;
}

function getEmployedVlans ($object_id, $domain_vlanlist)
{
	$employed = array(); // keyed by vlan_id. Value is dummy int
	// find persistent VLANs in domain
	foreach ($domain_vlanlist as $vlan_id => $vlan)
		if ($vlan['vlan_type'] == 'compulsory')
			$employed[$vlan_id] = 1;

	// find VLANs for object's L3 allocations
	$cell = spotEntity ('object', $object_id);
	amplifyCell ($cell);
	foreach (array ('ipv4', 'ipv6') as $family)
	{
		$seen_nets = array();
		foreach ($cell[$family] as $ip_bin => $allocation)
			if ($net_id = getIPAddressNetworkId ($ip_bin))
			{
				if (! isset($seen_nets[$net_id]))
					$seen_nets[$net_id]	= 1;
				else
					continue;
				$net = spotEntity ("${family}net", $net_id);
				foreach ($net['8021q'] as $vlan)
					if (! isset ($employed[$vlan['vlan_id']]))
						$employed[$vlan['vlan_id']] = 1;
			}
	}
	return array_keys ($employed);
}

// take port list with order applied and return uplink ports in the same format
function produceUplinkPorts ($domain_vlanlist, $portlist, $object_id)
{
	$ret = array();

	$employed = getEmployedVlans ($object_id, $domain_vlanlist);
	foreach ($portlist as $port_name => $port)
		if ($port['vst_role'] != 'uplink')
			foreach ($port['allowed'] as $vlan_id)
				if (array_key_exists ($vlan_id, $domain_vlanlist) && !in_array ($vlan_id, $employed))
					$employed[] = $vlan_id;

	foreach ($portlist as $port_name => $port)
		if ($port['vst_role'] == 'uplink')
		{
			$employed_here = array();
			foreach ($employed as $vlan_id)
				if (matchVLANFilter ($vlan_id, $port['wrt_vlans']))
					$employed_here[] = $vlan_id;
			$ret[$port_name] = array
			(
				'vst_role' => 'uplink',
				'mode' => 'trunk',
				'allowed' => $employed_here,
				'native' => 0,
			);
		}
	return $ret;
}

function same8021QConfigs ($a, $b)
{
	return	$a['mode'] == $b['mode'] &&
		array_values_same ($a['allowed'], $b['allowed']) &&
		$a['native'] == $b['native'];
}

// Return TRUE, if the port can be edited by the user.
function editable8021QPort ($port)
{
	return in_array ($port['vst_role'], array ('trunk', 'access', 'anymode'));
}

// Decide, whether the given 802.1Q port mode is permitted by
// VST port role.
function goodModeForVSTRole ($mode, $role)
{
	switch ($mode)
	{
	case 'access':
		return in_array ($role, array ('access', 'anymode'));
	case 'trunk':
		return in_array ($role, array ('trunk', 'uplink', 'downlink', 'anymode'));
	default:
		throw new InvalidArgException ('mode', $mode);
	}
}

/*

Relation between desired (D), cached (C) and running (R)
copies of switch ports (P) list.

  D         C           R
+---+     +---+       +---+
| P |-----| P |-?  +--| P |
+---+     +---+   /   +---+
| P |-----| P |--+  ?-| P |
+---+     +---+       +---+
| P |-----| P |-------| P |
+---+     +---+       +---+
| P |-----| P |--+  ?-| P |
+---+     +---+   \   +---+
| P |-----| P |--+ +--| P |
+---+     +---+   \   +---+
                   +--| P |
                      +---+
                    ?-| P |
                      +---+

A modified local version of a port in "conflict" state ignores remote
changes until remote change maintains its difference. Once both edits
match, the local copy "locks" on the remote and starts tracking it.

v
a           "o" -- remOte version
l           "l" -- Local version
u           "b" -- Both versions
e

^
|         o           b
|           o
| l l l l l l b     b
|   o   o       b
| o               b
|
|     o
|
|
0----------------------------------------------> time

*/
function get8021QSyncOptions
(
	$vswitch,
	$D, // desired config
	$C, // cached config
	$R  // running-config
)
{
	$default_port = array
	(
		'mode' => 'access',
		'allowed' => array (VLAN_DFL_ID),
		'native' => VLAN_DFL_ID,
	);
	$ret = array();
	$allports = array();
	foreach (array_unique (array_merge (array_keys ($C), array_keys ($R))) as $pn)
		$allports[$pn] = array();
	foreach (apply8021QOrder ($vswitch['template_id'], $allports) as $pn => $port)
	{
		// catch anomalies early
		if ($port['vst_role'] == 'none')
		{
			if ((!array_key_exists ($pn, $R) or $R[$pn]['mode'] == 'none') and !array_key_exists ($pn, $C))
				$ret[$pn] = array ('status' => 'none');
			else
				$ret[$pn] = array
				(
					'status' => 'martian_conflict',
					'left' => array_key_exists ($pn, $C) ? $C[$pn] : array ('mode' => 'none'),
					'right' => array_key_exists ($pn, $R) ? $R[$pn] : array ('mode' => 'none'),
				);
			continue;
		}
		elseif ((!array_key_exists ($pn, $R) or $R[$pn]['mode'] == 'none') and array_key_exists ($pn, $C))
		{
			$ret[$pn] = array
			(
				'status' => 'martian_conflict',
				'left' => array_key_exists ($pn, $C) ? $C[$pn] : array ('mode' => 'none'),
				'right' => array_key_exists ($pn, $R) ? $R[$pn] : array ('mode' => 'none'),
			);
			continue;
		}
		// (DC_): port missing from device
		if (!array_key_exists ($pn, $R))
		{
			$ret[$pn] = array ('left' => $D[$pn]);
			if (same8021QConfigs ($D[$pn], $default_port))
				$ret[$pn]['status'] = 'ok_to_delete';
			else
			{
				$ret[$pn]['status'] = 'delete_conflict';
				$ret[$pn]['lastseen'] = $C[$pn];
			}
			continue;
		}
		// (__R): port missing from DB
		if (!array_key_exists ($pn, $C))
		{
			// Allow importing any configuration, which passes basic
			// validation. If port mode doesn't match its VST role,
			// this will be handled later WRT each port.
			$ret[$pn] = array
			(
				'status' => acceptable8021QConfig ($R[$pn]) ? 'ok_to_add' : 'add_conflict',
				'right' => $R[$pn],
			);
			continue;
		}
		$D_eq_C = same8021QConfigs ($D[$pn], $C[$pn]);
		$C_eq_R = same8021QConfigs ($C[$pn], $R[$pn]);
		// (DCR), D = C = R: data in sync
		if ($D_eq_C and $C_eq_R) // implies D == R
		{
			$ret[$pn] = array
			(
				'status' => 'in_sync',
				'both' => $R[$pn],
			);
			continue;
		}
		// (DCR), D = C: no local edit in the way
		if ($D_eq_C)
			$ret[$pn] = array
			(
				'status' => 'ok_to_pull',
				'left' => $D[$pn],
				'right' => $R[$pn],
			);
		// (DCR), C = R: no remote edit in the way
		elseif ($C_eq_R)
			$ret[$pn] = array
			(
				'status' => 'ok_to_push',
				'left' => $D[$pn],
				'right' => $R[$pn],
			);
		// (DCR), D = R: end of version conflict, restore tracking
		elseif (same8021QConfigs ($D[$pn], $R[$pn]))
			$ret[$pn] = array
			(
				'status' => 'ok_to_merge',
				'both' => $R[$pn],
			);
		else // D != C, C != R, D != R: version conflict
			$ret[$pn] = array
			(
				'status' => editable8021QPort ($port) ?
					// In case the port is normally updated by user, let him
					// resolve the conflict. If the system manages this port,
					// arrange the data to let remote version go down.
					'merge_conflict' : 'ok_to_push_with_merge',
				'left' => $D[$pn],
				'right' => $R[$pn],
			);
	}
	return $ret;
}

// return number of records updated successfully of FALSE, if a conflict was in the way
function exec8021QDeploy ($object_id, $do_push)
{
	global $dbxlink;
	$nsaved = $npushed = $nsaved_uplinks = 0;
	$dbxlink->beginTransaction();
	if (NULL === $vswitch = getVLANSwitchInfo ($object_id, 'FOR UPDATE'))
		throw new InvalidArgException ('object_id', $object_id, 'VLAN domain is not set for this object');
	$D = getStored8021QConfig ($vswitch['object_id'], 'desired');
	$C = getStored8021QConfig ($vswitch['object_id'], 'cached');
	try
	{
		$R = getRunning8021QConfig ($vswitch['object_id']);
	}
	catch (RTGatewayError $e)
	{
		usePreparedExecuteBlade
		(
			'UPDATE VLANSwitch SET last_errno=?, last_error_ts=NOW() WHERE object_id=?',
			array (E_8021Q_PULL_REMOTE_ERROR, $vswitch['object_id'])
		);
		$dbxlink->commit();
		throw $e;
	}
	$conflict = FALSE;
	$ok_to_push = array();
	foreach (get8021QSyncOptions ($vswitch, $D, $C, $R['portdata']) as $pn => $port)
	{
		// always update cache with new data from switch
		switch ($port['status'])
		{
		case 'ok_to_merge':
			// FIXME: this can be logged
			upd8021QPort ('cached', $vswitch['object_id'], $pn, $port['both']);
			break;
		case 'ok_to_delete':
			del8021QPort ($vswitch['object_id'], $pn);
			$nsaved++;
			break;
		case 'ok_to_add':
			add8021QPort ($vswitch['object_id'], $pn, $port['right']);
			$nsaved++;
			break;
		case 'delete_conflict':
		case 'merge_conflict':
		case 'add_conflict':
		case 'martian_conflict':
			$conflict = TRUE;
			break;
		case 'ok_to_pull':
			// FIXME: this can be logged
			upd8021QPort ('desired', $vswitch['object_id'], $pn, $port['right']);
			upd8021QPort ('cached', $vswitch['object_id'], $pn, $port['right']);
			$nsaved++;
			break;
		case 'ok_to_push_with_merge':
			upd8021QPort ('cached', $vswitch['object_id'], $pn, $port['right']);
			// fall through
		case 'ok_to_push':
			$ok_to_push[$pn] = $port['left'];
			break;
		}
	}
	// redo uplinks unconditionally
	$domain_vlanlist = getDomainVLANs ($vswitch['domain_id']);
	$Dnew = apply8021QOrder ($vswitch['template_id'], getStored8021QConfig ($vswitch['object_id'], 'desired'));
	// Take new "desired" configuration and derive uplink port configuration
	// from it. Then cancel changes to immune VLANs and save resulting
	// changes (if any left).
	$new_uplinks = filter8021QChangeRequests ($domain_vlanlist, $Dnew, produceUplinkPorts ($domain_vlanlist, $Dnew, $vswitch['object_id']));
	$nsaved_uplinks += replace8021QPorts ('desired', $vswitch['object_id'], $Dnew, $new_uplinks);
	if ($nsaved + $nsaved_uplinks)
		// saved configuration has changed (either "user" ports have changed,
		// or uplinks, or both), so bump revision number up)
		touchVLANSwitch ($vswitch['object_id']);
	if ($conflict)
		usePreparedExecuteBlade
		(
			'UPDATE VLANSwitch SET out_of_sync="yes", last_errno=?, last_error_ts=NOW() WHERE object_id=?',
			array (E_8021Q_VERSION_CONFLICT, $vswitch['object_id'])
		);
	else
	{
		usePreparedExecuteBlade
		(
			'UPDATE VLANSwitch SET last_errno=?, last_error_ts=NOW() WHERE object_id=?',
			array (E_8021Q_NOERROR, $vswitch['object_id'])
		);
		// Modified uplinks are very likely to differ from those in R-copy,
		// so don't mark device as clean, if this happened. This can cost
		// us an additional, empty round of sync, but at least out_of_sync
		// won't be mistakenly set to 'no'.
		// FIXME: A cleaner way of coupling pull and push operations would
		// be to split this function into two.
		if (!count ($ok_to_push) and !$nsaved_uplinks)
			usePreparedExecuteBlade
			(
				'UPDATE VLANSwitch SET out_of_sync="no" WHERE object_id=?',
				array ($vswitch['object_id'])
			);
		elseif ($do_push)
		{
			usePreparedExecuteBlade
			(
				'UPDATE VLANSwitch SET last_push_started=NOW() WHERE object_id=?',
				array ($vswitch['object_id'])
			);
			try
			{
				$vlan_names = array();
				foreach ($R['vlanlist'] as $vid)
					$vlan_names[$vid] = @$R['vlannames']['vid'];
				$npushed += exportSwitch8021QConfig ($vswitch, $R['vlanlist'], $R['portdata'], $ok_to_push, $vlan_names);
				// update cache for ports deployed
				replace8021QPorts ('cached', $vswitch['object_id'], $R['portdata'], $ok_to_push);
				usePreparedExecuteBlade
				(
					'UPDATE VLANSwitch SET last_push_finished=NOW(), out_of_sync="no", last_errno=? WHERE object_id=?',
					array (E_8021Q_NOERROR, $vswitch['object_id'])
				);
			}
			catch (RTGatewayError $r)
			{
				usePreparedExecuteBlade
				(
					'UPDATE VLANSwitch SET out_of_sync="yes", last_error_ts=NOW(), last_errno=? WHERE object_id=?',
					array (E_8021Q_PUSH_REMOTE_ERROR, $vswitch['object_id'])
				);
				callHook ('pushErrorHandler', $object_id, $r);
			}
		}
	}
	$dbxlink->commit();
	// start downlink work only after unlocking current object to make deadlocks less likely to happen
	// TODO: only process changed uplink ports
	if ($nsaved_uplinks)
		initiateUplinksReverb ($vswitch['object_id'], $new_uplinks);
	return $conflict ? FALSE : $nsaved + $npushed + $nsaved_uplinks;
}

function strerror8021Q ($errno)
{
	switch ($errno)
	{
	case E_8021Q_VERSION_CONFLICT:
		return 'pull failed due to version conflict';
	case E_8021Q_PULL_REMOTE_ERROR:
		return 'pull failed due to remote error';
	case E_8021Q_PUSH_REMOTE_ERROR:
		return 'push failed due to remote error';
	case E_8021Q_SYNC_DISABLED:
		return 'sync disabled by operator';
	default:
		return "unknown error code ${errno}";
	}
}

function saveDownlinksReverb ($object_id, $requested_changes)
{
	$nsaved = 0;
	global $dbxlink;
	$dbxlink->beginTransaction();
	if (NULL === $vswitch = getVLANSwitchInfo ($object_id, 'FOR UPDATE')) // not configured, bail out
	{
		$dbxlink->rollBack();
		return;
	}
	$domain_vlanlist = getDomainVLANs ($vswitch['domain_id']);
	// aplly VST to the smallest set necessary
	$requested_changes = apply8021QOrder ($vswitch['template_id'], $requested_changes);
	$before = getStored8021QConfig ($object_id, 'desired');
	$changes_to_save = array();
	// first filter by wrt_vlans constraint
	foreach ($requested_changes as $pn => $requested)
		if (array_key_exists ($pn, $before) and $requested['vst_role'] == 'downlink')
		{
			$negotiated = array
			(
				'vst_role' => 'downlink',
				'mode' => 'trunk',
				'allowed' => array(),
				'native' => 0,
			);
			// wrt_vlans filter
			foreach ($requested['allowed'] as $vlan_id)
				if (matchVLANFilter ($vlan_id, $requested['wrt_vlans']))
					$negotiated['allowed'][] = $vlan_id;
			$changes_to_save[$pn] = $negotiated;
		}
	// immune VLANs filter
	foreach (filter8021QChangeRequests ($domain_vlanlist, $before, $changes_to_save) as $pn => $finalconfig)
		if (!same8021QConfigs ($finalconfig, $before[$pn]))
		{
			upd8021QPort ('desired', $vswitch['object_id'], $pn, $finalconfig);
			$nsaved++;
		}
	if ($nsaved)
		touchVLANSwitch ($vswitch['object_id']);
	$dbxlink->commit();
	return $nsaved;
}

// Use records from Port and Link tables to run a series of tasks on remote
// objects. These device-specific tasks will adjust downlink ports according to
// the current configuration of given uplink ports.
function initiateUplinksReverb ($object_id, $uplink_ports)
{
	$object = spotEntity ('object', $object_id);
	amplifyCell ($object);
	// Filter and regroup all requests (regardless of how many will succeed)
	// to end up with no more, than one execution per remote object.
	$upstream_config = array();
	foreach ($object['ports'] as $portinfo)
		if
		(
			array_key_exists ($portinfo['name'], $uplink_ports) and
			$portinfo['remote_object_id'] != '' and
			$portinfo['remote_name'] != ''
		)
			$upstream_config[$portinfo['remote_object_id']][$portinfo['remote_name']] = $uplink_ports[$portinfo['name']];
	// Note that when current object has several Port records inder same name
	// (but with unique IIF-OIF pair), these ports can be Link'ed to different
	// remote objects (using different media types, perhaps). Such a case can
	// be considered as normal, and each remote object will show up on the
	// task list (with its actual remote port name, of course).
	$done = 0;
	foreach ($upstream_config as $remote_object_id => $remote_ports)
		if ($changed = saveDownlinksReverb ($remote_object_id, $remote_ports))
		{
			$done += $changed;
			$done += apply8021qChangeRequest ($remote_object_id, array(), FALSE);
		}
	return $done;
}

// checks if the desired config of all uplink/downlink ports of that switch, and
// his neighbors, equals to the recalculated config. If not,
// sets the recalculated configs as desired and puts switches into out-of-sync state.
// Returns an array with object_id as key and portname subkey
function recalc8021QPorts ($switch_id)
{
	function find_connected_portinfo ($ports, $name)
	{
		foreach ($ports as $portinfo)
			if ($portinfo['name'] == $name and $portinfo['remote_object_id'] != '' and $portinfo['remote_name'] != '')
				return $portinfo;
	}

	$ret = array
	(
		'switches' => 0,
		'ports' => 0,
	);
	global $dbxlink;

	$object = spotEntity ('object', $switch_id);
	amplifyCell ($object);
	$vlan_config = getStored8021QConfig ($switch_id, 'desired');
	$vswitch = getVLANSwitchInfo ($switch_id);
	if (! $vswitch)
		return $ret;
	$domain_vlanlist = getDomainVLANs ($vswitch['domain_id']);
	$order = apply8021QOrder ($vswitch['template_id'], $vlan_config);
	$before = $order;

	$dbxlink->beginTransaction();
	// calculate remote uplinks and copy them to local downlinks
	foreach ($order as $pn => &$local_port_order)
	{
		if ($local_port_order['vst_role'] != 'downlink')
			continue;

		// if there is a link with remote side type 'uplink', use its vlan mask
		if ($portinfo = find_connected_portinfo ($object['ports'], $pn))
		{
			$remote_pn = $portinfo['remote_name'];
			$remote_vlan_config = getStored8021QConfig ($portinfo['remote_object_id'], 'desired');
			$remote_vswitch = getVLANSwitchInfo ($portinfo['remote_object_id']);
			if (! $remote_vswitch)
				continue;
			$remote_domain_vlanlist = getDomainVLANs ($remote_vswitch['domain_id']);
			$remote_order = apply8021QOrder ($remote_vswitch['template_id'], $remote_vlan_config);
			$remote_before = $remote_order;
			if ($remote_order[$remote_pn]['vst_role'] == 'uplink')
			{
				$remote_uplinks = filter8021QChangeRequests ($remote_domain_vlanlist, $remote_before, produceUplinkPorts ($remote_domain_vlanlist, $remote_order, $remote_vswitch['object_id']));
				$remote_port_order = $remote_uplinks[$remote_pn];
				$new_order = produceDownlinkPort ($domain_vlanlist, $pn, array ($pn => $local_port_order), $remote_port_order);
				$local_port_order = $new_order[$pn]; // this updates $order

				// queue changes in D-config of remote switch
				if ($changed = replace8021QPorts ('desired', $portinfo['remote_object_id'], $remote_before, array ($remote_pn => $remote_port_order)))
				{
					touchVLANSwitch ($portinfo['remote_object_id']);
					$ret['switches'] ++;
					$ret['ports'] += $changed;
				}
			}
		}
	}

	// calculate local uplinks, store changes in $order
	foreach (filter8021QChangeRequests ($domain_vlanlist, $before, produceUplinkPorts ($domain_vlanlist, $order, $vswitch['object_id'])) as $pn => $portorder)
		$order[$pn] = $portorder;
	// queue changes in D-config of local switch
	if ($changed = replace8021QPorts ('desired', $switch_id, $before, $order))
	{
		touchVLANSwitch ($portinfo['remote_object_id']);
		$ret['switches'] ++;
		$ret['ports'] += $changed;
	}

	// calculate the remote side of local uplinks
	foreach ($order as $pn => &$local_port_order)
	{
		if ($local_port_order['vst_role'] != 'uplink')
			continue;

		// if there is a link with remote side type 'downlink', replace its vlan mask
		if ($portinfo = find_connected_portinfo ($object['ports'], $pn))
		{
			$remote_pn = $portinfo['remote_name'];
			$remote_vlan_config = getStored8021QConfig ($portinfo['remote_object_id'], 'desired');
			$remote_vswitch = getVLANSwitchInfo ($portinfo['remote_object_id']);
			if (! $remote_vswitch)
				continue;
			$remote_domain_vlanlist = getDomainVLANs ($remote_vswitch['domain_id']);
			$remote_order = apply8021QOrder ($remote_vswitch['template_id'], $remote_vlan_config);
			$remote_before = $remote_order;
			if ($remote_order[$remote_pn]['vst_role'] == 'downlink')
			{
				$new_order = produceDownlinkPort ($remote_domain_vlanlist, $remote_pn, $remote_order, $local_port_order);
				// queue changes in D-config of remote switch
				if ($changed = replace8021QPorts ('desired', $portinfo['remote_object_id'], $remote_before, $new_order))
				{
					touchVLANSwitch ($portinfo['remote_object_id']);
					$ret['switches'] ++;
					$ret['ports'] += $changed;
				}
			}
		}
	}
	$dbxlink->commit();
	return $ret;
}

// This function takes 802.1q order and the order of corresponding remote uplink port.
// It returns assotiative array with single row. Key = $portname, value - produced port
// order based on $order, and having vlan list replaced based on $uplink_order, but filtered.
function produceDownlinkPort ($domain_vlanlist, $portname, $order, $uplink_order)
{
	$new_order = array ($portname => $order[$portname]);
	$new_order[$portname]['mode'] = 'trunk';
	$new_order[$portname]['allowed'] = array();
	$new_order[$portname]['native'] = 0;
	foreach ($uplink_order['allowed'] as $vlan_id)
	{
		if (matchVLANFilter ($vlan_id, $new_order[$portname]['wrt_vlans']))
		$new_order[$portname]['allowed'][] = $vlan_id;
	}
	return filter8021QChangeRequests ($domain_vlanlist, $order, $new_order);
}

function detectVLANSwitchQueue ($vswitch)
{
	if ($vswitch['out_of_sync'] == 'no')
		return 'done';
	switch ($vswitch['last_errno'])
	{
	case E_8021Q_NOERROR:
		$last_change_age = time() - $vswitch['last_change'];
		if ($last_change_age > getConfigVar ('8021Q_DEPLOY_MAXAGE'))
			return 'sync_ready';
		elseif ($last_change_age < getConfigVar ('8021Q_DEPLOY_MINAGE'))
			return 'sync_aging';
		else
			return 'sync_ready';
	case E_8021Q_VERSION_CONFLICT:
	case E_8021Q_PULL_REMOTE_ERROR:
	case E_8021Q_PUSH_REMOTE_ERROR:
		$last_error_age = time() - $vswitch['last_error_ts'];
		if ($last_error_age < getConfigVar ('8021Q_DEPLOY_RETRY'))
			return 'resync_aging';
		else
			return 'resync_ready';
	case E_8021Q_SYNC_DISABLED:
		return 'sync_ready';
	}
	return '';
}

function get8021QDeployQueues()
{
	global $dqtitle;
	$ret = array();
	foreach (array_keys ($dqtitle) as $qcode)
		if ($qcode != 'disabled')
			$ret[$qcode] = array
			(
				'enabled' => array(),
				'disabled' => array(),
			);
	foreach (getVLANSwitches() as $object_id)
	{
		$vswitch = getVLANSwitchInfo ($object_id);
		if ('' != $qcode = detectVLANSwitchQueue ($vswitch))
		{
			$cell = spotEntity ('object', $vswitch['object_id']);
			$enabled_key = considerConfiguredConstraint ($cell, 'SYNC_802Q_LISTSRC') ? 'enabled' : 'disabled';
			$ret[$qcode][$enabled_key][] = $vswitch;
		}
	}
	return $ret;
}

function acceptable8021QConfig ($port)
{
	switch ($port['mode'])
	{
	case 'trunk':
		return TRUE;
	case 'access':
		if
		(
			count ($port['allowed']) == 1 and
			in_array ($port['native'], $port['allowed'])
		)
			return TRUE;
		// fall through
	default:
		return FALSE;
	}
}

function authorize8021QChangeRequests ($before, $changes)
{
	global $script_mode;
	if (isset ($script_mode) and $script_mode)
		return $changes;
	$ret = array();
	foreach ($changes as $pn => $change)
	{
		foreach (array_diff ($before[$pn]['allowed'], $change['allowed']) as $removed_id)
			if (!permitted (NULL, NULL, NULL, array (array ('tag' => '$fromvlan_' . $removed_id), array ('tag' => '$vlan_' . $removed_id))))
				continue 2; // next port
		foreach (array_diff ($change['allowed'], $before[$pn]['allowed']) as $added_id)
			if (!permitted (NULL, NULL, NULL, array (array ('tag' => '$tovlan_' . $added_id), array ('tag' => '$vlan_' . $added_id))))
				continue 2; // next port
		$ret[$pn] = $change;
	}
	return $ret;
}

function formatPortIIFOIF ($port)
{
	$ret = '';
	if ($port['iif_id'] != 1)
		$ret .= $port['iif_name'] . '/';
	$ret .= $port['oif_name'];
	return $ret;
}

// returns '<a...</a>' html string containing a link to specified port or object.
// link title is "hostname portname" if both parts are defined
function formatPortLink($host_id, $hostname, $port_id, $portname, $a_class = '')
{
	$href = 'index.php?page=object&object_id=' . urlencode($host_id);
	$additional = '';
	if (isset ($port_id))
	{
		$href .= '&hl_port_id=' . urlencode($port_id);
		$additional = "name=\"port-$port_id\"";
	}
	if (! empty($a_class))
		$additional .= (empty($additional) ? '' : ' '). "class='$a_class'";

	$text_items = array();
	if (isset ($hostname))
		$text_items[] = $hostname;
	if (isset ($portname))
		$text_items[] = $portname;

	return "<a $additional href=\"$href\">" . implode(' ', $text_items) . '</a>';
}

// function returns a HTML-formatted link to the specified port
function formatPort ($port_info, $a_class = '')
{
	return formatPortLink
	(
		$port_info['object_id'],
		$port_info['object_name'],
		$port_info['id'],
		$port_info['name'],
		$a_class
	);
}

// function returns a HTML-formatted link to remote port, connected to the specified port
function formatLinkedPort ($port_info, $a_class = '')
{
	return formatPortLink
	(
		$port_info['remote_object_id'],
		$port_info['remote_object_name'],
		$port_info['remote_id'],
		$port_info['remote_name'],
		$a_class
	);
}

function compareDecomposedPortNames ($porta, $portb)
{
	$ret = 0;
	if ($porta['numidx'] != $portb['numidx'])
		$ret = ($porta['numidx'] - $portb['numidx'] > 0 ? 1 : -1);
	else
	{
		global $portsort_intersections;
		$prefix_diff = strcmp ($porta['prefix'], $portb['prefix']);
		if ($prefix_diff != 0)
			$prefix_diff = ($prefix_diff > 0 ? 1 : -1);
		$index_diff = 0;
		$a_parent = $b_parent = ''; // concatenation of 0..(n-1) numeric indices
		$separator = '';
		for ($i = 0; $i < $porta['numidx']; $i++)
		{
			if ($i < $porta['numidx'] - 1)
			{
				$a_parent .= $separator . $porta['index'][$i];
				$b_parent .= $separator . $portb['index'][$i];
				$separator = '-';
			}
			if ($porta['index'][$i] != $portb['index'][$i])
			{
				$index_diff = ($porta['index'][$i] - $portb['index'][$i] > 0 ? 1 : -1);
				break;
			}
		}
		// compare by portname fields
		if ($prefix_diff != 0 and $porta['numidx'] <= 1) // if index count is lte 1, sort by prefix
			$ret = $prefix_diff;
		// if index count > 1 and ports have different prefixes in intersecting index sections, sort by prefix
		elseif ($prefix_diff != 0 and $a_parent != '' and $a_parent == $b_parent and in_array ($a_parent, $portsort_intersections))
			$ret = $prefix_diff;
		// if indices are not equal, sort by index
		elseif ($index_diff != 0)
			$ret = $index_diff;
		// if all of name fields are equal, compare by some additional port fields
		elseif ($porta['iif_id'] != $portb['iif_id'])
			$ret = ($porta['iif_id'] - $portb['iif_id'] > 0 ? 1 : -1);
		elseif (0 != $result = strcmp ($porta['label'], $portb['label']))
			$ret = ($result > 0 ? 1 : -1);
		elseif (0 != $result = strcmp ($porta['l2address'], $portb['l2address']))
			$ret = ($result > 0 ? 1 : -1);
		elseif ($porta['id'] != $portb['id'])
			$ret = ($porta['id'] - $portb['id'] > 0 ? 1 : -1);
	}
	return $ret;
}

// Sort provided port list in a way based on natural. For example,
// switches can have ports:
// * fa0/1~48, gi0/1~4 (in this case 'gi' should come after 'fa'
// * fa1, gi0/1~48, te1/49~50 (type matters, then index)
// * gi5/1~3, te5/4~5 (here index matters more, than type)
// This implementation makes port type (prefix) matter for all
// interfaces, which have less, than 2 indices, but for other ports
// their indices matter more, than type (unless there is a clash
// of indices).
// When $name_in_value is TRUE, port name determines as $plist[$key]['name']
// Otherwise portname is the key of $plist
function sortPortList ($plist, $name_in_value = FALSE)
{
	$ret = array();
	$to_sort = array();
	$seen = array();
	global $portsort_intersections;
	$portsort_intersections = array();
	$prefix_re = '/^([^0-9]*)[0-9].*$/';
	foreach ($plist as $pkey => $pvalue)
	{
		$pn = $name_in_value ? $pvalue['name'] : $pkey;
		$numbers = preg_split ('/[^0-9]+/', $pn, -1, PREG_SPLIT_NO_EMPTY);
		$to_sort[] = array
		(
			'key' => $pkey,
			'prefix' => preg_replace ($prefix_re, '\\1', $pn),
			'numidx' => count ($numbers),
			'index' => $numbers,
			'iif_id' => isset($plist[$pkey]['iif_id']) ? $plist[$pkey]['iif_id'] : 0,
			'label' => isset($plist[$pkey]['label']) ? $plist[$pkey]['label'] : '',
			'l2address' => isset($plist[$pkey]['l2address']) ? $plist[$pkey]['l2address'] : '',
			'id' => isset($plist[$pkey]['id']) ? $plist[$pkey]['id'] : 0,
			'name' => $pn,
		);
		$parent = implode ('-', array_slice ($numbers, 0, count ($numbers) - 1));
		if (! isset ($seen[$parent]))
			$seen[$parent] = 1;
		else
			$portsort_intersections[$parent] = $parent;
	}
	usort ($to_sort, 'compareDecomposedPortNames');
	foreach ($to_sort as $pvalue)
		$ret[$pvalue['key']] = $plist[$pvalue['key']];
	return $ret;
}

// This function works like standard php usort function and uses sortPortList.
function usort_portlist(&$array)
{
	$temp_array = array();
	foreach($array as $portname)
		$temp_array[$portname] = 1;
	$array = array_keys (sortPortList ($temp_array, FALSE));
}

// This is a dual-purpose formating function:
// 1. Replace empty strings with nbsp.
// 2. Cut strings, which are too long, append "cut here" indicator and provide a mouse hint.
function niftyString ($string, $maxlen = 30, $usetags = TRUE)
{
	$cutind = '&hellip;'; // length is 1
	if (!mb_strlen ($string))
		return '&nbsp;';
	// a tab counts for a space
	$string = preg_replace ("/\t/", ' ', $string);
	if (!$maxlen or mb_strlen ($string) <= $maxlen)
		return htmlspecialchars ($string, ENT_QUOTES, 'UTF-8');
	return
		($usetags ? ("<span title='" . htmlspecialchars ($string, ENT_QUOTES, 'UTF-8') . "'>") : '') .
		str_replace (' ', '&nbsp;', htmlspecialchars (mb_substr ($string, 0, $maxlen - 1), ENT_QUOTES, 'UTF-8')) .
		$cutind .
		($usetags ? '</span>' : '');
}

// return a "?, ?, ?, ... ?, ?" string consisting of N question marks
function questionMarks ($count = 0)
{
	return implode (', ', array_fill (0, $count, '?'));
}

// returns search results as an array keyed with realm name
// groups found entities by realms in 'summary'
function searchEntitiesByText ($terms)
{
	$summary = array();

	if (FALSE !== ($ip_bin = ip4_checkparse ($terms)))
	// Search for IPv4 address.
	{
		if ($net_id = getIPv4AddressNetworkId ($ip_bin))
			$summary['ipv4addressbydq'][$ip_bin] = array ('net_id' => $net_id, 'ip' => $ip_bin);
	}
	elseif (FALSE !== ($ip_bin = ip6_checkparse ($terms)))
	// Search for IPv6 address
	{
		if ($net_id = getIPv6AddressNetworkId ($ip_bin))
			$summary['ipv6addressbydq'][$ip_bin] = array ('net_id' => $net_id, 'ip' => $ip_bin);
	}
	elseif (preg_match (RE_IP4_NET, $terms))
	// Search for IPv4 network
	{
		list ($base, $len) = explode ('/', $terms);
		if (NULL !== ($net_id = getIPv4AddressNetworkId (ip4_parse ($base), $len + 1)))
			$summary['ipv4net'][$net_id] = spotEntity('ipv4net', $net_id);
	}
	elseif (preg_match ('@(.*)/(\d+)$@', $terms, $matches) && FALSE !== ($ip_bin = ip6_checkparse ($matches[1])))
	// Search for IPv6 network
	{
		if (NULL !== ($net_id = getIPv6AddressNetworkId ($ip_bin, $matches[2] + 1)))
			$summary['ipv6net'][$net_id] = spotEntity('ipv6net', $net_id);
	}
	elseif (preg_match ('/^vlan\s*(\d+)$/i', $terms, $matches))
	{
		$byID = getSearchResultByField
		(
			'VLANDescription',
			array ('domain_id', 'vlan_id'),
			'vlan_id',
			$matches[1],
			'domain_id',
			1
		);
		foreach ($byID as $vlan)
		{
			// add vlans to results
			$vlan_ck = $vlan['domain_id'] . '-' . $vlan['vlan_id'];
			$vlan['id'] = $vlan_ck;
			$summary['vlan'][$vlan_ck] = $vlan;
			// add linked networks to results
			$vlan_info = getVLANInfo ($vlan_ck);
			foreach ($vlan_info['ipv4nets'] as $net_id)
				$summary['ipv4net'][$net_id] = spotEntity ("ipv4net", $net_id);
			foreach ($vlan_info['ipv6nets'] as $net_id)
				$summary['ipv6net'][$net_id] = spotEntity ("ipv6net", $net_id);
		}
	}
	else
	// Search for objects, addresses, networks, virtual services and RS pools by their description.
	{
		// search by FQDN has special treatment - if single object found, do not search by other fields
		$object_id_by_fqdn = NULL;
		$domains = preg_split ('/\s*,\s*/', strtolower (getConfigVar ('SEARCH_DOMAINS')));
		if (! empty ($domains) and $object_id = searchByMgmtHostname ($terms))
		{
			// get FQDN
			$attrs = getAttrValues ($object_id);
			$fqdn = '';
			if (isset ($attrs[3]['value']))
				$fqdn = strtolower (trim ($attrs[3]['value']));
			foreach ($domains as $domain)
				if ('.' . $domain === substr ($fqdn, -strlen ($domain) - 1))
				{
					$object_id_by_fqdn = $object_id;
					break;
				}
		}
		if ($object_id_by_fqdn)
		{
			$summary['object'][$object_id_by_fqdn] = array
			(
				'id' => $object_id_by_fqdn,
				'method' => 'fqdn',
			);
		}
		else
		{
			$summary['object'] = getObjectSearchResults ($terms);
			$summary['ipv4addressbydescr'] = getIPv4AddressSearchResult ($terms);
			$summary['ipv6addressbydescr'] = getIPv6AddressSearchResult ($terms);
			$summary['ipv4net'] = getIPv4PrefixSearchResult ($terms);
			$summary['ipv6net'] = getIPv6PrefixSearchResult ($terms);
			$summary['ipv4rspool'] = getIPv4RSPoolSearchResult ($terms);
			$summary['ipv4vs'] = getIPv4VServiceSearchResult ($terms);
			$summary['user'] = getAccountSearchResult ($terms);
			$summary['file'] = getFileSearchResult ($terms);
			$summary['rack'] = getRackSearchResult ($terms);
			$summary['vlan'] = getVLANSearchResult ($terms);
		}
	}
	# Filter search results in a way in some realms to omit records, which the
	# user would not be able to browse anyway.
	foreach (array ('object', 'ipv4net', 'ipv6net', 'ipv4rspool', 'ipv4vs', 'file', 'rack') as $realm)
		if (isset ($summary[$realm]))
			foreach ($summary[$realm] as $key => $record)
				if (! isolatedPermission ($realm, 'default', spotEntity ($realm, $record['id'])))
					unset ($summary[$realm][$key]);
	// clear empty search result realms
	foreach ($summary as $key => $data)
		if (! count ($data))
			unset ($summary[$key]);
	return $summary;
}

// returns URL to redirect to, or NULL if $result_type is unknown
function buildSearchRedirectURL ($result_type, $record)
{
	global $page;
	$next_page = $result_type;
	$id = isset ($record['id']) ? $record['id'] : NULL;
	$params = array();
	switch ($result_type)
	{
		case 'ipv4addressbydq':
		case 'ipv6addressbydq':
		case 'ipv4addressbydescr':
		case 'ipv6addressbydescr':
			$next_page = strlen ($record['ip']) == 16 ? 'ipv6net' : 'ipv4net';
			$id = isset ($record['net_id']) ? $record['net_id'] : getIPAddressNetworkId ($record['ip']);
			$params['hl_ip'] = ip_format ($record['ip']);
			break;
		case 'object':
			if (isset ($record['by_port']) and 1 == count ($record['by_port']))
			{
				$found_ports_ids = array_keys ($record['by_port']);
				$params['hl_port_id'] = $found_ports_ids[0];
			}
			break;
		case 'ipv4net':
		case 'ipv6net':
		case 'vlan':
		case 'user':
		case 'ipv4rspool':
		case 'ipv4vs':
		case 'file':
		case 'rack':
			break;
		default:
			return NULL;
	}
	if (array_key_exists ($next_page, $page) && isset ($page[$next_page]['bypass']))
		$key = $page[$next_page]['bypass'];
	if (! isset ($key) || ! isset ($id))
		return NULL;
	$params[$key] = $id;
	return buildRedirectURL ($next_page, 'default', $params);
}

function getRackCodeWarnings ()
{
	require_once 'code.php';
	$ret = array();
	global $rackCode;
	// tags
	foreach ($rackCode as $sentence)
		switch ($sentence['type'])
		{
			case 'SYNT_DEFINITION':
				$ret = array_merge ($ret, findTagWarnings ($sentence['definition']));
				break;
			case 'SYNT_ADJUSTMENT':
				$ret = array_merge ($ret, findTagWarnings ($sentence['condition']));
				$ret = array_merge ($ret, findCtxModWarnings ($sentence['modlist']));
				break;
			case 'SYNT_GRANT':
				$ret = array_merge ($ret, findTagWarnings ($sentence['condition']));
				break;
			default:
				$ret[] = array
				(
					'header' => 'internal error',
					'class' => 'error',
					'text' => "Skipped sentence of unknown type '${sentence['type']}'"
				);
		}
	// autotags
	foreach ($rackCode as $sentence)
		switch ($sentence['type'])
		{
			case 'SYNT_DEFINITION':
				$ret = array_merge ($ret, findAutoTagWarnings ($sentence['definition']));
				break;
			case 'SYNT_GRANT':
			case 'SYNT_ADJUSTMENT':
				$ret = array_merge ($ret, findAutoTagWarnings ($sentence['condition']));
				break;
			default:
				$ret[] = array
				(
					'header' => 'internal error',
					'class' => 'error',
					'text' => "Skipped sentence of unknown type '${sentence['type']}'"
				);
		}
	// predicates
	$plist = array();
	foreach ($rackCode as $sentence)
		if ($sentence['type'] == 'SYNT_DEFINITION')
			$plist[$sentence['term']] = $sentence['lineno'];
	foreach ($plist as $pname => $lineno)
	{
		foreach ($rackCode as $sentence)
			switch ($sentence['type'])
			{
				case 'SYNT_DEFINITION':
					if (referencedPredicate ($pname, $sentence['definition']))
						continue 3; // clear, next term
					break;
				case 'SYNT_GRANT':
				case 'SYNT_ADJUSTMENT':
					if (referencedPredicate ($pname, $sentence['condition']))
						continue 3; // idem
					break;
			}
		$ret[] = array
		(
			'header' => refRCLineno ($lineno),
			'class' => 'warning',
			'text' => "Predicate '${pname}' is defined, but never used."
		);
	}
	// expressions
	foreach ($rackCode as $sentence)
		switch (invariantExpression ($sentence))
		{
			case 'always true':
				$ret[] = array
				(
					'header' => refRCLineno ($sentence['lineno']),
					'class' => 'warning',
					'text' => "Expression is always true."
				);
				break;
			case 'always false':
				$ret[] = array
				(
					'header' => refRCLineno ($sentence['lineno']),
					'class' => 'warning',
					'text' => "Expression is always false."
				);
				break;
			default:
				break;
		}
	// bail out
	$nwarnings = count ($ret);
	$ret[] = array
	(
		'header' => 'summary',
		'class' => $nwarnings ? 'error' : 'success',
		'text' => "Analysis complete, ${nwarnings} issues discovered."
	);
	return $ret;
}

// Take a parse tree and figure out if it is a valid payload or not.
// Depending on that return either NULL or an array filled with the load
// of that expression.
function spotPayload ($text, $reqtype = 'SYNT_CODETEXT')
{
	require_once 'code.php';
	$lex = getLexemsFromRawText ($text);
	if ($lex['result'] != 'ACK')
		return $lex;
	$stack = getParseTreeFromLexems ($lex['load']);
	// The only possible way to "accept" is to have sole starting
	// nonterminal on the stack (and it must be of the requested class).
	if (count ($stack) == 1 and $stack[0]['type'] == $reqtype)
		return array ('result' => 'ACK', 'load' => isset ($stack[0]['load']) ? $stack[0]['load'] : $stack[0]);
	// No luck. Prepare to complain.
	if ($lineno = locateSyntaxError ($stack))
		return array ('result' => 'NAK', 'load' => "Syntax error for type '${reqtype}' near line ${lineno}");
	// HCF!
	return array ('result' => 'NAK', 'load' => "Syntax error for type '${reqtype}', line number unknown");
}

// Top-level wrapper for most of the code in this file. Get a text, return a parse tree
// (or error message).
function getRackCode ($text)
{
	if (!mb_strlen ($text))
		return array ('result' => 'NAK', 'load' => 'The RackCode text was found empty in ' . __FUNCTION__);
	$text = str_replace ("\r", '', $text) . "\n";
	$synt = spotPayload ($text, 'SYNT_CODETEXT');
	if ($synt['result'] != 'ACK')
		return $synt;
	// An empty sentence list is semantically valid, yet senseless,
	// so checking intermediate result once more won't hurt.
	if (!count ($synt['load']))
		return array ('result' => 'NAK', 'load' => 'Empty parse tree found in ' . __FUNCTION__);
	require_once 'code.php'; // for semanticFilter()
	return semanticFilter ($synt['load']);
}

// returns array with 'from', 'length' keys.
// if not found, 'from' is NULL;
// if length is not defined (to the end of line), length is -1
function getColumnCoordinates ($line, $column_name, $align = 'left')
{
	$result = array ('from' => NULL, 'length' => -1);
	$items = preg_split('/\s+/', $line);
	for ($i = 0; $i < count ($items); $i++)
	{
		$item = $items[$i];
		if ($column_name == $item)
		{
			$current_start = strpos ($line, $items[$i]);
			if ($align == 'left')
			{
				$result['from'] = $current_start;
				if ($i < count ($items) - 1)
				{
					$next_start = strpos ($line, $items[$i + 1]);
					$result['length'] = $next_start - $result['from'] - 1;
				}
				else
					$result['length'] = -1;
			}
			elseif ($align == 'right')
			{
				if ($i > 0)
					$prev_end = strpos ($line, $items[$i - 1]) + strlen ($items[$i - 1]);
				else
					$prev_end = -1;
				$result['from'] = $prev_end + 1;
				$result['length'] = $current_start - $result['from'] + strlen ($column_name);
			}
			break;
		}
	}
	return $result;
}

// Messages in the top of the page should be shown using these functions.
// You can call them multiple times to show multiple messages.
// $option can be 'inline' to echo message div, instead of putting it into $_SESSION and draw on next index page show
// These functions always return NULL
function showError   ($message, $option = '')
{
	setMessage ('error',   $message, $option == 'inline');
}

function showWarning ($message, $option = '')
{
	setMessage ('warning', $message, $option == 'inline');
}

function showSuccess ($message, $option = '')
{
	setMessage ('success', $message, $option == 'inline');
}

function showNotice  ($message, $option = '')
{
	setMessage ('neutral', $message, $option == 'inline');
}

// do not call this directly, use showError and its siblings instead
// $type could be 'error', 'warning', 'success' or 'neutral'
function setMessage ($type, $message, $direct_rendering)
{
	global $script_mode;
	if ($direct_rendering)
		echo '<div class="msg_' . $type . '">' . $message . '</div>';
	elseif (isset ($script_mode) and $script_mode)
	{
		if ($type == 'warning' or $type == 'error')
			file_put_contents ('php://stderr', strtoupper ($type) . ': ' . $message . "\n");
	}
	else
	{
		switch ($type)
		{
			case 'error':
				$code = 100;
				break;
			case 'warning':
				$code = 200;
				break;
			case 'success';
				$code = 0;
				break;
			case 'neutral':
			default:
				$code = 300;
				break;
		}
		showOneLiner ($code, array ($message));
	}
}

function showOneLiner ($code, $args = array())
{
	global $log_messages;
	$line = array ('c' => $code);
	if (! empty ($args))
		$line['a'] = $args;
	$log_messages[] = $line;
}

function showFuncMessage ($callfunc, $status, $log_args = array())
{
	global $msgcode;
	if (isset ($msgcode[$callfunc][$status]))
		showOneLiner ($msgcode[$callfunc][$status], $log_args);
	else
		showWarning ("Message '$status' is lost in $callfunc");
}

// function returns integer count of unshown messages in log buffer.
// message_type can be 'all', 'success', 'error', 'warning', 'neutral'.
function getMessagesCount ($message_type = 'all')
{
	global $log_messages;
	$result = 0;
	foreach ($log_messages as $msg)
		if ($msg['c'] < 100)
		{
			if ($message_type == 'success' || $message_type == 'all')
				++$result;
		}
		elseif ($msg['c'] < 200)
		{
			if ($message_type == 'error' || $message_type == 'all')
				++$result;
		}
		elseif ($msg['c'] < 300)
		{
			if ($message_type == 'warning' || $message_type == 'all')
				++$result;
		}
		else
		{
			if ($message_type == 'neutral' || $message_type == 'all')
				++$result;
		}
	return $result;
}

function isEthernetPort($port)
{
	return ($port['iif_id'] != 1 or preg_match('/Base|LACP/i', $port['oif_name']));
}

function loadConfigDefaults()
{
	global $configCache;
	$configCache = loadConfigCache();
	if (!count ($configCache))
		throw new RackTablesError ('Failed to load configuration from the database.', RackTablesError::INTERNAL);
	foreach ($configCache as $varname => &$row)
	{
		$row['is_altered'] = 'no';
		if ($row['vartype'] == 'uint') $row['varvalue'] = 0 + $row['varvalue'];
		$row['defaultvalue'] = $row['varvalue'];
	}
}

function alterConfigWithUserPreferences()
{
	global $configCache;
	global $userConfigCache;
	global $remote_username;
	$userConfigCache = loadUserConfigCache($remote_username);
	foreach ($userConfigCache as $key => $row)
	{
		if ($configCache[$key]['is_userdefined'] == 'yes')
		{
			$configCache[$key]['varvalue'] = $row['varvalue'];
			$configCache[$key]['is_altered'] = 'yes';
		}
	}
}

// Returns true if varname has a different value or varname is new
function isConfigVarChanged ($varname, $varvalue)
{
	global $configCache;
	if (!isset ($configCache))
		throw new RackTablesError ('configuration cache is unavailable', RackTablesError::INTERNAL);
	if ($varname == '')
		throw new InvalidArgException('$varname', $varname, 'Empty variable name');
	if (!isset ($configCache[$varname])) return true;
	if ($configCache[$varname]['vartype'] == 'uint')
		return $configCache[$varname]['varvalue'] !== 0 + $varvalue;
	else
		return $configCache[$varname]['varvalue'] !== $varvalue;
}

function getConfigVar ($varname = '')
{
	global $configCache;
	// We assume the only point of cache init, and it is init.php. If it
	// has failed, we don't retry loading.
	if (!isset ($configCache))
		throw new RackTablesError ('configuration cache is unavailable', RackTablesError::INTERNAL);
	if
	(
		$varname == ''
		or ! array_key_exists ($varname, $configCache)
	)
		throw new InvalidArgException ('$varname', $varname);
	return $configCache[$varname]['varvalue'];
}

// return portinfo array if object has a port with such name, or NULL
function getPortinfoByName (&$object, $portname)
{
	if (! isset ($object['ports']))
		amplifyCell ($object);
	foreach ($object['ports'] as $portinfo)
		if ($portinfo['name'] == $portname)
			return $portinfo;
	return NULL;
}

# For the given object ID return a getSelect-suitable list of object types
# compatible with the object's attributes, which have an assigned value in
# AttributeValue (no assigned values mean full compatibility). Being compatible
# with an attribute means having a record in AttributeMap (with the same chapter
# ID, if the attribute is dictionary-based). This knowledge is required to allow
# the user changing object type ID in a way, which leaves data in AttributeValue
# meeting constraints in AttributeMap upon the change.
function getObjectTypeChangeOptions ($object_id)
{
	$map = getAttrMap();
	$used = array();
	$ret = array();
	foreach (getAttrValues ($object_id) as $attr)
	{
		if (! array_key_exists ($attr['id'], $map))
			return array(); // inconsistent current data
		if ($attr['value'] != '')
			$used[] = $attr;
	}
	foreach (readChapter (CHAP_OBJTYPE, 'o') as $test_id => $text)
	{
		foreach ($used as $attr)
		{
			$app = $map[$attr['id']]['application'];
			if
			(
				(NULL === $appidx = scanArrayForItem ($app, 'objtype_id', $test_id)) or
				($attr['type'] == 'dict' and $attr['chapter_id'] != $app[$appidx]['chapter_no'])
			)
				continue 2; // next type ID
		}
		$ret[$test_id] = $text;
	}
	return $ret;
}

// Gets the timestamp and returns human-friendly short message describing the time difference
// between the current system time and the specified timestamp (like '2d 5h ago')
function formatAge ($timestamp)
{
	$seconds = time() - $timestamp;
	switch (TRUE)
	{
		case $seconds < 1:
			return 'just now';
		case $seconds < 60:
			return "${seconds}s" . ' ago';
		case $seconds <= 300:
			$mins = intval ($seconds / 60);
			$secs = $seconds % 60;
			return ($secs ? "{$mins}min ${secs}s" : "{$mins}m") . ' ago';
		case $seconds < 3600:
			return round ($seconds / 60) . 'min' . ' ago';
		case $seconds < 3 * 3600:
			$hrs = intval ($seconds / 3600);
			$mins = round (($seconds % 3600) / 60) . '';
			return ($mins ? "${hrs}h ${mins}min" : "${hrs}h") . ' ago';
		case $seconds < 86400:
			return round ($seconds / 3600) . 'h' . ' ago';
		case $seconds < 86400 * 3:
			$days = intval ($seconds / 86400);
			$hrs = round (($seconds - $days * 86400) / 3600);
			return ($hrs ? "${days}d ${hrs}h" : "${days}d") . ' ago';
		case $seconds < 86400 * 30.4375:
			return round ($seconds / 86400) . 'd' . ' ago';
		case $seconds < 86400 * 30.4375 * 4 :
			$mon = intval ($seconds / 86400 / 30.4375);
			$days = round (($seconds - $mon * 86400 * 30.4375) / 86400);
			return ($days ? "${mon}m ${days}d" : "${mon}m") . ' ago';
		case $seconds < 365.25 * 86400:
			return (round ($seconds / 86400 / 30.4375) . 'm') . ' ago';
		case $seconds < 2 * 365.25 * 86400:
			$yrs = intval ($seconds / 86400 / 365.25);
			$mon = round (($seconds - $yrs * 86400 * 365.25) / 86400 / 30.4375);
			return ($mon ? "${yrs}y ${mon}m" : "${yrs}y") . ' ago';
		default:
			return (round ($seconds / 86400 / 365.25) . 'y') . ' ago';
	}
}

// proxy function returning the output of another function. Takes any number of additional parameters
function getOutputOf ($func_name)
{
	ob_start();
	try
	{
		$params = func_get_args();
		array_shift($params);
		call_user_func_array ($func_name, $params);
		return ob_get_clean();
	}
	catch (Exception $e)
	{
		ob_clean();
		throw $e;
	}
}

// calls function which can be overriden in $hook array. Takes any number of additional parameters
function callHook ($hook_name)
{
	global $hook;
	$callback = $hook_name;
	if (isset ($hook[$hook_name]))
		$callback = $hook[$hook_name];
	$params = func_get_args();
	if ($callback !== 'universalHookHandler')
		array_shift ($params);
	if (is_callable ($callback))
		return call_user_func_array ($callback, $params);
}

// function to parse text table header, aligned by left side
// returns array suitable to be used by explodeTableLine
function guessTableStructure ($line)
{
	$ret = array();
	$i = 0;
	while (strlen ($line))
	{
		if (! preg_match ('/^(\s*\S+\s*)/', $line, $m))
			break;
		$header = trim ($m[1]);
		$ret[$header] = array ('begin' => $i, 'length' => strlen ($m[1]));
		$line = substr ($line, strlen ($m[1]));
		$i += strlen ($m[1]);
	}
	return $ret;
}

// takes text-formatted table line and an array returned by guessTableStructure
// returns array indexed by cell name. Works for left-aligned tables only
function explodeTableLine ($line, $table_schema)
{
	$ret = array();
	foreach ($table_schema as $header => $constraints)
	{
		$value = substr ($line, $constraints['begin'], $constraints['length']);
		$ret[$header] = trim ($value);
	}
	return $ret;
}

// returns number of changed ports (both local and remote)
// shows error messages unconditionally, and success messages respecting  to $verbose setting
// if $mutex_rev is set, checks if it is outdated
// NOTE: this function is calling itself through initiateUplinksReverb. It is important that
// the call to initiateUplinksReverb is outside of DB transaction scope.
function apply8021qChangeRequest ($switch_id, $changes, $verbose = TRUE, $mutex_rev = NULL)
{
	global $dbxlink;
	$dbxlink->beginTransaction();
	try
	{
		if (NULL === $vswitch = getVLANSwitchInfo ($switch_id, 'FOR UPDATE'))
			throw new InvalidArgException ('object_id', $switch_id, 'VLAN domain is not set for this object');
		if (isset ($mutex_rev) and $vswitch['mutex_rev'] != $mutex_rev)
			throw new InvalidRequestArgException ('mutex_rev', $mutex_rev, 'expired form data');
		$after = $before = apply8021QOrder ($vswitch['template_id'], getStored8021QConfig ($vswitch['object_id'], 'desired'));
		$domain_vlanlist = getDomainVLANs ($vswitch['domain_id']);
		$changes = filter8021QChangeRequests
		(
			$domain_vlanlist,
			$before,
			apply8021QOrder ($vswitch['template_id'], $changes)
		);
		$desired_ports_count = count ($changes);
		$changes = authorize8021QChangeRequests ($before, $changes);
		if (count ($changes) < $desired_ports_count)
			showWarning (sprintf ("Permission denied to change %d ports", $desired_ports_count - count ($changes)));
		foreach ($changes as $port_name => $port)
			$after[$port_name] = $port;
		$new_uplinks = filter8021QChangeRequests ($domain_vlanlist, $after, produceUplinkPorts ($domain_vlanlist, $after, $vswitch['object_id']));
		$npulled = replace8021QPorts ('desired', $vswitch['object_id'], $before, $changes);
		$nsaved_uplinks = replace8021QPorts ('desired', $vswitch['object_id'], $before, $new_uplinks);
		if ($npulled + $nsaved_uplinks)
			touchVLANSwitch ($vswitch['object_id']);
		$dbxlink->commit();
	}
	catch (Exception $e)
	{
		$dbxlink->rollBack();
		showError (sprintf ("Failed to update switchports: %s", $e->getMessage()));
		return 0;
	}
	$nsaved_downlinks = 0;
	if ($nsaved_uplinks)
		$nsaved_downlinks = initiateUplinksReverb ($vswitch['object_id'], $new_uplinks);
	// instant deploy to that switch if configured
	$done = 0;
	if ($npulled + $nsaved_uplinks > 0 and getConfigVar ('8021Q_INSTANT_DEPLOY') == 'yes')
	{
		try
		{
			if (FALSE === $done = exec8021QDeploy ($vswitch['object_id'], TRUE))
				showError ("deploy was blocked due to conflicting configuration versions");
			elseif ($verbose)
				showSuccess (sprintf ("Configuration for %u port(s) have been deployed", $done));
		}
		catch (Exception $e)
		{
			showError (sprintf ("Failed to deploy changes to switch: %s", $e->getMessage()));
		}
	}
	// report number of changed ports
	$total = $npulled + $nsaved_uplinks + $nsaved_downlinks;
	if ($verbose)
	{
		$message = sprintf ('%u port(s) have been changed', $total);
		if ($total > 0)
			showSuccess ($message);
		else
			showNotice ($message);
	}
	return $total;
}

// takes a full sublist of ipv4net entities ordered by (ip,mask)
// fills ['spare_ranges'] and ['kidc'] fields of each item of $nets.
function fillIPNetsCorrelation (&$nets)
{
	$stack = array();
	foreach ($nets as &$net)
	{
		$last = NULL; // last element popped from the stack
		while (count ($stack)) // spin stack leaving only the parents of the $net.
		{
			$top = &$stack[count ($stack) - 1];
			if (IPNetContains ($top, $net))
			{
				$top['kidc']++;
				break;
			}
			if (isset ($last))
				// possible hole in the end of $top
				fillIPSpareListBstr ($top, ip_next (ip_last ($last)), ip_last ($top));
			$last = array_pop ($stack);
		}
		if (count ($stack))
		{
			$top = &$stack[count ($stack) - 1];
			if (isset ($last))
				// possible hole in the middle of $top
				fillIPSpareListBstr ($top, ip_next (ip_last ($last)), ip_prev ($net['ip_bin']));
			else
				// possible hole in the beginning of $top
				fillIPSpareListBstr ($top, $top['ip_bin'], ip_prev ($net['ip_bin']));
		}
		$stack[] = &$net;
	}
	// final stack spin
	$last = NULL;
	while (count ($stack))
	{
		$top = &$stack[count ($stack) - 1];
		if (isset ($last))
		{
			// possible hole in the end of $top
			$last = ip_last ($last);
			$a = ip_next ($last);
			// check for crossing 0
			if (0 > strcmp ($a, $last))
				break;
			fillIPSpareListBstr ($top, $a, ip_last ($top));
		}
		$last = array_pop ($stack);
	}
}

// $a, $b - binary strings (IP addresses) meaning the beginning and the end of IP range.
// $net is an entity for hole autotags to be set to.
function fillIPSpareListBstr (&$net, $a, $b)
{
	$len = strlen ($a) * 8;
	while (0 >= strcmp ($a, $b))
	{
		$max_mask = 0; // the number of common binary bits in the major of $a and $b
		$xor = $a ^ $b;
		for ($i = 0; $i < strlen ($xor); $i++)
		{
			$max_mask += 8;
			if ($xor[$i] != "\0")
			{
				$byte = ord ($xor[$i]);
				do
				{
					$max_mask--;
					$byte >>= 1;
				} while ($byte != 0);
				break;
			}
		}

		for ($mask = $max_mask; $mask <= $len; $mask++)
		{
			$bmask = ip_mask ($mask, $len == 128);
			$last_a = $a | ~ $bmask;
			if ($a == ($a & $bmask) and 0 >= strcmp ($last_a, $b))
			{
				$net['spare_ranges'][$mask][] = $a;
				$a = ip_next ($last_a);
				// check for crossing 0
				if (0 > strcmp ($a, $last_a))
					break 2;
				break;
			}
		}
	}
}

// returns TRUE if the network cell is allowed to be deleted, FALSE otherwise
// $netinfo could be either ipv4net or ipv6net entity.
// in case of returning FALSE, $netinfo['addrlist'] is set
function isIPNetworkEmpty (&$netinfo)
{
	if (getConfigVar ('IPV4_JAYWALK') == 'yes')
		return TRUE;
	if (! isset ($netinfo['addrlist']))
		loadIPAddrList ($netinfo);
	$pure_array = ($netinfo['realm'] == 'ipv4net') ?
		array ($netinfo['ip_bin'] => 'network', ip_last ($netinfo) => 'broadcast') : // v4
		array ($netinfo['ip_bin'] => 'Subnet-Router anycast'); // v6
	$pure_auto = 0;
	foreach ($pure_array as $ip => $comment)
		if
		(
			array_key_exists ($ip, $netinfo['addrlist']) and
			$netinfo['addrlist'][$ip]['name'] == $comment and
			$netinfo['addrlist'][$ip]['reserved'] == 'yes' and
			! count ($netinfo['addrlist'][$ip]['allocs']) and
			! count ($netinfo['addrlist'][$ip]['rsplist']) and
			! count ($netinfo['addrlist'][$ip]['vslist']) and
			(
				$netinfo['realm'] == 'ipv6net' or (
					! count ($netinfo['addrlist'][$ip]['outpf']) and
					! count ($netinfo['addrlist'][$ip]['inpf'])

				)
			)
		)
			$pure_auto++;
	return ($netinfo['own_addrc'] <= $pure_auto);
}

// returns the first element of given array, or NULL if array is empty
function array_first ($array)
{
	$single = array_slice (array_values ($array), 0, 1);
	if (count ($single))
		return $single[0];
}

// returns the last element of given array, or NULL if array is empty
function array_last ($array)
{
	$single = array_slice (array_values ($array), -1, 1);
	if (count ($single))
		return $single[0];
}

// Registers additional ophandler on page-tab-opname triplet.
// Valid $method values are 'before' and 'after'.
//   'before' puts your ophandler in the beginning of the list (and thus before the default)
//   'after' puts your ophandler to the end of the list (and thus after the default)
function registerOpHandler ($page, $tab, $opname, $callback, $method = 'before')
{
	global $ophandlers_stack;
	global $ophandler;
	if (! isset ($ophandler[$page][$tab][$opname]))
		$ophandlers_stack[$page][$tab][$opname] = array();
	if (isset ($ophandler[$page][$tab][$opname]) && $ophandler[$page][$tab][$opname] != 'universalOpHandler')
	{
		$ophandlers_stack[$page][$tab][$opname] = array ($ophandler[$page][$tab][$opname]);
		$ophandler[$page][$tab][$opname] = 'universalOpHandler';
	}
	$ophandler[$page][$tab][$opname] = 'universalOpHandler';
	if ($method == 'before')
		array_unshift ($ophandlers_stack[$page][$tab][$opname], $callback);
	elseif ($method == 'after')
		array_push ($ophandlers_stack[$page][$tab][$opname], $callback);
	else
		throw new RacktablesError ("unknown ophandler injection method '$method'");
}

// call this from custom ophandler registered by registerOpHandler
// to prevent the rest of the ophanlers to run
function stopOpPropagation()
{
	global $ophandler_propagation_stop;
	$ophandler_propagation_stop = TRUE;
}

function universalOpHandler()
{
	global $ophandler_propagation_stop;
	$ophandler_propagation_stop = FALSE;
	global $ophandlers_stack;
	global $pageno, $tabno, $op;
	$op_stack = $ophandlers_stack[$pageno][$tabno][$op];
	$ret = NULL;
	foreach ($op_stack as $callback)
	{
		$ret_i = call_user_func ($callback);
		if (strlen ($ret_i) || ! isset ($ret))
			$ret = $ret_i;
		if ($ophandler_propagation_stop)
			break;
	}
	return $ret;
}

// Registers additional tabhandler on page-tab pair.
// Valid $method values are 'before', 'after' and 'replace'.
//   'before' puts your tabhandler in the beginning of the list (and thus before the default)
//   'after' puts your tabhandler to the end of the list (and thus after the default)
//   'replace': the same as 'after', but the rendered tab is replaced by your output, not appended. See also getRenderedTab
function registerTabHandler ($page, $tab, $callback, $method = 'after')
{
	global $tabhandlers_stack;
	global $tabhandler;

	if (! isset ($tabhandlers_stack[$page][$tab]))
		$tabhandlers_stack[$page][$tab] = array();

	if (isset ($tabhandler[$page][$tab]) && $tabhandler[$page][$tab] != 'universalTabHandler')
		array_push ($tabhandlers_stack[$page][$tab], $tabhandler[$page][$tab]);
	$tabhandler[$page][$tab] = 'universalTabHandler';

	if ($method == 'before')
		array_unshift ($tabhandlers_stack[$page][$tab], $callback);
	elseif ($method == 'after')
		array_push ($tabhandlers_stack[$page][$tab], $callback);
	elseif ($method == 'replace')
		array_push ($tabhandlers_stack[$page][$tab], '!' . $callback);
	else
		throw new RacktablesError ("unknown tabhandler injection method '$method'");
}

// Returns  tab content already rendered by previous tabhandlers in the chain registered by registerTabHandler.
// It is useful in custom tabhandlers registered with 'replace' method.
function getRenderedTab()
{
	global $tabhandler_output;
	return $tabhandler_output;
}

// call this from custom tabhandler registered by registerTabHandler
// to prevent the rest of the tabhanlers to run
function stopTabPropagation()
{
	global $tabhandler_propagation_stop;
	$tabhandler_propagation_stop = TRUE;
}

function universalTabHandler($bypass = NULL)
{
	global $tabhandler_propagation_stop;
	$tabhandler_propagation_stop = FALSE;
	global $tabhandlers_stack;
	global $tabhandler_output;
	global $pageno, $tabno;
	$tab_stack = $tabhandlers_stack[$pageno][$tabno];
	$ret = NULL;
	$tabhandler_output = '';
	foreach ($tab_stack as $callback)
	{
		$do_replace = FALSE;
		if ($callback[0] == '!')
		{
			$callback = substr ($callback, 1);
			$do_replace = TRUE;
		}
		ob_start();
		$ret = call_user_func ($callback, $bypass);
		$current_output = ob_get_contents();
		ob_end_clean();
		if ($do_replace)
			$tabhandler_output = $current_output;
		else
			$tabhandler_output .= $current_output;
		if ($tabhandler_propagation_stop)
			break;
	}
	echo $tabhandler_output;
	return $ret;
}

// $method could be 'before', 'after', 'chain'
function registerHook ($hook_name, $callback, $method = 'after')
{
	global $hooks_stack, $hook;

	if (! isset ($hooks_stack[$hook_name]))
		$hooks_stack[$hook_name] = array();

	if (isset ($hook[$hook_name]) && $hook[$hook_name] != 'universalHookHandler')
		array_push ($hooks_stack[$hook_name], $hook[$hook_name]);
	$hook[$hook_name] = 'universalHookHandler';

	if ($method == 'before')
		array_unshift ($hooks_stack[$hook_name], $callback);
	elseif ($method == 'after')
		array_push ($hooks_stack[$hook_name], $callback);
	elseif ($method == 'chain')
	{
		// if we are trying to chain on the built-in function, push it to the stack
		if (empty ($hooks_stack[$hook_name]) && is_callable ($hook_name))
			array_push ($hooks_stack[$hook_name], $hook_name);

		array_push ($hooks_stack[$hook_name], '!' . $callback);
	}
	else
		throw new InvalidRequestArgException ('method', $method, "Invalid hook method");
}

// hook handlers dispatcher. registerHook leaves 'universalHookHandler' in $hook
function universalHookHandler()
{
	global $hook_propagation_stop;
	$hook_propagation_stop = FALSE;
	global $hooks_stack;
	$ret = NULL;
	$bk_params = func_get_args();
	$hook_name = array_shift ($bk_params);
	if (! array_key_exists ($hook_name, $hooks_stack) || ! is_array ($hooks_stack[$hook_name]))
		throw new InvalidRequestArgException ('$hooks_stack["' . $hook_name . '"]', $hooks_stack[$hook_name]);

	foreach ($hooks_stack[$hook_name] as $callback)
	{
		$params = $bk_params;
		if ('!' === substr ($callback, 0, 1))
		{
			$callback = substr ($callback, 1);
			array_unshift ($params, $ret);
		}
		if (is_callable ($callback))
			$ret = call_user_func_array ($callback, $params);
		else
			throw new RackTablesError ("Call of non-existant callback '$callback'", RackTablesError::INTERNAL);
		if ($hook_propagation_stop)
			break;
	}
	return $ret;
}

// call this from custom hook registered by registerHook
// to prevent the rest of the hooks to run
function stopHookPropagation()
{
	global $hook_propagation_stop;
	$hook_propagation_stop = TRUE;
}

function arePortTypesCompatible ($oif1, $oif2)
{
	foreach (getPortOIFCompat() as $item)
		if ($item['type1'] == $oif1 && $item['type2'] == $oif2)
			return TRUE;
	return FALSE;
}

function arePortsCompatible ($portinfo_a, $portinfo_b)
{
	return arePortTypesCompatible ($portinfo_a['oif_id'], $portinfo_b['oif_id']);
}

// takes an array of cells,
// returns an array indexed by cell id, values are simple text representation of a cell.
// Intended to pass its return value to printSelect routine.
function formatEntityList ($list)
{
	$ret = array();
	foreach ($list as $entity)
		switch ($entity['realm'])
		{
			case 'object':
				$ret[$entity['id']] = $entity['dname'];
				break;
			case 'ipv4vs':
				$ret[$entity['id']] = $entity['name'] . (strlen ($entity['name']) ? ' ' : '') . '(' . $entity['dname'] . ')';
				break;
			case 'ipv4rspool':
				$ret[$entity['id']] = $entity['name'];
				break;
			default:
				$ret[$entity['id']] = $entity['realm'] . '#' . $entity['id'];
		}
	asort ($ret);
	return $ret;
}

// returns reversed (top-to-bottom) $unit_no if $rack_cell is configured to be reversed,
// or unchanged $unit_no otherwise.
function inverseRackUnit ($unit_no, $rack_cell)
{
	if (considerConfiguredConstraint ($rack_cell, 'REVERSED_RACKS_LISTSRC'))
		$unit_no = $rack_cell['height'] - $unit_no + 1;
	return $unit_no;
}

function isCLIMode ()
{
	return !isset ($_SERVER['REQUEST_METHOD']);
}

// Checks if 802.1Q port uplink/downlink feature is misconfigured.
// Returns FALSE if 802.1Q port role/linking is wrong, TRUE otherwise.
function checkPortRole ($vswitch, $port_name, $port_order)
{
	static $links_cache = array();
	if (! isset ($links_cache[$vswitch['object_id']]))
		$links_cache = array ($vswitch['object_id'] => getObjectPortsAndLinks ($vswitch['object_id']));

	$local_auto = ($port_order['vst_role'] == 'uplink' || $port_order['vst_role'] == 'downlink') ?
		$port_order['vst_role'] :
		FALSE;

	// find linked port with the same name
	foreach ($links_cache[$vswitch['object_id']] as $portinfo)
		if ($portinfo['linked'] && ios12ShortenIfName ($portinfo['name']) == $port_name)
		{
			if ($port_name != $portinfo['name'])
				return FALSE; // typo in local port name
			$remote_vswitch = getVLANSwitchInfo ($portinfo['remote_object_id']);
			if (! $remote_vswitch)
				return ! $local_auto;

			$remote_ports = apply8021QOrder ($remote_vswitch['template_id'], getStored8021QConfig ($remote_vswitch['object_id'], 'desired'));
			if (! $remote = @$remote_ports[$portinfo['remote_name']])
				// linked auto-port must have corresponding remote 802.1Q port
				return
					! $local_auto &&
					! isset ($remote_ports[ios12ShortenIfName ($portinfo['remote_name'])]); // typo in remote port name

			$remote_auto = ($remote['vst_role'] == 'uplink' || $remote['vst_role'] == 'downlink') ?
				$remote['vst_role'] :
				FALSE;

			if (! $remote_auto && ! $local_auto)
				return TRUE;
			elseif ($remote_auto && $local_auto && $local_auto != $remote_auto && $vswitch['domain_id'] == $remote_vswitch['domain_id'])
				return TRUE; // auto-calc link ends must belong to the same domain
			else
				return FALSE;
		}
	return TRUE; // not linked port
}

# Convert InvalidArgException to InvalidRequestArgException with a choice of
# replacing the reference to the failed argument or leaving it unchanged.
function convertToIRAE ($iae, $override_argname = NULL)
{
	if (! ($iae instanceof InvalidArgException))
		throw new InvalidArgException ('iae', '(object)', 'not an instance of InvalidArgException class');

	if (is_null ($override_argname))
	{
		$reported_argname = $iae->getName();
		$reported_argvalue = $iae->getValue();
	}
	else
	{
		$reported_argname = $override_argname;
		$reported_argvalue = $_REQUEST[$override_argname];
	}
	return new InvalidRequestArgException ($reported_argname, $reported_argvalue, $iae->getReason());
}

# Produce a textual date/time from a given UNIX timestamp
# If timestamp is omitted, time() value is used
function datetimestrFromTimestamp ($ts = NULL)
{
	if (! isset ($ts))
		$ts = time();
	return strftime (getConfigVar ('DATETIME_FORMAT'), $ts);
}

# vice versa
function timestampFromDatetimestr ($s)
{
	$format = getConfigVar ('DATETIME_FORMAT');
	if (FALSE === $tmp = strptime ($s, $format))
		throw new InvalidArgException ('s', $s, "not a date in format '${format}'");
	return mktime
	(
		$tmp['tm_hour'],       # 0~23
		$tmp['tm_min'],        # 0~59
		$tmp['tm_sec'],        # 0~59
		$tmp['tm_mon'] + 1,    # 0~11 -> 1~12
		$tmp['tm_mday'],       # 1~31
		$tmp['tm_year'] + 1900 # 0~n -> 1900~n
	);
}

?>
