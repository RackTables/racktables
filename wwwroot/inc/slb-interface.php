<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

require_once 'slb2-interface.php';

function renderSLBDefConfig()
{
	$defaults = getSLBDefaults();
	startPortlet ('SLB default configs');
	printOpFormIntro ('save');
	echo '<table cellspacing=0 cellpadding=5 align=center>';
	echo '<tr><th class=tdright>VS config</th><td colspan=2><textarea name=vsconfig rows=10 cols=80>' . stringForTextarea ($defaults['vsconfig']) . '</textarea></td>';
	echo '<td rowspan=2>';
	echo '</td></tr>';
	echo '<tr><th class=tdright>RS config</th><td colspan=2><textarea name=rsconfig rows=10 cols=80>' . stringForTextarea ($defaults['rsconfig']) . '</textarea></td></tr>';
	echo '</form></table>';
	printImageHREF ('SAVE', 'Save changes', TRUE);
	finishPortlet();
}

function renderSLBEntityCell ($cell, $highlighted = FALSE)
{
	setEntityColors ($cell);
	$class = "slbcell realm-${cell['realm']} id-${cell['id']} " . getCellClass ($cell, 'list_plain');
	$a_class = $highlighted ? 'highlight' : '';

	echo "<table class='$class'>";
	switch ($cell['realm'])
	{
	case 'object':
		echo "<tr><td><a class='$a_class' href='index.php?page=object&object_id=${cell['id']}'>${cell['dname']}</a>";
		echo "</td></tr><tr><td>";
		printImageHREF ('LB');
		echo "</td></tr>";
		break;
	case 'ipv4vs':
		echo "<tr><td rowspan=3 width='5%'>";
		printImageHREF ('VS');
		echo "</td><td>";
		echo "<a class='$a_class' href='index.php?page=ipv4vs&vs_id=${cell['id']}'>";
		echo $cell['dname'] . "</a></td></tr><tr><td>";
		echo $cell['name'] . '</td></tr>';
		break;
	case 'ipvs':
		echo "<tr><td rowspan=3 width='5%'>";
		printImageHREF ('VS');
		echo "</td><td>";
		echo "<a class='$a_class' href='index.php?page=ipvs&vs_id=${cell['id']}'>";
		echo $cell['name'] . "</a></td></tr>";
		break;
	case 'ipv4rspool':
		echo "<tr><td>";
		echo "<a class='$a_class' href='index.php?page=ipv4rspool&pool_id=${cell['id']}'>";
		echo $cell['name'] == '' ? "ANONYMOUS pool [${cell['id']}]" : stringForTD ($cell['name']);
		echo "</a></td></tr><tr><td>";
		printImageHREF ('RS pool');
		if ($cell['rscount'])
			echo ' <small>(' . $cell['rscount'] . ')</small>';
		echo "</td></tr>";
		break;
	}
	echo "<tr><td>";
	echo count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;';
	echo "</td></tr></table>";

}

function renderSLBEditTab ($entity_id)
{
	global $pageno;
	renderSLBTripletsEdit (spotEntity ($pageno, $entity_id));
}

