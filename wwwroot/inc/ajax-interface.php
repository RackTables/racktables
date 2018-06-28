<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*
 *
 *  This file contains HTML-generating functions required by AJAX handlers.
 *
 *
*/

// retrieves ports link status from switch and formats results to dynamic-HTML
// Returns an array to be encoded in JSON and passed to client's browser.
function formatPortLinkHints ($object_id)
{
	$result = array();
	$linkStatus = queryDevice ($object_id, 'getportstatus');
	$statusmap = array
	(
		'up' => 'link up',
		'down' => 'link down',
		'disabled' => 'link disabled',
	);
	foreach ($linkStatus as $portname => $link_info)
	{
		$hidden_lines = array();
		$hidden_lines[] = $portname . ': ' . $link_info['status'];
		if (isset ($link_info['speed']))
			$hidden_lines[] = 'Speed: ' . $link_info['speed'];
		if (isset ($link_info['duplex']))
			$hidden_lines[] = 'Duplex: ' . $link_info['duplex'];
		if (count ($hidden_lines))
			$result[$portname]['popup'] = implode ('<br>', $hidden_lines);
		$visible_part = getImageHREF (array_fetch ($statusmap, $link_info['status'], '16x16t'));
		$result[$portname]['inline'] = $visible_part;
	}
	// put empty pictures for not-found ports
	$object = spotEntity ('object', $object_id);
	amplifyCell ($object);
	foreach ($object['ports'] as $port)
		if (! isset ($result[$port['name']]))
			$result[$port['name']]['inline'] = getImageHREF ('16x16t');
	return $result;
}

// retrieves MAC address list from switch and formats results to dynamic-HTML
// Returns an array to be encoded in JSON and passed to client's browser.
function formatPortMacHints ($object_id)
{
	$result = array();
	if ($_REQUEST['ac'] == 'get-port-portmac')
	{
		$port_name = $_REQUEST['port_name'];
		$ports = reduceSubarraysToColumn (getObjectPortsAndLinks ($_REQUEST['object_id'], FALSE), 'name');
		$macList = in_array($port_name, $ports) ?
				queryDevice ($object_id, 'getportmaclist', array ($port_name)) :
				array();
	}
	else
		$macList = queryDevice ($object_id, 'getmaclist');
	foreach ($macList as $portname => $list)
	{
		$visible_part = count ($list) . ' MACs';
		$result[$portname]['inline'] = $visible_part;
		if (count ($list))
		{
			$hidden_part = '<table width="100%"><tr><th>MAC<th>VID</tr>';
			foreach ($list as $mac)
				$hidden_part .= '<tr><td>' . $mac['mac'] . '<td>' . $mac['vid'] . '</tr>';
			$result[$portname]['popup'] = $hidden_part;
		}
	}
	return $result;
}

// retrieves port configs from switch and formats results to dynamic-HTML
// Returns an array to be encoded in JSON and passed to client's browser.
function formatPortConfigHints ($object_id, $R = NULL)
{
	$result = array();
	if (! isset ($R))
		$R = getRunning8021QConfig ($object_id);
	foreach ($R['portconfig'] as $portname => $portconfig)
	{
		$hidden_part = '';
		foreach ($portconfig as $line)
			$hidden_part .= '<span class="'. $line['type'] . '">' . htmlentities ($line['line']) . '</span><br />';
		$result[$portname]['popup'] = $hidden_part;
	}
	return $result;
}

// returns html-formatted span tag with last changed in title
// takes 3 args:
//  log_item - array with keys 'user', 'time'. Could be empty
//  text - the text placed into the span
//  html_class - the additional css class
function formatLoggedSpan ($log_item, $text, $html_class = '')
{
	$title = '';
	if (! empty ($log_item))
	{
		$html_class = trim ($html_class . ' hover-history');
		$title = htmlspecialchars ($log_item['user'] . ', ' . formatAge ($log_item['time']), ENT_QUOTES);
	}
	return "<span" .
		($html_class != '' ? " class='$html_class'" : '') .
		($title != '' ? " title='$title'" : '') .
		">$text</span>";
}

