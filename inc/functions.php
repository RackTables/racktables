<?
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
	elseif (in_array ($objectData['objtype_id'], explode (',', NAMEFUL_OBJTYPES)))
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
//	echo "for startRow==${startRow} and template==(${template[0]}, ${template[1]}, ${template[2]}) height==${height}<br>\n";
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
				if ($colspan != 0)
					$rackData[$startRow - $height][$locidx]['skipped'] = TRUE;
				else
				{
					$colspan = $templateWidth[$template_idx];
					if ($colspan > 1)
						$rackData[$startRow - $height][$locidx]['colspan'] = $colspan;
					if ($maxheight > 1)
						$rackData[$startRow - $height][$locidx]['rowspan'] = $maxheight;
				}
			}
		}
	}
	return;
}

// This function finds rowspan/solspan/skipped atom attributes for renderRack()
// What we actually have to do is to find all possible rectangles for each objects
// and then find the widest of those with the maximal square.
function markAllSpans (&$rackData = NULL)
{
	if ($rackData == NULL)
	{
		showError ('Invalid rackData in markupAllSpans()');
		return;
	}
	for ($i = $rackData['height']; $i > 0; $i--)
	{
		// calculate height of 6 possible span templates (array is presorted by width descending)
		global $template;
		for ($j = 0; $j < 6; $j++)
			$height[$j] = rectHeight ($rackData, $i, $j);
		// find the widest rectangle of those with maximal height
		$maxheight = max ($height);
		if ($maxheight > 0)
		{
			$best_template_index = 0;
			for ($j = 0; $j < 6; $j++)
				if ($height[$j] == $maxheight)
				{
					$best_template_index = $j;
					break;
				}
			// distribute span marks
			markSpan ($rackData, $i, $maxheight, $best_template_index);
		}
	}
}

function delRow ($row_id = 0)
{
	if ($row_id == 0)
	{
		showError ('Not all required args to delRow() are present.');
		return;
	}
	if (!isset ($_REQUEST['confirmed']) || $_REQUEST['confirmed'] != 'true')
	{
		echo "Press <a href='?op=del_row&row_id=${row_id}&confirmed=true'>here</a> to confirm rack row deletion.";
		return;
	}
	global $dbxlink;
	echo 'Deleting rack row information: ';
	$result = $dbxlink->query ("update RackRow set deleted = 'yes' where id=${row_id} limit 1");
	if ($result->rowCount() != 1)
	{
		showError ('Marked ' . $result.rowCount() . ' rows as deleted, but expected 1');
		return;
	}
	echo 'OK<br>';
	recordHistory ('RackRow', "id = ${row_id}");
	echo "Information was deleted. You may return to <a href='?op=list_rows&editmode=on'>rack row list</a>.";
}

function delRack ($rack_id = 0)
{
	if ($rack_id == 0)
	{
		showError ('Not all required args to delRack() are present.');
		return;
	}
	if (!isset ($_REQUEST['confirmed']) || $_REQUEST['confirmed'] != 'true')
	{
		echo "Press <a href='?op=del_rack&rack_id=${rack_id}&confirmed=true'>here</a> to confirm rack deletion.";
		return;
	}
	global $dbxlink;
	echo 'Deleting rack information: ';
	$result = $dbxlink->query ("update Rack set deleted = 'yes' where id=${rack_id} limit 1");
	if ($result->rowCount() != 1)
	{
		showError ('Marked ' . $result.rowCount() . ' rows as deleted, but expected 1');
		return;
	}
	echo 'OK<br>';
	recordHistory ('Rack', "id = ${rack_id}");
	echo "Information was deleted. You may return to <a href='?op=list_racks&editmode=on'>rack list</a>.";
}

