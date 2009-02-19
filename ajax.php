<?php

$_REQUEST['page'] = 'perms';
$_REQUEST['tab'] = 'edit';
require 'inc/init.php';
fixContext();

// We have a chance to handle an error before starting HTTP header.
if (!permitted())
{
	$errlog = array
	(
		'v' => 2,
		'm' => array (0 => array ('c' => 157)) // operation not permitted
	);
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


?>
