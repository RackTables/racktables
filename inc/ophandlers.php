<?php
/*
*
*  This file is a library of operation handlers for RackTables.
*
*/

// This array is deprecated. Please do not add new message constants to it.
// use the new showError, showWarning, showSuccess functions instead
global $msgcode;
$msgcode = array();

global $opspec_list;
$opspec_list = array();

$opspec_list['rackspace-edit-addRow'] = array
(
	'table' => 'RackRow',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'name', 'assertion' => 'string')
	),
);
$opspec_list['rackspace-edit-delete'] = array
(
	'table' => 'RackRow',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'row_id', 'table_colname' => 'id', 'assertion' => 'uint')
	),
);
$opspec_list['rackspace-edit-updateRow'] = array
(
	'table' => 'RackRow',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'name', 'assertion' => 'string')
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'row_id', 'table_colname' => 'id', 'assertion' => 'uint')
	),
);
$opspec_list['object-ports-delPort'] = array
(
	'table' => 'Port',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'port_id', 'table_colname' => 'id', 'assertion' => 'uint'),
		array ('url_argname' => 'object_id', 'assertion' => 'uint'),
	),
);
$opspec_list['object-ports-unlinkPort'] = array
(
	'table' => 'Link',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'port_id', 'table_colname' => 'porta', 'assertion' => 'uint'),
		array ('url_argname' => 'port_id', 'table_colname' => 'portb', 'assertion' => 'uint'),
	),
	'conjunction' => 'OR',
);
$opspec_list['object-log-del'] = array
(
	'table' => 'ObjectLog',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'logid', 'table_colname' => 'id', 'assertion' => 'uint'),
		array ('url_argname' => 'object_id', 'assertion' => 'uint'),
	),
);
$opspec_list['ipv4vs-editlblist-delLB'] =
$opspec_list['ipv4rspool-editlblist-delLB'] =
$opspec_list['object-editrspvs-delLB'] = array
(
	'table' => 'IPv4LB',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'object_id', 'assertion' => 'uint'),
		array ('url_argname' => 'pool_id', 'table_colname' => 'rspool_id', 'assertion' => 'uint'),
		array ('url_argname' => 'vs_id', 'assertion' => 'uint'),
	),
);
$opspec_list['ipv4vs-editlblist-updLB'] =
$opspec_list['ipv4rspool-editlblist-updLB'] =
$opspec_list['object-editrspvs-updLB'] = array
(
	'table' => 'IPv4LB',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'vsconfig', 'assertion' => 'string0', 'if_empty' => 'NULL'),
		array ('url_argname' => 'rsconfig', 'assertion' => 'string0', 'if_empty' => 'NULL'),
		array ('url_argname' => 'prio', 'assertion' => 'uint0', 'if_empty' => 'NULL'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'object_id', 'assertion' => 'uint'),
		array ('url_argname' => 'pool_id', 'table_colname' => 'rspool_id', 'assertion' => 'uint'),
		array ('url_argname' => 'vs_id', 'assertion' => 'uint'),
	),
);
$opspec_list['ipv4net-properties-editRange'] = array
(
	'table' => 'IPv4Network',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'name', 'assertion' => 'string0'),
		array ('url_argname' => 'comment', 'assertion' => 'string0'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint')
	),
);
$opspec_list['ipv6net-properties-editRange'] = array
(
	'table' => 'IPv6Network',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'name', 'assertion' => 'string0'),
		array ('url_argname' => 'comment', 'assertion' => 'string0'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint')
	),
);
$opspec_list['ipv4rspool-editrslist-delRS'] = array
(
	'table' => 'IPv4RS',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['ipv4rspool-edit-updIPv4RSP'] = array
(
	'table' => 'IPv4RSPool',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'name', 'assertion' => 'string0', 'if_empty' => 'NULL'),
		array ('url_argname' => 'vsconfig', 'assertion' => 'string0', 'if_empty' => 'NULL'),
		array ('url_argname' => 'rsconfig', 'assertion' => 'string0', 'if_empty' => 'NULL'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'pool_id', 'table_colname' => 'id', 'assertion' => 'uint')
	),
);
$opspec_list['file-edit-updateFile'] = array
(
	'table' => 'File',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'file_name', 'table_colname' => 'name', 'assertion' => 'string'),
		array ('url_argname' => 'file_type', 'table_colname' => 'type', 'assertion' => 'string'),
		array ('url_argname' => 'file_comment', 'table_colname' => 'comment', 'assertion' => 'string0', 'if_empty' => 'NULL'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'file_id', 'table_colname' => 'id', 'assertion' => 'uint')
	),
);
$opspec_list['portmap-edit-add'] = array
(
	'table' => 'PortCompat',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'type1', 'assertion' => 'uint'),
		array ('url_argname' => 'type2', 'assertion' => 'uint'),
	),
);
$opspec_list['portmap-edit-del'] = array
(
	'table' => 'PortCompat',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'type1', 'assertion' => 'uint'),
		array ('url_argname' => 'type2', 'assertion' => 'uint'),
	),
);
$opspec_list['portifcompat-edit-del'] = array
(
	'table' => 'PortInterfaceCompat',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'iif_id', 'assertion' => 'uint'),
		array ('url_argname' => 'oif_id', 'assertion' => 'uint'),
	),
);
$opspec_list['attrs-editmap-del'] = array
(
	'table' => 'AttributeMap',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'attr_id', 'assertion' => 'uint'),
		array ('url_argname' => 'objtype_id', 'assertion' => 'uint'),
	),
);
$opspec_list['attrs-editattrs-add'] = array
(
	'table' => 'Attribute',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'attr_type', 'table_colname' => 'type', 'assertion' => 'enum/attr_type'),
		array ('url_argname' => 'attr_name', 'table_colname' => 'name', 'assertion' => 'string'),
	),
);
$opspec_list['attrs-editattrs-del'] = array
(
	'table' => 'Attribute',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'attr_id', 'table_colname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['attrs-editattrs-upd'] = array
(
	'table' => 'Attribute',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'attr_name', 'table_colname' => 'name', 'assertion' => 'string'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'attr_id', 'table_colname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['dict-chapters-add'] = array
(
	'table' => 'Chapter',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'chapter_name', 'table_colname' => 'name', 'assertion' => 'string')
	),
);
$opspec_list['chapter-edit-add'] = array
(
	'table' => 'Dictionary',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'chapter_no', 'table_colname' => 'chapter_id', 'assertion' => 'uint'),
		array ('url_argname' => 'dict_value', 'assertion' => 'string'),
	),
);
$opspec_list['chapter-edit-del'] = array
(
	'table' => 'Dictionary',
	'action' => 'DELETE',
	'arglist' => array
	(
		// Technically dict_key is enough to delete, but including chapter_id into
		// WHERE clause makes sure, that the action actually happends for the same
		// chapter, which authorization was granted for.
		array ('url_argname' => 'chapter_no', 'table_colname' => 'chapter_id', 'assertion' => 'uint'),
		array ('url_argname' => 'dict_key', 'assertion' => 'uint'),
	),
);
$opspec_list['tagtree-edit-createTag'] = array
(
	'table' => 'TagTree',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'tag_name', 'table_colname' => 'tag', 'assertion' => 'tag'),
		array ('url_argname' => 'parent_id', 'assertion' => 'uint0', 'if_empty' => 'NULL'),
	),
);
$opspec_list['tagtree-edit-destroyTag'] = array
(
	'table' => 'TagTree',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'tag_id', 'table_colname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['tagtree-edit-updateTag'] = array
(
	'table' => 'TagTree',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'tag_name', 'table_colname' => 'tag', 'assertion' => 'tag'),
		array ('url_argname' => 'parent_id', 'assertion' => 'uint0', 'if_empty' => 'NULL'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'tag_id', 'table_colname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['8021q-vstlist-upd'] = array
(
	'table' => 'VLANSwitchTemplate',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'vst_maxvlans', 'table_colname' => 'max_local_vlans', 'assertion' => 'uint0', 'if_empty' => 'NULL'),
		array ('url_argname' => 'vst_descr', 'table_colname' => 'description', 'assertion' => 'string'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'vst_id', 'table_colname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['8021q-vdlist-upd'] = array
(
	'table' => 'VLANDomain',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'vdom_descr', 'table_colname' => 'description', 'assertion' => 'string'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'vdom_id', 'table_colname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['vlandomain-vlanlist-add'] = array
(
	'table' => 'VLANDescription',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'vdom_id', 'table_colname' => 'domain_id', 'assertion' => 'uint'),
		array ('url_argname' => 'vlan_id', 'assertion' => 'uint'),
		array ('url_argname' => 'vlan_type', 'assertion' => 'enum/vlan_type'),
		array ('url_argname' => 'vlan_descr', 'assertion' => 'string0', 'if_empty' => 'NULL'),
	),
);

// This function is DEPRECATED. Show messages through showError and showSuccess,
// you dont need to return anything from an ophandler to redirect user back to the page containing submit form
function buildWideRedirectURL ($log = NULL, $nextpage = NULL, $nexttab = NULL, $moreArgs = array())
{
	global $page, $pageno, $tabno;
	if ($nextpage === NULL)
		$nextpage = $pageno;
	if ($nexttab === NULL)
		$nexttab = $tabno;
	$url = "index.php?page=${nextpage}&tab=${nexttab}";
	if (isset ($page[$nextpage]['bypass']))
		$url .= '&' . $page[$nextpage]['bypass'] . '=' . $_REQUEST[$page[$nextpage]['bypass']];

	if (count ($moreArgs) > 0)
		foreach ($moreArgs as $arg => $value)
			if (gettype ($value) != 'array')
				$url .= '&' . urlencode ($arg) . '=' . urlencode ($value);
			else
				foreach ($value as $v)
					$url .= '&' . urlencode ($arg . '[]') . '=' . urlencode ($v);

	if (! empty ($log))
	{
		if (empty ($_SESSION['log']))
			$_SESSION['log'] = $log;
		elseif ($_SESSION['log']['v'] == $log['v'])
			$_SESSION['log'] = array_merge_recursive($log, $_SESSION['log']);
		elseif ($log['v'] == 1 and $_SESSION['log']['v'] == 2)
			foreach ($log['m'] as $msg)
				setMessage($msg['message'], $msg['code'], FALSE);
		elseif ($log['v'] == 2 and $_SESSION['log']['v'] == 1)
		{
			foreach ($_SESSION['log'] as $msg)
			{
				if (! is_array ($msg))
					continue;
				var_dump ($msg);
				$new_v2_item = array('c' => '', 'a' => array());
				switch ($msg['code'])
				{
					case 'error':
						$new_v2_item['c'] = 100;
						break;
					case 'success':
						$new_v2_item['c'] = 0;
						break;
					case 'warning':
						$new_v2_item['c'] = 200;
						break;
					default:
						$new_v2_item['c'] = 300;
				}
				$new_v2_item['a'][] = $msg['message'];
				$log['m'][] = $new_v2_item;
			}
			$_SESSION['log'] = $log; // substitute v1 log structure with merged v2
		}
	}
	return $url;
}

// This function is DEPRECATED. Show messages through showError and showSuccess,
// you dont need to return anything from an ophandler to redirect user back to the page containing submit form
function buildRedirectURL ($callfunc, $status, $log_args = array(), $nextpage = NULL, $nexttab = NULL, $url_args = array())
{
	global $pageno, $tabno, $msgcode;
	if ($nextpage === NULL)
		$nextpage = $pageno;
	if ($nexttab === NULL)
		$nexttab = $tabno;
	return buildWideRedirectURL (oneLiner ($msgcode[$callfunc][$status], $log_args), $nextpage, $nexttab, $url_args);
}

$msgcode['addPortForwarding']['OK'] = 48;
$msgcode['addPortForwarding']['ERR'] = 100;
function addPortForwarding ()
{
	assertUIntArg ('object_id');
	assertIPv4Arg ('localip');
	assertIPv4Arg ('remoteip');
	assertUIntArg ('localport');
	assertStringArg ('proto');
	assertStringArg ('description', TRUE);
	$remoteport = isset ($_REQUEST['remoteport']) ? $_REQUEST['remoteport'] : '';
	if (!strlen ($remoteport))
		$remoteport = $_REQUEST['localport'];

	$error = newPortForwarding
	(
		$_REQUEST['object_id'],
		$_REQUEST['localip'],
		$_REQUEST['localport'],
		$_REQUEST['remoteip'],
		$remoteport,
		$_REQUEST['proto'],
		$_REQUEST['description']
	);

	if ($error == '')
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
}

$msgcode['delPortForwarding']['OK'] = 49;
$msgcode['delPortForwarding']['ERR'] = 111;
function delPortForwarding ()
{
	assertUIntArg ('object_id');
	assertIPv4Arg ('localip');
	assertIPv4Arg ('remoteip');
	assertUIntArg ('localport');
	assertUIntArg ('remoteport');
	assertStringArg ('proto');

	$result = deletePortForwarding
	(
		$_REQUEST['object_id'],
		$_REQUEST['localip'],
		$_REQUEST['localport'],
		$_REQUEST['remoteip'],
		$_REQUEST['remoteport'],
		$_REQUEST['proto']
	);
	buildRedirectURL (__FUNCTION__, $result !== FALSE ? 'OK' : 'ERR');
}

$msgcode['updPortForwarding']['OK'] = 51;
$msgcode['updPortForwarding']['ERR'] = 109;
function updPortForwarding ()
{
	assertUIntArg ('object_id');
	assertIPv4Arg ('localip');
	assertIPv4Arg ('remoteip');
	assertUIntArg ('localport');
	assertUIntArg ('remoteport');
	assertStringArg ('proto');
	assertStringArg ('description');

	$result = updatePortForwarding
	(
		$_REQUEST['object_id'],
		$_REQUEST['localip'],
		$_REQUEST['localport'],
		$_REQUEST['remoteip'],
		$_REQUEST['remoteport'],
		$_REQUEST['proto'],
		$_REQUEST['description']
	);
	buildRedirectURL (__FUNCTION__, $result !== FALSE ? 'OK' : 'ERR');
}

$msgcode['addPortForObject']['OK'] = 48;
$msgcode['addPortForObject']['ERR2'] = 100;
function addPortForObject ()
{
	assertStringArg ('port_name', TRUE);
	genericAssertion ('port_l2address', 'l2address0');
	genericAssertion ('port_name', 'string');
	$error = commitAddPort
	(
		$_REQUEST['object_id'],
		trim ($_REQUEST['port_name']),
		$_REQUEST['port_type_id'],
		trim ($_REQUEST['port_label']),
		trim ($_REQUEST['port_l2address'])
	);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['port_name']));
}

