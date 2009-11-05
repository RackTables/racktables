<?php
require 'inc/init.php';
// purely for renderAccessDenied()
require 'inc/interface.php';

assertUIntArg ('file_id');
$pageno = 'file';
$tabno = 'download';
fixContext();
if (!permitted())
	renderAccessDenied();

$asattach = (isset ($_REQUEST['asattach']) and $_REQUEST['asattach'] == 'no') ? FALSE : TRUE;
$file = getFile($_REQUEST['file_id']);
if ($file != NULL) 
{
	header("Content-Type: {$file['type']}");
	header("Content-Length: {$file['size']}");
	if ($asattach)
		header("Content-Disposition: attachment; filename={$file['name']}");
	echo $file['contents'];
}
?>
