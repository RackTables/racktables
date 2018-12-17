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
	'sharedrouter' => 'Shared router',
	'point2point' => 'Point-to-point',
);
// address allocation code, IPv4 addresses and objects view
$aac_right = array
(
	'regular' => '',
	'virtual' => '<span class="aac-right" title="' . $aat['virtual'] . '">L</span>',
	'shared' => '<span class="aac-right" title="' . $aat['shared'] . '">S</span>',
	'router' => '<span class="aac-right" title="' . $aat['router'] . '">R</span>',
	'sharedrouter' => '<span class="aac-right" title="' . $aat['sharedrouter'] . '">R</span>',
	'point2point' => '<span class="aac-right" title="' . $aat['point2point'] . '">P</span>',
);
// address allocation code, IPv4 networks view
$aac_left = array
(
	'regular' => '',
	'virtual' => '<span class="aac-left" title="' . $aat['virtual'] . '">L:</span>',
	'shared' => '<span class="aac-left" title="' . $aat['shared'] . '">S:</span>',
	'router' => '<span class="aac-left" title="' . $aat['router'] . '">R:</span>',
	'sharedrouter' => '<span class="aac-left" title="' . $aat['sharedrouter'] . '">R:</span>',
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
	$https = (isset ($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '';
	$port = (! in_array ($_SERVER['SERVER_PORT'], array (80, 443))) ? ':' . $_SERVER['SERVER_PORT'] : '';
	printf ('http%s://logout@%s%s%s?logout', $https, $_SERVER['SERVER_NAME'], $port, $_SERVER['PHP_SELF']);
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
	global $remote_displayname;
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
	echo '<head><title>' . getTitle ($pageno) . '</title>';
	printPageHeaders();
	echo '</head>';
	echo '<body>';
	echo '<div class=maintable>';
	echo '<div class=mainheader>';
	echo '<div style="float: right" class=greeting>';
	echo mkA ($remote_displayname, 'myaccount', NULL, 'default');
	echo ' [ <a href="';
	showLogoutURL();
	echo '">logout</a> ]</div>'; // greeting
	echo getConfigVar ('enterprise') . ' RackTables ';
	echo '<a href="http://racktables.org" title="Visit RackTables site">' . CODE_VERSION . '</a>';
	renderQuickLinks();
	echo '</div>'; // mainheader
	echo '<div class=menubar>';
	showPathAndSearch ($pageno, $tabno);
	echo '</div>';
	echo '<div class=tabbar>';
	showTabs ($pageno, $tabno);
	echo '</div>';
	echo '<div class=msgbar>';
	showMessageOrError();
	echo '</div>';
	echo "<div class=pagebar>${payload}</div>";
	echo '</div>'; // maintable
	echo '</body>';
	echo '</html>';
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
	echo '<table border=0 cellpadding=0 cellspacing=0 width="100%">';
	echo '<tr><td><div style="text-align: center; margin: 10px; ">';
	echo '<table width="100%" cellspacing=0 cellpadding=20 class=mainmenu border=0>';
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
	echo '</table>';
	echo '</div></td></tr>';
	echo '</table>';
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
	if ($alloc['addrinfo']['name'] != '')
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

	addJSText (<<<'END'
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
	); // addJSText()
	startPortlet ('Location filter');
	echo <<<'END'
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
	global $pageno;
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
					$location_id = $row['location_id'];

					if (
						$location_id != '' && isset ($_SESSION['locationFilter']) && ! in_array ($location_id, $_SESSION['locationFilter']) ||
						empty ($rackList) && ! $cellfilter['is_empty']
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
							if ($rackListIdx > 0 && $maxPerRow > 0 && $rackListIdx % $maxPerRow == 0)
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
	echo "</td><td class='pcright ${pageno}' width='25%'>";
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
	addJSText (<<<'JSTXT'
	function locationeditor_showselectbox(e) {
		$(this).load('index.php', {module: 'ajax', ac: 'get-location-select', locationid: this.id});
		$(this).unbind('mousedown', locationeditor_showselectbox);
	}
	$(document).ready(function () {
		$('select.locationlist-popup').bind('mousedown', locationeditor_showselectbox);
	});
JSTXT
	); // addJSText()

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
		echo '</td><td>&nbsp;';
		echo '</td><td>&nbsp;</td></tr></form>';
	}
	startPortlet ('Rows');
	echo "<table border=0 cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>&nbsp;</th><th># Racks</th><th># Devices</th><th>Location</th><th>Name</th><th>&nbsp;</th><th>&nbsp;</th><th>Row link</th></tr>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ();
	foreach (listCells ('row') as $row_id => $rowInfo)
	{
		echo '<tr><td>';
		$rc = $rowInfo['rackc'];
		$delete_racks_str = $rc ? " and $rc rack(s)" : '';
		echo getOpLink (array ('op'=>'deleteRow', 'row_id'=>$row_id), '', 'destroy', 'Delete row'.$delete_racks_str, 'need-confirmation');
		printOpFormIntro ('updateRow', array ('row_id' => $row_id));
		echo '</td><td class=tdright>';
		echo $rc;
		echo '</td><td class=tdright>';
		echo getRowMountsCount ($row_id);
		echo '</td><td>';
		renderLocationSelectTree ('location_id', $rowInfo['location_id']);
		echo "</td><td><input type=text name=name value='${rowInfo['name']}'></td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</form></td>";
		echo '<td>&nbsp;</td>';
		echo '<td class=tdleft>' . mkCellA ($rowInfo) . '</td>';
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
			$record['value'] != '' &&
			permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $record['id'])))
		)
			$summary['{sticker}' . $record['name']] = formatAttributeValue ($record, 1561);

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

function renderEditAttributeTRs ($update_op, $values, $objtype_id, $skip_ids = array())
{
	$datehint = ' (' . datetimeFormatHint (getConfigVar ('DATETIME_FORMAT')) . ')';
	$i = 0;
	foreach ($values as $record)
	{
		$annex = array (array ('tag' => '$attr_' . $record['id']));
		$can_view = permitted (NULL, NULL, NULL, $annex);
		if (in_array ($record['id'], $skip_ids) || ! $can_view)
			continue;
		$can_update = permitted (NULL, NULL, $update_op, $annex);
		$can_clear = permitted (NULL, NULL, 'clearSticker', $annex);
		// Ability to update ultimately includes ability to set to an empty value,
		// i.e. to clear, but making the check this way in the ophandler is complicated,
		// so let's keep it consistently imperfect for the time being and maybe
		// fix it later.
		$clear_html = ($record['value'] != '' && ($can_clear /* || $can_update*/)) ?
			getOpLink (array ('op' => 'clearSticker', 'attr_id' => $record['id']), '', 'clear', 'Clear value', 'need-confirmation') :
			'&nbsp;';
		echo "<tr><td>${clear_html}</td>";
		echo '<th class=sticker>' . $record['name'] . ($record['type'] == 'date' ? $datehint : '') . ':</th>';
		echo '<td class=tdleft>';
		switch ($record['type'])
		{
			case 'uint':
			case 'float':
			case 'string':
				$ro_or_rw = $can_update ? "name=${i}_value" : 'disabled';
				echo "<input type=text ${ro_or_rw} value='${record['value']}'>";
				break;
			case 'date':
				$ro_or_rw = $can_update ? "name=${i}_value" : 'disabled';
				$date_value = $record['value'] ? datetimestrFromTimestamp ($record['value']) : '';
				echo "<input type=text ${ro_or_rw} value='${date_value}'>";
				break;
			case 'dict':
				$ro_or_rw = $can_update ? array ('name' => "${i}_value") : array ('name' => "${i}_value", 'disabled' => 1);
				$chapter = readChapter ($record['chapter_id'], 'o');
				$chapter[0] = '-- NOT SET --';
				$chapter = cookOptgroups ($chapter, $objtype_id, $record['key']);
				printNiftySelect ($chapter, $ro_or_rw, $record['key']);
				break;
			default:
				throw new InvalidArgException ('record[type]', $record['type']);
		} // switch
		if ($can_update)
		{
			echo "<input type=hidden name=${i}_attr_id value=${record['id']}>";
			$i++;
		}
		echo '</td></tr>';
	} // foreach
	echo "<input type=hidden name=num_attrs value=${i}>";
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
	renderEditAttributeTRs ('updateRow', getAttrValuesSorted ($row_id), 1561);
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
	if ($objectData['asset_no'] != '')
		$prefix = "<div title='${objectData['asset_no']}";
	else
		$prefix = "<div title='no asset tag";
	// Don't tell about label, if it matches common name.
	$body = '';
	if ($objectData['name'] != $objectData['label'] && $objectData['label'] != '')
		$body = ", visible label is \"${objectData['label']}\"";
	// Display list of child objects, if any
	$objectChildren = getChildren ($objectData, 'object');
	$slotRows = $slotCols = $slotInfo = $slotData = $slotTitle = $slotClass = array ();
	if (! count ($objectChildren))
		$suffix = "'>";
	else
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
				if ($childData['asset_no'] != '')
					$slotTitle[$slot] = "<div title='${childData['asset_no']}";
				else
					$slotTitle[$slot] = "<div title='no asset tag";
				if ($childData['label'] != '' && $childData['label'] != $childData['dname'])
					$slotTitle[$slot] .= ", visible label is \"${childData['label']}\"";
				$slotTitle[$slot] .= "'>";
				$slotClass[$slot] = 'state_T';
				if ($childData['id'] == $hl_obj_id)
					$slotClass[$slot] .= 'h';
				if ($childData['has_problems'] == 'yes')
					$slotClass[$slot] .= 'w';

				$child = spotEntity ('object', $childData['id']);
				setEntityColors ($child);
				$class_context = $childData['id'] == $hl_obj_id ? 'atom_selected' : 'atom_plain';
				$slotClass[$slot] .= getCellClass ($child, $class_context);

			}
		}
		natsort($childNames);
		$suffix = sprintf(", contains %s'>", implode(', ', $childNames));
	}
	echo "${prefix}${body}${suffix}" . mkCellA ($objectData) . '</div>';
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
									$tmp = mb_substr($slotInfo[$s], 0, 1);
									for($i = 1; $i < mb_strlen($slotInfo[$s]); $i++)
									{
										$tmp .= '<br>' . mb_substr($slotInfo[$s], $i, 1);
									}
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
	setEntityColors ($rackData);
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
	$reverse = considerConfiguredConstraint ($rackData, 'REVERSED_RACKS_LISTSRC');
	for ($i = $rackData['height']; $i > 0; $i--)
	{
		echo '<tr><th>' . inverseRackUnit ($rackData['height'], $i, $reverse) . '</th>';
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			if (isset ($rackData[$i][$locidx]['skipped']))
				continue;
			$state = $rackData[$i][$locidx]['state'];

			$class = "atom state_${state}";

			if (isset ($rackData[$i][$locidx]['hl']))
				$class .= $rackData[$i][$locidx]['hl'];

			if($state == 'T')
			{
				$objectData = spotEntity ('object', $rackData[$i][$locidx]['object_id']);
				setEntityColors ($objectData);
				$class_context = $rackData[$i][$locidx]['object_id'] == $hl_obj_id ? 'atom_selected' : 'atom_plain';
				$class .= getCellClass ($objectData, $class_context);
			}

			echo "<td class='${class}'";

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

			$class = "atom state_${state}";
			setEntityColors ($zeroUObject);
			$class_context = $zeroUObject['id'] == $hl_obj_id ? 'atom_selected' : 'atom_plain';
			$class .= getCellClass ($zeroUObject, $class_context);

			echo "<tr><td class='${class}'>";
			printObjectDetailsForRenderRack ($zeroUObject['id']);
			echo "</td></tr>\n";
		}
		echo "</table>\n";
	}
	echo "</center>\n";
}