function getLocationSelectAJAX()
{
	global $pageno, $tabno;
	$pageno = 'rackspace';
	$tabno = 'default';
	fixContext();
	assertPermission();
	$locationlist = listCells ('location');
	$locationtree = treeFromList (addTraceToNodes ($locationlist));
	$options = array ();
	$selected_id = '';
	if (! isset($_REQUEST['locationid']))
		$options['error'] = "Sorry, param 'locationid' is empty. Reload page and try again";
	elseif (! preg_match("/locationid_(\d+)/i", $_REQUEST['locationid'], $m))
		$options['error'] = "Sorry, wrong format locationid:'".$_REQUEST['locationid']."'. Reload page and try again";
	else
	{
		$current_location_id = $m[1];
		$selected_id = $locationlist[$current_location_id]['parent_id'];
		echo "<option value=0>-- NONE --</option>";
	}
	foreach ($locationtree as $location)
	{
		if ($location['id'] == $current_location_id)
			continue;
		echo "<option value=${location['id']} ";
		if ($location['id'] == $selected_id)
			echo 'selected ';
		echo "style='font-weight: bold'>${location['name']}</option>";
		if ($location['kidc'] > 0)
			printLocationChildrenSelectOptions ($location, $selected_id, $current_location_id);
	}
}

function getParentNodeOptionsAJAX()
{
	global $pageno, $tabno;
	$selected_id = NULL;
	try
	{
		$tmp = genericAssertion ('node_id', 'string');
		if (! preg_match ('/^nodeid_(\d+)$/', $tmp, $m))
			throw new InvalidRequestArgException ('node_id', $tmp, 'format mismatch');
		$node_id = $m[1];
		switch ($node_type = genericAssertion ('node_type', 'string'))
		{
			case 'existing tag':
				$pageno = 'tagtree';
				$tabno = 'default';
				fixContext();
				assertPermission();
				global $taglist;
				$selected_id = $taglist[$node_id]['parent_id'];
				$options = getParentNodeOptionsExisting ($taglist, 'tag', $node_id);
				break;
			default:
				throw new InvalidRequestArgException ('node_type', $node_type, 'unknown type');
		}
	}
	catch (Exception $e)
	{
		$options = array ('error' => $e->getMessage());
	}
	echo getSelectOptions ($options, $selected_id);
}

function verifyCodeAJAX()
{
	global $pageno, $tabno;
	$pageno = 'perms';
	$tabno = 'edit';
	fixContext();
	assertPermission();
	genericAssertion ('code', 'string');
	$result = getRackCode (dos2unix ($_REQUEST['code']));
	if ($result['result'] == 'ACK')
		echo "ACK\n";
	else
		echo "NAK\n" . $result['load'];
}

// echoes JSON-encoded text
function getPortInfoAJAX()
{
	$funcmap = array
	(
		'get-port-link' => 'formatPortLinkHints',
		'get-port-mac'  => 'formatPortMacHints',
		'get-port-portmac' => 'formatPortMacHints',
		'get-port-conf' => 'formatPortConfigHints',
	);
	$opmap = array
	(
		'get-port-link' => 'get_link_status',
		'get-port-mac'  => 'get_mac_list',
		'get-port-portmac' => 'get_port_mac_list',
		'get-port-conf' => 'get_port_conf',
	);
	$object_id = genericAssertion ('object_id', 'natural');
	fixContext (spotEntity ('object', $object_id));
	assertPermission ('object', 'liveports', $opmap[$_REQUEST['ac']]);
	echo json_encode ($funcmap[$_REQUEST['ac']] ($object_id));
}

function updatePortRsvAJAX()
{
	$text = genericAssertion ('text', 'string0');
	$port_info = getPortInfo (genericAssertion ('id', 'natural'));
	fixContext (spotEntity ('object', $port_info['object_id']));
	assertPermission ('object', 'ports', 'editPort');
	if ($port_info['linked'])
		throw new RackTablesError ('Can\'t update port comment: port is already linked');
	if (! isset ($port_info['reservation_comment']))
		$port_info['reservation_comment'] = '';
	if ($port_info['reservation_comment'] !== $text)
		commitUpdatePortComment ($port_info['id'], $text);
	echo 'OK';
}

