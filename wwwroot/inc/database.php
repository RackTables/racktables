<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

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
			'rack_id' => '(SELECT MIN(rack_id) FROM RackSpace WHERE object_id = RackObject.id)',
			'rack_id_2' => "(SELECT MIN(parent_entity_id) FROM EntityLink WHERE child_entity_type='object' AND child_entity_id = RackObject.id AND parent_entity_type = 'rack')",
			'container_id' => "(SELECT MIN(parent_entity_id) FROM EntityLink WHERE child_entity_type='object' AND child_entity_id = RackObject.id AND parent_entity_type = 'object')",
			'container_name' => '(SELECT name FROM RackObject WHERE id = container_id)',
			'container_objtype_id' => '(SELECT objtype_id FROM RackObject WHERE id = container_id)',
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
			'ip_bin' => 'ip',
			'mask' => 'mask',
			'name' => 'name',
			'comment' => 'comment',
			'8021q' => '(SELECT GROUP_CONCAT(CONCAT(domain_id, "-", vlan_id) ORDER BY domain_id, vlan_id) FROM VLANIPv4 WHERE ipv4net_id = id)',
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
			'8021q' => '(SELECT GROUP_CONCAT(CONCAT(domain_id, "-", vlan_id) ORDER BY domain_id, vlan_id) FROM VLANIPv6 WHERE ipv6net_id = id)',
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
			'vip_bin' => 'vip',
			'vport' => 'vport',
			'proto' => 'proto',
			'name' => 'name',
			'vsconfig' => 'vsconfig',
			'rsconfig' => 'rsconfig',
			'refcnt' => '(select count(vs_id) from IPv4LB where vs_id = id)',
			//'vip' =>
			//'dname' =>
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('IPv4VS.vip', 'IPv4VS.proto', 'IPv4VS.vport'),
	),
	'ipvs' => array
	(
		'table' => 'VS',
		'columns' => array
		(
			'id' => 'id',
			'name' => 'name',
			'vsconfig' => 'vsconfig',
			'rsconfig' => 'rsconfig',
		),
		'keycolumn' => 'id',
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
			'location_id' => 'location_id',
			'location_name' => 'location_name',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('location_name', 'row_name', 'sort_order', 'Rack.name'),
		'pidcolumn' => 'row_id',
	),
	'row' => array
	(
		'table' => 'Row',
		'columns' => array
		(
			'id' => 'id',
			'name' => 'name',
			'location_id' => 'location_id',
			'location_name' => 'location_name',
			'rackc' => '(select count(Rack.id) from Rack where row_id = Row.id)',
		),
		'keycolumn' => 'id',
		'ordcolumns' => array ('location_name', 'name'),
	),
	'location' => array
	(
		'table' => 'Location',
		'columns' => array
		(
			'id' => 'id',
			'name' => 'name',
			'has_problems' => 'has_problems',
			'comment' => 'comment',
			'parent_id' => 'parent_id',
			'parent_name' => 'parent_name',
			'refcnt' => "(SELECT COUNT(child_entity_id) FROM EntityLink EL WHERE EL.parent_entity_type = 'location' AND EL.parent_entity_id = Location.id)",
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

//  flags to pass to scanIPSpace, scanIPv4Space, scanIPv6Space
define ('IPSCAN_DO_ADDR', 1 << 0);
define ('IPSCAN_DO_ALLOCS', 1 << 1);
define ('IPSCAN_DO_VS', 1 << 2);
define ('IPSCAN_DO_RS', 1 << 3);
define ('IPSCAN_DO_NAT', 1 << 4);
define ('IPSCAN_DO_LOG', 1 << 5);
define ('IPSCAN_RTR_ONLY', 1 << 6);

define ('IPSCAN_ANY', -1 ^ IPSCAN_RTR_ONLY);
define ('IPSCAN_DO_SLB', IPSCAN_DO_VS | IPSCAN_DO_RS);

$object_attribute_cache = array();

// Return list of locations directly under a specified location
function getLocations ($location_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, name FROM Location WHERE parent_id = ? ORDER BY name',
		array ($location_id)
	);
	return reduceSubarraysToColumn (reindexById ($result->fetchAll (PDO::FETCH_ASSOC)), 'name');
}

// Return detailed information about one rack row.
function getRowInfo ($row_id)
{
	$query =
		"SELECT Row.id AS id, Row.name AS name, COUNT(Rack.id) AS count, " .
		"IF(ISNULL(SUM(Rack.height)),0,SUM(Rack.height)) AS sum, " .
		"Location.id AS location_id, Location.name AS location " .
		"FROM Row LEFT JOIN Rack ON Rack.row_id = Row.id " .
		"LEFT OUTER JOIN Location ON Row.location_id = Location.id " .
		"WHERE Row.id = ? " .
		"GROUP BY Row.id, Location.id";
	$result = usePreparedSelectBlade ($query, array ($row_id));
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
		return $row;
	throw new EntityNotFoundException ('rackrow', $row_id);
}

// TODO: deprecated function. delete it
function getAllRows ()
{
	return listCells ('row');
}

// Return list of rows directly under a specified location
function getRows ($location_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT R.id, R.name FROM Row R ' .
		'INNER JOIN EntityLink EL ON ' .
		"EL.parent_entity_type = 'location' " .
		"AND EL.child_entity_type = 'row' " .
		"AND EL.child_entity_id = R.id " .
		'WHERE EL.parent_entity_id = ? ' .
		'ORDER BY R.name',
		array ($location_id)
	);
	return reduceSubarraysToColumn (reindexById ($result->fetchAll (PDO::FETCH_ASSOC)), 'name');
}

function getRacks ($row_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, name, asset_no, height, sort_order, comment, row_name FROM Rack WHERE row_id = ? ORDER BY sort_order',
		array ($row_id)
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}

# Return rack and row details for those objects on the list that have
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
		'parent_entity_id AS row_id, Row_.name AS row_name ' .
		'FROM Object Rack_ ' .
		"LEFT JOIN EntityLink ON (Rack_.id = child_entity_id AND parent_entity_type = 'row' AND child_entity_type = 'rack') " .
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

# Return container details for a list of objects
function getContainerInfo ($object_ids)
{
	if (! count ($object_ids))
		return array ();
	$result = usePreparedSelectBlade
	(
		'SELECT EL.child_entity_id, EL.parent_entity_id, RO.name, RO.objtype_id ' .
		'FROM EntityLink EL ' .
		'LEFT JOIN RackObject RO ON EL.parent_entity_id = RO.id ' .
		'WHERE EL.child_entity_id IN (' . questionMarks (count ($object_ids)) . ') ' .
		"AND EL.parent_entity_type = 'object' " .
		"AND EL.child_entity_type = 'object' " .
		'ORDER BY RO.name',
		$object_ids
	);
	$ret = array ();
	foreach ($result as $row)
		$ret[$row['child_entity_id']][] = array
		(
			'container_id'    => $row['parent_entity_id'],
			'container_dname' => formatObjectDisplayedName ($row['name'], $row['objtype_id'])
		);
	unset ($result);
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
	if (isset ($SQLinfo['pidcolumn']) && $parent_id)
	{
		$query .= " WHERE ${SQLinfo['table']}.${SQLinfo['pidcolumn']} = ?";
		$qparams[] = $parent_id;
	}
	if (isset ($SQLinfo['ordcolumns']))
	{
		$query .= " ORDER BY ";
		foreach ($SQLinfo['ordcolumns'] as $oc)
			$query .= "${oc}, ";
		$query = trim($query, ', ');
	}
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
		// use the temporary rack_id_2 key and remove this key from the result array
		if ($realm == 'object')
		{
			if (! isset ($ret[$entity_id]['rack_id']))
				$ret[$entity_id]['rack_id'] = $ret[$entity_id]['rack_id_2'];
			unset ($ret[$entity_id]['rack_id_2']);
		}
	}
	unset($result);

	// select tags and link them to previosly fetched entities
	$query = 'SELECT entity_id, tag_id, user AS tag_user, UNIX_TIMESTAMP(date) AS tag_time FROM TagStorage WHERE entity_realm = ?';
	$result = usePreparedSelectBlade ($query, array($realm));
	global $taglist;
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$tag_id = $row['tag_id'];
		if (array_key_exists($row['entity_id'], $ret))
			$ret[$row['entity_id']]['etags'][$tag_id] = array
			(
				'id' => $tag_id,
				'tag' => $taglist[$tag_id]['tag'],
				'parent_id' => $taglist[$tag_id]['parent_id'],
				'user' => $row['tag_user'],
				'time' => $row['tag_time'],
			);
	}
	unset($result);
	// Add necessary finish to the list before returning it. Maintain caches.
	if (!$parent_id)
		unset ($entityCache['partial'][$realm]);
	if ($realm == 'object') // cache dict attributes of all objects to speed up autotags calculation
		cacheDictAttrValues();
	foreach ($ret as $entity_id => &$entity)
	{
		sortEntityTags ($entity); // changes ['etags'] and ['itags']
		switch ($realm)
		{
		case 'object':
			setDisplayedName ($entity); // set $entity['dname']
			break;
		case 'ipv4net':
			$entity = array_merge ($entity, constructIPRange (ip4_int2bin ($entity['ip_bin']), $entity['mask']));
			processIPNetVlans ($entity);
			$entity['spare_ranges'] = array();
			$entity['kidc'] = 0;
			break;
		case 'ipv6net':
			$entity = array_merge ($entity, constructIPRange ($entity['ip_bin'], $entity['mask']));
			processIPNetVlans ($entity);
			$entity['spare_ranges'] = array();
			$entity['kidc'] = 0;
			break;
		case 'ipv4vs':
			$entity['vip'] = ip_format ($entity['vip_bin']);
			setDisplayedName ($entity); // set $entity['dname']
			$entity['vsconfig'] = dos2unix ($entity['vsconfig']);
			$entity['rsconfig'] = dos2unix ($entity['rsconfig']);
			break;
		case 'ipv4rspool':
			$entity['vsconfig'] = dos2unix ($entity['vsconfig']);
			$entity['rsconfig'] = dos2unix ($entity['rsconfig']);
			break;
		case 'ipvs':
			$entity['vsconfig'] = dos2unix ($entity['vsconfig']);
			$entity['rsconfig'] = dos2unix ($entity['rsconfig']);
			break;
		default:
			break;
		}
	}
	if ($realm == 'ipv4net' || $realm == 'ipv6net')
		fillIPNetsCorrelation ($ret);

	foreach (array_keys ($ret) as $entity_id)
	{
		$entity = &$ret[$entity_id];
		$entity['atags'] = callHook ('generateEntityAutoTags', $entity);
		if (!$parent_id)
			$entityCache['complete'][$realm][$entity_id] = $entity;
		else
			$entityCache['partial'][$realm][$entity_id] = $entity;
	}

	return $ret;
}

