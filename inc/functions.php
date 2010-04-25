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

// Entity type by page number mapping is 1:1 atm, but may change later.
$etype_by_pageno = array
(
	'ipv4net' => 'ipv4net',
	'ipv4rspool' => 'ipv4rspool',
	'ipv4vs' => 'ipv4vs',
	'object' => 'object',
	'rack' => 'rack',
	'user' => 'user',
	'file' => 'file',
);

// Rack thumbnail image width summands: "front", "interior" and "rear" elements w/o surrounding border.
$rtwidth = array
(
	0 => 9,
	1 => 21,
	2 => 9
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

// This function assures that specified argument was passed
// and is a number greater than zero.
function assertUIntArg ($argname, $allow_zero = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is missing');
	if (!is_numeric ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is not a number');
	if ($_REQUEST[$argname] < 0)
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is less than zero');
	if (!$allow_zero and $_REQUEST[$argname] === 0)
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is zero');
}

// This function assures that specified argument was passed
// and is a non-empty string.
function assertStringArg ($argname, $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is missing');
	if (!is_string ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is not a string');
	if (!$ok_if_empty and !strlen ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is an empty string');
}

function assertBoolArg ($argname, $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is missing');
	if (!is_string ($_REQUEST[$argname]) or $_REQUEST[$argname] != 'on')
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is not a string');
	if (!$ok_if_empty and !strlen ($_REQUEST[$argname]))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is an empty string');
}

function assertIPv4Arg ($argname, $ok_if_empty = FALSE)
{
	assertStringArg ($argname, $ok_if_empty);
	if (strlen ($_REQUEST[$argname]) and long2ip (ip2long ($_REQUEST[$argname])) !== $_REQUEST[$argname])
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'parameter is not a valid ipv4 address');
}

function assertPCREArg ($argname)
{
	assertStringArg ($argname, TRUE); // empty pattern is Ok
	if (FALSE === preg_match ($_REQUEST[$argname], 'test'))
		throw new InvalidRequestArgException($argname, $_REQUEST[$argname], 'PCRE validation failed');
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
function markAllSpans (&$rackData = NULL)
{
	if ($rackData == NULL)
	{
		showWarning ('Invalid rackData', __FUNCTION__);
		return;
	}
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
// in the provided string, an empty string for an empty string or NULL for error.
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
		case (preg_match (RE_L2_IPCFG, $string) or preg_match (RE_L2_WWN_HYPHEN, $string)):
			return str_replace ('-', '', $string);
		default:
			return NULL;
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
function getPrevIDforRack ($row_id = 0, $rack_id = 0)
{
	if ($row_id <= 0 or $rack_id <= 0)
	{
		showWarning ('Invalid arguments passed', __FUNCTION__);
		return NULL;
	}
	$rackList = listCells ('rack', $row_id);
	doubleLink ($rackList);
	if (isset ($rackList[$rack_id]['prev_key']))
		return $rackList[$rack_id]['prev_key'];
	return NULL;
}

function getNextIDforRack ($row_id = 0, $rack_id = 0)
{
	if ($row_id <= 0 or $rack_id <= 0)
	{
		showWarning ('Invalid arguments passed', __FUNCTION__);
		return NULL;
	}
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

function sortByName ($a, $b)
{
	$result = sortTokenize ($a['name'], $b['name']);
	if ($result != 0)
		return $result;
	if ($a['iif_id'] != $b['iif_id'])
		return $a['iif_id'] - $b['iif_id'];
	$result = strcmp ($a['label'], $b['label']);
	if ($result != 0)
		return $result;
	$result = strcmp ($a['l2address'], $b['l2address']);
	if ($result != 0)
		return $result;
	return $a['id'] - $b['id'];
}

function sortObjectAddressesAndNames ($a, $b)
{
	$objname_cmp = sortTokenize($a['object_name'], $b['object_name']);
	if ($objname_cmp == 0)
	{
		$name_a = (isset ($a['port_name'])) ? $a['port_name'] : '';
		$name_b = (isset ($b['port_name'])) ? $b['port_name'] : '';
		$objname_cmp = sortTokenize($name_a, $name_b);
		if ($objname_cmp == 0)
			sortTokenize($a['ip'], $b['ip']);
		return $objname_cmp;
	}
	return $objname_cmp;
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
// This function parses the line and returns text suitable for either A
// (rendering <A HREF>) or O (for <OPTION>).
function parseWikiLink ($line, $which)
{
	if (preg_match ('/^\[\[.+\]\]$/', $line) == 0)
	{
		// always strip the marker for A-data, but let cookOptgroup()
		// do this later (otherwise it can't sort groups out)
		if ($which == 'a')
			return preg_replace ('/^.+%GSKIP%/', '', preg_replace ('/^(.+)%GPASS%/', '\\1 ', $line));
		else
			return $line;
	}
	$line = preg_replace ('/^\[\[(.+)\]\]$/', '$1', $line);
	$s = explode ('|', $line);
	$o_value = trim ($s[0]);
	if ($which == 'o')
		return $o_value;
	$o_value = preg_replace ('/^.+%GSKIP%/', '', preg_replace ('/^(.+)%GPASS%/', '\\1 ', $o_value));
	$a_value = trim ($s[1]);
	return "<a href='${a_value}'>${o_value}</a>";
}

// FIXME: should this be saved as "P-data"?
function execGMarker ($line)
{
	return preg_replace ('/^.+%GSKIP%/', '', preg_replace ('/^(.+)%GPASS%/', '\\1 ', $line));
}

// rackspace usage for a single rack
// (T + W + U) / (height * 3 - A)
function getRSUforRack ($data = NULL)
{
	if ($data == NULL)
	{
		showWarning ('Invalid argument', __FUNCTION__);
		return NULL;
	}
	$counter = array ('A' => 0, 'U' => 0, 'T' => 0, 'W' => 0, 'F' => 0);
	for ($unit_no = $data['height']; $unit_no > 0; $unit_no--)
		for ($locidx = 0; $locidx < 3; $locidx++)
			$counter[$data[$unit_no][$locidx]['state']]++;
	return ($counter['T'] + $counter['W'] + $counter['U']) / ($counter['T'] + $counter['W'] + $counter['U'] + $counter['F']);
}

// Same for row.
function getRSUforRackRow ($rowData = NULL)
{
	if ($rowData === NULL)
	{
		showWarning ('Invalid argument', __FUNCTION__);
		return NULL;
	}
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
	// HCF
	return NULL;
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

function serializeTags ($chain, $baseurl = '')
{
	$comma = '';
	$ret = '';
	foreach ($chain as $taginfo)
	{
		$ret .= $comma .
			($baseurl == '' ? '' : "<a href='${baseurl}cft[]=${taginfo['id']}'>") .
			$taginfo['tag'] .
			($baseurl == '' ? '' : '</a>');
		$comma = ', ';
	}
	return $ret;
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

// Universal autotags generator, a complementing function for loadEntityTags().
// Bypass key isn't strictly typed, but interpreted depending on the realm.
function generateEntityAutoTags ($cell)
{
	$ret = array();
	switch ($cell['realm'])
	{
		case 'rack':
			$ret[] = array ('tag' => '$rackid_' . $cell['id']);
			$ret[] = array ('tag' => '$any_rack');
			break;
		case 'object':
			$ret[] = array ('tag' => '$id_' . $cell['id']);
			$ret[] = array ('tag' => '$typeid_' . $cell['objtype_id']);
			$ret[] = array ('tag' => '$any_object');
			if (validTagName ('$cn_' . $cell['name'], TRUE))
				$ret[] = array ('tag' => '$cn_' . $cell['name']);
			if (!strlen ($cell['rack_id']))
				$ret[] = array ('tag' => '$unmounted');
			if (!$cell['nports'])
				$ret[] = array ('tag' => '$portless');
			if ($cell['asset_no'] == '')
				$ret[] = array ('tag' => '$no_asset_tag');
			break;
		case 'ipv4net':
			$ret[] = array ('tag' => '$ip4netid_' . $cell['id']);
			$ret[] = array ('tag' => '$ip4net-' . str_replace ('.', '-', $cell['ip']) . '-' . $cell['mask']);
			for ($i = 8; $i < 32; $i++)
			{
				// these conditions hit 1 to 3 times per each i
				if ($cell['mask'] >= $i)
					$ret[] = array ('tag' => '$masklen_ge_' . $i);
				if ($cell['mask'] <= $i)
					$ret[] = array ('tag' => '$masklen_le_' . $i);
				if ($cell['mask'] == $i)
					$ret[] = array ('tag' => '$masklen_eq_' . $i);
			}
			$ret[] = array ('tag' => '$any_ip4net');
			$ret[] = array ('tag' => '$any_net');
			break;
		case 'ipv4vs':
			$ret[] = array ('tag' => '$ipv4vsid_' . $cell['id']);
			$ret[] = array ('tag' => '$any_ipv4vs');
			$ret[] = array ('tag' => '$any_vs');
			break;
		case 'ipv4rspool':
			$ret[] = array ('tag' => '$ipv4rspid_' . $cell['id']);
			$ret[] = array ('tag' => '$any_ipv4rsp');
			$ret[] = array ('tag' => '$any_rsp');
			break;
		case 'user':
			// {$username_XXX} autotag is generated always, but {$userid_XXX}
			// appears only for accounts, which exist in local database.
			$ret[] = array ('tag' => '$username_' . $cell['user_name']);
			if (isset ($cell['user_id']))
				$ret[] = array ('tag' => '$userid_' . $cell['user_id']);
			break;
		case 'file':
			$ret[] = array ('tag' => '$fileid_' . $cell['id']);
			$ret[] = array ('tag' => '$any_file');
			break;
		default: // HCF!
			break;
	}
	// {$tagless} doesn't apply to users
	switch ($cell['realm'])
	{
		case 'rack':
		case 'object':
		case 'ipv4net':
		case 'ipv4vs':
		case 'ipv4rspool':
		case 'file':
			if (!count ($cell['etags']))
				$ret[] = array ('tag' => '$untagged');
			break;
		default:
			break;
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
	$pmap = array
	(
		'accounts' => 'userlist',
		'rspools' => 'ipv4rsplist',
		'rspool' => 'ipv4rsp',
		'vservices' => 'ipv4vslist',
		'vservice' => 'ipv4vs',
		'objects' => 'depot',
		'objgroup' => 'depot',
	);
	$tmap = array();
	$tmap['objects']['newmulti'] = 'addmore';
	$tmap['objects']['newobj'] = 'addmore';
	$tmap['object']['switchvlans'] = 'livevlans';
	$tmap['object']['slb'] = 'editrspvs';
	$tmap['object']['portfwrd'] = 'nat4';
	$tmap['object']['network'] = 'ipv4';
	if (isset ($pmap[$pageno]))
		redirectUser ($pmap[$pageno], $tabno);
	if (isset ($tmap[$pageno][$tabno]))
		redirectUser ($pageno, $tmap[$pageno][$tabno]);
	// check if we accidentaly got on a dynamic tab that shouldn't be shown for this object
	if
	(
		isset ($trigger[$pageno][$tabno]) and
		!strlen (call_user_func ($trigger[$pageno][$tabno]))
	)
		redirectUser ($pageno, 'default');
}

function prepareNavigation() {
	global
		$pageno,
		$tabno;

	$pageno = (isset ($_REQUEST['page'])) ? $_REQUEST['page'] : 'index';

// Special handling of tab number to substitute the "last" index where applicable.
// Always show explicitly requested tab, substitute the last used name in case
// it is awailable, fall back to the default one.

	if (isset ($_REQUEST['tab'])) {
		$tabno = $_REQUEST['tab'];
	} elseif (basename($_SERVER['PHP_SELF']) == 'index.php' and getConfigVar ('SHOW_LAST_TAB') == 'yes' and isset ($_SESSION['RTLT'][$pageno])) {
		redirectUser ($pageno, $_SESSION['RTLT'][$pageno]);
	} else {
		$tabno = 'default';
	}
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

function getCellFilter ()
{
	global $sic;
	if (isset ($_REQUEST['tagfilter']) and is_array ($_REQUEST['tagfilter']))
	{
		$_REQUEST['cft'] = $_REQUEST['tagfilter'];
		unset ($_REQUEST['tagfilter']);
	}
	$ret = array
	(
		'tagidlist' => array(),
		'tnamelist' => array(),
		'pnamelist' => array(),
		'andor' => '',
		'text' => '',
		'extratext' => '',
		'expression' => array(),
		'urlextra' => '', // Just put text here and let makeHref call urlencode().
	);
	switch (TRUE)
	{
	case (!isset ($_REQUEST['andor'])):
		$andor2 = getConfigVar ('FILTER_DEFAULT_ANDOR');
		break;
	case ($_REQUEST['andor'] == 'and'):
	case ($_REQUEST['andor'] == 'or'):
		$ret['andor'] = $andor2 = $_REQUEST['andor'];
		$ret['urlextra'] .= '&andor=' . $ret['andor'];
		break;
	default:
		showWarning ('Invalid and/or switch value in submitted form', __FUNCTION__);
		return NULL;
	}
	$andor1 = '';
	// Both tags and predicates, which don't exist, should be
	// handled somehow. Discard them silently for now.
	if (isset ($_REQUEST['cft']) and is_array ($_REQUEST['cft']))
	{
		global $taglist;
		foreach ($_REQUEST['cft'] as $req_id)
			if (isset ($taglist[$req_id]))
			{
				$ret['tagidlist'][] = $req_id;
				$ret['tnamelist'][] = $taglist[$req_id]['tag'];
				$ret['text'] .= $andor1 . '{' . $taglist[$req_id]['tag'] . '}';
				$andor1 = ' ' . $andor2 . ' ';
				$ret['urlextra'] .= '&cft[]=' . $req_id;
			}
	}
	if (isset ($_REQUEST['cfp']) and is_array ($_REQUEST['cfp']))
	{
		global $pTable;
		foreach ($_REQUEST['cfp'] as $req_name)
			if (isset ($pTable[$req_name]))
			{
				$ret['pnamelist'][] = $req_name;
				$ret['text'] .= $andor1 . '[' . $req_name . ']';
				$andor1 = ' ' . $andor2 . ' ';
				$ret['urlextra'] .= '&cfp[]=' . $req_name;
			}
	}
	// Extra text comes from TEXTAREA and is easily screwed by standard escaping function.
	if (isset ($sic['cfe']))
	{
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
	$finaltext = implode (' ' . $andor2 . ' ', $finaltext);
	if (strlen ($finaltext))
	{
		$parse = spotPayload ($finaltext, 'SYNT_EXPR');
		$ret['expression'] = $parse['result'] == 'ACK' ? $parse['load'] : NULL;
		// It's not quite fair enough to put the blame of the whole text onto
		// non-empty "extra" portion of it, but it's the only user-generated portion
		// of it, thus the most probable cause of parse error.
		if (strlen ($ret['extratext']))
			$ret['extraclass'] = $parse['result'] == 'ACK' ? 'validation-success' : 'validation-error';
	}
	return $ret;
}

// Return an empty message log.
function emptyLog ()
{
	return array
	(
		'v' => 2,
		'm' => array()
	);
}

// Return a message log consisting of only one message.
function oneLiner ($code, $args = array())
{
	$ret = emptyLog();
	$ret['m'][] = count ($args) ? array ('c' => $code, 'a' => $args) : array ('c' => $code);
	return $ret;
}

// Merge message payload from two message logs given and return the result.
function mergeLogs ($log1, $log2)
{
	$ret = emptyLog();
	$ret['m'] = array_merge ($log1['m'], $log2['m']);
	return $ret;
}

function validTagName ($s, $allow_autotag = FALSE)
{
	if (1 == preg_match (TAGNAME_REGEXP, $s))
		return TRUE;
	if ($allow_autotag and 1 == preg_match (AUTOTAGNAME_REGEXP, $s))
		return TRUE;
	return FALSE;
}

function redirectUser ($p, $t)
{
	global $page;
	$l = "index.php?page=${p}&tab=${t}";
	if (isset ($page[$p]['bypass']) and isset ($_REQUEST[$page[$p]['bypass']]))
		$l .= '&' . $page[$p]['bypass'] . '=' . $_REQUEST[$page[$p]['bypass']];
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
function apply_macros ($macros, $subject)
{
	$ret = $subject;
	foreach ($macros as $search => $replace)
		$ret = str_replace ($search, $replace, $ret);
	return $ret;
}

function buildLVSConfig ($object_id = 0)
{
	if ($object_id <= 0)
	{
		showWarning ('Invalid argument', __FUNCTION__);
		return;
	}
	$oInfo = spotEntity ('object', $object_id);
	$lbconfig = getSLBConfig ($object_id);
	if ($lbconfig === NULL)
	{
		showWarning ('getSLBConfig() failed', __FUNCTION__);
		return;
	}
	$newconfig = "#\n#\n# This configuration has been generated automatically by RackTables\n";
	$newconfig .= "# for object_id == ${object_id}\n# object name: ${oInfo['name']}\n#\n#\n\n\n";
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
			'%RSPOOLNAME%' => $vsinfo['pool_name']
		);
		$newconfig .=  "virtual_server ${vsinfo['vip']} ${vsinfo['vport']} {\n";
		$newconfig .=  "\tprotocol ${vsinfo['proto']}\n";
		$newconfig .= apply_macros
		(
			$macros,
			lf_wrap ($vsinfo['vs_vsconfig']) .
			lf_wrap ($vsinfo['lb_vsconfig']) .
			lf_wrap ($vsinfo['pool_vsconfig'])
		);
		foreach ($vsinfo['rslist'] as $rs)
		{
			if (!strlen ($rs['rsport']))
				$rs['rsport'] = $vsinfo['vport'];
			$macros['%RSIP%'] = $rs['rsip'];
			$macros['%RSPORT%'] = $rs['rsport'];
			$newconfig .=  "\treal_server ${rs['rsip']} ${rs['rsport']} {\n";
			$newconfig .= apply_macros
			(
				$macros,
				lf_wrap ($vsinfo['vs_rsconfig']) .
				lf_wrap ($vsinfo['lb_rsconfig']) .
				lf_wrap ($vsinfo['pool_rsconfig']) .
				lf_wrap ($rs['rs_rsconfig'])
			);
			$newconfig .=  "\t}\n";
		}
		$newconfig .=  "}\n\n\n";
	}
	// FIXME: deal somehow with Mac-styled text, the below replacement will screw it up
	return str_replace ("\r", '', $newconfig);
}

// Indicate occupation state of each IP address: none, ordinary or problematic.
function markupIPv4AddrList (&$addrlist)
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

// Scan the given address list (returned by scanIPv4Space) and return a list of all routers found.
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
	{
		throw new RuntimeException('Internal error, the recurring loop lost control');
	}

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
	{
		throw new RuntimeException ('Internal error, cannot decide between left and right');
	}
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
	markupIPv4AddrList ($netinfo['addrlist']);
}

function countOwnIPv4Addresses (&$node)
{
	$toscan = array();
	$node['addrt'] = 0;
	$node['mask_bin'] = binMaskFromDec ($node['mask']);
	$node['mask_bin_inv'] = binInvMaskFromDec ($node['mask']);
	$node['db_first'] = sprintf ('%u', 0x00000000 + $node['ip_bin'] & $node['mask_bin']);
	$node['db_last'] = sprintf ('%u', 0x00000000 + $node['ip_bin'] | ($node['mask_bin_inv']));
	if (!count ($node['kids']))
	{
		$toscan[] = array ('i32_first' => $node['db_first'], 'i32_last' => $node['db_last']);
		$node['addrt'] = binInvMaskFromDec ($node['mask']) + 1;
	}
	else
		foreach ($node['kids'] as $nested)
			if (!isset ($nested['id'])) // spare
			{
				$toscan[] = array ('i32_first' => $nested['db_first'], 'i32_last' => $nested['db_last']);
				$node['addrt'] += binInvMaskFromDec ($nested['mask']) + 1;
			}
	// Don't do anything more, because the displaying function will load the addresses anyway.
	return;
	$node['addrc'] = count (scanIPv4Space ($toscan));
}

function nodeIsCollapsed ($node)
{
	return $node['symbol'] == 'node-collapsed';
}

function loadOwnIPv4Addresses (&$node)
{
	$toscan = array();
	if (!isset ($node['kids']) or !count ($node['kids']))
		$toscan[] = array ('i32_first' => $node['db_first'], 'i32_last' => $node['db_last']);
	else
		foreach ($node['kids'] as $nested)
			if (!isset ($nested['id'])) // spare
				$toscan[] = array ('i32_first' => $nested['db_first'], 'i32_last' => $nested['db_last']);
	$node['addrlist'] = scanIPv4Space ($toscan);
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

// Take a MySQL or other generic timestamp and make it prettier
function formatTimestamp ($timestamp) {
	return date('n/j/y g:iA', strtotime($timestamp));
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

function makeHref($params = array())
{
	$ret = 'index.php?';
	$first = true;
	foreach($params as $key=>$value)
	{
		if (!$first)
			$ret.='&';
		$ret .= urlencode($key).'='.urlencode($value);
		$first = false;
	}
	return $ret;
}

function makeHrefProcess($params = array())
{
	global $pageno, $tabno;
	$ret = 'process.php?';
	$first = true;
	if (!isset($params['page']))
		$params['page'] = $pageno;
	if (!isset($params['tab']))
		$params['tab'] = $tabno;
	foreach($params as $key=>$value)
	{
		if (!$first)
			$ret.='&';
		$ret .= urlencode($key).'='.urlencode($value);
		$first = false;
	}
	return $ret;
}

function makeHrefForHelper ($helper_name, $params = array())
{
	$ret = 'popup.php?helper=' . $helper_name;
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
			if (FALSE !== preg_match ("/^([^@]+)(@${object_type_id})?\$/", trim ($sieve), $regs))
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
	$ret['other'] = $therest;
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

// Tell, if a constraint from config option permits given record.
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
	return judgeCell ($cell, $parseCache[$varname]['load']);
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
	case 'user':
	case 'ipv4net':
	case 'file':
	case 'ipv4vs':
	case 'ipv4rspool':
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

// Derive a complete cell structure from the given username regardless
// if it is a local account or not.
function constructUserCell ($username)
{
	if (NULL !== ($userid = getUserIDByUsername ($username)))
		return spotEntity ('user', $userid);
	$ret = array
	(
		'realm' => 'user',
		'user_name' => $username,
		'user_realname' => '',
		'etags' => array(),
		'itags' => array(),
	);
	$ret['atags'] = generateEntityAutoTags ($ret);
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

function getVLANDomain ($vdid)
{
	$ret = getVLANDomainInfo ($vdid);
	$ret['vlanlist'] = array();
	foreach (getDomainVLANs ($vdid) as $vlan_id => $vlan_descr)
		$ret['vlanlist'][$vlan_id] = $vlan_descr;
	$ret['switchlist'] = getVLANDomainSwitches ($vdid);
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
	$tagged = array();
	foreach ($vlanport['allowed'] as $vlan_id)
		if ($vlan_id != $vlanport['native'])
			$tagged[] = $vlan_id;
	sort ($tagged);
	$ret .= $vlanport['native'] ? $vlanport['native'] : '';
	$tagged_bits = array();
	$id_from = $id_to = 0;
	foreach ($tagged as $next_id)
	{
		if ($id_to)
		{
			if ($next_id == $id_to + 1) // merge
			{
				$id_to = $next_id;
				continue;
			}
			// flush
			$tagged_bits[] = $id_from == $id_to ? $id_from : "${id_from}-${id_to}";
		}
		$id_from = $id_to = $next_id; // start next pair
	}
	// pull last pair
	if ($id_to)
		$tagged_bits[] = $id_from == $id_to ? $id_from : "${id_from}-${id_to}";
	if (count ($tagged))
		$ret .= '+' . implode (',', $tagged_bits);
	return strlen ($ret) ? $ret : 'default';
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
function formatVLANName ($vlaninfo, $plaintext = FALSE)
{
	$ret = ($plaintext ? '' : '<tt>') . 'VLAN' . $vlaninfo['vlan_id'] . ($plaintext ? '' : '</tt>');
	if (strlen ($vlaninfo['vlan_descr']))
		$ret .= ' ' . ($plaintext ? '' : '<i>') . '(' . niftyString ($vlaninfo['vlan_descr']) . ')' . ($plaintext ? '' : '</i>');
	return $ret;
}

// Read given running-config and return a list of work items in a format
// similar to the one, which importSwitch8021QConfig() understands.
function ios12ReadVLANConfig ($input)
{
	$ret = array
	(
		'vlanlist' => array(),
		'portdata' => array(),
	);
	$procfunc = 'ios12ScanTopLevel';
	foreach (explode ("\n", $input) as $line)
		$procfunc = $procfunc ($ret, $line);
	return $ret;
}

function ios12ScanTopLevel (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^interface ((Ethernet|FastEthernet|GigabitEthernet|TenGigabitEthernet)[[:digit:]]+(/[[:digit:]]+)*)$@', $line, $matches)):
		// map interface name
		$matches[1] = preg_replace ('@^Ethernet(.+)$@', 'et\\1', $matches[1]);
		$matches[1] = preg_replace ('@^FastEthernet(.+)$@', 'fa\\1', $matches[1]);
		$matches[1] = preg_replace ('@^GigabitEthernet(.+)$@', 'gi\\1', $matches[1]);
		$matches[1] = preg_replace ('@^TenGigabitEthernet(.+)$@', 'te\\1', $matches[1]);
		$work['current'] = array ('port_name' => $matches[1]);
		return 'ios12PickSwitchportCommand'; // switch to interface block reading
	case (preg_match ('/^VLAN Name                             Status    Ports$/', $line, $matches)):
		return 'ios12PickVLANCommand';
	default:
		return __FUNCTION__; // continue scan
	}
}

function ios12PickSwitchportCommand (&$work, $line)
{
	if ($line[0] != ' ') // end of interface section
	{
		$boiled = array ('port_name' => $work['current']['port_name']);
		// fill in defaults
		if (!array_key_exists ('mode', $work['current']))
			$work['current']['mode'] = 'access';
		if (!array_key_exists ('access vlan', $work['current']))
			$work['current']['access vlan'] = 1;
		if (!array_key_exists ('trunk native vlan', $work['current']))
			$work['current']['trunk native vlan'] = 1;
		if (!array_key_exists ('trunk allowed vlan', $work['current']))
			$work['current']['trunk allowed vlan'] = range (VLAN_MIN_ID, VLAN_MAX_ID);
		// save work, if it makes sense
		switch ($work['current']['mode'])
		{
		case 'access':
			$work['portdata'][$work['current']['port_name']] = array
			(
				'mode' => 'access',
				'allowed' => array ($work['current']['access vlan']),
				'native' => $work['current']['access vlan'],
			);
			break;
		case 'trunk':
			// Having configured VLAN as "native" doesn't mean anything
			// as long as it's not listed on the "allowed" line.
			$effective_native = in_array
			(
				$work['current']['trunk native vlan'],
				$work['current']['trunk allowed vlan']
			) ? $work['current']['trunk native vlan'] : 0;
			$work['portdata'][$work['current']['port_name']] = array
			(
				'mode' => 'trunk',
				'allowed' => $work['current']['trunk allowed vlan'],
				'native' => $effective_native,
			);
			break;
		default:
			// dot1q-tunnel, dynamic, private-vlan --- skip these
		}
		unset ($work['current']);
		return 'ios12ScanTopLevel';
	}
	// not yet
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^ switchport mode (.+)$@', $line, $matches)):
		$work['current']['mode'] = $matches[1];
		break;
	case (preg_match ('@^ switchport access vlan (.+)$@', $line, $matches)):
		$work['current']['access vlan'] = $matches[1];
		break;
	case (preg_match ('@^ switchport trunk native vlan (.+)$@', $line, $matches)):
		$work['current']['trunk native vlan'] = $matches[1];
		break;
	case (preg_match ('@^ switchport trunk allowed vlan add (.+)$@', $line, $matches)):
		$work['current']['trunk allowed vlan'] = array_merge
		(
			$work['current']['trunk allowed vlan'],
			iosParseVLANString ($matches[1])
		);
		break;
	case (preg_match ('@^ switchport trunk allowed vlan (.+)$@', $line, $matches)):
		$work['current']['trunk allowed vlan'] = iosParseVLANString ($matches[1]);
		break;
	default: // suppress warning on irrelevant config clause
	}
	return __FUNCTION__;
}

function ios12PickVLANCommand (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case ($line == '---- -------------------------------- --------- -------------------------------'):
		// ignore the rest of VLAN table header;
		break;
	case (preg_match ('@! END OF VLAN LIST$@', $line)):
		return 'ios12ScanTopLevel';
	case (preg_match ('@^([[:digit:]]+) {1,4}.{32} active    @', $line, $matches)):
		if (!array_key_exists ($matches[1], $work['vlanlist']))
			$work['vlanlist'][] = $matches[1];
		break;
	default:
	}
	return __FUNCTION__;
}

function iosParseVLANString ($string)
{
	$ret = array();
	foreach (explode (',', $string) as $item)
	{
		$matches = array();
		if (preg_match ('/^([[:digit:]]+)$/', $item, $matches))
			$ret[] = $matches[1];
		elseif (preg_match ('/^([[:digit:]]+)-([[:digit:]]+)$/', $item, $matches))
			$ret = array_merge ($ret, range ($matches[1], $matches[2]));
	}
	return $ret;
}

// Another finite automata to read a dialect of Foundry configuration.
function fdry5ReadVLANConfig ($input)
{
	$ret = array
	(
		'vlanlist' => array(),
		'portdata' => array(),
	);
	$procfunc = 'fdry5ScanTopLevel';
	foreach (explode ("\n", $input) as $line)
		$procfunc = $procfunc ($ret, $line);
	return $ret;
}

function fdry5ScanTopLevel (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^vlan ([[:digit:]]+)( name .+)? (by port)$@', $line, $matches)):
		if (!array_key_exists ($matches[1], $work['vlanlist']))
			$work['vlanlist'][] = $matches[1];
		$work['current'] = array ('vlan_id' => $matches[1]);
		return 'fdry5PickVLANSubcommand';
	case (preg_match ('@^interface ethernet ([[:digit:]]+/[[:digit:]]+/[[:digit:]]+)$@', $line, $matches)):
		$work['current'] = array ('port_name' => 'e' . $matches[1]);
		return 'fdry5PickInterfaceSubcommand';
	default:
		return __FUNCTION__;
	}
}

function fdry5PickVLANSubcommand (&$work, $line)
{
	if ($line[0] != ' ') // end of VLAN section
	{
		unset ($work['current']);
		return 'fdry5ScanTopLevel';
	}
	// not yet
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^ tagged (.+)$@', $line, $matches)):
		// add current VLAN to 'allowed' list of each mentioned port
		foreach (fdry5ParsePortString ($matches[1]) as $port_name)
			if (array_key_exists ($port_name, $work['portdata']))
				$work['portdata'][$port_name]['allowed'][] = $work['current']['vlan_id'];
			else
				$work['portdata'][$port_name] = array
				(
					'mode' => 'trunk',
					'allowed' => array ($work['current']['vlan_id']),
					'native' => 0, // can be updated later
				);
			$work['portdata'][$port_name]['mode'] = 'trunk';
		break;
	case (preg_match ('@^ untagged (.+)$@', $line, $matches)):
		// replace 'native' column of each mentioned port with current VLAN ID
		foreach (fdry5ParsePortString ($matches[1]) as $port_name)
		{
			if (array_key_exists ($port_name, $work['portdata']))
			{
				$work['portdata'][$port_name]['native'] = $work['current']['vlan_id'];
				$work['portdata'][$port_name]['allowed'][] = $work['current']['vlan_id'];
			}
			else
				$work['portdata'][$port_name] = array
				(
					'mode' => 'access',
					'allowed' => array ($work['current']['vlan_id']),
					'native' => $work['current']['vlan_id'],
				);
			// Untagged ports are initially assumed to be access ports, and
			// when this assumption is right, this is the final port mode state.
			// When the port is dual-mode one, this is detected and justified
			// later in "interface" section of config text.
			$work['portdata'][$port_name]['mode'] = 'access';
		}
		break;
	default: // nom-nom
	}
	return __FUNCTION__;
}

function fdry5PickInterfaceSubcommand (&$work, $line)
{
	if ($line[0] != ' ') // end of interface section
	{
		if (array_key_exists ('dual-mode', $work['current']))
		{
			if (array_key_exists ($work['current']['port_name'], $work['portdata']))
				// update existing record
				$work['portdata'][$work['current']['port_name']]['native'] = $work['current']['dual-mode'];
			else
				// add new
				$work['portdata'][$work['current']['port_name']] = array
				(
					'allowed' => array ($work['current']['dual-mode']),
					'native' => $work['current']['dual-mode'],
				);
			// a dual-mode port is always considered a trunk port
			// (but not in the IronWare's meaning of "trunk") regardless of
			// number of assigned tagged VLANs
			$work['portdata'][$work['current']['port_name']]['mode'] = 'trunk';
		}
		unset ($work['current']);
		return 'fdry5ScanTopLevel';
	}
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^ dual-mode( +[[:digit:]]+ *)?$@', $line, $matches)):
		// default VLAN ID for dual-mode command is 1
		$work['current']['dual-mode'] = strlen (trim ($matches[1])) ? trim ($matches[1]) : 1;
		break;
	default: // nom-nom
	}
	return __FUNCTION__;
}

function fdry5ParsePortString ($string)
{
	$ret = array();
	$tokens = explode (' ', trim ($string));
	while (count ($tokens))
	{
		$letters = array_shift ($tokens); // "ethe", "to"
		$numbers = array_shift ($tokens); // "x", "x/x", "x/x/x"
		switch ($letters)
		{
		case 'ethe':
			if ($prev_numbers != NULL)
				$ret[] = 'e' . $prev_numbers;
			$prev_numbers = $numbers;
			break;
		case 'to':
			$ret = array_merge ($ret, fdry5GenPortRange ($prev_numbers, $numbers));
			$prev_numbers = NULL; // no action on next token
			break;
		default: // ???
			return array();
		}
	}
	// flush delayed item
	if ($prev_numbers != NULL)
		$ret[] = 'e' . $prev_numbers;
	return $ret;
}

// Take two indices in form "x", "x/x" or "x/x/x" and return the range of
// ports spanning from the first to the last. The switch software makes it
// easier to perform, because "ethe x/x/x to y/y/y" ranges never cross
// unit/slot boundary (every index except the last remains constant).
function fdry5GenPortRange ($from, $to)
{
	$matches = array();
	if (1 !== preg_match ('@^([[:digit:]]+/)?([[:digit:]]+/)?([[:digit:]]+)$@', $from, $matches))
		return array();
	$prefix = 'e' . $matches[1] . $matches[2];
	$from_idx = $matches[3];
	if (1 !== preg_match ('@^([[:digit:]]+/)?([[:digit:]]+/)?([[:digit:]]+)$@', $to, $matches))
		return array();
	$to_idx = $matches[3];
	for ($i = $from_idx; $i <= $to_idx; $i++)
		$ret[] = $prefix . $i;
	return $ret;
}

// an implementation for Huawei syntax
function vrp53ReadVLANConfig ($input)
{
	$ret = array
	(
		'vlanlist' => array(),
		'portdata' => array(),
	);
	$procfunc = 'vrp53ScanTopLevel';
	foreach (explode ("\n", $input) as $line)
		$procfunc = $procfunc ($ret, $line);
	return $ret;
}

function vrp53ScanTopLevel (&$work, $line)
{
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^ vlan batch (.+)$@', $line, $matches)):
		foreach (vrp53ParseVLANString ($matches[1]) as $vlan_id)
			$work['vlanlist'][] = $vlan_id;
		return __FUNCTION__;
	case (preg_match ('@^interface ((GigabitEthernet|XGigabitEthernet)([[:digit:]]+/[[:digit:]]+/[[:digit:]]+))$@', $line, $matches)):
		$matches[1] = preg_replace ('@^GigabitEthernet(.+)$@', 'gi\\1', $matches[1]);
		$matches[1] = preg_replace ('@^XGigabitEthernet(.+)$@', 'xg\\1', $matches[1]);
		$work['current'] = array ('port_name' => $matches[1]);
		return 'vrp53PickInterfaceSubcommand';
	default:
		return __FUNCTION__;
	}
}

function vrp53ParseVLANString ($string)
{
	$string = preg_replace ('/ to /', '-', $string);
	$string = preg_replace ('/ /', ',', $string);
	return iosParseVLANString ($string);
}

function vrp53PickInterfaceSubcommand (&$work, $line)
{
	if ($line[0] == '#') // end of interface section
	{
		// Configuration Guide - Ethernet 3.3.4:
		// "By default, the interface type is hybrid."
		if (!array_key_exists ('link-type', $work['current']))
			$work['current']['link-type'] = 'hybrid';
		if (!array_key_exists ('allowed', $work['current']))
			$work['current']['allowed'] = array();
		if (!array_key_exists ('native', $work['current']))
			$work['current']['native'] = 0;
		switch ($work['current']['link-type'])
		{
		case 'access':
			$work['portdata'][$work['current']['port_name']] = array
			(
				'allowed' => array ($work['current']['default vlan']),
				'native' => $work['current']['default vlan'],
				'mode' => 'access',
			);
			break;
		case 'trunk':
			$work['portdata'][$work['current']['port_name']] = array
			(
				'allowed' => $work['current']['allowed'],
				'native' => 0,
				'mode' => 'trunk',
			);
			break;
		case 'hybrid':
			$work['portdata'][$work['current']['port_name']] = array
			(
				'allowed' => $work['current']['allowed'],
				'native' => $work['current']['native'],
				'mode' => 'trunk',
			);
			break;
		default: // dot1q-tunnel ?
		}
		unset ($work['current']);
		return 'vrp53ScanTopLevel';
	}
	$matches = array();
	switch (TRUE)
	{
	case (preg_match ('@^ port default vlan ([[:digit:]]+)$@', $line, $matches)):
		$work['current']['native'] = $matches[1];
		if (!array_key_exists ('allowed', $work['current']))
			$work['current']['allowed'] = array();
		if (!in_array ($matches[1], $work['current']['allowed']))
			$work['current']['allowed'][] = $matches[1];
		break;
	case (preg_match ('@^ port link-type (.+)$@', $line, $matches)):
		$work['current']['link-type'] = $matches[1];
		break;
	case (preg_match ('@^ port trunk allow-pass vlan (.+)$@', $line, $matches)):
		if (!array_key_exists ('allowed', $work['current']))
			$work['current']['allowed'] = array();
		foreach (vrp53ParseVLANString ($matches[1]) as $vlan_id)
			if (!in_array ($vlan_id, $work['current']['allowed']))
				$work['current']['allowed'][] = $vlan_id;
		break;
	default: // nom-nom
	}
	return __FUNCTION__;
}

function nxos4Read8021QConfig ($input)
{
	return $input;
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

// Get a list of VLAN management pseudo-commands and return a text
// of real vendor-specific commands, which implement the work.
// This work is done in two rounds:
// 1. For "add allowed" and "rem allowed" commands detect continuous
//    sequences of VLAN IDs and replace them with ranges of form "A-B",
//    where B>A.
// 2. Iterate over the resulting list and produce real CLI commands.
function ios12TranslatePushQueue ($queue)
{
	$compressed = array();
	$buffered = NULL;
	foreach ($queue as $item)
	{
		if ($buffered !== NULL)
		{
			if
			(
				($item['opcode'] == 'add allowed' or $item['opcode'] == 'rem allowed') and
				$item['opcode'] == $buffered['opcode'] and // same command
				$item['arg1'] == $buffered['arg1'] and // same interface
				$item['arg2'] == $buffered['arg3'] + 1 // fits into buffered range
			)
			{
				// merge and wait for next
				$buffered['arg3'] = $item['arg2'];
				continue;
			}
			// flush
			$compressed[] = array
			(
				'opcode' => $buffered['opcode'],
				'arg1' => $buffered['arg1'],
				'arg2' =>
					$buffered['arg2'] .
					($buffered['arg2'] == $buffered['arg3'] ? '' : ('-' . $buffered['arg3'])),
			);
			$buffered = NULL;
		}
		if ($item['opcode'] == 'add allowed' or $item['opcode'] == 'rem allowed')
		// engage next round
			$buffered = array
			(
				'opcode' => $item['opcode'],
				'arg1' => $item['arg1'],
				'arg2' => $item['arg2'],
				'arg3' => $item['arg2'],
			);
		else
			$compressed[] = $item; // pass through
	}
	if ($buffered !== NULL)
		// Below implies 'opcode' IN ('add allowed', 'rem allowed') and
		// a fixed structure of the buffered remainder.
		$compressed[] = array
		(
			'opcode' => $buffered['opcode'],
			'arg1' => $buffered['arg1'],
			'arg2' =>
				$buffered['arg2'] .
				($buffered['arg2'] == $buffered['arg3'] ? '' : ('-' . $buffered['arg3'])),
		);
	$ret = "configure terminal\n";
	foreach ($compressed as $cmd)
		switch ($cmd['opcode'])
		{
		case 'create VLAN':
			$ret .= "vlan ${cmd['arg1']}\nexit\n";
			break;
		case 'destroy VLAN':
			$ret .= "no vlan ${cmd['arg1']}\n";
			break;
		case 'add allowed':
			$ret .= "interface ${cmd['arg1']}\nswitchport trunk allowed vlan add ${cmd['arg2']}\nexit\n";
			break;
		case 'rem allowed':
			$ret .= "interface ${cmd['arg1']}\nswitchport trunk allowed vlan remove ${cmd['arg2']}\nexit\n";
			break;
		case 'set native':
			$ret .= "interface ${cmd['arg1']}\nswitchport trunk native vlan ${cmd['arg2']}\nexit\n";
			break;
		case 'unset native':
			$ret .= "interface ${cmd['arg1']}\nno switchport trunk native vlan ${cmd['arg2']}\nexit\n";
			break;
		case 'set access':
			$ret .= "interface ${cmd['arg1']}\nswitchport access vlan ${cmd['arg2']}\nexit\n";
			break;
		case 'unset access':
			$ret .= "interface ${cmd['arg1']}\nno switchport access vlan\nexit\n";
			break;
		case 'set mode':
			$ret .= "interface ${cmd['arg1']}\nswitchport mode ${cmd['arg2']}\n";
			if ($cmd['arg2'] == 'trunk')
				$ret .= "no switchport trunk native vlan\nswitchport trunk allowed vlan none\n";
			$ret .= "exit\n";
			break;
		}
	$ret .= "end\n";
	return $ret;
}

function fdry5TranslatePushQueue ($queue)
{
	$ret = "conf t\n";
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'create VLAN':
			$ret .= "vlan ${cmd['arg1']}\nexit\n";
			break;
		case 'destroy VLAN':
			$ret .= "no vlan ${cmd['arg1']}\n";
			break;
		case 'add allowed':
			$ret .= "vlan ${cmd['arg2']}\ntagged ${cmd['arg1']}\nexit\n";
			break;
		case 'rem allowed':
			$ret .= "vlan ${cmd['arg2']}\nno tagged ${cmd['arg1']}\nexit\n";
			break;
		case 'set native':
			$ret .= "interface ${cmd['arg1']}\ndual-mode ${cmd['arg2']}\nexit\n";
			break;
		case 'unset native':
			$ret .= "interface ${cmd['arg1']}\nno dual-mode ${cmd['arg2']}\nexit\n";
			break;
		case 'set access':
			$ret .= "vlan ${cmd['arg2']}\nuntagged ${cmd['arg1']}\nexit\n";
			break;
		case 'unset access':
			$ret .= "vlan ${cmd['arg2']}\nno untagged ${cmd['arg1']}\nexit\n";
			break;
		case 'set mode': // NOP
			break;
		}
	$ret .= "end\n";
	return $ret;
}

function vrp53TranslatePushQueue ($queue)
{
	$ret = "system-view\n";
	foreach ($queue as $cmd)
		switch ($cmd['opcode'])
		{
		case 'create VLAN':
			$ret .= "vlan ${cmd['arg1']}\nquit\n";
			break;
		case 'destroy VLAN':
			$ret .= "undo vlan ${cmd['arg1']}\n";
			break;
		case 'add allowed':
			$ret .= "interface ${cmd['arg1']}\nport trunk allow-pass vlan ${cmd['arg2']}\nquit\n";
			break;
		case 'rem allowed':
			$ret .= "interface ${cmd['arg1']}\nundo port trunk allow-pass vlan ${cmd['arg2']}\nquit\n";
			break;
		case 'set native':
		case 'set access':
			$ret .= "interface ${cmd['arg1']}\nport default vlan ${cmd['arg2']}\nquit\n";
			break;
		case 'unset native':
		case 'unset access':
			$ret .= "interface ${cmd['arg1']}\nundo port default vlan\nquit\n";
			break;
		case 'set mode':
			$modemap = array ('access' => 'access', 'trunk' => 'hybrid');
			$ret .= "interface ${cmd['arg1']}\nport link-type " . $modemap[$cmd['arg2']] . "\n";
			if ($cmd['arg2'] == 'hybrid')
				$ret .= "undo port default vlan\nundo port trunk allow-pass vlan all\n";
			$ret .= "quit\n";
			break;
		}
	$ret .= "return\n";
	return $ret;
}

// Return TRUE, if every value of A1 is present in A2 and vice versa,
// regardless of each array's sort order and indexing.
function array_values_same ($a1, $a2)
{
	return !count (array_diff ($a1, $a2)) and !count (array_diff ($a2, $a1));
}

// Use the VLAN switch template associated with the given object
// to set VST role for each port of the provided list. Return
// resulting list.
function apply8021QOrder ($vst_id, $portlist)
{
	$vst = getVLANSwitchTemplate ($vst_id);
	foreach ($vst['rules'] as $rule)
		foreach (array_keys ($portlist) as $port_name)
			if
			(
				!array_key_exists ('vst_role', $portlist[$port_name]) and
				preg_match ($rule['port_pcre'], $port_name)
			)
			{
				$portlist[$port_name]['vst_role'] = $rule['port_role'];
				$portlist[$port_name]['wrt_vlans'] = $rule['wrt_vlans'];
			}
	foreach (array_keys ($portlist) as $port_name)
		if (!array_key_exists ('vst_role', $portlist[$port_name]))
			$portlist[$port_name]['vst_role'] = 'none';
	return $portlist;
}

function apply8021QTemplate ($vst_id, $portnames)
{
	$vst = getVLANSwitchTemplate ($vst_id);
	$ret = array();
	foreach ($portnames as $port_name)
	{
		foreach ($vst['rules'] as $rule)
			if (preg_match ($rule['port_pcre'], $port_name))
			{
				$ret[$port_name] = array
				(
					'vst_role' => $rule['port_role'],
					'wrt_vlans' => $rule['wrt_vlans'],
				);
				break;
			}
		$ret[$port_name] = array
		(
			'vst_role' => 'none',
			'wrt_vlans' => '',
		);
	}
	return $ret;
}

// take port list with order applied and return uplink ports in the same format
function produceUplinkPorts ($domain_vlanlist, $portlist)
{
	$ret = array();
	$employed = array();
	foreach ($domain_vlanlist as $vlan_id => $vlan)
		if ($vlan['vlan_type'] == 'compulsory')
			$employed[] = $vlan_id;
	foreach ($portlist as $port_name => $port)
		if ($port['vst_role'] != 'uplink')
			foreach ($port['allowed'] as $vlan_id)
				if (!in_array ($vlan_id, $employed))
					$employed[] = $vlan_id;
	foreach ($portlist as $port_name => $port)
		if ($port['vst_role'] == 'uplink')
		{
			if (!mb_strlen ($port['wrt_vlans']))
				$employed_here = $employed;
			else
			{
				$employed_here = array();
				$wrt_vlans = iosParseVLANString ($port['wrt_vlans']);
				foreach ($employed as $vlan_id)
					if (in_array ($vlan_id, $wrt_vlans))
						$employed_here[] = $vlan_id;
			}
			$ret[$port_name] = array
			(
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
	$empty_port = array
	(
		'mode' => 'none',
		'allowed' => array(),
		'native' => 0,
	);
	$plan = array
	(
		'to_pull' => array(),
		'to_push' => array(),
		'in_conflict' => array(),
	);
	$all_port_names = array_unique (array_merge (array_keys ($C), array_keys ($R)));
	foreach ($all_port_names as $pn)
	{
		if (!array_key_exists ($pn, $R)) // missing from device
		{
			if (!same8021QConfigs ($D[$pn], $default_port))
				$plan['in_conflict'][] = $pn;
			else
				$plan['to_pull'][$pn] = $empty_port;
			continue;
		}
		if (!array_key_exists ($pn, $C)) // missing from DB
		{
			$plan['to_pull'][$pn] = $R[$pn];
			continue;
		}
		$D_eq_C = same8021QConfigs ($D[$pn], $C[$pn]);
		$C_eq_R = same8021QConfigs ($C[$pn], $R[$pn]);
		if ($D_eq_C and $C_eq_R) // implies D == R
			continue;
		if ($D_eq_C)
			$plan['to_pull'][$pn] = $R[$pn];
		elseif ($C_eq_R)
			$plan['to_push'][$pn] = $D[$pn];
		elseif (same8021QConfigs ($D[$pn], $R[$pn]))
			$plan['to_pull'][$pn] = $R[$pn];
		else // D != C, C != R, D != R
			$plan['in_conflict'][] = $pn;
	}
	return $plan;
}

// print part of HTML HEAD block
function printPageHeaders ()
{
	global $pageheaders;
	ksort ($pageheaders);
	foreach ($pageheaders as $s)
		echo $s . "\n";
	echo "<style type='text/css'>\n";
	foreach (array ('F', 'A', 'U', 'T', 'Th', 'Tw', 'Thw') as $statecode)
	{
		echo "td.state_${statecode} {\n";
		echo "\ttext-align: center;\n";
		echo "\tbackground-color: #" . (getConfigVar ('color_' . $statecode)) . ";\n";
		echo "\tfont: bold 10px Verdana, sans-serif;\n";
		echo "}\n\n";
	}
	echo '</style>';
}

?>
