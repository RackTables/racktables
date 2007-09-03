<?
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
		showError ("Parameter '${argname}' is missing.");
		die();
	}
	if (!is_numeric ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is not a number.");
		die();
	}
	if ($_REQUEST[$argname] < 0)
	{
		showError ("Parameter '${argname}' is less than zero.");
		die();
	}
	if (!$allow_zero and $_REQUEST[$argname] == 0)
	{
		showError ("Parameter '${argname}' is equal to zero.");
		die();
	}
}

// This function assures that specified argument was passed
// and is a non-empty string.
function assertStringArg ($argname, $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing.");
		die();
	}
	if (!is_string ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is not a string.");
		die();
	}
	if (!$ok_if_empty and empty ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is an empty string.");
		die();
	}
}

function assertBoolArg ($argname, $ok_if_empty = FALSE)
{
	if (!isset ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is missing.");
		die();
	}
	if (!is_string ($_REQUEST[$argname]) or $_REQUEST[$argname] != 'on')
	{
		showError ("Parameter '${argname}' is not a string.");
		die();
	}
	if (!$ok_if_empty and empty ($_REQUEST[$argname]))
	{
		showError ("Parameter '${argname}' is an empty string.");
		die();
	}
}

function handler_row ($tabno)
{
	assertUIntArg ('row_id');
	switch ($tabno)
	{
		case 'default':
			renderRow ($_REQUEST['row_id']);
			break;
		case 'newrack':
			renderNewRackForm ($_REQUEST['row_id']);
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_row().");
	}
}

function handler_rack ($tabno)
{
	assertUIntArg ('rack_id');
	switch ($tabno)
	{
		case 'default':
			// FIXME: add tab renderer instead of table generator
			renderRackPage ($_REQUEST['rack_id']);
			break;
		case 'problems':
			renderRackProblems ($_REQUEST['rack_id']);
			break;
		case 'design':
			renderRackDesign ($_REQUEST['rack_id']);
			break;
		case 'edit':
			renderEditRackForm ($_REQUEST['rack_id']);
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_rack().");
	}
}

function handler_object ($tabno)
{
	assertUIntArg ('object_id');
	switch ($tabno)
	{
		case 'default':
			renderRackObject ($_REQUEST['object_id']);
			break;
		case 'rackspace':
			renderRackSpaceForObject ($_REQUEST['object_id']);
			break;
		case 'ports':
			renderPortsForObject ($_REQUEST['object_id']);
			break;
		case 'network':
			renderNetworkForObject ($_REQUEST['object_id']);
			break;
		case 'edit':
			renderEditObjectForm ($_REQUEST['object_id']);
			break;
		case 'portfwrd':
			renderIPAddressPortForwarding($_REQUEST['object_id']);
			break;
		case 'switchvlans':
			renderVLANMembership($_REQUEST['object_id']);
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_object().");
	}
}

function handler_objects ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderObjectGroupSummary();
			break;
		case 'newobj':
			renderNewObjectForm();
			break;
		case 'newmulti':
			renderAddMultipleObjectsForm();
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_objects().");
	}
}

function handler_objgroup ()
{
	assertUIntArg ('group_id');
	renderObjectGroup ($_REQUEST['group_id']);
}

function handler_rackspace ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderRackspace();
			break;
		case 'history':
			renderRackspaceHistory ();
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_rackspace().");
	}
}

function handler_ipv4space ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderAddressspace();
			break;
		case 'newrange':
			renderAddNewRange();
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_ipv4space().");
	}
}

function handler_iprange ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderIPRange();
			break;
		case 'properties':
			renderIPRangeProperties();
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_iprange().");
	}
}

function handler_ipaddress ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderIPAddress();
			break;
		case 'properties':
			renderIPAddressProperties();
			break;
		case 'assignment':
			renderIPAddressAssignment();
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_ipaddress().");
	}
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
			showError ("Invalid tab '${tabno}' requested in handler_config().");
	}
}

function handler_accounts ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderAccounts();
			break;
		case 'edit':
			renderAccountsEditForm();
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_accounts().");
	}
}

function handler_perms ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderPermissions();
			break;
		case 'edit':
			renderPermissionsEditForm();
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_perms().");
	}
}

function handler_ro ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderReadonlyParameters();
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_ro().");
	}
}

function handler_ui ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderUIConfig();
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_ui().");
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
			showError ("Invalid tab '${tabno}' requested in handler_portmap().");
	}
}

function handler_dict ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderDictionary();
			break;
		case 'edit':
			renderDictionaryEditor();
			break;
		case 'chapters':
			renderChaptersEditor();
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_dict().");
	}
}

function handler_attrs ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderAttributes();
			break;
		case 'editattrs':
			renderEditAttributesForm ();
			break;
		case 'editmap':
			renderEditAttrMapForm ();
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_attrs().");
	}
}

function handler_reports ($tabno)
{
	switch ($tabno)
	{
		case 'default':
			renderReportSummary();
			break;
		default:
			showError ("Invalid tab '${tabno}' requested in handler_reports().");
	}
}

?>