// Very much like listCells(), but return only one record requested
// throws an exception if entity not exists
function spotEntity ($realm, $id, $ignore_cache = FALSE)
{
	if (! $ignore_cache)
	{
		global $entityCache;
		if (isset ($entityCache['complete'][$realm]))
		{
			if (isset ($entityCache['complete'][$realm][$id]))
				return $entityCache['complete'][$realm][$id];
		}
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
	$result = usePreparedSelectBlade ($query, array ($realm, $id));
	$ret = array();
	global $taglist;
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		if (!isset ($ret['realm']))
		{
			$ret = array ('realm' => $realm);
			foreach (array_keys ($SQLinfo['columns']) as $alias)
				$ret[$alias] = $row[$alias];
			// use the temporary rack_id_2 key and remove this key from the result array
			if ($realm == 'object')
			{
				if (! isset ($ret['rack_id']))
					$ret['rack_id'] = $ret['rack_id_2'];
				unset ($ret['rack_id_2']);
			}
			$ret['etags'] = array();
			if ($row['tag_id'] != NULL && isset ($taglist[$row['tag_id']]))
				$ret['etags'][$row['tag_id']] = array
				(
					'id' => $row['tag_id'],
					'tag' => $taglist[$row['tag_id']]['tag'],
					'parent_id' => $taglist[$row['tag_id']]['parent_id'],
					'user' => $row['tag_user'],
					'time' => $row['tag_time'],
				);
		}
		elseif (isset ($taglist[$row['tag_id']]))
			$ret['etags'][$row['tag_id']] = array
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
	sortEntityTags ($ret); // changes ['etags'] and ['itags']
	switch ($realm)
	{
	case 'object':
		setDisplayedName ($ret); // set $ret['dname']
		break;
	case 'ipv4net':
		processIPNetVlans ($ret);
		$ret = array_merge ($ret, constructIPRange (ip4_int2bin ($ret['ip_bin']), $ret['mask']));
		if (! fillNetKids ($ret))
		{
			$ret['spare_ranges'] = array();
			$ret['kidc'] = 0;
		}
		break;
	case 'ipv6net':
		processIPNetVlans ($ret);
		$ret = array_merge ($ret, constructIPRange ($ret['ip_bin'], $ret['mask']));
		if (! fillNetKids ($ret))
		{
			$ret['spare_ranges'] = array();
			$ret['kidc'] = 0;
		}
		break;
	case 'ipv4vs':
		$ret['vip'] = ip_format ($ret['vip_bin']);
		setDisplayedName ($ret); // set $ret['dname']
		$ret['vsconfig'] = dos2unix ($ret['vsconfig']);
		$ret['rsconfig'] = dos2unix ($ret['rsconfig']);
		break;
	case 'ipv4rspool':
		$ret['vsconfig'] = dos2unix ($ret['vsconfig']);
		$ret['rsconfig'] = dos2unix ($ret['rsconfig']);
		break;
	case 'ipvs':
		$ret['vsconfig'] = dos2unix ($ret['vsconfig']);
		$ret['rsconfig'] = dos2unix ($ret['rsconfig']);
		break;
	default:
		break;
	}

	$ret['atags'] = generateEntityAutoTags ($ret);
	if (! $ignore_cache)
		$entityCache['partial'][$realm][$id] = $ret;
	return $ret;
}

function fillNetKids (&$net_cell)
{
	if ($net_cell['realm'] == 'ipv6net')
	{
		$table = 'IPv6Network';
		$ip_first = $net_cell['ip_bin'];
		$ip_last = ip_last ($net_cell);
	}
	else
	{
		$table = 'IPv4Network';
		$ip_first = ip4_bin2db ($net_cell['ip_bin']);
		$ip_last = ip4_bin2db (ip_last ($net_cell));
	}

	$result = usePreparedSelectBlade ("
SELECT id, ip as ip_bin, mask FROM $table
WHERE ip BETWEEN ? AND ? AND mask >= ?
ORDER BY ip, mask
", array ($ip_first, $ip_last, $net_cell['mask']));
	$nets = array();
	while ($net_row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = $net_row['ip_bin'];
		if ($net_cell['realm'] == 'ipv4net')
			$ip_bin = ip4_int2bin ($ip_bin);
		$nets[] = constructIPRange ($ip_bin, $net_row['mask']) +
			array(
				'id' => $net_row['id'],
				'spare_ranges' => array(),
				'kidc' => 0,
			);
	}
	unset ($result);

	fillIPNetsCorrelation ($nets, 1);
	if (is_array ($nets[0]) && $nets[0]['id'] == $net_cell['id'])
	{
		$net_cell['spare_ranges'] = $nets[0]['spare_ranges'];
		$net_cell['kidc'] = $nets[0]['kidc'];
		return TRUE;
	}
	return FALSE;
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
		$record['files'] = getFilesOfEntity ($record['realm'], $record['id']);
		break;
	case 'file':
		$record['links'] = getFileLinks ($record['id']);
		break;
	case 'location':
		$record['locations'] = getLocations ($record['id']);
		$record['rows'] = getRows ($record['id']);
		break;
	case 'row':
		$record['racks'] = getRacks ($record['id']);
		break;
	case 'rack':
		// start with default rackspace
		for ($i = $record['height']; $i > 0; $i--)
			for ($locidx = 0; $locidx < 3; $locidx++)
				$record[$i][$locidx]['state'] = 'F';
		// load difference
		$query =
			"select unit_no, atom, state, object_id, has_problems " .
			"from RackSpace LEFT JOIN Object ON Object.id = object_id where rack_id = ? and " .
			"unit_no between 1 and ? order by unit_no";
		$result = usePreparedSelectBlade ($query, array ($record['id'], $record['height']));
		global $loclist;
		$mounted_objects = array();
		// fetch Zero-U mounted objects
		foreach (getChildren ($record, 'object') as $child)
			$mounted_objects[$child['id']] = TRUE;

		$rows = $result->fetchAll (PDO::FETCH_ASSOC);
		unset ($result);
		foreach ($rows as $row)
		{
			$record[$row['unit_no']][$loclist[$row['atom']]]['state'] = $row['state'];
			$record[$row['unit_no']][$loclist[$row['atom']]]['object_id'] = $row['object_id'];
			$record[$row['unit_no']][$loclist[$row['atom']]]['hl'] = $row['has_problems'] == 'yes' ? 'w' : '';
			if ($row['state'] == 'T' && $row['object_id'] != NULL)
				$mounted_objects[$row['object_id']] = TRUE;
		}

		$record['isDeletable'] = (count ($rows) || count ($mounted_objects)) ? FALSE : TRUE;
		$record['mountedObjects'] = array_keys ($mounted_objects);
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
	case 'ipvs':
		$record['ports'] = array();
		$record['vips'] = array();
		$result = usePreparedSelectBlade ("SELECT proto, vport, vsconfig, rsconfig FROM VSPorts WHERE vs_id = ?", array ($record['id']));
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
		{
			$row['vsconfig'] = dos2unix ($row['vsconfig']);
			$row['rsconfig'] = dos2unix ($row['rsconfig']);
			$record['ports'][] = $row;
		}
		unset ($result);
		$result = usePreparedSelectBlade ("SELECT vip, vsconfig, rsconfig FROM VSIPs WHERE vs_id = ?", array ($record['id']));
		while ($row = $result->fetch (PDO::FETCH_ASSOC))
		{
			$row['vsconfig'] = dos2unix ($row['vsconfig']);
			$row['rsconfig'] = dos2unix ($row['rsconfig']);
			$record['vips'][] = $row;
		}
		unset ($result);
		break;
	default:
	}
}

// is called by spotEntity and listCells.
// replaces ['8021q'] text value in cell by an array with 'domain_id' and 'vlan_id' subkeys
// also sets ['vlanc'] cell key to the binded vlans count.
function processIPNetVlans (&$cell)
{
	if (empty ($cell['8021q']))
		$cell['8021q'] = array();
	else
	{
		$ck_list = explode (',', $cell['8021q']);
		$cell['8021q'] = array();
		foreach ($ck_list as $vlan_ck)
		{
			list ($domain_id, $vlan_id) = decodeVLANCK ($vlan_ck);
			$cell['8021q'][] = array ('domain_id' => $domain_id, 'vlan_id' => $vlan_id);
		}
	}
	$cell['vlanc'] = count ($cell['8021q']);
}

function fetchPortList ($sql_where_clause, $query_params = array())
{
	$query = <<<END
SELECT
	Port.id,
	Port.name,
	Port.object_id,
	Object.name AS object_name,
	Port.l2address,
	Port.label,
	Port.reservation_comment,
	Port.iif_id,
	Port.type AS oif_id,
	(SELECT PortInnerInterface.iif_name FROM PortInnerInterface WHERE PortInnerInterface.id = Port.iif_id) AS iif_name,
	(SELECT PortOuterInterface.oif_name FROM PortOuterInterface WHERE PortOuterInterface.id = Port.type) AS oif_name,
	IF(la.porta, la.cable, lb.cable) AS cableid,
	IF(la.porta, pa.id, pb.id) AS remote_id,
	IF(la.porta, pa.name, pb.name) AS remote_name,
	IF(la.porta, pa.object_id, pb.object_id) AS remote_object_id,
	IF(la.porta, oa.name, ob.name) AS remote_object_name,
	(SELECT COUNT(*) FROM PortLog WHERE PortLog.port_id = Port.id) AS log_count,
	PortLog.user,
	UNIX_TIMESTAMP(PortLog.date) as time
FROM
	Port
	INNER JOIN Object ON Port.object_id = Object.id
	LEFT JOIN Link AS la ON la.porta = Port.id
	LEFT JOIN Port AS pa ON pa.id = la.portb
	LEFT JOIN Object AS oa ON pa.object_id = oa.id
	LEFT JOIN Link AS lb on lb.portb = Port.id
	LEFT JOIN Port AS pb ON pb.id = lb.porta
	LEFT JOIN Object AS ob ON pb.object_id = ob.id
	LEFT JOIN PortLog ON PortLog.id = (SELECT id FROM PortLog WHERE PortLog.port_id = Port.id ORDER BY date DESC LIMIT 1)
WHERE
	$sql_where_clause
END;

	$result = usePreparedSelectBlade ($query, $query_params);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$row['l2address'] = l2addressFromDatabase ($row['l2address']);
		$row['linked'] = isset ($row['remote_id']) ? 1 : 0;

		// last changed log
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

function getObjectPortsAndLinks ($object_id, $sorted = TRUE)
{
	$ret = fetchPortList ("Port.object_id = ?", array ($object_id));
	if ($sorted)
		$ret = sortPortList ($ret, TRUE);
	return $ret;
}

// This function provides data for syncObjectPorts() and requires only two tables locked.
function getObjectPortsAndLinksTerse ($object_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, name, iif_id, type AS oif_id, label, l2address, reservation_comment, ' .
		'(SELECT COUNT(*) FROM Link WHERE porta = Port.id OR portb = Port.id) AS link_count ' .
		'FROM Port WHERE object_id = ?',
		array ($object_id)
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// Fetch the object type via SQL.
// spotEntity cannot be used because it references RackObject, which doesn't suit Racks, Rows, or Locations.
function getObjectType ($object_id)
{
	$result = usePreparedSelectBlade ('SELECT objtype_id from Object WHERE id = ?', array ($object_id));
	return $result->fetchColumn ();
}

// If the given name is used by any object other than the current object,
// raise an exception.  Validation is bypassed for certain object types
// where duplicates are acceptable.
// NOTE: This could be enforced more strictly at the database level using triggers.
function checkObjectNameUniqueness ($name, $type_id, $object_id = 0)
{
	// Some object types do not need unique names
	// 1560 - Rack
	// 1561 - Row
	$dupes_allowed = array (1560, 1561);
	if (in_array ($type_id, $dupes_allowed))
		return;

	$result = usePreparedSelectBlade
	(
		'SELECT COUNT(*) FROM Object WHERE name = ? AND id != ?',
		array ($name, $object_id)
	);
	$row = $result->fetch (PDO::FETCH_NUM);
	if ($row[0] != 0)
		throw new InvalidRequestArgException ('name', $name, 'An object with that name already exists');
}

function commitAddObject ($new_name, $new_label, $new_type_id, $new_asset_no, $taglist = array())
{
	checkObjectNameUniqueness ($new_name, $new_type_id);
	usePreparedInsertBlade
	(
		'Object',
		array
		(
			'name' => nullIfEmptyStr ($new_name),
			'label' => nullIfEmptyStr ($new_label),
			'objtype_id' => $new_type_id,
			'asset_no' => nullIfEmptyStr ($new_asset_no),
		)
	);
	$object_id = lastInsertID();
	switch ($new_type_id)
	{
		case 1560:
			$realm = 'rack';
			break;
		case 1561:
			$realm = 'row';
			break;
		case 1562:
			$realm = 'location';
			break;
		default:
			$realm = 'object';
	}
	lastCreated ($realm, $object_id);

	// Store any tags before executeAutoPorts() calls spotEntity() and populates the cache.
	produceTagsForNewRecord ($realm, $taglist, $object_id);
	// Do AutoPorts magic
	if ($realm == 'object')
		executeAutoPorts ($object_id);
	recordObjectHistory ($object_id);
	return $object_id;
}

function commitRenameObject ($object_id, $new_name)
{
	$type_id = getObjectType ($object_id);
	checkObjectNameUniqueness ($new_name, $type_id, $object_id);
	usePreparedUpdateBlade
	(
		'Object',
		array
		(
			'name' => nullIfEmptyStr ($new_name),
		),
		array
		(
			'id' => $object_id
		)
	);
	recordObjectHistory ($object_id);
}

function commitUpdateObject ($object_id, $new_name, $new_label, $new_has_problems, $new_asset_no, $new_comment)
{
	$set_columns = array
	(
		'name' => nullIfEmptyStr ($new_name),
		'label' => nullIfEmptyStr ($new_label),
		'has_problems' => $new_has_problems == '' ? 'no' : $new_has_problems,
		'asset_no' => nullIfEmptyStr ($new_asset_no),
		'comment' => nullIfEmptyStr ($new_comment),
	);
	$override = callHook('commitUpdateObjectBefore_hook', $object_id, $set_columns);
	if ( is_array ($override) )
	{
		$set_columns = $override;
	}
	$type_id = getObjectType ($object_id);
	checkObjectNameUniqueness ($new_name, $type_id, $object_id);
	usePreparedUpdateBlade
	(
		'Object',
		$set_columns,
		array
		(
			'id' => $object_id
		)
	);
	recordObjectHistory ($object_id);
	callHook ('commitUpdateObjectAfter_hook', $object_id);
}

function compare_name ($a, $b)
{
	return strnatcmp($a['name'], $b['name']);
}

// find either parents or children of a record
function getEntityRelatives ($type, $entity_type, $entity_id)
{
	$ret = array();

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
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = array(
			'entity_type' => $row['entity_type'],
			'entity_id' => $row['entity_id'],
		);

	return $ret;
}

function getParents ($entity, $result_realm = NULL)
{
	return getRelatives ($entity, 'parents', $result_realm);
}

function getChildren ($entity, $result_realm = NULL)
{
	return getRelatives ($entity, 'children', $result_realm);
}

function getRelatives ($entity, $type, $result_realm = NULL)
{
	$ret = array();
	foreach (getEntityRelatives ($type, $entity['realm'], $entity['id']) as $link_id => $struct)
		if (! isset ($result_realm) || $result_realm == $struct['entity_type'])
			$ret[$link_id] = spotEntity ($struct['entity_type'], $struct['entity_id']);
	return $ret;
}

// This function is recursive and returns only object IDs.
function getObjectContentsList ($object_id, $children = array ())
{
	$self = __FUNCTION__;
	$result = usePreparedSelectBlade
	(
		'SELECT child_entity_id FROM EntityLink ' .
		'WHERE parent_entity_type = "object" AND child_entity_type = "object" AND parent_entity_id = ?',
		array ($object_id)
	);
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($rows as $row)
	{
		if (in_array ($row['child_entity_id'], $children))
			throw new RackTablesError ("Circular reference for object ${object_id}", RackTablesError::INTERNAL);
		$children[] = $row['child_entity_id'];
		$children = array_unique (array_merge ($children, $self ($row['child_entity_id'], $children)));
	}
	return $children;
}

// This function is recursive and returns only location IDs.
function getLocationChildrenList ($location_id, $children = array ())
{
	$self = __FUNCTION__;
	$result = usePreparedSelectBlade ('SELECT id FROM Location WHERE parent_id = ?', array ($location_id));
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($rows as $row)
	{
		if (in_array ($row['id'], $children))
			throw new RackTablesError ("Circular reference for location ${location_id}", RackTablesError::INTERNAL);
		$children[] = $row['id'];
		$children = array_unique (array_merge ($children, $self ($row['id'], $children)));
	}
	return $children;
}

// DEPRECATED: use getTagDescendents() instead
// This function is recursive and returns only tag IDs.
function getTagChildrenList ($tag_id, $children = array ())
{
	$self = __FUNCTION__;
	$result = usePreparedSelectBlade ('SELECT id FROM TagTree WHERE parent_id = ?', array ($tag_id));
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($rows as $row)
	{
		if (in_array ($row['id'], $children))
			throw new RackTablesError ("Circular reference for tag ${tag_id}", RackTablesError::INTERNAL);
		$children[] = $row['id'];
		$children = array_unique (array_merge ($children, $self ($row['id'], $children)));
	}
	return $children;
}

function commitLinkEntities ($parent_entity_type, $parent_entity_id, $child_entity_type, $child_entity_id)
{
	// a location's parent may not be one of its children
	if
	(
		$parent_entity_type == 'location' &&
		$child_entity_type == 'location' &&
		in_array ($parent_entity_id, getLocationChildrenList ($child_entity_id))
	)
		throw new RackTablesError ("Circular reference for location ${parent_entity_id}", RackTablesError::INTERNAL);

	// an object's container may not be one of its contained objects
	if
	(
		$parent_entity_type == 'object' &&
		$child_entity_type == 'object' &&
		in_array ($parent_entity_id, getObjectContentsList ($child_entity_id))
	)
		throw new RackTablesError ("Circular reference for object ${parent_entity_id}", RackTablesError::INTERNAL);

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

function commitUpdateEntityLink
(
	$old_parent_entity_type, $old_parent_entity_id, $old_child_entity_type, $old_child_entity_id,
	$new_parent_entity_type, $new_parent_entity_id, $new_child_entity_type, $new_child_entity_id
)
{
	// a location's parent may not be one of its children
	if
	(
		$new_parent_entity_type == 'location' &&
		$new_child_entity_type == 'location' &&
		in_array ($new_parent_entity_id, getLocationChildrenList ($new_child_entity_id))
	)
		throw new RackTablesError ("Circular reference for location ${new_parent_entity_id}", RackTablesError::INTERNAL);

	// an object's container may not be one of its contained objects
	if
	(
		$new_parent_entity_type == 'object' &&
		$new_child_entity_type == 'object' &&
		in_array ($new_parent_entity_id, getObjectContentsList ($new_child_entity_id))
	)
		throw new RackTablesError ("Circular reference for object ${new_parent_entity_id}", RackTablesError::INTERNAL);

	usePreparedUpdateBlade
	(
		'EntityLink',
		array
		(
			'parent_entity_type' => $new_parent_entity_type,
			'parent_entity_id' => $new_parent_entity_id,
			'child_entity_type' => $new_child_entity_type,
			'child_entity_id' => $new_child_entity_id
		),
		array
		(
			'parent_entity_type' => $old_parent_entity_type,
			'parent_entity_id' => $old_parent_entity_id,
			'child_entity_type' => $old_child_entity_type,
			'child_entity_id' => $old_child_entity_id
		)
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

// return VM clusters and corresponding stats
//	- number of hypervisors
//	- number of resource pools
//	- number of VMs whose parent is the cluster itself
//	- number of VMs whose parent is one of the resource pools in the cluster
function getVMClusterSummary ()
{
	$query = <<<END
SELECT
	O.id,
	O.name,
	(SELECT COUNT(*) FROM EntityLink EL
		LEFT JOIN Object O_H ON EL.child_entity_id = O_H.id
		LEFT JOIN AttributeValue AV ON O_H.id = AV.object_id
		WHERE EL.parent_entity_type = 'object'
		AND EL.child_entity_type = 'object'
		AND EL.parent_entity_id = O.id
		AND O_H.objtype_id = 4
		AND AV.attr_id = 26
		AND AV.uint_value = 1501) AS hypervisors,
	(SELECT COUNT(*) FROM EntityLink EL
		LEFT JOIN Object O_RP ON EL.child_entity_id = O_RP.id
		WHERE EL.parent_entity_type = 'object'
		AND EL.child_entity_type = 'object'
		AND EL.parent_entity_id = O.id
		AND O_RP.objtype_id = 1506) AS resource_pools,
	(SELECT COUNT(*) FROM EntityLink EL
		LEFT JOIN Object O_C_VM ON EL.child_entity_id = O_C_VM.id
		WHERE EL.parent_entity_type = 'object'
		AND EL.child_entity_type = 'object'
		AND EL.parent_entity_id = O.id
		AND O_C_VM.objtype_id = 1504) AS cluster_vms,
	(SELECT COUNT(*) FROM EntityLink EL
		LEFT JOIN Object O_RP_VM ON EL.child_entity_id = O_RP_VM.id
		WHERE EL.parent_entity_type = 'object'
		AND EL.child_entity_type = 'object'
		AND EL.parent_entity_id IN
			(SELECT child_entity_id FROM EntityLink EL
				LEFT JOIN Object O_RP ON EL.child_entity_id = O_RP.id
				WHERE EL.parent_entity_type = 'object'
				AND EL.child_entity_type = 'object'
				AND EL.parent_entity_id = O.id
				AND O_RP.objtype_id = 1506)
		AND O_RP_VM.objtype_id = 1504) AS resource_pool_vms
FROM Object O
WHERE O.objtype_id = 1505
ORDER BY O.name
END;
	$result = usePreparedSelectBlade ($query);
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
function commitDeleteObject ($object_id)
{
	// Reset most of stuff
	commitResetObject ($object_id);
	// Object itself
	usePreparedDeleteBlade ('Object', array ('id' => $object_id));
	// Dangling links
	usePreparedExecuteBlade
	(
		'DELETE FROM EntityLink WHERE ' .
		"(parent_entity_type IN ('rack', 'row', 'location') AND parent_entity_id = ?) OR " .
		"(child_entity_type IN ('rack', 'row', 'location') AND child_entity_id = ?)",
		array ($object_id, $object_id)
	);
}

function commitResetObject ($object_id)
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
	// SLB
	usePreparedDeleteBlade ('IPv4LB', array ('object_id' => $object_id));
	usePreparedDeleteBlade ('VSEnabledIPs', array ('object_id' => $object_id));
	usePreparedDeleteBlade ('VSEnabledPorts', array ('object_id' => $object_id));
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
	# Munin graphs
	usePreparedDeleteBlade ('MuninGraph', array ('object_id' => $object_id));
	# Do an additional reset if needed
	callHook ('resetObject_hook', $object_id);
}

function commitUpdateRack ($rack_id, $new_row_id, $new_name, $new_height, $new_has_problems, $new_asset_no, $new_comment)
{
	// Can't shrink a rack if rows being deleted contain mounted objects
	$check_result = usePreparedSelectBlade ('SELECT COUNT(*) AS count FROM RackSpace WHERE rack_id = ? AND unit_no > ?', array ($rack_id, $new_height));
	$check_row = $check_result->fetch (PDO::FETCH_ASSOC);
	unset ($check_result);
	if ($check_row['count'] > 0)
		throw new InvalidArgException ('new_height', $new_height, 'Cannot shrink rack, objects are still mounted there');

	// Determine if the row changed
	$old_rack =  spotEntity ('rack', $rack_id);
	$old_row_id = $old_rack['row_id'];
	if ($old_row_id != $new_row_id)
	{
		// Move it to the specified row
		usePreparedUpdateBlade
		(
			'EntityLink',
			array ('parent_entity_id' => $new_row_id),
			array ('child_entity_type' => 'rack', 'child_entity_id' => $rack_id)
		);

		// Set the sort_order attribute so it's placed at the end of the new row
		$rowInfo = getRowInfo ($new_row_id);
		usePreparedUpdateBlade
		(
			'AttributeValue',
			array ('uint_value' => $rowInfo['count']),
			array ('object_id' => $rack_id, 'attr_id' => 29)
		);

		// Reset the sort order of the old row
		resetRackSortOrder ($old_row_id);
	}

	// Update the height
	commitUpdateAttrValue ($rack_id, 27, $new_height);

	// Update the rack
	commitUpdateObject ($rack_id, $new_name, NULL, $new_has_problems, $new_asset_no, $new_comment);
}

// Unmount all objects from the rack
function commitCleanRack ($rack_id)
{
	$rack = spotEntity ('rack', $rack_id);
	foreach (getChildren ($rack, 'object') as $child)
		commitUnlinkEntities ('rack', $rack_id, 'object', $child['id']);
	usePreparedDeleteBlade ('RackSpace', array ('rack_id' => $rack_id));
	usePreparedDeleteBlade ('RackThumbnail', array ('rack_id' => $rack_id));
}

// Drop the rack
function commitDeleteRack ($rack_id)
{
	$rack = spotEntity ('rack', $rack_id);
	releaseFiles ('rack', $rack_id);
	destroyTagsForEntity ('rack', $rack_id);
	usePreparedDeleteBlade ('RackSpace', array ('rack_id' => $rack_id));
	commitDeleteObject ($rack_id);
	resetRackSortOrder ($rack['row_id']);
}

// Drop the row with all racks inside
function commitDeleteRow ($row_id)
{
	$racks = getRacks ($row_id);
	foreach ($racks as $rack)
		commitDeleteRack ($rack['id']);
	commitDeleteObject ($row_id);
}

// Returns mounted devices count in all racks inside the specified row
function getRowMountsCount ($row_id)
{
	$query =<<<END
SELECT COUNT(*) FROM (
	SELECT object_id FROM RackSpace rs LEFT JOIN EntityLink el ON (rs.rack_id = el.child_entity_id)
	WHERE
		rs.object_id IS NOT NULL AND
		el.parent_entity_id = ? AND el.parent_entity_type = "row" AND el.child_entity_type = "rack"
	UNION
	SELECT el1.child_entity_id object_id FROM EntityLink el1 LEFT JOIN EntityLink el2 ON (el1.parent_entity_id = el2.child_entity_id)
	WHERE
		el1.parent_entity_type = "rack" AND el1.child_entity_type = "object" AND
		el2.parent_entity_id = ? AND el2.parent_entity_type = "row" AND el2.child_entity_type = "rack"
) x
END;
	$result = usePreparedSelectBlade ($query, array ($row_id, $row_id));
	return $result->fetchColumn();
}

// Returns mounted devices count in specified rack
function getRackMountsCount ($rack_id)
{
	$query =<<<END
SELECT COUNT(*) FROM (
	SELECT object_id FROM RackSpace WHERE object_id IS NOT NULL AND rack_id = ?
	UNION
	SELECT child_entity_id object_id FROM EntityLink WHERE
		parent_entity_id = ? AND parent_entity_type = "rack" AND child_entity_type = "object"
) x
END;
	$result = usePreparedSelectBlade ($query, array ($rack_id, $rack_id));
	return $result->fetchColumn();
}

// Used when sort order is manually changed, and when a rack is moved or deleted
// Input is expected to be a pre-sorted array of rack IDs
function updateRackSortOrder ($racks)
{
	for ($i = 0; $i<count($racks); $i++)
	{
		usePreparedUpdateBlade
		(
			'AttributeValue',
			array ('uint_value' => $i+1),
			array ('object_id' => $racks[$i], 'attr_id' => 29)
		);
	}
}

function resetRackSortOrder ($row_id)
{
	// Re-order the row's racks
	$racks = getRacks($row_id);
	$rack_ids = array ();
	foreach ($racks as $rack_id => $rackDetails)
		$rack_ids[] = $rack_id;
	updateRackSortOrder ($rack_ids);
}

// This function builds a list of rack-unit-atom records assigned to
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
function getMolecule ($mid)
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
		'INSERT INTO ObjectHistory ' .
		'SELECT id, name, label, objtype_id, asset_no, has_problems, comment, ' .
		'CURRENT_TIMESTAMP(), ? FROM Object WHERE id=?',
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
function getOperationMolecules ($op_id)
{
	$result = usePreparedSelectBlade ('SELECT old_molecule_id, new_molecule_id FROM MountOperation WHERE id = ?', array ($op_id));
	// We expect one row.
	$row = $result->fetch (PDO::FETCH_ASSOC);
	return array ($row['old_molecule_id'], $row['new_molecule_id']);
}

function getResidentRacksData ($object_id = 0, $fetch_rackdata = TRUE)
{
	$result = usePreparedSelectBlade
	(
		// Include racks that the object is directly mounted in
		"SELECT rack_id FROM RackSpace WHERE object_id = ? " .
		"UNION " .
		// Include racks that it's parent is mounted in
		"SELECT RS.rack_id FROM RackSpace RS INNER JOIN EntityLink EL ON RS.object_id = EL.parent_entity_id AND EL.parent_entity_type = 'object' WHERE EL.child_entity_id = ? AND EL.child_entity_type = 'object' " .
		"UNION " .
		// and racks that it is 'Zero-U' mounted in
		"SELECT parent_entity_id AS rack_id FROM EntityLink WHERE parent_entity_type = 'rack' AND child_entity_type = 'object' AND child_entity_id = ? " .
		'ORDER BY rack_id', array ($object_id, $object_id, $object_id)
	);
	$rows = $result->fetchAll (PDO::FETCH_NUM);
	unset ($result);

	$ret = array();
	foreach ($rows as $row)
		if (! isset ($ret[$row[0]]))
		{
			if (!$fetch_rackdata)
				$rackData = $row[0];
			else
			{
				$rackData = spotEntity ('rack', $row[0]);
				amplifyCell ($rackData);
			}
			$ret[$row[0]] = $rackData;
		}
	return $ret;
}

function commitAddPort ($object_id, $port_name, $port_type_id, $port_label, $port_l2address)
{
	global $dbxlink;
	$db_l2address = l2addressForDatabase ($port_l2address);
	list ($iif_id, $oif_id) = parsePortIIFOIF ($port_type_id);
	// The conditional table locking is less relevant now due to syncObjectPorts().
	if ($db_l2address != '')
		$dbxlink->exec ('LOCK TABLES Port WRITE');
	try
	{
		assertUniqueL2Addresses (array ($db_l2address), $object_id);
		$ret = commitAddPortReal ($object_id, $port_name, $iif_id, $oif_id, $port_label, $db_l2address);
	}
	catch (Exception $e)
	{
		if ($db_l2address != '')
			$dbxlink->exec ('UNLOCK TABLES');
		throw $e;
	}
	if ($db_l2address != '')
		$dbxlink->exec ('UNLOCK TABLES');
	return $ret;
}

// Having the call to assertUniqueL2Addresses() in this function would break things because
// if the constraint check fails for any port the whole "transaction" needs to be rolled
// back. Thus the calling function must call assertUniqueL2Addresses() for all involved ports
// first and only then start making any calls to this function.
function commitAddPortReal ($object_id, $port_name, $iif_id, $oif_id, $port_label, $db_l2address)
{
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
			'l2address' => nullIfEmptyStr ($db_l2address),
		)
	);
	lastCreated ('port', lastInsertID());
	return lastInsertID();
}

function getPortReservationComment ($port_id, $extrasql = '')
{
	$result = usePreparedSelectBlade ("SELECT reservation_comment FROM Port WHERE id = ? $extrasql", array ($port_id));
	return $result->fetchColumn();
}

function commitUpdatePort ($object_id, $port_id, $port_name, $port_type_id, $port_label, $port_l2address, $port_reservation_comment)
{
	global $dbxlink;
	$db_l2address = l2addressForDatabase ($port_l2address);
	list ($iif_id, $oif_id) = parsePortIIFOIF ($port_type_id);
	if ($db_l2address != '')
		$dbxlink->exec ('LOCK TABLES Port WRITE, PortLog WRITE');
	try
	{
		assertUniqueL2Addresses (array ($db_l2address), $object_id);
		commitUpdatePortReal ($object_id, $port_id, $port_name, $iif_id, $oif_id, $port_label, $db_l2address, $port_reservation_comment);
	}
	catch (Exception $e)
	{
		if ($db_l2address != '')
			$dbxlink->exec ('UNLOCK TABLES');
		throw $e;
	}
	if ($db_l2address != '')
		$dbxlink->exec ('UNLOCK TABLES');
}

// The comment about commitAddPortReal() also applies here.
function commitUpdatePortReal ($object_id, $port_id, $port_name, $iif_id, $oif_id, $port_label, $db_l2address, $port_reservation_comment)
{
	$old_reservation_comment = getPortReservationComment ($port_id);
	$port_reservation_comment = nullIfEmptyStr ($port_reservation_comment);
	usePreparedUpdateBlade
	(
		'Port',
		array
		(
			'name' => $port_name,
			'iif_id' => $iif_id,
			'type' => $oif_id,
			'label' => $port_label,
			'reservation_comment' => $port_reservation_comment,
			'l2address' => nullIfEmptyStr ($db_l2address),
		),
		array
		(
			'id' => $port_id,
			'object_id' => $object_id
		)
	);
	if ($old_reservation_comment !== $port_reservation_comment)
		addPortLogEntry ($port_id, sprintf ("Reservation changed from '%s' to '%s'", $old_reservation_comment, $port_reservation_comment));
}

function commitUpdatePortComment ($port_id, $port_reservation_comment)
{
	global $dbxlink;
	$dbxlink->beginTransaction();
	$prev_comment = getPortReservationComment ($port_id, 'FOR UPDATE');
	$reservation_comment = nullIfEmptyStr ($port_reservation_comment);
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
	if ($prev_comment !== $reservation_comment)
		addPortLogEntry ($port_id, sprintf ("Reservation changed from '%s' to '%s'", $prev_comment, $reservation_comment));
	$dbxlink->commit();
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
		"IPv4Allocation.type as type, ".
		"INET_NTOA(ip) as ip ".
		"from IPv4Allocation join Object on id=object_id "
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function linkPorts ($porta, $portb, $cable = NULL)
{
	if ($porta == $portb)
		throw new InvalidArgException ('porta/portb', $porta, "Ports can't be the same");

	$result = usePreparedSelectBlade
	(
		'SELECT COUNT(*) FROM Link WHERE porta IN (?,?) OR portb IN (?,?)',
		array ($porta, $portb, $porta, $portb)
	);
	if ($result->fetchColumn () != 0)
		throw new RTDatabaseError ("Port ${porta} or ${portb} is already linked");
	unset ($result);

	$ret = usePreparedInsertBlade
	(
		'Link',
		array
		(
			'porta' => $porta,
			'portb' => $portb,
			'cable' => nullIfEmptyStr ($cable),
		)
	);
	usePreparedUpdateBlade ('Port', array ('reservation_comment' => NULL), array ('id' => array ($porta, $portb)));

	// log new links
	$result = usePreparedSelectBlade
	(
		"SELECT Port.id, Port.name as port_name, Object.name as obj_name FROM Port " .
		"INNER JOIN Object ON Port.object_id = Object.id WHERE Port.id IN (?, ?)",
		array ($porta, $portb)
	);
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($rows as $row)
	{
		$pair_id = ($row['id'] == $porta ? $portb : $porta);
		addPortLogEntry ($pair_id, sprintf ("linked to %s %s", $row['obj_name'], $row['port_name']));
	}
	return $ret;
}

function commitUpdatePortLink ($port_id, $cable = NULL)
{
	return usePreparedUpdateBlade
	(
		'Link',
		array ('cable' => nullIfEmptyStr ($cable)),
		array ('porta' => $port_id, 'portb' => $port_id),
		'OR'
	);
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
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($rows as $row)
	{
		addPortLogEntry ($row['id_a'], sprintf ("unlinked from %s %s", $row['obj_name_b'], $row['port_name_b']));
		addPortLogEntry ($row['id_b'], sprintf ("unlinked from %s %s", $row['obj_name_a'], $row['port_name_a']));
	}

	// remove existing link
	return usePreparedDeleteBlade ('Link', array ('porta' => $port_id, 'portb' => $port_id), 'OR');
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

function addIPLogEntry ($ip_bin, $message)
{
	switch (strlen ($ip_bin))
	{
		case 4:  return addIPv4LogEntry ($ip_bin, $message);
		case 16: return addIPv6LogEntry ($ip_bin, $message);
		default: throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	}
}

function addIPv4LogEntry ($ip_bin, $message)
{
	global $disable_logging;
	if (isset ($disable_logging) && $disable_logging)
		return;
	global $remote_username;
	usePreparedExecuteBlade
	(
		"INSERT INTO IPv4Log (ip, date, user, message) VALUES (?, NOW(), ?, ?)",
		array (ip4_bin2db ($ip_bin), $remote_username, $message)
	);
}

function addIPv6LogEntry ($ip_bin, $message)
{
	global $disable_logging;
	if (isset ($disable_logging) && $disable_logging)
		return;
	global $remote_username;
	usePreparedExecuteBlade
	(
		"INSERT INTO IPv6Log (ip, date, user, message) VALUES (?, NOW(), ?, ?)",
		array ($ip_bin, $remote_username, $message)
	);
}

function fetchIPLogEntry ($ip_bin)
{
	switch (strlen ($ip_bin))
	{
		case 4:  return fetchIPv4LogEntry ($ip_bin);
		case 16: return fetchIPv6LogEntry ($ip_bin);
		default: throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	}
}

function fetchIPv4LogEntry ($ip_bin)
{
	$result = usePreparedSelectBlade
	(
		"SELECT date, user, message FROM IPv4Log WHERE ip = ? ORDER BY date ASC",
		array (ip4_bin2db ($ip_bin))
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function fetchIPv6LogEntry ($ip_bin)
{
	$result = usePreparedSelectBlade
	(
		"SELECT date, user, message FROM IPv6Log WHERE ip = ? ORDER BY date ASC",
		array ($ip_bin)
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// wrapper around getObjectIPv4AllocationList and getObjectIPv6AllocationList
function getObjectIPAllocationList ($object_id)
{
	return
		getObjectIPv4AllocationList ($object_id) +
		getObjectIPv6AllocationList ($object_id);
}

// Returns all IPv4 addresses allocated to object, but does not attach detailed info about address
// Used instead of getObjectIPv4Allocations if you need perfomance but 'addrinfo' value
function getObjectIPv4AllocationList ($object_id)
{
	$ret = array();
	$result = usePreparedSelectBlade
	(
		'SELECT name AS osif, type, ip FROM IPv4Allocation ' .
		'WHERE object_id = ?',
		array ($object_id)
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[ip4_int2bin ($row['ip'])] = array ('osif' => $row['osif'], 'type' => $row['type']);
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

// Return all IP addresses allocated to the object sorted by allocation name.
// Attach detailed info about address to each alocation records.
// Index result by binary ip
function getObjectIPAllocations ($object_id)
{
	return amplifyAllocationList (getObjectIPAllocationList ($object_id));
}
function getObjectIPv4Allocations ($object_id)
{
	return amplifyAllocationList (getObjectIPv4AllocationList ($object_id));
}
function getObjectIPv6Allocations ($object_id)
{
	return amplifyAllocationList (getObjectIPv6AllocationList ($object_id));
}

function amplifyAllocationList ($alloc_list)
{
	$ret = array();
	$sorted = array();
	foreach ($alloc_list as $ip_bin => $alloc)
		$sorted[$alloc['osif']][$ip_bin] = $alloc;
	foreach (sortPortList ($sorted) as $osif => $subarray)
		foreach ($subarray as $ip_bin => $alloc)
		{
			$alloc['addrinfo'] = getIPAddress ($ip_bin);
			$ret[$ip_bin] = $alloc;
		}
	return $ret;
}

function scanIPNet ($net_info, $filter_flags = IPSCAN_ANY)
{
	$pairlist = array(
		array('first' => $net_info['ip_bin'], 'last' => ip_last ($net_info))
	);
	return scanIPSpace ($pairlist, $filter_flags);
}

function scanIPSpace ($pairlist, $filter_flags = IPSCAN_ANY)
{
	$v4_pairs = array();
	$v6_pairs = array();
	foreach ($pairlist as $pair)
	{
		if (strlen ($pair['first']) == 4)
			$v4_pairs[] = $pair;
		elseif (strlen ($pair['first']) == 16)
			$v6_pairs[] = $pair;
	}
	return
		scanIPv4Space ($v4_pairs, $filter_flags) +
		scanIPv6Space ($v6_pairs, $filter_flags);
}

// Check the range requested for meaningful IPv4 records, build them
// into a list and return. Return an empty list if nothing matched.
// Both arguments are expected in 4-byte binary string form. The resulting list
// is keyed by 4-byte binary IPs, items aren't sorted.
// LATER: accept a list of pairs and build WHERE sub-expression accordingly
function scanIPv4Space ($pairlist, $filter_flags = IPSCAN_ANY)
{
	$ret = array();
	if (!count ($pairlist)) // this is normal for a network completely divided into smaller parts
		return $ret;
	$pairlist = reduceIPPairList ($pairlist);
	// FIXME: this is a copy-and-paste prototype
	$or = '';
	$whereexpr1 = '(';
	$whereexpr2 = '(';
	$whereexpr3a = '(';
	$whereexpr3b = '(';
	$whereexpr4 = '(';
	$whereexpr5a = '(';
	$whereexpr5b = '(';
	$whereexpr6 = '(';
	$qparams = array();
	$qparams_bin = array();
	foreach ($pairlist as $tmp)
	{
		$whereexpr1 .= $or . "ip between ? and ?";
		$whereexpr2 .= $or . "ip between ? and ?";
		$whereexpr3a .= $or . "vip between ? and ?";
		$whereexpr3b .= $or . "vip between ? and ?";
		$whereexpr4 .= $or . "rsip between ? and ?";
		$whereexpr5a .= $or . "remoteip between ? and ?";
		$whereexpr5b .= $or . "localip between ? and ?";
		$whereexpr6 .= $or . "l.ip between ? and ?";
		$or = ' or ';
		$qparams[] = ip4_bin2db ($tmp['first']);
		$qparams[] = ip4_bin2db ($tmp['last']);
		$qparams_bin[] = $tmp['first'];
		$qparams_bin[] = $tmp['last'];
	}
	$whereexpr1 .= ')';
	$whereexpr2 .= ')';
	$whereexpr3a .= ')';
	$whereexpr3b .= ')';
	$whereexpr4 .= ')';
	$whereexpr5a .= ')';
	$whereexpr5b .= ')';
	$whereexpr6 .= ')';

	// 1. collect labels and reservations
	if ($filter_flags & IPSCAN_DO_ADDR)
	{
	$query = "select ip, name, comment, reserved from IPv4Address ".
		"where ${whereexpr1} and (reserved = 'yes' or name != '' or comment != '')";
	$result = usePreparedSelectBlade ($query, $qparams);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = ip4_int2bin ($row['ip']);
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPAddress ($ip_bin);
		$ret[$ip_bin]['name'] = $row['name'];
		$ret[$ip_bin]['comment'] = $row['comment'];
		$ret[$ip_bin]['reserved'] = $row['reserved'];
	}
	unset ($result);
	}

	// 2. check for allocations
	if ($filter_flags & IPSCAN_DO_ALLOCS)
	{
	if ($filter_flags & IPSCAN_RTR_ONLY)
		$whereexpr2 .= " AND ia.type = 'router'";
	$query =
		"select ia.ip, ia.object_id, ia.name, ia.type, Object.name as object_name " .
		"from IPv4Allocation AS ia INNER JOIN Object ON ia.object_id = Object.id where ${whereexpr2} order by ia.type";
	$result = usePreparedSelectBlade ($query, $qparams);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = ip4_int2bin ($row['ip']);
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPAddress ($ip_bin);
		$ret[$ip_bin]['allocs'][] = array
		(
			'type' => $row['type'],
			'name' => $row['name'],
			'object_id' => $row['object_id'],
			'object_name' => $row['object_name'],
		);
	}
	unset ($result);
	}

	// 3a. look for virtual services
	if ($filter_flags & IPSCAN_DO_VS)
	{
	$query = "select id, vip from IPv4VS where ${whereexpr3a}";
	$result = usePreparedSelectBlade ($query, $qparams_bin);
	$allRows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($allRows as $row)
	{
		$ip_bin = $row['vip'];
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPAddress ($ip_bin);
		$ret[$ip_bin]['vslist'][] = $row['id'];
	}

	// 3b. look for virtual service groups
	$query = "select vs_id, vip from VSIPs where ${whereexpr3b}";
	$result = usePreparedSelectBlade ($query, $qparams_bin);
	$allRows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($allRows as $row)
	{
		$ip_bin = $row['vip'];
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPAddress ($ip_bin);
		$ret[$ip_bin]['vsglist'][] = $row['vs_id'];
	}
	}

	// 4. don't forget about real servers along with pools
	if ($filter_flags & IPSCAN_DO_RS)
	{
	$query = "select rsip, rspool_id from IPv4RS where ${whereexpr4}";
	$result = usePreparedSelectBlade ($query, $qparams_bin);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = $row['rsip'];
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPAddress ($ip_bin);
		$ret[$ip_bin]['rsplist'][] = $row['rspool_id'];
	}
	unset ($result);
	}

	// 5. add NAT rules, remote ip
	if ($filter_flags & IPSCAN_DO_NAT)
	{
	$query =
		"select " .
		"proto, " .
		"localip, " .
		"localport, " .
		"remoteip, " .
		"remoteport, " .
		"description " .
		"from IPv4NAT " .
		"where ${whereexpr5a} " .
		"order by localip, localport, remoteip, remoteport, proto";
	$result = usePreparedSelectBlade ($query, $qparams);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin_local = ip4_int2bin ($row['localip']);
		$ip_bin_remote = ip4_int2bin ($row['remoteip']);
		$row['localip_bin'] = $ip_bin_local;
		$row['remoteip_bin'] = $ip_bin_remote;
		$row['localip'] = ip_format ($ip_bin_local);
		$row['remoteip'] = ip_format ($ip_bin_remote);
		if (!isset ($ret[$ip_bin_remote]))
			$ret[$ip_bin_remote] = constructIPAddress ($ip_bin_remote);
		$ret[$ip_bin_remote]['inpf'][] = $row;
	}
	unset ($result);
	// 5. add NAT rules, local ip
	$query =
		"select " .
		"proto, " .
		"localip, " .
		"localport, " .
		"remoteip, " .
		"remoteport, " .
		"description " .
		"from IPv4NAT " .
		"where ${whereexpr5b} " .
		"order by localip, localport, remoteip, remoteport, proto";
	$result = usePreparedSelectBlade ($query, $qparams);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin_local = ip4_int2bin ($row['localip']);
		$ip_bin_remote = ip4_int2bin ($row['remoteip']);
		$row['localip_bin'] = $ip_bin_local;
		$row['remoteip_bin'] = $ip_bin_remote;
		$row['localip'] = ip_format ($ip_bin_local);
		$row['remoteip'] = ip_format ($ip_bin_remote);
		if (!isset ($ret[$ip_bin_local]))
			$ret[$ip_bin_local] = constructIPAddress ($ip_bin_local);
		$ret[$ip_bin_local]['outpf'][] = $row;
	}
	unset ($result);
	}

	// 6. collect last log message
	if ($filter_flags & IPSCAN_DO_LOG)
	{
	$query = "select l.ip, l.user, UNIX_TIMESTAMP(l.date) AS time from IPv4Log l INNER JOIN " .
		" (SELECT MAX(id) as id FROM IPv4Log GROUP BY ip) v USING (id) WHERE ${whereexpr6}";
	$result = usePreparedSelectBlade ($query, $qparams);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = ip4_int2bin ($row['ip']);
		if (isset ($ret[$ip_bin]))
			$ret[$ip_bin]['last_log'] = array
			(
				'user' => $row['user'],
				'time' => $row['time'],
			);
	}
	unset ($result);
	}

	$override = callHook ('scanIPSpace_hook', $ret, $pairlist, $filter_flags);
	if (isset ($override))
		$ret = $override;

	return $ret;
}

// Check the range requested for meaningful IPv6 records, build them
// into a list and return. Return an empty list if nothing matched.
// Both arguments are expected as 16-byte binary IPs. The resulting list
// is keyed by 16-byte bynary IPs, items aren't sorted.
function scanIPv6Space ($pairlist, $filter_flags = IPSCAN_ANY)
{
	$ret = array();
	if (!count ($pairlist)) // this is normal for a network completely divided into smaller parts
		return $ret;
	$pairlist = reduceIPPairList ($pairlist);

	$or = '';
	$whereexpr1 = '(';
	$whereexpr2 = '(';
	$whereexpr3a = '(';
	$whereexpr3b = '(';
	$whereexpr4 = '(';
	$whereexpr6 = '(';
	$qparams = array();
	foreach ($pairlist as $tmp)
	{
		$whereexpr1 .= $or . "ip between ? and ?";
		$whereexpr2 .= $or . "ip between ? and ?";
		$whereexpr3a .= $or . "vip between ? and ?";
		$whereexpr3b .= $or . "vip between ? and ?";
		$whereexpr4 .= $or . "rsip between ? and ?";
		$whereexpr6 .= $or . "l.ip between ? and ?";
		$or = ' or ';
		$qparams[] = $tmp['first'];
		$qparams[] = $tmp['last'];
	}
	$whereexpr1 .= ')';
	$whereexpr2 .= ')';
	$whereexpr3a .= ')';
	$whereexpr3b .= ')';
	$whereexpr4 .= ')';
	$whereexpr6 .= ')';

	// 1. collect labels and reservations
	if ($filter_flags & IPSCAN_DO_ADDR)
	{
	$query = "select ip, name, comment, reserved from IPv6Address ".
		"where ${whereexpr1} and (reserved = 'yes' or name != '' or comment != '')";
	$result = usePreparedSelectBlade ($query, $qparams);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = $row['ip'];
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPAddress ($ip_bin);
		$ret[$ip_bin]['name'] = $row['name'];
		$ret[$ip_bin]['comment'] = $row['comment'];
		$ret[$ip_bin]['reserved'] = $row['reserved'];
	}
	unset ($result);
	}

	// 2. check for allocations
	if ($filter_flags & IPSCAN_DO_ALLOCS)
	{
	if ($filter_flags & IPSCAN_RTR_ONLY)
		$whereexpr2 .= " AND ia.type = 'router'";
	$query =
		"select ia.ip, ia.object_id, ia.name, ia.type, Object.name as object_name " .
		"from IPv6Allocation AS ia INNER JOIN Object ON ia.object_id = Object.id where ${whereexpr2} order by ia.type";
	$result = usePreparedSelectBlade ($query, $qparams);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = $row['ip'];
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPAddress ($ip_bin);
		$ret[$ip_bin]['allocs'][] = array
		(
			'type' => $row['type'],
			'name' => $row['name'],
			'object_id' => $row['object_id'],
			'object_name' => $row['object_name'],
		);
	}
	unset ($result);
	}

	// 3a. look for virtual services
	if ($filter_flags & IPSCAN_DO_VS)
	{
	$query = "select id, vip from IPv4VS where ${whereexpr3a}";
	$result = usePreparedSelectBlade ($query, $qparams);
	$allRows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($allRows as $row)
	{
		$ip_bin = $row['vip'];
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPAddress ($ip_bin);
		$ret[$ip_bin]['vslist'][] = $row['id'];
	}

	// 3b. look for virtual service groups
	$query = "select vs_id, vip from VSIPs where ${whereexpr3b}";
	$result = usePreparedSelectBlade ($query, $qparams);
	$allRows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($allRows as $row)
	{
		$ip_bin = $row['vip'];
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPAddress ($ip_bin);
		$ret[$ip_bin]['vsglist'][] = $row['vs_id'];
	}
	}

	// 4. don't forget about real servers along with pools
	if ($filter_flags & IPSCAN_DO_RS)
	{
	$query = "select rsip, rspool_id from IPv4RS where ${whereexpr4}";
	$result = usePreparedSelectBlade ($query, $qparams);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = $row['rsip'];
		if (!isset ($ret[$ip_bin]))
			$ret[$ip_bin] = constructIPAddress ($ip_bin);
		$ret[$ip_bin]['rsplist'][] = $row['rspool_id'];
	}
	unset ($result);
	}

	// 6. collect last log message
	if ($filter_flags & IPSCAN_DO_LOG)
	{
	$query = "select l.ip, l.user, UNIX_TIMESTAMP(l.date) AS time from IPv6Log l INNER JOIN " .
		" (SELECT MAX(id) as id FROM IPv6Log GROUP BY ip) v USING (id) WHERE ${whereexpr6}";
	$result = usePreparedSelectBlade ($query, $qparams);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = $row['ip'];
		if (isset ($ret[$ip_bin]))
			$ret[$ip_bin]['last_log'] = array
			(
				'user' => $row['user'],
				'time' => $row['time'],
			);
	}
	unset ($result);
	}

	$override = callHook ('scanIPSpace_hook', $ret, $pairlist, $filter_flags);
	if (isset ($override))
		$ret = $override;

	return $ret;
}

function bindIPToObject ($ip_bin, $object_id = 0, $name = '', $type = '')
{
	switch (strlen ($ip_bin))
	{
		case 4:
			$db_ip = ip4_bin2db ($ip_bin);
			$table = 'IPv4Allocation';
			$table2 = 'IPv4Address';
			break;
		case 16:
			$db_ip = $ip_bin;
			$table = 'IPv6Allocation';
			$table2 = 'IPv6Address';
			break;
		default: throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	}

	// release IP reservation and/or comment if configured
	$release = getConfigVar ('IPV4_AUTO_RELEASE');
	if ($release >= 2)
		usePreparedExecuteBlade ("DELETE FROM $table2 WHERE ip = ?", array ($db_ip));
	elseif ($release >= 1)
		usePreparedExecuteBlade ("UPDATE $table2 SET reserved = 'no' WHERE ip = ?", array ($db_ip));

	usePreparedInsertBlade
	(
		$table,
		array ('ip' => $db_ip, 'object_id' => $object_id, 'name' => $name, 'type' => $type)
	);
	// store history line
	$cell = spotEntity ('object', $object_id);
	setDisplayedName ($cell);
	addIPLogEntry ($ip_bin, "Binded with ${cell['dname']}, ifname=$name");
}

function bindIPv4ToObject ($ip_bin, $object_id = 0, $name = '', $type = '')
{
	if (strlen ($ip_bin) != 4)
		throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	return bindIPToObject ($ip_bin, $object_id, $name, $type);
}

function bindIPv6ToObject ($ip_bin, $object_id = 0, $name = '', $type = '')
{
	if (strlen ($ip_bin) != 16)
		throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	return bindIPToObject ($ip_bin, $object_id, $name, $type);
}

// Universal v4/v6 wrapper around getIPv4AddressNetworkId and getIPv6AddressNetworkId.
// Return the id of the smallest IP network containing the given IP address
// or NULL, if nothing was found. When finding the covering network for
// another network, it is important to filter out matched records with longer
// masks (they aren't going to be the right pick).
function getIPAddressNetworkId ($ip_bin, $masklen = NULL)
{
	switch (strlen ($ip_bin))
	{
		case 4:  return getIPv4AddressNetworkId ($ip_bin, isset ($masklen) ? $masklen : 32);
		case 16: return getIPv6AddressNetworkId ($ip_bin, isset ($masklen) ? $masklen : 128);
		default: throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	}
}

// Returns ipv4net or ipv6net entity, or NULL if no spanning network found.
// Throws an exception if $ip_bin is not valid binary address;
function spotNetworkByIP ($ip_bin, $masklen = NULL)
{
	$net_id = getIPAddressNetworkId ($ip_bin, $masklen);
	if (! $net_id)
		return NULL;
	switch (strlen ($ip_bin))
	{
		case 4:  return spotEntity ('ipv4net', $net_id);
		case 16: return spotEntity ('ipv6net', $net_id);
		default: throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	}
}

// Return the id of the smallest IPv4 network containing the given IPv4 address
// or NULL, if nothing was found. When finding the covering network for
// another network, it is important to filter out matched records with longer
// masks (they aren't going to be the right pick).
function getIPv4AddressNetworkId ($ip_bin, $masklen = 32)
{
	$row = callHook ('fetchIPv4AddressNetworkRow', $ip_bin, $masklen);
	return $row === NULL ? NULL : $row['id'];
}

function fetchIPAddressNetworkRow ($ip_bin, $masklen = NULL)
{
	switch (strlen ($ip_bin))
	{
	case 4:
		return callHook ('fetchIPv4AddressNetworkRow', $ip_bin, isset ($masklen) ? $masklen : 32);
	case 16:
		return callHook ('fetchIPv6AddressNetworkRow', $ip_bin, isset ($masklen) ? $masklen : 128);
	default:
		throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary address");
	}
}

function fetchIPv4AddressNetworkRow ($ip_bin, $masklen = 32)
{
	$ip_db = ip4_bin2db ($ip_bin);
	$result = usePreparedSelectBlade
	(
		'SELECT id, ip, mask, name, comment FROM IPv4Network ' .
		'WHERE ? & (4294967295 >> (32 - mask)) << (32 - mask) = ip AND ip <= ? AND mask < ? ' .
		'ORDER BY mask DESC LIMIT 1',
		array ($ip_db, $ip_db, $masklen)
	);
	return nullIfFalse ($result->fetch (PDO::FETCH_ASSOC));
}

// Return the id of the smallest IPv6 network containing the given IPv6 address
// ($ip is an instance of IPv4Address class) or NULL, if nothing was found.
function getIPv6AddressNetworkId ($ip_bin, $masklen = 128)
{
	$row = callHook ('fetchIPv6AddressNetworkRow', $ip_bin, $masklen);
	return $row === NULL ? NULL : $row['id'];
}

function fetchIPv6AddressNetworkRow ($ip_bin, $masklen = 128)
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, ip, mask, last_ip, name, comment FROM IPv6Network ' .
		'WHERE ip <= ? AND last_ip >= ? AND mask < ? ' .
		'ORDER BY mask DESC LIMIT 1',
		array ($ip_bin, $ip_bin, $masklen)
	);
	return nullIfFalse ($result->fetch (PDO::FETCH_ASSOC));
}

