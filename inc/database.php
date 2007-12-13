<?php
/*
*
*  This file is a library of database access functions for RackTables.
*
*/

function escapeString ($value)
{
	global $dbxlink;
	return substr ($dbxlink->quote (htmlentities ($value)), 1, -1);
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
		showError ('SQL query failed in getRackRowInfo()');
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
		showError ('SQL query failed in getObjectList()');
		return;
	}
	$ret = array();
	$clist = array ('id', 'name', 'label', 'barcode', 'objtype_name', 'objtype_id', 'asset_no', 'rack_id', 'Rack_name');
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		foreach ($clist as $dummy => $cname)
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
		"select Rack.id, Rack.name, height, Rack.comment, row_id, dict_value as row_name " .
		"from Rack left join Dictionary on row_id = dict_key natural join Chapter " .
		"where chapter_name = 'RackRow' and Rack.deleted = 'no' " .
		(($row_id == 0) ? "" : "and row_id = ${row_id} ") .
		"order by row_name, Rack.id";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed in getRacksForRow()');
		return;
	}
	$ret = array();
	$clist = array ('id', 'name', 'height', 'comment', 'row_id', 'row_name');
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach ($clist as $dummy => $cname)
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
			showError ('Invalid rack_id in getRackData()');
		return NULL;
	}
	global $dbxlink;
	$query =
		"select Rack.id, Rack.name, row_id, height, Rack.comment, dict_value as row_name from " .
		"Rack left join Dictionary on Rack.row_id = dict_key natural join Chapter " .
		"where chapter_name = 'RackRow' and Rack.id='${rack_id}' and Rack.deleted = 'no' limit 1";
	$result1 = $dbxlink->query ($query);
	if ($result1 == NULL)
	{
		if ($silent == FALSE)
			showError ("SQL query #1 failed in getRackData()");
		return NULL;
	}
	if (($row = $result1->fetch (PDO::FETCH_ASSOC)) == NULL)
	{
		if ($silent == FALSE)
			showError ('Query #1 succeded, but returned no data in getRackData()');
		return NULL;
	}

	// load metadata
	$rack['id'] = $row['id'];
	$rack['name'] = $row['name'];
	$rack['height'] = $row['height'];
	$rack['comment'] = $row['comment'];
	$rack['row_id'] = $row['row_id'];
	$rack['row_name'] = $row['row_name'];
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
			showError ('SQL query failure #2 in getRackData()');
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
		showError ('Invalid object_id in getObjectInfo()');
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
		showError ("SQL query failed in getObjectInfo (${object_id}) with error ${ei[1]} (${ei[2]})");
		return NULL;
	}
	if (($row = $result->fetch (PDO::FETCH_ASSOC)) == NULL)
	{
		showError ('Query succeded, but returned no data in getObjectInfo()');
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
		showError ('Invalid object_id in getObjectPorts()');
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
		showError ('SQL query failed in commitAddRack()');
		return FALSE;
	}
	// last_insert_id() is MySQL-specific
	$query = 'select last_insert_id()';
	$result2 = $dbxlink->query ($query);
	if ($result2 == NULL)
	{
		showError ('Cannot get last ID in commitAddRack()');
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
		showError ("SQL query '${query}' failed in commitAddObject(): " . $errorInfo[2]);
		die;
	}
	if ($result1->rowCount() != 1)
	{
		showError ('Adding new object failed in commitAddObject()');
		return FALSE;
	}
	$query = 'select last_insert_id()';
	$result2 = $dbxlink->query ($query);
	if ($result2 == NULL)
	{
		$errorInfo = $dbxlink->errorInfo();
		showError ("SQL query '${query}' failed in commitAddObject(): " . $errorInfo[2]);
		die;
	}
	// we always have a row
	$row = $result2->fetch (PDO::FETCH_NUM);
	$last_insert_id = $row[0];
	$result2->closeCursor();
	return recordHistory ('RackObject', "id = ${last_insert_id}");
}

function commitUpdateObject ($object_id = 0, $new_name = '', $new_label = '', $new_barcode = '', $new_type_id = 0, $new_has_problems = 'no', $new_asset_no = '', $new_comment = '')
{
	if ($object_id == 0 || $new_type_id == 0)
	{
		showError ('Not all required args to commitUpdateObject() are present.');
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
		showError ("SQL query '${query}' failed in commitUpdateObject");
		return FALSE;
	}
	if ($result->rowCount() != 1)
	{
		showError ('Error updating object information in commitUpdateObject()');
		return FALSE;
	}
	$result->closeCursor();
	return recordHistory ('RackObject', "id = ${object_id}");
}