// called exclusively by renderSLBTripletsEdit. Renders form to add new SLB link.
// realms 1 and 2 are realms to draw inputs for
function renderNewSLBItemForm ($realm1, $realm2)
{
	/**
	 * Returns a list of values, a human readable name and options
	 * for the selecttag for a given realm.
	 */
	function get_realm_data ($realm)
	{
		$name = NULL;
		$list = array();
		$options = array();
		switch ($realm)
		{
			case 'object':
				$name = 'Load balancer';
				$list = getNarrowObjectList ('IPV4LB_LISTSRC');
				$options = array ('name' => 'object_id');
				break;
			case 'ipv4vs':
				$name = 'Virtual service';
				$list = formatEntityList (listCells ('ipv4vs'));
				$options = array ('name' => 'vs_id');
				break;
			case 'ipv4rspool':
				$name = 'RS pool';
				$list = formatEntityList (listCells ('ipv4rspool'));
				$options = array ('name' => 'pool_id');
				break;
			default:
				throw new InvalidArgException('realm', $realm);
		}
		return array ('name' => $name, 'list' => $list, 'options' => $options);
	}

	$realm1_data = get_realm_data ($realm1);
	$realm2_data = get_realm_data ($realm2);
	startPortlet ('Add new');
	echo "<table cellspacing=0 cellpadding=5 align=center>";
	if (count ($realm1_data['list']) && count ($realm2_data['list']))
		printOpFormIntro ('addLB');
	echo "<tr valign=top><th class=tdright>{$realm1_data['name']}</th><td class=tdleft>";
	printSelect ($realm1_data['list'], $realm1_data['options']);
	echo '</td><td class=tdcenter valign=middle rowspan=2>';
	if (count ($realm1_data['list']) && count ($realm2_data['list']))
		printImageHREF ('ADD', 'Configure LB', TRUE);
	else
	{
		$names = array();
		if (! count ($realm1_data['list']))
			$names[] = 'a ' . $realm1_data['name'];
		if (! count ($realm2_data['list']))
			$names[] = 'a ' . $realm2_data['name'];
		$message = 'Please create ' . (implode (' and ', $names)) . '.';
		showNotice ($message);
		printImageHREF ('DENIED', $message, FALSE);
	}
	echo "<tr valign=top><th class=tdright>{$realm2_data['name']}</th><td class=tdleft>";
	printSelect ($realm2_data['list'], $realm2_data['options']);
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>VS config</th><td colspan=2><textarea name=vsconfig rows=10 cols=80></textarea></td></tr>";
	echo "<tr><th class=tdright>RS config</th><td colspan=2><textarea name=rsconfig rows=10 cols=80></textarea></td></tr>";
	echo "<tr><th class=tdright>Priority</th><td class=tdleft colspan=2><input name=prio size=10></td></tr>";
	echo "</form></table>\n";
	finishPortlet();
}

// supports object, ipv4vs, ipv4rspool, ipaddress cell types
function renderSLBTriplets ($cell)
{
	$is_cell_ip = (isset ($cell['ip_bin']) && isset ($cell['vslist']));
	$additional_js_params = $is_cell_ip ? '' : ", {'" . $cell['realm'] . "': " . $cell['id'] . '}';
	$triplets = SLBTriplet::getTriplets ($cell);
	if (count ($triplets))
	{
		$cells = array();
		foreach ($triplets[0]->display_cells as $field)
			$cells[] = $triplets[0]->$field;

		// render table header
		startPortlet ('VS instances (' . count ($triplets) . ')');
		echo "<table cellspacing=0 cellpadding=5 align=center class=widetable><tr>";
		$headers = array
		(
			'object' => 'LB',
			'ipv4vs' => 'VS',
			'ipv4rspool' => 'RS pool',
		);
		foreach ($cells as $slb_cell)
			echo '<th>' . $headers[$slb_cell['realm']] . '</th>';
		foreach (array ('VS config', 'RS config', 'Prio') as $header)
			echo "<th>$header</th>";
		echo "</tr>";

		// render table rows
		global $nextorder;
		$order = 'odd';
		foreach ($triplets as $slb)
		{
			$cells = array();
			foreach ($slb->display_cells as $field)
				$cells[] = $slb->$field;
			echo "<tr valign=top class='row_${order} triplet-row'>";
			foreach ($cells as $slb_cell)
			{
				echo "<td class=tdleft>";
				$highlighted = $is_cell_ip &&
				(
					$slb_cell['realm'] == 'ipv4vs' && $slb->vs['vip_bin'] == $cell['ip_bin'] ||
					$slb_cell['realm'] == 'ipv4rspool' && $slb->vs['vip_bin'] != $cell['ip_bin']
				);
				renderSLBEntityCell ($slb_cell, $highlighted);
				echo "</td>";
			}
			echo "<td class=slbconf>" . htmlspecialchars ($slb->slb['vsconfig']) . "</td>";
			echo "<td class=slbconf>" . htmlspecialchars ($slb->slb['rsconfig']) . "</td>";
			echo "<td class=slbconf>" . htmlspecialchars ($slb->slb['prio']) . "</td>";
			echo "</tr>\n";
			$order = $nextorder[$order];
		}
		echo "</table>\n";
		finishPortlet();
	}
}

