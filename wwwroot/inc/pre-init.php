<?php
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
if (! isset ($racktables_confdir)) // the directory containing local.php and secret.php (default is wwwroot/inc)
	$racktables_confdir = dirname (__FILE__);
if (! isset ($path_to_secret_php))
	$path_to_secret_php = $racktables_confdir . '/secret.php';
if (! isset ($path_to_local_php))
	$path_to_local_php = $racktables_confdir . '/local.php';

// secret.php may be missing, generally it is OK
if (fileSearchExists ($path_to_secret_php))
{
	$found_secret_file = TRUE;
	require_once $path_to_secret_php;
}
else
	$found_secret_file = FALSE;

// (re)connects to DB, stores PDO object in $dbxlink global var
function connectDB()
{
	global $dbxlink, $pdo_dsn, $db_username, $db_password;
	$dbxlink = NULL;
	// Now try to connect...
	try
	{
		$dbxlink = new PDO ($pdo_dsn, $db_username, $db_password);
	}
	catch (PDOException $e)
	{
		throw new RackTablesError ("Database connection failed:\n\n" . $e->getMessage(), RackTablesError::INTERNAL);
	}
	$dbxlink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbxlink->exec ("set names 'utf8'");
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
