<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

function getPatchCableHeapCursorCode ($heap, $zoom_heap_id)
{
	global $pageno, $tabno;
	if ($heap['logc'] == 0)
		return '&nbsp;';
	$linkparams = array
	(
		'page' => $pageno,
		'tab' => $tabno,
	);
	if ($heap['id'] == $zoom_heap_id)
	{
		$imagename = 'Zooming';
		$imagetext = 'hide event log';
	}
	else
	{
		$imagename = 'Zoom';
		$imagetext = 'display event log';
		$linkparams['zoom_heap_id'] = $heap['id'];
	}
	return '<a href="' . makeHref ($linkparams) . '">'  . getImageHREF ($imagename, $imagetext) . '</a>';
}

function renderPatchCableHeapSummary()
{
	if (! count ($summary = getPatchCableHeapSummary()))
		return;
	startPortlet ('Heaps');
	$columns = array
	(
		array ('th_text' => 'Amount', 'row_key' => 'amount', 'td_class' => 'tdright'),
		array ('th_text' => 'End 1', 'row_key' => 'end1_connector'),
		array ('th_text' => 'Cable type', 'row_key' => 'pctype'),
		array ('th_text' => 'End 2', 'row_key' => 'end2_connector'),
		array ('th_text' => 'Length', 'row_key' => 'length', 'td_class' => 'tdright'),
		array ('th_text' => 'Description', 'row_key' => 'description'),
		array ('th_text' => '&nbsp;', 'row_key' => 'cursor_code', 'td_escape' => FALSE),
	);
	$zoom_heap_id = array_key_exists ('zoom_heap_id', $_REQUEST) ? genericAssertion ('zoom_heap_id', 'uint') : NULL;
	foreach (array_keys ($summary) as $key)
		$summary[$key]['cursor_code'] = getPatchCableHeapCursorCode ($summary[$key], $zoom_heap_id);
	renderTableViewer ($columns, $summary);
	finishPortlet();

	if ($zoom_heap_id === NULL)
		return;
	if (! count ($eventlog = getPatchCableHeapLogEntries ($zoom_heap_id)))
		return;
	startPortlet ('Event log');
	$columns = array
	(
		array ('th_text' => 'Date', 'row_key' => 'date'),
		array ('th_text' => 'User', 'row_key' => 'user', 'td_maxlen' => 255),
		array ('th_text' => 'Message', 'row_key' => 'message', 'td_maxlen' => 255),
	);
	renderTableViewer ($columns, $eventlog);
	finishPortlet();
}

