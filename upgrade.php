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
			// create tables for storing files (requires InnoDB support)
			if (!isInnoDBSupported ())
			{
				die ('<b>Cannot upgrade because InnoDB tables are not supported by your MySQL server. See the README for details.</b>');
			}
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
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (1,798,'Network security')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,799,'Cisco%GPASS%ASA 5505')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,800,'Cisco%GPASS%ASA 5510')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,801,'Cisco%GPASS%ASA 5520')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,802,'Cisco%GPASS%ASA 5540')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,803,'Cisco%GPASS%ASA 5550')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,804,'Cisco%GPASS%ASA 5580-20')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,805,'Cisco%GPASS%ASA 5580-40')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,806,'[[Cisco%GPASS%IDS 4215 | http://www.cisco.com/en/US/products/hw/vpndevc/ps4077/ps5367/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,807,'[[Cisco%GPASS%IDS 4240 | http://www.cisco.com/en/US/products/ps5768/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,808,'[[Cisco%GPASS%IDS 4255 | http://www.cisco.com/en/US/products/ps5769/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,809,'[[Cisco%GPASS%IDS 4260 | http://www.cisco.com/en/US/products/ps6751/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,810,'[[Cisco%GPASS%IDS 4270 | http://www.cisco.com/en/US/products/ps9157/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,811,'Foundry%GPASS%SecureIron 100')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,812,'Foundry%GPASS%SecureIron 100C')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,813,'Foundry%GPASS%SecureIron 300')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,814,'Foundry%GPASS%SecureIron 300C')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,815,'Foundry%GPASS%SecureIronLS 100-4802')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,816,'Foundry%GPASS%SecureIronLS 300-32GC02')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,817,'Foundry%GPASS%SecureIronLS 300-32GC10G')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,818,'[[D-Link%GPASS%DFL-1600 | http://www.dlink.com/products/?sec=0&pid=454]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,819,'[[D-Link%GPASS%DFL-M510 | http://www.dlink.com/products/?sec=0&pid=455]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,820,'[[Extreme Networks%GPASS%Sentriant AG200 | http://www.extremenetworks.com/products/sentriant-ag200.aspx]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,821,'[[Extreme Networks%GPASS%Sentriant NG300 | http://www.extremenetworks.com/products/sentriant-ng300.aspx]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,822,'[[Force10%GPASS%P-Series | http://www.force10networks.com/products/pseries.asp]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,823,'[[Juniper%GPASS%SSG 140 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/ssg_140/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,824,'[[Juniper%GPASS%SSG 320 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/ssg_300_series/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,825,'[[Juniper%GPASS%SSG 350 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/ssg_300_series/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,826,'[[Juniper%GPASS%SSG 520 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/ssg_500_series/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,827,'[[Juniper%GPASS%SSG 550 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/ssg_500_series/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,828,'[[Juniper%GPASS%ISG 1000 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/isg_series_slash_gprs/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,829,'[[Juniper%GPASS%ISG 2000 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/isg_series_slash_gprs/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,830,'[[Juniper%GPASS%NetScreen 5200 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/isg_series_slash_gprs/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,831,'[[Juniper%GPASS%NetScreen 5400 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/isg_series_slash_gprs/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,832,'[[Juniper%GPASS%SRX 5600 | http://www.juniper.net/products_and_services/srx_series/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,833,'[[Juniper%GPASS%SRX 5800 | http://www.juniper.net/products_and_services/srx_series/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,834,'[[SonicWall%GPASS%PRO 1260 | http://www.sonicwall.com/us/products/PRO_1260.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,835,'[[SonicWall%GPASS%PRO 2040 | http://www.sonicwall.com/us/products/PRO_2040.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,836,'[[SonicWall%GPASS%PRO 3060 | http://www.sonicwall.com/us/products/PRO_3060.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,837,'[[SonicWall%GPASS%PRO 4060 | http://www.sonicwall.com/us/products/PRO_4060.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,838,'[[SonicWall%GPASS%PRO 4100 | http://www.sonicwall.com/us/products/PRO_4100.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,839,'[[SonicWall%GPASS%PRO 5060 | http://www.sonicwall.com/us/products/PRO_5060.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,840,'[[SonicWall%GPASS%NSA 240 | http://www.sonicwall.com/us/products/NSA_240.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,841,'[[SonicWall%GPASS%NSA 2400 | http://www.sonicwall.com/us/products/NSA_2400.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,842,'[[SonicWall%GPASS%NSA 3500 | http://www.sonicwall.com/us/products/NSA_3500.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,843,'[[SonicWall%GPASS%NSA 4500 | http://www.sonicwall.com/us/products/NSA_4500.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,844,'[[SonicWall%GPASS%NSA 5000 | http://www.sonicwall.com/us/products/NSA_5000.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,845,'[[SonicWall%GPASS%NSA E5500 | http://www.sonicwall.com/us/products/NSA_E5500.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,846,'[[SonicWall%GPASS%NSA E6500 | http://www.sonicwall.com/us/products/NSA_E6500.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (24,847,'[[SonicWall%GPASS%NSA E7500 | http://www.sonicwall.com/us/products/NSA_E7500.html]]')";
			$query[] = "UPDATE Dictionary SET chapter_no = 24 WHERE (dict_key = 717 OR dict_key = 718 OR dict_key = 719)";
			$query[] = "UPDATE Dictionary SET chapter_no = 24, dict_value = '[[Juniper%GPASS%NetScreen 100 | http://www.juniper.net/customers/support/products/netscreen100.jsp]]' WHERE dict_key = 287";
			$query[] = "UPDATE Dictionary SET dict_value = 'Network switch' WHERE dict_key = 8";
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
	printReleaseNotes ($batchid);
}

echo '<br>Database version == ' . getDatabaseVersion();
echo "<p align=justify>Your database seems to be up-to-date. " .
	"Now the best thing to do would be to follow to the <a href='${root}'>main page</a> " .
	"and explore your data. Have a nice day.</p>";

?>
