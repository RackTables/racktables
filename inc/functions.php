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

function delRow ($row_id = 0)
{
	if ($row_id == 0)
	{
		showError ('Not all required args are present.', __FUNCTION__);
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
		showError ('Marked ' . $result.rowCount() . ' rows as deleted, but expected 1', __FUNCTION__);
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
		showError ('Not all required args are present.', __FUNCTION__);
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
		showError ('Marked ' . $result.rowCount() . ' rows as deleted, but expected 1', __FUNCTION__);
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
		showError ('Not all required args are present.', __FUNCTION__);
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
		showError ('Marked ' . $result.rowCount() . ' rows as deleted, but expected 1', __FUNCTION__);
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

function getIPRange ($id = 0)
{
	global $dbxlink;
	$query =
		"select ".
		"id as IPRanges_id, ".
		"INET_NTOA(ip) as IPRanges_ip, ".
		"mask as IPRanges_mask, ".
		"name as IPRanges_name ".
		"from IPRanges ".
		"where id = '$id'";
	$result = useSelectBlade ($query, __FUNCTION__);
	$ret = array();
	$row = $result->fetch (PDO::FETCH_ASSOC);
	if ($row == NULL)
		return $ret;
	$ret['id'] = $row['IPRanges_id'];
	$ret['ip'] = $row['IPRanges_ip'];
	$ret['ip_bin'] = ip2long ($row['IPRanges_ip']);
	$ret['mask_bin'] = binMaskFromDec($row['IPRanges_mask']);
	$ret['mask_bin_inv'] = binInvMaskFromDec($row['IPRanges_mask']);
	$ret['name'] = $row['IPRanges_name'];
	$ret['mask'] = $row['IPRanges_mask'];
	$ret['addrlist'] = array();
	$result->closeCursor();
	unset ($result);
	// We risk losing some significant bits in an unsigned 32bit integer,
	// unless it is converted to a string.
	$db_first = "'" . sprintf ('%u', 0x00000000 + $ret['ip_bin'] & $ret['mask_bin']) . "'";
	$db_last  = "'" . sprintf ('%u', 0x00000000 + $ret['ip_bin'] | ($ret['mask_bin_inv'])) . "'";

	// Don't try to build up the whole structure in a single pass. Request
	// the list of user comments and reservations and merge allocations in
	// at a latter point.
	$query =
		"select INET_NTOA(ip) as ip, name, reserved from IPAddress " .
		"where ip between ${db_first} and ${db_last} " .
		"and (reserved = 'yes' or name != '')";
	$result = $dbxlink->query ($query);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = ip2long ($row['ip']);
		$ret['addrlist'][$ip_bin] = $row;
		$tmp = array();
		foreach (array ('ip', 'name', 'reserved') as $cname)
			$tmp[$cname] = $row[$cname];
		$tmp['references'] = array();
		$tmp['lbrefs'] = array();
		$tmp['rsrefs'] = array();
		$ret['addrlist'][$ip_bin] = $tmp;
	}
	$result->closeCursor();
	unset ($result);

	$query =
		"select INET_NTOA(ipb.ip) as ip, ro.id as object_id, " .
		"ro.name as object_name, ipb.name, ipb.type, objtype_id, " .
		"dict_value as objtype_name from " .
		"IPBonds as ipb inner join RackObject as ro on ipb.object_id = ro.id " .
		"left join Dictionary on objtype_id=dict_key natural join Chapter " .
		"where ip between ${db_first} and ${db_last} " .
		"and chapter_name = 'RackObjectType'" .
		"order by ipb.type, object_name";
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = ip2long ($row['ip']);
		if (!isset ($ret['addrlist'][$ip_bin]))
		{
			$ret['addrlist'][$ip_bin] = array();
			$ret['addrlist'][$ip_bin]['ip'] = $row['ip'];
			$ret['addrlist'][$ip_bin]['name'] = '';
			$ret['addrlist'][$ip_bin]['reserved'] = 'no';
			$ret['addrlist'][$ip_bin]['references'] = array();
			$ret['addrlist'][$ip_bin]['lbrefs'] = array();
			$ret['addrlist'][$ip_bin]['rsrefs'] = array();
		}
		$tmp = array();
		foreach (array ('object_id', 'type', 'name') as $cname)
			$tmp[$cname] = $row[$cname];
		$quasiobject['name'] = $row['object_name'];
		$quasiobject['objtype_id'] = $row['objtype_id'];
		$quasiobject['objtype_name'] = $row['objtype_name'];
		$tmp['object_name'] = displayedName ($quasiobject);
		$ret['addrlist'][$ip_bin]['references'][] = $tmp;
	}
	$result->closeCursor();
	unset ($result);

	$query = "select vs_id, inet_ntoa(vip) as ip, vport, proto, " .
		"object_id, objtype_id, ro.name, dict_value as objtype_name from " .
		"IPVirtualService as vs inner join IPLoadBalancer as lb on vs.id = lb.vs_id " .
		"inner join RackObject as ro on lb.object_id = ro.id " .
		"left join Dictionary on objtype_id=dict_key " .
		"natural join Chapter " .
		"where vip between ${db_first} and ${db_last} " .
		"and chapter_name = 'RackObjectType'" .
		"order by vport, proto, ro.name, object_id";
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = ip2long ($row['ip']);
		if (!isset ($ret['addrlist'][$ip_bin]))
		{
			$ret['addrlist'][$ip_bin] = array();
			$ret['addrlist'][$ip_bin]['ip'] = $row['ip'];
			$ret['addrlist'][$ip_bin]['name'] = '';
			$ret['addrlist'][$ip_bin]['reserved'] = 'no';
			$ret['addrlist'][$ip_bin]['references'] = array();
			$ret['addrlist'][$ip_bin]['lbrefs'] = array();
			$ret['addrlist'][$ip_bin]['rsrefs'] = array();
		}
		$tmp = $qbject = array();
		foreach (array ('object_id', 'vport', 'proto', 'vs_id') as $cname)
			$tmp[$cname] = $row[$cname];
		foreach (array ('name', 'objtype_id', 'objtype_name') as $cname)
			$qobject[$cname] = $row[$cname];
		$tmp['object_name'] = displayedName ($qobject);
		$ret['addrlist'][$ip_bin]['lbrefs'][] = $tmp;
	}
	$result->closeCursor();
	unset ($result);

	$query = "select inet_ntoa(rsip) as ip, rsport, rspool_id, rsp.name as rspool_name from " .
		"IPRealServer as rs inner join IPRSPool as rsp on rs.rspool_id = rsp.id " .
		"where rsip between ${db_first} and ${db_last} " .
		"order by ip, rsport, rspool_id";
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = ip2long ($row['ip']);
		if (!isset ($ret['addrlist'][$ip_bin]))
		{
			$ret['addrlist'][$ip_bin] = array();
			$ret['addrlist'][$ip_bin]['ip'] = $row['ip'];
			$ret['addrlist'][$ip_bin]['name'] = '';
			$ret['addrlist'][$ip_bin]['reserved'] = 'no';
			$ret['addrlist'][$ip_bin]['references'] = array();
			$ret['addrlist'][$ip_bin]['lbrefs'] = array();
			$ret['addrlist'][$ip_bin]['rsrefs'] = array();
		}
		$tmp = array();
		foreach (array ('rspool_id', 'rsport', 'rspool_name') as $cname)
			$tmp[$cname] = $row[$cname];
		$ret['addrlist'][$ip_bin]['rsrefs'][] = $tmp;
	}

	return $ret;
}

