<?php
/*
*
*  This file contains help rendering functions for RackTables.
*
*/

// Generate a link to the help tab.
function genHelpLink ($tabno)
{
	global $root;
	return "${root}?page=help&tab=${tabno}";
}

// Checks if a topic is present for page and tab and render a hinting element;
// do nothing otherwise.
function lookupHelpTopic ($pageno, $tabno)
{
	global $helptab;
	if (!isset ($helptab[$pageno][$tabno]))
		return;
	echo "<li style='position: absolute; top: 80px; right: 0px;'><a style='background-color: #f0f0f0; border: 0;' href='" . genHelpLink ($helptab[$pageno][$tabno]);
	echo "' alt='Help' title='Help is available for this page'>";
	printImageHREF ('helphint');
	echo '</a>';
	return;
}

// Prints the help page content.
function renderHelpTab ($tabno)
{
	switch ($tabno)
	{
		default:
			startPortlet ('Oops!');
			echo __FUNCTION__ . ": There was no help text found for help tab '${tabno}'";
			finishPortlet();
			break;
	}
}

?>
