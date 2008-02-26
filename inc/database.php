<?php
/*
*
*  This file is a library of database access functions for RackTables.
*
*/

function escapeString ($value, $do_db_escape = TRUE)
{
	$ret = htmlentities ($value, ENT_QUOTES, 'UTF-8');
	if ($do_db_escape)
	{
		global $dbxlink;
		$ret = substr ($dbxlink->quote ($ret), 1, -1);
	}
	return $ret;
}

// This function returns detailed information about either all or one
// rack row depending on its argument.
function getRackRowInfo ($rackrow_id = 0)
{
	global $dbxlink;
	$query =
		"select dict_key, dict_value, count(Rack.id) as count, " .
		"if(isnull(sum(Rack.height)),0,sum(Rack.height)) as sum " .
		"from Chapter natural join Dictionary left join Rack on Rack.row_id = dict_key " .
		"where chapter_name = 'RackRow' " .
		($rackrow_id > 0 ? "and dict_key = ${rackrow_id} " : '') .
		"group by dict_key order by dict_value";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	$ret = array();
	$clist = array ('dict_key', 'dict_value', 'count', 'sum');
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach ($clist as $dummy => $cname)
			$ret[$row['dict_key']][$cname] = $row[$cname];
	$result->closeCursor();
	if ($rackrow_id > 0)
		return current ($ret);
	else
		return $ret;
}

// This function returns id->name map for all object types. The map is used
// to build <select> input for objects.
function getObjectTypeList ()
{
	return readChapter ('RackObjectType');
}

function getObjectList ($type_id = 0)
{
	global $dbxlink;
	$query =
		"select distinct RackObject.id as id , RackObject.name as name, dict_value as objtype_name, " .
		"RackObject.label as label, RackObject.barcode as barcode, " .
		"dict_key as objtype_id, asset_no, rack_id, Rack.name as Rack_name from " .
		"((RackObject inner join Dictionary on objtype_id=dict_key natural join Chapter) " .
		"left join RackSpace on RackObject.id = object_id) " .
		"left join Rack on rack_id = Rack.id " .
		"where objtype_id = '${type_id}' and RackObject.deleted = 'no' " .
		"and chapter_name = 'RackObjectType' order by name";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return;
	}
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		foreach (array (
			'id',
			'name',
			'label',
			'barcode',
			'objtype_name',
			'objtype_id',
			'asset_no',
			'rack_id',
			'Rack_name'
			) as $cname)
			$ret[$row['id']][$cname] = $row[$cname];
		$ret[$row['id']]['dname'] = displayedName ($ret[$row['id']]);
	}
	$result->closeCursor();
	return $ret;
}

function getRacksForRow ($row_id = 0)
{
	global $dbxlink;
	$query =
		"select Rack.id, Rack.name, height, Rack.comment, row_id, " .
		"'yes' as left_is_front, 'yes' as bottom_is_unit1, dict_value as row_name " .
		"from Rack left join Dictionary on row_id = dict_key natural join Chapter " .
		"where chapter_name = 'RackRow' and Rack.deleted = 'no' " .
		(($row_id == 0) ? "" : "and row_id = ${row_id} ") .
		"order by row_name, Rack.id";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return;
	}
	$ret = array();
	$clist = array
	(
		'id',
		'name',
		'height',
		'comment',
		'row_id',
		'left_is_front',
		'bottom_is_unit1',
		'row_name'
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach ($clist as $cname)
			$ret[$row['id']][$cname] = $row[$cname];
	$result->closeCursor();
	usort ($ret, 'sortRacks');
	$ret = restoreRackIDs ($ret);
	return $ret;
}

// This is a popular helper for getting information about
// a particular rack and its rackspace at once.
function getRackData ($rack_id = 0, $silent = FALSE)
{
	if ($rack_id == 0)
	{
		if ($silent == FALSE)
			showError ('Invalid rack_id', __FUNCTION__);
		return NULL;
	}
	global $dbxlink;
	$query =
		"select Rack.id, Rack.name, row_id, height, Rack.comment, " .
		"'yes' as left_is_front, 'yes' as bottom_is_unit1, dict_value as row_name from " .
		"Rack left join Dictionary on Rack.row_id = dict_key natural join Chapter " .
		"where chapter_name = 'RackRow' and Rack.id='${rack_id}' and Rack.deleted = 'no' limit 1";
	$result1 = $dbxlink->query ($query);
	if ($result1 == NULL)
	{
		if ($silent == FALSE)
			showError ("SQL query #1 failed", __FUNCTION__);
		return NULL;
	}
	if (($row = $result1->fetch (PDO::FETCH_ASSOC)) == NULL)
	{
		if ($silent == FALSE)
			showError ('Query #1 succeded, but returned no data', __FUNCTION__);
		return NULL;
	}

	// load metadata
	$clist = array
	(
		'id',
		'name',
		'height',
		'comment',
		'row_id',
		'left_is_front',
		'bottom_is_unit1',
		'row_name'
	);
	foreach ($clist as $cname)
		$rack[$cname] = $row[$cname];
	$result1->closeCursor();

	// start with default rackspace
	for ($i = $rack['height']; $i > 0; $i--)
		for ($locidx = 0; $locidx < 3; $locidx++)
			$rack[$i][$locidx]['state'] = 'F';

	// load difference
	$query =
		"select unit_no, atom, state, object_id " .
		"from RackSpace where rack_id = ${rack_id} and " .
		"unit_no between 1 and " . $rack['height'] . " order by unit_no";
	$result2 = $dbxlink->query ($query);
	if ($result2 == NULL)
	{
		if ($silent == FALSE)
			showError ('SQL query failure #2', __FUNCTION__);
		return NULL;
	}
	global $loclist;
	while ($row = $result2->fetch (PDO::FETCH_ASSOC))
	{
		$rack[$row['unit_no']][$loclist[$row['atom']]]['state'] = $row['state'];
		$rack[$row['unit_no']][$loclist[$row['atom']]]['object_id'] = $row['object_id'];
	}
	$result2->closeCursor();
	return $rack;
}

// This is a popular helper.
function getObjectInfo ($object_id = 0)
{
	if ($object_id == 0)
	{
		showError ('Invalid object_id', __FUNCTION__);
		return;
	}
	global $dbxlink;
	$query =
		"select id, name, label, barcode, dict_value as objtype_name, asset_no, dict_key as objtype_id, has_problems, comment from " .
		"RackObject inner join Dictionary on objtype_id = dict_key natural join Chapter " .
		"where id = '${object_id}' and deleted = 'no' and chapter_name = 'RackObjectType' limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		$ei = $dbxlink->errorInfo();
		showError ("SQL query failed with error ${ei[1]} (${ei[2]})", __FUNCTION__);
		return NULL;
	}
	if (($row = $result->fetch (PDO::FETCH_ASSOC)) == NULL)
	{
		showError ('Query succeded, but returned no data', __FUNCTION__);
		$ret = NULL;
	}
	else
	{
		$ret['id'] = $row['id'];
		$ret['name'] = $row['name'];
		$ret['label'] = $row['label'];
		$ret['barcode'] = $row['barcode'];
		$ret['objtype_name'] = $row['objtype_name'];
		$ret['objtype_id'] = $row['objtype_id'];
		$ret['has_problems'] = $row['has_problems'];
		$ret['asset_no'] = $row['asset_no'];
		$ret['dname'] = displayedName ($ret);
		$ret['comment'] = $row['comment'];
	}
	$result->closeCursor();
	return $ret;
}

function getPortTypes ()
{
	return readChapter ('PortType');
}

function getObjectPortsAndLinks ($object_id = 0)
{
	if ($object_id == 0)
	{
		showError ('Invalid object_id', __FUNCTION__);
		return;
	}
	global $dbxlink;
	$query =
		"select Port.id as Port_id, ".
		"Port.name as Port_name, ".
		"Port.label as Port_label, ".
		"Port.l2address as Port_l2address, ".
		"Port.type as Port_type, ".
		"Port.reservation_comment as Port_reservation_comment, " .
		"dict_value as PortType_name, ".
		"RemotePort.id as RemotePort_id, ".
		"RemotePort.name as RemotePort_name, ".
		"RemotePort.object_id as RemotePort_object_id, ".
		"RackObject.name as RackObject_name ".
		"from (".
			"(".
				"(".
					"Port inner join Dictionary on Port.type = dict_key natural join Chapter".
				") ".
				"left join Link on Port.id=Link.porta or Port.id=Link.portb ".
			") ".
			"left join Port as RemotePort on Link.portb=RemotePort.id or Link.porta=RemotePort.id ".
		") ".
		"left join RackObject on RemotePort.object_id=RackObject.id ".
		"where chapter_name = 'PortType' and Port.object_id=${object_id} ".
		"and (Port.id != RemotePort.id or RemotePort.id is null) ".
		"order by Port_name";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		return NULL;
	}
	else
	{
		$ret=array();
		$count=0;
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
		{
			$ret[$count]['id'] = $row['Port_id'];
			$ret[$count]['name'] = $row['Port_name'];
			$ret[$count]['l2address'] = l2addressFromDatabase ($row['Port_l2address']);
			$ret[$count]['label'] = $row['Port_label'];
			$ret[$count]['type_id'] = $row['Port_type'];
			$ret[$count]['type'] = $row['PortType_name'];
			$ret[$count]['reservation_comment'] = $row['Port_reservation_comment'];
			$ret[$count]['remote_id'] = $row['RemotePort_id'];
			$ret[$count]['remote_name'] = htmlentities ($row['RemotePort_name'], ENT_QUOTES);
			$ret[$count]['remote_object_id'] = $row['RemotePort_object_id'];
			$ret[$count]['remote_object_name'] = $row['RackObject_name'];
			// Save on displayedName() calls.
			if (empty ($row['RackObject_name']) and !empty ($row['RemotePort_object_id']))
			{
				$oi = getObjectInfo ($row['RemotePort_object_id']);
				$ret[$count]['remote_object_name'] = displayedName ($oi);
			}
			$count++;
		}
	}
	$result->closeCursor();
	return $ret;
}