function commitUpdateRack ($rack_id, $new_name, $new_height, $new_row_id, $new_comment)
{
	if (empty ($rack_id) || empty ($new_name) || empty ($new_height))
	{
		showError ('Not all required args to commitUpdateRack() are present.');
		return FALSE;
	}
	global $dbxlink;
	$query = "update Rack set name='${new_name}', height='${new_height}', comment='${new_comment}', row_id=${new_row_id} " .
		"where id='${rack_id}' limit 1";
	$result1 = $dbxlink->query ($query);
	if ($result1->rowCount() != 1)
	{
		showError ('Error updating rack information in commitUpdateRack()');
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
				return array ('code' => 500, 'message' => "${rack_name}: SQL DELETE query failed in processGridForm()");
			if ($newstate != 'F')
			{
				$query =
					"insert into RackSpace(rack_id, unit_no, atom, state) " .
					"values(${rack_id}, ${unit_no}, '${atom}', '${newstate}') ";
				$r = $dbxlink->query ($query);
				if ($r == NULL)
					return array ('code' => 500, 'message' => "${rack_name}: SQL INSERT query failed in processGridForm()");
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
		return array ('code' => 200, 'message' => "${rack_name}: All changes were successfully saved.");
	else
		return array ('code' => 300, 'message' => "${rack_name}: No changes.");
}

// This function builds a list of rack-unit-atom records, which are assigned to
// the requested object.
function getMoleculeForObject ($object_id = 0)
{
	if ($object_id == 0)
	{
		showError ("object_id == 0 in getMoleculeForObject()");
		return NULL;
	}
	global $dbxlink;
	$query =
		"select rack_id, unit_no, atom from RackSpace " .
		"where state = 'T' and object_id = ${object_id} order by rack_id, unit_no, atom";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query failed in getMoleculeForObject()");
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
		showError ("mid == 0 in getMolecule()");
		return NULL;
	}
	global $dbxlink;
	$query =
		"select rack_id, unit_no, atom from Atom " .
		"where molecule_id=${mid}";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query failed in getMolecule()");
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
		showError ('Error inserting into Molecule in createMolecule()');
		return NULL;
	}
	$query = 'select last_insert_id()';
	$result2 = $dbxlink->query ($query);
	if ($result2 == NULL)
	{
		showError ('Cannot get last ID in createMolecule().');
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
			showError ('Error inserting into Atom in createMolecule()');
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
		showError ("SQL query failed in recordHistory() for table ${tableName}");
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
		showError ('SQL query failed in getRackspaceHistory()');
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
		showError ("Missing argument to getOperationMolecules()");
		return;
	}
	global $dbxlink;
	$query = "select old_molecule_id, new_molecule_id from MountOperation where id = ${op_id}";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query failed in getOperationMolecules()");
		return;
	}
	// We expect one row.
	$row = $result->fetch (PDO::FETCH_ASSOC);
	if ($row == NULL)
	{
		showError ("SQL query succeded, but returned no results in getOperationMolecules().");
		return;
	}
	$omid = $row['old_molecule_id'];
	$nmid = $row['new_molecule_id'];
	$result->closeCursor();
	return array ($omid, $nmid);
}

function getResidentRacksData ($object_id = 0)
{
	if ($object_id <= 0)
	{
		showError ('Invalid object_id in getResidentRacksData()');
		return;
	}
	$query = "select distinct rack_id from RackSpace where object_id = ${object_id} order by rack_id";
	global $dbxlink;
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query failed in getResidentRacksData()");
		return;
	}
	$rows = $result->fetchAll (PDO::FETCH_NUM);
	$result->closeCursor();
	$ret = array();
	foreach ($rows as $row)
	{
		$rackData = getRackData ($row[0]);
		if ($rackData == NULL)
		{
			showError ('getRackData() failed in getResidentRacksData()');
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
		showError ('SQL query failed in getObjectGroupSummary');
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
		showError ('SQL query failure in getUnmountedObjects()');
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
		showError ('SQL query failure in getProblematicObjects()');
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
		showError ('Invalid object_id in commitAddPort()');
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
		return 'unlinkPort() failed in delObjectPort()';
	if (useDeleteBlade ('Port', 'id', $port_id) != TRUE)
		return 'useDeleteBlade() failed in delObjectPort()';
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
		showError ("SQL query failure in getObjectAddressesAndNames()");
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
		showError ("SQL query failure in getEmptyPortsOfType($type_id)");
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
		showError ('Invalid object_id in getObjectAddresses()');
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
		showError ("SQL query failed in getObjectAddresses()");
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
		return 'Invalid range ID in commitDeleteRange()';
	if (useDeleteBlade ('IPRanges', 'id', $id))
		return '';
	else
		return 'SQL query failed in commitDeleteRange';
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
		showError ('SQL query failed in getUserAccounts()');
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
		showError ('SQL query failed in getUserPermissions()');
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
		showError ('SQL query failed in objectIDbyl2address()');
		return NULL;
	}
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	$result->closeCursor();
	if (count ($rows) == 0) // No results.
		return NULL;
	if (count ($rows) == 1) // Target found.
		return $rows[0];
	showError ('More than one results found in objectIDbyl2address(). This is probably a broken unique key.');
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
		showError ('SQL query failed in getPortID()');
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
		showError ('SQL query failed in commitUpdateUserAccount()');
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
		showError ('SQL query failed in commitEnableUserAccount()');
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
		showError ('SQL query failed in commitRevokePermission()');
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
		showError ('SQL query failed in getPortCompat()');
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
		showError ('Invalid arguments to removePortCompat');
		die;
	}
	$query = "delete from PortCompat where type1 = ${type1} and type2 = ${type2} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed in removePortCompat()');
		die;
	}
	return TRUE;
}

