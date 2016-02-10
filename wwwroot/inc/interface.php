<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*
*
*  This file contains frontend functions for RackTables.
*
*/

require_once 'ajax-interface.php';
require_once 'slb-interface.php';

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
	'point2point' => 'Point-to-point',
);
// address allocation code, IPv4 addresses and objects view
$aac_right = array
(
	'regular' => '',
	'virtual' => '<span class="aac-right" title="' . $aat['virtual'] . '">L</span>',
	'shared' => '<span class="aac-right" title="' . $aat['shared'] . '">S</span>',
	'router' => '<span class="aac-right" title="' . $aat['router'] . '">R</span>',
	'point2point' => '<span class="aac-right" title="' . $aat['point2point'] . '">P</span>',
);
// address allocation code, IPv4 networks view
$aac_left = array
(
	'regular' => '',
	'virtual' => '<span class="aac-left" title="' . $aat['virtual'] . '">L:</span>',
	'shared' => '<span class="aac-left" title="' . $aat['shared'] . '">S:</span>',
	'router' => '<span class="aac-left" title="' . $aat['router'] . '">R:</span>',
	'point2point' => '<span class="aac-left" title="' . $aat['point2point'] . '">P:</span>',
);

$vtdecoder = array
(
	'ondemand' => '',
	'compulsory' => 'P',
#	'alien' => 'NT',
);

$vtoptions = array
(
	'ondemand' => 'auto',
	'compulsory' => 'permanent',
#	'alien' => 'never touch',
);

// This may be populated later onsite, report rendering function will use it.
// See the $systemreport for structure.
$localreports = array();

$CodePressMap = array
(
	'sql' => 'sql',
	'php' => 'php',
	'html' => 'htmlmixed',
	'css' => 'css',
	'js' => 'javascript',
);

$attrtypes = array
(
	'uint' => '[U] unsigned integer',
	'float' => '[F] floating point',
	'string' => '[S] string',
	'dict' => '[D] dictionary record',
	'date' => '[T] date'
);

function showLogoutURL ()
{
	$https = (isset ($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') ? 's' : '';
	$port = (! in_array ($_SERVER['SERVER_PORT'], array (80, 443))) ? ':' . $_SERVER['SERVER_PORT'] : '';
	$pathinfo = pathinfo ($_SERVER['REQUEST_URI']);
	$dirname = $pathinfo['dirname'];
	// add a trailing slash if the installation resides in a subdirectory
	if ($dirname != '/')
		$dirname .= '/';
	printf ('http%s://logout@%s%s?logout', $https, $_SERVER['SERVER_NAME'], $dirname);
}

$quick_links = NULL; // you can override this in your local.php, but first initialize it with getConfiguredQuickLinks()

function renderQuickLinks()
{
	global $quick_links;
	if (! isset ($quick_links))
		$quick_links = getConfiguredQuickLinks();
	echo '<ul class="qlinks">';
	foreach ($quick_links as $link)
		echo '<li><a href="' . $link['href'] . '">' . str_replace (' ', '&nbsp;', $link['title']) . '</a></li>';
	echo '</ul>';
}

function renderInterfaceHTML ($pageno, $tabno, $payload)
{
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title><?php echo getTitle ($pageno); ?></title>
<?php printPageHeaders(); ?>
</head>
<body>
<div class="maintable">
 <div class="mainheader">
  <div style="float: right" class=greeting><a href='index.php?page=myaccount&tab=default'><?php global $remote_displayname; echo $remote_displayname ?></a> [ <a href='<?php showLogoutURL(); ?>'>logout</a> ]</div>
 <?php echo getConfigVar ('enterprise') ?> RackTables <a href="http://racktables.org" title="Visit RackTables site"><?php echo CODE_VERSION ?></a><?php renderQuickLinks() ?>
 </div>
 <div class="menubar"><?php showPathAndSearch ($pageno, $tabno); ?></div>
 <div class="tabbar"><?php showTabs ($pageno, $tabno); ?></div>
 <div class="msgbar"><?php showMessageOrError(); ?></div>
 <div class="pagebar"><?php echo $payload; ?></div>
</div>
</body>
</html>
<?php
}

// Main menu.
function renderIndexItem ($ypageno)
{
	echo (! permitted ($ypageno)) ? "          <td>&nbsp;</td>\n" :
		"          <td>\n" .
		"            <h1><a href='" . makeHref (array ('page' => $ypageno)) . "'>" .
		getPageName ($ypageno) . "<br>\n" . getImageHREF ($ypageno) .
		"</a></h1>\n" .
		"          </td>\n";
}

function renderIndex ()
{
	global $indexlayout;
?>
<table border=0 cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		<td>
			<div style='text-align: center; margin: 10px; '>
			<table width='100%' cellspacing=0 cellpadding=20 class=mainmenu border=0>
<?php
foreach ($indexlayout as $row)
{
	echo '<tr>';
	foreach ($row as $column)
		if ($column === NULL)
			echo '<td>&nbsp;</td>';
		else
			renderIndexItem ($column);
	echo '</tr>';
}
?>
			</table>
			</div>
		</td>
	</tr>
</table>
<?php
}

function getRenderedAlloc ($object_id, $alloc)
{
	$ret = array
	(
		'tr_class' => '',
		'td_name_suffix' => '',
		'td_ip' => '',
		'td_network' => '',
		'td_routed_by' => '',
		'td_peers' => '',
	);
	$dottedquad = $alloc['addrinfo']['ip'];
	$ip_bin = $alloc['addrinfo']['ip_bin'];

	$hl_ip_bin = NULL;
	if (isset ($_REQUEST['hl_ip']))
	{
		$hl_ip_bin = ip_parse ($_REQUEST['hl_ip']);
		addAutoScrollScript ("ip-" . $_REQUEST['hl_ip']);
	}

	$ret['tr_class'] = $alloc['addrinfo']['class'];
	if ($hl_ip_bin === $ip_bin)
		$ret['tr_class'] .= ' highlight';

	// render IP change history
	$ip_title = '';
	$ip_class = '';
	if (isset ($alloc['addrinfo']['last_log']))
	{
		$log = $alloc['addrinfo']['last_log'];
		$ip_title = "title='" .
			htmlspecialchars
			(
				$log['user'] . ', ' . formatAge ($log['time']),
				ENT_QUOTES
			) . "'";
		$ip_class = 'hover-history underline';
	}

	// render IP address td
	global $aac_right;
	$netinfo = spotNetworkByIP ($ip_bin);
	$ret['td_ip'] = "<td class='tdleft'>";
	if (isset ($netinfo))
	{
		$title = $dottedquad;
		if (getConfigVar ('EXT_IPV4_VIEW') != 'yes')
			$title .= '/' . $netinfo['mask'];
		$ret['td_ip'] .= "<a name='ip-$dottedquad' class='$ip_class' $ip_title href='" .
			makeHref (
				array
				(
					'page' => 'ipaddress',
					'hl_object_id' => $object_id,
					'ip' => $dottedquad,
				)
			) . "'>$title</a>";
	}
	else
		$ret['td_ip'] .= "<span class='$ip_class' $ip_title>$dottedquad</span>";
	$ret['td_ip'] .= $aac_right[$alloc['type']];
	if (strlen ($alloc['addrinfo']['name']))
		$ret['td_ip'] .= ' (' . stringForLabel ($alloc['addrinfo']['name']) . ')';
	$ret['td_ip'] .= '</td>';

	// render network and routed_by tds
	$td_class = 'tdleft';
	if (! isset ($netinfo))
	{
		$ret['td_network'] = "<td class='$td_class sparenetwork'>N/A</td>";
		$ret['td_routed_by'] = $ret['td_network'];
	}
	else
	{
		$ret['td_network'] = "<td class='$td_class'>" .
			getOutputOf ('renderCell', $netinfo) . '</td>';

		// render "routed by" td
		if ($display_routers = (getConfigVar ('IPV4_TREE_RTR_AS_CELL') == 'none'))
			$ret['td_routed_by'] = '<td>&nbsp;</td>';
		else
		{
			$other_routers = array();
			foreach (findNetRouters ($netinfo) as $router)
				if ($router['id'] != $object_id)
					$other_routers[] = $router;
			if (count ($other_routers))
				$ret['td_routed_by'] = getOutputOf ('printRoutersTD', $other_routers, $display_routers);
			else
				$ret['td_routed_by'] = "<td class='$td_class'>&nbsp;</td>";
		}
	}

	// render peers td
	$ret['td_peers'] = "<td class='$td_class'>";
	$prefix = '';
	$separator = '; ';
	if ($alloc['addrinfo']['reserved'] == 'yes')
	{
		$ret['td_peers'] .= $prefix . '<strong>RESERVED</strong>';
		$prefix = $separator;
	}
	foreach ($alloc['addrinfo']['allocs'] as $allocpeer)
		if ($allocpeer['object_id'] != $object_id)
		{
			$ret['td_peers'] .= $prefix . makeIPAllocLink ($ip_bin, $allocpeer);
			$prefix = $separator;
		}
		elseif ($allocpeer['type'] == 'point2point' && isset ($netinfo))
		{
			// show PtP peers in the IP network
			$addrlist = isset ($netinfo['own_addrlist']) ? $netinfo['own_addrlist'] : getIPAddrList ($netinfo, IPSCAN_DO_ALLOCS);
			foreach (getPtPNeighbors ($ip_bin, $addrlist) as $p_ip_bin => $p_alloc_list)
				foreach ($p_alloc_list as $p_alloc)
				{
					$ret['td_peers'] .= $prefix . '&harr;&nbsp;' . makeIPAllocLink ($p_ip_bin, $p_alloc);
					$prefix = $separator;
				}
		}

	$ret['td_peers'] .= '</td>';

	return $ret;
}

function renderLocationFilterPortlet ()
{
	// Recursive function used to build the location tree
	function renderLocationCheckbox ($subtree, $level = 0)
	{
		$self = __FUNCTION__;

		foreach ($subtree as $location_id => $location)
		{
			echo "<div class=tagbox style='text-align:left; padding-left:" . ($level * 16) . "px;'>";
			$checked = (! isset ($_SESSION['locationFilter']) || in_array ($location['id'], $_SESSION['locationFilter'])) ? 'checked' : '';
			echo "<label><input type=checkbox name='location_id[]' class=${level} value='${location['id']}'${checked} onClick=checkAll(this)>${location['name']}";
			echo '</label>';
			if ($location['kidc'])
			{
				echo "<a id='lfa" . $location['id'] . "' onclick=\"expand('${location['id']}')\" href\"#\" > - </a>";
				echo "<div id='lfd" . $location['id'] . "'>";
				$self ($location['kids'], $level + 1);
				echo '</div>';
			}
			echo '</div>';
		}
	}

	addJS(<<<END
function checkAll(bx) {
	for (var tbls=document.getElementsByTagName("table"), i=tbls.length; i--;)
		if (tbls[i].id == "locationFilter") {
			var bxs=tbls[i].getElementsByTagName("input");
			var in_tree = false;
			for (var j=0; j<bxs.length; j++) {
				if(in_tree == false && bxs[j].value == bx.value)
					in_tree = true;
				else if(parseInt(bxs[j].className) <= parseInt(bx.className))
					in_tree = false;
				if (bxs[j].type=="checkbox" && in_tree == true)
					bxs[j].checked = bx.checked;
			}
		}
}

function collapseAll(bx) {
	for (var tbls=document.getElementsByTagName("table"), i=tbls.length; i--;)
		if (tbls[i].id == "locationFilter") {
			var bxs=tbls[i].getElementsByTagName("div");
			//loop through divs to hide unchecked
			for (var j=0; j<bxs.length; j++) {
				var is_checked = -1;
				var in_div=bxs[j].getElementsByTagName("input");
				//loop through input to find if any is checked
				for (var k=0; k<in_div.length; k++) {
					if(in_div[k].type="checkbox") {
						if (in_div[k].checked == true) {
							is_checked = true;
							break;
						}
						else
							is_checked = false;
					}
				}
				// nothing selected and element id is lfd, collapse it
				if (is_checked == false && !bxs[j].id.indexOf("lfd"))
					expand(bxs[j].id.substr(3));
			}
		}
}

function expand(id) {
	var divid = document.getElementById("lfd" + id);
	var iconid = document.getElementById("lfa" + id);
	if (divid.style.display == 'none') {
		divid.style.display = 'block';
		iconid.innerHTML = ' - ';
	} else {
		divid.style.display = 'none';
		iconid.innerHTML = ' + ';
	}
}
END
	,TRUE);
	startPortlet ('Location filter');
	echo <<<END
<table border=0 align=center cellspacing=0 class="tagtree" id="locationFilter">
    <form method=post>
    <input type=hidden name=page value=rackspace>
    <input type=hidden name=tab value=default>
    <input type=hidden name=changeLocationFilter value=true>
END;

	$locationlist = listCells ('location');
	if (count ($locationlist))
	{
		echo "<tr><td class=tagbox style='padding-left: 0px'><label>";
		echo "<input type=checkbox name='location'  onClick=checkAll(this)> Toggle all";
		echo "<img src=?module=chrome&uri=pix/1x1t.gif onLoad=collapseAll(this)>"; // dirty hack to collapse all when page is displayed
		echo "</label></td></tr>\n";
		echo "<tr><td class=tagbox><hr>\n";
		renderLocationCheckbox (treeFromList (addTraceToNodes ($locationlist)));
		echo "<hr></td></tr>\n";
		echo '<tr><td>';
		printImageHREF ('setfilter', 'set filter', TRUE);
		echo "</td></tr>\n";
	}
	else
	{
		echo "<tr><td class='tagbox sparenetwork'>(no locations exist)</td></tr>\n";
		echo "<tr><td>";
		printImageHREF ('setfilter gray');
		echo "</td></tr>\n";
	}

	echo "</form></table>\n";
	finishPortlet ();
}

function rackspaceCmp ($a, $b)
{
	$ret = strnatcasecmp ($a['location_tree'], $b['location_tree']);
	if (!$ret)
		$ret = strnatcasecmp ($a['row_name'], $b['row_name']);
	return $ret;
}

function getRackThumbLink ($rack, $scale = 1, $object_id = NULL)
{
	if (! is_int ($scale) || $scale <= 0)
		throw new InvalidArgException ('scale', $scale, 'must be a natural number');
	$width = getRackImageWidth() * $scale;
	$height = getRackImageHeight ($rack['height']) * $scale;
	$title = "${rack['height']} units";
	$src = '?module=image' .
		($scale == 1 && $object_id === NULL ? '&img=minirack' : "&img=midirack&scale=${scale}") .
		"&rack_id=${rack['id']}" .
		($object_id === NULL ? '' : "&object_id=${object_id}");
	$img = "<img border=0 width=${width} height=${height} title='${title}' src='${src}'>";
	return mkA ($img, 'rack', $rack['id']);
}

function renderRackspace ()
{
	// Handle the location filter
	startSession();
	if (isset ($_REQUEST['changeLocationFilter']))
		unset ($_SESSION['locationFilter']);
	if (isset ($_REQUEST['location_id']))
		$_SESSION['locationFilter'] = $_REQUEST['location_id'];
	session_commit();

	echo "<table class=objview border=0 width='100%'><tr><td class=pcleft>";

	$found_racks = array();
	$cellfilter = getCellFilter();
	if (! ($cellfilter['is_empty'] && !isset ($_SESSION['locationFilter']) && renderEmptyResults ($cellfilter, 'racks', getEntitiesCount ('rack'))))
	{
		$rows = array();
		$rackCount = 0;
		foreach (listCells ('row') as $row_id => $rowInfo)
		{
			$rackList = applyCellFilter ('rack', $cellfilter, $row_id);
			$found_racks = array_merge ($found_racks, $rackList);
			$location_id = $rowInfo['location_id'];
			$locationIdx = 0;
			// contains location names in the form of 'grandparent parent child', used for sorting 
			$locationTree = '';
			// contains location names as well as links
			$hrefLocationTree = '';
			while ($location_id)
			{
				if ($locationIdx == 20)
				{
					showWarning ("Warning: There is likely a circular reference in the location tree.  Investigate location ${location_id}.");
					break;
				}
				$parentLocation = spotEntity ('location', $location_id);
				$locationTree = sprintf ('%s %s', $parentLocation['name'], $locationTree);
				$hrefLocationTree = "&raquo; <a href='" .
					makeHref(array('page'=>'location', 'location_id'=>$parentLocation['id'])) .
					"${cellfilter['urlextra']}'>${parentLocation['name']}</a> " .
					$hrefLocationTree;
				$location_id = $parentLocation['parent_id'];
				$locationIdx++;
			}
			$hrefLocationTree = substr ($hrefLocationTree, 8);
			$rows[] = array (
				'location_id' => $rowInfo['location_id'],
				'location_tree' => $locationTree,
				'href_location_tree' => $hrefLocationTree,
				'row_id' => $row_id,
				'row_name' => $rowInfo['name'],
				'racks' => $rackList
			);
			$rackCount += count($rackList);
		}

		// sort by location, then by row
		usort ($rows, 'rackspaceCmp');

		if (! renderEmptyResults($cellfilter, 'racks', $rackCount))
		{
			// generate thumb gallery
			global $nextorder;
			// Zero value effectively disables the limit.
			$maxPerRow = getConfigVar ('RACKS_PER_ROW');
			$order = 'odd';
			if (! count ($rows))
				echo "<h2>No rows found</h2>\n";
			else
			{
				echo '<table border=0 cellpadding=10 class=cooltable>';
				echo '<tr><th class=tdleft>Location</th><th class=tdleft>Row</th><th class=tdleft>Racks</th></tr>';
				foreach ($rows as $row)
				{
					$rackList = $row['racks'];

					if (
						$location_id != '' and isset ($_SESSION['locationFilter']) and !in_array ($location_id, $_SESSION['locationFilter']) or
						empty ($rackList) and ! $cellfilter['is_empty']
					)
						continue;
					$rackListIdx = 0;
					echo "<tr class=row_${order}><th class=tdleft>${row['href_location_tree']}</th>";
					echo "<th class=tdleft><a href='".makeHref(array('page'=>'row', 'row_id'=>$row['row_id']))."${cellfilter['urlextra']}'>${row['row_name']}</a></th>";
					echo "<th class=tdleft><table border=0 cellspacing=5><tr>";
					if (! count ($rackList))
						echo '<td>(empty row)</td>';
					else
						foreach ($rackList as $rack)
						{
							if ($rackListIdx > 0 and $maxPerRow > 0 and $rackListIdx % $maxPerRow == 0)
							{
								echo '</tr></table></th></tr>';
								echo "<tr class=row_${order}><th class=tdleft></th><th class=tdleft>${row['row_name']} (continued)";
								echo "</th><th class=tdleft><table border=0 cellspacing=5><tr>";
							}
							echo '<td align=center valign=bottom>' . getRackThumbLink ($rack);
							echo '<br>' . mkA (stringForLabel ($rack['name']), 'rack', $rack['id']) . '</td>';
							$rackListIdx++;
						}
					$order = $nextorder[$order];
					echo "</tr></table></th></tr>\n";
				}
				echo "</table>\n";
			}
		}
	}
	echo '</td><td class=pcright width="25%">';
	renderCellFilterPortlet ($cellfilter, 'rack', $found_racks);
	echo "<br>\n";
	renderLocationFilterPortlet ();
	echo "</td></tr></table>\n";
}

function renderLocationRowForEditor ($subtree, $level = 0)
{
	$self = __FUNCTION__;
	foreach ($subtree as $locationinfo)
	{
		echo "<tr><td align=left style='padding-left: " . ($locationinfo['kidc'] ? $level : ($level + 1) * 16) . "px;'>";
		if ($locationinfo['kidc'])
			printImageHREF ('node-expanded-static');
		if ($locationinfo['refcnt'] > 0 || $locationinfo['kidc'] > 0)
			printImageHREF ('nodestroy');
		else
			echo getOpLink (array ('op' => 'deleteLocation', 'location_id' => $locationinfo['id']), '', 'destroy', 'Delete location');
		echo '</td><td class=tdleft>';
		printOpFormIntro ('updateLocation', array ('location_id' => $locationinfo['id']));
		$parent = isset ($locationinfo['parent_id']) ? $locationinfo['parent_id'] : 0;
		echo getSelect
		(
			array ( $parent => $parent ? htmlspecialchars ($locationinfo['parent_name']) : '-- NONE --'),
			array ('name' => 'parent_id', 'id' => 'locationid_' . $locationinfo['id'], 'class' => 'locationlist-popup'),
			$parent,
			FALSE
		);
		echo "</td><td class=tdleft>";
		echo "<input type=text size=48 name=name value='${locationinfo['name']}'>";
		echo '</td><td>' . getImageHREF ('save', 'Save changes', TRUE) . "</form></td></tr>\n";
		if ($locationinfo['kidc'])
			$self ($locationinfo['kids'], $level + 1);
	}
}

function renderLocationSelectTree ($select_name, $selected_id = NULL)
{
	echo "<select name='${select_name}'>";
	echo '<option value=0>-- NONE --</option>';
	$locationlist = listCells ('location');
	foreach (treeFromList (addTraceToNodes ($locationlist)) as $location)
	{
		echo "<option value=${location['id']} style='font-weight: bold' ";
		if ($location['id'] == $selected_id )
		    echo ' selected';
		echo ">${location['name']}</option>";
		printLocationChildrenSelectOptions ($location, $selected_id);
	}
	echo '</select>';
}

function renderRackspaceLocationEditor ()
{
	$js = <<<JSTXT
	function locationeditor_showselectbox(e) {
		$(this).load('index.php', {module: 'ajax', ac: 'get-location-select', locationid: this.id});
		$(this).unbind('mousedown', locationeditor_showselectbox);
	}
	$(document).ready(function () {
		$('select.locationlist-popup').bind('mousedown', locationeditor_showselectbox);
	});
JSTXT;

	addJS($js, TRUE	);
	function printNewItemTR ()
	{
		printOpFormIntro ('addLocation');
		echo '<tr><td>';
		printImageHREF ('create', 'Add new location', TRUE);
		echo '</td><td>';
		renderLocationSelectTree ('parent_id');
		echo '</td><td><input type=text size=48 name=name></td><td>';
		printImageHREF ('create', 'Add new location', TRUE);
		echo "</td></tr></form>\n";
	}

	startPortlet ('Locations');
	echo "<table border=0 cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>&nbsp;</th><th>Parent</th><th>Name</th><th>&nbsp;</th></tr>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();

	$locations = listCells ('location');
	renderLocationRowForEditor (treeFromList (addTraceToNodes ($locations)));

	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo "</table><br>\n";
	finishPortlet();
}

function renderRackspaceRowEditor ()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('addRow');
		echo '<tr><td>';
		printImageHREF ('create', 'Add new row', TRUE);
		echo '</td><td>&nbsp;';
		echo '</td><td>&nbsp;';
		echo '</td><td>';
		renderLocationSelectTree ('location_id');
		echo '</td><td><input type=text name=name></td><td>';
		printImageHREF ('create', 'Add new row', TRUE);
		echo '</td></tr></form>';
	}
	startPortlet ('Rows');
	echo "<table border=0 cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>&nbsp;</th><th># Racks</th><th># Devices</th><th>Location</th><th>Name</th><th>&nbsp;</th><th>Row link</th></tr>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ();
	foreach (listCells ('row') as $row_id => $rowInfo)
	{
		echo '<tr><td>';
		$rc = $rowInfo['rackc'];
		$delete_racks_str = $rc ? " and $rc rack(s)" : '';
		echo getOpLink (array ('op'=>'deleteRow', 'row_id'=>$row_id), '', 'destroy', 'Delete row'.$delete_racks_str, 'need-confirmation');
		printOpFormIntro ('updateRow', array ('row_id' => $row_id));
		echo '</td><td>';
		echo $rc;
		echo '</td><td>';
		echo getRowMountsCount ($row_id);
		echo '</td><td>';
		renderLocationSelectTree ('location_id', $rowInfo['location_id']);
		echo "</td><td><input type=text name=name value='${rowInfo['name']}'></td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</form></td>";
		echo "<td>" . mkCellA ($rowInfo) . "</td>";
		echo "</tr>\n";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ();
	echo "</table><br>\n";
	finishPortlet();
}

function renderRow ($row_id)
{
	$rowInfo = getRowInfo ($row_id);
	$cellfilter = getCellFilter();
	$rackList = applyCellFilter ('rack', $cellfilter, $row_id);

	$summary = array ();
	$summary['Name'] = $rowInfo['name'];
	if ($rowInfo['location_id'])
		$summary['Location'] = mkA ($rowInfo['location'], 'location', $rowInfo['location_id']);
	$summary['Racks'] = $rowInfo['count'];
	$summary['Units'] = $rowInfo['sum'];
	$summary['% used'] = getProgressBar (getRSUforRow ($rackList));
	foreach (getAttrValuesSorted ($row_id) as $record)
		if
		(
			$record['value'] != '' and
			permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $record['id'])))
		)
			$summary['{sticker}' . $record['name']] = formatAttributeValue ($record);

	// Main layout starts.
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";

	// Left portlet with row information.
	echo "<tr><td class=pcleft>";
	renderEntitySummary ($rowInfo, 'Summary', $summary);
	renderCellFilterPortlet ($cellfilter, 'rack', $rackList, array ('row_id' => $row_id));
	renderFilesPortlet ('row',$row_id);
	echo "</td><td class=pcright>";

	global $nextorder;
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
		$class = ($rack['has_problems'] == 'yes') ? 'error' : $order;
		echo "<td align=center valign=bottom class=row_${class}>" .
			getRackThumbLink ($rack, getConfigVar ('ROW_SCALE')) .
			'<br>' . mkA (stringForLabel ($rack['name']), 'rack', $rack['id']) . '</td>';
		$order = $nextorder[$order];
		$rackListIdx++;
	}
	echo "</tr></table>\n";
	finishPortlet();
	echo "</td></tr></table>";
}

function renderEditRowForm ($row_id)
{
	$row = getRowInfo ($row_id);

	startPortlet ('Attributes');
	printOpFormIntro ('updateRow');
	echo '<table border=0 align=center>';
	echo '<tr><td>&nbsp;</td><th class=tdright>Location:</th><td class=tdleft>';
	$locations = array ();
	$locations[0] = '-- NOT SET --';
	foreach (listCells ('location') as $id => $locationInfo)
		$locations[$id] = $locationInfo['name'];
	natcasesort ($locations);
	printSelect ($locations, array ('name' => 'location_id'), $row['location_id']);
	echo "</td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Name (required):</th><td class=tdleft><input type=text name=name value='${row['name']}'></td></tr>\n";

	// optional attributes
	$values = getAttrValuesSorted ($row_id);
	$num_attrs = count ($values);
	echo "<input type=hidden name=num_attrs value=${num_attrs}>\n";
	$i = 0;
	foreach ($values as $record)
	{
		echo "<input type=hidden name=${i}_attr_id value=${record['id']}>";
		echo '<tr><td>';
		if (strlen ($record['value']))
			echo getOpLink (array('op'=>'clearSticker', 'attr_id'=>$record['id']), '', 'clear', 'Clear value', 'need-confirmation');
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
				$chapter = cookOptgroups ($chapter, 1562, $record['key']);
				printNiftySelect ($chapter, array ('name' => "${i}_value"), $record['key']);
				break;
		}
		echo "</td></tr>\n";
		$i++;
	}
	if ($row['count'] == 0)
	{
		echo '<tr><td>&nbsp;</td><th class=tdright>Actions:</th><td class=tdleft>';
		echo getOpLink (array ('op'=>'deleteRow'), '', 'destroy', 'Delete row', 'need-confirmation');
		echo "&nbsp;</td></tr>\n";
	}
	echo "<tr><td class=submit colspan=3>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>\n";
	echo '</form></table><br>';
	finishPortlet();

	startPortlet ('History');
	renderObjectHistory ($row_id);
	finishPortlet();
}

