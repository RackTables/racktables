<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

function findSparePorts ($port_info, $filter)
{
	$qparams = array ();
	$query = "
SELECT
	p.id,
	p.name,
	p.reservation_comment,
	p.iif_id,
	p.type as oif_id,
	pii.iif_name,
	poi.oif_name,
	p.object_id,
	o.objtype_id as object_tid,
	o.name as object_name
FROM Port p
INNER JOIN Object o ON o.id = p.object_id
INNER JOIN PortInnerInterface pii ON p.iif_id = pii.id
INNER JOIN PortOuterInterface poi ON poi.id = p.type
";
	// porttype filter (non-strict match)
	$query .= "
INNER JOIN (
	SELECT Port.id FROM Port
	INNER JOIN
	(
		SELECT DISTINCT	pic2.iif_id
		FROM PortInterfaceCompat pic2
		INNER JOIN PortCompat pc ON pc.type2 = pic2.oif_id
";
		if ($port_info['iif_id'] != 1)
		{
			$query .= " INNER JOIN PortInterfaceCompat pic ON pic.oif_id = pc.type1 WHERE pic.iif_id = ? AND ";
			$qparams[] = $port_info['iif_id'];
		}
		else
		{
			$query .= " WHERE pc.type1 = ? AND ";
			$qparams[] = $port_info['oif_id'];
		}
		$query .= "
			pic2.iif_id <> 1
	) AS sub1 USING (iif_id)
	UNION
	SELECT Port.id
	FROM Port
	INNER JOIN PortCompat ON type1 = type
	WHERE
		iif_id = 1 and type2 = ?
) AS sub2 ON sub2.id = p.id
";
	$qparams[] = $port_info['oif_id'];

	// self and linked ports filter
	$query .= " WHERE p.id <> ? " .
		"AND p.id NOT IN (SELECT porta FROM Link) " .
		"AND p.id NOT IN (SELECT portb FROM Link) ";
	$qparams[] = $port_info['id'];
	// rack filter
	if (! empty ($filter['racks']))
	{
		// objects directly mounted in the racks
		$query .= sprintf
		(
			'AND p.object_id IN (SELECT DISTINCT object_id FROM RackSpace WHERE rack_id IN (%s) ',
			questionMarks (count ($filter['racks']))
		);
		// children of objects directly mounted in the racks
		$query .= sprintf
		(
			"UNION SELECT child_entity_id FROM EntityLink WHERE parent_entity_type='object' AND child_entity_type = 'object' AND parent_entity_id IN (SELECT DISTINCT object_id FROM RackSpace WHERE rack_id IN (%s)) ",
			questionMarks (count ($filter['racks']))
		);
		// zero-U objects mounted to the racks
		$query .= sprintf
		(
			"UNION SELECT child_entity_id FROM EntityLink WHERE parent_entity_type='rack' AND child_entity_type='object' AND parent_entity_id IN (%s)) ",
			questionMarks (count ($filter['racks']))
		);
		$qparams = array_merge ($qparams, $filter['racks']);
		$qparams = array_merge ($qparams, $filter['racks']);
		$qparams = array_merge ($qparams, $filter['racks']);
	}
	// objectname filter
	if (! empty ($filter['objects']))
	{
		$query .= 'AND o.name like ? ';
		$qparams[] = '%' . $filter['objects'] . '%';
	}
	// asset_no filter
	if (! empty ($filter['asset_no']))
	{
		$query .= 'AND o.asset_no like ? ';
		$qparams[] = '%' . $filter['asset_no'] . '%';
	}
	// portname filter
	if (! empty ($filter['ports']))
	{
		$query .= 'AND p.name LIKE ? ';
		$qparams[] = '%' . $filter['ports'] . '%';
	}
	// ordering
	$query .= ' ORDER BY o.name';

	$ret = array();
	$result = usePreparedSelectBlade ($query, $qparams);
	
	$rows_by_pn = array();
	$prev_object_id = NULL;
	
	// fetch port rows from the DB
	while (TRUE)
	{
		$row = $result->fetch (PDO::FETCH_ASSOC);
		if (isset ($prev_object_id) && (! $row || $row['object_id'] != $prev_object_id))
		{
			// handle sorted object's portlist
			foreach (sortPortList ($rows_by_pn) as $ports_subarray)
				foreach ($ports_subarray as $port_row)
				{
					$port_description = formatObjectDisplayedName ($port_row['object_name'], $port_row['object_tid']) .
						' --  ' . $port_row['name'];
					if (count ($ports_subarray) > 1)
					{
						$if_type = $port_row['iif_id'] == 1 ? $port_row['oif_name'] : $port_row['iif_name'];
						$port_description .= " ($if_type)";
					}
					if (! empty ($port_row['reservation_comment']))
						$port_description .= '  --  ' . $port_row['reservation_comment'];
					$ret[$port_row['id']] = $port_description;
				}
			$rows_by_pn = array();
		}
		$prev_object_id = $row['object_id'];
		if ($row)
			$rows_by_pn[$row['name']][] = $row;
		else
			break;
	}

	return $ret;
}