// Don't require any records in IPAddress, but if there is one,
// merge the data between getting allocation list. Collect enough data
// to call displayedName() ourselves.
function getIPAddress ($ip = 0)
{
	$ret = array
	(
		'bonds' => array(),
		'outpf' => array(),
		'inpf' => array(),
		'vslist' => array(),
		'rslist' => array(),
		'exists' => 0,
		'name' => '',
		'reserved' => 'no'
	);
	$query =
		"select ".
		"name, reserved ".
		"from IPAddress ".
		"where ip = INET_ATON('$ip') and (reserved = 'yes' or name != '')";
	$result = useSelectBlade ($query, __FUNCTION__);
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ret['exists'] = 1;
		$ret['name'] = $row['name'];
		$ret['reserved'] = $row['reserved'];
	}
	$result->closeCursor();
	unset ($result);

	$query =
		"select ".
		"IPBonds.object_id as object_id, ".
		"IPBonds.name as name, ".
		"IPBonds.type as type, ".
		"objtype_id, dict_value as objtype_name, " .
		"RackObject.name as object_name ".
		"from IPBonds join RackObject on IPBonds.object_id=RackObject.id ".
		"left join Dictionary on objtype_id=dict_key natural join Chapter " .
		"where IPBonds.ip=INET_ATON('$ip') ".
		"and chapter_name = 'RackObjectType' " .
		"order by RackObject.id, IPBonds.name";
	$result = useSelectBlade ($query, __FUNCTION__);
	$count = 0;
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ret['bonds'][$count]['object_id'] = $row['object_id'];
		$ret['bonds'][$count]['name'] = $row['name'];
		$ret['bonds'][$count]['type'] = $row['type'];
		$qo = array();
		$qo['name'] = $row['object_name'];
		$qo['objtype_id'] = $row['objtype_id'];
		$qo['objtype_name'] = $row['objtype_name'];
		$ret['bonds'][$count]['object_name'] = displayedName ($qo);
		$count++;
		$ret['exists'] = 1;
	}
	$result->closeCursor();
	unset ($result);

	$query = "select id, vport, proto, name from IPVirtualService where vip = inet_aton('${ip}')";
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$new = $row;
		$new['vip'] = $ip;
		$ret['vslist'][] = $new;
	}
	$result->closeCursor();
	unset ($result);

	$query = "select inservice, rsport, IPRSPool.id as pool_id, IPRSPool.name as poolname from " .
		"IPRealServer inner join IPRSPool on rspool_id = IPRSPool.id " .
		"where rsip = inet_aton('${ip}')";
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$new = $row;
		$new['rsip'] = $ip;
		$ret['rslist'][] = $new;
	}
	$result->closeCursor();
	unset ($result);

	$query =
		"select " .
		"proto, " .
		"INET_NTOA(localip) as localip, " .
		"localport, " .
		"INET_NTOA(remoteip) as remoteip, " .
		"remoteport, " .
		"description " .
		"from PortForwarding " .
		"where remoteip = inet_aton('${ip}') or localip = inet_aton('${ip}') " .
		"order by localip, localport, remoteip, remoteport, proto";
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		if ($row['remoteip'] == $ip)
			$ret['inpf'][] = $row;
		if ($row['localip'] == $ip)
			$ret['outpf'][] = $row;
	}
	$result->closeCursor();
	unset ($result);

	return $ret;
}

