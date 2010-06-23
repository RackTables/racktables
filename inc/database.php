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
			'has_problems' => 'has_problems',
			'comment' => 'comment',
			'nports' => '(SELECT COUNT(*) FROM Port WHERE object_id = RackObject.id)',
			'runs8021Q' => '(SELECT 1 FROM VLANSwitch WHERE object_id = id LIMIT 1)',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('RackObject.name'),
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
		'ordcolumns' => array ('UserAccount.user_name'),
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
			'parent_id' => '(SELECT id FROM IPv4Network AS subt WHERE IPv4Network.ip & (4294967295 >> (32 - subt.mask)) << (32 - subt.mask) = subt.ip and subt.mask < IPv4Network.mask ORDER BY subt.mask DESC limit 1)',
			'vlanc' => '(SELECT COUNT(*) FROM VLANIPv4 WHERE ipv4net_id = id)',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('IPv4Network.ip', 'IPv4Network.mask'),
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
		'ordcolumns' => array ('File.name'),
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
		'ordcolumns' => array ('IPv4VS.vip', 'IPv4VS.proto', 'IPv4VS.vport'),
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
		'ordcolumns' => array ('IPv4RSPool.name', 'IPv4RSPool.id'),
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
		'ordcolumns' => array ('row_name', 'Rack.name'),
		'pidcolumn' => 'row_id',
	),
);

$searchfunc = array
(
	'object' => array
	(
		'by_sticker' => 'getStickerSearchResults',
		'by_port' => 'getPortSearchResults',
		'by_attr' => 'getObjectAttrsSearchResults',
		'by_iface' => 'getObjectIfacesSearchResults',
		'by_nat' => 'getObjectNATSearchResults',
	),
);

$tablemap_8021q = array
(
	'desired' => array
	(
		'pvm' => 'PortVLANMode',
		'pav' => 'PortAllowedVLAN',
		'pnv' => 'PortNativeVLAN',
	),
	'cached' => array
	(
		'pvm' => 'CachedPVM',
		'pav' => 'CachedPAV',
		'pnv' => 'CachedPNV',
	),
);

function escapeString ($value, $do_db_escape = FALSE)
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
		"where RackRow.id = ? " .
		"group by RackRow.id";
	$result = usePreparedSelectBlade ($query, array ($rackrow_id));
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
		return $row;
	else
		throw new EntityNotFoundException ('rackrow', $rackrow_id);
}

function getRackRows ()
{
	$result = usePreparedSelectBlade ('SELECT id, name FROM RackRow ORDER BY name');
	$rows = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$rows[$row['id']] = $row['name'];
	return $rows;
}

function commitAddRow($rackrow_name)
{
	return usePreparedInsertBlade ('RackRow', array ('name' => $rackrow_name));
}

function commitUpdateRow ($rackrow_id, $rackrow_name)
{
	return usePreparedExecuteBlade ('UPDATE RackRow SET name = ? WHERE id = ?', array ($rackrow_name, $rackrow_id));
}

function commitDeleteRow($rackrow_id)
{
	return usePreparedDeleteBlade ('RackRow', array ('id' => $rackrow_id));
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
	$qparams = array ($realm);
	$query = 'SELECT tag_id';
	foreach ($SQLinfo['columns'] as $alias => $expression)
		// Automatically prepend table name to each single column, but leave all others intact.
		$query .= ', ' . ($alias == $expression ? "${SQLinfo['table']}.${alias}" : "${expression} as ${alias}");
	$query .= " FROM ${SQLinfo['table']} LEFT JOIN TagStorage on entity_realm = ? and entity_id = ${SQLinfo['table']}.${SQLinfo['keycolumn']}";
	if (isset ($SQLinfo['pidcolumn']) and $parent_id)
	{
		$query .= " WHERE ${SQLinfo['table']}.${SQLinfo['pidcolumn']} = ?";
		$qparams[] = $parent_id;
	}
	$query .= " ORDER BY ";
	foreach ($SQLinfo['ordcolumns'] as $oc)
		$query .= "${oc}, ";
	$query .= " tag_id";
	$result = usePreparedSelectBlade ($query, $qparams);
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
	$query .= " FROM ${SQLinfo['table']} LEFT JOIN TagStorage on entity_realm = ? and entity_id = ${SQLinfo['table']}.${SQLinfo['keycolumn']}";
	$query .= " WHERE ${SQLinfo['table']}.${SQLinfo['keycolumn']} = ?";
	$query .= " ORDER BY tag_id";
	$result = usePreparedSelectBlade ($query, array ($realm, $id));
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
	case 'file':
		$record['links'] = getFileLinks ($record['id']);
		break;
	case 'ipv4rspool':
		$record['lblist'] = array();
		$query = "select object_id, vs_id, lb.vsconfig, lb.rsconfig from " .
			"IPv4LB as lb inner join IPv4VS as vs on lb.vs_id = vs.id " .
			"where rspool_id = ? order by object_id, vip, vport";
		$result = usePreparedSelectBlade ($query, array ($record['id']));
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
			$record['lblist'][$row['object_id']][$row['vs_id']] = array
			(
				'rsconfig' => $row['rsconfig'],
				'vsconfig' => $row['vsconfig'],
			);
		unset ($result);
		$record['rslist'] = array();
		$query = "select id, inservice, inet_ntoa(rsip) as rsip, rsport, rsconfig from " .
			"IPv4RS where rspool_id = ? order by IPv4RS.rsip, rsport";
		$result = usePreparedSelectBlade ($query, array ($record['id']));
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
			"where vs_id = ? order by pool.name, object_id";
		$result = usePreparedSelectBlade ($query, array ($record['id']));
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
			"from RackSpace where rack_id = ? and " .
			"unit_no between 1 and ? order by unit_no";
		$result = usePreparedSelectBlade ($query, array ($record['id'], $record['height']));
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
	case 'ipv4net':
		$record['8021q'] = getIPv4Network8021QBindings ($record['id']);
		break;
	default:
	}
}

function getObjectPortsAndLinks ($object_id)
{
	$query = "SELECT id, name, label, l2address, iif_id, (SELECT iif_name FROM PortInnerInterface WHERE id = iif_id) AS iif_name, " .
		"type AS oif_id, (SELECT dict_value FROM Dictionary WHERE dict_key = type) AS oif_name, reservation_comment " .
		"FROM Port WHERE object_id = ?";
	// list and decode all ports of the current object
	$result = usePreparedSelectBlade ($query, array ($object_id));
	$ret=array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$row['l2address'] = l2addressFromDatabase ($row['l2address']);
		$row['remote_id'] = NULL;
		$row['remote_name'] = NULL;
		$row['remote_object_id'] = NULL;
		$ret[] = $row;
	}
	unset ($result);
	// now find and decode remote ends for all locally terminated connections
	// FIXME: can't this data be extracted in one pass with sub-queries?
	foreach (array_keys ($ret) as $tmpkey)
	{
		$portid = $ret[$tmpkey]['id'];
		$remote_id = NULL;
		$query = "select porta, portb from Link where porta = ? or portb = ?";
		$result = usePreparedSelectBlade ($query, array ($portid, $portid));
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
			$query = "SELECT name, object_id FROM Port WHERE id = ?";
			$result = usePreparedSelectBlade ($query, array ($remote_id));
			if ($row = $result->fetch (PDO::FETCH_ASSOC))
			{
				$ret[$tmpkey]['remote_name'] = $row['name'];
				$ret[$tmpkey]['remote_object_id'] = $row['object_id'];
			}
			$ret[$tmpkey]['remote_id'] = $remote_id;
			unset ($result);
		}
	}
	usort ($ret, 'sortByName');
	return $ret;
}

function commitAddRack ($name, $height = 0, $row_id = 0, $comment, $taglist)
{
	if ($row_id <= 0 or $height <= 0 or !strlen ($name))
		return FALSE;
	$result = usePreparedInsertBlade
	(
		'Rack',
		array
		(
			'row_id' => $row_id,
			'name' => $name,
			'height' =>  $height,
			'comment' => $comment
		)
	);
	$last_insert_id = lastInsertID();
	return (produceTagsForLastRecord ('rack', $taglist, $last_insert_id) == '') and recordHistory ('Rack', $last_insert_id);
}

function commitAddObject ($new_name, $new_label, $new_barcode, $new_type_id, $new_asset_no, $taglist = array())
{
	// Maintain UNIQUE INDEX for common names and asset tags by
	// filtering out empty strings (not NULLs).
	$result1 = usePreparedInsertBlade
	(
		'RackObject',
		array
		(
			'name' => !strlen ($new_name) ? NULL : $new_name,
			'label' => $new_label,
			'barcode' => !strlen ($new_barcode) ? NULL : $new_barcode,
			'objtype_id' => $new_type_id,
			'asset_no' => !strlen ($new_asset_no) ? NULL : $new_asset_no,
		)
	);
	$last_insert_id = lastInsertID();
	// Do AutoPorts magic
	executeAutoPorts ($last_insert_id, $new_type_id);
	// Now tags...
	$error = produceTagsForLastRecord ('object', $taglist, $last_insert_id);

	recordHistory ('RackObject', $last_insert_id);

	return $last_insert_id;
}

function commitUpdateObject ($object_id = 0, $new_name = '', $new_label = '', $new_barcode = '', $new_type_id = 0, $new_has_problems = 'no', $new_asset_no = '', $new_comment = '')
{
	if ($new_type_id == 0)
		throw new InvalidArgException ('$new_type_id', $new_type_id);
	$ret = FALSE !== usePreparedExecuteBlade
	(
		'UPDATE RackObject SET name=?, label=?, barcode=?, objtype_id=?, has_problems=?, ' .
		'asset_no=?, comment=? WHERE id=?',
		array
		(
			!strlen ($new_name) ? NULL : $new_name,
			$new_label,
			!strlen ($new_barcode) ? NULL : $new_barcode,
			$new_type_id,
			$new_has_problems,
			!strlen ($new_asset_no) ? NULL : $new_asset_no,
			$new_comment,
			$object_id
		)
	);
	return $ret and recordHistory ('RackObject', $object_id);
}

// Remove file links related to the entity, but leave the entity and file(s) intact.
function releaseFiles ($entity_realm, $entity_id)
{
	usePreparedDeleteBlade ('FileLink', array ('entity_type' => $entity_realm, 'entity_id' => $entity_id));
}