// Return a list of all objects that are possible parents
//    Special case for VMs and VM Virtual Switches
//        - only select Servers with the Hypervisor attribute set to Yes
function findObjectParentCandidates ($object_id)
{
	$object = spotEntity ('object', $object_id);
	$args = array ($object['objtype_id'], $object_id, $object_id);

	$query = "SELECT O.id, O.name, O.objtype_id FROM Object O ";
	$query .= "LEFT JOIN ObjectParentCompat OPC ON O.objtype_id = OPC.parent_objtype_id ";
	$query .= "WHERE OPC.child_objtype_id = ? ";
	$query .= "AND O.id != ? ";
	// exclude existing parents
	$query .= "AND O.id NOT IN (SELECT parent_entity_id FROM EntityLink WHERE parent_entity_type = 'object' AND child_entity_type = 'object' AND child_entity_id = ?) ";
	if ($object['objtype_id'] == 1504 || $object['objtype_id'] == 1507)
	{
		array_push($args, $object['objtype_id'], $object_id, $object_id);
		$query .= "AND OPC.parent_objtype_id != 4 ";
		$query .= "UNION ";
		$query .= "SELECT O.id, O.name, O.objtype_id FROM Object O  ";
		$query .= "LEFT JOIN ObjectParentCompat OPC ON O.objtype_id = OPC.parent_objtype_id ";
		$query .= "LEFT JOIN AttributeValue AV ON O.id = AV.object_id ";
		$query .= "WHERE OPC.child_objtype_id = ? ";
		$query .= "AND (O.objtype_id = 4 AND AV.attr_id = 26 AND AV.uint_value = 1501) ";
		$query .= "AND O.id != ? ";
		// exclude existing parents
		$query .= "AND O.id NOT IN (SELECT parent_entity_id FROM EntityLink WHERE parent_entity_type = 'object' AND child_entity_type = 'object' AND child_entity_id = ?) ";
	}
	$query .= "ORDER BY 2";

	$result = usePreparedSelectBlade ($query, $args);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = empty ($row['name']) ? sprintf("[%s] - object %d", decodeObjectType ($row['objtype_id']), $row['id']) : $row['name'];
	return $ret;
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
			$objname_cmp = sortTokenize ($a['ip'], $b['ip']);
	}
	return $objname_cmp;
}

function renderPopupObjectSelector()
{
	assertPermission('object', 'default');
	$object_id = getBypassValue();
	echo '<h2>Choose a container:</h2>';
	echo '<form action="javascript:;">';
	$parents = findObjectParentCandidates($object_id);
	printSelect ($parents, array ('name' => 'parents', 'size' => getConfigVar ('MAXSELSIZE')));
	echo '<br>';
	echo "<input type=submit value='Proceed' onclick='".
		"if (getElementById(\"parents\").value != \"\") {".
		"	opener.location=\"?module=redirect&page=object&tab=edit&op=linkObjects&object_id=${object_id}&child_entity_type=object&child_entity_id=${object_id}&parent_entity_type=object&parent_entity_id=\"+getElementById(\"parents\").value; ".
		"	window.close();}'>";
	echo '</form>';
}