// renders a list of slb links. it is called from 3 different pages, wich compute their links lists differently.
// each triplet in $triplets array contains balancer, pool, VS cells and config values for triplet: RS, VS configs and pair.
function renderSLBTripletsEdit ($cell)
{
	list ($realm1, $realm2) = array_values (array_diff (array ('object', 'ipv4vs', 'ipv4rspool'), array ($cell['realm'])));
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		callHook ('renderNewSLBItemForm', $realm1, $realm2);

	$triplets = SLBTriplet::getTriplets ($cell);
	if (count ($triplets))
	{
		$cells = array();
		foreach ($triplets[0]->display_cells as $field)
			$cells[] = $triplets[0]->$field;

		startPortlet ('Manage existing (' . count ($triplets) . ')');
		echo "<table cellspacing=0 cellpadding=5 align=center class=cooltable>\n";
		global $nextorder;
		$order = 'odd';
		foreach ($triplets as $slb)
		{
			$cells = array();
			foreach ($slb->display_cells as $field)
				$cells[] = $slb->$field;
			$ids = array
			(
				'object_id' => $slb->lb['id'],
				'vs_id' => $slb->vs['id'],
				'pool_id' => $slb->rs['id'],
			);
			$del_params = $ids;
			$del_params['op'] = 'delLB';
			printOpFormIntro ('updLB', $ids);
			echo "<tr valign=top class=row_${order}><td rowspan=2 class=tdright valign=middle>";
			echo getOpLink ($del_params, '', 'DELETE', 'Unconfigure');
			echo "</td><td class=tdleft valign=bottom>";
			renderSLBEntityCell ($cells[0]);
			echo "</td><td>VS config &darr;<br><textarea name=vsconfig rows=5 cols=70>" . stringForTextarea ($slb->slb['vsconfig']) . "</textarea></td>";
			echo '<td class=tdleft rowspan=2 valign=middle>';
			printImageHREF ('SAVE', 'Save changes', TRUE);
			echo "</td>";
			echo "</tr><tr class=row_${order}><td class=tdleft valign=top>";
			renderSLBEntityCell ($cells[1]);
			echo '</td><td>';
			echo "<textarea name=rsconfig rows=5 cols=70>" . stringForTextarea ($slb->slb['rsconfig']) . "</textarea><br>RS config &uarr;";
			echo "<div style='float:left; margin-top:10px'><label><input name=prio type=text size=10 value=\"" . htmlspecialchars ($slb->slb['prio']) . "\"> &larr; Priority</label></div>";
			echo '</td></tr></form>';
			$order = $nextorder[$order];
		}
		echo "</table>\n";
		finishPortlet();
	}

	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		callHook ('renderNewSLBItemForm', $realm1, $realm2);
}

function renderLBList ()
{
	$cells = array();
	foreach (scanRealmByText('object', getConfigVar ('IPV4LB_LISTSRC')) as $object)
		$cells[$object['id']] = $object;
	renderCellList ('object', 'items', FALSE, $cells);
}

function renderRSPool ($pool_id)
{
	$poolInfo = spotEntity ('ipv4rspool', $pool_id);

	echo "<table border=0 class=objectview cellspacing=0 cellpadding=0>";
	if ($poolInfo['name'] != '')
		echo "<tr><td colspan=2 align=center><h1>{$poolInfo['name']}</h1></td></tr>";
	echo "<tr><td class=pcleft>\n";

	$summary = array();
	$summary['Pool name'] = $poolInfo['name'];
	$summary['Real servers'] = $poolInfo['rscount'];
	$summary['VS instances'] = $poolInfo['refcnt'];
	$summary['tags'] = '';
	$summary['VS configuration'] = '<div class="dashed slbconf">' . htmlspecialchars ($poolInfo['vsconfig']) . '</div>';
	$summary['RS configuration'] = '<div class="dashed slbconf">' . htmlspecialchars ($poolInfo['rsconfig']) . '</div>';
	renderEntitySummary ($poolInfo, 'Summary', $summary);
	callHook ('portletRSPoolSrv', $pool_id);

	echo "</td><td class=pcright>\n";
	renderSLBTriplets2 ($poolInfo);
	renderSLBTriplets ($poolInfo);
	echo "</td></tr><tr><td colspan=2>\n";
	renderFilesPortlet ('ipv4rspool', $pool_id);
	echo "</td></tr></table>\n";
}

function portletRSPoolSrv ($pool_id)
{
	$poolInfo = spotEntity ('ipv4rspool', $pool_id);
	if ($poolInfo['rscount'])
	{
		$rs_list = getRSListInPool ($poolInfo['id']);
		$rs_table = callHook ('prepareRealServersTable', $rs_list);
		startPortlet ("Real servers ({$poolInfo['rscount']})");
		echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
		echo "<tr>";
		foreach ($rs_table['columns'] as $title)
			echo "<th>$title</th>";
		echo "</tr>";
		foreach ($rs_table['rows'] as $rs)
		{
			echo "<tr valign=top>";
			foreach (array_keys ($rs_table['columns']) as $field)
			{
				switch ($field)
				{
					case 'inservice':
						echo "<td align=center>";
						if ($rs['inservice'] == 'yes')
							printImageHREF ('inservice', 'in service');
						else
							printImageHREF ('notinservice', 'NOT in service');
						break;
					case 'rsip':
						echo '<td class=tdleft>' . mkA ($rs[$field], 'ipaddress', $rs[$field]);
						break;
					case 'rsconfig':
						echo "<td class=slbconf>";
						echo $rs[$field];
						break;
					default:
						echo "<td class=tdleft>";
						echo $rs[$field];
						break;
				}
				echo '</td>';
			}
			echo '</tr>';
		}
		echo "</table>\n";
		finishPortlet();
	}
}

