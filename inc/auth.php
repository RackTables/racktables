<?php
/*

Authentication library for RackTables.

*/

// This function ensures that we don't continue without a legitimate
// username and password.
function authenticate ()
{
	if
	(
		!isset ($_SERVER['PHP_AUTH_USER']) or
		!isset ($_SERVER['PHP_AUTH_PW']) or
		!authenticated ($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) or
		isset ($_REQUEST['logout'])
	)
	{
		header ('WWW-Authenticate: Basic realm="' . getConfigVar ('enterprise') . ' RackTables access"');
		header ('HTTP/1.0 401 Unauthorized');
		showError ('This system requires authentication. You should use a username and a password.');
		die();
	}
}

// Merge accumulated tags into a single chain, add location-specific
// autotags and try getting access clearance. Page and tab are mandatory,
// operation is optional.
function permitted ($p = NULL, $t = NULL, $o = NULL)
{
	global $pageno, $tabno, $op;
	global
		$user_tags,
		$auto_tags,
		$expl_tags,
		$impl_tags;

	if ($p === NULL)
		$p = $pageno;
	if ($t === NULL)
		$t = $tabno;
	$subject = array_merge
	(
		$user_tags,
		$auto_tags,
		$expl_tags,
		$impl_tags
	);
	$subject[] = array ('tag' => '$page_' . $p);
	$subject[] = array ('tag' => '$tab_' . $t);
	if ($o === NULL and isset ($op))
		$subject[] = array ('tag' => '$op_' . $op);
	return gotClearanceForTagChain ($subject);
}

function accessiblePath ($p, $t)
{
	global $user_tags;
	$subject = $user_tags;
	$subject[] = array ('tag' => '$page_' . $p);
	$subject[] = array ('tag' => '$tab_' . $t);
	return gotClearanceForTagChain ($subject);
}

// This function returns TRUE, if username and password are valid.
function authenticated ($username, $password)
{
	global $accounts;
	if (!isset ($accounts[$username]) or $accounts[$username]['user_enabled'] != 'yes')
		return FALSE;
	// Always authenticate the administrator locally, thus giving him a chance
	// to fix broken installation.
	if ($accounts[$username]['user_id'] == 1)
		return authenticated_via_database ($username, $password);
	switch (getConfigVar ('USER_AUTH_SRC'))
	{
		case 'database':
			return authenticated_via_database ($username, $password);
			break;
		case 'ldap':
			return authenticated_via_ldap ($username, $password);
			break;
		default:
			showError ("Unknown user authentication source configured.", __FUNCTION__);
			return FALSE;
			break;
	}
	// and just to be sure...
	return FALSE;
}

function authenticated_via_ldap ($username, $password)
{
	global $ldap_server, $ldap_domain;
	if ($connect = @ldap_connect ($ldap_server))
		if ($bind = @ldap_bind ($connect, "${username}@${ldap_domain}", $password))
		{
			@ldap_close ($connect);
			return TRUE;
		}
	@ldap_close ($connect);
	return FALSE;
}

function authenticated_via_database ($username, $password)
{
	global $accounts;
	if (!defined ('HASH_HMAC'))
	{
		showError ('Fatal error: PHP hash extension is missing', __FUNCTION__);
		die();
	}
	if (array_search (PASSWORD_HASH, hash_algos()) === FALSE)
	{
		showError ('Password hash not supported, authentication impossible.', __FUNCTION__);
		die();
	}
	if (!isset ($accounts[$username]['user_password_hash']))
		return FALSE;
	if ($accounts[$username]['user_password_hash'] == hash (PASSWORD_HASH, $password))
		return TRUE;
	return FALSE;
}

// This function returns password hash for given user ID.
function getHashByID ($user_id = 0)
{
	if ($user_id <= 0)
	{
		showError ('Invalid user_id', __FUNCTION__);
		return NULL;
	}
	global $accounts;
	foreach ($accounts as $account)
		if ($account['user_id'] == $user_id)
			return $account['user_password_hash'];
	return NULL;
}

// Likewise.
function getUsernameByID ($user_id = 0)
{
	if ($user_id <= 0)
	{
		showError ('Invalid user_id', __FUNCTION__);
		return NULL;
	}
	global $accounts;
	foreach ($accounts as $account)
		if ($account['user_id'] == $user_id)
			return $account['user_name'];
	showError ("User with ID '${user_id}' not found!");
	return NULL;
}

?>
