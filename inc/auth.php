<?php
/*

Authentication library for RackTables.

*/

// This function ensures that we don't continue without a legitimate
// username and password (also make sure, that both are present, this
// is especially useful for LDAP auth code to not deceive itself with
// anonymous binding). It also initializes $remote_* and $*_tags vars.
// Fatal errors are followed by exit (1) to aid in script debugging.
function authenticate ()
{
	global
		$remote_username,
		$remote_displayname,
		$auto_tags,
		$user_given_tags,
		$user_auth_src,
		$require_local_account;
	if (!isset ($user_auth_src) or !isset ($require_local_account))
	{
		showError ('secret.php misconfiguration: either user_auth_src or require_local_account are missing', __FUNCTION__);
		exit (1);
	}
	if (isset ($_REQUEST['logout']))
		dieWith401(); // Reset browser credentials cache.
	switch ($user_auth_src)
	{
		case 'database':
		case 'ldap':
			if
			(
				!isset ($_SERVER['PHP_AUTH_USER']) or
				!strlen ($_SERVER['PHP_AUTH_USER']) or
				!isset ($_SERVER['PHP_AUTH_PW']) or
				!strlen ($_SERVER['PHP_AUTH_PW'])
			)
				dieWith401();
			$remote_username = $_SERVER['PHP_AUTH_USER'];
			break;
		case 'httpd':
			if
			(
				!isset ($_SERVER['REMOTE_USER']) or
				!strlen ($_SERVER['REMOTE_USER'])
			)
			{
				showError ('System misconfiguration. The web-server didn\'t authenticate the user, although ought to do.');
				die;
			}
			$remote_username = $_SERVER['REMOTE_USER'];
			break;
		default:
			showError ('Invalid authentication source!', __FUNCTION__);
			die;
	}
	// Fallback value.
	$remote_displayname = $remote_username;
	if (NULL !== ($remote_userid = getUserIDByUsername ($remote_username)))
	{
		// Always set $remote_displayname, if a local account exists and has one.
		// This can be overloaded from LDAP later though.
		$userinfo = spotEntity ('user', $remote_userid);
		$user_given_tags = $userinfo['etags'];
		if (!empty ($userinfo['user_realname']))
			$remote_displayname = $userinfo['user_realname'];
	}
	elseif ($require_local_account)
		dieWith401();
	$auto_tags = array_merge ($auto_tags, generateEntityAutoTags ('user', $remote_username));
	switch (TRUE)
	{
		// Just trust the server, because the password isn't known.
		case ('httpd' == $user_auth_src):
			return;
		// When using LDAP, leave a mean to fix things. Admin user is always authenticated locally.
		case ('database' == $user_auth_src or $remote_userid == 1):
			if (authenticated_via_database ($remote_username, $_SERVER['PHP_AUTH_PW']))
				return;
			break;
		case ('ldap' == $user_auth_src):
			if (authenticated_via_ldap ($remote_username, $_SERVER['PHP_AUTH_PW']))
				return;
			break;
		default:
			showError ('Invalid authentication source!', __FUNCTION__);
			die;
	}
	dieWith401();
}

function dieWith401 ()
{
	header ('WWW-Authenticate: Basic realm="' . getConfigVar ('enterprise') . ' RackTables access"');
	header ('HTTP/1.0 401 Unauthorized');
	showError ('This system requires authentication. You should use a username and a password.');
	die();
}