function prepareRealServersTable ($rs_list)
{
	$columns = array
	(
		'inservice' => '',
		'rsip' => 'address',
		'rsport' => 'port',
		'rsconfig' => 'RS config',
		'comment' => 'comment',
	);
	$not_seen = $columns;
	foreach ($rs_list as $rs)
		foreach ($rs as $key => $value)
			if (! empty ($value) && isset ($not_seen[$key]))
				unset ($not_seen[$key]);
	foreach (array_keys ($not_seen) as $key)
		if ($key != 'rsip')
			unset ($columns[$key]);
	return array
	(
		'columns' => $columns,
		'rows' => $rs_list,
	);
}

function renderEditRSList ($rs_list)
{
	global $nextorder;

	echo "<table cellspacing=0 cellpadding=5 align=center class=cooltable>\n";
	echo "<tr><th>&nbsp;</th><th>Address</th><th>Port</th><th>Comment</th><th>in service</th><th>configuration</th><th>&nbsp;</th></tr>\n";
	// new RS form
	printOpFormIntro ('addRS');
	echo "<tr class=row_odd valign=top><td>";
	printImageHREF ('add', 'Add new real server');
	echo "</td><td><input type=text name=rsip></td>";
	$default_port = emptyStrIfZero (getConfigVar ('DEFAULT_SLB_RS_PORT'));
	echo "<td><input type=text name=rsport size=5 value='$default_port'></td>";
	echo "<td><input type=text name=comment size=15></td>";
	$checked = (getConfigVar ('DEFAULT_IPV4_RS_INSERVICE') == 'yes') ? 'checked' : '';
	echo "<td><input type=checkbox name=inservice $checked></td>";
	echo "<td><textarea name=rsconfig></textarea></td><td>";
	printImageHREF ('ADD', 'Add new real server', TRUE);
	echo "</td></tr></form>\n";

	$order = 'even';
	foreach ($rs_list as $rsid => $rs)
	{
		printOpFormIntro ('updRS', array ('rs_id' => $rsid));
		echo "<tr valign=top class=row_${order}><td>";
		echo getOpLink (array('op'=>'delRS', 'id'=>$rsid), '', 'delete', 'Delete this real server');
		echo "</td><td><input type=text name=rsip value='${rs['rsip']}'></td>";
		echo "<td><input type=text name=rsport size=5 value='${rs['rsport']}'></td>";
		echo "<td><input type=text name=comment size=15 value='${rs['comment']}'></td>";
		$checked = $rs['inservice'] == 'yes' ? 'checked' : '';
		echo "<td><input type=checkbox name=inservice $checked></td>";
		echo "<td><textarea name=rsconfig>${rs['rsconfig']}</textarea></td><td>";
		printImageHREF ('SAVE', 'Save changes', TRUE);
		echo "</td></tr></form>\n";
		$order = $nextorder[$order];
	}
	echo "</table>\n";
}

function portletRSPoolAddMany ($pool_id)
{
	startPortlet ('Add many');
	printOpFormIntro ('addMany');
	echo "<table border=0 align=center>\n<tr><td>";
	if (getConfigVar ('DEFAULT_IPV4_RS_INSERVICE') == 'yes')
		printImageHREF ('inservice', 'in service');
	else
		printImageHREF ('notinservice', 'NOT in service');
	echo "</td><td>Format: ";
	$formats = callHook ('getBulkRealsFormats');
	printSelect ($formats, array ('name' => 'format'));
	echo "</td><td><input type=submit value=Parse></td></tr>\n";
	echo "<tr><td colspan=3><textarea name=rawtext cols=100 rows=25></textarea></td></tr>\n";
	echo "</table>\n";
	finishPortlet();
}