function addPortCompat ($type1 = 0, $type2 = 0)
{
	if ($type1 <= 0 or $type2 <= 0)
	{
		showError ('Invalid arguments to addPortCompat');
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
	$query =
		"select chapter_name, Chapter.chapter_no, dict_key, dict_value, sticky from " .
		"Chapter natural left join Dictionary order by chapter_name, dict_value";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed in getDict()');
		return NULL;
	}
	$dict = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
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
			$dict[$chapter_no]['word'][$row['dict_key']] = $row['dict_value'];
	}
	$result->closeCursor();
	return $dict;
}

function getDictStats ()
{
	$stock_chapters = array (1, 2, 3, 11, 12, 13, 14, 16, 17, 18, 19, 20);
	global $dbxlink;
	$query =
		"select Chapter.chapter_no, chapter_name, count(dict_key) as wc from " .
		"Chapter natural left join Dictionary group by Chapter.chapter_no";
	$result1 = $dbxlink->query ($query);
	if ($result1 == NULL)
	{
		showError ('SQL query #1 failed in getDictStats()');
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
		showError ('SQL query #2 failed in getDictStats()');
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

function commitUpdateDictionary ($chapter_no = 0, $dict_key = 0, $dict_value = '')
{
	if ($chapter_no <= 0 or $dict_key <= 0 or empty ($dict_value))
	{
		showError ('Invalid args to commitUpdateDictionary()');
		die;
	}
	global $dbxlink;
	$query =
		"update Dictionary set dict_value = '${dict_value}' where chapter_no=${chapter_no} " .
		"and dict_key=${dict_key} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed in commitUpdateDictionary()');
		die;
	}
	return TRUE;
}

function commitSupplementDictionary ($chapter_no = 0, $dict_value = '')
{
	if ($chapter_no <= 0 or empty ($dict_value))
	{
		showError ('Invalid args to commitSupplementDictionary()');
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
		showError ('Invalid args to commitReduceDictionary()');
		die;
	}
	global $dbxlink;
	$query =
		"delete from Dictionary where chapter_no=${chapter_no} " .
		"and dict_key=${dict_key} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed in commitReduceDictionary()');
		die;
	}
	return TRUE;
}

function commitAddChapter ($chapter_name = '')
{
	if (empty ($chapter_name))
	{
		showError ('Invalid args to commitAddChapter()');
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
		showError ('Invalid args to commitUpdateChapter()');
		die;
	}
	global $dbxlink;
	$query =
		"update Chapter set chapter_name = '${chapter_name}' where chapter_no = ${chapter_no} " .
		"and sticky = 'no' limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed in commitUpdateChapter()');
		die;
	}
	return TRUE;
}

function commitDeleteChapter ($chapter_no = 0)
{
	if ($chapter_no <= 0)
	{
		showError ('Invalid args to commitDeleteChapter()');
		die;
	}
	global $dbxlink;
	$query =
		"delete from Chapter where chapter_no = ${chapter_no} and sticky = 'no' limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed in commitDeleteChapter()');
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
		showError ('invalid argument to readChapter()');
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
		showError ("SQL query '${query}'\nwith message '${errorInfo[2]}'\nfailed in readChapter('${chapter_name}')");
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
		showError ("SQL query '${query}'\nwith message '${errorInfo[2]}'\nfailed in getAttrMap()");
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
		showError ('Invalid args to commitUpdateAttribute()');
		die;
	}
	global $dbxlink;
	$query =
		"update Attribute set attr_name = '${attr_name}' " .
		"where attr_id = ${attr_id} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query '${query}' failed in commitUpdateAttribute()");
		die;
	}
	return TRUE;
}

