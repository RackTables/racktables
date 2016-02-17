<?php
require_once '../wwwroot/inc/dictionary.php';
require_once '../wwwroot/inc/upgrade.php';

// The 'data' directory contains an SQL dump file for each version, including both schema and sample data.
// Developers should generate a dump file as part of the release process.  Instructions are in the wiki.
// For each version, this test will drop & re-create the database, then attempt to upgrade to the current version.
class UpgradeTest extends PHPUnit_Framework_TestCase
{
	protected static $versions = NULL;

	public static function setUpBeforeClass ()
	{
		$files = scandir ('./data');
		self::$versions = array ();
		foreach ($files as $file)
		{
			$version = preg_replace ('/^(\d+)\.(\d+)\.(\d+)\.sql$/', '\\1.\\2.\\3', $file, -1, $count);
			if ($count)
				self::$versions[] = $version;
		}
		natsort (self::$versions);
	}

	/**
	 * @group large
	 */
	public function testUpgrades ()
	{
		global $db_username, $db_password, $dbver, $dbxlink;

		foreach (self::$versions as $version)
		{
			$dbver = $version;
			$db_name = getDBName();
			$mysql_bin = '/usr/bin/mysql';
			$dbxlink->exec ("DROP DATABASE ${db_name}");
			$dbxlink->exec ("CREATE DATABASE ${db_name} CHARACTER SET utf8 COLLATE utf8_general_ci");
			// FIXME: Importing the dump for 0.20.0 (and likely for the subsequent releases) fails when the configured
			// MySQL username is different from "racktables", which is the recorded trigger definer in the file.
			// A quick workaround, which may be not appropriate for a production server, is to grant the SUPER privilege:
			// mysql> GRANT SUPER ON *.* TO racktables_user@localhost;
			exec ("${mysql_bin} --user=${db_username} --password=${db_password} ${db_name} < ./data/${version}.sql", $output, $exitcode);
			$this->assertEquals (0, $exitcode, "Populating the ${version} database failed");

			// the DB was deleted & re-created, so the current connection is invalid; re-connect
			connectDB ();
			$path = getDBUpgradePath ($version, CODE_VERSION);
			$this->assertNotNull ($path, "Empty upgrade path when upgrading from ${version}");

			foreach ($path as $batchid)
			{
				ob_start ();
				executeUpgradeBatch ($batchid);
				$output = ob_get_clean ();
				$this->assertNotContains ('queries failed', $output, "Upgrading from ${version} failed at ${batchid}");
			}
			ob_start ();
			executeUpgradeBatch ('dictionary');
			$output = ob_get_clean ();
			$this->assertNotContains ('queries failed', $output, "Upgrading from ${version} failed when reloading dictionary");
		}
	}
}
?>