function handlePopupPortLink()
{
	assertPermission('depot', 'default');
	genericAssertion ('port', 'natural');
	genericAssertion ('remote_port', 'natural');
	assertStringArg ('cable', TRUE);
	$port_info = getPortInfo ($_REQUEST['port']);
	$remote_port_info = getPortInfo ($_REQUEST['remote_port']);
	$POIFC = getPortOIFCompat();
	if (isset ($_REQUEST['port_type']) && isset ($_REQUEST['remote_port_type']))
	{
		$type_local = $_REQUEST['port_type'];
		$type_remote = $_REQUEST['remote_port_type'];
	}
	else
	{
		$type_local = $port_info['oif_id'];
		$type_remote = $remote_port_info['oif_id'];
	}
	$matches = FALSE;
	$js_table = '';
	foreach ($POIFC as $pair)
		if ($pair['type1'] == $type_local && $pair['type2'] == $type_remote)
		{
			$matches = TRUE;
			break;
		}
		else
			$js_table .= "POIFC['${pair['type1']}-${pair['type2']}'] = 1;\n";

	if ($matches)
	{
		if ($port_info['oif_id'] != $type_local)
			commitUpdatePortOIF ($port_info['id'], $type_local);
		if ($remote_port_info['oif_id'] != $type_remote)
			commitUpdatePortOIF ($remote_port_info['id'], $type_remote);
		linkPorts ($port_info['id'], $remote_port_info['id'], $_REQUEST['cable']);
		// patch cable?
		if (array_key_exists ('heap_id', $_REQUEST))
		{
			// Leave the compatibility constraints check up to the foreign keys.
			if (0 != $heap_id = genericAssertion ('heap_id', 'uint0'))
			{
				$heaps = getPatchCableHeapSummary();
				if (commitModifyPatchCableAmount ($heap_id, -1))
					showSuccess ('consumed a patch cable from ' . formatPatchCableHeapAsPlainText ($heaps[$heap_id]));
				else
					showError ('failed to consume a patch cable');
			}
		}
		showOneLiner
		(
			8,
			array
			(
				formatPortLink ($port_info['object_id'], NULL, $port_info['id'], $port_info['name']),
				formatPort ($remote_port_info),
			)
		);
		addJSText (<<<'END'
window.opener.location.reload(true);
window.close();
END
		);
		backupLogMessages();
	}
	else
	{
		// JS code to display port compatibility hint
		// Heredoc, not nowdoc!
		addJSText (<<<"END"
POIFC = {};
$js_table
$(document).ready(function () {
	$('select.porttype').change(onPortTypeChange);
	onPortTypeChange();
});
function onPortTypeChange() {
	var key = $('*[name=port_type]')[0].value + '-' + $('*[name=remote_port_type]')[0].value;
	if (POIFC[key] == 1)
	{
		$('#hint-not-compat').hide();
		$('#hint-compat').show();
	}
	else
	{
		$('#hint-compat').hide();
		$('#hint-not-compat').show();
	}
}
END
		);
		addCSSText (<<<'END'
.compat-hint {
	display: none;
	font-size: 125%;
}
.compat-hint#hint-compat {
	color: green;
}
.compat-hint#hint-not-compat {
	color: #804040;
}
END
		);
		// render port type editor form
		echo '<form method=GET>';
		echo '<input type=hidden name="module" value="popup">';
		echo '<input type=hidden name="helper" value="portlist">';
		echo '<input type=hidden name="port" value="' . $port_info['id'] . '">';
		echo '<input type=hidden name="remote_port" value="' . $remote_port_info['id'] . '">';
		echo '<input type=hidden name="cable" value="' . htmlspecialchars ($_REQUEST['cable'], ENT_QUOTES) . '">';
		echo '<p>The ports you have selected are not compatible. Please select a compatible transceiver pair.';
		echo '<p>';
		echo formatPort ($port_info) . ' ';
		if ($port_info['iif_id'] == 1)
		{
			echo formatPortIIFOIF ($port_info);
			echo '<input type=hidden name="port_type" value="' . $port_info['oif_id'] . '">';
		}
		else
		{
			echo '<label>' . $port_info['iif_name'] . ' ';
			printSelect (getExistingPortTypeOptions ($port_info), array ('class' => 'porttype', 'name' => 'port_type'), $type_local);
			echo '</label>';
		}
		echo ' &mdash; ';
		if ($remote_port_info['iif_id'] == 1)
		{
			echo formatPortIIFOIF ($remote_port_info);
			echo '<input type=hidden name="remote_port_type" value="' . $remote_port_info['oif_id'] . '">';
		}
		else
		{
			echo '<label>' . $remote_port_info['iif_name'] . ' ';
			printSelect (getExistingPortTypeOptions ($remote_port_info), array ('class' => 'porttype', 'name' => 'remote_port_type'), $type_remote);
			echo '</label>';
		}
		echo ' ' . formatPort ($remote_port_info);
		echo '<p class="compat-hint" id="hint-not-compat">&#10005; Not compatible port types</p>';
		echo '<p class="compat-hint" id="hint-compat">&#10004; Compatible port types</p>';
		echo '<p><input type=submit name="do_link" value="Link">';
	}
}