function commitAddRack ($name, $height, $row_id, $comment)
{
	global $dbxlink;
	$query = "insert into Rack(row_id, name, height, comment) values('${row_id}', '${name}', '${height}', '${comment}')";
	$result1 = $dbxlink->query ($query);
	if ($result1 == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return FALSE;
	}
	// last_insert_id() is MySQL-specific
	$query = 'select last_insert_id()';
	$result2 = $dbxlink->query ($query);
	if ($result2 == NULL)
	{
		showError ('Cannot get last ID', __FUNCTION__);
		return FALSE;
	}
	// we always have a row
	$row = $result2->fetch (PDO::FETCH_NUM);
	$last_insert_id = $row[0];
	$result2->closeCursor();
	return recordHistory ('Rack', "id = ${last_insert_id}");
}

function commitAddObject ($new_name, $new_label, $new_barcode, $new_type_id, $new_asset_no)
{
	global $dbxlink;
	// Maintain UNIQUE INDEX for common names and asset tags.
	$new_asset_no = empty ($new_asset_no) ? 'NULL' : "'${new_asset_no}'";
	$new_barcode = empty ($new_barcode) ? 'NULL' : "'${new_barcode}'";
	$new_name = empty ($new_name) ? 'NULL' : "'${new_name}'";
	$query =
		"insert into RackObject(name, label, barcode, objtype_id, asset_no) " .
		"values(${new_name}, '${new_label}', ${new_barcode}, '${new_type_id}', ${new_asset_no})";
	$result1 = $dbxlink->query ($query);
	if ($result1 == NULL)
	{
		$errorInfo = $dbxlink->errorInfo();
		showError ("SQL query '${query}' failed: ${errorInfo[2]}", __FUNCTION__);
		die;
	}
	if ($result1->rowCount() != 1)
	{
		showError ('Adding new object failed', __FUNCTION__);
		return FALSE;
	}
	$query = 'select last_insert_id()';
	$result2 = $dbxlink->query ($query);
	if ($result2 == NULL)
	{
		$errorInfo = $dbxlink->errorInfo();
		showError ("SQL query '${query}' failed: ${errorInfo[2]}", __FUNCTION__);
		die;
	}
	// we always have a row
	$row = $result2->fetch (PDO::FETCH_NUM);
	$last_insert_id = $row[0];
	$result2->closeCursor();
	// Do AutoPorts magic
	executeAutoPorts ($last_insert_id, $new_type_id);
	return recordHistory ('RackObject', "id = ${last_insert_id}");
}

function commitUpdateObject ($object_id = 0, $new_name = '', $new_label = '', $new_barcode = '', $new_type_id = 0, $new_has_problems = 'no', $new_asset_no = '', $new_comment = '')
{
	if ($object_id == 0 || $new_type_id == 0)
	{
		showError ('Not all required args are present.', __FUNCTION__);
		return FALSE;
	}
	global $dbxlink;
	$new_asset_no = empty ($new_asset_no) ? 'NULL' : "'${new_asset_no}'";
	$new_barcode = empty ($new_barcode) ? 'NULL' : "'${new_barcode}'";
	$new_name = empty ($new_name) ? 'NULL' : "'${new_name}'";
	$query = "update RackObject set name=${new_name}, label='${new_label}', barcode=${new_barcode}, objtype_id='${new_type_id}', " .
		"has_problems='${new_has_problems}', asset_no=${new_asset_no}, comment='${new_comment}' " .
		"where id='${object_id}' limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query '${query}' failed", __FUNCTION__);
		return FALSE;
	}
	$result->closeCursor();
	return recordHistory ('RackObject', "id = ${object_id}");
}

function commitUpdateRack ($rack_id, $new_name, $new_height, $new_row_id, $new_comment)
{
	if (empty ($rack_id) || empty ($new_name) || empty ($new_height))
	{
		showError ('Not all required args are present.', __FUNCTION__);
		return FALSE;
	}
	global $dbxlink;
	$query = "update Rack set name='${new_name}', height='${new_height}', comment='${new_comment}', row_id=${new_row_id} " .
		"where id='${rack_id}' limit 1";
	$result1 = $dbxlink->query ($query);
	if ($result1->rowCount() != 1)
	{
		showError ('Error updating rack information', __FUNCTION__);
		return FALSE;
	}
	return recordHistory ('Rack', "id = ${rack_id}");
}

// This function accepts rack data returned by getRackData(), validates and applies changes
// supplied in $_REQUEST and returns resulting array. Only those changes are examined, which
// correspond to current rack ID.
// 1st arg is rackdata, 2nd arg is unchecked state, 3rd arg is checked state.
// If 4th arg is present, object_id fields will be updated accordingly to the new state.
// The function returns the modified rack upon success.
function processGridForm (&$rackData, $unchecked_state, $checked_state, $object_id = 0)
{
	global $loclist, $dbxlink;
	$rack_id = $rackData['id'];
	$rack_name = $rackData['name'];
	$rackchanged = FALSE;
	for ($unit_no = $rackData['height']; $unit_no > 0; $unit_no--)
	{
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			if ($rackData[$unit_no][$locidx]['enabled'] != TRUE)
				continue;
			// detect a change
			$state = $rackData[$unit_no][$locidx]['state'];
			if (isset ($_REQUEST["atom_${rack_id}_${unit_no}_${locidx}"]) and $_REQUEST["atom_${rack_id}_${unit_no}_${locidx}"] == 'on')
				$newstate = $checked_state;
			else
				$newstate = $unchecked_state;
			if ($state == $newstate)
				continue;
			$rackchanged = TRUE;
			// and validate
			$atom = $loclist[$locidx];
			// The only changes allowed are those introduced by checkbox grid.
			if
			(
				!($state == $checked_state && $newstate == $unchecked_state) &&
				!($state == $unchecked_state && $newstate == $checked_state)
			)
				return array ('code' => 500, 'message' => "${rack_name}: Rack ID ${rack_id}, unit ${unit_no}, 'atom ${atom}', cannot change state from '${state}' to '${newstate}'");
			// Here we avoid using ON DUPLICATE KEY UPDATE by first performing DELETE
			// anyway and then looking for probable need of INSERT.
			$query =
				"delete from RackSpace where rack_id = ${rack_id} and " .
				"unit_no = ${unit_no} and atom = '${atom}' limit 1";
			$r = $dbxlink->query ($query);
			if ($r == NULL)
				return array ('code' => 500, 'message' => __FUNCTION__ . ": ${rack_name}: SQL DELETE query failed");
			if ($newstate != 'F')
			{
				$query =
					"insert into RackSpace(rack_id, unit_no, atom, state) " .
					"values(${rack_id}, ${unit_no}, '${atom}', '${newstate}') ";
				$r = $dbxlink->query ($query);
				if ($r == NULL)
					return array ('code' => 500, 'message' => __FUNCTION__ . ": ${rack_name}: SQL INSERT query failed");
			}
			if ($newstate == 'T' and $object_id != 0)
			{
				// At this point we already have a record in RackSpace.
				$query =
					"update RackSpace set object_id=${object_id} " .
					"where rack_id=${rack_id} and unit_no=${unit_no} and atom='${atom}' limit 1";
				$r = $dbxlink->query ($query);
				if ($r->rowCount() == 1)
					$rackData[$unit_no][$locidx]['object_id'] = $object_id;
				else
					return array ('code' => 500, 'message' => "${rack_name}: Rack ID ${rack_id}, unit ${unit_no}, atom '${atom}' failed to set object_id to '${object_id}'");
			}
		}
	}
	if ($rackchanged)
	{
		resetThumbCache ($rack_id);
		return array ('code' => 200, 'message' => "${rack_name}: All changes were successfully saved.");
	}
	else
		return array ('code' => 300, 'message' => "${rack_name}: No changes.");
}

// This function builds a list of rack-unit-atom records, which are assigned to
// the requested object.
function getMoleculeForObject ($object_id = 0)
{
	if ($object_id == 0)
	{
		showError ("object_id == 0", __FUNCTION__);
		return NULL;
	}
	global $dbxlink;
	$query =
		"select rack_id, unit_no, atom from RackSpace " .
		"where state = 'T' and object_id = ${object_id} order by rack_id, unit_no, atom";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query failed", __FUNCTION__);
		return NULL;
	}
	$ret = $result->fetchAll (PDO::FETCH_ASSOC);
	$result->closeCursor();
	return $ret;
}

// This function builds a list of rack-unit-atom records for requested molecule.
function getMolecule ($mid = 0)
{
	if ($mid == 0)
	{
		showError ("mid == 0", __FUNCTION__);
		return NULL;
	}
	global $dbxlink;
	$query =
		"select rack_id, unit_no, atom from Atom " .
		"where molecule_id=${mid}";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query failed", __FUNCTION__);
		return NULL;
	}
	$ret = $result->fetchAll (PDO::FETCH_ASSOC);
	$result->closeCursor();
	return $ret;
}

// This function creates a new record in Molecule and number of linked
// R-U-A records in Atom.
function createMolecule ($molData)
{
	global $dbxlink;
	$query = "insert into Molecule values()";
	$result1 = $dbxlink->query ($query);
	if ($result1->rowCount() != 1)
	{
		showError ('Error inserting into Molecule', __FUNCTION__);
		return NULL;
	}
	$query = 'select last_insert_id()';
	$result2 = $dbxlink->query ($query);
	if ($result2 == NULL)
	{
		showError ('Cannot get last ID.', __FUNCTION__);
		return NULL;
	}
	$row = $result2->fetch (PDO::FETCH_NUM);
	$molecule_id = $row[0];
	$result2->closeCursor();
	foreach ($molData as $dummy => $rua)
	{
		$rack_id = $rua['rack_id'];
		$unit_no = $rua['unit_no'];
		$atom = $rua['atom'];
		$query =
			"insert into Atom(molecule_id, rack_id, unit_no, atom) " .
			"values (${molecule_id}, ${rack_id}, ${unit_no}, '${atom}')";
		$result3 = $dbxlink->query ($query);
		if ($result3 == NULL or $result3->rowCount() != 1)
		{
			showError ('Error inserting into Atom', __FUNCTION__);
			return NULL;
		}
	}
	return $molecule_id;
}

// History logger. This function assumes certain table naming convention and
// column design:
// 1. History table name equals to dictionary table name plus 'History'.
// 2. History table must have the same row set (w/o keys) plus one row named
// 'ctime' of type 'timestamp'.
function recordHistory ($tableName, $whereClause)
{
	global $dbxlink, $remote_username;
	$query = "insert into ${tableName}History select *, current_timestamp(), '${remote_username}' from ${tableName} where ${whereClause}";
	$result = $dbxlink->query ($query);
	if ($result == NULL or $result->rowCount() != 1)
	{
		showError ("SQL query '${query}' failed for table ${tableName}", __FUNCTION__);
		return FALSE;
	}
	return TRUE;
}

