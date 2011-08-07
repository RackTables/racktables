<?php
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

$virtual_obj_types = array
(
	1504,
	1505,
	1506,
	1507
);

$netmaskbylen = array
(
	32 => '255.255.255.255',
	31 => '255.255.255.254',
	30 => '255.255.255.252',
	29 => '255.255.255.248',
	28 => '255.255.255.240',
	27 => '255.255.255.224',
	26 => '255.255.255.192',
	25 => '255.255.255.128',
	24 => '255.255.255.0',
	23 => '255.255.254.0',
	22 => '255.255.252.0',
	21 => '255.255.248.0',
	20 => '255.255.240.0',
	19 => '255.255.224.0',
	18 => '255.255.192.0',
	17 => '255.255.128.0',
	16 => '255.255.0.0',
	15 => '255.254.0.0',
	14 => '255.252.0.0',
	13 => '255.248.0.0',
	12 => '255.240.0.0',
	11 => '255.224.0.0',
	10 => '255.192.0.0',
	9 => '255.128.0.0',
	8 => '255.0.0.0',
	7 => '254.0.0.0',
	6 => '252.0.0.0',
	5 => '248.0.0.0',
	4 => '240.0.0.0',
	3 => '224.0.0.0',
	2 => '192.0.0.0',
	1 => '128.0.0.0'
);

$wildcardbylen = array
(
	32 => '0.0.0.0',
	31 => '0.0.0.1',
	30 => '0.0.0.3',
	29 => '0.0.0.7',
	28 => '0.0.0.15',
	27 => '0.0.0.31',
	26 => '0.0.0.63',
	25 => '0.0.0.127',
	24 => '0.0.0.255',
	23 => '0.0.1.255',
	22 => '0.0.3.255',
	21 => '0.0.7.255',
	20 => '0.0.15.255',
	19 => '0.0.31.255',
	18 => '0.0.63.255',
	17 => '0.0.127.255',
	16 => '0.0.255.25',
	15 => '0.1.255.255',
	14 => '0.3.255.255',
	13 => '0.7.255.255',
	12 => '0.15.255.255',
	11 => '0.31.255.255',
	10 => '0.63.255.255',
	9 => '0.127.255.255',
	8 => '0.255.255.255',
	7 => '1.255.255.255',
	6 => '3.255.255.255',
	5 => '7.255.255.255',
	4 => '15.255.255.255',
	3 => '31.255.255.255',
	2 => '63.255.255.255',
	1 => '127.255.255.255'
);

$masklenByDQ = array
(
	'255.255.255.255' => 32,
	'255.255.255.254' => 31,
	'255.255.255.252' => 30,
	'255.255.255.248' => 29,
	'255.255.255.240' => 28,
	'255.255.255.224' => 27,
	'255.255.255.192' => 26,
	'255.255.255.128' => 25,
	'255.255.255.0' => 24,
	'255.255.254.0' => 23,
	'255.255.252.0' => 22,
	'255.255.248.0' => 21,
	'255.255.240.0' => 20,
	'255.255.224.0' => 19,
	'255.255.192.0' => 18,
	'255.255.128.0' => 17,
	'255.255.0.0' => 16,
	'255.254.0.0' => 15,
	'255.252.0.0' => 14,
	'255.248.0.0' => 13,
	'255.240.0.0' => 12,
	'255.224.0.0' => 11,
	'255.192.0.0' => 10,
	'255.128.0.0' => 9,
	'255.0.0.0' => 8,
	'254.0.0.0' => 7,
	'252.0.0.0' => 6,
	'248.0.0.0' => 5,
	'240.0.0.0' => 4,
	'224.0.0.0' => 3,
	'192.0.0.0' => 2,
	'128.0.0.0' => 1,
	'0.0.0.0' => 0,
);

// 802.1Q deploy queue titles
$dqtitle = array
(
	'sync_aging' => 'Normal, aging',
	'resync_aging' => 'Version conflict, aging',
	'sync_ready' => 'Normal, ready for sync',
	'resync_ready' => 'Version conflict, ready for retry',
	'failed' => 'Failed',
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
}

function isInteger ($arg, $allow_zero = FALSE)
{
	if (! is_numeric ($arg))
		return FALSE;
	if (! $allow_zero and ! $arg)
		return FALSE;
	return TRUE;
}