function renderPopupPortSelector()
{
	if (isset ($_REQUEST['do_link']))
		return handlePopupPortLink();
	assertPermission('depot', 'default');
	genericAssertion ('port', 'natural');
	$port_id = $_REQUEST['port'];
	$port_info = getPortInfo ($port_id);
	$in_rack = isCheckSet ('in_rack');

	// fill port filter structure
	$filter = array
	(
		'racks' => array(),
		'objects' => '',
		'ports' => '',
		'asset_no' => '',
	);
	if (isset ($_REQUEST['filter-obj']))
		$filter['objects'] = trim($_REQUEST['filter-obj']);
	if (isset ($_REQUEST['filter-port']))
		$filter['ports'] = trim($_REQUEST['filter-port']);
	if (isset ($_REQUEST['filter-asset_no']))
		$filter['asset_no'] = trim($_REQUEST['filter-asset_no']);
	if ($in_rack)
	{
		$object = spotEntity ('object', $port_info['object_id']);
		if ($object['rack_id']) // the object itself is mounted in a rack
			$filter['racks'] = getProximateRacks ($object['rack_id'], getConfigVar ('PROXIMITY_RANGE'));
		elseif ($object['container_id']) // the object is not mounted in a rack, but it's container may be
		{
			$container = spotEntity ('object', $object['container_id']);
			if ($container['rack_id'])
				$filter['racks'] = getProximateRacks ($container['rack_id'], getConfigVar ('PROXIMITY_RANGE'));
		}
	}
	$spare_ports = array();
	if
	(
		! empty ($filter['racks'])  ||
		! empty ($filter['objects']) ||
		! empty ($filter['ports']) ||
		! empty ($filter['asset_no'])
	)
		$spare_ports = findSparePorts ($port_info, $filter);

	includeJQueryUI (TRUE);

	// display search form
	echo 'Link ' . formatPort ($port_info) . ' to...';
	echo '<form method=GET>';
	startPortlet ('Port list filter');
	echo '<input type=hidden name="module" value="popup">';
	echo '<input type=hidden name="helper" value="portlist">';
	echo '<input type=hidden name="port" value="' . $port_id . '">';
	echo '<table align="center" valign="bottom"><tr>';
	echo '<td class="tdleft"><label>Object name:<br><input id="filter-obj" type=text size=8 name="filter-obj" value="' . htmlspecialchars ($filter['objects'], ENT_QUOTES) . '"></label></td>';
	echo '<td class="tdleft"><label>Asset tag:<br><input id="filter-asset" type=text size=8 name="filter-asset_no" value="' . htmlspecialchars ($filter['asset_no'], ENT_QUOTES) . '"></label></td>';
	echo '<td class="tdleft"><label>Port name:<br><input id="filter-port" type=text size=6 name="filter-port" value="' . htmlspecialchars ($filter['ports'], ENT_QUOTES) . '"></label></td>';
	echo '<td class="tdleft" valign="bottom"><label><input type=checkbox name="in_rack"' . ($in_rack ? ' checked' : '') . '>Nearest racks</label></td>';
	echo '<td valign="bottom"><input type=submit value="show ports"></td>';
	echo '</tr></table>';
	finishPortlet();

	addJSText (<<<'JSEND'
		$(document).ready( function() {
			$("#filter-obj").autocomplete({
				source: "?module=ajax&ac=autocomplete&realm=object",
				minLength: 3,
				focus: function(event, ui) {
						if( ui.item.value == '' )
							event.preventDefault();
				},
				select: function(event, ui) {
						if( ui.item.value == '' )
							event.preventDefault();
				}
			});
			$("#filter-asset").autocomplete({
				source: "?module=ajax&ac=autocomplete&realm=asset",
				minLength: 3,
				focus: function(event, ui) {
						if( ui.item.value == '' )
							event.preventDefault();
				},
				select: function(event, ui) {
						if( ui.item.value == '' )
							event.preventDefault();
				}
			});
			$("#filter-port").autocomplete({
				source: "?module=ajax&ac=autocomplete&realm=port",
				minLength: 3,
				focus: function(event, ui) {
						if( ui.item.value == '' )
							event.preventDefault();
				},
				select: function(event, ui) {
						if( ui.item.value == '' )
							event.preventDefault();
				}
			});
		});
JSEND
	);

	// display results
	startPortlet ('Compatible spare ports');
	if (! count ($spare_ports))
		echo '(nothing found)';
	else
	{
		echo getSelect ($spare_ports, array ('name' => 'remote_port', 'size' => getConfigVar ('MAXSELSIZE')), NULL, FALSE);
		echo "<p>Cable ID: <input type=text id=cable name=cable>";
		// suggest patch cables where it makes sense
		$heaps = getPatchCableHeapOptionsForOIF ($port_info['oif_id']);
		if (count ($heaps))
			// Use + instead of array_merge() to avoid renumbering the keys.
			echo '<p>Patch cable: ' . getSelect (array (0 => 'none') + $heaps, array ('name' => 'heap_id'));
		echo "<p><input type='submit' value='Link' name='do_link'>";
	}
	finishPortlet();
	echo '</form>';
}

