<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

function renderSystemReports ()
{
	$tmp = array
	(
		array
		(
			'title' => 'Dictionary/objects',
			'type' => 'counters',
			'func' => 'getDictStats'
		),
		array
		(
			'title' => 'Rackspace',
			'type' => 'counters',
			'func' => 'getRackspaceStats'
		),
		array
		(
			'title' => 'Files',
			'type' => 'counters',
			'func' => 'getFileStats'
		),
		array
		(
			'title' => 'Tags top list',
			'type' => 'custom',
			'func' => 'renderTagStats'
		),
		array
		(
			'title' => 'RackCode stats',
			'type' => 'counters',
			'func' => 'getRackCodeStats',
		),
		array
		(
			'title' => 'RackCode warnings',
			'type' => 'messages',
			'func' => 'getRackCodeWarnings',
		),
		array
		(
			'title' => 'IPv4',
			'type' => 'counters',
			'func' => 'getIPv4Stats'
		),
		array
		(
			'title' => 'IPv6',
			'type' => 'counters',
			'func' => 'getIPv6Stats'
		),
	);
	foreach (getPortIIFOptions() as $iif_id => $iif_name)
		if (count (getPortIIFStats ($iif_id)))
			$tmp[] = array
			(
				'title' => "{$iif_name} ports",
				'type' => 'meters',
				'func' => 'getPortIIFStats',
				'args' => $iif_id,
			);
	renderReports ($tmp);
}

function renderLocalReports ()
{
	global $localreports;
	renderReports ($localreports);
}

function render8021QReport ()
{
	if (!count ($domains = getVLANDomainOptions()))
	{
		echo '<center><h3>(no VLAN configuration exists)</h3></center>';
		return;
	}
	$vlanstats = array();
	for ($i = VLAN_MIN_ID; $i <= VLAN_MAX_ID; $i++)
		$vlanstats[$i] = array();
	$header = '<tr><th>&nbsp;</th>';
	foreach ($domains as $domain_id => $domain_name)
	{
		foreach (getDomainVLANList ($domain_id) as $vlan_id => $vlan_info)
			$vlanstats[$vlan_id][$domain_id] = $vlan_info;
		$header .= '<th>' . mkA ($domain_name, 'vlandomain', $domain_id) . '</th>';
	}
	$header .= '</tr>';
	$output = $available = array();
	for ($i = VLAN_MIN_ID; $i <= VLAN_MAX_ID; $i++)
		if (!count ($vlanstats[$i]))
			$available[] = $i;
		else
			$output[$i] = FALSE;
	foreach (listToRanges ($available) as $span)
	{
		if ($span['to'] - $span['from'] < 4)
			for ($i = $span['from']; $i <= $span['to']; $i++)
				$output[$i] = FALSE;
		else
		{
			$output[$span['from']] = TRUE;
			$output[$span['to']] = FALSE;
		}
	}
	ksort ($output, SORT_NUMERIC);
	$header_delay = 0;
	startPortlet ('VLAN existence per domain');
	echo '<table border=1 cellspacing=0 cellpadding=5 align=center class=rackspace>';
	foreach ($output as $vlan_id => $tbc)
	{
		if (--$header_delay <= 0)
		{
			echo $header;
			$header_delay = 25;
		}
		echo '<tr class="state_' . (count ($vlanstats[$vlan_id]) ? 'T' : 'F');
		echo '"><th class=tdright>' . $vlan_id . '</th>';
		foreach (array_keys ($domains) as $domain_id)
		{
			echo '<td class=tdcenter>';
			if (! array_key_exists ($domain_id, $vlanstats[$vlan_id]))
				echo '&nbsp;';
			else
			{
				$attrs = $vlanstats[$vlan_id][$domain_id]['vlan_descr'] == '' ? NULL :
					array ('title' => $vlanstats[$vlan_id][$domain_id]['vlan_descr']);
				echo mkA ('&exist;', 'vlan', "${domain_id}-${vlan_id}", NULL, $attrs);
			}
			echo '</td>';
		}
		echo '</tr>';
		if ($tbc)
			echo '<tr class="state_A"><th>...</th><td colspan=' . count ($domains) . '>&nbsp;</td></tr>';
	}
	echo '</table>';
	finishPortlet();
}

function renderReports ($what)
{
	if (!count ($what))
		return;
	echo "<table align=center>\n";
	foreach ($what as $item)
	{
		echo "<tr><th colspan=2><h3>${item['title']}</h3></th></tr>\n";
		switch ($item['type'])
		{
			case 'counters':
				if (array_key_exists ('args', $item))
					$data = $item['func'] ($item['args']);
				else
					$data = $item['func'] ();
				foreach ($data as $header => $data)
					echo "<tr><td class=tdright>${header}:</td><td class=tdleft>${data}</td></tr>\n";
				break;
			case 'messages':
				if (array_key_exists ('args', $item))
					$data = $item['func'] ($item['args']);
				else
					$data = $item['func'] ();
				foreach ($data as $msg)
					echo "<tr class='msg_${msg['class']}'><td class=tdright>${msg['header']}:</td><td class=tdleft>${msg['text']}</td></tr>\n";
				break;
			case 'meters':
				if (array_key_exists ('args', $item))
					$data = $item['func'] ($item['args']);
				else
					$data = $item['func'] ();
				foreach ($data as $meter)
				{
					echo "<tr><td class=tdright>${meter['title']}:</td><td class=tdleft>";
					renderProgressBar ($meter['max'] ? $meter['current'] / $meter['max'] : 0);
					echo ' <small>' . ($meter['max'] ? $meter['current'] . '/' . $meter['max'] : '0') . '</small></td></tr>';
				}
				break;
			case 'custom':
				echo "<tr><td colspan=2>";
				$item['func'] ();
				echo "</td></tr>\n";
				break;
			default:
				throw new InvalidArgException ('type', $item['type']);
		}
		echo "<tr><td colspan=2><hr></td></tr>\n";
	}
	echo "</table>\n";
}