function bindIpToObject ($ip = '', $object_id = 0, $name = '', $type = '')
{
	global $dbxlink;

	$range = getRangeByIp ($ip);
	if (!$range)
		return 'Non-existant ip address. Try adding IP range first';

	$result = useInsertBlade
	(
		'IPBonds',
		array
		(
			'ip' => "INET_ATON('$ip')",
			'object_id' => "'${object_id}'",
			'name' => "'${name}'",
			'type' => "'${type}'"
		)
	);
	return $result ? '' : (__FUNCTION__ . '(): useInsertBlade() failed');
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
	$result = useSelectBlade ($query, __FUNCTION__);
// FIXME: this dead call was executed 4 times per 1 object search!
//	$typeList = getObjectTypeList();
	$clist = array ('id', 'name', 'label', 'asset_no', 'barcode', 'objtype_id', 'objtype_name');
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
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

	$result = useInsertBlade
	(
		'PortForwarding',
		array
		(
			'object_id' => $object_id,
			'localip' => "INET_ATON('${localip}')",
			'remoteip' => "INET_ATON('$remoteip')",
			'localport' => $localport,
			'remoteport' => $remoteport,
			'proto' => "'${proto}'",
			'description' => "'${description}'",
		)
	);
	if ($result)
		return '';
	else
		return __FUNCTION__ . ': Failed to insert the rule.';
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

function getNATv4ForObject ($object_id)
{
	$ret = array();
	$ret['out'] = array();
	$ret['in'] = array();
	$query =
		"select ".
		"proto, ".
		"INET_NTOA(localip) as localip, ".
		"localport, ".
		"INET_NTOA(remoteip) as remoteip, ".
		"remoteport, ".
		"ipa1.name as local_addr_name, " .
		"ipa2.name as remote_addr_name, " .
		"description ".
		"from PortForwarding ".
		"left join IPAddress as ipa1 on PortForwarding.localip = ipa1.ip " .
		"left join IPAddress as ipa2 on PortForwarding.remoteip = ipa2.ip " .
		"where object_id='$object_id' ".
		"order by localip, localport, proto, remoteip, remoteport";
	$result = useSelectBlade ($query, __FUNCTION__);
	$count=0;
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		foreach (array ('proto', 'localport', 'localip', 'remoteport', 'remoteip', 'description', 'local_addr_name', 'remote_addr_name') as $cname)
			$ret['out'][$count][$cname] = $row[$cname];
		$count++;
	}
	$result->closeCursor();
	unset ($result);

	$query =
		"select ".
		"proto, ".
		"INET_NTOA(localip) as localip, ".
		"localport, ".
		"INET_NTOA(remoteip) as remoteip, ".
		"remoteport, ".
		"PortForwarding.object_id as object_id, ".
		"RackObject.name as object_name, ".
		"description ".
		"from ((PortForwarding join IPBonds on remoteip=IPBonds.ip) join RackObject on PortForwarding.object_id=RackObject.id) ".
		"where IPBonds.object_id='$object_id' ".
		"order by remoteip, remoteport, proto, localip, localport";
	$result = useSelectBlade ($query, __FUNCTION__);
	$count=0;
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		foreach (array ('proto', 'localport', 'localip', 'remoteport', 'remoteip', 'object_id', 'object_name', 'description') as $cname)
			$ret['in'][$count][$cname] = $row[$cname];
		$count++;
	}
	$result->closeCursor();

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