// This function assures that specified argument was passed
// and is a non-empty string.
function assertStringArg ($argname, $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, '', 'parameter is missing');
	if (!is_string ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is not a string');
	if (!$ok_if_empty and !strlen ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is an empty string');
}

function assertBoolArg ($argname, $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, '', 'parameter is missing');
	if (!is_string ($_REQUEST[$argname]) or $_REQUEST[$argname] != 'on')
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is not a string');
	if (!$ok_if_empty and !strlen ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is an empty string');
}

// function returns IPv6Address object, null if arg is correct IPv4, or throws an exception
function assertIPArg ($argname, $ok_if_empty = FALSE)
{
	assertStringArg ($argname, $ok_if_empty);
	$ip = $_REQUEST[$argname];
	if (FALSE !== strpos ($ip, ':'))
	{
		$v6address = new IPv6Address;
		$result = $v6address->parse ($ip);
		$ret = $v6address;
	}
	else
	{
		$result = long2ip (ip2long ($ip)) === $ip;
		$ret = NULL;
	}
	if (! $result)
		throw new InvalidRequestArgException ($argname, $ip, 'parameter is not a valid IPv4 or IPv6 address');
	return $ret;
}

function assertIPv4Arg ($argname, $ok_if_empty = FALSE)
{
	assertStringArg ($argname, $ok_if_empty);
	if (strlen ($_REQUEST[$argname]) and long2ip (ip2long ($_REQUEST[$argname])) !== $_REQUEST[$argname])
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is not a valid ipv4 address');
}

// function returns IPv6Address object, or throws an exception
function assertIPv6Arg ($argname, $ok_if_empty = FALSE)
{
	assertStringArg ($argname, $ok_if_empty);
	$ipv6 = new IPv6Address;
	if (strlen ($_REQUEST[$argname]) and ! $ok_if_empty and ! $ipv6->parse ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is not a valid ipv6 address');
	return $ipv6;
}

function assertPCREArg ($argname)
{
	assertStringArg ($argname, TRUE); // empty pattern is Ok
	if (FALSE === preg_match ($_REQUEST[$argname], 'test'))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'PCRE validation failed');
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
		assertStringArg ($argname);
		break;
	case 'string0':
		assertStringArg ($argname, TRUE);
		break;
	case 'uint':
		assertUIntArg ($argname);
		break;
	case 'uint0':
		assertUIntArg ($argname, TRUE);
		break;
	case 'inet4':
		assertIPv4Arg ($argname);
		break;
	case 'inet6':
		assertIPv6Arg ($argname);
		break;
	case 'l2address':
		assertStringArg ($argname);
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
		break;
	case 'tag':
		assertStringArg ($argname);
		if (!validTagName ($sic[$argname]))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Invalid tag name');
		break;
	case 'pcre':
		assertPCREArg ($argname);
		break;
	case 'json':
		assertStringArg ($argname);
		if (NULL === json_decode ($sic[$argname], TRUE))
			throw new InvalidRequestArgException ($argname, '(omitted)', 'Invalid JSON code received from client');
		break;
	case 'array':
		if (! array_key_exists ($argname, $_REQUEST))
			throw new InvalidRequestArgException ($argname, '(missing argument)');
		if (! is_array ($_REQUEST[$argname]))
			throw new InvalidRequestArgException ($argname, '(omitted)', 'argument is not an array');
		break;
	case 'enum/attr_type':
		assertStringArg ($argname);
		if (!in_array ($sic[$argname], array ('uint', 'float', 'string', 'dict')))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		break;
	case 'enum/vlan_type':
		assertStringArg ($argname);
		// "Alien" type is not valid until the logic is fixed to implement it in full.
		if (!in_array ($sic[$argname], array ('ondemand', 'compulsory')))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		break;
	case 'enum/wdmstd':
		assertStringArg ($argname);
		global $wdm_packs;
		if (! array_key_exists ($sic[$argname], $wdm_packs))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		break;
	case 'enum/ipproto':
		assertStringArg ($argname);
		if (!in_array ($sic[$argname], array ('TCP', 'UDP')))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		break;
	case 'enum/inet4alloc':
	case 'enum/inet6alloc':
		assertStringArg ($argname);
		if (!in_array ($sic[$argname], array ('regular', 'shared', 'virtual', 'router')))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		break;
	case 'enum/dqcode':
		assertStringArg ($argname);
		global $dqtitle;
		if (! array_key_exists ($sic[$argname], $dqtitle))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		break;
	case 'iif':
		if (!array_key_exists ($sic[$argname], getPortIIFOptions()))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		break;
	case 'vlan':
	case 'vlan1':
		genericAssertion ($argname, 'uint');
		if ($argtype == 'vlan' and $sic[$argname] == VLAN_DFL_ID)
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'default VLAN cannot be changed');
		if ($sic[$argname] > VLAN_MAX_ID or $sic[$argname] < VLAN_MIN_ID)
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'out of valid range');
		break;
	case 'rackcode/expr':
		genericAssertion ($argname, 'string0');
		if ($sic[$argname] == '')
			return;
		$parse = spotPayload ($sic[$argname], 'SYNT_EXPR');
		if ($parse['result'] != 'ACK')
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'RackCode parsing error');
		break;
	default:
		throw new InvalidArgException ('argtype', $argtype); // comes not from user's input
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
	genericAssertion ($page[$pageno]['bypass'], $page[$pageno]['bypass_type']);
	return $sic[$page[$pageno]['bypass']];
}

