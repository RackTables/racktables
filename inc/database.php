<?php
/*
*
*  This file is a library of database access functions for RackTables.
*
*/

$SQLSchema = array
(
	'object' => array
	(
		'table' => 'RackObject',
		'columns' => array
		(
			'id' => 'id',
			'name' => 'name',
			'label' => 'label',
			'barcode' => 'barcode',
			'asset_no' => 'asset_no',
			'objtype_id' => 'objtype_id',
			'rack_id' => '(select rack_id from RackSpace where object_id = id order by rack_id asc limit 1)',
			'Rack_name' => '(select name from Rack where id = rack_id)',
			'row_id' => '(select row_id from Rack where id = rack_id)',
			'Row_name' => '(select name from RackRow where id = row_id)',
			'objtype_name' => '(select dict_value from Dictionary where dict_key = objtype_id)',
			'has_problems' => 'has_problems',
			'comment' => 'comment',
			'nports' => '(SELECT COUNT(*) FROM Port WHERE object_id = RackObject.id)',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('name'),
	),
	'user' => array
	(
		'table' => 'UserAccount',
		'columns' => array
		(
			'user_id' => 'user_id',
			'user_name' => 'user_name',
			'user_password_hash' => 'user_password_hash',
			'user_realname' => 'user_realname',
		),
		'keycolumn' => 'user_id',
		'ordcolumns' => array ('user_name'),
	),
	'ipv4net' => array
	(
		'table' => 'IPv4Network',
		'columns' => array
		(
			'id' => 'id',
			'ip' => 'INET_NTOA(IPv4Network.ip)',
			'mask' => 'mask',
			'name' => 'name',
			'comment' => 'comment',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('ip', 'mask'),
	),
	'file' => array
	(
		'table' => 'File',
		'columns' => array
		(
			'id' => 'id',
			'name' => 'name',
			'type' => 'type',
			'size' => 'size',
			'ctime' => 'ctime',
			'mtime' => 'mtime',
			'atime' => 'atime',
			'comment' => 'comment',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('name'),
	),
	'ipv4vs' => array
	(
		'table' => 'IPv4VS',
		'columns' => array
		(
			'id' => 'id',
			'vip' => 'INET_NTOA(vip)',
			'vport' => 'vport',
			'proto' => 'proto',
			'name' => 'name',
			'vsconfig' => 'vsconfig',
			'rsconfig' => 'rsconfig',
			'poolcount' => '(select count(vs_id) from IPv4LB where vs_id = id)',
			'dname' => 'CONCAT_WS("/", CONCAT_WS(":", INET_NTOA(vip), vport), proto)',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('vip', 'proto', 'vport'),
	),
	'ipv4rspool' => array
	(
		'table' => 'IPv4RSPool',
		'columns' => array
		(
			'id' => 'id',
			'name' => 'name',
			'refcnt' => '(select count(rspool_id) from IPv4LB where rspool_id = id)',
			'rscount' => '(select count(rspool_id) from IPv4RS where rspool_id = IPv4RSPool.id)',
			'vsconfig' => 'vsconfig',
			'rsconfig' => 'rsconfig',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('name', 'id'),
	),
	'rack' => array
	(
		'table' => 'Rack',
		'columns' => array
		(
			'id' => 'id',
			'name' => 'name',
			'height' => 'height',
			'comment' => 'comment',
			'row_id' => 'row_id',
			'row_name' => '(select name from RackRow where RackRow.id = row_id)',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('row_id', 'name'),
		'pidcolumn' => 'row_id',
	),
);

function isInnoDBSupported ($dbh = FALSE) {
	global $dbxlink;

	// sometimes db handle isn't available globally, must be passed
	if (!$dbxlink)
		$dbxlink = $dbh;

	// create a temp table
	$dbxlink->query("CREATE TABLE `innodb_test` (`id` int) ENGINE=InnoDB");
	$row = $dbxlink->query("SHOW TABLE STATUS LIKE 'innodb_test'")->fetch(PDO::FETCH_ASSOC);
	$dbxlink->query("DROP TABLE `innodb_test`");
	if ($row['Engine'] == 'InnoDB')
		return TRUE;

	return FALSE;
}

function escapeString ($value, $do_db_escape = TRUE)
{
	$ret = htmlspecialchars ($value, ENT_QUOTES, 'UTF-8');
	if ($do_db_escape)
	{
		global $dbxlink;
		$ret = substr ($dbxlink->quote ($ret), 1, -1);
	}
	return $ret;
}

// Return detailed information about one rack row.
function getRackRowInfo ($rackrow_id)
{
	$query =
		"select RackRow.id as id, RackRow.name as name, count(Rack.id) as count, " .
		"if(isnull(sum(Rack.height)),0,sum(Rack.height)) as sum " .
		"from RackRow left join Rack on Rack.row_id = RackRow.id " .
		"where RackRow.id = ${rackrow_id} " .
		"group by RackRow.id";
	$result = useSelectBlade ($query, __FUNCTION__);
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
		return $row;
	else
		return NULL;
}

function getRackRows ()
{
	$query = "select id, name from RackRow ";
	$result = useSelectBlade ($query, __FUNCTION__);
	$rows = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$rows[$row['id']] = $row['name'];
	$result->closeCursor();
	asort ($rows);
	return $rows;
}

function commitAddRow($rackrow_name)
{
	return useInsertBlade('RackRow', array('name'=>"'$rackrow_name'"));
}

function commitUpdateRow($rackrow_id, $rackrow_name)
{
	global $dbxlink;
	$query = "update RackRow set name = '${rackrow_name}' where id=${rackrow_id}";
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ("SQL query '${query}' failed", __FUNCTION__);
		return FALSE;
	}
	$result->closeCursor();
	return TRUE;
}

function commitDeleteRow($rackrow_id)
{
	global $dbxlink;
	$query = "select count(*) from Rack where row_id=${rackrow_id}";
	$result = $dbxlink->query ($query);
	if (($result!=NULL) && ($row = $result->fetch(PDO::FETCH_NUM)) )
	{
		if ($row[0] == 0)
		{
			$query = "delete from RackRow where id=${rackrow_id}";
			$result = $dbxlink->query ($query);
			if ($result == NULL)
			{
				showError ("SQL query '${query}' failed", __FUNCTION__);
				return FALSE;
			}
		}
	}
	else
	{
		showError ("SQL query '${query}' failed", __FUNCTION__);
		return FALSE;
	}
	$result->closeCursor();
	return TRUE;
}

// This function returns id->name map for all object types. The map is used
// to build <select> input for objects.
function getObjectTypeList ()
{
	return readChapter ('RackObjectType');
}

// Return a simple object list w/o related information, so that the returned value
// can be directly used by printSelect(). An optional argument is the name of config
// option with constraint in RackCode.
function getNarrowObjectList ($varname = '')
{
	$wideList = listCells ('object');
	if (strlen ($varname) and strlen (getConfigVar ($varname)))
	{
		global $parseCache;
		if (!isset ($parseCache[$varname]))
			$parseCache[$varname] = spotPayload (getConfigVar ($varname), 'SYNT_EXPR');
		if ($parseCache[$varname]['result'] != 'ACK')
			return array();
		$wideList = filterCellList ($wideList, $parseCache[$varname]['load']);
	}
	$ret = array();
	foreach ($wideList as $cell)
		$ret[$cell['id']] = $cell['dname'];
	return $ret;
}

// For a given realm return a list of entity records, each with
// enough information for judgeCell() to execute.
function listCells ($realm, $parent_id = 0)
{
	if (!$parent_id)
	{
		global $entityCache;
		if (isset ($entityCache['complete'][$realm]))
			return $entityCache['complete'][$realm];
	}
	global $SQLSchema;
	if (!isset ($SQLSchema[$realm]))
		throw new RealmNotFoundException ($realm);
	$SQLinfo = $SQLSchema[$realm];
	$query = 'SELECT tag_id';
	foreach ($SQLinfo['columns'] as $alias => $expression)
		// Automatically prepend table name to each single column, but leave all others intact.
		$query .= ', ' . ($alias == $expression ? "${SQLinfo['table']}.${alias}" : "${expression} as ${alias}");
	$query .= " FROM ${SQLinfo['table']} LEFT JOIN TagStorage on entity_realm = '${realm}' and entity_id = ${SQLinfo['table']}.${SQLinfo['keycolumn']}";
	if (isset ($SQLinfo['pidcolumn']) and $parent_id)
		$query .= " WHERE ${SQLinfo['table']}.${SQLinfo['pidcolumn']} = ${parent_id}";
	$query .= " ORDER BY ";
	foreach ($SQLinfo['ordcolumns'] as $oc)
		$query .= "${SQLinfo['table']}.${oc}, ";
	$query .= " tag_id";
	$result = useSelectBlade ($query, __FUNCTION__);
	$ret = array();
	global $taglist;
	// Index returned result by the value of key column.
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$entity_id = $row[$SQLinfo['keycolumn']];
		// Init the first record anyway, but store tag only if there is one.
		if (!isset ($ret[$entity_id]))
		{
			$ret[$entity_id] = array ('realm' => $realm);
			foreach (array_keys ($SQLinfo['columns']) as $alias)
				$ret[$entity_id][$alias] = $row[$alias];
			$ret[$entity_id]['etags'] = array();
			if ($row['tag_id'] != NULL && isset ($taglist[$row['tag_id']]))
				$ret[$entity_id]['etags'][] = array
				(
					'id' => $row['tag_id'],
					'tag' => $taglist[$row['tag_id']]['tag'],
					'parent_id' => $taglist[$row['tag_id']]['parent_id'],
				);
		}
		elseif (isset ($taglist[$row['tag_id']]))
			// Meeting existing key later is always more tags on existing list.
			$ret[$entity_id]['etags'][] = array
			(
				'id' => $row['tag_id'],
				'tag' => $taglist[$row['tag_id']]['tag'],
				'parent_id' => $taglist[$row['tag_id']]['parent_id'],
			);
	}
	// Add necessary finish to the list before returning it. Maintain caches.
	if (!$parent_id)
		unset ($entityCache['partial'][$realm]);
	foreach (array_keys ($ret) as $entity_id)
	{
		$ret[$entity_id]['etags'] = getExplicitTagsOnly ($ret[$entity_id]['etags']);
		$ret[$entity_id]['itags'] = getImplicitTags ($ret[$entity_id]['etags']);
		$ret[$entity_id]['atags'] = generateEntityAutoTags ($ret[$entity_id]);
		switch ($realm)
		{
		case 'object':
			setDisplayedName ($ret[$entity_id]);
			break;
		case 'ipv4net':
			$ret[$entity_id]['ip_bin'] = ip2long ($ret[$entity_id]['ip']);
			$ret[$entity_id]['mask_bin'] = binMaskFromDec ($ret[$entity_id]['mask']);
			$ret[$entity_id]['mask_bin_inv'] = binInvMaskFromDec ($ret[$entity_id]['mask']);
			$ret[$entity_id]['db_first'] = sprintf ('%u', 0x00000000 + $ret[$entity_id]['ip_bin'] & $ret[$entity_id]['mask_bin']);
			$ret[$entity_id]['db_last'] = sprintf ('%u', 0x00000000 + $ret[$entity_id]['ip_bin'] | ($ret[$entity_id]['mask_bin_inv']));
			break;
		default:
			break;
		}
		if (!$parent_id)
			$entityCache['complete'][$realm][$entity_id] = $ret[$entity_id];
		else
			$entityCache['partial'][$realm][$entity_id] = $ret[$entity_id];
	}
	return $ret;
}

// Very much like listCells(), but return only one record requested (or NULL,
// if it does not exist).
function spotEntity ($realm, $id)
{
	global $entityCache;
	if (isset ($entityCache['complete'][$realm]))
	// Emphasize the absence of record, if listCells() has already been called.
		if (isset ($entityCache['complete'][$realm][$id]))
			return $entityCache['complete'][$realm][$id];
		else
			throw new EntityNotFoundException ($realm, $id);
	elseif (isset ($entityCache['partial'][$realm][$id]))
		return $entityCache['partial'][$realm][$id];
	global $SQLSchema;
	if (!isset ($SQLSchema[$realm]))
		throw new RealmNotFoundException ($realm);
	$SQLinfo = $SQLSchema[$realm];
	$query = 'SELECT tag_id';
	foreach ($SQLinfo['columns'] as $alias => $expression)
		// Automatically prepend table name to each single column, but leave all others intact.
		$query .= ', ' . ($alias == $expression ? "${SQLinfo['table']}.${alias}" : "${expression} as ${alias}");
	$query .= " FROM ${SQLinfo['table']} LEFT JOIN TagStorage on entity_realm = '${realm}' and entity_id = ${SQLinfo['table']}.${SQLinfo['keycolumn']}";
	$query .= " WHERE ${SQLinfo['table']}.${SQLinfo['keycolumn']} = ${id}";
	$query .= " ORDER BY tag_id";
	$result = useSelectBlade ($query, __FUNCTION__);
	$ret = array();
	global $taglist;
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		if (!isset ($ret['realm']))
		{
			$ret = array ('realm' => $realm);
			foreach (array_keys ($SQLinfo['columns']) as $alias)
				$ret[$alias] = $row[$alias];
			$ret['etags'] = array();
			if ($row['tag_id'] != NULL && isset ($taglist[$row['tag_id']]))
				$ret['etags'][] = array
				(
					'id' => $row['tag_id'],
					'tag' => $taglist[$row['tag_id']]['tag'],
					'parent_id' => $taglist[$row['tag_id']]['parent_id'],
				);
		}
		elseif (isset ($taglist[$row['tag_id']]))
			$ret['etags'][] = array
			(
				'id' => $row['tag_id'],
				'tag' => $taglist[$row['tag_id']]['tag'],
				'parent_id' => $taglist[$row['tag_id']]['parent_id'],
			);
	unset ($result);
	if (!isset ($ret['realm'])) // no rows were returned
		throw new EntityNotFoundException ($realm, $id);
	$ret['etags'] = getExplicitTagsOnly ($ret['etags']);
	$ret['itags'] = getImplicitTags ($ret['etags']);
	$ret['atags'] = generateEntityAutoTags ($ret);
	switch ($realm)
	{
	case 'object':
		setDisplayedName ($ret);
		break;
	case 'ipv4net':
		$ret['ip_bin'] = ip2long ($ret['ip']);
		$ret['mask_bin'] = binMaskFromDec ($ret['mask']);
		$ret['mask_bin_inv'] = binInvMaskFromDec ($ret['mask']);
		$ret['db_first'] = sprintf ('%u', 0x00000000 + $ret['ip_bin'] & $ret['mask_bin']);
		$ret['db_last'] = sprintf ('%u', 0x00000000 + $ret['ip_bin'] | ($ret['mask_bin_inv']));
		break;
	default:
		break;
	}
	$entityCache['partial'][$realm][$id] = $ret;
	return $ret;
}

// This function can be used with array_walk().
function amplifyCell (&$record, $dummy = NULL)
{
	switch ($record['realm'])
	{
	case 'object':
		$record['ports'] = getObjectPortsAndLinks ($record['id']);
		$record['ipv4'] = getObjectIPv4Allocations ($record['id']);
		$record['nat4'] = getNATv4ForObject ($record['id']);
		$record['ipv4rspools'] = getRSPoolsForObject ($record['id']);
		$record['files'] = getFilesOfEntity ($record['realm'], $record['id']);
		break;
	case 'ipv4net':
		$record['parent_id'] = getIPv4AddressNetworkId ($record['ip'], $record['mask']);
		break;
	case 'file':
		$record['links'] = getFileLinks ($record['id']);
		break;
	case 'ipv4rspool':
		$record['lblist'] = array();
		$query = "select object_id, vs_id, lb.vsconfig, lb.rsconfig from " .
			"IPv4LB as lb inner join IPv4VS as vs on lb.vs_id = vs.id " .
			"where rspool_id = ${record['id']} order by object_id, vip, vport";
		$result = useSelectBlade ($query, __FUNCTION__);
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
			$record['lblist'][$row['object_id']][$row['vs_id']] = array
			(
				'rsconfig' => $row['rsconfig'],
				'vsconfig' => $row['vsconfig'],
			);
		unset ($result);
		$record['rslist'] = array();
		$query = "select id, inservice, inet_ntoa(rsip) as rsip, rsport, rsconfig from " .
			"IPv4RS where rspool_id = ${record['id']} order by IPv4RS.rsip, rsport";
		$result = useSelectBlade ($query, __FUNCTION__);
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
			$record['rslist'][$row['id']] = array
			(
				'inservice' => $row['inservice'],
				'rsip' => $row['rsip'],
				'rsport' => $row['rsport'],
				'rsconfig' => $row['rsconfig'],
			);
		unset ($result);
		break;
	case 'ipv4vs':
		// Get the detailed composition of a particular virtual service, namely the list
		// of all pools, each shown with the list of objects servicing it. VS/RS configs
		// will be returned as well.
		$record['rspool'] = array();
		$query = "select pool.id, name, pool.vsconfig, pool.rsconfig, object_id, " .
			"lb.vsconfig as lb_vsconfig, lb.rsconfig as lb_rsconfig from " .
			"IPv4RSPool as pool left join IPv4LB as lb on pool.id = lb.rspool_id " .
			"where vs_id = ${record['id']} order by pool.name, object_id";
		$result = useSelectBlade ($query, __FUNCTION__);
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
		{
			if (!isset ($record['rspool'][$row['id']]))
				$record['rspool'][$row['id']] = array
				(
					'name' => $row['name'],
					'vsconfig' => $row['vsconfig'],
					'rsconfig' => $row['rsconfig'],
					'lblist' => array(),
				);
			if ($row['object_id'] == NULL)
				continue;
			$record['rspool'][$row['id']]['lblist'][$row['object_id']] = array
			(
				'vsconfig' => $row['lb_vsconfig'],
				'rsconfig' => $row['lb_rsconfig'],
			);
		}
		unset ($result);
		break;
	case 'rack':
		$record['mountedObjects'] = array();
		// start with default rackspace
		for ($i = $record['height']; $i > 0; $i--)
			for ($locidx = 0; $locidx < 3; $locidx++)
				$record[$i][$locidx]['state'] = 'F';
		// load difference
		$query =
			"select unit_no, atom, state, object_id " .
			"from RackSpace where rack_id = ${record['id']} and " .
			"unit_no between 1 and " . $record['height'] . " order by unit_no";
		$result = useSelectBlade ($query, __FUNCTION__);
		global $loclist;
		$mounted_objects = array();
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
		{
			$record[$row['unit_no']][$loclist[$row['atom']]]['state'] = $row['state'];
			$record[$row['unit_no']][$loclist[$row['atom']]]['object_id'] = $row['object_id'];
			if ($row['state'] == 'T' and $row['object_id'] != NULL)
				$mounted_objects[$row['object_id']] = TRUE;
		}
		$record['mountedObjects'] = array_keys ($mounted_objects);
		unset ($result);
		break;
	default:
	}
}

function getPortTypes ()
{
	return readChapter ('PortType');
}

function getObjectPortsAndLinks ($object_id)
{
	// prepare decoder
	$ptd = readChapter ('PortType');
	$query = "select id, name, label, l2address, type as type_id, reservation_comment from Port where object_id = ${object_id}";
	// list and decode all ports of the current object
	$result = useSelectBlade ($query, __FUNCTION__);
	$ret=array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$row['type'] = $ptd[$row['type_id']];
		$row['l2address'] = l2addressFromDatabase ($row['l2address']);
		$row['remote_id'] = NULL;
		$row['remote_name'] = NULL;
		$row['remote_object_id'] = NULL;
		$row['remote_object_name'] = NULL;
		$row['remote_type_id'] = NULL;
		$ret[] = $row;
	}
	unset ($result);
	// now find and decode remote ends for all locally terminated connections
	// FIXME: can't this data be extracted in one pass with sub-queries?
	foreach (array_keys ($ret) as $tmpkey)
	{
		$portid = $ret[$tmpkey]['id'];
		$remote_id = NULL;
		$query = "select porta, portb from Link where porta = {$portid} or portb = ${portid}";
		$result = useSelectBlade ($query, __FUNCTION__);
		if ($row = $result->fetch (PDO::FETCH_ASSOC))
		{
			if ($portid != $row['porta'])
				$remote_id = $row['porta'];
			elseif ($portid != $row['portb'])
				$remote_id = $row['portb'];
		}
		unset ($result);
		if ($remote_id) // there's a remote end here
		{
			$query = "select Port.name as port_name, Port.type as port_type, object_id, RackObject.name as object_name " .
				"from Port left join RackObject on Port.object_id = RackObject.id " .
				"where Port.id = ${remote_id}";
			$result = useSelectBlade ($query, __FUNCTION__);
			if ($row = $result->fetch (PDO::FETCH_ASSOC))
			{
				$ret[$tmpkey]['remote_name'] = $row['port_name'];
				$ret[$tmpkey]['remote_object_id'] = $row['object_id'];
				$ret[$tmpkey]['remote_object_name'] = $row['object_name'];
				$ret[$tmpkey]['remote_type_id'] = $row['port_type'];
			}
			$ret[$tmpkey]['remote_id'] = $remote_id;
			unset ($result);
			// only call displayedName() when necessary
			if (!strlen ($ret[$tmpkey]['remote_object_name']) and strlen ($ret[$tmpkey]['remote_object_id']))
			{
				$oi = spotEntity ('object', $ret[$tmpkey]['remote_object_id']);
				$ret[$tmpkey]['remote_object_name'] = $oi['dname'];
			}
		}
	}
	return $ret;
}

function commitAddRack ($name, $height = 0, $row_id = 0, $comment, $taglist)
{
	if ($row_id <= 0 or $height <= 0 or !strlen ($name))
		return FALSE;
	$result = useInsertBlade
	(
		'Rack',
		array
		(
			'row_id' => $row_id,
			'name' => "'${name}'",
			'height' =>  $height,
			'comment' => "'${comment}'"
		)
	);
	if ($result == NULL)
	{
		showError ('useInsertBlade() failed', __FUNCTION__);
		return FALSE;
	}
	$last_insert_id = lastInsertID();
	return (produceTagsForLastRecord ('rack', $taglist, $last_insert_id) == '') and recordHistory ('Rack', "id = ${last_insert_id}");
}

function commitAddObject ($new_name, $new_label, $new_barcode, $new_type_id, $new_asset_no, $taglist = array())
{
	global $dbxlink;
	// Maintain UNIQUE INDEX for common names and asset tags by
	// filtering out empty strings (not NULLs).
	$result1 = useInsertBlade
	(
		'RackObject',
		array
		(
			'name' => !strlen ($new_name) ? 'NULL' : "'${new_name}'",
			'label' => "'${new_label}'",
			'barcode' => !strlen ($new_barcode) ? 'NULL' : "'${new_barcode}'",
			'objtype_id' => $new_type_id,
			'asset_no' => !strlen ($new_asset_no) ? 'NULL' : "'${new_asset_no}'"
		)
	);
	if ($result1 == NULL)
	{
		showError ("SQL query #1 failed", __FUNCTION__);
		return FALSE;
	}
	$last_insert_id = lastInsertID();
	// Do AutoPorts magic
	executeAutoPorts ($last_insert_id, $new_type_id);
	// Now tags...
	$error = produceTagsForLastRecord ('object', $taglist, $last_insert_id);
	if ($error != '')
	{
		showError ("Error adding tags for the object: ${error}");
		return FALSE;
	}
	return recordHistory ('RackObject', "id = ${last_insert_id}");
}

function commitUpdateObject ($object_id = 0, $new_name = '', $new_label = '', $new_barcode = '', $new_type_id = 0, $new_has_problems = 'no', $new_asset_no = '', $new_comment = '')
{
	if ($new_type_id == 0)
		throw new InvalidArgException ('$new_type_id', $new_type_id);
	global $dbxlink;
	$new_asset_no = !strlen ($new_asset_no) ? 'NULL' : "'${new_asset_no}'";
	$new_barcode = !strlen ($new_barcode) ? 'NULL' : "'${new_barcode}'";
	$new_name = !strlen ($new_name) ? 'NULL' : "'${new_name}'";
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

// Remove file links related to the entity, but leave the entity and file(s) intact.
function releaseFiles ($entity_realm, $entity_id)
{
	global $dbxlink;
	$dbxlink->exec ("DELETE FROM FileLink WHERE entity_type = '${entity_realm}' AND entity_id = ${entity_id}");
}

// There are times when you want to delete all traces of an object
function commitDeleteObject ($object_id = 0)
{
	global $dbxlink;
	releaseFiles ('object', $object_id);
	destroyTagsForEntity ('object', $object_id);
	$dbxlink->query("DELETE FROM AttributeValue WHERE object_id = ${object_id}");
	$dbxlink->query("DELETE FROM IPv4LB WHERE object_id = ${object_id}");
	$dbxlink->query("DELETE FROM IPv4Allocation WHERE object_id = ${object_id}");
	$dbxlink->query("DELETE FROM Port WHERE object_id = ${object_id}");
	$dbxlink->query("DELETE FROM IPv4NAT WHERE object_id = ${object_id}");
	$dbxlink->query("DELETE FROM RackSpace WHERE object_id = ${object_id}");
	$dbxlink->query("DELETE FROM Atom WHERE molecule_id IN (SELECT new_molecule_id FROM MountOperation WHERE object_id = ${object_id})");
	$dbxlink->query("DELETE FROM Molecule WHERE id IN (SELECT new_molecule_id FROM MountOperation WHERE object_id = ${object_id})");
	$dbxlink->query("DELETE FROM MountOperation WHERE object_id = ${object_id}");
	$dbxlink->query("DELETE FROM RackObjectHistory WHERE id = ${object_id}");
	$dbxlink->query("DELETE FROM RackObject WHERE id = ${object_id}");

	return '';
}

function commitDeleteRack($rack_id)
{
	global $dbxlink;
	releaseFiles ('rack', $rack_id);
	destroyTagsForEntity ('rack', $rack_id);
	$query = "delete from RackSpace where rack_id = '${rack_id}'";
	$dbxlink->query ($query);
	$query = "delete from RackHistory where id = '${rack_id}'";
	$dbxlink->query ($query);
	$query = "delete from Rack where id = '${rack_id}'";
	$dbxlink->query ($query);
	return TRUE;
}

function commitUpdateRack ($rack_id, $new_name, $new_height, $new_row_id, $new_comment)
{
	if (!strlen ($rack_id) || !strlen ($new_name) || !strlen ($new_height))
	{
		showError ('Not all required args are present.', __FUNCTION__);
		return FALSE;
	}
	global $dbxlink;

	// Can't shrink a rack if rows being deleted contain mounted objects
	$check_sql = "SELECT COUNT(*) AS count FROM RackSpace WHERE rack_id = '${rack_id}' AND unit_no > '{$new_height}'";
	$check_result = $dbxlink->query($check_sql);
	$check_row = $check_result->fetch (PDO::FETCH_ASSOC);
	unset ($check_result);
	if ($check_row['count'] > 0) {
		showError ('Cannot shrink rack, objects are still mounted there', __FUNCTION__);
		return FALSE;
	}

	$update_sql = "update Rack set name='${new_name}', height='${new_height}', comment='${new_comment}', row_id=${new_row_id} " .
		"where id='${rack_id}' limit 1";
	$update_result = $dbxlink->query ($update_sql);
	if ($update_result->rowCount() != 1)
	{
		showError ('Error updating rack information', __FUNCTION__);
		return FALSE;
	}
	return recordHistory ('Rack', "id = ${rack_id}");
}

// This function accepts rack data returned by amplifyCell(), validates and applies changes
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
	$query =
		"select rack_id, unit_no, atom from RackSpace " .
		"where state = 'T' and object_id = ${object_id} order by rack_id, unit_no, atom";
	$result = useSelectBlade ($query, __FUNCTION__);
	$ret = $result->fetchAll (PDO::FETCH_ASSOC);
	$result->closeCursor();
	return $ret;
}

// This function builds a list of rack-unit-atom records for requested molecule.
function getMolecule ($mid = 0)
{
	$query =
		"select rack_id, unit_no, atom from Atom " .
		"where molecule_id=${mid}";
	$result = useSelectBlade ($query, __FUNCTION__);
	$ret = $result->fetchAll (PDO::FETCH_ASSOC);
	$result->closeCursor();
	return $ret;
}

// returns exactly what is's named after
function lastInsertID ()
{
	if (NULL == ($result = useSelectBlade ('select last_insert_id()', __FUNCTION__)))
	{
		showError ('SQL query failed!', __FUNCTION__);
		die;
	}
	$row = $result->fetch (PDO::FETCH_NUM);
	return $row[0];
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
	$molecule_id = lastInsertID();
	foreach ($molData as $rua)
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
	$query =
		"select mo.id as mo_id, ro.id as ro_id, ro.name, mo.ctime, mo.comment, dict_value as objtype_name, user_name from " .
		"MountOperation as mo inner join RackObject as ro on mo.object_id = ro.id " .
		"inner join Dictionary on objtype_id = dict_key join Chapter on Chapter.id = Dictionary.chapter_id " .
		"where Chapter.name = 'RackObjectType' order by ctime desc";
	$result = useSelectBlade ($query, __FUNCTION__);
	$ret = $result->fetchAll(PDO::FETCH_ASSOC);
	$result->closeCursor();
	return $ret;
}

// This function is used in renderRackspaceHistory()
function getOperationMolecules ($op_id = 0)
{
	$query = "select old_molecule_id, new_molecule_id from MountOperation where id = ${op_id}";
	$result = useSelectBlade ($query, __FUNCTION__);
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
	$query = "select distinct rack_id from RackSpace where object_id = ${object_id} order by rack_id";
	$result = useSelectBlade ($query, __FUNCTION__);
	$rows = $result->fetchAll (PDO::FETCH_NUM);
	unset ($result);
	$ret = array();
	foreach ($rows as $row)
	{
		if (!$fetch_rackdata)
		{
			$ret[$row[0]] = $row[0];
			continue;
		}
		if (NULL == ($rackData = spotEntity ('rack', $row[0])))
		{
			showError ('Rack not found', __FUNCTION__);
			return NULL;
		}
		amplifyCell ($rackData);
		$ret[$row[0]] = $rackData;
	}
	return $ret;
}

function commitAddPort ($object_id = 0, $port_name, $port_type_id, $port_label, $port_l2address)
{
	if (NULL === ($db_l2address = l2addressForDatabase ($port_l2address)))
		return "Invalid L2 address ${port_l2address}";
	global $dbxlink;
	$dbxlink->exec ('LOCK TABLES Port WRITE');
	if (alreadyUsedL2Address ($db_l2address, $object_id))
	{
		$dbxlink->exec ('UNLOCK TABLES');
		return "address ${db_l2address} belongs to another object";
	}
	$result = useInsertBlade
	(
		'Port',
		array
		(
			'name' => "'${port_name}'",
			'object_id' => "'${object_id}'",
			'label' => "'${port_label}'",
			'type' => "'${port_type_id}'",
			'l2address' => ($db_l2address === '') ? 'NULL' : "'${db_l2address}'"
		)
	);
	$dbxlink->exec ('UNLOCK TABLES');
	if ($result)
		return '';
	else
		return 'SQL query failed';
}

// The fifth argument may be either explicit 'NULL' or some (already quoted by the upper layer)
// string value. In case it is omitted, we just assign it its current value.
// It would be nice to simplify this semantics later.
function commitUpdatePort ($object_id, $port_id, $port_name, $port_type_id, $port_label, $port_l2address, $port_reservation_comment = 'reservation_comment')
{
	global $dbxlink;
	if (NULL === ($db_l2address = l2addressForDatabase ($port_l2address)))
		return "Invalid L2 address ${port_l2address}";
	global $dbxlink;
	$dbxlink->exec ('LOCK TABLES Port WRITE');
	if (alreadyUsedL2Address ($db_l2address, $object_id))
	{
		$dbxlink->exec ('UNLOCK TABLES');
		return "address ${db_l2address} belongs to another object";
	}
	$query =
		"update Port set name='$port_name', type=$port_type_id, label='$port_label', " .
		"reservation_comment = ${port_reservation_comment}, l2address=" .
		(($db_l2address === '') ? 'NULL' : "'${db_l2address}'") .
		" WHERE id='$port_id' and object_id=${object_id}";
	$result = $dbxlink->exec ($query);
	$dbxlink->exec ('UNLOCK TABLES');
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

function getAllIPv4Allocations ()
{
	$query =
		"select object_id as object_id, ".
		"RackObject.name as object_name, ".
		"IPv4Allocation.name as name, ".
		"INET_NTOA(ip) as ip ".
		"from IPv4Allocation join RackObject on id=object_id ";
	$result = useSelectBlade ($query, __FUNCTION__);
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
	$query =
		"select distinct Port.id as Port_id, ".
		"Port.object_id as Port_object_id, ".
		"RackObject.name as Object_name, ".
		"Port.name as Port_name, ".
		"Port.type as Port_type_id, ".
		"dict_value as Port_type_name ".
		"from ( ".
		"	( ".
		"		Port inner join Dictionary on Port.type = dict_key join Chapter on Chapter.id = Dictionary.chapter_id ".
		"	) ".
		" 	join RackObject on Port.object_id = RackObject.id ".
		") ".
		"left join Link on Port.id=Link.porta or Port.id=Link.portb ".
		"inner join PortCompat on Port.type = PortCompat.type2 ".
		"where Chapter.name = 'PortType' and PortCompat.type1 = '$type_id' and Link.porta is NULL ".
		"and Port.reservation_comment is null order by Object_name, Port_name";
	$result = useSelectBlade ($query, __FUNCTION__);
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

// Return all IPv4 addresses allocated to the objects. Attach detailed
// info about address to each alocation records. Index result by dotted-quad
// address.
function getObjectIPv4Allocations ($object_id = 0)
{
	$ret = array();
	$query = 'select name as osif, type, inet_ntoa(ip) as dottedquad from IPv4Allocation ' .
		"where object_id = ${object_id} " .
		'order by ip';
	$result = useSelectBlade ($query, __FUNCTION__);
	// don't spawn a sub-query with unfetched buffer, it may fail
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['dottedquad']] = array ('osif' => $row['osif'], 'type' => $row['type']);
	unset ($result);
	foreach (array_keys ($ret) as $dottedquad)
		$ret[$dottedquad]['addrinfo'] = getIPv4Address ($dottedquad);
	return $ret;
}

// Return minimal IPv4 address, optionally with "ip" key set, if requested.
function constructIPv4Address ($dottedquad = NULL)
{
	$ret = array
	(
		'name' => '',
		'reserved' => 'no',
		'outpf' => array(),
		'inpf' => array(),
		'rslist' => array(),
		'allocs' => array(),
		'lblist' => array()
	);
	if ($dottedquad != NULL)
		$ret['ip'] = $dottedquad;
	return $ret;
}

// Check the range requested for meaningful IPv4 records, build them
// into a list and return. Return an empty list if nothing matched.
// Both arguments are expected in signed int32 form. The resulting list
// is keyed by uint32 form of each IP address, items aren't sorted.
// LATER: accept a list of pairs and build WHERE sub-expression accordingly
function scanIPv4Space ($pairlist)
{
	$ret = array();
	if (!count ($pairlist)) // this is normal for a network completely divided into smaller parts
		return $ret;
	// FIXME: this is a copy-and-paste prototype
	$or = '';
	$whereexpr1 = '(';
	$whereexpr2 = '(';
	$whereexpr3 = '(';
	$whereexpr4 = '(';
	$whereexpr5a = '(';
	$whereexpr5b = '(';
	foreach ($pairlist as $tmp)
	{
		$db_first = sprintf ('%u', 0x00000000 + $tmp['i32_first']);
		$db_last = sprintf ('%u', 0x00000000 + $tmp['i32_last']);
		$whereexpr1 .= $or . "ip between ${db_first} and ${db_last}";
		$whereexpr2 .= $or . "ip between ${db_first} and ${db_last}";
		$whereexpr3 .= $or . "vip between ${db_first} and ${db_last}";
		$whereexpr4 .= $or . "rsip between ${db_first} and ${db_last}";
		$whereexpr5a .= $or . "remoteip between ${db_first} and ${db_last}";
		$whereexpr5b .= $or . "localip between ${db_first} and ${db_last}";
		$or = ' or ';
	}
	$whereexpr1 .= ')';
	$whereexpr2 .= ')';
	$whereexpr3 .= ')';
	$whereexpr4 .= ')';
	$whereexpr5a .= ')';
	$whereexpr5b .= ')';

	// 1. collect labels and reservations
	$query = "select INET_NTOA(ip) as ip, name, reserved from IPv4Address ".
		"where ${whereexpr1} and (reserved = 'yes' or name != '')";
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = ip2long ($row['ip']);
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPv4Address ($row['ip']);
		$ret[$ip_bin]['name'] = $row['name'];
		$ret[$ip_bin]['reserved'] = $row['reserved'];
	}
	unset ($result);

	// 2. check for allocations
	$query =
		"select INET_NTOA(ip) as ip, object_id, name, type " .
		"from IPv4Allocation where ${whereexpr2} order by type";
	$result = useSelectBlade ($query, __FUNCTION__);
	// release DBX early to avoid issues with nested spotEntity() calls
	$allRows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($allRows as $row)
	{
		$ip_bin = ip2long ($row['ip']);
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPv4Address ($row['ip']);
		$oinfo = spotEntity ('object', $row['object_id']);
		$ret[$ip_bin]['allocs'][] = array
		(
			'type' => $row['type'],
			'name' => $row['name'],
			'object_id' => $row['object_id'],
			'object_name' => $oinfo['dname'],
		);
	}

	// 3. look for virtual services and related LB 
	$query = "select vs_id, inet_ntoa(vip) as ip, vport, proto, vs.name, object_id " .
		"from IPv4VS as vs inner join IPv4LB as lb on vs.id = lb.vs_id " .
		"where ${whereexpr3} order by vport, proto, object_id";
	$result = useSelectBlade ($query, __FUNCTION__);
	$allRows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($allRows as $row)
	{
		$ip_bin = ip2long ($row['ip']);
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPv4Address ($row['ip']);
		$oinfo = spotEntity ('object', $row['object_id']);
		$ret[$ip_bin]['lblist'][] = array
		(
			'vport' => $row['vport'],
			'proto' => $row['proto'],
			'vs_id' => $row['vs_id'],
			'name' => $row['name'],
			'vip' => $row['ip'],
			'object_id' => $row['object_id'],
			'object_name' => $oinfo['dname'],
		);
	}

	// 4. don't forget about real servers along with pools
	$query = "select inet_ntoa(rsip) as ip, inservice, rsport, rspool_id, rsp.name as rspool_name from " .
		"IPv4RS as rs inner join IPv4RSPool as rsp on rs.rspool_id = rsp.id " .
		"where ${whereexpr4} " .
		"order by ip, rsport, rspool_id";
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = ip2long ($row['ip']);
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPv4Address ($row['ip']);
		$tmp = array();
		foreach (array ('rspool_id', 'rsport', 'rspool_name', 'inservice') as $cname)
			$tmp[$cname] = $row[$cname];
		$ret[$ip_bin]['rslist'][] = $tmp;
	}
	unset ($result);

	// 5. add NAT rules, part 1
	$query =
		"select " .
		"proto, " .
		"INET_NTOA(localip) as localip, " .
		"localport, " .
		"INET_NTOA(remoteip) as remoteip, " .
		"remoteport, " .
		"description " .
		"from IPv4NAT " .
		"where ${whereexpr5a} " .
		"order by localip, localport, remoteip, remoteport, proto";
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$remoteip_bin = ip2long ($row['remoteip']);
		if (!isset ($ret[$remoteip_bin]))
			$ret[$remoteip_bin] = constructIPv4Address ($row['remoteip']);
		$ret[$remoteip_bin]['inpf'][] = $row;
	}
	unset ($result);
	// 5. add NAT rules, part 2
	$query =
		"select " .
		"proto, " .
		"INET_NTOA(localip) as localip, " .
		"localport, " .
		"INET_NTOA(remoteip) as remoteip, " .
		"remoteport, " .
		"description " .
		"from IPv4NAT " .
		"where ${whereexpr5b} " .
		"order by localip, localport, remoteip, remoteport, proto";
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$localip_bin = ip2long ($row['localip']);
		if (!isset ($ret[$localip_bin]))
			$ret[$localip_bin] = constructIPv4Address ($row['localip']);
		$ret[$localip_bin]['outpf'][] = $row;
	}
	unset ($result);
	return $ret;
}