// Used by renderRack()
function printObjectDetailsForRenderRack ($object_id, $hl_obj_id = 0)
{
	$objectData = spotEntity ('object', $object_id);
	if (strlen ($objectData['asset_no']))
		$prefix = "<div title='${objectData['asset_no']}";
	else
		$prefix = "<div title='no asset tag";
	// Don't tell about label, if it matches common name.
	$body = '';
	if ($objectData['name'] != $objectData['label'] and strlen ($objectData['label']))
		$body = ", visible label is \"${objectData['label']}\"";
	// Display list of child objects, if any
	$objectChildren = getChildren ($objectData, 'object');
	$slotRows = $slotCols = $slotInfo = $slotData = $slotTitle = $slotClass = array ();
	if (count($objectChildren) > 0)
	{
		foreach ($objectChildren as $childData)
		{
			$childNames[] = $childData['name'];
			$attrData = getAttrValues ($childData['id']);
			$numRows = $numCols = 1;
			if (isset ($attrData[2])) // HW type
			{
				extractLayout ($attrData[2]);
				if (isset ($attrData[2]['rows']))
				{
					$numRows = $attrData[2]['rows'];
					$numCols = $attrData[2]['cols'];
				}
			}
			if (isset ($attrData['28'])) // slot number
			{
				$slot = $attrData['28']['value'];
				if (preg_match ('/\d+/', $slot, $matches))
					$slot = $matches[0];
				$slotRows[$slot] = $numRows;
				$slotCols[$slot] = $numCols;
				$slotInfo[$slot] = $childData['dname'];
				$slotData[$slot] = $childData['id'];
				if (strlen ($childData['asset_no']))
					$slotTitle[$slot] = "<div title='${childData['asset_no']}";
				else
					$slotTitle[$slot] = "<div title='no asset tag";
				if (strlen ($childData['label']) and $childData['label'] != $childData['dname'])
					$slotTitle[$slot] .= ", visible label is \"${childData['label']}\"";
				$slotTitle[$slot] .= "'>";
				$slotClass[$slot] = 'state_T';
				if ($childData['id'] == $hl_obj_id)
					$slotClass[$slot] .= 'h';
				if ($childData['has_problems'] == 'yes')
					$slotClass[$slot] .= 'w';
			}
		}
		natsort($childNames);
		$suffix = sprintf(", contains %s'>", implode(', ', $childNames));
	}
	else
		$suffix = "'>";
	echo "${prefix}${body}${suffix}" . mkA ($objectData['dname'], 'object', $objectData['id']) . '</div>';
	if (in_array ($objectData['objtype_id'], array (1502,1503))) // server chassis, network chassis
	{
		$objAttr = getAttrValues ($objectData['id']);
		if (isset ($objAttr[2])) // HW type
		{
			extractLayout ($objAttr[2]);
			if (isset ($objAttr[2]['rows']))
			{
				$rows = $objAttr[2]['rows'];
				$cols = $objAttr[2]['cols'];
				$layout = $objAttr[2]['layout'];
				echo "<table width='100%' border='1'>";
				for ($r = 0; $r < $rows; $r++)
				{
					echo '<tr>';
					for ($c = 0; $c < $cols; $c++)
					{
						$s = ($r * $cols) + $c + 1;
						if (isset ($slotData[$s]))
						{
							if ($slotData[$s] >= 0)
							{
								for ($lr = 0; $lr < $slotRows[$s]; $lr++)
									for ($lc = 0; $lc < $slotCols[$s]; $lc++)
									{
										$skip = ($lr * $cols) + $lc;
										if ($skip > 0)
											$slotData[$s + $skip] = -1;
									}
								echo '<td';
								if ($slotRows[$s] > 1)
									echo " rowspan=$slotRows[$s]";
								if ($slotCols[$s] > 1)
									echo " colspan=$slotCols[$s]";
								echo " class='${slotClass[$s]}'>${slotTitle[$s]}";
								if ($layout == 'V')
								{
									$tmp = substr ($slotInfo[$s], 0, 1);
									foreach (str_split (substr ($slotInfo[$s], 1)) as $letter)
										$tmp .= '<br>' . $letter;
									$slotInfo[$s] = $tmp;
								}
								echo mkA ($slotInfo[$s], 'object', $slotData[$s]);
								echo '</div></td>';
							}
						}
						else
							echo "<td class='state_F'><div title=\"Free slot\">&nbsp;</div></td>";
					}
					echo '</tr>';
				}
				echo '</table>';
			}
		}
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
	$neighbors = getRackNeighbors ($rackData['row_id'], $rack_id);
	$prev_id = $neighbors['prev'];
	$next_id = $neighbors['next'];
	echo "<center><table border=0><tr valign=middle>";
	echo '<td><h2>' . mkA ($rackData['row_name'], 'row', $rackData['row_id']) . ' :</h2></td>';
	if ($prev_id != NULL)
		echo '<td>' . mkA (getImageHREF ('prev', 'previous rack'), 'rack', $prev_id) . '</td>';
	echo '<td><h2>' . mkA ($rackData['name'], 'rack', $rackData['id']) . '</h2></td>';
	if ($next_id != NULL)
		echo '<td>' . mkA (getImageHREF ('next', 'next rack'), 'rack', $next_id) . '</td>';
	echo "</h2></td></tr></table>\n";
	echo "<table class=rack border=0 cellspacing=0 cellpadding=1>\n";
	echo "<tr><th width='10%'>&nbsp;</th><th width='20%'>Front</th>";
	echo "<th width='50%'>Interior</th><th width='20%'>Back</th></tr>\n";
	for ($i = $rackData['height']; $i > 0; $i--)
	{
		echo "<tr><th>" . inverseRackUnit ($i, $rackData) . "</th>";
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			if (isset ($rackData[$i][$locidx]['skipped']))
				continue;
			$state = $rackData[$i][$locidx]['state'];
			echo "<td class='atom state_${state}";
			if (isset ($rackData[$i][$locidx]['hl']))
				echo $rackData[$i][$locidx]['hl'];
			echo "'";
			if (isset ($rackData[$i][$locidx]['colspan']))
				echo ' colspan=' . $rackData[$i][$locidx]['colspan'];
			if (isset ($rackData[$i][$locidx]['rowspan']))
				echo ' rowspan=' . $rackData[$i][$locidx]['rowspan'];
			echo ">";
			switch ($state)
			{
				case 'T':
					printObjectDetailsForRenderRack ($rackData[$i][$locidx]['object_id'], $hl_obj_id);
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
	echo "</table>\n";
	// Get a list of all of objects Zero-U mounted to this rack
	$zeroUObjects = getChildren ($rackData, 'object');
	uasort ($zeroUObjects, 'compare_name');
	if (count ($zeroUObjects) > 0)
	{
		echo "<br><table width='75%' class=rack border=0 cellspacing=0 cellpadding=1>\n";
		echo "<tr><th>Zero-U:</th></tr>\n";
		foreach ($zeroUObjects as $zeroUObject)
		{
			$state = ($zeroUObject['id'] == $hl_obj_id) ? 'Th' : 'T';
			if ($zeroUObject['has_problems'] == 'yes')
				$state .= 'w';
			echo "<tr><td class='atom state_${state}'>";
			printObjectDetailsForRenderRack($zeroUObject['id']);
			echo "</td></tr>\n";
		}
		echo "</table>\n";
	}
	echo "</center>\n";
}

function renderRackSortForm ($row_id)
{
	includeJQueryUI (false);
	$js = <<<JSTXT
	$(document).ready(
		function () {
			$("#sortRacks").sortable({
				update : function () {
					serial = $('#sortRacks').sortable('serialize');
					$.ajax({
						url: 'index.php?module=ajax&ac=upd-rack-sort-order&row_id=${row_id}',
						type: 'post',
						data: serial,
					});
				}
			});
		}
	);
JSTXT;
	addJS($js, true);

	startPortlet ('Racks');
	echo "<table border=0 cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>Drag to change order</th></tr>\n";
	echo "<tr><td><ul class='uflist' id='sortRacks'>\n";
	foreach (getRacks($row_id) as $rack_id => $rackInfo)
		echo "<li id=racks_${rack_id}>${rackInfo['name']}</li>\n";
	echo "</ul></td></tr></table>\n";
	finishPortlet();
}

function renderNewRackForm()
{
	$default_height = getConfigVar ('DEFAULT_RACK_HEIGHT');
	if ($default_height == 0)
		$default_height = '';
	startPortlet ('Add one');
	printOpFormIntro ('addRack', array ('got_data' => 'TRUE'));
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Name (required):</th><td class=tdleft><input type=text name=name></td>";
	echo "<tr><th class=tdright>Height in units (required):</th><td class=tdleft><input type=text name=height1 value='${default_height}'></td></tr>\n";
	echo "<tr><th class=tdright>Asset tag:</th><td class=tdleft><input type=text name=asset_no></td></tr>\n";
	echo "<tr><th class=tdright>Tags:</td><td class=tdleft>";
	printTagsPicker ();
	echo "</td></tr>\n";
	echo "<tr><td class=submit colspan=2>";
	printImageHREF ('CREATE', 'Add', TRUE);
	echo "</td></tr></table></form>";
	finishPortlet();

	startPortlet ('Add many');
	printOpFormIntro ('addRack', array ('got_mdata' => 'TRUE'));
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Height in units (*):</th><td class=tdleft><input type=text name=height2 value='${default_height}'></td>";
	echo "<tr><th class=tdright>Assign tags:</td><td class=tdleft>";
	printTagsPicker ();
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>Rack names (required):</th><td class=tdleft><textarea name=names cols=40 rows=25></textarea></td></tr>\n";
	echo "<tr><td class=submit colspan=2>";
	printImageHREF ('CREATE', 'Add', TRUE);
	echo '</form></table>';
	finishPortlet();
}

function renderEditObjectForm()
{
	global $pageno;
	$object_id = getBypassValue();
	$object = spotEntity ('object', $object_id);
	startPortlet ();
	printOpFormIntro ('update');

	// static attributes
	echo '<table border=0 cellspacing=0 cellpadding=3 align=center>';
	echo "<tr><td>&nbsp;</td><th colspan=2><h2>Attributes</h2></th></tr>";
	echo '<tr><td>&nbsp;</td><th class=tdright>Type:</th><td class=tdleft>';
	printSelect (getObjectTypeChangeOptions ($object['id']), array ('name' => 'object_type_id'), $object['objtype_id']);
	echo '</td></tr>';
	// baseline info
	echo "<tr><td>&nbsp;</td><th class=tdright>Common name:</th><td class=tdleft><input type=text name=object_name value='${object['name']}'></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Visible label:</th><td class=tdleft><input type=text name=object_label value='${object['label']}'></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Asset tag:</th><td class=tdleft><input type=text name=object_asset_no value='${object['asset_no']}'></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Tags:</th><td class=tdleft>";
	printTagsPicker ();
	echo "</td></tr>\n";
	// parent selection
	if (objectTypeMayHaveParent ($object['objtype_id']))
	{
		$parents = getParents ($object, 'object');
		// lookup the human-readable object type, sort by it
		foreach ($parents as $parent_id => $parent)
			$parents[$parent_id]['object_type'] = decodeObjectType ($parent['objtype_id']);
		$grouped_parents = groupBy ($parents, 'object_type');
		ksort ($grouped_parents);
		foreach ($grouped_parents as $parents_group)
		{
			uasort ($parents_group, 'compare_name');
			$label = $parents_group[key ($parents_group)]['object_type'] . (count($parents_group) > 1 ? ' containers:' : ' container:');
			foreach ($parents_group as $link_id => $parent_cell)
			{
				echo "<tr><td>&nbsp;</td>";
				echo "<th class=tdright>${label}</th><td class=tdleft>";
				echo mkCellA ($parent_cell);
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo getOpLink (array('op'=>'unlinkObjects', 'link_id'=>$link_id), '', 'cut', 'Unlink container');
				echo "</td></tr>\n";
				$label = '&nbsp;';
			}
		}
		echo "<tr><td>&nbsp;</td>";
		echo "<th class=tdright>Select container:</th><td class=tdleft>";
		echo getPopupLink ('objlist', array(), 'findlink', 'attach', 'Select a container');
		echo "</td></tr>\n";
	}
	// optional attributes
	$i = 0;
	$values = getAttrValuesSorted ($object_id);
	if (count($values) > 0)
	{
		foreach ($values as $record)
		{
			if (! permitted (NULL, NULL, NULL, array (
				array ('tag' => '$attr_' . $record['id']),
				array ('tag' => '$any_op'),
			)))
				continue;
			echo "<input type=hidden name=${i}_attr_id value=${record['id']}>";
			echo '<tr><td>';
			if (strlen ($record['value']))
				echo getOpLink (array('op'=>'clearSticker', 'attr_id'=>$record['id']), '', 'clear', 'Clear value', 'need-confirmation');
			else
				echo '&nbsp;';
			echo '</td>';
			echo "<th class=sticker>${record['name']}";
			if ($record['type'] == 'date')
				echo ' (' . datetimeFormatHint (getConfigVar ('DATETIME_FORMAT')) . ')';
			echo ':</th><td class=tdleft>';
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
				case 'date':
					$date_value = $record['value'] ? datetimestrFromTimestamp ($record['value']) : '';
					echo "<input type=text name=${i}_value value='${date_value}'>";
					break;
			}
			echo "</td></tr>\n";
			$i++;
		}
	}
	echo '<input type=hidden name=num_attrs value=' . $i . ">\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Has problems:</th><td class=tdleft><input type=checkbox name=object_has_problems";
	if ($object['has_problems'] == 'yes')
		echo ' checked';
	echo "></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Actions:</th><td class=tdleft>";
	echo getOpLink (array ('op'=>'deleteObject', 'page'=>'depot', 'tab'=>'addmore', 'object_id'=>$object_id), '' ,'destroy', 'Delete object', 'need-confirmation');
	echo "&nbsp;";
	echo getOpLink (array ('op'=>'resetObject'), '' ,'clear', 'Reset (cleanup) object', 'need-confirmation');
	echo "</td></tr>\n";
	echo "<tr><td colspan=3><b>Comment:</b><br><textarea name=object_comment rows=10 cols=80>${object['comment']}</textarea></td></tr>";

	echo "<tr><th class=submit colspan=3>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</form></th></tr></table>\n";
	finishPortlet();

	echo '<table border=0 width=100%><tr><td>';
	startPortlet ('history');
	renderObjectHistory ($object_id);
	finishPortlet();
	echo '</td></tr></table>';
}

function renderEditRackForm ($rack_id)
{
	global $pageno;
	$rack = spotEntity ('rack', $rack_id);
	amplifyCell ($rack);

	startPortlet ('Attributes');
	printOpFormIntro ('updateRack');
	echo '<table border=0 align=center>';
	echo "<tr><td>&nbsp;</td><th class=tdright>Rack row:</th><td class=tdleft>";
	foreach (listCells ('row') as $row_id => $rowInfo)
	{
		$trail = getLocationTrail ($rowInfo['location_id'], FALSE);
		$rows[$row_id] = empty ($trail) ? $rowInfo['name'] : $rowInfo['name'] . ' [' . $trail . ']';
	}
	natcasesort ($rows);
	printSelect ($rows, array ('name' => 'row_id'), $rack['row_id']);
	echo "</td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Name (required):</th><td class=tdleft><input type=text name=name value='${rack['name']}'></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Height (required):</th><td class=tdleft><input type=text name=height value='${rack['height']}'></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Asset tag:</th><td class=tdleft><input type=text name=asset_no value='${rack['asset_no']}'></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Tags:</th><td class=tdleft>";
	printTagsPicker ();
	echo "</td></tr>\n";
	// optional attributes
	$values = getAttrValuesSorted ($rack_id);
	$num_attrs = count($values);
	$num_attrs = $num_attrs-2; // subtract for the 'height' and 'sort_order' attributes
	echo "<input type=hidden name=num_attrs value=${num_attrs}>\n";
	$i = 0;
	foreach ($values as $record)
	{
		// Skip the 'height' attribute as it's already displayed as a required field
		// Also skip the 'sort_order' attribute
		if ($record['id'] == 27 or $record['id'] == 29)
			continue;
		echo "<input type=hidden name=${i}_attr_id value=${record['id']}>";
		echo '<tr><td>';
		if (strlen ($record['value']))
			echo getOpLink (array('op'=>'clearSticker', 'attr_id'=>$record['id']), '', 'clear', 'Clear value', 'need-confirmation');
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
				$chapter = cookOptgroups ($chapter, 1560, $record['key']);
				printNiftySelect ($chapter, array ('name' => "${i}_value"), $record['key']);
				break;
		}
		echo "</td></tr>\n";
		$i++;
	}
	echo "<tr><td>&nbsp;</td><th class=tdright>Has problems:</th><td class=tdleft><input type=checkbox name=has_problems";
	if ($rack['has_problems'] == 'yes')
		echo ' checked';
	echo "></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Actions:</th><td class=tdleft>";
	if ($rack['isDeletable'])
	{
		echo getOpLink (array ('op'=>'deleteRack'), '', 'destroy', 'Delete rack', 'need-confirmation');
		echo "&nbsp;";
	}
	else
	{
		echo getOpLink (array ('op'=>'cleanRack'), '' ,'clear', 'Reset (cleanup) rack mounts', 'need-confirmation');
		echo "&nbsp;";
	}
	echo "</td></tr>\n";
	echo "<tr><td colspan=3><b>Comment:</b><br><textarea name=comment rows=10 cols=80>${rack['comment']}</textarea></td></tr>";
	echo "<tr><td class=submit colspan=3>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>\n";
	echo '</form></table><br>';
	finishPortlet();

	startPortlet ('History');
	renderObjectHistory ($rack_id);
	finishPortlet();
}

// populates the $summary array with the sum of power attributes of the objects mounted into the rack
function populateRackPower ($rackData, &$summary)
{
	$power_attrs = array(
		7, // 'float','max. current, Ampers'
		13, // 'float','max power, Watts'
	);
	$sum = array();
	if (! isset ($rackData['mountedObjects']))
		amplifyCell ($rackData);
	foreach ($rackData['mountedObjects'] as $object_id)
	{
		$attrs = getAttrValues ($object_id);
		foreach ($power_attrs as $attr_id)
			if (isset ($attrs[$attr_id]) && $attrs[$attr_id]['type'] == 'float')
			{
				if (! isset ($sum[$attr_id]))
				{
					$sum[$attr_id]['sum'] = 0.0;
					$sum[$attr_id]['name'] = $attrs[$attr_id]['name'];
				}
				$sum[$attr_id]['sum'] += $attrs[$attr_id]['value'];
			}
	}
	foreach ($sum as $attr)
		if ($attr['sum'] > 0.0)
			$summary[$attr['name']] = $attr['sum'];
}

// used by renderGridForm() and renderRackPage()
function renderRackInfoPortlet ($rackData)
{
	$summary = array();
	$summary['Rack row'] = mkA ($rackData['row_name'], 'row', $rackData['row_id']);
	$summary['Name'] = $rackData['name'];
	$summary['Height'] = $rackData['height'];
	if (strlen ($rackData['asset_no']))
		$summary['Asset tag'] = $rackData['asset_no'];
	if ($rackData['has_problems'] == 'yes')
		$summary[] = array ('<tr><td colspan=2 class=msg_error>Has problems</td></tr>');
	populateRackPower ($rackData, $summary);
	// Display populated attributes, but skip 'height' since it's already displayed above
	// and skip 'sort_order' because it's modified using AJAX
	foreach (getAttrValuesSorted ($rackData['id']) as $record)
		if ($record['id'] != 27 && $record['id'] != 29 && strlen ($record['value']))
			$summary['{sticker}' . $record['name']] = formatAttributeValue ($record);
	$summary['% used'] = getProgressBar (getRSUforRack ($rackData));
	$summary['Objects'] = count ($rackData['mountedObjects']);
	$summary['tags'] = '';
	renderEntitySummary ($rackData, 'summary', $summary);
	if ($rackData['comment'] != '')
	{
		startPortlet ('Comment');
		echo '<div class=commentblock>' . string_insert_hrefs ($rackData['comment']) . '</div>';
		finishPortlet ();
	}
}

// This is a universal editor of rack design/waste.
// FIXME: switch to using printOpFormIntro()
function renderGridForm ($rack_id, $filter, $header, $submit, $state1, $state2)
{
	$rackData = spotEntity ('rack', $rack_id);
	amplifyCell ($rackData);
	$filter ($rackData);

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
	$is_ro = !rackModificationPermitted ($rackData, 'updateRack');
	startPortlet ($header);
	addJS ('js/racktables.js');
	echo "<center>\n";
	$read_only_text = $is_ro ? '(read-only)' : '&nbsp;';
	echo "<p style='color: red; margin-top:0px'>${read_only_text}</p>\n";
	echo "<table class=rack border=0 cellspacing=0 cellpadding=1>\n";
	echo "<tr><th width='10%'>&nbsp;</th>";
	echo "<th width='20%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '0', ${rackData['height']})\">Front</a></th>";
	echo "<th width='50%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '1', ${rackData['height']})\">Interior</a></th>";
	echo "<th width='20%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '2', ${rackData['height']})\">Back</a></th></tr>\n";
	printOpFormIntro ('updateRack');
	markupAtomGrid ($rackData, $state2);
	renderAtomGrid ($rackData, $is_ro);
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

function renderObjectPortRow ($port, $is_highlighted)
{
	// highlight port name with yellow if it's name is not canonical
	$canon_pn = shortenPortName ($port['name'], $port['object_id']);
	$name_class = $canon_pn == $port['name'] ? '' : 'trwarning';

	echo '<tr';
	if ($is_highlighted)
		echo ' class=highlight';
	$a_class = isEthernetPort ($port) ? 'port-menu' : '';
	echo "><td class='tdleft $name_class' NOWRAP><a name='port-${port['id']}' class='interactive-portname nolink $a_class'>${port['name']}</a></td>";
	echo "<td class=tdleft>${port['label']}</td>";
	echo "<td class=tdleft>" . formatPortIIFOIF ($port) . "</td><td class=tdleft><tt>${port['l2address']}</tt></td>";
	if ($port['remote_object_id'])
	{
		echo "<td class=tdleft>" .
			formatPortLink ($port['remote_object_id'], $port['remote_object_name'], $port['remote_id'], NULL) .
			"</td>";
		echo "<td class=tdleft>" . formatLoggedSpan ($port['last_log'], $port['remote_name'], 'underline') . "</td>";
		$editable = permitted ('object', 'ports', 'editPort')
			? 'editable'
			: '';
		echo "<td class=tdleft><span class='rsvtext $editable id-${port['id']} op-upd-reservation-cable'>${port['cableid']}</span></td>";
	}
	else
		echo implode ('', formatPortReservation ($port)) . '<td></td>';
	echo "</tr>";
}

function renderObject ($object_id)
{
	global $nextorder, $virtual_obj_types;
	$info = spotEntity ('object', $object_id);
	amplifyCell ($info);
	// Main layout starts.
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${info['dname']}</h1></td></tr>\n";
	// left column with uknown number of portlets
	echo "<tr><td class=pcleft>";

	// display summary portlet
	$summary  = array();
	if (strlen ($info['name']))
		$summary['Common name'] = $info['name'];
	elseif (considerConfiguredConstraint ($info, 'NAMEWARN_LISTSRC'))
		$summary[] = array ('<tr><td colspan=2 class=msg_error>Common name is missing.</td></tr>');
	$summary['Object type'] = '<a href="' . makeHref (array (
		'page' => 'depot',
		'tab' => 'default',
		'cfe' => '{$typeid_' . $info['objtype_id'] . '}'
	)) . '">' .  decodeObjectType ($info['objtype_id']) . '</a>';
	if (strlen ($info['label']))
		$summary['Visible label'] = $info['label'];
	if (strlen ($info['asset_no']))
		$summary['Asset tag'] = $info['asset_no'];
	elseif (considerConfiguredConstraint ($info, 'ASSETWARN_LISTSRC'))
		$summary[] = array ('<tr><td colspan=2 class=msg_error>Asset tag is missing.</td></tr>');
	$parents = getParents ($info, 'object');
	// lookup the human-readable object type, sort by it
	foreach ($parents as $parent_id => $parent)
		$parents[$parent_id]['object_type'] = decodeObjectType ($parent['objtype_id']);
	$grouped_parents = groupBy ($parents, 'object_type');
	ksort ($grouped_parents);
	foreach ($grouped_parents as $parents_group)
	{
		uasort ($parents_group, 'compare_name');
		$label = $parents_group[key ($parents_group)]['object_type'] . (count($parents_group) > 1 ? ' containers' : ' container');
		$fmt_parents = array();
		foreach ($parents_group as $parent)
			$fmt_parents[] = mkCellA ($parent);
		$summary[$label] = implode ('<br>', $fmt_parents);
	}
	$children = getChildren ($info, 'object');
	foreach (groupBy ($children, 'objtype_id') as $objtype_id => $children_group)
	{
		uasort ($children_group, 'compare_name');
		$fmt_children = array();
		foreach ($children_group as $child)
			$fmt_children[] = mkCellA ($child);
		$summary["Contains " . strtolower(decodeObjectType ($objtype_id))] = implode ('<br>', $fmt_children);
	}
	if ($info['has_problems'] == 'yes')
		$summary[] = array ('<tr><td colspan=2 class=msg_error>Has problems</td></tr>');
	foreach (getAttrValuesSorted ($object_id) as $record)
		if
		(
			strlen ($record['value']) and
			permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $record['id'])))
		)
			$summary['{sticker}' . $record['name']] = formatAttributeValue ($record);
	$summary[] = array (getOutputOf ('printTagTRs',
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
	));
	renderEntitySummary ($info, 'summary', $summary);

	if (strlen ($info['comment']))
	{
		startPortlet ('Comment');
		echo '<div class=commentblock>' . string_insert_hrefs ($info['comment']) . '</div>';
		finishPortlet ();
	}

	$logrecords = getLogRecordsForObject ($_REQUEST['object_id']);
	if (count ($logrecords))
	{
		startPortlet ('log records');
		echo "<table cellspacing=0 cellpadding=5 align=center class=widetable width='100%'>";
		$order = 'odd';
		foreach ($logrecords as $row)
		{
			echo "<tr class=row_${order} valign=top>";
			echo '<td class=tdleft>' . $row['date'] . '<br>' . $row['user'] . '</td>';
			echo '<td class="logentry">' . string_insert_hrefs (htmlspecialchars ($row['content'], ENT_NOQUOTES)) . '</td>';
			echo '</tr>';
			$order = $nextorder[$order];
		}
		echo '</table>';
		finishPortlet();
	}

	switchportInfoJS ($object_id); // load JS code to make portnames interactive
	renderFilesPortlet ('object', $object_id);

	if (count ($info['ports']))
	{
		startPortlet ('ports and links');
		$hl_port_id = 0;
		if (isset ($_REQUEST['hl_port_id']))
		{
			assertUIntArg ('hl_port_id');
			$hl_port_id = $_REQUEST['hl_port_id'];
			addAutoScrollScript ("port-$hl_port_id");
		}
		echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>";
		echo '<tr><th class=tdleft>Local name</th><th class=tdleft>Visible label</th>';
		echo '<th class=tdleft>Interface</th><th class=tdleft>L2 address</th>';
		echo '<th class=tdcenter colspan=2>Remote object and port</th>';
		echo '<th class=tdleft>Cable ID</th></tr>';
		foreach ($info['ports'] as $port)
			callHook ('renderObjectPortRow', $port, ($hl_port_id == $port['id']));
		if (permitted (NULL, 'ports', 'set_reserve_comment'))
			addJS ('js/inplace-edit.js');
		echo "</table><br>";
		finishPortlet();
	}

	if (count ($info['ipv4']) + count ($info['ipv6']))
	{
		startPortlet ('IP addresses');
		echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
		if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
			echo "<tr class=tdleft><th>OS interface</th><th>IP address</th><th>network</th><th>routed by</th><th>peers</th></tr>\n";
		else
			echo "<tr class=tdleft><th>OS interface</th><th>IP address</th><th>peers</th></tr>\n";

		// group IP allocations by interface name instead of address family
		$allocs_by_iface = array();
		foreach (array ('ipv4', 'ipv6') as $ip_v)
			foreach ($info[$ip_v] as $ip_bin => $alloc)
				$allocs_by_iface[$alloc['osif']][$ip_bin] = $alloc;

		// sort allocs array by portnames
		foreach (sortPortList ($allocs_by_iface) as $iface_name => $alloclist)
		{
			$is_first_row = TRUE;
			foreach ($alloclist as $alloc)
			{
				$rendered_alloc = callHook ('getRenderedAlloc', $object_id, $alloc);
				echo "<tr class='${rendered_alloc['tr_class']}' valign=top>";

				// display iface name, same values are grouped into single cell
				if ($is_first_row)
				{
					$rowspan = count ($alloclist) > 1 ? 'rowspan="' . count ($alloclist) . '"' : '';
					echo "<td class=tdleft $rowspan>" . $iface_name . $rendered_alloc['td_name_suffix'] . "</td>";
					$is_first_row = FALSE;
				}
				echo $rendered_alloc['td_ip'];
				if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
				{
					echo $rendered_alloc['td_network'];
					echo $rendered_alloc['td_routed_by'];
				}
				echo $rendered_alloc['td_peers'];

				echo "</tr>\n";
			}
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
				$localip_bin = ip_parse ($pf['localip']);
				if (array_key_exists ($localip_bin, $info['ipv4']))
				{
					$class = $info['ipv4'][$localip_bin]['addrinfo']['class'];
					$osif = $info['ipv4'][$localip_bin]['osif'] . ': ';
				}
				echo "<tr class='$class'>";
				echo "<td>${pf['proto']}</td><td class=tdleft>${osif}" . getRenderedIPPortPair ($pf['localip'], $pf['localport']) . "</td>";
				echo "<td class=tdleft>" . getRenderedIPPortPair ($pf['remoteip'], $pf['remoteport']) . "</td>";
				$address = getIPAddress (ip4_parse ($pf['remoteip']));
				echo "<td class='description'>";
				if (count ($address['allocs']))
					foreach($address['allocs'] as $bond)
						echo mkA ("${bond['object_name']}(${bond['name']})", 'object', $bond['object_id']) . ' ';
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
				echo "<td>${pf['proto']}/" . getRenderedIPPortPair ($pf['localip'], $pf['localport']) . "</td>";
				echo '<td class="description">' . mkA ($pf['object_name'], 'object', $pf['object_id']);
				echo "</td><td>" . getRenderedIPPortPair ($pf['remoteip'], $pf['remoteport']) . "</td>";
				echo "<td class='description'>${pf['description']}</td></tr>";
			}
			echo "</table><br><br>";
		}
		finishPortlet();
	}

	renderSLBTriplets2 ($info);
	renderSLBTriplets ($info);
	echo "</td>\n";

	// After left column we have (surprise!) right column with rackspace portlet only.
	echo "<td class=pcright>";
	if (!in_array($info['objtype_id'], $virtual_obj_types))
	{
		// rackspace portlet
		startPortlet ('rackspace allocation');
		foreach (getResidentRacksData ($object_id, FALSE) as $rack_id)
			renderRack ($rack_id, $object_id);
		echo '<br>';
		finishPortlet();
	}
	echo "</td></tr>";
	echo "</table>\n";
}

function renderRackMultiSelect ($sname, $racks, $selected)
{
	// Transform the given flat list into a list of groups, each representing a rack row.
	$rdata = array();
	foreach ($racks as $rack)
	{
		$trail = getLocationTrail ($rack['location_id'], FALSE);
		if(!empty ($trail))
			$row_name = $trail . ' : ' . $rack['row_name'];
		else
			$row_name = $rack['row_name'];
		$rdata[$row_name][$rack['id']] = $rack['name'];
	}
	echo "<select name=${sname} multiple size=" . getConfigVar ('MAXSELSIZE') . " onchange='getElementsByName(\"updateObjectAllocation\")[0].submit()'>\n";
	$row_names = array_keys ($rdata);
	natsort ($row_names);
	foreach ($row_names as $optgroup)
	{
		echo "<optgroup label='${optgroup}'>";
		foreach ($rdata[$optgroup] as $rack_id => $rack_name)
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
		echo "</td><td class='tdleft'><input type=text size=16 name=port_name></td>\n";
		echo "<td><input type=text name=port_label></td><td>";
		printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type_id'), $prefs['selected']);
		echo "<td><input type=text name=port_l2address size=18 maxlength=24></td>\n";
		echo "<td colspan=4>&nbsp;</td><td>";
		printImageHREF ('add', 'add a port', TRUE);
		echo "</td></tr></form>";
	}
	if (getConfigVar('ENABLE_MULTIPORT_FORM') == 'yes' || getConfigVar('ENABLE_BULKPORT_FORM') == 'yes' )
		startPortlet ('Ports and interfaces');
	else
		echo '<br>';
	$object = spotEntity ('object', $object_id);
	amplifyCell ($object);
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes' && getConfigVar('ENABLE_BULKPORT_FORM') == 'yes'){
		echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
		echo "<tr><th>&nbsp;</th><th class=tdleft>Local name</th><th class=tdleft>Visible label</th><th class=tdleft>Interface</th><th class=tdleft>Start Number</th>";
		echo "<th class=tdleft>Count</th><th>&nbsp;</th></tr>\n";
		printOpFormIntro ('addBulkPorts');
		echo "<tr><td>";
		printImageHREF ('add', 'add ports', TRUE);
		echo "</td><td><input type=text size=8 name=port_name></td>\n";
		echo "<td><input type=text name=port_label></td><td>";
		printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type_id'), $prefs['selected']);
		echo "<td><input type=text name=port_numbering_start size=3 maxlength=3></td>\n";
		echo "<td><input type=text name=port_numbering_count size=3 maxlength=3></td>\n";
		echo "<td>&nbsp;</td><td>";
		printImageHREF ('add', 'add ports', TRUE);
		echo "</td></tr></form>";
		echo "</table><br>\n";
	}

	echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	echo "<tr><th>&nbsp;</th><th class=tdleft>Local name</th><th class=tdleft>Visible label</th><th class=tdleft>Interface</th><th class=tdleft>L2 address</th>";
	echo "<th class=tdcenter colspan=2>Remote object and port</th><th>Cable ID</th><th class=tdcenter>(Un)link or (un)reserve</th><th>&nbsp;</th></tr>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($prefs);

	// clear ports link
	echo getOpLink (array ('op'=>'deleteAll'), 'Clear port list', 'clear', '', 'need-confirmation');

	// rename ports link
	$n_ports_to_rename = 0;
	foreach ($object['ports'] as $port)
		if ($port['name'] != shortenPortName ($port['name'], $object['id']))
			$n_ports_to_rename++;
	if ($n_ports_to_rename)
		echo '<p>' . getOpLink (array ('op'=>'renameAll'), "Auto-rename $n_ports_to_rename ports", 'recalc', 'Use RackTables naming convention for this device type') . '</p>';

	if (isset ($_REQUEST['hl_port_id']))
	{
		assertUIntArg ('hl_port_id');
		$hl_port_id = intval ($_REQUEST['hl_port_id']);
		addAutoScrollScript ("port-$hl_port_id");
	}
	switchportInfoJS ($object_id); // load JS code to make portnames interactive
	foreach ($object['ports'] as $port)
	{
		// highlight port name with yellow if it's name is not canonical
		$canon_pn = shortenPortName ($port['name'], $port['object_id']);
		$name_class = $canon_pn == $port['name'] ? '' : 'trwarning';

		$tr_class = isset ($hl_port_id) && $hl_port_id == $port['id'] ? 'class="highlight"' : '';
		printOpFormIntro ('editPort', array ('port_id' => $port['id']));
		echo "<tr $tr_class><td><a name='port-${port['id']}' href='".makeHrefProcess(array('op'=>'delPort', 'port_id'=>$port['id']))."'>";
		printImageHREF ('delete', 'Unlink and Delete this port');
		echo "</a></td>\n";
		$a_class = isEthernetPort ($port) ? 'port-menu' : '';
		echo "<td class='tdleft $name_class' NOWRAP><input type=text name=name class='interactive-portname $a_class' value='${port['name']}' size=16></td>";
		echo "<td><input type=text name=label value='${port['label']}'></td>";
		echo '<td>';
		if ($port['iif_id'] != 1)
			echo '<label>' . $port['iif_name'] . ' ';
		$port_type_opts = array();
		if (! $port['linked'])
			$port_type_opts = getUnlinkedPortTypeOptions ($port['iif_id']);
		else
			foreach (getExistingPortTypeOptions ($port) as $oif_id => $opt_str)
				$port_type_opts[$port['iif_name']][$port['iif_id'] . '-' . $oif_id] = $opt_str;
		printNiftySelect ($port_type_opts, array ('name' => 'port_type_id'), $port['iif_id'] . '-' . $port['oif_id']);
		if ($port['iif_id'] != 1)
			echo '</label>';
		echo '</td>';

		// 18 is enough to fit 6-byte MAC address in its longest form,
		// while 24 should be Ok for WWN
		echo "<td><input type=text name=l2address value='${port['l2address']}' size=18 maxlength=24></td>\n";
		if ($port['remote_object_id'])
		{
			echo "<td>" .
				formatLoggedSpan ($port['last_log'], formatPortLink ($port['remote_object_id'], $port['remote_object_name'], $port['remote_id'], NULL)) .
				"</td>";
			echo "<td> " . formatLoggedSpan ($port['last_log'], $port['remote_name'], 'underline') .
				"<input type=hidden name=reservation_comment value=''></td>";
			echo "<td><input type=text name=cable value='${port['cableid']}'></td>";
			echo "<td class=tdcenter>";
			echo getOpLink (array('op'=>'unlinkPort', 'port_id'=>$port['id'], ), '', 'cut', 'Unlink this port');
			echo "</td>";
		}
		elseif (strlen ($port['reservation_comment']))
		{
			echo "<td>" . formatLoggedSpan ($port['last_log'], 'Reserved:', 'strong underline') . "</td>";
			echo "<td><input type=text name=reservation_comment value='${port['reservation_comment']}'></td>";
			echo "<td></td>";
			echo "<td class=tdcenter>";
			echo getOpLink (array('op'=>'useup', 'port_id'=>$port['id']), '', 'clear', 'Use up this port');
			echo "</td>";
		}
		else
		{
			$in_rack = getConfigVar ('NEAREST_RACKS_CHECKBOX');
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class=tdcenter>";
			echo getPopupLink ('portlist', array ('port' => $port['id'], 'in_rack' => ($in_rack == "yes" ? "on" : "")), 'findlink', 'plug', '', 'Link this port');
			echo " <input type=text name=reservation_comment></td>\n";
		}
		echo "<td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</td></form></tr>\n";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ($prefs);
	echo "</table><br>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes' && getConfigVar('ENABLE_BULKPORT_FORM') == 'yes'){
		echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
		echo "<tr><th>&nbsp;</th><th class=tdleft>Local name</th><th class=tdleft>Visible label</th><th class=tdleft>Interface</th><th class=tdleft>Start Number</th>";
		echo "<th class=tdleft>Count</th><th>&nbsp;</th></tr>\n";
		printOpFormIntro ('addBulkPorts');
		echo "<tr><td>";
		printImageHREF ('add', 'add ports', TRUE);
		echo "</td><td><input type=text size=8 name=port_name></td>\n";
		echo "<td><input type=text name=port_label></td><td>";
		printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type_id'), $prefs['selected']);
		echo "<td><input type=text name=port_numbering_start size=3 maxlength=3></td>\n";
		echo "<td><input type=text name=port_numbering_count size=3 maxlength=3></td>\n";
		echo "<td>&nbsp;</td><td>";
		printImageHREF ('add', 'add ports', TRUE);
		echo "</td></tr></form>";
		echo "</table><br>\n";
	}
	if (getConfigVar('ENABLE_MULTIPORT_FORM') == 'yes')
		finishPortlet();
	if (getConfigVar('ENABLE_MULTIPORT_FORM') != 'yes')
		return;

	startPortlet ('Add/update multiple ports');
	printOpFormIntro ('addMultiPorts');
	$formats = array
	(
		'c3600asy' => 'Cisco 3600 async: sh line | inc TTY',
		'fiwg' => 'Foundry ServerIron/FastIron WorkGroup/Edge: sh int br',
		'fisxii' => 'Foundry FastIron SuperX/II4000: sh int br',
		'ssv1' => 'SSV:&lt;interface name&gt; [&lt;MAC address&gt;]',
	);
	echo 'Format: ' . getSelect ($formats, array ('name' => 'format'), 'ssv1');
	echo 'Default port type: ';
	printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type'), $prefs['selected']);
	echo "<input type=submit value='Parse output'><br>\n";
	echo "<textarea name=input cols=100 rows=50></textarea><br>\n";
	echo '</form>';
	finishPortlet();
}

function renderIPForObject ($object_id)
{
	function printNewItemTR ($default_type)
	{
		global $aat;
		printOpFormIntro ('add');
		echo "<tr><td>"; // left btn
		printImageHREF ('add', 'allocate', TRUE);
		echo "</td>";
		echo "<td class=tdleft><input type='text' size='10' name='bond_name'></td>\n"; // if-name
		echo "<td class=tdleft><input type=text name='ip'></td>\n"; // IP
		if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
			echo "<td colspan=2>&nbsp;</td>"; // network, routed by
		echo '<td>';
		printSelect ($aat, array ('name' => 'bond_type'), $default_type); // type
		echo "</td><td>&nbsp;</td><td>"; // misc
		printImageHREF ('add', 'allocate', TRUE); // right btn
		echo "</td></tr></form>";
	}
	global $aat;
	startPortlet ('Allocations');
	echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'><tr>\n";
	echo '<th>&nbsp;</th>';
	echo '<th>OS interface</th>';
	echo '<th>IP address</th>';
	if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
	{
		echo '<th>network</th>';
		echo '<th>routed by</th>';
	}
	echo '<th>type</th>';
	echo '<th>misc</th>';
	echo '<th>&nbsp;</th>';
	echo '</tr>';

	$alloc_list = ''; // most of the output is stored here
	$used_alloc_types = array();
	foreach (getObjectIPAllocations ($object_id) as $alloc)
	{
		if (! isset ($used_alloc_types[$alloc['type']]))
			$used_alloc_types[$alloc['type']] = 0;
		$used_alloc_types[$alloc['type']]++;

		$rendered_alloc = callHook ('getRenderedAlloc', $object_id, $alloc);
		$alloc_list .= getOutputOf ('printOpFormIntro', 'upd', array ('ip' => $alloc['addrinfo']['ip']));
		$alloc_list .= "<tr class='${rendered_alloc['tr_class']}' valign=top>";

		$alloc_list .= "<td>" . getOpLink (array ('op' => 'del', 'ip' => $alloc['addrinfo']['ip']), '', 'delete', 'Delete this IP address') . "</td>";
		$alloc_list .= "<td class=tdleft><input type='text' name='bond_name' value='${alloc['osif']}' size=10>" . $rendered_alloc['td_name_suffix'] . "</td>";
		$alloc_list .= $rendered_alloc['td_ip'];
		if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
		{
			$alloc_list .= $rendered_alloc['td_network'];
			$alloc_list .= $rendered_alloc['td_routed_by'];
		}
		$alloc_list .= '<td>' . getSelect ($aat, array ('name' => 'bond_type'), $alloc['type']) . "</td>";
		$alloc_list .= $rendered_alloc['td_peers'];
		$alloc_list .= "<td>" .getImageHREF ('save', 'Save changes', TRUE) . "</td>";

		$alloc_list .= "</form></tr>\n";
	}
	asort ($used_alloc_types, SORT_NUMERIC);
	$most_popular_type = empty ($used_alloc_types) ? 'regular' : array_last (array_keys ($used_alloc_types));

	if ($list_on_top = (getConfigVar ('ADDNEW_AT_TOP') != 'yes'))
		echo $alloc_list;
	printNewItemTR ($most_popular_type);
	if (! $list_on_top)
		echo $alloc_list;

	echo "</table><br>\n";
	finishPortlet();
}

