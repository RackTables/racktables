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
			'asset_no' => 'asset_no',
			'objtype_id' => 'objtype_id',
			'rack_id' => '(SELECT rack_id FROM RackSpace WHERE object_id = id ORDER BY rack_id ASC LIMIT 1)',
			'has_problems' => 'has_problems',
			'comment' => 'comment',
			'nports' => '(SELECT COUNT(*) FROM Port WHERE object_id = RackObject.id)',
			'8021q_domain_id' => '(SELECT domain_id FROM VLANSwitch WHERE object_id = id LIMIT 1)',
			'8021q_template_id' => '(SELECT template_id FROM VLANSwitch WHERE object_id = id LIMIT 1)',
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
	'ipv6net' => array
	(
		'table' => 'IPv6Network',
		'columns' => array
		(
			'id' => 'id',
			'ip_bin' => 'ip',
			'mask' => 'mask',
			'name' => 'name',
			'comment' => 'comment',
			'parent_id' => '(SELECT id FROM IPv6Network AS subt WHERE IPv6Network.ip >= subt.ip AND IPv6Network.last_ip <= subt.last_ip AND IPv6Network.mask > subt.mask ORDER BY subt.mask DESC limit 1)',
			'vlanc' => '(SELECT COUNT(*) FROM VLANIPv6 WHERE ipv6net_id = id)',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('IPv6Network.ip', 'IPv6Network.mask'),
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
			'asset_no' => 'asset_no',
			'has_problems' => 'has_problems',
			'comment' => 'comment',
			'row_id' => 'row_id',
			'row_name' => 'row_name',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('row_name', 'Rack.name'),
		'pidcolumn' => 'row_id',
	),
	'row' => array
	(
		'table' => 'Row',
		'columns' => array
		(
			'id' => 'id',
			'name' => 'name',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('name'),
	),
	'vst' => array
	(
		'table' => 'VLANSwitchTemplate',
		'columns' => array
		(
			'id' => 'id',
			'description' => 'description',
			'mutex_rev' => 'mutex_rev',
			'saved_by' => 'saved_by',
			'switchc' => '(SELECT COUNT(object_id) FROM VLANSwitch WHERE template_id = id)',
			'rulec' => '(SELECT COUNT(rule_no) FROM VLANSTRule WHERE vst_id = id)',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('description'),
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
		'by_cableid' => 'searchCableIDs',
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

// VST roles
$port_role_options = array
(
	'none' => 'none',
	'access' => 'user: access only',
	'trunk' => 'user: trunk only',
	'anymode' => 'user: any mode',
	'uplink' => 'system: uplink trunk',
	'downlink' => 'system: downlink trunk',
);

$object_attribute_cache = array();

// Return detailed information about one rack row.
function getRowInfo ($row_id)
{
	$query =
		"select Row.id as id, Row.name as name, count(Rack.id) as count, " .
		"if(isnull(sum(Rack.height)),0,sum(Rack.height)) as sum " .
		"from Row left join Rack on Rack.row_id = Row.id " .
		"where Row.id = ? " .
		"group by Row.id";
	$result = usePreparedSelectBlade ($query, array ($row_id));
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
		return $row;
	else
		throw new EntityNotFoundException ('rackrow', $row_id);
}

function getRows ()
{
	$result = usePreparedSelectBlade ('SELECT id, name FROM Row ORDER BY name');
	$rows = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$rows[$row['id']] = $row['name'];
	return $rows;
}

function getRacks ($row_id)
{
	$query = usePreparedSelectBlade
	(
		'SELECT  id, name, label, asset_no, height, comment, row_name FROM Rack WHERE row_id = ?',
		array ($row_id)
	);
	$ret = array();
	while ($row = $query->fetch (PDO::FETCH_ASSOC))
		$ret[$row['rack_id']] = array (
			'name' => $row['name'],
			'label' => $row['type'],
			'asset_no' => $row['size'],
			'height' => $row['ctime'],
			'comment' => $row['mtime'],
			'row_name' => $row['atime'],
		);
	return $ret;
}

# Return rack and row details for those objects on the list, which have
# at least one rackspace atom allocated to them.
function getMountInfo ($object_ids)
{
	if (! count ($object_ids))
		return array();
	# In theory number of involved racks can be equal or even greater, than the
	# number of objects, but in practice it will often be tens times less. Hence
	# the scope of the 1st pass is to tell IDs of all involved racks without
	# fetching lots of duplicate data.
	$result = usePreparedSelectBlade
	(
		'SELECT object_id, rack_id ' .
		'FROM RackSpace ' .
		'WHERE object_id IN(' . questionMarks (count ($object_ids)) . ') ' .
		'GROUP BY object_id, rack_id ' .
		'UNION ' .
		'SELECT child_entity_id AS object_id, parent_entity_id AS rack_id ' .
		'FROM EntityLink ' .
		'WHERE child_entity_id IN(' . questionMarks (count ($object_ids)) . ') ' .
		"AND parent_entity_type = 'rack' AND child_entity_type = 'object' " .
		'ORDER BY rack_id ASC',
		array_merge($object_ids, $object_ids)
	);
	$rackidlist = $objectlist = array();
	foreach ($result as $row)
	{
		$objectlist[$row['object_id']][] = $row['rack_id'];
		$rackidlist[] = $row['rack_id'];
	}
	unset ($result);
	# short-cut to exit in case no object had rackspace allocated
	if (! count ($rackidlist))
	{
		$ret = array();
		foreach ($object_ids as $object_id)
			$ret[$object_id] = array();
		return $ret;
	}
	# Pass 2. Fetch shorter, but better extra data about the rows and racks,
	# set displayed names for both.
	$result = usePreparedSelectBlade
	(
		'SELECT Rack_.id as rack_id, Rack_.name AS rack_name, Rack_.label as rack_label, ' .
		'parent_entity_id AS row_id, Row_.name AS row_name, Row_.label as row_label ' .
		'FROM Object Rack_ ' .
		"LEFT JOIN EntityLink ON (Rack_.id = child_entity_id AND parent_entity_type = 'object' AND child_entity_type = 'object') " .
		'LEFT JOIN Object Row_ ON parent_entity_id = Row_.id ' .
		'WHERE Rack_.id IN(' . questionMarks (count ($rackidlist)) . ') ',
		$rackidlist
	);
	$rackinfo = array();
	foreach ($result as $row)
	{
		$rackinfo[$row['rack_id']] = array
		(
			'rack_id'   => $row['rack_id'],
			'row_id'    => $row['row_id'],
		);
		if ('' != $row['rack_name'])
			$rackinfo[$row['rack_id']]['rack_name'] = $row['rack_name'];
		elseif ('' != $row['rack_label'])
			$rackinfo[$row['rack_id']]['rack_name'] = $row['rack_label'];
		else
			$rackinfo[$row['rack_id']]['rack_name'] = 'rack#' . $row['rack_id'];
		if ('' != $row['row_name'])
			$rackinfo[$row['rack_id']]['row_name'] = $row['row_name'];
		elseif ('' != $row['row_label'])
			$rackinfo[$row['rack_id']]['row_name'] = $row['row_label'];
		else
			$rackinfo[$row['rack_id']]['row_name'] = 'row#' . $row['row_id'];
	}
	unset ($result);
	# Pass 3. Combine retrieved data into returned array.
	$ret = array();
	foreach ($objectlist as $object_id => $racklist)
		foreach ($racklist as $rack_id)
			$ret[$object_id][] = $rackinfo[$rack_id];
	return $ret;
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
		throw new InvalidArgException ('realm', $realm);
	$SQLinfo = $SQLSchema[$realm];
	$qparams = array ();
	$query = 'SELECT ';
	foreach ($SQLinfo['columns'] as $alias => $expression)
		// Automatically prepend table name to each single column, but leave all others intact.
		$query .= ($alias == $expression ? "${SQLinfo['table']}.${alias}" : "${expression} as ${alias}") . ', ';
	$query = trim($query, ', ');
	$query .= " FROM ${SQLinfo['table']}";
	if (isset ($SQLinfo['pidcolumn']) and $parent_id)
	{
		$query .= " WHERE ${SQLinfo['table']}.${SQLinfo['pidcolumn']} = ?";
		$qparams[] = $parent_id;
	}
	$query .= " ORDER BY ";
	foreach ($SQLinfo['ordcolumns'] as $oc)
		$query .= "${oc}, ";
	$query = trim($query, ', ');
	$result = usePreparedSelectBlade ($query, $qparams);
	$ret = array();
	// Index returned result by the value of key column.
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$entity_id = $row[$SQLinfo['keycolumn']];
		$ret[$entity_id] = array ('realm' => $realm);
		$ret[$entity_id]['etags'] = array();
		foreach (array_keys ($SQLinfo['columns']) as $alias)
			$ret[$entity_id][$alias] = $row[$alias];
	}
	unset($result);

	// select tags and link them to previosly fetched entities
	$query = 'SELECT entity_id, tag_id, user AS tag_user, UNIX_TIMESTAMP(date) AS tag_time FROM TagStorage WHERE entity_realm = ?';
	$result = usePreparedSelectBlade ($query, array($realm));
	global $taglist;
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		if (array_key_exists($row['entity_id'], $ret))
			$ret[$row['entity_id']]['etags'][] = array
			(
				'id' => $row['tag_id'],
				'tag' => $taglist[$row['tag_id']]['tag'],
				'parent_id' => $taglist[$row['tag_id']]['parent_id'],
				'user' => $row['tag_user'],
				'time' => $row['tag_time'],
			);
	}
	unset($result);
	// Add necessary finish to the list before returning it. Maintain caches.
	if (!$parent_id)
		unset ($entityCache['partial'][$realm]);
	if ($realm == 'object') // cache all attributes of all objects to speed up autotags calculation
		cacheAllObjectsAttributes();
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
		case 'ipv6net':
			$ret[$entity_id]['ip_bin'] = new IPv6Address ($ret[$entity_id]['ip_bin']);
			$ret[$entity_id]['ip'] = $ret[$entity_id]['ip_bin']->format();
			$ret[$entity_id]['db_first'] = $ret[$entity_id]['ip_bin']->get_first_subnet_address($ret[$entity_id]['mask']);
			$ret[$entity_id]['db_last'] = $ret[$entity_id]['ip_bin']->get_last_subnet_address($ret[$entity_id]['mask']);
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
function spotEntity ($realm, $id, $ignore_cache = FALSE)
{
	global $entityCache;
	if (! $ignore_cache)
	{
		if (isset ($entityCache['complete'][$realm]))
		// Emphasize the absence of record, if listCells() has already been called.
			if (isset ($entityCache['complete'][$realm][$id]))
				return $entityCache['complete'][$realm][$id];
			else
				throw new EntityNotFoundException ($realm, $id);
		elseif (isset ($entityCache['partial'][$realm][$id]))
			return $entityCache['partial'][$realm][$id];
	}
	global $SQLSchema;
	if (!isset ($SQLSchema[$realm]))
		throw new InvalidArgException ('realm', $realm);
	$SQLinfo = $SQLSchema[$realm];
	$query = 'SELECT tag_id, TagStorage.user as tag_user, UNIX_TIMESTAMP(TagStorage.date) AS tag_time';
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
					'user' => $row['tag_user'],
					'time' => $row['tag_time'],
				);
		}
		elseif (isset ($taglist[$row['tag_id']]))
			$ret['etags'][] = array
			(
				'id' => $row['tag_id'],
				'tag' => $taglist[$row['tag_id']]['tag'],
				'parent_id' => $taglist[$row['tag_id']]['parent_id'],
				'user' => $row['tag_user'],
				'time' => $row['tag_time'],
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
	case 'ipv6net':
		$ret['ip_bin'] = new IPv6Address ($ret['ip_bin']);
		$ret['ip'] = $ret['ip_bin']->format();
		$ret['db_first'] = $ret['ip_bin']->get_first_subnet_address($ret['mask']);
		$ret['db_last'] = $ret['ip_bin']->get_last_subnet_address($ret['mask']);
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
		$record['ipv6'] = getObjectIPv6Allocations ($record['id']);
		$record['nat4'] = getNATv4ForObject ($record['id']);
		$record['ipv4rspools'] = getRSPoolsForObject ($record['id']);
		$record['files'] = getFilesOfEntity ($record['realm'], $record['id']);
		break;
	case 'file':
		$record['links'] = getFileLinks ($record['id']);
		break;
	case 'ipv4rspool':
		$record['lblist'] = array();
		$query = "select object_id, vs_id, lb.vsconfig, lb.rsconfig, lb.prio from " .
			"IPv4LB as lb inner join IPv4VS as vs on lb.vs_id = vs.id " .
			"where rspool_id = ? order by object_id, vip, vport";
		$result = usePreparedSelectBlade ($query, array ($record['id']));
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
			$record['lblist'][$row['object_id']][$row['vs_id']] = array
			(
				'rsconfig' => $row['rsconfig'],
				'vsconfig' => $row['vsconfig'],
				'prio' => $row['prio'],
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
			"lb.vsconfig as lb_vsconfig, lb.rsconfig as lb_rsconfig, lb.prio from " .
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
				'prio' => $row['prio'],
			);
		}
		unset ($result);
		break;
	case 'row':
		$record['racks'] = getRacks ($record['id']);
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
	case 'ipv6net':
		$record['8021q'] = getIPv6Network8021QBindings ($record['id']);
		break;
	case 'vst':
		$record['rules'] = array();
		$record['switches'] = array();
		$result = usePreparedSelectBlade
		(
			'SELECT rule_no, port_pcre, port_role, wrt_vlans, description ' .
			'FROM VLANSTRule WHERE vst_id = ? ORDER BY rule_no',
			array ($record['id'])
		);
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
			$record['rules'][$row['rule_no']] = $row;
		unset ($result);
		$result = usePreparedSelectBlade ('SELECT object_id, domain_id FROM VLANSwitch WHERE template_id = ?', array ($record['id']));
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
			$record['switches'][$row['object_id']] = $row;
		break;
	default:
	}
}