function getIPv4Address ($dottedquad = '')
{
	if ($dottedquad == '')
		throw new InvalidArgException ('$dottedquad', $dottedquad);
	$i32 = ip2long ($dottedquad); // signed 32 bit
	$scanres = scanIPv4Space (array (array ('i32_first' => $i32, 'i32_last' => $i32)));
	if (!isset ($scanres[$i32]))
		//$scanres[$i32] = constructIPv4Address ($dottedquad); // XXX: this should be verified to not break things
		return constructIPv4Address ($dottedquad);
	markupIPv4AddrList ($scanres);
	return $scanres[$i32];
}

function bindIpToObject ($ip = '', $object_id = 0, $name = '', $type = '')
{
	$result = useInsertBlade
	(
		'IPv4Allocation',
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

// Return the id of the smallest IPv4 network containing the given IPv4 address
// or NULL, if nothing was found. When finding the covering network for
// another network, it is important to filter out matched records with longer
// masks (they aren't going to be the right pick).
function getIPv4AddressNetworkId ($dottedquad, $masklen = 32)
{
// N.B. To perform the same for IPv6 address and networks, some pre-requisites
// are necessary and a different query. IPv6 addresses are 128 bit long, which
// is too much for both PHP and MySQL data types. These values must be split
// into 4 32-byte long parts (b_u32_0, b_u32_1, b_u32_2, b_u32_3).
// Then each network must have its 128-bit netmask split same way and either
// stored right in its record or JOINed from decoder and accessible as m_u32_0,
// m_u32_1, m_u32_2, m_u32_3. After that the query to pick the smallest network
// covering the given address would look as follows:
// $query = 'select id from IPv6Network as n where ' .
// "(${b_u32_0} & n.m_u32_0 = n.b_u32_0) and " .
// "(${b_u32_1} & n.m_u32_1 = n.b_u32_1) and " .
// "(${b_u32_2} & n.m_u32_2 = n.b_u32_2) and " .
// "(${b_u32_3} & n.m_u32_3 = n.b_u32_3) and " .
// "mask < ${masklen} " .
// 'order by mask desc limit 1';

	$query = 'select id from IPv4Network where ' .
		"inet_aton('${dottedquad}') & (4294967295 >> (32 - mask)) << (32 - mask) = ip " .
		"and mask < ${masklen} " .
		'order by mask desc limit 1';
	$result = useSelectBlade ($query, __FUNCTION__);
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
		return $row['id'];
	return NULL;
}

function updateIPv4Network_real ($id = 0, $name = '', $comment = '')
{
	global $dbxlink;
	$query = $dbxlink->prepare ('UPDATE IPv4Network SET name = ?, comment = ? WHERE id = ?');
	// TODO: $dbxlink->setAttribute (PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
	return $query->execute (array ($name, $comment, $id)) ? '' : 'SQL query failed in ' . __FUNCTION__;
}

// This function is actually used not only to update, but also to create records,
// that's why ON DUPLICATE KEY UPDATE was replaced by DELETE-INSERT pair
// (MySQL 4.0 workaround).
function updateAddress ($ip = 0, $name = '', $reserved = 'no')
{
	// DELETE may safely fail.
	$r = useDeleteBlade ('IPv4Address', 'ip', "INET_ATON('${ip}')");
	// INSERT may appear not necessary.
	if ($name == '' and $reserved == 'no')
		return '';
	if (useInsertBlade ('IPv4Address', array ('name' => "'${name}'", 'reserved' => "'${reserved}'", 'ip' => "INET_ATON('${ip}')")))
		return '';
	else
		return __FUNCTION__ . '(): useInsertBlade() failed';
}

function updateBond ($ip='', $object_id=0, $name='', $type='')
{
	global $dbxlink;

	$query =
		"update IPv4Allocation set name='$name', type='$type' where ip=INET_ATON('$ip') and object_id='$object_id'";
	$result = $dbxlink->exec ($query);
	return '';
}

function unbindIpFromObject ($ip='', $object_id=0)
{
	global $dbxlink;

	$query =
		"delete from IPv4Allocation where ip=INET_ATON('$ip') and object_id='$object_id'";
	$result = $dbxlink->exec ($query);
	return '';
}

function getIPv4PrefixSearchResult ($terms)
{
	$byname = getSearchResultByField
	(
		'IPv4Network',
		array ('id'),
		'name',
		$terms,
		'ip'
	);
	$ret = array();
	foreach ($byname as $row)
		$ret[] = spotEntity ('ipv4net', $row['id']);
	return $ret;
}

function getIPv4AddressSearchResult ($terms)
{
	$query = "select inet_ntoa(ip) as ip, name from IPv4Address where ";
	$or = '';
	foreach (explode (' ', $terms) as $term)
	{
		$query .= $or . "name like '%${term}%'";
		$or = ' or ';
	}
	$result = useSelectBlade ($query, __FUNCTION__);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[] = $row;
	return $ret;
}

function getIPv4RSPoolSearchResult ($terms)
{
	$byname = getSearchResultByField
	(
		'IPv4RSPool',
		array ('id'),
		'name',
		$terms,
		'name'
	);
	$ret = array();
	foreach ($byname as $row)
		$ret[] = spotEntity ('ipv4rspool', $row['id']);
	return $ret;
}

function getIPv4VServiceSearchResult ($terms)
{
	$byname = getSearchResultByField
	(
		'IPv4VS',
		array ('id'),
		'name',
		$terms,
		'vip'
	);
	$ret = array();
	foreach ($byname as $row)
		$ret[] = spotEntity ('ipv4vs', $row['id']);
	return $ret;
}

function getAccountSearchResult ($terms)
{
	$byUsername = getSearchResultByField
	(
		'UserAccount',
		array ('user_id', 'user_name', 'user_realname'),
		'user_name',
		$terms,
		'user_name'
	);
	$byRealname = getSearchResultByField
	(
		'UserAccount',
		array ('user_id', 'user_name', 'user_realname'),
		'user_realname',
		$terms,
		'user_name'
	);
	// Filter out dupes.
	foreach ($byUsername as $res1)
		foreach (array_keys ($byRealname) as $key2)
			if ($res1['user_id'] == $byRealname[$key2]['user_id'])
			{
				unset ($byRealname[$key2]);
				continue 2;
			}
	$ret = array_merge ($byUsername, $byRealname);
	// Set realm, so it's renderable.
	foreach (array_keys ($ret) as $key)
		$ret[$key]['realm'] = 'user';
	return $ret;
}

function getFileSearchResult ($terms)
{
	$byName = getSearchResultByField
	(
		'File',
		array ('id'),
		'name',
		$terms,
		'name'
	);
	$byComment = getSearchResultByField
	(
		'File',
		array ('id'),
		'comment',
		$terms,
		'name'
	);
	// Filter out dupes.
	foreach ($byName as $res1)
		foreach (array_keys ($byComment) as $key2)
			if ($res1['id'] == $byComment[$key2]['id'])
			{
				unset ($byComment[$key2]);
				continue 2;
			}
	$ret = array();
	foreach (array_merge ($byName, $byComment) as $row)
		$ret[] = spotEntity ('file', $row['id']);
	return $ret;
}

function getRackSearchResult ($terms)
{
	$byName = getSearchResultByField
	(
		'Rack',
		array ('id'),
		'name',
		$terms,
		'name'
	);
	$byComment = getSearchResultByField
	(
		'Rack',
		array ('id'),
		'comment',
		$terms,
		'name'
	);
	// Filter out dupes.
	foreach ($byName as $res1)
		foreach (array_keys ($byComment) as $key2)
			if ($res1['id'] == $byComment[$key2]['id'])
			{
				unset ($byComment[$key2]);
				continue 2;
			}
	$ret = array();
	foreach (array_merge ($byName, $byComment) as $row)
		$ret[] = spotEntity ('rack', $row['id']);
	return $ret;
}

function getSearchResultByField ($tname, $rcolumns, $scolumn, $terms, $ocolumn = '', $exactness = 0)
{
	$pfx = '';
	$query = 'select ';
	foreach ($rcolumns as $col)
	{
		$query .= $pfx . $col;
		$pfx = ', ';
	}
	$pfx = '';
	$query .= " from ${tname} where ";
	foreach (explode (' ', $terms) as $term)
	{
		switch ($exactness)
		{
		case 2: // does this work as expected?
			$query .= $pfx . "binary ${scolumn} = '${term}'";
			break;
		case 1:
			$query .= $pfx . "${scolumn} = '${term}'";
			break;
		default:
			$query .= $pfx . "${scolumn} like '%${term}%'";
			break;
		}
		$pfx = ' or ';
	}
	if ($ocolumn != '')
		$query .= " order by ${ocolumn}";
	$result = useSelectBlade ($query, __FUNCTION__);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[] = $row;
	return $ret;
}

function getObjectSearchResults ($what)
{
	$ret = array();
	foreach (getStickerSearchResults ($what) as $objRecord)
	{
		$ret[$objRecord['id']]['id'] = $objRecord['id'];
		$ret[$objRecord['id']]['by_sticker'] = $objRecord['by_sticker'];
	}
	foreach (getPortSearchResults ($what) as $objRecord)
	{
		$ret[$objRecord['id']]['id'] = $objRecord['id'];
		$ret[$objRecord['id']]['by_port'] = $objRecord['by_port'];
	}
	foreach (getObjectAttrsSearchResults ($what) as $objRecord)
	{
		$ret[$objRecord['id']]['id'] = $objRecord['id'];
		$ret[$objRecord['id']]['by_attr'] = $objRecord['by_attr'];
	}
	foreach (getObjectIfacesSearchResults ($what) as $objRecord)
	{
		$ret[$objRecord['id']]['id'] = $objRecord['id'];
		$ret[$objRecord['id']]['by_iface'] = $objRecord['by_iface'];
	}
	foreach (getObjectNATSearchResults ($what) as $objRecord)
	{
		$ret[$objRecord['id']]['id'] = $objRecord['id'];
		$ret[$objRecord['id']]['by_nat'] = $objRecord['by_nat'];
	}
	return $ret;
}

function getObjectAttrsSearchResults ($what)
{
	$ret = array();
	foreach (array ('name', 'label', 'asset_no', 'barcode') as $column)
	{
		$tmp = getSearchResultByField
		(
			'RackObject',
			array ('id'),
			$column,
			$what,
			$column
		);
		foreach ($tmp as $row)
		{
			$ret[$row['id']]['id'] = $row['id'];
			$ret[$row['id']]['by_attr'][] = $column;
		}
	}
	return $ret;
}

// Look for EXACT value in stickers and return a list of pairs "object_id-attribute_id",
// which matched. A partilar object_id could be returned more, than once, if it has
// multiple matching stickers. Search is only performed on "string" attributes.
function getStickerSearchResults ($what, $exactness = 0)
{
	$stickers = getSearchResultByField
	(
		'AttributeValue',
		array ('object_id', 'attr_id'),
		'string_value',
		$what,
		'object_id',
		$exactness
	);
	$map = getAttrMap();
	$ret = array();
	foreach ($stickers as $sticker)
		if ($map[$sticker['attr_id']]['type'] == 'string')
		{
			$ret[$sticker['object_id']]['id'] = $sticker['object_id'];
			$ret[$sticker['object_id']]['by_sticker'][] = $sticker['attr_id'];
		}
	return $ret;
}

// search in port "reservation comment" and "L2 address" columns
function getPortSearchResults ($what)
{
	$ports = getSearchResultByField
	(
		'Port',
		array ('object_id', 'id'),
		'reservation_comment',
		$what,
		'object_id',
		0
	);
	$ret = array();
	foreach ($ports as $port)
	{
		$ret[$port['object_id']]['id'] = $port['object_id'];
		$ret[$port['object_id']]['by_port'][] = $port['id'];
	}
	if (NULL === ($db_l2address = l2addressForDatabase ($what)))
		return $ret;
	$ports = getSearchResultByField
	(
		'Port',
		array ('object_id', 'id'),
		'l2address',
		$db_l2address,
		'object_id',
		2
	);
	foreach ($ports as $port)
	{
		$ret[$port['object_id']]['id'] = $port['object_id'];
		$ret[$port['object_id']]['by_port'][] = $port['id'];
	}
	return $ret;
}

// search in IPv4 allocations
function getObjectIfacesSearchResults ($what)
{
	$ret = array();
	$ifaces = getSearchResultByField
	(
		'IPv4Allocation',
		array ('object_id', 'name'),
		'name',
		$what,
		'object_id'
	);
	foreach ($ifaces as $row)
	{
		$ret[$row['object_id']]['id'] = $row['object_id'];
		$ret[$row['object_id']]['by_iface'][] = $row['name'];
	}
	return $ret;
}

function getObjectNATSearchResults ($what)
{
	$ret = array();
	$ifaces = getSearchResultByField
	(
		'IPv4NAT',
		array ('object_id', 'description'),
		'description',
		$what,
		'object_id'
	);
	foreach ($ifaces as $row)
	{
		$ret[$row['object_id']]['id'] = $row['object_id'];
		$ret[$row['object_id']]['by_nat'][] = $row['description'];
	}
	return $ret;
}

// This function returns either port ID or NULL for specified arguments.
// This function returns either port ID or NULL for specified arguments.
function getPortID ($object_id, $port_name)
{
	$query = "select id from Port where object_id=${object_id} and name='${port_name}' limit 2";
	$result = useSelectBlade ($query, __FUNCTION__);
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

// This function returns an array of all port type pairs from PortCompat table.
function getPortCompat ()
{
	$query =
		"select type1, type2, d1.dict_value as type1name, d2.dict_value as type2name from " .
		"PortCompat as pc inner join Dictionary as d1 on pc.type1 = d1.dict_key " .
		"inner join Dictionary as d2 on pc.type2 = d2.dict_key " .
		"inner join Chapter as c1 on d1.chapter_id = c1.id " .
		"inner join Chapter as c2 on d2.chapter_id = c2.id " .
		"where c1.name = 'PortType' and c2.name = 'PortType'";
	$result = useSelectBlade ($query, __FUNCTION__);
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
// FIXME: there's a lot of excess legacy code in this function, it's reasonable
// to merge it with readChapter().
function getDict ($parse_links = FALSE)
{
	$query =
		"select Chapter.name as chapter_name, Chapter.id as chapter_no, dict_key, dict_value, sticky from " .
		"Chapter left join Dictionary on Chapter.id = Dictionary.chapter_id order by Chapter.name, dict_value";
	$result = useSelectBlade ($query, __FUNCTION__);
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
		{
			$dict[$chapter_no]['word'][$row['dict_key']] = ($parse_links or $row['dict_key'] <= MAX_DICT_KEY) ?
				parseWikiLink ($row['dict_value'], 'a') : $row['dict_value'];
			$dict[$chapter_no]['refcnt'][$row['dict_key']] = 0;
		}
	}
	unset ($result);
	// Find the list of all assigned values of dictionary-addressed attributes, each with
	// chapter/word keyed reference counters. Use the structure to adjust reference counters
	// of the returned disctionary words.
	$query = "select a.id as attr_id, am.chapter_id as chapter_no, uint_value, count(object_id) as refcnt " .
		"from Attribute as a inner join AttributeMap as am on a.id = am.attr_id " .
		"inner join AttributeValue as av on a.id = av.attr_id " .
		"inner join Dictionary as d on am.chapter_id = d.chapter_id and av.uint_value = d.dict_key " .
		"where a.type = 'dict' group by a.id, am.chapter_id, uint_value " .
		"order by a.id, am.chapter_id, uint_value";
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$dict[$row['chapter_no']]['refcnt'][$row['uint_value']] = $row['refcnt'];
	unset ($result);
	// PortType chapter is referenced by PortCompat and Port tables
	$query = 'select dict_key as uint_value, chapter_id as chapter_no, (select count(*) from PortCompat where type1 = dict_key or type2 = dict_key) + ' .
		'(select count(*) from Port where type = dict_key) as refcnt ' .
		'from Dictionary where chapter_id = 2';
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$dict[$row['chapter_no']]['refcnt'][$row['uint_value']] = $row['refcnt'];
	unset ($result);
	// RackObjectType chapter is referenced by AttributeMap and RackObject tables
	$query = 'select dict_key as uint_value, chapter_id as chapter_no, (select count(*) from AttributeMap where objtype_id = dict_key) + ' .
		'(select count(*) from RackObject where objtype_id = dict_key) as refcnt from Dictionary where chapter_id = 1';
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$dict[$row['chapter_no']]['refcnt'][$row['uint_value']] = $row['refcnt'];
	unset ($result);
	return $dict;
}

function getDictStats ()
{
	$stock_chapters = array (1, 2, 11, 12, 13, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27);
	$query =
		"select Chapter.id as chapter_no, Chapter.name as chapter_name, count(dict_key) as wc from " .
		"Chapter left join Dictionary on Chapter.id = Dictionary.chapter_id group by Chapter.id";
	$result = useSelectBlade ($query, __FUNCTION__);
	$tc = $tw = $uc = $uw = 0;
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$tc++;
		$tw += $row['wc'];;
		if (in_array ($row['chapter_no'], $stock_chapters))
			continue;
		$uc++;
		$uw += $row['wc'];;
	}
	$result->closeCursor();
	unset ($result);
	$query = "select count(id) as attrc from RackObject as ro left join " .
		"AttributeValue as av on ro.id = av.object_id group by ro.id";
	$result = useSelectBlade ($query, __FUNCTION__);
	$to = $ta = $so = 0;
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$to++;
		if ($row['attrc'] != 0)
		{
			$so++;
			$ta += $row['attrc'];
		}
	}
	$result->closeCursor();
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

function getIPv4Stats ()
{
	$ret = array();
	$subject = array();
	$subject[] = array ('q' => 'select count(id) from IPv4Network', 'txt' => 'Networks');
	$subject[] = array ('q' => 'select count(ip) from IPv4Address', 'txt' => 'Addresses commented/reserved');
	$subject[] = array ('q' => 'select count(ip) from IPv4Allocation', 'txt' => 'Addresses allocated');
	$subject[] = array ('q' => 'select count(*) from IPv4NAT', 'txt' => 'NAT rules');
	$subject[] = array ('q' => 'select count(id) from IPv4VS', 'txt' => 'Virtual services');
	$subject[] = array ('q' => 'select count(id) from IPv4RSPool', 'txt' => 'Real server pools');
	$subject[] = array ('q' => 'select count(id) from IPv4RS', 'txt' => 'Real servers');
	$subject[] = array ('q' => 'select count(distinct object_id) from IPv4LB', 'txt' => 'Load balancers');

	foreach ($subject as $item)
	{
		$result = useSelectBlade ($item['q'], __FUNCTION__);
		$row = $result->fetch (PDO::FETCH_NUM);
		$ret[$item['txt']] = $row[0];
		$result->closeCursor();
		unset ($result);
	}
	return $ret;
}

function getRackspaceStats ()
{
	$ret = array();
	$subject = array();
	$subject[] = array ('q' => 'select count(*) from RackRow', 'txt' => 'Rack rows');
	$subject[] = array ('q' => 'select count(*) from Rack', 'txt' => 'Racks');
	$subject[] = array ('q' => 'select avg(height) from Rack', 'txt' => 'Average rack height');
	$subject[] = array ('q' => 'select sum(height) from Rack', 'txt' => 'Total rack units in field');

	foreach ($subject as $item)
	{
		$result = useSelectBlade ($item['q'], __FUNCTION__);
		$row = $result->fetch (PDO::FETCH_NUM);
		$ret[$item['txt']] = !strlen ($row[0]) ? 0 : $row[0];
		$result->closeCursor();
		unset ($result);
	}
	return $ret;
}

/*

The following allows figuring out records in TagStorage, which refer to non-existing entities:

mysql> select entity_id from TagStorage left join Files on entity_id = id where entity_realm = 'file' and id is null;
mysql> select entity_id from TagStorage left join IPv4Network on entity_id = id where entity_realm = 'ipv4net' and id is null;
mysql> select entity_id from TagStorage left join RackObject on entity_id = id where entity_realm = 'object' and id is null;
mysql> select entity_id from TagStorage left join Rack on entity_id = id where entity_realm = 'rack' and id is null;
mysql> select entity_id from TagStorage left join IPv4VS on entity_id = id where entity_realm = 'ipv4vs' and id is null;
mysql> select entity_id from TagStorage left join IPv4RSPool on entity_id = id where entity_realm = 'ipv4rspool' and id is null;
mysql> select entity_id from TagStorage left join UserAccount on entity_id = user_id where entity_realm = 'user' and user_id is null;

Accordingly, these are the records, which refer to non-existent tags:

mysql> select tag_id from TagStorage left join TagTree on tag_id = id where id is null;

*/

// chapter_no is a must, see at @commitReduceDictionary() why
function commitUpdateDictionary ($chapter_no = 0, $dict_key = 0, $dict_value = '')
{
	if ($chapter_no <= 0)
		throw new InvalidArgException ('$chapter_no', $chapter_no);
	if ($dict_key <= 0)
		throw new InvalidArgException ('$dict_key', $dict_key);
	if (!strlen ($dict_value))
		throw new InvalidArgException ('$dict_value', $dict_value);

	global $dbxlink;
	$query =
		"update Dictionary set dict_value = '${dict_value}' where chapter_id=${chapter_no} " .
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
	if ($chapter_no <= 0)
		throw new InvalidArgException ('$chapter_no', $chapter_no);
	if (!strlen ($dict_value))
		throw new InvalidArgException ('$dict_value', $dict_value);
	return useInsertBlade
	(
		'Dictionary',
		array ('chapter_id' => $chapter_no, 'dict_value' => "'${dict_value}'")
	);
}

// Technically dict_key is enough to delete, but including chapter_id into
// WHERE clause makes sure, that the action actually happends for the same
// chapter, which authorization was granted for.
function commitReduceDictionary ($chapter_no = 0, $dict_key = 0)
{
	global $dbxlink;
	$query =
		"delete from Dictionary where chapter_id=${chapter_no} " .
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
	if (!strlen ($chapter_name))
		throw new InvalidArgException ('$chapter_name', $chapter_name);
	return useInsertBlade
	(
		'Chapter',
		array ('name' => "'${chapter_name}'")
	);
}

function commitUpdateChapter ($chapter_no = 0, $chapter_name = '')
{
	if (!strlen ($chapter_name))
		throw new InvalidArgException ('$chapter_name', $chapter_name);
	global $dbxlink;
	$query =
		"update Chapter set name = '${chapter_name}' where id = ${chapter_no} " .
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
	global $dbxlink;
	$query =
		"delete from Chapter where id = ${chapter_no} and sticky = 'no' limit 1";
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
	if (!strlen ($chapter_name))
		throw new InvalidArgException ('$chapter_name', $chapter_name);
	$query =
		"select dict_key, dict_value from Dictionary join Chapter on Chapter.id = Dictionary.chapter_id " .
		"where Chapter.name = '${chapter_name}'";
	$result = useSelectBlade ($query, __FUNCTION__);
	$chapter = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$chapter[$row['dict_key']] = parseWikiLink ($row['dict_value'], 'o');
	$result->closeCursor();
	// SQL ORDER BY had no sense, because we need to sort after link rendering, not before.
	asort ($chapter);
	return $chapter;
}

// Return a list of all stickers with sticker map applied. Each sticker records will
// list all its ways on the map with refcnt set.
function getAttrMap ()
{
	$query =
		'SELECT id, type, name, chapter_id, (SELECT name FROM Chapter WHERE id = chapter_id) ' .
		'AS chapter_name, objtype_id, (SELECT dict_value FROM Dictionary WHERE dict_key = objtype_id) ' .
		'AS objtype_name, (SELECT COUNT(object_id) FROM AttributeValue AS av INNER JOIN RackObject AS ro ' .
		'ON av.object_id = ro.id WHERE av.attr_id = Attribute.id AND ro.objtype_id = AttributeMap.objtype_id) ' .
		'AS refcnt FROM Attribute LEFT JOIN AttributeMap ON id = attr_id ORDER BY Attribute.name, objtype_id';
	$result = useSelectBlade ($query, __FUNCTION__);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		if (!isset ($ret[$row['id']]))
			$ret[$row['id']] = array
			(
				'id' => $row['id'],
				'type' => $row['type'],
				'name' => $row['name'],
				'application' => array(),
			);
		if ($row['objtype_id'] == '')
			continue;
		$application = array
		(
			'objtype_id' => $row['objtype_id'],
			'objtype_name' => $row['objtype_name'],
			'refcnt' => $row['refcnt'],
		);
		if ($row['type'] == 'dict')
		{
			$application['chapter_no'] = $row['chapter_id'];
			$application['chapter_name'] = $row['chapter_name'];
		}
		$ret[$row['id']]['application'][] = $application;
	}
	return $ret;
}

function commitUpdateAttribute ($attr_id = 0, $attr_name = '')
{
	if ($attr_id <= 0 or !strlen ($attr_name))
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	global $dbxlink;
	$query =
		"update Attribute set name = '${attr_name}' " .
		"where id = ${attr_id} limit 1";
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
	if (!strlen ($attr_name))
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
		array ('name' => "'${attr_name}'", 'type' => "'${attr_type}'")
	);
}

