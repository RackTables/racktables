<?php
/*

Authentication library for RackTables.

*/

// This function ensures that we don't continue without a legitimate
// username and password (also make sure, that both are present, this
// is especially useful for LDAP auth code to not deceive itself with
// anonymous binding). It also initializes $remote_username and $accounts.
// Fatal errors are followed by exit (1) to aid in script debugging.
function authenticate ()
{
	global $remote_username, $remote_displayname, $accounts, $user_auth_src, $require_valid_user, $script_mode;
	if (!isset ($user_auth_src) or !isset ($require_valid_user))
	{
		showError ('secret.php misconfiguration: either user_auth_src or require_valid_user are missing', __FUNCTION__);
		exit (1);
	}
	$accounts = getUserAccounts();
	if ($accounts === NULL)
	{
		showError ('Failed to initialize access database.', __FUNCTION__);
		exit (1);
	}
	if (isset ($script_mode) and $script_mode === TRUE)
		return;
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
	if ($require_valid_user and !isset ($accounts[$remote_username]))
		dieWith401();
	if (isset ($accounts[$remote_username]) and $accounts[$remote_username]['user_enabled'] != 'yes')
		dieWith401();
	$remote_displayname = $remote_username;
	switch (TRUE)
	{
		// Just trust the server, because the password isn't known.
		case ('httpd' == $user_auth_src):
			if (authenticated_via_httpd ($remote_username))
			{
				$remote_displayname = "EXT: ${remote_username}";
				return;
			}
			break;
		// When using LDAP, leave a mean to fix things. Admin user is always authenticated locally.
		case ('database' == $user_auth_src or $accounts[$remote_username]['user_id'] == 1):
			if (authenticated_via_database ($remote_username, $_SERVER['PHP_AUTH_PW']))
			{
				if (!empty ($accounts[$remote_username]['user_realname']))
					$remote_displayname = $accounts[$remote_username]['user_realname'];
				return;
			}
			break;
		case ('ldap' == $user_auth_src):
			// Call below also sets $remote_displayname.
			if (authenticated_via_ldap ($remote_username, $_SERVER['PHP_AUTH_PW']))
			{
				if (!empty ($accounts[$remote_username]['user_realname']))
					$remote_displayname = $accounts[$remote_username]['user_realname'];
				return;
			}
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
		$impl_tags,
		$annex
	);
	$subject[] = array ('tag' => '$page_' . $p);
	$subject[] = array ('tag' => '$tab_' . $t);
	if ($o === NULL and isset ($op))
	{
		$subject[] = array ('tag' => '$op_' . $op);
		$subject[] = array ('tag' => '$any_op');
	}
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

function accessibleSubpage ($p)
{
	global $user_tags;
	$subject = $user_tags;
	$subject[] = array ('tag' => '$page_' . $p);
	$subject[] = array ('tag' => '$tab_default');
	return gotClearanceForTagChain ($subject);
}

function authenticated_via_ldap ($username, $password)
{
	global $ldap_server, $ldap_domain, $ldap_search_dn, $ldap_search_attr;
	global $remote_username, $remote_displayname, $ldap_displayname_attrs;
	if ($connect = @ldap_connect ($ldap_server))
	{
		if (isset ($ldap_domain) and !empty ($ldap_domain))
			$auth_user_name = $username . "@" . $ldap_domain;
		elseif
		(
			isset ($ldap_search_dn) and
			!empty ($ldap_search_dn) and
			isset ($ldap_search_attr) and
			!empty ($ldap_search_attr)
		)
		{
			$results = @ldap_search ($connect, $ldap_search_dn, "(${ldap_search_attr}=${username})", array("dn"));
			if (@ldap_count_entries ($connect, $results) != 1)
			{
				@ldap_close ($connect);
				return FALSE;
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
		if ($bind = @ldap_bind ($connect, $auth_user_name, $password))
		{
			// Some servers deny anonymous search, thus search only after binding.
			// Displayed name only makes sense for authenticated users anyway.
			if
			(
				isset ($ldap_displayname_attrs) and
				count ($ldap_displayname_attrs) and
				isset ($ldap_search_dn) and
				!empty ($ldap_search_dn) and
				isset ($ldap_search_attr) and
				!empty ($ldap_search_attr)
			)
			{
				$results = @ldap_search
				(
					$connect,
					$ldap_search_dn,
					"(${ldap_search_attr}=${username})",
					array_merge (array ('memberof'), $ldap_displayname_attrs)
				);
				if (@ldap_count_entries ($connect, $results) == 1 or TRUE)
				{
					$info = @ldap_get_entries ($connect, $results);
					ldap_free_result ($results);
					$remote_displayname = '';
					$space = '';
					foreach ($ldap_displayname_attrs as $attr)
					{
						$remote_displayname .= $space . $info[0][$attr][0];
						$space = ' ';
					}
					// Pull group membership, if any was returned.
					if (isset ($info[0]['memberof']))
					{
						global $auto_tags;
						for ($i = 0; $i < $info[0]['memberof']['count']; $i++)
							foreach (explode (',', $info[0]['memberof'][$i]) as $pair)
							{
								list ($attr_name, $attr_value) = explode ('=', $pair);
								if ($attr_name == 'CN')
								{
									$auto_tags[] = array ('tag' => "\$lgcn_${attr_value}");
									break;
								}
							}
					}
				}
			}
			@ldap_close ($connect);
			return TRUE;
		}
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

function authenticated_via_httpd ($username)
{
	// Reaching here means, that .htaccess authentication passed.
	// Let's make sure, that user exists in the database, and give clearance.
	global $accounts;
	return isset ($accounts[$username]);
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
