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
	);
	renderReports ($tmp);
}

function renderLocalReports ()
{
	global $localreports;
	renderReports ($localreports);
}

function renderRackCodeReports ()
{
	$tmp = array
	(
		array
		(
			'title' => 'Stats',
			'type' => 'counters',
			'func' => 'getRackCodeStats',
		),
		array
		(
			'title' => 'Warnings',
			'type' => 'messages',
			'func' => 'getRackCodeWarnings',
		),
	);
	renderReports ($tmp);
}

function renderIPv4Reports ()
{
	$tmp = array
	(
		array
		(
			'title' => 'Stats',
			'type' => 'counters',
			'func' => 'getIPv4Stats'
		),
	);
	renderReports ($tmp);
}

function renderIPv6Reports ()
{
	$tmp = array
	(
		array
		(
			'title' => 'Stats',
			'type' => 'counters',
			'func' => 'getIPv6Stats'
		),
	);
	renderReports ($tmp);
}

function renderPortsReport ()
{
	$tmp = array();
	foreach (getPortIIFOptions() as $iif_id => $iif_name)
		if (count (getPortIIFStats ($iif_id)))
			$tmp[] = array
			(
				'title' => $iif_name,
				'type' => 'meters',
				'func' => 'getPortIIFStats',
				'args' => $iif_id,
			);
	renderReports ($tmp);
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
			if (array_key_exists ($domain_id, $vlanstats[$vlan_id]))
				echo mkA ('&exist;', 'vlan', "${domain_id}-${vlan_id}");
			else
				echo '&nbsp;';
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
					echo "<tr><td class=tdright>${meter['title']}:</td><td class=tdcenter>";
					renderProgressBar ($meter['max'] ? $meter['current'] / $meter['max'] : 0);
					echo '<br><small>' . ($meter['max'] ? $meter['current'] . '/' . $meter['max'] : '0') . '</small></td></tr>';
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
	global $taglist;
	echo '<table border=1><tr><th>tag</th><th>total</th><th>objects</th><th>IPv4 nets</th><th>IPv6 nets</th>';
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
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>Attribute</th><th>Chapter</th><th class=tdright>Object TypeID</th></tr>\n";
		foreach ($orphans as $orphan)
		{
			echo '<tr>';
			echo "<td>${orphan['attr_name']}</td>";
			echo "<td>${orphan['chapter_name']}</td>";
			echo "<td class=tdright>${orphan['objtype_id']}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
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
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th class=tdright>ID</th><th>Name</th><th class=tdright>Type ID</th></tr>\n";
		foreach ($orphans as $orphan)
		{
			echo '<tr>';
			echo "<td class=tdright>${orphan['id']}</td>";
			echo "<td>${orphan['name']}</td>";
			echo "<td class=tdright>${orphan['objtype_id']}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
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
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th class=tdright>ID</th><th>Name</th><th class=tdright>Type ID</th></tr>\n";
		foreach ($orphans as $orphan)
		{
			echo '<tr>';
			echo "<td class=tdright>${orphan['id']}</td>";
			echo "<td>${orphan['name']}</td>";
			echo "<td class=tdright>${orphan['objtype_id']}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
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
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>Parent</th><th class=tdright>Parent Type ID</th><th>Child</th><th class=tdright>Child Type ID</th></tr>\n";
		foreach ($orphans as $orphan)
		{
			echo '<tr>';
			echo "<td>${orphan['parent_name']}</td>";
			echo "<td class=tdright>${orphan['parent_objtype_id']}</td>";
			echo "<td>${orphan['child_name']}</td>";
			echo "<td class=tdright>${orphan['child_objtype_id']}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
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
		'LEFT JOIN ObjectParentCompat OPC ON PO.objtype_id = OPC.parent_objtype_id ' .
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
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>Contained Obj Name</th><th>Contained Obj Type</th><th>Container Obj Name</th><th>Container Obj Type</th></tr>\n";
		foreach ($invalids as $invalid)
		{
			echo '<tr>';
			echo "<td>${invalid['child_name']}</td>";
			echo "<td>${invalid['child_type']}</td>";
			echo "<td>${invalid['parent_name']}</td>";
			echo "<td>${invalid['parent_type']}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
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
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>Object A</th><th>Port A Name</th><th>Port A Type</th><th>Object B</th><th>Port B Name</th><th>Port B Type</th></tr>\n";
		foreach ($invalids as $invalid)
		{
			echo '<tr>';
			echo "<td>${invalid['obja_name']}</td>";
			echo "<td>${invalid['porta_name']}</td>";
			echo "<td>${invalid['porta_type']}</td>";
			echo "<td>${invalid['objb_name']}</td>";
			echo "<td>${invalid['portb_name']}</td>";
			echo "<td>${invalid['portb_type']}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
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
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>Tag</th><th>Parent Type</th><th class=tdright>Parent ID</th></tr>\n";
		foreach ($orphans as $orphan)
		{
			$realm_name = formatRealmName ($orphan['entity_realm']);
			echo '<tr>';
			echo "<td>${orphan['tag']}</td>";
			echo "<td>${realm_name}</td>";
			echo "<td class=tdright>${orphan['entity_id']}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
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
			'SELECT FL.entity_type, FL.entity_id, F.name FROM FileLink FL ' .
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
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>File</th><th>Parent Type</th><th class=tdright>Parent ID</th></tr>\n";
		foreach ($orphans as $orphan)
		{
			$realm_name = formatRealmName ($orphan['entity_type']);
			echo '<tr>';
			echo "<td>${orphan['name']}</td>";
			echo "<td>${realm_name}</td>";
			echo "<td class=tdright>${orphan['entity_id']}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
		finishPortLet ();
	}

	// check 8: missing triggers
	$triggers= array
	(
		'Link-before-insert' => 'Link',
		'Link-before-update' => 'Link'
	);
	$result = usePreparedSelectBlade
	(
		'SELECT TRIGGER_NAME, EVENT_OBJECT_TABLE ' .
		'FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = SCHEMA()'
	);
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	$existing_triggers = $missing_triggers = array ();
	foreach ($rows as $row)
		$existing_triggers[$row['TRIGGER_NAME']] = $row['EVENT_OBJECT_TABLE'];
	foreach ($triggers as $trigger => $table)
		if (! array_key_exists ($trigger, $existing_triggers))
			$missing_triggers[$trigger] = $table;
	if (count ($missing_triggers))
	{
		$violations = TRUE;
		startPortlet ('Missing Triggers (' . count ($missing_triggers) . ')');
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>Table</th><th>Trigger</th></tr>\n";
		foreach ($missing_triggers as $trigger => $table)
		{
			echo '<tr>';
			echo "<td>${table}</td>";
			echo "<td>${trigger}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
		finishPortLet ();
	}

	// check 9: missing foreign keys
	$fkeys= array
	(
		'Atom-FK-molecule_id' => 'Atom',
		'Atom-FK-rack_id' => 'Atom',
		'AttributeMap-FK-chapter_id' => 'AttributeMap',
		'AttributeMap-FK-attr_id' => 'AttributeMap',
		'AttributeValue-FK-map' => 'AttributeValue',
		'AttributeValue-FK-object' => 'AttributeValue',
		'CachedPAV-FK-object-port' => 'CachedPAV',
		'CachedPAV-FK-vlan_id' => 'CachedPAV',
		'CachedPNV-FK-compound' => 'CachedPNV',
		'CachedPVM-FK-object_id' => 'CachedPVM',
		'CactiGraph-FK-server_id' => 'CactiGraph',
		'CactiGraph-FK-server_id' => 'CactiGraph',
		'Dictionary-FK-chapter_id' => 'Dictionary',
		'FileLink-File_fkey' => 'FileLink',
		'IPv4Allocation-FK-object_id' => 'IPv4Allocation',
		'IPv4LB-FK-vs_id' => 'IPv4LB',
		'IPv4LB-FK-object_id' => 'IPv4LB',
		'IPv4LB-FK-rspool_id' => 'IPv4LB',
		'IPv4NAT-FK-object_id' => 'IPv4NAT',
		'IPv4RS-FK' => 'IPv4RS',
		'IPv6Allocation-FK-object_id' => 'IPv6Allocation',
		'Link-FK-a' => 'Link',
		'Link-FK-b' => 'Link',
		'MountOperation-FK-object_id' => 'MountOperation',
		'MountOperation-FK-old_molecule_id' => 'MountOperation',
		'MountOperation-FK-new_molecule_id' => 'MountOperation',
		'MuninGraph-FK-server_id' => 'MuninGraph',
		'MuninGraph-FK-server_id' => 'MuninGraph',
		'ObjectHistory-FK-object_id' => 'ObjectHistory',
		'ObjectLog-FK-object_id' => 'ObjectLog',
		'PatchCableConnectorCompat-FK-connector_id' => 'PatchCableConnectorCompat',
		'PatchCableConnectorCompat-FK-pctype_id' => 'PatchCableConnectorCompat',
		'PatchCableHeap-FK-compat1' => 'PatchCableHeap',
		'PatchCableHeap-FK-compat2' => 'PatchCableHeap',
		'PatchCableHeapLog-FK-heap_id' => 'PatchCableHeapLog',
		'PatchCableOIFCompat-FK-oif_id' => 'PatchCableOIFCompat',
		'PatchCableOIFCompat-FK-pctype_id' => 'PatchCableOIFCompat',
		'Port-FK-iif-oif' => 'Port',
		'Port-FK-object_id' => 'Port',
		'PortAllowedVLAN-FK-object-port' => 'PortAllowedVLAN',
		'PortAllowedVLAN-FK-vlan_id' => 'PortAllowedVLAN',
		'PortCompat-FK-oif_id1' => 'PortCompat',
		'PortCompat-FK-oif_id2' => 'PortCompat',
		'PortInterfaceCompat-FK-iif_id' => 'PortInterfaceCompat',
		'PortInterfaceCompat-FK-oif_id' => 'PortInterfaceCompat',
		'PortLog_ibfk_1' => 'PortLog',
		'PortNativeVLAN-FK-compound' => 'PortNativeVLAN',
		'PortVLANMode-FK-object-port' => 'PortVLANMode',
		'RackSpace-FK-rack_id' => 'RackSpace',
		'RackSpace-FK-object_id' => 'RackSpace',
		'TagStorage-FK-TagTree' => 'TagStorage',
		'TagTree-K-parent_id' => 'TagTree',
		'UserConfig-FK-varname' => 'UserConfig',
		'VLANDescription-FK-domain_id' => 'VLANDescription',
		'VLANDescription-FK-vlan_id' => 'VLANDescription',
		'VLANIPv4-FK-compound' => 'VLANIPv4',
		'VLANIPv4-FK-ipv4net_id' => 'VLANIPv4',
		'VLANIPv6-FK-compound' => 'VLANIPv6',
		'VLANIPv6-FK-ipv6net_id' => 'VLANIPv6',
		'VLANSTRule-FK-vst_id' => 'VLANSTRule',
		'VLANSwitch-FK-domain_id' => 'VLANSwitch',
		'VLANSwitch-FK-object_id' => 'VLANSwitch',
		'VLANSwitch-FK-template_id' => 'VLANSwitch',
		'VSEnabledIPs-FK-object_id' => 'VSEnabledIPs',
		'VSEnabledIPs-FK-rspool_id' => 'VSEnabledIPs',
		'VSEnabledIPs-FK-vs_id-vip' => 'VSEnabledIPs',
		'VSEnabledPorts-FK-object_id' => 'VSEnabledPorts',
		'VSEnabledPorts-FK-rspool_id' => 'VSEnabledPorts',
		'VSEnabledPorts-FK-vs_id-proto-vport' => 'VSEnabledPorts',
		'VSIPs-vs_id' => 'VSIPs',
		'VS-vs_id' => 'VSPorts'
	);
	$result = usePreparedSelectBlade
	(
		'SELECT CONSTRAINT_NAME, TABLE_NAME ' .
		'FROM information_schema.TABLE_CONSTRAINTS ' .
		"WHERE CONSTRAINT_SCHEMA = SCHEMA() AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
	);
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	$existing_fkeys = $missing_fkeys = array ();
	foreach ($rows as $row)
		$existing_fkeys[$row['CONSTRAINT_NAME']] = $row['TABLE_NAME'];
	foreach ($fkeys as $fkey => $table)
		if (! array_key_exists ($fkey, $existing_fkeys))
			$missing_fkeys[$fkey] = $table;
	if (count ($missing_fkeys))
	{
		$violations = TRUE;
		startPortlet ('Missing Foreign Keys (' . count ($missing_fkeys) . ')');
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>Table</th><th>Key</th></tr>\n";
		foreach ($missing_fkeys as $fkey => $table)
		{
			echo '<tr>';
			echo "<td>${table}</td>";
			echo "<td>${fkey}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
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
		echo "<table cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th class=tdright>Contained ID</th><th>Contained Object</th><th class=tdright>Container ID</th><th>Container Object</th></tr>\n";
		foreach ($invalids as $invalid)
		{
			echo '<tr>';
			echo "<td class=tdright>${invalid['id']}</td>";
			echo "<td>${invalid['name']}</td>";
			echo "<td class=tdright>${invalid['container_id']}</td>";
			echo "<td>${invalid['container_name']}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
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
		$columns = array
		(
			array ('th_text' => 'L2 address', 'row_key' => 'l2address'),
			array ('th_text' => 'Object', 'row_key' => 'object', 'td_escape' => FALSE),
			array ('th_text' => 'Port', 'row_key' => 'name'),
		);
		startPortlet ('L2 address format errors');
		renderTableViewer ($columns, $rows);
		finishPortlet();
	}
	unset ($result);
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
			$rows[$key]['object'] = mkCellA (spotEntity ('object', $rows[$key]['object_id']));
		$columns = array
		(
			array ('th_text' => 'L2 address', 'row_key' => 'l2address'),
			array ('th_text' => 'Object', 'row_key' => 'object', 'td_escape' => FALSE),
			array ('th_text' => 'Port', 'row_key' => 'name'),
		);
		startPortlet ('L2 address unique constraint errors');
		renderTableViewer ($columns, $rows);
		finishPortlet();
	}

	if (! $violations)
		echo '<h2 class=centered>No integrity violations found</h2>';
}

?>