function commitDeleteAttribute ($attr_id = 0)
{
	if ($attr_id <= 0)
	{
		showError ('Invalid args', __FUNCTION__);
		die;
	}
	return useDeleteBlade ('Attribute', 'id', $attr_id);
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
			'chapter_id' => $chapter_no
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
function getAttrValues ($object_id, $strip_optgroup = FALSE)
{
	if ($object_id <= 0)
	{
		showError ('Invalid argument', __FUNCTION__);
		return NULL;
	}
	$ret = array();
	$query =
		"select A.id as attr_id, A.name as attr_name, A.type as attr_type, C.name as chapter_name, " .
		"AV.uint_value, AV.float_value, AV.string_value, D.dict_value from " .
		"RackObject as RO inner join AttributeMap as AM on RO.objtype_id = AM.objtype_id " .
		"inner join Attribute as A on AM.attr_id = A.id " .
		"left join AttributeValue as AV on AV.attr_id = AM.attr_id and AV.object_id = RO.id " .
		"left join Dictionary as D on D.dict_key = AV.uint_value and AM.chapter_id = D.chapter_id " .
		"left join Chapter as C on AM.chapter_id = C.id " .
		"where RO.id = ${object_id} order by A.type, A.name";
	$result = useSelectBlade ($query, __FUNCTION__);
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
				$record['value'] = $row[$row['attr_type'] . '_value'];
				$record['a_value'] = parseWikiLink ($record['value'], 'a');
				break;
			case 'dict':
				$record['value'] = parseWikiLink ($row[$row['attr_type'] . '_value'], 'o', $strip_optgroup);
				$record['a_value'] = parseWikiLink ($row[$row['attr_type'] . '_value'], 'a', $strip_optgroup);
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
	if (!strlen ($value))
		return commitResetAttrValue ($object_id, $attr_id);
	global $dbxlink;
	$query1 = "select type as attr_type from Attribute where id = ${attr_id}";
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
function useDeleteBlade ($tablename, $keyname, $keyvalue)
{
	global $dbxlink;
	return 1 === $dbxlink->exec ("delete from ${tablename} where ${keyname}=${keyvalue} limit 1");
}

function useSelectBlade ($query, $caller = 'N/A')
{
	global $dbxlink;
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		$ei = $dbxlink->errorInfo();
		showError ("SQL query '${query}'\n failed in useSelectBlade with error ${ei[1]} (${ei[2]})", $caller);
		return NULL;
	}
	return $result;
}

function loadConfigCache ()
{
	$query = 'SELECT varname, varvalue, vartype, is_hidden, emptyok, description FROM Config ORDER BY varname';
	$result = useSelectBlade ($query, __FUNCTION__);
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
	if (!strlen ($varname))
		throw new InvalidArgException ('$varname', $varname);
	if ($varvalue === NULL)
		throw new InvalidArgException ('$varvalue', $varvalue);
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
	if (count ($rows) != 1 || !strlen ($rows[0][0]))
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
	$query = 'select vs.id as vsid, inet_ntoa(vip) as vip, vport, proto, vs.name, object_id, ' .
		'lb.rspool_id, pool.name as pool_name, count(rs.id) as rscount ' .
		'from IPv4VS as vs inner join IPv4LB as lb on vs.id = lb.vs_id ' .
		'inner join IPv4RSPool as pool on lb.rspool_id = pool.id ' .
		'left join IPv4RS as rs on rs.rspool_id = lb.rspool_id ' .
		'group by vs.id, object_id order by vs.vip, object_id';
	$result = useSelectBlade ($query, __FUNCTION__);
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

function addRStoRSPool ($pool_id = 0, $rsip = '', $rsport = 0, $inservice = 'no', $rsconfig = '')
{
	if ($pool_id <= 0)
		throw new InvalidArgException ('$pool_id', $pool_id);
	if (!strlen ($rsport) or $rsport === 0)
		$rsport = 'NULL';
	return useInsertBlade
	(
		'IPv4RS',
		array
		(
			'rsip' => "inet_aton('${rsip}')",
			'rsport' => $rsport,
			'rspool_id' => $pool_id,
			'inservice' => ($inservice == 'yes' ? "'yes'" : "'no'"),
			'rsconfig' => (!strlen ($rsconfig) ? 'NULL' : "'${rsconfig}'")
		)
	);
}

function commitCreateVS ($vip = '', $vport = 0, $proto = '', $name = '', $vsconfig, $rsconfig, $taglist = array())
{
	if (!strlen ($vip))
		throw new InvalidArgException ('$vip', $vip);
	if ($vport <= 0)
		throw new InvalidArgException ('$vport', $vport);
	if (!strlen ($proto))
		throw new InvalidArgException ('$proto', $proto);
	if (!useInsertBlade
	(
		'IPv4VS',
		array
		(
			'vip' => "inet_aton('${vip}')",
			'vport' => $vport,
			'proto' => "'${proto}'",
			'name' => (!strlen ($name) ? 'NULL' : "'${name}'"),
			'vsconfig' => (!strlen ($vsconfig) ? 'NULL' : "'${vsconfig}'"),
			'rsconfig' => (!strlen ($rsconfig) ? 'NULL' : "'${rsconfig}'")
		)
	))
		return __FUNCTION__ . ': SQL insertion failed';
	return produceTagsForLastRecord ('ipv4vs', $taglist);
}

function addLBtoRSPool ($pool_id = 0, $object_id = 0, $vs_id = 0, $vsconfig = '', $rsconfig = '')
{
	if ($pool_id <= 0)
		throw new InvalidArgException ('$pool_id', $pool_id);
	if ($object_id <= 0)
		throw new InvalidArgException ('$object_id', $object_id);
	if ($vs_id <= 0)
		throw new InvalidArgException ('$vs_id', $vs_id);
	return useInsertBlade
	(
		'IPv4LB',
		array
		(
			'object_id' => $object_id,
			'rspool_id' => $pool_id,
			'vs_id' => $vs_id,
			'vsconfig' => (!strlen ($vsconfig) ? 'NULL' : "'${vsconfig}'"),
			'rsconfig' => (!strlen ($rsconfig) ? 'NULL' : "'${rsconfig}'")
		)
	);
}

function commitDeleteRS ($id = 0)
{
	if ($id <= 0)
		return FALSE;
	return useDeleteBlade ('IPv4RS', 'id', $id);
}

function commitDeleteVS ($id = 0)
{
	releaseFiles ('ipv4vs', $id);
	return useDeleteBlade ('IPv4VS', 'id', $id) && destroyTagsForEntity ('ipv4vs', $id);
}

function commitDeleteLB ($object_id = 0, $pool_id = 0, $vs_id = 0)
{
	global $dbxlink;
	if ($object_id <= 0 or $pool_id <= 0 or $vs_id <= 0)
		return FALSE;
	$query = "delete from IPv4LB where object_id = ${object_id} and " .
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
	if (long2ip (ip2long ($rsip)) !== $rsip)
		throw new InvalidArgException ('$rsip', $rsip);
	if (!strlen ($rsport) or $rsport === 0)
		$rsport = 'NULL';
	global $dbxlink;
	$query =
		"update IPv4RS set rsip = inet_aton('${rsip}'), rsport = ${rsport}, rsconfig = " .
		(!strlen ($rsconfig) ? 'NULL' : "'${rsconfig}'") .
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
	global $dbxlink;
	$query =
		"update IPv4LB set vsconfig = " .
		(!strlen ($vsconfig) ? 'NULL' : "'${vsconfig}'") .
		', rsconfig = ' .
		(!strlen ($rsconfig) ? 'NULL' : "'${rsconfig}'") .
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
	if (!strlen ($vip))
		throw new InvalidArgException ('$vip', $vip);
	if ($vport <= 0)
		throw new InvalidArgException ('$vport', $vport);
	if (!strlen ($proto))
		throw new InvalidArgException ('$proto', $proto);
	global $dbxlink;
	$query = "update IPv4VS set " .
		"vip = inet_aton('${vip}'), " .
		"vport = ${vport}, " .
		"proto = '${proto}', " .
		'name = ' . (!strlen ($name) ? 'NULL,' : "'${name}', ") .
		'vsconfig = ' . (!strlen ($vsconfig) ? 'NULL,' : "'${vsconfig}', ") .
		'rsconfig = ' . (!strlen ($rsconfig) ? 'NULL' : "'${rsconfig}'") .
		" where id = ${vsid} limit 1";
	$result = $dbxlink->exec ($query);
	if ($result === NULL)
		return FALSE;
	else
		return TRUE;
}

function loadThumbCache ($rack_id = 0)
{
	$ret = NULL;
	$query = "select thumb_data from Rack where id = ${rack_id} and thumb_data is not null limit 1";
	$result = useSelectBlade ($query, __FUNCTION__);
	$row = $result->fetch (PDO::FETCH_ASSOC);
	if ($row)
		$ret = base64_decode ($row['thumb_data']);
	$result->closeCursor();
	return $ret;
}

function saveThumbCache ($rack_id = 0, $cache = NULL)
{
	global $dbxlink;
	if ($cache == NULL)
		throw new InvalidArgException ('$cache', $cache);
	$data = base64_encode ($cache);
	$query = "update Rack set thumb_data = '${data}' where id = ${rack_id} limit 1";
	$result = $dbxlink->exec ($query);
}

function resetThumbCache ($rack_id = 0)
{
	global $dbxlink;
	$query = "update Rack set thumb_data = NULL where id = ${rack_id} limit 1";
	$result = $dbxlink->exec ($query);
}

// Return the list of attached RS pools for the given object. As long as we have
// the LB-VS UNIQUE in IPv4LB table, it is Ok to key returned records
// by vs_id, because there will be only one RS pool listed for each VS of the
// current object.
function getRSPoolsForObject ($object_id = 0)
{
	$query = 'select vs_id, inet_ntoa(vip) as vip, vport, proto, vs.name, pool.id as pool_id, ' .
		'pool.name as pool_name, count(rsip) as rscount, lb.vsconfig, lb.rsconfig from ' .
		'IPv4LB as lb inner join IPv4RSPool as pool on lb.rspool_id = pool.id ' .
		'inner join IPv4VS as vs on lb.vs_id = vs.id ' .
		'left join IPv4RS as rs on lb.rspool_id = rs.rspool_id ' .
		"where lb.object_id = ${object_id} " .
		'group by lb.rspool_id, lb.vs_id order by vs.vip, vport, proto, pool.name';
	$result = useSelectBlade ($query, __FUNCTION__);
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach (array ('vip', 'vport', 'proto', 'name', 'pool_id', 'pool_name', 'rscount', 'vsconfig', 'rsconfig') as $cname)
			$ret[$row['vs_id']][$cname] = $row[$cname];
	$result->closeCursor();
	return $ret;
}

function commitCreateRSPool ($name = '', $vsconfig = '', $rsconfig = '', $taglist = array())
{
	if (!strlen ($name))
		throw new InvalidArgException ('$name', $name);
	if (!useInsertBlade
	(
		'IPv4RSPool',
		array
		(
			'name' => (!strlen ($name) ? 'NULL' : "'${name}'"),
			'vsconfig' => (!strlen ($vsconfig) ? 'NULL' : "'${vsconfig}'"),
			'rsconfig' => (!strlen ($rsconfig) ? 'NULL' : "'${rsconfig}'")
		)
	))
		return __FUNCTION__ . ': SQL insertion failed';
	return produceTagsForLastRecord ('ipv4rspool', $taglist);
}

function commitDeleteRSPool ($pool_id = 0)
{
	global $dbxlink;
	if ($pool_id <= 0)
		return FALSE;
	releaseFiles ('ipv4rspool', $pool_id);
	return useDeleteBlade ('IPv4RSPool', 'id', $pool_id) && destroyTagsForEntity ('ipv4rspool', $pool_id);
}

function commitUpdateRSPool ($pool_id = 0, $name = '', $vsconfig = '', $rsconfig = '')
{
	global $dbxlink;
	$query = "update IPv4RSPool set " .
		'name = ' . (!strlen ($name) ? 'NULL,' : "'${name}', ") .
		'vsconfig = ' . (!strlen ($vsconfig) ? 'NULL,' : "'${vsconfig}', ") .
		'rsconfig = ' . (!strlen ($rsconfig) ? 'NULL' : "'${rsconfig}'") .
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
	$query = "select id, inservice, inet_ntoa(rsip) as rsip, rsport, rspool_id, rsconfig " .
		"from IPv4RS order by rspool_id, IPv4RS.rsip, rsport";
	$result = useSelectBlade ($query, __FUNCTION__);
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
	$query = "select object_id, count(rspool_id) as poolcount " .
		"from IPv4LB group by object_id order by object_id";
	$result = useSelectBlade ($query, __FUNCTION__);
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['object_id']] = $row['poolcount'];
	$result->closeCursor();
	return $ret;
}

// For the given object return: its vsconfig/rsconfig; the list of RS pools
// attached (each with vsconfig/rsconfig in turn), each with the list of
// virtual services terminating the pool. Each pool also lists all real
// servers with rsconfig.
function getSLBConfig ($object_id)
{
	$ret = array();
	$query = 'select vs_id, inet_ntoa(vip) as vip, vport, proto, vs.name as vs_name, ' .
		'vs.vsconfig as vs_vsconfig, vs.rsconfig as vs_rsconfig, ' .
		'lb.vsconfig as lb_vsconfig, lb.rsconfig as lb_rsconfig, pool.id as pool_id, pool.name as pool_name, ' .
		'pool.vsconfig as pool_vsconfig, pool.rsconfig as pool_rsconfig, ' .
		'rs.id as rs_id, inet_ntoa(rsip) as rsip, rsport, rs.rsconfig as rs_rsconfig from ' .
		'IPv4LB as lb inner join IPv4RSPool as pool on lb.rspool_id = pool.id ' .
		'inner join IPv4VS as vs on lb.vs_id = vs.id ' .
		'inner join IPv4RS as rs on lb.rspool_id = rs.rspool_id ' .
		"where lb.object_id = ${object_id} and rs.inservice = 'yes' " .
		"order by vs.vip, vport, proto, pool.name, rs.rsip, rs.rsport";
	$result = useSelectBlade ($query, __FUNCTION__);
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
	if (!strlen ($inservice))
		throw new InvalidArgException ('$inservice', $inservice);
	if ($rs_id <= 0)
		throw new InvalidArgException ('$rs_id', $rs_id);
	global $dbxlink;
	$query = "update IPv4RS set inservice = '${inservice}' where id = ${rs_id} limit 1";
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
	if ($object_id == 0)
		throw new InvalidArgException ('$object_id', $object_id);
	if ($type_id == 0)
		throw new InvalidArgException ('$type_id', $type_id);
	$ret = TRUE;
	foreach (getAutoPorts ($type_id) as $autoport)
		$ret = $ret and '' == commitAddPort ($object_id, $autoport['name'], $autoport['type'], '', '');
	return $ret;
}

// Return only implicitly listed tags, the rest of the chain will be
// generated/deducted later at higher levels.
// Result is a chain: randomly indexed taginfo list.
function loadEntityTags ($entity_realm = '', $entity_id = 0)
{
	$ret = array();
	if (!in_array ($entity_realm, array ('file', 'ipv4net', 'ipv4vs', 'ipv4rspool', 'object', 'rack', 'user')))
		return $ret;
	$query = "select tt.id, tag from " .
		"TagStorage as ts inner join TagTree as tt on ts.tag_id = tt.id " .
		"where entity_realm = '${entity_realm}' and entity_id = ${entity_id} " .
		"order by tt.tag";
	$result = useSelectBlade ($query, __FUNCTION__);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = $row;
	$result->closeCursor();
	return getExplicitTagsOnly ($ret);
}

// Return a tag chain with all DB tags on it.
function getTagList ()
{
	$ret = array();
	$query = "select id, parent_id, tag, entity_realm as realm, count(entity_id) as refcnt " .
		"from TagTree left join TagStorage on id = tag_id " .
		"group by id, entity_realm order by tag";
	$result = useSelectBlade ($query, __FUNCTION__);
	$ci = 0; // Collation index. The resulting rows are ordered according to default collation,
	// which is utf8_general_ci for UTF-8.
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		if (!isset ($ret[$row['id']]))
			$ret[$row['id']] = array
			(
				'id' => $row['id'],
				'tag' => $row['tag'],
				'ci' => $ci++,
				'parent_id' => $row['parent_id'],
				'refcnt' => array ('total' => 0)
			);
		if ($row['realm'])
		{
			$ret[$row['id']]['refcnt'][$row['realm']] = $row['refcnt'];
			$ret[$row['id']]['refcnt']['total'] += $row['refcnt'];
		}
	}
	$result->closeCursor();
	return $ret;
}

