<?php
/*
*
*  This file contains frontend functions for RackTables.
*
*/

// Interface function's special.
$nextorder['odd'] = 'even';
$nextorder['even'] = 'odd';

// address allocation type
$aat = array
(
	'regular' => 'Connected',
	'virtual' => 'Loopback',
	'shared' => 'Shared',
	'router' => 'Router',
);
// address allocation code, IPv4 addresses and objects view
$aac = array
(
	'regular' => '',
	'virtual' => '<strong>L</strong>',
	'shared' => '<strong>S</strong>',
	'router' => '<strong>R</strong>',
);
// address allocation code, IPv4 networks view
$aac2 = array
(
	'regular' => '',
	'virtual' => '<strong>L:</strong>',
	'router' => '<strong>R:</strong>',
);

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
						<h1><a href='<?php echo $root; ?>?page=ipv4slb'>IPv4 SLB<br>
						<?php printImageHREF ('ipv4slb'); ?></a></h1>
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
	$tagfilter = getTagFilter();
	$tagfilter_str = getTagFilterStr ($tagfilter);
	echo "<table class=objview border=0 width='100%'><tr><td class=pcleft>";
	renderTagFilterPortlet ($tagfilter, 'rack');
	echo '</td><td class=pcright>';
	echo '<table border=0 cellpadding=10 cellpadding=1>';
	// generate thumb gallery
	$rackrowList = getRackspace ($tagfilter);
	global $root, $nextorder;
	$rackwidth = getRackImageWidth();
	$order = 'odd';
	foreach ($rackrowList as $rackrow)
	{
		echo "<tr class=row_${order}><th class=tdleft>";
		echo "<a href='${root}?page=row&row_id=${rackrow['row_id']}${tagfilter_str}'>";
		echo "${rackrow['row_name']}</a></th>";
		$rackList = getRacksForRow ($rackrow['row_id'], $tagfilter);
		echo "<td><table border=0 cellspacing=5><tr>";
		foreach ($rackList as $rack)
		{
			echo "<td align=center><a href='${root}?page=rack&rack_id=${rack['id']}'>";
			echo "<img border=0 width=${rackwidth} height=";
			echo getRackImageHeight ($rack['height']);
			echo " title='${rack['height']} units'";
			echo "src='render_image.php?img=minirack&rack_id=${rack['id']}'>";
			echo "<br>${rack['name']}</a></td>";
		}
		echo "</tr></table></tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table>\n";
	echo "</td></tr></table>\n";
}

