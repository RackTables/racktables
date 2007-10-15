<?
/*

Authentication library for RackTables.

*/

// This function ensures that we don't continue without a legitimate
// username and password.
function authenticate ()
{
	if (array_search (PASSWORD_HASH, hash_algos()) === FALSE)
	{
		showError ('Password hash not supported, authentication impossible.');
		die();
	}
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

// Show error unless the user is allowed access here.
function authorize ()
{
	global $remote_username, $pageno, $tabno;
	if (!authorized ($remote_username, $pageno, $tabno))
	{
		showError ("User '${remote_username}' is not allowed to access here.");
		die();
	}
}

// This function returns TRUE, if username and password are valid.
function authenticated ($username, $password)
{
	global $accounts;
	if (!isset ($accounts[$username]['user_password_hash']))
		return FALSE;
	if ($accounts[$username]['user_enabled'] != 'yes')
		return FALSE;
	if ($accounts[$username]['user_password_hash'] == hash (PASSWORD_HASH, $password))
		return TRUE;
	return FALSE;
}

// This function returns TRUE, if specified user has access to the
// page and tab.
function authorized ($username, $pageno, $tabno)
{
	global $perms;
	// Deny access by default, then accumulate all corrections from database.
	// Order of nested cycles is important here!
	// '%' as page or tab name has a special value and means "any".
	// 0 as user_id means "any user".
	$answer = 'no';
	foreach (array ('%', $username) as $u)
		foreach (array ('%', $tabno) as $t)
			foreach (array ('%', $pageno) as $p)
				if (isset ($perms[$u][$p][$t]))
					$answer = $perms[$u][$p][$t];
	if ($answer == 'yes')
		return TRUE;
	return FALSE;
}

// This function returns password hash for given user ID.
function getHashByID ($user_id = 0)
{
	if ($user_id <= 0)
	{
		showError ('Invalid user_id in getHashByID()');
		return NULL;
	}
	global $accounts;
	foreach ($accounts as $account)
		if ($account['user_id'] == $user_id)
			return $account['user_password_hash'];
	return NULL;
}

?>
