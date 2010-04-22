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

$vtdecoder = array
(
	'ondemand' => '',
	'compulsory' => 'MH',
	'alien' => 'NT',
);

$vtoptions = array
(
	'ondemand' => 'on demand',
	'compulsory' => 'must have',
	'alien' => 'never touch',
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
$image['depot']['path'] = 'pix/server.png';
$image['depot']['width'] = 218;
$image['depot']['height'] = 200;
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
$image['8021q']['path'] = 'pix/8021q.png';
$image['8021q']['width'] = 218;
$image['8021q']['height'] = 200;
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
$image['Cut']['path'] = 'pix/tango-edit-cut-22x22.png';
$image['Cut']['width'] = 22;
$image['Cut']['height'] = 22;
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
$image['DELETE']['path'] = 'pix/tango-list-remove-32x32.png';
$image['DELETE']['width'] = 32;
$image['DELETE']['height'] = 32;
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
$image['save']['path'] = 'pix/tango-document-save-16x16.png';
$image['save']['width'] = 16;
$image['save']['height'] = 16;
$image['SAVE']['path'] = 'pix/tango-document-save-32x32.png';
$image['SAVE']['width'] = 32;
$image['SAVE']['height'] = 32;
$image['NOSAVE']['path'] = 'pix/tango-document-save-32x32-gray.png';
$image['NOSAVE']['width'] = 32;
$image['NOSAVE']['height'] = 32;
$image['create']['path'] = 'pix/tango-document-new.png';
$image['create']['width'] = 16;
$image['create']['height'] = 16;
$image['CREATE']['path'] = 'pix/tango-document-new-big.png';
$image['CREATE']['width'] = 32;
$image['CREATE']['height'] = 32;
$image['DENIED']['path'] = 'pix/tango-dialog-error-big.png';
$image['DENIED']['width'] = 32;
$image['DENIED']['height'] = 32;
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
$image['dragons']['width'] = 195;
$image['dragons']['height'] = 33;
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
$image['object']['path'] = 'pix/bracket-16x16.png';
$image['object']['width'] = 16;
$image['object']['height'] = 16;
$image['OBJECT']['path'] = 'pix/bracket-32x32.png';
$image['OBJECT']['width'] = 32;
$image['OBJECT']['height'] = 32;
$image['ATTACH']['path'] = 'pix/crystal-attach-32x32.png';
$image['ATTACH']['width'] = 32;
$image['ATTACH']['height'] = 32;
$image['Attach']['path'] = 'pix/crystal-attach-22x22.png';
$image['Attach']['width'] = 22;
$image['Attach']['height'] = 22;
$image['attach']['path'] = 'pix/crystal-attach-16x16.png';
$image['attach']['width'] = 16;
$image['attach']['height'] = 16;
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
$image['text']['path'] = 'pix/tango-text-x-generic-16x16.png';
$image['text']['width'] = 16;
$image['text']['height'] = 16;
$image['NET']['path'] = 'pix/crystal-network_local-32x32.png';
$image['NET']['width'] = 32;
$image['NET']['height'] = 32;
$image['net']['path'] = 'pix/crystal-network_local-16x16.png';
$image['net']['width'] = 16;
$image['net']['height'] = 16;
$image['USER']['path'] = 'pix/crystal-edit-user-32x32.png';
$image['USER']['width'] = 32;
$image['USER']['height'] = 32;
$image['setfilter']['path'] = 'pix/pgadmin3-viewfiltereddata.png';
$image['setfilter']['width'] = 32;
$image['setfilter']['height'] = 32;
$image['setfilter gray']['path'] = 'pix/pgadmin3-viewfiltereddata-grayscale.png';
$image['setfilter gray']['width'] = 32;
$image['setfilter gray']['height'] = 32;
$image['resetfilter']['path'] = 'pix/pgadmin3-viewdata.png';
$image['resetfilter']['width'] = 32;
$image['resetfilter']['height'] = 32;
$image['resetfilter gray']['path'] = 'pix/pgadmin3-viewdata-grayscale.png';
$image['resetfilter gray']['width'] = 32;
$image['resetfilter gray']['height'] = 32;
$image['knight']['path'] = 'pix/smiley_knight.png';
$image['knight']['width'] = 72;
$image['knight']['height'] = 33;
$image['UPDATEALL']['path'] = 'pix/tango-system-software-update-32x32.png';
$image['UPDATEALL']['width'] = 32;
$image['UPDATEALL']['height'] = 32;
$image['CORE']['path'] = 'pix/crystal-apps-core-32x32.png';
$image['CORE']['width'] = 32;
$image['CORE']['height'] = 32;
$image['Zoom']['path'] = 'pix/tango-system-search-22x22.png';
$image['Zoom']['width'] = 22;
$image['Zoom']['height'] = 22;
$image['Zooming']['path'] = 'pix/tango-view-fullscreen-22x22.png';
$image['Zooming']['width'] = 22;
$image['Zooming']['height'] = 22;

// This may be populated later onsite, report rendering function will use it.
// See the $systemreport for structure.
$localreports = array();

// This also can be modified in local.php.
$pageheaders = array
(
	100 => "<link rel=stylesheet type='text/css' href=pi.css />",
	200 => "<link rel=icon href='pix/racktables.ico' type='image/x-icon' />",
);

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

// Main menu.
function renderIndexItem ($ypageno) {
  global $page;
  if (permitted($ypageno)) {
    print "          <td>\n";          
    print "            <h1><a href='".makeHref(array('page'=>$ypageno))."'>".$page[$ypageno]['title']."<br>\n";
    printImageHREF ($ypageno);
    print "</a></h1>\n";
    print "          </td>\n";
  } else {
    print "          <td>&nbsp;</td>\n";
  }
}

function renderIndex ()
{
?>
<table border=0 cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		<td>
			<div style='text-align: center; margin: 10px; '>
			<table width='100%' cellspacing=0 cellpadding=20 class=mainmenu border=0>
				<tr>
<?php
renderIndexItem('rackspace');
renderIndexItem('depot');
renderIndexItem('ipv4space');
renderIndexItem('files');
renderIndexItem('8021q');
?>          
				</tr>
				<tr>
<?php
renderIndexItem('config');
renderIndexItem('reports');
renderIndexItem('ipv4slb');
print "          <td>&nbsp;</td>";
print "          <td>&nbsp;</td>";
?>          
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
				}
				echo "<td align=center><a href='".makeHref(array('page'=>'rack', 'rack_id'=>$rack['id']))."'>";
				echo "<img border=0 width=${rackwidth} height=";
				echo getRackImageHeight ($rack['height']);
				echo " title='${rack['height']} units'";
				echo "src='render_image.php?img=minirack&rack_id=${rack['id']}'>";
				echo "<br>${rack['name']}</a></td>";
				$rackListIdx++;
			}
		$order = $nextorder[$order];
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
	$rowInfo = getRackRowInfo ($row_id);
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
					$objectData = spotEntity ('object', $rackData[$i][$locidx]['object_id']);
					if (strlen ($objectData['asset_no']))
						$prefix = "<div title='${objectData['asset_no']}";
					else
						$prefix = "<div title='no asset tag";
					// Don't tell about label, if it matches common name.
					if ($objectData['name'] != $objectData['label'] and strlen ($objectData['label']))
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
	renderNewEntityTags ('rack');
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
	renderNewEntityTags ('rack');
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
	$object = spotEntity ('object', $object_id);
	startPortlet ();
	printOpFormIntro ('update');

	// static attributes
	echo '<table border=0 cellspacing=0 cellpadding=3 align=center>';
	echo "<tr><td>&nbsp;</td><th colspan=2><h2>Attributes</h2></th></tr>";
	echo "<tr><td>&nbsp;</td><th class=tdright>Type:</th><td class=tdleft>";
	printNiftySelect (cookOptgroups (readChapter (CHAP_OBJTYPE, 'o')), array ('name' => 'object_type_id'), $object['objtype_id']);
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
			if (strlen ($record['value']))
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
					$chapter = readChapter ($record['chapter_id'], 'o');
					$chapter[0] = '-- NOT SET --';
					$chapter = cookOptgroups ($chapter, $object['objtype_id'], $record['key']);
					printNiftySelect ($chapter, array ('name' => "${i}_value"), $record['key']);
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
		makeHrefProcess(array('op'=>'deleteObject', 'page'=>'depot', 'tab'=>'addmore', 'object_id'=>$object_id)).
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
	printSelect (getRackRows(), array ('name' => 'rack_row_id'), $rack['row_id']);
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

function printSelect ($optionList, $select_attrs = array(), $selected_id = NULL)
{
	echo getSelect ($optionList, $select_attrs, $selected_id);
}

// Input array keys are OPTION VALUEs and input array values are OPTION text.
function getSelect ($optionList, $select_attrs = array(), $selected_id = NULL)
{
	$ret = '';
	if (!array_key_exists ('name', $select_attrs))
		return '';
	$ret .= '<select';
	foreach ($select_attrs as $attr_name => $attr_value)
		$ret .= " ${attr_name}=${attr_value}";
	$ret .= '>';
	foreach ($optionList as $dict_key => $dict_value)
		$ret .= "<option value='${dict_key}'" . ($dict_key == $selected_id ? ' selected' : '') . ">${dict_value}</option>";
	$ret .= '</select>';
	return $ret;
}

function printNiftySelect ($groupList, $select_attrs = array(), $selected_id = NULL)
{
	echo getNiftySelect ($groupList, $select_attrs, $selected_id);
}

// Input is a cooked list of OPTGROUPs, each with own sub-list of OPTIONs in the same
// format as printSelect() expects.
function getNiftySelect ($groupList, $select_attrs, $selected_id = NULL)
{
	// special treatment for ungrouped data
	if (count ($groupList) == 1 and isset ($groupList['other']))
		return getSelect ($groupList['other'], $select_attrs, $selected_id);
	if (!array_key_exists ('name', $select_attrs))
		return '';
	$ret = '<select';
	foreach ($select_attrs as $attr_name => $attr_value)
		$ret .= " ${attr_name}=${attr_value}";
	$ret .= '>';
	foreach ($groupList as $groupname => $groupdata)
	{
		$ret .= "<optgroup label='${groupname}'>";
		foreach ($groupdata as $dict_key => $dict_value)
			$ret .= "<option value='${dict_key}'" . ($dict_key == $selected_id ? ' selected' : '') . ">${dict_value}</option>";
		$ret .= '</optgroup>';
	}
	$ret .= '</select>';
	return $ret;
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
	printTagTRs ($rackData, makeHref(array('page'=>'rackspace', 'tab'=>'default'))."&");
	if (strlen ($rackData['comment']))
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
	$info = spotEntity ('object', $object_id);
	// FIXME: employ amplifyCell() instead of calling loader functions directly
	amplifyCell ($info);
	// Main layout starts.
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${info['dname']}</h1></td></tr>\n";
	// left column with uknown number of portlets
	echo "<tr><td class=pcleft>";
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	if (strlen ($info['name']))
		echo "<tr><th width='50%' class=tdright>Common name:</th><td class=tdleft>${info['name']}</td></tr>\n";
	// FIXME: don't call spotEntity() each time, do it once in the beginning.
	elseif (considerConfiguredConstraint ($info, 'NAMEWARN_LISTSRC'))
		echo "<tr><td colspan=2 class=msg_error>Common name is missing.</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Object type:</th><td class=tdleft><a href='";
	echo makeHref (array (
		'page' => 'depot',
		'tab' => 'default',
		'cfe' => '{$typeid_' . $info['objtype_id'] . '}'
	));
	echo "'>" . decodeObjectType ($info['objtype_id'], 'o') . '</a></td></tr>';
	if (strlen ($info['asset_no']))
		echo "<tr><th width='50%' class=tdright>Asset tag:</th><td class=tdleft>${info['asset_no']}</td></tr>\n";
	// FIXME: ditto
	elseif (considerConfiguredConstraint ($info, 'ASSETWARN_LISTSRC'))
		echo "<tr><td colspan=2 class=msg_error>Asset tag is missing.</td></tr>\n";
	if (strlen ($info['label']))
		echo "<tr><th width='50%' class=tdright>Visible label:</th><td class=tdleft>${info['label']}</td></tr>\n";
	if (strlen ($info['barcode']))
		echo "<tr><th width='50%' class=tdright>Barcode:</th><td class=tdleft>${info['barcode']}</td></tr>\n";
	if ($info['has_problems'] == 'yes')
		echo "<tr><td colspan=2 class=msg_error>Has problems</td></tr>\n";
	foreach (getAttrValues ($object_id) as $record)
		if (strlen ($record['value']))
			echo "<tr><th width='50%' class=sticker>${record['name']}:</th><td class=sticker>${record['a_value']}</td></tr>\n";
	printTagTRs
	(
		$info,
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

	if (strlen ($info['comment']))
	{
		startPortlet ('Comment');
		echo '<div class=commentblock>' . string_insert_hrefs ($info['comment']) . '</div>';
		finishPortlet ();
	}

	renderFilesPortlet ('object', $object_id);

	if (count ($info['ports']))
	{
		startPortlet ('ports and links');
		$hl_port_id = 0;
		if (isset ($_REQUEST['hl_port_id']))
		{
			assertUIntArg ('hl_port_id');
			$hl_port_id = $_REQUEST['hl_port_id'];
		}
		echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>";
		echo '<tr><th class=tdleft>Local name</th><th class=tdleft>Visible label</th>';
		echo '<th class=tdleft>Interface</th><th class=tdleft>L2 address</th>';
		echo '<th class=tdcenter colspan=2>Remote object and port</th></tr>';
		foreach ($info['ports'] as $port)
		{
			echo '<tr';
			if ($hl_port_id == $port['id'])
				echo ' class=port_highlight';
			echo "><td class=tdleft>${port['name']}</td><td class=tdleft>${port['label']}</td><td class=tdleft>";
			if ($port['iif_id'] != 1)
				echo $port['iif_name'] . '/';
			echo $port['oif_name'] . "</td><td class=tdleft><tt>${port['l2address']}</tt></td>";
			if ($port['remote_object_id'])
			{
				$remote_object = spotEntity ('object', $port['remote_object_id']);
				echo "<td class=tdleft><a href='".makeHref(array('page'=>'object', 'object_id'=>$port['remote_object_id'], 'hl_port_id'=>$port['remote_id']))."'>${remote_object['dname']}</a></td>";
				echo "<td class=tdleft>${port['remote_name']}</td>";
			}
			elseif (strlen ($port['reservation_comment']))
			{
				echo "<td class=tdleft><b>Reserved:</b></td>";
				echo "<td class='tdleft rsvtext'>${port['reservation_comment']}</td>";
			}
			else
				echo '<td>&nbsp;</td><td>&nbsp;</td>';
			echo "</tr>";
		}
		echo "</table><br>";
		finishPortlet();
	}

	$alloclist = $info['ipv4'];
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
			assertIPv4Arg ('hl_ipv4_addr');
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
			if (strlen ($alloc['addrinfo']['name']))
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
				if (isset ($allocpeer['osif']) and strlen ($allocpeer['osif']))
					echo $allocpeer['osif'] . '@';
				echo $allocpeer['object_name'] . '</a>';
				$prefix = '; ';
			}
			echo "</td></tr>\n";
		}
		echo "</table><br>\n";
		finishPortlet();
	}

	$forwards = $info['nat4'];
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
				elseif (strlen ($pf['remote_addr_name']))
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

	$pools = $info['ipv4rspools'];
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

