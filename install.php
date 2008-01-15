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
echo "<h1>${title}</h1>";
//
// Check if the software is already installed.
function not_already_installed()
{
	return TRUE;
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
	return TRUE;
}

function init_database ()
{
	echo 'Initializing the database...<br>';
	return TRUE;
}

function congrats ()
{
	echo 'Congratulations! RackTables installation is complete. Press Next to open your main page.';
	return TRUE;
}

if ($stepfunc[$step] ())
	echo "<a href='?step=" . ($step + 1) . "'>next</a>";
else
	echo "<a href='?step=${next_step}'>retry</a>";

?>
</center>
</body>
</html>