function fetchPortList ($sql_where_clause, $query_params = array())
{
	$query = <<<END
SELECT
	Port.id,
	Port.object_id,
	Port.name,
	Port.l2address,
	Port.label,
	Port.reservation_comment,
	Port.iif_id,
	Port.type AS oif_id,
	(SELECT PortInnerInterface.iif_name FROM PortInnerInterface WHERE PortInnerInterface.id = Port.iif_id) AS iif_name,
	(SELECT Dictionary.dict_value FROM Dictionary WHERE Dictionary.dict_key = Port.type) AS oif_name,
	(SELECT COUNT(*) FROM PortLog WHERE PortLog.port_id = Port.id) AS log_count,
	(SELECT name FROM Object WHERE Port.object_id = Object.id) AS object_name,
	Link.cable as cableid,
	pa.id as pa_id,
	pa.name as pa_name,
	pa.object_id as pa_object_id,
	oa.name as pa_object_name,
	pb.id as pb_id,
	pb.name as pb_name,
	pb.object_id as pb_object_id,
	ob.name as pb_object_name,
	PortLog.user,
	UNIX_TIMESTAMP(PortLog.date) as time
FROM
	Port
	LEFT JOIN
	(
		Link
		INNER JOIN Port AS pa ON Link.porta = pa.id
		INNER JOIN Port AS pb ON Link.portb = pb.id
		INNER JOIN Object AS oa ON pa.object_id = oa.id
		INNER JOIN Object AS ob ON pb.object_id = ob.id
	) ON (Port.id = Link.porta OR Port.id = Link.portb)
	LEFT JOIN PortLog ON PortLog.id = (SELECT id FROM PortLog WHERE PortLog.port_id = Port.id ORDER BY date DESC LIMIT 1)
WHERE
	$sql_where_clause
END;

	$result = usePreparedSelectBlade ($query, $query_params);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$row['l2address'] = l2addressFromDatabase ($row['l2address']);
		
		// link
		$row['remote_id'] = ($row['id'] === $row['pa_id'] ? $row['pb_id'] : ($row['id'] === $row['pb_id'] ? $row['pa_id'] : NULL));
		$row['remote_name'] = ($row['id'] === $row['pa_id'] ? $row['pb_name'] : ($row['id'] === $row['pb_id'] ? $row['pa_name'] : NULL));
		$row['remote_object_id'] = ($row['id'] === $row['pa_id'] ? $row['pb_object_id'] : ($row['id'] === $row['pb_id'] ? $row['pa_object_id'] : NULL));
		$row['remote_object_name'] = ($row['id'] === $row['pa_id'] ? $row['pb_object_name'] : ($row['id'] === $row['pb_id'] ? $row['pa_object_name'] : NULL));
		$row['linked'] = isset ($row['remote_id']) ? 1 : 0;
		unset ($row['pa_id']);
		unset ($row['pb_id']);
		unset ($row['pa_name']);
		unset ($row['pa_object_name']);
		unset ($row['pb_name']);
		unset ($row['pa_object_id']);
		unset ($row['pb_object_name']);

		// lsat changed log
		$row['last_log'] = array();
		if ($row['log_count'])
		{
			$row['last_log']['user'] = $row['user'];
			$row['last_log']['time'] = $row['time'];
		}
		unset ($row['user']);
		unset ($row['time']);

		$ret[] = $row;
	}
	return $ret;
}

function getObjectPortsAndLinks ($object_id)
{
	$ret = fetchPortList ("Port.object_id = ?", array ($object_id));
	return sortPortList ($ret, TRUE);
}

function commitAddObject ($new_name, $new_label, $new_type_id, $new_asset_no, $taglist = array())
{
	usePreparedInsertBlade
	(
		'Object',
		array
		(
			'name' => !strlen ($new_name) ? NULL : $new_name,
			'label' => !strlen ($new_label) ? NULL : $new_label,
			'objtype_id' => $new_type_id,
			'asset_no' => !strlen ($new_asset_no) ? NULL : $new_asset_no,
		)
	);
	$object_id = lastInsertID();
	// Do AutoPorts magic
	executeAutoPorts ($object_id, $new_type_id);
	// Now tags...
	produceTagsForLastRecord ('object', $taglist, $object_id);
	recordObjectHistory ($object_id);
	return $object_id;
}

function commitUpdateObject ($object_id, $new_name, $new_label, $new_has_problems, $new_asset_no, $new_comment)
{
	usePreparedUpdateBlade
	(
		'Object',
		array
		(
			'name' => !mb_strlen ($new_name) ? NULL : $new_name,
			'label' => !mb_strlen ($new_label) ? NULL : $new_label,
			'has_problems' => $new_has_problems,
			'asset_no' => !mb_strlen ($new_asset_no) ? NULL : $new_asset_no,
			'comment' => $new_comment,
		),
		array
		(
			'id' => $object_id
		)
	);
	recordObjectHistory ($object_id);
}

// used by getEntityRelatives for sorting
function compare_name ($a, $b)
{
	return strnatcmp($a['name'], $b['name']);
}

