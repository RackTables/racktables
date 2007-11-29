<?php
/*
*
*  This file contains frontend functions for RackTables.
*
*/

// Interface function's special.
$nextorder['odd'] = 'even';
$nextorder['even'] = 'odd';

// Main menu.
function renderIndex ()
{
	global $root;
?>
<table border=0 cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		<td>
			<div style='text-align: center; margin: 10px; '>
			<table width='100%' cellspacing=0 cellpadding=30 class=mainmenu border=0>
				<tr>
					<td>
						<h1><a href='<?php echo $root; ?>?page=rackspace'>Rackspace<br>
						<?php printImageHREF ('rackspace'); ?></a></h1>
					</td>
					<td>
						<h1><a href='<?php echo $root; ?>?page=objects'>Objects<br>
						<?php printImageHREF ('objects'); ?></a></h1>
					</td>
					<td>
							<h1><a href='<?php echo $root; ?>?page=ipv4space'>IPv4 space<br>
							<?php printImageHREF ('ipv4space'); ?></a></h1>
					</td>
				</tr>
			</table>
			<table width='100%' cellspacing=0 cellpadding=30 class=mainmenu border=0>
				<tr>
					<td>
							<h1><a href='<?php echo $root; ?>?page=config'>Configuration<br>
							<?php printImageHREF ('config'); ?></a></h1>
					</td>
					<td>
						<h1><a href='<?php echo $root; ?>?page=reports'>Reports<br>
						<?php printImageHREF ('reports'); ?></a></h1>
					</td>
					<td>
						<h1><a href='<?php echo $root; ?>?page=help'>Help<br>
						<?php printImageHREF ('help'); ?></a></h1>
					</td>
				</tr>
			</table>
			</div>
		</td>
	</tr>
</table>
<?php
}