// This function is deprecated. Do not rely on its internals,
// it will probably be removed in the next major relese.
// Use new showError, showWarning, showSuccess functions.
// Log array is stored in global $log_messages. Its format is simple: plain ordered array
// with values having keys 'c' (both message code and severity) and 'a' (sprintf arguments array)
function showMessageOrError ()
{
	global $log_messages;

	startSession();
	if (isset ($_SESSION['log']))
	{
		$log_messages = array_merge ($_SESSION['log'], $log_messages);
		unset ($_SESSION['log']);
	}
	session_commit();

	if (empty ($log_messages))
		return;
	$msginfo = array
	(
// records 0~99 with success messages
		0 => array ('code' => 'success', 'format' => '%s'),
		5 => array ('code' => 'success', 'format' => 'added record "%s" successfully'),
		6 => array ('code' => 'success', 'format' => 'updated record "%s" successfully'),
		7 => array ('code' => 'success', 'format' => 'deleted record "%s" successfully'),
		8 => array ('code' => 'success', 'format' => 'Port %s successfully linked with %s'),
		10 => array ('code' => 'success', 'format' => 'Added %u ports, updated %u ports, encountered %u errors.'),
		21 => array ('code' => 'success', 'format' => 'Generation complete'),
		26 => array ('code' => 'success', 'format' => 'updated %u records successfully'),
		37 => array ('code' => 'success', 'format' => 'added %u records successfully'),
		38 => array ('code' => 'success', 'format' => 'removed %u records successfully'),
		43 => array ('code' => 'success', 'format' => 'Saved successfully.'),
		44 => array ('code' => 'success', 'format' => '%s failures and %s successfull changes.'),
		48 => array ('code' => 'success', 'format' => 'added a record successfully'),
		49 => array ('code' => 'success', 'format' => 'deleted a record successfully'),
		51 => array ('code' => 'success', 'format' => 'updated a record successfully'),
		57 => array ('code' => 'success', 'format' => 'Reset complete'),
		58 => array ('code' => 'success', 'format' => '%u device(s) unmounted successfully'),
		63 => array ('code' => 'success', 'format' => '%u change request(s) have been processed'),
		67 => array ('code' => 'success', 'format' => "Tag rolling done, %u objects involved"),
		71 => array ('code' => 'success', 'format' => 'File "%s" was linked successfully'),
		72 => array ('code' => 'success', 'format' => 'File was unlinked successfully'),
		82 => array ('code' => 'success', 'format' => "Bulk port creation was successful. %u ports created, %u failed"),
		87 => array ('code' => 'success', 'format' => '802.1Q recalculate: %d ports changed on %d switches'),
// records 100~199 with fatal error messages
		100 => array ('code' => 'error', 'format' => '%s'),
		109 => array ('code' => 'error', 'format' => 'failed updating a record'),
		131 => array ('code' => 'error', 'format' => 'invalid format requested'),
		141 => array ('code' => 'error', 'format' => 'Encountered %u errors, updated %u record(s)'),
		149 => array ('code' => 'error', 'format' => 'Turing test failed'),
		150 => array ('code' => 'error', 'format' => 'Can only change password under DB authentication.'),
		151 => array ('code' => 'error', 'format' => 'Old password doesn\'t match.'),
		152 => array ('code' => 'error', 'format' => 'New passwords don\'t match.'),
		154 => array ('code' => 'error', 'format' => "Verification error: %s"),
		155 => array ('code' => 'error', 'format' => 'Save failed.'),
		159 => array ('code' => 'error', 'format' => 'Permission denied moving port %s from VLAN%u to VLAN%u'),
		161 => array ('code' => 'error', 'format' => 'Endpoint not found. Please either set FQDN attribute or assign an IP address to the object.'),
		162 => array ('code' => 'error', 'format' => 'More than one IP address is assigned to this object, please configure FQDN attribute.'),
		170 => array ('code' => 'error', 'format' => 'There is no network for IP address "%s"'),
		172 => array ('code' => 'error', 'format' => 'Malformed request'),
		179 => array ('code' => 'error', 'format' => 'Expired form has been declined.'),
		188 => array ('code' => 'error', 'format' => "Fatal SNMP failure"),
		189 => array ('code' => 'error', 'format' => "Unknown OID '%s'"),
		191 => array ('code' => 'error', 'format' => "deploy was blocked due to conflicting configuration versions"),

// records 200~299 with warnings
		200 => array ('code' => 'warning', 'format' => '%s'),
		201 => array ('code' => 'warning', 'format' => 'nothing happened...'),
		206 => array ('code' => 'warning', 'format' => '%s is not empty'),
		207 => array ('code' => 'warning', 'format' => 'File upload failed, error: %s'),

// records 300~399 with notices
		300 => array ('code' => 'neutral', 'format' => '%s'),

	);
	// Handle the arguments. Is there any better way to do it?
	foreach ($log_messages as $record)
	{
		if (!isset ($record['c']) or !isset ($msginfo[$record['c']]))
		{
			$prefix = isset ($record['c']) ? $record['c'] . ': ' : '';
			echo "<div class=msg_neutral>(${prefix}this message was lost)</div>";
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
	$log_messages = array();
}

// renders two tables: port link status and learned MAC list
function renderPortsInfo($object_id)
{
	$breed = detectDeviceBreed ($object_id);
	try
	{
		if (permitted (NULL, NULL, 'get_link_status'))
		{
			if (validBreedFunction ($breed, 'getportstatus'))
				$linkStatus = queryDevice ($object_id, 'getportstatus');
		}
		else
			showWarning ("You don't have permission to view ports link status");

		if (permitted (NULL, NULL, 'get_mac_list'))
		{
			if (validBreedFunction ($breed, 'getmaclist'))
				$macList = sortPortList (queryDevice ($object_id, 'getmaclist'));
		}
		else
			showWarning ("You don't have permission to view learned MAC list");
	}
	catch (RTGatewayError $e)
	{
		showError ($e->getMessage());
		return;
	}

	global $nextorder;
	echo "<table width='100%'><tr>";
	if (! empty ($linkStatus))
	{
		echo "<td valign='top' width='50%'>";
		startPortlet('Link status');
		echo "<table width='80%' class='widetable' cellspacing=0 cellpadding='5px' align='center'><tr><th>Port<th><th>Link status<th>Link info</tr>";
		$order = 'even';
		foreach ($linkStatus as $pn => $link)
		{
			switch ($link['status'])
			{
				case 'up':
					$img_filename = 'link-up.png';
					break;
				case 'down':
					$img_filename = 'link-down.png';
					break;
				case 'disabled':
					$img_filename = 'link-disabled.png';
					break;
				default:
					$img_filename = '1x1t.gif';
			}

			echo "<tr class='row_$order'>";
			$order = $nextorder[$order];
			echo '<td>' . $pn;
			echo '<td>' . '<img width=16 height=16 src="?module=chrome&uri=pix/' . $img_filename . '">';
			echo '<td>' . $link['status'];
			$info = '';
			if (isset ($link['speed']))
				$info .= $link['speed'];
			if (isset ($link['duplex']))
			{
				if (! empty ($info))
					$info .= ', ';
				$info .= $link['duplex'];
			}
			echo '<td>' . $info;
			echo '</tr>';
		}
		echo "</table></td>";
		finishPortlet();
	}

	if (! empty ($macList))
	{
		echo "<td valign='top' width='50%'>";
		$rendered_macs = '';
		$mac_count = 0;
		$rendered_macs .=  "<table width='80%' class='widetable' cellspacing=0 cellpadding='5px' align='center'><tr><th>MAC<th>Vlan<th>Port</tr>";
		$order = 'even';
		foreach ($macList as $pn => $list)
		{
			$order = $nextorder[$order];
			foreach ($list as $item)
			{
				++$mac_count;
				$rendered_macs .= "<tr class='row_$order'>";
				$rendered_macs .= '<td style="font-family: monospace">' . $item['mac'];
				$rendered_macs .= '<td>' . $item['vid'];
				$rendered_macs .= '<td>' . $pn;
				$rendered_macs .= '</tr>';
			}
		}
		$rendered_macs .= "</table></td>";

		startPortlet("Learned MACs ($mac_count)");
		echo $rendered_macs;
		finishPortlet();
	}

	echo "</td></tr></table>";
}

/*
The following conditions must be met:
1. We can mount onto free atoms only. This means: if any record for an atom
already exists in RackSpace, it can't be used for mounting.
2. We can't unmount from 'W' atoms. Operator should review appropriate comments
and either delete them before unmounting or refuse to unmount the object.
*/
function renderRackSpaceForObject ($object_id)
{
	// Always process occupied racks plus racks chosen by user. First get racks with
	// already allocated rackspace...
	$workingRacksData = getResidentRacksData ($object_id);
	// ...and then add those chosen by user (if any).
	if (isset($_REQUEST['rackmulti']))
		foreach ($_REQUEST['rackmulti'] as $cand_id)
			if (!isset ($workingRacksData[$cand_id]))
			{
				$rackData = spotEntity ('rack', $cand_id);
				amplifyCell ($rackData);
				$workingRacksData[$cand_id] = $rackData;
			}

	// Get a list of rack ids that are parents of this object.
	$object = spotEntity ('object', $object_id);
	$parentRacks = reduceSubarraysToColumn (getParents ($object, 'rack'), 'id');

	// Main layout starts.
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0><tr>";

	// Left portlet with rack list.
	echo "<td class=pcleft height='1%'>";
	startPortlet ('Racks');
	$allRacksData = listCells ('rack');

	// filter rack list to match only racks having common tags with the object (reducing $allRacksData)
	if (! isset ($_REQUEST['show_all_racks']) and getConfigVar ('FILTER_RACKLIST_BY_TAGS') == 'yes')
	{
		$matching_racks = array();
		$matched_tags = array();
		foreach ($allRacksData as $rack)
		{
			$tag_chain = array_merge ($rack['etags'], $rack['itags']);
			foreach ($object['etags'] as $tag)
				if (tagOnChain ($tag, $tag_chain))
				{
					$matching_racks[$rack['id']] = $rack;
					$matched_tags[$tag['id']] = $tag;
					break;
				}
		}
		// add current object's racks even if they dont match filter
		foreach ($workingRacksData as $rack_id => $rack)
			if (! isset ($matching_racks[$rack_id]))
				$matching_racks[$rack_id] = $rack;
		// if matching racks found, and rack list is reduced, show 'show all' link
		if (count ($matching_racks) and count ($matching_racks) != count ($allRacksData))
		{
			$filter_text = '';
			foreach ($matched_tags as $tag)
				$filter_text .= (empty ($filter_text) ? '' : ' or ') . '{' . $tag['tag'] . '}';
			$href_show_all = trim($_SERVER['REQUEST_URI'], '&');
			$href_show_all .= htmlspecialchars('&show_all_racks=1');
			echo "(filtered by <span class='filter-text'>$filter_text</span>, <a href='$href_show_all'>show all</a>)<p>";
			$allRacksData = $matching_racks;
		}
	}

	if (count ($allRacksData) <= getConfigVar ('RACK_PRESELECT_THRESHOLD'))
		foreach ($allRacksData as $rack)
			if (!array_key_exists ($rack['id'], $workingRacksData))
			{
				amplifyCell ($rack);
				$workingRacksData[$rack['id']] = $rack;
			}
	foreach (array_keys ($workingRacksData) as $rackId)
		applyObjectMountMask ($workingRacksData[$rackId], $object_id);
	printOpFormIntro ('updateObjectAllocation');
	renderRackMultiSelect ('rackmulti[]', $allRacksData, array_keys ($workingRacksData));
	echo "<br><br>";
	finishPortlet();
	echo "</td>";

	// Middle portlet with comment and submit.
	echo "<td class=pcleft>";
	startPortlet ('Comment (for Rackspace History)');
	echo "<textarea name=comment rows=10 cols=40></textarea><br>\n";
	echo "<input type=submit value='Save' name=got_atoms>\n";
	echo "<br><br>";
	finishPortlet();
	echo "</td>";

	// Right portlet with rendered racks. If this form submit is not final, we have to
	// reflect the former state of the grid in current form.
	echo "<td class=pcright rowspan=2 height='1%'>";
	startPortlet ('Working copy');
	includeJQueryUI (false);
	addJS ('js/racktables.js');
	addJS ('js/bulkselector.js');
	echo '<table border=0 cellspacing=10 align=center><tr>';
	foreach ($workingRacksData as $rack_id => $rackData)
	{
		$is_ro = !rackModificationPermitted ($rackData, 'updateObjectAllocation');
		// Order is important here: only original allocation is highlighted.
		highlightObject ($rackData, $object_id);
		markupAtomGrid ($rackData, 'T');
		// If we have a form processed, discard user input and show new database
		// contents.
		if (!$is_ro && isset ($_REQUEST['rackmulti'][0])) // is an update
			mergeGridFormToRack ($rackData);
		echo "<td valign=top>";
		echo "<center>\n<h2 style='margin:0px'>${rackData['name']}</h2>\n";
		$read_only_text = $is_ro ? '(read-only)' : '&nbsp;';
		echo "<p style='color: red; margin-top:0px'>${read_only_text}</p>\n";
		echo "<table class=rack id=selectableRack border=0 cellspacing=0 cellpadding=1>\n";
		echo "<tr><th width='10%'>&nbsp;</th>";
		echo "<th width='20%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '0', ${rackData['height']})\">Front</a></th>";
		echo "<th width='50%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '1', ${rackData['height']})\">Interior</a></th>";
		echo "<th width='20%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '2', ${rackData['height']})\">Back</a></th></tr>\n";
		renderAtomGrid ($rackData, $is_ro);
		echo "<tr><th width='10%'>&nbsp;</th>";
		echo "<th width='20%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '0', ${rackData['height']})\">Front</a></th>";
		echo "<th width='50%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '1', ${rackData['height']})\">Interior</a></th>";
		echo "<th width='20%'><a href='javascript:;' onclick=\"toggleColumnOfAtoms('${rack_id}', '2', ${rackData['height']})\">Back</a></th></tr>\n";
		echo "</table>\n<br>\n";
		// Determine zero-u checkbox status.
		// If form has been submitted, use form data, otherwise use DB data.
		if (!$is_ro && isset($_REQUEST['op']))
			$checked = isset($_REQUEST['zerou_'.$rack_id]) ? 'checked' : '';
		else
			$checked = in_array($rack_id, $parentRacks) ? 'checked' : '';
		$disabled_text = $is_ro ? ' disabled' : '';
		echo "<label for=zerou_${rack_id}>Zero-U:</label> <input type=checkbox ${checked} name=zerou_${rack_id} id=zerou_${rack_id}${disabled_text}>\n<br><br>\n";
		echo "<input type='button' onclick='uncheckAll();' value='Uncheck all'>\n";
		echo '</center></td>';
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
			echo "<tr><th>" . inverseRackUnit ($i, $rackData) . "</th>";
			for ($locidx = 0; $locidx < 3; $locidx++)
			{
				$state = $rackData[$i][$locidx]['state'];
				echo "<td class='atom state_${state}'>&nbsp;</td>\n";
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
	$objects = array();
	$objects_count = getEntitiesCount ('object');
	$showobjecttype = (getConfigVar ('SHOW_OBJECTTYPE') == 'yes');

	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";

	if ($objects_count == 0)
		echo '<h2>No objects exist</h2>';
	// 1st attempt: do not fetch all objects if cellfilter is empty and rendering empty result is enabled
	elseif (! ($cellfilter['is_empty'] && renderEmptyResults ($cellfilter, 'objects', $objects_count)))
	{
		$objects = applyCellFilter ('object', $cellfilter);
		// 2nd attempt: do not render all fetched objects if rendering empty result is enabled
		if (! renderEmptyResults ($cellfilter, 'objects', count($objects)))
		{
			startPortlet ('Objects (' . count ($objects) . ')');
			echo '<br><br><table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
			echo '<tr><th>Common name</th>';
			if ($showobjecttype)
				echo '<th>Type</th>';
			echo '<th>Visible label</th><th>Asset tag</th><th>Row/Rack or Container</th></tr>';
			$order = 'odd';
			# gather IDs of all objects and fetch rackspace info in one pass
			$idlist = array();
			foreach ($objects as $obj)
				$idlist[] = $obj['id'];
			$mountinfo = getMountInfo ($idlist);
			$containerinfo = getContainerInfo ($idlist);
			foreach ($objects as $obj)
			{
				$problem = ($obj['has_problems'] == 'yes') ? 'has_problems' : '';
				echo "<tr class='row_${order} tdleft ${problem}' valign=top><td>" . mkA ("<strong>${obj['dname']}</strong>", 'object', $obj['id']);
				if (count ($obj['etags']))
					echo '<br><small>' . serializeTags ($obj['etags'], makeHref(array('page'=>$pageno, 'tab'=>'default')) . '&') . '</small>';
				echo '</td>';
				if ($showobjecttype)
					echo "<td>" . decodeObjectType ($obj['objtype_id']) . "</td>";
				echo "<td>${obj['label']}</td>";
				echo "<td>${obj['asset_no']}</td>";
				$places = array();
				if (array_key_exists ($obj['id'], $containerinfo))
					foreach ($containerinfo[$obj['id']] as $ci)
						$places[] = mkA ($ci['container_dname'], 'object', $ci['container_id']);
				if (array_key_exists ($obj['id'], $mountinfo))
					foreach ($mountinfo[$obj['id']] as $mi)
						$places[] = mkA ($mi['row_name'], 'row', $mi['row_id']) . '/' . mkA ($mi['rack_name'], 'rack', $mi['rack_id']);
				if (! count ($places))
					$places[] = 'Unmounted';
				echo "<td>" . implode (', ', $places) . '</td>';
				echo '</tr>';
				$order = $nextorder[$order];
			}
			echo '</table>';
			finishPortlet();
		}
	}

	echo "</td><td class=pcright width='25%'>";

	renderCellFilterPortlet ($cellfilter, 'object', $objects);
	echo "</td></tr></table>\n";
}

// This function returns TRUE if the result set is too big to be rendered, and no filter is set.
// In this case it renders the describing message instead.
function renderEmptyResults($cellfilter, $entities_name, $count = NULL)
{
	if (!$cellfilter['is_empty'])
		return FALSE;
	if (isset ($_REQUEST['show_all_objects']))
		return FALSE;
	$max = intval(getConfigVar('MAX_UNFILTERED_ENTITIES'));
	if (0 == $max || $count <= $max)
		return FALSE;

	$href_show_all = trim($_SERVER['REQUEST_URI'], '&');
	$href_show_all .= htmlspecialchars('&show_all_objects=1');
	$suffix = isset ($count) ? " ($count)" : '';
	echo <<<END
<p>Please set a filter to display the corresponging $entities_name.
<br><a href="$href_show_all">Show all $entities_name$suffix</a>
END;
	return TRUE;
}

// History viewer for history-enabled simple dictionaries.
function renderObjectHistory ($object_id)
{
	$order = 'odd';
	global $nextorder;
	echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
	echo '<tr><th>change time</th><th>author</th><th>name</th><th>visible label</th><th>asset no</th><th>has problems?</th><th>comment</th></tr>';
	$result = usePreparedSelectBlade
	(
		'SELECT ctime, user_name, name, label, asset_no, has_problems, comment FROM ObjectHistory WHERE id=? ORDER BY ctime',
		array ($object_id)
	);
	while ($row = $result->fetch (PDO::FETCH_NUM))
	{
		echo "<tr class=row_${order}><td>${row[0]}</td>";
		for ($i = 1; $i <= 6; $i++)
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
		echo '</td><td>' . stringForTD ($row['comment'], 0) . '</td></tr>';
		$order = $nextorder[$order];
	}
	echo "</table>\n";
	finishPortlet();

	echo '</td></tr></table>';
}

function renderIPSpaceRecords ($tree, $baseurl, $target = 0, $level = 1)
{
	$self = __FUNCTION__;
	$knight = (getConfigVar ('IPV4_ENABLE_KNIGHT') == 'yes');

	// scroll page to the highlighted item
	if ($target && isset ($_REQUEST['hl_net']))
		addAutoScrollScript ("net-$target");

	foreach ($tree as $item)
	{
		$display_routers = (getConfigVar ('IPV4_TREE_RTR_AS_CELL') != 'none');

		if (isset ($item['id']))
		{
			$decor = array ('indent' => $level);
			if ($item['symbol'] == 'node-collapsed')
				$decor['symbolurl'] = "${baseurl}&eid=${item['id']}&hl_net=1";
			elseif ($item['symbol'] == 'node-expanded')
				$decor['symbolurl'] = $baseurl . ($item['parent_id'] ? "&eid=${item['parent_id']}&hl_net=1" : '');
			$tr_class = '';
			if ($target == $item['id'] && isset ($_REQUEST['hl_net']))
			{
				$decor['tdclass'] = ' highlight';
				$tr_class = $decor['tdclass'];
			}
			echo "<tr valign=top class=\"$tr_class\">";
			printIPNetInfoTDs ($item, $decor);

			// capacity and usage
			echo "<td class=tdcenter>";
			echo getRenderedIPNetCapacity ($item);
			echo "</td>";

			if ($display_routers)
				printRoutersTD (findNetRouters ($item), getConfigVar ('IPV4_TREE_RTR_AS_CELL'));
			echo "</tr>";
			if ($item['symbol'] == 'node-expanded' or $item['symbol'] == 'node-expanded-static')
				$self ($item['kids'], $baseurl, $target, $level + 1);
		}
		elseif (getConfigVar ('IPV4_TREE_SHOW_UNALLOCATED') == 'yes')
		{
			// non-allocated (spare) IP range
			echo "<tr valign=top>";
			printIPNetInfoTDs ($item, array ('indent' => $level, 'knight' => $knight, 'tdclass' => 'sparenetwork'));

			// capacity and usage
			echo "<td class=tdcenter>";
			echo getRenderedIPNetCapacity ($item);
			echo "</td>";
			if ($display_routers)
				echo "<td></td>";
			echo "</tr>";
		}
	}
}

function renderIPSpace()
{
	global $pageno, $tabno;
	$realm = ($pageno == 'ipv4space' ? 'ipv4net' : 'ipv6net');
	$cellfilter = getCellFilter();

	// expand request can take either natural values or "ALL". Zero means no expanding.
	$eid = isset ($_REQUEST['eid']) ? $_REQUEST['eid'] : 0;

	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";

	$netlist = array();
	if (! ($cellfilter['is_empty'] && ! $eid && renderEmptyResults($cellfilter, 'IP nets', getEntitiesCount ($realm))))
	{
		$top = NULL;
		foreach (listCells ($realm) as $net)
		{
			if (isset ($top) and IPNetContains ($top, $net))
				;
			elseif (! count ($cellfilter['expression']) or judgeCell ($net, $cellfilter['expression']))
				$top = $net;
			else
				continue;
			$netlist[$net['id']] = $net;
		}
		$netcount = count ($netlist);
		$tree = prepareIPTree ($netlist, $eid);

		if (! renderEmptyResults($cellfilter, 'IP nets', count($tree)))
		{
			startPortlet ("networks (${netcount})");
			echo '<h4>';
			$all = "<a href='".makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'eid'=>'ALL')) .
					$cellfilter['urlextra'] . "'>expand&nbsp;all</a>";
			$none = "<a href='".makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'eid'=>'NONE')) .
					$cellfilter['urlextra'] . "'>collapse&nbsp;all</a>";
			$auto = "<a href='".makeHref(array('page'=>$pageno, 'tab'=>$tabno)) .
				$cellfilter['urlextra'] . "'>auto-collapse</a>";

			if ($eid === 0)
				echo 'auto-collapsing at threshold ' . getConfigVar ('TREE_THRESHOLD') . " ($all / $none)";
			elseif ($eid === 'ALL')
				echo "expanding all ($auto / $none)";
			elseif ($eid === 'NONE')
				echo "collapsing all ($all / $auto)";
			else
			{
				try
				{
					$netinfo = spotEntity ($realm, $eid);
					echo "expanding ${netinfo['ip']}/${netinfo['mask']} ($auto / $all / $none)";
				}
				catch (EntityNotFoundException $e)
				{
					// ignore invalid eid error
				}
			}
			echo "</h4><table class='widetable' border=0 cellpadding=5 cellspacing=0 align='center'>\n";
			echo "<tr><th>prefix</th><th>name/tags</th><th>capacity</th>";
			if (getConfigVar ('IPV4_TREE_RTR_AS_CELL') != 'none')
				echo "<th>routed by</th>";
			echo "</tr>\n";
			$baseurl = makeHref(array('page'=>$pageno, 'tab'=>$tabno)) . $cellfilter['urlextra'];
			renderIPSpaceRecords ($tree, $baseurl, $eid);
			echo "</table>\n";
			finishPortlet();
		}
	}

	echo '</td><td class=pcright>';
	renderCellFilterPortlet ($cellfilter, $realm, $netlist);
	echo "</td></tr></table>\n";
}

function renderIPSpaceEditor()
{
	global $pageno;
	$realm = ($pageno == 'ipv4space' ? 'ipv4net' : 'ipv6net');
	$net_page = $realm; // 'ipv4net', 'ipv6net'
	$addrspaceList = listCells ($realm);
	startPortlet ('Manage existing (' . count ($addrspaceList) . ')');
	if (count ($addrspaceList))
	{
		echo "<table class='widetable' border=0 cellpadding=5 cellspacing=0 align='center'>\n";
		echo "<tr><th>&nbsp;</th><th>prefix</th><th>name</th><th>capacity</th></tr>";
		foreach ($addrspaceList as $netinfo)
		{
			echo "<tr valign=top><td>";
			if (! isIPNetworkEmpty ($netinfo))
				printImageHREF ('nodestroy', 'There are ' . count ($netinfo['addrlist']) . ' allocations inside');
			else
				echo getOpLink (array	('op' => 'del', 'id' => $netinfo['id']), '', 'destroy', 'Delete this prefix');
			echo '</td><td class=tdleft>' . mkA ("${netinfo['ip']}/${netinfo['mask']}", $net_page, $netinfo['id']) . '</td>';
			echo '<td class=tdleft>' . stringForTD ($netinfo['name']);
			if (count ($netinfo['etags']))
				echo '<br><small>' . serializeTags ($netinfo['etags']) . '</small>';
			echo '</td><td>';
			echo getRenderedIPNetCapacity ($netinfo);
			echo '</tr>';
		}
		echo "</table>";
		finishPortlet();
	}
}

function renderIPNewNetForm ()
{
	global $pageno;
	if ($pageno == 'ipv6space')
	{
		$realm = 'ipv6net';
		$regexp = '^[a-fA-F0-9:]*:[a-fA-F0-9:\.]*/\d{1,3}$';
	}
	else
	{
		$realm = 'ipv4net';
		$regexp = '^(\d{1,3}\.){3}\d{1,3}/\d{1,2}$';
	}

	// IP prefix validator
	addJS ('js/live_validation.js');
	$regexp = addslashes ($regexp);
	addJS (<<<END
$(document).ready(function () {
	$('form#add input[name="range"]').attr('match', '$regexp');
	Validate.init();
});
END
	, TRUE);

	startPortlet ('Add new');
	printOpFormIntro ('add');
	echo '<table border=0 cellpadding=5 cellspacing=0 align=center>';

	// inputs column
	$prefix_value = empty ($_REQUEST['set-prefix']) ? '' : $_REQUEST['set-prefix'];
	echo "<th class=tdright>Prefix:</th><td class=tdleft><input type=text name='range' size=36 class='live-validate' value='${prefix_value}'></td>";
	echo '<tr><th class=tdright>VLAN:</th><td class=tdleft>';
	echo getOptionTree ('vlan_ck', getAllVLANOptions(), array ('select_class' => 'vertical')) . '</td></tr>';
	echo "<tr><th class=tdright>Name:</th><td class=tdleft><input type=text name='name' size='20'></td></tr>";
	echo '<tr><th class=tdright>Tags:</th><td class="tdleft">';
	printTagsPicker ();
	echo '</td></tr>';
	echo '<tr><td class=tdright><input type=checkbox name="is_connected"></td><th class=tdleft>reserve subnet-router anycast address</th></tr>';
	echo "<tr><td colspan=2>";
	printImageHREF ('CREATE', 'Add a new network', TRUE);
	echo '</td></tr>';
	echo "</table></form><br><br>\n";
	finishPortlet();
}

function getRenderedIPNetBacktrace ($range)
{
	if (getConfigVar ('EXT_IPV4_VIEW') != 'yes')
		return array();

	$v = ($range['realm'] == 'ipv4net') ? 4 : 6;
	$space = "ipv${v}space"; // ipv4space, ipv6space
	$tag = "\$ip${v}netid_"; // $ip4netid_, $ip6netid_

	$ret = array();
	// Build a backtrace from all parent networks.
	$clen = $range['mask'];
	$backtrace = array();
	$backtrace['&rarr;'] = $range;
	$key = '';
	while (NULL !== ($upperid = getIPAddressNetworkId ($range['ip_bin'], $clen)))
	{
		$upperinfo = spotEntity ($range['realm'], $upperid);
		$clen = $upperinfo['mask'];
		$key .= '&uarr;';
		$backtrace[$key] = $upperinfo;
	}
	foreach (array_reverse ($backtrace) as $arrow => $ainfo)
	{
		$link = '<a href="' . makeHref (array (
			'page' => $space,
			'tab' => 'default',
			'clear-cf' => '',
			'cfe' => '{' . $tag . $ainfo['id'] . '}',
			'hl_net' => 1,
			'eid' => $range['id'],
		)) . '" title="View IP tree with this net as root">' . $arrow . '</a>';
		$ret[] = array ($link, getOutputOf ('renderCell', $ainfo));
	}
	return $ret;
}

function renderIPNetwork ($id)
{
	global $pageno;
	$realm = $pageno; // 'ipv4net', 'ipv6net'
	$range = spotEntity ($realm, $id);
	loadIPAddrList ($range);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${range['ip']}/${range['mask']}</h1><h2>";
	echo htmlspecialchars ($range['name'], ENT_QUOTES, 'UTF-8') . "</h2></td></tr>\n";

	echo "<tr><td class=pcleft width='50%'>";

	// render summary portlet
	$summary = array();
	$summary['%% used'] = getRenderedIPNetCapacity ($range);
	$summary = getRenderedIPNetBacktrace ($range) + $summary;
	if ($realm == 'ipv4net')
	{
		$summary[] = array ('Netmask:', ip4_format ($range['mask_bin']));
		$summary[] = array ('Netmask:', "0x" . strtoupper (implode ('', unpack ('H*', $range['mask_bin']))));
		$summary['Wildcard bits'] = ip4_format ( ~ $range['mask_bin']);
	}

	$reuse_domain = considerConfiguredConstraint ($range, '8021Q_MULTILINK_LISTSRC');
	$domainclass = array();
	foreach (array_count_values (reduceSubarraysToColumn ($range['8021q'], 'domain_id')) as $domain_id => $vlan_count)
		$domainclass[$domain_id] = $vlan_count == 1 ? '' : ($reuse_domain ? '{trwarning}' : '{trerror}');
	foreach ($range['8021q'] as $item)
		$summary[] = array ($domainclass[$item['domain_id']] . 'VLAN:', formatVLANAsHyperlink (getVlanRow ($item['domain_id'] . '-' . $item['vlan_id'])));
	if (getConfigVar ('EXT_IPV4_VIEW') == 'yes' and count ($routers = findNetRouters ($range)))
	{
		$summary['Routed by'] = '';
		foreach ($routers as $rtr)
			$summary['Routed by'] .= getOutputOf ('renderRouterCell', $rtr['ip_bin'], $rtr['iface'], spotEntity ('object', $rtr['id']));
	}
	$summary['tags'] = '';
	renderEntitySummary ($range, 'summary', $summary);

	if (strlen ($range['comment']))
	{
		startPortlet ('Comment');
		echo '<div class=commentblock>' . string_insert_hrefs (htmlspecialchars ($range['comment'], ENT_QUOTES, 'UTF-8')) . '</div>';
		finishPortlet ();
	}

	renderFilesPortlet ($realm, $id);
	echo "</td>\n";

	echo "<td class=pcright>";
	startPortlet ('details');
	renderIPNetworkAddresses ($range);
	finishPortlet();
	echo "</td></tr></table>\n";
}

// Used solely by renderSeparator
function renderEmptyIPv6 ($ip_bin, $hl_ip)
{
	$class = 'tdleft';
	if ($ip_bin === $hl_ip)
		$class .= ' highlight';
	$fmt = ip6_format ($ip_bin);
	echo "<tr class='$class'><td><a name='ip-$fmt' href='" . makeHref (array ('page' => 'ipaddress', 'ip' => $fmt)) . "'>" . $fmt;
	$editable = permitted ('ipaddress', 'properties', 'editAddress')
		? 'editable'
		: '';
	echo "</a></td><td><span class='rsvtext $editable id-$fmt op-upd-ip-name'></span></td>";
	echo "<td><span class='rsvtext $editable id-$fmt op-upd-ip-comment'></span></td><td>&nbsp;</td></tr>\n";
}

// Renders empty table line to shrink empty IPv6 address ranges.
// If the range consists of single address, renders the address instead of empty line.
// Renders address $hl_ip inside the range.
// Used solely by renderIPv6NetworkAddresses
function renderSeparator ($first, $last, $hl_ip)
{
	$self = __FUNCTION__;
	if (strcmp ($first, $last) > 0)
		return;
	if ($first == $last)
		renderEmptyIPv6 ($first, $hl_ip);
	elseif (isset ($hl_ip) && strcmp ($hl_ip, $first) >= 0 && strcmp ($hl_ip, $last) <= 0)
	{ // $hl_ip is inside the range $first - $last
		$self ($first, ip_prev ($hl_ip), $hl_ip);
		renderEmptyIPv6 ($hl_ip, $hl_ip);
		$self (ip_next ($hl_ip), $last, $hl_ip);
	}
	else
		echo "<tr><td colspan=4 class=tdleft></td></tr>\n";
}

// calculates page number that contains given $ip (used by renderIPv6NetworkAddresses)
function getPageNumOfIPv6 ($list, $ip_bin, $maxperpage)
{
	if (intval ($maxperpage) <= 0 || count ($list) <= $maxperpage)
		return 0;
	$keys = array_keys ($list);
	for ($i = 1; $i <= count ($keys); $i++)
		if (strcmp ($keys[$i-1], $ip_bin) >= 0)
			return intval ($i / $maxperpage);
	return intval (count ($list) / $maxperpage);
}

function renderIPNetworkAddresses ($range)
{
	switch (strlen ($range['ip_bin']))
	{
		case 4:  return renderIPv4NetworkAddresses ($range);
		case 16: return renderIPv6NetworkAddresses ($range);
		default: throw new InvalidArgException ("range['ip_bin']", $range['ip_bin']);
	}
}

