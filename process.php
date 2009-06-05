<?php
ob_start();
try {
// Include init after ophandlers, not before, so local.php can redefine things later.
require 'inc/ophandlers.php';
require 'inc/init.php';

// FIXME: find a better way to handle this error
if ($_REQUEST['op'] == 'addFile' && !isset($_FILES['file']['error'])) {
	showError ("File upload error, it's size probably exceeds upload_max_filesize directive in php.ini");
	die;
}
fixContext();

if (!strlen ($op) or !isset ($ophandler[$pageno][$tabno][$op]))
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
ob_end_flush();
}
catch (Exception $e)
{
	ob_end_clean();
	printException($e);
}
?>

?>
