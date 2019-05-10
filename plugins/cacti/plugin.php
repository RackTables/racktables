<?php

function plugin_cacti_info ()
{
	return array
	(
		'name' => 'cacti',
		'longname' => 'Cacti',
		'version' => '1.0',
		'home_url' => 'http://www.racktables.org/'
	);
}

function plugin_cacti_init ()
{
	global $interface_requires, $opspec_list, $page, $tab, $trigger;
	$tab['object']['cacti'] = 'Cacti Graphs';
	registerTabHandler ('object', 'cacti', 'renderObjectCactiGraphs');
	$trigger['object']['cacti'] = 'triggerCactiGraphs';
	registerOpHandler ('object', 'cacti', 'add', 'tableHandler');
	registerOpHandler ('object', 'cacti', 'del', 'tableHandler');

	$page['cacti']['title'] = 'Cacti';
	$page['cacti']['parent'] = 'config';
	$tab['cacti']['default'] = 'View';
	$tab['cacti']['servers'] = 'Manage servers';
	registerTabHandler ('cacti', 'default', 'renderCactiConfig');
	registerTabHandler ('cacti', 'servers', 'renderCactiServersEditor');
	registerOpHandler ('cacti', 'servers', 'add', 'tableHandler');
	registerOpHandler ('cacti', 'servers', 'del', 'tableHandler');
	registerOpHandler ('cacti', 'servers', 'upd', 'tableHandler');
	$interface_requires['cacti-*'] = 'interface-config.php';

	registerHook ('dispatchImageRequest_hook', 'plugin_cacti_dispatchImageRequest');
	registerHook ('resetObject_hook', 'plugin_cacti_resetObject');
	registerHook ('resetUIConfig_hook', 'plugin_cacti_resetUIConfig');

	$opspec_list['object-cacti-add'] = array
	(
		'table' => 'CactiGraph',
		'action' => 'INSERT',
		'arglist' => array
		(
			array ('url_argname' => 'object_id', 'assertion' => 'natural'),
			array ('url_argname' => 'server_id', 'assertion' => 'natural'),
			array ('url_argname' => 'graph_id', 'assertion' => 'natural'),
			array ('url_argname' => 'caption', 'assertion' => 'string0'),
		),
	);
	$opspec_list['object-cacti-del'] = array
	(
		'table' => 'CactiGraph',
		'action' => 'DELETE',
		'arglist' => array
		(
			array ('url_argname' => 'object_id', 'assertion' => 'natural'),
			array ('url_argname' => 'server_id', 'assertion' => 'natural'),
			array ('url_argname' => 'graph_id', 'assertion' => 'natural'),
		),
	);
	$opspec_list['cacti-servers-add'] = array
	(
		'table' => 'CactiServer',
		'action' => 'INSERT',
		'arglist' => array
		(
			array ('url_argname' => 'base_url', 'assertion' => 'string'),
			array ('url_argname' => 'username', 'assertion' => 'string0'),
			array ('url_argname' => 'password', 'assertion' => 'string0'),
		),
	);
	$opspec_list['cacti-servers-del'] = array
	(
		'table' => 'CactiServer',
		'action' => 'DELETE',
		'arglist' => array
		(
			array ('url_argname' => 'id', 'assertion' => 'natural'),
		),
	);
	$opspec_list['cacti-servers-upd'] = array
	(
		'table' => 'CactiServer',
		'action' => 'UPDATE',
		'set_arglist' => array
		(
			array ('url_argname' => 'base_url', 'assertion' => 'string'),
			array ('url_argname' => 'username', 'assertion' => 'string0'),
			array ('url_argname' => 'password', 'assertion' => 'string0'),
		),
		'where_arglist' => array
		(
			array ('url_argname' => 'id', 'assertion' => 'natural'),
		),
	);

	global $plugin_cacti_fkeys;
	$plugin_cacti_fkeys = array (
		array ('fkey_name' => 'CactiGraph-FK-object_id', 'table_name' => 'CactiGraph'),
		array ('fkey_name' => 'CactiGraph-FK-server_id', 'table_name' => 'CactiGraph'),
	);
}