function renderIPv4NetworkPageLink ($rangeid, $page, $title)
{
	global $pageno, $tabno;
	return "<a href='".makeHref (array ('page' => $pageno, 'tab' => $tabno, 'id' => $rangeid, 'pg' => $page)) . "' title='".$title."'>".$page."</a> ";
}

function renderIPv4NetworkPagination ($range, $page, $numpages)
{
	$rendered_pager = '';
	$startip = ip4_bin2int ($range['ip_bin']);
	$endip = ip4_bin2int (ip_last ($range));
	$rangeid = $range['id'];
	// Should make this configurable perhaps
	// How many pages before/after current page to show
	$prepostpagecount = 8;
	// Minimum pages where pagination does not happen
	$paginationat = 16; // 16 pages is a /20, 32 is a /19
	$maxperpage = getConfigVar ('IPV4_ADDRS_PER_PAGE');
	if ($numpages <= $paginationat)
	{
		// create original pagination
		for ($i = 0; $i < $numpages; $i++)
			if ($i == $page)
				$rendered_pager .= "<b>".$i."</b> ";
			else
				$rendered_pager .= renderIPv4NetworkPageLink($rangeid, $i, ip4_format(ip4_int2bin($startip + $i * $maxperpage)));
	}
	else // number of pages > page range, create ranged pagination
	{
		// page is within first subset
		if ($page - $prepostpagecount <= 1)
		{
			for ($i = 0; $i < $page; $i++)
				$rendered_pager .= renderIPv4NetworkPageLink($rangeid, $i, ip4_format(ip4_int2bin($startip + $i * $maxperpage)));
		}
		// render 0 ... [page - prepostpagecount] [page - prepostpagecount + 1] ... [page - 1]
		else
		{
			$rendered_pager .= renderIPv4NetworkPageLink($rangeid, 0, ip4_format(ip4_int2bin($startip)));
			$rendered_pager .= "... ";
			for ($i = $page - $prepostpagecount; $i < $page; $i++)
				$rendered_pager .= renderIPv4NetworkPageLink($rangeid, $i, ip4_format(ip4_int2bin($startip + $i * $maxperpage)));
		}
		// render current page
		$rendered_pager .= "<b>".$page."</b> ";
		// page is within last subset
		if ($page + $prepostpagecount >= $numpages-2)
		{
			for ($i = $page+1; $i < $numpages; $i++)
				$rendered_pager .= renderIPv4NetworkPageLink($rangeid, $i, ip4_format(ip4_int2bin($startip + $i * $maxperpage)));
		}
		// render [page + 1] [page + 2] ... [page + postpagecount] ... [end page]
		else
		{
			for ($i = $page+1; $i <= $page+$prepostpagecount; $i++)
				$rendered_pager .= renderIPv4NetworkPageLink($rangeid, $i, ip4_format(ip4_int2bin($startip + $i * $maxperpage)));
			$rendered_pager .= "... ";
			$rendered_pager .= renderIPv4NetworkPageLink($rangeid, ($numpages-1), ip4_format(ip4_int2bin($endip)));
		}
	}
	return $rendered_pager;
}

function renderIPv4NetworkAddresses ($range)
{
	global $pageno, $tabno, $aac_left;
	$startip = ip4_bin2int ($range['ip_bin']);
	$endip = ip4_bin2int (ip_last ($range));

	if (isset ($_REQUEST['hl_ip']))
	{
		$hl_ip = ip4_bin2int (ip4_parse ($_REQUEST['hl_ip']));
		addAutoScrollScript ('ip-' . $_REQUEST['hl_ip']); // scroll page to highlighted ip
	}

	// pager
	$maxperpage = getConfigVar ('IPV4_ADDRS_PER_PAGE');
	$address_count = $endip - $startip + 1;
	$page = 0;
	$rendered_pager = '';
	if ($address_count > $maxperpage && $maxperpage > 0)
	{
		$page = isset ($_REQUEST['pg']) ? $_REQUEST['pg'] : (isset ($hl_ip) ? intval (($hl_ip - $startip) / $maxperpage) : 0);
		if ($numpages = ceil ($address_count / $maxperpage))
		{
			echo '<h3>' . ip4_format (ip4_int2bin ($startip)) . ' ~ ' . ip4_format (ip4_int2bin ($endip)) . '</h3>';
			$rendered_pager = renderIPv4NetworkPagination ($range, $page, $numpages);
		}
		$startip = $startip + $page * $maxperpage;
		$endip = min ($startip + $maxperpage - 1, $endip);
	}

	echo $rendered_pager;
	echo "<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center' width='100%'>\n";
	echo "<tr><th>Address</th><th>Name</th><th>Comment</th><th>Allocation</th></tr>\n";

	markupIPAddrList ($range['addrlist']);
	for ($ip = $startip; $ip <= $endip; $ip++)
	{
		$ip_bin = ip4_int2bin ($ip);
		$dottedquad = ip4_format ($ip_bin);
		$tr_class = (isset ($hl_ip) && $hl_ip == $ip ? 'highlight' : '');
		if (isset ($range['addrlist'][$ip_bin]))
			$addr = $range['addrlist'][$ip_bin];
		else
		{
			echo "<tr class='tdleft $tr_class'><td class=tdleft><a name='ip-$dottedquad' href='" . makeHref(array('page'=>'ipaddress', 'ip' => $dottedquad)) . "'>$dottedquad</a></td>";
			$editable = permitted ('ipaddress', 'properties', 'editAddress')
				? 'editable'
				: '';
			echo "<td><span class='rsvtext $editable id-$dottedquad op-upd-ip-name'></span></td>";
			echo "<td><span class='rsvtext $editable id-$dottedquad op-upd-ip-comment'></span></td><td></td></tr>\n";
			continue;
		}
		// render IP change history
		$title = '';
		$history_class = '';
		if (isset ($addr['last_log']))
		{
			$title = ' title="' . htmlspecialchars ($addr['last_log']['user'] . ', ' . formatAge ($addr['last_log']['time']) , ENT_QUOTES) . '"';
			$history_class = 'hover-history underline';
		}
		$tr_class .= ' ' . $addr['class'];
		echo "<tr class='tdleft $tr_class'>";
		echo "<td><a class='$history_class' $title name='ip-$dottedquad' href='".makeHref(array('page'=>'ipaddress', 'ip'=>$addr['ip']))."'>${addr['ip']}</a></td>";
		$editable =
			(empty ($addr['allocs']) || !empty ($addr['name']) || !empty ($addr['comment']))
			&& permitted ('ipaddress', 'properties', 'editAddress')
			? 'editable'
			: '';
		echo "<td><span class='rsvtext $editable id-$dottedquad op-upd-ip-name'>${addr['name']}</span></td>";
		echo "<td><span class='rsvtext $editable id-$dottedquad op-upd-ip-comment'>${addr['comment']}</span></td>";
		echo "<td>";
		$delim = '';
		if ( $addr['reserved'] == 'yes')
		{
			echo "<strong>RESERVED</strong> ";
			$delim = '; ';
		}
		foreach ($addr['allocs'] as $ref)
		{
			echo $delim . $aac_left[$ref['type']];
			echo makeIPAllocLink ($ip_bin, $ref, TRUE);
			$delim = '; ';
		}
		if ($delim != '')
			$delim = '<br>';
		foreach ($addr['vslist'] as $vs_id)
		{
			$vs = spotEntity ('ipv4vs', $vs_id);
			echo $delim . mkA ("${vs['name']}:${vs['vport']}/${vs['proto']}", 'ipv4vs', $vs['id']) . '&rarr;';
			$delim = '<br>';
		}
		foreach ($addr['vsglist'] as $vs_id)
		{
			$vs = spotEntity ('ipvs', $vs_id);
			echo $delim . mkA ($vs['name'], 'ipvs', $vs['id']) . '&rarr;';
			$delim = '<br>';
		}
		foreach ($addr['rsplist'] as $rsp_id)
		{
			$rsp = spotEntity ('ipv4rspool', $rsp_id);
			echo "${delim}&rarr;" . mkA ($rsp['name'], 'ipv4rspool', $rsp['id']);
			$delim = '<br>';
		}
		echo "</td></tr>\n";
	}
	// end of iteration
	if (permitted (NULL, NULL, 'set_reserve_comment'))
		addJS ('js/inplace-edit.js');

	echo "</table>";
	if (! empty ($rendered_pager))
		echo '<p>' . $rendered_pager . '</p>';
}

function renderIPv6NetworkAddresses ($netinfo)
{
	global $pageno, $tabno, $aac_left;
	echo "<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center' width='100%'>\n";
	echo "<tr><th>Address</th><th>Name</th><th>Comment</th><th>Allocation</th></tr>\n";

	$hl_ip = NULL;
	if (isset ($_REQUEST['hl_ip']))
	{
		$hl_ip = ip6_parse ($_REQUEST['hl_ip']);
		addAutoScrollScript ('ip-' . ip6_format ($hl_ip));
	}

	$addresses = $netinfo['addrlist'];
	ksort ($addresses);
	markupIPAddrList ($addresses);

	// pager
	$maxperpage = getConfigVar ('IPV4_ADDRS_PER_PAGE');
	if (count ($addresses) > $maxperpage && $maxperpage > 0)
	{
		$page = isset ($_REQUEST['pg']) ? $_REQUEST['pg'] : (isset ($hl_ip) ? getPageNumOfIPv6 ($addresses, $hl_ip, $maxperpage) : 0);
		$numpages = ceil (count ($addresses) / $maxperpage);
		echo "<center><h3>$numpages pages:</h3>";
		for ($i=0; $i<$numpages; $i++)
		{
			if ($i == $page)
				echo "<b>$i</b> ";
			else
				echo "<a href='" . makeHref (array ('page' => $pageno, 'tab' => $tabno, 'id' => $netinfo['id'], 'pg' => $i)) . "'>$i</a> ";
		}
		echo "</center>";
	}

	$i = 0;
	$interruped = FALSE;
	$prev_ip = ip_prev ($netinfo['ip_bin']);
	foreach ($addresses as $ip_bin => $addr)
	{
		if (isset ($page))
		{
			++$i;
			if ($i <= $maxperpage * $page)
				continue;
			elseif ($i > $maxperpage * ($page + 1))
			{
				$interruped = TRUE;
				break;
			}
		}

		if ($ip_bin != ip_next ($prev_ip))
			renderSeparator (ip_next ($prev_ip), ip_prev ($ip_bin), $hl_ip);
		$prev_ip = $ip_bin;

		// render IP change history
		$title = '';
		$history_class = '';
		if (isset ($addr['last_log']))
		{
			$title = ' title="' . htmlspecialchars ($addr['last_log']['user'] . ', ' . formatAge ($addr['last_log']['time']) , ENT_QUOTES) . '"';
			$history_class = 'hover-history underline';
		}

		$tr_class = $addr['class'] . ' tdleft' . ($hl_ip === $ip_bin ? ' highlight' : '');
		echo "<tr class='$tr_class'>";
		echo "<td><a class='$history_class' $title name='ip-${addr['ip']}' href='" . makeHref (array ('page' => 'ipaddress', 'ip' => $addr['ip'])) . "'>${addr['ip']}</a></td>";
		$editable =
			(empty ($addr['allocs']) || !empty ($addr['name'])
			&& permitted ('ipaddress', 'properties', 'editAddress')
			? 'editable'
			: '');
		echo "<td><span class='rsvtext $editable id-${addr['ip']} op-upd-ip-name'>${addr['name']}</span></td>";
		echo "<td><span class='rsvtext $editable id-${addr['ip']} op-upd-ip-comment'>${addr['comment']}</span></td><td>";
		$delim = '';
		if ( $addr['reserved'] == 'yes')
		{
			echo "<strong>RESERVED</strong> ";
			$delim = '; ';
		}
		foreach ($addr['allocs'] as $ref)
		{
			echo $delim . $aac_left[$ref['type']];
			echo makeIPAllocLink ($ip_bin, $ref, TRUE);
			$delim = '; ';
		}
		if ($delim != '')
			$delim = '<br>';
		foreach ($addr['vslist'] as $vs_id)
		{
			$vs = spotEntity ('ipv4vs', $vs_id);
			echo $delim . mkA ("${vs['name']}:${vs['vport']}/${vs['proto']}", 'ipv4vs', $vs['id']) . '&rarr;';
			$delim = '<br>';
		}
		foreach ($addr['vsglist'] as $vs_id)
		{
			$vs = spotEntity ('ipvs', $vs_id);
			echo $delim . mkA ($vs['name'], 'ipvs', $vs['id']) . '&rarr;';
			$delim = '<br>';
		}
		foreach ($addr['rsplist'] as $rsp_id)
		{
			$rsp = spotEntity ('ipv4rspool', $rsp_id);
			echo "${delim}&rarr;" . mkA ($rsp['name'], 'ipv4rspool', $rsp['id']);
			$delim = '<br>';
		}
		echo "</td></tr>\n";
	}
	if (! $interruped)
		renderSeparator (ip_next ($prev_ip), ip_last ($netinfo), $hl_ip);
	if (isset ($page))
	{ // bottom pager
		echo "<tr><td colspan=3>";
		if ($page > 0)
			echo "<a href='" . makeHref (array ('page' => $pageno, 'tab' => $tabno, 'id' => $netinfo['id'], 'pg' => $page - 1)) . "'><< prev</a> ";
		if ($page < $numpages - 1)
			echo "<a href='" . makeHref (array ('page' => $pageno, 'tab' => $tabno, 'id' => $netinfo['id'], 'pg' => $page + 1)) . "'>next >></a> ";
		echo "</td></tr>";
	}
	echo "</table>";
	if (permitted (NULL, NULL, 'set_reserve_comment'))
		addJS ('js/inplace-edit.js');
}

function renderIPNetworkProperties ($id)
{
	global $pageno;
	$netdata = spotEntity (etypeByPageno(), $id);
	echo "<center><h1>${netdata['ip']}/${netdata['mask']}</h1></center>\n";
	printOpFormIntro ('editRange');
	echo "<table border=0 cellpadding=5 cellspacing=0 align='center'>\n";
	echo '<tr><th class=tdright><label for=nameinput>Name:</label></th>';
	echo "<td class=tdleft><input type=text name=name id=nameinput size=80 maxlength=255 value='";
	echo htmlspecialchars ($netdata['name'], ENT_QUOTES, 'UTF-8') . "'></tr>";
	echo "<tr><th class=tdright>Tags:</th><td class=tdleft>";
	printTagsPicker ();
	echo "</td></tr>\n";
	echo '<tr><th class=tdright><label for=commentinput>Comment:</label></th>';
	echo "<td class=tdleft><textarea name=comment id=commentinput cols=80 rows=25>\n";
	echo stringForTextarea ($netdata['comment']) . "</textarea></tr>";
	echo "<tr><td colspan=2 class=tdcenter>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr></table></form>\n";

	echo '<center>';
	if (! isIPNetworkEmpty ($netdata))
		echo getOpLink (NULL, 'delete this prefix', 'nodestroy', 'There are ' . count ($netdata['addrlist']) . ' allocations inside');
	else
		echo getOpLink (array('op'=>'del'), 'delete this prefix', 'destroy');
	echo '</center>';
}

function renderIPAddress ($ip_bin)
{
	global $aat, $nextorder;
	$address = getIPAddress ($ip_bin);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${address['ip']}</h1></td></tr>\n";

	echo "<tr><td class=pcleft>";

	$summary = array();
	if (strlen ($address['name']))
		$summary['Name'] = $address['name'];
	if (strlen ($address['comment']))
		$summary['Comment'] = $address['comment'];
	$summary['Reserved'] = $address['reserved'];
	$summary['Allocations'] = count ($address['allocs']);
	if (isset ($address['outpf']))
		$summary['Originated NAT connections'] = count ($address['outpf']);
	if (isset ($address['inpf']))
		$summary['Arriving NAT connections'] = count ($address['inpf']);
	renderEntitySummary ($address, 'summary', $summary);

	// render SLB portlet
	if (! empty ($address['vslist']) or ! empty ($address['vsglist']) or ! empty ($address['rsplist']))
	{
		startPortlet ("");
		if (! empty ($address['vsglist']))
		{
			printf ("<h2>virtual service groups (%d):</h2>", count ($address['vsglist']));
			foreach ($address['vsglist'] as $vsg_id)
				renderSLBEntityCell (spotEntity ('ipvs', $vsg_id));
		}

		if (! empty ($address['vslist']))
		{
			printf ("<h2>virtual services (%d):</h2>", count ($address['vslist']));
			foreach ($address['vslist'] as $vs_id)
				renderSLBEntityCell (spotEntity ('ipv4vs', $vs_id));
		}

		if (! empty ($address['rsplist']))
		{
			printf ("<h2>RS pools (%d):</h2>", count ($address['rsplist']));
			foreach ($address['rsplist'] as $rsp_id)
				renderSLBEntityCell (spotEntity ('ipv4rspool', $rsp_id));
		}
		finishPortlet();
	}
	echo "</td>\n";

	echo "<td class=pcright>";
	if (isset ($address['class']) and ! empty ($address['allocs']))
	{
		startPortlet ('allocations');
		echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>\n";
		echo "<tr><th>object</th><th>OS interface</th><th>allocation type</th></tr>\n";
		// render all allocation records for this address the same way
		foreach ($address['allocs'] as $bond)
		{
			$tr_class = "${address['class']} tdleft";
			if (isset ($_REQUEST['hl_object_id']) and $_REQUEST['hl_object_id'] == $bond['object_id'])
				$tr_class .= ' highlight';
			echo "<tr class='$tr_class'>" .
				"<td>" . makeIPAllocLink ($ip_bin, $bond) . "</td>" .
				"<td>${bond['name']}</td>" .
				"<td><strong>" . $aat[$bond['type']] . "</strong></td>" .
				"</tr>\n";
		}
		echo "</table><br><br>";
		finishPortlet();
	}

	if (! empty ($address['rsplist']))
	{
		startPortlet ("RS pools:");
		foreach ($address['rsplist'] as $rsp_id)
		{
			renderSLBEntityCell (spotEntity ('ipv4rspool', $rsp_id));
			echo '<br>';
		}
		finishPortlet();
	}

	if (! empty ($address['vsglist']))
		foreach ($address['vsglist'] as $vsg_id)
			renderSLBTriplets2 (spotEntity ('ipvs', $vsg_id), FALSE, $ip_bin);

	if (! empty ($address['vslist']))
		renderSLBTriplets ($address);

	foreach (array ('outpf' => 'departing NAT rules', 'inpf' => 'arriving NAT rules') as $key => $title)
		if (! empty ($address[$key]))
		{
			startPortlet ($title);
			echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>\n";
			echo "<tr><th>proto</th><th>from</th><th>to</th><th>comment</th></tr>\n";
			foreach ($address[$key] as $rule)
			{
				echo "<tr>";
				echo "<td>" . $rule['proto'] . "</td>";
				echo "<td>" . getRenderedIPPortPair ($rule['localip'], $rule['localport']) . "</td>";
				echo "<td>" . getRenderedIPPortPair ($rule['remoteip'], $rule['remoteport']) . "</td>";
				echo "<td>" . $rule['description'] . "</td></tr>";
				echo "</tr>";
			}
			echo "</table>";
			finishPortlet();
		}

	echo "</td></tr>";
	echo "</table>\n";
}

function renderIPAddressProperties ($ip_bin)
{
	$address = getIPAddress ($ip_bin);
	echo "<center><h1>${address['ip']}</h1></center>\n";

	startPortlet ('update');
	echo "<table border=0 cellpadding=10 cellpadding=1 align='center'>\n";
	printOpFormIntro ('editAddress');
	echo '<tr><td class=tdright><label for=id_name>Name:</label></td>';
	echo "<td class=tdleft><input type=text name=name id=id_name size=20 value='${address['name']}'></tr>";
	echo '<tr><td class=tdright><label for=id_comment>Comment:</label></td>';
	echo "<td class=tdleft><input type=text name=comment id=id_comment size=20 value='${address['comment']}'></tr>";
	echo '<td class=tdright><label for=id_reserved>Reserved:</label></td>';
	echo "<td class=tdleft><input type=checkbox name=reserved id=id_reserved size=20 ";
	echo ($address['reserved']=='yes') ? 'checked' : '';
	echo "></tr><tr><td class=tdleft>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></form><td class=tdright>";
	if (!strlen ($address['name']) and $address['reserved'] == 'no')
		printImageHREF ('CLEAR gray');
	else
	{
		printOpFormIntro ('editAddress', array ('name' => '', 'reserved' => '', 'comment' => ''));
		printImageHREF ('CLEAR', 'Release', TRUE);
		echo "</form>";
	}
	echo "</td></tr></table>\n";
	finishPortlet();
}

function renderIPAddressAllocations ($ip_bin)
{
	function printNewItemTR ()
	{
		global $aat;
		printOpFormIntro ('add');
		echo "<tr><td>";
		printImageHREF ('add', 'allocate', TRUE);
		echo "</td><td>";
		printSelect (getNarrowObjectList ('IPV4OBJ_LISTSRC'), array ('name' => 'object_id'));
		echo "</td><td><input type=text name=bond_name size=10></td><td>";
		printSelect ($aat, array ('name' => 'bond_type', 'regular'));
		echo "</td><td>";
		printImageHREF ('add', 'allocate', TRUE);
		echo "</td></form></tr>";
	}
	global $aat;

	$address = getIPAddress ($ip_bin);
	echo "<center><h1>${address['ip']}</h1></center>\n";
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
			printOpFormIntro ('upd', array ('object_id' => $bond['object_id']));
			echo "<td>" . getOpLink (array ('op' => 'del', 'object_id' => $bond['object_id'] ), '', 'delete', 'Unallocate address') . "</td>";
			echo "<td>" . makeIPAllocLink ($ip_bin, $bond) . "</td>";
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
		printSelect (array ('TCP' => 'TCP', 'UDP' => 'UDP', 'ALL' => 'ALL'), array ('name' => 'proto'));

		$options = array();
		foreach ($alloclist as $ip_bin => $alloc)
		{
			$ip = $alloc['addrinfo']['ip'];
			$name = (!isset ($alloc['addrinfo']['name']) or !strlen ($alloc['addrinfo']['name'])) ? '' : (' (' . stringForLabel ($alloc['addrinfo']['name']) . ')');
			$osif = (!isset ($alloc['osif']) or !strlen ($alloc['osif'])) ? '' : ($alloc['osif'] . ': ');
			$options[$ip] = $osif . $ip . $name;
		}
		printSelect ($options, array ('name' => 'localip'));

		echo ":<input type='text' name='localport' size='4'></td>";
		echo "<td><input type='text' name='remoteip' id='remoteip' size='10'>";
		echo getPopupLink ('inet4list', array(), 'findobjectip', 'find', 'Find object');
		echo ":<input type='text' name='remoteport' size='4'></td><td></td>";
		echo "<td colspan=1><input type='text' name='description' size='20'></td><td>";
		printImageHREF ('add', 'Add new NAT rule', TRUE);
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
		$localip_bin = ip4_parse ($pf['localip']);
		if (isset ($focus['ipv4'][$localip_bin]))
		{
			$class = $focus['ipv4'][$localip_bin]['addrinfo']['class'];
			$osif = $focus['ipv4'][$localip_bin]['osif'] . ': ';
		}

		echo "<tr class='$class'>";
		echo "<td>" . getOpLink  (
			array (
				'op'=>'delNATv4Rule',
				'localip'=>$pf['localip'],
				'localport'=>$pf['localport'],
				'remoteip'=>$pf['remoteip'],
				'remoteport'=>$pf['remoteport'],
				'proto'=>$pf['proto'],
			), '', 'delete', 'Delete NAT rule'
		) . "</td>";
		echo "<td>${pf['proto']}/${osif}" . getRenderedIPPortPair ($pf['localip'], $pf['localport']);
		if (strlen ($pf['local_addr_name']))
			echo ' (' . $pf['local_addr_name'] . ')';
		echo "</td>";
		echo "<td>" . getRenderedIPPortPair ($pf['remoteip'], $pf['remoteport']) . "</td>";

		$address = getIPAddress (ip4_parse ($pf['remoteip']));

		echo "<td class='description'>";
		if (count ($address['allocs']))
			foreach ($address['allocs'] as $bond)
				echo mkA ("${bond['object_name']}(${bond['name']})", 'object', $bond['object_id']) . ' ';
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
		echo "<tr><td>" . getOpLink (
			array(
				'op'=>'delNATv4Rule',
				'localip'=>$pf['localip'],
				'localport'=>$pf['localport'],
				'remoteip'=>$pf['remoteip'],
				'remoteport'=>$pf['remoteport'],
				'proto'=>$pf['proto'],
			), '', 'delete', 'Delete NAT rule'
		) . "</td>";
		echo "<td>${pf['proto']}/" . getRenderedIPPortPair ($pf['localip'], $pf['localport']) . "</td>";
		echo '<td class="description">' . mkA ($pf['object_name'], 'object', $pf['object_id']);
		echo "</td><td>" . getRenderedIPPortPair ($pf['remoteip'], $pf['remoteport']) . "</td>";
		echo "<td class='description'>${pf['description']}</td></tr>";
	}

	echo "</table><br><br>";
}

function renderAddMultipleObjectsForm ()
{
	$typelist = withoutLocationTypes (readChapter (CHAP_OBJTYPE, 'o'));
	$typelist[0] = 'select type...';
	$typelist = cookOptgroups ($typelist);
	$max = getConfigVar ('MASSCOUNT');

	startPortlet ('Distinct types, same tags');
	printOpFormIntro ('addObjects');
	echo '<table border=0 align=center>';
	echo "<tr><th>Object type</th><th>Common name</th><th>Visible label</th>";
	echo "<th>Asset tag</th><th>Tags</th></tr>\n";
	for ($i = 0; $i < $max; $i++)
	{
		echo '<tr><td>';
		// Don't employ DEFAULT_OBJECT_TYPE to avoid creating ghost records for pre-selected empty rows.
		printNiftySelect ($typelist, array ('name' => "${i}_object_type_id"), 0);
		echo '</td>';
		echo "<td><input type=text size=30 name=${i}_object_name></td>";
		echo "<td><input type=text size=30 name=${i}_object_label></td>";
		echo "<td><input type=text size=20 name=${i}_object_asset_no></td>";
		if ($i == 0)
		{
			echo "<td valign=top rowspan=${max}>";
			printTagsPicker ();
			echo "</td>\n";
		}
		echo "</tr>\n";
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
	printTagsPicker ();
	echo "</td></tr>";
	echo "<tr><td colspan=2><input type=submit name=got_very_fast_data value='Go!'></td></tr></table>\n";
	echo "</form>\n";
	finishPortlet();
}

function searchHandler()
{
	$terms = trim ($_REQUEST['q']);
	if (!strlen ($terms))
		throw new InvalidRequestArgException('q', $_REQUEST['q'], 'Search string cannot be empty.');
	renderSearchResults ($terms, searchEntitiesByText ($terms));
}

function renderSearchResults ($terms, $summary)
{
	// calculate the number of found objects
	$nhits = 0;
	foreach ($summary as $realm => $list)
		$nhits += count ($list);

	if ($nhits == 0)
	{
		echo "<center><h2>Nothing found for '${terms}'</h2></center>";
		return;
	}
	elseif ($nhits == 1)
	{
		foreach ($summary as $realm => $record)
		{
			if (is_array ($record))
				$record = array_shift ($record);
			break;
		}
		$url = buildSearchRedirectURL ($realm, $record);
		if (isset ($url))
			redirectUser ($url);
	}
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
							$record = $aval[$attr_id];
							echo "<tr><th width='50%' class=sticker>${record['name']}:</th>";
							echo "<td class=sticker>" . formatAttributeValue ($record) . "</td></tr>";
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
									$port_href = '<a href="' . makeHref (array
									(
										'page' => 'object',
										'object_id' => $object['id'],
										'hl_port_id' => $port_id
									)) . '">port ' . $port['name'] . '</a>';
									echo "<tr><td>${port_href}:</td>";
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
					if (isset ($obj['by_cableid']))
					{
						echo '<ul>';
						foreach ($obj['by_cableid'] as $cableid)
							echo "<li>link cable ID: ${cableid}</li>";
						echo '</ul>';
					}
					echo "</td></tr>";
					$order = $nextorder[$order];
				}
				echo '</table>';
				finishPortlet();
				break;
			case 'ipv4net':
			case 'ipv6net':
				if ($where == 'ipv4net')
					startPortlet ("<a href='index.php?page=ipv4space'>IPv4 networks</a>");
				elseif ($where == 'ipv6net')
					startPortlet ("<a href='index.php?page=ipv6space'>IPv6 networks</a>");

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
			case 'ipv6addressbydescr':
				if ($where == 'ipv4addressbydescr')
					startPortlet ('IPv4 addresses');
				elseif ($where == 'ipv6addressbydescr')
					startPortlet ('IPv6 addresses');
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
				// FIXME: address, parent network, routers (if extended view is enabled)
				echo '<tr><th>Address</th><th>Description</th><th>Comment</th></tr>';
				foreach ($what as $addr)
				{
					echo "<tr class=row_${order}><td class=tdleft>";
					$fmt = ip_format ($addr['ip']);
					$parentnet = getIPAddressNetworkId ($addr['ip']);
					if ($parentnet !== NULL)
						echo "<a href='" . makeHref (array (
								'page' => strlen ($addr['ip']) == 16 ? 'ipv6net' : 'ipv4net',
								'id' => $parentnet,
								'tab' => 'default',
								'hl_ip' => $fmt,
							)) . "'>${fmt}</a></td>";
					else
						echo "<a href='index.php?page=ipaddress&tab=default&ip=${fmt}'>${fmt}</a></td>";
					echo "<td class=tdleft>${addr['name']}</td><td>${addr['comment']}</td></tr>";
					$order = $nextorder[$order];
				}
				echo '</table>';
				finishPortlet();
				break;
			case 'ipv4rspool':
				startPortlet ("<a href='index.php?page=ipv4slb&tab=rspools'>RS pools</a>");
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
			case 'ipvs':
				startPortlet ("<a href='index.php?page=ipv4slb&tab=vs'>VS groups</a>");
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
				startPortlet ("<a href='index.php?page=ipv4slb&tab=default'>Virtual services</a>");
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
			case 'row':
				startPortlet ("<a href='index.php?page=rackspace'>Rack rows</a>");
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
				foreach ($what as $cell)
				{
					echo "<tr class=row_${order}><td class=tdleft>";
					echo mkCellA ($cell);
					echo "</td></tr>";
					$order = $nextorder[$order];
				}
				echo '</table>';
				finishPortlet();
				break;
			case 'location':
				startPortlet ("<a href='index.php?page=rackspace'>Locations</a>");
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
			case 'vlan':
				startPortlet ("<a href='index.php?page=8021q'>VLANs</a>");
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
				foreach ($what as $vlan)
				{
					echo "<tr class=row_${order}><td class=tdleft>";
					echo formatVLANAsHyperlink (getVlanRow ($vlan['id'])) . "</td></tr>";
					$order = $nextorder[$order];
				}
				echo '</table>';
				finishPortlet();
				break;
			default: // you can use that in your plugins to add some non-standard search results
				startPortlet($where);
				echo $what;
				finishPortlet();
		}
}

// This function prints a table of checkboxes to aid the user in toggling mount atoms
// from one state to another. The first argument is rack data as
// produced by amplifyCell(), the second is the R/O flag. When this flag is true all checkboxes
// are become disabled

function renderAtomGrid ($data, $is_ro=FALSE)
{
	$rack_id = $data['id'];
	addJS ('js/racktables.js');
	for ($unit_no = $data['height']; $unit_no > 0; $unit_no--)
	{
		echo "<tr><th><a href='javascript:;' onclick=\"toggleRowOfAtoms('${rack_id}','${unit_no}')\">" . inverseRackUnit ($unit_no, $data) . "</a></th>";
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			$name = "atom_${rack_id}_${unit_no}_${locidx}";
			$state = $data[$unit_no][$locidx]['state'];
			echo "<td class='atom state_${state}";
			if (isset ($data[$unit_no][$locidx]['hl']))
				echo $data[$unit_no][$locidx]['hl'];
			echo "'>";
			$disabled_text = $is_ro ? ' disabled' : '';
			if (!($data[$unit_no][$locidx]['enabled'] === TRUE))
				echo "<input type=checkbox id=${name} disabled>";
			else
				echo "<input type=checkbox" . $data[$unit_no][$locidx]['checked'] . " name=${name} id=${name}${disabled_text}>";
			echo '</td>';
		}
		echo "</tr>\n";
	}
}

function renderCellList ($realm = NULL, $title = 'items', $do_amplify = FALSE, $celllist = NULL)
{
	if ($realm === NULL)
		$realm = etypeByPageno();
	global $nextorder;
	$order = 'odd';
	$cellfilter = getCellFilter();
	if (! isset ($celllist))
		$celllist = applyCellFilter ($realm, $cellfilter);
	else
		$celllist = filterCellList ($celllist, $cellfilter['expression']);

	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";

	if ($realm != 'file' || ! renderEmptyResults ($cellfilter, 'files', count($celllist)))
	{
		if ($do_amplify)
			array_walk ($celllist, 'amplifyCell');
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
	}
	echo '</td><td class=pcright>';
	renderCellFilterPortlet ($cellfilter, $realm, $celllist);
	echo "</td></tr></table>\n";
}

function renderLocationPage ($location_id)
{
	$locationData = spotEntity ('location', $location_id);
	amplifyCell ($locationData);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0><tr>";

	// Left column with information.
	echo "<td class=pcleft>";
	$summary = array();
	$summary['Name'] = $locationData['name'];
	if (! empty ($locationData['parent_id']))
		$summary['Parent location'] = mkA ($locationData['parent_name'], 'location', $locationData['parent_id']);
	$summary['Child locations'] = count($locationData['locations']);
	$summary['Rows'] = count($locationData['rows']);
	if ($locationData['has_problems'] == 'yes')
		$summary[] = array ('<tr><td colspan=2 class=msg_error>Has problems</td></tr>');
	foreach (getAttrValuesSorted ($locationData['id']) as $record)
		if
		(
			$record['value'] != '' and
			permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $record['id'])))
		)
			$summary['{sticker}' . $record['name']] = formatAttributeValue ($record);
	$summary['tags'] = '';
	renderEntitySummary ($locationData, 'Summary', $summary);
	if ($locationData['comment'] != '')
	{
		startPortlet ('Comment');
		echo '<div class=commentblock>' . string_insert_hrefs ($locationData['comment']) . '</div>';
		finishPortlet ();
	}
	renderFilesPortlet ('location', $location_id);
	echo '</td>';

	// Right column with list of rows and child locations
	echo '<td class=pcright>';
	startPortlet ('Rows ('. count ($locationData['rows']) . ')');
	echo "<table border=0 cellspacing=0 cellpadding=5 align=center>\n";
	foreach ($locationData['rows'] as $row_id => $name)
		echo '<tr><td>' . mkA ($name, 'row', $row_id) . '</td></tr>';
	echo "</table>\n";
	finishPortlet();
	startPortlet ('Child Locations (' . count ($locationData['locations']) . ')');
	echo "<table border=0 cellspacing=0 cellpadding=5 align=center>\n";
	foreach ($locationData['locations'] as $location_id => $name)
		echo '<tr><td>' . mkA ($name, 'location', $location_id) . '</td></tr>';
	echo "</table>\n";
	finishPortlet();
	echo '</td>';
	echo '</tr></table>';
}