function getRackspaceHistory ()
{
	global $dbxlink;
	$query =
		"select mo.id as mo_id, ro.id as ro_id, ro.name, mo.ctime, mo.comment, dict_value as objtype_name, user_name from " .
		"MountOperation as mo inner join RackObject as ro on mo.object_id = ro.id " .
		"inner join Dictionary on objtype_id = dict_key natural join Chapter " .
		"where chapter_name = 'RackObjectType' order by ctime desc";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return;
	}
	$ret = $result->fetchAll(PDO::FETCH_ASSOC);
	$result->closeCursor();
	return $ret;
}

// This function is used in renderRackspaceHistory()
function getOperationMolecules ($op_id = 0)
{
	if ($op_id <= 0)
	{
		showError ("Missing argument", __FUNCTION__);
		return;
	}
	global $dbxlink;
	$query = "select old_molecule_id, new_molecule_id from MountOperation where id = ${op_id}";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query failed", __FUNCTION__);
		return;
	}
	// We expect one row.
	$row = $result->fetch (PDO::FETCH_ASSOC);
	if ($row == NULL)
	{
		showError ("SQL query succeded, but returned no results.", __FUNCTION__);
		return;
	}
	$omid = $row['old_molecule_id'];
	$nmid = $row['new_molecule_id'];
	$result->closeCursor();
	return array ($omid, $nmid);
}

function getResidentRacksData ($object_id = 0, $fetch_rackdata = TRUE)
{
	if ($object_id <= 0)
	{
		showError ('Invalid object_id', __FUNCTION__);
		return;
	}
	$query = "select distinct rack_id from RackSpace where object_id = ${object_id} order by rack_id";
	global $dbxlink;
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query failed", __FUNCTION__);
		return;
	}
	$rows = $result->fetchAll (PDO::FETCH_NUM);
	$result->closeCursor();
	$ret = array();
	foreach ($rows as $row)
	{
		if (!$fetch_rackdata)
		{
			$ret[$row[0]] = $row[0];
			continue;
		}
		$rackData = getRackData ($row[0]);
		if ($rackData == NULL)
		{
			showError ('getRackData() failed', __FUNCTION__);
			return NULL;
		}
		$ret[$row[0]] = $rackData;
	}
	$result->closeCursor();
	return $ret;
}

function getObjectGroupInfo ($group_id = 0)
{
	$query =
		'select dict_key as id, dict_value as name, count(id) as count from ' .
		'Dictionary natural join Chapter left join RackObject on dict_key = objtype_id ' .
		'where chapter_name = "RackObjectType" ' .
		(($group_id > 0) ? "and dict_key = ${group_id} " : '') .
		'group by dict_key';
	global $dbxlink;
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	$ret = array();
	$clist = array ('id', 'name', 'count');
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach ($clist as $dummy => $cname)
			$ret[$row['id']][$cname] = $row[$cname];
	$result->closeCursor();
	if ($group_id > 0)
		return current ($ret);
	else
		return $ret;
}

// This function returns objects, which have no rackspace assigned to them.
// Additionally it keeps rack_id parameter, so we can silently pre-select
// the rack required.
function getUnmountedObjects ()
{
	global $dbxlink;
	$query =
		'select dict_value as objtype_name, dict_key as objtype_id, name, label, barcode, id, asset_no from ' .
		'RackObject inner join Dictionary on objtype_id = dict_key natural join Chapter ' .
		'left join RackSpace on id = object_id '.
		'where rack_id is null and chapter_name = "RackObjectType" order by dict_value, name, label, asset_no, barcode';
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failure', __FUNCTION__);
		return NULL;
	}
	$ret = array();
	$clist = array ('id', 'name', 'label', 'barcode', 'objtype_name', 'objtype_id', 'asset_no');
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		foreach ($clist as $dummy => $cname)
			$ret[$row['id']][$cname] = $row[$cname];
		$ret[$row['id']]['dname'] = displayedName ($ret[$row['id']]);
	}
	$result->closeCursor();
	return $ret;
}

function getProblematicObjects ()
{
	global $dbxlink;
	$query =
		'select dict_value as objtype_name, dict_key as objtype_id, name, id, asset_no from ' .
		'RackObject inner join Dictionary on objtype_id = dict_key natural join Chapter '.
		'where has_problems = "yes" and chapter_name = "RackObjectType" order by objtype_name, name';
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failure', __FUNCTION__);
		return NULL;
	}
	$ret = array();
	$clist = array ('id', 'name', 'objtype_name', 'objtype_id', 'asset_no');
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		foreach ($clist as $dummy => $cname)
			$ret[$row['id']][$cname] = $row[$cname];
		$ret[$row['id']]['dname'] = displayedName ($ret[$row['id']]);
	}
	$result->closeCursor();
	return $ret;
}

function commitAddPort ($object_id = 0, $port_name, $port_type_id, $port_label, $port_l2address)
{
	if ($object_id <= 0)
	{
		showError ('Invalid object_id', __FUNCTION__);
		return;
	}
	$port_l2address = l2addressForDatabase ($port_l2address);
	$result = useInsertBlade
	(
		'Port',
		array
		(
			'name' => "'${port_name}'",
			'object_id' => "'${object_id}'",
			'label' => "'${port_label}'",
			'type' => "'${port_type_id}'",
			'l2address' => "${port_l2address}"
		)
	);
	if ($result)
		return '';
	else
		return 'SQL query failed';
}

function commitUpdatePort ($port_id, $port_name, $port_label, $port_l2address, $port_reservation_comment)
{
	global $dbxlink;
	$port_l2address = l2addressForDatabase ($port_l2address);
	$query =
		"update Port set name='$port_name', label='$port_label', " .
		"reservation_comment = ${port_reservation_comment}, l2address=${port_l2address} " .
		"where id='$port_id'";
	$result = $dbxlink->exec ($query);
	if ($result == 1)
		return '';
	$errorInfo = $dbxlink->errorInfo();
	// We could update nothing.
	if ($errorInfo[0] == '00000')
		return '';
	return $errorInfo[2];
}

function delObjectPort ($port_id)
{
	if (unlinkPort ($port_id) != '')
		return __FUNCTION__ . ': unlinkPort() failed';
	if (useDeleteBlade ('Port', 'id', $port_id) != TRUE)
		return __FUNCTION__ . ': useDeleteBlade() failed';
	return '';
}

function getObjectAddressesAndNames ()
{
	global $dbxlink;
	$query =
		"select object_id as object_id, ".
		"RackObject.name as object_name, ".
		"IPBonds.name as name, ".
		"INET_NTOA(ip) as ip ".
		"from IPBonds join RackObject on id=object_id ";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query failure", __FUNCTION__);
		return NULL;
	}
	$ret = array();
	$count=0;
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ret[$count]['object_id']=$row['object_id'];
		$ret[$count]['object_name']=$row['object_name'];
		$ret[$count]['name']=$row['name'];
		$ret[$count]['ip']=$row['ip'];
		$count++;
	}
	$result->closeCursor();
	return $ret;
}


function getEmptyPortsOfType ($type_id)
{
	global $dbxlink;
	$query =
		"select distinct Port.id as Port_id, ".
		"Port.object_id as Port_object_id, ".
		"RackObject.name as Object_name, ".
		"Port.name as Port_name, ".
		"Port.type as Port_type_id, ".
		"dict_value as Port_type_name ".
		"from ( ".
		"	( ".
		"		Port inner join Dictionary on Port.type = dict_key natural join Chapter ".
		"	) ".
		" 	join RackObject on Port.object_id = RackObject.id ".
		") ".
		"left join Link on Port.id=Link.porta or Port.id=Link.portb ".
		"inner join PortCompat on Port.type = PortCompat.type2 ".
		"where chapter_name = 'PortType' and PortCompat.type1 = '$type_id' and Link.porta is NULL ".
		"and Port.reservation_comment is null order by Object_name, Port_name";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query failure, type_id == ${type_id}", __FUNCTION__);
		return NULL;
	}
	$ret = array();
	$count=0;
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ret[$count]['Port_id']=$row['Port_id'];
		$ret[$count]['Port_object_id']=$row['Port_object_id'];
		$ret[$count]['Object_name']=$row['Object_name'];
		$ret[$count]['Port_name']=$row['Port_name'];
		$ret[$count]['Port_type_id']=$row['Port_type_id'];
		$ret[$count]['Port_type_name']=$row['Port_type_name'];
		$count++;
	}
	$result->closeCursor();
	return $ret;
}

function linkPorts ($porta, $portb)
{
	if ($porta == $portb)
		return "Ports can't be the same";
	if ($porta > $portb)
	{
		$tmp = $porta;
		$porta = $portb;
		$portb = $tmp;
	}
	global $dbxlink;
	$query1 = "insert into Link set porta='${porta}', portb='{$portb}'";
	$query2 = "update Port set reservation_comment = NULL where id = ${porta} or id = ${portb} limit 2";
	// FIXME: who cares about the return value?
	$result = $dbxlink->exec ($query1);
	$result = $dbxlink->exec ($query2);
	return '';
	
}

function unlinkPort ($port)
{
	global $dbxlink;
	$query =
		"delete from Link where porta='$port' or portb='$port'";
	$result = $dbxlink->exec ($query);
	return '';
	
}

