<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title>RackTables installation</title>
<link rel=stylesheet type='text/css' href=pi.css />
</head>
<body>
<?php

function already_installed()
{
	return FALSE;
}

function test_install_step ($stepno)
{
	switch ($stepno)
	{
		// Step 0. Check if the software is already installed.
		case 0:
			return 1;
			break;
		// Step 1. Check for PHP extensions.
		case 1:
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
			if (defined ('GD_VERSION'))
				echo '<td class=msg_success>Ok';
			else
			{
				echo '<td class=msg_error>not found';
				$nerrs++;
			}
			echo '</td></tr>';

			echo "</table>\n";
			return !$nerrs;
			break;
		// Step 2. Check that we can write to configuration file.
		case 2:
			return 1;
			break;
		// Step 3. Ask for DB connection paramaters and test
		// the connection. Neither save the parameters nor allow
		// going further until we succeed with the given 
		// credentials.
		case 3:
			return 1;
			break;
		default:
			die ("Unexpected argument '${stepno}'");
	}
}

//********************************** START **************
if (isset ($_REQUEST['step']))
	$next_step = $_REQUEST['step'];
elseif (!already_installed())
	$next_step = 1;
else
	die ('Already installed.');

$result = test_install_step ($next_step);
switch ($result)
{
	case -1: // fail
		
		break;
	case 0: // retry
		break;
	case 1: // advance
}

?>
</body>
</html>