function renderEditLocationForm ($location_id)
{
	global $pageno;
	$location = spotEntity ('location', $location_id);
	amplifyCell ($location);

	startPortlet ('Attributes');
	printOpFormIntro ('updateLocation');
	echo '<table border=0 align=center>';
	echo "<tr><td>&nbsp;</td><th class=tdright>Parent location:</th><td class=tdleft>";
	$locations = array ();
	$locations[0] = '-- NOT SET --';
	foreach (listCells ('location') as $id => $locationInfo)
		$locations[$id] = $locationInfo['name'];
	natcasesort($locations);
	printSelect ($locations, array ('name' => 'parent_id'), $location['parent_id']);
	echo "</td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Name (required):</th><td class=tdleft><input type=text name=name value='${location['name']}'></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Tags:</th><td class=tdleft>";
	printTagsPicker ();
	echo "</td></tr>\n";
	// optional attributes
	$values = getAttrValuesSorted ($location_id);
	$num_attrs = count($values);
	echo "<input type=hidden name=num_attrs value=${num_attrs}>\n";
	$i = 0;
	foreach ($values as $record)
	{
		echo "<input type=hidden name=${i}_attr_id value=${record['id']}>";
		echo '<tr><td>';
		if (strlen ($record['value']))
			echo getOpLink (array ('op'=>'clearSticker', 'attr_id'=>$record['id']), '', 'clear', 'Clear value', 'need-confirmation');
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
				$chapter = cookOptgroups ($chapter, 1562, $record['key']);
				printNiftySelect ($chapter, array ('name' => "${i}_value"), $record['key']);
				break;
		}
		echo "</td></tr>\n";
		$i++;
	}
	echo "<tr><td>&nbsp;</td><th class=tdright>Has problems:</th><td class=tdleft><input type=checkbox name=has_problems";
	if ($location['has_problems'] == 'yes')
		echo ' checked';
	echo "></td></tr>\n";
	if (count ($location['locations']) == 0 and count ($location['rows']) == 0)
	{
		echo "<tr><td>&nbsp;</td><th class=tdright>Actions:</th><td class=tdleft>";
		echo getOpLink (array('op'=>'deleteLocation'), '', 'destroy', 'Delete location', 'need-confirmation');
		echo "&nbsp;</td></tr>\n";
	}
	echo "<tr><td colspan=3><b>Comment:</b><br><textarea name=comment rows=10 cols=80>${location['comment']}</textarea></td></tr>";
	echo "<tr><td class=submit colspan=3>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>\n";
	echo '</form></table><br>';
	finishPortlet();

	startPortlet ('History');
	renderObjectHistory ($location_id);
	finishPortlet();
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
	echo '<td class=pcright>';
	startPortlet ('Rack diagram');
	renderRack ($rack_id);
	finishPortlet();
	echo '</td>';

	echo '</tr></table>';
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

function renderSNMPPortFinder ($object_id)
{
	if (!extension_loaded ('snmp'))
	{
		echo "<div class=msg_error>The PHP SNMP extension is not loaded.  Cannot continue.</div>";
		return;
	}
	$snmpcomm = getConfigVar('DEFAULT_SNMP_COMMUNITY');
	if (empty($snmpcomm))
		$snmpcomm = 'public';

	startPortlet ('SNMPv1');
	printOpFormIntro ('querySNMPData', array ('ver' => 1));
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th class=tdright><label for=communityv1>Community: </label></th>';
	echo "<td class=tdleft><input type=text name=community id=communityv1 value='${snmpcomm}'></td></tr>";
	echo '<tr><td colspan=2><input type=submit value="Try now"></td></tr>';
	echo '</table></form>';
	finishPortlet();

	startPortlet ('SNMPv2c');
	printOpFormIntro ('querySNMPData', array ('ver' => 2));
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th class=tdright><label for=communityv2>Community: </label></th>';
	echo "<td class=tdleft><input type=text name=community id=communityv2 value='${snmpcomm}'></td></tr>";
	echo '<tr><td colspan=2><input type=submit value="Try now"></td></tr>';
	echo '</table></form>';
	finishPortlet();

	startPortlet ('SNMPv3');
	printOpFormIntro ('querySNMPData', array ('ver' => 3));
	$sloptions = array
	(
		'noAuthNoPriv' => 'noAuth and noPriv',
		'authNoPriv' => 'auth without Priv',
		'authPriv' => 'auth with Priv',
	);
?>
	<table cellspacing=0 cellpadding=5 align=center class=widetable>
	<tr>
		<th class=tdright><label for=sec_name>Security User:</label></th>
		<td class=tdleft><input type=text id=sec_name name=sec_name value='<?php echo $snmpcomm;?>'></td>
	</tr>
	<tr>
		<th class=tdright><label for="sec_level">Security Level:</label></th>
		<td class=tdleft><?php printSelect ($sloptions, array ('name' => 'sec_level'), 'noAuthNoPriv'); ?></td>
	</tr>
	<tr>
		<th class=tdright><label for="auth_protocol_1">Auth Type:</label></th>
		<td class=tdleft>
		<input id=auth_protocol_1 name=auth_protocol type=radio value=md5 />
		<label for=auth_protocol_1>MD5</label>
		<input id=auth_protocol_2 name=auth_protocol type=radio value=sha />
		<label for=auth_protocol_2>SHA</label>
		</td>
	</tr>
	<tr>
		<th class=tdright><label for=auth_passphrase>Auth Key:</label></th>
		<td class=tdleft><input type=text id=auth_passphrase name=auth_passphrase></td>
	</tr>
	<tr>
		<th class=tdright><label for=priv_protocol_1>Priv Type:</label></th>
		<td class=tdleft>
		<input id=priv_protocol_1 name=priv_protocol type=radio value=DES />
		<label for=priv_protocol_1>DES</label>
		<input id=priv_protocol_2 name=priv_protocol type=radio value=AES />
		<label for=priv_protocol_2>AES</label>
		</td>
	</tr>
	<tr>
		<th class=tdright><label for=priv_passphrase>Priv Key</label></th>
		<td class=tdleft><input type=text id=priv_passphrase name=priv_passphrase></td>
	</tr>
	<tr><td colspan=2><input type=submit value="Try now"></td></tr>
	</table>
<?php
	echo '</form>';
	finishPortlet();
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
	loadIPAddrList ($range);
	$can_import = permitted (NULL, NULL, 'importPTRData');
	echo "<center><h1>${range['ip']}/${range['mask']}</h1><h2>${range['name']}</h2></center>\n";

	echo "<table class=objview border=0 width='100%'><tr><td class=pcleft>";
	startPortlet ('current records');
	$startip = ip4_bin2int ($range['ip_bin']);
	$endip = ip4_bin2int (ip_last ($range));
	$numpages = 0;
	if ($endip - $startip > $maxperpage)
	{
		$numpages = ($endip - $startip) / $maxperpage;
		$startip = $startip + $page * $maxperpage;
		$endip = $startip + $maxperpage - 1;
	}
	echo "<center>";
	if ($numpages)
		echo '<h3>' . ip4_format (ip4_int2bin ($startip)) . ' ~ ' . ip4_format (ip4_int2bin ($endip)) . '</h3>';
	for ($i=0; $i<$numpages; $i++)
		if ($i == $page)
			echo "<b>$i</b> ";
		else
			echo "<a href='".makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'id'=>$id, 'pg'=>$i))."'>$i</a> ";
	echo "</center>";

	// FIXME: address counter could be calculated incorrectly in some cases
	if ($can_import)
	{
		printOpFormIntro ('importPTRData', array ('addrcount' => ($endip - $startip + 1)));
		$idx = 1;
		$box_counter = 1;
	}

	echo "<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center'>\n";
	echo '<tr><th>address</th><th>current name</th><th>DNS data</th>';
	if ($can_import)
		echo '<th>import</th>';
	echo '</tr>';
	$cnt_match = $cnt_mismatch = $cnt_missing = 0;
	for ($ip = $startip; $ip <= $endip; $ip++)
	{
		// Find the (optional) DB name and the (optional) DNS record, then
		// compare values and produce a table row depending on the result.
		$ip_bin = ip4_int2bin ($ip);
		$addr = isset ($range['addrlist'][$ip_bin]) ? $range['addrlist'][$ip_bin] : array ('name' => '', 'reserved' => 'no');
		$straddr = ip4_format ($ip_bin);
		$ptrname = gethostbyaddr ($straddr);
		if ($ptrname == $straddr)
			$ptrname = '';
		if ($can_import)
		{
			echo "<input type=hidden name=addr_${idx} value=${straddr}>\n";
			echo "<input type=hidden name=descr_${idx} value=${ptrname}>\n";
			echo "<input type=hidden name=rsvd_${idx} value=${addr['reserved']}>\n";
		}
		echo '<tr';
		$print_cbox = FALSE;
		// Ignore network and broadcast addresses
		if (($ip == $startip && $addr['name'] == 'network') || ($ip == $endip && $addr['name'] == 'broadcast'))
			echo ' class=trbusy';
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
		if (isset ($range['addrlist'][$ip_bin]['class']) and strlen ($range['addrlist'][$ip_bin]['class']))
			echo ' ' . $range['addrlist'][$ip_bin]['class'];
		echo "'>" . mkA ($straddr, 'ipaddress', $straddr) . '</td>';
		echo "<td class=tdleft>${addr['name']}</td><td class=tdleft>${ptrname}</td>";
		if ($can_import)
		{
			echo '<td>';
			if ($print_cbox)
				echo "<input type=checkbox name=import_${idx} id=atom_1_" . $box_counter++ . "_1>";
			else
				echo '&nbsp;';
			echo '</td>';
			$idx++;
		}
		echo "</tr>\n";
	}
	if ($can_import && $box_counter > 1)
	{
		echo '<tr><td colspan=3 align=center><input type=submit value="Import selected records"></td><td>';
		addJS ('js/racktables.js');
		echo --$box_counter ? "<a href='javascript:;' onclick=\"toggleColumnOfAtoms(1, 1, ${box_counter})\">(toggle selection)</a>" : '&nbsp;';
		echo '</td></tr>';
	}
	echo "</table>";
	if ($can_import)
		echo '</form>';
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
	$ptlist = getPortOIFOptions();
	echo "<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center'>\n";
	echo "<caption>The following ports can be quickly added:</caption>";
	echo "<tr><th>type</th><th>name</th></tr>";
	foreach (getAutoPorts ($info) as $autoport)
		echo "<tr><td>" . $ptlist[$autoport['type']] . "</td><td>${autoport['name']}</td></tr>";
	printOpFormIntro ('generate');
	echo "<tr><td colspan=2 align=center>";
	echo "<input type=submit value='Generate'>";
	echo "</td></tr>";
	echo "</table></form>";
}

# Return a list of items representing tags with checkboxes.
function buildTagCheckboxRows ($inputname, $preselect, $neg_preselect, $taginfo, $refcnt_realm = '', $level = 0)
{
	static $is_first_time = TRUE;
	$inverted = tagOnChain ($taginfo, $neg_preselect);
	$selected = tagOnChain ($taginfo, $preselect);
	$ret = array
	(
		'tr_class' => ($level == 0 && $taginfo['id'] > 0 && ! $is_first_time) ? 'separator' : '',
		'td_class' => 'tagbox',
		'level' => $level,
		# calculate HTML classnames for separators feature
		'input_class' => $level ? 'tag-cb' : 'tag-cb root',
		'input_value' => $taginfo['id'],
		'text_tagname' => $taginfo['tag'],
	);
	$is_first_time = FALSE;
	$prepared_inputname = $inputname;
	if ($inverted)
	{
		$ret['td_class'] .= ' inverted';
		$prepared_inputname = preg_replace ('/^cf/', 'nf', $prepared_inputname);
	}
	$ret['input_name'] = $prepared_inputname;
	if ($selected)
	{
		$ret['td_class'] .= $inverted ? ' selected-inverted' : ' selected';
		$ret['input_extraattrs'] = 'checked';
	}
	if (array_key_exists ('is_assignable', $taginfo) and $taginfo['is_assignable'] == 'no')
	{
		$ret['input_extraattrs'] = 'disabled';
		$ret['tr_class'] .= (array_key_exists ('kidc', $taginfo) and $taginfo['kidc'] == 0) ? ' trwarning' : ' trnull';
	}
	if (strlen ($refcnt_realm) and isset ($taginfo['refcnt'][$refcnt_realm]))
		$ret['text_refcnt'] = $taginfo['refcnt'][$refcnt_realm];
	$ret = array ($ret);
	if (array_key_exists ('kids', $taginfo))
		foreach ($taginfo['kids'] as $kid)
			$ret = array_merge ($ret, call_user_func (__FUNCTION__, $inputname, $preselect, $neg_preselect, $kid, $refcnt_realm, $level + 1));
	return $ret;
}

# generate HTML from the data produced by the above function
function printTagCheckboxTable ($input_name, $preselect, $neg_preselect, $taglist, $realm = '')
{
	foreach ($taglist as $taginfo)
		foreach (buildTagCheckboxRows ($input_name, $preselect, $neg_preselect, $taginfo, $realm) as $row)
		{
			$tag_class = isset ($taginfo['id']) && isset ($taginfo['refcnt']) ? getTagClassName ($row['input_value']) : '';
			echo "<tr class='${row['tr_class']}'><td colspan=2 class='${row['td_class']}' style='padding-left: " . ($row['level'] * 16) . "px;'>";
			echo "<label><input type=checkbox class='${row['input_class']}' name='${row['input_name']}[]' value='${row['input_value']}'";
			if (array_key_exists ('input_extraattrs', $row))
				echo ' ' . $row['input_extraattrs'];
			echo '> <span class="' . $tag_class . '">' . $row['text_tagname'] . '</span>';
			if (array_key_exists ('text_refcnt', $row))
				echo " <i>(${row['text_refcnt']})</i>";
			echo '</label></td></tr>';
		}
}

function renderEntityTagsPortlet ($title, $tags, $preselect, $realm)
{
	startPortlet ($title);
	echo  '<a class="toggleTreeMode" style="display:none" href="#"></a>';
	echo '<table border=0 cellspacing=0 cellpadding=3 align=center class="tagtree">';
	printOpFormIntro ('saveTags');
	printTagCheckboxTable ('taglist', $preselect, array(), $tags, $realm);
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
	global $taglist, $target_given_tags;
	echo '<table border=0 width="100%"><tr>';

	if (count ($taglist) > getConfigVar ('TAGS_QUICKLIST_THRESHOLD'))
	{
		$minilist = getTagChart (getConfigVar ('TAGS_QUICKLIST_SIZE'), etypeByPageno(), $target_given_tags);
		// It could happen, that none of existing tags have been used in the current realm.
		if (count ($minilist))
		{
			$js_code = "tag_cb.setTagShortList ({";
			$is_first = TRUE;
			foreach ($minilist as $tag)
			{
				if (! $is_first)
					$js_code .= ",";
				$is_first = FALSE;
				$js_code .= "\n\t${tag['id']} : 1";
			}
			$js_code .= "\n});\n$(document).ready(tag_cb.compactTreeMode);";
			addJS ('js/tag-cb.js');
			addJS ($js_code, TRUE);
		}
	}

	// do not do anything about empty tree, trigger function ought to work this out
	echo '<td class=pcright>';
	renderEntityTagsPortlet ('Tag tree', getTagTree(), $target_given_tags, etypeByPageno());
	echo '</td>';

	echo '</tr></table>';
}

// This one is going to replace the tag filter.
function renderCellFilterPortlet ($preselect, $realm, $cell_list = array(), $bypass_params = array())
{
	addJS ('js/tag-cb.js');
	addJS ('tag_cb.enableNegation()', TRUE);

	global $pageno, $tabno, $taglist;
	$filterc =
	(
		count ($preselect['tagidlist']) +
		count ($preselect['pnamelist']) +
		(mb_strlen ($preselect['extratext']) ? 1 : 0)
	);
	$title = $filterc ? "Tag filters (${filterc})" : 'Tag filters';
	startPortlet ($title);
	echo "<form method=get>\n";
	echo '<table border=0 align=center cellspacing=0 class="tagtree">';
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
			$class = 'tagbox' . ($andor == $boolop ? ' selected' : '');
			$checked = $andor == $boolop ? ' checked' : '';
			echo "<td class='${class}'><label><input type=radio name=andor value=${boolop}";
			echo $checked . ">${boolop}</input></label></td>";
		}
	}

	$negated_chain = array();
	foreach ($preselect['negatedlist'] as $key)
		$negated_chain[] = array ('id' => $key);
	// tags block
	if (getConfigVar ('FILTER_SUGGEST_TAGS') == 'yes' or count ($preselect['tagidlist']))
	{
		if (count ($preselect['tagidlist']))
			$enable_reset = TRUE;
		echo $hr;
		$hr = $ruler;

		// Show a tree of tags, pre-select according to currently requested list filter.
		$objectivetags = getShrinkedTagTree($cell_list, $realm, $preselect);
		if (!count ($objectivetags))
			echo "<tr><td colspan=2 class='tagbox sparenetwork'>(nothing is tagged yet)</td></tr>";
		else
		{
			$enable_apply = TRUE;
			printTagCheckboxTable ('cft', buildTagChainFromIds ($preselect['tagidlist']), $negated_chain, $objectivetags, $realm);
		}

		if (getConfigVar('SHRINK_TAG_TREE_ON_CLICK') == 'yes')
			addJS ('tag_cb.enableSubmitOnClick()', TRUE);
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
		// Repack matching predicates in a way that tagOnChain() understands.
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
			printTagCheckboxTable ('cfp', $myPreselect, $negated_chain, $myPredicates);
		}
	}
	// extra code
	$enable_textify = FALSE;
	if (getConfigVar ('FILTER_SUGGEST_EXTRA') == 'yes' or strlen ($preselect['extratext']))
	{
		$enable_textify = !empty ($preselect['text']) || !empty($preselect['extratext']);
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
		foreach ($bypass_params as $bypass_name => $bypass_value)
			echo '<input type=hidden name="' . htmlspecialchars ($bypass_name, ENT_QUOTES) . '" value="' . htmlspecialchars ($bypass_value, ENT_QUOTES) . '">' . "\n";
		// FIXME: The user will be able to "submit" the empty form even without a "submit"
		// input. To make things consistent, it is necessary to avoid pritning both <FORM>
		// and "and/or" radio-buttons, when enable_apply isn't TRUE.
		if (!$enable_apply)
			printImageHREF ('setfilter gray');
		else
			printImageHREF ('setfilter', 'set filter', TRUE);
		echo '</form>';
		if ($enable_textify)
		{
			$text = empty ($preselect['text']) || empty ($preselect['extratext'])
				? $preselect['text']
				: '(' . $preselect['text'] . ')';
			$text .= !empty ($preselect['extratext']) && !empty ($preselect['text'])
				? ' ' . $preselect['andor'] . ' '
				: '';
			$text .= empty ($preselect['text']) || empty ($preselect['extratext'])
				? $preselect['extratext']
				: '(' . $preselect['extratext'] . ')';
			$text = addslashes ($text);
			echo " <a href=\"#\" onclick=\"textifyCellFilter(this, '$text'); return false\">";
			printImageHREF ('COPY', 'Make text expression from current filter');
			echo '</a>';
			addJS (<<<END
function textifyCellFilter(target, text)
{
	var portlet = $(target).closest ('.portlet');
	portlet.find ('textarea[name="cfe"]').html (text);
	portlet.find ('input[type="checkbox"]').attr('checked', '');
	portlet.find ('input[type="radio"][value="and"]').attr('checked','true');
}
END
				, TRUE
			);
		}
		echo '</td><td class=tdright>';
		// "reset"
		if (!$enable_reset)
			printImageHREF ('resetfilter gray');
		else
		{
			echo "<form method=get>\n";
			echo "<input type=hidden name=page value=${pageno}>\n";
			echo "<input type=hidden name=tab value=${tabno}>\n";
			echo "<input type=hidden name='cft[]' value=''>\n";
			echo "<input type=hidden name='cfp[]' value=''>\n";
			echo "<input type=hidden name='nft[]' value=''>\n";
			echo "<input type=hidden name='nfp[]' value=''>\n";
			echo "<input type=hidden name='cfe' value=''>\n";
			foreach ($bypass_params as $bypass_name => $bypass_value)
				echo '<input type=hidden name="' . htmlspecialchars ($bypass_name, ENT_QUOTES) . '" value="' . htmlspecialchars ($bypass_value, ENT_QUOTES) . '">' . "\n";
			printImageHREF ('resetfilter', 'reset filter', TRUE);
			echo '</form>';
		}
		echo '</td></tr>';
	}
	echo '</table>';
	finishPortlet();
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
	printTagsPicker ();
	echo "</td></tr>";
	echo "<tr><th>Control question: the sum of ${a} and ${b}</th><td><input type=text name=sum></td></tr>";
	echo "<tr><td colspan=2 align=center><input type=submit value='Go!'></td></tr>";
	echo "</table></form>";
}

function renderFileSummary ($file)
{
	$summary = array();
	$summary['Type'] = stringForTD ($file['type']);
	$btn = isolatedPermission ('file', 'download', $file) ?
		(makeFileDownloadButton ($file['id']) . '&nbsp;') : '';
	$summary['Size'] = $btn . formatFileSize ($file['size']);
	$summary['Created'] = $file['ctime'];
	$summary['Modified'] = $file['mtime'];
	$summary['Accessed'] = $file['atime'];
	$summary['tags'] = '';
	if (strlen ($file['comment']))
		$summary['Comment'] = '<div class="dashed commentblock">' . string_insert_hrefs (htmlspecialchars ($file['comment'])) . '</div>';
	renderEntitySummary ($file, 'summary', $summary);
}

function renderFileLinks ($links)
{
	startPortlet ('Links (' . count ($links) . ')');
	echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	foreach ($links as $link)
	{
		$cell = spotEntity ($link['entity_type'], $link['entity_id']);
		echo '<tr><td class=tdleft>';
		switch ($link['entity_type'])
		{
			case 'user':
			case 'ipv4net':
			case 'rack':
			case 'ipvs':
			case 'ipv4vs':
			case 'ipv4rspool':
			case 'object':
				renderCell ($cell);
				break;
			default:
				echo formatRealmName ($link['entity_type']) . ': ' . mkCellA ($cell);
				break;
		}
		echo '</td></tr>';
	}
	echo "</table><br>\n";
	finishPortlet();
}

function renderFilePreview ($pcode)
{
	startPortlet ('preview');
	echo $pcode;
	finishPortlet();
}

// File-related functions
function renderFile ($file_id)
{
	$file = spotEntity ('file', $file_id);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>" . htmlspecialchars ($file['name']) . "</h1></td></tr>\n";
	echo "<tr><td class=pcleft>";

	callHook ('renderFileSummary', $file);

	$links = getFileLinks ($file_id);
	if (count ($links))
		callHook ('renderFileLinks', $links);

	echo "</td>";

	if (isolatedPermission ('file', 'download', $file) and '' != ($pcode = getFilePreviewCode ($file)))
	{
		echo "<td class=pcright>";
		callHook ('renderFilePreview', $pcode);
		echo "</td>";
	}

	echo "</tr></table>\n";
}

function renderFileReuploader ()
{
	startPortlet ('Replace existing contents');
	printOpFormIntro ('replaceFile', array (), TRUE);
	echo "<input type=file size=10 name=file>&nbsp;\n";
	printImageHREF ('save', 'Save changes', TRUE);
	echo "</form>\n";
	finishPortlet();
}

function renderFileDownloader ($file_id)
{
	echo '<br><center>' . makeFileDownloadButton ($file_id, 'DOWNLOAD') . '</center>';
}

function renderFileProperties ($file_id)
{
	$file = spotEntity ('file', $file_id);
	printOpFormIntro ('updateFile');
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>MIME-type:</th><td class=tdleft><input type=text name=file_type value='";
	echo htmlspecialchars ($file['type']) . "'></td></tr>";
	echo "<tr><th class=tdright>Tags:</th><td class=tdleft>";
	printTagsPicker ();
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>Filename:</th><td class=tdleft><input type=text name=file_name value='";
	echo htmlspecialchars ($file['name']) . "'></td></tr>\n";
	echo "<tr><th class=tdright>Comment:</th><td class=tdleft><textarea name=file_comment rows=10 cols=80>\n";
	echo stringForTextarea ($file['comment']) . "</textarea></td></tr>\n";
	echo "<tr><th class=tdright>Actions:</th><td class=tdleft>";
	echo getOpLink (array ('op'=>'deleteFile', 'page'=>'files', 'tab'=>'manage', 'file_id'=>$file_id), '', 'destroy', 'Delete file', 'need-confirmation');
	echo '</td></tr>';
	echo "<tr><th class=submit colspan=2>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo '</th></tr></table></form>';
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
		echo '<tr><th class=tdright>Comment:</th><td class=tdleft><textarea name=comment rows=10 cols=80></textarea></td></tr>';
		echo '<tr><th class=tdright>Tags:</td><td class=tdleft>';
		printTagsPicker ();
		echo '</td></tr>';
		echo "<tr><th class=tdright>File:</th><td class=tdleft><input type='file' size='10' name='file'></td></td>";
		echo "<tr><td colspan=2>";
		printImageHREF ('CREATE', 'Upload file', TRUE);
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
				echo getOpLink (array('op'=>'deleteFile', 'file_id'=>$file['id']), '', 'DESTROY', 'Delete file', 'need-confirmation');
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
	$entity_type = etypeByPageno();

	startPortlet ('Upload and link new');
	echo "<table border=0 cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	echo "<tr><th>File</th><th>Comment</th><th></th></tr>\n";
	printOpFormIntro ('addFile', array (), TRUE);
	echo "<tr>";
	echo "<td class=tdleft><input type='file' size='10' name='file'></td>\n";
	echo "<td class=tdleft><textarea name=comment rows=10 cols=80></textarea></td><td>\n";
	printImageHREF ('CREATE', 'Upload file', TRUE);
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
			echo getOpLink (array('op'=>'unlinkFile', 'link_id'=>$file['link_id']), '', 'CUT', 'Unlink file');
			echo "</td></tr>\n";
		}
		echo "</table><br>\n";
		finishPortlet();
	}
}


// Iterate over what findRouters() returned and output some text suitable for a TD element.
function printRoutersTD ($rlist, $as_cell = 'yes')
{
	$rtrclass = 'tdleft';
	foreach ($rlist as $rtr)
	{
		$tmp = getIPAddress ($rtr['ip_bin']);
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
			renderRouterCell ($rtr['ip_bin'], $rtr['iface'], $rinfo);
		else
			echo $pfx . '<a href="' . makeHref (array ('page' => 'object', 'object_id' => $rtr['id'], 'tab' => 'default', 'hl_ip' => ip_format ($rtr['ip_bin']))) . '">' . $rinfo['dname'] . '</a>';
		$pfx = "<br>\n";
	}
	echo '</td>';
}

// Same as for routers, but produce two TD cells to lay the content out better.
function printIPNetInfoTDs ($netinfo, $decor = array())
{
	$ip_ver = strlen ($netinfo['ip_bin']) == 16 ? 6 : 4;
	$formatted = $netinfo['ip'] . "/" . $netinfo['mask'];
	if ($netinfo['symbol'] == 'spacer')
	{
		$decor['indent']++;
		$netinfo['symbol'] = '';
	}
	echo '<td class="tdleft';
	if (array_key_exists ('tdclass', $decor))
		echo ' ' . $decor['tdclass'];
	echo '" style="padding-left: ' . ($decor['indent'] * 16) . 'px;">';
	if (strlen ($netinfo['symbol']))
	{
		if (array_key_exists ('symbolurl', $decor))
			echo "<a href='${decor['symbolurl']}'>";
		printImageHREF ($netinfo['symbol']);
		if (array_key_exists ('symbolurl', $decor))
			echo '</a>';
	}
	if (isset ($netinfo['id']))
		echo "<a name='net-${netinfo['id']}' href='index.php?page=ipv${ip_ver}net&id=${netinfo['id']}'>";
	echo $formatted;
	if (isset ($netinfo['id']))
		echo '</a>';
	if (getConfigVar ('IPV4_TREE_SHOW_VLAN') == 'yes' and ! empty ($netinfo['8021q']))
	{
		echo '<br>';
		renderNetVLAN ($netinfo);
	}
	echo '</td><td class="tdleft';
	if (array_key_exists ('tdclass', $decor))
		echo ' ' . $decor['tdclass'];
	echo '">';
	if (!isset ($netinfo['id']))
	{
		printImageHREF ('dragons', 'Here be dragons.');
		if ($decor['knight'])
		{
			echo '<a href="' . makeHref (array
			(
				'page' => "ipv${ip_ver}space",
				'tab' => 'newrange',
				'set-prefix' => $formatted,
			)) . '">';
			printImageHREF ('knight', 'create network here');
			echo '</a>';
		}
	}
	else
	{
		echo stringForTD ($netinfo['name']);
		if (count ($netinfo['etags']))
			echo '<br><small>' . serializeTags ($netinfo['etags'], "index.php?page=ipv${ip_ver}space&tab=default&") . '</small>';
	}
	echo "</td>";
}

function renderCell ($cell)
{
	$override = callHook ('getRenderedCell', $cell);
	if (isset ($override))
	{
		echo $override;
		return;
	}
	switch ($cell['realm'])
	{
	case 'user':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		printImageHREF ('USER');
		echo '</td><td>' . mkA (stringForTD ($cell['user_name']), 'user', $cell['user_id']) . '</td></tr>';
		if (strlen ($cell['user_realname']))
			echo "<tr><td><strong>" . stringForTD ($cell['user_realname']) . "</strong></td></tr>";
		else
			echo "<tr><td class=sparenetwork>no name</td></tr>";
		echo '<td>';
		if (!isset ($cell['etags']))
			$cell['etags'] = getExplicitTagsOnly (loadEntityTags ('user', $cell['user_id']));
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
		echo mkA ('<strong>' . stringForTD ($cell['name']) . '</strong>', 'file', $cell['id']);
		echo "</td><td rowspan=3 valign=top>";
		if (isset ($cell['links']) and count ($cell['links']))
			printf ("<small>%s</small>", serializeFileLinks ($cell['links']));
		echo "</td></tr><tr><td>";
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo '</td></tr><tr><td>';
		if (isolatedPermission ('file', 'download', $cell))
			echo makeFileDownloadButton ($cell['id']) . '&nbsp;';
		echo formatFileSize ($cell['size']);
		echo "</td></tr></table>";
		break;
	case 'ipv4vs':
	case 'ipvs':
	case 'ipv4rspool':
		renderSLBEntityCell ($cell);
		break;
	case 'ipv4net':
	case 'ipv6net':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		printImageHREF ('NET');
		echo '</td><td>' . mkA ("${cell['ip']}/${cell['mask']}", $cell['realm'], $cell['id']);
		echo getRenderedIPNetCapacity ($cell);
		echo '</td></tr>';

		echo "<tr><td>";
		if (strlen ($cell['name']))
			echo "<strong>" . stringForTD ($cell['name']) . "</strong>";
		else
			echo "<span class=sparenetwork>no name</span>";
		// render VLAN
		renderNetVLAN ($cell);
		echo "</td></tr>";
		echo '<tr><td>';
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	case 'rack':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		echo getRackThumbLink ($cell);
		echo "</td><td>";
		echo mkA ('<strong>' . stringForTD ($cell['name']) . '</strong>', 'rack', $cell['id']);
		echo "</td></tr><tr><td>";
		echo stringForTD ($cell['comment']);
		echo "</td></tr><tr><td>";
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	case 'location':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		printImageHREF ('LOCATION');
		echo "</td><td>";
		echo mkA ('<strong>' . stringForTD ($cell['name']) . '</strong>', 'location', $cell['id']);
		echo "</td></tr><tr><td>";
		echo stringForTD ($cell['comment']);
		echo "</td></tr><tr><td>";
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	case 'object':
		echo "<table class='slbcell vscell'><tr><td rowspan=2 width='5%'>";
		printImageHREF ('OBJECT');
		echo '</td><td>';
		echo mkA ('<strong>' . stringForLabel ($cell['dname']) . '</strong>', 'object', $cell['id']);
		echo "<br /><small>" . stringForLabel (decodeObjectType ($cell['objtype_id'])) . "</small></td></tr>";
		echo '<tr><td>', count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	default:
		throw new InvalidArgException ('realm', $cell['realm']);
	}
}

function renderRouterCell ($ip_bin, $ifname, $cell)
{
	$dottedquad = ip_format ($ip_bin);
	echo "<table class=slbcell><tr><td rowspan=3>${dottedquad}";
	if (strlen ($ifname))
		echo '@' . $ifname;
	echo "</td>";
	echo "<td><a href='index.php?page=object&object_id=${cell['id']}&hl_ip=${dottedquad}'><strong>${cell['dname']}</strong></a></td>";
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
				$ret .= "<a href='?module=download&file_id=${file['id']}&asattach=no'>";
			$ret .= "<img width=${width} height=${height} src='?module=image&img=preview&file_id=${file['id']}'>";
			if ($resampled)
				$ret .= '</a><br>(click to zoom)';
			break;
		case 'text/plain':
			if ($file['size'] < getConfigVar ('PREVIEW_TEXT_MAXCHARS'))
			{
				$file = getFile ($file['id']);
				$ret .= '<textarea readonly rows=' . getConfigVar ('PREVIEW_TEXT_ROWS');
				$ret .= ' cols=' . getConfigVar ('PREVIEW_TEXT_COLS') . '>';
				$ret .= stringForTextarea ($file['contents']);
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
	addJS ('js/codepress/codepress.js');
	echo "<tr><td><textarea rows=45 cols=180 id=file_text name=file_text class='codepress " . $syntax . "'>\n";
	echo stringForTextarea ($fullInfo['contents']) . '</textarea></td></tr>';
	echo "<tr><td class=submit><input type=submit value='Save' onclick='$(file_text).toggleEditor();'>";
	echo "</td></tr>\n</table></form>\n";
}

function showPathAndSearch ($pageno, $tabno)
{
	// This function returns array of page numbers leading to the target page
	// plus page number of target page itself. The first element is the target
	// page number and the last element is the index page number.
	function getPath ($targetno)
	{
		$self = __FUNCTION__;
		global $page;
		$path = array();
		$page_name = preg_replace ('/:.*/', '', $targetno);
		// Recursion breaks at first parentless page.
		if ($page_name == 'ipaddress')
		{
			// case ipaddress is a universal v4/v6 page, it has two parents and requires special handling
			$ip_bin = ip_parse ($_REQUEST['ip']);
			$parent = (strlen ($ip_bin) == 16 ? 'ipv6net' : 'ipv4net');
			$path = $self ($parent);
			$path[] = $targetno;
		}
		elseif (!isset ($page[$page_name]['parent']))
			$path = array ($targetno);
		else
		{
			$path = $self ($page[$page_name]['parent']);
			$path[] = $targetno;
		}
		return $path;
	}
	global $page, $tab;
	// Path.
	$path = getPath ($pageno);
	$items = array();
	foreach (array_reverse ($path) as $no)
	{
		if (preg_match ('/(.*):(.*)/', $no, $m) && isset ($tab[$m[1]][$m[2]]))
			$title = array
			(
				'name' => $tab[$m[1]][$m[2]],
				'params' => array('page' => $m[1], 'tab' => $m[2]),
			);
		elseif (isset ($page[$no]['title']))
			$title = array
			(
				'name' => $page[$no]['title'],
				'params' => array()
			);
		else
			$title = callHook ('dynamic_title_decoder', $no);
		$item = "<a href='index.php?";
		if (! isset ($title['params']['page']))
			$title['params']['page'] = $no;
		if (! isset ($title['params']['tab']))
			$title['params']['tab'] = 'default';
		$is_first = TRUE;
		$anchor_tail = '';
		foreach ($title['params'] as $param_name => $param_value)
		{
			if ($param_name == '#')
			{
				$anchor_tail = '#' . $param_value;
				continue;
			}
			$item .= ($is_first ? '' : '&') . "${param_name}=${param_value}";
			$is_first = FALSE;
		}
		$item .= $anchor_tail;
		$item .= "'>" . $title['name'] . "</a>";
		$items[] = $item;

		// insert location bread crumbs
		switch ($no)
		{
		case 'object':
			$object = spotEntity ('object', $title['params']['object_id']);
			if ($object['rack_id'])
			{
				$rack = spotEntity ('rack', $object['rack_id']);
				$items[] = mkA ($rack['name'], 'rack', $rack['id']);
				$items[] = mkA ($rack['row_name'], 'row', $rack['row_id']);
				if ($rack['location_id'])
				{
					$trail = getLocationTrail ($rack['location_id']);
					if (! empty ($trail))
						$items[] = $trail;
				}
			}
			break;
		case 'row':
			$trail = getLocationTrail ($title['params']['location_id']);
			if (! empty ($trail))
				$items[] = $trail;
			break;
		case 'location':
			// overwrite the bread crumb for current location with whole path
			$items[count ($items)-1] = getLocationTrail ($title['params']['location_id']);
			break;
		}
	}
	// Search form.
	echo "<div class='searchbox' style='float:right'>";
	echo "<form name=search method=get>";
	echo '<input type=hidden name=page value=search>';
	echo "<input type=hidden name=last_page value=$pageno>";
	echo "<input type=hidden name=last_tab value=$tabno>";
	// This input will be the first, if we don't add ports or addresses.
	echo "<label>Search:<input type=text name=q size=20 value='".(isset ($_REQUEST['q']) ? htmlspecialchars ($_REQUEST['q'], ENT_QUOTES) : '')."'></label></form></div>";

	// Path (breadcrumbs)
	echo implode(' : ', array_reverse ($items));
}

function getTitle ($pageno)
{
	global $page;
	if (isset ($page[$pageno]['title']))
		return $page[$pageno]['title'];
	$tmp = callHook ('dynamic_title_decoder', $pageno);
	return $tmp['name'];
}

function showTabs ($pageno, $tabno)
{
	global $tab, $page, $trigger;
	if (!isset ($tab[$pageno]['default']))
		return;
	echo "<div class=greynavbar><ul id=foldertab style='margin-bottom: 0px; padding-top: 10px;'>";
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
			$tabclass = 'current'; // override any class for an active selection
		echo "<li><a class=${tabclass}";
		echo " href='" . makeHref (makePageParams (array ('tab' => $tabidx)));
		echo "'>${tabtitle}</a></li>\n";
	}
	echo "</ul></div>";
}

