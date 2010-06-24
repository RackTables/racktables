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
	throw new RackTablesError ('File upload error, check upload_max_filesize in php.ini', RackTablesError::MISCONFIGURED);
fixContext();

if (!isset ($ophandler[$pageno][$tabno][$op]) or !function_exists ($ophandler[$pageno][$tabno][$op]))
	throw new RackTablesError ("Invalid navigation data for '${pageno}-${tabno}-${op}'", RackTablesError::INTERNAL);

// We have a chance to handle an error before starting HTTP header.
if (!isset ($delayauth[$pageno][$tabno][$op]) and !permitted())
	$location = buildWideRedirectURL (oneLiner (157)); // operation not permitted
else
{
	$location = call_user_func ($ophandler[$pageno][$tabno][$op]);
	if (!strlen ($location))
		throw new RackTablesError ('Operation handler failed to return its status', RackTablesError::INTERNAL);
}
header ("Location: " . $location);
ob_end_flush();
}
// "soft" failures only require a short error message
catch (InvalidRequestArgException $e)
{
	ob_end_clean();
	header ('Location: ' . buildWideRedirectURL (oneLiner (107, array ($e->getMessage()))));
}
catch (RTDBConstraintError $e)
{
	ob_end_clean();
	header ('Location: ' . buildWideRedirectURL (oneLiner (108, array ($e->getMessage()))));
}
// the rest ends up in a dedicated page
catch (Exception $e)
{
	ob_end_clean();
	printException ($e);
}

?>