function renderRackspace ()
{
?>
	<table border=0 cellpadding=10 cellpadding=1>
<?php
	// generate thumb gallery
	$rackrowList = getRackRowInfo();
	global $root, $nextorder;
	$rackwidth = getConfigVar ('rtwidth_0') + getConfigVar ('rtwidth_1') + getConfigVar ('rtwidth_2');
	$order = 'odd';
	foreach ($rackrowList as $rackrow)
	{
		echo "<tr class=row_${order}><th><a href='${root}?page=row&row_id=${rackrow['dict_key']}'>${rackrow['dict_value']}</a></th>";
		$rackList = getRacksForRow ($rackrow['dict_key']);
		echo "<td><table border=0 cellspacing=5><tr>";
		foreach ($rackList as $dummy => $rack)
		{
			echo "<td align=center><a href='${root}?page=rack&rack_id=${rack['id']}'>";
			echo "<img border=0 width=${rackwidth} height=";
			echo 3 + 3 + $rack['height'] * 2;
			echo " title='${rack['height']} units'";
			echo "src='render_rack_thumb.php?rack_id=${rack['id']}'>";
			echo "<br>${rack['name']}</a></td>";
		}
		echo "</tr></table></tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table>\n";
}

function renderRow ($row_id)
{
	if ($row_id == 0)
	{
		showError ('Invalid row_id in renderRow()');
		return;
	}
	if (($rowInfo = getRackRowInfo ($row_id)) == NULL)
	{
		showError ('getRackRowInfo() failed in renderRow()');
		return;
	}
	// Main layout starts.
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";

	// Left portlet with row information.
	echo "<tr><td class=pcleft>";
	startPortlet ($rowInfo['dict_value']);
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>Racks:</th><td class=tdleft>${rowInfo['count']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Units:</th><td class=tdleft>${rowInfo['sum']}</td></tr>\n";
	echo "</table><br>\n";
	finishPortlet();

	echo "</td><td class=pcright>";

	global $root, $nextorder;
	$rackwidth = getConfigVar ('rtwidth_0') + getConfigVar ('rtwidth_1') + getConfigVar ('rtwidth_2');
	$rackList = getRacksForRow ($row_id);
	$order = 'odd';
	startPortlet ('Racks');
	echo "<table border=0 cellspacing=5 align='center'><tr>";
	foreach ($rackList as $dummy => $rack)
	{
		echo "<td align=center class=row_${order}><a href='${root}?page=rack&rack_id=${rack['id']}'>";
		echo "<img border=0 width=" . $rackwidth * getConfigVar ('ROW_SCALE') . " height=";
		echo (3 + 3 + $rack['height'] * 2) * getConfigVar ('ROW_SCALE');
		echo " title='${rack['height']} units'";
		echo "src='render_rack_thumb.php?rack_id=${rack['id']}'>";
		echo "<br>${rack['name']}</a></td>";
		$order = $nextorder[$order];
	}
	echo "</tr></table>\n";
	finishPortlet();

	echo "</td></tr></table>";
}

function showError ($info = '')
{
	global $root;
	echo '<div class=msg_error>An error has occured. ';
	if (empty ($info))
		echo 'No additional information is available.';
	else
		echo "Additional information:<br><p>\n<pre>\n${info}\n</pre></p>";
	echo "Go back or try starting from <a href='${root}'>index page</a>.<br></div>\n";
}

// This function renders rack as HTML table.
function renderRack ($rack_id = 0, $hl_obj_id = 0)
{
	if ($rack_id == 0)
	{
		showError ('Invalid rack_id in renderRack()');
		return;
	}
	if (($rackData = getRackData ($rack_id)) == NULL)
	{
		showError ('getRackData() failed in renderRack()');
		return;
	}
	global $root, $pageno, $tabno;
	markAllSpans ($rackData);
	if ($hl_obj_id > 0)
		highlightObject ($rackData, $hl_obj_id);
	markupObjectProblems ($rackData);
	$prev_id = getPrevIDforRack ($rackData['row_id'], $rack_id);
	$next_id = getNextIDforRack ($rackData['row_id'], $rack_id);
	echo "<center>\n<h2><a href='${root}?page=row&row_id=${rackData['row_id']}'>${rackData['row_name']}</a> :";
	// FIXME: use 'bypass'?
	if ($prev_id != NULL)
		echo " <a href='${root}?page=rack&rack_id=${prev_id}'>&lt; &lt; &lt;</a>";
	echo " <a href='${root}?page=rack&rack_id=${rackData['id']}'>${rackData['name']}</a>";
	if ($next_id != NULL)
		echo " <a href='${root}?page=rack&rack_id=${next_id}'>&gt; &gt; &gt;</a>";
	echo "</h2>\n";
	echo "<table class=rack border=0 cellspacing=0 cellpadding=1>\n";
	echo "<tr><th width='10%'>&nbsp;</th><th width='20%'>Front</th>";
	echo "<th width='50%'>Interior</th><th width='20%'>Back</th></tr>\n";
	for ($i = $rackData['height']; $i > 0; $i--)
	{
		echo "<tr><th>$i</th>";
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			if (isset ($rackData[$i][$locidx]['skipped']))
				continue;
			$state = $rackData[$i][$locidx]['state'];
			echo "<td class=state_${state}";
			if (isset ($rackData[$i][$locidx]['hl']))
				echo $rackData[$i][$locidx]['hl'];
			if (isset ($rackData[$i][$locidx]['colspan']))
				echo ' colspan=' . $rackData[$i][$locidx]['colspan'];
			if (isset ($rackData[$i][$locidx]['rowspan']))
				echo ' rowspan=' . $rackData[$i][$locidx]['rowspan'];
			echo ">";
			switch ($state)
			{
				case 'T':
					$objectData = getObjectInfo ($rackData[$i][$locidx]['object_id']);
					if (!empty ($objectData['asset_no']))
						$prefix = "<div title='${objectData['asset_no']}";
					else
						$prefix = "<div title='no asset tag";
					// Don't tell about label, if it matches common name.
					if ($objectData['name'] != $objectData['label'] and !empty ($objectData['label']))
						$suffix = ", visible label is \"${objectData['label']}\"'>";
					else
						$suffix = "'>";
					echo $prefix . $suffix;
					echo "<a href='${root}?page=object&object_id=${objectData['id']}'>${objectData['dname']}</a></div>";
					break;
				case 'A':
					echo '<div title="This rackspace does not exist">&nbsp;</div>';
					break;
				case 'F':
					echo '<div title="Free rackspace">&nbsp;</div>';
					break;
				case 'U':
					echo '<div title="Problematic rackspace, you CAN\'T mount here">&nbsp;</div>';
					break;
				default:
					echo '<div title="No data">&nbsp;</div>';
					break;
			}
			echo '</td>';
		}
		echo "</tr>\n";
	}
	echo "</table></center>\n";
}

function renderNewObjectForm ()
{
	global $pageno, $tabno;

	// Look for current submit.
	if (isset ($_REQUEST['got_data']))
	{
		$log = array();
		assertUIntArg ('object_type_id');
		assertStringArg ('object_name', TRUE);
		assertStringArg ('object_label', TRUE);
		assertStringArg ('object_barcode', TRUE);
		assertStringArg ('object_asset_no', TRUE);
		$type_id = $_REQUEST['object_type_id'];
		$name = $_REQUEST['object_name'];
		$label = $_REQUEST['object_label'];
		$asset_no = $_REQUEST['object_asset_no'];
		$barcode = $_REQUEST['object_barcode'];

		if (commitAddObject ($name, $label, $barcode, $type_id, $asset_no) === TRUE)
			$log[] = array ('code' => 'success', 'message' => "Added new object '${name}'");
		else
			$log[] = array ('code' => 'error', 'message' => 'commitAddObject() failed in renderNewObjectForm()');
		printLog ($log);
	}

	// Render a form for the next.
	startPortlet ('Object attributes');
	echo '<form>';
	echo "<input type=hidden name=page value=${pageno}>";
	echo "<input type=hidden name=tab value=${tabno}>";
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Type:</th><td class=tdleft>";
	printSelect (getObjectTypeList(), 'object_type_id');
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>Common name:</th><td class=tdleft><input type=text name=object_name></td></tr>\n";
	echo "<tr><th class=tdright>Visible label:</th><td class=tdleft><input type=text name=object_label></td></tr>\n";
	echo "<tr><th class=tdright>Asset tag:</th><td class=tdleft><input type=text name=object_asset_no></td></tr>\n";
	echo "<tr><th class=tdright>Barcode:</th><td class=tdleft><input type=text name=object_barcode></td></tr>\n";
	echo "<tr><td class=submit colspan=2><input type=submit name=got_data value='Create'></td></tr>\n";
	echo '</form></table>';
	finishPortlet();
}

function renderNewRackForm ($row_id)
{
	global $pageno, $tabno;

	// Look for current submit.
	if (isset ($_REQUEST['got_data']))
	{
		$log = array();
		assertStringArg ('rack_name');
		assertUIntArg ('rack_height');
		assertStringArg ('rack_comment', TRUE);
		$name = $_REQUEST['rack_name'];
		$height = $_REQUEST['rack_height'];
		$comment = $_REQUEST['rack_comment'];

		if (commitAddRack ($name, $height, $row_id, $comment) === TRUE)
			$log[] = array ('code' => 'success', 'message' => "Added new rack '${name}'");
		else
			$log[] = array ('code' => 'error', 'message' => 'commitAddRack() failed in renderNewRackForm()');
		printLog ($log);
	}

	// Render a form for the next.
	startPortlet ('Rack attributes');
	echo '<form>';
	echo "<input type=hidden name=page value=${pageno}>";
	echo "<input type=hidden name=tab value=${tabno}>";
	echo "<input type=hidden name=row_id value=${row_id}>";
	echo '<table border=0 align=center>';
	$defh = getConfigVar ('DEFAULT_RACK_HEIGHT');
	if ($defh == 0)
		$defh = '';
	echo "<tr><th class=tdright>Name (required):</th><td class=tdleft><input type=text name=rack_name tabindex=1></td></tr>\n";
	echo "<tr><th class=tdright>Height in units (required):</th><td class=tdleft><input type=text name=rack_height tabindex=2 value='${defh}'></td></tr>\n";
	echo "<tr><th class=tdright>Comment:</th><td class=tdleft><input type=text name=rack_comment tabindex=3></td></tr>\n";
	echo "<tr><td class=submit colspan=2><input type=submit name=got_data value='Create'></td></tr>\n";
	echo '</form></table>';
	finishPortlet();
}

function renderEditObjectForm ($object_id)
{
	showMessageOrError();
	// Handle submit.
	if (isset ($_REQUEST['got_data']))
	{
		$log = array();
		// object_id is already verified by page handler
		assertUIntArg ('object_type_id');
		assertStringArg ('object_name', TRUE);
		assertStringArg ('object_label', TRUE);
		assertStringArg ('object_barcode', TRUE);
		assertStringArg ('object_asset_no', TRUE);
		$type_id = $_REQUEST['object_type_id'];
		if (isset ($_REQUEST['object_has_problems']) and $_REQUEST['object_has_problems'] == 'on')
			$has_problems = 'yes';
		else
			$has_problems = 'no';
		$name = $_REQUEST['object_name'];
		$label = $_REQUEST['object_label'];
		$barcode = $_REQUEST['object_barcode'];
		$asset_no = $_REQUEST['object_asset_no'];
		$comment = $_REQUEST['object_comment'];

		if (commitUpdateObject ($object_id, $name, $label, $barcode, $type_id, $has_problems, $asset_no, $comment) === TRUE)
			$log[] = array ('code' => 'success', 'message' => "Updated object '${name}'");
		else
			$log[] = array ('code' => 'error', 'message' => 'commitUpdateObject() failed in renderEditObjectForm()');
		printLog ($log);
	}

	global $pageno, $tabno;
	$object = getObjectInfo ($object_id);
	if ($object == NULL)
	{
		showError ('getObjectInfo() failed in renderEditObjectForm()');
		return;
	}

	// Render a form for the next submit;
	echo '<table border=0 width=100%><tr>';

	echo '<td class=pcleft>';
	startPortlet ('Static attributes');
	echo '<form>';
	echo "<input type=hidden name=page value=${pageno}>";
	echo "<input type=hidden name=tab value=${tabno}>";
	echo "<input type=hidden name=object_id value=${object_id}>";
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Type:</th><td class=tdleft>";
	printSelect (getObjectTypeList(), 'object_type_id', $object['objtype_id']);
	echo "</td></tr>\n";
	// Common attributes.
	echo "<tr><th class=tdright>Common name:</th><td class=tdleft><input type=text name=object_name value='${object['name']}'></td></tr>\n";
	echo "<tr><th class=tdright>Visible label:</th><td class=tdleft><input type=text name=object_label value='${object['label']}'></td></tr>\n";
	echo "<tr><th class=tdright>Asset tag:</th><td class=tdleft><input type=text name=object_asset_no value='${object['asset_no']}'></td></tr>\n";
	echo "<tr><th class=tdright>Barcode:</th><td class=tdleft><input type=text name=object_barcode value='${object['barcode']}'></td></tr>\n";
	echo "<tr><th class=tdright>Has problems:</th><td class=tdleft><input type=checkbox name=object_has_problems";
	if ($object['has_problems'] == 'yes')
		echo ' checked';
	echo "></td></tr>\n";
	echo "<tr><td colspan=2><b>Comment:</b><br><textarea name=object_comment rows=10 cols=80>${object['comment']}</textarea></td></tr>";
	echo "<tr><th class=submit colspan=2><input type=submit name=got_data value='Update'></td></tr>\n";
	echo '</form></table><br>';
	finishPortlet();
	echo '</td>';
	
	// Optional attributes.
	echo '<td class=pcright>';
	startPortlet ('Optional attributes');
	$values = getAttrValues ($object_id);
	global $root;
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>&nbsp;</th><th>Attribute</th><th>Value</th><th>&nbsp;</th></tr>\n";
	echo "<form method=post action='${root}process.php'>\n";
	echo "<input type=hidden name=page value=${pageno}>\n";
	echo "<input type=hidden name=tab value=${tabno}>\n";
	echo "<input type=hidden name=op value=upd>\n";
	echo "<input type=hidden name=object_id value=${object_id}>\n";
	echo '<input type=hidden name=num_attrs value=' . count($values) . ">\n";

	$i = 0;
	foreach ($values as $record)
	{
		echo "<input type=hidden name=${i}_attr_id value=${record['id']}>";
		echo "<tr><td><a href=${root}process.php?page=${pageno}&tab=${tabno}&op=del&object_id=${object_id}&attr_id=${record['id']}>";
		printImageHREF ('delete', 'Delete value');
		echo "</a></td>";
		echo "<td class=tdright>${record['name']}:</td><td class=tdleft>";
		switch ($record['type'])
		{
			case 'uint':
			case 'float':
			case 'string':
				echo "<input type=text name=${i}_value value='${record['value']}'>";
				break;
			case 'dict':
				$chapter = readChapter ($record['chapter_name']);
				$chapter[] = array ('dict_key' => 0, 'dict_value' => '-- NOT SET --');
				printSelect ($chapter, "${i}_value", $record['key']);
				break;
		}
		echo "</td></tr>\n";
		$i++;
	}
	echo "<tr><td colspan=3><input type=submit value='Update'></td></tr>\n";
	echo "</form>";
	echo "</table>\n";
	finishPortlet();
	echo '</td>';

	echo '</tr><tr>';

	echo '<td colspan=2>';
	startPortlet ('history');
	renderHistory ($pageno, $object_id);
	finishPortlet();
	echo '</td>';

	echo '</tr></table>';
}

// This is a clone of renderEditObjectForm().
function renderEditRackForm ($rack_id)
{
	// Handle submit.
	if (isset ($_REQUEST['got_data']))
	{
		$log = array();
		assertUIntArg ('rack_row_id');
		assertUIntArg ('rack_height');
		assertStringArg ('rack_name');
		assertStringArg ('rack_comment', TRUE);
		$row_id = $_REQUEST['rack_row_id'];
		$height = $_REQUEST['rack_height'];
		$name = $_REQUEST['rack_name'];
		$comment = $_REQUEST['rack_comment'];

		if (commitUpdateRack ($rack_id, $name, $height, $row_id, $comment) === TRUE)
			$log[] = array ('code' => 'success', 'message' => "Updated rack '${name}'");
		else
			$log[] = array ('code' => 'error', 'message' => 'commitUpdateRack() failed in renderEditRackForm()');
		printLog ($log);
	}

	global $pageno, $tabno;
	$rack = getRackData ($rack_id);
	if ($rack == NULL)
	{
		showError ('getRackData() failed in renderEditRackForm()');
		return;
	}

	// Render a form for the next.
	startPortlet ('Rack attributes');
	echo '<form>';
	echo "<input type=hidden name=page value=${pageno}>";
	echo "<input type=hidden name=tab value=${tabno}>";
	echo "<input type=hidden name=rack_id value=${rack_id}>";
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Rack row:</th><td class=tdleft>";
	printSelect (getRackRowInfo(), 'rack_row_id', $rack['row_id']);
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>Name (required):</th><td class=tdleft><input type=text name=rack_name value='${rack['name']}'></td></tr>\n";
	echo "<tr><th class=tdright>Height (required):</th><td class=tdleft><input type=text name=rack_height value='${rack['height']}'></td></tr>\n";
	echo "<tr><th class=tdright>Comment:</th><td class=tdleft><input type=text name=rack_comment value='${rack['comment']}'></td></tr>\n";
	echo "<tr><td class=submit colspan=2><input type=submit name=got_data value='Update'></td></tr>\n";
	echo '</form></table><br>';
	finishPortlet();
	
	startPortlet ('History');
	renderHistory ($pageno, $rack_id);
	finishPortlet();
}

// This is a helper for creators and editors.
function printSelect ($rowList, $select_name, $selected_id = 1)
{
	echo "<select name=${select_name}>";
	foreach ($rowList as $dummy => $data)
	{
		echo "<option value=${data['dict_key']}";
		if ($data['dict_key'] == $selected_id)
			echo ' selected';
		echo ">${data['dict_value']}</option>";
	}
	echo "</select>";
}

// This is a universal editor of rack design/waste.
function renderGridForm ($rack_id = 0, $filter, $header, $submit, $state1, $state2)
{
	if ($rack_id == 0)
	{
		showError ('Invalid rack_id in renderGridFrom()');
		return;
	}
	if (($rackData = getRackData ($rack_id)) == NULL)
	{
		showError ('getRackData() failed in renderGridFrom()');
		return;
	}

	global $root, $pageno, $tabno;
	$filter($rackData);
	markupObjectProblems ($rackData);

	// Process form submit.
	if (isset ($_REQUEST['do_update']))
	{
		$log[] = processGridForm ($rackData, $state1, $state2);
		printLog($log);
	}

	// Render the result whatever it is.
	// Main layout.
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${rackData['name']}</h1></td></tr>\n";

	// Left column with information portlet.
	echo "<tr><td class=pcleft height='1%' width='50%'>";
	startPortlet ('Rack information');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>Rack name:</th><td class=tdleft>${rackData['name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Height:</th><td class=tdleft>${rackData['height']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Rack row:</th><td class=tdleft>${rackData['row_name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Comment:</th><td class=tdleft>${rackData['comment']}</td></tr>\n";
	echo "</table>\n";
	finishPortlet();

	echo "</td>\n";
	echo "<td class=pcright>";

	// Grid form.
	startPortlet ($header);
	echo "<center>\n";
	echo "<table class=rack border=0 cellspacing=0 cellpadding=1>\n";
	echo "<tr><th width='10%'>&nbsp;</th><th width='20%'>Front</th>";
	echo "<th width='50%'>Interior</th><th width='20%'>Back</th></tr>\n";
	echo "<form action='${root}?'>\n";
	echo "<input type=hidden name=page value=${pageno}>\n";
	echo "<input type=hidden name=tab value=${tabno}>\n";
	echo "<input type=hidden name=rack_id value=${rack_id}>\n";
	markupAtomGrid ($rackData, $state2);
	renderAtomGrid ($rackData);
	echo "</table></center>\n";
	echo "<br><input type=submit name=do_update value='${submit}'></form><br><br>\n";
	finishPortlet();
	echo "</td></tr></table>\n";
}

function renderRackDesign ($rack_id)
{
	renderGridForm ($rack_id, 'applyRackDesignMask', 'Rack design', 'Set rack design', 'A', 'F');
}

function renderRackProblems ($rack_id = 0)
{
	renderGridForm ($rack_id, 'applyRackProblemMask', 'Rack problems', 'Mark unusable atoms', 'F', 'U');
}

function startPortlet ($title = '')
{
	echo "<div class=portlet><h2>${title}</h2>";
}

function finishPortlet ()
{
	echo "</div>\n";
}

function printRefsOfType ($refs, $type, $eq)
{
	global $root;
	$gotone=0;
	foreach ($refs as $ref)
	{
		if ($eq($ref['type'], $type))
		{
			if ($gotone) echo ', ';
			echo "<a href='${root}?page=object&object_id=${ref['object_id']}'>";
			if (!empty ($ref['name']))
				echo $ref['name'] . '@';
			echo "${ref['object_name']}</a>";
			$gotone=1;
		}
	}
}

function renderRackObject ($object_id = 0)
{
	global $root;
	if ($object_id <= 0)
	{
		showError ('Invalid object_id in renderRackObject()');
		return;
	}
	$info = getObjectInfo ($object_id);
	if ($info == NULL)
	{
		showError ('getObjectInfo() failed in renderRackObject()');
		return;
	}
	// Main layout starts.
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${info['dname']}</h1></td></tr>\n";
	// left column with uknown number of portlets
	echo "<tr><td class=pcleft>";
	startPortlet ('Object information');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	if (!empty ($info['name']))
		echo "<tr><th width='50%' class=tdright>Common name:</th><td class=tdleft>${info['name']}</td></tr>\n";
	elseif (in_array ($info['objtype_id'], explode (',', getConfigVar ('NAMEFUL_OBJTYPES'))))
		echo "<tr><td colspan=2 class=msg_error>Common name is missing.</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Object type:</th><td class=tdleft>${info['objtype_name']}</td></tr>\n";
	if (!empty ($info['asset_no']))
		echo "<tr><th width='50%' class=tdright>Asset tag:</th><td class=tdleft>${info['asset_no']}</td></tr>\n";
	elseif (in_array ($info['objtype_id'], explode (',', getConfigVar ('NAMEFUL_OBJTYPES'))))
		echo "<tr><td colspan=2 class=msg_error>Asset tag is missing.</td></tr>\n";
	if (!empty ($info['label']))
		echo "<tr><th width='50%' class=tdright>Visible label:</th><td class=tdleft>${info['label']}</td></tr>\n";
	if (!empty ($info['barcode']))
		echo "<tr><th width='50%' class=tdright>Barcode:</th><td class=tdleft>${info['barcode']}</td></tr>\n";
	if ($info['has_problems'] == 'yes')
		echo "<tr><td colspan=2 class=msg_error>Has problems</td></tr>\n";
	foreach (getAttrValues ($object_id) as $record)
		if (!empty ($record['value']))
			echo "<tr><th width='50%' class=opt_attr_th>${record['name']}:</th><td class=tdleft>${record['value']}</td></tr>\n";
	echo "</table><br>\n";
	finishPortlet();

	if (!empty ($info['comment']))
	{
		startPortlet ('Comment');
		echo '<div class=commentblock>' . $info['comment'] . '</div>';
		finishPortlet ();
	}

	$ports = getObjectPortsAndLinks ($object_id);
	if (count ($ports))
	{
		startPortlet ('Ports and links');
		usort($ports, 'sortByName');
		if ($ports)
		{
			$hl_port_id = 0;
			if (isset ($_REQUEST['hl_port_id']))
			{
				assertUIntArg ('hl_port_id');
				$hl_port_id = $_REQUEST['hl_port_id'];
			}
			echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
			echo "<tr><th>Local name</th><th>Visible label</th><th>Port type</th><th>L2 address</th>";
			echo "<th>Rem. Object</th><th>Rem. port</th></tr>\n";
			foreach ($ports as $port)
			{
				echo '<tr';
				if ($hl_port_id == $port['id'])
					echo ' class=port_highlight';
				echo "><td>${port['name']}</td><td>${port['label']}</td><td>${port['type']}</td>";
				echo "<td>${port['l2address']}</td>";
				if ($port['remote_object_id'])
				{
					echo "<td><a href='${root}?page=object&object_id=${port['remote_object_id']}'>${port['remote_object_name']}</a></td>";
					echo "<td>${port['remote_name']}</td>";
				}
				elseif (!empty ($port['reservation_comment']))
				{
					echo "<td><b>Reserved;</b></td>";
					echo "<td>${port['reservation_comment']}</td>";
				}
				else
					echo '<td>&nbsp;</td><td>&nbsp;</td>';
				echo "</tr>\n";
			}
			echo "</table><br>\n";
		}
		finishPortlet();
	}
	$addresses = getObjectAddresses ($object_id);
	usort($addresses, 'sortAddresses');
	if (count ($addresses))
	{
		startPortlet ('IPv4 addresses');
		echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
		echo "<tr><th>Interface name</th><th>IP Address</th><th>Description</th><th>Misc</th></tr>\n";
		foreach ($addresses as $addr)
		{
			if (strlen($addr['address_name'])>40)
				$address_name = substr($addr['address_name'],0,38).'...';
			else
				$address_name = $addr['address_name'];

			$virtnum = countRefsOfType($addr['references'], 'virtual', 'eq');
			$sharednum = countRefsOfType($addr['references'], 'shared', 'eq');
			$regnum = countRefsOfType($addr['references'], 'regular', 'eq');
			$notvirtnum = countRefsOfType($addr['references'], 'virtual', 'neq');

			if ($addr['address_reserved']=='yes')
				$class='trwarning';
			elseif ($addr['type']!='virtual' && $regnum>0)
				$class='trwarning';
			elseif ($addr['type']=='regular' && $sharednum>0)
				$class='trwarning';
			else 
				$class='';

			echo "<tr class='$class'><td>${addr['name']}</td><td><a href='${root}?page=ipaddress&ip=${addr['ip']}'>${addr['ip']}</a></td><td class='description'>$address_name</td><td>\n";

			if ($addr['address_reserved']=='yes')
				echo "<b>Reserved;</b> ";

			if ($addr['type'] == 'virtual')
			{
				echo "<b>V</b>";
				if ($notvirtnum > 0)
				{
					echo " Owners: ";
					printRefsOfType($addr['references'], 'virtual', 'neq');
				}
			}
			elseif ($addr['type'] == 'shared')
			{
				echo "<b>S</b>";
				if ($sharednum > 0)
				{
					echo " Peers: ";
					printRefsOfType($addr['references'], 'shared', 'eq');
					echo ";";
				}
				if ($virtnum > 0)
				{
					echo " Virtuals: ";
					printRefsOfType($addr['references'], 'virtual', 'eq');
					echo ";";
				}
				if ($regnum > 0)
				{
					echo " Collisions: ";
					printRefsOfType($addr['references'], 'regular', 'eq');
				}
				
			}
			else
			{
				if ($virtnum > 0)
				{
					echo " Virtuals: ";
					printRefsOfType($addr['references'], 'virtual', 'eq');
					echo ";";
				}
				if ($notvirtnum > 0)
				{
					echo " Collisions: ";
					printRefsOfType($addr['references'], 'virtual', 'neq');
				}
			}

			echo "</td></tr>\n";
		}
		echo "</table><br>\n";
		finishPortlet();
	}

	$forwards = getObjectForwards ($object_id);
	if (count($forwards['in']) or count($forwards['out']))
	{
		startPortlet('NATv4');

		if (count($forwards['out']))
		{

			echo "<h3>locally performed NAT</h3>";

			echo "<table class='widetable' cesspadding=5 cellspacing=0 border=0 align='center'>\n";
			echo "<tr><th>Proto</th><th>Match endpoint</th><th>Translate to</th><th>Target object</th><th>Rule comment</th></tr>\n";

			foreach ($forwards['out'] as $pf)
			{
				$class='trwarning';
				$name='';
				foreach ($addresses as $addr)
					if ($addr['ip'] == $pf['localip'])
					{
						$class='';
						$name=$addr['name'];
						break;
					}

				echo "<tr class='$class'>";

				echo "<td>${pf['proto']}</td><td class=tdleft>${name}: <a href='${root}?page=ipaddress&tab=default&ip=${pf['localip']}'>${pf['localip']}</a>:${pf['localport']}</td>";

				echo "<td class=tdleft><a href='${root}?page=ipaddress&tab=default&ip=${pf['remoteip']}'>${pf['remoteip']}</a>:${pf['remoteport']}</td>";

				$address=getIPAddress($pf['remoteip']);

				echo "<td class='description'>";
				if (count ($address['bonds']))
					foreach($address['bonds'] as $bond)
						echo "<a href='${root}?page=object&tab=default&object_id=${bond['object_id']}'>${bond['object_name']}(${bond['name']})</a> ";
				elseif (!empty ($pf['remote_addr_name']))
					echo '(' . $pf['remote_addr_name'] . ')';

				echo "</td><td class='description'>${pf['description']}</td>";

				echo "</tr>";
			}
			echo "</table><br><br>";
		}
		if (count($forwards['in']))
		{
			echo "<h3>arriving NAT connections</h3>";

			echo "<table class='widetable' cesspadding=5 cellspacing=0 border=0 align='center'>\n";
			echo "<tr><th>Matched endpoint</th><th>Source object</th><th>Translated to</th><th>Rule comment</th></tr>\n";

			foreach ($forwards['in'] as $pf)
			{
				echo "<tr>";
				echo "<td>${pf['proto']}/<a href='${root}?page=ipaddress&tab=default&ip=${pf['localip']}'>${pf['localip']}</a>:${pf['localport']}</td>";

				echo "<td class='description'><a href='${root}?page=object&tab=default&object_id=${pf['object_id']}'>${pf['object_name']}</a>";

				echo "</td><td><a href='${root}?page=ipaddress&tab=default&ip=${pf['remoteip']}'>${pf['remoteip']}</a>:${pf['remoteport']}</td>";
				echo "<td class='description'>${pf['description']}</td></tr>";
			}

			echo "</table><br><br>";
		}
		finishPortlet();
	}

	echo "</td>\n";

	// After left column we have (surprise!) right column with rackspace portled only.
	echo "<td class=pcright>";
	// rackspace portlet
	startPortlet ('Rackspace allocation');
	// FIXME: now we call getRackData() twice
	$racks = getResidentRacksData ($object_id);
	foreach ($racks as $rackData)
		renderRack ($rackData['id'], $object_id);
	echo '<br>';
	finishPortlet();
	echo "</td></tr>";
	echo "</table>\n";
}

function renderRackMultiSelect ($sname, $racks, $selected)
{
	echo "<select name=${sname} multiple size=" . getConfigVar ('MAXSELSIZE') . " onchange='getElementById(\"racks\").submit()'>\n";
	foreach ($racks as $rack)
	{
		echo "<option value=${rack['id']}";
		if (!(array_search ($rack['id'], $selected) === FALSE))
			echo ' selected';
		echo">${rack['row_name']}: ${rack['name']}</option>\n";
	}
	echo "</select>\n";
}

function showMessageOrError ()
{
	if (isset($_REQUEST['message']))
		echo "<div class=msg_success>${_REQUEST['message']}</div>";
	if (isset($_REQUEST['error']))
		echo "<div class=msg_error>${_REQUEST['error']}</div>";
}

// This function renders a form for port edition.
function renderPortsForObject ($object_id = 0)
{
	global $root, $pageno, $tabno;
	if ($object_id <= 0)
	{
		showError ('Invalid object_id in renderPortsForObject()');
		return;
	}
	showMessageOrError();
	startPortlet ('Ports and interfaces');
	$ports = getObjectPortsAndLinks ($object_id);
	usort($ports, 'sortByName');
	echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	echo "<tr><th>&nbsp;</th><th>Local name</th><th>Visible label</th><th>Port type</th><th>L2 address</th>";
	echo "<th>Rem. object</th><th>Rem. port</th><th>(Un)link or (un)reserve</th><th>&nbsp;</th></tr>\n";
	foreach ($ports as $port)
	{
		echo "<form action='${root}process.php'>";
		echo "<input type=hidden name=op value=editPort>";
		echo "<input type=hidden name=page value='${pageno}'>\n";
		echo "<input type=hidden name=tab value='${tabno}'>\n";
		echo "<input type=hidden name=port_id value='${port['id']}'>";
		echo "<input type=hidden name=object_id value='$object_id'>\n";
		echo "<tr><td><a href='${root}process.php?op=delPort&page=${pageno}&tab=${tabno}&port_id=${port['id']}&object_id=$object_id&port_name=${port['name']}'>";
		printImageHREF ('delete', 'Unlink and Delete this port');
		echo "</a></td>\n";
		echo "<td><input type=text name=name value='${port['name']}' size=8></td>";
		echo "<td><input type=text name=label value='${port['label']}' size=24></td>";
		echo "<td>${port['type']}</td>\n";
		echo "<td><input type=text name=l2address value='${port['l2address']}'></td>\n";
		if ($port['remote_object_id'])
		{
			echo "<td><a href='${root}?page=object&object_id=${port['remote_object_id']}'>${port['remote_object_name']}</a></td>";
			echo "<td>${port['remote_name']}</td>";
			echo "<td><a href='${root}process.php?op=unlinkPort&page=${pageno}&tab=${tabno}&port_id=${port['id']}&object_id=$object_id&port_name=";
			echo urlencode ($port['name']);
			echo "&remote_port_name=${port['remote_name']}&remote_object_name=${port['remote_object_name']}'>";
			printImageHREF ('unlink', 'Unlink this port');
			echo "</a></td>";
		}
		elseif (!empty ($port['reservation_comment']))
		{
			echo "<td><b>Reserved;</b></td>";
			echo "<td><input type=text name=reservation_comment value='${port['reservation_comment']}'></td>";
			echo "<td><a href='${root}process.php?op=useup&page=${pageno}&tab=${tabno}&port_id=${port['id']}&object_id=${object_id}'>";
			printImageHREF ('useup', 'Use up this port');
			echo "</a></td>";
		}
		else
		{
			echo "<td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td>";
			echo "<a href='javascript:;' onclick='window.open(\"${root}link_helper.php?port=${port['id']}&type=${port['type_id']}&object_id=$object_id&port_name=";
			echo urlencode ($port['name']);
			echo "\",\"findlink\",\"height=700, width=400, location=no, menubar=no, resizable=yes, scrollbars=no, status=no, titlebar=no, toolbar=no\");'>";
			printImageHREF ('link', 'Link this port');
			echo "</a> <input type=text name=reservation_comment>";
			echo "</td>\n";
		}
		echo "<td><input type='submit' value='OK'></td>";
		echo "</form></tr>\n";
	}
	echo "<form action='${root}process.php'><tr>";
	echo "<td colspan=2><input type=text size=10 name=port_name tabindex=100></td>\n";
	echo "<td><input type=text size=24 name=port_label tabindex=101></td>";
	echo "<input type=hidden name=op value=addPort>\n";
	echo "<input type=hidden name=object_id value='${object_id}'>\n";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<td><select name='port_type_id' tabindex=102>\n";
	$types = getPortTypes();
	$default_port_type = getConfigVar ('default_port_type');
	foreach ($types as $typeid => $typename)
	{
		echo "<option value='${typeid}'";
		if ($typeid == $default_port_type)
			echo " selected";
		echo ">${typename}</option>\n";
	}
	echo "</select></td>";
	echo "<td><input type=text name=port_l2address tabindex=103></td>\n";
	echo "<td colspan=4><input type='submit' value='Add a new port' tabindex=104></td></tr></form>";
	echo "</table><br>\n";
	finishPortlet();

	startPortlet ('Add/update multiple ports');
	echo "<form action=${root}process.php method=post>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=object_id value='${object_id}'>\n";
	echo "<input type=hidden name=op value=addMultiPorts>";
	echo 'Format: <select name=format>';
	echo '<option value=c2900 disabled>Cisco 2900 series: sh int eth</option>';
	echo '<option value=c3600eth disabled>Cisco 3600 ethernet: sh arp | inc -</option>';
	echo '<option value=c3600asy>Cisco 3600 async: sh line | inc TTY</option>';
	echo '<option value=fiwg selected>Foundry ServerIron/FastIron WorkGroup/Edge: sh int br</option>';
	echo '<option value=fiedge disabled>Foundry FastIron Edge: sh int br</option>';
	echo '<option value=fisxii>Foundry FastIron SuperX/II4000: sh int br</option>';
	echo "</select>";
	echo 'Default port type: ';
	echo "<select name=port_type>\n";
	foreach ($types as $typeid => $typename)
	{
		echo "<option value='${typeid}'";
		if ($typeid == $default_port_type)
			echo " selected";
		echo ">${typename}</option>\n";
	}
	echo "</select>";
	echo "<input type=submit value='Parse output'><br>\n";
	echo "<textarea name=input cols=100 rows=50></textarea><br>\n";
	echo '</form>';
	finishPortlet();
}

function renderNetworkForObject ($object_id=0)
{
	global $root, $pageno, $tabno;
	if ($object_id <= 0)
	{
		showError ('Invalid object_id in renderNetworkForObject()');
		return;
	}
	showMessageOrError();
	startPortlet ('Network Addresses');
	$addresses = getObjectAddresses ($object_id);
	usort($addresses, 'sortAddresses');
	echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	echo "<tr><th>&nbsp;</th><th>Interface name</th><th>IP Address</th><th>Description</th><th>Type</th><th>Misc</th><th>&nbsp</th></tr>\n";
	foreach ($addresses as $addr)
	{
		if (strlen($addr['address_name'])>40)
			$address_name = substr($addr['address_name'],0,38).'...';
		else
			$address_name = $addr['address_name'];

		$virtnum = countRefsOfType($addr['references'], 'virtual', 'eq');
		$sharednum = countRefsOfType($addr['references'], 'shared', 'eq');
		$regnum = countRefsOfType($addr['references'], 'regular', 'eq');
		$notvirtnum = countRefsOfType($addr['references'], 'virtual', 'neq');

		if ($addr['address_reserved']=='yes')
			$class='trwarning';
		elseif ($addr['type']!='virtual' && $regnum>0)
			$class='trwarning';
		elseif ($addr['type']=='regular' && $sharednum>0)
			$class='trwarning';
		else 
			$class='';

		echo "<form action='process.php'>";
		echo "<input type=hidden name=page value='${pageno}'>\n";
		echo "<input type=hidden name=tab value='${tabno}'>\n";
		echo "<input type=hidden name=op value=editAddressFromObject>";
		echo "<input type=hidden name=object_id value='$object_id'>";
		echo "<input type=hidden name=ip value='${addr['ip']}'>";
		echo "<tr class='$class'><td><a href='process.php?op=delAddrFObj&page=${pageno}&tab=${tabno}&ip=${addr['ip']}&object_id=$object_id'>";
		printImageHREF ('delete', 'Delete this IPv4 address');
		echo "</a></td>";
		echo "<td><input type='text' name='bond_name' value='${addr['name']}' size=10></td>";
		echo "<td><a href='${root}?page=ipaddress&ip=${addr['ip']}'>${addr['ip']}</a></td>";
		echo "<td class='description'>$address_name</td>\n";
		echo "<td><select name='bond_type'>";
		foreach (array('regular'=>'Regular', 'virtual'=>'Virtual', 'shared'=>'Shared') as $n => $v)
		{
			echo "<option value='$n'";
			if ($addr['type'] == $n)
				echo " selected";
			echo ">$v</option>";
		}
		echo "</td><td>";
		if ($addr['address_reserved']=='yes')
			echo "<b>Reserved</b>; ";

		if ($addr['type'] == 'virtual')
		{
			if ($notvirtnum > 0)
			{
				echo " Owners: ";
				printRefsOfType($addr['references'], 'virtual', 'neq');
			}
		}
		elseif ($addr['type'] == 'shared')
		{
			if ($sharednum > 0)
			{
				echo " Peers: ";
				printRefsOfType($addr['references'], 'shared', 'eq');
				echo ";";
			}
			if ($virtnum > 0)
			{
				echo " Virtuals: ";
				printRefsOfType($addr['references'], 'virtual', 'eq');
				echo ";";
			}
			if ($regnum > 0)
			{
				echo " Collisions: ";
				printRefsOfType($addr['references'], 'regular', 'eq');
			}
			
		}
		else
		{
			if ($virtnum > 0)
			{
				echo " Virtuals: ";
				printRefsOfType($addr['references'], 'virtual', 'eq');
				echo ";";
			}
			if ($notvirtnum > 0)
			{
				echo " Collisions: ";
				printRefsOfType($addr['references'], 'virtual', 'neq');
			}
		}

		echo "</td><td><input type=submit value='OK'></td></form></tr>\n";
	}


	echo "<form action='${root}process.php'><tr><td colspan=2><input type='text' size='10' name='name' tabindex=100></td>\n";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=op value=addAddrFObj>\n";
	echo "<input type=hidden name=object_id value='$object_id'>\n";

	echo "<td><input type=text name='ip' tabindex=101>\n";
	echo "</td><td><select name='type' tabindex=102>";
	echo "<option value='regular'>Regular</option>";
	echo "<option value='virtual'>Virtual</option>";
	echo "<option value='shared'>Shared</option>";
	echo "</select>";
	echo "</td><td colspan=3><input type='submit' value='Add a new interface' tabindex=103></td></tr></form>";
	echo "</table><br>\n";
	finishPortlet();

}

function printLog ($log)
{
	foreach ($log as $record)
		echo "<div class=msg_${record['code']}>${record['message']}</div>";
}

/*
The following conditions must be followed:
1. We can mount onto free atoms only. This means: if any record for an atom
already exists in RackSpace, it can't be used for mounting.
2. We can't unmount from 'W' atoms. Operator should review appropriate comments
and either delete them before unmounting or refuse to unmount the object.
*/

// We extensively use $_REQUEST in the function.
function renderRackSpaceForObject ($object_id = 0)
{
	if ($object_id <= 0)
	{
		showError ('Invalid object_id in renderRackSpaceForObject()');
		return;
	}
	$is_submit = isset ($_REQUEST['got_atoms']);
	$is_update = isset ($_REQUEST['rackmulti'][0]);
	$info = getObjectInfo ($object_id);
	if ($info == NULL)
	{
		showError ('getObjectInfo() failed in renderRackSpaceForObject()');
		return;
	}
	// Always process occupied racks plus racks chosen by user. First get racks with
	// already allocated rackspace...
	$workingRacksData = getResidentRacksData ($object_id);
	if ($workingRacksData === NULL)
	{
		print_r ($workingRacksData);
		showError ('getResidentRacksData() failed in renderRackSpaceForObject()');
		return;
	}

	// ...and then add those chosen by user (if any).
	if ($is_update)
		foreach ($_REQUEST['rackmulti'] as $cand_id)
		{
			if (!isset ($workingRacksData[$cand_id]))
			{
				$rackData = getRackData ($cand_id);
				if ($rackData == NULL)
				{
					showError ('getRackData() failed in renderRackSpaceForObject()');
					return NULL;
				}
				$workingRacksData[$cand_id] = $rackData;
			}
		}

	// Do it only once...
	foreach ($workingRacksData as &$rackData)
		applyObjectMountMask ($rackData, $object_id);
	// Now we workaround an old caveat: http://bugs.php.net/bug.php?id=37410
	unset ($rackData);

	// Here we process form submit by trying to save all submitted info to database.
	if ($is_submit)
	{
		$oldMolecule = getMoleculeForObject ($object_id);
		$worldchanged = FALSE;
		$log = array();
		foreach ($workingRacksData as $rack_id => $rackData)
		{
			$logrecord = processGridForm ($rackData, 'F', 'T', $object_id);
			$log[] = $logrecord;
			if ($logrecord['code'] != 300)
			{
				$worldchanged = TRUE;
				// Reload our working copy after form processing.
				$rackData = getRackData ($rack_id);
				if ($rackData == NULL)
					$log[] = array ('code' => 500, 'message' => 'Working copy update failed in renderRackSpaceForObject()');
				applyObjectMountMask ($rackData, $object_id);
				$workingRacksData[$rack_id] = $rackData;
			}
		}
		if ($worldchanged)
		{
			// Log a record.
			$newMolecule = getMoleculeForObject ($object_id);
			$oc = count ($oldMolecule);
			$nc = count ($newMolecule);
			$omid = $oc ? createMolecule ($oldMolecule) : 'NULL';
			$nmid = $nc ? createMolecule ($newMolecule) : 'NULL';
			global $remote_username;
			$comment = empty ($_REQUEST['comment']) ? 'NULL' : "'${_REQUEST['comment']}'";
			$query =
				"insert into MountOperation(object_id, old_molecule_id, new_molecule_id, user_name, comment) " .
				"values (${object_id}, ${omid}, ${nmid}, '${remote_username}', ${comment})";
			global $dbxlink;
			$result = $dbxlink->query ($query);
			if ($result == NULL)
				$log[] = array ('code' => 'error', 'message' => 'SQL query failed during history logging.');
			else
				$log[] = array ('code' => 'success', 'message' => 'History logged.');
		}
		printLog ($log);
	}

	// This is the time for rendering.
	global $root, $pageno, $tabno;
	echo "<form id='racks' action='${root}'>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=object_id value='${object_id}'>\n";
	// Main layout starts.
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0><tr>";

	// Left portlet with rack list.
	echo "<td class=pcleft height='1%'>";
	startPortlet ('Racks');
	$allRacksData = getRacksForRow();
	renderRackMultiSelect ('rackmulti[]', $allRacksData, array_keys ($workingRacksData));
	echo "<br>";
	echo "<br>";
	finishPortlet();
	echo "</td>";

	// Middle portlet with comment and submit.
	echo "<td class=pcleft>";
	startPortlet ('Comment');
	echo "<textarea name=comment rows=10 cols=40></textarea><br>\n";
	echo "<input type=submit value='Save' name=got_atoms>\n";
	echo "<br>";
	echo "<br>";
	finishPortlet();
	echo "</td>";

	// Right portlet with rendered racks. If this form submit is not final, we have to
	// reflect the former state of the grid in current form.
	echo "<td class=pcright rowspan=2 height='1%'>";
	startPortlet ('Working copy');
	echo '<table border=0 cellspacing=10 align=center><tr>';
	foreach ($workingRacksData as $rack_id => $rackData)
	{
		// Order is important here: only original allocation is highlighted.
		highlightObject ($rackData, $object_id);
		markupAtomGrid ($rackData, 'T');
		// If we have a form processed, discard user input and show new database
		// contents.
		if (!$is_submit and $is_update)
			mergeGridFormToRack ($rackData);
		echo "<td valign=top>";
		echo "<center>\n<h2>${rackData['name']}</h2>\n";
		echo "<table class=rack border=0 cellspacing=0 cellpadding=1>\n";
		echo "<tr><th width='10%'>&nbsp;</th><th width='20%'>Front</th>";
		echo "<th width='50%'>Interior</th><th width='20%'>Back</th></tr>\n";
		renderAtomGrid ($rackData);
		echo "<tr><th width='10%'>&nbsp;</th><th width='20%'>Front</th>";
		echo "<th width='50%'>Interior</th><th width='20%'>Back</th></tr>\n";
		echo "</table></center>\n";
		echo '</td>';
	}
	echo "</tr></table>";
	finishPortlet();
	echo "</td>\n";

	echo "</form>\n";
	echo "</tr></table>\n";
}

function renderMolecule ($mdata, $object_id)
{
	// sort data out
	$rackpack = array();
	global $loclist;
	foreach ($mdata as $dummy => $rua)
	{
		$rack_id = $rua['rack_id'];
		$unit_no = $rua['unit_no'];
		$atom = $rua['atom'];
		if (!isset ($rackpack[$rack_id]))
		{
			$rackData = getRackData ($rack_id);
			for ($i = $rackData['height']; $i > 0; $i--)
				for ($locidx = 0; $locidx < 3; $locidx++)
					$rackData[$i][$locidx]['state'] = 'F';
			$rackpack[$rack_id] = $rackData;
		}
		$rackpack[$rack_id][$unit_no][$loclist[$atom]]['state'] = 'T';
		$rackpack[$rack_id][$unit_no][$loclist[$atom]]['object_id'] = $object_id;
	}
	// now we have some racks to render
	foreach ($rackpack as $dummy => $rackData)
	{
		markAllSpans ($rackData);
		echo "<table class=molecule cellspacing=0>\n";
		echo "<caption>${rackData['name']}</caption>\n";
		echo "<tr><th width='10%'>&nbsp;</th><th width='20%'>Front</th><th width='50%'>Interior</th><th width='20%'>Back</th></tr>\n";
		for ($i = $rackData['height']; $i > 0; $i--)
		{
			echo "<tr><th>$i</th>";
			for ($locidx = 0; $locidx < 3; $locidx++)
			{
				$state = $rackData[$i][$locidx]['state'];
				echo "<td class=state_${state}>&nbsp;</td>\n";
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
	}
}

function renderUnmountedObjectsPortlet ()
{
	startPortlet ('Unmounted objects');
	$objs = getUnmountedObjects();
	if ($objs === NULL)
	{
		showError ('getUnmountedObjects() failed in renderUnmountedObjectsPortlet()');
		return;
	}
	global $root, $nextorder;
	$order = 'odd';
	echo '<br><br><table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
	echo '<tr><th>Common name</th><th>Visible label</th><th>Asset number</th><th>Barcode</th></tr>';
	foreach ($objs as $obj)
	{
		echo "<tr class=row_${order}><td><a href='${root}?page=object&object_id=${obj['id']}'>${obj['dname']}</a></td>";
		echo "<td>${obj['label']}</td>";
		echo "<td>${obj['asset_no']}</td>";
		echo "<td>${obj['barcode']}</td></tr>";
		$order = $nextorder[$order];
	}
	echo "</table><br>\n";
	finishPortlet();
}

function renderProblematicObjectsPortlet ()
{
	startPortlet ('Problematic objects');
	$objs = getProblematicObjects();
	if ($objs === NULL)
	{
		showError ('getProblematicObjects() failed in renderProblematicObjectsPortlet()');
		return;
	}
	global $root, $nextorder;
	$order = 'odd';
	echo '<br><br><table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
	echo '<tr><th>Type</th><th>Common name</th></tr>';
	foreach ($objs as $obj)
	{
		echo "<tr class=row_${order}><td>${obj['objtype_name']}</td>";
		echo "<td><a href='${root}?page=object&object_id=${obj['id']}'>${obj['dname']}</a></tr>";
		$order = $nextorder[$order];
	}
	echo "</table><br>\n";
	finishPortlet();
}

function renderObjectGroupSummary ()
{
	global $root;
	$summary = getObjectGroupInfo();
	if ($summary === NULL)
	{
		showError ('getObjectGroupInfo() failed in renderObjectGroupSummary()');
		return;
	}
	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft width='25%'>";

	startPortlet ('Summary');
	foreach ($summary as $gi)
	{
		echo "<a href='${root}?page=objgroup&group_id=${gi['id']}'><b>${gi['name']}</b></a> <i>(${gi['count']})</i><br>";
	}
	finishPortlet();

	echo '</td><td class=pcright>';
	renderUnmountedObjectsPortlet();
	echo '</td><td class=pcright>';
	renderProblematicObjectsPortlet();
	echo "</td></tr></table>\n";
}

function renderObjectGroup ($group_id = 0)
{
	global $root;
	$summary = getObjectGroupInfo();
	if ($summary == NULL)
	{
		showError ('getObjectGroupInfo() failed in renderObjectGroup()');
		return;
	}
	$objects = getObjectList ($group_id);
	if ($objects === NULL)
	{
		showError ('getObjectList() failed in renderObjectGroup()');
		return;
	}
	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft width='25%'>";

	startPortlet ('All objects');
	foreach ($summary as $gi)
	{
		echo "<a href='${root}?page=objgroup&group_id=${gi['id']}'><b>${gi['name']}</b></a> <i>(${gi['count']})</i><br>";
	}
	finishPortlet();

	echo '</td><td class=pcright>';

	startPortlet ('Object group');
	echo '<br><br><table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
	echo '<tr><th>Common name</th><th>Visible label</th><th>Asset tag</th><th>Barcode</th><th>Rack</th></tr>';
	$order = 'odd';
	global $nextorder;
	foreach ($objects as $obj)
	{
		echo "<tr class=row_${order}><td><a href='${root}?page=object&object_id=${obj['id']}'>${obj['dname']}</a></td>";
		echo "<td>${obj['label']}</td>";
		echo "<td>${obj['asset_no']}</td>";
		echo "<td>${obj['barcode']}</td>";
		if ($obj['rack_id'])
			echo "<td><a href='${root}?page=rack&rack_id=${obj['rack_id']}'>${obj['Rack_name']}</a></td>";
		else
			echo '<td>Unmounted</td>';
		echo '</tr>';
		$order = $nextorder[$order];
	}
	echo '</table>';
	finishPortlet();

	echo "</td></tr></table>";
}

function renderEmptyPortsSelect ($port_id, $type_id)
{
	$ports = getEmptyPortsOfType($type_id);
	usort($ports, 'sortEmptyPorts');
	foreach ($ports as $port)
	{
		if ($port_id == $port['Port_id'])
			continue;
		echo "<option value='${port['Port_id']}' onclick='getElementById(\"remote_port_name\").value=\"${port['Port_name']}\"; getElementById(\"remote_object_name\").value=\"${port['Object_name']}\";'>${port['Object_name']} ${port['Port_name']}</option>\n";
	}
}

function renderObjectAddressesAndNames ()
{
	$addresses = getObjectAddressesAndNames();
	usort($addresses, 'sortObjectAddressesAndNames');
	foreach ($addresses as $address)
	{
		echo "<option value='${address['ip']}' onclick='getElementById(\"ip\").value=\"${address['ip']}\";'>${address['object_name']} ${address['name']} ${address['ip']}</option>\n";
	}
}

// History viewer for history-enabled simple dictionaries.
function renderHistory ($object_type, $object_id)
{
	switch ($object_type)
	{
		case 'row':
			$query = "select ctime, user_name, name, deleted, comment from RackRowHistory where id = ${object_id} order by ctime";
			$header = '<tr><th>change time</th><th>author</th><th>rack row name</th><th>is deleted?</th><th>rack row comment</th></tr>';
			$extra = 4;
			break;
		case 'rack':
			$query =
				"select ctime, user_name, rh.name, rh.deleted, d.dict_value as name, rh.height, rh.comment " .
				"from RackHistory as rh left join Dictionary as d on rh.row_id = d.dict_key " .
				"natural join Chapter " .
				"where chapter_name = 'RackRow' and rh.id = ${object_id} order by ctime";
			$header = '<tr><th>change time</th><th>author</th><th>rack name</th><th>is deleted?</th><th>rack row name</th><th>rack height</th><th>rack comment</th></tr>';
			$extra = 6;
			break;
		case 'object':
			$query =
				"select ctime, user_name, name, label, barcode, asset_no, deleted, has_problems, dict_value, comment " .
				"from RackObjectHistory inner join Dictionary on objtype_id = dict_key natural join Chapter " .
				"where chapter_name = 'RackObjectType' and id=${object_id} order by ctime";
			$header = '<tr><th>change time</th><th>author</th><th>common name</th><th>visible label</th><th>barcode</th><th>asset no</th><th>is deleted?</th><th>has problems?</th><th>object type</th><th>comment</th></tr>';
			$extra = 9;
			break;
		default:
			showError ("Uknown object type '${object_type}' in renderHistory()");
			return;
	}
	global $dbxlink;
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed in renderHistory()');
		return;
	}
	echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
	$order = 'odd';
	global $nextorder;
	echo $header;
	while ($row = $result->fetch (PDO::FETCH_NUM))
	{
		echo "<tr class=row_${order}><td>${row[0]}</td>";
		for ($i = 1; $i <= $extra; $i++)
			echo "<td>" . $row[$i] . "</td>";
		echo "</tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table><br>\n";
}

function renderRackspaceHistory ()
{
	global $root, $nextorder, $pageno, $tabno;
	$order = 'odd';
	$history = getRackspaceHistory();
	// Show the last operation by default.
	if (isset ($_REQUEST['op_id']))
		$op_id = $_REQUEST['op_id'];
	elseif (isset ($history[0]['mo_id']))
		$op_id = $history[0]['mo_id'];
	else $op_id = NULL;
	
	$omid = NULL;
	$nmid = NULL;
	$object_id = 1;
	if ($op_id)
		list ($omid, $nmid) = getOperationMolecules ($op_id);

	// Main layout starts.
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";

	// Left top portlet with old allocation.
	echo "<tr><td class=pcleft>";
	startPortlet ('Old allocation');
	if ($omid)
	{
		$oldMolecule = getMolecule ($omid);
		renderMolecule ($oldMolecule, $object_id);
	}
	else
		echo "nothing";
	finishPortlet();

	echo '</td><td class=pcright>';

	// Right top portlet with new allocation
	startPortlet ('New allocation');
	if ($nmid)
	{
		$newMolecule = getMolecule ($nmid);
		renderMolecule ($newMolecule, $object_id);
	}
	else
		echo "nothing";
	finishPortlet();
	
	echo '</td></tr><tr><td colspan=2>';
	
	// Bottom portlet with list

	startPortlet ('Rackspace allocation history');
	echo "<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>\n";
	echo "<tr><th>timestamp</th><th>author</th><th>rack object ID</th><th>rack object type</th><th>rack object name</th><th>comment</th></tr>\n";
	foreach ($history as $row)
	{
		if ($row['mo_id'] == $op_id)
			$class = 'hl';
		else
			$class = "row_${order}";
		echo "<tr class=${class}><td><a href='${root}?page=${pageno}&tab=${tabno}&op_id=${row['mo_id']}'>${row['ctime']}</a></td>";
		echo "<td>${row['user_name']}</td>";
		echo "<td>${row['ro_id']}</td><td>${row['objtype_name']}</td><td>${row['name']}</td><td>${row['comment']}</td>\n";
		echo "</tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table>\n";
	finishPortlet();
	
	echo '</td></tr></table>';
	
}

function renderAddressspace ()
{
	global $root;
	echo "<table class='widetable' border=0 cellpadding=10 cellspacing=0 align='center'>\n";
	$addrspaceList = getAddressspaceList();
	echo "<tr><th>Address range</th><th>Name</th><th>Total/used addresses</th></tr>";
	foreach ($addrspaceList as $iprange)
	{
		echo "<tr><td><a href='${root}?page=iprange&id=${iprange['id']}'>${iprange['ip']}/${iprange['mask']}</a></td><td>${iprange['name']}</td><td>";
		echo ($iprange['ip_bin'] | $iprange['mask_bin_inv']) - ($iprange['ip_bin'] & $iprange['mask_bin'])+1;
		$range = getIPRange($iprange['id']);
		echo "/";
		echo count($range['addrlist']);
		echo "</td></tr>";
	}
	echo "</table>\n";
}

function renderAddNewRange ()
{
	global $root, $pageno, $tabno;
	showMessageOrError();

	echo "<center><h2>Add New Range</h2></center>\n";
	echo "<table class='widetable' border=0 cellpadding=10 align='center'>\n";
	echo "<tr><th>Address range</th><th>Name</th><th>C&gt;*</th><th>&nbsp;</th></tr>\n";
	echo "<form name='add_new_range' action='process.php'>\n";
	echo "<input type=hidden name=op value=addRange>\n";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<tr><td class='tdcenter'><input type=text name='range' size=18 class='live-validate'></td>\n";
	echo "<td class='tdcenter'><input type=text name='name' size='20'></td>\n";
	echo "<td class='tdcenter'><input type=checkbox name='is_bcast' checked></td>\n";
	echo "<td class='tdcenter'><input type=submit value='Add a new range'></td></tr>\n";
	echo "</form></table><br><br>\n";

	echo "<center><h2>Existing Ranges</h2></center>\n";
	echo "<table class='widetable' border=0 cellpadding=10 align='center'>\n";
	$addrspaceList = getAddressspaceList();
	echo "<tr><th>&nbsp;</th><th>Address range</th><th>Name</th><th>Total/used addresses</th></tr>";
	foreach ($addrspaceList as $iprange)
	{
		$range = getIPRange($iprange['id']);
		$usedips = count ($range['addrlist']);
		echo "<tr>";
		if ($usedips == 0)
		{
			echo "<td><a href='process.php?op=delRange&page=${pageno}&tab=${tabno}&id=${iprange['id']}'>";
			printImageHREF ('delete', 'Delete this IP range');
			echo "</a></td>\n";
		}
		else
			echo "<td>&nbsp</td>";
		echo "<td><a href='${root}?page=iprange&id=${iprange['id']}'>${iprange['ip']}/${iprange['mask']}</a></td><td>${iprange['name']}</td><td>";
		echo ($iprange['ip_bin'] | $iprange['mask_bin_inv']) - ($iprange['ip_bin'] & $iprange['mask_bin'])+1;
		echo "/";
		echo $usedips;
		#echo "</td></tr></table>";
		echo "</td></tr>";
	}
}

function renderIPRange ()
{
	global $root;
	$maxperpage = getConfigVar ('IPV4_ADDRS_PER_PAGE');
	$id = $_REQUEST['id'];
	if (isset($_REQUEST['pg']))
		$page = $_REQUEST['pg'];
	else
		$page=0;

	$range = getIPRange($id);
	echo "<center><h1>${range['ip']}/${range['mask']}</h1><h2>${range['name']}</h2></center>\n";

	$startip = $range['ip_bin'] & $range['mask_bin'];
	$endip = $range['ip_bin'] | $range['mask_bin_inv'];
	$realstartip = $startip;
	$realendip = $endip;
	$numpages = 0;
	if($endip - $startip > $maxperpage)
	{
		$numpages = ($endip - $startip)/$maxperpage;
		$startip = $startip + $page * $maxperpage;
		$endip = $startip + $maxperpage-1;
	}
	echo "<center>";
	if ($numpages)
		echo '<h3>' . long2ip ($startip) . ' ~ ' . long2ip ($endip) . '</h3>';
	for ($i=0; $i<$numpages; $i++)
	{
		if ($i == $page)
			echo "<b>$i</b> ";
		else
			echo "<a href='${root}?page=iprange&id=$id&pg=$i'>$i</a> ";
	}
	echo "</center>";

	echo "<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center'>\n";
	echo "<tr><th>Address</th><th>Name</th><th>Allocation</th></tr>\n";


	for($ip = $startip; $ip<=$endip; $ip++)
	{
		if (isset ($range['addrlist'][$ip]))
		{
			$numshared = countRefsOfType($range['addrlist'][$ip]['references'], 'shared', 'eq');
			$numreg = countRefsOfType($range['addrlist'][$ip]['references'], 'regular', 'eq');
			$numvirt = countRefsOfType($range['addrlist'][$ip]['references'], 'virtual', 'eq');
			
			$addr = $range['addrlist'][$ip];
			if ( ($numshared > 0 && $numreg > 0) || $numreg > 1 )
				echo "<tr class='trwarning'>";
			elseif ( $addr['reserved'] == 'yes' and $numshared+$numreg+$numvirt > 0)
				echo "<tr class='trwarning'>";
			elseif ( $addr['reserved'] == 'yes')
				echo "<tr class='trbusy'>";
			elseif ( $numshared > 0 || $numreg > 0)
				echo "<tr class='trbusy'>";
			else
				echo "<tr>";

			echo "<td><a href='${root}?page=ipaddress&ip=${addr['ip']}'>${addr['ip']}</a></td><td>${addr['name']}</td><td>";
			if ( $addr['reserved'] == 'yes')
				echo "<b>Reserved;</b> ";
			foreach ($range['addrlist'][$ip]['references'] as $ref)
			{
				echo "<a href='${root}?page=object&object_id=${ref['object_id']}'>";
				echo $ref['name'] . (empty ($ref['name']) ? '' : '@');
				echo "${ref['object_name']}</a>; ";
			}
			echo "</td></tr>\n";
		}
		else
		{
			echo "<tr><td><a href='${root}?page=ipaddress&ip=".long2ip($ip)."'>".long2ip($ip)."</a></td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
		}
	}

	echo "</table>";
	
}

function renderIPRangeProperties ()
{
	global $pageno, $tabno;
	$id = $_REQUEST['id'];
	showMessageOrError();
	$range = getIPRange($id);
	echo "<center><h1>${range['ip']}/${range['mask']}</h1></center>\n";
	echo "<table border=0 cellpadding=10 cellpadding=1 align='center'>\n";
	echo "<form action='process.php'><input type=hidden name=op value=editRange>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=id value='${id}'>";
	echo "<tr><td class='tdright'>Name:</td><td class='tdleft'><input type=text name=name size=20 value='${range['name']}'></tr><tr><td colspan=2 class='tdcenter'><input type=submit value='Update range'></td></form></tr>";
	echo "</table>\n";

}

function renderIPAddress ()
{
	global $root;
	$ip = $_REQUEST['ip'];
	$address = getIPAddress($ip);
	echo "<center><h1>$ip</h1>";
	if ($address['exists'] == 1)
		echo "<h2>${address['name']}</h2>";
	echo "</center>\n";

//	echo "<table width='100%' cesspadding=5 cellspacing=0 border=0 align='center'>";
//	echo "<tr valign='top'><td>";

	startPortlet ('Address assignment');
	echo "<table class='widetable' cesspadding=5 cellspacing=0 border=0 align='center'>\n";
	echo "<tr><th>Object name</th><th>Interface name</th><th>Interface type</th></tr>\n";

	$numshared = countRefsOfType($address['bonds'], 'shared', 'eq');
	$numreg = countRefsOfType($address['bonds'], 'regular', 'eq');
	$numvirt = countRefsOfType($address['bonds'], 'virtual', 'eq');

	
	if ( ($numshared > 0 && $numreg > 0) || $numreg > 1 )
		$class='trwarning';
	elseif ( $address['reserved'] == 'yes' and $numshared+$numreg+$numvirt > 0)
		$class='trwarning';
	else
		$class='';



	if ($address['reserved'] == 'yes')
		echo "<tr class='$class'><td colspan='3'><b>RESERVED</b></td></tr>";
	foreach ($address['bonds'] as $bond)
	{
		echo "<tr class='$class'><td><a href='${root}?page=object&object_id=${bond['object_id']}'>${bond['object_name']}</td><td>${bond['name']}</td><td><b>";
		switch ($bond['type'])
		{
			case 'virtual':
				echo "Virtual";
				break;
			case 'shared':
				echo "Shared";
				break;
			case 'regular':
				echo "Regular";
				break;
		}
		echo "</b></td></tr>\n";
	}
	echo "</table><br><br>";
	finishPortlet();

//	echo "</td><td>";
//	echo "</td></tr></table>";
}

function renderIPAddressProperties ()
{
	global $pageno, $tabno;
	$ip = $_REQUEST['ip'];
	showMessageOrError();
	$address = getIPAddress($ip);
	echo "<center><h1>$ip</h1></center>\n";
	echo "<table border=0 cellpadding=10 cellpadding=1 align='center'>\n";
	echo "<form action='process.php'><input type=hidden name=op value=editAddress>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=ip value='${ip}'>";
	echo "<tr><td class='tdright'>Name:</td><td class='tdleft'><input type=text name=name size=20 value='".($address['exists']==1?$address['name']:'')."'></tr>";
	echo "<td class='tdright'>Reserved:</td><td class='tdleft'><input type=checkbox name=reserved size=20 ".($address['exists']==1?(($address['reserved']=='yes')?'checked':''):'')."></tr>";
	echo "<tr><td colspan=2 class='tdcenter'><input type=submit value='Update address'></td></form></tr>";
	echo "</table>\n";

}

function renderIPAddressAssignment ()
{
	global $pageno, $tabno, $root;
	$ip = $_REQUEST['ip'];
	$address = getIPAddress($ip);

	showMessageOrError();
	echo "<center><h1>$ip</h1></center>\n";


	echo "<table class='widetable' cesspadding=5 cellspacing=0 border=0 align='center'>\n";
	echo "<tr><th>&nbsp;</th><th>Object name</th><th>Interface name</th><th>Interface type</th><th>&nbsp;</th></tr>\n";

	$numshared = countRefsOfType($address['bonds'], 'shared', 'eq');
	$numreg = countRefsOfType($address['bonds'], 'regular', 'eq');
	$numvirt = countRefsOfType($address['bonds'], 'virtual', 'eq');

	
	if ( ($numshared > 0 && $numreg > 0) || $numreg > 1 )
		$class='trwarning';
	elseif ( $address['reserved'] == 'yes' and $numshared+$numreg+$numvirt > 0)
		$class='trwarning';
	else
		$class='';



	if ($address['reserved'] == 'yes')
		echo "<tr class='$class'><td colspan='5'><b>RESERVED</b></td></tr>";
	foreach ($address['bonds'] as $bond)
	{
		echo "<tr class='$class'><form action='process.php'>";
		echo "<input type=hidden name=op value='editBondForAddress'>";
		echo "<input type=hidden name=page value='${pageno}'>";
		echo "<input type=hidden name=tab value='${tabno}'>";
		echo "<input type=hidden name=ip value='$ip'>";
		echo "<input type=hidden name=object_id value='${bond['object_id']}'>";
		echo "<td><a href='process.php?op=delIpAssignment&page=${pageno}&tab=${tabno}&ip=$ip&object_id=${bond['object_id']}'>";
		printImageHREF ('delete', 'Unallocate address');
		echo "</a></td>";
		echo "<td><a href='${root}?page=object&object_id=${bond['object_id']}'>${bond['object_name']}</td>";
		echo "<td><input type='text' name='bond_name' value='${bond['name']}' size=10></td>";
		echo "<td><select name='bond_type'>";
		switch ($bond['type'])
		{
			case 'virtual':
				echo "<option value='regular'>Regular</option>";
				echo "<option value='virtual' selected>Virtual</option>";
				echo "<option value='shared'>Shared</option>";
				break;
			case 'shared':
				echo "<option value='regular'>Regular</option>";
				echo "<option value='virtual'>Virtual</option>";
				echo "<option value='shared' selected>Shared</option>";
				break;
			case 'regular':
				echo "<option value='regular' selected>Regular</option>";
				echo "<option value='virtual'>Virtual</option>";
				echo "<option value='shared'>Shared</option>";
				break;
		}
		echo "</select></td><td><input type='submit' value='OK'></td></form></tr>\n";
	}
	echo "<form action='process.php'><input type='hidden' name='op' value='bindObjectToIp'>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type='hidden' name='ip' value='$ip'>";
	echo "<td colspan=2><select name='object_id'>";

	foreach (array(1, 4, 7, 8, 12, 14) as $type) 
	{
		//get all Balck Boxes, Servers, Routers, Switches, UPS, Modems
		$objects = getObjectList($type);
		foreach ($objects as $object)
			echo "<option value='${object['id']}'>${object['dname']}</option>";
	}

	echo "</select></td><td><input type='text' name='bond_name' value='' size=10></td>";
	echo "<td><select name='bond_type'><option value='regular'>Regular</option><option value='virtual'>Virtual</option><option value='shared'>Shared</option></select></td>";
	echo "<td><input type='submit' value='Assign address'></td></form></tr>";
	echo "</table><br><br>";

}

function renderIPAddressPortForwarding ($object_id=0)
{
	global $pageno, $tabno, $root;
	
	$info = getObjectInfo ($object_id);
	$forwards = getObjectForwards ($object_id);
	$addresses = getObjectAddresses ($object_id);
	showMessageOrError();
	echo "<center><h2>locally performed NAT</h2></center>";

	echo "<table class='widetable' cesspadding=5 cellspacing=0 border=0 align='center'>\n";
	echo "<tr><th></th><th>Match endpoint</th><th>Translate to</th><th>Target object</th><th>Comment</th></tr>\n";

	foreach ($forwards['out'] as $pf)
	{
		$class='trwarning';
		$name='';
		foreach ($addresses as $addr)
			if ($addr['ip'] == $pf['localip'])
			{
				$class='';
				$name = $addr['name'];
				break;
			}

		echo "<tr class='$class'>";
		echo "<td><a href='process.php?op=delPortForwarding&localip=${pf['localip']}&localport=${pf['localport']}&remoteip=${pf['remoteip']}&remoteport=${pf['remoteport']}&proto=${pf['proto_bin']}&object_id=$object_id&page=${pageno}&tab=${tabno}'><img src='${root}/pix/delete_s.gif' title='Delete port forwarding' border=0 width=16 height=16></a></td>";
		echo "<td>${pf['proto']}/${name}: <a href='${root}?page=ipaddress&tab=default&ip=${pf['localip']}'>${pf['localip']}</a>:${pf['localport']}";
		if (!empty ($pf['local_addr_name']))
			echo ' (' . $pf['local_addr_name'] . ')';
		echo "</td>";
		echo "<td><a href='${root}?page=ipaddress&tab=default&ip=${pf['remoteip']}'>${pf['remoteip']}</a>:${pf['remoteport']}</td>";

		$address=getIPAddress($pf['remoteip']);

		echo "<td class='description'>";
		if (count ($address['bonds']))
			foreach($address['bonds'] as $bond)
				echo "<a href='${root}?page=object&tab=default&object_id=${bond['object_id']}'>${bond['object_name']}(${bond['name']})</a> ";
		elseif (!empty ($pf['remote_addr_name']))
			echo '(' . $pf['remote_addr_name'] . ')';
		echo "</td><form action='process.php'><input type='hidden' name='op' value='updPortForwarding'><input type=hidden name=page value='${pageno}'><input type=hidden name=tab value='${tabno}'><input type='hidden' name='object_id' value='$object_id'><input type='hidden' name='localip' value='${pf['localip']}'><input type='hidden' name='localport' value='${pf['localport']}'><input type='hidden' name='remoteip' value='${pf['remoteip']}'><input type='hidden' name='remoteport' value='${pf['remoteport']}'><input type='hidden' name='proto' value='${pf['proto_bin']}'><td class='description'><input type='text' name='description' value='${pf['description']}'> <input type='submit' value='OK'></td></form>";
		echo "</tr>";
	}
	echo "<form action='process.php'><input type='hidden' name='op' value='forwardPorts'>";
	echo "<input type='hidden' name='object_id' value='$object_id'>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<tr align='center'><td colspan=2><select name='proto'><option value='1'>TCP</option><option value='2'>UDP</option></select><select name='localip'>";

	foreach ($addresses as $addr)
		echo "<option value='${addr['ip']}'>" . (empty ($addr['name']) ? '' : "${addr['name']}: ") .
			"${addr['ip']}" . (empty ($addr['address_name']) ? '' : " (${addr['address_name']})") . "</option>";

	echo "</select>:<input type='text' name='localport' size='4'></td><td><input type='text' name='remoteip' id='remoteip' size='10'>";
	echo "<a href='javascript:;' onclick='window.open(\"${root}/find_object_ip_helper.php\", \"findobjectip\", \"height=700, width=400, location=no, menubar=no, resizable=yes, scrollbars=no, status=no, titlebar=no, toolbar=no\");'><img src='${root}/pix/find.png' title='Find object' border=0 height=16 width=16></a>";
	echo ":<input type='text' name='remoteport' size='4'></td><td></td><td colspan=1><input type='text' name='description' size='20'> <input type='submit' value='Create Forwarding'></td></tr>";
	echo "</form>";

	echo "</table><br><br>";


	echo "<center><h2>arriving NAT connections</h2></center>";
	echo "<table class='widetable' cesspadding=5 cellspacing=0 border=0 align='center'>\n";
	echo "<tr><th></th><th>Source</th><th>Source objects</th><th>Target</th><th>Description</th></tr>\n";

	foreach ($forwards['in'] as $pf)
	{
		echo "<tr>";

		echo "<td><a href='process.php?op=delPortForwarding&localip=${pf['localip']}&localport=${pf['localport']}&remoteip=${pf['remoteip']}&remoteport=${pf['remoteport']}&proto=${pf['proto_bin']}&object_id=${pf['object_id']}&page=${pageno}&tab=${tabno}'><img src='${root}/pix/delete_s.gif' title='Delete port forwarding' border=0 width=16 height=16></a></td>";
		echo "<td>${pf['proto']}/<a href='${root}?page=ipaddress&tab=default&ip=${pf['localip']}'>${pf['localip']}</a>:${pf['localport']}</td>";
		echo "<td class='description'><a href='${root}?page=object&tab=default&object_id=${pf['object_id']}'>${pf['object_name']}</a>";
		echo "</td><td><a href='${root}?page=ipaddress&tab=default&ip=${pf['remoteip']}'>${pf['remoteip']}</a>:${pf['remoteport']}</td>";
		echo "<td class='description'>${pf['description']}</td></tr>";
	}

//	echo "<form action='process.php'><input type='hidden' name='op' value='forwardPorts'>";
//	echo "<input type='hidden' name='object_id' value='$object_id'>";
//	echo "<input type=hidden name=page value='${pageno}'>\n";
//	echo "<input type=hidden name=tab value='${tabno}'>\n";
//	echo "<tr align='center'><td colspan=2><select name='proto'><option value='1'>TCP</option><option value='2'>UDP</option><input type='text' name='localip' size='10'>:<input type='text' name='localport' size='4'></td><td><select name='localip'>";
//	foreach ($addresses as $addr)
//		echo "<option value='${addr['ip']}'>${addr['ip']}</option>";
//
//	echo "</select>:<input type='text' name='remoteport' size='4'></td><td><input type='text' name='description' size='20'></td><td><input type='submit' value='Create Forwarding'></td></tr>";
//	echo "</form>";
	echo "</table><br><br>";


}


function renderAddMultipleObjectsForm ()
{
	global $pageno, $tabno, $nextorder;

	$type_id = array();
	$global_type_id = 0;
	$name = array();
	$asset_no = array();
	$keepvalues = FALSE;
	$log = array();
	// Look for current submit.
	if (isset ($_REQUEST['got_fast_data']))
	{
		$keepvalues = TRUE;
		$max = getConfigVar ('MASSCOUNT');
		for ($i = 0; $i < $max; $i++)
		{
			if (!isset ($_REQUEST["${i}_object_type_id"]))
			{
				$log[] = array ('code' => 'error', 'message' => "Submitted form is invalid at line " . $i + 1);
				break;
			}
			assertUIntArg ("${i}_object_type_id", TRUE);
			assertStringArg ("${i}_object_name", TRUE);
			assertStringArg ("${i}_object_label", TRUE);
			assertStringArg ("${i}_object_asset_no", TRUE);
			assertStringArg ("${i}_object_barcode", TRUE);
			$type_id[$i] = $_REQUEST["${i}_object_type_id"];
			// Save user input for possible rendering.
			$name[$i] = $_REQUEST["${i}_object_name"];
			$label[$i] = $_REQUEST["${i}_object_label"];
			$asset_no[$i] = $_REQUEST["${i}_object_asset_no"];
			$barcode[$i] = $_REQUEST["${i}_object_barcode"];

			// It's better to skip silently than printing a notice.
			if ($type_id[$i] == 0)
				continue;
			if (commitAddObject ($name[$i], $label[$i], $barcode[$i], $type_id[$i], $asset_no[$i]) === TRUE)
				$log[] = array ('code' => 'success', 'message' => "Added new object '${name[$i]}'");
			else
				$log[] = array ('code' => 'error', 'message' => 'commitAddObject() failed in renderAddMultipleObjectsForm()');
		}
	}
	elseif (isset ($_REQUEST['got_very_fast_data']))
	{
		$keepvalues = TRUE;
		assertUIntArg ('global_type_id', TRUE);
		assertStringArg ('namelist', TRUE);
		$global_type_id = $_REQUEST['global_type_id'];
		if ($global_type_id == 0)
		{
			if (!empty ($_REQUEST['namelist']))
				$log[] = array ('code' => 'error', 'message' => 'Object type is not selected, check the form below');
			else
				$log[] = array ('code' => 'error', 'message' => 'Empty form has been ignored. Cheers.');
		}
		else
		{
			// The name extractor below was stolen from ophandlers.php:addMultiPorts()
			$names1 = explode ('\n', $_REQUEST['namelist']);
			$names2 = array();
			foreach ($names1 as $line)
			{
				$parts = explode ('\r', $line);
				reset ($parts);
				if (empty ($parts[0]))
					continue;
				else
					$names2[] = rtrim ($parts[0]);
			}
			foreach ($names2 as $cname)
				if (commitAddObject ($cname, '', '', $global_type_id, '') === TRUE)
					$log[] = array ('code' => 'success', 'message' => "Added new object '${cname}'");
				else
					$log[] = array ('code' => 'error', 'message' => "Could not add '${cname}'");
		}
	}
	printLog ($log);

	// Render a form for the next.
	$typelist = getObjectTypeList();
	$typelist[0]['dict_key'] = 0;
	$typelist[0]['dict_value'] = 'select type...';

	startPortlet ('Fast way');
	echo '<form>';
	echo "<input type=hidden name=page value=${pageno}>";
	echo "<input type=hidden name=tab value=${tabno}>";
	echo '<table border=0 align=center>';
	echo "<tr><th>Object type</th><th>Common name</th><th>Visible label</th><th>Asset tag</th><th>Barcode</th></tr>\n";
	// If a user forgot to select object type on input, we keep his
	// previous input in the form.
	$max = getConfigVar ('MASSCOUNT');
	for ($i = 0; $i < $max; $i++)
	{
		echo '<tr><td>';
		printSelect ($typelist, "${i}_object_type_id", 0);
		echo '</td>';
		echo "<td><input type=text size=30 name=${i}_object_name";
		if ($keepvalues and $type_id[$i] == 0)
			echo " value='${name[$i]}'";
		echo "></td>";
		echo "<td><input type=text size=30 name=${i}_object_label";
		if ($keepvalues and $type_id[$i] == 0)
			echo " value='${label[$i]}'";
		echo "></td>";
		echo "<td><input type=text size=20 name=${i}_object_asset_no";
		if ($keepvalues and $type_id[$i] == 0)
			echo " value='${asset_no[$i]}'";
		echo "></td>";
		echo "<td><input type=text size=10 name=${i}_object_barcode";
		if ($keepvalues and $type_id[$i] == 0)
			echo " value='${barcode[$i]}'";
		echo "></td>";
		echo "</tr>\n";
	}
	echo "<tr><td class=submit colspan=5><input type=submit name=got_fast_data value='Create'></td></tr>\n";
	echo "</form></table>\n";
	finishPortlet();

	startPortlet ('Very fast way');
	echo '<form>';
	echo "<input type=hidden name=page value=${pageno}>";
	echo "<input type=hidden name=tab value=${tabno}>";
	echo 'For each line shown below create an object of type ';
	printSelect ($typelist, "global_type_id", 0);
	echo " <input type=submit name=got_very_fast_data value='Go!'><br>\n";
	echo "<textarea name=namelist cols=40 rows=25>\n";
	if ($keepvalues and $global_type_id == 0)
		echo $_REQUEST['namelist'];
	echo "</textarea></form>\n";
	finishPortlet();
}

function printGreeting ()
{
	global $remote_username, $accounts, $root;
	$account = $accounts[$remote_username];
	echo "Hello, ${account['user_realname']}. This is RackTables " . CODE_VERSION . ". Click <a href='${root}?logout'>here</a> to logout.";
}

function renderSearchResults ()
{
	global $remote_username, $root;
	$terms = trim ($_REQUEST['q']);
	if (empty ($terms))
	{
		showError ('Search string cannot be empty.');
		return;
	}
	if (!authorized ($remote_username, 'object', 'default'))
	{
		showError ('You are not authorized for viewing information about objects.');
		return;
	}
	// If we search for L2 address, we can either find one or find none.
	if
	(
		preg_match ('/^[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?$/i', $terms) or
		preg_match ('/^[0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f]$/i', $terms) or
		preg_match ('/^[0-9a-f][0-9a-f][0-9a-f][0-9a-f].[0-9a-f][0-9a-f][0-9a-f][0-9a-f].[0-9a-f][0-9a-f][0-9a-f][0-9a-f]$/i', $terms)
	)
	// Search for L2 address.
	{
		$result = searchByl2address ($terms);
		if ($result !== NULL)
		{
			echo "<script language='Javascript'>document.location='${root}?page=object";
			echo "&hl_port_id=${result['port_id']}";
			echo "&object_id=${result['object_id']}';//</script>";
		}
		else
			echo "L2 address '${terms}' not found!";
	}
	elseif (preg_match ('/^[0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f]$/i', $terms))
	// STP bridge ID: bridge priotity + port MAC address. Cut off first 4 chars and look for MAC address.
	{
		$terms = substr ($terms, 4);
		$result = searchByl2address ($terms);
		if ($result !== NULL)
		{
			echo "<script language='Javascript'>document.location='${root}?page=object";
			echo "&hl_port_id=${result['port_id']}";
			echo "&object_id=${result['object_id']}';//</script>";
		}
		else
			echo "L2 address '${terms}' not found!";
	}
	elseif (preg_match ('/^[0-9][0-9]?[0-9]?\.[0-9]?[0-9]?[0-9]?\.[0-9][0-9]?[0-9]?\.[0-9]?[0-9]?[0-9]?$/i', $terms))
	// Search for IP address.
	{
		$result = getRangeByIp ($terms);
		if ($result !== NULL)
		{
			echo "<script language='Javascript'>document.location='${root}?page=ipaddress";
			echo "&ip=${terms}";
			echo "';//</script>";
		}
		else
			echo "IP address '${terms}' not found!";
		return;
	}
	else
	// Search for objects.
	{
		$objects = getSearchResults ($terms);
		if (count ($objects) == 1)
		{
			$obj = current ($objects);
			echo "<script language='Javascript'>document.location='${root}?page=object&object_id=${obj['id']}';//</script>";
		}
		elseif (count ($objects) > 1)
		{
			echo '<br><br><table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
			echo '<tr><th>Common name</th><th>Visible label</th><th>Asset tag</th><th>barcode</th></tr>';
			$order = 'odd';
			global $nextorder;
			foreach ($objects as $obj)
			{
				echo "<tr class=row_${order}><td><a href=\"${root}?page=object&object_id=${obj['id']}\">${obj['dname']}</a></td>";
				echo "<td>${obj['label']}</td>";
				echo "<td>${obj['asset_no']}</td>";
				echo "<td>${obj['barcode']}</td></tr>";
				$order = $nextorder[$order];
			}
			echo '</table>';
		}
		else
			echo "Object '${terms}' not found!";
	}
}

// This function prints a table of checkboxes to aid the user in toggling mount atoms
// from one state to another. The first argument is rack data as
// produced by getRackData(), the second is the value used for the 'unckecked' state
// and the third is the value used for 'checked' state.
// Usage contexts:
// for mounting an object:             printAtomGrid ($data, 'F', 'T')
// for changing rack design:           printAtomGrid ($data, 'A', 'F')
// for adding rack problem:            printAtomGrid ($data, 'F', 'U')
// for adding object problem:          printAtomGrid ($data, 'T', 'W')

function renderAtomGrid ($data)
{
	$rack_id = $data['id'];
	for ($unit_no = $data['height']; $unit_no > 0; $unit_no--)
	{
		echo "<tr><th>${unit_no}</th>";
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			$state = $data[$unit_no][$locidx]['state'];
			echo "<td class=state_${state}";
			if (isset ($data[$unit_no][$locidx]['hl']))
				echo $data[$unit_no][$locidx]['hl'];
			echo ">";
			if (!($data[$unit_no][$locidx]['enabled'] === TRUE))
				echo '<input type=checkbox disabled>';
			else
				echo "<input type=checkbox" . $data[$unit_no][$locidx]['checked'] . " name=atom_${rack_id}_${unit_no}_${locidx}>";
			echo '</td>';
		}
		echo "</tr>\n";
	}
}

function renderPermissions ()
{
	startPortlet ('User permissions');
	echo "<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	echo "<tr><th>Username</th><th>Page</th><th>Tab</th><th>Access</th></tr>";
	global $perms, $nextorder;
	$order = 'odd';
	foreach ($perms as $username => $pages)
		foreach ($pages as $page => $tabs)
			foreach ($tabs as $tab => $access)
			{
				echo "<tr class=row_${order}><td class=tdleft>$username</td><td>$page</td><td>$tab</td><td>$access</td></tr>\n";
				$order = $nextorder[$order];
			}
	echo "</table>\n";
	finishPortlet();
}

function renderAccounts ()
{
	global $nextorder, $accounts;
	startPortlet ('User accounts');
	echo "<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	echo "<tr><th class=tdleft>Username</th><th class=tdleft>Real name</th></tr>";
	$order = 'odd';
	foreach ($accounts as $user)
	{
		echo "<tr class=row_${order}><td class=tdleft>${user['user_name']}</td><td class=tdleft>${user['user_realname']}</td></li>";
		$order = $nextorder[$order];
	}
	echo '</table>';
	finishPortlet();
}

function renderAccountsEditForm ()
{
	global $root, $pageno, $tabno, $accounts;
	startPortlet ('User accounts');
	showMessageOrError();
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>op</th><th>Username</th><th>Real name</th><th>Password</th><th>&nbsp;</th></tr>\n";
	foreach ($accounts as $account)
	{
		echo "<form action='${root}process.php'>";
		echo "<input type=hidden name=op value=updateAccount>";
		echo "<input type=hidden name=page value='${pageno}'>\n";
		echo "<input type=hidden name=tab value='${tabno}'>\n";
		echo "<input type=hidden name=id value='${account['user_id']}'><tr>";
		echo "<td>";
		if ($account['user_enabled'] == 'yes' && $account['user_id'] != 1)
		{
			echo "<a href='${root}process.php?op=disableAccount&page=${pageno}&tab=${tabno}&id=${account['user_id']}'>";
			printImageHREF ('blockuser', 'disable account');
			echo "</a>\n";
		}
		if ($account['user_enabled'] == 'no' && $account['user_id'] != 1)
		{
			echo "<a href='${root}process.php?op=enableAccount&page=${pageno}&tab=${tabno}&id=${account['user_id']}'>";
			printImageHREF ('unblockuser', 'enable account');
			echo "</a>\n";
		}
		// Otherwise skip icon.
		echo "</td>";
		echo "<td><input type=text name=username value='${account['user_name']}' size=16></td>";
		echo "<td><input type=text name=realname value='${account['user_realname']}' size=24></td>";
		echo "<td><input type=password name=password value='${account['user_password_hash']}' size=64></td>";
		echo "<td><input type='submit' value='OK'></td>";
		echo "</form></tr>\n";
	}
	echo "<form action='${root}process.php' method=post><tr>";
	echo "<input type=hidden name=op value=createAccount>\n";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<td colspan=2><input type=text size=16 name=username tabindex=100></td>\n";
	echo "<td><input type=text size=24 name=realname tabindex=101></td>";
	echo "<td><input type=password size=64 name=password tabindex=102></td>";
	echo "<td colspan=4><input type=submit value='Create account' tabindex=103></td></tr></form>";
	echo "</table><br>\n";
	finishPortlet();
}

function printChildrenAsOptions ($root, $depth = 0)
{
	echo "<option value=${root['title']}>";
	if ($depth == 0)
		echo '* ';
	for ($i = 0; $i < $depth; $i++)
		echo '-- ';
	echo $root['title'];
	echo "</option>\n";
	foreach ($root['kids'] as $kid)
		printChildrenAsOptions ($kid, $depth + 1);
}

// 1. Find all parentless pages.
// 2. For each of them recursively find all children.
// 3. Output the tree with recursion tree display.
function printPagesTree ()
{
	global $page;
	echo '<pre>';
	foreach ($page as $ctitle => $cpage)
		if (!isset ($cpage['parent']))
		{
			$croot['title'] = $ctitle;
			$croot['kids'] = getAllChildPages ($ctitle);
			printChildrenAsOptions ($croot);
		}
	echo '</pre>';
}

function renderPermissionsEditForm ()
{
	global $root, $pageno, $tabno, $perms, $accounts;
	startPortlet ('User permissions');
	showMessageOrError();
	echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	echo "<tr><th>&nbsp;</th><th>Username</th><th>Page</th><th>Tab</th><th>Access</th></tr>\n";
	foreach ($perms as $username => $pages)
		foreach ($pages as $access_page => $tabs)
			foreach ($tabs as $access_tab => $access)
			{
				echo "<td>";
				if ($username != '%')
					$userid = $accounts[$username]['user_id'];
				else
					$userid = 0;
				echo "<a href='${root}process.php?op=revoke&page=${pageno}&tab=${tabno}&access_userid=${userid}&access_page=${access_page}&access_tab=${access_tab}'>";
				printImageHREF ('revoke', 'Revoke permission');
				echo "</a></td>";
				echo "<td>${username}</td>";
				echo "<td>${access_page}</td>";
				echo "<td>${access_tab}</td>";
				echo "<td>${access}</td>";
				echo "</tr>\n";
			}
	echo "<form action='${root}process.php' method=post><tr>";
	echo "<input type=hidden name=op value=grant>\n";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	// FIXME: border=0 doesn't work here for unknown reason
	echo "<td>";
	printImageHREF ('grant', '', TRUE, 103);
	echo "</td>";
	echo "<td><select name=access_userid>";
	echo "<option value=0>ANY</option>";
	foreach ($accounts as $account)
		echo "<option value=${account['user_id']}>${account['user_name']}</option>";
	echo "</select></td>\n";
	echo "<td><select name=access_page>";
	echo "<option value='%'>ANY</option>";
	printPagesTree();
	echo "</select></td>";
	echo "<td><input type=text size=16 name=access_tab tabindex=102 value=default></td>";
	echo "<td><input type=radio name=access_value value=no checked>no <input type=radio name=access_value value=yes>yes</td>";
	echo "</tr></form>";
	echo "</table><br>\n";
	finishPortlet();
}

function renderPortMap ($editable = FALSE)
{
	global $nextorder, $root, $pageno, $tabno;
	showMessageOrError();
	startPortlet ("Port compatibility map");
	$ptlist = getPortTypes();
	$pclist = getPortCompat();
	$pctable = buildPortCompatMatrixFromList ($ptlist, $pclist);
	if ($editable)
	{
		echo "<form method=post action='${root}process.php'>";
		echo "<input type=hidden name=page value='${pageno}'>";
		echo "<input type=hidden name=tab value='${tabno}'>";
		echo "<input type=hidden name=op value=save>";
	}
	echo "<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	echo "<tr><th class=vert_th>&nbsp;</th>";
	foreach ($ptlist as $name2)
		echo "<th>to ${name2}</th>";
	echo "</tr>";
	// Make a copy to have an independent array pointer.
	$ptlistY = $ptlist;
	$order = 'odd';
	foreach ($ptlistY as $type1 => $name1)
	{
		echo "<tr class=row_${order}><th class=vert_th style='border-bottom: 0px;'>from $name1</th>";
		foreach ($ptlist as $type2 => $name2)
		{
			echo '<td><input type=checkbox' . ($editable ? " name=atom_${type1}_${type2}" : ' disabled');
			echo ($pctable[$type1][$type2] ? ' checked' : '') . '></td>';
		}
		echo "</tr>\n";
		$order = $nextorder[$order];
	}
	echo '</table><br>';
	if ($editable)
	{
		echo "<input type=submit value='Save changes'>";
		echo "</form>";
	}
	finishPortlet();
}

function renderConfigMainpage ()
{
	global $pageno, $root;
	$children = getDirectChildPages ($pageno);
	echo '<ul>';
	foreach ($children as $cpageno => $child)
	{
		$ctitle = $child['title']($cpageno);
		echo "<li><a href='${root}?page=${cpageno}'>" . $ctitle['name'] . "</li>\n";
	}
	echo '';
	echo '</ul>';
}

function renderRackPage ($rack_id)
{
	if ($rack_id == 0)
	{
		showError ('Invalid rack_id in renderRack()');
		return;
	}
	if (($rackData = getRackData ($rack_id)) == NULL)
	{
		showError ('getRackData() failed in renderRack()');
		return;
	}
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0><tr>";

	// Left column with information.
	echo "<td class=pcleft>";
	startPortlet ('Rack information');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>Rack row:</th><td class=tdleft>${rackData['row_name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Name:</th><td class=tdleft>${rackData['name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Height:</th><td class=tdleft>${rackData['height']}</td></tr>\n";
	if (!empty ($rackData['comment']))
		echo "<tr><th width='50%' class=tdright>Comment:</th><td class=tdleft>${rackData['comment']}</td></tr>\n";
	echo '</table>';
	finishPortlet();
	echo '</td>';

	// Right column with rendered rack.
	echo '<td>';
	startPortlet ('Rack diagram');
	renderRack ($rack_id);
	finishPortlet();
	echo '</td>';

	echo '</tr></table>';
}

function renderDictionary ()
{
	global $nextorder;
	$dict = getDict();
	echo "<table border=0><tr>";
	foreach ($dict as $chapter)
	{
		echo "<td class=pcleft>";
		startPortlet ($chapter['name'] . ' (' . count ($chapter['word']) . ')');
		echo "<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
		$order = 'odd';
		foreach ($chapter['word'] as $key => $value)
		{
			echo "<tr class=row_${order}><td class=tdleft><div title='key=${key}'>${value}</div></td></tr>";
			$order = $nextorder[$order];
		}
		echo "</table>";
		finishPortlet();
		echo "</td>";
	}
	echo "</tr></table>";
}

function renderDictionaryEditor ()
{
	global $root, $pageno, $tabno;
	showMessageOrError();
	$dict = getDict();
	echo "<table border=0><tr>";
	foreach ($dict as $chapter)
	{
		echo "<td class=pcleft>";
		startPortlet ($chapter['name'] . ' (' . count ($chapter['word']) . ')');
		echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
		foreach ($chapter['word'] as $key => $value)
		{
			echo "<form action='${root}process.php' method=post>";
			echo "<input type=hidden name=page value='${pageno}'>";
			echo "<input type=hidden name=tab value='${tabno}'>";
			echo "<input type=hidden name=op value='upd'>";
			echo "<input type=hidden name=chapter_no value='${chapter['no']}'>";
			echo "<input type=hidden name=dict_key value='${key}'>";
			echo '<tr>';
			echo "<td><a href='${root}process.php?page=${pageno}&tab=${tabno}&op=del&chapter_no=${chapter['no']}&dict_key=${key}'>";
			printImageHREF ('delete', 'Delete word');
			echo "</a></td>";
			echo "<td class=tdright><input type=text name=dict_value size=32 value='${value}'></td>";
			echo "<td><input type=submit value=OK></td>";
			echo '</tr></form>';
		}
		echo "<form action='${root}process.php' method=post>";
		echo "<input type=hidden name=page value='${pageno}'>";
		echo "<input type=hidden name=tab value='${tabno}'>";
		echo "<input type=hidden name=op value=add>";
		echo "<input type=hidden name=chapter_no value='${chapter['no']}'>";
		echo '<tr>';
		echo "<td>&nbsp;</td>";
		echo "<td class=tdright><input type=text name=dict_value size=32></td>";
		echo "<td><input type=submit value=OK></td>";
		echo '</tr></form>';
		echo "</table>";
		finishPortlet();
		echo "</td>";
	}
	echo "</tr></table>";
}

// We don't allow to rename/delete a sticky chapter and we don't allow
// to delete a non-empty chapter.
function renderChaptersEditor ()
{
	global $root, $pageno, $tabno;
	showMessageOrError();
	$dict = getDict();
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo '<tr><th>&nbsp;</th><th>Chapter name</th><th>Words</th><th>&nbsp;</th></tr>';
	foreach ($dict as $chapter)
	{
		$wordcount = count ($chapter['word']);
		$sticky = $chapter['sticky'];
		echo "<form action='${root}process.php' method=post>";
		echo "<input type=hidden name=page value='${pageno}'>";
		echo "<input type=hidden name=tab value='${tabno}'>";
		echo "<input type=hidden name=op value=upd>";
		echo "<input type=hidden name=chapter_no value='${chapter['no']}'>";
		echo '<tr>';
		echo '<td>';
		if ($sticky or $wordcount > 0)
			echo '&nbsp;';
		else
		{
			echo "<a href='${root}process.php?page=${pageno}&tab=${tabno}&op=del&chapter_no=${chapter['no']}'>";
			printImageHREF ('delete', 'Remove chapter');
			echo "</a>";
		}
		echo '</td>';
		echo "<td><input type=text name=chapter_name value='${chapter['name']}'" . ($sticky ? ' disabled' : '') . "></td>";
		echo "<td class=tdleft>${wordcount}</td><td>";
		if ($sticky)
			echo '&nbsp;';
		else
			echo "<input type=submit value='OK'>";
		echo '</td></tr>';
		echo '</form>';
	}
	echo "<form action='${root}process.php' method=post>";
	echo "<input type=hidden name=page value='${pageno}'>";
	echo "<input type=hidden name=tab value='${tabno}'>";
	echo "<input type=hidden name=op value=add>";
	echo '<tr><td>';
	printImageHREF ('add', '', TRUE);
	echo "</td><td colspan=3><input type=text name=chapter_name></td>";
	echo '</tr>';
	echo '</form>';
	echo "</table>\n";
}

function renderAttributes ()
{
	global $nextorder;
	$attrMap = getAttrMap();
	startPortlet ('Optional attributes');
	echo "<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	echo "<tr><th class=tdleft>Attribute name</th><th class=tdleft>Attribute type</th><th class=tdleft>Applies to</th></tr>";
	$order = 'odd';
	foreach ($attrMap as $attr)
	{
		echo "<tr class=row_${order}>";
		echo "<td class=tdleft>${attr['name']}</td>";
		echo "<td class=tdleft>${attr['type']}</td>";
		echo '<td class=tdleft>';
		if (count ($attr['application']) == 0)
			echo '&nbsp;';
		else
			foreach ($attr['application'] as $app)
				if ($attr['type'] == 'dict')
					echo "${app['objtype_name']} (values from '${app['chapter_name']}')<br>";
				else
					echo "${app['objtype_name']}<br>";
		echo '</td>';
		echo "</tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table><br>\n";
	finishPortlet();
}

function renderEditAttributesForm ()
{
	global $root, $pageno, $tabno;
	$attrMap = getAttrMap();
	showMessageOrError();
	startPortlet ('Optional attributes');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo '<tr><th>&nbsp;</th><th>Name</th><th>Type</th><th>&nbsp;</th></tr>';
	foreach ($attrMap as $attr)
	{
		echo "<form action='${root}process.php' method=post>";
		echo "<input type=hidden name=page value='${pageno}'>";
		echo "<input type=hidden name=tab value='${tabno}'>";
		echo "<input type=hidden name=op value=upd>";
		echo "<input type=hidden name=attr_id value='${attr['id']}'>";
		echo '<tr>';
		echo "<td><a href='${root}process.php?page=${pageno}&tab=${tabno}&op=del&attr_id=${attr['id']}'>";
		printImageHREF ('delete', 'Remove attribute');
		echo '</a></td>';
		echo "<td><input type=text name=attr_name value='${attr['name']}'></td>";
		echo "<td>${attr['type']}</td>";
		echo "<td><input type=submit value='OK'></td>";
		echo '</tr>';
		echo '</form>';
	}
	echo "<form action='${root}process.php' method=post>";
	echo "<input type=hidden name=page value='${pageno}'>";
	echo "<input type=hidden name=tab value='${tabno}'>";
	echo "<input type=hidden name=op value=add>";
	echo '<tr><td>';
	printImageHREF ('add', '', TRUE);
	echo "</td><td><input type=text name=attr_name></td>";
	echo '<td><select name=attr_type>';
	echo '<option value=uint>uint</option>';
	echo '<option value=float>float</option>';
	echo '<option value=string>string</option>';
	echo '<option value=dict>dict</option>';
	echo '</select></td>';
	echo '</tr>';
	echo '</form>';
	echo "</table>\n";
	finishPortlet();
}

function renderEditAttrMapForm ()
{
	global $root, $pageno, $tabno;
	$attrMap = getAttrMap();
	showMessageOrError();
	startPortlet ('Attribute map');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo '<tr><th>&nbsp;</th><th>Attribute name</th><th>Object type</th><th>Dictionary chapter</th></tr>';
	foreach ($attrMap as $attr)
	{
		if (count ($attr['application']) == 0)
			continue;
		foreach ($attr['application'] as $app)
		{
			echo '<tr>';
			echo '<td>';
			echo "<a href='${root}process.php?page=${pageno}&tab=${tabno}&op=del&";
			echo "attr_id=${attr['id']}&objtype_id=${app['objtype_id']}'>";
			printImageHREF ('delete', 'Remove mapping');
			echo "</a>";
			echo '</td>';
			echo "<td>${attr['name']}</td>";
			echo "<td>${app['objtype_name']}</td>";
			echo "<td>";
			if ($attr['type'] == 'dict')
				echo "${app['chapter_name']}";
			else
				echo '&nbsp;';
			echo "</td></tr>\n";
		}
	}
	echo "<form action='${root}process.php' method=post>";
	echo "<input type=hidden name=page value='${pageno}'>";
	echo "<input type=hidden name=tab value='${tabno}'>";
	echo "<input type=hidden name=op value=add>";
	echo '<tr><td>';
	printImageHREF ('add', '', TRUE);
	echo "</td><td><select name=attr_id>";
	$shortType['uint'] = 'U';
	$shortType['float'] = 'F';
	$shortType['string'] = 'S';
	$shortType['dict'] = 'D';
	foreach ($attrMap as $attr)
		echo "<option value=${attr['id']}>[" . $shortType[$attr['type']] . "] ${attr['name']}</option>";
	echo "</select></td>";
	echo '<td>';
	printSelect (getObjectTypeList(), 'objtype_id');
	echo '</td>';
	$dict = getDict();
	echo '<td><select name=chapter_no>';
	foreach ($dict as $chapter)
		if (!$chapter['sticky'])
			echo "<option value='${chapter['no']}'>${chapter['name']}</option>";
	echo '</select></td>';
	echo '</tr>';
	echo '</form>';
	echo "</table>\n";
	finishPortlet();
}

function printImageHREF ($tag, $title = '', $do_input = FALSE, $tabindex = 0)
{
	global $root;
	$image['error']['path'] = 'pix/error.png';
	$image['error']['width'] = 76;
	$image['error']['height'] = 17;
	$image['logo']['path'] = 'pix/defaultlogo.png';
	$image['logo']['width'] = 210;
	$image['logo']['height'] = 40;
	$image['rackspace']['path'] = 'pix/racks.png';
	$image['rackspace']['width'] = 218;
	$image['rackspace']['height'] = 200;
	$image['objects']['path'] = 'pix/server.png';
	$image['objects']['width'] = 218;
	$image['objects']['height'] = 200;
	$image['ipv4space']['path'] = 'pix/addressspace.png';
	$image['ipv4space']['width'] = 218;
	$image['ipv4space']['height'] = 200;
	$image['config']['path'] = 'pix/configuration.png';
	$image['config']['width'] = 218;
	$image['config']['height'] = 200;
	$image['reports']['path'] = 'pix/report.png';
	$image['reports']['width'] = 218;
	$image['reports']['height'] = 200;
	$image['help']['path'] = 'pix/help.png';
	$image['help']['width'] = 218;
	$image['help']['height'] = 200;
	$image['reserve']['path'] = 'pix/stop.png';
	$image['reserve']['width'] = 16;
	$image['reserve']['height'] = 16;
	$image['useup']['path'] = 'pix/go.png';
	$image['useup']['width'] = 16;
	$image['useup']['height'] = 16;
	$image['blockuser'] = $image['reserve'];
	$image['unblockuser'] = $image['useup'];
	$image['link']['path'] = 'pix/link.png';
	$image['link']['width'] = 24;
	$image['link']['height'] = 24;
	$image['unlink']['path'] = 'pix/unlink.png';
	$image['unlink']['width'] = 24;
	$image['unlink']['height'] = 24;
	$image['add']['path'] = 'pix/greenplus.png';
	$image['add']['width'] = 16;
	$image['add']['height'] = 16;
	$image['delete']['path'] = 'pix/delete_s.gif';
	$image['delete']['width'] = 16;
	$image['delete']['height'] = 16;
	$image['grant'] = $image['add'];
	$image['revoke'] = $image['delete'];
	$image['helphint']['path'] = 'pix/helphint.png';
	$image['helphint']['width'] = 24;
	$image['helphint']['height'] = 24;
	if (!isset ($image[$tag]))
		$tag = 'error';
	$img = $image[$tag];
	if ($do_input == TRUE)
		echo
			"<input type=image name=submit " .
			"src='${root}${img['path']}' " .
			"border=0 " .
			($tabindex ? '' : "tabindex=${tabindex}") .
			">";
	else
		echo
			"<img " .
			"src='${root}${img['path']}' " .
			"width=${img['width']} " .
			"height=${img['height']} " .
			"border=0 " .
			(empty ($title) ? '' : "title='${title}'") .
			">";
}

// This function returns URL for favourite icon.
function getFaviconURL ()
{
	global $root;
	return $root . 'pix/racktables.ico';
}

function renderReportSummary ()
{
	echo "<table width='100%'>\n";
	echo "<tr><td class=pcleft>\n";
	startPortlet ("Dictionary/objects stats");
	echo "<table>\n";
	foreach (getDictStats() as $header => $data)
	{
		echo "<tr><th>${header}:</th><td>${data}</td></tr>\n";
	}
	echo "</table>\n";
	finishPortlet();

	echo "</td><td class=pcright>\n";

	startPortlet ("Here be dragons");
	dragon();
	dragon();
	dragon();
	echo 'ASCII art &copy; Daniel C. Au';
	finishPortlet();
	echo "</td></tr>\n";
	echo "</table>\n";
}

function dragon ()
{
?>
<div class=dragon><pre><font color="#00ff33">
                 \||/
                 |  <font color="#ff0000">@</font>___oo  
       /\  /\   / (__<font color=yellow>,,,,</font>|
      ) /^\) ^\/ _)
      )   /^\/   _) 
      )   _ /  / _)
  /\  )/\/ ||  | )_)    
 &lt;  &gt;      |(<font color=white>,,</font>) )__)
  ||      /    \)___)\
  | \____(      )___) )___
   \______(_______<font color=white>;;;</font> __<font color=white>;;;</font>

</font></pre></div>
<?php
}