// This function renders a form for port edition.
function renderPortsForObject ($object_id)
{
	$prefs = getPortListPrefs();
	function printNewItemTR ($prefs)
	{
		printOpFormIntro ('addPort');
		echo "<tr><td>";
		printImageHREF ('add', 'add a port', TRUE);
		echo "</td><td><input type=text size=8 name=port_name tabindex=100></td>\n";
		echo "<td><input type=text name=port_label tabindex=101></td><td>";
		printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type_id', 'tabindex' => 102), $prefs['selected']);
		echo "<td><input type=text name=port_l2address tabindex=103 size=18 maxlength=24></td>\n";
		echo "<td colspan=3>&nbsp;</td><td>";
		printImageHREF ('add', 'add a port', TRUE, 104);
		echo "</td></tr></form>";
	}
	if (getConfigVar('ENABLE_MULTIPORT_FORM') == 'yes')
		startPortlet ('Ports and interfaces');
	else
		echo '<br>';
	$object = spotEntity ('object', $object_id);
	amplifyCell ($object);
	echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	echo "<tr><th>&nbsp;</th><th class=tdleft>Local name</th><th class=tdleft>Visible label</th><th class=tdleft>Interface</th><th class=tdleft>L2 address</th>";
	echo "<th class=tdcenter colspan=2>Remote object and port</th><th class=tdcenter>(Un)link or (un)reserve</th><th>&nbsp;</th></tr>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($prefs);
	foreach ($object['ports'] as $port)
	{
		printOpFormIntro ('editPort', array ('port_id' => $port['id']));
		echo "<tr><td><a href='".makeHrefProcess(array('op'=>'delPort', 'port_id'=>$port['id'], 'object_id'=>$object_id, 'port_name'=>$port['name']))."'>";
		printImageHREF ('delete', 'Unlink and Delete this port');
		echo "</a></td>\n";
		echo "<td><input type=text name=name value='${port['name']}' size=8></td>";
		echo "<td><input type=text name=label value='${port['label']}'></td>";
		if (!$port['remote_object_id'])
		{
			echo '<td>';
			if ($port['iif_id'] != 1)
				echo '<label>' . $port['iif_name'] . ' ';
			printSelect (getExistingPortTypeOptions ($port['id']), array ('name' => 'port_type_id'), $port['oif_id']);
			if ($port['iif_id'] != 1)
				echo '</label>';
			echo '</td>';
		}
		else
		{
			echo "<input type=hidden name=port_type_id value='${port['oif_id']}'><td class=tdleft>";
			if ($port['iif_id'] != 1)
				echo $port['iif_name'] . '/';
			echo "${port['oif_name']}</td>\n";
		}
		// 18 is enough to fit 6-byte MAC address in its longest form,
		// while 24 should be Ok for WWN
		echo "<td><input type=text name=l2address value='${port['l2address']}' size=18 maxlength=24></td>\n";
		if ($port['remote_object_id'])
		{
			$remote_object = spotEntity ('object', $port['remote_object_id']);
			echo "<td><a href='".makeHref(array('page'=>'object', 'object_id'=>$port['remote_object_id']))."'>${remote_object['dname']}</a></td>";
			echo "<td>${port['remote_name']}</td>";
			echo "<td class=tdcenter><a href='".
				makeHrefProcess(array(
					'op'=>'unlinkPort', 
					'port_id'=>$port['id'],
					'remote_port_id' => $port['remote_id'],
					'object_id'=>$object_id)).
			"'>";
			printImageHREF ('cut', 'Unlink this port');
			echo "</a></td>";
		}
		elseif (strlen ($port['reservation_comment']))
		{
			echo "<td><b>Reserved:</b></td>";
			echo "<td><input type=text name=reservation_comment value='${port['reservation_comment']}'></td>";
			echo "<td class=tdcenter><a href='".
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
			//echo "<td>&nbsp;</td><td>&nbsp;</td><td class=tdcenter><a href='javascript:;'";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td class=tdcenter><span";
			$helper_args = array
			(
				'port' => $port['id'],
				'in_rack' => 'n',
			);
			$popup_args = 'height=700, width=400, location=no, menubar=no, '.
				'resizable=yes, scrollbars=no, status=no, titlebar=no, toolbar=no';
			echo " ondblclick='window.open(\"" . makeHrefForHelper ('portlist', $helper_args);
			echo "\",\"findlink\",\"${popup_args}\");'";
			// end of onclick=
			$helper_args['in_rack'] = 'y';
			echo " onclick='window.open(\"" . makeHrefForHelper ('portlist', $helper_args);
			echo "\",\"findlink\",\"${popup_args}\");'";
			// end of onclick=
			echo '>';
			// end of <a>
			printImageHREF ('plug', 'Link this port');
			echo "</span>";
			echo " <input type=text name=reservation_comment></td>\n";
		}
		echo "<td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</td></form></tr>\n";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ($prefs);
	echo "</table><br>\n";
	if (getConfigVar('ENABLE_MULTIPORT_FORM') == 'yes')
		finishPortlet();
	if (getConfigVar('ENABLE_MULTIPORT_FORM') != 'yes')
		return;

	startPortlet ('Add/update multiple ports');
	printOpFormIntro ('addMultiPorts');
	echo 'Format: <select name=format tabindex=201>';
	echo '<option value=c3600asy>Cisco 3600 async: sh line | inc TTY</option>';
	echo '<option value=fiwg selected>Foundry ServerIron/FastIron WorkGroup/Edge: sh int br</option>';
	echo '<option value=fisxii>Foundry FastIron SuperX/II4000: sh int br</option>';
	echo '<option value=ssv1>SSV:&lt;interface name&gt; &lt;MAC address&gt;</option>';
	echo "</select>";
	echo 'Default port type: ';
	printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type', 'tabindex' => 202), $prefs['selected']);
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
		printSelect ($aat, array ('name' => 'bond_type', 'tabindex' => 102));
		echo "</td><td>&nbsp;</td><td>";
		printImageHREF ('add', 'allocate', TRUE, 103);
		echo "</td></tr></form>";
	}
	$focus = spotEntity ('object', $object_id);
	amplifyCell ($focus);
	global $aat;
	startPortlet ('Allocations');
	echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	echo '<tr><th>&nbsp;</th><th>OS interface</th><th>IP address</th>';
	if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
		echo '<th>network</th><th>routed by</th>';
	echo '<th>type</th><th>misc</th><th>&nbsp</th></tr>';

	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach ($focus['ipv4'] as $dottedquad => $alloc)
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
		if (strlen ($alloc['addrinfo']['name']))
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
		printSelect ($aat, array ('name' => 'bond_type'), $alloc['type']);
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
			if (isset ($allocpeer['osif']) and strlen ($allocpeer['osif']))
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
function showMessageOrError ()
{
	if (!isset ($_SESSION['log']))
		return;
	$log = $_SESSION['log'];
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
				81 => array ('code' => 'success', 'format' => "SNMP: completed '%s' work"),

// records 100~199 with fatal error messages
				100 => array ('code' => 'error', 'format' => '%s'),
				101 => array ('code' => 'error', 'format' => 'Port name cannot be empty'),
				102 => array ('code' => 'error', 'format' => "Error creating user account '%s'"),
				103 => array ('code' => 'error', 'format' => 'User not found!'),
				104 => array ('code' => 'error', 'format' => "Error updating user account '%s'"),
// ...
// ...
// ...
// ...
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
				179 => array ('code' => 'error', 'format' => 'Expired form has been declined.'),
				180 => array ('code' => 'error', 'format' => 'Error saving file, all changes lost!'),
				181 => array ('code' => 'error', 'format' => "file uploads not allowed, change 'file_uploads' parameter in php.ini"),
				182 => array ('code' => 'error', 'format' => 'SQL query failed: %s'),
				183 => array ('code' => 'error', 'format' => "Tag id '%s' does not exist."),
				184 => array ('code' => 'error', 'format' => 'Submitted form is invalid at line %u'),
				185 => array ('code' => 'error', 'format' => "Failed to add object '%s'"),
				186 => array ('code' => 'error', 'format' => 'Incomplete form has been ignored. Cheers.'),
				187 => array ('code' => 'error', 'format' => "Internal error in function '%s'"),
				188 => array ('code' => 'error', 'format' => "Fatal SNMP failure"),
				189 => array ('code' => 'error', 'format' => "Unknown OID '%s'"),
				190 => array ('code' => 'error', 'format' => "Invalid VLAN ID '%s'"),

// records 200~299 with warnings
				200 => array ('code' => 'warning', 'format' => '%s'),
				201 => array ('code' => 'warning', 'format' => 'nothing happened...'),
				202 => array ('code' => 'warning', 'format' => 'gw: %s'),
				203 => array ('code' => 'warning', 'format' => 'Port %s seems to be the first in VLAN %u at this switch.'),
				204 => array ('code' => 'warning', 'format' => 'Check uplink/downlink configuration for proper operation.'),
				205 => array ('code' => 'warning', 'format' => '%u change request(s) have been ignored'),
				206 => array ('code' => 'warning', 'format' => 'Rack is not empty'),
				207 => array ('code' => 'warning', 'format' => 'Ignored empty request'),

// records 300~399 with notices
				300 => array ('code' => 'neutral', 'format' => '%s'),

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
	unset($_SESSION['log']);
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
	$info = spotEntity ('object', $object_id);
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
				$rackData = spotEntity ('rack', $cand_id);
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
			$workingRacksData[$rack['id']] = $rack;
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

function renderDepot ()
{
	global $pageno, $nextorder;
	$cellfilter = getCellFilter();
	$objects = filterCellList (listCells ('object'), $cellfilter['expression']);

	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";

	startPortlet ('Objects (' . count ($objects) . ')');
	echo '<br><br><table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
	echo '<tr><th>Common name</th><th>Visible label</th><th>Asset tag</th><th>Barcode</th><th>Row/Rack</th></tr>';
	$order = 'odd';
	foreach ($objects as $obj)
	{
		if (isset ($_REQUEST['hl_object_id']) and $_REQUEST['hl_object_id'] == $obj['id'])
			$secondclass = 'tdleft port_highlight';
		else
			$secondclass = 'tdleft';
		echo "<tr class=row_${order} valign=top><td class='${secondclass}'><a href='".makeHref(array('page'=>'object', 'object_id'=>$obj['id']))."'><strong>${obj['dname']}</strong></a>";
		if (count ($obj['etags']))
			echo '<br><small>' . serializeTags ($obj['etags'], makeHref(array('page'=>$pageno, 'tab'=>'default')) . '&') . '</small>';
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
			throw new RealmNotFoundException($object_type);
	}
	global $dbxlink;
	$result = $dbxlink->query ($query);
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
	echo "<tr><th>timestamp</th><th>author</th><th>object</th><th>comment</th></tr>\n";
	foreach ($history as $row)
	{
		if ($row['mo_id'] == $op_id)
			$class = 'hl';
		else
			$class = "row_${order}";
		echo "<tr class=${class}><td><a href='".makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'op_id'=>$row['mo_id']))."'>${row['ctime']}</a></td>";
		echo "<td>${row['user_name']}</td><td>";
		renderCell (spotEntity ('object', $row['ro_id']));
		echo "</td><td>${row['comment']}</td>\n";
		echo "</tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table>\n";
	finishPortlet();

	echo '</td></tr></table>';
}

function renderIPv4SpaceRecords ($tree, $baseurl, $target = 0, $level = 1)
{
	$self = __FUNCTION__;
	static $vdomlist = NULL;
	if ($vdomlist == NULL and getConfigVar ('IPV4_TREE_SHOW_VLAN') == 'yes')
		$vdomlist = getVLANDomainList();
	foreach ($tree as $item)
	{
		if (getConfigVar ('IPV4_TREE_SHOW_USAGE') == 'yes')
			loadIPv4AddrList ($item); // necessary to compute router list and address counter
		else
		{
			$item['addrlist'] = array();
			$item['addrc'] = 0;
		}
		$used = $item['addrc'];
		$maxdirect = $item['addrt'];
		$maxtotal = binInvMaskFromDec ($item['mask']) + 1;
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
				renderProgressBar ($maxdirect ? $used/$maxdirect : 0);
				echo "<br><small>${used}/${maxdirect}" . ($maxdirect == $maxtotal ? '' : "/${maxtotal}") . '</small>';
			}
			else
				echo "<small>${maxdirect}</small>";
			echo "</td>";
			if (getConfigVar ('IPV4_TREE_SHOW_VLAN') == 'yes')
			{
				echo '<td class=tdleft>';
				if (count ($item['8021q']))
				{
					echo '<ul>';
					foreach ($item['8021q'] as $binding)
					{
						echo '<li><a href="' . makeHref (array ('page' => 'vlan', 'vlan_ck' => $binding['domain_id'] . '-' . $binding['vlan_id'])) . '">';
						echo $binding['vlan_id'] . '@' . niftyString ($vdomlist[$binding['domain_id']]['description'], 15) . '</a></li>';
					}
					echo '</ul>';
				}
				echo '</td>';
			}
			if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
				printRoutersTD (findRouters ($item['addrlist']), getConfigVar ('IPV4_TREE_RTR_AS_CELL'));
			echo "</tr>";
			if ($item['symbol'] == 'node-expanded' or $item['symbol'] == 'node-expanded-static')
				$self ($item['kids'], $baseurl, $target, $level + 1);
		}
		else
		{
			echo "<tr valign=top>";
			printIPv4NetInfoTDs ($item, 'tdleft sparenetwork', $level, $item['symbol']);
			echo "<td class=tdcenter>";
			if (getConfigVar ('IPV4_TREE_SHOW_USAGE') == 'yes')
			{
				renderProgressBar ($used/$maxtotal, 'sparenetwork');
				echo "<br><small>${used}/${maxtotal}</small>";
			}
			else
				echo "<small>${maxtotal}</small>";
			if (getConfigVar ('IPV4_TREE_SHOW_VLAN') == 'yes')
				echo '</td><td>&nbsp;</td>';
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
		echo "expanding ${netinfo['ip']}/${netinfo['mask']} (<a href='" .
			makeHref (array ('page' => $pageno, 'tab' => $tabno)) .
			$cellfilter['urlextra'] . "'>auto-collapse</a> / <a href='" .
			makeHref (array ('page' => $pageno, 'tab' => $tabno, 'eid' => 'ALL')) .
			$cellfilter['urlextra'] . "'>expand&nbsp;all</a>)";
	}
	echo "</h4><table class='widetable' border=0 cellpadding=5 cellspacing=0 align='center'>\n";
	echo "<tr><th>prefix</th><th>name/tags</th><th>capacity</th>";
	if (getConfigVar ('IPV4_TREE_SHOW_VLAN') == 'yes')
		echo '<th>VLAN</th>';
	if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
		echo "<th>routed by</th>";
	echo "</tr>\n";
	$baseurl = makeHref(array('page'=>$pageno, 'tab'=>$tabno)) . $cellfilter['urlextra'];
	renderIPv4SpaceRecords ($tree, $baseurl, $eid);
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
				$oi = spotEntity ('object', $lb_object_id);
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

	function printNewItemTR ()
	{
		startPortlet ('Add new');
		echo '<table border=0 cellpadding=10 align=center>';
		// This form requires a name, so JavaScript validator can find it.
		// No printOpFormIntro() hence
		echo "<form method=post name='add_new_range' action='".makeHrefProcess()."'>\n";
		echo "<input type=hidden name=op value=addIPv4Prefix>\n";
		// tags column
		echo '<tr><td rowspan=4><h3>assign tags</h3>';
		renderNewEntityTags ('ipv4net');
		echo '</td>';
		// inputs column
		$prefix_value = empty ($_REQUEST['set-prefix']) ? '' : $_REQUEST['set-prefix'];
		echo "<th class=tdright>prefix</th><td class=tdleft><input type=text name='range' size=18 class='live-validate' tabindex=1 value='${prefix_value}'></td>";
		echo "<tr><th class=tdright>name</th><td class=tdleft><input type=text name='name' size='20' tabindex=2></td></tr>";
		echo "<tr><th class=tdright>connected network</th><td class=tdleft><input type=checkbox name='is_bcast' tabindex=3></td></tr>";
		echo "<tr><td colspan=2>";
		printImageHREF ('CREATE', 'Add a new network', TRUE, 4);
		echo '</td></tr>';
		echo "</form></table><br><br>\n";
		finishPortlet();
	}

	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	if (count ($addrspaceList = listCells ('ipv4net')))
	{
		startPortlet ('Manage existing (' . count ($addrspaceList) . ')');
		echo "<table class='widetable' border=0 cellpadding=5 cellspacing=0 align='center'>\n";
		echo "<tr><th>&nbsp;</th><th>prefix</th><th>name</th><th>capacity</th></tr>";
		if (getConfigVar ('IPV4_JAYWALK') != 'yes')
		{
			array_walk ($addrspaceList, 'amplifyCell');
			$tree = prepareIPv4Tree ($addrspaceList, 'ALL');
			// this is only called for having "trace" set
			treeFromList ($addrspaceList);
		}
		foreach ($addrspaceList as $netinfo)
		{
			$netinfo = peekNode ($tree, $netinfo['trace'], $netinfo['id']);
			// now we have all subnets listed in netinfo
			loadIPv4AddrList ($netinfo);
			$used = $netinfo['addrc'];
			$maxdirect = $netinfo['addrt'];
			$maxtotal = binInvMaskFromDec ($netinfo['mask']) + 1;
			echo "<tr valign=top><td>";
			if (getConfigVar ('IPV4_JAYWALK') == 'yes')
			{
				echo "<a href='".makeHrefProcess(array('op'=>'delIPv4Prefix', 'id'=>$netinfo['id']))."'>";
				printImageHREF ('destroy', 'Delete this prefix');
				echo "</a>";
			}
			else // only render clickable image for empty networks
			{
				if (count ($netinfo['addrlist']))
					printImageHREF ('nodestroy', 'There are ' . count ($netinfo['addrlist']) . ' allocations inside');
				else
				{
					echo "<a href='".makeHrefProcess(array('op'=>'delIPv4Prefix', 'id'=>$netinfo['id']))."'>";
					printImageHREF ('destroy', 'Delete this prefix');
					echo "</a>";
				}

			}
			echo '</td><td class=tdleft><a href="' . makeHref (array ('page' => 'ipv4net', 'id' => $netinfo['id'])) . '">';
			echo "${netinfo['ip']}/${netinfo['mask']}</a></td>";
			echo '<td class=tdleft>' . htmlspecialchars ($netinfo['name']) . '</td><td>';
			renderProgressBar ($maxdirect ? $used/$maxdirect : 0);
			echo "<br><small>${used}/${maxdirect}" . ($maxdirect == $maxtotal ? '' : "/${maxtotal}") . '</small></td>';
			echo '</tr>';
		}
		echo "</table>";
		finishPortlet();
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
}

function renderIPv4Network ($id)
{
	global $pageno, $tabno, $aac2, $netmaskbylen, $wildcardbylen;
	$maxperpage = getConfigVar ('IPV4_ADDRS_PER_PAGE');
	if (isset($_REQUEST['pg']))
		$page = $_REQUEST['pg'];
	else
		$page=0;

	$range = spotEntity ('ipv4net', $id);
	amplifyCell ($range);
	loadIPv4AddrList ($range);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${range['ip']}/${range['mask']}</h1><h2>";
	echo htmlspecialchars ($range['name'], ENT_QUOTES, 'UTF-8') . "</h2></td></tr>\n";

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

	foreach ($range['8021q'] as $item)
	{
		$vlaninfo = getVLANInfo ($item['domain_id'] . '-' . $item['vlan_id']);
		echo '<tr><th width="50%" class=tdright>VLAN:</th><td class=tdleft><a href="';
		echo makeHref (array ('page' => 'vlan', 'vlan_ck' => $vlaninfo['vlan_ck'])) . '">';
		echo formatVLANName ($vlaninfo) . ' @' . niftyString ($vlaninfo['domain_descr']);
		echo '</a></td></tr>';
	}
	if (getConfigVar ('EXT_IPV4_VIEW') == 'yes' and count ($routers = findRouters ($range['addrlist'])))
	{
		echo "<tr><th width='50%' class=tdright>Routed by:</th>";
		printRoutersTD ($routers);
		echo "</tr>\n";
	}

	printTagTRs ($range, makeHref(array('page'=>'ipv4space', 'tab'=>'default'))."&");
	echo "</table><br>\n";
	finishPortlet();

	if (strlen ($range['comment']))
	{
		startPortlet ('Comment');
		echo '<div class=commentblock>' . string_insert_hrefs (htmlspecialchars ($range['comment'], ENT_QUOTES, 'UTF-8')) . '</div>';
		finishPortlet ();
	}

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
			echo $ref['name'] . (!strlen ($ref['name']) ? '' : '@');
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
	echo '<tr><td class=tdright><label for=nameinput>Name:</label></td>';
	echo "<td class=tdleft><input type=text name=name id=nameinput size=80 maxlength=255 value='";
	echo htmlspecialchars ($netdata['name'], ENT_QUOTES, 'UTF-8') . "'></tr>";
	echo '<tr><td class=tdright><label for=commentinput>Comment:</label></td>';
	echo "<td class=tdleft><textarea name=comment id=commentinput cols=80 rows=25>\n";
	echo htmlspecialchars ($netdata['comment'], ENT_QUOTES, 'UTF-8') . "</textarea></tr>";
	echo "<tr><td colspan=2 class=tdcenter>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></form></tr></table>\n";
}

function renderIPv4Address ($dottedquad)
{
	global $aat, $nextorder;
	$address = getIPv4Address ($dottedquad);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${dottedquad}</h1></td></tr>\n";

	echo "<tr><td class=pcleft>";
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	if (strlen ($address['name']))
		echo "<tr><th width='50%' class=tdright>Comment:</th><td class=tdleft>${address['name']}</td></tr>";
	echo "<tr><th width='50%' class=tdright>Allocations:</th><td class=tdleft>" . count ($address['allocs']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Originated NAT connections:</th><td class=tdleft>" . count ($address['outpf']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Arriving NAT connections:</th><td class=tdleft>" . count ($address['inpf']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>SLB virtual services:</th><td class=tdleft>" . count ($address['lblist']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>SLB real servers:</th><td class=tdleft>" . count ($address['rslist']) . "</td></tr>\n";
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
		echo "<tr><th>VS</th><th>LB</th></tr>\n";
		$order = 'odd';
		foreach ($address['lblist'] as $vsinfo)
		{
			echo "<tr valign=top class=row_${order}><td class=tdleft>";
			renderCell (spotEntity ('ipv4vs', $vsinfo['vs_id']));
			echo "</td><td class=tdleft>";
			renderLBCell ($vsinfo['object_id']);
			echo "</td></tr>";
			$order = $nextorder[$order];
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
	if (!strlen ($address['name']) and $address['reserved'] == 'no')
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
		printSelect (getNarrowObjectList ('IPV4OBJ_LISTSRC'), array ('name' => 'object_id', 'tabindex' => 100));
		echo "</td><td><input type=text tabindex=101 name=bond_name size=10></td><td>";
		printSelect ($aat, array ('name' => 'bond_type', 'tabindex' => 102));
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
			printSelect ($aat, array ('name' => 'bond_type'), $bond['type']);
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
		printSelect (array ('TCP' => 'TCP', 'UDP' => 'UDP'), array ('name' => 'proto'));
		echo "<select name='localip' tabindex=1>";

		foreach ($alloclist as $dottedquad => $alloc)
		{
			$name = (!isset ($alloc['addrinfo']['name']) or !strlen ($alloc['addrinfo']['name'])) ? '' : (' (' . niftyString ($alloc['addrinfo']['name']) . ')');
			$osif = (!isset ($alloc['osif']) or !strlen ($alloc['osif'])) ? '' : ($alloc['osif'] . ': ');
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
	
	$focus = spotEntity ('object', $object_id);
	amplifyCell ($focus);
	echo "<center><h2>locally performed NAT</h2></center>";

	echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>\n";
	echo "<tr><th></th><th>Match endpoint</th><th>Translate to</th><th>Target object</th><th>Comment</th><th>&nbsp;</th></tr>\n";

	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($focus['ipv4']);
	foreach ($focus['nat4']['out'] as $pf)
	{
		$class = 'trerror';
		$osif = '';
		if (isset ($focus['ipv4'][$pf['localip']]))
		{
			$class = $focus['ipv4'][$pf['localip']]['addrinfo']['class'];
			$osif = $focus['ipv4'][$pf['localip']]['osif'] . ': ';
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
		if (strlen ($pf['local_addr_name']))
			echo ' (' . $pf['local_addr_name'] . ')';
		echo "</td>";
		echo "<td><a href='".makeHref(array('page'=>'ipaddress', 'tab'=>'default', 'ip'=>$pf['remoteip']))."'>${pf['remoteip']}</a>:${pf['remoteport']}</td>";

		$address = getIPv4Address ($pf['remoteip']);

		echo "<td class='description'>";
		if (count ($address['allocs']))
			foreach ($address['allocs'] as $bond)
				echo "<a href='".makeHref(array('page'=>'object', 'tab'=>'default', 'object_id'=>$bond['object_id']))."'>${bond['object_name']}(${bond['name']})</a> ";
		elseif (strlen ($pf['remote_addr_name']))
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
		printNewItemTR ($focus['ipv4']);

	echo "</table><br><br>";
	if (!count ($focus['nat4']))
		return;

	echo "<center><h2>arriving NAT connections</h2></center>";
	echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>\n";
	echo "<tr><th></th><th>Source</th><th>Source objects</th><th>Target</th><th>Description</th></tr>\n";

	foreach ($focus['nat4']['in'] as $pf)
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
	$typelist = readChapter (CHAP_OBJTYPE, 'o');
	$typelist[0] = 'select type...';
	$typelist = cookOptgroups ($typelist);
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
		printNiftySelect ($typelist, array ('name' => "${i}_object_type_id", 'tabindex' => $tabindex), 0);
		echo '</td>';
		echo "<td><input type=text size=30 name=${i}_object_name tabindex=${tabindex}></td>";
		echo "<td><input type=text size=30 name=${i}_object_label tabindex=${tabindex}></td>";
		echo "<td><input type=text size=20 name=${i}_object_asset_no tabindex=${tabindex}></td>";
		echo "<td><input type=text size=10 name=${i}_object_barcode tabindex=${tabindex}></td>";
		if ($i == 0)
		{
			echo "<td valign=top rowspan=${max}>";
			renderNewEntityTags ('object');
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
	printNiftySelect ($typelist, array ('name' => 'global_type_id'), getConfigVar ('DEFAULT_OBJECT_TYPE'));
	echo "</td></tr>";
	echo "<tr><th>Tags</th></tr>";
	echo "<tr><td valign=top>";
	renderNewEntityTags ('object');
	echo "</td></tr>";
	echo "<tr><td colspan=2><input type=submit name=got_very_fast_data value='Go!'></td></tr></table>\n";
	echo "</form>\n";
	finishPortlet();
}

function printGreeting ()
{
	global $remote_username, $remote_displayname;
	echo "Hello, <a href='index.php?page=myaccount&tab=default'>${remote_displayname}</a>. This is RackTables " .
		CODE_VERSION .
		". Click <a href='index.php?logout'>here</a> to logout.";
}

function renderSearchResults ()
{
	$terms = trim ($_REQUEST['q']);
	if (!strlen ($terms))
	{
		throw new InvalidRequestArgException('q', $_REQUEST['q'], 'Search string cannot be empty.');
		return;
	}
	if (!permitted ('depot', 'default'))
	{
		throw new NotAuthorizedException('You are not authorized for viewing information about objects.');
	}
	$nhits = 0;
	if (preg_match (RE_IP4_ADDR, $terms))
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
			case 'ipv4addressbydq':
				$parentnet = getIPv4AddressNetworkId ($record);
				if ($parentnet !== NULL)
					echo "<script language='Javascript'>document.location='index.php?page=ipv4net&tab=default&id=${parentnet}&hl_ipv4_addr=${record}';//</script>";
				else
					echo "<script language='Javascript'>document.location='index.php?page=ipaddress&ip=${record}';//</script>";
				break;
			case 'ipv4addressbydescr':
				$parentnet = getIPv4AddressNetworkId ($record['ip']);
				if ($parentnet !== NULL)
					echo "<script language='Javascript'>document.location='index.php?page=ipv4net&tab=default&id=${parentnet}&hl_ipv4_addr=${record['ip']}';//</script>";
				else
					echo "<script language='Javascript'>document.location='index.php?page=ipaddress&ip=${record['ip']}';//</script>";
				break;
			case 'ipv4network':
				echo "<script language='Javascript'>document.location='index.php?page=ipv4net";
				echo "&id=${record['id']}";
				echo "';//</script>";
				break;
			case 'object':
				if (isset ($record['by_port']) and 1 == count ($record['by_port']))
					$hl = '&hl_port_id=' . key ($record['by_port']);
				else
					$hl = '';
				echo "<script language='Javascript'>document.location='index.php?page=object&object_id=${record['id']}${hl}';//</script>";
				break;
			case 'ipv4rspool':
				echo "<script language='Javascript'>document.location='index.php?page=ipv4rspool&pool_id=${record['id']}';//</script>";
				break;
			case 'ipv4vs':
				echo "<script language='Javascript'>document.location='index.php?page=ipv4vs&vs_id=${record['id']}';//</script>";
				break;
			case 'user':
				echo "<script language='Javascript'>document.location='index.php?page=user&user_id=${record['user_id']}';//</script>";
				break;
			case 'file':
				echo "<script language='Javascript'>document.location='index.php?page=file&file_id=${record['id']}';//</script>";
				break;
			case 'rack':
				echo "<script language='Javascript'>document.location='index.php?page=rack&rack_id=${record['id']}';//</script>";
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
					startPortlet ("<a href='index.php?page=depot'>Objects</a>");
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					echo '<tr><th>what</th><th>why</th></tr>';
					foreach ($what as $obj)
					{
						echo "<tr class=row_${order} valign=top><td>";
						$object = spotEntity ('object', $obj['id']);
						renderCell ($object);
						echo "</td><td class=tdleft>";
						if (isset ($obj['by_attr']))
						{
							// only explain non-obvious reasons for listing
							echo '<ul>';
							foreach ($obj['by_attr'] as $attr_name)
								if ($attr_name != 'name')
									echo "<li>${attr_name} matched</li>";
							echo '</ul>';
						}
						if (isset ($obj['by_sticker']))
						{
							echo '<table>';
							$aval = getAttrValues ($obj['id']);
							foreach ($obj['by_sticker'] as $attr_id)
							{
								$record = array
								(
									'name' => $aval[$attr_id]['name'],
									'a_value' => $aval[$attr_id]['a_value']
								);
								echo "<tr><th width='50%' class=sticker>${record['name']}:</th>";
								echo "<td class=sticker>${record['a_value']}</td></tr>";
							}
							echo '</table>';
						}
						if (isset ($obj['by_port']))
						{
							echo '<table>';
							amplifyCell ($object);
							foreach ($obj['by_port'] as $port_id => $text)
								foreach ($object['ports'] as $port)
									if ($port['id'] == $port_id)
									{
										echo "<tr><td>port ${port['name']}:</td>";
										echo "<td class=tdleft>${text}</td></tr>";
										break; // next reason
									}
							echo '</table>';
						}
						if (isset ($obj['by_iface']))
						{
							echo '<ul>';
							foreach ($obj['by_iface'] as $ifname)
								echo "<li>interface ${ifname}</li>";
							echo '</ul>';
						}
						if (isset ($obj['by_nat']))
						{
							echo '<ul>';
							foreach ($obj['by_nat'] as $comment)
								echo "<li>NAT rule: ${comment}</li>";
							echo '</ul>';
						}
						echo "</td></tr>";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
				case 'ipv4network':
					startPortlet ("<a href='index.php?page=ipv4space'>IPv4 networks</a>");
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
							echo "<a href='index.php?page=ipv4net&tab=default&id=${parentnet}&hl_ipv4_addr=${addr['ip']}'>${addr['ip']}</a></td>";
						else
							echo "<a href='index.php?page=ipaddress&ip=${addr['ip']}'>${addr['ip']}</a></td>";
						echo "<td class=tdleft>${addr['name']}</td></tr>";
						$order = $nextorder[$order];
					}
					echo '</table>';
					finishPortlet();
					break;
				case 'ipv4rspool':
					startPortlet ("<a href='index.php?page=ipv4rsplist'>RS pools</a>");
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
					startPortlet ("<a href='index.php?page=ipv4vslist'>Virtual services</a>");
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
					startPortlet ("<a href='index.php?page=userlist'>Users</a>");
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
					startPortlet ("<a href='index.php?page=files'>Files</a>");
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
					startPortlet ("<a href='index.php?page=rackspace'>Racks</a>");
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
		startPortlet ('Add new');
		printOpFormIntro ('createUser');
		echo '<table cellspacing=0 cellpadding=5 align=center>';
		echo '<tr><th>&nbsp;</th><th>&nbsp;</th><th>Assign tags</th></tr>';
		echo '<tr><th class=tdright>Username</th><td class=tdleft><input type=text size=64 name=username tabindex=100></td>';
		echo '<td rowspan=4>';
		renderNewEntityTags ('user');
		echo '</td></tr>';
		echo '<tr><th class=tdright>Real name</th><td class=tdleft><input type=text size=64 name=realname tabindex=101></td></tr>';
		echo '<tr><th class=tdright>Password</th><td class=tdleft><input type=password size=64 name=password tabindex=102></td></tr>';
		echo '<tr><td colspan=2>';
		printImageHREF ('CREATE', 'Add new account', TRUE, 103);
		echo '</td></tr>';
		echo '</table></form>';
		finishPortlet();
	}
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	$accounts = listCells ('user');
	startPortlet ('Manage existing (' . count ($accounts) . ')');
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>Username</th><th>Real name</th><th>Password</th><th>&nbsp;</th></tr>';
	foreach ($accounts as $account)
	{
		printOpFormIntro ('updateUser', array ('user_id' => $account['user_id']));
		echo "<tr><td><input type=text name=username value='${account['user_name']}' size=16></td>";
		echo "<td><input type=text name=realname value='${account['user_realname']}' size=24></td>";
		echo "<td><input type=password name=password value='${account['user_password_hash']}' size=40></td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo '</td></form></tr>';
	}
	echo '</table><br>';
	finishPortlet();
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
}

function renderPortOIFCompatViewer()
{
	global $nextorder;
	$order = 'odd';
	$last_left_oif_id = NULL;
	echo '<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>';
	echo '<tr><th>From interface</th><th>To interface</th></tr>';
	foreach (getPortOIFCompat() as $pair)
	{
		if ($last_left_oif_id != $pair['type1'])
		{
			$order = $nextorder[$order];
			$last_left_oif_id = $pair['type1'];
		}
		echo "<tr class=row_${order}><td>${pair['type1name']}</td><td>${pair['type2name']}</td></tr>";
	}
	echo '</table>';
}

function renderPortOIFCompatEditor()
{
	function printNewitemTR()
	{
		printOpFormIntro ('add');
		echo '<tr><th class=tdleft>';
		printImageHREF ('add', 'add pair', TRUE);
		echo '</th><th class=tdleft>';
		printSelect (readChapter (CHAP_PORTTYPE), array ('name' => 'type1'));
		echo '</th><th class=tdleft>';
		printSelect (readChapter (CHAP_PORTTYPE), array ('name' => 'type2'));
		echo '</th></tr></form>';
	}

	global $nextorder;
	$last_left_oif_id = NULL;
	$order = 'odd';
	echo '<br><table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>';
	echo '<tr><th>&nbsp;</th><th>From Interface</th><th>To Interface</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewitemTR();
	foreach (getPortOIFCompat() as $pair)
	{
		if ($last_left_oif_id != $pair['type1'])
		{
			$order = $nextorder[$order];
			$last_left_oif_id = $pair['type1'];
		}
		echo "<tr class=row_${order}><td>";
		echo '<a href="' . makeHrefProcess (array ('op' => 'del', 'type1' => $pair['type1'], 'type2' => $pair['type2'])) . '">';
		printImageHREF ('delete', 'remove pair', TRUE);
		echo "</a></td><td class=tdleft>${pair['type1name']}</td><td class=tdleft>${pair['type2name']}</td></tr>";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewitemTR();
	echo '</table>';
}

// Find direct sub-pages and dump as a list.
// FIXME: assume all config kids to have static titles at the moment,
// but use some proper abstract function later.
function renderConfigMainpage ()
{
	global $pageno, $page;
	echo '<ul>';
	foreach ($page as $cpageno => $cpage)
		if (isset ($cpage['parent']) and $cpage['parent'] == $pageno  && permitted($cpageno))
			echo "<li><a href='index.php?page=${cpageno}'>" . $cpage['title'] . "</li>\n";
	echo '</ul>';
}

function renderRackPage ($rack_id)
{
	$rackData = spotEntity ('rack', $rack_id);
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
	global $nextorder, $max_dict_key;
	$words = readChapter ($tgt_chapter_no, 'a');
	$wc = count ($words);
	if (!$wc)
	{
		echo "<center><h2>(no records)</h2></center>";
		return;
	}
	$refcnt = getChapterRefc ($tgt_chapter_no, array_keys ($words));
	echo "<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	echo "<tr><th colspan=3>${wc} record(s)</th></tr>\n";
	echo "<tr><th>Origin</th><th>Refcnt</th><th>Word</th></tr>\n";
	$order = 'odd';
	foreach ($words as $key => $value)
	{
		echo "<tr class=row_${order}><td>";
		printImageHREF (($key <= $max_dict_key[CODE_VERSION]) ? 'computer' : 'favorite');
		echo '</td><td>';
		if ($refcnt[$key])
			echo $refcnt[$key];
		echo "</td><td><div title='key=${key}'>${value}</div></td></tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table>\n<br>";
}

function renderChapterEditor ($tgt_chapter_no)
{
	global $nextorder, $max_dict_key;
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
	echo "<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	$words = readChapter ($tgt_chapter_no);
	$refcnt = getChapterRefc ($tgt_chapter_no, array_keys ($words));
	$order = 'odd';
	echo "<tr><th>Origin</th><th>&nbsp;</th><th>Word</th><th>&nbsp;</th></tr>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach ($words as $key => $value)
	{
		echo "<tr class=row_${order}><td>";
		$order = $nextorder[$order];
		// Show plain row for stock records, render a form for user's ones.
		if ($key <= $max_dict_key[CODE_VERSION])
		{
			printImageHREF ('computer');
			echo "</td><td>&nbsp;</td><td>${value}</td><td>&nbsp;</td></tr>";
			continue;
		}
		printOpFormIntro ('upd', array ('dict_key' => $key));
		printImageHREF ('favorite');
		echo "</td><td>";
		// Prevent deleting words currently used somewhere.
		if ($refcnt[$key])
			printImageHREF ('nodelete', 'referenced ' . $refcnt[$key] . ' time(s)');
		else
		{
			echo "<a href='".makeHrefProcess(array('op'=>'del', 'chapter_no'=>$tgt_chapter_no, 'dict_key'=>$key))."'>";
			printImageHREF ('delete', 'Delete word');
			echo "</a>";
		}
		echo '</td>';
		echo "<td class=tdright><input type=text name=dict_value size=64 value='${value}'></td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</td></tr></form>";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo "</table>\n";
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
	startPortlet ('Optional attributes');
	echo "<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>";
	echo "<tr><th class=tdleft>Attribute name</th><th class=tdleft>Attribute type</th><th class=tdleft>Applies to</th></tr>";
	$order = 'odd';
	foreach (getAttrMap() as $attr)
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
					echo decodeObjectType ($app['objtype_id'], 'a') . " (values from '${app['chapter_name']}')<br>";
				else
					echo decodeObjectType ($app['objtype_id'], 'a') . '<br>';
		echo '</td></tr>';
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
		printImageHREF ('create', 'Create attribute', TRUE);
		echo "</td><td><input type=text tabindex=100 name=attr_name></td><td>";
		global $attrtypes;
		printSelect ($attrtypes, array ('name' => 'attr_type', 'tabindex' => 101));
		echo '</td><td>';
		printImageHREF ('add', 'Create attribute', TRUE, 102);
		echo '</td></tr></form>';
	}
	startPortlet ('Optional attributes');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo '<tr><th>&nbsp;</th><th>Name</th><th>Type</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach (getAttrMap() as $attr)
	{
		printOpFormIntro ('upd', array ('attr_id' => $attr['id']));
		echo '<tr><td>';
		if (count ($attr['application']))
			printImageHREF ('nodestroy', count ($attr['application']) . ' reference(s) in attribute map');
		else
		{
			echo "<a href='".makeHrefProcess(array('op'=>'del', 'attr_id'=>$attr['id']))."'>";
			printImageHREF ('destroy', 'Remove attribute');
			echo '</a>';
		}
		echo "</td><td><input type=text name=attr_name value='${attr['name']}'></td>";
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
		echo '<tr><td colspan=2 class=tdleft>';
		echo '<select name=attr_id tabindex=100>';
		$shortType['uint'] = 'U';
		$shortType['float'] = 'F';
		$shortType['string'] = 'S';
		$shortType['dict'] = 'D';
		foreach ($attrMap as $attr)
			echo "<option value=${attr['id']}>[" . $shortType[$attr['type']] . "] ${attr['name']}</option>";
		echo "</select></td><td class=tdleft>";
		printImageHREF ('add', '', TRUE);
		echo ' ';
		printNiftySelect (cookOptgroups (readChapter (CHAP_OBJTYPE, 'o')), array ('name' => 'objtype_id', 'tabindex' => 101));
		echo ' <select name=chapter_no tabindex=102><option value=0>-- dictionary chapter for [D] attributes --</option>';
		foreach (getChapterList() as $chapter)
			if ($chapter['sticky'] != 'yes')
				echo "<option value='${chapter['id']}'>${chapter['name']}</option>";
		echo '</select></td></tr></form>';
	}
	global $attrtypes, $nextorder;
	$order = 'odd';
	$attrMap = getAttrMap();
	startPortlet ('Attribute map');
	echo "<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>";
	echo '<tr><th class=tdleft>Attribute name</th><th class=tdleft>Attribute type</th><th class=tdleft>Applies to</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($attrMap);
	foreach ($attrMap as $attr)
	{
		if (!count ($attr['application']))
			continue;
		echo "<tr class=row_${order}><td class=tdleft>${attr['name']}</td>";
		echo "<td class=tdleft>" . $attrtypes[$attr['type']] . "</td><td colspan=2 class=tdleft>";
		foreach ($attr['application'] as $app)
		{
			if ($app['refcnt'])
				printImageHREF ('nodelete', $app['refcnt'] . ' value(s) stored for objects');
			else
			{
				echo "<a href='".makeHrefProcess(array('op'=>'del', 'attr_id'=>$attr['id'], 'objtype_id'=>$app['objtype_id']))."'>";
				printImageHREF ('delete', 'Remove mapping');
				echo "</a>";
			}
			echo ' ';
			if ($attr['type'] == 'dict')
				echo decodeObjectType ($app['objtype_id'], 'o') . " (values from '${app['chapter_name']}')<br>";
			else
				echo decodeObjectType ($app['objtype_id'], 'o') . '<br>';
		}
		echo "</td></tr>";
		$order = $nextorder[$order];
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
	global $image;
	if (!isset ($image[$tag]))
		$tag = 'error';
	$img = $image[$tag];
	if ($do_input == TRUE)
		return
			"<input type=image name=submit class=icon " .
			"src='${img['path']}' " .
			"border=0 " .
			($tabindex ? "tabindex=${tabindex}" : '') .
			(!strlen ($title) ? '' : " title='${title}'") . // JT: Add title to input hrefs too
			">";
	else
		return
			"<img " .
			"src='${img['path']}' " .
			"width=${img['width']} " .
			"height=${img['height']} " .
			"border=0 " .
			(!strlen ($title) ? '' : "title='${title}'") .
			">";
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
	);
	renderReports ($tmp);
}

function renderPortsReport ()
{
	$tmp = array();
	foreach (getPortIIFOptions() as $iif_id => $iif_name)
		if (count (getPortIIFStats (array ($iif_id))))
			$tmp[] = array
			(
				'title' => $iif_name,
				'type' => 'meters',
				'func' => 'getPortIIFStats',
				'args' => array ($iif_id),
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
				throw new RuntimeException ('Internal data error');
		}
		echo "<tr><td colspan=2><hr></td></tr>\n";
	}
	echo "</table>\n";
}

function renderTagStats ()
{
	global $taglist;
	echo '<table border=1><tr><th>tag</th><th>total</th><th>objects</th><th>IPv4 nets</th><th>racks</th>';
	echo '<th>IPv4 VS</th><th>IPv4 RS pools</th><th>users</th><th>files</th></tr>';
	$pagebyrealm = array
	(
		'file' => 'files&tab=default',
		'ipv4net' => 'ipv4space&tab=default',
		'ipv4vs' => 'ipv4vslist&tab=default',
		'ipv4rspool' => 'ipv4rsplist&tab=default',
		'object' => 'depot&tab=default',
		'rack' => 'rackspace&tab=default',
		'user' => 'userlist&tab=default'
	);
	foreach (getTagChart (getConfigVar ('TAGS_TOPLIST_SIZE')) as $taginfo)
	{
		echo "<tr><td>${taginfo['tag']}</td><td>" . $taginfo['refcnt']['total'] . "</td>";
		foreach (array ('object', 'ipv4net', 'rack', 'ipv4vs', 'ipv4rspool', 'user', 'file') as $realm)
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
	list ($vlanlist, $portlist, $maclist) = $data;
	$vlanpermissions = array();
	foreach ($portlist as $port)
	{
		if (array_key_exists ($port['vlanid'], $vlanpermissions))
			continue;
		$vlanpermissions[$port['vlanid']] = array();
		foreach (array_keys ($vlanlist) as $to)
		{
			$annex = array();
			$annex[] = array ('tag' => '$fromvlan_' . $port['vlanid']);
			$annex[] = array ('tag' => '$tovlan_' . $to);
			if (permitted (NULL, NULL, 'setPortVLAN', $annex))
				$vlanpermissions[$port['vlanid']][] = $to;
		}
	}

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
		elseif (!array_key_exists ($port['vlanid'], $vlanpermissions) or !count ($vlanpermissions[$port['vlanid']]))
		{
			echo "<input type=hidden name=vlanid_${portno} value=${port['vlanid']}>";
			echo "<select disabled name=vlanid_${portno}>";
			echo "<option value=${port['vlanid']} selected>${port['vlanid']}</option>";
			echo "</select>";
		}
		else
		{
			echo "<select name=vlanid_${portno}>";
			// A port may belong to a VLAN, which is absent from the VLAN table, this is normal.
			// We must be able to render its SELECT properly at least.
			$in_table = FALSE;
			foreach ($vlanpermissions[$port['vlanid']] as $v)
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
		$snmpcomm = getConfigVar('DEFAULT_SNMP_COMMUNITY');
		if (empty($snmpcomm))
			$snmpcomm = 'public';

		echo "<p align=center>
This object has no ports listed, that's why you see this form. If you supply a SNMP community,
I can try to automatically harvest the data. As soon as at least one port is added,
this tab will not be seen any more. Good luck.<br>\n";
		echo "<input type=text name=community value='" . $snmpcomm . "'>\n";
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
	if (strlen ($vsinfo['name']))
		echo "<tr><td colspan=2 align=center><h1>${vsinfo['name']}</h1></td></tr>\n";
	echo '<tr>';

	echo '<td class=pcleft>';
	startPortlet ('Frontend');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	if (strlen ($vsinfo['name']))
		echo "<tr><th width='50%' class=tdright>Name:</th><td class=tdleft>${vsinfo['name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Protocol:</th><td class=tdleft>${vsinfo['proto']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Virtual IP address:</th><td class=tdleft><a href='".makeHref(array('page'=>'ipaddress', 'tab'=>'default', 'ip'=>$vsinfo['vip']))."'>${vsinfo['vip']}</a></td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Virtual port:</th><td class=tdleft>${vsinfo['vport']}</td></tr>\n";
	printTagTRs ($vsinfo, makeHref(array('page'=>'ipv4vslist', 'tab'=>'default'))."&");
	if (strlen ($vsinfo['vsconfig']))
	{
		echo "<tr><th class=slbconf>VS configuration:</th><td>&nbsp;</td></tr>";
		echo "<tr><td colspan=2 class='dashed slbconf'>${vsinfo['vsconfig']}</td></tr>\n";
	}
	if (strlen ($vsinfo['rsconfig']))
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
		if (strlen ($poolInfo['vsconfig']))
			echo "<tr><th>VS config</th><td class='dashed slbconf'>${poolInfo['vsconfig']}</td></tr>";
		if (strlen ($poolInfo['rsconfig']))
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
				if (strlen ($lbInfo['vsconfig']))
					echo "<tr><th>VS config</th><td class='dashed slbconf'>${lbInfo['vsconfig']}</td></tr>";
				if (strlen ($lbInfo['rsconfig']))
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
	$done = ((int) ($percentage * 100));
	echo "<img width=100 height=10 border=0 title='${done}%' src='render_image.php?img=progressbar&done=${done}";
	echo (!strlen ($theme) ? '' : "&theme=${theme}") . "'>";
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
	printSelect ($formats, array ('name' => 'format'), 'ssv_1');
	echo "</td><td><input type=submit value=Parse></td></tr>\n";
	echo "<tr><td colspan=3><textarea name=rawtext cols=100 rows=50></textarea></td></tr>\n";
	echo "</table>\n";
	finishPortlet();
}

function renderRSPoolLBForm ($pool_id)
{
	$poolInfo = spotEntity ('ipv4rspool', $pool_id);
	amplifyCell ($poolInfo);

	function printNewItemTR ()
	{
		startPortlet ('Add new');
		echo "<table cellspacing=0 cellpadding=5 align=center>";
		printOpFormIntro ('addLB');
		echo "<tr valign=top><th class=tdright>Load balancer</th><td class=tdleft>";
		printSelect (getNarrowObjectList ('IPV4LB_LISTSRC'), array ('name' => 'object_id', 'tabindex' => 1));
		echo '</td><td class=tdcenter valign=middle rowspan=2>';
		printImageHREF ('ADD', 'Configure LB', TRUE, 5);
		echo '</td></tr><tr><th class=tdright>Virtual service</th><td class=tdleft>';
		printSelect (getIPv4VSOptions(), array ('name' => 'vs_id', 'tabindex' => 2));
		echo "</td></tr>\n";
		echo "<tr><th class=tdright>VS config</th><td colspan=2><textarea tabindex=3 name=vsconfig rows=10 cols=80></textarea></td></tr>";
		echo "<tr><th class=tdright>RS config</th><td colspan=2><textarea tabindex=4 name=rsconfig rows=10 cols=80></textarea></td></tr>";
		echo "</form></table>\n";
		finishPortlet();
	}
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	if (count ($poolInfo['lblist']))
	{
		startPortlet ('Manage existing (' . count ($poolInfo['lblist']) . ')');
		echo "<table cellspacing=0 cellpadding=5 align=center class=cooltable>\n";
		global $nextorder;
		$order = 'odd';
		foreach ($poolInfo['lblist'] as $object_id => $vslist)
			foreach ($vslist as $vs_id => $configs)
			{
				printOpFormIntro ('updLB', array ('vs_id' => $vs_id, 'object_id' => $object_id));
				echo "<tr valign=top class=row_${order}><td rowspan=2 class=tdright valign=middle><a href='".makeHrefProcess(array('op'=>'delLB', 'pool_id'=>$pool_id, 'object_id'=>$object_id, 'vs_id'=>$vs_id))."'>";
				printImageHREF ('DELETE', 'Unconfigure');
				echo "</a></td>";
				echo "<td class=tdleft valign=bottom>";
				renderLBCell ($object_id);
				echo "</td><td>VS config &darr;<br><textarea name=vsconfig rows=5 cols=70>${configs['vsconfig']}</textarea></td>";
				echo '<td class=tdleft rowspan=2 valign=middle>';
				printImageHREF ('SAVE', 'Save changes', TRUE);
				echo "</td></tr><tr class=row_${order}><td class=tdleft valign=top>";
				renderCell (spotEntity ('ipv4vs', $vs_id));
				echo '</td><td>';
				echo "<textarea name=rsconfig rows=5 cols=70>${configs['rsconfig']}</textarea><br>RS config &uarr;";
				echo '</td></tr></form>';
				$order = $nextorder[$order];
			}
		echo "</table>\n";
		finishPortlet();
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
}

function renderVServiceLBForm ($vs_id)
{
	global $nextorder;
	$vsinfo = spotEntity ('ipv4vs', $vs_id);
	amplifyCell ($vsinfo);

	function printNewItemTR ()
	{
		startPortlet ('Add new');
		echo '<table cellspacing=0 cellpadding=5 align=center>';
		printOpFormIntro ('addLB');
		echo '<tr valign=top><th class=tdright>Load balancer</th><td class=tdleft>';
		printSelect (getNarrowObjectList ('IPV4LB_LISTSRC'), array ('name' => 'object_id', 'tabindex' => 101));
		echo '</td><td rowspan=2 class=tdcenter valign=middle>';
		printImageHREF ('ADD', 'Configure LB', TRUE, 105);
		echo '</td></tr><tr><th class=tdright>RS pool</th><td class=tdleft>';
		printSelect (getIPv4RSPoolOptions(), array ('name' => 'pool_id', 'tabindex' => 102));
		echo '</td></tr>';
		echo '<tr><th class=tdright>VS config</th><td colspan=2><textarea tabindex=103 name=vsconfig rows=10 cols=80></textarea></td></tr>';
		echo '<tr><th class=tdright>RS config</th><td colspan=2><textarea tabindex=104 name=rsconfig rows=10 cols=80></textarea></td></tr>';
		echo '</form></table>';
		finishPortlet();
	}
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	if (count ($vsinfo['rspool']))
	{
		startPortlet ('Manage existing (' . count ($vsinfo['rspool']) . ')');
		echo '<table cellspacing=0 cellpadding=5 align=center class=cooltable>';
		$order = 'odd';
		foreach ($vsinfo['rspool'] as $pool_id => $rspinfo)
			foreach ($rspinfo['lblist'] as $object_id => $configs)
			{
				printOpFormIntro ('updLB', array ('pool_id' => $pool_id, 'object_id' => $object_id));
				echo "<tr valign=middle class=row_${order}><td rowspan=2>";
				echo "<a href='".makeHrefProcess(array('op'=>'delLB', 'pool_id'=>$pool_id, 'object_id'=>$object_id, 'vs_id'=>$vs_id))."'>";
				printImageHREF ('DELETE', 'Unconfigure');
				echo "</a></td>";
				echo '<td class=tdleft valign=bottom>';
				renderLBCell ($object_id);
				echo "</td><td>VS config &darr;<br><textarea name=vsconfig rows=5 cols=70>${configs['vsconfig']}</textarea></td>";
				echo '<td rowspan=2>';
				printImageHREF ('SAVE', 'Save changes', TRUE);
				echo '</td></tr>';
				echo "<tr class=row_${order}><td valign=top>";
				renderCell (spotEntity ('ipv4rspool', $pool_id));
				echo "</td><td><textarea name=rsconfig rows=5 cols=70>${configs['rsconfig']}</textarea><br>";
				echo 'RS config &uarr;</td></tr></form>';
				$order = $nextorder[$order];
			}
		echo '</table>';
		finishPortlet();
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
}

function renderRSPool ($pool_id)
{
	global $nextorder;
	$poolInfo = spotEntity ('ipv4rspool', $pool_id);
	amplifyCell ($poolInfo);

	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	if (strlen ($poolInfo['name']))
		echo "<tr><td colspan=2 align=center><h1>{$poolInfo['name']}</h1></td></tr>";
	echo "<tr><td class=pcleft>\n";

	startPortlet ('Summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	if (strlen ($poolInfo['name']))
		echo "<tr><th width='50%' class=tdright>Pool name:</th><td class=tdleft>${poolInfo['name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Real servers:</th><td class=tdleft>" . count ($poolInfo['rslist']) . "</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Load balancers:</th><td class=tdleft>" . count ($poolInfo['lblist']) . "</td></tr>\n";
	printTagTRs ($poolInfo, makeHref(array('page'=>'ipv4rsplist', 'tab'=>'default'))."&");
	if (strlen ($poolInfo['vsconfig']))
	{
		echo "<tr><th width='50%' class=tdright>VS configuration:</th><td>&nbsp;</td></tr>\n";
		echo "<tr><td colspan=2 class='dashed slbconf'>${poolInfo['vsconfig']}</td></tr>\n";
	}
	if (strlen ($poolInfo['rsconfig']))
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

	function printNewItemTR ($protocols)
	{
		startPortlet ('Add new');
		printOpFormIntro ('add');
		echo "<table border=0 cellpadding=10 cellspacing=0 align=center>\n";
		echo "<tr valign=bottom><td>&nbsp;</td><th>VIP</th><th>port</th><th>proto</th><th>name</th><th>&nbsp;</th><th>Assign tags</th></tr>";
		echo '<tr valign=top><td>&nbsp;</td>';
		echo "<td><input type=text name=vip tabindex=101></td>";
		$default_port = getConfigVar ('DEFAULT_SLB_VS_PORT');
		if ($default_port == 0)
			$default_port = '';
		echo "<td><input type=text name=vport size=5 value='${default_port}' tabindex=102></td><td>";
		printSelect ($protocols, array ('name' => 'proto'), 'TCP');
		echo '</td><td><input type=text name=name tabindex=104></td><td>';
		printImageHREF ('CREATE', 'create virtual service', TRUE, 105);
		echo "</td><td rowspan=3>";
		renderNewEntityTags ('ipv4vs');
		echo "</td></tr><tr><th>VS configuration</th><td colspan=5 class=tdleft><textarea name=vsconfig rows=10 cols=80></textarea></td>";
		echo "<tr><th>RS configuration</th><td colspan=5 class=tdleft><textarea name=rsconfig rows=10 cols=80></textarea></td></tr>";
		echo '</table></form>';
		finishPortlet();
	}

	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($protocols);

	if (count ($vslist = listCells ('ipv4vs')))
	{
		startPortlet ('Delete existing (' . count ($vslist) . ')');
		echo '<table class=cooltable border=0 cellpadding=10 cellspacing=0 align=center>';
		$order = 'odd';
		foreach ($vslist as $vsid => $vsinfo)
		{
			echo "<tr valign=top class=row_${order}><td valign=middle>";
			if ($vsinfo['poolcount'])
				printImageHREF ('NODESTROY', 'there are ' . $vsinfo['poolcount'] . ' RS pools configured');
			else
			{
				echo "<a href='".makeHrefProcess(array('op'=>'del', 'vs_id'=>$vsid))."'>";
				printImageHREF ('DESTROY', 'delete virtual service');
				echo '</a>';
			}
			echo "</td><td class=tdleft>";
			renderCell ($vsinfo);
			echo "</td></tr>";
			$order = $nextorder[$order];
		}
		echo "</table>";
		finishPortlet();
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ($protocols);
}

function renderRSPoolList ()
{
	renderCellList ('ipv4rspool', 'RS pools');
}

function editRSPools ()
{
	function printNewItemTR()
	{
		startPortlet ('Add new');
		printOpFormIntro ('add');
		echo "<table border=0 cellpadding=10 cellspacing=0 align=center>";
		echo "<tr><th class=tdright>Name</th>";
		echo "<td class=tdleft><input type=text name=name tabindex=101></td><td>";
		printImageHREF ('CREATE', 'create real server pool', TRUE, 104);
		echo "</td><th>Assign tags</th></tr>";
		echo "<tr><th class=tdright>VS config</th><td colspan=2><textarea name=vsconfig rows=10 cols=80 tabindex=102></textarea></td>";
		echo "<td rowspan=2>";
		renderNewEntityTags ('ipv4rspool');
		echo "</td></tr>";
		echo "<tr><th class=tdright>RS config</th><td colspan=2><textarea name=rsconfig rows=10 cols=80 tabindex=103></textarea></td></tr>";
		echo "</table></form>";
		finishPortlet();
	}

	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	if (count ($pool_list = listCells ('ipv4rspool')))
	{
		startPortlet ('Delete existing (' . count ($pool_list) . ')');
		echo "<table class=cooltable border=0 cellpadding=10 cellspacing=0 align=center>\n";
		global $nextorder;
		$order='odd';
		foreach ($pool_list as $pool_info)
		{
			echo "<tr valign=top class=row_${order}><td valign=middle>";
			if ($pool_info['refcnt'])
				printImageHREF ('NODESTROY', 'RS pool is used ' . $pool_info['refcnt'] . ' time(s)');
			else
			{
				echo "<a href='".makeHrefProcess(array('op'=>'del', 'pool_id'=>$pool_info['id']))."'>";
				printImageHREF ('DESTROY', 'delete real server pool');
				echo '</a>';
			}
			echo '</td><td class=tdleft>';
			renderCell ($pool_info);
			echo '</td></tr>';
			$order = $nextorder[$order];
		}
		echo "</table>";
		finishPortlet();
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
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
		echo !strlen ($pool_list[$rsinfo['rspool_id']]['name']) ? 'ANONYMOUS' : $pool_list[$rsinfo['rspool_id']]['name'];
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
	$order = 'odd';
	foreach (getLBList() as $object_id => $poolcount)
	{
		$oi = spotEntity ('object', $object_id);
		echo "<tr valign=top class=row_${order}><td><a href='".makeHref(array('page'=>'object', 'object_id'=>$object_id))."'>";
		echo $oi['dname'] . '</a></td>';
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
			if (strlen ($ptrname))
			{
				echo ' class=trok';
				$cnt_match++;
			}
		}
		elseif (!strlen ($addr['name']) or !strlen ($ptrname))
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
		if (isset ($range['addrlist'][$ip]['class']) and strlen ($range['addrlist'][$ip]['class']))
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
	$info = spotEntity ('object', $object_id);
	$ptlist = readChapter (CHAP_PORTTYPE, 'a');
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
	$refc = $taginfo['refcnt']['total'];
	echo "<tr><td align=left style='padding-left: " . ($level * 16) . "px;'>";
	if (count ($taginfo['kids']))
		printImageHREF ('node-expanded-static');
	echo '<span title="id = ' . $taginfo['id'] . '">' . $taginfo['tag'];
	echo ($refc ? " <i>(${refc})</i>" : '') . '</span></td></tr>';
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
	if ($taginfo['refcnt']['total'] > 0 or count ($taginfo['kids']) > 0)
		printImageHREF ('nodestroy', $taginfo['refcnt']['total'] . ' references, ' . count ($taginfo['kids']) . ' sub-tags');
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
		echo "<tr><td align=left style='padding-left: 16px;'>";
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

function renderTagCheckbox ($inputname, $preselect, $taginfo, $refcnt_realm = '', $level = 0)
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
	echo "<label><input type=checkbox name='${inputname}[]' value='${taginfo['id']}'${selected}> ";
	echo $taginfo['tag'];
	if (strlen ($refcnt_realm) and isset ($taginfo['refcnt'][$refcnt_realm]))
		echo ' <i>(' . $taginfo['refcnt'][$refcnt_realm] . ')</i>';
	echo "</label></td></tr>";
	if (isset ($taginfo['kids']))
		foreach ($taginfo['kids'] as $kid)
			$self ($inputname, $preselect, $kid, $refcnt_realm, $level + 1);
}

function renderEntityTagsPortlet ($title, $tags, $preselect, $realm)
{
	startPortlet ($title);
	echo '<table border=0 cellspacing=0 cellpadding=3 align=center>';
	printOpFormIntro ('saveTags');
	foreach ($tags as $taginfo)
		renderTagCheckbox ('taglist', $preselect, $taginfo, $realm);
	echo '<tr><td class=tdleft>';
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</form></td><td class=tdright>";
	if (!count ($preselect))
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

function renderEntityTags ($entity_id)
{
	global $tagtree, $taglist, $target_given_tags, $pageno, $etype_by_pageno;
	echo '<table border=0 width="100%"><tr>';

	if (count ($taglist) > getConfigVar ('TAGS_QUICKLIST_THRESHOLD'))
	{
		$minilist = getTagChart (getConfigVar ('TAGS_QUICKLIST_SIZE'), $etype_by_pageno[$pageno], $target_given_tags);
		// It could happen, that none of existing tags have been used in the current realm.
		if (count ($minilist))
		{
			echo '<td class=pcleft width="50%">';
			renderEntityTagsPortlet ('Quick list', $minilist, $target_given_tags, $etype_by_pageno[$pageno]);
			echo '</td>';
		}
	}

	// do not do anything about empty tree, trigger function ought to work this out
	echo '<td class=pcright>';
	renderEntityTagsPortlet ('Full tree', $tagtree, $target_given_tags, $etype_by_pageno[$pageno]);
	echo '</td>';

	echo '</tr></table>';
}

function printTagTRs ($cell, $baseurl = '')
{
	if (getConfigVar ('SHOW_EXPLICIT_TAGS') == 'yes' and count ($cell['etags']))
	{
		echo "<tr><th width='50%' class=tagchain>Explicit tags:</th><td class=tagchain>";
		echo serializeTags ($cell['etags'], $baseurl) . "</td></tr>\n";
	}
	if (getConfigVar ('SHOW_IMPLICIT_TAGS') == 'yes' and count ($cell['itags']))
	{
		echo "<tr><th width='50%' class=tagchain>Implicit tags:</th><td class=tagchain>";
		echo serializeTags ($cell['itags'], $baseurl) . "</td></tr>\n";
	}
	if (getConfigVar ('SHOW_AUTOMATIC_TAGS') == 'yes' and count ($cell['atags']))
	{
		echo "<tr><th width='50%' class=tagchain>Automatic tags:</th><td class=tagchain>";
		echo serializeTags ($cell['atags']) . "</td></tr>\n";
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
	// "reset filter" button only gets active when a filter is applied
	$enable_reset = FALSE;
	// "apply filter" button only gets active when there are checkbox/textarea inputs on the roster
	$enable_apply = FALSE;
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
			echo "<td class=${class}><label><input type=radio name=andor value=${boolop}";
			echo $checked . ">${boolop}</input></label></td>";
		}
	}
	// tags block
	if (getConfigVar ('FILTER_SUGGEST_TAGS') == 'yes' or count ($preselect['tagidlist']))
	{
		if (count ($preselect['tagidlist']))
			$enable_reset = TRUE;
		echo $hr;
		$hr = $ruler;
		// Show a tree of tags, pre-select according to currently requested list filter.
		global $tagtree;
		$objectivetags = getObjectiveTagTree ($tagtree, $realm, $preselect['tagidlist']);
		if (!count ($objectivetags))
			echo "<tr><td colspan=2 class='tagbox sparenetwork'>(nothing is tagged yet)</td></tr>";
		else
		{
			$enable_apply = TRUE;
			foreach ($objectivetags as $taginfo)
				renderTagCheckbox ('cft', buildTagChainFromIds ($preselect['tagidlist']), $taginfo, $realm);
		}
	}
	// predicates block
	if (getConfigVar ('FILTER_SUGGEST_PREDICATES') == 'yes' or count ($preselect['pnamelist']))
	{
		if (count ($preselect['pnamelist']))
			$enable_reset = TRUE;
		echo $hr;
		$hr = $ruler;
		global $pTable;
		$myPredicates = array();
		$psieve = getConfigVar ('FILTER_PREDICATE_SIEVE');
		// Repack matching predicates in a way, which tagOnChain() understands.
		foreach (array_keys ($pTable) as $pname)
			if (preg_match ("/${psieve}/", $pname))
				$myPredicates[] = array ('id' => $pname, 'tag' => $pname);
		if (!count ($myPredicates))
			echo "<tr><td colspan=2 class='tagbox sparenetwork'>(no predicates to show)</td></tr>";
		else
		{
			$enable_apply = TRUE;
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
		$enable_apply = TRUE;
		if (strlen ($preselect['extratext']))
			$enable_reset = TRUE;
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
		echo '<tr><td class=tdleft>';
		// "apply"
		echo "<input type=hidden name=page value=${pageno}>\n";
		echo "<input type=hidden name=tab value=${tabno}>\n";
		if ($bypass_name != '')
			echo "<input type=hidden name=${bypass_name} value='${bypass_value}'>\n";
		// FIXME: The user will be able to "submit" the empty form even without a "submit"
		// input. To make things consistent, it is necessary to avoid pritning both <FORM>
		// and "and/or" radio-buttons, when enable_apply isn't TRUE.
		if (!$enable_apply)
			printImageHREF ('setfilter gray');
		else
			printImageHREF ('setfilter', 'set filter', TRUE);
		echo '</form>';
		echo '</td><td class=tdright>';
		// "reset"
		if (!$enable_reset)
			printImageHREF ('resetfilter gray');
		else
		{
			echo "<form method=get>\n";
			echo "<input type=hidden name=page value=${pageno}>\n";
			echo "<input type=hidden name=tab value=${tabno}>\n";
			if ($bypass_name != '')
				echo "<input type=hidden name=${bypass_name} value='${bypass_value}'>\n";
			printImageHREF ('resetfilter', 'reset filter', TRUE);
			echo '</form>';
		}
		echo '</td></tr>';
	}
	echo '</table>';
	finishPortlet();
}

// Dump all tags in a single SELECT element.
function renderNewEntityTags ($for_realm = '')
{
	global $taglist, $tagtree;
	if (!count ($taglist))
	{
		echo "No tags defined";
		return;
	}
	echo '<div class=tagselector><table border=0 align=center>';
	foreach ($tagtree as $taginfo)
		renderTagCheckbox ('taglist', array(), $taginfo, $for_realm);
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
	function printNewItemTR ()
	{
		startPortlet ('Add new');
		echo '<table cellspacing=0 cellpadding=5 align=center>';
		printOpFormIntro ('addLB');
		echo '<tr><th class=tdright>Virtual service</th><td class=tdleft>';
		printSelect (getIPv4VSOptions(), array ('name' => 'vs_id', 'tabindex' => 101));
		echo '</td><td class=tdcenter valign=middle rowspan=2>';
		printImageHREF ('ADD', 'Configure LB', TRUE, 105);
		echo '</td></tr>';
		echo '</tr><th class=tdright>RS pool</th><td class=tdleft>';
		printSelect (getIPv4RSPoolOptions(), array ('name' => 'pool_id', 'tabindex' => 102));
		echo "</td></tr>";
		echo '<tr><th class=tdright>VS config</th><td colspan=2><textarea tabindex=103 name=vsconfig rows=10 cols=80></textarea></td></tr>';
		echo '<tr><th class=tdright>RS config</th><td colspan=2><textarea tabindex=104 name=rsconfig rows=10 cols=80></textarea></td></tr>';
		echo '</form></table>';
		finishPortlet();
	}
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();

	$focus = spotEntity ('object', $object_id);
	amplifyCell ($focus);
	if (count ($focus['ipv4rspools']))
	{
		startPortlet ('Manage existing (' . count ($focus['ipv4rspools']) . ')');
		echo '<table cellspacing=0 cellpadding=5 align=center class=cooltable>';
		$order = 'odd';
		global $nextorder;
		foreach ($focus['ipv4rspools'] as $vs_id => $vsinfo)
		{
			printOpFormIntro ('updLB', array ('vs_id' => $vs_id, 'pool_id' => $vsinfo['pool_id']));
			echo "<tr class=row_${order}><td rowspan=2 valign=center><a href='".makeHrefProcess(array('op'=>'delLB', 'pool_id'=>$vsinfo['pool_id'], 'object_id'=>$object_id, 'vs_id'=>$vs_id))."'>";
			printImageHREF ('DELETE', 'Unconfigure');
			echo "</a></td>";
			echo "</td><td class=tdleft valign=bottom>";
			renderCell (spotEntity ('ipv4vs', $vs_id));
			echo '</td>';
			echo "<td>VS config &darr;<br><textarea name=vsconfig rows=5 cols=70>${vsinfo['vsconfig']}</textarea></td>";
			echo "<td rowspan=2 valign=middle>";
			printImageHREF ('SAVE', 'Save changes', TRUE);
			echo '</td></tr>';
			echo "<tr class=row_${order}><td valign=top>";
			renderCell (spotEntity ('ipv4rspool', $vsinfo['pool_id']));
			echo "</td><td><textarea name=rsconfig rows=5 cols=70>${vsinfo['rsconfig']}</textarea><br>RS config &uarr;</td></tr>";
			echo '</form>';
			$order = $nextorder[$order];
		}
		echo "</table>\n";
		finishPortlet();
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
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
	printSelect (array ('TCP' => 'TCP', 'UDP' => 'UDP'), array ('name' => 'proto'), $vsinfo['proto']);
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>name:</th><td class=tdleft><input tabindex=4 type=text name=name value='${vsinfo['name']}'></td></tr>\n";
	echo "<tr><th class=tdright>VS config:</th><td class=tdleft><textarea tabindex=5 name=vsconfig rows=20 cols=80>${vsinfo['vsconfig']}</textarea></td></tr>\n";
	echo "<tr><th class=tdright>RS config:</th><td class=tdleft><textarea tabindex=6 name=rsconfig rows=20 cols=80>${vsinfo['rsconfig']}</textarea></td></tr>\n";
	echo "<tr><th class=submit colspan=2>";
	printImageHREF ('SAVE', 'Save changes', TRUE, 7);
	echo "</td></tr>\n";
	echo "</table></form>\n";
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
	$userinfo = spotEntity ('user', $user_id);

	startPortlet ('summary');
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Account name:</th><td class=tdleft>${userinfo['user_name']}</td></tr>";
	echo '<tr><th class=tdright>Real name:</th><td class=tdleft>' . $userinfo['user_realname'] . '</td></tr>';
	printTagTRs ($userinfo, makeHref(array('page'=>'userlist', 'tab'=>'default'))."&");
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

function renderMyPreferences ()
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
		if ($v['is_userdefined'] != 'yes')
			continue;
		echo "<input type=hidden name=${i}_varname value='${v['varname']}'>";
		echo "<tr><td class=\"tdright\">${v['description']}</td>";
		echo "<td class=\"tdleft\"><input type=text name=${i}_varvalue value='${v['varvalue']}' size=24></td>";
		if ($v['is_altered'] == 'yes')
			echo "<td class=\"tdleft\"><a href=\"".
				makeHrefProcess(array('op'=>'reset', 'varname'=>$v['varname']))
				."\">reset</a></td>";
		else
			echo "<td class=\"tdleft\">(default)</td>";
		echo "</tr>\n";
		$i++;
	}
	echo "<input type=hidden name=num_vars value=${i}>\n";
	echo "<tr><td colspan=3>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>";
	echo "</form>";
	finishPortlet();

}



function renderAccessDenied ()
{
	header ('Content-Type: text/html; charset=UTF-8');
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
	echo "<head><title>RackTables: access denied</title>\n";
	printPageHeaders();
	echo "</head><body>";
	global $pageno, $tabno,
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
	echo "<tr><td colspan=2 align=center>Click <a href='index.php?logout'>here</a> to logout.</td></tr>\n";
	echo "</table>\n";
	echo "</body></html>";
	die;
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
	global $nextorder, $aac;
	$file = spotEntity ('file', $file_id);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>" . htmlspecialchars ($file['name']) . "</h1></td></tr>\n";
	echo "<tr><td class=pcleft>";
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>Type:</th>";
	printf("<td class=tdleft>%s</td></tr>", htmlspecialchars ($file['type']));
	echo "<tr><th width='50%' class=tdright>Size:</th><td class=tdleft>";
	if (isolatedPermission ('file', 'download', $file))
	{
		echo "<a href='download.php?file_id=${file_id}'>";
		printImageHREF ('download', 'Download file');
		echo '</a>&nbsp;';
	}
	printf("%s</td></tr>", formatFileSize($file['size']));
	echo "<tr><th width='50%' class=tdright>Created:</th>";
	printf("<td class=tdleft>%s</td></tr>", formatTimestamp($file['ctime']));
	echo "<tr><th width='50%' class=tdright>Modified:</th>";
	printf("<td class=tdleft>%s</td></tr>", formatTimestamp($file['mtime']));
	echo "<tr><th width='50%' class=tdright>Accessed:</th>";
	printf("<td class=tdleft>%s</td></tr>", formatTimestamp($file['atime']));

	printTagTRs ($file, makeHref(array('page'=>'files', 'tab'=>'default'))."&");
	if (strlen ($file['comment']))
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
				case 'object':
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

	if (isolatedPermission ('file', 'download', $file) and '' != ($pcode = getFilePreviewCode ($file)))
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

function renderFileDownloader ($file_id)
{
	echo "<br><center><a target='_blank' href='download.php?file_id=${file_id}&asattach=1'>";
	printImageHREF ('DOWNLOAD');
	echo '</a></center>';
}

function renderFileProperties ($file_id)
{
	$file = spotEntity ('file', $file_id);
	if ($file === NULL)
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
	// Used for uploading a parentless file
	function printNewItemTR ()
	{
		startPortlet ('Upload new');
		printOpFormIntro ('addFile', array (), TRUE);
		echo "<table border=0 cellspacing=0 cellpadding='5' align='center'>";
		echo '<tr><th colspan=2>Comment</th><th>Assign tags</th></tr>';
		echo '<tr><td valign=top colspan=2><textarea tabindex=101 name=comment rows=10 cols=80></textarea></td>';
		echo '<td rowspan=2>';
		renderNewEntityTags ('file');
		echo '</td></tr>';
		echo "<tr><td class=tdleft><label>File: <input type='file' size='10' name='file' tabindex=100></label></td><td class=tdcenter>";
		printImageHREF ('CREATE', 'Upload file', TRUE, 102);
		echo '</td></tr>';
		echo "</table></form><br>";
		finishPortlet();
	}

	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	if (count ($files = listCells ('file')))
	{
		startPortlet ('Manage existing (' . count ($files) . ')');
		global $nextorder;
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
			echo '</td><td class=tdcenter valign=middle>';
			if (count ($file['links']))
				printImageHREF ('NODESTROY', 'References (' . count ($file['links']) . ')');
			else
			{
				echo "<a href='".makeHrefProcess(array('op'=>'deleteFile', 'file_id'=>$file['id'])).
					"' onclick=\"javascript:return confirm('Are you sure you want to delete the file?')\">";
				printImageHREF ('DESTROY', 'Delete file');
				echo "</a>";
			}
			echo "</td></tr>";
			$order = $nextorder[$order];
		}
		echo '</table>';
		finishPortlet();
	}

	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
}

function renderFilesPortlet ($entity_type = NULL, $entity_id = 0)
{
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
			$file = spotEntity ('file', $file['id']);
			renderCell ($file);
			echo "</td><td class=tdleft>${file['comment']}</td></tr>";
			if (isolatedPermission ('file', 'download', $file) and '' != ($pcode = getFilePreviewCode ($file)))
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
		printSelect ($files, array ('name' => 'file_id'));
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
	global $pageno, $tabno, $page;

	echo "<form method=post name=${opname} action='process.php?page=${pageno}&tab=${tabno}&op=${opname}'";
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
	if (!mb_strlen ($string))
		return '&nbsp;';
	// a tab counts for a space
	$string = preg_replace ("/\t/", ' ', $string);
	if (!$maxlen or mb_strlen ($string) <= $maxlen)
		return htmlspecialchars ($string, ENT_QUOTES, 'UTF-8');
	return "<span title='" . htmlspecialchars ($string, ENT_QUOTES, 'UTF-8') . "'>" .
		str_replace (' ', '&nbsp;', htmlspecialchars (mb_substr ($string, 0, $maxlen - 1), ENT_QUOTES, 'UTF-8')) . $cutind . '</span>';
}

// Iterate over what findRouters() returned and output some text suitable for a TD element.
function printRoutersTD ($rlist, $as_cell = 'yes')
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
	$pfx = '';
	foreach ($rlist as $rtr)
	{
		$rinfo = spotEntity ('object', $rtr['id']);
		if ($as_cell == 'yes')
			renderRouterCell ($rtr['addr'], $rtr['iface'], $rinfo);
		else
			echo $pfx . '<a href="' . makeHref (array ('page' => 'object', 'object_id' => $rinfo['id'])) . '">' . $rinfo['dname'] . '</a>';
		$pfx = "<br>\n";
	}
	echo '</td>';
}

// Same as for routers, but produce two TD cells to lay the content out better.
function printIPv4NetInfoTDs ($netinfo, $tdclass = 'tdleft', $indent = 0, $symbol = 'spacer', $symbolurl = '')
{
	if ($symbol == 'spacer')
	{
		$indent++;
		$symbol = '';
	}
	echo "<td class='${tdclass}' style='padding-left: " . ($indent * 16) . "px;'>";
	if (strlen ($symbol))
	{
		if (strlen ($symbolurl))
			echo "<a href='${symbolurl}'>";
		printImageHREF ($symbol, $symbolurl);
		if (strlen ($symbolurl))
			echo '</a>';
	}
	if (isset ($netinfo['id']))
		echo "<a href='index.php?page=ipv4net&id=${netinfo['id']}'>";
	echo "${netinfo['ip']}/${netinfo['mask']}";
	if (isset ($netinfo['id']))
		echo '</a>';
	echo "</td><td class='${tdclass}'>";
	if (!isset ($netinfo['id']))
	{
		printImageHREF ('dragons', 'Here be dragons.');
		if (getConfigVar ('IPV4_ENABLE_KNIGHT') == 'yes')
		{
			echo '<a href="' . makeHref (array
			(
				'page' => 'ipv4space',
				'tab' => 'newrange',
				'set-prefix' => $netinfo['ip'] . '/' . $netinfo['mask'],
			)) . '">';
			printImageHREF ('knight', 'create network here', TRUE);
			echo '</a>';
		}
	}
	else
	{
		echo niftyString ($netinfo['name']);
		if (count ($netinfo['etags']))
			echo '<br><small>' . serializeTags ($netinfo['etags'], "index.php?page=ipv4space&tab=default&") . '</small>';
	}
	echo "</td>";
}

function renderCell ($cell)
{
	switch ($cell['realm'])
	{
	case 'user':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		printImageHREF ('USER');
		echo '</td>';
		echo "<td><a href='index.php?page=user&user_id=${cell['user_id']}'>${cell['user_name']}</a></td></tr>";
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
		printf ("<a href='index.php?page=file&file_id=%s'><strong>%s</strong></a>", $cell['id'], niftyString ($cell['name']));
		echo "</td><td rowspan=3 valign=top>";
		if (isset ($cell['links']) and count ($cell['links']))
			printf ("<small>%s</small>", serializeFileLinks ($cell['links']));
		echo "</td></tr><tr><td>";
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo '</td></tr><tr><td>';
		if (isolatedPermission ('file', 'download', $cell))
		{
			echo "<a href='download.php?file_id=${cell['id']}'>";
			printImageHREF ('download', 'Download file');
			echo '</a>&nbsp;';
		}
		echo formatFileSize ($cell['size']);
		echo "</td></tr></table>";
		break;
	case 'ipv4vs':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		printImageHREF ('VS');
		echo "</td><td>";
		echo "<a href='index.php?page=ipv4vs&vs_id=${cell['id']}'>";
		echo $cell['dname'] . "</a></td></tr><tr><td>";
		echo $cell['name'] . '</td></tr><tr><td>';
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	case 'ipv4rspool':
		echo "<table class='slbcell vscell'><tr><td>";
		echo "<a href='index.php?page=ipv4rspool&pool_id=${cell['id']}'>";
		echo !strlen ($cell['name']) ? "ANONYMOUS pool [${cell['id']}]" : niftyString ($cell['name']);
		echo "</a></td></tr><tr><td>";
		printImageHREF ('RS pool');
		if ($cell['rscount'])
			echo ' <small>(' . $cell['rscount'] . ')</small>';
		echo "</td></tr><tr><td>";
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	case 'ipv4net':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		printImageHREF ('NET');
		echo '</td>';
		echo "<td><a href='index.php?page=ipv4net&id=${cell['id']}'>${cell['ip']}/${cell['mask']}</a></td></tr>";
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
		printf ("<a href='index.php?page=rack&rack_id=%s'><strong>%s</strong></a>", $cell['id'], niftyString ($cell['name']));
		echo "</td></tr><tr><td>";
		echo niftyString ($cell['comment']);
		echo "</td></tr><tr><td>";
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	case 'object':
		echo "<table class='slbcell vscell'><tr><td rowspan=2 width='5%'>";
		printImageHREF ('OBJECT');
		echo '</td>';
		echo "<td><a href='index.php?page=object&object_id=${cell['id']}'>";
		echo "<strong>" . niftyString ($cell['dname']) . "</strong></a></td></tr>";
		echo '<td>';
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	default:
		throw new RealmNotFoundException($cell['realm']);
	}
}

function renderLBCell ($object_id)
{
	$oi = spotEntity ('object', $object_id);
	echo "<table class=slbcell><tr><td>";
	echo "<a href='index.php?page=object&object_id=${object_id}'>${oi['dname']}</a>";
	echo "</td></tr><tr><td>";
	printImageHREF ('LB');
	echo "</td></tr><tr><td>";
	if (count ($oi['etags']))
		echo '<small>' . serializeTags ($oi['etags']) . '</small>';
	echo "</td></tr></table>";
}

function renderRouterCell ($dottedquad, $ifname, $cell)
{
	echo "<table class=slbcell><tr><td rowspan=3>${dottedquad}";
	if (strlen ($ifname))
		echo '@' . $ifname;
	echo "</td>";
	echo "<td><a href='index.php?page=object&object_id=${cell['id']}&hl_ipv4_addr=${dottedquad}'><strong>${cell['dname']}</strong></a></td>";
	echo "</td></tr><tr><td>";
	printImageHREF ('router');
	echo "</td></tr><tr><td>";
	if (count ($cell['etags']))
		echo '<small>' . serializeTags ($cell['etags']) . '</small>';
	echo "</td></tr></table>";
}

// Return HTML code necessary to show a preview of the file give. Return an empty string,
// if a preview cannot be shown
function getFilePreviewCode ($file)
{
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
				$ret .= "<a href='download.php?file_id=${file['id']}&asattach=no'>";
			$ret .= "<img width=${width} height=${height} src='render_image.php?img=preview&file_id=${file['id']}'>";
			if ($resampled)
				$ret .= '</a><br>(click to zoom)';
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
	global $page;
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
		echo ": <a href='index.php?page=${no}&tab=default";
		foreach ($title['params'] as $param_name => $param_value)
			echo "&${param_name}=${param_value}";
		echo "'>" . $title['name'] . "</a>";
	}
	echo "</td>";
	// Search form.
	echo "<td><table border=0 cellpadding=0 cellspacing=0><tr><td>Search:</td>";
	echo "<form name=search method=get><td>";
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
	global $tab, $page, $trigger;
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
		elseif (!strlen ($tabclass = call_user_func ($trigger[$pageno][$tabidx])))
			continue;
		if ($tabidx == $tabno)
		       $tabclass = 'current'; // override any class for an an active selection
		echo "<li><a class=${tabclass}";
		echo " href='index.php?page=${pageno}&tab=${tabidx}";
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
	global $sic;
	switch ($path_position)
	{
	case 'index':
		return array
		(
			'name' => '/' . getConfigVar ('enterprise'),
			'params' => array()
		);
	case 'chapter':
		assertUIntArg ('chapter_no');
		$chapters = getChapterList();
		$chapter_no = $_REQUEST['chapter_no'];
		$chapter_name = isset ($chapters[$chapter_no]) ? $chapters[$chapter_no]['name'] : 'N/A';
		return array
		(
			'name' => "Chapter '${chapter_name}'",
			'params' => array ('chapter_no' => $chapter_no)
		);
	case 'user':
		assertUIntArg ('user_id');
		$userinfo = spotEntity ('user', $_REQUEST['user_id']);
		return array
		(
			'name' => "Local user '" . $userinfo['user_name'] . "'",
			'params' => array ('user_id' => $_REQUEST['user_id'])
		);
	case 'ipv4rspool':
		assertUIntArg ('pool_id');
		$poolInfo = spotEntity ('ipv4rspool', $_REQUEST['pool_id']);
		return array
		(
			'name' => !strlen ($poolInfo['name']) ? 'ANONYMOUS' : $poolInfo['name'],
			'params' => array ('pool_id' => $_REQUEST['pool_id'])
		);
	case 'ipv4vs':
		assertUIntArg ('vs_id');
		$tmp = spotEntity ('ipv4vs', $_REQUEST['vs_id']);
		return array
		(
			'name' => $tmp['dname'],
			'params' => array ('vs_id' => $_REQUEST['vs_id'])
		);
	case 'object':
		assertUIntArg ('object_id');
		$object = spotEntity ('object', $_REQUEST['object_id']);
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
		assertUIntArg ('rack_id');
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
		assertUIntArg ('file_id');
		$file = spotEntity ('file', $_REQUEST['file_id']);
		if ($file == NULL)
			return array
			(
				'name' => __FUNCTION__ . '() failure',
				'params' => array()
			);
		return array
		(
			'name' => niftyString ($file['name']),
			'params' => array ('file_id' => $_REQUEST['file_id'])
		);
	case 'ipaddress':
		assertIPv4Arg ('ip');
		$address = getIPv4Address ($_REQUEST['ip']);
		return array
		(
			'name' => $_REQUEST['ip'] . ($address['name'] != '' ? ' (' . $address['name'] . ')' : ''),
			'params' => array ('ip' => $_REQUEST['ip'])
		);
	case 'ipv4net':
		global $pageno;
		switch ($pageno)
		{
		case 'ipv4net':
			assertUIntArg ('id');
			$range = spotEntity ('ipv4net', $_REQUEST['id']);
			return array
			(
				'name' => $range['ip'] . '/' . $range['mask'],
				'params' => array ('id' => $_REQUEST['id'])
			);
		case 'ipaddress':
			assertIPv4Arg ('ip');
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
			assertUIntArg ('rack_id');
			$rack = spotEntity ('rack', $_REQUEST['rack_id']);
			return array
			(
				'name' => $rack['row_name'],
				'params' => array ('row_id' => $rack['row_id'])
			);
		case 'row':
			assertUIntArg ('row_id');
			$rowInfo = getRackRowInfo ($_REQUEST['row_id']);
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
	case 'vlandomain':
		global $pageno;
		switch ($pageno)
		{
		case 'vlandomain':
			$vdom_id = $_REQUEST['vdom_id'];
			break;
		case 'vlan':
			list ($vdom_id, $dummy) = decodeVLANCK ($_REQUEST['vlan_ck']);
			break;
		default:
			return array
			(
				'name' => __FUNCTION__ . '() failure',
				'params' => array()
			);
			
		}
		$dominfo = getVLANDomainInfo ($vdom_id);
		return array
		(
			'name' => niftyString ("domain '${dominfo['description']}'"),
			'params' => array ('vdom_id' => $vdom_id)
		);
	case 'vlan':
		return array
		(
			'name' => formatVLANName (getVLANInfo ($sic['vlan_ck']), TRUE),
			'params' => array ('vlan_ck' => $sic['vlan_ck'])
		);
	case 'vst':
		$vst = getVLANSwitchTemplate ($sic['vst_id']);
		return array
		(
			'name' => niftyString ("template '${vst['description']}'", 50),
			'params' => array ('vst_id' => $vst['id'])
		);
	default:
		return array
		(
			'name' => __FUNCTION__ . '() failure',
			'params' => array()
		);
	}
}

function renderPortIFCompat()
{
	global $nextorder;
	echo '<br><table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>';
	echo '<tr><th class=tdleft>inner interface</th><th class=tdleft>outer interface</th></tr>';
	$last_iif_id = 0;
	$order = 'even';
	foreach (getPortInterfaceCompat() as $record)
	{
		if ($last_iif_id != $record['iif_id'])
		{
			$order = $nextorder[$order];
			$last_iif_id = $record['iif_id'];
		}
		echo "<tr class=row_${order}><td class=tdleft>${record['iif_name']}</td><td class=tdleft>${record['oif_name']}</td></tr>";
	}
	echo '</table>';
}

function renderPortIFCompatEditor()
{
	function printNewitemTR()
	{
		printOpFormIntro ('add');
		echo '<tr><th class=tdleft>';
		printImageHREF ('add', 'add pair', TRUE);
		echo '</th><th class=tdleft>';
		printSelect (getPortIIFOptions(), array ('name' => 'iif_id'));
		echo '</th><th class=tdleft>';
		printSelect (readChapter (CHAP_PORTTYPE), array ('name' => 'oif_id'));
		echo '</th></tr></form>';
	}

	startPortlet ('WDM standard by interface');
	$packs = array
	(
		'1000cwdm80' => array
		(
			'title' => '1000Base-CWDM80 (8 channels)',
			'iif_ids' => array (3, 4),
		),
		'1000dwdm80' => array
		(
			'title' => '1000Base-DWDM80 (42 channels)',
			'iif_ids' => array (3, 4),
		),
		'10000dwdm80' => array
		(
			'title' => '10GBase-ZR-DWDM80 (42 channels)',
			'iif_ids' => array (9, 6, 5, 8, 7),
		),
	);
	$iif = getPortIIFOptions();
	global $nextorder;
	$order = 'odd';
	echo '<table border=0 align=center cellspacing=0 cellpadding=5>';
	foreach ($packs as $codename => $packinfo)
	{
		echo "<tr><th>&nbsp;</th><th colspan=2>${packinfo['title']}</th></tr>";
		foreach ($packinfo['iif_ids'] as $iif_id)
		{
			echo "<tr class=row_${order}><th class=tdleft>" . $iif[$iif_id] . '</th><td><a href="';
			echo makeHrefProcess (array ('op' => 'addPack', 'standard' => $codename, 'iif_id' => $iif_id));
			echo '">' . getImageHREF ('add') . '</a></td><td><a href="';
			echo makeHrefProcess (array ('op' => 'delPack', 'standard' => $codename, 'iif_id' => $iif_id));
			echo '">' . getImageHREF ('delete') . '</a></td></tr>';
			$order = $nextorder[$order];
		}
	}
	echo '</table>';
	finishPortlet();

	startPortlet ('interface by interface');
	global $nextorder;
	$last_iif_id = 0;
	$order = 'even';
	echo '<br><table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>';
	echo '<tr><th>&nbsp;</th><th class=tdleft>inner interface</th><th class=tdleft>outer interface</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewitemTR();
	foreach (getPortInterfaceCompat() as $record)
	{
		if ($last_iif_id != $record['iif_id'])
		{
			$order = $nextorder[$order];
			$last_iif_id = $record['iif_id'];
		}
		echo "<tr class=row_${order}><td>";
		echo '<a href="' . makeHrefProcess (array ('op' => 'del', 'iif_id' => $record['iif_id'], 'oif_id' => $record['oif_id'])) . '">';
		printImageHREF ('delete', 'remove pair', TRUE);
		echo "</a></td><td class=tdleft>${record['iif_name']}</td><td class=tdleft>${record['oif_name']}</td></tr>";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewitemTR();
	echo '</table>';
	finishPortlet();
}

// print part of HTML HEAD block
function printPageHeaders ()
{
	global $pageheaders;
	ksort ($pageheaders);
	foreach ($pageheaders as $s)
		echo $s . "\n";
	echo "<style type='text/css'>\n";
	foreach (array ('F', 'A', 'U', 'T', 'Th', 'Tw', 'Thw') as $statecode)
	{
		echo "td.state_${statecode} {\n";
		echo "\ttext-align: center;\n";
		echo "\tbackground-color: #" . (getConfigVar ('color_' . $statecode)) . ";\n";
		echo "\tfont: bold 10px Verdana, sans-serif;\n";
		echo "}\n\n";
	}
	echo '</style>';
}

function render8021QOrderForm ($some_id)
{
	function printNewItemTR ()
	{
		$all_vswitches = getVLANSwitches();
		global $pageno;
		printOpFormIntro ('add');
		echo '<tr>';
		if ($pageno != 'object')
		{
			echo '<td>';
			// hide any object, which is already in the table
			$options = array();
			foreach (getNarrowObjectList ('VLANSWITCH_LISTSRC') as $object_id => $object_dname)
				if (!in_array ($object_id, $all_vswitches))
					$options[$object_id] = $object_dname;
			printSelect ($options, array ('name' => 'object_id', 'tabindex' => 101));
			echo '</td>';
		}
		if ($pageno != 'vlandomain')
		{
			$options = array();
			foreach (getVLANDomainList() as $vdom_id => $vdom_info)
				$options[$vdom_id] = $vdom_info['description'];
			echo '<td>' . getSelect ($options, array ('name' => 'vdom_id', 'tabindex' => 102), getConfigVar ('DEFAULT_VDOM_ID')) . '</td>';
		}
		if ($pageno != 'vst')
		{
			$options = array();
			foreach (getVLANSwitchTemplates() as $vst_id => $vst_info)
				$options[$vst_id] = $vst_info['description'];
			echo '<td>' . getSelect ($options, array ('name' => 'vst_id', 'tabindex' => 103), getConfigVar ('DEFAULT_VST_ID')) . '</td>';
		}
		echo '<td>' . getImageHREF ('Attach', 'set', TRUE, 104) . '</td></tr></form>';
	}
	global $pageno;
	$minuslines = array(); // indexed by object_id, which is unique
	switch ($pageno)
	{
	case 'object':
		if (NULL !== $vswitch = getVLANSwitchInfo ($some_id))
			$minuslines[$some_id] = array
			(
				'vdom_id' => $vswitch['domain_id'],
				'vst_id' => $vswitch['template_id'],
			);
		break;
	case 'vlandomain':
		foreach (getVLANDomainSwitches ($some_id) as $vswitch)
			$minuslines[$vswitch['object_id']] = array
			(
				'vdom_id' => $some_id,
				'vst_id' => $vswitch['template_id'],
			);
		break;
	case 'vst':
		$vst = getVLANSwitchTemplate ($some_id);
		foreach ($vst['switches'] as $vswitch)
			$minuslines[$vswitch['object_id']] = array
			(
				'vdom_id' => $vswitch['domain_id'],
				'vst_id' => $some_id,
			);
		break;
	default:
		throw new InvalidArgException ('pageno', $pageno, 'this function only works for a fixed set of values');
	}
	echo "<br><table border=0 cellspacing=0 cellpadding=3 align=center>";
	echo '<tr>';
	if ($pageno != 'object')
		echo '<th>switch</th>';
	if ($pageno != 'vlandomain')
		echo '<th>domain</th>';
	if ($pageno != 'vst')
		echo '<th>template</th>';
	echo '<th>&nbsp;</th></tr>';
	// object_id is a UNIQUE in VLANSwitch table, so there is no sense
	// in a "plus" row on the form, when there is already a "minus" one
	if
	(
		getConfigVar ('ADDNEW_AT_TOP') == 'yes' and
		($pageno != 'object' or !count ($minuslines))
	)
		printNewItemTR();
	$vdomlist = getVLANDomainList();
	$vstlist = getVLANSwitchTemplates();
	foreach ($minuslines as $item_object_id => $item)
	{
		echo '<tr>';
		if ($pageno != 'object')
		{
			$object = spotEntity ('object', $item_object_id);
			echo "<td>${object['dname']}</td>";
		}
		if ($pageno != 'vlandomain')
			echo '<td>' . $vdomlist[$item['vdom_id']]['description'] . '</td>';
		if ($pageno != 'vst')
			echo '<td>' . $vstlist[$item['vst_id']]['description'] . '</td>';
		echo '<td><a href="' . makeHrefProcess (array
		(
			'op' => 'del',
			'object_id' => $item_object_id,
			// These below are only necessary for redirect to work,
			// actual deletion uses object_id only.
			'vdom_id' => $item['vdom_id'],
			'vst_id' => $item['vst_id'],
		)) . '">';
		echo getImageHREF ('Cut', 'unset') . '</a></td></tr>';
	}
	if
	(
		getConfigVar ('ADDNEW_AT_TOP') != 'yes' and
		($pageno != 'object' or !count ($minuslines))
	)
		printNewItemTR();
	echo '</table>';
}

function render8021QStatus ()
{
	echo '<table border=0 class=objectview cellspacing=0 cellpadding=0>';
	echo '<tr valign=top><td class=pcleft width="30%">';
	if (!count ($vdlist = getVLANDomainList()))
		startPortlet ('no VLAN domains');
	else
	{
		startPortlet ('VLAN domains (' . count ($vdlist) . ')');
		echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
		echo '<tr><th>description</th><th>VLANs</th><th>switches</th><th>';
		echo getImageHREF ('net') . '</th></tr>';
		$stats = array();
		foreach (array ('vlanc', 'switchc', 'ipv4netc') as $cname)
			$stats[$cname] = 0;
		foreach (getVLANDomainList() as $vdom_id => $dominfo)
		{
			foreach (array ('vlanc', 'switchc', 'ipv4netc') as $cname)
				$stats[$cname] += $dominfo[$cname];
			echo "<tr align=left><td><a href='";
			echo makeHref (array ('page' => 'vlandomain', 'vdom_id' => $vdom_id)) . "'>";
			echo niftyString ($dominfo['description']) . "</a></td><td>${dominfo['vlanc']}</td>";
			echo "<td>${dominfo['switchc']}</td><td>${dominfo['ipv4netc']}</td></tr>";
		}
		if (count ($vdlist) > 1)
		{
			echo '<tr align=left><td>total:</td>';
			foreach (array ('vlanc', 'switchc', 'ipv4netc') as $cname)
				echo '<td>' . $stats[$cname] . '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
	finishPortlet();

	echo '</td><td class=pcleft width="30%">';

	if (!count ($vstlist = getVLANSwitchTemplates()))
		startPortlet ('no switch templates');
	else
	{
		startPortlet ('switch templates (' . count ($vstlist) . ')');
		echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
		echo '<tr><th>description</th><th>rules</th><th>switches</th></tr>';
		foreach (getVLANSwitchTemplates() as $vst_id => $vst_info)
		{
			echo "<tr align=left><td><a href='";
			echo makeHref (array ('page' => 'vst', 'vst_id' => $vst_id)) . "'>";
			echo niftyString ($vst_info['description']) . "</a></td><td>${vst_info['rulec']}</td>";
			echo "<td>${vst_info['switchc']}</td></tr>";
		}
		echo '</table>';
	}
	finishPortlet();

	echo '</td><td class=pcright>';

	if (!count ($dplan = get8021QDeployPlan()))
		startPortlet ('deploy plan is empty');
	else
	{
		global $nextorder;
		startPortlet ('deploy plan');
		echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
		echo '<tr><th>switch</th><th>age</th></tr>';
		$order = 'odd';
		foreach ($dplan as $item)
		{
			echo "<tr class=row_${order}><td>";
			renderCell (spotEntity ('object', $item['object_id']));
			echo '</td><td>';
			echo $item['age'];
			echo '</td></tr>';
			$order = $nextorder[$order];
		}
		echo '</table>';
	}
	finishPortlet();
	echo '</td></tr></table>';
}

function renderVLANDomainListEditor ()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr><td>';
		printImageHREF ('create', 'create domain', TRUE, 104);
		echo '</td><td>';
		echo '<input type=text name=vdom_descr tabindex=102>';
		echo '</td><td>';
		printImageHREF ('create', 'create domain', TRUE, 103);
		echo '</td></tr></form>';
	}
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>&nbsp;</th><th>description</th><th>&nbsp</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach (getVLANDomainList() as $vdom_id => $dominfo)
	{
		printOpFormIntro ('upd', array ('vdom_id' => $vdom_id));
		echo '<tr><td>';
		if ($dominfo['switchc'] or $dominfo['vlanc'])
			printImageHREF ('nodestroy', 'domain used elsewhere');
		else
		{
			echo '<a href="';
			echo makeHrefProcess (array ('op' => 'del', 'vdom_id' => $vdom_id)) . '">';
			printImageHREF ('destroy', 'delete domain');
			echo '</a>';
		}
		echo '</td><td><input name=vdom_descr type=text value="';
		echo niftyString ($dominfo['description'], 0) . '">';
		echo '</td><td>';
		printImageHREF ('save', 'update description', TRUE);
		echo '</td></tr></form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo '</table>';
}

function renderVLANDomain ($vdom_id)
{
	global $nextorder;
	$mydomain = getVLANDomain ($vdom_id);
	echo '<table border=0 class=objectview cellspacing=0 cellpadding=0>';
	echo '<tr><td colspan=2 align=center><h1>' . niftyString ($mydomain['description']);
	echo '</h1></td></tr>';
	echo "<tr><td class=pcleft width='50%'>";
	if (!count ($mydomain['switchlist']))
		startPortlet ('no orders');
	else
	{
		startPortlet ('orders (' . count ($mydomain['switchlist']) . ')');
		echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
		$order = 'odd';
		foreach (array_keys ($mydomain['switchlist']) as $object_id)
		{
			echo "<tr class=row_${order}><td>";
			renderCell (spotEntity ('object', $object_id));
			echo '</td></tr>';
			$order = $nextorder[$order];
		}
		echo '</table>';
	}
	finishPortlet();

	echo '</td><td class=pcright>';

	if (!count ($myvlans = getDomainVLANs ($vdom_id)))
		startPortlet ('no VLANs');
	else
	{
		startPortlet ('VLANs (' . count ($myvlans) . ')');
		$order = 'odd';
		global $vtdecoder;
		echo '<table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>';
		echo '<tr><th>VLAN ID</th><th>propagation</th><th>';
		printImageHREF ('net');
		echo '</th><th>description</th></tr>';
		foreach ($myvlans as $vlan_id => $vlan_info)
		{
			echo "<tr class=row_${order}><td class=tdright><a href='";
			echo makeHref (array ('page' => 'vlan', 'vlan_ck' => "${vdom_id}-${vlan_id}"));
			echo "'>${vlan_id}</a></td>";
			echo '<td>' . $vtdecoder[$vlan_info['vlan_type']] . '</td>';
			echo '<td>' . ($vlan_info['netc'] ? $vlan_info['netc'] : '&nbsp;') . '</td>';
			echo "<td class=tdleft>${vlan_info['vlan_descr']}</td></tr>";
			$order = $nextorder[$order];
		}
		echo '</table>';
	}
	finishPortlet();
	echo '</td></tr></table>';
}

function renderVLANDomainVLANList ($vdom_id)
{
	function printNewItemTR ()
	{
		global $vtoptions;
		printOpFormIntro ('add');
		echo '<tr><td>';
		printImageHREF ('create', 'add VLAN', TRUE, 110);
		echo '</td><td>';
		echo '<input type=text name=vlan_id size=4 tabindex=101>';
		echo '</td><td>';
		printSelect ($vtoptions, array ('name' => 'vlan_type', 'tabindex' => 102), 'ondemand');
		echo '</td><td>';
		echo '<input type=text name=vlan_descr tabindex=103>';
		echo '</td><td>';
		printImageHREF ('create', 'add VLAN', TRUE, 110);
		echo '</td></tr></form>';
	}
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>&nbsp;</th><th>ID</th><th>propagation</th><th>description</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	global $vtoptions;
	foreach (getDomainVLANs ($vdom_id) as $vlan_id => $vlan_info)
	{
		printOpFormIntro ('upd', array ('vlan_id' => $vlan_id));
		echo '<tr><td><a href="';
		echo makeHrefProcess (array ('op' => 'del', 'vdom_id' => $vdom_id, 'vlan_id' => $vlan_id)) . '">';
		printImageHREF ('destroy', 'delete VLAN');
		echo '</a></td><td class=tdright><tt>' . $vlan_id . '</tt></td><td>';
		printSelect ($vtoptions, array ('name' => 'vlan_type'), $vlan_info['vlan_type']);
		echo '</td><td>';
		echo '<input name=vlan_descr type=text value="' . htmlspecialchars ($vlan_info['vlan_descr']) . '">';
		echo '</td><td>';
		printImageHREF ('save', 'update description', TRUE);
		echo '</td></tr></form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo '</table>';
}

// Show a list of 802.1Q-eligible ports in any way, but when one of
// them is selected as current, also display a form for its setup.
function renderObject8021QPorts ($object_id)
{
	global $pageno, $tabno, $sic;
	$vswitch = getVLANSwitchInfo ($object_id);
	$vdom = getVLANDomain ($vswitch['domain_id']);
	$req_port_name = array_key_exists ('port_name', $sic) ? $sic['port_name'] : '';
	$desired_config = apply8021QOrder ($vswitch['template_id'], getDesired8021QConfig ($object_id));
	uksort ($desired_config, 'sortTokenize');
	$uplinks = produceUplinkPorts ($vdom['vlanlist'], $desired_config);
	echo '<table border=0 width="100%"><tr valign=top><td class=tdleft width="30%">';
	// port list
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>port</th><th>last&nbsp;saved&nbsp;config</th>';
	echo $req_port_name == '' ? '<th>new&nbsp;config</th></tr>' : '</tr>';
	if ($req_port_name == '');
		printOpFormIntro ('save', array ('mutex_rev' => $vswitch['mutex_rev']));
	$nports = 0; // count only access ports
	foreach ($desired_config as $port_name => $port)
	{
		$text_left = serializeVLANPack ($port);
		// decide on row class
		switch ($port['vst_role'])
		{
		case 'none':
			if ($port['mode'] == 'none')
				continue; // early miss
			$trclass = 'trerror'; // stuck ghost port
			$text_right = '&nbsp;';
			break;
		case 'uplink':
			$text_right = serializeVLANPack ($uplinks[$port_name]);
			$trclass = $text_left == $text_right ? 'trbusy' : 'trwarning';
			break;
		case 'trunk':
			$trclass = $port['vst_role'] == $port['mode'] ? 'trbusy' : 'trwarning';
			$linkparams = array
			(
				'page' => $pageno,
				'tab' => $tabno,
				'object_id' => $object_id,
			);
			if ($port_name == $req_port_name)
			{
				$imagename = 'Zooming';
				$imagetext = 'zoom out';
			}
			else
			{
				$imagename = 'Zoom';
				$imagetext = 'zoom in';
				$linkparams['port_name'] = $port_name;
			}
			$text_right = "<a href='" . makeHref ($linkparams) . "'>"  .
				getImageHREF ($imagename, $imagetext) . '</a>';
			break;
		case 'access':
			$trclass = $port['vst_role'] == $port['mode'] ? 'trbusy' : 'trwarning';
			if ($req_port_name != '')
			{
				// don't render a form for access ports, when a trunk port is zoomed
				$text_right = '&nbsp;';
				break;
			}
			$text_right = "<input type=hidden name=pn_${nports} value=${port_name}>";
			$text_right .= "<input type=hidden name=pm_${nports} value=access>";
			$wrt_vlans = iosParseVLANString ($port['wrt_vlans']);
			$options = array();
			// list only new options, which are listen in domain and fit into VST
			foreach ($vdom['vlanlist'] as $vlan_id => $vlan_info)
				if
				(
					$vlan_id != $port['native'] and
					(!count ($wrt_vlans) or in_array ($vlan_id, $wrt_vlans))
				)
					$options[$vlan_id] = formatVLANName ($vlan_info, TRUE);
			ksort ($options);
			$options['same'] = '-- no change --';
			$text_right .= getSelect ($options, array ('name' => "pnv_${nports}"), 'same');
			$nports++;
			break;
		}
		echo "<tr class=${trclass}><td>${port_name}</td><td>${text_left}</td><td>${text_right}</td></tr>";
	}
	if ($req_port_name == '' and $nports)
		echo "<input type=hidden name=nports value=${nports}>" .
			'<tr><td colspan=3 class=tdcenter>' .
			getImageHREF ('SAVE', 'save configuration', TRUE) .
			'</td></tr></form>';
	echo '</table>';
	echo '</td>';
	// configuration of currently selected port, if any
	if (!array_key_exists ($req_port_name, $desired_config))
		echo '<td colspan=2>&nbsp;</td>';
	else
		renderPortVLANConfig
		(
			$vswitch,
			$vdom,
			$req_port_name,
			$desired_config[$req_port_name]
		);
	echo '</tr></table>';
}

function renderPortVLANConfig ($vswitch, $vdom, $port_name, $vlanport)
{
	if (!count ($vdom['vlanlist']))
	{
		echo '<td colspan=2>(configured VLAN domain is empty)</td>';
		return;
	}
	$formextra = array
	(
		'mutex_rev' => $vswitch['mutex_rev'],
		'nports' => 1,
		'pn_0' => $port_name,
		'pm_0' => $vlanport['vst_role'], // calling function must make sure it is not 'uplink'
	);
	printOpFormIntro ('save', $formextra);
	echo '<td width="35%">';
	echo '<table border=0 cellspacing=0 cellpadding=3 align=center>';
	echo '<tr><th colspan=2>allowed</th></tr>';
	foreach ($vdom['vlanlist'] as $vlan_id => $vlan_info)
	{
		if (in_array ($vlan_id, $vlanport['allowed']))
		{
			$selected = ' checked';
			$class = 'seltagbox';
		}
		else
		{
			$selected = '';
			$class = 'tagbox';
		}
		// A real relation to an alien VLANs is shown for a
		// particular port, but it cannot be changed by user.
		if ($vlan_info['vlan_type'] == 'alien')
			$selected .= ' disabled';
		echo "<tr><td colspan=2 class=${class}>";
		echo "<label><input type=checkbox name='pav_0[]' value='${vlan_id}'${selected}> ";
		echo formatVLANName ($vlan_info) . "</label></td></tr>";
	}
	echo '</table>';
	echo '</td><td width="35%">';
	// rightmost table also contains form buttons
	echo '<table border=0 cellspacing=0 cellpadding=3 align=center>';
	echo '<tr><th colspan=2>native</th></tr>';
	if (!count ($vlanport['allowed']))
		echo '<tr><td colspan=2>(no allowed VLANs for this port)</td></tr>';
	else
	{
		$native_options = array (0 => '-- NONE --');
		foreach ($vlanport['allowed'] as $allowed_id)
			$native_options[$allowed_id] = formatVLANName ($vdom['vlanlist'][$allowed_id]);
		foreach ($native_options as $vlan_id => $vlan_text)
		{
			if ($vlan_id == $vlanport['native'])
			{
				$selected = ' checked';
				$class = 'seltagbox';
			}
			else
			{
				$selected = '';
				$class = 'tagbox';
			}
			// When one or more alien VLANs are present on port's list of allowed VLANs,
			// they are shown among radio options, but disabled, so that the user cannot
			// break traffic of these VLANs. In addition to that, when port's native VLAN
			// is set to one of these alien VLANs, the whole group of radio buttons is
			// disabled. These measures make it harder for the system to break a VLAN,
			// which is explicitly protected from it.
			if
			(
				(array_key_exists ($vlanport['native'], $vdom['vlanlist']) and
				$vdom['vlanlist'][$vlanport['native']]['vlan_type'] == 'alien')
				or
				(array_key_exists ($vlan_id, $vdom['vlanlist']) and
				$vdom['vlanlist'][$vlan_id]['vlan_type'] == 'alien')
			)
				$selected .= ' disabled';
			echo "<tr><td colspan=2 class=${class}>";
			echo "<label><input type=radio name='pnv_0' value='${vlan_id}'${selected}> ";
			echo $vlan_text . "</label></td></tr>";
		}
	}
	echo '<tr><td class=tdleft>';
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo '</form></td><td class=tdright>';
	if (!count ($vlanport['allowed']))
		printImageHREF ('CLEAR gray');
	else
	{
		printOpFormIntro ('save', $formextra);
		printImageHREF ('CLEAR', 'Unassign all VLANs', TRUE);
		echo '</form>';
	}
	echo '</td></tr></table>';
	echo '</td>';
}

function renderVLANInfo ($vlan_ck)
{
	global $vtoptions, $nextorder;
	$vlan = getVLANInfo ($vlan_ck);
	echo '<table border=0 class=objectview cellspacing=0 cellpadding=0>';
	echo "<tr><td colspan=2 align=center><h1>${mydomain['description']}</h1></td></tr>";
	echo "<tr><td class=pcleft width='50%'>";
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>";
	echo "<tr><th width='50%' class=tdright>Domain:</th><td class=tdleft>";
	echo niftyString ($vlan['domain_descr'], 0) . '</td></tr>';
	echo "<tr><th width='50%' class=tdright>VLAN ID:</th><td class=tdleft>${vlan['vlan_id']}</td></tr>";
	if (strlen ($vlan['vlan_descr']))
		echo "<tr><th width='50%' class=tdright>Description:</th><td class=tdleft>" .
			niftyString ($vlan['vlan_descr'], 0) . "</td></tr>";
	echo "<tr><th width='50%' class=tdright>Propagation:</th><td class=tdleft>" . $vtoptions[$vlan['vlan_prop']] . "</td></tr>";
	echo '</table>';
	finishPortlet();
	startPortlet ('networks');
	if (!count ($vlan['ipv4nets']))
		echo '(none)';
	else
	{
		$order = 'odd';
		echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
		echo '<tr><th>';
		printImageHREF ('net');
		echo '</th><th>';
		printImageHREF ('text');
		echo '</th></tr>';
		foreach ($vlan['ipv4nets'] as $netid)
		{
			$net = spotEntity ('ipv4net', $netid);
			#echo "<tr class=row_${order}><td>";
			echo '<tr><td>';
			renderCell ($net);
			echo '</td><td>' . (mb_strlen ($net['comment']) ? niftyString ($net['comment']) : '&nbsp;');
			echo '</td></tr>';
			$order = $nextorder[$order];
		}
		echo '</table>';
	}
	finishPortlet();
	echo '</td><td class=pcright>';
	startPortlet ('ports');
	if (!count ($confports = getVLANConfiguredPorts ($vlan_ck)))
		echo '(none)';
	else
	{
		global $nextorder;
		$order = 'odd';
		echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
		echo '<tr><th>switch</th><th>ports</th></tr>';
		foreach ($confports as $switch_id => $portlist)
		{
			usort ($portlist, 'sortTokenize');
			echo "<tr class=row_${order} valign=top><td>";
			renderCell (spotEntity ('object', $switch_id));
			echo '</td><td class=tdleft><ul>';
			foreach ($portlist as $port_name)
				echo "<li>${port_name}</li>";
			echo '</ul></td></tr>';
			$order = $nextorder[$order];
		}
		echo '</table>';
	}
	finishPortlet();
	echo '</td></tr></table>';
}

function renderVLANIPv4 ($some_id)
{
	function printNewItemTR ($sname, $options)
	{
		if (!count ($options))
			return;
		printOpFormIntro ('bind');
		echo '<tr><td>' . getNiftySelect ($options, array ('name' => $sname, 'tabindex' => 101));
		echo '</td><td>' . getImageHREF ('Attach', 'bind', TRUE, 102) . '</td></tr></form>';
	}
	global $pageno;
	$minuslines = array();
	$plusoptions = array();
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr>';
	switch ($pageno)
	{
	case 'vlan':
		echo '<th>' . getImageHREF ('net') . '</th>';
		$vlan = getVLANInfo ($some_id);
		foreach ($vlan['ipv4nets'] as $net_id)
			$minuslines[] = array
			(
				'ipv4net_id' => $net_id,
				'domain_id' => $vlan['domain_id'],
				'vlan_id' => $vlan['vlan_id'],
			);
		// Any VLAN can link to any network, which isn't yet linked to current domain.
		foreach (getVLANIPv4Options ($vlan['domain_id']) as $net_id)
		{
			$netinfo = spotEntity ('ipv4net', $net_id);
			if (considerConfiguredConstraint ($netinfo, 'VLANIPV4NET_LISTSRC'))
				$plusoptions['other'][$net_id] =
					$netinfo['ip'] . '/' . $netinfo['mask'] . ' ' . $netinfo['name'];
		}
		$select_name = 'id';
		break;
	case 'ipv4net':
		echo '<th>VLAN</th>';
		$netinfo = spotEntity ('ipv4net', $some_id);
		amplifyCell ($netinfo);
		// find out list of VLAN domains, where the current network is already linked
		foreach ($netinfo['8021q'] as $item)
			$minuslines[] = array
			(
				'ipv4net_id' => $netinfo['id'],
				'domain_id' => $item['domain_id'],
				'vlan_id' => $item['vlan_id'],
			);
		// offer all other
		foreach (getVLANDomainList() as $dominfo)
			if (NULL === scanArrayForItem ($minuslines, 'domain_id', $dominfo['id']))
				foreach (getDomainVLANs ($dominfo['id']) as $vlaninfo)
					$plusoptions[$dominfo['description']][$dominfo['id']. '-' . $vlaninfo['vlan_id']] =
						$vlaninfo['vlan_id'] . ' (' . $vlaninfo['netc'] . ') ' . $vlaninfo['vlan_descr'];
		$select_name = 'vlan_ck';
		break;
	}
	echo '<th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($select_name, $plusoptions);
	foreach ($minuslines as $item)
	{
		echo '<tr><td>';
		switch ($pageno)
		{
		case 'vlan':
			renderCell (spotEntity ('ipv4net', $item['ipv4net_id']));
			break;
		case 'ipv4net':
			$vlaninfo = getVLANInfo ($item['domain_id'] . '-' . $item['vlan_id']);
			echo formatVLANName ($vlaninfo) . ' @' . niftyString ($vlaninfo['domain_descr']);
			break;
		}
		echo '</td><td><a href="';
		echo makeHrefProcess
		(
			array
			(
				'op' => 'unbind',
				'id' => $item['ipv4net_id'],
				'vlan_ck' => $item['domain_id'] . '-' . $item['vlan_id']
			)
		);
		echo '">' . getImageHREF ('Cut', 'unbind') . '</a></td></tr>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ($select_name, $plusoptions);
	echo '</table>';
}

function renderObject8021QSync ($object_id)
{
	global $pageno, $tabno;
	try
	{
		$running_config = getRunning8021QConfig ($object_id);
	}
	catch (RuntimeException $re)
	{
		showWarning ('Could not retrieve running-config of this device with the following error:<br>' . $re->getMessage(), __FUNCTION__);
		return;
	}
	$formports = getDesired8021QConfig ($object_id);
	// The form is based on the "desired" list, which has every
	// 802.1Q-eligible port of the object plus any port names
	// already stored in the database. This list may be further
	// extended by the "running" list of the actual device.
	foreach (array_keys ($formports) as $port_name)
	{
		$formports[$port_name]['running_mode'] = 'none';
		$formports[$port_name]['running_allowed'] = array();
		$formports[$port_name]['running_native'] = 0;
	}
	foreach ($running_config['portdata'] as $port_name => $item)
	{
		if (!array_key_exists ($port_name, $formports))
			$formports[$port_name] = array
			(
				'mode' => 'none',
				'allowed' => array(),
				'native' => 0,
			);
		$formports[$port_name]['running_mode'] = $item['mode'];
		$formports[$port_name]['running_allowed'] = $item['allowed'];
		$formports[$port_name]['running_native'] = $item['native'];
	}
	uksort ($formports, 'sortTokenize');
	$vswitch = getVLANSwitchInfo ($object_id);
	$formports = apply8021QOrder ($vswitch['template_id'], $formports);
	$domvlans = array_keys (getDomainVLANs ($vswitch['domain_id']));
	printOpFormIntro ('sync', array ('mutex_rev' => $vswitch['mutex_rev']));
	$nrows = count ($formports);
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th rowspan=2>port</th><th rowspan=2>last&nbsp;saved&nbsp;config</th><th colspan=3>winner</th>';
	echo '<th rowspan=2>running&nbsp;config</th></tr><tr>';
	foreach (array ('left', 'asis', 'right') as $pos)
		echo "<th><input type=radio name=column_radio value=${pos} " .
			"onclick=\"checkColumnOfRadios('i_', ${nrows}, '_${pos}')\"></th>";
	echo '</tr>';
	$rownum = 0;
	foreach ($formports as $port_name => $port)
	{
		$desired_cfgstring = serializeVLANPack (array ('mode' => $port['mode'], 'native' => $port['native'], 'allowed' => $port['allowed']));
		$running_cfgstring = serializeVLANPack (array ('mode' => $port['running_mode'], 'native' => $port['running_native'], 'allowed' => $port['running_allowed']));
		// decide on the radio inputs now
		$radio = array ('left' => TRUE, 'asis' => TRUE, 'right' => TRUE);
		$checked = array ('left' => '', 'asis' => ' checked', 'right' => '');
		if
		(
			$port['vst_role'] == 'uplink' or
			($port['vst_role'] != 'access' and $port['vst_role'] != 'trunk') or
			$desired_cfgstring == $running_cfgstring
		)
			$skip_inputs = TRUE;
		else
		{
			$skip_inputs = FALSE;
			// enable, but consider each option independently
			if ($desired_cfgstring == 'none')
				$radio['left'] = FALSE;
			// if any of the running VLANs isn't in the domain...
			if (count (array_diff ($port['running_allowed'], $domvlans)))
				$radio['right'] = FALSE;
		}
		if ($desired_cfgstring == $running_cfgstring)
			// locked row : normal row
			$trclass = $port['vst_role'] == 'none' ? 'trwarning' : 'trbusy';
		else
			// locked difference : fixable difference
			$trclass = $port['vst_role'] == 'none' ? 'trerror' : 'trwarning';
		echo "<tr class=${trclass}><td>${port_name}</td>";
		if ($skip_inputs)
			echo "<td>${desired_cfgstring}</td>";
		else
			echo "<td><label for=i_${rownum}_left>${desired_cfgstring}</label></td>";
		foreach ($radio as $pos => $enabled)
		{
			echo '<td>';
			if (!$enabled or $skip_inputs)
				echo '&nbsp;';
			else
				echo "<input id=i_${rownum}_${pos} name=i_${rownum} type=radio value=${pos}" . $checked[$pos] . ">";
			echo '</td>';
		}
		if ($skip_inputs)
			echo "<td>${running_cfgstring}</td>";
		else
			echo "<td><label for=i_${rownum}_right>${running_cfgstring}</label></td>";
		echo '</tr>';
		if (!$skip_inputs)
		{
			echo "<input type=hidden name=rm_${rownum} value=${port['running_mode']}>";
			echo "<input type=hidden name=rn_${rownum} value=${port['running_native']}>";
			foreach ($port['running_allowed'] as $a)
				echo "<input type=hidden name=ra_${rownum}[] value=${a}>";
			echo "<input type=hidden name=pn_${rownum} value='" . htmlspecialchars ($port_name) . "'>";
		}
		$rownum += $skip_inputs ? 0 : 1;
	}
	echo "<input type=hidden name=nrows value=${rownum}>";
	echo '<tr><td colspan=6 align=center>';
	printImageHREF ('CORE', 'sumbit all updates', TRUE);
	echo '</td></tr>';
	echo '</table>';
	echo '</form>';
}

function renderVSTListEditor()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr>';
		echo '<td>' . getImageHREF ('create', 'create template', TRUE, 104) . '</td>';
		echo '<td><input type=text name=vst_descr tabindex=101></td>';
		echo '<td><input type=text name=vst_maxvlans tabindex=102></td>';
		echo '<td>' . getImageHREF ('create', 'create domain', TRUE, 103) . '</td>';
		echo '</tr></form>';
	}
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>&nbsp;</th><th>description</th><th>max local VLANs on a switch</th><th>&nbsp</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach (getVLANSwitchTemplates() as $vst_id => $vst_info)
	{
		printOpFormIntro ('upd', array ('vst_id' => $vst_id));
		echo '<tr><td>';
		if ($vst_info['switchc'])
			printImageHREF ('nodestroy', 'template used elsewhere');
		else
		{
			echo '<a href="' . makeHrefProcess (array ('op' => 'del', 'vst_id' => $vst_id)) . '">';
			echo getImageHREF ('destroy', 'delete domain') . '</a>';
		}
		echo '</td>';
		echo '<td><input name=vst_descr type=text value="' . niftyString ($vst_info['description'], 0) . '"></td>';
		echo "<td><input name=vst_maxvlans type=text value=${vst_info['max_local_vlans']}></td>";
		echo '<td>' . getImageHREF ('save', 'update template', TRUE) . '</td>';
		echo '</tr></form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo '</table>';
}

function renderVST ($vst_id)
{
	global $nextorder;
	$vst = getVLANSwitchTemplate ($vst_id);
	echo '<table border=0 class=objectview cellspacing=0 cellpadding=0>';
	echo "<tr><td colspan=2 align=center><h1>${vst['description']}</h1><h2>";
	echo "<tr><td class=pcleft width='50%'>";
	if (!count ($vst['rules']))
		startPortlet ('no rules');
	else
	{
		startPortlet ('rules (' . count ($vst['rules']) . ')');
		echo '<table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>';
		echo '<tr><th>sequence</th><th>regexp</th><th>role</th><th>VLAN IDs</th></tr>';
		$order = 'odd';
		foreach ($vst['rules'] as $item)
		{
			echo "<tr class=row_${order} align=left>";
			echo "<td>${item['rule_no']}</td><td><tt>${item['port_pcre']}</tt></td>";
			echo "<td>${item['port_role']}</td><td>${item['wrt_vlans']}</td></tr>";
			$order = $nextorder[$order];
		}
		echo '</table>';
	}
	finishPortlet();
	echo '</td><td class=pcright>';
	if (!count ($vst['switches']))
		startPortlet ('no orders');
	else
	{
		startPortlet ('orders (' . count ($vst['switches']) . ')');
		echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
		$order = 'odd';
		foreach (array_keys ($vst['switches']) as $object_id)
		{
			echo "<tr class=row_${order}><td>";
			renderCell (spotEntity ('object', $object_id));
			echo '</td></tr>';
			$order = $nextorder[$order];
		}
		echo '</table>';
	}
	finishPortlet();
	echo '</td></tr></table>';
}

function renderVSTEditor ($vst_id)
{
	$vst = getVLANSwitchTemplate ($vst_id);
	echo '<center><h1>' . niftyString ($vst['description']) . '</h1></center>';
	echo '<table border=0 cellpadding=10 cellpadding=1 align=center>';
	printOpFormIntro ('upd');
	echo '<tr><td class=tdright><label for=input1>Description:</label></td>';
	echo "<td class=tdleft><input type=text name=vst_descr id=input1 maxlength=255 value='";
	echo niftyString ($vst['description'], 0) . "'></td></tr>";
	echo '<tr><td class=tdright><label for=input2>Max local VLANs:</label></td>';
	echo "<td class=tdleft><input type=text name=vst_maxvlans id=input2 value=";
	echo $vst['max_local_vlans'] . '></td></tr>';
	echo '<tr><td colspan=2 class=tdcenter>' . getImageHREF ('SAVE', 'Save changes', TRUE) . '</td></tr>';
	echo '</form></table>';
}

function renderVSTRulesEditor ($vst_id)
{
	function printNewItemTR ($port_role_options)
	{
		printOpFormIntro ('add');
		echo '<tr>';
		echo '<td>' . getImageHREF ('add', 'add rule', TRUE, 106) . '</td>';
		echo '<td><input type=text tabindex=101 name=rule_no size=3></td>';
		echo '<td><input type=text tabindex=102 name=port_pcre></td>';
		echo '<td>' . getSelect ($port_role_options, array ('name' => 'port_role', 'tabindex' => 103), 'trunk') . '</td>';
		echo '<td><input type=text tabindex=104 name=wrt_vlans></td>';
		echo '<td>' . getImageHREF ('add', 'add rule', TRUE, 105) . '</td>';
		echo '</tr></form>';
	}
	$vst = getVLANSwitchTemplate ($vst_id);
	echo '<center><h1>' . niftyString ($vst['description']) . '</h1></center>';
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>&nbsp;</th><th>sequence</th><th>regexp</th><th>role</th><th>VLAN IDs</th><th>&nbsp;</th></tr>';
	$port_role_options = array
	(
		'access' => 'access',
		'trunk' => 'trunk',
		'uplink' => 'uplink',
	);
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($port_role_options);
	foreach ($vst['rules'] as $item)
	{
		printOpFormIntro ('upd', array ('rule_no' => $item['rule_no']));
		echo '<tr>';
		echo '<td><a href="' . makeHrefProcess (array ('op' => 'del', 'vst_id' => $vst_id, 'rule_no' => $item['rule_no'])) . '">';
		echo getImageHREF ('destroy', 'delete rule') . '</a></td>';
		echo "<td><input type=text name=new_rule_no value=${item['rule_no']} size=3></td>";
		echo "<td><input type=text name=port_pcre value='" . niftyString ($item['port_pcre'], 0) . "'></td>";
		echo '<td>' . getSelect ($port_role_options, array ('name' => 'port_role'), $item['port_role']) . '</td>';
		echo "<td><input type=text name=wrt_vlans value='${item['wrt_vlans']}'></td>";
		echo '<td>' . getImageHref ('save', 'update rule', TRUE) . '</td>';
		echo '</tr></form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ($port_role_options);
	echo '</table>';
}

?>
