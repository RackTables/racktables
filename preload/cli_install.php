#!/usr/bin/env php
<?php
# Bootstrap the minimal RackTables runtime (same path as the web installer).
# Usage: php cli_install.php <password>
#
# This replicates the database initialization that the web installer does in
# steps 5 (init_database_static) and 6 (init_database_dynamic).

# Tell RackTables we are in CLI mode so init.php skips web-only auth.
$script_mode = TRUE;

require_once 'wwwroot/inc/pre-init.php';
require_once 'wwwroot/inc/dictionary.php';
require_once 'wwwroot/inc/config.php';
require_once 'wwwroot/inc/install.php';

# ---------------------------------------------------------------
# create all tables, views, default attributes, chapters
# ---------------------------------------------------------------
ob_start ();
$rc = init_database_static ();
$out = ob_get_clean ();
if ($out)
{
	echo $out;
}
if (!$rc)
{
	echo "\n[ERROR] Database static initialization failed.\n";
	exit (1);
}

# ---------------------------------------------------------------
# create the admin user
# ---------------------------------------------------------------
$hash = sha1 ("admin");
$query = "INSERT INTO `UserAccount` (`user_id`, `user_name`, " .
	"`user_password_hash`, `user_realname`) " .
	"VALUES (1,'admin','${hash}','RackTables Administrator')";

try
{
	$dbxlink->exec ($query);
	echo "Admin user created successfully.\n";
}
catch (PDOException $e)
{
	echo "[ERROR] Failed to create admin user: " . $e->getMessage () . "\n";
	exit (1);
}
