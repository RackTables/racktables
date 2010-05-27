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
define ('CODE_VERSION', '0.17.10');
define ('CHAP_OBJTYPE', 1);
define ('CHAP_PORTTYPE', 2);

$max_dict_key = array
(
	'0.17.0' => 988,
	'0.17.1' => 988,
	'0.17.2' => 1150,
	'0.17.3' => 1150,
	'0.17.4' => 1150,
	'0.17.5' => 1322,
	'0.17.6' => 1326,
	'0.17.7' => 1326,
	'0.17.8' => 1334,
	'0.17.9' => 1334,
	'0.17.10' => 1349,
);

define ('TAGNAME_REGEXP', '/^[\p{L}0-9]([. _~-]?[\p{L}0-9])*$/u');
define ('AUTOTAGNAME_REGEXP', '/^\$[\p{L}0-9]([. _~-]?[\p{L}0-9])*$/u');
// The latter matches both SunOS and Linux-styled formats.
define ('RE_L2_IFCFG', '/^[0-9a-f]{1,2}(:[0-9a-f]{1,2}){5}$/i');
define ('RE_L2_CISCO', '/^[0-9a-f]{4}(\.[0-9a-f]{4}){2}$/i');
define ('RE_L2_SOLID', '/^[0-9a-f]{12}$/i');
define ('RE_L2_IPCFG', '/^[0-9a-f]{2}(-[0-9a-f]{2}){5}$/i');
define ('RE_L2_WWN_COLON', '/^[0-9a-f]{1,2}(:[0-9a-f]{1,2}){7}$/i');
define ('RE_L2_WWN_HYPHEN', '/^[0-9a-f]{2}(-[0-9a-f]{2}){7}$/i');
define ('RE_L2_WWN_SOLID', '/^[0-9a-f]{16}$/i');
define ('RE_IP4_ADDR', '#^[0-9]{1,3}(\.[0-9]{1,3}){3}$#');
define ('RE_IP4_NET', '#^[0-9]{1,3}(\.[0-9]{1,3}){3}/[0-9]{1,2}$#');
define ('E_8021Q_NOERROR', 0);
define ('E_8021Q_VERSION_CONFLICT', 101);
define ('E_8021Q_PULL_REMOTE_ERROR', 102);
define ('E_8021Q_PUSH_REMOTE_ERROR', 103);
define ('E_8021Q_SYNC_DISABLED', 104);
define ('VLAN_MIN_ID', 1);
define ('VLAN_MAX_ID', 4094);
define ('VLAN_DFL_ID', 1);

function loadConfigDefaults() {
	global $configCache;
	$configCache = loadConfigCache();
	if (!count ($configCache)) {
		throw new RuntimeException('Failed to load configuration from the database.');
	}
	foreach ($configCache as $varname => &$row) {
		$row['is_altered'] = 'no';
		if ($row['vartype'] == 'uint') $row['varvalue'] = 0 + $row['varvalue'];
		$row['defaultvalue'] = $row['varvalue'];
	}
}

function alterConfigWithUserPreferences() {
	global $configCache;
	global $userConfigCache;
	global $remote_username;
	$userConfigCache = loadUserConfigCache($remote_username);
	foreach($userConfigCache as $key => $row) {
		if ($configCache[$key]['is_userdefined'] == 'yes') {
			$configCache[$key]['varvalue'] = $row['varvalue'];
			$configCache[$key]['is_altered'] = 'yes';
		}
	}
}

// Returns true if varname has a different value or varname is new
function isConfigVarChanged($varname, $varvalue) {
	global $configCache;
	if (!isset ($configCache))
		throw new RuntimeException ("Configuration cache is unavailable");
	if ($varname == '')
		throw new InvalidArgException('$varname', $varname, 'Empty variable name');
	if (!isset ($configCache[$varname])) return true;
	if ($configCache[$varname]['vartype'] == 'uint')
		return $configCache[$varname]['varvalue'] !== 0 + $varvalue;
	else
		return $configCache[$varname]['varvalue'] !== $varvalue;
}

