<?php

function plugin_munin_info ()
{
	return array
	(
		'name' => 'munin',
		'longname' => 'Munin',
		'version' => '1.0',
		'home_url' => 'http://www.racktables.org/'
	);
}

function plugin_munin_init ()
{
	global $interface_requires, $opspec_list, $page, $tab, $trigger;
	$tab['object']['munin'] = 'Munin Graphs';
	registerTabHandler ('object', 'munin', 'renderObjectMuninGraphs');
	$trigger['object']['munin'] = 'triggerMuninGraphs';
	$ophandler['object']['munin']['add'] = 'tableHandler';
	$ophandler['object']['munin']['del'] = 'tableHandler';

	$page['munin']['title'] = 'Munin';
	$page['munin']['parent'] = 'config';
	$tab['munin']['default'] = 'View';
	$tab['munin']['servers'] = 'Manage servers';
	registerTabHandler ('munin', 'default', 'renderMuninConfig');
	registerTabHandler ('munin', 'servers', 'renderMuninServersEditor');
	registerOpHandler ('munin', 'servers', 'add', 'tableHandler');
	registerOpHandler ('munin', 'servers', 'del', 'tableHandler');
	registerOpHandler ('munin', 'servers', 'upd', 'tableHandler');
	$interface_requires['munin-*'] = 'interface-config.php';

	registerHook ('dispatchImageRequest_hook', 'plugin_munin_dispatchImageRequest');
	registerHook ('resetObject_hook', 'plugin_munin_resetObject');
	registerHook ('resetUIConfig_hook', 'plugin_munin_resetUIConfig');

	$opspec_list['object-munin-add'] = array
	(
		'table' => 'MuninGraph',
		'action' => 'INSERT',
		'arglist' => array
		(
			array ('url_argname' => 'object_id', 'assertion' => 'natural'),
			array ('url_argname' => 'server_id', 'assertion' => 'natural'),
			array ('url_argname' => 'graph', 'assertion' => 'string'),
			array ('url_argname' => 'caption', 'assertion' => 'string0'),
		),
	);
	$opspec_list['object-munin-del'] = array
	(
		'table' => 'MuninGraph',
		'action' => 'DELETE',
		'arglist' => array
		(
			array ('url_argname' => 'object_id', 'assertion' => 'natural'),
			array ('url_argname' => 'server_id', 'assertion' => 'natural'),
			array ('url_argname' => 'graph', 'assertion' => 'string'),
		),
	);
	$opspec_list['munin-servers-add'] = array
	(
		'table' => 'MuninServer',
		'action' => 'INSERT',
		'arglist' => array
		(
			array ('url_argname' => 'base_url', 'assertion' => 'string')
		),
	);
	$opspec_list['munin-servers-del'] = array
	(
		'table' => 'MuninServer',
		'action' => 'DELETE',
		'arglist' => array
		(
			array ('url_argname' => 'id', 'assertion' => 'natural'),
		),
	);
	$opspec_list['munin-servers-upd'] = array
	(
		'table' => 'MuninServer',
		'action' => 'UPDATE',
		'set_arglist' => array
		(
			array ('url_argname' => 'base_url', 'assertion' => 'string'),
		),
		'where_arglist' => array
		(
			array ('url_argname' => 'id', 'assertion' => 'natural'),
		),
	);

	global $plugin_munin_fkeys;
	$plugin_munin_fkeys = array (
		array ('fkey_name' => 'MuninGraph-FK-object_id', 'table_name' => 'MuninGraph'),
		array ('fkey_name' => 'MuninGraph-FK-server_id', 'table_name' => 'MuninGraph'),
	);
}