// This function is actually used not only to update, but also to create records,
// that's why ON DUPLICATE KEY UPDATE was replaced by DELETE-INSERT pair
// (MySQL 4.0 workaround).
function updateAddress ($ip_bin, $name = '', $reserved = 'no', $comment)
{
	switch (strlen ($ip_bin))
	{
		case 4:
			$table = 'IPv4Address';
			$db_ip = ip4_bin2db ($ip_bin);
			break;
		case 16:
			$table = 'IPv6Address';
			$db_ip = $ip_bin;
			break;
		default: throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	}

	// compute update log message
	$result = usePreparedSelectBlade ("SELECT name, comment, reserved FROM $table WHERE ip = ?", array ($db_ip));
	$old_name = '';
	$old_comment = '';
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$old_name = $row['name'];
		$old_comment = $row['comment'];
	}

	// If the 'comment' argument was specified when this function was called, use it.
	// If not, retain the old value.
	$comment = (func_num_args () == 4 ) ? $comment : $old_comment;
	$new_row = array ('name' => $name, 'comment' => $comment, 'reserved' => $reserved);
	$new_row_empty = $name == '' && $comment == '' && $reserved == 'no';

	unset ($result);
	$messages = array ();
	if ($name != $old_name)
	{
		if ($name == '')
			$messages[] = "name '$old_name' removed";
		elseif ($old_name == '')
			$messages[] = "name set to '$name'";
		else
			$messages[] = "name changed from '$old_name' to '$name'";
	}
	if ($comment != $old_comment)
	{
		if ($comment == '')
			$messages[] = "comment '$old_comment' removed";
		elseif ($old_name == '')
			$messages[] = "comment set to '$comment'";
		else
			$messages[] = "comment changed from '$old_comment' to '$comment'";
	}

	if ($row && ! $new_row_empty && $row == $new_row)
		return;
	if ($row)
		usePreparedDeleteBlade ($table, array ('ip' => $db_ip));
	// INSERT may appear not necessary.
	if (! $new_row_empty)
		usePreparedInsertBlade
		(
			$table,
			array ('name' => $name, 'comment' => $comment, 'reserved' => $reserved, 'ip' => $db_ip)
		);
	// store history line
	if ($messages)
		addIPLogEntry ($ip_bin, ucfirst (implode (', ', $messages)));
}

