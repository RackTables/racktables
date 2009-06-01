<?php
require 'inc/init.php';

assertUIntArg ('file_id', __FILE__);
$pageno = 'file';
$tabno = 'default';
fixContext();
if (!permitted())
{
	showError ("Permission denied", __FILE__);
	die();
}

$file = getFile($_REQUEST['file_id']);
if ($file != NULL) 
{
	header("Content-Type: {$file['type']}");
	header("Content-Length: {$file['size']}");
	header("Content-Disposition: attachment; filename={$file['name']}");
	echo $file['contents'];
}
?>