function renderTagStats ()
{
	echo '<table class="zebra widetable"><tr><th>tag</th><th>total</th><th>objects</th><th>IPv4 nets</th><th>IPv6 nets</th>';
	echo '<th>racks</th><th>IPv4 VS</th><th>IPv4 RS pools</th><th>users</th><th>files</th></tr>';
	$pagebyrealm = array
	(
		'file' => 'files&tab=default',
		'ipv4net' => 'ipv4space&tab=default',
		'ipv6net' => 'ipv6space&tab=default',
		'ipv4vs' => 'ipv4slb&tab=default',
		'ipv4rspool' => 'ipv4slb&tab=rspools',
		'object' => 'depot&tab=default',
		'rack' => 'rackspace&tab=default',
		'user' => 'userlist&tab=default'
	);
	foreach (getTagChart (getConfigVar ('TAGS_TOPLIST_SIZE')) as $taginfo)
	{
		echo "<tr><td>${taginfo['tag']}</td><td>" . $taginfo['refcnt']['total'] . "</td>";
		foreach (array ('object', 'ipv4net', 'ipv6net', 'rack', 'ipv4vs', 'ipv4rspool', 'user', 'file') as $realm)
		{
			echo '<td>';
			if (!isset ($taginfo['refcnt'][$realm]))
				echo '&nbsp;';
			else
			{
				echo "<a href='index.php?page=" . $pagebyrealm[$realm] . "&cft[]=${taginfo['id']}'>";
				echo $taginfo['refcnt'][$realm] . '</a>';
			}
			echo '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
}

function renderExpirations ()
{
	global $nextorder, $expirations;
	$attrmap = getAttrMap();
	foreach ($expirations as $attr_id => $sections)
	{
		startPortlet ($attrmap[$attr_id]['name']);
		foreach ($sections as $section)
		{
			$count = 1;
			$order = 'odd';
			$result = scanAttrRelativeDays ($attr_id, $section['from'], $section['to']);

			echo '<table align=center width=60% border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
			echo "<caption>${section['title']}</caption>\n";

			if (! count ($result))
			{
				echo "<tr><td colspan=4>(none)</td></tr></table><br>\n";
				continue;
			}
			echo '<tr valign=top><th align=center>Count</th><th align=center>Name</th>';
			echo "<th align=center>Asset Tag</th><th align=center>OEM S/N 1</th><th align=center>Date Warranty <br> Expires</th></tr>\n";
			foreach ($result as $row)
			{
				$date_value = datetimestrFromTimestamp ($row['uint_value']);

				$object = spotEntity ('object', $row['object_id']);
				$attributes = getAttrValues ($object['id']);
				$oem_sn_1 = array_key_exists (1, $attributes) ? $attributes[1]['a_value'] : '&nbsp;';
				echo '<tr class=' . $section['class'] . $order . ' valign=top>';
				echo "<td class=tdright>${count}</td>";
				echo '<td class=tdleft>' . mkCellA ($object) . '</td>';
				echo "<td class=tdleft>${object['asset_no']}</td>";
				echo "<td class=tdleft>${oem_sn_1}</td>";
				echo "<td>${date_value}</td>";
				echo "</tr>\n";
				$order = $nextorder[$order];
				$count++;
			}
			echo "</table><br>\n";
		}
		finishPortlet ();
	}
}

// The validity of some data cannot be guaranteed using foreign keys.
// Display any invalid rows that have crept in.
// Possible enhancements:
//    - check for IP addresses whose subnet does not exist in IPvXNetwork (X = 4 or 6)
//        - IPvXAddress, IPvXAllocation, IPvXLog, IPvXRS, IPvXVS
//    - provide links/buttons to delete invalid rows
//    - verify that the current DDL is correct for each DB element
//        - columns, indexes, character sets
function renderDataIntegrityReport ()
{
	$violations = FALSE;

	// check 1: EntityLink rows referencing not-existent relatives
	// check 1.1: children
	$realms = array
	(
		'location' => 'Location',
		'object' => 'RackObject',
		'rack' => 'Rack',
		'row' => 'Row'
	);
	$orphans = array ();
	foreach ($realms as $realm => $table)
	{
		$result = usePreparedSelectBlade
		(
			'SELECT EL.parent_entity_type, EL.parent_entity_id, ' .
			'EL.child_entity_type, EL.child_entity_id FROM EntityLink EL ' .
			"LEFT JOIN ${table} ON EL.child_entity_id = ${table}.id " .
			"WHERE EL.child_entity_type = ? AND ${table}.id IS NULL",
			array ($realm)
		);
		$rows = $result->fetchAll (PDO::FETCH_ASSOC);
		unset ($result);
		$orphans = array_merge ($orphans, $rows);
	}
	if (count ($orphans))
	{
		$violations = TRUE;
		startPortlet ('EntityLink: Missing Children (' . count ($orphans) . ')');
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>Parent</th><th>Child Type</th><th class=tdright>Child ID</th></tr>\n";
		foreach ($orphans as $orphan)
		{
			$realm_name = formatRealmName ($orphan['parent_entity_type']);
			try
			{
				$parent = spotEntity ($orphan['parent_entity_type'], $orphan['parent_entity_id']);
				$parent_name = $parent['name'];
			}
			catch (EntityNotFoundException $e)
			{
				$parent_name = 'missing from DB';
			}
			echo '<tr>';
			echo "<td>${realm_name}: ${parent_name}</td>";
			echo "<td>${orphan['child_entity_type']}</td>";
			echo "<td class=tdright>${orphan['child_entity_id']}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
		finishPortLet ();
	}

	// check 1.2: parents
	$orphans = array ();
	foreach ($realms as $realm => $table)
	{
		$result = usePreparedSelectBlade
		(
			'SELECT EL.parent_entity_type, EL.parent_entity_id, ' .
			'EL.child_entity_type, EL.child_entity_id FROM EntityLink EL ' .
			"LEFT JOIN ${table} ON EL.parent_entity_id = ${table}.id " .
			"WHERE EL.parent_entity_type = ? AND ${table}.id IS NULL",
			array ($realm)
		);
		$rows = $result->fetchAll (PDO::FETCH_ASSOC);
		unset ($result);
		$orphans = array_merge ($orphans, $rows);
	}
	if (count ($orphans))
	{
		$violations = TRUE;
		startPortlet ('EntityLink: Missing Parents (' . count ($orphans) . ')');
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>Child</th><th>Parent Type</th><th class=tdright>Parent ID</th></tr>\n";
		foreach ($orphans as $orphan)
		{
			$realm_name = formatRealmName ($orphan['child_entity_type']);
			try
			{
				$child = spotEntity ($orphan['child_entity_type'], $orphan['child_entity_id']);
				$child_name = $child['name'];
			}
			catch (EntityNotFoundException $e)
			{
				$child_name = 'missing from DB';
			}
			echo '<tr>';
			echo "<td>${realm_name}: ${child_name}</td>";
			echo "<td>${orphan['parent_entity_type']}</td>";
			echo "<td class=tdright>${orphan['parent_entity_id']}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
		finishPortLet ();
	}

	// check 3: multiple tables referencing non-existent dictionary entries
	// check 3.1: AttributeMap
	$orphans = array ();
	$result = usePreparedSelectBlade
	(
		'SELECT AM.objtype_id, A.name AS attr_name, C.name AS chapter_name ' .
		'FROM AttributeMap AM ' .
		'LEFT JOIN Attribute A ON AM.attr_id = A.id ' .
		'LEFT JOIN Chapter C ON AM.chapter_id = C.id ' .
		'LEFT JOIN Dictionary D ON AM.objtype_id = D.dict_key ' .
		'WHERE D.dict_key IS NULL'
	);
	$orphans = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	if (count ($orphans))
	{
		$violations = TRUE;
		startPortlet ('AttributeMap: Invalid Mappings (' . count ($orphans) . ')');
		$columns = array
		(
			array ('th_text' => 'Attribute', 'row_key' => 'attr_name'),
			array ('th_text' => 'Chapter', 'row_key' => 'chapter_name'),
			array ('th_text' => 'Object TypeID', 'row_key' => 'objtype_id', 'td_class' => 'tdright'),
		);
		renderTableViewer ($columns, $orphans);
		finishPortLet ();
	}

	// check 3.2: Object
	$orphans = array ();
	$result = usePreparedSelectBlade
	(
		'SELECT O.id, O.name, O.objtype_id FROM Object O ' .
		'LEFT JOIN Dictionary D ON O.objtype_id = D.dict_key ' .
		'WHERE D.dict_key IS NULL'
	);
	$orphans = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	if (count ($orphans))
	{
		$violations = TRUE;
		startPortlet ('Object: Invalid Types (' . count ($orphans) . ')');
		$columns = array
		(
			array ('th_text' => 'ID', 'row_key' => 'id', 'td_class' => 'tdright'),
			array ('th_text' => 'Name', 'row_key' => 'name'),
			array ('th_text' => 'Type ID', 'row_key' => 'objtype_id', 'td_class' => 'tdright'),
		);
		renderTableViewer ($columns, $orphans);
		finishPortLet ();
	}

	// check 3.3: ObjectHistory
	$orphans = array ();
	$result = usePreparedSelectBlade
	(
		'SELECT OH.id, OH.name, OH.objtype_id FROM ObjectHistory OH ' .
		'LEFT JOIN Dictionary D ON OH.objtype_id = D.dict_key ' .
		'WHERE D.dict_key IS NULL'
	);
	$orphans = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	if (count ($orphans))
	{
		$violations = TRUE;
		startPortlet ('ObjectHistory: Invalid Types (' . count ($orphans) . ')');
		$columns = array
		(
			array ('th_text' => 'ID', 'row_key' => 'id', 'td_class' => 'tdright'),
			array ('th_text' => 'Name', 'row_key' => 'name'),
			array ('th_text' => 'Type ID', 'row_key' => 'objtype_id', 'td_class' => 'tdright'),
		);
		renderTableViewer ($columns, $orphans);
		finishPortLet ();
	}

	// check 3.4: ObjectParentCompat
	$orphans = array ();
	$result = usePreparedSelectBlade
	(
		'SELECT OPC.parent_objtype_id, OPC.child_objtype_id, PD.dict_value AS parent_name, CD.dict_value AS child_name '.
		'FROM ObjectParentCompat OPC ' .
		'LEFT JOIN Dictionary PD ON OPC.parent_objtype_id = PD.dict_key ' .
		'LEFT JOIN Dictionary CD ON OPC.child_objtype_id = CD.dict_key ' .
		'WHERE PD.dict_key IS NULL OR CD.dict_key IS NULL'
	);
	$orphans = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	if (count ($orphans))
	{
		$violations = TRUE;
		startPortlet ('Object Container Compatibility rules: Invalid Parent or Child Type (' . count ($orphans) . ')');
		$columns = array
		(
			array ('th_text' => 'Parent', 'row_key' => 'parent_name'),
			array ('th_text' => 'Parent Type ID', 'row_key' => 'parent_objtype_id', 'td_class' => 'tdright'),
			array ('th_text' => 'Child', 'row_key' => 'child_name'),
			array ('th_text' => 'Child Type ID', 'row_key' => 'child_objtype_id', 'td_class' => 'tdright'),
		);
		renderTableViewer ($columns, $orphans);
		finishPortLet ();
	}

	// check 4: relationships that violate ObjectParentCompat Rules
	$invalids = array ();
	$result = usePreparedSelectBlade
	(
		'SELECT CO.id AS child_id, CO.objtype_id AS child_type_id, CD.dict_value AS child_type, CO.name AS child_name, ' .
		'PO.id AS parent_id, PO.objtype_id AS parent_type_id, PD.dict_value AS parent_type, PO.name AS parent_name ' .
		'FROM Object CO ' .
		'LEFT JOIN EntityLink EL ON CO.id = EL.child_entity_id ' .
		'LEFT JOIN Object PO ON EL.parent_entity_id = PO.id ' .
		'LEFT JOIN ObjectParentCompat OPC ON PO.objtype_id = OPC.parent_objtype_id AND CO.objtype_id = OPC.child_objtype_id ' .
		'LEFT JOIN Dictionary PD ON PO.objtype_id = PD.dict_key ' .
		'LEFT JOIN Dictionary CD ON CO.objtype_id = CD.dict_key ' .
		"WHERE EL.parent_entity_type = 'object' AND EL.child_entity_type = 'object' " .
		'AND OPC.parent_objtype_id IS NULL'
	);
	$invalids = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	if (count ($invalids))
	{
		$violations = TRUE;
		startPortlet ('Objects: Violate Object Container Compatibility rules (' . count ($invalids) . ')');
		$columns = array
		(
			array ('th_text' => 'Contained Obj Name', 'row_key' => 'child_name'),
			array ('th_text' => 'Contained Obj Type', 'row_key' => 'child_type'),
			array ('th_text' => 'Container Obj Name', 'row_key' => 'parent_name'),
			array ('th_text' => 'Container Obj Type', 'row_key' => 'parent_type'),
		);
		renderTableViewer ($columns, $invalids);
		finishPortLet ();
	}

	// check 5: Links that violate PortCompat Rules
	$invalids = array ();
	$result = usePreparedSelectBlade
	(
		'SELECT OA.id AS obja_id, OA.name AS obja_name, L.porta AS porta_id, PA.name AS porta_name, POIA.oif_name AS porta_type, ' .
		'OB.id AS objb_id, OB.name AS objb_name, L.portb AS portb_id, PB.name AS portb_name, POIB.oif_name AS portb_type ' .
		'FROM Link L ' .
		'LEFT JOIN Port PA ON L.porta = PA.id ' .
		'LEFT JOIN Object OA ON PA.object_id = OA.id ' .
		'LEFT JOIN PortOuterInterface POIA ON PA.type = POIA.id ' .
		'LEFT JOIN Port PB ON L.portb = PB.id ' .
		'LEFT JOIN Object OB ON PB.object_id = OB.id ' .
		'LEFT JOIN PortOuterInterface POIB ON PB.type = POIB.id ' .
		'LEFT JOIN PortCompat PC on PA.type = PC.type1 AND PB.type = PC.type2 ' .
		'WHERE PC.type1 IS NULL OR PC.type2 IS NULL'
	);
	$invalids = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	if (count ($invalids))
	{
		$violations = TRUE;
		startPortlet ('Port Links: Violate Port Compatibility Rules (' . count ($invalids) . ')');
		$columns = array
		(
			array ('th_text' => 'Object A', 'row_key' => 'obja_name'),
			array ('th_text' => 'Port A Name', 'row_key' => 'porta_name'),
			array ('th_text' => 'Port A Type', 'row_key' => 'porta_type'),
			array ('th_text' => 'Object B', 'row_key' => 'objb_name'),
			array ('th_text' => 'Port B Name', 'row_key' => 'portb_name'),
			array ('th_text' => 'Port B Type', 'row_key' => 'portb_type'),
		);
		renderTableViewer ($columns, $invalids);
		finishPortLet ();
	}

	// check 6: TagStorage rows referencing non-existent parents
	$realms = array
	(
		'file' => array ('table' => 'File', 'column' => 'id'),
		'ipv4net' => array ('table' => 'IPv4Network', 'column' => 'id'),
		'ipv4rspool' => array ('table' => 'IPv4RSPool', 'column' => 'id'),
		'ipv4vs' => array ('table' => 'IPv4VS', 'column' => 'id'),
		'ipv6net' => array ('table' => 'IPv6Network', 'column' => 'id'),
		'ipvs' => array ('table' => 'VS', 'column' => 'id'),
		'location' => array ('table' => 'Location', 'column' => 'id'),
		'object' => array ('table' => 'RackObject', 'column' => 'id'),
		'rack' => array ('table' => 'Rack', 'column' => 'id'),
		'user' => array ('table' => 'UserAccount', 'column' => 'user_id'),
		'vst' => array ('table' => 'VLANSwitchTemplate', 'column' => 'id'),
	);
	$orphans = array ();
	foreach ($realms as $realm => $details)
	{
		$result = usePreparedSelectBlade
		(
			'SELECT TS.entity_realm, TS.entity_id, TT.tag FROM TagStorage TS ' .
			'LEFT JOIN TagTree TT ON TS.tag_id = TT.id ' .
			"LEFT JOIN ${details['table']} ON TS.entity_id = ${details['table']}.${details['column']} " .
			"WHERE TS.entity_realm = ? AND ${details['table']}.${details['column']} IS NULL",
			array ($realm)
		);
		$rows = $result->fetchAll (PDO::FETCH_ASSOC);
		unset ($result);
		$orphans = array_merge ($orphans, $rows);
	}
	if (count ($orphans))
	{
		$violations = TRUE;
		startPortlet ('TagStorage: Missing Parents (' . count ($orphans) . ')');
		foreach (array_keys ($orphans) as $key)
			$orphans[$key]['realm_name'] = formatRealmName ($orphans[$key]['entity_realm']);
		$columns = array
		(
			array ('th_text' => 'Tag', 'row_key' => 'tag'),
			array ('th_text' => 'Parent Type', 'row_key' => 'realm_name'),
			array ('th_text' => 'Parent ID', 'row_key' => 'entity_id', 'td_class' => 'tdright'),
		);
		renderTableViewer ($columns, $orphans);
		finishPortLet ();
	}

	// check 7: FileLink rows referencing non-existent parents
	// re-use the realms list from the TagStorage check, with a few mods
	unset ($realms['file'], $realms['vst']);
	$realms['row'] = array ('table' => 'Row', 'column' => 'id');
	$orphans = array ();
	foreach ($realms as $realm => $details)
	{
		$result = usePreparedSelectBlade
		(
			'SELECT FL.entity_type, FL.entity_id, F.id FROM FileLink FL ' .
			'LEFT JOIN File F ON FL.file_id = F.id ' .
			"LEFT JOIN ${details['table']} ON FL.entity_id = ${details['table']}.${details['column']} " .
			"WHERE FL.entity_type = ? AND ${details['table']}.${details['column']} IS NULL",
			array ($realm)
		);
		$rows = $result->fetchAll (PDO::FETCH_ASSOC);
		unset ($result);
		$orphans = array_merge ($orphans, $rows);
	}
	if (count ($orphans))
	{
		$violations = TRUE;
		startPortlet ('FileLink: Missing Parents (' . count ($orphans) . ')');
		foreach (array_keys ($orphans) as $key)
		{
			$orphans[$key]['name'] = mkCellA (spotEntity ('file', $orphans[$key]['id']));
			$orphans[$key]['realm_name'] = formatRealmName ($orphans[$key]['entity_type']);
		}
		$columns = array
		(
			array ('th_text' => 'File', 'row_key' => 'name', 'td_escape' => FALSE),
			array ('th_text' => 'Parent Type', 'row_key' => 'realm_name'),
			array ('th_text' => 'Parent ID', 'row_key' => 'entity_id', 'td_class' => 'tdright'),
		);
		renderTableViewer ($columns, $orphans);
		finishPortLet ();
	}

	// check 8: triggers
	$known_triggers= array
	(
		array ('trigger_name' => 'Link-before-insert', 'table_name' => 'Link'),
		array ('trigger_name' => 'Link-before-update', 'table_name' => 'Link'),
		array ('trigger_name' => 'EntityLink-before-insert', 'table_name' => 'EntityLink'),
		array ('trigger_name' => 'EntityLink-before-update', 'table_name' => 'EntityLink'),
	);
	$known_triggers = reindexById ($known_triggers, 'trigger_name');

	$result = usePreparedSelectBlade
	(
		'SELECT TRIGGER_NAME AS trigger_name, EVENT_OBJECT_TABLE AS table_name ' .
		'FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = SCHEMA()'
	);
	$existing_triggers = reindexById ($result->fetchAll (PDO::FETCH_ASSOC), 'trigger_name');
	unset ($result);

	$missing_triggers = array_diff_key ($known_triggers, $existing_triggers);
	$unknown_triggers = array_diff_key ($existing_triggers, $known_triggers);
	$columns = array
	(
		array ('th_text' => 'Table', 'row_key' => 'table_name'),
		array ('th_text' => 'Trigger', 'row_key' => 'trigger_name'),
	);

	if (count ($missing_triggers))
	{
		$violations = TRUE;
		startPortlet ('Missing Triggers (' . count ($missing_triggers) . ')');
		renderTableViewer ($columns, $missing_triggers);
		finishPortLet ();
	}

	if (count ($unknown_triggers))
	{
		$violations = TRUE;
		startPortlet ('Unknown Triggers (' . count ($unknown_triggers) . ')');
		renderTableViewer ($columns, $unknown_triggers);
		finishPortLet ();
	}

	// check 9: foreign keys
	$known_fkeys = array
	(
		array ('fkey_name' => 'Atom-FK-molecule_id', 'table_name' => 'Atom'),
		array ('fkey_name' => 'Atom-FK-rack_id', 'table_name' => 'Atom'),
		array ('fkey_name' => 'AttributeMap-FK-chapter_id', 'table_name' => 'AttributeMap'),
		array ('fkey_name' => 'AttributeMap-FK-attr_id', 'table_name' => 'AttributeMap'),
		array ('fkey_name' => 'AttributeValue-FK-map', 'table_name' => 'AttributeValue'),
		array ('fkey_name' => 'AttributeValue-FK-object', 'table_name' => 'AttributeValue'),
		array ('fkey_name' => 'CachedPAV-FK-object-port', 'table_name' => 'CachedPAV'),
		array ('fkey_name' => 'CachedPAV-FK-vlan_id', 'table_name' => 'CachedPAV'),
		array ('fkey_name' => 'CachedPNV-FK-compound', 'table_name' => 'CachedPNV'),
		array ('fkey_name' => 'CachedPVM-FK-object_id', 'table_name' => 'CachedPVM'),
		array ('fkey_name' => 'Dictionary-FK-chapter_id', 'table_name' => 'Dictionary'),
		array ('fkey_name' => 'FileLink-File_fkey', 'table_name' => 'FileLink'),
		array ('fkey_name' => 'IPv4Allocation-FK-object_id', 'table_name' => 'IPv4Allocation'),
		array ('fkey_name' => 'IPv4LB-FK-vs_id', 'table_name' => 'IPv4LB'),
		array ('fkey_name' => 'IPv4LB-FK-object_id', 'table_name' => 'IPv4LB'),
		array ('fkey_name' => 'IPv4LB-FK-rspool_id', 'table_name' => 'IPv4LB'),
		array ('fkey_name' => 'IPv4NAT-FK-object_id', 'table_name' => 'IPv4NAT'),
		array ('fkey_name' => 'IPv4RS-FK', 'table_name' => 'IPv4RS'),
		array ('fkey_name' => 'IPv6Allocation-FK-object_id', 'table_name' => 'IPv6Allocation'),
		array ('fkey_name' => 'Link-FK-a', 'table_name' => 'Link'),
		array ('fkey_name' => 'Link-FK-b', 'table_name' => 'Link'),
		array ('fkey_name' => 'MountOperation-FK-object_id', 'table_name' => 'MountOperation'),
		array ('fkey_name' => 'MountOperation-FK-old_molecule_id', 'table_name' => 'MountOperation'),
		array ('fkey_name' => 'MountOperation-FK-new_molecule_id', 'table_name' => 'MountOperation'),
		array ('fkey_name' => 'ObjectHistory-FK-object_id', 'table_name' => 'ObjectHistory'),
		array ('fkey_name' => 'ObjectLog-FK-object_id', 'table_name' => 'ObjectLog'),
		array ('fkey_name' => 'PatchCableConnectorCompat-FK-connector_id', 'table_name' => 'PatchCableConnectorCompat'),
		array ('fkey_name' => 'PatchCableConnectorCompat-FK-pctype_id', 'table_name' => 'PatchCableConnectorCompat'),
		array ('fkey_name' => 'PatchCableHeap-FK-compat1', 'table_name' => 'PatchCableHeap'),
		array ('fkey_name' => 'PatchCableHeap-FK-compat2', 'table_name' => 'PatchCableHeap'),
		array ('fkey_name' => 'PatchCableHeapLog-FK-heap_id', 'table_name' => 'PatchCableHeapLog'),
		array ('fkey_name' => 'PatchCableOIFCompat-FK-oif_id', 'table_name' => 'PatchCableOIFCompat'),
		array ('fkey_name' => 'PatchCableOIFCompat-FK-pctype_id', 'table_name' => 'PatchCableOIFCompat'),
		array ('fkey_name' => 'Port-FK-iif-oif', 'table_name' => 'Port'),
		array ('fkey_name' => 'Port-FK-object_id', 'table_name' => 'Port'),
		array ('fkey_name' => 'PortAllowedVLAN-FK-object-port', 'table_name' => 'PortAllowedVLAN'),
		array ('fkey_name' => 'PortAllowedVLAN-FK-vlan_id', 'table_name' => 'PortAllowedVLAN'),
		array ('fkey_name' => 'PortCompat-FK-oif_id1', 'table_name' => 'PortCompat'),
		array ('fkey_name' => 'PortCompat-FK-oif_id2', 'table_name' => 'PortCompat'),
		array ('fkey_name' => 'PortInterfaceCompat-FK-iif_id', 'table_name' => 'PortInterfaceCompat'),
		array ('fkey_name' => 'PortInterfaceCompat-FK-oif_id', 'table_name' => 'PortInterfaceCompat'),
		array ('fkey_name' => 'PortLog_ibfk_1', 'table_name' => 'PortLog'),
		array ('fkey_name' => 'PortNativeVLAN-FK-compound', 'table_name' => 'PortNativeVLAN'),
		array ('fkey_name' => 'PortVLANMode-FK-object-port', 'table_name' => 'PortVLANMode'),
		array ('fkey_name' => 'RackSpace-FK-rack_id', 'table_name' => 'RackSpace'),
		array ('fkey_name' => 'RackSpace-FK-object_id', 'table_name' => 'RackSpace'),
		array ('fkey_name' => 'RackThumbnail-FK-rack_id', 'table_name' => 'RackThumbnail'),
		array ('fkey_name' => 'TagStorage-FK-TagTree', 'table_name' => 'TagStorage'),
		array ('fkey_name' => 'TagTree-K-parent_id', 'table_name' => 'TagTree'),
		array ('fkey_name' => 'UserConfig-FK-varname', 'table_name' => 'UserConfig'),
		array ('fkey_name' => 'VLANDescription-FK-domain_id', 'table_name' => 'VLANDescription'),
		array ('fkey_name' => 'VLANDescription-FK-vlan_id', 'table_name' => 'VLANDescription'),
		array ('fkey_name' => 'VLANDomain-FK-group_id', 'table_name' => 'VLANDomain'),
		array ('fkey_name' => 'VLANIPv4-FK-compound', 'table_name' => 'VLANIPv4'),
		array ('fkey_name' => 'VLANIPv4-FK-ipv4net_id', 'table_name' => 'VLANIPv4'),
		array ('fkey_name' => 'VLANIPv6-FK-compound', 'table_name' => 'VLANIPv6'),
		array ('fkey_name' => 'VLANIPv6-FK-ipv6net_id', 'table_name' => 'VLANIPv6'),
		array ('fkey_name' => 'VLANSTRule-FK-vst_id', 'table_name' => 'VLANSTRule'),
		array ('fkey_name' => 'VLANSwitch-FK-domain_id', 'table_name' => 'VLANSwitch'),
		array ('fkey_name' => 'VLANSwitch-FK-object_id', 'table_name' => 'VLANSwitch'),
		array ('fkey_name' => 'VLANSwitch-FK-template_id', 'table_name' => 'VLANSwitch'),
		array ('fkey_name' => 'VSEnabledIPs-FK-object_id', 'table_name' => 'VSEnabledIPs'),
		array ('fkey_name' => 'VSEnabledIPs-FK-rspool_id', 'table_name' => 'VSEnabledIPs'),
		array ('fkey_name' => 'VSEnabledIPs-FK-vs_id-vip', 'table_name' => 'VSEnabledIPs'),
		array ('fkey_name' => 'VSEnabledPorts-FK-object_id', 'table_name' => 'VSEnabledPorts'),
		array ('fkey_name' => 'VSEnabledPorts-FK-rspool_id', 'table_name' => 'VSEnabledPorts'),
		array ('fkey_name' => 'VSEnabledPorts-FK-vs_id-proto-vport', 'table_name' => 'VSEnabledPorts'),
		array ('fkey_name' => 'VSIPs-vs_id', 'table_name' => 'VSIPs'),
		array ('fkey_name' => 'VS-vs_id', 'table_name' => 'VSPorts'),
	);

	$plugins = getPlugins ('enabled');
	foreach (array_keys ($plugins) as $plugin)
	{
		global ${"plugin_${plugin}_fkeys"};
		if (isset (${"plugin_${plugin}_fkeys"}))
			$known_fkeys = array_merge ($known_fkeys, ${"plugin_${plugin}_fkeys"});
	}
	$known_fkeys = reindexById ($known_fkeys, 'fkey_name');
	ksort ($known_fkeys);

	$result = usePreparedSelectBlade
	(
		'SELECT CONSTRAINT_NAME as fkey_name, TABLE_NAME AS table_name ' .
		'FROM information_schema.TABLE_CONSTRAINTS ' .
		"WHERE CONSTRAINT_SCHEMA = SCHEMA() AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
	);
	$existing_fkeys = reindexById ($result->fetchAll (PDO::FETCH_ASSOC), 'fkey_name');
	unset ($result);
	$missing_fkeys = array_diff_key ($known_fkeys, $existing_fkeys);
	$unknown_fkeys = array_diff_key ($existing_fkeys, $known_fkeys);
	$columns = array
	(
		array ('th_text' => 'Table', 'row_key' => 'table_name'),
		array ('th_text' => 'Key', 'row_key' => 'fkey_name'),
	);

	if (count ($missing_fkeys))
	{
		$violations = TRUE;
		startPortlet ('Missing Foreign Keys (' . count ($missing_fkeys) . ')');
		renderTableViewer ($columns, $missing_fkeys);
		finishPortLet ();
	}

	if (count ($unknown_fkeys))
	{
		$violations = TRUE;
		startPortlet ('Unknown Foreign Keys (' . count ($unknown_fkeys) . ')');
		renderTableViewer ($columns, $unknown_fkeys);
		finishPortLet ();
	}

	// check 10: circular references
	//     - all affected members of the tree are displayed
	//     - it would be beneficial to only display the offending records
	// check 10.1: locations
	$invalids = array ();
	$locations = listCells ('location');
	foreach ($locations as $location)
	{
		try
		{
			$children = getLocationChildrenList ($location['id']);
		}
		catch (RackTablesError $e)
		{
			$invalids[] = $location;
		}
	}
	if (count ($invalids))
	{
		$violations = TRUE;
		startPortlet ('Locations: Tree Contains Circular References (' . count ($invalids) . ')');
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th class=tdright>Child ID</th><th>Child Location</th><th class=tdright>Parent ID</th><th>Parent Location</th></tr>\n";
		foreach ($invalids as $invalid)
		{
			echo '<tr>';
			echo "<td class=tdright>${invalid['id']}</td>";
			echo "<td>${invalid['name']}</td>";
			echo "<td class=tdright>${invalid['parent_id']}</td>";
			echo "<td>${invalid['parent_name']}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
		finishPortLet ();
	}

	// check 10.2: objects
	$invalids = array ();
	$objects = listCells ('object');
	foreach ($objects as $object)
	{
		try
		{
			$children = getObjectContentsList ($object['id']);
		}
		catch (RackTablesError $e)
		{
			$invalids[] = $object;
		}
	}
	if (count ($invalids))
	{
		$violations = TRUE;
		startPortlet ('Objects: Tree Contains Circular References (' . count ($invalids) . ')');
		foreach (array_keys ($invalids) as $key)
			$invalids[$key]['object'] = mkCellA ($invalids[$key]);
		$columns = array
		(
			array ('th_text' => 'Contained ID', 'row_key' => 'id', 'td_class' => 'tdright'),
			array ('th_text' => 'Contained Object', 'row_key' => 'object', 'td_escape' => FALSE),
			array ('th_text' => 'Container ID', 'row_key' => 'container_id', 'td_class' => 'tdright'),
			array ('th_text' => 'Container Object', 'row_key' => 'container_name'),
		);
		renderTableViewer ($columns, $invalids);
		finishPortLet ();
	}

	// check 10.3: tags
	global $taglist;
	$invalids = getInvalidNodes ($taglist);
	if (count ($invalids))
	{
		$violations = TRUE;
		startPortlet ('Tags: Tree Contains Circular References (' . count ($invalids) . ')');
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th class=tdright>Child ID</th><th>Child Tag</th><th class=tdright>Parent ID</th><th>Parent Tag</th></tr>\n";
		foreach ($invalids as $invalid)
		{
			echo '<tr>';
			echo "<td class=tdright>${invalid['id']}</td>";
			echo "<td>${invalid['tag']}</td>";
			echo "<td class=tdright>${invalid['parent_id']}</td>";
			printf('<td>%s</td>', $taglist[$invalid['parent_id']]['tag']);
			echo "</tr>\n";
		}
		echo "</table>\n";
		finishPortLet ();
	}

	// L2 addresses
	$columns = array
	(
		array ('th_text' => 'L2 address', 'row_key' => 'l2address', 'td_class' => 'l2address'),
		array ('th_text' => 'Object', 'row_key' => 'object', 'td_escape' => FALSE),
		array ('th_text' => 'Port', 'row_key' => 'name'),
	);

	// The section below is only required so long as Port.l2address is a char column,
	// switching to a binary type should eliminate the need for this check.
	$result = usePreparedSelectBlade
	(
		'SELECT l2address, object_id, name FROM Port ' .
		'WHERE l2address IS NOT NULL AND l2address NOT REGEXP("^[0-9A-F]+$")'
	);
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	if (count ($rows))
	{
		$violations = TRUE;
		foreach (array_keys ($rows) as $key)
			$rows[$key]['object'] = mkCellA (spotEntity ('object', $rows[$key]['object_id']));
		startPortlet ('L2 address invalid characters');
		renderTableViewer ($columns, $rows);
		finishPortlet();
	}

	// The section below will be relevant as long as the L2 address constraint remains
	// implemented at PHP level.
	$result = usePreparedSelectBlade
	(
		'SELECT l2address, object_id, name, ' .
		'(SELECT COUNT(*) FROM Port AS P2 WHERE P2.l2address = P1.l2address AND P2.object_id != P1.object_id) AS ocnt ' .
		'FROM Port AS P1 WHERE P1.l2address IS NOT NULL HAVING ocnt > 0  ORDER BY l2address, object_id'
	);
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	if (count ($rows))
	{
		$violations = TRUE;
		foreach (array_keys ($rows) as $key)
		{
			$rows[$key]['object'] = mkCellA (spotEntity ('object', $rows[$key]['object_id']));
			$rows[$key]['l2address'] = l2addressFromDatabase ($rows[$key]['l2address']);
		}
		startPortlet ('L2 address unique constraint errors');
		renderTableViewer ($columns, $rows);
		finishPortlet();
	}

	$result = usePreparedSelectBlade
	(
		'SELECT l2address, object_id, name ' .
		'FROM Port WHERE LENGTH(l2address) NOT IN(12, 16, 40)'
	);
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	if (count ($rows))
	{
		$violations = TRUE;
		// Do not try to call l2addressFromDatabase() as it will normally throw an exception.
		foreach (array_keys ($rows) as $key)
			$rows[$key]['object'] = mkCellA (spotEntity ('object', $rows[$key]['object_id']));
		startPortlet ('L2 address invalid length');
		renderTableViewer ($columns, $rows);
		finishPortlet();
	}

	if (! $violations)
		echo '<h2 class=centered>No integrity violations found</h2>';
}

function renderServerConfigurationReport ()
{
	echo '<br>';
	try
	{
		$test_innodb = isInnoDBSupported();
	}
	catch (PDOException $e)
	{
		showError ('InnoDB test failed (is binary logging enabled?).');
		$test_innodb = FALSE;
	}
	platform_is_ok ($test_innodb);
}