function getObjectCount ($rackData)
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
	return count ($objects);
}

// Perform substitutions and return resulting string
function apply_macros ($macros, $subject)
{
	$ret = $subject;
	foreach ($macros as $search => $replace)
		$ret = str_replace ($search, $replace, $ret);
	return $ret;
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
function attachChildTag (&$tree, $parent_id, $child_id, $child_info)
{
	foreach ($tree as $tagid => $taginfo)
	{
		if ($tagid == $parent_id)
		{
			$tree[$tagid]['kids'][$child_id] = $child_info;
			return TRUE;
		}
		elseif (attachChildTag ($tree[$tagid]['kids'], $parent_id, $child_id, $child_info))
			return TRUE;
	}
	return FALSE;
}

// Build a tree from the tag list and return it.
function getTagTree ()
{
	global $taglist;
	$mytaglist = $taglist;
	$ret = array();
	while (count ($mytaglist) > 0)
	{
		$picked = FALSE;
		foreach ($mytaglist as $tagid => $taginfo)
		{
			$taginfo['kids'] = array();
			if ($taginfo['parent_id'] == NULL)
			{
				$ret[$tagid] = $taginfo;
				$picked = TRUE;
				unset ($mytaglist[$tagid]);
			}
			elseif (attachChildTag ($ret, $taginfo['parent_id'], $tagid, $taginfo))
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

function serializeTags ($trail, $baseurl = '')
{
	$comma = '';
	$ret = '';
	foreach ($trail as $taginfo)
	{
		$ret .= $comma .
			($baseurl == '' ? '' : "<a href='${baseurl}tagfilter[]=${taginfo['id']}'>") .
			$taginfo['tag'] .
			($baseurl == '' ? '' : '</a>');
		$comma = ', ';
	}
	return $ret;
}

// a helper for getTrailExpansion()
function traceTrail ($tree, $trail)
{
	// For each tag find its path from the root, then combine items
	// of all paths and add them to the trail, if they aren't there yet.
	$ret = array();
	foreach ($tree as $taginfo1)
	{
		$hit = FALSE;
		foreach ($trail as $taginfo2)
			if ($taginfo1['id'] == $taginfo2['id'])
			{
				$hit = TRUE;
				break;
			}
		if (count ($taginfo1['kids']) > 0)
		{
			$subsearch = traceTrail ($taginfo1['kids'], $trail);
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

// For each tag add all its parent tags onto the list. Don't expect anything
// except user's tags on the trail.
function getTrailExpansion ($trail)
{
	global $tagtree;
	return traceTrail ($tagtree, $trail);
}

// Return the list of missing implicit tags.
function getImplicitTags ($oldtags)
{
	$ret = array();
	$newtags = getTrailExpansion ($oldtags);
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

// Minimize the trail: exclude all implicit tags and return the resulting trail.
function getExplicitTagsOnly ($trail, $tree = NULL)
{
	global $tagtree;
	if ($tree === NULL)
		$tree = $tagtree;
	$ret = array();
	foreach ($tree as $taginfo)
	{
		if (isset ($taginfo['kids']))
		{
			$harvest = getExplicitTagsOnly ($trail, $taginfo['kids']);
			if (count ($harvest) > 0)
			{
				$ret = array_merge ($ret, $harvest);
				continue;
			}
		}
		// This tag isn't implicit, test is for being explicit.
		foreach ($trail as $testtag)
			if ($taginfo['id'] == $testtag['id'])
			{
				$ret[] = $testtag;
				break;
			}
	}
	return $ret;
}

// Maximize the trail: for each tag add all tags, for which it is direct or indirect parent.
// Unlike other functions, this one accepts and returns a list of integer tag IDs, not
// a list of tag structures.
function complementByKids ($idlist, $tree = NULL, $getall = FALSE)
{
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
			$ret = array_merge ($ret, complementByKids ($idlist, $taginfo['kids'], $getallkids));
		$getallkids = FALSE;
	}
	return $ret;
}

function loadRackObjectAutoTags ()
{
	assertUIntArg ('object_id', __FUNCTION__);
	$object_id = $_REQUEST['object_id'];
	$oinfo = getObjectInfo ($object_id);
	$ret = array();
	$ret[] = array ('tag' => '$id_' . $_REQUEST['object_id']);
	$ret[] = array ('tag' => '$any_object');
	return $ret;
}

// Common code for both prefix and address tag listers.
function getIPv4PrefixTags ($prefix)
{
	$ret = array();
	$ret[] = array ('tag' => '$ipv4net-' . str_replace ('.', '-', $prefix['ip']) . '-' . $prefix['mask']);
	// FIXME: find and list tags for all parent networks
	$ret[] = array ('tag' => '$any_ipv4net');
	$ret[] = array ('tag' => '$any_net');
	return $ret;
}

function loadIPv4PrefixAutoTags ()
{
	assertUIntArg ('id', __FUNCTION__);
	return array_merge
	(
		array (array ('tag' => '$id_' . $_REQUEST['id'])),
		getIPv4PrefixTags (getIPRange ($_REQUEST['id']))
	);
}

function loadIPv4AddressAutoTags ()
{
	assertIPv4Arg ('ip', __FUNCTION__);
	return array_merge
	(
		array (array ('tag' => '$ipv4net-' . str_replace ('.', '-', $_REQUEST['ip']) . '-32')),
		getIPv4PrefixTags (getRangeByIP ($_REQUEST['ip']))
	);
}

function loadRackAutoTags ()
{
	assertUIntArg ('rack_id', __FUNCTION__);
	$ret = array();
	$ret[] = array ('tag' => '$id_' . $_REQUEST['rack_id']);
	$ret[] = array ('tag' => '$any_rack');
	return $ret;
}

function loadIPv4VSAutoTags ()
{
	assertUIntArg ('id', __FUNCTION__);
	$ret = array();
	$ret[] = array ('tag' => '$id_' . $_REQUEST['id']);
	$ret[] = array ('tag' => '$any_ipv4vs');
	$ret[] = array ('tag' => '$any_vs');
	return $ret;
}

function loadIPv4RSPoolAutoTags ()
{
	assertUIntArg ('pool_id', __FUNCTION__);
	$ret = array();
	$ret[] = array ('tag' => '$id_' . $_REQUEST['pool_id']);
	$ret[] = array ('tag' => '$any_ipv4rspool');
	$ret[] = array ('tag' => '$any_rspool');
	return $ret;
}

function getGlobalAutoTags ()
{
	global $remote_username, $accounts;
	$ret = array();
	$user_id = 0;
	foreach ($accounts as $a)
		if ($a['user_name'] == $remote_username)
		{
			$user_id = $a['user_id'];
			break;
		}
	$ret[] = array ('tag' => '$username_' . $remote_username);
	$ret[] = array ('tag' => '$userid_' . $user_id);
	return $ret;
}

// Build a tag trail from supplied tag id list and return it.
function buildTrailFromIds ($tagidlist)
{
	global $taglist;
	$ret = array();
	foreach ($tagidlist as $tag_id)
		if (isset ($taglist[$tag_id]))
			$ret[] = $taglist[$tag_id];
	return $ret;
}

// Process a given tag tree and return only meaningful branches. The resulting
// (sub)tree will have refcnt leaves on every last branch.
function getObjectiveTagTree ($tree, $realm)
{
	$ret = array();
	foreach ($tree as $taginfo)
	{
		$subsearch = array();
		$pick = FALSE;
		if (count ($taginfo['kids']))
		{
			$subsearch = getObjectiveTagTree ($taginfo['kids'], $realm);
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
	foreach (getExplicitTagsOnly (buildTrailFromIds ($tagfilter)) as $taginfo)
		$ret .= "&tagfilter[]=" . $taginfo['id'];
	return $ret;
}

?>
