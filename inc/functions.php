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

// Objects of some types should be explicitly shown as
// anonymous (labelless). This function is a single place where the
// decision about displayed name is made.
function displayedName ($objectData)
{
	if ($objectData['name'] != '')
		return $objectData['name'];
	elseif (in_array ($objectData['objtype_id'], explode (',', getConfigVar ('NAMEFUL_OBJTYPES'))))
		return "ANONYMOUS " . $objectData['objtype_name'];
	else
		return "[${objectData['objtype_name']}]";
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
		showError ('Invalid rackData', __FUNCTION__);
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

// This mask should allow toggling 'T' and 'W' on object's rackspace.
function applyObjectProblemMask (&$rackData)
{
	for ($unit_no = $rackData['height']; $unit_no > 0; $unit_no--)
		for ($locidx = 0; $locidx < 3; $locidx++)
			switch ($rackData[$unit_no][$locidx]['state'])
			{
				case 'T':
				case 'W':
					$rackData[$unit_no][$locidx]['enabled'] = ($rackData[$unit_no][$locidx]['object_id'] == $object_id);
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
// in getRackData(). We don't honour 'skipped' key, because
// the function is also used for thumb creation.
function markupObjectProblems (&$rackData)
{
	for ($i = $rackData['height']; $i > 0; $i--)
		for ($locidx = 0; $locidx < 3; $locidx++)
			if ($rackData[$i][$locidx]['state'] == 'T')
			{
				$object = getObjectInfo ($rackData[$i][$locidx]['object_id']);
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

function search_cmpObj ($a, $b)
{
	return ($a['score'] > $b['score'] ? -1 : 1);
}

function getObjectSearchResults ($terms)
{
	$objects = array();
	mergeSearchResults ($objects, $terms, 'name');
	mergeSearchResults ($objects, $terms, 'label');
	mergeSearchResults ($objects, $terms, 'asset_no');
	mergeSearchResults ($objects, $terms, 'barcode');
	if (count ($objects) == 1)
		usort ($objects, 'search_cmpObj');
	return $objects;
}

// This function removes all colons and dots from a string.
function l2addressForDatabase ($string)
{
	if (empty ($string))
		return 'NULL';
	$pieces = explode (':', $string);
	// This workaround is for SunOS ifconfig.
	foreach ($pieces as &$byte)
		if (strlen ($byte) == 1)
			$byte = '0' . $byte;
	// And this workaround is for PHP.
	unset ($byte);
	$string = implode ('', $pieces);
	$pieces = explode ('.', $string);
	$string = implode ('', $pieces);
	$string = strtoupper ($string);
	return "'$string'";
}

function l2addressFromDatabase ($string)
{
	switch (strlen ($string))
	{
		case 12: // Ethernet
		case 16: // FireWire
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
		showError ('Invalid arguments passed', __FUNCTION__);
		return NULL;
	}
	$rackList = getRacksForRow ($row_id);
	doubleLink ($rackList);
	if (isset ($rackList[$rack_id]['prev_key']))
		return $rackList[$rack_id]['prev_key'];
	return NULL;
}

function getNextIDforRack ($row_id = 0, $rack_id = 0)
{
	if ($row_id <= 0 or $rack_id <= 0)
	{
		showError ('Invalid arguments passed', __FUNCTION__);
		return NULL;
	}
	$rackList = getRacksForRow ($row_id);
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

// After applying usort() to a rack list we will lose original array keys.
// This function restores the keys so they are equal to rack IDs.
function restoreRackIDs ($racks)
{
	$ret = array();
	foreach ($racks as $rack)
		$ret[$rack['id']] = $rack;
	return $ret;
}

function sortTokenize ($a, $b)
{
	$aold='';
	while ($a != $aold)
	{
		$aold=$a;
		$a = ereg_replace('[^a-zA-Z0-9]',' ',$a);
		$a = ereg_replace('([0-9])([a-zA-Z])','\\1 \\2',$a);
		$a = ereg_replace('([a-zA-Z])([0-9])','\\1 \\2',$a);
	}

	$bold='';
	while ($b != $bold)
	{
		$bold=$b;
		$b = ereg_replace('[^a-zA-Z0-9]',' ',$b);
		$b = ereg_replace('([0-9])([a-zA-Z])','\\1 \\2',$b);
		$b = ereg_replace('([a-zA-Z])([0-9])','\\1 \\2',$b);
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
	return sortTokenize($a['name'], $b['name']);
}

function sortRacks ($a, $b)
{
	return sortTokenize($a['row_name'] . ': ' . $a['name'], $b['row_name'] . ': ' . $b['name']);
}

function sortEmptyPorts ($a, $b)
{
	$objname_cmp = sortTokenize($a['Object_name'], $b['Object_name']);
	if ($objname_cmp == 0)
	{
		return sortTokenize($a['Port_name'], $b['Port_name']);
	}
	return $objname_cmp;
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

function sortAddresses ($a, $b)
{
	$name_cmp = sortTokenize($a['name'], $b['name']);
	if ($name_cmp == 0)
	{
		return sortTokenize($a['ip'], $b['ip']);
	}
	return $name_cmp;
}

// This function expands port compat list into a matrix.
function buildPortCompatMatrixFromList ($portTypeList, $portCompatList)
{
	$matrix = array();
	// Create type matrix and markup compatible types.
	foreach (array_keys ($portTypeList) as $type1)
		foreach (array_keys ($portTypeList) as $type2)
			$matrix[$type1][$type2] = FALSE;
	foreach ($portCompatList as $pair)
		$matrix[$pair['type1']][$pair['type2']] = TRUE;
	return $matrix;
}

// This function returns an array of single element of object's FQDN attribute,
// if FQDN is set. The next choice is object's common name, if it looks like a
// hostname. Otherwise an array of all 'regular' IP addresses of the
// object is returned (which may appear 0 and more elements long).
function findAllEndpoints ($object_id, $fallback = '')
{
	$values = getAttrValues ($object_id);
	foreach ($values as $record)
		if ($record['name'] == 'FQDN' && !empty ($record['value']))
			return array ($record['value']);
	$regular = array();
	foreach (getObjectIPv4Allocations ($object_id) as $dottedquad => $alloc)
		if ($alloc['type'] == 'regular')
			$regular[] = $dottedquad;
	if (!count ($regular) && !empty ($fallback))
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
function parseWikiLink ($line, $which, $strip_optgroup = FALSE)
{
	if (preg_match ('/^\[\[.+\]\]$/', $line) == 0)
	{
		if ($strip_optgroup)
			return ereg_replace ('^.+%GSKIP%', '', ereg_replace ('^(.+)%GPASS%', '\\1 ', $line));
		else
			return $line;
	}
	$line = preg_replace ('/^\[\[(.+)\]\]$/', '$1', $line);
	$s = explode ('|', $line);
	$o_value = trim ($s[0]);
	if ($strip_optgroup)
		$o_value = ereg_replace ('^.+%GSKIP%', '', ereg_replace ('^(.+)%GPASS%', '\\1 ', $o_value));
	$a_value = trim ($s[1]);
	if ($which == 'a')
		return "<a href='${a_value}'>${o_value}</a>";
	if ($which == 'o')
		return $o_value;
}

function buildVServiceName ($vsinfo = NULL)
{
	if ($vsinfo == NULL)
	{
		showError ('NULL argument', __FUNCTION__);
		return NULL;
	}
	return $vsinfo['vip'] . ':' . $vsinfo['vport'] . '/' . $vsinfo['proto'];
}

function buildRSPoolName ($rspool = NULL)
{
	if ($rspool == NULL)
	{
		showError ('NULL argument', __FUNCTION__);
		return NULL;
	}
	return strlen ($rspool['name']) ? $rspool['name'] : 'ANONYMOUS pool';
}

// rackspace usage for a single rack
// (T + W + U) / (height * 3 - A)
function getRSUforRack ($data = NULL)
{
	if ($data == NULL)
	{
		showError ('Invalid argument', __FUNCTION__);
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
		showError ('Invalid argument', __FUNCTION__);
		return NULL;
	}
	if (!count ($rowData))
		return 0;
	$counter = array ('A' => 0, 'U' => 0, 'T' => 0, 'W' => 0, 'F' => 0);
	$total_height = 0;
	foreach (array_keys ($rowData) as $rack_id)
	{
		$data = getRackData ($rack_id);
		$total_height += $data['height'];
		for ($unit_no = $data['height']; $unit_no > 0; $unit_no--)
			for ($locidx = 0; $locidx < 3; $locidx++)
				$counter[$data[$unit_no][$locidx]['state']]++;
	}
	return ($counter['T'] + $counter['W'] + $counter['U']) / ($counter['T'] + $counter['W'] + $counter['U'] + $counter['F']);
}

// Return a list of object IDs, which can be found in the given rackspace block.
function stuffInRackspace ($rackData)
{
	$objects = array();
	for ($i = $rackData['height']; $i > 0; $i--)
		for ($locidx = 0; $locidx < 3; $locidx++)
			if
			(
				$rackData[$i][$locidx]['state'] == 'T' and
				!in_array ($rackData[$i][$locidx]['object_id'], $objects)
			)
				$objects[] = $rackData[$i][$locidx]['object_id'];
	return $objects;
}

// Make sure the string is always wrapped with LF characters
function lf_wrap ($str)
{
	$ret = trim ($str, "\r\n");
	if (!empty ($ret))
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

// Find if a particular tag id exists on the tree, then attach the
// given child tag to it. If the parent tag doesn't exist, return FALSE.
function attachChildTag (&$tree, $parent_id, $child_id, $child_info, $threshold = 0)
{
	$self = __FUNCTION__;
	foreach (array_keys ($tree) as $tagid)
	{
		if ($tagid == $parent_id)
		{
			if (!$threshold or ($threshold and $tree[$tagid]['kidc'] + 1 < $threshold))
				$tree[$tagid]['kids'][$child_id] = $child_info;
			// Reset the list only once.
			if (++$tree[$tagid]['kidc'] == $threshold)
				$tree[$tagid]['kids'] = array();
			return TRUE;
		}
		elseif ($self ($tree[$tagid]['kids'], $parent_id, $child_id, $child_info, $threshold))
			return TRUE;
	}
	return FALSE;
}

// Build a tree from the item list and return it. Input and output data is
// indexed by item id (nested items in output are recursively stored in 'kids'
// key, which is in turn indexed by id. Functions, which are ready to handle
// tree collapsion/expansion themselves, may request non-zero threshold value
// for smaller resulting tree.
function treeFromList ($mytaglist, $threshold = 0)
{
	$ret = array();
	while (count ($mytaglist) > 0)
	{
		$picked = FALSE;
		foreach ($mytaglist as $tagid => $taginfo)
		{
			$taginfo['kidc'] = 0;
			$taginfo['kids'] = array();
			if ($taginfo['parent_id'] == NULL)
			{
				$ret[$tagid] = $taginfo;
				$picked = TRUE;
				unset ($mytaglist[$tagid]);
			}
			elseif (attachChildTag ($ret, $taginfo['parent_id'], $tagid, $taginfo, $threshold))
			{
				$picked = TRUE;
				unset ($mytaglist[$tagid]);
			}
		}
		if (!$picked) // Only orphaned items on the list.
			break;
	}
	return $ret;
}

// Build a tree from the tag list and return everything _except_ the tree.
function getOrphanedTags ()
{
	global $taglist;
	$mytaglist = $taglist;
	$dummy = array();
	while (count ($mytaglist) > 0)
	{
		$picked = FALSE;
		foreach ($mytaglist as $tagid => $taginfo)
		{
			$taginfo['kidc'] = 0;
			$taginfo['kids'] = array();
			if ($taginfo['parent_id'] == NULL)
			{
				$dummy[$tagid] = $taginfo;
				$picked = TRUE;
				unset ($mytaglist[$tagid]);
			}
			elseif (attachChildTag ($dummy, $taginfo['parent_id'], $tagid, $taginfo))
			{
				$picked = TRUE;
				unset ($mytaglist[$tagid]);
			}
		}
		if (!$picked) // Only orphaned items on the list.
			return $mytaglist;
	}
	return array();
}

function serializeTags ($chain, $baseurl = '')
{
	$comma = '';
	$ret = '';
	foreach ($chain as $taginfo)
	{
		$ret .= $comma .
			($baseurl == '' ? '' : "<a href='${baseurl}tagfilter[]=${taginfo['id']}'>") .
			$taginfo['tag'] .
			($baseurl == '' ? '' : '</a>');
		$comma = ', ';
	}
	return $ret;
}

// For each tag add all its parent tags onto the list. Don't expect anything
// except user's tags on the chain.
function getTagChainExpansion ($chain, $tree = NULL)
{
	$self = __FUNCTION__;
	if ($tree === NULL)
	{
		global $tagtree;
		$tree = $tagtree;
	}
	// For each tag find its path from the root, then combine items
	// of all paths and add them to the chain, if they aren't there yet.
	$ret = array();
	foreach ($tree as $taginfo1)
	{
		$hit = FALSE;
		foreach ($chain as $taginfo2)
			if ($taginfo1['id'] == $taginfo2['id'])
			{
				$hit = TRUE;
				break;
			}
		if (count ($taginfo1['kids']) > 0)
		{
			$subsearch = $self ($chain, $taginfo1['kids']);
			if (count ($subsearch))
			{
				$hit = TRUE;
				$ret = array_merge ($ret, $subsearch);
			}
		}
		if ($hit)
			$ret[] = $taginfo1;
	}
	return $ret;
}

// Return the list of missing implicit tags.
function getImplicitTags ($oldtags)
{
	$ret = array();
	$newtags = getTagChainExpansion ($oldtags);
	foreach ($newtags as $newtag)
	{
		$already_exists = FALSE;
		foreach ($oldtags as $oldtag)
			if ($newtag['id'] == $oldtag['id'])
			{
				$already_exists = TRUE;
				break;
			}
		if ($already_exists)
			continue;
		$ret[] = array ('id' => $newtag['id'], 'tag' => $newtag['tag'], 'parent_id' => $newtag['parent_id']);
	}
	return $ret;
}

// Minimize the chain: exclude all implicit tags and return the result.
function getExplicitTagsOnly ($chain, $tree = NULL)
{
	$self = __FUNCTION__;
	global $tagtree;
	if ($tree === NULL)
		$tree = $tagtree;
	$ret = array();
	foreach ($tree as $taginfo)
	{
		if (isset ($taginfo['kids']))
		{
			$harvest = $self ($chain, $taginfo['kids']);
			if (count ($harvest) > 0)
			{
				$ret = array_merge ($ret, $harvest);
				continue;
			}
		}
		// This tag isn't implicit, test is for being explicit.
		foreach ($chain as $testtag)
			if ($taginfo['id'] == $testtag['id'])
			{
				$ret[] = $testtag;
				break;
			}
	}
	return $ret;
}

// Maximize the chain: for each tag add all tags, for which it is direct or indirect parent.
// Unlike other functions, this one accepts and returns a list of integer tag IDs, not
// a list of tag structures. Same structure (tag ID list) is returned after processing.
function complementByKids ($idlist, $tree = NULL, $getall = FALSE)
{
	$self = __FUNCTION__;
	global $tagtree;
	if ($tree === NULL)
		$tree = $tagtree;
	$getallkids = $getall;
	$ret = array();
	foreach ($tree as $taginfo)
	{
		foreach ($idlist as $test_id)
			if ($getall or $taginfo['id'] == $test_id)
			{
				$ret[] = $taginfo['id'];
				// Once matched node makes all sub-nodes match, but don't make
				// a mistake of matching every other node at the current level.
				$getallkids = TRUE;
				break;
			}
		if (isset ($taginfo['kids']))
			$ret = array_merge ($ret, $self ($idlist, $taginfo['kids'], $getallkids));
		$getallkids = FALSE;
	}
	return $ret;
}

function getUserAutoTags ($username = NULL)
{
	global $remote_username, $accounts;
	if ($username == NULL)
		$username = $remote_username;
	$ret = array();
	$ret[] = array ('tag' => '$username_' . $username);
	if (isset ($accounts[$username]['user_id']))
		$ret[] = array ('tag' => '$userid_' . $accounts[$username]['user_id']);
	return $ret;
}

function loadRackObjectAutoTags ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	$object_id = $_REQUEST['object_id'];
	$oinfo = getObjectInfo ($object_id);
	$ret = array();
	$ret[] = array ('tag' => '$id_' . $object_id);
	$ret[] = array ('tag' => '$typeid_' . $oinfo['objtype_id']);
	$ret[] = array ('tag' => '$any_object');
	if (validTagName ($oinfo['name']))
		$ret[] = array ('tag' => '$cn_' . $oinfo['name']);
	return $ret;
}

// Common code for both prefix and address tag listers.
function loadFileAutoTags ()
{
	assertUIntArg ('file_id', __FUNCTION__);
	$ret = array();
	$ret[] = array ('tag' => '$fileid_' . $_REQUEST['file_id']);
	$ret[] = array ('tag' => '$any_file');
	return $ret;
}

function getIPv4PrefixTags ($netid)
{
	$netinfo = getIPv4NetworkInfo ($netid);
	$ret = array();
	$ret[] = array ('tag' => '$ip4net-' . str_replace ('.', '-', $netinfo['ip']) . '-' . $netinfo['mask']);
	// FIXME: find and list tags for all parent networks?
	$ret[] = array ('tag' => '$any_ip4net');
	$ret[] = array ('tag' => '$any_net');
	return $ret;
}

function loadIPv4PrefixAutoTags ()
{
	assertUIntArg ('id', __FUNCTION__);
	return array_merge
	(
		array (array ('tag' => '$ip4netid_' . $_REQUEST['id'])),
		getIPv4PrefixTags ($_REQUEST['id'])
	);
}

function loadIPv4AddressAutoTags ()
{
	assertIPv4Arg ('ip', __FUNCTION__);
	return array_merge
	(
		array (array ('tag' => '$ip4net-' . str_replace ('.', '-', $_REQUEST['ip']) . '-32')),
		getIPv4PrefixTags (getIPv4AddressNetworkId ($_REQUEST['ip']))
	);
}

function loadRackAutoTags ()
{
	assertUIntArg ('rack_id', __FUNCTION__);
	$ret = array();
	$ret[] = array ('tag' => '$rackid_' . $_REQUEST['rack_id']);
	$ret[] = array ('tag' => '$any_rack');
	return $ret;
}

function loadIPv4VSAutoTags ()
{
	assertUIntArg ('vs_id', __FUNCTION__);
	$ret = array();
	$ret[] = array ('tag' => '$ipv4vsid_' . $_REQUEST['vs_id']);
	$ret[] = array ('tag' => '$any_ipv4vs');
	$ret[] = array ('tag' => '$any_vs');
	return $ret;
}

function loadIPv4RSPoolAutoTags ()
{
	assertUIntArg ('pool_id', __FUNCTION__);
	$ret = array();
	$ret[] = array ('tag' => '$ipv4rspid_' . $_REQUEST['pool_id']);
	$ret[] = array ('tag' => '$any_ipv4rsp');
	$ret[] = array ('tag' => '$any_rsp');
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

// Idem, but use ID list instead of chain.
function tagOnIdList ($taginfo, $tagidlist)
{
	if (!isset ($taginfo['id']))
		return FALSE;
	foreach ($tagidlist as $tagid)
		if ($taginfo['id'] == $tagid)
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

// If the page-tab-op triplet is final, make $expl_tags and $impl_tags
// hold all appropriate (explicit and implicit) tags respectively.
// Otherwise some limited redirection is necessary (only page and tab
// names are preserved, ophandler name change isn't handled).
function fixContext ()
{
	global $pageno, $tabno, $auto_tags, $expl_tags, $impl_tags, $page;

	$pmap = array
	(
		'accounts' => 'userlist',
		'rspools' => 'ipv4rsplist',
		'rspool' => 'ipv4rsp',
		'vservices' => 'ipv4vslist',
		'vservice' => 'ipv4vs',
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

	if (isset ($page[$pageno]['autotagloader']))
		$auto_tags = $page[$pageno]['autotagloader'] ();
	if
	(
		isset ($page[$pageno]['tagloader']) and
		isset ($page[$pageno]['bypass']) and
		isset ($_REQUEST[$page[$pageno]['bypass']])
	)
	{
		$expl_tags = $page[$pageno]['tagloader'] ($_REQUEST[$page[$pageno]['bypass']]);
		$impl_tags = getImplicitTags ($expl_tags);
	}
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
function getObjectiveTagTree ($tree, $realm)
{
	$self = __FUNCTION__;
	$ret = array();
	foreach ($tree as $taginfo)
	{
		$subsearch = array();
		$pick = FALSE;
		if (count ($taginfo['kids']))
		{
			$subsearch = $self ($taginfo['kids'], $realm);
			$pick = count ($subsearch) > 0;
		}
		if (isset ($taginfo['refcnt'][$realm]))
			$pick = TRUE;
		if (!$pick)
			continue;
		$ret[] = array
		(
			'id' => $taginfo['id'],
			'tag' => $taginfo['tag'],
			'parent_id' => $taginfo['parent_id'],
			'refcnt' => $taginfo['refcnt'],
			'kids' => $subsearch
		);
	}
	return $ret;
}

function getTagFilter ()
{
	return isset ($_REQUEST['tagfilter']) ? complementByKids ($_REQUEST['tagfilter']) : array();
}

function getTagFilterStr ($tagfilter = array())
{
	$ret = '';
	foreach (getExplicitTagsOnly (buildTagChainFromIds ($tagfilter)) as $taginfo)
		$ret .= "&tagfilter[]=" . $taginfo['id'];
	return $ret;
}

function buildWideRedirectURL ($log, $nextpage = NULL, $nexttab = NULL)
{
	global $root, $page, $pageno, $tabno;
	if ($nextpage === NULL)
		$nextpage = $pageno;
	if ($nexttab === NULL)
		$nexttab = $tabno;
	$url = "${root}?page=${nextpage}&tab=${nexttab}";
	if (isset ($page[$nextpage]['bypass']))
		$url .= '&' . $page[$nextpage]['bypass'] . '=' . $_REQUEST[$page[$nextpage]['bypass']];
	$url .= "&log=" . urlencode (base64_encode (serialize ($log)));
	return $url;
}

function buildRedirectURL ($status, $args = array(), $nextpage = NULL, $nexttab = NULL)
{
	global $msgcode, $pageno, $tabno, $op;
	if ($nextpage === NULL)
		$nextpage = $pageno;
	if ($nexttab === NULL)
		$nexttab = $tabno;
	return buildWideRedirectURL (oneLiner ($msgcode[$pageno][$tabno][$op][$status], $args), $nextpage, $nexttab);
}

// Return a message log consisting of only one message.
function oneLiner ($code, $args = array())
{
	$ret = array ('v' => 2);
	$ret['m'][] = count ($args) ? array ('c' => $code, 'a' => $args) : array ('c' => $code);
	return $ret;
}

// Return mesage code by status code.
function getMessageCode ($status)
{
	global $pageno, $tabno, $op, $msgcode;
	return $msgcode[$pageno][$tabno][$op][$status];
}

function validTagName ($s, $allow_autotag = FALSE)
{
	if (1 == mb_ereg (TAGNAME_REGEXP, $s))
		return TRUE;
	if ($allow_autotag and 1 == mb_ereg (AUTOTAGNAME_REGEXP, $s))
		return TRUE;
	return FALSE;
}

function redirectUser ($p, $t)
{
	global $page, $root;
	$l = "{$root}?page=${p}&tab=${t}";
	if (isset ($page[$p]['bypass']) and isset ($_REQUEST[$page[$p]['bypass']]))
		$l .= '&' . $page[$p]['bypass'] . '=' . $_REQUEST[$page[$p]['bypass']];
	header ("Location: " . $l);
	die;
}

function getRackCodeStats ()
{
	global $rackCode;
	$defc = $grantc = 0;
	foreach ($rackCode as $s)
		switch ($s['type'])
		{
			case 'SYNT_DEFINITION':
				$defc++;
				break;
			case 'SYNT_GRANT':
				$grantc++;
				break;
			default:
				break;
		}
	$ret = array ('Definition sentences' => $defc, 'Grant sentences' => $grantc);
	return $ret;
}

function getRackImageWidth ()
{
	return 3 + getConfigVar ('rtwidth_0') + getConfigVar ('rtwidth_1') + getConfigVar ('rtwidth_2') + 3;
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
		showError ('Invalid argument', __FUNCTION__);
		return;
	}
	$oInfo = getObjectInfo ($object_id);
	$lbconfig = getSLBConfig ($object_id);
	if ($lbconfig === NULL)
	{
		showError ('getSLBConfig() failed', __FUNCTION__);
		return;
	}
	$newconfig = "#\n#\n# This configuration has been generated automatically by RackTables\n";
	$newconfig .= "# for object_id == ${object_id}\n# object name: ${oInfo['name']}\n#\n#\n\n\n";
	foreach ($lbconfig as $vs_id => $vsinfo)
	{
		$newconfig .=  "########################################################\n" .
			"# VS (id == ${vs_id}): " . (empty ($vsinfo['vs_name']) ? 'NO NAME' : $vsinfo['vs_name']) . "\n" .
			"# RS pool (id == ${vsinfo['pool_id']}): " . (empty ($vsinfo['pool_name']) ? 'ANONYMOUS' : $vsinfo['pool_name']) . "\n" .
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
			if (empty ($rs['rsport']))
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
	if (empty ($sortfunc))
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
	if (!isset ($netdata['kids']) or empty ($netdata['kids']))
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
		showError ('Internal error, the recurring loop lost control', __FUNCTION__);
		die;
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
		showError ('Internal error, cannot decide between left and right', __FUNCTION__);
		die;
	}
}

function treeApplyFunc (&$tree, $func = '', $stopfunc = '')
{
	if (empty ($func))
		return;
	$self = __FUNCTION__;
	foreach (array_keys ($tree) as $key)
	{
		$func ($tree[$key]);
		if (!empty ($stopfunc) and $stopfunc ($tree[$key]))
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
	if (empty ($node['kids']))
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
	$node['addrc'] = count (scanIPv4Space ($toscan));
}

function nodeIsCollapsed ($node)
{
	return $node['symbol'] == 'node-collapsed';
}

function loadOwnIPv4Addresses (&$node)
{
	$toscan = array();
	if (empty ($node['kids']))
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
		$here = ($target and isset ($tree[$key]['id']) and $tree[$key]['id'] == $target);
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

// Get a tree and target entity ID on it. Return the list of IDs, which make the path
// from the target entity to tree root. If such a path exists, it will consist of at
// least the target ID. Otherwise an empty list will be returned. The "list" is a randomly
// indexed array of values.
function traceEntity ($tree, $target_id)
{
	$self = __FUNCTION__;
	foreach ($tree as $node)
	{
		if ($node['id'] == $target_id) // here!
			return array ($target_id);
		$subtrace = $self ($node['kids'], $target_id); // below?
		if (count ($subtrace))
		{
			array_push ($subtrace, $node['id']);
			return $subtrace;
		}
		// next...
	}
	return array();
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

// Display hrefs for all of a file's parents
function serializeFileLinks ($links)
{
	global $root;

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
		$ret .= sprintf("%s<a href='%s?%s%s'>%s</a>", $comma, $root, $params, $li['entity_id'], $li['name']);
		$comma = ', ';
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

?>