function renderRackSortForm ($row_id)
{
	includeJQueryUIJS();
	// Heredoc, not nowdoc!
	$js = <<<"JSTXT"
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
	addJSText ($js);

	startPortlet ('Racks');
	echo "<table border=0 cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo "<tr><th>Drag to change order</th></tr>\n";
	echo "<tr><td class=tdleft><ul class='uflist' id='sortRacks'>\n";
	foreach (getRacks($row_id) as $rack_id => $rackInfo)
		echo "<li id=racks_${rack_id}>${rackInfo['name']}</li>\n";
	echo "</ul></td></tr></table>\n";
	finishPortlet();
}

function renderNewRackForm()
{
	$default_height = emptyStrIfZero (getConfigVar ('DEFAULT_RACK_HEIGHT'));
	startPortlet ('Add one');
	printOpFormIntro ('addRack', array ('mode' => 'one'));
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
	printOpFormIntro ('addRack', array ('mode' => 'many'));
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
	renderEditAttributeTRs ('update', getAttrValuesSorted ($object_id), $object['objtype_id']);
	echo '<tr><td>&nbsp;</td><th class=tdright><label for=object_has_problems>Has problems:</label></th>';
	echo '<td class=tdleft><input type=checkbox name=object_has_problems id=object_has_problems';
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
	renderEditAttributeTRs ('updateRack', getAttrValuesSorted ($rack_id), 1560, array (27, 29));
	echo '<tr><td>&nbsp;</td><th class=tdright><label for=has_problems>Has problems:</label></th>';
	echo '<td class=tdleft><input type=checkbox name=has_problems id=has_problems';
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
	if ($rackData['asset_no'] != '')
		$summary['Asset tag'] = $rackData['asset_no'];
	if ($rackData['has_problems'] == 'yes')
		$summary[] = array ('<tr><td colspan=2 class=msg_error>Has problems</td></tr>');
	populateRackPower ($rackData, $summary);
	// Display populated attributes, but skip 'height' since it's already displayed above
	// and skip 'sort_order' because it's modified using AJAX
	foreach (getAttrValuesSorted ($rackData['id']) as $record)
		if
		(
			$record['id'] != 27 && $record['id'] != 29 &&
			$record['value'] != '' &&
			permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $record['id'])))
		)
			$summary['{sticker}' . $record['name']] = formatAttributeValue ($record, 1560);
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
	includeJQueryUIJS();
	addJSInternal ('js/racktables.js');
	$table_id = 'selectableRack';
	addBulkSelectorJS ($table_id);
	echo "<center>\n";
	$read_only_text = $is_ro ? '(read-only)' : '&nbsp;';
	echo "<p style='color: red; margin-top:0px'>${read_only_text}</p>\n";
	echo "<table class=rack id={$table_id} border=0 cellspacing=0 cellpadding=1>\n";
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

function renderObjectPortHeaderRow()
{
	// Renders the headers for the ports table on the default page

	echo '<tr><th class=tdleft>Local name</th><th class=tdleft>Visible label</th>';
	echo '<th class=tdleft>Interface</th><th class=tdleft>L2 address</th>';
	echo '<th class=tdcenter colspan=2>Remote object and port</th>';
	echo '<th class=tdleft>Cable ID</th></tr>';
}

function renderObjectPortRow ($port, $is_highlighted)
{
	// highlight port name with yellow if its name is not canonical
	$canon_pn = shortenPortName ($port['name'], $port['object_id']);
	$name_class = $canon_pn == $port['name'] ? '' : 'trwarning';

	echo '<tr';
	if ($is_highlighted)
		echo ' class=highlight';
	$a_class = isEthernetPort ($port) ? 'port-menu' : '';
	echo "><td class='tdleft $name_class' NOWRAP><a name='port-${port['id']}' class='interactive-portname nolink $a_class'>${port['name']}</a></td>";
	echo "<td class=tdleft>${port['label']}</td>";
	echo "<td class=tdleft>" . formatPortIIFOIF ($port) . "</td><td class='tdleft l2address'>${port['l2address']}</td>";
	if ($port['remote_object_id'])
	{
		$dname = formatObjectDisplayedName ($port['remote_object_name'], $port['remote_object_tid']);
		echo "<td class=tdleft>" .
			formatPortLink ($port['remote_object_id'], $dname, $port['remote_id'], NULL) .
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
	// A mandatory left column with varying number of portlets.
	echo "<tr><td class=pcleft>";

	// display summary portlet
	$summary = array();
	if ($info['name'] != '')
		$summary['Common name'] = $info['name'];
	elseif (considerConfiguredConstraint ($info, 'NAMEWARN_LISTSRC'))
		$summary[] = array ('<tr><td colspan=2 class=msg_error>Common name is missing.</td></tr>');
	$summary['Object type'] = '<a href="' . makeHref (array (
		'page' => 'depot',
		'tab' => 'default',
		'cfe' => '{$typeid_' . $info['objtype_id'] . '}'
	)) . '">' .  decodeObjectType ($info['objtype_id']) . '</a>';
	if ($info['label'] != '')
		$summary['Visible label'] = $info['label'];
	if ($info['asset_no'] != '')
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
		$summary["Contains " . mb_strtolower(decodeObjectType ($objtype_id))] = implode ('<br>', $fmt_children);
	}
	if ($info['has_problems'] == 'yes')
		$summary[] = array ('<tr><td colspan=2 class=msg_error>Has problems</td></tr>');
	foreach (getAttrValuesSorted ($object_id) as $record)
		if
		(
			$record['value'] != '' &&
			permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $record['id'])))
		)
			$summary['{sticker}' . $record['name']] = formatAttributeValue ($record, $info['objtype_id']);
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

	if ($info['comment'] != '')
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
			genericAssertion ('hl_port_id', 'natural');
			$hl_port_id = $_REQUEST['hl_port_id'];
			addAutoScrollScript ("port-$hl_port_id");
		}
		echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>";
		callHook ('renderObjectPortHeaderRow');
		foreach ($info['ports'] as $port)
			callHook ('renderObjectPortRow', $port, ($hl_port_id == $port['id']));
		if (permitted (NULL, 'ports', 'set_reserve_comment'))
			addJSInternal ('js/inplace-edit.js');
		echo "</table><br>";
		finishPortlet();
	}

	if (count ($info['ipv4']) + count ($info['ipv6']))
	{
		startPortlet ('IP addresses');
		echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
		if ('yes' == $ext_ipv4_view = getConfigVar ('EXT_IPV4_VIEW'))
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
				if ($ext_ipv4_view == 'yes')
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
	if (count ($forwards['in']) || count ($forwards['out']))
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
				echo "<td class=tdleft>${pf['proto']}</td><td class=tdleft>${osif}" . getRenderedIPPortPair ($pf['localip'], $pf['localport']) . "</td>";
				echo "<td class=tdleft>" . getRenderedIPPortPair ($pf['remoteip'], $pf['remoteport']) . "</td>";
				$address = getIPAddress (ip4_parse ($pf['remoteip']));
				echo "<td class='description tdleft'>";
				if (count ($address['allocs']))
					foreach($address['allocs'] as $bond)
						echo mkA ("${bond['object_name']}(${bond['name']})", 'object', $bond['object_id']) . ' ';
				elseif ($pf['remote_addr_name'] != '')
					echo '(' . $pf['remote_addr_name'] . ')';
				echo "</td><td class='description tdleft'>${pf['description']}</td></tr>";
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
				echo "<td class=tdleft>${pf['proto']}/" . getRenderedIPPortPair ($pf['localip'], $pf['localport']) . "</td>";
				echo '<td class="description tdleft">' . mkA ($pf['object_name'], 'object', $pf['object_id']);
				echo "</td><td class=tdleft>" . getRenderedIPPortPair ($pf['remoteip'], $pf['remoteport']) . "</td>";
				echo "<td class='description tdleft'>${pf['description']}</td></tr>";
			}
			echo "</table><br><br>";
		}
		finishPortlet();
	}

	renderSLBTriplets2 ($info);
	renderSLBTriplets ($info);
	echo "</td>\n";

	// A conditional right column with the rackspace portlet only.
	if
	(
		! in_array ($info['objtype_id'], $virtual_obj_types) &&
		count ($rack_ids = getResidentRackIDs ($object_id))
	)
	{
		echo '<td class=pcright>';
		startPortlet ('rackspace allocation');
		foreach ($rack_ids as $rack_id)
			renderRack ($rack_id, $object_id);
		echo '<br>';
		finishPortlet();
		echo '</td>';
	}
	echo "<tr></table>\n";
}