function renderRow ($row_id = 0)
{
	if ($row_id == 0)
	{
		showError ('Invalid row_id', __FUNCTION__);
		return;
	}
	if (($rowInfo = getRackRowInfo ($row_id)) == NULL)
	{
		showError ('getRackRowInfo() failed', __FUNCTION__);
		return;
	}
	$tagfilter = getTagFilter();
	$rackList = getRacksForRow ($row_id, $tagfilter);
	// Main layout starts.
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";

	// Left portlet with row information.
	echo "<tr><td class=pcleft>";
	startPortlet ($rowInfo['name']);
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>Racks:</th><td class=tdleft>${rowInfo['count']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Units:</th><td class=tdleft>${rowInfo['sum']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Utilization:</th><td class=tdleft>";
	renderProgressBar (getRSUforRackRow ($rackList));
	echo "</td></tr>\n";
	echo "</table><br>\n";
	finishPortlet();

	echo "</td><td class=pcright rowspan=2>";

	global $root, $nextorder;
	$rackwidth = getRackImageWidth() * getConfigVar ('ROW_SCALE');
	$order = 'odd';
	startPortlet ('Racks');
	echo "<table border=0 cellspacing=5 align='center'><tr>";
	foreach ($rackList as $rack)
	{
		echo "<td align=center class=row_${order}><a href='${root}?page=rack&rack_id=${rack['id']}'>";
		echo "<img border=0 width=${rackwidth} height=" . (getRackImageHeight ($rack['height']) * getConfigVar ('ROW_SCALE'));
		echo " title='${rack['height']} units'";
		echo "src='render_image.php?img=minirack&rack_id=${rack['id']}'>";
		echo "<br>${rack['name']}</a></td>";
		$order = $nextorder[$order];
	}
	echo "</tr></table>\n";
	finishPortlet();
	echo "</td></tr>";

	echo "<tr><td class=pcleft>";
	renderTagFilterPortlet ($tagfilter, 'rack', 'row_id', $row_id);
	echo "</td></tr></table>";
}

function showError ($info = '', $funcname = 'N/A')
{
	global $root;
	echo "<div class=msg_error>An error has occured in [${funcname}]. ";
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
		showError ('Invalid rack_id', __FUNCTION__);
		return;
	}
	if (($rackData = getRackData ($rack_id)) == NULL)
	{
		showError ('getRackData() failed', __FUNCTION__);
		return;
	}
	global $root, $pageno, $tabno;
	markAllSpans ($rackData);
	if ($hl_obj_id > 0)
		highlightObject ($rackData, $hl_obj_id);
	markupObjectProblems ($rackData);
	$prev_id = getPrevIDforRack ($rackData['row_id'], $rack_id);
	$next_id = getNextIDforRack ($rackData['row_id'], $rack_id);
	echo "<center><table border=0><tr valign=middle>";
	echo "<td><h2><a href='${root}?page=row&row_id=${rackData['row_id']}'>${rackData['row_name']}</a> :</h2></td>";
	// FIXME: use 'bypass'?
	if ($prev_id != NULL)
	{
		echo "<td><a href='${root}?page=rack&rack_id=${prev_id}'>";
		printImageHREF ('prev', 'previous rack');
		echo "</a></td>";
	}
	echo "<td><h2><a href='${root}?page=rack&rack_id=${rackData['id']}'>${rackData['name']}</a></h2></td>";
	if ($next_id != NULL)
	{
		echo "<td><a href='${root}?page=rack&rack_id=${next_id}'>";
		printImageHREF ('next', 'next rack');
		echo "</a></td>";
	}
	echo "</h2></td></tr></table>\n";
	if ($rackData['left_is_front'] == 'yes')
		$markup = array ('left' => 'Front', 'right' => 'Back');
	else
		$markup = array ('left' => 'Back', 'right' => 'Front');
	echo "<table class=rack border=0 cellspacing=0 cellpadding=1>\n";
	echo "<tr><th width='10%'>&nbsp;</th><th width='20%'>${markup['left']}</th>";
	echo "<th width='50%'>Interior</th><th width='20%'>${markup['right']}</th></tr>\n";
	for ($i = $rackData['height']; $i > 0; $i--)
	{
		echo '<tr><th>' . ($rackData['bottom_is_unit1'] == 'yes' ? $i : $rackData['height'] - $i + 1) . '</th>';
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

function renderNewRackForm ($row_id)
{
	global $pageno, $tabno;
	$log = array();
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();

	// Look for current submit.
	if (isset ($_REQUEST['got_data']))
	{
		assertStringArg ('rack_name', __FUNCTION__);
		assertUIntArg ('rack_height1', __FUNCTION__);
		assertStringArg ('rack_comment', __FUNCTION__, TRUE);
		$name = $_REQUEST['rack_name'];

		if (commitAddRack ($name, $_REQUEST['rack_height1'], $row_id, $_REQUEST['rack_comment'], $taglist) === TRUE)
			$log[] = array ('code' => 'success', 'message' => "Added new rack '${name}'");
		else
			$log[] = array ('code' => 'error', 'message' => __FUNCTION__ . ': commitAddRack() failed');
	}
	elseif (isset ($_REQUEST['got_mdata']))
	{
		assertUIntArg ('rack_height2', __FUNCTION__);
		assertStringArg ('rack_names', __FUNCTION__, TRUE);
		// copy-and-paste from renderAddMultipleObjectsForm()
		$names1 = explode ('\n', $_REQUEST['rack_names']);
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
			if (commitAddRack ($cname, $_REQUEST['rack_height2'], $row_id, '', $taglist) === TRUE)
				$log[] = array ('code' => 'success', 'message' => "Added new rack '${cname}'");
			else
				$log[] = array ('code' => 'error', 'message' => __FUNCTION__ . ': commitAddRack() failed');
	}
	printLog ($log);

	echo "<table border=0 width='100%'><tr><td valign=top>";
	// Render a form for the next.
	startPortlet ('Add one');
	echo '<form>';
	echo "<input type=hidden name=page value=${pageno}>";
	echo "<input type=hidden name=tab value=${tabno}>";
	echo "<input type=hidden name=row_id value=${row_id}>";
	echo '<table border=0 align=center>';
	$defh = getConfigVar ('DEFAULT_RACK_HEIGHT');
	if ($defh == 0)
		$defh = '';
	echo "<tr><th class=tdright>Rack name (*):</th><td class=tdleft><input type=text name=rack_name tabindex=1></td></tr>\n";
	echo "<tr><th class=tdright>Height in units (*):</th><td class=tdleft><input type=text name=rack_height1 tabindex=2 value='${defh}'></td></tr>\n";
	echo "<tr><th class=tdright>Comment:</th><td class=tdleft><input type=text name=rack_comment tabindex=3></td></tr>\n";
	echo "<tr><td class=submit colspan=2><input type=submit name=got_data value='Add'></td></tr>\n";
	echo '</table>';
	finishPortlet();
	echo '</td>';

	echo '<td rowspan=2 valign=top>';
	startPortlet ('Pre-assigned tags');
	renderTagSelect();
	finishPortlet();
	echo '</td></tr>';

	echo '<tr><td valign=top>';
	startPortlet ('Add many');
	echo '<table border=0 align=center>';
	$defh = getConfigVar ('DEFAULT_RACK_HEIGHT');
	if ($defh == 0)
		$defh = '';
	echo "<tr><th class=tdright>Height in units (*):</th><td class=tdleft><input type=text name=rack_height2 value='${defh}'></td></tr>\n";
	echo "<tr><th class=tdright>Rack names (*):</th><td class=tdleft><textarea name=rack_names cols=40 rows=25></textarea></td></tr>\n";
	echo "<tr><td class=submit colspan=2><input type=submit name=got_mdata value='Add'></td></tr>\n";
	echo '</form></table>';
	finishPortlet();
	echo '</td></tr>';
	echo '</table>';
}

function renderEditObjectForm ($object_id)
{
	showMessageOrError();

	global $pageno, $tabno, $root;
	$object = getObjectInfo ($object_id);
	if ($object == NULL)
	{
		showError ('getObjectInfo() failed', __FUNCTION__);
		return;
	}
	echo '<table border=0 width=100%><tr>';

	echo '<td class=pcleft>';
	startPortlet ('Static attributes');
	echo "<form method=post action='${root}process.php?page=${pageno}&tab=${tabno}&op=update'>";
	echo "<input type=hidden name=object_id value=${object_id}>";
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Type:</th><td class=tdleft>";
	printSelect (getObjectTypeList(), 'object_type_id', $object['objtype_id']);
	echo "</td></tr>\n";
	// baseline info
	echo "<tr><th class=tdright>Common name:</th><td class=tdleft><input type=text name=object_name value='${object['name']}'></td></tr>\n";
	echo "<tr><th class=tdright>Visible label:</th><td class=tdleft><input type=text name=object_label value='${object['label']}'></td></tr>\n";
	echo "<tr><th class=tdright>Asset tag:</th><td class=tdleft><input type=text name=object_asset_no value='${object['asset_no']}'></td></tr>\n";
	echo "<tr><th class=tdright>Barcode:</th><td class=tdleft><input type=text name=object_barcode value='${object['barcode']}'></td></tr>\n";
	echo "<tr><th class=tdright>Has problems:</th><td class=tdleft><input type=checkbox name=object_has_problems";
	if ($object['has_problems'] == 'yes')
		echo ' checked';
	echo "></td></tr>\n";
	echo "<tr><td colspan=2><b>Comment:</b><br><textarea name=object_comment rows=10 cols=80>${object['comment']}</textarea></td></tr>";
	echo "<tr><th class=submit colspan=2>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>\n";
	echo '</form></table><br>';
	finishPortlet();
	echo '</td>';
	
	// stickers
	echo '<td class=pcright>';
	startPortlet ('Optional attributes');
	$values = getAttrValues ($object_id);
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>&nbsp;</th><th>Attribute</th><th>Value</th><th>&nbsp;</th></tr>\n";
	echo "<form method=post action='${root}process.php?page=${pageno}&tab=${tabno}&op=updateStickers'>\n";
	echo "<input type=hidden name=object_id value=${object_id}>\n";
	echo '<input type=hidden name=num_attrs value=' . count($values) . ">\n";

	$i = 0;
	foreach ($values as $record)
	{
		echo "<input type=hidden name=${i}_attr_id value=${record['id']}>";
		echo '<tr><td>';
		if (!empty ($record['value']))
		{
			echo "<a href='${root}process.php?page=${pageno}&tab=${tabno}&op=clearSticker&object_id=${object_id}&attr_id=${record['id']}'>";
			printImageHREF ('clear', 'Clear value');
			echo '</a>';
		}
		else
			echo '&nbsp;';
		echo '</td>';
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
				$chapter[0] = '-- NOT SET --';
				printSelect ($chapter, "${i}_value", $record['key']);
				break;
		}
		echo "</td></tr>\n";
		$i++;
	}
	echo "<tr><td colspan=3>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>\n";
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
		assertUIntArg ('rack_row_id', __FUNCTION__);
		assertUIntArg ('rack_height', __FUNCTION__);
		assertStringArg ('rack_name', __FUNCTION__);
		assertStringArg ('rack_comment', __FUNCTION__, TRUE);
		$row_id = $_REQUEST['rack_row_id'];
		$height = $_REQUEST['rack_height'];
		$name = $_REQUEST['rack_name'];
		$comment = $_REQUEST['rack_comment'];

		if (commitUpdateRack ($rack_id, $name, $height, $row_id, $comment) === TRUE)
			$log[] = array ('code' => 'success', 'message' => "Updated rack '${name}'");
		else
			$log[] = array ('code' => 'error', 'message' => __FUNCTION__ . ': commitUpdateRack() failed');
		resetThumbCache ($rack_id);
		printLog ($log);
	}

	global $pageno, $tabno;
	$rack = getRackData ($rack_id);
	if ($rack == NULL)
	{
		showError ('getRackData() failed', __FUNCTION__);
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
	printSelect (readChapter ('RackRow'), 'rack_row_id', $rack['row_id']);
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
	// First collect all data for OPTGROUPs, then ouput it and dump
	// the rest of records as is.
	$optgroup = array();
	$other = array();
	foreach ($rowList as $dict_key => $dict_value)
	{
		if (strpos ($dict_value, '%GSKIP%') !== FALSE)
		{
			$tmp = explode ('%GSKIP%', $dict_value, 2);
			$optgroup[$tmp[0]][$dict_key] = $tmp[1];
		}
		elseif (strpos ($dict_value, '%GPASS%') !== FALSE)
		{
			$tmp = explode ('%GPASS%', $dict_value, 2);
			$optgroup[$tmp[0]][$dict_key] = $tmp[1];
		}
		else
			$other[$dict_key] = $dict_value;
	}
	echo "<select name=${select_name}>";
	if (!count ($optgroup))
	{
		foreach ($other as $dict_key => $dict_value)
		{
			echo "<option value=${dict_key}";
			if ($dict_key == $selected_id)
				echo ' selected';
			echo ">${dict_value}</option>";
		}
	}
	else
	{
		foreach ($optgroup as $groupname => $groupdata)
		{
			echo "<optgroup label='${groupname}'>";
			foreach ($groupdata as $dict_key => $dict_value)
			{
				echo "<option value=${dict_key}";
				if ($dict_key == $selected_id)
					echo ' selected';
				echo ">${dict_value}</option>";
			}
			echo "</optgroup>\n";
		}
		if (count ($other))
		{
			echo "<optgroup label='other'>\n";
			foreach ($other as $dict_key => $dict_value)
			{
				echo "<option value=${dict_key}";
				if ($dict_key == $selected_id)
					echo ' selected';
				echo ">${dict_value}</option>";
			}
			echo "</optgroup>\n";
		}
	}
	echo "</select>";
}

// used by renderGridForm() and renderRackPage()
function renderRackInfoPortlet ($rackData)
{
	global $root;
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>Rack row:</th><td class=tdleft>${rackData['row_name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Name:</th><td class=tdleft>${rackData['name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Height:</th><td class=tdleft>${rackData['height']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Utilization:</th><td class=tdleft>";
	renderProgressBar (getRSUforRack ($rackData));
	echo "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Objects:</th><td class=tdleft>";
	echo getObjectCount ($rackData);
	echo "</td></tr>\n";
	printTagTRs ("${root}?page=rackspace&");
	if (!empty ($rackData['comment']))
		echo "<tr><th width='50%' class=tdright>Comment:</th><td class=tdleft>${rackData['comment']}</td></tr>\n";
	echo '</table>';
	finishPortlet();
}

// This is a universal editor of rack design/waste.
function renderGridForm ($rack_id = 0, $filter, $header, $submit, $state1, $state2)
{
	if ($rack_id == 0)
	{
		showError ('Invalid rack_id', __FUNCTION__);
		return;
	}
	if (($rackData = getRackData ($rack_id)) == NULL)
	{
		showError ('getRackData() failed', __FUNCTION__);
		return;
	}

	global $root, $pageno, $tabno;
	$filter ($rackData);
	markupObjectProblems ($rackData);

	// Process form submit.
	if (isset ($_REQUEST['do_update']))
	{
		$log[] = processGridForm ($rackData, $state1, $state2);
		printLog ($log);
		$rackData = getRackData ($rack_id);
		$filter ($rackData);
		markupObjectProblems ($rackData);
	}

	// Render the result whatever it is.
	// Main layout.
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${rackData['name']}</h1></td></tr>\n";

	// Left column with information portlet.
	echo "<tr><td class=pcleft height='1%' width='50%'>";
	renderRackInfoPortlet ($rackData);
	echo "</td>\n";
	echo "<td class=pcright>";

	// Grid form.
	startPortlet ($header);
	echo "<center>\n";
	echo "<table class=rack border=0 cellspacing=0 cellpadding=1>\n";
	echo "<tr><th width='10%'>&nbsp;</th><th width='20%'>Front</th>";
	echo "<th width='50%'>Interior</th><th width='20%'>Back</th></tr>\n";
	echo "<form method=post action='${root}?'>\n";
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
	global $root, $nextorder, $aac;
	if ($object_id <= 0)
	{
		showError ('Invalid object_id', __FUNCTION__);
		return;
	}
	$info = getObjectInfo ($object_id);
	if ($info == NULL)
	{
		showError ('getObjectInfo() failed', __FUNCTION__);
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
	echo "<tr><th width='50%' class=tdright>Object type:</th>";
	echo "<td class=tdleft><a href='${root}?page=objgroup&group_id=${info['objtype_id']}&hl_object_id=${object_id}'>${info['objtype_name']}</a></td></tr>\n";
	if (!empty ($info['asset_no']))
		echo "<tr><th width='50%' class=tdright>Asset tag:</th><td class=tdleft>${info['asset_no']}</td></tr>\n";
	elseif (in_array ($info['objtype_id'], explode (',', getConfigVar ('REQUIRE_ASSET_TAG_FOR'))))
		echo "<tr><td colspan=2 class=msg_error>Asset tag is missing.</td></tr>\n";
	if (!empty ($info['label']))
		echo "<tr><th width='50%' class=tdright>Visible label:</th><td class=tdleft>${info['label']}</td></tr>\n";
	if (!empty ($info['barcode']))
		echo "<tr><th width='50%' class=tdright>Barcode:</th><td class=tdleft>${info['barcode']}</td></tr>\n";
	if ($info['has_problems'] == 'yes')
		echo "<tr><td colspan=2 class=msg_error>Has problems</td></tr>\n";
	foreach (getAttrValues ($object_id, TRUE) as $record)
		if (!empty ($record['value']))
			echo "<tr><th width='50%' class=opt_attr_th>${record['name']}:</th><td class=tdleft>${record['a_value']}</td></tr>\n";
	printTagTRs ("${root}?page=objgroup&group_id=${info['objtype_id']}&");
	echo "</table><br>\n";
	finishPortlet();

	if (!empty ($info['comment']))
	{
		startPortlet ('Comment');
		echo '<div class=commentblock>' . string_insert_hrefs ($info['comment']) . '</div>';
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
				assertUIntArg ('hl_port_id', __FUNCTION__);
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
					echo "<td><a href='${root}?page=object&object_id=${port['remote_object_id']}&hl_port_id=${port['remote_id']}'>${port['remote_object_name']}</a></td>";
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
		echo "<tr><th>OS interface</th><th>IP address</th><th>description</th><th>misc</th></tr>\n";
		$hl_ipv4_addr = '';
		if (isset ($_REQUEST['hl_ipv4_addr']))
		{
			assertIPv4Arg ('hl_ipv4_addr', __FUNCTION__);
			$hl_ipv4_addr = $_REQUEST['hl_ipv4_addr'];
		}
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

			echo "<tr";
			if ($addr['address_reserved']=='yes')
				echo ' class=trerror';
			elseif ($addr['type']!='virtual' && $regnum>0)
				echo ' class=trerror';
			elseif ($addr['type']=='regular' && $sharednum>0)
				echo ' class=trerror';

			if ($hl_ipv4_addr == $addr['ip'])
				echo ' class=port_highlight';
			echo "><td class=tdleft>${addr['name']}</td><td class=tdleft>";
			echo "<a href='${root}?page=ipaddress&ip=${addr['ip']}&hl_object_id=${object_id}'>";
			echo "${addr['ip']}</a></td><td class='description'>$address_name</td><td class=tdleft>\n";

			if ($addr['address_reserved']=='yes')
				echo "<b>Reserved;</b> ";

			echo $aac[$addr['type']];
			switch ($addr['type'])
			{
				case 'virtual':
					if ($notvirtnum > 0)
					{
						echo " Owners: ";
						printRefsOfType($addr['references'], 'virtual', 'neq');
					}
					break;
				case 'router':
					break;
				case 'shared':
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
					break;
				case 'regular':
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
					break;
				default:
					echo __FUNCTION__ . '(): internal error! ';
					break;
			}
			echo "</td></tr>\n";
		}
		echo "</table><br>\n";
		finishPortlet();
	}

	$forwards = getNATv4ForObject ($object_id);
	if (count($forwards['in']) or count($forwards['out']))
	{
		startPortlet('NATv4');

		if (count($forwards['out']))
		{

			echo "<h3>locally performed NAT</h3>";

			echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>\n";
			echo "<tr><th>Proto</th><th>Match endpoint</th><th>Translate to</th><th>Target object</th><th>Rule comment</th></tr>\n";

			foreach ($forwards['out'] as $pf)
			{
				$class='trerror';
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
				$address = getIPv4Address ($pf['remoteip']);
				echo "<td class='description'>";
				if (count ($address['allocs']))
					foreach($address['allocs'] as $bond)
						echo "<a href='${root}?page=object&tab=default&object_id=${bond['object_id']}'>${bond['object_name']}(${bond['name']})</a> ";
				elseif (!empty ($pf['remote_addr_name']))
					echo '(' . $pf['remote_addr_name'] . ')';
				echo "</td><td class='description'>${pf['description']}</td></tr>";
			}
			echo "</table><br><br>";
		}
		if (count($forwards['in']))
		{
			echo "<h3>arriving NAT connections</h3>";
			echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>\n";
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

	$pools = getRSPoolsForObject ($object_id);
	if (count ($pools))
	{
		$order = 'odd';
		startPortlet ('Real server pools');
		echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
		echo "<tr><th>VS</th><th>RS pool</th><th>RS</th><th>VS config</th><th>RS config</th></tr>\n";
		foreach ($pools as $vs_id => $info)
		{
			echo "<tr valign=top class=row_${order}><td class=tdleft><a href='${root}?page=ipv4vs&vs_id=${vs_id}'>";
			echo buildVServiceName ($info);
			echo '</a>';
			if (!empty ($info['name']))
				echo "<br>${info['name']}";
			echo "</td><td class=tdleft><a href='${root}?page=ipv4rsp&pool_id=${info['pool_id']}'>";
			echo (empty ($info['pool_name']) ? 'ANONYMOUS' : $info['pool_name']);
			echo '</a></td><td class=tdleft>' . $info['rscount'] . '</td>';
			echo "<td class=tdleft><pre>${info['vsconfig']}</pre></td>";
			echo "<td class=tdleft><pre>${info['rsconfig']}</pre></td>";
			echo "</tr>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n";
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
	// Transform the given flat list into a list of groups, each representing a rack row.
	$rdata = array();
	foreach ($racks as $rack)
		if (!isset ($rdata[$rack['row_name']]))
			$rdata[$rack['row_name']] = array ($rack['id'] => $rack['name']);
		else
			$rdata[$rack['row_name']][$rack['id']] = $rack['name'];
	echo "<select name=${sname} multiple size=" . getConfigVar ('MAXSELSIZE') . " onchange='getElementById(\"racks\").submit()'>\n";
	foreach ($rdata as $optgroup => $racklist)
	{
		echo "<optgroup label='${optgroup}'>";
		foreach ($racklist as $rack_id => $rack_name)
		{
			echo "<option value=${rack_id}";
			if (!(array_search ($rack_id, $selected) === FALSE))
				echo ' selected';
			echo">${rack_name}</option>\n";
		}
	}
	echo "</select>\n";
}

function showMessageOrError ()
{
	if (isset ($_REQUEST['message']))
		echo "<div class=msg_success>${_REQUEST['message']}</div>";
	elseif (isset ($_REQUEST['error']))
		echo "<div class=msg_error>${_REQUEST['error']}</div>";
	elseif (isset ($_REQUEST['log']))
		printLog (unserialize (base64_decode ($_REQUEST['log'])));
}

// This function renders a form for port edition.
function renderPortsForObject ($object_id = 0)
{
	global $root, $pageno, $tabno;
	if ($object_id <= 0)
	{
		showError ('Invalid object_id', __FUNCTION__);
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
		echo "<td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</td></form></tr>\n";
	}
	echo "<form action='${root}process.php'><tr><td>";
	printImageHREF ('add', '', TRUE, 104);
	echo "</td><td><input type=text size=8 name=port_name tabindex=100></td>\n";
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
	echo "<td colspan=4>&nbsp;</td></tr></form>";
	echo "</table><br>\n";
	finishPortlet();

	startPortlet ('Add/update multiple ports');
	echo "<form action=${root}process.php method=post>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=object_id value='${object_id}'>\n";
	echo "<input type=hidden name=op value=addMultiPorts>";
	echo 'Format: <select name=format>';
	echo '<option value=c3600asy>Cisco 3600 async: sh line | inc TTY</option>';
	echo '<option value=fiwg selected>Foundry ServerIron/FastIron WorkGroup/Edge: sh int br</option>';
	echo '<option value=fisxii>Foundry FastIron SuperX/II4000: sh int br</option>';
	echo '<option value=ssv1>SSV:&lt;interface name&gt; &lt;MAC address&gt;</option>';
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

function renderIPv4ForObject ($object_id = 0)
{
	global $root, $pageno, $tabno, $aat;
	if ($object_id <= 0)
	{
		showError ('Invalid object_id', __FUNCTION__);
		return;
	}
	showMessageOrError();
	startPortlet ('Allocations');
	$addresses = getObjectAddresses ($object_id);
	usort($addresses, 'sortAddresses');
	echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	echo "<tr><th>&nbsp;</th><th>OS interface</th><th>IP address</th><th>description</th><th>type</th><th>misc</th><th>&nbsp</th></tr>\n";
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
			$class='trerror';
		elseif ($addr['type']!='virtual' && $regnum>0)
			$class='trerror';
		elseif ($addr['type']=='regular' && $sharednum>0)
			$class='trerror';
		else 
			$class='';

		echo "<form action='${root}process.php'>";
		echo "<input type=hidden name=page value='${pageno}'>\n";
		echo "<input type=hidden name=tab value='${tabno}'>\n";
		echo "<input type=hidden name=op value=updIPv4Allocation>";
		echo "<input type=hidden name=object_id value='$object_id'>";
		echo "<input type=hidden name=ip value='${addr['ip']}'>";
		echo "<tr class='$class'><td><a href='${root}process.php?op=delIPv4Allocation&page=${pageno}&tab=${tabno}&ip=${addr['ip']}&object_id=$object_id'>";
		printImageHREF ('delete', 'Delete this IPv4 address');
		echo "</a></td>";
		echo "<td class=tdleft><input type='text' name='bond_name' value='${addr['name']}' size=10></td>";
		echo "<td class=tdleft><a href='${root}?page=ipaddress&ip=${addr['ip']}'>${addr['ip']}</a></td>";
		echo "<td class='description'>$address_name</td>\n<td>";
		printSelect ($aat, 'bond_type', $addr['type']);
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

		echo "</td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</td></form></tr>\n";
	}


	echo "<form action='${root}process.php'><tr><td>";
	printImageHREF ('add', 'Allocate new address', TRUE, 99);
	echo "</td><td class=tdleft>";
	echo "<input type='text' size='10' name='bond_name' tabindex=100></td>\n";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=op value=addIPv4Allocation>\n";
	echo "<input type=hidden name=object_id value='$object_id'>\n";
	echo "<td class=tdleft><input type=text name='ip' tabindex=101>\n";
	echo "</td><td>&nbsp;</td><td>";
	printSelect ($aat, 'bond_type');
	echo "</td><td colspan=2>&nbsp;</td></tr></form>";
	echo "</table><br>\n";
	finishPortlet();

}

// Log structure versions:
// 1: the whole structure is a list of code-message pairs
// 2 and later: there's a "v" field set, which indicates the version
// 2: there's a "m" list set to hold message code and optional arguments
function printLog ($log)
{
	switch (TRUE)
	{
		case !isset ($log['v']):
		case $log['v'] == 1:
			foreach ($log as $key => $record)
				if ($key !== 'v')
					echo "<div class=msg_${record['code']}>${record['message']}</div>";
			break;
		case $log['v'] == 2:
			$msginfo = array
			(
				0 => array ('code' => 'success', 'format' => 'Success: %s'),
				1 => array ('code' => 'success', 'format' => '%u new records done, %u already existed'),
				2 => array ('code' => 'success', 'format' => 'NATv4 rule was successfully added.'),
				3 => array ('code' => 'success', 'format' => 'NATv4 rule was successfully deleted.'),
				4 => array ('code' => 'success', 'format' => 'NATv4 rule was successfully updated'),
				5 => array ('code' => 'success', 'format' => 'Port %s was added successfully'),
				6 => array ('code' => 'success', 'format' => 'Port %s was updated successfully'),
				7 => array ('code' => 'success', 'format' => 'Port %s was deleted successfully'),
				8 => array ('code' => 'success', 'format' => 'Port %s successfully linked with port %s at object %s'),
				9 => array ('code' => 'success', 'format' => 'Port %s was successfully unlinked from %s@%s'),
				10 => array ('code' => 'success', 'format' => 'Added %u ports, updated %u ports, encountered %u errors.'),
				11 => array ('code' => 'success', 'format' => 'Reservation removed.'),
				12 => array ('code' => 'success', 'format' => 'allocation updated'),
				13 => array ('code' => 'success', 'format' => 'allocated'),
				14 => array ('code' => 'success', 'format' => 'deallocated'),
				15 => array ('code' => 'success', 'format' => 'Reset succeeded.'),
				16 => array ('code' => 'success', 'format' => 'Update done'),
				17 => array ('code' => 'success', 'format' => 'Update(s) succeeded.'),
				18 => array ('code' => 'success', 'format' => 'Load balancer was successfully added'),
				19 => array ('code' => 'success', 'format' => 'Load balancer was successfully deleted'),
				20 => array ('code' => 'success', 'format' => 'Load balancer info was successfully updated'),
				21 => array ('code' => 'success', 'format' => 'Generation complete'),
				22 => array ('code' => 'success', 'format' => 'Chained %u tags'),
				23 => array ('code' => 'success', 'format' => 'IPv4 prefix successfully added'),
				24 => array ('code' => 'success', 'format' => 'IPv4 prefix deleted'),
				25 => array ('code' => 'success', 'format' => 'IPv4 prefix updated'),
				26 => array ('code' => 'success', 'format' => '%u IP address(es) were successfully updated'),
				27 => array ('code' => 'success', 'format' => 'IPv4 address updated'),
				28 => array ('code' => 'success', 'format' => 'Virtual service was successfully created'),
				29 => array ('code' => 'success', 'format' => 'Virtual service was successfully deleted'),
				30 => array ('code' => 'success', 'format' => 'Virtual service was successfully updated'),
				31 => array ('code' => 'success', 'format' => 'RS pool was successfully created'),
				32 => array ('code' => 'success', 'format' => 'RS pool was successfully deleted'),
				33 => array ('code' => 'success', 'format' => 'RS pool was successfully updated'),
				34 => array ('code' => 'success', 'format' => 'Real server was successfully added'),
				35 => array ('code' => 'success', 'format' => 'Real server was successfully deleted'),
				36 => array ('code' => 'success', 'format' => 'Real server was successfully updated'),
				37 => array ('code' => 'success', 'format' => 'Successfully added %u real servers'),
				38 => array ('code' => 'success', 'format' => '%u real server(s) were successfully (de)activated'),
				39 => array ('code' => 'success', 'format' => 'User account %s updated.'),
				40 => array ('code' => 'success', 'format' => 'User account %s created.'),
				41 => array ('code' => 'success', 'format' => 'User account disabled.'),
				42 => array ('code' => 'success', 'format' => 'User account enabled.'),
				43 => array ('code' => 'success', 'format' => 'Saved successfully.'),
				44 => array ('code' => 'success', 'format' => '%s failures and %s successfull changes.'),
				45 => array ('code' => 'success', 'format' => "Attribute '%s' created."),
				46 => array ('code' => 'success', 'format' => 'Rename successful.'),
				47 => array ('code' => 'success', 'format' => 'Attribute was deleted.'),
				48 => array ('code' => 'success', 'format' => 'Supplement succeeded.'),
				49 => array ('code' => 'success', 'format' => 'Reduction succeeded.'),
				50 => array ('code' => 'success', 'format' => 'Reduction succeeded.'),
				51 => array ('code' => 'success', 'format' => 'Update succeeded.'),
				52 => array ('code' => 'success', 'format' => 'Supplement succeeded.'),
				53 => array ('code' => 'success', 'format' => 'Chapter was deleted.'),
				54 => array ('code' => 'success', 'format' => 'Chapter was updated.'),
				55 => array ('code' => 'success', 'format' => 'Chapter was added.'),
				56 => array ('code' => 'success', 'format' => 'Update succeeded.'),
				57 => array ('code' => 'success', 'format' => 'Reset complete'),
				58 => array ('code' => 'success', 'format' => "Successfully deleted tag ."),
				59 => array ('code' => 'success', 'format' => "Created tag '%s'."),
				60 => array ('code' => 'success', 'format' => "Updated tag '%s'."),
				61 => array ('code' => 'success', 'format' => 'Password changed successfully.'),
				62 => array ('code' => 'success', 'format' => 'gw: %s'),
				63 => array ('code' => 'success', 'format' => '%u change request(s) have been processed'),
				64 => array ('code' => 'success', 'format' => 'Port %s@%s has been assigned to VLAN %u'),

				100 => array ('code' => 'error', 'format' => 'Generic error: %s'),
				101 => array ('code' => 'error', 'format' => 'Port name cannot be empty'),
				102 => array ('code' => 'error', 'format' => "Error creating user account '%s'"),
				103 => array ('code' => 'error', 'format' => 'getHashByID() failed'),
				104 => array ('code' => 'error', 'format' => "Error updating user account '%s'"),
				105 => array ('code' => 'error', 'format' => 'Error enabling user account.'),
				106 => array ('code' => 'error', 'format' => 'Error disabling user account.'),
				107 => array ('code' => 'error', 'format' => 'Admin account cannot be disabled'),
				108 => array ('code' => 'error', 'format' => '%u failures and %u successfull changes.'),
				109 => array ('code' => 'error', 'format' => 'Update failed!'),
				110 => array ('code' => 'error', 'format' => 'Supplement failed!'),
				111 => array ('code' => 'error', 'format' => 'Reduction failed!'),
				112 => array ('code' => 'error', 'format' => 'Error adding chapter.'),
				113 => array ('code' => 'error', 'format' => 'Error updating chapter.'),
				114 => array ('code' => 'error', 'format' => 'Error deleting chapter.'),
				115 => array ('code' => 'error', 'format' => 'Error renaming attribute.'),
				116 => array ('code' => 'error', 'format' => 'Error creating attribute.'),
				117 => array ('code' => 'error', 'format' => 'Error deleting attribute.'),
				118 => array ('code' => 'error', 'format' => 'Supplement failed!'),
				119 => array ('code' => 'error', 'format' => 'Reduction failed!'),
				120 => array ('code' => 'error', 'format' => 'Reset failed!'),
				121 => array ('code' => 'error', 'format' => 'commitUpdateObject() failed'),
				122 => array ('code' => 'error', 'format' => 'One or more update(s) failed!'),
				123 => array ('code' => 'error', 'format' => 'Cannot process submitted data: unknown format code.'),
				124 => array ('code' => 'error', 'format' => 'Error removing reservation!'),
				125 => array ('code' => 'error', 'format' => "Update failed with error: '%s'"),
				126 => array ('code' => 'error', 'format' => 'addRStoRSPool() failed'),
				127 => array ('code' => 'error', 'format' => 'Added %u real servers and encountered %u errors'),
				128 => array ('code' => 'error', 'format' => 'commitDeleteRS() failed'),
				129 => array ('code' => 'error', 'format' => 'commitDeleteLB() failed'),
				130 => array ('code' => 'error', 'format' => 'commitDeleteVS() failed'),
				131 => array ('code' => 'error', 'format' => 'invalid format requested'),
				132 => array ('code' => 'error', 'format' => 'invalid protocol'),
				133 => array ('code' => 'error', 'format' => 'commitUpdateRS() failed'),
				134 => array ('code' => 'error', 'format' => 'commitUpdateLB() failed'),
				135 => array ('code' => 'error', 'format' => 'commitUpdateVS() failed'),
				136 => array ('code' => 'error', 'format' => 'addLBtoRSPool() failed'),
				137 => array ('code' => 'error', 'format' => 'addLBtoRSPool() failed'),
				138 => array ('code' => 'error', 'format' => 'commitDeleteRSPool() failed'),
				139 => array ('code' => 'error', 'format' => 'commitUpdateRSPool() failed'),
				140 => array ('code' => 'error', 'format' => 'Encountered %u errors, (de)activated %u real servers'),
				141 => array ('code' => 'error', 'format' => 'Encountered %u errors, updated %u IP address(es)'),
				142 => array ('code' => 'error', 'format' => 'executeAutoPorts() failed'),
				143 => array ('code' => 'error', 'format' => 'Tried chaining %u tags, but experienced %u errors.'),
				144 => array ('code' => 'error', 'format' => "Error deleting tag: '%s'"),
				145 => array ('code' => 'error', 'format' => "Invalid tag name '%s'"),
				146 => array ('code' => 'error', 'format' => "Tag '%s' (or similar name) already exists"),
				147 => array ('code' => 'error', 'format' => "Could not create tag '%s' because of error '%s'"),
				148 => array ('code' => 'error', 'format' => "Could not update tag '%s' because of error '%s'"),
				149 => array ('code' => 'error', 'format' => 'Turing test failed'),
				150 => array ('code' => 'error', 'format' => 'Can only change password under DB authentication.'),
				151 => array ('code' => 'error', 'format' => 'Old password doesn\'t match.'),
				152 => array ('code' => 'error', 'format' => 'New passwords don\'t match.'),
				153 => array ('code' => 'error', 'format' => 'Password change failed.'),
				154 => array ('code' => 'error', 'format' => "Verification error: %s"),
				155 => array ('code' => 'error', 'format' => 'Save failed.'),
				156 => array ('code' => 'error', 'format' => 'getSwitchVLANs() failed'),
				157 => array ('code' => 'error', 'format' => 'operation not permitted'),
				158 => array ('code' => 'error', 'format' => 'Ignoring malformed record #%u in form submit'),
				159 => array ('code' => 'error', 'format' => 'Permission denied moving port %s from VLAN%u to VLAN%u'),
				160 => array ('code' => 'error', 'format' => 'Invalid arguments'),
				161 => array ('code' => 'error', 'format' => 'Endpoint not found. Please either set FQDN attribute or assign an IP address to the object.'),
				162 => array ('code' => 'error', 'format' => 'More than one IP address is assigned to this object, please configure FQDN attribute.'),
				163 => array ('code' => 'error', 'format' => 'Failed to get any response from queryGateway() or the gateway died'),
				164 => array ('code' => 'error', 'format' => 'Gateway failure: %s.'),
				165 => array ('code' => 'error', 'format' => 'Gateway failure: malformed reply.'),
				166 => array ('code' => 'error', 'format' => 'gw: %s'),
				167 => array ('code' => 'error', 'format' => 'Could not find port %s'),
				168 => array ('code' => 'error', 'format' => 'Port %s is a trunk'),
				169 => array ('code' => 'error', 'format' => 'Failed to configure %s, connector returned code %u'),
				170 => array ('code' => 'error', 'format' => 'There is no network for IP address "%s"'),

				200 => array ('code' => 'warning', 'format' => 'generic warning: %s'),
				201 => array ('code' => 'warning', 'format' => 'nothing happened...'),
				202 => array ('code' => 'warning', 'format' => 'gw: %s'),
				203 => array ('code' => 'warning', 'format' => 'Port %s seems to be the first in VLAN %u at this switch.'),
				204 => array ('code' => 'warning', 'format' => 'Check uplink/downlink configuration for proper operation.'),
				205 => array ('code' => 'warning', 'format' => '%u change request(s) have been ignored'),
			);
			// Handle the arguments. Is there any better way to do it?
			foreach ($log['m'] as $record)
			{
				if (!isset ($record['c']) or !isset ($msginfo[$record['c']]))
				{
					echo '<div class=msg_neutral>(this message was lost)</div>';
					continue;
				}
				if (isset ($record['a']))
					switch (count ($record['a']))
					{
						case 1:
							$msgtext = sprintf
							(
								$msginfo[$record['c']]['format'],
								$record['a'][0]
							);
							break;
						case 2:
							$msgtext = sprintf
							(
								$msginfo[$record['c']]['format'],
								$record['a'][0],
								$record['a'][1]
							);
							break;
						case 3:
							$msgtext = sprintf
							(
								$msginfo[$record['c']]['format'],
								$record['a'][0],
								$record['a'][1],
								$record['a'][2]
							);
							break;
						case 4:
						default:
							$msgtext = sprintf
							(
								$msginfo[$record['c']]['format'],
								$record['a'][0],
								$record['a'][1],
								$record['a'][2],
								$record['a'][3]
							);
							break;
					}
				else
					$msgtext = $msginfo[$record['c']]['format'];
				echo '<div class=msg_' . $msginfo[$record['c']]['code'] . ">${msgtext}</div>";
			}
			break;
		default:
			echo '<div class=msg_error>' . __FUNCTION__ . ': internal error</div>';
			break;
	}
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
		showError ('Invalid object_id', __FUNCTION__);
		return;
	}
	$is_submit = isset ($_REQUEST['got_atoms']);
	$is_update = isset ($_REQUEST['rackmulti'][0]);
	$info = getObjectInfo ($object_id);
	if ($info == NULL)
	{
		showError ('getObjectInfo() failed', __FUNCTION__);
		return;
	}
	// Always process occupied racks plus racks chosen by user. First get racks with
	// already allocated rackspace...
	$workingRacksData = getResidentRacksData ($object_id);
	if ($workingRacksData === NULL)
	{
		print_r ($workingRacksData);
		showError ('getResidentRacksData() failed', __FUNCTION__);
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
					showError ('getRackData() failed', __FUNCTION__);
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
					$log[] = array ('code' => 500, 'message' => 'Working copy update failed in ', __FUNCTION__);
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
	if (count ($allRacksData) <= getConfigVar ('RACK_PRESELECT_THRESHOLD'))
	{
		foreach (array_keys ($allRacksData) as $rack_id)
		{
			$rackData = getRackData ($rack_id);
			if ($rackData == NULL)
			{
				showError ('getRackData() failed', __FUNCTION__);
				return NULL;
			}
			$workingRacksData[$rack_id] = $rackData;
		}
		foreach ($workingRacksData as &$rackData)
			applyObjectMountMask ($rackData, $object_id);
		unset ($rackData);
	}
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
	foreach ($mdata as $rua)
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
	foreach ($rackpack as $rackData)
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
		showError ('getUnmountedObjects() failed', __FUNCTION__);
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
		showError ('getProblematicObjects() failed', __FUNCTION__);
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

function renderObjectSpace ()
{
	global $root, $taglist, $tagtree;
	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft width='50%'>";
	startPortlet ('View all by type');
	$groupInfo = getObjectGroupInfo();
	if ($groupInfo === NULL)
	{
		showError ('getObjectGroupInfo() failed', __FUNCTION__);
		return;
	}
	if (count ($groupInfo) == 0)
		echo "No objects exist in DB";
	else
	{
		echo '<div align=left><ul>';
		foreach ($groupInfo as $gi)
			echo "<li><a href='${root}?page=objgroup&group_id=${gi['id']}'>${gi['name']}</a> (${gi['count']})</li>";
		echo '</ul></div>';
	}
	finishPortlet();

	echo '</td><td class=pcright>';

	startPortlet ('View all by tag');
	if (count ($taglist) == 0)
		echo "No tags exist in DB";
	else
		renderTagCloud ('object');
	finishPortlet();
	echo "</td></tr></table>\n";
}

function renderObjectGroup ()
{
	global $root, $pageno, $tabno, $nextorder, $taglist, $tagtree;
	assertUIntArg ('group_id', __FUNCTION__, TRUE);
	$group_id = $_REQUEST['group_id'];
	$tagfilter = getTagFilter();
	$tagfilter_str = getTagFilterStr ($tagfilter);
	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft width='25%'>";
	startPortlet ('change type');
	$groupInfo = getObjectGroupInfo();
	if ($groupInfo === NULL)
	{
		showError ('getObjectGroupInfo() failed', __FUNCTION__);
		return;
	}
	if (count ($groupInfo) == 0)
		echo "No objects exist in DB";
	else
	{
		echo '<div align=left><ul>';
		foreach ($groupInfo as $gi)
		{
			echo "<li><a href='${root}?page=${pageno}&group_id=${gi['id']}${tagfilter_str}'>";
			if ($gi['id'] == $group_id)
				echo '<strong>';
			echo "${gi['name']}</a>";
			if ($gi['id'] == $group_id)
				echo '</strong>';
			echo " (${gi['count']})";
			if ($gi['id'] == $group_id)
				echo ' &larr;';
			echo "</li>";
		}
		echo '</ul></div>';
	}
	finishPortlet();

	echo '</td><td class=pcleft>';

	startPortlet ('Objects');
	$objects = getObjectList ($group_id, $tagfilter, getTFMode());
	if ($objects === NULL)
	{
		showError ('getObjectList() failed', __FUNCTION__);
		return;
	}
	echo '<br><br><table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
	echo '<tr><th>Common name</th><th>Visible label</th><th>Asset tag</th><th>Barcode</th><th>Rack</th></tr>';
	$order = 'odd';
	foreach ($objects as $obj)
	{
		if (isset ($_REQUEST['hl_object_id']) and $_REQUEST['hl_object_id'] == $obj['id'])
			$secondclass = 'tdleft port_highlight';
		else
			$secondclass = 'tdleft';
		echo "<tr class=row_${order}><td class='${secondclass}'><a href='${root}?page=object&object_id=${obj['id']}'>${obj['dname']}</a></td>";
		echo "<td class='${secondclass}'>${obj['label']}</td>";
		echo "<td class='${secondclass}'>${obj['asset_no']}</td>";
		echo "<td class='${secondclass}'>${obj['barcode']}</td>";
		if ($obj['rack_id'])
			echo "<td class='${secondclass}'><a href='${root}?page=rack&rack_id=${obj['rack_id']}'>${obj['Rack_name']}</a></td>";
		else
			echo "<td class='${secondclass}'>Unmounted</td>";
		echo '</tr>';
		$order = $nextorder[$order];
	}
	echo '</table>';
	finishPortlet();

	echo "</td><td class=pcright width='25%'>";

	renderTagFilterPortlet ($tagfilter, 'object', 'group_id', $group_id);
	echo "</td></tr></table>\n";
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

function renderAllIPv4Allocations ()
{
	$addresses = getAllIPv4Allocations();
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
			showError ("Uknown object type '${object_type}'", __FUNCTION__);
			return;
	}
	global $dbxlink;
	$result = $dbxlink->query ($query);
	if ($result == NULL)
	{
		showError ('SQL query failed', __FUNCTION__);
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
	global $root, $pageno;

	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";

	startPortlet ('Networks');
	echo "<table class='widetable' border=0 cellpadding=5 cellspacing=0 align='center'>\n";
	$tagfilter = getTagFilter();
	$addrspaceList = getAddressspaceList ($tagfilter, getTFMode());
	echo "<tr><th>prefix</th><th>name/tags</th><th>utilization</th></tr>\n";
	foreach ($addrspaceList as $iprange)
	{
		$netdata = getIPv4Network ($iprange['id']);
		$prefixtags = loadIPv4PrefixTags ($iprange['id']);
		$total = ($netdata['ip_bin'] | $netdata['mask_bin_inv']) - ($netdata['ip_bin'] & $netdata['mask_bin']) + 1;
		$used = count ($netdata['addrlist']);
		echo "<tr valign=top><td class=tdleft><a href='${root}?page=iprange&id=${iprange['id']}'>${iprange['ip']}/${netdata['mask']}</a></td>";
		echo "<td class=tdleft>${netdata['name']}";
		if (count ($prefixtags))
		{
			echo "<br>";
			echo serializeTags ($prefixtags, "${root}?page=${pageno}&");
		}
		echo "</td><td class=tdcenter>";
		renderProgressBar ($used/$total);
		echo "<br><small>${used}/${total}</small></td></tr>";
	}
	echo "</table>\n";
	finishPortlet();
	echo '</td><td class=pcright>';
	renderTagFilterPortlet ($tagfilter, 'ipv4net');
	echo "</td></tr></table>\n";
}

function renderIPv4SLB ()
{
	global $root, $page, $nextorder;

	startPortlet ('SLB configuration');
	echo "<table border=0 width='100%'><tr>";
	foreach (array ('ipv4vslist', 'ipv4rsplist', 'rservers', 'lbs') as $pno)
		echo "<td><h3><a href='${root}?page=${pno}'>" . $page[$pno]['title'] . "</a></h3></td>";
	echo '</tr></table>';
	finishPortlet();

	$summary = getSLBSummary();
	startPortlet ('SLB tactical overview');
	// A single id-keyed array isn't used here to preserve existing
	// order of LBs returned by getSLBSummary()
	$lblist = array();
	$lbdname = array();
	foreach ($summary as $vipdata)
		foreach (array_keys ($vipdata['lblist']) as $lb_object_id)
			if (!in_array ($lb_object_id, $lblist))
			{
				$oi = getObjectInfo ($lb_object_id);
				$lbdname[$lb_object_id] = $oi['dname'];
				$lblist[] = $lb_object_id;
			}
	if (!count ($summary))
		echo 'none configured';
	else
	{
		$order = 'odd';
		echo "<table class='widetable' border=0 cellpadding=5 cellspacing=0 align='center'>\n";
		echo "<tr><th>VS&nbsp;&darr; LB&nbsp;&rarr;</th>";
		foreach ($lblist as $lb_object_id)
			echo "<th><a href='${root}?page=object&tab=default&object_id=${lb_object_id}'>" . $lbdname[$lb_object_id]  . "</a></th>";
		echo "</tr>\n";
		foreach ($summary as $vsid => $vsdata)
		{
			echo "<tr class=row_${order}><td class=tdleft><a href='$root?page=ipv4vs&tab=default&vs_id=${vsid}'>";
			echo buildVServiceName ($vsdata);
			echo '</a>';
			if (!empty ($vsdata['name']))
				echo "<br>${vsdata['name']}";
			echo "</td>";
			foreach ($lblist as $lb_object_id)
			{
				echo '<td class=tdleft>';
				if (!isset ($vsdata['lblist'][$lb_object_id]))
					echo '&nbsp;';
				else
				{
					echo $vsdata['lblist'][$lb_object_id]['size'];
					echo " (<a href='${root}?page=ipv4rsp&pool_id=";
				       	echo $vsdata['lblist'][$lb_object_id]['id'] . "'>";
					echo $vsdata['lblist'][$lb_object_id]['name'] . '</a>)';
				}
				echo '</td>';
			}
			echo "</tr>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n";
	}
	finishPortlet ();
}

function renderAddNewRange ()
{
	global $root, $pageno, $tabno;
	showMessageOrError();

	startPortlet ("Add new");
	echo "<table class='widetable' border=0 cellpadding=10 align='center'>\n";
	echo "<tr><th>prefix</th><th>name</th><th>connected network</th><th>assign tags</th><th>&nbsp;</th></tr>\n";
	echo "<form name='add_new_range' action='${root}process.php'>\n";
	echo "<input type=hidden name=op value=addIPv4Prefix>\n";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<tr valign=top><td class='tdcenter'><input type=text name='range' size=18 class='live-validate' tabindex=1></td>\n";
	echo "<td class='tdcenter'><input type=text name='name' size='20' tabindex=2></td>\n";
	echo "<td class='tdcenter'><input type=checkbox name='is_bcast' tabindex=3 checked></td>\n";
	echo "<td>\n";
	renderTagSelect();
	echo "</td><td class=tdcenter>";
	printImageHREF ('CREATE', 'Add a new network', TRUE, 4);
	echo "</td></tr>\n";
	echo "</form></table><br><br>\n";
	finishPortlet();

	startPortlet ("Manage existing");
	echo "<table class='widetable' border=0 cellpadding=5 cellspacing=0 align='center'>\n";
	$addrspaceList = getAddressspaceList();
	echo "<tr><th>&nbsp;</th><th>prefix</th><th>name</th><th>utilization</th></tr>";
	foreach ($addrspaceList as $iprange)
	{
		$netdata = getIPv4Network ($iprange['id']);
		$usedips = count ($netdata['addrlist']);
		$totalips = ($netdata['ip_bin'] | $netdata['mask_bin_inv']) - ($netdata['ip_bin'] & $netdata['mask_bin']) + 1;
		echo "<tr valign=top><td>";
		if ($usedips == 0)
		{
			echo "<a href='${root}process.php?op=delIPv4Prefix&page=${pageno}&tab=${tabno}&id=${iprange['id']}'>";
			printImageHREF ('delete', 'Delete this IP range');
			echo "</a>";
		}
		else
			printImageHREF ('nodelete', 'There are IP addresses allocated or reserved');
		echo "</td>\n<td class=tdleft><a href='${root}?page=iprange&id=${iprange['id']}'>";
		echo "${netdata['ip']}/${netdata['mask']}</a></td><td class=tdleft>${netdata['name']}";
		echo "</td><td class=tdcenter>";
		renderProgressBar ($usedips / $totalips);
		echo "<br><small>${usedips}/${totalips}</small></td></tr>\n";
	}
	echo "</table>";
	finishPortlet();
}

function renderIPv4Network ($id)
{
	global $root, $pageno, $tabno, $aac2;
	$netmaskbylen = array
	(
		32 => '255.255.255.255',
		31 => '255.255.255.254',
		30 => '255.255.255.252',
		29 => '255.255.255.248',
		28 => '255.255.255.240',
		27 => '255.255.255.224',
		26 => '255.255.255.192',
		25 => '255.255.255.128',
		24 => '255.255.255.0',
		23 => '255.255.254.0',
		22 => '255.255.252.0',
		21 => '255.255.248.0',
		20 => '255.255.240.0',
		19 => '255.255.224.0',
		18 => '255.255.192.0',
		17 => '255.255.128.0',
		16 => '255.255.0.0',
		15 => '255.254.0.0',
		14 => '255.252.0.0',
		13 => '255.248.0.0',
		12 => '255.240.0.0',
		11 => '255.224.0.0',
		10 => '255.192.0.0',
		9 => '255.128.0.0',
		8 => '255.0.0.0',
		7 => '254.0.0.0',
		6 => '252.0.0.0',
		5 => '248.0.0.0',
		4 => '240.0.0.0',
		3 => '224.0.0.0',
		2 => '192.0.0.0',
		1 => '128.0.0.0'
	);
	$wildcardbylen = array
	(
		32 => '0.0.0.0',
		31 => '0.0.0.1',
		30 => '0.0.0.3',
		29 => '0.0.0.7',
		28 => '0.0.0.15',
		27 => '0.0.0.31',
		26 => '0.0.0.63',
		25 => '0.0.0.127',
		24 => '0.0.0.255',
		23 => '0.0.1.255',
		22 => '0.0.3.255',
		21 => '0.0.7.255',
		20 => '0.0.15.255',
		19 => '0.0.31.255',
		18 => '0.0.63.255',
		17 => '0.0.127.255',
		16 => '0.0.255.25',
		15 => '0.1.255.255',
		14 => '0.3.255.255',
		13 => '0.7.255.255',
		12 => '0.15.255.255',
		11 => '0.31.255.255',
		10 => '0.63.255.255',
		9 => '0.127.255.255',
		8 => '0.255.255.255',
		7 => '1.255.255.255',
		6 => '3.255.255.255',
		5 => '7.255.255.255',
		4 => '15.255.255.255',
		3 => '31.255.255.255',
		2 => '63.255.255.255',
		1 => '127.255.255.255'
	);
	$maxperpage = getConfigVar ('IPV4_ADDRS_PER_PAGE');
	if (isset($_REQUEST['pg']))
		$page = $_REQUEST['pg'];
	else
		$page=0;

	$range = getIPv4Network ($id);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${range['ip']}/${range['mask']}</h1><h2>${range['name']}</h2></td></tr>\n";

	echo "<tr><td class=pcleft width='50%'>";
	startPortlet ('summary');
	$total = ($range['ip_bin'] | $range['mask_bin_inv']) - ($range['ip_bin'] & $range['mask_bin']) + 1;
	$used = count ($range['addrlist']);
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>Utilization:</th><td class=tdleft>";
	renderProgressBar ($used/$total);
	echo "&nbsp;${used}/${total}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Netmask:</th><td class=tdleft>";
	echo $netmaskbylen[$range['mask']];
	echo "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Wildcard bits:</th><td class=tdleft>";
	echo $wildcardbylen[$range['mask']];
	echo "</td></tr>\n";
	printTagTRs ("${root}?page=ipv4space&");
	echo "</table><br>\n";
	finishPortlet();
	echo "</td>\n";

	echo "<td class=pcright>";
	startPortlet ('details');
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
			echo "<a href='${root}?page=${pageno}&tab=${tabno}&id=$id&pg=$i'>$i</a> ";
	}
	echo "</center>";

	echo "<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center' width='100%'>\n";
	echo "<tr><th>Address</th><th>Name</th><th>Allocation</th></tr>\n";


	for ($ip = $startip; $ip <= $endip; $ip++) :
		if (isset ($_REQUEST['hl_ipv4_addr']) and ip2long ($_REQUEST['hl_ipv4_addr']) == $ip)
			$secondstyle = 'tdleft port_highlight';
		else
			$secondstyle = 'tdleft';
		if (!isset ($range['addrlist'][$ip]))
		{
			echo "<tr><td class=tdleft><a href='${root}?page=ipaddress&ip=" . long2ip ($ip) . "'>" . long2ip ($ip);
			echo "</a></td><td class='${secondstyle}'>&nbsp;</td><td class='${secondstyle}'>&nbsp;</td></tr>\n";
			continue;
		}
		$addr = $range['addrlist'][$ip];
		echo "<tr class='${addr['class']}'>";

		echo "<td class=tdleft><a href='${root}?page=ipaddress&ip=${addr['ip']}'>${addr['ip']}</a></td>";
		echo "<td class='${secondstyle}'>${addr['name']}</td><td class='${secondstyle}'>";
		$delim = '';
		$prologue = '';
		if ( $addr['reserved'] == 'yes')
		{
			echo "<strong>RESERVED</strong> ";
			$delim = '; ';
		}
		foreach ($range['addrlist'][$ip]['allocs'] as $ref)
		{
			echo $delim . $aac2[$ref['type']];
			echo "<a href='${root}?page=object&object_id=${ref['object_id']}";
			echo "&hl_ipv4_addr=${addr['ip']}'>";
			echo $ref['name'] . (empty ($ref['name']) ? '' : '@');
			echo "${ref['object_name']}</a>";
			$delim = '; ';
		}
		if ($delim != '')
		{
			$delim = '';
			$prologue = '<br>';
		}
		foreach ($range['addrlist'][$ip]['lblist'] as $ref)
		{
			echo $prologue;
			$prologue = '';
			echo "${delim}<a href='${root}?page=object&object_id=${ref['object_id']}'>";
			echo "${ref['object_name']}</a>:<a href='${root}?page=ipv4vs&vs_id=${ref['vs_id']}'>";
			echo "${ref['vport']}/${ref['proto']}</a>&rarr;";
			$delim = '; ';
		}
		if ($delim != '')
		{
			$delim = '';
			$prologue = '<br>';
		}
		foreach ($range['addrlist'][$ip]['rslist'] as $ref)
		{
			echo $prologue;
			$prologue = '';
			echo "${delim}&rarr;${ref['rsport']}@<a href='${root}?page=ipv4rsp&pool_id=${ref['rspool_id']}'>";
			echo "${ref['rspool_name']}</a>";
			$delim = '; ';
		}
		echo "</td></tr>\n";
	endfor;
	// end of iteration

	echo "</table>";
	finishPortlet();
	echo "</td></tr></table>\n";
}

function renderIPv4NetworkProperties ($id)
{
	global $root, $pageno, $tabno;
	showMessageOrError();
	$netdata = getIPv4NetworkInfo ($id);
	echo "<center><h1>${netdata['ip']}/${netdata['mask']}</h1></center>\n";
	echo "<table border=0 cellpadding=10 cellpadding=1 align='center'>\n";
	echo "<form action='${root}process.php'><input type=hidden name=op value=editRange>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=id value='${id}'>";
	echo "<tr><td class='tdright'>Name:</td><td class='tdleft'><input type=text name=name size=20 value='${netdata['name']}'></tr>";
	echo "<tr><td colspan=2 class=tdcenter>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></form></tr></table>\n";

}

function renderIPv4Address ($dottedquad)
{
	global $root, $aat;
	$address = getIPv4Address ($dottedquad);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${dottedquad}</h1></td></tr>\n";
	if (!empty ($address['name']))
		echo "<tr><td colspan=2 align=center><h2>${address['name']}</h2></td></tr>\n";

	echo "<tr><td class=pcleft>";
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>Allocations:</th><td class=tdleft>" . count ($address['bonds']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Originated NAT connections:</th><td class=tdleft>" . count ($address['outpf']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Arriving NAT connections:</th><td class=tdleft>" . count ($address['inpf']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>SLB virtual services:</th><td class=tdleft>" . count ($address['vslist']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>SLB real servers:</th><td class=tdleft>" . count ($address['rslist']) . "</td></tr>\n";
	printTagTRs();
	echo "</table><br>\n";
	finishPortlet();
	echo "</td>\n";

	echo "<td class=pcright>";

	if (!empty ($address['class']))
	{
		startPortlet ('allocations');
		echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>\n";
		echo "<tr><th>object</th><th>OS interface</th><th>allocation type</th></tr>\n";
		$class = $address['class'];
		// render all allocation records for this address the same way
		if ($address['reserved'] == 'yes')
			echo "<tr class='${class}'><td colspan=2>&nbsp;</td><td class=tdleft><strong>RESERVED</strong></td></tr>";
		foreach ($address['allocs'] as $bond)
		{
			if (isset ($_REQUEST['hl_object_id']) and $_REQUEST['hl_object_id'] == $bond['object_id'])
				$secondclass = 'tdleft port_highlight';
			else
				$secondclass = 'tdleft';
			echo "<tr class='$class'><td class=tdleft><a href='${root}?page=object&object_id=${bond['object_id']}";
			echo "&hl_ipv4_addr=${dottedquad}'>${bond['object_name']}</td><td class='${secondclass}'>${bond['name']}</td><td class='${secondclass}'><strong>";
			echo $aat[$bond['type']];
			echo "</strong></td></tr>\n";
		}
		echo "</table><br><br>";
		finishPortlet();
	}

	if (count ($address['lblist']))
	{
		startPortlet ('Virtual services (' . count ($address['lblist']) . ')');
		echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>\n";
		echo "<tr><th>VS</th><th>name</th></tr>\n";
		foreach ($address['lblist'] as $vsinfo)
		{
			echo "<tr><td class=tdleft><a href='${root}?page=ipv4vs&vs_id=${vsinfo['vs_id']}'>";
			echo buildVServiceName ($vsinfo) . "</a></td><td class=tdleft>";
			echo $vsinfo['name'] . "</td></tr>\n";
		}
		echo "</table><br><br>";
		finishPortlet();
	}

	if (count ($address['rslist']))
	{
		startPortlet ('Real servers (' . count ($address['rslist']) . ')');
		echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>\n";
		echo "<tr><th>&nbsp;</th><th>port</th><th>RS pool</th></tr>\n";
		foreach ($address['rslist'] as $rsinfo)
		{
			echo "<tr><td>";
			if ($rsinfo['inservice'] == 'yes')
				printImageHREF ('inservice', 'in service');
			else
				printImageHREF ('notinservice', 'NOT in service');
			echo "</td><td class=tdleft>${rsinfo['rsport']}</td><td class=tdleft><a href='${root}?page=ipv4rsp&pool_id=${rsinfo['rspool_id']}'>";
			echo $rsinfo['rspool_name'] . "</a></td></tr>\n";
		}
		echo "</table><br><br>";
		finishPortlet();
	}

	if (count ($address['outpf']))
	{
		startPortlet ('departing NAT rules');
		echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>\n";
		echo "<tr><th>proto</th><th>from</th><th>to</th><th>comment</th></tr>\n";
		foreach ($address['outpf'] as $rule)
			echo "<tr><td>${rule['proto']}</td><td>${rule['localip']}:${rule['localport']}</td><td>${rule['remoteip']}:${rule['localport']}</td><td>${rule['description']}</td></tr>";
		echo "</table>";
		finishPortlet();
	}

	if (count ($address['inpf']))
	{
		startPortlet ('arriving NAT rules');
		echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>\n";
		echo "<tr><th>proto</th><th>from</th><th>to</th><th>comment</th></tr>\n";
		foreach ($address['inpf'] as $rule)
			echo "<tr><td>${rule['proto']}</td><td>${rule['localip']}:${rule['localport']}</td><td>${rule['remoteip']}:${rule['localport']}</td><td>${rule['description']}</td></tr>";
		echo "</table>";
		finishPortlet();
	}

	echo "</td></tr>";
	echo "</table>\n";
}

function renderIPv4AddressProperties ($dottedquad)
{
	global $pageno, $tabno, $root;
	showMessageOrError();
	$address = getIPv4Address ($dottedquad);
	echo "<center><h1>$dottedquad</h1></center>\n";
	startPortlet ('update');
	echo "<table border=0 cellpadding=10 cellpadding=1 align='center'>\n";
	echo "<form action='${root}process.php'><input type=hidden name=op value=editAddress>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=ip value='${dottedquad}'>";
	echo "<tr><td class='tdright'>Name:</td><td class='tdleft'><input type=text name=name size=20 value='${address['name']}'></tr>";
	echo "<td class='tdright'>Reserved:</td><td class='tdleft'><input type=checkbox name=reserved size=20 ";
	echo ($address['reserved']=='yes') ? 'checked' : '';
	echo "></tr><tr><td colspan=2 class='tdcenter'>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></form></tr></table>\n";
	finishPortlet();
	if (empty ($address['name']) and $address['reserved'] == 'no')
		return;
	startPortlet ('release');
	echo "<form action='${root}process.php?page=${pageno}&tab=${tabno}&op=editAddress' method=post>";
	echo "<input type=hidden name=ip value='${dottedquad}'>";
	echo "<input type=hidden name=name value=''>";
	echo "<input type=hidden name=reserved value=''>";
	echo "<input type=submit value='release'></form>";
	finishPortlet();
}

function renderIPv4AddressAllocations ($dottedquad)
{
	showMessageOrError();
	global $pageno, $tabno, $root, $aat;

	$address = getIPv4Address ($dottedquad);
	$class = $address['class'];
	echo "<center><h1>${dottedquad}</h1></center>\n";
	echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>\n";
	echo "<tr><th>&nbsp;</th><th>object</th><th>OS interface</th><th>allocation type</th><th>&nbsp;</th></tr>\n";

	if ($address['reserved'] == 'yes')
		echo "<tr class='${class}'><td colspan=3>&nbsp;</td><td class=tdleft><strong>RESERVED</strong></td><td>&nbsp;</td></tr>";
	foreach ($address['allocs'] as $bond)
	{
		echo "<tr class='$class'><form action='${root}process.php'>";
		echo "<input type=hidden name=op value='updIPv4Allocation'>";
		echo "<input type=hidden name=page value='${pageno}'>";
		echo "<input type=hidden name=tab value='${tabno}'>";
		echo "<input type=hidden name=ip value='$dottedquad'>";
		echo "<input type=hidden name=object_id value='${bond['object_id']}'>";
		echo "<td><a href='${root}process.php?op=delIPv4Allocation&page=${pageno}&tab=${tabno}&ip=${dottedquad}&object_id=${bond['object_id']}'>";
		printImageHREF ('delete', 'Unallocate address');
		echo "</a></td>";
		echo "<td><a href='${root}?page=object&object_id=${bond['object_id']}&hl_ipv4_addr=${dottedquad}'>${bond['object_name']}</td>";
		echo "<td><input type='text' name='bond_name' value='${bond['name']}' size=10></td><td>";
		printSelect ($aat, 'bond_type', $bond['type']);
		echo "</td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</td></form></tr>\n";
	}
	echo "<form action='${root}process.php'><input type='hidden' name='op' value='addIPv4Allocation'>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type='hidden' name='ip' value='${dottedquad}'>";
	echo "<td>";
	printImageHREF ('add', 'new allocation', TRUE);
	echo "</td><td><select name='object_id'>";

	foreach (explode (',', getConfigVar ('IPV4_PERFORMERS')) as $type) 
		foreach (getNarrowObjectList ($type) as $object)
			echo "<option value='${object['id']}'>${object['dname']}</option>";

	echo "</select></td><td><input type='text' name='bond_name' value='' size=10></td><td>";
	printSelect ($aat, 'bond_type');
	echo "</td><td>&nbsp;</td></form></tr>";
	echo "</table><br><br>";

}

function renderNATv4ForObject ($object_id = 0)
{
	global $pageno, $tabno, $root;
	
	$info = getObjectInfo ($object_id);
	$forwards = getNATv4ForObject ($object_id);
	$addresses = getObjectAddresses ($object_id);
	showMessageOrError();
	echo "<center><h2>locally performed NAT</h2></center>";

	echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>\n";
	echo "<tr><th></th><th>Match endpoint</th><th>Translate to</th><th>Target object</th><th>Comment</th><th>&nbsp;</th></tr>\n";

	foreach ($forwards['out'] as $pf)
	{
		$class='trerror';
		$name='';
		foreach ($addresses as $addr)
			if ($addr['ip'] == $pf['localip'])
			{
				$class='';
				$name = $addr['name'];
				break;
			}

		echo "<tr class='$class'>";
		echo "<td><a href='${root}process.php?op=delNATv4Rule&localip=${pf['localip']}&localport=${pf['localport']}&remoteip=${pf['remoteip']}&remoteport=${pf['remoteport']}&proto=${pf['proto']}&object_id=$object_id&page=${pageno}&tab=${tabno}'>";
		printImageHREF ('delete', 'Delete NAT rule');
		echo "</a></td>";
		echo "<td>${pf['proto']}/${name}: <a href='${root}?page=ipaddress&tab=default&ip=${pf['localip']}'>${pf['localip']}</a>:${pf['localport']}";
		if (!empty ($pf['local_addr_name']))
			echo ' (' . $pf['local_addr_name'] . ')';
		echo "</td>";
		echo "<td><a href='${root}?page=ipaddress&tab=default&ip=${pf['remoteip']}'>${pf['remoteip']}</a>:${pf['remoteport']}</td>";

		$address = getIPv4Address ($pf['remoteip']);

		echo "<td class='description'>";
		if (count ($address['allocs']))
			foreach ($address['allocs'] as $bond)
				echo "<a href='${root}?page=object&tab=default&object_id=${bond['object_id']}'>${bond['object_name']}(${bond['name']})</a> ";
		elseif (!empty ($pf['remote_addr_name']))
			echo '(' . $pf['remote_addr_name'] . ')';
		echo "</td><form action='${root}process.php'><input type=hidden name=op value=updNATv4Rule><input type=hidden name=page value='${pageno}'>";
		echo "<input type=hidden name=tab value='${tabno}'><input type='hidden' name='object_id' value='$object_id'>";
		echo "<input type='hidden' name='localip' value='${pf['localip']}'><input type='hidden' name='localport' value='${pf['localport']}'>";
		echo "<input type='hidden' name='remoteip' value='${pf['remoteip']}'><input type='hidden' name='remoteport' value='${pf['remoteport']}'>";
		echo "<input type='hidden' name='proto' value='${pf['proto']}'><td class='description'>";
		echo "<input type='text' name='description' value='${pf['description']}'></td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</td></form></tr>";
	}
	echo "<form action='${root}process.php'><input type='hidden' name=op value=addNATv4Rule>";
	echo "<input type='hidden' name='object_id' value='$object_id'>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<tr align='center'><td>";
	printImageHREF ('add', 'Add new NAT rule', TRUE);
	echo '</td><td>';
	printSelect (array ('TCP' => 'TCP', 'UDP' => 'UDP'), 'proto');
	echo "<select name='localip' tabindex=1>";

	foreach ($addresses as $addr)
		echo "<option value='${addr['ip']}'>" . (empty ($addr['name']) ? '' : "${addr['name']}: ") .
			"${addr['ip']}" . (empty ($addr['address_name']) ? '' : " (${addr['address_name']})") . "</option>";

	echo "</select>:<input type='text' name='localport' size='4' tabindex=2></td>";
	echo "<td><input type='text' name='remoteip' id='remoteip' size='10' tabindex=3>";
	echo "<a href='javascript:;' onclick='window.open(\"${root}/find_object_ip_helper.php\", \"findobjectip\", \"height=700, width=400, location=no, menubar=no, resizable=yes, scrollbars=no, status=no, titlebar=no, toolbar=no\");'>";
	printImageHREF ('find', 'Find object');
	echo "</a>";
	echo ":<input type='text' name='remoteport' size='4' tabindex=4></td><td></td>";
	echo "<td colspan=1><input type='text' name='description' size='20' tabindex=5></td><td>&nbsp;</td></tr>";
	echo "</form>";

	echo "</table><br><br>";

	echo "<center><h2>arriving NAT connections</h2></center>";
	echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>\n";
	echo "<tr><th></th><th>Source</th><th>Source objects</th><th>Target</th><th>Description</th></tr>\n";

	foreach ($forwards['in'] as $pf)
	{
		echo "<tr><td><a href='${root}process.php?op=delNATv4Rule&localip=${pf['localip']}&localport=${pf['localport']}&remoteip=${pf['remoteip']}&remoteport=${pf['remoteport']}&proto=${pf['proto']}&object_id=${pf['object_id']}&page=${pageno}&tab=${tabno}'>";
		printImageHREF ('delete', 'Delete NAT rule');
		echo "</a></td>";
		echo "<td>${pf['proto']}/<a href='${root}?page=ipaddress&tab=default&ip=${pf['localip']}'>${pf['localip']}</a>:${pf['localport']}</td>";
		echo "<td class='description'><a href='${root}?page=object&tab=default&object_id=${pf['object_id']}'>${pf['object_name']}</a>";
		echo "</td><td><a href='${root}?page=ipaddress&tab=default&ip=${pf['remoteip']}'>${pf['remoteip']}</a>:${pf['remoteport']}</td>";
		echo "<td class='description'>${pf['description']}</td></tr>";
	}

	echo "</table><br><br>";
}

function renderAddMultipleObjectsForm ()
{
	global $root, $pageno, $tabno, $nextorder;

	$type_id = array();
	$global_type_id = 0;
	$name = array();
	$asset_no = array();
	$keepvalues1 = $keepvalues2 = FALSE;
	$log = array();
	// Look for current submit.
	if (isset ($_REQUEST['got_fast_data']))
	{
		$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
		$keepvalues1 = TRUE;
		$max = getConfigVar ('MASSCOUNT');
		for ($i = 0; $i < $max; $i++)
		{
			if (!isset ($_REQUEST["${i}_object_type_id"]))
			{
				$log[] = array ('code' => 'error', 'message' => "Submitted form is invalid at line " . $i + 1);
				break;
			}
			assertUIntArg ("${i}_object_type_id", __FUNCTION__, TRUE);
			assertStringArg ("${i}_object_name", __FUNCTION__, TRUE);
			assertStringArg ("${i}_object_label", __FUNCTION__, TRUE);
			assertStringArg ("${i}_object_asset_no", __FUNCTION__, TRUE);
			assertStringArg ("${i}_object_barcode", __FUNCTION__, TRUE);
			$type_id[$i] = $_REQUEST["${i}_object_type_id"];
			// Save user input for possible rendering.
			$name[$i] = $_REQUEST["${i}_object_name"];
			$label[$i] = $_REQUEST["${i}_object_label"];
			$asset_no[$i] = $_REQUEST["${i}_object_asset_no"];
			$barcode[$i] = $_REQUEST["${i}_object_barcode"];

			// It's better to skip silently, than to print a notice.
			if ($type_id[$i] == 0)
				continue;
			if (commitAddObject ($name[$i], $label[$i], $barcode[$i], $type_id[$i], $asset_no[$i], $taglist) === TRUE)
				$log[] = array ('code' => 'success', 'message' => "Added new object '${name[$i]}'");
			else
				$log[] = array ('code' => 'error', 'message' => __FUNCTION__ . ': commitAddObject() failed');
		}
	}
	elseif (isset ($_REQUEST['got_very_fast_data']))
	{
		$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
		$keepvalues2 = TRUE;
		assertUIntArg ('global_type_id', __FUNCTION__, TRUE);
		assertStringArg ('namelist', __FUNCTION__, TRUE);
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
				if (commitAddObject ($cname, '', '', $global_type_id, '', $taglist) === TRUE)
					$log[] = array ('code' => 'success', 'message' => "Added new object '${cname}'");
				else
					$log[] = array ('code' => 'error', 'message' => "Could not add '${cname}'");
		}
	}
	printLog ($log);

	// Render a form for the next.
	$typelist = getObjectTypeList();
	$typelist[0] = 'select type...';

	startPortlet ('Distinct types, same tags');
	$max = getConfigVar ('MASSCOUNT');
	echo "<form name=fastform method=post action='${root}?page=${pageno}&tab=${tabno}'>";
	echo '<table border=0 align=center>';
	echo "<tr><th>Object type</th><th>Common name</th><th>Visible label</th>";
	echo "<th>Asset tag</th><th>Barcode</th><th>Tags</th></tr>\n";
	// If a user forgot to select object type on input, we keep his
	// previous input in the form.
	for ($i = 0; $i < $max; $i++)
	{
		echo '<tr><td>';
		// Don't employ DEFAULT_OBJECT_TYPE to avoid creating ghost records for pre-selected empty rows.
		printSelect ($typelist, "${i}_object_type_id", 0);
		echo '</td>';
		echo "<td><input type=text size=30 name=${i}_object_name";
		if ($keepvalues1 and isset ($name[$i]) and (!isset ($type_id[$i]) or $type_id[$i] == 0))
			echo " value='${name[$i]}'";
		echo "></td>";
		echo "<td><input type=text size=30 name=${i}_object_label";
		if ($keepvalues1 and isset ($label[$i]) and (!isset ($type_id[$i]) or $type_id[$i] == 0))
			echo " value='${label[$i]}'";
		echo "></td>";
		echo "<td><input type=text size=20 name=${i}_object_asset_no";
		if ($keepvalues1 and isset ($asset_no[$i]) and (!isset ($type_id[$i]) or $type_id[$i] == 0))
			echo " value='${asset_no[$i]}'";
		echo "></td>";
		echo "<td><input type=text size=10 name=${i}_object_barcode";
		if ($keepvalues1 and isset ($barcode[$i]) and (!isset ($type_id[$i]) or $type_id[$i] == 0))
			echo " value='${barcode[$i]}'";
		echo "></td>";
		if ($i == 0)
		{
			echo "<td valign=top rowspan=${max}>";
			renderTagSelect();
			echo "</td>\n";
		}
		echo "</tr>\n";
	}
	echo "<tr><td class=submit colspan=5><input type=submit name=got_fast_data value='Go!'></td></tr>\n";
	echo "</form></table>\n";
	finishPortlet();

	startPortlet ('Same type, same tags');
	echo "<form name=veryfastform method=post action='${root}?page=${pageno}&tab=${tabno}'>";
	echo "<table border=0 align=center><tr><th>names</th><th>type</th></tr>";
	echo "<tr><td rowspan=3><textarea name=namelist cols=40 rows=25>\n";
	if ($keepvalues2 and $global_type_id == 0)
		echo $_REQUEST['namelist'];
	echo "</textarea></td><td valign=top>";
	printSelect ($typelist, "global_type_id", getConfigVar ('DEFAULT_OBJECT_TYPE'));
	echo "</td></tr>";
	echo "<tr><th>Tags</th></tr>";
	echo "<tr><td valign=top>";
	renderTagSelect();
	echo "</td></tr>";
	echo "<tr><td colspan=2><input type=submit name=got_very_fast_data value='Go!'></td></tr></table>\n";
	echo "</form>\n";
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
		showError ('Search string cannot be empty.', __FUNCTION__);
		return;
	}
	if (!permitted ('objects', 'default'))
	{
		showError ('You are not authorized for viewing information about objects.', __FUNCTION__);
		return;
	}
	$nhits = 0;
	// If we search for L2 address, we can either find one or find none.
	if
	(
		preg_match ('/^[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?$/i', $terms) or
		preg_match ('/^[0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f]$/i', $terms) or
		preg_match ('/^[0-9a-f][0-9a-f][0-9a-f][0-9a-f].[0-9a-f][0-9a-f][0-9a-f][0-9a-f].[0-9a-f][0-9a-f][0-9a-f][0-9a-f]$/i', $terms) or
		// STP bridge ID: bridge priotity + port MAC address. Cut off first 4 chars and look for MAC address.
		preg_match ('/^[0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f]$/i', $terms)
	)
	// Search for L2 address.
	{
		$terms = str_replace ('.', '', $terms);
		$terms = str_replace (':', '', $terms);
		$terms = substr ($terms, -12);
		$result = searchByl2address ($terms);
		if ($result !== NULL)
		{
			$nhits++;
			$lasthit = 'port';
			$summary['port'][] = $result;
		}
	}
	elseif (preg_match ('/^[0-9][0-9]?[0-9]?\.[0-9]?[0-9]?[0-9]?\.[0-9][0-9]?[0-9]?\.[0-9]?[0-9]?[0-9]?$/i', $terms))
	// Search for IP address.
	{
		$result = getRangeByIp ($terms);
		if ($result !== NULL)
		{
			$nhits++;
			$lasthit = 'ipv4address1';
			$summary['ipv4address1'][] = $terms;
		}
	}
	else
	// Search for objects, addresses, networks, virtual services and RS pools by their description.
	{
		$tmp = getObjectSearchResults ($terms);
		if (count ($tmp))
		{
			$nhits += count ($tmp);
			$lasthit = 'object';
			$summary['object'] = $tmp;
		}
		$tmp = getIPv4AddressSearchResult ($terms);
		if (count ($tmp))
		{
			$nhits += count ($tmp);
			$lasthit = 'ipv4address2';
			$summary['ipv4address2'] = $tmp;
		}
		$tmp = getIPv4PrefixSearchResult ($terms);
		if (count ($tmp))
		{
			$nhits += count ($tmp);
			$lasthit = 'ipv4network';
			$summary['ipv4network'] = $tmp;
		}
		$tmp = getIPv4RSPoolSearchResult ($terms);
		if (count ($tmp))
		{
			$nhits += count ($tmp);
			$lasthit = 'ipv4rspool';
			$summary['ipv4rspool'] = $tmp;
		}
		$tmp = getIPv4VServiceSearchResult ($terms);
		if (count ($tmp))
		{
			$nhits += count ($tmp);
			$lasthit = 'ipv4vs';
			$summary['ipv4vs'] = $tmp;
		}
		$tmp = getAccountSearchResult ($terms);
		if (count ($tmp))
		{
			$nhits += count ($tmp);
			$lasthit = 'user';
			$summary['user'] = $tmp;
		}
	}
	if ($nhits == 0)
		echo "<center><h2>Nothing found for '${terms}'</h2></center>";
	elseif ($nhits == 1)
	{
		$record = current ($summary[$lasthit]);
		switch ($lasthit)
		{
			case 'port':
				echo "<script language='Javascript'>document.location='${root}?page=object";
				echo "&hl_port_id=" . $record['port_id'];
				echo "&object_id=" . $record['object_id'] . "';//</script>";
				break;
			case 'ipv4address1':
				echo "<script language='Javascript'>document.location='${root}?page=ipaddress";
				echo "&ip=${record}";
				echo "';//</script>";
				break;
			case 'ipv4address2':
				echo "<script language='Javascript'>document.location='${root}?page=ipaddress";
				echo "&ip=${record['ip']}";
				echo "';//</script>";
				break;
			case 'ipv4network':
				echo "<script language='Javascript'>document.location='${root}?page=iprange";
				echo "&id=${record['id']}";
				echo "';//</script>";
				break;
			case 'object':
				echo "<script language='Javascript'>document.location='${root}?page=object&object_id=${record['id']}';//</script>";
				break;
			case 'ipv4rspool':
				echo "<script language='Javascript'>document.location='${root}?page=ipv4rsp&pool_id=${record['pool_id']}';//</script>";
				break;
			case 'ipv4vs':
				echo "<script language='Javascript'>document.location='${root}?page=ipv4vs&vs_id=${record['id']}';//</script>";
				break;
			case 'user':
				echo "<script language='Javascript'>document.location='${root}?page=user&user_id=${record['user_id']}';//</script>";
				break;
		}
		return;
	}
	else
	{
		global $nextorder;
		$order = 'odd';
		echo "<center><h2>${nhits} result(s) found for '${terms}'</h2></center>";
		foreach ($summary as $where => $what)
			switch ($where)
			{
				case 'object':
					startPortlet ("<a href='${root}?page=objects'>Objects</a>");
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					echo '<tr><th>Common name</th><th>Visible label</th><th>Asset tag</th><th>barcode</th></tr>';
					foreach ($what as $obj)
					{
						echo "<tr class=row_${order}><td><a href=\"${root}?page=object&object_id=${obj['id']}\">${obj['dname']}</a></td>";
						echo "<td>${obj['label']}</td>";
						echo "<td>${obj['asset_no']}</td>";
						echo "<td>${obj['barcode']}</td></tr>";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
				case 'ipv4network':
					startPortlet ("<a href='${root}?page=ipv4space'>IPv4 networks</a>");
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					echo '<tr><th>network</th><th>name/tags</th></tr>';
					foreach ($what as $net)
					{
						$prefixtags = loadIPv4PrefixTags ($net['id']);
						echo "<tr class=row_${order} valign=top><td class=tdleft><a href='${root}?page=iprange&id=${net['id']}'>${net['ip']}";
						echo '/' . $net['mask'] . '</a></td>';
						echo "<td class=tdleft>${net['name']}";
						if (count ($prefixtags))
						{
							echo "<br>";
							echo serializeTags ($prefixtags, "${root}?page=ipv4space&");
						}
						echo "</td></tr>";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
				case 'ipv4address2':
					startPortlet ('IPv4 addresses');
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					echo '<tr><th>Address</th><th>Descritpion</th></tr>';
					foreach ($what as $addr)
					{
						echo "<tr class=row_${order}><td class=tdleft><a href='${root}?page=ipaddress&ip=${addr['ip']}'>";
						echo "${addr['ip']}</a></td>";
						echo "<td class=tdleft>${addr['name']}</td></tr>";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
				case 'ipv4rspool':
					startPortlet ("<a href='${root}?page=ipv4rsplist'>RS pools</a>");
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					foreach ($what as $rspool)
					{
						echo "<tr class=row_${order}><td class=tdleft><a href='${root}?page=ipv4rsp&pool_id=${rspool['pool_id']}'>";
						echo buildRSPoolName ($rspool);
						echo "</a></td></tr>";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
				case 'ipv4vs':
					startPortlet ("<a href='${root}?page=ipv4vslist'>Virtual services</a>");
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					echo '<tr><th>VS</th><th>Descritpion</th></tr>';
					foreach ($what as $vs)
					{
						echo "<tr class=row_${order}><td class=tdleft><a href='${root}?page=ipv4vs&vs_id=${vs['id']}'>";
						echo buildVServiceName ($vs);
						echo "</a></td><td class=tdleft>${vs['name']}</td></tr>";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
				case 'user':
					startPortlet ("<a href='${root}?page=userlist'>Users</a>");
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					echo '<tr><th>username</th><th>realname</th></tr>';
					foreach ($what as $item)
					{
						echo "<tr class=row_${order}><td class=tdleft><a href='${root}?page=user&user_id=${item['user_id']}'>";
						echo $item['user_name'];
						echo "</a></td><td class=tdleft>${item['user_realname']}</td></tr>";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
			}
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

function renderUserList ()
{
	global $nextorder, $accounts, $root;
	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";
	startPortlet ('User accounts');
	echo "<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	echo "<tr><th class=tdleft>Username</th><th class=tdleft>Real name</th></tr>";
	$order = 'odd';
	$tagfilter = getTagFilter();
	foreach (getUserAccounts ($tagfilter, getTFMode()) as $user)
	{
		echo "<tr class=row_${order}><td class=tdleft><a href='${root}?page=user&user_id=${user['user_id']}'>";
		echo "${user['user_name']}</a></td>";
		echo "<td class=tdleft>${user['user_realname']}</td></li>";
		$order = $nextorder[$order];
	}
	echo '</table>';
	finishPortlet();
	echo '</td><td class=pcright>';
	renderTagFilterPortlet ($tagfilter, 'user');
	echo "</td></tr></table>\n";
}

function renderUserListEditor ()
{
	global $root, $pageno, $tabno, $accounts;
	startPortlet ('User accounts');
	showMessageOrError();
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>status (click to change)</th><th>Username</th><th>Real name</th><th>Password</th><th>&nbsp;</th></tr>\n";
	foreach ($accounts as $account)
	{
		echo "<form action='${root}process.php'>";
		echo "<input type=hidden name=op value=updateUser>";
		echo "<input type=hidden name=page value='${pageno}'>\n";
		echo "<input type=hidden name=tab value='${tabno}'>\n";
		echo "<input type=hidden name=user_id value='${account['user_id']}'><tr>";
		echo "<td>";
		if ($account['user_enabled'] == 'yes' && $account['user_id'] != 1)
		{
			echo "<a href='${root}process.php?op=disableUser&page=${pageno}&tab=${tabno}&user_id=${account['user_id']}'>";
			printImageHREF ('blockuser', 'disable account');
			echo "</a>\n";
		}
		if ($account['user_enabled'] == 'no' && $account['user_id'] != 1)
		{
			echo "<a href='${root}process.php?op=enableUser&page=${pageno}&tab=${tabno}&user_id=${account['user_id']}'>";
			printImageHREF ('unblockuser', 'enable account');
			echo "</a>\n";
		}
		// Otherwise skip icon.
		echo "</td>";
		echo "<td><input type=text name=username value='${account['user_name']}' size=16></td>";
		echo "<td><input type=text name=realname value='${account['user_realname']}' size=24></td>";
		echo "<td><input type=password name=password value='${account['user_password_hash']}' size=64></td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</td></form></tr>\n";
	}
	echo "<form action='${root}process.php' method=post><tr>";
	echo "<input type=hidden name=op value=createUser>\n";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<td>&nbsp;</td><td><input type=text size=16 name=username tabindex=100></td>\n";
	echo "<td><input type=text size=24 name=realname tabindex=101></td>";
	echo "<td><input type=password size=64 name=password tabindex=102></td><td>";
	printImageHREF ('create', 'Add new account', TRUE, 103);
	echo "</td></tr></form>";
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

function renderPortMapViewer ()
{
	renderPortMap (FALSE);
}

function renderPortMapEditor ()
{
	renderPortMap (TRUE);
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
			echo '<td' . ($pctable[$type1][$type2] ? " class=portmap_highlight_$order" : '') . '>';
			echo '<input type=checkbox' . ($editable ? " name=atom_${type1}_${type2}" : ' disabled');
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
	// FIXME: assume all config kids to have static titles at the moment,
	// but use some proper abstract function later.
	foreach ($children as $cpageno => $child)
		echo "<li><a href='${root}?page=${cpageno}'>" . $child['title'] . "</li>\n";
	echo '';
	echo '</ul>';
}

function renderRackPage ($rack_id)
{
	if ($rack_id == 0)
	{
		showError ('Invalid rack_id', __FUNCTION__);
		return;
	}
	if (($rackData = getRackData ($rack_id)) == NULL)
	{
		showError ('getRackData() failed', __FUNCTION__);
		return;
	}
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0><tr>";

	// Left column with information.
	echo "<td class=pcleft>";
	renderRackInfoPortlet ($rackData);
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
	$dict = getDict (TRUE);
	echo "<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	foreach ($dict as $chapter_no => $chapter)
	{
		$order = 'odd';
		echo "<tr><th>Chapter</th><th>refs</th><th>Word</th></tr>\n";
		$wc = count ($chapter['word']);
		echo "<tr class=row_${order}><td class=tdleft" . ($wc ? " rowspan = ${wc}" : '');
		echo "><div title='number=${chapter_no}'>${chapter['name']} (${wc} records)</div></td>";
		if (!$wc)
			echo "<td colspan=2>none</td>";
		else
		{
			$chap_start = TRUE;
			foreach ($chapter['word'] as $key => $value)
			{
				if (!$chap_start)
					echo "<tr class=row_${order}>";
				else
					$chap_start = FALSE;
				echo '<td>' . ($chapter['refcnt'][$key] ? $chapter['refcnt'][$key] : '&nbsp;') . '</td>';
				echo "<td><div title='key=${key}'>${value}</div></td></tr>\n";
				$order = $nextorder[$order];
			}
		}
	}
	echo "</table>\n<br>";
}

function renderDictionaryEditor ()
{
	global $root, $pageno, $tabno, $nextorder;
	$dict = getDict();
	showMessageOrError();
	echo "<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	foreach ($dict as $chapter_no => $chapter)
	{
		$order = 'odd';
		echo "<tr><th>Chapter</th><th>&nbsp;</th><th>Word</th><th>&nbsp;</th></tr>\n";
		$wc = count ($chapter['word']);
		// One extra span for the new record per each chapter block.
		echo "<tr class=row_${order}><td class=tdleft" . ($wc ? ' rowspan = ' . ($wc + 1) : '');
		echo "><div title='number=${chapter_no}'>${chapter['name']} (${wc} records)</div></td>";
		echo "<form action='${root}process.php' method=post>";
		echo "<input type=hidden name=page value='${pageno}'>";
		echo "<input type=hidden name=tab value='${tabno}'>";
		echo "<input type=hidden name=op value=add>";
		echo "<input type=hidden name=chapter_no value='${chapter['no']}'>";
		echo "<td>&nbsp;</td>";
		echo "<td class=tdright><input type=text name=dict_value size=32></td>";
		echo "<td><input type=submit value='Add new'></td>";
		echo '</tr></form>';
		$order = $nextorder[$order];
		foreach ($chapter['word'] as $key => $value)
		{
			echo "<form action='${root}process.php' method=post>";
			echo "<input type=hidden name=page value='${pageno}'>";
			echo "<input type=hidden name=tab value='${tabno}'>";
			echo "<input type=hidden name=op value='upd'>";
			echo "<input type=hidden name=chapter_no value='${chapter['no']}'>";
			echo "<input type=hidden name=dict_key value='${key}'>";
			echo "<tr class=row_${order}><td>";
			// Prevent deleting words currently used somewhere.
			if ($chapter['refcnt'][$key])
				printImageHREF ('nodelete', 'referenced ' . $chapter['refcnt'][$key] . ' time(s)');
			else
			{
				echo "<a href='${root}process.php?page=${pageno}&tab=${tabno}&op=del&chapter_no=${chapter['no']}&dict_key=${key}'>";
				printImageHREF ('delete', 'Delete word');
				echo "</a>";
			}
			echo '</td>';
			echo "<td class=tdright><input type=text name=dict_value size=32 value='${value}'></td>";
			echo "<td><input type=submit value=OK></td>";
			echo "</tr></form>\n";
			$order = $nextorder[$order];
		} // foreach ($chapter['word']
	} // foreach ($dict
	echo "</table>\n";
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
		if ($sticky)
			printImageHREF ('nodelete', 'system chapter');
		elseif ($wordcount > 0)
			printImageHREF ('nodelete', 'contains ' . $wordcount . ' word(s)');
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
	$image['ipv4slb']['path'] = 'pix/slb.png';
	$image['ipv4slb']['width'] = 218;
	$image['ipv4slb']['height'] = 200;
	$image['config']['path'] = 'pix/configuration.png';
	$image['config']['width'] = 218;
	$image['config']['height'] = 200;
	$image['reports']['path'] = 'pix/report.png';
	$image['reports']['width'] = 218;
	$image['reports']['height'] = 200;
	$image['useup']['path'] = 'pix/tango-edit-clear.png';
	$image['useup']['width'] = 16;
	$image['useup']['height'] = 16;
	$image['link']['path'] = 'pix/tango-network-wired.png';
	$image['link']['width'] = 16;
	$image['link']['height'] = 16;
	$image['unlink']['path'] = 'pix/tango-edit-clear.png';
	$image['unlink']['width'] = 16;
	$image['unlink']['height'] = 16;
	$image['add']['path'] = 'pix/tango-list-add.png';
	$image['add']['width'] = 16;
	$image['add']['height'] = 16;
	$image['delete']['path'] = 'pix/tango-list-remove.png';
	$image['delete']['width'] = 16;
	$image['delete']['height'] = 16;
	$image['nodelete']['path'] = 'pix/tango-list-remove-shadow.png';
	$image['nodelete']['width'] = 16;
	$image['nodelete']['height'] = 16;
	$image['grant'] = $image['add'];
	$image['revoke'] = $image['delete'];
	$image['inservice']['path'] = 'pix/tango-emblem-system.png';
	$image['inservice']['width'] = 16;
	$image['inservice']['height'] = 16;
	$image['notinservice']['path'] = 'pix/tango-dialog-error.png';
	$image['notinservice']['width'] = 16;
	$image['notinservice']['height'] = 16;
	$image['blockuser'] = $image['inservice'];
	$image['unblockuser'] = $image['notinservice'];
	$image['find']['path'] = 'pix/tango-system-search.png';
	$image['find']['width'] = 16;
	$image['find']['height'] = 16;
	$image['spacer']['path'] = 'pix/pixel.png';
	$image['spacer']['width'] = 16;
	$image['spacer']['height'] = 16;
	$image['next']['path'] = 'pix/tango-go-next.png';
	$image['next']['width'] = 32;
	$image['next']['height'] = 32;
	$image['prev']['path'] = 'pix/tango-go-previous.png';
	$image['prev']['width'] = 32;
	$image['prev']['height'] = 32;
	$image['clear']['path'] = 'pix/tango-edit-clear.png';
	$image['clear']['width'] = 16;
	$image['clear']['height'] = 16;
	$image['save']['path'] = 'pix/tango-document-save.png';
	$image['save']['width'] = 16;
	$image['save']['height'] = 16;
	$image['SAVE']['path'] = 'pix/tango-document-save-big.png';
	$image['SAVE']['width'] = 32;
	$image['SAVE']['height'] = 32;
	$image['create']['path'] = 'pix/tango-document-new.png';
	$image['create']['width'] = 16;
	$image['create']['height'] = 16;
	$image['CREATE']['path'] = 'pix/tango-document-new-big.png';
	$image['CREATE']['width'] = 32;
	$image['CREATE']['height'] = 32;
	$image['DENIED']['path'] = 'pix/tango-dialog-error-big.png';
	$image['DENIED']['width'] = 32;
	$image['DENIED']['height'] = 32;
	if (!isset ($image[$tag]))
		$tag = 'error';
	$img = $image[$tag];
	if ($do_input == TRUE)
		echo
			"<input type=image name=submit class=icon " .
			"src='${root}${img['path']}' " .
			"border=0 " .
			($tabindex ? '' : "tabindex=${tabindex}") .
			(empty ($title) ? '' : " title='${title}'") . // JT: Add title to input hrefs too
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
	startPortlet ("Dictionary/objects");
	echo "<table>\n";
	foreach (getDictStats() as $header => $data)
		echo "<tr><th class=tdright>${header}:</th><td class=tdleft>${data}</td></tr>\n";
	echo "</table>\n";
	finishPortlet();
	startPortlet ('IPv4');
	echo "<table>\n";
	foreach (getIPv4Stats() as $header => $data)
		echo "<tr><th class=tdright>${header}:</th><td class=tdleft>${data}</td></tr>\n";
	echo "</table>\n";
	finishPortlet();
	startPortlet ('Rackspace');
	echo "<table>\n";
	foreach (getRackspaceStats() as $header => $data)
		echo "<tr><th class=tdright>${header}:</th><td class=tdleft>${data}</td></tr>\n";
	echo "</table>\n";
	finishPortlet();
	startPortlet ('RackCode');
	echo "<table>\n";
	foreach (getRackCodeStats() as $header => $data)
		echo "<tr><th class=tdright>${header}:</th><td class=tdleft>${data}</td></tr>\n";
	echo "</table>\n";
	finishPortlet();

	echo "</td><td class=pcright>\n";

	startPortlet ("Tag popularity");
	echo "<table>\n";
	foreach (getTagStats() as $header => $data)
		echo "<tr><th class=tdright>${header}:</th><td class=tdleft>${data}</td></tr>\n";
	echo "</table>\n";
	finishPortlet();
	echo "</td></tr>\n";
	echo "</table>\n";
}

function dragon ()
{
	startPortlet ('Here be dragons');
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
	finishPortlet();
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
	showMessageOrError();
	$data = getSwitchVLANs ($object_id);
	if ($data === NULL)
	{
		showError ('getSwitchVLANs() returned NULL', __FUNCTION__);
		return;
	}
	list ($vlanlist, $portlist, $maclist) = $data;

	echo '<table border=0 width="100%"><tr><td colspan=3>';

	startPortlet ('Current status');
	echo "<table class=widetable cellspacing=3 cellpadding=5 align=center width='100%'><tr>";
	echo "<form method=post action='${root}process.php?page=${pageno}&tab=${tabno}&op=setPortVLAN'>";
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
		elseif ($port['vlanid'] == 'routed')
		{
			echo "<input type=hidden name=vlanid_${portno} value='routed'>";
			echo "<select disabled multiple='multiple' size=1><option>ROUTED</option></select>";
		}
		else
		{
			echo "<select name=vlanid_${portno}>";
			// A port may belong to a VLAN, which is absent from the VLAN table, this is normal.
			// We must be able to render its SELECT properly at least.
			$in_table = FALSE;
			foreach ($vlanlist as $v => $d)
			{
				echo "<option value=${v}";
				if ($v == $port['vlanid'])
				{
					echo ' selected';
					$in_table = TRUE;
				}
				echo ">${v}</option>\n";
			}
			if (!$in_table)
				echo "<option value=${port['vlanid']} selected>${port['vlanid']}</option>\n";
			echo "</select>";
		}
		$portno++;
		echo "</td>";
	}
	echo "</tr><tr><td colspan=" . ($ports_per_row + 1) . "><input type=submit value='Save changes'></form></td></tr></table>";
	finishPortlet();

	echo '</td></tr><tr><td class=pcleft>';
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

	startPortlet ('Color legend');
	echo '<table>';
	echo "<tr><th>port state</th><th>color code</th></tr>";
	echo "<tr><td>not connected</td><td class=port_notconnect>SAMPLE</td></tr>";
	echo "<tr><td>disabled</td><td class=port_disabled>SAMPLE</td></tr>";
	echo "<tr><td>unknown</td><td class=port_unknown>SAMPLE</td></tr>";
	echo "<tr><td>connected with none MAC addresses active</td><td class=port_connected_none>SAMPLE</td></tr>";
	echo "<tr><td>connected with 1 MAC addresses active</td><td class=port_connected_single>SAMPLE</td></tr>";
	echo "<tr><td>connected with 1+ MAC addresses active</td><td class=port_connected_multi>SAMPLE</td></tr>";
	echo '</table>';
	finishPortlet();

	echo '</td><td class=pcright>';

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
					echo "<tr class=row_${order}><td class=tdleft>$portname</td><td class=tdleft>$vlanid</td>";
					echo "<td class=tdleft>$addr</td></tr>\n";
					$order = $nextorder[$order];
				}
		echo '</table>';
		finishPortlet();
	}

	// End of main table.
	echo '</td></tr></table>';
}

// This snippet either renders a form inviting the user to start SNMP query
// on the current device or displays the result of the scan.
function renderSNMPPortFinder ($object_id = 0)
{
	global $pageno, $tabno;
	if ($object_id <= 0)
	{
		showError ('Invalid object_id', __FUNCTION__);
		return;
	}
// FIXME: check if SNMP PHP extension is available!
	if (isset ($_REQUEST['do_scan']))
	{
		assertStringArg ('community', __FUNCTION__);
		printLog (doSNMPmining ($object_id, $_REQUEST['community']));
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
	echo "This button will reset user interface configuration to its defaults (except organization name and auth source): ";
	echo "<input type=submit value='proceed'>";
	echo "</form>";
}

function renderFirstRowForm ()
{
	global $root, $pageno, $tabno;
	echo "<form action='${root}process.php'>\n";
	echo "<input type=hidden name=page value=dict>\n";
	echo "<input type=hidden name=tab value=edit>\n";
	echo "<input type=hidden name=op value=add>\n";
	echo "<input type=hidden name=chapter_no value=3>\n";
?>
<p align=center>
Your rackspace seems to be empty, and this form will create your first rack row,
just fill in the name. All the subsequent rack rows will have to be added from the
Dictionary edit page in Configuration section.
<br>
<input type=text name=dict_value value='my server room'>
<input type=submit value='OK'> 
</p>
<?php
}

function renderLVSConfig ($object_id = 0)
{
	showMessageOrError();
	global $root, $pageno, $tabno;
	if ($object_id <= 0)
	{
		showError ('Invalid argument', __FUNCTION__);
		return;
	}
	echo '<br>';
	echo "<form method=post action='${root}process.php?page=${pageno}&tab=${tabno}&op=submitSLBConfig'>";
	echo "<input type=hidden name=object_id value=${object_id}>";
	echo "<center><input type=submit value='Submit for activation'></center>";
	echo "</form>";
	echo '<pre>';
	echo buildLVSConfig ($object_id);
	echo '</pre>';
}

function renderRouterConfig ($object_id = 0)
{
	showMessageOrError();
	global $root, $pageno, $tabno;
	if ($object_id <= 0)
	{
		showError ('Invalid argument', __FUNCTION__);
		return;
	}
	echo '<br>';
	echo "<form method=post action='${root}process.php?page=${pageno}&tab=${tabno}&op=submitRouterConfig'>";
	echo "<input type=hidden name=object_id value=${object_id}>";
	echo "<center><input type=submit value='Submit for activation'></center>";
	echo "</form>";
	echo '<pre>';
	echo buildRouterConfig ($object_id);
	echo '</pre>';
}

function renderVirtualService ($vsid)
{
	global $root, $nextorder;
	if ($vsid <= 0)
	{
		showError ('Invalid vsid', __FUNCTION__);
		return;
	}
	$vsinfo = getVServiceInfo ($vsid);
	echo '<table border=0 class=objectview cellspacing=0 cellpadding=0>';
	if (!empty ($vsinfo['name']))
		echo "<tr><td colspan=2 align=center><h1>${vsinfo['name']}</h1></td></tr>\n";
	echo '<tr>';

	echo '<td class=pcleft>';
	startPortlet ('Frontend');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	if (!empty ($vsinfo['name']))
		echo "<tr><th width='50%' class=tdright>Name:</th><td class=tdleft>${vsinfo['name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Protocol:</th><td class=tdleft>${vsinfo['proto']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Virtual IP address:</th><td class=tdleft><a href='${root}?page=ipaddress&tab=default&ip=${vsinfo['vip']}'>${vsinfo['vip']}</a></td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Virtual port:</th><td class=tdleft>${vsinfo['vport']}</td></tr>\n";
	printTagTRs ("${root}?page=ipv4vslist&");
	if (!empty ($vsinfo['vsconfig']))
	{
		echo "<tr><th width='50%' class=tdright>VS configuration:</th><td class=tdleft>&nbsp;</td></tr>\n";
		echo "<tr><td class=tdleft colspan=2><pre>${vsinfo['vsconfig']}</pre></td></tr>\n";
	}
	if (!empty ($vsinfo['rsconfig']))
	{
		echo "<tr><th width='50%' class=tdright>RS configuration:</th><td class=tdleft>&nbsp;</td></tr>\n";
		echo "<tr><td class=tdleft colspan=2><pre>${vsinfo['rsconfig']}</pre></td></tr>\n";
	}
	echo "</table>\n";
	finishPortlet ();
	echo '</td>';

	echo '<td class=pcright>';
	startPortlet ('Backend');
	echo "<table cellspacing=0 cellpadding=5 align=center border=0>\n";
	echo "<tr><th>real server pool</th><th>load balancers</th></tr>\n";
	$order = 'odd';
	foreach ($vsinfo['rspool'] as $pool_id => $poolInfo)
	{
		echo "<tr class=row_${order}><td class=tdleft>";
		// Pool info
		echo '<table width=100%>';
		echo "<tr><td colspan=2><a href='${root}?page=ipv4rsp&pool_id=${pool_id}'>";
		if (!empty ($poolInfo['name']))
			echo $poolInfo['name'];
		else
			echo 'ANONYMOUS';
		echo "</a></td></tr>";
		if (!empty ($poolInfo['vsconfig']))
			echo "<tr><th>VS config</th><td class=tdleft><pre>${poolInfo['vsconfig']}</pre></td></tr>";
		if (!empty ($poolInfo['rsconfig']))
			echo "<tr><th>RS config</th><td class=tdleft><pre>${poolInfo['rsconfig']}</pre></td></tr>";
		echo '</table>';
		echo '</td><td>';
		// LB list
		if (!count ($poolInfo['lblist']))
			echo 'none';
		else
		{
			echo '<table width=100%>';
			foreach ($poolInfo['lblist'] as $object_id => $lbInfo)
			{
				// FIXME: dname should be cached
				$oi = getObjectInfo ($object_id);
				echo "<tr><td colspan=2><a href='${root}?page=object&object_id=${object_id}'>";
				echo $oi['dname'] . '</a></td></tr>';
				if (!empty ($lbInfo['vsconfig']))
					echo "<tr><th>VS config</th><td class=tdleft><pre>${lbInfo['vsconfig']}</pre></td></tr>";
				if (!empty ($lbInfo['rsconfig']))
					echo "<tr><th>RS config</th><td class=tdleft><pre>${lbInfo['rsconfig']}</pre></td></tr>";
			}
			echo '</table>';
		}
		echo "</td></tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table>\n";
	finishPortlet ();
	echo '</td>';

	echo '</tr><table>';
}

function renderProgressBar ($percentage = 0)
{
	global $root;
	$done = ((int) ($percentage * 100));
	echo "<img width=100 height=10 border=0 title='${done}%' src='${root}render_image.php?img=progressbar&done=${done}'>";
}

function renderRSPoolServerForm ($pool_id = 0)
{
	global $root, $pageno, $tabno, $nextorder;
	if ($pool_id <= 0)
	{
		showError ('Invalid pool_id', __FUNCTION__);
		return;
	}
	showMessageOrError();
	$poolInfo = getRSPoolInfo ($pool_id);

	if (($rsc = count ($poolInfo['rslist'])))
	{
		startPortlet ("Manage existing (${rsc})");
		echo "<table cellspacing=0 cellpadding=5 align=center class=cooltable>\n";
		echo "<tr><th>&nbsp;</th><th>Address</th><th>Port</th><th>configuration</th><th>&nbsp;</th></tr>\n";
		$order = 'odd';
		foreach ($poolInfo['rslist'] as $rsid => $rs)
		{
			echo "<form action='${root}process.php'>";
			echo "<input type=hidden name=page value='${pageno}'>\n";
			echo "<input type=hidden name=tab value='${tabno}'>\n";
			echo "<input type=hidden name=op value=updRS>";
			echo "<input type=hidden name=rs_id value='${rsid}'>";
			echo "<input type=hidden name=pool_id value='${pool_id}'>";
			echo "<tr valign=top class=row_${order}><td><a href='${root}process.php?page=${pageno}&tab=${tabno}";
			echo "&op=delRS&pool_id=${pool_id}&id=${rsid}'>";
			printImageHREF ('delete', 'Delete this real server');
			echo "</td><td><input type=text name=rsip value='${rs['rsip']}'></td>";
			echo "<td><input type=text name=rsport size=5 value='${rs['rsport']}'></td>";
			echo "<td><textarea name=rsconfig>${rs['rsconfig']}</textarea></td><td>";
			printImageHREF ('save', 'Save changes', TRUE);
			echo "</td></tr></form>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n";
		finishPortlet();
	}

	startPortlet ('Add one');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>in service</th><th>Address</th><th>Port</th><th>&nbsp;</th></tr>\n";
	echo "<form name=addone action='${root}process.php'>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=op value=addRS>";
	echo "<input type=hidden name=pool_id value='${pool_id}'>";
	echo "<tr><td>";
	if (getConfigVar ('DEFAULT_IPV4_RS_INSERVICE') == 'yes')
		printImageHREF ('inservice', 'in service');
	else
		printImageHREF ('notinservice', 'NOT in service');
	echo "</td><td><input type=text name=remoteip id=remoteip tabindex=1> ";
	echo "<a href='javascript:;' onclick='window.open(\"${root}find_object_ip_helper.php\", \"findobjectip\", \"height=700, width=400, location=no, menubar=no, resizable=yes, scrollbars=no, status=no, titlebar=no, toolbar=no\");'>";
	printImageHREF ('find', 'pick address');
	echo "</a></td>";
	$default_port = getConfigVar ('DEFAULT_SLB_RS_PORT');
	if ($default_port == 0)
		$default_port = '';
	echo "<td><input type=text name=rsport size=5 value='${default_port}'  tabindex=2></td><td>";
	printImageHREF ('add', 'Add new', TRUE, 3);
	echo "</td></tr><tr><th colspan=4>configuration</th></tr>";
	echo "<tr><td colspan=4><textarea name=rsconfig rows=10 cols=80 tabindex=4></textarea></td></tr>";
	echo "</form></table>\n";
	finishPortlet();

	startPortlet ('Add many');
	echo "<form name=addmany action='${root}process.php'>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=op value=addMany>";
	echo "<input type=hidden name=pool_id value='${pool_id}'>";
	echo "<table border=0 align=center>\n<tr><td>";
	if (getConfigVar ('DEFAULT_IPV4_RS_INSERVICE') == 'yes')
		printImageHREF ('inservice', 'in service');
	else
		printImageHREF ('notinservice', 'NOT in service');
	echo "</td><td>Format: ";
	$formats = array
	(
		'ipvs_2' => 'ipvsadm -l -n (address and port)',
		'ipvs_3' => 'ipvsadm -l -n (address, port and weight)',
		'ssv_2' => 'SSV: &lt;IP address&gt; &lt;port&gt;'
	);
	printSelect ($formats, 'format');
	echo "</td><td><input type=submit value=Parse></td></tr>\n";
	echo "<tr><td colspan=3><textarea name=rawtext cols=100 rows=50></textarea></td></tr>\n";
	echo "</table>\n";
	finishPortlet();
}

function renderRSPoolLBForm ($pool_id = 0)
{
	global $root, $pageno, $tabno, $nextorder;
	showMessageOrError();

	$poolInfo = getRSPoolInfo ($pool_id);
	$vs_list = array ();
	foreach (getVSList() as $vsid => $vsinfo)
		$vs_list[$vsid] = buildVServiceName ($vsinfo) . (empty ($vsinfo['name']) ? '' : " (${vsinfo['name']})");

	if (count ($poolInfo['lblist']))
	{
		startPortlet ('Manage existing (' . count ($poolInfo['lblist']) . ')');
		echo "<table cellspacing=0 cellpadding=5 align=center class=cooltable>\n";
		echo "<tr><th>&nbsp;</th><th>LB</th><th>VS</th><th>VS config</th><th>RS config</th><th>&nbsp;</th></tr>\n";
		$order = 'odd';
		foreach ($poolInfo['lblist'] as $object_id => $vslist)
			foreach ($vslist as $vs_id => $configs)
			{
				$oi = getObjectInfo ($object_id);
				echo "<form action='${root}process.php' method=post>";
				echo "<input type=hidden name=page value='${pageno}'>\n";
				echo "<input type=hidden name=tab value='${tabno}'>\n";
				echo "<input type=hidden name=op value=updLB>";
				echo "<input type=hidden name=pool_id value='${pool_id}'>";
				echo "<input type=hidden name=vs_id value='${vs_id}'>";
				echo "<input type=hidden name=object_id value='${object_id}'>";
				echo "<tr valign=top class=row_${order}><td><a href='${root}process.php?page=${pageno}&tab=${tabno}&op=delLB&pool_id=${pool_id}&object_id=${object_id}&vs_id=${vs_id}'>";
				printImageHREF ('delete', 'Unconfigure');
				echo "</a></td>";
				echo "<td class=tdleft><a href='${root}?page=object&object_id=${object_id}'>${oi['dname']}</a></td>";
				echo "<td class=tdleft><a href='${root}?page=ipv4vs&vs_id=${vs_id}'>";
				$vsinfo = getVServiceInfo ($vs_id);
				echo buildVServiceName ($vsinfo) . '</a>';
				if (!empty ($vsinfo['name']))
					echo " (${vsinfo['name']})";
				echo "</td><td><textarea name=vsconfig>${configs['vsconfig']}</textarea></td>";
				echo "<td><textarea name=rsconfig>${configs['rsconfig']}</textarea></td><td>";
				printImageHREF ('save', 'Save changes', TRUE);
				echo "</td></tr></form>\n";
				$order = $nextorder[$order];
			}
		echo "</table>\n";
		finishPortlet();
	}

	startPortlet ('Add new');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<form action='${root}process.php' method=post>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=op value=addLB>";
	echo "<input type=hidden name=pool_id value='${pool_id}'>";
	echo "<tr valign=top><th>LB / VS</th><td class=tdleft><select name='object_id' tabindex=1>";
	foreach (explode (',', getConfigVar ('NATV4_PERFORMERS')) as $type)
		foreach (getNarrowObjectList ($type) as $object)
			echo "<option value='${object['id']}'>${object['dname']}</option>";
	echo "</select> ";
	printSelect ($vs_list, 'vs_id');
	echo "</td><td>";
	printImageHREF ('add', 'Configure LB', TRUE, 2);
	echo "</td></tr>\n";
	echo "<tr><th>VS config</th><td colspan=2><textarea name=vsconfig rows=10 cols=80></textarea></td></tr>";
	echo "<tr><th>RS config</th><td colspan=2><textarea name=rsconfig rows=10 cols=80></textarea></td></tr>";
	echo "</form></table>\n";
	finishPortlet();
}

function renderVServiceLBForm ($vs_id = 0)
{
	global $root, $pageno, $tabno, $nextorder;
	showMessageOrError();
	$vsinfo = getVServiceInfo ($vs_id);

	if (count ($vsinfo['rspool']))
	{
		startPortlet ('Manage existing (' . count ($vsinfo['rspool']) . ')');
		echo "<table cellspacing=0 cellpadding=5 align=center class=cooltable>\n";
		echo "<tr><th>&nbsp;</th><th>LB</th><th>RS pool</th><th>VS config</th><th>RS config</th><th>&nbsp;</th></tr>\n";
		$order = 'odd';
		foreach ($vsinfo['rspool'] as $pool_id => $rspinfo)
			foreach ($rspinfo['lblist'] as $object_id => $configs)
			{
				$oi = getObjectInfo ($object_id);
				echo "<form action='${root}process.php' method=post>";
				echo "<input type=hidden name=page value='${pageno}'>\n";
				echo "<input type=hidden name=tab value='${tabno}'>\n";
				echo "<input type=hidden name=op value=updLB>";
				echo "<input type=hidden name=pool_id value='${pool_id}'>";
				echo "<input type=hidden name=vs_id value='${vs_id}'>";
				echo "<input type=hidden name=object_id value='${object_id}'>";
				echo "<tr valign=top class=row_${order}><td><a href='${root}process.php?page=${pageno}&tab=${tabno}&op=delLB&pool_id=${pool_id}&object_id=${object_id}&vs_id=${vs_id}'>";
				printImageHREF ('delete', 'Unconfigure');
				echo "</a></td>";
				echo "<td class=tdleft><a href='${root}?page=object&object_id=${object_id}'>${oi['dname']}</a></td>";
				echo "<td class=tdleft><a href='${root}?page=ipv4rsp&pool_id=${pool_id}'>${rspinfo['name']}</a></td>";
				echo "<td><textarea name=vsconfig>${configs['vsconfig']}</textarea></td>";
				echo "<td><textarea name=rsconfig>${configs['rsconfig']}</textarea></td><td>";
				printImageHREF ('save', 'Save changes', TRUE);
				echo "</td></tr></form>\n";
				$order = $nextorder[$order];
			}
		echo "</table>\n";
		finishPortlet();
	}

	$rsplist = array();
	foreach (getRSPoolList() as $pool_id => $poolInfo)
		$rsplist[$pool_id] = $poolInfo['name'];
	startPortlet ('Add new');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<form action='${root}process.php' method=post>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=op value=addLB>";
	echo "<input type=hidden name=vs_id value='${vs_id}'>";
	echo "<tr valign=top><th>LB / RS pool</th><td class=tdleft><select name='object_id' tabindex=1>";
	foreach (explode (',', getConfigVar ('NATV4_PERFORMERS')) as $type)
		foreach (getNarrowObjectList ($type) as $object)
			echo "<option value='${object['id']}'>${object['dname']}</option>";
	echo "</select> ";
	printSelect ($rsplist, 'pool_id');
	echo "</td><td>";
	printImageHREF ('add', 'Configure LB', TRUE, 2);
	echo "</td></tr>\n";
	echo "<tr><th>VS config</th><td colspan=2><textarea name=vsconfig rows=10 cols=80></textarea></td></tr>";
	echo "<tr><th>RS config</th><td colspan=2><textarea name=rsconfig rows=10 cols=80></textarea></td></tr>";
	echo "</form></table>\n";
	finishPortlet();
}

function renderRSPool ($pool_id = 0)
{
	global $root;
	if ($pool_id <= 0)
	{
		showError ('Invalid pool_id', __FUNCTION__);
		return;
	}
	$poolInfo = getRSPoolInfo ($pool_id);
	if ($poolInfo == NULL)
	{
		showError ('getRSPoolInfo() returned NULL', __FUNCTION__);
		return;
	}

	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	if (!empty ($poolInfo['name']))
		echo "<tr><td colspan=2 align=center><h1>{$poolInfo['name']}</h1></td></tr>";
	echo "<tr><td class=pcleft>\n";

	startPortlet ('Configuration');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	if (!empty ($poolInfo['name']))
		echo "<tr><th width='50%' class=tdright>Pool name:</th><td class=tdleft>${poolInfo['name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Real servers:</th><td class=tdleft>" . count ($poolInfo['rslist']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Load balancers:</th><td class=tdleft>" . count ($poolInfo['lblist']) . "</td></tr>\n";
	printTagTRs ("${root}?page=ipv4rsplist&");
	echo "</table>";
	finishPortlet();

	startPortlet ('Load balancers (' . count ($poolInfo['lblist']) . ')');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>VS</th><th>LB</th><th>VS config</th><th>RS config</th></tr>";
	foreach ($poolInfo['lblist'] as $object_id => $vslist)
		foreach ($vslist as $vs_id => $configs)
	{
		$oi = getObjectInfo ($object_id);
		$vi = getVServiceInfo ($vs_id);
		echo "<tr valign=top><td class=tdleft><a href='${root}?page=ipv4vs&vs_id=${vs_id}'>";
		echo buildVServiceName ($vi);
		echo "</a></td><td class=tdleft><a href='${root}?page=object&object_id=${object_id}'>${oi['dname']}</a></td>";
		echo "<td class=tdleft><pre>${configs['vsconfig']}</pre></td>";
		echo "<td class=tdleft><pre>${configs['rsconfig']}</pre></td></tr>\n";
	}
	echo "</table>\n";
	finishPortlet();

	echo "</td><td class=pcright>\n";

	startPortlet ('Real servers (' . count ($poolInfo['rslist']) . ')');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>in service</th><th>address</th><th>port</th><th>RS configuration</th></tr>";
	foreach ($poolInfo['rslist'] as $rs)
	{
		echo "<tr valign=top><td align=center>";
		if ($rs['inservice'] == 'yes')
			printImageHREF ('inservice', 'in service');
		else
			printImageHREF ('notinservice', 'NOT in service');
		echo "</td><td class=tdleft><a href='${root}?page=ipaddress&ip=${rs['rsip']}'>${rs['rsip']}</a></td>";
		echo "<td class=tdleft>${rs['rsport']}</td><td class=tdleft><pre>${rs['rsconfig']}</pre></td></tr>\n";
	}
	echo "</table>\n";
	finishPortlet();

	echo "</td></tr></table>\n";
}

function renderVSList ()
{
	global $root, $pageno, $nextorder;
	$tagfilter = getTagFilter();
	$vslist = getVSList ($tagfilter, getTFMode());
	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";

	startPortlet ('Virtual services (' . count ($vslist) . ')');
	echo "<table class=widetable border=0 cellpadding=10 cellspacing=0 align=center>\n";
	echo "<tr><th>endpoint, name, tags</th><th>VS configuration</th><th>RS configuration</th></tr>";
	$order = 'odd';
	foreach ($vslist as $vsid => $vsinfo)
	{
		$vstags = loadIPv4VSTags ($vsid);
		echo "<tr align=left valign=top class=row_${order}><td class=tdleft><a href='${root}?page=ipv4vs&vs_id=${vsid}'>" . buildVServiceName ($vsinfo);
		echo "</a><br>${vsinfo['name']}";
		if (count ($vstags))
		{
			echo '<br>';
			echo serializeTags ($vstags, "${root}?page=${pageno}&");
		}
		echo "</td><td><pre>${vsinfo['vsconfig']}</pre></td>";
		echo "<td><pre>${vsinfo['rsconfig']}</pre></td>";
		echo "</tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table>";
	finishPortlet();
	echo '</td><td class=pcright>';
	renderTagFilterPortlet ($tagfilter, 'ipv4vs');
	echo '</td></tr></table>';
}

function renderVSListEditForm ()
{
	global $root, $pageno, $tabno, $nextorder;
	showMessageOrError();
	$protocols = array ('TCP' => 'TCP', 'UDP' => 'UDP');

	startPortlet ('Add new');
	echo "<form method=post action='${root}process.php'>\n";
	echo "<input type=hidden name=page value=${pageno}>\n";
	echo "<input type=hidden name=tab value=${tabno}>\n";
	echo "<input type=hidden name=op value=add>\n";
	echo "<table class=widetable border=0 cellpadding=10 cellspacing=0 align=center>\n";
	echo "<tr><th>&nbsp;</th><th>VIP</th><th>port</th><th>proto</th><th>name</th><th>&nbsp;</th></tr>";
	echo "<tr valign=top><td>&nbsp;</td>";
	echo "<td><input type=text name=vip tabindex=1></td>";
	$default_port = getConfigVar ('DEFAULT_SLB_VS_PORT');
	if ($default_port == 0)
		$default_port = '';
	echo "<td><input type=text name=vport size=5 value='${default_port}' tabindex=2></td><td>";
	printSelect ($protocols, 'proto', 'TCP');
	echo "</td>";
	echo "<td><input type=text name=name tabindex=4></td><td>";
	printImageHREF ('CREATE', 'create virtual service', TRUE);
	echo "</td></tr><tr><th>VS configuration</th><td colspan=4 class=tdleft><textarea name=vsconfig rows=10 cols=80></textarea></td>\n";
	echo "<td rowspan=2><h3>assign tags</h3>";
	renderTagSelect();
	echo "</td></tr>";
	echo "<tr><th>RS configuration</th><td colspan=4 class=tdleft><textarea name=rsconfig rows=10 cols=80></textarea></td></tr>\n";
	echo "</table>";
	echo "</form>\n";
	finishPortlet();

	$vslist = getVSList();
	if (!count ($vslist))
		return;
	startPortlet ('Manage existing (' . count ($vslist) . ')');
	echo "<table class=cooltable border=0 cellpadding=10 cellspacing=0 align=center>\n";
	echo "<tr><th>&nbsp;</th><th>VIP</th><th>port</th><th>proto</th><th>name</th>";
	echo "<th>VS configuration</th><th>RS configuration</th><th></th></tr>";
	$order = 'odd';
	foreach ($vslist as $vsid => $vsinfo)
	{
		echo "<form method=post action='${root}process.php'>\n";
		echo "<input type=hidden name=page value=${pageno}>\n";
		echo "<input type=hidden name=tab value=${tabno}>\n";
		echo "<input type=hidden name=op value=upd>\n";
		echo "<input type=hidden name=vs_id value=${vsid}>\n";
		echo "<tr valign=top class=row_${order}><td>";
		if ($vsinfo['poolcount'])
			printImageHREF ('nodelete', 'there are ' . $vsinfo['poolcount'] . ' RS pools configured');
		else
		{
			echo "<a href='${root}process.php?page=${pageno}&tab=${tabno}&op=del&vs_id=${vsid}'>";
			printImageHREF ('delete', 'delete virtual service');
			echo '</a>';
		}
		echo "</td><td class=tdleft><input type=text name=vip value='${vsinfo['vip']}'></td>";
		echo "<td class=tdleft><input type=text name=vport size=5 value='${vsinfo['vport']}'></td>";
		echo "<td class=tdleft>";
		printSelect ($protocols, 'proto', $vsinfo['proto']);
		echo "</td>";
		echo "<td class=tdleft><input type=text name=name value='${vsinfo['name']}'></td>";
		echo "<td><textarea name=vsconfig>${vsinfo['vsconfig']}</textarea></td>";
		echo "<td><textarea name=rsconfig>${vsinfo['rsconfig']}</textarea></td><td>";
		printImageHREF ('save', 'save changes', TRUE);
		echo "</td></tr></form>\n";
		$order = $nextorder[$order];
	}
	echo "</table>";
	finishPortlet();
}

function renderRSPoolList ()
{
	global $root, $pageno, $nextorder;
	$tagfilter = getTagFilter();
	$pool_list = getRSPoolList ($tagfilter, getTFMode());
	if ($pool_list === NULL)
	{
		showError ('getRSPoolList() failed', __FUNCTION__);
		return;
	}
	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";
	startPortlet ('RS pools (' . count ($pool_list) . ')');
	echo "<table class=widetable border=0 cellpadding=10 cellspacing=0 align=center>\n";
	echo "<tr><th>name, refcnt, tags</th><th>VS configuration</th><th>RS configuration</th></tr>";
	$order = 'odd';
	foreach ($pool_list as $pool_id => $pool_info)
	{
		$pooltags = loadIPv4RSPoolTags ($pool_id);
		echo "<tr valign=top class=row_${order}><td class=tdleft>";
		echo "<a href='${root}?page=ipv4rsp&pool_id=${pool_id}'>" . (empty ($pool_info['name']) ? 'ANONYMOUS' : $pool_info['name']) . '</a>';
		echo ($pool_info['refcnt'] ? ", ${pool_info['refcnt']}" : '');
		if (count ($pooltags))
		{
			echo '<br>';
			echo serializeTags ($pooltags, "${root}?page=${pageno}&");
		}
		echo "</td><td class=tdleft><pre>${pool_info['vsconfig']}</pre></td>";
		echo "<td class=tdleft><pre>${pool_info['rsconfig']}</pre></td>";
		echo "</tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table>";
	finishPortlet ();
	echo '</td><td class=pcright>';
	renderTagFilterPortlet ($tagfilter, 'ipv4rspool');
	echo '</td></tr></table>';
}

function editRSPools ()
{
	global $root, $pageno, $tabno, $nextorder;
	showMessageOrError();

	startPortlet ('Add new');
	echo "<form method=post action='${root}process.php'>\n";
	echo "<input type=hidden name=page value=${pageno}>\n";
	echo "<input type=hidden name=tab value=${tabno}>\n";
	echo "<input type=hidden name=op value=add>\n";
	echo "<table class=widetable border=0 cellpadding=10 cellspacing=0 align=center>\n";
	echo "<tr><th>name</th>";
	echo "<td class=tdleft><input type=text name=name tabindex=1></td><td>";
	printImageHREF ('CREATE', 'create real server pool', TRUE);
	echo "</td></tr><tr><th>VS configuration</th><td><textarea name=vsconfig rows=10 cols=80></textarea></td>";
	echo "<td rowspan=2><h3>assign tags</h3>";
	renderTagSelect();
	echo "</td></tr>";
	echo "<tr><th>RS configuration</th><td><textarea name=rsconfig rows=10 cols=80></textarea></td></tr>";
	echo "</table></form>";
	finishPortlet();

	$pool_list = getRSPoolList();
	if (!count ($pool_list))
		return;
	startPortlet ('Manage existing (' . count ($pool_list) . ')');
	echo "<table class=cooltable border=0 cellpadding=10 cellspacing=0 align=center>\n";
	echo "<tr><th>&nbsp;</th><th>name</th><th>VS configuration</th><th>RS configuration</th><th>&nbsp;</th></tr>";
	$order='odd';
	foreach ($pool_list as $pool_id => $pool_info)
	{
		echo "<form method=post action='${root}process.php'>\n";
		echo "<input type=hidden name=page value=${pageno}>\n";
		echo "<input type=hidden name=tab value=${tabno}>\n";
		echo "<input type=hidden name=op value=upd>\n";
		echo "<input type=hidden name=pool_id value=${pool_id}>\n";
		echo "<tr valign=top class=row_${order}><td>";
		if ($pool_info['refcnt'])
			printImageHREF ('nodelete', 'RS pool is used ' . $pool_info['refcnt'] . ' time(s)');
		else
		{
			echo "<a href='${root}process.php?page=${pageno}&tab=${tabno}&op=del&pool_id=${pool_id}'>";
			printImageHREF ('delete', 'delete real server pool');
			echo '</a>';
		}
		echo "</td>";
		echo "<td class=tdleft><input type=text name=name value='${pool_info['name']}'></td>";
		echo "<td><textarea name=vsconfig>${pool_info['vsconfig']}</textarea></td>";
		echo "<td><textarea name=rsconfig>${pool_info['rsconfig']}</textarea></td><td>";
		printImageHREF ('save', 'save changes', TRUE);
		echo "</td></tr></form>\n";
		$order = $nextorder[$order];
	}
	echo "</table>";
	finishPortlet();
}

function renderRealServerList ()
{
	global $root, $nextorder;
	$rslist = getRSList ();
	$pool_list = getRSPoolList ();
	echo "<table class=widetable border=0 cellpadding=10 cellspacing=0 align=center>\n";
	echo "<tr><th>RS pool</th><th>in service</th><th>real IP address</th><th>real port</th><th>RS configuration</th></tr>";
	$order = 'even';
	$last_pool_id = 0;
	foreach ($rslist as $rsinfo)
	{
		if ($last_pool_id != $rsinfo['rspool_id'])
		{
			$order = $nextorder[$order];
			$last_pool_id = $rsinfo['rspool_id'];
		}
		echo "<tr valign=top class=row_${order}><td><a href='${root}?page=ipv4rsp&pool_id=${rsinfo['rspool_id']}'>";
		echo empty ($pool_list[$rsinfo['rspool_id']]['name']) ? 'ANONYMOUS' : $pool_list[$rsinfo['rspool_id']]['name'];
		echo '</a></td><td align=center>';
		if ($rsinfo['inservice'] == 'yes')
			printImageHREF ('inservice', 'in service');
		else
			printImageHREF ('notinservice', 'NOT in service');
		echo "</td><td><a href='${root}?page=ipaddress&ip=${rsinfo['rsip']}'>${rsinfo['rsip']}</a></td>";
		echo "<td>${rsinfo['rsport']}</td>";
		echo "<td><pre>${rsinfo['rsconfig']}</pre></td>";
		echo "</tr>\n";
	}
	echo "</table>";
}

function renderLBList ()
{
	global $root, $nextorder;
	echo "<table class=widetable border=0 cellpadding=10 cellspacing=0 align=center>\n";
	echo "<tr><th>Object</th><th>RS pools configured</th></tr>";
	$oicache = array();
	$order = 'odd';
	foreach (getLBList() as $object_id => $poolcount)
	{
		if (!isset ($oicache[$object_id]))
			$oicache[$object_id] = getObjectInfo ($object_id);
		echo "<tr valign=top class=row_${order}><td><a href='${root}?page=object&object_id=${object_id}'>";
		echo $oicache[$object_id]['dname'] . '</a></td>';
		echo "<td>${poolcount}</td></tr>";
		$order = $nextorder[$order];
	}
	echo "</table>";
}

function renderRSPoolRSInServiceForm ($pool_id = 0)
{
	global $root, $pageno, $tabno;
	if ($pool_id <= 0)
	{
		showError ('Invalid pool_id', __FUNCTION__);
		return;
	}
	showMessageOrError();
	$poolInfo = getRSPoolInfo ($pool_id);
	$rscount = count ($poolInfo['rslist']);
	echo "<form method=post action='${root}process.php'>\n";
	echo "<input type=hidden name=page value=${pageno}>\n";
	echo "<input type=hidden name=tab value=${tabno}>\n";
	echo "<input type=hidden name=op value=upd>\n";
	echo "<input type=hidden name=pool_id value=${pool_id}>\n";
	echo "<input type=hidden name=rscount value=${rscount}>\n";
	echo "<table class=widetable border=0 cellpadding=10 cellspacing=0 align=center>\n";
	echo "<tr><th>RS address</th><th>RS port</th><th>RS configuration</th><th>in service</th></tr>\n";
	$recno = 1;
	foreach ($poolInfo['rslist'] as $rs_id => $rsinfo)
	{
		echo "<input type=hidden name=rsid_${recno} value=${rs_id}>\n";
		echo "<tr valign=top><td>${rsinfo['rsip']}</td><td>${rsinfo['rsport']}</td><td><pre>${rsinfo['rsconfig']}</pre></td>";
		echo "<td><input type=checkbox tabindex=${recno} name=inservice_${recno}" . ($rsinfo['inservice'] == 'yes' ? ' checked' : '') . "></td>";
		echo "</tr>";
		$recno++;
	}
	echo "<tr><td colspan=4 align=center>";
	printImageHREF ('SAVE', 'Save changes', TRUE, $recno);
	echo "</td></tr></table>\n</form>";
}

function renderLivePTR ($id = 0)
{
	if ($id == 0)
	{
		showError ("Invalid argument", __FUNCTION__);
		return;
	}
	showMessageOrError();
	if (isset($_REQUEST['pg']))
		$page = $_REQUEST['pg'];
	else
		$page=0;
	global $root, $pageno, $tabno;
	$maxperpage = getConfigVar ('IPV4_ADDRS_PER_PAGE');
	$range = getIPv4Network ($id);
	echo "<center><h1>${range['ip']}/${range['mask']}</h1><h2>${range['name']}</h2></center>\n";

	echo "<table class=objview border=0 width='100%'><tr><td class=pcleft>";
	startPortlet ('current records');
	$startip = $range['ip_bin'] & $range['mask_bin'];
	$endip = $range['ip_bin'] | $range['mask_bin_inv'];
	$realstartip = $startip;
	$realendip = $endip;
	$numpages = 0;
	if ($endip - $startip > $maxperpage)
	{
		$numpages = ($endip - $startip) / $maxperpage;
		$startip = $startip + $page * $maxperpage;
		$endip = $startip + $maxperpage - 1;
	}
	echo "<center>";
	if ($numpages)
		echo '<h3>' . long2ip ($startip) . ' ~ ' . long2ip ($endip) . '</h3>';
	for ($i=0; $i<$numpages; $i++)
		if ($i == $page)
			echo "<b>$i</b> ";
		else
			echo "<a href='${root}?page=${pageno}&tab=${tabno}&id=$id&pg=$i'>$i</a> ";
	echo "</center>";

	echo "<form method=post action=${root}process.php>";
	echo "<input type=hidden name=page value=${pageno}>\n";
	echo "<input type=hidden name=tab value=${tabno}>\n";
	echo "<input type=hidden name=op value=importPTRData>\n";
	echo "<input type=hidden name=id value=${id}>\n";
	echo '<input type=hidden name=addrcount value=' . ($endip - $startip + 1) . ">\n";

	echo "<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center'>\n";
	echo "<tr><th>address</th><th>current name</th><th>DNS data</th><th>import</th></tr>\n";
	$idx = 1;
	$cnt_match = $cnt_mismatch = $cnt_missing = 0;
	for ($ip = $startip; $ip <= $endip; $ip++)
	{
		// Find the (optional) DB name and the (optional) DNS record, then
		// compare values and produce a table row depending on the result.
		$addr = isset ($range['addrlist'][$ip]) ? $range['addrlist'][$ip] : array ('name' => '', 'reserved' => 'no');
		$straddr = long2ip ($ip);
		$ptrname = gethostbyaddr ($straddr);
		if ($ptrname == $straddr)
			$ptrname = '';
		echo "<input type=hidden name=addr_${idx} value=${straddr}>\n";
		echo "<input type=hidden name=descr_${idx} value=${ptrname}>\n";
		echo "<input type=hidden name=rsvd_${idx} value=${addr['reserved']}>\n";
		echo '<tr';
		$print_cbox = FALSE;
		if ($addr['name'] == $ptrname)
		{
			if (!empty ($ptrname))
			{
				echo ' class=trok';
				$cnt_match++;
			}
		}
		elseif (empty ($addr['name']) or empty ($ptrname))
		{
			echo ' class=trwarning';
			$print_cbox = TRUE;
			$cnt_missing++;
		}
		else
		{
			echo ' class=trerror';
			$print_cbox = TRUE;
			$cnt_mismatch++;
		}
		echo "><td class='tdleft";
		if (!empty ($range['addrlist'][$ip]['class']))
			echo ' ' . $range['addrlist'][$ip]['class'];
		echo "'><a href='${root}?page=ipaddress&ip=${straddr}'>${straddr}</a></td>";
		echo "<td class=tdleft>${addr['name']}</td><td class=tdleft>${ptrname}</td><td>";
		if ($print_cbox)
			echo "<input type=checkbox name=import_${idx} tabindex=${idx}>";
		else
			echo '&nbsp;';
		echo "</td></tr>\n";
		$idx++;
	}
	echo "<tr><td colspan=4 align=center><input type=submit value='Import selected records'></td></tr>";
	echo "</table>";
	echo "</form>";
	finishPortlet();

	echo "</td><td class=pcright>";

	startPortlet ('stats');
	echo "<table border=0 width='100%' cellspacing=0 cellpadding=2>";
	echo "<tr class=trok><th class=tdright>Exact matches:</th><td class=tdleft>${cnt_match}</td></tr>\n";
	echo "<tr class=trwarning><th class=tdright>Missing from DB/DNS:</th><td class=tdleft>${cnt_missing}</td></tr>\n";
	if ($cnt_mismatch)
		echo "<tr class=trerror><th class=tdright>Mismatches:</th><td class=tdleft>${cnt_mismatch}</td></tr>\n";
	echo "</table>\n";
	finishPortlet();

	echo "</td></tr></table>\n";
}

function renderAutoPortsForm ($object_id = 0)
{
	global $root, $pageno, $tabno;
	if ($object_id <= 0)
	{
		showError ('Invalid object_id', __FUNCTION__);
		return;
	}
	// If the below call has any data to display, the non-default redirection from the generator
	// has failed. Don't ignore the message log anyway.
	showMessageOrError();
	$info = getObjectInfo ($object_id);
	$ptlist = readChapter ('PortType');
	echo "<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center'>\n";
	echo "<caption>The following ports can be quickly added:</caption>";
	echo "<tr><th>type</th><th>name</th></tr>";
	foreach (getAutoPorts ($info['objtype_id']) as $autoport)
		echo "<tr><td>" . $ptlist[$autoport['type']] . "</td><td>${autoport['name']}</td></tr>";
	echo "<form method=post action='${root}process.php'>\n";
	echo "<input type=hidden name=page value=${pageno}>\n";
	echo "<input type=hidden name=tab value=${tabno}>\n";
	echo "<input type=hidden name=object_id value=${object_id}>\n";
	echo "<input type=hidden name=op value=generate>\n";
	echo "<tr><td colspan=2 align=center>";
	echo "<input type=submit value='Generate'>";
	echo "</td></tr>";
	echo "</table>";
}

function renderTagRowForViewer ($taginfo, $level = 0)
{
	echo '<tr><td align=left>';
	for ($i = 0; $i < $level; $i++)
		printImageHREF ('spacer');
	echo $taginfo['tag'];
	echo "</td></tr>\n";
	foreach ($taginfo['kids'] as $kid)
		renderTagRowForViewer ($kid, $level + 1);
}

function renderTagRowForCloud ($taginfo, $realm, $level = 0)
{
	global $root;
	echo '<tr><td align=left>';
	for ($i = 0; $i < $level; $i++)
		printImageHREF ('spacer');
	echo "<a href='${root}?page=objgroup&group_id=0&tagfilter[]=${taginfo['id']}'>";
	echo $taginfo['tag'] . '</a>';
	if (isset ($taginfo['refcnt'][$realm]))
		echo ' (' . $taginfo['refcnt'][$realm] . ')';
	echo "</td></tr>\n";
	foreach ($taginfo['kids'] as $kid)
		renderTagRowForCloud ($kid, $realm, $level + 1);
}

function renderTagRowForEditor ($taginfo, $level = 0)
{
	global $root, $pageno, $tabno, $taglist;
	echo '<tr><td class=tdleft>';
	for ($i = 0; $i < $level; $i++)
		printImageHREF ('spacer');
	$nrefs = 0;
	foreach ($taginfo['refcnt'] as $part)
		$nrefs += $part;
	if ($nrefs > 0 or count ($taginfo['kids']) > 0)
		printImageHREF ('nodelete', "${nrefs} references, " . count ($taginfo['kids']) . ' sub-tags');
	else
	{
		echo "<a href='${root}process.php?page=${pageno}&tab=${tabno}&op=destroyTag&tag_id=${taginfo['id']}'>";
		printImageHREF ('delete', 'Delete tag');
		echo "</a>";
	}
	echo "</td>\n<td>";
	echo "<form method=post action='${root}process.php?page=${pageno}&tab=${tabno}&op=updateTag'>";
	echo "<input type=hidden name=tag_id value=${taginfo['id']}><input type=text name=tag_name ";
	echo "value='${taginfo['tag']}'></td><td><select name=parent_id>";
	echo "<option value=0>-- NONE --</option>\n";
	foreach ($taglist as $tlinfo)
	{
		echo "<option value=${tlinfo['id']}" . ($tlinfo['id'] == $taginfo['parent_id'] ? ' selected' : '');
		echo ">${tlinfo['tag']}</option>";
	}
	echo "</select></td><td>";
	printImageHREF ('save', 'Save changes', TRUE);
	echo "</form></td></tr>\n";
	foreach ($taginfo['kids'] as $kid)
		renderTagRowForEditor ($kid, $level + 1);
}

function renderTagTree ()
{
	global $tagtree;
	echo '<center><table>';
	foreach ($tagtree as $taginfo)
	{
		echo '<tr>';
		renderTagRowForViewer ($taginfo);
		echo "</tr>\n";
	}
	echo '</table></center>';
}

function renderTagCloud ($realm = '')
{
	global $taglist, $tagtree;
	echo '<table>';
	foreach (getObjectiveTagTree ($tagtree, $realm) as $taginfo)
	{
		echo '<tr>';
		renderTagRowForCloud ($taginfo, $realm);
		echo "</tr>\n";
	}
	echo '</table>';
}

function renderTagTreeEditor ()
{
	global $root, $pageno, $tabno, $taglist, $tagtree;
	showMessageOrError();
	echo "<table class=objview border=0 width='100%'><tr><td class=pcleft>";
	startPortlet ('tag tree');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>&nbsp;</th><th>tag</th><th>parent</th><th>&nbsp;</th></tr>\n";
	foreach ($tagtree as $taginfo)
		renderTagRowForEditor ($taginfo);
	echo "<form action='${root}process.php' method=post>";
	echo "<input type=hidden name=page value='${pageno}'>";
	echo "<input type=hidden name=tab value='${tabno}'>";
	echo "<input type=hidden name=op value='createTag'>";
	echo "<tr><td class=tdleft>";
	printImageHREF ('grant', 'Create tag', TRUE, 102);
	echo '</td><td><input type=text name=tag_name tabindex=100></td><td><select name=parent_id tabindex=101>';
	echo "<option value=0>-- NONE --</option>\n";
	foreach ($taglist as $taginfo)
		echo "<option value=${taginfo['id']}>${taginfo['tag']}</option>";
	echo "</select></td><td>&nbsp;</td></tr>";
	echo "</form>\n";
	echo '</table>';
	finishPortlet();

	echo "</td><td><td class=pcright>";

	startPortlet ('fallen leaves');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>tag</th><th>parent</th><th>&nbsp;</th></tr>\n";
	foreach (getOrphanedTags() as $taginfo)
	{
		echo '<tr><td>';
		echo "<form method=post action='${root}process.php?page=${pageno}&tab=${tabno}&op=updateTag'>";
		echo "<input type=hidden name=tag_id value=${taginfo['id']}>";
		echo "<input type=hidden name=tag_name value=${taginfo['tag']}>";
		echo "${taginfo['tag']}</td><td><select name=parent_id>";
		echo "<option value=0>-- NONE --</option>\n";
		foreach ($taglist as $tlinfo)
		{
			echo "<option value=${tlinfo['id']}" . ($tlinfo['id'] == $taglist[$taginfo['id']]['parent_id'] ? ' selected' : '');
			echo ">${tlinfo['tag']}</option>";
		}
		echo "</select></td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</form></td></tr>\n";
	}
	echo '</table>';
	finishPortlet();
	echo "</td></tr></table>";
}

// Output a sequence of OPTION elements, selecting those, which are present on the
// explicit tags list.
function renderTagOption ($taginfo, $level = 0)
{
	global $expl_tags;
	$selected = '';
	foreach ($expl_tags as $etaginfo)
		if ($taginfo['id'] == $etaginfo['id'])
		{
			$selected = ' selected';
			break;
		}
	echo '<option value=' . $taginfo['id'] . "${selected}>";
	for ($i = 0; $i < $level; $i++)
		echo '-- ';
	echo $taginfo['tag'] . "</option>\n";
	foreach ($taginfo['kids'] as $kid)
		renderTagOption ($kid, $level + 1);
}

// Idem, but select those, which are shown on the $_REQUEST['tagfiler'] array.
// Ignore tag ids, which can't be found on the tree.
function renderTagOptionForFilter ($taginfo, $tagfilter, $realm, $level = 0)
{
	echo $level;
	$selected = '';
	foreach ($tagfilter as $filter_id)
		if ($taginfo['id'] == $filter_id)
		{
			$selected = ' selected';
			break;
		}
	echo '<option value=' . $taginfo['id'] . "${selected}>";
	for ($i = 0; $i < $level; $i++)
		echo '-- ';
	echo $taginfo['tag'] . (isset ($taginfo['refcnt'][$realm]) ? ' (' . $taginfo['refcnt'][$realm] . ')' : '') . "</option>\n";
	foreach ($taginfo['kids'] as $kid)
		renderTagOptionForFilter ($kid, $tagfilter, $realm, $level + 1);
}

function renderObjectTags ($id)
{
	renderEntityTagChainEditor ('object', 'object_id', $id);
}

function renderIPv4PrefixTags ($id)
{
	renderEntityTagChainEditor ('ipv4net', 'id', $id);
}

function renderRackTags ($id)
{
	renderEntityTagChainEditor ('rack', 'rack_id', $id);
}

function renderIPv4VSTags ($id)
{
	renderEntityTagChainEditor ('ip4vs', 'vs_id', $id);
}

function renderIPv4RSPoolTags ($id)
{
	renderEntityTagChainEditor ('ip4rspool', 'pool_id', $id);
}

function renderUserTags ($id)
{
	renderEntityTagChainEditor ('user', 'user_id', $id);
}

function renderEntityTagChainEditor ($entity_realm = '', $bypass_name, $entity_id = 0)
{
	global $tagtree;
	if ($entity_realm == '' or $entity_id <= 0)
	{
		showError ('Invalid or missing arguments', __FUNCTION__);
		return;
	}
	global $root, $pageno, $tabno, $expl_tags;
	showMessageOrError();
	startPortlet ('Tag list (' . count ($expl_tags) . ')');
	echo "<form method=post action='${root}process.php'>\n";
	echo "<input type=hidden name=page value=${pageno}>\n";
	echo "<input type=hidden name=tab value=${tabno}>\n";
	echo "<input type=hidden name=${bypass_name} value=${entity_id}>\n";
	echo "<input type=hidden name=op value=saveTags>\n";
	echo '<select name=taglist[] multiple size=' . getConfigVar ('MAXSELSIZE') . '>';
	foreach ($tagtree as $taginfo)
		renderTagOption ($taginfo);
	echo '</select><br>';
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</form>\n";
	finishPortlet();
}

function printTagTRs ($baseurl = '')
{
	global $expl_tags, $impl_tags, $auto_tags;
	if (getConfigVar ('SHOW_EXPLICIT_TAGS') == 'yes' and count ($expl_tags))
	{
		echo "<tr><th width='50%' class=tag_list_th>Explicit tags:</th><td class=tdleft>";
		echo serializeTags ($expl_tags, $baseurl) . "</td></tr>\n";
	}
	if (getConfigVar ('SHOW_IMPLICIT_TAGS') == 'yes' and count ($impl_tags))
	{
		echo "<tr><th width='50%' class=tag_list_th>Implicit tags:</th><td class=tdleft>";
		echo serializeTags ($impl_tags, $baseurl) . "</td></tr>\n";
	}
	if (getConfigVar ('SHOW_AUTOMATIC_TAGS') == 'yes' and count ($auto_tags))
	{
		echo "<tr><th width='50%' class=tag_list_th>Automatic tags:</th><td class=tdleft>";
		echo serializeTags ($auto_tags) . "</td></tr>\n";
	}
}

// Detect, filter and return requested tag filter mode: either 'and' or 'or'.
function getTFMode ()
{
	if (isset ($_REQUEST['tfmode']) and $_REQUEST['tfmode'] == 'all')
		return 'all';
	return 'any';
}

// Output a portlet with currently selected tags and prepare a form for update.
function renderTagFilterPortlet ($tagfilter, $realm, $bypass_name = '', $bypass_value = '')
{
	global $pageno, $tabno, $taglist, $tagtree;
	$objectivetags = getObjectiveTagTree ($tagtree, $realm);
	startPortlet ('Tag filter');
	if (!count ($objectivetags))
	{
		echo "None defined for current realm.<br>";
		return;
	}
	echo "<form method=get>\n";
	echo "<input type=hidden name=page value=${pageno}>\n";
	echo "<input type=hidden name=tab value=${tabno}>\n";
	if ($bypass_name != '')
		echo "<input type=hidden name=${bypass_name} value='${bypass_value}'>\n";
	echo '<select name=tagfilter[] multiple>';
	foreach ($objectivetags as $taginfo)
		renderTagOptionForFilter ($taginfo, $tagfilter, $realm);
	echo '</select><br>';
//	$tfmode = getTFMode();
//	echo '<input type=radio name=tfmode value=all' . ($tfmode == 'all' ? ' checked' : '') . '>all ';
//	echo '<input type=radio name=tfmode value=any' . ($tfmode == 'any' ? ' checked' : '') . '>any ';
	echo "<input type=submit value='Apply'></form>\n";
	finishPortlet();
}

// Dump all tags in a single SELECT element.
function renderTagSelect ()
{
	global $taglist, $tagtree;
	if (!count ($taglist))
	{
		echo "No tags defined";
		return;
	}
	echo '<select name=taglist[] multiple>';
	foreach ($tagtree as $taginfo)
		renderTagOption ($taginfo);
	echo '</select><br>';
}

function renderTagRollerForRow ($row_id)
{
	global $root, $pageno, $tabno;
	$a = rand (1, 20);
	$b = rand (1, 20);
	$sum = $a + $b;
	showMessageOrError();
	echo "<form method=post action='${root}process.php?page=${pageno}&tab=${tabno}&op=rollTags'>";
	echo "<input type=hidden name=row_id value='${row_id}'>";
	echo "<input type=hidden name=realsum value='${sum}'>";
	echo "<table border=1 align=center>";
	echo "<tr><td colspan=2>This special tool allows assigning tags to physical contents (racks <s>and contained objects</s>) of the current ";
	echo "rack row.<br>The tag(s) selected below will be ";
	echo "appended to already assigned tag(s) of each particular entity. </td></tr>";
	echo "<tr><th>Tags</th><td>";
	renderTagSelect();
	echo "</td></tr>";
	echo "<tr><th>Control question: the sum of ${a} and ${b}</th><td><input type=text name=sum></td></tr>";
	echo "<tr><td colspan=2 align=center><input type=submit value='Go!'></td></tr>";
	echo "</table></form>";
}

function renderObjectSLB ($object_id)
{
	global $root, $pageno, $tabno, $nextorder;
	showMessageOrError();
	$vs_list = $rsplist = array();
	foreach (getVSList() as $vsid => $vsinfo)
		$vs_list[$vsid] = buildVServiceName ($vsinfo) . (empty ($vsinfo['name']) ? '' : " (${vsinfo['name']})");
	foreach (getRSPoolList() as $pool_id => $poolInfo)
		$rsplist[$pool_id] = $poolInfo['name'];

	startPortlet ('Add new');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<form action='${root}process.php' method=post>";
	echo "<input type=hidden name=page value='${pageno}'>\n";
	echo "<input type=hidden name=tab value='${tabno}'>\n";
	echo "<input type=hidden name=op value=addLB>";
	echo "<input type=hidden name=object_id value='${object_id}'>";
	echo "<tr valign=top><th>VS / RS pool</th><td class=tdleft>";
	printSelect ($vs_list, 'vs_id');
	echo "</td><td>";
	printSelect ($rsplist, 'pool_id');
	echo "</td><td>";
	printImageHREF ('add', 'Configure LB', TRUE, 2);
	echo "</td></tr>\n";
	echo "<tr><th>VS config</th><td colspan=2><textarea name=vsconfig rows=10 cols=80></textarea></td></tr>";
	echo "<tr><th>RS config</th><td colspan=2><textarea name=rsconfig rows=10 cols=80></textarea></td></tr>";
	echo "</form></table>\n";
	finishPortlet();

	$myvslist = getRSPoolsForObject ($object_id);
	if (count ($myvslist))
	{
		startPortlet ('Manage existing (' . count ($myvslist) . ')');
		echo "<table cellspacing=0 cellpadding=5 align=center class=cooltable>\n";
		echo "<tr><th>&nbsp;</th><th>VS</th><th>RS pool</th><th>VS config</th><th>RS config</th><th>&nbsp;</th></tr>\n";
		$order = 'odd';
		foreach ($myvslist as $vs_id => $vsinfo)
		{
			echo "<form action='${root}process.php' method=post>";
			echo "<input type=hidden name=page value='${pageno}'>\n";
			echo "<input type=hidden name=tab value='${tabno}'>\n";
			echo "<input type=hidden name=op value=updLB>";
			echo "<input type=hidden name=pool_id value='${vsinfo['pool_id']}'>";
			echo "<input type=hidden name=vs_id value='${vs_id}'>";
			echo "<input type=hidden name=object_id value='${object_id}'>";
			echo "<tr valign=top class=row_${order}><td><a href='${root}process.php?page=${pageno}&tab=${tabno}&op=delLB&pool_id=${vsinfo['pool_id']}&object_id=${object_id}&vs_id=${vs_id}'>";
			printImageHREF ('delete', 'Unconfigure');
			echo "</a></td>";
			echo "</td><td class=tdleft><a href='${root}?page=ipv4vs&vs_id=${vs_id}'>";
			echo buildVServiceName ($vsinfo) . "</a>";
			if (!empty ($vsinfo['name']))
				echo '<br>' . $vsinfo['name'];
			echo "</td><td class=tdleft>" . $rsplist[$vsinfo['pool_id']] . "</td>";
			echo "<td><textarea name=vsconfig>${vsinfo['vsconfig']}</textarea></td>";
			echo "<td><textarea name=rsconfig>${vsinfo['rsconfig']}</textarea></td><td>";
			printImageHREF ('save', 'Save changes', TRUE);
			echo "</td></tr></form>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n";
		finishPortlet();
	}
}

function renderEditRSPool ($pool_id)
{
	global $root, $pageno, $tabno;
	showMessageOrError();
	$poolinfo = getRSPoolInfo ($pool_id);
	echo "<form method=post action='${root}process.php?page=${pageno}&tab=${tabno}&op=updIPv4RSP'>\n";
	echo "<input type=hidden name=pool_id value=${pool_id}>\n";
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>name:</th><td class=tdleft><input type=text name=name value='${poolinfo['name']}'></td></tr>\n";
	echo "<tr><th class=tdright>VS config:</th><td class=tdleft><textarea name=vsconfig rows=20 cols=80>${poolinfo['vsconfig']}</textarea></td></tr>\n";
	echo "<tr><th class=tdright>RS config:</th><td class=tdleft><textarea name=rsconfig rows=20 cols=80>${poolinfo['rsconfig']}</textarea></td></tr>\n";
	echo "<tr><th class=submit colspan=2>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>\n";
	echo "</table></form>\n";
}

function renderEditVService ($vsid)
{
	global $root, $pageno, $tabno;
	showMessageOrError();
	$protocols = array ('TCP' => 'TCP', 'UDP' => 'UDP');
	$vsinfo = getVServiceInfo ($vsid);
	echo "<form method=post action='${root}process.php?page=${pageno}&tab=${tabno}&op=updIPv4VS'>\n";
	echo "<input type=hidden name=vs_id value=${vsid}>\n";
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>VIP:</th><td class=tdleft><input type=text name=vip value='${vsinfo['vip']}'></td></tr>\n";
	echo "<tr><th class=tdright>port:</th><td class=tdleft><input type=text name=vport value='${vsinfo['vport']}'></td></tr>\n";
	echo "<tr><th class=tdright>proto:</th><td class=tdleft>";
		printSelect ($protocols, 'proto', $vsinfo['proto']);
		echo "</td></tr>\n";
	echo "<tr><th class=tdright>name:</th><td class=tdleft><input type=text name=name value='${vsinfo['name']}'></td></tr>\n";
	echo "<tr><th class=tdright>VS config:</th><td class=tdleft><textarea name=vsconfig rows=20 cols=80>${vsinfo['vsconfig']}</textarea></td></tr>\n";
	echo "<tr><th class=tdright>RS config:</th><td class=tdleft><textarea name=rsconfig rows=20 cols=80>${vsinfo['rsconfig']}</textarea></td></tr>\n";
	echo "<tr><th class=submit colspan=2>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>\n";
	echo "</table></form>\n";
}

function dump ($var)
{
	echo '<pre>';
	print_r ($var);
	echo '</pre>';
}

function renderRackCodeViewer ()
{
	$text = loadScript ('RackCode');
	dump ($text);
}

function renderRackCodeEditor ()
{
	global $root, $pageno, $tabno;
	$text = loadScript ('RackCode');
	showMessageOrError();
	echo "<form method=post action='${root}process.php?page=${pageno}&tab=${tabno}&op=saveRackCode'>";
	echo '<table border=0 align=center>';
	echo "<tr><td><textarea rows=50 cols=80 name=rackcode>" . $text . "</textarea></td></tr>\n";
	echo "<tr><td align=center><input type=submit value='save'></td></tr>";
	echo '</table>';
	echo "</form>";
}

function renderUser ($user_id)
{
	global $accounts, $expl_tags, $impl_tags;
	$username = getUsernameByID ($user_id);
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Account name:</th><td>${username}</td></tr>";
	echo '<tr><th class=tdright>Real name:</th><td>' . $accounts[$username]['user_realname'] . '</td></tr>';
	echo '<tr><th class=tdright>Enabled:</th><td>';
	// This is weird, some other image titles have to be used.
	if ($accounts[$username]['user_enabled'] == 'yes')
		printImageHREF ('blockuser', 'enabled');
	else
		printImageHREF ('unblockuser', 'disabled');
	echo '</td></tr>';
	// Using printTagTRs() is inappropriate here, because autotags will be filled with current user's
	// data, not the viewed one.
//	printTagTRs ("${root}?page=userlist&");
	if (getConfigVar ('SHOW_EXPLICIT_TAGS') == 'yes' and count ($expl_tags))
	{
		echo "<tr><th width='50%' class=tag_list_th>Explicit tags:</th><td class=tdleft>";
		echo serializeTags ($expl_tags, $baseurl) . "</td></tr>\n";
	}
	if (getConfigVar ('SHOW_IMPLICIT_TAGS') == 'yes' and count ($impl_tags))
	{
		echo "<tr><th width='50%' class=tag_list_th>Implicit tags:</th><td class=tdleft>";
		echo serializeTags ($impl_tags, $baseurl) . "</td></tr>\n";
	}
	$target_auto_tags = getUserAutoTags ($username);
	if (getConfigVar ('SHOW_AUTOMATIC_TAGS') == 'yes' and count ($target_auto_tags))
	{
		echo "<tr><th width='50%' class=tag_list_th>Automatic tags:</th><td class=tdleft>";
		echo serializeTags ($target_auto_tags) . "</td></tr>\n";
	}
	echo '</table>';
}

function renderMyPasswordEditor ()
{
	global $root, $pageno, $tabno, $remote_username, $accounts;
	showMessageOrError();
	echo "<form method=post action='${root}process.php?page=${pageno}&tab=${tabno}&op=changeMyPassword'>";
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Current password (*):</th><td><input type=password name=oldpassword tabindex=1></td></tr>";
	echo "<tr><th class=tdright>New password (*):</th><td><input type=password name=newpassword1 tabindex=2></td></tr>";
	echo "<tr><th class=tdright>New password again (*):</th><td><input type=password name=newpassword2 tabindex=3></td></tr>";
	echo "<tr><td colspan=2 align=center><input type=submit value='Change' tabindex=4></td></tr>";
	echo '</table></form>';
}

function renderAccessDenied ()
{
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
	echo "<head><title>RackTables: access denied</title>\n";
	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	echo "<link rel=stylesheet type='text/css' href=pi.css />\n";
	echo "<link rel=icon href='" . getFaviconURL() . "' type='image/x-icon' />";
	echo "<link rel=icon href='" . getFaviconURL() . "' type='image/x-icon' />";
	echo "</head><body>";
	global $user_tags, $auto_tags, $expl_tags, $impl_tags, $pageno, $tabno;
	echo "<table border=1 cellspacing=0 cellpadding=3 width='50%' align=center>\n";
	echo '<tr><th colspan=2><h3>';
	printImageHREF ('DENIED');
	echo ' access denied ';
	printImageHREF ('DENIED');
	echo '</h3></th></tr>';
	echo "<tr><th width='50%' class=tag_list_th>Explicit tags:</th><td class=tdleft>";
	echo serializeTags ($expl_tags) . "&nbsp;</td></tr>\n";
	echo "<tr><th width='50%' class=tag_list_th>Implicit tags:</th><td class=tdleft>";
	echo serializeTags ($impl_tags) . "&nbsp;</td></tr>\n";
	echo "<tr><th width='50%' class=tag_list_th>Automatic tags:</th><td class=tdleft>";
	echo serializeTags ($auto_tags) . "&nbsp;</td></tr>\n";
	echo "<tr><th width='50%' class=tag_list_th>This user tags:</th><td class=tdleft>";
	echo serializeTags ($user_tags) . "&nbsp;</td></tr>\n";
	echo "<tr><th width='50%' class=tag_list_th>Requested page:</th><td class=tdleft>${pageno}</td></tr>\n";
	echo "<tr><th width='50%' class=tag_list_th>Requested tab:</th><td class=tdleft>${tabno}</td></tr>\n";
	echo "</table>\n";
	echo "</body></html>";
}

function renderMyAccount ()
{
	global $remote_username, $accounts;
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0 width='50%'>";
	echo "<tr><td colspan=2 align=center><h1>${remote_username}</h1></td></tr>\n";
	echo "<tr><td colspan=2 align=center><h2>" . $accounts[$remote_username]['user_realname'] . "</h2></td></tr>\n";
	echo "</table>";
}

?>