function delObject ($object_id = 0)
{
	if ($object_id == 0)
	{
		showError ('Not all required args to delObject() are present.');
		return;
	}
	if (!isset ($_REQUEST['confirmed']) || $_REQUEST['confirmed'] != 'true')
	{
		echo "Press <a href='?op=del_object&object_id=${object_id}&confirmed=true'>here</a> to confirm object deletion.";
		return;
	}
	global $dbxlink;
	echo 'Deleting object information: ';
	$result = $dbxlink->query ("update RackObject set deleted = 'yes' where id=${object_id} limit 1");
	if ($result->rowCount() != 1)
	{
		showError ('Marked ' . $result.rowCount() . ' rows as deleted, but expected 1');
		return;
	}
	echo 'OK<br>';
	recordHistory ('RackObject', "id = ${object_id}");
	echo "Information was deleted. You may return to <a href='?op=list_objects&editmode=on'>object list</a>.";
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

function binMaskFromDec ($maskL)
{
	$binmask=0;
	for ($i=0; $i<$maskL; $i++)
	{
		$binmask*=2;
		$binmask+=1;
	}
	for ($i=$maskL; $i<32; $i++)
	{
		$binmask*=2;
	}
	return $binmask;
}

function binInvMaskFromDec ($maskL)
{
	$binmask=0;
	for ($i=0; $i<$maskL; $i++)
	{
		$binmask*=2;
	}
	for ($i=$maskL; $i<32; $i++)
	{
		$binmask*=2;
		$binmask+=1;
	}
	return $binmask;
}

function addRange ($range='', $name='')
{
	// $range is in x.x.x.x/x format, split into ip/mask vars
	$rangeArray = explode('/', $range);
	$ip = $rangeArray[0];
	$mask = $rangeArray[1];

	$ipL = ip2long($ip);
	$maskL = ip2long($mask);
	if ($ipL == -1 || $ipL === FALSE)
		return 'Bad ip address';
	if ($mask < 32 && $mask > 0)
		$maskL = $mask;
	else
	{
		$maskB = decbin($maskL);
		if (strlen($maskB)!=32)
			return 'Bad mask';
		$ones=0;
		$zeroes=FALSE;
		foreach( str_split ($maskB) as $digit)
		{
			if ($digit == '0')
				$zeroes = TRUE;
			if ($digit == '1')
			{
				$ones++;
				if ($zeroes == TRUE)
					return 'Bad mask';
			}
		}
		$maskL = $ones;
	}
	$binmask = binMaskFromDec($maskL);
	$ipL = $ipL & $binmask;

	$query =
		"select ".
		"id, ip, mask, name ".
		"from IPRanges ";
	

	global $dbxlink;

	$result = $dbxlink->query ($query);

	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$otherip = $row['ip'];
		$othermask = binMaskFromDec($row['mask']);
//		echo "checking $otherip & $othermask ".($otherip & $othermask)." == $ipL & $othermask ".($ipL & $othermask)." ".decbin($otherip)." ".decbin($othermask)." ".decbin($otherip & $othermask)." ".decbin($ipL)." ".decbin($othermask)." ".decbin($ipL & $othermask)."\n";
//		echo "checking $otherip & $binmask ".($otherip & $binmask)." == $ipL & $binmask ".($ipL & $binmask)." ".decbin($otherip)." ".decbin($binmask)." ".decbin($otherip & $binmask)." ".decbin($ipL)." ".decbin($binmask)." ".decbin($ipL & $binmask)."\n";
//		echo "\n";
//		flush();
		if (($otherip & $othermask) == ($ipL & $othermask))
			return "This subnet intersects with ".long2ip($row['ip'])."/${row['mask']}";
		if (($otherip & $binmask) == ($ipL & $binmask))
			return "This subnet intersects with ".long2ip($row['ip'])."/${row['mask']}";
	}
	$result->closeCursor();
	$query =
		"insert into IPRanges set ip=".sprintf('%u', $ipL).", mask='$maskL', name='$name'";
	$result = $dbxlink->exec ($query);
	return '';
}