function commitCreateTag ($tagname = '', $parent_id = 0)
{
	if ($tagname == '' or $parent_id === 0)
		return "Invalid args to " . __FUNCTION__;
	$result = useInsertBlade
	(
		'TagTree',
		array
		(
			'tag' => "'${tagname}'",
			'parent_id' => $parent_id
		)
	);
	global $dbxlink;
	if ($result)
		return '';
	elseif ($dbxlink->errorCode() == 23000)
		return "name '${tag_name}' is already used";
	else
		return "SQL query failed in " . __FUNCTION__;
}

function commitDestroyTag ($tagid = 0)
{
	if ($tagid == 0)
		return 'Invalid arg to ' . __FUNCTION__;
	if (useDeleteBlade ('TagTree', 'id', $tagid))
		return '';
	else
		return 'useDeleteBlade() failed in ' . __FUNCTION__;
}

function commitUpdateTag ($tag_id, $tag_name, $parent_id)
{
	if ($parent_id == 0)
		$parent_id = 'NULL';
	global $dbxlink;
	$query = "update TagTree set tag = '${tag_name}', parent_id = ${parent_id} " .
		"where id = ${tag_id} limit 1";
	$result = $dbxlink->exec ($query);
	if ($result !== FALSE)
		return '';
	elseif ($dbxlink->errorCode() == 23000)
		return "name '${tag_name}' is already used";
	else
		return 'SQL query failed in ' . __FUNCTION__;
}

