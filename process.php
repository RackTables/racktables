<?

require 'inc/init.php';
authorize();

$op = (isset ($_REQUEST['op'])) ? $_REQUEST['op'] : '';

if (!isset ($ophandler[$pageno][$tabno][$op]))
{
	showError ("Invalid request in operation broker: page '${pageno}', tab '${tabno}', op '${op}'");
	die();
}

// We have a chance to handle an error before starting HTTP header.
$location = $ophandler[$pageno][$tabno][$op]();
header ("Location: " . $location);

?>
