<?php

require 'inc/init.php';
fixContext();

if (empty ($op) or !isset ($ophandler[$pageno][$tabno][$op]))
{
	showError ("Invalid request in operation broker: page '${pageno}', tab '${tabno}', op '${op}'");
	die();
}

// We have a chance to handle an error before starting HTTP header.
$location =
	permitted() ?
	$ophandler[$pageno][$tabno][$op]() :
	buildRedirectURL ($pageno, $tabno, 'error', 'Operation not permitted!');
header ("Location: " . $location);

?>