// FIXME: after falling back to using existing getObjectInfo we don't
// need that large query. Shrink it some later.
function getObjectAddresses ($object_id = 0)
{
	if ($object_id == 0)
	{
		showError ('Invalid object_id', __FUNCTION__);
		return;
	}
	global $dbxlink;
	$query =
		"select ".
		"IPAddress.name as IPAddress_name, ".
		"IPAddress.reserved as IPAddress_reserved, ".
		"IPBonds.name as IPBonds_name, ".
		"INET_NTOA(IPBonds.ip) as IPBonds_ip, ".
		"IPBonds.type as IPBonds_type, ".
		"RemoteBonds.name as RemoteBonds_name, ".
		"RemoteBonds.type as RemoteBonds_type, ".
		"RemoteBonds.object_id as RemoteBonds_object_id, ".
		"RemoteObject.name as RemoteObject_name from IPBonds " .
		"left join IPBonds as RemoteBonds on IPBonds.ip=RemoteBonds.ip " .
			"and IPBonds.object_id!=RemoteBonds.object_id " .
		"left join IPAddress on IPBonds.ip=IPAddress.ip " .
		"left join RackObject as RemoteObject on RemoteBonds.object_id=RemoteObject.id ".
		"where ".
		"IPBonds.object_id = ${object_id} ".
		"order by IPBonds.ip, RemoteObject.name";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query failed", __FUNCTION__);
		return NULL;
	}
	else
	{
		$ret=array();
		$count=0;
		$refcount=0;
		$prev_ip = 0;
		// We are going to call getObjectInfo() for some rows,
		// hence the connector must be unloaded from the
		// current data.
		$rows = $result->fetchAll (PDO::FETCH_ASSOC);
		$result->closeCursor();
		foreach ($rows as $row)
		{
			if ($prev_ip != $row['IPBonds_ip'])
			{
				$count++;
				$refcount=0;
				$prev_ip = $row['IPBonds_ip'];
				$ret[$count]['address_name'] = $row['IPAddress_name'];
				$ret[$count]['address_reserved'] = $row['IPAddress_reserved'];
				$ret[$count]['ip'] = $row['IPBonds_ip'];
				$ret[$count]['name'] = $row['IPBonds_name'];
				$ret[$count]['type'] = $row['IPBonds_type'];
				$ret[$count]['references'] = array();
			}

			if ($row['RemoteBonds_type'])
			{
				$ret[$count]['references'][$refcount]['type'] = $row['RemoteBonds_type'];
				$ret[$count]['references'][$refcount]['name'] = $row['RemoteBonds_name'];
				$ret[$count]['references'][$refcount]['object_id'] = $row['RemoteBonds_object_id'];
				if (empty ($row['RemoteBonds_object_id']))
					$ret[$count]['references'][$refcount]['object_name'] = $row['RemoteObject_name'];
				else
				{
					$oi = getObjectInfo ($row['RemoteBonds_object_id']);
					$ret[$count]['references'][$refcount]['object_name'] = displayedName ($oi);
				}
				$refcount++;
			}
		}
	}
	return $ret;
}

function getAddressspaceList ()
{
	global $dbxlink;
	$query =
		"select ".
		"id as IPRanges_id, ".
		"INET_NTOA(ip) as IPRanges_ip, ".
		"mask as IPRanges_mask, ".
		"name as IPRanges_name ".
		"from IPRanges ".
		"order by ip";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		return NULL;
	}
	else
	{
		$ret=array();
		$count=0;
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
		{
			$ret[$count]['id'] = $row['IPRanges_id'];
			$ret[$count]['ip'] = $row['IPRanges_ip'];
			$ret[$count]['ip_bin'] = ip2long($row['IPRanges_ip']);
			$ret[$count]['name'] = $row['IPRanges_name'];
			$ret[$count]['mask'] = $row['IPRanges_mask'];
			$ret[$count]['mask_bin'] = binMaskFromDec($row['IPRanges_mask']);
			$ret[$count]['mask_bin_inv'] = binInvMaskFromDec($row['IPRanges_mask']);
			$count++;
		}
	}
	$result->closeCursor();
	return $ret;

}

function getRangeByIp ($ip = '', $id = 0)
{
	global $dbxlink;
	if ($id == 0)
		$query =
			"select ".
			"id, INET_NTOA(ip) as ip, mask, name ".
			"from IPRanges ";
	else
		$query =
			"select ".
			"id, INET_NTOA(ip) as ip, mask, name ".
			"from IPRanges where id='$id'";
		
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		return NULL;
	}
	else
	{
		$ret=array();
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
		{
			$binmask=binMaskFromDec($row['mask']);
			if ((ip2long($ip) & $binmask) == ip2long($row['ip']))
			{
				$ret['id'] = $row['id'];
				$ret['ip'] = $row['ip'];
				$ret['ip_bin'] = ip2long($row['ip']);
				$ret['name'] = $row['name'];
				$ret['mask'] = $row['mask'];
				$result->closeCursor();
				return $ret;
			}
		}
	}
	$result->closeCursor();
	return NULL;
}

function updateRange ($id=0, $name='')
{
	global $dbxlink;
	$query =
		"update IPRanges set name='$name' where id='$id'";
	$result = $dbxlink->exec ($query);
	return '';
	
}

// This function is actually used not only to update, but also to create records,
// that's why ON DUPLICATE KEY UPDATE was replaced by DELETE-INSERT pair
// (MySQL 4.0 workaround).
function updateAddress ($ip=0, $name='', $reserved='no')
{
	// DELETE may safely fail.
	$r = useDeleteBlade ('IPAddress', 'ip', "INET_ATON('${ip}')", FALSE);
	// INSERT may appear not necessary.
	if ($name == '' and $reserved == 'no')
		return '';
	if (useInsertBlade ('IPAddress', array ('name' => "'${name}'", 'reserved' => "'${reserved}'", 'ip' => "INET_ATON('${ip}')")))
		return '';
	else
		return 'useInsertBlade() failed in updateAddress()';
}

// FIXME: This function doesn't wipe relevant records from IPAddress table.
function commitDeleteRange ($id = 0)
{
	if ($id <= 0)
		return __FUNCTION__ . ': Invalid range ID';
	if (useDeleteBlade ('IPRanges', 'id', $id))
		return '';
	else
		return __FUNCTION__ . ': SQL query failed';
}

function updateBond ($ip='', $object_id=0, $name='', $type='')
{
	global $dbxlink;

	$query =
		"update IPBonds set name='$name', type='$type' where ip=INET_ATON('$ip') and object_id='$object_id'";
	$result = $dbxlink->exec ($query);
	return '';
}

function unbindIpFromObject ($ip='', $object_id=0)
{
	global $dbxlink;

	$query =
		"delete from IPBonds where ip=INET_ATON('$ip') and object_id='$object_id'";
	$result = $dbxlink->exec ($query);
	return '';
}

// This function returns either all or one user account. Array key is user name.
function getUserAccounts ()
{
	global $dbxlink;
	$query =
		'select user_id, user_name, user_password_hash, user_realname, user_enabled ' .
		'from UserAccount order by user_name';
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	$ret = array();
	$clist = array ('user_id', 'user_name', 'user_realname', 'user_password_hash', 'user_enabled');
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach ($clist as $dummy => $cname)
			$ret[$row['user_name']][$cname] = $row[$cname];
	$result->closeCursor();
	return $ret;
}

// This function returns permission array for all user accounts. Array key is user name.
function getUserPermissions ()
{
	global $dbxlink;
	$query =
		"select UserPermission.user_id, user_name, page, tab, access from " .
		"UserPermission natural left join UserAccount where (user_name is not null) or " .
		"(user_name is null and UserPermission.user_id = 0) order by user_name, page, tab";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		if ($row['user_id'] == 0)
			$row['user_name'] = '%';
		$ret[$row['user_name']][$row['page']][$row['tab']] = $row['access'];
	}
	$result->closeCursor();
	return $ret;
}

function searchByl2address ($l2addr)
{
	global $dbxlink;
	$l2addr = l2addressForDatabase ($l2addr);
	$query = "select object_id, Port.id as port_id from RackObject as ro inner join Port on ro.id = Port.object_id " .
		"where l2address = ${l2addr}";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	$result->closeCursor();
	if (count ($rows) == 0) // No results.
		return NULL;
	if (count ($rows) == 1) // Target found.
		return $rows[0];
	showError ('More than one results was found. This is probably a broken unique key.', __FUNCTION__);
	return NULL;
}

// This function returns either port ID or NULL for specified arguments.
function getPortID ($object_id, $port_name)
{
	global $dbxlink;
	$query = "select id from Port where object_id=${object_id} and name='${port_name}' limit 2";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	$rows = $result->fetchAll (PDO::FETCH_NUM);
	if (count ($rows) != 1)
		return NULL;
	$ret = $rows[0][0];
	$result->closeCursor();
	return $ret;
}

function commitCreateUserAccount ($username, $realname, $password)
{
	return useInsertBlade
	(
		'UserAccount',
		array
		(
			'user_name' => "'${username}'",
			'user_realname' => "'${realname}'",
			'user_password_hash' => "'${password}'"
		)
	);
}

function commitUpdateUserAccount ($id, $new_username, $new_realname, $new_password)
{
	global $dbxlink;
	$query =
		"update UserAccount set user_name = '${new_username}', user_realname = '${new_realname}', " .
		"user_password_hash = '${new_password}' where user_id = ${id} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		die;
	}
	return TRUE;
}

function commitEnableUserAccount ($id, $new_enabled_value)
{
	global $dbxlink;
	$query =
		"update UserAccount set user_enabled = '${new_enabled_value}' " .
		"where user_id = ${id} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		die;
	}
	return TRUE;
}

function commitGrantPermission ($userid, $page, $tab, $value)
{
	return useInsertBlade
	(
		'UserPermission',
		array
		(
			'user_id' => $userid,
			'page' => "'${page}'",
			'tab' => "'${tab}'",
			'access' => "'${value}'"
		)
	);
}

function commitRevokePermission ($userid, $page, $tab)
{
	global $dbxlink;
	$query =
		"delete from UserPermission where user_id = '${userid}' and page = '${page}' " .
		"and tab = '$tab' limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		die;
	}
	return TRUE;
}

// This function returns an array of all port type pairs from PortCompat table.
function getPortCompat ()
{
	global $dbxlink;
	$query =
		"select type1, type2, d1.dict_value as type1name, d2.dict_value as type2name from " .
		"PortCompat as pc inner join Dictionary as d1 on pc.type1 = d1.dict_key " .
		"inner join Dictionary as d2 on pc.type2 = d2.dict_key " .
		"inner join Chapter as c1 on d1.chapter_no = c1.chapter_no " .
		"inner join Chapter as c2 on d2.chapter_no = c2.chapter_no " .
		"where c1.chapter_name = 'PortType' and c2.chapter_name = 'PortType'";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	$ret = $result->fetchAll (PDO::FETCH_ASSOC);
	$result->closeCursor();
	return $ret;
}

function removePortCompat ($type1 = 0, $type2 = 0)
{
	global $dbxlink;
	if ($type1 == 0 or $type2 == 0)
	{
		showError ('Invalid arguments', __FUNCTION__);
		die;
	}
	$query = "delete from PortCompat where type1 = ${type1} and type2 = ${type2} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		die;
	}
	return TRUE;
}

function addPortCompat ($type1 = 0, $type2 = 0)
{
	if ($type1 <= 0 or $type2 <= 0)
	{
		showError ('Invalid arguments', __FUNCTION__);
		die;
	}
	return useInsertBlade
	(
		'PortCompat',
		array ('type1' => $type1, 'type2' => $type2)
	);
}