// Merge accumulated tags into a single chain, add location-specific
// autotags and try getting access clearance. Page and tab are mandatory,
// operation is optional.
function permitted ($p = NULL, $t = NULL, $o = NULL, $annex = array())
{
	global $pageno, $tabno, $op;
	global $auto_tags;

	if ($p === NULL)
		$p = $pageno;
	if ($t === NULL)
		$t = $tabno;
	$my_auto_tags = $auto_tags;
	$my_auto_tags[] = array ('tag' => '$page_' . $p);
	$my_auto_tags[] = array ('tag' => '$tab_' . $t);
	if ($o === NULL and !empty ($op)) // $op can be set to empty string
	{
		$my_auto_tags[] = array ('tag' => '$op_' . $op);
		$my_auto_tags[] = array ('tag' => '$any_op');
	}
	$subject = array_merge
	(
		$my_auto_tags,
		$annex
	);
	// XXX: The solution below is only appropriate for a corner case of a more universal
	// problem: to make the decision for an entity belonging to a cascade of nested
	// containers. Each container being an entity itself, it may have own tags (explicit
	// and implicit accordingly). There's a fixed set of rules (RackCode) with each rule
	// being able to evaluate any built and given context and produce either a decision
	// or a lack of decision.
	// There are several levels of context for the target entity, at least one for entities
	// belonging directly to the tree root. Each level's context is a union of given
	// container's tags and the tags of the contained entities.
	// The universal problem originates from the fact, that certain rules may change
	// their product as context level changes, thus forcing some final decision (but not
	// adding a lack of it). With rule code being principles and context cascade being
	// circumstances, there are two uttermost approaches or moralities.
	//
	// Fundamentalism: principles over circumstances. When a rule doesn't produce any
	// decision, go on to the next rule. When all rules are evaluated, go on to the next
	// security context level.
	//
	// Opportunism: circumstances over principles. With a lack of decision, work with the
	// same rule, trying to evaluate it against the next level (and next, and next...),
	// until all levels are tried. Only then go on to the next rule.
	//
	// With the above being simple discrete algorythms, I believe, that they very reliably
	// replicate human behavior. This gives a vast ground for further research, so I would
	// only note, that the morale used in RackTables is "principles first".
	return gotClearanceForTagChain ($subject);
}

function authenticated_via_ldap ($username, $password)
{
	global $LDAP_options, $remote_displayname, $auto_tags;

	// Destroy the cache each time config changes.
	if (sha1 (serialize ($LDAP_options)) != loadScript ('LDAPConfigHash'))
	{
		discardLDAPCache();
		saveScript ('LDAPConfigHash', sha1 (serialize ($LDAP_options)));
	}
	$oldinfo = acquireLDAPCache ($username, sha1 ($password), $LDAP_options['cache_expiry']);
	// Remember to have releaseLDAPCache() called before any return statement.
	if ($oldinfo === NULL) // cache miss
	{
		// On cache miss execute complete procedure and return the result. In case
		// of successful authentication put a record into cache.
		$newinfo = queryLDAPServer ($username, $password);
		if ($newinfo['result'] == 'ACK')
		{
			$remote_displayname = $newinfo['displayed_name'];
			foreach ($newinfo['memberof'] as $autotag)
				$auto_tags[] = array ('tag' => $autotag);
			replaceLDAPCacheRecord ($username, sha1 ($password), $newinfo['displayed_name'], $newinfo['memberof']);
		}
		releaseLDAPCache();
		// Do cache maintenence each time fresh data is stored.
		discardLDAPCache ($LDAP_options['cache_expiry']);
		return $newinfo['result'] == 'ACK';
	}
	// There are two confidence levels of cache hits: "certain" and "uncertain". In either case
	// expect authentication success, unless it's well-timed to perform a retry,
	// which may sometimes bring a NAK decision.
	if ($oldinfo['success_age'] < $LDAP_options['cache_refresh'] or $oldinfo['retry_age'] < $LDAP_options['cache_retry'])
	{
		releaseLDAPCache();
		$remote_displayname = $oldinfo['displayed_name'];
		foreach ($oldinfo['memberof'] as $autotag)
			$auto_tags[] = array ('tag' => $autotag);
		return TRUE;
	}
	// Either refresh threshold or retry threshold reached.
	$newinfo = queryLDAPServer ($username, $password);
	switch ($newinfo['result'])
	{
	case 'ACK': // refresh existing record
		$remote_displayname = $newinfo['displayed_name'];
		foreach ($newinfo['memberof'] as $autotag)
			$auto_tags[] = array ('tag' => $autotag);
		replaceLDAPCacheRecord ($username, sha1 ($password), $newinfo['displayed_name'], $newinfo['memberof']);
		releaseLDAPCache();
		return TRUE;
	case 'NAK': // The record isn't valid any more.
		deleteLDAPCacheRecord ($username);
		releaseLDAPCache();
		return FALSE;
	case 'CAN': // retry failed, do nothing, use old value till next retry
		$remote_displayname = $oldinfo['displayed_name'];
		foreach ($oldinfo['memberof'] as $autotag)
			$auto_tags[] = array ('tag' => $autotag);
		touchLDAPCacheRecord ($username);
		releaseLDAPCache();
		return TRUE;
	default:
		showError ('Internal error during LDAP cache dispatching', __FUNCTION__);
		die;
	}
	// This is never reached.
	return FALSE;
}