function commitAddAttribute ($attr_name = '', $attr_type = '')
{
	if (empty ($attr_name))
	{
		showError ('Invalid args to commitAddAttribute()');
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
			showError ('Invalid args to commitAddAttribute()');
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
		showError ('Invalid args to commitDeleteAttribute()');
		die;
	}
	return useDeleteBlade ('Attribute', 'attr_id', $attr_id);
}

// FIXME: don't store garbage in chapter_no for non-dictionary types.
function commitSupplementAttrMap ($attr_id = 0, $objtype_id = 0, $chapter_no = 0)
{
	if ($attr_id <= 0 or $objtype_id <= 0 or $chapter_no <= 0)
	{
		showError ('Invalid args to commitSupplementAttrMap()');
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
		showError ('Invalid args to commitReduceAttrMap()');
		die;
	}
	global $dbxlink;
	$query =
		"delete from AttributeMap where attr_id=${attr_id} " .
		"and objtype_id=${objtype_id} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed in commitReduceAttrMap()');
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
		showError ('Invalid argument to getAttrValues()');
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
		showError ("SQL query '${query}'\nwith message '${errorInfo[2]}'\nfailed in getAttrValues()");
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
		showError ('Invalid arguments to commitResetAttrValue()');
		die;
	}
	global $dbxlink;
	$query = "delete from AttributeValue where object_id = ${object_id} and attr_id = ${attr_id} limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed in commitResetAttrValue()');
		die;
	}
	return TRUE;
}

// FIXME: don't share common code with use commitResetAttrValue()
function commitUpdateAttrValue ($object_id = 0, $attr_id = 0, $value = '')
{
	if ($object_id <= 0 or $attr_id <= 0)
	{
		showError ('Invalid arguments to commitUpdateAttrValue()');
		die;
	}
	if (empty ($value))
		return commitResetAttrValue ($object_id, $attr_id);
	global $dbxlink;
	$query1 = "select attr_type from Attribute where attr_id = ${attr_id}";
	$result = $dbxlink->query ($query1);
	if ($result == NULL)
	{
		showError ('SQL query #1 failed in commitUpdateAttrValue()');
		die;
	}
	$row = $result->fetch (PDO::FETCH_NUM);
	if ($row == NULL)
	{
		showError ('SQL query #1 returned no results in commitUpdateAttrValue()');
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
			showError ("Unknown attribute type '${attr_type}' met in commitUpdateAttrValue()");
			die;
	}
	$query2 =
		"delete from AttributeValue where " .
		"object_id = ${object_id} and attr_id = ${attr_id} limit 1";
	$result = $dbxlink->query ($query2);
	if ($result == NULL)
	{
		showError ('SQL query #2 failed in commitUpdateAttrValue()');
		die;
	}
	// We know $value isn't empty here.
	$query3 =
		"insert into AttributeValue set ${column} = '${value}', " .
		"object_id = ${object_id}, attr_id = ${attr_id} ";
	$result = $dbxlink->query ($query3);
	if ($result == NULL)
	{
		showError ('SQL query #3 failed in commitUpdateAttrValue()');
		die;
	}
	return TRUE;
}

function commitUseupPort ($port_id = 0)
{
	if ($port_id <= 0)
	{
		showError ("Invalid argument to commitUseupPort()");
		die;
	}
	global $dbxlink;
	$query = "update Port set reservation_comment = NULL where id = ${port_id} limit 1";
	$result = $dbxlink->exec ($query);
	if ($result == NULL)
	{
		showError ("SQL query failed in commitUseupPort()");
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
		showError ("SQL query '${query}'\nwith message '${errorInfo[2]}'\nfailed in loadConfigCache()");
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
		showError ('Invalid arguments to storeConfigVar()');
		return FALSE;
	}
	$query = "update Config set varvalue='${varvalue}' where varname='${varname}' limit 1";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query '${query}' failed in storeConfigVar()");
		return FALSE;
	}
	$rc = $result->rowCount();
	$result->closeCursor();
	if ($rc == 0 or $rc == 1)
		return TRUE;
	showError ("Something went wrong in storeConfigVar('${varname}', '${varvalue}')");
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
		die ('SQL query #1 failed in getDatabaseVersion() with error ' . $errorInfo[2]);
	}
	$rows = $result->fetchAll (PDO::FETCH_NUM);
	if (count ($rows) != 1 || empty ($rows[0][0]))
	{
		$result->closeCursor();
		die ('Cannot guess database version. Config table is present, but DB_VERSION is missing or invalid. Giving up.');
	}
	$ret = $rows[0][0];
	$result->closeCursor();
	return $ret;
}

?>
