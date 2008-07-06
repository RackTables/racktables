<?php
/*
*
* This file performs RackTables initialisation. After you include it
* from 1st-level page, don't forget to call fixContext(). This is done
* to allow reloading of pageno and tabno variables. pageno and tabno
* together participate in forming security context by generating
* related autotags.
*
*/

$root = (empty($_SERVER['HTTPS'])?'http':'https').
	'://'.
	(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:($_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']=='80'?'':$_SERVER['SERVER_PORT']))).
	dirname($_SERVER['PHP_SELF']);
if (substr ($root, -1) != '/')
	$root .= '/';

// This is the first thing we need to do.
require_once 'inc/config.php';

// What we need first is database and interface functions.
require_once 'inc/interface.php';
require_once 'inc/functions.php';
require_once 'inc/database.php';
if (file_exists ('inc/secret.php'))
	require_once 'inc/secret.php';
else
{
	showError
	(
		"Database connection parameters are read from inc/secret.php file, " .
		"which cannot be found.\nYou probably need to complete the installation " .
		"procedure by following <a href='${root}install.php'>this link</a>."
	);
	die;
}

// Now try to connect...
try
{
	$dbxlink = new PDO ($pdo_dsn, $db_username, $db_password);
}
catch (PDOException $e)
{
	showError ("Database connection failed:\n\n" . $e->getMessage());
	die();
}

$dbxlink->exec ("set names 'utf8'");

if (get_magic_quotes_gpc())
	foreach ($_REQUEST as $key => $value)
		if (gettype ($value) == 'string')
			$_REQUEST[$key] = stripslashes ($value);

if (!set_magic_quotes_runtime (0))
{
	showError ('Failed to turn magic quotes off');
	die;
}

// Escape any globals before we ever try to use them.
foreach ($_REQUEST as $key => $value)
	if (gettype ($value) == 'string')
		$_REQUEST[$key] = escapeString ($value);

if (isset ($_SERVER['PHP_AUTH_USER']))
	$_SERVER['PHP_AUTH_USER'] = escapeString ($_SERVER['PHP_AUTH_USER']);
if (isset ($_SERVER['PHP_AUTH_PW']))
	$_SERVER['PHP_AUTH_PW'] = escapeString ($_SERVER['PHP_AUTH_PW']);

$dbver = getDatabaseVersion();
if ($dbver != CODE_VERSION)
{
	echo '<p align=justify>This Racktables installation seems to be ' .
		'just upgraded to version ' . CODE_VERSION . ', while the '.
		'database version is ' . $dbver . '. No user will be ' .
		'either authenticated or shown any page until the upgrade is ' .
		"finished. Follow <a href='${root}upgrade.php'>this link</a> and " .
		'authenticate as administrator to finish the upgrade.</p>';
	die;
}

if (!mb_internal_encoding ('UTF-8') or !mb_regex_encoding ('UTF-8'))
{
	showError ('Failed setting multibyte string encoding to UTF-8');
	die;
}
$configCache = loadConfigCache();
if (!count ($configCache))
{
	showError ('Failed to load configuration from the database.');
	die();
}

require_once 'inc/code.php';
// Depending on the 'result' value the 'load' carries either the
// parse tree or error message.
$rackCode = getRackCode (loadScript ('RackCode'));
if ($rackCode['result'] != 'ACK')
{
	// FIXME: display a message with an option to reset RackCode text
	showError ('Could not load the RackCode due to error: ' . $rackCode['load'], __FUNCTION__);
	die;
}
$rackCode = $rackCode['load'];

// Now init authentication.

require_once 'inc/auth.php';
// Load access database once.
$accounts = getUserAccounts();
if ($accounts === NULL)
{
	showError ('Failed to initialize access database.');
	die();
}

authenticate();

// Authentication passed.
// Note that we don't perform autorization here, so each 1st level page
// has to do it in its way, e.g. to call authorize().

$remote_username = $_SERVER['PHP_AUTH_USER'];
$pageno = (isset ($_REQUEST['page'])) ? $_REQUEST['page'] : 'index';
$tabno = (isset ($_REQUEST['tab'])) ? $_REQUEST['tab'] : 'default';
$op = (isset ($_REQUEST['op'])) ? $_REQUEST['op'] : '';
// Order matters here.
$taglist = getTagList();
$tagtree = getTagTree();

require_once 'inc/navigation.php';
require_once 'inc/pagetitles.php';
require_once 'inc/ophandlers.php';
require_once 'inc/triggers.php';
require_once 'inc/gateways.php';
require_once 'inc/snmp.php';

// These will be filled in by fixContext()
$auto_tags = array();
$expl_tags = array();
$impl_tags = array();
// and this will remain constant
$user_tags = loadUserTags ($accounts[$remote_username]['user_id']);
$user_tags = array_merge ($user_tags, getImplicitTags ($user_tags), getUserAutoTags());

?>
