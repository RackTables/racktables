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
	'shared' => '<strong>S:</strong>',
	'router' => '<strong>R:</strong>',
);

// Let's have it here, so extensions can add their own images.
$image = array();
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
$image['files']['path'] = 'pix/files.png';
$image['files']['width'] = 218;
$image['files']['height'] = 200;
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
$image['download']['path'] = 'pix/download.png';
$image['download']['width'] = 16;
$image['download']['height'] = 16;
$image['DOWNLOAD']['path'] = 'pix/download-big.png';
$image['DOWNLOAD']['width'] = 32;
$image['DOWNLOAD']['height'] = 32;
$image['plug']['path'] = 'pix/tango-network-wired.png';
$image['plug']['width'] = 16;
$image['plug']['height'] = 16;
$image['cut']['path'] = 'pix/tango-edit-cut-16x16.png';
$image['cut']['width'] = 16;
$image['cut']['height'] = 16;
$image['CUT']['path'] = 'pix/tango-edit-cut-32x32.png';
$image['CUT']['width'] = 32;
$image['CUT']['height'] = 32;
$image['add']['path'] = 'pix/tango-list-add.png';
$image['add']['width'] = 16;
$image['add']['height'] = 16;
$image['ADD']['path'] = 'pix/tango-list-add-big.png';
$image['ADD']['width'] = 32;
$image['ADD']['height'] = 32;
$image['delete']['path'] = 'pix/tango-list-remove.png';
$image['delete']['width'] = 16;
$image['delete']['height'] = 16;
$image['destroy']['path'] = 'pix/tango-user-trash-16x16.png';
$image['destroy']['width'] = 16;
$image['destroy']['height'] = 16;
$image['nodestroy']['path'] = 'pix/tango-user-trash-16x16-gray.png';
$image['nodestroy']['width'] = 16;
$image['nodestroy']['height'] = 16;
$image['NODESTROY']['path'] = 'pix/tango-user-trash-32x32-gray.png';
$image['NODESTROY']['width'] = 32;
$image['NODESTROY']['height'] = 32;
$image['DESTROY']['path'] = 'pix/tango-user-trash-32x32.png';
$image['DESTROY']['width'] = 32;
$image['DESTROY']['height'] = 32;
$image['nodelete']['path'] = 'pix/tango-list-remove-shadow.png';
$image['nodelete']['width'] = 16;
$image['nodelete']['height'] = 16;
$image['inservice']['path'] = 'pix/tango-emblem-system.png';
$image['inservice']['width'] = 16;
$image['inservice']['height'] = 16;
$image['notinservice']['path'] = 'pix/tango-dialog-error.png';
$image['notinservice']['width'] = 16;
$image['notinservice']['height'] = 16;
$image['find']['path'] = 'pix/tango-system-search.png';
$image['find']['width'] = 16;
$image['find']['height'] = 16;
$image['next']['path'] = 'pix/tango-go-next.png';
$image['next']['width'] = 32;
$image['next']['height'] = 32;
$image['prev']['path'] = 'pix/tango-go-previous.png';
$image['prev']['width'] = 32;
$image['prev']['height'] = 32;
$image['clear']['path'] = 'pix/tango-edit-clear.png';
$image['clear']['width'] = 16;
$image['clear']['height'] = 16;
$image['CLEAR']['path'] = 'pix/tango-edit-clear-big.png';
$image['CLEAR']['width'] = 32;
$image['CLEAR']['height'] = 32;
$image['CLEAR gray']['path'] = 'pix/tango-edit-clear-gray-32x32.png';
$image['CLEAR gray']['width'] = 32;
$image['CLEAR gray']['height'] = 32;
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
$image['apply']['path'] = 'pix/tango-emblem-system.png';
$image['apply']['width'] = 16;
$image['apply']['height'] = 16;
$image['node-collapsed']['path'] = 'pix/node-collapsed.png';
$image['node-collapsed']['width'] = 16;
$image['node-collapsed']['height'] = 16;
$image['node-expanded']['path'] = 'pix/node-expanded.png';
$image['node-expanded']['width'] = 16;
$image['node-expanded']['height'] = 16;
$image['node-expanded-static']['path'] = 'pix/node-expanded-static.png';
$image['node-expanded-static']['width'] = 16;
$image['node-expanded-static']['height'] = 16;
$image['dragons']['path'] = 'pix/mitsudragon.png';
$image['dragons']['width'] = 125;
$image['dragons']['height'] = 21;
$image['LB']['path'] = 'pix/loadbalancer.png';
$image['LB']['width'] = 32;
$image['LB']['height'] = 32;
$image['RS pool']['path'] = 'pix/serverpool.png';
$image['RS pool']['width'] = 48;
$image['RS pool']['height'] = 16;
$image['VS']['path'] = 'pix/servicesign.png';
$image['VS']['width'] = 39;
$image['VS']['height'] = 62;
$image['router']['path'] = 'pix/router.png';
$image['router']['width'] = 32;
$image['router']['height'] = 32;
$image['ATTACH']['path'] = 'pix/crystal-attach-32x32.png';
$image['ATTACH']['width'] = 32;
$image['ATTACH']['height'] = 32;
$image['favorite']['path'] = 'pix/tango-emblem-favorite.png';
$image['favorite']['width'] = 16;
$image['favorite']['height'] = 16;
$image['computer']['path'] = 'pix/tango-computer.png';
$image['computer']['width'] = 16;
$image['computer']['height'] = 16;
$image['empty file']['path'] = 'pix/crystal-file-empty-32x32.png';
$image['empty file']['width'] = 32;
$image['empty file']['height'] = 32;
$image['text file']['path'] = 'pix/crystal-file-text-32x32.png';
$image['text file']['width'] = 32;
$image['text file']['height'] = 32;
$image['image file']['path'] = 'pix/crystal-file-image-32x32.png';
$image['image file']['width'] = 32;
$image['image file']['height'] = 32;
$image['NET']['path'] = 'pix/crystal-network-32x32.png';
$image['NET']['width'] = 32;
$image['NET']['height'] = 32;
$image['USER']['path'] = 'pix/crystal-edit-user-32x32.png';
$image['USER']['width'] = 32;
$image['USER']['height'] = 32;

// This may be populated later onsite, report rendering function will use it.
// See the $systemreport for structure.
$localreports = array();
$CodePressMap = array
(
	'sql' => 'sql',
	'php' => 'php',
	'html' => 'html',
	'css' => 'css',
	'js' => 'javascript',
);

$attrtypes = array
(
	'uint' => '[U] unsigned integer',
	'float' => '[F] floating point',
	'string' => '[S] string',
	'dict' => '[D] dictionary record'
);

// Rack thumbnail image width summands: "front", "interior" and "rear" elements w/o surrounding border.
$rtwidth = array
(
	0 => 9,
	1 => 21,
	2 => 9
);

// Main menu.
function renderIndex ()
{
?>
<table border=0 cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		<td>
			<div style='text-align: center; margin: 10px; '>
			<table width='100%' cellspacing=0 cellpadding=30 class=mainmenu border=0>
				<tr>
					<td>
						<h1><a href='<?php echo makeHref(array('page'=>'rackspace')) ?>'>Rackspace<br>
						<?php printImageHREF ('rackspace'); ?></a></h1>
					</td>
					<td>
						<h1><a href='<?php echo makeHref(array('page'=>'depot')) ?>'>Objects<br>
						<?php printImageHREF ('objects'); ?></a></h1>
					</td>
					<td>
						<h1><a href='<?php echo makeHref(array('page'=>'ipv4space')) ?>'>IPv4 space<br>
						<?php printImageHREF ('ipv4space'); ?></a></h1>
					</td>
					<td>
						<h1><a href='<?php echo makeHref(array('page'=>'files')) ?>'>Files<br>
						<?php printImageHREF ('files'); ?></a></h1>
					</td>
				</tr>
				<tr>
					<td>
						<h1><a href='<?php echo makeHref(array('page'=>'config')) ?>'>Configuration<br>
						<?php printImageHREF ('config'); ?></a></h1>
					</td>
					<td>
						<h1><a href='<?php echo makeHref(array('page'=>'reports')) ?>'>Reports<br>
						<?php printImageHREF ('reports'); ?></a></h1>
					</td>
					<td>
						<h1><a href='<?php echo makeHref(array('page'=>'ipv4slb')) ?>'>IPv4 SLB<br>
						<?php printImageHREF ('ipv4slb'); ?></a></h1>
					</td>
					<td>&nbsp;</td>
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
	echo "<table class=objview border=0 width='100%'><tr><td class=pcleft>";
	$cellfilter = getCellFilter();
	renderCellFilterPortlet ($cellfilter, 'rack');
	echo '</td><td class=pcright>';
	echo '<table border=0 cellpadding=10 cellpadding=1>';
	// generate thumb gallery
	global $nextorder;
	$rackwidth = getRackImageWidth();
	// Zero value effectively disables the limit.
	$maxPerRow = getConfigVar ('RACKS_PER_ROW');
	$order = 'odd';
	foreach (getRackRows() as $row_id => $row_name)
	{
		$rackList = filterCellList (listCells ('rack', $row_id), $cellfilter['expression']);
		if (!count ($rackList) and count ($cellfilter['expression']))
			continue;
		$rackListIdx = 0;
		echo "<tr class=row_${order}><th class=tdleft>";
		echo "<a href='".makeHref(array('page'=>'row', 'row_id'=>$row_id))."${cellfilter['urlextra']}'>";
		echo "${row_name}</a></th><td><table border=0 cellspacing=5><tr>";
		if (!count ($rackList))
			echo "<td>(empty row)</td>";
		else
			foreach ($rackList as $rack)
			{
				if ($rackListIdx > 0 and $maxPerRow > 0 and $rackListIdx % $maxPerRow == 0)
				{
					echo '</tr></table></tr>';
					echo "<tr class=row_${order}><th class=tdleft>${row_name} (continued)";
					echo "</th><td><table border=0 cellspacing=5><tr>";
					$order = $nextorder[$order];
				}
				echo "<td align=center><a href='".makeHref(array('page'=>'rack', 'rack_id'=>$rack['id']))."'>";
				echo "<img border=0 width=${rackwidth} height=";
				echo getRackImageHeight ($rack['height']);
				echo " title='${rack['height']} units'";
				echo "src='render_image.php?img=minirack&rack_id=${rack['id']}'>";
				echo "<br>${rack['name']}</a></td>";
				$rackListIdx++;
			}
		echo "</tr></table></tr>\n";
	}
	echo "</table>\n";
	echo "</td></tr></table>\n";
}

function renderRackspaceRowEditor ()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('addRow');
		echo "<tr><td>";
		printImageHREF ('create', 'Add new row', TRUE);
		echo "</td><td><input type=text name=name tabindex=100></td><td>";
		printImageHREF ('create', 'Add new row', TRUE, 101);
		echo "</td></tr></form>";
	}
	startPortlet ('Rows');
	echo "<table border=0 cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>&nbsp;</th><th>Name</th><th>&nbsp;</th></tr>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach (getRackRows() as $row_id => $row_name)
	{
		echo "<tr><td>";
		if ($rc = count (listCells ('rack', $row_id)))
			printImageHREF ('nodestroy', "${rc} rack(s) here");
		else
		{
			echo "<a href=\"".makeHrefProcess(array('op'=>'delete', 'row_id'=>$row_id))."\">";
			printImageHREF ('destroy', 'Delete row');
			echo "</a>";
		}
		printOpFormIntro ('updateRow', array ('row_id' => $row_id));
		echo "</td><td><input type=text name=name value='${row_name}'></td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</form></td></tr>\n";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo "</table><br>\n";
	finishPortlet();
}