function renderRackMultiSelect ($sname, $racks, $selected)
{
	// Transform the given flat list into a list of groups, each representing a rack row.
	$rdata = array();
	foreach ($racks as $rack)
	{
		if ('' != $trail = getLocationTrail ($rack['location_id'], FALSE))
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
		echo "<td><input type=text name=port_l2address size=17 maxlength=59></td>\n";
		echo "<td colspan=4>&nbsp;</td><td>";
		printImageHREF ('add', 'add a port', TRUE);
		echo "</td></tr></form>";
	}

	function printBulkForm ($prefs)
	{
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

	startPortlet ('Ports and interfaces');
	$object = spotEntity ('object', $object_id);
	amplifyCell ($object);
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes' && getConfigVar ('ENABLE_BULKPORT_FORM') == 'yes')
		printBulkForm ($prefs);

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
		echo '<p>' . getOpLink (array ('op'=>'renameAll'), "Auto-rename $n_ports_to_rename port(s)", 'recalc', 'Use RackTables naming convention for this device type') . '</p>';

	if (isset ($_REQUEST['hl_port_id']))
	{
		genericAssertion ('hl_port_id', 'natural');
		$hl_port_id = intval ($_REQUEST['hl_port_id']);
		addAutoScrollScript ("port-$hl_port_id");
	}
	switchportInfoJS ($object_id); // load JS code to make portnames interactive
	foreach ($object['ports'] as $port)
	{
		// highlight port name with yellow if its name is not canonical
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
		echo '<td class=tdleft>';
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

		// 17 is the full notation length of a MAC address, 23 -- of a WWN address and 59 -- of an IPoIB address.
		echo "<td><input type=text name=l2address value='${port['l2address']}' size=17 maxlength=59></td>\n";
		if ($port['remote_object_id'])
		{
			$dname = formatObjectDisplayedName ($port['remote_object_name'], $port['remote_object_tid']);
			echo "<td class=tdleft>" .
				formatLoggedSpan ($port['last_log'], formatPortLink ($port['remote_object_id'], $dname, $port['remote_id'], NULL)) .
				"</td>";
			echo "<td class=tdleft> " . formatLoggedSpan ($port['last_log'], $port['remote_name'], 'underline') .
				"<input type=hidden name=reservation_comment value=''></td>";
			echo "<td><input type=text name=cable value='${port['cableid']}'></td>";
			echo "<td class=tdcenter>";
			echo getOpLink (array('op'=>'unlinkPort', 'port_id'=>$port['id'], ), '', 'cut', 'Unlink this port');
			echo "</td>";
		}
		elseif ($port['reservation_comment'] != '')
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
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes' && getConfigVar ('ENABLE_BULKPORT_FORM') == 'yes')
		printBulkForm ($prefs);
	finishPortlet();

	if (getConfigVar ('ENABLE_MULTIPORT_FORM') == 'yes' && permitted (NULL, NULL, 'addMultiPorts'))
	{
		startPortlet ('Add/update multiple ports');
		printOpFormIntro ('addMultiPorts');
		$formats = array
		(
			'ssv1' => 'SSV:<interface name> [<MAC address>]',
		);
		echo 'Format: ' . getSelect ($formats, array ('name' => 'format'), 'ssv1') . ' ';
		echo 'Default port type: ';
		printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type'), $prefs['selected']);
		echo "<input type=submit value='Parse output'><br>\n";
		echo "<textarea name=input cols=100 rows=50></textarea><br>\n";
		echo '</form>';
		finishPortlet();
	}
}

function renderIPForObject ($object_id)
{
	function printNewItemTR ($default_type, $object_id)
	{
		global $aat;

		includeJQueryUIJS();
		includeJQueryUICSS();

		// Heredoc, not nowdoc!
		addJSText (<<<"JSEND"
			$(document).ready( function() {
				$('[name="bond_name"]').autocomplete({
					source: "?module=ajax&ac=autocomplete&realm=bond_name&object_id=$object_id",
					//minLength: 3,
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
		); // addJSText()
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
	$ext_ipv4_view = getConfigVar ('EXT_IPV4_VIEW');
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
		if ($ext_ipv4_view == 'yes')
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
	$most_popular_type = ! count ($used_alloc_types) ? 'regular' : array_last (array_keys ($used_alloc_types));

	if ($list_on_top = (getConfigVar ('ADDNEW_AT_TOP') != 'yes'))
		echo $alloc_list;
	printNewItemTR ($most_popular_type, $object_id);
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
		10 => array ('code' => 'success', 'format' => 'Added %u port(s), updated %u port(s), encountered %u error(s).'),
		21 => array ('code' => 'success', 'format' => 'Generation complete'),
		26 => array ('code' => 'success', 'format' => 'updated %u record(s) successfully'),
		37 => array ('code' => 'success', 'format' => 'added %u record(s) successfully'),
		38 => array ('code' => 'success', 'format' => 'removed %u record(s) successfully'),
		43 => array ('code' => 'success', 'format' => 'Saved successfully.'),
		44 => array ('code' => 'success', 'format' => '%s failure(s) and %s successfull change(s).'),
		48 => array ('code' => 'success', 'format' => 'added a record successfully'),
		49 => array ('code' => 'success', 'format' => 'deleted a record successfully'),
		51 => array ('code' => 'success', 'format' => 'updated a record successfully'),
		57 => array ('code' => 'success', 'format' => 'Reset complete'),
		58 => array ('code' => 'success', 'format' => '%u device(s) unmounted successfully'),
		63 => array ('code' => 'success', 'format' => '%u change request(s) have been processed'),
		67 => array ('code' => 'success', 'format' => "Tag rolling done, %u object(s) involved"),
		71 => array ('code' => 'success', 'format' => 'File "%s" was linked successfully'),
		72 => array ('code' => 'success', 'format' => 'File was unlinked successfully'),
		82 => array ('code' => 'success', 'format' => "Bulk port creation was successful. %u port(s) created, %u failed"),
		87 => array ('code' => 'success', 'format' => '802.1Q recalculate: %d port(s) changed on %d switch(es)'),
// records 100~199 with fatal error messages
		100 => array ('code' => 'error', 'format' => '%s'),
		109 => array ('code' => 'error', 'format' => 'failed updating a record'),
		131 => array ('code' => 'error', 'format' => 'invalid format requested'),
		141 => array ('code' => 'error', 'format' => 'Encountered %u error(s), updated %u record(s)'),
		149 => array ('code' => 'error', 'format' => 'Turing test failed'),
		150 => array ('code' => 'error', 'format' => 'Can only change password under DB authentication.'),
		151 => array ('code' => 'error', 'format' => 'Old password doesn\'t match.'),
		152 => array ('code' => 'error', 'format' => 'New passwords don\'t match.'),
		154 => array ('code' => 'error', 'format' => "Verification error: %s"),
		155 => array ('code' => 'error', 'format' => 'Save failed.'),
		159 => array ('code' => 'error', 'format' => 'Permission denied moving port %s from VLAN%u to VLAN%u'),
		170 => array ('code' => 'error', 'format' => 'There is no network for IP address "%s"'),
		172 => array ('code' => 'error', 'format' => 'Malformed request'),
		179 => array ('code' => 'error', 'format' => 'Expired form has been declined.'),
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
		if (! isset ($record['c']) || ! isset ($msginfo[$record['c']]))
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
		echo "<table width='80%' class='widetable' cellspacing=0 cellpadding='5px' align='center'>";
		echo '<tr><th>Port</th><th>&nbsp;</th><th>Link status</th><th>Link info</th></tr>';
		$statusmap = array
		(
			'up' => 'link up',
			'down' => 'link down',
			'disabled' => 'link disabled',
		);
		$order = 'even';
		foreach ($linkStatus as $pn => $link)
		{
			echo "<tr class='row_$order'>";
			$order = $nextorder[$order];
			echo '<td>' . $pn . '</td>';
			echo '<td>' . getImageHREF (array_fetch ($statusmap, $link['status'], '16x16t')) . '</td>';
			echo '<td>' . $link['status'] . '</td>';
			$info = '';
			if (isset ($link['speed']))
				$info .= $link['speed'];
			if (isset ($link['duplex']))
			{
				if ($info != '')
					$info .= ', ';
				$info .= $link['duplex'];
			}
			echo '<td>' . $info . '</td>';
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
		$rendered_macs .= "<table width='80%' class='widetable' cellspacing=0 cellpadding='5px' align='center'>";
		$rendered_macs .= '<tr><th>MAC address</th><th>VLAN</th><th>Port</th></tr>';
		$order = 'even';
		foreach ($macList as $pn => $list)
		{
			$order = $nextorder[$order];
			foreach ($list as $item)
			{
				++$mac_count;
				$rendered_macs .= "<tr class='row_$order'>";
				$rendered_macs .= '<td style="font-family: monospace">' . $item['mac'] . '</td>';
				$rendered_macs .= '<td>' . $item['vid'] . '</td>';
				$rendered_macs .= '<td>' . $pn . '</td>';
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

function addBulkSelectorJS ($element_id)
{
	// Heredoc, not nowdoc!
	addJSText (<<<"ENDOFJAVASCRIPT"
$(function () {
    $("#{$element_id} tbody").selectable({
        filter: 'td.atom',
        cancel: 'th,a',
        stop: function () {
            $(".ui-selected input:enabled", this).each(function () {
                this.checked = !this.checked
            });
        }
    });
});
ENDOFJAVASCRIPT
	); // addJSText()
}

// An object can be mounted onto free atoms only, that is, if any record for an atom
// already exists in RackSpace, it cannot be used for mounting.
function renderRackSpaceForObject ($object_id)
{
	// Always process occupied racks plus racks chosen by user. First get racks with
	// already allocated rackspace...
	$workingRacksData = array();
	foreach (getResidentRackIDs ($object_id) as $rack_id)
	{
		$rackData = spotEntity ('rack', $rack_id);
		amplifyCell ($rackData);
		$workingRacksData[$rack_id] = $rackData;
	}
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
	if (! isset ($_REQUEST['show_all_racks']) && getConfigVar ('FILTER_RACKLIST_BY_TAGS') == 'yes')
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
		if (count ($matching_racks) && count ($matching_racks) != count ($allRacksData))
		{
			$tmp = array();
			foreach ($matched_tags as $tag)
				$tmp[] = '{' . $tag['tag'] . '}';
			$filter_text = implode (' or ', $tmp);
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
	echo '<textarea name=comment rows=10 cols=40></textarea><br><br>';
	echo "<input type=submit value='Save' name=got_atoms>\n";
	echo "<br><br>";
	finishPortlet();
	echo "</td>";

	// Right portlet with rendered racks. If this form submit is not final,
	// the former state of the grid needs to make it to the current form.
	echo "<td class=pcright rowspan=2 height='1%'>";
	startPortlet ('Working copy');
	includeJQueryUIJS();
	addJSInternal ('js/racktables.js');
	echo '<table border=0 cellspacing=10 align=center><tr>';
	foreach ($workingRacksData as $rack_id => $rackData)
	{
		$table_id = "selectableRack_{$rack_id}";
		addBulkSelectorJS ($table_id);
		$is_ro = !rackModificationPermitted ($rackData, 'updateObjectAllocation');
		// Order is important here: only original allocation is highlighted.
		highlightObject ($rackData, $object_id);
		markupAtomGrid ($rackData, 'T');
		// If an HTTP form has been processed, discard user input and show new database
		// contents.
		if (!$is_ro && isset ($_REQUEST['rackmulti'][0])) // is an update
			mergeGridFormToRack ($rackData);
		echo '<td valign=bottom>';
		echo '<center><h2 style="margin:0px">';
		echo mkA ($rackData['row_name'], 'row', $rackData['row_id']);
		echo ' : ';
		echo mkA ($rackData['name'], 'rack', $rackData['id']);
		echo '</h2>';
		$read_only_text = $is_ro ? '(read-only)' : '&nbsp;';
		echo "<p style='color: red; margin-top:0px'>${read_only_text}</p>\n";
		echo "<table class=rack id={$table_id} border=0 cellspacing=0 cellpadding=1>\n";
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
		echo "<label>Zero-U: <input type=checkbox ${checked} name=zerou_${rack_id}${disabled_text}></label>\n<br><br>\n";
		echo "<input type='button' onclick='uncheckAllAtoms({$rack_id}, {$rackData['height']});' value='Uncheck all'>\n";
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
	// Now there are some racks to render.
	foreach ($rackpack as $rackData)
	{
		echo "<table class=molecule cellspacing=0>\n";
		echo "<caption>${rackData['name']}</caption>\n";
		echo "<tr><th width='10%'>&nbsp;</th><th width='20%'>Front</th><th width='50%'>Interior</th><th width='20%'>Back</th></tr>\n";
		$reverse = considerConfiguredConstraint ($rackData, 'REVERSED_RACKS_LISTSRC');
		for ($i = $rackData['height']; $i > 0; $i--)
		{
			echo '<tr><th>' . inverseRackUnit ($rackData['height'], $i, $reverse) . '</th>';
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

				setEntityColors ($obj);
				$class = getCellClass ($obj, 'list_plain');

				echo "<tr class='row_${order} tdleft ${problem}${class}' valign=top><td>" . mkA ("<strong>${obj['dname']}</strong>", 'object', $obj['id']);
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

	echo "</td><td class='pcright ${pageno}' width='25%'>";

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
	$max = getConfigVar('MAX_UNFILTERED_ENTITIES');
	if (0 == $max || $count <= $max)
		return FALSE;

	$href_show_all = trim($_SERVER['REQUEST_URI'], '&');
	$href_show_all .= htmlspecialchars('&show_all_objects=1');
	$suffix = isset ($count) ? " ($count)" : '';
	// Heredoc, not nowdoc!
	echo <<<"END"
<p>Please set a filter to display the corresponging $entities_name.
<br><a href="$href_show_all">Show all $entities_name$suffix</a>
END;
	return TRUE;
}

// History viewer for history-enabled simple dictionaries.
function renderObjectHistory ($object_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT ctime, user_name, name, label, asset_no, has_problems, comment FROM ObjectHistory WHERE id=? ORDER BY ctime',
		array ($object_id)
	);
	$columns = array
	(
		array ('th_text' => 'Change time', 'row_key' => 'ctime'),
		array ('th_text' => 'Author', 'row_key' => 'user_name'),
		array ('th_text' => 'Name', 'row_key' => 'name'),
		array ('th_text' => 'Visible label', 'row_key' => 'label'),
		array ('th_text' => 'Asset tag', 'row_key' => 'asset_no'),
		array ('th_text' => 'Has problems?', 'row_key' => 'has_problems'),
		array ('th_text' => 'Comment', 'row_key' => 'comment'),
	);
	renderTableViewer ($columns, $result->fetchAll (PDO::FETCH_ASSOC));
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
	$display_routers = (getConfigVar ('IPV4_TREE_RTR_AS_CELL') != 'none');

	// scroll page to the highlighted item
	if ($target && isset ($_REQUEST['hl_net']))
		addAutoScrollScript ("net-$target");

	foreach ($tree as $item)
	{
		if (isset ($item['id']))
		{
			$decor = array ('indent' => $level);
			if ($item['symbol'] == 'node-collapsed')
				$decor['symbolurl'] = "${baseurl}&eid=${item['id']}&hl_net=1";
			elseif ($item['symbol'] == 'node-expanded')
				$decor['symbolurl'] = $baseurl . ($item['parent_id'] ? "&eid=${item['parent_id']}&hl_net=1" : '');

			setEntityColors ($item);
			$class_context = ($target == $item['id'] && isset ($_REQUEST['hl_net'])) ? 'list_selected' : 'list_plain';
			$tr_class = getCellClass ($item, $class_context);
			// Use old-style highlighting for colourless networks.
			if ($class_context == 'list_selected' && $tr_class == '')
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
			if ($item['symbol'] == 'node-expanded' || $item['symbol'] == 'node-expanded-static')
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
			if (isset ($top) && IPNetContains ($top, $net))
				;
			elseif (! count ($cellfilter['expression']) || judgeCell ($net, $cellfilter['expression']))
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
			echo "</h4><table class='widetable zebra' border=0 cellpadding=5 cellspacing=0 align='center'>\n";
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

	echo "</td><td class='pcright ${pageno}'>";
	renderCellFilterPortlet ($cellfilter, $realm, $netlist);
	echo "</td></tr></table>\n";
}

function renderIPSpaceEditor()
{
	global $pageno;
	$realm = ($pageno == 'ipv4space' ? 'ipv4net' : 'ipv6net');
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
			echo '</td><td class=tdleft>' . mkCellA ($netinfo) . '</td>';
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
	addJSInternal ('js/live_validation.js');
	$regexp = addslashes ($regexp);
	// Heredoc, not nowdoc!
	addJSText (<<<"END"
$(document).ready(function () {
	$('form#add input[name="range"]').attr('match', '$regexp');
	Validate.init();
});
END
	); // addJSText()

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
	echo '<tr><td class=tdright><input type=checkbox name="is_connected" id="is_connected"></td>';
	echo '<th class=tdleft><label for="is_connected">reserve subnet-router anycast address</label></th></tr>';
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
	setEntityColors ($range);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${range['ip']}/${range['mask']}</h1><h2>";
	echo htmlspecialchars ($range['name'], ENT_QUOTES, 'UTF-8') . "</h2></td></tr>\n";

	echo "<tr><td class=pcleft width='50%'>";

	// render summary portlet
	$summary = array();
	$summary['% used'] = getRenderedIPNetCapacity ($range);
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
	if (getConfigVar ('EXT_IPV4_VIEW') == 'yes' && count ($routers = findNetRouters ($range)))
	{
		$summary['Routed by'] = '';
		foreach ($routers as $rtr)
			$summary['Routed by'] .= getOutputOf ('renderRouterCell', $rtr['ip_bin'], $rtr['iface'], spotEntity ('object', $rtr['id']));
	}
	$summary['tags'] = '';
	renderEntitySummary ($range, 'summary', $summary);

	if ($range['comment'] != '')
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
	if ($maxperpage <= 0 || count ($list) <= $maxperpage)
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
	global $aac_left;
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

	ob_start ();
	echo "<tr><th>Address</th><th>Name</th><th>Comment</th><th>Allocation</th></tr>\n";
	$row_html = ob_get_clean ();
	$override = callHook ('renderIPv4NetworkAddressesHeaderRow_hook', $row_html);
	echo is_string ($override) ? $override : $row_html;

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
			ob_start ();
			echo "<tr class='tdleft $tr_class'><td class=tdleft><a name='ip-$dottedquad' href='" . makeHref(array('page'=>'ipaddress', 'ip' => $dottedquad)) . "'>$dottedquad</a></td>";
			$editable = permitted ('ipaddress', 'properties', 'editAddress')
				? 'editable'
				: '';
			echo "<td><span class='rsvtext $editable id-$dottedquad op-upd-ip-name'></span></td>";
			echo "<td><span class='rsvtext $editable id-$dottedquad op-upd-ip-comment'></span></td><td></td></tr>\n";
			$row_html = ob_get_clean ();
			$override = callHook ('renderIPv4NetworkAddressesRow_hook', $row_html, $ip_bin, null);
			echo is_string ($override) ? $override : $row_html;
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
		ob_start ();
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
			$delim = ';<br/>';
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
		$row_html = ob_get_clean ();
		$override = callHook ('renderIPv4NetworkAddressesRow_hook', $row_html, $ip_bin, $addr);
		echo is_string ($override) ? $override : $row_html;
	}
	// end of iteration
	if (permitted (NULL, NULL, 'set_reserve_comment'))
		addJSInternal ('js/inplace-edit.js');

	echo "</table>";
	if ($rendered_pager != '')
		echo '<p>' . $rendered_pager . '</p>';
}

function renderIPv6NetworkAddresses ($netinfo)
{
	global $pageno, $tabno, $aac_left;
	echo "<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center' width='100%'>\n";
	ob_start ();
	echo "<tr><th>Address</th><th>Name</th><th>Comment</th><th>Allocation</th></tr>\n";
	$row_html = ob_get_clean ();
	$override = callHook ('renderIPv6NetworkAddressesHeaderRow_hook', $row_html);
	echo is_string ($override) ? $override : $row_html;

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
		ob_start ();
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
			$delim = ';<br/>';
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
		$row_html = ob_get_clean ();
		$override = callHook ('renderIPv6NetworkAddressesRow_hook', $row_html, $ip_bin, $addr);
		echo is_string ($override) ? $override : $row_html;
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
		addJSInternal ('js/inplace-edit.js');
}

function renderIPNetworkProperties ($id)
{
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
	global $aat;
	$address = getIPAddress ($ip_bin);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${address['ip']}</h1></td></tr>\n";

	echo "<tr><td class=pcleft>";

	$summary = array();
	if ($address['name'] != '')
		$summary['Name'] = $address['name'];
	if ($address['comment'] != '')
		$summary['Comment'] = $address['comment'];
	$summary['Reserved'] = $address['reserved'];
	$summary['Allocations'] = count ($address['allocs']);
	if (isset ($address['outpf']))
		$summary['Originated NAT connections'] = count ($address['outpf']);
	if (isset ($address['inpf']))
		$summary['Arriving NAT connections'] = count ($address['inpf']);
	renderEntitySummary ($address, 'summary', $summary);

	// render SLB portlet
	if (! empty ($address['vslist']) || ! empty ($address['vsglist']) || ! empty ($address['rsplist']))
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
	if (isset ($address['class']) && ! empty ($address['allocs']))
	{
		startPortlet ('allocations');
		echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>\n";
		echo "<tr><th>object</th><th>OS interface</th><th>allocation type</th></tr>\n";
		// render all allocation records for this address the same way
		foreach ($address['allocs'] as $bond)
		{
			$tr_class = "${address['class']} tdleft";
			if (isset ($_REQUEST['hl_object_id']) && $_REQUEST['hl_object_id'] == $bond['object_id'])
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
	if ($address['name'] == '' && $address['reserved'] == 'no')
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

		includeJQueryUIJS();
		includeJQueryUICSS();

		addJSText (<<<'JSEND'
			$(document).ready( function() {
				$('[name="bond_name"]').autocomplete({
					//minLength: 3,
					search: function(event, ui) {
						var aid = $(this).attr('aid');
						var object_id = $('#aid-'+aid+' [name="object_id"]').val();
						if (!object_id)
							event.preventDefault();
						$(this).autocomplete('option', 'source', '?module=ajax&ac=autocomplete&realm=bond_name&object_id='+object_id);
					},
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
		); // addJSText()
		printOpFormIntro ('add');
		echo "<tr id='aid-new'><td>";
		printImageHREF ('add', 'allocate', TRUE);
		echo "</td><td>";
		printSelect (getNarrowObjectList ('IPV4OBJ_LISTSRC'), array ('name' => 'object_id'));
		echo "</td><td><input type=text name=bond_name size=10 aid='new'></td><td>";
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
			echo "<tr class='$class' id='aid-{$bond['object_id']}'>";
			printOpFormIntro ('upd', array ('object_id' => $bond['object_id']));
			echo "<td>" . getOpLink (array ('op' => 'del', 'object_id' => $bond['object_id'] ), '', 'delete', 'Unallocate address') . "</td>";
			echo "<td>" . makeIPAllocLink ($ip_bin, $bond) . "</td>";
			echo "<td><input type='text' name='bond_name' value='${bond['name']}' size=10 aid='{$bond['object_id']}'></td><td>";
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
		global $natv4_proto;
		printOpFormIntro ('addNATv4Rule');
		echo "<tr align='center'><td>";
		printImageHREF ('add', 'Add new NAT rule', TRUE);
		echo '</td><td>';
		printSelect ($natv4_proto, array ('name' => 'proto'));

		$options = array();
		foreach ($alloclist as $ip_bin => $alloc)
		{
			$ip = $alloc['addrinfo']['ip'];
			$name = (! isset ($alloc['addrinfo']['name']) || $alloc['addrinfo']['name'] == '') ? '' : (' (' . stringForLabel ($alloc['addrinfo']['name']) . ')');
			$osif = (! isset ($alloc['osif']) || $alloc['osif'] == '') ? '' : ($alloc['osif'] . ': ');
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
		if ($pf['local_addr_name'] != '')
			echo ' (' . $pf['local_addr_name'] . ')';
		echo "</td>";
		echo "<td>" . getRenderedIPPortPair ($pf['remoteip'], $pf['remoteport']) . "</td>";

		$address = getIPAddress (ip4_parse ($pf['remoteip']));

		echo "<td class='description'>";
		if (count ($address['allocs']))
			foreach ($address['allocs'] as $bond)
				echo mkA ("${bond['object_name']}(${bond['name']})", 'object', $bond['object_id']) . ' ';
		elseif ($pf['remote_addr_name'] != '')
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
	printOpFormIntro ('addObjects', array ('num_records' => $max));
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
	try
	{
		$terms = trim (genericAssertion ('q', 'string'));
	}
	catch (InvalidRequestArgException $irae)
	{
		$terms = '';
	}
	if ($terms == '')
	{
		showError ('Search string cannot be empty.');
		redirectUser (buildRedirectURL ('index', 'default'));
	}

	try
	{
		parseSearchTerms ($terms);
		// Discard the return value as searchEntitiesByText() and its retriever
		// functions expect the original string as the parameter.
	}
	catch (InvalidArgException $iae)
	{
		showError ($iae->getMessage());
		redirectUser (buildRedirectURL ('index', 'default'));
	}

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
	echo "<center><h2>${nhits} result(s) found for '${terms}'</h2></center>";
	foreach ($summary as $where => $what)
		switch ($where)
		{
			case 'object':
				startPortlet (mkA ('Objects', 'depot'));
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class="cooltable zebra">';
				echo '<tr><th>what</th><th>why</th></tr>';
				foreach ($what as $obj)
				{
					echo "<tr valign=top><td>";
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
							if ($attr_id == 3) // FQDN
							{
								// Switch context for the RackCode in MGMT_PROTOS to work.
								$saved_ctx = getContext();
								fixContext ($object);
							}
							echo '<td class=sticker>' . formatAttributeValue ($record, $object['objtype_id']) . '</td></tr>';
							if ($attr_id == 3)
								restoreContext ($saved_ctx);
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
				}
				echo '</table>';
				finishPortlet();
				break;
			case 'ipv4net':
			case 'ipv6net':
				if ($where == 'ipv4net')
					startPortlet (mkA ('IPv4 networks', 'ipv4space'));
				elseif ($where == 'ipv6net')
					startPortlet (mkA ('IPv6 networks', 'ipv6space'));

				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class="cooltable zebra0">';
				foreach ($what as $cell)
				{
					echo "<tr valign=top><td>";
					renderCell ($cell);
					echo "</td></tr>\n";
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
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class="cooltable zebra">';
				// FIXME: address, parent network, routers (if extended view is enabled)
				echo '<tr><th>Address</th><th>Description</th><th>Comment</th></tr>';
				foreach ($what as $addr)
				{
					echo "<tr><td class=tdleft>";
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
						echo mkA ($fmt, 'ipaddress', $fmt, 'default') . '</td>';
					echo "<td class=tdleft>${addr['name']}</td><td>${addr['comment']}</td></tr>";
				}
				echo '</table>';
				finishPortlet();
				break;
			case 'ipv4rspool':
				startPortlet (mkA ('RS pools', 'ipv4slb', NULL, 'rspools'));
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class="cooltable zebra0">';
				foreach ($what as $cell)
				{
					echo "<tr><td class=tdleft>";
					renderCell ($cell);
					echo "</td></tr>";
				}
				echo '</table>';
				finishPortlet();
				break;
			case 'ipvs':
				startPortlet (mkA ('VS groups', 'ipv4slb', NULL, 'vs'));
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class="cooltable zebra0">';
				foreach ($what as $cell)
				{
					echo "<tr><td class=tdleft>";
					renderCell ($cell);
					echo "</td></tr>";
				}
				echo '</table>';
				finishPortlet();
				break;
			case 'ipv4vs':
				startPortlet (mkA ('Virtual services', 'ipv4slb', NULL, 'default'));
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class="cooltable zebra0">';
				foreach ($what as $cell)
				{
					echo "<tr><td class=tdleft>";
					renderCell ($cell);
					echo "</td></tr>";
				}
				echo '</table>';
				finishPortlet();
				break;
			case 'user':
				startPortlet (mkA ('Users', 'userlist'));
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class="cooltable zebra0">';
				foreach ($what as $item)
				{
					echo "<tr><td class=tdleft>";
					renderCell ($item);
					echo "</td></tr>";
				}
				echo '</table>';
				finishPortlet();
				break;
			case 'file':
				startPortlet (mkA ('Files', 'files'));
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class="cooltable zebra0">';
				foreach ($what as $cell)
				{
					echo "<tr><td class=tdleft>";
					renderCell ($cell);
					echo "</td></tr>";
				}
				echo '</table>';
				finishPortlet();
				break;
			case 'rack':
				startPortlet (mkA ('Racks', 'rackspace'));
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class="cooltable zebra0">';
				foreach ($what as $cell)
				{
					echo "<tr><td class=tdleft>";
					renderCell ($cell);
					echo "</td></tr>";
				}
				echo '</table>';
				finishPortlet();
				break;
			case 'row':
				startPortlet (mkA ('Rack rows', 'rackspace'));
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class="cooltable zebra0">';
				foreach ($what as $cell)
				{
					echo "<tr><td class=tdleft>";
					echo mkCellA ($cell);
					echo "</td></tr>";
				}
				echo '</table>';
				finishPortlet();
				break;
			case 'location':
				startPortlet (mkA ('Locations', 'rackspace'));
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class="cooltable zebra0">';
				foreach ($what as $cell)
				{
					echo "<tr><td class=tdleft>";
					renderCell ($cell);
					echo "</td></tr>";
				}
				echo '</table>';
				finishPortlet();
				break;
			case 'vlan':
				startPortlet (mkA ('VLANs', '8021q'));
				echo '<table border=0 cellpadding=5 cellspacing=0 align=center class="cooltable zebra0">';
				foreach ($what as $vlan)
				{
					echo "<tr><td class=tdleft>";
					echo formatVLANAsHyperlink (getVlanRow ($vlan['id'])) . "</td></tr>";
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

function renderAtomGrid ($data, $is_ro = FALSE)
{
	markAllSpans ($data);
	$rack_id = $data['id'];
	$reverse = considerConfiguredConstraint ($data, 'REVERSED_RACKS_LISTSRC');
	addJSInternal ('js/racktables.js');
	for ($unit_no = $data['height']; $unit_no > 0; $unit_no--)
	{
		$unit_label = inverseRackUnit ($data['height'], $unit_no, $reverse);
		echo "<tr><th><a href='javascript:;' onclick=\"toggleRowOfAtoms('${rack_id}','${unit_no}')\">${unit_label}</a></th>";
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			$show_checkbox = $data[$unit_no][$locidx]['enabled'];
			if (! $show_checkbox && array_fetch ($data[$unit_no][$locidx], 'skipped', FALSE))
				continue;
			$state = $data[$unit_no][$locidx]['state'];
			$td = array ('class' => $show_checkbox ? 'atom ' : '');
			$td['class'] .= "state_{$state}";
			if (array_key_exists ('hl', $data[$unit_no][$locidx]))
			{
				// Implies $state != 'F'.
				$hl = $data[$unit_no][$locidx]['hl'];
				$td['class'] .= $hl;
				if ($state == 'T')
				{
					// Implies object_id is set and the value is not NULL.
					$objectData = spotEntity ('object', $data[$unit_no][$locidx]['object_id']);
					setEntityColors ($objectData);
					$class_context = ($hl == 'h' || $hl == 'hw') ? 'atom_selected' : 'atom_plain';
					$td['class'] .= getCellClass ($objectData, $class_context);
				}
			}
			if (! $show_checkbox)
				foreach (array ('colspan', 'rowspan') as $key)
					if (array_key_exists ($key, $data[$unit_no][$locidx]))
						$td[$key] = $data[$unit_no][$locidx][$key];

			echo makeHtmlTag ('td', $td);
			if ($show_checkbox)
			{
				// FIXME: This data requires a cleaner handover from markupAtomGrid()
				// to be better suited for makeHtmlTag().
				$name = "atom_${rack_id}_${unit_no}_${locidx}";
				$disabled_text = $is_ro ? ' disabled' : '';
				echo "<input type=checkbox" . $data[$unit_no][$locidx]['checked'] . " name=${name} id=${name}${disabled_text}>";
			}
			elseif ($state == 'T')
				printObjectDetailsForRenderRack ($data[$unit_no][$locidx]['object_id']);
			else
				echo '&nbsp;';
			echo '</td>';
		}
		echo "</tr>\n";
	}
}

function renderCellList ($realm = NULL, $title = 'items', $do_amplify = FALSE, $celllist = NULL)
{
	if ($realm === NULL)
		$realm = etypeByPageno();
	global $pageno;
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
		if (! count ($celllist))
			echo '(none)';
		else
		{
			echo '<table class="cooltable zebra0" border=0 cellpadding=5 cellspacing=0 align=center>';
			foreach ($celllist as $cell)
			{
				echo '<tr><td>';
				renderCell ($cell);
				echo '</td></tr>';
			}
			echo '</table>';
		}
		finishPortlet();
	}
	echo "</td><td class='pcright ${pageno}'>";
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
			$record['value'] != '' &&
			permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $record['id'])))
		)
			$summary['{sticker}' . $record['name']] = formatAttributeValue ($record, 1562);
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
	renderEditAttributeTRs ('updateLocation', getAttrValuesSorted ($location_id), 1562);
	echo '<tr>' .
		'<td>&nbsp;</td>' .
		'<th class=tdright><label for=has_problems>Has problems:</label></th>' .
		'<td class=tdleft><input type=checkbox name=has_problems id=has_problems' . ($location['has_problems'] == 'yes' ? ' checked' : '') . '></td>' .
		"</tr>\n";
	if (count ($location['locations']) == 0 && count ($location['rows']) == 0)
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
	echo <<<'ENDOFTEXT'
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
ENDOFTEXT;
	finishPortlet();
}

function renderSNMPPortFinder ($object_id)
{
	if (!extension_loaded ('snmp'))
	{
		echo "<div class=msg_error>The PHP SNMP extension is not loaded.  Cannot continue.</div>";
		return;
	}
	if ('' == $snmpcomm = getConfigVar ('DEFAULT_SNMP_COMMUNITY'))
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
	$slselect = getSelect ($sloptions, array ('name' => 'sec_level'), 'noAuthNoPriv');
	// Heredoc, not nowdoc!
	echo <<<"ENDOFTEXT"
	<table cellspacing=0 cellpadding=5 align=center class=widetable>
	<tr>
		<th class=tdright><label for=sec_name>Security User:</label></th>
		<td class=tdleft><input type=text id=sec_name name=sec_name value='${snmpcomm}'></td>
	</tr>
	<tr>
		<th class=tdright><label for="sec_level">Security Level:</label></th>
		<td class=tdleft>${slselect}</td>
	</tr>
	<tr>
		<th class=tdright>Auth Type:</th>
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
		<th class=tdright>Priv Type:</th>
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
ENDOFTEXT;
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
			if ($ptrname != '')
			{
				echo ' class=trok';
				$cnt_match++;
			}
		}
		elseif ($addr['name'] == '' || $ptrname == '')
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
		if (isset ($range['addrlist'][$ip_bin]['class']) && $range['addrlist'][$ip_bin]['class'] != '')
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
		addJSInternal ('js/racktables.js');
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
	if (array_key_exists ('is_assignable', $taginfo) && $taginfo['is_assignable'] == 'no')
	{
		$ret['input_extraattrs'] = 'disabled';
		$ret['tr_class'] .= (array_key_exists ('kidc', $taginfo) && $taginfo['kidc'] == 0) ? ' trwarning' : ' trnull';
	}
	if (array_key_exists ('description', $taginfo) && $taginfo['description'] != '')
		$ret['description'] = $taginfo['description'];

	if ($refcnt_realm != '' && isset ($taginfo['refcnt'][$refcnt_realm]))
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
			if (! array_key_exists ('description', $row))
				$tag_extraattrs = '';
			else
			{
				$tag_extraattrs = 'title="' . stringForOption ($row['description'], 0) . '"';
				$tag_class .= ' tag-descr';
			}
			echo "> <span class='${tag_class}' ${tag_extraattrs}>${row['text_tagname']}</span>";
			if (array_key_exists ('text_refcnt', $row))
				echo " <i>(${row['text_refcnt']})</i>";
			echo '</label></td></tr>';
		}
}

function renderEntityTagsPortlet ($title, $tags, $preselect, $realm)
{
	startPortlet ($title);
	echo '<a class="toggleTreeMode" style="display:none" href="#"></a>';
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
		// It could happen that none of existing tags have been used in the current realm.
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
			addJSInternal ('js/tag-cb.js');
			addJSText ($js_code);
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
	addJSInternal ('js/tag-cb.js');
	addJSText ('tag_cb.enableNegation()');

	global $pageno, $tabno, $taglist;
	$filterc =
	(
		count ($preselect['tagidlist']) +
		count ($preselect['pnamelist']) +
		($preselect['extratext'] != '' ? 1 : 0)
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
	if (getConfigVar ('FILTER_SUGGEST_ANDOR') == 'yes' || $preselect['andor'] != '')
	{
		echo $hr;
		$hr = $ruler;
		$andor = $preselect['andor'] != '' ? $preselect['andor'] : getConfigVar ('FILTER_DEFAULT_ANDOR');
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
	if (getConfigVar ('FILTER_SUGGEST_TAGS') == 'yes' || count ($preselect['tagidlist']))
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
			addJSText ('tag_cb.enableSubmitOnClick()');
	}
	// predicates block
	if (getConfigVar ('FILTER_SUGGEST_PREDICATES') == 'yes' || count ($preselect['pnamelist']))
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
	if (getConfigVar ('FILTER_SUGGEST_EXTRA') == 'yes' || $preselect['extratext'] != '')
	{
		$enable_textify = !empty ($preselect['text']) || !empty($preselect['extratext']);
		$enable_apply = TRUE;
		if ($preselect['extratext'] != '')
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
			addJSText (<<<'END'
function textifyCellFilter(target, text)
{
	var portlet = $(target).closest ('.portlet');
	portlet.find ('textarea[name="cfe"]').html (text);
	portlet.find ('input[type="checkbox"]').attr('checked', '');
	portlet.find ('input[type="radio"][value="and"]').attr('checked','true');
}
END
			); // addJSText()
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
	if ($file['comment'] != '')
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

	if (isolatedPermission ('file', 'download', $file) && '' != ($pcode = getFilePreviewCode ($file)))
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
		echo "<tr><th class=tdright>File:</th><td class=tdleft><input type='file' size='10' name='file'></td></tr>";
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
			if (isolatedPermission ('file', 'download', $file) && '' != ($pcode = getFilePreviewCode ($file)))
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
	if ($netinfo['symbol'] != '')
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
	if (getConfigVar ('IPV4_TREE_SHOW_VLAN') == 'yes' && ! empty ($netinfo['8021q']))
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
	setEntityColors ($cell);
	$class = 'slbcell vscell ' . getCellClass ($cell, 'list_plain');
	switch ($cell['realm'])
	{
	case 'user':
		echo "<table class='{$class}'><tr><td rowspan=3 width='5%'>";
		printImageHREF ('USER');
		echo '</td><td>' . mkA (stringForTD ($cell['user_name']), 'user', $cell['user_id']) . '</td></tr>';
		if ($cell['user_realname'] != '')
			echo "<tr><td><strong>" . stringForTD ($cell['user_realname']) . "</strong></td></tr>";
		else
			echo "<tr><td class=sparenetwork>no name</td></tr>";
		echo '<td>';
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	case 'file':
		echo "<table class='{$class}'><tr><td rowspan=3 width='5%'>";
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
		$title = 'created ' . $cell['ctime'];
		if ($cell['ctime'] != $cell['mtime'])
			$title .= "\nmodified " . $cell['mtime'];
		echo "<span title='${title}'>";
		echo mkA ('<strong>' . stringForTD ($cell['name']) . '</strong>', 'file', $cell['id']);
		echo '</span>';
		echo "</td><td rowspan=3 valign=top>";
		if (isset ($cell['links']) && count ($cell['links']))
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
		echo "<table class='{$class}'><tr><td rowspan=3 width='5%'>";
		printImageHREF ('NET');
		echo '</td><td>' . mkCellA ($cell);
		echo getRenderedIPNetCapacity ($cell);
		echo '</td></tr>';

		echo "<tr><td>";
		if ($cell['name'] != '')
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
		echo "<table class='{$class}'><tr><td rowspan=3 width='5%'>";
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
		echo "<table class='{$class}'><tr><td rowspan=3 width='5%'>";
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
		echo "<table class='{$class}'><tr><td rowspan=2 width='5%'>";
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
	setEntityColors ($cell);
	// This block appears either on a plain page background (in which case both
	// "list" and "atom" work about the same), or inside a network row, which
	// uses a mix of zebra and tag colours (in which case "atom" works better
	// as it overlays the router's tag colours without mixing).
	$class = 'slbcell ' . getCellClass ($cell, 'atom_plain');
	$dottedquad = ip_format ($ip_bin);
	echo "<table class='${class}'><tr><td rowspan=3>${dottedquad}";
	if ($ifname != '')
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
			if (function_exists ('getimagesizefromstring'))
				list ($width, $height) = getimagesizefromstring ($file['contents']);
			else
			{
				$image = imagecreatefromstring ($file['contents']);
				$width = imagesx ($image);
				$height = imagesy ($image);
			}
			if ($width < getConfigVar ('PREVIEW_IMAGE_MAXPXS') && $height < getConfigVar ('PREVIEW_IMAGE_MAXPXS'))
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
	$fullInfo = getFile ($file_id);
	printOpFormIntro ('updateFileText', array ('mtime_copy' => $fullInfo['mtime']));
	echo '<table border=0 align=center>';
	echo "<tr><td><textarea rows=45 cols=180 name=file_text>\n";
	echo stringForTextarea ($fullInfo['contents']) . '</textarea></td></tr>';
	echo "<tr><td class=submit><input type=submit value='Save'>";
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
		global $sic;
		$path = array();
		$page_name = preg_replace ('/:.*/', '', $targetno);
		// Recursion breaks at first parentless page.
		if ($page_name == 'ipaddress')
		{
			// case ipaddress is a universal v4/v6 page, it has two parents and requires special handling
			$ip_bin = ip_parse ($sic['ip']);
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
	global $sic;
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
				$items[] = mkCellA ($rack);
				$items[] = mkA ($rack['row_name'], 'row', $rack['row_id']);
				if ($rack['location_id'] && ('' != $trail = getLocationTrail ($rack['location_id'])))
					$items[] = $trail;
			}
			break;
		case 'row':
			if ('' != $trail = getLocationTrail ($title['params']['location_id']))
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
	// This input's implicit tabindex will be the lowest unless there is a form with ports or addresses on the page.
	echo '<label><u>S</u>earch:<input accesskey="s" type=text name=q size=20 value="';
	echo array_key_exists ('q', $sic) ? stringForTextInputValue ($sic['q']) : '';
	echo '"></label></form></div>';

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
		elseif ('' == $tabclass = call_user_func ($trigger[$pageno][$tabidx]))
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
		$chapter_no = genericAssertion ('chapter_no', 'natural');
		$chapters = getChapterList();
		$chapter_name = isset ($chapters[$chapter_no]) ? $chapters[$chapter_no]['name'] : 'N/A';
		return array
		(
			'name' => "Chapter '${chapter_name}'",
			'params' => array ('chapter_no' => $chapter_no)
		);
	case 'user':
		$userinfo = spotEntity ('user', genericAssertion ('user_id', 'natural'));
		return array
		(
			'name' => "Local user '" . $userinfo['user_name'] . "'",
			'params' => array ('user_id' => $userinfo['user_id'])
		);
	case 'ipv4rspool':
		$pool_info = spotEntity ('ipv4rspool', genericAssertion ('pool_id', 'natural'));
		return array
		(
			'name' => $pool_info['name'] == '' ? 'ANONYMOUS' : $pool_info['name'],
			'params' => array ('pool_id' => $pool_info['id'])
		);
	case 'ipv4vs':
		$vs_info = spotEntity ('ipv4vs', genericAssertion ('vs_id', 'natural'));
		return array
		(
			'name' => $vs_info['dname'],
			'params' => array ('vs_id' => $vs_info['id'])
		);
	case 'ipvs':
		$vs_info = spotEntity ('ipvs', genericAssertion ('vs_id', 'natural'));
		return array
		(
			'name' => $vs_info['name'],
			'params' => array ('vs_id' => $vs_info['id'])
		);
	case 'object':
		$object = spotEntity ('object', genericAssertion ('object_id', 'natural'));
		return array
		(
			'name' => $object['dname'],
			'params' => array ('object_id' => $object['id'])
		);
	case 'location':
		$location = spotEntity ('location', genericAssertion ('location_id', 'natural'));
		return array
		(
			'name' => $location['name'],
			'params' => array ('location_id' => $location['id'])
		);
	case 'row':
		switch ($pageno)
		{
		case 'rack':
			$rack = spotEntity ('rack', genericAssertion ('rack_id', 'natural'));
			return array
			(
				'name' => $rack['row_name'],
				'params' => array ('row_id' => $rack['row_id'], 'location_id' => $rack['location_id'])
			);
		case 'row':
			$row_info = getRowInfo (genericAssertion ('row_id', 'natural'));
			return array
			(
				'name' => $row_info['name'],
				'params' => array ('row_id' => $row_info['id'], 'location_id' => $row_info['location_id'])
			);
		default:
			break;
		}
	case 'rack':
		$rack_info = spotEntity ('rack', genericAssertion ('rack_id', 'natural'));
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
		$file = spotEntity ('file', genericAssertion ('file_id', 'natural'));
		return array
		(
			'name' => stringForOption ($file['name'], 30),
			'params' => array ('file_id' => $_REQUEST['file_id'])
		);
	case 'ipaddress':
		$address = getIPAddress (ip_parse ($_REQUEST['ip']));
		return array
		(
			'name' => stringForOption ($address['ip'] . ($address['name'] != '' ? ' (' . $address['name'] . ')' : ''), 50),
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
				$net = spotEntity ($path_position, genericAssertion ('id', 'natural'));
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
			'name' => "domain '" . stringForOption ($vdlist[$vdom_id], 40) . "'",
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
			'name' => "template '" . stringForOption ($vst['description'], 40) . "'",
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
		if ($port['name'] != '')
			$myports[$port['name']][] = $port;

	// scroll to selected port
	if (isset ($_REQUEST['hl_port_id']))
	{
		genericAssertion ('hl_port_id', 'natural');
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
					$error_message = 'No such port on ' . mkCellA ($dp_remote_object);
					break;
				}

				// determine match or mismatch of local link
				foreach ($local_ports as $portinfo_local)
					if ($portinfo_local['remote_id'])
					{
						if
						(
							$portinfo_local['remote_object_id'] == $dp_remote_object_id &&
							$portinfo_local['remote_name'] == $dp_neighbor['port']
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
				if (isset ($hl_port_id) && $hl_port_id == $portinfo_local['id'])
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

	addJSText (<<<'END'
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
		if ($left_text != $right_text && $right_text != '')
		{
			if ($text != '')
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

function formatAttributeValue ($record, $objtype_id)
{
	switch ($record['type'])
	{
	case 'uint':
	case 'float':
		return $record['value'];
	case 'date':
		return sprintf
		(
			'<span title="%s">%s</span>',
			datetimeFormatHint (getConfigVar ('DATETIME_FORMAT')),
			datetimestrFromTimestamp ($record['value'])
		);
	case 'string':
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
					// FIXME: In the current implementation the exception class is neither RackCodeError
					// nor RCParserError, which is likely wrong. In the specific case of a syntax error
					// it would be helpful to display its text in the warning.
					showWarning ("could not parse '${filter}' for management protocol '${proto}' in MGMT_PROTOS");
					continue;
				}
		return array_key_exists ('href', $record) ?
			"<a href='${record['href']}'>${record['a_value']}</a>" :
			$record['a_value'];
	case 'dict':
		$map = array
		(
			// The rackspace view features the tag filter but a dictionary-based autotag would
			// never match there because the current code generates {$attr_X_Y} only for objects.
			// As soon as racks have these autotags too, changing the value below to 'rackspace'
			// will make filtering work as expected.
			1560 => NULL,
			// The user interface at the moment does not implement the tag filter for rows or
			// locations, also these object types don't have dictionary-based autotags.
			1561 => NULL,
			1562 => NULL,
		);
		if (NULL === $filter_pageno = array_fetch ($map, $objtype_id, 'depot'))
			$result = $record['a_value'];
		else
		{
			$filter_args = array
			(
				'page' => $filter_pageno,
				'tab' => 'default',
				'andor' => 'and',
				'cfe' => '{$attr_' . $record['id'] . '_' . $record['key'] . '}',
			);
			$filter_url = makeHref ($filter_args);
			$result = "<a href='${filter_url}'>${record['a_value']}</a>";
		}
		if (array_key_exists ('href', $record))
			$result .= "&nbsp;<a class='img-link' href='${record['href']}'>" . getImageHREF ('html', 'vendor\'s info page') . "</a>";
		return $result;
	default:
		throw new InvalidArgException ('record[type]', $record['type']);
	}
}

function addAutoScrollScript ($anchor_name)
{
	// Heredoc, not nowdoc!
	addJSText (<<<"END"
$(document).ready(function() {
	var anchor = document.getElementsByName('$anchor_name')[0];
	if (anchor)
		anchor.scrollIntoView(false);
});
END
	);
}

//
// Display object level logs
//
function renderObjectLogEditor ()
{
	echo '<center><h2>Log records for this object (' . mkA ('complete list', 'objectlog') . ')</h2></center>';
	printOpFormIntro ('add');
	echo '<table with="80%" align=center border=0 cellpadding=5 cellspacing=0 align=center class="cooltable zebra0">';
	echo '<tr valign=top>';
	echo '<td class=tdcenter>' . getImageHREF ('CREATE', 'add record', TRUE) . '</td>';
	echo '<td><textarea name=logentry rows=10 cols=80></textarea></td>';
	echo '<td class=tdcenter>' . getImageHREF ('CREATE', 'add record', TRUE) . '</td>' ;
	echo '</tr></form>';

	foreach (getLogRecordsForObject (getBypassValue()) as $row)
	{
		echo '<tr valign=top>';
		echo '<td class=tdleft>' . $row['date'] . '<br>' . $row['user'] . '</td>';
		echo '<td class="logentry">' . string_insert_hrefs (htmlspecialchars ($row['content'], ENT_NOQUOTES)) . '</td>';
		echo "<td class=tdleft>";
		echo getOpLink (array('op'=>'del', 'log_id'=>$row['id']), '', 'DESTROY', 'Delete log entry');
		echo "</td></tr>\n";
	}
	echo '</table>';
}

//
// Display form and All log entries
//
function allObjectLogs ()
{
	if (0 == count ($logs = getLogRecords()))
	{
		echo '<center><h2>No logs exist</h2></center>';
		return;
	}
	$columns = array
	(
		array ('th_text' => 'Object', 'th_class' => 'tdleft', 'row_key' => 0, 'td_escape' => FALSE, 'td_class' => 'tdleft'),
		array ('th_text' => 'Date/user', 'th_class' => 'tdleft', 'row_key' => 1, 'td_escape' => FALSE, 'td_class' => 'tdleft'),
		array ('th_text' => getImageHREF ('text'), 'th_class' => 'tdcenter', 'row_key' => 2, 'td_escape' => FALSE, 'td_class' => 'logentry'),
	);
	$rows = array();
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
		$rows[] = array
		(
			mkA ($text, $entity, $row['object_id'], 'log'),
			$row['date'] . '<br>' . stringForLabel ($row['user'], 0),
			string_insert_hrefs (htmlspecialchars ($row['content'], ENT_NOQUOTES)),
		);
	}
	renderTableViewer ($columns, $rows);
}

// FIXME: this function is not used
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
	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";

	$clusters = getVMClusterSummary ();
	startPortlet ('Clusters (' . count ($clusters) . ')');
	if (count($clusters) > 0)
	{
		echo "<table border=0 cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>Cluster</th><th>Hypervisors</th><th>Resource Pools</th><th>Cluster VMs</th><th>RP VMs</th><th>Total VMs</th></tr>\n";
		foreach ($clusters as $cluster)
		{
			$total_vms = $cluster['cluster_vms'] + $cluster['resource_pool_vms'];
			echo '<tr valign=top>';
			echo '<td class="tdleft">' . mkA ("<strong>${cluster['name']}</strong>", 'object', $cluster['id']) . '</td>';
			echo "<td class='tdleft'>${cluster['hypervisors']}</td>";
			echo "<td class='tdleft'>${cluster['resource_pools']}</td>";
			echo "<td class='tdleft'>${cluster['cluster_vms']}</td>";
			echo "<td class='tdleft'>${cluster['resource_pool_vms']}</td>";
			echo "<td class='tdleft'>$total_vms</td>";
			echo "</tr>\n";
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
		echo "<table border=0 cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>Pool</th><th>Cluster</th><th>VMs</th></tr>\n";
		foreach ($pools as $pool)
		{
			echo '<tr valign=top>';
			echo '<td class="tdleft">' . mkA ("<strong>${pool['name']}</strong>", 'object', $pool['id']) . '</td>';
			echo '<td class="tdleft">';
			if ($pool['cluster_id'])
				echo mkA ("<strong>${pool['cluster_name']}</strong>", 'object', $pool['cluster_id']);
			echo '</td>';
			echo "<td class='tdleft'>${pool['VMs']}</td>";
			echo "</tr>\n";
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
		echo "<table border=0 cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>Hypervisor</th><th>Cluster</th><th>VMs</th></tr>\n";
		foreach ($hypervisors as $hypervisor)
		{
			echo '<tr valign=top>';
			echo '<td class="tdleft">' . mkA ("<strong>${hypervisor['name']}</strong>", 'object', $hypervisor['id']) . '</td>';
			echo '<td class="tdleft">';
			if ($hypervisor['cluster_id'])
				echo mkA ("<strong>${hypervisor['cluster_name']}</strong>", 'object', $hypervisor['cluster_id']);
			echo '</td>';
			echo "<td class='tdleft'>${hypervisor['VMs']}</td>";
			echo "</tr>\n";
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
		echo "<table border=0 cellpadding=5 cellspacing=0 align=center class='cooltable zebra'>\n";
		echo "<tr><th>Name</th></tr>\n";
		foreach ($switches as $switch)
		{
			echo '<tr valign=top>';
			echo '<td class="tdleft">' . mkA ("<strong>${switch['name']}</strong>", 'object', $switch['id']) . '</td>';
			echo "</tr>\n";
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
		'mac' => array ('op' => 'get_mac_list', 'gw' => 'getmaclist'),
		'portmac' => array ('op' => 'get_port_mac_list', 'gw' => 'getportmaclist'),
	);
	$breed = detectDeviceBreed ($object_id);
	$allowed_ops = array();
	foreach ($available_ops as $prefix => $data)
		if
		(
			permitted ('object', 'liveports', $data['op']) &&
			validBreedFunction ($breed, $data['gw'])
		)
			$allowed_ops[] = $prefix;

	addJSInternal ('js/jquery.thumbhover.js');
	addCSSInternal ('css/jquery.contextmenu.css');
	addJSInternal ('js/jquery.contextmenu.js');
	addJSText ("enabled_elements = " . json_encode ($allowed_ops));
	addJSInternal ('js/portinfo.js');
}

function renderIPAddressLog ($ip_bin)
{
	startPortlet ('Log messages');
	echo '<table class="widetable zebra" cellspacing="0" cellpadding="5" align="center" width="50%"><tr>';
	echo '<th class=tdleft>Date &uarr;</th>';
	echo '<th class=tdleft>User</th>';
	echo '<th class=tdleft>Log message</th>';
	echo '</tr>';
	foreach (array_reverse (fetchIPLogEntry ($ip_bin)) as $line)
	{
		echo '<tr>';
		echo '<td class=tdleft>' . $line['date'] . '</td>';
		echo '<td class=tdleft>' . $line['user'] . '</td>';
		echo '<td class=tdleft>' . $line['message'] . '</td>';
		echo '</tr>';
	}
	echo '</table>';
	finishPortlet();
}

// returns an array with two items - each is HTML-formatted <TD> tag
function formatPortReservation ($port)
{
	$ret = array();
	$ret[] = '<td class=tdleft>' .
		($port['reservation_comment'] != '' ? formatLoggedSpan ($port['last_log'], 'Reserved:', 'strong underline') : '').
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
	$columns = array
	(
		array ('th_text' => 'Origin', 'row_key' => 'img', 'td_escape' => FALSE),
		array ('th_text' => 'Key', 'row_key' => $column['key']),
		array ('th_text' => $column['header'], 'row_key' => $column['value'], 'td_maxlen' => $column['width']),
	);
	foreach (array_keys ($rows) as $key)
		$rows[$key]['img'] = $rows[$key]['origin'] == 'default' ? getImageHREF ('computer', 'default') :
			getImageHREF ('favorite', 'custom');
	renderTableViewer ($columns, $rows);
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

// Each table column descriptor is an array that contains at least the "row_key"
// key, which tells where the data for that column is in each row. It may also
// contain additional keys below:
//
// th_text   -- text for the TH (not HTML escaped!)
// th_class  -- CSS class for the TH
// td_class  -- CSS class for the TD
// td_escape -- whether to do HTML escaping (defaults to TRUE)
// td_maxlen -- cutoff margin for escaping (defaults to 0)
function renderTableViewer ($columns, $rows, $params = NULL)
{
	$header_row = FALSE;
	foreach ($columns as $col)
	{
		if (! array_key_exists ('row_key', $col))
			throw new InvalidArgException ('columns', '(array)', '\'row_key\' is not set for a column');
		if (array_key_exists ('th_text', $col))
			$header_row = TRUE;
	}
	if ($params === NULL)
		$params = array
		(
			'cellspacing' => 0,
			'cellpadding' => 5,
			'align' => 'center',
			'class' => 'widetable zebra0',
		);
	echo makeHtmlTag ('table', $params);
	if ($header_row)
	{
		echo '<thead><tr>';
		foreach ($columns as $col)
		if (! array_key_exists ('th_text', $col))
			echo '<th>&nbsp;</th>';
		else
		{
			echo '<th';
			if (array_key_exists ('th_class', $col))
				echo ' class=' . $col['th_class'];
			echo '>' . $col['th_text'] . '</th>';
		}
		echo '</tr></thead>';
	}
	echo '<tbody>';
	foreach ($rows as $row)
	{
		$trattr = array ('align' => 'left', 'valign' => 'top');
		if (array_key_exists ('_tr_class', $row))
			$trattr['class'] = $row['_tr_class'];
		echo makeHtmlTag ('tr', $trattr);
		foreach ($columns as $col)
			if (! array_key_exists ($col['row_key'], $row))
				echo '<td class=trerror>data error</td>';
			else
			{
				echo '<td';
				if (array_key_exists ('td_class', $col))
					echo " class='{$col['td_class']}'";
				echo '>';
				$text = $row[$col['row_key']];
				if (array_fetch ($col, 'td_escape', TRUE))
					$text = stringForTD ($text, array_fetch ($col, 'td_maxlen', 0));
				echo $text . '</td>';
			}
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
}