function updateV4Address ($ip_bin, $name = '', $reserved = 'no', $comment = '')
{
	if (strlen ($ip_bin) != 4)
		throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	return updateAddress ($ip_bin, $name, $reserved, $comment);
}

function updateV6Address ($ip_bin, $name = '', $reserved = 'no', $comment = '')
{
	if (strlen ($ip_bin) != 16)
		throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	return updateAddress ($ip_bin, $name, $reserved, $comment);
}

function updateIPBond ($ip_bin, $object_id=0, $name='', $type='')
{
	switch (strlen ($ip_bin))
	{
		case 4:  return updateIPv4Bond ($ip_bin, $object_id, $name, $type);
		case 16: return updateIPv6Bond ($ip_bin, $object_id, $name, $type);
		default: throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	}
}

function updateIPv4Bond ($ip_bin, $object_id=0, $name='', $type='')
{
	usePreparedUpdateBlade
	(
		'IPv4Allocation',
		array
		(
			'name' => $name,
			'type' => $type,
		),
		array
		(
			'ip' => ip4_bin2db ($ip_bin),
			'object_id' => $object_id,
		)
	);
}

function updateIPv6Bond ($ip_bin, $object_id=0, $name='', $type='')
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
			'ip' => $ip_bin,
			'object_id' => $object_id,
		)
	);
}


function unbindIPFromObject ($ip_bin, $object_id)
{
	switch (strlen ($ip_bin))
	{
		case 4:
			$table = 'IPv4Allocation';
			$db_ip = ip4_bin2db ($ip_bin);
			break;
		case 16:
			$table = 'IPv6Allocation';
			$db_ip = $ip_bin;
			break;
		default: throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	}

	$n_deleted = usePreparedDeleteBlade
	(
		$table,
		array ('ip' => $db_ip, 'object_id' => $object_id)
	);
	if ($n_deleted)
	{
		// store history line
		$cell = spotEntity ('object', $object_id);
		setDisplayedName ($cell);
		addIPLogEntry ($ip_bin, "Removed from ${cell['dname']}");
	}
}

function unbindIPv4FromObject ($ip_bin, $object_id)
{
	if (strlen ($ip_bin) != 4)
		throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	return unbindIPFromObject ($ip_bin, $object_id);
}

function unbindIPv6FromObject ($ip_bin, $object_id)
{
	if (strlen ($ip_bin) != 16)
		throw new InvalidArgException ('ip_bin', $ip_bin, "Invalid binary IP");
	return unbindIPFromObject ($ip_bin, $object_id);
}

function getIPv4PrefixSearchResult ($terms)
{
	$ret = array();
	foreach (array ('name', 'comment') as $column)
	{
		$tmp = getSearchResultByField
		(
			'IPv4Network',
			array ('id'),
			$column,
			$terms,
			'ip'
		);
		foreach ($tmp as $row)
			$ret[$row['id']] = spotEntity ('ipv4net', $row['id']);
	}
	return $ret;
}

function getIPv6PrefixSearchResult ($terms)
{
	$ret = array();
	foreach (array ('name', 'comment') as $column)
	{
		$tmp = getSearchResultByField
		(
			'IPv6Network',
			array ('id'),
			$column,
			$terms,
			'ip'
		);
		foreach ($tmp as $row)
			$ret[$row['id']] = spotEntity ('ipv6net', $row['id']);
	}
	return $ret;
}

function getIPv4AddressSearchResult ($terms)
{
	$query = "select ip, name, comment from IPv4Address where ";
	$or = '';
	$qparams = array();
	foreach (explode (' ', $terms) as $term)
	{
		$query .= $or . "name like ? or comment like ?";
		$or = ' or ';
		$qparams[] = "%${term}%";
		$qparams[] = "%${term}%";
	}
	$result = usePreparedSelectBlade ($query, $qparams);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$ip_bin = ip4_int2bin ($row['ip']);
		$row['ip'] = $ip_bin;
		$ret[$ip_bin] = $row;
	}
	return $ret;
}

function getIPv6AddressSearchResult ($terms)
{
	$query = "select ip, name, comment from IPv6Address where ";
	$or = '';
	$qparams = array();
	foreach (explode (' ', $terms) as $term)
	{
		$query .= $or . "name like ? or comment like ?";
		$or = ' or ';
		$qparams[] = "%${term}%";
		$qparams[] = "%${term}%";
	}
	$result = usePreparedSelectBlade ($query, $qparams);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC), 'ip');
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

function getVServiceSearchResult ($terms)
{
	$byname = getSearchResultByField
	(
		'VS',
		array ('id'),
		'name',
		$terms,
		'name'
	);
	$ret = array();
	foreach ($byname as $row)
		$ret[$row['id']] = spotEntity ('ipvs', $row['id']);
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
			$user['id'] = $user['user_id'];
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
	$bySticker = getStickerSearchResults ('Rack', $terms);
	// Filter out dupes.
	foreach ($byName as $res1)
	{
		foreach (array_keys ($byComment) as $key2)
			if ($res1['id'] == $byComment[$key2]['id'])
				unset ($byComment[$key2]);
		foreach (array_keys ($byAssetNo) as $key3)
			if ($res1['id'] == $byAssetNo[$key3]['id'])
				unset ($byAssetNo[$key3]);
		foreach (array_keys ($bySticker) as $key4)
			if ($res1['id'] == $bySticker[$key4]['id'])
				unset ($bySticker[$key4]);
	}
	$ret = array();
	foreach (array_merge ($byName, $byComment, $byAssetNo, $bySticker) as $row)
		$ret[$row['id']] = spotEntity ('rack', $row['id']);
	return $ret;
}

function getRowSearchResult ($terms)
{
	$byName = getSearchResultByField
	(
		'Row',
		array ('id'),
		'name',
		$terms,
		'name'
	);

	$ret = array();
	foreach ($byName as $row)
		$ret[$row['id']] = spotEntity ('row', $row['id']);
	return $ret;
}