// Attempt a server conversation and return an array describing the outcome:
//
// 'result' => 'CAN' : connect (or search) failed completely
//
// 'result' => 'NAK' : server replied and denied access (or search returned odd data)
//
// 'result' => 'ACK' : server replied and cleared access, there were no search errors
// 'displayed_name' : a string built according to LDAP displayname_attrs option
// 'memberof' => filtered list of all LDAP groups the user belongs to
//
function queryLDAPServer ($username, $password)
{
	global $LDAP_options;

	$connect = @ldap_connect ($LDAP_options['server']);
	if ($connect === FALSE)
		return array ('result' => 'CAN');

	// Decide on the username we will actually authenticate for.
	if (isset ($LDAP_options['domain']) and !empty ($LDAP_options['domain']))
		$auth_user_name = $username . "@" . $LDAP_options['domain'];
	elseif
	(
		isset ($LDAP_options['search_dn']) and
		!empty ($LDAP_options['search_dn']) and
		isset ($LDAP_options['search_attr']) and
		!empty ($LDAP_options['search_attr'])
	)
	{
		$results = @ldap_search ($connect, $LDAP_options['search_dn'], '(' . $LDAP_options['search_attr'] . "=${username})", array("dn"));
		if ($results === FALSE)
			return array ('result' => 'CAN');
		if (@ldap_count_entries ($connect, $results) != 1)
		{
			@ldap_close ($connect);
			return array ('result' => 'NAK');
		}
		$info = @ldap_get_entries ($connect, $results);
		ldap_free_result ($results);
		$auth_user_name = $info[0]['dn'];
	}
	else
	{
		showError ('LDAP misconfiguration. Cannon build username for authentication.', __FUNCTION__);
		die;
	}
	$bind = @ldap_bind ($connect, $auth_user_name, $password);
	if ($bind === FALSE)
		switch (ldap_errno ($connect))
		{
		case 49: // LDAP_INVALID_CREDENTIALS
			return array ('result' => 'NAK');
		default:
			return array ('result' => 'CAN');
		}
	// preliminary decision may change during searching
	$ret = array ('result' => 'ACK', 'displayed_name' => '', 'memberof' => array());
	// Some servers deny anonymous search, thus search (if requested) only after binding.
	// Displayed name only makes sense for authenticated users anyway.
	if
	(
		isset ($LDAP_options['displayname_attrs']) and
		count ($LDAP_options['displayname_attrs']) and
		isset ($LDAP_options['search_dn']) and
		!empty ($LDAP_options['search_dn']) and
		isset ($LDAP_options['search_attr']) and
		!empty ($LDAP_options['search_attr'])
	)
	{
		$results = @ldap_search
		(
			$connect,
			$LDAP_options['search_dn'],
			'(' . $LDAP_options['search_attr'] . "=${username})",
			array_merge (array ('memberof'), explode (' ', $LDAP_options['displayname_attrs']))
		);
		if (@ldap_count_entries ($connect, $results) != 1)
		{
			@ldap_close ($connect);
			return array ('result' => 'NAK');
		}
		$info = @ldap_get_entries ($connect, $results);
		ldap_free_result ($results);
		$space = '';
		foreach (explode (' ', $LDAP_options['displayname_attrs']) as $attr)
		{
			$ret['displayed_name'] .= $space . $info[0][$attr][0];
			$space = ' ';
		}
		// Pull group membership, if any was returned.
		if (isset ($info[0]['memberof']))
			for ($i = 0; $i < $info[0]['memberof']['count']; $i++)
				foreach (explode (',', $info[0]['memberof'][$i]) as $pair)
				{
					list ($attr_name, $attr_value) = explode ('=', $pair);
					if ($attr_name == 'CN' and validTagName ('$lgcn_' . $attr_value, TRUE))
						$ret['memberof'][] = '$lgcn_' . $attr_value;
				}
	}
	@ldap_close ($connect);
	return $ret;
}

function authenticated_via_database ($username, $password)
{
	if (NULL === ($userid = getUserIDByUsername ($username))) // user not found
		return FALSE;
	// FIXME: consider avoiding this DB call, because user data should be already
	// available in authenticate().
	if (NULL === ($userinfo = spotEntity ('user', $userid))) // user found, DB error
	{
		showError ('Cannot load user data', __FUNCTION__);
		die();
	}
	return $userinfo['user_password_hash'] == sha1 ($password);
}

?>
