<?php
ob_start();
try {

require 'inc/init.php';
require_once 'inc/ajax-interface.php';
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
	case 'get-port-link': // returns JSON-encoded text
		assertUIntArg ('object_id');
		$object = spotEntity ('object', $_REQUEST['object_id']);
		fixContext ($object);
		if (! permitted ('object', 'liveports', 'get_link_status'))
			throw new RacktablesError ('Permission denied: $op_get_link_status check failed');
		$data = formatPortLinkHints ($_REQUEST['object_id']);
		echo json_encode ($data);
	break;
	case 'get-port-mac': // returns JSON-encoded text
		assertUIntArg ('object_id');
		$object = spotEntity ('object', $_REQUEST['object_id']);
		fixContext ($object);
		if (! permitted ('object', 'liveports', 'get_mac_list'))
			throw new RacktablesError ('Permission denied: $op_get_mac_list check failed');
		$data = formatPortMacHints ($_REQUEST['object_id']);
		echo json_encode ($data);
	break;
	case 'get-port-conf': // returns JSON-encoded text
		assertUIntArg ('object_id');
		$object = spotEntity ('object', $_REQUEST['object_id']);
		fixContext ($object);
		if (! permitted ('object', 'liveports', 'get_port_conf'))
			throw new RacktablesError ('Permission denied: $op_get_port_conf check failed');
		$data = formatPortConfigHints ($_REQUEST['object_id']);
		echo json_encode ($data);
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
	echo "NAK\nRuntime exception: ". $e->getMessage();
}

?>
