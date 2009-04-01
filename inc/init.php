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
		"procedure by following <a href='${root}install.php'>this link</a>.",
		__FILE__
	);
	exit (1);
}

// Now try to connect...
try
{
	$dbxlink = new PDO ($pdo_dsn, $db_username, $db_password);
}
catch (PDOException $e)
{
	showError ("Database connection failed:\n\n" . $e->getMessage(), __FILE__);
	exit (1);
}

$dbxlink->exec ("set names 'utf8'");

if (get_magic_quotes_gpc())
	foreach ($_REQUEST as $key => $value)
		if (gettype ($value) == 'string')
			$_REQUEST[$key] = stripslashes ($value);

if (!set_magic_quotes_runtime (0))
{
	showError ('Failed to turn magic quotes off', __FILE__);
	exit (1);
}

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
		'database version is ' . $dbver . '. No user will be ' .
		'either authenticated or shown any page until the upgrade is ' .
		"finished. Follow <a href='${root}upgrade.php'>this link</a> and " .
		'authenticate as administrator to finish the upgrade.</p>';
	exit (1);
}

if (!mb_internal_encoding ('UTF-8') or !mb_regex_encoding ('UTF-8'))
{
	showError ('Failed setting multibyte string encoding to UTF-8', __FILE__);
	exit (1);
}
$configCache = loadConfigCache();
if (!count ($configCache))
{
	showError ('Failed to load configuration from the database.', __FILE__);
	exit (1);
}

require_once 'inc/code.php'; // for getRackCode()
$rackCodeCache = loadScript ('RackCodeCache');
if ($rackCodeCache == NULL or empty ($rackCodeCache))
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
{
	// FIXME: display a message with an option to reset RackCode text
	showError ('Could not load the RackCode due to error: ' . $rackCode['load'], __FILE__);
	exit (1);
}
$rackCode = $rackCode['load'];

require_once 'inc/auth.php';
$auto_tags = array();
authenticate(); // sometimes this generates autotags, but never --- given tags
// Authentication passed.
// Note that we don't perform autorization here, so each 1st level page
// has to do it in its way, e.g. by calling authorize() after fixContext().

if (!isset ($script_mode) or $script_mode !== TRUE)
	session_start();


$pageno = (isset ($_REQUEST['page'])) ? $_REQUEST['page'] : 'index';
// Special handling of tab number to substitute the "last" index where applicable.
// Always show explicitly requested tab, substitute the last used name in case
// it is awailable, fall back to the default one.

if (isset ($_REQUEST['tab']))
	$tabno = $_REQUEST['tab'];
elseif (basename($_SERVER['PHP_SELF']) == 'index.php' and getConfigVar ('SHOW_LAST_TAB') == 'yes' and isset ($_SESSION['RTLT'][$pageno]))
{
	$tabno = $_SESSION['RTLT'][$pageno];
	$url = "${root}?page=$pageno&tab=$tabno";
	foreach ($_GET as $name=>$value)
	{
		if ($name == 'page' or $name == 'tab') continue;
		if (gettype($value) == 'array')
			foreach($value as $v)
				$url .= '&'.urlencode($name.'[]').'='.urlencode($v);
		else
			$url .= '&'.urlencode($name).'='.urlencode($value);
	}
	header('Location: '.$url);
	exit();
}
else
	$tabno = 'default';



$op = (isset ($_REQUEST['op'])) ? $_REQUEST['op'] : '';

$taglist = getTagList();
$tagtree = treeFromList ($taglist);
sortTree ($tagtree, 'taginfoCmp');

require_once 'inc/navigation.php';
require_once 'inc/pagetitles.php';
require_once 'inc/ophandlers.php';
require_once 'inc/triggers.php';
require_once 'inc/gateways.php';
require_once 'inc/snmp.php';
if (file_exists ('inc/local.php'))
	require_once 'inc/local.php';

// These will be filled in by fixContext()
$expl_tags = array();
$impl_tags = array();
// Initial chain for the current target.
$target_given_tags = array();
// Initial chain for the current user.
$user_given_tags = array();
if (!isset ($script_mode) or $script_mode !== TRUE)
{
	$auto_tags = array_merge ($auto_tags, generateEntityAutoTags ('user', $remote_username));
	if (isset ($accounts[$remote_username]))
		$user_given_tags = loadEntityTags ('user', $accounts[$remote_username]['user_id']);
}

?>
