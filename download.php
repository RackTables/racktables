<?php
require 'inc/init.php';

if (empty($_REQUEST['file_id']))
{
	showError ("Invalid file specified", __FILE__);
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
