<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*
*
*  This file is a library of computational functions for RackTables.
*
*/

defineIfNotDefined ('TAGNAME_REGEXP', '/^[\p{L}0-9-]( ?([._~+%-] ?)?[\p{L}0-9:])*(%|\+)?$/u');
defineIfNotDefined ('AUTOTAGNAME_REGEXP', '/^\$[\p{L}0-9-]( ?([._~+%-] ?)?[\p{L}0-9:])*(%|\+)?$/u');

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

$message_buffering = FALSE;

define ('CHAP_OBJTYPE', 1);
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
	'ipvs' => 'ipvs',
	'object' => 'object',
	'rack' => 'rack',
	'row' => 'row',
	'location' => 'location',
	'user' => 'user',
	'file' => 'file',
	'vst' => 'vst',
);
$pageno_by_etype = array_flip ($etype_by_pageno);

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

// Default input for renderExpirations(), can be overridden in local plugins.
$expirations = array();
$expirations[21] = array
(
	array ('from' => -365, 'to' => 0, 'class' => 'has_problems_', 'title' => 'has expired within last year'),
	array ('from' => 0, 'to' => 30, 'class' => 'row_', 'title' => 'expires within 30 days'),
	array ('from' => 30, 'to' => 60, 'class' => 'row_', 'title' => 'expires within 60 days'),
	array ('from' => 60, 'to' => 90, 'class' => 'row_', 'title' => 'expires within 90 days'),
);
$expirations[22] = $expirations[21];
$expirations[24] = $expirations[21];

$natv4_proto = array ('TCP' => 'TCP', 'UDP' => 'UDP', 'ALL' => 'ALL');

$log_messages = array(); // messages waiting for displaying

function defineIfNotDefined ($constant, $value, $case_insensitive = FALSE)
{
	if (defined ($constant) === FALSE)
		define ($constant, $value, $case_insensitive);
}

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
	if (! $allow_zero && $_REQUEST[$argname] == 0)
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is zero');
	return $_REQUEST[$argname];
}

function isInteger ($arg, $allow_zero = FALSE)
{
	return is_numeric ($arg) && ($allow_zero || $arg != 0);
}

# Make sure the arg is a parsable date, return its UNIX timestamp equivalent
# (or empty string for empty input, when allowed).
#
# FIXME: This function should be removed for the reasons below:
# 1. Its naming is wrong as it would accept any argument value that is valid
#    per DATETIME_FORMAT configuration variable, which by default is set to
#    date only but can include time if necessary.
# 2. It converts the target value from a string to a UNIX timestamp, which is
#    not the semantics of similar functions.
# 3. It is not used anywhere in the code anymore.
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
		throw $e->newIRAE ($argname);
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
	if (! $ok_if_empty && $_REQUEST[$argname] == '')
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is an empty string');
	return $sic[$argname];
}

function assertBoolArg ($argname, $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, '', 'parameter is missing');
	if (! is_string ($_REQUEST[$argname]) || $_REQUEST[$argname] != 'on')
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is not a string');
	if (! $ok_if_empty && $_REQUEST[$argname] == '')
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
		throw $e->newIRAE ($argname);
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
		throw $e->newIRAE ($argname);
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
		throw $e->newIRAE ($argname);
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
	return isset ($arg) && FALSE !== @preg_match ($arg, 'test');
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
	case 'uint0':
		return assertUIntArg ($argname, TRUE);
	case 'decimal0':
		if ('' == assertStringArg ($argname, TRUE))
			return '';
		// fall through
	case 'decimal':
		if (! preg_match ('/^\d+(\.\d+)?$/', assertStringArg ($argname)))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'format error');
		return $sic[$argname];
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
	case 'array0':
		if (! array_key_exists ($argname, $_REQUEST))
			return array();
		if (! is_array ($_REQUEST[$argname]))
			throw new InvalidRequestArgException ($argname, '(omitted)', 'argument is not an array');
		return $_REQUEST[$argname];
	case 'datetime0':
		if ('' == assertStringArg ($argname, TRUE))
			return '';
		// fall through
	case 'datetime':
		$argvalue = assertStringArg ($argname);
		try
		{
			timestampFromDatetimestr ($argvalue); // discard the result on success
		}
		catch (InvalidArgException $iae)
		{
			throw $iae->newIRAE ($argname);
		}
		return $argvalue;
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
	case 'enum/natv4proto':
		assertStringArg ($argname);
		global $natv4_proto;
		if (! array_key_exists ($sic[$argname], $natv4_proto))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/alloc_type':
		assertStringArg ($argname);
		if (!in_array ($sic[$argname], array ('regular', 'shared', 'virtual', 'router', 'point2point')))
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
	// 'vlan' -- any valid VLAN ID except the default
	// 'vlan1' -- any valid VLAN ID including the default
	case 'vlan':
	case 'vlan1':
		assertUIntArg ($argname);
		if ($argtype == 'vlan' && $sic[$argname] == VLAN_DFL_ID)
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'default VLAN not allowed');
		if ($sic[$argname] > VLAN_MAX_ID || $sic[$argname] < VLAN_MIN_ID)
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'not a valid VLAN ID');
		return $sic[$argname];
	case 'uint-vlan':
	case 'uint-vlan1':
		if (! preg_match ('/^([1-9][0-9]*)-([1-9][0-9]*)$/', assertStringArg ($argname), $m))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'format error');
		if ($argtype == 'uint-vlan' && $m[2] == VLAN_DFL_ID)
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'default VLAN not allowed');
		if ($m[2] > VLAN_MAX_ID || $m[2] < VLAN_MIN_ID)
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'not a valid VLAN ID');
		return $sic[$argname];
	case 'rackcode/expr':
		if ('' == assertStringArg ($argname, TRUE))
			return array();
		if (! $expr = compileExpression ($sic[$argname]))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'not a valid RackCode expression');
		return $expr;
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
// There is at least one bit of code that depends on the NULL return value
// (although it does not explicitly check for it), it is the "interface" case
// in index.php, which makes an unconditional call to here. Changing this
// function to throw an exception instead will require changing at least
// that code too.
function getBypassValue()
{
	global $page, $pageno;
	if (!array_key_exists ('bypass', $page[$pageno]))
		return NULL;
	if (!array_key_exists ('bypass_type', $page[$pageno]))
		throw new RackTablesError ("Internal structure error at node '${pageno}' (bypass_type is not set)", RackTablesError::INTERNAL);
	return genericAssertion ($page[$pageno]['bypass'], $page[$pageno]['bypass_type']);
}

// fills $args array with the bypass values of specified $pageno that are provided in $_REQUEST
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
function formatObjectDisplayedName ($name, $objtype_id)
{
	return ($name != '') ? $name : sprintf ('[%s]', decodeObjectType ($objtype_id));
}

// Set the dname attribute within a cell
function setDisplayedName (&$cell)
{
	if ($cell['realm'] == 'object')
	{
		$cell['dname'] = formatObjectDisplayedName ($cell['name'], $cell['objtype_id']);
		// If the object has a container, set its dname as well
		if ($cell['container_id'])
			$cell['container_dname'] = formatObjectDisplayedName ($cell['container_name'], $cell['container_objtype_id']);
	}
	elseif ($cell['realm'] == 'ipv4vs')
		if ($cell['proto'] == 'MARK')
			$cell['dname'] = 'fwmark: ' . implode ('', unpack ('N', substr ($cell['vip_bin'], 0, 4)));
		else
			$cell['dname'] = $cell['vip'] . ':' . $cell['vport'] . '/' . $cell['proto'];
}

// This function finds height of solid rectangle of atoms that are all
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
				if
				(
					isset ($rackData[$startRow - $height][$locidx]['skipped']) ||
					isset ($rackData[$startRow - $height][$locidx]['rowspan']) ||
					isset ($rackData[$startRow - $height][$locidx]['colspan']) ||
					$rackData[$startRow - $height][$locidx]['state'] != 'T'
				)
					break 2;
				if ($object_id == 0)
					$object_id = $rackData[$startRow - $height][$locidx]['object_id'];
				if ($object_id != $rackData[$startRow - $height][$locidx]['object_id'])
					break 2;
			}
		}
		// If the first row can't offer anything, bail out.
		if ($height == 0 && $object_id == 0)
			break;
		$height++;
	}
	while ($startRow - $height > 0);
	return $height;
}

