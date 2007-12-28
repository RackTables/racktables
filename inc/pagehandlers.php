<?php
/*
*
*  This file is a library of page handlers for RackTables.
*
*/

// This function assures that specified argument was passed
// and is a number greater than zero.
function assertUIntArg ($argname, $allow_zero = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing.", __FUNCTION__);
		die();
	}
	if (!is_numeric ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is not a number.", __FUNCTION__);
		die();
	}
	if ($_REQUEST[$argname] < 0)
	{
		showError ("Parameter '${argname}' is less than zero.", __FUNCTION__);
		die();
	}
	if (!$allow_zero and $_REQUEST[$argname] == 0)
	{
		showError ("Parameter '${argname}' is equal to zero.", __FUNCTION__);
		die();
	}
}

// This function assures that specified argument was passed
// and is a non-empty string.
function assertStringArg ($argname, $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing.", __FUNCTION__);
		die();
	}
	if (!is_string ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is not a string.", __FUNCTION__);
		die();
	}
	if (!$ok_if_empty and empty ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is an empty string.", __FUNCTION__);
		die();
	}
}

function assertBoolArg ($argname, $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing.", __FUNCTION__);
		die();
	}
	if (!is_string ($_REQUEST[$argname]) or $_REQUEST[$argname] != 'on')
	{
		showError ("Parameter '${argname}' is not a string.", __FUNCTION__);
		die();
	}
	if (!$ok_if_empty and empty ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is an empty string.", __FUNCTION__);
		die();
	}
}

function handler_objgroup ()
{
	assertUIntArg ('group_id');
	renderObjectGroup ($_REQUEST['group_id']);
}

function handler_search ($tabno)
{
	renderSearchResults();
}

function handler_config ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderConfigMainpage();
			break;
		default:
			showError ("Invalid tab '${tabno}' requested.", __FUNCTION__);
	}
}

function handler_portmap ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderPortMap (FALSE);
			break;
		case 'edit':
			renderPortMap (TRUE);
			break;
		default:
			showError ("Invalid tab '${tabno}' requested.", __FUNCTION__);
	}
}

?>
