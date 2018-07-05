<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*

Below is a mix of authentication (confirming user's identity) and authorization
(access controlling) functions of RackTables. The former set is expected to
be working with only database.php file included.

*/

// This function ensures that the web-interface does not continue without legitimate
// username and password (also make sure that both are present, this
// is especially useful for LDAP auth code to not deceive itself with
// anonymous binding). It also initializes $remote_* and $*_tags vars.
function authenticate ()
{
	function assertHTTPCredentialsReceived()
	{
		if
		(
			! isset ($_SERVER['PHP_AUTH_USER']) ||
			$_SERVER['PHP_AUTH_USER'] == '' ||
			! isset ($_SERVER['PHP_AUTH_PW']) ||
			$_SERVER['PHP_AUTH_PW'] == ''
		)
			throw new RackTablesError ('', RackTablesError::NOT_AUTHENTICATED);
	}

	global
		$remote_username,
		$remote_displayname,
		$auto_tags,
		$user_given_tags,
		$user_auth_src,
		$script_mode,
		$require_local_account;
	// Phase 1. Assert basic pre-requisites, short-circuit the logout request.
	if (! isset ($user_auth_src) || ! isset ($require_local_account))
		throw new RackTablesError ('secret.php: either user_auth_src or require_local_account are missing', RackTablesError::MISCONFIGURED);
	if (isset ($_REQUEST['logout']))
	{
		if (isset ($user_auth_src) && 'saml' == $user_auth_src)
			saml_logout ();
		throw new RackTablesError ('', RackTablesError::NOT_AUTHENTICATED); // Reset browser credentials cache.
	}
	// Phase 2. Do some method-specific processing, initialize $remote_username on success.
	switch (TRUE)
	{
		case isset ($script_mode) && $script_mode && isset ($remote_username) && $remote_username != '':
			break; // skip this phase
		case 'database' == $user_auth_src:
			assertHTTPCredentialsReceived();
			$remote_username = $_SERVER['PHP_AUTH_USER'];
			break;
		case 'ldap' == $user_auth_src:
			assertHTTPCredentialsReceived();
			$remote_username = $_SERVER['PHP_AUTH_USER'];
			constructLDAPOptions();
			break;
		case 'httpd' == $user_auth_src:
			if
			(
				! isset ($_SERVER['REMOTE_USER']) or
				$_SERVER['REMOTE_USER'] == ''
			)
				throw new RackTablesError ('The web-server didn\'t authenticate the user, although ought to do.', RackTablesError::MISCONFIGURED);
			$remote_username = $_SERVER['REMOTE_USER'];
			break;
		case 'saml' == $user_auth_src:
			$saml_username = '';
			$saml_dispname = '';
			if (! authenticated_via_saml ($saml_username, $saml_dispname))
				throw new RackTablesError ('', RackTablesError::NOT_AUTHENTICATED);
			$remote_username = $saml_username;
			break;
		default:
			throw new RackTablesError ('Invalid authentication source!', RackTablesError::MISCONFIGURED);
	}
	// Phase 3. Handle local account requirement, pull user tags into security context.
	$userinfo = constructUserCell ($remote_username);
	if ($require_local_account && ! isset ($userinfo['user_id']))
		throw new RackTablesError ('', RackTablesError::NOT_AUTHENTICATED);
	$user_given_tags = $userinfo['etags'];
	$auto_tags = array_merge ($auto_tags, $userinfo['atags']);
	// Phase 4. Do more method-specific processing, initialize $remote_displayname on success.
	switch (TRUE)
	{
		case isset ($script_mode) && $script_mode:
			return; // success
		// Just trust the server, because the password isn't known.
		case 'httpd' == $user_auth_src:
			$remote_displayname = $userinfo['user_realname'] != '' ?
				$userinfo['user_realname'] :
				$remote_username;
			return; // success
		// When using LDAP, leave a mean to fix things. Admin user is always authenticated locally.
		case array_key_exists ('user_id', $userinfo) && $userinfo['user_id'] == 1:
		case 'database' == $user_auth_src:
			$remote_displayname = $userinfo['user_realname'] != '' ?
				$userinfo['user_realname'] :
				$remote_username;
			if (authenticated_via_database ($userinfo, $_SERVER['PHP_AUTH_PW']))
				return; // success
			break; // failure
		case 'ldap' == $user_auth_src:
			$ldap_dispname = '';
			if (! authenticated_via_ldap ($remote_username, $_SERVER['PHP_AUTH_PW'], $ldap_dispname))
				break; // failure
			$remote_displayname = $userinfo['user_realname'] != '' ? // local value is most preferred
				$userinfo['user_realname'] :
				($ldap_dispname != '' ? $ldap_dispname : $remote_username); // then one from LDAP
			return; // success
		case 'saml' == $user_auth_src:
			$remote_displayname = $saml_dispname != '' ? $saml_dispname : $saml_username;
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
	if ($o === NULL && $op != '') // $op can be set to empty string
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
	// The universal problem originates from the fact that certain rules may change
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
	// With the above being simple discrete algorythms, I believe that they very reliably
	// replicate human behavior. This gives a vast ground for further research, so I would
	// only note that the morale used in RackTables is "principles first".
	return gotClearanceForTagChain ($subject);
}

# a "throwing" wrapper for above
function assertPermission ($p = NULL, $t = NULL, $o = NULL, $annex = array())
{
	if (! permitted ($p, $t, $o, $annex))
		throw new RTPermissionDenied();
}

# Process a (globally available) RackCode permissions parse tree (which
# stands for a sequence of rules), evaluating each rule against a list of
# tags. This list of tags consists of (globally available) explicit and
# implicit tags plus some extra tags, available through the argument of the
# function. The latter tags are referred to as "constant" tags, because
# RackCode syntax allows for "context modifier" constructs, which result in
# implicit and explicit tags being assigned or unassigned. Such context
# changes remain in effect even upon return from this function.
function gotClearanceForTagChain ($const_base)
{
	global $rackCode, $expl_tags, $impl_tags;
	$context = array_merge ($const_base, $expl_tags, $impl_tags);
	$context = reindexById ($context, 'tag', TRUE);

	foreach ($rackCode as $sentence)
	{
		switch ($sentence['type'])
		{
			case 'SYNT_GRANT':
				if (eval_expression ($sentence['condition'], $context))
					return $sentence['decision'];
				break;
			case 'SYNT_ADJUSTMENT':
				if
				(
					eval_expression ($sentence['condition'], $context) &&
					processAdjustmentSentence ($sentence['modlist'], $expl_tags)
				) // recalculate implicit chain only after actual change, not just on matched condition
				{
					$impl_tags = getImplicitTags ($expl_tags); // recalculate
					$context = array_merge ($const_base, $expl_tags, $impl_tags);
					$context = reindexById ($context, 'tag', TRUE);
				}
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

// a wrapper for SAML auth method
function authenticated_via_saml (&$saml_username = NULL, &$saml_displayname = NULL)
{
	global $SAML_options, $auto_tags;
	if (! file_exists ($SAML_options['simplesamlphp_basedir'] . '/lib/_autoload.php'))
		throw new RackTablesError ('Configured for SAML authentication, but simplesaml is not found.', RackTablesError::MISCONFIGURED);
	require_once ($SAML_options['simplesamlphp_basedir'] . '/lib/_autoload.php');
	$as = new SimpleSAML_Auth_Simple ($SAML_options['sp_profile']);
	if (! $as->isAuthenticated())
		$as->requireAuth();
	$attributes = $as->getAttributes();
	$saml_username = saml_getAttributeValue ($attributes, $SAML_options['usernameAttribute']);
	$saml_displayname = saml_getAttributeValue ($attributes, $SAML_options['fullnameAttribute']);
	if (array_key_exists ('groupListAttribute', $SAML_options))
		foreach (saml_getAttributeValues ($attributes, $SAML_options['groupListAttribute']) as $autotag)
			$auto_tags[] = array ('tag' => '$sgcn_' . $autotag);
	return $as->isAuthenticated();
}

function saml_logout ()
{
	global $SAML_options;
	if (! file_exists ($SAML_options['simplesamlphp_basedir'] . '/lib/_autoload.php'))
		throw new RackTablesError ('Configured for SAML authentication, but simplesaml is not found.', RackTablesError::MISCONFIGURED);
	require_once ($SAML_options['simplesamlphp_basedir'] . '/lib/_autoload.php');
	$as = new SimpleSAML_Auth_Simple ($SAML_options['sp_profile']);
	header("Location: ".$as->getLogoutURL('/'));
	exit;
}

function saml_getAttributeValue ($attributes, $name)
{
	if (! isset ($attributes[$name]))
		return '';
	return is_array ($attributes[$name]) ? $attributes[$name][0] : $attributes[$name];
}

function saml_getAttributeValues ($attributes, $name)
{
	if (! isset ($attributes[$name]))
		return array();
	return is_array ($attributes[$name]) ? $attributes[$name] : array($attributes[$name]);
}

function constructLDAPOptions()
{
	global $LDAP_options;
	if (! isset ($LDAP_options))
		throw new RackTablesError ('$LDAP_options has not been defined (see secret.php)', RackTablesError::MISCONFIGURED);
	$LDAP_defaults = array
	(
		'group_attr' => 'memberof',
		'group_filter' => '/^[Cc][Nn]=([^,]+)/',
		'cache_refresh' => 300,
		'cache_retry' => 15,
		'cache_expiry' => 600,
	);
	foreach ($LDAP_defaults as $option_name => $option_value)
		if (! array_key_exists ($option_name, $LDAP_options))
			$LDAP_options[$option_name] = $option_value;
}

// a wrapper for two LDAP auth methods below
function authenticated_via_ldap ($username, $password, &$ldap_displayname)
{
	global $LDAP_options, $debug_mode;
	try
	{
		// Destroy the cache each time config changes.
		if ($LDAP_options['cache_expiry'] != 0 &&
			sha1 (serialize ($LDAP_options)) != loadScript ('LDAPConfigHash'))
		{
			discardLDAPCache();
			saveScript ('LDAPConfigHash', sha1 (serialize ($LDAP_options)));
			deleteScript ('LDAPLastSuccessfulServer');
		}

		if
		(
			$LDAP_options['cache_retry'] > $LDAP_options['cache_refresh'] ||
			$LDAP_options['cache_refresh'] > $LDAP_options['cache_expiry']
		)
			throw new RackTablesError ('LDAP misconfiguration: refresh/retry/expiry mismatch', RackTablesError::MISCONFIGURED);
		if ($LDAP_options['cache_expiry'] == 0) // immediate expiry set means disabled cache
			return authenticated_via_ldap_nocache ($username, $password, $ldap_displayname);
		// authenticated_via_ldap_cache()'s way of locking can sometimes result in
		// a PDO error condition that convertPDOException() was not able to dispatch.
		// To avoid reaching printPDOException() (which prints backtrace with password
		// argument in cleartext), any remaining PDO condition is converted locally.
		return authenticated_via_ldap_cache ($username, $password, $ldap_displayname);
	}
	catch (PDOException $e)
	{
		if (isset ($debug_mode) && $debug_mode)
			// in debug mode re-throw DB exception as-is
			throw $e;
		else
			// re-create exception to hide private data from its backtrace
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

// check that LDAP cache row contains correct password and is not expired
// if check_for_refreshing = TRUE, also checks that cache row does not need refreshing
function isLDAPCacheValid ($cache_row, $password_hash, $check_for_refreshing = FALSE)
{
	global $LDAP_options;
	return
		is_array ($cache_row) &&
		$cache_row['successful_hash'] === $password_hash &&
		$cache_row['success_age'] < $LDAP_options['cache_expiry'] &&
		(
			// There are two confidence levels of cache hits: "certain" and "uncertain". In either case
			// expect authentication success, unless it's well-timed to perform a retry,
			// which may sometimes bring a NAK decision.
			! $check_for_refreshing ||
			(
				$cache_row['success_age'] < $LDAP_options['cache_refresh'] ||
				isset ($cache_row['retry_age']) &&
				$cache_row['retry_age'] < $LDAP_options['cache_retry']
			)
		);
}

// Idem, but consider existing data in cache and modify/discard it, when necessary.
// Remember to have releaseLDAPCache() called before any return statement.
// Perform cache maintenance on each update.
function authenticated_via_ldap_cache ($username, $password, &$ldap_displayname)
{
	global $LDAP_options, $auto_tags;

	$user_data = array(); // fill auto_tags and ldap_displayname from this array
	$password_hash = sha1 ($password);

	// first try to get cache row without locking it (quick way)
	$cache_row = fetchLDAPCacheRow ($username);
	if (isLDAPCacheValid ($cache_row, $password_hash, TRUE))
		$user_data = $cache_row; // cache HIT
	else
	{
		// cache miss or expired. Try to lock LDAPCache for $username
		$cache_row = acquireLDAPCache ($username);
		if (isLDAPCacheValid ($cache_row, $password_hash, TRUE))
			$user_data = $cache_row; // cache HIT, but with DB lock
		else
		{
			$ldap_answer = queryLDAPServer ($username, $password);
			switch ($ldap_answer['result'])
			{
			case 'ACK':
				replaceLDAPCacheRecord ($username, $password_hash, $ldap_answer['displayed_name'], $ldap_answer['memberof']);
				$user_data = $ldap_answer;
				break;
			case 'NAK': // The record isn't valid any more.
				// TODO: negative result caching
				deleteLDAPCacheRecord ($username);
				break;
			case 'CAN': // LDAP query failed, use old value till next retry
				if (isLDAPCacheValid ($cache_row, $password_hash, FALSE))
				{
					touchLDAPCacheRecord ($username);
					$user_data = $cache_row;
				}
				else
					deleteLDAPCacheRecord ($username);
				break;
			default:
				throw new RackTablesError ('structure error', RackTablesError::INTERNAL);
			}
		}
		releaseLDAPCache();
	}

	if ($user_data)
	{
		$ldap_displayname = $user_data['displayed_name'];
		foreach ($user_data['memberof'] as $autotag)
			$auto_tags[] = array ('tag' => $autotag);
		return TRUE;
	}
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

	if (extension_loaded ('ldap') === FALSE)
		throw new RackTablesError ('LDAP misconfiguration. LDAP PHP Module is not installed.', RackTablesError::MISCONFIGURED);

	$ldap_cant_connect_codes = array
	(
		-1,			// Can't contact LDAP server error
		-5,			// LDAP Timed out error
		-11,		// LDAP connect error
	);

	$last_successful_server = loadScript ('LDAPLastSuccessfulServer');
	$success_server = NULL;
	$servers = preg_split ("/\s+/", $LDAP_options['server'], NULL, PREG_SPLIT_NO_EMPTY);
	if (isset ($last_successful_server) && in_array ($last_successful_server, $servers))	// Cached server is still present in config ?
	{
		// Use last successful server first
		$servers = array_diff ($servers, array ($last_successful_server));
		array_unshift ($servers, $last_successful_server);
	}
	// Try to connect to each server until first success
	foreach ($servers as $server)
	{
		$connect = @ldap_connect ($server, array_fetch ($LDAP_options, 'port', 389));
		if ($connect === FALSE)
			continue;
		ldap_set_option ($connect, LDAP_OPT_NETWORK_TIMEOUT, array_fetch ($LDAP_options, 'server_alive_timeout', 2));
		// If use_tls configuration option is set, then try establish TLS session instead of ldap_bind
		if (isset ($LDAP_options['use_tls']) && $LDAP_options['use_tls'] >= 1)
		{
			$tls = ldap_start_tls ($connect);
			if ($LDAP_options['use_tls'] >= 2 && $tls == FALSE)
			{
				if (in_array (ldap_errno ($connect), $ldap_cant_connect_codes))
					continue;
				else
					throw new RackTablesError ('LDAP misconfiguration: LDAP TLS required but not successfully negotiated.', RackTablesError::MISCONFIGURED);
			}
			$success_server = $server;
			break;
		}
		else
		{
			if (@ldap_bind ($connect) || !in_array (ldap_errno ($connect), $ldap_cant_connect_codes))
			{
				$success_server = $server;
				// Cleanup after check. This connection will be used below
				@ldap_unbind ($connect);
				$connect = ldap_connect ($server, array_fetch ($LDAP_options, 'port', 389));
				break;
			}
		}
	}
	if (!isset ($success_server))
		return array ('result' => 'CAN');
	if ($LDAP_options['cache_expiry'] != 0 &&
		$last_successful_server !== $success_server)
		saveScript ('LDAPLastSuccessfulServer', $success_server);

	if (array_key_exists ('options', $LDAP_options) && is_array ($LDAP_options['options']))
		foreach ($LDAP_options['options'] as $opt_code => $opt_value)
			ldap_set_option ($connect, $opt_code, $opt_value);

	// Build the server's version of the user's username for ldap_bind(). This may
	// involve an anonymous (or a non-anonymous, with another ldap_bind()) LDAP search.
	if (isset ($LDAP_options['domain']) && $LDAP_options['domain'] != '')
		$auth_user_name = $username . "@" . $LDAP_options['domain'];
	elseif
	(
		isset ($LDAP_options['search_dn']) &&
		$LDAP_options['search_dn'] != '' &&
		isset ($LDAP_options['search_attr']) &&
		$LDAP_options['search_attr'] != ''
	)
	{
		// If a search_bind_rdn is supplied, bind to that and use it to search.
		// This is required unless a server offers anonymous searching.
		// Using bind again on the connection works as expected.
		// The password is optional as it might be optional on server, too.
		if (isset ($LDAP_options['search_bind_rdn']) && $LDAP_options['search_bind_rdn'] != '')
		{
			$search_bind = @ldap_bind
			(
				$connect,
				$LDAP_options['search_bind_rdn'],
				isset ($LDAP_options['search_bind_password']) ? $LDAP_options['search_bind_password'] : NULL
			);
			if ($search_bind === FALSE)
				throw new RackTablesError
				(
					'LDAP misconfiguration. You have specified a search_bind_rdn ' .
					(isset ($LDAP_options['search_bind_password']) ? 'with' : 'without') .
					' a search_bind_password, but the server refused it with: ' . ldap_error ($connect),
					RackTablesError::MISCONFIGURED
				);
		}
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
		isset ($LDAP_options['displayname_attrs']) &&
		$LDAP_options['displayname_attrs'] != '' &&
		isset ($LDAP_options['search_dn']) &&
		$LDAP_options['search_dn'] != '' &&
		isset ($LDAP_options['search_attr']) &&
		$LDAP_options['search_attr'] != ''
	)
	{
		$results = @ldap_search
		(
			$connect,
			$LDAP_options['search_dn'],
			'(' . $LDAP_options['search_attr'] . "=${username})",
			array_merge (array ($LDAP_options['group_attr']), explode (' ', $LDAP_options['displayname_attrs']))
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
			if (isset ($info[0][$attr]))
			{
				$ret['displayed_name'] .= $space . $info[0][$attr][0];
				$space = ' ';
			}
		// Pull group membership, if any was returned.
		if (isset ($info[0][$LDAP_options['group_attr']]))
			for ($i = 0; $i < $info[0][$LDAP_options['group_attr']]['count']; $i++)
				if
				(
					preg_match ($LDAP_options['group_filter'], $info[0][$LDAP_options['group_attr']][$i], $matches) &&
					validTagName ('$lgcn_' . $matches[1], TRUE)
				)
					$ret['memberof'][] = '$lgcn_' . $matches[1];
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
