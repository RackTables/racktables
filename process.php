<?php

// Include init after ophandlers/snmp, not before, so local.php can redefine things.
require 'inc/ophandlers.php';
// snmp.php is an exception, it is treated by a special hack
if (isset ($_REQUEST['op']) and $_REQUEST['op'] == 'querySNMPData')
	include 'inc/snmp.php';
require 'inc/init.php';
assertStringArg ('op', __FILE__);
$op = $_REQUEST['op'];
prepareNavigation();
// FIXME: find a better way to handle this error
if ($op == 'addFile' && !isset($_FILES['file']['error'])) {
	showError ("File upload error, it's size probably exceeds upload_max_filesize directive in php.ini");
	die;
}
fixContext();


if (!isset ($ophandler[$pageno][$tabno][$op]))
{
	showError ("Invalid request in operation broker: page '${pageno}', tab '${tabno}', op '${op}'", __FILE__);
	die();
}

// We have a chance to handle an error before starting HTTP header.
if (!isset ($delayauth[$pageno][$tabno][$op]) and !permitted())
	$location = buildWideRedirectURL (oneLiner (157)); // operation not permitted
else
{
	$location = $ophandler[$pageno][$tabno][$op]();
	if (!strlen ($location))
	{
		showError ('Operation handler failed to return its status', __FILE__);
	}
}
header ("Location: " . $location);

?>
