<?php

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
	$location = buildWideRedirectURL ($errlog);
	header ("Location: " . $location);
	exit();
}

switch ($_GET['ac'])
{
	case 'verifyCode':
		$code = $_REQUEST['code'];
		$result = getRackCode($code);
		if ($result['result'] == 'ACK')
			echo 'ACK';
		else
			echo "NAK\n".$result['load'];
	break;
}


?>