function renderUIConfig ()
{
	global $configCache, $nextorder;
	showMessageOrError();
	startPortlet ('Current configuration');
	echo '<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center width="50%">';
	echo '<tr><th class=tdleft>Option</th><th class=tdleft>Value</th></tr>';
	$order = 'odd';
	foreach ($configCache as $v)
	{
		if ($v['is_hidden'] != 'no')
			continue;
		echo "<tr class=row_${order}>";
		echo "<td class=tdright>${v['description']}</td>\n";
		echo "<td class=tdleft>${v['varvalue']}</td></tr>";
		$order = $nextorder[$order];
	}
	echo "</table>\n";
	finishPortlet();
}

function renderUIConfigEditForm ()
{
	global $root, $pageno, $tabno, $configCache;
	showMessageOrError();
	startPortlet ('Current configuration');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable width='50%'>\n";
	echo "<tr><th class=tdleft>Option</th>";
	echo "<th class=tdleft>Value</th></tr>";
	echo "<form action='${root}process.php'>";
	echo "<input type=hidden name=op value='upd'>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";

	$i = 0;
	foreach ($configCache as $v)
	{
		if ($v['is_hidden'] != 'no')
			continue;
		echo "<input type=hidden name=${i}_varname value='${v['varname']}'>";
		echo "<tr><td class=tdright>${v['description']}</td>";
		echo "<td class=tdleft><input type=text name=${i}_varvalue value='${v['varvalue']}' size=24></td>";
		echo "</tr>\n";
		$i++;
	}
	echo "<input type=hidden name=num_vars value=${i}>\n";
	echo "<tr><td colspan=2><input type=submit value='Save changes'></td></tr>";
	echo "</form>";
	finishPortlet();
}