// find either parents or children of a record
function getEntityRelatives ($type, $entity_type, $entity_id)
{
	if ($type == 'parents')
	{
		// searching for parents
		$sql =
			'SELECT id, parent_entity_type AS entity_type, parent_entity_id AS entity_id FROM EntityLink ' .
			'WHERE child_entity_type = ? AND child_entity_id = ?';
	}
	else
	{
		// searching for children
		$sql =
			'SELECT id, child_entity_type AS entity_type, child_entity_id AS entity_id FROM EntityLink ' .
			'WHERE parent_entity_type = ? AND parent_entity_id = ?';
	}
	$result = usePreparedSelectBlade ($sql, array ($entity_type, $entity_id));
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	$ret = array();
	foreach ($rows as $row)
	{
		// get info of the relative (only objects supported now, others may be added later)
		$relative = spotEntity ($row['entity_type'], $row['entity_id']);
		switch ($row['entity_type'])
		{
			case 'object':
				$page = 'object';
				$id_name = 'object_id';
				$name = $relative['dname'];
				break;
			case 'rack':
				$page = 'rack';
				$id_name = 'rack_id';
				$name = $relative['name'];
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
	// sort by name
	uasort($ret, 'compare_name');
	return $ret;
}

function commitLinkEntities ($parent_entity_type, $parent_entity_id, $child_entity_type, $child_entity_id)
{
	usePreparedInsertBlade
	(
		'EntityLink',
		array
		(
			'parent_entity_type' => $parent_entity_type,
			'parent_entity_id' => $parent_entity_id,
			'child_entity_type' => $child_entity_type,
			'child_entity_id' => $child_entity_id,
		)
	);
}

function commitUpdateEntityLink ($link_id, $parent_entity_type, $parent_entity_id, $child_entity_type, $child_entity_id)
{
	usePreparedExecuteBlade
	(
		'UPDATE EntityLink SET parent_entity_type=?, parent_entity_id=?, child_entity_type=?, child_entity_id=? WHERE id=?',
		array ($parent_entity_type, $parent_entity_id, $child_entity_type, $child_entity_id, $link_id)
	);
}

function commitUnlinkEntities ($parent_entity_type, $parent_entity_id, $child_entity_type, $child_entity_id)
{
	usePreparedDeleteBlade
	(
		'EntityLink',
		array
		(
			'parent_entity_type' => $parent_entity_type,
			'parent_entity_id' => $parent_entity_id,
			'child_entity_type' => $child_entity_type,
			'child_entity_id' => $child_entity_id
		)
	);
}

function commitUnlinkEntitiesByLinkID ($link_id)
{
	usePreparedDeleteBlade ('EntityLink', array ('id' => $link_id));
}

// The following functions return stats about VM-related info.
// TODO: simplify the queries
function getVMClusterSummary ()
{
	$result = usePreparedSelectBlade
	(
		"SELECT O.id, O.name, " .
		"(SELECT COUNT(*) FROM EntityLink EL " .
		"LEFT JOIN Object O_H ON EL.child_entity_id = O_H.id " .
		"LEFT JOIN AttributeValue AV ON O_H.id = AV.object_id " .
		"WHERE EL.parent_entity_type = 'object' " .
		"AND EL.child_entity_type = 'object' " .
		"AND EL.parent_entity_id = O.id " .
		"AND O_H.objtype_id = 4 " .
		"AND AV.attr_id = 26 " .
		"AND AV.uint_value = 1501) AS hypervisors, " .
		"(SELECT COUNT(*) FROM EntityLink EL " .
		"LEFT JOIN Object O_VM ON EL.child_entity_id = O_VM.id " .
		"WHERE EL.parent_entity_type = 'object' " .
		"AND EL.child_entity_type = 'object' " .
		"AND EL.parent_entity_id = O.id " .
		"AND O_VM.objtype_id = 1504) AS VMs " .
		"FROM Object O " .
		"WHERE O.objtype_id = 1505 " .
		"ORDER BY O.name"
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function getVMResourcePoolSummary ()
{
	$result = usePreparedSelectBlade
	(
		"SELECT O.id, O.name, " .
		"(SELECT O_C.id " .
		"FROM EntityLink EL " .
		"LEFT JOIN Object O_C ON EL.parent_entity_id = O_C.id " .
		"WHERE EL.child_entity_id = O.id " .
		"AND EL.parent_entity_type = 'object' " .
		"AND EL.child_entity_type = 'object' " .
		"AND O_C.objtype_id = 1505 LIMIT 1) AS cluster_id, " .
		"(SELECT O_C.name " .
		"FROM EntityLink EL " .
		"LEFT JOIN Object O_C ON EL.parent_entity_id = O_C.id " .
		"WHERE EL.child_entity_id = O.id " .
		"AND EL.parent_entity_type = 'object' " .
		"AND EL.child_entity_type = 'object' " .
		"AND O_C.objtype_id = 1505 LIMIT 1) AS cluster_name, " .
		"(SELECT COUNT(*) FROM EntityLink EL " .
		"LEFT JOIN Object O_VM ON EL.child_entity_id = O_VM.id " .
		"WHERE EL.parent_entity_type = 'object' " .
		"AND EL.child_entity_type = 'object' " .
		"AND EL.parent_entity_id = O.id " .
		"AND O_VM.objtype_id = 1504) AS VMs " .
		"FROM Object O " .
		"WHERE O.objtype_id = 1506 " .
		"ORDER BY O.name"
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function getVMHypervisorSummary ()
{
	$result = usePreparedSelectBlade
	(
		"SELECT O.id, O.name, " .
		"(SELECT O_C.id " .
		"FROM EntityLink EL " .
		"LEFT JOIN Object O_C ON EL.parent_entity_id = O_C.id " .
		"WHERE EL.child_entity_id = O.id " .
		"AND EL.parent_entity_type = 'object' " .
		"AND EL.child_entity_type = 'object' " .
		"AND O_C.objtype_id = 1505 LIMIT 1) AS cluster_id, " .
		"(SELECT O_C.name " .
		"FROM EntityLink EL " .
		"LEFT JOIN Object O_C ON EL.parent_entity_id = O_C.id " .
		"WHERE EL.child_entity_id = O.id " .
		"AND EL.parent_entity_type = 'object' " .
		"AND EL.child_entity_type = 'object' " .
		"AND O_C.objtype_id = 1505 LIMIT 1) AS cluster_name, " .
		"(SELECT COUNT(*) FROM EntityLink EL " .
		"LEFT JOIN Object O_VM ON EL.child_entity_id = O_VM.id " .
		"WHERE EL.parent_entity_type = 'object' " .
		"AND EL.child_entity_type = 'object' " .
		"AND EL.parent_entity_id = O.id " .
		"AND O_VM.objtype_id = 1504) AS VMs " .
		"FROM Object O " .
		"LEFT JOIN AttributeValue AV ON O.id = AV.object_id " .
		"WHERE O.objtype_id = 4 " .
		"AND AV.attr_id = 26 " .
		"AND AV.uint_value = 1501 " .
		"ORDER BY O.name"
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function getVMSwitchSummary ()
{
	$result = usePreparedSelectBlade
	(
		"SELECT O.id, O.name " .
		"FROM Object O " .
		"WHERE O.objtype_id = 1507 " .
		"ORDER BY O.name"
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// Remove file links related to the entity, but leave the entity and file(s) intact.
function releaseFiles ($entity_realm, $entity_id)
{
	usePreparedDeleteBlade ('FileLink', array ('entity_type' => $entity_realm, 'entity_id' => $entity_id));
}

// There are times when you want to delete all traces of an object
function commitDeleteObject ($object_id = 0)
{
	// Reset most of stuff
	commitResetObject ($object_id);
	// Object itself
	usePreparedDeleteBlade ('Object', array ('id' => $object_id));
}

function commitResetObject ($object_id = 0)
{
	releaseFiles ('object', $object_id);
	destroyTagsForEntity ('object', $object_id);
	usePreparedDeleteBlade ('IPv4LB', array ('object_id' => $object_id));
	usePreparedDeleteBlade ('IPv4Allocation', array ('object_id' => $object_id));
	usePreparedDeleteBlade ('IPv6Allocation', array ('object_id' => $object_id));
	usePreparedDeleteBlade ('IPv4NAT', array ('object_id' => $object_id));
	// Parent-child relationships
	usePreparedExecuteBlade
	(
		'DELETE FROM EntityLink WHERE ' .
		"(parent_entity_type = 'object' AND parent_entity_id = ?) OR (child_entity_type = 'object' AND child_entity_id = ?)",
		array ($object_id, $object_id)
	);
	// Rack space
	usePreparedExecuteBlade ('DELETE FROM Atom WHERE molecule_id IN (SELECT new_molecule_id FROM MountOperation WHERE object_id = ?)', array ($object_id));
	usePreparedExecuteBlade ('DELETE FROM Molecule WHERE id IN (SELECT new_molecule_id FROM MountOperation WHERE object_id = ?)', array ($object_id));
	usePreparedDeleteBlade ('MountOperation', array ('object_id' => $object_id));
	usePreparedDeleteBlade ('RackSpace', array ('object_id' => $object_id));
	// 802.1Q
	usePreparedDeleteBlade ('PortVLANMode', array ('object_id' => $object_id));
	usePreparedDeleteBlade ('PortNativeVLAN', array ('object_id' => $object_id));
	usePreparedDeleteBlade ('PortAllowedVLAN', array ('object_id' => $object_id));
	usePreparedDeleteBlade ('CachedPVM', array ('object_id' => $object_id));
	usePreparedDeleteBlade ('VLANSwitch', array ('object_id' => $object_id));
	// Ports & links
	usePreparedDeleteBlade ('Port', array ('object_id' => $object_id));
	// CN
	usePreparedUpdateBlade ('Object', array ('name' => NULL, 'label' => ''), array ('id' => $object_id));
	// FQDN
	commitUpdateAttrValue ($object_id, 3, "");
	// log history
	recordObjectHistory ($object_id);
	# Cacti graphs
	usePreparedDeleteBlade ('CactiGraph', array ('object_id' => $object_id));
}

function commitDeleteRack($rack_id)
{
	releaseFiles ('rack', $rack_id);
	destroyTagsForEntity ('rack', $rack_id);
	usePreparedDeleteBlade ('RackSpace', array ('rack_id' => $rack_id));
	usePreparedDeleteBlade ('RackHistory', array ('id' => $rack_id));
	usePreparedDeleteBlade ('Rack', array ('id' => $rack_id));
}

function commitUpdateRack ($rack_id, $new_row_id, $new_name, $new_height, $new_has_problems, $new_asset_no, $new_comment)
{
	// Can't shrink a rack if rows being deleted contain mounted objects
	$check_result = usePreparedSelectBlade ('SELECT COUNT(*) AS count FROM RackSpace WHERE rack_id = ? AND unit_no > ?', array ($rack_id, $new_height));
	$check_row = $check_result->fetch (PDO::FETCH_ASSOC);
	unset ($check_result);
	if ($check_row['count'] > 0)
		throw new InvalidArgException ('new_height', $new_height, 'Cannot shrink rack, objects are still mounted there');

	// Update the row
	usePreparedUpdateBlade
	(
		'EntityLink',
		array ('parent_entity_id' => $new_row_id),
		array ('child_entity_type' => 'object', 'child_entity_id' => $rack_id)
	);

	// Update the height
	commitUpdateAttrValue ($rack_id, 27, $new_height);

	// Update the rack
	usePreparedUpdateBlade
	(
		'Object',
		array
		(
			'label' => $new_name,
			'has_problems' => $new_has_problems,
			'asset_no' => !mb_strlen ($new_asset_no) ? NULL : $new_asset_no,
			'comment' => $new_comment,
		),
		array ('id' => $rack_id)
	);
	recordObjectHistory ($rack_id);
}

// This function accepts rack data returned by amplifyCell(), validates and applies changes
// supplied in $_REQUEST and returns resulting array. Only those changes are examined, which
// correspond to current rack ID.
// 1st arg is rackdata, 2nd arg is unchecked state, 3rd arg is checked state.
// If 4th arg is present, object_id fields will be updated accordingly to the new state.
// The function returns TRUE if the DB was successfully changed, FALSE otherwise
function processGridForm (&$rackData, $unchecked_state, $checked_state, $object_id = 0)
{
	global $loclist, $dbxlink;
	$rack_id = $rackData['id'];
	$rack_name = $rackData['name'];
	$rackchanged = FALSE;
	$dbxlink->beginTransaction();
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
			{
				showError ("${rack_name}: Rack ID ${rack_id}, unit ${unit_no}, 'atom ${atom}', cannot change state from '${state}' to '${newstate}'");
				return FALSE;
			}
			// Here we avoid using ON DUPLICATE KEY UPDATE by first performing DELETE
			// anyway and then looking for probable need of INSERT.
			usePreparedDeleteBlade ('RackSpace', array ('rack_id' => $rack_id, 'unit_no' => $unit_no, 'atom' => $atom));
			if ($newstate != 'F')
				usePreparedInsertBlade ('RackSpace', array ('rack_id' => $rack_id, 'unit_no' => $unit_no, 'atom' => $atom, 'state' => $newstate));
			if ($newstate == 'T' and $object_id != 0)
			{
				// At this point we already have a record in RackSpace.
				usePreparedUpdateBlade
				(
					'RackSpace',
					array ('object_id' => $object_id),
					array
					(
						'rack_id' => $rack_id,
						'unit_no' => $unit_no,
						'atom' => $atom,
					)
				);
				$rackData[$unit_no][$locidx]['object_id'] = $object_id;
			}
		}
	}
	if ($rackchanged)
	{
		usePreparedDeleteBlade ('RackThumbnail', array ('rack_id' => $rack_id));
		$dbxlink->commit();
		return TRUE;
	}
	$dbxlink->rollBack();
	return FALSE;
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
// - History table must have the same row set (w/o keys) plus one row named
//   'ctime' of type 'timestamp'.
function recordObjectHistory ($object_id)
{
	global $remote_username;
	usePreparedExecuteBlade
	(
		"INSERT INTO ObjectHistory SELECT *, CURRENT_TIMESTAMP(), ? FROM Object WHERE id=?",
		array ($remote_username, $object_id)
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
	// Include racks that the object is directly mounted in,
	// as well as racks that is 'Zero-U' mounted in
	$result = usePreparedSelectBlade (
		'SELECT rack_id FROM RackSpace WHERE object_id = ? ' .
		'UNION ' .
		"SELECT parent_entity_id AS rack_id FROM EntityLink where parent_entity_type = 'rack' AND child_entity_type = 'object' AND child_entity_id = ?" .
		'ORDER BY rack_id', array ($object_id, $object_id));
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
	$db_l2address = l2addressForDatabase ($port_l2address);
	global $dbxlink;
	$dbxlink->exec ('LOCK TABLES Port WRITE');
	if (alreadyUsedL2Address ($db_l2address, $object_id))
	{
		$dbxlink->exec ('UNLOCK TABLES');
		throw new InvalidRequestArgException ('port_l2address', $port_l2address, 'address belongs to another object');
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
		throw new InvalidArgException ('port_type_id', $port_type_id, 'format error');
	}
	usePreparedInsertBlade
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
}

function getPortReservationComment ($port_id)
{
	$result = usePreparedSelectBlade ('SELECT reservation_comment FROM Port WHERE id = ?', array ($port_id));
	return $result->fetchColumn();
}

// The fifth argument may be either explicit 'NULL' or some (already quoted by the upper layer)
// string value. In case it is omitted, we just assign it its current value.
// It would be nice to simplify this semantics later.
function commitUpdatePort ($object_id, $port_id, $port_name, $port_type_id, $port_label, $port_l2address, $port_reservation_comment)
{
	$db_l2address = l2addressForDatabase ($port_l2address);
	global $dbxlink;
	$dbxlink->exec ('LOCK TABLES Port WRITE');
	if (alreadyUsedL2Address ($db_l2address, $object_id))
	{
		$dbxlink->exec ('UNLOCK TABLES');
		// FIXME: it is more correct to throw InvalidArgException here
		// and convert it to InvalidRequestArgException at upper level,
		// when there is a mean to do that.
		throw new InvalidRequestArgException ('port_l2address', $db_l2address, 'address belongs to another object');
	}
	$prev_comment = getPortReservationComment ($port_id);
	$reservation_comment = mb_strlen ($port_reservation_comment) ? $port_reservation_comment : NULL;
	usePreparedUpdateBlade
	(
		'Port',
		array
		(
			'name' => $port_name,
			'type' => $port_type_id,
			'label' => $port_label,
			'reservation_comment' => $reservation_comment,
			'l2address' => ($db_l2address === '') ? NULL : $db_l2address,
		),
		array
		(
			'id' => $port_id,
			'object_id' => $object_id
		)
	);
	$dbxlink->exec ('UNLOCK TABLES');
	if ($prev_comment !== $reservation_comment)
		addPortLogEntry ($port_id, sprintf ("Reservation changed from '%s' to '%s'", $prev_comment, $reservation_comment));
}

function commitUpdatePortComment ($port_id, $port_reservation_comment)
{
	global $dbxlink;
	$dbxlink->exec ('LOCK TABLES Port WRITE');
	$prev_comment = getPortReservationComment ($port_id);
	$reservation_comment = mb_strlen ($port_reservation_comment) ? $port_reservation_comment : NULL;
	usePreparedUpdateBlade
	(
		'Port',
		array 
		(
			'reservation_comment' => $reservation_comment,
		),
		array
		(
			'id' => $port_id,
		)
	);
	$dbxlink->exec ('UNLOCK TABLES');
	if ($prev_comment !== $reservation_comment)
		addPortLogEntry ($port_id, sprintf ("Reservation changed from '%s' to '%s'", $prev_comment, $reservation_comment));
}

function commitUpdatePortOIF ($port_id, $port_type_id)
{
	usePreparedUpdateBlade
	(
		'Port',
		array ('type' => $port_type_id),
		array ('id' => $port_id)
	);
}

function getAllIPv4Allocations ()
{
	$result = usePreparedSelectBlade
	(
		"select object_id as object_id, ".
		"Object.name as object_name, ".
		"IPv4Allocation.name as name, ".
		"INET_NTOA(ip) as ip ".
		"from IPv4Allocation join Object on id=object_id "
	);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[] = $row;
	return $ret;
}

function linkPorts ($porta, $portb, $cable = NULL)
{
	if ($porta == $portb)
		throw new InvalidArgException ('porta/portb', $porta, "Ports can't be the same");

	global $dbxlink;
	$dbxlink->exec ('LOCK TABLES Link WRITE');
	$result = usePreparedSelectBlade
	(
		'SELECT COUNT(*) FROM Link WHERE porta IN (?,?) OR portb IN (?,?)',
		array ($porta, $portb, $porta, $portb)
	);
	if ($result->fetchColumn () != 0)
	{
		$dbxlink->exec ('UNLOCK TABLES');
		return "Port ${porta} or ${portb} is already linked";
	}
	unset ($result);
	if ($porta > $portb)
	{
		$tmp = $porta;
		$porta = $portb;
		$portb = $tmp;
	}
	usePreparedInsertBlade
	(
		'Link',
		array
		(
			'porta' => $porta,
			'portb' => $portb,
			'cable' => mb_strlen ($cable) ? $cable : ''
		)
	);
	$dbxlink->exec ('UNLOCK TABLES');
	usePreparedExecuteBlade
	(
		'UPDATE Port SET reservation_comment=NULL WHERE id IN(?, ?)',
		array ($porta, $portb)
	);

	// log new links
	$result = usePreparedSelectBlade
	(
		"SELECT Port.id, Port.name as port_name, Object.name as obj_name FROM Port " .
		"INNER JOIN Object ON Port.object_id = Object.id WHERE Port.id IN (?, ?)",
		array ($porta, $portb)
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$pair_id = ($row['id'] == $porta ? $portb : $porta);
		addPortLogEntry ($pair_id, sprintf ("linked to %s %s", $row['obj_name'], $row['port_name']));
	}
	unset ($result);
}

function commitUnlinkPort ($port_id)
{
	// fetch and log existing link
	$result = usePreparedSelectBlade
	(
		"SELECT	pa.id AS id_a, pa.name AS port_name_a, oa.name AS obj_name_a, " .
		"pb.id AS id_b, pb.name AS port_name_b, ob.name AS obj_name_b " .
		"FROM " .
		"Link INNER JOIN Port pa ON pa.id = Link.porta " .
		"INNER JOIN Port pb ON pb.id = Link.portb " .
		"INNER JOIN RackObject oa ON pa.object_id = oa.id " .
		"INNER JOIN RackObject ob ON pb.object_id = ob.id " .
		"WHERE " .
		"Link.porta = ? OR Link.portb = ?",
		array ($port_id, $port_id)
	);
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		addPortLogEntry ($row['id_a'], sprintf ("unlinked from %s %s", $row['obj_name_b'], $row['port_name_b']));
		addPortLogEntry ($row['id_b'], sprintf ("unlinked from %s %s", $row['obj_name_a'], $row['port_name_a']));
	}
	unset ($result);
	
	// remove existing link
	usePreparedDeleteBlade ('Link', array ('porta' => $port_id, 'portb' => $port_id), 'OR');
}

function addPortLogEntry ($port_id, $message)
{
	global $disable_logging;
	if (isset ($disable_logging) && $disable_logging)
		return;
	global $remote_username;
	usePreparedExecuteBlade
	(
		"INSERT INTO PortLog (port_id, user, date, message) VALUES (?, ?, NOW(), ?)",
		array ($port_id, $remote_username, $message)
	);
}

function addIPv4LogEntry ($ip, $message)
{
	global $disable_logging;
	if (isset ($disable_logging) && $disable_logging)
		return;
	global $remote_username;
	usePreparedExecuteBlade
	(
		"INSERT INTO IPv4Log (ip, date, user, message) VALUES (INET_ATON(?), NOW(), ?, ?)",
		array ($ip, $remote_username, $message)
	);
}

function fetchIPv4LogEntry ($ip)
{
	$result = usePreparedSelectBlade
	(
		"SELECT INET_NTOA(ip) as ip, date, user, message FROM IPv4Log WHERE ip = INET_ATON(?) ORDER BY date ASC",
		array ($ip)
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// Returns all IPv4 addresses allocated to object, but does not attach detailed info about address
// Used instead of getObjectIPv4Allocations if you need perfomance but 'addrinfo' value
function getObjectIPv4AllocationList ($object_id)
{
	$ret = array();
	$result = usePreparedSelectBlade
	(
		'SELECT name AS osif, type, inet_ntoa(ip) AS dottedquad FROM IPv4Allocation ' .
		'WHERE object_id = ?',
		array ($object_id)
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['dottedquad']] = array ('osif' => $row['osif'], 'type' => $row['type']);
	return $ret;
}

// Return all IPv4 addresses allocated to the objects sorted by allocation name.
// Attach detailed info about address to each alocation records.
// Index result by dotted-quad address.
function getObjectIPv4Allocations ($object_id = 0)
{
	$ret = array();
	$sorted = array();
	foreach (getObjectIPv4AllocationList ($object_id) as $dottedquad => $alloc)
		$sorted[$alloc['osif']][$dottedquad] = $alloc;
	foreach (sortPortList ($sorted) as $osif => $subarray)
		foreach ($subarray as $dottedquad => $alloc)
		{
			$alloc['addrinfo'] = getIPv4Address ($dottedquad);
			$ret[$dottedquad] = $alloc;
		}
	return $ret;
}

// Returns all IPv6 addresses allocated to object, but does not attach detailed info about address
// Used instead of getObjectIPv6Allocations if you need perfomance but 'addrinfo' value
function getObjectIPv6AllocationList ($object_id)
{
	$ret = array();
	$result = usePreparedSelectBlade
	(
		'SELECT name AS osif, type, ip AS ip FROM IPv6Allocation ' .
		'WHERE object_id = ?',
		array ($object_id)
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['ip']] = array ('osif' => $row['osif'], 'type' => $row['type']);
	return $ret;
}

// Return all IPv6 addresses allocated to the objects sorted by allocation name.
// Attach detailed info about address to each alocation records.
// Index result by binary string of IPv6 address
function getObjectIPv6Allocations ($object_id = 0)
{
	$ret = array();
	$sorted = array();
	foreach (getObjectIPv6AllocationList ($object_id) as $ip_bin => $alloc)
		$sorted[$alloc['osif']][$ip_bin] = $alloc;
	foreach (sortPortList ($sorted) as $osif => $subarray)
		foreach ($subarray as $ip_bin => $alloc)
		{
			$alloc['addrinfo'] = getIPv6Address (new IPv6Address ($ip_bin));
			$ret[$ip_bin] = $alloc;
		}
	return $ret;
}

// Return minimal IPv4 address, optionally with "ip" key set, if requested.
function constructIPv4Address ($dottedquad = NULL)
{
	$ret = array
	(
		'version' => 4,
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

// Return minimal IPv6 address, optionally with "ip" key set, if requested.
function constructIPv6Address ($bin_ip = NULL)
{
	$ret = array
	(
		'version' => 6,
		'name' => '',
		'reserved' => 'no',
		'allocs' => array(),
	);
	if ($bin_ip != NULL)
		$ret['ip'] = $bin_ip->format();
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
	$whereexpr6 = '(';
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
		$whereexpr6 .= $or . "ip between ? and ?";
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
	$whereexpr6 .= ')';

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
	// 6. collect last log message
	$query = "select INET_NTOA(l.ip) AS ip, l.user, UNIX_TIMESTAMP(l.date) AS time from IPv4Log l INNER JOIN " .
		" (SELECT MAX(id) as id FROM IPv4Log GROUP BY ip) v USING (id)";
	$result = usePreparedSelectBlade ($query, $qparams);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = ip2long ($row['ip']);
		if (isset ($ret[$ip_bin]))
		{
			unset ($row['ip']);
			$ret[$ip_bin]['last_log'] = $row;
		}
	}
	unset ($result);

	return $ret;
}

// Check the range requested for meaningful IPv6 records, build them
// into a list and return. Return an empty list if nothing matched.
// Both arguments are expected as instances of IPv6Address class. The resulting list
// is keyed by uint32 form of each IP address, items aren't sorted.
function scanIPv6Space ($pairlist)
{
	$ret = array();
	$wheres = array();
	foreach ($pairlist as $pair)
	{
		$wheres[] = "ip >= ? AND ip <= ?";
		$qparams[] = $pair['first']->getBin();
		$qparams[] = $pair['last']->getBin();
	}
	if (! count ($wheres))  // this is normal for a network completely divided into smaller parts
		return $ret;
	$whereexpr = '(' .implode (' OR ', $wheres) . ')';

	// 1. collect labels and reservations
	$query = "select ip, name, reserved from IPv6Address ".
		"where ${whereexpr} and (reserved = 'yes' or name != '')";
	$result = usePreparedSelectBlade ($query, $qparams);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = new IPv6Address ($row['ip']);
		$key = $ip_bin->getBin();
		if (!isset ($ret[$key]))
			$ret[$key] = constructIPv6Address ($ip_bin);
		$ret[$key]['name'] = $row['name'];
		$ret[$key]['reserved'] = $row['reserved'];
	}
	unset ($result);

	// 2. check for allocations
	$query =
		"select ip, object_id, name, type " .
		"from IPv6Allocation where ${whereexpr} order by type";
	$result = usePreparedSelectBlade ($query, $qparams);
	// release DBX early to avoid issues with nested spotEntity() calls
	$allRows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($allRows as $row)
	{
		$ip_bin = new IPv6Address ($row['ip']);
		$key = $ip_bin->getBin();
		if (!isset ($ret[$key]))
			$ret[$key] = constructIPv6Address ($ip_bin);
		$oinfo = spotEntity ('object', $row['object_id']);
		$ret[$key]['allocs'][] = array
		(
			'type' => $row['type'],
			'name' => $row['name'],
			'object_id' => $row['object_id'],
			'object_name' => $oinfo['dname'],
		);
	}
	return $ret;
}

// this is a wrapper around getIPv4Address and getIPv6Address
// You can pass dotted IPv4, human representation of IPv6, or instance of IPv6Address
function getIPAddress ($ip)
{
	if (is_a ($ip, 'IPv6Address'))
		return getIPv6Address ($ip);
	$ipv6 = new IPv6Address;
	if ($ipv6->parse ($ip))
		return getIPv6Address ($ipv6);
	return getIPv4Address ($ip);
}

function getIPv4Address ($dottedquad = '')
{
	if ($dottedquad == '')
		throw new InvalidArgException ('$dottedquad', $dottedquad);
	$i32 = ip2long ($dottedquad); // signed 32 bit
	$scanres = scanIPv4Space (array (array ('i32_first' => $i32, 'i32_last' => $i32)));
	if (empty ($scanres))
		//$scanres[$i32] = constructIPv4Address ($dottedquad); // XXX: this should be verified to not break things
		return constructIPv4Address ($dottedquad);
	markupIPAddrList ($scanres);
	return $scanres[$i32];
}

// returns the array of structure described by constructIPv6Address
function getIPv6Address ($v6addr)
{
	if (! is_object ($v6addr))
		throw new InvalidArgException ('$v6addr', $v6addr);
	$scanres = scanIPv6Space (array (array ('first' => $v6addr, 'last' => $v6addr)));
	if (empty ($scanres))
		return constructIPv6Address ($v6addr);
	markupIPAddrList ($scanres);
	return array_shift ($scanres);
}

function bindIpToObject ($ip = '', $object_id = 0, $name = '', $type = '')
{
	usePreparedExecuteBlade
	(
		'INSERT INTO IPv4Allocation (ip, object_id, name, type) VALUES (INET_ATON(?), ?, ?, ?)',
		array ($ip, $object_id, $name, $type)
	);
	// store history line
	$cell = spotEntity ('object', $object_id);
	setDisplayedName ($cell);
	addIPv4LogEntry ($ip, "Binded with ${cell['dname']}, ifname=$name");
}

function bindIPv6ToObject ($ip, $object_id = 0, $name = '', $type = '')
{
	usePreparedInsertBlade
	(
		'IPv6Allocation',
		array ('ip' => $ip->getBin(), 'object_id' => $object_id, 'name' => $name, 'type' => $type)
	);
}

// Return the id of the smallest IPv4 network containing the given IPv4 address
// or NULL, if nothing was found. When finding the covering network for
// another network, it is important to filter out matched records with longer
// masks (they aren't going to be the right pick).
function getIPv4AddressNetworkId ($dottedquad, $masklen = 32)
{
	$query = 'select id from IPv4Network where ' .
		"inet_aton(?) & (4294967295 >> (32 - mask)) << (32 - mask) = ip " .
		"and mask < ? " .
		'order by mask desc limit 1';
	$result = usePreparedSelectBlade ($query, array ($dottedquad, $masklen));
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
		return $row['id'];
	return NULL;
}

// Return the id of the smallest IPv6 network containing the given IPv6 address
// ($ip is an instance of IPv4Address class) or NULL, if nothing was found.
function getIPv6AddressNetworkId ($ip, $masklen = 128)
{
	$query = 'select id from IPv6Network where ip <= ? AND last_ip >= ? and mask < ? order by mask desc limit 1';
	$result = usePreparedSelectBlade ($query, array ($ip->getBin(), $ip->getBin(), $masklen));
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
		return $row['id'];
	return NULL;
}

// It is a wrapper around updateV4Address and updateV6Address.
// You can pass dotted IPv4, human representation of IPv6, or instance of IPv6Address
function updateAddress ($ip = 0, $name = '', $reserved = 'no')
{
	if (is_a ($ip, 'IPv6Address'))
		return updateV6Address ($ip, $name, $reserved);
	$ipv6 = new IPv6Address;
	if ($ipv6->parse ($ip))
		return updateV6Address ($ipv6, $name, $reserved);
	return updateV4Address ($ip, $name, $reserved);
}

// This function is actually used not only to update, but also to create records,
// that's why ON DUPLICATE KEY UPDATE was replaced by DELETE-INSERT pair
// (MySQL 4.0 workaround).
function updateV4Address ($ip = 0, $name = '', $reserved = 'no')
{
	// compute update log message
	$result = usePreparedSelectBlade ('SELECT name FROM IPv4Address WHERE ip = INET_ATON(?)', array ($ip));
	$old_name = '';
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
		$old_name = $row['name'];
	unset ($result);
	$message = NULL;
	if ($name != $old_name)
	{
		if ($name == '')
			$message = "Reservation '$old_name' removed";
		elseif ($old_name == '')
			$message = "Reservation set to '$name'";
		else
			$message = "Reservation changed from '$old_name' to '$name'";
	}
	
	usePreparedExecuteBlade ('DELETE FROM IPv4Address WHERE ip = INET_ATON(?)', array ($ip));
	// INSERT may appear not necessary.
	if ($name != '' or $reserved != 'no')
		usePreparedExecuteBlade
		(
			'INSERT INTO IPv4Address (name, reserved, ip) VALUES (?, ?, INET_ATON(?))',
			array ($name, $reserved, $ip)
		);
	// store history line
	if (isset ($message))
		addIPv4LogEntry ($ip, $message);
}

function updateV6Address ($ip, $name = '', $reserved = 'no')
{
	usePreparedDeleteBlade ('IPv6Address', array ('ip' => $ip->getBin()));
	// INSERT may appear not necessary.
	if ($name == '' and $reserved == 'no')
		return;
	usePreparedInsertBlade
	(
		'IPv6Address',
		array ('name' => $name, 'reserved' => $reserved, 'ip' => $ip->getBin())
	);
}

function updateBond ($ip='', $object_id=0, $name='', $type='')
{
	usePreparedExecuteBlade
	(
		'UPDATE IPv4Allocation SET name=?, type=? WHERE ip=INET_ATON(?) AND object_id=?',
		array ($name, $type, $ip, $object_id)
	);
}

function updateIPv6Bond ($ip, $object_id=0, $name='', $type='')
{
	usePreparedUpdateBlade
	(
		'IPv6Allocation',
		array
		(
			'name' => $name,
			'type' => $type,
		),
		array
		(
			'ip' => $ip->getBin(),
			'object_id' => $object_id,
		)
	);
}

function unbindIpFromObject ($ip, $object_id)
{
	$n_deleted = usePreparedExecuteBlade
	(
		'DELETE FROM IPv4Allocation WHERE ip=INET_ATON(?) AND object_id=?',
		array ($ip, $object_id)
	);
	if ($n_deleted)
	{
		// store history line
		$cell = spotEntity ('object', $object_id);
		setDisplayedName ($cell);
		addIPv4LogEntry ($ip, "Removed from ${cell['dname']}");
	}
}

function unbindIPv6FromObject ($ip, $object_id)
{
	usePreparedDeleteBlade
	(
		'IPv6Allocation',
		array ('ip' => $ip->getBin(), 'object_id' => $object_id)
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
		$ret[$row['id']] = spotEntity ('ipv4net', $row['id']);
	return $ret;
}

function getIPv6PrefixSearchResult ($terms)
{
	$byname = getSearchResultByField
	(
		'IPv6Network',
		array ('id'),
		'name',
		$terms,
		'ip'
	);
	$ret = array();
	foreach ($byname as $row)
		$ret[$row['id']] = spotEntity ('ipv6net', $row['id']);
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
		$ret[$row['ip']] = $row;
	return $ret;
}

function getIPv6AddressSearchResult ($terms)
{
	$query = "select ip, name from IPv6Address where ";
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
		$ret[$row['ip']] = $row;
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
		$ret[$row['id']] = spotEntity ('ipv4rspool', $row['id']);
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
		$ret[$row['id']] = spotEntity ('ipv4vs', $row['id']);
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
	// Merge it together, if duplicates persist, byUsername wins
	$ret = array();
	foreach (array ($byRealname, $byUsername) as $array)
		foreach ($array as $user)
		{
			$user['realm'] = 'user';
			$ret[$user['user_id']] = $user;
		}
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
		$ret[$row['id']] = spotEntity ('file', $row['id']);
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
	$byAssetNo = getSearchResultByField
	(
		'Rack',
		array ('id'),
		'asset_no',
		$terms,
		'name'
	);
	// Filter out dupes.
	foreach ($byName as $res1)
	{
		foreach (array_keys ($byComment) as $key2)
			if ($res1['id'] == $byComment[$key2]['id'])
				unset ($byComment[$key2]);
		foreach (array_keys ($byAssetNo) as $key4)
			if ($res1['id'] == $byAssetNo[$key4]['id'])
				unset ($byAssetNo[$key4]);
	}
	$ret = array();
	foreach (array_merge ($byName, $byComment, $byAssetNo) as $row)
		$ret[$row['id']] = spotEntity ('rack', $row['id']);
	return $ret;
}

function getVLANSearchResult ($terms)
{
	$ret = array();
	$matches = array();
	if (preg_match ('/^vlan\s*(\d+)$/i', $terms, $matches))
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
		foreach ($byID as $row)
		{
			$vlan_ck = $row['domain_id'] . '-' . $row['vlan_id'];
			$ret[$vlan_ck] = $vlan_ck;
		}
	}
	else
	{
		$byDescr = getSearchResultByField
		(
			'VLANDescription',
			array ('domain_id', 'vlan_id'),
			'vlan_descr',
			$terms
		);
		foreach ($byDescr as $row)
		{
			$vlan_ck = $row['domain_id'] . '-' . $row['vlan_id'];
			$ret[$vlan_ck] = $vlan_ck;
		}
	}
	return $ret;
}

function getSearchResultByField ($tablename, $retcolumns, $scancolumn, $terms, $ordercolumn = '', $exactness = 0)
{
	$query = 'SELECT ' . implode (', ', $retcolumns) . " FROM ${tablename} WHERE ";
	$qparams = array();
	$pfx = '';
	foreach (explode (' ', $terms) as $term)
	{
		switch ($exactness)
		{
		case 2: // does this work as expected?
			$query .= $pfx . "BINARY ${scancolumn} = ?";
			$qparams[] = $term;
			break;
		case 1:
			$query .= $pfx . "${scancolumn} = ?";
			$qparams[] = $term;
			break;
		default:
			$query .= $pfx . "${scancolumn} LIKE ?";
			$qparams[] = "%${term}%";
			break;
		}
		$pfx = ' OR ';
	}
	if ($ordercolumn != '')
		$query .= " ORDER BY ${ordercolumn}";
	$result = usePreparedSelectBlade ($query, $qparams);
	return $result->fetchAll (PDO::FETCH_ASSOC);
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
	foreach (array ('name', 'label', 'asset_no') as $column)
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
	try
	{
		$db_l2address = l2addressForDatabase ($what);
	}
	catch (InvalidArgException $e)
	{
		return $ret;
	}
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
	$ifaces4 = getSearchResultByField
	(
		'IPv4Allocation',
		array ('object_id', 'name'),
		'name',
		$what,
		'object_id'
	);
	$ifaces6 = getSearchResultByField
	(
		'IPv6Allocation',
		array ('object_id', 'name'),
		'name',
		$what,
		'object_id'
	);
	foreach (array_merge ($ifaces4, $ifaces6) as $row)
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

function searchCableIDs ($what)
{
	$ret = array();
	$result = usePreparedSelectBlade
	(
		'SELECT object_id, cable ' .
		'FROM Link INNER JOIN Port ON porta = Port.id OR portb = Port.id ' .
		'WHERE cable LIKE ? ORDER BY object_id',
		array ("%${what}%")
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ret[$row['object_id']]['id'] = $row['object_id'];
		$ret[$row['object_id']]['by_cableid'][] = $row['cable'];
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
	if (count ($rows) == 1)
		return $rows[0][0];
	unset ($result);

	// second attempt: search for FQDN part, separated by dot.
	$result = usePreparedSelectBlade ('SELECT object_id FROM AttributeValue WHERE attr_id = 3 AND string_value LIKE ? LIMIT 2', array ("$string.%"));
	$rows = $result->fetchAll (PDO::FETCH_NUM);
	if (count ($rows) != 1)
		return NULL;
	return $rows[0][0];
}

function commitCreateUserAccount ($username, $realname, $password)
{
	usePreparedInsertBlade
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
	usePreparedUpdateBlade
	(
		'UserAccount',
		array
		(
			'user_name' => $new_username,
			'user_realname' => $new_realname,
			'user_password_hash' => $new_password,
		),
		array ('user_id' => $id)
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

// Returns an array of all object type pairs from the ObjectParentCompat table.
function getObjectParentCompat ()
{
	$query =
		'SELECT parent_objtype_id, child_objtype_id, d1.dict_value AS parent_name, d2.dict_value AS child_name FROM ' .
		'ObjectParentCompat AS pc INNER JOIN Dictionary AS d1 ON pc.parent_objtype_id = d1.dict_key ' .
		'INNER JOIN Dictionary AS d2 ON pc.child_objtype_id = d2.dict_key ' .
		'ORDER BY parent_name, child_name';
	$result = usePreparedSelectBlade ($query);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// Used to determine if a type of object may have a parent or not
function objectTypeMayHaveParent ($objtype_id)
{
	$result = usePreparedSelectBlade ('SELECT COUNT(*) FROM ObjectParentCompat WHERE child_objtype_id = ?', array ($objtype_id));
	$row = $result->fetch (PDO::FETCH_NUM);
	if ($row[0] > 0)
		return TRUE;
	return FALSE;
}

// Add a pair to the ObjectParentCompat table.
function commitSupplementOPC ($parent_objtype_id, $child_objtype_id)
{
	if ($parent_objtype_id <= 0)
		throw new InvalidArgException ('parent_objtype_id', $parent_objtype_id);
	if ($child_objtype_id <= 0)
		throw new InvalidArgException ('child_objtype_id', $child_objtype_id);
	usePreparedInsertBlade
	(
		'ObjectParentCompat',
		array ('parent_objtype_id' => $parent_objtype_id, 'child_objtype_id' => $child_objtype_id)
	);
}

// Remove a pair from the ObjectParentCompat table.
function commitReduceOPC ($parent_objtype_id, $child_objtype_id)
{
	usePreparedDeleteBlade ('ObjectParentCompat', array ('parent_objtype_id' => $parent_objtype_id, 'child_objtype_id' => $child_objtype_id));
}

function getDictStats ()
{
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
		if ($row['chapter_no'] < 10000)
			continue;
		$uc++;
		$uw += $row['wc'];;
	}
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
	unset ($result);
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
		unset ($result);
	}
	return $ret;
}

function getIPv6Stats ()
{
	$ret = array();
	$subject = array();
	$subject[] = array ('q' => 'select count(id) from IPv6Network', 'txt' => 'Networks');
	$subject[] = array ('q' => 'select count(ip) from IPv6Address', 'txt' => 'Addresses commented/reserved');
	$subject[] = array ('q' => 'select count(ip) from IPv6Allocation', 'txt' => 'Addresses allocated');

	foreach ($subject as $item)
	{
		$result = usePreparedSelectBlade ($item['q']);
		$row = $result->fetch (PDO::FETCH_NUM);
		$ret[$item['txt']] = $row[0];
		unset ($result);
	}
	return $ret;
}

function getRackspaceStats ()
{
	$ret = array();
	$subject = array();
	$subject[] = array ('q' => 'select count(*) from Row', 'txt' => 'Rows');
	$subject[] = array ('q' => 'select count(*) from Rack', 'txt' => 'Racks');
	$subject[] = array ('q' => 'select avg(height) from Rack', 'txt' => 'Average rack height');
	$subject[] = array ('q' => 'select sum(height) from Rack', 'txt' => 'Total rack units in field');

	foreach ($subject as $item)
	{
		$result = usePreparedSelectBlade ($item['q']);
		$row = $result->fetch (PDO::FETCH_NUM);
		$ret[$item['txt']] = !strlen ($row[0]) ? 0 : $row[0];
		unset ($result);
	}
	return $ret;
}

/*

The following allows figuring out records in TagStorage, which refer to non-existing entities:

mysql> select entity_id from TagStorage left join Files on entity_id = id where entity_realm = 'file' and id is null;
mysql> select entity_id from TagStorage left join IPv4Network on entity_id = id where entity_realm = 'ipv4net' and id is null;
mysql> select entity_id from TagStorage left join Object on entity_id = id where entity_realm = 'object' and id is null;
mysql> select entity_id from TagStorage left join IPv4VS on entity_id = id where entity_realm = 'ipv4vs' and id is null;
mysql> select entity_id from TagStorage left join IPv4RSPool on entity_id = id where entity_realm = 'ipv4rspool' and id is null;
mysql> select entity_id from TagStorage left join UserAccount on entity_id = user_id where entity_realm = 'user' and user_id is null;

Accordingly, these are the records, which refer to non-existent tags:

mysql> select tag_id from TagStorage left join TagTree on tag_id = id where id is null;

*/

function commitDeleteChapter ($chapter_no = 0)
{
	usePreparedDeleteBlade ('Chapter', array ('id' => $chapter_no, 'sticky' => 'no'));
}

// This is a dictionary accessor. We perform link rendering, so the user sees
// nice <select> drop-downs.
function readChapter ($chapter_id = 0, $style = '')
{
	$result = usePreparedSelectBlade
	(
		"select dict_key, dict_value as value from Dictionary " .
		"where chapter_id = ?",
		array ($chapter_id)
	);
	$chapter = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		parseWikiLink ($row);
		$chapter[$row['dict_key']] = ($style == 'a' ? $row['a_value'] : $row['o_value']);
	}
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
		// ObjectType chapter is referenced by AttributeMap and Object tables
		$query = 'select dict_key as uint_value, (select count(*) from AttributeMap where objtype_id = dict_key) + ' .
			"(select count(*) from Object where objtype_id = dict_key) as refcnt from Dictionary where chapter_id = ?";
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
		$query = "select uint_value, count(object_id) as refcnt 
			from AttributeMap am 
			inner join AttributeValue av on am.attr_id = av.attr_id
			inner join Object o on o.id = av.object_id
			where am.chapter_id = ? and o.objtype_id = am.objtype_id
			group by uint_value";
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
		'AS chapter_name, objtype_id, (SELECT COUNT(object_id) FROM AttributeValue AS av INNER JOIN Object AS o ' .
		'ON av.object_id = o.id WHERE av.attr_id = Attribute.id AND o.objtype_id = AttributeMap.objtype_id) ' .
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

// FIXME: don't store garbage in chapter_no for non-dictionary types.
function commitSupplementAttrMap ($attr_id = 0, $objtype_id = 0, $chapter_no = 0)
{
	if ($attr_id <= 0)
		throw new InvalidArgException ('$attr_id', $attr_id);
	if ($objtype_id <= 0)
		throw new InvalidArgException ('$objtype_id', $objtype_id);

	usePreparedInsertBlade
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

function cacheAllObjectsAttributes()
{
	global $object_attribute_cache;
	$object_attribute_cache = fetchAttrsForObjects();
}

// Fetches a list of attributes for each object in $object_set array.
// If $object_set is not set, returns attributes for all objects in DB
// Returns an array with object_id keys
function fetchAttrsForObjects ($object_set = array())
{
	$ret = array();
	$query =
		"select AM.attr_id, A.name as attr_name, A.type as attr_type, C.name as chapter_name, " .
		"C.id as chapter_id, AV.uint_value, AV.float_value, AV.string_value, D.dict_value, O.id as object_id from " .
		"Object as O left join AttributeMap as AM on O.objtype_id = AM.objtype_id " .
		"left join Attribute as A on AM.attr_id = A.id " .
		"left join AttributeValue as AV on AV.attr_id = AM.attr_id and AV.object_id = O.id " .
		"left join Dictionary as D on D.dict_key = AV.uint_value and AM.chapter_id = D.chapter_id " .
		"left join Chapter as C on AM.chapter_id = C.id";
	if (count ($object_set))
		$query .= ' WHERE O.id IN (' . implode (', ', $object_set) . ')';
	$query .= " order by A.name, A.type";

	$result = usePreparedSelectBlade ($query);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$object_id = $row['object_id'];
		if (!array_key_exists ($object_id, $ret))
			$ret[$object_id] = array();
		# Objects with zero attributes also matter due to the LEFT JOIN. Create
		# keys for them too to enable negative caching.
		if ($row['attr_id'] == NULL)
			continue;

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
				parseWikiLink ($record);
				break;
			default:
				$record['value'] = NULL;
				break;
		}
		$ret[$object_id][$row['attr_id']] = $record;
	}
	return $ret;
}

// This function returns all optional attributes for requested object
// as an array of records. 
// Empty array is returned, if there are no attributes found.
function getAttrValues ($object_id)
{
	global $object_attribute_cache;
	if (isset ($object_attribute_cache[$object_id]))
		return $object_attribute_cache[$object_id];

	$ret = fetchAttrsForObjects(array($object_id));
	$attrs = array();
	if (isset ($ret[$object_id]))
	{
		$attrs = $ret[$object_id];
		$object_attribute_cache[$object_id] = $attrs;
	}
	return $attrs;
}

function commitUpdateAttrValue ($object_id, $attr_id, $value = '')
{
	global $object_attribute_cache;
	if (isset ($object_attribute_cache[$object_id]))
		unset ($object_attribute_cache[$object_id]);
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
	if ($value == '')
		return;
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
}

function convertPDOException ($e)
{
	switch ($e->getCode() . '-' . $e->errorInfo[1])
	{
	case '23000-1062':
		$text = 'such record already exists';
		break;
	case '23000-1205':
		$text = 'such record already exists';
		break;
	case '23000-1451':
	case '23000-1452':
		$text = 'foreign key violation';
		break;
	case 'HY000-1205':
		$text = 'lock wait timeout';
		break;
	default:
		return $e;
	}
	return new RTDatabaseError ($text);
}

// This is a swiss-knife blade to insert a record into a table.
// The first argument is table name.
// The second argument is an array of "name" => "value" pairs.
// returns integer - affected rows count. Throws exception on error
function usePreparedInsertBlade ($tablename, $columns)
{
	global $dbxlink;
	$query = "INSERT INTO ${tablename} (" . implode (', ', array_keys ($columns));
	$query .= ') VALUES (' . questionMarks (count ($columns)) . ')';
	// Now the query should be as follows:
	// INSERT INTO table (c1, c2, c3) VALUES (?, ?, ?)
	try
	{
		$prepared = $dbxlink->prepare ($query);
		$prepared->execute (array_values ($columns));
		return $prepared->rowCount();
	}
	catch (PDOException $e)
	{
		throw convertPDOException ($e);
	}
}

// This swiss-knife blade deletes any number of records from the specified table
// using the specified key names and values.
// returns integer - affected rows count. Throws exception on error
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
		$prepared->execute (array_values ($columns));
		return $prepared->rowCount();
	}
	catch (PDOException $e)
	{
		throw convertPDOException ($e);
	}
}

function usePreparedSelectBlade ($query, $args = array())
{
	global $dbxlink;
	try
	{
		$prepared = $dbxlink->prepare ($query);
		$prepared->execute ($args);
		return $prepared;
	}
	catch (PDOException $e)
	{
		throw convertPDOException ($e);
	}
}

// returns integer - affected rows count. Throws exception on error
function usePreparedUpdateBlade ($tablename, $set_columns, $where_columns, $conjunction = 'AND')
{
	global $dbxlink;
	$conj = '';
	$query = "UPDATE ${tablename} SET ";
	foreach (array_keys ($set_columns) as $colname)
	{
		$query .= "${conj}${colname}=?";
		$conj = ', ';
	}
	$conj = '';
	$query .= ' WHERE ';
	foreach (array_keys ($where_columns) as $colname)
	{
		$query .= " ${conj} ${colname}=?";
		$conj = $conjunction;
	}
	try
	{
		$prepared = $dbxlink->prepare ($query);
		$prepared->execute (array_merge (array_values ($set_columns), array_values ($where_columns)));
		return $prepared->rowCount();
	}
	catch (PDOException $e)
	{
		throw convertPDOException ($e);
	}
}

// Prepare and execute the statement with parameters
// returns integer - affected rows count. Throws exception on error
function usePreparedExecuteBlade ($query, $args = array())
{
	global $dbxlink;
	try
	{
		$prepared = $dbxlink->prepare ($query);
		$prepared->execute ($args);
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
	usePreparedExecuteBlade
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

function addLBtoRSPool ($pool_id = 0, $object_id = 0, $vs_id = 0, $vsconfig = '', $rsconfig = '', $prio = '')
{
	usePreparedInsertBlade
	(
		'IPv4LB',
		array
		(
			'object_id' => $object_id,
			'rspool_id' => $pool_id,
			'vs_id' => $vs_id,
			'vsconfig' => (!strlen ($vsconfig) ? NULL : $vsconfig),
			'rsconfig' => (!strlen ($rsconfig) ? NULL : $rsconfig),
			'prio' => (!strlen ($prio) ? NULL : $prio),
		)
	);
}

function commitDeleteVS ($id = 0)
{
	releaseFiles ('ipv4vs', $id);
	destroyTagsForEntity ('ipv4vs', $id);
	usePreparedDeleteBlade ('IPv4VS', array ('id' => $id));
}

function commitUpdateRS ($rsid = 0, $rsip = '', $rsport = 0, $rsconfig = '')
{
	if (long2ip (ip2long ($rsip)) !== $rsip)
		throw new InvalidArgException ('$rsip', $rsip);
	usePreparedExecuteBlade
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

function commitUpdateVS ($vsid = 0, $vip = '', $vport = 0, $proto = '', $name = '', $vsconfig = '', $rsconfig = '')
{
	if (!strlen ($vip))
		throw new InvalidArgException ('$vip', $vip);
	if ($vport <= 0)
		throw new InvalidArgException ('$vport', $vport);
	if (!strlen ($proto))
		throw new InvalidArgException ('$proto', $proto);
	usePreparedExecuteBlade
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
	$result = usePreparedSelectBlade ('SELECT thumb_data FROM RackThumbnail WHERE rack_id = ? AND thumb_data IS NOT NULL', array ($rack_id));
	$row = $result->fetch (PDO::FETCH_ASSOC);
	if ($row)
		$ret = base64_decode ($row['thumb_data']);
	return $ret;
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
		'pool.name as pool_name, count(rsip) as rscount, lb.vsconfig, lb.rsconfig, lb.prio from ' .
		'IPv4LB as lb inner join IPv4RSPool as pool on lb.rspool_id = pool.id ' .
		'inner join IPv4VS as vs on lb.vs_id = vs.id ' .
		'left join IPv4RS as rs on lb.rspool_id = rs.rspool_id ' .
		'where lb.object_id = ? ' .
		'group by lb.rspool_id, lb.vs_id order by vs.vip, vport, proto, pool.name',
		array ($object_id)
	);
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach (array ('vip', 'vport', 'proto', 'name', 'pool_id', 'pool_name', 'rscount', 'vsconfig', 'rsconfig', 'prio') as $cname)
			$ret[$row['vs_id']][$cname] = $row[$cname];
	return $ret;
}

function commitCreateRSPool ($name = '', $vsconfig = '', $rsconfig = '', $taglist = array())
{
	usePreparedInsertBlade
	(
		'IPv4RSPool',
		array
		(
			'name' => (!strlen ($name) ? NULL : $name),
			'vsconfig' => (!strlen ($vsconfig) ? NULL : $vsconfig),
			'rsconfig' => (!strlen ($rsconfig) ? NULL : $rsconfig)
		)
	);
	produceTagsForLastRecord ('ipv4rspool', $taglist);
}

function commitDeleteRSPool ($pool_id = 0)
{
	releaseFiles ('ipv4rspool', $pool_id);
	destroyTagsForEntity ('ipv4rspool', $pool_id);
	usePreparedDeleteBlade ('IPv4RSPool', array ('id' => $pool_id));
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
		'lb.vsconfig as lb_vsconfig, lb.rsconfig as lb_rsconfig, lb.prio as prio, pool.id as pool_id, pool.name as pool_name, ' .
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
			foreach (array ('vip', 'vport', 'proto', 'vs_name', 'vs_vsconfig', 'vs_rsconfig', 'lb_vsconfig', 'lb_rsconfig', 'pool_vsconfig', 'pool_rsconfig', 'pool_id', 'pool_name', 'prio') as $c)
				$ret[$vs_id][$c] = $row[$c];
			$ret[$vs_id]['rslist'] = array();
		}
		foreach (array ('rsip', 'rsport', 'rs_rsconfig') as $c)
			$ret[$vs_id]['rslist'][$row['rs_id']][$c] = $row[$c];
	}
	return $ret;
}

function commitUpdateSLBDefConf ($data)
{
	saveScript('DefaultVSConfig', $data['vs']);
	saveScript('DefaultRSConfig', $data['rs']);
}

function getSLBDefaults ($do_cache_result = FALSE) {
	static $ret = array();

	if (! $do_cache_result)
		$ret = array();
	elseif (! empty ($ret))
		return $ret;

	$ret['vs'] = loadScript('DefaultVSConfig');
	$ret['rs'] = loadScript('DefaultRSConfig');
	return $ret;
}

function commitSetInService ($rs_id = 0, $inservice = '')
{
	if (! in_array ($inservice, array ('yes', 'no')))
		throw new InvalidArgException ('$inservice', $inservice);
	usePreparedUpdateBlade ('IPv4RS', array ('inservice' => $inservice), array ('id' => $rs_id));
}

function executeAutoPorts ($object_id = 0, $type_id = 0)
{
	if ($object_id == 0)
		throw new InvalidArgException ('$object_id', $object_id);
	if ($type_id == 0)
		throw new InvalidArgException ('$type_id', $type_id);
	foreach (getAutoPorts ($type_id) as $autoport)
		commitAddPort ($object_id, $autoport['name'], $autoport['type'], '', '');
}

// Return only explicitly listed tags, the rest of the chain will be
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
	return $ret;
}

# Universal autotags generator, a complementing function for loadEntityTags().
# Bypass key isn't strictly typed, but interpreted depending on the realm.
function generateEntityAutoTags ($cell)
{
	$ret = array();
	if (! array_key_exists ('realm', $cell))
		throw new InvalidArgException ('cell', '(array)', 'malformed structure');
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
			if (isset ($cell['8021q_domain_id']))
			{
				$ret[] = array ('tag' => '$runs_8021Q');
				$ret[] = array ('tag' => '$8021Q_domain_' . $cell['8021q_domain_id']);
				if (isset ($cell['8021q_template_id']))
					$ret[] = array ('tag' => '$8021Q_tpl_' . $cell['8021q_template_id']);
			}

			# dictionary attribute autotags '$attr_X_Y'
			$attrs = getAttrValues($cell['id']);
			foreach ($attrs as $attr_id => $attr_record)
				if (isset ($attr_record['key']))
					$ret[] = array ('tag' => "\$attr_{$attr_id}_{$attr_record['key']}");
			break;
		case 'ipv4net':
			$ret[] = array ('tag' => '$ip4netid_' . $cell['id']);
			$ret[] = array ('tag' => '$ip4net-' . str_replace ('.', '-', $cell['ip']) . '-' . $cell['mask']);
			if ($cell['vlanc'])
				$ret[] = array ('tag' => '$runs_8021Q');
			for ($i = 8; $i < 32; $i++)
			{
				# these conditions hit 1 to 3 times per each i
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
		case 'ipv6net':
			$ret[] = array ('tag' => '$ip6netid_' . $cell['id']);
			if ($cell['vlanc'])
				$ret[] = array ('tag' => '$runs_8021Q');
			$ret[] = array ('tag' => '$any_ip6net');
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
			# {$username_XXX} autotag is generated always, but {$userid_XXX}
			# appears only for accounts, which exist in local database.
			$ret[] = array ('tag' => '$username_' . $cell['user_name']);
			if (isset ($cell['user_id']))
				$ret[] = array ('tag' => '$userid_' . $cell['user_id']);
			break;
		case 'file':
			$ret[] = array ('tag' => '$fileid_' . $cell['id']);
			$ret[] = array ('tag' => '$any_file');
			break;
		case 'vst':
			$ret[] = array ('tag' => '$vstid_' . $cell['id']);
			$ret[] = array ('tag' => '$any_vst');
			break;
		default:
			throw new InvalidArgException ('cell', '(array)', 'this input does not belong here');
			break;
	}
	# {$tagless} doesn't apply to users
	switch ($cell['realm'])
	{
		case 'rack':
		case 'object':
		case 'ipv4net':
		case 'ipv6net':
		case 'ipv4vs':
		case 'ipv4rspool':
		case 'file':
		case 'vst':
			if (!count ($cell['etags']))
				$ret[] = array ('tag' => '$untagged');
			break;
		default:
			break;
	}
	return $ret;
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
			// introduce the 'pseudo'-ream 'ipnet' which combines 'ipv4net' and 'ipv6net' realms.
			if ($row['realm'] == 'ipv4net' || $row['realm'] == 'ipv6net')
				if (isset ($ret[$row['id']]['refcnt']['ipnet']))
					$ret[$row['id']]['refcnt']['ipnet'] += $row['refcnt'];
				else
					$ret[$row['id']]['refcnt']['ipnet'] = $row['refcnt'];
		}
	}
	return $ret;
}

// Drop the whole chain stored.
function destroyTagsForEntity ($entity_realm, $entity_id)
{
	usePreparedDeleteBlade ('TagStorage', array ('entity_realm' => $entity_realm, 'entity_id' => $entity_id));
}

// Drop only one record. This operation doesn't involve retossing other tags, unlike when adding.
// FIXME: this function could be used by 3rd-party scripts, dismiss it at some later point,
// but not now.
function deleteTagForEntity ($entity_realm, $entity_id, $tag_id)
{
	usePreparedDeleteBlade ('TagStorage', array ('entity_realm' => $entity_realm, 'entity_id' => $entity_id, 'tag_id' => $tag_id));
}

// Push a record into TagStorage unconditionally.
function addTagForEntity ($realm, $entity_id, $tag_id)
{
	global $SQLSchema;
	global $remote_username;
	if (! array_key_exists ($realm, $SQLSchema))
		throw new InvalidArgException ('realm', $realm);
	// spotEntity ($realm, $entity_id) would be a more expensive way
	// to validate two parameters
	usePreparedExecuteBlade
	(
		'INSERT INTO TagStorage (entity_realm, entity_id, tag_id, user, date) VALUES (?, ?, ?, ?, NOW())',
		array
		(
			$realm,
			$entity_id,
			$tag_id,
			$remote_username,
		)
	);
}

// Add records into TagStorage, if this makes sense (IOW, they don't appear
// on the implicit list already). Then remove any other records, which
// appear on the "implicit" side of the chain. This will make sure,
// that both the tag base is still minimal and all requested tags appear on
// the resulting tag chain.
// Return TRUE, if any changes were committed.
function rebuildTagChainForEntity ($realm, $entity_id, $extrachain = array(), $replace = FALSE)
{
	// Put the current explicit sub-chain into a buffer and merge all tags from
	// the extra chain, which aren't there yet.
	$oldchain = array();
	$newchain = array();
	foreach (loadEntityTags ($realm, $entity_id) as $oldtag)
		$oldchain[$oldtag['id']] = $oldtag;
	$tmpchain = $replace ? array() : $oldchain;
	foreach ($extrachain as $extratag)
		$tmpchain[$extratag['id']] = $extratag;
	// minimize the working buffer
	foreach (getExplicitTagsOnly ($tmpchain) as $taginfo)
		$newchain[$taginfo['id']] = $taginfo;

	$result = FALSE;
	foreach (array_diff (array_keys($oldchain), array_keys ($newchain)) as $tag_id)
	{
		deleteTagForEntity ($realm, $entity_id, $tag_id);
		$result = TRUE;
	}
	foreach (array_diff (array_keys($newchain), array_keys ($oldchain)) as $tag_id)
	{
		addTagForEntity ($realm, $entity_id, $tag_id);
		$result = TRUE;
	}
	return $result;
}

// Presume, that the target record has no tags attached.
function produceTagsForLastRecord ($realm, $tagidlist, $last_insert_id = 0)
{
	if (!$last_insert_id)
		$last_insert_id = lastInsertID();
	foreach (getExplicitTagsOnly (buildTagChainFromIds ($tagidlist)) as $taginfo)
		addTagForEntity ($realm, $last_insert_id, $taginfo['id']);
}

function createIPv4Prefix ($range = '', $name = '', $is_bcast = FALSE, $taglist = array(), $vlan_ck = NULL)
{
	// $range is in x.x.x.x/x format, split into ip/mask vars
	$rangeArray = explode('/', $range);
	if (count ($rangeArray) != 2)
		throw new InvalidRequestArgException ('range', $range, 'Invalid IPv4 prefix');
	$ip = $rangeArray[0];
	$mask = $rangeArray[1];

	if (!strlen ($ip) or !strlen ($mask))
		throw new InvalidRequestArgException ('range', $range, 'Invalid IPv4 prefix');
	$ipL = ip2long($ip);
	$maskL = ip2long($mask);
	if ($ipL == -1 || $ipL === FALSE)
		throw new InvalidRequestArgException ('range', $range, 'Invalid IPv4 address');
	if ($mask < 32 && $mask > 0)
		$maskL = $mask;
	else
	{
		$maskB = decbin($maskL);
		if (strlen($maskB)!=32)
			throw new InvalidRequestArgException ('range', $range, 'Invalid netmask');
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
					throw new InvalidRequestArgException ('range', $range, 'Invalid netmask');
			}
		}
		$maskL = $ones;
	}
	$binmask = binMaskFromDec($maskL);
	$ipL = $ipL & $binmask;
	usePreparedInsertBlade
	(
		'IPv4Network',
		array
		(
			'ip' => sprintf ('%u', $ipL),
			'mask' => $maskL,
			'name' => $name
		)
	);
	$network_id = lastInsertID();

	if ($is_bcast and $maskL < 31)
	{
		$network_addr = long2ip ($ipL);
		$broadcast_addr = long2ip ($ipL | binInvMaskFromDec ($maskL));
		updateV4Address ($network_addr, 'network', 'yes');
		updateV4Address ($broadcast_addr, 'broadcast', 'yes');
	}
	produceTagsForLastRecord ('ipv4net', $taglist);
	if ($vlan_ck != NULL)
	{
		$ctx = getContext();
		fixContext (spotEntity ('ipv4net', $network_id));
		if (permitted ('ipv4net', '8021q', 'bind'))
			commitSupplementVLANIPv4 ($vlan_ck, $network_id);
		restoreContext ($ctx);
	}
	return $network_id;
}

function createIPv6Prefix ($range = '', $name = '', $is_connected = FALSE, $taglist = array(), $vlan_ck = NULL)
{
	// $range is in aaa0:b::c:d/x format, split into ip/mask vars
	$rangeArray = explode ('/', $range);
	if (count ($rangeArray) != 2)
		throw new InvalidRequestArgException ('range', $range, 'Invalid IPv6 prefix');
	$ip = $rangeArray[0];
	$mask = $rangeArray[1];
	$address = new IPv6Address;
	if (!strlen ($ip) or !strlen ($mask) or ! $address->parse ($ip))
		throw new InvalidRequestArgException ('range', $range, 'Invalid IPv6 prefix');
	$network_addr = $address->get_first_subnet_address ($mask);
	$broadcast_addr = $address->get_last_subnet_address ($mask);
	if (! $network_addr || ! $broadcast_addr)
		throw new InvalidRequestArgException ('range', $range, 'Invalid netmask');
	usePreparedInsertBlade
	(
		'IPv6Network',
		array
		(
			'ip' => $network_addr->getBin(),
			'last_ip' => $broadcast_addr->getBin(),
			'mask' => $mask,
			'name' => $name
		)
	);
	$network_id = lastInsertID();
	# RFC3513 2.6.1 - Subnet-Router anycast
	if ($is_connected)
		updateV6Address ($network_addr, 'Subnet-Router anycast', 'yes');
	produceTagsForLastRecord ('ipv6net', $taglist);
	if ($vlan_ck != NULL)
	{
		$ctx = getContext();
		fixContext (spotEntity ('ipv6net', $network_id));
		if (permitted ('ipv6net', '8021q', 'bind'))
			commitSupplementVLANIPv6 ($vlan_ck, $network_id);
		restoreContext ($ctx);
	}
	return $network_id;
}

// FIXME: This function doesn't wipe relevant records from IPv4Address table.
function destroyIPv4Prefix ($id)
{
	releaseFiles ('ipv4net', $id);
	usePreparedDeleteBlade ('IPv4Network', array ('id' => $id));
	destroyTagsForEntity ('ipv4net', $id);
}

// FIXME: This function doesn't wipe relevant records from IPv6Address table.
function destroyIPv6Prefix ($id)
{
	releaseFiles ('ipv6net', $id);
	usePreparedDeleteBlade ('IPv6Network', array ('id' => $id));
	destroyTagsForEntity ('ipv6net', $id);
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
	usePreparedExecuteBlade
	(
		'INSERT INTO Script (script_name, script_text) VALUES (?, ?) ' .
		'ON DUPLICATE KEY UPDATE script_text=?',
		array ($name, $text, $text)
	);
}

function newPortForwarding ($object_id, $localip, $localport, $remoteip, $remoteport, $proto, $description)
{
	if (NULL === getIPv4AddressNetworkId ($localip))
		throw new InvalidArgException ('localip', $localip, "Non existant ip");
	if (NULL === getIPv4AddressNetworkId ($remoteip))
		throw new InvalidArgException ('remoteip', $remoteip, "Non existant ip");
	if ( ($localport <= 0) or ($localport >= 65536) )
		throw new InvalidArgException ('localport', $localport, "invaild port");
	if ( ($remoteport <= 0) or ($remoteport >= 65536) )
		throw new InvalidArgException ('remoteport', $remoteport, "invaild port");

	usePreparedExecuteBlade
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
}

function deletePortForwarding ($object_id, $localip, $localport, $remoteip, $remoteport, $proto)
{
	usePreparedExecuteBlade
	(
		'DELETE FROM IPv4NAT WHERE object_id=? AND localip=INET_ATON(?) AND ' .
		'remoteip=INET_ATON(?) AND localport=? AND remoteport=? AND proto=?',
		array ($object_id, $localip, $remoteip, $localport, $remoteport, $proto)
	);
}

function updatePortForwarding ($object_id, $localip, $localport, $remoteip, $remoteport, $proto, $description)
{
	usePreparedExecuteBlade
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
		"Object.name as object_name, ".
		"description ".
		"from ((IPv4NAT join IPv4Allocation on remoteip=IPv4Allocation.ip) join Object on IPv4NAT.object_id=Object.id) ".
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

function getFile ($file_id)
{
	$query = usePreparedSelectBlade
	(
		'SELECT id, name, type, size, ctime, mtime, atime, contents, comment ' .
		'FROM File WHERE id = ?',
		array ($file_id)
	);
	if (($row = $query->fetch (PDO::FETCH_ASSOC)) == NULL)
		// FIXME: isn't this repeating the code already in spotEntity()?
		throw new EntityNotFoundException ('file', $file_id);
	return $row;
}

function getFileCache ($file_id)
{
	$query = usePreparedSelectBlade
	(
		'SELECT File.thumbnail FROM File ' .
		'WHERE File.id = ? and File.thumbnail IS NOT NULL',
		array ($file_id)
	);
	if (($row = $query->fetch (PDO::FETCH_ASSOC)) == NULL)
		return FALSE;
	return $row['thumbnail'];
}

function commitAddFileCache ($file_id, $contents)
{               
	global $dbxlink;
	try     
	{
		$query = $dbxlink->prepare ('UPDATE File SET thumbnail = ? WHERE id = ?');
		$query->bindParam (1, $contents, PDO::PARAM_LOB);
		$query->bindParam (2, $file_id);
		return $query->execute();
	}
	catch (PDOException $e)
	{
		throw convertPDOException ($e);
	}
}               

function getFileLinks ($file_id)
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
			case 'ipv6net':
				$page = $row['entity_type'];
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

function commitAddFile ($name, $type, $contents, $comment)
{
	global $dbxlink;
	try
	{
		$query = $dbxlink->prepare ('INSERT INTO File (name, type, ctime, mtime, atime, contents, comment) VALUES (?, ?, NOW(), NOW(), NOW(), ?, ?)');
		$query->bindParam (1, $name);
		$query->bindParam (2, $type);
		$query->bindParam (3, $contents, PDO::PARAM_LOB);
		$query->bindParam (4, $comment);
		$query->execute();
		usePreparedExecuteBlade ('UPDATE File SET size = LENGTH(contents) WHERE id = ?', array (lastInsertID()));
	}
	catch (PDOException $e)
	{
		throw convertPDOException ($e);
	}
}

function commitReplaceFile ($file_id = 0, $contents)
{
	global $dbxlink;
	$query = $dbxlink->prepare('UPDATE File SET mtime = NOW(), contents = ?, size = LENGTH(contents), thumbnail = NULL WHERE id = ?');
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

function commitUnlinkFile ($link_id)
{
	usePreparedDeleteBlade ('FileLink', array ('id' => $link_id));
}

function commitDeleteFile ($file_id)
{
	destroyTagsForEntity ('file', $file_id);
	usePreparedDeleteBlade ('File', array ('id' => $file_id));
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
	usePreparedExecuteBlade ('UPDATE LDAPCache SET last_retry=NOW() WHERE presented_username=?', array ($form_username));
}

function replaceLDAPCacheRecord ($form_username, $password_hash, $dname, $memberof)
{
	// FIXME: This sequence is able to trigger a deadlock, namely, when executed
	// in parallel from multiple working copies of the same user, which for some
	// reason has no valid record in LDAPCache. Perhaps, using REPLACE INTO can
	// lower the chances of this.
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
	usePreparedDeleteBlade ('LDAPCache', array ('presented_username' => $form_username));
}

// Age all records older, than cache_expiry seconds, and all records made in future.
// Calling this function w/o argument purges the whole LDAP cache.
function discardLDAPCache ($maxage = 0)
{
	usePreparedExecuteBlade ('DELETE from LDAPCache WHERE TIMESTAMPDIFF(SECOND, first_success, NOW()) >= ? or NOW() < first_success', array ($maxage));
}

function getUserIDByUsername ($username)
{
	$result = usePreparedSelectBlade ('SELECT user_id FROM UserAccount WHERE user_name = ?', array ($username));
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
		return $row['user_id'];
	return NULL;
}

# Derive a complete cell structure from the given username regardless
# if it is a local account or not.
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

function getPortTypeUsageStatistics()
{
	$result = usePreparedSelectBlade
	(
		'SELECT p.iif_id, p.type, COUNT(p.id) AS count FROM Port p INNER JOIN Link l '.
		'ON (p.id = l.porta or p.id = l.portb) WHERE p.type <> 0 GROUP BY iif_id, type'
	);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['iif_id'] . '-' . $row['type']] = $row['count'];
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
	usePreparedInsertBlade
	(
		'PortInterfaceCompat',
		array ('iif_id' => $iif_id, 'oif_id' => $oif_id)
	);
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
		array_slice ($args, 0, 1) // array with only the first argument
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function getPortInfo ($port_id)
{
	$result = fetchPortList ('Port.id = ?', array ($port_id));
	return empty ($result) ? NULL : $result[0];
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
		'ifNull(NETS4.C, 0) + ifNull(NETS6.C, 0) as netc, ' .
		'ifNull(PORTS.C, 0) as portc ' .
		'FROM VLANDescription AS VD ' .
		'LEFT JOIN (SELECT vlan_id, COUNT(ipv4net_id) AS C FROM VLANIPv4 WHERE domain_id = ? GROUP BY domain_id, vlan_id) AS NETS4 USING(vlan_id) ' .
		'LEFT JOIN (SELECT vlan_id, COUNT(ipv6net_id) AS C FROM VLANIPv6 WHERE domain_id = ? GROUP BY domain_id, vlan_id) AS NETS6 USING(vlan_id) ' .
		'LEFT JOIN ' .
		'(   SELECT PAV.vlan_id AS vlan_id, COUNT(port_name) AS C ' .
		'    FROM VLANSwitch AS VS ' .
		'    INNER JOIN PortAllowedVLAN AS PAV ON VS.object_id = PAV.object_id ' .
		'    WHERE VS.domain_id = ? ' . 
		'    GROUP BY VS.domain_id, PAV.vlan_id ' .
		') AS PORTS USING(vlan_id) ' .
		'WHERE domain_id = ? ' .
		'ORDER BY vlan_id',
		array ($vdom_id, $vdom_id, $vdom_id, $vdom_id)
	);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['vlan_id']] = $row;
	return $ret;
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
	$ret['ipv6nets'] = array();
	unset ($result);
	$query = 'SELECT ipv4net_id FROM VLANIPv4 WHERE domain_id = ? AND vlan_id = ? ORDER BY ipv4net_id';
	$result = usePreparedSelectBlade ($query, array ($vdom_id, $vlan_id));
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret['ipv4nets'][] = $row['ipv4net_id'];
	unset ($result);
	$query = 'SELECT ipv6net_id FROM VLANIPv6 WHERE domain_id = ? AND vlan_id = ? ORDER BY ipv6net_id';
	$result = usePreparedSelectBlade ($query, array ($vdom_id, $vlan_id));
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret['ipv6nets'][] = $row['ipv6net_id'];

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

// return list of network IDs, which are not bound to the given VLAN domain
function getVLANIPv6Options ($except_vdid)
{
	$ret = array();
	$prepared = usePreparedSelectBlade
	(
		'SELECT id FROM IPv6Network WHERE id NOT IN ' .
		'(SELECT ipv6net_id FROM VLANIPv6 WHERE domain_id = ?)' .
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
	usePreparedInsertBlade
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

function commitSupplementVLANIPv6 ($vlan_ck, $ipv6net_id)
{
	list ($vdom_id, $vlan_id) = decodeVLANCK ($vlan_ck);
	usePreparedInsertBlade
	(
		'VLANIPv6',
		array
		(
			'domain_id' => $vdom_id,
			'vlan_id' => $vlan_id,
			'ipv6net_id' => $ipv6net_id,
		)
	);
}

function commitReduceVLANIPv4 ($vlan_ck, $ipv4net_id)
{
	list ($vdom_id, $vlan_id) = decodeVLANCK ($vlan_ck);
	usePreparedDeleteBlade
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

function commitReduceVLANIPv6 ($vlan_ck, $ipv6net_id)
{
	list ($vdom_id, $vlan_id) = decodeVLANCK ($vlan_ck);
	usePreparedDeleteBlade
	(
		'VLANIPv6',
		array
		(
			'domain_id' => $vdom_id,
			'vlan_id' => $vlan_id,
			'ipv6net_id' => $ipv6net_id,
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
	usePreparedInsertBlade
	(
		$tablemap_8021q['cached']['pvm'],
		array ('object_id' => $object_id, 'port_name' => $port_name, 'vlan_mode' => $port['mode'])
	);
	usePreparedInsertBlade
	(
		$tablemap_8021q['desired']['pvm'],
		array ('object_id' => $object_id, 'port_name' => $port_name, 'vlan_mode' => $port['mode'])
	);
	upd8021QPort ('cached', $object_id, $port_name, $port);
	upd8021QPort ('desired', $object_id, $port_name, $port);
}

function del8021QPort ($object_id, $port_name)
{
	// rely on ON DELETE CASCADE for PortAllowedVLAN and PortNativeVLAN
	global $tablemap_8021q;
	usePreparedDeleteBlade
	(
		$tablemap_8021q['desired']['pvm'],
		array ('object_id' => $object_id, 'port_name' => $port_name)
	);
	usePreparedDeleteBlade
	(
		$tablemap_8021q['cached']['pvm'],
		array ('object_id' => $object_id, 'port_name' => $port_name)
	);
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
	usePreparedUpdateBlade
	(
		$tablemap_8021q[$instance]['pvm'],
		array ('vlan_mode' => $port['mode']),
		array ('object_id' => $object_id, 'port_name' => $port_name)
	);
	usePreparedDeleteBlade (
		$tablemap_8021q[$instance]['pav'],
		array ('object_id' => $object_id, 'port_name' => $port_name)
	);
	// The goal is to INSERT as many rows as there are values in 'allowed' list
	// without wrapping each row with own INSERT (otherwise the SQL connection
	// instantly becomes the bottleneck).
	foreach (listToRanges ($port['allowed']) as $range)
		usePreparedExecuteBlade
		(
			'INSERT INTO ' . $tablemap_8021q[$instance]['pav'] . ' (object_id, port_name, vlan_id) ' .
			'SELECT ?, ?, vlan_id FROM VLANValidID WHERE vlan_id BETWEEN ? AND ?',
			array ($object_id, $port_name, $range['from'], $range['to'])
		);
	if
	(
		$port['native'] and
		in_array ($port['native'], $port['allowed'])
	)
		usePreparedInsertBlade
		(
			$tablemap_8021q[$instance]['pnv'],
			array ('object_id' => $object_id, 'port_name' => $port_name, 'vlan_id' => $port['native'])
		);
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
		{
			upd8021QPort ($instance, $object_id, $port_name, $port);
			$done++;
		}
	return $done;
}

function commitUpdateVSTRules ($vst_id, $mutex_rev, $rules)
{
	global $dbxlink, $remote_username;
	$dbxlink->beginTransaction();
	$result = usePreparedSelectBlade
	(
		'SELECT mutex_rev, saved_by FROM VLANSwitchTemplate ' .
		'WHERE id = ? FOR UPDATE',
		array ($vst_id)
	);
	$vst = $result->fetch (PDO::FETCH_ASSOC);
	unset ($result);
	if ($vst['mutex_rev'] != $mutex_rev)
		throw new InvalidRequestArgException ('mutex_rev', $mutex_rev, "already saved by ${vst['saved_by']}");
	usePreparedDeleteBlade ('VLANSTRule', array ('vst_id' => $vst_id));
	foreach ($rules as $rule)
		usePreparedInsertBlade ('VLANSTRule', array_merge (array ('vst_id' => $vst_id), $rule));
	usePreparedExecuteBlade ('UPDATE VLANSwitchTemplate SET mutex_rev=mutex_rev+1, saved_by=? WHERE id=?', array ($remote_username, $vst_id));
	$dbxlink->commit();
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

function getIPv6Network8021QBindings ($ipv6net_id)
{
	$prepared = usePreparedSelectBlade
	(
		'SELECT domain_id, vlan_id FROM VLANIPv6 ' .
		'WHERE ipv6net_id = ? ORDER BY domain_id',
		array ($ipv6net_id)
	);
	return $prepared->fetchAll (PDO::FETCH_ASSOC);
}

// Return entity ID, if its 'name' column equals to provided string, or NULL otherwise (nothing
// found or more, than one row returned by query due to some odd reason).
function lookupEntityByString ($realm, $value, $column = 'name')
{
	global $SQLSchema;
	if (!isset ($SQLSchema[$realm]))
		throw new InvalidArgException ('realm', $realm);
	$SQLinfo = $SQLSchema[$realm];
	$query = "SELECT ${SQLinfo['keycolumn']} AS id FROM ${SQLinfo['table']} WHERE ${SQLinfo['table']}.${column}=? LIMIT 2";
	$result = usePreparedSelectBlade ($query, array ($value));
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);	
	if (count ($rows) != 1)
		return NULL;
	return $rows[0]['id'];
}

// returns an array of attribute_id`s wich use specified chapter id.  
function getChapterAttributes($chapter_id)
{
	$prepared = usePreparedSelectBlade ('SELECT DISTINCT attr_id FROM AttributeMap WHERE chapter_id = ?', array ($chapter_id));
	$rows = $prepared->fetchAll (PDO::FETCH_COLUMN);
	return is_array($rows) ? $rows : array();
}

function getLogRecordsForObject ($object_id)
{
	$result = usePreparedSelectBlade ('SELECT id, content, date, user FROM ObjectLog WHERE object_id = ? ORDER BY date DESC', array ($object_id));
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function getLogRecords()
{
	$result = usePreparedSelectBlade
	(
		'SELECT OL.id AS log_id, O.objtype_id, O.name, OL.content, OL.date, OL.user, O.id AS object_id ' .
		'FROM ObjectLog OL LEFT JOIN Object O ON OL.object_id = O.id ' .
		'ORDER BY OL.date DESC'
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// In softfail mode die only on fatal errors, letting the user check
// and resubmit his input.
function setConfigVar ($varname = '', $varvalue = '', $softfail = FALSE)
{
	global $configCache;
	if (!isset ($configCache))
		throw new RackTablesError ('configuration cache is unavailable', RackTablesError::INTERNAL);
	if
	(
		$varname == ''
		or ! array_key_exists ($varname, $configCache)
	)
		throw new InvalidArgException ('$varname', $varname);
	if ($configCache[$varname]['is_hidden'] != 'no')
		throw new InvalidRequestArgException ('$varname', $varname, 'a hidden variable cannot be changed by user');
	if (!mb_strlen ($varvalue) && $configCache[$varname]['emptyok'] != 'yes')
		throw new InvalidRequestArgException ('$varvalue', $varvalue, "'${varname}' is required to have a non-empty value");
	if (mb_strlen ($varvalue) && $configCache[$varname]['vartype'] == 'uint' && (!is_numeric ($varvalue) or $varvalue < 0 ))
		throw new InvalidRequestArgException ('$varvalue', $varvalue, "'${varname}' can accept UINT values only");
	// Update cache only if the changes went into DB.
	usePreparedUpdateBlade ('Config', array ('varvalue' => $varvalue), array ('varname' => $varname));
	$configCache[$varname]['varvalue'] = $varvalue;
}

function setUserConfigVar ($varname = '', $varvalue = '')
{
	global $configCache;
	global $remote_username;
	if (!isset ($configCache))
		throw new RackTablesError ('configuration cache is unavailable', RackTablesError::INTERNAL);
	if
	(
		$varname == ''
		or ! array_key_exists ($varname, $configCache)
	)
		throw new InvalidArgException ('$varname', $varname);
	if ($configCache[$varname]['is_userdefined'] != 'yes')
		throw new InvalidRequestArgException ('$varname', $varname, 'a system-wide setting cannot be changed by user');
	if ($configCache[$varname]['is_hidden'] != 'no')
		throw new InvalidRequestArgException ('$varname', $varname, 'a hidden variable cannot be changed by user');
	if (!mb_strlen ($varvalue) && $configCache[$varname]['emptyok'] != 'yes')
		throw new InvalidRequestArgException ('$varvalue', $varvalue, "'${varname}' is required to have a non-empty value");
	if (mb_strlen ($varvalue) && $configCache[$varname]['vartype'] == 'uint' && (!is_numeric ($varvalue) or $varvalue < 0 ))
		throw new InvalidRequestArgException ('$varvalue', $varvalue, "'${varname}' can accept UINT values only");
	// Update cache only if the changes went into DB.
	usePreparedExecuteBlade
	(
		'REPLACE UserConfig SET varvalue=?, varname=?, user=?',
		array ($varvalue, $varname, $remote_username)
	);
	$configCache[$varname]['varvalue'] = $varvalue;
}

function resetUserConfigVar ($varname = '')
{
	global $configCache;
	global $remote_username;
	if (!isset ($configCache))
		throw new RackTablesError ('configuration cache is unavailable', RackTablesError::INTERNAL);
	if
	(
		$varname == ''
		or ! array_key_exists ($varname, $configCache)
	)
		throw new InvalidArgException ('$varname', $varname);
	if ($configCache[$varname]['is_userdefined'] != 'yes')
		throw new InvalidRequestArgException ('$varname', $varname, 'a system-wide setting cannot be changed by user');
	if ($configCache[$varname]['is_hidden'] != 'no')
		throw new InvalidRequestArgException ('$varname', $varname, 'a hidden variable cannot be changed by user');
	// Update cache only if the changes went into DB.
	usePreparedDeleteBlade ('UserConfig', array ('varname' => $varname, 'user' => $remote_username));
}

// parses QUICK_LINK_PAGES config var and returns array with ('href'=>..., 'title'=>...) items
function getConfiguredQuickLinks()
{
	$ret = array();
	foreach (explode (',', getConfigVar('QUICK_LINK_PAGES')) as $page_code)
		if (! empty ($page_code))
		{
			$title = getPageName ($page_code);
			if (! empty ($title))
				$ret[] = array ('href' => makeHref (array ('page' => $page_code)), 'title' => $title);
		}
	return $ret;
}

function getCactiGraphsForObject ($object_id)
{
	$result = usePreparedSelectBlade ('SELECT graph_id, caption FROM CactiGraph WHERE object_id = ? ORDER BY graph_id', array ($object_id));
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC), 'graph_id');
}

?>