// Objects of some types should be explicitly shown as
// anonymous (labelless). This function is a single place where the
// decision about displayed name is made.
function setDisplayedName (&$cell)
{
	if ($cell['name'] != '')
		$cell['dname'] = $cell['name'];
	else
	{
		$cell['atags'][] = array ('tag' => '$nameless');
		if (considerConfiguredConstraint ($cell, 'NAMEWARN_LISTSRC'))
			$cell['dname'] = 'ANONYMOUS ' . decodeObjectType ($cell['objtype_id'], 'o');
		else
			$cell['dname'] = '[' . decodeObjectType ($cell['objtype_id'], 'o') . ']';
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
	for ($unit_no = $rackData['height']; $unit_no > 0; $unit_no--)
		for ($locidx = 0; $locidx < 3; $locidx++)
			if
			(
				$rackData[$unit_no][$locidx]['state'] == 'T' and
				$rackData[$unit_no][$locidx]['object_id'] == $object_id
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

// netmask conversion from length to number
function binMaskFromDec ($maskL)
{
	$map_straight = array (
		0  => 0x00000000,
		1  => 0x80000000,
		2  => 0xc0000000,
		3  => 0xe0000000,
		4  => 0xf0000000,
		5  => 0xf8000000,
		6  => 0xfc000000,
		7  => 0xfe000000,
		8  => 0xff000000,
		9  => 0xff800000,
		10 => 0xffc00000,
		11 => 0xffe00000,
		12 => 0xfff00000,
		13 => 0xfff80000,
		14 => 0xfffc0000,
		15 => 0xfffe0000,
		16 => 0xffff0000,
		17 => 0xffff8000,
		18 => 0xffffc000,
		19 => 0xffffe000,
		20 => 0xfffff000,
		21 => 0xfffff800,
		22 => 0xfffffc00,
		23 => 0xfffffe00,
		24 => 0xffffff00,
		25 => 0xffffff80,
		26 => 0xffffffc0,
		27 => 0xffffffe0,
		28 => 0xfffffff0,
		29 => 0xfffffff8,
		30 => 0xfffffffc,
		31 => 0xfffffffe,
		32 => 0xffffffff,
	);
	return $map_straight[$maskL];
}

// complementary value
function binInvMaskFromDec ($maskL)
{
	$map_compl = array (
		0  => 0xffffffff,
		1  => 0x7fffffff,
		2  => 0x3fffffff,
		3  => 0x1fffffff,
		4  => 0x0fffffff,
		5  => 0x07ffffff,
		6  => 0x03ffffff,
		7  => 0x01ffffff,
		8  => 0x00ffffff,
		9  => 0x007fffff,
		10 => 0x003fffff,
		11 => 0x001fffff,
		12 => 0x000fffff,
		13 => 0x0007ffff,
		14 => 0x0003ffff,
		15 => 0x0001ffff,
		16 => 0x0000ffff,
		17 => 0x00007fff,
		18 => 0x00003fff,
		19 => 0x00001fff,
		20 => 0x00000fff,
		21 => 0x000007ff,
		22 => 0x000003ff,
		23 => 0x000001ff,
		24 => 0x000000ff,
		25 => 0x0000007f,
		26 => 0x0000003f,
		27 => 0x0000001f,
		28 => 0x0000000f,
		29 => 0x00000007,
		30 => 0x00000003,
		31 => 0x00000001,
		32 => 0x00000000,
	);
	return $map_compl[$maskL];
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
	foreach (getObjectIPv4Allocations ($object_id) as $dottedquad => $alloc)
		if ($alloc['type'] == 'regular')
			$regular[] = $dottedquad;
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

// Make sure the string is always wrapped with LF characters
function lf_wrap ($str)
{
	$ret = trim ($str, "\r\n");
	if (strlen ($ret))
		$ret .= "\n";
	return $ret;
}

// Adopted from Mantis BTS code.
function string_insert_hrefs ($s)
{
	if (getConfigVar ('DETECT_URLS') != 'yes')
		return $s;
	# Find any URL in a string and replace it by a clickable link
	$s = preg_replace( '/(([[:alpha:]][-+.[:alnum:]]*):\/\/(%[[:digit:]A-Fa-f]{2}|[-_.!~*\';\/?%^\\\\:@&={\|}+$#\(\),\[\][:alnum:]])+)/se',
		"'<a href=\"'.rtrim('\\1','.').'\">\\1</a> [<a href=\"'.rtrim('\\1','.').'\" target=\"_blank\">^</a>]'",
		$s);
	$s = preg_replace( '/\b' . email_regex_simple() . '\b/i',
		'<a href="mailto:\0">\0</a>',
		$s);
	return $s;
}

// Idem.
function email_regex_simple ()
{
	return "(([a-z0-9!#*+\/=?^_{|}~-]+(?:\.[a-z0-9!#*+\/=?^_{|}~-]+)*)" . # recipient
	"\@((?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?))"; # @domain
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
			if (count ($tmp) != 3)
				continue;
			$nports = $tmp[0];
			$port_type = $tmp[1];
			$format = $tmp[2];
			for ($i = 0; $i < $nports; $i++)
				$ret[] = array ('type' => $port_type, 'name' => @sprintf ($format, $i));
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

// Build a tree from the item list and return it. Input and output data is
// indexed by item id (nested items in output are recursively stored in 'kids'
// key, which is in turn indexed by id. Functions, which are ready to handle
// tree collapsion/expansion themselves, may request non-zero threshold value
// for smaller resulting tree.
function treeFromList (&$orig_nodelist, $threshold = 0, $return_main_payload = TRUE)
{
	$tree = array();
	$nodelist = $orig_nodelist;
	// Array equivalent of traceEntity() function.
	$trace = array();
	// set kidc and kids only once
	foreach (array_keys ($nodelist) as $nodeid)
	{
		$nodelist[$nodeid]['kidc'] = 0;
		$nodelist[$nodeid]['kids'] = array();
	}
	do
	{
		$nextpass = FALSE;
		foreach (array_keys ($nodelist) as $nodeid)
		{
			// When adding a node to the working tree, book another
			// iteration, because the new item could make a way for
			// others onto the tree. Also remove any item added from
			// the input list, so iteration base shrinks.
			// First check if we can assign directly.
			if ($nodelist[$nodeid]['parent_id'] == NULL)
			{
				$tree[$nodeid] = $nodelist[$nodeid];
				$trace[$nodeid] = array(); // Trace to root node is empty
				unset ($nodelist[$nodeid]);
				$nextpass = TRUE;
			}
			// Now look if it fits somewhere on already built tree.
			elseif (isset ($trace[$nodelist[$nodeid]['parent_id']]))
			{
				// Trace to a node is a trace to its parent plus parent id.
				$trace[$nodeid] = $trace[$nodelist[$nodeid]['parent_id']];
				$trace[$nodeid][] = $nodelist[$nodeid]['parent_id'];
				pokeNode ($tree, $trace[$nodeid], $nodeid, $nodelist[$nodeid], $threshold);
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

function redirectIfNecessary ()
{
	global
		$trigger,
		$pageno,
		$tabno;

	if
	(
		! isset ($_REQUEST['tab']) and
		isset ($_SESSION['RTLT'][$pageno]) and
		getConfigVar ('SHOW_LAST_TAB') == 'yes' and
		permitted ($pageno, $_SESSION['RTLT'][$pageno]['tabname']) and
		time() - $_SESSION['RTLT'][$pageno]['time'] <= TAB_REMEMBER_TIMEOUT
	)
		redirectUser ($pageno, $_SESSION['RTLT'][$pageno]['tabname']);

	// check if we accidentaly got on a dynamic tab that shouldn't be shown for this object
	if
	(
		isset ($trigger[$pageno][$tabno]) and
		!strlen (call_user_func ($trigger[$pageno][$tabno]))
	)
	{
		$_SESSION['RTLT'][$pageno]['dont_remember'] = 1;
		redirectUser ($pageno, 'default');
	}
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
		$target_realm = $etype_by_pageno[$pageno];
		assertUIntArg ($page[$pageno]['bypass']);
		$target_id = $_REQUEST[$page[$pageno]['bypass']];
		$target = spotEntity ($target_realm, $target_id);
		$target_given_tags = $target['etags'];
		if ($target['realm'] != 'user')
			$auto_tags = array_merge ($auto_tags, $target['atags']);
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
function getShrinkedTagTree($entity_list, $realm, $preselect) {
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
function shrinkSubtree($tree, $used_tags, $preselect, $realm) {
	$self = __FUNCTION__;
	
	foreach($tree as $i => &$item) {
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
	// if the page is submitted we get an andor value so we know they are trying to start a new filter or clearing the existing one.
	if(isset($_REQUEST['andor']))
	{
		$andor_used = TRUE;
		unset($_SESSION[$pageno]); // delete saved filter
	}
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

function redirectUser ($p, $t)
{
	global $page;
	$l = "index.php?page=${p}&tab=${t}";
	if (isset ($page[$p]['bypass']) and isset ($_REQUEST[$page[$p]['bypass']]))
		$l .= '&' . $page[$p]['bypass'] . '=' . $_REQUEST[$page[$p]['bypass']];
	if (isset ($page[$p]['bypass_tabs']))
		foreach ($page[$p]['bypass_tabs'] as $param_name)
			if (isset ($_REQUEST[$param_name]))
				$l .= '&' . urlencode ($param_name) . '=' . urlencode ($_REQUEST[$param_name]);
	header ("Location: " . $l);
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

// Perform substitutions and return resulting string
// used solely by buildLVSConfig()
function apply_macros ($macros, $subject, &$error_macro_stat)
{
	// clear all text before last %RESET% macro
	$reset_keyword = '%RESET%';
	$reset_position = mb_strpos($subject, $reset_keyword, 0);
	if ($reset_position === FALSE)
		$ret = $subject;
	else
		$ret = trim
		(
			mb_substr($subject, $reset_position + mb_strlen($reset_keyword)),
			"\n\r"
		);

	foreach ($macros as $search => $replace)
	{
		if (empty($replace))
		{
			$replace = "<span class=\"msg_error\">$search</span>";
			$count = 0;
			$ret = str_replace ($search, $replace, $ret, $count);
			if ($count)
			{
				if (array_key_exists($search, $error_macro_stat))
					$error_macro_stat[$search] += $count;
				else
					$error_macro_stat[$search] = $count;
			}
		}
		else
			$ret = str_replace ($search, $replace, $ret);
	}
	return $ret;
}

// throws RTBuildLVSConfigError exception if undefined macros found
function buildLVSConfig ($object_id)
{
	$oInfo = spotEntity ('object', $object_id);
	$defaults = getSLBDefaults (TRUE);
	$lbconfig = getSLBConfig ($object_id);
	if ($lbconfig === NULL)
	{
		showWarning ('getSLBConfig() failed');
		return;
	}
	$newconfig = "#\n#\n# This configuration has been generated automatically by RackTables\n";
	$newconfig .= "# for object_id == ${object_id}\n# object name: ${oInfo['name']}\n#\n#\n\n\n";
	
	$error_stat = array();
	foreach ($lbconfig as $vs_id => $vsinfo)
	{
		$newconfig .=  "########################################################\n" .
			"# VS (id == ${vs_id}): " . (!strlen ($vsinfo['vs_name']) ? 'NO NAME' : $vsinfo['vs_name']) . "\n" .
			"# RS pool (id == ${vsinfo['pool_id']}): " . (!strlen ($vsinfo['pool_name']) ? 'ANONYMOUS' : $vsinfo['pool_name']) . "\n" .
			"########################################################\n";
		# The order of inheritance is: VS -> LB -> pool [ -> RS ]
		$macros = array
		(
			'%VIP%' => $vsinfo['vip'],
			'%VPORT%' => $vsinfo['vport'],
			'%PROTO%' => $vsinfo['proto'],
			'%VNAME%' =>  $vsinfo['vs_name'],
			'%RSPOOLNAME%' => $vsinfo['pool_name'],
			'%PRIO%' => $vsinfo['prio']
		);
		$newconfig .=  "virtual_server ${vsinfo['vip']} ${vsinfo['vport']} {\n";
		$newconfig .=  "\tprotocol ${vsinfo['proto']}\n";
		$newconfig .= lf_wrap (apply_macros
		(
			$macros,
			lf_wrap ($defaults['vs']) .
			lf_wrap ($vsinfo['vs_vsconfig']) .
			lf_wrap ($vsinfo['lb_vsconfig']) .
			lf_wrap ($vsinfo['pool_vsconfig']),
			$error_stat
		));
		foreach ($vsinfo['rslist'] as $rs)
		{
			if (!strlen ($rs['rsport']))
				$rs['rsport'] = $vsinfo['vport'];
			$macros['%RSIP%'] = $rs['rsip'];
			$macros['%RSPORT%'] = $rs['rsport'];
			$newconfig .=  "\treal_server ${rs['rsip']} ${rs['rsport']} {\n";
			$newconfig .= lf_wrap (apply_macros
			(
				$macros,
				lf_wrap ($defaults['rs']) .
				lf_wrap ($vsinfo['vs_rsconfig']) .
				lf_wrap ($vsinfo['lb_rsconfig']) .
				lf_wrap ($vsinfo['pool_rsconfig']) .
				lf_wrap ($rs['rs_rsconfig']),
				$error_stat
			));
			$newconfig .=  "\t}\n";
		}
		$newconfig .=  "}\n\n\n";
	}
	if (! empty($error_stat))
	{
		$error_messages = array();
		foreach ($error_stat as $macro => $count)
			$error_messages[] = "Error: macro $macro can not be empty ($count occurences)";
		throw new RTBuildLVSConfigError($error_messages, $newconfig, $object_id);
	}
	
	// FIXME: deal somehow with Mac-styled text, the below replacement will screw it up
	return dos2unix ($newconfig);
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
		$nvirtloopback = ($refc['shared'] + $refc['virtual'] > 0) ? 1 : 0; // modulus of virtual + shared
		$nreserved = ($addrlist[$ip_bin]['reserved'] == 'yes') ? 1 : 0; // only one reservation is possible ever
		$nrealms = $nreserved + $nvirtloopback + $refc['regular'] + $refc['router']; // latter two are connected and router allocations
		
		if ($nrealms == 1)
			$addrlist[$ip_bin]['class'] = 'trbusy';
		elseif ($nrealms > 1)
			$addrlist[$ip_bin]['class'] = 'trerror';
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
					'addr' => $addr['ip']
				);
	return $ret;
}

// Assist in tag chain sorting.
function taginfoCmp ($tagA, $tagB)
{
	return $tagA['ci'] - $tagB['ci'];
}

// Compare networks. When sorting a tree, the records on the list will have
// distinct base IP addresses.
// "The comparison function must return an integer less than, equal to, or greater
// than zero if the first argument is considered to be respectively less than,
// equal to, or greater than the second." (c) PHP manual
function IPv4NetworkCmp ($netA, $netB)
{
	// On 64-bit systems this function can be reduced to just this:
	if (PHP_INT_SIZE == 8)
		return $netA['ip_bin'] - $netB['ip_bin'];
	// There's a problem just substracting one u32 integer from another,
	// because the result may happen big enough to become a negative i32
	// integer itself (PHP tries to cast everything it sees to signed int)
	// The comparison below must treat positive and negative values of both
	// arguments.
	// Equal values give instant decision regardless of their [equal] sign.
	if ($netA['ip_bin'] == $netB['ip_bin'])
		return 0;
	// Same-signed values compete arithmetically within one of i32 contiguous ranges:
	// 0x00000001~0x7fffffff 1~2147483647
	// 0 doesn't have any sign, and network 0.0.0.0 isn't allowed
	// 0x80000000~0xffffffff -2147483648~-1
	$signA = $netA['ip_bin'] / abs ($netA['ip_bin']);
	$signB = $netB['ip_bin'] / abs ($netB['ip_bin']);
	if ($signA == $signB)
	{
		if ($netA['ip_bin'] > $netB['ip_bin'])
			return 1;
		else
			return -1;
	}
	else // With only one of two values being negative, it... wins!
	{
		if ($netA['ip_bin'] < $netB['ip_bin'])
			return 1;
		else
			return -1;
	}
}

function IPv6NetworkCmp ($netA, $netB)
{
	return strcmp ($netA['ip_bin']->getBin(), $netB['ip_bin']->getBin());
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
	$worktree = array
	(
		'ip_bin' => $netdata['ip_bin'],
		'mask' => $netdata['mask']
	);
	foreach ($netdata['kids'] as $pfx)
		iptree_embed ($worktree, $pfx);
	$netdata['kids'] = iptree_construct ($worktree);
	$netdata['kidc'] = count ($netdata['kids']);
}

function ipv6tree_fill (&$netdata)
{
	if (!isset ($netdata['kids']) or !count ($netdata['kids']))
		return;
	// If we really have nested prefixes, they must fit into the tree.
	$worktree = array
	(
		'ip_bin' => $netdata['ip_bin'],
		'mask' => $netdata['mask']
	);
	foreach ($netdata['kids'] as $pfx)
		ipv6tree_embed ($worktree, $pfx);
	$netdata['kids'] = ipv6tree_construct ($worktree);
	$netdata['kidc'] = count ($netdata['kids']);
}

function iptree_construct ($node)
{
	$self = __FUNCTION__;

	if (!isset ($node['right']))
	{
		if (!isset ($node['ip']))
		{
			$node['ip'] = long2ip ($node['ip_bin']);
			$node['kids'] = array();
			$node['kidc'] = 0;
			$node['name'] = '';
		}
		return array ($node);
	}
	else
		return array_merge ($self ($node['left']), $self ($node['right']));
}

function ipv6tree_construct ($node)
{
	$self = __FUNCTION__;

	if (!isset ($node['right']))
	{
		if (!isset ($node['ip']))
		{
			$node['ip'] = $node['ip_bin']->format();
			$node['kids'] = array();
			$node['kidc'] = 0;
			$node['name'] = '';
		}
		return array ($node);
	}
	else
		return array_merge ($self ($node['left']), $self ($node['right']));
}

function iptree_embed (&$node, $pfx)
{
	$self = __FUNCTION__;

	// hit?
	if ($node['ip_bin'] == $pfx['ip_bin'] and $node['mask'] == $pfx['mask'])
	{
		$node = $pfx;
		return;
	}
	if ($node['mask'] == $pfx['mask'])
		throw new RackTablesError ('the recurring loop lost control', RackTablesError::INTERNAL);

	// split?
	if (!isset ($node['right']))
	{
		// Fill in db_first/db_last to make it possible to run scanIPv4Space() on the node.
		$node['left']['mask'] = $node['mask'] + 1;
		$node['left']['ip_bin'] = $node['ip_bin'];
		$node['left']['db_first'] = sprintf ('%u', $node['left']['ip_bin']);
		$node['left']['db_last'] = sprintf ('%u', $node['left']['ip_bin'] | binInvMaskFromDec ($node['left']['mask']));

		$node['right']['mask'] = $node['mask'] + 1;
		$node['right']['ip_bin'] = $node['ip_bin'] + binInvMaskFromDec ($node['mask'] + 1) + 1;
		$node['right']['db_first'] = sprintf ('%u', $node['right']['ip_bin']);
		$node['right']['db_last'] = sprintf ('%u', $node['right']['ip_bin'] | binInvMaskFromDec ($node['right']['mask']));
	}

	// repeat!
	if (($node['left']['ip_bin'] & binMaskFromDec ($node['left']['mask'])) == ($pfx['ip_bin'] & binMaskFromDec ($node['left']['mask'])))
		$self ($node['left'], $pfx);
	elseif (($node['right']['ip_bin'] & binMaskFromDec ($node['right']['mask'])) == ($pfx['ip_bin'] & binMaskFromDec ($node['left']['mask'])))
		$self ($node['right'], $pfx);
	else
		throw new RackTablesError ('cannot decide between left and right', RackTablesError::INTERNAL);
}

function ipv6tree_embed (&$node, $pfx)
{
	$self = __FUNCTION__;

	// hit?
	if ($node['ip_bin'] == $pfx['ip_bin'] and $node['mask'] == $pfx['mask'])
	{
		$node = $pfx;
		return;
	}
	if ($node['mask'] == $pfx['mask'])
		throw new RackTablesError ('the recurring loop lost control', RackTablesError::INTERNAL);

	// split?
	if (!isset ($node['right']))
	{
		$node['left']['mask'] = $node['mask'] + 1;
		$node['left']['ip_bin'] = $node['ip_bin'];
		$node['left']['db_first'] = $node['ip_bin']->get_first_subnet_address ($node['mask'] + 1);
		$node['left']['db_last'] = $node['ip_bin']->get_last_subnet_address ($node['mask'] + 1);

		$node['right']['mask'] = $node['mask'] + 1;
		$node['right']['ip_bin'] = $node['ip_bin']->get_last_subnet_address ($node['mask'] + 1)->next();
		$node['right']['db_first'] = $node['right']['ip_bin'];
		$node['right']['db_last'] = $node['right']['ip_bin']->get_last_subnet_address ($node['mask'] + 1);
	}

	// repeat!
	if ($node['left']['db_first'] == $pfx['ip_bin']->get_first_subnet_address ($node['left']['mask']))
		$self ($node['left'], $pfx);
	elseif ($node['right']['db_first'] == $pfx['ip_bin']->get_first_subnet_address ($node['left']['mask']))
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

function loadIPv4AddrList (&$netinfo)
{
	loadOwnIPv4Addresses ($netinfo);
	markupIPAddrList ($netinfo['addrlist']);
}

function countOwnIPv4Addresses (&$node)
{
	$node['addrt'] = 0;
	if (empty ($node['kids']))
		$node['addrt'] = binInvMaskFromDec ($node['mask']) + 1;
	else
		foreach ($node['kids'] as $nested)
			if (!isset ($nested['id'])) // spare
				$node['addrt'] += binInvMaskFromDec ($nested['mask']) + 1;
}

function nodeIsCollapsed ($node)
{
	return $node['symbol'] == 'node-collapsed';
}

// implies countOwnIPv4Addresses
function loadOwnIPv4Addresses (&$node)
{
	$toscan = array();
	$node['addrt'] = 0;
	if (!isset ($node['kids']) or !count ($node['kids']))
	{
		$toscan[] = array ('i32_first' => $node['db_first'], 'i32_last' => $node['db_last']);
		$node['addrt'] = $node['db_last'] - $node['db_first'] + 1;
	}
	else
	{
		$node['addrt'] = 0;
		foreach ($node['kids'] as $nested)
			if (!isset ($nested['id'])) // spare
			{
				$toscan[] = array ('i32_first' => $nested['db_first'], 'i32_last' => $nested['db_last']);
				$node['addrt'] += $nested['db_last'] - $nested['db_first'] + 1;
			}
	}
	$node['addrlist'] = scanIPv4Space ($toscan);
	$node['addrc'] = count ($node['addrlist']);
}

function loadIPv6AddrList (&$netinfo)
{
	loadOwnIPv6Addresses ($netinfo);
	markupIPAddrList ($netinfo['addrlist']);
}

function loadOwnIPv6Addresses (&$node)
{
	$toscan = array();
	$node['addrt'] = 0;
	if (empty ($node['kids']))
		$toscan[] = array ('first' => $node['ip_bin'], 'last' => $node['ip_bin']->get_last_subnet_address ($node['mask']));
	else
		foreach ($node['kids'] as $nested)
			if (!isset ($nested['id'])) // spare
				$toscan[] = array ('first' => $nested['ip_bin'], 'last' => $nested['ip_bin']->get_last_subnet_address ($nested['mask']));
	$node['addrlist'] = scanIPv6Space ($toscan);
	$node['addrc'] = count ($node['addrlist']);
}

function prepareIPv4Tree ($netlist, $expanded_id = 0)
{
	// treeFromList() requires parent_id to be correct for an item to get onto the tree,
	// so perform necessary pre-processing to make orphans belong to root. This trick
	// was earlier performed by getIPv4NetworkList().
	$netids = array_keys ($netlist);
	foreach ($netids as $cid)
		if (!in_array ($netlist[$cid]['parent_id'], $netids))
			$netlist[$cid]['parent_id'] = NULL;
	$tree = treeFromList ($netlist); // medium call
	sortTree ($tree, 'IPv4NetworkCmp');
	// complement the tree before markup to make the spare networks have "symbol" set
	treeApplyFunc ($tree, 'iptree_fill');
	iptree_markup_collapsion ($tree, getConfigVar ('TREE_THRESHOLD'), $expanded_id);
	// count addresses after the markup to skip computation for hidden tree nodes
	treeApplyFunc ($tree, 'countOwnIPv4Addresses', 'nodeIsCollapsed');
	return $tree;
}

function prepareIPv6Tree ($netlist, $expanded_id = 0)
{
	// treeFromList() requires parent_id to be correct for an item to get onto the tree,
	// so perform necessary pre-processing to make orphans belong to root. This trick
	// was earlier performed by getIPv4NetworkList().
	$netids = array_keys ($netlist);
	foreach ($netids as $cid)
		if (!in_array ($netlist[$cid]['parent_id'], $netids))
			$netlist[$cid]['parent_id'] = NULL;
	$tree = treeFromList ($netlist); // medium call
	sortTree ($tree, 'IPv6NetworkCmp');
	// complement the tree before markup to make the spare networks have "symbol" set
	treeApplyFunc ($tree, 'ipv6tree_fill');
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
		if (!$tree[$key]['kidc']) // terminal node
			$tree[$key]['symbol'] = 'spacer';
		elseif ($tree[$key]['kidc'] < $threshold)
			$tree[$key]['symbol'] = 'node-expanded-static';
		elseif ($here or $below)
			$tree[$key]['symbol'] = 'node-expanded';
		else
			$tree[$key]['symbol'] = 'node-collapsed';
		$ret = ($ret or $here or $below); // parentheses are necessary for this to be computed correctly
	}
	return $ret;
}

// Convert entity name to human-readable value
function formatEntityName ($name) {
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
function formatFileSize ($bytes) {
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
function convertToBytes ($value) {
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

function ip_quad2long ($ip)
{
      return sprintf("%u", ip2long($ip));
}

function ip_long2quad ($quad)
{
      return long2ip($quad);
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
	global $pageno, $tabno;
	$tmp = array();
	if (! array_key_exists ('page', $params))
		$params['page'] = $pageno;
	if (! array_key_exists ('tab', $params))
		$params['tab'] = $tabno;
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
	// Always keep "other" OPTGROUP at the SELECT bottom.
	$therest = array();
	foreach ($recordList as $dict_key => $dict_value)
		if (strpos ($dict_value, '%GSKIP%') !== FALSE)
		{
			$tmp = explode ('%GSKIP%', $dict_value, 2);
			$ret[$tmp[0]][$dict_key] = $tmp[1];
		}
		elseif (strpos ($dict_value, '%GPASS%') !== FALSE)
		{
			$tmp = explode ('%GPASS%', $dict_value, 2);
			$ret[$tmp[0]][$dict_key] = $tmp[1];
		}
		else
			$therest[$dict_key] = $dict_value;
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
	$ret['other'] = $therest;
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

function getIPv4VSOptions ()
{
	$ret = array();
	foreach (listCells ('ipv4vs') as $vsid => $vsinfo)
		$ret[$vsid] = $vsinfo['dname'] . (!strlen ($vsinfo['name']) ? '' : " (${vsinfo['name']})");
	return $ret;
}

function getIPv4RSPoolOptions ()
{
	$ret = array();
	foreach (listCells ('ipv4rspool') as $pool_id => $poolInfo)
		$ret[$pool_id] = $poolInfo['name'];
	return $ret;
}

function getVSTOptions()
{
	$ret = array();
	foreach (listCells ('vst') as $vst)
		$ret[$vst['id']] = niftyString ($vst['description'], 30, FALSE);
	return $ret;
}

# Return a 2-dimensional array in the format understood by getNiftySelect(),
# which would contain all VLANs of all VLAN domains except domains, which IDs
# are present in $except_domains argument.
function getAllVLANOptions ($except_domains = array())
{
	$ret = array();
	foreach (getVLANDomainStats() as $domain)
		if (! in_array ($domain['id'], $except_domains))
			foreach (getDomainVLANs ($domain['id']) as $vlan)
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
function formatVLANName ($vlaninfo, $context = 'markup long')
{
	switch ($context)
	{
	case 'option':
		$ret = $vlaninfo['vlan_id'];
		if ($vlaninfo['vlan_descr'] != '')
			$ret .= ' ' . niftyString ($vlaninfo['vlan_descr']);
		return $ret;
	case 'label':
		$ret = $vlaninfo['vlan_id'];
		if ($vlaninfo['vlan_descr'] != '')
			$ret .= ' <i>(' . niftyString ($vlaninfo['vlan_descr']) . ')</i>';
		return $ret;
	case 'plain long':
		$ret = 'VLAN' . $vlaninfo['vlan_id'];
		if ($vlaninfo['vlan_descr'] != '')
			$ret .= ' (' . niftyString ($vlaninfo['vlan_descr'], 20, FALSE) . ')';
		return $ret;
	case 'hyperlink':
		$ret = '<a href="';
		$ret .= makeHref (array ('page' => 'vlan', 'vlan_ck' => $vlaninfo['domain_id'] . '-' . $vlaninfo['vlan_id']));
		$ret .= '">' . formatVLANName ($vlaninfo, 'markup long') . '</a>';
		return $ret;
	case 'markup long':
	default:
		$ret = 'VLAN' . $vlaninfo['vlan_id'];
		$ret .= ' @' . niftyString ($vlaninfo['domain_descr']);
		if ($vlaninfo['vlan_descr'] != '')
			$ret .= ' <i>(' . niftyString ($vlaninfo['vlan_descr']) . ')</i>';
		return $ret;
	}
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
	$ifname = preg_replace ('@^[Pp]ort-channel(.+)$@', 'po\\1', $ifname);
	$ifname = preg_replace ('@^(?:XGigabitEthernet|XGE)(.+)$@', 'xg\\1', $ifname);
	$ifname = strtolower ($ifname);
	return $ifname;
}

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
	$ret[] = array();
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
	$crq = array();
	// Before removing each old VLAN as such it is necessary to unassign
	// ports from it (to remove VLAN from each ports' list of "allowed"
	// VLANs). This change in turn requires, that a port's "native"
	// VLAN isn't set to the one being removed from its "allowed" list.
	foreach ($ports_to_do as $port_name => $port)
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
					$crq[] = array
					(
						'opcode' => 'rem allowed',
						'port' => $port_name,
						'vlans' => $queue,
					);
			break;
		case 'access->access':
			if ($port['old_native'] and $port['old_native'] != $port['new_native'])
				$crq[] = array
				(
					'opcode' => 'unset access',
					'arg1' => $port_name,
					'arg2' => $port['old_native'],
				);
			break;
		case 'access->trunk':
			$crq[] = array
			(
				'opcode' => 'unset access',
				'arg1' => $port_name,
				'arg2' => $port['old_native'],
			);
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
					$crq[] = array
					(
						'opcode' => 'rem allowed',
						'port' => $port_name,
						'vlans' => $queue,
					);
			break;
		default:
			throw new InvalidArgException ('ports_to_do', '(hidden)', 'error in structure');
		}
	// Now it is safe to unconfigure VLANs, which still exist on device,
	// but are not present on the "new" list.
	// FIXME: put all IDs into one pseudo-command to make it easier
	// for translators to create/destroy VLANs in batches, where
	// target platform allows them to do.
	foreach (array_diff ($old_managed_vlans, $new_managed_vlans) as $vlan_id)
		$crq[] = array
		(
			'opcode' => 'destroy VLAN',
			'arg1' => $vlan_id,
		);
	// Configure VLANs, which must be present on the device, but are not yet.
	foreach (array_diff ($new_managed_vlans, $old_managed_vlans) as $vlan_id)
		$crq[] = array
		(
			'opcode' => 'create VLAN',
			'arg1' => $vlan_id,
		);
	// Now, when all new VLANs are created (queued), it is safe to assign (queue)
	// ports to the new VLANs.
	foreach ($ports_to_do as $port_name => $port)
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
			$crq[] = array
			(
				'opcode' => 'set mode',
				'arg1' => $port_name,
				'arg2' => $port['new_mode'],
			);
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
				'opcode' => 'set mode',
				'arg1' => $port_name,
				'arg2' => $port['new_mode'],
			);
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
		foreach ($cell[$family] as $ip => $allocation)
		{
			if ($family == 'ipv6')
				$ip = new IPv6Address ($ip);
			if ($net_id = ($family == 'ipv6' ? getIPv6AddressNetworkId ($ip) : getIPv4AddressNetworkId ($ip)))
			{
				if (! isset($seen_nets[$net_id]))
					$seen_nets[$net_id]	= 1;
				else
					continue;
				$net = spotEntity ("${family}net", $net_id);
				amplifyCell ($net);
				foreach ($net['8021q'] as $vlan)
					if (! isset ($employed[$vlan['vlan_id']]))
						$employed[$vlan['vlan_id']] = 1;
			}
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
				if (!in_array ($vlan_id, $employed))
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
		return 0;
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
	{
		// saved configuration has changed (either "user" ports have changed,
		// or uplinks, or both), so bump revision number up)
		usePreparedExecuteBlade
		(
			'UPDATE VLANSwitch SET mutex_rev=mutex_rev+1, last_change=NOW(), out_of_sync="yes" WHERE object_id=?',
			array ($vswitch['object_id'])
		);
	}
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
				$vlan_names = isset ($R['vlannames']) ? $R['vlannames'] : array();
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
			}
		}
	}
	$dbxlink->commit();
	// start downlink work only after unlocking current object to make deadlocks less likely to happen
	// TODO: only process changed uplink ports
	if ($nsaved_uplinks)
		initiateUplinksReverb ($vswitch['object_id'], $new_uplinks);
	return $nsaved + $npushed + $nsaved_uplinks;
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
		usePreparedExecuteBlade
		(
			'UPDATE VLANSwitch SET mutex_rev=mutex_rev+1, last_change=NOW(), out_of_sync="yes" WHERE object_id=?',
			array ($vswitch['object_id'])
		);
	$dbxlink->commit();
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
	foreach ($upstream_config as $remote_object_id => $remote_ports)
		saveDownlinksReverb ($remote_object_id, $remote_ports);
}

// checks if the desired config of all uplink/downlink ports of that switch, and
// his neighbors, equals to the recalculated config. If not, and $check_only is FALSE,
// sets the recalculated configs as desired and puts switches into out-of-sync state.
// Returns an array with object_id as key and portname subkey
function recalc8021QPorts ($switch_id, $check_only = FALSE)
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
	if (! $vswitch) {
		return $ret;
	}
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
				if ($changed = queueChangesToSwitch ($portinfo['remote_object_id'], array ($remote_pn => $remote_port_order), $remote_before, $check_only))
				{
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
	if ($changed = queueChangesToSwitch ($switch_id, $order, $before, $check_only))
	{
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
				if ($changed = queueChangesToSwitch ($portinfo['remote_object_id'], $new_order, $remote_before, $check_only))
				{
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

// does upd8021QPort on any port from $order array which is not equal to the corresponding $before port.
// returns changed port count.
// If $check_only is TRUE, return port count that could be changed unless $check_only, does nothing to DB.
function queueChangesToSwitch ($switch_id, $order, $before, $check_only = FALSE)
{
	global $script_mode;
	$ret = array();
	$nsaved = 0;
	foreach ($order as $portname => $portorder)
		if (! same8021QConfigs ($portorder, $before[$portname]))
		{
			if ($script_mode)
			{
				$object = spotEntity ('object', $switch_id);
				print $object['name'] . " $portname: " . serializeVLANPack ($before[$portname]) . ' -> ' . serializeVLANPack ($portorder) . "\n";
			}
			if (! $check_only)
			{
				upd8021QPort ('desired', $switch_id, $portname, $portorder);
				$nsaved++;
			}
		}
	
	if (! $check_only && $nsaved)
		usePreparedExecuteBlade
		(
			'UPDATE VLANSwitch SET mutex_rev=mutex_rev+1, last_change=NOW(), out_of_sync="yes" WHERE object_id=?',
			array ($switch_id)
		);
	return $nsaved;
}

function detectVLANSwitchQueue ($vswitch)
{
	if ($vswitch['out_of_sync'] == 'no')
		return 'done';
	switch ($vswitch['last_errno'])
	{
	case E_8021Q_NOERROR:
		if ($vswitch['last_change_age_seconds'] > getConfigVar ('8021Q_DEPLOY_MAXAGE'))
			return 'sync_ready';
		elseif ($vswitch['last_change_age_seconds'] < getConfigVar ('8021Q_DEPLOY_MINAGE'))
			return 'sync_aging';
		else
			return 'sync_ready';
	case E_8021Q_VERSION_CONFLICT:
		if ($vswitch['last_error_age_seconds'] < getConfigVar ('8021Q_DEPLOY_RETRY'))
			return 'resync_aging';
		else
			return 'resync_ready';
	case E_8021Q_PULL_REMOTE_ERROR:
	case E_8021Q_PUSH_REMOTE_ERROR:
		return 'failed';
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
	$ipv6 = new IPv6Address;

	if (preg_match (RE_IP4_ADDR, $terms))
	// Search for IPv4 address.
	{
		if ($net_id = getIPv4AddressNetworkId ($terms))
			$summary['ipv4addressbydq'][$terms] = array ('net_id' => $net_id, 'ip' => $terms);
		
	}
	elseif ($ipv6->parse ($terms))
	// Search for IPv6 address
	{
		if ($net_id = getIPv6AddressNetworkId ($ipv6))
			$summary['ipv6addressbydq'][$net_id] = array ('net_id' => $net_id, 'ip' => $ipv6);
	}
	elseif (preg_match (RE_IP4_NET, $terms))
	// Search for IPv4 network
	{
		list ($base, $len) = explode ('/', $terms);
		if (NULL !== ($net_id = getIPv4AddressNetworkId ($base, $len + 1)))
			$summary['ipv4network'][$net_id] = spotEntity('ipv4net', $net_id);
	}
	elseif (preg_match ('@(.*)/(\d+)$@', $terms, $matches) && $ipv6->parse ($matches[1]))
	// Search for IPv6 network
	{
		if (NULL !== ($net_id = getIPv6AddressNetworkId ($ipv6, $matches[2] + 1)))
			$summary['ipv6network'][$net_id] = spotEntity('ipv6net', $net_id);
	}
	elseif ($found_id = searchByMgmtHostname ($terms))
	{
		$summary['object'][$found_id] = array
		(
			'id' => $found_id,
			'method' => 'fqdn',
		);
	}
	else
	// Search for objects, addresses, networks, virtual services and RS pools by their description.
	{
		$summary['object'] = getObjectSearchResults ($terms);
		$summary['ipv4addressbydescr'] = getIPv4AddressSearchResult ($terms);
		$summary['ipv6addressbydescr'] = getIPv6AddressSearchResult ($terms);
		$summary['ipv4network'] = getIPv4PrefixSearchResult ($terms);
		$summary['ipv6network'] = getIPv6PrefixSearchResult ($terms);
		$summary['ipv4rspool'] = getIPv4RSPoolSearchResult ($terms);
		$summary['ipv4vs'] = getIPv4VServiceSearchResult ($terms);
		$summary['user'] = getAccountSearchResult ($terms);
		$summary['file'] = getFileSearchResult ($terms);
		$summary['rack'] = getRackSearchResult ($terms);
		$summary['vlan'] = getVLANSearchResult ($terms);
	}
	# Filter search results in a way in some realms to omit records, which the
	# user would not be able to browse anyway.
	if (isset ($summary['object']))
		foreach ($summary['object'] as $key => $record)
			if (! isolatedPermission ('object', 'default', spotEntity ('object', $record['id'])))
				unset ($summary['object'][$key]);
	if (isset ($summary['ipv4network']))
		foreach ($summary['ipv4network'] as $key => $netinfo)
			if (! isolatedPermission ('ipv4net', 'default', $netinfo))
				unset ($summary['ipv4network'][$key]);
	if (isset ($summary['ipv6network']))
		foreach ($summary['ipv6network'] as $key => $netinfo)
			if (! isolatedPermission ('ipv6net', 'default', $netinfo))
				unset ($summary['ipv6network'][$key]);
	if (isset ($summary['file']))
		foreach ($summary['file'] as $key => $fileinfo)
			if (! isolatedPermission ('file', 'default', $fileinfo))
				unset ($summary['file'][$key]);

	// clear empty search result realms
	foreach ($summary as $key => $data)
		if (! count ($data))
			unset ($summary[$key]);
	return $summary;
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
function showError   ($message, $option = '') { setMessage ('error',   $message, $option == 'inline'); }
function showWarning ($message, $option = '') { setMessage ('warning', $message, $option == 'inline'); }
function showSuccess ($message, $option = '') { setMessage ('success', $message, $option == 'inline'); }
function showNotice  ($message, $option = '') { setMessage ('neutral', $message, $option == 'inline'); }

// do not call this directly, use showError and its siblings instead
// $type could be 'error', 'warning', 'success' or 'neutral'
function setMessage ($type, $message, $direct_rendering)
{
	global $script_mode;
	if ($direct_rendering)
		echo '<div class="msg_' . $type . '">' . $message . '</div>';
	elseif (isset ($script_mode) and $script_mode and ($type == 'warning' or $type == 'error'))
		file_put_contents ('php://stderr', strtoupper ($type) . ': ' . $message . "\n");
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
	$line = array ('c' => $code);
	if (! empty ($args))
		$line['a'] = $args;
	if (! isset ($_SESSION['log']))
		$_SESSION['log'] = array();
	$_SESSION['log'][] = $line;
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
	$result = 0;
	if (isset ($_SESSION['log']))
		foreach ($_SESSION['log'] as $msg)
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

function loadConfigDefaults() {
	global $configCache;
	$configCache = loadConfigCache();
	if (!count ($configCache))
		throw new RackTablesError ('Failed to load configuration from the database.', RackTablesError::INTERNAL);
	foreach ($configCache as $varname => &$row) {
		$row['is_altered'] = 'no';
		if ($row['vartype'] == 'uint') $row['varvalue'] = 0 + $row['varvalue'];
		$row['defaultvalue'] = $row['varvalue'];
	}
}

function alterConfigWithUserPreferences() {
	global $configCache;
	global $userConfigCache;
	global $remote_username;
	$userConfigCache = loadUserConfigCache($remote_username);
	foreach($userConfigCache as $key => $row) {
		if ($configCache[$key]['is_userdefined'] == 'yes') {
			$configCache[$key]['varvalue'] = $row['varvalue'];
			$configCache[$key]['is_altered'] = 'yes';
		}
	}
}

// Returns true if varname has a different value or varname is new
function isConfigVarChanged($varname, $varvalue) {
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

?>