function getLocationSearchResult ($terms)
{
	$byName = getSearchResultByField
	(
		'Location',
		array ('id'),
		'name',
		$terms,
		'name'
	);
	$byComment = getSearchResultByField
	(
		'Location',
		array ('id'),
		'comment',
		$terms,
		'name'
	);
	$bySticker = getStickerSearchResults ('Location', $terms);
	// Filter out dupes.
	foreach ($byName as $res1)
	{
		foreach (array_keys ($byComment) as $key2)
			if ($res1['id'] == $byComment[$key2]['id'])
				unset ($byComment[$key2]);
		foreach (array_keys ($bySticker) as $key3)
			if ($res1['id'] == $bySticker[$key3]['id'])
				unset ($bySticker[$key3]);
	}
	$ret = array();
	foreach (array_merge ($byName, $byComment, $bySticker) as $location)
		$ret[$location['id']] = spotEntity ('location', $location['id']);
	return $ret;
}

function getVLANSearchResult ($terms)
{
	$ret = array();
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
		$row['id'] = $vlan_ck;
		$ret[$vlan_ck] = $row;
	}
	return $ret;
}

function getSearchResultByField ($tablename, $retcolumns, $scancolumn, $terms, $ordercolumn = '', $exactness = 0)
{
	$query = 'SELECT ' . implode (', ', $retcolumns) . " FROM ${tablename} WHERE ";
	$qparams = array();
	$pfx = '';
	$pterms = $exactness == 3 ? explode (' ', $terms) : parseSearchTerms ($terms);
	foreach ($pterms as $term)
	{
		switch ($exactness)
		{
		case 3:
			$query .= $pfx . "${scancolumn} REGEXP ?";
			$qparams[] = $term;
			break;
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
	foreach (getStickerSearchResults ('RackObject', $what) as $objRecord)
	{
		$ret[$objRecord['id']]['id'] = $objRecord['id'];
		$ret[$objRecord['id']]['by_sticker'] = $objRecord['by_sticker'];
	}
	return $ret;
}

function getObjectAttrsSearchResults ($what)
{
	$ret = array();
	foreach (array ('name', 'label', 'asset_no', 'comment') as $column)
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

// Search stickers and return a list of pairs "object_id-attribute_id"
// that matched. A partilar object_id could be returned more than once, if it has
// multiple matching stickers. Search is only performed on "string" or "dict" attributes.
function getStickerSearchResults ($tablename, $what)
{
	$attr_types = array();
	$result = usePreparedSelectBlade
	(
		'SELECT AV.object_id, AV.attr_id FROM AttributeValue AV ' .
		"INNER JOIN ${tablename} O ON AV.object_id = O.id " .
		'INNER JOIN Attribute A ON AV.attr_id = A.id ' .
		'LEFT JOIN AttributeMap AM ON A.type = "dict" AND AV.object_tid = AM.objtype_id AND AV.attr_id = AM.attr_id ' .
		'LEFT JOIN Dictionary D ON AM.chapter_id = D.chapter_id AND AV.uint_value = D.dict_key ' .
		'WHERE string_value LIKE ? ' .
		'OR (A.type = "dict" AND dict_value LIKE ?) ORDER BY object_id',
		array ("%${what}%", "%${what}%")
	);
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		if (! array_key_exists ($row['attr_id'], $attr_types))
			$attr_types[$row['attr_id']] = getAttrType ($row['attr_id']);
		if (in_array ($attr_types[$row['attr_id']], array ('string', 'dict')))
		{
			$ret[$row['object_id']]['id'] = $row['object_id'];
			$ret[$row['object_id']]['by_sticker'][] = $row['attr_id'];
		}
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
	$result = usePreparedSelectBlade ('SELECT id FROM Port WHERE object_id = ? AND name = ?', array ($object_id, $port_name));
	return reduceSubarraysToColumn ($result->fetchAll (PDO::FETCH_ASSOC), 'id');
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
	return count ($rows) == 1 ? $rows[0][0] : NULL;
}

// returns an array of object ids
function searchByAttrValue ($attr_id, $value)
{
	$type = getAttrType ($attr_id);
	if (! isset ($type))
		throw new InvalidArgException ('attr_id', $attr_id, "No such attribute");

	switch ($type)
	{
		case 'string':
			$field = 'string_value';
			break;
		case 'float':
			$field = 'float_value';
			break;
		case 'uint':
		case 'dict':
		case 'date':
			$field = 'uint_value';
			break;
		default:
			throw new InvalidArgException ('type', $type);
	}

	$result = usePreparedSelectBlade ("
SELECT object_id FROM AttributeValue
WHERE
	attr_id = ?
	AND $field = ?
", array ($attr_id, $value)
);
	return $result->fetchAll (PDO::FETCH_COLUMN, 0);
}


// returns user_id
// throws an exception if error occured
function commitCreateUserAccount ($username, $realname, $password)
{
	usePreparedInsertBlade
	(
		'UserAccount',
		array
		(
			'user_name' => $username,
			'user_realname' => $realname == '' ? NULL : $realname,
			'user_password_hash' => $password,
		)
	);
	$user_id = lastInsertID();
	lastCreated ('user', $user_id);
	return $user_id;
}

function commitUpdateUserAccount ($id, $new_username, $new_realname, $new_password)
{
	usePreparedUpdateBlade
	(
		'UserAccount',
		array
		(
			'user_name' => $new_username,
			'user_realname' => $new_realname == '' ? NULL : $new_realname,
			'user_password_hash' => $new_password,
		),
		array ('user_id' => $id)
	);
}

// This function returns an array of all port type pairs from PortCompat table.
function getPortOIFCompat ($ignore_cache = FALSE)
{
	static $cache = NULL;
	if (! $ignore_cache && isset ($cache))
		return $cache;

	$query =
		"SELECT type1, type2, POI1.oif_name AS type1name, POI2.oif_name AS type2name FROM " .
		"PortCompat AS pc INNER JOIN PortOuterInterface AS POI1 ON pc.type1 = POI1.id " .
		"INNER JOIN PortOuterInterface AS POI2 ON pc.type2 = POI2.id " .
		'ORDER BY type1name, type2name';
	$result = usePreparedSelectBlade ($query);
	$cache = $result->fetchAll (PDO::FETCH_ASSOC);
	return $cache;
}

function addPortOIFCompat ($type1, $type2)
{
	return usePreparedExecuteBlade ("INSERT IGNORE INTO PortCompat (type1, type2) VALUES (?, ?),(?, ?)", array ($type1, $type2, $type2, $type1));
}

function deletePortOIFCompat ($type1, $type2)
{
	return usePreparedExecuteBlade ("DELETE FROM PortCompat WHERE (type1 = ? AND type2 = ?) OR (type1 = ? AND type2 = ?)", array ($type1, $type2, $type2, $type1));
}

// Returns an array of all object type pairs from the ObjectParentCompat table.
function getObjectParentCompat ()
{
	$result = usePreparedSelectBlade
	(
		'SELECT parent_objtype_id, child_objtype_id, d1.dict_value AS parent_name, d2.dict_value AS child_name, ' .
		'(SELECT COUNT(*) FROM EntityLink EL ' .
		"LEFT JOIN Object PO ON (EL.parent_entity_id = PO.id AND EL.parent_entity_type = 'object') " .
		"LEFT JOIN Object CO ON (EL.child_entity_id = CO.id AND EL.child_entity_type = 'object') " .
		'WHERE PO.objtype_id = parent_objtype_id AND CO.objtype_id = child_objtype_id) AS count ' .
		'FROM ObjectParentCompat AS pc INNER JOIN Dictionary AS d1 ON pc.parent_objtype_id = d1.dict_key ' .
		'INNER JOIN Dictionary AS d2 ON pc.child_objtype_id = d2.dict_key ' .
		'ORDER BY parent_name, child_name'
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// Used to determine if a type of object may have a parent or not
function objectTypeMayHaveParent ($objtype_id)
{
	$result = usePreparedSelectBlade ('SELECT COUNT(*) FROM ObjectParentCompat WHERE child_objtype_id = ?', array ($objtype_id));
	return $result->fetchColumn() > 0;
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
		$ret[$item['txt']] = $row[0] == '' ? 0 : $row[0];
		unset ($result);
	}
	return $ret;
}

function getPortsCount ($object_id)
{
	$result = usePreparedSelectBlade ("SELECT COUNT(id) FROM Port WHERE object_id = ?", array($object_id));
	return $result->fetchColumn();
}

# FIXME: this function is not used any more
function commitDeleteChapter ($chapter_no)
{
	usePreparedDeleteBlade ('Chapter', array ('id' => $chapter_no, 'sticky' => 'no'));
}

// This is a dictionary accessor. We perform link rendering, so the user sees
// nice <select> drop-downs.
function readChapter ($chapter_id, $style = '')
{
	$result = usePreparedSelectBlade ('SELECT id FROM Chapter WHERE id = ?', array ($chapter_id));
	if (FALSE === $result->fetchColumn())
		throw new EntityNotFoundException ('chapter', $chapter_id);
	unset ($result);
	$result = usePreparedSelectBlade
	(
		'SELECT dict_key, dict_value AS value FROM Dictionary WHERE chapter_id = ?',
		array ($chapter_id)
	);
	$chapter = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		if ($style == 'r')
			$value = $row['value'];
		else
		{
			parseWikiLink ($row);
			$value = ($style == 'a' ? $row['a_value'] : $row['o_value']);
		}
		$chapter[$row['dict_key']] = $value;
	}
	// SQL ORDER BY had no sense, because we need to sort after link rendering, not before.
	// Try to sort after the parsing in the same way as ORDER BY would do.
	// (SORT_FLAG_CASE is only available in PHP 5.4.0 and later.)
	asort ($chapter, defined ('SORT_FLAG_CASE') ? (SORT_STRING | SORT_FLAG_CASE) : SORT_STRING);
	return $chapter;
}

// Return refcounters for all given keys of the given chapter.
function getChapterRefc ($chapter_id, $keylist)
{
	$ret = array_fill_keys ($keylist, 0);
	switch ($chapter_id)
	{
	case CHAP_OBJTYPE:
		// ObjectType chapter is referenced by AttributeMap and Object tables
		$query = 'SELECT dict_key AS uint_value, (SELECT COUNT(*) FROM AttributeMap WHERE objtype_id = dict_key) + ' .
			'(SELECT COUNT(*) FROM Object WHERE objtype_id = dict_key) AS refcnt FROM Dictionary WHERE chapter_id = ?';
		break;
	default:
		// Find the list of all assigned values of dictionary-addressed attributes, each with
		// chapter/word keyed reference counters.
		$query = 'SELECT uint_value, count(object_id) AS refcnt
			FROM AttributeMap am
			INNER JOIN AttributeValue av ON am.attr_id = av.attr_id
			INNER JOIN Object o ON o.id = av.object_id
			WHERE am.chapter_id = ? AND o.objtype_id = am.objtype_id
			GROUP BY uint_value';
		break;
	}
	$result = usePreparedSelectBlade ($query, array ($chapter_id));
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['uint_value']] = $row['refcnt'];
	return $ret;
}

// Return references counter for each of the given OIF IDs. This includes not
// only PortCompat and PortInterfaceCompat but also Port even though the latter
// is not based on PortOuterInterface directly.
function getPortOIFRefc()
{
	$result = usePreparedSelectBlade
	(
		'SELECT POI.id, (' .
		'(SELECT COUNT(*) FROM PortCompat WHERE type1 = id) + ' .
		'(SELECT COUNT(*) FROM Port WHERE type = POI.id) + ' .
		'(SELECT COUNT(*) FROM PortInterfaceCompat WHERE oif_id = POI.id)' .
		') AS refcnt FROM PortOuterInterface AS POI'
	);
	return reduceSubarraysToColumn (reindexById ($result->fetchAll (PDO::FETCH_ASSOC)), 'refcnt');
}

function getAttrType ($attr_id)
{
	$result = usePreparedSelectBlade ('SELECT type FROM Attribute WHERE id = ?' , array ($attr_id));
	return $result->fetchColumn();
}

function getObjTypeAttrMap ($objtype_id)
{
	$result = usePreparedSelectBlade ('SELECT id, type, name, chapter_id, sticky FROM Attribute INNER JOIN AttributeMap ON id = attr_id WHERE objtype_id = ?' , array ($objtype_id));
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}

// Return a list of all stickers with sticker map applied. Each sticker records will
// list all its ways on the map with refcnt set.
// The function is pretty heavy, uses temporary tables and scans many rows,
// so try to not use it unless it is really necessary
function getAttrMap ()
{
	static $cached_result = NULL;
	if (isset ($cached_result))
		return $cached_result;

	$result = usePreparedSelectBlade
	(
		'SELECT id, type, name, chapter_id, sticky, (SELECT dict_value FROM Dictionary WHERE dict_key = objtype_id) '.
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
			'sticky' => $row['sticky'],
			'refcnt' => $row['refcnt'],
		);
		if ($row['type'] == 'dict')
		{
			$application['chapter_no'] = $row['chapter_id'];
			$application['chapter_name'] = $row['chapter_name'];
		}
		$ret[$row['id']]['application'][] = $application;
	}
	$cached_result = $ret;
	return $ret;
}

function commitSupplementAttrMap ($attr_id, $objtype_id, $chapter_no = NULL)
{
	if ($objtype_id <= 0)
		throw new InvalidArgException ('objtype_id', $objtype_id);
	if (getAttrType ($attr_id) != 'dict')
		$chapter_no = NULL;
	elseif ($chapter_no === NULL)
		throw new InvalidArgException ('chapter_no', '(NULL)', 'must not be NULL for a [D] attribute');

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
		$query .= ' WHERE O.id IN (' . questionMarks (count ($object_set)) . ')';

	$result = usePreparedSelectBlade ($query, $object_set);
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
			case 'date':
				$record['value'] = $row['uint_value'];
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
	$ret = array();
	if (isset ($object_attribute_cache[$object_id]))
		return $object_attribute_cache[$object_id];

	$tmp = fetchAttrsForObjects (array ($object_id));
	if (isset ($tmp[$object_id]))
		$ret = $object_attribute_cache[$object_id] = $tmp[$object_id];
	return $ret;
}

// returns the same data as getAttrValues, but sorts the result array
// by the attr_name using SQL server's collation
function getAttrValuesSorted ($object_id)
{
	static $attr_order = NULL;
	if (! isset ($attr_order))
	{
		$attr_order = array();
		$result = usePreparedSelectBlade ("SELECT id FROM Attribute ORDER by name");
		$i = 0;
		foreach ($result->fetchAll (PDO::FETCH_COLUMN, 0) as $attr_id)
			$attr_order[$attr_id] = $i++;
		unset ($result);
	}
	return customKsort (getAttrValues ($object_id), $attr_order);
}

// FIXME: This function causes RTDatabaseError if the attribute is not
// enabled for the given object in AttributeMap. It would be better to detect
// the mismatch here and throw InvalidArgException instead.
function commitUpdateAttrValue ($object_id, $attr_id, $value = '')
{
	global $object_attribute_cache;
	if (isset ($object_attribute_cache[$object_id]))
		unset ($object_attribute_cache[$object_id]);
	$result = usePreparedSelectBlade
	(
		'SELECT A.type AS attr_type, AV.attr_id, AV.uint_value, AV.float_value, AV.string_value ' .
		'FROM Attribute AS A ' .
		'LEFT JOIN AttributeValue AS AV ON A.id = AV.attr_id AND AV.object_id = ? ' .
		'WHERE A.id = ?',
		array ($object_id, $attr_id)
	);
	if (! $row = $result->fetch (PDO::FETCH_ASSOC))
		throw new InvalidArgException ('attr_id', $attr_id, 'No such attribute');
	$attr_type = $row['attr_type'];
	unset ($result);
	switch ($attr_type)
	{
		case 'uint':
		case 'float':
		case 'string':
			$column = $attr_type . '_value';
			break;
		case 'dict':
		case 'date':
			$column = 'uint_value';
			break;
		default:
			throw new RackTablesError ("Unknown attribute type '${attr_type}' for object_id ${object_id} attr_id ${attr_id}", RackTablesError::INTERNAL);
	}
	if (isset ($row['attr_id']))
	{
		// AttributeValue row present in table
		$where = array ('object_id' => $object_id, 'attr_id' => $attr_id);
		if ($value == '')
			usePreparedDeleteBlade ('AttributeValue', $where);
		elseif ($row[$column] !== $value)
			usePreparedUpdateBlade ('AttributeValue', array ($column => $value), $where);
	}
	elseif ($value != '')
		usePreparedInsertBlade
		(
			'AttributeValue',
			array
			(
				$column => $value,
				'object_id' => $object_id,
				'object_tid' => getObjectType ($object_id),
				'attr_id' => $attr_id,
			)
		);
}

function convertPDOException ($e)
{
	switch ($e->getCode() . '-' . $e->errorInfo[1])
	{
	case '23000-1062':
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

function makeSetSQL ($column_names)
{
	if (! count ($column_names))
		throw new InvalidArgException ('column_names', '(empty array)', 'must not be empty');
	$tmp = array();
	// Same syntax works for NULL as well.
	foreach ($column_names as $each)
		$tmp[] = "${each}=?";
	return implode (', ', $tmp);
}

function makeWhereSQL ($where_columns, $conjunction, &$params = array())
{
	if (! in_array (strtoupper ($conjunction), array ('AND', '&&', 'OR', '||', 'XOR')))
		throw new InvalidArgException ('conjunction', $conjunction, 'invalid operator');
	if (! count ($where_columns))
		throw new InvalidArgException ('where_columns', '(empty array)', 'must not be empty');
	$params = array();
	$tmp = array();
	foreach ($where_columns as $colname => $colvalue)
		if ($colvalue === NULL)
			$tmp[] = "${colname} IS NULL";
		elseif (is_array ($colvalue))
		{
			// Suppress any string keys to keep array_merge() from overwriting.
			$params = array_merge ($params, array_values ($colvalue));
			$tmp[] = sprintf ('%s IN(%s)', $colname, questionMarks (count ($colvalue)));
		}
		else
		{
			$tmp[] = "${colname}=?";
			$params[] = $colvalue;
		}
	return implode (" ${conjunction} ", $tmp);
}

// This swiss-knife blade deletes any number of records from the specified table
// using the specified key names and values.
// returns integer - affected rows count. Throws exception on error
function usePreparedDeleteBlade ($tablename, $columns = array(), $conjunction = 'AND')
{
	global $dbxlink;
	if (! count ($columns))
		throw new InvalidArgException ('columns', '(empty array)', 'in this function DELETE must have WHERE');
	$query = "DELETE FROM ${tablename} WHERE " . makeWhereSQL ($columns, $conjunction, $where_values);
	try
	{
		$prepared = $dbxlink->prepare ($query);
		$prepared->execute ($where_values);
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
function usePreparedUpdateBlade ($tablename, $set_columns = array(), $where_columns = array(), $conjunction = 'AND')
{
	global $dbxlink;
	if (! count ($set_columns))
		throw new InvalidArgException ('set_columns', '(empty array)', 'UPDATE must have SET');
	if (! count ($where_columns))
		throw new InvalidArgException ('where_columns', '(empty array)', 'in this function UPDATE must have WHERE');
	$query = "UPDATE ${tablename} SET " . makeSetSQL (array_keys ($set_columns));
	$query .= ' WHERE ' . makeWhereSQL ($where_columns, $conjunction, $where_values);
	try
	{
		$prepared = $dbxlink->prepare ($query);
		$prepared->execute (array_merge (array_values ($set_columns), $where_values));
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
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC), 'varname');
}

function loadUserConfigCache ($username)
{
	if ($username == '')
		throw new InvalidArgException ('username', $username, 'must not be empty');
	$result = usePreparedSelectBlade ('SELECT varname, varvalue FROM UserConfig WHERE user = ?', array ($username));
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC), 'varname');
}

function loadThumbCache ($rack_id)
{
	$ret = NULL;
	$result = usePreparedSelectBlade ('SELECT thumb_data FROM RackThumbnail WHERE rack_id = ? AND thumb_data IS NOT NULL', array ($rack_id));
	$row = $result->fetch (PDO::FETCH_ASSOC);
	if ($row)
		$ret = base64_decode ($row['thumb_data']);
	return $ret;
}

function executeAutoPorts ($object_id)
{
	foreach (getAutoPorts (spotEntity ('object', $object_id)) as $autoport)
		commitAddPort ($object_id, $autoport['name'], $autoport['type'], '', '');
}

// Return only explicitly listed tags, the rest of the chain will be
// generated/deducted later at higher levels.
// Result is a chain: randomly indexed taginfo list.
function loadEntityTags ($entity_realm, $entity_id)
{
	$result = usePreparedSelectBlade
	(
		"SELECT tt.id, tag FROM " .
		"TagStorage AS ts INNER JOIN TagTree AS tt ON ts.tag_id = tt.id " .
		"WHERE entity_realm = ? AND entity_id = ? " .
		"ORDER BY tt.tag",
		array ($entity_realm, $entity_id)
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}

function cacheDictAttrValues()
{
	global $dict_attr_cache;
	$dict_attr_cache = array();
	$result = usePreparedSelectBlade ("
SELECT
	AV.attr_id,
	AV.uint_value,
	AV.object_id
FROM
	AttributeValue as AV
	JOIN  Attribute as A ON AV.attr_id = A.id
WHERE
	A.type = 'dict'
	AND uint_value IS NOT NULL
");
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$dict_attr_cache[$row['object_id']][$row['attr_id']] = $row['uint_value'];
}

# Universal autotags generator, a complementing function for loadEntityTags().
# Bypass key isn't strictly typed, but interpreted depending on the realm.
function generateEntityAutoTags ($cell)
{
	global $dict_attr_cache;
	$ret = array();
	if (! array_key_exists ('realm', $cell))
		throw new InvalidArgException ('cell', '(array)', 'malformed structure');
	switch ($cell['realm'])
	{
		case 'location':
			$ret[] = array ('tag' => '$locationid_' . $cell['id']);
			$ret[] = array ('tag' => '$any_location');
			break;
		case 'row':
			$ret[] = array ('tag' => '$rowid_' . $cell['id']);
			$ret[] = array ('tag' => '$any_row');
			break;
		case 'rack':
			$ret[] = array ('tag' => '$rackid_' . $cell['id']);
			$ret[] = array ('tag' => '$any_rack');
			break;
		case 'object':
			$ret[] = array ('tag' => '$id_' . $cell['id']);
			$ret[] = array ('tag' => '$typeid_' . $cell['objtype_id']);
			$ret[] = array ('tag' => '$any_object');
			if ($cell['name'] == '')
				$ret[] = array ('tag' => '$nameless');
			if (validTagName ('$cn_' . $cell['name'], TRUE))
				$ret[] = array ('tag' => '$cn_' . $cell['name']);
			if ($cell['rack_id'] == '' && $cell['container_id'] == '')
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
			$dict_attrs = array();
			if (isset ($dict_attr_cache))
			{
				if (isset ($dict_attr_cache[$cell['id']]))
					$dict_attrs = $dict_attr_cache[$cell['id']];
			}
			else
			{
				foreach (getAttrValues($cell['id']) as $attr_id => $attr_record)
					if (isset ($attr_record['key']))
						$dict_attrs[$attr_id] = $attr_record['key'];
			}
			foreach ($dict_attrs as $attr_id => $key)
				$ret[] = array ('tag' => "\$attr_{$attr_id}_{$key}");
			break;
		case 'ipv4net':
			// v4-only rules
			$ret[] = array ('tag' => '$ip4net-' . str_replace ('.', '-', $cell['ip']) . '-' . $cell['mask']);
		case 'ipv6net':
			// common (v4 & v6) rules
			$ver = $cell['realm'] == 'ipv4net' ? 4 : 6;
			$ret[] = array ('tag' => "\$ip${ver}netid_" . $cell['id']);
			$ret[] = array ('tag' => "\$any_ip${ver}net");
			$ret[] = array ('tag' => '$any_net');

			$ret[] = array ('tag' => '$masklen_eq_' . $cell['mask']);

			if ($cell['vlanc'])
				$ret[] = array ('tag' => '$runs_8021Q');

			foreach ($cell['8021q'] as $vlan_info)
				$ret[] = array ('tag' => '$vlan_' . $vlan_info['vlan_id']);

			foreach (array_keys ($cell['spare_ranges']) as $mask)
				$ret[] = array ('tag' => '$spare_' . $mask);

			if ($cell['kidc'] > 0)
				$ret[] = array ('tag' => '$aggregate');
			break;
		case 'ipv4vs':
			$ret[] = array ('tag' => '$ipvsid_' . $cell['id']);
			if (strlen ($cell['vip_bin']) == 16)
				$ret[] = array ('tag' => '$any_ipv6vs');
			else
				$ret[] = array ('tag' => '$any_ipv4vs');
			$ret[] = array ('tag' => '$any_vs');
			if ($cell['refcnt'] == 0)
				$ret[] = array ('tag' => '$unused');
			$ret[] = array ('tag' => '$type_' . strtolower ($cell['proto'])); // $type_tcp, $type_udp or $type_mark
			break;
		case 'ipvs':
			$ret[] = array ('tag' => '$ipvsid_' . $cell['id']);
			$ret[] = array ('tag' => '$any_vs');
			break;
		case 'ipv4rspool':
			$ret[] = array ('tag' => '$ipv4rspid_' . $cell['id']);
			$ret[] = array ('tag' => '$any_ipv4rsp');
			$ret[] = array ('tag' => '$any_rsp');
			if ($cell['refcnt'] == 0)
				$ret[] = array ('tag' => '$unused');
			break;
		case 'user':
			# {$username_XXX} autotag is generated always, but {$userid_XXX}
			# appears only for accounts that exist in local database.
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
	# {$untagged} doesn't apply to users
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

// Return a tag chain with all DB tags on it. ORDER BY is important as it
// enables treeFromList() and sortTree() to do their jobs properly. Doing
// the same sorting in PHP is possible but complicated and may deliver
// results different from MySQL.
function getTagList ($extra_sql = '')
{
	$result = usePreparedSelectBlade ("SELECT id, parent_id, is_assignable, tag FROM TagTree ORDER BY tag ${extra_sql}");
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}

function getTagUsage ($ignore_cache = FALSE)
{
	global $taglist;
	static $ret = NULL;
	if (isset ($ret) && ! $ignore_cache)
		return $ret;
	$ret = array();

	foreach ($taglist as $id => $taginfo)
		$ret[$id] = $taginfo + array('refcnt' => array('total' => 0));

	$result = usePreparedSelectBlade ("SELECT entity_realm AS realm, tag_id AS id, count(*) AS refcnt FROM TagStorage GROUP BY tag_id, entity_realm");
	while ($row = $result->fetch(PDO::FETCH_ASSOC))
	{
		$ret[$row['id']]['refcnt'][$row['realm']] = $row['refcnt'];
		$ret[$row['id']]['refcnt']['total'] += $row['refcnt'];
		// introduce the 'pseudo'-realm 'ipnet' which combines 'ipv4net' and 'ipv6net' realms.
		if ($row['realm'] == 'ipv4net' || $row['realm'] == 'ipv6net')
			if (isset ($ret[$row['id']]['refcnt']['ipnet']))
				$ret[$row['id']]['refcnt']['ipnet'] += $row['refcnt'];
			else
				$ret[$row['id']]['refcnt']['ipnet'] = $row['refcnt'];
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
	return usePreparedDeleteBlade ('TagStorage', array ('entity_realm' => $entity_realm, 'entity_id' => $entity_id, 'tag_id' => $tag_id));
}

// A tag's parent may not be one of its children, the tag itself or a tag
// that does not belong to the forest of rooted trees because of a cycle.
function commitUpdateTag ($tag_id, $tag_name, $parent_id, $is_assignable)
{
	global $dbxlink;
	$dbxlink->beginTransaction();
	try
	{
		// Use the copy from within the transaction.
		assertValidParentId (addTraceToNodes (getTagList ('FOR UPDATE')), $tag_id, $parent_id);
		usePreparedUpdateBlade
		(
			'TagTree',
			array
			(
				'tag' => $tag_name,
				'parent_id' => nullIfZero ($parent_id),
				'is_assignable' => $is_assignable
			),
			array ('id' => $tag_id)
		);
		$dbxlink->commit();
	}
	catch (PDOException $pe)
	{
		$dbxlink->rollBack();
		throw convertPDOException ($pe);
	}
	catch (Exception $e)
	{
		$dbxlink->rollBack();
		throw $e;
	}
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
// on the implicit list already). Then remove any other records that
// appear on the "implicit" side of the chain. This will make sure,
// that both the tag base is still minimal and all requested tags appear on
// the resulting tag chain.
// Return TRUE, if any changes were committed.
function rebuildTagChainForEntity ($realm, $entity_id, $extrachain = array(), $replace = FALSE)
{
	// Put the current explicit sub-chain into a buffer and merge all tags from
	// the extra chain that aren't there yet.
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
function produceTagsForNewRecord ($realm, $tagidlist, $record_id)
{
	foreach (getExplicitTagsOnly (buildTagChainFromIds ($tagidlist)) as $taginfo)
		addTagForEntity ($realm, $record_id, $taginfo['id']);
}

function createIPv4Prefix ($range = '', $name = '', $is_connected = FALSE, $taglist = array(), $vlan_ck = NULL)
{
	// $range is in x.x.x.x/x format, split into ip/mask vars
	$rangeArray = explode('/', $range);
	if (count ($rangeArray) != 2)
		throw new InvalidRequestArgException ('range', $range, 'Invalid IPv4 prefix');
	$ip = $rangeArray[0];
	$mask = $rangeArray[1];
	$forbidden_ranges = array
	(
		constructIPRange ("\0\0\0\0", 8), # 0.0.0.0/8
		constructIPRange ("\xF0\0\0\0", 4), # 240.0.0.0/4
	);
	$net = constructIPRange (ip4_parse ($ip), $mask);
	foreach ($forbidden_ranges as $invalid_net)
		if (IPNetContainsOrEqual ($invalid_net, $net))
			throw new InvalidArgException ('range', $range, 'Reserved IPv4 network');

	usePreparedInsertBlade
	(
		'IPv4Network',
		array
		(
			'ip' => ip4_bin2db ($net['ip_bin']),
			'mask' => $mask,
			'name' => $name
		)
	);
	$network_id = lastInsertID();
	lastCreated ('ipv4net', $network_id);

	if ($is_connected && $mask < 31)
	{
		updateV4Address ($net['ip_bin'], 'network', 'yes');
		updateV4Address (ip_last ($net), 'broadcast', 'yes');
	}
	produceTagsForNewRecord ('ipv4net', $taglist, $network_id);
	if ($vlan_ck != NULL)
		commitSupplementVLANIPv4 ($vlan_ck, $network_id);
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
	$net = constructIPRange (ip6_parse ($ip), $mask);
	usePreparedInsertBlade
	(
		'IPv6Network',
		array
		(
			'ip' => $net['ip_bin'],
			'last_ip' => ip_last ($net),
			'mask' => $mask,
			'name' => $name
		)
	);
	$network_id = lastInsertID();
	lastCreated ('ipv6net', lastInsertID());

	# RFC3513 2.6.1 - Subnet-Router anycast
	if ($is_connected)
		updateV6Address ($net['ip_bin'], 'Subnet-Router anycast', 'yes');
	produceTagsForNewRecord ('ipv6net', $taglist, $network_id);
	if ($vlan_ck != NULL)
		commitSupplementVLANIPv6 ($vlan_ck, $network_id);
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
	$result = usePreparedSelectBlade ("SELECT script_text FROM Script WHERE script_name = ?", array ($name));
	return nullIfFalse ($result->fetchColumn());
}

function saveScript ($name, $text)
{
	if ($name == '')
		throw new InvalidArgException ('name', $name, 'must not be empty');
	if (!isset ($text))
		return deleteScript ($name);
	return usePreparedExecuteBlade
	(
		'INSERT INTO Script (script_name, script_text) VALUES (?, ?) ' .
		'ON DUPLICATE KEY UPDATE script_text=?',
		array ($name, $text, $text)
	);
}

function deleteScript ($name)
{
	if ($name == '')
		throw new InvalidArgException ('name', $name);
	return usePreparedDeleteBlade ('Script', array ('script_name' => $name));
}

function newPortForwarding ($object_id, $localip_bin, $localport, $remoteip_bin, $remoteport, $proto, $description)
{
	if (NULL === getIPv4AddressNetworkId ($localip_bin))
		throw new InvalidArgException ('localip_bin', ip4_format ($localip_bin), 'address does not belong to a known network');
	if (NULL === getIPv4AddressNetworkId ($remoteip_bin))
		throw new InvalidArgException ('remoteip_bin', ip4_format ($remoteip_bin), 'address does not belong to a known network');
	if ( $proto == "ALL" )
	{
		$localport = 0;
		$remoteport = 0;
	}
	else
	{
		if ($localport <= 0 || $localport >= 65536)
			throw new InvalidArgException ('localport', $localport, 'Invaild port');
		if ($remoteport <= 0 || $remoteport >= 65536)
			throw new InvalidArgException ('remoteport', $remoteport, 'Invaild port');
	}

	return usePreparedInsertBlade
	(
		'IPv4NAT',
		array
		(
			'object_id' => $object_id,
			'localip' => ip4_bin2db ($localip_bin),
			'localport' => $localport,
			'remoteip' => ip4_bin2db ($remoteip_bin),
			'remoteport' => $remoteport,
			'proto' => $proto,
			'description' => $description,
		)
	);
}

function deletePortForwarding ($object_id, $localip_bin, $localport, $remoteip_bin, $remoteport, $proto)
{
	return usePreparedDeleteBlade
	(
		'IPv4NAT',
		array
		(
			'object_id' => $object_id,
			'localip' => ip4_bin2db ($localip_bin),
			'localport' => $localport,
			'remoteip' => ip4_bin2db ($remoteip_bin),
			'remoteport' => $remoteport,
			'proto' => $proto,
		)
	);
}

function updatePortForwarding ($object_id, $localip_bin, $localport, $remoteip_bin, $remoteport, $proto, $description)
{
	return usePreparedUpdateBlade
	(
		'IPv4NAT',
		array ('description' => $description),
		array
		(
			'object_id' => $object_id,
			'localip' => ip4_bin2db ($localip_bin),
			'localport' => $localport,
			'remoteip' => ip4_bin2db ($remoteip_bin),
			'remoteport' => $remoteport,
			'proto' => $proto,
		)
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
		"localip, ".
		"localport, ".
		"remoteip, ".
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
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$row['localip_bin'] = ip4_int2bin ($row['localip']);
		$row['localip'] = ip_format ($row['localip_bin']);
		$row['remoteip_bin'] = ip4_int2bin ($row['remoteip']);
		$row['remoteip'] = ip_format ($row['remoteip_bin']);
		$ret['out'][] = $row;
	}
	unset ($result);

	$result = usePreparedSelectBlade
	(
		"select ".
		"proto, ".
		"localip, ".
		"localport, ".
		"remoteip, ".
		"remoteport, ".
		"IPv4NAT.object_id as object_id, ".
		"Object.name as object_name, ".
		"description ".
		"from ((IPv4NAT join IPv4Allocation on remoteip=IPv4Allocation.ip) join Object on IPv4NAT.object_id=Object.id) ".
		"where IPv4Allocation.object_id=? ".
		"order by remoteip, remoteport, proto, localip, localport",
		array ($object_id)
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$row['localip_bin'] = ip4_int2bin ($row['localip']);
		$row['localip'] = ip_format ($row['localip_bin']);
		$row['remoteip_bin'] = ip4_int2bin ($row['remoteip']);
		$row['remoteip'] = ip_format ($row['remoteip_bin']);
		$ret['in'][] = $row;
	}
	return $ret;
}

// Return a list of files that are not linked to the specified record. This list
// will be used by printSelect().
function getAllUnlinkedFiles ($entity_type, $entity_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, name FROM File ' .
		'WHERE id NOT IN (SELECT file_id FROM FileLink WHERE entity_type = ? AND entity_id = ?) ' .
		'ORDER BY name, id',
		array ($entity_type, $entity_id)
	);
	return reduceSubarraysToColumn (reindexById ($result->fetchAll (PDO::FETCH_ASSOC)), 'name');
}

// FIXME: return a standard cell list, so upper layer can iterate over
// it conveniently.
function getFilesOfEntity ($entity_type, $entity_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT FileLink.file_id, FileLink.id AS link_id, name, type, size, ctime, mtime, atime, comment ' .
		'FROM FileLink LEFT JOIN File ON FileLink.file_id = File.id ' .
		'WHERE FileLink.entity_type = ? AND FileLink.entity_id = ? ORDER BY name',
		array ($entity_type, $entity_id)
	);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
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
	$result = usePreparedSelectBlade
	(
		'SELECT id, name, type, size, ctime, mtime, atime, contents, comment ' .
		'FROM File WHERE id = ?',
		array ($file_id)
	);
	if (($row = $result->fetch (PDO::FETCH_ASSOC)) == NULL)
		// FIXME: isn't this repeating the code already in spotEntity()?
		throw new EntityNotFoundException ('file', $file_id);
	return $row;
}

function getFileCache ($file_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT File.thumbnail FROM File ' .
		'WHERE File.id = ? and File.thumbnail IS NOT NULL',
		array ($file_id)
	);
	return $result->fetchColumn();
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
	$result = usePreparedSelectBlade
	(
		'SELECT id, entity_type, entity_id FROM FileLink ' .
		'WHERE file_id = ? ORDER BY entity_type, entity_id',
		array ($file_id)
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
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
	$ret["Unlinked files"] = $result->fetchColumn ();
	unset ($result);

	// Find total number of files
	$result = usePreparedSelectBlade ('SELECT COUNT(*) FROM File');
	$ret["Total files"] = $result->fetchColumn ();

	return $ret;
}

// returns file id
// throws an exception if error occured
function commitAddFile ($name, $type, $contents, $comment)
{
	global $dbxlink;
	switch ($type)
	{
	case 'image/x-png':
		$type = 'image/png';
		break;
	case 'image/pjpeg':
		$type = 'image/jpeg';
		break;
	default:
	}
	try
	{
		# File.size has no default value, set to 0 with MySQL strict mode in mind.
		$query = $dbxlink->prepare ('INSERT INTO File (name, type, size, ctime, mtime, atime, contents, comment) VALUES (?, ?, 0, NOW(), NOW(), NOW(), ?, ?)');
		$query->bindParam (1, $name);
		$query->bindParam (2, $type);
		$query->bindParam (3, $contents, PDO::PARAM_LOB);
		$query->bindParam (4, $comment);
		$query->execute();
		$file_id = lastInsertID();
		usePreparedExecuteBlade ('UPDATE File SET size = LENGTH(contents) WHERE id = ?', array ($file_id));
		return $file_id;
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
	$result = usePreparedSelectBlade
	(
		'SELECT id, sticky, name, count(chapter_id) as wordc ' .
		'FROM Chapter LEFT JOIN Dictionary ON Chapter.id = chapter_id ' .
		'GROUP BY id ORDER BY name'
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}

// Return file id by file name.
function findFileByName ($filename)
{
	$result = usePreparedSelectBlade ('SELECT id FROM File WHERE name = ?', array ($filename));
	return nullIfFalse ($result->fetchColumn());
}

function fetchLDAPCacheRow ($username, $extrasql = '')
{
	$result = usePreparedSelectBlade
	(
		'SELECT TIMESTAMPDIFF(SECOND, first_success, now()) AS success_age, ' .
		'TIMESTAMPDIFF(SECOND, last_retry, now()) AS retry_age, displayed_name, memberof, successful_hash ' .
		'FROM LDAPCache WHERE presented_username = ? ' . $extrasql,
		array ($username)
	);
	$row = $result->fetch (PDO::FETCH_ASSOC);
	if ($row)
	{
		$members = unserialize (base64_decode ($row['memberof']));
		$row['memberof'] = is_array ($members) ? $members : array();
	}
	return $row;
}

// locks LDAPCache row for given username or throws an exception
function acquireLDAPCache ($username, $max_tries = 2)
{
	$self = __FUNCTION__;
	global $dbxlink;

	// guarantee there is a row to lock
	usePreparedExecuteBlade ("INSERT IGNORE INTO LDAPCache (presented_username) VALUES (?)", array ($username));
	$dbxlink->beginTransaction();

	if ($row = fetchLDAPCacheRow ($username, 'FOR UPDATE'))
		return $row;

	// maybe another instance deleted our row before we've locked it. Try again
	if ($max_tries > 0)
		return $self ($username, $max_tries - 1);

	// the problem still persists after retries, throw an exception
	throw new RackTablesError ("Unable to acquire lock on LDAPCache", RackTablesError::INTERNAL);
}

function releaseLDAPCache ()
{
	global $dbxlink;
	$dbxlink->commit();
}

// This actually changes only last_retry.
function touchLDAPCacheRecord ($username)
{
	usePreparedExecuteBlade ('UPDATE LDAPCache SET last_retry=NOW() WHERE presented_username=?', array ($username));
}

function replaceLDAPCacheRecord ($username, $password_hash, $dname, $memberof)
{
	usePreparedDeleteBlade ('LDAPCache', array ('presented_username' => $username));
	usePreparedInsertBlade ('LDAPCache',
		array
		(
			'presented_username' => $username,
			'successful_hash' => $password_hash,
			'displayed_name' => $dname,
			'memberof' => base64_encode (serialize ($memberof)),
		)
	);
}

function deleteLDAPCacheRecord ($username)
{
	usePreparedDeleteBlade ('LDAPCache', array ('presented_username' => $username));
}

// Purge all records older than the threshold, as well as any records made in future.
// Calling this function w/o argument purges the whole LDAP cache.
function discardLDAPCache ($maxseconds = 0)
{
	usePreparedExecuteBlade ('DELETE FROM LDAPCache WHERE TIMESTAMPDIFF(SECOND, first_success, NOW()) >= ? OR NOW() < first_success', array ($maxseconds));
}

function getUserIDByUsername ($username)
{
	$result = usePreparedSelectBlade ('SELECT user_id FROM UserAccount WHERE user_name = ?', array ($username));
	return nullIfFalse ($result->fetchColumn());
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

// DEPRECATED but snmpgeneric.php uses it, remove in 0.21.0.
function alreadyUsedL2Address ($address, $my_object_id)
{
	try
	{
		assertUniqueL2Addresses (array ($address), $my_object_id);
		return FALSE;
	}
	catch (InvalidArgException $iae)
	{
		return TRUE;
	}
}

// Raise an exception if any of the given MAC/WWN addresses (less empty strings)
// belongs to a port with an object ID other than the given. This constraint makes
// it possible to reuse L2 addresses within one object's set of ports and to keep
// them universally unique otherwise. Every L2 address on the input list must have
// been conditioned with l2AddressForDatabase().
function assertUniqueL2Addresses ($db_l2addresses, $my_object_id)
{
	// Reindex the array such that array_merge() below works as expected.
	$db_l2addresses = array_values (array_unique (array_filter ($db_l2addresses, 'strlen')));
	if (0 == count ($db_l2addresses))
		return;
	$qm = questionMarks (count ($db_l2addresses));
	// BINARY in the second comparison is what the query is actually looking for but without
	// the first (non-BINARY) comparison the table index does not work as expected.
	$query = 'SELECT l2address, object_id, name FROM Port ' .
		"WHERE l2address IN(${qm}) AND BINARY l2address IN(${qm}) AND object_id != ? LIMIT 1";
	$params = array_merge ($db_l2addresses, $db_l2addresses, array ($my_object_id));
	$result = usePreparedSelectBlade ($query, $params);
	if ($row = $result->fetch (PDO::FETCH_ASSOC))
		throw new InvalidArgException ('L2 address', $row['l2address'], "already used by object#{$row['object_id']} port '{$row['name']}'");
}

function getPortInterfaceCompat()
{
	$result = usePreparedSelectBlade
	(
		'SELECT iif_id, iif_name, oif_id, oif_name ' .
		'FROM PortInterfaceCompat INNER JOIN PortInnerInterface AS PII ON PII.id = iif_id ' .
		'INNER JOIN PortOuterInterface AS POI ON POI.id = oif_id ' .
		'ORDER BY iif_name, oif_name'
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// Return a set of options for a plain SELECT. These options include the current
// OIF of the given port and all OIFs of its permanent IIF.
// If given port is already linked, returns only types compatible with the remote port's type
function getExistingPortTypeOptions ($portinfo)
{
	$remote_type = NULL;
	if ($portinfo['linked'])
	{
		$remote_portinfo = getPortInfo ($portinfo['remote_id']);
		$result = usePreparedSelectBlade ("
SELECT oif_id, oif_name
FROM PortInterfaceCompat
INNER JOIN PortOuterInterface ON oif_id = id
INNER JOIN PortCompat pc ON pc.type1 = oif_id AND pc.type2 = ?
WHERE iif_id = ?
ORDER BY oif_name
", array ($remote_portinfo['oif_id'], $portinfo['iif_id'])
		);
	}
	else
	{
		$result = usePreparedSelectBlade ("
SELECT oif_id, oif_name
FROM PortInterfaceCompat
INNER JOIN PortOuterInterface ON oif_id = id
WHERE iif_id = ?
ORDER BY oif_name
", array ($portinfo['iif_id'])
		);
	}

	return reduceSubarraysToColumn (reindexById ($result->fetchAll (PDO::FETCH_ASSOC), 'oif_id'), 'oif_name');
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
	$result = usePreparedSelectBlade ('SELECT id, iif_name FROM PortInnerInterface ORDER BY iif_name');
	return reduceSubarraysToColumn (reindexById ($result->fetchAll (PDO::FETCH_ASSOC)), 'iif_name');
}

function getPortOIFOptions()
{
	$result = usePreparedSelectBlade ('SELECT id, oif_name FROM PortOuterInterface ORDER BY oif_name');
	return reduceSubarraysToColumn (reindexById ($result->fetchAll (PDO::FETCH_ASSOC)), 'oif_name');
}

function commitSupplementPIC ($iif_id, $oif_id)
{
	usePreparedInsertBlade
	(
		'PortInterfaceCompat',
		array ('iif_id' => $iif_id, 'oif_id' => $oif_id)
	);
}

function getPortIIFStats ($iif_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT oif_name AS title, COUNT(Port.id) AS max, ' .
		'COUNT(reservation_comment) + ' .
		'COUNT(la.porta) + COUNT(lb.portb) AS current ' .
		'FROM Port INNER JOIN PortOuterInterface AS POI ON type = POI.id ' .
		'LEFT JOIN Link AS la ON la.porta = Port.id ' .
		'LEFT JOIN Link AS lb ON lb.portb = Port.id ' .
		'WHERE iif_id = ? GROUP BY type',
		array ($iif_id)
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function getPortInfo ($port_id)
{
	$result = fetchPortList ('Port.id = ?', array ($port_id));
	return count ($result) ? $result[0] : NULL;
}

function getVLANDomainStats ()
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, group_id, description, ' .
		'(SELECT COUNT(vd.id) FROM VLANDomain vd WHERE vd.group_id = VLANDomain.id) as subdomc, ' .
		'(SELECT COUNT(vlan_id) FROM VLANDescription WHERE domain_id = id) AS vlanc, ' .
		'(SELECT COUNT(ipv4net_id) FROM VLANIPv4 WHERE domain_id = id) AS ipv4netc, ' .
		'(SELECT COUNT(object_id) FROM VLANSwitch WHERE domain_id = id) AS switchc, ' .
		'(SELECT COUNT(port_name) FROM VLANSwitch AS VS INNER JOIN PortVLANMode AS PVM ON VS.object_id = PVM.object_id WHERE domain_id = id) AS portc ' .
		'FROM VLANDomain ORDER BY description'
	);
	$ret = reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
	foreach ($ret as $vdom_id => $domain)
		if ($domain['group_id'])
		{
			// sum only vlans/nets because subdomains have switches/ports of their own
			$ret[$domain['group_id']]['vlanc'] += $domain['vlanc'];
			$ret[$domain['group_id']]['ipv4netc'] += $domain['ipv4netc'];
		}
	return $ret;
}

function getVLANDomainOptions()
{
	$result = usePreparedSelectBlade ('SELECT id, description FROM VLANDomain ORDER BY description');
	return reduceSubarraysToColumn (reindexById ($result->fetchAll (PDO::FETCH_ASSOC)), 'description');
}

function getVLANDomain ($vdid)
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, group_id, description, ' .
		'(SELECT COUNT(vd.id) FROM VLANDomain vd WHERE vd.group_id = VLANDomain.id) as subdomc ' .
		'FROM VLANDomain WHERE id = ?',
		array ($vdid)
	);
	if (!$ret = $result->fetch (PDO::FETCH_ASSOC))
		throw new EntityNotFoundException ('VLAN domain', $vdid);
	unset ($result);
	$ret['vlanlist'] = getDomainVLANList ($vdid);
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

function getDomainGroupMembers ($vdom_group_id)
{
	$result = usePreparedSelectBlade ("SELECT id FROM VLANDomain WHERE group_id = ?", array ($vdom_group_id));
	return $result->fetchAll (PDO::FETCH_COLUMN, 0);
}

// This function is pretty heavy. Consider use of getDomainVLANList instead
// If $strict is false, returns VLANs belonging to the domain or group.
// Otherwise the vlans of group subdomains are not returned.
function getDomainVLANs ($vdom_id, $strict = FALSE)
{
	$self = __FUNCTION__;
	if (! $strict && $members = getDomainGroupMembers ($vdom_id))
	{
		$ret = $self ($vdom_id, TRUE);
		foreach ($members as $member_vdom_id)
			foreach ($self ($member_vdom_id, TRUE) as $vid => $vlan_info)
				if (! isset ($ret[$vid]))
					$ret[$vid] = $vlan_info;
				else
				{
					$ret[$vid]['netc'] += $vlan_info['netc'];
					$ret[$vid]['portc'] += $vlan_info['portc'];
				}
		ksort ($ret, SORT_NUMERIC);
		return $ret;
	}

	$result = usePreparedSelectBlade
	(<<<END
SELECT
	vlan_id,
	vlan_type,
	vlan_descr,
	(SELECT COUNT(ipv4net_id) FROM VLANIPv4 AS VI WHERE VI.domain_id = VD.domain_id and VI.vlan_id = VD.vlan_id) +
	(SELECT COUNT(ipv6net_id) FROM VLANIPv6 AS VI WHERE VI.domain_id = VD.domain_id and VI.vlan_id = VD.vlan_id) AS netc,
	s2.portc
FROM
	VLANDescription VD LEFT JOIN
	(
		SELECT
			PAV.vlan_id as vid,
			COUNT(PAV.port_name) as portc
		FROM
			VLANSwitch VS
			INNER JOIN VLANDescription USING (domain_id)
			INNER JOIN PortAllowedVLAN PAV ON PAV.object_id = VS.object_id AND VLANDescription.vlan_id = PAV.vlan_id
		WHERE VS.domain_id = ?
		GROUP BY PAV.vlan_id
	) AS s2 ON vlan_id = s2.vid
WHERE domain_id = ?
ORDER BY vlan_id

END
		, array ($vdom_id, $vdom_id)
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC), 'vlan_id');
}

// faster than getDomainVLANs, but w/o statistics.
// If $strict is false, returns VLANs belonging to the domain or group.
// Otherwise the vlans of group subdomains are not returned.
function getDomainVLANList ($vdom_id, $strict = FALSE)
{
	if (! $strict && $members = getDomainGroupMembers ($vdom_id))
	{
		$self = __FUNCTION__;
		$ret = $self ($vdom_id, TRUE);
		foreach ($members as $member_vdom_id)
			$ret += $self ($member_vdom_id, TRUE);
		return $ret;
	}

	$result = usePreparedSelectBlade
	(<<<END
SELECT
	vlan_id,
	vlan_type,
	vlan_descr
FROM
	VLANDescription AS VD
WHERE domain_id = ?
ORDER BY vlan_id
END
		, array ($vdom_id)
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC), 'vlan_id');
}

function getVLANSwitches()
{
	$result = usePreparedSelectBlade ('SELECT object_id FROM VLANSwitch');
	return reduceSubarraysToColumn ($result->fetchAll (PDO::FETCH_ASSOC), 'object_id');
}

function getVLANSwitchInfo ($object_id, $extrasql = '')
{
	return array_first (getVLANSwitchInfoRows (array ('object_id' => $object_id), $extrasql));
}

function getVLANSwitchInfoRows ($filter = array(), $extrasql = '')
{
	$query =
		'SELECT object_id, domain_id, template_id, mutex_rev, out_of_sync, last_errno, ' .
		'UNIX_TIMESTAMP(last_change) as last_change, ' .
		'UNIX_TIMESTAMP(last_push_started) as last_push_started, ' .
		'UNIX_TIMESTAMP(last_push_finished) as last_push_finished, ' .
		'UNIX_TIMESTAMP(last_error_ts) as last_error_ts ' .
		'FROM VLANSwitch';
	$params = array();
	if ($filter)
		$query .= ' WHERE ' . makeWhereSQL ($filter, 'AND', $params);
	$query .= ' ' . $extrasql;
	$result = usePreparedSelectBlade ($query, $params);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

// Reads the per-port VLAN configuration for a given object.
// $instance could be either 'desired' or 'cached'.
// $port_names is a filter enumerating names of ports to select. If empty, selects all ports.
function getStored8021QConfig ($object_id, $instance = 'desired', $port_names = array())
{
	global $tablemap_8021q;
	if (!array_key_exists ($instance, $tablemap_8021q))
		throw new InvalidArgException ('instance', $instance);
	$sql_filter = ' WHERE object_id = ?';
	$sql_params = array ($object_id);
	if (count ($port_names) == 1)
	{
		$sql_filter .= ' AND port_name = ?';
		$sql_params[] = array_first ($port_names);
	}
	else if (count ($port_names) > 1)
	{
		$sql_filter .= ' AND port_name IN(' . questionMarks (count ($port_names)) . ')';
		$sql_params = array_merge ($sql_params, $port_names);
	}

	$ret = array();
	$result = usePreparedSelectBlade
	(
		'SELECT port_name, vlan_mode FROM ' . $tablemap_8021q[$instance]['pvm'] . $sql_filter,
		$sql_params
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
		'SELECT port_name, vlan_id FROM ' . $tablemap_8021q[$instance]['pav'] . $sql_filter,
		$sql_params
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['port_name']]['allowed'][] = $row['vlan_id'];
	unset ($result);
	$result = usePreparedSelectBlade
	(
		'SELECT port_name, vlan_id FROM ' . $tablemap_8021q[$instance]['pnv'] . $sql_filter,
		$sql_params
	);
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['port_name']]['native'] = $row['vlan_id'];
	return $ret;
}

function getVlanRow ($vlan_ck)
{
	list ($vdom_id, $vlan_id) = decodeVLANCK ($vlan_ck);
	$query = 'SELECT domain_id, vlan_id, vlan_type AS vlan_prop, vlan_descr, ' .
		'(SELECT description FROM VLANDomain WHERE id = domain_id) AS domain_descr, ' .
		'(SELECT group_id FROM VLANDomain WHERE id = domain_id) AS domain_group_id ' .
		'FROM VLANDescription WHERE domain_id = ? AND vlan_id = ?';
	$result = usePreparedSelectBlade ($query, array ($vdom_id, $vlan_id));
	if (FALSE === $ret = $result->fetch (PDO::FETCH_ASSOC))
		throw new EntityNotFoundException ('VLAN', $vlan_ck);
	$ret['vlan_ck'] = $vlan_ck;
	return $ret;
}

function getVLANInfo ($vlan_ck)
{
	list ($vdom_id, $vlan_id) = decodeVLANCK ($vlan_ck);
	$ret = getVlanRow ($vlan_ck);

	$result = usePreparedSelectBlade
	(
	 	'SELECT ipv4net_id FROM VLANIPv4 WHERE domain_id = ? AND vlan_id = ? ORDER BY ipv4net_id',
		array ($vdom_id, $vlan_id)
	);
	$ret['ipv4nets'] = reduceSubarraysToColumn ($result->fetchAll (PDO::FETCH_ASSOC), 'ipv4net_id');
	unset ($result);
	$result = usePreparedSelectBlade
	(
	 	'SELECT ipv6net_id FROM VLANIPv6 WHERE domain_id = ? AND vlan_id = ? ORDER BY ipv6net_id',
		array ($vdom_id, $vlan_id)
	);
	$ret['ipv6nets'] = reduceSubarraysToColumn ($result->fetchAll (PDO::FETCH_ASSOC), 'ipv6net_id');
	return $ret;
}

// return list of network IDs that are not bound to the given VLAN domain
function getVLANIPv4Options ($except_vdid)
{
	$prepared = usePreparedSelectBlade
	(
		'SELECT id FROM IPv4Network WHERE id NOT IN ' .
		'(SELECT ipv4net_id FROM VLANIPv4 WHERE domain_id = ?)' .
		'ORDER BY ip, mask',
		array ($except_vdid)
	);
	return reduceSubarraysToColumn ($prepared->fetchAll (PDO::FETCH_ASSOC), 'id');
}

// return list of network IDs that are not bound to the given VLAN domain
function getVLANIPv6Options ($except_vdid)
{
	$prepared = usePreparedSelectBlade
	(
		'SELECT id FROM IPv6Network WHERE id NOT IN ' .
		'(SELECT ipv6net_id FROM VLANIPv6 WHERE domain_id = ?)' .
		'ORDER BY ip, mask',
		array ($except_vdid)
	);
	return reduceSubarraysToColumn ($prepared->fetchAll (PDO::FETCH_ASSOC), 'id');
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

// Return a list of switches that have specific VLAN configured on
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
	$changed = 0;
	$changed += usePreparedInsertBlade
	(
		$tablemap_8021q['cached']['pvm'],
		array ('object_id' => $object_id, 'port_name' => $port_name, 'vlan_mode' => $port['mode'])
	);
	$changed += usePreparedInsertBlade
	(
		$tablemap_8021q['desired']['pvm'],
		array ('object_id' => $object_id, 'port_name' => $port_name, 'vlan_mode' => $port['mode'])
	);
	$changed += upd8021QPort ('cached', $object_id, $port_name, $port, NULL);
	$changed += upd8021QPort ('desired', $object_id, $port_name, $port, NULL);
	return $changed ? 1 : 0;
}

function del8021QPort ($object_id, $port_name)
{
	// rely on ON DELETE CASCADE for PortAllowedVLAN and PortNativeVLAN
	global $tablemap_8021q;
	$changed = 0;
	$changed += usePreparedDeleteBlade
	(
		$tablemap_8021q['desired']['pvm'],
		array ('object_id' => $object_id, 'port_name' => $port_name)
	);
	$changed += usePreparedDeleteBlade
	(
		$tablemap_8021q['cached']['pvm'],
		array ('object_id' => $object_id, 'port_name' => $port_name)
	);

	callHook ('portConfChanged', $object_id, $port_name, NULL);
	return $changed ? 1 : 0;
}

// Returns list of tuples ("where_text" params_array) that covers
// all the VLANs in $vlan_list.
// aggregates sparse VLANs into single IN() condition
// each range larger than 5 is returned as separate BETWEEN condition
function makeVlanListWhere ($db_field_name, $vlan_list)
{
	$ret = array();
	$in_list = array();
	foreach (listToRanges ($vlan_list) as $range)
	{
		if ($range['from'] == $range['to'])
			$in_list[] = $range['from'];
		elseif ($range['to'] - $range['from'] < 5)
			for ($i = $range['from']; $i <= $range['to']; ++$i)
				$in_list[] = $i;
		else
			$ret[] = array ("$db_field_name BETWEEN ? AND ?", array ($range['from'], $range['to']));
	}
	if ($in_list)
		$ret[] = array ("$db_field_name IN(" . questionMarks(count ($in_list)) . ')', $in_list);
	return $ret;
}

function upd8021QPort ($instance = 'desired', $object_id, $port_name, $port, $before)
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
	if ($port['mode'] != 'trunk' && ! count ($port['allowed']))
		return 0;
	$changed = 0;
	if (! isset ($before) || $before['mode'] != $port['mode'])
		$changed += usePreparedUpdateBlade
		(
			$tablemap_8021q[$instance]['pvm'],
			array ('vlan_mode' => $port['mode']),
			array ('object_id' => $object_id, 'port_name' => $port_name)
		);


	if (isset ($before))
	{
		$add_list = array_diff ($port['allowed'], $before['allowed']);
		$del_list = array_diff ($before['allowed'], $port['allowed']);
		foreach (makeVlanListWhere ('vlan_id', $del_list) as $where)
			$changed += usePreparedExecuteBlade
			(
				'DELETE FROM ' . $tablemap_8021q[$instance]['pav'] .
				' WHERE object_id = ? AND port_name = ? AND ' . $where[0],
				array_merge (array ($object_id, $port_name), $where[1])
			);
	}
	else
	{
		$add_list = $port['allowed'];
		$changed += usePreparedDeleteBlade (
			$tablemap_8021q[$instance]['pav'],
			array ('object_id' => $object_id, 'port_name' => $port_name)
		);
	}

	// The goal is to INSERT as many rows as there are values in 'allowed' list
	// without wrapping each row with own INSERT (otherwise the SQL connection
	// instantly becomes the bottleneck).
	foreach (makeVlanListWhere ('vlan_id', $add_list) as $where)
		$changed += usePreparedExecuteBlade
		(
			'INSERT INTO ' . $tablemap_8021q[$instance]['pav'] . ' (object_id, port_name, vlan_id) ' .
			'SELECT ?, ?, vlan_id FROM VLANValidID WHERE ' . $where[0],
			array_merge (array ($object_id, $port_name), $where[1])
		);

	if (! $port['native'] && (! isset ($before) || in_array ($before['native'], $port['allowed'])))
		$changed += usePreparedDeleteBlade
		(
			$tablemap_8021q[$instance]['pnv'],
			array ('object_id' => $object_id, 'port_name' => $port_name)
		);
	elseif ($port['native'] && (! isset ($before) || $before['native'] != $port['native']))
		$changed += usePreparedExecuteBlade
		(
			'REPLACE INTO ' . $tablemap_8021q[$instance]['pnv'] .
			' (object_id, port_name, vlan_id) VALUES (?, ?, ?)',
			array ($object_id, $port_name, $port['native'])
		);

	if ($instance == 'desired' && $changed)
		callHook ('portConfChanged', $object_id, $port_name, $port);
	return $changed ? 1 : 0;
}

function replace8021QPorts ($instance = 'desired', $object_id, $before, $changes)
{
	$done = 0;
	foreach ($changes as $pn => $port)
		$done += upd8021QPort ($instance, $object_id, $pn, $port, array_fetch ($before, $pn, NULL));
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
	return count ($rows) == 1 ? $rows[0]['id'] : NULL;
}

// Returns an array of attribute IDs that are mapped to the specified chapter.
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

function setConfigVar ($varname, $varvalue)
{
	global $configCache;
	if (!isset ($configCache))
		throw new RackTablesError ('configuration cache is unavailable', RackTablesError::INTERNAL);
	if (NULL === $var = array_fetch ($configCache, $varname, NULL))
		throw new InvalidArgException ('varname', $varname, 'unknown variable');
	if ($var['is_hidden'] != 'no')
		throw new InvalidArgException ('varname', $varname, 'a hidden variable cannot be changed');
	if ($varvalue == '' && $var['emptyok'] != 'yes')
		throw new InvalidArgException ('varvalue', $varvalue, "'${varname}' must have a non-empty value");
	if ($varvalue != '' && $var['vartype'] == 'uint' && (! is_numeric ($varvalue) || $varvalue < 0 ))
		throw new InvalidArgException ('varvalue', $varvalue, "'${varname}' must be an unsigned integer");
	// Update cache only if the changes went into DB.
	usePreparedUpdateBlade ('Config', array ('varvalue' => $varvalue), array ('varname' => $varname));
	$configCache[$varname]['varvalue'] = $varvalue;
	$configCache[$varname]['defaultvalue'] = $varvalue;
	alterConfigWithUserPreferences();
}

function setUserConfigVar ($varname, $varvalue)
{
	global $configCache;
	global $remote_username;
	if (!isset ($configCache))
		throw new RackTablesError ('configuration cache is unavailable', RackTablesError::INTERNAL);
	if (NULL === $var = array_fetch ($configCache, $varname, NULL))
		throw new InvalidArgException ('varname', $varname, 'unknown variable');
	if ($var['is_userdefined'] != 'yes')
		throw new InvalidArgException ('varname', $varname, 'a system-wide setting cannot be changed by user');
	if ($var['is_hidden'] != 'no')
		throw new InvalidArgException ('varname', $varname, 'a hidden variable cannot be changed');
	if ($varvalue == '' && $var['emptyok'] != 'yes')
		throw new InvalidArgException ('varvalue', $varvalue, "'${varname}' must have a non-empty value");
	if ($varvalue != '' && $var['vartype'] == 'uint' && (! is_numeric ($varvalue) || $varvalue < 0 ))
		throw new InvalidArgException ('varvalue', $varvalue, "'${varname}' must be an unsigned integer");
	// Update cache only if the changes went into DB.
	usePreparedExecuteBlade
	(
		'REPLACE INTO UserConfig SET varvalue=?, varname=?, user=?',
		array ($varvalue, $varname, $remote_username)
	);
	$configCache[$varname]['varvalue'] = $varvalue;
	$configCache[$varname]['is_altered'] = 'yes';
}

function resetUserConfigVar ($varname)
{
	global $configCache;
	global $remote_username;
	if (!isset ($configCache))
		throw new RackTablesError ('configuration cache is unavailable', RackTablesError::INTERNAL);
	if (NULL === $var = array_fetch ($configCache, $varname, NULL))
		throw new InvalidArgException ('varname', $varname, 'unknown variable');
	if ($var['is_hidden'] != 'no')
		throw new InvalidArgException ('varname', $varname, 'a hidden variable cannot be changed');
	// Update cache only if the changes went into DB.
	if (! array_key_exists ('is_altered', $var) || $var['is_altered'] != 'yes')
		return;
	usePreparedDeleteBlade ('UserConfig', array ('varname' => $varname, 'user' => $remote_username));
	$configCache[$varname]['varvalue'] = $configCache[$varname]['defaultvalue'];
	unset ($configCache[$varname]['is_altered']);
}

// parses QUICK_LINK_PAGES config var and returns array with ('href'=>..., 'title'=>...) items
function getConfiguredQuickLinks()
{
	$ret = array();
	foreach (explode (',', getConfigVar('QUICK_LINK_PAGES')) as $page_code)
		if ($page_code != '')
			if ('' != $title = getPageName ($page_code))
				$ret[] = array ('href' => makeHref (array ('page' => $page_code)), 'title' => $title);
	return $ret;
}

function getCactiGraphsForObject ($object_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT server_id, graph_id, caption FROM CactiGraph WHERE object_id = ? ORDER BY server_id, graph_id',
		array ($object_id)
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC), 'graph_id');
}

function getMuninGraphsForObject ($object_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT server_id, graph, caption FROM MuninGraph WHERE object_id = ? ORDER BY server_id, graph',
		array ($object_id)
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC), 'graph');
}

function touchVLANSwitch ($switch_id)
{
	return usePreparedExecuteBlade
	(
		'UPDATE VLANSwitch SET mutex_rev=mutex_rev+1, last_change=NOW(), out_of_sync="yes" WHERE object_id=?',
		array ($switch_id)
	);
}

function detouchVLANSwitch ($switch_id, $mutex_rev)
{
	return usePreparedExecuteBlade
	(
		'UPDATE VLANSwitch SET last_change=NOW(), out_of_sync="no" WHERE object_id=? AND mutex_rev = ?',
		array ($switch_id, $mutex_rev)
	);
}

function setVLANSwitchError ($object_id, $errno)
{
	return usePreparedExecuteBlade
	(
		'UPDATE VLANSwitch SET last_errno=?, last_error_ts=NOW() WHERE object_id=?',
		array ($errno, $object_id)
	);
}

function setVLANSwitchTimestamp ($object_id, $field_name)
{
	return usePreparedExecuteBlade
	(
		"UPDATE VLANSwitch SET `$field_name`=NOW() WHERE object_id=?",
		array ($object_id)
	);
}

# Return list of rows for objects that have the date stored in the given
# attribute belonging to the given range (relative to today's date).
function scanAttrRelativeDays ($attr_id, $not_before_days, $not_after_days)
{
	if (getAttrType ($attr_id) != 'date')
		throw new InvalidArgException ('attr_id', $attr_id, 'attribute cannot store dates');
	$result = usePreparedSelectBlade
	(
		'SELECT uint_value, object_id FROM AttributeValue ' .
		'WHERE attr_id=? and FROM_UNIXTIME(uint_value) BETWEEN '.
		'DATE_ADD(curdate(), INTERVAL ? DAY) and DATE_ADD(curdate(), INTERVAL ? DAY)',
		array ($attr_id, $not_before_days, $not_after_days)
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function getCactiServers()
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, base_url, username, password, COUNT(graph_id) AS num_graphs ' .
		'FROM CactiServer AS CS LEFT JOIN CactiGraph AS CG ON CS.id = CG.server_id GROUP BY id'
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}

function getMuninServers()
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, base_url, COUNT(MG.object_id) AS num_graphs ' .
		'FROM MuninServer AS MS LEFT JOIN MuninGraph AS MG ON MS.id = MG.server_id GROUP BY id'
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}

function isTransactionActive()
{
	global $dbxlink;
	try
	{
		if ($dbxlink->beginTransaction())
		{
			$dbxlink->rollBack();
			return FALSE;
		}
		throw new RackTablesError ("beginTransaction returned false instead of throwing exception", RackTablesError::INTERNAL);
	}
	catch (PDOException $e)
	{
		return TRUE;
	}
}


function getRowsCount ($table)
{
	$result = usePreparedSelectBlade ("SELECT COUNT(*) FROM `$table`");
	return $result->fetchColumn();
}

function getEntitiesCount ($realm)
{
	global $SQLSchema;
	if (!isset ($SQLSchema[$realm]))
		throw new InvalidArgException ('realm', $realm);
	return getRowsCount ($SQLSchema[$realm]['table']);
}

function getPatchCableConnectorList()
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, origin, connector, ' .
		'(SELECT COUNT(*) FROM PatchCableConnectorCompat WHERE connector_id = id) AS refc ' .
		'FROM PatchCableConnector ORDER BY connector'
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function getPatchCableConnectorOptions()
{
	$ret = array();
	foreach (getPatchCableConnectorList() as $item)
		$ret[$item['id']] = $item['connector'] . ($item['origin'] == 'custom' ? ' (custom)' : '');
	return $ret;
}

function getPatchCableTypeList()
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, origin, pctype, ' .
		'(SELECT COUNT(*) FROM PatchCableConnectorCompat WHERE pctype_id = PatchCableType.id) + ' .
		'(SELECT COUNT(*) FROM PatchCableOIFCompat WHERE pctype_id = PatchCableType.id) AS refc ' .
		'FROM PatchCableType ORDER BY pctype'
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function getPatchCableTypeOptions()
{
	$ret = array();
	foreach (getPatchCableTypeList() as $item)
		$ret[$item['id']] = $item['pctype'] . ($item['origin'] == 'custom' ? ' (custom)' : '');
	return $ret;
}

function getPatchCableHeapSummary()
{
	$result = usePreparedSelectBlade
	(
		'SELECT PCH.id, end1_conn_id, PCC1.connector AS end1_connector, pctype_id, pctype, ' .
		'end2_conn_id, PCC2.connector AS end2_connector, amount, length, description, ' .
		'COUNT(PCHL.id) AS logc FROM PatchCableHeap AS PCH ' .
		'INNER JOIN PatchCableType AS PCT ON PCH.pctype_id = PCT.id ' .
		'INNER JOIN PatchCableConnector AS PCC1 ON end1_conn_id = PCC1.id ' .
		'INNER JOIN PatchCableConnector AS PCC2 ON end2_conn_id = PCC2.id ' .
		'LEFT JOIN PatchCableHeapLog AS PCHL ON PCH.id = PCHL.heap_id ' .
		'GROUP BY PCH.id ' .
		'ORDER BY pctype, end1_connector, end2_connector, description, id '
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}

function getPatchCableHeapOptionsForOIF ($oif_id)
{
	$result = usePreparedSelectBlade ('SELECT pctype_id FROM PatchCableOIFCompat WHERE oif_id = ?', array ($oif_id));
	$pctypes = reduceSubarraysToColumn ($result->fetchAll (PDO::FETCH_ASSOC), 'pctype_id');
	unset ($result);
	$ret = array();
	foreach (getPatchCableHeapSummary() as $item)
		if ($item['amount'] > 0 && in_array ($item['pctype_id'], $pctypes))
			$ret[$item['id']] = formatPatchCableHeapAsPlainText ($item);
	return $ret;
}

function getPatchCableConnectorCompat()
{
	$result = usePreparedSelectBlade
	(
		'SELECT pctype_id, pctype, connector_id, connector FROM PatchCableConnectorCompat ' .
		'INNER JOIN PatchCableType AS PCT ON pctype_id = PCT.id ' .
		'INNER JOIN PatchCableConnector AS PCC ON connector_id = PCC.id ' .
		'ORDER BY pctype, connector'
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function getPatchCableOIFCompat()
{
	$result = usePreparedSelectBlade
	(
		'SELECT pctype_id, pctype, oif_id, oif_name FROM PatchCableOIFCompat ' .
		'INNER JOIN PatchCableType AS PCT ON pctype_id = PCT.id ' .
		'INNER JOIN PortOuterInterface AS POI ON oif_id = POI.id ' .
		'ORDER BY pctype, oif_name'
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function commitModifyPatchCableAmount ($heap_id, $by_amount)
{
	global $dbxlink;
	$dbxlink->beginTransaction();
	usePreparedExecuteBlade
	(
		'UPDATE PatchCableHeap SET amount = amount + ? WHERE id = ? AND amount + ? >= 0',
		array ($by_amount, $heap_id, $by_amount)
	);
	addPatchCableHeapLogEntry ($heap_id, "amount adjusted by ${by_amount}");
	return $dbxlink->commit();
}

function commitSetPatchCableAmount ($heap_id, $new_amount)
{
	global $dbxlink;
	$dbxlink->beginTransaction();
	usePreparedUpdateBlade
	(
		'PatchCableHeap',
		array ('amount' => $new_amount),
		array ('id' => $heap_id)
	);
	addPatchCableHeapLogEntry ($heap_id, "amount set to ${new_amount}");
	return $dbxlink->commit();
}

function getPatchCableHeapLogEntries ($heap_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT date, user, message FROM PatchCableHeapLog WHERE heap_id = ? ORDER BY date DESC',
		array ($heap_id)
	);
	return $result->fetchAll (PDO::FETCH_ASSOC);
}

function addPatchCableHeapLogEntry ($heap_id, $message)
{
	global $disable_logging;
	if (isset ($disable_logging) && $disable_logging)
		return;
	global $remote_username;
	usePreparedExecuteBlade
	(
		"INSERT INTO PatchCableHeapLog (heap_id, date, user, message) VALUES (?, NOW(), ?, ?)",
		array ($heap_id, $remote_username, $message)
	);
}

function selectRackOrder ($row_id)
{
	$result = usePreparedSelectBlade ("SELECT id FROM Rack WHERE row_id = ? ORDER BY sort_order, name", array($row_id));
	return $result->fetchAll (PDO::FETCH_COLUMN, 0);
}

function getDBName()
{
	global $pdo_dsn;
	if (preg_match ('/\bdbname=(.+?)(;|$)/', $pdo_dsn, $m))
		return $m[1];
	throw new RackTablesError ('failed to spot "dbname" in $pdo_dsn', RackTablesError::INTERNAL);
}

// Sets exclusive server-global named lock.
// Always returns TRUE if no exceptions were thrown
// A lock is implicitly released on any subsequent call to setDBMutex in the same connection
function setDBMutex ($name, $timeout = 5)
{
	$fullname = getDBName() . '.' . $name;
	$result = usePreparedSelectBlade ('SELECT GET_LOCK(?, ?)', array ($fullname, $timeout));
	$row = $result->fetchColumn();
	if ($row === NULL)
		throw new RTDatabaseError ("error occured when executing GET_LOCK on $fullname");
	if ($row !== '1')
		throw new RTDatabaseError ("lock wait timeout for $fullname");
	return TRUE;
}

function tryDBMutex ($name, $timeout = 0)
{
	try
	{
		return setDBMutex ($name, $timeout);
	}
	catch (RTDatabaseError $e)
	{
		return FALSE;
	}
}

function releaseDBMutex ($name)
{
	$result = usePreparedSelectBlade ('SELECT RELEASE_LOCK(?)', array (getDBName() . '.' . $name));
	$row = $result->fetchColumn();
	return $row === '1';
}

?>
