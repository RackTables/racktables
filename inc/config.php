<?php
/*
 *
 * This file used to hold a collection of constants, variables and arrays,
 * which drived the way misc RackTables functions performed. Now most of
 * them have gone into the database, and there is a user interface
 * for changing them. This file now provides a couple of functions to
 * access the new config storage.
 *
 */


// Current code version is subject to change with each new release.
define ('CODE_VERSION', '0.17.4');
define ('CHAP_OBJTYPE', 1);
define ('CHAP_PORTTYPE', 2);

$max_dict_key = array
(
	'0.17.0' => 988,
	'0.17.1' => 988,
	'0.17.2' => 1150,
	'0.17.3' => 1150,
	'0.17.4' => 1150,
	'0.17.5' => 1316,
);

define ('TAGNAME_REGEXP', '^[[:alnum:]]([\. _~-]?[[:alnum:]])*$');
define ('AUTOTAGNAME_REGEXP', '^\$[[:alnum:]]([\. _~-]?[[:alnum:]])*$');
// The latter matches both SunOS and Linux-styled formats.
define ('RE_L2_IFCFG', '/^[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?$/i');
define ('RE_L2_CISCO', '/^[0-9a-f][0-9a-f][0-9a-f][0-9a-f].[0-9a-f][0-9a-f][0-9a-f][0-9a-f].[0-9a-f][0-9a-f][0-9a-f][0-9a-f]$/i');
define ('RE_L2_SOLID', '/^[0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f]$/i');
define ('RE_L2_IPCFG', '/^[0-9a-f][0-9a-f]-[0-9a-f][0-9a-f]-[0-9a-f][0-9a-f]-[0-9a-f][0-9a-f]-[0-9a-f][0-9a-f]-[0-9a-f][0-9a-f]$/i');
define ('RE_L2_WWN_COLON', '/^[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]?:[0-9a-f][0-9a-f]$/i');
define ('RE_L2_WWN_HYPHEN', '/^[0-9a-f][0-9a-f]-[0-9a-f][0-9a-f]-[0-9a-f][0-9a-f]-[0-9a-f][0-9a-f]-[0-9a-f][0-9a-f]-[0-9a-f][0-9a-f]-[0-9a-f][0-9a-f]-[0-9a-f][0-9a-f]$/i');
define ('RE_L2_WWN_SOLID', '/^[0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f]$/i');
define ('RE_IP4_ADDR', '/^[0-9][0-9]?[0-9]?\.[0-9]?[0-9]?[0-9]?\.[0-9][0-9]?[0-9]?\.[0-9][0-9]?[0-9]?$/i');
define ('RE_IP4_NET', '/^[0-9][0-9]?[0-9]?\.[0-9]?[0-9]?[0-9]?\.[0-9][0-9]?[0-9]?\.[0-9][0-9]?[0-9]?\/[0-9][0-9]?$/i');

function getConfigVar ($varname = '')
{
	global $configCache;
	// We assume the only point of cache init, and it is init.php. If it
	// has failed, we don't retry loading.
	if (!isset ($configCache))
	{
		showError ("Configuration cache is unavailable", __FUNCTION__);
		die;
	}
	if ($varname == '')
	{
		showError ("Missing argument", __FUNCTION__);
		die;
	}
	if (isset ($configCache[$varname]))
	{
		// Try casting to int, if possible.
		if ($configCache[$varname]['vartype'] == 'uint')
			return 0 + $configCache[$varname]['varvalue'];
		else
			return $configCache[$varname]['varvalue'];
	}
	return NULL;
}

// In softfail mode die only on fatal errors, letting the user check
// and resubmit his input.
function setConfigVar ($varname = '', $varvalue = '', $softfail = FALSE)
{
	global $configCache;
	if (!isset ($configCache))
	{
		showError ('Configuration cache is unavailable', __FUNCTION__);
		die;
	}
	if (!strlen ($varname))
	{
		showError ("Empty argument", __FUNCTION__);
		die;
	}
	// We don't operate on unknown data.
	if (!isset ($configCache[$varname]))
	{
		showError ("don't know how to handle '${varname}'", __FUNCTION__);
		die;
	}
	if ($configCache[$varname]['is_hidden'] != 'no')
	{
		$errormsg = "'${varname}' is a system variable and cannot be changed by user.";
		if ($softfail)
			return $errormsg;
		showError ($errormsg, __FUNCTION__);
		die;
	}
	if (!strlen ($varvalue) && $configCache[$varname]['emptyok'] != 'yes')
	{
		$errormsg = "'${varname}' is configured to take non-empty value. Perhaps there was a reason to do so.";
		if ($softfail)
			return $errormsg;
		showError ($errormsg, __FUNCTION__);
		die;
	}
	if (strlen ($varvalue) && $configCache[$varname]['vartype'] == 'uint' && (!is_numeric ($varvalue) or $varvalue < 0 ))
	{
		$errormsg = "'${varname}' can accept UINT values only";
		if ($softfail)
			return $errormsg;
		showError ($errormsg, __FUNCTION__);
		die;
	}
	// Update cache only if the changes went into DB.
	if (storeConfigVar ($varname, $varvalue))
	{
		$configCache[$varname]['varvalue'] = $varvalue;
		if ($softfail)
			return '';
	}
	elseif ($softfail)
		return "storeConfigVar ('${varname}', '${varvalue}') failed in setConfigVar()";
}

?>