function plugin_munin_install ()
{
	if (extension_loaded ('curl') === FALSE)
		throw new RackTablesError ('cURL PHP module is not installed', RackTablesError::MISCONFIGURED);

	global $dbxlink;

	$dbxlink->query(
"CREATE TABLE `MuninServer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `base_url` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB");

	$dbxlink->query	(
"CREATE TABLE `MuninGraph` (
  `object_id` int(10) unsigned NOT NULL,
  `server_id` int(10) unsigned NOT NULL,
  `graph` char(255) NOT NULL,
  `caption`  char(255) DEFAULT NULL,
  PRIMARY KEY (`object_id`,`server_id`,`graph`),
  KEY `server_id` (`server_id`),
  KEY `graph` (`graph`),
  CONSTRAINT `MuninGraph-FK-server_id` FOREIGN KEY (`server_id`) REFERENCES `MuninServer` (`id`),
  CONSTRAINT `MuninGraph-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB");

	addConfigVar ('MUNIN_LISTSRC', 'false', 'string', 'yes', 'no', 'no', 'List of object with Munin graphs');

	return TRUE;
}

function plugin_munin_uninstall ()
{
	deleteConfigVar ('MUNIN_LISTSRC');

	global $dbxlink;
	$dbxlink->query	("DROP TABLE `MuninGraph`");
	$dbxlink->query	("DROP TABLE `MuninServer`");

	return TRUE;
}

function plugin_munin_upgrade ()
{
	return TRUE;
}

function plugin_munin_dispatchImageRequest ()
{
	global $pageno, $tabno;

	if ($_REQUEST['img'] == 'muningraph')
	{
		$pageno = 'object';
		$tabno = 'munin';
		fixContext ();
		assertPermission ();
		$graph = genericAssertion ('graph', 'string');
		if (! array_key_exists ($graph, getMuninGraphsForObject (getBypassValue())))
			throw new InvalidRequestArgException ('graph', $graph);
		proxyMuninRequest (genericAssertion ('server_id', 'natural'), $graph);
	}
	return TRUE;
}

function plugin_munin_resetObject ($object_id)
{
	usePreparedDeleteBlade ('MuninGraph', array ('object_id' => $object_id));
}

function plugin_munin_resetUIConfig ()
{
	setConfigVar ('MUNIN_LISTSRC', 'false');
}

function getMuninGraphsForObject ($object_id)
{
	$result = usePreparedSelectBlade
	(
		'SELECT server_id, graph, caption FROM MuninGraph WHERE object_id = ? ORDER BY server_id, graph',
		array ($object_id)
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC), 'graph');
}

// Split object's FQDN (or the common name if FQDN is not set) into the
// hostname and domain name in Munin convention (using the first period as the
// separator), and return the pair. Throw an exception on error.
function getMuninNameAndDomain ($object_id)
{
	$o = spotEntity ('object', $object_id);
	$hd = $o['name'];
	// FQDN overrides the common name for Munin purposes.
	$attrs = getAttrValues ($object_id);
	if (array_key_exists (3, $attrs) && $attrs[3]['value'] != '')
		$hd = $attrs[3]['value'];
	if (2 != count ($ret = preg_split ('/\./', $hd, 2)))
		throw new InvalidArgException ('$object_id', $object_id, 'the name is not in the host.do.ma.in format');
	return $ret;
}

function getMuninServers ()
{
	$result = usePreparedSelectBlade
	(
		'SELECT id, base_url, COUNT(MG.object_id) AS num_graphs ' .
		'FROM MuninServer AS MS LEFT JOIN MuninGraph AS MG ON MS.id = MG.server_id GROUP BY id'
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}

function renderMuninConfig ()
{
	$columns = array
	(
		array ('th_text' => 'base URL', 'row_key' => 'base_url', 'td_maxlen' => 150),
		array ('th_text' => 'graph(s)', 'row_key' => 'num_graphs', 'td_class' => 'tdright'),
	);
	$servers = getMuninServers ();
	startPortlet ('Munin servers (' . count ($servers) . ')');
	renderTableViewer ($columns, $servers);
	finishPortlet ();
}

function renderMuninServersEditor ()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr>';
		echo '<td>' . getImageHREF ('create', 'add a new server', TRUE) . '</td>';
		echo '<td><input type=text size=48 name=base_url></td>';
		echo '<td>&nbsp;</td>';
		echo '<td>' . getImageHREF ('create', 'add a new server', TRUE) . '</td>';
		echo '</tr></form>';
	}
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>&nbsp;</th><th>base URL</th><th>graph(s)</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ();
	foreach (getMuninServers () as $server)
	{
		printOpFormIntro ('upd', array ('id' => $server['id']));
		echo '<tr><td>';
		if ($server['num_graphs'])
			printImageHREF ('nodestroy', 'cannot delete, graphs exist');
		else
			echo getOpLink (array ('op' => 'del', 'id' => $server['id']), '', 'destroy', 'delete this server');
		echo '</td>';
		echo '<td><input type=text size=48 name=base_url value="' . htmlspecialchars ($server['base_url'], ENT_QUOTES, 'UTF-8') . '"></td>';
		echo "<td class=tdright>${server['num_graphs']}</td>";
		echo '<td>' . getImageHREF ('save', 'update this server', TRUE) . '</td>';
		echo '</tr></form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ();
	echo '</table>';
}

function renderObjectMuninGraphs ($object_id)
{
	function printNewItem ($servers)
	{
		$options = array();
		foreach ($servers as $server)
			$options[$server['id']] = "${server['id']}: ${server['base_url']}";
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
		echo "<br/><br/>\n";
	}
	if (! extension_loaded ('curl'))
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

	$servers = getMuninServers ();
	startPortlet ('Munin Graphs');
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItem ($servers);
	echo "<table cellspacing=\"0\" cellpadding=\"10\" align=\"center\" width=\"50%\">\n";

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
		echo "</td></tr>\n";
	}
	echo "</table>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItem ($servers);
	finishPortlet ();
}

function proxyMuninRequest ($server_id, $graph)
{
	try
	{
		list ($host, $domain) = getMuninNameAndDomain (getBypassValue ());
	}
	catch (InvalidArgException $e)
	{
		throw new RTImageError ('munin_graph');
	}

	$ret = array ();
	$servers = getMuninServers ();
	if (! array_key_exists ($server_id, $servers))
		throw new InvalidRequestArgException ('server_id', $server_id);
	$munin_url = $servers[$server_id]['base_url'];
	$url = "${munin_url}/${domain}/${host}.${domain}/${graph}-day.png";

	$session = curl_init ();

	// Initial options up here so a specific type can override them
	curl_setopt ($session, CURLOPT_FOLLOWLOCATION, FALSE);
	curl_setopt ($session, CURLOPT_TIMEOUT, 10);
	curl_setopt ($session, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt ($session, CURLOPT_URL, $url);

	if (isset($_SESSION['MUNINCOOKIE'][$munin_url]))
		curl_setopt ($session, CURLOPT_COOKIE, $_SESSION['MUNINCOOKIE'][$munin_url]);

	// Request the image
	$ret['contents'] = curl_exec ($session);
	$ret['type'] = curl_getinfo ($session, CURLINFO_CONTENT_TYPE);
	$ret['size'] = curl_getinfo ($session, CURLINFO_SIZE_DOWNLOAD);

	curl_close ($session);

	if ($ret['type'] != NULL)
		header ("Content-Type: {$ret['type']}");
	if ($ret['size'] > 0)
		header ("Content-Length: {$ret['size']}");

	echo $ret['contents'];
}

function triggerMuninGraphs()
{
	if (! count (getMuninServers ()))
		return '';
	if
	(
		count (getMuninGraphsForObject (getBypassValue ())) or
		considerConfiguredConstraint (spotEntity ('object', getBypassValue ()), 'MUNIN_LISTSRC')
	)
		return 'std';
	return '';
}
