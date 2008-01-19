<?php
$stepfunc[1] = 'not_already_installed';
$stepfunc[2] = 'platform_is_ok';
$stepfunc[3] = 'init_config';
$stepfunc[4] = 'init_database';
$stepfunc[5] = 'congrats';

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
	include ('inc/secret.php');
	if (isset ($pdo_dsn))
	{
		echo 'Your configuration file exists and seems to hold necessary data already.<br>';
		return FALSE;
	}
	else
	{
		echo 'There seem to be no existing installation here.<br>';
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
	fwrite ($conf, "\$db_password = '" . $_REQUEST['mysql_password'] . "';\n");
	fwrite ($conf, "?>\n");
	fclose ($conf);
	echo "The configuration file has been written successfully.<br>";
	return TRUE;
}

function init_database ()
{
	echo 'Initializing the database...<br>';
	foreach (array ('structure', 'dictbase', 'dictvendors') as $part)
	{
		echo $part;
	}
	return TRUE;
}

function congrats ()
{
	echo 'Congratulations! RackTables installation is complete. Press Next to open your main page.';
	return TRUE;
}

?>
