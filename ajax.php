<?php
ob_start();
try {

require 'inc/init.php';
$pageno = 'perms';
$tabno = 'edit';
fixContext();

if (!permitted())
{
	echo "NAK\nPermission denied";
	exit();
}

switch ($_REQUEST['ac'])
{
	case 'verifyCode':
		$code = str_replace ('\r', '', str_replace ('\n', "\n", $_REQUEST['code']));
		$result = getRackCode($code);
		if ($result['result'] == 'ACK')
			echo 'ACK';
		else
			echo "NAK\n".$result['load'];
	break;
}
ob_end_flush();
}
catch (Exception $e)
{
	ob_end_clean();
	printException($e);
}

?>