// Drop the whole chain stored.
function destroyTagsForEntity ($entity_realm, $entity_id)
{
	global $dbxlink;
	$query = "delete from TagStorage where entity_realm = '${entity_realm}' and entity_id = ${entity_id}";
	$result = $dbxlink->exec ($query);
	if ($result === NULL)
		return FALSE;
	else
		return TRUE;
}

// Drop only one record. This operation doesn't involve retossing other tags, unlike when adding.
// FIXME: this function could be used by 3rd-party scripts, dismiss it at some later point,
// but not now.
function deleteTagForEntity ($entity_realm, $entity_id, $tag_id)
{
	global $dbxlink;
	$query = "delete from TagStorage where entity_realm = '${entity_realm}' and entity_id = ${entity_id} and tag_id = ${tag_id}";
	$result = $dbxlink->exec ($query);
	if ($result === NULL)
		return FALSE;
	else
		return TRUE;
}

// Push a record into TagStorage unconditionally.
function addTagForEntity ($realm = '', $entity_id, $tag_id)
{
	if (!strlen ($realm))
		return FALSE;
	return useInsertBlade
	(
		'TagStorage',
		array
		(
			'entity_realm' => "'${realm}'",
			'entity_id' => $entity_id,
			'tag_id' => $tag_id,
		)
	);
}

