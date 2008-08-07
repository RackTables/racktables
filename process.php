<?php

require 'inc/init.php';
fixContext();

if (empty ($op) or !isset ($ophandler[$pageno][$tabno][$op]))
{
	showError ("Invalid request in operation broker: page '${pageno}', tab '${tabno}', op '${op}'", __FILE__);
	die();
}

// We have a chance to handle an error before starting HTTP header.
if (!isset ($delayauth[$pageno][$tabno][$op]) and !permitted())
{
	$errlog = array
	(
		'v' => 2,
		'm' => array (0 => array ('c' => 157)) // operation not permitted
	);
	$location = buildWideRedirectURL ($errlog);
}
else
{
	$location = $ophandler[$pageno][$tabno][$op]();
	if (empty ($location))
	{
		showError ('Operation handler failed to return its status', __FILE__);
	}
}
header ("Location: " . $location);

?>
