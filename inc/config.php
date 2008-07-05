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
define ('CODE_VERSION', '0.16.0');

// The name of hash used to store account password hashes
// in the database. I think, we are happy with this one forever.
define ('PASSWORD_HASH', 'sha1');

define ('TAGNAME_REGEXP', '^[[:alnum:]]([\. _-~]?[[:alnum:]])*$');
define ('AUTOTAGNAME_REGEXP', '^\$[[:alnum:]]([\. _-~]?[[:alnum:]])*$');

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
	if (empty ($varname))
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
	if (empty ($varvalue) && $configCache[$varname]['emptyok'] != 'yes')
	{
		$errormsg = "'${varname}' is configured to take non-empty value. Perhaps there was a reason to do so.";
		if ($softfail)
			return $errormsg;
		showError ($errormsg, __FUNCTION__);
		die;
	}
	if (!empty ($varvalue) && $configCache[$varname]['vartype'] == 'uint' && (!is_numeric ($varvalue) or $varvalue < 0 ))
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
