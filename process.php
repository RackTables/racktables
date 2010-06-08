<?php
ob_start();
try {
// Include init after ophandlers/snmp, not before, so local.php can redefine things.
require 'inc/ophandlers.php';
// snmp.php is an exception, it is treated by a special hack
if (isset ($_REQUEST['op']) and $_REQUEST['op'] == 'querySNMPData')
	include 'inc/snmp.php';
require 'inc/init.php';
assertStringArg ('op');
$op = $_REQUEST['op'];
prepareNavigation();
// FIXME: find a better way to handle this error
if ($op == 'addFile' && !isset($_FILES['file']['error']))
	throw new Exception ('File upload error, check upload_max_filesize in php.ini', E_INTERNAL);
fixContext();

if (!isset ($ophandler[$pageno][$tabno][$op]) or !function_exists ($ophandler[$pageno][$tabno][$op]))
	throw new Exception ("Invalid navigation data for '${pageno}-${tabno}-${op}'", E_INTERNAL);

// We have a chance to handle an error before starting HTTP header.
if (!isset ($delayauth[$pageno][$tabno][$op]) and !permitted())
	$location = buildWideRedirectURL (oneLiner (157)); // operation not permitted
else
{
	$location = call_user_func ($ophandler[$pageno][$tabno][$op]);
	if (!strlen ($location))
		throw new Exception ('Operation handler failed to return its status', E_INTERNAL);
}
header ("Location: " . $location);
ob_end_flush();
}
catch (Exception $e)
{
	ob_end_clean();
	if ($e->getCode() == E_DB_CONSTRAINT)
		header ('Location: ' . buildWideRedirectURL (oneLiner (108, array ($e->getMessage()))));
	else
		printException($e);
}

?>
