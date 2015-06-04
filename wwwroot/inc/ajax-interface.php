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
// returns array which could be packed into json and passed to client's browser
function formatPortLinkHints ($object_id)
{
	$result = array();
	$linkStatus = queryDevice ($object_id, 'getportstatus');
	foreach ($linkStatus as $portname => $link_info)
	{
		$link_info = $linkStatus[$portname];
		switch ($link_info['status'])
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
		}

		$hidden_lines = array();
		$hidden_lines[] = $portname . ': ' . $link_info['status'];
		if (isset ($link_info['speed']))
			$hidden_lines[] = 'Speed: ' . $link_info['speed'];
		if (isset ($link_info['duplex']))
			$hidden_lines[] = 'Duplex: ' . $link_info['duplex'];
		if (count ($hidden_lines))
			$result[$portname]['popup'] = implode ('<br>', $hidden_lines);
		$visible_part = "<img width=16 height=16 src='?module=chrome&uri=pix/${img_filename}'>";
		$result[$portname]['inline'] = $visible_part;
	}
	// put empty pictures for not-found ports
	$object = spotEntity ('object', $object_id);
	amplifyCell ($object);
	foreach ($object['ports'] as $port)
		if (! isset ($result[$port['name']]))
			$result[$port['name']]['inline'] = "<img width=16 height=16 src='?module=chrome&uri=pix/1x1t.gif'>";
	return $result;
}

// retrieves MAC address list from switch and formats results to dynamic-HTML
// returns array which could be packed into json and passed to client's browser
function formatPortMacHints ($object_id)
{
	$result = array();
	if ($_REQUEST['ac'] == 'get-port-portmac')
	{
		$port_name = $_REQUEST['port_name'];
		$ports = reduceSubarraysToColumn (getObjectPortsAndLinks($_REQUEST['object_id']), 'name');
		$macList = in_array($port_name, $ports) ?
				queryDevice ($object_id, 'getportmaclist', array ($port_name)) :
				array();
	}
	else
		$macList = queryDevice ($object_id, 'getmaclist');
	foreach ($macList as $portname => $list)
	{
		$list = $macList[$portname];
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
// returns array which could be packed into json and passed to client's browser
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
		(strlen ($html_class) ? " class='$html_class'" : '') .
		(strlen ($title) ? " title='$title'" : '') .
		">$text</span>";
}

function getTagSelectAJAX()
{
	global $taglist;
	$options = array();
	$selected_id = '';
	if (! isset($_REQUEST['tagid']))
		$options['error'] = "Sorry, param 'tagid' is empty. Reload page and try again";
	elseif (! preg_match("/tagid_(\d+)/i", $_REQUEST['tagid'], $m))
		$options['error'] = "Sorry, wrong format tagid:'".$_REQUEST['tagid']."'. Reload page and try again";
	else
	{
		$current_tag_id = $m[1];
		$selected_id = $taglist[$current_tag_id]['parent_id'];
		echo $selected_id;
		$options[0] = '-- NONE --';
		foreach ($taglist as $tag_id => $taginfo)
			if (! in_array ($current_tag_id, $taginfo['trace']) && $current_tag_id != $tag_id)
				$options[$tag_id] = $taginfo['tag'];
	}
	foreach ($options as $tag_id => $value)
		echo "<option value='$tag_id'" .
			($tag_id == $selected_id ? ' selected' : '') .
			'>' . htmlspecialchars ($value) . '</option>';
}

function getLocationSelectAJAX()
{
	$locationlist = listCells ('location');
	$locationtree = treeFromList ($locationlist); // adds ['trace'] keys into $locationlist items
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
		echo $selected_id;
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
			printLocationChildrenSelectOptions ($location, 0, $selected_id, $current_location_id);
	}
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
	genericAssertion ('object_id', 'uint');
	fixContext (spotEntity ('object', $_REQUEST['object_id']));
	assertPermission ('object', 'liveports', $opmap[$_REQUEST['ac']]);
	echo json_encode ($funcmap[$_REQUEST['ac']] ($_REQUEST['object_id']));
}

function updatePortRsvAJAX()
{
	global $sic;
	assertUIntArg ('id');
	assertStringArg ('text', TRUE);
	$port_info = getPortInfo ($sic['id']);
	fixContext (spotEntity ('object', $port_info['object_id']));
	assertPermission ('object', 'ports', 'editPort');
	if ($port_info['linked'])
		throw new RackTablesError ('Cant update port comment: port is already linked');
	if (! isset ($port_info['reservation_comment']))
		$port_info['reservation_comment'] = '';
	if ($port_info['reservation_comment'] !== $sic['text'])
		commitUpdatePortComment ($sic['id'], $sic['text']);
	echo 'OK';
}

function updateIPNameAJAX()
{
	global $sic;
	assertStringArg ('text', TRUE);
	$ip_bin = assertIPArg ('id');
	$addr = getIPAddress ($ip_bin);
	if (! empty ($addr['allocs']) && empty ($addr['name']))
		throw new RackTablesError ('Cant update IP name: address is allocated');
	$net = spotNetworkByIP ($ip_bin);
	if (isset ($net))
		fixContext ($net);
	assertPermission ('ipaddress', 'properties', 'editAddress');
	$reserved = (empty ($sic['text']) ? 'no' : $addr['reserved']); // unset reservation if user clears name
	$comment = (empty ($addr['comment']) ? '' : $addr['comment']);
	updateAddress ($ip_bin, $sic['text'], $reserved, $comment);
	echo 'OK';
}

function updateIPCommentAJAX()
{
	global $sic;
	assertStringArg ('text', TRUE);
	$ip_bin = assertIPArg ('id');
	$addr = getIPAddress ($ip_bin);
	$net = spotNetworkByIP ($ip_bin);
	if (isset ($net))
		fixContext ($net);
	assertPermission ('ipaddress', 'properties', 'editAddress');
	updateAddress ($ip_bin, $addr['name'], $addr['reserved'], $sic['text']);
	echo 'OK';
}

function updateCableIdAJAX()
{
	global $sic;
	assertUIntArg ('id');
	assertStringArg ('text', TRUE);
	$port_info = getPortInfo ($sic['id']);
	fixContext (spotEntity ('object', $port_info['object_id']));
	assertPermission ('object', 'ports', 'editPort');
	if (! $port_info['linked'])
		throw new RackTablesError ('Cant update cable ID: port is not linked');
	if ($port_info['reservation_comment'] !== $sic['text'])
		commitUpdatePortLink ($sic['id'], $sic['text']);
	echo 'OK';
}

function updateRackSortOrderAJAX()
{
	updateRackSortOrder ($_REQUEST['racks']);
	echo 'OK';
}

function getNetUsageAJAX()
{
	assertStringArg ('net_id');
	list ($ip, $mask) = explode ('/', $_REQUEST['net_id']);
	$ip_bin = ip_parse ($ip);
	$net = spotNetworkByIP ($ip_bin, $mask + 1);
	if (! isset ($net) or $net['mask'] != $mask)
		$net = constructIPRange ($ip_bin, $mask);
	loadIPAddrList ($net);
	echo getRenderedIPNetCapacity ($net);
}

?>