// This function marks atoms to be avoided by rectHeight() and assigns rowspan/colspan
// attributes.
function markSpan (&$rackData, $startRow, $maxheight, $template_idx)
{
	global $template, $templateWidth;
	$colspan = 0;
	for ($height = 0; $height < $maxheight; $height++)
		for ($locidx = 0; $locidx < 3; $locidx++)
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

// check permissions for rack modification
function rackModificationPermitted ($rackData, $op, $with_context=TRUE)
{
	$op_annex = array (array ('tag' => '$op_'.$op), array ('tag' => '$any_op'));
	$rack_op_annex = array_merge ($rackData['etags'], $rackData['itags'], $rackData['atags'], $op_annex);
	$context = !$with_context || permitted (NULL, NULL, NULL, $op_annex);
	return $context && permitted (NULL, NULL, NULL, $rack_op_annex);
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

// This function highlights specified object by amending the
// 'hl' suffix of the class name.
function highlightObject (&$rackData, $object_id)
{
	// Also highlight parent objects
	$object = spotEntity ('object', $object_id);
	$parents = reindexById (getParents ($object, 'object'));

	for ($unit_no = $rackData['height']; $unit_no > 0; $unit_no--)
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			$atom = &$rackData[$unit_no][$locidx];
			if
			(
				$atom['state'] == 'T' &&
				($atom['object_id'] == $object_id || isset ($parents[$atom['object_id']]))
			)
				$atom['hl'] = 'h' . $atom['hl'];
		}
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
			if ($rackData[$unit_no][$locidx]['enabled'])
				$rackData[$unit_no][$locidx]['checked'] =
					isCheckSet ("atom_${rack_id}_${unit_no}_${locidx}") ? ' checked' : '';
}

// wrapper around ip4_mask and ip6_mask
// netmask conversion from length to binary string
// v4/v6 mode is toggled by $is_ipv6 parameter
// Throws exception if $prefix_len is invalid
function ip_mask ($prefix_len, $is_ipv6)
{
	return $is_ipv6 ? ip6_mask ($prefix_len) : ip4_mask ($prefix_len);
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

	if ($prefix_len >= 0 && $prefix_len <= 32)
		return $mask[$prefix_len];
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

	if ($prefix_len >= 0 && $prefix_len <= 128)
		return $mask[$prefix_len];
	throw new InvalidArgException ('prefix_len', $prefix_len);
}

// Return a uniformly (010203040506 or 0102030405060708) formatted address, if it is present
// in the provided string, an empty string for an empty string or raise an exception.
function l2addressForDatabase ($string)
{
	$string = strtoupper (trim ($string));
	$ret = '';
	switch (TRUE)
	{
		case ($string == '' || preg_match (RE_L2_SOLID, $string) || preg_match (RE_L2_WWN_SOLID, $string)):
			$ret = $string;
			break;
		case (preg_match (RE_L2_IFCFG, $string) || preg_match (RE_L2_WWN_COLON, $string)):
			// reformat output of SunOS ifconfig
			$ret = '';
			foreach (explode (':', $string) as $byte)
				$ret .= (strlen ($byte) == 1 ? '0' : '') . $byte;
			$ret = $ret;
			break;
		case (preg_match (RE_L2_CISCO, $string)):
			$ret = str_replace ('.', '', $string);
			break;
		case (preg_match (RE_L2_HUAWEI, $string)):
			$ret = str_replace ('-', '', $string);
			break;
		case (preg_match (RE_L2_IPCFG, $string) || preg_match (RE_L2_WWN_HYPHEN, $string)):
			$ret = str_replace ('-', '', $string);
			break;
		default:
			throw new InvalidArgException ('string', $string, 'malformed MAC/WWN address');
	}
	// some switches provide this fake address through SNMP. Store it as NULL to allow multiple copies
	if ($ret === '000000000000')
		$ret = '';
	return $ret;
}

function l2addressFromDatabase ($string)
{
	switch (strlen ($string))
	{
		case 12: // Ethernet
		case 16: // FireWire/Fibre Channel
			return implode (':', str_split ($string, 2));
		default:
			return $string;
	}
}

// DEPRECATED, remove in 0.21.0
function getPrevIDforRack ($row_id, $rack_id)
{
	$n = getRackNeighbors ($row_id, $rack_id);
	return $n['prev'];
}

// DEPRECATED, remove in 0.21.0
function getNextIDforRack ($row_id, $rack_id)
{
	$n = getRackNeighbors ($row_id, $rack_id);
	return $n['next'];
}

function getRackNeighbors ($row_id, $rack_id)
{
	$ret = array ('prev' => NULL, 'next' => NULL);
	$ids = selectRackOrder ($row_id);
	$index = array_search ($rack_id, $ids);
	if ($index !== FALSE && $index > 0)
		$ret['prev'] = $ids[$index - 1];
	if ($index !== FALSE && $index + 1 < count ($ids))
		$ret['next'] = $ids[$index + 1];
	return $ret;
}

// Return a list of rack IDs that are P or less positions
// far from the given rack in its row.
function getProximateRacks ($rack_id, $proximity = 0)
{
	$ret = array ($rack_id);
	if ($proximity > 0)
	{
		$rack = spotEntity ('rack', $rack_id);
		$rackList = selectRackOrder ($rack['row_id']);
		$cur_item = array_search ($rack_id, $rackList);
		if (FALSE !== $cur_item)
		{
			if ($todo = min ($cur_item, $proximity))
				$ret = array_merge ($ret, array_slice ($rackList, $cur_item - $todo, $todo));
			if ($todo = min (count ($rackList) - 1 - $cur_item, $proximity))
				$ret = array_merge ($ret, array_slice ($rackList, $cur_item + 1, $todo));
		}
	}
	return $ret;
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
		if (is_numeric($ar[$i]) && is_numeric($br[$i]))
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
		if ($record['id'] == 3 && $record['value'] != '') // FQDN
			return array ($record['value']);
	$regular = array();
	foreach (getObjectIPv4AllocationList ($object_id) as $ip_bin => $alloc)
		if ($alloc['type'] == 'regular')
			$regular[] = ip4_format ($ip_bin);
	// FIXME: add IPv6 allocations to this list
	if (!count ($regular) && $fallback != '')
		return array ($fallback);
	return $regular;
}

// Split object's FQDN (or the common name if FQDN is not set) into the
// hostname and domain name in Munin convention (using the first period as the
// separator), and return the pair. Throw an exception on error.
function getMuninNameAndDomain ($object_id)
{
	$o = spotEntity ('object', $object_id);
	$hd = $o['name'];
	// FQDN overrides the common name for Munin purposes.
	$attrs = getAttrValues ($object_id);
	if (array_key_exists (3, $attrs) && $attrs[3]['value'] != '')
		$hd = $attrs[3]['value'];
	if (2 != count ($ret = preg_split ('/\./', $hd, 2)))
		throw new InvalidArgException ('object_id', $object_id, 'the name is not in the host.do.ma.in format');
	return $ret;
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
	return preg_replace ('/^.+%GSKIP%/', '',
		preg_replace ('/^(.+)%GPASS%/', '\\1 ',
			preg_replace ('/%L\d+,\d+(H|V|)%/', '', $line)));
}

// extract the layout information from the %L...% marker in the dictionary info
// This is somewhat similar to the %GPASS %GSKIP
function extractLayout (&$record)
{
	if (preg_match ('/%L(\d+),(\d+)(H|V|)%/', $record['value'], $matches))
	{
		$record['rows'] = $matches[1];
		$record['cols'] = $matches[2];
		$record['layout'] = $matches[3];
		if ($record['layout'] == '')
			$record['layout'] = ($record['cols'] >= 4) ? 'V' : 'H';
	}
}

// rackspace usage for a single rack
// (T + W + U) / (height * 3 - A)
function getRSUforRack ($data)
{
	$counter = array ('A' => 0, 'U' => 0, 'T' => 0, 'W' => 0, 'F' => 0);
	for ($unit_no = $data['height']; $unit_no > 0; $unit_no--)
		for ($locidx = 0; $locidx < 3; $locidx++)
			$counter[$data[$unit_no][$locidx]['state']]++;
	return ($counter['T'] + $counter['W'] + $counter['U']) /
		($counter['T'] + $counter['W'] + $counter['U'] + $counter['F']);
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
	return ($counter['T'] + $counter['W'] + $counter['U']) /
		($counter['T'] + $counter['W'] + $counter['U'] + $counter['F']);
}

function string_insert_hrefs_callback ($m)
{
	$t_url_href    = 'href="' . rtrim($m[1], '.') . '"';
	$s_url_replace = "<a ${t_url_href}>$m[1]</a> [<a ${t_url_href} target=\"_blank\">^</a>]";
	return $s_url_replace;
}

# Detect URLs and email addresses in the string and replace them with href anchors
# (adopted from MantisBT, core/string_api.php:string_insert_hrefs).
function string_insert_hrefs ($p_string)
{
	static $s_url_regex = null;
	static $s_url_replace = null;
	static $s_email_regex = null;
	static $s_anchor_regex = '/(<a[^>]*>.*?<\/a>)/is';

	if (getConfigVar ('DETECT_URLS') != 'yes')
		return $p_string;

	# Initialize static variables
	if (is_null ($s_url_regex))
	{
		# URL regex
		$t_url_protocol = '(?:[[:alpha:]][-+.[:alnum:]]*):\/\/';

		# %2A notation in url's
		$t_url_hex = '%[[:digit:]A-Fa-f]{2}';

		# valid set of characters that may occur in url scheme. Note: - should be first (A-F != -AF).
		$t_url_valid_chars       = '-_.,!~*\';\/?%^\\\\:@&={\|}+$#[:alnum:]\pL';
		$t_url_chars             = "(?:${t_url_hex}|[${t_url_valid_chars}\(\)\[\]])";
		$t_url_chars2            = "(?:${t_url_hex}|[${t_url_valid_chars}])";
		$t_url_chars_in_brackets = "(?:${t_url_hex}|[${t_url_valid_chars}\(\)])";
		$t_url_chars_in_parens   = "(?:${t_url_hex}|[${t_url_valid_chars}\[\]])";

		$t_url_part1 = "${t_url_chars}";
		$t_url_part2 = "(?:\(${t_url_chars_in_parens}*\)|\[${t_url_chars_in_brackets}*\]|${t_url_chars2})";

		$s_url_regex = "/(${t_url_protocol}(${t_url_part1}*?${t_url_part2}+))/su";

		# URL replacement
		$t_url_href    = "href=\"'.rtrim('\\1','.').'\"";
		$s_url_replace = "'<a ${t_url_href}>\\1</a> [<a ${t_url_href} target=\"_blank\">^</a>]'";

		# e-mail regex
		$s_email_regex = substr_replace (email_regex_simple(), '(?:mailto:)?', 1, 0);
	}

	# Find any URL in a string and replace it by a clickable link
	$p_string = preg_replace_callback
	(
		$s_url_regex,
		'string_insert_hrefs_callback',
		$p_string
	);

	# Find any email addresses in the string and replace them with a clickable
	# mailto: link, making sure that we skip processing of any existing anchor
	# tags, to avoid parts of URLs such as https://user@example.com/ or
	# http://user:password@example.com/ to be not treated as an email.
	$t_pieces = preg_split ($s_anchor_regex, $p_string, null, PREG_SPLIT_DELIM_CAPTURE);
	$p_string = '';
	foreach ($t_pieces as $piece)
		if (preg_match ($s_anchor_regex, $piece))
			$p_string .= $piece;
		else
			$p_string .= preg_replace ($s_email_regex, '<a href="mailto:\0">\0</a>', $piece);

	return $p_string;
}

# Adopted from MantisBT, core/email_api.php:email_regex_simple.
function email_regex_simple()
{
	static $s_email_regex = null;

	if (is_null ($s_email_regex))
	{
		$t_recipient = "([a-z0-9!#*+\/=?^_{|}~-]+(?:\.[a-z0-9!#*+\/=?^_{|}~-]+)*)";

		# a domain is one or more subdomains
		$t_subdomain = "(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)";
		$t_domain    = "(${t_subdomain}(?:\.${t_subdomain})*)";

		$s_email_regex = "/${t_recipient}\@${t_domain}/i";
	}
	return $s_email_regex;
}

// Parse AUTOPORTS_CONFIG and return a list of generated pairs (port_type, port_name)
// for the requested object.
function getAutoPorts ($object)
{
	return parseAutoPortsConfig (getAutoPortsConfig ($object));
}

// Extract automatic ports schema from the AUTOPORTS_CONFIG
// based on the given object's type. Can be overriden
function getAutoPortsConfig ($object)
{
	$override = callHook ('getAutoPortsConfig_hook', $object);
	if (isset ($override))
		return $override;

	$typemap = explode (';', str_replace (' ', '', getConfigVar ('AUTOPORTS_CONFIG')));
	foreach ($typemap as $equation)
	{
		$tmp = explode ('=', $equation);
		if (count ($tmp) != 2)
			continue;
		$objtype_id = $tmp[0];
		if ($objtype_id == $object['objtype_id'])
			return $tmp[1];
	}
}

function parseAutoPortsConfig ($schema)
{
	$ret = array();

	foreach (explode ('+', $schema) as $product)
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
	return $ret;
}

// Use pre-served trace to traverse the tree, then place given node where it belongs.
function pokeNode (&$tree, $trace, $key, $value, $threshold = 0)
{
	$self = __FUNCTION__;
	// This function needs the trace to be followed FIFO-way. The fastest
	// way to do so is to use array_push() for putting values into the
	// list and array_shift() for getting them out. This exposed up to 11%
	// performance gain compared to other patterns of array_push/array_unshift/
	// array_reverse/array_pop/array_shift conjunction.
	$myid = array_shift ($trace);
	if (count ($trace)) // not yet reached the target
		$self ($tree[$myid]['kids'], $trace, $key, $value, $threshold);
	else // just did
	{
		if (! $threshold || ($threshold && $tree[$myid]['kidc'] + 1 < $threshold))
			$tree[$myid]['kids'][$key] = $value;
		// Reset accumulated records once, when the limit is reached, not each time
		// after that.
		if (++$tree[$myid]['kidc'] == $threshold)
			$tree[$myid]['kids'] = array();
	}
}

// Likewise traverse the tree with the trace and return the final node.
// This function is not currently used in RackTables main code but it works well.
function peekNode ($tree, $trace, $target_id)
{
	$self = __FUNCTION__;
	if (NULL === ($next = array_shift ($trace))) // warm
	{
		foreach ($tree as $node)
			if (array_key_exists ('id', $node) && $node['id'] == $target_id) // hot
				return $node;
	}
	else // cold
	{
		foreach ($tree as $node)
			if (array_key_exists ('id', $node) && $node['id'] == $next) // warmer
				return $self ($node['kids'], $trace, $target_id);
	}
	throw new RackTablesError ('inconsistent tree data', RackTablesError::INTERNAL);
}

// The structure used in RackTables to represent tags is called a forest of rooted
// trees, which is a set of directed graphs each having a node appointed as root
// and exactly one path possible from the root node to every other node of the
// graph.
//
// The TagTree database table contains a generic list of graph nodes with each node
// having an optional incoming directed edge from any existing node. This table
// generally can encode any set of any directed graphs that allow at most one
// incoming edge per node. This includes but is not limited to the forest of rooted
// trees. However, a number of RackTables functions specifically relies upon
// consistent relations between the tags (presented either as a complete forest
// structure or just as each node's path from the root), hence an early validation
// step is required in the PHP code to implement the constraints in full.
//
// The function below implements this step. For every node on the input list that
// belongs to a forest of rooted trees it sets the 'trace' key to the sequence of
// node IDs that leads from tree root up to (but not including) the node. As an
// edge case, for each root node it sets this sequence to an empty list. For any
// nodes not in the forest (i.e., those that form any graph cycle or descend from
// such a cycle) it leaves 'trace' unset.
function addTraceToNodes ($nodelist)
{
	foreach ($nodelist as $nodeid => $node)
	{
		$trace = array();
		$parentid = $node['parent_id'];
		while ($parentid != NULL)
		{
			if (! isset ($nodelist[$parentid]))
			{
				// bad parent_id
				$trace = NULL;
				break;
			}

			// check for cycles every 10 steps
			if (0 == (count ($trace) % 10) && in_array ($parentid, $trace))
			{
				// cycle detected
				$trace = NULL;
				break;
			}
			array_unshift ($trace, $parentid);
			$parentid = $nodelist[$parentid]['parent_id'];
		}
		if (isset ($trace))
			$nodelist[$nodeid]['trace'] = $trace;
	}
	return $nodelist;
}

function treeItemCmp ($a, $b)
{
	return $a['__tree_index'] - $b['__tree_index'];
}

function getTagTree()
{
	return treeFromList (getTagUsage());
}

// Build a tree from the item list and return it. Input and output data is
// indexed by item id (nested items in output are recursively stored in 'kids'
// key, which is in turn indexed by id. Functions that are ready to handle
// tree collapsion/expansion themselves may request non-zero threshold value
// for smaller resulting tree.
// FIXME: The 2nd argument to this function seems not to be used any more.
// FIXME: The structure this function returns is a forest of rooted trees
//        despite the terminology it used to use.
function treeFromList ($nodelist, $threshold = 0)
{
	$tree = array();

	// Preserve original ordering in __tree_index.
	$ti = 0;
	foreach (array_keys ($nodelist) as $key)
	{
		$nodelist[$key]['__tree_index'] = $ti++;
		$nodelist[$key]['kidc'] = 0;
		$nodelist[$key]['kids'] = array();
	}

	$done_ids = array();
	do
	{
		$nextpass = FALSE;
		foreach (array_keys ($nodelist) as $nodeid)
		{
			$node = $nodelist[$nodeid];
			// Skip any irrelevant nodes early as they will fail the checks below anyway.
			if (! array_key_exists ('trace', $node))
				continue;
			$parentid = $node['parent_id'];
			// Moving a node from the input list to the output tree potentially enables more
			// nodes to make it from the list to the same tree as well, hence in this case make
			// another full round after the current one.

			if ($parentid == NULL) // A root node?
			{
				$tree[$nodeid] = $node;
				unset ($nodelist[$nodeid]);
				$done_ids[] = $nodeid;
				$nextpass = TRUE;
			}
			elseif (in_array ($parentid, $done_ids)) // Has a direct parent node already on the tree?
			{
				// Being here implies the current node's trace is at least one element long.
				pokeNode ($tree, $node['trace'], $nodeid, $node, $threshold);
				unset ($nodelist[$nodeid]);
				$done_ids[] = $nodeid;
				$nextpass = TRUE;
			}
		}
	}
	while ($nextpass);
	sortTree ($tree, 'treeItemCmp'); // sort the resulting tree by the order in original list
	return $tree;
}

// Return those tags that belong to the full list of tags but don't belong
// to the forest of rooted trees as found by addTraceToNodes().
function getInvalidNodes ($nodelist)
{
	$ret = array();
	foreach ($nodelist as $node_id => $node)
		if (! array_key_exists ('trace', $node))
			$ret[$node_id] = $node;
	return $ret;
}

// Throw an exception unless it is OK to assign the given parent ID
// to the node with the given ID.
function assertValidParentId ($nodelist, $node_id, $parent_id)
{
	if ($parent_id == 0)
		return;
	if ($parent_id == $node_id)
		throw new InvalidArgException ('parent_id', $parent_id, 'must be different from the tag ID');
	if (! array_key_exists ($parent_id, $nodelist))
		throw new InvalidArgException ('parent_id', $parent_id, 'must refer to an existing tag');
	if (! array_key_exists ('trace', $nodelist[$parent_id]))
		throw new InvalidArgException ('parent_id', $parent_id, 'would add to an existing graph cycle');
	if (in_array ($node_id, $nodelist[$parent_id]['trace']))
		throw new InvalidArgException ('parent_id', $parent_id, 'would create a new graph cycle');
}

// Given an existing node ID filter a list of traced nodes and silently skip
// the nodes that are not valid parent node options. Filtering criteria are
// effectively the same as in the function above but use a simpler expression.
function getParentNodeOptionsExisting ($nodelist, $textfield, $node_id)
{
	$ret = array (0 => '-- NONE --');
	foreach ($nodelist as $key => $each)
		if
		(
			$key != $node_id &&
			array_key_exists ('trace', $each) &&
			! in_array ($node_id, $each['trace'])
		)
			$ret[$key] = $each[$textfield];
	return $ret;
}

// Idem, but for a new node, which doesn't yet exist, or a node that is based
// on a circular reference. The condition is even simpler in this case.
function getParentNodeOptionsNew ($nodelist, $textfield)
{
	$ret = array (0 => '-- NONE --');
	foreach ($nodelist as $key => $each)
		if (array_key_exists ('trace', $each))
			$ret[$key] = $each[$textfield];
	return $ret;
}

// removes implicit tags from ['etags'] array and fills ['itags'] array
// Replaces call sequence "getExplicitTagsOnly, getImplicitTags"
function sortEntityTags (&$cell)
{
	global $taglist;
	if (! is_array ($cell['etags']))
		throw new InvalidArgException ('cell[etags]', $cell['etags']);
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
	return NULL !== scanArrayForItem ($tagchain, 'id', $taginfo['id']);
}

function tagNameOnChain ($tagname, $tagchain)
{
	return NULL !== scanArrayForItem ($tagchain, 'tag', $tagname);
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

// returns an array of tag ids that have $tagid as its parent (all levels)
function getTagDescendents ($tagid)
{
	global $taglist;
	$ret = array();
	foreach ($taglist as $id => $taginfo)
		if (array_key_exists ('trace', $taginfo) && in_array ($tagid, $taginfo['trace']))
			$ret[] = $id;
	return $ret;
}

function redirectIfNecessary ()
{
	global
		$trigger,
		$pageno,
		$tabno;
	startSession();
	if
	(
		! isset ($_REQUEST['tab']) &&
		isset ($_SESSION['RTLT'][$pageno]) &&
		getConfigVar ('SHOW_LAST_TAB') == 'yes' &&
		permitted ($pageno, $_SESSION['RTLT'][$pageno]['tabname']) &&
		time() - $_SESSION['RTLT'][$pageno]['time'] <= TAB_REMEMBER_TIMEOUT
	)
		redirectUser (buildRedirectURL ($pageno, $_SESSION['RTLT'][$pageno]['tabname']));

	// Fall back to default when a trigger was OK about a tab when generating the previous page
	// but isn't OK anymore when generating the current page and the tab is the requested tab.
	if
	(
		isset ($trigger[$pageno][$tabno]) &&
		'' == call_user_func ($trigger[$pageno][$tabno])
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
	global $pageno, $tabno;
	$pageno = array_fetch ($_REQUEST, 'page', 'index');
	$tabno = array_fetch ($_REQUEST, 'tab', 'default');
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
		$etype_by_pageno;

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
			isset ($taginfo['refcnt'][$realm]) ||
			count ($subsearch) > 1 ||
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

// Preprocess tag tree to get only tags that can effectively reduce given filter result,
// then pass shrinked tag tree to getObjectiveTagTree and return its result.
// This makes sense only if andor mode is 'and', otherwise function does not modify tree.
// 'Given filter' is a pair of $entity_list(filter result) and $preselect(filter data).
// 'Effectively' means reduce to non-empty result.
function getShrinkedTagTree ($entity_list, $realm, $preselect)
{
	$tagtree = getTagTree();
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
		else
		{
			if (isset ($used_tags[$item['id']]) && $used_tags[$item['id']])
				$item['refcnt'][$realm] = $used_tags[$item['id']];
			else
				unset($item['refcnt'][$realm]);
		}
	}
	return $tree;
}

// Get taginfo record by tag name, return NULL, if record doesn't exist.
function getTagByName ($tag_name)
{
	global $taglist;
	static $cache = NULL;
	if (! isset ($cache))
	{
		$cache = array();
		foreach ($taglist as $key => $taginfo)
			$cache[$taginfo['tag']] = $taginfo;
	}
	return array_fetch($cache, $tag_name, NULL);
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
# This is an earlier and more generic variety of getTagDescendents().
function getTagIDListForNode ($treenode)
{
	$self = __FUNCTION__;
	$ret = array ($treenode['id']);
	foreach ($treenode['kids'] as $item)
		$ret = array_merge ($ret, $self ($item));
	return $ret;
}

function applyCellFilter ($realm, $cellfilter, $parent_id = NULL)
{
	if ($res = callHook ('applyCellFilter_hook', $realm, $cellfilter, $parent_id))
		return $res;
	return filterCellList (listCells ($realm, $parent_id), $cellfilter['expression']);
}

function getCellFilter ()
{
	global $sic;
	global $pageno;
	$andor_used = FALSE;
	startSession();
	// if the page is submitted we get an andor value so we know they are trying to start a new filter or clearing the existing one.
	if (isset($_REQUEST['andor']))
		$andor_used = TRUE;
	if ($andor_used || array_key_exists ('clear-cf', $_REQUEST))
		unset($_SESSION[$pageno]); // delete saved filter

	// otherwise inject saved filter to the $_REQUEST and $sic vars
	elseif (isset ($_SESSION[$pageno]['filter']) && is_array ($_SESSION[$pageno]['filter']) && getConfigVar ('STATIC_FILTER') == 'yes')
		foreach (array('andor', 'cfe', 'cft[]', 'cfp[]', 'nft[]', 'nfp[]') as $param)
		{
			$param = str_replace ('[]', '', $param, $is_array);
			if (! isset ($_REQUEST[$param]) && isset ($_SESSION[$pageno]['filter'][$param]) && (! $is_array || is_array ($_SESSION[$pageno]['filter'][$param])))
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
		session_commit();
		return NULL;
	}
	// Both tags and predicates that don't exist, should be
	// handled somehow. Discard them silently for now.
	global $taglist, $pTable;
	foreach (array ('cft', 'cfp', 'nft', 'nfp') as $param)
		if (isset ($_REQUEST[$param]) && is_array ($_REQUEST[$param]))
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
		$ret['extratext'] = trim ($sic['cfe']);
		$ret['urlextra'] .= '&cfe=' . $ret['extratext'];
	}
	$finaltext = array();
	if ($ret['text'] != '')
		$finaltext[] = '(' . $ret['text'] . ')';
	if ($ret['extratext'] != '')
		$finaltext[] = '(' . $ret['extratext'] . ')';
	$andor_used = $andor_used || (count($finaltext) > 1);
	$finaltext = implode (' ' . $andor . ' ', $finaltext);
	if ($finaltext != '')
	{
		$ret['is_empty'] = FALSE;
		$ret['expression'] = compileExpression ($finaltext);
		// It's not quite fair enough to put the blame of the whole text onto
		// non-empty "extra" portion of it, but it's the only user-generated portion
		// of it, thus the most probable cause of parse error.
		if ($ret['extratext'] != '')
			$ret['extraclass'] = $ret['expression'] ? 'validation-success' : 'validation-error';
	}
	if (! $andor_used)
		$ret['andor'] = getConfigVar ('FILTER_DEFAULT_ANDOR');
	else
		$ret['urlextra'] .= '&andor=' . $ret['andor'];
	session_commit();
	return $ret;
}

function buildRedirectURL ($nextpage = NULL, $nexttab = NULL, $moreArgs = array())
{
	$params = array();
	if ($nextpage !== NULL)
		$params['page'] = $nextpage;
	if ($nexttab !== NULL)
		$params['tab'] = $nexttab;
	$params = makePageParams ($params + $moreArgs);
	unset ($params['module']); // 'interface' module is the default
	return makeHref ($params);
}

// store the accumulated message list into he $SESSION array to display them later
function backupLogMessages()
{
	global $log_messages;
	if (! empty ($log_messages))
	{
		startSession();
		$_SESSION['log'] = $log_messages;
		session_commit();
	}
}

function redirectUser ($url)
{
	backupLogMessages();
	header ("Location: " . $url);
	die;
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
// Returns number of marked up (busy) addresses
function markupIPAddrList (&$addrlist)
{
	$used = 0;
	foreach (array_keys ($addrlist) as $ip_bin)
	{
		$refc = array
		(
			'shared' => 0,  // virtual
			'virtual' => 0, // loopback
			'regular' => 0, // connected host
			'router' => 0,   // connected gateway
			'point2point' => 0,
		);
		$nallocs = 0;
		foreach ($addrlist[$ip_bin]['allocs'] as $a)
		{
			$refc[$a['type']]++;
			$nallocs++;
		}
		$nreserved = ($addrlist[$ip_bin]['reserved'] == 'yes') ? 1 : 0; // only one reservation is possible ever
		if ($nallocs > 1 && $nallocs != $refc['shared'] || $nallocs && $nreserved)
		{
			$addrlist[$ip_bin]['class'] = 'trerror';
			++$used;
		}
		elseif (! isIPAddressEmpty ($addrlist[$ip_bin], array ('name', 'comment', 'inpf', 'outpf'))) // these fields don't trigger the 'busy' status
		{
			$addrlist[$ip_bin]['class'] = 'trbusy';
			++$used;
		}
		else
			$addrlist[$ip_bin]['class'] = '';
	}
	return $used;
}

function findNetRouters ($net)
{
	if (isset ($net['own_addrlist']))
		$own_addrlist = $net['own_addrlist'];
	else
	{
		// do not call loadIPAddrList, it is expensive.
		// instead, do our own DB scan only for router allocations
		$rtrlist = scanIPNet ($net, IPSCAN_DO_ALLOCS | IPSCAN_RTR_ONLY);
		$own_addrlist = filterOwnAddrList ($net, $rtrlist);
	}
	return findRouters ($own_addrlist);
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

function numSign ($x)
{
	if ($x < 0)
		return -1;
	if ($x > 0)
		return 1;
	return 0;
}

function numCompare ($a, $b)
{
	return numSign ($a - $b);
}

// compare binary IPs (IPv4 are less than IPv6)
// valid return values are: 1, 0, -1
function IPCmp ($ip_binA, $ip_binB)
{
	if (strlen ($ip_binA) !== strlen ($ip_binB))
		return numCompare (strlen ($ip_binA), strlen ($ip_binB));
	return numSign (strcmp ($ip_binA, $ip_binB));
}

// Binary compare the first addresses of each pair
// If the first addresses of the compared pairs are equal, compare last addresses in reverse order
// valid return values are: 1, 0, -1
function IPSpaceCmp ($pairA, $pairB)
{
	$first = IPCmp ($pairA['first'], $pairB['first']);
	return $first ? $first : IPCmp ($pairB['last'], $pairA['last']);
}

// filter netsted ip pairs
function reduceIPPairList ($pairlist)
{
	$ret = array();
	$left = $pairlist;
	usort ($left, 'IPSpaceCmp');
	while ($left)
	{
		$agg = array_shift ($left);
		$ret[] = $agg;
		foreach ($left as $id => $pair)
			if ($pair['first'] >= $agg['first'] && $pair['last'] <= $agg['last'])
				unset ($left[$id]);
	}
	return $ret;
}

// Compare networks. When sorting a tree, the records on the list will have
// distinct base IP addresses.
// valid return values are: 2, 1, 0, -1, -2
// -2, 2 have special meaning: $netA includes $netB or vice versa, respecively
// "The comparison function must return an integer less than, equal to, or greater
// than zero if the first argument is considered to be respectively less than,
// equal to, or greater than the second." (c) PHP manual
function IPNetworkCmp ($netA, $netB)
{
	$ret = IPCmp ($netA['ip_bin'], $netB['ip_bin']);
	if ($ret == 0)
		$ret = $netA['mask'] < $netB['mask'] ? -1 : ($netA['mask'] > $netB['mask'] ? 1 : 0);
	if ($ret == -1 && $netA['ip_bin'] === ($netB['ip_bin'] & $netA['mask_bin']))
		$ret = -2;
	if ($ret == 1 && $netB['ip_bin'] === ($netA['ip_bin'] & $netB['mask_bin']))
		$ret = 2;
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

// Sort each level of the tree independently using the given compare function.
function sortTree (&$tree, $cmpfunc = '')
{
	$self = __FUNCTION__;
	if (! is_callable ($cmpfunc))
		throw new InvalidArgException ('cmpfunc', $cmpfunc, 'is not a callable function');
	usort ($tree, $cmpfunc);
	foreach (array_keys ($tree) as $tagid)
		$self ($tree[$tagid]['kids'], $cmpfunc);
}

function iptree_fill (&$netdata)
{
	if (! isset ($netdata['kids']) || ! count ($netdata['kids']))
		return;

	foreach ($netdata['spare_ranges'] as $mask => $list)
	{
		$spare_mask = $mask;
		// align spare IPv6 nets by nibble boundary
		if (strlen ($netdata['ip_bin']) == 16 && $mask % 4)
			$spare_mask = $mask + 4 - ($mask % 4);
		foreach ($list as $ip_bin)
			foreach (splitNetworkByMask (constructIPRange ($ip_bin, $mask), $spare_mask) as $spare)
				$netdata['kids'][] = $spare + array('kids' => array(), 'kidc' => 0, 'name' => '');
	}

	if (count ($netdata['kids']) != $netdata['kidc'])
	{
		$netdata['kidc'] = count ($netdata['kids']);
		usort ($netdata['kids'], 'IPNetworkCmp');
	}
}

// returns TRUE if inet_ntop and inet_pton functions exist
function is_inet_avail()
{
	static $ret = NULL;
	if (! isset ($ret))
		$ret = is_callable ('inet_pton') && is_callable ('inet_ntop');
	return $ret;
}

// returns TRUE if inet_ntop and inet_pton functions exist and support IPv6
function is_inet6_avail()
{
	static $ret = NULL;
	if (! isset ($ret))
		$ret = is_inet_avail() && defined ('AF_INET6');
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

		if (is_inet6_avail())
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
	try
	{
		if (FALSE !== strpos ($ip, ':'))
			return ip6_parse ($ip);
		else
			return ip4_parse ($ip);
	}
	catch (InvalidArgException $e)
	{
		// re-throw with general error message, without specifying an IP family
		throw new InvalidArgException ('ip', $ip, "Invalid IP address");
	}
}

function ip4_parse ($ip)
{
	if (is_inet_avail())
	{
		if (FALSE !== ($ret = @inet_pton ($ip)) && strlen ($ret) == 4)
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
		if (is_inet6_avail())
		{
			if (FALSE !== ($ret = @inet_pton ($ip)) && strlen ($ret) == 16)
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
	return $ip_int >= 0 ? $ip_int : sprintf ('%u', 0x00000000 + $ip_int);
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
		if ($oct <= 255 && $oct >= 0)
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
		if ($oct <= 255 && $oct >= 0)
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
function constructIPRange ($ip_bin, $mask = NULL)
{
	$node = array();
	switch (strlen ($ip_bin))
	{
		case 4: // IPv4
			if ($mask === NULL)
				$mask = 32;
			elseif (! is_numeric($mask) || $mask < 0 || $mask > 32)
				throw new InvalidArgException ('mask', $mask, "Invalid v4 prefix length");
			$node['mask_bin'] = ip4_mask ($mask);
			$node['mask'] = $mask;
			$node['ip_bin'] = $ip_bin & $node['mask_bin'];
			$node['ip'] = ip4_format ($node['ip_bin']);
			break;
		case 16: // IPv6
			if ($mask === NULL)
				$mask = 128;
			elseif (! is_numeric($mask) || $mask < 0 || $mask > 128)
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
		'vsglist' => array(),
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

function treeApplyFunc1 (&$tree, $func)
{
	$self = __FUNCTION__;
	foreach (array_keys ($tree) as $key)
	{
		$func ($tree[$key]);
		$self ($tree[$key]['kids'], $func);
	}
}

function treeApplyFunc2 (&$tree, $func, $stopfunc)
{
	$self = __FUNCTION__;
	foreach (array_keys ($tree) as $key)
	{
		$func ($tree[$key]);
		if (! $stopfunc ($tree[$key]))
			$self ($tree[$key]['kids'], $func);
	}
}

// Note that the stop function is called after processing a tree item, not before.
// In other words, for a given tree node either all its sub-nodes are processed or
// none at all.
// XXX: Perhaps instead of a separate stop function it would be better to convey the
// feedback through the applied function's return value. This would also leave it up
// to the applied function whether to stop before or after modifying the current node.
function treeApplyFunc (&$tree, $func, $stopfunc = NULL)
{
	if (! is_callable ($func))
		throw new InvalidArgException ('func', $func, 'is not callable');
	if ($stopfunc === NULL)
		treeApplyFunc1 ($tree, $func);
	else
	{
		if (! is_callable ($stopfunc))
			throw new InvalidArgException ('stopfunc', $stopfunc, 'is not callable');
		treeApplyFunc2 ($tree, $func, $stopfunc);
	}
}

function nodeIsCollapsed ($node)
{
	return $node['symbol'] == 'node-collapsed';
}

// returns those addresses from $addrlist that do not belong to $net's subsequent networks
function filterOwnAddrList ($net, $addrlist)
{
	if ($net['kidc'] == 0)
		return $addrlist;

	// net has children
	$ret = array();
	foreach ($net['spare_ranges'] as $mask => $spare_list)
		foreach ($spare_list as $spare_ip)
		{
			$spare_mask = ip_mask ($mask, strlen ($net['ip_bin']) == 16);
			foreach ($addrlist as $bin_ip => $addr)
				if (($bin_ip & $spare_mask) == $spare_ip)
					$ret[$bin_ip] = $addr;
		}
	return $ret;
}

function getIPAddrList ($net, $flags = IPSCAN_ANY)
{
	$addrlist = scanIPNet ($net, $flags);
	return filterOwnAddrList ($net, $addrlist);
}

// sets 'addrlist', 'own_addrlist', 'addrc', 'own_addrc' keys of $node
// 'addrc' and 'own_addrc' are sizes of 'addrlist' and 'own_addrlist', respectively
function loadIPAddrList (&$node)
{
	$node['addrlist'] = scanIPNet ($node);

	if (! isset ($node['id']))
		$node['own_addrlist'] = $node['addrlist'];
	else
		$node['own_addrlist'] = filterOwnAddrList ($node, $node['addrlist']);

	$node['addrc'] = count ($node['addrlist']);
	$node['own_addrc'] = count ($node['own_addrlist']);
}

// returns list of PtP-typed allocs from $addrlist indexed by ip_bin
function getPtPNeighbors ($ip_bin, $addrlist)
{
	$ret = array();
	if (isset ($addrlist[$ip_bin]))
	{
		$found_ptp_alloc = FALSE;
		foreach ($addrlist[$ip_bin]['allocs'] as $alloc)
			if ($alloc['type'] == 'point2point')
			{
				$found_ptp_alloc = TRUE;
				break;
			}
		if ($found_ptp_alloc)
			foreach ($addrlist as $i_ip_bin => $i_address)
				if ($ip_bin !== $i_ip_bin)
					foreach ($i_address['allocs'] as $alloc)
						if ($alloc['type'] == 'point2point')
							$ret[$i_ip_bin][] = $alloc;
	}
	return $ret;
}

// returns the array of structure described by constructIPAddress
function getIPAddress ($ip_bin)
{
	$scanres = scanIPSpace (array (array ('first' => $ip_bin, 'last' => $ip_bin)));
	if (empty ($scanres))
		$scanres[$ip_bin] = constructIPAddress ($ip_bin);
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
		while (count ($stack))
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
		if (! count ($stack))
			$net['parent_id'] = NULL;
		array_push ($stack, $net_id);
	}
	unset ($stack);

	return treeFromList (addTraceToNodes ($netlist));
}

function prepareIPTree ($netlist, $expanded_id = 0)
{
	$tree = makeIPTree ($netlist);
	// complement the tree before markup to make the spare networks have "symbol" set
	treeApplyFunc ($tree, 'iptree_fill');
	iptree_markup_collapsion ($tree, getConfigVar ('TREE_THRESHOLD'), $expanded_id);
	return $tree;
}

# Traverse IPv4/IPv6 tree and return a list of all networks that
# exist in DB and don't have any sub-networks.
function getTerminalNetworks ($tree)
{
	$self = __FUNCTION__;
	$ret = array();
	foreach ($tree as $node)
		if ($node['kidc'] == 0 && isset ($node['realm']))
			$ret[] = $node;
		else
			$ret = array_merge ($ret, $self ($node['kids']));
	return $ret;
}

// Check all items of the tree recursively, until the requested target id is
// found. Mark all items leading to this item as "expanded", collapsing all
// the rest that exceed the given threshold (if the threshold is given).
function iptree_markup_collapsion (&$tree, $threshold = 1024, $target = 0)
{
	$self = __FUNCTION__;
	$ret = FALSE;
	foreach (array_keys ($tree) as $key)
	{
		$here = $target === 'ALL' || ($target > 0 && isset ($tree[$key]['id']) && $tree[$key]['id'] == $target);
		$below = $self ($tree[$key]['kids'], $threshold, $target);
		$expand_enabled = ($target !== 'NONE');
		if (!$tree[$key]['kidc']) // terminal node
			$tree[$key]['symbol'] = 'spacer';
		elseif ($expand_enabled && $tree[$key]['kidc'] < $threshold)
			$tree[$key]['symbol'] = 'node-expanded-static';
		elseif ($expand_enabled && ($here || $below))
			$tree[$key]['symbol'] = 'node-expanded';
		else
			$tree[$key]['symbol'] = 'node-collapsed';
		$ret = $ret || $here || $below;
	}
	return $ret;
}

// Convert entity name to human-readable value
function formatRealmName ($realm)
{
	$realmstr = array
	(
		'ipv4net' => 'IPv4 Network',
		'ipv6net' => 'IPv6 Network',
		'ipv4rspool' => 'IPv4 RS Pool',
		'ipv4vs' => 'IPv4 Virtual Service',
		'ipvs' => 'IP Virtual Service',
		'object' => 'Object',
		'rack' => 'Rack',
		'row' => 'Row',
		'location' => 'Location',
		'user' => 'User',
		'file' => 'File',
		'vst' => 'VLAN switch template',
	);
	return array_fetch ($realmstr, $realm, 'invalid');
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
function mkA ($text, $nextpage, $bypass = NULL, $nexttab = NULL, $attrs = array())
{
	global $page, $tab;
	if ($text == '')
		throw new InvalidArgException ('text', $text, 'must not be empty');
	if (! array_key_exists ($nextpage, $page))
		throw new InvalidArgException ('nextpage', $nextpage, 'not a valid page name');
	$args = array ('page' => $nextpage);
	if ($nexttab !== NULL)
	{
		if (! array_key_exists ($nexttab, $tab[$nextpage]))
			throw new InvalidArgException ('nexttab', $nexttab, 'not a valid tab name');
		$args['tab'] = $nexttab;
	}
	if (array_key_exists ('bypass', $page[$nextpage]))
	{
		if ($bypass === NULL)
			throw new InvalidArgException ('bypass', '(NULL)', 'must be specified for the given page name');
		$args[$page[$nextpage]['bypass']] = $bypass;
	}
	$attrs['href'] = makeHref ($args);
	$ret = '<a';
	foreach ($attrs as $attr_name => $attr_value)
		$ret .= " $attr_name=" . '"' . htmlspecialchars ($attr_value, ENT_QUOTES) . '"';
	return $ret . '>' . $text . '</a>';
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

function makePageParams ($params = array())
{
	global $pageno, $tabno;
	$ret = array();
	// assure that page and tab keys go first
	$ret['page'] = isset ($params['page']) ? $params['page'] : $pageno;
	$ret['tab'] = isset ($params['tab']) ? $params['tab'] : $tabno;
	$ret += $params;
	if ($ret['page'] === $pageno)
		fillBypassValues ($pageno, $ret);
	return $ret;
}

function makeHrefProcess ($params = array())
{
	return makeHref (array ('module' => 'redirect') + makePageParams ($params));
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
			if (preg_match ("/^([^@]+)(@${object_type_id})?\$/", trim ($sieve), $regs))
				$screenlist[] = $regs[1];

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

function dos2unix ($text)
{
	return str_replace ("\r\n", "\n", $text);
}

function unix2dos ($text)
{
	return str_replace ("\n", "\r\n", $text);
}

function buildPredicateTable (&$rackCode)
{
	$ret = array();
	$new_rackCode = array();

	foreach ($rackCode as $sentence)
		if ($sentence['type'] == 'SYNT_DEFINITION')
			$ret[$sentence['term']] = $sentence['definition'];
		else
			$new_rackCode[] = $sentence;

	// remove SYNT_DEFINITION statements from the original rackCode to
	// make permitted() calls faster.
	$rackCode = $new_rackCode;

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

function eval_expression ($expr, $tagchain, $silent = FALSE)
{
	$self = __FUNCTION__;
	global $pTable;

	switch ($expr['type'])
	{
		// Return true, if given tag is present on the tag chain.
		case 'LEX_TAG':
			return isset ($tagchain[$expr['load']]);
		case 'LEX_PREDICATE': // Find given predicate in the symbol table and evaluate it.
			$pname = $expr['load'];
			if (!isset ($pTable[$pname]))
			{
				if (!$silent)
					showWarning ("Undefined predicate [${pname}]");
				return NULL;
			}
			return $self ($pTable[$pname], $tagchain, $silent);
		case 'LEX_BOOL':
			return $expr['load'];
		case 'SYNT_NOT_EXPR': // logical NOT
			$tmp = $self ($expr['load'], $tagchain, $silent);
			return is_bool($tmp) ? !$tmp : $tmp;
		case 'SYNT_AND_EXPR': // logical AND
			foreach ($expr['tag_args'] as $tag)
				if (! isset ($tagchain[$tag]))
					return FALSE; // early failure
			foreach ($expr['expr_args'] as $sub_expr)
				if (! $self ($sub_expr, $tagchain, $silent))
					return FALSE; // early failure
			return TRUE;
		case 'SYNT_EXPR': // logical OR
			foreach ($expr['tag_args'] as $tag)
				if (isset ($tagchain[$tag]))
					return TRUE; // early success
			foreach ($expr['expr_args'] as $sub_expr)
				if ($self ($sub_expr, $tagchain, $silent))
					return TRUE; // early success
			return FALSE;
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
	$context = array_merge ($cell['etags'], $cell['itags'], $cell['atags']);
	$context = reindexById ($context, 'tag', TRUE);
	return eval_expression
	(
		$expression,
		$context,
		TRUE
	);
}

function judgeContext ($expression)
{
	global $expl_tags, $impl_tags, $auto_tags;
	$context = array_merge ($expl_tags, $impl_tags, $auto_tags);
	$context = reindexById ($context, 'tag', TRUE);
	return eval_expression
	(
		$expression,
		$context,
		TRUE
	);
}

// Tell, if a constraint from config option permits given record.
// An undefined $cell means current context.
function considerConfiguredConstraint ($cell, $varname)
{
	try
	{
		return considerGivenConstraint ($cell, getConfigVar ($varname));
	}
	catch (RackTablesError $e)
	{
		return FALSE; // constraint set, but cannot be used due to compilation error
	}
}

// Tell, if the given arbitrary RackCode text addresses the given record
// (an empty text matches any record).
// An undefined $cell means current context.
function considerGivenConstraint ($cell, $filter)
{
	if ($filter == '')
		return TRUE;
	if (! $expr = compileExpression ($filter))
		throw new InvalidArgException ('filter', $filter, 'not a valid RackCode expression');
	if (isset ($cell))
		return judgeCell ($cell, $expr);
	else
		return judgeContext ($expr);
}

// Return list of records in the given realm that conform to
// the given RackCode expression. If the realm is unknown or text
// doesn't validate as a RackCode expression, return NULL.
// Otherwise (successful scan) return a list of all matched
// records, even if the list is empty (array() !== NULL). If the
// text is an empty string, return all found records in the given
// realm.
function scanRealmByText ($realm, $ftext = '')
{
	if ('' == $ftext = trim ($ftext))
		$fexpr = array();
	else
	{
		$fexpr = compileExpression ($ftext);
		if (! $fexpr)
			return NULL;
	}
	return filterCellList (listCells ($realm), $fexpr);
}

function getVSTOptions()
{
	return reduceSubarraysToColumn (reindexById (listCells ('vst')), 'description');
}

# Return an array in the format understood by getNiftySelect() and getOptionTree(),
# so that the options stand for all VLANs grouped by respective VLAN domains, except
# those listed on the "except" array.
function getAllVLANOptions ($except = array())
{
	$ret = array();
	foreach (getVLANDomainOptions() as $domain_id => $domain_descr)
	{
		$domain_list = array();
		foreach (getDomainVLANList ($domain_id, TRUE) as $vlan)
			$domain_list["${domain_id}-${vlan['vlan_id']}"] = "${vlan['vlan_id']} ${vlan['vlan_descr']}";
		if (isset ($except[$domain_id]))
			foreach ($except[$domain_id] as $vid)
				if (isset ($domain_list["${domain_id}-${vid}"]))
					unset ($domain_list["${domain_id}-${vid}"]);
		$ret[$domain_descr] = $domain_list;
	}
	return $ret;
}

// Let's have this debug helper here to enable debugging of process.php w/o interface.php.
function dump ($var)
{
	echo '<div align=left><pre>';
	var_dump ($var);
	echo '</pre></div>';
}

function getTagChart ($limit = 0, $realm = 'total', $special_tags = array())
{
	$taglist_usage = getTagUsage();
	// first build top-N chart...
	$toplist = array();
	foreach ($taglist_usage as $taginfo)
		if (isset ($taginfo['refcnt'][$realm]))
			$toplist[$taginfo['id']] = $taginfo['refcnt'][$realm];
	arsort ($toplist, SORT_NUMERIC);
	$ret = array();
	$done = 0;
	foreach (array_keys ($toplist) as $tag_id)
	{
		$ret[$tag_id] = $taglist_usage[$tag_id];
		if (++$done == $limit)
			break;
	}
	// ...then make sure, that every item of the special list is shown
	// (using the same sort order)
	$extra = array();
	foreach ($special_tags as $taginfo)
		if (!array_key_exists ($taginfo['id'], $ret))
			$extra[$taginfo['id']] = $taglist_usage[$taginfo['id']]['refcnt'][$realm];
	arsort ($extra, SORT_NUMERIC);
	foreach (array_keys ($extra) as $tag_id)
		$ret[] = $taglist_usage[$tag_id];
	return $ret;
}

// $style is deprecated and unused
function decodeObjectType ($objtype_id, $style = '')
{
	static $types;
	if (! isset ($types))
		$types = readChapter (CHAP_OBJTYPE, 'a');
	return $types[$objtype_id];
}

// This wrapper makes it possible to call permitted() with the security context
// containing the given object of temporary interest without the [previously loaded]
// main subject.
function isolatedPermission ($p, $t, $cell)
{
	$saved = getContext();
	// retarget
	fixContext ($cell);
	// remember decision
	$ret = permitted ($p, $t);
	restoreContext ($saved);
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
		if (count ($tmp) == 2 && $tmp[0] > 0 && $tmp[1] > 0)
			$ret['oif_picks'][$tmp[0]] = $tmp[1];
	}
	// enforce default value
	if (!array_key_exists (1, $ret['oif_picks']))
		$ret['oif_picks'][1] = 24;
	$ret['selected'] = $ret['iif_pick'] . '-' . $ret['oif_picks'][$ret['iif_pick']];
	return $ret;
}

function getNewPortTypeOptions()
{
	return getUnlinkedPortTypeOptions (NULL);
}

// Return data for printNiftySelect() with port type options. All OIF options
// for the default or current (passed) IIFs will be shown, but only the default
// OIFs will be present for each other IIFs. IIFs for that there is no default
// OIF will not be listed.
// This SELECT will be used in "manage object ports" form.
function getUnlinkedPortTypeOptions ($port_iif_id)
{
	static $cache;
	static $prefs;
	static $compat;
	if (! isset ($cache))
	{
		$cache = array();
		$prefs = getPortListPrefs();
		$compat = getPortInterfaceCompat();
	}

	if (isset ($cache[$port_iif_id]))
		return $cache[$port_iif_id];

	$ret = array();
	$seen_oifs = array();
	$ambiguous_oifs = array();
	foreach ($compat as $row)
	{
		if (isset ($seen_oifs[$row['oif_id']]))
			$ambiguous_oifs[$row['oif_id']] = 1;
		$seen_oifs[$row['oif_id']] = 1;
	}
	foreach ($compat as $row)
	{
		if ($row['iif_id'] == $prefs['iif_pick'] || $row['iif_id'] == $port_iif_id)
			$optgroup = $row['iif_name'];
		elseif (array_key_exists ($row['iif_id'], $prefs['oif_picks']) && $prefs['oif_picks'][$row['iif_id']] == $row['oif_id'])
			$optgroup = 'other';
		else
			continue;
		if (!array_key_exists ($optgroup, $ret))
			$ret[$optgroup] = array();
		if (isset ($ambiguous_oifs[$row['oif_id']]) && $optgroup == 'other')
			$name = $row['iif_name'] . ' / ' . $row['oif_name'];
		else
			$name = $row['oif_name'];
		$ret[$optgroup][$row['iif_id'] . '-' . $row['oif_id']] = $name;
	}

	$cache[$port_iif_id] = $ret;
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
	return $ret != '' ? $ret : 'default';
}

function groupIntsToRanges ($list, $exclude_value = NULL)
{
	$result = array();
	sort ($list);
	$id_from = $id_to = 0;
	$list[] = -1;
	foreach ($list as $next_id)
		if (! isset ($exclude_value) || $next_id != $exclude_value)
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
		$ret .= ' ' . $vlaninfo['vlan_descr'];
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

function formatVLANAsShortLink ($vlaninfo)
{
	$title = sprintf ('VLAN %d @ %s', $vlaninfo['vlan_id'], $vlaninfo['domain_descr']);
	if ($vlaninfo['vlan_descr'] != '')
		$title .= ' (' . $vlaninfo['vlan_descr'] . ')';
	$attrs = array ('title' => $title);
	return mkA ($vlaninfo['vlan_id'], 'vlan', $vlaninfo['domain_id'] . '-' . $vlaninfo['vlan_id'], NULL, $attrs);
}

function formatVLANAsRichText ($vlaninfo)
{
	$ret = 'VLAN' . $vlaninfo['vlan_id'];
	$ret .= ' @' . niftyString ($vlaninfo['domain_descr']);
	if ($vlaninfo['vlan_descr'] != '')
		$ret .= ' <i>(' . niftyString ($vlaninfo['vlan_descr']) . ')</i>';
	return $ret;
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
			$ret[] = intval ($matches[1]);
		elseif (preg_match ('/^([[:digit:]]+)-([[:digit:]]+)$/', $item, $matches))
			$ret = array_merge ($ret, range ($matches[1], $matches[2]));
		else
			throw new InvalidArgException ('string', $string, 'format mismatch');
	}
	return $ret;
}

// Scan given array and return the key that addresses the first item
// with requested column set to given value (or NULL if there is none such).
// Note that 0 and NULL mean completely different things and thus
// require strict checking (=== and !===).
// Also note that this is not a 1:1 reinvention of PHP's array_search() as
// this function looks one level deeper (which could be done by means of
// array_column(), which appears only in PHP 5 >= 5.5.0).
function scanArrayForItem ($table, $scan_column, $scan_value)
{
	foreach ($table as $key => $row)
		if ($row[$scan_column] == $scan_value)
			return $key;
	return NULL;
}

// Return TRUE, if every value of A1 is present in A2 and vice versa,
// regardless of each array's sort order and indexing.
// Any duplicate values in either of the arguments would be treated same
// as a single occurrence, IOW, there is an implicit array_unique() here.
function array_values_same ($a1, $a2)
{
	if (! is_array ($a1))
		throw new InvalidArgException ('a1', $a1, 'is not an array');
	if (! is_array ($a2))
		throw new InvalidArgException ('a2', $a2, 'is not an array');
	return ! count (array_diff ($a1, $a2)) && ! count (array_diff ($a2, $a1));
}

# Reindex provided array of arrays by a column value that is present in
# each sub-array and is assumed to be unique. Most often, make "id" column in
# a list of cells into the key space.
function reindexById ($input, $column_name = 'id', $ignore_dups = FALSE)
{
	$ret = array();
	if (! is_array ($input))
		throw new InvalidArgException ('input', $input, 'must be an array');
	foreach ($input as $item)
	{
		if (! isset ($item[$column_name]))
			throw new InvalidArgException ('input', '(array)', 'ID column missing');
		if (isset ($ret[$item[$column_name]]))
		{
			if (!$ignore_dups)
				throw new InvalidArgException ('column_name', $column_name, 'duplicate ID value ' . $item[$column_name]);
		}
		else
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
#
# A similar array_column() function is available in PHP 5 >= 5.5.0.
function reduceSubarraysToColumn ($input, $column)
{
	$ret = array();
	if (! is_array ($input))
		throw new InvalidArgException ('input', $input, 'must be an array');
	foreach ($input as $key => $item)
		if (array_key_exists ($column, $item))
			$ret[$key] = $item[$column];
		else
			throw new InvalidArgException ('input', '(array)', "column '${column}' is not set for subarray at index '${key}'");
	return $ret;
}

// Use the VLAN switch template to set VST role for each port of
// the provided list. Return resulting list.
function apply8021QOrder ($vswitch, $portlist)
{
	$hook_result = callHook ('apply8021Qrder_hook', $vswitch, $portlist);
	if (isset ($hook_result))
		return $hook_result;

	$vst_id = $vswitch['template_id'];
	$vst = spotEntity ('vst', $vswitch['template_id']);
	amplifyCell ($vst);

	// warm the vlan_filter cache for every rule
	foreach ($vst['rules'] as $i_rule => $rule)
		$vst['rules'][$i_rule]['vlan_filter'] = buildVLANFilter ($rule['port_role'], $rule['wrt_vlans']);

	foreach (array_keys ($portlist) as $port_name)
	{
		foreach ($vst['rules'] as $rule)
			if (preg_match ($rule['port_pcre'], $port_name))
			{
				$portlist[$port_name]['vst_role'] = $rule['port_role'];
				$portlist[$port_name]['wrt_vlans'] = $rule['vlan_filter'];
				continue 2;
			}
		$portlist[$port_name]['vst_role'] = 'none';
	}
	return $portlist;
}

// Return a sequence of integer ranges for given port role and VLAN filter string.
// FIXME: for an "anymode" port the same filter currently applies to the "trunk"
// and "access" configurations of the port, i.e. such a port cannot be set to "A1"
// by means of the user interface though as far as the data model goes "A1" is a
// valid configuration for an "anymode" port.
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
		if ($min <= $vlan_id && $vlan_id <= $max)
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
		if ($range['from'] <= $vlan_id && $vlan_id <= $range['to'])
			return TRUE;
	return FALSE;
}

function filterVLANList ($vlan_list, $vfilter)
{
	$ret = array();
	foreach ($vlan_list as $vid)
		if (matchVLANFilter ($vid, $vfilter))
			$ret[] = $vid;
	return $ret;
}

function generate8021QDeployOps ($vswitch, $device_vlanlist, $before, $changes)
{
	$domain_vlanlist = getDomainVLANList ($vswitch['domain_id']);
	$employed_vlans = getEmployedVlans ($vswitch['object_id'], $domain_vlanlist);

	// only ignore VLANs that exist and are explicitly shown as "alien"
	$old_managed_vlans = array();
	foreach ($device_vlanlist as $vlan_id)
		if
		(
			! array_key_exists ($vlan_id, $domain_vlanlist) ||
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
	// 2. all "current" non-alien allowed VLANs of those ports that are left
	//    intact (regardless if a VLAN exists in VLAN domain, but looking,
	//    if it is present in device's own VLAN table)
	// 3. all "new" allowed VLANs of those ports that we do "push" now
	// Like for old_managed_vlans, a VLANs is never listed, only if it
	// exists and belongs to "alien" type.
	$new_managed_vlans = array();
	// We need to count down the number of ports still using specific vlan
	// in order to delete it from device as soon as vlan will be removed from the last port
	// This array tracks port count:
	//  * keys are vlan_id's;
	//  * values are the number of changed ports that were using this vlan in old configuration
	$used_vlans = array();
	foreach ($employed_vlans as $vlan_id)
		$used_vlans[$vlan_id] = 1; // prevent deletion of an employed vlan

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
					array_key_exists ($vlan_id, $domain_vlanlist) &&
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
				isset ($domain_vlanlist[$vlan_id]) &&
				$domain_vlanlist[$vlan_id]['vlan_type'] == 'ondemand' &&
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
			if ($port['old_native'] && $port['old_native'] != $port['new_native'])
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
				if (count ($queue))
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
			if ($port['old_native'] && $port['old_native'] != $port['new_native'])
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
				if (count ($queue))
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
			// For each allowed VLAN that is present on the "new" list and missing from
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
			if ($port['new_native'] && $port['new_native'] != $port['old_native'])
				$crq[] = array
				(
					'opcode' => 'set native',
					'arg1' => $port_name,
					'arg2' => $port['new_native'],
				);
			break;
		case 'access->access':
			if ($port['new_native'] && $port['new_native'] != $port['old_native'])
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
	$running_config,
	$changes
)
{
	$crq = generate8021QDeployOps ($vswitch, $running_config['vlanlist'], $running_config['portdata'], $changes);
	if (count ($crq))
	{
		array_unshift ($crq, array ('opcode' => 'begin configuration'));
		$crq[] = array ('opcode' => 'end configuration');
		if (considerConfiguredConstraint (spotEntity ('object', $vswitch['object_id']), '8021Q_WRI_AFTER_CONFT_LISTSRC'))
			$crq[] = array ('opcode' => 'save configuration');
		setVLANSwitchTimestamp ($vswitch['object_id'], 'last_push_started');
		setDevice8021QConfig ($vswitch['object_id'], $crq, $running_config['vlannames']);
		setVLANSwitchTimestamp ($vswitch['object_id'], 'last_push_finished');
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
					$before[$port_name]['native'] == $immune ||
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
	foreach (getObjectIPAllocationList ($object_id) as $ip_bin => $alloc)
		if ($net = spotNetworkByIP ($ip_bin))
			foreach ($net['8021q'] as $vlan)
				if (isset ($domain_vlanlist[$vlan['vlan_id']]) && ! isset ($employed[$vlan['vlan_id']]))
					$employed[$vlan['vlan_id']] = 1;
	$ret = array_keys ($employed);
	$override = callHook ('getEmployedVlans_hook', $ret, $object_id, $domain_vlanlist);
	if (isset ($override))
		$ret = $override;
	return $ret;
}

// take port list with order applied and return uplink ports in the same format
function produceUplinkPorts ($domain_vlanlist, $portlist, $object_id)
{
	$ret = array();

	$employed = array();
	foreach (getEmployedVlans ($object_id, $domain_vlanlist) as $vlan_id)
		$employed[$vlan_id] = $vlan_id;

	foreach ($portlist as $port_name => $port)
		if ($port['vst_role'] != 'uplink')
			foreach ($port['allowed'] as $vlan_id)
				if (! isset ($employed[$vlan_id]) && isset ($domain_vlanlist[$vlan_id]))
					$employed[$vlan_id] = $vlan_id;

	foreach ($portlist as $port_name => $port)
		if ($port['vst_role'] == 'uplink')
			$ret[$port_name] = array
			(
				'vst_role' => 'uplink',
				'mode' => 'trunk',
				'allowed' => filterVLANList ($employed, $port['wrt_vlans']),
				'native' => 0,
			);
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
	foreach (apply8021QOrder ($vswitch, $allports) as $pn => $port)
	{
		// catch anomalies early
		if ($port['vst_role'] == 'none')
		{
			if ((! array_key_exists ($pn, $R) || $R[$pn]['mode'] == 'none') && ! array_key_exists ($pn, $C))
				$ret[$pn] = array ('status' => 'none');
			else
				$ret[$pn] = array
				(
					'status' => 'martian_conflict',
					'left' => array_fetch ($C, $pn, array ('mode' => 'none')),
					'right' => array_fetch ($R, $pn, array ('mode' => 'none')),
				);
			continue;
		}
		elseif ((! array_key_exists ($pn, $R) || $R[$pn]['mode'] == 'none') && array_key_exists ($pn, $C))
		{
			$ret[$pn] = array
			(
				'status' => 'martian_conflict',
				'left' => array_fetch ($C, $pn, array ('mode' => 'none')),
				'right' => array_fetch ($R, $pn, array ('mode' => 'none')),
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
			// Allow importing any configuration that passes basic
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
		if ($D_eq_C && $C_eq_R) // implies D == R
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
	$nsaved = $npushed = $nsaved_uplinks = 0;
	if (NULL === $vswitch = getVLANSwitchInfo ($object_id))
		throw new InvalidArgException ('object_id', $object_id, 'VLAN domain is not set for this object');
	if (! tryDBMutex (__FUNCTION__ . "-$object_id", 10))
		throw new RTGatewayError ("802.1Q sync is already active for object #$object_id");

	try
	{
		$R = getRunning8021QConfig ($vswitch['object_id']);
	}
	catch (RTGatewayError $e)
	{
		setVLANSwitchError ($object_id, E_8021Q_PULL_REMOTE_ERROR);
		throw $e;
	}

	global $dbxlink;
	$dbxlink->beginTransaction();
	$vswitch = getVLANSwitchInfo ($object_id, 'FOR UPDATE');
	$D = getStored8021QConfig ($vswitch['object_id'], 'desired');
	$Dnew = $D;
	$C = getStored8021QConfig ($vswitch['object_id'], 'cached');
	$conflict = FALSE;
	$ok_to_push = array();
	foreach (get8021QSyncOptions ($vswitch, $D, $C, $R['portdata']) as $pn => $port)
	{
		// always update cache with new data from switch
		switch ($port['status'])
		{
		case 'ok_to_merge':
			// FIXME: this can be logged
			upd8021QPort ('cached', $vswitch['object_id'], $pn, $port['both'], $C[$pn]);
			break;
		case 'ok_to_delete':
			$nsaved += del8021QPort ($vswitch['object_id'], $pn);
			unset ($Dnew[$pn]);
			break;
		case 'ok_to_add':
			$nsaved += add8021QPort ($vswitch['object_id'], $pn, $port['right']);
			$Dnew[$pn] = $port['right'];
			break;
		case 'delete_conflict':
		case 'merge_conflict':
		case 'add_conflict':
		case 'martian_conflict':
			$conflict = TRUE;
			break;
		case 'ok_to_pull':
			// FIXME: this can be logged
			$nsaved += upd8021QPort ('desired', $vswitch['object_id'], $pn, $port['right'], $D[$pn]);
			upd8021QPort ('cached', $vswitch['object_id'], $pn, $port['right'], $C[$pn]);
			$Dnew[$pn] = $port['right'];
			break;
		case 'ok_to_push_with_merge':
			upd8021QPort ('cached', $vswitch['object_id'], $pn, $port['right'], $C[$pn]);
			// fall through
		case 'ok_to_push':
			$ok_to_push[$pn] = $port['left'];
			break;
		}
	}
	// redo uplinks if some changes were pulled
	if ($nsaved)
	{
		$domain_vlanlist = getDomainVLANList ($vswitch['domain_id']);
		$Dnew = apply8021QOrder ($vswitch, $Dnew);
		// Take new "desired" configuration and derive uplink port configuration
		// from it. Then cancel changes to immune VLANs and save resulting
		// changes (if any left).
		$new_uplinks = filter8021QChangeRequests ($domain_vlanlist, $Dnew, produceUplinkPorts ($domain_vlanlist, $Dnew, $vswitch['object_id']));
		$nsaved_uplinks += replace8021QPorts ('desired', $vswitch['object_id'], $Dnew, $new_uplinks);
	}

	$out_of_sync = FALSE;
	$errno = E_8021Q_NOERROR;
	if ($nsaved + $nsaved_uplinks || count ($ok_to_push))
		$out_of_sync = TRUE;
	if ($conflict)
	{
		$errno = E_8021Q_VERSION_CONFLICT;
		$out_of_sync = TRUE;
	}
	setVLANSwitchError ($object_id, $errno);

	$mutex_rev = $vswitch['mutex_rev'];
	if ($vswitch['out_of_sync'] == "yes" && ! $out_of_sync)
		detouchVLANSwitch ($object_id, $mutex_rev);
	elseif ($vswitch['out_of_sync'] == "no" && $out_of_sync)
	{
		touchVLANSwitch ($object_id);
		++$mutex_rev;
	}
	$dbxlink->commit();

	if ($out_of_sync && $do_push)
	{
		try
		{
			$npushed += exportSwitch8021QConfig ($vswitch, $R, $ok_to_push);
		}
		catch (RTGatewayError $r)
		{
			setVLANSwitchError ($object_id, E_8021Q_PUSH_REMOTE_ERROR);
			callHook ('pushErrorHandler', $object_id, $r);
			throw $r;
		}

		// update cache for ports deployed
		$dbxlink->beginTransaction();
		replace8021QPorts ('cached', $vswitch['object_id'], $R['portdata'], $ok_to_push);
		setVLANSwitchError ($object_id, E_8021Q_NOERROR);
		detouchVLANSwitch ($object_id, $mutex_rev);
		$dbxlink->commit();
	}
	// start downlink work only after unlocking current object to make deadlocks less likely to happen
	// TODO: only process changed uplink ports
	if ($nsaved_uplinks)
		initiateUplinksReverb ($vswitch['object_id'], $new_uplinks);
	return $conflict ? FALSE : $nsaved + $npushed + $nsaved_uplinks;
}

function strerror8021Q ($errno)
{
	$errstr = array
	(
		E_8021Q_VERSION_CONFLICT => 'pull failed due to version conflict',
		E_8021Q_PULL_REMOTE_ERROR => 'pull failed due to remote error',
		E_8021Q_PUSH_REMOTE_ERROR => 'push failed due to remote error',
		E_8021Q_SYNC_DISABLED => 'sync disabled by operator',
	);
	return array_fetch ($errstr, $errno, "unknown error code ${errno}");
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
	$domain_vlanlist = getDomainVLANList ($vswitch['domain_id']);
	// aplly VST to the smallest set necessary
	$requested_changes = apply8021QOrder ($vswitch, $requested_changes);
	$before = getStored8021QConfig ($object_id, 'desired', array_keys ($requested_changes));
	$changes_to_save = array();
	// first filter by wrt_vlans constraint
	foreach ($requested_changes as $pn => $requested)
		if (array_key_exists ($pn, $before) && $requested['vst_role'] == 'downlink')
			$changes_to_save[$pn] = array
			(
				'vst_role' => 'downlink',
				'mode' => 'trunk',
				'allowed' => filterVLANList ($requested['allowed'], $requested['wrt_vlans']),
				'native' => 0,
			);
	// immune VLANs filter
	foreach (filter8021QChangeRequests ($domain_vlanlist, $before, $changes_to_save) as $pn => $finalconfig)
		$nsaved += upd8021QPort ('desired', $vswitch['object_id'], $pn, $finalconfig, $before[$pn]);
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
	// Filter and regroup all requests (regardless of how many will succeed)
	// to end up with no more, than one execution per remote object.
	$upstream_config = array();
	foreach (getObjectPortsAndLinks ($object_id, FALSE) as $portinfo)
		if
		(
			$portinfo['linked'] &&
			array_key_exists ($portinfo['name'], $uplink_ports)
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

// returns the first port from $ports that is connected and its name equals to $name
function findConnectedPort ($ports, $name)
{
	foreach ($ports as $portinfo)
		if ($portinfo['linked'] && $portinfo['name'] == $name)
			return $portinfo;
}

// checks if the desired config of all uplink/downlink ports of that switch, and
// his neighbors, equals to the recalculated config. If not,
// sets the recalculated configs as desired and puts switches into out-of-sync state.
// Returns an array with object_id as key and portname subkey
function recalc8021QPorts ($switch_id)
{
	$ret = array
	(
		'switches' => 0,
		'ports' => 0,
	);
	$vswitch = getVLANSwitchInfo ($switch_id);
	if (! $vswitch)
		return $ret;

	$ports = array(); // only linked ports appear here
	foreach (getObjectPortsAndLinks ($switch_id, FALSE) as $portinfo)
		if ($portinfo['linked'])
			$ports[$portinfo['name']] = $portinfo;

	$order = apply8021QOrder ($vswitch, getStored8021QConfig ($switch_id, 'desired', array_keys ($ports)));

	$self_processed = FALSE;

	// calculate remote uplinks and copy them to local downlinks
	foreach ($ports as $portinfo)
		if
		(
			isset ($order[$portinfo['name']]) &&
			$order[$portinfo['name']]['vst_role'] == 'downlink'
		)
		{
			// if there is a link with remote side type 'uplink', use its vlan mask
			$remote_pn = $portinfo['remote_name'];
			$remote_vswitch = getVLANSwitchInfo ($portinfo['remote_object_id']);
			if (! $remote_vswitch)
				continue;
			$n = apply8021qChangeRequest ($remote_vswitch['object_id'], array(), FALSE, NULL, array($portinfo['remote_name'] => ''));
			$ret['switches'] += $n ? 1 : 0;
			$ret['ports'] += $n;
			if ($n > 1)
				$self_processed = TRUE;
		}

	// if no connected downlinks found, re-calculate local uplinks
	// (otherwise the remote switch has already called apply8021qChangeRequest for us)
	if ($self_processed)
		$ret['switches'] += 1;
	elseif ($ret['ports'] == 0)
	{
		$n = apply8021qChangeRequest ($switch_id, array(), FALSE, NULL, array());
		$ret['switches'] += $n ? 1 : 0;
		$ret['ports'] += $n;
	}
	return $ret;
}

// This function takes 802.1q order and the order of corresponding remote uplink port.
// It returns assotiative array with single row. Key = $portname, value - produced port
// order based on $order, and having vlan list replaced based on $uplink_order, but filtered.
function produceDownlinkPort ($domain_vlanlist, $portname, $order, $uplink_order)
{
	$new_order = array ($portname => $order[$portname]);
	$new_order[$portname]['mode'] = 'trunk';
	$new_order[$portname]['allowed'] = filterVLANList ($uplink_order['allowed'], $new_order[$portname]['wrt_vlans']);
	$new_order[$portname]['native'] = 0;
	return filter8021QChangeRequests ($domain_vlanlist, $order, $new_order);
}

function detectVLANSwitchQueue ($vswitch)
{
	if ($vswitch['out_of_sync'] == 'no' && $vswitch['last_errno'] != E_8021Q_SYNC_DISABLED)
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
		return 'disabled';
	}
	return '';
}

function get8021QDeployQueues()
{
	global $dqtitle;
	$ret = array();
	foreach (array_keys ($dqtitle) as $qcode)
		$ret[$qcode] = array();
	foreach (getVLANSwitches() as $object_id)
	{
		$vswitch = getVLANSwitchInfo ($object_id);
		if ('' != $qcode = detectVLANSwitchQueue ($vswitch))
			$ret[$qcode][] = $vswitch;
	}
	return $ret;
}

function acceptable8021QConfig ($port)
{
	switch ($port['mode'])
	{
	case 'trunk':
		return $port['native'] == 0 || in_array ($port['native'], $port['allowed']);
	case 'access':
		return $port['native'] > 0 && count ($port['allowed']) == 1 &&
			in_array ($port['native'], $port['allowed']);
	default:
		return FALSE;
	}
}

function nativeVlanChangePermitted ($pn, $from_vid, $to_vid, $op = NULL)
{
	$before = array ($pn => array (
		'mode' => 'access',
		'native' => $from_vid,
		'allowed' => array ($from_vid),
	));
	$changes = array ($pn => array (
		'mode' => 'access',
		'native' => $to_vid,
		'allowed' => array ($to_vid),
	));

	return count (authorize8021QChangeRequests ($before, $changes, $op)) != 0;
}

function authorize8021QChangeRequests ($before, $changes, $op = NULL)
{
	if (NULL !== $ret = callHook ('authorize8021QChangeRequests_hook', $before, $changes, $op))
		return $ret;
	global $script_mode;
	if (isset ($script_mode) && $script_mode)
		return $changes;
	$ret = array();
	foreach ($changes as $pn => $change)
	{
		foreach (array_diff ($before[$pn]['allowed'], $change['allowed']) as $removed_id)
			if (!permitted (NULL, NULL, $op, array (array ('tag' => '$fromvlan_' . $removed_id), array ('tag' => '$vlan_' . $removed_id))))
				continue 2; // next port
		foreach (array_diff ($change['allowed'], $before[$pn]['allowed']) as $added_id)
			if (!permitted (NULL, NULL, $op, array (array ('tag' => '$tovlan_' . $added_id), array ('tag' => '$vlan_' . $added_id))))
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

// Not an equivalent of the above but related.
function parsePortIIFOIF ($port_type)
{
	if (preg_match ('/^([[:digit:]]+)-([[:digit:]]+)$/', $port_type, $matches))
		return array ($matches[1], $matches[2]);
	if (preg_match ('/^([[:digit:]]+)$/', $port_type, $matches))
		return array (1, $matches[1]);
	throw new InvalidArgException ('port_type', $port_type, 'format error');
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
		$additional .= ($additional == '' ? '' : ' '). "class='$a_class'";

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

	$prefix_diff = strcmp ($porta['prefix'], $portb['prefix']);

	// concatenation of 0..(n-1) numeric indices
	$a_parent = $porta['idx_parent'];
	$b_parent = $portb['idx_parent'];

	$index_diff = 0;
	for ($i = 0; $i < $porta['numidx']; $i++)
	{
		if ($i >= $portb['numidx'])
		{
			$index_diff = 1; // a > b
			break;
		}
		if ($porta['index'][$i] != $portb['index'][$i])
		{
			$index_diff = $porta['index'][$i] - $portb['index'][$i];
			break;
		}
	}
	if ($index_diff == 0 && $porta['numidx'] < $portb['numidx'])
		$index_diff = -1; // a < b

	// compare by portname fields
	if ($prefix_diff != 0 && ($porta['numidx'] <= 1 || $portb['numidx'] <= 1)) // if index count is lte 1, sort by prefix
	{
		$ret = $porta['numidx'] - $portb['numidx'];
		if ($ret == 0)
			$ret = $prefix_diff;
	}
	// if index count > 1 and ports have different prefixes in intersecting index sections, sort by prefix
	elseif ($prefix_diff != 0 && $a_parent != '' && $a_parent == $b_parent)
		$ret = $prefix_diff;
	// if indices are not equal, sort by index
	elseif ($index_diff != 0)
		$ret = $index_diff;
	// if all of name fields are equal, compare by some additional port fields
	elseif ($porta['iif_id'] != $portb['iif_id'])
		$ret = $porta['iif_id'] - $portb['iif_id'];
	elseif (0 != $result = strcmp ($porta['label'], $portb['label']))
		$ret = $result;
	elseif (0 != $result = strcmp ($porta['l2address'], $portb['l2address']))
		$ret = $result;
	elseif ($porta['id'] != $portb['id'])
		$ret = $porta['id'] - $portb['id'];

	return ($ret > 0) - ($ret < 0);
}

// Sort provided port list in a way based on natural. For example,
// switches can have ports:
// * fa0/1~48, gi0/1~4 (in this case 'gi' should come after 'fa'
// * fa1, gi0/1~48, te1/49~50 (type matters, then index)
// * gi5/1~3, te5/4~5 (here index matters more, than type)
// This implementation makes port type (prefix) matter for all
// interfaces that have less than 2 indices, but for other ports
// their indices matter more than type (unless there is a clash
// of indices).
// When $name_in_value is TRUE, port name determines as $plist[$key]['name']
// Otherwise portname is the key of $plist
function sortPortList ($plist, $name_in_value = FALSE)
{
	$ret = array();
	$to_sort = array();
	$prefix_re = '/^([^0-9]*)[0-9].*$/';
	foreach ($plist as $pkey => $pvalue)
	{
		$pn = $name_in_value ? $pvalue['name'] : $pkey;
		$numbers = preg_split ('/[^0-9]+/', $pn, -1, PREG_SPLIT_NO_EMPTY);
		$parent = implode ('-', array_slice ($numbers, 0, count ($numbers) - 1));
		$to_sort[] = array
		(
			'key' => $pkey,
			'prefix' => preg_replace ($prefix_re, '\\1', $pn),
			'numidx' => count ($numbers),
			'index' => $numbers,
			'idx_parent' => $parent,
			'iif_id' => isset($plist[$pkey]['iif_id']) ? $plist[$pkey]['iif_id'] : 0,
			'label' => isset($plist[$pkey]['label']) ? $plist[$pkey]['label'] : '',
			'l2address' => isset($plist[$pkey]['l2address']) ? $plist[$pkey]['l2address'] : '',
			'id' => isset($plist[$pkey]['id']) ? $plist[$pkey]['id'] : 0,
			'name' => $pn,
		);
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

// return a "?, ?, ?, ... ?, ?" string consisting of N question marks
function questionMarks ($count = 0)
{
	if ($count <= 0)
		throw new InvalidArgException ('count', $count, 'must be greater than zero');
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
		if (! empty ($domains) && $object_id = searchByMgmtHostname ($terms))
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
			$summary['ipvs'] = getVServiceSearchResult ($terms);
			$summary['ipv4vs'] = getIPv4VServiceSearchResult ($terms);
			$summary['user'] = getAccountSearchResult ($terms);
			$summary['file'] = getFileSearchResult ($terms);
			$summary['rack'] = getRackSearchResult ($terms);
			$summary['row'] = getRowSearchResult ($terms);
			$summary['location'] = getLocationSearchResult ($terms);
			$summary['vlan'] = getVLANSearchResult ($terms);
		}
	}
	# Filter search results in a way in some realms to omit records that the
	# user would not be able to browse anyway.
	foreach (array ('object', 'ipv4net', 'ipv6net', 'ipv4rspool', 'ipv4vs', 'ipvs', 'file', 'rack', 'row', 'location') as $realm)
		if (isset ($summary[$realm]))
			foreach ($summary[$realm] as $key => $record)
				if (! isolatedPermission ($realm, 'default', spotEntity ($realm, $record['id'])))
					unset ($summary[$realm][$key]);
	// clear empty search result realms
	return array_filter ($summary, 'count');
}

// returns URL to redirect to, or NULL if $result_type is unknown
function buildSearchRedirectURL ($result_type, $record)
{
	global $pageno_by_etype, $page;
	$id = isset ($record['id']) ? $record['id'] : NULL;
	$params = array();
	switch ($result_type)
	{
		case 'ipv4addressbydq':
		case 'ipv6addressbydq':
		case 'ipv4addressbydescr':
		case 'ipv6addressbydescr':
			$address = getIPAddress ($record['ip']);
			if (count ($address['allocs']) == 1 && isIPAddressEmpty ($address, array ('allocs')))
			{
				$next_page = 'object';
				$id = $address['allocs'][0]['object_id'];
				$params['hl_ip'] = ip_format ($record['ip']);
			}
			elseif (count ($address['vsglist'] + $address['vslist']) && isIPAddressEmpty ($address, array ('vslist', 'vsglist')))
			{
				$next_page = 'ipaddress';
				$id = ip_format ($record['ip']);
			}
			else
			{
				$next_page = strlen ($record['ip']) == 16 ? 'ipv6net' : 'ipv4net';
				$id = isset ($record['net_id']) ? $record['net_id'] : getIPAddressNetworkId ($record['ip']);
				$params['hl_ip'] = ip_format ($record['ip']);
			}
			break;
		case 'vlan':
			$next_page = 'vlan';
			$id = $record['id'];
			break;
		default:
			if (! isset ($pageno_by_etype[$result_type]))
				return NULL;
			$next_page = $pageno_by_etype[$result_type];
			if ($result_type == 'object')
				if (isset ($record['by_port']) && 1 == count ($record['by_port']))
				{
					$found_ports_ids = array_keys ($record['by_port']);
					$params['hl_port_id'] = $found_ports_ids[0];
				}
			break;
	}
	if (array_key_exists ($next_page, $page) && isset ($page[$next_page]['bypass']))
		$key = $page[$next_page]['bypass'];
	if (! isset ($key) || ! isset ($id))
		return NULL;
	$params[$key] = $id;
	if (isset ($_REQUEST['last_tab']) && isset ($_REQUEST['last_page']) && $next_page == $_REQUEST['last_page'])
		$next_tab = assertStringArg('last_tab');
	return buildRedirectURL ($next_page, isset ($next_tab) ? $next_tab : 'default', $params);
}

// This works like explode() with space as a separator with the added difference
// that anything in double quotes is returned as a single word.
function parseSearchTerms ($terms)
{
	$ret = array();
	if (mb_substr_count ($terms, '"') % 2 != 0)
		throw new InvalidArgException ('terms', $terms, 'contains odd number of quotes');
	$state = 'whitespace';
	$buffer = '';
	$len = mb_strlen ($terms);
	for ($i = 0; $i < $len; $i++)
	{
		$c = mb_substr ($terms, $i, 1);
		switch ($state)
		{
		case 'whitespace':
			switch ($c)
			{
			case ' ':
				break; // nom-nom
			case '"':
				$buffer = '';
				$state = 'quoted_string';
				break;
			default:
				$buffer = $c;
				$state = 'word';
			}
			break;

		case 'word':
			switch ($c)
			{
			case '"':
				throw new InvalidArgException ('terms', $terms, 'punctuation error');
			case ' ':
				$ret[] = $buffer;
				$buffer = '';
				$state = 'whitespace';
				break;
			default:
				$buffer .= $c;
			}
			break;

		case 'quoted_string':
			switch ($c)
			{
			case '"':
				if (trim ($buffer) == '')
					throw new InvalidArgException ('terms', $terms, 'punctuation error');
				$ret[] = trim ($buffer);
				$buffer = '';
				$state = 'whitespace';
				// FIXME: this does not detect missing whitespace that would be reasonable
				// to expect between the closing quote and the next token, if any.
				break;
			default:
				$buffer .= $c;
			}
			break;
		}
	}
	if ($buffer != '')
		$ret[] = $buffer;

	return $ret;
}

// Take a parse tree and figure out if it is a valid payload or not.
// Depending on that return either NULL or an array filled with the load
// of that expression.
define('PARSER_ABI_VER', 2);
function spotPayload ($text, $reqtype = 'SYNT_CODETEXT')
{
	require_once 'code.php';
	try
	{
		$parser = new RackCodeParser();
		$tree = $parser->parse ($text, $reqtype == 'SYNT_EXPR' ? 'expr' : 'prog');
		return array ('result' => 'ACK', 'ABI_ver' => PARSER_ABI_VER, 'load' => $tree);
	}
	catch (RCParserError $e)
	{
		$msg = $e->getMessage();
		if ($reqtype != 'SYNT_EXPR' || $e->lineno != 1)
			$msg .= ", line {$e->lineno}";
		return array ('result' => 'NAK', 'ABI_ver' => PARSER_ABI_VER, 'load' => $msg);
	}
}

// Top-level wrapper for most of the code in this file. Get a text, return a parse tree
// (or error message).
function getRackCode ($text)
{
	if ($text == '')
		return array ('result' => 'NAK', 'ABI_ver' => PARSER_ABI_VER, 'load' => 'The RackCode text was found empty in ' . __FUNCTION__);
	$text = str_replace ("\r", '', $text) . "\n";
	$synt = spotPayload ($text, 'SYNT_CODETEXT');
	if ($synt['result'] != 'ACK')
		return $synt;
	// An empty sentence list is semantically valid, yet senseless,
	// so checking intermediate result once more won't hurt.
	if (!count ($synt['load']))
		return array ('result' => 'NAK', 'ABI_ver' => PARSER_ABI_VER, 'load' => 'Empty parse tree found in ' . __FUNCTION__);
	return $synt;
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
	global $script_mode, $message_buffering;
	if ($direct_rendering)
		echo '<div class="msg_' . $type . '">' . $message . '</div>';
	elseif (isset ($script_mode) && $script_mode && !$message_buffering)
	{
		if ($type == 'warning' || $type == 'error')
			file_put_contents ('php://stderr', strtoupper ($type) . ': ' . strip_tags ($message) . "\n");
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

// Works only in $script_mode == TRUE
function setMessageBuffering ($state)
{
	global $message_buffering;
	$message_buffering = $state;
}

function flushMessageBuffer()
{
	global $log_messages, $script_mode;

	if (!isset ($script_mode) || !$script_mode)
		return;

	$code_str_map = array
	(
		100 => 'ERROR',
		200 => 'WARNING',
	);

	foreach ($log_messages as $line)
		if (isset ($code_str_map[$line['c']]))
		{
			$type = $code_str_map[$line['c']];
			$message = strip_tags (implode ("\n", $line['a']));
			file_put_contents ('php://stderr', $type . ': ' . $message . "\n");
		}
	$log_messages = array();
}

function clearMessageBuffer()
{
	global $log_messages;
	$log_messages = array();
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
	return ($port['iif_id'] != 1 || preg_match('/Base|LACP/i', $port['oif_name']));
}

function loadConfigDefaults()
{
	$ret = loadConfigCache();
	if (!count ($ret))
		throw new RackTablesError ('Failed to load configuration from the database.', RackTablesError::INTERNAL);
	foreach ($ret as $varname => &$row)
	{
		$row['is_altered'] = 'no';
		if ($row['vartype'] == 'uint')
			$row['varvalue'] = 0 + $row['varvalue'];
		$row['defaultvalue'] = $row['varvalue'];
	}
	return $ret;
}

function alterConfigWithUserPreferences()
{
	global $configCache;
	global $remote_username;
	foreach (loadUserConfigCache($remote_username) as $key => $row)
		if ($configCache[$key]['is_userdefined'] == 'yes')
		{
			$configCache[$key]['varvalue'] = $row['varvalue'];
			$configCache[$key]['is_altered'] = 'yes';
		}
}

// Returns true if varname has a different value or varname is new
function isConfigVarChanged ($varname, $varvalue)
{
	global $configCache;
	if (!isset ($configCache))
		throw new RackTablesError ('configuration cache is unavailable', RackTablesError::INTERNAL);
	if ($varname == '')
		throw new InvalidArgException('varname', $varname, 'Empty variable name');
	if (!isset ($configCache[$varname]))
		return TRUE;
	if ($configCache[$varname]['vartype'] == 'uint')
		return $configCache[$varname]['varvalue'] !== 0 + $varvalue;
	else
		return $configCache[$varname]['varvalue'] !== $varvalue;
}

// This function depends on init.php to have the cache array initialized.
function getConfigVar ($varname)
{
	global $configCache;
	if (! isset ($configCache))
		throw new RackTablesError ('configuration cache is unavailable', RackTablesError::INTERNAL);
	if (! array_key_exists ($varname, $configCache))
		throw new InvalidArgException ('varname', $varname, 'no such configuration variable');
	return $configCache[$varname]['varvalue'];
}

// return portinfo array if object has a port with such name, or NULL
// in strict mode the resulting port name is always equal to the $portname.
// in non-strict mode names are compared using shortenIfName()
function getPortinfoByName (&$object, $portname, $strict_mode = TRUE)
{
	if (! isset ($object['ports']))
		$object['ports'] = getObjectPortsAndLinks ($object['id']);
	if (! $strict_mode)
	{
		$breed = detectDeviceBreed ($object['id']);
		$portname = shortenIfName ($portname, $breed);
	}
	$ret = NULL;
	foreach ($object['ports'] as $portinfo)
		if ($portname == ($strict_mode ? $portinfo['name'] : shortenIfName ($portinfo['name'], $breed)))
		{
			$ret = $portinfo;
			if ($ret['linked'])
				break;
		}
		elseif (isset ($ret))
			break;
	return $ret;
}

// exclude location-related object types
function withoutLocationTypes ($objtypes)
{
	global $location_obj_types;
	return array_diff_key ($objtypes, array_fill_keys ($location_obj_types, 0));
}

# For the given object ID return a getSelect-suitable list of object types
# compatible with the object's attributes that have an assigned value in
# AttributeValue (no assigned values mean full compatibility). Being compatible
# with an attribute means having a record in AttributeMap (with the same chapter
# ID, if the attribute is dictionary-based). This knowledge is required to allow
# the user changing object type ID in a way that leaves data in AttributeValue
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
	foreach (withoutLocationTypes (readChapter (CHAP_OBJTYPE, 'o')) as $test_id => $text)
	{
		foreach ($used as $attr)
		{
			$app = $map[$attr['id']]['application'];
			if
			(
				(NULL === $appidx = scanArrayForItem ($app, 'objtype_id', $test_id)) ||
				($attr['type'] == 'dict' && $attr['chapter_id'] != $app[$appidx]['chapter_no'])
			)
				continue 2; // next type ID
		}
		$ret[$test_id] = $text;
	}
	return $ret;
}

// Gets the timestamp and returns human-friendly short message describing the time difference
// between the current system time and the specified timestamp (like '2d 5h ago')
function formatAgeTimestamp ($timestamp)
{
	return formatAgeSeconds (time() - $timestamp);
}

// For backward compatibility.
function formatAge ($timestamp)
{
	return formatAgeTimestamp ($timestamp);
}

function formatAgeSeconds ($seconds)
{
	switch (TRUE)
	{
		case $seconds < 1:
			return 'just now';
		case $seconds < 60:
			return "${seconds}s" . ' ago';
		case $seconds <= 300:
			$mins = intval ($seconds / 60);
			$secs = $seconds % 60;
			return ($secs ? "{$mins}min ${secs}s" : "{$mins}min") . ' ago';
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
	while ($line != '')
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
// if $uplinks_filter is not empty, changes only those uplink ports that are keys of this array
// NOTE: this function is calling itself through initiateUplinksReverb. It is important that
// the call to initiateUplinksReverb is outside of DB transaction scope.
function apply8021qChangeRequest ($switch_id, $changes, $verbose = TRUE, $mutex_rev = NULL, $uplinks_filter = array())
{
	global $dbxlink;
	$dbxlink->beginTransaction();
	try
	{
		if (NULL === $vswitch = getVLANSwitchInfo ($switch_id, 'FOR UPDATE'))
			throw new InvalidArgException ('object_id', $switch_id, 'VLAN domain is not set for this object');
		if (isset ($mutex_rev) && $vswitch['mutex_rev'] != $mutex_rev)
			throw new InvalidRequestArgException ('mutex_rev', $mutex_rev, 'expired form data');
		$after = $before = apply8021QOrder ($vswitch, getStored8021QConfig ($vswitch['object_id'], 'desired'));
		$domain_vlanlist = getDomainVLANList ($vswitch['domain_id']);
		$changes = filter8021QChangeRequests
		(
			$domain_vlanlist,
			$before,
			apply8021QOrder ($vswitch, $changes)
		);
		$desired_ports_count = count ($changes);
		$changes = authorize8021QChangeRequests ($before, $changes);
		if (count ($changes) < $desired_ports_count)
			showWarning (sprintf ("Permission denied to change %d ports", $desired_ports_count - count ($changes)));
		foreach ($changes as $port_name => $port)
			$after[$port_name] = $port;
		$new_uplinks = filter8021QChangeRequests ($domain_vlanlist, $after, produceUplinkPorts ($domain_vlanlist, $after, $vswitch['object_id']));
		if ($uplinks_filter)
			$new_uplinks = array_intersect_key ($new_uplinks, $uplinks_filter);
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
	$nsaved_downlinks = initiateUplinksReverb ($vswitch['object_id'], $new_uplinks);
	// instant deploy to that switch if configured
	$done = 0;
	if ($npulled + $nsaved_uplinks > 0 && getConfigVar ('8021Q_INSTANT_DEPLOY') == 'yes')
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
function fillIPNetsCorrelation (&$nets, $max_depth = 0)
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
				// skip the network if max_depth exceeded
				if ($max_depth && count ($stack) > $max_depth)
					continue 2;
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
			if ($a == ($a & $bmask) && 0 >= strcmp ($last_a, $b))
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

// returns TRUE if all of the fields set by constructIPAddress are empty
function isIPAddressEmpty ($addrinfo, $except_fields = array())
{
	// string fields
	$check_fields = array ('name', 'comment');
	foreach (array_diff ($check_fields, $except_fields) as $field)
		if (array_key_exists ($field, $addrinfo) && $addrinfo[$field] != '')
			return FALSE;

	// "boolean" fields
	if (! in_array ('reserved', $except_fields) && $addrinfo['reserved'] != 'no')
		return FALSE;

	// array fields
	$check_fields = array ('allocs', 'rsplist', 'vslist', 'vsglist');
	if (strlen ($addrinfo['ip_bin']) == 4)
		$check_fields = array_merge ($check_fields, array ('inpf', 'outpf'));
	foreach (array_diff ($check_fields, $except_fields) as $field)
		if (array_key_exists ($field, $addrinfo) && is_array ($addrinfo[$field]) && count ($addrinfo[$field]) > 0)
			return FALSE;
	return TRUE;
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
			array_key_exists ($ip, $netinfo['addrlist']) &&
			$netinfo['addrlist'][$ip]['name'] == $comment &&
			$netinfo['addrlist'][$ip]['reserved'] == 'yes' &&
			isIPAddressEmpty ($netinfo['addrlist'][$ip], array ('name', 'reserved'))
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

// FIXME: Remove this function at some point because it is a reimplementation
// of array_diff_key().
// returns array of key-value pairs from array $a such that keys are not present in $b
function array_sub ($a, $b)
{
	$ret = array();
	foreach ($a as $key => $value)
		if (! array_key_exists($key, $b))
			$ret[$key] = $value;
	return $ret;
}

// returns the requested element value or the default value if not found
function array_fetch ($array, $key, $default_value)
{
	return array_key_exists ($key, $array) ? $array[$key] : $default_value;
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
		throw new RacktablesError ("unknown ophandler injection method '$method'", RackTablesError::INTERNAL);
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
		if ($ret_i != '' || ! isset ($ret))
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
		throw new RacktablesError ("unknown tabhandler injection method '$method'", RackTablesError::INTERNAL);
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

	// if we are trying to register on the built-in function, push it to the stack
	if (empty ($hooks_stack[$hook_name]) && is_callable ($hook_name))
		array_push ($hooks_stack[$hook_name], $hook_name);

	if ($method == 'before')
		array_unshift ($hooks_stack[$hook_name], $callback);
	elseif ($method == 'after')
		array_push ($hooks_stack[$hook_name], $callback);
	elseif ($method == 'chain')
		array_push ($hooks_stack[$hook_name], '!' . $callback);
	else
		throw new InvalidRequestArgException ('method', $method, "Invalid hook method");
}

// hook handlers dispatcher. registerHook leaves 'universalHookHandler' in $hook
function universalHookHandler()
{
	global $hook_propagation_stop;
	if (! isset ($hook_propagation_stop))
		$hook_propagation_stop = array();
	array_unshift ($hook_propagation_stop, FALSE);
	global $hooks_stack;
	$ret = NULL;
	$bk_params = func_get_args();
	$hook_name = array_shift ($bk_params);
	if (! array_key_exists ($hook_name, $hooks_stack) || ! is_array ($hooks_stack[$hook_name]))
		throw new InvalidRequestArgException ('hooks_stack["' . $hook_name . '"]', $hooks_stack[$hook_name]);
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
		if ($hook_propagation_stop[0])
			break;
	}
	array_shift ($hook_propagation_stop);
	return $ret;
}

// call this from custom hook registered by registerHook
// to prevent the rest of the hooks to run
function stopHookPropagation()
{
	global $hook_propagation_stop;
	if ($hook_propagation_stop)
		$hook_propagation_stop[0] = TRUE;
}

function arePortTypesCompatible ($oif1, $oif2)
{
	static $map = NULL;
	if (! isset ($map))
	{
		$map = array();
		foreach (getPortOIFCompat() as $item)
			$map[$item['type1']][$item['type2']] = 1;
	}
	return isset ($map[$oif1][$oif2]);
}

function arePortsCompatible ($portinfo_a, $portinfo_b)
{
	return arePortTypesCompatible ($portinfo_a['oif_id'], $portinfo_b['oif_id']);
}

// returns HTML-formatted link to the given entity
function mkCellA ($cell, $title = NULL)
{
	global $pageno_by_etype;
	if (! isset ($pageno_by_etype[$cell['realm']]))
		throw new RackTablesError ("Internal structure error in array \$pageno_by_etype. Page for realm '${cell['realm']}' is not set", RackTablesError::INTERNAL);
	$cell_page = $pageno_by_etype[$cell['realm']];
	$cell_key = $cell[$cell['realm'] == 'user' ? 'user_id' : 'id'];
	if ($title === NULL)
		switch ($cell['realm'])
		{
			case 'object':
			case 'ipv4vs':
			case 'ipv4net':
			case 'ipv6net':
				$title = formatEntityName ($cell);
				break;
			default:
				$title = formatRealmName ($cell['realm']) . ' ' . formatEntityName ($cell);
				break;
		}
	return mkA ($title, $cell_page, $cell_key);
}

// Returns a list of entities of a given realm, like listCells.
// An optional $varname is the name of config option with constraint in RackCode.
function listConstraint ($realm, $varname = '')
{
	$wideList = listCells ($realm);
	if ($varname != '' && ('' != $filter = getConfigVar ($varname)))
	{
		$expr = compileExpression ($filter);
		if (! $expr)
			return array();
		$wideList = filterCellList ($wideList, $expr);
	}
	return $wideList;
}

// Return a simple object list w/o related information, so that the returned value
// can be directly used by printSelect(). An optional argument is the name of config
// option with constraint in RackCode.
function getNarrowObjectList ($varname = '')
{
	return formatEntityList (listConstraint ('object', $varname));
}

// takes an array of cells,
// returns an array indexed by cell id, values are simple text representation of a cell.
// Intended to pass its return value to printSelect routine.
function formatEntityList ($list)
{
	$ret = array();
	foreach ($list as $entity)
		$ret[$entity['id']] = formatEntityName ($entity);
	asort ($ret);
	return $ret;
}

function formatEntityName ($entity)
{
	$ret = '';
	switch ($entity['realm'])
	{
		case 'object':
			$ret = $entity['dname'];
			break;
		case 'ipv4vs':
			$ret = $entity['name'] . ($entity['name'] != '' ? ' ' : '') . '(' . $entity['dname'] . ')';
			break;
		case 'ipv4net':
		case 'ipv6net':
			$ret = $entity['ip'] . '/' . $entity['mask'];
			break;
		case 'user':
			$ret = $entity['user_name'];
			break;
		case 'ipvs':
		case 'ipv4rspool':
		case 'file':
		case 'rack':
		case 'row':
		case 'location':
			$ret = $entity['name'];
			break;
	}
	if ($ret == '')
		$ret = '[unnamed] #' . $entity['id'];
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

// returns true either if given domains are the same
// or if one is a group and other is its member
function sameDomains ($domain_id_1, $domain_id_2)
{
	if ($domain_id_1 == $domain_id_2)
		return TRUE;
	static $cache = array();
	if (! isset ($cache[$domain_id_1]))
		$cache[$domain_id_1] = getDomainGroupMembers ($domain_id_1);
	if (! isset ($cache[$domain_id_2]))
		$cache[$domain_id_2] = getDomainGroupMembers ($domain_id_2);
	return in_array ($domain_id_1, $cache[$domain_id_2]) || in_array ($domain_id_2, $cache[$domain_id_1]);
}

// Checks if 802.1Q port uplink/downlink feature is misconfigured.
// Returns FALSE if 802.1Q port role/linking is wrong, TRUE otherwise.
function checkPortRole ($vswitch, $portinfo, $port_name, $port_order)
{
	if (! $portinfo || ! $portinfo['linked'])
		return TRUE; // not linked port


	// find linked port with the same name
	if ($port_name != $portinfo['name'])
		return FALSE; // typo in local port name

	$local_auto = ($port_order['vst_role'] == 'uplink' || $port_order['vst_role'] == 'downlink') ?
		$port_order['vst_role'] :
		FALSE;
	$remote_vswitch = getVLANSwitchInfo ($portinfo['remote_object_id']);
	if (! $remote_vswitch)
		return ! $local_auto;

	$remote_pn = $portinfo['remote_name'];
	$remote_ports = apply8021QOrder ($remote_vswitch, getStored8021QConfig ($remote_vswitch['object_id'], 'desired', array ($remote_pn)));
	if (! array_key_exists($remote_pn, $remote_ports))
		// linked auto-port must have corresponding remote 802.1Q port
		return
			! $local_auto &&
			! isset ($remote_ports[shortenIfName ($remote_pn, NULL, $portinfo['remote_object_id'])]); // typo in remote port name
	$remote = $remote_ports[$remote_pn];

	$remote_auto = ($remote['vst_role'] == 'uplink' || $remote['vst_role'] == 'downlink') ?
		$remote['vst_role'] :
		FALSE;

	if (! $remote_auto && ! $local_auto)
		return TRUE;
	if ($remote_auto && $local_auto && $local_auto != $remote_auto && sameDomains ($vswitch['domain_id'], $remote_vswitch['domain_id']))
		return TRUE; // auto-calc link ends must belong to the same domain
	return FALSE;
}

# Convert InvalidArgException to InvalidRequestArgException with a choice of
# replacing the reference to the failed argument or leaving it unchanged.
#
# DEPRECATED, use InvalidArgException::newIRAE()
function convertToIRAE ($iae, $override_argname = NULL)
{
	if (! ($iae instanceof InvalidArgException))
		throw new InvalidArgException ('iae', '(object)', 'not an instance of InvalidArgException class');
	return $iae->newIRAE ($override_argname);
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
	$ret = mktime
	(
		$tmp['tm_hour'],       # 0~23
		$tmp['tm_min'],        # 0~59
		$tmp['tm_sec'],        # 0~59
		$tmp['tm_mon'] + 1,    # 0~11 -> 1~12
		$tmp['tm_mday'],       # 1~31
		$tmp['tm_year'] + 1900 # 0~n -> 1900~n
	);
	# PHP UNIX time has a wider (at least on 64-bit systems) range than the unsigned
	# 32-bit integer type RackTables allocates in the database for UNIX time.
	if ($ret < 0)
		throw new InvalidArgException ('s', $s, 'is before 1970-01-01 00:00:00 UTC');
	if ($ret >= 0xFFFFFFFF)
		throw new InvalidArgException ('s', $s, 'is on or after 2106-02-07 06:28:15 UTC');
	return $ret;
}

# Produce a human-readable clue, such as 'YYYY-MM-DD' for '%Y-%m-%d'.
function datetimeFormatHint ($format)
{
	$subst = array
	(
		# leave ISO-8601:1988 week-numbering years (%g, %G) alone
		'%y' => 'YY',
		'%Y' => 'YYYY',
		'%m' => 'MM',
		'%d' => 'DD',
	);
	return str_replace (array_keys ($subst), $subst, $format);
}

// Return TRUE, if the object belongs to specified type and has
// specified attribute belonging to the given set of values.
function checkTypeAndAttribute ($object_id, $type_id, $attr_id, $values)
{
	$object = spotEntity ('object', $object_id);
	if ($object['objtype_id'] == $type_id)
		foreach (getAttrValues ($object_id) as $record)
			if ($record['id'] == $attr_id && in_array ($record['key'], $values))
				return TRUE;
	return FALSE;
}

// The old name, remove at a later point.
function nullEmptyStr ($str)
{
	return nullIfEmptyStr ($str);
}

function nullIfEmptyStr ($str)
{
	return $str != '' ? $str : NULL;
}

function nullIfFalse ($x)
{
	return $x === FALSE ? NULL : $x;
}

function nullIfZero ($x)
{
	return $x == 0 ? NULL : $x;
}

function printLocationChildrenSelectOptions ($location, $parent_id, $location_id = NULL, $level = 0)
{
	$self = __FUNCTION__;
	$level++;
	foreach ($location['kids'] as $subLocation)
	{
		if ($subLocation['id'] == $location_id)
			continue;
		echo "<option value=${subLocation['id']}";
		if ($subLocation['id'] == $parent_id)
			echo ' selected';
		echo '>' . str_repeat ('&raquo; ', $level) . "${subLocation['name']}</option>\n";
		if ($subLocation['kidc'] > 0)
			$self ($subLocation, $parent_id, $location_id, $level);
	}
}

function validTagName ($s, $allow_autotag = FALSE)
{
	return preg_match (TAGNAME_REGEXP, $s) ||
		($allow_autotag && preg_match (AUTOTAGNAME_REGEXP, $s));
}

// returns html string with parent location names
// link: if each name should be wrapped in an href
function getLocationTrail ($location_id, $link = TRUE, $spacer = ' : ')
{
	// XXX: $location_tree is an array, not a tree
	static $location_tree = array ();
	if (count ($location_tree) == 0)
		foreach (listCells ('location') as $location)
			$location_tree[$location['id']] = array ('parent_id' => $location['parent_id'], 'name' => $location['name']);

	// prepend parent location(s) to given location string
	$names = array();
	$id = $location_id;
	$locationIdx = 0;
	while (isset ($id))
	{
		if ($locationIdx == 20)
		{
			showWarning ('Warning: There is likely a circular reference in the location tree.');
			break;
		}
		$name = $location_tree[$id]['name'];
		array_unshift ($names, $link ? mkA ($name, 'location', $id) : $name);
		$id = $location_tree[$id]['parent_id'];
		$locationIdx++;
	}
	return implode ($spacer, $names);
}

function cmp_array_sizes ($a, $b)
{
	return numCompare (count ($a), count ($b));
}

// parses the value of MGMT_PROTOS config variable and returns an array
// indexed by protocol name with corresponding textual RackCode values
function getMgmtProtosConfig ($ignore_cache = FALSE)
{
	static $cache = NULL;
	if (!$ignore_cache && isset ($cache))
		return $cache;

	$cache = array();
	$config = getConfigVar ('MGMT_PROTOS');
	foreach (explode (';', $config) as $item)
	{
		$item = trim ($item);
		if ($item == '')
			continue;
		if (preg_match('/^(\S+)\s*:\s*(.*)$/', $item, $m))
			$cache[$m[1]] = $m[2];
	}
	return $cache;
}

// returns compiled RackCode expression or NULL if syntax error occurs
// caches the result in $exprCache global
function compileExpression ($code, $do_cache_lookup = TRUE)
{
	global $exprCache;
	if (! is_array ($exprCache))
		$exprCache = array();
	if ($do_cache_lookup && array_key_exists($code, $exprCache))
		return $exprCache[$code];

	$ret = NULL;
	$parse = spotPayload ($code, 'SYNT_EXPR');
	if ($parse['result'] == 'ACK')
		$ret = $parse['load'];
	$exprCache[$code] = $ret;
	return $ret;
}

// a caching wrapper around detectDeviceBreed and shortenIfName
function shortenPortName ($if_name, $object_id)
{
	static $breed_cache = array();
	if (! array_key_exists($object_id, $breed_cache))
		$breed_cache[$object_id] = detectDeviceBreed ($object_id);
	$breed = $breed_cache[$object_id];
	return $breed == '' ? $if_name : shortenIfName ($if_name, $breed);
}

// returns an array of IP ranges of size $dst_mask > $netinfo['mask'], or array ($netinfo)
function splitNetworkByMask ($netinfo, $dst_mask)
{
	$self = __FUNCTION__;
	if ($netinfo['mask'] >= $dst_mask)
		return array ($netinfo);

	return array_merge
	(
		$self (constructIPRange ($netinfo['ip_bin'], $netinfo['mask'] + 1), $dst_mask),
		$self (constructIPRange (ip_last ($netinfo), $netinfo['mask'] + 1), $dst_mask)
	);
}

// this function is used both to remember and to retrieve the last created entity's ID
// it stores given id in the static var, and returns the stored value is called without args
// used in plugins to make additional work on created entity in the chained ophandler
// returns an array of realm-ID pairs
function lastCreated ($realm = NULL, $id = NULL)
{
	static $last_ids = array();
	if (isset ($realm) && isset ($id))
		$last_ids[] = array('realm' => $realm, 'id' => $id);
	return $last_ids;
}

// returns last id of a given type from lastCreated() result array
function getLastCreatedId ($realm)
{
	foreach (array_reverse (lastCreated()) as $item)
		if ($item['realm'] == $realm)
			return $item['id'];
}

function formatPatchCableHeapAsPlainText ($heap)
{
	$text = "${heap['amount']} pcs: [${heap['end1_connector']}] ${heap['pctype']} [${heap['end2_connector']}]";
	if ($heap['description'] != '')
		$text .=  " (${heap['description']})";
	return niftyString ($text, 512, FALSE);
}

// takes a list of structures and the field name in those structures.
// returns a two-dimentional list indexed by the value of the given field
// the subsequent index value is taken from the index of the original $list.
function groupBy ($list, $group_field)
{
	$ret = array();
	if (! is_array ($list))
		throw new InvalidArgException ('list', $list, 'must be an array');
	foreach ($list as $index => $item)
	{
		if (! is_array ($item))
			throw new InvalidArgException ("list[${index}]", $item, 'must be an array');
		$key = '';
		if (isset ($item[$group_field]))
			$key = (string) $item[$group_field];
		$ret[$key][$index] = $item;
	}
	return $ret;
}

// returns the associative $array sorted by its keys
// sort order is taken from the $order array:
// $array[i] is arranged with $array[j] conforming to
// numeric comparison of $order[i] and $order[j].
function customKsort ($array, $order)
{
	$ret = array();
	foreach ($array as $key => $value)
		$ret[$key] = isset ($order[$key]) ? $order[$key] : array_last ($order) + 1;

	asort ($ret, SORT_NUMERIC);
	foreach (array_keys ($ret) as $key)
		$ret[$key] = $array[$key];
	return $ret;
}

// RT uses PHP sessions on demand and tries to minimize session lifetime
// to allow concurrent operations for a single user.
// You should call session_commit after each call of this function.
function startSession()
{
	if (is_callable ('session_status'))
	{
		if (session_status() != PHP_SESSION_ACTIVE)
			session_start();
	}
	else
	{
		// compatibility mode for PHP prior to 5.4.0
		$old_errorlevel = error_reporting (E_ALL & ~E_NOTICE);
		session_start();
		error_reporting ($old_errorlevel);
	}
}

// loads session data. Use if you need to only read from _SESSION.
function startROSession()
{
	startSession();
	session_commit();
}

// removes a VLAN from ports that contain it, but keep a VLAN amongst others in a range
function pinpointDeleteVlan ($domain_id, $vlan_id)
{
	$ret = 0;
	$vlan_ck = $domain_id . '-' . $vlan_id;
	$domain_vlanlist = getDomainVLANList ($domain_id);
	$used_ports = getVLANConfiguredPorts ($vlan_ck);
	foreach ($used_ports as $object_id => $port_list)
	{
		$vswitch = getVLANSwitchInfo ($object_id); // get mutex rev
		$D = getStored8021QConfig ($object_id, 'desired', $port_list);
		$changes = array();
		foreach ($port_list as $pn)
		{
			if (! isset ($D[$pn]))
				continue;
			// remove vlan from a port only if its range does not contain foreign vlans
			foreach (listToRanges ($D[$pn]['allowed']) as $range)
				if (matchVLANFilter ($vlan_id, array ($range)))
				{
					for ($i = $range['from']; $i <= $range['to']; ++$i)
						if (! isset ($domain_vlanlist[$i]))
							continue 3; // keep vlan, skip to next port
				}
			// remove vlan from port
			$conf = $D[$pn];
			$conf['allowed'] = array_diff ($conf['allowed'], array ($vlan_id));
			if ($conf['mode'] == 'access')
				$conf['mode'] = 'trunk';
			if ($conf['native'] == $vlan_id)
				$conf['native'] = 0;
			$changes[$pn] = $conf;
		}
		if ($changes)
			$ret += apply8021qChangeRequest ($object_id, $changes, FALSE, $vswitch['mutex_rev']);
	}

	return $ret;
}

function etypeByPageno ($pg = NULL)
{
	global $etype_by_pageno, $pageno;
	if ($pg === NULL)
		$pg = $pageno;
	if (! array_key_exists ($pg, $etype_by_pageno))
		throw new RackTablesError ('key not found', RackTablesError::INTERNAL);
	return $etype_by_pageno[$pg];
}

function requireExtraFiles ($reqlist)
{
	global $pageno, $tabno;

	function requireListOfFiles ($x)
	{
		if (! is_array ($x))
			require_once $x;
		else
			foreach ($x as $filename)
				require_once $filename;
	}

	if (array_key_exists ("${pageno}-${tabno}", $reqlist))
		requireListOfFiles ($reqlist["${pageno}-${tabno}"]);
	if (array_key_exists ("${pageno}-*", $reqlist))
		requireListOfFiles ($reqlist["${pageno}-*"]);
}

// Return the text as a list of lines after removing CRs, empty lines
// and leading/trailing whitespace.
function textareaCooked ($text)
{
	$ret = dos2unix ($text);
	$ret = explode ("\n", $ret);
	$ret = array_map ('trim', $ret);
	$ret = array_diff ($ret, array (''));
	$ret = array_values ($ret); // reindex to be consistent enough for the tests
	return $ret;
}

// Used to fill $desiredPorts argument for syncObjectPorts.
// Call this function just like commitAddPort except the first argument
function addDesiredPort (&$desiredPorts, $port_name, $port_type_id, $port_label, $port_l2address)
{
	list ($iif_id, $oif_id) = parsePortIIFOIF ($port_type_id);
	$desiredPorts["{$port_name}-{$iif_id}"] = array (
		'name' => $port_name,
		'iif_id' => $iif_id,
		'oif_id' => $oif_id,
		'label' => $port_label,
		'l2address' => $port_l2address,
	);
}

// synchronizes the list of the object ports to the desired list of ports
// $desiredPorts is a list of ports in getObjectPortsAndLinks format.
// required port fields are name, iif_id, oif_id, label and l2address.
// $desiredPorts can be filled by addDesiredPort function using commitAddPort format
function syncObjectPorts ($object_id, $desiredPorts)
{
	global $dbxlink;
	$to_delete = $to_update = $real_ports = array();

	// The check that does not require access to the database goes first.
	foreach (array_keys ($desiredPorts) as $k)
		$desiredPorts[$k]['l2address'] = l2AddressForDatabase ($desiredPorts[$k]['l2address']);

	// Further processing must be done with exclusive access to the table. Even when the
	// only changes requested are to add ports w/o MAC addresses or to update existing
	// ports in a way that does not introduce new MAC addresses, it is impossible to
	// tell reliably which ports require which actions without locking the table first.
	$dbxlink->exec ('LOCK TABLES Port WRITE, PortLog WRITE, Link READ');
	foreach (getObjectPortsAndLinksTerse ($object_id) as $port)
	{
		$key = "{$port['name']}-{$port['iif_id']}";
		if (! array_key_exists ($key, $desiredPorts))
		{
			$to_delete[] = $port;
			continue;
		}
		if ($port['l2address'] != $desiredPorts[$key]['l2address'] || $port['label'] != $desiredPorts[$key]['label'])
			$to_update[$key] = $port;
		$real_ports[$key] = 1;
	}
	$to_add = array_diff_key ($desiredPorts, $real_ports);

	try
	{
		assertUniqueL2Addresses (reduceSubarraysToColumn (array_merge ($to_update, $to_add), 'l2address'), $object_id);
		// Make the actual changes.
		foreach ($to_delete as $port)
			if ($port['link_count'] != 0)
				showWarning (sprintf ("Port %s should be deleted, but it's used", formatPort ($port)));
			else
				usePreparedDeleteBlade ('Port', array ('id' => $port['id']));
		foreach ($to_update as $key => $port)
			commitUpdatePortReal
			(
				$object_id,
				$port['id'],
				$port['name'],
				$port['iif_id'],
				$port['oif_id'],
				$desiredPorts[$key]['label'],
				$desiredPorts[$key]['l2address'],
				$port['reservation_comment']
			);
		foreach ($to_add as $key => $port)
			commitAddPortReal
			(
				$object_id,
				$port['name'],
				$port['iif_id'],
				$port['oif_id'],
				$port['label'],
				$port['l2address']
			);
	}
	catch (Exception $e)
	{
		$dbxlink->exec ('UNLOCK TABLES');
		throw $e;
	}

	$dbxlink->exec ('UNLOCK TABLES');
	showSuccess (sprintf ('Added ports: %u, changed: %u, deleted: %u', count ($to_add), count ($to_update), count ($to_delete)));
}

?>