// There are times when you want to delete all traces of an object
function commitDeleteObject ($object_id = 0)
{
	releaseFiles ('object', $object_id);
	destroyTagsForEntity ('object', $object_id);
	usePreparedDeleteBlade ('IPv4LB', array ('object_id' => $object_id));
	usePreparedDeleteBlade ('IPv4Allocation', array ('object_id' => $object_id));
	usePreparedDeleteBlade ('IPv4NAT', array ('object_id' => $object_id));
	usePreparedExecuteBlade ('DELETE FROM Atom WHERE molecule_id IN (SELECT new_molecule_id FROM MountOperation WHERE object_id = ?)', array ($object_id));
	usePreparedExecuteBlade ('DELETE FROM Molecule WHERE id IN (SELECT new_molecule_id FROM MountOperation WHERE object_id = ?)', array ($object_id));
	usePreparedDeleteBlade ('RackObject', array ('id' => $object_id));
	return '';
}

function commitDeleteRack($rack_id)
{
	releaseFiles ('rack', $rack_id);
	destroyTagsForEntity ('rack', $rack_id);
	usePreparedDeleteBlade ('RackSpace', array ('rack_id' => $rack_id));
	usePreparedDeleteBlade ('RackHistory', array ('id' => $rack_id));
	usePreparedDeleteBlade ('Rack', array ('id' => $rack_id));
	return TRUE;
}

function commitUpdateRack ($rack_id, $new_name, $new_height, $new_row_id, $new_comment)
{
	// Can't shrink a rack if rows being deleted contain mounted objects
	$check_result = usePreparedSelectBlade ('SELECT COUNT(*) AS count FROM RackSpace WHERE rack_id = ? AND unit_no > ?', array ($rack_id, $new_height));
	$check_row = $check_result->fetch (PDO::FETCH_ASSOC);
	unset ($check_result);
	if ($check_row['count'] > 0)
		throw new InvalidArgException ('new_height', $new_height, 'Cannot shrink rack, objects are still mounted there');

	usePreparedExecuteBlade
	(
		'UPDATE Rack SET name=?, height=?, comment=?, row_id=? WHERE id=?',
		array
		(
			$new_name,
			$new_height,
			$new_comment,
			$new_row_id,
			$rack_id,
		)
	);
	return recordHistory ('Rack', $rack_id);
}

