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
$location = buildWideRedirectURL();

// FIXME: find a better way to handle this error
if ($op == 'addFile' && !isset($_FILES['file']['error']))
	throw new RackTablesError ('File upload error, check upload_max_filesize in php.ini', RackTablesError::MISCONFIGURED);
fixContext();

if
(
	!isset ($ophandler[$pageno][$tabno][$op]) or
	!function_exists ($ophandler[$pageno][$tabno][$op])
)
	throw new RackTablesError ("Invalid navigation data for '${pageno}-${tabno}-${op}'", RackTablesError::INTERNAL);

// We have a chance to handle an error before starting HTTP header.
if (!isset ($delayauth[$pageno][$tabno][$op]) and !permitted())
	showError ('Operation not permitted');
else
{
	// Call below does the job of bypass argument assertion, if such is required,
	// so the ophandler function doesn't have to re-assert this portion of its
	// arguments. And it would be even better to pass returned value to ophandler,
	// so it is not necessary to remember the name of bypass in it.
	getBypassValue();
	if (strlen ($redirect_to = call_user_func ($ophandler[$pageno][$tabno][$op])))
		$location = $redirect_to;
}
header ("Location: " . $location);
ob_end_flush();
}
// "soft" failures only require a short error message
catch (InvalidRequestArgException $e)
{
	ob_end_clean();
	showError ($e->getMessage());
	header ('Location: ' . $location);
}
catch (RTDatabaseError $e)
{
	ob_end_clean();
	showError ('Database error: ' . $e->getMessage());
	header ('Location: ' . $location);
}
// the rest ends up in a dedicated page
catch (Exception $e)
{
	ob_end_clean();
	printException ($e);
}

?>
