<?php

$relnotes = array
(
	'0.17.0' => "<font color=red><strong>Release notes for ${batchid}</strong></font><br>" .
		"Another change is the addition of support for file uploads.  Files are stored<br>" .
		"in the database.  There are several settings in php.ini which you may need to modify:<br>" .
		"    file_uploads        - needs to be On<br>" .
		"    upload_max_filesize - max size for uploaded files<br>" .
		"    post_max_size       - max size of all form data submitted via POST (including files)<br>",
);

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
		'0.16.5',
		'0.16.6',
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

// Upgrade batches are named exactly as the release where they first appear.
// That is simple, but seems sufficient for beginning.
function executeUpgradeBatch ($batchid)
{
	$query = array();
	global $dbxlink;
	switch ($batchid)
	{
		case '0.16.5':
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('IPV4_TREE_SHOW_USAGE','yes','string','no','no','Show address usage in IPv4 tree')";
			$query[] = "update Config set varvalue = '0.16.5' where varname = 'DB_VERSION'";
			break;
		case '0.16.6':
			$query[] = "update Config set varvalue = '0.16.6' where varname = 'DB_VERSION'";
			break;
		case '0.17.0':
			// create tables for storing files (requires InnoDB support)
			if (!isInnoDBSupported ())
			{
				showError ("Cannot upgrade because InnoDB tables are not supported by your MySQL server. See the README for details.", __FILE__);
				die;
			}
			// Many dictionary changes were made... remove all dictvendor entries and install fresh.
			// Take care not to erase locally added records. 0.16.x ends with max key 797
			$query[] = 'DELETE FROM Dictionary WHERE ((chapter_no BETWEEN 11 AND 14) or (chapter_no BETWEEN 16 AND 19) ' .
				'or (chapter_no BETWEEN 21 AND 24)) and dict_key <= 797';
			$f = fopen ("install/init-dictvendors.sql", 'r');
			if ($f === FALSE)
			{
				showError ("Failed to open install/init-dictvendors.sql for reading");
				die;
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
			foreach (explode (";\n", $longq) as $dict_query)
			{
				if (empty ($dict_query))
					continue;
				$query[] = $dict_query;
			}

			// schema changes for file management
			$query[] = "
CREATE TABLE `File` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) NOT NULL,
  `type` char(255) NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL,
  `mtime` datetime NOT NULL,
  `atime` datetime NOT NULL,
  `contents` longblob NOT NULL,
  `comment` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB";
			$query[] = "
CREATE TABLE `FileLink` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `file_id` int(10) unsigned NOT NULL,
  `entity_type` enum('ipv4net','ipv4rspool','ipv4vs','object','rack','user') NOT NULL default 'object',
  `entity_id` int(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FileLink-file_id` (`file_id`),
  CONSTRAINT `FileLink-File_fkey` FOREIGN KEY (`file_id`) REFERENCES `File` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB";
			$query[] = "ALTER TABLE TagStorage MODIFY COLUMN target_realm enum('file','ipv4net','ipv4rspool','ipv4vs','object','rack','user') NOT NULL default 'object'";

			// add network security as an object type
			$query[] = "INSERT INTO `Chapter` (`chapter_no`, `sticky`, `chapter_name`) VALUES (24,'no','network security models')";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (798,1,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (798,2,24)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (798,3,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (798,5,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (798,14,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (798,16,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (798,17,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (798,18,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (798,20,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (798,21,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (798,22,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (798,24,0)";
			$query[] = "UPDATE Dictionary SET dict_value = 'Network switch' WHERE dict_key = 8";
			$query[] = 'alter table IPBonds rename to IPv4Allocation';
			$query[] = 'alter table PortForwarding rename to IPv4NAT';
			$query[] = 'alter table IPRanges rename to IPv4Network';
			$query[] = 'alter table IPAddress rename to IPv4Address';
			$query[] = 'alter table IPLoadBalancer rename to IPv4LB';
			$query[] = 'alter table IPRSpool rename to IPv4RSPool';
			$query[] = 'alter table IPRealServer rename to IPv4RS';
			$query[] = 'alter table IPVirtualServer rename to IPv4VS';
			$query[] = "UPDATE Config SET varvalue = '0.17.0' WHERE varname = 'DB_VERSION'";
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
	if (isset ($relnotes[$batchid]))
		echo $relnotes[$batchid];
}

echo '<br>Database version == ' . getDatabaseVersion();
echo "<p align=justify>Your database seems to be up-to-date. " .
	"Now the best thing to do would be to follow to the <a href='${root}'>main page</a> " .
	"and explore your data. Have a nice day.</p>";

?>