// Add records into TagStorage, if this makes sense (IOW, they don't appear
// on the implicit list already). Then remove any other records, which
// appear on the "implicit" side of the chain. This will make sure,
// that both the tag base is still minimal and all requested tags appear on
// the resulting tag chain.
// Return TRUE, if any changes were committed.
function rebuildTagChainForEntity ($realm, $entity_id, $extrachain = array())
{
	// Put the current explicit sub-chain into a buffer and merge all tags from
	// the extra chain, which aren't there yet.
	$newchain = $oldchain = loadEntityTags ($realm, $entity_id);
	foreach ($extrachain as $extratag)
		if (!tagOnChain ($extratag, $newchain))
			$newchain[] = $extratag;
	// Then minimize the working buffer and check if it differs from the original
	// chain we started with. If it is so, save the work and signal the upper layer.
	$newchain = getExplicitTagsOnly ($newchain);
	if (tagChainCmp ($oldchain, $newchain))
	{
		destroyTagsForEntity ($realm, $entity_id);
		foreach ($newchain as $taginfo)
			addTagForEntity ($realm, $entity_id, $taginfo['id']);
		return TRUE;
	}
	return FALSE;
}

// Presume, that the target record has no tags attached.
function produceTagsForLastRecord ($realm, $tagidlist, $last_insert_id = 0)
{
	if (!count ($tagidlist))
		return '';
	if (!$last_insert_id)
		$last_insert_id = lastInsertID();
	$errcount = 0;
	foreach (getExplicitTagsOnly (buildTagChainFromIds ($tagidlist)) as $taginfo)
		if (addTagForEntity ($realm, $last_insert_id, $taginfo['id']) == FALSE)
			$errcount++;	
	if (!$errcount)
		return '';
	else
		return "Experienced ${errcount} errors adding tags in realm '${realm}' for entity ID == ${last_insert_id}";
}

