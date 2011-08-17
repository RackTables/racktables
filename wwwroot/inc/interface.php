<?php
/*
*
*  This file contains frontend functions for RackTables.
*
*/

require_once 'ajax-interface.php';

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
<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" class="maintable">
 <tr class="mainheader"><td>
 <?php echo getConfigVar ('enterprise') ?> RackTables <a href="http://racktables.org" title="Visit RackTables site"><?php echo CODE_VERSION ?></a><?php renderQuickLinks() ?>
 <div style="float: right" class=greeting><a href='index.php?page=myaccount&tab=default'><?php global $remote_displayname; echo $remote_displayname ?></a> [ <a href='?logout'>logout</a> ]</div>
 </td></tr>
 <tr><td class="menubar">
  <table border="0" width="100%" cellpadding="3" cellspacing="0">
  <tr><?php showPathAndSearch ($pageno); ?></tr>
  </table>
 </td></tr>
 <tr><td><?php showTabs ($pageno, $tabno); ?></td></tr>
 <tr><td><?php showMessageOrError(); ?></td></tr>
 <tr><td><?php echo $payload; ?></td></tr>
</table>
</body>
</html>
<?php
}

// Main menu.
function renderIndexItem ($ypageno) {
  global $page;
  if (permitted($ypageno)) {
	  $title = getPageName ($ypageno);
	print "          <td>\n";          
    print "            <h1><a href='".makeHref(array('page'=>$ypageno))."'>".$title."<br>\n";
    printImageHREF ($ypageno);
    print "</a></h1>\n";
    print "          </td>\n";
  } else {
    print "          <td>&nbsp;</td>\n";
  }
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
		'td_ip' => '',
		'td_network' => '',
		'td_routed_by' => '',
		'td_peers' => '',
	);
	$dottedquad = $alloc['addrinfo']['ip'];

	$hl_ip_addr = '';
	if (isset ($_REQUEST['hl_ipv6_addr']))
	{
		if ($hl_ipv6 = assertIPv6Arg ('hl_ipv6_addr'))
			$hl_ip_addr = $hl_ipv6->format();
	}
	elseif (isset ($_REQUEST['hl_ipv4_addr']))
		$hl_ip_addr = $_REQUEST['hl_ipv4_addr'];
	if ($hl_ip_addr)
		addAutoScrollScript ("ip-$hl_ip_addr");

	// prepare realm and network info
	if ($alloc['addrinfo']['version'] == 6)
	{
		$ipv6_address = new IPv6Address();
		$ipv6_address->parse ($dottedquad);
		$addr_page_name = 'ipv6address';
		if ($netid = getIPv6AddressNetworkId ($ipv6_address))
		{
			$netinfo = spotEntity ('ipv6net', $netid);
			loadIPv6AddrList ($netinfo);
		}
	}
	else
	{
		$addr_page_name = 'ipaddress';
		if ($netid = getIPv4AddressNetworkId ($dottedquad))
		{
			$netinfo = spotEntity ('ipv4net', $netid);
			loadIPv4AddrList ($netinfo);
		}
	}

	$ret['tr_class'] = $alloc['addrinfo']['class'];
	$td_class = 'tdleft';
	if ($hl_ip_addr == $dottedquad)
		$td_class .= ' port_highlight';
	
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
	global $aac;
	$ret['td_ip'] = "<td class='$td_class'>";
	if (NULL !== $netid)
		$ret['td_ip'] .= "<a name='ip-$dottedquad' class='$ip_class' $ip_title href='" .
			makeHref (
				array
				(
					'page' => $addr_page_name,
					'hl_object_id' => $object_id,
					'ip' => $dottedquad,
				)
			) . "'>" . $dottedquad . "</a>";
	else
		$ret['td_ip'] .= "<span class='$ip_class' $ip_title>$dottedquad</span>";
	if (getConfigVar ('EXT_IPV4_VIEW') != 'yes')
		$ret['td_ip'] .= '<small>/' . (NULL === $netid ? '??' : $netinfo['mask']) . '</small>';
	$ret['td_ip'] .= '&nbsp;' . $aac[$alloc['type']];
	if (strlen ($alloc['addrinfo']['name']))
		$ret['td_ip'] .= ' (' . niftyString ($alloc['addrinfo']['name']) . ')';
	$ret['td_ip'] .= '</td>';

	// render network and routed_by tds
	if (NULL === $netid)
	{
		$ret['td_network'] = "<td class='$td_class sparenetwork'>N/A</td>";
		$ret['td_routed_by'] = $ret['td_network'];
	}
	else
	{
		$ret['td_network'] = "<td class='$td_class'>" .
			getOutputOf ('renderCell', $netinfo) . '</td>';

		// filter out self-allocation
		$other_routers = array();
		foreach (findRouters ($netinfo['addrlist']) as $router)
			if ($router['id'] != $object_id)
				$other_routers[] = $router;
		if (count ($other_routers))
			$ret['td_routed_by'] = getOutputOf ('printRoutersTD', $other_routers);
		else
			$ret['td_routed_by'] = "<td class='$td_class'>&nbsp;</td>";
	}
	
	// render peers td
	$ret['td_peers'] = "<td class='$td_class'>";
	$prefix = '';
	if ($alloc['addrinfo']['reserved'] == 'yes')
	{
		$ret['td_peers'] .= $prefix . '<strong>RESERVED</strong>';
		$prefix = '; ';
	}
	foreach ($alloc['addrinfo']['allocs'] as $allocpeer)
	{
		if ($allocpeer['object_id'] == $object_id)
			continue;
		$ret['td_peers'] .= $prefix . "<a href='" . makeHref (array ('page' => 'object', 'object_id' => $allocpeer['object_id'])) . "'>";
		if (isset ($allocpeer['osif']) and strlen ($allocpeer['osif']))
			$ret['td_peers'] .= $allocpeer['osif'] . '@';
		$ret['td_peers'] .= $allocpeer['object_name'] . '</a>';
		$prefix = '; ';
	}
	$ret['td_peers'] .= '</td>';

	return $ret;
}

function renderRackspace ()
{
	$found_racks = array();
	$rows = array();
	$cellfilter = getCellFilter();
	$rackCount = 0;
	foreach (getRows() as $row_id => $row_name) {
		$rackList = filterCellList (listCells ('rack', $row_id), $cellfilter['expression']);
		$found_racks = array_merge($found_racks, $rackList);
		$rows[] = array(
			'row_id' => $row_id,
			'row_name' => $row_name,
			'racks' => $rackList
		);
		$rackCount += count($rackList);
	}

	echo "<table class=objview border=0 width='100%'><tr><td class=pcleft>";
	renderCellFilterPortlet ($cellfilter, 'rack', $found_racks);
	echo '</td><td class=pcright>';

	if (! renderEmptyResults($cellfilter, 'racks', $rackCount))
	{
		echo '<table border=0 cellpadding=10 cellpadding=1>';
		// generate thumb gallery
		global $nextorder;
		$rackwidth = getRackImageWidth();
		// Zero value effectively disables the limit.
		$maxPerRow = getConfigVar ('RACKS_PER_ROW');
		$order = 'odd';
		foreach ($rows as $row)
		{
			$row_id = $row['row_id'];
			$row_name = $row['row_name'];
			$rackList = $row['racks'];

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
					echo "src='?module=image&img=minirack&rack_id=${rack['id']}'>";
					echo "<br>${rack['name']}</a></td>";
					$rackListIdx++;
				}
			$order = $nextorder[$order];
			echo "</tr></table></tr>\n";
		}
		echo "</table>\n";
	}
	echo "</td></tr></table>\n";
}

function renderRackspaceRowEditor ()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('addRow');
		echo "<input type=hidden name=objtype_id value=1561>\n";
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
	foreach (getRows() as $row_id => $row_name)
	{
		echo "<tr><td>";
		if ($rc = count (listCells ('rack', $row_id)))
			printImageHREF ('nodestroy', "${rc} rack(s) here");
		else
		{
			echo "<a href=\"".makeHrefProcess(array('op'=>'deleteRow', 'row_id'=>$row_id))."\">";
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
	$rowInfo = getRowInfo ($row_id);
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
	echo "<tr><th width='50%' class=tdright>% used:</th><td class=tdleft>";
	renderProgressBar (getRSUforRow ($rackList));
	echo "</td></tr>\n";
	echo "</table><br>\n";
	finishPortlet();
	renderCellFilterPortlet ($cellfilter, 'rack', $rackList, 'row_id', $row_id);

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
		$class = ($rack['has_problems'] == 'yes') ? 'error' : $order;
		echo "<td align=center class=row_${class}><a href='".makeHref(array('page'=>'rack', 'rack_id'=>$rack['id']))."'>";
		echo "<img border=0 width=${rackwidth} height=" . (getRackImageHeight ($rack['height']) * getConfigVar ('ROW_SCALE'));
		echo " title='${rack['height']} units'";
		echo "src='?module=image&img=minirack&rack_id=${rack['id']}'>";
		echo "<br>${rack['name']}</a></td>";
		$order = $nextorder[$order];
		$rackListIdx++;
	}
	echo "</tr></table>\n";
	finishPortlet();
	echo "</td></tr></table>";
}

// Used by renderRack()
function printObjectDetailsForRenderRack ($object_id)
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
	$objectChildren = getEntityRelatives ('children', 'object', $objectData['id']);
	if (count($objectChildren) > 0)
	{
		foreach ($objectChildren as $child)
			$childNames[] = $child['name'];
		natsort($childNames);
		$suffix = sprintf(", contains %s'>", implode(', ', $childNames));
	}
	else
		$suffix = "'>";
	echo $prefix . $body . $suffix;
	echo "<a href='".makeHref(array('page'=>'object', 'object_id'=>$objectData['id']))."'>${objectData['dname']}</a></div>";
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
	addAtomCSS();
	for ($i = $rackData['height']; $i > 0; $i--)
	{
		echo "<tr><th>${i}</th>";
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
					printObjectDetailsForRenderRack($rackData[$i][$locidx]['object_id']);
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
	$zeroUObjects = getEntityRelatives('children', 'rack', $rack_id);
	if (count($zeroUObjects) > 0) {
		echo "<br><table width='75%' class=rack border=0 cellspacing=0 cellpadding=1>\n";
		echo "<tr><th>Zero-U:</th></tr>\n";
		foreach ($zeroUObjects as $zeroUObject)
		{
			$state = ($zeroUObject['entity_id'] == $hl_obj_id) ? 'Th' : 'T';
			echo "<tr><td class='atom state_${state}'>";
			printObjectDetailsForRenderRack($zeroUObject['entity_id']);
			echo "</td></tr>\n";
		}
		echo "</table>\n";
	}
	echo "</center>\n";
}

function renderNewRackForm ($row_id)
{
	$default_height = getConfigVar ('DEFAULT_RACK_HEIGHT');
	if ($default_height == 0)
		$default_height = '';
	startPortlet ('Add one');
	printOpFormIntro ('addRack', array ('got_data' => 'TRUE'));
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Name (required):</th><td class=tdleft><input type=text name=name tabindex=1></td>";
	echo "<td rowspan=4>Assign tags:<br>";
	renderNewEntityTags ('rack');
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>Height in units (required):</th><td class=tdleft><input type=text name=height1 tabindex=2 value='${default_height}'></td></tr>\n";
	echo "<tr><th class=tdright>Asset tag:</th><td class=tdleft><input type=text name=asset_no tabindex=4></td></tr>\n";
	echo "<tr><td class=submit colspan=2>";
	printImageHREF ('CREATE', 'Add', TRUE);
	echo "</td></tr></table></form>";
	finishPortlet();

	startPortlet ('Add many');
	printOpFormIntro ('addRack', array ('got_mdata' => 'TRUE'));
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Height in units (*):</th><td class=tdleft><input type=text name=height2 value='${default_height}'></td>";
	echo "<td rowspan=3 valign=top>Assign tags:<br>";
	renderNewEntityTags ('rack');
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>Rack names (required):</th><td class=tdleft><textarea name=names cols=40 rows=25></textarea></td></tr>\n";
	echo "<tr><td class=submit colspan=2>";
	printImageHREF ('CREATE', 'Add', TRUE);
	echo '</form></table>';
	finishPortlet();
}