function renderPatchCableHeapEditor()
{
	function printNewitemTR()
	{
		printOpFormIntro ('add');
		echo '<tr>';
		echo '<td class=tdleft>' . getImageHREF ('create', 'create new', TRUE) . '</td>';
		echo "<td>&nbsp;</td>";
		echo '<td>' . getSelect (getPatchCableConnectorOptions(), array ('name' => 'end1_conn_id')) . '</td>';
		echo '<td>' . getSelect (getPatchCableTypeOptions(), array ('name' => 'pctype_id')) . '</td>';
		echo '<td>' . getSelect (getPatchCableConnectorOptions(), array ('name' => 'end2_conn_id')) . '</td>';
		echo '<td><input type=text size=6 name=length value="1.00"></td>';
		echo '<td><input type=text size=48 name=description></td>';
		echo '<td class=tdleft>' . getImageHREF ('create', 'create new', TRUE) . '</td>';
		echo '</tr></form>';
	}
	echo '<table class=widetable border=0 cellpadding=5 cellspacing=0 align=center>';
	echo '<tr><th>&nbsp;</th><th>Amount</th><th>End 1</th><th>Cable type</th><th>End 2</th><th>Length</th><th>Description</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewitemTR();
	foreach (getPatchCableHeapSummary() as $heap)
	{
		printOpFormIntro ('upd', array ('id' => $heap['id']));
		echo '<tr>';
		echo '<td>' . getOpLink (array ('op' => 'del', 'id' => $heap['id']), '', 'destroy', 'remove') . '</td>';
		echo "<td class=tdright>${heap['amount']}</td>";
		echo '<td>' . getSelect (getPatchCableConnectorOptions(), array ('name' => 'end1_conn_id'), $heap['end1_conn_id']) . '</td>';
		echo '<td>' . getSelect (getPatchCableTypeOptions(), array ('name' => 'pctype_id'), $heap['pctype_id']) . '</td>';
		echo '<td>' . getSelect (getPatchCableConnectorOptions(), array ('name' => 'end2_conn_id'), $heap['end2_conn_id']) . '</td>';
		echo "<td><input type=text size=6 name=length value='${heap['length']}'></td>";
		echo '<td><input type=text size=48 name=description value="' . stringForTextInputValue ($heap['description'], 255) . '"></td>';
		echo '<td>' . getImageHREF ('save', 'Save changes', TRUE) . '</td>';
		echo '</tr>';
		echo '</form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewitemTR();
	echo '</table>';
}

function renderPatchCableHeapAmount()
{
	echo '<table class=widetable border=0 cellpadding=5 cellspacing=0 align=center>';
	echo '<tr><th colspan=3>Amount</th><th>End 1</th><th>Cable type</th><th>End 2</th><th>Length</th><th>Description</th><th>&nbsp;</th></tr>';
	foreach (getPatchCableHeapSummary() as $heap)
	{
		printOpFormIntro ('set', array ('id' => $heap['id']));
		echo '<tr>';
		echo '<td>';
		if ($heap['amount'] > 0)
			echo getOpLink (array ('op' => 'dec', 'id' => $heap['id']), '', 'delete', 'consume');
		else
			echo getImageHREF ('nodelete');
		echo '</td>';
		echo "<td><input type=text size=7 name=amount value='${heap['amount']}'></td>";
		echo '<td>' . getOpLink (array ('op' => 'inc', 'id' => $heap['id']), '', 'add', 'replenish') . '</td>';
		echo '<td>' . stringForTD ($heap['end1_connector'], 32) . '</td>';
		echo '<td>' . stringForTD ($heap['pctype'], 255) . '</td>';
		echo '<td>' . stringForTD ($heap['end2_connector'], 32) . '</td>';
		echo "<td class=tdright>${heap['length']}</td>";
		echo '<td>' . stringForTD ($heap['description'], 255) . '</td>';
		echo '<td>' . getImageHREF ('save', 'Save changes', TRUE) . '</td>';
		echo '</tr></form>';
	}
	echo '</table>';
}

function renderPatchCableConfiguration()
{
	echo '<table class=objview border=0 width="100%"><tr><td class=pcleft>';

	startPortlet ('Connectors');
	renderSimpleTableWithOriginViewer
	(
		getPatchCableConnectorList(),
		array
		(
			'header' => 'Connector',
			'key' => 'id',
			'value' => 'connector',
			'width' => 32,
		)
	);
	finishPortlet();

	startPortlet ('Connector compatibility');
	renderTwoColumnCompatTableViewer
	(
		getPatchCableConnectorCompat(),
		array
		(
			'header' => 'Cable type',
			'key' => 'pctype_id',
			'value' => 'pctype',
			'width' => 64,
		),
		array
		(
			'header' => 'Connector',
			'key' => 'connector_id',
			'value' => 'connector',
			'width' => 32,
		)
	);
	finishPortlet();

	echo '</td><td class=pcright>';

	startPortlet ('Cable types');
	renderSimpleTableWithOriginViewer
	(
		getPatchCableTypeList(),
		array
		(
			'header' => 'Cable type',
			'key' => 'id',
			'value' => 'pctype',
			'width' => 64,
		)
	);
	finishPortlet();

	startPortlet ('Cable types and port outer interfaces');
	renderTwoColumnCompatTableViewer
	(
		getPatchCableOIFCompat(),
		array
		(
			'header' => 'Cable type',
			'key' => 'pctype_id',
			'value' => 'pctype',
			'width' => 64,
		),
		array
		(
			'header' => 'Outer interface',
			'key' => 'oif_id',
			'value' => 'oif_name',
			'width' => 48,
		)
	);
	finishPortlet();

	echo '</td></tr></table>';
}

function renderPatchCableConnectorEditor()
{
	echo '<br>';
	renderSimpleTableWithOriginEditor
	(
		getPatchCableConnectorList(),
		array
		(
			'header' => 'Connector',
			'key' => 'id',
			'value' => 'connector',
			'width' => 32,
		)
	);
	echo '<br>';
}

function renderPatchCableTypeEditor()
{
	echo '<br>';
	renderSimpleTableWithOriginEditor
	(
		getPatchCableTypeList(),
		array
		(
			'header' => 'Cable type',
			'key' => 'id',
			'value' => 'pctype',
			'width' => 64,
		)
	);
	echo '<br>';
}

function renderPatchCableConnectorCompatEditor()
{
	echo '<br>';
	renderTwoColumnCompatTableEditor
	(
		getPatchCableConnectorCompat(),
		array
		(
			'header' => 'Cable type',
			'key' => 'pctype_id',
			'value' => 'pctype',
			'width' => 64,
			'options' => getPatchCableTypeOptions(),
		),
		array
		(
			'header' => 'Connector',
			'key' => 'connector_id',
			'value' => 'connector',
			'width' => 32,
			'options' => getPatchCableConnectorOptions()
		)
	);
	echo '<br>';
}

function renderPatchCableOIFCompatEditor()
{
	echo '<br>';
	renderTwoColumnCompatTableEditor
	(
		getPatchCableOIFCompat(),
		array
		(
			'header' => 'Cable type',
			'key' => 'pctype_id',
			'value' => 'pctype',
			'width' => 64,
			'options' => getPatchCableTypeOptions(),
		),
		array
		(
			'header' => 'Outer interface',
			'key' => 'oif_id',
			'value' => 'oif_name',
			'width' => 48,
			'options' => getPortOIFOptions()
		)
	);
	echo '<br>';
}