function renderRow ($row_id)
{
	if (($rowInfo = getRackRowInfo ($row_id)) == NULL)
	{
		showError ('getRackRowInfo() failed', __FUNCTION__);
		return;
	}
	$cellfilter = getCellFilter();
	$rackList = filterCellList (listCells ('rack', $row_id), $cellfilter['expression']);
	// Main layout starts.
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";

	// Left portlet with row information.
	echo "<tr><td class=pcleft>";
	startPortlet ($rowInfo['name']);
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>Racks:</th><td class=tdleft>${rowInfo['count']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Units:</th><td class=tdleft>${rowInfo['sum']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>%% used:</th><td class=tdleft>";
	renderProgressBar (getRSUforRackRow ($rackList));
	echo "</td></tr>\n";
	echo "</table><br>\n";
	finishPortlet();
	renderCellFilterPortlet ($cellfilter, 'rack', 'row_id', $row_id);

	echo "</td><td class=pcright>";

	global $nextorder;
	$rackwidth = getRackImageWidth() * getConfigVar ('ROW_SCALE');
	// Maximum number of racks per row is proportionally less, but at least 1.
	$maxPerRow = max (floor (getConfigVar ('RACKS_PER_ROW') / getConfigVar ('ROW_SCALE')), 1);
	$rackListIdx = 0;
	$order = 'odd';
	startPortlet ('Racks');
	echo "<table border=0 cellspacing=5 align='center'><tr>";
	foreach ($rackList as $rack)
	{
		if ($rackListIdx % $maxPerRow == 0)
		{
			if ($rackListIdx > 0)
				echo '</tr>';
			echo '<tr>';
		}
		echo "<td align=center class=row_${order}><a href='".makeHref(array('page'=>'rack', 'rack_id'=>$rack['id']))."'>";
		echo "<img border=0 width=${rackwidth} height=" . (getRackImageHeight ($rack['height']) * getConfigVar ('ROW_SCALE'));
		echo " title='${rack['height']} units'";
		echo "src='render_image.php?img=minirack&rack_id=${rack['id']}'>";
		echo "<br>${rack['name']}</a></td>";
		$order = $nextorder[$order];
		$rackListIdx++;
	}
	echo "</tr></table>\n";
	finishPortlet();
	echo "</td></tr></table>";
}

function showError ($info = '', $location = 'N/A')
{
	if (preg_match ('/\.php$/', $location))
		$location = basename ($location);
	elseif ($location != 'N/A')
		$location = $location . '()';
	echo "<div class=msg_error>An error has occured in [${location}]. ";
	if (empty ($info))
		echo 'No additional information is available.';
	else
		echo "Additional information:<br><p>\n<pre>\n${info}\n</pre></p>";
	echo "Go back or try starting from <a href='".makeHref()."'>index page</a>.<br></div>\n";
}

// This function assures that specified argument was passed
// and is a number greater than zero.
function assertUIntArg ($argname, $caller = 'N/A', $allow_zero = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if (!is_numeric ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is not a number (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if ($_REQUEST[$argname] < 0)
	{
		showError ("Parameter '${argname}' is less than zero (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if (!$allow_zero and $_REQUEST[$argname] == 0)
	{
		showError ("Parameter '${argname}' is equal to zero (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
}

// This function assures that specified argument was passed
// and is a non-empty string.
function assertStringArg ($argname, $caller = 'N/A', $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if (!is_string ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is not a string (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if (!$ok_if_empty and empty ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is an empty string (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
}

function assertBoolArg ($argname, $caller = 'N/A', $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if (!is_string ($_REQUEST[$argname]) or $_REQUEST[$argname] != 'on')
	{
		showError ("Parameter '${argname}' is not a string (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
	if (!$ok_if_empty and empty ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is an empty string (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
}

function assertIPv4Arg ($argname, $caller = 'N/A', $ok_if_empty = FALSE)
{
	assertStringArg ($argname, $caller, $ok_if_empty);
	if (!empty ($_REQUEST[$argname]) and long2ip (ip2long ($_REQUEST[$argname])) !== $_REQUEST[$argname])
	{
		showError ("IPv4 address validation failed for value '" . $_REQUEST[$argname] . "' (calling function is [${caller}]).", __FUNCTION__);
		die();
	}
}

// This function renders rack as HTML table.
function renderRack ($rack_id, $hl_obj_id = 0)
{
	$rackData = spotEntity ('rack', $rack_id);
	amplifyCell ($rackData);
	markAllSpans ($rackData);
	if ($hl_obj_id > 0)
		highlightObject ($rackData, $hl_obj_id);
	markupObjectProblems ($rackData);
	$prev_id = getPrevIDforRack ($rackData['row_id'], $rack_id);
	$next_id = getNextIDforRack ($rackData['row_id'], $rack_id);
	echo "<center><table border=0><tr valign=middle>";
	echo "<td><h2><a href='".makeHref(array('page'=>'row', 'row_id'=>$rackData['row_id']))."'>${rackData['row_name']}</a> :</h2></td>";
	// FIXME: use 'bypass'?
	if ($prev_id != NULL)
	{
		echo "<td><a href='".makeHref(array('page'=>'rack', 'rack_id'=>$prev_id))."'>";
		printImageHREF ('prev', 'previous rack');
		echo "</a></td>";
	}
	echo "<td><h2><a href='".makeHref(array('page'=>'rack', 'rack_id'=>$rackData['id']))."'>${rackData['name']}</a></h2></td>";
	if ($next_id != NULL)
	{
		echo "<td><a href='".makeHref(array('page'=>'rack', 'rack_id'=>$next_id))."'>";
		printImageHREF ('next', 'next rack');
		echo "</a></td>";
	}
	echo "</h2></td></tr></table>\n";
	echo "<table class=rack border=0 cellspacing=0 cellpadding=1>\n";
	echo "<tr><th width='10%'>&nbsp;</th><th width='20%'>Front</th>";
	echo "<th width='50%'>Interior</th><th width='20%'>Back</th></tr>\n";
	for ($i = $rackData['height']; $i > 0; $i--)
	{
		echo "<tr><th>${i}</th>";
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
					echo "<a href='".makeHref(array('page'=>'object', 'object_id'=>$objectData['id']))."'>${objectData['dname']}</a></div>";
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
	startPortlet ('Add one');
	printOpFormIntro ('addRack', array ('got_data' => 'TRUE'));
	echo '<table border=0 align=center>';
	$defh = getConfigVar ('DEFAULT_RACK_HEIGHT');
	if ($defh == 0)
		$defh = '';
	echo "<tr><th class=tdright>Rack name (*):</th><td class=tdleft><input type=text name=rack_name tabindex=1></td>";
	echo "<td rowspan=4>Assign tags:<br>";
	renderNewEntityTags();
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>Height in units (*):</th><td class=tdleft><input type=text name=rack_height1 tabindex=2 value='${defh}'></td></tr>\n";
	echo "<tr><th class=tdright>Comment:</th><td class=tdleft><input type=text name=rack_comment tabindex=3></td></tr>\n";
	echo "<tr><td class=submit colspan=2>";
	printImageHREF ('CREATE', 'Add', TRUE);
	echo "</td></tr></table></form>";
	finishPortlet();

	startPortlet ('Add many');
	printOpFormIntro ('addRack', array ('got_mdata' => 'TRUE'));
	echo '<table border=0 align=center>';
	$defh = getConfigVar ('DEFAULT_RACK_HEIGHT');
	if ($defh == 0)
		$defh = '';
	echo "<tr><th class=tdright>Height in units (*):</th><td class=tdleft><input type=text name=rack_height2 value='${defh}'></td>";
	echo "<td rowspan=3 valign=top>Assign tags:<br>";
	renderNewEntityTags();
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>Rack names (*):</th><td class=tdleft><textarea name=rack_names cols=40 rows=25></textarea></td></tr>\n";
	echo "<tr><td class=submit colspan=2>";
	printImageHREF ('CREATE', 'Add', TRUE);
	echo '</form></table>';
	finishPortlet();
}

function renderEditObjectForm ($object_id)
{
	global $pageno;
	$object = getObjectInfo ($object_id);
	if ($object == NULL)
	{
		showError ('getObjectInfo() failed', __FUNCTION__);
		return;
	}
	startPortlet ();
	printOpFormIntro ('update');

	// static attributes
	echo '<table border=0 cellspacing=0 cellpadding=3 align=center>';
	echo "<tr><td>&nbsp;</td><th colspan=2><h2>Attributes</h2></th></tr>";
	echo "<tr><td>&nbsp;</td><th class=tdright>Type:</th><td class=tdleft>";
	printSelect (getObjectTypeList(), 'object_type_id', $object['objtype_id']);
	echo "</td></tr>\n";
	// baseline info
	echo "<tr><td>&nbsp;</td><th class=tdright>Common name:</th><td class=tdleft><input type=text name=object_name value='${object['name']}'></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Visible label:</th><td class=tdleft><input type=text name=object_label value='${object['label']}'></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Asset tag:</th><td class=tdleft><input type=text name=object_asset_no value='${object['asset_no']}'></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Barcode:</th><td class=tdleft><input type=text name=object_barcode value='${object['barcode']}'></td></tr>\n";
	// optional attributes
	$values = getAttrValues ($object_id);
	echo '<input type=hidden name=num_attrs value=' . count($values) . ">\n";
	if (count($values) > 0)
	{
		$i = 0;
		foreach ($values as $record)
		{
			echo "<input type=hidden name=${i}_attr_id value=${record['id']}>";
			echo '<tr><td>';
			if (!empty ($record['value']))
			{
				echo "<a href='".makeHrefProcess(array('op'=>'clearSticker', 'object_id'=>$object_id, 'attr_id'=>$record['id']))."'>";
				printImageHREF ('clear', 'Clear value');
				echo '</a>';
			}
			else
				echo '&nbsp;';
			echo '</td>';
			echo "<th class=sticker>${record['name']}:</th><td class=tdleft>";
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
					$chapter = cookOptgroups ($chapter, $object['objtype_id'], $record['key']);
					printNiftySelect ($chapter, "${i}_value", $record['key']);
					break;
			}
			echo "</td></tr>\n";
			$i++;
		}
	}
	echo "<tr><td>&nbsp;</td><th class=tdright>Has problems:</th><td class=tdleft><input type=checkbox name=object_has_problems";
	if ($object['has_problems'] == 'yes')
		echo ' checked';
	echo "></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Actions:</th><td class=tdleft><a href='".
		makeHrefProcess(array('op'=>'deleteObject', 'page'=>'depot', 'tab'=>'default', 'object_id'=>$object_id)).
		"' onclick=\"javascript:return confirm('Are you sure you want to delete the object?')\">Delete object</a></td></tr>\n";
	echo "<tr><td colspan=3><b>Comment:</b><br><textarea name=object_comment rows=10 cols=80>${object['comment']}</textarea></td></tr>";

	echo "<tr><th class=submit colspan=3>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</form></th></tr></table>\n";
	finishPortlet();

	echo '<table border=0 width=100%><tr><td>';
	startPortlet ('history');
	renderHistory ($pageno, $object_id);
	finishPortlet();
	echo '</td></tr></table>';
}

// This is a clone of renderEditObjectForm().
function renderEditRackForm ($rack_id)
{
	global $pageno;
	$rack = spotEntity ('rack', $rack_id);
	amplifyCell ($rack);

	startPortlet ('Rack attributes');
	printOpFormIntro ('updateRack');
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Rack row:</th><td class=tdleft>";
	printSelect (getRackRows(), 'rack_row_id', $rack['row_id']);
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>Name (required):</th><td class=tdleft><input type=text name=rack_name value='${rack['name']}'></td></tr>\n";
	echo "<tr><th class=tdright>Height (required):</th><td class=tdleft><input type=text name=rack_height value='${rack['height']}'></td></tr>\n";
	echo "<tr><th class=tdright>Comment:</th><td class=tdleft><input type=text name=rack_comment value='${rack['comment']}'></td></tr>\n";
	if (count ($rack['mountedObjects']) == 0)
	{
		echo "<tr><th class=tdright>Actions:</th><td class=tdleft><a href='".
        	        makeHrefProcess(array('op'=>'deleteRack', 'rack_id'=>$rack_id)).
                	"' onclick=\"javascript:return confirm('Are you sure you want to delete the rack?')\">Delete rack</a></td></tr>\n";
	}
	echo "<tr><td class=submit colspan=2>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>\n";
	echo '</form></table><br>';
	finishPortlet();
	
	startPortlet ('History');
	renderHistory ($pageno, $rack_id);
	finishPortlet();
}

// This is a helper for creators and editors.
// Input array keys are OPTION VALUEs and input array values are OPTION text.
function printSelect ($optionList, $select_name, $selected_id = NULL, $tabindex = NULL)
{
	echo "<select name=${select_name}" . ($tabindex ? " tabindex=${tabindex}" : '') . '>';
	foreach ($optionList as $dict_key => $dict_value)
		echo "<option value='${dict_key}'" . ($dict_key == $selected_id ? ' selected' : '') . ">${dict_value}</option>";
	echo "</select>";
}

// Input is a cooked list of OPTGROUPs, each with own sub-list of OPTIONs in the same
// format as printSelect() expects.
function printNiftySelect ($groupList, $select_name, $selected_id = NULL, $tabindex = NULL)
{
	// special treatment for ungrouped data
	if (count ($groupList) == 1 and isset ($groupList['other']))
	{
		printSelect ($groupList['other'], $select_name, $selected_id, $tabindex);
		return;
	}
	echo "<select name=${select_name}" . ($tabindex ? " tabindex=${tabindex}" : '') . '>';
	foreach ($groupList as $groupname => $groupdata)
	{
		echo "<optgroup label='${groupname}'>";
		foreach ($groupdata as $dict_key => $dict_value)
			echo "<option value='${dict_key}'" . ($dict_key == $selected_id ? ' selected' : '') . ">${dict_value}</option>";
		echo "</optgroup>\n";
	}
	echo "</select>";
}

// used by renderGridForm() and renderRackPage()
function renderRackInfoPortlet ($rackData)
{
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>Rack row:</th><td class=tdleft>${rackData['row_name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Name:</th><td class=tdleft>${rackData['name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Height:</th><td class=tdleft>${rackData['height']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>%% used:</th><td class=tdleft>";
	renderProgressBar (getRSUforRack ($rackData));
	echo "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Objects:</th><td class=tdleft>";
	echo count ($rackData['mountedObjects']);
	echo "</td></tr>\n";
	printTagTRs (makeHref(array('page'=>'rackspace', 'tab'=>'default'))."&");
	if (!empty ($rackData['comment']))
		echo "<tr><th width='50%' class=tdright>Comment:</th><td class=tdleft>${rackData['comment']}</td></tr>\n";
	echo '</table>';
	finishPortlet();
}

// This is a universal editor of rack design/waste.
// FIXME: switch to using printOpFormIntro()
function renderGridForm ($rack_id, $filter, $header, $submit, $state1, $state2)
{
	$rackData = spotEntity ('rack', $rack_id);
	amplifyCell ($rackData);
	$filter ($rackData);
	markupObjectProblems ($rackData);

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
	echo "<tr><th width='10%'>&nbsp;</th>";
	echo "<th width='20%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '0', ${rackData['height']})\">Front</a></th>";
	echo "<th width='50%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '1', ${rackData['height']})\">Interior</a></th>";
	echo "<th width='20%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '2', ${rackData['height']})\">Back</a></th></tr>\n";
	printOpFormIntro ('updateRack');
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

function renderRackProblems ($rack_id)
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

function renderRackObject ($object_id)
{
	global $nextorder, $aac;
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
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	if (!empty ($info['name']))
		echo "<tr><th width='50%' class=tdright>Common name:</th><td class=tdleft>${info['name']}</td></tr>\n";
	// FIXME: don't call spotEntity() each time, do it once in the beginning.
	elseif (considerConfiguredConstraint (spotEntity ('object', $object_id), 'NAMEWARN_LISTSRC'))
		echo "<tr><td colspan=2 class=msg_error>Common name is missing.</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Object type:</th><td class=tdleft><a href='";
	echo makeHref (array (
		'page' => 'depot',
		'tab' => 'default',
		'cfe' => '{$typeid_' . $info['objtype_id'] . '}'
	));
	echo "'>${info['objtype_name']}</a></td></tr>\n";
	if (!empty ($info['asset_no']))
		echo "<tr><th width='50%' class=tdright>Asset tag:</th><td class=tdleft>${info['asset_no']}</td></tr>\n";
	// FIXME: ditto
	elseif (considerConfiguredConstraint (spotEntity ('object', $object_id), 'ASSETWARN_LISTSRC'))
		echo "<tr><td colspan=2 class=msg_error>Asset tag is missing.</td></tr>\n";
	if (!empty ($info['label']))
		echo "<tr><th width='50%' class=tdright>Visible label:</th><td class=tdleft>${info['label']}</td></tr>\n";
	if (!empty ($info['barcode']))
		echo "<tr><th width='50%' class=tdright>Barcode:</th><td class=tdleft>${info['barcode']}</td></tr>\n";
	if ($info['has_problems'] == 'yes')
		echo "<tr><td colspan=2 class=msg_error>Has problems</td></tr>\n";
	foreach (getAttrValues ($object_id, TRUE) as $record)
		if (!empty ($record['value']))
			echo "<tr><th width='50%' class=sticker>${record['name']}:</th><td class=sticker>${record['a_value']}</td></tr>\n";
	printTagTRs
	(
		makeHref
		(
			array
			(
				'page'=>'depot',
				'tab'=>'default',
				'andor' => 'and',
				'cfe' => '{$typeid_' . $info['objtype_id'] . '}',
			)
		)."&"
	);
	echo "</table><br>\n";
	finishPortlet();

	if (!empty ($info['comment']))
	{
		startPortlet ('Comment');
		echo '<div class=commentblock>' . string_insert_hrefs ($info['comment']) . '</div>';
		finishPortlet ();
	}

	renderFilesPortlet ('object', $object_id);

	$ports = getObjectPortsAndLinks ($object_id);
	if (count ($ports))
	{
		startPortlet ('ports and links');
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
					echo "<td><a href='".makeHref(array('page'=>'object', 'object_id'=>$port['remote_object_id'], 'hl_port_id'=>$port['remote_id']))."'>${port['remote_object_name']}</a></td>";
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

	$alloclist = getObjectIPv4Allocations ($object_id);
	if (count ($alloclist))
	{
		startPortlet ('IPv4 addresses');
		echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
		if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
			echo "<tr><th>OS interface</th><th>IP address</th><th>network</th><th>routed by</th><th>peers</th></tr>\n";
		else
			echo "<tr><th>OS interface</th><th>IP address</th><th>peers</th></tr>\n";
		$hl_ipv4_addr = '';
		if (isset ($_REQUEST['hl_ipv4_addr']))
		{
			assertIPv4Arg ('hl_ipv4_addr', __FUNCTION__);
			$hl_ipv4_addr = $_REQUEST['hl_ipv4_addr'];
		}
		foreach ($alloclist as $dottedquad => $alloc)
		{
			$address_name = niftyString ($alloc['addrinfo']['name']);
			$class = $alloc['addrinfo']['class'];
			$secondclass = ($hl_ipv4_addr == $dottedquad) ? 'tdleft port_highlight' : 'tdleft';
			$netid = getIPv4AddressNetworkId ($dottedquad);
			if (NULL !== $netid)
			{
				$netinfo = spotEntity ('ipv4net', $netid);
				loadIPv4AddrList ($netinfo);
			}
			echo "<tr class='${class}' valign=top><td class=tdleft>${alloc['osif']}</td><td class='${secondclass}'>";
			if (NULL !== $netid)
				echo "<a href='".makeHref(array('page'=>'ipaddress', 'ip'=>$dottedquad, 'hl_object_id'=>$object_id))."'>${dottedquad}</a>";
			else
				echo $dottedquad;
			if (getConfigVar ('EXT_IPV4_VIEW') != 'yes')
				echo '<small>/' . (NULL === $netid ? '??' : $netinfo['mask']) . '</small>';
			echo '&nbsp;' . $aac[$alloc['type']];
			if (!empty ($alloc['addrinfo']['name']))
				echo ' (' . niftyString ($alloc['addrinfo']['name']) . ')';
			echo '</td>';
			if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
			{
				if (NULL === $netid)
					echo '<td class=sparenetwork>N/A</td><td class=sparenetwork>N/A</td>';
				else
				{
					echo "<td class='${secondclass}'>";
					renderCell ($netinfo);
					echo "</td>";
					// filter out self-allocation
					$other_routers = array();
					foreach (findRouters ($netinfo['addrlist']) as $router)
						if ($router['id'] != $object_id)
							$other_routers[] = $router;
					if (count ($other_routers))
						printRoutersTD ($other_routers);
					else
						echo "<td class='${secondclass}'>&nbsp;</td>";
				}
			}
			// peers
			echo "<td class='${secondclass}'>\n";
			$prefix = '';
			if ($alloc['addrinfo']['reserved'] == 'yes')
			{
				echo $prefix . '<strong>RESERVED</strong>';
				$prefix = '; ';
			}
			foreach ($alloc['addrinfo']['allocs'] as $allocpeer)
			{
				if ($allocpeer['object_id'] == $object_id)
					continue;
				echo $prefix . "<a href='".makeHref(array('page'=>'object', 'object_id'=>$allocpeer['object_id']))."'>";
				if (!empty ($allocpeer['osif']))
					echo $allocpeer['osif'] . '@';
				echo $allocpeer['object_name'] . '</a>';
				$prefix = '; ';
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
				$class = 'trerror';
				$osif = '';
				if (isset ($alloclist [$pf['localip']]))
				{
					$class = $alloclist [$pf['localip']]['addrinfo']['class'];
					$osif = $alloclist [$pf['localip']]['osif'] . ': ';
				}
				echo "<tr class='$class'>";
				echo "<td>${pf['proto']}</td><td class=tdleft>${osif}<a href='".makeHref(array('page'=>'ipaddress', 'tab'=>'default', 'ip'=>$pf['localip']))."'>${pf['localip']}</a>:${pf['localport']}</td>";
				echo "<td class=tdleft><a href='".makeHref(array('page'=>'ipaddress', 'tab'=>'default', 'ip'=>$pf['remoteip']))."'>${pf['remoteip']}</a>:${pf['remoteport']}</td>";
				$address = getIPv4Address ($pf['remoteip']);
				echo "<td class='description'>";
				if (count ($address['allocs']))
					foreach($address['allocs'] as $bond)
						echo "<a href='".makeHref(array('page'=>'object', 'tab'=>'default', 'object_id'=>$bond['object_id']))."'>${bond['object_name']}(${bond['name']})</a> ";
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
				echo "<td>${pf['proto']}/<a href='".makeHref(array('page'=>'ipaddress', 'tab'=>'default', 'ip'=>$pf['localip']))."'>${pf['localip']}</a>:${pf['localport']}</td>";
				echo "<td class='description'><a href='".makeHref(array('page'=>'object', 'tab'=>'default', 'object_id'=>$pf['object_id']))."'>${pf['object_name']}</a>";
				echo "</td><td><a href='".makeHref(array('page'=>'ipaddress', 'tab'=>'default', 'ip'=>$pf['remoteip']))."'>${pf['remoteip']}</a>:${pf['remoteport']}</td>";
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
		startPortlet ('Real server pools (' . count ($pools) . ')');
		echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
		echo "<tr><th>VS</th><th>RS pool</th><th>RS</th><th>VS config</th><th>RS config</th></tr>\n";
		foreach ($pools as $vs_id => $info)
		{
 			echo "<tr valign=top class=row_${order}><td class=tdleft>";
 			renderCell (spotEntity ('ipv4vs', $vs_id));
 			echo "</td><td class=tdleft>";
 			renderCell (spotEntity ('ipv4rspool', $info['pool_id']));
 			echo '</td><td class=tdleft>' . $info['rscount'] . '</td>';
 			echo "<td class=slbconf>${info['vsconfig']}</td>";
 			echo "<td class=slbconf>${info['rsconfig']}</td>";
			echo "</tr>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n";
		finishPortlet();
	}
	echo "</td>\n";

	// After left column we have (surprise!) right column with rackspace portlet only.
	echo "<td class=pcright>";
	// rackspace portlet
	startPortlet ('rackspace allocation');
	foreach (getResidentRacksData ($object_id, FALSE) as $rack_id)
		renderRack ($rack_id, $object_id);
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
	echo "<select name=${sname} multiple size=" . getConfigVar ('MAXSELSIZE') . " onchange='getElementsByName(\"updateObjectAllocation\")[0].submit()'>\n";
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
	if (isset ($_SESSION['log']))
	{
		printLog ($_SESSION['log']);
		unset($_SESSION['log']);
	}
}

// This function renders a form for port edition.
function renderPortsForObject ($object_id)
{
	function printNewItemTR ()
	{
		printOpFormIntro ('addPort');
		echo "<tr><td>";
		printImageHREF ('add', 'add a port', TRUE);
		echo "</td><td><input type=text size=8 name=port_name tabindex=100></td>\n";
		echo "<td><input type=text size=24 name=port_label tabindex=101></td><td>";
		printSelect (getPortTypes(), 'port_type_id', getConfigVar ('default_port_type'), 102);
		echo "<td><input type=text name=port_l2address tabindex=103></td>\n";
		echo "<td colspan=3>&nbsp;</td><td>";
		printImageHREF ('add', 'add a port', TRUE, 104);
		echo "</td></tr></form>";
	}
	startPortlet ('Ports and interfaces');
	$ports = getObjectPortsAndLinks ($object_id);
	usort($ports, 'sortByName');
	echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	echo "<tr><th>&nbsp;</th><th>Local name</th><th>Visible label</th><th>Port type</th><th>L2 address</th>";
	echo "<th>Rem. object</th><th>Rem. port</th><th>(Un)link or (un)reserve</th><th>&nbsp;</th></tr>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach ($ports as $port)
	{
		printOpFormIntro ('editPort', array ('port_id' => $port['id']));
		echo "<tr><td><a href='".makeHrefProcess(array('op'=>'delPort', 'port_id'=>$port['id'], 'object_id'=>$object_id, 'port_name'=>$port['name']))."'>";
		printImageHREF ('delete', 'Unlink and Delete this port');
		echo "</a></td>\n";
		echo "<td><input type=text name=name value='${port['name']}' size=8></td>";
		echo "<td><input type=text name=label value='${port['label']}' size=24></td>";
		if (!$port['remote_object_id'])
		{
			echo "<td>";
			printSelect (getPortTypes(), 'port_type_id', $port['type_id']);
			echo "</td>";
		}
		else
		{
			echo "<input type=hidden name=port_type_id value='${port['type_id']}'>";
			echo "<td class=tdleft>${port['type']}</td>\n";
		}
		echo "<td><input type=text name=l2address value='${port['l2address']}'></td>\n";
		if ($port['remote_object_id'])
		{
			echo "<td><a href='".makeHref(array('page'=>'object', 'object_id'=>$port['remote_object_id']))."'>${port['remote_object_name']}</a></td>";
			echo "<td>${port['remote_name']}</td>";
			echo "<td><a href='".
				makeHrefProcess(array(
					'op'=>'unlinkPort', 
					'port_id'=>$port['id'], 
					'object_id'=>$object_id, 
					'port_name'=>$port['name'], 
					'remote_port_name'=>$port['remote_name'], 
					'remote_object_name'=>$port['remote_object_name'])).
			"'>";
			printImageHREF ('cut', 'Unlink this port');
			echo "</a></td>";
		}
		elseif (!empty ($port['reservation_comment']))
		{
			echo "<td><b>Reserved;</b></td>";
			echo "<td><input type=text name=reservation_comment value='${port['reservation_comment']}'></td>";
			echo "<td><a href='".
				makeHrefProcess(array(
					'op'=>'useup',
					'port_id'=>$port['id'], 
					'object_id'=>$object_id)).
			"'>";
			printImageHREF ('clear', 'Use up this port');
			echo "</a></td>";
		}
		else
		{
			echo "<td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td>";
			echo "<a href='javascript:;' onclick='window.open(\"".makeHrefForHelper('portlist', array('port'=>$port['id'], 'type'=>$port['type_id'], 'object_id'=>$object_id, 'port_name'=>$port['name']))."\",\"findlink\",\"height=700, width=400, location=no, menubar=no, resizable=yes, scrollbars=no, status=no, titlebar=no, toolbar=no\");'>";
			printImageHREF ('plug', 'Link this port');
			echo "</a> <input type=text name=reservation_comment>";
			echo "</td>\n";
		}
		echo "<td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</td></form></tr>\n";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo "</table><br>\n";
	finishPortlet();

	startPortlet ('Add/update multiple ports');
	printOpFormIntro ('addMultiPorts');
	echo 'Format: <select name=format tabindex=201>';
	echo '<option value=c3600asy>Cisco 3600 async: sh line | inc TTY</option>';
	echo '<option value=fiwg selected>Foundry ServerIron/FastIron WorkGroup/Edge: sh int br</option>';
	echo '<option value=fisxii>Foundry FastIron SuperX/II4000: sh int br</option>';
	echo '<option value=ssv1>SSV:&lt;interface name&gt; &lt;MAC address&gt;</option>';
	echo "</select>";
	echo 'Default port type: ';
	printSelect (getPortTypes(), 'port_type', getConfigVar ('default_port_type'), 202);
	echo "<input type=submit value='Parse output' tabindex=204><br>\n";
	echo "<textarea name=input cols=100 rows=50 tabindex=203></textarea><br>\n";
	echo '</form>';
	finishPortlet();
}

function renderIPv4ForObject ($object_id)
{
	function printNewItemTR ()
	{
		global $aat;
		printOpFormIntro ('addIPv4Allocation');
		echo "<tr><td>";
		printImageHREF ('add', 'allocate', TRUE);
		echo "</td>";
		echo "<td class=tdleft><input type='text' size='10' name='bond_name' tabindex=100></td>\n";
		echo "<td class=tdleft><input type=text name='ip' tabindex=101></td>\n";
		echo "<td colspan=2>&nbsp;</td><td>";
		printSelect ($aat, 'bond_type', NULL, 102);
		echo "</td><td>&nbsp;</td><td>";
		printImageHREF ('add', 'allocate', TRUE, 103);
		echo "</td></tr></form>";
	}
	global $aat;
	startPortlet ('Allocations');
	$alloclist = getObjectIPv4Allocations ($object_id);
	echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	echo '<tr><th>&nbsp;</th><th>OS interface</th><th>IP address</th>';
	if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
		echo '<th>network</th><th>routed by</th>';
	echo '<th>type</th><th>misc</th><th>&nbsp</th></tr>';

	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach ($alloclist as $dottedquad => $alloc)
	{
		$class = $alloc['addrinfo']['class'];
		$netid = getIPv4AddressNetworkId ($dottedquad);
		if (NULL !== $netid)
		{
			$netinfo = spotEntity ('ipv4net', $netid);
			loadIPv4AddrList ($netinfo);
		}
		printOpFormIntro ('updIPv4Allocation', array ('ip' => $dottedquad));
		echo "<tr class='$class' valign=top><td><a href='".makeHrefProcess(array('op'=>'delIPv4Allocation', 'ip'=>$dottedquad, 'object_id'=>$object_id))."'>";
		printImageHREF ('delete', 'Delete this IPv4 address');
		echo "</a></td>";
		echo "<td class=tdleft><input type='text' name='bond_name' value='${alloc['osif']}' size=10></td><td class=tdleft>";
		if (NULL !== $netid)
			echo "<a href='".makeHref(array('page'=>'ipaddress', 'ip'=>$dottedquad))."'>${dottedquad}</a>";
		else
			echo $dottedquad;
		if (getConfigVar ('EXT_IPV4_VIEW') != 'yes')
			echo '<small>/' . (NULL === $netid ? '??' : $netinfo['mask']) . '</small>';
		if (!empty ($alloc['addrinfo']['name']))
			echo ' (' . niftyString ($alloc['addrinfo']['name']) . ')';
		echo '</td>';
		// FIXME: this a copy-and-paste from renderRackObject()
		if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
		{
			if (NULL === $netid)
				echo '<td class=sparenetwork>N/A</td><td class=sparenetwork>N/A</td>';
			else
			{
				echo '<td>';
				renderCell ($netinfo);
				echo '</td>';
				// filter out self-allocation
				$other_routers = array();
				foreach (findRouters ($netinfo['addrlist']) as $router)
					if ($router['id'] != $object_id)
						$other_routers[] = $router;
				if (count ($other_routers))
					printRoutersTD ($other_routers);
				else
					echo "<td>&nbsp;</td>";
			}
		}
		echo '<td>';
		printSelect ($aat, 'bond_type', $alloc['type']);
		echo "</td><td>";
		$prefix = '';
		if ($alloc['addrinfo']['reserved'] == 'yes')
		{
			echo $prefix . '<strong>RESERVED</strong>';
			$prefix = '; ';
		}
		foreach ($alloc['addrinfo']['allocs'] as $allocpeer)
		{
			if ($allocpeer['object_id'] == $object_id)
				continue;
			echo $prefix . "<a href='".makeHref(array('page'=>'object', 'object_id'=>$allocpeer['object_id']))."'>";
			if (!empty ($allocpeer['osif']))
				echo $allocpeer['osif'] . '@';
			echo $allocpeer['object_name'] . '</a>';
			$prefix = '; ';
		}
		echo "</td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</td></form></tr>\n";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();

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
// records 0~99 with success messages
				0 => array ('code' => 'success', 'format' => '%s'),
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
// ...
// ...
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
				58 => array ('code' => 'success', 'format' => "Deleted tag '%s'."),
				59 => array ('code' => 'success', 'format' => "Created tag '%s'."),
				60 => array ('code' => 'success', 'format' => "Updated tag '%s'."),
				61 => array ('code' => 'success', 'format' => 'Password changed successfully.'),
				62 => array ('code' => 'success', 'format' => 'gw: %s'),
				63 => array ('code' => 'success', 'format' => '%u change request(s) have been processed'),
				64 => array ('code' => 'success', 'format' => 'Port %s@%s has been assigned to VLAN %u'),
				65 => array ('code' => 'success', 'format' => "Added new rack '%s'"),
				66 => array ('code' => 'success', 'format' => "File sent Ok via handler '%s'"),
				67 => array ('code' => 'success', 'format' => "Tag rolling done, %u objects involved"),
				68 => array ('code' => 'success', 'format' => "Updated rack '%s'"),
				69 => array ('code' => 'success', 'format' => 'File "%s" was added successfully'),
				70 => array ('code' => 'success', 'format' => 'File "%s" was updated successfully'),
				71 => array ('code' => 'success', 'format' => 'File "%s" was linked successfully'),
				72 => array ('code' => 'success', 'format' => 'File was unlinked successfully'),
				73 => array ('code' => 'success', 'format' => 'File "%s" was deleted successfully'),
				74 => array ('code' => 'success', 'format' => 'Row "%s" was added successfully'),
				75 => array ('code' => 'success', 'format' => 'Row "%s" was updated successfully'),
				76 => array ('code' => 'success', 'format' => 'Object "%s" was deleted successfully'),
				77 => array ('code' => 'success', 'format' => 'Row "%s" was deleted successfully'),
				78 => array ('code' => 'success', 'format' => 'File "%s" saved Ok'),
				79 => array ('code' => 'success', 'format' => 'Rack "%s" was deleted successfully'),
				80 => array ('code' => 'success', 'format' => "Added new object '%s'"),

// records 100~199 with fatal error messages
				100 => array ('code' => 'error', 'format' => '%s'),
				101 => array ('code' => 'error', 'format' => 'Port name cannot be empty'),
				102 => array ('code' => 'error', 'format' => "Error creating user account '%s'"),
				103 => array ('code' => 'error', 'format' => 'User not found!'),
				104 => array ('code' => 'error', 'format' => "Error updating user account '%s'"),
// ...
// ...
// ...
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
// ...
				147 => array ('code' => 'error', 'format' => "Could not create tag '%s': %s"),
				148 => array ('code' => 'error', 'format' => "Could not update tag '%s': %s"),
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
				171 => array ('code' => 'error', 'format' => "Failed creating rack '%s'. Already exists in this row?"),
				172 => array ('code' => 'error', 'format' => 'Malformed request'),
				173 => array ('code' => 'error', 'format' => "Invalid IPv4 prefix '%s'"),
				174 => array ('code' => 'error', 'format' => 'Bad IPv4 address'),
				175 => array ('code' => 'error', 'format' => 'Invalid netmask'),
				176 => array ('code' => 'error', 'format' => 'This network already exists'),
				177 => array ('code' => 'error', 'format' => 'commitUpdateRack() failed'),
				178 => array ('code' => 'error', 'format' => 'file not found'),
				179 => array ('code' => 'error', 'format' => 'Declining outdated text. Re-edit the file for consistency.'),
				180 => array ('code' => 'error', 'format' => 'Error saving file, all changes lost!'),
				181 => array ('code' => 'error', 'format' => "file uploads not allowed, change 'file_uploads' parameter in php.ini"),
				182 => array ('code' => 'error', 'format' => 'SQL query failed: %s'),
				183 => array ('code' => 'error', 'format' => "Tag id '%s' does not exist."),
				184 => array ('code' => 'error', 'format' => 'Submitted form is invalid at line %u'),
				185 => array ('code' => 'error', 'format' => "Failed to add object '%s'"),
				186 => array ('code' => 'error', 'format' => 'Incomplete form has been ignored. Cheers.'),
				187 => array ('code' => 'error', 'format' => "Internal error in function '%s'"),

// records 200~299 with warnings
				200 => array ('code' => 'warning', 'format' => '%s'),
				201 => array ('code' => 'warning', 'format' => 'nothing happened...'),
				202 => array ('code' => 'warning', 'format' => 'gw: %s'),
				203 => array ('code' => 'warning', 'format' => 'Port %s seems to be the first in VLAN %u at this switch.'),
				204 => array ('code' => 'warning', 'format' => 'Check uplink/downlink configuration for proper operation.'),
				205 => array ('code' => 'warning', 'format' => '%u change request(s) have been ignored'),
				206 => array ('code' => 'warning', 'format' => 'Rack is not empty'),
				207 => array ('code' => 'warning', 'format' => 'Ignored empty request'),

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
function renderRackSpaceForObject ($object_id)
{
	$is_update = isset ($_REQUEST['rackmulti'][0]);
	$info = getObjectInfo ($object_id);
	if ($info == NULL)
	{
		showError ('getObjectInfo() failed', __FUNCTION__);
		return;
	}
	// Always process occupied racks plus racks chosen by user. First get racks with
	// already allocated rackspace...
	if (NULL === ($workingRacksData = getResidentRacksData ($object_id)))
		die; // some error already shown

	// ...and then add those chosen by user (if any).
	if (isset($_REQUEST['rackmulti']))
		foreach ($_REQUEST['rackmulti'] as $cand_id)
		{
			if (!isset ($workingRacksData[$cand_id]))
			{
				if (NULL == ($rackData = spotEntity ('rack', $cand_id)))
				{
					showError ('Rack not found', __FUNCTION__);
					return NULL;
				}
				amplifyCell ($rackData);
				$workingRacksData[$cand_id] = $rackData;
			}
		}

	printOpFormIntro ('updateObjectAllocation');

	// Do it only once...
	foreach ($workingRacksData as $rackId => &$rackData)
	{
		applyObjectMountMask ($rackData, $object_id);
		echo "<input type=\"hidden\" name=\"rackmulti[]\" value=\"$rackId\">";
	}
	// Now we workaround an old caveat: http://bugs.php.net/bug.php?id=37410
	unset ($rackData);

	// This is the time for rendering.

	// Main layout starts.
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0><tr>";

	// Left portlet with rack list.
	echo "<td class=pcleft height='1%'>";
	startPortlet ('Racks');
	$allRacksData = listCells ('rack');
	if (count ($allRacksData) <= getConfigVar ('RACK_PRESELECT_THRESHOLD'))
	{
		foreach ($allRacksData as $rack)
		{
			amplifyCell ($rack);
			$workingRacksData[$rack_id] = $rack;
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
		if ($is_update)
			mergeGridFormToRack ($rackData);
		echo "<td valign=top>";
		echo "<center>\n<h2>${rackData['name']}</h2>\n";
		echo "<table class=rack border=0 cellspacing=0 cellpadding=1>\n";
		echo "<tr><th width='10%'>&nbsp;</th>";
		echo "<th width='20%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '0', ${rackData['height']})\">Front</a></th>";
		echo "<th width='50%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '1', ${rackData['height']})\">Interior</a></th>";
		echo "<th width='20%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '2', ${rackData['height']})\">Back</a></th></tr>\n";
		renderAtomGrid ($rackData);
		echo "<tr><th width='10%'>&nbsp;</th>";
		echo "<th width='20%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '0', ${rackData['height']})\">Front</a></th>";
		echo "<th width='50%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '1', ${rackData['height']})\">Interior</a></th>";
		echo "<th width='20%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '2', ${rackData['height']})\">Back</a></th></tr>\n";
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
			$rackData = spotEntity ('rack', $rack_id);
			amplifyCell ($rackData);
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

function renderDepot ()
{
	global $pageno, $nextorder;
	$cellfilter = getCellFilter();
	$objects = filterCellList (listCells ('object'), $cellfilter['expression']);

	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";

	startPortlet ('Objects (' . count ($objects) . ')');
	if ($objects === NULL)
	{
		showError ('Fatal error retrieving object list', __FUNCTION__);
		return;
	}
	echo '<br><br><table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
	echo '<tr><th>Common name</th><th>Visible label</th><th>Asset tag</th><th>Barcode</th><th>Row/Rack</th></tr>';
	$order = 'odd';
	foreach ($objects as $obj)
	{
		if (isset ($_REQUEST['hl_object_id']) and $_REQUEST['hl_object_id'] == $obj['id'])
			$secondclass = 'tdleft port_highlight';
		else
			$secondclass = 'tdleft';
		$tags = loadEntityTags ('object', $obj['id']);
		echo "<tr class=row_${order} valign=top><td class='${secondclass}'><a href='".makeHref(array('page'=>'object', 'object_id'=>$obj['id']))."'><strong>${obj['dname']}</strong></a>";
		if (count ($tags))
			echo '<br><small>' . serializeTags ($tags, makeHref(array('page'=>$pageno, 'tab'=>'default')) . '&') . '</small>';
		echo "</td><td class='${secondclass}'>${obj['label']}</td>";
		echo "<td class='${secondclass}'>${obj['asset_no']}</td>";
		echo "<td class='${secondclass}'>${obj['barcode']}</td>";
		if ($obj['rack_id'])
			echo "<td class='${secondclass}'><a href='".makeHref(array('page'=>'row', 'row_id'=>$obj['row_id']))."'>${obj['Row_name']}</a>/<a href='".makeHref(array('page'=>'rack', 'rack_id'=>$obj['rack_id']))."'>${obj['Rack_name']}</a></td>";
		else
			echo "<td class='${secondclass}'>Unmounted</td>";
		echo '</tr>';
		$order = $nextorder[$order];
	}
	echo '</table>';
	finishPortlet();

	echo "</td><td class=pcright width='25%'>";

	renderCellFilterPortlet ($cellfilter, 'object');
	echo "</td></tr></table>\n";
}

// History viewer for history-enabled simple dictionaries.
function renderHistory ($object_type, $object_id)
{
	switch ($object_type)
	{
		case 'row':
			$query = "select ctime, user_name, name, comment from RackRowHistory where id = ${object_id} order by ctime";
			$header = '<tr><th>change time</th><th>author</th><th>rack row name</th><th>rack row comment</th></tr>';
			$extra = 3;
			break;
		case 'rack':
			$query =
				"select ctime, user_name, rh.name, rr.name as name, rh.height, rh.comment " .
				"from RackHistory as rh left join RackRow as rr on rh.row_id = rr.id " .
				"where rh.id = ${object_id} order by ctime";
			$header = '<tr><th>change time</th><th>author</th><th>rack name</th><th>rack row name</th><th>rack height</th><th>rack comment</th></tr>';
			$extra = 5;
			break;
		case 'object':
			$query =
				"select ctime, user_name, RackObjectHistory.name as name, label, barcode, asset_no, has_problems, dict_value, comment " .
				"from RackObjectHistory inner join Dictionary on objtype_id = dict_key join Chapter on Dictionary.chapter_id = Chapter.id " .
				"where Chapter.name = 'RackObjectType' and RackObjectHistory.id=${object_id} order by ctime";
			$header = '<tr><th>change time</th><th>author</th><th>common name</th><th>visible label</th><th>barcode</th><th>asset no</th><th>has problems?</th><th>object type</th><th>comment</th></tr>';
			$extra = 8;
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
	global $nextorder, $pageno, $tabno;
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
		echo "<tr class=${class}><td><a href='".makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'op_id'=>$row['mo_id']))."'>${row['ctime']}</a></td>";
		echo "<td>${row['user_name']}</td>";
		echo "<td>${row['ro_id']}</td><td>${row['objtype_name']}</td><td>${row['name']}</td><td>${row['comment']}</td>\n";
		echo "</tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table>\n";
	finishPortlet();

	echo '</td></tr></table>';
}

function renderIPv4SpaceRecords ($tree, &$tagcache, $baseurl, $target = 0, $level = 1)
{
	$self = __FUNCTION__;
	foreach ($tree as $item)
	{
		$total = $item['addrt'];
		if (getConfigVar ('IPV4_TREE_SHOW_USAGE') == 'yes')
		{
			loadIPv4AddrList ($item); // necessary to compute router list and address counter
			$used = $item['addrc'];
		}
		else
		{
			$item['addrlist'] = array();
			$item['addrc'] = 0;
		}
		if (isset ($item['id']))
		{
			if ($item['symbol'] == 'node-collapsed')
				$expandurl = "${baseurl}&eid=" . $item['id'] . "#netid" . $item['id'];
			elseif ($item['symbol'] == 'node-expanded')
				$expandurl = $baseurl . ($item['parent_id'] ? "&eid=${item['parent_id']}#netid${item['parent_id']}" : '');
			else
				$expandurl = '';
			echo "<tr valign=top>";
			printIPv4NetInfoTDs ($item, 'tdleft', $level, $item['symbol'], $expandurl);
			echo "<td class=tdcenter>";
			if ($target == $item['id'])
				echo "<a name=netid${target}></a>";
			if (getConfigVar ('IPV4_TREE_SHOW_USAGE') == 'yes')
			{
				renderProgressBar ($total ? $used/$total : 0);
				echo "<br><small>${used}/${total}</small>";
			}
			else
				echo "<small>${total}</small>";
			echo "</td>";
			if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
				printRoutersTD (findRouters ($item['addrlist']), $tagcache);
			echo "</tr>";
			if ($item['symbol'] == 'node-expanded' or $item['symbol'] == 'node-expanded-static')
				$self ($item['kids'], $tagcache, $baseurl, $target, $level + 1);
		}
		else
		{
			echo "<tr valign=top>";
			printIPv4NetInfoTDs ($item, 'tdleft sparenetwork', $level, $item['symbol']);
			echo "<td class=tdcenter>";
			if (getConfigVar ('IPV4_TREE_SHOW_USAGE') == 'yes')
			{
				renderProgressBar ($used/$total, 'sparenetwork');
				echo "<br><small>${used}/${total}</small>";
			}
			else
				echo "<small>${total}</small>";
			echo "</td><td>&nbsp;</td></tr>";
		}
	}
}

function renderIPv4Space ()
{
	global $pageno, $tabno;
	$cellfilter = getCellFilter();
	$netlist = filterCellList (listCells ('ipv4net'), $cellfilter['expression']);
	array_walk ($netlist, 'amplifyCell');

	$netcount = count ($netlist);
	// expand request can take either natural values or "ALL". Zero means no expanding.
	$eid = isset ($_REQUEST['eid']) ? $_REQUEST['eid'] : 0;
	$tree = prepareIPv4Tree ($netlist, $eid);

	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";
	startPortlet ("networks (${netcount})");
	echo '<h4>';
	if ($eid === 0)
		echo 'auto-collapsing at threshold ' . getConfigVar ('TREE_THRESHOLD') .
			" (<a href='".makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'eid'=>'ALL')) .
			$cellfilter['urlextra'] . "'>expand all</a>)";
	elseif ($eid === 'ALL')
		echo "expanding all (<a href='".makeHref(array('page'=>$pageno, 'tab'=>$tabno)) .
			$cellfilter['urlextra'] . "'>auto-collapse</a>)";
	else
	{
		$netinfo = spotEntity ('ipv4net', $eid);
		echo "expanding ${netinfo['ip']}/${netinfo['mask']} (<a href='".makeHref(array('page'=>$pageno, 'tab'=>$tabno))."'>auto-collapse</a> / <a href='".makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'eid'=>'ALL'))."'>expand&nbsp;all</a>)"; 
	}
	echo "</h4><table class='widetable' border=0 cellpadding=5 cellspacing=0 align='center'>\n";
	echo "<tr><th>prefix</th><th>name/tags</th><th>capacity</th>";
	if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
		echo "<th>routed by</th>";
	echo "</tr>\n";
	$tagcache = array();
	$baseurl = makeHref(array('page'=>$pageno, 'tab'=>$tabno)) . $cellfilter['urlextra'];
	renderIPv4SpaceRecords ($tree, $tagcache, $baseurl, $eid);
	echo "</table>\n";
	finishPortlet();
	echo '</td><td class=pcright>';
	renderCellFilterPortlet ($cellfilter, 'ipv4net');
	echo "</td></tr></table>\n";
}

function renderIPv4SLB ()
{
	global $page, $nextorder;

	startPortlet ('SLB configuration');
	echo "<table border=0 width='100%'><tr>";
	foreach (array ('ipv4vslist', 'ipv4rsplist', 'rservers', 'lbs') as $pno)
		echo "<td><h3><a href='".makeHref(array('page'=>$pno))."'>" . $page[$pno]['title'] . "</a></h3></td>";
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
		echo "<tr valign=top><td>&nbsp;</td>";
		foreach ($lblist as $lb_object_id)
		{
			#echo "<th><a href='".makeHref(array('page'=>'object', 'tab'=>'default', 'object_id'=>$lb_object_id))."'>" . $lbdname[$lb_object_id]  . "</a></th>";
			echo '<td>';
			renderLBCell ($lb_object_id);
			echo '</td>';
		}
		echo "</tr>\n";
		foreach ($summary as $vsid => $vsdata)
		{
			echo "<tr class=row_${order}><td class=tdleft>";
			renderCell (spotEntity ('ipv4vs', $vsid));
			echo "</td>";
			foreach ($lblist as $lb_object_id)
			{
				echo '<td class=tdleft>';
				if (!isset ($vsdata['lblist'][$lb_object_id]))
					echo '&nbsp;';
				else
				{
					echo $vsdata['lblist'][$lb_object_id]['size'];
//					echo " (<a href='".makeHref(array('page'=>'ipv4rspool', 'pool_id'=>$vsdata['lblist'][$lb_object_id]['id'])). "'>";
//					echo $vsdata['lblist'][$lb_object_id]['name'] . '</a>)';
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

function renderIPv4SpaceEditor ()
{
	// IPv4 validator
?>
	<script type="text/javascript">
	function init() {
		document.add_new_range.range.setAttribute('match', "^\\d\\d?\\d?\\.\\d\\d?\\d?\\.\\d\\d?\\d?\\.\\d\\d?\\d?\\/\\d\\d?$");

		Validate.init();
	}
	window.onload=init;
	</script>
<?php

	startPortlet ("Add new");
	echo '<table border=0 cellpadding=10 align=center>';
	// This form requires a name, so JavaScript validator can find it.
	// No printOpFormIntro() hence
	echo "<form method=post name='add_new_range' action='".makeHrefProcess()."'>\n";
	echo "<input type=hidden name=op value=addIPv4Prefix>\n";
	// tags column
	echo '<tr><td rowspan=4><h3>assign tags</h3>';
	renderNewEntityTags();
	echo '</td>';
	// inputs column
	echo "<th class=tdright>prefix</th><td class=tdleft><input type=text name='range' size=18 class='live-validate' tabindex=1></td>";
	echo "<tr><th class=tdright>name</th><td class=tdleft><input type=text name='name' size='20' tabindex=2></td></tr>";
	echo "<tr><th class=tdright>connected network</th><td class=tdleft><input type=checkbox name='is_bcast' tabindex=3></td></tr>";
	echo "<tr><td colspan=2>";
	printImageHREF ('CREATE', 'Add a new network', TRUE, 4);
	echo '</td></tr>';
	echo "</form></table><br><br>\n";
	finishPortlet();

	$addrspaceList = listCells ('ipv4net');
	array_walk ($addrspaceList, 'amplifyCell');
	if (count ($addrspaceList))
	{
		startPortlet ('Manage existing (' . count ($addrspaceList) . ')');
		echo "<table class='widetable' border=0 cellpadding=5 cellspacing=0 align='center'>\n";
		echo "<tr><th>&nbsp;</th><th>prefix</th><th>name</th><th>&nbsp;</th></tr>";
		foreach ($addrspaceList as $netinfo)
		{
			echo "<form method=post action='".makeHrefProcess(array('op'=>'updIPv4Prefix', 'id'=>$netinfo['id']))."'>";
			echo "<tr valign=top><td>";
			if (getConfigVar ('IPV4_JAYWALK') == 'yes')
			{
				echo "<a href='".makeHrefProcess(array('op'=>'delIPv4Prefix', 'id'=>$netinfo['id']))."'>";
				printImageHREF ('destroy', 'Delete this prefix');
				echo "</a>";
			}
			else // only render clickable image for empty networks
			{
				$netdata = spotEntity ('ipv4net', $netinfo['id']);
				loadIPv4AddrList ($netdata);
				if (count ($netdata['addrlist']))
					printImageHREF ('nodestroy', 'There are ' . count ($netdata['addrlist']) . ' allocations inside');
				else
				{
					echo "<a href='".makeHrefProcess(array('op'=>'delIPv4Prefix', 'id'=>$netinfo['id']))."'>";
					printImageHREF ('destroy', 'Delete this prefix');
					echo "</a>";
				}

			}
			echo "</td>\n<td class=tdleft>${netinfo['ip']}/${netinfo['mask']}</td>";
			echo "<td><input type=text name=name size=40 value='${netinfo['name']}'>";
			echo "</td><td>";
			printImageHREF ('save', 'Save changes', TRUE);
			echo "</td></tr></form>\n";
		}
		echo "</table>";
		finishPortlet();
	}
}

function renderIPv4Network ($id)
{
	global $pageno, $tabno, $aac2;
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

	$range = spotEntity ('ipv4net', $id);
	loadIPv4AddrList ($range);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${range['ip']}/${range['mask']}</h1><h2>${range['name']}</h2></td></tr>\n";

	echo "<tr><td class=pcleft width='50%'>";
	startPortlet ('summary');
	$total = ($range['ip_bin'] | $range['mask_bin_inv']) - ($range['ip_bin'] & $range['mask_bin']) + 1;
	$used = count ($range['addrlist']);
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";

	echo "<tr><th width='50%' class=tdright>%% used:</th><td class=tdleft>";
	renderProgressBar ($used/$total);
	echo "&nbsp;${used}/${total}</td></tr>\n";

	if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
	{
		// Build a backtrace from all parent networks.
		$clen = $range['mask'];
		$backtrace = array();
		while (NULL !== ($upperid = getIPv4AddressNetworkId ($range['ip'], $clen)))
		{
			$upperinfo = spotEntity ('ipv4net', $upperid);
			$clen = $upperinfo['mask'];
			$backtrace[] = $upperinfo;
		}
		$arrows = count ($backtrace);
		foreach (array_reverse ($backtrace) as $ainfo)
		{
			echo "<tr><th width='50%' class=tdright>";
			for ($i = 0; $i < $arrows; $i++)
				echo '&uarr;';
			$arrows--;
			echo "</th><td class=tdleft>";
			renderCell ($ainfo);
			echo "</td></tr>";
		}
		echo "<tr><th width='50%' class=tdright>&rarr;</th>";
		echo "<td class=tdleft>";
		renderCell ($range);
		echo "</td></tr>";
		// FIXME: get and display nested networks
		// $theitem = pickLeaf ($ipv4tree, $id);
	}

	echo "<tr><th width='50%' class=tdright>Netmask:</th><td class=tdleft>";
	echo $netmaskbylen[$range['mask']];
	echo "</td></tr>\n";

	echo "<tr><th width='50%' class=tdright>Netmask:</th><td class=tdleft>";
	printf ('0x%08X', binMaskFromDec ($range['mask']));
	echo "</td></tr>\n";

	echo "<tr><th width='50%' class=tdright>Wildcard bits:</th><td class=tdleft>";
	echo $wildcardbylen[$range['mask']];
	echo "</td></tr>\n";

	$routers = findRouters ($range['addrlist']);
	if (getConfigVar ('EXT_IPV4_VIEW') == 'yes' and count ($routers))
	{
		echo "<tr><th width='50%' class=tdright>Routed by:</th>";
		printRoutersTD ($routers);
		echo "</tr>\n";
	}

	printTagTRs (makeHref(array('page'=>'ipv4space', 'tab'=>'default'))."&");
	echo "</table><br>\n";
	finishPortlet();

	renderFilesPortlet ('ipv4net', $id);
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
			echo "<a href='".makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'id'=>$id, 'pg'=>$i))."'>$i</a> ";
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
			echo "<tr><td class=tdleft><a href='".makeHref(array('page'=>'ipaddress', 'ip'=>ip_long2quad($ip)))."'>" . ip_long2quad($ip);
			echo "</a></td><td class='${secondstyle}'>&nbsp;</td><td class='${secondstyle}'>&nbsp;</td></tr>\n";
			continue;
		}
		$addr = $range['addrlist'][$ip];
		echo "<tr class='${addr['class']}'>";

		echo "<td class=tdleft><a href='".makeHref(array('page'=>'ipaddress', 'ip'=>$addr['ip']))."'>${addr['ip']}</a></td>";
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
			echo "<a href='".makeHref(array('page'=>'object', 'object_id'=>$ref['object_id'], 'hl_ipv4_addr'=>$addr['ip']))."'>";
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
			echo "${delim}<a href='".makeHref(array('page'=>'object', 'object_id'=>$ref['object_id']))."'>";
			echo "${ref['object_name']}</a>:<a href='".makeHref(array('page'=>'ipv4vs', 'vs_id'=>$ref['vs_id']))."'>";
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
			echo "${delim}&rarr;${ref['rsport']}@<a href='".makeHref(array('page'=>'ipv4rspool', 'pool_id'=>$ref['rspool_id']))."'>";
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
	$netdata = spotEntity ('ipv4net', $id);
	echo "<center><h1>${netdata['ip']}/${netdata['mask']}</h1></center>\n";
	echo "<table border=0 cellpadding=10 cellpadding=1 align='center'>\n";
	printOpFormIntro ('editRange');
	echo "<tr><td class='tdright'>Name:</td><td class='tdleft'><input type=text name=name size=20 value='${netdata['name']}'></tr>";
	echo "<tr><td colspan=2 class=tdcenter>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></form></tr></table>\n";
}

function renderIPv4Address ($dottedquad)
{
	global $aat;
	$address = getIPv4Address ($dottedquad);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${dottedquad}</h1></td></tr>\n";
	if (!empty ($address['name']))
		echo "<tr><td colspan=2 align=center><h2>${address['name']}</h2></td></tr>\n";

	echo "<tr><td class=pcleft>";
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>Allocations:</th><td class=tdleft>" . count ($address['allocs']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Originated NAT connections:</th><td class=tdleft>" . count ($address['outpf']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Arriving NAT connections:</th><td class=tdleft>" . count ($address['inpf']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>SLB virtual services:</th><td class=tdleft>" . count ($address['lblist']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>SLB real servers:</th><td class=tdleft>" . count ($address['rslist']) . "</td></tr>\n";
	printTagTRs();
	echo "</table><br>\n";
	finishPortlet();
	echo "</td>\n";

	echo "<td class=pcright>";

	if (isset ($address['class']))
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
			echo "<tr class='$class'><td class=tdleft><a href='".makeHref(array('page'=>'object', 'object_id'=>$bond['object_id'], 'hl_ipv4_addr'=>$dottedquad))."'>${bond['object_name']}</td><td class='${secondclass}'>${bond['name']}</td><td class='${secondclass}'><strong>";
			echo $aat[$bond['type']];
			echo "</strong></td></tr>\n";
		}
		echo "</table><br><br>";
		finishPortlet();
	}

	// FIXME: The returned list is structured differently, than we expect it to be. One of the sides
	// must be fixed.
	if (count ($address['lblist']))
	{
		startPortlet ('Virtual services (' . count ($address['lblist']) . ')');
		echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>\n";
		echo "<tr><th>VS</th><th>name</th></tr>\n";
		foreach ($address['lblist'] as $vsinfo)
		{
			echo "<tr><td class=tdleft><a href='".makeHref(array('page'=>'ipv4vs', 'vs_id'=>$vsinfo['vs_id']))."'>";
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
			echo "</td><td class=tdleft>${rsinfo['rsport']}</td><td class=tdleft><a href='".makeHref(array('page'=>'ipv4rspool', 'pool_id'=>$rsinfo['rspool_id']))."'>";
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
			echo "<tr><td>${rule['proto']}</td><td>${rule['localip']}:${rule['localport']}</td><td>${rule['remoteip']}:${rule['remoteport']}</td><td>${rule['description']}</td></tr>";
		echo "</table>";
		finishPortlet();
	}

	if (count ($address['inpf']))
	{
		startPortlet ('arriving NAT rules');
		echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>\n";
		echo "<tr><th>proto</th><th>from</th><th>to</th><th>comment</th></tr>\n";
		foreach ($address['inpf'] as $rule)
			echo "<tr><td>${rule['proto']}</td><td>${rule['localip']}:${rule['localport']}</td><td>${rule['remoteip']}:${rule['remoteport']}</td><td>${rule['description']}</td></tr>";
		echo "</table>";
		finishPortlet();
	}

	echo "</td></tr>";
	echo "</table>\n";
}

function renderIPv4AddressProperties ($dottedquad)
{
	$address = getIPv4Address ($dottedquad);
	echo "<center><h1>$dottedquad</h1></center>\n";

	startPortlet ('update');
	echo "<table border=0 cellpadding=10 cellpadding=1 align='center'>\n";
	printOpFormIntro ('editAddress');
	echo "<tr><td class='tdright'>Name:</td><td class='tdleft'><input type=text name=name size=20 value='${address['name']}'></tr>";
	echo "<td class='tdright'>Reserved:</td><td class='tdleft'><input type=checkbox name=reserved size=20 ";
	echo ($address['reserved']=='yes') ? 'checked' : '';
	echo "></tr><tr><td class=tdleft>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></form><td class=tdright>";
	if (empty ($address['name']) and $address['reserved'] == 'no')
		printImageHREF ('CLEAR gray');
	else
	{
		printOpFormIntro ('editAddress', array ('name' => '', 'reserved' => ''));
		printImageHREF ('CLEAR', 'Release', TRUE);
		echo "</form>";
	}
	echo "</td></tr></table>\n";
	finishPortlet();
}

function renderIPv4AddressAllocations ($dottedquad)
{
	function printNewItemTR ()
	{
		global $aat;
		printOpFormIntro ('addIPv4Allocation');
		echo "<tr><td>";
		printImageHREF ('add', 'allocate', TRUE);
		echo "</td><td>";
		printSelect (getNarrowObjectList ('IPV4OBJ_LISTSRC'), 'object_id', NULL, 100);
		echo "</td><td><input type=text tabindex=101 name=bond_name size=10></td><td>";
		printSelect ($aat, 'bond_type', NULL, 102);
		echo "</td><td>";
		printImageHREF ('add', 'allocate', TRUE, 103);
		echo "</td></form></tr>";
	}
	global $aat;

	$address = getIPv4Address ($dottedquad);

	echo "<center><h1>${dottedquad}</h1></center>\n";
	echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>\n";
	echo "<tr><th>&nbsp;</th><th>object</th><th>OS interface</th><th>allocation type</th><th>&nbsp;</th></tr>\n";

	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	if (isset ($address['class']))
	{
		$class = $address['class'];
		if ($address['reserved'] == 'yes')
			echo "<tr class='${class}'><td colspan=3>&nbsp;</td><td class=tdleft><strong>RESERVED</strong></td><td>&nbsp;</td></tr>";
		foreach ($address['allocs'] as $bond)
		{
			echo "<tr class='$class'>";
			printOpFormIntro ('updIPv4Allocation', array ('object_id' => $bond['object_id']));
			echo "<td><a href='".makeHrefProcess(array('op'=>'delIPv4Allocation', 'ip'=>$dottedquad, 'object_id'=>$bond['object_id']))."'>";
			printImageHREF ('delete', 'Unallocate address');
			echo "</a></td>";
			echo "<td><a href='".makeHref(array('page'=>'object', 'object_id'=>$bond['object_id'], 'hl_ipv4_addr'=>$dottedquad))."'>${bond['object_name']}</td>";
			echo "<td><input type='text' name='bond_name' value='${bond['name']}' size=10></td><td>";
			printSelect ($aat, 'bond_type', $bond['type']);
			echo "</td><td>";
			printImageHREF ('save', 'Save changes', TRUE);
			echo "</td></form></tr>\n";
		}
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo "</table><br><br>";
}

function renderNATv4ForObject ($object_id)
{
	function printNewItemTR ($alloclist)
	{
		printOpFormIntro ('addNATv4Rule');
		echo "<tr align='center'><td>";
		printImageHREF ('add', 'Add new NAT rule', TRUE);
		echo '</td><td>';
		printSelect (array ('TCP' => 'TCP', 'UDP' => 'UDP'), 'proto');
		echo "<select name='localip' tabindex=1>";

		foreach ($alloclist as $dottedquad => $alloc)
		{
			$name = empty ($alloc['addrinfo']['name']) ? '' : (' (' . niftyString ($alloc['addrinfo']['name']) . ')');
			$osif = empty ($alloc['osif']) ? '' : ($alloc['osif'] . ': ');
			echo "<option value='${dottedquad}'>${osif}${dottedquad}${name}</option>";
		}

		echo "</select>:<input type='text' name='localport' size='4' tabindex=2></td>";
		echo "<td><input type='text' name='remoteip' id='remoteip' size='10' tabindex=3>";
		echo "<a href='javascript:;' onclick='window.open(\"" . makeHrefForHelper ('inet4list');
		echo "\", \"findobjectip\", \"height=700, width=400, location=no, menubar=no, resizable=yes, scrollbars=no, status=no, titlebar=no, toolbar=no\");'>";
		printImageHREF ('find', 'Find object');
		echo "</a>";
		echo ":<input type='text' name='remoteport' size='4' tabindex=4></td><td></td>";
		echo "<td colspan=1><input type='text' name='description' size='20' tabindex=5></td><td>";
		printImageHREF ('add', 'Add new NAT rule', TRUE, 6);
		echo "</td></tr></form>";
	}
	
	$info = getObjectInfo ($object_id);
	$forwards = getNATv4ForObject ($object_id);
	$alloclist = getObjectIPv4Allocations ($object_id);
	echo "<center><h2>locally performed NAT</h2></center>";

	echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>\n";
	echo "<tr><th></th><th>Match endpoint</th><th>Translate to</th><th>Target object</th><th>Comment</th><th>&nbsp;</th></tr>\n";

	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($alloclist);
	foreach ($forwards['out'] as $pf)
	{
		$class = 'trerror';
		$osif = '';
		if (isset ($alloclist [$pf['localip']]))
		{
			$class = $alloclist [$pf['localip']]['addrinfo']['class'];
			$osif = $alloclist [$pf['localip']]['osif'] . ': ';
		}

		echo "<tr class='$class'>";
		echo "<td><a href='".
			makeHrefProcess(array(
				'op'=>'delNATv4Rule', 
				'localip'=>$pf['localip'],
				'localport'=>$pf['localport'],
				'remoteip'=>$pf['remoteip'],
				'remoteport'=>$pf['remoteport'],
				'proto'=>$pf['proto'], 
				'object_id'=>$object_id)).
		"'>";
		printImageHREF ('delete', 'Delete NAT rule');
		echo "</a></td>";
		echo "<td>${pf['proto']}/${osif}<a href='".makeHref(array('page'=>'ipaddress', 'tab'=>'default', 'ip'=>$pf['localip']))."'>${pf['localip']}</a>:${pf['localport']}";
		if (!empty ($pf['local_addr_name']))
			echo ' (' . $pf['local_addr_name'] . ')';
		echo "</td>";
		echo "<td><a href='".makeHref(array('page'=>'ipaddress', 'tab'=>'default', 'ip'=>$pf['remoteip']))."'>${pf['remoteip']}</a>:${pf['remoteport']}</td>";

		$address = getIPv4Address ($pf['remoteip']);

		echo "<td class='description'>";
		if (count ($address['allocs']))
			foreach ($address['allocs'] as $bond)
				echo "<a href='".makeHref(array('page'=>'object', 'tab'=>'default', 'object_id'=>$bond['object_id']))."'>${bond['object_name']}(${bond['name']})</a> ";
		elseif (!empty ($pf['remote_addr_name']))
			echo '(' . $pf['remote_addr_name'] . ')';
		printOpFormIntro
		(
			'updNATv4Rule',
			array
			(
				'localip' => $pf['localip'],
				'localport' => $pf['localport'],
				'remoteip' => $pf['remoteip'],
				'remoteport' => $pf['remoteport'],
				'proto' => $pf['proto']
			)
		);
		echo "</td><td class='description'>";
		echo "<input type='text' name='description' value='${pf['description']}'></td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</td></form></tr>";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ($alloclist);

	echo "</table><br><br>";

	echo "<center><h2>arriving NAT connections</h2></center>";
	echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>\n";
	echo "<tr><th></th><th>Source</th><th>Source objects</th><th>Target</th><th>Description</th></tr>\n";

	foreach ($forwards['in'] as $pf)
	{
		echo "<tr><td><a href='".
			makeHrefProcess(array(
				'op'=>'delNATv4Rule',
				'localip'=>$pf['localip'],
				'localport'=>$pf['localport'],
				'remoteip'=>$pf['remoteip'],
				'remoteport'=>$pf['remoteport'],
				'proto'=>$pf['proto'],
				'object_id'=>$pf['object_id']
				)).
		"'>";
		printImageHREF ('delete', 'Delete NAT rule');
		echo "</a></td>";
		echo "<td>${pf['proto']}/<a href='".makeHref(array('page'=>'ipaddress', 'tab'=>'default', 'ip'=>$pf['localip']))."'>${pf['localip']}</a>:${pf['localport']}</td>";
		echo "<td class='description'><a href='".makeHref(array('page'=>'object', 'tab'=>'default', 'object_id'=>$pf['object_id']))."'>${pf['object_name']}</a>";
		echo "</td><td><a href='".makeHref(array('page'=>'ipaddress', 'tab'=>'default', 'ip'=>$pf['remoteip']))."'>${pf['remoteip']}</a>:${pf['remoteport']}</td>";
		echo "<td class='description'>${pf['description']}</td></tr>";
	}

	echo "</table><br><br>";
}

function renderAddMultipleObjectsForm ()
{
	$typelist = getObjectTypeList();
	$typelist[0] = 'select type...';
	$max = getConfigVar ('MASSCOUNT');
	$tabindex = 100;

	startPortlet ('Distinct types, same tags');
	printOpFormIntro ('addObjects');
	echo '<table border=0 align=center>';
	echo "<tr><th>Object type</th><th>Common name</th><th>Visible label</th>";
	echo "<th>Asset tag</th><th>Barcode</th><th>Tags</th></tr>\n";
	for ($i = 0; $i < $max; $i++)
	{
		echo '<tr><td>';
		// Don't employ DEFAULT_OBJECT_TYPE to avoid creating ghost records for pre-selected empty rows.
		printSelect ($typelist, "${i}_object_type_id", 0, $tabindex);
		echo '</td>';
		echo "<td><input type=text size=30 name=${i}_object_name tabindex=${tabindex}></td>";
		echo "<td><input type=text size=30 name=${i}_object_label tabindex=${tabindex}></td>";
		echo "<td><input type=text size=20 name=${i}_object_asset_no tabindex=${tabindex}></td>";
		echo "<td><input type=text size=10 name=${i}_object_barcode tabindex=${tabindex}></td>";
		if ($i == 0)
		{
			echo "<td valign=top rowspan=${max}>";
			renderNewEntityTags();
			echo "</td>\n";
		}
		echo "</tr>\n";
		$tabindex++;
	}
	echo "<tr><td class=submit colspan=5><input type=submit name=got_fast_data value='Go!'></td></tr>\n";
	echo "</form></table>\n";
	finishPortlet();

	startPortlet ('Same type, same tags');
	printOpFormIntro ('addLotOfObjects');
	echo "<table border=0 align=center><tr><th>names</th><th>type</th></tr>";
	echo "<tr><td rowspan=3><textarea name=namelist cols=40 rows=25>\n";
	echo "</textarea></td><td valign=top>";
	printSelect ($typelist, "global_type_id", getConfigVar ('DEFAULT_OBJECT_TYPE'));
	echo "</td></tr>";
	echo "<tr><th>Tags</th></tr>";
	echo "<tr><td valign=top>";
	renderNewEntityTags();
	echo "</td></tr>";
	echo "<tr><td colspan=2><input type=submit name=got_very_fast_data value='Go!'></td></tr></table>\n";
	echo "</form>\n";
	finishPortlet();
}

function printGreeting ()
{
	global $root, $remote_username, $remote_displayname;
	echo "Hello, <a href='${root}?page=myaccount&tab=default'>${remote_displayname}</a>. This is RackTables " .
		CODE_VERSION .
		". Click <a href='${root}?logout'>here</a> to logout.";
}

function renderSearchResults ()
{
	global $root;
	$terms = trim ($_REQUEST['q']);
	if (empty ($terms))
	{
		showError ('Search string cannot be empty.', __FUNCTION__);
		return;
	}
	if (!permitted ('depot', 'default'))
	{
		showError ('You are not authorized for viewing information about objects.', __FUNCTION__);
		return;
	}
	$nhits = 0;
	// If we search for L2 address, we can either find one or find none.
	if
	(
		preg_match (RE_L2_IFCFG, $terms) or
		preg_match (RE_L2_SOLID, $terms) or
		preg_match (RE_L2_CISCO, $terms) or
		preg_match (RE_L2_IPCFG, $terms) or
		// Foundry STP bridge ID: bridge priotity + port MAC address. Cut off first 4 chars and look for MAC address.
		preg_match (RE_L2_FDRYSTP, $terms)
	)
	// Search for L2 address.
	{
		$terms = str_replace ('.', '', $terms);
		$terms = str_replace (':', '', $terms);
		$terms = str_replace ('-', '', $terms);
		$terms = substr ($terms, -12);
		$result = searchByl2address ($terms);
		if ($result !== NULL)
		{
			$nhits++;
			$lasthit = 'port';
			$summary['port'][] = $result;
		}
	}
	elseif (preg_match (RE_IP4_ADDR, $terms))
	// Search for IPv4 address.
	{
		if (NULL !== getIPv4AddressNetworkId ($terms))
		{
			$nhits++;
			$lasthit = 'ipv4addressbydq';
			$summary['ipv4addressbydq'][] = $terms;
		}
	}
	elseif (preg_match (RE_IP4_NET, $terms))
	// Search for IPv4 network
	{
		list ($base, $len) = explode ('/', $terms);
		if (NULL !== ($tmp = getIPv4AddressNetworkId ($base, $len + 1)))
		{
			$nhits++;
			$lasthit = 'ipv4network';
			$summary['ipv4network'][] = spotEntity ('ipv4net', $tmp);
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
			$lasthit = 'ipv4addressbydescr';
			$summary['ipv4addressbydescr'] = $tmp;
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
		$tmp = getFileSearchResult ($terms);
		if (count ($tmp))
		{
			$nhits += count ($tmp);
			$lasthit = 'file';
			$summary['file'] = $tmp;
		}
		$tmp = getRackSearchResult ($terms);
		if (count ($tmp))
		{
			$nhits += count ($tmp);
			$lasthit = 'rack';
			$summary['rack'] = $tmp;
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
			case 'ipv4addressbydq':
				$parentnet = getIPv4AddressNetworkId ($record);
				if ($parentnet !== NULL)
					echo "<script language='Javascript'>document.location='${root}?page=ipv4net&tab=default&id=${parentnet}&hl_ipv4_addr=${record}';//</script>";
				else
					echo "<script language='Javascript'>document.location='${root}?page=ipaddress&ip=${record}';//</script>";
				break;
			case 'ipv4addressbydescr':
				$parentnet = getIPv4AddressNetworkId ($record['ip']);
				if ($parentnet !== NULL)
					echo "<script language='Javascript'>document.location='${root}?page=ipv4net&tab=default&id=${parentnet}&hl_ipv4_addr=${record['ip']}';//</script>";
				else
					echo "<script language='Javascript'>document.location='${root}?page=ipaddress&ip=${record['ip']}';//</script>";
				break;
			case 'ipv4network':
				echo "<script language='Javascript'>document.location='${root}?page=ipv4net";
				echo "&id=${record['id']}";
				echo "';//</script>";
				break;
			case 'object':
				echo "<script language='Javascript'>document.location='${root}?page=object&object_id=${record['id']}';//</script>";
				break;
			case 'ipv4rspool':
				echo "<script language='Javascript'>document.location='${root}?page=ipv4rspool&pool_id=${record['pool_id']}';//</script>";
				break;
			case 'ipv4vs':
				echo "<script language='Javascript'>document.location='${root}?page=ipv4vs&vs_id=${record['id']}';//</script>";
				break;
			case 'user':
				echo "<script language='Javascript'>document.location='${root}?page=user&user_id=${record['user_id']}';//</script>";
				break;
			case 'file':
				echo "<script language='Javascript'>document.location='${root}?page=file&file_id=${record['id']}';//</script>";
				break;
			case 'rack':
				echo "<script language='Javascript'>document.location='${root}?page=rack&rack_id=${record['id']}';//</script>";
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
					startPortlet ("<a href='${root}?page=depot'>Objects</a>");
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					echo '<tr><th>Common name</th><th>Visible label</th><th>Asset tag</th><th>Barcode</th></tr>';
					foreach ($what as $obj)
					{
						$tags = loadEntityTags ('object', $obj['id']);
						echo "<tr class=row_${order} valign=top><td class=tdleft><a href=\"${root}?page=object&object_id=${obj['id']}\">${obj['dname']}</a>";
						if (count ($tags))
							echo '<br><small>' . serializeTags ($tags) . '</small>';
						echo "</td><td>${obj['label']}</td>";
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
					foreach ($what as $cell)
					{
						echo "<tr class=row_${order} valign=top><td>";
						renderCell ($cell);
						echo "</td></tr>\n";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
				case 'ipv4addressbydescr':
					startPortlet ('IPv4 addresses');
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					// FIXME: address, parent network, routers (if extended view is enabled)
					echo '<tr><th>Address</th><th>Description</th></tr>';
					foreach ($what as $addr)
					{
						echo "<tr class=row_${order}><td class=tdleft>";
						$parentnet = getIPv4AddressNetworkId ($addr['ip']);
						if ($parentnet !== NULL)
							echo "<a href='${root}?page=ipv4net&tab=default&id=${parentnet}&hl_ipv4_addr=${addr['ip']}'>${addr['ip']}</a></td>";
						else
							echo "<a href='${root}?page=ipaddress&ip=${addr['ip']}'>${addr['ip']}</a></td>";
						echo "<td class=tdleft>${addr['name']}</td></tr>";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
				case 'ipv4rspool':
					startPortlet ("<a href='${root}?page=ipv4rsplist'>RS pools</a>");
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					foreach ($what as $cell)
					{
						echo "<tr class=row_${order}><td class=tdleft>";
						renderCell ($cell);
						echo "</td></tr>";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
				case 'ipv4vs':
					startPortlet ("<a href='${root}?page=ipv4vslist'>Virtual services</a>");
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					foreach ($what as $cell)
					{
						echo "<tr class=row_${order}><td class=tdleft>";
						renderCell ($cell);
						echo "</td></tr>";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
				case 'user':
					startPortlet ("<a href='${root}?page=userlist'>Users</a>");
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					foreach ($what as $item)
					{
						echo "<tr class=row_${order}><td class=tdleft>";
						renderCell ($item);
						echo "</td></tr>";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
				case 'file':
					startPortlet ("<a href='${root}?page=files'>Files</a>");
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					foreach ($what as $cell)
					{
						echo "<tr class=row_${order}><td class=tdleft>";
						renderCell ($cell);
						echo "</td></tr>";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
				case 'rack':
					startPortlet ("<a href='${root}?page=rackspace'>Racks</a>");
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					foreach ($what as $cell)
					{
						echo "<tr class=row_${order}><td class=tdleft>";
						renderCell ($cell);
						echo "</td></tr>";
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
// produced by amplifyCell(), the second is the value used for the 'unckecked' state
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
		echo "<tr><th><a href='javascript:;' onclick=\"toggleRowOfAtoms('${rack_id}','${unit_no}')\">${unit_no}</a></th>";
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			$name = "atom_${rack_id}_${unit_no}_${locidx}";
			$state = $data[$unit_no][$locidx]['state'];
			echo "<td class=state_${state}";
			if (isset ($data[$unit_no][$locidx]['hl']))
				echo $data[$unit_no][$locidx]['hl'];
			echo ">";
			if (!($data[$unit_no][$locidx]['enabled'] === TRUE))
				echo "<input type=checkbox id=${name} disabled>";
			else
				echo "<input type=checkbox" . $data[$unit_no][$locidx]['checked'] . " name=${name} id=${name}>";
			echo '</td>';
		}
		echo "</tr>\n";
	}
}

function renderCellList ($realm = NULL, $title = 'items', $do_amplify = FALSE)
{
	if ($realm === NULL)
	{
		global $pageno;
		$realm = $pageno;
	}
	global $nextorder;
	$order = 'odd';
	$cellfilter = getCellFilter();
	$celllist = filterCellList (listCells ($realm), $cellfilter['expression']);
	if ($do_amplify)
		array_walk ($celllist, 'amplifyCell');
	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";
	startPortlet ($title . ' (' . count ($celllist) . ')');
	echo "<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	foreach ($celllist as $cell)
	{
		echo "<tr class=row_${order}><td>";
		renderCell ($cell);
		echo "</td></tr>\n";
		$order = $nextorder[$order];
	}
	echo '</table>';
	finishPortlet();
	echo '</td><td class=pcright>';
	renderCellFilterPortlet ($cellfilter, $realm);
	echo "</td></tr></table>\n";
}

function renderUserList ()
{
	renderCellList ('user', 'User accounts');
}

function renderUserListEditor ()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('createUser');
		echo "<tr><td><input type=text size=16 name=username tabindex=100></td>\n";
		echo "<td><input type=text size=24 name=realname tabindex=101></td>";
		echo "<td><input type=password size=64 name=password tabindex=102></td><td>";
		printImageHREF ('create', 'Add new account', TRUE, 103);
		echo "</td></tr></form>";
	}
	$accounts = listCells ('user');
	startPortlet ('User accounts (' . count ($accounts) . ')');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>Username</th><th>Real name</th><th>Password</th><th>&nbsp;</th></tr>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach ($accounts as $account)
	{
		printOpFormIntro ('updateUser', array ('user_id' => $account['user_id']));
		echo "<tr><td><input type=text name=username value='${account['user_name']}' size=16></td>";
		echo "<td><input type=text name=realname value='${account['user_realname']}' size=24></td>";
		echo "<td><input type=password name=password value='${account['user_password_hash']}' size=64></td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</td></form></tr>\n";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo "</table><br>\n";
	finishPortlet();
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
	global $nextorder;
	startPortlet ("Port compatibility map");
	$ptlist = getPortTypes();
	$pclist = getPortCompat();
	$pctable = buildPortCompatMatrixFromList ($ptlist, $pclist);
	if ($editable)
		printOpFormIntro ('save');
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
		printImageHREF ('SAVE', 'Save changes', TRUE);
		echo "</form>";
	}
	finishPortlet();
}

// Find direct sub-pages and dump as a list.
// FIXME: assume all config kids to have static titles at the moment,
// but use some proper abstract function later.
function renderConfigMainpage ()
{
	global $pageno, $page, $root;
	echo '<ul>';
	foreach ($page as $cpageno => $cpage)
		if (isset ($cpage['parent']) and $cpage['parent'] == $pageno)
			echo "<li><a href='${root}?page=${cpageno}'>" . $cpage['title'] . "</li>\n";
	echo '</ul>';
}

function renderRackPage ($rack_id)
{
	if (NULL == ($rackData = spotEntity ('rack', $rack_id)))
	{
		showError ('Rack not found', __FUNCTION__);
		return;
	}
	amplifyCell ($rackData);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0><tr>";

	// Left column with information.
	echo "<td class=pcleft>";
	renderRackInfoPortlet ($rackData);
	renderFilesPortlet ('rack', $rack_id);
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
	echo '<ul>';
	foreach (getChapterList() as $chapter_no => $chapter)
	{
		$wc = $chapter['wordc'];
		echo "<li><a href='".makeHref(array('page'=>'chapter', 'chapter_no'=>$chapter_no))."'>${chapter['name']}</a>";
		echo " (${wc} records)</li>";
	}
	echo '</ul>';
}

function renderChapter ($tgt_chapter_no)
{
	global $nextorder;
	foreach (getDict (TRUE) as $chapter_no => $chapter)
	{
		if ($chapter_no != $tgt_chapter_no)
			continue;
		$wc = count ($chapter['word']);
		if (!$wc)
		{
			echo "<center><h2>(no records)</h2></center>";
			break;
		}
		echo "<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
		echo "<tr><th colspan=3>${wc} record(s)</th></tr>\n";
		echo "<tr><th>Origin</th><th>Refcnt</th><th>Word</th></tr>\n";
		$order = 'odd';
		foreach ($chapter['word'] as $key => $value)
		{
			echo "<tr class=row_${order}><td>";
			printImageHREF (($key <= MAX_DICT_KEY) ? 'computer' : 'favorite');
			echo '</td><td>';
			if ($chapter['refcnt'][$key])
				echo $chapter['refcnt'][$key];
			echo "</td><td><div title='key=${key}'>${value}</div></td></tr>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n<br>";
		break;
	}
}

function renderChapterEditor ($tgt_chapter_no)
{
	global $nextorder;
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr><td>&nbsp;</td><td>';
		printImageHREF ('add', 'Add new', TRUE);
		echo "</td>";
		echo "<td class=tdleft><input type=text name=dict_value size=32 tabindex=100></td><td>";
		printImageHREF ('add', 'Add new', TRUE, 101);
		echo '</td></tr></form>';
	}
	$dict = getDict();
	echo "<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	foreach ($dict as $chapter_no => $chapter)
	{
		if ($chapter_no != $tgt_chapter_no)
			continue;
		$order = 'odd';
		echo "<tr><th>Origin</th><th>&nbsp;</th><th>Word</th><th>&nbsp;</th></tr>\n";
		if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
			printNewItemTR();
		foreach ($chapter['word'] as $key => $value)
		{
			echo "<tr class=row_${order}><td>";
			// Show plain row for stock records, render a form for user's ones.
			if ($key <= MAX_DICT_KEY)
			{
				printImageHREF ('computer');
				echo "</td><td>&nbsp;</td><td>${value}</td><td>&nbsp;</td></tr>";
			}
			else
			{
				printOpFormIntro ('upd', array ('dict_key' => $key));
				printImageHREF ('favorite');
				echo "</td><td>";
				// Prevent deleting words currently used somewhere.
				if ($chapter['refcnt'][$key])
					printImageHREF ('nodelete', 'referenced ' . $chapter['refcnt'][$key] . ' time(s)');
				else
				{
					echo "<a href='".makeHrefProcess(array('op'=>'del', 'chapter_no'=>$chapter_no, 'dict_key'=>$key))."'>";
					printImageHREF ('delete', 'Delete word');
					echo "</a>";
				}
				echo '</td>';
				echo "<td class=tdright><input type=text name=dict_value size=64 value='${value}'></td><td>";
				printImageHREF ('save', 'Save changes', TRUE);
				echo "</td></tr></form>\n";
			}
			$order = $nextorder[$order];
		} // foreach ($chapter['word']
		if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
			printNewItemTR();
		echo "</table>\n";
		break;
	} // foreach ($dict
}

// We don't allow to rename/delete a sticky chapter and we don't allow
// to delete a non-empty chapter.
function renderChaptersEditor ()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr><td>';
		printImageHREF ('create', 'Add new', TRUE);
		echo "</td><td><input type=text name=chapter_name tabindex=100></td><td>&nbsp;</td><td>";
		printImageHREF ('create', 'Add new', TRUE, 101);
		echo '</td></tr></form>';
	}
	$dict = getChapterList();
	foreach (array_keys ($dict) as $chapter_no)
		$dict[$chapter_no]['mapped'] = FALSE;
	foreach (getAttrMap() as $attrinfo)
		if ($attrinfo['type'] == 'dict')
			foreach ($attrinfo['application'] as $app)
				$dict[$app['chapter_no']]['mapped'] = TRUE;
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo '<tr><th>&nbsp;</th><th>Chapter name</th><th>Words</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach ($dict as $chapter_id => $chapter)
	{
		$wordcount = $chapter['wordc'];
		$sticky = $chapter['sticky'] == 'yes';
		printOpFormIntro ('upd', array ('chapter_no' => $chapter_id));
		echo '<tr>';
		echo '<td>';
		if ($sticky)
			printImageHREF ('nodestroy', 'system chapter');
		elseif ($wordcount > 0)
			printImageHREF ('nodestroy', 'contains ' . $wordcount . ' word(s)');
		elseif ($chapter['mapped'])
			printImageHREF ('nodestroy', 'used in attribute map');
		else
		{
			echo "<a href='".makeHrefProcess(array('op'=>'del', 'chapter_no'=>$chapter_id))."'>";
			printImageHREF ('destroy', 'Remove chapter');
			echo "</a>";
		}
		echo '</td>';
		echo "<td><input type=text name=chapter_name value='${chapter['name']}'" . ($sticky ? ' disabled' : '') . "></td>";
		echo "<td class=tdleft>${wordcount}</td><td>";
		if ($sticky)
			echo '&nbsp;';
		else
			printImageHREF ('save', 'Save changes', TRUE);
		echo '</td></tr>';
		echo '</form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo "</table>\n";
}

function renderAttributes ()
{
	global $nextorder, $attrtypes;
	$attrMap = getAttrMap();
	startPortlet ('Optional attributes');
	echo "<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	echo "<tr><th class=tdleft>Attribute name</th><th class=tdleft>Attribute type</th><th class=tdleft>Applies to</th></tr>";
	$order = 'odd';
	foreach ($attrMap as $attr)
	{
		echo "<tr class=row_${order}>";
		echo "<td class=tdleft>${attr['name']}</td>";
		echo "<td class=tdleft>" . $attrtypes[$attr['type']] . "</td>";
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
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr><td>';
		printImageHREF ('add', 'Create attribute', TRUE);
		echo "</td><td><input type=text tabindex=100 name=attr_name></td><td>";
		global $attrtypes;
		printSelect ($attrtypes, 'attr_type', NULL, 101);
		echo '</td><td>';
		printImageHREF ('add', 'Create attribute', TRUE, 102);
		echo '</td></tr></form>';
	}
	$attrMap = getAttrMap();
	startPortlet ('Optional attributes');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo '<tr><th>&nbsp;</th><th>Name</th><th>Type</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach ($attrMap as $attr)
	{
		printOpFormIntro ('upd', array ('attr_id' => $attr['id']));
		echo '<tr>';
		echo "<td><a href='".makeHrefProcess(array('op'=>'del', 'attr_id'=>$attr['id']))."'>";
		printImageHREF ('delete', 'Remove attribute');
		echo '</a></td>';
		echo "<td><input type=text name=attr_name value='${attr['name']}'></td>";
		echo "<td class=tdleft>${attr['type']}</td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo '</td></tr>';
		echo '</form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo "</table>\n";
	finishPortlet();
}

function renderEditAttrMapForm ()
{
	function printNewItemTR ($attrMap)
	{
		printOpFormIntro ('add');
		echo '<tr><td>';
		printImageHREF ('add', '', TRUE);
		echo "</td><td><select name=attr_id tabindex=100>";
		$shortType['uint'] = 'U';
		$shortType['float'] = 'F';
		$shortType['string'] = 'S';
		$shortType['dict'] = 'D';
		foreach ($attrMap as $attr)
			echo "<option value=${attr['id']}>[" . $shortType[$attr['type']] . "] ${attr['name']}</option>";
		echo "</select></td>";
		echo '<td>';
		printSelect (getObjectTypeList(), 'objtype_id', NULL, 101);
		echo '</td>';
		echo '<td><select name=chapter_no tabindex=102>';
		foreach (getChapterList() as $chapter)
			if ($chapter['sticky'] != 'yes')
				echo "<option value='${chapter['id']}'>${chapter['name']}</option>";
		echo '</select></td><td>';
		printImageHREF ('add', '', TRUE, 103);
		echo '</td></tr>';
		echo '</form>';
	}
	$attrMap = getAttrMap();
	startPortlet ('Attribute map');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo '<tr><th>&nbsp;</th><th>Attribute name</th><th>Object type</th><th>Dictionary chapter</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($attrMap);
	foreach ($attrMap as $attr)
	{
		if (count ($attr['application']) == 0)
			continue;
		foreach ($attr['application'] as $app)
		{
			echo '<tr>';
			echo '<td>';
			echo "<a href='".makeHrefProcess(array('op'=>'del', 'attr_id'=>$attr['id'], 'objtype_id'=>$app['objtype_id']))."'>";
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
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ($attrMap);
	echo "</table>\n";
	finishPortlet();
}

function printImageHREF ($tag, $title = '', $do_input = FALSE, $tabindex = 0)
{
	echo getImageHREF ($tag, $title, $do_input, $tabindex);
}

function getImageHREF ($tag, $title = '', $do_input = FALSE, $tabindex = 0)
{
	global $root, $image;
	if (!isset ($image[$tag]))
		$tag = 'error';
	$img = $image[$tag];
	if ($do_input == TRUE)
		return
			"<input type=image name=submit class=icon " .
			"src='${root}${img['path']}' " .
			"border=0 " .
			($tabindex ? "tabindex=${tabindex}" : '') .
			(empty ($title) ? '' : " title='${title}'") . // JT: Add title to input hrefs too
			">";
	else
		return
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
			'title' => 'Tags top-50',
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
			'func' => 'getRackCodeStats'
		),
		array
		(
			'title' => 'Warnings',
			'type' => 'messages',
			'func' => 'getRackCodeWarnings'
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
		array
		(
			'title' => 'Lost addresses',
			'type' => 'custom',
			'func' => 'getLostIPv4Addresses'
		),
	);
	renderReports ($tmp);
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
				foreach ($item['func'] () as $header => $data)
					echo "<tr><td class=tdright>${header}:</td><td class=tdleft>${data}</td></tr>\n";
				break;
			case 'messages':
				foreach ($item['func'] () as $msg)
					echo "<tr class='msg_${msg['class']}'><td class=tdright>${msg['header']}:</td><td class=tdleft>${msg['text']}</td></tr>\n";
				break;
			case 'custom':
				echo "<tr><td colspan=2>";
				$item['func'] ();
				echo "</td></tr>\n";
				break;
			default:
				showError ('Internal data error', __FUNCTION__);
		}
		echo "<tr><td colspan=2><hr></td></tr>\n";
	}
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
	global $configCache;
	startPortlet ('Current configuration');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable width='50%'>\n";
	echo "<tr><th class=tdleft>Option</th>";
	echo "<th class=tdleft>Value</th></tr>";
	printOpFormIntro ('upd');

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
	echo "<tr><td colspan=2>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>";
	echo "</form>";
	finishPortlet();
}

// This function queries the gateway about current VLAN configuration and
// renders a form suitable for submit. Ah, and it does submit processing as well.
function renderVLANMembership ($object_id)
{
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
	printOpFormIntro ('setPortVLAN');
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

function renderSNMPPortFinder ($object_id)
{
	printOpFormIntro ('querySNMPData');
	if (!extension_loaded ('snmp'))
	{
		echo "<div class=msg_error>The PHP SNMP extension is not loaded.  Cannot continue.</div>";
	}
	else
	{
		echo "<p align=center>
This object has no ports listed, that's why you see this form. If you supply a SNMP community,
I can try to automatically harvest the data. As soon as at least one port is added,
this tab will not be seen any more. Good luck.<br>\n";
		echo "<input type=text name=community value='public'>\n";
		echo "<input type=submit name='do_scan' value='Go!'> \n";
		echo "</form></p>\n";
	}
}

function renderUIResetForm()
{
	printOpFormIntro ('go');
	echo "This button will reset user interface configuration to its defaults (except organization name): ";
	echo "<input type=submit value='proceed'>";
	echo "</form>";
}

function renderLVSConfig ($object_id)
{
	echo '<br>';
	printOpFormIntro ('submitSLBConfig');
	echo "<center><input type=submit value='Submit for activation'></center>";
	echo "</form>";
	echo '<pre>';
	echo buildLVSConfig ($object_id);
	echo '</pre>';
}

function renderVirtualService ($vsid)
{
	global $nextorder;
	$vsinfo = spotEntity ('ipv4vs', $vsid);
	amplifyCell ($vsinfo);
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
	echo "<tr><th width='50%' class=tdright>Virtual IP address:</th><td class=tdleft><a href='".makeHref(array('page'=>'ipaddress', 'tab'=>'default', 'ip'=>$vsinfo['vip']))."'>${vsinfo['vip']}</a></td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Virtual port:</th><td class=tdleft>${vsinfo['vport']}</td></tr>\n";
	printTagTRs (makeHref(array('page'=>'ipv4vslist', 'tab'=>'default'))."&");
	if (!empty ($vsinfo['vsconfig']))
	{
		echo "<tr><th class=slbconf>VS configuration:</th><td>&nbsp;</td></tr>";
		echo "<tr><td colspan=2 class='dashed slbconf'>${vsinfo['vsconfig']}</td></tr>\n";
	}
	if (!empty ($vsinfo['rsconfig']))
	{
		echo "<tr><th class=slbconf>RS configuration:</th><td class=tdleft>&nbsp;</td></tr>\n";
		echo "<tr><td colspan=2 class='dashed slbconf'>${vsinfo['rsconfig']}</td></tr>\n";
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
		echo "<tr class=row_${order} valign=top><td class=tdleft>";
		// Pool info
		echo '<table width=100%>';
		echo "<tr><td colspan=2>";
		renderCell (spotEntity ('ipv4rspool', $pool_id));
		echo "</td></tr>";
		if (!empty ($poolInfo['vsconfig']))
			echo "<tr><th>VS config</th><td class='dashed slbconf'>${poolInfo['vsconfig']}</td></tr>";
		if (!empty ($poolInfo['rsconfig']))
			echo "<tr><th>RS config</th><td class='dashed slbconf'>${poolInfo['rsconfig']}</td></tr>";
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
				echo "<tr><td colspan=2>";
				renderLBCell ($object_id);
				echo '</td></tr>';
				if (!empty ($lbInfo['vsconfig']))
					echo "<tr><th>VS config</th><td class='dashed slbconf'>${lbInfo['vsconfig']}</td></tr>";
				if (!empty ($lbInfo['rsconfig']))
					echo "<tr><th>RS config</th><td class='dashed slbconf'>${lbInfo['rsconfig']}</td></tr>";
			}
			echo '</table>';
		}
		echo "</td></tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table>\n";
	finishPortlet ();
	echo '</td></tr><tr><td colspan=2>';
	renderFilesPortlet ('ipv4vs', $vsid);
	echo '</tr><table>';
}

function renderProgressBar ($percentage = 0, $theme = '')
{
	global $root;
	$done = ((int) ($percentage * 100));
	echo "<img width=100 height=10 border=0 title='${done}%' src='${root}render_image.php?img=progressbar&done=${done}";
	echo (empty ($theme) ? '' : "&theme=${theme}") . "'>";
}

function renderRSPoolServerForm ($pool_id)
{
	global $nextorder;
	$poolInfo = spotEntity ('ipv4rspool', $pool_id);
	amplifyCell ($poolInfo);

	if (($rsc = count ($poolInfo['rslist'])))
	{
		startPortlet ("Manage existing (${rsc})");
		echo "<table cellspacing=0 cellpadding=5 align=center class=cooltable>\n";
		echo "<tr><th>&nbsp;</th><th>Address</th><th>Port</th><th>configuration</th><th>&nbsp;</th></tr>\n";
		$order = 'odd';
		foreach ($poolInfo['rslist'] as $rsid => $rs)
		{
			printOpFormIntro ('updRS', array ('rs_id' => $rsid));
			echo "<tr valign=top class=row_${order}><td><a href='".makeHrefProcess(array('op'=>'delRS', 'pool_id'=>$pool_id, 'id'=>$rsid))."'>";
			printImageHREF ('delete', 'Delete this real server');
			echo "</td><td><input type=text name=rsip value='${rs['rsip']}'></td>";
			echo "<td><input type=text name=rsport size=5 value='${rs['rsport']}'></td>";
			echo "<td><textarea name=rsconfig>${rs['rsconfig']}</textarea></td><td>";
			printImageHREF ('SAVE', 'Save changes', TRUE);
			echo "</td></tr></form>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n";
		finishPortlet();
	}

	startPortlet ('Add one');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>in service</th><th>Address</th><th>Port</th><th>&nbsp;</th></tr>\n";
	printOpFormIntro ('addRS');
	echo "<tr><td>";
	if (getConfigVar ('DEFAULT_IPV4_RS_INSERVICE') == 'yes')
		printImageHREF ('inservice', 'in service');
	else
		printImageHREF ('notinservice', 'NOT in service');
	echo "</td><td><input type=text name=remoteip id=remoteip tabindex=1> ";
	echo "<a href='javascript:;' onclick='window.open(\"" . makeHrefForHelper ('inet4list');
	echo "\", \"findobjectip\", \"height=700, width=400, location=no, menubar=no, resizable=yes, scrollbars=no, status=no, titlebar=no, toolbar=no\");'>";
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
	printOpFormIntro ('addMany');
	echo "<table border=0 align=center>\n<tr><td>";
	if (getConfigVar ('DEFAULT_IPV4_RS_INSERVICE') == 'yes')
		printImageHREF ('inservice', 'in service');
	else
		printImageHREF ('notinservice', 'NOT in service');
	echo "</td><td>Format: ";
	$formats = array
	(
		'ssv_1' => 'SSV: &lt;IP address&gt;',
		'ssv_2' => 'SSV: &lt;IP address&gt; &lt;port&gt;',
		'ipvs_2' => 'ipvsadm -l -n (address and port)',
		'ipvs_3' => 'ipvsadm -l -n (address, port and weight)',
	);
	printSelect ($formats, 'format', 'ssv_1');
	echo "</td><td><input type=submit value=Parse></td></tr>\n";
	echo "<tr><td colspan=3><textarea name=rawtext cols=100 rows=50></textarea></td></tr>\n";
	echo "</table>\n";
	finishPortlet();
}

function renderRSPoolLBForm ($pool_id)
{
	global $nextorder;
	$poolInfo = spotEntity ('ipv4rspool', $pool_id);
	amplifyCell ($poolInfo);

	if (count ($poolInfo['lblist']))
	{
		startPortlet ('Manage existing (' . count ($poolInfo['lblist']) . ')');
		echo "<table cellspacing=0 cellpadding=5 align=center class=cooltable>\n";
		echo "<tr><th>&nbsp;</th><th>LB</th><th>VS</th><th>VS config</th><th>RS config</th><th>&nbsp;</th></tr>\n";
		$order = 'odd';
		foreach ($poolInfo['lblist'] as $object_id => $vslist)
			foreach ($vslist as $vs_id => $configs)
			{
				printOpFormIntro ('updLB', array ('vs_id' => $vs_id, 'object_id' => $object_id));
				echo "<tr valign=top class=row_${order}><td><a href='".makeHrefProcess(array('op'=>'delLB', 'pool_id'=>$pool_id, 'object_id'=>$object_id, 'vs_id'=>$vs_id))."'>";
				printImageHREF ('delete', 'Unconfigure');
				echo "</a></td>";
				echo "<td class=tdleft>";
				renderLBCell ($object_id);
				echo "</td><td class=tdleft>";
				renderCell (spotEntity ('ipv4vs', $vs_id));
				echo "</td><td><textarea name=vsconfig>${configs['vsconfig']}</textarea></td>";
				echo "<td><textarea name=rsconfig>${configs['rsconfig']}</textarea></td><td>";
				printImageHREF ('SAVE', 'Save changes', TRUE);
				echo "</td></tr></form>\n";
				$order = $nextorder[$order];
			}
		echo "</table>\n";
		finishPortlet();
	}

	startPortlet ('Add new');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	printOpFormIntro ('addLB');
	echo "<tr valign=top><th>LB / VS</th><td class=tdleft>";
	printSelect (getNarrowObjectList ('IPV4LB_LISTSRC'), 'object_id', NULL, 1);
	printSelect (getIPv4VSOptions(), 'vs_id', NULL, 2);
	echo "</td><td>";
	printImageHREF ('add', 'Configure LB', TRUE, 5);
	echo "</td></tr>\n";
	echo "<tr><th>VS config</th><td colspan=2><textarea tabindex=3 name=vsconfig rows=10 cols=80></textarea></td></tr>";
	echo "<tr><th>RS config</th><td colspan=2><textarea tabindex=4 name=rsconfig rows=10 cols=80></textarea></td></tr>";
	echo "</form></table>\n";
	finishPortlet();
}

function renderVServiceLBForm ($vs_id)
{
	global $nextorder;
	$vsinfo = spotEntity ('ipv4vs', $vs_id);
	amplifyCell ($vsinfo);

	if (count ($vsinfo['rspool']))
	{
		startPortlet ('Manage existing (' . count ($vsinfo['rspool']) . ')');
		echo "<table cellspacing=0 cellpadding=5 align=center class=cooltable>\n";
		echo "<tr><th>&nbsp;</th><th>LB</th><th>RS pool</th><th>VS config</th><th>RS config</th><th>&nbsp;</th></tr>\n";
		$order = 'odd';
		foreach ($vsinfo['rspool'] as $pool_id => $rspinfo)
			foreach ($rspinfo['lblist'] as $object_id => $configs)
			{
				printOpFormIntro ('updLB', array ('pool_id' => $pool_id, 'object_id' => $object_id));
				echo "<tr valign=top class=row_${order}><td><a href='".makeHrefProcess(array('op'=>'delLB', 'pool_id'=>$pool_id, 'object_id'=>$object_id, 'vs_id'=>$vs_id))."'>";
				printImageHREF ('delete', 'Unconfigure');
				echo "</a></td>";
				echo "<td class=tdleft>";
				renderLBCell ($object_id);
				echo "</td><td class=tdleft>";
				renderCell (spotEntity ('ipv4rspool', $pool_id));
				echo "</td><td><textarea name=vsconfig>${configs['vsconfig']}</textarea></td>";
				echo "<td><textarea name=rsconfig>${configs['rsconfig']}</textarea></td><td>";
				printImageHREF ('SAVE', 'Save changes', TRUE);
				echo "</td></tr></form>\n";
				$order = $nextorder[$order];
			}
		echo "</table>\n";
		finishPortlet();
	}

	startPortlet ('Add new');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	printOpFormIntro ('addLB');
	echo "<tr valign=top><th>LB / RS pool</th><td class=tdleft>";
	printSelect (getNarrowObjectList ('IPV4LB_LISTSRC'), 'object_id', NULL, 1);
	printSelect (getIPv4RSPoolOptions(), 'pool_id', NULL, 2);
	echo "</td><td>";
	printImageHREF ('add', 'Configure LB', TRUE, 5);
	echo "</td></tr>\n";
	echo "<tr><th>VS config</th><td colspan=2><textarea tabindex=3 name=vsconfig rows=10 cols=80></textarea></td></tr>";
	echo "<tr><th>RS config</th><td colspan=2><textarea tabindex=4 name=rsconfig rows=10 cols=80></textarea></td></tr>";
	echo "</form></table>\n";
	finishPortlet();
}

function renderRSPool ($pool_id)
{
	global $nextorder;
	$poolInfo = spotEntity ('ipv4rspool', $pool_id);
	if ($poolInfo == NULL)
	{
		showError ('Could not load data!', __FUNCTION__);
		return;
	}
	amplifyCell ($poolInfo);

	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	if (!empty ($poolInfo['name']))
		echo "<tr><td colspan=2 align=center><h1>{$poolInfo['name']}</h1></td></tr>";
	echo "<tr><td class=pcleft>\n";

	startPortlet ('Summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	if (!empty ($poolInfo['name']))
		echo "<tr><th width='50%' class=tdright>Pool name:</th><td class=tdleft>${poolInfo['name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Real servers:</th><td class=tdleft>" . count ($poolInfo['rslist']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Load balancers:</th><td class=tdleft>" . count ($poolInfo['lblist']) . "</td></tr>\n";
	printTagTRs (makeHref(array('page'=>'ipv4rsplist', 'tab'=>'default'))."&");
	if (!empty ($poolInfo['vsconfig']))
	{
		echo "<tr><th width='50%' class=tdright>VS configuration:</th><td>&nbsp;</td></tr>\n";
		echo "<tr><td colspan=2 class='dashed slbconf'>${poolInfo['vsconfig']}</td></tr>\n";
	}
	if (!empty ($poolInfo['rsconfig']))
	{
		echo "<tr><th width='50%' class=tdright>RS configuration:</th><td>&nbsp;</td></tr>\n";
		echo "<tr><td colspan=2 class='dashed slbconf'>${poolInfo['rsconfig']}</td></tr>\n";
	}
	echo "</table>";
	finishPortlet();

	startPortlet ('Load balancers (' . count ($poolInfo['lblist']) . ')');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>VS</th><th>LB</th><th>VS config</th><th>RS config</th></tr>";
	$order = 'odd';
	foreach ($poolInfo['lblist'] as $object_id => $vslist)
		foreach ($vslist as $vs_id => $configs)
	{
		echo "<tr valign=top class=row_${order}><td class=tdleft><a href='".makeHref(array('page'=>'ipv4vs', 'vs_id'=>$vs_id))."'>";
		renderCell (spotEntity ('ipv4vs', $vs_id));
		echo "</td><td>";
		renderLBCell ($object_id);
		echo "</td><td class=slbconf>${configs['vsconfig']}</td>";
		echo "<td class=slbconf>${configs['rsconfig']}</td></tr>\n";
		$order = $nextorder[$order];
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
		echo "</td><td class=tdleft><a href='".makeHref(array('page'=>'ipaddress', 'ip'=>$rs['rsip']))."'>${rs['rsip']}</a></td>";
		echo "<td class=tdleft>${rs['rsport']}</td><td class=slbconf>${rs['rsconfig']}</td></tr>\n";
	}
	echo "</table>\n";
	finishPortlet();
	echo "</td></tr><tr><td colspan=2>\n";
	renderFilesPortlet ('ipv4rspool', $pool_id);
	echo "</td></tr></table>\n";
}

function renderVSList ()
{
	renderCellList ('ipv4vs', 'Virtual services');
}

function renderVSListEditForm ()
{
	global $nextorder;
	$protocols = array ('TCP' => 'TCP', 'UDP' => 'UDP');

	startPortlet ('Add new');
	printOpFormIntro ('add');
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
	renderNewEntityTags();
	echo "</td></tr>";
	echo "<tr><th>RS configuration</th><td colspan=4 class=tdleft><textarea name=rsconfig rows=10 cols=80></textarea></td></tr>\n";
	echo "</table>";
	echo "</form>\n";
	finishPortlet();

	$vslist = listCells ('ipv4vs');
	if (!count ($vslist))
		return;
	startPortlet ('Manage existing (' . count ($vslist) . ')');
	echo "<table class=cooltable border=0 cellpadding=10 cellspacing=0 align=center>\n";
	echo "<tr><th>&nbsp;</th><th>VIP</th><th>port</th><th>proto</th><th>name</th>";
	echo "<th>VS configuration</th><th>RS configuration</th><th></th></tr>";
	$order = 'odd';
	foreach ($vslist as $vsid => $vsinfo)
	{
		printOpFormIntro ('upd', array ('vs_id' => $vsid));
		echo "<tr valign=top class=row_${order}><td>";
		if ($vsinfo['poolcount'])
			printImageHREF ('nodelete', 'there are ' . $vsinfo['poolcount'] . ' RS pools configured');
		else
		{
			echo "<a href='".makeHrefProcess(array('op'=>'del', 'vs_id'=>$vsid))."'>";
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
		printImageHREF ('SAVE', 'save changes', TRUE);
		echo "</td></tr></form>\n";
		$order = $nextorder[$order];
	}
	echo "</table>";
	finishPortlet();
}

function renderRSPoolList ()
{
	renderCellList ('ipv4rspool', 'RS pools');
}

function editRSPools ()
{
	global $nextorder;
	startPortlet ('Add new');
	printOpFormIntro ('add');
	echo "<table class=widetable border=0 cellpadding=10 cellspacing=0 align=center>\n";
	echo "<tr><th>name</th>";
	echo "<td class=tdleft><input type=text name=name tabindex=1></td><td>";
	printImageHREF ('CREATE', 'create real server pool', TRUE);
	echo "</td></tr><tr><th>VS configuration</th><td><textarea name=vsconfig rows=10 cols=80></textarea></td>";
	echo "<td rowspan=2><h3>assign tags</h3>";
	renderNewEntityTags();
	echo "</td></tr>";
	echo "<tr><th>RS configuration</th><td><textarea name=rsconfig rows=10 cols=80></textarea></td></tr>";
	echo "</table></form>";
	finishPortlet();

	$pool_list = listCells ('ipv4rspool');
	if (!count ($pool_list))
		return;
	startPortlet ('Manage existing (' . count ($pool_list) . ')');
	echo "<table class=cooltable border=0 cellpadding=10 cellspacing=0 align=center>\n";
	echo "<tr><th>&nbsp;</th><th>name</th><th>VS configuration</th><th>RS configuration</th><th>&nbsp;</th></tr>";
	$order='odd';
	foreach ($pool_list as $pool_id => $pool_info)
	{
		printOpFormIntro ('upd', array ('pool_id' => $pool_id));
		echo "<tr valign=top class=row_${order}><td>";
		if ($pool_info['refcnt'] or $pool_info['rscount'])
			printImageHREF ('nodelete', 'RS pool is used ' . $pool_info['refcnt'] . ' time(s)');
		else
		{
			echo "<a href='".makeHrefProcess(array('op'=>'del', 'pool_id'=>$pool_id))."'>";
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
	global $nextorder;
	$rslist = getRSList ();
	$pool_list = listCells ('ipv4rspool');
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
		echo "<tr valign=top class=row_${order}><td><a href='".makeHref(array('page'=>'ipv4rspool', 'pool_id'=>$rsinfo['rspool_id']))."'>";
		echo empty ($pool_list[$rsinfo['rspool_id']]['name']) ? 'ANONYMOUS' : $pool_list[$rsinfo['rspool_id']]['name'];
		echo '</a></td><td align=center>';
		if ($rsinfo['inservice'] == 'yes')
			printImageHREF ('inservice', 'in service');
		else
			printImageHREF ('notinservice', 'NOT in service');
		echo "</td><td><a href='".makeHref(array('page'=>'ipaddress', 'ip'=>$rsinfo['rsip']))."'>${rsinfo['rsip']}</a></td>";
		echo "<td>${rsinfo['rsport']}</td>";
		echo "<td><pre>${rsinfo['rsconfig']}</pre></td>";
		echo "</tr>\n";
	}
	echo "</table>";
}

function renderLBList ()
{
	global $nextorder;
	echo "<table class=widetable border=0 cellpadding=10 cellspacing=0 align=center>\n";
	echo "<tr><th>Object</th><th>RS pools configured</th></tr>";
	$oicache = array();
	$order = 'odd';
	foreach (getLBList() as $object_id => $poolcount)
	{
		if (!isset ($oicache[$object_id]))
			$oicache[$object_id] = getObjectInfo ($object_id);
		echo "<tr valign=top class=row_${order}><td><a href='".makeHref(array('page'=>'object', 'object_id'=>$object_id))."'>";
		echo $oicache[$object_id]['dname'] . '</a></td>';
		echo "<td>${poolcount}</td></tr>";
		$order = $nextorder[$order];
	}
	echo "</table>";
}

function renderRSPoolRSInServiceForm ($pool_id)
{
	$poolInfo = spotEntity ('ipv4rspool', $pool_id);
	amplifyCell ($poolInfo);
	printOpFormIntro ('upd', array ('rscount' => count ($poolInfo['rslist'])));
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

function renderLivePTR ($id)
{
	if (isset($_REQUEST['pg']))
		$page = $_REQUEST['pg'];
	else
		$page=0;
	global $pageno, $tabno;
	$maxperpage = getConfigVar ('IPV4_ADDRS_PER_PAGE');
	$range = spotEntity ('ipv4net', $id);
	loadIPv4AddrList ($range);
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
			echo "<a href='".makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'id'=>$id, 'pg'=>$i))."'>$i</a> ";
	echo "</center>";

	// FIXME: address counter could be calculated incorrectly in some cases
	printOpFormIntro ('importPTRData', array ('addrcount' => ($endip - $startip + 1)));

	echo "<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center'>\n";
	echo "<tr><th>address</th><th>current name</th><th>DNS data</th><th>import</th></tr>\n";
	$idx = 1;
	$box_counter = 1;
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
		echo "'><a href='".makeHref(array('page'=>'ipaddress', 'ip'=>$straddr))."'>${straddr}</a></td>";
		echo "<td class=tdleft>${addr['name']}</td><td class=tdleft>${ptrname}</td><td>";
		if ($print_cbox)
			echo "<input type=checkbox name=import_${idx} tabindex=${idx} id=atom_1_" . $box_counter++ . "_1>";
		else
			echo '&nbsp;';
		echo "</td></tr>\n";
		$idx++;
	}
	echo "<tr><td colspan=3 align=center><input type=submit value='Import selected records'></td><td>";
	echo --$box_counter ? "<a href='javascript:;' onclick=\"toggleColumnOfAtoms(1, 1, ${box_counter})\">(toggle selection)</a>" : '&nbsp;';
	echo "</td></tr>";
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

function renderAutoPortsForm ($object_id)
{
	$info = getObjectInfo ($object_id);
	$ptlist = readChapter ('PortType');
	echo "<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center'>\n";
	echo "<caption>The following ports can be quickly added:</caption>";
	echo "<tr><th>type</th><th>name</th></tr>";
	foreach (getAutoPorts ($info['objtype_id']) as $autoport)
		echo "<tr><td>" . $ptlist[$autoport['type']] . "</td><td>${autoport['name']}</td></tr>";
	printOpFormIntro ('generate');
	echo "<tr><td colspan=2 align=center>";
	echo "<input type=submit value='Generate'>";
	echo "</td></tr>";
	echo "</table></form>";
}

function renderTagRowForViewer ($taginfo, $level = 0)
{
	$self = __FUNCTION__;
	if (!count ($taginfo['kids']))
		$level++; // Shift instead of placing a spacer. This won't impact any nested nodes.
	echo "<tr><td align=left style='padding-left: " . ($level * 16) . "px;'>";
	if (count ($taginfo['kids']))
		printImageHREF ('node-expanded-static');
	echo '<span title="id = ' . $taginfo['id'] . '">';
	echo $taginfo['tag'] . '</span>';
	echo "</td></tr>\n";
	foreach ($taginfo['kids'] as $kid)
		$self ($kid, $level + 1);
}

function renderTagRowForEditor ($taginfo, $level = 0)
{
	$self = __FUNCTION__;
	global $taglist;
	if (!count ($taginfo['kids']))
		$level++; // Idem
	echo "<tr><td align=left style='padding-left: " . ($level * 16) . "px;'>";
	if (count ($taginfo['kids']))
		printImageHREF ('node-expanded-static');
	$nrefs = 0;
	foreach ($taginfo['refcnt'] as $part)
		$nrefs += $part;
	if ($nrefs > 0 or count ($taginfo['kids']) > 0)
		printImageHREF ('nodestroy', "${nrefs} references, " . count ($taginfo['kids']) . ' sub-tags');
	else
	{
		echo "<a href='".makeHrefProcess(array('op'=>'destroyTag', 'tag_id'=>$taginfo['id']))."'>";
		printImageHREF ('destroy', 'Delete tag');
		echo "</a>";
	}
	echo "</td>\n<td>";
	printOpFormIntro ('updateTag', array ('tag_id' => $taginfo['id']));
	echo "<input type=text name=tag_name ";
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
		$self ($kid, $level + 1);
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

function renderTagTreeEditor ()
{
	function printNewItemTR ()
	{
		global $taglist;
		printOpFormIntro ('createTag');
		echo "<tr><td class=tdleft>";
		printImageHREF ('create', 'Create tag', TRUE);
		echo '</td><td><input type=text name=tag_name tabindex=100></td><td><select name=parent_id tabindex=101>';
		echo "<option value=0>-- NONE --</option>\n";
		foreach ($taglist as $taginfo)
			echo "<option value=${taginfo['id']}>${taginfo['tag']}</option>";
		echo "</select></td><td>";
		printImageHREF ('create', 'Create tag', TRUE, 102);
		echo "</td></tr></form>\n";
	}
	global $taglist, $tagtree;

	$otags = getOrphanedTags();
	if (count ($otags))
	{
		startPortlet ('fallen leaves');
		echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
		echo "<tr><th>tag</th><th>parent</th><th>&nbsp;</th></tr>\n";
		foreach ($otags as $taginfo)
		{
			printOpFormIntro ('updateTag', array ('tag_id' => $taginfo['id'], 'tag_name' => $taginfo['tag']));
			echo "<tr><td>${taginfo['tag']}</td><td><select name=parent_id>";
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
	}

	startPortlet ('tag tree');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>&nbsp;</th><th>tag</th><th>parent</th><th>&nbsp;</th></tr>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach ($tagtree as $taginfo)
		renderTagRowForEditor ($taginfo);
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo '</table>';
	finishPortlet();
}

function renderTagCheckbox ($inputname, $preselect, $taginfo, $level = 0)
{
	$self = __FUNCTION__;
	if (tagOnChain ($taginfo, $preselect))
	{
		$selected = ' checked';
		$class = 'seltagbox';
	}
	else
	{
		$selected = '';
		$class = 'tagbox';
	}
	echo "<tr><td colspan=2 class=${class} style='padding-left: " . ($level * 16) . "px;'>";
	echo "<input type=checkbox name='${inputname}[]' value='${taginfo['id']}'${selected}> ";
	echo $taginfo['tag'] . "</td></tr>\n";
	foreach ($taginfo['kids'] as $kid)
		$self ($inputname, $preselect, $kid, $level + 1);
}

function renderEntityTags ($entity_id)
{
	global $tagtree, $target_given_tags, $pageno, $page, $target_given_tags;
	$bypass_name = $page[$pageno]['bypass'];
	startPortlet ('Tag list');
	echo '<table border=0 cellspacing=0 cellpadding=3 align=center>';
	printOpFormIntro ('saveTags');
	// Show a tree of tags with preselection, which matches current chain.
	foreach ($tagtree as $taginfo)
		renderTagCheckbox ('taglist', $target_given_tags, $taginfo);
	echo '<tr><td class=tdleft>';
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</form></td><td class=tdright>";
	if (!count ($target_given_tags))
		printImageHREF ('CLEAR gray');
	else
	{
		printOpFormIntro ('saveTags', array ('taglist[]' => ''));
		printImageHREF ('CLEAR', 'Reset all tags', TRUE);
		echo '</form>';
	}
	echo '</td></tr></table>';
	finishPortlet();
}

function printTagTRs ($baseurl = '')
{
	global $expl_tags, $impl_tags, $auto_tags, $target_given_tags;
	if (getConfigVar ('SHOW_EXPLICIT_TAGS') == 'yes' and count ($target_given_tags))
	{
		echo "<tr><th width='50%' class=tagchain>Given explicit tags:</th><td class=tagchain>";
		echo serializeTags ($target_given_tags, $baseurl) . "</td></tr>\n";
		// only display "effective" line, when if differs
		if (tagChainCmp ($target_given_tags, $expl_tags))
			echo "<tr><th width='50%' class=tagchain>Effective explicit tags:</th><td class=tagchain>" .
				serializeTags ($expl_tags, $baseurl) . "</td></tr>\n";
	}
	if (getConfigVar ('SHOW_IMPLICIT_TAGS') == 'yes' and count ($impl_tags))
	{
		echo "<tr><th width='50%' class=tagchain>Effective implicit tags:</th><td class=tagchain>";
		echo serializeTags ($impl_tags, $baseurl) . "</td></tr>\n";
	}
	if (getConfigVar ('SHOW_AUTOMATIC_TAGS') == 'yes' and count ($auto_tags))
	{
		echo "<tr><th width='50%' class=tagchain>Automatic tags:</th><td class=tagchain>";
		echo serializeTags ($auto_tags) . "</td></tr>\n";
	}
}

// This one is going to replace the tag filter.
function renderCellFilterPortlet ($preselect, $realm, $bypass_name = '', $bypass_value = '')
{
	global $pageno, $tabno, $taglist, $tagtree;
	startPortlet ('filter');
	echo "<form method=get>\n";
	echo '<table border=0 align=center>';
	$ruler = "<tr><td colspan=2 class=tagbox><hr></td></tr>\n";
	$hr = '';
	// and/or block
	if (getConfigVar ('FILTER_SUGGEST_ANDOR') == 'yes' or strlen ($preselect['andor']))
	{
		echo $hr;
		$hr = $ruler;
		$andor = strlen ($preselect['andor']) ? $preselect['andor'] : getConfigVar ('FILTER_DEFAULT_ANDOR');
		echo '<tr>';
		foreach (array ('and', 'or') as $boolop)
		{
			$class = $andor == $boolop ? 'seltagbox' : 'tagbox';
			$checked = $andor == $boolop ? ' checked' : '';
			echo "<td class=${class}><input type=radio name=andor value=${boolop}";
			echo $checked . ">${boolop}</input></td>";
		}
	}
	// tags block
	if (getConfigVar ('FILTER_SUGGEST_TAGS') == 'yes' or count ($preselect['tagidlist']))
	{
		echo $hr;
		$hr = $ruler;
		// Show a tree of tags, pre-select according to currently requested list filter.
		global $tagtree;
		$objectivetags = getObjectiveTagTree ($tagtree, $realm);
		if (!count ($objectivetags))
			echo "<tr><td colspan=2 class='tagbox sparenetwork'>(nothing is tagged yet)</td></tr>";
		else
			foreach ($objectivetags as $taginfo)
				renderTagCheckbox ('cft', buildTagChainFromIds ($preselect['tagidlist']), $taginfo);
	}
	// predicates block
	if (getConfigVar ('FILTER_SUGGEST_PREDICATES') == 'yes' or count ($preselect['pnamelist']))
	{
		echo $hr;
		$hr = $ruler;
		global $pTable;
		$myPredicates = array();
		$psieve = getConfigVar ('FILTER_PREDICATE_SIEVE');
		// Repack matching predicates in a way, which tagOnChain() understands.
		foreach (array_keys ($pTable) as $pname)
			if (mb_ereg_match ($psieve, $pname))
				$myPredicates[] = array ('id' => $pname, 'tag' => $pname, 'kids' => array());
		if (!count ($myPredicates))
			echo "<tr><td colspan=2 class='tagbox sparenetwork'>(no predicates to show)</td></tr>";
		else
		{
			// Repack preselect likewise.
			$myPreselect = array();
			foreach ($preselect['pnamelist'] as $pname)
				$myPreselect[] = array ('id' => $pname);
			foreach ($myPredicates as $pinfo)
				renderTagCheckbox ('cfp', $myPreselect, $pinfo);
		}
	}
	// extra code
	if (getConfigVar ('FILTER_SUGGEST_EXTRA') == 'yes' or strlen ($preselect['extratext']))
	{
		echo $hr;
		$hr = $ruler;
		$class = isset ($preselect['extraclass']) ? 'class=' . $preselect['extraclass'] : '';
		echo "<tr><td colspan=2><textarea name=cfe ${class}>\n" . $preselect['extratext'];
		echo "</textarea></td></tr>\n";
	}
	// submit block
	{
		echo $hr;
		$hr = $ruler;
		// "apply"
		echo '<tr><td>';
		echo "<input type=hidden name=page value=${pageno}>\n";
		echo "<input type=hidden name=tab value=${tabno}>\n";
		if ($bypass_name != '')
			echo "<input type=hidden name=${bypass_name} value='${bypass_value}'>\n";
		printImageHREF ('apply', 'Apply filter', TRUE);
		echo "</form></td><td>";
		// "reset"
		echo "<form method=get>\n";
		echo "<input type=hidden name=page value=${pageno}>\n";
		echo "<input type=hidden name=tab value=${tabno}>\n";
		if ($bypass_name != '')
			echo "<input type=hidden name=${bypass_name} value='${bypass_value}'>\n";
		printImageHREF ('clear', 'reset', TRUE);
		echo '</form></td></tr>';
	}
	echo '</table>';
	finishPortlet();
}

// Dump all tags in a single SELECT element.
function renderNewEntityTags ()
{
	global $taglist, $tagtree;
	if (!count ($taglist))
	{
		echo "No tags defined";
		return;
	}
	echo '<div class=tagselector><table border=0 align=center>';
	foreach ($tagtree as $taginfo)
		renderTagCheckbox ('taglist', array(), $taginfo);
	echo '</table></div>';
}

function renderTagRollerForRow ($row_id)
{
	$a = rand (1, 20);
	$b = rand (1, 20);
	$sum = $a + $b;
	printOpFormIntro ('rollTags', array ('realsum' => $sum));
	echo "<table border=1 align=center>";
	echo "<tr><td colspan=2>This special tool allows assigning tags to physical contents (racks <strong>and all contained objects</strong>) of the current ";
	echo "rack row.<br>The tag(s) selected below will be ";
	echo "appended to already assigned tag(s) of each particular entity. </td></tr>";
	echo "<tr><th>Tags</th><td>";
	renderNewEntityTags();
	echo "</td></tr>";
	echo "<tr><th>Control question: the sum of ${a} and ${b}</th><td><input type=text name=sum></td></tr>";
	echo "<tr><td colspan=2 align=center><input type=submit value='Go!'></td></tr>";
	echo "</table></form>";
}

function renderObjectSLB ($object_id)
{
	global $nextorder;
	// Keep the list in a variable to assist in decoding pool name below.

	startPortlet ('Add new');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	printOpFormIntro ('addLB');
	echo "<tr valign=top><th>VS / RS pool</th><td class=tdleft>";
	printSelect (getIPv4VSOptions(), 'vs_id', NULL, 1);
	echo "</td><td>";
	printSelect (getIPv4RSPoolOptions(), 'pool_id', NULL, 2);
	echo "</td><td>";
	printImageHREF ('add', 'Configure LB', TRUE, 5);
	echo "</td></tr>\n";
	echo "<tr><th>VS config</th><td colspan=2><textarea tabindex=3 name=vsconfig rows=10 cols=80></textarea></td></tr>";
	echo "<tr><th>RS config</th><td colspan=2><textarea tabindex=4 name=rsconfig rows=10 cols=80></textarea></td></tr>";
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
			printOpFormIntro ('updLB', array ('vs_id' => $vs_id, 'pool_id' => $vsinfo['pool_id']));
			echo "<tr valign=top class=row_${order}><td><a href='".makeHrefProcess(array('op'=>'delLB', 'pool_id'=>$vsinfo['pool_id'], 'object_id'=>$object_id, 'vs_id'=>$vs_id))."'>";
			printImageHREF ('delete', 'Unconfigure');
			echo "</a></td>";
			echo "</td><td class=tdleft>";
			renderCell (spotEntity ('ipv4vs', $vs_id));
			echo "</td><td class=tdleft>";
			renderCell (spotEntity ('ipv4rspool', $vsinfo['pool_id']));
			echo "</td><td><textarea name=vsconfig>${vsinfo['vsconfig']}</textarea></td>";
			echo "<td><textarea name=rsconfig>${vsinfo['rsconfig']}</textarea></td><td>";
			printImageHREF ('SAVE', 'Save changes', TRUE);
			echo "</td></tr></form>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n";
		finishPortlet();
	}
}

function renderEditRSPool ($pool_id)
{
	$poolinfo = spotEntity ('ipv4rspool', $pool_id);
	printOpFormIntro ('updIPv4RSP');
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
	$vsinfo = spotEntity ('ipv4vs', $vsid);
	amplifyCell ($vsinfo);
	printOpFormIntro ('updIPv4VS');
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>VIP:</th><td class=tdleft><input tabindex=1 type=text name=vip value='${vsinfo['vip']}'></td></tr>\n";
	echo "<tr><th class=tdright>port:</th><td class=tdleft><input tabindex=2 type=text name=vport value='${vsinfo['vport']}'></td></tr>\n";
	echo "<tr><th class=tdright>proto:</th><td class=tdleft>";
	printSelect (array ('TCP' => 'TCP', 'UDP' => 'UDP'), 'proto', $vsinfo['proto']);
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>name:</th><td class=tdleft><input tabindex=4 type=text name=name value='${vsinfo['name']}'></td></tr>\n";
	echo "<tr><th class=tdright>VS config:</th><td class=tdleft><textarea tabindex=5 name=vsconfig rows=20 cols=80>${vsinfo['vsconfig']}</textarea></td></tr>\n";
	echo "<tr><th class=tdright>RS config:</th><td class=tdleft><textarea tabindex=6 name=rsconfig rows=20 cols=80>${vsinfo['rsconfig']}</textarea></td></tr>\n";
	echo "<tr><th class=submit colspan=2>";
	printImageHREF ('SAVE', 'Save changes', TRUE, 7);
	echo "</td></tr>\n";
	echo "</table></form>\n";
}

function dump ($var)
{
	echo '<div align=left><pre>';
	print_r ($var);
	echo '</pre></div>';
}

function renderRackCodeViewer ()
{
	$text = loadScript ('RackCode');
	echo '<table width="100%" border=0>';
	$lineno = 1;
	foreach (explode ("\n", $text) as $line)
	{
		echo "<tr><td class=tdright><a name=line${lineno}>${lineno}</a></td>";
		echo "<td class=tdleft>${line}</td></tr>";
		$lineno++;
	}
}

function renderRackCodeEditor ()
{
	$text = loadScript ('RackCode');
	printOpFormIntro ('saveRackCode');
	echo <<<ENDJAVASCRIPT
<script type="text/javascript">
var prevCode = '';
function verify()
{
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: "ac=verifyCode&code="+RCTA.getCode(),
		success: function (data)
		{
			arr = data.split("\\n");
			if (arr[0] == "ACK")
			{
				$("#SaveChanges")[0].disabled = "";
				$("#ShowMessage")[0].innerHTML = "Code verification OK, don't forget to save the code";
				$("#ShowMessage")[0].className = "msg_success";
			}
			else
			{
				$("#SaveChanges")[0].disabled = "disabled";
				$("#ShowMessage")[0].innerHTML = arr[1];
				$("#ShowMessage")[0].className = "msg_warning";
			}
			prevCode = RCTA.getCode();
		}
	});
}


function invalidate()
{
	if (prevCode != RCTA.getCode())
	{
		prevCode = RCTA.getCode();
		$("#SaveChanges")[0].disabled = "disabled";
		$("#ShowMessage")[0].innerHTML = "";
		$("#ShowMessage")[0].className = "";
	}
}

setInterval(invalidate, 1000);
</script>
ENDJAVASCRIPT;

	echo '<table border=0 align=center>';
	echo "<tr><td><textarea rows=40 cols=100 name=rackcode id=RCTA class='codepress rackcode'>";
	echo $text . "</textarea></td></tr>\n";
	echo "<tr><td align=center>";
	echo '<div id="ShowMessage"></div>';
	echo "<input type='button' value='Verify' onclick='verify();'>";
	echo "<input type='submit' value='Save' disabled='disabled' id='SaveChanges' onclick='RCTA.toggleEditor();'>";
//	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>";
	echo '</table>';
	echo "</form>";
}

function renderUser ($user_id)
{
	global $target_given_tags;
	$userinfo = spotEntity ('user', $user_id);

	startPortlet ('summary');
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Account name:</th><td class=tdleft>${userinfo['user_name']}</td></tr>";
	echo '<tr><th class=tdright>Real name:</th><td class=tdleft>' . $userinfo['user_realname'] . '</td></tr>';
	// Using printTagTRs() is inappropriate here, because autotags will be filled with current user's
	// data, not the viewed one. Another special reason is that the displayed user's given tags are in
	// the "target" chain.
	$baseurl = makeHref(array('page'=>'userlist', 'tab'=>'default'))."&";
	if (getConfigVar ('SHOW_EXPLICIT_TAGS') == 'yes' and count ($target_given_tags))
	{
		echo "<tr><th width='50%' class=tagchain>Given explicit tags:</th><td class=tagchain>";
		echo serializeTags ($target_given_tags, $baseurl) . "</td></tr>\n";
	}
	$target_shadow = getImplicitTags ($target_given_tags);
	if (getConfigVar ('SHOW_IMPLICIT_TAGS') == 'yes' and count ($target_shadow))
	{
		echo "<tr><th width='50%' class=tagchain>Given implicit tags:</th><td class=tagchain>";
		echo serializeTags ($target_shadow, $baseurl) . "</td></tr>\n";
	}
	$target_auto_tags = generateEntityAutoTags ('user', $userinfo['user_name']);
	if (getConfigVar ('SHOW_AUTOMATIC_TAGS') == 'yes' and count ($target_auto_tags))
	{
		echo "<tr><th width='50%' class=tagchain>Automatic tags:</th><td class=tagchain>";
		echo serializeTags ($target_auto_tags) . "</td></tr>\n";
	}
	echo '</table>';
	finishPortlet();

	renderFilesPortlet ('user', $user_id);
}

function renderMyPasswordEditor ()
{
	printOpFormIntro ('changeMyPassword');
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
	echo "</head><body>";
	global $root, $pageno, $tabno,
		$user_given_tags,
		$target_given_tags,
		$auto_tags,
		$expl_tags,
		$impl_tags;
	echo "<table border=1 cellspacing=0 cellpadding=3 width='50%' align=center>\n";
	echo '<tr><th colspan=2><h3>';
	printImageHREF ('DENIED');
	echo ' access denied ';
	printImageHREF ('DENIED');
	echo '</h3></th></tr>';
	echo "<tr><th width='50%' class=tagchain>User given tags:</th><td class=tagchain>";
	echo serializeTags ($user_given_tags) . "&nbsp;</td></tr>\n";
	echo "<tr><th width='50%' class=tagchain>Target given tags:</th><td class=tagchain>";
	echo serializeTags ($target_given_tags) . "&nbsp;</td></tr>\n";
	echo "<tr><th width='50%' class=tagchain>Effective explicit tags:</th><td class=tagchain>";
	echo serializeTags ($expl_tags) . "&nbsp;</td></tr>\n";
	echo "<tr><th width='50%' class=tagchain>Effective implicit tags:</th><td class=tagchain>";
	echo serializeTags ($impl_tags) . "&nbsp;</td></tr>\n";
	echo "<tr><th width='50%' class=tagchain>Automatic tags:</th><td class=tagchain>";
	echo serializeTags ($auto_tags) . "&nbsp;</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Requested page:</th><td class=tdleft>${pageno}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Requested tab:</th><td class=tdleft>${tabno}</td></tr>\n";
	echo "<tr><td colspan=2 align=center>Click <a href='${root}?logout'>here</a> to logout.</td></tr>\n";
	echo "</table>\n";
	echo "</body></html>";
}

function renderMyAccount ()
{
	global $remote_username, $remote_displayname;
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0 width='50%'>";
	echo "<tr><td colspan=2 align=center><h1>${remote_username}</h1></td></tr>\n";
	echo "<tr><td colspan=2 align=center><h2>${remote_displayname}</h2></td></tr>\n";
	echo "</table>";
}

// File-related functions
function renderFile ($file_id)
{
	global $nextorder, $aac, $root;
	$file = getFileInfo ($file_id);
	if ($file == NULL)
	{
		showError ('getFileInfo() failed', __FUNCTION__);
		return;
	}
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>" . htmlspecialchars ($file['name']) . "</h1></td></tr>\n";
	echo "<tr><td class=pcleft>";
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>Type:</th>";
	printf("<td class=tdleft>%s</td></tr>", htmlspecialchars ($file['type']));
	echo "<tr><th width='50%' class=tdright>Size:</th>";
	echo "<td class=tdleft><a href='${root}download.php?file_id=${file_id}'>";
	printImageHREF ('download', 'Download file');
	printf("</a>&nbsp;%s</td></tr>", formatFileSize($file['size']));
	echo "<tr><th width='50%' class=tdright>Created:</th>";
	printf("<td class=tdleft>%s</td></tr>", formatTimestamp($file['ctime']));
	echo "<tr><th width='50%' class=tdright>Modified:</th>";
	printf("<td class=tdleft>%s</td></tr>", formatTimestamp($file['mtime']));
	echo "<tr><th width='50%' class=tdright>Accessed:</th>";
	printf("<td class=tdleft>%s</td></tr>", formatTimestamp($file['atime']));

	printTagTRs (makeHref(array('page'=>'files', 'tab'=>'default'))."&");
	if (!empty ($file['comment']))
	{
		echo '<tr><th class=slbconf>Comment:</th><td>&nbsp;</td></tr>';
		echo '<tr><td colspan=2 class="dashed slbconf">' . string_insert_hrefs (htmlspecialchars ($file['comment'])) . '</td></tr>';
	}
	echo "</table><br>\n";
	finishPortlet();

	$links = getFileLinks ($file_id);
	if (count ($links))
	{
		startPortlet ('Links (' . count ($links) . ')');
		echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
		foreach ($links as $link)
		{
			echo '<tr><td class=tdleft>';
			switch ($link['entity_type'])
			{
				case 'user':
				case 'ipv4net':
				case 'rack':
				case 'ipv4vs':
				case 'ipv4rspool':
					renderCell (spotEntity ($link['entity_type'], $link['entity_id']));
					break;
				default:
					echo formatEntityName ($link['entity_type']) . ': ';
					echo "<a href='" . makeHref(array('page'=>$link['page'], $link['id_name']=>$link['entity_id']));
					echo "'>${link['name']}</a>";
					break;
			}
			echo '</td></tr>';
		}
		echo "</table><br>\n";
		finishPortlet();
	}

	if ('' != ($pcode = getFilePreviewCode ($file)))
	{
		echo "</td><td class=pcright>";
		startPortlet ('preview');
		echo $pcode;
		finishPortlet();
	}

	echo "</td></tr>";
	echo "</table>\n";
}

function renderFileReuploader ()
{
	startPortlet ('Replace existing contents');
	printOpFormIntro ('replaceFile', array (), TRUE);
	echo "<input type=file size=10 name=file tabindex=100>&nbsp;\n";
	printImageHREF ('save', 'Save changes', TRUE, 101);
	echo "</form>\n";
	finishPortlet();
}

function renderFileProperties ($file_id)
{
	$file = getFileInfo ($file_id);
	if ($file === NULL)
	{
		showError ('getFileInfo() failed', __FUNCTION__);
		return;
	}
	echo '<table border=0 align=center>';
	printOpFormIntro ('updateFile');
	echo "<tr><th class=tdright>MIME-type:</th><td class=tdleft><input tabindex=101 type=text name=file_type value='";
	echo htmlspecialchars ($file['type']) . "'></td></tr>";
	echo "<tr><th class=tdright>Filename:</th><td class=tdleft><input tabindex=102 type=text name=file_name value='";
	echo htmlspecialchars ($file['name']) . "'></td></tr>\n";
	echo "<tr><th class=tdright>Comment:</th><td class=tdleft><textarea tabindex=103 name=file_comment rows=10 cols=80>\n";
	echo htmlspecialchars ($file['comment']) . "</textarea></td></tr>\n";
	echo "<tr><th class=submit colspan=2>";
	printImageHREF ('SAVE', 'Save changes', TRUE, 102);
	echo '</th></tr></form></table>';
}

function renderFileBrowser ()
{
	renderCellList ('file', 'Files', TRUE);
}

// Like renderFileBrowser(), but with the option to delete files
function renderFileManager ()
{
	global $nextorder;
	// Used for uploading a parentless file
	startPortlet ('Upload new');
	echo "<table border=0 cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	echo "<tr><th>File</th><th>Comment</th><th></th></tr>\n";
	printOpFormIntro ('addFile', array (), TRUE);
	echo "<tr>";
	echo "<td class=tdleft><input type='file' size='10' name='file' tabindex=100></td>\n";
	echo "<td class=tdleft><textarea tabindex=101 name=comment rows=10 cols=80></textarea></td>\n";
	echo '<td>';
	printImageHREF ('CREATE', 'Upload file', TRUE, 102);
	echo '</td></tr></form>';
	echo "</table><br>\n";
	finishPortlet();

	$files = listCells ('file');
	if (!count ($files))
		return;

	startPortlet ('Manage existing (' . count ($files) . ')');
	$order = 'odd';
	echo '<table cellpadding=5 cellspacing=0 align=center class=cooltable>';
	echo '<tr><th>File</th><th>Unlink</th><th>Destroy</th></tr>';
	foreach ($files as $file)
	{
		printf("<tr class=row_%s valign=top><td class=tdleft>", $order);
		renderCell ($file);
		// Don't load links data earlier to enable special processing.
		amplifyCell ($file);
		echo '</td><td class=tdleft>';
		echo serializeFileLinks ($file['links'], TRUE);
		echo '</td><td class=tdcenter>';
		if (count ($file['links']))
			printImageHREF ('NODESTROY', 'References (' . count ($file['links']) . ')');
		else
		{
			echo "<a href='".makeHrefProcess(array('op'=>'deleteFile', 'file_id'=>$file['id'])).
				"' onclick=\"javascript:return confirm('Are you sure you want to delete the file?')\">";
			printImageHREF ('DESTROY', 'Delete file');
			echo "</a>";
		}
		echo "</td></tr>\n";
		$order = $nextorder[$order];
	}
	echo '</table>';
	finishPortlet();
}

function renderFilesPortlet ($entity_type = NULL, $entity_id = 0)
{
	if ($entity_type == NULL || $entity_id <= 0)
	{
		showError ('Invalid entity info', __FUNCTION__);
		return;
	}

	$files = getFilesOfEntity ($entity_type, $entity_id);
	if (count ($files))
	{
		startPortlet ('files (' . count ($files) . ')');
		echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
		echo "<tr><th>File</th><th>Comment</th></tr>\n";
		foreach ($files as $file)
		{
			echo "<tr valign=top><td class=tdleft>";
			// That's a bit of overkill and ought to be justified after
			// getFilesOfEntity() returns a standard cell list.
			renderCell (spotEntity ('file', $file['id']));
			echo "</td><td class=tdleft>${file['comment']}</td></tr>";
			if ('' != ($pcode = getFilePreviewCode ($file)))
				echo "<tr><td colspan=2>${pcode}</td></tr>\n";
		}
		echo "</table><br>\n";
		finishPortlet();
	}
}

function renderFilesForEntity ($entity_id)
{
	global $page, $pageno, $etype_by_pageno;
	// Now derive entity_type and bypass_name from pageno.
	$entity_type = $etype_by_pageno[$pageno];
	$id_name = $page[$pageno]['bypass'];
	
	startPortlet ('Upload and link new');
	echo "<table border=0 cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	echo "<tr><th>File</th><th>Comment</th><th></th></tr>\n";
	printOpFormIntro ('addFile', array (), TRUE);
	echo "<tr>";
	echo "<td class=tdleft><input type='file' size='10' name='file' tabindex=100></td>\n";
	echo "<td class=tdleft><textarea tabindex=101 name=comment rows=10 cols=80></textarea></td><td>\n";
	printImageHREF ('CREATE', 'Upload file', TRUE, 102);
	echo "</td></tr></form>";
	echo "</table><br>\n";
	finishPortlet();

	$files = getAllUnlinkedFiles ($entity_type, $entity_id);
	if (count ($files))
	{
		startPortlet ('Link existing (' . count ($files) . ')');
		printOpFormIntro ('linkFile');
		echo "<table border=0 cellspacing=0 cellpadding='5' align='center'>\n";
		echo '<tr><td class=tdleft>';
		printSelect ($files, 'file_id');
		echo '</td><td class=tdleft>';
		printImageHREF ('ATTACH', 'Link file', TRUE);
		echo '</td></tr></table>';
		echo "</form>\n";
		finishPortlet();
	}

	$filelist = getFilesOfEntity ($entity_type, $entity_id);
	if (count ($filelist))
	{
		startPortlet ('Manage linked (' . count ($filelist) . ')');
		echo "<table border=0 cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
		echo "<tr><th>File</th><th>Comment</th><th>Unlink</th></tr>\n";
		foreach ($filelist as $file_id => $file)
		{
			echo "<tr valign=top><td class=tdleft>";
			renderCell (spotEntity ('file', $file_id));
			echo "</td><td class=tdleft>${file['comment']}</td><td class=tdcenter>";
			echo "<a href='".makeHrefProcess(array('op'=>'unlinkFile', 'link_id'=>$file['link_id'], $id_name=>$entity_id))."'>";
			printImageHREF ('CUT', 'Unlink file');
			echo "</a></td></tr>\n";
		}
		echo "</table><br>\n";
		finishPortlet();
	}
}

// Print common operation form prologue, include bypass argument, if
// appropriate, and some extra hidden inputs, if requested.
// Use special encoding for upload forms
function printOpFormIntro ($opname, $extra = array(), $upload = FALSE)
{
	global $root, $pageno, $tabno, $page;

	echo "<form method=post name=${opname} action='${root}process.php?page=${pageno}&tab=${tabno}&op=${opname}'";
	if ($upload)
		echo " enctype='multipart/form-data'";
	echo ">";
	if (isset ($page[$pageno]['bypass']) and isset ($_REQUEST[$page[$pageno]['bypass']]))
		$extra[$page[$pageno]['bypass']] = $_REQUEST[$page[$pageno]['bypass']];
	foreach ($extra as $inputname => $inputvalue)
		echo "<input type=hidden name=${inputname} value='${inputvalue}'>";
}

// This is a dual-purpose formating function:
// 1. Replace empty strings with nbsp.
// 2. Cut strings, which are too long, append "cut here" indicator and provide a mouse hint.
function niftyString ($string, $maxlen = 30)
{
	$cutind = '&hellip;'; // length is 1
	if (empty ($string))
		return '&nbsp;';
	if (mb_strlen ($string) > $maxlen)
		return "<span title='" . htmlspecialchars ($string, ENT_QUOTES, 'UTF-8') . "'>" .
			str_replace (' ', '&nbsp;', str_replace ("\t", ' ', mb_substr ($string, 0, $maxlen - 1))) . $cutind . '</span>';
	return $string;
}

// Iterate over what findRouters() returned and output some text suitable for a TD element.
function printRoutersTD ($rlist)
{
	$rtrclass = 'tdleft';
	foreach ($rlist as $rtr)
	{
		$tmp = getIPv4Address ($rtr['addr']);
		if ($tmp['class'] == 'trerror')
		{
			$rtrclass = 'tdleft trerror';
			break;
		}
	}
	echo "<td class='${rtrclass}'>";
	foreach ($rlist as $rtr)
		renderRouterCell ($rtr['addr'], $rtr['iface'], $rtr['id'], $rtr['dname']);
	echo '</td>';
}

// Same as for routers, but produce two TD cells to lay the content out better.
function printIPv4NetInfoTDs ($netinfo, $tdclass = 'tdleft', $indent = 0, $symbol = 'spacer', $symbolurl = '')
{
	global $root;
	$tags = isset ($netinfo['id']) ? loadEntityTags ('ipv4net', $netinfo['id']) : array();
	if ($symbol == 'spacer')
	{
		$indent++;
		$symbol = '';
	}
	echo "<td class='${tdclass}' style='padding-left: " . ($indent * 16) . "px;'>";
	if (!empty ($symbol))
	{
		if (!empty ($symbolurl))
			echo "<a href='${symbolurl}'>";
		printImageHREF ($symbol, $symbolurl);
		if (!empty ($symbolurl))
			echo '</a>';
	}
	if (isset ($netinfo['id']))
		echo "<a href='${root}?page=ipv4net&id=${netinfo['id']}'>";
	echo "${netinfo['ip']}/${netinfo['mask']}";
	if (isset ($netinfo['id']))
		echo '</a>';
	echo "</td><td class='${tdclass}'>";
	if (!isset ($netinfo['id']))
		printImageHREF ('dragons', 'Here be dragons.');
	else
	{
		echo niftyString ($netinfo['name']);
		if (count ($tags))
			echo '<br><small>' . serializeTags ($tags, "${root}?page=ipv4space&tab=default&") . '</small>';
	}
	echo "</td>";
}

function renderCell ($cell)
{
	global $root;
	switch ($cell['realm'])
	{
	case 'user':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		printImageHREF ('USER');
		echo '</td>';
		echo "<td><a href='${root}?page=user&user_id=${cell['user_id']}'>${cell['user_name']}</a></td></tr>";
		if (strlen ($cell['user_realname']))
			echo "<tr><td><strong>" . niftyString ($cell['user_realname']) . "</strong></td></tr>";
		else
			echo "<tr><td class=sparenetwork>no name</td></tr>";
		echo '<td>';
		if (!isset ($cell['etags']))
			$cell['etags'] = loadEntityTags ('user', $cell['user_id']);
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	case 'file':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		switch ($cell['type'])
		{
			case 'text/plain':
				printImageHREF ('text file');
				break;
			case 'image/jpeg':
			case 'image/png':
			case 'image/gif':
				printImageHREF ('image file');
				break;
			default:
				printImageHREF ('empty file');
				break;
		}
		echo "</td><td>";
		printf ("<a href='${root}?page=file&file_id=%s'><strong>%s</strong></a>", $cell['id'], niftyString ($cell['name']));
		echo "</td><td rowspan=3 valign=top>";
		if (isset ($cell['links']) and count ($cell['links']))
			printf ("<small>%s</small>", serializeFileLinks ($cell['links']));
		echo "</td></tr><tr><td>";
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr><tr><td><a href='${root}download.php?file_id=${cell['id']}'>";
		printImageHREF ('download', 'Download file');
		echo '</a>&nbsp;';
		echo formatFileSize ($cell['size']);
		echo "</td></tr></table>";
		break;
	case 'ipv4vs':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		printImageHREF ('VS');
		echo "</td><td>";
		echo "<a href='${root}?page=ipv4vs&vs_id=${cell['id']}'>";
		echo $cell['dname'] . "</a></td></tr><tr><td>";
		echo $cell['name'] . '</td></tr><tr><td>';
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	case 'ipv4rspool':
		echo "<table class=slbcell><tr><td>";
		echo "<a href='${root}?page=ipv4rspool&pool_id=${cell['id']}'>";
		echo empty ($cell['name']) ? "ANONYMOUS pool [${cell['id']}]" : niftyString ($cell['name']);
		echo "</a></td></tr><tr><td>";
		printImageHREF ('RS pool');
		echo "</td></tr><tr><td>";
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	case 'ipv4net':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		printImageHREF ('NET');
		echo '</td>';
		echo "<td><a href='${root}?page=ipv4net&id=${cell['id']}'>${cell['ip']}/${cell['mask']}</a></td></tr>";
		if (strlen ($cell['name']))
			echo "<tr><td><strong>" . niftyString ($cell['name']) . "</strong></td></tr>";
		else
			echo "<tr><td class=sparenetwork>no name</td></tr>";
		echo '<td>';
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	case 'rack':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		$thumbwidth = getRackImageWidth();
		$thumbheight = getRackImageHeight ($cell['height']);
		echo "<img border=0 width=${thumbwidth} height=${thumbheight} title='${cell['height']} units' ";
		echo "src='render_image.php?img=minirack&rack_id=${cell['id']}'>";
		echo "</td><td>";
		printf ("<a href='${root}?page=rack&rack_id=%s'><strong>%s</strong></a>", $cell['id'], niftyString ($cell['name']));
		echo "</td></tr><tr><td>";
		echo niftyString ($cell['comment']);
		echo "</td></tr><tr><td>";
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	default:
		showError ('odd data', __FUNCTION__);
		break;
	}
}

function renderLBCell ($object_id)
{
	global $root;
	$oi = getObjectInfo ($object_id);
	echo "<table class=slbcell><tr><td>";
	echo "<a href='${root}?page=object&object_id=${object_id}'>${oi['dname']}</a>";
	echo "</td></tr><tr><td>";
	printImageHREF ('LB');
	echo "</td></tr><tr><td><small>";
	echo serializeTags (loadEntityTags ('object', $object_id));
	echo "</small></td></tr></table>";
}

function renderRouterCell ($dottedquad, $ifname, $object_id, $object_dname)
{
	global $root;
	echo "<table class=slbcell><tr><td rowspan=3>${dottedquad}";
	if (!empty ($ifname))
		echo '@' . $ifname;
	echo "</td>";
	echo "<td><a href='${root}?page=object&object_id=${object_id}&hl_ipv4_addr=${dottedquad}'><strong>${object_dname}</strong></a></td>";
	echo "</td></tr><tr><td>";
	printImageHREF ('router');
	echo "</td></tr><tr><td><small>";
	echo serializeTags (loadEntityTags ('object', $object_id));
	echo "</small></td></tr></table>";
}

// Return HTML code necessary to show a preview of the file give. Return an empty string,
// if a preview cannot be shown
function getFilePreviewCode ($file)
{
	global $root;
	$ret = '';
	switch ($file['type'])
	{
		// "These types will be automatically detected if your build of PHP supports them: JPEG, PNG, GIF, WBMP, and GD2."
		case 'image/jpeg':
		case 'image/png':
		case 'image/gif':
			$file = getFile ($file['id']);
			$image = imagecreatefromstring ($file['contents']);
			$width = imagesx ($image);
			$height = imagesy ($image);
			if ($width < getConfigVar ('PREVIEW_IMAGE_MAXPXS') and $height < getConfigVar ('PREVIEW_IMAGE_MAXPXS'))
				$resampled = FALSE;
			else
			{
				$ratio = getConfigVar ('PREVIEW_IMAGE_MAXPXS') / max ($width, $height);
				$width = $width * $ratio;
				$height = $height * $ratio;
				$resampled = TRUE;
			}
			if ($resampled)
				$ret .= "<a href='${root}render_image.php?img=view&file_id=${file['id']}'>";
			$ret .= "<img width=${width} height=${height} src='${root}render_image.php?img=preview&file_id=${file['id']}'>";
			if ($resampled)
				$ret .= '</a>';
			break;
		case 'text/plain':
			if ($file['size'] < getConfigVar ('PREVIEW_TEXT_MAXCHARS'))
			{
				$file = getFile ($file['id']);
				$ret .= '<textarea readonly rows=' . getConfigVar ('PREVIEW_TEXT_ROWS');
				$ret .= ' cols=' . getConfigVar ('PREVIEW_TEXT_COLS') . '>';
				$ret .= htmlspecialchars ($file['contents']);
				$ret .= '</textarea>';
			}
			break;
		default:
			break;
	}
	return $ret;
}

function renderTextEditor ($file_id)
{
	global $CodePressMap;
	$fullInfo = getFile ($file_id);
	printOpFormIntro ('updateFileText', array ('mtime_copy' => $fullInfo['mtime']));
	preg_match('/.+\.([^.]*)$/', $fullInfo['name'], $matches); # get file extension
	if (isset ($matches[1]) && isset ($CodePressMap[$matches[1]]))
		$syntax = $CodePressMap[$matches[1]];
	else
		$syntax = "text";
	echo '<table border=0 align=center>';
	echo "<tr><td><textarea rows=45 cols=180 id=file_text name=file_text tabindex=101 class='codepress " . $syntax . "'>\n";
	echo htmlspecialchars ($fullInfo['contents']) . '</textarea></td></tr>';
	echo "<tr><td class=submit><input type=submit value='Save' onclick='file_text.toggleEditor();'>";
	echo "</td></tr>\n</table></form>\n";
}

function showPathAndSearch ($pageno)
{
	// This function returns array of page numbers leading to the target page
	// plus page number of target page itself. The first element is the target
	// page number and the last element is the index page number.
	function getPath ($targetno)
	{
		$self = __FUNCTION__;
		global $page;
		$path = array();
		// Recursion breaks at first parentless page.
		if (!isset ($page[$targetno]['parent']))
			$path = array ($targetno);
		else
		{
			$path = $self ($page[$targetno]['parent']);
			$path[] = $targetno;
		}
		return $path;
	}
	global $root, $page;
	// Path.
	echo "<td class=activemenuitem width='99%'>" . getConfigVar ('enterprise');
	$path = getPath ($pageno);
	foreach ($path as $no)
	{
		if (isset ($page[$no]['title']))
			$title = array
			(
				'name' => $page[$no]['title'],
				'params' => array()
			);
		else
			$title = dynamic_title_decoder ($no);
		echo ": <a href='${root}?page=${no}&tab=default";
		foreach ($title['params'] as $param_name => $param_value)
			echo "&${param_name}=${param_value}";
		echo "'>" . $title['name'] . "</a>";
	}
	echo "</td>";
	// Search form.
	echo "<td><table border=0 cellpadding=0 cellspacing=0><tr><td>Search:</td>";
	echo "<form name=search method=get action='${root}'><td>";
	echo '<input type=hidden name=page value=search>';
	// This input will be the first, if we don't add ports or addresses.
	echo "<input type=text name=q size=20 tabindex=1000></td></form></tr></table></td>";
}

function getTitle ($pageno)
{
	global $page;
	if (isset ($page[$pageno]['title']))
		return $page[$pageno]['title'];
	$tmp = dynamic_title_decoder ($pageno);
	return $tmp['name'];
}

function showTabs ($pageno, $tabno)
{
	global $tab, $root, $page, $trigger;
	if (!isset ($tab[$pageno]['default']))
		return;
	echo "<td><div class=greynavbar><ul id=foldertab style='margin-bottom: 0px; padding-top: 10px;'>";
	foreach ($tab[$pageno] as $tabidx => $tabtitle)
	{
		// Hide forbidden tabs.
		if (!permitted ($pageno, $tabidx))
			continue;
		// Dynamic tabs should only be shown in certain cases (trigger exists and returns true).
		if (!isset ($trigger[$pageno][$tabidx]))
			$tabclass = 'std';
		elseif (!strlen ($tabclass = $trigger[$pageno][$tabidx] ()))
			continue;
		if ($tabidx == $tabno)
		       $tabclass = 'current'; // override any class for an an active selection
		echo "<li><a class=${tabclass}";
		echo " href='${root}?page=${pageno}&tab=${tabidx}";
		if (isset ($page[$pageno]['bypass']) and isset ($_REQUEST[$page[$pageno]['bypass']]))
		{
			$bpname = $page[$pageno]['bypass'];
			$bpval = $_REQUEST[$bpname];
			echo "&${bpname}=${bpval}";
		}
		echo "'>${tabtitle}</a></li>\n";
	}
	echo "</ul></div></td>\n";
}

// Arg is path page number, which can be different from the primary page number,
// for example title for 'ipv4net' can be requested to build navigation path for
// both IPv4 network and IPv4 address. Another such page number is 'row', which
// fires for both row and its racks. Use pageno for decision in such cases.
function dynamic_title_decoder ($path_position)
{
	switch ($path_position)
	{
	case 'index':
		return array
		(
			'name' => '/' . getConfigVar ('enterprise'),
			'params' => array()
		);
	case 'chapter':
		assertUIntArg ('chapter_no', __FUNCTION__);
		$chapters = getChapterList();
		$chapter_no = $_REQUEST['chapter_no'];
		$chapter_name = isset ($chapters[$chapter_no]) ? $chapters[$chapter_no]['name'] : 'N/A';
		return array
		(
			'name' => "Chapter '${chapter_name}'",
			'params' => array ('chapter_no' => $chapter_no)
		);
	case 'user':
		assertUIntArg ('user_id', __FUNCTION__);
		$userinfo = spotEntity ('user', $_REQUEST['user_id']);
		return array
		(
			'name' => "Local user '" . $userinfo['user_name'] . "'",
			'params' => array ('user_id' => $_REQUEST['user_id'])
		);
	case 'ipv4rspool':
		assertUIntArg ('pool_id', __FUNCTION__);
		$poolInfo = spotEntity ('ipv4rspool', $_REQUEST['pool_id']);
		return array
		(
			'name' => empty ($poolInfo['name']) ? 'ANONYMOUS' : $poolInfo['name'],
			'params' => array ('pool_id' => $_REQUEST['pool_id'])
		);
	case 'ipv4vs':
		assertUIntArg ('vs_id', __FUNCTION__);
		$tmp = spotEntity ('ipv4vs', $_REQUEST['vs_id']);
		return array
		(
			'name' => $tmp['dname'],
			'params' => array ('vs_id' => $_REQUEST['vs_id'])
		);
	case 'object':
		assertUIntArg ('object_id', __FUNCTION__);
		$object = getObjectInfo ($_REQUEST['object_id']);
		if ($object == NULL)
			return array
			(
				'name' => __FUNCTION__ . '() failure',
				'params' => array()
			);
		return array
		(
			'name' => $object['dname'],
			'params' => array ('object_id' => $_REQUEST['object_id'])
		);
	case 'rack':
		assertUIntArg ('rack_id', __FUNCTION__);
		$rack = spotEntity ('rack', $_REQUEST['rack_id']);
		return array
		(
			'name' => $rack['name'],
			'params' => array ('rack_id' => $_REQUEST['rack_id'])
		);
	case 'search':
		if (isset ($_REQUEST['q']))
			return array
			(
				'name' => "search results for '${_REQUEST['q']}'",
				'params' => array ('q' => $_REQUEST['q'])
			);
		else
			return array
			(
				'name' => 'search results',
				'params' => array()
			);
	case 'file':
		assertUIntArg ('file_id', __FUNCTION__);
		$file = getFileInfo ($_REQUEST['file_id']);
		if ($file == NULL)
			return array
			(
				'name' => __FUNCTION__ . '() failure',
				'params' => array()
			);
		return array
		(
			'name' => htmlspecialchars ($file['name']),
			'params' => array ('file_id' => $_REQUEST['file_id'])
		);
	case 'ipaddress':
		assertIPv4Arg ('ip', __FUNCTION__);
		return array
		(
			'name' => $_REQUEST['ip'],
			'params' => array ('ip' => $_REQUEST['ip'])
		);
	case 'ipv4net':
		global $pageno;
		switch ($pageno)
		{
		case 'ipv4net':
			assertUIntArg ('id', __FUNCTION__);
			$range = spotEntity ('ipv4net', $_REQUEST['id']);
			return array
			(
				'name' => $range['ip'] . '/' . $range['mask'],
				'params' => array ('id' => $_REQUEST['id'])
			);
		case 'ipaddress':
			assertIPv4Arg ('ip', __FUNCTION__);
			$range = spotEntity ('ipv4net', getIPv4AddressNetworkId ($_REQUEST['ip']));
			return array
			(
				'name' => $range['ip'] . '/' . $range['mask'],
				'params' => array
				(
					'id' => $range['id'],
					'hl_ipv4_addr' => $_REQUEST['ip']
				)
			);
		default:
			return array
			(
				'name' => __FUNCTION__ . '() failure',
				'params' => array()
			);
		}
	case 'row':
		global $pageno;
		switch ($pageno)
		{
		case 'rack':
			assertUIntArg ('rack_id', __FUNCTION__);
			$rack = spotEntity ('rack', $_REQUEST['rack_id']);
			if ($rack == NULL)
			{
				showError ('Rack not found', __FUNCTION__);
				return NULL;
			}
			return array
			(
				'name' => $rack['row_name'],
				'params' => array ('row_id' => $rack['row_id'])
			);
		case 'row':
			assertUIntArg ('row_id', __FUNCTION__);
			$rowInfo = getRackRowInfo ($_REQUEST['row_id']);
			if ($rowInfo == NULL)
			{
				showError ('getRackRowInfo() failed', __FUNCTION__);
				return NULL;
			}
			return array
			(
				'name' => $rowInfo['name'],
				'params' => array ('row_id' => $_REQUEST['row_id'])
			);
		default:
			return array
			(
				'name' => __FUNCTION__ . '() failure',
				'params' => array()
			);
		}
	default:
		return array
		(
			'name' => __FUNCTION__ . '() failure',
			'params' => array()
		);
	}
}

?>
