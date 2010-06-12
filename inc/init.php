<?php
/*
*
* This file performs RackTables initialisation. After you include it
* from 1st-level page, don't forget to call fixContext(). This is done
* to enable override of of pageno and tabno variables. pageno and tabno
* together participate in forming security context by generating
* related autotags.
*
*/

// This is the first thing we need to do.
require_once 'inc/exceptions.php';
require_once 'inc/config.php';

// What we need first is database and interface functions.
require_once 'inc/functions.php';
require_once 'inc/database.php';
// Always have default values for these options, so if a user didn't
// care to set, something would be working anyway.
$user_auth_src = 'database';
$require_local_account = TRUE;

function showError ($info = '', $location = 'N/A')
{
	if (preg_match ('/\.php$/', $location))
		$location = basename ($location);
	elseif ($location != 'N/A')
		$location = $location . '()';
	echo "<div class=msg_error>An error has occured in [${location}]. ";
	if (!strlen ($info))
		echo 'No additional information is available.';
	else
		echo "Additional information:<br><p>\n<pre>\n${info}\n</pre></p>";
	echo "Go back or try starting from <a href='".makeHref()."'>index page</a>.<br></div>\n";
}

/*
 * This is almost a clone of showError(). This is added to get rid of 
 * cases when script dies after showError() is shown.
 */

function showWarning ($info = '', $location = 'N/A')
{
	if (preg_match ('/\.php$/', $location))
		$location = basename ($location);
	elseif ($location != 'N/A')
		$location = $location . '()';
	echo "<div class=msg_error>Warning event at [${location}]. ";
	if (!strlen ($info))
		echo 'No additional information is available.';
	else
		echo "Additional information:<br><p>\n<pre>\n${info}\n</pre></p>";
}



if (file_exists ('inc/secret.php'))
	require_once 'inc/secret.php';
else
{
	throw new Exception
	(
		"Database connection parameters are read from inc/secret.php file, " .
		"which cannot be found.<br>You probably need to complete the installation " .
		"procedure by following <a href='install.php'>this link</a>.",
		E_MISCONFIGURED
	);
}

// Now try to connect...
try
{
	$dbxlink = new PDO ($pdo_dsn, $db_username, $db_password);
}
catch (PDOException $e)
{
	throw new Exception ("Database connection failed:\n\n" . $e->getMessage(), E_INTERNAL);
}
$dbxlink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbxlink->exec ("set names 'utf8'");

// Magic quotes feature is deprecated, but just in case the local system
// still has it activated, reverse its effect.
if (function_exists ('get_magic_quotes_gpc') and get_magic_quotes_gpc())
	foreach ($_REQUEST as $key => $value)
		if (gettype ($value) == 'string')
			$_REQUEST[$key] = stripslashes ($value);

// Escape any globals before we ever try to use them, but keep a copy of originals.
$sic = array();
foreach ($_REQUEST as $key => $value)
{
	$sic[$key] = dos2unix ($value);
	if (gettype ($value) == 'string')
		$_REQUEST[$key] = escapeString (dos2unix ($value));
}

if (isset ($_SERVER['PHP_AUTH_USER']))
	$_SERVER['PHP_AUTH_USER'] = escapeString ($_SERVER['PHP_AUTH_USER']);
if (isset ($_SERVER['REMOTE_USER']))
	$_SERVER['REMOTE_USER'] = escapeString ($_SERVER['REMOTE_USER']);

$dbver = getDatabaseVersion();
if ($dbver != CODE_VERSION)
{
	echo '<p align=justify>This Racktables installation seems to be ' .
		'just upgraded to version ' . CODE_VERSION . ', while the '.
		'database version is ' . $dbver . '.<br>No user will be ' .
		'either authenticated or shown any page until the upgrade is ' .
		"finished.<br>Follow <a href='upgrade.php'>this link</a> and " .
		'authenticate as administrator to finish the upgrade.</p>';
	exit (1);
}

if (!mb_internal_encoding ('UTF-8'))
	throw new Exception ('Failed setting multibyte string encoding to UTF-8', E_INTERNAL);

loadConfigDefaults();

require_once 'inc/code.php'; // for getRackCode()
$rackCodeCache = loadScript ('RackCodeCache');
if ($rackCodeCache == NULL or !strlen ($rackCodeCache))
{
	$rackCode = getRackCode (loadScript ('RackCode'));
	saveScript ('RackCodeCache', base64_encode (serialize ($rackCode)));
}
else
{
	$rackCode = unserialize (base64_decode ($rackCodeCache));
	if ($rackCode === FALSE) // invalid cache
	{
		saveScript ('RackCodeCache', '');
		$rackCode = getRackCode (loadScript ('RackCode'));
	}
}

// Depending on the 'result' value the 'load' carries either the
// parse tree or error message.
if ($rackCode['result'] != 'ACK')
	throw new Exception ($rackCode['load'], E_BAD_RACKCODE);
$rackCode = $rackCode['load'];
// Only call buildPredicateTable() once and save the result, because it will remain
// constant during one execution for constraints processing.
$pTable = buildPredicateTable ($rackCode);
// Constraints parse trees aren't cached in the database, so the least to keep
// things running is to maintain application cache for them.
$parseCache = array();
$entityCache = array();
// used by getExplicitTagsOnly()
$tagRelCache = array();

$taglist = getTagList();
$tagtree = treeFromList ($taglist);
sortTree ($tagtree, 'taginfoCmp');

require_once 'inc/auth.php';
$auto_tags = array();
// Initial chain for the current user.
$user_given_tags = array();

// This also can be modified in local.php.
$pageheaders = array
(
	100 => "<link rel='STYLESHEET' type='text/css' href='css/pi.css' />",
	102 => "<link rel='ICON' type='image/x-icon' href='pix/racktables.ico' />",
	200 => "<script language='javascript' type='text/javascript' src='js/racktables.js'></script>",
	201 => "<script language='javascript' type='text/javascript' src='js/jquery-1.3.1.min.js'></script>",
	202 => "<script language='javascript' type='text/javascript' src='js/live_validation.js'></script>",
	203 => "<script language='javascript' type='text/javascript' src='js/codepress/codepress.js'></script>",
);

if (!isset ($script_mode) or $script_mode !== TRUE)
{
	// A successful call to authenticate() always generates autotags and somethimes
	// even given/implicit tags. It also sets remote_username and remote_displayname.
	authenticate();
	// Authentication passed.
	// Note that we don't perform autorization here, so each 1st level page
	// has to do it in its way, e.g. by calling authorize() after fixContext().
	session_start();
}
else
{
	// Some functions require remote_username to be set to something to act correctly,
	// even though they don't use the value itself.
	$admin_account = spotEntity ('user', 1);
	$remote_username = $admin_account['user_name'];
	unset ($admin_account);
}

alterConfigWithUserPreferences();

require_once 'inc/navigation.php';
require_once 'inc/triggers.php';


$op = '';
require_once 'inc/gateways.php';
if (file_exists ('inc/local.php'))
	require_once 'inc/local.php';

// These will be filled in by fixContext()
$expl_tags = array();
$impl_tags = array();
// Initial chain for the current target.
$target_given_tags = array();

?>