// This function accepts rack data returned by amplifyCell(), validates and applies changes
// supplied in $_REQUEST and returns resulting array. Only those changes are examined, which
// correspond to current rack ID.
// 1st arg is rackdata, 2nd arg is unchecked state, 3rd arg is checked state.
// If 4th arg is present, object_id fields will be updated accordingly to the new state.
// The function returns the modified rack upon success.
function processGridForm (&$rackData, $unchecked_state, $checked_state, $object_id = 0)
{
	global $loclist;
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
			usePreparedDeleteBlade ('RackSpace', array ('rack_id' => $rack_id, 'unit_no' => $unit_no, 'atom' => $atom));
			if ($newstate != 'F')
				usePreparedInsertBlade ('RackSpace', array ('rack_id' => $rack_id, 'unit_no' => $unit_no, 'atom' => $atom, 'state' => $newstate));
			if ($newstate == 'T' and $object_id != 0)
			{
				// At this point we already have a record in RackSpace.
				$r = usePreparedExecuteBlade
				(
					'UPDATE RackSpace SET object_id=? ' .
					'WHERE rack_id=? AND unit_no=? AND atom=?',
					array ($object_id, $rack_id, $unit_no, $atom)
				);
				if ($r === FALSE)
					return array ('code' => 500, 'message' => "${rack_name}: Rack ID ${rack_id}, unit ${unit_no}, atom '${atom}' failed to set object_id to '${object_id}'");
				$rackData[$unit_no][$locidx]['object_id'] = $object_id;
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
function getMoleculeForObject ($object_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT rack_id, unit_no, atom FROM RackSpace ' .
		'WHERE state = "T" AND object_id = ? ORDER BY rack_id, unit_no, atom',
		array ($object_id)
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// This function builds a list of rack-unit-atom records for requested molecule.
function getMolecule ($mid = 0)
{
	$result = usePreparedSelectBlade ('SELECT rack_id, unit_no, atom FROM Atom WHERE molecule_id = ?', array ($mid));
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// returns exactly what is's named after
function lastInsertID ()
{
	$result = usePreparedSelectBlade ('select last_insert_id()');
	$row = $result->fetch (PDO::FETCH_NUM);
	return $row[0];
}

// This function creates a new record in Molecule and number of linked
// R-U-A records in Atom.
function createMolecule ($molData)
{
	usePreparedExecuteBlade ('INSERT INTO Molecule VALUES()');
	$molecule_id = lastInsertID();
	foreach ($molData as $rua)
		usePreparedInsertBlade
		(
			'Atom',
			array
			(
				'molecule_id' => $molecule_id,
				'rack_id' => $rua['rack_id'],
				'unit_no' => $rua['unit_no'],
				'atom' => $rua['atom'],
			)
		);
	return $molecule_id;
}

// History logger. This function assumes certain table naming convention and
// column design:
// 1. History table name equals to dictionary table name plus 'History'.
// 2. History table must have the same row set (w/o keys) plus one row named
// 'ctime' of type 'timestamp'.
function recordHistory ($tableName, $orig_id)
{
	global $remote_username;
	return FALSE !== usePreparedExecuteBlade
	(
		"INSERT INTO ${tableName}History SELECT *, CURRENT_TIMESTAMP(), ? " .
		"FROM ${tableName} WHERE id=?",
		array ($remote_username, $orig_id)
	);
}

function getRackspaceHistory ()
{
	$result = usePreparedSelectBlade
	(
		"SELECT id as mo_id, object_id as ro_id, ctime, comment, user_name FROM " .
		"MountOperation ORDER BY ctime DESC"
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// This function is used in renderRackspaceHistory()
function getOperationMolecules ($op_id = 0)
{
	$result = usePreparedSelectBlade ('SELECT old_molecule_id, new_molecule_id FROM MountOperation WHERE id = ?', array ($op_id));
	// We expect one row.
	$row = $result->fetch (PDO::FETCH_ASSOC);
	return array ($row['old_molecule_id'], $row['new_molecule_id']);
}

function getResidentRacksData ($object_id = 0, $fetch_rackdata = TRUE)
{
	$result = usePreparedSelectBlade ('SELECT DISTINCT rack_id FROM RackSpace WHERE object_id = ? ORDER BY rack_id', array ($object_id));
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
		$rackData = spotEntity ('rack', $row[0]);
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
	$matches = array();
	switch (1)
	{
	case preg_match ('/^([[:digit:]]+)-([[:digit:]]+)$/', $port_type_id, $matches):
		$iif_id = $matches[1];
		$oif_id = $matches[2];
		break;
	case preg_match ('/^([[:digit:]]+)$/', $port_type_id, $matches):
		$iif_id = 1;
		$oif_id = $matches[1];
		break;
	default:
		$dbxlink->exec ('UNLOCK TABLES');
		return "invalid port type id '${port_type_id}'";
	}
	$result = usePreparedInsertBlade
	(
		'Port',
		array
		(
			'name' => $port_name,
			'object_id' => $object_id,
			'label' => $port_label,
			'iif_id' => $iif_id,
			'type' => $oif_id,
			'l2address' => ($db_l2address === '') ? NULL : $db_l2address,
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
function commitUpdatePort ($object_id, $port_id, $port_name, $port_type_id, $port_label, $port_l2address, $port_reservation_comment)
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
	$result = usePreparedExecuteBlade
	(
		'UPDATE Port SET name=?, type=?, label=?, reservation_comment=?, ' .
		'l2address=? WHERE id=? AND object_id=?',
		array
		(
			$port_name,
			$port_type_id,
			$port_label,
			mb_strlen ($port_reservation_comment) ? $port_reservation_comment : NULL,
			($db_l2address === '') ? NULL : $db_l2address,
			$port_id,
			$object_id
		)
	);
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
	if (usePreparedDeleteBlade ('Port', array ('id' => $port_id)) != TRUE)
		return __FUNCTION__ . ': usePreparedDeleteBlade() failed';
	return '';
}

function getAllIPv4Allocations ()
{
	$result = usePreparedSelectBlade
	(
		"select object_id as object_id, ".
		"RackObject.name as object_name, ".
		"IPv4Allocation.name as name, ".
		"INET_NTOA(ip) as ip ".
		"from IPv4Allocation join RackObject on id=object_id "
	);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[] = $row;
	return $ret;
}

function linkPorts ($porta, $portb)
{
	if ($porta == $portb)
		throw new InvalidArgException ('porta/portb', $porta, "Ports can't be the same");
	if ($porta > $portb)
	{
		$tmp = $porta;
		$porta = $portb;
		$portb = $tmp;
	}
	$ret = FALSE !== usePreparedInsertBlade ('Link', array ('porta' => $porta, 'portb' => $portb));
	$ret = $ret and FALSE !== usePreparedExecuteBlade
	(
		'UPDATE Port SET reservation_comment=NULL WHERE id IN(?, ?)',
		array ($porta, $portb)
	);
	return $ret ? '' : 'query failed';
}

function unlinkPort ($port_id)
{
	usePreparedDeleteBlade ('Link', array ('porta' => $port_id, 'portb' => $port_id), 'OR');
	return '';
}

// Return all IPv4 addresses allocated to the objects. Attach detailed
// info about address to each alocation records. Index result by dotted-quad
// address.
function getObjectIPv4Allocations ($object_id = 0)
{
	$ret = array();
	$result = usePreparedSelectBlade
	(
		'SELECT name AS osif, type, inet_ntoa(ip) AS dottedquad FROM IPv4Allocation ' .
		'WHERE object_id = ? ORDER BY ip',
		array ($object_id)
	);
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
	$qparams = array();
	foreach ($pairlist as $tmp)
	{
		$db_first = sprintf ('%u', 0x00000000 + $tmp['i32_first']);
		$db_last = sprintf ('%u', 0x00000000 + $tmp['i32_last']);
		$whereexpr1 .= $or . "ip between ? and ?";
		$whereexpr2 .= $or . "ip between ? and ?";
		$whereexpr3 .= $or . "vip between ? and ?";
		$whereexpr4 .= $or . "rsip between ? and ?";
		$whereexpr5a .= $or . "remoteip between ? and ?";
		$whereexpr5b .= $or . "localip between ? and ?";
		$or = ' or ';
		$qparams[] = $db_first;
		$qparams[] = $db_last;
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
	$result = usePreparedSelectBlade ($query, $qparams);
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
	$result = usePreparedSelectBlade ($query, $qparams);
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
	$result = usePreparedSelectBlade ($query, $qparams);
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
	$result = usePreparedSelectBlade ($query, $qparams);
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
	$result = usePreparedSelectBlade ($query, $qparams);
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
	$result = usePreparedSelectBlade ($query, $qparams);
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
	return usePreparedExecuteBlade
	(
		'INSERT INTO IPv4Allocation (ip, object_id, name, type) VALUES (INET_ATON(?), ?, ?, ?)',
		array ($ip, $object_id, $name, $type)
	);
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
		"inet_aton(?) & (4294967295 >> (32 - mask)) << (32 - mask) = ip " .
		"and mask < ? " .
		'order by mask desc limit 1';
	$result = usePreparedSelectBlade ($query, array ($dottedquad, $masklen));
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
		return $row['id'];
	return NULL;
}

function updateIPv4Network_real ($id = 0, $name = '', $comment = '')
{
	return usePreparedExecuteBlade ('UPDATE IPv4Network SET name = ?, comment = ? WHERE id = ?', array ($name, $comment, $id));
}

// This function is actually used not only to update, but also to create records,
// that's why ON DUPLICATE KEY UPDATE was replaced by DELETE-INSERT pair
// (MySQL 4.0 workaround).
function updateAddress ($ip = 0, $name = '', $reserved = 'no')
{
	usePreparedExecuteBlade ('DELETE FROM IPv4Address WHERE ip = INET_ATON(?)', array ($ip));
	// INSERT may appear not necessary.
	if ($name == '' and $reserved == 'no')
		return '';
	$ret = usePreparedExecuteBlade
	(
		'INSERT INTO IPv4Address (name, reserved, ip) VALUES (?, ?, INET_ATON(?))',
		array ($name, $reserved, $ip)
	);
	return $ret !== FALSE ? '' : (__FUNCTION__ . 'query failed');
}

function updateBond ($ip='', $object_id=0, $name='', $type='')
{
	return usePreparedExecuteBlade
	(
		'UPDATE IPv4Allocation SET name=?, type=? WHERE ip=INET_ATON(?) AND object_id=?',
		array ($name, $type, $ip, $object_id)
	);
}

function unbindIpFromObject ($ip='', $object_id=0)
{
	return usePreparedExecuteBlade
	(
		'DELETE FROM IPv4Allocation WHERE ip=INET_ATON(?) AND object_id=?',
		array ($ip, $object_id)
	);
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
	$qparams = array();
	foreach (explode (' ', $terms) as $term)
	{
		$query .= $or . "name like ?";
		$or = ' or ';
		$qparams[] = "%${term}%";
	}
	$result = usePreparedSelectBlade ($query, $qparams);
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
	$qparams = array();
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
			$query .= $pfx . "binary ${scolumn} = ?";
			$qparams[] = $term;
			break;
		case 1:
			$query .= $pfx . "${scolumn} = ?";
			$qparams[] = $term;
			break;
		default:
			$query .= $pfx . "${scolumn} like ?";
			$qparams[] = "%${term}%";
			break;
		}
		$pfx = ' or ';
	}
	if ($ocolumn != '')
		$query .= " order by ${ocolumn}";
	$result = usePreparedSelectBlade ($query, $qparams);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[] = $row;
	return $ret;
}

function getObjectSearchResults ($what)
{
	$ret = array();
	global $searchfunc;
	foreach ($searchfunc['object'] as $method => $func)
		foreach ($func ($what) as $objRecord)
		{
			$ret[$objRecord['id']]['id'] = $objRecord['id'];
			$ret[$objRecord['id']][$method] = $objRecord[$method];
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

// search in port "reservation comment", "label" and "L2 address" columns
function getPortSearchResults ($what)
{
	$ret = array();
	$ports = getSearchResultByField
	(
		'Port',
		array ('object_id', 'id', 'reservation_comment'),
		'reservation_comment',
		$what,
		'object_id',
		0
	);
	foreach ($ports as $port)
	{
		$ret[$port['object_id']]['id'] = $port['object_id'];
		$ret[$port['object_id']]['by_port'][$port['id']] = $port['reservation_comment'];
	}
	$ports = getSearchResultByField
	(
		'Port',
		array ('object_id', 'id', 'label'),
		'label',
		$what,
		'object_id',
		0
	);
	foreach ($ports as $port)
	{
		$ret[$port['object_id']]['id'] = $port['object_id'];
		$ret[$port['object_id']]['by_port'][$port['id']] = $port['label'];
	}
	if (NULL === ($db_l2address = l2addressForDatabase ($what)))
		return $ret;
	$ports = getSearchResultByField
	(
		'Port',
		array ('object_id', 'id', 'l2address'),
		'l2address',
		$db_l2address,
		'object_id',
		2
	);
	foreach ($ports as $port)
	{
		$ret[$port['object_id']]['id'] = $port['object_id'];
		$ret[$port['object_id']]['by_port'][$port['id']] = $port['l2address'];
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
function getPortIDs ($object_id, $port_name)
{
	$ret = array();
	$result = usePreparedSelectBlade ('SELECT id FROM Port WHERE object_id = ? AND name = ?', array ($object_id, $port_name));
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[] = $row['id'];
	return $ret;
}

// Search in "FQDN" attribute only, and return object ID, when there is exactly
// one result found (and NULL in any other case).
function searchByMgmtHostname ($string)
{
	$result = usePreparedSelectBlade ('SELECT object_id FROM AttributeValue WHERE attr_id = 3 AND string_value = ? LIMIT 2', array ($string));
	$rows = $result->fetchAll (PDO::FETCH_NUM);
	if (count ($rows) != 1)
		return NULL;
	return $rows[0][0];
}

function commitCreateUserAccount ($username, $realname, $password)
{
	return usePreparedInsertBlade
	(
		'UserAccount',
		array
		(
			'user_name' => $username,
			'user_realname' => $realname,
			'user_password_hash' => $password,
		)
	);
}

function commitUpdateUserAccount ($id, $new_username, $new_realname, $new_password)
{
	return usePreparedExecuteBlade
	(
		'UPDATE UserAccount SET user_name=?, user_realname=?, user_password_hash=? ' .
		'WHERE user_id=?',
		array ($new_username, $new_realname, $new_password, $id)
	);
}

// This function returns an array of all port type pairs from PortCompat table.
function getPortOIFCompat ()
{
	$query =
		"select type1, type2, d1.dict_value as type1name, d2.dict_value as type2name from " .
		"PortCompat as pc inner join Dictionary as d1 on pc.type1 = d1.dict_key " .
		"inner join Dictionary as d2 on pc.type2 = d2.dict_key " .
		'ORDER BY type1name, type2name';
	$result = usePreparedSelectBlade ($query);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// Add a pair to the PortCompat table.
function commitSupplementPOIFC ($type1, $type2)
{
	if ($type1 <= 0)
		throw new InvalidArgException ('type1', $type1);
	if ($type2 <= 0)
		throw new InvalidArgException ('type2', $type2);
	return usePreparedInsertBlade
	(
		'PortCompat',
		array ('type1' => $type1, 'type2' => $type2)
	);
}

// Remove a pair from the PortCompat table.
function commitReducePOIFC ($type1, $type2)
{
	return usePreparedDeleteBlade ('PortCompat', array ('type1' => $type1, 'type2' => $type2));
}

function getDictStats ()
{
	$stock_chapters = array (1, 2, 11, 12, 13, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28);
	$result = usePreparedSelectBlade
	(
		"select Chapter.id as chapter_no, Chapter.name as chapter_name, count(dict_key) as wc from " .
		"Chapter left join Dictionary on Chapter.id = Dictionary.chapter_id group by Chapter.id"
	);
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
	$result = usePreparedSelectBlade
	(
		"select count(object_id) as attrc from RackObject as ro left join " .
		"AttributeValue as av on ro.id = av.object_id group by ro.id"
	);
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
		$result = usePreparedSelectBlade ($item['q']);
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
		$result = usePreparedSelectBlade ($item['q']);
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
	return usePreparedExecuteBlade
	(
		'UPDATE Dictionary SET dict_value=? WHERE chapter_id=? AND dict_key=?',
		array ($dict_value, $chapter_no, $dict_key)
	);
}

function commitSupplementDictionary ($chapter_no = 0, $dict_value = '')
{
	if ($chapter_no <= 0)
		throw new InvalidArgException ('$chapter_no', $chapter_no);
	if (!strlen ($dict_value))
		throw new InvalidArgException ('$dict_value', $dict_value);
	return usePreparedInsertBlade
	(
		'Dictionary',
		array ('chapter_id' => $chapter_no, 'dict_value' => $dict_value)
	);
}

// Technically dict_key is enough to delete, but including chapter_id into
// WHERE clause makes sure, that the action actually happends for the same
// chapter, which authorization was granted for.
function commitReduceDictionary ($chapter_no = 0, $dict_key = 0)
{
	return usePreparedDeleteBlade ('Dictionary', array ('chapter_id' => $chapter_no, 'dict_key' => $dict_key));
}

function commitAddChapter ($chapter_name = '')
{
	if (!strlen ($chapter_name))
		throw new InvalidArgException ('$chapter_name', $chapter_name);
	return usePreparedInsertBlade
	(
		'Chapter',
		array ('name' => $chapter_name)
	);
}

function commitUpdateChapter ($chapter_no = 0, $chapter_name = '')
{
	if (!strlen ($chapter_name))
		throw new InvalidArgException ('$chapter_name', $chapter_name);
	return usePreparedExecuteBlade
	(
		'UPDATE Chapter SET name=? WHERE id=? AND sticky="no"',
		array ($chapter_name, $chapter_no)
	);
}

function commitDeleteChapter ($chapter_no = 0)
{
	return usePreparedDeleteBlade ('Chapter', array ('id' => $chapter_no, 'sticky' => 'no'));
}

// This is a dictionary accessor. We perform link rendering, so the user sees
// nice <select> drop-downs.
function readChapter ($chapter_id = 0, $style = '')
{
	$result = usePreparedSelectBlade
	(
		"select dict_key, dict_value from Dictionary " .
		"where chapter_id = ?",
		array ($chapter_id)
	);
	$chapter = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$chapter[$row['dict_key']] = $style == '' ? $row['dict_value'] : parseWikiLink ($row['dict_value'], $style);
	$result->closeCursor();
	// SQL ORDER BY had no sense, because we need to sort after link rendering, not before.
	asort ($chapter);
	return $chapter;
}

// Return refcounters for all given keys of the given chapter.
function getChapterRefc ($chapter_id, $keylist)
{
	$ret = array();
	foreach ($keylist as $key)
		$ret[$key] = 0;
	switch ($chapter_id)
	{
	case CHAP_OBJTYPE:
		// RackObjectType chapter is referenced by AttributeMap and RackObject tables
		$query = 'select dict_key as uint_value, (select count(*) from AttributeMap where objtype_id = dict_key) + ' .
			"(select count(*) from RackObject where objtype_id = dict_key) as refcnt from Dictionary where chapter_id = ?";
		break;
	case CHAP_PORTTYPE:
		// PortOuterInterface chapter is referenced by PortCompat, PortInterfaceCompat and Port tables
		$query = 'select dict_key as uint_value, (select count(*) from PortCompat where type1 = dict_key or type2 = dict_key) + ' .
			'(select count(*) from Port where type = dict_key) + (SELECT COUNT(*) FROM PortInterfaceCompat WHERE oif_id = dict_key) as refcnt ' .
			"from Dictionary where chapter_id = ?";
		break;
	default:
		// Find the list of all assigned values of dictionary-addressed attributes, each with
		// chapter/word keyed reference counters.
		$query = "select uint_value, count(object_id) as refcnt " .
			"from Attribute as a inner join AttributeMap as am on a.id = am.attr_id " .
			"inner join AttributeValue as av on a.id = av.attr_id " .
			"inner join Dictionary as d on am.chapter_id = d.chapter_id and av.uint_value = d.dict_key " .
			"where a.type = 'dict' and am.chapter_id = ? group by uint_value";
		break;
	}
	$result = usePreparedSelectBlade ($query, array ($chapter_id));
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['uint_value']] = $row['refcnt'];
	return $ret;
}

// Return a list of all stickers with sticker map applied. Each sticker records will
// list all its ways on the map with refcnt set.
function getAttrMap ()
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, type, name, chapter_id, (SELECT dict_value FROM Dictionary WHERE dict_key = objtype_id) '.
		'AS objtype_name, (SELECT name FROM Chapter WHERE id = chapter_id) ' .
		'AS chapter_name, objtype_id, (SELECT COUNT(object_id) FROM AttributeValue AS av INNER JOIN RackObject AS ro ' .
		'ON av.object_id = ro.id WHERE av.attr_id = Attribute.id AND ro.objtype_id = AttributeMap.objtype_id) ' .
		'AS refcnt FROM Attribute LEFT JOIN AttributeMap ON id = attr_id ORDER BY Attribute.name, objtype_name'
	);
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
	return usePreparedExecuteBlade ('UPDATE Attribute SET name=? WHERE id=?', array ($attr_name, $attr_id));
}

function commitAddAttribute ($attr_name = '', $attr_type = '')
{
	if (!strlen ($attr_name))
		throw new InvalidArgException ('$attr_name', $attr_name);
	switch ($attr_type)
	{
		case 'uint':
		case 'float':
		case 'string':
		case 'dict':
			break;
		default:
			throw new InvalidArgException ('$attr_type', $attr_type, 'Attribute type not supported');
	}
	return usePreparedInsertBlade
	(
		'Attribute',
		array ('name' => $attr_name, 'type' => $attr_type)
	);
}

function commitDeleteAttribute ($attr_id = 0)
{
	return usePreparedDeleteBlade ('Attribute', array ('id' => $attr_id));
}

// FIXME: don't store garbage in chapter_no for non-dictionary types.
function commitSupplementAttrMap ($attr_id = 0, $objtype_id = 0, $chapter_no = 0)
{
	if ($attr_id <= 0)
		throw new InvalidArgException ('$attr_id', $attr_id);
	if ($objtype_id <= 0)
		throw new InvalidArgException ('$objtype_id', $objtype_id);

	return usePreparedInsertBlade
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
	return usePreparedDeleteBlade ('AttributeMap', array ('attr_id' => $attr_id, 'objtype_id' => $objtype_id));
}

// This function returns all optional attributes for requested object
// as an array of records. NULL is returned on error and empty array
// is returned, if there are no attributes found.
function getAttrValues ($object_id)
{
	$ret = array();
	$result = usePreparedSelectBlade
	(
		"select A.id as attr_id, A.name as attr_name, A.type as attr_type, C.name as chapter_name, " .
		"C.id as chapter_id, AV.uint_value, AV.float_value, AV.string_value, D.dict_value from " .
		"RackObject as RO inner join AttributeMap as AM on RO.objtype_id = AM.objtype_id " .
		"inner join Attribute as A on AM.attr_id = A.id " .
		"left join AttributeValue as AV on AV.attr_id = AM.attr_id and AV.object_id = RO.id " .
		"left join Dictionary as D on D.dict_key = AV.uint_value and AM.chapter_id = D.chapter_id " .
		"left join Chapter as C on AM.chapter_id = C.id " .
		"where RO.id = ? order by A.name, A.type",
		array ($object_id)
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$record = array();
		$record['id'] = $row['attr_id'];
		$record['name'] = $row['attr_name'];
		$record['type'] = $row['attr_type'];
		switch ($row['attr_type'])
		{
			case 'dict':
				$record['chapter_id'] = $row['chapter_id'];
				$record['chapter_name'] = $row['chapter_name'];
				$record['key'] = $row['uint_value'];
				// fall through
			case 'uint':
			case 'float':
			case 'string':
				$record['value'] = $row[$row['attr_type'] . '_value'];
				$record['o_value'] = parseWikiLink ($record['value'], 'o');
				$record['a_value'] = parseWikiLink ($record['value'], 'a');
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
	return usePreparedDeleteBlade ('AttributeValue', array ('object_id' => $object_id, 'attr_id' => $attr_id));
}

function commitUpdateAttrValue ($object_id = 0, $attr_id = 0, $value = '')
{
	if ($object_id <= 0)
		throw new InvalidArgException ('$objtype_id', $objtype_id);
	if ($attr_id <= 0)
		throw new InvalidArgException ('$attr_id', $attr_id);
	if (!strlen ($value))
		return commitResetAttrValue ($object_id, $attr_id);
	$result = usePreparedSelectBlade ('select type as attr_type from Attribute where id = ?', array ($attr_id));
	$row = $result->fetch (PDO::FETCH_NUM);
	$attr_type = $row[0];
	unset ($result);
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
			throw new InvalidArgException ('$attr_type', $attr_type, 'Unknown attribute type found in object #'.$object_id.', attribute #'.$attr_id);
	}
	usePreparedDeleteBlade ('AttributeValue', array ('object_id' => $object_id, 'attr_id' => $attr_id));
	// We know $value isn't empty here.
	usePreparedInsertBlade
	(
		'AttributeValue',
		array
		(
			$column => $value,
			'object_id' => $object_id,
			'attr_id' => $attr_id,
		)
	);
	return TRUE;
}

function commitUseupPort ($port_id = 0)
{
	return usePreparedExecuteBlade ('UPDATE Port SET reservation_comment=NULL WHERE id=?', array ($port_id));
}

function convertPDOException ($e)
{
	if ($e->getCode() != 23000)
		return $e;
	switch ($e->errorInfo[1])
	{
	case 1062:
		$text = 'such record already exists';
		break;
	case 1451:
	case 1452:
		$text = 'foreign key violation';
		break;
	default:
		$text = 'unknown error code ' . $e->errorInfo[1];
		break;
	}
	return new Exception ($text, E_DB_CONSTRAINT);
}

// This is a swiss-knife blade to insert a record into a table.
// The first argument is table name.
// The second argument is an array of "name" => "value" pairs.
function usePreparedInsertBlade ($tablename, $columns)
{
	global $dbxlink;
	$query = "INSERT INTO ${tablename} (" . implode (', ', array_keys ($columns));
	$query .= ') VALUES (' . implode (', ', array_fill (0, count ($columns), '?')) . ')';
	// Now the query should be as follows:
	// INSERT INTO table (c1, c2, c3) VALUES (?, ?, ?)
	try
	{
		$prepared = $dbxlink->prepare ($query);
		if (!$prepared->execute (array_values ($columns)))
			return FALSE;
		return $prepared->rowCount() == 1;
	}
	catch (PDOException $e)
	{
		throw convertPDOException ($e);
	}
}

// This swiss-knife blade deletes any number of records from the specified table
// using the specified key names and values.
function usePreparedDeleteBlade ($tablename, $columns, $conjunction = 'AND')
{
	global $dbxlink;
	$conj = '';
	$query = "DELETE FROM ${tablename} WHERE ";
	foreach ($columns as $colname => $colvalue)
	{
		$query .= " ${conj} ${colname}=?";
		$conj = $conjunction;
	}
	try
	{
		$prepared = $dbxlink->prepare ($query);
		if (!$prepared->execute (array_values ($columns)))
			return FALSE;
		return $prepared->rowCount(); // FALSE !== 0
	}
	catch (PDOException $e)
	{
		throw convertPDOException ($e);
	}
}

function usePreparedSelectBlade ($query, $args = array())
{
	global $dbxlink;
	$prepared = $dbxlink->prepare ($query);
	if (!$prepared->execute ($args))
		return FALSE;
	return $prepared;
}

// Prepare and execute the statement with parameters, then return number of
// rows affected (or FALSE in case of an error).
function usePreparedExecuteBlade ($query, $args = array())
{
	global $dbxlink;
	try
	{
		if (!$prepared = $dbxlink->prepare ($query))
			return FALSE;
		if (!$prepared->execute ($args))
			return FALSE;
		return $prepared->rowCount();
	}
	catch (PDOException $e)
	{
		throw convertPDOException ($e);
	}
}

function loadConfigCache ()
{
	$result = usePreparedSelectBlade ('SELECT varname, varvalue, vartype, is_hidden, emptyok, description, is_userdefined FROM Config ORDER BY varname');
	$cache = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$cache[$row['varname']] = $row;
	return $cache;
}

function loadUserConfigCache ($username = NULL)
{
	if (!strlen ($username))
		throw new InvalidArgException ('$username', $username);
	$result = usePreparedSelectBlade ('SELECT varname, varvalue FROM UserConfig WHERE user = ?', array ($username));
	$cache = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$cache[$row['varname']] = $row;
	return $cache;
}

function deleteUserConfigVar ($username = NULL, $varname = NULL)
{
        usePreparedDeleteBlade ('UserConfig', array ('varname' => $varname, 'user' => $username));
}

function storeUserConfigVar ($username = NULL, $varname = NULL, $varvalue = NULL)
{
	if (!strlen ($username))
		throw new InvalidArgException ('$username', $username);
	if (!strlen ($varname))
		throw new InvalidArgException ('$varname', $varname);
	if ($varvalue === NULL)
		throw new InvalidArgException ('$varvalue', $varvalue);
	return usePreparedExecuteBlade
	(
		'REPLACE UserConfig SET varvalue=?, varname=?, user=?',
		array ($varvalue, $varname, $username)
	);
}

// setConfigVar() is expected to perform all necessary filtering
function storeConfigVar ($varname = NULL, $varvalue = NULL)
{
	if ($varvalue === NULL)
		throw new InvalidArgException ('$varvalue', $varvalue);
	return usePreparedExecuteBlade
	(
		'UPDATE Config SET varvalue=? WHERE varname=?',
		array ($varvalue, $varname)
	);
}

// Database version detector. Should behave corretly on any
// working dataset a user might have.
function getDatabaseVersion ()
{
	$result = usePreparedSelectBlade ('SELECT varvalue FROM Config WHERE varname = "DB_VERSION" and vartype = "string"');
	if ($result == NULL)
	{
		global $dbxlink;
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
	$result = usePreparedSelectBlade
	(
		'select vs.id as vsid, inet_ntoa(vip) as vip, vport, proto, vs.name, object_id, ' .
		'lb.rspool_id, pool.name as pool_name, count(rs.id) as rscount ' .
		'from IPv4VS as vs inner join IPv4LB as lb on vs.id = lb.vs_id ' .
		'inner join IPv4RSPool as pool on lb.rspool_id = pool.id ' .
		'left join IPv4RS as rs on rs.rspool_id = lb.rspool_id ' .
		'group by vs.id, object_id order by vs.vip, object_id'
	);
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
	return $ret;
}

function addRStoRSPool ($pool_id = 0, $rsip = '', $rsport = 0, $inservice = 'no', $rsconfig = '')
{
	return usePreparedExecuteBlade
	(
		'INSERT INTO IPv4RS (rsip, rsport, rspool_id, inservice, rsconfig) VALUES (INET_ATON(?), ?, ?, ?, ?)',
		array
		(
			$rsip,
			(!strlen ($rsport) or $rsport === 0) ? NULL : $rsport,
			$pool_id,
			$inservice == 'yes' ? 'yes' : 'no',
			!strlen ($rsconfig) ? NULL : $rsconfig
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
	if (FALSE === usePreparedExecuteBlade
	(
		'INSERT INTO IPv4VS (vip, vport, proto, name, vsconfig, rsconfig) VALUES (INET_ATON(?), ?, ?, ?, ?, ?)',
		array
		(
			$vip,
			$vport,
			$proto,
			!strlen ($name) ? NULL : $name,
			!strlen ($vsconfig) ? NULL : $vsconfig,
			!strlen ($rsconfig) ? NULL : $rsconfig,
		)
	))
		return __FUNCTION__ . ': SQL insertion failed';
	return produceTagsForLastRecord ('ipv4vs', $taglist);
}

function addLBtoRSPool ($pool_id = 0, $object_id = 0, $vs_id = 0, $vsconfig = '', $rsconfig = '')
{
	return usePreparedInsertBlade
	(
		'IPv4LB',
		array
		(
			'object_id' => $object_id,
			'rspool_id' => $pool_id,
			'vs_id' => $vs_id,
			'vsconfig' => (!strlen ($vsconfig) ? NULL : $vsconfig),
			'rsconfig' => (!strlen ($rsconfig) ? NULL : $rsconfig)
		)
	);
}

function commitDeleteRS ($id = 0)
{
	return usePreparedDeleteBlade ('IPv4RS', array ('id' => $id));
}

function commitDeleteVS ($id = 0)
{
	releaseFiles ('ipv4vs', $id);
	return FALSE !== usePreparedDeleteBlade ('IPv4VS', array ('id' => $id)) && FALSE !== destroyTagsForEntity ('ipv4vs', $id);
}

function commitDeleteLB ($object_id = 0, $pool_id = 0, $vs_id = 0)
{
	return usePreparedDeleteBlade ('IPv4LB', array ('object_id' => $object_id, 'rspool_id' => $pool_id, 'vs_id' => $vs_id));
}

function commitUpdateRS ($rsid = 0, $rsip = '', $rsport = 0, $rsconfig = '')
{
	if (long2ip (ip2long ($rsip)) !== $rsip)
		throw new InvalidArgException ('$rsip', $rsip);
	return usePreparedExecuteBlade
	(
		'UPDATE IPv4RS SET rsip=INET_ATON(?), rsport=?, rsconfig=? WHERE id=?',
		array
		(
			$rsip,
			(!strlen ($rsport) or $rsport === 0) ? NULL : $rsport,
			!strlen ($rsconfig) ? NULL : $rsconfig,
			$rsid,
		)
	);
}

function commitUpdateLB ($object_id = 0, $pool_id = 0, $vs_id = 0, $vsconfig = '', $rsconfig = '')
{
	return usePreparedExecuteBlade
	(
		'UPDATE IPv4LB SET vsconfig=?, rsconfig=? WHERE object_id=? AND rspool_id=? AND vs_id=?',
		array
		(
			!strlen ($vsconfig) ? NULL : $vsconfig,
			!strlen ($rsconfig) ? NULL : $rsconfig,
			$object_id,
			$pool_id,
			$vs_id,
		)
	);
}

function commitUpdateVS ($vsid = 0, $vip = '', $vport = 0, $proto = '', $name = '', $vsconfig = '', $rsconfig = '')
{
	if (!strlen ($vip))
		throw new InvalidArgException ('$vip', $vip);
	if ($vport <= 0)
		throw new InvalidArgException ('$vport', $vport);
	if (!strlen ($proto))
		throw new InvalidArgException ('$proto', $proto);
	return usePreparedExecuteBlade
	(
		'UPDATE IPv4VS SET vip=INET_ATON(?), vport=?, proto=?, name=?, vsconfig=?, rsconfig=? WHERE id=?',
		array
		(
			$vip,
			$vport,
			$proto,
			!strlen ($name) ? NULL : $name,
			!strlen ($vsconfig) ? NULL : $vsconfig,
			!strlen ($rsconfig) ? NULL : $rsconfig,
			$vsid,
		)
	);
}

function loadThumbCache ($rack_id = 0)
{
	$ret = NULL;
	$result = usePreparedSelectBlade ("SELECT thumb_data FROM Rack WHERE id = ? AND thumb_data IS NOT NULL", array ($rack_id));
	$row = $result->fetch (PDO::FETCH_ASSOC);
	if ($row)
		$ret = base64_decode ($row['thumb_data']);
	return $ret;
}

function saveThumbCache ($rack_id = 0, $cache = NULL)
{
	if ($cache == NULL)
		throw new InvalidArgException ('$cache', $cache);
	usePreparedExecuteBlade ('UPDATE Rack SET thumb_data=? WHERE id=?', array (base64_encode ($cache), $rack_id));
}

function resetThumbCache ($rack_id = 0)
{
	usePreparedExecuteBlade ('UPDATE Rack SET thumb_data=NULL WHERE id=?', array ($rack_id));
}

// Return the list of attached RS pools for the given object. As long as we have
// the LB-VS UNIQUE in IPv4LB table, it is Ok to key returned records
// by vs_id, because there will be only one RS pool listed for each VS of the
// current object.
function getRSPoolsForObject ($object_id = 0)
{
	$result = usePreparedSelectBlade
	(
		'select vs_id, inet_ntoa(vip) as vip, vport, proto, vs.name, pool.id as pool_id, ' .
		'pool.name as pool_name, count(rsip) as rscount, lb.vsconfig, lb.rsconfig from ' .
		'IPv4LB as lb inner join IPv4RSPool as pool on lb.rspool_id = pool.id ' .
		'inner join IPv4VS as vs on lb.vs_id = vs.id ' .
		'left join IPv4RS as rs on lb.rspool_id = rs.rspool_id ' .
		'where lb.object_id = ? ' .
		'group by lb.rspool_id, lb.vs_id order by vs.vip, vport, proto, pool.name',
		array ($object_id)
	);
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach (array ('vip', 'vport', 'proto', 'name', 'pool_id', 'pool_name', 'rscount', 'vsconfig', 'rsconfig') as $cname)
			$ret[$row['vs_id']][$cname] = $row[$cname];
	return $ret;
}

function commitCreateRSPool ($name = '', $vsconfig = '', $rsconfig = '', $taglist = array())
{
	if (!strlen ($name))
		throw new InvalidArgException ('$name', $name);
	if (!usePreparedInsertBlade
	(
		'IPv4RSPool',
		array
		(
			'name' => (!strlen ($name) ? NULL : $name),
			'vsconfig' => (!strlen ($vsconfig) ? NULL : $vsconfig),
			'rsconfig' => (!strlen ($rsconfig) ? NULL : $rsconfig)
		)
	))
		return __FUNCTION__ . ': SQL insertion failed';
	return produceTagsForLastRecord ('ipv4rspool', $taglist);
}

function commitDeleteRSPool ($pool_id = 0)
{
	releaseFiles ('ipv4rspool', $pool_id);
	return usePreparedDeleteBlade ('IPv4RSPool', array ('id' => $pool_id)) && destroyTagsForEntity ('ipv4rspool', $pool_id);
}

function commitUpdateRSPool ($pool_id = 0, $name = '', $vsconfig = '', $rsconfig = '')
{
	return usePreparedExecuteBlade
	(
		'UPDATE IPv4RSPool SET name=?, vsconfig=?, rsconfig=? WHERE id=?',
		array
		(
			!strlen ($name) ? NULL : $name,
			!strlen ($vsconfig) ? NULL : $vsconfig,
			!strlen ($rsconfig) ? NULL : $rsconfig,
			$pool_id,
		)
	);
}

function getRSList ()
{
	$result = usePreparedSelectBlade
	(
		"select id, inservice, inet_ntoa(rsip) as rsip, rsport, rspool_id, rsconfig " .
		"from IPv4RS order by rspool_id, IPv4RS.rsip, rsport"
	);
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach (array ('inservice', 'rsip', 'rsport', 'rspool_id', 'rsconfig') as $cname)
			$ret[$row['id']][$cname] = $row[$cname];
	return $ret;
}

// Return the list of all currently configured load balancers with their pool count.
function getLBList ()
{
	$result = usePreparedSelectBlade
	(
		"select object_id, count(rspool_id) as poolcount " .
		"from IPv4LB group by object_id order by object_id"
	);
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['object_id']] = $row['poolcount'];
	return $ret;
}

// For the given object return: its vsconfig/rsconfig; the list of RS pools
// attached (each with vsconfig/rsconfig in turn), each with the list of
// virtual services terminating the pool. Each pool also lists all real
// servers with rsconfig.
function getSLBConfig ($object_id)
{
	$ret = array();
	$result = usePreparedSelectBlade
	(
		'select vs_id, inet_ntoa(vip) as vip, vport, proto, vs.name as vs_name, ' .
		'vs.vsconfig as vs_vsconfig, vs.rsconfig as vs_rsconfig, ' .
		'lb.vsconfig as lb_vsconfig, lb.rsconfig as lb_rsconfig, pool.id as pool_id, pool.name as pool_name, ' .
		'pool.vsconfig as pool_vsconfig, pool.rsconfig as pool_rsconfig, ' .
		'rs.id as rs_id, inet_ntoa(rsip) as rsip, rsport, rs.rsconfig as rs_rsconfig from ' .
		'IPv4LB as lb inner join IPv4RSPool as pool on lb.rspool_id = pool.id ' .
		'inner join IPv4VS as vs on lb.vs_id = vs.id ' .
		'inner join IPv4RS as rs on lb.rspool_id = rs.rspool_id ' .
		"where lb.object_id = ? and rs.inservice = 'yes' " .
		"order by vs.vip, vport, proto, pool.name, rs.rsip, rs.rsport",
		array ($object_id)
	);
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
	return $ret;
}

function commitSetInService ($rs_id = 0, $inservice = '')
{
	if (!strlen ($inservice))
		throw new InvalidArgException ('$inservice', $inservice);
	return usePreparedExecuteBlade ('UPDATE IPv4RS SET inservice=? WHERE id=?', array ($inservice, $rs_id));
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
	$result = usePreparedSelectBlade
	(
		"select tt.id, tag from " .
		"TagStorage as ts inner join TagTree as tt on ts.tag_id = tt.id " .
		"where entity_realm = ? and entity_id = ? " .
		"order by tt.tag",
		array ($entity_realm, $entity_id)
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = $row;
	return getExplicitTagsOnly ($ret);
}

// Return a tag chain with all DB tags on it.
function getTagList ()
{
	$ret = array();
	$result = usePreparedSelectBlade
	(
		"select id, parent_id, tag, entity_realm as realm, count(entity_id) as refcnt " .
		"from TagTree left join TagStorage on id = tag_id " .
		"group by id, entity_realm order by tag"
	);
	// Collation index. The resulting rows are ordered according to default collation,
	// which is utf8_general_ci for UTF-8.
	$ci = 0;
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
	return $ret;
}

function commitCreateTag ($tagname = '', $parent_id = 0)
{
	if ($tagname == '')
		throw new InvalidArgException ('tagname', $tagname);
	return usePreparedInsertBlade
	(
		'TagTree',
		array
		(
			'tag' => $tagname,
			'parent_id' => $parent_id
		)
	);
}

function commitDestroyTag ($tagid = 0)
{
	if (usePreparedDeleteBlade ('TagTree', array ('id' => $tagid)))
		return '';
	return 'usePreparedDeleteBlade() failed in ' . __FUNCTION__;
}

function commitUpdateTag ($tag_id, $tag_name, $parent_id)
{
	return usePreparedExecuteBlade
	(
		'UPDATE TagTree SET tag=?, parent_id=? WHERE id=?',
		array
		(
			$tag_name,
			$parent_id == 0 ? NULL : $parent_id,
			$tag_id,
		)
	);
}

// Drop the whole chain stored.
function destroyTagsForEntity ($entity_realm, $entity_id)
{
	return usePreparedDeleteBlade ('TagStorage', array ('entity_realm' => $entity_realm, 'entity_id' => $entity_id));
}

// Drop only one record. This operation doesn't involve retossing other tags, unlike when adding.
// FIXME: this function could be used by 3rd-party scripts, dismiss it at some later point,
// but not now.
function deleteTagForEntity ($entity_realm, $entity_id, $tag_id)
{
	return usePreparedDeleteBlade ('TagStorage', array ('entity_realm' => $entity_realm, 'entity_id' => $entity_id, 'tag_id' => $tag_id));
}

// Push a record into TagStorage unconditionally.
function addTagForEntity ($realm = '', $entity_id, $tag_id)
{
	if (!in_array ($realm, array ('file', 'ipv4net', 'ipv4vs', 'ipv4rspool', 'object', 'rack', 'user')))
		return FALSE;
	return usePreparedInsertBlade
	(
		'TagStorage',
		array
		(
			'entity_realm' => $realm,
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
	$result = usePreparedInsertBlade
	(
		'IPv4Network',
		array
		(
			'ip' => sprintf ('%u', $ipL),
			'mask' => $maskL,
			'name' => $name
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
	releaseFiles ('ipv4net', $id);
	if (FALSE === usePreparedDeleteBlade ('IPv4Network', array ('id' => $id)))
		return __FUNCTION__ . ': SQL query #1 failed';
	if (FALSE === destroyTagsForEntity ('ipv4net', $id))
		return __FUNCTION__ . ': SQL query #2 failed';
	return '';
}

function loadScript ($name)
{
	$result = usePreparedSelectBlade ("select script_text from Script where script_name = ?", array ($name));
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
	return usePreparedExecuteBlade
	(
		'INSERT INTO Script (script_name, script_text) VALUES (?, ?) ' .
		'ON DUPLICATE KEY UPDATE script_text=?',
		array ($name, $text, $text)
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

	$result = usePreparedExecuteBlade
	(
		'INSERT INTO IPv4NAT (object_id, localip, remoteip, localport, remoteport, proto, description) ' .
		'VALUES (?, INET_ATON(?), INET_ATON(?), ?, ?, ?, ?)',
		array
		(
			$object_id,
			$localip,
			$remoteip,
			$localport,
			$remoteport,
			$proto,
			$description,
		)
	);
	return $result !== FALSE ? '' : (__FUNCTION__ . '(): failed to insert the rule');
}

function deletePortForwarding ($object_id, $localip, $localport, $remoteip, $remoteport, $proto)
{
	return usePreparedExecuteBlade
	(
		'DELETE FROM IPv4NAT WHERE object_id=? AND localip=INET_ATON(?) AND ' .
		'remoteip=INET_ATON(?) AND localport=? AND remoteport=? AND proto=?',
		array ($object_id, $localip, $remoteip, $localport, $remoteport, $proto)
	);
}

function updatePortForwarding ($object_id, $localip, $localport, $remoteip, $remoteport, $proto, $description)
{
	return usePreparedExecuteBlade
	(
		'UPDATE IPv4NAT SET description=? WHERE object_id=? AND localip=INET_ATON(?) AND remoteip=INET_ATON(?) ' .
		'AND localport=? AND remoteport=? AND proto=?',
		array ($description, $object_id, $localip, $remoteip, $localport, $remoteport, $proto)
	);
}

function getNATv4ForObject ($object_id)
{
	$ret = array();
	$ret['out'] = array();
	$ret['in'] = array();
	$result = usePreparedSelectBlade
	(
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
		"where object_id=? ".
		"order by localip, localport, proto, remoteip, remoteport",
		array ($object_id)
	);
	$count=0;
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		foreach (array ('proto', 'localport', 'localip', 'remoteport', 'remoteip', 'description', 'local_addr_name', 'remote_addr_name') as $cname)
			$ret['out'][$count][$cname] = $row[$cname];
		$count++;
	}
	unset ($result);

	$result = usePreparedSelectBlade
	(
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
		"where IPv4Allocation.object_id=? ".
		"order by remoteip, remoteport, proto, localip, localport",
		array ($object_id)
	);
	$count=0;
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		foreach (array ('proto', 'localport', 'localip', 'remoteport', 'remoteip', 'object_id', 'object_name', 'description') as $cname)
			$ret['in'][$count][$cname] = $row[$cname];
		$count++;
	}
	return $ret;
}

// Return a list of files, which are not linked to the specified record. This list
// will be used by printSelect().
function getAllUnlinkedFiles ($entity_type = NULL, $entity_id = 0)
{
	$query = usePreparedSelectBlade
	(
		'SELECT id, name FROM File ' .
		'WHERE id NOT IN (SELECT file_id FROM FileLink WHERE entity_type = ? AND entity_id = ?) ' .
		'ORDER BY name, id',
		array ($entity_type, $entity_id)
	);
	$ret=array();
	while ($row = $query->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = $row['name'];
	return $ret;
}

// FIXME: return a standard cell list, so upper layer can iterate over
// it conveniently.
function getFilesOfEntity ($entity_type = NULL, $entity_id = 0)
{
	$query = usePreparedSelectBlade
	(
		'SELECT FileLink.file_id, FileLink.id AS link_id, name, type, size, ctime, mtime, atime, comment ' .
		'FROM FileLink LEFT JOIN File ON FileLink.file_id = File.id ' .
		'WHERE FileLink.entity_type = ? AND FileLink.entity_id = ? ORDER BY name',
		array ($entity_type, $entity_id)
	);
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
	$query = usePreparedSelectBlade
	(
		'SELECT id, name, type, size, ctime, mtime, atime, contents, comment ' .
		'FROM File WHERE id = ?',
		array ($file_id)
	);
	if (($row = $query->fetch (PDO::FETCH_ASSOC)) == NULL)
	{
		showWarning ('Query succeeded, but returned no data', __FUNCTION__);
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
		usePreparedExecuteBlade ('UPDATE File SET atime = ? WHERE id = ?', array (date ('YmdHis'), $file_id));
	}
	return $ret;
}

function getFileLinks ($file_id = 0)
{
	$query = usePreparedSelectBlade
	(
		'SELECT id, file_id, entity_type, entity_id FROM FileLink ' .
		'WHERE file_id = ? ORDER BY entity_type, entity_id',
		array ($file_id)
	);
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
	$result = usePreparedSelectBlade ('SELECT entity_type, COUNT(*) AS count FROM FileLink GROUP BY entity_type');
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		if ($row['count'] > 0)
			$ret["Links in realm '${row['entity_type']}'"] = $row['count'];
	unset ($result);

	// Find number of files without any linkage
	$result = usePreparedSelectBlade
	(
		'SELECT COUNT(*) ' .
		'FROM File ' .
		'WHERE id NOT IN (SELECT file_id FROM FileLink)'
	);
	$ret["Unattached files"] = $result->fetchColumn ();
	unset ($result);

	// Find total number of files
	$result = usePreparedSelectBlade ('SELECT COUNT(*) FROM File');
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
	try
	{
		return $query->execute();
	}
	catch (PDOException $e)
	{
		throw convertPDOException ($e);
	}
}

function commitLinkFile ($file_id, $entity_type, $entity_id)
{
	return usePreparedExecuteBlade
	(
		'INSERT INTO FileLink (file_id, entity_type, entity_id) VALUES (?, ?, ?)',
		array ($file_id, $entity_type, $entity_id)
	);
}

function commitReplaceFile ($file_id = 0, $contents)
{
	global $dbxlink;
	$query = $dbxlink->prepare('UPDATE File SET mtime = NOW(), contents = ?, size = LENGTH(contents) WHERE id = ?');
	$query->bindParam(1, $contents, PDO::PARAM_LOB);
	$query->bindParam(2, $file_id);

	try
	{
		return $query->execute();
	}
	catch (PDOException $e)
	{
		throw convertPDOException ($e);
	}
}

function commitUpdateFile ($file_id = 0, $new_name = '', $new_type = '', $new_comment = '')
{
	if (!strlen ($new_name))
		throw new InvalidArgException ('$new_name', $new_name);
	if (!strlen ($new_type))
		throw new InvalidArgException ('$new_type', $new_type);
	return usePreparedExecuteBlade
	(
		'UPDATE File SET name = ?, type = ?, comment = ? WHERE id = ?',
		array ($new_name, $new_type, $new_comment, $file_id)
	);
}

function commitUnlinkFile ($link_id)
{
	if (usePreparedDeleteBlade ('FileLink', array ('id' => $link_id)) === FALSE)
		return __FUNCTION__ . '(): query failed';
	return '';
}

function commitDeleteFile ($file_id)
{
	destroyTagsForEntity ('file', $file_id);
	if (usePreparedDeleteBlade ('File', array ('id' => $file_id)) === FALSE)
		return __FUNCTION__ . '(): query failed';
	return '';
}

function getChapterList ()
{
	$ret = array();
	$result = usePreparedSelectBlade
	(
		'SELECT id, sticky, name, count(chapter_id) as wordc ' .
		'FROM Chapter LEFT JOIN Dictionary ON Chapter.id = chapter_id ' .
		'GROUP BY id ORDER BY name'
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = $row;
	return $ret;
}

// Return file id by file name.
function findFileByName ($filename)
{
	$query = usePreparedSelectBlade ('SELECT id FROM File WHERE name = ?', array ($filename));
	if (($row = $query->fetch (PDO::FETCH_ASSOC)))
		return $row['id'];

	return NULL;
}

function acquireLDAPCache ($form_username, $password_hash, $expiry = 0)
{
	global $dbxlink;
	$dbxlink->beginTransaction();
	$result = usePreparedSelectBlade
	(
		'SELECT TIMESTAMPDIFF(SECOND, first_success, now()) AS success_age, ' .
		'TIMESTAMPDIFF(SECOND, last_retry, now()) AS retry_age, displayed_name, memberof ' .
		'FROM LDAPCache WHERE presented_username = ? AND successful_hash = ? ' .
		'HAVING success_age < ? FOR UPDATE',
		array ($form_username, $password_hash, $expiry)
	);
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
	return usePreparedExecuteBlade ('UPDATE LDAPCache SET last_retry=NOW() WHERE presented_username=?', array ($form_username));
}

function replaceLDAPCacheRecord ($form_username, $password_hash, $dname, $memberof)
{
	deleteLDAPCacheRecord ($form_username);
	usePreparedInsertBlade ('LDAPCache',
		array
		(
			'presented_username' => $form_username,
			'successful_hash' => $password_hash,
			'displayed_name' => $dname,
			'memberof' => base64_encode (serialize ($memberof)),
		)
	);
}

function deleteLDAPCacheRecord ($form_username)
{
	return usePreparedDeleteBlade ('LDAPCache', array ('presented_username' => $form_username));
}

// Age all records older, than cache_expiry seconds, and all records made in future.
// Calling this function w/o argument purges the whole LDAP cache.
function discardLDAPCache ($maxage = 0)
{
	return usePreparedExecuteBlade ('DELETE from LDAPCache WHERE TIMESTAMPDIFF(SECOND, first_success, NOW()) >= ? or NOW() < first_success', array ($maxage));
}

function getUserIDByUsername ($username)
{
	$result = usePreparedSelectBlade ('SELECT user_id FROM UserAccount WHeRE user_name = ?', array ($username));
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
	$result = usePreparedSelectBlade
	(
		'SELECT COUNT(*) FROM Port WHERE BINARY l2address = ? AND object_id != ?',
		array ($address, $my_object_id)
	);
	$row = $result->fetch (PDO::FETCH_NUM);
	return $row[0] != 0;
}

function getPortInterfaceCompat()
{
	$result = usePreparedSelectBlade
	(
		'SELECT iif_id, iif_name, oif_id, dict_value AS oif_name ' .
		'FROM PortInterfaceCompat INNER JOIN PortInnerInterface ON id = iif_id ' .
		'INNER JOIN Dictionary ON dict_key = oif_id ' .
		'ORDER BY iif_name, oif_name'
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// Return a set of options for a plain SELECT. These options include the current
// OIF of the given port and all OIFs of its permanent IIF.
function getExistingPortTypeOptions ($port_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT oif_id, dict_value AS oif_name ' .
		'FROM PortInterfaceCompat INNER JOIN Dictionary ON oif_id = dict_key ' .
		'WHERE iif_id = (SELECT iif_id FROM Port WHERE id = ?) ' .
		'ORDER BY oif_name',
		array ($port_id)
	);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['oif_id']] = $row['oif_name'];
	return $ret;
}

function getPortIIFOptions()
{
	$ret = array();
	$result = usePreparedSelectBlade ('SELECT id, iif_name FROM PortInnerInterface ORDER BY iif_name');
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = $row['iif_name'];
	return $ret;
}

function commitSupplementPIC ($iif_id, $oif_id)
{
	return usePreparedInsertBlade
	(
		'PortInterfaceCompat',
		array ('iif_id' => $iif_id, 'oif_id' => $oif_id)
	);
}

function commitReducePIC ($iif_id, $oif_id)
{
	return usePreparedDeleteBlade ('PortInterfaceCompat', array ('iif_id' => $iif_id, 'oif_id' => $oif_id));
}

function getPortIIFStats ($args)
{
	$result = usePreparedSelectBlade
	(
		'SELECT dict_value AS title, COUNT(id) AS max, ' .
		'COUNT(reservation_comment) + ' .
		'SUM((SELECT COUNT(*) FROM Link WHERE id IN (porta, portb))) AS current ' .
		'FROM Port INNER JOIN Dictionary ON type = dict_key ' .
		'WHERE iif_id = ? GROUP BY type',
		array (current ($args))
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function getPortInfo ($port_id)
{
	$result = usePreparedSelectBlade
	(
		"SELECT object_id, name, iif_id, type AS oif_id, l2address, ".
		"(SELECT dict_value FROM Dictionary WHERE dict_key = type) AS oif_name, " .
		"(SELECT COUNT(*) FROM Link WHERE id IN (porta, portb)) AS linked, " .
		"(SELECT iif_name FROM PortInnerInterface WHERE id = iif_id) AS iif_name " .
		"FROM Port WHERE id = ?",
		array ($port_id)
	);
	return $result->fetch (PDO::FETCH_ASSOC);
}

function getVLANDomainStats ()
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, description, ' .
		'(SELECT COUNT(vlan_id) FROM VLANDescription WHERE domain_id = id) AS vlanc, ' .
		'(SELECT COUNT(ipv4net_id) FROM VLANIPv4 WHERE domain_id = id) AS ipv4netc, ' .
		'(SELECT COUNT(object_id) FROM VLANSwitch WHERE domain_id = id) AS switchc, ' .
		'(SELECT COUNT(port_name) FROM VLANSwitch AS VS INNER JOIN PortVLANMode AS PVM ON VS.object_id = PVM.object_id WHERE domain_id = id) AS portc ' .
		'FROM VLANDomain ORDER BY description'
	);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = $row;
	return $ret;
}

function getVLANDomainOptions()
{
	$result = usePreparedSelectBlade ('SELECT id, description FROM VLANDomain ORDER BY description');
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = $row['description'];
	return $ret;
}

function getVLANDomain ($vdid)
{
	$result = usePreparedSelectBlade ('SELECT id, description FROM VLANDomain WHERE id = ?', array ($vdid));
	if (!$ret = $result->fetch (PDO::FETCH_ASSOC))
		throw new EntityNotFoundException ('VLAN domain', $vdid);
	unset ($result);
	$ret['vlanlist'] = getDomainVLANs ($vdid);
	$ret['switchlist'] = array();
	$result = usePreparedSelectBlade
	(
		'SELECT object_id, template_id, last_errno, out_of_sync, ' .
		'TIMESTAMPDIFF(SECOND, last_change, NOW()) AS age_seconds ' .
		'FROM VLANSwitch WHERE domain_id = ? ORDER BY object_id',
		array ($vdid)
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret['switchlist'][$row['object_id']] = $row;
	return $ret;
}

function getDomainVLANs ($vdom_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT vlan_id, vlan_type, vlan_descr, ' .
		'(SELECT COUNT(ipv4net_id) FROM VLANIPv4 WHERE domain_id = ? AND vlan_id = VD.vlan_id) AS netc, ' .
		'(SELECT COUNT(port_name) FROM VLANSwitch AS VS INNER JOIN PortAllowedVLAN AS PAV ' .
		'ON VS.object_id = PAV.object_id WHERE domain_id = ? AND PAV.vlan_id = VD.vlan_id GROUP BY PAV.vlan_id) AS portc ' .
		'FROM VLANDescription AS VD ' .
		'WHERE domain_id = ? ' .
		'ORDER BY vlan_id',
		array ($vdom_id, $vdom_id, $vdom_id)
	);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['vlan_id']] = $row;
	return $ret;
}

function commitReduceVLANDescription ($vdom_id, $vlan_id)
{
	return usePreparedDeleteBlade
	(
		'VLANDescription',
		array
		(
			'domain_id' => $vdom_id,
			'vlan_id' => $vlan_id,
		)
	);
}

function commitUpdateVLANDescription ($vdom_id, $vlan_id, $vlan_type, $vlan_descr)
{
	return usePreparedExecuteBlade
	(
		'UPDATE VLANDescription SET vlan_descr=?, vlan_type=? WHERE domain_id=? AND vlan_id=?',
		array
		(
			!mb_strlen ($vlan_descr) ? NULL : $vlan_descr,
			$vlan_type,
			$vdom_id,
			$vlan_id,
		)
	);
}

function commitUpdateVLANDomain ($vdom_id, $vdom_descr)
{
	return usePreparedExecuteBlade
	(
		'UPDATE VLANDomain SET description=? WHERE id=?',
		array ($vdom_descr, $vdom_id)
	);
}

function getVLANSwitches()
{
	$ret = array();
	$result = usePreparedSelectBlade ('SELECT object_id FROM VLANSwitch');
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[] = $row['object_id'];
	return $ret;
}

function getVLANSwitchInfo ($object_id, $extrasql = '')
{
	$result = usePreparedSelectBlade
	(
		'SELECT object_id, domain_id, template_id, mutex_rev, out_of_sync, last_errno, ' .
		'TIMESTAMPDIFF(SECOND, last_push_started, last_push_finished) AS last_push_lasted, ' .
		'SEC_TO_TIME(TIMESTAMPDIFF(SECOND, last_change, NOW())) AS last_change_age, ' .
		'TIMESTAMPDIFF(SECOND, last_change, NOW()) AS last_change_age_seconds, ' .
		'SEC_TO_TIME(TIMESTAMPDIFF(SECOND, last_error_ts, NOW())) AS last_error_age, ' .
		'TIMESTAMPDIFF(SECOND, last_error_ts, NOW()) AS last_error_age_seconds, ' .
		'SEC_TO_TIME(TIMESTAMPDIFF(SECOND, last_push_finished, NOW())) AS last_push_age, ' .
		'last_change, last_push_finished, last_error_ts ' .
		'FROM VLANSwitch WHERE object_id = ? ' . $extrasql,
		array ($object_id)
	);
	if ($result and $row = $result->fetch (PDO::FETCH_ASSOC))
		return $row;
	return NULL;
}

function getStored8021QConfig ($object_id, $instance = 'desired')
{
	global $tablemap_8021q;
	if (!array_key_exists ($instance, $tablemap_8021q))
		throw new InvalidArgException ('instance', $instance);
	$ret = array();
	$result = usePreparedSelectBlade
	(
		'SELECT port_name, vlan_mode FROM ' . $tablemap_8021q[$instance]['pvm'] . ' WHERE object_id = ?',
		array ($object_id)
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['port_name']] = array
		(
			'mode' => $row['vlan_mode'],
			'allowed' => array(),
			'native' => 0,
		);
	unset ($result);
	$result = usePreparedSelectBlade
	(
		'SELECT port_name, vlan_id FROM ' . $tablemap_8021q[$instance]['pav'] . ' WHERE object_id = ?',
		array ($object_id)
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['port_name']]['allowed'][] = $row['vlan_id'];
	unset ($result);
	$result = usePreparedSelectBlade
	(
		'SELECT port_name, vlan_id FROM ' . $tablemap_8021q[$instance]['pnv'] . ' WHERE object_id = ?',
		array ($object_id)
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['port_name']]['native'] = $row['vlan_id'];
	return $ret;
}

function getVLANInfo ($vlan_ck)
{
	list ($vdom_id, $vlan_id) = decodeVLANCK ($vlan_ck);
	$query = 'SELECT domain_id, vlan_id, vlan_type AS vlan_prop, vlan_descr, ' .
		'(SELECT description FROM VLANDomain WHERE id = domain_id) AS domain_descr ' .
		'FROM VLANDescription WHERE domain_id = ? AND vlan_id = ?';
	$result = usePreparedSelectBlade ($query, array ($vdom_id, $vlan_id));
	if (NULL == ($ret = $result->fetch (PDO::FETCH_ASSOC)))
		throw new EntityNotFoundException ('VLAN', $vlan_ck);
	$ret['vlan_ck'] = $vlan_ck;
	$ret['ipv4nets'] = array();
	unset ($result);
	$query = 'SELECT ipv4net_id FROM VLANIPv4 WHERE domain_id = ? AND vlan_id = ? ORDER BY ipv4net_id';
	$result = usePreparedSelectBlade ($query, array ($vdom_id, $vlan_id));
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret['ipv4nets'][] = $row['ipv4net_id'];
	return $ret;
}

// return list of network IDs, which are not bound to the given VLAN domain
function getVLANIPv4Options ($except_vdid)
{
	$ret = array();
	$prepared = usePreparedSelectBlade
	(
		'SELECT id FROM IPv4Network WHERE id NOT IN ' .
		'(SELECT ipv4net_id FROM VLANIPv4 WHERE domain_id = ?)' .
		'ORDER BY ip, mask',
		array ($except_vdid)
	);
	while ($row = $prepared->fetch (PDO::FETCH_ASSOC))
		$ret[] = $row['id'];
	return $ret;
}

function commitSupplementVLANIPv4 ($vlan_ck, $ipv4net_id)
{
	list ($vdom_id, $vlan_id) = decodeVLANCK ($vlan_ck);
	return usePreparedInsertBlade
	(
		'VLANIPv4',
		array
		(
			'domain_id' => $vdom_id,
			'vlan_id' => $vlan_id,
			'ipv4net_id' => $ipv4net_id,
		)
	);
}

function commitReduceVLANIPv4 ($vlan_ck, $ipv4net_id)
{
	list ($vdom_id, $vlan_id) = decodeVLANCK ($vlan_ck);
	return usePreparedDeleteBlade
	(
		'VLANIPv4',
		array
		(
			'domain_id' => $vdom_id,
			'vlan_id' => $vlan_id,
			'ipv4net_id' => $ipv4net_id,
		)
	);
}

// Return a list of switches, which have specific VLAN configured on
// any port (each switch with the list of such ports).
function getVLANConfiguredPorts ($vlan_ck)
{
	$result = usePreparedSelectBlade
	(
		'SELECT PAV.object_id, PAV.port_name ' .
		'FROM PortAllowedVLAN AS PAV ' .
		'INNER JOIN VLANSwitch AS VS ON PAV.object_id = VS.object_id ' .
		'WHERE domain_id = ? AND vlan_id = ? ' .
		'ORDER BY PAV.object_id, PAV.port_name',
		decodeVLANCK ($vlan_ck)
	);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['object_id']][] = $row['port_name'];
	return $ret;
}

function add8021QPort ($object_id, $port_name, $port)
{
	global $tablemap_8021q;
	if
	(
		!usePreparedInsertBlade
		(
			$tablemap_8021q['cached']['pvm'],
			array ('object_id' => $object_id, 'port_name' => $port_name, 'vlan_mode' => $port['mode'])
		) or
		!usePreparedInsertBlade
		(
			$tablemap_8021q['desired']['pvm'],
			array ('object_id' => $object_id, 'port_name' => $port_name, 'vlan_mode' => $port['mode'])
		)
	)
		throw new Exception ('', E_DB_WRITE_FAILED);
	upd8021QPort ('cached', $object_id, $port_name, $port);
	upd8021QPort ('desired', $object_id, $port_name, $port);
	return 1;
}

function del8021QPort ($object_id, $port_name)
{
	// rely on ON DELETE CASCADE for PortAllowedVLAN and PortNativeVLAN
	global $tablemap_8021q;
	if
	(	FALSE === usePreparedDeleteBlade
		(
			$tablemap_8021q['desired']['pvm'],
			array ('object_id' => $object_id, 'port_name' => $port_name)
		) or
		FALSE === usePreparedDeleteBlade
		(
			$tablemap_8021q['cached']['pvm'],
			array ('object_id' => $object_id, 'port_name' => $port_name)
		)
	)
		throw new Exception ('', E_DB_WRITE_FAILED);
	return 1;
}

function upd8021QPort ($instance = 'desired', $object_id, $port_name, $port)
{
	global $tablemap_8021q;
	if (!array_key_exists ($instance, $tablemap_8021q))
		throw new InvalidArgException ('instance', $instance);
	// Replace current port configuration with the provided one. If the new
	// native VLAN ID doesn't belong to the allowed list, don't issue
	// INSERT query, which would always trigger an FK exception.
	// This function indicates an error, but doesn't revert it, so it is
	// assummed, that the calling function performs necessary transaction wrapping.
	// A record on a port with none VLANs allowed makes no sense regardless of port mode.
	if ($port['mode'] != 'trunk' and !count ($port['allowed']))
		return 0;
	usePreparedExecuteBlade
	(
		'UPDATE ' . $tablemap_8021q[$instance]['pvm'] . ' SET vlan_mode=? WHERE object_id=? AND port_name=?',
		array ($port['mode'], $object_id, $port_name)
	);
	if (FALSE === usePreparedDeleteBlade ($tablemap_8021q[$instance]['pav'], array ('object_id' => $object_id, 'port_name' => $port_name)))
		throw new Exception ('', E_DB_WRITE_FAILED);
	// FIXME: The goal is to INSERT as many rows as there are values in 'allowed' list
	// without wrapping each row with own INSERT (otherwise the SQL connection
	// instantly becomes the bottleneck).
	foreach ($port['allowed'] as $vlan_id)
		if (!usePreparedInsertBlade ($tablemap_8021q[$instance]['pav'], array ('object_id' => $object_id, 'port_name' => $port_name, 'vlan_id' => $vlan_id)))
			throw new Exception ('', E_DB_WRITE_FAILED);
	if
	(
		$port['native'] and
		in_array ($port['native'], $port['allowed']) and
		!usePreparedInsertBlade ($tablemap_8021q[$instance]['pnv'], array ('object_id' => $object_id, 'port_name' => $port_name, 'vlan_id' => $port['native']))
	)
		throw new Exception ('', E_DB_WRITE_FAILED);
	return 1;
}

function replace8021QPorts ($instance = 'desired', $object_id, $before, $changes)
{
	$done = 0;
	foreach ($changes as $port_name => $port)
		if 
		(
			!array_key_exists ($port_name, $before) or
			!same8021QConfigs ($port, $before[$port_name])
		)
			$done += upd8021QPort ($instance, $object_id, $port_name, $port);
	return $done;
}

function getVSTStats()
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, max_local_vlans, description, ' .
		'(SELECT COUNT(object_id) FROM VLANSwitch WHERE template_id = id) AS switchc, ' .
		'(SELECT COUNT(rule_no) FROM VLANSTRule WHERE vst_id = id) AS rulec ' .
		'FROM VLANSwitchTemplate ORDER BY description'
	);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = $row;
	return $ret;
}

function getVSTOptions()
{
	$result = usePreparedSelectBlade ('SELECT id, description FROM VLANSwitchTemplate ORDER BY description');
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = $row['description'];
	return $ret;
}

function getVLANSwitchTemplate ($vst_id)
{
	$result = usePreparedSelectBlade ('SELECT id, max_local_vlans, description FROM VLANSwitchTemplate WHERE id = ?', array ($vst_id));
	if (!($ret = $result->fetch (PDO::FETCH_ASSOC)))
		throw new EntityNotFoundException ('vst', $vst_id);
	unset ($result);
	$ret['rules'] = array();
	$ret['switches'] = array();
	$result = usePreparedSelectBlade
	(
		'SELECT rule_no, port_pcre, port_role, wrt_vlans, description ' .
		'FROM VLANSTRule WHERE vst_id = ? ORDER BY rule_no',
		array ($vst_id)
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret['rules'][$row['rule_no']] = $row;
	unset ($result);
	$result = usePreparedSelectBlade ('SELECT object_id, domain_id FROM VLANSwitch WHERE template_id = ?', array ($vst_id));
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret['switches'][$row['object_id']] = $row;
	return $ret;
}

function commitUpdateVST ($vst_id, $max_local_vlans, $description)
{
	return usePreparedExecuteBlade
	(
		'UPDATE VLANSwitchTemplate SET max_local_vlans=?, description=? WHERE id=?',
		array ($max_local_vlans, $description, $vst_id)
	);
}

function commitUpdateVSTRule ($vst_id, $rule_no, $new_rule_no, $port_pcre, $port_role, $wrt_vlans, $description)
{
	return usePreparedExecuteBlade
	(
		'UPDATE VLANSTRule SET rule_no = ?, port_pcre = ?, port_role = ?, wrt_vlans = ?, description = ? ' .
		'WHERE vst_id = ? AND rule_no = ?',
		array ($new_rule_no, $port_pcre, $port_role, $wrt_vlans, $description, $vst_id, $rule_no)
	);
}

function getIPv4Network8021QBindings ($ipv4net_id)
{
	$prepared = usePreparedSelectBlade
	(
		'SELECT domain_id, vlan_id FROM VLANIPv4 ' .
		'WHERE ipv4net_id = ? ORDER BY domain_id',
		array ($ipv4net_id)
	);
	return $prepared->fetchAll (PDO::FETCH_ASSOC);
}

?>