function getIPRange ($id = 0)
{
	global $dbxlink;
	$query =
		"select ".
		"id as IPRanges_id, ".
		"INET_NTOA(ip) as IPRanges_ip, ".
		"ip as IPRanges_ip_bin, ".
		"mask as IPRanges_mask, ".
		"name as IPRanges_name ".
		"from IPRanges ".
		"where id = '$id'";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		return NULL;
	}
	else
	{
		$ret=array();
		if ($row = $result->fetch (PDO::FETCH_ASSOC))
		{
			$ret['id'] = $row['IPRanges_id'];
			$ret['ip'] = $row['IPRanges_ip'];
			$ret['ip_bin'] = ip2long($ret['ip']);
			$ret['mask_bin'] = binMaskFromDec($row['IPRanges_mask']);
			$ret['mask_bin_inv'] = binInvMaskFromDec($row['IPRanges_mask']);
			$ret['name'] = $row['IPRanges_name'];
			$ret['mask'] = $row['IPRanges_mask'];
			$ret['addrlist'] = array();

			$result->closeCursor();
			$query =
				"select ".
				"IPAddress.ip as ip_bin, ".
				"INET_NTOA(IPAddress.ip) as ip, ".
				"IPAddress.name as name, ".
				"IPAddress.reserved as reserved, ".
				"IPBonds.object_id as object_id, ".
				"RackObject.name as object_name, ".
				"IPBonds.name as bond_name, ".
				"IPBonds.type as bond_type ".
				"from IPAddress left join ".
					"(IPBonds join RackObject on IPBonds.object_id = RackObject.id) ".
				"on IPAddress.ip = IPBonds.ip ".
				"where IPAddress.ip >= '".sprintf('%u', ($ret['ip_bin'] & $ret['mask_bin']))."' and ".
					"IPAddress.ip <= '".sprintf('%u', ($ret['ip_bin'] | ($ret['mask_bin_inv']) ))."' ".
				"having (reserved='yes' or name != '' or IPAddress.name != '' or object_id is not NULL) ". 
				"order by IPAddress.ip, IPBonds.type, RackObject.name";
			$res_list=$dbxlink->query ($query);
			$prev_ip=0;
			if ($res_list != NULL)
			{
				while ($row1 = $res_list->fetch (PDO::FETCH_ASSOC))
				{
					if ($prev_ip != $row1['ip'])
					{
						$refcount=0;
						$count=ip2long($row1['ip']);
						$ret['addrlist'][$count]['name'] = $row1['name'];
						$ret['addrlist'][$count]['reserved'] = $row1['reserved'];
						$ret['addrlist'][$count]['ip'] = $row1['ip'];
						$ret['addrlist'][$count]['ip_bin'] = ip2long($row1['ip']);
						$prev_ip = $ret['addrlist'][$count]['ip'];
						$ret['addrlist'][$count]['references'] = array();
					}

					if ($row1['bond_type'])
					{
						$ret['addrlist'][$count]['references'][$refcount]['type'] = $row1['bond_type'];
						$ret['addrlist'][$count]['references'][$refcount]['name'] = $row1['bond_name'];
						$ret['addrlist'][$count]['references'][$refcount]['object_id'] = $row1['object_id'];
						$ret['addrlist'][$count]['references'][$refcount]['object_name'] = $row1['object_name'];
						$refcount++;
					}
				}
				$res_list->closeCursor();
			}
		}
	}
	return $ret;
}

function getIPAddress ($ip=0)
{
	$ret = array();
	$ret['range'] = getIPRange($ip);
	$ret['bonds'] = array();
	$ret['outpf'] = array();
	$ret['inpf'] = array();
	$ret['exists'] = 0;
	$ret['reserved'] = 'no';
	global $dbxlink;
	$query =
		"select ".
		"ip, name, reserved ".
		"from IPAddress ".
		"where ip = INET_ATON('$ip')";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		return NULL;
	}
	else
	{
		if ($row = $result->fetch (PDO::FETCH_ASSOC))
		{
			$ret['exists'] = 1;
			$ret['ip_bin'] = $row['ip'];
			$ret['ip'] = $ip;
			$ret['name'] = $row['name'];
			$ret['reserved'] = $row['reserved'];
		}
		$result->fetch (PDO::FETCH_ASSOC);
	}
	$result->closeCursor();

	if ($ret['exists'] == 1)
	{
		$query =
			"select ".
			"IPBonds.object_id as object_id, ".
			"IPBonds.name as name, ".
			"IPBonds.type as type, ".
			"RackObject.name as object_name ".
			"from IPBonds join RackObject on IPBonds.object_id=RackObject.id ".
			"where IPBonds.ip=INET_ATON('$ip') ".
			"order by RackObject.id, IPBonds.name";
		$result1 = $dbxlink->query ($query);
		$count=0;
		while ($row = $result1->fetch (PDO::FETCH_ASSOC))
		{
			$ret['bonds'][$count]['object_id'] = $row['object_id'];
			$ret['bonds'][$count]['name'] = $row['name'];
			$ret['bonds'][$count]['type'] = $row['type'];
			$ret['bonds'][$count]['object_name'] = $row['object_name'];
			$count++;
		}
		$result1->closeCursor();


	}

	return $ret;
}
	
