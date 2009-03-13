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
	require 'inc/init.php';
	global $root;
	header ("Location: " . $root);
	exit;
}
$title = "RackTables installation: step ${step} of " . count ($stepfunc);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel=stylesheet type='text/css' href=pi.css />
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
	echo "<input type=submit value='proceed'>";
}
else
{
	$next_step = $step;
	echo "<input type=submit value='retry'>";
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

// Check for PHP extensions.
function platform_is_ok ()
{
	$nerrs = 0;
	echo "<table border=1><tr><th>check item</th><th>result</th></tr>\n";

	echo '<tr><td>PDO extension</td>';
	if (class_exists ('PDO'))
		echo '<td class=msg_success>Ok';
	else
	{
		echo '<td class=msg_error>not found';
		$nerrs++;
	}
	echo '</td></tr>';

	echo '<tr><td>PDO-MySQL</td>';
	if (defined ('PDO::MYSQL_ATTR_READ_DEFAULT_FILE'))
		echo '<td class=msg_success>Ok';
	else
	{
		echo '<td class=msg_error>not found';
		$nerrs++;
	}
	echo '</td></tr>';

	echo '<tr><td>hash functions</td>';
	if (function_exists ('hash_algos'))
		echo '<td class=msg_success>Ok';
	else
	{
		echo '<td class=msg_error>not found';
		$nerrs++;
	}
	echo '</td></tr>';

	echo '<tr><td>SNMP extension</td>';
	if (defined ('SNMP_NULL'))
		echo '<td class=msg_success>Ok';
	else
		echo '<td class=msg_warning>Not found. Live SNMP tab will not function properly until the extension is installed.';
	echo '</td></tr>';

	echo '<tr><td>GD functions</td>';
	if (defined ('IMG_PNG'))
		echo '<td class=msg_success>Ok';
	else
	{
		echo '<td class=msg_error>not found';
		$nerrs++;
	}
	echo '</td></tr>';

	echo '<tr><td>HTTP scheme</td>';
	if (!empty($_SERVER['HTTPS']))
		echo '<td class=msg_success>HTTPs';
	else
		echo '<td class=msg_warning>HTTP (all your passwords will be transmitted in cleartext)';
	echo '</td></tr>';

	echo '<tr><td>Multibyte string extension</td>';
	if (defined ('MB_CASE_LOWER'))
		echo '<td class=msg_success>Ok';
	else
	{
		echo '<td class=msg_error>not found';
		$nerrs++;
	}
	echo '</td></tr>';

	echo '<tr><td>LDAP extension</td>';
	if (defined ('LDAP_OPT_DEREF'))
		echo '<td class=msg_success>Ok';
	else
	{
		echo '<td class=msg_warning>not found, LDAP authentication will not work';
	}
	echo '</td></tr>';

	echo "</table>\n";
	return !$nerrs;
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
		echo "The following commands should suffice:<pre>touch inc/secret.php\nchmod 666 inc/secret.php</pre>";
		echo 'Fedora Linux with SELinux may require this file to be owned by specific user (apache) and/or executing "setenforce 0" for the time of installation. ';
		echo 'SELinux may be turned back on with "setenforce 1" command.';
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
		echo '<table>';
		echo "<tr><td><label for=mysql_host>MySQL host:</label></td>";
		echo "<td><input type=text name=mysql_host id=mysql_host value=localhost></td></tr>\n";
		echo "<tr><td><label for=mysql_host>database:</label></td>";
		echo "<td><input type=text name=mysql_db id=mysql_db value=racktables></td></tr>\n";
		echo "<tr><td><label for=mysql_username>username:</label></td>";
		echo "<td><input type=text name=mysql_username></td></tr>\n";
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

	// Make sure InnoDB is supported
	require_once 'inc/database.php';
	if (!isInnoDBSupported ($dbxlink))
	{
		echo 'Error: InnoDB support is disabled.  See the README for details.';
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
	fwrite ($conf, "// More info http://racktables.org/trac/wiki/RackTablesUserAuthentication\n");
	fwrite ($conf, "\$user_auth_src = 'database';\n");
	fwrite ($conf, "\$require_valid_user = TRUE;\n\n");
	fwrite ($conf, "// This is only necessary for 'ldap' authentication source\n");
	fwrite ($conf, "\$ldap_server = 'some.server';\n");
	fwrite ($conf, "\$ldap_domain = 'some.domain';\n\n");
	fwrite ($conf, "#\$ldap_search_dn = 'ou=people,O=YourCompany';\n");
	fwrite ($conf, "\$ldap_search_attr = 'uid';\n");
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
	foreach (array ('structure', 'dictbase', 'dictvendors') as $part)
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
			if (ereg ('^--', $line))
				continue;
			$longq .= $line;
		}
		fclose ($f);
		$nq = $nerrs = 0;
		foreach (explode (";\n", $longq) as $query)
		{
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
		$query = "INSERT INTO `UserAccount` (`user_id`, `user_name`, `user_password_hash`, `user_realname`) " .
			"VALUES (1,'admin',sha1('${_REQUEST['password']}'),'RackTables Administrator')";
		$result = $dbxlink->exec ($query);
		echo "Administrator password has been set successfully.<br>";
		return TRUE;
	}
}

function congrats ()
{
	echo 'Congratulations! RackTables installation is complete. After pressing Proceed you will ';
	echo 'enter the system. Authenticate with <strong>admin</strong> username.<br>';
	echo "RackTables web-site runs some <a href='http://racktables.org/trac/wiki'>wiki</a> pages ";
	echo "and <a href='http://racktables.org/trac/report/1'>a bug tracker</a>.<br>We have also got ";
	echo "a <a href='http://www.freelists.org/list/racktables-users'>mailing list</a> for users. Have fun.<br>";
	return TRUE;
}

?>