function renderRSPoolServerForm ($pool_id)
{
	$poolInfo = spotEntity ('ipv4rspool', $pool_id);
	startPortlet ("Manage RS list (${poolInfo['rscount']})");
	renderEditRSList (getRSListInPool ($pool_id));
	finishPortlet();

	portletRSPoolAddMany ($pool_id);
}

function getBulkRealsFormats()
{
	return array
	(
		'ssv_1' => 'SSV: <IP address>',
		'ssv_2' => 'SSV: <IP address> <port>',
		'ipvs_2' => 'ipvsadm -l -n (address and port)',
		'ipvs_3' => 'ipvsadm -l -n (address, port and weight)',
	);
}

function renderRSPoolList ()
{
	renderCellList ('ipv4rspool', 'RS pools');
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
		echo "<tr valign=top class=row_${order}><td>";
		$dname = $pool_list[$rsinfo['rspool_id']]['name'] != '' ? $pool_list[$rsinfo['rspool_id']]['name'] : 'ANONYMOUS';
		echo mkA ($dname, 'ipv4rspool', $rsinfo['rspool_id']);
		echo '</td><td align=center>';
		if ($rsinfo['inservice'] == 'yes')
			printImageHREF ('inservice', 'in service');
		else
			printImageHREF ('notinservice', 'NOT in service');
		echo '</td><td>' . mkA ($rsinfo['rsip'], 'ipaddress', $rsinfo['rsip']) . '</td>';
		echo "<td>${rsinfo['rsport']}</td>";
		echo "<td><pre>${rsinfo['rsconfig']}</pre></td>";
		echo "</tr>\n";
	}
	echo "</table>";
}


function renderNewRSPoolForm ()
{
	startPortlet ('Add new RS pool');
	printOpFormIntro ('add');
	echo "<table border=0 cellpadding=5 cellspacing=0 align=center>\n";
	echo "<tr><th class=tdright>Name:</th>";
	echo "<td class=tdleft><input type=text name=name></td><td>";
	echo "</td></tr><th class=tdright>Tags:</th><td class='tdleft'>";
	printTagsPicker ();
	echo "</td></tr>";
	echo "<tr><th class=tdright>VS config:</th><td colspan=2><textarea name=vsconfig rows=10 cols=80></textarea></td></tr>\n";
	echo "<tr><th class=tdright>RS config:</th><td colspan=2><textarea name=rsconfig rows=10 cols=80></textarea></td></tr>\n";
	echo "<tr><td colspan=2>";
	printImageHREF ('CREATE', 'create real server pool', TRUE);
	echo "</td></tr>";
	echo "</table></form>\n";
	finishPortlet();
}

function renderVirtualService ($vsid)
{
	$vsinfo = spotEntity ('ipv4vs', $vsid);
	echo '<table border=0 class=objectview cellspacing=0 cellpadding=0>';
	if ($vsinfo['name'] != '')
		echo "<tr><td colspan=2 align=center><h1>${vsinfo['name']}</h1></td></tr>\n";
	echo '<tr>';

	echo '<td class=pcleft>';
	$summary = array();
	$summary['Name'] = $vsinfo['name'];
	$summary['Protocol'] = $vsinfo['proto'];
	$summary['Virtual IP address'] = mkA ($vsinfo['vip'], 'ipaddress', $vsinfo['vip']);
	$summary['Virtual port'] = $vsinfo['vport'];
	$summary['tags'] = '';
	$summary['VS configuration'] = '<div class="dashed slbconf">' . $vsinfo['vsconfig'] . '</div>';
	$summary['RS configuration'] = '<div class="dashed slbconf">' . $vsinfo['rsconfig'] . '</div>';
	renderEntitySummary ($vsinfo, 'Summary', $summary);
	echo '</td>';

	echo '<td class=pcright>';
	renderSLBTriplets ($vsinfo);
	echo '</td></tr><tr><td colspan=2>';
	renderFilesPortlet ('ipv4vs', $vsid);
	echo '</tr><table>';
}

function renderVSList ()
{
	renderCellList ('ipv4vs', 'Virtual services');
}