function renderEditObjectForm()
{
	global $pageno, $virtual_obj_types;
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
	if (in_array($object['objtype_id'], $virtual_obj_types))
	{
		echo "<input type=hidden name=object_label value=''>\n";
		echo "<input type=hidden name=object_asset_no value=''>\n";
	}
	else
	{
		echo "<tr><td>&nbsp;</td><th class=tdright>Visible label:</th><td class=tdleft><input type=text name=object_label value='${object['label']}'></td></tr>\n";
		echo "<tr><td>&nbsp;</td><th class=tdright>Asset tag:</th><td class=tdleft><input type=text name=object_asset_no value='${object['asset_no']}'></td></tr>\n";
	}
	// parent selection
	if (objectTypeMayHaveParent ($object['objtype_id']))
	{
		$parents = getEntityRelatives ('parents', 'object', $object_id);
		foreach ($parents as $link_id => $parent_details)
		{
			if (!isset($label))
				$label = count($parents) > 1 ? 'Containers:' : 'Container:';
			echo "<tr><td>&nbsp;</td>";
			echo "<th class=tdright>${label}</th><td class=tdleft>";
			echo "<a href='".makeHref(array('page'=>'object', 'object_id'=>$parent_details['entity_id']))."'>${parent_details['name']}</a>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<a href='".
				makeHrefProcess(array(
					'op'=>'unlinkEntities', 
					'link_id'=>$link_id,
					'object_id'=>$object_id,
					'page='=>'object',
					'tab'=>'edit')).
			"'>";
			printImageHREF ('cut', 'Unlink container');
			echo "</a>";
			echo "</td></tr>\n";
			$label = '&nbsp;';
		}
		echo "<tr><td>&nbsp;</td>";
		echo "<th class=tdright>Select container:</th><td class=tdleft>";
		echo "<span";
		$helper_args = array ('object_id' => $object_id);
		$popup_args = 'height=700, width=400, location=no, menubar=no, '.
			'resizable=yes, scrollbars=yes, status=no, titlebar=no, toolbar=no';
		echo " onclick='window.open(\"" . makeHrefForHelper ('objlist', $helper_args);
		echo "\",\"findlink\",\"${popup_args}\");'>";
		printImageHREF ('attach', 'Select a container');
		echo "</span></td></tr>\n";
	}
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
	echo "<tr><td>&nbsp;</td><th class=tdright>Actions:</th><td class=tdleft>"; 
	echo "<a href='".
		makeHrefProcess(array('op'=>'deleteObject', 'page'=>'depot', 'tab'=>'addmore', 'object_id'=>$object_id)).
		"' onclick=\"javascript:return confirm('Are you sure you want to delete the object?')\">" . getImageHREF ('destroy', 'Delete object') . "</a>";
	echo "&nbsp;";
	echo "<a href='".
		makeHrefProcess(array ('op'=>'resetObject', 'page' => 'object', 'tab' => 'edit', 'object_id' => $object_id)).
		"' onclick=\"javascript:return confirm('Are you sure you want to reset most of object properties?')\">" . getImageHREF ('clear', 'Reset (cleanup) object') . "</a>";
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

// This is a clone of renderEditObjectForm().
function renderEditRackForm ($rack_id)
{
	global $pageno;
	$rack = spotEntity ('rack', $rack_id);
	amplifyCell ($rack);

	startPortlet ('Attributes');
	printOpFormIntro ('updateRack');
	echo '<table border=0 align=center>';
	echo "<tr><td>&nbsp;</td><th class=tdright>Rack row:</th><td class=tdleft>";
	printSelect (getRows(), array ('name' => 'row_id'), $rack['row_id']);
	echo "</td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Name (required):</th><td class=tdleft><input type=text name=name value='${rack['name']}'></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Height (required):</th><td class=tdleft><input type=text name=height value='${rack['height']}'></td></tr>\n";
	echo "<tr><td>&nbsp;</td><th class=tdright>Asset tag:</th><td class=tdleft><input type=text name=asset_no value='${rack['asset_no']}'></td></tr>\n";
	// optional attributes
	$values = getAttrValues ($rack_id);
	$num_attrs = count($values);
	$num_attrs = $num_attrs-1; // subtract for the 'height' attribute
	echo "<input type=hidden name=num_attrs value=${num_attrs}>\n";
	$i = 0;
	foreach ($values as $record)
	{
		// Skip the 'height' attribute as it's already displayed as a required field
		if ($record['id'] == 27)
			continue;
		echo "<input type=hidden name=${i}_attr_id value=${record['id']}>";
		echo '<tr><td>';
		if (strlen ($record['value']))
		{
			echo "<a href='".makeHrefProcess(array('op'=>'clearSticker', 'rack_id'=>$rack_id, 'attr_id'=>$record['id']))."'>";
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
	if (count ($rack['mountedObjects']) == 0)
	{
		echo "<tr><td>&nbsp;</td><th class=tdright>Actions:</th><td class=tdleft>"; 
		echo "<a href='".
			makeHrefProcess(array('op'=>'deleteRack', 'rack_id'=>$rack_id)).
			"' onclick=\"javascript:return confirm('Are you sure you want to delete the rack?')\">" . getImageHREF ('destroy', 'Delete rack') . "</a>";
		echo "&nbsp;</td></tr>\n";
	}
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

// used by renderGridForm() and renderRackPage()
function renderRackInfoPortlet ($rackData)
{
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>Rack row:</th><td class=tdleft>${rackData['row_name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Name:</th><td class=tdleft>${rackData['name']}</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Height:</th><td class=tdleft>${rackData['height']}</td></tr>\n";
	if (strlen ($rackData['asset_no']))
		echo "<tr><th width='50%' class=tdright>Asset tag:</th><td class=tdleft>${rackData['asset_no']}</td></tr>\n";
	if ($rackData['has_problems'] == 'yes')
		echo "<tr><td colspan=2 class=msg_error>Has problems</td></tr>\n";
	// Display populated attributes, but skip Height since it's already displayed above
	foreach (getAttrValues ($rackData['id']) as $record)
		if ($record['id'] != 27 && strlen ($record['value']))
		{
			echo "<tr><th width='50%' class=sticker>${record['name']}:</th><td class=sticker>" .
				formatAttributeValue ($record) .
				"</td></tr>\n";
		}
	echo "<tr><th width='50%' class=tdright>% used:</th><td class=tdleft>";
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
	addJS ('js/racktables.js');
	echo "<center>\n";
	echo "<table class=rack border=0 cellspacing=0 cellpadding=1>\n";
	echo "<tr><th width='10%'>&nbsp;</th>";
	echo "<th width='20%'><a href='javascript:;' oncontextmenu=\"blockToggleColumnOfAtoms('${rack_id}', '0', ${rackData['height']}); return false;\" onclick=\"toggleColumnOfAtoms('${rack_id}', '0', ${rackData['height']})\">Front</a></th>";
	echo "<th width='50%'><a href='javascript:;' oncontextmenu=\"blockToggleColumnOfAtoms('${rack_id}', '1', ${rackData['height']}); return false;\" onclick=\"toggleColumnOfAtoms('${rack_id}', '1', ${rackData['height']})\">Interior</a></th>";
	echo "<th width='20%'><a href='javascript:;' oncontextmenu=\"blockToggleColumnOfAtoms('${rack_id}', '2', ${rackData['height']}); return false;\" onclick=\"toggleColumnOfAtoms('${rack_id}', '2', ${rackData['height']})\">Back</a></th></tr>\n";
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
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	if (strlen ($info['name']))
		echo "<tr><th width='50%' class=tdright>Common name:</th><td class=tdleft>${info['name']}</td></tr>\n";
	elseif (considerConfiguredConstraint ($info, 'NAMEWARN_LISTSRC'))
		echo "<tr><td colspan=2 class=msg_error>Common name is missing.</td></tr>\n";
	echo "<tr><th width='50%' class=tdright>Object type:</th><td class=tdleft><a href='";
	echo makeHref (array (
		'page' => 'depot',
		'tab' => 'default',
		'cfe' => '{$typeid_' . $info['objtype_id'] . '}'
	));
	echo "'>" . decodeObjectType ($info['objtype_id'], 'o') . '</a></td></tr>';
	if (strlen ($info['label']))
		echo "<tr><th width='50%' class=tdright>Visible label:</th><td class=tdleft>${info['label']}</td></tr>\n";
	if (strlen ($info['asset_no']))
		echo "<tr><th width='50%' class=tdright>Asset tag:</th><td class=tdleft>${info['asset_no']}</td></tr>\n";
	elseif (considerConfiguredConstraint ($info, 'ASSETWARN_LISTSRC'))
		echo "<tr><td colspan=2 class=msg_error>Asset tag is missing.</td></tr>\n";
	if ($parents = getEntityRelatives ('parents', 'object', $object_id))
	{
		foreach ($parents as $parent)
		{
			if (!isset($label))
				$label = count($parents) > 1 ? 'Containers:' : 'Container:';
			echo "<tr><th width='50%' class=tdright>${label}</th><td class=tdleft>";
			echo "<a href='".makeHref(array('page'=>'object', 'object_id'=>$parent['entity_id']))."'>${parent['name']}</a>";
			echo "</td></tr>\n";
			$label = '&nbsp;';
		}
		unset ($label);
	}
	if ($children = getEntityRelatives ('children', 'object', $object_id))
	{
		foreach ($children as $child)
		{
			if (!isset($label))
				$label = 'Contains:';
			echo "<tr><th width='50%' class=tdright>${label}</th><td class=tdleft>";
			echo "<a href='".makeHref(array('page'=>'object', 'object_id'=>$child['entity_id']))."'>${child['name']}</a>";
			echo "</td></tr>\n";
			$label = '&nbsp;';
		}
	}
	if ($info['has_problems'] == 'yes')
		echo "<tr><td colspan=2 class=msg_error>Has problems</td></tr>\n";
	foreach (getAttrValues ($object_id) as $record)
		if
		(
			strlen ($record['value']) and 
			permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $record['id'])))
		)
			echo "<tr><th width='50%' class=sticker>${record['name']}:</th><td class=sticker>" .
				formatAttributeValue ($record) . "</td></tr>";
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

	if (count ($logrecords = getLogRecordsForObject ($_REQUEST['object_id'])))
	{
		startPortlet ('log records');
		echo "<table cellspacing=0 cellpadding=5 align=center class=widetable width='100%'>";
		$order = 'odd';
		foreach (getLogRecordsForObject ($_REQUEST['object_id']) as $row)
		{
			echo "<tr class=row_${order} valign=top>";
			echo '<td class=tdleft>' . $row['date'] . '<br>' . $row['user'] . '</td>';
			echo '<td class="slbconf rsvtext">' . string_insert_hrefs (htmlspecialchars ($row['content'], ENT_NOQUOTES)) . '</td>';
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
		{
			echo '<tr';
			if ($hl_port_id == $port['id'])
				echo ' class=port_highlight';
			$a_class = isEthernetPort ($port) ? 'port-menu' : '';
			echo "><td class='tdleft' NOWRAP><a name='port-${port['id']}' class='ancor interactive-portname nolink $a_class'>${port['name']}</a></td>";
			echo "<td class=tdleft>${port['label']}</td>";
			echo "<td class=tdleft>" . formatPortIIFOIF ($port) . "</td><td class=tdleft><tt>${port['l2address']}</tt></td>";
			if ($port['remote_object_id'])
			{
				echo "<td class=tdleft>" .
					formatPortLink ($port['remote_object_id'], $port['remote_object_name'], $port['remote_id'], NULL) . 
					"</td>";
				echo "<td class=tdleft>" . formatLoggedSpan ($port['last_log'], $port['remote_name'], 'underline') . "</td>";
				echo "<td class='tdleft rsvtext'>${port['cableid']}</td>";
			}
			else
				echo implode ('', formatPortReservation ($port)) . '<td></td>';
			echo "</tr>";
		}
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
			echo "<tr><th>OS interface</th><th>IP address</th><th>network</th><th>routed by</th><th>peers</th></tr>\n";
		else
			echo "<tr><th>OS interface</th><th>IP address</th><th>peers</th></tr>\n";

		// group IP allocations by interface name instead of address family
		$allocs_by_iface = array();
		foreach (array ('ipv4', 'ipv6') as $ip_v)
			foreach ($info[$ip_v] as $dottedquad => $alloc)
				$allocs_by_iface[$alloc['osif']][$dottedquad] = $alloc;
				
		// sort allocs array by portnames
		foreach (sortPortList ($allocs_by_iface) as $iface_name => $alloclist)
		{
			$is_first_row = TRUE;
			foreach ($alloclist as $alloc)
			{
				$rendered_alloc = getRenderedAlloc ($object_id, $alloc);
				echo "<tr class='${rendered_alloc['tr_class']}' valign=top>";

				// display iface name, same values are grouped into single cell
				if ($is_first_row)
				{
					$rowspan = count ($alloclist) > 1 ? 'rowspan="' . count ($alloclist) . '"' : '';
					echo "<td class=tdleft $rowspan>$iface_name</td>";
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
		echo "<tr><th>VS</th><th>RS pool</th><th>RS</th><th>VS config</th><th>RS config</th><th>Prio</th></tr>\n";
		foreach ($pools as $vs_id => $vs_info)
		{
 			echo "<tr valign=top class=row_${order}><td class=tdleft>";
 			renderCell (spotEntity ('ipv4vs', $vs_id));
 			echo "</td><td class=tdleft>";
 			renderCell (spotEntity ('ipv4rspool', $vs_info['pool_id']));
 			echo '</td><td class=tdleft>' . $vs_info['rscount'] . '</td>';
 			echo "<td class=slbconf>${vs_info['vsconfig']}</td>";
 			echo "<td class=slbconf>${vs_info['rsconfig']}</td>";
			echo "<td class=slbconf>${vs_info['prio']}</td>";
			echo "</tr>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n";
		finishPortlet();
	}
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
		echo "</td><td class='tdleft'><input type=text size=8 name=port_name tabindex=100></td>\n";
		echo "<td><input type=text name=port_label tabindex=101></td><td>";
		printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type_id', 'tabindex' => 102), $prefs['selected']);
		echo "<td><input type=text name=port_l2address tabindex=103 size=18 maxlength=24></td>\n";
		echo "<td colspan=3>&nbsp;</td><td>";
		printImageHREF ('add', 'add a port', TRUE, 104);
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
		echo "</td><td><input type=text size=8 name=port_name tabindex=105></td>\n";
		echo "<td><input type=text name=port_label tabindex=106></td><td>";
		printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type_id', 'tabindex' => 107), $prefs['selected']);
		echo "<td><input type=text name=port_numbering_start tabindex=108 size=3 maxlength=3></td>\n";
		echo "<td><input type=text name=port_numbering_count tabindex=109 size=3 maxlength=3></td>\n";
		echo "<td>&nbsp;</td><td>";
		printImageHREF ('add', 'add ports', TRUE, 110);
		echo "</td></tr></form>";
		echo "</table><br>\n";
	}
	
	echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
	echo "<tr><th>&nbsp;</th><th class=tdleft>Local name</th><th class=tdleft>Visible label</th><th class=tdleft>Interface</th><th class=tdleft>L2 address</th>";
	echo "<th class=tdcenter colspan=2>Cable, Remote object and port</th><th class=tdcenter>(Un)link or (un)reserve</th><th>&nbsp;</th></tr>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($prefs);

	// clear ports link
	echo "<a href='".
		makeHrefProcess(array ('op'=>'deleteAll', 'page' => 'object', 'tab' => 'ports', 'object_id' => $object_id)).
		"' onclick=\"javascript:return confirm('Are you sure you want to delete all existing ports?')\">" . getImageHREF ('clear', 'Clear port list') . " Clear port list</a>";

	if (isset ($_REQUEST['hl_port_id']))
	{
		assertUIntArg ('hl_port_id');
		$hl_port_id = intval ($_REQUEST['hl_port_id']);
		addAutoScrollScript ("port-$hl_port_id");
	}
	switchportInfoJS ($object_id); // load JS code to make portnames interactive
	foreach ($object['ports'] as $port)
	{
		$tr_class = isset ($hl_port_id) && $hl_port_id == $port['id'] ? 'class="port_highlight"' : '';
		printOpFormIntro ('editPort', array ('port_id' => $port['id']));
		echo "<tr $tr_class><td><a name='port-${port['id']}' href='".makeHrefProcess(array('op'=>'delPort', 'port_id'=>$port['id'], 'object_id'=>$object_id))."'>";
		printImageHREF ('delete', 'Unlink and Delete this port');
		echo "</a></td>\n";
		$a_class = isEthernetPort ($port) ? 'port-menu' : '';
		echo "<td class='tdleft' NOWRAP><input type=text name=name class='interactive-portname $a_class' value='${port['name']}' size=8></td>";
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
			echo formatPortIIFOIF ($port) . '</td>';
		}
		// 18 is enough to fit 6-byte MAC address in its longest form,
		// while 24 should be Ok for WWN
		echo "<td><input type=text name=l2address value='${port['l2address']}' size=18 maxlength=24></td>\n";
		if ($port['remote_object_id'])
		{
			echo "<td>${port['cableid']} " .
				formatLoggedSpan ($port['last_log'], formatPortLink ($port['remote_object_id'], $port['remote_object_name'], $port['remote_id'], NULL)) .
				"</td>";
			echo "<td> " . formatLoggedSpan ($port['last_log'], $port['remote_name'], 'underline') .
				"<input type=hidden name=reservation_comment value=''></td>";
			echo "<td class=tdcenter><a href='".
				makeHrefProcess(array(
					'op'=>'unlinkPort', 
					'port_id'=>$port['id'],
					'object_id'=>$object_id)).
			"'>";
			printImageHREF ('cut', 'Unlink this port');
			echo "</a></td>";
		}
		elseif (strlen ($port['reservation_comment']))
		{
			echo "<td>" . formatLoggedSpan ($port['last_log'], 'Reserved:', 'strong underline') . "</td>";
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
			);
			$popup_args = 'height=700, width=400, location=no, menubar=no, '.
				'resizable=yes, scrollbars=yes, status=no, titlebar=no, toolbar=no';
			echo " ondblclick='window.open(\"" . makeHrefForHelper ('portlist', $helper_args);
			echo "\",\"findlink\",\"${popup_args}\");'";
			// end of onclick=
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
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes' && getConfigVar('ENABLE_BULKPORT_FORM') == 'yes'){
		echo "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
		echo "<tr><th>&nbsp;</th><th class=tdleft>Local name</th><th class=tdleft>Visible label</th><th class=tdleft>Interface</th><th class=tdleft>Start Number</th>";
		echo "<th class=tdleft>Count</th><th>&nbsp;</th></tr>\n";
		printOpFormIntro ('addBulkPorts'); 
		echo "<tr><td>";
		printImageHREF ('add', 'add ports', TRUE);
		echo "</td><td><input type=text size=8 name=port_name tabindex=105></td>\n";
		echo "<td><input type=text name=port_label tabindex=106></td><td>";
		printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type_id', 'tabindex' => 107), $prefs['selected']);
		echo "<td><input type=text name=port_numbering_start tabindex=108 size=3 maxlength=3></td>\n";
		echo "<td><input type=text name=port_numbering_count tabindex=109 size=3 maxlength=3></td>\n";
		echo "<td>&nbsp;</td><td>";
		printImageHREF ('add', 'add ports', TRUE, 110);
		echo "</td></tr></form>";
		echo "</table><br>\n";
	}
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

function renderIPTabForObject ($object_id, $ip_v)
{
	function getOpnameByIPFamily ($opname, $ip_v)
	{
		// do not assemble opnames from the peaces to be able to grep the code by opnames
		switch ($opname . '-'. $ip_v)
		{
			case 'add-4': return 'addIPv4Allocation';
			case 'add-6': return 'addIPv6Allocation';
			case 'upd-4': return 'updIPv4Allocation';
			case 'upd-6': return 'updIPv6Allocation';
			case 'del-4': return 'delIPv4Allocation';
			case 'del-6': return 'delIPv6Allocation';
			default: throw new InvalidArgException ('$opname or $ip_v', "$opname or $ip_v");
		}
	}
	function printNewItemTR ($ip_v, $default_type)
	{
		global $aat;
		printOpFormIntro (getOpnameByIPFamily ('add', $ip_v));
		echo "<tr><td>";
		printImageHREF ('add', 'allocate', TRUE);
		echo "</td>";
		echo "<td class=tdleft><input type='text' size='10' name='bond_name' tabindex=100></td>\n";
		echo "<td class=tdleft><input type=text name='ip' tabindex=101></td>\n";
		echo "<td colspan=2>&nbsp;</td><td>";
		printSelect ($aat, array ('name' => 'bond_type', 'tabindex' => 102), $default_type);
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

	$alloc_list = ''; // most of the output is stored here
	$used_alloc_types = array();
	foreach ($focus['ipv' . $ip_v] as $alloc) // ['ipv4'] or ['ipv6']
	{
		if (! isset ($used_alloc_types[$alloc['type']]))
			$used_alloc_types[$alloc['type']] = 0;
		$used_alloc_types[$alloc['type']]++;

		$rendered_alloc = getRenderedAlloc ($object_id, $alloc);
		$alloc_list .= getOutputOf ('printOpFormIntro', getOpnameByIPFamily ('upd', $ip_v), array ('ip' => $alloc['addrinfo']['ip']));
		$alloc_list .= "<tr class='${rendered_alloc['tr_class']}' valign=top>";

		$alloc_list .= "<td><a href='" .
			makeHrefProcess
			(
				array
				(
					'op' => getOpnameByIPFamily ('del', $ip_v),
					'ip' => $alloc['addrinfo']['ip'],
					'object_id' => $object_id
				)
			) . "'>" . 
			getImageHREF ('delete', 'Delete this IP address') .
			"</a></td>";
		$alloc_list .= "<td class=tdleft><input type='text' name='bond_name' value='${alloc['osif']}' size=10></td>";
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
	$most_popular_type = empty ($used_alloc_types) ? 'regular' : array_pop (array_keys ($used_alloc_types));

	if ($list_on_top = (getConfigVar ('ADDNEW_AT_TOP') != 'yes'))
		echo $alloc_list;
	printNewItemTR ($ip_v, $most_popular_type);
	if (! $list_on_top)
		echo $alloc_list;

	echo "</table><br>\n";
	finishPortlet();
}

function renderIPv4ForObject ($object_id)
{
	renderIPTabForObject ($object_id, '4');
}

function renderIPv6ForObject ($object_id)
{
	renderIPTabForObject ($object_id, '6');
}

// This function is deprecated. Do not rely on its internals,
// it will probably be removed in the next major relese.
// Use new showError, showWarning, showSuccess functions.
// Log array is stored in $_SESSION['log']. Its format is simple: plain ordered array 
// with values having keys 'c' (both message code and severity) and 'a' (sprintf arguments array)
function showMessageOrError ()
{
	if (empty ($_SESSION['log']))
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
		41 => array ('code' => 'success', 'format' => 'uplink ports reverb queued'),
		43 => array ('code' => 'success', 'format' => 'Saved successfully.'),
		44 => array ('code' => 'success', 'format' => '%s failures and %s successfull changes.'),
		48 => array ('code' => 'success', 'format' => 'added a record successfully'),
		49 => array ('code' => 'success', 'format' => 'deleted a record successfully'),
		51 => array ('code' => 'success', 'format' => 'updated a record successfully'),
		57 => array ('code' => 'success', 'format' => 'Reset complete'),
		63 => array ('code' => 'success', 'format' => '%u change request(s) have been processed'),
		67 => array ('code' => 'success', 'format' => "Tag rolling done, %u objects involved"),
		71 => array ('code' => 'success', 'format' => 'File "%s" was linked successfully'),
		72 => array ('code' => 'success', 'format' => 'File was unlinked successfully'),
		81 => array ('code' => 'success', 'format' => "SNMP: completed '%s' work"),
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
		206 => array ('code' => 'warning', 'format' => 'Rack is not empty'),

// records 300~399 with notices
		300 => array ('code' => 'neutral', 'format' => '%s'),

	);
	// Handle the arguments. Is there any better way to do it?
	foreach ($_SESSION['log'] as $record)
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
	unset ($_SESSION['log']);
}

// renders two tables: port link status and learned MAC list
function renderPortsInfo($object_id)
{
	global $nextorder;
	echo "<table width='100%'><tr>";
	
	if (permitted (NULL, NULL, 'get_link_status'))
	{
		try
		{
			$linkStatus = queryDevice ($object_id, 'getportstatus');
		}
		catch (RackTablesError $e) {}
		if (! empty ($linkStatus))
		{
			echo "<td valign='top' width='50%'>";
			startPortlet('Link status');
			echo "<table width='80%' class='widetable' cellspacing=0 cellpadding='5px' align='center'><tr><th>Port<th>Link status<th>Link info</tr>";
			$order = 'even';
			foreach ($linkStatus as $pn => $link)
			{
				echo "<tr class='row_$order'>";
				$order = $nextorder[$order];
				echo '<td>' . $pn;
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
	}

	try
	{
		$macList = sortPortList (queryDevice ($object_id, 'getmaclist'));
	}
	catch (RackTablesError $e) {}	
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

	// Get a list of all of this object's parents,
	// then trim the list to only include parents which are racks
	$objectParents = getEntityRelatives('parents', 'object', $object_id);
	$parentRacks = array();
	foreach ($objectParents as $parentData)
		if ($parentData['entity_type'] == 'rack')
			$parentRacks[] = $parentData['entity_id'];

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
		$object = spotEntity ('object', $object_id);
		$matched_tags = array();
		foreach ($allRacksData as $rack)
			foreach ($object['etags'] as $tag)
				if (tagOnChain ($tag, $rack['etags']) or tagOnChain ($tag, $rack['itags']))
				{
					$matching_racks[$rack['id']] = $rack;
					$matched_tags[$tag['id']] = $tag;
					break;
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
	startPortlet ('Comment');
	echo "<textarea name=comment rows=10 cols=40></textarea><br>\n";
	echo "<input type=submit value='Save' name=got_atoms>\n";
	echo "<br><br>";
	finishPortlet();
	echo "</td>";

	// Right portlet with rendered racks. If this form submit is not final, we have to
	// reflect the former state of the grid in current form.
	echo "<td class=pcright rowspan=2 height='1%'>";
	startPortlet ('Working copy');
	addJS ('js/racktables.js');
	echo '<table border=0 cellspacing=10 align=center><tr>';
	foreach ($workingRacksData as $rack_id => $rackData)
	{
		// Order is important here: only original allocation is highlighted.
		highlightObject ($rackData, $object_id);
		markupAtomGrid ($rackData, 'T');
		// If we have a form processed, discard user input and show new database
		// contents.
		if (isset ($_REQUEST['rackmulti'][0])) // is an update
			mergeGridFormToRack ($rackData);
		echo "<td valign=top>";
		echo "<center>\n<h2>${rackData['name']}</h2>\n";
		echo "<table class=rack border=0 cellspacing=0 cellpadding=1>\n";
		echo "<tr><th width='10%'>&nbsp;</th>";
		echo "<th width='20%'><a href='javascript:;' oncontextmenu=\"blockToggleColumnOfAtoms('${rack_id}', '0', ${rackData['height']}); return false;\" onclick=\"toggleColumnOfAtoms('${rack_id}', '0', ${rackData['height']})\">Front</a></th>";
		echo "<th width='50%'><a href='javascript:;' oncontextmenu=\"blockToggleColumnOfAtoms('${rack_id}', '1', ${rackData['height']}); return false;\" onclick=\"toggleColumnOfAtoms('${rack_id}', '1', ${rackData['height']})\">Interior</a></th>";
		echo "<th width='20%'><a href='javascript:;' oncontextmenu=\"blockToggleColumnOfAtoms('${rack_id}', '2', ${rackData['height']}); return false;\" onclick=\"toggleColumnOfAtoms('${rack_id}', '2', ${rackData['height']})\">Back</a></th></tr>\n";
		renderAtomGrid ($rackData);
		echo "<tr><th width='10%'>&nbsp;</th>";
		echo "<th width='20%'><a href='javascript:;' oncontextmenu=\"blockToggleColumnOfAtoms('${rack_id}', '0', ${rackData['height']}); return false;\" onclick=\"toggleColumnOfAtoms('${rack_id}', '0', ${rackData['height']})\">Front</a></th>";
		echo "<th width='50%'><a href='javascript:;' oncontextmenu=\"blockToggleColumnOfAtoms('${rack_id}', '1', ${rackData['height']}); return false;\" onclick=\"toggleColumnOfAtoms('${rack_id}', '1', ${rackData['height']})\">Interior</a></th>";
		echo "<th width='20%'><a href='javascript:;' oncontextmenu=\"blockToggleColumnOfAtoms('${rack_id}', '2', ${rackData['height']}); return false;\" onclick=\"toggleColumnOfAtoms('${rack_id}', '2', ${rackData['height']})\">Back</a></th></tr>\n";
		echo "</table>\n<br>\n";
		// Determine zero-u checkbox status.
		// If form has been submitted, use form data, otherwise use DB data.
		if (isset($_REQUEST['op'])) 
			$checked = isset($_REQUEST['zerou_'.$rack_id]) ? 'checked' : '';
		else
			$checked = in_array($rack_id, $parentRacks) ? 'checked' : '';
		echo "Zero-U: <input type=checkbox ${checked} name=zerou_${rack_id} id=zerou_${rack_id}>";
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
	addAtomCSS();
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
	$objects = filterCellList (listCells ('object'), $cellfilter['expression']);

	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";

	if (! renderEmptyResults ($cellfilter, 'objects', count($objects)))
	{
		if (count($objects) > 0)
		{
			startPortlet ('Objects (' . count ($objects) . ')');
			echo '<br><br><table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
			echo '<tr><th>Common name</th><th>Visible label</th><th>Asset tag</th><th>Row/Rack</th></tr>';
			$order = 'odd';
			# gather IDs of all objects and fetch rackspace info in one pass
			$idlist = array();
			foreach ($objects as $obj)
				$idlist[] = $obj['id'];
			$mountinfo = getMountInfo ($idlist);
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
				$places = array();
				if (! array_key_exists ($obj['id'], $mountinfo))
					$places[] = 'Unmounted';
				else
					foreach ($mountinfo[$obj['id']] as $mi)
						$places[] = mkA ($mi['row_name'], 'row', $mi['row_id']) . '/' . mkA ($mi['rack_name'], 'rack', $mi['rack_id']);
				echo "<td class='${secondclass}'>" . implode (', ', $places) . '</td>';
				echo '</tr>';
				$order = $nextorder[$order];
			}
			echo '</table>';
			finishPortlet();
		}
		else
			echo '<h2>No objects exist</h2>';
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
		echo '</td><td>' . niftyString ($row['comment'], 0) . '</td></tr>';
		$order = $nextorder[$order];
	}
	echo "</table>\n";
	finishPortlet();

	echo '</td></tr></table>';
}

function renderIPv4SpaceRecords ($tree, $baseurl, $target = 0, $knight, $level = 1)
{
	$self = __FUNCTION__;
	static $vdomlist = NULL;
	if ($vdomlist == NULL and getConfigVar ('IPV4_TREE_SHOW_VLAN') == 'yes')
		$vdomlist = getVLANDomainOptions();

	// scroll page to the highlighted item
	if ($target && isset ($_REQUEST['hl_net']))
		addAutoScrollScript ("net-$target");

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
			$decor = array ('indent' => $level);
			if ($item['symbol'] == 'node-collapsed')
				$decor['symbolurl'] = "${baseurl}&eid=" . $item['id'];
			elseif ($item['symbol'] == 'node-expanded')
				$decor['symbolurl'] = $baseurl . ($item['parent_id'] ? "&eid=${item['parent_id']}" : '');
			echo "<tr valign=top>";
			if ($target == $item['id'] && isset ($_REQUEST['hl_net']))
				$decor['tdclass'] = 'port_highlight';
			printIPNetInfoTDs ($item, $decor);
			echo "<td class=tdcenter>";
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
						// FIXME: would formatVLANName() do this?
						echo $binding['vlan_id'] . '@' . niftyString ($vdomlist[$binding['domain_id']], 15) . '</a></li>';
					}
					echo '</ul>';
				}
				echo '</td>';
			}
			if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
				printRoutersTD (findRouters ($item['addrlist']), getConfigVar ('IPV4_TREE_RTR_AS_CELL'));
			echo "</tr>";
			if ($item['symbol'] == 'node-expanded' or $item['symbol'] == 'node-expanded-static')
				$self ($item['kids'], $baseurl, $target, $knight, $level + 1);
		}
		else
		{
			echo "<tr valign=top>";
			printIPNetInfoTDs ($item, array ('indent' => $level, 'knight' => $knight, 'tdclass' => 'sparenetwork'));
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

function renderIPv6SpaceRecords ($tree, $baseurl, $target = 0, $knight, $level = 1)
{
	$self = __FUNCTION__;
	static $vdomlist = NULL;
	if ($vdomlist == NULL and getConfigVar ('IPV4_TREE_SHOW_VLAN') == 'yes')
		$vdomlist = getVLANDomainOptions();

	// scroll page to the highlighted item
	if ($target && isset ($_REQUEST['hl_net']))
		addAutoScrollScript ("net-$target");

	foreach ($tree as $item)
	{
		if (getConfigVar ('IPV4_TREE_SHOW_USAGE') == 'yes')
			loadIPv6AddrList ($item); // necessary to compute router list and address counter
		else
		{
			$item['addrlist'] = array();
			$item['addrc'] = 0;
		}
		if (isset ($item['id']))
		{
			$decor = array ('indent' => $level);
			if ($item['symbol'] == 'node-collapsed')
				$decor['symbolurl'] = "${baseurl}&eid=" . $item['id'];
			elseif ($item['symbol'] == 'node-expanded')
				$decor['symbolurl'] = $baseurl . ($item['parent_id'] ? "&eid=${item['parent_id']}#net6id${item['parent_id']}" : '');
			echo "<tr valign=top>";
			if ($target == $item['id'] && isset ($_REQUEST['hl_net']))
				$decor['tdclass'] = ' port_highlight';
			printIPNetInfoTDs ($item, $decor);
			echo "<td class=tdcenter>";
			// show net usage
			echo formatIPv6NetUsage ($item['addrc'], $item['mask']);
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
						// FIXME: would formatVLANName() do this?
						echo $binding['vlan_id'] . '@' . niftyString ($vdomlist[$binding['domain_id']], 15) . '</a></li>';
					}
					echo '</ul>';
				}
				echo '</td>';
			}
			if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
				printRoutersTD (findRouters ($item['addrlist']), getConfigVar ('IPV4_TREE_RTR_AS_CELL'));
			echo "</tr>";
			if ($item['symbol'] == 'node-expanded' or $item['symbol'] == 'node-expanded-static')
				$self ($item['kids'], $baseurl, $target, $knight, $level + 1);
		}
		/* do not display spare networks
		else
		{ // display spare networks
			echo "<tr valign=top>";
			printIPNetInfoTDs ($item, array ('indent' => $level, 'knight' => $knight, 'tdclass' => 'sparenetwork'));
			echo "<td class=tdcenter>";
			echo formatIPv6NetUsage ($item['addrc'], $item['mask']);
			if (getConfigVar ('IPV4_TREE_SHOW_VLAN') == 'yes')
				echo '</td><td>&nbsp;</td>';
			echo "</td><td>&nbsp;</td></tr>";
		}*/
	}
}

// if $used is NULL, returns only human-formatted mask.
// Otherwise returns "$used in/of " . human-formatted-mask
function formatIPv6NetUsage ($used, $mask)
{
	$prefixes = array
	(
		0 =>  '',
		3 =>  'k',
		6 =>  'M',
		9 =>  'G',
		12 => 'T',
		15 => 'P',
		18 => 'E',
		21 => 'Z',
		24 => 'Y',
	);

	if ($mask <= 64)
	{
		$what = '/64 net';
		$preposition = 'in';
		$mask += 64;
	}
	else
	{
		$what = 'IP';
		$preposition = 'of';
	}
	$what .= (0 == $mask % 64 ? '' : 's');
	$addrc = isset ($used) ? "$used $preposition " : '';

	$dec_order = intval ((128 - $mask) / 10) * 3;
	$mult = isset ($prefixes[$dec_order]) ? $prefixes[$dec_order] : '??';
	
	$cnt = 1 << ((128 - $mask) % 10);
	if ($cnt == 1 && $mult == '')
		$cnt = 'single';

	return "<small>${addrc}${cnt}${mult} ${what}</small>";
}

function renderIPv4Space ()
{
	global $pageno, $tabno;
	$cellfilter = getCellFilter();
	$netlist = listCells ('ipv4net');
	$allcount = count ($netlist);
	$netlist = filterCellList ($netlist, $cellfilter['expression']);
	array_walk ($netlist, 'amplifyCell');

	$netcount = count ($netlist);
	// expand request can take either natural values or "ALL". Zero means no expanding.
	$eid = isset ($_REQUEST['eid']) ? $_REQUEST['eid'] : 0;
	$tree = prepareIPv4Tree ($netlist, $eid);

	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";
	if (! renderEmptyResults($cellfilter, 'IPv4 nets', count($tree)))
	{
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
		renderIPv4SpaceRecords ($tree, $baseurl, $eid, $netcount == $allcount and getConfigVar ('IPV4_ENABLE_KNIGHT') == 'yes');
		echo "</table>\n";
		finishPortlet();
	}

	echo '</td><td class=pcright>';
	renderCellFilterPortlet ($cellfilter, 'ipv4net', $netlist);
	echo "</td></tr></table>\n";
}

function renderIPv6Space ()
{
	global $pageno, $tabno;
	$cellfilter = getCellFilter();
	$netlist = listCells ('ipv6net');
	$allcount = count ($netlist);
	$netlist = filterCellList ($netlist, $cellfilter['expression']);
	array_walk ($netlist, 'amplifyCell');

	$netcount = count ($netlist);
	// expand request can take either natural values or "ALL". Zero means no expanding.
	$eid = isset ($_REQUEST['eid']) ? $_REQUEST['eid'] : 0;
	$tree = prepareIPv6Tree ($netlist, $eid);

	echo "<table border=0 class=objectview>\n";
	echo "<tr><td class=pcleft>";
	if (! renderEmptyResults($cellfilter, 'IPv6 nets', count($tree)))
	{
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
			$netinfo = spotEntity ('ipv6net', $eid);
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
		renderIPv6SpaceRecords ($tree, $baseurl, $eid, $netcount == $allcount and getConfigVar ('IPV4_ENABLE_KNIGHT') == 'yes');
		echo "</table>\n";
		finishPortlet();
	}

	echo '</td><td class=pcright>';
	renderCellFilterPortlet ($cellfilter, 'ipv6net', $netlist);
	echo "</td></tr></table>\n";
}

function renderSLBDefConfig()
{
	$defaults = getSLBDefaults ();
	startPortlet ('SLB default configs');
	echo '<table cellspacing=0 cellpadding=5 align=center>';
	printOpFormIntro ('save');
	echo '<tr><th class=tdright>VS config</th><td colspan=2><textarea tabindex=103 name=vsconfig rows=10 cols=80>' . htmlspecialchars($defaults['vs']) . '</textarea></td>';
	echo '<td rowspan=2>';
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo '</td></tr>';
	echo '<tr><th class=tdright>RS config</th><td colspan=2><textarea tabindex=104 name=rsconfig rows=10 cols=80>' . htmlspecialchars($defaults['rs']) . '</textarea></td></tr>';
	echo '</form></table>';
	finishPortlet();
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
	addJs ('js/live_validation.js');
	$regexp = addslashes ('^(\d{1,3}\.){3}\d{1,3}/\d{1,2}$');
	addJs (<<<END
$(document).ready(function () {
	document.add_new_range.range.setAttribute('match', '$regexp');
	Validate.init();
});
END
	, TRUE);

	function printNewItemTR ()
	{
		startPortlet ('Add new');
		echo '<table border=0 cellpadding=10 align=center>';
		// This form requires a name, so JavaScript validator can find it.
		// No printOpFormIntro() hence
		echo "<form method=post name='add_new_range' action='".makeHrefProcess()."'>\n";
		echo "<input type=hidden name=op value=addIPv4Prefix>\n";
		// tags column
		echo '<tr><td rowspan=5><h3>assign tags</h3>';
		renderNewEntityTags ('ipv4net');
		echo '</td>';
		// inputs column
		$prefix_value = empty ($_REQUEST['set-prefix']) ? '' : $_REQUEST['set-prefix'];
		echo "<th class=tdright>prefix</th><td class=tdleft><input type=text name='range' size=18 class='live-validate' tabindex=10 value='${prefix_value}'></td>";
		echo '<tr><th class=tdright>VLAN</th><td class=tdleft>';
		echo getOptionTree ('vlan_ck', getAllVLANOptions(), array ('select_class' => 'vertical', 'tabindex' => 20)) . '</td></tr>';
		echo "<tr><th class=tdright>name</th><td class=tdleft><input type=text name='name' size='20' tabindex=30></td></tr>";
		echo '<tr><td class=tdright><input type=checkbox name="is_bcast" tabindex=40></td><th class=tdleft>reserve network and router addresses</th></tr>';
		echo "<tr><td colspan=2>";
		printImageHREF ('CREATE', 'Add a new network', TRUE, 50);
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
		array_walk ($addrspaceList, 'amplifyCell');
		$tree = prepareIPv4Tree ($addrspaceList, 'ALL');
		// this is only called for having "trace" set
		treeFromList ($addrspaceList);
		foreach ($addrspaceList as $netinfo)
		{
			$netinfo = peekNode ($tree, $netinfo['trace'], $netinfo['id']);
			// now we have all subnets listed in netinfo
			loadIPv4AddrList ($netinfo);
			$used = $netinfo['addrc'];
			$maxdirect = $netinfo['addrt'];
			$maxtotal = binInvMaskFromDec ($netinfo['mask']) + 1;
			echo "<tr valign=top><td>";
			if (count ($netinfo['addrlist']) && getConfigVar ('IPV4_JAYWALK') == 'no')
				printImageHREF ('nodestroy', 'There are ' . count ($netinfo['addrlist']) . ' allocations inside');
			else
			{
				echo "<a href='".makeHrefProcess(array('op'=>'delIPv4Prefix', 'id'=>$netinfo['id']))."'>";
				printImageHREF ('destroy', 'Delete this prefix');
				echo "</a>";
			}
			echo '</td><td class=tdleft><a href="' . makeHref (array ('page' => 'ipv4net', 'id' => $netinfo['id'])) . '">';
			echo "${netinfo['ip']}/${netinfo['mask']}</a></td>";
			echo '<td class=tdleft>' . niftyString ($netinfo['name']);
			if (count ($netinfo['etags']))
				echo '<br><small>' . serializeTags ($netinfo['etags']) . '</small>';
			echo '</td><td>';
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

function renderIPv6SpaceEditor ()
{
	// IPv6 validator
	addJs ('js/live_validation.js');
	$regexp = addslashes ('^[a-fA-F0-9:]*:[a-fA-F0-9:\.]*/\d{1,3}$');
	addJs (<<<END
$(document).ready(function () {
	document.add_new_range.range.setAttribute('match', '$regexp');
	Validate.init();
});
END
	, TRUE);

	function printNewItemTR ()
	{
		startPortlet ('Add new');
		echo '<table border=0 cellpadding=10 align=center>';
		// This form requires a name, so JavaScript validator can find it.
		// No printOpFormIntro() hence
		echo "<form method=post name='add_new_range' action='".makeHrefProcess()."'>\n";
		echo "<input type=hidden name=op value=addIPv6Prefix>\n";
		// tags column
		echo '<tr><td rowspan=5><h3>assign tags</h3>';
		renderNewEntityTags ('ipv4net');
		echo '</td>';
		// inputs column
		$prefix_value = empty ($_REQUEST['set-prefix']) ? '' : $_REQUEST['set-prefix'];
		echo "<th class=tdright>prefix</th><td class=tdleft><input type=text name='range' size=36 class='live-validate' tabindex=10 value='${prefix_value}'></td>";
		echo '<tr><th class=tdright>VLAN</th><td class=tdleft>';
		echo getOptionTree ('vlan_ck', getAllVLANOptions(), array ('select_class' => 'vertical', 'tabindex' => 20)) . '</td></tr>';
		echo "<tr><th class=tdright>name</th><td class=tdleft><input type=text name='name' size='20' tabindex=30></td></tr>";
		echo '<tr><td class=tdright><input type=checkbox name="is_connected" tabindex=40></td><th class=tdleft>reserve subnet-router anycast address</th></tr>';
		echo "<tr><td colspan=2>";
		printImageHREF ('CREATE', 'Add a new network', TRUE, 50);
		echo '</td></tr>';
		echo "</form></table><br><br>\n";
		finishPortlet();
	}

	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	if (count ($addrspaceList = listCells ('ipv6net')))
	{
		startPortlet ('Manage existing (' . count ($addrspaceList) . ')');
		echo "<table class='widetable' border=0 cellpadding=5 cellspacing=0 align='center'>\n";
		echo "<tr><th>&nbsp;</th><th>prefix</th><th>name</th><th>capacity</th></tr>";
		array_walk ($addrspaceList, 'amplifyCell');
		$tree = prepareIPv6Tree ($addrspaceList, 'ALL');
		// this is only called for having "trace" set
		treeFromList ($addrspaceList);
		foreach ($addrspaceList as $netinfo)
		{
			$netinfo = peekNode ($tree, $netinfo['trace'], $netinfo['id']);
			// now we have all subnets listed in netinfo
			loadIPv6AddrList ($netinfo);
			echo "<tr valign=top><td>";
			if (count ($netinfo['addrlist']) && getConfigVar ('IPV4_JAYWALK') == 'no')
				printImageHREF ('nodestroy', 'There are ' . count ($netinfo['addrlist']) . ' allocations inside');
			else
			{
				echo "<a href='".makeHrefProcess (array	('op' => 'delIPv6Prefix', 'id' => $netinfo['id'])) . "'>";
				printImageHREF ('destroy', 'Delete this prefix');
				echo "</a>";
			}
			echo '</td><td class=tdleft><a href="' . makeHref (array ('page' => 'ipv6net', 'id' => $netinfo['id'])) . '">';
			echo "${netinfo['ip']}/${netinfo['mask']}</a></td>";
			echo '<td class=tdleft>' . niftyString ($netinfo['name']);
			if (count ($netinfo['etags']))
				echo '<br><small>' . serializeTags ($netinfo['etags']) . '</small>';
			echo '</td><td>';
			echo formatIPv6NetUsage ($netinfo['addrc'], $netinfo['mask']);
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
		echo formatVLANName ($vlaninfo, 'markup long');
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

	if (isset ($_REQUEST['hl_ipv4_addr']))
	{
		$hl_ip = ip2long ($_REQUEST['hl_ipv4_addr']);
		$hl_dottedquad = ip_long2quad ($hl_ip);
		addAutoScrollScript ("ip-$hl_dottedquad"); // scroll page to highlighted ip
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
			echo '<h3>' . long2ip ($startip) . ' ~ ' . long2ip ($endip) . '</h3>';
			for ($i = 0; $i < $numpages; $i++)
				if ($i == $page)
					$rendered_pager .= "<b>$i</b> ";
				else
					$rendered_pager .= "<a href='".makeHref (array ('page' => $pageno, 'tab' => $tabno, 'id' => $id, 'pg' => $i)) . "'>$i</a> ";
		}
		$startip = $startip + $page * $maxperpage;
		$endip = min ($startip + $maxperpage - 1, $endip);
	}

	echo $rendered_pager;
	echo "<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center' width='100%'>\n";
	echo "<tr><th>Address</th><th>Name</th><th>Allocation</th></tr>\n";

	for ($ip = $startip; $ip <= $endip; $ip++) :
		$dottedquad = ip_long2quad($ip);
		$secondstyle = 'tdleft' . (isset ($hl_ip) && $hl_ip == $ip ? ' port_highlight' : '');
		if (!isset ($range['addrlist'][$ip]))
		{
			echo "<tr><td class=tdleft><a class='ancor' name='ip-$dottedquad' href='" . makeHref(array('page'=>'ipaddress', 'ip' => $dottedquad)) . "'>$dottedquad</a></td>";
			echo "<td class='rsv-port ${secondstyle}'><span class='rsvtext'></span></td><td class='${secondstyle}'>&nbsp;</td></tr>\n";
			continue;
		}
		$addr = $range['addrlist'][$ip];
		// render IP change history
		$title = '';
		$history_class = '';
		if (isset ($addr['last_log']))
		{
			$title = ' title="' . htmlspecialchars ($addr['last_log']['user'] . ', ' . formatAge ($addr['last_log']['time']) , ENT_QUOTES) . '"';
			$history_class = 'hover-history underline';
		}
		echo "<tr class='${addr['class']}'>";
		echo "<td class=tdleft><a class='ancor $history_class' $title name='ip-$dottedquad' href='".makeHref(array('page'=>'ipaddress', 'ip'=>$addr['ip']))."'>${addr['ip']}</a></td>";
		echo "<td class='${secondstyle} " .
			(empty ($addr['allocs']) || !empty ($addr['name']) ? 'rsv-port' : '') .
			"'><span class='rsvtext'>${addr['name']}</span></td><td class='${secondstyle}'>";
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
			echo "<a href='".makeHref(array('page'=>'object', 'object_id'=>$ref['object_id'], 'tab' => 'default', 'hl_ipv4_addr'=>$addr['ip']))."'>";
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
	if (permitted (NULL, NULL, 'set_reserve_comment'))
		addJS ('js/inplace-edit.js');

	echo "</table>";
	if (! empty ($rendered_pager))
		echo '<p>' . $rendered_pager . '</p>';

	finishPortlet();
	echo "</td></tr></table>\n";
}

// based on renderIPv4Network
function renderIPv6Network ($id)
{
	$range = spotEntity ('ipv6net', $id);
	amplifyCell ($range);
	loadIPv6AddrList ($range);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${range['ip']}/${range['mask']}</h1><h2>";
	echo htmlspecialchars ($range['name'], ENT_QUOTES, 'UTF-8') . "</h2></td></tr>\n";

	echo "<tr><td class=pcleft width='50%'>";
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	echo "<tr><th width='50%' class=tdright>%% used:</th><td class=tdleft>";
	echo "&nbsp;" . formatIPv6NetUsage (count ($range['addrlist']), $range['mask']) . "</td></tr>\n";

	if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
	{
		// Build a backtrace from all parent networks.
		$backtrace = array();
		$current = $range;
		while ($current['parent_id'])
		{
			$current = spotEntity ('ipv6net', $current['parent_id']);
			$backtrace[] = $current;
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
	}

	foreach ($range['8021q'] as $item)
	{
		$vlaninfo = getVLANInfo ($item['domain_id'] . '-' . $item['vlan_id']);
		echo '<tr><th width="50%" class=tdright>VLAN:</th><td class=tdleft><a href="';
		echo makeHref (array ('page' => 'vlan', 'vlan_ck' => $vlaninfo['vlan_ck'])) . '">';
		echo formatVLANName ($vlaninfo, 'markup long');
		echo '</a></td></tr>';
	}
	if (getConfigVar ('EXT_IPV4_VIEW') == 'yes' and count ($routers = findRouters ($range['addrlist'])))
	{
		echo "<tr><th width='50%' class=tdright>Routed by:</th>";
		printRoutersTD ($routers);
		echo "</tr>\n";
	}

	printTagTRs ($range, makeHref (array ('page' => 'ipv6space', 'tab' => 'default')) . "&");
	echo "</table><br>\n";
	finishPortlet();

	if (strlen ($range['comment']))
	{
		startPortlet ('Comment');
		echo '<div class=commentblock>' . string_insert_hrefs (htmlspecialchars ($range['comment'], ENT_QUOTES, 'UTF-8')) . '</div>';
		finishPortlet ();
	}

	renderFilesPortlet ('ipv6net', $id);
	echo "</td>\n";

	// render address list
	echo "<td class=pcright>";
	startPortlet ('details');
	renderIPv6NetworkAddresses ($range);
	finishPortlet();
	echo "</td></tr></table>\n";
}

// Used solely by renderSeparator
function renderEmptyIPv6 ($ip, $hl_ip)
{
	$class = 'tdleft';
	if (isset ($hl_ip) && $ip == $hl_ip)
		$class .= ' port_highlight';
	$fmt = $ip->format();
	echo "<tr><td class=tdleft><a class='ancor' name='ip-$fmt' href='" . makeHref (array ('page' => 'ipv6address', 'ip' => $fmt)) . "'>" . $fmt;
	echo "</a></td><td class='${class} rsv-port'><span class='rsvtext'></span></td><td class='${class}'>&nbsp;</td></tr>\n";
}

// Renders empty table line to shrink empty IPv6 address ranges.
// If the range consists of single address, renders the address instead of empty line.
// Renders address $hl_ip inside the range.
// Used solely by renderIPv6NetworkAddresses
function renderSeparator ($first, $after, $hl_ip)
{
	$self = __FUNCTION__;
	if (strcmp ($first->getBin(), $after->getBin()) >= 0)
		return;
	if ($first->next() == $after)
		renderEmptyIPv6 ($first, $hl_ip);
	elseif (isset ($hl_ip) && strcmp ($hl_ip->getBin(), $first->getBin()) >= 0 && strcmp ($hl_ip->getBin(), $after->getBin()) < 0)
	{ // $hl_ip is inside the range $first - ($after-1)
		$self ($first, $hl_ip, $hl_ip);
		renderEmptyIPv6 ($hl_ip, $hl_ip);
		$self ($hl_ip->next(), $after, $hl_ip);
	}
	else
		echo "<tr><td colspan=3 class=tdleft></td></tr>\n";
}

// calculates page number which contains given $ip (used by renderIPv6NetworkAddresses)
function getPageNumOfIPv6 ($list, $ip, $maxperpage)
{
	if (intval ($maxperpage) <= 0 || count ($list) <= $maxperpage)
		return 0;
	$bin_ip = $ip->getBin();
	$keys = array_keys ($list);
	for ($i = 1; $i <= count ($keys); $i++)
		if (strcmp ($keys[$i-1], $bin_ip) >= 0)
			return intval ($i / $maxperpage);
	return intval (count ($list) / $maxperpage);
}

function renderIPv6NetworkAddresses ($netinfo)
{
	global $pageno, $tabno, $aac2;
	echo "<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center' width='100%'>\n";
	echo "<tr><th>Address</th><th>Name</th><th>Allocation</th></tr>\n";

	$hl_ip = new IPv6Address;
	if (! isset ($_REQUEST['hl_ipv6_addr']) || ! $hl_ip->parse ($_REQUEST['hl_ipv6_addr']))
		$hl_ip = NULL;
	else
		addAutoScrollScript ('ip-' . $hl_ip->format());

	$prev_ip = $netinfo['ip_bin']; // really this is the next to previosly seen ip.
	$addresses = $netinfo['addrlist'];
	ksort ($addresses);

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
	foreach ($addresses as $bin_ip => $addr)
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

		$ipv6 = new IPv6Address ($bin_ip);
		if ($ipv6 != $prev_ip)
			renderSeparator ($prev_ip, $ipv6, $hl_ip);
		$prev_ip = $ipv6->next();
		
		$secondstyle = 'tdleft';
		if (isset ($hl_ip) && $hl_ip == $ipv6)
			$secondstyle .= ' port_highlight';
		echo "<tr class='${addr['class']}'>";
		echo "<td class=tdleft><a class='ancor' name='ip-${addr['ip']}' href='" . makeHref (array ('page' => 'ipv6address', 'ip' => $addr['ip'])) . "'>${addr['ip']}</a></td>";
		echo "<td class='${secondstyle} " .
			(empty ($addr['allocs']) || !empty ($addr['name']) ? 'rsv-port' : '') .
			"'><span class='rsvtext'>${addr['name']}</span></td><td class='${secondstyle}'>";
		$delim = '';
		$prologue = '';
		if ( $addr['reserved'] == 'yes')
		{
			echo "<strong>RESERVED</strong> ";
			$delim = '; ';
		}
		foreach ($addr['allocs'] as $ref)
		{
			echo $delim . $aac2[$ref['type']];
			echo "<a href='" . makeHref (array ('page' => 'object', 'object_id' => $ref['object_id'], 'hl_ipv6_addr' => $addr['ip'])) . "'>";
			echo $ref['name'] . (!strlen ($ref['name']) ? '' : '@');
			echo "${ref['object_name']}</a>";
			$delim = '; ';
		}
		if ($delim != '')
		{
			$delim = '';
			$prologue = '<br>';
		}
		echo "</td></tr>\n";
	}
	if (! $interruped)
		renderSeparator ($prev_ip, $netinfo['ip_bin']->get_last_subnet_address ($netinfo['mask'])->next(), $hl_ip);
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
	$netdata = spotEntity ($pageno, $id);
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

function renderIPAddress ($dottedquad)
{
	global $aat, $nextorder;
	$address = getIPAddress ($dottedquad);
	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	echo "<tr><td colspan=2 align=center><h1>${address['ip']}</h1></td></tr>\n";

	echo "<tr><td class=pcleft>";
	startPortlet ('summary');
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	if (strlen ($address['name']))
		echo "<tr><th width='50%' class=tdright>Comment:</th><td class=tdleft>${address['name']}</td></tr>";
	echo "<tr><th width='50%' class=tdright>Allocations:</th><td class=tdleft>" . count ($address['allocs']) . "</td></tr>\n";
	if ($address['version'] == 4)
	{
		echo "<tr><th width='50%' class=tdright>Originated NAT connections:</th><td class=tdleft>" . count ($address['outpf']) . "</td></tr>\n";
		echo "<tr><th width='50%' class=tdright>Arriving NAT connections:</th><td class=tdleft>" . count ($address['inpf']) . "</td></tr>\n";
		echo "<tr><th width='50%' class=tdright>SLB virtual services:</th><td class=tdleft>" . count ($address['lblist']) . "</td></tr>\n";
		echo "<tr><th width='50%' class=tdright>SLB real servers:</th><td class=tdleft>" . count ($address['rslist']) . "</td></tr>\n";
	}
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
			echo "<tr class='$class'><td class=tdleft><a href='" . makeHref (array ('page' => 'object', 'object_id' => $bond['object_id'], 'tab' => 'default', 'hl_ipv' . $address['version'] . '_addr' => $address['ip'])) . "'>${bond['object_name']}</td><td class='${secondclass}'>${bond['name']}</td><td class='${secondclass}'><strong>";
			echo $aat[$bond['type']];
			echo "</strong></td></tr>\n";
		}
		echo "</table><br><br>";
		finishPortlet();
	}

	// FIXME: The returned list is structured differently, than we expect it to be. One of the sides
	// must be fixed.
	if (! empty ($address['lblist']))
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

	if (! empty ($address['rslist']))
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

	if (! empty ($address['outpf']))
	{
		startPortlet ('departing NAT rules');
		echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>\n";
		echo "<tr><th>proto</th><th>from</th><th>to</th><th>comment</th></tr>\n";
		foreach ($address['outpf'] as $rule)
			echo "<tr><td>${rule['proto']}</td><td>${rule['localip']}:${rule['localport']}</td><td>${rule['remoteip']}:${rule['remoteport']}</td><td>${rule['description']}</td></tr>";
		echo "</table>";
		finishPortlet();
	}

	if (! empty ($address['inpf']))
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

function renderIPAddressProperties ($dottedquad)
{
	$address = getIPAddress ($dottedquad);
	echo "<center><h1>${address['ip']}</h1></center>\n";

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

function renderIPAddressAllocations ($dottedquad)
{
	function printNewItemTR ($opname)
	{
		global $aat;
		printOpFormIntro ($opname);
		echo "<tr><td>";
		printImageHREF ('add', 'allocate', TRUE);
		echo "</td><td>";
		printSelect (getNarrowObjectList ('IPV4OBJ_LISTSRC'), array ('name' => 'object_id', 'tabindex' => 100));
		echo "</td><td><input type=text tabindex=101 name=bond_name size=10></td><td>";
		printSelect ($aat, array ('name' => 'bond_type', 'tabindex' => 102, 'regular'));
		echo "</td><td>";
		printImageHREF ('add', 'allocate', TRUE, 103);
		echo "</td></form></tr>";
	}
	global $aat;

	$address = getIPAddress ($dottedquad);
	$opname = $address['version'] == 6 ? 'addIPv6Allocation' : 'addIPv4Allocation';
	echo "<center><h1>${address['ip']}</h1></center>\n";
	echo "<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>\n";
	echo "<tr><th>&nbsp;</th><th>object</th><th>OS interface</th><th>allocation type</th><th>&nbsp;</th></tr>\n";

	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR($opname);
	if (isset ($address['class']))
	{
		$class = $address['class'];
		if ($address['reserved'] == 'yes')
			echo "<tr class='${class}'><td colspan=3>&nbsp;</td><td class=tdleft><strong>RESERVED</strong></td><td>&nbsp;</td></tr>";
		foreach ($address['allocs'] as $bond)
		{
			echo "<tr class='$class'>";
			printOpFormIntro
			(
				$address['version'] == 6 ? 'updIPv6Allocation' : 'updIPv4Allocation',
				array ('object_id' => $bond['object_id'])
			);
			echo "<td><a href='"
				. makeHrefProcess
				(
					array
					(
						'op' => $address['version'] == 6 ? 'delIPv6Allocation' : 'delIPv4Allocation',
						'ip' => $address['ip'],
						'object_id' => $bond['object_id']
					)
				)
				. "'>";
			printImageHREF ('delete', 'Unallocate address');
			echo "</a></td>";
			echo "<td><a href='" . makeHref (array ('page' => 'object', 'object_id' => $bond['object_id'], 'hl_ipv' . $address['version'] . '_addr' => $address['ip'])) . "'>${bond['object_name']}</td>";
			echo "<td><input type='text' name='bond_name' value='${bond['name']}' size=10></td><td>";
			printSelect ($aat, array ('name' => 'bond_type'), $bond['type']);
			echo "</td><td>";
			printImageHREF ('save', 'Save changes', TRUE);
			echo "</td></form></tr>\n";
		}
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR($opname);
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
	global $location_obj_types, $virtual_obj_types;
	$typelist = readChapter (CHAP_OBJTYPE, 'o');
	$typelist[0] = 'select type...';
	$typelist = cookOptgroups ($typelist);
	$max = getConfigVar ('MASSCOUNT');
	$tabindex = 100;

	// create a list of object types to exclude (virtual and location-related ones)
	$exclude_typelist = array_merge($location_obj_types, $virtual_obj_types);

	$phys_typelist = $typelist;
	foreach ($phys_typelist['other'] as $key => $value)
	{
		// remove from list if type should be excluded
		if ($key > 0 && in_array($key, $exclude_typelist))
			unset($phys_typelist['other'][$key]);
	}
	startPortlet ('Physical objects');
	printOpFormIntro ('addObjects');
	echo '<table border=0 align=center>';
	echo "<tr><th>Object type</th><th>Common name</th><th>Visible label</th>";
	echo "<th>Asset tag</th><th>Tags</th></tr>\n";
	for ($i = 0; $i < $max; $i++)
	{
		echo '<tr><td>';
		// Don't employ DEFAULT_OBJECT_TYPE to avoid creating ghost records for pre-selected empty rows.
		printNiftySelect ($phys_typelist, array ('name' => "${i}_object_type_id", 'tabindex' => $tabindex), 0);
		echo '</td>';
		echo "<td><input type=text size=30 name=${i}_object_name tabindex=${tabindex}></td>";
		echo "<td><input type=text size=30 name=${i}_object_label tabindex=${tabindex}></td>";
		echo "<td><input type=text size=20 name=${i}_object_asset_no tabindex=${tabindex}></td>";
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

	// create a list containing only virtual object types
	$virt_typelist = $typelist;
	foreach ($virt_typelist['other'] as $key => $value)
	{
		if ($key > 0 && !in_array($key, $virtual_obj_types))
			unset($virt_typelist['other'][$key]);
	}
	startPortlet ('Virtual objects');
	printOpFormIntro ('addObjects');
	echo "<input type=hidden name=virtual_objects value=''>\n";
	echo '<table border=0 align=center>';
	echo "<tr><th>Object type</th><th>Common name</th><th>Tags</th></tr>\n";
	for ($i = 0; $i < $max; $i++)
	{
		echo '<tr><td>';
		// Don't employ DEFAULT_OBJECT_TYPE to avoid creating ghost records for pre-selected empty rows.
		printNiftySelect ($virt_typelist, array ('name' => "${i}_object_type_id", 'tabindex' => $tabindex), 0);
		echo '</td>';
		echo "<td><input type=text size=30 name=${i}_object_name tabindex=${tabindex}></td>";
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
		echo "<center><h2>Nothing found for '${terms}'</h2></center>";
	elseif ($nhits == 1)
	{
		foreach ($summary as $realm => $record)
		{
			if (is_array ($record))
				$record = array_shift ($record);
			break;
		}
		switch ($realm)
		{
			case 'ipv4addressbydq':
				if ($record['net_id'] !== NULL)
					echo "<script language='Javascript'>document.location='index.php?page=ipv4net&tab=default&id=${record['net_id']}&hl_ipv4_addr=${record['ip']}';//</script>";
				break;
			case 'ipv6addressbydq':
				$v6_ip_dq = $record['ip']->format();
				if ($record['net_id'] !== NULL)
					echo "<script language='Javascript'>document.location='index.php?page=ipv6net&tab=default&id=${record['net_id']}&hl_ipv6_addr=${v6_ip_dq}';//</script>";
				break;
			case 'ipv4addressbydescr':
				$parentnet = getIPv4AddressNetworkId ($record['ip']);
				if ($parentnet !== NULL)
					echo "<script language='Javascript'>document.location='index.php?page=ipv4net&tab=default&id=${parentnet}&hl_ipv4_addr=${record['ip']}';//</script>";
				break;
			case 'ipv6addressbydescr':
				$v6_ip = new IPv6Address ($record['ip']);
				$v6_ip_dq = $v6_ip->format();
				$parentnet = getIPv6AddressNetworkId ($v6_ip);
				if ($parentnet !== NULL)
					echo "<script language='Javascript'>document.location='index.php?page=ipv4net&tab=default&id=${parentnet}&hl_ipv6_addr=${v6_ip_dq}';//</script>";
				break;
			case 'ipv4network':
				echo "<script language='Javascript'>document.location='index.php?page=ipv4net";
				echo "&id=${record['id']}";
				echo "';//</script>";
				break;
			case 'ipv6network':
				echo "<script language='Javascript'>document.location='index.php?page=ipv6net";
				echo "&id=${record['id']}";
				echo "';//</script>";
				break;
			case 'object':
				if (isset ($record['by_port']) and 1 == count ($record['by_port']))
				{
					$found_ports_ids = array_keys ($record['by_port']);
					$hl = '&hl_port_id=' . $found_ports_ids[0];
				}
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
			case 'vlan':
				echo "<script language='Javascript'>document.location='index.php?page=vlan&vlan_ck=${record}';//</script>";
				break;
			default:
				startPortlet($realm);
				echo $record;
				finishPortlet();
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
				case 'ipv4network':
					startPortlet ("<a href='index.php?page=ipv4space'>IPv4 networks</a>");
				case 'ipv6network':
					if ($where == 'ipv6network')
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
				case 'ipv6addressbydescr':
					startPortlet ('IPv6 addresses');
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					// FIXME: address, parent network, routers (if extended view is enabled)
					echo '<tr><th>Address</th><th>Description</th></tr>';
					foreach ($what as $addr)
					{
						echo "<tr class=row_${order}><td class=tdleft>";
						$v6_ip = new IPv6Address ($addr['ip']);
						$v6_ip_dq = $v6_ip->format();
						$parentnet = getIPv6AddressNetworkId ($v6_ip);
						if ($parentnet !== NULL)
							echo "<a href='index.php?page=ipv6net&tab=default&id=${parentnet}&hl_ipv6_addr=${v6_ip_dq}'>${v6_ip_dq}</a></td>";
						else
							echo "<a href='index.php?page=ipaddress&ip=${v6_ip_dq}'>${v6_ip_dq}</a></td>";
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
				case 'vlan':
					startPortlet ("<a href='index.php?page=8021q'>VLANs</a>");
					echo '<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>';
					foreach ($what as $vlan_ck)
					{
						echo "<tr class=row_${order}><td class=tdleft>";
						echo formatVLANName (getVLANInfo ($vlan_ck), 'hyperlink') . "</td></tr>";
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
	addAtomCSS();
	addJS ('js/racktables.js');
	for ($unit_no = $data['height']; $unit_no > 0; $unit_no--)
	{
		echo "<tr><th><a href='javascript:;' oncontextmenu=\"blockToggleRowOfAtoms('${rack_id}','${unit_no}'); return false;\" onclick=\"toggleRowOfAtoms('${rack_id}','${unit_no}')\">${unit_no}</a></th>";
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			$name = "atom_${rack_id}_${unit_no}_${locidx}";
			$state = $data[$unit_no][$locidx]['state'];
			echo "<td class='atom state_${state}";
			if (isset ($data[$unit_no][$locidx]['hl']))
				echo $data[$unit_no][$locidx]['hl'];
			echo "'>";
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

function renderOIFCompatViewer()
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

function renderOIFCompatEditor()
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

	global $nextorder, $wdm_packs;

	startPortlet ('WDM wideband receivers');
	echo '<table border=0 align=center cellspacing=0 cellpadding=5>';
	echo '<tr><th>&nbsp;</th><th>enable</th><th>disable</th></tr>';
	$order = 'odd';
	foreach ($wdm_packs as $codename => $packinfo)
	{
		echo "<tr class=row_${order}><td class=tdleft>" . $packinfo['title'] . '</td><td><a href="';
		echo makeHrefProcess (array ('op' => 'addPack', 'standard' => $codename));
		echo '">' . getImageHREF ('add') . '</a></td><td><a href="';
		echo makeHrefProcess (array ('op' => 'delPack', 'standard' => $codename));
		echo '">' . getImageHREF ('delete') . '</a></td></tr>';
		$order = $nextorder[$order];
	}
	echo '</table>';
	finishPortlet();

	startPortlet ('interface by interface');
	$last_left_oif_id = NULL;
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
		printImageHREF ('delete', 'remove pair');
		echo "</a></td><td class=tdleft>${pair['type1name']}</td><td class=tdleft>${pair['type2name']}</td></tr>";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewitemTR();
	echo '</table>';
	finishPortlet();
}

function renderObjectParentCompatViewer()
{
	global $nextorder;
	$order = 'odd';
	$last_left_parent_id = NULL;
	echo '<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>';
	echo '<tr><th>Parent</th><th>Child</th></tr>';
	foreach (getObjectParentCompat() as $pair)
	{
		if ($last_left_parent_id != $pair['parent_objtype_id'])
		{
			$order = $nextorder[$order];
			$last_left_parent_id = $pair['parent_objtype_id'];
		}
		echo "<tr class=row_${order}><td>${pair['parent_name']}</td><td>${pair['child_name']}</td></tr>\n";
	}
	echo '</table>';
}

function renderObjectParentCompatEditor()
{
	function printNewitemTR()
	{
		printOpFormIntro ('add');
		echo '<tr><th class=tdleft>';
		printImageHREF ('add', 'add pair', TRUE);
		echo '</th><th class=tdleft>';
		printSelect (readChapter (CHAP_OBJTYPE), array ('name' => 'parent_objtype_id'));
		echo '</th><th class=tdleft>';
		printSelect (readChapter (CHAP_OBJTYPE), array ('name' => 'child_objtype_id'));
		echo "</th></tr></form>\n";
	}

	global $nextorder;
	$last_left_parent_id = NULL;
	$order = 'odd';
	echo '<br><table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>';
	echo '<tr><th>&nbsp;</th><th>Parent</th><th>Child</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewitemTR();
	foreach (getObjectParentCompat() as $pair)
	{
		if ($last_left_parent_id != $pair['parent_objtype_id'])
		{
			$order = $nextorder[$order];
			$last_left_parent_id = $pair['parent_objtype_id'];
		}
		echo "<tr class=row_${order}><td>";
		echo '<a href="' . makeHrefProcess (array ('op' => 'del', 'parent_objtype_id' => $pair['parent_objtype_id'], 'child_objtype_id' => $pair['child_objtype_id'])) . '">';
		printImageHREF ('delete', 'remove pair');
		echo "</a></td><td class=tdleft>${pair['parent_name']}</td><td class=tdleft>${pair['child_name']}</td></tr>\n";
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
	echo '<td class=pcright>';
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
	$words = readChapter ($tgt_chapter_no, 'a');
	$wc = count ($words);
	if (!$wc)
	{
		echo "<center><h2>(no records)</h2></center>";
		return;
	}
	$refcnt = getChapterRefc ($tgt_chapter_no, array_keys ($words));
	$attrs = getChapterAttributes($tgt_chapter_no);
	echo "<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	echo "<tr><th colspan=3>${wc} record(s)</th></tr>\n";
	echo "<tr><th>Origin</th><th>Refcnt</th><th>Word</th></tr>\n";
	$order = 'odd';
	foreach ($words as $key => $value)
	{
		echo "<tr class=row_${order}><td>";
		printImageHREF ($key < 50000 ? 'computer' : 'favorite');
		echo '</td><td>';
		if ($refcnt[$key])
		{
			$cfe = '';
			foreach ($attrs as $attr_id)
			{
				if (! empty($cfe))
					$cfe .= ' or ';
				$cfe .= '{$attr_' . $attr_id . '_' . $key . '}';
			}
			
			if (! empty($cfe))
			{
				$href = makeHref
				(
					array
					(
						'page'=>'depot',
						'tab'=>'default',
						'andor' => 'and',
						'cfe' => $cfe
					)
				);
				echo '<a href="' . $href . '">' . $refcnt[$key] . '</a>';
			}
			else
				echo $refcnt[$key];
		}
		echo "</td><td><div title='key=${key}'>${value}</div></td></tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table>\n<br>";
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
		echo "<td class=tdleft><input type=text name=dict_value size=64 tabindex=100></td><td>";
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
		if ($key < 50000)
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
		echo "<td class=tdleft><input type=text name=dict_value size=64 value='${value}'></td><td>";
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
		$objtypes = readChapter (CHAP_OBJTYPE, 'o');
		unset ($objtypes[1561], $objtypes[1562]); // attributes may not be assigned to rows or locations yet
		printNiftySelect (cookOptgroups ($objtypes), array ('name' => 'objtype_id', 'tabindex' => 101));
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
		foreach (getDomainVLANs ($domain_id) as $vlan_id => $vlan_info)
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
	echo '<table border=1 cellspacing=0 cellpadding=5 align=center>';
	foreach ($output as $vlan_id => $tbc)
	{
		if (--$header_delay <= 0)
		{
			echo $header;
			$header_delay = 25;
		}
		echo '<tr><th class=tdright>' . $vlan_id . '</th>';
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
			echo '<tr><th>...</th><td colspan=' . count ($domains) . '>&nbsp;</td></tr>';
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
		'ipv4vs' => 'ipv4vslist&tab=default',
		'ipv4rspool' => 'ipv4rsplist&tab=default',
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
	echo '<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center width="70%">';
	echo '<tr><th class=tdleft>Option</th><th class=tdleft>Value</th></tr>';
	$order = 'odd';
	foreach ($configCache as $v)
	{
		if ($v['is_hidden'] != 'no')
			continue;
		echo "<tr class=row_${order}>";
		echo "<td nowrap valign=top class=tdright>${v['description']}</td>";
		echo "<td valign=top class=tdleft>${v['varvalue']}</td></tr>";
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
	try
	{
		$data = getSwitchVLANs ($object_id);
	}
	catch (RTGatewayError $re)
	{
		showWarning ('Device configuration unavailable:<br>' . $re->getMessage());
		return;
	}
	list ($vlanlist, $portlist, $maclist) = $data;
	$vlanpermissions = array();
	foreach ($portlist as $port)
	{
		if (array_key_exists ($port['vlanid'], $vlanpermissions))
			continue;
		$vlanpermissions[$port['vlanid']] = array();
		foreach (array_keys ($vlanlist) as $to)
			if
			(
				permitted (NULL, NULL, 'setPortVLAN', array (array ('tag' => '$fromvlan_' . $port['vlanid']), array ('tag' => '$vlan_' . $port['vlanid']))) and
				permitted (NULL, NULL, 'setPortVLAN', array (array ('tag' => '$tovlan_' . $to), array ('tag' => '$vlan_' . $to)))
			)
				$vlanpermissions[$port['vlanid']][] = $to;
	}

	if (isset ($_REQUEST['hl_port_id']))
	{
		assertUIntArg ('hl_port_id');
		$hl_port_id = intval ($_REQUEST['hl_port_id']);
		$object = spotEntity ('object', $object_id);
		amplifyCell ($object);
		foreach ($object['ports'] as $port)
			if (mb_strlen ($port['name']) && $port['id'] == $hl_port_id)
			{
				$hl_port_name = $port['name'];
				break;
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
		$td_class = 'port_';
		if ($port['status'] == 'notconnect')
			$td_class .= 'notconnect';
		elseif ($port['status'] == 'disabled')
			$td_class .= 'disabled';
		elseif ($port['status'] != 'connected')
			$td_class .= 'unknown';
		elseif (!isset ($maclist[$port['portname']]))
			$td_class .= 'connected_none';
		else
		{
			$maccount = 0;
			foreach ($maclist[$port['portname']] as $vlanid => $addrs)
				$maccount += count ($addrs);
			if ($maccount == 1)
				$td_class .= 'connected_single';
			else
				$td_class .= 'connected_multi';
		}
		if (isset ($hl_port_name) and strcasecmp ($hl_port_name, $port['portname']) == 0)
			$td_class .= (strlen($td_class) ? ' ' : '') . 'border_highlight';
		echo "<td class='$td_class'>" . $port['portname'] . '<br>';
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
This object has no ports listed, that's why you see this form. I can try to automatically harvest the data.
As soon as at least one port is added, this tab will not be seen any more. Good luck.
<br />
You may enter just the snmp community and use SNMPv3 (leave community empty and fill the other fields)
<br />\n";

		echo "<input type=text name=community value='" . $snmpcomm . "'><br /><br />\n";

		echo '
		<label for="sec_name">Security User</label>
		<input type="text" id="sec_name" name="sec_name"><br />

		
		<label for="sec_level">Security Level</label>
		<select id="sec_level" name="sec_level"> 
			<option value="noAuthNoPriv" selected="selected">noAuth and no Priv</option>
			<option value="authNoPriv" >auth without Priv</option>
			<option value="authPriv" >auth with Priv</option>
		</select>
		<br />

		<label for="auth_protocol_1">Auth Type</label>
		<input id="auth_protocol_1" name="auth_protocol" type="radio" value="md5" />
		<label for="auth_protocol_1">MD5</label>
		<input id="auth_protocol_2" name="auth_protocol" type="radio" value="sha" />
		<label for="auth_protocol_2">SHA</label>
		<br />

		<label for="auth_passphrase">Auth Key</label>
		<input type="text" id="auth_passphrase" name="auth_passphrase">
		<br />

		<label for="priv_protocol_1">Priv Type</label>
		<input id="priv_protocol_1" name="priv_protocol" type="radio" value="DES" />
		<label for="priv_protocol_1">DES</label>
		<input id="priv_protocol_2" name="priv_protocol" type="radio" value="AES" />
		<label for="priv_protocol_2">AES</label>
		<br />

		<label for="priv_passphrase">Priv Key</label>
		<input type="text" id="priv_passphrase" name="priv_passphrase">
		<br />';
		

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
	try
	{
		$config = buildLVSConfig ($object_id);

		printOpFormIntro ('submitSLBConfig');
		echo "<center><input type=submit value='Submit for activation'></center>";
		echo "</form>";
	}
	catch(RTBuildLVSConfigError $e)
	{
		$config = $e->config_to_display;
		foreach ($e->message_list as $msg)
			echo '<div class="msg_error">' . $msg . '</div>';
	}
	echo "<pre>$config</pre>";
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
				if (strlen ($lbInfo['prio']))
					echo "<tr><th>Prio</th><td class='dashed slbconf'>${lbInfo['prio']}</td></tr>";

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
	echo getProgressBar ($percentage, $theme);
}

function getProgressBar ($percentage = 0, $theme = '')
{
	$done = ((int) ($percentage * 100));
	$ret = "<img width=100 height=10 border=0 title='${done}%' src='?module=progressbar&done=${done}";
	if ($theme != '')
		$ret .= "&theme=${theme}";
	$ret .= "'>";
	return $ret;
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
	$triplets = array();
	$display_count = 0;
	foreach ($poolInfo['lblist'] as $object_id => $vslist)
	{
		++$display_count;
		foreach ($vslist as $vs_id => $configs)
			$triplets[] = array(
				'ids' => array(
					'object_id' => $object_id,
					'vs_id' => $vs_id,
					'pool_id' => $pool_id
				),
				'rsconfig' => $configs['rsconfig'],
				'vsconfig' => $configs['vsconfig'],
				'prio' => $configs['prio']
			);
	}
	renderSLBTriplets('object', 'ipv4vs', $triplets, $display_count);
}

function renderVServiceLBForm ($vs_id)
{
	$vsinfo = spotEntity ('ipv4vs', $vs_id);
	amplifyCell ($vsinfo);
	$triplets = array();
	$display_count = 0;
	foreach ($vsinfo['rspool'] as $pool_id => $rspinfo)
	{
		++$display_count;
		foreach ($rspinfo['lblist'] as $object_id => $configs)
			$triplets[] = array(
				'ids' => array(
					'object_id' => $object_id,
					'vs_id' => $vs_id,
					'pool_id' => $pool_id
				),
				'rsconfig' => $configs['rsconfig'],
				'vsconfig' => $configs['vsconfig'],
				'prio' => $configs['prio']
			);
	}
	renderSLBTriplets('object', 'ipv4rspool', $triplets, $display_count);
}

function renderObjectSLB ($object_id)
{
	$focus = spotEntity ('object', $object_id);
	amplifyCell ($focus);
	$triplets = array();
	foreach ($focus['ipv4rspools'] as $vs_id => $vsinfo)
		$triplets[] = array(
			'ids' => array(
				'object_id' => $object_id,
				'vs_id' => $vs_id,
				'pool_id' => $vsinfo['pool_id']
			),
			'rsconfig' => $vsinfo['rsconfig'],
			'vsconfig' => $vsinfo['vsconfig'],
			'prio' => $vsinfo['prio']
		);
	$display_count = count($triplets);
	renderSLBTriplets('ipv4vs', 'ipv4rspool', $triplets, $display_count);
}

// called exclusively by renderSLBTriplets. Renders form to add new SLB link.
// realms 1 and 2 are realms to draw inputs for
function renderNewSLBItemForm ($realm1, $realm2)
{
	function print_realm_select_input($realm)
	{
		switch ($realm)
		{
			case 'object':
				echo "<tr valign=top><th class=tdright>Load balancer</th><td class=tdleft>";
				printSelect (getNarrowObjectList ('IPV4LB_LISTSRC'), array ('name' => 'object_id', 'tabindex' => 100));
				break;
			case 'ipv4vs':
				echo '</td></tr><tr><th class=tdright>Virtual service</th><td class=tdleft>';
				printSelect (getIPv4VSOptions(), array ('name' => 'vs_id', 'tabindex' => 101));
				break;
			case 'ipv4rspool':
				echo '</td></tr><tr><th class=tdright>RS pool</th><td class=tdleft>';
				printSelect (getIPv4RSPoolOptions(), array ('name' => 'pool_id', 'tabindex' => 102));
				break;
			default:
				throw new InvalidArgException('realm', $realm);
		}
	}

	startPortlet ('Add new');
	echo "<table cellspacing=0 cellpadding=5 align=center>";
	printOpFormIntro ('addLB');
	print_realm_select_input($realm1);
	echo '</td><td class=tdcenter valign=middle rowspan=2>';
	printImageHREF ('ADD', 'Configure LB', TRUE, 120);
	print_realm_select_input($realm2);
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>VS config</th><td colspan=2><textarea tabindex=110 name=vsconfig rows=10 cols=80></textarea></td></tr>";
	echo "<tr><th class=tdright>RS config</th><td colspan=2><textarea tabindex=111 name=rsconfig rows=10 cols=80></textarea></td></tr>";
	echo "<tr><th class=tdright>Priority</th><td class=tdleft colspan=2><input tabindex=112 name=prio size=10></td></tr>";
	echo "</form></table>\n";
	finishPortlet();
}

// renders a list of slb links. it is called from 3 different pages, wich compute their links lists differently.
// each triplet in $triplets array contains balancer id, pool id, VS id and config values for triplet: RS, VS configs and pair.
// realms 1 and 2 are needed to indicate the order of displaying cells in left column.
// e.g. if we draw a RS pool page, realm1 should be set to 'object'(balancer), realm2 - to 'ipv4vs'
function renderSLBTriplets ($realm1, $realm2, $triplets, $display_count) {
	function get_object_id_by_realm($realm, $triplet)
	{
		switch ($realm) {
			case 'object':
				$key ='object_id';
				break;
			case 'ipv4vs':
				$key = 'vs_id';
				break;
			case 'ipv4rspool':
				$key = 'pool_id';
				break;
			default:
				throw new InvalidArgException('realm', $realm);
		}
		return $triplet['ids'][$key];
	}

	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		renderNewSLBItemForm($realm1, $realm2);

	if (count($triplets)) {
		startPortlet ('Manage existing (' . $display_count . ')');
		echo "<table cellspacing=0 cellpadding=5 align=center class=cooltable>\n";

		echo "<table cellspacing=0 cellpadding=5 align=center class=cooltable>\n";
		global $nextorder;
		$order = 'odd';
		$i = 0;
		foreach ($triplets as $slb)
		{
			++$i;
			$del_params = $slb['ids'];
			$del_params['op'] = 'delLB';
			printOpFormIntro ('updLB', $slb['ids']);
			echo "<tr valign=top class=row_${order}><td rowspan=2 class=tdright valign=middle><a href='".makeHrefProcess($del_params)."'>";
			printImageHREF ('DELETE', 'Unconfigure');
			echo "</a></td>";
			echo "<td class=tdleft valign=bottom>";
			renderSLBEntityCell ($realm1, get_object_id_by_realm($realm1, $slb));
			echo "</td><td>VS config &darr;<br><textarea name=vsconfig rows=5 cols=70>${slb['vsconfig']}</textarea></td>";
			echo '<td class=tdleft rowspan=2 valign=middle>';
			printImageHREF ('SAVE', 'Save changes', TRUE);
			echo "</td></tr><tr class=row_${order}><td class=tdleft valign=top>";
			renderSLBEntityCell ($realm2, get_object_id_by_realm($realm2, $slb));
			echo '</td><td>';
			echo "<textarea name=rsconfig rows=5 cols=70>${slb['rsconfig']}</textarea><br>RS config &uarr;";
			$prio_id = "prio-$i";
			$prio_value = htmlspecialchars($slb['prio']);
			echo "<div style='float:left; margin-top:10px'><input name=prio type=text size=10 id=\"$prio_id\" value=\"$prio_value\"><label for=\"$prio_id\"> &larr; Priority</label></div>";
			echo '</td></tr></form>';
			$order = $nextorder[$order];
		}
		echo "</table>\n";
		finishPortlet();
	}

	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		renderNewSLBItemForm($realm1, $realm2);
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
	echo "<tr><th>VS</th><th>LB</th><th>VS config</th><th>RS config</th><th>Prio</th></tr>";
	$order = 'odd';
	foreach ($poolInfo['lblist'] as $object_id => $vslist)
		foreach ($vslist as $vs_id => $configs)
	{
		echo "<tr valign=top class=row_${order}><td class=tdleft><a href='".makeHref(array('page'=>'ipv4vs', 'vs_id'=>$vs_id))."'>";
		renderCell (spotEntity ('ipv4vs', $vs_id));
		echo "</td><td>";
		renderLBCell ($object_id);
		echo "</td><td class=slbconf>${configs['vsconfig']}</td>";
		echo "<td class=slbconf>${configs['rsconfig']}</td>\n";
		echo "<td class=slbconf>${configs['prio']}</td></tr>\n";
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
	addJS ('js/racktables.js');
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
	$statsdecoder = array
	(
		'total' => ' total records linked',
		'object' => ' object(s)',
		'rack' => ' rack(s)',
		'file' => ' file(s)',
		'user' => ' user account(s)',
		'ipv6net' => ' IPv6 network(s)',
		'ipv4net' => ' IPv4 network(s)',
		'ipv4vs' => ' IPv4 virtual service(s)',
		'ipv4rspool' => ' IPv4 real server pool(s)',
		'vst' => ' VLAN switch template(s)',
	);
	if (!count ($taginfo['kids']))
		$level++; // Shift instead of placing a spacer. This won't impact any nested nodes.
	$refc = $taginfo['refcnt']['total'];
	echo "<tr><td align=left style='padding-left: " . ($level * 16) . "px;'>";
	if (count ($taginfo['kids']))
		printImageHREF ('node-expanded-static');
	$stats = array ("tag ID = ${taginfo['id']}");
	if ($taginfo['refcnt']['total'])
		foreach ($taginfo['refcnt'] as $article => $count)
			if (array_key_exists ($article, $statsdecoder))
				$stats[] = $count . $statsdecoder[$article];
	echo '<span title="' . implode (', ', $stats) . '">' . $taginfo['tag'];
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
	if ($taginfo['kidc'])
		printImageHREF ('node-expanded-static');
	if ($taginfo['refcnt']['total'] > 0 or $taginfo['kidc'])
		printImageHREF ('nodestroy', $taginfo['refcnt']['total'] . ' references, ' . $taginfo['kidc'] . ' sub-tags');
	else
		echo '<a href="' . makeHrefProcess (array ('op' => 'destroyTag', 'tag_id' => $taginfo['id']))
			. '">' . getImageHREF ('destroy', 'Delete tag') . '</a>';
	echo '</td><td>';
	printOpFormIntro ('updateTag', array ('tag_id' => $taginfo['id']));
	echo "<input type=text size=48 name=tag_name ";
	echo "value='${taginfo['tag']}'></td><td class=tdleft>";
	$options = array (0 => '-- NONE --');
	# The call below works, because taginfo is actually a tree node, not a
	# pure "taginfo" structure.
	$hidden = getTagIDListForNode ($taginfo);
	# Exclude the current tag itself and all of its sub-tags from the "new parent
	# tag" list of options, because setting any of these as the new parent will
	# introduce a dependency loop into the tree. This hint does not prevent loops
	# as such, but lowers the chances they are created unintentionally.
	foreach ($taglist as $nominee)
		if (! in_array ($nominee['id'], $hidden))
			$options[$nominee['id']] = $nominee['tag'];
	printSelect ($options, array ('name' => 'parent_id'), $taginfo['parent_id']);
	echo '</td><td>' . getImageHREF ('save', 'Save changes', TRUE) . '</form></td></tr>';
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
		echo '</td><td><input type=text size=48 name=tag_name tabindex=100></td><td><select name=parent_id tabindex=101>';
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
		echo '<tr><th>tag name</th><th>parent tag</th><th>&nbsp;</th></tr>';
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
	echo '<tr><th>&nbsp;</th><th>tag name</th><th>parent tag</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach ($tagtree as $taginfo)
		renderTagRowForEditor ($taginfo);
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo '</table>';
	finishPortlet();
}

function renderTagCheckbox ($inputname, $preselect, $taginfo, $refcnt_realm = '', $level = 0, $inverted_preselect = array())
{
	$self = __FUNCTION__;
	$td_class = 'tagbox';
	$inverted = tagOnChain ($taginfo, $inverted_preselect);
	$selected = tagOnChain ($taginfo, $preselect);
	$prepared_inputname = $inputname;
	if ($inverted)
	{
		$td_class .= ' inverted';
		$prepared_inputname = preg_replace ('/^cf/', 'nf', $prepared_inputname);
	}
	if ($selected)
		$td_class .= ' ' . ($inverted ? 'selected-inverted' : 'selected');
	// calculate html classnames for separators feature 
	static $is_first_time = TRUE;
	$input_class = 'tag-cb' . ($level == 0 ? ' root' : '');
	$tr_class = ($level == 0 && $taginfo['id'] > 0 && !$is_first_time ? 'separator' : '');
	$is_first_time = FALSE;
	
	echo "<tr class='$tr_class'><td colspan=2 class='$td_class' style='padding-left: " . ($level * 16) . "px;'>";
	echo "<label><input type=checkbox class='$input_class' name='${prepared_inputname}[]' value='${taginfo['id']}'" . ($selected ? ' checked' : '') . "> ";
	echo $taginfo['tag'];
	if (strlen ($refcnt_realm) and isset ($taginfo['refcnt'][$refcnt_realm]))
		echo ' <i>(' . $taginfo['refcnt'][$refcnt_realm] . ')</i>';
	echo "</label></td></tr>";
	if (isset ($taginfo['kids']))
		foreach ($taginfo['kids'] as $kid)
			$self ($inputname, $preselect, $kid, $refcnt_realm, $level + 1, $inverted_preselect);
}

function renderEntityTagsPortlet ($title, $tags, $preselect, $realm)
{
	startPortlet ($title);
	echo  '<a class="toggleTreeMode" style="display:none" href="#"></a>';
	echo '<table border=0 cellspacing=0 cellpadding=3 align=center class="tagtree">';
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
			$js_code = "tag_cb.setTagShortList ({";
			$is_first = TRUE;
			foreach($minilist as $tag) {
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
	renderEntityTagsPortlet ('Tag tree', $tagtree, $target_given_tags, $etype_by_pageno[$pageno]);
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
function renderCellFilterPortlet ($preselect, $realm, $cell_list = array(), $bypass_name = '', $bypass_value = '')
{
	addJS ('js/tag-cb.js');
	addJS ('tag_cb.enableNegation()', TRUE);

	global $pageno, $tabno, $taglist, $tagtree;
	$filterc =
	(
		count ($preselect['tagidlist']) +
		count ($preselect['pnamelist']) +
		(mb_strlen ($preselect['extratext']) ? 1 : 0)
	);
	$title = $filterc ? "filters (${filterc})" : 'filters';
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
			foreach ($objectivetags as $taginfo)
				renderTagCheckbox ('cft', buildTagChainFromIds ($preselect['tagidlist']), $taginfo, $realm, 0, $negated_chain);
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
				renderTagCheckbox ('cfp', $myPreselect, $pinfo, '', 0, $negated_chain);
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
			echo "<input type=hidden name='cft[]' value=''>\n";
			echo "<input type=hidden name='cfp[]' value=''>\n";
			echo "<input type=hidden name='nft[]' value=''>\n";
			echo "<input type=hidden name='nfp[]' value=''>\n";
			echo "<input type=hidden name='cfe' value=''>\n";
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
	echo '<div class=tagselector><table border=0 align=center cellspacing=0 class="tagtree">';
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
	addJS ('js/codepress/codepress.js');
	addJS (<<<ENDJAVASCRIPT
function verify()
{
	$.ajax({
		type: "POST",
		url: "index.php",
		data: {'module': 'ajax', 'ac': 'verifyCode', 'code': RCTA.getCode()},
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
		}
	});
}

$(document).ready(function() {
	$("#SaveChanges")[0].disabled = "disabled";
	$("#ShowMessage")[0].innerHTML = "";
	$("#ShowMessage")[0].className = "";
});
ENDJAVASCRIPT
	, TRUE);

	$text = loadScript ('RackCode');
	printOpFormIntro ('saveRackCode');
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

function renderMyAccount ()
{
	global $remote_username, $remote_displayname, $user_given_tags;
	startPortlet ('Current user info');
	echo '<div style="text-align: left; display: inline-block;">';
	echo "<table>";
	echo "<tr><th>Login:</th><td>${remote_username}</td></tr>\n";
	echo "<tr><th>Name:</th><td>${remote_displayname}</td></tr>\n";
	echo "<tr><th>Tags:</th><td>" . serializeTags ($user_given_tags) . "</td></tr>\n";
	echo '</table></div>';
}

function renderMyQuickLinks ()
{
	global $indexlayout, $page;
	startPortlet ('Items to display in page header');
	echo '<div style="text-align: left; display: inline-block;">';
	printOpFormIntro ('save');
	echo '<ul class="qlinks-form">';
	$active_items = explode (',', getConfigVar ('QUICK_LINK_PAGES'));
	foreach ($indexlayout as $row)
		foreach ($row as $ypageno)
		{
			$checked_state = in_array ($ypageno, $active_items) ? 'checked' : '';
			echo "<li><label><input type='checkbox' name='page_list[]' value='$ypageno' $checked_state>" . getPageName ($ypageno) . "</label></li>\n";
		}
	echo '</ul>';
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo '</form></div>';
	finishPortlet();
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
		echo "<a href='?module=download&file_id=${file_id}'>";
		printImageHREF ('download', 'Download file');
		echo '</a>&nbsp;';
	}
	printf("%s</td></tr>", formatFileSize($file['size']));
	echo "<tr><th width='50%' class=tdright>Created:</th>";
	printf("<td class=tdleft>%s</td></tr>", $file['ctime']);
	echo "<tr><th width='50%' class=tdright>Modified:</th>";
	printf("<td class=tdleft>%s</td></tr>", $file['mtime']);
	echo "<tr><th width='50%' class=tdright>Accessed:</th>";
	printf("<td class=tdleft>%s</td></tr>", $file['atime']);

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
	echo "<br><center><a target='_blank' href='?module=download&file_id=${file_id}&asattach=1'>";
	printImageHREF ('DOWNLOAD');
	echo '</a></center>';
}

function renderFileProperties ($file_id)
{
	$file = spotEntity ('file', $file_id);
	echo '<table border=0 align=center>';
	printOpFormIntro ('updateFile');
	echo "<tr><th class=tdright>MIME-type:</th><td class=tdleft><input tabindex=101 type=text name=file_type value='";
	echo htmlspecialchars ($file['type']) . "'></td></tr>";
	echo "<tr><th class=tdright>Filename:</th><td class=tdleft><input tabindex=102 type=text name=file_name value='";
	echo htmlspecialchars ($file['name']) . "'></td></tr>\n";
	echo "<tr><th class=tdright>Comment:</th><td class=tdleft><textarea tabindex=103 name=file_comment rows=10 cols=80>\n";
	echo htmlspecialchars ($file['comment']) . "</textarea></td></tr>\n";
	echo "<tr><th class=tdright>Actions:</th><td class=tdleft>";
	echo "<a href='".
		makeHrefProcess (array ('op'=>'deleteFile', 'page'=>'files', 'tab'=>'manage', 'file_id'=>$file_id)).
		"' onclick=\"javascript:return confirm('Are you sure you want to delete the file?')\">" .
		getImageHREF ('destroy', 'Delete file') . "</a>";
	echo '</td></tr>';
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

	echo "<form method=post id=${opname} name=${opname} action='?module=redirect&page=${pageno}&tab=${tabno}&op=${opname}'";
	if ($upload)
		echo " enctype='multipart/form-data'";
	echo ">";
	if (isset ($page[$pageno]['bypass']) and isset ($_REQUEST[$page[$pageno]['bypass']]))
		$extra[$page[$pageno]['bypass']] = $_REQUEST[$page[$pageno]['bypass']];
	foreach ($extra as $inputname => $inputvalue)
		echo "<input type=hidden name=${inputname} value='${inputvalue}'>";
}

// Iterate over what findRouters() returned and output some text suitable for a TD element.
function printRoutersTD ($rlist, $as_cell = 'yes')
{
	$rtrclass = 'tdleft';
	foreach ($rlist as $rtr)
	{
		$tmp = getIPAddress ($rtr['addr']);
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
function printIPNetInfoTDs ($netinfo, $decor = array())
{
	$ip_ver = is_a ($netinfo['ip_bin'], 'IPv6Address') ? 6 : 4;
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
		echo niftyString ($netinfo['name']);
		if (count ($netinfo['etags']))
			echo '<br><small>' . serializeTags ($netinfo['etags'], "index.php?page=ipv${ip_ver}space&tab=default&") . '</small>';
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
		printf ("<a href='index.php?page=file&file_id=%s'><strong>%s</strong></a>", $cell['id'], niftyString ($cell['name']));
		echo "</td><td rowspan=3 valign=top>";
		if (isset ($cell['links']) and count ($cell['links']))
			printf ("<small>%s</small>", serializeFileLinks ($cell['links']));
		echo "</td></tr><tr><td>";
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo '</td></tr><tr><td>';
		if (isolatedPermission ('file', 'download', $cell))
		{
			// FIXME: reuse renderFileDownloader()
			echo "<a href='?module=download&file_id=${cell['id']}'>";
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
	case 'ipv6net':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		printImageHREF ('NET');
		echo '</td>';
		echo "<td><a href='index.php?page={$cell['realm']}&id=${cell['id']}'>${cell['ip']}/${cell['mask']}</a>";
		if (getConfigVar ('IPV4_TREE_SHOW_USAGE') == 'yes')
		{
			echo '<div class="net-usage">';
			if ($cell['realm'] == 'ipv4net')
			{
				loadOwnIPv4Addresses ($cell);
				$used = $cell['addrc'];
				$maxdirect = $cell['addrt'];
				
				echo "<small>$used/$maxdirect</small> ";
				renderProgressBar ($maxdirect ? $used/$maxdirect : 0);
				
			}
			elseif ($cell['realm'] == 'ipv6net')
			{
				loadOwnIPv6Addresses ($cell);
				$used = $cell['addrc'];
				echo formatIPv6NetUsage ($used, $cell['mask']);
			}
			echo '</div>';
		}
		echo '</td></tr>';

		if (strlen ($cell['name']))
			echo "<tr><td><strong>" . niftyString ($cell['name']) . "</strong></td></tr>";
		else
			echo "<tr><td class=sparenetwork>no name</td></tr>";
		echo '<tr><td>';
		echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
		echo "</td></tr></table>";
		break;
	case 'rack':
		echo "<table class='slbcell vscell'><tr><td rowspan=3 width='5%'>";
		$thumbwidth = getRackImageWidth();
		$thumbheight = getRackImageHeight ($cell['height']);
		echo "<img border=0 width=${thumbwidth} height=${thumbheight} title='${cell['height']} units' ";
		echo "src='?module=image&img=minirack&rack_id=${cell['id']}'>";
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
		throw new InvalidArgException ('realm', $cell['realm']);
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

function renderSLBEntityCell ($realm, $object_id)
{
	switch($realm)
	{
		case 'object':
			renderLBCell($object_id);
			break;
		case 'ipv4vs':
		case 'ipv4rspool':
			$cell = spotEntity ($realm, $object_id);
			renderCell($cell);
			break;
		default:
			throw new InvalidArgException('realm', $realm);
	}
}

function renderRouterCell ($dottedquad, $ifname, $cell)
{
	echo "<table class=slbcell><tr><td rowspan=3>${dottedquad}";
	if (strlen ($ifname))
		echo '@' . $ifname;
	echo "</td>";
	$ipv6 = new IPv6Address;
	$ip_type = $ipv6->parse ($dottedquad) ? 'ipv6' : 'ipv4';
	echo "<td><a href='index.php?page=object&object_id=${cell['id']}&hl_${ip_type}_addr=${dottedquad}'><strong>${cell['dname']}</strong></a></td>";
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
	addJS ('js/codepress/codepress.js');
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
	echo "<td class=activemenuitem width='99%'>";
	$path = getPath ($pageno);
	$items = array();
	foreach (array_reverse ($path) as $no)
	{
		if (isset ($page[$no]['title']))
			$title = array
			(
				'name' => $page[$no]['title'],
				'params' => array()
			);
		else
			$title = dynamic_title_decoder ($no);
		$item = "<a href='index.php?";
		if (! isset ($title['params']['page']))
			$title['params']['page'] = $no;
		if (! isset ($title['params']['tab']))
			$title['params']['tab'] = 'default';
		$is_first = TRUE;
		$ancor_tail = '';
		foreach ($title['params'] as $param_name => $param_value)
		{
			if ($param_name == '#')
			{
				$ancor_tail = '#' . $param_value;
				continue;
			}
			$item .= ($is_first ? '' : '&') . "${param_name}=${param_value}";
			$is_first = FALSE;
		}
		$item .= $ancor_tail;
		$item .= "'>" . $title['name'] . "</a>";
		$items[] = $item;
	}
	echo implode(' : ', array_reverse ($items));
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
		echo " href='index.php?page=${pageno}&tab=${tabidx}";
		if (isset ($page[$pageno]['bypass']) and isset ($_REQUEST[$page[$pageno]['bypass']]))
		{
			$bpname = $page[$pageno]['bypass'];
			$bpval = $_REQUEST[$bpname];
			echo "&${bpname}=${bpval}";
		}
		if (isset ($page[$pageno]['bypass_tabs']))
			foreach ($page[$pageno]['bypass_tabs'] as $param_name)
				if (isset ($_REQUEST[$param_name]))
					echo "&" . urlencode ($param_name) . '=' . urlencode ($_REQUEST[$param_name]);
		
		echo "'>${tabtitle}</a></li>\n";
	}
	echo "</ul></div>";
}

// Arg is path page number, which can be different from the primary page number,
// for example title for 'ipv4net' can be requested to build navigation path for
// both IPv4 network and IPv4 address. Another such page number is 'row', which
// fires for both row and its racks. Use pageno for decision in such cases.
function dynamic_title_decoder ($path_position)
{
	global $sic;
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
			'name' => niftyString ($file['name'], 30, FALSE),
			'params' => array ('file_id' => $_REQUEST['file_id'])
		);
	case 'ipaddress':
		$address = getIPv4Address ($_REQUEST['ip']);
		return array
		(
			'name' => niftyString ($_REQUEST['ip'] . ($address['name'] != '' ? ' (' . $address['name'] . ')' : ''), 50, FALSE),
			'params' => array ('ip' => $_REQUEST['ip'])
		);
	case 'ipv6address':
		$address = getIPv6Address (assertIPArg ('ip'));
		return array
		(
			'name' => niftyString ($address['ip'] . ($address['name'] != '' ? ' (' . $address['name'] . ')' : ''), 50, FALSE),
			'params' => array ('ip' => $address['ip'])
		);
	case 'ipv4net':
	case 'ipv6net':
        global $pageno;
        switch ($pageno)
		{
			case 'ipaddress':
				$range = spotEntity ('ipv4net', getIPv4AddressNetworkId ($_REQUEST['ip']));
				$net_id = $range['id'];
				return array
				(
					'name' => $range['ip'] . '/' . $range['mask'],
					'params' => array
					(
						'id' => $range['id'],
						'page' => 'ipv4net',
						'hl_ipv4_addr' => $_REQUEST['ip']
					)
				);
			case 'ipv6address':
				$ipv6 = assertIPArg ('ip');
				$range = spotEntity ('ipv6net', getIPv6AddressNetworkId ($ipv6));
				$net_id = $range['id'];
				return array
				(
					'name' => $range['ip'] . '/' . $range['mask'],
					'params' => array
					(
						'id' => $range['id'],
						'page' => 'ipv6net',
						'hl_ipv6_addr' => $_REQUEST['ip']
					)
				);
			default:
				assertUIntArg ('id');
				$range = spotEntity ($path_position, $_REQUEST['id']);
				$net_id = $range['id'];
				return array
				(
					'name' => $range['ip'] . '/' . $range['mask'],
					'params' => array ('id' => $_REQUEST['id'])
				);
		}
	case 'ipv4space':
	case 'ipv6space':
		global $pageno;
		$ip_ver = preg_replace ('/[^\d]*/', '', $path_position);
		$params = isset ($net_id) ? array ('eid' => $net_id, 'hl_net' => 1) : array();
		unset ($net_id);
		return array
		(
			'name' => "IPv$ip_ver space",
			'params' => $params,
		);
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
			$rowInfo = getRowInfo ($_REQUEST['row_id']);
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
		$vdlist = getVLANDomainOptions();
		if (!array_key_exists ($vdom_id, $vdlist))
			throw new EntityNotFoundException ('VLAN domain', $vdom_id);
		return array
		(
			'name' => niftyString ("domain '" . $vdlist[$vdom_id] . "'", 20, FALSE),
			'params' => array ('vdom_id' => $vdom_id)
		);
	case 'vlan':
		return array
		(
			'name' => formatVLANName (getVLANInfo ($sic['vlan_ck']), 'plain long'),
			'params' => array ('vlan_ck' => $sic['vlan_ck'])
		);
	case 'vst':
		$vst = spotEntity ('vst', $sic['vst_id']);
		return array
		(
			'name' => niftyString ("template '" . $vst['description'] . "'", 50, FALSE),
			'params' => array ('vst_id' => $sic['vst_id'])
		);
	case 'dqueue':
		global $dqtitle;
		return array
		(
			'name' => 'queue "' . $dqtitle[$sic['dqcode']] . '"',
			'params' => array ('qcode' => $sic['dqcode'])
		);
	default:
		return array
		(
			'name' => __FUNCTION__ . '() failure',
			'params' => array()
		);
	}
}

function renderIIFOIFCompat()
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

function renderIIFOIFCompatEditor()
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
	$iif = getPortIIFOptions();
	global $nextorder, $wdm_packs;
	$order = 'odd';
	echo '<table border=0 align=center cellspacing=0 cellpadding=5>';
	foreach ($wdm_packs as $codename => $packinfo)
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
		printImageHREF ('delete', 'remove pair');
		echo "</a></td><td class=tdleft>${record['iif_name']}</td><td class=tdleft>${record['oif_name']}</td></tr>";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewitemTR();
	echo '</table>';
	finishPortlet();
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
			// hide any object, which is already in the table
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
			printSelect ($options, array ('name' => 'object_id', 'tabindex' => 101, 'size' => getConfigVar ('MAXSELSIZE')), $focus['prev_objid']);
			echo '</td>';
		}
		if ($pageno != 'vlandomain')
			echo '<td>' . getSelect (getVLANDomainOptions(), array ('name' => 'vdom_id', 'tabindex' => 102, 'size' => getConfigVar ('MAXSELSIZE')), $focus['prev_vdid']) . '</td>';
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
					$options[$nominee['id']] = niftyString ($nominee['description'], 30, FALSE);
			}
			echo '<td>' . getSelect ($options, array ('name' => 'vst_id', 'tabindex' => 103, 'size' => getConfigVar ('MAXSELSIZE')), $focus['prev_vstid']) . '</td>';
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
			$cutblock = '<a href="' . makeHrefProcess ($args) . '">';
			$cutblock .= getImageHREF ('Cut', 'unbind') . '</a>';
		}
		restoreContext ($ctx);
		echo '<tr>';
		if ($pageno != 'object')
		{
			$object = spotEntity ('object', $item_object_id);
			echo '<td><a href="' . makeHREF (array ('page' => 'object', 'object_id' => $object['id'])) . '">';
			echo "${object['dname']}</a></td>";
		}
		if ($pageno != 'vlandomain')
			echo '<td><a href="' . makeHREF (array ('page' => 'vlandomain', 'vdom_id' => $item['vdom_id'])) . '">' .
				$vdomlist[$item['vdom_id']] . '</a></td>';
		if ($pageno != 'vst')
			echo '<td><a href="' . makeHREF (array ('page' => 'vst', 'vst_id' => $item['vst_id'])) . '">' .
				$vstlist[$item['vst_id']] . '</a></td>';
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
			echo "<tr align=left><td><a href='";
			echo makeHref (array ('page' => 'vlandomain', 'vdom_id' => $vdom_id)) . "'>";
			echo niftyString ($dominfo['description']) . '</a></td>';
			foreach ($columns as $cname)
				echo '<td>' . $dominfo[$cname] . '</td>';
			echo '</tr>';
		}
		if (count ($vdlist) > 1)
		{
			echo '<tr align=left><td>total:</td>';
			foreach ($columns as $cname)
				echo '<td>' . $stats[$cname] . '</td>';
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
			echo mkA (niftyString ($vst_info['description']), 'vst', $vst_id);
			if (count ($vst_info['etags']))
				echo '<br><small>' . serializeTags ($vst_info['etags']) . '</small>';
			echo '</td>';
			echo "<td>${vst_info['rulec']}</td><td>${vst_info['switchc']}</td></tr>";
		}
		echo '</table>';
	}
	finishPortlet();

	echo '</td><td class=pcright>';

	startPortlet ('deploy queues');
	$enabled_total = 0;
	$disabled_total = 0;
	echo '<table border=0 cellspacing=0 cellpadding=3 width="100%">';
	foreach (get8021QDeployQueues() as $qcode => $qitems)
	{
		echo '<tr><th width="50%" class=tdright><a href="' . makeHREF (array ('page' => 'dqueue', 'dqcode' => $qcode));
		echo '">' . $dqtitle[$qcode] . '</a>:</th>';
	    echo '<td class=tdleft>' . count ($qitems['enabled']) . '</td></tr>';

		$enabled_total += count ($qitems['enabled']);
		$disabled_total += count ($qitems['disabled']);
	}
	echo '</table>';
	$total = $enabled_total + $disabled_total;
	echo "<p align=left>$total switches total";
	if ($disabled_total)
		echo ', <a href="' . makeHREF (array ('page' => 'dqueue', 'dqcode' => 'disabled')) . '">' .
		$disabled_total . "</a> disabled";
	echo '</p>';
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
		echo '<input type=text size=48 name=vdom_descr tabindex=102>';
		echo '</td><td>';
		printImageHREF ('create', 'create domain', TRUE, 103);
		echo '</td></tr></form>';
	}
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>&nbsp;</th><th>description</th><th>&nbsp</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach (getVLANDomainStats() as $vdom_id => $dominfo)
	{
		printOpFormIntro ('upd', array ('vdom_id' => $vdom_id));
		echo '<tr><td>';
		if ($dominfo['switchc'] or $dominfo['vlanc'] > 1)
			printImageHREF ('nodestroy', 'domain used elsewhere');
		else
		{
			echo '<a href="';
			echo makeHrefProcess (array ('op' => 'del', 'vdom_id' => $vdom_id)) . '">';
			printImageHREF ('destroy', 'delete domain');
			echo '</a>';
		}
		echo '</td><td><input name=vdom_descr type=text size=48 value="';
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

	if (!count ($myvlans = getDomainVLANs ($vdom_id)))
		startPortlet ('no VLANs');
	else
	{
		startPortlet ('VLANs (' . count ($myvlans) . ')');
		$order = 'odd';
		global $vtdecoder;
		echo '<table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>';
		echo '<tr><th>VLAN ID</th><th>propagation</th><th>';
		printImageHREF ('net', 'IPv4 networks linked');
		echo '</th><th>ports</th><th>description</th></tr>';
		foreach ($myvlans as $vlan_id => $vlan_info)
		{
			echo "<tr class=row_${order}><td class=tdright><a href='";
			echo makeHref (array ('page' => 'vlan', 'vlan_ck' => "${vdom_id}-${vlan_id}"));
			echo "'>${vlan_id}</a></td>";
			echo '<td>' . $vtdecoder[$vlan_info['vlan_type']] . '</td>';
			echo '<td class=tdright>' . ($vlan_info['netc'] ? $vlan_info['netc'] : '&nbsp;') . '</td>';
			echo '<td class=tdright>' . ($vlan_info['portc'] ? $vlan_info['portc'] : '&nbsp;') . '</td>';
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
		echo '<input type=text size=48 name=vlan_descr tabindex=103>';
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
		echo '<tr><td>';
		if ($vlan_info['portc'] or $vlan_id == VLAN_DFL_ID)
			printImageHREF ('nodestroy', $vlan_info['portc'] . ' ports configured');
		else
		{
			echo '<a href="';
			echo makeHrefProcess (array ('op' => 'del', 'vdom_id' => $vdom_id, 'vlan_id' => $vlan_id)) . '">';
			echo getImageHREF ('destroy', 'delete VLAN') . '</a>';
		}
		echo '</td><td class=tdright><tt>' . $vlan_id . '</tt></td><td>';
		printSelect ($vtoptions, array ('name' => 'vlan_type'), $vlan_info['vlan_type']);
		echo '</td><td>';
		echo '<input name=vlan_descr type=text size=48 value="' . htmlspecialchars ($vlan_info['vlan_descr']) . '">';
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
	$desired_config = apply8021QOrder ($vswitch['template_id'], getStored8021QConfig ($object_id, 'desired'));
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
	$object = spotEntity ('object', $object_id);
	amplifyCell ($object);
	$sockets = array();
	if (isset ($_REQUEST['hl_port_id']))
	{
		assertUIntArg ('hl_port_id');
		$hl_port_id = intval ($_REQUEST['hl_port_id']);
		$hl_port_name = NULL;
		addAutoScrollScript ("port-$hl_port_id");
	}
	foreach ($object['ports'] as $port)
		if (mb_strlen ($port['name']) and array_key_exists ($port['name'], $desired_config))
		{
			if (isset ($hl_port_id) and $hl_port_id == $port['id'])
				$hl_port_name = $port['name'];
			$socket = array ('interface' => formatPortIIFOIF ($port));
			if ($port['remote_object_id'])
				$socket['link'] = formatLoggedSpan ($port['last_log'], formatLinkedPort ($port));
			elseif (strlen ($port['reservation_comment']))
				$socket['link'] = formatLoggedSpan ($port['last_log'], 'Rsv:', 'strong underline') . ' ' .
				formatLoggedSpan ($port['last_log'], $port['reservation_comment']);
			else
				$socket['link'] = '&nbsp;';
			$sockets[$port['name']][] = $socket;
		}
	unset ($object);
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
			$trclass = 'trerror'; // stuck ghost port
			$text_right = '&nbsp;';
			break;
		case 'downlink':
			$text_right = '(downlink)';
			$trclass = 'trbusy';
			break;
		case 'uplink':
			$text_right = serializeVLANPack ($uplinks[$port_name]);
			$trclass = same8021QConfigs ($port, $uplinks[$port_name]) ? 'trbusy' : 'trwarning';
			break;
		case 'trunk':
			$trclass =
			(
				$port['vst_role'] != $port['mode'] or
				count (array_diff ($port['allowed'], array_keys ($vdom['vlanlist'])))
			) ? 'trwarning' : 'trbusy';
			$text_right = getTrunkPortCursorCode ($object_id, $port_name, $req_port_name);
			break;
		case 'access':
			$trclass =
			(
				$port['vst_role'] != $port['mode'] or
				!array_key_exists ($port['native'], $vdom['vlanlist'])
			) ? 'trwarning' : 'trbusy';
			// ---
			$text_right = getAccessPortControlCode ($req_port_name, $vdom, $port_name, $port, $nports);
			break;
		case 'anymode':
			$trclass = count (array_diff ($port['allowed'], array_keys ($vdom['vlanlist']))) ?
				'trwarning' : 'trbusy';
			$text_right = getAccessPortControlCode ($req_port_name, $vdom, $port_name, $port, $nports);
			$text_right .= '&nbsp;';
			$text_right .= getTrunkPortCursorCode ($object_id, $port_name, $req_port_name);
			break;
		default:
			throw new InvalidArgException ('vst_role', $port['vst_role']);
		}
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
		$ancor = '';
		$tdclass = '';
		if (isset ($hl_port_name) and $hl_port_name == $port_name)
		{
			$tdclass .= 'class="border_highlight"';
			$ancor = "name='port-$hl_port_id'";
		}
		echo "<tr class='${trclass}' valign=top><td${td_extra} ${tdclass} NOWRAP><a class='interactive-portname port-menu nolink' $ancor>${port_name}</a></td>" . $socket_columns;
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
		echo '<li>' . getImageHREF ('SAVE', 'save configuration', TRUE, 100) . '</li>';
	}
	echo '</form>';
	if (permitted (NULL, NULL, NULL, array (array ('tag' => '$op_recalc8021Q'))))
		echo '<li><a href="' . makeHrefProcess (array ('op' => 'exec8021QRecalc', 'object_id' => $object_id)) . '">' .
			getImageHREF ('RECALC', 'Recalculate uplinks and downlinks') . '</a></li>';
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
		return formatVLANName ($vdom['vlanlist'][$port['native']], 'label');

	static $vlanpermissions = array();
	if (!array_key_exists ($port['native'], $vlanpermissions))
	{
		$vlanpermissions[$port['native']] = array();
		foreach (array_keys ($vdom['vlanlist']) as $to)
			if
			(
				permitted (NULL, NULL, 'save8021QConfig', array (array ('tag' => '$fromvlan_' . $port['native']), array ('tag' => '$vlan_' . $port['native']))) and
				permitted (NULL, NULL, 'save8021QConfig', array (array ('tag' => '$tovlan_' . $to), array ('tag' => '$vlan_' . $to)))
			)
				$vlanpermissions[$port['native']][] = $to;
	}
	$ret = "<input type=hidden name=pn_${nports} value=${port_name}>";
	$ret .= "<input type=hidden name=pm_${nports} value=access>";
	$options = array();
	// Offer only options, which are listed in domain and fit into VST.
	// Never offer immune VLANs regardless of VST filter for this port.
	// Also exclude current VLAN from the options, unless current port
	// mode is "trunk" (in this case it should be possible to set VST-
	// approved mode without changing native VLAN ID).
	foreach ($vdom['vlanlist'] as $vlan_id => $vlan_info)
		if
		(
			($vlan_id != $port['native'] or $port['mode'] == 'trunk') and
			$vlan_info['vlan_type'] != 'alien' and
			in_array ($vlan_id, $vlanpermissions[$port['native']]) and
			matchVLANFilter ($vlan_id, $port['wrt_vlans'])
		)
			$options[$vlan_id] = formatVLANName ($vlan_info, 'option');
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
			'text' => formatVLANName ($vlan_info, 'label'),
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
					'text' => formatVLANName ($vdom['vlanlist'][$vlan_id], 'label'),
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
			// disabled. These measures make it harder for the system to break a VLAN,
			// which is explicitly protected from it.
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
	echo '<table border=0 class=objectview cellspacing=0 cellpadding=0>';
	echo '<tr><td colspan=2 align=center><h1>' . formatVLANName ($vlan, 'markup long') . '</h1></td></tr>';
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
	$others = getSearchResultByField
	(
		'VLANDescription',
		array ('domain_id'),
		'vlan_id',
		$vlan['vlan_id'],
		'domain_id',
		1
	);
	foreach ($others as $other)
		if ($other['domain_id'] != $vlan['domain_id'])
			echo '<tr><th class=tdright>Counterpart:</th><td class=tdleft>' .
				formatVLANName (getVLANInfo ("${other['domain_id']}-${vlan['vlan_id']}"), 'hyperlink') .
				'</td></tr>';
	echo '</table>';
	finishPortlet();
	if (0 == count ($vlan['ipv4nets']) + count ($vlan['ipv6nets']))
		startPortlet ('no networks');
	else
	{
		startPortlet ('networks (' . (count ($vlan['ipv4nets']) + count ($vlan['ipv6nets'])) . ')');
		$order = 'odd';
		echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
		echo '<tr><th>';
		printImageHREF ('net');
		echo '</th><th>';
		printImageHREF ('text');
		echo '</th></tr>';
		foreach (array ('ipv4net', 'ipv6net') as $nettype)
		foreach ($vlan[$nettype . 's'] as $netid)
		{
			$net = spotEntity ($nettype, $netid);
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

	$confports = getVLANConfiguredPorts ($vlan_ck);

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
					echo formatPortLink ($object['id'], NULL, $portinfo['id'], $portinfo['name']);
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
		echo '<tr><td>' . getOptionTree ($sname, $options, array ('tabindex' => 101));
		echo '</td><td>' . getImageHREF ('ATTACH', 'bind', TRUE, 102) . '</td></tr></form>';
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
		foreach ($vlan[$ip_ver . "nets"] as $net_id)
			$minuslines[] = array
			(
				'net_id' => $net_id,
				'domain_id' => $vlan['domain_id'],
				'vlan_id' => $vlan['vlan_id'],
			);
		// Any VLAN can link to any network, which isn't yet linked to current domain.
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
		amplifyCell ($netinfo);
		# find out VLAN domains, where the current network already has a VLAN linked
		$except_domains = array();
		foreach ($netinfo['8021q'] as $item)
		{
			$except_domains[] = $item['domain_id'];
			$minuslines[] = array
			(
				'net_id' => $netinfo['id'],
				'domain_id' => $item['domain_id'],
				'vlan_id' => $item['vlan_id'],
			);
		}
		# offer VLANs from all other domains
		$plusoptions = getAllVLANOptions ($except_domains);
		$select_name = 'vlan_ck';
		$extra = array ('id' => $netinfo['id']);
		break;
	}
	echo '<th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($select_name, $plusoptions, $extra);
	foreach ($minuslines as $item)
	{
		echo '<tr class=trbusy><td>';
		switch ($pageno)
		{
		case 'vlan':
			renderCell (spotEntity ($ip_ver . 'net', $item['net_id']));
			break;
		case 'ipv4net':
		case 'ipv6net':
			$vlaninfo = getVLANInfo ($item['domain_id'] . '-' . $item['vlan_id']);
			echo formatVLANName ($vlaninfo, 'markup long');
			break;
		}
		echo '</td><td><a href="';
		echo makeHrefProcess
		(
			array
			(
				'id' => $some_id,
				'op' => 'unbind',
				'id' => $item['net_id'],
				'vlan_ck' => $item['domain_id'] . '-' . $item['vlan_id']
			)
		);
		echo '">' . getImageHREF ('Cut', 'unbind') . '</a></td></tr>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ($select_name, $plusoptions, $extra);
	echo '</table>';
}

function renderObject8021QSync ($object_id)
{
	$vswitch = getVLANSwitchInfo ($object_id);
	$object = spotEntity ('object', $object_id);
	try
	{
		$R = getRunning8021QConfig ($object_id);
	}
	catch (Exception $re)
	{
		showWarning ('Device configuration unavailable:<br>' . $re->getMessage());
		return;
	}
	$D = getStored8021QConfig ($vswitch['object_id'], 'desired');
	$C = getStored8021QConfig ($vswitch['object_id'], 'cached');
	$plan = apply8021QOrder ($vswitch['template_id'], get8021QSyncOptions ($vswitch, $D, $C, $R['portdata']));
	$maxdecisions = 0;
	foreach ($plan as $port)
		if
		(
			$port['status'] == 'delete_conflict' or
			$port['status'] == 'merge_conflict' or
			$port['status'] == 'add_conflict' or
			$port['status'] == 'martian_conflict'
		)
			$maxdecisions++;

	if (isset ($_REQUEST['hl_port_id']))
	{
		assertUIntArg ('hl_port_id');
		$hl_port_id = intval ($_REQUEST['hl_port_id']);
		$hl_port_name = NULL;
		addAutoScrollScript ("port-$hl_port_id");

		amplifyCell ($object);
		foreach ($object['ports'] as $port)
			if (mb_strlen ($port['name']) && $port['id'] == $hl_port_id)
			{
				$hl_port_name = $port['name'];
				break;
			}
	}
	
	echo '<table border=0 class=objectview cellspacing=0 cellpadding=0>';
	echo '<tr><td class=pcleft width="50%">';

	startPortlet ('schedule');
	echo '<table border=0 cellspacing=0 cellpadding=3 align=center>';
	// FIXME: sort rows newest event last
	$rows = array();
	if (! considerConfiguredConstraint ($object, 'SYNC_802Q_LISTSRC'))
		$rows['auto sync'] = '<span class="trerror">disabled by operator</span>';
	$rows['last local change'] = $vswitch['last_change'] . ' (' . $vswitch['last_change_age'] . ' ago)';
	$rows['device out of sync'] = $vswitch['out_of_sync'];
	if ($vswitch['out_of_sync'] == 'no')
	{
		$rows['last sync session with device'] = $vswitch['last_push_finished'] . ' (' . $vswitch['last_push_age'] .
			' ago, lasted ' . $vswitch['last_push_lasted'] . ')';
	}
	if ($vswitch['last_errno'])
		$rows['failed'] = $vswitch['last_error_ts'] . ' (' . strerror8021Q ($vswitch['last_errno']) . ')';
	foreach ($rows as $th => $td)
		echo "<tr><th width='50%' class=tdright>${th}:</th><td class=tdleft colspan=2>${td}</td></tr>";

	echo '<tr><th class=tdright>run now:</th><td class=tdcenter>';
	printOpFormIntro ('exec8021QPull');
	echo getImageHREF ('prev', 'pull remote changes in', TRUE, 101) . '</form></td><td class=tdcenter>';
	if ($maxdecisions)
		echo getImageHREF ('COMMIT gray', 'cannot push due to version conflict(s)');
	else
	{
		printOpFormIntro ('exec8021QPush');
		echo getImageHREF ('COMMIT', 'push local changes out', TRUE, 102) . '</form>';
	}
	echo '</td></tr>';
	echo '</table>';
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

	echo '</td><td class=pcright>';

	startPortlet ('preview/resolve');

	switchportInfoJS ($object_id); // load JS code to make portnames interactive
	// initialize one of three popups: we've got data already
	$port_config = addslashes (json_encode (formatPortConfigHints ($object_id, $R)));
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
				"onclick=\"checkColumnOfRadios('i_', ${maxdecisions}, '_${pos}')\"></th>";
	}
	echo '<th width="40%">running&nbsp;version</th></tr>';
	$rownum = 0;
	$plan = sortPortList ($plan);
	$domvlans = array_keys (getDomainVLANs ($vswitch['domain_id']));
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

		$ancor = '';
		$td_class = '';
		if (isset ($hl_port_name) and $hl_port_name == $port_name)
		{
			$ancor = "name='port-$hl_port_id'";
			$td_class = ' border_highlight';
		}
		echo "<tr class='${trclass}'><td class='tdleft${td_class}' NOWRAP><a class='interactive-portname port-menu nolink' $ancor>${port_name}</a></td>";
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
	finishPortlet();

	echo '</td></tr></table>';
}

function renderVSTListEditor()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr>';
		echo '<td>' . getImageHREF ('create', 'create template', TRUE, 104) . '</td>';
		echo '<td><input type=text size=48 name=vst_descr tabindex=101></td>';
		echo '<td>' . getImageHREF ('create', 'create template', TRUE, 103) . '</td>';
		echo '</tr></form>';
	}
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>&nbsp;</th><th>description</th><th>&nbsp</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach (listCells ('vst') as $vst_id => $vst_info)
	{
		printOpFormIntro ('upd', array ('vst_id' => $vst_id));
		echo '<tr><td>';
		if ($vst_info['switchc'])
			printImageHREF ('nodestroy', 'template used elsewhere');
		else
		{
			echo '<a href="' . makeHrefProcess (array ('op' => 'del', 'vst_id' => $vst_id)) . '">';
			echo getImageHREF ('destroy', 'delete template') . '</a>';
		}
		echo '</td>';
		echo '<td><input name=vst_descr type=text size=48 value="' . niftyString ($vst_info['description'], 0) . '"></td>';
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
	echo '<tr><td colspan=2 align=center><h1>' . niftyString ($vst['description'], 0) . '</h1><h2>';
	echo "<tr><td class=pcleft width='50%'>";
	startPortlet ('summary');
	echo '<table border=0 cellspacing=0 cellpadding=3 width="100%">';
	printTagTRs ($vst);
	echo '</table>';
	finishPortlet();
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
	$vst = spotEntity ('vst', $vst_id);
	amplifyCell ($vst);
	if ($vst['rulec'])
		$source_options = array();
	else
	{
		$source_options = array();
		foreach (listCells ('vst') as $vst_id => $vst_info)
			if ($vst_info['rulec'])
				$source_options[$vst_id] = niftyString ('(' . $vst_info['rulec'] . ') ' . $vst_info['description']);
	}
	addJS ('js/vst_editor.js');
	echo '<center><h1>' . niftyString ($vst['description']) . '</h1></center>';
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
	printOpFormIntro ('upd');
	echo '<table cellspacing=0 cellpadding=5 align=center class="widetable template-rules">';
	echo '<tr><th></th><th>sequence</th><th>regexp</th><th>role</th>';
	echo '<th>VLAN IDs</th><th>comment</th><th><a href="#" class="vst-add-rule initial">' . getImageHREF ('add', 'Add rule') . '</a></th></tr>';
	global $port_role_options;
	$row_html  = '<td><a href="#" class="vst-del-rule">' . getImageHREF ('destroy', 'delete rule') . '</a></td>';
	$row_html .= '<td><input type=text name=rule_no value="%s" size=3></td>';
	$row_html .= '<td><input type=text name=port_pcre value="%s"></td>';
	$row_html .= '<td>%s</td>';
	$row_html .= '<td><input type=text name=wrt_vlans value="%s"></td>';
	$row_html .= '<td><input type=text name=description value="%s"></td>';
	$row_html .= '<td><a href="#" class="vst-add-rule">' . getImageHREF ('add', 'Duplicate rule') . '</a></td>';
	addJS ("var new_vst_row = '" . addslashes (sprintf ($row_html, '', '', getSelect ($port_role_options, array ('name' => 'port_role'), 'anymode'), '', '')) . "';", TRUE);
	foreach (isset ($_SESSION['vst_edited']) ? $_SESSION['vst_edited'] : $vst['rules'] as $item)
		printf ('<tr>' . $row_html . '</tr>', $item['rule_no'], htmlspecialchars ($item['port_pcre'], ENT_QUOTES),  getSelect ($port_role_options, array ('name' => 'port_role'), $item['port_role']), $item['wrt_vlans'], $item['description']);
	echo '</table>';
	echo '<input type=hidden name="template_json">';
	echo '<input type=hidden name="mutex_rev" value="' . $vst['mutex_rev'] . '">';
	echo '<center>' . getImageHref ('SAVE', 'Save template', TRUE) . '</center>';
	echo '</form>';
	if (isset ($_SESSION['vst_edited']))
	{
		// draw current template
		renderVSTRules ($vst['rules'], 'currently saved tamplate');
		unset ($_SESSION['vst_edited']);
	}
	
	if (count ($source_options))
		finishPortlet();
}

function renderDeployQueue()
{
	global $nextorder, $dqtitle;
	$order = 'odd';
	$dqcode = getBypassValue();
	$allq = get8021QDeployQueues();
	$en_key = $dqcode == 'disabled' ? 'disabled' : 'enabled';
	foreach ($allq as $qcode => $data)
		if ($dqcode == 'disabled' || $dqcode == $qcode)
		{
			if (! count ($data[$en_key]))
				continue;
			if ($dqcode == 'disabled')
				echo "<h2 align=center>Queue " . $dqtitle[$qcode] . " (" . count ($data[$en_key]) . ")</h2>";
			echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
			echo '<tr><th>switch</th><th>age</th><th>';
			foreach ($data[$en_key] as $item)
			{
				echo "<tr class=row_${order}><td>";
				renderCell (spotEntity ('object', $item['object_id']));
				echo "</td><td>${item['last_change_age']}</td></tr>";
				$order = $nextorder[$order];
			}
			echo '</table>';
		}
}

function renderDiscoveredNeighbors ($object_id)
{
	global $tabno;
	static $POIFC;
	
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
		showWarning ($e->getMessage());
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
				amplifyCell($dp_remote_object);

				// get port list which names match CDP portname
				$remote_ports = array(); // list of remote (by DP info) ports
				foreach ($dp_remote_object['ports'] as $port)
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
							getExistingPortTypeOptions ($portinfo['id']);
						foreach ($tmp_types as $oif_id => $oif_name)
							$port_types[$side][$oif_id][] = array ('id' => $oif_id, 'name' => $oif_name, 'portinfo' => $portinfo);
					}

				if (! isset ($POIFC))
				{
					$POIFC = array();
					foreach (getPortOIFCompat() as $item)
					{
						$POIFC[$item['type1']][$item['type2']] = TRUE;
						$POIFC[$item['type2']][$item['type1']] = TRUE;
					}
				}
				foreach ($port_types['left'] as $left_id => $left)
				foreach ($port_types['right'] as $right_id => $right)
					if (isset ($POIFC[$left_id][$right_id]))
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
			echo "<td>" . ($portinfo_local ?  formatPortIIFOIF ($portinfo_local) : '&nbsp') . "</td>";
			echo "<td>" . formatIfTypeVariants ($variants, "ports_${inputno}") . "</td>";
			echo "<td>${dp_neighbor['device']}</td>";
			echo "<td>" . ($portinfo_remote ? formatPortLink ($dp_remote_object_id, NULL, $portinfo_remote['id'], $portinfo_remote['name']) : $dp_neighbor['port'] ) . "</td>";
			echo "<td>" . ($portinfo_remote ?  formatPortIIFOIF ($portinfo_remote) : '&nbsp') . "</td>";
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
	if (! isset ($record['key'])) // if record is a dictionary value, generate href with autotag in cfe
	{
		if ($record['id'] == 3) // FQDN attribute
		{
			$protos_to_try = array (
				'ssh' => 'SSH_OBJS_LISTSRC',
				'telnet' => 'TELNET_OBJS_LISTSRC',
			);
			foreach ($protos_to_try as $proto => $cfgvar)
				if (considerConfiguredConstraint (NULL, $cfgvar))
					return "<a title='Open $proto session' class='mgmt-link' href='" . $proto . '://' . $record['a_value'] . "'>${record['a_value']}</a>";
		}
		return isset ($record['href']) ? "<a href=\"".$record['href']."\">${record['a_value']}</a>" : $record['a_value'];
	}
	else
	{
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
			$result .= "&nbsp;<a class='img-link' href='${record['href']}'>" . getImageHREF ('html', 'vendor`s info page') . "</a>";
		return $result;
	}
}

function addAutoScrollScript ($ancor_name)
{
	addJS (<<<END
$(document).ready(function() {
	var ancor = document.getElementsByName('$ancor_name')[0];
	if (ancor)
		ancor.scrollIntoView(false);
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

	if (isset($_REQUEST['object_id']))
	{
		$entity = 'object';
		$id_name = 'object_id';
		$object_id = $_REQUEST['object_id'];
	}
	else
	{
		$entity = 'rack';
		$id_name = 'rack_id';
		$object_id = $_REQUEST['rack_id'];
	}

	echo "<center><h2>Log records for this ${entity} (<a href=?page=objectlog>complete list</a>)</h2></center>";
	printOpFormIntro ('add');
	echo "<table with=80% align=center border=0 cellpadding=5 cellspacing=0 align=center class=cooltable><tr valign=top class=row_odd>";
	echo '<td class=tdcenter>' . getImageHREF ('CREATE', 'add record', TRUE, 101) . '</td>';
	echo '<td><textarea name=logentry rows=10 cols=80 tabindex=100></textarea></td>';
	echo '<td class=tdcenter>' . getImageHREF ('CREATE', 'add record', TRUE, 101) . '</td>' ;
	echo '</tr></form>';

	$order = 'even';
	foreach (getLogRecordsForObject ($object_id) as $row)
	{
		echo "<tr class=row_${order} valign=top>";
		echo '<td class=tdleft>' . $row['date'] . '<br>' . $row['user'] . '</td>';
		echo '<td class="slbconf rsvtext">' . string_insert_hrefs (htmlspecialchars ($row['content'], ENT_NOQUOTES)) . '</td>';
		echo "<td class=tdleft><a href=\"".makeHrefProcess(array('op'=>'del', 'log_id'=>$row['id'], $id_name=>$object_id))."\">";
		echo getImageHREF ('DESTROY', 'Delete log entry') . '</a></td>';
		echo "</tr>\n";
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
			// Link to a different page if the object is a Rack
			if ($row['objtype_id'] == 1560)
			{
				$entity = 'rack';
				$id_name = 'rack_id';
			}
			else
			{
				$entity = 'object';
				$id_name = 'object_id';
			}
			echo "<tr class=row_${order} valign=top>";
			echo "<td align=left><a href='".makeHref(array('page'=>$entity, 'tab'=>'log', $id_name=>$row['object_id']))."'>${row['name']}</a></td>";
			echo '<td class=tdleft>' . $row['date'] . '<br>' . $row['user'] . '</td>';
			echo '<td class="slbconf rsvtext">' . string_insert_hrefs (htmlspecialchars ($row['content'], ENT_NOQUOTES)) . '</td>';
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
		echo "<tr><th>Cluster</th><th>Hypervisors</th><th>VMs</th></tr>\n";
		$order = 'odd';
		foreach ($clusters as $cluster)
		{
			echo "<tr class=row_${order} valign=top><td class='tdleft'><a href='".makeHref(array('page'=>'object', 'object_id'=>$cluster['id']))."'><strong>${cluster['name']}</strong></a></td>";
			echo "<td class='tdleft'>${cluster['hypervisors']}</td>";
			echo "<td class='tdleft'>${cluster['VMs']}</td>";
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
			echo "<tr class=row_${order} valign=top><td class='tdleft'><a href='".makeHref(array('page'=>'object', 'object_id'=>$pool['id']))."'><strong>${pool['name']}</strong></a></td>";
			echo "<td class='tdleft'><a href='".makeHref(array('page'=>'object', 'object_id'=>$pool['cluster_id']))."'><strong>${pool['cluster_name']}</strong></a></td>";
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
			echo "<tr class=row_${order} valign=top><td class='tdleft'><a href='".makeHref(array('page'=>'object', 'object_id'=>$hypervisor['id']))."'><strong>${hypervisor['name']}</strong></a></td>";
			echo "<td class='tdleft'><a href='".makeHref(array('page'=>'object', 'object_id'=>$hypervisor['cluster_id']))."'><strong>${hypervisor['cluster_name']}</strong></a></td>";
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
			echo "<tr class=row_${order} valign=top><td class='tdleft'><a href='".makeHref(array('page'=>'object', 'object_id'=>$switch['id']))."'><strong>${switch['name']}</strong></a></td>";
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
	$availible_ops = array
	(
		'link' => array ('op' => 'get_link_status', 'gw' => 'getportstatus'),
		'conf' => array ('op' => 'get_port_conf', 'gw' => 'get8021q'),
		'mac' =>  array ('op' => 'get_mac_list', 'gw' => 'getmaclist'),
	);
	$breed = detectDeviceBreed ($object_id);
	$allowed_ops = array();
	foreach ($availible_ops as $prefix => $data)
		if
		(
			permitted ('object', 'liveports', $data['op']) and
			validBreedFunction ($breed, $data['gw'])
		)
			$allowed_ops[] = $prefix;

	// make JS array with allowed items
	$list = '';
	foreach ($allowed_ops as $item)
		$list .= "'" . addslashes ($item) . "', ";
	$list = trim ($list, ", ");

	addJS ('js/jquery.thumbhover.js');
	addCSS ('css/jquery.contextmenu.css');
	addJS ('js/jquery.contextmenu.js');
	addJS ("enabled_elements = [ $list ];", TRUE);
	addJS ('js/portinfo.js');
}

function addAtomCSS()
{
	// do not add generated css to page twice
	static $is_first_call = TRUE;
	if (! $is_first_call)
		return;
	$is_first_call = FALSE;

	$style = '';
	foreach (array ('F', 'A', 'U', 'T', 'Th', 'Tw', 'Thw') as $statecode)
		$style .= "td.atom.state_${statecode} { background-color: #" . (getConfigVar ('color_' . $statecode)) . "; }\n";
	addCSS ($style, TRUE);
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

function renderIPv4AddressLog ()
{
	assertIPv4Arg('ip');
	startPortlet ('Log messages');
	echo '<table class="widetable" cellspacing="0" cellpadding="5" align="center" width="50%"><tr>';
	echo '<th>Date &uarr;</th>';
	echo '<th>User</th>';
	echo '<th>Log message</th>';
	echo '</tr>';
	$odd = FALSE;
	foreach (array_reverse (fetchIPv4LogEntry($_REQUEST['ip'])) as $line)
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
	if (!extension_loaded ('curl'))
		throw new RackTablesError ("The PHP cURL extension is not loaded.", RackTablesError::MISCONFIGURED);

	if (!($cacti_url = getConfigVar('CACTI_URL')))
		throw new RackTablesError ("Cacti URL not configured.", RackTablesError::MISCONFIGURED);

	startPortlet ('Cacti Graphs');
	echo "<table cellspacing=\"0\" align=\"center\" width=\"50%\">";
	echo "<tr><td>&nbsp;</td><th>Cacti Graph ID</th><th>Caption</th><td>&nbsp;</td></tr>\n";
	printOpFormIntro ('add');
	echo "<tr><td>";
	printImageHREF ('Attach', 'Link new graph', TRUE);
	echo "</td><td><input type=text name=graph_id tabindex=100></td><td><input type=text name=caption tabindex=101></td><td>";
	printImageHREF ('Attach', 'Link new graph', TRUE, 101);
	echo "</td></tr></form>";
	echo "</table>";
	echo "<br/><br/>";

	echo "<table cellspacing=\"0\" cellpadding=\"10\" align=\"center\" width=\"50%\">";

	foreach (getCactiGraphsForObject ($object_id) as $graph_id => $graph)
	{
		echo "<tr><td>";
		echo "<a href='${cacti_url}/graph.php?action=view&local_graph_id=${graph_id}&rra_id=all' target='_blank'>";
		echo "<img src='index.php?module=image&img=cactigraph&object_id=${object_id}&graph_id=${graph_id}' alt='Cacti Graph ID: ${graph_id}'>";
		echo "</a><br/>";
		echo "<a href='" . makeHrefProcess (array ('op' => 'del', 'object_id'=> $object_id, 'graph_id' => $graph_id)) . "' onclick=\"javascript:return confirm('Are you sure you want to delete the graph?')\">" . getImageHREF ('Cut', 'Unlink graph') . "</a>";
		echo "&nbsp; &nbsp;${graph['caption']}";
		echo "</td></tr>";
	}
	echo '</table>';
	finishPortlet ();
}

?>
