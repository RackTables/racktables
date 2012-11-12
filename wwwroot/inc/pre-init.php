<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*
*
* This file is a library needed by all modules of RackTables (installer, upgrader)
* to make them able to find code and data.
*
*/

require_once 'exceptions.php';
require_once 'interface-lib.php';

// Always have default values for these options, so if a user didn't
// care to set, something would be working anyway.
$user_auth_src = 'database';
$require_local_account = TRUE;

$racktables_rootdir = realpath (dirname (__FILE__) . '/..'); // you can not override this
# Below are default values for several paths. The right way to change these
# is to add respective line(s) to secret.php, unless this is a "shared
# code, multiple instances" deploy, in which case the paths could be changed
# in the custom entry point wrapper (like own index.php)
if (! isset ($racktables_staticdir)) // the directory containing 'pix', 'js', 'css' dirs
	$racktables_staticdir = $racktables_rootdir;
if (! isset ($racktables_gwdir)) // the directory containing the 'telnet' and 'ssh' scripts
	$racktables_gwdir = realpath ($racktables_rootdir . '/../gateways');
if (! isset ($racktables_confdir)) // the directory containing secret.php (default is wwwroot/inc)
	$racktables_confdir = dirname (__FILE__);
if (! isset ($path_to_secret_php)) // you can overrride the path to secret.php separately from $racktables_confdir (legacy feature)
	$path_to_secret_php = $racktables_confdir . '/secret.php';
if (! isset ($racktables_plugins_dir)) // the directory where RT will load additional *.php files (like local.php) from
	$racktables_plugins_dir = realpath ($racktables_rootdir . '/../plugins');

// secret.php may be missing, generally it is OK
if (fileSearchExists ($path_to_secret_php))
{
	$found_secret_file = TRUE;
	require_once $path_to_secret_php;
}
else
	$found_secret_file = FALSE;

// determine local paths after loading of secret.php (maybe it has overrided racktables_plugins_dir)
if (! isset ($local_gwdir)) // the directory where RT will search gateway scripts if not found in $racktables_gwdir
	$local_gwdir = $racktables_plugins_dir . '/gateways';
if (! isset ($local_staticdir)) // the directory where RT will search static files (js/*, css/*, pix/*) if not found in $racktables_staticdir
	$local_staticdir = $racktables_plugins_dir;

// (re)connects to DB, stores PDO object in $dbxlink global var
function connectDB()
{
	global $dbxlink, $pdo_dsn, $db_username, $db_password, $pdo_bufsize;
	$dbxlink = NULL;
	$drvoptions = array
	(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::MYSQL_ATTR_INIT_COMMAND => 'set names "utf8"',
	);
	if (isset ($pdo_bufsize))
		$drvoptions[PDO::MYSQL_ATTR_MAX_BUFFER_SIZE] = $pdo_bufsize;
	try
	{
		$dbxlink = new PDO ($pdo_dsn, $db_username, $db_password, $drvoptions);
	}
	catch (PDOException $e)
	{
		throw new RackTablesError ("Database connection failed:\n\n" . $e->getMessage(), RackTablesError::INTERNAL);
	}
}

// tries to guess the existance of the file before the php's include using the same searching method.
// in addition to calling file_exists, searches the current file's directory if the path is not looks
// like neither absolute nor relative.
function fileSearchExists ($filename)
{
	if (! preg_match ('@^(\.+)?/@', $filename))
	{
		$this_file_dir = dirname (__FILE__);
		if (file_exists ($this_file_dir . '/' . $filename))
			return TRUE;
	}
	return file_exists ($filename);
}

?>
