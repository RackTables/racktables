<?php

// This script is intended for execution through a web-browser, e.g.:
// https://example.com/racktables/install.php
// See README file for more information.

$stepfunc[1] = 'not_already_installed';
$stepfunc[2] = 'platform_is_ok';
$stepfunc[3] = 'init_config';
$stepfunc[4] = 'init_database_static';
$stepfunc[5] = 'init_database_dynamic';
$stepfunc[6] = 'congrats';
$dbxlink = NULL;

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
require_once ('inc/dictionary.php');
header ('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title><?php echo $title; ?></title>
<link rel=stylesheet type='text/css' href='css/pi.css' />
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
// Check if the software is already installed.
function not_already_installed()
{
	@include ('inc/secret.php');
	if (isset ($pdo_dsn))
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
	if (!is_writable ('inc/secret.php'))
	{
		echo "The inc/secret.php file is not writable by web-server. Make sure it is.";
		echo "The following commands should suffice:<pre>touch inc/secret.php; chmod 666 inc/secret.php</pre>";
		echo 'Fedora Linux with SELinux may require this file to be owned by specific user (apache) and/or executing "setenforce 0" for the time of installation. ';
		echo 'SELinux may be turned back on with "setenforce 1" command.<br>';
		return FALSE;
	}
	if
	(
		!isset ($_REQUEST['save_config']) or
		empty ($_REQUEST['mysql_host']) or
		empty ($_REQUEST['mysql_db']) or
		empty ($_REQUEST['mysql_username']) or
		empty ($_REQUEST['mysql_password'])
	)
	{
		echo "<input type=hidden name=save_config value=1>\n";
		echo '<h3>Hint on setting up a database:</h3><pre>';
		echo "mysql&gt; CREATE DATABASE racktables_db CHARACTER SET utf8 COLLATE utf8_general_ci;\n";
		echo "mysql&gt; grant all privileges on racktables_db.* to racktables_user@localhost identified by 'MY_SECRET_PASSWORD';\n</pre>";
		echo '<table>';
		echo "<tr><td><label for=mysql_host>MySQL host:</label></td>";
		echo "<td><input type=text name=mysql_host id=mysql_host value=localhost></td></tr>\n";
		echo "<tr><td><label for=mysql_host>database:</label></td>";
		echo "<td><input type=text name=mysql_db id=mysql_db value=racktables_db></td></tr>\n";
		echo "<tr><td><label for=mysql_username>username:</label></td>";
		echo "<td><input type=text name=mysql_username value=racktables_user></td></tr>\n";
		echo "<tr><td><label for=mysql_password>password:</label></td>";
		echo "<td><input type=password name=mysql_password></td></tr>\n";
		echo '</table>';
		return FALSE;
	}
	$pdo_dsn = 'mysql:host=' . $_REQUEST['mysql_host'] . ';dbname=' . $_REQUEST['mysql_db'];
	try
	{
		$dbxlink = new PDO ($pdo_dsn, $_REQUEST['mysql_username'], $_REQUEST['mysql_password']);
	}
	catch (PDOException $e)
	{
		echo "<input type=hidden name=save_config value=1>\n";
		echo '<table>';
		echo "<tr><td><label for=mysql_host>MySQL host:</label></td>";
		echo "<td><input type=text name=mysql_host id=mysql_host value='" . $_REQUEST['mysql_host'] . "'></td></tr>\n";
		echo "<tr><td><label for=mysql_host>database:</label></td>";
		echo "<td><input type=text name=mysql_db id=mysql_db value='" . $_REQUEST['mysql_db'] . "'></td></tr>\n";
		echo "<tr><td><label for=mysql_username>username:</label></td>";
		echo "<td><input type=text name=mysql_username value='" . $_REQUEST['mysql_username'] . "'></td></tr>\n";
		echo "<tr><td><label for=mysql_password>password:</label></td>";
		echo "<td><input type=password name=mysql_password value='" . $_REQUEST['mysql_password'] . "'></td></tr>\n";
		echo "<tr><td colspan=2>The above parameters did not work. Check and try again.</td></tr>\n";
		echo '</table>';
		return FALSE;
	}

	$conf = fopen ('inc/secret.php', 'w+');
	if ($conf === FALSE)
	{
		echo "Error: failed to open inc/secret.php for writing";
		return FALSE;
	}
	fwrite ($conf, "<?php\n/* This file has been generated automatically by RackTables installer.\n");
	fwrite ($conf, " * you shouldn't normally edit it unless your database setup has changed.\n");
	fwrite ($conf, " */\n");
	fwrite ($conf, "\$pdo_dsn = '${pdo_dsn}';\n");
	fwrite ($conf, "\$db_username = '" . $_REQUEST['mysql_username'] . "';\n");
	fwrite ($conf, "\$db_password = '" . $_REQUEST['mysql_password'] . "';\n\n");
	fwrite ($conf, <<<ENDOFTEXT
// Default setting is to authenticate users locally, but it is possible to
// employ existing LDAP or Apache userbase. Uncommenting below two lines MAY
// help in switching authentication to LDAP completely.
// More info: http://sourceforge.net/apps/mediawiki/racktables/index.php?title=RackTablesAdminGuide
#\$user_auth_src = 'ldap';
#\$require_local_account = FALSE;

// This is only necessary for 'ldap' authentication source
\$LDAP_options = array
(
	'server' => 'some.server',
	'domain' => 'some.domain',
#	'search_dn' => 'ou=people,O=YourCompany',
	'search_attr' => 'uid',
#	'displayname_attrs' => 'givenname familyname',

// LDAP cache, values in seconds. Refresh, retry and expiry values are
// treated exactly as those for DNS SOA record. Example values 300-15-600:
// unconditionally remeber successful auth for 5 minutes, after that still
// permit user access, but try to revalidate username and password on the
// server (not more often, than once in 15 seconds). After 10 minutes of
// unsuccessful retries give up and deny access, so someone goes to fix
// LDAP server.
	'cache_refresh' => 300,
	'cache_retry' => 15,
	'cache_expiry' => 600,
);


ENDOFTEXT
);
	fwrite ($conf, "?>\n");
	fclose ($conf);
	echo "The configuration file has been written successfully.<br>";
	return TRUE;
}

function connect_to_db ()
{
	require ('inc/secret.php');
	global $dbxlink;
	try
	{
		$dbxlink = new PDO ($pdo_dsn, $db_username, $db_password);
	}
	catch (PDOException $e)
	{
		die ('Error connecting to the database');
	}
}

function init_database_static ()
{
	connect_to_db ();
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
	echo "<tr><th>file</th><th>queries</th><th>errors</th></tr>";
	$errlist = array();
	foreach (array ('structure', 'dictbase') as $part)
	{
		$filename = "install/init-${part}.sql";
		echo "<tr><td>${filename}</td>";
		$f = fopen ("install/init-${part}.sql", 'r');
		if ($f === FALSE)
		{
			echo "Failed to open install/init-${part}.sql for reading";
			return FALSE;
		}
		$longq = '';
		while (!feof ($f))
		{
			$line = fgets ($f);
			if ('--' == substr ($line, 0, 2))
				continue;
			$longq .= $line;
		}
		fclose ($f);
		$nq = $nerrs = 0;
		foreach (preg_split ("/;\s*\n/", $longq) as $query)
		{
			$query = trim($query);
			if (empty ($query))
				continue;
			$nq++;
			if ($dbxlink->exec ($query) === FALSE)
			{
				$nerrs++;
				$errlist[] = $query;
			}
		}
		echo "<td>${nq}</td><td>${nerrs}</td></tr>\n";
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
	if (count ($errlist))
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
	connect_to_db();
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
	echo "<a href='http://sourceforge.net/apps/mediawiki/racktables/index.php?title=RackTablesAdminGuide'>";
	echo "wiki</a> and a ";
	echo "<a href='http://www.freelists.org/list/racktables-users'>mailing list</a> for users. Have fun.<br>";
	return TRUE;
}

?>
