<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

function renderInstallerHTML()
{
$stepfunc[1] = 'not_already_installed';
$stepfunc[2] = 'platform_is_ok';
$stepfunc[3] = 'init_config';
$stepfunc[4] = 'check_config_access';
$stepfunc[5] = 'init_database_static';
$stepfunc[6] = 'init_database_dynamic';
$stepfunc[7] = 'congrats';

if (isset ($_REQUEST['step']))
	$step = $_REQUEST['step'];
else
	$step = 1;

if ($step > count ($stepfunc))
{
	$root = (empty ($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
	$root .= isset ($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']=='80'?'':$_SERVER['SERVER_PORT']));
	// "Since PHP 4.3.0, you will often get a slash or a dot back from
	// dirname() in situations where the older functionality would have given
	// you the empty string."
	// "On Windows, both slash (/) and backslash (\) are used as directory
	// separator character."
	$root .= strtr (dirname ($_SERVER['PHP_SELF']), '\\', '/');
	if (substr ($root, -1) != '/')
		$root .= '/';
	header ("Location: ${root}");
	exit;
}
$title = "RackTables installation: step ${step} of " . count ($stepfunc);
header ('Content-Type: text/html; charset=UTF-8');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title><?php echo $title; ?></title>
<style type="text/css">
.tdleft {
	text-align: left;
}

.trok {
	background-color: #80FF80;
}

.trwarning {
	background-color: #FFFF80;
}

.trerror {
	background-color: #FF8080;
}
</style>
</head>
<body>
<center>
<?php
echo "<h1>${title}</h1><p>";

echo "</p><form method=post>\n";
$testres = $stepfunc[$step] ();
if ($testres)
{
	$next_step = $step + 1;
	echo "<br><input type=submit value='proceed'>";
}
else
{
	$next_step = $step;
	echo "<br><input type=submit value='retry'>";
}
echo "<input type=hidden name=step value='${next_step}'>\n";

?>
</form>
</center>
</body>
</html>

<?php
}

// Check if the software is already installed.
function not_already_installed()
{
	global $found_secret_file, $pdo_dsn;
	if ($found_secret_file && isset ($pdo_dsn))
	{
		echo 'Your configuration file exists and seems to hold necessary data already.<br>';
		return FALSE;
	}
	else
	{
		echo 'There seem to be no existing installation here, I am going to setup one now.<br>';
		return TRUE;
	}
}

// Check that we can write to configuration file.
// If so, ask for DB connection paramaters and test
// the connection. Neither save the parameters nor allow
// going further until we succeed with the given
// credentials.
function init_config ()
{
	function print_form
	(
		$use_tcp = TRUE,
		$tcp_host = 'localhost',
		$tcp_port = '',
		$unix_socket = '/var/lib/mysql/mysql.sock',
		$database = 'racktables_db',
		$username = 'racktables_user',
		$password = ''
	)
	{
		echo "<input type=hidden name=save_config value=1>\n";
		echo '<h3>Server-side MySQL setup of the database:</h3><div align=left><pre class=trok>';
		echo "mysql&gt;\nCREATE DATABASE racktables_db CHARACTER SET utf8 COLLATE utf8_general_ci;\n";
		echo "CREATE USER racktables_user@localhost IDENTIFIED BY 'MY_SECRET_PASSWORD';\n";
		echo "GRANT ALL PRIVILEGES ON racktables_db.* TO racktables_user@localhost;\n</pre></div>";
		echo '<table>';
		echo '<tr><td><label for=conn_tcp>TCP connection</label></td>';
		echo '<td><input type=radio name=conn value=conn_tcp id=conn_tcp' . ($use_tcp ? ' checked' : '') . '></td></tr>';
		echo '<tr><td><label for=conn_unix>UNIX socket</label></td>';
		echo '<td><input type=radio name=conn value=conn_unix id=conn_unix' . ($use_tcp ? '' : ' checked') . '></td></tr>';
		echo "<tr><td><label for=mysql_host>TCP host:</label></td>";
		echo "<td><input type=text name=mysql_host id=mysql_host value='${tcp_host}'></td></tr>\n";
		echo "<tr><td><label for=mysql_port>TCP port (if not 3306):</label></td>";
		echo "<td><input type=text name=mysql_port id=mysql_port value='${tcp_port}'></td></tr>\n";
		echo "<tr><td><label for=mysql_socket>UNIX socket:</label></td>";
		echo "<td><input type=text name=mysql_socket id=mysql_socket value='${unix_socket}'></td></tr>\n";
		echo "<tr><td><label for=mysql_db>database:</label></td>";
		echo "<td><input type=text name=mysql_db id=mysql_db value='${database}'></td></tr>\n";
		echo "<tr><td><label for=mysql_username>username:</label></td>";
		echo "<td><input type=text name=mysql_username id=mysql_username value='${username}'></td></tr>\n";
		echo "<tr><td><label for=mysql_password>password:</label></td>";
		echo "<td><input type=password name=mysql_password id=mysql_password value='${password}'></td></tr>\n";
		echo '</table>';
	}
	global $path_to_secret_php;
	if (!is_writable ($path_to_secret_php))
	{
		echo "The $path_to_secret_php file is not writable by web-server. Make sure it is.";
		echo "The following commands should suffice:<pre>touch '$path_to_secret_php'; chmod a=rw '$path_to_secret_php'</pre>";
		echo 'Fedora Linux with SELinux may require this file to be owned by specific user (apache) and/or executing "setenforce 0" for the time of installation. ';
		echo 'SELinux may be turned back on with "setenforce 1" command.<br>';
		return FALSE;
	}
	if (! array_key_exists ('save_config', $_REQUEST))
	{
		print_form();
		return FALSE;
	}
	if (empty ($_REQUEST['mysql_db']) || empty ($_REQUEST['mysql_username']))
	{
		print_form
		(
			$_REQUEST['conn'] == 'conn_tcp',
			$_REQUEST['mysql_host'],
			$_REQUEST['mysql_port'],
			$_REQUEST['mysql_socket'],
			$_REQUEST['mysql_db'],
			$_REQUEST['mysql_username'],
			$_REQUEST['mysql_password']
		);
		echo '<h2 class=trerror>Missing database/username parameter!</h2>';
		return FALSE;
	}
	if ($_REQUEST['conn'] == 'conn_tcp' && empty ($_REQUEST['mysql_host']))
	{
		print_form
		(
			$_REQUEST['conn'] == 'conn_tcp',
			$_REQUEST['mysql_host'],
			$_REQUEST['mysql_port'],
			$_REQUEST['mysql_socket'],
			$_REQUEST['mysql_db'],
			$_REQUEST['mysql_username'],
			$_REQUEST['mysql_password']
		);
		echo '<h2 class=trerror>Missing TCP hostname parameter!</h2>';
		return FALSE;
	}
	if ($_REQUEST['conn'] == 'conn_unix' && empty ($_REQUEST['mysql_socket']))
	{
		print_form
		(
			$_REQUEST['conn'] == 'conn_tcp',
			$_REQUEST['mysql_host'],
			$_REQUEST['mysql_port'],
			$_REQUEST['mysql_socket'],
			$_REQUEST['mysql_db'],
			$_REQUEST['mysql_username'],
			$_REQUEST['mysql_password']
		);
		echo '<h2 class=trerror>Missing UNIX socket parameter!</h2>';
		return FALSE;
	}
	# finally OK to make a connection attempt
	$pdo_dsn = 'mysql:';
	switch ($_REQUEST['conn'])
	{
	case 'conn_tcp':
		$pdo_dsn .= 'host=' . $_REQUEST['mysql_host'];
		if (! empty ($_REQUEST['mysql_port']) && $_REQUEST['mysql_port'] != '3306')
			$pdo_dsn .= ';port=' . $_REQUEST['mysql_port'];
		break;
	case 'conn_unix':
		$pdo_dsn .= 'unix_socket=' . $_REQUEST['mysql_socket'];
		break;
	default:
		print_form();
		echo '<h2 class=trerror>form error</h2>';
		return FALSE;
	}
	$pdo_dsn .= ';dbname=' . $_REQUEST['mysql_db'];
	try
	{
		$dbxlink = new PDO ($pdo_dsn, $_REQUEST['mysql_username'], $_REQUEST['mysql_password']);
	}
	catch (PDOException $e)
	{
		print_form
		(
			$_REQUEST['conn'] == 'conn_tcp',
			$_REQUEST['mysql_host'],
			$_REQUEST['mysql_port'],
			$_REQUEST['mysql_socket'],
			$_REQUEST['mysql_db'],
			$_REQUEST['mysql_username'],
			$_REQUEST['mysql_password']
		);
		echo "<h2 class=trerror>Database connection failed. Check parameters and try again.</h2>\n";
		echo "PDO DSN: <tt class=trwarning>${pdo_dsn}</tt><br>";
		return FALSE;
	}

	$conf = fopen ($path_to_secret_php, 'w+');
	if ($conf === FALSE)
	{
		echo "Error: failed to open $path_to_secret_php for writing";
		return FALSE;
	}
	fwrite ($conf, "<?php\n# This file has been generated automatically by RackTables installer.\n");
	fwrite ($conf, "\$pdo_dsn = '${pdo_dsn}';\n");
	fwrite ($conf, "\$db_username = '" . $_REQUEST['mysql_username'] . "';\n");
	fwrite ($conf, "\$db_password = '" . $_REQUEST['mysql_password'] . "';\n\n");
	fwrite ($conf, <<<ENDOFTEXT
# Setting MySQL client buffer size may be required to make downloading work for
# larger files, but it does not work with mysqlnd.
# \$pdo_bufsize = 50 * 1024 * 1024;
# Setting PDO SSL key, cert, and CA will allow a SSL/TLS connection to the MySQL
# DB. Make sure the files are readable by the web server
# \$pdo_ssl_key = '/path/to/ssl/key'
# \$pdo_ssl_cert = '/path/to/ssl/cert'
# \$pdo_ssl_ca = '/path/to/ssl/ca'

\$user_auth_src = 'database';
\$require_local_account = TRUE;
# Default setting is to authenticate users locally, but it is possible to
# employ existing LDAP or Apache user accounts. Check RackTables wiki for
# more information, in particular, this page for LDAP configuration details:
# http://wiki.racktables.org/index.php?title=LDAP

#\$LDAP_options = array
#(
#	'server' => 'localhost',
#	'domain' => 'example.com',
#	'search_attr' => '',
#	'search_dn' => '',
# // The following credentials will be used when searching for the user's DN:
#	'search_bind_rdn' => NULL,
#	'search_bind_password' => NULL,
#	'displayname_attrs' => '',
#	'options' => array (LDAP_OPT_PROTOCOL_VERSION => 3),
#	'use_tls' => 2,         // 0 == don't attempt, 1 == attempt, 2 == require
#);

# For SAML configuration details:
# http://wiki.racktables.org/index.php?title=SAML

#\$SAML_options = array
#(
#	'simplesamlphp_basedir' => '../simplesaml',
#	'sp_profile' => 'default-sp',
#	'usernameAttribute' => 'eduPersonPrincipName',
#	'fullnameAttribute' => 'fullName',
#	'groupListAttribute' => 'memberOf',
#);

# This HTML banner is intended to assist users in dispatching their issues
# to the local tech support service. Its text (in its verbatim form) will
# be appended to assorted error messages visible in user's browser (including
# "not authenticated" message). Beware of placing any sensitive information
# here, it will be readable by unauthorized visitors.
#\$helpdesk_banner = '<B>This RackTables instance is supported by Example Inc. IT helpdesk, dial ext. 1234 to report a problem.</B>';


ENDOFTEXT
);
	fwrite ($conf, "?>\n");
	fclose ($conf);
	echo "The configuration file has been written successfully.<br>";
	return TRUE;
}

function get_process_owner()
{
	// this function requires the posix extention and returns the fallback value otherwise
	if (is_callable ('posix_getpwuid') && is_callable ('posix_geteuid'))
	{
		$user = posix_getpwuid(posix_geteuid());
		if (isset ($user['name']))
			return $user['name'];
	}
	return 'nobody';
}

function check_config_access()
{
	global $path_to_secret_php;
	if (! is_writable ($path_to_secret_php) && is_readable ($path_to_secret_php))
	{
		echo 'The configuration file ownership/permissions seem to be OK.<br>';
		return TRUE;
	}
	$uname = get_process_owner();
	echo 'Please set ownership (<tt>chown</tt>) and/or permissions (<tt>chmod</tt>) ';
	echo "of <tt>${path_to_secret_php}</tt> on the server filesystem as follows:";
	echo '<div align=left><ul>';
	echo '<li>The file MUST NOT be writable by the httpd process.</li>';
	echo '<li>The file MUST be readable by the httpd process.</li>';
	echo '<li>The file should not be readable by anyone except the httpd process.</li>';
	echo '<li>The file should not be writable by anyone.</li>';
	echo '</ul></div>';
	echo 'For example, if httpd runs as user "' . $uname . '" and group "nogroup", commands ';
	echo 'similar to the following may work (though not guaranteed to, please consider ';
	echo 'only as an example):';
	echo "<pre>chown $uname:nogroup secret.php; chmod 440 secret.php</pre>";
	return FALSE;
}

function connect_to_db_or_die ()
{
	try
	{
		connectDB();
	}
	catch (RackTablesError $e)
	{
		die ('Error connecting to the database');
	}
}

function init_database_static ()
{
	connect_to_db_or_die();
	global $dbxlink;
	if (!isInnoDBSupported())
	{
		echo 'InnoDB test failed! Please configure MySQL server properly and retry.';
		return FALSE;
	}
	$result = $dbxlink->query ('show tables');
	$tables = $result->fetchAll (PDO::FETCH_NUM);
	$result->closeCursor();
	unset ($result);
	if (count ($tables))
	{
		echo 'Your database is already holding ' . count ($tables);
		echo ' tables, so I will stop here and let you check it yourself.<br>';
		echo 'There is some important data there probably.<br>';
		return FALSE;
	}
	echo 'Initializing the database...<br>';
	echo '<table border=1>';
	echo "<tr><th>section</th><th>queries</th><th>errors</th></tr>";
	$failures = array();
	foreach (array ('structure', 'dictbase') as $part)
	{
		echo "<tr><td>${part}</td>";
		$nq = $nerrs = 0;
		foreach (get_pseudo_file ($part) as $q)
			try
			{
				$result = $dbxlink->query ($q);
				$nq++;
			}
			catch (PDOException $e)
			{
				$nerrs++;
				$errorInfo = $dbxlink->errorInfo();
				$failures[] = array ($q, $errorInfo[2]);
			}
		echo "<td>${nq}</td><td>${nerrs}</td></tr>\n";
	}
	if (!count ($failures))
		echo "<strong><font color=green>done</font></strong>";
	else
	{
		echo "<strong><font color=red>The following queries failed:</font></strong><br><pre>";
		foreach ($failures as $f)
		{
			list ($q, $i) = $f;
			echo "${q} -- ${i}\n";
		}
	}
	// (re)load dictionary by pure PHP means w/o any external file
	echo "<tr><td>dictionary</td>";
	$nq = $nerrs = 0;
	$dictq = array();
	foreach (reloadDictionary() as $query)
	{
		$nq++;
		if ($dbxlink->exec ($query) === FALSE)
		{
			$nerrs++;
			$errlist[] = $query;
		}
	}
	echo "<td>${nq}</td><td>${nerrs}</td></tr>\n";

	echo '</table>';
	if (isset($errlist) && count ($errlist))
	{
		echo '<pre>The following queries failed:\n';
		foreach ($errlist as $q)
			echo "${q}\n\n";
		echo '</pre>';
		return FALSE;
	}
	return TRUE;
}

function init_database_dynamic ()
{
	connect_to_db_or_die();
	global $dbxlink;
	if (! isset ($_REQUEST['password']) || empty ($_REQUEST['password']))
	{
		$result = $dbxlink->query ('select count(user_id) from UserAccount where user_id = 1');
		$row = $result->fetch (PDO::FETCH_NUM);
		$nrecs = $row[0];
		$result->closeCursor();
		if (!$nrecs)
		{
			echo '<table border=1>';
			echo '<caption>Administrator password not set</caption>';
			echo '<tr><td><input type=password name=password></td></tr>';
			echo '</table>';
		}
		return FALSE;
	}
	else
	{
		// Never send cleartext password over the wire.
		$hash = sha1 ($_REQUEST['password']);
		$query = "INSERT INTO `UserAccount` (`user_id`, `user_name`, `user_password_hash`, `user_realname`) " .
			"VALUES (1,'admin','${hash}','RackTables Administrator')";
		$result = $dbxlink->exec ($query);
		echo "Administrator password has been set successfully.<br>";
		return TRUE;
	}
}

function congrats ()
{
	echo 'Congratulations! RackTables installation is complete. After pressing Proceed you will ';
	echo 'enter the system. Authenticate with <strong>admin</strong> username.<br>RackTables project has a ';
	echo "<a href='http://wiki.racktables.org/index.php?title=RackTablesAdminGuide'>";
	echo "wiki</a> and a ";
	echo "<a href='http://www.freelists.org/list/racktables-users'>mailing list</a> for users. Have fun.<br>";
	return TRUE;
}

function get_pseudo_file ($name)
{
	switch ($name)
	{
	case 'structure':
		$query = array();

		$query[] = "alter database character set utf8 collate utf8_unicode_ci";
		$query[] = "set names 'utf8'";
		$query[] = "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0";

		$query[] = "CREATE TABLE `Atom` (
  `molecule_id` int(10) unsigned default NULL,
  `rack_id` int(10) unsigned default NULL,
  `unit_no` int(10) unsigned default NULL,
  `atom` enum('front','interior','rear') default NULL,
  CONSTRAINT `Atom-FK-molecule_id` FOREIGN KEY (`molecule_id`) REFERENCES `Molecule` (`id`) ON DELETE CASCADE,
  CONSTRAINT `Atom-FK-rack_id` FOREIGN KEY (`rack_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `Attribute` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` enum('string','uint','float','dict','date') default NULL,
  `name` char(64) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `AttributeMap` (
  `objtype_id` int(10) unsigned NOT NULL default '1',
  `attr_id` int(10) unsigned NOT NULL default '1',
  `chapter_id` int(10) unsigned default NULL,
  `sticky` enum('yes','no') default 'no',
  UNIQUE KEY `objtype_id` (`objtype_id`,`attr_id`),
  KEY `attr_id` (`attr_id`),
  KEY `chapter_id` (`chapter_id`),
  CONSTRAINT `AttributeMap-FK-chapter_id` FOREIGN KEY (`chapter_id`) REFERENCES `Chapter` (`id`),
  CONSTRAINT `AttributeMap-FK-attr_id` FOREIGN KEY (`attr_id`) REFERENCES `Attribute` (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `AttributeValue` (
  `object_id` int(10) unsigned NOT NULL,
  -- Default value intentionally breaks the constraint, this blocks
  -- any insertion that doesn't have 'object_tid' on the column list.
  `object_tid` int(10) unsigned NOT NULL default '0',
  `attr_id` int(10) unsigned NOT NULL,
  `string_value` char(255) default NULL,
  `uint_value` int(10) unsigned default NULL,
  `float_value` float default NULL,
  PRIMARY KEY (`object_id`,`attr_id`),
  KEY `attr_id-uint_value` (`attr_id`,`uint_value`),
  KEY `attr_id-string_value` (`attr_id`,`string_value`(12)),
  KEY `id-tid` (`object_id`,`object_tid`),
  KEY `object_tid-attr_id` (`object_tid`,`attr_id`),
  CONSTRAINT `AttributeValue-FK-map` FOREIGN KEY (`object_tid`, `attr_id`) REFERENCES `AttributeMap` (`objtype_id`, `attr_id`),
  CONSTRAINT `AttributeValue-FK-object` FOREIGN KEY (`object_id`, `object_tid`) REFERENCES `Object` (`id`, `objtype_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `CachedPAV` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `CachedPAV-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `CachedPVM` (`object_id`, `port_name`) ON DELETE CASCADE,
  CONSTRAINT `CachedPAV-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `CachedPNV` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  UNIQUE KEY `port_id` (`object_id`,`port_name`),
  CONSTRAINT `CachedPNV-FK-compound` FOREIGN KEY (`object_id`, `port_name`, `vlan_id`) REFERENCES `CachedPAV` (`object_id`, `port_name`, `vlan_id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `CachedPVM` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_mode` enum('access','trunk') NOT NULL default 'access',
  PRIMARY KEY  (`object_id`,`port_name`),
  CONSTRAINT `CachedPVM-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `CactiGraph` (
  `object_id` int(10) unsigned NOT NULL,
  `server_id` int(10) unsigned NOT NULL,
  `graph_id` int(10) unsigned NOT NULL,
  `caption`  char(255) DEFAULT NULL,
  PRIMARY KEY (`object_id`,`server_id`,`graph_id`),
  KEY `graph_id` (`graph_id`),
  KEY `server_id` (`server_id`),
  CONSTRAINT `CactiGraph-FK-server_id` FOREIGN KEY (`server_id`) REFERENCES `CactiServer` (`id`),
  CONSTRAINT `CactiGraph-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `CactiServer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `base_url` char(255) DEFAULT NULL,
  `username` char(64) DEFAULT NULL,
  `password` char(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `Chapter` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sticky` enum('yes','no') default 'no',
  `name` char(128) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `Config` (
  `varname` char(32) NOT NULL,
  `varvalue` text NOT NULL,
  `vartype` enum('string','uint') NOT NULL default 'string',
  `emptyok` enum('yes','no') NOT NULL default 'no',
  `is_hidden` enum('yes','no') NOT NULL default 'yes',
  `is_userdefined` enum('yes','no') NOT NULL default 'no',
  `description` text,
  PRIMARY KEY  (`varname`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `Dictionary` (
  `chapter_id` int(10) unsigned NOT NULL,
  `dict_key` int(10) unsigned NOT NULL auto_increment,
  `dict_sticky` enum('yes','no') DEFAULT 'no',
  `dict_value` char(255) default NULL,
  PRIMARY KEY  (`dict_key`),
  UNIQUE KEY `dict_unique` (`chapter_id`,`dict_value`,`dict_sticky`),
  CONSTRAINT `Dictionary-FK-chapter_id` FOREIGN KEY (`chapter_id`) REFERENCES `Chapter` (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `EntityLink` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_entity_type` enum('location','object','rack','row') NOT NULL,
  `parent_entity_id` int(10) unsigned NOT NULL,
  `child_entity_type` enum('location','object','rack','row') NOT NULL,
  `child_entity_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `EntityLink-unique` (`parent_entity_type`,`parent_entity_id`,`child_entity_type`,`child_entity_id`),
  KEY `EntityLink-compound` (`parent_entity_type`,`child_entity_type`,`child_entity_id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `File` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) NOT NULL,
  `type` char(255) NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL,
  `mtime` datetime NOT NULL,
  `atime` datetime NOT NULL,
  `thumbnail` longblob,
  `contents` longblob NOT NULL,
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `FileLink` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `file_id` int(10) unsigned NOT NULL,
  `entity_type` enum('ipv4net','ipv4rspool','ipv4vs','ipvs','ipv6net','location','object','rack','row','user') NOT NULL default 'object',
  `entity_id` int(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FileLink-file_id` (`file_id`),
  UNIQUE KEY `FileLink-unique` (`file_id`,`entity_type`,`entity_id`),
  CONSTRAINT `FileLink-File_fkey` FOREIGN KEY (`file_id`) REFERENCES `File` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `IPv4Address` (
  `ip` int(10) unsigned NOT NULL default '0',
  `name` char(255) NOT NULL default '',
  `comment` char(255) NOT NULL default '',
  `reserved` enum('yes','no') default NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `IPv4Allocation` (
  `object_id` int(10) unsigned NOT NULL default '0',
  `ip` int(10) unsigned NOT NULL default '0',
  `name` char(255) NOT NULL default '',
  `type` enum('regular','shared','virtual','router','point2point') NOT NULL DEFAULT 'regular',
  PRIMARY KEY  (`object_id`,`ip`),
  KEY `ip` (`ip`),
  CONSTRAINT `IPv4Allocation-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `IPv4LB` (
  `object_id` int(10) unsigned default NULL,
  `rspool_id` int(10) unsigned default NULL,
  `vs_id` int(10) unsigned default NULL,
  `prio` varchar(255) default NULL,
  `vsconfig` text,
  `rsconfig` text,
  UNIQUE KEY `LB-VS` (`object_id`,`vs_id`),
  KEY `IPv4LB-FK-rspool_id` (`rspool_id`),
  KEY `IPv4LB-FK-vs_id` (`vs_id`),
  CONSTRAINT `IPv4LB-FK-vs_id` FOREIGN KEY (`vs_id`) REFERENCES `IPv4VS` (`id`),
  CONSTRAINT `IPv4LB-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`),
  CONSTRAINT `IPv4LB-FK-rspool_id` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `IPv4Log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `user` varchar(64) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip-date` (`ip`,`date`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `IPv6Log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` binary(16) NOT NULL,
  `date` datetime NOT NULL,
  `user` varchar(64) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip-date` (`ip`,`date`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `IPv4NAT` (
  `object_id` int(10) unsigned NOT NULL default '0',
  `proto` enum('TCP','UDP','ALL') NOT NULL default 'TCP',
  `localip` int(10) unsigned NOT NULL default '0',
  `localport` smallint(5) unsigned NOT NULL default '0',
  `remoteip` int(10) unsigned NOT NULL default '0',
  `remoteport` smallint(5) unsigned NOT NULL default '0',
  `description` char(255) default NULL,
  PRIMARY KEY  (`object_id`,`proto`,`localip`,`localport`,`remoteip`,`remoteport`),
  KEY `localip` (`localip`),
  KEY `remoteip` (`remoteip`),
  KEY `object_id` (`object_id`),
  CONSTRAINT `IPv4NAT-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `IPv4Network` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip` int(10) unsigned NOT NULL default '0',
  `mask` int(10) unsigned NOT NULL default '0',
  `name` char(255) default NULL,
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `base-len` (`ip`,`mask`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `IPv4RS` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `inservice` enum('yes','no') NOT NULL default 'no',
  `rsip` varbinary(16) NOT NULL,
  `rsport` smallint(5) unsigned default NULL,
  `rspool_id` int(10) unsigned default NULL,
  `rsconfig` text,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY  (`id`),
  KEY `rsip` (`rsip`),
  UNIQUE KEY `pool-endpoint` (`rspool_id`,`rsip`,`rsport`),
  CONSTRAINT `IPv4RS-FK` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `IPv4RSPool` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) default NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `IPv4VS` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `vip` varbinary(16) NOT NULL,
  `vport` smallint(5) unsigned default NULL,
  `proto` enum('TCP','UDP','MARK') NOT NULL default 'TCP',
  `name` char(255) default NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY  (`id`),
  KEY `vip` (`vip`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `IPv6Address` (
  `ip` binary(16) NOT NULL,
  `name` char(255) NOT NULL default '',
  `comment` char(255) NOT NULL default '',
  `reserved` enum('yes','no') default NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `IPv6Allocation` (
  `object_id` int(10) unsigned NOT NULL default '0',
  `ip` binary(16) NOT NULL,
  `name` char(255) NOT NULL default '',
  `type` enum('regular','shared','virtual','router','point2point') NOT NULL DEFAULT 'regular',
  PRIMARY KEY  (`object_id`,`ip`),
  KEY `ip` (`ip`),
  CONSTRAINT `IPv6Allocation-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `IPv6Network` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip` binary(16) NOT NULL,
  `mask` int(10) unsigned NOT NULL,
  `last_ip` binary(16) NOT NULL,
  `name` char(255) default NULL,
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ip` (`ip`,`mask`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `LDAPCache` (
  `presented_username` char(64) NOT NULL,
  `successful_hash` char(40) NOT NULL,
  `first_success` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `last_retry` timestamp NULL default NULL,
  `displayed_name` char(128) default NULL,
  `memberof` text,
  UNIQUE KEY `presented_username` (`presented_username`),
  KEY `scanidx` (`presented_username`,`successful_hash`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `Link` (
  `porta` int(10) unsigned NOT NULL default '0',
  `portb` int(10) unsigned NOT NULL default '0',
  `cable` char(64) DEFAULT NULL,
  PRIMARY KEY  (`porta`,`portb`),
  UNIQUE KEY `porta` (`porta`),
  UNIQUE KEY `portb` (`portb`),
  CONSTRAINT `Link-FK-a` FOREIGN KEY (`porta`) REFERENCES `Port` (`id`) ON DELETE CASCADE,
  CONSTRAINT `Link-FK-b` FOREIGN KEY (`portb`) REFERENCES `Port` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `Molecule` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `MountOperation` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned NOT NULL default '0',
  `ctime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `user_name` char(64) default NULL,
  `old_molecule_id` int(10) unsigned default NULL,
  `new_molecule_id` int(10) unsigned default NULL,
  `comment` text,
  PRIMARY KEY  (`id`),
  KEY `object_id` (`object_id`),
  CONSTRAINT `MountOperation-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE,
  CONSTRAINT `MountOperation-FK-old_molecule_id` FOREIGN KEY (`old_molecule_id`) REFERENCES `Molecule` (`id`) ON DELETE CASCADE,
  CONSTRAINT `MountOperation-FK-new_molecule_id` FOREIGN KEY (`new_molecule_id`) REFERENCES `Molecule` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `MuninGraph` (
  `object_id` int(10) unsigned NOT NULL,
  `server_id` int(10) unsigned NOT NULL,
  `graph` char(255) NOT NULL,
  `caption`  char(255) DEFAULT NULL,
  PRIMARY KEY (`object_id`,`server_id`,`graph`),
  KEY `server_id` (`server_id`),
  KEY `graph` (`graph`),
  CONSTRAINT `MuninGraph-FK-server_id` FOREIGN KEY (`server_id`) REFERENCES `MuninServer` (`id`),
  CONSTRAINT `MuninGraph-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `MuninServer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `base_url` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `ObjectLog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(10) unsigned NOT NULL,
  `user` char(64) NOT NULL,
  `date` datetime NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `object_id` (`object_id`),
  KEY `date` (`date`),
  CONSTRAINT `ObjectLog-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `ObjectParentCompat` (
  `parent_objtype_id` int(10) unsigned NOT NULL,
  `child_objtype_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `parent_child` (`parent_objtype_id`,`child_objtype_id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PatchCableConnector` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `origin` enum('default','custom') NOT NULL DEFAULT 'custom',
  `connector` char(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `connector_per_origin` (`connector`,`origin`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PatchCableConnectorCompat` (
  `pctype_id` int(10) unsigned NOT NULL,
  `connector_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pctype_id`,`connector_id`),
  KEY `connector_id` (`connector_id`),
  CONSTRAINT `PatchCableConnectorCompat-FK-connector_id` FOREIGN KEY (`connector_id`) REFERENCES `PatchCableConnector` (`id`),
  CONSTRAINT `PatchCableConnectorCompat-FK-pctype_id` FOREIGN KEY (`pctype_id`) REFERENCES `PatchCableType` (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PatchCableHeap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pctype_id` int(10) unsigned NOT NULL,
  `end1_conn_id` int(10) unsigned NOT NULL,
  `end2_conn_id` int(10) unsigned NOT NULL,
  `amount` smallint(5) unsigned NOT NULL DEFAULT '0',
  `length` decimal(5,2) unsigned NOT NULL DEFAULT '1.00',
  `description` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compat1` (`pctype_id`,`end1_conn_id`),
  KEY `compat2` (`pctype_id`,`end2_conn_id`),
  CONSTRAINT `PatchCableHeap-FK-compat1` FOREIGN KEY (`pctype_id`, `end1_conn_id`) REFERENCES `PatchCableConnectorCompat` (`pctype_id`, `connector_id`),
  CONSTRAINT `PatchCableHeap-FK-compat2` FOREIGN KEY (`pctype_id`, `end2_conn_id`) REFERENCES `PatchCableConnectorCompat` (`pctype_id`, `connector_id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PatchCableHeapLog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `heap_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `user` char(64) NOT NULL,
  `message` char(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `heap_id-date` (`heap_id`,`date`),
  CONSTRAINT `PatchCableHeapLog-FK-heap_id` FOREIGN KEY (`heap_id`) REFERENCES `PatchCableHeap` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PatchCableOIFCompat` (
  `pctype_id` int(10) unsigned NOT NULL,
  `oif_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pctype_id`,`oif_id`),
  KEY `oif_id` (`oif_id`),
  CONSTRAINT `PatchCableOIFCompat-FK-oif_id` FOREIGN KEY (`oif_id`) REFERENCES `PortOuterInterface` (`id`),
  CONSTRAINT `PatchCableOIFCompat-FK-pctype_id` FOREIGN KEY (`pctype_id`) REFERENCES `PatchCableType` (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PatchCableType` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `origin` enum('default','custom') NOT NULL DEFAULT 'custom',
  `pctype` char(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pctype_per_origin` (`pctype`,`origin`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `Port` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned NOT NULL default '0',
  `name` char(255) NOT NULL default '',
  `iif_id` int(10) unsigned NOT NULL,
  `type` int(10) unsigned NOT NULL default '0',
  `l2address` char(64) default NULL,
  `reservation_comment` char(255) default NULL,
  `label` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `object_iif_oif_name` (`object_id`,`iif_id`,`type`,`name`),
  KEY `type` (`type`),
  KEY `comment` (`reservation_comment`),
  KEY `l2address` (`l2address`),
  KEY `Port-FK-iif-oif` (`iif_id`,`type`),
  CONSTRAINT `Port-FK-iif-oif` FOREIGN KEY (`iif_id`, `type`) REFERENCES `PortInterfaceCompat` (`iif_id`, `oif_id`),
  CONSTRAINT `Port-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PortAllowedVLAN` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `PortAllowedVLAN-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `PortVLANMode` (`object_id`, `port_name`) ON DELETE CASCADE,
  CONSTRAINT `PortAllowedVLAN-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PortCompat` (
  `type1` int(10) unsigned NOT NULL default '0',
  `type2` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `type1_2` (`type1`,`type2`),
  KEY `type2` (`type2`),
  CONSTRAINT `PortCompat-FK-oif_id1` FOREIGN KEY (`type1`) REFERENCES `PortOuterInterface` (`id`),
  CONSTRAINT `PortCompat-FK-oif_id2` FOREIGN KEY (`type2`) REFERENCES `PortOuterInterface` (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PortInnerInterface` (
  `id` int(10) unsigned NOT NULL,
  `iif_name` char(16) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `iif_name` (`iif_name`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PortInterfaceCompat` (
  `iif_id` int(10) unsigned NOT NULL,
  `oif_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `pair` (`iif_id`,`oif_id`),
  CONSTRAINT `PortInterfaceCompat-FK-iif_id` FOREIGN KEY (`iif_id`) REFERENCES `PortInnerInterface` (`id`),
  CONSTRAINT `PortInterfaceCompat-FK-oif_id` FOREIGN KEY (`oif_id`) REFERENCES `PortOuterInterface` (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PortLog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `port_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `user` varchar(64) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `port_id-date` (`port_id`,`date`),
  CONSTRAINT `PortLog_ibfk_1` FOREIGN KEY (`port_id`) REFERENCES `Port` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PortNativeVLAN` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  UNIQUE KEY `port_id` (`object_id`,`port_name`),
  CONSTRAINT `PortNativeVLAN-FK-compound` FOREIGN KEY (`object_id`, `port_name`, `vlan_id`) REFERENCES `PortAllowedVLAN` (`object_id`, `port_name`, `vlan_id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PortOuterInterface` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `oif_name` char(48) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oif_name` (`oif_name`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `PortVLANMode` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_mode` enum('access','trunk') NOT NULL default 'access',
  PRIMARY KEY  (`object_id`,`port_name`),
  CONSTRAINT `PortVLANMode-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `CachedPVM` (`object_id`, `port_name`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `Object` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) default NULL,
  `label` char(255) default NULL,
  `objtype_id` int(10) unsigned NOT NULL default '1',
  `asset_no` char(64) default NULL,
  `has_problems` enum('yes','no') NOT NULL default 'no',
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `asset_no` (`asset_no`),
  KEY `id-tid` (`id`,`objtype_id`),
  KEY `type_id` (`objtype_id`,`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `ObjectHistory` (
  `id` int(10) unsigned default NULL,
  `name` char(255) default NULL,
  `label` char(255) default NULL,
  `objtype_id` int(10) unsigned default NULL,
  `asset_no` char(64) default NULL,
  `has_problems` enum('yes','no') NOT NULL default 'no',
  `comment` text,
  `ctime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `user_name` char(64) default NULL,
  KEY `id` (`id`),
  CONSTRAINT `ObjectHistory-FK-object_id` FOREIGN KEY (`id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `RackSpace` (
  `rack_id` int(10) unsigned NOT NULL default '0',
  `unit_no` int(10) unsigned NOT NULL default '0',
  `atom` enum('front','interior','rear') NOT NULL default 'interior',
  `state` enum('A','U','T') NOT NULL default 'A',
  `object_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`rack_id`,`unit_no`,`atom`),
  KEY `RackSpace_object_id` (`object_id`),
  CONSTRAINT `RackSpace-FK-rack_id` FOREIGN KEY (`rack_id`) REFERENCES `Object` (`id`),
  CONSTRAINT `RackSpace-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `RackThumbnail` (
  `rack_id` int(10) unsigned NOT NULL,
  `thumb_data` blob,
  UNIQUE KEY `rack_id` (`rack_id`),
  CONSTRAINT `RackThumbnail-FK-rack_id` FOREIGN KEY (`rack_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `Script` (
  `script_name` char(64) NOT NULL,
  `script_text` longtext,
  PRIMARY KEY  (`script_name`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `TagStorage` (
  `entity_realm` enum('file','ipv4net','ipv4rspool','ipv4vs','ipvs','ipv6net','location','object','rack','user','vst') NOT NULL default 'object',
  `entity_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL default '0',
  `tag_is_assignable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `user` char(64) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  UNIQUE KEY `entity_tag` (`entity_realm`,`entity_id`,`tag_id`),
  KEY `entity_id` (`entity_id`),
  KEY `TagStorage-FK-tag_id` (`tag_id`),
  KEY `tag_id-tag_is_assignable` (`tag_id`,`tag_is_assignable`),
  CONSTRAINT `TagStorage-FK-TagTree` FOREIGN KEY (`tag_id`, `tag_is_assignable`) REFERENCES `TagTree` (`id`, `is_assignable`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `TagTree` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned default NULL,
  `is_assignable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `tag` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tag` (`tag`),
  KEY `TagTree-K-parent_id` (`parent_id`),
  KEY `id-is_assignable` (`id`,`is_assignable`),
  CONSTRAINT `TagTree-K-parent_id` FOREIGN KEY (`parent_id`) REFERENCES `TagTree` (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `UserAccount` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_name` char(64) NOT NULL default '',
  `user_password_hash` char(40) default NULL,
  `user_realname` char(64) default NULL,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `UserConfig` (
  `varname` char(32) NOT NULL,
  `varvalue` text NOT NULL,
  `user` char(64) NOT NULL,
  UNIQUE KEY `user_varname` (`user`,`varname`),
  KEY `varname` (`varname`),
  CONSTRAINT `UserConfig-FK-varname` FOREIGN KEY (`varname`) REFERENCES `Config` (`varname`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `VLANDescription` (
  `domain_id` int(10) unsigned NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  `vlan_type` enum('ondemand','compulsory','alien') NOT NULL default 'ondemand',
  `vlan_descr` char(255) default NULL,
  PRIMARY KEY  (`domain_id`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `VLANDescription-FK-domain_id` FOREIGN KEY (`domain_id`) REFERENCES `VLANDomain` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VLANDescription-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `VLANDomain` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(10) unsigned default NULL,
  `description` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `description` (`description`),
  CONSTRAINT `VLANDomain-FK-group_id` FOREIGN KEY (`group_id`) REFERENCES `VLANDomain` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `VLANIPv4` (
  `domain_id` int(10) unsigned NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL,
  `ipv4net_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `network-domain-vlan` (`ipv4net_id`,`domain_id`,`vlan_id`),
  KEY `VLANIPv4-FK-compound` (`domain_id`,`vlan_id`),
  CONSTRAINT `VLANIPv4-FK-compound` FOREIGN KEY (`domain_id`, `vlan_id`) REFERENCES `VLANDescription` (`domain_id`, `vlan_id`) ON DELETE CASCADE,
  CONSTRAINT `VLANIPv4-FK-ipv4net_id` FOREIGN KEY (`ipv4net_id`) REFERENCES `IPv4Network` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `VLANIPv6` (
  `domain_id` int(10) unsigned NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL,
  `ipv6net_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `network-domain-vlan` (`ipv6net_id`,`domain_id`,`vlan_id`),
  KEY `VLANIPv6-FK-compound` (`domain_id`,`vlan_id`),
  CONSTRAINT `VLANIPv6-FK-compound` FOREIGN KEY (`domain_id`, `vlan_id`) REFERENCES `VLANDescription` (`domain_id`, `vlan_id`) ON DELETE CASCADE,
  CONSTRAINT `VLANIPv6-FK-ipv6net_id` FOREIGN KEY (`ipv6net_id`) REFERENCES `IPv6Network` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `VLANSTRule` (
  `vst_id` int(10) unsigned NOT NULL,
  `rule_no` int(10) unsigned NOT NULL,
  `port_pcre` char(255) NOT NULL,
  `port_role` enum('access','trunk','anymode','uplink','downlink','none') NOT NULL default 'none',
  `wrt_vlans` text,
  `description` char(255) default NULL,
  UNIQUE KEY `vst-rule` (`vst_id`,`rule_no`),
  CONSTRAINT `VLANSTRule-FK-vst_id` FOREIGN KEY (`vst_id`) REFERENCES `VLANSwitchTemplate` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `VLANSwitch` (
  `object_id` int(10) unsigned NOT NULL,
  `domain_id` int(10) unsigned NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `mutex_rev` int(10) unsigned NOT NULL default '0',
  `out_of_sync` enum('yes','no') NOT NULL default 'yes',
  `last_errno` int(10) unsigned NOT NULL default '0',
  `last_change` timestamp NOT NULL default '0000-00-00 00:00:00',
  `last_push_started` timestamp NOT NULL default '0000-00-00 00:00:00',
  `last_push_finished` timestamp NOT NULL default '0000-00-00 00:00:00',
  `last_error_ts` timestamp NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `object_id` (`object_id`),
  KEY `domain_id` (`domain_id`),
  KEY `template_id` (`template_id`),
  KEY `out_of_sync` (`out_of_sync`),
  KEY `last_errno` (`last_errno`),
  CONSTRAINT `VLANSwitch-FK-domain_id` FOREIGN KEY (`domain_id`) REFERENCES `VLANDomain` (`id`),
  CONSTRAINT `VLANSwitch-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`),
  CONSTRAINT `VLANSwitch-FK-template_id` FOREIGN KEY (`template_id`) REFERENCES `VLANSwitchTemplate` (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `VLANSwitchTemplate` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `mutex_rev` int(10) NOT NULL,
  `description` char(255) default NULL,
  `saved_by` char(64) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `VLANValidID` (
  `vlan_id` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`vlan_id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `VS` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) DEFAULT NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `VSIPs` (
  `vs_id` int(10) unsigned NOT NULL,
  `vip` varbinary(16) NOT NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY (`vs_id`,`vip`),
  KEY `vip` (`vip`),
  CONSTRAINT `VSIPs-vs_id` FOREIGN KEY (`vs_id`) REFERENCES `VS` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `VSPorts` (
  `vs_id` int(10) unsigned NOT NULL,
  `proto` enum('TCP','UDP','MARK') NOT NULL,
  `vport` int(10) unsigned NOT NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY (`vs_id`,`proto`,`vport`),
  KEY `proto-vport` (`proto`,`vport`),
  CONSTRAINT `VS-vs_id` FOREIGN KEY (`vs_id`) REFERENCES `VS` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `VSEnabledIPs` (
  `object_id` int(10) unsigned NOT NULL,
  `vs_id` int(10) unsigned NOT NULL,
  `vip` varbinary(16) NOT NULL,
  `rspool_id` int(10) unsigned NOT NULL,
  `prio` varchar(255) DEFAULT NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY (`object_id`,`vs_id`,`vip`,`rspool_id`),
  KEY `vip` (`vip`),
  KEY `VSEnabledIPs-FK-vs_id-vip` (`vs_id`,`vip`),
  KEY `VSEnabledIPs-FK-rspool_id` (`rspool_id`),
  CONSTRAINT `VSEnabledIPs-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VSEnabledIPs-FK-rspool_id` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VSEnabledIPs-FK-vs_id-vip` FOREIGN KEY (`vs_id`, `vip`) REFERENCES `VSIPs` (`vs_id`, `vip`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "CREATE TABLE `VSEnabledPorts` (
  `object_id` int(10) unsigned NOT NULL,
  `vs_id` int(10) unsigned NOT NULL,
  `proto` enum('TCP','UDP','MARK') NOT NULL,
  `vport` int(10) unsigned NOT NULL,
  `rspool_id` int(10) unsigned NOT NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY (`object_id`,`vs_id`,`proto`,`vport`,`rspool_id`),
  KEY `VSEnabledPorts-FK-vs_id-proto-vport` (`vs_id`,`proto`,`vport`),
  KEY `VSEnabledPorts-FK-rspool_id` (`rspool_id`),
  CONSTRAINT `VSEnabledPorts-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VSEnabledPorts-FK-rspool_id` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VSEnabledPorts-FK-vs_id-proto-vport` FOREIGN KEY (`vs_id`, `proto`, `vport`) REFERENCES `VSPorts` (`vs_id`, `proto`, `vport`) ON DELETE CASCADE
) ENGINE=InnoDB";

		$query[] = "
CREATE TRIGGER `EntityLink-before-insert` BEFORE INSERT ON `EntityLink` FOR EACH ROW
EntityLinkTrigger:BEGIN
  DECLARE parent_objtype, child_objtype, count INTEGER;

  # forbid linking an entity to itself
  IF NEW.parent_entity_type = NEW.child_entity_type AND NEW.parent_entity_id = NEW.child_entity_id THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  # in some scenarios, only one parent is allowed
  CASE CONCAT(NEW.parent_entity_type, '.', NEW.child_entity_type)
    WHEN 'location.location' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'location' AND child_entity_id = NEW.child_entity_id;
    WHEN 'location.row' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'row' AND child_entity_id = NEW.child_entity_id;
    WHEN 'row.rack' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'row' AND child_entity_type = 'rack' AND child_entity_id = NEW.child_entity_id;
    ELSE
      # some other scenario, assume it is valid
      SET count = 0;
  END CASE; 
  IF count > 0 THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  IF NEW.parent_entity_type = 'object' AND NEW.child_entity_type = 'object' THEN
    # lock objects to prevent concurrent link establishment
    SELECT objtype_id INTO parent_objtype FROM Object WHERE id = NEW.parent_entity_id FOR UPDATE;
    SELECT objtype_id INTO child_objtype FROM Object WHERE id = NEW.child_entity_id FOR UPDATE;

    # only permit the link if object types are compatibile
    SELECT COUNT(*) INTO count FROM ObjectParentCompat WHERE parent_objtype_id = parent_objtype AND child_objtype_id = child_objtype;
    IF count = 0 THEN
      SET NEW.parent_entity_id = NULL;
    END IF;
  END IF;
END;
";
		$query[] = "
CREATE TRIGGER `EntityLink-before-update` BEFORE UPDATE ON `EntityLink` FOR EACH ROW
EntityLinkTrigger:BEGIN
  DECLARE parent_objtype, child_objtype, count INTEGER;

  # forbid linking an entity to itself
  IF NEW.parent_entity_type = NEW.child_entity_type AND NEW.parent_entity_id = NEW.child_entity_id THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  # in some scenarios, only one parent is allowed
  CASE CONCAT(NEW.parent_entity_type, '.', NEW.child_entity_type)
    WHEN 'location.location' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'location' AND child_entity_id = NEW.child_entity_id AND id != NEW.id;
    WHEN 'location.row' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'row' AND child_entity_id = NEW.child_entity_id AND id != NEW.id;
    WHEN 'row.rack' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'row' AND child_entity_type = 'rack' AND child_entity_id = NEW.child_entity_id AND id != NEW.id;
    ELSE
      # some other scenario, assume it is valid
      SET count = 0;
  END CASE; 
  IF count > 0 THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  IF NEW.parent_entity_type = 'object' AND NEW.child_entity_type = 'object' THEN
    # lock objects to prevent concurrent link establishment
    SELECT objtype_id INTO parent_objtype FROM Object WHERE id = NEW.parent_entity_id FOR UPDATE;
    SELECT objtype_id INTO child_objtype FROM Object WHERE id = NEW.child_entity_id FOR UPDATE;

    # only permit the link if object types are compatibile
    SELECT COUNT(*) INTO count FROM ObjectParentCompat WHERE parent_objtype_id = parent_objtype AND child_objtype_id = child_objtype;
    IF count = 0 THEN
      SET NEW.parent_entity_id = NULL;
    END IF;
  END IF;
END;
";
		$link_trigger_body = <<<ENDOFTRIGGER
LinkTrigger:BEGIN
  DECLARE tmp, porta_type, portb_type, count INTEGER;

  IF NEW.porta = NEW.portb THEN
    # forbid connecting a port to itself
    SET NEW.porta = NULL;
    LEAVE LinkTrigger;
  ELSEIF NEW.porta > NEW.portb THEN
    # force porta < portb
    SET tmp = NEW.porta;
    SET NEW.porta = NEW.portb;
    SET NEW.portb = tmp;
  END IF; 

  # lock ports to prevent concurrent link establishment
  SELECT type INTO porta_type FROM Port WHERE id = NEW.porta FOR UPDATE;
  SELECT type INTO portb_type FROM Port WHERE id = NEW.portb FOR UPDATE;

  # only permit the link if ports are compatibile
  SELECT COUNT(*) INTO count FROM PortCompat WHERE (type1 = porta_type AND type2 = portb_type) OR (type1 = portb_type AND type2 = porta_type);
  IF count = 0 THEN
    SET NEW.porta = NULL;
  END IF;
END;
ENDOFTRIGGER;
		$query[] = "CREATE TRIGGER `Link-before-insert` BEFORE INSERT ON `Link` FOR EACH ROW $link_trigger_body";
		$query[] = "CREATE TRIGGER `Link-before-update` BEFORE UPDATE ON `Link` FOR EACH ROW $link_trigger_body";

		$query[] = "CREATE VIEW `Location` AS SELECT O.id, O.name, O.has_problems, O.comment, P.id AS parent_id, P.name AS parent_name
FROM `Object` O
LEFT JOIN (
  `Object` P INNER JOIN `EntityLink` EL
  ON EL.parent_entity_id = P.id AND P.objtype_id = 1562 AND EL.parent_entity_type = 'location' AND EL.child_entity_type = 'location'
) ON EL.child_entity_id = O.id
WHERE O.objtype_id = 1562";

		$query[] = "CREATE VIEW `Row` AS SELECT O.id, O.name, L.id AS location_id, L.name AS location_name
  FROM `Object` O
  LEFT JOIN `EntityLink` EL ON O.id = EL.child_entity_id AND EL.parent_entity_type = 'location' AND EL.child_entity_type = 'row'
  LEFT JOIN `Object` L ON EL.parent_entity_id = L.id AND L.objtype_id = 1562
  WHERE O.objtype_id = 1561";

		$query[] = "CREATE VIEW `Rack` AS SELECT O.id, O.name AS name, O.asset_no, O.has_problems, O.comment,
  AV_H.uint_value AS height,
  AV_S.uint_value AS sort_order,
  RT.thumb_data,
  R.id AS row_id,
  R.name AS row_name,
  L.id AS location_id,
  L.name AS location_name
  FROM `Object` O
  LEFT JOIN `AttributeValue` AV_H ON O.id = AV_H.object_id AND AV_H.attr_id = 27
  LEFT JOIN `AttributeValue` AV_S ON O.id = AV_S.object_id AND AV_S.attr_id = 29
  LEFT JOIN `RackThumbnail` RT ON O.id = RT.rack_id
  LEFT JOIN `EntityLink` RL ON O.id = RL.child_entity_id  AND RL.parent_entity_type = 'row' AND RL.child_entity_type = 'rack'
  INNER JOIN `Object` R ON R.id = RL.parent_entity_id
  LEFT JOIN `EntityLink` LL ON R.id = LL.child_entity_id AND LL.parent_entity_type = 'location' AND LL.child_entity_type = 'row'
  LEFT JOIN `Object` L ON L.id = LL.parent_entity_id
  WHERE O.objtype_id = 1560";

		$query[] = "CREATE VIEW `RackObject` AS SELECT id, name, label, objtype_id, asset_no, has_problems, comment FROM `Object`
 WHERE `objtype_id` NOT IN (1560, 1561, 1562)";

		$query[] = "SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS";

		return $query;
##########################################################################
	case 'dictbase':
		$db_version = CODE_VERSION;
		$query = array();

		$query[] = "INSERT INTO `Attribute` (`id`, `type`, `name`) VALUES
(1,'string','OEM S/N 1'),
(2,'dict','HW type'),
(3,'string','FQDN'),
(4,'dict','SW type'),
(5,'string','SW version'),
(6,'uint','number of ports'),
(7,'float','max. current, Ampers'),
(8,'float','power load, percents'),
(13,'float','max power, Watts'),
(14,'string','contact person'),
(16,'uint','flash memory, MB'),
(17,'uint','DRAM, MB'),
(18,'uint','CPU, MHz'),
(20,'string','OEM S/N 2'),
(21,'date','support contract expiration'),
(22,'date','HW warranty expiration'),
(24,'date','SW warranty expiration'),
(25,'string','UUID'),
(26,'dict','Hypervisor'),
(27,'uint','Height, units'),
(28,'string','Slot number'),
(29,'uint','Sort order'),
(30,'dict','Mgmt type'),
-- ^^^^^ Any new 'default' attributes must go above this line! ^^^^^
-- Primary key value 9999 makes sure, that AUTO_INCREMENT on server restart
-- doesn't drop below 10000 (other code relies on this, site-specific
-- attributes are assigned IDs starting from 10000).
(9999,'string','base MAC address')";

		$query[] = "INSERT INTO `Chapter` (`id`, `sticky`, `name`) VALUES
(1,'yes','ObjectType'),
(11,'no','server models'),
(12,'no','network switch models'),
(13,'no','server OS type'),
(14,'no','switch OS type'),
(16,'no','router OS type'),
(17,'no','router models'),
(18,'no','disk array models'),
(19,'no','tape library models'),
(21,'no','KVM switch models'),
(23,'no','console models'),
(24,'no','network security models'),
(25,'no','wireless models'),
(26,'no','fibre channel switch models'),
(27,'no','PDU models'),
(28,'no','Voice/video hardware'),
(29,'no','Yes/No'),
(30,'no','network chassis models'),
(31,'no','server chassis models'),
(32,'no','virtual switch models'),
(33,'no','virtual switch OS type'),
(34,'no','power supply chassis models'),
(35,'no','power supply models'),
(36,'no','serial console server models'),
(37,'no','wireless OS type'),
(38,'no','management interface type'),
-- Default chapters must have ID less than 10000, add them above this line.
(9999,'no','multiplexer models')";

		$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`, `sticky`) VALUES
(2,1,NULL,'no'),
(2,2,27,'no'),
(2,3,NULL,'no'),
(2,5,NULL,'no'),
(4,1,NULL,'no'),
(4,2,11,'no'),
(4,3,NULL,'no'),
(4,4,13,'no'),
(4,14,NULL,'no'),
(4,21,NULL,'no'),
(4,22,NULL,'no'),
(4,24,NULL,'no'),
(4,25,NULL,'no'),
(4,26,29,'yes'),
(4,28,NULL,'yes'),
(5,1,NULL,'no'),
(5,2,18,'no'),
(6,1,NULL,'no'),
(6,2,19,'no'),
(6,20,NULL,'no'),
(7,1,NULL,'no'),
(7,2,17,'no'),
(7,3,NULL,'no'),
(7,4,16,'no'),
(7,5,NULL,'no'),
(7,14,NULL,'no'),
(7,16,NULL,'no'),
(7,17,NULL,'no'),
(7,18,NULL,'no'),
(7,21,NULL,'no'),
(7,22,NULL,'no'),
(7,24,NULL,'no'),
(8,1,NULL,'yes'),
(8,2,12,'yes'),
(8,3,NULL,'no'),
(8,4,14,'yes'),
(8,5,NULL,'no'),
(8,14,NULL,'no'),
(8,16,NULL,'no'),
(8,17,NULL,'no'),
(8,18,NULL,'no'),
(8,20,NULL,'no'),
(8,21,NULL,'no'),
(8,22,NULL,'no'),
(8,24,NULL,'no'),
(8,28,NULL,'yes'),
(9,6,NULL,'no'),
(12,1,NULL,'no'),
(12,3,NULL,'no'),
(12,7,NULL,'no'),
(12,8,NULL,'no'),
(12,13,NULL,'no'),
(12,20,NULL,'no'),
(15,2,23,'no'),
(445,1,NULL,'no'),
(445,2,21,'no'),
(445,3,NULL,'no'),
(445,5,NULL,'no'),
(445,14,NULL,'no'),
(445,22,NULL,'no'),
(447,1,NULL,'no'),
(447,2,9999,'no'),
(447,3,NULL,'no'),
(447,5,NULL,'no'),
(447,14,NULL,'no'),
(447,22,NULL,'no'),
(798,1,NULL,'no'),
(798,2,24,'no'),
(798,3,NULL,'no'),
(798,5,NULL,'no'),
(798,14,NULL,'no'),
(798,16,NULL,'no'),
(798,17,NULL,'no'),
(798,18,NULL,'no'),
(798,20,NULL,'no'),
(798,21,NULL,'no'),
(798,22,NULL,'no'),
(798,24,NULL,'no'),
(798,28,NULL,'yes'),
(965,1,NULL,'no'),
(965,2,25,'no'),
(965,3,NULL,'no'),
(965,4,37,'no'),
(1055,2,26,'no'),
(1055,28,NULL,'yes'),
(1323,1,NULL,'no'),
(1323,2,28,'no'),
(1323,3,NULL,'no'),
(1323,5,NULL,'no'),
(1397,1,NULL,'no'),
(1397,2,34,'no'),
(1397,14,NULL,'no'),
(1397,21,NULL,'no'),
(1397,22,NULL,'no'),
(1398,1,NULL,'no'),
(1398,2,35,'no'),
(1398,14,NULL,'no'),
(1398,21,NULL,'no'),
(1398,22,NULL,'no'),
(1502,1,NULL,'no'),
(1502,2,31,'no'),
(1502,3,NULL,'no'),
(1502,14,NULL,'no'),
(1502,20,NULL,'no'),
(1502,21,NULL,'no'),
(1502,22,NULL,'no'),
(1503,1,NULL,'no'),
(1503,2,30,'no'),
(1503,3,NULL,'no'),
(1503,4,14,'no'),
(1503,5,NULL,'no'),
(1503,14,NULL,'no'),
(1503,16,NULL,'no'),
(1503,17,NULL,'no'),
(1503,18,NULL,'no'),
(1503,20,NULL,'no'),
(1503,21,NULL,'no'),
(1503,22,NULL,'no'),
(1503,24,NULL,'no'),
(1504,3,NULL,'no'),
(1504,4,13,'no'),
(1504,14,NULL,'no'),
(1504,24,NULL,'no'),
(1505,14,NULL,'no'),
(1506,14,NULL,'no'),
(1506,17,NULL,'no'),
(1506,18,NULL,'no'),
(1507,1,NULL,'no'),
(1507,2,32,'no'),
(1507,3,NULL,'no'),
(1507,4,33,'no'),
(1507,5,NULL,'no'),
(1507,14,NULL,'no'),
(1507,20,NULL,'no'),
(1507,21,NULL,'no'),
(1507,22,NULL,'no'),
(1560,27,NULL,'yes'),
(1560,29,NULL,'yes'),
(1562,14,NULL,'no'),
(1644,1,NULL,'no'),
(1644,2,36,'no'),
(1644,3,NULL,'no'),
(1787,3,NULL,'no'),
(1787,14,NULL,'no'),
(1787,30,38,'yes')";

		$query[] = "INSERT INTO PatchCableConnector (id, origin, connector) VALUES
(1,'default','FC/PC'),(2,'default','FC/APC'),
(3,'default','LC/PC'),(4,'default','LC/APC'),
(5,'default','MPO-12/PC'),(6,'default','MPO-12/APC'),
(7,'default','MPO-24/PC'),(8,'default','MPO-24/APC'),
(9,'default','SC/PC'),(10,'default','SC/APC'),
(11,'default','ST/PC'),(12,'default','ST/APC'),
(13,'default','T568/8P8C/RJ45'),
(14,'default','SFP-1000'),
(15,'default','SFP+'),
(999,'default','CX4/SFF-8470')";

		$query[] = "INSERT INTO PatchCableType (id, origin, pctype) VALUES
(1,'default','duplex OM1'),
(2,'default','duplex OM2'),
(3,'default','duplex OM3'),
(4,'default','duplex OM4'),
(5,'default','duplex OS1'),
(6,'default','duplex OS2'),
(7,'default','simplex OM1'),
(8,'default','simplex OM2'),
(9,'default','simplex OM3'),
(10,'default','simplex OM4'),
(11,'default','simplex OS1'),
(12,'default','simplex OS2'),
(13,'default','Cat.5 TP'),
(14,'default','Cat.6 TP'),
(15,'default','Cat.6a TP'),
(16,'default','Cat.7 TP'),
(17,'default','Cat.7a TP'),
(18,'default','12-fiber OM3'),
(19,'default','12-fiber OM4'),
(20,'default','10Gb/s CX4 coax'),
(21,'default','24-fiber OM3'),
(22,'default','24-fiber OM4'),
(23,'default','1Gb/s 50cm shielded'),
(24,'default','10Gb/s 24AWG twinax'),
(25,'default','10Gb/s 26AWG twinax'),
(26,'default','10Gb/s 28AWG twinax'),
(27,'default','10Gb/s 30AWG twinax'),
(999,'default','Cat.3 TP')";

		$query[] = "INSERT INTO PatchCableConnectorCompat (pctype_id, connector_id) VALUES
(1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1), -- FC/PC
(1,2),(2,2),(3,2),(4,2),(5,2),(6,2),(7,2),(8,2),(9,2),(10,2),(11,2),(12,2), -- FC/APC
(1,3),(2,3),(3,3),(4,3),(5,3),(6,3),(7,3),(8,3),(9,3),(10,3),(11,3),(12,3), -- LC/PC
(1,4),(2,4),(3,4),(4,4),(5,4),(6,4),(7,4),(8,4),(9,4),(10,4),(11,4),(12,4), -- LC/APC
(1,9),(2,9),(3,9),(4,9),(5,9),(6,9),(7,9),(8,9),(9,9),(10,9),(11,9),(12,9), -- SC/PC
(1,10),(2,10),(3,10),(4,10),(5,10),(6,10),(7,10),(8,10),(9,10),(10,10),(11,10),(12,10), -- SC/APC
(1,11),(2,11),(3,11),(4,11),(5,11),(6,11),(7,11),(8,11),(9,11),(10,11),(11,11),(12,11), -- ST/PC
(1,12),(2,12),(3,12),(4,12),(5,12),(6,12),(7,12),(8,12),(9,12),(10,12),(11,12),(12,12), -- ST/APC
(13,13),(14,13),(15,13),(16,13),(17,13),(999,13), -- T568
(18,5),(19,5), -- MPO-12/PC
(18,6),(19,6), -- MPO-12/APC
(20,999), -- CX4
(21,7),(22,7), -- MPO-24/PC
(21,8),(22,8), -- MPO-24/APC
(23,14), -- SFP-1000
(24,15),(25,15),(26,15),(27,15) -- SFP+";

		$query[] = "INSERT INTO `PortInnerInterface` VALUES
(1,'hardwired'),
(2,'SFP-100'),
(3,'GBIC'),
(4,'SFP-1000'),
(5,'XENPAK'),
(6,'X2'),
(7,'XPAK'),
(8,'XFP'),
(9,'SFP+'),
(10,'QSFP+'),
(11,'CFP'),
(12,'CFP2'),
(13,'CPAK'),
(14,'CXP'),
(15,'QSFP28')";

		$query[] = "INSERT INTO `PortOuterInterface` VALUES
(16,'AC-in'),
(17,'10Base2'),
(18,'10Base-T'),
(19,'100Base-TX'),
(24,'1000Base-T'),
(29,'RS-232 (RJ-45)'),
(30,'10GBase-SR'),
(31,'virtual bridge'),
(32,'sync serial'),
(33,'KVM (host)'),
(34,'1000Base-ZX'),
(35,'10GBase-ER'),
(36,'10GBase-LR'),
(37,'10GBase-LRM'),
(38,'10GBase-ZR'),
(39,'10GBase-LX4'),
(40,'10GBase-CX4'),
(41,'10GBase-KX4'),
(42, '1000Base-EX'),
(439,'dry contact'),
(440,'unknown'),
(446,'KVM (console)'),
(681,'RS-232 (DB-9)'),
(682,'RS-232 (DB-25)'),
(1077,'empty SFP-1000'),
(1078,'empty GBIC'),
(1079,'empty XENPAK'),
(1080,'empty X2'),
(1081,'empty XPAK'),
(1082,'empty XFP'),
(1084,'empty SFP+'),
(1087,'1000Base-T (Dell 1855)'),
(1088,'1000Base-BX40-D'),
(1089,'1000Base-BX40-U'),
(1090,'1000Base-BX80-D'),
(1091,'1000Base-BX80-U'),
(1195,'100Base-FX'),
(1196,'100Base-SX'),
(1197,'100Base-LX10'),
(1198,'100Base-BX10-D'),
(1199,'100Base-BX10-U'),
(1200,'100Base-EX'),
(1201,'100Base-ZX'),
(1202,'1000Base-SX'),
(1203,'1000Base-SX+'),
(1204,'1000Base-LX'),
(1205,'1000Base-LX10'),
(1206,'1000Base-BX10-D'),
(1207,'1000Base-BX10-U'),
(1208,'empty SFP-100'),
(1209,'1000Base-CWDM80-1470 (gray)'),
(1210,'1000Base-CWDM80-1490 (violet)'),
(1211,'1000Base-CWDM80-1510 (blue)'),
(1212,'1000Base-CWDM80-1530 (green)'),
(1213,'1000Base-CWDM80-1550 (yellow)'),
(1214,'1000Base-CWDM80-1570 (orange)'),
(1215,'1000Base-CWDM80-1590 (red)'),
(1216,'1000Base-CWDM80-1610 (brown)'),
(1217,'1000Base-DWDM80-61.42 (ITU 20)'),
(1218,'1000Base-DWDM80-60.61 (ITU 21)'),
(1219,'1000Base-DWDM80-59.79 (ITU 22)'),
(1220,'1000Base-DWDM80-58.98 (ITU 23)'),
(1221,'1000Base-DWDM80-58.17 (ITU 24)'),
(1222,'1000Base-DWDM80-57.36 (ITU 25)'),
(1223,'1000Base-DWDM80-56.55 (ITU 26)'),
(1224,'1000Base-DWDM80-55.75 (ITU 27)'),
(1225,'1000Base-DWDM80-54.94 (ITU 28)'),
(1226,'1000Base-DWDM80-54.13 (ITU 29)'),
(1227,'1000Base-DWDM80-53.33 (ITU 30)'),
(1228,'1000Base-DWDM80-52.52 (ITU 31)'),
(1229,'1000Base-DWDM80-51.72 (ITU 32)'),
(1230,'1000Base-DWDM80-50.92 (ITU 33)'),
(1231,'1000Base-DWDM80-50.12 (ITU 34)'),
(1232,'1000Base-DWDM80-49.32 (ITU 35)'),
(1233,'1000Base-DWDM80-48.51 (ITU 36)'),
(1234,'1000Base-DWDM80-47.72 (ITU 37)'),
(1235,'1000Base-DWDM80-46.92 (ITU 38)'),
(1236,'1000Base-DWDM80-46.12 (ITU 39)'),
(1237,'1000Base-DWDM80-45.32 (ITU 40)'),
(1238,'1000Base-DWDM80-44.53 (ITU 41)'),
(1239,'1000Base-DWDM80-43.73 (ITU 42)'),
(1240,'1000Base-DWDM80-42.94 (ITU 43)'),
(1241,'1000Base-DWDM80-42.14 (ITU 44)'),
(1242,'1000Base-DWDM80-41.35 (ITU 45)'),
(1243,'1000Base-DWDM80-40.56 (ITU 46)'),
(1244,'1000Base-DWDM80-39.77 (ITU 47)'),
(1245,'1000Base-DWDM80-38.98 (ITU 48)'),
(1246,'1000Base-DWDM80-38.19 (ITU 49)'),
(1247,'1000Base-DWDM80-37.40 (ITU 50)'),
(1248,'1000Base-DWDM80-36.61 (ITU 51)'),
(1249,'1000Base-DWDM80-35.82 (ITU 52)'),
(1250,'1000Base-DWDM80-35.04 (ITU 53)'),
(1251,'1000Base-DWDM80-34.25 (ITU 54)'),
(1252,'1000Base-DWDM80-33.47 (ITU 55)'),
(1253,'1000Base-DWDM80-32.68 (ITU 56)'),
(1254,'1000Base-DWDM80-31.90 (ITU 57)'),
(1255,'1000Base-DWDM80-31.12 (ITU 58)'),
(1256,'1000Base-DWDM80-30.33 (ITU 59)'),
(1257,'1000Base-DWDM80-29.55 (ITU 60)'),
(1258,'1000Base-DWDM80-28.77 (ITU 61)'),
(1259,'10GBase-ZR-DWDM80-61.42 (ITU 20)'),
(1260,'10GBase-ZR-DWDM80-60.61 (ITU 21)'),
(1261,'10GBase-ZR-DWDM80-59.79 (ITU 22)'),
(1262,'10GBase-ZR-DWDM80-58.98 (ITU 23)'),
(1263,'10GBase-ZR-DWDM80-58.17 (ITU 24)'),
(1264,'10GBase-ZR-DWDM80-57.36 (ITU 25)'),
(1265,'10GBase-ZR-DWDM80-56.55 (ITU 26)'),
(1266,'10GBase-ZR-DWDM80-55.75 (ITU 27)'),
(1267,'10GBase-ZR-DWDM80-54.94 (ITU 28)'),
(1268,'10GBase-ZR-DWDM80-54.13 (ITU 29)'),
(1269,'10GBase-ZR-DWDM80-53.33 (ITU 30)'),
(1270,'10GBase-ZR-DWDM80-52.52 (ITU 31)'),
(1271,'10GBase-ZR-DWDM80-51.72 (ITU 32)'),
(1272,'10GBase-ZR-DWDM80-50.92 (ITU 33)'),
(1273,'10GBase-ZR-DWDM80-50.12 (ITU 34)'),
(1274,'10GBase-ZR-DWDM80-49.32 (ITU 35)'),
(1275,'10GBase-ZR-DWDM80-48.51 (ITU 36)'),
(1276,'10GBase-ZR-DWDM80-47.72 (ITU 37)'),
(1277,'10GBase-ZR-DWDM80-46.92 (ITU 38)'),
(1278,'10GBase-ZR-DWDM80-46.12 (ITU 39)'),
(1279,'10GBase-ZR-DWDM80-45.32 (ITU 40)'),
(1280,'10GBase-ZR-DWDM80-44.53 (ITU 41)'),
(1281,'10GBase-ZR-DWDM80-43.73 (ITU 42)'),
(1282,'10GBase-ZR-DWDM80-42.94 (ITU 43)'),
(1283,'10GBase-ZR-DWDM80-42.14 (ITU 44)'),
(1284,'10GBase-ZR-DWDM80-41.35 (ITU 45)'),
(1285,'10GBase-ZR-DWDM80-40.56 (ITU 46)'),
(1286,'10GBase-ZR-DWDM80-39.77 (ITU 47)'),
(1287,'10GBase-ZR-DWDM80-38.98 (ITU 48)'),
(1288,'10GBase-ZR-DWDM80-38.19 (ITU 49)'),
(1289,'10GBase-ZR-DWDM80-37.40 (ITU 50)'),
(1290,'10GBase-ZR-DWDM80-36.61 (ITU 51)'),
(1291,'10GBase-ZR-DWDM80-35.82 (ITU 52)'),
(1292,'10GBase-ZR-DWDM80-35.04 (ITU 53)'),
(1293,'10GBase-ZR-DWDM80-34.25 (ITU 54)'),
(1294,'10GBase-ZR-DWDM80-33.47 (ITU 55)'),
(1295,'10GBase-ZR-DWDM80-32.68 (ITU 56)'),
(1296,'10GBase-ZR-DWDM80-31.90 (ITU 57)'),
(1297,'10GBase-ZR-DWDM80-31.12 (ITU 58)'),
(1298,'10GBase-ZR-DWDM80-30.33 (ITU 59)'),
(1299,'10GBase-ZR-DWDM80-29.55 (ITU 60)'),
(1300,'10GBase-ZR-DWDM80-28.77 (ITU 61)'),
(1316,'1000Base-T (Dell M1000e)'),
(1322,'AC-out'),
(1399,'DC'),
(1424,'1000Base-CX'),
(1425,'10GBase-ER-DWDM40-61.42 (ITU 20)'),
(1426,'10GBase-ER-DWDM40-60.61 (ITU 21)'),
(1427,'10GBase-ER-DWDM40-59.79 (ITU 22)'),
(1428,'10GBase-ER-DWDM40-58.98 (ITU 23)'),
(1429,'10GBase-ER-DWDM40-58.17 (ITU 24)'),
(1430,'10GBase-ER-DWDM40-57.36 (ITU 25)'),
(1431,'10GBase-ER-DWDM40-56.55 (ITU 26)'),
(1432,'10GBase-ER-DWDM40-55.75 (ITU 27)'),
(1433,'10GBase-ER-DWDM40-54.94 (ITU 28)'),
(1434,'10GBase-ER-DWDM40-54.13 (ITU 29)'),
(1435,'10GBase-ER-DWDM40-53.33 (ITU 30)'),
(1436,'10GBase-ER-DWDM40-52.52 (ITU 31)'),
(1437,'10GBase-ER-DWDM40-51.72 (ITU 32)'),
(1438,'10GBase-ER-DWDM40-50.92 (ITU 33)'),
(1439,'10GBase-ER-DWDM40-50.12 (ITU 34)'),
(1440,'10GBase-ER-DWDM40-49.32 (ITU 35)'),
(1441,'10GBase-ER-DWDM40-48.51 (ITU 36)'),
(1442,'10GBase-ER-DWDM40-47.72 (ITU 37)'),
(1443,'10GBase-ER-DWDM40-46.92 (ITU 38)'),
(1444,'10GBase-ER-DWDM40-46.12 (ITU 39)'),
(1445,'10GBase-ER-DWDM40-45.32 (ITU 40)'),
(1446,'10GBase-ER-DWDM40-44.53 (ITU 41)'),
(1447,'10GBase-ER-DWDM40-43.73 (ITU 42)'),
(1448,'10GBase-ER-DWDM40-42.94 (ITU 43)'),
(1449,'10GBase-ER-DWDM40-42.14 (ITU 44)'),
(1450,'10GBase-ER-DWDM40-41.35 (ITU 45)'),
(1451,'10GBase-ER-DWDM40-40.56 (ITU 46)'),
(1452,'10GBase-ER-DWDM40-39.77 (ITU 47)'),
(1453,'10GBase-ER-DWDM40-38.98 (ITU 48)'),
(1454,'10GBase-ER-DWDM40-38.19 (ITU 49)'),
(1455,'10GBase-ER-DWDM40-37.40 (ITU 50)'),
(1456,'10GBase-ER-DWDM40-36.61 (ITU 51)'),
(1457,'10GBase-ER-DWDM40-35.82 (ITU 52)'),
(1458,'10GBase-ER-DWDM40-35.04 (ITU 53)'),
(1459,'10GBase-ER-DWDM40-34.25 (ITU 54)'),
(1460,'10GBase-ER-DWDM40-33.47 (ITU 55)'),
(1461,'10GBase-ER-DWDM40-32.68 (ITU 56)'),
(1462,'10GBase-ER-DWDM40-31.90 (ITU 57)'),
(1463,'10GBase-ER-DWDM40-31.12 (ITU 58)'),
(1464,'10GBase-ER-DWDM40-30.33 (ITU 59)'),
(1465,'10GBase-ER-DWDM40-29.55 (ITU 60)'),
(1466,'10GBase-ER-DWDM40-28.77 (ITU 61)'),
(1469,'virtual port'),
(1588,'empty QSFP'),
(1589,'empty CFP2'),
(1590,'empty CPAK'),
(1591,'empty CXP'),
(1603,'1000Base-T (HP c-Class)'),
(1604,'100Base-TX (HP c-Class)'),
(1642,'10GBase-T'),
(1660,'40GBase-FR'),
(1661,'40GBase-KR4'),
(1662,'40GBase-ER4'),
(1663,'40GBase-SR4'),
(1664,'40GBase-LR4'),
(1668,'empty CFP'),
(1669,'100GBase-SR10'),
(1670,'100GBase-LR4'),
(1671,'100GBase-ER4'),
(1672,'100GBase-SR4'),
(1673,'100GBase-KR4'),
(1674,'100GBase-KP4'),

(1675,'100GBase-LR10'),
(1676,'100GBase-ER10'),
(1677,'100GBase-CR4'),
(1678,'100GBase-CR10'),

(1999,'10GBase-KR')
";
// Add new outer interface types with id < 2000. Values 2000 and up are for
// users' local types.

		$query[] = "INSERT INTO PatchCableOIFCompat (pctype_id, oif_id) VALUES
(13,18),(14,18),(15,18),(16,18),(17,18),(999,18), -- 10Base-T: Cat.3+ TP
(11,1198),(12,1198),(11,1199),(12,1199),          -- 100Base-BX10: 1xSMF
(5,1197),(6,1197),                                -- 100Base-LX10: 2xSMF
(5,1200),(6,1200),                                -- 100Base-EX: 2xSMF
(5,1201),(6,1201),                                -- 100Base-ZX: 2xSMF
(1,1195),(2,1195),(3,1195),(4,1195),              -- 100Base-FX: 2xMMF
(1,1196),(2,1196),(3,1196),(4,1196),              -- 100Base-SX: 2xMMF
(13,19),(14,19),(15,19),(16,19),(17,19),          -- 100Base-TX: Cat.5+ TP
(11,1206),(12,1206),(11,1207),(12,1207),          -- 1000Base-BX10: 1xSMF
(11,1088),(12,1088),(11,1089),(12,1089),          -- 1000Base-BX40: 1xSMF
(11,1090),(12,1090),(11,1091),(12,1091),          -- 1000Base-BX80: 1xSMF
(5,1204),(6,1204),                                -- 1000Base-LX: 2xSMF
(5,1205),(6,1205),                                -- 1000Base-LX10: 2xSMF
(1,1202),(2,1202),(3,1202),(4,1202),              -- 1000Base-SX: 2xMMF
(1,1203),(2,1203),(3,1203),(4,1203),              -- 1000Base-SX+: 2xMMF
(13,24),(14,24),(15,24),(16,24),(17,24),          -- 1000Base-T: Cat.5+ TP
(5,34),(6,34),                                    -- 1000Base-ZX: 2xSMF
(23,1077),                                        -- 1000Base direct attach: shielded
(1,30),(2,30),(3,30),(4,30),                      -- 10GBase-SR: 2xMMF
(5,36),(6,36),                                    -- 10GBase-LR: 2xSMF
(5,35),(6,35),                                    -- 10GBase-ER: 2xSMF
(5,38),(6,38),                                    -- 10GBase-ZR: 2xSMF
(1,39),(2,39),(3,39),(4,39),(5,39),(6,39),        -- 10GBase-LX4: 2xMMF/2xSMF
(1,37),(2,37),(3,37),(4,37),                      -- 10GBase-LRM: 2xMMF
(14,1642),(15,1642),(16,1642),(17,1642),          -- 10GBase-T: Cat.6+ TP
(20,40),                                          -- 10GBase-CX4: coax
(24,1084),(25,1084),(26,1084),(27,1084),          -- 10GBase direct attach: twinax
(18,1663),(19,1663),                              -- 40GBase-SR4: 8xMMF
(5,1664),(6,1664),                                -- 40GBase-LR4: 2xSMF
(5,1662),(6,1662),                                -- 40GBase-ER4: 2xSMF
(5,1660),(6,1660),                                -- 40GBase-FR: 2xSMF
(21,1669),(22,1669),                              -- 100GBase-SR10: 20xMMF
(18,1672),(19,1672),                              -- 100GBase-SR4: 8xMMF
(5,1670),(6,1670),                                -- 100GBase-LR4: 2xSMF
(5,1671),(6,1671),                                -- 100GBase-ER4: 2xSMF
(5,1675),(6,1675),                                -- 100GBase-LR10: 2xSMF
(5,1676),(6,1676)                                 -- 100GBase-ER10: 2xSMF";

		$query[] = "INSERT INTO `ObjectParentCompat` VALUES
(3,13),
(4,1504),
(4,1507),
(1397,1398),
(1502,4),
(1503,8),
(1505,4),
(1505,1504),
(1505,1506),
(1505,1507),
(1506,4),
(1506,1504),
(1787,4),
(1787,8),
(1787,1502)";

		$query[] = "INSERT INTO `PortInterfaceCompat` VALUES
-- SFP-100: empty SFP-100, 100Base-FX, 100Base-SX, 100Base-LX10, 100Base-BX10-D, 100Base-BX10-U, 100Base-EX, 100Base-ZX
(2,1208),(2,1195),(2,1196),(2,1197),(2,1198),(2,1199),(2,1200),(2,1201),
-- GBIC: empty GBIC, 1000Base-T, 1000Base-ZX, 1000Base-EX, 1000Base-SX, 1000Base-SX+, 1000Base-LX, 1000Base-LX10, 1000Base-BX10-D, 1000Base-BX10-U
(3,1078),(3,24),(3,34),(3,42),(3,1202),(3,1203),(3,1204),(3,1205),(3,1206),(3,1207),
-- SFP-1000: empty SFP-1000, 1000Base-T, 1000Base-ZX, 1000Base-EX, 1000Base-SX, 1000Base-SX+, 1000Base-LX, 1000Base-LX10, 1000Base-BX10-D, 1000Base-BX10-U
(4,1077),(4,24),(4,34),(4,42),(4,1202),(4,1203),(4,1204),(4,1205),(4,1206),(4,1207),
-- SFP-1000: 1000Base-BX40-D, 1000Base-BX40-U, 1000Base-BX80-D, 1000Base-BX80-U
(4,1088),(4,1089),(4,1090),(4,1091),
-- XENPAK: empty XENPAK, 10GBase-SR, 10GBase-ER, 10GBase-LR, 10GBase-LRM, 10GBase-ZR, 10GBase-LX4, 10GBase-CX4
(5,1079),(5,30),(5,35),(5,36),(5,37),(5,38),(5,39),(5,40),
-- X2: empty X2, 10GBase-SR, 10GBase-ER, 10GBase-LR, 10GBase-LRM, 10GBase-ZR, 10GBase-LX4, 10GBase-CX4
(6,1080),(6,30),(6,35),(6,36),(6,37),(6,38),(6,39),(6,40),
-- XPAK: empty XPAK, 10GBase-SR, 10GBase-ER, 10GBase-LR, 10GBase-LRM, 10GBase-ZR, 10GBase-LX4, 10GBase-CX4
(7,1081),(7,30),(7,35),(7,36),(7,37),(7,38),(7,39),(7,40),
-- XFP: empty XFP, 10GBase-SR, 10GBase-ER, 10GBase-LR, 10GBase-LRM, 10GBase-ZR, 10GBase-LX4, 10GBase-CX4
(8,1082),(8,30),(8,35),(8,36),(8,37),(8,38),(8,39),(8,40),
-- SFP+: empty SFP+, 10GBase-SR, 10GBase-ER, 10GBase-LR, 10GBase-LRM, 10GBase-ZR, 10GBase-LX4, 10GBase-CX4
(9,1084),(9,30),(9,35),(9,36),(9,37),(9,38),(9,39),(9,40),
-- QSFP+: empty QSFP, 40GBase-FR, 40GBase-ER4, 40GBase-SR4, 40GBase-LR4
(10,1588),(10,1660),(10,1662),(10,1663),(10,1664),
-- CFP: empty CFP, 100GBase-SR10, 100GBase-LR4, 100GBase-ER4, 100GBase-SR4, 100GBase-KR4, 100GBase-KP4, 100GBase-LR10, 100GBase-ER10
(11,1668),(11,1669),(11,1670),(11,1671),(11,1672),(11,1673),(11,1674),(11,1675),(11,1676),
-- CFP2: empty CFP2, 100GBase-SR10, 100GBase-LR4, 100GBase-ER4, 100GBase-SR4, 100GBase-KR4, 100GBase-KP4, 100GBase-LR10, 100GBase-ER10
(12,1589),(12,1669),(12,1670),(12,1671),(12,1672),(12,1673),(12,1674),(12,1675),(12,1676),
-- CPAK: empty CPAK, 100GBase-SR10, 100GBase-LR4, 100GBase-ER4, 100GBase-SR4, 100GBase-KR4, 100GBase-KP4, 100GBase-LR10, 100GBase-ER10
(13,1590),(13,1669),(13,1670),(13,1671),(13,1672),(13,1673),(13,1674),(13,1675),(13,1676),
-- CXP: empty CXP, 100GBase-CR4, 100GBase-CR10
(14,1591),(14,1677),(14,1678),
-- QSFP28: empty QSFP, 40GBase-FR, 40GBase-ER4, 40GBase-SR4, 40GBase-LR4, 100GBase-LR4, 100GBase-ER4, 100GBase-SR4, 100GBase-KR4, 100GBase-KP4
(15,1588),(15,1660),(15,1662),(15,1663),(15,1664),(15,1670),(15,1671),(15,1672),(15,1673),(15,1674),
-- hardwired: AC-in, 100Base-TX, 1000Base-T, RS-232 (RJ-45), virtual bridge, KVM (host), KVM (console), RS-232 (DB-9), RS-232 (DB-25), AC-out, DC, virtual port
(1,16),(1,19),(1,24),(1,29),(1,31),(1,33),(1,446),(1,681),(1,682),(1,1322),(1,1399),(1,1469)";

		$query[] = "INSERT INTO `PortCompat` (`type1`, `type2`) VALUES
(17,17),
(18,18),
(19,19),
(24,24),
(18,19),
(18,24),
(19,24),
(29,29),
(30,30),
(16,1322),
(29,681),
(29,682),
(32,32),
(33,446),
(34,34),
(35,35),
(36,36),
(37,37),
(38,38),
(39,39),
(40,40),
(41,41),
(42,42),
(439,439),
(681,681),
(681,682),
(682,682),
(1077,1077),
(1084,1084),
(1087,1087),
(1088,1089),
(1090,1091),
(1195,1195),
(1196,1196),
(1197,1197),
(1198,1199),
(1200,1200),
(1201,1201),
(1202,1202),
(1203,1203),
(1204,1204),
(1205,1205),
(1206,1207),
(1209,1209),
(1210,1210),
(1211,1211),
(1212,1212),
(1213,1213),
(1214,1214),
(1215,1215),
(1216,1216),
(1217,1217),
(1218,1218),
(1219,1219),
(1220,1220),
(1221,1221),
(1222,1222),
(1223,1223),
(1224,1224),
(1225,1225),
(1226,1226),
(1227,1227),
(1228,1228),
(1229,1229),
(1230,1230),
(1231,1231),
(1232,1232),
(1233,1233),
(1234,1234),
(1235,1235),
(1236,1236),
(1237,1237),
(1238,1238),
(1239,1239),
(1240,1240),
(1241,1241),
(1242,1242),
(1243,1243),
(1244,1244),
(1245,1245),
(1246,1246),
(1247,1247),
(1248,1248),
(1249,1249),
(1250,1250),
(1251,1251),
(1252,1252),
(1253,1253),
(1254,1254),
(1255,1255),
(1256,1256),
(1257,1257),
(1258,1258),
(1259,1259),
(1260,1260),
(1261,1261),
(1262,1262),
(1263,1263),
(1264,1264),
(1265,1265),
(1266,1266),
(1267,1267),
(1268,1268),
(1269,1269),
(1270,1270),
(1271,1271),
(1272,1272),
(1273,1273),
(1274,1274),
(1275,1275),
(1276,1276),
(1277,1277),
(1278,1278),
(1279,1279),
(1280,1280),
(1281,1281),
(1282,1282),
(1283,1283),
(1284,1284),
(1285,1285),
(1286,1286),
(1287,1287),
(1288,1288),
(1289,1289),
(1290,1290),
(1291,1291),
(1292,1292),
(1293,1293),
(1294,1294),
(1295,1295),
(1296,1296),
(1297,1297),
(1298,1298),
(1299,1299),
(1300,1300),
(1316,1316),
(1424,1424),
(1425,1425),
(1426,1426),
(1427,1427),
(1428,1428),
(1429,1429),
(1430,1430),
(1431,1431),
(1432,1432),
(1433,1433),
(1434,1434),
(1435,1435),
(1436,1436),
(1437,1437),
(1438,1438),
(1439,1439),
(1440,1440),
(1441,1441),
(1442,1442),
(1443,1443),
(1444,1444),
(1445,1445),
(1446,1446),
(1447,1447),
(1448,1448),
(1449,1449),
(1450,1450),
(1451,1451),
(1452,1452),
(1453,1453),
(1454,1454),
(1455,1455),
(1456,1456),
(1457,1457),
(1458,1458),
(1459,1459),
(1460,1460),
(1461,1461),
(1462,1462),
(1463,1463),
(1464,1464),
(1465,1465),
(1466,1466),
(1469,1469),
(1399,1399),
(1588,1588),
(1588,1589),
(1588,1590),
(1589,1589),
(1589,1590),
(1590,1590),
(1591,1591),
(1603,1603),
(1660,1660),
(1661,1661),
(1662,1662),
(1663,1663),
(1664,1664),
(1668,1668),
(1669,1669),
(1670,1670),
(1671,1671),
(1672,1672),
(1673,1673),
(1674,1674),
(1675,1675),
(1676,1676),
(1677,1677),
(1678,1678),
(1642,1642),
(1999,1999)";

		// make PortCompat symmetric (insert missing reversed-order pairs)
		$query[] = "INSERT INTO PortCompat SELECT pc1.type2, pc1.type1 FROM PortCompat pc1 LEFT JOIN PortCompat pc2 ON pc1.type1 = pc2.type2 AND pc1.type2 = pc2.type1 WHERE pc2.type1 IS NULL";

		$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES
('IPV4_TREE_SHOW_UNALLOCATED', 'yes', 'string', 'no', 'no', 'yes', 'Show unallocated networks in IPv4 tree'),
('MASSCOUNT','8','uint','no','no','yes','&quot;Fast&quot; form is this many records tall'),
('MAXSELSIZE','30','uint','no','no','yes','&lt;SELECT&gt; lists height'),
('enterprise','MyCompanyName','string','no','no','no','Organization name'),
('ROW_SCALE','2','uint','no','no','yes','Picture scale for rack row display'),
('IPV4_ADDRS_PER_PAGE','256','uint','no','no','yes','IPv4 addresses per page'),
('DEFAULT_RACK_HEIGHT','42','uint','yes','no','yes','Default rack height'),
('DEFAULT_SLB_VS_PORT','','uint','yes','no','yes','Default port of SLB virtual service'),
('DEFAULT_SLB_RS_PORT','','uint','yes','no','yes','Default port of SLB real server'),
('DETECT_URLS','no','string','yes','no','yes','Detect URLs in text fields'),
('RACK_PRESELECT_THRESHOLD','1','uint','no','no','yes','Rack pre-selection threshold'),
('DEFAULT_IPV4_RS_INSERVICE','no','string','no','no','yes','Inservice status for new SLB real servers'),
('AUTOPORTS_CONFIG','4 = 1*33*kvm + 2*24*eth%u;15 = 1*446*kvm','string','yes','no','no','AutoPorts configuration'),
('DEFAULT_OBJECT_TYPE','4','uint','yes','no','yes','Default object type for new objects'),
('SHOW_EXPLICIT_TAGS','yes','string','no','no','yes','Show explicit tags'),
('SHOW_IMPLICIT_TAGS','yes','string','no','no','yes','Show implicit tags'),
('SHOW_AUTOMATIC_TAGS','no','string','no','no','yes','Show automatic tags'),
('IPV4_AUTO_RELEASE','1','uint','no','no','yes','Auto-release IPv4 addresses on allocation'),
('SHOW_LAST_TAB','yes','string','yes','no','yes','Remember last tab shown for each page'),
('EXT_IPV4_VIEW','yes','string','no','no','yes','Extended IPv4 view'),
('TREE_THRESHOLD','25','uint','yes','no','yes','Tree view auto-collapse threshold'),
('IPV4_JAYWALK','no','string','no','no','no','Enable IPv4 address allocations w/o covering network'),
('ADDNEW_AT_TOP','yes','string','no','no','yes','Render \"add new\" line at top of the list'),
('IPV4_TREE_SHOW_USAGE','no','string','no','no','yes','Show address usage in IPv4 tree'),
('PREVIEW_TEXT_MAXCHARS','10240','uint','yes','no','yes','Max chars for text file preview'),
('PREVIEW_TEXT_ROWS','25','uint','yes','no','yes','Rows for text file preview'),
('PREVIEW_TEXT_COLS','80','uint','yes','no','yes','Columns for text file preview'),
('PREVIEW_IMAGE_MAXPXS','320','uint','yes','no','yes','Max pixels per axis for image file preview'),
('VENDOR_SIEVE','','string','yes','no','yes','Vendor sieve configuration'),
('IPV4LB_LISTSRC','false','string','yes','no','no','List source: IPv4 load balancers'),
('IPV4OBJ_LISTSRC','not ({\$typeid_3} or {\$typeid_9} or {\$typeid_10} or {\$typeid_11})','string','yes','no','no','List source: IPv4-enabled objects'),
('IPV4NAT_LISTSRC','{\$typeid_4} or {\$typeid_7} or {\$typeid_8} or {\$typeid_798}','string','yes','no','no','List source: IPv4 NAT performers'),
('ASSETWARN_LISTSRC','{\$typeid_4} or {\$typeid_7} or {\$typeid_8}','string','yes','no','no','List source: objects for that asset tag should be set'),
('NAMEWARN_LISTSRC','{\$typeid_4} or {\$typeid_7} or {\$typeid_8}','string','yes','no','no','List source: objects for that common name should be set'),
('RACKS_PER_ROW','12','uint','yes','no','yes','Racks per row'),
('FILTER_PREDICATE_SIEVE','','string','yes','no','yes','Predicate sieve regex(7)'),
('FILTER_DEFAULT_ANDOR','and','string','no','no','yes','Default list filter boolean operation (or/and)'),
('FILTER_SUGGEST_ANDOR','yes','string','no','no','yes','Suggest and/or selector in list filter'),
('FILTER_SUGGEST_TAGS','yes','string','no','no','yes','Suggest tags in list filter'),
('FILTER_SUGGEST_PREDICATES','yes','string','no','no','yes','Suggest predicates in list filter'),
('FILTER_SUGGEST_EXTRA','yes','string','no','no','yes','Suggest extra expression in list filter'),
('DEFAULT_SNMP_COMMUNITY','public','string','no','no','no','Default SNMP Community string'),
('IPV4_ENABLE_KNIGHT','yes','string','no','no','yes','Enable IPv4 knight feature'),
('TAGS_TOPLIST_SIZE','50','uint','yes','no','yes','Tags top list size'),
('TAGS_QUICKLIST_SIZE','20','uint','no','no','yes','Tags quick list size'),
('TAGS_QUICKLIST_THRESHOLD','50','uint','yes','no','yes','Tags quick list threshold'),
('ENABLE_MULTIPORT_FORM','no','string','no','no','yes','Enable \"Add/update multiple ports\" form'),
('DEFAULT_PORT_IIF_ID','1','uint','no','no','no','Default port inner interface ID'),
('DEFAULT_PORT_OIF_IDS','1=24; 3=1078; 4=1077; 5=1079; 6=1080; 8=1082; 9=1084; 10=1588; 11=1668; 12=1589; 13=1590; 14=1591; 15=1588','string','no','no','no','Default port outer interface IDs'),
('IPV4_TREE_RTR_AS_CELL','no','string','no','no','yes','Show full router info for each network in IPv4 tree view'),
('PROXIMITY_RANGE','0','uint','yes','no','yes','Proximity range (0 is current rack only)'),
('VLANSWITCH_LISTSRC', '', 'string', 'yes', 'no', 'yes', 'List of VLAN running switches'),
('VLANIPV4NET_LISTSRC', '', 'string', 'yes', 'no', 'yes', 'List of VLAN-based IPv4 networks'),
('IPV4_TREE_SHOW_VLAN','yes','string','no','no','yes','Show VLAN for each network in IPv4 tree'),
('DEFAULT_VDOM_ID','','uint','yes','no','yes','Default VLAN domain ID'),
('DEFAULT_VST_ID','','uint','yes','no','yes','Default VLAN switch template ID'),
('8021Q_DEPLOY_MINAGE','300','uint','no','no','no','802.1Q deploy minimum age'),
('8021Q_DEPLOY_MAXAGE','3600','uint','no','no','no','802.1Q deploy maximum age'),
('8021Q_DEPLOY_RETRY','10800','uint','no','no','no','802.1Q deploy retry timer'),
('8021Q_WRI_AFTER_CONFT_LISTSRC','false','string','no','no','no','802.1Q: save device configuration after deploy (RackCode)'),
('8021Q_INSTANT_DEPLOY','no','string','no','no','yes','802.1Q: instant deploy'),
('STATIC_FILTER','yes','string','no','no','yes','Enable Filter Caching'),
('ENABLE_BULKPORT_FORM','yes','string','no','no','yes','Enable \"Bulk Port\" form'),
('CDP_RUNNERS_LISTSRC', '', 'string', 'yes', 'no', 'no', 'List of devices running CDP'),
('LLDP_RUNNERS_LISTSRC', '', 'string', 'yes', 'no', 'no', 'List of devices running LLDP'),
('SHRINK_TAG_TREE_ON_CLICK','yes','string','no','no','yes','Dynamically hide useless tags in tagtree'),
('MAX_UNFILTERED_ENTITIES','0','uint','no','no','yes','Max item count to display on unfiltered result page'),
('SYNCDOMAIN_MAX_PROCESSES','0','uint','yes','no', 'no', 'How many worker proceses syncdomain cron script should create'),
('PORT_EXCLUSION_LISTSRC','{\$typeid_3} or {\$typeid_10} or {\$typeid_11} or {\$typeid_1505} or {\$typeid_1506}','string','yes','no','no','List source: objects without ports'),
('FILTER_RACKLIST_BY_TAGS','yes','string','yes','no','yes','Rackspace: show only racks matching the current object\'s tags'),
('MGMT_PROTOS','ssh: {\$typeid_4}; telnet: {\$typeid_8}','string','yes','no','yes','Mapping of management protocol to devices'),
('SYNC_8021Q_LISTSRC','','string','yes','no','no','List of VLAN switches sync is enabled on'),
('QUICK_LINK_PAGES','depot,ipv4space,rackspace','string','yes','no','yes','List of pages to display in quick links'),
('CACTI_LISTSRC','false','string','yes','no','no','List of object with Cacti graphs'),
('CACTI_RRA_ID','1','uint','no','no','yes','RRA ID for Cacti graphs displayed in RackTables'),
('MUNIN_LISTSRC','false','string','yes','no','no','List of object with Munin graphs'),
('VIRTUAL_OBJ_LISTSRC','1504,1505,1506,1507','string','no','no','no','List source: virtual objects'),
('DATETIME_ZONE','UTC','string','yes','no','yes','Timezone to use for displaying/calculating dates'),
('DATETIME_FORMAT','%Y-%m-%d','string','no','no','yes','PHP strftime() format to use for date output'),
('SEARCH_DOMAINS','','string','yes','no','yes','DNS domain list (comma-separated) to search in FQDN attributes'),
('8021Q_EXTSYNC_LISTSRC','false','string','yes','no','no','List source: objects with extended 802.1Q sync'),
('8021Q_MULTILINK_LISTSRC','false','string','yes','no','no','List source: IPv4/IPv6 networks allowing multiple VLANs from same domain'),
('REVERSED_RACKS_LISTSRC', 'false', 'string', 'yes', 'no', 'no', 'List of racks with reversed (top to bottom) units order'),
('NEAREST_RACKS_CHECKBOX', 'yes', 'string', 'yes', 'no', 'yes', 'Enable nearest racks in port list filter by default'),
('SHOW_OBJECTTYPE', 'yes', 'string', 'no', 'no', 'yes', 'Show object type column on depot page'),
('DB_VERSION','${db_version}','string','no','yes','no','Database version.')";

		$query[] = "INSERT INTO `Script` VALUES ('RackCode','allow {\$userid_1}')";

		$tmpstr = 'INSERT INTO VLANValidID (vlan_id) VALUES ';
		$sep = '';
		for ($i = 1; $i <= 4094; $i++)
		{
			$tmpstr .= "${sep}(${i})";
			$sep = ', ';
		}
		$query[] = $tmpstr;
		unset ($i);
		unset ($sep);
		unset ($tmpstr);

	return $query;
	}
}

?>
