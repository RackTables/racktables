<?php
ob_start();
try {

require 'inc/init.php';
assertStringArg ('ac');

switch ($_REQUEST['ac'])
{
case 'verifyCode':
	$pageno = 'perms';
	$tabno = 'edit';
	fixContext();
	if (!permitted())
	{
		echo "NAK\nPermission denied";
		exit();
	}
	assertStringArg ('code');
	$result = getRackCode (dos2unix ($_REQUEST['code']));
	if ($result['result'] == 'ACK')
		echo "ACK\n";
	else
		echo "NAK\n" . $result['load'];
	break;
default:
	throw new InvalidRequestArgException ('ac', $_REQUEST['ac']);
}
ob_end_flush();
}
catch (InvalidRequestArgException $e)
{
	ob_end_clean();
	echo "NAK\nMalformed request";
}
catch (Exception $e)
{
	ob_end_clean();
	echo "NAK\nRuntime exception";
}

?>