// This function queries the gateway about current VLAN configuration and
// renders a form suitable for submit. Ah, and it does submit processing as well.
function renderVLANMembership ($object_id = 0)
{
	global $root, $pageno, $tabno, $remote_username;
	if ($object_id <= 0)
	{
		showError ('Invalid object_id in renderVLANMembership()');
		return;
	}

	// Handle probable pending submit.
	if (isset ($_REQUEST['portcount']))
	{
		$data = getSwitchVLANs ($object_id);
		if ($data === NULL)
		{
			showError ('getSwitchVLANs() failed in renderVLANMembership() during submit processing');
			return;
		}
		list ($vlanlist, $portlist) = $data;
		// Here we just build up 1 set command for the gateway with all of the ports
		// included. The gateway is expected to filter unnecessary changes silently
		// and to provide a list of responses with either error or success message
		// for each of the rest.
		assertUIntArg ('portcount');
		$nports = $_REQUEST['portcount'];
		$prefix = 'set ';
		$log = array();
		$setcmd = '';
		for ($i = 0; $i < $nports; $i++)
			if
			(
				!isset ($_REQUEST['portname_' . $i]) ||
				!isset ($_REQUEST['vlanid_' . $i]) ||
				$_REQUEST['portname_' . $i] != $portlist[$i]['portname']
			)
				$log[] = array ('code' => 'error', 'message' => "Ignoring mailformed record #${i} in form submit");
			elseif
			(
				$_REQUEST['vlanid_' . $i] == $portlist[$i]['vlanid'] ||
				$portlist[$i]['vlaind'] == 'TRUNK'
			)
				continue;
			else
			{
				$setcmd .= $prefix . $_REQUEST['portname_' . $i] . '=' . $_REQUEST['vlanid_' . $i];
				$prefix = ';';
			}
		printLog ($log);
		// Feed the gateway and interpret its (non)response.
		if ($setcmd != '')
			printLog (setSwitchVLANs ($object_id, $setcmd));
	}

	// Reload and render.
	$data = getSwitchVLANs ($object_id);
	if ($data === NULL)
		return;
	list ($vlanlist, $portlist, $maclist) = $data;

	// This table divides the space into 2 columns.
	echo '<table border=0 width="100%"><tr><td class=pcleft>';

	// left column: 1 portlet
	startPortlet ('VLAN table');
	echo '<table class=cooltable cellspacing=0 cellpadding=5 align=center width="100%">';
	echo "<tr><th>ID</th><th>Description</th></tr>";
	$order = 'even';
	global $nextorder;
	foreach ($vlanlist as $id => $descr)
	{
		echo "<tr class=row_${order}><td class=tdright>${id}</td><td class=tdleft>${descr}</td></tr>";
		$order = $nextorder[$order];
	}
	echo '</table>';
	finishPortlet();

	echo '</td><td class=pcright>';
	// Right column: table with 2 rows, each holding 1 portlet
	echo '<table border=0 width="100%"><tr><td>';

	startPortlet ('Current status');
	echo "<table class=widetable cellspacing=3 cellpadding=5 align=center width='100%'><tr>";
	echo "<form method=post>";
	echo "<input type=hidden name=page value='${pageno}'>";
	echo "<input type=hidden name=tab value='${tabno}'>";
	echo "<input type=hidden name=object_id value=${object_id}>";
	$portcount = count ($portlist);
	echo "<input type=hidden name=portcount value=" . $portcount . ">\n";
	$portno = 0;
	$ports_per_row = getConfigVar ('PORTS_PER_ROW');
	foreach ($portlist as $port)
	{
		// Don't let wide forms break our fancy pages.
		if ($portno % $ports_per_row == 0)
		{
			if ($portno > 0)
				echo "</tr>\n";
			echo "<tr><th>" . ($portno + 1) . "-" . ($portno + $ports_per_row > $portcount ? $portcount : $portno + $ports_per_row) . "</th>";
		}
		echo '<td class=port_';
		if ($port['status'] == 'notconnect')
			echo 'notconnect';
		elseif ($port['status'] == 'disabled')
			echo 'disabled';
		elseif ($port['status'] != 'connected')
			echo 'unknown';
		elseif (!isset ($maclist[$port['portname']]))
			echo 'connected_none';
		else
		{
			$maccount = 0;
			foreach ($maclist[$port['portname']] as $vlanid => $addrs)
				$maccount += count ($addrs);
			if ($maccount == 1)
				echo 'connected_single';
			else
				echo 'connected_multi';
		}
		echo '>' . $port['portname'] . '<br>';
		echo "<input type=hidden name=portname_${portno} value=" . $port['portname'] . '>';
		if ($port['vlanid'] == 'trunk')
		{
			echo "<input type=hidden name=vlanid_${portno} value='trunk'>";
			echo "<select disabled multiple='multiple' size=1><option>TRUNK</option></select>";
		}
		else
		{
			echo "<select name=vlanid_${portno}>";
			foreach ($vlanlist as $v => $d)
			{
				echo "<option value=${v}";
				if ($v == $port['vlanid'])
					echo ' selected';
				echo ">${v}</option>";
			}
			echo "</select>";
		}
		$portno++;
		echo "</td>";
	}
	echo "</tr><tr><td colspan=" . ($ports_per_row + 1) . "><input type=submit value='Save changes'></form></td></tr></table>";
	finishPortlet();

	echo '</td></tr><tr><td align=center>';
	// second row
	if (count ($maclist))
	{
		startPortlet ('MAC address table');
		echo '<table border=0 class=cooltable align=center cellspacing=0 cellpadding=5>';
		echo "<tr><th>Port</th><th>VLAN ID</th><th>MAC address</th></tr>\n";
		$order = 'even';
		foreach ($maclist as $portname => $portdata)
			foreach ($portdata as $vlanid => $addrgroup)
				foreach ($addrgroup as $addr)
				{
					echo "<tr class=row_${order}><td>$portname</td><td>$vlanid</td><td>$addr</td></tr>\n";
					$order = $nextorder[$order];
				}
		echo '</table>';
		finishPortlet();
	}

	// End of 2-portlet table.
	echo '</td></tr></table>';

	// End of main table.
	echo '</td></tr></table>';
}