// This function returns the dictionary as an array of trees, each tree
// representing a single chapter. Each element has 'id', 'name', 'sticky'
// and 'word' keys with the latter holding all the words within the chapter.
function getDict ()
{
	global $dbxlink;
	$query1 =
		"select chapter_name, Chapter.chapter_no, dict_key, dict_value, sticky from " .
		"Chapter natural left join Dictionary order by chapter_name, dict_value";
	$result1 = $dbxlink->query ($query1);
	if ($result1 == NULL)
	{
		showError ('SQL query #1 failed', __FUNCTION__);
		return NULL;
	}
	$dict = array();
	while ($row = $result1->fetch (PDO::FETCH_ASSOC))
	{
		$chapter_no = $row['chapter_no'];
		if (!isset ($dict[$chapter_no]))
		{
			$dict[$chapter_no]['no'] = $chapter_no;
			$dict[$chapter_no]['name'] = $row['chapter_name'];
			$dict[$chapter_no]['sticky'] = $row['sticky'] == 'yes' ? TRUE : FALSE;
			$dict[$chapter_no]['word'] = array();
		}
		if ($row['dict_key'] != NULL)
		{
			$dict[$chapter_no]['word'][$row['dict_key']] = $row['dict_value'];
			$dict[$chapter_no]['refcnt'][$row['dict_key']] = 0;
		}
	}
	$result1->closeCursor();
// Find the list of all assigned values of dictionary-addressed attributes, each with
// chapter/word keyed reference counters. Use the structure to adjust reference counters
// of the returned disctionary words.
	$query2 = "select a.attr_id, am.chapter_no, uint_value, count(object_id) as refcnt " .
		"from Attribute as a inner join AttributeMap as am on a.attr_id = am.attr_id " .
		"inner join AttributeValue as av on a.attr_id = av.attr_id " .
		"inner join Dictionary as d on am.chapter_no = d.chapter_no and av.uint_value = d.dict_key " .
		"where attr_type = 'dict' group by a.attr_id, am.chapter_no, uint_value " .
		"order by a.attr_id, am.chapter_no, uint_value";
	$result2 = $dbxlink->query ($query2);
	if ($result2 == NULL)
	{
		showError ('SQL query #2 failed', __FUNCTION__);
		return NULL;
	}
	$refcnt = array();
	while ($row = $result2->fetch (PDO::FETCH_ASSOC))
		$dict[$row['chapter_no']]['refcnt'][$row['uint_value']] = $row['refcnt'];
	$result2->closeCursor();
	return $dict;
}

function getDictStats ()
{
	$stock_chapters = array (1, 2, 3, 11, 12, 13, 14, 16, 17, 18, 19, 20, 21, 22, 23);
	global $dbxlink;
	$query =
		"select Chapter.chapter_no, chapter_name, count(dict_key) as wc from " .
		"Chapter natural left join Dictionary group by Chapter.chapter_no";
	$result1 = $dbxlink->query ($query);
	if ($result1 == NULL)
	{
		showError ('SQL query #1 failed', __FUNCTION__);
		return NULL;
	}
	$tc = $tw = $uc = $uw = 0;
	while ($row = $result1->fetch (PDO::FETCH_ASSOC))
	{
		$tc++;
		$tw += $row['wc'];;
		if (in_array ($row['chapter_no'], $stock_chapters))
			continue;
		$uc++;
		$uw += $row['wc'];;
	}
	$result1->closeCursor();
	$query = "select count(attr_id) as attrc from RackObject as ro left join " .
		"AttributeValue as av on ro.id = av.object_id group by ro.id";
	$result2 = $dbxlink->query ($query);
	if ($result2 == NULL)
	{
		showError ('SQL query #2 failed', __FUNCTION__);
		return NULL;
	}
	$to = $ta = $so = 0;
	while ($row = $result2->fetch (PDO::FETCH_ASSOC))
	{
		$to++;
		if ($row['attrc'] != 0)
		{
			$so++;
			$ta += $row['attrc'];
		}
	}
	$result2->closeCursor();
	$ret = array();
	$ret['Total chapters in dictionary'] = $tc;
	$ret['Total words in dictionary'] = $tw;
	$ret['User chapters'] = $uc;
	$ret['Words in user chapters'] = $uw;
	$ret['Total objects'] = $to;
	$ret['Objects with stickers'] = $so;
	$ret['Total stickers attached']  = $ta;
	return $ret;
}

function getIPv4Stats()
{
	global $dbxlink;
	$ret = array();
	$subject = array();
	$subject[] = array ('q' => 'select count(id) from IPRanges', 'txt' => 'Networks');
	$subject[] = array ('q' => 'select count(ip) from IPAddress', 'txt' => 'Addresses commented/reserved');
	$subject[] = array ('q' => 'select count(ip) from IPBonds', 'txt' => 'Addresses allocated');
	$subject[] = array ('q' => 'select count(*) from PortForwarding', 'txt' => 'NAT rules');
	$subject[] = array ('q' => 'select count(id) from IPVirtualService', 'txt' => 'Virtual services');
	$subject[] = array ('q' => 'select count(id) from IPRSPool', 'txt' => 'Real server pools');
	$subject[] = array ('q' => 'select count(id) from IPRealServer', 'txt' => 'Real servers');
	$subject[] = array ('q' => 'select count(distinct object_id) from IPLoadBalancer', 'txt' => 'Load balancers');

	foreach ($subject as $item)
	{
		$result = $dbxlink->query ($item['q']);
		if ($result == NULL)
		{
			showError ("SQL query '${item['q']}' failed", __FUNCTION__);
			return NULL;
		}
		$row = $result->fetch (PDO::FETCH_NUM);
		$ret[$item['txt']] = $row[0];
		$result->closeCursor();
		unset ($result);
	}
	return $ret;
}

function getRackspaceStats()
{
	global $dbxlink;
	$ret = array();
	$subject = array();
	$subject[] = array ('q' => 'select count(*) from Dictionary where chapter_no = 3', 'txt' => 'Rack rows');
	$subject[] = array ('q' => 'select count(*) from Rack', 'txt' => 'Racks');
	$subject[] = array ('q' => 'select avg(height) from Rack', 'txt' => 'Average rack height');
	$subject[] = array ('q' => 'select sum(height) from Rack', 'txt' => 'Total rack units in field');

	foreach ($subject as $item)
	{
		$result = $dbxlink->query ($item['q']);
		if ($result == NULL)
		{
			showError ("SQL query '${item['q']}' failed", __FUNCTION__);
			return NULL;
		}
		$row = $result->fetch (PDO::FETCH_NUM);
		$ret[$item['txt']] = empty ($row[0]) ? 0 : $row[0];
		$result->closeCursor();
		unset ($result);
	}
	return $ret;
}

function commitUpdateDictionary ($chapter_no = 0, $dict_key = 0, $dict_value = '')
{
	if ($chapter_no <= 0 or $dict_key <= 0 or empty ($dict_value))
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	global $dbxlink;
	$query =
		"update Dictionary set dict_value = '${dict_value}' where chapter_no=${chapter_no} " .
		"and dict_key=${dict_key} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		die;
	}
	return TRUE;
}

function commitSupplementDictionary ($chapter_no = 0, $dict_value = '')
{
	if ($chapter_no <= 0 or empty ($dict_value))
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	return useInsertBlade
	(
		'Dictionary',
		array ('chapter_no' => $chapter_no, 'dict_value' => "'${dict_value}'")
	);
}

function commitReduceDictionary ($chapter_no = 0, $dict_key = 0)
{
	if ($chapter_no <= 0 or $dict_key <= 0)
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	global $dbxlink;
	$query =
		"delete from Dictionary where chapter_no=${chapter_no} " .
		"and dict_key=${dict_key} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		die;
	}
	return TRUE;
}

function commitAddChapter ($chapter_name = '')
{
	if (empty ($chapter_name))
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	return useInsertBlade
	(
		'Chapter',
		array ('chapter_name' => "'${chapter_name}'")
	);
}

function commitUpdateChapter ($chapter_no = 0, $chapter_name = '')
{
	if ($chapter_no <= 0 or empty ($chapter_name))
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	global $dbxlink;
	$query =
		"update Chapter set chapter_name = '${chapter_name}' where chapter_no = ${chapter_no} " .
		"and sticky = 'no' limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		die;
	}
	return TRUE;
}

function commitDeleteChapter ($chapter_no = 0)
{
	if ($chapter_no <= 0)
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	global $dbxlink;
	$query =
		"delete from Chapter where chapter_no = ${chapter_no} and sticky = 'no' limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		die;
	}
	return TRUE;
}

// This is a dictionary accessor. We perform link rendering, so the user sees
// nice <select> drop-downs.
function readChapter ($chapter_name = '')
{
	if (empty ($chapter_name))
	{
		showError ('invalid argument', __FUNCTION__);
		return NULL;
	}
	global $dbxlink;
	$query =
		"select dict_key, dict_value from Dictionary natural join Chapter " .
		"where chapter_name = '${chapter_name}'";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		$errorInfo = $dbxlink->errorInfo();
		showError ("SQL query '${query}'\nwith message '${errorInfo[2]}'\nfailed for chapter_no = '${chapter_name}'", __FUNCTION__);
		return NULL;
	}
	$chapter = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$chapter[$row['dict_key']] = parseWikiLink ($row['dict_value'], 'o');
	$result->closeCursor();
	// SQL ORDER BY had no sense, because we need to sort after link rendering, not before.
	asort ($chapter);
	return $chapter;
}

function getAttrMap ()
{
	global $dbxlink;
	$query =
		"select a.attr_id, a.attr_type, a.attr_name, am.objtype_id, " .
		"d.dict_value as objtype_name, am.chapter_no, c2.chapter_name from " .
		"Attribute as a natural left join AttributeMap as am " .
		"left join Dictionary as d on am.objtype_id = d.dict_key " .
		"left join Chapter as c1 on d.chapter_no = c1.chapter_no " .
		"left join Chapter as c2 on am.chapter_no = c2.chapter_no " .
		"where c1.chapter_name = 'RackObjectType' or c1.chapter_name is null " .
		"order by attr_name";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		$errorInfo = $dbxlink->errorInfo();
		showError ("SQL query '${query}'\nwith message '${errorInfo[2]}'\nfailed", __FUNCTION__);
		return NULL;
	}
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$attr_id = $row['attr_id'];
		if (!isset ($ret[$attr_id]))
		{
			$ret[$attr_id]['id'] = $attr_id;
			$ret[$attr_id]['type'] = $row['attr_type'];
			$ret[$attr_id]['name'] = $row['attr_name'];
			$ret[$attr_id]['application'] = array();
		}
		if ($row['objtype_id'] == '')
			continue;
		$application['objtype_id'] = $row['objtype_id'];
		$application['objtype_name'] = $row['objtype_name'];
		if ($row['attr_type'] == 'dict')
		{
			$application['chapter_no'] = $row['chapter_no'];
			$application['chapter_name'] = $row['chapter_name'];
		}
		$ret[$attr_id]['application'][] = $application;
	}
	$result->closeCursor();
	return $ret;
}

