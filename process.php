<?php
ob_start();
try {
// Include init after ophandlers, not before, so local.php can redefine things later.
require 'inc/ophandlers.php';
require 'inc/init.php';
assertStringArg ('op');
$op = $_REQUEST['op'];

// FIXME: find a better way to handle this error
if ($op == 'addFile' && !isset($_FILES['file']['error'])) {
	throw new RuntimeException("File upload error, it's size probably exceeds upload_max_filesize directive in php.ini");
}
fixContext();

if (!isset ($ophandler[$pageno][$tabno][$op]))
{
	throw new RuntimeException("Invalid request in operation broker: page '${pageno}', tab '${tabno}', op '${op}'");
}

// This is the only exception at the moment, so its handling is hardcoded.
if ($op == 'querySNMPData')
	include 'inc/snmp.php';

// We have a chance to handle an error before starting HTTP header.
if (!isset ($delayauth[$pageno][$tabno][$op]) and !permitted())
	$location = buildWideRedirectURL (oneLiner (157)); // operation not permitted
else
{
	$location = $ophandler[$pageno][$tabno][$op]();
	if (!strlen ($location))
	{
		throw new RuntimeException('Operation handler failed to return its status');
	}
}
header ("Location: " . $location);
ob_end_flush();
}
catch (Exception $e)
{
	ob_end_clean();
	printException($e);
}
?>

?>