function createIPv4Prefix ($range = '', $name = '', $is_bcast = FALSE, $taglist = array())
{
	// $range is in x.x.x.x/x format, split into ip/mask vars
	$rangeArray = explode('/', $range);
	if (count ($rangeArray) != 2)
		return "Invalid IPv4 prefix '${range}'";
	$ip = $rangeArray[0];
	$mask = $rangeArray[1];

	if (!strlen ($ip) or !strlen ($mask))
		return "Invalid IPv4 prefix '${range}'";
	$ipL = ip2long($ip);
	$maskL = ip2long($mask);
	if ($ipL == -1 || $ipL === FALSE)
		return 'Bad IPv4 address';
	if ($mask < 32 && $mask > 0)
		$maskL = $mask;
	else
	{
		$maskB = decbin($maskL);
		if (strlen($maskB)!=32)
			return 'Invalid netmask';
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
					return 'Invalid netmask';
			}
		}
		$maskL = $ones;
	}
	$binmask = binMaskFromDec($maskL);
	$ipL = $ipL & $binmask;
	$result = useInsertBlade
	(
		'IPv4Network',
		array
		(
			'ip' => sprintf ('%u', $ipL),
			'mask' => "'${maskL}'",
			'name' => "'${name}'"
		)
	);
	if ($result != TRUE)
		return "Could not add ${range} (already exists?).";

	if ($is_bcast and $maskL < 31)
	{
		$network_addr = long2ip ($ipL);
		$broadcast_addr = long2ip ($ipL | binInvMaskFromDec ($maskL));
		updateAddress ($network_addr, 'network', 'yes');
		updateAddress ($broadcast_addr, 'broadcast', 'yes');
	}
	return produceTagsForLastRecord ('ipv4net', $taglist);
}

// FIXME: This function doesn't wipe relevant records from IPv4Address table.
function destroyIPv4Prefix ($id = 0)
{
	if ($id <= 0)
		return __FUNCTION__ . ': Invalid IPv4 prefix ID';
	releaseFiles ('ipv4net', $id);
	if (!useDeleteBlade ('IPv4Network', 'id', $id))
		return __FUNCTION__ . ': SQL query #1 failed';
	if (!destroyTagsForEntity ('ipv4net', $id))
		return __FUNCTION__ . ': SQL query #2 failed';
	return '';
}

function loadScript ($name)
{
	$result = useSelectBlade ("select script_text from Script where script_name = '${name}'");
	$row = $result->fetch (PDO::FETCH_NUM);
	if ($row !== FALSE)
		return $row[0];
	else
		return NULL;
}

function saveScript ($name = '', $text)
{
	if (!strlen ($name))
		throw new InvalidArgException ('$name', $name);
	// delete regardless of existence
	useDeleteBlade ('Script', 'script_name', "'${name}'");
	return useInsertBlade
	(
		'Script',
		array
		(
			'script_name' => "'${name}'",
			'script_text' => "'${text}'"
		)
	);
}