function dynamic_title_decoder ($path_position)
{
	try
	{
		return dynamic_title_decoder_throwing ($path_position);
	}
	catch (RackTablesError $e)
	{
		return array
		(
			'name' => __FUNCTION__ . '() failure',
			'params' => array()
		);
	}
}

// Arg is path page number, which can be different from the primary page number,
// for example title for 'ipv4net' can be requested to build navigation path for
// both IPv4 network and IPv4 address. Another such page number is 'row', which
// fires for both row and its racks. Use pageno for decision in such cases.
function dynamic_title_decoder_throwing ($path_position)
{
	global $sic, $page_by_realm;
	global $pageno;
	static $net_id;
	switch ($path_position)
	{
	case 'index':
		return array
		(
			'name' => '/' . getConfigVar ('enterprise'),
			'params' => array()
		);
	case 'chapter':
		$chapter_no = assertUIntArg ('chapter_no');
		$chapters = getChapterList();
		$chapter_name = isset ($chapters[$chapter_no]) ? $chapters[$chapter_no]['name'] : 'N/A';
		return array
		(
			'name' => "Chapter '${chapter_name}'",
			'params' => array ('chapter_no' => $chapter_no)
		);
	case 'user':
		$userinfo = spotEntity ('user', assertUIntArg ('user_id'));
		return array
		(
			'name' => "Local user '" . $userinfo['user_name'] . "'",
			'params' => array ('user_id' => $userinfo['user_id'])
		);
	case 'ipv4rspool':
		$pool_info = spotEntity ('ipv4rspool', assertUIntArg ('pool_id'));
		return array
		(
			'name' => !strlen ($pool_info['name']) ? 'ANONYMOUS' : $pool_info['name'],
			'params' => array ('pool_id' => $pool_info['id'])
		);
	case 'ipv4vs':
		$vs_info = spotEntity ('ipv4vs', assertUIntArg ('vs_id'));
		return array
		(
			'name' => $vs_info['dname'],
			'params' => array ('vs_id' => $vs_info['id'])
		);
	case 'ipvs':
		$vs_info = spotEntity ('ipvs', assertUIntArg ('vs_id'));
		return array
		(
			'name' => $vs_info['name'],
			'params' => array ('vs_id' => $vs_info['id'])
		);
	case 'object':
		$object = spotEntity ('object', assertUIntArg ('object_id'));
		return array
		(
			'name' => $object['dname'],
			'params' => array ('object_id' => $object['id'])
		);
	case 'location':
		$location = spotEntity ('location', assertUIntArg ('location_id'));
		return array
		(
			'name' => $location['name'],
			'params' => array ('location_id' => $location['id'])
		);
	case 'row':
		switch ($pageno)
		{
		case 'rack':
			$rack = spotEntity ('rack', assertUIntArg ('rack_id'));
			return array
			(
				'name' => $rack['row_name'],
				'params' => array ('row_id' => $rack['row_id'], 'location_id' => $rack['location_id'])
			);
		case 'row':
			$row_info = getRowInfo (assertUIntArg ('row_id'));
			return array
			(
				'name' => $row_info['name'],
				'params' => array ('row_id' => $row_info['id'], 'location_id' => $row_info['location_id'])
			);
		default:
			break;
		}
	case 'rack':
		$rack_info = spotEntity ('rack', assertUIntArg ('rack_id'));
		return array
		(
			'name' => $rack_info['name'],
			'params' => array ('rack_id' => $rack_info['id'])
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
		$file = spotEntity ('file', assertUIntArg ('file_id'));
		return array
		(
			'name' => niftyString ($file['name'], 30, FALSE),
			'params' => array ('file_id' => $_REQUEST['file_id'])
		);
	case 'ipaddress':
		$address = getIPAddress (ip_parse ($_REQUEST['ip']));
		return array
		(
			'name' => niftyString ($address['ip'] . ($address['name'] != '' ? ' (' . $address['name'] . ')' : ''), 50, FALSE),
			'params' => array ('ip' => $address['ip'])
		);
	case 'ipv4net':
	case 'ipv6net':
		switch ($pageno)
		{
			case 'ipaddress':
				$net = spotNetworkByIP (ip_parse ($_REQUEST['ip']));
				$ret = array
				(
					'name' => $net['ip'] . '/' . $net['mask'],
					'params' => array
					(
						'id' => $net['id'],
						'page' => $net['realm'], // 'ipv4net', 'ipv6net'
						'hl_ip' => $_REQUEST['ip'],
					)
				);
				return ($ret);
			default:
				$net = spotEntity ($path_position, assertUIntArg ('id'));
				return array
				(
					'name' => $net['ip'] . '/' . $net['mask'],
					'params' => array ('id' => $net['id'])
				);
		}
		break;
	case 'ipv4space':
	case 'ipv6space':
		switch ($pageno)
		{
			case 'ipaddress':
				$net_id = getIPAddressNetworkId (ip_parse ($_REQUEST['ip']));
				break;
			case 'ipv4net':
			case 'ipv6net':
				$net_id = $_REQUEST['id'];
				break;
			default:
				$net_id = NULL;
		}
		$params = array();
		if (isset ($net_id))
			$params = array ('eid' => $net_id, 'hl_net' => 1, 'clear-cf' => '');
		unset ($net_id);
		$ip_ver = preg_replace ('/[^\d]*/', '', $path_position);
		return array
		(
			'name' => "IPv$ip_ver space",
			'params' => $params,
		);
	case 'vlandomain':
		switch ($pageno)
		{
		case 'vlandomain':
			$vdom_id = $_REQUEST['vdom_id'];
			break;
		case 'vlan':
			list ($vdom_id, $dummy) = decodeVLANCK ($_REQUEST['vlan_ck']);
			break;
		default:
			break;
		}
		$vdlist = getVLANDomainOptions();
		if (!array_key_exists ($vdom_id, $vdlist))
			throw new EntityNotFoundException ('VLAN domain', $vdom_id);
		return array
		(
			'name' => "domain '" . niftyString ($vdlist[$vdom_id], 40, FALSE) . "'",
			'params' => array ('vdom_id' => $vdom_id)
		);
	case 'vlan':
		return array
		(
			'name' => formatVLANAsPlainText (getVlanRow ($sic['vlan_ck'])),
			'params' => array ('vlan_ck' => $sic['vlan_ck'])
		);
	case 'vst':
		$vst = spotEntity ('vst', $sic['vst_id']);
		return array
		(
			'name' => "template '" . niftyString ($vst['description'], 40, FALSE) . "'",
			'params' => array ('vst_id' => $sic['vst_id'])
		);
	case 'dqueue':
		global $dqtitle;
		return array
		(
			'name' => 'queue "' . $dqtitle[$sic['dqcode']] . '"',
			'params' => array ('qcode' => $sic['dqcode'])
		);
	}

	// default behaviour is throwing an exception
	throw new RackTablesError ('dynamic_title decoding error', RackTablesError::INTERNAL);
}

function renderTwoColumnCompatTableViewer ($compat, $left, $right)
{
	global $nextorder;
	$last_lkey = NULL;
	$order = 'odd';
	echo '<table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>';
	echo "<tr><th>Key</th><th class=tdleft>${left['header']}</th><th>Key</th><th class=tdleft>${right['header']}</th></tr>";
	foreach ($compat as $item)
	{
		if ($last_lkey !== $item[$left['key']])
		{
			$order = $nextorder[$order];
			$last_lkey = $item[$left['key']];
		}
		echo "<tr class=row_${order}>";
		echo "<td class=tdright>${item[$left['key']]}</td>";
		echo '<td class=tdleft>' . stringForTD ($item[$left['value']], $left['width']) . '</td>';
		echo "<td class=tdright>${item[$right['key']]}</td>";
		echo '<td class=tdleft>' . stringForTD ($item[$right['value']], $right['width']) . '</td>';
		echo '</tr>';
	}
	echo '</table>';
}

function renderTwoColumnCompatTableEditor ($compat, $left, $right)
{
	function printNewitemTR ($lkey, $loptions, $rkey, $roptions)
	{
		printOpFormIntro ('add');
		echo '<tr><th class=tdleft>';
		printImageHREF ('add', 'add pair', TRUE);
		echo '</th><th class=tdleft>';
		printSelect ($loptions, array ('name' => $lkey));
		echo '</th><th class=tdleft>';
		printSelect ($roptions, array ('name' => $rkey));
		echo '</th></tr></form>';
	}

	global $nextorder;
	$last_lkey = NULL;
	$order = 'odd';
	echo '<table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>';
	echo "<tr><th>&nbsp;</th><th class=tdleft>${left['header']}</th><th class=tdleft>${right['header']}</th></tr>";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewitemTR ($left['key'], $left['options'], $right['key'], $right['options']);
	foreach ($compat as $item)
	{
		if ($last_lkey !== $item[$left['key']])
		{
			$order = $nextorder[$order];
			$last_lkey = $item[$left['key']];
		}
		echo "<tr class=row_${order}>";
		echo '<td>';
		echo getOpLink (array ('op' => 'del', $left['key'] => $item[$left['key']], $right['key'] => $item[$right['key']]), '', 'delete', 'remove pair');
		echo '</td>';
		echo '<td class=tdleft>' . stringForTD ($item[$left['value']], $left['width']) . '</td>';
		echo '<td class=tdleft>' . stringForTD ($item[$right['value']], $right['width']) . '</td>';
		echo '</tr>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewitemTR ($left['key'], $left['options'], $right['key'], $right['options']);
	echo '</table>';
}

function render8021QOrderForm ($some_id)
{
	function printNewItemTR ()
	{
		$all_vswitches = getVLANSwitches();
		global $pageno;
		$hintcodes = array ('prev_vdid' => 'DEFAULT_VDOM_ID', 'prev_vstid' => 'DEFAULT_VST_ID', 'prev_objid' => NULL);
		$focus = array();
		foreach ($hintcodes as $hint_code => $option_name)
		if (array_key_exists ($hint_code, $_REQUEST))
		{
			assertUIntArg ($hint_code);
			$focus[$hint_code] = $_REQUEST[$hint_code];
		}
		elseif ($option_name != NULL)
			$focus[$hint_code] = getConfigVar ($option_name);
		else
			$focus[$hint_code] = NULL;
		printOpFormIntro ('add');
		echo '<tr>';
		if ($pageno != 'object')
		{
			echo '<td>';
			// hide any object that is already in the table
			$options = array();
			foreach (getNarrowObjectList ('VLANSWITCH_LISTSRC') as $object_id => $object_dname)
				if (!in_array ($object_id, $all_vswitches))
				{
					$ctx = getContext();
					spreadContext (spotEntity ('object', $object_id));
					$decision = permitted (NULL, NULL, 'del');
					restoreContext ($ctx);
					if ($decision)
						$options[$object_id] = $object_dname;
				}
			printSelect ($options, array ('name' => 'object_id', 'size' => getConfigVar ('MAXSELSIZE')), $focus['prev_objid']);
			echo '</td>';
		}
		if ($pageno != 'vlandomain')
			echo '<td>' . getSelect (getVLANDomainOptions(), array ('name' => 'vdom_id', 'size' => getConfigVar ('MAXSELSIZE')), $focus['prev_vdid']) . '</td>';
		if ($pageno != 'vst')
		{
			$options = array();
			foreach (listCells ('vst') as $nominee)
			{
				$ctx = getContext();
				spreadContext ($nominee);
				$decision = permitted (NULL, NULL, 'add');
				restoreContext ($ctx);
				if ($decision)
					$options[$nominee['id']] = $nominee['description'];
			}
			echo '<td>' . getSelect ($options, array ('name' => 'vst_id', 'size' => getConfigVar ('MAXSELSIZE')), $focus['prev_vstid']) . '</td>';
		}
		echo '<td>' . getImageHREF ('Attach', 'set', TRUE) . '</td></tr></form>';
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
		$vlandomain = getVLANDomain ($some_id);
		foreach ($vlandomain['switchlist'] as $vswitch)
			$minuslines[$vswitch['object_id']] = array
			(
				'vdom_id' => $some_id,
				'vst_id' => $vswitch['template_id'],
			);
		break;
	case 'vst':
		$vst = spotEntity ('vst', $some_id);
		amplifyCell ($vst);
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
	echo "<br><table border=0 cellspacing=0 cellpadding=5 align=center>";
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
	$vdomlist = getVLANDomainOptions();
	$vstlist = getVSTOptions();
	foreach ($minuslines as $item_object_id => $item)
	{
		$ctx = getContext();
		if ($pageno != 'object')
			spreadContext (spotEntity ('object', $item_object_id));
		if ($pageno != 'vst')
			spreadContext (spotEntity ('vst', $item['vst_id']));
		if (! permitted (NULL, NULL, 'del'))
			$cutblock = getImageHREF ('Cut gray', 'permission denied');
		else
		{
			$args = array
			(
				'op' => 'del',
				'object_id' => $item_object_id,
				# Extra args below are only necessary for redirect and permission
				# check to work, actual deletion uses object_id only.
				'vdom_id' => $item['vdom_id'],
				'vst_id' => $item['vst_id'],
			);
			$cutblock = getOpLink ($args, '', 'Cut', 'unbind');
		}
		restoreContext ($ctx);
		echo '<tr>';
		if ($pageno != 'object')
		{
			$object = spotEntity ('object', $item_object_id);
			echo '<td>' . mkA ($object['dname'], 'object', $object['id']) . '</td>';
		}
		if ($pageno != 'vlandomain')
			echo '<td>' . mkA (stringForTD ($vdomlist[$item['vdom_id']], 64), 'vlandomain', $item['vdom_id']) . '</td>';
		if ($pageno != 'vst')
			echo '<td>' . mkA ($vstlist[$item['vst_id']], 'vst', $item['vst_id']) . '</td>';
		echo "<td>${cutblock}</td></tr>";
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
	global $dqtitle;
	echo '<table border=0 class=objectview cellspacing=0 cellpadding=0>';
	echo '<tr valign=top><td class=pcleft width="40%">';
	if (!count ($vdlist = getVLANDomainStats()))
		startPortlet ('no VLAN domains');
	else
	{
		startPortlet ('VLAN domains (' . count ($vdlist) . ')');
		echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
		echo '<tr><th>description</th><th>VLANs</th><th>switches</th><th>';
		echo getImageHREF ('net') . '</th><th>ports</th></tr>';
		$stats = array();
		$columns = array ('vlanc', 'switchc', 'ipv4netc', 'portc');
		foreach ($columns as $cname)
			$stats[$cname] = 0;
		foreach ($vdlist as $vdom_id => $dominfo)
		{
			foreach ($columns as $cname)
				$stats[$cname] += $dominfo[$cname];
			echo '<tr align=left><td>' . mkA (stringForTD ($dominfo['description']), 'vlandomain', $vdom_id) . '</td>';
			foreach ($columns as $cname)
				echo '<td class=tdright>' . $dominfo[$cname] . '</td>';
			echo '</tr>';
		}
		if (count ($vdlist) > 1)
		{
			echo '<tr align=left><td>total:</td>';
			foreach ($columns as $cname)
				echo '<td class=tdright>' . $stats[$cname] . '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
	finishPortlet();

	echo '</td><td class=pcleft width="40%">';

	if (!count ($vstlist = listCells ('vst')))
		startPortlet ('no switch templates');
	else
	{
		startPortlet ('switch templates (' . count ($vstlist) . ')');
		echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
		echo '<tr><th>description</th><th>rules</th><th>switches</th></tr>';
		foreach ($vstlist as $vst_id => $vst_info)
		{
			echo '<tr align=left valign=top><td>';
			echo mkA (stringForTD ($vst_info['description']), 'vst', $vst_id);
			if (count ($vst_info['etags']))
				echo '<br><small>' . serializeTags ($vst_info['etags']) . '</small>';
			echo '</td>';
			echo "<td class=tdright>${vst_info['rulec']}</td><td class=tdright>${vst_info['switchc']}</td></tr>";
		}
		echo '</table>';
	}
	finishPortlet();

	echo '</td><td class=pcright>';

	startPortlet ('deploy queues');
	$total = 0;
	echo '<table border=0 cellspacing=0 cellpadding=3 width="100%">';
	foreach (get8021QDeployQueues() as $qcode => $qitems)
	{
		echo '<tr><th width="50%" class=tdright>' . mkA ($dqtitle[$qcode], 'dqueue', $qcode) . ':</th>';
		echo '<td class=tdleft>' . count ($qitems) . '</td></tr>';

		$total += count ($qitems);
	}
	echo '<tr><th width="50%" class=tdright>Total:</th>';
	echo '<td class=tdleft>' . $total . '</td></tr>';
	echo '</table>';
	finishPortlet();
	echo '</td></tr></table>';
}

function renderVLANDomainListEditor ()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr><td>';
		printImageHREF ('create', 'create domain', TRUE);
		echo '</td><td>';
		echo '<input type=text size=48 name=vdom_descr>';
		echo '</td>&nbsp;<td>';
		echo '</td><td>';
		printImageHREF ('create', 'create domain', TRUE);
		echo '</td></tr></form>';
	}
	$domain_list = getVLANDomainStats();
	$group_opts = array('existing groups' => array (0 => '-- no group --'));
	foreach ($domain_list as $vdom_id => $dominfo)
	{
		if ($dominfo['group_id'])
			continue;
		$key = $dominfo['subdomc'] ? 'existing groups' : 'create group';
		$group_opts[$key][$vdom_id] = $dominfo['description'];
	}

	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>&nbsp;</th><th>description</th><th>group</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach ($domain_list as $vdom_id => $dominfo)
	{
		printOpFormIntro ('upd', array ('vdom_id' => $vdom_id));
		echo '<tr><td>';
		if ($dominfo['switchc'] or $dominfo['vlanc'] > 1)
			printImageHREF ('nodestroy', 'domain used elsewhere');
		else
			echo getOpLink (array ('op' => 'del', 'vdom_id' => $vdom_id), '', 'destroy', 'delete domain');
		echo '</td><td><input name=vdom_descr type=text size=48 value="';
		echo stringForTextInputValue ($dominfo['description'], 255) . '">';
		echo '</td><td>';
		if ($dominfo['subdomc'])
			printSelect (array (0 => 'a domain group'), array ('name' => 'group_id'));
		else
			printNiftySelect ($group_opts, array ('name' => 'group_id'), intval($dominfo['group_id']));
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
	echo '<tr><td colspan=2 align=center><h1>' . stringForTD ($mydomain['description']);
	echo '</h1></td></tr>';
	echo "<tr><td class=pcleft width='50%'>";
	if (!count ($mydomain['switchlist']))
		startPortlet ('no orders');
	else
	{
		startPortlet ('orders (' . count ($mydomain['switchlist']) . ')');
		echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
		echo '<tr><th>switch</th><th>template</th><th>status</th></tr>';
		$order = 'odd';
		$vstlist = getVSTOptions();
		global $dqtitle;
		foreach ($mydomain['switchlist'] as $switchinfo)
		{
			echo "<tr class=row_${order}><td>";
			renderCell (spotEntity ('object', $switchinfo['object_id']));
			echo '</td><td class=tdleft>';
			echo $vstlist[$switchinfo['template_id']];
			echo '</td><td>';
			$qcode = detectVLANSwitchQueue (getVLANSwitchInfo ($switchinfo['object_id']));
			printImageHREF ("DQUEUE ${qcode}", $dqtitle[$qcode]);
			echo '</td></tr>';
			$order = $nextorder[$order];
		}
		echo '</table>';
	}
	finishPortlet();

	echo '</td><td class=pcright>';

	$domain_options = getVLANDomainOptions();
	$myvlans = array();
	foreach (array_merge(array ($vdom_id), getDomainGroupMembers ($vdom_id)) as $domain_id)
		foreach (getDomainVLANs ($domain_id, TRUE) as $vlan_id => $vlan_info)
		{
			$vlan_info['domain_id'] = $domain_id;
			$vlan_info['domain_descr'] = $domain_options[$domain_id];
			$myvlans[$vlan_id][$domain_id] = $vlan_info;
		}
	ksort ($myvlans, SORT_NUMERIC);

	if (!count ($myvlans))
		startPortlet ('no VLANs');
	else
	{
		startPortlet ('VLANs (' . count ($myvlans) . ')');
		$order = 'odd';
		global $vtdecoder;
		echo '<table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>';
		echo '<tr><th>VLAN ID</th><th><span title="propagation flag">P</span></th><th>';
		printImageHREF ('net', 'IPv4 networks linked');
		echo '</th><th>ports</th><th>description</th></tr>';
		foreach ($myvlans as $vlan_id => $vlan_list)
		{
			foreach ($vlan_list as $domain_id => $vlan_info)
			{
				echo "<tr class=row_${order}>";
				echo '<td class=tdright>' . (count ($vlan_list) > 1 ? stringForLabel ($domain_options[$domain_id]) . ' ' : '') .
					formatVLANAsShortLink ($vlan_info) . '</td>';
				echo '<td>' . $vtdecoder[$vlan_info['vlan_type']] . '</td>';
				echo '<td class=tdright>' . ($vlan_info['netc'] ? $vlan_info['netc'] : '&nbsp;') . '</td>';
				echo '<td class=tdright>' . ($vlan_info['portc'] ? $vlan_info['portc'] : '&nbsp;') . '</td>';
				echo '<td class=tdleft>' . stringForLabel ($vlan_info['vlan_descr']) . '</td></tr>';
			}
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
		printImageHREF ('create', 'add VLAN', TRUE);
		echo '</td><td>';
		echo '<input type=text name=vlan_id size=4>';
		echo '</td><td>';
		printSelect ($vtoptions, array ('name' => 'vlan_type'), 'ondemand');
		echo '</td><td>';
		echo '<input type=text size=48 name=vlan_descr>';
		echo '</td><td>';
		printImageHREF ('create', 'add VLAN', TRUE);
		echo '</td></tr></form>';
	}
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>&nbsp;</th><th>ID</th><th>propagation</th><th>description</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	global $vtoptions;
	foreach (getDomainVLANs ($vdom_id, TRUE) as $vlan_id => $vlan_info)
	{
		printOpFormIntro ('upd', array ('vlan_id' => $vlan_id));
		echo '<tr><td>';
		if ($vlan_info['portc'] or $vlan_id == VLAN_DFL_ID)
			printImageHREF ('nodestroy', $vlan_info['portc'] . ' ports configured');
		else
			echo getOpLink (array ('op' => 'del', 'vlan_id' => $vlan_id), '', 'destroy', 'delete VLAN');
		echo '</td><td class=tdright><tt>' . $vlan_id . '</tt></td><td>';
		printSelect ($vtoptions, array ('name' => 'vlan_type'), $vlan_info['vlan_type']);
		echo '</td><td>';
		echo '<input name=vlan_descr type=text size=48 value="' . stringForTextInputValue ($vlan_info['vlan_descr'], 255) . '">';
		echo '</td><td>';
		printImageHREF ('save', 'update description', TRUE);
		echo '</td></tr></form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo '</table>';
}

function get8021QPortTrClass ($port, $domain_vlans, $desired_mode = NULL)
{
	if (isset ($desired_mode) && $desired_mode != $port['mode'])
		return 'trwarning';
	if (count (array_diff ($port['allowed'], array_keys ($domain_vlans))))
		return 'trwarning';
	return 'trbusy';
}

// Show a list of 802.1Q-eligible ports in any way, but when one of
// them is selected as current, also display a form for its setup.
function renderObject8021QPorts ($object_id)
{
	global $sic;
	$vswitch = getVLANSwitchInfo ($object_id);
	$vdom = getVLANDomain ($vswitch['domain_id']);
	$req_port_name = array_fetch ($sic, 'port_name', '');
	$desired_config = apply8021QOrder ($vswitch, getStored8021QConfig ($object_id, 'desired'));
	$cached_config = getStored8021QConfig ($object_id, 'cached');
	$desired_config = sortPortList	($desired_config);
	$uplinks = filter8021QChangeRequests ($vdom['vlanlist'], $desired_config, produceUplinkPorts ($vdom['vlanlist'], $desired_config, $vswitch['object_id']));
	echo '<table border=0 width="100%"><tr valign=top><td class=tdleft width="50%">';
	// port list
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>port</th><th>interface</th><th>link</th><th width="25%">last&nbsp;saved&nbsp;config</th>';
	echo $req_port_name == '' ? '<th width="25%">new&nbsp;config</th></tr>' : '<th>(zooming)</th></tr>';
	if ($req_port_name == '');
		printOpFormIntro ('save8021QConfig', array ('mutex_rev' => $vswitch['mutex_rev'], 'form_mode' => 'save'));
	$sockets = array();
	if (isset ($_REQUEST['hl_port_id']))
	{
		assertUIntArg ('hl_port_id');
		$hl_port_id = intval ($_REQUEST['hl_port_id']);
		$hl_port_name = NULL;
		addAutoScrollScript ("port-$hl_port_id");
	}
	$indexed_ports = array();
	$breed = detectDeviceBreed ($object_id);
	foreach (getObjectPortsAndLinks ($object_id, FALSE) as $port)
	{
		$pn = shortenIfName ($port['name'], $breed);
		if (! isset ($indexed_ports[$pn]) || ! $indexed_ports[$pn]['linked'])
			$indexed_ports[$pn] = $port;
		if (mb_strlen ($port['name']) and array_key_exists ($port['name'], $desired_config))
		{
			if (isset ($hl_port_id) and $hl_port_id == $port['id'])
				$hl_port_name = $port['name'];
			$socket = array ('interface' => formatPortIIFOIF ($port), 'link' => '&nbsp;');
			if ($port['remote_object_id'])
				$socket['link'] = formatLoggedSpan ($port['last_log'], formatLinkedPort ($port));
			elseif (strlen ($port['reservation_comment']))
				$socket['link'] = implode (' ', array(
					formatLoggedSpan ($port['last_log'], 'Rsv:', 'strong underline'),
					formatLoggedSpan ($port['last_log'], $port['reservation_comment'])
				));
			$sockets[$port['name']][] = $socket;
		}
	}
	$nports = 0; // count only access ports
	switchportInfoJS ($object_id); // load JS code to make portnames interactive
	foreach ($desired_config as $port_name => $port)
	{
		$text_left = formatVLANPackDiff ($cached_config[$port_name], $port);
		// decide on row class
		switch ($port['vst_role'])
		{
		case 'none':
			if ($port['mode'] == 'none')
				continue 2; // early miss
			$text_right = '&nbsp;';
			$trclass = 'trerror'; // stuck ghost port
			break;
		case 'downlink':
			$text_right = '(downlink)';
			$trclass = get8021QPortTrClass ($port, $vdom['vlanlist'], 'trunk');
			break;
		case 'uplink':
			$text_right = '(uplink)';
			$trclass = same8021QConfigs ($port, $uplinks[$port_name]) ? 'trbusy' : 'trwarning';
			break;
		case 'trunk':
			$text_right = getTrunkPortCursorCode ($object_id, $port_name, $req_port_name);
			$trclass = get8021QPortTrClass ($port, $vdom['vlanlist'], 'trunk');
			break;
		case 'access':
			$text_right = getAccessPortControlCode ($req_port_name, $vdom, $port_name, $port, $nports);
			$trclass = get8021QPortTrClass ($port, $vdom['vlanlist'], 'access');
			break;
		case 'anymode':
			$text_right = getAccessPortControlCode ($req_port_name, $vdom, $port_name, $port, $nports);
			$text_right .= '&nbsp;';
			$text_right .= getTrunkPortCursorCode ($object_id, $port_name, $req_port_name);
			$trclass = get8021QPortTrClass ($port, $vdom['vlanlist'], NULL);
			break;
		default:
			throw new InvalidArgException ('vst_role', $port['vst_role']);
		}
		if (isset ($indexed_ports[$port_name]) && ! checkPortRole ($vswitch, $indexed_ports[$port_name], $port_name, $port))
			$trclass = 'trerror';

		if (!array_key_exists ($port_name, $sockets))
		{
			$socket_columns = '<td>&nbsp;</td><td>&nbsp;</td>';
			$td_extra = '';
		}
		else
		{
			$td_extra = count ($sockets[$port_name]) > 1 ? (' rowspan=' . count ($sockets[$port_name])) : '';
			$socket_columns = '';
			foreach ($sockets[$port_name][0] as $tmp)
				$socket_columns .= '<td>' . $tmp . '</td>';
		}
		$anchor = '';
		$tdclass = '';
		if (isset ($hl_port_name) and $hl_port_name == $port_name)
		{
			$tdclass .= 'class="border_highlight"';
			$anchor = "name='port-$hl_port_id'";
		}
		echo "<tr class='${trclass}' valign=top><td${td_extra} ${tdclass} NOWRAP><a class='interactive-portname port-menu nolink' $anchor>${port_name}</a></td>" . $socket_columns;
		echo "<td${td_extra}>${text_left}</td><td class=tdright nowrap${td_extra}>${text_right}</td></tr>";
		if (!array_key_exists ($port_name, $sockets))
			continue;
		$first_socket = TRUE;
		foreach ($sockets[$port_name] as $socket)
			if ($first_socket)
				$first_socket = FALSE;
			else
			{
				echo "<tr class=${trclass} valign=top>";
				foreach ($socket as $tmp)
					echo '<td>' . $tmp . '</td>';
				echo '</tr>';
			}
	}
	echo '<tr><td colspan=5 class=tdcenter><ul class="btns-8021q-sync">';
	if ($req_port_name == '' and $nports)
	{
		echo "<input type=hidden name=nports value=${nports}>";
		echo '<li>' . getImageHREF ('SAVE', 'save configuration', TRUE) . '</li>';
	}
	echo '</form>';
	if (permitted (NULL, NULL, NULL, array (array ('tag' => '$op_recalc8021Q'))))
		echo '<li>' . getOpLink (array ('op' => 'exec8021QRecalc'), '', 'RECALC', 'Recalculate uplinks and downlinks') . '</li>';
	echo '</ul></td></tr></table>';
	if ($req_port_name == '');
		echo '</form>';
	echo '</td>';
	// configuration of currently selected port, if any
	if (!array_key_exists ($req_port_name, $desired_config))
	{
		echo '<td>';
		$port_options = array();
		foreach ($desired_config as $pn => $portinfo)
			if (editable8021QPort ($portinfo))
				$port_options[$pn] = same8021QConfigs ($desired_config[$pn], $cached_config[$pn]) ?
					$pn : "${pn} (*)";
		if (count ($port_options) < 2)
			echo '&nbsp;';
		else
		{
			startPortlet ('port duplicator');
			echo '<table border=0 align=center>';
			printOpFormIntro ('save8021QConfig', array ('mutex_rev' => $vswitch['mutex_rev'], 'form_mode' => 'duplicate'));
			echo '<tr><td>' . getSelect ($port_options, array ('name' => 'from_port')) . '</td></tr>';
			echo '<tr><td>&darr; &darr; &darr;</td></tr>';
			echo '<tr><td>' . getSelect ($port_options, array ('name' => 'to_ports[]', 'size' => getConfigVar ('MAXSELSIZE'), 'multiple' => 1)) . '</td></tr>';
			echo '<tr><td>' . getImageHREF ('COPY', 'duplicate', TRUE) . '</td></tr>';
			echo '</form></table>';
			finishPortlet();
		}
		echo '</td>';
	}
	else
		renderTrunkPortControls
		(
			$vswitch,
			$vdom,
			$req_port_name,
			$desired_config[$req_port_name]
		);
	echo '</tr></table>';
}

// Return the text to place into control column of VLAN ports list
// and modify $nports, when this text was a series of INPUTs.
function getAccessPortControlCode ($req_port_name, $vdom, $port_name, $port, &$nports)
{
	// don't render a form for access ports, when a trunk port is zoomed
	if ($req_port_name != '')
		return '&nbsp;';
	if
	(
		array_key_exists ($port['native'], $vdom['vlanlist']) and
		$vdom['vlanlist'][$port['native']]['vlan_type'] == 'alien'
	)
		return formatVLANAsLabel ($vdom['vlanlist'][$port['native']]);

	static $vlanpermissions = array(); // index: from_vid. value: to_list
	$from = $port['native'];
	if (!array_key_exists ($from, $vlanpermissions))
	{
		$vlanpermissions[$from] = array();
		foreach (array_keys ($vdom['vlanlist']) as $to)
			if (nativeVlanChangePermitted ($port_name, $from, $to, 'save8021QConfig'))
				$vlanpermissions[$from][] = $to;
	}
	$ret = "<input type=hidden name=pn_${nports} value=${port_name}>";
	$ret .= "<input type=hidden name=pm_${nports} value=access>";
	$options = array();
	// Offer only options that are listed in domain and fit into VST.
	// Never offer immune VLANs regardless of VST filter for this port.
	// Also exclude current VLAN from the options, unless current port
	// mode is "trunk" (in this case it should be possible to set VST-
	// approved mode without changing native VLAN ID).
	foreach ($vdom['vlanlist'] as $vlan_id => $vlan_info)
		if
		(
			($vlan_id != $from or $port['mode'] == 'trunk') and
			$vlan_info['vlan_type'] != 'alien' and
			in_array ($vlan_id, $vlanpermissions[$from]) and
			matchVLANFilter ($vlan_id, $port['wrt_vlans'])
		)
			$options[$vlan_id] = formatVLANAsOption ($vlan_info);
	ksort ($options);
	$options['same'] = '-- no change --';
	$ret .= getSelect ($options, array ('name' => "pnv_${nports}"), 'same');
	$nports++;
	return $ret;
}

function getTrunkPortCursorCode ($object_id, $port_name, $req_port_name)
{
	global $pageno, $tabno;
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
	return "<a href='" . makeHref ($linkparams) . "'>"  .
		getImageHREF ($imagename, $imagetext) . '</a>';
}

function renderTrunkPortControls ($vswitch, $vdom, $port_name, $vlanport)
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
		'pm_0' => 'trunk',
		'form_mode' => 'save',
	);
	printOpFormIntro ('save8021QConfig', $formextra);
	echo '<td width="35%">';
	echo '<table border=0 cellspacing=0 cellpadding=3 align=center>';
	echo '<tr><th colspan=2>allowed</th></tr>';
	// Present all VLANs of the domain and all currently configured VLANs
	// (regardless if these sets intersect or not).
	$allowed_options = array();
	foreach ($vdom['vlanlist'] as $vlan_id => $vlan_info)
		$allowed_options[$vlan_id] = array
		(
			'vlan_type' => $vlan_info['vlan_type'],
			'text' => formatVLANAsLabel ($vlan_info),
		);
	foreach ($vlanport['allowed'] as $vlan_id)
		if (!array_key_exists ($vlan_id, $allowed_options))
			$allowed_options[$vlan_id] = array
			(
				'vlan_type' => 'none',
				'text' => "unlisted VLAN ${vlan_id}",
			);
	ksort ($allowed_options);
	foreach ($allowed_options as $vlan_id => $option)
	{
		$selected = '';
		$class = 'tagbox';
		if (in_array ($vlan_id, $vlanport['allowed']))
		{
			$selected = ' checked';
			$class .= ' selected';
		}
		// A real relation to an alien VLANs is shown for a
		// particular port, but it cannot be changed by user.
		if ($option['vlan_type'] == 'alien')
			$selected .= ' disabled';
		echo "<tr><td nowrap colspan=2 class='${class}'>";
		echo "<label><input type=checkbox name='pav_0[]' value='${vlan_id}'${selected}> ";
		echo $option['text'] . "</label></td></tr>";
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
		$native_options = array (0 => array ('vlan_type' => 'none', 'text' => '-- NONE --'));
		foreach ($vlanport['allowed'] as $vlan_id)
			$native_options[$vlan_id] = array_key_exists ($vlan_id, $vdom['vlanlist']) ? array
				(
					'vlan_type' => $vdom['vlanlist'][$vlan_id]['vlan_type'],
					'text' => formatVLANAsLabel ($vdom['vlanlist'][$vlan_id]),
				) : array
				(
					'vlan_type' => 'none',
					'text' => "unlisted VLAN ${vlan_id}",
				);
		foreach ($native_options as $vlan_id => $option)
		{
			$selected = '';
			$class = 'tagbox';
			if ($vlan_id == $vlanport['native'])
			{
				$selected = ' checked';
				$class .= ' selected';
			}
			// When one or more alien VLANs are present on port's list of allowed VLANs,
			// they are shown among radio options, but disabled, so that the user cannot
			// break traffic of these VLANs. In addition to that, when port's native VLAN
			// is set to one of these alien VLANs, the whole group of radio buttons is
			// disabled. These measures make it harder for the system to break a VLAN
			// that is explicitly protected from it.
			if
			(
				$native_options[$vlanport['native']]['vlan_type'] == 'alien' or
				$option['vlan_type'] == 'alien'
			)
				$selected .= ' disabled';
			echo "<tr><td nowrap colspan=2 class='${class}'>";
			echo "<label><input type=radio name='pnv_0' value='${vlan_id}'${selected}> ";
			echo $option['text'] . "</label></td></tr>";
		}
	}
	echo '<tr><td class=tdleft>';
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo '</form></td><td class=tdright>';
	if (!count ($vlanport['allowed']))
		printImageHREF ('CLEAR gray');
	else
	{
		printOpFormIntro ('save8021QConfig', $formextra);
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
	$group_members = getDomainGroupMembers ($vlan['domain_id']);

	// list of VLANs to display linked nets and ports from.
	// If domain is a group master, this list contains all
	// the counterpart vlans from domain group members.
	// If domain is a group member, the list contains one
	// counterpart vlan from the domain master.
	$group_ck_list = array();

	echo '<table border=0 class=objectview cellspacing=0 cellpadding=0>';
	echo '<tr><td colspan=2 align=center><h1>' . formatVLANAsRichText ($vlan) . '</h1></td></tr>';
	echo "<tr><td class=pcleft width='50%'>";
	$summary = array();
	$summary['Domain'] = stringForTD ($vlan['domain_descr'], 0);
	$summary['VLAN ID'] = $vlan['vlan_id'];
	if (strlen ($vlan['vlan_descr']))
		$summary['Description'] = stringForTD ($vlan['vlan_descr'], 0);
	$summary['Propagation'] = $vtoptions[$vlan['vlan_prop']];

	$others = getSearchResultByField
	(
		'VLANDescription',
		array ('domain_id'),
		'vlan_id',
		$vlan['vlan_id'],
		'domain_id',
		1
	);
	$counterparts = array();
	$group_counterparts = array();
	$domain_list = getVLANDomainOptions();
	foreach ($others as $other)
		if ($other['domain_id'] != $vlan['domain_id'])
		{
			$counterpart_ck = "${other['domain_id']}-${vlan['vlan_id']}";
			$counterpart_vlan = getVlanRow ($counterpart_ck);
			$counterpart_link = mkA
			(
				$domain_list[$counterpart_vlan['domain_id']] . ': ' . $counterpart_vlan['vlan_descr'],
				'vlan',
				$counterpart_ck
			);
			if
			(
				$counterpart_vlan['domain_id'] == $vlan['domain_group_id'] or
				in_array($counterpart_vlan['domain_id'], $group_members)
			)
			{
				$group_ck_list[$counterpart_ck] = $counterpart_vlan;
				$group_counterparts[] = $counterpart_link;
			}
			elseif ($vlan['domain_group_id'] and $counterpart_vlan['domain_group_id'] == $vlan['domain_group_id'])
				$group_counterparts[] = $counterpart_link;
			else
				$counterparts[] = $counterpart_link;
		}
	if ($group_counterparts)
	{
		$group_id = $vlan['domain_group_id'] ? $vlan['domain_group_id'] : $vlan['domain_id'];
		$summary[$domain_list[$group_id] . ' counterparts'] = implode ('<br>', $group_counterparts);
	}
	if ($counterparts)
		$summary['Counterparts'] = implode ('<br>', $counterparts);
	renderEntitySummary ($vlan, 'summary', $summary);

	$networks = array();
	$net_vlan_list = array ($vlan);
	foreach (array_keys ($group_ck_list) as $grouped_ck)
		$net_vlan_list[] = getVLANInfo ($grouped_ck);
	foreach ($net_vlan_list as $net_vlan)
		foreach (array ('ipv4net' => 'ipv4nets', 'ipv6net' => 'ipv6nets') as $realm => $key)
			foreach ($net_vlan[$key] as $net_id)
				$networks["$realm-$net_id"] = spotEntity ($realm, $net_id);

	if (0 == count ($networks))
		startPortlet ('no networks');
	else
	{
		startPortlet ('networks (' . count ($networks) . ')');
		$order = 'odd';
		echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
		echo '<tr><th>';
		printImageHREF ('net');
		echo '</th><th>';
		printImageHREF ('text');
		echo '</th></tr>';
		foreach ($networks as $net)
		{
			echo '<tr><td>';
			renderCell ($net);
			echo '</td><td>' . stringForTD ($net['comment']);
			echo '</td></tr>';
			$order = $nextorder[$order];
		}
		echo '</table>';
	}
	finishPortlet();

	$confports = getVLANConfiguredPorts ($vlan_ck);
	foreach (array_keys ($group_ck_list) as $grouped_ck)
		$confports += getVLANConfiguredPorts ($grouped_ck);
	if ($vlan['domain_group_id'])
	{
		// we should find configured port on master's members
		// even if master domain itself does not have such VLAN
		$master_ck = $vlan['domain_group_id'] . '-' . $vlan['vlan_id'];
		if (! isset ($group_ck_list[$master_ck]))
			$confports += getVLANConfiguredPorts ($master_ck);
	}

	// get non-switch device list
	$foreign_devices = array();
	foreach ($confports as $switch_id => $portlist)
	{
		$object = spotEntity ('object', $switch_id);
		foreach ($portlist as $port_name)
			if ($portinfo = getPortinfoByName ($object, $port_name))
				if ($portinfo['linked'] && ! isset ($confports[$portinfo['remote_object_id']]))
					$foreign_devices[$portinfo['remote_object_id']][] = $portinfo;
	}
	if (! empty ($foreign_devices))
	{
		startPortlet ("Non-switch devices");
		echo "<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>";
		echo '<tr><th>device</th><th>ports</th></tr>';
		$order = 'odd';
		foreach ($foreign_devices as $cell_id => $ports)
		{
			echo "<tr class=row_${order} valign=top><td>";
			$cell = spotEntity ('object', $cell_id);
			renderCell ($cell);
			echo "</td><td><ul>";
			foreach ($ports as $portinfo)
				echo "<li>" . formatPortLink ($portinfo['remote_object_id'], NULL, $portinfo['remote_id'], $portinfo['remote_name']) . ' &mdash; ' . formatPort ($portinfo) . "</li>";
			echo "</ul></td></tr>";
			$order = $nextorder[$order];
		}
		echo '</table>';
		finishPortlet();
	}

	echo '</td><td class=pcright>';
	if (!count ($confports))
		startPortlet ('no ports');
	else
	{
		startPortlet ('Switch ports (' . count ($confports) . ')');
		global $nextorder;
		$order = 'odd';
		echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
		echo '<tr><th>switch</th><th>ports</th></tr>';
		foreach ($confports as $switch_id => $portlist)
		{
			usort_portlist ($portlist);
			echo "<tr class=row_${order} valign=top><td>";
			$object = spotEntity ('object', $switch_id);
			renderCell ($object);
			echo '</td><td class=tdleft><ul>';
			foreach ($portlist as $port_name)
			{
				echo '<li>';
				if ($portinfo = getPortinfoByName ($object, $port_name))
				{
					echo formatPortLink ($object['id'], NULL, $portinfo['id'], $portinfo['name']);
					if ($portinfo['linked'])
						echo ' &mdash; ' . formatPortLink ($portinfo['remote_object_id'], $portinfo['remote_object_name'], $portinfo['remote_id'], NULL);
				}
				else
					echo $port_name;
				echo '</li>';
			}
			echo '</ul></td></tr>';
			$order = $nextorder[$order];
		}
		echo '</table>';
	}
	finishPortlet();
	echo '</td></tr></table>';
}

function renderVLANIPLinks ($some_id)
{
	function printNewItemTR ($sname, $options, $extra = array())
	{
		if (!count ($options))
			return;
		printOpFormIntro ('bind', $extra);
		echo '<tr><td>' . getOptionTree ($sname, $options);
		echo '</td><td>' . getImageHREF ('ATTACH', 'bind', TRUE) . '</td></tr></form>';
	}
	global $pageno, $tabno;
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr>';

	// fill $minuslines, $plusoptions, $select_name
	$minuslines = array();
	$plusoptions = array();
	$extra = array();
	switch ($pageno)
	{
	case 'vlan':
		$ip_ver = $tabno == 'ipv6' ? 'ipv6' : 'ipv4';
		echo '<th>' . getImageHREF ('net') . '</th>';
		$vlan = getVLANInfo ($some_id);
		$domainclass = array ($vlan['domain_id'] => 'trbusy');
		foreach ($vlan[$ip_ver . "nets"] as $net_id)
			$minuslines[] = array
			(
				'net_id' => $net_id,
				'domain_id' => $vlan['domain_id'],
				'vlan_id' => $vlan['vlan_id'],
			);
		// Any VLAN can link to any network that isn't yet linked to current domain.
		// get free IP nets
		$netlist_func  = $ip_ver == 'ipv6' ? 'getVLANIPv6Options' : 'getVLANIPv4Options';
		foreach ($netlist_func ($vlan['domain_id']) as $net_id)
		{
			$netinfo = spotEntity ($ip_ver . 'net', $net_id);
			if (considerConfiguredConstraint ($netinfo, 'VLANIPV4NET_LISTSRC'))
				$plusoptions['other'][$net_id] =
					$netinfo['ip'] . '/' . $netinfo['mask'] . ' ' . $netinfo['name'];
		}
		$select_name = 'id';
		$extra = array ('vlan_ck' => $vlan['domain_id'] . '-' . $vlan['vlan_id']);
		break;
	case 'ipv4net':
	case 'ipv6net':
		echo '<th>VLAN</th>';
		$netinfo = spotEntity ($pageno, $some_id);
		$reuse_domain = considerConfiguredConstraint ($netinfo, '8021Q_MULTILINK_LISTSRC');
		# For each of the domains linked to the network produce class name based on
		# number of VLANs linked and the current "reuse" setting.
		$domainclass = array();
		foreach (array_count_values (reduceSubarraysToColumn ($netinfo['8021q'], 'domain_id')) as $domain_id => $vlan_count)
			$domainclass[$domain_id] = $vlan_count == 1 ? 'trbusy' : ($reuse_domain ? 'trwarning' : 'trerror');
		# Depending on the setting and the currently linked VLANs reduce the list of new
		# options by either particular VLANs or whole domains.
		$except = array();
		foreach ($netinfo['8021q'] as $item)
		{
			if ($reuse_domain)
				$except[$item['domain_id']][] = $item['vlan_id'];
			elseif (! array_key_exists ($item['domain_id'], $except))
				$except[$item['domain_id']] = range (VLAN_MIN_ID, VLAN_MAX_ID);
			$minuslines[] = array
			(
				'net_id' => $netinfo['id'],
				'domain_id' => $item['domain_id'],
				'vlan_id' => $item['vlan_id'],
			);
		}
		$plusoptions = getAllVLANOptions ($except);
		$select_name = 'vlan_ck';
		$extra = array ('id' => $netinfo['id']);
		break;
	}
	echo '<th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($select_name, $plusoptions, $extra);
	foreach ($minuslines as $item)
	{
		echo '<tr class=' . $domainclass[$item['domain_id']] . '><td>';
		switch ($pageno)
		{
		case 'vlan':
			renderCell (spotEntity ($ip_ver . 'net', $item['net_id']));
			break;
		case 'ipv4net':
		case 'ipv6net':
			$vlaninfo = getVlanRow ($item['domain_id'] . '-' . $item['vlan_id']);
			echo formatVLANAsRichText ($vlaninfo);
			break;
		}
		echo '</td><td>';
		echo getOpLink (array ('id' => $some_id, 'op' => 'unbind', 'id' => $item['net_id'], 'vlan_ck' => $item['domain_id'] . '-' . $item['vlan_id']), '', 'Cut', 'unbind');
		echo '</td></tr>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ($select_name, $plusoptions, $extra);
	echo '</table>';
}

function renderObject8021QSync ($object_id)
{
	$vswitch = getVLANSwitchInfo ($object_id);
	$object = spotEntity ('object', $object_id);
	amplifyCell ($object);
	$maxdecisions = 0;
	$D = getStored8021QConfig ($vswitch['object_id'], 'desired');
	$C = getStored8021QConfig ($vswitch['object_id'], 'cached');
	try
	{
		$R = getRunning8021QConfig ($object_id);
		$plan = apply8021QOrder ($vswitch, get8021QSyncOptions ($vswitch, $D, $C, $R['portdata']));
		foreach ($plan as $port)
			if
			(
				$port['status'] == 'delete_conflict' or
				$port['status'] == 'merge_conflict' or
				$port['status'] == 'add_conflict' or
				$port['status'] == 'martian_conflict'
			)
				$maxdecisions++;
	}
	catch (RTGatewayError $re)
	{
		$error = $re->getMessage();
		$R = NULL;
	}

	echo '<table border=0 class=objectview cellspacing=0 cellpadding=0>';
	echo '<tr><td class=pcleft width="50%">';
	startPortlet ('schedule');
	renderObject8021QSyncSchedule ($object, $vswitch, $maxdecisions);
	finishPortlet();
	startPortlet ('preview legend');
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>status</th><th width="50%">color code</th></tr>';
	echo '<tr><td class=tdright>with template role:</td><td class=trbusy>&nbsp;</td></tr>';
	echo '<tr><td class=tdright>without template role:</td><td>&nbsp;</td></tr>';
	echo '<tr><td class=tdright>new data:</td><td class=trok>&nbsp;</td></tr>';
	echo '<tr><td class=tdright>warnings in new data:</td><td class=trwarning>&nbsp;</td></tr>';
	echo '<tr><td class=tdright>fatal errors in new data:</td><td class=trerror>&nbsp;</td></tr>';
	echo '<tr><td class=tdright>deleted data:</td><td class=trnull>&nbsp;</td></tr>';
	echo '</table>';
	finishPortlet();
	if (considerConfiguredConstraint ($object, '8021Q_EXTSYNC_LISTSRC'))
	{
		startPortlet ('add/remove 802.1Q ports');
		renderObject8021QSyncPorts ($object, $D);
		finishPortlet();
	}
	echo '</td><td class=pcright>';
	startPortlet ('sync plan live preview');
	if ($R !== NULL)
		renderObject8021QSyncPreview ($object, $vswitch, $plan, $C, $R, $maxdecisions);
	else
		echo "<p class=row_error>gateway error: ${error}</p>";
	finishPortlet();
	echo '</td></tr></table>';
}

function renderObject8021QSyncSchedule ($object, $vswitch, $maxdecisions)
{
	echo '<table border=0 cellspacing=0 cellpadding=3 align=center>';
	// FIXME: sort rows newest event last
	$rows = array();
	if (! considerConfiguredConstraint ($object, 'SYNC_802Q_LISTSRC'))
		$rows['auto sync'] = '<span class="trerror">disabled by operator</span>';
	$rows['last local change'] = datetimestrFromTimestamp ($vswitch['last_change']) . ' (' . formatAge ($vswitch['last_change']) . ')';
	$rows['device out of sync'] = $vswitch['out_of_sync'];
	if ($vswitch['out_of_sync'] == 'no')
	{
		$push_duration = $vswitch['last_push_finished'] - $vswitch['last_push_started'];
		$rows['last sync session with device'] = datetimestrFromTimestamp ($vswitch['last_push_started']) . ' (' . formatAge ($vswitch['last_push_started']) .
			', ' . ($push_duration < 0 ?  'interrupted' : "lasted ${push_duration}s") . ')';
	}
	if ($vswitch['last_errno'])
		$rows['failed'] = datetimestrFromTimestamp ($vswitch['last_error_ts']) . ' (' . strerror8021Q ($vswitch['last_errno']) . ')';

	if (NULL !== $new_rows = callHook ('alter8021qSyncSummaryItems', $rows))
		$rows = $new_rows;

	foreach ($rows as $th => $td)
		echo "<tr><th width='50%' class=tdright>${th}:</th><td class=tdleft colspan=2>${td}</td></tr>";

	echo '<tr><th class=tdright>run now:</th><td class=tdcenter>';
	printOpFormIntro ('exec8021QPull');
	echo getImageHREF ('prev', 'pull remote changes in', TRUE) . '</form></td><td class=tdcenter>';
	if ($maxdecisions)
		echo getImageHREF ('COMMIT gray', 'cannot push due to version conflict(s)');
	else
	{
		printOpFormIntro ('exec8021QPush');
		echo getImageHREF ('COMMIT', 'push local changes out', TRUE) . '</form>';
	}
	echo '</td></tr>';
	echo '</table>';
}

function renderObject8021QSyncPreview ($object, $vswitch, $plan, $C, $R, $maxdecisions)
{
	if (isset ($_REQUEST['hl_port_id']))
	{
		assertUIntArg ('hl_port_id');
		$hl_port_id = intval ($_REQUEST['hl_port_id']);
		$hl_port_name = NULL;
		addAutoScrollScript ("port-$hl_port_id");

		foreach ($object['ports'] as $port)
			if (mb_strlen ($port['name']) && $port['id'] == $hl_port_id)
			{
				$hl_port_name = $port['name'];
				break;
			}
		unset ($object);
	}

	switchportInfoJS ($vswitch['object_id']); // load JS code to make portnames interactive
	// initialize one of three popups: we've got data already
	$port_config = addslashes (json_encode (formatPortConfigHints ($vswitch['object_id'], $R)));
	addJS (<<<END
$(document).ready(function(){
	var confData = $.parseJSON('$port_config');
	applyConfData(confData);
	var menuItem = $('.context-menu-item.itemname-conf');
	menuItem.addClass($.contextMenu.disabledItemClassName);
	setItemIcon(menuItem[0], 'ok');
});
END
	, TRUE);
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable width="100%">';
	if ($maxdecisions)
		echo '<tr><th colspan=2>&nbsp;</th><th colspan=3>discard</th><th>&nbsp;</th></tr>';
	echo '<tr valign=top><th>port</th><th width="40%">last&nbsp;saved&nbsp;version</th>';
	if ($maxdecisions)
	{
		addJS ('js/racktables.js');
		printOpFormIntro ('resolve8021QConflicts', array ('mutex_rev' => $vswitch['mutex_rev']));
		foreach (array ('left', 'asis', 'right') as $pos)
			echo "<th class=tdcenter><input type=radio name=column_radio value=${pos} " .
				"onclick=\"checkColumnOfRadios8021Q('i_', ${maxdecisions}, '_${pos}')\"></th>";
	}
	echo '<th width="40%">running&nbsp;version</th></tr>';
	$rownum = 0;
	$plan = sortPortList ($plan);
	$domvlans = array_keys (getDomainVLANList ($vswitch['domain_id']));
	$default_port = array
	(
		'mode' => 'access',
		'allowed' => array (VLAN_DFL_ID),
		'native' => VLAN_DFL_ID,
	);
	foreach ($plan as $port_name => $item)
	{
		$trclass = $left_extra = $right_extra = $left_text = $right_text = '';
		$radio_attrs = array();
		switch ($item['status'])
		{
		case 'ok_to_delete':
			$left_text = serializeVLANPack ($item['left']);
			$right_text = 'none';
			$left_extra = ' trnull';
			$right_extra = ' trok'; // no confirmation is necessary
			break;
		case 'delete_conflict':
			$trclass = 'trbusy';
			$left_extra = ' trerror'; // can be fixed on request
			$right_extra = ' trnull';
			$left_text = formatVLANPackDiff ($item['lastseen'], $item['left']);
			$right_text = '&nbsp;';
			$radio_attrs = array ('left' => '', 'asis' => ' checked', 'right' => ' disabled');
			// dummy setting to suppress warnings in resolve8021QConflicts()
			$item['right'] = $default_port;
			break;
		case 'add_conflict':
			$trclass = 'trbusy';
			$right_extra = ' trerror';
			$left_text = '&nbsp;';
			$right_text = serializeVLANPack ($item['right']);
			break;
		case 'ok_to_add':
			$trclass = 'trbusy';
			$right_extra = ' trok';
			$left_text = '&nbsp;';
			$right_text = serializeVLANPack ($item['right']);
			break;
		case 'ok_to_merge':
			$trclass = 'trbusy';
			$left_extra = ' trok';
			$right_extra = ' trok';
			// fall through
		case 'in_sync':
			$trclass = 'trbusy';
			$left_text = $right_text = serializeVLANPack ($item['both']);
			break;
		case 'ok_to_pull':
			// at least one of the sides is not in the default state
			$trclass = 'trbusy';
			$right_extra = ' trok';
			$left_text = serializeVLANPack ($item['left']);
			$right_text = serializeVLANPack ($item['right']);
			break;
		case 'ok_to_push':
			$trclass = ' trbusy';
			$left_extra = ' trok';
			$left_text = formatVLANPackDiff ($C[$port_name], $item['left']);
			$right_text = serializeVLANPack ($item['right']);
			break;
		case 'merge_conflict':
			$trclass = 'trbusy';
			$left_extra = ' trerror';
			$right_extra = ' trerror';
			$left_text = formatVLANPackDiff ($C[$port_name], $item['left']);
			$right_text = serializeVLANPack ($item['right']);
			// enable, but consider each option independently
			// Don't accept running VLANs not in domain, and
			// don't offer anything, that VST will deny.
			// Consider domain and template constraints.
			$radio_attrs = array ('left' => '', 'asis' => ' checked', 'right' => '');
			if
			(
				!acceptable8021QConfig ($item['right']) or
				count (array_diff ($item['right']['allowed'], $domvlans)) or
				!goodModeForVSTRole ($item['right']['mode'], $item['vst_role'])
			)
				$radio_attrs['left'] = ' disabled';
			break;
		case 'ok_to_push_with_merge':
			$trclass = 'trbusy';
			$left_extra = ' trok';
			$right_extra = ' trwarning';
			$left_text = formatVLANPackDiff ($C[$port_name], $item['left']);
			$right_text = serializeVLANPack ($item['right']);
			break;
		case 'none':
			$left_text = '&nbsp;';
			$right_text = '&nbsp;';
			break;
		case 'martian_conflict':
			if ($item['right']['mode'] == 'none')
				$right_text = '&nbsp;';
			else
			{
				$right_text = serializeVLANPack ($item['right']);
				$right_extra = ' trerror';
			}
			if ($item['left']['mode'] == 'none')
				$left_text = '&nbsp;';
			else
			{
				$left_text = serializeVLANPack ($item['left']);
				$left_extra = ' trerror';
				$radio_attrs = array ('left' => '', 'asis' => ' checked', 'right' => ' disabled');
				// idem, see above
				$item['right'] = $default_port;
			}
			break;
		default:
			$trclass = 'trerror';
			$left_text = $right_text = 'internal rendering error';
			break;
		}

		$anchor = '';
		$td_class = '';
		if (isset ($hl_port_name) and $hl_port_name == $port_name)
		{
			$anchor = "name='port-$hl_port_id'";
			$td_class = ' border_highlight';
		}
		echo "<tr class='${trclass}'><td class='tdleft${td_class}' NOWRAP><a class='interactive-portname port-menu nolink' $anchor>${port_name}</a></td>";
		if (!count ($radio_attrs))
		{
			echo "<td class='tdleft${left_extra}'>${left_text}</td>";
			if ($maxdecisions)
				echo '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
			echo "<td class='tdleft${right_extra}'>${right_text}</td>";
		}
		else
		{
			echo "<td class='tdleft${left_extra}'><label for=i_${rownum}_left>${left_text}</label></td>";
			foreach ($radio_attrs as $pos => $attrs)
				echo "<td><input id=i_${rownum}_${pos} name=i_${rownum} type=radio value=${pos}${attrs}></td>";
			echo "<td class='tdleft${right_extra}'><label for=i_${rownum}_right>${right_text}</label></td>";
		}
		echo '</tr>';
		if (count ($radio_attrs))
		{
			echo "<input type=hidden name=rm_${rownum} value=" . $item['right']['mode'] . '>';
			echo "<input type=hidden name=rn_${rownum} value=" . $item['right']['native'] . '>';
			foreach ($item['right']['allowed'] as $a)
				echo "<input type=hidden name=ra_${rownum}[] value=${a}>";
			echo "<input type=hidden name=pn_${rownum} value='" . htmlspecialchars ($port_name) . "'>";
		}
		$rownum += count ($radio_attrs) ? 1 : 0;
	}
	if ($rownum) // normally should be equal to $maxdecisions
	{
		echo "<input type=hidden name=nrows value=${rownum}>";
		echo '<tr><td colspan=2>&nbsp;</td><td colspan=3 align=center class=tdcenter>';
		printImageHREF ('UNLOCK', 'resolve conflicts', TRUE);
		echo '</td><td>&nbsp;</td></tr>';
	}
	echo '</table>';
	echo '</form>';
}

function renderObject8021QSyncPorts ($object, $D)
{
	$allethports = array();
	foreach (array_filter ($object['ports'], 'isEthernetPort') as $port)
		$allethports[$port['name']] = formatPortIIFOIF ($port);
	$enabled = array();
	# OPTIONSs for existing 802.1Q ports
	foreach (sortPortList ($D) as $portname => $portconfig)
		$enabled["disable ${portname}"] = "${portname} ("
			. array_fetch ($allethports, $portname, 'N/A')
			. ') ' . serializeVLANPack ($portconfig);
	# OPTIONs for potential 802.1Q ports
	$disabled = array();
	foreach (sortPortList ($allethports) as $portname => $iifoif)
		if (! array_key_exists ("disable ${portname}", $enabled))
			$disabled["enable ${portname}"] = "${portname} (${iifoif})";
	printOpFormIntro ('updPortList');
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><td>';
	printNiftySelect
	(
		array ('select ports to disable 802.1Q' => $enabled, 'select ports to enable 802.1Q' => $disabled),
		array ('name' => 'ports[]', 'multiple' => 1, 'size' => getConfigVar ('MAXSELSIZE'))
	);
	echo '</td></tr>';
	echo '<tr><td>' . getImageHREF ('RECALC', 'process changes', TRUE) . '</td></tr>';
	echo '</table></form>';
}

function renderVSTListEditor()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr>';
		echo '<td>' . getImageHREF ('create', 'create template', TRUE) . '</td>';
		echo '<td><input type=text size=48 name=vst_descr></td>';
		echo '<td>' . getImageHREF ('create', 'create template', TRUE) . '</td>';
		echo '</tr></form>';
	}
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>&nbsp;</th><th>description</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach (listCells ('vst') as $vst_id => $vst_info)
	{
		printOpFormIntro ('upd', array ('vst_id' => $vst_id));
		echo '<tr><td>';
		if ($vst_info['switchc'])
			printImageHREF ('nodestroy', 'template used elsewhere');
		else
			echo getOpLink (array ('op' => 'del', 'vst_id' => $vst_id), '', 'destroy', 'delete template');
		echo '</td>';
		echo '<td><input name=vst_descr type=text size=48 value="' . stringForTextInputValue ($vst_info['description'], 255) . '"></td>';
		echo '<td>' . getImageHREF ('save', 'update template', TRUE) . '</td>';
		echo '</tr></form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo '</table>';
}

function renderVSTRules ($rules, $title = NULL)
{
	if (!count ($rules))
		startPortlet (isset ($title) ? $title : 'no rules');
	else
	{
		global $port_role_options, $nextorder;
		startPortlet (isset ($title) ? $title : 'rules (' . count ($rules) . ')');
		echo '<table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>';
		echo '<tr><th>sequence</th><th>regexp</th><th>role</th><th>VLAN IDs</th><th>comment</th></tr>';
		$order = 'odd';
		foreach ($rules as $item)
		{
			echo "<tr class=row_${order} align=left>";
			echo "<td>${item['rule_no']}</td>";
			echo "<td nowrap><tt>${item['port_pcre']}</tt></td>";
			echo '<td nowrap>' . $port_role_options[$item['port_role']] . '</td>';
			echo "<td>${item['wrt_vlans']}</td>";
			echo "<td>${item['description']}</td>";
			echo '</tr>';
			$order = $nextorder[$order];
		}
		echo '</table>';
	}
	finishPortlet();
}

function renderVST ($vst_id)
{
	$vst = spotEntity ('vst', $vst_id);
	amplifyCell ($vst);
	echo '<table border=0 class=objectview cellspacing=0 cellpadding=0>';
	echo '<tr><td colspan=2 align=center><h1>' . stringForTD ($vst['description'], 0) . '</h1></td></tr>';
	echo "<tr><td class=pcleft width='50%'>";

	renderEntitySummary ($vst, 'summary', array ('tags' => ''));

	renderVSTRules ($vst['rules']);
	echo '</td><td class=pcright>';
	if (!count ($vst['switches']))
		startPortlet ('no orders');
	else
	{
		global $nextorder;
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

function renderVSTRulesEditor ($vst_id)
{
	global $port_role_options;
	$vst = spotEntity ('vst', $vst_id);
	amplifyCell ($vst);
	$source_options = array();
	if (! $vst['rulec'])
		foreach (listCells ('vst') as $vst_id => $vst_info)
			if ($vst_info['rulec'])
				$source_options[$vst_id] = '(' . $vst_info['rulec'] . ') ' . $vst_info['description'];
	addJS ('js/vst_editor.js');
	echo '<center><h1>' . stringForLabel ($vst['description']) . '</h1></center>';
	if (count ($source_options))
	{
		startPortlet ('clone another template');
		printOpFormIntro ('clone');
		echo '<input type=hidden name="mutex_rev" value="' . $vst['mutex_rev'] . '">';
		echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
		echo '<tr><td>' . getSelect ($source_options, array ('name' => 'from_id')) . '</td>';
		echo '<td>' . getImageHREF ('COPY', 'copy from selected', TRUE) . '</td></tr></table></form>';
		finishPortlet();
		startPortlet ('add rules one by one');
	}
	printOpFormIntro ('upd', array ('mutex_rev' => $vst['mutex_rev']));
	echo '<table cellspacing=0 cellpadding=5 align=center class="widetable template-rules">';
	echo "<tr><th class=tdright>Tags:</th><td class=tdleft style='border-top: none;'>";
	printTagsPicker ();
	echo "</td></tr>";
	echo '<tr><th></th><th>sequence</th><th>regexp</th><th>role</th>';
	echo '<th>VLAN IDs</th><th>comment</th><th><a href="#" class="vst-add-rule initial">' . getImageHREF ('add', 'Add rule') . '</a></th></tr>';
	$row_html  = '<td><a href="#" class="vst-del-rule">' . getImageHREF ('destroy', 'delete rule') . '</a></td>';
	$row_html .= '<td><input type=text name=rule_no value="%s" size=3></td>';
	$row_html .= '<td><input type=text name=port_pcre value="%s"></td>';
	$row_html .= '<td>%s</td>';
	$row_html .= '<td><input type=text name=wrt_vlans value="%s"></td>';
	$row_html .= '<td><input type=text name=description value="%s"></td>';
	$row_html .= '<td><a href="#" class="vst-add-rule">' . getImageHREF ('add', 'Duplicate rule') . '</a></td>';
	addJS ("var new_vst_row = '" . addslashes (sprintf ($row_html, '', '', getSelect ($port_role_options, array ('name' => 'port_role'), 'anymode'), '', '')) . "';", TRUE);
	startSession();
	foreach (array_fetch ($_SESSION, 'vst_edited', $vst['rules']) as $item)
		printf ('<tr>' . $row_html . '</tr>', $item['rule_no'], htmlspecialchars ($item['port_pcre'], ENT_QUOTES),  getSelect ($port_role_options, array ('name' => 'port_role'), $item['port_role']), $item['wrt_vlans'], $item['description']);
	echo '</table>';
	echo '<input type=hidden name="template_json">';
	echo '<center>' . getImageHref ('SAVE', 'Save template', TRUE) . '</center>';
	echo '</form>';
	if (isset ($_SESSION['vst_edited']))
	{
		// draw current template
		renderVSTRules ($vst['rules'], 'currently saved tamplate');
		unset ($_SESSION['vst_edited']);
	}
	session_commit();

	if (count ($source_options))
		finishPortlet();
}

function renderDeployQueue()
{
	global $nextorder, $dqtitle;
	$order = 'odd';
	$dqcode = getBypassValue();
	$allq = get8021QDeployQueues();
	foreach ($allq as $qcode => $data)
		if ($dqcode == $qcode)
		{
			echo "<h2 align=center>Queue '" . $dqtitle[$qcode] . "' (" . count ($data) . ")</h2>";
			if (! count ($data))
				continue;
			echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
			echo '<tr><th>switch</th><th>changed</th><th>';
			foreach ($data as $item)
			{
				echo "<tr class=row_${order}><td>";
				renderCell (spotEntity ('object', $item['object_id']));
				echo "</td><td>" . formatAge ($item['last_change']) . "</td></tr>";
				$order = $nextorder[$order];
			}
			echo '</table>';
		}
}

function renderDiscoveredNeighbors ($object_id)
{
	global $tabno;

	$opcode_by_tabno = array
	(
		'livecdp' => 'getcdpstatus',
		'livelldp' => 'getlldpstatus',
	);
	try
	{
		$neighbors = queryDevice ($object_id, $opcode_by_tabno[$tabno]);
		$neighbors = sortPortList ($neighbors);
	}
	catch (RTGatewayError $e)
	{
		showError ($e->getMessage());
		return;
	}
	$mydevice = spotEntity ('object', $object_id);
	amplifyCell ($mydevice);

	// reindex by port name
	$myports = array();
	foreach ($mydevice['ports'] as $port)
		if (mb_strlen ($port['name']))
			$myports[$port['name']][] = $port;

	// scroll to selected port
	if (isset ($_REQUEST['hl_port_id']))
	{
		assertUIntArg('hl_port_id');
		$hl_port_id = intval ($_REQUEST['hl_port_id']);
		addAutoScrollScript ("port-$hl_port_id");
	}

	switchportInfoJS($object_id); // load JS code to make portnames interactive
	printOpFormIntro ('importDPData');
	echo '<br><table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th colspan=2>local port</th><th></th><th>remote device</th><th colspan=2>remote port</th><th><input type="checkbox" checked id="cb-toggle"></th></tr>';
	$inputno = 0;
	foreach ($neighbors as $local_port => $remote_list)
	{
		$initial_row = TRUE; // if port has multiple neighbors, the first table row is initial
		// array of local ports with the name specified by DP
		$local_ports = isset($myports[$local_port]) ? $myports[$local_port] : array();
		foreach ($remote_list as $dp_neighbor) // step over DP neighbors
		{
			$error_message = NULL;
			$link_matches = FALSE;
			$portinfo_local = NULL;
			$portinfo_remote = NULL;
			$variants = array();

			do { // once-cyle fake loop used only to break out of it
				if (! empty($local_ports))
					$portinfo_local = $local_ports[0];

				// find remote object by DP information
				$dp_remote_object_id = searchByMgmtHostname ($dp_neighbor['device']);
				if (! $dp_remote_object_id)
					$dp_remote_object_id = lookupEntityByString ('object', $dp_neighbor['device']);
				if (! $dp_remote_object_id)
				{
					$error_message = "No such neighbor <i>${dp_neighbor['device']}</i>";
					break;
				}
				$dp_remote_object = spotEntity ('object', $dp_remote_object_id);
				$dp_neighbor['port'] = shortenIfName ($dp_neighbor['port'], NULL, $dp_remote_object['id']);

				// get list of ports that have name matching CDP portname
				$remote_ports = array(); // list of remote (by DP info) ports
				foreach (getObjectPortsAndLinks ($dp_remote_object_id, FALSE) as $port)
					if ($port['name'] == $dp_neighbor['port'])
					{
						$portinfo_remote = $port;
						$remote_ports[] = $port;
					}

				// check if ports with such names exist on devices
				if (empty ($local_ports))
				{
					$error_message = "No such local port <i>$local_port</i>";
					break;
				}
				if (empty ($remote_ports))
				{
					$error_message = "No such port on "
						. formatPortLink ($dp_remote_object['id'], $dp_remote_object['name'], NULL, NULL);
					break;
				}

				// determine match or mismatch of local link
				foreach ($local_ports as $portinfo_local)
					if ($portinfo_local['remote_id'])
					{
						if
						(
							$portinfo_local['remote_object_id'] == $dp_remote_object_id
							and $portinfo_local['remote_name'] == $dp_neighbor['port']
						)
						{
							// set $portinfo_remote to corresponding remote port
							foreach ($remote_ports as $portinfo_remote)
								if ($portinfo_remote['id'] == $portinfo_local['remote_id'])
									break;
							$link_matches = TRUE;
							unset ($error_message);
						}
						elseif ($portinfo_local['remote_object_id'] != $dp_remote_object_id)
							$error_message = "Remote device mismatch - port linked to "
								. formatLinkedPort ($portinfo_local);
						else // ($portinfo_local['remote_name'] != $dp_neighbor['port'])
							$error_message = "Remote port mismatch - port linked to "
								. formatPortLink ($portinfo_local['remote_object_id'], NULL, $portinfo_local['remote_id'], $portinfo_local['remote_name']);;
						break 2;
					}

				// no local links found, try to search for remote links
				foreach ($remote_ports as $portinfo_remote)
					if ($portinfo_remote['remote_id'])
					{
						$remote_link_html = formatLinkedPort ($portinfo_remote);
						$remote_port_html = formatPortLink ($portinfo_remote['object_id'], NULL, $portinfo_remote['id'], $portinfo_remote['name']);
						$error_message = "Remote port $remote_port_html is already linked to $remote_link_html";
						break 2;
					}

				// no links found on both sides, search for a compatible port pair
				$port_types = array();
				foreach (array ('left' => $local_ports, 'right' => $remote_ports) as $side => $port_list)
					foreach ($port_list as $portinfo)
					{
						$tmp_types = ($portinfo['iif_id'] == 1) ?
							array ($portinfo['oif_id'] => $portinfo['oif_name']) :
							getExistingPortTypeOptions ($portinfo);
						foreach ($tmp_types as $oif_id => $oif_name)
							$port_types[$side][$oif_id][] = array ('id' => $oif_id, 'name' => $oif_name, 'portinfo' => $portinfo);
					}

				foreach ($port_types['left'] as $left_id => $left)
				foreach ($port_types['right'] as $right_id => $right)
					if (arePortTypesCompatible ($left_id, $right_id))
						foreach ($left as $left_port)
						foreach ($right as $right_port)
							$variants[] = array ('left' => $left_port, 'right' => $right_port);
				if (! count ($variants)) // no compatible ports found
					$error_message = "Incompatible port types";
			} while (FALSE); // do {

			$tr_class = $link_matches ? 'trok' : (isset ($error_message) ? 'trerror' : 'trwarning');
			echo "<tr class=\"$tr_class\">";
			if ($initial_row)
			{
				$count = count ($remote_list);
				$td_class = '';
				if (isset ($hl_port_id) and $hl_port_id == $portinfo_local['id'])
					$td_class = "class='border_highlight'";
				echo "<td rowspan=\"$count\" $td_class NOWRAP>" .
					($portinfo_local ?
						formatPortLink ($mydevice['id'], NULL, $portinfo_local['id'], $portinfo_local['name'], 'interactive-portname port-menu') :
						"<a class='interactive-portname port-menu nolink'>$local_port</a>"
					) .
					($count > 1 ? "<br> ($count neighbors)" : '') .
					'</td>';
				$initial_row = FALSE;
			}
			echo "<td>" . ($portinfo_local ?  formatPortIIFOIF ($portinfo_local) : '&nbsp;') . "</td>";
			echo "<td>" . formatIfTypeVariants ($variants, "ports_${inputno}") . "</td>";
			echo "<td>${dp_neighbor['device']}</td>";
			echo "<td>" . ($portinfo_remote ? formatPortLink ($dp_remote_object_id, NULL, $portinfo_remote['id'], $portinfo_remote['name']) : $dp_neighbor['port'] ) . "</td>";
			echo "<td>" . ($portinfo_remote ?  formatPortIIFOIF ($portinfo_remote) : '&nbsp;') . "</td>";
			echo "<td>";
			if (! empty ($variants))
			{
				echo "<input type=checkbox name=do_${inputno} class='cb-makelink'>";
				$inputno++;
			}
			echo "</td>";

			if (isset ($error_message))
				echo "<td style=\"background-color: white; border-top: none\">$error_message</td>";
			echo "</tr>";
		}
	}
	if ($inputno)
	{
		echo "<input type=hidden name=nports value=${inputno}>";
		echo '<tr><td colspan=7 align=center>' . getImageHREF ('CREATE', 'import selected', TRUE) . '</td></tr>';
	}
	echo '</table></form>';

	addJS (<<<END
$(document).ready(function () {
	$('#cb-toggle').click(function (event) {
		var list = $('.cb-makelink');
		for (var i in list) {
			var cb = list[i];
			cb.checked = event.target.checked;
		}
	}).triggerHandler('click');
});
END
		, TRUE
	);
}

// $variants is an array of items like this:
// array (
//	'left' => array ('id' => oif_id, 'name' => oif_name, 'portinfo' => $port_info),
//	'left' => array ('id' => oif_id, 'name' => oif_name, 'portinfo' => $port_info),
// )
function formatIfTypeVariants ($variants, $select_name)
{
	if (empty ($variants))
		return;
	static $oif_usage_stat = NULL;
	$select = array();
	$creating_transceivers = FALSE;
	$most_used_count = 0;
	$selected_key = NULL;
	$multiple_left = FALSE;
	$multiple_right = FALSE;

	$seen_ports = array();
	foreach ($variants as $item)
	{
		if (isset ($seen_ports['left']) && $item['left']['portinfo']['id'] != $seen_ports['left'])
			$multiple_left = TRUE;
		if (isset ($seen_ports['right']) && $item['right']['portinfo']['id'] != $seen_ports['right'])
			$multiple_right = TRUE;
		$seen_ports['left'] = $item['left']['portinfo']['id'];
		$seen_ports['right'] = $item['right']['portinfo']['id'];
	}

	if (! isset ($oif_usage_stat))
		$oif_usage_stat = getPortTypeUsageStatistics();

	foreach ($variants as $item)
	{
		// format text label for selectbox item
		$left_text = ($multiple_left ? $item['left']['portinfo']['iif_name'] . '/' : '') . $item['left']['name'];
		$right_text = ($multiple_right ? $item['right']['portinfo']['iif_name'] . '/' : '') . $item['right']['name'];
		$text = $left_text;
		if ($left_text != $right_text && strlen ($right_text))
		{
			if (strlen ($text))
				$text .= " | ";
			$text .= $right_text;
		}

		// fill the $params: port ids and port types
		$params = array
		(
			'a_id' => $item['left']['portinfo']['id'],
			'b_id' => $item['right']['portinfo']['id'],
		);
		$popularity_count = 0;
		foreach (array ('left' => 'a', 'right' => 'b') as $side => $letter)
		{
			$params[$letter . '_oif'] = $item[$side]['id'];
			$type_key = $item[$side]['portinfo']['iif_id'] . '-' . $item[$side]['id'];
			if (isset ($oif_usage_stat[$type_key]))
				$popularity_count += $oif_usage_stat[$type_key];
		}

		$key = ''; // key sample: a_id:id,a_oif:id,b_id:id,b_oif:id
		foreach ($params as $i => $j)
			$key .= "$i:$j,";
		$key = trim($key, ",");
		$select[$key] = (count ($variants) == 1 ? '' : $text); // empty string if there is simple single variant
		$weights[$key] = $popularity_count;
	}
	arsort ($weights, SORT_NUMERIC);
	$sorted_select = array();
	foreach (array_keys ($weights) as $key)
		$sorted_select[$key] = $select[$key];
	return getSelect ($sorted_select, array('name' => $select_name));
}

function formatAttributeValue ($record)
{
	if ('date' == $record['type'])
		return datetimestrFromTimestamp ($record['value']);

	if (! isset ($record['key'])) // if record is a dictionary value, generate href with autotag in cfe
	{
		if ($record['id'] == 3) // FQDN attribute
			foreach (getMgmtProtosConfig() as $proto => $filter)
				try
				{
					if (considerGivenConstraint (NULL, $filter))
					{
						$blank = (preg_match ('/^https?$/', $proto) ? 'target=_blank' : '');
						return "<a $blank title='Open $proto session' class='mgmt-link' href='" . $proto . '://' . $record['a_value'] . "'>${record['a_value']}</a>";
					}
				}
				catch (RackTablesError $e)
				{
					// syntax error in $filter
					continue;
				}
		return isset ($record['href']) ? "<a href=\"".$record['href']."\">${record['a_value']}</a>" : $record['a_value'];
	}

	$href = makeHref
	(
		array
		(
			'page'=>'depot',
			'tab'=>'default',
			'andor' => 'and',
			'cfe' => '{$attr_' . $record['id'] . '_' . $record['key'] . '}',
		)
	);
	$result = "<a href='$href'>" . $record['a_value'] . "</a>";
	if (isset ($record['href']))
		$result .= "&nbsp;<a class='img-link' href='${record['href']}'>" . getImageHREF ('html', 'vendor&apos;s info page') . "</a>";
	return $result;
}

function addAutoScrollScript ($anchor_name)
{
	addJS (<<<END
$(document).ready(function() {
	var anchor = document.getElementsByName('$anchor_name')[0];
	if (anchor)
		anchor.scrollIntoView(false);
});
END
	, TRUE);
}

//
// Display object level logs
//
function renderObjectLogEditor ()
{
	global $nextorder;

	echo "<center><h2>Log records for this object (<a href=?page=objectlog>complete list</a>)</h2></center>";
	printOpFormIntro ('add');
	echo "<table with=80% align=center border=0 cellpadding=5 cellspacing=0 align=center class=cooltable><tr valign=top class=row_odd>";
	echo '<td class=tdcenter>' . getImageHREF ('CREATE', 'add record', TRUE) . '</td>';
	echo '<td><textarea name=logentry rows=10 cols=80></textarea></td>';
	echo '<td class=tdcenter>' . getImageHREF ('CREATE', 'add record', TRUE) . '</td>' ;
	echo '</tr></form>';

	$order = 'even';
	foreach (getLogRecordsForObject (getBypassValue()) as $row)
	{
		echo "<tr class=row_${order} valign=top>";
		echo '<td class=tdleft>' . $row['date'] . '<br>' . $row['user'] . '</td>';
		echo '<td class="logentry">' . string_insert_hrefs (htmlspecialchars ($row['content'], ENT_NOQUOTES)) . '</td>';
		echo "<td class=tdleft>";
		echo getOpLink (array('op'=>'del', 'log_id'=>$row['id']), '', 'DESTROY', 'Delete log entry');
		echo "</td></tr>\n";
		$order = $nextorder[$order];
	}
	echo '</table>';
}

//
// Display form and All log entries
//
function allObjectLogs ()
{
	$logs = getLogRecords ();

	if (count($logs) > 0)
	{
		global $nextorder;
		echo "<br><table width='80%' align=center border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>";
		echo '<tr valign=top><th class=tdleft>Object</th><th class=tdleft>Date/user</th>';
		echo '<th class=tdcenter>' . getImageHREF ('text') . '</th></tr>';

		$order = 'odd';
		foreach ($logs as $row)
		{
			switch ($row['objtype_id'])
			{
				case 1560:
					$text = $row['name'];
					$entity = 'rack';
					break;
				case 1561:
					$text = $row['name'];
					$entity = 'row';
					break;
				case 1562:
					$text = $row['name'];
					$entity = 'location';
					break;
				default:
					$object = spotEntity ('object', $row['object_id']);
					$text = $object['dname'];
					$entity = 'object';
					break;
			}
			echo "<tr class=row_${order} valign=top>";
			echo '<td class=tdleft>' . mkA ($text, $entity, $row['object_id'], 'log') . '</td>';
			echo '<td class=tdleft>' . $row['date'] . '<br>' . $row['user'] . '</td>';
			echo '<td class="logentry">' . string_insert_hrefs (htmlspecialchars ($row['content'], ENT_NOQUOTES)) . '</td>';
			echo "</tr>\n";
			$order = $nextorder[$order];
		}
		echo '</table>';
	}
	else
		echo '<center><h2>No logs exist</h2></center>';
}

function renderGlobalLogEditor()
{
	echo "<table with='80%' align=center border=0 cellpadding=5 cellspacing=0 align=center class=cooltable><tr valign=top>";
	printOpFormIntro ('add');
	echo '<th align=left>Name: ' . getSelect (getNarrowObjectList(), array ('name' => 'object_id')) . '</th>';
	echo "<tr><td align=left><table with=100% border=0 cellpadding=0 cellspacing=0><tr><td colspan=2><textarea name=logentry rows=3 cols=80></textarea></td></tr>";
	echo '<tr><td align=left></td><td align=right>' . getImageHREF ('CREATE', 'add record', TRUE) . '</td>';
	echo '</tr></table></td></tr>';
	echo '</form>';
	echo '</table>';
}

function renderVirtualResourcesSummary ()
{
	global $pageno, $nextorder;

	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";

	$clusters = getVMClusterSummary ();
	startPortlet ('Clusters (' . count ($clusters) . ')');
	if (count($clusters) > 0)
	{
		echo "<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>\n";
		echo "<tr><th>Cluster</th><th>Hypervisors</th><th>Resource Pools</th><th>Cluster VMs</th><th>RP VMs</th><th>Total VMs</th></tr>\n";
		$order = 'odd';
		foreach ($clusters as $cluster)
		{
			$total_vms = $cluster['cluster_vms'] + $cluster['resource_pool_vms'];
			echo "<tr class=row_${order} valign=top>";
			echo '<td class="tdleft">' . mkA ("<strong>${cluster['name']}</strong>", 'object', $cluster['id']) . '</td>';
			echo "<td class='tdleft'>${cluster['hypervisors']}</td>";
			echo "<td class='tdleft'>${cluster['resource_pools']}</td>";
			echo "<td class='tdleft'>${cluster['cluster_vms']}</td>";
			echo "<td class='tdleft'>${cluster['resource_pool_vms']}</td>";
			echo "<td class='tdleft'>$total_vms</td>";
			echo "</tr>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n";
	}
	else
		echo '<b>No clusters exist</b>';
	finishPortlet();

	echo "</td><td class=pcright>";

	$pools = getVMResourcePoolSummary ();
	startPortlet ('Resource Pools (' . count ($pools) . ')');
	if (count($pools) > 0)
	{
		echo "<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>\n";
		echo "<tr><th>Pool</th><th>Cluster</th><th>VMs</th></tr>\n";
		$order = 'odd';
		foreach ($pools as $pool)
		{
			echo "<tr class=row_${order} valign=top>";
			echo '<td class="tdleft">' . mkA ("<strong>${pool['name']}</strong>", 'object', $pool['id']) . '</td>';
			echo '<td class="tdleft">';
			if ($pool['cluster_id'])
				echo mkA ("<strong>${pool['cluster_name']}</strong>", 'object', $pool['cluster_id']);
			echo '</td>';
			echo "<td class='tdleft'>${pool['VMs']}</td>";
			echo "</tr>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n";
	}
	else
		echo '<b>No pools exist</b>';
	finishPortlet();

	echo "</td></tr><tr><td class=pcleft>";

	$hypervisors = getVMHypervisorSummary ();
	startPortlet ('Hypervisors (' . count ($hypervisors) . ')');
	if (count($hypervisors) > 0)
	{
		echo "<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>\n";
		echo "<tr><th>Hypervisor</th><th>Cluster</th><th>VMs</th></tr>\n";
		$order = 'odd';
		foreach ($hypervisors as $hypervisor)
		{
			echo "<tr class=row_${order} valign=top>";
			echo '<td class="tdleft">' . mkA ("<strong>${hypervisor['name']}</strong>", 'object', $hypervisor['id']) . '</td>';
			echo '<td class="tdleft">';
			if ($hypervisor['cluster_id'])
				echo mkA ("<strong>${hypervisor['cluster_name']}</strong>", 'object', $hypervisor['cluster_id']);
			echo '</td>';
			echo "<td class='tdleft'>${hypervisor['VMs']}</td>";
			echo "</tr>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n";
	}
	else
		echo '<b>No hypervisors exist</b>';
	finishPortlet();

	echo "</td><td class=pcright>";

	$switches = getVMSwitchSummary ();
	startPortlet ('Virtual Switches (' . count ($switches) . ')');
	if (count($switches) > 0)
	{
		echo "<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>\n";
		echo "<tr><th>Name</th></tr>\n";
		$order = 'odd';
		foreach ($switches as $switch)
		{
			echo "<tr class=row_${order} valign=top>";
			echo '<td class="tdleft">' . mkA ("<strong>${switch['name']}</strong>", 'object', $switch['id']) . '</td>';
			echo "</tr>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n";
	}
	else
		echo '<b>No virtual switches exist</b>';
	finishPortlet();

	echo "</td></tr></table>\n";
}

function switchportInfoJS($object_id)
{
	$available_ops = array
	(
		'link' => array ('op' => 'get_link_status', 'gw' => 'getportstatus'),
		'conf' => array ('op' => 'get_port_conf', 'gw' => 'get8021q'),
		'mac' =>  array ('op' => 'get_mac_list', 'gw' => 'getmaclist'),
		'portmac' => array ('op' => 'get_port_mac_list', 'gw' => 'getportmaclist'),
	);
	$breed = detectDeviceBreed ($object_id);
	$allowed_ops = array();
	foreach ($available_ops as $prefix => $data)
		if
		(
			permitted ('object', 'liveports', $data['op']) and
			validBreedFunction ($breed, $data['gw'])
		)
			$allowed_ops[] = $prefix;

	addJS ('js/jquery.thumbhover.js');
	addCSS ('css/jquery.contextmenu.css');
	addJS ('js/jquery.contextmenu.js');
	addJS ("enabled_elements = " . json_encode ($allowed_ops), TRUE);
	addJS ('js/portinfo.js');
}

// Formats VLAN packs: if they are different, the old appears stroken, and the new appears below it
// If comparing the two sets seems being complicated for human, this function generates a diff between old and new packs
function formatVLANPackDiff ($old, $current)
{
	$ret = '';
	$new_pack = serializeVLANPack ($current);
	$new_size = substr_count ($new_pack, ',');
	if (! same8021QConfigs ($old, $current))
	{
		$old_pack = serializeVLANPack ($old);
		$old_size = substr_count ($old_pack, ',');
		$ret .= '<s>' . $old_pack . '</s><br>';
		// make diff
		$added = groupIntsToRanges (array_diff ($current['allowed'], $old['allowed']));
		$removed = groupIntsToRanges (array_diff ($old['allowed'], $current['allowed']));
		if ($old['mode'] == $current['mode'] && $current['mode'] == 'trunk')
		{
			if (! empty ($added))
				$ret .= '<span class="vlan-diff diff-add">+ ' . implode (', ', $added) . '</span><br>';
			if (! empty ($removed))
				$ret .= '<span class="vlan-diff diff-rem">- ' . implode (', ', $removed) . '</span><br>';
		}
	}
	$ret .= $new_pack;
	return $ret;
}

function renderIPAddressLog ($ip_bin)
{
	startPortlet ('Log messages');
	echo '<table class="widetable" cellspacing="0" cellpadding="5" align="center" width="50%"><tr>';
	echo '<th>Date &uarr;</th>';
	echo '<th>User</th>';
	echo '<th>Log message</th>';
	echo '</tr>';
	$odd = FALSE;
	foreach (array_reverse (fetchIPLogEntry ($ip_bin)) as $line)
	{
		$tr_class = $odd ? 'row_odd' : 'row_even';
		echo "<tr class='$tr_class'>";
		echo '<td>' . $line['date'] . '</td>';
		echo '<td>' . $line['user'] . '</td>';
		echo '<td>' . $line['message'] . '</td>';
		echo '</tr>';
		$odd = !$odd;
	}
	echo '</table>';
	finishPortlet();
}

function renderObjectCactiGraphs ($object_id)
{
	function printNewItemTR ($options)
	{
		echo "<table cellspacing=\"0\" align=\"center\" width=\"50%\">";
		echo "<tr><td>&nbsp;</td><th>Server</th><th>Graph ID</th><th>Caption</th><td>&nbsp;</td></tr>\n";
		printOpFormIntro ('add');
		echo "<tr><td>";
		printImageHREF ('Attach', 'Link new graph', TRUE);
		echo '</td><td>' . getSelect ($options, array ('name' => 'server_id'));
		echo "</td><td><input type=text name=graph_id></td><td><input type=text name=caption></td><td>";
		printImageHREF ('Attach', 'Link new graph', TRUE);
		echo "</td></tr></form>";
		echo "</table>";
		echo "<br/><br/>";
	}
	if (!extension_loaded ('curl'))
		throw new RackTablesError ("The PHP cURL extension is not loaded.", RackTablesError::MISCONFIGURED);

	$servers = getCactiServers();
	$options = array();
	foreach ($servers as $server)
		$options[$server['id']] = "${server['id']}: ${server['base_url']}";
	startPortlet ('Cacti Graphs');
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes' && permitted('object','cacti','add'))
		printNewItemTR ($options);
	echo "<table cellspacing=\"0\" cellpadding=\"10\" align=\"center\" width=\"50%\">";
	foreach (getCactiGraphsForObject ($object_id) as $graph_id => $graph)
	{
		$cacti_url = $servers[$graph['server_id']]['base_url'];
		$text = "(graph ${graph_id} on server ${graph['server_id']})";
		echo "<tr><td>";
		echo "<a href='${cacti_url}/graph.php?action=view&local_graph_id=${graph_id}&rra_id=all' target='_blank'>";
		echo "<img src='index.php?module=image&img=cactigraph&object_id=${object_id}&server_id=${graph['server_id']}&graph_id=${graph_id}' alt='${text}' title='${text}'></a></td><td>";
		if(permitted('object','cacti','del'))
			echo getOpLink (array ('op' => 'del', 'server_id' => $graph['server_id'], 'graph_id' => $graph_id), '', 'Cut', 'Unlink graph', 'need-confirmation');
		echo "&nbsp; &nbsp;${graph['caption']}";
		echo "</td></tr>";
	}
	echo '</table>';
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes' && permitted('object','cacti','add'))
		printNewItemTR ($options);
	finishPortlet ();
}

function renderObjectMuninGraphs ($object_id)
{
	function printNewItem ($options)
	{
		echo "<table cellspacing=\"0\" align=\"center\" width=\"50%\">";
		echo "<tr><td>&nbsp;</td><th>Server</th><th>Graph</th><th>Caption</th><td>&nbsp;</td></tr>\n";
		printOpFormIntro ('add');
		echo "<tr><td>";
		printImageHREF ('Attach', 'Link new graph', TRUE);
		echo '</td><td>' . getSelect ($options, array ('name' => 'server_id'));
		echo "</td><td><input type=text name=graph></td><td><input type=text name=caption></td><td>";
		printImageHREF ('Attach', 'Link new graph', TRUE);
		echo "</td></tr></form>";
		echo "</table>";
		echo "<br/><br/>";
	}
	if (!extension_loaded ('curl'))
	{
		showError ('The PHP cURL extension is not loaded.');
		return;
	}
	try
	{
		list ($host, $domain) = getMuninNameAndDomain ($object_id);
	}
	catch (InvalidArgException $e)
	{
		showError ('This object does not have the FQDN or the common name in the host.do.ma.in format.');
		return;
	}

	$servers = getMuninServers();
	$options = array();
	foreach ($servers as $server)
		$options[$server['id']] = "${server['id']}: ${server['base_url']}";
	startPortlet ('Munin Graphs');
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItem ($options);
	echo "<table cellspacing=\"0\" cellpadding=\"10\" align=\"center\" width=\"50%\">";

	foreach (getMuninGraphsForObject ($object_id) as $graph_name => $graph)
	{
		$munin_url = $servers[$graph['server_id']]['base_url'];
		$text = "(graph ${graph_name} on server ${graph['server_id']})";
		echo "<tr><td>";
		echo "<a href='${munin_url}/${domain}/${host}.${domain}/${graph_name}.html' target='_blank'>";
		echo "<img src='index.php?module=image&img=muningraph&object_id=${object_id}&server_id=${graph['server_id']}&graph=${graph_name}' alt='${text}' title='${text}'></a></td>";
		echo "<td>";
		echo getOpLink (array ('op' => 'del', 'server_id' => $graph['server_id'], 'graph' => $graph_name), '', 'Cut', 'Unlink graph', 'need-confirmation');
		echo "&nbsp; &nbsp;${graph['caption']}";
		echo "</td></tr>";
	}
	echo '</table>';
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItem ($options);
	finishPortlet ();
}

function renderEditVlan ($vlan_ck)
{
	global $vtoptions;
	$vlan = getVLANInfo ($vlan_ck);
	startPortlet ('Modify');
	printOpFormIntro ('upd');
	// static attributes
	echo '<table border=0 cellspacing=0 cellpadding=2 align=center>';
	echo '<tr><th class=tdright>Name:</th><td class=tdleft>' .
		"<input type=text size=40 name=vlan_descr value='" . stringForTextInputValue ($vlan['vlan_descr'], 255) . "'>" .
		'</td></tr>';
	echo '<tr><th class=tdright>Type:</th><td class=tdleft>' .
		getSelect ($vtoptions, array ('name' => 'vlan_type'), $vlan['vlan_prop']) .
		'</td></tr>';
	echo '</table>';
	echo '<p>';
	echo '<input type="hidden" name="vdom_id" value="' . $vlan['domain_id'] . '">';
	echo '<input type="hidden" name="vlan_id" value="' . $vlan['vlan_id'] . '">';
	printImageHREF ('SAVE', 'Update VLAN', TRUE);
	echo '</form><p>';
	// get configured ports count
	$portc = 0;
	foreach (getVLANConfiguredPorts ($vlan_ck) as $subarray)
		$portc += count ($subarray);

	$clear_line = '';
	$delete_line = '';
	if ($portc)
	{
		$clear_line .= '<p>';
		$clear_line .= getOpLink (array ('op' => 'clear'), 'remove', 'clear', "remove this VLAN from $portc ports") .
			' this VLAN from ' . mkA ("${portc} ports", 'vlan', $vlan_ck);
	}

	$reason = '';
	if ($vlan['vlan_id'] == VLAN_DFL_ID)
		$reason = "You can not delete default VLAN";
	if (! empty ($reason))
		echo getOpLink (NULL, 'delete VLAN', 'nodestroy', $reason);
	else
		echo getOpLink (array ('op' => 'del', 'vlan_ck' => $vlan_ck), 'delete VLAN', 'destroy', '', 'need-confirmation');
	echo $clear_line;

	finishPortlet();
}

// returns an array with two items - each is HTML-formatted <TD> tag
function formatPortReservation ($port)
{
	$ret = array();
	$ret[] = '<td class=tdleft>' .
		(strlen ($port['reservation_comment']) ? formatLoggedSpan ($port['last_log'], 'Reserved:', 'strong underline') : '').
		'</td>';
	$editable = permitted ('object', 'ports', 'editPort')
		? 'editable'
		: '';
	$ret[] = '<td class=tdleft>' .
		formatLoggedSpan ($port['last_log'], $port['reservation_comment'], "rsvtext $editable id-${port['id']} op-upd-reservation-port") .
		'</td>';
	return $ret;
}

function renderEditUCSForm()
{
	startPortlet ('UCS Actions');
	printOpFormIntro ('autoPopulateUCS');
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo "<tr><th class=tdright><label for=ucs_login>Login:</label></th>";
	echo "<td class=tdleft colspan=2><input type=text name=ucs_login id=ucs_login></td></tr>\n";
	echo "<tr><th class=tdright><label for=ucs_password>Password:</label></th>";
	echo "<td class=tdleft colspan=2><input type=password name=ucs_password id=ucs_password></td></tr>\n";
	echo "<tr><th colspan=3><input type=checkbox name=use_terminal_settings id=use_terminal_settings>";
	echo "<label for=use_terminal_settings>Use Credentials from terminal_settings()</label></th></tr>\n";
	echo "<tr><th class=tdright>Actions:</th><td class=tdleft>";
	printImageHREF ('DQUEUE sync_ready', 'Auto-populate UCS', TRUE);
	echo '</td><td class=tdright>';
	echo getOpLink (array ('op' => 'cleanupUCS'), '', 'CLEAR', 'Clean-up UCS domain', 'need-confirmation');
	echo "</td></tr></table></form>\n";
	finishPortlet();
}

function renderSimpleTableWithOriginViewer ($rows, $column)
{
	echo '<table class=widetable border=0 cellpadding=5 cellspacing=0 align=center>';
	echo "<tr><th>Origin</th><th>Key</th><th>${column['header']}</th></tr>";
	foreach ($rows as $row)
	{
		echo '<tr>';
		echo '<td>';
		if ($row['origin'] == 'default')
			echo getImageHREF ('computer', 'default');
		else
			echo getImageHREF ('favorite', 'custom');
		echo '</td>';
		echo "<td class=tdright>${row[$column['key']]}</td>";
		echo '<td class=tdleft>' . stringForTD ($row[$column['value']], $column['width']) . '</td>';
		echo '</tr>';
	}
	echo '</table>';
}

function renderSimpleTableWithOriginEditor ($rows, $column)
{
	function printNewitemTR ($column)
	{
		printOpFormIntro ('add');
		echo '<tr>';
		echo '<td>&nbsp;</td>';
		echo '<td class=tdleft>' . getImageHREF ('create', 'create new', TRUE) . '</td>';
		echo "<td><input type=text size=${column['width']} name=${column['value']}></td>";
		echo '<td class=tdleft>' . getImageHREF ('create', 'create new', TRUE) . '</td>';
		echo '</tr></form>';
	}
	echo '<table class=widetable border=0 cellpadding=5 cellspacing=0 align=center>';
	echo "<tr><th>Origin</th><th>&nbsp;</th><th>${column['header']}</th><th>&nbsp;</th></tr>";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewitemTR ($column);
	foreach ($rows as $row)
	{
		echo '<tr>';
		if ($row['origin'] == 'default')
		{
			echo '<td>' . getImageHREF ('computer', 'default') . '</td>';
			echo '<td>&nbsp;</td>';
			echo '<td>' . stringForTD ($row[$column['value']], $column['width']) . '</td>';
			echo '<td>&nbsp;</td>';
		}
		else
		{
			printOpFormIntro ('upd', array ($column['key'] => $row[$column['key']]));
			echo '<td>' . getImageHREF ('favorite', 'custom') . '</td>';
			echo '<td>';
			if (array_key_exists ('refc', $row) && $row['refc'] > 0)
				echo getImageHREF ('nodestroy', "referenced ${row['refc']} times");
			else
				echo getOpLink (array ('op' => 'del', $column['key'] => $row[$column['key']]), '', 'destroy', 'remove');
			echo '</td>';
			echo "<td><input type=text size=${column['width']} name=${column['value']} value='" . stringForTextInputValue ($row[$column['value']], $column['width']) . "'></td>";
			echo '<td>' . getImageHREF ('save', 'Save changes', TRUE) . '</td>';
			echo '</form>';
		}
		echo '</tr>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewitemTR ($column);
	echo '</table>';
}

?>
