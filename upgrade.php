<?php

// At the moment we assume, that for any two releases we can
// sequentally execute all batches, that separate them, and
// nothing will break. If this changes one day, the function
// below will have to generate smarter upgrade paths, while
// the upper layer will remain the same.
// Returning an empty array means that no upgrade is necessary.
function getDBUpgradePath ($v1, $v2)
{
	$versionhistory = array
	(
		'0.16.4',
		'0.17.0',
	);
	if (!in_array ($v1, $versionhistory) || !in_array ($v2, $versionhistory))
	{
		showError ("An upgrade path has been requested for versions '${v1}' and '${v2}', " .
		  "and at least one of those isn't known to me.", __FILE__);
		die;
	}
	$skip = TRUE;
	$path = array();
	// Now collect all versions > $v1 and <= $v2
	foreach ($versionhistory as $v)
	{
		if ($v == $v1)
		{
			$skip = FALSE;
			continue;
		}
		if ($skip)
			continue;
		$path[] = $v;
		if ($v == $v2)
			break;
	}
	return $path;
}

function printReleaseNotes ($batchid)
{
	switch ($batchid)
	{
		default:
			break;
	}
}

// Upgrade batches are name exactly as the release where they first appear.
// That simple, but seems sufficient for beginning.
function executeUpgradeBatch ($batchid)
{
	$query = array();
	global $dbxlink;
	switch ($batchid)
	{
		case '0.17.0':
			$query[] = "update Config set varvalue = '0.17.0' where varname = 'DB_VERSION'";
			break;
		default:
			showError ("executeUpgradeBatch () failed, because batch '${batchid}' isn't defined", __FILE__);
			die;
			break;
	}
	$failures = array();
	$ndots = 0;
	echo "<pre>Executing database upgrade batch '${batchid}':\n";
	foreach ($query as $q)
	{
		$result = $dbxlink->query ($q);
		if ($result != NULL)
			echo '.';
		else
		{
			echo '!';
			$errorInfo = $dbxlink->errorInfo();
			$failures[] = array ($q, $errorInfo[2]);
		}
		if (++$ndots == 50)
		{
			echo "\n";
			flush();
			$ndots = 0;
		}
	}
	echo '<br>';
	if (!count ($failures))
		echo "No errors!\n";
	else
	{
		echo "The following queries failed:\n<font color=red>";
		foreach ($failures as $f)
		{
			list ($q, $i) = $f;
			echo "${q} // ${i}\n";
		}
	}
	echo '</font></pre>';
}

// ******************************************************************
//
//                  Execution starts here
//
// ******************************************************************

$root = (empty($_SERVER['HTTPS'])?'http':'https').
	'://'.
	(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:($_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']=='80'?'':$_SERVER['SERVER_PORT']))).
	dirname($_SERVER['PHP_SELF']);
if (substr ($root, -1) != '/')
	$root .= '/';

// The below will be necessary as long as we rely on showError()
require_once 'inc/interface.php';

require_once 'inc/config.php';
require_once 'inc/database.php';
if (file_exists ('inc/secret.php'))
	require_once 'inc/secret.php';
else
	die ("Database connection parameters are read from inc/secret.php file, " .
		"which cannot be found.\nCopy provided inc/secret-sample.php to " .
		"inc/secret.php and modify to your setup.\n\nThen reload the page.");

try
{
	$dbxlink = new PDO ($pdo_dsn, $db_username, $db_password);
}
catch (PDOException $e)
{
	die ("Database connection failed:\n\n" . $e->getMessage());
}

// Now we need to be sure that the current user is the administrator.
// The rest doesn't matter within this context.
// We still continue to use the current authenticator though, but this will
// last only till the UserAccounts remains the same. After that this file
// will have to dig into the DB for the user accounts.
require_once 'inc/auth.php';

// 1. This didn't fail sanely, because getUserAccounts() depended on showError()
// 2. getUserAccounts() doesn't work for old DBs since 0.16.0. Let's have own
// copy until it breaks too.

function getUserAccounts_local ()
{
	global $dbxlink;
	$query = 'select user_id, user_name, user_password_hash from UserAccount order by user_name';
	if (($result = $dbxlink->query ($query)) == NULL)
		die ('SQL query failed in ' . __FUNCTION__);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach (array ('user_id', 'user_name', 'user_password_hash') as $cname)
			$ret[$row['user_name']][$cname] = $row[$cname];
	return $ret;
}

$accounts = getUserAccounts_local();

// Only administrator is always authenticated locally, so reject others
// for authenticate() to succeed.

if
(
	!isset ($_SERVER['PHP_AUTH_USER']) or
	!isset ($_SERVER['PHP_AUTH_PW']) or
	$accounts[$_SERVER['PHP_AUTH_USER']]['user_id'] != 1 or
	!authenticated_via_database (escapeString ($_SERVER['PHP_AUTH_USER']), escapeString ($_SERVER['PHP_AUTH_PW']))
)
{
	header ('WWW-Authenticate: Basic realm="RackTables upgrade"');
	header ('HTTP/1.0 401 Unauthorized');
	showError ('You must be authenticated as an administrator to complete the upgrade.', __FILE__);
	die;
}

$dbver = getDatabaseVersion();
echo 'Code version: ' . CODE_VERSION . '<br>';
echo 'Database version: ' . $dbver . '<br>';
if ($dbver == CODE_VERSION)
{
	die ("<p align=justify>No action is necessary. " .
		"Proceed to the <a href='${root}'>main page</a>, " .
		"check your data and have a nice day.</p>");
}

foreach (getDBUpgradePath ($dbver, CODE_VERSION) as $batchid)
{
	executeUpgradeBatch ($batchid);
	printReleaseNotes ($batchid);
}

echo '<br>Database version == ' . getDatabaseVersion();
echo "<p align=justify>Your database seems to be up-to-date. " .
	"Now the best thing to do would be to follow to the <a href='${root}'>main page</a> " .
	"and explore your data. Have a nice day.</p>";

?>
