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
	$root = (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
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
	if ($found_secret_file and isset ($pdo_dsn))
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
		echo "GRANT ALL PRIVILEGES ON racktables_db.* TO racktables_user@localhost IDENTIFIED BY 'MY_SECRET_PASSWORD';\n</pre></div>";
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
		echo "The following commands should suffice:<pre>touch '$path_to_secret_php'; chmod 666 '$path_to_secret_php'</pre>";
		echo 'Fedora Linux with SELinux may require this file to be owned by specific user (apache) and/or executing "setenforce 0" for the time of installation. ';
		echo 'SELinux may be turned back on with "setenforce 1" command.<br>';
		return FALSE;
	}
	if (! array_key_exists ('save_config', $_REQUEST))
	{
		print_form();
		return FALSE;
	}
	if (empty ($_REQUEST['mysql_db']) or empty ($_REQUEST['mysql_username']))
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
	if ($_REQUEST['conn'] == 'conn_tcp' and empty ($_REQUEST['mysql_host']))
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
	if ($_REQUEST['conn'] == 'conn_unix' and empty ($_REQUEST['mysql_socket']))
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
		if (!empty ($_REQUEST['mysql_port']) and $_REQUEST['mysql_port'] != '3306')
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
	fwrite ($conf, "# Setting MySQL client buffer size may be required to make downloading work for\n");
	fwrite ($conf, "# larger files, but it does not work with mysqlnd.\n");
	fwrite ($conf, "# \$pdo_bufsize = 50 * 1024 * 1024;\n\n");
	fwrite ($conf, <<<ENDOFTEXT

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
	if (is_callable('posix_getpwuid') and is_callable('posix_geteuid'))
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
	if (! is_writable ($path_to_secret_php) and is_readable ($path_to_secret_php))
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
	echo "<pre>chown $uname:nogroup secret.php; chmod 400 secret.php</pre>";
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
		{
			if (empty ($q))
				continue;
			$stmt = $dbxlink->prepare($q);
			try
			{
				$stmt->execute();
				$stmt->closeCursor();
				$nq++;
			}
			catch (PDOException $e)
			{
				$nerrs++;
				$errorInfo = $dbxlink->errorInfo();
				$failures[] = array ($q, $errorInfo[2]);
			}
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
	if (!isset ($_REQUEST['password']) or empty ($_REQUEST['password']))
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
	$query = array();
	switch ($name)
	{
	case 'structure':
		$query[] = file_get_contents('../schema/baseline.sql');
		break;
	case 'dictbase':
		$query[] = file_get_contents('../schema/data.sql');
		$v = CODE_VERSION;
		$query[] = "update config set varvalue='${v}' where varname = 'DB_VERSION'";
		break;
	}
	return $query;
}

?>
