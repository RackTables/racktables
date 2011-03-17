<?php
/*

Below is a mix of authentication (confirming user's identity) and authorization
(access controlling) functions of RackTables. The former set is expected to
be working with only database.php file included.

*/

// This function ensures that we don't continue without a legitimate
// username and password (also make sure, that both are present, this
// is especially useful for LDAP auth code to not deceive itself with
// anonymous binding). It also initializes $remote_* and $*_tags vars.
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
		throw new RackTablesError ('secret.php: either user_auth_src or require_local_account are missing', RackTablesError::MISCONFIGURED);
	if (isset ($_REQUEST['logout']))
		throw new RackTablesError ('', RackTablesError::NOT_AUTHENTICATED); // Reset browser credentials cache.
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
				throw new RackTablesError ('', RackTablesError::NOT_AUTHENTICATED);
			$remote_username = $_SERVER['PHP_AUTH_USER'];
			break;
		case 'httpd':
			if
			(
				!isset ($_SERVER['REMOTE_USER']) or
				!strlen ($_SERVER['REMOTE_USER'])
			)
				throw new RackTablesError ('The web-server didn\'t authenticate the user, although ought to do.', RackTablesError::MISCONFIGURED);
			$remote_username = $_SERVER['REMOTE_USER'];
			break;
		default:
			throw new RackTablesError ('Invalid authentication source!', RackTablesError::MISCONFIGURED);
			die;
	}
	$userinfo = constructUserCell ($remote_username);
	if ($require_local_account and !isset ($userinfo['user_id']))
		throw new RackTablesError ('', RackTablesError::NOT_AUTHENTICATED);
	$user_given_tags = $userinfo['etags'];
	$auto_tags = array_merge ($auto_tags, $userinfo['atags']);
	switch (TRUE)
	{
		// Just trust the server, because the password isn't known.
		case ('httpd' == $user_auth_src):
			$remote_displayname = strlen ($userinfo['user_realname']) ?
				$userinfo['user_realname'] :
				$remote_username;
			return; // success
		// When using LDAP, leave a mean to fix things. Admin user is always authenticated locally.
		case ('database' == $user_auth_src or (array_key_exists ('user_id', $userinfo) and $userinfo['user_id'] == 1)):
			$remote_displayname = strlen ($userinfo['user_realname']) ?
				$userinfo['user_realname'] :
				$remote_username;
			if (authenticated_via_database ($userinfo, $_SERVER['PHP_AUTH_PW']))
				return; // success
			break; // failure
		case ('ldap' == $user_auth_src):
			$ldap_dispname = '';
			$ldap_success = authenticated_via_ldap ($remote_username, $_SERVER['PHP_AUTH_PW'], $ldap_dispname);
			if (!$ldap_success)
				break; // failure
			$remote_displayname = strlen ($userinfo['user_realname']) ? // local value is most preferred
				$userinfo['user_realname'] :
				(strlen ($ldap_dispname) ? $ldap_dispname : $remote_username); // then one from LDAP
			return; // success
		default:
			throw new RackTablesError ('Invalid authentication source!', RackTablesError::MISCONFIGURED);
	}
	throw new RackTablesError ('', RackTablesError::NOT_AUTHENTICATED);
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
	if ($o === NULL and strlen ($op)) // $op can be set to empty string
		$o = $op;
	$my_auto_tags = $auto_tags;
	$my_auto_tags[] = array ('tag' => '$page_' . $p);
	$my_auto_tags[] = array ('tag' => '$tab_' . $t);
	if ($o !== NULL) // these tags only make sense in certain cases
	{
		$my_auto_tags[] = array ('tag' => '$op_' . $o);
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

# a "throwing" wrapper for above
function assertPermission ($p = NULL, $t = NULL, $o = NULL, $annex = array())
{
	if (! permitted ($p, $t, $o, $annex))
		throw new RTPermissionDenied();
}

// The argument doesn't include explicit and implicit tags. This allows us to derive implicit chain
// each time we modify the given argument (and work with the modified copy from now on).
// After the work is done the global $impl_tags is silently modified
function gotClearanceForTagChain ($const_base)
{
	global $rackCode, $expl_tags, $impl_tags;
	$ptable = array();
	foreach ($rackCode as $sentence)
	{
		switch ($sentence['type'])
		{
			case 'SYNT_DEFINITION':
				$ptable[$sentence['term']] = $sentence['definition'];
				break;
			case 'SYNT_GRANT':
				if (eval_expression ($sentence['condition'], array_merge ($const_base, $expl_tags, $impl_tags), $ptable))
					switch ($sentence['decision'])
					{
						case 'LEX_ALLOW':
							return TRUE;
						case 'LEX_DENY':
							return FALSE;
						default:
							throw new RackTablesError ("Condition match for unknown grant decision '${sentence['decision']}'", RackTablesError::INTERNAL);
					}
				break;
			case 'SYNT_ADJUSTMENT':
				if
				(
					eval_expression ($sentence['condition'], array_merge ($const_base, $expl_tags, $impl_tags), $ptable) and
					processAdjustmentSentence ($sentence['modlist'], $expl_tags)
				) // recalculate implicit chain only after actual change, not just on matched condition
					$impl_tags = getImplicitTags ($expl_tags); // recalculate
				break;
			default:
				throw new RackTablesError ("Can't process sentence of unknown type '${sentence['type']}'", RackTablesError::INTERNAL);
		}
	}
	return FALSE;
}

// Process a context adjustment request, update given chain accordingly,
// return TRUE on any changes done.
// The request is a sequence of clear/insert/remove requests exactly as cooked
// for each SYNT_CTXMODLIST node.
function processAdjustmentSentence ($modlist, &$chain)
{
	global $rackCode;
	$didChanges = FALSE;
	foreach ($modlist as $mod)
		switch ($mod['op'])
		{
			case 'insert':
				foreach ($chain as $etag)
					if ($etag['tag'] == $mod['tag']) // already there, next request
						break 2;
				$search = getTagByName ($mod['tag']);
				if ($search === NULL) // skip martians silently
					break;
				$chain[] = $search;
				$didChanges = TRUE;
				break;
			case 'remove':
				foreach ($chain as $key => $etag)
					if ($etag['tag'] == $mod['tag']) // drop first match and return
					{
						unset ($chain[$key]);
						$didChanges = TRUE;
						break 2;
					}
				break;
			case 'clear':
				$chain = array();
				$didChanges = TRUE;
				break;
			default: // HCF
				throw new RackTablesError ('invalid structure', RackTablesError::INTERNAL);
		}
	return $didChanges;
}

// a wrapper for two LDAP auth methods below
function authenticated_via_ldap ($username, $password, &$ldap_displayname)
{
	global $LDAP_options;
	if
	(
		$LDAP_options['cache_retry'] > $LDAP_options['cache_refresh'] or
		$LDAP_options['cache_refresh'] > $LDAP_options['cache_expiry']
	)
		throw new RackTablesError ('LDAP misconfiguration: refresh/retry/expiry mismatch', RackTablesError::MISCONFIGURED);
	if ($LDAP_options['cache_expiry'] == 0) // immediate expiry set means disabled cache
		return authenticated_via_ldap_nocache ($username, $password, $ldap_displayname);
	// authenticated_via_ldap_cache()'s way of locking can sometimes result in
	// a PDO error condition, which convertPDOException() was not able to dispatch.
	// To avoid reaching printPDOException() (which prints backtrace with password
	// argument in cleartext), any remaining PDO condition is converted locally.
	try
	{
		return authenticated_via_ldap_cache ($username, $password, $ldap_displayname);
	}
	catch (PDOException $e)
	{
		throw new RackTablesError ('LDAP caching error', RackTablesError::DB_WRITE_FAILED);
	}
}

// Authenticate given user with known LDAP server, completely ignore LDAP cache data.
function authenticated_via_ldap_nocache ($username, $password, &$ldap_displayname)
{
	global $auto_tags;
	$server_test = queryLDAPServer ($username, $password);
	if ($server_test['result'] == 'ACK')
	{
		$ldap_displayname = $server_test['displayed_name'];
		foreach ($server_test['memberof'] as $autotag)
			$auto_tags[] = array ('tag' => $autotag);
		return TRUE;
	}
	return FALSE;
}

// Idem, but consider existing data in cache and modify/discard it, when necessary.
// Remember to have releaseLDAPCache() called before any return statement.
// Perform cache maintenance on each update.
function authenticated_via_ldap_cache ($username, $password, &$ldap_displayname)
{
	global $LDAP_options, $auto_tags;

	// Destroy the cache each time config changes.
	if (sha1 (serialize ($LDAP_options)) != loadScript ('LDAPConfigHash'))
	{
		discardLDAPCache();
		saveScript ('LDAPConfigHash', sha1 (serialize ($LDAP_options)));
	}
	$oldinfo = acquireLDAPCache ($username, sha1 ($password), $LDAP_options['cache_expiry']);
	if ($oldinfo === NULL) // cache miss
	{
		// On cache miss execute complete procedure and return the result. In case
		// of successful authentication put a record into cache.
		$newinfo = queryLDAPServer ($username, $password);
		if ($newinfo['result'] == 'ACK')
		{
			$ldap_displayname = $newinfo['displayed_name'];
			foreach ($newinfo['memberof'] as $autotag)
				$auto_tags[] = array ('tag' => $autotag);
			replaceLDAPCacheRecord ($username, sha1 ($password), $newinfo['displayed_name'], $newinfo['memberof']);
			releaseLDAPCache();
			discardLDAPCache ($LDAP_options['cache_expiry']);
			return TRUE;
		}
		releaseLDAPCache();
		return FALSE;
	}
	// cache HIT
	// There are two confidence levels of cache hits: "certain" and "uncertain". In either case
	// expect authentication success, unless it's well-timed to perform a retry,
	// which may sometimes bring a NAK decision.
	if ($oldinfo['success_age'] < $LDAP_options['cache_refresh'] or $oldinfo['retry_age'] < $LDAP_options['cache_retry'])
	{
		releaseLDAPCache();
		$ldap_displayname = $oldinfo['displayed_name'];
		foreach ($oldinfo['memberof'] as $autotag)
			$auto_tags[] = array ('tag' => $autotag);
		return TRUE;
	}
	// Either refresh threshold or retry threshold reached.
	$newinfo = queryLDAPServer ($username, $password);
	switch ($newinfo['result'])
	{
	case 'ACK': // refresh existing record
		$ldap_displayname = $newinfo['displayed_name'];
		foreach ($newinfo['memberof'] as $autotag)
			$auto_tags[] = array ('tag' => $autotag);
		replaceLDAPCacheRecord ($username, sha1 ($password), $newinfo['displayed_name'], $newinfo['memberof']);
		releaseLDAPCache();
		discardLDAPCache ($LDAP_options['cache_expiry']);
		return TRUE;
	case 'NAK': // The record isn't valid any more.
		deleteLDAPCacheRecord ($username);
		releaseLDAPCache();
		discardLDAPCache ($LDAP_options['cache_expiry']);
		return FALSE;
	case 'CAN': // retry failed, do nothing, use old value till next retry
		$ldap_displayname = $oldinfo['displayed_name'];
		foreach ($oldinfo['memberof'] as $autotag)
			$auto_tags[] = array ('tag' => $autotag);
		touchLDAPCacheRecord ($username);
		releaseLDAPCache();
		discardLDAPCache ($LDAP_options['cache_expiry']);
		return TRUE;
	default:
		throw new RackTablesError ('structure error', RackTablesError::INTERNAL);
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

	if(extension_loaded('ldap') === FALSE)
		throw new RackTablesError ('LDAP misconfiguration. LDAP PHP Module is not installed.', RackTablesError::MISCONFIGURED);
		
	$connect = @ldap_connect ($LDAP_options['server']);
	if ($connect === FALSE)
		return array ('result' => 'CAN');

	// Decide on the username we will actually authenticate for.
	if (isset ($LDAP_options['domain']) and strlen ($LDAP_options['domain']))
		$auth_user_name = $username . "@" . $LDAP_options['domain'];
	elseif
	(
		isset ($LDAP_options['search_dn']) and
		strlen ($LDAP_options['search_dn']) and
		isset ($LDAP_options['search_attr']) and
		strlen ($LDAP_options['search_attr'])
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
		throw new RackTablesError ('LDAP misconfiguration. Cannon build username for authentication.', RackTablesError::MISCONFIGURED);
	if (array_key_exists ('options', $LDAP_options) and is_array ($LDAP_options['options']))
		foreach ($LDAP_options['options'] as $opt_code => $opt_value)
			ldap_set_option ($connect, $opt_code, $opt_value);
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
		strlen ($LDAP_options['displayname_attrs']) and
		isset ($LDAP_options['search_dn']) and
		strlen ($LDAP_options['search_dn']) and
		isset ($LDAP_options['search_attr']) and
		strlen ($LDAP_options['search_attr'])
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
					$items = explode ('=', $pair);
					if (count ($items) != 2)
						continue;
					list ($attr_name, $attr_value) = $items;
					if (strtoupper ($attr_name) == 'CN' and validTagName ('$lgcn_' . $attr_value, TRUE))
						$ret['memberof'][] = '$lgcn_' . $attr_value;
				}
	}
	@ldap_close ($connect);
	return $ret;
}

function authenticated_via_database ($userinfo, $password)
{
	if (!isset ($userinfo['user_id'])) // not a local account
		return FALSE;
	return $userinfo['user_password_hash'] == sha1 ($password);
}

?>