function commitUpdateAttribute ($attr_id = 0, $attr_name = '')
{
	if ($attr_id <= 0 or empty ($attr_name))
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	global $dbxlink;
	$query =
		"update Attribute set attr_name = '${attr_name}' " .
		"where attr_id = ${attr_id} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query '${query}' failed", __FUNCTION__);
		die;
	}
	return TRUE;
}

function commitAddAttribute ($attr_name = '', $attr_type = '')
{
	if (empty ($attr_name))
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	switch ($attr_type)
	{
		case 'uint':
		case 'float':
		case 'string':
		case 'dict':
			break;
		default:
			showError ('Invalid args', __FUNCTION__);
			die;
	}
	return useInsertBlade
	(
		'Attribute',
		array ('attr_name' => "'${attr_name}'", 'attr_type' => "'${attr_type}'")
	);
}

function commitDeleteAttribute ($attr_id = 0)
{
	if ($attr_id <= 0)
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	return useDeleteBlade ('Attribute', 'attr_id', $attr_id);
}

// FIXME: don't store garbage in chapter_no for non-dictionary types.
function commitSupplementAttrMap ($attr_id = 0, $objtype_id = 0, $chapter_no = 0)
{
	if ($attr_id <= 0 or $objtype_id <= 0 or $chapter_no <= 0)
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	return useInsertBlade
	(
		'AttributeMap',
		array
		(
			'attr_id' => $attr_id,
			'objtype_id' => $objtype_id,
			'chapter_no' => $chapter_no
		)
	);
}

function commitReduceAttrMap ($attr_id = 0, $objtype_id)
{
	if ($attr_id <= 0 or $objtype_id <= 0)
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	global $dbxlink;
	$query =
		"delete from AttributeMap where attr_id=${attr_id} " .
		"and objtype_id=${objtype_id} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		die;
	}
	return TRUE;
}

// This function returns all optional attributes for requested object
// as an array of records. NULL is returned on error and empty array
// is returned, if there are no attributes found.
function getAttrValues ($object_id)
{
	if ($object_id <= 0)
	{
		showError ('Invalid argument', __FUNCTION__);
		return NULL;
	}
	global $dbxlink;
	$ret = array();
	$query =
		"select A.attr_id, A.attr_name, A.attr_type, C.chapter_name, " .
		"AV.uint_value, AV.float_value, AV.string_value, D.dict_value from " .
		"RackObject as RO inner join AttributeMap as AM on RO.objtype_id = AM.objtype_id " .
		"inner join Attribute as A using (attr_id) " .
		"left join AttributeValue as AV on AV.attr_id = AM.attr_id and AV.object_id = RO.id " .
		"left join Dictionary as D on D.dict_key = AV.uint_value and AM.chapter_no = D.chapter_no " .
		"left join Chapter as C on AM.chapter_no = C.chapter_no " .
		"where RO.id = ${object_id} order by A.attr_type";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		$errorInfo = $dbxlink->errorInfo();
		showError ("SQL query '${query}'\nwith message '${errorInfo[2]}'\nfailed", __FUNCTION__);
		return NULL;
	}
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$record = array();
		$record['id'] = $row['attr_id'];
		$record['name'] = $row['attr_name'];
		$record['type'] = $row['attr_type'];
		switch ($row['attr_type'])
		{
			case 'uint':
			case 'float':
			case 'string':
			case 'dict':
				$record['value'] = parseWikiLink ($row[$row['attr_type'] . '_value'], 'o');
				$record['a_value'] = parseWikiLink ($row[$row['attr_type'] . '_value'], 'a');
				$record['chapter_name'] = $row['chapter_name'];
				$record['key'] = $row['uint_value'];
				break;
			default:
				$record['value'] = NULL;
				break;
		}
		$ret[$row['attr_id']] = $record;
	}
	$result->closeCursor();
	return $ret;
}

function commitResetAttrValue ($object_id = 0, $attr_id = 0)
{
	if ($object_id <= 0 or $attr_id <= 0)
	{
		showError ('Invalid arguments', __FUNCTION__);
		die;
	}
	global $dbxlink;
	$query = "delete from AttributeValue where object_id = ${object_id} and attr_id = ${attr_id} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		die;
	}
	return TRUE;
}

// FIXME: don't share common code with use commitResetAttrValue()
function commitUpdateAttrValue ($object_id = 0, $attr_id = 0, $value = '')
{
	if ($object_id <= 0 or $attr_id <= 0)
	{
		showError ('Invalid arguments', __FUNCTION__);
		die;
	}
	if (empty ($value))
		return commitResetAttrValue ($object_id, $attr_id);
	global $dbxlink;
	$query1 = "select attr_type from Attribute where attr_id = ${attr_id}";
	$result = $dbxlink->query ($query1);
	if ($result == NULL)
	{
		showError ('SQL query #1 failed', __FUNCTION__);
		die;
	}
	$row = $result->fetch (PDO::FETCH_NUM);
	if ($row == NULL)
	{
		showError ('SQL query #1 returned no results', __FUNCTION__);
		die;
	}
	$attr_type = $row[0];
	$result->closeCursor();
	switch ($attr_type)
	{
		case 'uint':
		case 'float':
		case 'string':
			$column = $attr_type . '_value';
			break;
		case 'dict':
			$column = 'uint_value';
			break;
		default:
			showError ("Unknown attribute type '${attr_type}' met", __FUNCTION__);
			die;
	}
	$query2 =
		"delete from AttributeValue where " .
		"object_id = ${object_id} and attr_id = ${attr_id} limit 1";
	$result = $dbxlink->query ($query2);
	if ($result == NULL)
	{
		showError ('SQL query #2 failed', __FUNCTION__);
		die;
	}
	// We know $value isn't empty here.
	$query3 =
		"insert into AttributeValue set ${column} = '${value}', " .
		"object_id = ${object_id}, attr_id = ${attr_id} ";
	$result = $dbxlink->query ($query3);
	if ($result == NULL)
	{
		showError ('SQL query #3 failed', __FUNCTION__);
		die;
	}
	return TRUE;
}

function commitUseupPort ($port_id = 0)
{
	if ($port_id <= 0)
	{
		showError ("Invalid argument", __FUNCTION__);
		die;
	}
	global $dbxlink;
	$query = "update Port set reservation_comment = NULL where id = ${port_id} limit 1";
	$result = $dbxlink->exec ($query);
	if ($result == NULL)
	{
		showError ("SQL query failed", __FUNCTION__);
		die;
	}
	return TRUE;
	
}

// This is a swiss-knife blade to insert a record into a table.
// The first argument is table name.
// The second argument is an array of "name" => "value" pairs.
// The function returns either TRUE or FALSE (we expect one row
// to be inserted).
function useInsertBlade ($tablename, $values)
{
	global $dbxlink;
	$namelist = $valuelist = '';
	foreach ($values as $name => $value)
	{
		$namelist = $namelist . ($namelist == '' ? "(${name}" : ", ${name}");
		$valuelist = $valuelist . ($valuelist == '' ? "(${value}" : ", ${value}");
	}
	$query = "insert into ${tablename} ${namelist}) values ${valuelist})";
	$result = $dbxlink->exec ($query);
	if ($result != 1)
		return FALSE;
	return TRUE;
}

// This swiss-knife blade deletes one record from the specified table
// using the specified key name and value.
function useDeleteBlade ($tablename, $keyname, $keyvalue, $quotekey = TRUE)
{
	global $dbxlink;
	if ($quotekey == TRUE)
		$query = "delete from ${tablename} where ${keyname}='$keyvalue' limit 1";
	else
		$query = "delete from ${tablename} where ${keyname}=$keyvalue limit 1";
	$result = $dbxlink->exec ($query);
	if ($result === NULL)
		return FALSE;
	elseif ($result != 1)
		return FALSE;
	else
		return TRUE;
}

function loadConfigCache ()
{
	global $dbxlink;
	$query = 'SELECT varname, varvalue, vartype, is_hidden, emptyok, description FROM Config ORDER BY varname';
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		$errorInfo = $dbxlink->errorInfo();
		showError ("SQL query '${query}'\nwith message '${errorInfo[2]}'\nfailed", __FUNCTION__);
		return NULL;
	}
	$cache = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$cache[$row['varname']] = $row;
	$result->closeCursor();
	return $cache;
}

// setConfigVar() is expected to perform all necessary filtering
function storeConfigVar ($varname = NULL, $varvalue = NULL)
{
	global $dbxlink;
	if (empty ($varname) || $varvalue === NULL)
	{
		showError ('Invalid arguments', __FUNCTION__);
		return FALSE;
	}
	$query = "update Config set varvalue='${varvalue}' where varname='${varname}' limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query '${query}' failed", __FUNCTION__);
		return FALSE;
	}
	$rc = $result->rowCount();
	$result->closeCursor();
	if ($rc == 0 or $rc == 1)
		return TRUE;
	showError ("Something went wrong for args '${varname}', '${varvalue}'", __FUNCTION__);
	return FALSE;
}

// Database version detector. Should behave corretly on any
// working dataset a user might have.
function getDatabaseVersion ()
{
	global $dbxlink;
	$query = "select varvalue from Config where varname = 'DB_VERSION' and vartype = 'string'";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		$errorInfo = $dbxlink->errorInfo();
		if ($errorInfo[0] == '42S02') // ER_NO_SUCH_TABLE
			return '0.14.4';
		die (__FUNCTION__ . ': SQL query #1 failed with error ' . $errorInfo[2]);
	}
	$rows = $result->fetchAll (PDO::FETCH_NUM);
	if (count ($rows) != 1 || empty ($rows[0][0]))
	{
		$result->closeCursor();
		die (__FUNCTION__ . ': Cannot guess database version. Config table is present, but DB_VERSION is missing or invalid. Giving up.');
	}
	$ret = $rows[0][0];
	$result->closeCursor();
	return $ret;
}