function getConfigVar ($varname = '')
{
	global $configCache;
	// We assume the only point of cache init, and it is init.php. If it
	// has failed, we don't retry loading.
	if (!isset ($configCache))
	{
		throw new RuntimeException ("Configuration cache is unavailable");
	}
	if ($varname == '')
	{
		throw new InvalidArgException('$varname', $varname, 'Empty variable name');
	}
	if (isset ($configCache[$varname]))
	{
		// Try casting to int, if possible.
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
		throw new RuntimeException ("Configuration cache is unavailable");
	if (!strlen ($varname))
		throw new InvalidArgException('$varname', $varname, 'Empty variable name');
	// We don't operate on unknown data.
	if (!isset ($configCache[$varname]))
		throw new InvalidArgException('$varname', $varname, "Don't know how to handle '${varname}'");
	if ($configCache[$varname]['is_hidden'] != 'no')
		throw new InvalidArgException('$varname', $varname, "'${varname}' is a system variable and cannot be changed by user.");
	if (!strlen ($varvalue) && $configCache[$varname]['emptyok'] != 'yes')
		throw new InvalidArgException('$varname', $varname, "'${varname}' is configured to take non-empty value. Perhaps there was a reason to do so.");
	if (strlen ($varvalue) && $configCache[$varname]['vartype'] == 'uint' && (!is_numeric ($varvalue) or $varvalue < 0 ))
		throw new InvalidArgException('$varname', $varname, "'${varname}' can accept UINT values only");
	// Update cache only if the changes went into DB.
	storeConfigVar ($varname, $varvalue);
	$configCache[$varname]['varvalue'] = $varvalue;
}

function setUserConfigVar ($varname = '', $varvalue = '')
{
	global $configCache;
	global $remote_username;
	if (!isset ($configCache))
		throw new RuntimeException ("Configuration cache is unavailable");
	if (!strlen ($varname))
		throw new InvalidArgException('$varname', $varname, 'Empty variable name');
	// We don't operate on unknown data.
	if (!isset ($configCache[$varname]))
		throw new InvalidArgException('$varname', $varname, "Don't know how to handle '${varname}'");
	if ($configCache[$varname]['is_userdefined'] != 'yes')
		throw new InvalidArgException('$varname', $varname, "'${varname}' cannot be changed by user.");
	if ($configCache[$varname]['is_hidden'] != 'no')
		throw new InvalidArgException('$varname', $varname, "'${varname}' is a system variable and cannot be changed by user.");
	if (!strlen ($varvalue) && $configCache[$varname]['emptyok'] != 'yes')
		throw new InvalidArgException('$varname', $varname, "'${varname}' is configured to take non-empty value. Perhaps there was a reason to do so.");
	if (strlen ($varvalue) && $configCache[$varname]['vartype'] == 'uint' && (!is_numeric ($varvalue) or $varvalue < 0 ))
		throw new InvalidArgException('$varname', $varname, "'${varname}' can accept UINT values only");
	// Update cache only if the changes went into DB.
	storeUserConfigVar ($remote_username, $varname, $varvalue);
	$configCache[$varname]['varvalue'] = $varvalue;
}

function resetUserConfigVar ($varname = '')
{
	global $configCache;
	global $remote_username;
	if (!isset ($configCache))
		throw new RuntimeException ("Configuration cache is unavailable");
	if (!strlen ($varname))
		throw new InvalidArgException('$varname', $varname, 'Empty variable name');
	// We don't operate on unknown data.
	if (!isset ($configCache[$varname]))
		throw new InvalidArgException('$varname', $varname, "Don't know how to handle '${varname}'");
	if ($configCache[$varname]['is_userdefined'] != 'yes')
		throw new InvalidArgException('$varname', $varname, "'${varname}' cannot be changed by user.");
	if ($configCache[$varname]['is_hidden'] != 'no')
		throw new InvalidArgException('$varname', $varname, "'${varname}' is a system variable and cannot be changed by user.");
	// Update cache only if the changes went into DB.
	deleteUserConfigVar ($remote_username, $varname);
}

?>