function newPortForwarding ($object_id, $localip, $localport, $remoteip, $remoteport, $proto, $description)
{
	if (NULL === getIPv4AddressNetworkId ($localip))
		return "$localip: Non existant ip";
	if (NULL === getIPv4AddressNetworkId ($remoteip))
		return "$remoteip: Non existant ip";
	if ( ($localport <= 0) or ($localport >= 65536) )
		return "$localport: invaild port";
	if ( ($remoteport <= 0) or ($remoteport >= 65536) )
		return "$remoteport: invaild port";

	$result = useInsertBlade
	(
		'IPv4NAT',
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

function deletePortForwarding ($object_id, $localip, $localport, $remoteip, $remoteport, $proto)
{
	global $dbxlink;

	$query =
		"delete from IPv4NAT where object_id='$object_id' and localip=INET_ATON('$localip') and remoteip=INET_ATON('$remoteip') and localport='$localport' and remoteport='$remoteport' and proto='$proto'";
	$result = $dbxlink->exec ($query);
	return '';
}

function updatePortForwarding ($object_id, $localip, $localport, $remoteip, $remoteport, $proto, $description)
{
	global $dbxlink;

	$query =
		"update IPv4NAT set description='$description' where object_id='$object_id' and localip=INET_ATON('$localip') and remoteip=INET_ATON('$remoteip') and localport='$localport' and remoteport='$remoteport' and proto='$proto'";
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
		"from IPv4NAT ".
		"left join IPv4Address as ipa1 on IPv4NAT.localip = ipa1.ip " .
		"left join IPv4Address as ipa2 on IPv4NAT.remoteip = ipa2.ip " .
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
		"IPv4NAT.object_id as object_id, ".
		"RackObject.name as object_name, ".
		"description ".
		"from ((IPv4NAT join IPv4Allocation on remoteip=IPv4Allocation.ip) join RackObject on IPv4NAT.object_id=RackObject.id) ".
		"where IPv4Allocation.object_id='$object_id' ".
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

// Return a list of files, which are not linked to the specified record. This list
// will be used by printSelect().
function getAllUnlinkedFiles ($entity_type = NULL, $entity_id = 0)
{
	if ($entity_type == NULL)
		throw new InvalidArgException ('$entity_type', $entity_type);
	if ($entity_id == 0)
		throw new InvalidArgException ('$entity_id', $entity_id);
	global $dbxlink;
	$sql =
		'SELECT id, name FROM File ' .
		'WHERE id NOT IN (SELECT file_id FROM FileLink WHERE entity_type = ? AND entity_id = ?) ' .
		'ORDER BY name, id';
	$query = $dbxlink->prepare($sql);
	$query->bindParam(1, $entity_type);
	$query->bindParam(2, $entity_id);
	$query->execute();
	$ret=array();
	while ($row = $query->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = $row['name'];
	return $ret;
}

// FIXME: return a standard cell list, so upper layer can iterate over
// it conveniently.
function getFilesOfEntity ($entity_type = NULL, $entity_id = 0)
{
	if ($entity_type === NULL)
		throw new InvalidArgException ('$entity_type', $entity_type);
	if ($entity_id <= 0)
		throw new InvalidArgException ('$entity_id', $entity_id);
	global $dbxlink;
	$sql =
		'SELECT FileLink.file_id, FileLink.id AS link_id, name, type, size, ctime, mtime, atime, comment ' .
		'FROM FileLink LEFT JOIN File ON FileLink.file_id = File.id ' .
		'WHERE FileLink.entity_type = ? AND FileLink.entity_id = ? ORDER BY name';
	$query  = $dbxlink->prepare($sql);
	$query->bindParam(1, $entity_type);
	$query->bindParam(2, $entity_id);
	$query->execute();
	$ret = array();
	while ($row = $query->fetch (PDO::FETCH_ASSOC))
		$ret[$row['file_id']] = array (
			'id' => $row['file_id'],
			'link_id' => $row['link_id'],
			'name' => $row['name'],
			'type' => $row['type'],
			'size' => $row['size'],
			'ctime' => $row['ctime'],
			'mtime' => $row['mtime'],
			'atime' => $row['atime'],
			'comment' => $row['comment'],
		);
	return $ret;
}

function getFile ($file_id = 0)
{
	global $dbxlink;
	$query = $dbxlink->prepare('SELECT * FROM File WHERE id = ?');
	$query->bindParam(1, $file_id);
	$query->execute();
	if (($row = $query->fetch (PDO::FETCH_ASSOC)) == NULL)
	{
		showError ('Query succeeded, but returned no data', __FUNCTION__);
		$ret = NULL;
	}
	else
	{
		$ret = array();
		$ret['id'] = $row['id'];
		$ret['name'] = $row['name'];
		$ret['type'] = $row['type'];
		$ret['size'] = $row['size'];
		$ret['ctime'] = $row['ctime'];
		$ret['mtime'] = $row['mtime'];
		$ret['atime'] = $row['atime'];
		$ret['contents'] = $row['contents'];
		$ret['comment'] = $row['comment'];
		$query->closeCursor();

		// Someone accessed this file, update atime
		$q_atime = $dbxlink->prepare('UPDATE File SET atime = ? WHERE id = ?');
		$q_atime->bindParam(1, date('YmdHis'));
		$q_atime->bindParam(2, $file_id);
		$q_atime->execute();
	}
	return $ret;
}

function getFileLinks ($file_id = 0)
{
	global $dbxlink;
	$query = $dbxlink->prepare('SELECT * FROM FileLink WHERE file_id = ? ORDER BY entity_type, entity_id');
	$query->bindParam(1, $file_id);
	$query->execute();
	$rows = $query->fetchAll (PDO::FETCH_ASSOC);
	$ret = array();
	foreach ($rows as $row)
	{
		// get info of the parent
		switch ($row['entity_type'])
		{
			case 'ipv4net':
				$page = 'ipv4net';
				$id_name = 'id';
				$parent = spotEntity ($row['entity_type'], $row['entity_id']);
				$name = sprintf("%s (%s/%s)", $parent['name'], $parent['ip'], $parent['mask']);
				break;
			case 'ipv4rspool':
				$page = 'ipv4rspool';
				$id_name = 'pool_id';
				$parent = spotEntity ($row['entity_type'], $row['entity_id']);
				$name = $parent['name'];
				break;
			case 'ipv4vs':
				$page = 'ipv4vs';
				$id_name = 'vs_id';
				$parent = spotEntity ($row['entity_type'], $row['entity_id']);
				$name = $parent['name'];
				break;
			case 'object':
				$page = 'object';
				$id_name = 'object_id';
				$parent = spotEntity ($row['entity_type'], $row['entity_id']);
				$name = $parent['dname'];
				break;
			case 'rack':
				$page = 'rack';
				$id_name = 'rack_id';
				$parent = spotEntity ($row['entity_type'], $row['entity_id']);
				$name = $parent['name'];
				break;
			case 'user':
				$page = 'user';
				$id_name = 'user_id';
				$parent = spotEntity ($row['entity_type'], $row['entity_id']);
				$name = $parent['user_name'];
				break;
		}

		// name needs to have some value for hrefs to work
        if (!strlen ($name))
			$name = sprintf("[Unnamed %s]", formatEntityName($row['entity_type']));

		$ret[$row['id']] = array(
				'page' => $page,
				'id_name' => $id_name,
				'entity_type' => $row['entity_type'],
				'entity_id' => $row['entity_id'],
				'name' => $name
		);
	}
	return $ret;
}

function getFileStats ()
{
	$query = 'SELECT entity_type, COUNT(*) AS count FROM FileLink GROUP BY entity_type';
	$result = useSelectBlade ($query, __FUNCTION__);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		if ($row['count'] > 0)
			$ret["Links in realm '${row['entity_type']}'"] = $row['count'];
	unset ($result);

	// Find number of files without any linkage
	$linkless_sql =
		'SELECT COUNT(*) ' .
		'FROM File ' .
		'WHERE id NOT IN (SELECT file_id FROM FileLink)';
	$result = useSelectBlade ($linkless_sql, __FUNCTION__);
	$ret["Unattached files"] = $result->fetchColumn ();
	unset ($result);

	// Find total number of files
	$total_sql = 'SELECT COUNT(*) FROM File';
	$result = useSelectBlade ($total_sql, __FUNCTION__);
	$ret["Total files"] = $result->fetchColumn ();

	return $ret;
}

function commitAddFile ($name, $type, $size, $contents, $comment)
{
	$now = date('YmdHis');

	global $dbxlink;
	$query  = $dbxlink->prepare('INSERT INTO File (name, type, size, ctime, mtime, atime, contents, comment) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
	$query->bindParam(1, $name);
	$query->bindParam(2, $type);
	$query->bindParam(3, $size);
	$query->bindParam(4, $now);
	$query->bindParam(5, $now);
	$query->bindParam(6, $now);
	$query->bindParam(7, $contents, PDO::PARAM_LOB);
	$query->bindParam(8, $comment);

	$result = $query->execute();


	if ($result)
		return '';
	elseif ($query->errorCode() == 23000)
		return "commitAddFile: File named '${name}' already exists";
	else
		return 'commitAddFile: SQL query failed';
}

function commitLinkFile ($file_id, $entity_type, $entity_id)
{
	global $dbxlink;
	$query  = $dbxlink->prepare('INSERT INTO FileLink (file_id, entity_type, entity_id) VALUES (?, ?, ?)');
	$query->bindParam(1, $file_id);
	$query->bindParam(2, $entity_type);
	$query->bindParam(3, $entity_id);

	$result = $query->execute();

	if ($result)
		return '';
	else
		return 'commitLinkFile: SQL query failed';
}

function commitReplaceFile ($file_id = 0, $contents)
{
	global $dbxlink;
	$query = $dbxlink->prepare('UPDATE File SET mtime = NOW(), contents = ?, size = LENGTH(contents) WHERE id = ?');
	$query->bindParam(1, $contents, PDO::PARAM_LOB);
	$query->bindParam(2, $file_id);

	$result = $query->execute();
	if (!$result)
	{
		showError ('commitReplaceFile: SQL query failed', __FUNCTION__);
		return FALSE;
	}
	return '';
}

function commitUpdateFile ($file_id = 0, $new_name = '', $new_type = '', $new_comment = '')
{
	if (!strlen ($new_name))
		throw new InvalidArgException ('$new_name', $new_name);
	if (!strlen ($new_type))
		throw new InvalidArgException ('$new_type', $new_type);
	global $dbxlink;
	$query = $dbxlink->prepare('UPDATE File SET name = ?, type = ?, comment = ? WHERE id = ?');
	$query->bindParam(1, $new_name);
	$query->bindParam(2, $new_type);
	$query->bindParam(3, $new_comment);
	$query->bindParam(4, $file_id);

	$result = $query->execute();
	if (!$result)
		return 'SQL query failed in ' . __FUNCTION__;
	return '';
}

function commitUnlinkFile ($link_id)
{
	if (useDeleteBlade ('FileLink', 'id', $link_id) != TRUE)
		return __FUNCTION__ . ': useDeleteBlade() failed';
	return '';
}

function commitDeleteFile ($file_id)
{
	destroyTagsForEntity ('file', $file_id);
	if (useDeleteBlade ('File', 'id', $file_id) != TRUE)
		return __FUNCTION__ . ': useDeleteBlade() failed';
	return '';
}

function getChapterList ()
{
	$ret = array();
	$result = useSelectBlade ('SELECT id, sticky, name, count(chapter_id) as wordc FROM Chapter LEFT JOIN Dictionary ON Chapter.id = chapter_id GROUP BY id ORDER BY name');
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = $row;
	return $ret;
}

// Return file id by file name.
function findFileByName ($filename)
{
	global $dbxlink;
	$query = $dbxlink->prepare('SELECT id FROM File WHERE name = ?');
	$query->bindParam(1, $filename);
	$query->execute();
	if (($row = $query->fetch (PDO::FETCH_ASSOC)))
		return $row['id'];

	return NULL;
}

function acquireLDAPCache ($form_username, $password_hash, $expiry = 0)
{
	global $dbxlink;
	$dbxlink->beginTransaction();
	$query = "select now() - first_success as success_age, now() - last_retry as retry_age, displayed_name, memberof " .
		"from LDAPCache where presented_username = '${form_username}' and successful_hash = '${password_hash}' " .
		"having success_age < ${expiry} for update";
	$result = useSelectBlade ($query);
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$row['memberof'] = unserialize (base64_decode ($row['memberof']));
		return $row;
	}
	return NULL;
}

function releaseLDAPCache ()
{
	global $dbxlink;
	$dbxlink->commit();
}

// This actually changes only last_retry.
function touchLDAPCacheRecord ($form_username)
{
	global $dbxlink;
	$query = "update LDAPCache set last_retry = NOW() where presented_username = '${form_username}'";
	$dbxlink->exec ($query);
}

function replaceLDAPCacheRecord ($form_username, $password_hash, $dname, $memberof)
{
	deleteLDAPCacheRecord ($form_username);
	useInsertBlade ('LDAPCache',
		array
		(
			'presented_username' => "'${form_username}'",
			'successful_hash' => "'${password_hash}'",
			'displayed_name' => "'${dname}'",
			'memberof' => "'" . base64_encode (serialize ($memberof)) . "'"
		)
	);
}

function deleteLDAPCacheRecord ($form_username)
{
	return useDeleteBlade ('LDAPCache', 'presented_username', "'${form_username}'");
}

// Age all records older, than cache_expiry seconds, and all records made in future.
// Calling this function w/o argument purges the whole LDAP cache.
function discardLDAPCache ($maxage = 0)
{
	global $dbxlink;
	$dbxlink->exec ("DELETE from LDAPCache WHERE NOW() - first_success >= ${maxage} or NOW() < first_success");
}

function getUserIDByUsername ($username)
{
	$query = "select user_id from UserAccount where user_name = '${username}'";
	if (($result = useSelectBlade ($query, __FUNCTION__)) == NULL) 
	{
		showError ('SQL query failed', __FUNCTION__);
		die;
	}
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
		return $row['user_id'];
	return NULL;
}

// Return TRUE, if the given address is assigned to a port of any object
// except the current object. Using this function as a constraint makes
// it possible to reuse L2 addresses within one object, yet keeping them
// universally unique on the other hand.
function alreadyUsedL2Address ($address, $my_object_id)
{
	$query = "SELECT COUNT(*) FROM Port WHERE BINARY l2address = '${address}' AND object_id != ${my_object_id}";
	if (NULL == ($result = useSelectBlade ($query, __FUNCTION__)))
	{
		showError ('SQL query failed', __FUNCTION__);
		die;
	}
	$row = $result->fetch (PDO::FETCH_NUM);
	return $row[0] != 0;
}

?>