// Return an array of virtual services. For each of them list real server pools
// with their load balancers and other stats.
function getSLBSummary ()
{
	global $dbxlink;
	$query = 'select vs.id as vsid, inet_ntoa(vip) as vip, vport, proto, vs.name, object_id, ' .
		'lb.rspool_id, pool.name as pool_name, count(rs.id) as rscount ' .
		'from IPVirtualService as vs inner join IPLoadBalancer as lb on vs.id = lb.vs_id ' .
		'inner join IPRSPool as pool on lb.rspool_id = pool.id ' .
		'left join IPRealServer as rs on rs.rspool_id = lb.rspool_id ' .
		'group by vs.id, object_id order by vs.vip, object_id';
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		$errorInfo = $dbxlink->errorInfo();
		showError ("SQL query '${query}' failed with message '${errorInfo[2]}'", __FUNCTION__);
		return NULL;
	}
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$vsid = $row['vsid'];
		$object_id = $row['object_id'];
		if (!isset ($ret[$vsid]))
		{
			$ret[$vsid] = array();
			foreach (array ('vip', 'vport', 'proto', 'name') as $cname)
				$ret[$vsid][$cname] = $row[$cname];
			$ret[$vsid]['lblist'] = array();
		}
		// There's only one assigned RS pool possible for each LB-VS combination.
		$ret[$vsid]['lblist'][$row['object_id']] = array
		(
			'id' => $row['rspool_id'],
			'size' => $row['rscount'],
			'name' => $row['pool_name']
		);
	}
	$result->closeCursor();
	return $ret;
}

// Get the detailed composition of a particular virtual service, namely the list
// of all pools, each shown with the list of objects servicing it. VS/RS configs
// will be returned as well.
function getVServiceInfo ($vsid = 0)
{
	global $dbxlink;
	$query1 = "select inet_ntoa(vip) as vip, vport, proto, name, vsconfig, rsconfig " .
		"from IPVirtualService where id = ${vsid}";
	$result1 = $dbxlink->query ($query1);
	if ($result1 == NULL)
	{
		showError ('SQL query #1 failed', __FUNCTION__);
		return NULL;
	}
	$vsinfo = array ();
	$row = $result1->fetch (PDO::FETCH_ASSOC);
	if (!$row)
		return NULL;
	foreach (array ('vip', 'vport', 'proto', 'name', 'vsconfig', 'rsconfig') as $cname)
		$vsinfo[$cname] = $row[$cname];
	$vsinfo['rspool'] = array();
	$result1->closeCursor();
	$query2 = "select pool.id, name, pool.vsconfig, pool.rsconfig, object_id, " .
		"lb.vsconfig as lb_vsconfig, lb.rsconfig as lb_rsconfig from " .
		"IPRSPool as pool left join IPLoadBalancer as lb on pool.id = lb.rspool_id " .
		"where vs_id = ${vsid} order by pool.name, object_id";
	$result2 = $dbxlink->query ($query2);
	if ($result2 == NULL)
	{
		showError ('SQL query #2 failed', __FUNCTION__);
		return NULL;
	}
	while ($row = $result2->fetch (PDO::FETCH_ASSOC))
	{
		if (!isset ($vsinfo['rspool'][$row['id']]))
		{
			$vsinfo['rspool'][$row['id']]['name'] = $row['name'];
			$vsinfo['rspool'][$row['id']]['vsconfig'] = $row['vsconfig'];
			$vsinfo['rspool'][$row['id']]['rsconfig'] = $row['rsconfig'];
			$vsinfo['rspool'][$row['id']]['lblist'] = array();
		}
		if ($row['object_id'] == NULL)
			continue;
		$vsinfo['rspool'][$row['id']]['lblist'][$row['object_id']] = array
		(
			'vsconfig' => $row['lb_vsconfig'],
			'rsconfig' => $row['lb_rsconfig']
		);
	}
	$result2->closeCursor();
	return $vsinfo;
}

// Collect and return the following info about the given real server pool:
// basic information
// parent virtual service information
// load balancers list (each with a list of VSes)
// real servers list

function getRSPoolInfo ($id = 0)
{
	global $dbxlink;
	$query1 = "select id, name, vsconfig, rsconfig from " .
		"IPRSPool where id = ${id}";
	$result1 = $dbxlink->query ($query1);
	if ($result1 == NULL)
	{
		showError ('SQL query #1 failed', __FUNCTION__);
		return NULL;
	}
	$ret = array();
	$row = $result1->fetch (PDO::FETCH_ASSOC);
	if (!$row)
		return NULL;
	foreach (array ('id', 'name', 'vsconfig', 'rsconfig') as $c)
		$ret[$c] = $row[$c];
	$result1->closeCursor();
	$ret['lblist'] = array();
	$ret['rslist'] = array();
	$query2 = "select object_id, vs_id, vsconfig, rsconfig from IPLoadBalancer where rspool_id = ${id} order by object_id";
	$result2 = $dbxlink->query ($query2);
	if ($result2 == NULL)
	{
		showError ('SQL query #2 failed', __FUNCTION__);
		return NULL;
	}
	while ($row = $result2->fetch (PDO::FETCH_ASSOC))
		foreach (array ('vsconfig', 'rsconfig') as $c)
			$ret['lblist'][$row['object_id']][$row['vs_id']][$c] = $row[$c];
	$result2->closeCursor();
	$query3 = "select id, inservice, inet_ntoa(rsip) as rsip, rsport, rsconfig from " .
		"IPRealServer where rspool_id = ${id} order by IPRealServer.rsip, rsport";
	$result3 = $dbxlink->query ($query3);
	if ($result3 == NULL)
	{
		showError ('SQL query #3 failed', __FUNCTION__);
		return NULL;
	}
	while ($row = $result3->fetch (PDO::FETCH_ASSOC))
		foreach (array ('inservice', 'rsip', 'rsport', 'rsconfig') as $c)
			$ret['rslist'][$row['id']][$c] = $row[$c];
	$result3->closeCursor();
	return $ret;
}

function addRStoRSPool ($pool_id = 0, $rsip = '', $rsport = 0, $inservice = 'no', $rsconfig = '')
{
	if ($pool_id <= 0 or $rsport <= 0)
	{
		showError ('Invalid arguments', __FUNCTION__);
		die;
	}
	return useInsertBlade
	(
		'IPRealServer',
		array
		(
			'rsip' => "inet_aton('${rsip}')",
			'rsport' => $rsport,
			'rspool_id' => $pool_id,
			'inservice' => ($inservice == 'yes' ? "'yes'" : "'no'"),
			'rsconfig' => (empty ($rsconfig) ? 'NULL' : "'${rsconfig}'")
		)
	);
}

function commitCreateVS ($vip = '', $vport = 0, $proto = '', $name = '', $vsconfig, $rsconfig)
{
	if (empty ($vip) or $vport <= 0 or empty ($proto))
	{
		showError ('Invalid arguments', __FUNCTION__);
		die;
	}
	return useInsertBlade
	(
		'IPVirtualService',
		array
		(
			'vip' => "inet_aton('${vip}')",
			'vport' => $vport,
			'proto' => "'${proto}'",
			'name' => (empty ($name) ? 'NULL' : "'${name}'"),
			'vsconfig' => (empty ($vsconfig) ? 'NULL' : "'${vsconfig}'"),
			'rsconfig' => (empty ($rsconfig) ? 'NULL' : "'${rsconfig}'")
		)
	);
}

function addLBtoRSPool ($pool_id = 0, $object_id = 0, $vs_id = 0, $vsconfig = '', $rsconfig = '')
{
	if ($pool_id <= 0 or $object_id <= 0 or $vs_id <= 0)
	{
		showError ('Invalid arguments', __FUNCTION__);
		die;
	}
	return useInsertBlade
	(
		'IPLoadBalancer',
		array
		(
			'object_id' => $object_id,
			'rspool_id' => $pool_id,
			'vs_id' => $vs_id,
			'vsconfig' => (empty ($vsconfig) ? 'NULL' : "'${vsconfig}'"),
			'rsconfig' => (empty ($rsconfig) ? 'NULL' : "'${rsconfig}'")
		)
	);
}

function commitDeleteRS ($id = 0)
{
	if ($id <= 0)
		return FALSE;
	return useDeleteBlade ('IPRealServer', 'id', $id);
}

function commitDeleteVS ($id = 0)
{
	if ($id <= 0)
		return FALSE;
	return useDeleteBlade ('IPVirtualService', 'id', $id);
}

function commitDeleteLB ($object_id = 0, $pool_id = 0, $vs_id = 0)
{
	global $dbxlink;
	if ($object_id <= 0 or $pool_id <= 0 or $vs_id <= 0)
		return FALSE;
	$query = "delete from IPLoadBalancer where object_id = ${object_id} and " .
		"rspool_id = ${pool_id} and vs_id = ${vs_id} limit 1";
	$result = $dbxlink->exec ($query);
	if ($result === NULL)
		return FALSE;
	elseif ($result != 1)
		return FALSE;
	else
		return TRUE;
}

function commitUpdateRS ($rsid = 0, $rsip = '', $rsport = 0, $rsconfig = '')
{
	if ($rsid <= 0 or $rsport <= 0)
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	if (long2ip (ip2long ($rsip)) !== $rsip)
	{
		showError ("Invalid IP address '${rsip}'", __FUNCTION__);
		die;
	}
	global $dbxlink;
	$query =
		"update IPRealServer set rsip = inet_aton('${rsip}'), rsport = ${rsport}, rsconfig = " .
		(empty ($rsconfig) ? 'NULL' : "'${rsconfig}'") .
		" where id = ${rsid} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query '${query}' failed", __FUNCTION__);
		die;
	}
	return TRUE;
}

function commitUpdateLB ($object_id = 0, $pool_id = 0, $vs_id = 0, $vsconfig = '', $rsconfig = '')
{
	if ($object_id <= 0 or $pool_id <= 0 or $vs_id <= 0)
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	global $dbxlink;
	$query =
		"update IPLoadBalancer set vsconfig = " .
		(empty ($vsconfig) ? 'NULL' : "'${vsconfig}'") .
		', rsconfig = ' .
		(empty ($rsconfig) ? 'NULL' : "'${rsconfig}'") .
		" where object_id = ${object_id} and rspool_id = ${pool_id} " .
		"and vs_id = ${vs_id} limit 1";
	$result = $dbxlink->exec ($query);
	if ($result === NULL)
		return FALSE;
	else
		return TRUE;
}