function bindIpToObject ($ip='', $object_id=0, $name='', $type='')
{
	global $dbxlink;

	$range = getRangeByIp($ip);

	if (!$range)
		return 'Non-existant ip address. Try adding IP range first';

	$address = getIPAddress($ip);

	if ($address['exists'] == 0)
	{
		$query =
			"insert into IPAddress set ip=INET_ATON('$ip')";
		$dbxlink->exec ($query);
	}

	$query =
		"insert into IPBonds set ip=INET_ATON('$ip'), object_id='$object_id', name='$name', type='$type'";
	$result = $dbxlink->exec ($query);
	return '';
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

// This function performs search and then calculates score for each result.
// Given previous search results in $objects argument, it adds new results
// to the array and updates score for existing results, if it is greater than
// existing score.
function mergeSearchResults (&$objects, $terms, $fieldname)
{
	global $dbxlink;
	$query =
		"select name, label, asset_no, barcode, ro.id, dict_key as objtype_id, " .
		"dict_value as objtype_name, asset_no from RackObject as ro inner join Dictionary " .
		"on objtype_id = dict_key natural join Chapter where chapter_name = 'RackObjectType' and ";
	$count = 0;
	foreach (explode (' ', $terms) as $term)
	{
		if ($count) $query .= ' or ';
		$query .= "${fieldname} like '%$term%'";
		$count++;
	}
	$query .= "";
	$result = $dbxlink->query($query);
	if ($result == NULL)
	{
		showError ("SQL query failed in mergeSearchResults()");
		return NULL;
	}
// FIXME: this dead call was executed 4 times per 1 object search!
//	$typeList = getObjectTypeList();
	$clist = array ('id', 'name', 'label', 'asset_no', 'barcode', 'objtype_id', 'objtype_name');
	while ($row = $result->fetch(PDO::FETCH_ASSOC))
	{
		foreach ($clist as $cname)
			$object[$cname] = $row[$cname];
		$object['score'] = 0;
		$object['dname'] = displayedName ($object);
		unset ($object['objtype_id']);
		foreach (explode (' ', $terms) as $term)
			if (strstr ($object['name'], $term))
				$object['score'] += 1;
		unset ($object['name']);
		if (!isset ($objects[$row['id']]))
			$objects[$row['id']] = $object;
		elseif ($objects[$row['id']]['score'] < $object['score'])
			$objects[$row['id']]['score'] = $object['score'];
	}
	return $objects;
}

function getSearchResults ($terms)
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
		showError ('Invalid arguments passed to getPrevIDforRack()');
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
		showError ('Invalid arguments passed to getNextIDforRack()');
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

function eq ($a, $b)
{
	return $a==$b;
}

function neq ($a, $b)
{
	return $a!=$b;
}

function countRefsOfType ($refs, $type, $eq)
{
	$count=0;
	foreach ($refs as $ref)
	{
		if ($eq($ref['type'], $type))
			$count++;
	}
	return $count;
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
		$objname_cmp = sortTokenize($a['port_name'], $b['port_name']);
		if ($objname_cmp == 0)
		{
			sortTokenize($a['ip'], $b['ip']);
		}
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

function newPortForwarding($object_id, $localip, $localport, $remoteip, $remoteport, $proto, $description)
{
	global $dbxlink;

	$range = getRangeByIp($localip);
	if (!$range)
		return "$localip: Non existant ip";
	
	$range = getRangeByIp($remoteip);
	if (!$range)
		return "$remoteip: Non existant ip";
	
	if ( ($localport <= 0) or ($localport >= 65536) )
		return "$localport: invaild port";

	if ( ($remoteport <= 0) or ($remoteport >= 65536) )
		return "$remoteport: invaild port";

        $query =
                "insert into PortForwarding set object_id='$object_id', localip=INET_ATON('$localip'), remoteip=INET_ATON('$remoteip'), localport='$localport', remoteport='$remoteport', proto='$proto', description='$description'";
        $result = $dbxlink->exec ($query);

	return '';
}

function deletePortForwarding($object_id, $localip, $localport, $remoteip, $remoteport, $proto)
{
	global $dbxlink;

	$query =
		"delete from PortForwarding where object_id='$object_id' and localip=INET_ATON('$localip') and remoteip=INET_ATON('$remoteip') and localport='$localport' and remoteport='$remoteport' and proto='$proto'";
	$result = $dbxlink->exec ($query);
	return '';
}

function updatePortForwarding($object_id, $localip, $localport, $remoteip, $remoteport, $proto, $description)
{
	global $dbxlink;

	$query =
		"update PortForwarding set description='$description' where object_id='$object_id' and localip=INET_ATON('$localip') and remoteip=INET_ATON('$remoteip') and localport='$localport' and remoteport='$remoteport' and proto='$proto'";
	$result = $dbxlink->exec ($query);
	return '';
}

function getObjectForwards($object_id)
{
	global $dbxlink;

	$ret = array();
	$ret['out'] = array();
	$ret['in'] = array();
	$query =
		"select ".
		"dict_value as proto, ".
		"proto as proto_bin, ".
		"INET_NTOA(localip) as localip, ".
		"localport, ".
		"INET_NTOA(remoteip) as remoteip, ".
		"remoteport, ".
		"description ".
		"from PortForwarding inner join Dictionary on proto = dict_key natural join Chapter ".
		"where object_id='$object_id' and chapter_name = 'Protocols' ".
		"order by localip, localport, proto, remoteip, remoteport";
	$result2 = $dbxlink->query ($query);
	$count=0;
	while ($row = $result2->fetch (PDO::FETCH_ASSOC))
	{
		$ret['out'][$count]['proto'] = $row['proto'];
		$ret['out'][$count]['proto_bin'] = $row['proto_bin'];
		$ret['out'][$count]['localip'] = $row['localip'];
		$ret['out'][$count]['localport'] = $row['localport'];
		$ret['out'][$count]['remoteip'] = $row['remoteip'];
		$ret['out'][$count]['remoteport'] = $row['remoteport'];
		$ret['out'][$count]['description'] = $row['description'];
		$count++;
	}
	$result2->closeCursor();

	$query =
		"select ".
		"dict_value as proto, ".
		"proto as proto_bin, ".
		"INET_NTOA(localip) as localip, ".
		"localport, ".
		"INET_NTOA(remoteip) as remoteip, ".
		"remoteport, ".
		"PortForwarding.object_id as object_id, ".
		"RackObject.name as object_name, ".
		"description ".
		"from ((PortForwarding join IPBonds on remoteip=ip) join RackObject on PortForwarding.object_id=RackObject.id) inner join Dictionary on proto = dict_key natural join Chapter ".
		"where IPBonds.object_id='$object_id' and chapter_name = 'Protocols' ".
		"order by remoteip, remoteport, proto, localip, localport";
	$result3 = $dbxlink->query ($query);
	$count=0;
	while ($row = $result3->fetch (PDO::FETCH_ASSOC))
	{
		$ret['in'][$count]['proto'] = $row['proto'];
		$ret['in'][$count]['proto_bin'] = $row['proto_bin'];
		$ret['in'][$count]['localport'] = $row['localport'];
		$ret['in'][$count]['localip'] = $row['localip'];
		$ret['in'][$count]['remoteport'] = $row['remoteport'];
		$ret['in'][$count]['remoteip'] = $row['remoteip'];
		$ret['in'][$count]['object_id'] = $row['object_id'];
		$ret['in'][$count]['object_name'] = $row['object_name'];
		$ret['in'][$count]['description'] = $row['description'];
		$count++;
	}
	$result3->closeCursor();

	return $ret;
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
	$addresses = getObjectAddresses ($object_id);
	$regular = array();
	foreach ($addresses as $idx => $address)
		if ($address['type'] == 'regular')
			$regular[] = $address['ip'];
	if (!count ($regular) && !empty ($fallback))
		return array ($fallback);
	return $regular;
}

?>