$msgcode['editPortForObject']['OK'] = 7;
function editPortForObject ()
{
	global $sic;
	assertUIntArg ('port_id');
	assertUIntArg ('port_type_id');
	assertStringArg ('reservation_comment', TRUE);
	genericAssertion ('l2address', 'l2address0');
	genericAssertion ('name', 'string');
	commitUpdatePort ($sic['object_id'], $sic['port_id'], $sic['name'], $sic['port_type_id'], $sic['label'], $sic['l2address'], $sic['reservation_comment']);
	return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['name']));
}

$msgcode['linkPortForObject']['OK'] = 8;
$msgcode['linkPortForObject']['ERR'] = 100;
function linkPortForObject ()
{
	assertUIntArg ('port_id');
	assertUIntArg ('remote_port_id');
	assertStringArg ('cable', TRUE);

	// FIXME: ensure, that at least one of these ports belongs to the current object
	$error = linkPorts ($_REQUEST['port_id'], $_REQUEST['remote_port_id'], $_REQUEST['cable']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	global $sic;
	$local_port_info = getPortInfo ($sic['port_id']);
	$remote_port_info = getPortInfo ($sic['remote_port_id']);
	$remote_object = spotEntity ('object', $remote_port_info['object_id']);
	return buildRedirectURL
	(
		__FUNCTION__,
		'OK',
		array
		(
			$local_port_info['name'],
			$remote_port_info['name'],
			$remote_object['dname'],
		)
	);
}

$msgcode['addMultiPorts']['OK'] = 10;
$msgcode['addMultiPorts']['ERR'] = 123;
function addMultiPorts ()
{
	assertStringArg ('format');
	assertStringArg ('input');
	assertStringArg ('port_type');
	$format = $_REQUEST['format'];
	$port_type = $_REQUEST['port_type'];
	$object_id = $_REQUEST['object_id'];
	// Input lines are escaped, so we have to explode and to chop by 2-char
	// \n and \r respectively.
	$lines1 = explode ("\n", $_REQUEST['input']);
	foreach ($lines1 as $line)
	{
		$parts = explode ('\r', $line);
		reset ($parts);
		if (!strlen ($parts[0]))
			continue;
		else
			$lines2[] = rtrim ($parts[0]);
	}
	$ports = array();
	foreach ($lines2 as $line)
	{
		switch ($format)
		{
			case 'fisxii':
				$words = explode (' ', preg_replace ('/[[:space:]]+/', ' ', $line));
				list ($slot, $port) = explode ('/', $words[0]);
				$ports[] = array
				(
					'name' => "e ${slot}/${port}",
					'l2address' => $words[8],
					'label' => "slot ${slot} port ${port}"
				);
				break;
			case 'c3600asy':
				$words = explode (' ', preg_replace ('/[[:space:]]+/', ' ', trim (substr ($line, 3))));
/*
How Async Lines are Numbered in Cisco 3600 Series Routers
http://www.cisco.com/en/US/products/hw/routers/ps274/products_tech_note09186a00801ca70b.shtml

Understanding 16- and 32-Port Async Network Modules
http://www.cisco.com/en/US/products/hw/routers/ps274/products_tech_note09186a00800a93f0.shtml
*/
				$async = $words[0];
				$slot = floor (($async - 1) / 32);
				$octalgroup = floor (($async - 1 - $slot * 32) / 8);
				$cable = $async - $slot * 32 - $octalgroup * 8;
				$og_label[0] = 'async 0-7';
				$og_label[1] = 'async 8-15';
				$og_label[2] = 'async 16-23';
				$og_label[3] = 'async 24-31';
				$ports[] = array
				(
					'name' => "async ${async}",
					'l2address' => '',
					'label' => "slot ${slot} " . $og_label[$octalgroup] . " cable ${cable}"
				);
				break;
			case 'fiwg':
				$words = explode (' ', preg_replace ('/[[:space:]]+/', ' ', $line));
				$ifnumber = $words[0] * 1;
				$ports[] = array
				(
					'name' => "e ${ifnumber}",
					'l2address' => "${words[8]}",
					'label' => "${ifnumber}"
				);
				break;
			case 'ssv1':
				$words = explode (' ', $line);
				if (!strlen ($words[0]) or !strlen ($words[1]))
					continue;
				$ports[] = array
				(
					'name' => $words[0],
					'l2address' => $words[1],
					'label' => ''
				);
				break;
			default:
				return buildRedirectURL (__FUNCTION__, 'ERR');
				break;
		}
	}
	// Create ports, if they don't exist.
	$added_count = $updated_count = $error_count = 0;
	foreach ($ports as $port)
	{
		$port_ids = getPortIDs ($object_id, $port['name']);
		if (!count ($port_ids))
		{
			$result = commitAddPort ($object_id, $port['name'], $port_type, $port['label'], $port['l2address']);
			if ($result == '')
				$added_count++;
			else
				$error_count++;
		}
		elseif (count ($port_ids) == 1) // update only single-socket ports
		{
			$result = commitUpdatePort ($object_id, $port_ids[0], $port['name'], $port_type, $port['label'], $port['l2address']);
			if ($result == '')
				$updated_count++;
			else
				$error_count++;
		}
	}
	return buildRedirectURL (__FUNCTION__, 'OK', array ($added_count, $updated_count, $error_count));
}

$msgcode['addBulkPorts']['OK'] = 82;
function addBulkPorts ()
{
	assertStringArg ('port_type_id');
	assertStringArg ('port_name');
	assertStringArg ('port_label', TRUE);
	assertUIntArg ('port_numbering_start', TRUE);
	assertUIntArg ('port_numbering_count');
	
	$object_id = $_REQUEST['object_id'];
	$port_name = $_REQUEST['port_name'];
	$port_type_id = $_REQUEST['port_type_id'];
	$port_label = $_REQUEST['port_label'];
	$port_numbering_start = $_REQUEST['port_numbering_start'];
	$port_numbering_count = $_REQUEST['port_numbering_count'];
	
	$added_count = $error_count = 0;
	if(strrpos($port_name, "%u") === false )
		$port_name .= '%u';
	for ($i=0,$c=$port_numbering_start; $i<$port_numbering_count; $i++,$c++)
	{
		$result = commitAddPort ($object_id, @sprintf($port_name,$c), $port_type_id, @sprintf($port_label,$c), '');
		if ($result == '')
			$added_count++;
		else
			$error_count++;
	}
	return buildRedirectURL (__FUNCTION__, 'OK', array ($added_count, $error_count));
}

$msgcode['updIPv4Allocation']['OK'] = 51;
$msgcode['updIPv4Allocation']['ERR'] = 109;
function updIPv4Allocation ()
{
	assertIPv4Arg ('ip');
	assertUIntArg ('object_id');
	assertStringArg ('bond_name', TRUE);
	genericAssertion ('bond_type', 'enum/inet4alloc');

	$result = updateBond ($_REQUEST['ip'], $_REQUEST['object_id'], $_REQUEST['bond_name'], $_REQUEST['bond_type']);
	return buildRedirectURL (__FUNCTION__, $result === FALSE ? 'ERR' : 'OK');
}

$msgcode['updIPv6Allocation']['OK'] = 51;
$msgcode['updIv6PAllocation']['ERR'] = 109;
function updIPv6Allocation ()
{
	$ipv6 = assertIPv6Arg ('ip');
	assertUIntArg ('object_id');
	assertStringArg ('bond_name', TRUE);
	genericAssertion ('bond_type', 'enum/inet6alloc');

	$result = updateIPv6Bond ($ipv6, $_REQUEST['object_id'], $_REQUEST['bond_name'], $_REQUEST['bond_type']);
	return buildRedirectURL (__FUNCTION__, $result === FALSE ? 'ERR' : 'OK');
}

$msgcode['delIPv4Allocation']['OK'] = 49;
$msgcode['delIPv4Allocation']['ERR'] = 111;
function delIPv4Allocation ()
{
	assertIPv4Arg ('ip');
	assertUIntArg ('object_id');

	$result = unbindIpFromObject ($_REQUEST['ip'], $_REQUEST['object_id']);
	return buildRedirectURL (__FUNCTION__, $result === FALSE ? 'ERR' : 'OK');
}

$msgcode['delIPv6Allocation']['OK'] = 49;
$msgcode['delIPv6Allocation']['ERR'] = 111;
function delIPv6Allocation ()
{
	assertUIntArg ('object_id');
	$ipv6 = assertIPv6Arg ('ip');
	$result = unbindIPv6FromObject ($ipv6, $_REQUEST['object_id']);
	return buildRedirectURL (__FUNCTION__, $result === FALSE ? 'ERR' : 'OK');
}

$msgcode['addIPv4Allocation']['OK'] = 48;
$msgcode['addIPv4Allocation']['ERR1'] = 170;
$msgcode['addIPv4Allocation']['ERR2'] = 100;
function addIPv4Allocation ()
{
	assertIPv4Arg ('ip');
	assertUIntArg ('object_id');
	assertStringArg ('bond_name', TRUE);
	genericAssertion ('bond_type', 'enum/inet4alloc');

	// Strip masklen.
	$ip = preg_replace ('@/[[:digit:]]+$@', '', $_REQUEST['ip']);
	if  (getConfigVar ('IPV4_JAYWALK') != 'yes' and NULL === getIPv4AddressNetworkId ($ip))
		return buildRedirectURL (__FUNCTION__, 'ERR1', array ($ip));
	
	if (FALSE === bindIpToObject ($ip, $_REQUEST['object_id'], $_REQUEST['bond_name'], $_REQUEST['bond_type']))
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($error));
	$address = getIPv4Address ($ip);
	if ($address['reserved'] == 'yes' or strlen ($address['name']))
	{
		$release = getConfigVar ('IPV4_AUTO_RELEASE');
		if ($release >= 1)
			$address['reserved'] = 'no';
		if ($release >= 2)
			$address['name'] = '';
		updateAddress ($ip, $address['name'], $address['reserved']);
	}
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addIPv6Allocation']['OK'] = 48;
$msgcode['addIPv6Allocation']['ERR1'] = 170;
$msgcode['addIPv6Allocation']['ERR2'] = 100;
function addIPv6Allocation ()
{
	assertUIntArg ('object_id');
	assertStringArg ('bond_name', TRUE);
	genericAssertion ('bond_type', 'enum/inet6alloc');

	// Strip masklen.
	$ipv6 = new IPv6Address;
	if (! $ipv6->parse (preg_replace ('@/\d+$@', '', $_REQUEST['ip'])))
		throw new InvalidRequestArgException('ip', $_REQUEST['ip'], 'parameter is not a valid ipv6 address');

	if  (getConfigVar ('IPV4_JAYWALK') != 'yes' and NULL === getIPv6AddressNetworkId ($ipv6))
		return buildRedirectURL (__FUNCTION__, 'ERR1', array ($ip));

	if (FALSE === bindIPv6ToObject ($ipv6, $_REQUEST['object_id'], $_REQUEST['bond_name'], $_REQUEST['bond_type']))
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($error));
	$address = getIPv6Address ($ipv6);
	if ($address['reserved'] == 'yes' or strlen ($address['name']))
	{
		$release = getConfigVar ('IPV4_AUTO_RELEASE');
		if ($release >= 1)
			$address['reserved'] = 'no';
		if ($release >= 2)
			$address['name'] = '';
		updateAddress ($ipv6, $address['name'], $address['reserved']);
	}
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addIPv4Prefix']['OK'] = 48;
$msgcode['addIPv4Prefix']['ERR'] = 100;
function addIPv4Prefix ()
{
	assertStringArg ('range');
	assertStringArg ('name', TRUE);

	$is_bcast = isset ($_REQUEST['is_bcast']) ? $_REQUEST['is_bcast'] : 'off';
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	global $sic;
	$error = createIPv4Prefix ($_REQUEST['range'], $sic['name'], $is_bcast == 'on', $taglist);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addIPv6Prefix']['OK'] = 48;
$msgcode['addIPv6Prefix']['ERR'] = 100;
function addIPv6Prefix ()
{
	assertStringArg ('range');
	assertStringArg ('name', TRUE);

	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	global $sic;
	$error = createIPv6Prefix ($_REQUEST['range'], $sic['name'], $taglist);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['delIPv4Prefix']['OK'] = 49;
$msgcode['delIPv4Prefix']['ERR'] = 100;
function delIPv4Prefix ()
{
	assertUIntArg ('id');
	$error = destroyIPv4Prefix ($_REQUEST['id']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['delIPv6Prefix']['OK'] = 49;
$msgcode['delIPv6Prefix']['ERR'] = 100;
function delIPv6Prefix ()
{
	assertUIntArg ('id');
	$error = destroyIPv6Prefix ($_REQUEST['id']);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['editAddress']['OK'] = 51;
$msgcode['editAddress']['ERR'] = 100;
function editAddress ()
{
	assertStringArg ('name', TRUE);

	if (isset ($_REQUEST['reserved']))
		$reserved = $_REQUEST['reserved'];
	else
		$reserved = 'off';
	$error = updateAddress ($_REQUEST['ip'], $_REQUEST['name'], $reserved == 'on' ? 'yes' : 'no');
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['editv6Address']['OK'] = 51;
$msgcode['editv6Address']['ERR'] = 100;
function editv6Address ()
{
	$ipv6 = assertIPArg ('ip');
	assertStringArg ('name', TRUE);

	if (isset ($_REQUEST['reserved']))
		$reserved = $_REQUEST['reserved'];
	else
		$reserved = 'off';
	$error = updateAddress ($ipv6, $_REQUEST['name'], $reserved == 'on' ? 'yes' : 'no');
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['createUser']['OK'] = 5;
$msgcode['createUser']['ERR'] = 102;
function createUser ()
{
	assertStringArg ('username');
	assertStringArg ('realname', TRUE);
	assertStringArg ('password');
	$username = $_REQUEST['username'];
	$password = sha1 ($_REQUEST['password']);
	$result = commitCreateUserAccount ($username, $_REQUEST['realname'], $password);
	if ($result != TRUE)
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($username));
	if (isset ($_REQUEST['taglist']))
		produceTagsForLastRecord ('user', $_REQUEST['taglist']);
	return buildRedirectURL (__FUNCTION__, 'OK', array ($username));
}

$msgcode['updateUser']['OK'] = 7;
$msgcode['updateUser']['ERR2'] = 104;
function updateUser ()
{
	assertStringArg ('username');
	assertStringArg ('realname', TRUE);
	assertStringArg ('password');
	$username = $_REQUEST['username'];
	$new_password = $_REQUEST['password'];
	$userinfo = spotEntity ('user', $_REQUEST['user_id']);
	// Update user password only if provided password is not the same as current password hash.
	if ($new_password != $userinfo['user_password_hash'])
		$new_password = sha1 ($new_password);
	$result = commitUpdateUserAccount ($_REQUEST['user_id'], $username, $_REQUEST['realname'], $new_password);
	if ($result !== FALSE)
		return buildRedirectURL (__FUNCTION__, 'OK', array ($username));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($username));
}

$msgcode['updateDictionary']['OK'] = 51;
function updateDictionary ()
{
	assertUIntArg ('dict_key');
	assertStringArg ('dict_value');
	// this request must be built with chapter_no
	usePreparedUpdateBlade
	(
		'Dictionary',
		array ('dict_value' => $sic['dict_value']),
		array
		(
			'chapter_id' => $sic['chapter_no'],
			'dict_key' => $sic['dict_key'],
		)
	);
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateChapter']['OK'] = 51;
function updateChapter ()
{
	assertUIntArg ('chapter_no');
	assertStringArg ('chapter_name');
	usePreparedUpdateBlade
	(
		'Chapter',
		array
		(
			'name' => $chapter_name,
		),
		array
		(
			'id' => $chapter_no,
			'sticky' => 'no', // note this constant, it protects system chapters
		)
	);
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['delChapter']['OK'] = 49;
$msgcode['delChapter']['ERR'] = 111;
function delChapter ()
{
	assertUIntArg ('chapter_no');
	if (commitDeleteChapter ($_REQUEST['chapter_no']))
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['supplementAttrMap']['OK'] = 48;
$msgcode['supplementAttrMap']['ERR1'] = 154;
$msgcode['supplementAttrMap']['ERR2'] = 110;
function supplementAttrMap ()
{
	assertUIntArg ('attr_id');
	assertUIntArg ('objtype_id');
	$attrMap = getAttrMap();
	if ($attrMap[$_REQUEST['attr_id']]['type'] != 'dict')
		$chapter_id = NULL;
	else
	{
		try
		{
			assertUIntArg ('chapter_no');
		}
		catch (InvalidRequestArgException $e)
		{
			return buildRedirectURL (__FUNCTION__, 'ERR1', array ('chapter not selected'));
		}
		$chapter_id = $_REQUEST['chapter_no'];
	}
	if (commitSupplementAttrMap ($_REQUEST['attr_id'], $_REQUEST['objtype_id'], $chapter_id) !== FALSE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR2');
}

$msgcode['clearSticker']['OK'] = 49;
$msgcode['clearSticker']['ERR'] = 120;
function clearSticker ()
{
	assertUIntArg ('attr_id');
	if (commitResetAttrValue ($_REQUEST['object_id'], $_REQUEST['attr_id']) !== FALSE)
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['updateObjectAllocation']['OK'] = 63;
function updateObjectAllocation ()
{
	global $remote_username, $sic;
	if (!isset ($_REQUEST['got_atoms']))
	{
		unset($_GET['page']);
		unset($_GET['tab']);
		unset($_GET['op']);
		unset($_POST['page']);
		unset($_POST['tab']);
		unset($_POST['op']);
		return buildWideRedirectURL (array(), NULL, NULL, array_merge ($_GET, $_POST));
	}
	$object_id = $_REQUEST['object_id'];
	$workingRacksData = array();
	foreach ($_REQUEST['rackmulti'] as $cand_id)
	{
		if (!isset ($workingRacksData[$cand_id]))
		{
			$rackData = spotEntity ('rack', $cand_id);
			amplifyCell ($rackData);
			$workingRacksData[$cand_id] = $rackData;
		}
	}

	foreach ($workingRacksData as &$rd)
		applyObjectMountMask ($rd, $object_id);

	$oldMolecule = getMoleculeForObject ($object_id);
	$changecnt = 0;
	$log = array();
	foreach ($workingRacksData as $rack_id => $rackData)
	{
		$logrecord = processGridForm ($rackData, 'F', 'T', $object_id);
		$log[] = $logrecord;
		if ($logrecord['code'] == 300)
			continue;
		$changecnt++;
		// Reload our working copy after form processing.
		$rackData = spotEntity ('rack', $cand_id);
		amplifyCell ($rackData);
		applyObjectMountMask ($rackData, $object_id);
		$workingRacksData[$rack_id] = $rackData;
	}
	if (!$changecnt)
		return buildRedirectURL (__FUNCTION__, 'OK', $changecnt);
	// Log a record.
	$newMolecule = getMoleculeForObject ($object_id);
	usePreparedInsertBlade
	(
		'MountOperation', 
		array
		(
			'object_id' => $object_id,
			'old_molecule_id' => count ($oldMolecule) ? createMolecule ($oldMolecule) : NULL,
			'new_molecule_id' => count ($newMolecule) ? createMolecule ($newMolecule) : NULL,
			'user_name' => $remote_username,
			'comment' => empty ($sic['comment']) ? NULL : $sic['comment'],
		)
	);
	$log[] = array ('code' => 200, 'message' => 'history logged');
	return buildWideRedirectURL ($log);
}

$msgcode['updateObject']['OK'] = 51;
$msgcode['updateObject']['ERR'] = 109;
function updateObject ()
{
	assertUIntArg ('num_attrs', TRUE);
	assertStringArg ('object_name', TRUE);
	assertStringArg ('object_label', TRUE);
	assertStringArg ('object_barcode', TRUE);
	assertStringArg ('object_asset_no', TRUE);
	if (isset ($_REQUEST['object_has_problems']) and $_REQUEST['object_has_problems'] == 'on')
		$has_problems = 'yes';
	else
		$has_problems = 'no';

	if (commitUpdateObject (
		$_REQUEST['object_id'],
		$_REQUEST['object_name'],
		$_REQUEST['object_label'],
		$_REQUEST['object_barcode'],
		$has_problems,
		$_REQUEST['object_asset_no'],
		$_REQUEST['object_comment']
	) !== TRUE)
		return buildRedirectURL (__FUNCTION__, 'ERR');

	// Update optional attributes
	$oldvalues = getAttrValues ($_REQUEST['object_id']);
	$result = array();
	$num_attrs = isset ($_REQUEST['num_attrs']) ? $_REQUEST['num_attrs'] : 0;
	for ($i = 0; $i < $num_attrs; $i++)
	{
		assertUIntArg ("${i}_attr_id");
		$attr_id = $_REQUEST["${i}_attr_id"];

		// Field is empty, delete attribute and move on. OR if the field type is a dictionary and it is the --NOT SET-- value of 0
		if (!strlen ($_REQUEST["${i}_value"]) || ($oldvalues[$attr_id]['type']=='dict' && $_REQUEST["${i}_value"] == 0))
		{
			commitResetAttrValue ($_REQUEST['object_id'], $attr_id);
			continue;
		}

		// The value could be uint/float, but we don't know ATM. Let SQL
		// server check this and complain.
		assertStringArg ("${i}_value");
		$value = $_REQUEST["${i}_value"];
		switch ($oldvalues[$attr_id]['type'])
		{
			case 'uint':
			case 'float':
			case 'string':
				$oldvalue = $oldvalues[$attr_id]['value'];
				break;
			case 'dict':
				$oldvalue = $oldvalues[$attr_id]['key'];
				break;
			default:
		}
		if ($value === $oldvalue) // ('' == 0), but ('' !== 0)
			continue;

		// Note if the queries succeed or not, it determines which page they see.
		$result[] = commitUpdateAttrValue ($_REQUEST['object_id'], $attr_id, $value);
	}
	if (in_array (FALSE, $result))
		return buildRedirectURL (__FUNCTION__, 'ERR');

	// Invalidate thumb cache of all racks objects could occupy.
	foreach (getResidentRacksData ($_REQUEST['object_id'], FALSE) as $rack_id)
		usePreparedUpdateBlade ('Rack', array ('thumb_data' => NULL), array ('id' => $rack_id));

	return buildRedirectURL (__FUNCTION__, 'OK');
}

function addMultipleObjects()
{
	$log = emptyLog();
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	$max = getConfigVar ('MASSCOUNT');
	for ($i = 0; $i < $max; $i++)
	{
		if (!isset ($_REQUEST["${i}_object_type_id"]))
		{
			$log = mergeLogs ($log, oneLiner (184, array ($i + 1)));
			break;
		}
		assertUIntArg ("${i}_object_type_id", TRUE);
		assertStringArg ("${i}_object_name", TRUE);
		assertStringArg ("${i}_object_label", TRUE);
		assertStringArg ("${i}_object_asset_no", TRUE);
		assertStringArg ("${i}_object_barcode", TRUE);
		$name = $_REQUEST["${i}_object_name"];

		// It's better to skip silently, than to print a notice.
		if ($_REQUEST["${i}_object_type_id"] == 0)
			continue;
		if (($object_id = commitAddObject
		(
			$name,
			$_REQUEST["${i}_object_label"],
			$_REQUEST["${i}_object_barcode"],
			$_REQUEST["${i}_object_type_id"],
			$_REQUEST["${i}_object_asset_no"],
			$taglist
		)) !== FALSE){
			$info = spotEntity ('object', $object_id);
			// FIXME: employ amplifyCell() instead of calling loader functions directly
			amplifyCell ($info);
			$log = mergeLogs ($log, oneLiner (5, array ('<a href="' . makeHref (array ('page' => 'object', 'tab' => 'default', 'object_id' => $object_id)) . '">' . $info['dname'] . '</a>')));
		}else{
			$log = mergeLogs ($log, oneLiner (147, array ($name)));
		}
	}
	return buildWideRedirectURL ($log);
}

function addLotOfObjects()
{
	$log = emptyLog();
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	assertUIntArg ('global_type_id', TRUE);
	assertStringArg ('namelist', TRUE);
	$global_type_id = $_REQUEST['global_type_id'];
	if ($global_type_id == 0 or !strlen ($_REQUEST['namelist']))
		$log = mergeLogs ($log, oneLiner (186));
	else
	{
		// The name extractor below was stolen from ophandlers.php:addMultiPorts()
		$names1 = explode ("\n", $_REQUEST['namelist']);
		$names2 = array();
		foreach ($names1 as $line)
		{
			$parts = explode ('\r', $line);
			reset ($parts);
			if (!strlen ($parts[0]))
				continue;
			else
				$names2[] = rtrim ($parts[0]);
		}
		foreach ($names2 as $name)
			if (($object_id = commitAddObject ($name, '', '', $global_type_id, '', $taglist)) !== FALSE)
			{
				$info = spotEntity ('object', $object_id);
				amplifyCell ($info);
				$log = mergeLogs ($log, oneLiner (5, array ('<a href="' . makeHref (array ('page' => 'object', 'tab' => 'default', 'object_id' => $object_id)) . '">' . $info['dname'] . '</a>')));
			}
			else
				$log = mergeLogs ($log, oneLiner (147, array ($name)));
	}
	return buildWideRedirectURL ($log);
}

$msgcode['deleteObject']['OK'] = 6;
function deleteObject ()
{
	assertUIntArg ('object_id');
	$oinfo = spotEntity ('object', $_REQUEST['object_id']);

	$racklist = getResidentRacksData ($_REQUEST['object_id'], FALSE);
	commitDeleteObject ($_REQUEST['object_id']);
	foreach ($racklist as $rack_id)
		usePreparedUpdateBlade ('Rack', array ('thumb_data' => NULL), array ('id' => $rack_id));
	return buildRedirectURL (__FUNCTION__, 'OK', array ($oinfo['dname']));
}

$msgcode['resetObject']['OK'] = 57;
function resetObject ()
{
	$oinfo = spotEntity ('object', $_REQUEST['object_id']);

	$racklist = getResidentRacksData ($_REQUEST['object_id'], FALSE);
	commitResetObject ($_REQUEST['object_id']);
	foreach ($racklist as $rack_id)
		usePreparedUpdateBlade ('Rack', array ('thumb_data' => NULL), array ('id' => $rack_id));
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['useupPort']['OK'] = 49;
function useupPort ()
{
	global $sic;
	assertUIntArg ('port_id');
	usePreparedUpdateBlade
	(
		'Port',
		array
		(
			'reservation_comment' => NULL,
		),
		array
		(
			'object_id' => $sic['object_id'],
			'id' => $sic['port_id'],
		)
	);
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateUI']['OK'] = 51;
function updateUI ()
{
	assertUIntArg ('num_vars');

	for ($i = 0; $i < $_REQUEST['num_vars']; $i++)
	{
		assertStringArg ("${i}_varname");
		assertStringArg ("${i}_varvalue", TRUE);
		$varname = $_REQUEST["${i}_varname"];
		$varvalue = $_REQUEST["${i}_varvalue"];

		// If form value = value in DB, don't bother updating DB
		if (!isConfigVarChanged($varname, $varvalue))
			continue;
		// any exceptions will be handled by process.php
		setConfigVar ($varname, $varvalue, TRUE);
	}
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['saveMyPreferences']['OK'] = 51;
function saveMyPreferences ()
{
	assertUIntArg ('num_vars');

	for ($i = 0; $i < $_REQUEST['num_vars']; $i++)
	{
		assertStringArg ("${i}_varname");
		assertStringArg ("${i}_varvalue", TRUE);
		$varname = $_REQUEST["${i}_varname"];
		$varvalue = $_REQUEST["${i}_varvalue"];

		// If form value = value in DB, don't bother updating DB
		if (!isConfigVarChanged($varname, $varvalue))
			continue;
		setUserConfigVar ($varname, $varvalue);
	}
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['resetMyPreference']['OK'] = 51;
function resetMyPreference ()
{
	assertStringArg ("varname");
	resetUserConfigVar ($_REQUEST["varname"]);
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['resetUIConfig']['OK'] = 57;
function resetUIConfig()
{
	setConfigVar ('MASSCOUNT','15');
	setConfigVar ('MAXSELSIZE','30');
	setConfigVar ('ROW_SCALE','2');
	setConfigVar ('PORTS_PER_ROW','12');
	setConfigVar ('IPV4_ADDRS_PER_PAGE','256');
	setConfigVar ('DEFAULT_RACK_HEIGHT','42');
	setConfigVar ('DEFAULT_SLB_VS_PORT','');
	setConfigVar ('DEFAULT_SLB_RS_PORT','');
	setConfigVar ('DETECT_URLS','no');
	setConfigVar ('RACK_PRESELECT_THRESHOLD','1');
	setConfigVar ('DEFAULT_IPV4_RS_INSERVICE','no');
	setConfigVar ('AUTOPORTS_CONFIG','4 = 1*33*kvm + 2*24*eth%u;15 = 1*446*kvm');
	setConfigVar ('SHOW_EXPLICIT_TAGS','yes');
	setConfigVar ('SHOW_IMPLICIT_TAGS','yes');
	setConfigVar ('SHOW_AUTOMATIC_TAGS','no');
	setConfigVar ('DEFAULT_OBJECT_TYPE','4');
	setConfigVar ('IPV4_AUTO_RELEASE','1');
	setConfigVar ('SHOW_LAST_TAB', 'no');
	setConfigVar ('EXT_IPV4_VIEW', 'yes');
	setConfigVar ('TREE_THRESHOLD', '25');
	setConfigVar ('IPV4_JAYWALK', 'no');
	setConfigVar ('ADDNEW_AT_TOP', 'yes');
	setConfigVar ('IPV4_TREE_SHOW_USAGE', 'yes');
	setConfigVar ('PREVIEW_TEXT_MAXCHARS', '10240');
	setConfigVar ('PREVIEW_TEXT_ROWS', '25');
	setConfigVar ('PREVIEW_TEXT_COLS', '80');
	setConfigVar ('PREVIEW_IMAGE_MAXPXS', '320');
	setConfigVar ('VENDOR_SIEVE', '');
	setConfigVar ('IPV4LB_LISTSRC', '{$typeid_4}');
	setConfigVar ('IPV4OBJ_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8} or {$typeid_12} or {$typeid_445} or {$typeid_447} or {$typeid_798}');
	setConfigVar ('IPV4NAT_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8} or {$typeid_798}');
	setConfigVar ('ASSETWARN_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8}');
	setConfigVar ('NAMEWARN_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8}');
	setConfigVar ('RACKS_PER_ROW','12');
	setConfigVar ('FILTER_PREDICATE_SIEVE','');
	setConfigVar ('FILTER_DEFAULT_ANDOR','or');
	setConfigVar ('FILTER_SUGGEST_ANDOR','yes');
	setConfigVar ('FILTER_SUGGEST_TAGS','yes');
	setConfigVar ('FILTER_SUGGEST_PREDICATES','yes');
	setConfigVar ('FILTER_SUGGEST_EXTRA','no');
	setConfigVar ('DEFAULT_SNMP_COMMUNITY','public');
	setConfigVar ('IPV4_ENABLE_KNIGHT','yes');
	setConfigVar ('TAGS_TOPLIST_SIZE','50');
	setConfigVar ('TAGS_QUICKLIST_SIZE','20');
	setConfigVar ('TAGS_QUICKLIST_THRESHOLD','50');
	setConfigVar ('ENABLE_MULTIPORT_FORM', 'no');
	setConfigVar ('DEFAULT_PORT_IIF_ID', '1');
	setConfigVar ('DEFAULT_PORT_OIF_IDS', '1=24; 3=1078; 4=1077; 5=1079; 6=1080; 8=1082; 9=1084');
	setConfigVar ('IPV4_TREE_RTR_AS_CELL', 'yes');
	setConfigVar ('PROXIMITY_RANGE', 0);
	setConfigVar ('IPV4_TREE_SHOW_VLAN', 'yes');
	setConfigVar ('VLANSWITCH_LISTSRC', '');
	setConfigVar ('VLANIPV4NET_LISTSRC', '');
	setConfigVar ('DEFAULT_VDOM_ID', '');
	setConfigVar ('DEFAULT_VST_ID', '');
	setConfigVar ('STATIC_FILTER', 'yes');
	setConfigVar ('8021Q_DEPLOY_MINAGE', '300');
	setConfigVar ('8021Q_DEPLOY_MAXAGE', '3600');
	setConfigVar ('8021Q_DEPLOY_RETRY', '10800');
	setConfigVar ('8021Q_WRI_AFTER_CONFT_LISTSRC', 'false');
	setConfigVar ('8021Q_INSTANT_DEPLOY', 'no');
	setConfigVar ('CDP_RUNNERS_LISTSRC', '');
	setConfigVar ('LLDP_RUNNERS_LISTSRC', '');
	setConfigVar ('HNDP_RUNNERS_LISTSRC', '');
	setConfigVar ('SHRINK_TAG_TREE_ON_CLICK', 'yes');
	setConfigVar ('MAX_UNFILTERED_ENTITIES', '0');
	setConfigVar ('SYNCDOMAIN_MAX_PROCESSES', '0');
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addRealServer']['OK'] = 48;
$msgcode['addRealServer']['ERR'] = 110;
// Add single record.
function addRealServer ()
{
	assertUIntArg ('pool_id');
	assertIPv4Arg ('remoteip');
	assertStringArg ('rsport', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (!addRStoRSPool (
		$_REQUEST['pool_id'],
		$_REQUEST['remoteip'],
		$_REQUEST['rsport'],
		getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'),
		$_REQUEST['rsconfig']
	))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addRealServers']['OK'] = 37;
$msgcode['addRealServers']['ERR1'] = 131;
$msgcode['addRealServers']['ERR2'] = 127;
// Parse textarea submitted and try adding a real server for each line.
function addRealServers ()
{
	assertStringArg ('format');
	assertStringArg ('rawtext');
	$ngood = $nbad = 0;
	$rsconfig = '';
	// Keep in mind, that the text will have HTML entities (namely '>') escaped.
	foreach (explode ("\n", dos2unix ($_REQUEST['rawtext'])) as $line)
	{
		if (!strlen ($line))
			continue;
		$match = array ();
		switch ($_REQUEST['format'])
		{
			case 'ipvs_2': // address and port only
				if (!preg_match ('/^  -&gt; ([0-9\.]+):([0-9]+) /', $line, $match))
					continue;
				if (addRStoRSPool ($_REQUEST['pool_id'], $match[1], $match[2], getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), ''))
					$ngood++;
				else
					$nbad++;
				break;
			case 'ipvs_3': // address, port and weight
				if (!preg_match ('/^  -&gt; ([0-9\.]+):([0-9]+) +[a-zA-Z]+ +([0-9]+) /', $line, $match))
					continue;
				if (addRStoRSPool ($_REQUEST['pool_id'], $match[1], $match[2], getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), 'weight ' . $match[3]))
					$ngood++;
				else
					$nbad++;
				break;
			case 'ssv_2': // IP address and port
				if (!preg_match ('/^([0-9\.]+) ([0-9]+)$/', $line, $match))
					continue;
				if (addRStoRSPool ($_REQUEST['pool_id'], $match[1], $match[2], getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), ''))
					$ngood++;
				else
					$nbad++;
				break;
			case 'ssv_1': // IP address
				if (!preg_match ('/^([0-9\.]+)$/', $line, $match))
					continue;
				if (addRStoRSPool ($_REQUEST['pool_id'], $match[1], 0, getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), ''))
					$ngood++;
				else
					$nbad++;
				break;
			default:
				return buildRedirectURL (__FUNCTION__, 'ERR1');
				break;
		}
	}
	if ($nbad == 0 and $ngood > 0)
		return buildRedirectURL (__FUNCTION__, 'OK', array ($ngood));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR2', array ($ngood, $nbad));
}

$msgcode['addVService']['OK'] = 48;
function addVService ()
{
	assertIPv4Arg ('vip');
	assertUIntArg ('vport');
	genericAssertion ('proto', 'enum/ipproto');
	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	usePreparedExecuteBlade
	(
		'INSERT INTO IPv4VS (vip, vport, proto, name, vsconfig, rsconfig) VALUES (INET_ATON(?), ?, ?, ?, ?, ?)',
		array
		(
			$_REQUEST['vip'],
			$_REQUEST['vport'],
			$_REQUEST['proto'],
			!mb_strlen ($_REQUEST['name']) ? NULL : $_REQUEST['name'],
			!strlen ($_REQUEST['vsconfig']) ? NULL : $_REQUEST['vsconfig'],
			!strlen ($_REQUEST['rsconfig']) ? NULL : $_REQUEST['rsconfig'],
		)
	);
	produceTagsForLastRecord ('ipv4vs', isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array());
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['deleteVService']['OK'] = 49;
$msgcode['deleteVService']['ERR'] = 111;
function deleteVService ()
{
	assertUIntArg ('vs_id');
	if (!commitDeleteVS ($_REQUEST['vs_id']))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateSLBDefConfig']['OK'] = 43;
$msgcode['updateSLBDefConfig']['ERR'] = 109;
function updateSLBDefConfig ()
{
	$data = array(
		'vs' => $_REQUEST['vsconfig'],
		'rs' => $_REQUEST['rsconfig']
	);
	if (!commitUpdateSLBDefConf ($data))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateRealServer']['OK'] = 51;
$msgcode['updateRealServer']['ERR'] = 109;
function updateRealServer ()
{
	assertUIntArg ('rs_id');
	assertIPv4Arg ('rsip');
	assertStringArg ('rsport', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (FALSE === commitUpdateRS (
		$_REQUEST['rs_id'],
		$_REQUEST['rsip'],
		$_REQUEST['rsport'],
		$_REQUEST['rsconfig']
	))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateVService']['OK'] = 51;
$msgcode['updateVService']['ERR'] = 109;
function updateVService ()
{
	assertUIntArg ('vs_id');
	assertIPv4Arg ('vip');
	assertUIntArg ('vport');
	genericAssertion ('proto', 'enum/ipproto');
	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (FALSE === commitUpdateVS (
		$_REQUEST['vs_id'],
		$_REQUEST['vip'],
		$_REQUEST['vport'],
		$_REQUEST['proto'],
		$_REQUEST['name'],
		$_REQUEST['vsconfig'],
		$_REQUEST['rsconfig']
	))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addLoadBalancer']['OK'] = 48;
$msgcode['addLoadBalancer']['ERR'] = 110;
function addLoadBalancer ()
{
	assertUIntArg ('pool_id');
	assertUIntArg ('object_id');
	assertUIntArg ('vs_id');
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if (! empty($_REQUEST['prio']))
		assertUIntArg('prio', TRUE);

	if (!addLBtoRSPool (
		$_REQUEST['pool_id'],
		$_REQUEST['object_id'],
		$_REQUEST['vs_id'],
		$_REQUEST['vsconfig'],
		$_REQUEST['rsconfig'],
		$_REQUEST['prio']
	))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['addRSPool']['OK'] = 48;
$msgcode['addRSPool']['ERR'] = 100;
function addRSPool ()
{
	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	$error = commitCreateRSPool
	(
		$_REQUEST['name'],
		$_REQUEST['vsconfig'],
		$_REQUEST['rsconfig'],
		isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array()
	);
	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['deleteRSPool']['OK'] = 49;
$msgcode['deleteRSPool']['ERR'] = 111;
function deleteRSPool ()
{
	assertUIntArg ('pool_id');
	if (commitDeleteRSPool ($_REQUEST['pool_id']) === FALSE)
		return buildRedirectURL (__FUNCTION__, 'ERR');
	else
		return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['updateRSInService']['OK'] = 26;
$msgcode['updateRSInService']['ERR'] = 141;
function updateRSInService ()
{
	assertUIntArg ('rscount');
	$pool_id = $_REQUEST['pool_id'];
	$orig = spotEntity ('ipv4rspool', $pool_id);
	amplifyCell ($orig);
	$nbad = $ngood = 0;
	for ($i = 1; $i <= $_REQUEST['rscount']; $i++)
	{
		$rs_id = $_REQUEST["rsid_${i}"];
		if (isset ($_REQUEST["inservice_${i}"]) and $_REQUEST["inservice_${i}"] == 'on')
			$newval = 'yes';
		else
			$newval = 'no';
		if ($newval != $orig['rslist'][$rs_id]['inservice'])
		{
			if (FALSE !== commitSetInService ($rs_id, $newval))
				$ngood++;
			else
				$nbad++;
		}
	}
	if (!$nbad)
		return buildRedirectURL (__FUNCTION__, 'OK', array ($ngood));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($nbad, $ngood));
}

$msgcode['importPTRData']['OK'] = 26;
$msgcode['importPTRData']['ERR'] = 141;
// FIXME: check, that each submitted address belongs to the prefix we
// are operating on.
function importPTRData ()
{
	assertUIntArg ('addrcount');
	$nbad = $ngood = 0;
	for ($i = 0; $i < $_REQUEST['addrcount']; $i++)
	{
		$inputname = "import_${i}";
		if (!isset ($_REQUEST[$inputname]) or $_REQUEST[$inputname] != 'on')
			continue;
		assertIPv4Arg ("addr_${i}");
		assertStringArg ("descr_${i}", TRUE);
		assertStringArg ("rsvd_${i}");
		// Non-existent addresses will not have this argument set in request.
		$rsvd = 'no';
		if ($_REQUEST["rsvd_${i}"] == 'yes')
			$rsvd = 'yes';
		if (updateAddress ($_REQUEST["addr_${i}"], $_REQUEST["descr_${i}"], $rsvd) == '')
			$ngood++;
		else
			$nbad++;
	}
	if (!$nbad)
		return buildRedirectURL (__FUNCTION__, 'OK', array ($ngood));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($nbad, $ngood));
}

$msgcode['generateAutoPorts']['OK'] = 21;
$msgcode['generateAutoPorts']['ERR'] = 142;
function generateAutoPorts ()
{
	global $pageno;
	$info = spotEntity ('object', $_REQUEST['object_id']);
	// Navigate away in case of success, stay at the place otherwise.
	if (executeAutoPorts ($_REQUEST['object_id'], $info['objtype_id']))
		return buildRedirectURL (__FUNCTION__, 'OK', array(), $pageno, 'ports');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

$msgcode['saveEntityTags']['OK'] = 26;
$msgcode['saveEntityTags']['ERR1'] = 143;
// Filter out implicit tags before storing the new tag set.
function saveEntityTags ()
{
	global $pageno, $etype_by_pageno;
	if (!isset ($etype_by_pageno[$pageno]))
		throw new RackTablesError ('key not found in etype_by_pageno', RackTablesError::INTERNAL);
	$realm = $etype_by_pageno[$pageno];
	$entity_id = getBypassValue();
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	// Build a chain from the submitted data, minimize it,
	// then wipe existing records and store the new set instead.
	destroyTagsForEntity ($realm, $entity_id);
	// TODO: these actions are very close to what rebuildTagChainForEntity() does,
	// so why not use it?
	$newchain = getExplicitTagsOnly (buildTagChainFromIds ($taglist));
	$n_succeeds = $n_errors = 0;
	foreach ($newchain as $taginfo)
		if (addTagForEntity ($realm, $entity_id, $taginfo['id']))
			$n_succeeds++;
		else
			$n_errors++;
	if ($n_errors)
		return buildRedirectURL (__FUNCTION__, 'ERR1', array ($n_succeeds, $n_errors));
	else
		return buildRedirectURL (__FUNCTION__, 'OK', array ($n_succeeds));
}

$msgcode['rollTags']['OK'] = 67;
$msgcode['rollTags']['ERR'] = 149;
function rollTags ()
{
	assertStringArg ('sum', TRUE);
	assertUIntArg ('realsum');
	if ($_REQUEST['sum'] != $_REQUEST['realsum'])
		return buildRedirectURL (__FUNCTION__, 'ERR');
	// Even if the user requested an empty tag list, don't bail out, but process existing
	// tag chains with "zero" extra. This will make sure, that the stuff processed will
	// have its chains refined to "normal" form.
	$extratags = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	$n_ok = 0;
	// Minimizing the extra chain early, so that tag rebuilder doesn't have to
	// filter out the same tag again and again. It will have own noise to cancel.
	$extrachain = getExplicitTagsOnly (buildTagChainFromIds ($extratags));
	foreach (listCells ('rack', $_REQUEST['row_id']) as $rack)
	{
		if (rebuildTagChainForEntity ('rack', $rack['id'], $extrachain))
			$n_ok++;
		amplifyCell ($rack);
		foreach ($rack['mountedObjects'] as $object_id)
			if (rebuildTagChainForEntity ('object', $object_id, $extrachain))
				$n_ok++;
	}
	return buildRedirectURL (__FUNCTION__, 'OK', array ($n_ok));
}

$msgcode['changeMyPassword']['OK'] = 51;
$msgcode['changeMyPassword']['ERR1'] = 150;
$msgcode['changeMyPassword']['ERR2'] = 151;
$msgcode['changeMyPassword']['ERR3'] = 152;
$msgcode['changeMyPassword']['ERR4'] = 153;
function changeMyPassword ()
{
	global $remote_username, $user_auth_src;
	if ($user_auth_src != 'database')
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	assertStringArg ('oldpassword');
	assertStringArg ('newpassword1');
	assertStringArg ('newpassword2');
	$remote_userid = getUserIDByUsername ($remote_username);
	$userinfo = spotEntity ('user', $remote_userid);
	if ($userinfo['user_password_hash'] != sha1 ($_REQUEST['oldpassword']))
		return buildRedirectURL (__FUNCTION__, 'ERR2');
	if ($_REQUEST['newpassword1'] != $_REQUEST['newpassword2'])
		return buildRedirectURL (__FUNCTION__, 'ERR3');
	if (FALSE !== commitUpdateUserAccount ($remote_userid, $userinfo['user_name'], $userinfo['user_realname'], sha1 ($_REQUEST['newpassword1'])))
		return buildRedirectURL (__FUNCTION__, 'OK');
	else
		return buildRedirectURL (__FUNCTION__, 'ERR4');
}

$msgcode['saveRackCode']['OK'] = 43;
$msgcode['saveRackCode']['ERR1'] = 154;
$msgcode['saveRackCode']['ERR2'] = 155;
function saveRackCode ()
{
	assertStringArg ('rackcode');
	// For the test to succeed, unescape LFs, strip CRs.
	$newcode = dos2unix ($_REQUEST['rackcode']);
	$parseTree = getRackCode ($newcode);
	if ($parseTree['result'] != 'ACK')
		return buildRedirectURL (__FUNCTION__, 'ERR1', array ($parseTree['load']));
	if (FALSE !== saveScript ('RackCode', $newcode))
	{
		saveScript ('RackCodeCache', base64_encode (serialize ($parseTree)));
		return buildRedirectURL (__FUNCTION__, 'OK');
	}
	return buildRedirectURL (__FUNCTION__, 'ERR2');
}

$msgcode['setPortVLAN']['ERR'] = 164;
// This handler's context is pre-built, but not authorized. It is assumed, that the
// handler will take existing context and before each commit check authorization
// on the base chain plus necessary context added.
function setPortVLAN ()
{
	assertUIntArg ('portcount');
	try
	{
		$data = getSwitchVLANs ($_REQUEST['object_id']);
	}
	catch (RTGatewayError $re)
	{
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($re->getMessage()));
	}
	list ($vlanlist, $portlist) = $data;
	// Here we just build up 1 set command for the gateway with all of the ports
	// included. The gateway is expected to filter unnecessary changes silently
	// and to provide a list of responses with either error or success message
	// for each of the rest.
	$nports = $_REQUEST['portcount'];
	$prefix = 'set ';
	$log = emptyLog();
	$setcmd = '';
	for ($i = 0; $i < $nports; $i++)
	{
		genericAssertion ('portname_' . $i, 'string');
		genericAssertion ('vlanid_' . $i, 'string');
		if ($_REQUEST['portname_' . $i] != $portlist[$i]['portname'])
			throw new InvalidRequestArgException ('portname_' . $i, $_REQUEST['portname_' . $i], 'expected to be ' . $portlist[$i]['portname']);
		if
		(
			$_REQUEST['vlanid_' . $i] == $portlist[$i]['vlanid'] ||
			$portlist[$i]['vlaind'] == 'TRUNK'
		)
			continue;
		$portname = $_REQUEST['portname_' . $i];
		$oldvlanid = $portlist[$i]['vlanid'];
		$newvlanid = $_REQUEST['vlanid_' . $i];
		if
		(
			!permitted (NULL, NULL, NULL, array (array ('tag' => '$fromvlan_' . $oldvlanid))) or
			!permitted (NULL, NULL, NULL, array (array ('tag' => '$tovlan_' . $newvlanid)))
		)
		{
			$log = mergeLogs ($log, oneLiner (159, array ($portname, $oldvlanid, $newvlanid)));
			continue;
		}
		$setcmd .= $prefix . $portname . '=' . $newvlanid;
		$prefix = ';';
	}
	// Feed the gateway and interpret its (non)response.
	if ($setcmd == '')
		$log = mergeLogs ($log, oneLiner (201));
	else
	{
		try
		{
			$log = mergeLogs ($log, setSwitchVLANs ($_REQUEST['object_id'], $setcmd));
		}
		catch (RTGatewayError $e)
		{
			$log = mergeLogs ($log, oneLiner (164, $e->getMessage()));
		}
	}
	return buildWideRedirectURL ($log);
}

$msgcode['submitSLBConfig']['OK'] = 66;
$msgcode['submitSLBConfig']['ERR'] = 164;
function submitSLBConfig ()
{
	$newconfig = buildLVSConfig ($_REQUEST['object_id']);
	try
	{
		gwSendFileToObject ($_REQUEST['object_id'], 'slbconfig', html_entity_decode ($newconfig, ENT_QUOTES, 'UTF-8'));
	}
	catch (RTGatewayError $e)
	{
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($e->getMessage()));
	}
	return buildRedirectURL (__FUNCTION__, 'OK', array ('slbconfig'));
}

$msgcode['addRack']['OK'] = 51;
$msgcode['addRack']['ERR2'] = 172;
function addRack ()
{
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	if (isset ($_REQUEST['got_data']))
	{
		assertStringArg ('rack_name');
		assertUIntArg ('rack_height1');
		assertStringArg ('rack_comment', TRUE);
		commitAddRack ($_REQUEST['rack_name'], $_REQUEST['rack_height1'], $_REQUEST['row_id'], $_REQUEST['rack_comment'], $taglist);
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['rack_name']));
	}
	elseif (isset ($_REQUEST['got_mdata']))
	{
		assertUIntArg ('rack_height2');
		assertStringArg ('rack_names', TRUE);
		$log = emptyLog();
		// copy-and-paste from renderAddMultipleObjectsForm()
		$names1 = explode ("\n", $_REQUEST['rack_names']);
		$names2 = array();
		foreach ($names1 as $line)
		{
			$parts = explode ('\r', $line);
			reset ($parts);
			if (!strlen ($parts[0]))
				continue;
			else
				$names2[] = rtrim ($parts[0]);
		}
		global $msgcode;
		foreach ($names2 as $cname)
		{
			commitAddRack ($cname, $_REQUEST['rack_height2'], $_REQUEST['row_id'], '', $taglist);
			$log['m'][] = array ('c' => $msgcode[__FUNCTION__]['OK'], 'a' => array ($cname));
		}
		return buildWideRedirectURL ($log);
	}
	else
		return buildRedirectURL (__FUNCTION__, 'ERR2');
}

$msgcode['deleteRack']['OK'] = 6;
$msgcode['deleteRack']['ERR'] = 100;
$msgcode['deleteRack']['ERR1'] = 206;
function deleteRack ()
{
	assertUIntArg ('rack_id');
	$rackData = spotEntity ('rack', $_REQUEST['rack_id']);
	amplifyCell ($rackData);
	if (count ($rackData['mountedObjects']))
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	if (TRUE !== commitDeleteRack ($_REQUEST['rack_id']))
		return buildRedirectURL (__FUNCTION__, 'ERR', array(), 'rackspace', 'default');
	return buildRedirectURL (__FUNCTION__, 'OK', array ($rackData['name']), 'rackspace', 'default');
}

$msgcode['updateRack']['OK'] = 7;
$msgcode['updateRack']['ERR'] = 109;
function updateRack ()
{
	assertUIntArg ('rack_row_id');
	assertUIntArg ('rack_height');
	assertStringArg ('rack_name');
	assertStringArg ('rack_comment', TRUE);

	global $sic;
	usePreparedUpdateBlade ('Rack', array ('thumb_data' => NULL), array ('id' => $sic['rack_id']));
	if (TRUE === commitUpdateRack ($_REQUEST['rack_id'], $_REQUEST['rack_name'], $_REQUEST['rack_height'], $_REQUEST['rack_row_id'], $_REQUEST['rack_comment']))
		return buildRedirectURL (__FUNCTION__, 'OK', array ($_REQUEST['rack_name']));
	else
		return buildRedirectURL (__FUNCTION__, 'ERR');
}

function updateRackDesign ()
{
	$rackData = spotEntity ('rack', $_REQUEST['rack_id']);
	amplifyCell ($rackData);
	applyRackDesignMask($rackData);
	markupObjectProblems ($rackData);
	$response = processGridForm ($rackData, 'A', 'F');
	return buildWideRedirectURL (array($response));
}

function updateRackProblems ()
{
	$rackData = spotEntity ('rack', $_REQUEST['rack_id']);
	amplifyCell ($rackData);
	applyRackProblemMask($rackData);
	markupObjectProblems ($rackData);
	$response = processGridForm ($rackData, 'F', 'U');
	return buildWideRedirectURL (array($response));
}

function querySNMPData ()
{
	assertStringArg ('community', TRUE);

	$snmpsetup = array ();
	if ($_REQUEST['community'] != '')
		$snmpsetup['community'] = $_REQUEST['community'];
	else
	{
		assertStringArg ('sec_name');
		assertStringArg ('sec_level');
		assertStringArg ('auth_protocol');
		assertStringArg ('auth_passphrase', TRUE);
		assertStringArg ('priv_protocol');
		assertStringArg ('priv_passphrase', TRUE);

		$snmpsetup['sec_name'] = $_REQUEST['sec_name'];
		$snmpsetup['sec_level'] = $_REQUEST['sec_level'];
		$snmpsetup['auth_protocol'] = $_REQUEST['auth_protocol'];
		$snmpsetup['auth_passphrase'] = $_REQUEST['auth_passphrase'];
		$snmpsetup['priv_protocol'] = $_REQUEST['priv_protocol'];
		$snmpsetup['priv_passphrase'] = $_REQUEST['priv_passphrase'];
	}
	return doSNMPmining ($_REQUEST['object_id'], $snmpsetup);
}

$msgcode['addFileWithoutLink']['OK'] = 5;
$msgcode['addFileWithoutLink']['ERR2'] = 110;
// File-related functions
function addFileWithoutLink ()
{
	assertStringArg ('comment', TRUE);

	// Make sure the file can be uploaded
	if (get_cfg_var('file_uploads') != 1)
		throw new RackTablesError ('file uploads not allowed, change "file_uploads" parameter in php.ini', RackTablesError::MISCONFIGURED);

	$fp = fopen($_FILES['file']['tmp_name'], 'rb');
	global $sic;
	if (FALSE === commitAddFile ($_FILES['file']['name'], $_FILES['file']['type'], $fp, $sic['comment']))
		return buildRedirectURL (__FUNCTION__, 'ERR2');
	if (isset ($_REQUEST['taglist']))
		produceTagsForLastRecord ('file', $_REQUEST['taglist']);
	return buildRedirectURL (__FUNCTION__, 'OK', array (htmlspecialchars ($_FILES['file']['name'])));
}

$msgcode['addFileToEntity']['OK'] = 5;
$msgcode['addFileToEntity']['ERR2'] = 181;
$msgcode['addFileToEntity']['ERR3'] = 110;
function addFileToEntity ()
{
	global $pageno, $etype_by_pageno;
	if (!isset ($etype_by_pageno[$pageno]))
		throw new RackTablesError ('key not found in etype_by_pageno', RackTablesError::INTERNAL);
	$realm = $etype_by_pageno[$pageno];
	$entity_id = getBypassValue();
	assertStringArg ('comment', TRUE);

	// Make sure the file can be uploaded
	if (get_cfg_var('file_uploads') != 1)
		return buildRedirectURL (__FUNCTION__, 'ERR2');

	$fp = fopen($_FILES['file']['tmp_name'], 'rb');
	global $sic;
	if (FALSE === commitAddFile ($_FILES['file']['name'], $_FILES['file']['type'], $fp, $sic['comment']))
		return buildRedirectURL (__FUNCTION__, 'ERR3');
	if (FALSE === usePreparedInsertBlade ('FileLink', array ('file_id' => lastInsertID(), 'entity_type' => $realm, 'entity_id' => $entity_id)))
		return buildRedirectURL (__FUNCTION__, 'ERR3');

	return buildRedirectURL (__FUNCTION__, 'OK', array (htmlspecialchars ($_FILES['file']['name'])));
}

$msgcode['linkFileToEntity']['OK'] = 71;
$msgcode['linkFileToEntity']['ERR2'] = 110;
function linkFileToEntity ()
{
	assertUIntArg ('file_id');
	global $pageno, $etype_by_pageno, $sic;
	if (!isset ($etype_by_pageno[$pageno]))
		throw new RackTablesError ('key not found in etype_by_pageno', RackTablesError::INTERNAL);

	$fi = spotEntity ('file', $sic['file_id']);
	if (FALSE === usePreparedInsertBlade ('FileLink', array ('file_id' => $sic['file_id'], 'entity_type' => $etype_by_pageno[$pageno], 'entity_id' => getBypassValue())))
		return buildRedirectURL (__FUNCTION__, 'ERR2');

	return buildRedirectURL (__FUNCTION__, 'OK', array (htmlspecialchars ($fi['name'])));
}

$msgcode['replaceFile']['OK'] = 7;
$msgcode['replaceFile']['ERR1'] = 181;
$msgcode['replaceFile']['ERR2'] = 207;
$msgcode['replaceFile']['ERR3'] = 109;
function replaceFile ()
{
	global $sic;

	// Make sure the file can be uploaded
	if (get_cfg_var('file_uploads') != 1)
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	$shortInfo = spotEntity ('file', $sic['file_id']);

	$fp = fopen($_FILES['file']['tmp_name'], 'rb');
	if ($fp === FALSE)
		return buildRedirectURL (__FUNCTION__, 'ERR2');
	if (FALSE === commitReplaceFile ($sic['file_id'], $fp))
		return buildRedirectURL (__FUNCTION__, 'ERR3');

	usePreparedExecuteBlade
	(
		'UPDATE File SET thumbnail = NULL WHERE id = ?',
		$sic['file_id']
	);

	return buildRedirectURL (__FUNCTION__, 'OK', array (htmlspecialchars ($shortInfo['name'])));
}

$msgcode['unlinkFile']['OK'] = 72;
$msgcode['unlinkFile']['ERR'] = 111;
function unlinkFile ()
{
	assertUIntArg ('link_id');
	return buildRedirectURL (__FUNCTION__, commitUnlinkFile ($_REQUEST['link_id']) === FALSE ? 'ERR' : 'OK');
}

$msgcode['deleteFile']['OK'] = 6;
$msgcode['deleteFile']['ERR'] = 111;
function deleteFile ()
{
	assertUIntArg ('file_id');
	$shortInfo = spotEntity ('file', $_REQUEST['file_id']);
	$error = commitDeleteFile ($_REQUEST['file_id']);

	if ($error != '')
		return buildRedirectURL (__FUNCTION__, 'ERR', array ($error));

	return buildRedirectURL (__FUNCTION__, 'OK', array (htmlspecialchars ($shortInfo['name'])));
}

$msgcode['updateFileText']['OK'] = 7;
$msgcode['updateFileText']['ERR1'] = 179;
$msgcode['updateFileText']['ERR2'] = 155;
function updateFileText ()
{
	assertStringArg ('mtime_copy');
	assertStringArg ('file_text', TRUE); // it's Ok to save empty
	$shortInfo = spotEntity ('file', $_REQUEST['file_id']);
	if ($shortInfo['mtime'] != $_REQUEST['mtime_copy'])
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	global $sic;
	if (FALSE === commitReplaceFile ($sic['file_id'], $sic['file_text']))
		return buildRedirectURL (__FUNCTION__, 'ERR2');
	return buildRedirectURL (__FUNCTION__, 'OK', array (htmlspecialchars ($shortInfo['name'])));
}

$msgcode['addPortInterfaceCompat']['OK'] = 48;
$msgcode['addPortInterfaceCompat']['ERR'] = 110;
function addPortInterfaceCompat ()
{
	assertUIntArg ('iif_id');
	assertUIntArg ('oif_id');
	if (commitSupplementPIC ($_REQUEST['iif_id'], $_REQUEST['oif_id']))
		return buildRedirectURL (__FUNCTION__, 'OK');
	return buildRedirectURL (__FUNCTION__, 'ERR');
}

$ifcompatpack = array
(
	'1000cwdm80' => array (1209, 1210, 1211, 1212, 1213, 1214, 1215, 1216),
	'1000dwdm80' => array // ITU channels 20~61
	(
		1217, 1218, 1219, 1220, 1221, 1222, 1223, 1224, 1225, 1226,
		1227, 1228, 1229, 1230, 1231, 1232, 1233, 1234, 1235, 1236,
		1237, 1238, 1239, 1240, 1241, 1242, 1243, 1244, 1245, 1246,
		1247, 1248, 1249, 1250, 1251, 1252, 1253, 1254, 1255, 1256,
		1257, 1258
	),
	'10000dwdm80' => array // same channels for 10GE
	(
		1259, 1260, 1261, 1262, 1263, 1264, 1265, 1266, 1267, 1268,
		1269, 1270, 1271, 1272, 1273, 1274, 1275, 1276, 1277, 1278,
		1279, 1280, 1281, 1282, 1283, 1284, 1285, 1286, 1287, 1288,
		1289, 1290, 1291, 1292, 1293, 1294, 1295, 1296, 1297, 1298,
		1299, 1300
	),
);

$msgcode['addPortInterfaceCompatPack']['OK'] = 44;
function addPortInterfaceCompatPack ()
{
	genericAssertion ('standard', 'enum/wdmstd');
	genericAssertion ('iif_id', 'iif');
	global $ifcompatpack;
	$ngood = $nbad = 0;
	foreach ($ifcompatpack[$_REQUEST['standard']] as $oif_id)
		if (commitSupplementPIC ($_REQUEST['iif_id'], $oif_id))
			$ngood++;
		else
			$nbad++;
	return buildRedirectURL (__FUNCTION__, 'OK', array ($nbad, $ngood));
}

$msgcode['delPortInterfaceCompatPack']['OK'] = 44;
$msgcode['delPortInterfaceCompatPack']['ERR'] = 123;
function delPortInterfaceCompatPack ()
{
	assertStringArg ('standard');
	assertUIntArg ('iif_id');
	global $ifcompatpack, $sic;
	if (!array_key_exists ($sic['standard'], $ifcompatpack) or !array_key_exists ($sic['iif_id'], getPortIIFOptions()))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	$ngood = $nbad = 0;
	foreach ($ifcompatpack[$sic['standard']] as $oif_id)
		if (usePreparedDeleteBlade ('PortInterfaceCompat', array ('iif_id' => $sic['iif_id'], 'oif_id' => $oif_id)))
			$ngood++;
		else
			$nbad++;
	return buildRedirectURL (__FUNCTION__, 'OK', array ($nbad, $ngood));
}

$msgcode['add8021QOrder']['OK'] = 48;
$msgcode['add8021QOrder']['ERR'] = 110;
function add8021QOrder ()
{
	assertUIntArg ('vdom_id');
	assertUIntArg ('object_id');
	assertUIntArg ('vst_id');
	global $sic;
	$result = usePreparedExecuteBlade
	(
		'INSERT INTO VLANSwitch (domain_id, object_id, template_id, last_change, out_of_sync) ' .
		'VALUES (?, ?, ?, NOW(), "yes")',
		array ($sic['vdom_id'], $sic['object_id'], $sic['vst_id'])
	);
	return buildRedirectURL (__FUNCTION__, $result !== FALSE ? 'OK' : 'ERR');
}

$msgcode['del8021QOrder']['OK'] = 49;
$msgcode['del8021QOrder']['ERR'] = 111;
function del8021QOrder ()
{
	assertUIntArg ('object_id');
	assertUIntArg ('vdom_id');
	assertUIntArg ('vst_id');
	global $sic;
	$result = usePreparedDeleteBlade ('VLANSwitch', array ('object_id' => $sic['object_id']));
	$focus_hints = array
	(
		'prev_objid' => $_REQUEST['object_id'],
		'prev_vstid' => $_REQUEST['vst_id'],
		'prev_vdid' => $_REQUEST['vdom_id'],
	);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR', array(), NULL, NULL, $focus_hints);
}

$msgcode['delVLANDescription']['OK'] = 49;
$msgcode['delVLANDescription']['ERR1'] = 105;
$msgcode['delVLANDescription']['ERR2'] = 111;
function delVLANDescription ()
{
	assertUIntArg ('vlan_id');
	global $sic;
	if ($sic['vlan_id'] == VLAN_DFL_ID)
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	$result = commitReduceVLANDescription ($sic['vdom_id'], $sic['vlan_id']);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR2');
}

$msgcode['updVLANDescription']['OK'] = 51;
$msgcode['updVLANDescription']['ERR1'] = 105;
function updVLANDescription ()
{
	assertUIntArg ('vlan_id');
	assertStringArg ('vlan_type');
	assertStringArg ('vlan_descr', TRUE);
	global $sic;
	if ($sic['vlan_id'] == VLAN_DFL_ID)
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	usePreparedUpdateBlade
	(
		'VLANDescription',
		array
		(
			'vlan_descr' => !mb_strlen ($sic['vlan_descr']) ? NULL : $sic['vlan_descr'],
			'vlan_type' => $sic['vlan_type'],
		),
		array
		(
			'domain_id' => $sic['vdom_id'],
			'vlan_id' => $sic['vlan_id'],
		)
	);
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['createVLANDomain']['OK'] = 48;
$msgcode['createVLANDomain']['ERR'] = 110;
function createVLANDomain ()
{
	assertStringArg ('vdom_descr');
	global $sic;
	$result = usePreparedInsertBlade
	(
		'VLANDomain',
		array
		(
			'description' => $sic['vdom_descr'],
		)
	);
	$result = $result and usePreparedInsertBlade
	(
		'VLANDescription',
		array
		(
			'domain_id' => lastInsertID(),
			'vlan_id' => VLAN_DFL_ID,
			'vlan_type' => 'compulsory',
			'vlan_descr' => 'default',
		)
	);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['destroyVLANDomain']['OK'] = 49;
$msgcode['destroyVLANDomain']['ERR'] = 111;
function destroyVLANDomain ()
{
	assertUIntArg ('vdom_id');
	global $sic;
	$result = FALSE !== usePreparedDeleteBlade ('VLANDomain', array ('id' => $sic['vdom_id']));
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['save8021QPorts']['OK1'] = 63;
$msgcode['save8021QPorts']['OK2'] = 41;
$msgcode['save8021QPorts']['ERR2'] = 109;
function save8021QPorts ()
{
	global $sic, $dbxlink;
	assertUIntArg ('mutex_rev', TRUE); // counts from 0
	assertStringArg ('form_mode');
	if ($sic['form_mode'] != 'save' and $sic['form_mode'] != 'duplicate')
		throw new InvalidRequestArgException ('form_mode', $sic['form_mode']);
	$extra = array();
	$dbxlink->beginTransaction();
	try
	{
		if (NULL === $vswitch = getVLANSwitchInfo ($sic['object_id'], 'FOR UPDATE'))
			throw new InvalidArgException ('object_id', $object_id, 'VLAN domain is not set for this object');
		if ($vswitch['mutex_rev'] != $sic['mutex_rev'])
			throw new InvalidRequestArgException ('mutex_rev', $sic['mutex_rev'], 'expired form data');
		$after = $before = apply8021QOrder ($vswitch['template_id'], getStored8021QConfig ($sic['object_id'], 'desired'));
		$changes = array();
		switch ($sic['form_mode'])
		{
		case 'save':
			assertUIntArg ('nports');
			if ($sic['nports'] == 1)
			{
				assertStringArg ('pn_0');
				$extra = array ('port_name' => $sic['pn_0']);
			}
			for ($i = 0; $i < $sic['nports']; $i++)
			{
				assertStringArg ('pn_' . $i);
				assertStringArg ('pm_' . $i);
				// An access port only generates form input for its native VLAN,
				// which we derive allowed VLAN list from.
				$native = isset ($sic['pnv_' . $i]) ? $sic['pnv_' . $i] : 0;
				switch ($sic["pm_${i}"])
				{
				case 'trunk':
#				assertArrayArg ('pav_' . $i);
					$allowed = isset ($sic['pav_' . $i]) ? $sic['pav_' . $i] : array();
					break;
				case 'access':
					if ($native == 'same')
						continue 2;
					assertUIntArg ('pnv_' . $i);
					$allowed = array ($native);
					break;
				default:
					throw new InvalidRequestArgException ("pm_${i}", $_REQUEST["pm_${i}"], 'unknown port mode');
				}
				$changes[$sic['pn_' . $i]] = array
				(
					'mode' => $sic['pm_' . $i],
					'allowed' => $allowed,
					'native' => $native,
				);
			}
			break;
		case 'duplicate':
			assertStringArg ('from_port');
#			assertArrayArg ('to_ports');
			if (!array_key_exists ($sic['from_port'], $before))
				throw new InvalidArgException ('from_port', $sic['from_port'], 'this port does not exist');
			foreach ($sic['to_ports'] as $tpn)
				if (!array_key_exists ($tpn, $before))
					throw new InvalidArgException ('to_ports[]', $tpn, 'this port does not exist');
				elseif ($tpn != $sic['from_port'])
					$changes[$tpn] = $before[$sic['from_port']];
			break;
		}
		$domain_vlanlist = getDomainVLANs ($vswitch['domain_id']);
		$changes = filter8021QChangeRequests
		(
			$domain_vlanlist,
			$before,
			apply8021QOrder ($vswitch['template_id'], $changes)
		);
		$changes = authorize8021QChangeRequests ($before, $changes);
		foreach ($changes as $port_name => $port)
			$after[$port_name] = $port;
		$new_uplinks = filter8021QChangeRequests ($domain_vlanlist, $after, produceUplinkPorts ($domain_vlanlist, $after));
		$npulled = replace8021QPorts ('desired', $vswitch['object_id'], $before, $changes);
		$nsaved_uplinks = replace8021QPorts ('desired', $vswitch['object_id'], $before, $new_uplinks);
	}
	catch (Exception $e)
	{
		$dbxlink->rollBack();
		return buildRedirectURL (__FUNCTION__, 'ERR2', array(), NULL, NULL, $extra);
	}
	if ($npulled + $nsaved_uplinks)
		$result = usePreparedExecuteBlade
		(
			'UPDATE VLANSwitch SET mutex_rev=mutex_rev+1, last_change=NOW(), out_of_sync="yes" WHERE object_id=?',
			array ($sic['object_id'])
		);
	$dbxlink->commit();
	$log = oneLiner (63, array ($npulled + $nsaved_uplinks));
	if ($nsaved_uplinks)
	{
		initiateUplinksReverb ($vswitch['object_id'], $new_uplinks);
		$log = mergeLogs ($log, oneLiner (41));
	}
	if ($npulled + $nsaved_uplinks > 0 and getConfigVar ('8021Q_INSTANT_DEPLOY') == 'yes')
	{
		try
		{
			if (FALSE === $done = exec8021QDeploy ($sic['object_id'], TRUE))
				$log = mergeLogs ($log, oneLiner (191));
			else
				$log = mergeLogs ($log, oneLiner (63, array ($done)));
		}
		catch (Exception $e)
		{
			$log = mergeLogs ($log, oneLiner (109));
		}
	}
	return buildWideRedirectURL ($log, NULL, NULL, $extra);
}

$msgcode['bindVLANtoIPv4']['OK'] = 48;
$msgcode['bindVLANtoIPv4']['ERR'] = 110;
function bindVLANtoIPv4 ()
{
	assertUIntArg ('id'); // network id
	global $sic;
	$result = commitSupplementVLANIPv4 ($sic['vlan_ck'], $sic['id']);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['bindVLANtoIPv6']['OK'] = 48;
$msgcode['bindVLANtoIPv6']['ERR'] = 110;
function bindVLANtoIPv6 ()
{
	assertUIntArg ('id'); // network id
	global $sic;
	$result = commitSupplementVLANIPv6 ($sic['vlan_ck'], $_REQUEST['id']);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['unbindVLANfromIPv4']['OK'] = 49;
$msgcode['unbindVLANfromIPv4']['ERR'] = 111;
function unbindVLANfromIPv4 ()
{
	assertUIntArg ('id'); // network id
	global $sic;
	$result = commitReduceVLANIPv4 ($sic['vlan_ck'], $sic['id']);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['unbindVLANfromIPv6']['OK'] = 49;
$msgcode['unbindVLANfromIPv6']['ERR'] = 111;
function unbindVLANfromIPv6 ()
{
	assertUIntArg ('id'); // network id
	global $sic;
	$result = commitReduceVLANIPv6 ($sic['vlan_ck'], $sic['id']);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['process8021QSyncRequest']['OK'] = 63;
$msgcode['process8021QSyncRequest']['ERR'] = 191;
function process8021QSyncRequest ()
{
	// behave depending on current operation: exec8021QPull or exec8021QPush
	global $sic, $op;
	if (FALSE === $done = exec8021QDeploy ($sic['object_id'], $op == 'exec8021QPush'))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	return buildRedirectURL (__FUNCTION__, 'OK', array ($done));
}

$msgcode['process8021QRecalcRequest']['CHANGED'] = 87;
$msgcode['process8021QRecalcRequest']['NO_CHANGES'] = 300;
$msgcode['process8021QRecalcRequest']['ERR'] = 157;
function process8021QRecalcRequest ()
{
	global $sic;
	if (! permitted (NULL, NULL, NULL, array (array ('tag' => '$op_recalc8021Q'))))
		return buildRedirectURL (__FUNCTION__, 'ERR');
	$counters = recalc8021QPorts ($sic['object_id']);
	if ($counters['ports'])
		return buildRedirectURL (__FUNCTION__, 'CHANGED', array ($counters['ports'], $counters['switches']));
	else
		return buildRedirectURL (__FUNCTION__, 'NO_CHANGES', array ('No changes were made'));
}

$msgcode['resolve8021QConflicts']['OK'] = 63;
$msgcode['resolve8021QConflicts']['ERR1'] = 179;
$msgcode['resolve8021QConflicts']['ERR2'] = 109;
function resolve8021QConflicts ()
{
	global $sic, $dbxlink;
	assertUIntArg ('mutex_rev', TRUE); // counts from 0
	assertUIntArg ('nrows');
	// Divide submitted radio buttons into 3 groups:
	// left (saved version wins)
	// asis (ignore)
	// right (running version wins)
	$F = array();
	for ($i = 0; $i < $sic['nrows']; $i++)
	{
		if (!array_key_exists ("i_${i}", $sic))
			continue;
		// let's hope other inputs are in place
		switch ($sic["i_${i}"])
		{
		case 'left':
		case 'right':
			$F[$sic["pn_${i}"]] = array
			(
				'mode' => $sic["rm_${i}"],
				'allowed' => $sic["ra_${i}"],
				'native' => $sic["rn_${i}"],
				'decision' => $sic["i_${i}"],
			);
			break;
		default:
			// don't care
		}
	}
	$dbxlink->beginTransaction();
	try
	{
		if (NULL === $vswitch = getVLANSwitchInfo ($sic['object_id'], 'FOR UPDATE'))
			throw new InvalidArgException ('object_id', $sic['object_id'], 'VLAN domain is not set for this object');
		if ($vswitch['mutex_rev'] != $sic['mutex_rev'])
			throw new InvalidRequestArgException ('mutex_rev', $sic['mutex_rev'], 'expired form (table data has changed)');
		$D = getStored8021QConfig ($vswitch['object_id'], 'desired');
		$C = getStored8021QConfig ($vswitch['object_id'], 'cached');
		$R = getRunning8021QConfig ($vswitch['object_id']);
		$plan = get8021QSyncOptions ($vswitch, $D, $C, $R['portdata']);
		$ndone = 0;
		foreach ($F as $port_name => $port)
		{
			if (!array_key_exists ($port_name, $plan))
				continue;
			elseif ($plan[$port_name]['status'] == 'merge_conflict')
			{
				// for R neither mutex nor revisions can be emulated, but revision change can be
				if (!same8021QConfigs ($port, $R['portdata'][$port_name]))
					throw new InvalidRequestArgException ("port ${port_name}", '(hidden)', 'expired form (switch data has changed)');
				if ($port['decision'] == 'right') // D wins, frame R by writing value of R to C
					$ndone += upd8021QPort ('cached', $vswitch['object_id'], $port_name, $port);
				elseif ($port['decision'] == 'left') // R wins, cross D up
					$ndone += upd8021QPort ('cached', $vswitch['object_id'], $port_name, $D[$port_name]);
				// otherwise there was no decision made
			}
			elseif
			(
				$plan[$port_name]['status'] == 'delete_conflict' or
				$plan[$port_name]['status'] == 'martian_conflict'
			)
			{
				if ($port['decision'] == 'left') // confirm deletion of local copy
					$ndone += del8021QPort ($vswitch['object_id'], $port_name);
			}
			// otherwise ignore a decision, which doesn't address a conflict
		}
	}
	catch (InvalidRequestArgException $e)
	{
		$dbxlink->rollBack();
		return buildRedirectURL (__FUNCTION__, 'ERR1');
	}
	catch (Exception $e)
	{
		$dbxlink->rollBack();
		return buildRedirectURL (__FUNCTION__, 'ERR2');
	}
	$dbxlink->commit();
	return buildRedirectURL (__FUNCTION__, 'OK', array ($ndone));
}

$msgcode['addVLANSwitchTemplate']['OK'] = 48;
$msgcode['addVLANSwitchTemplate']['ERR'] = 110;
function addVLANSwitchTemplate()
{
	assertStringArg ('vst_descr');
	global $sic;
	$max_local_vlans = NULL;
	if (array_key_exists ('vst_maxvlans', $sic) && mb_strlen ($sic['vst_maxvlans']))
	{
		assertUIntArg ('vst_maxvlans');
		$max_local_vlans = $sic['vst_maxvlans'];
	}
	$result = usePreparedInsertBlade
	(
		'VLANSwitchTemplate',
		array
		(
			'max_local_vlans' => $max_local_vlans,
			'description' => $sic['vst_descr'],
		)
	);
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['delVLANSwitchTemplate']['OK'] = 49;
$msgcode['delVLANSwitchTemplate']['ERR'] = 111;
function delVLANSwitchTemplate()
{
	assertUIntArg ('vst_id');
	global $sic;
	$result = FALSE !== usePreparedDeleteBlade ('VLANSwitchTemplate', array ('id' => $sic['vst_id']));
	return buildRedirectURL (__FUNCTION__, $result ? 'OK' : 'ERR');
}

$msgcode['cloneVSTRule']['OK'] = 48;
$msgcode['cloneVSTRule']['ERR'] = 179;
function cloneVSTRule()
{
	global $dbxlink;
	$message = '';
	assertUIntArg ('mutex_rev', TRUE);
	$dst_vst = getVLANSwitchTemplate ($_REQUEST['vst_id']);
	if ($dst_vst['mutex_rev'] != $_REQUEST['mutex_rev'])
		$message = "User ${dst_vst['saved_by']} saved this template after you started to edit it. Please concern differencies";
	else
	{
		assertUIntArg ('from_id');
		$src_vst = getVLANSwitchTemplate ($_REQUEST['from_id']);
		if (! commitUpdateVSTRules ($_REQUEST['vst_id'], $src_vst['rules']))
			$message = 'DB error';
	}
	$result = !(BOOL) $message;
	if ($result)
		$message = 'Supplement succeeded';
	return buildWideRedirectURL (array (array ('code' => $result ? 'success' : 'error', 'message' => $message)));
}

$msgcode['updVSTRule']['OK'] = 43;
function updVSTRule()
{
	// this is used for making throwing an invalid argument exception easier.
	function updVSTRule_get_named_param ($name, $haystack, &$last_used_name)
	{
		$last_used_name = $name;
		return isset ($haystack[$name]) ? $haystack[$name] : NULL;
	}

	global $port_role_options, $sic;
	assertUIntArg ('mutex_rev', TRUE);
	genericAssertion ('template_json', 'json');
	$data = json_decode ($sic['template_json'], TRUE);
	$rule_no = 0;
	try
	{
		$last_field = '';
		foreach ($data as $rule)
		{
			$rule_no++;
			if
			(
				! isInteger (updVSTRule_get_named_param ('rule_no', $rule, $last_field))
				or ! isPCRE (updVSTRule_get_named_param ('port_pcre', $rule, $last_field))
				or NULL === updVSTRule_get_named_param ('port_role', $rule, $last_field)
				or ! array_key_exists (updVSTRule_get_named_param ('port_role', $rule, $last_field), $port_role_options)
				or NULL ===  updVSTRule_get_named_param ('wrt_vlans', $rule, $last_field)
				or ! preg_match ('/^[ 0-9\-,]*$/',  updVSTRule_get_named_param ('wrt_vlans', $rule, $last_field))
				or NULL ===  updVSTRule_get_named_param ('description', $rule, $last_field)
			)
				throw new InvalidRequestArgException ($last_field, $rule[$last_field], "rule #$rule_no");
		}
		commitUpdateVSTRules ($_REQUEST['vst_id'], $_REQUEST['mutex_rev'], $data);
	}
	catch (Exception $e)
	{
		// Every case, which is soft-processed in process.php, will have the working copy available for a retry.
		if ($e instanceof InvalidRequestArgException or $e instanceof RTDatabaseError)
			$_SESSION['vst_edited'] = $data;
		throw $e;
	}
	return buildRedirectURL (__FUNCTION__, 'OK');
}

$msgcode['importDPData']['OK'] = 44;
function importDPData()
{
	global $sic;
	assertUIntArg ('nports');
	$nignored = $ndone = 0;
	$POIFC = getPortOIFCompat();
	for ($i = 0; $i < $sic['nports']; $i++)
		if (array_key_exists ("do_${i}", $sic))
		{
			assertUIntArg ("pid1_${i}");
			assertUIntArg ("pid2_${i}");
			$porta = getPortInfo ($_REQUEST["pid1_${i}"]);
			$portb = getPortInfo ($_REQUEST["pid2_${i}"]);
			if
			(
				$porta['linked'] or
				$portb['linked'] or
				($porta['object_id'] != $sic['object_id'] and $portb['object_id'] != $sic['object_id'])
			)
			{
				$nignored++;
				continue;
			}
			foreach ($POIFC as $item)
				if ($item['type1'] == $porta['oif_id'] and $item['type2'] == $portb['oif_id'])
				{
					linkPorts ($_REQUEST["pid1_${i}"], $_REQUEST["pid2_${i}"]);
					$ndone++;
					continue 2; // next port
				}
			$nignored++;
		}
	return buildRedirectURL (__FUNCTION__, 'OK', array ($nignored, $ndone));
}

$msgcode['addObjectlog']['OK'] = 0;
function addObjectlog ()
{
	assertStringArg ('logentry');
	global $remote_username, $sic;
	$oi = spotEntity ('object', $sic['object_id']);
	usePreparedExecuteBlade ('INSERT INTO ObjectLog SET object_id=?, user=?, date=NOW(), content=?', array ($sic['object_id'], $remote_username, $sic['logentry']));
	$ob_url = makeHref (array ('page' => 'object', 'tab' => 'objectlog', 'object_id' => $sic['object_id']));
	return buildRedirectURL (__FUNCTION__, 'OK', array ("Log entry for <a href=" . ${ob_url} . ">${oi['dname']}</a> added by ${remote_username}"));
}

function getOpspec()
{
	global $pageno, $tabno, $op, $opspec_list;
	if (!array_key_exists ($pageno . '-' . $tabno . '-' . $op, $opspec_list))
		throw new RackTablesError ('key not found in opspec_list', RackTablesError::INTERNAL);
	$ret = $opspec_list[$pageno . '-' . $tabno . '-' . $op];
	if
	(
		!array_key_exists ('table', $ret)
		or !array_key_exists ('action', $ret)
		// add further checks here
	)
		throw new RackTablesError ('malformed array structure in opspec_list', RackTablesError::INTERNAL);
	return $ret;
}

function tableHandler()
{
	$opspec = getOpspec();
	global $sic;
	$columns = array();
	foreach (array ('arglist', 'set_arglist', 'where_arglist') as $listname)
		foreach ($opspec[$listname] as $argspec)
		{
			genericAssertion ($argspec['url_argname'], $argspec['assertion']);
			// "table_colname" is normally used for an override, if it is not
			// set, use the URL argument name
			$table_colname = array_key_exists ('table_colname', $argspec) ?
				$argspec['table_colname'] :
				$argspec['url_argname'];
			$arg_value = $sic[$argspec['url_argname']];
			if
			(
				($argspec['assertion'] == 'uint0' and $arg_value == 0)
				or ($argspec['assertion'] == 'string0' and $arg_value == '')
			)
				switch (TRUE)
				{
				case !array_key_exists ('if_empty', $argspec): // no action requested
					break;
				case $argspec['if_empty'] == 'NULL':
					$arg_value = NULL;
					break;
				default:
					throw new InvalidArgException ('opspec', '(malformed array structure)', '"if_empty" not recognized');
				}
			$columns[$listname][$table_colname] = $arg_value;
		}
	switch ($opspec['action'])
	{
	case 'INSERT':
		$retcode = TRUE === usePreparedInsertBlade ($opspec['table'], $columns['arglist']) ? 48 : 110;
		break;
	case 'DELETE':
		$conjunction = array_key_exists ('conjunction', $opspec) ? $opspec['conjunction'] : 'AND';
		$retcode = FALSE !== usePreparedDeleteBlade ($opspec['table'], $columns['arglist'], $conjunction) ? 49 : 111;
		break;
	case 'UPDATE':
		usePreparedUpdateBlade
		(
			$opspec['table'],
			$columns['set_arglist'],
			$columns['where_arglist'],
			array_key_exists ('conjunction', $opspec) ? $opspec['conjunction'] : 'AND'
		);
		$retcode = 51;
		break;
	default:
		throw new InvalidArgException ('opspec/action', $opspec['action']);
	}
	return buildWideRedirectURL (oneLiner ($retcode));
}

?>
