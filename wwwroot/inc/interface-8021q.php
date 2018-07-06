<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

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
				$focus[$hint_code] = genericAssertion ($hint_code, 'natural');
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
		getConfigVar ('ADDNEW_AT_TOP') == 'yes' &&
		($pageno != 'object' || ! count ($minuslines))
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
			echo '<td>' . mkCellA ($object) . '</td>';
		}
		if ($pageno != 'vlandomain')
			echo '<td>' . mkA (stringForTD ($vdomlist[$item['vdom_id']], 64), 'vlandomain', $item['vdom_id']) . '</td>';
		if ($pageno != 'vst')
			echo '<td>' . mkA ($vstlist[$item['vst_id']], 'vst', $item['vst_id']) . '</td>';
		echo "<td>${cutblock}</td></tr>";
	}
	if
	(
		getConfigVar ('ADDNEW_AT_TOP') != 'yes' &&
		($pageno != 'object' || ! count ($minuslines))
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
		if ($dominfo['switchc'] || $dominfo['vlanc'] > 1)
			printImageHREF ('nodestroy', 'domain used elsewhere');
		else
			echo getOpLink (array ('op' => 'del', 'vdom_id' => $vdom_id), '', 'destroy', 'delete domain');
		echo '</td><td><input name=vdom_descr type=text size=48 value="';
		echo stringForTextInputValue ($dominfo['description'], 255) . '">';
		echo '</td><td>';
		if ($dominfo['subdomc'])
			printSelect (array (0 => 'a domain group'), array ('name' => 'group_id'));
		else
			printNiftySelect ($group_opts, array ('name' => 'group_id'), $dominfo['group_id']);
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
		if ($vlan_info['portc'] || $vlan_id == VLAN_DFL_ID)
			printImageHREF ('nodestroy', $vlan_info['portc'] . ' port(s) configured');
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
		genericAssertion ('hl_port_id', 'natural');
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
		if ($port['name'] != '' && array_key_exists ($port['name'], $desired_config))
		{
			if (isset ($hl_port_id) && $hl_port_id == $port['id'])
				$hl_port_name = $port['name'];
			$socket = array ('interface' => formatPortIIFOIF ($port), 'link' => '&nbsp;');
			if ($port['remote_object_id'])
				$socket['link'] = formatLoggedSpan ($port['last_log'], formatLinkedPort ($port));
			elseif ($port['reservation_comment'] != '')
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
		if (isset ($hl_port_name) && $hl_port_name == $port_name)
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
	if ($req_port_name == '' && $nports)
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
		array_key_exists ($port['native'], $vdom['vlanlist']) &&
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
	$ret = "<input type=hidden name=pn_${nports} value='${port_name}'>";
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
			($vlan_id != $from || $port['mode'] == 'trunk') &&
			$vlan_info['vlan_type'] != 'alien' &&
			in_array ($vlan_id, $vlanpermissions[$from]) &&
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
				$native_options[$vlanport['native']]['vlan_type'] == 'alien' ||
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
	if ($vlan['vlan_descr'] != '')
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
				$counterpart_vlan['domain_id'] == $vlan['domain_group_id'] ||
				in_array($counterpart_vlan['domain_id'], $group_members)
			)
			{
				$group_ck_list[$counterpart_ck] = $counterpart_vlan;
				$group_counterparts[] = $counterpart_link;
			}
			elseif ($vlan['domain_group_id'] && $counterpart_vlan['domain_group_id'] == $vlan['domain_group_id'])
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
		// Find configured port on master's members
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
	if (count ($foreign_devices))
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
			if (considerConfiguredConstraint ($netinfo, 'VLANNET_LISTSRC'))
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
				$port['status'] == 'delete_conflict' ||
				$port['status'] == 'merge_conflict' ||
				$port['status'] == 'add_conflict' ||
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
	if (! considerConfiguredConstraint ($object, 'SYNC_8021Q_LISTSRC'))
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
		genericAssertion ('hl_port_id', 'natural');
		$hl_port_id = intval ($_REQUEST['hl_port_id']);
		$hl_port_name = NULL;
		addAutoScrollScript ("port-$hl_port_id");

		foreach ($object['ports'] as $port)
			if ($port['name'] != '' && $port['id'] == $hl_port_id)
			{
				$hl_port_name = $port['name'];
				break;
			}
		unset ($object);
	}

	switchportInfoJS ($vswitch['object_id']); // load JS code to make portnames interactive
	// Initialize one of the three popups: the data is ready.
	$port_config = addslashes (json_encode (formatPortConfigHints ($vswitch['object_id'], $R)));
	addJS (<<<'END'
$(document).ready(function(){
	var confData = $.parseJSON('$port_config');
	applyConfData(confData);
	var menuItem = $('.context-menu-item.itemname-conf');
	menuItem.addClass($.contextMenu.disabledItemClassName);
	setItemIcon(menuItem[0], 'ok');
});

function checkColumnOfRadios8021Q (prefix, numRows, suffix)
{
	var elemId;
	for (var i=0; i < numRows; i++)
	{
		elemId = prefix + i + suffix;
		if (document.getElementById(elemId) == null) // no radios on this row
			continue;
		// Not all radios are present on each form. Hence each time
		// the user wants to flip all rows from left to right (or vice versa)
		// it is better to half-complete the request by setting to the
		// middle position rather than to fail completely due to missing
		// target input.
		if (document.getElementById(elemId).disabled == true)
			switch (suffix)
			{
			case '_asis':
				continue;
			case '_left':
			case '_right':
				elemId = prefix + i + '_asis';
			}
		document.getElementById(elemId).checked = true;
	}
}
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
			// don't offer anything that VST will deny.
			// Consider domain and template constraints.
			$radio_attrs = array ('left' => '', 'asis' => ' checked', 'right' => '');
			if
			(
				! acceptable8021QConfig ($item['right']) ||
				count (array_diff ($item['right']['allowed'], $domvlans)) ||
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
		if (isset ($hl_port_name) && $hl_port_name == $port_name)
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
		$allethports[$port['name']] = array ('iifoif' => formatPortIIFOIF ($port));
	$enabled = array();
	# OPTIONSs for existing 802.1Q ports
	foreach (sortPortList ($D) as $portname => $portconfig)
		$enabled["disable ${portname}"] = "${portname} ("
			. (array_key_exists ($portname, $allethports) ? $allethports[$portname]['iifoif']: 'N/A')
			. ') ' . serializeVLANPack ($portconfig);
	# OPTIONs for potential 802.1Q ports
	$disabled = array();
	foreach (sortPortList ($allethports) as $portname => $each)
		if (! array_key_exists ("disable ${portname}", $enabled))
			$disabled["enable ${portname}"] = "${portname} (${each['iifoif']})";
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
	echo '<center>' . getImageHREF ('SAVE', 'Save template', TRUE) . '</center>';
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
			if (count ($added))
				$ret .= '<span class="vlan-diff diff-add">+ ' . implode (', ', $added) . '</span><br>';
			if (count ($removed))
				$ret .= '<span class="vlan-diff diff-rem">- ' . implode (', ', $removed) . '</span><br>';
		}
	}
	$ret .= $new_pack;
	return $ret;
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
		$clear_line .= getOpLink (array ('op' => 'clear'), 'remove', 'clear', "remove this VLAN from $portc port(s)") .
			' this VLAN from ' . mkA ("${portc} port(s)", 'vlan', $vlan_ck);
	}

	if ($vlan['vlan_id'] == VLAN_DFL_ID)
		echo getOpLink (NULL, 'delete VLAN', 'nodestroy', 'You can not delete default VLAN');
	else
		echo getOpLink (array ('op' => 'del', 'vlan_ck' => $vlan_ck), 'delete VLAN', 'destroy', '', 'need-confirmation');
	echo $clear_line;

	finishPortlet();
}