function commitUpdateVS ($vsid = 0, $vip = '', $vport = 0, $proto = '', $name = '', $vsconfig = '', $rsconfig = '')
{
	if ($vsid <= 0 or empty ($vip) or $vport <= 0 or empty ($proto))
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	global $dbxlink;
	$query = "update IPVirtualService set " .
		"vip = inet_aton('${vip}'), " .
		"vport = ${vport}, " .
		"proto = '${proto}', " .
		'name = ' . (empty ($name) ? 'NULL,' : "'${name}', ") .
		'vsconfig = ' . (empty ($vsconfig) ? 'NULL,' : "'${vsconfig}', ") .
		'rsconfig = ' . (empty ($rsconfig) ? 'NULL' : "'${rsconfig}'") .
		" where id = ${vsid} limit 1";
	$result = $dbxlink->exec ($query);
	if ($result === NULL)
		return FALSE;
	else
		return TRUE;
}

// Return the list of virtual services, indexed by vs_id.
// Each record will be shown with its basic info plus RS pools counter.
function getVSList ()
{
	global $dbxlink;
	$query = "select vs.id, inet_ntoa(vip) as vip, vport, proto, vs.name, vs.vsconfig, vs.rsconfig, count(rspool_id) as poolcount " .
		"from IPVirtualService as vs left join IPLoadBalancer as lb on vs.id = lb.vs_id " .
		"group by vs.id order by vs.vip, proto, vport";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach (array ('vip', 'vport', 'proto', 'name', 'vsconfig', 'rsconfig', 'poolcount') as $cname)
			$ret[$row['id']][$cname] = $row[$cname];
	$result->closeCursor();
	return $ret;
}

// Return the list of RS pool, indexed by pool id.
function getRSPoolList ()
{
	global $dbxlink;
	$query = "select pool.id, pool.name, count(rspool_id) as refcnt, pool.vsconfig, pool.rsconfig " .
		"from IPRSPool as pool left join IPLoadBalancer as lb on pool.id = lb.rspool_id " .
		"group by pool.id order by pool.id, name";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach (array ('name', 'refcnt', 'vsconfig', 'rsconfig') as $cname)
			$ret[$row['id']][$cname] = $row[$cname];
	$result->closeCursor();
	return $ret;
}

function loadThumbCache ($rack_id = 0)
{
	global $dbxlink;
	$ret = NULL;
	$query = "select thumb_data from Rack where id = ${rack_id} and thumb_data is not null limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	$row = $result->fetch (PDO::FETCH_ASSOC);
	if ($row)
		$ret = base64_decode ($row['thumb_data']);
	$result->closeCursor();
	return $ret;
}

function saveThumbCache ($rack_id = 0, $cache = NULL)
{
	global $dbxlink;
	if ($rack_id == 0 or $cache == NULL)
	{
		showError ('Invalid arguments', __FUNCTION__);
		return;
	}
	$data = base64_encode ($cache);
	$query = "update Rack set thumb_data = '${data}' where id = ${rack_id} limit 1";
	$result = $dbxlink->exec ($query);
}

function resetThumbCache ($rack_id = 0)
{
	global $dbxlink;
	if ($rack_id == 0)
	{
		showError ('Invalid argument', __FUNCTION__);
		return;
	}
	$query = "update Rack set thumb_data = NULL where id = ${rack_id} limit 1";
	$result = $dbxlink->exec ($query);
}

// Return the list of attached RS pools for the given object. As long as we have
// the LB-VS UNIQUE in IPLoadBalancer table, it is Ok to key returned records
// by vs_id, because there will be only one RS pool listed for each VS of the
// current object.
function getRSPoolsForObject ($object_id = 0)
{
	if ($object_id <= 0)
	{
		showError ('Invalid object_id', __FUNCTION__);
		return NULL;
	}
	global $dbxlink;
	$query = 'select vs_id, inet_ntoa(vip) as vip, vport, proto, vs.name, pool.id as pool_id, ' .
		'pool.name as pool_name, count(rsip) as rscount, lb.vsconfig, lb.rsconfig from ' .
		'IPLoadBalancer as lb inner join IPRSPool as pool on lb.rspool_id = pool.id ' .
		'inner join IPVirtualService as vs on lb.vs_id = vs.id ' .
		'left join IPRealServer as rs on lb.rspool_id = rs.rspool_id ' .
		"where lb.object_id = ${object_id} " .
		'group by lb.rspool_id, lb.vs_id order by vs.vip, vport, proto, pool.name';
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach (array ('vip', 'vport', 'proto', 'name', 'pool_id', 'pool_name', 'rscount', 'vsconfig', 'rsconfig') as $cname)
			$ret[$row['vs_id']][$cname] = $row[$cname];
	$result->closeCursor();
	return $ret;
}

function commitCreateRSPool ($name = '', $vsconfig = '', $rsconfig = '')
{
	return useInsertBlade
	(
		'IPRSPool',
		array
		(
			'name' => (empty ($name) ? 'NULL' : "'${name}'"),
			'vsconfig' => (empty ($vsconfig) ? 'NULL' : "'${vsconfig}'"),
			'rsconfig' => (empty ($rsconfig) ? 'NULL' : "'${rsconfig}'")
		)
	);
}

function commitDeleteRSPool ($pool_id = 0)
{
	global $dbxlink;
	if ($pool_id <= 0)
		return FALSE;
	$query = "delete from IPRSPool where id = ${pool_id} limit 1";
	$result = $dbxlink->exec ($query);
	if ($result === NULL)
		return FALSE;
	elseif ($result != 1)
		return FALSE;
	else
		return TRUE;
}

function commitUpdateRSPool ($pool_id = 0, $name = '', $vsconfig = '', $rsconfig = '')
{
	if ($pool_id <= 0)
	{
		showError ('Invalid arg', __FUNCTION__);
		die;
	}
	global $dbxlink;
	$query = "update IPRSPool set " .
		'name = ' . (empty ($name) ? 'NULL,' : "'${name}', ") .
		'vsconfig = ' . (empty ($vsconfig) ? 'NULL,' : "'${vsconfig}', ") .
		'rsconfig = ' . (empty ($rsconfig) ? 'NULL' : "'${rsconfig}'") .
		" where id = ${pool_id} limit 1";
	$result = $dbxlink->exec ($query);
	if ($result === NULL)
		return FALSE;
	elseif ($result != 1)
		return FALSE;
	else
		return TRUE;
}

function getRSList ()
{
	global $dbxlink;
	$query = "select id, inservice, inet_ntoa(rsip) as rsip, rsport, rspool_id, rsconfig " .
		"from IPRealServer order by rspool_id, IPRealServer.rsip, rsport";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach (array ('inservice', 'rsip', 'rsport', 'rspool_id', 'rsconfig') as $cname)
			$ret[$row['id']][$cname] = $row[$cname];
	$result->closeCursor();
	return $ret;
}

// Return the list of all currently configured load balancers with their pool count.
function getLBList ()
{
	global $dbxlink;
	$query = "select object_id, count(rspool_id) as poolcount " .
		"from IPLoadBalancer group by object_id order by object_id";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['object_id']] = $row['poolcount'];
	$result->closeCursor();
	return $ret;
}

// For the given object return: it vsconfig/rsconfig; the list of RS pools
// attached (each with vsconfig/rsconfig in turn), each with the list of
// virtual services terminating the pool. Each pool also lists all real
// servers with rsconfig.
function buildLBConfig ($object_id)
{
	if ($object_id <= 0)
	{
		showError ('Invalid arg', __FUNCTION__);
		return NULL;
	}
	global $dbxlink;
	$ret = array();
	$query = 'select vs_id, inet_ntoa(vip) as vip, vport, proto, vs.name as vs_name, ' .
		'vs.vsconfig as vs_vsconfig, vs.rsconfig as vs_rsconfig, ' .
		'lb.vsconfig as lb_vsconfig, lb.rsconfig as lb_rsconfig, pool.id as pool_id, pool.name as pool_name, ' .
		'pool.vsconfig as pool_vsconfig, pool.rsconfig as pool_rsconfig, ' .
		'rs.id as rs_id, inet_ntoa(rsip) as rsip, rsport, rs.rsconfig as rs_rsconfig from ' .
		'IPLoadBalancer as lb inner join IPRSPool as pool on lb.rspool_id = pool.id ' .
		'inner join IPVirtualService as vs on lb.vs_id = vs.id ' .
		'inner join IPRealServer as rs on lb.rspool_id = rs.rspool_id ' .
		"where lb.object_id = ${object_id} and rs.inservice = 'yes' " .
		"order by vs.vip, vport, proto, pool.name, rs.rsip, rs.rsport";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
		return NULL;
	}
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$vs_id = $row['vs_id'];
		if (!isset ($ret[$vs_id]))
		{
			foreach (array ('vip', 'vport', 'proto', 'vs_name', 'vs_vsconfig', 'vs_rsconfig', 'lb_vsconfig', 'lb_rsconfig', 'pool_vsconfig', 'pool_rsconfig', 'pool_id', 'pool_name') as $c)
				$ret[$vs_id][$c] = $row[$c];
			$ret[$vs_id]['rslist'] = array();
		}
		foreach (array ('rsip', 'rsport', 'rs_rsconfig') as $c)
			$ret[$vs_id]['rslist'][$row['rs_id']][$c] = $row[$c];
	}
	$result->closeCursor();
	return $ret;
}

function commitSetInService ($rs_id = 0, $inservice = '')
{
	if ($rs_id <= 0 or empty ($inservice))
	{
		showError ('Invalid args', __FUNCTION__);
		return NULL;
	}
	global $dbxlink;
	$query = "update IPRealServer set inservice = '${inservice}' where id = ${rs_id} limit 1";
	$result = $dbxlink->exec ($query);
	if ($result === NULL)
		return FALSE;
	elseif ($result != 1)
		return FALSE;
	else
		return TRUE;
}

function executeAutoPorts ($object_id = 0, $type_id = 0)
{
	if ($object_id == 0 or $type_id == 0)
	{
		showError ('Invalid arguments', __FUNCTION__);
		die;
	}
	$ret = TRUE;
	foreach (getAutoPorts ($type_id) as $autoport)
		$ret = $ret and '' == commitAddPort ($object_id, $autoport['name'], $autoport['type'], '', '');
	return $ret;
}

?>