// This snippet either renders a form inviting the user to start SNMP query
// on the current device or displays the result of the scan.
function renderSNMPPortFinder ($object_id = 0)
{
	global $root, $pageno, $tabno, $remote_username;
	if ($object_id <= 0)
	{
		showError ('Invalid object_id in renderSNMPPortFinder()');
		return;
	}
// FIXME: check if SNMP PHP extension is available!
	if (isset ($_REQUEST['do_scan']))
	{
		$log = array();
// IDs: http://cisco.com/en/US/products/sw/cscowork/ps2064/products_device_support_table09186a0080803bb4.html
// 2960: http://www.cisco.com/en/US/products/ps6406/prod_models_comparison.html
// 2970: http://cisco.com/en/US/products/hw/switches/ps5206/products_qanda_item09186a00801b1750.shtml
// 3500XL: http://cisco.com/en/US/products/hw/switches/ps637/products_eol_models.html
// 3560: http://cisco.com/en/US/products/hw/switches/ps5528/products_data_sheet09186a00801f3d7f.html
// 3750: http://cisco.com/en/US/products/hw/switches/ps5023/products_data_sheet09186a008016136f.html
		$ciscomodel[283] = 'WS-C6509-E (9-slot system)';
// FIXME: hwtype hardcoded value will become invalid after the Dictionary table transformation
// in 0.14.7 version. Either the values will have to be adjusted as well or we have to switch
// to value lookup (not reliable).
		$hwtype[283] = 52;
#		$ciscomodel[694] = 'WS-C2960-24TC-L (24 Ethernet 10/100 ports and 2 dual-purpose uplinks)';
#		$ciscomodel[695] = 'WS-C2960-48TC-L (48 Ethernet 10/100 ports and 2 dual-purpose uplinks)';
		$ciscomodel[696] = 'WS-C2960G-24TC-L (20 Ethernet 10/100/1000 ports and 4 dual-purpose uplinks)';
		$hwtype[696] = 71;
		$ciscomodel[697] = 'WS-C2960G-48TC-L (44 Ethernet 10/100/1000 ports and 4 dual-purpose uplinks)';
		$hwtype[697] = 70;
#		$ciscomodel[716] = 'WS-C2960-24TT-L (24 Ethernet 10/100 ports and 2 10/100/1000 uplinks)';
#		$ciscomodel[717] = 'WS-C2960-48TT-L (48 Ethernet 10/100 ports and 2 10/100/1000 uplinks)';
		$ciscomodel[527] = 'WS-C2970G-24T (24 Ethernet 10/100/1000 ports)';
		$hwtype[527] = 114;
		$ciscomodel[561] = 'WS-C2970G-24TS (24 Ethernet 10/100/1000 ports and 4 10/100/1000 SFP uplinks)';
		$hwtype[561] = 115;
#		$ciscomodel[633] = 'WS-C3560-24TS (24 Ethernet 10/100 ports and 2 10/100/1000 SFP uplinks)';
#		$hwtype[633] = 73;
		$ciscomodel[634] = 'WS-C3560-48TS (48 Ethernet 10/100 ports and 4 10/100/1000 SFP uplinks)';
		$hwtype[634] = 74;
#		$ciscomodel[563] = 'WS-C3560-24PS (24 Ethernet 10/100 POE ports and 2 10/100/1000 SFP uplinks)';
#		$hwtype[563] = 75;
		$ciscomodel[564] = 'WS-C3560-48PS (48 Ethernet 10/100 POE ports and 4 10/100/1000 SFP uplinks)';
		$hwtype[564] = 76;
		$ciscomodel[614] = 'WS-C3560G-24PS (24 Ethernet 10/100/1000 POE ports and 4 10/100/1000 SFP uplinks)';
		$hwtype[614] = 79;
		$ciscomodel[615] = 'WS-C3560G-24TS (24 Ethernet 10/100/1000 ports and 4 10/100/1000 SFP uplinks)';
		$hwtype[615] = 77;
		$ciscomodel[616] = 'WS-C3560G-48PS (48 Ethernet 10/100/1000 POE ports and 4 10/100/1000 SFP uplinks)';
		$hwtype[616] = 80;
		$ciscomodel[617] = 'WS-C3560G-48TS (48 Ethernet 10/100/1000 ports and 4 10/100/1000 SFP uplinks)';
		$hwtype[617] = 78;
		$ciscomodel[58] = 'WS-C4503 (3-slot system)';
		$hwtype[58] = 49;
		$ciscomodel[503] = '4503 (3-slot system)';
		$hwtype[503] = 49;
		$ciscomodel[59] = 'WS-C4506 (6-slot system)';
		$hwtype[59] = 60;
		$ciscomodel[502] = '4506 (6-slot system)';
		$hwtype[502] = 60;
		$ciscomodel[626] = 'WS-C4948 (48 Ethernet 10/100/1000 ports and 4 10/100/1000 SFP uplinks)';
		$hwtype[626] = 51;
		$ciscomodel[659] = 'WS-C4948-10GE (48 Ethernet 10/100/1000 ports and 2 10Gb X2 uplinks)';
		assertStringArg ('community');
		$community = $_REQUEST['community'];
		$objectInfo = getObjectInfo ($object_id);
		$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
		$sysDescr = snmpget ($endpoints[0], $community, 'sysDescr.0');
		// Strip the object type, it's always string here.
		$sysDescr = substr ($sysDescr, strlen ('STRING: '));
		if (strpos ($sysDescr, 'Cisco IOS Software') === 0 or strpos ($sysDescr, 'Cisco Internetwork Operating System Software') === 0)
			$log[] = array ('code' => 'success', 'message' => 'Seems to be a Cisco box');
		else
		{
			$log[] = array ('code' => 'error', 'message' => 'No idea how to handle ' . $sysDescr);
			printLog ($log);
			return;
		}

		// It's a Cisco. Go on.
		$attrs = getAttrValues ($object_id);
		// Only fill in attribute values, if they are not set.
		// FIXME: this is hardcoded

		if (empty ($attrs[5]['value'])) // SW version
		{
			$IOSversion = ereg_replace ('^.*, Version ([^ ]+), .*$', '\\1', $sysDescr);
			$error = commitUpdateAttrValue ($object_id, 5, $IOSversion);
			if ($error == TRUE)
				$log[] = array ('code' => 'success', 'message' => 'SW version set to ' . $IOSversion);
			else
				$log[] = array ('code' => 'error', 'message' => 'Failed settig SW version: ' . $error);
		}

		if (empty ($attrs[4]['value']) and substr ($IOSversion, 0, 4) == '12.2') // switch OS type
		{
			$error = commitUpdateAttrValue ($object_id, 4, 21);
			if ($error == TRUE)
				$log[] = array ('code' => 'success', 'message' => 'Switch OS type set to Cisco IOS 12.2');
			else
				$log[] = array ('code' => 'error', 'message' => 'Failed settig Switch OS type: ' . $error);
		}

		$sysObjectID = snmpget ($endpoints[0], $community, 'sysObjectID.0');
		// Transform OID
		$sysObjectID = substr ($sysObjectID, strlen ('OID: SNMPv2-SMI::enterprises.9.1.'));
		if (!isset ($ciscomodel[$sysObjectID]))
		{
			$log[] = array ('code' => 'error', 'message' => 'Could not guess exact HW model!');
			printLog ($log);
			return;
		}
		$log[] = array ('code' => 'success', 'message' => 'HW is ' . $ciscomodel[$sysObjectID]);
		if (empty ($attrs[2]['value']) and isset ($hwtype[$sysObjectID])) // switch HW type
		{
			$error = commitUpdateAttrValue ($object_id, 2, $hwtype[$sysObjectID]);
			if ($error == TRUE)
				$log[] = array ('code' => 'success', 'message' => 'HW type updated Ok');
			else
				$log[] = array ('code' => 'error', 'message' => 'Failed settig HW type: ' . $error);
		}
		// Now fetch ifType, ifDescr and ifPhysAddr and let model-specific code sort the data out.
		$ifType = snmpwalkoid ($endpoints[0], $community, 'ifType');
		$ifDescr = snmpwalkoid ($endpoints[0], $community, 'ifdescr');
		$ifPhysAddress = snmpwalkoid ($endpoints[0], $community, 'ifPhysAddress');
		// Combine 3 tables into 1...
		$ifList1 = array();
		foreach ($ifType as $key => $val)
		{
			list ($dummy, $ifIndex) = explode ('.', $key);
			list ($dummy, $type) = explode (' ', $val);
			$ifList1[$ifIndex]['type'] = $type;
		}
		foreach ($ifDescr as $key => $val)
		{
			list ($dummy, $ifIndex) = explode ('.', $key);
			list ($dummy, $descr) = explode (' ', $val);
			$ifList1[$ifIndex]['descr'] = trim ($descr, '"');
		}
		foreach ($ifPhysAddress as $key => $val)
		{
			list ($dummy, $ifIndex) = explode ('.', $key);
			list ($dummy, $addr) = explode (':', $val);
			$addr = str_replace (' ', '', $addr);
			$ifList1[$ifIndex]['phyad'] = $addr;
		}
		// ...and then reverse it inside out to make description the key.
		$ifList2 = array();
		foreach ($ifList1 as $ifIndex => $data)
		{
			$ifList2[$data['descr']]['type'] = $data['type'];
			$ifList2[$data['descr']]['phyad'] = $data['phyad'];
			$ifList2[$data['descr']]['idx'] = $ifIndex;
		}
		$newports = 0;
		// Now we can directly pick necessary ports from the table accordingly
		// to our known hardware model.
		switch ($sysObjectID)
		{
		// FIXME: chassis edge switches often share a common naming scheme, so
		// the sequences below have to be generalized. Let's have some duplicated
		// code for the time being, as this is the first implementation ever.
			case '697': // WS-C2960G-48TC-L
				// 44 copper ports: 1X, 2X, 3X...
				// 4 combo ports: 45, 46, 47, 48. Don't list SFP connectors atm, as it's not
				// clear how to fit them into current Ports table structure.
				for ($i = 1; $i <= 48; $i++)
				{
					$label = ($i >= 45) ? "${i}" : "${i}X";
					$error = commitAddPort ($object_id, 'gi0/' . $i, 11, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
					if ($error == '')
						$newports++;
					else
						$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
				}
				break;
			case '696': // WS-C2960G-24TC-L
				// Quite similar to the above.
				for ($i = 1; $i <= 24; $i++)
				{
					$label = ($i >= 21) ? "${i}" : "${i}X";
					$error = commitAddPort ($object_id, 'gi0/' . $i, 11, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
					if ($error == '')
						$newports++;
					else
						$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
				}
				break;
			case '564': // WS-C3560-48PS
			case '634': // WS-C3560-48TS
				for ($i = 1; $i <= 48; $i++)
				{
					$label = "${i}X";
					$error = commitAddPort ($object_id, 'fa0/' . $i, 6, $label, $ifList2["FastEthernet0/${i}"]['phyad']);
					if ($error == '')
						$newports++;
					else
						$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
				}
				for ($i = 1; $i <= 4; $i++)
				{
					$label = "${i}";
					$error = commitAddPort ($object_id, 'gi0/' . $i, 11, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
					if ($error == '')
						$newports++;
					else
						$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
				}
				break;
			case '614': // WS-C3560G-24PS
			case '615': // WS-C3560G-24TS
			case '527': // WS-C2970G-24T
			case '561': // WS-C2970G-24TS
				for ($i = 1; $i <= 24; $i++)
				{
					$label = "${i}X";
					$error = commitAddPort ($object_id, 'gi0/' . $i, 11, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
					if ($error == '')
						$newports++;
					else
						$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
				}
				break;
			case '616': // WS-C3560G-48PS
			case '617': // WS-C3560G-48TS
			case '626': // WS-C4948
			case '659': // WS-C4948-10GE
				for ($i = 1; $i <= 48; $i++)
				{
					$label = "${i}X";
					$error = commitAddPort ($object_id, 'gi0/' . $i, 11, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
					if ($error == '')
						$newports++;
					else
						$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
				}
				break;
		// For modular devices we don't iterate over all possible port names,
		// but use the first list to pick everything that looks legitimate
		// for this hardware. It would be correct to fetch the list of modules
		// installed to generate lists of ports, but who is going to implement
		// this?
			case '503': // 4503
			case '58': // WS-C4503
			case '502': // 4506
			case '59': // WS-C4506
			case '283': // WS-C6509-E
				foreach ($ifList1 as $port)
				{
					if ($port['type'] != 'ethernet-csmacd(6)')
						continue;
					// Copper Fa/Gi harvesting is relatively simple, while 10Gig ports can
					// have random samples of transciever units.
					if (strpos ($port['descr'], 'FastEthernet') === 0) // Fa
					{
						$prefix = 'fa';
						$ptype = 6; // RJ-45/100Base-TX
						list ($slotno, $portno) = explode ('/', substr ($port['descr'], strlen ('FastEthernet')));
					}
					elseif (strpos ($port['descr'], 'GigabitEthernet') === 0) // Gi
					{
						$prefix = 'gi';
						$ptype = 11; // RJ-45/1000Base-T
						list ($slotno, $portno) = explode ('/', substr ($port['descr'], strlen ('GigabitEthernet')));
					}
					else continue;
					$label = "slot ${slotno} port ${portno}";
					$pname = "${prefix}${slotno}/${portno}";
					$error = commitAddPort ($object_id, $pname, $ptype, $label, $port['phyad']);
					if ($error == '')
						$newports++;
					else
						$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $pname . ': ' . $error);
				}
				break;
			default:
				showError ("Unexpected sysObjectID '${sysObjectID}' in renderSNMPPortFinder()");
		}
		$error = commitAddPort ($object_id, 'con0', 16, 'console', '');
		if ($error == '')
			$newports++;
		else
			$log[] = array ('code' => 'error', 'message' => 'Failed to add console port : ' . $error);
		if ($newports > 0)
			$log[] = array ('code' => 'success', 'message' => "Added ${newports} new ports");
		printLog ($log);
		return;
	}
	echo "<form method=post>\n";
	echo "<input type=hidden name=pageno value='${pageno}'>\n";
	echo "<input type=hidden name=tabno value='${pageno}'>\n";
?>
<p align=center>
This switch has no ports listed, that's why you see this form. If you supply SNMP community,
I can try atomatic data harvesting on the switch. As soon as at least one relevant port is found,
this tab will not be seen any more. Good luck.<br>
<input type=text name=community value='public'>
<input type=submit name='do_scan' value='Go!'> 
</p>
<?php
}

function renderUIResetForm()
{
	global $root, $pageno, $tabno;
	echo "<form method=post action='${root}process.php'>";
	echo "<input type=hidden name=page value=${pageno}>";
	echo "<input type=hidden name=tab value=${tabno}>";
	echo "<input type=hidden name=op value=go>";
	echo "This button will reset user interface configuration to its defaults (except organization name): ";
	echo "<input type=submit value='proceed'>";
	echo "</form>";
}

?>