function renderNewVSForm ()
{
	startPortlet ('Add new virtual service');
	printOpFormIntro ('add');
	$default_port = emptyStrIfZero (getConfigVar ('DEFAULT_SLB_VS_PORT'));
	global $vs_proto;
	echo "<table border=0 cellpadding=5 cellspacing=0 align=center>\n";
	echo "<tr><th class=tdright>VIP:</th><td class=tdleft><input type=text name=vip></td>";
	echo "<tr><th class=tdright>Port:</th><td class=tdleft>";
	echo "<input type=text name=vport size=5 value='${default_port}'></td></tr>";
	echo "<tr><th class=tdright>Proto:</th><td class=tdleft>";
	printSelect ($vs_proto, array ('name' => 'proto'), array_first (array_keys ($vs_proto)));
	echo "</td></tr>";
	echo "<tr><th class=tdright>Name:</th><td class=tdleft><input type=text name=name></td><td>";
	echo "<tr><th class=tdright>Tags:</th><td class=tdleft>";
	printTagsPicker ();
	echo "</td></tr>";
	echo "<tr><th class=tdrigh>VS configuration:</th><td class=tdleft><textarea name=vsconfig rows=10 cols=80></textarea></td></tr>";
	echo "<tr><th class=tdrigh>RS configuration:</th><td class=tdleft><textarea name=rsconfig rows=10 cols=80></textarea></td></tr>";
	echo "<tr><td colspan=2>";
	printImageHREF ('CREATE', 'create virtual service', TRUE);
	echo "</td></tr>";
	echo '</table></form>';
	finishPortlet();
}

function renderEditRSPool ($pool_id)
{
	$poolinfo = spotEntity ('ipv4rspool', $pool_id);
	printOpFormIntro ('updIPv4RSP');
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Name:</th><td class=tdleft><input type=text name=name value='${poolinfo['name']}'></td></tr>\n";
	echo "<tr><th class=tdright>Tags:</th><td class=tdleft>";
	printTagsPicker ();
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>VS config:</th><td class=tdleft><textarea name=vsconfig rows=20 cols=80>${poolinfo['vsconfig']}</textarea></td></tr>\n";
	echo "<tr><th class=tdright>RS config:</th><td class=tdleft><textarea name=rsconfig rows=20 cols=80>${poolinfo['rsconfig']}</textarea></td></tr>\n";
	echo "<tr><th class=submit colspan=2>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>\n";
	echo "</table></form>\n";

	// clone link
	echo '<p class="centered">';
	echo getOpLink (array ('op' => 'cloneIPv4RSP'), 'Clone RS pool', 'copy');
	echo '</p>';

	// delete link
	echo '<p class="centered">';
	if ($poolinfo['refcnt'] > 0)
		echo getOpLink (NULL, 'Delete RS pool', 'nodestroy', "Could not delete: there are ${poolinfo['refcnt']} LB link(s)");
	else
		echo getOpLink (array ('op' => 'del'), 'Delete RS pool', 'destroy');
	echo '</p>';
}

function renderEditVService ($vsid)
{
	$vsinfo = spotEntity ('ipv4vs', $vsid);
	printOpFormIntro ('updIPv4VS');
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>VIP:</th><td class=tdleft><input type=text name=vip value='${vsinfo['vip']}'></td></tr>\n";
	echo "<tr><th class=tdright>Port:</th><td class=tdleft><input type=text name=vport value='${vsinfo['vport']}'></td></tr>\n";
	echo "<tr><th class=tdright>Proto:</th><td class=tdleft>";
	global $vs_proto;
	printSelect ($vs_proto, array ('name' => 'proto'), $vsinfo['proto']);
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>Name:</th><td class=tdleft><input type=text name=name value='${vsinfo['name']}'></td></tr>\n";
	echo "<tr><th class=tdright>Tags:</th><td class=tdleft>";
	printTagsPicker ();
	echo "</td></tr>\n";
	echo "<tr><th class=tdright>VS config:</th><td class=tdleft><textarea name=vsconfig rows=20 cols=80>${vsinfo['vsconfig']}</textarea></td></tr>\n";
	echo "<tr><th class=tdright>RS config:</th><td class=tdleft><textarea name=rsconfig rows=20 cols=80>${vsinfo['rsconfig']}</textarea></td></tr>\n";
	echo "<tr><th class=submit colspan=2>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>\n";
	echo "</table></form>\n";

	// delete link
	echo '<p class="centered">';
	if ($vsinfo['refcnt'] > 0)
		echo getOpLink (NULL, 'Delete virtual service', 'nodestroy', "Could not delete: there are ${vsinfo['refcnt']} LB link(s)");
	else
		echo getOpLink (array ('op' => 'del'), 'Delete virtual service', 'destroy');
}

function renderLVSConfig ($object_id)
{
	printOpFormIntro ('submitSLBConfig');
	echo "<center><input type=submit value='Submit for activation'></center>";
	echo "</form>";
	echo "<pre>" . buildLVSConfig ($object_id) . "</pre>";
}