function updateIPNameAJAX()
{
	$text = genericAssertion ('text', 'string0');
	$ip_bin = assertIPArg ('id');
	$addr = getIPAddress ($ip_bin);
	if (! empty ($addr['allocs']) && empty ($addr['name']))
		throw new RackTablesError ('Can\'t update IP name: address is allocated');
	$net = spotNetworkByIP ($ip_bin);
	if (isset ($net))
		fixContext ($net);
	assertPermission ('ipaddress', 'properties', 'editAddress');
	$reserved = ($text == '' ? 'no' : $addr['reserved']); // unset reservation if user clears name
	$comment = (empty ($addr['comment']) ? '' : $addr['comment']);
	updateAddress ($ip_bin, $text, $reserved, $comment);
	echo 'OK';
}

function updateIPCommentAJAX()
{
	$text = genericAssertion ('text', 'string0');
	$ip_bin = assertIPArg ('id');
	$addr = getIPAddress ($ip_bin);
	$net = spotNetworkByIP ($ip_bin);
	if (isset ($net))
		fixContext ($net);
	assertPermission ('ipaddress', 'properties', 'editAddress');
	updateAddress ($ip_bin, $addr['name'], $addr['reserved'], $text);
	echo 'OK';
}

function updateCableIdAJAX()
{
	$text = genericAssertion ('text', 'string0');
	$port_info = getPortInfo (genericAssertion ('id', 'natural'));
	fixContext (spotEntity ('object', $port_info['object_id']));
	assertPermission ('object', 'ports', 'editPort');
	if (! $port_info['linked'])
		throw new RackTablesError ('Can\'t update cable ID: port is not linked');
	if ($port_info['reservation_comment'] !== $text)
		commitUpdatePortLink ($port_info['id'], $text);
	echo 'OK';
}

function updateRackSortOrderAJAX()
{
	global $pageno, $tabno;
	$pageno = 'row';
	$tabno = 'editracks';
	fixContext();
	assertPermission (NULL, NULL, 'save'); // FIXME: operation code not in navigation.php
	updateRackSortOrder ($_REQUEST['racks']);
	echo 'OK';
}

function getNetUsageAJAX()
{
	assertStringArg ('net_id');
	list ($ip, $mask) = explode ('/', $_REQUEST['net_id']);
	$ip_bin = ip_parse ($ip);
	$net = spotNetworkByIP ($ip_bin, $mask + 1);
	if (! isset ($net) || $net['mask'] != $mask)
		$net = constructIPRange ($ip_bin, $mask);
	loadIPAddrList ($net);
	echo getRenderedIPNetCapacity ($net);
}


function getAutocompleteListAJAX()
{
	$term = genericAssertion ('term', 'string0');
	$realm = genericAssertion ('realm', 'string');

	if (! $term)
		return;

	switch ($realm)
	{
		case 'object':
			$result = usePreparedSelectBlade ("SELECT name FROM Object WHERE name LIKE ? GROUP BY name ORDER BY name LIMIT 101", array ("%$term%"));
			$rows = $result->fetchAll (PDO::FETCH_COLUMN, 0);
			unset ($result);
			break;
		case 'asset':
			$result = usePreparedSelectBlade ("SELECT asset_no FROM Object WHERE asset_no LIKE ? GROUP BY asset_no ORDER BY asset_no LIMIT 101", array ("%$term%"));
			$rows = $result->fetchAll (PDO::FETCH_COLUMN, 0);
			unset ($result);
			break;
		case 'port':
			$result = usePreparedSelectBlade ("SELECT name FROM Port WHERE name LIKE ? GROUP BY name ORDER BY name LIMIT 101", array ("%$term%"));
			$rows = $result->fetchAll (PDO::FETCH_COLUMN, 0);
			unset ($result);
			break;
		case 'bond_name':
			$object_id = genericAssertion ('object_id', 'natural');
			$result = usePreparedSelectBlade ("SELECT name FROM Port WHERE object_id = ? AND name LIKE ? GROUP BY name ORDER BY name LIMIT 101", array ($object_id, "%$term%"));
			$rows = $result->fetchAll (PDO::FETCH_COLUMN, 0);
			unset ($result);
			break;
		default:
			return;
	}

	if (count ($rows) > 100 )
		$rows[] = array ('label' => '...', 'value' => '');

	echo json_encode ($rows);
}
