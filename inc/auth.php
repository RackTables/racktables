<?php
/*

Authentication library for RackTables.

*/

// This function ensures that we don't continue without a legitimate
// username and password (also make sure, that both are present, this
// is especially useful for LDAP auth code to not deceive itself with
// anonymous binding).
function authenticate ()
{
	if
	(
		!isset ($_SERVER['PHP_AUTH_USER']) or
		!strlen ($_SERVER['PHP_AUTH_USER']) or
		!isset ($_SERVER['PHP_AUTH_PW']) or
		!strlen ($_SERVER['PHP_AUTH_PW']) or
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
	global $ldap_server, $ldap_domain, $ldap_search_dn, $ldap_search_attr;
	if ($connect = @ldap_connect ($ldap_server))
	{
		if
		(
			!isset ($ldap_search_dn) or
			!isset ($ldap_search_attr) or
			empty ($ldap_search_dn) or
			empty ($ldap_search_attr)
		)
			$user_name = $username . "@" . $ldap_domain;
		else
		{
			$results = @ldap_search ($connect, $ldap_search_dn, "(${ldap_search_attr}=${username})", array("dn"));
			if (@ldap_count_entries ($connect, $results) != 1)
			{
				@ldap_close ($connect);
				return FALSE;
			}
			$info = @ldap_get_entries($connect,$results);
			$user_name = $info[0]['dn'];
		}
		if ($bind = @ldap_bind ($connect, $user_name, $password))
		{
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
