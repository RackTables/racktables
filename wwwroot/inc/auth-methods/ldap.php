<?php

$auth_methods['ldap'] = array
(
	'get_username' => 'auth_ldap_get_username',
	'authenticate' => 'authenticated_via_ldap',
);

function auth_ldap_get_username()
{
	assertHTTPCredentialsReceived();
	return $_SERVER['PHP_AUTH_USER'];
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
function authenticated_via_ldap ($userinfo)
{
	global $LDAP_options, $debug_mode, $remote_displayname;

	// When using LDAP, leave a mean to fix things. Admin user is always authenticated locally.
	if (isset ($userinfo['user_id']) && $userinfo['user_id'] == 1)
		return call_auth_method_op ('database', 'authenticate', $userinfo);

	$username = $userinfo['user_name'];
	$password = $_SERVER['PHP_AUTH_PW'];
	constructLDAPOptions();

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
			return authenticated_via_ldap_nocache ($username, $password, $remote_displayname);
		// authenticated_via_ldap_cache()'s way of locking can sometimes result in
		// a PDO error condition that convertPDOException() was not able to dispatch.
		// To avoid reaching printPDOException() (which prints backtrace with password
		// argument in cleartext), any remaining PDO condition is converted locally.
		return authenticated_via_ldap_cache ($username, $password, $remote_displayname);
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

	// Decide on the username we will actually authenticate for.
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