function plugin_cacti_install ()
{
	if (extension_loaded ('curl') === FALSE)
		throw new RackTablesError ('cURL PHP module is not installed', RackTablesError::MISCONFIGURED);

	global $dbxlink;

	$dbxlink->query (
"CREATE TABLE `CactiServer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `base_url` char(255) DEFAULT NULL,
  `username` char(64) DEFAULT NULL,
  `password` char(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB");

	$dbxlink->query	(
"CREATE TABLE `CactiGraph` (
  `object_id` int(10) unsigned NOT NULL,
  `server_id` int(10) unsigned NOT NULL,
  `graph_id` int(10) unsigned NOT NULL,
  `caption`  char(255) DEFAULT NULL,
  PRIMARY KEY (`object_id`,`server_id`,`graph_id`),
  KEY `graph_id` (`graph_id`),
  KEY `server_id` (`server_id`),
  CONSTRAINT `CactiGraph-FK-server_id` FOREIGN KEY (`server_id`) REFERENCES `CactiServer` (`id`),
  CONSTRAINT `CactiGraph-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB");

	addConfigVar ('CACTI_LISTSRC', 'false', 'string', 'yes', 'no', 'no', 'List of object with Cacti graphs');
	addConfigVar ('CACTI_RRA_ID', '1', 'uint', 'no', 'no', 'yes', 'RRA ID for Cacti graphs displayed in RackTables');

	return TRUE;
}

function plugin_cacti_uninstall ()
{
	deleteConfigVar ('CACTI_LISTSRC');
	deleteConfigVar ('CACTI_RRA_ID');

	global $dbxlink;
	$dbxlink->query	("DROP TABLE `CactiGraph`");
	$dbxlink->query	("DROP TABLE `CactiServer`");

	return TRUE;
}

function plugin_cacti_upgrade ()
{
	return TRUE;
}

function plugin_cacti_dispatchImageRequest ()
{
	global $pageno, $tabno;

	if ($_REQUEST['img'] == 'cactigraph')
	{
		$pageno = 'object';
		$tabno = 'cacti';
		fixContext ();
		assertPermission ();
		$graph_id = genericAssertion ('graph_id', 'natural');
		if (! array_key_exists ($graph_id, getCactiGraphsForObject (getBypassValue())))
			throw new InvalidRequestArgException ('graph_id', $graph_id);
		proxyCactiRequest (genericAssertion ('server_id', 'natural'), $graph_id);
		return TRUE;
	}
}

function plugin_cacti_resetObject ($object_id)
{
	usePreparedDeleteBlade ('CactiGraph', array ('object_id' => $object_id));
}

function plugin_cacti_resetUIConfig ()
{
	setConfigVar ('CACTI_LISTSRC', 'false');
	setConfigVar ('CACTI_RRA_ID', '1');
}

function getCactiGraphsForObject ($object_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT server_id, graph_id, caption FROM CactiGraph WHERE object_id = ? ORDER BY server_id, graph_id',
		array ($object_id)
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC), 'graph_id');
}

function getCactiServers ()
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, base_url, username, password, COUNT(graph_id) AS num_graphs ' .
		'FROM CactiServer AS CS LEFT JOIN CactiGraph AS CG ON CS.id = CG.server_id GROUP BY id'
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}

function renderCactiConfig ()
{
	$columns = array
	(
		array ('th_text' => 'base URL', 'row_key' => 'base_url'),
		array ('th_text' => 'username', 'row_key' => 'username'),
		array ('th_text' => 'graph(s)', 'row_key' => 'num_graphs', 'td_class' => 'tdright'),
	);
	$servers = getCactiServers ();
	startPortlet ('Cacti servers (' . count ($servers) . ')');
	renderTableViewer ($columns, $servers);
	finishPortlet ();
}

function renderCactiServersEditor ()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr>' .
			'<td>' . getImageHREF ('create', 'add a new server', TRUE) . '</td>' .
			'<td><input type=text size=48 name=base_url></td>' .
			'<td><input type=text size=24 name=username></td>' .
			'<td><input type=password size=24 name=password></td>' .
			'<td>&nbsp;</td>' .
			'<td>' . getImageHREF ('create', 'add a new server', TRUE) . '</td>' .
			'</tr></form>';
	}
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr>' .
		'<th>&nbsp;</th>' .
		'<th>base URL</th>' .
		'<th>username</th>' .
		'<th>password</th>' .
		'<th>graph(s)</th>' .
		'<th>&nbsp;</th>' .
		'</tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ();
	foreach (getCactiServers () as $server)
	{
		printOpFormIntro ('upd', array ('id' => $server['id']));
		echo '<tr><td>';
		if ($server['num_graphs'])
			printImageHREF ('nodestroy', 'cannot delete, graphs exist');
		else
			echo getOpLink (array ('op' => 'del', 'id' => $server['id']), '', 'destroy', 'delete this server');
		echo '</td>';
		echo '<td><input type=text size=48 name=base_url value="' . htmlspecialchars ($server['base_url'], ENT_QUOTES, 'UTF-8') . '"></td>';
		echo '<td><input type=text size=24 name=username value="' . htmlspecialchars ($server['username'], ENT_QUOTES, 'UTF-8') . '"></td>';
		echo '<td><input type=password size=24 name=password value="' . htmlspecialchars ($server['password'], ENT_QUOTES, 'UTF-8') . '"></td>';
		echo "<td class=tdright>${server['num_graphs']}</td>";
		echo '<td>' . getImageHREF ('save', 'update this server', TRUE) . '</td>';
		echo '</tr></form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ();
	echo '</table>';
}

function renderObjectCactiGraphs ($object_id)
{
	function printNewItemTR ($servers)
	{
		$options = array();
		foreach ($servers as $server)
			$options[$server['id']] = "${server['id']}: ${server['base_url']}";
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
		echo "<br/><br/>\n";
	}
	if (! extension_loaded ('curl'))
		throw new RackTablesError ('The PHP cURL extension is not loaded.', RackTablesError::MISCONFIGURED);

	$servers = getCactiServers ();
	startPortlet ('Cacti Graphs');
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes' && permitted ('object', 'cacti', 'add'))
		printNewItemTR ($servers);
	echo "<table cellspacing=\"0\" cellpadding=\"10\" align=\"center\" width=\"50%\">\n";
	foreach (getCactiGraphsForObject ($object_id) as $graph_id => $graph)
	{
		$cacti_url = $servers[$graph['server_id']]['base_url'];
		$text = "(graph ${graph_id} on server ${graph['server_id']})";
		echo "<tr><td>";
		echo "<a href='${cacti_url}/graph.php?action=view&local_graph_id=${graph_id}&rra_id=all' target='_blank'>";
		echo "<img src='index.php?module=image&img=cactigraph&object_id=${object_id}&server_id=${graph['server_id']}&graph_id=${graph_id}' alt='${text}' title='${text}'></a></td><td>";
		if(permitted ('object', 'cacti', 'del'))
			echo getOpLink (array ('op' => 'del', 'server_id' => $graph['server_id'], 'graph_id' => $graph_id), '', 'Cut', 'Unlink graph', 'need-confirmation');
		echo "&nbsp; &nbsp;${graph['caption']}";
		echo "</td></tr>\n";
	}
	echo "</table>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes' && permitted ('object', 'cacti', 'add'))
		printNewItemTR ($servers);
	finishPortlet ();
}

function proxyCactiRequest ($server_id, $graph_id)
{
	$ret = array ();
	$servers = getCactiServers ();
	if (! array_key_exists ($server_id, $servers))
		throw new InvalidRequestArgException ('server_id', $server_id);
	$cacti_url = $servers[$server_id]['base_url'];
	$url = "${cacti_url}/graph_image.php?action=view&local_graph_id=${graph_id}&rra_id=" . getConfigVar ('CACTI_RRA_ID');
	$postvars = 'action=login&login_username=' . $servers[$server_id]['username'];
	$postvars .= '&login_password=' . $servers[$server_id]['password'];

	$session = curl_init ();

	// Initial options up here so a specific type can override them
	curl_setopt ($session, CURLOPT_FOLLOWLOCATION, FALSE);
	curl_setopt ($session, CURLOPT_TIMEOUT, 10);
	curl_setopt ($session, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt ($session, CURLOPT_URL, $url);

	if (isset ($_SESSION['CACTICOOKIE'][$cacti_url]))
		curl_setopt ($session, CURLOPT_COOKIE, $_SESSION['CACTICOOKIE'][$cacti_url]);

	// Request the image
	$ret['contents'] = curl_exec ($session);
	$ret['type'] = curl_getinfo ($session, CURLINFO_CONTENT_TYPE);
	$ret['size'] = curl_getinfo ($session, CURLINFO_SIZE_DOWNLOAD);

	// Not an image, probably the login page
	if (preg_match ('/^text\/html.*/i', $ret['type']))
	{
		// Request to set the cookies
		curl_setopt ($session, CURLOPT_HEADER, TRUE);
		curl_setopt ($session, CURLOPT_COOKIE, "");	// clear the old cookie
		$headers = curl_exec ($session);

		// Get the cookies from the headers
		preg_match ('/Set-Cookie: ([^;]*)/i', $headers, $cookies);
		array_shift ($cookies); // Remove 'Set-Cookie: ...' value
		$cookie_header = implode (";", $cookies);
		$_SESSION['CACTICOOKIE'][$cacti_url] = $cookie_header; // store for later use by this user

		// CSRF security in 0.8.8h, regexp version
		if (preg_match ("/sid:([a-z0-9,]+)\"/", $ret['contents'], $csf_output))
			if (array_key_exists (1, $csf_output))
				$postvars .= "&__csrf_magic=$csf_output[1]";

		// POST Login
		curl_setopt ($session, CURLOPT_COOKIE, $cookie_header);
		curl_setopt ($session, CURLOPT_HEADER, FALSE);
		curl_setopt ($session, CURLOPT_POST, TRUE);
		curl_setopt ($session, CURLOPT_POSTFIELDS, $postvars);
		curl_exec ($session);

		// Request the image
		curl_setopt ($session, CURLOPT_HTTPGET, TRUE);
		$ret['contents'] = curl_exec ($session);
		$ret['type'] = curl_getinfo ($session, CURLINFO_CONTENT_TYPE);
		$ret['size'] = curl_getinfo ($session, CURLINFO_SIZE_DOWNLOAD);
	}

	curl_close ($session);

	if ($ret['type'] != NULL)
		header ("Content-Type: {$ret['type']}");
	if ($ret['size'] > 0)
		header ("Content-Length: {$ret['size']}");

	echo $ret['contents'];
}

function triggerCactiGraphs ()
{
	if (! count (getCactiServers ()))
		return '';
	if
	(
		count (getCactiGraphsForObject (getBypassValue ())) or
		considerConfiguredConstraint (spotEntity ('object', getBypassValue ()), 'CACTI_LISTSRC')
	)
		return 'std';
	return '';
}