function renderPopupIPv4Selector()
{
	assertPermission('ipv4space', 'default');
	echo '<h2>Choose an IPv4 allocation:</h2><br><br>';
	echo '<form action="javascript:;">';
	echo '<input type=hidden id=ip>';
	echo '<select size=' . getConfigVar ('MAXSELSIZE') . ' id=addresses>';
	$addresses = array();
	foreach (getAllIPv4Allocations() as $each)
	{
		$each['object_name'] = formatObjectDisplayedName ($each['object_name'], $each['objtype_id']);
		$addresses[] = $each;
	}
	usort ($addresses, 'sortObjectAddressesAndNames');
	foreach ($addresses as $address)
		echo "<option value='${address['ip']}' onclick='getElementById(\"ip\").value=\"${address['ip']}\";'>" .
		"${address['object_name']} ${address['name']} ${address['ip']}</option>\n";
	echo '</select><br><br>';
	echo "<input type=submit value='Proceed' onclick='".
		"if (getElementById(\"ip\")!=\"\") {".
		" opener.document.getElementById(\"remoteip\").value=getElementById(\"ip\").value;".
		" window.close();}'>";
	echo '</form>';
}

function renderPopupHTML ($contents)
{
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" style="height: 100%;">';
	echo '<head>';
	echo '<title>RackTables pop-up</title>';
	printPageHeaders();
	echo '</head>';
	echo '<body style="height: 100%;">';
	echo "<div class=popupbar>${contents}</div>";
	echo '</body>';
	echo '</html>';
}
