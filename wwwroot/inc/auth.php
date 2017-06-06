<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*

Below is a mix of authentication (confirming user's identity) and authorization
(access controlling) functions of RackTables.

Authentication methods are modular
To describe auth methods itselves there is a global variable $auth_methods
$auth_methods is an array where the key is the possible values of
$user_auth_src global variable. A value of $auth_methods must define
the following callbacks:
  * logout [optional]
  * get_username (you can assert on user headers there for example) [optional]
  * authenticate (authentication process itself) [required]
The result of authentication process is $remote_username
variable being set (via get_username return or by authenticate method) and
authenticate method should return TRUE.
Authentication code can use call_auth_method_op() for doing fallback logic as well.

*/

// load auth methods
$auth_methods = array();
if (FALSE !== $auth_files = glob ("${racktables_rootdir}/inc/auth-methods/*.php"))
	foreach ($auth_files as $auth_file)
		 require_once $auth_file;
if (FALSE !== $auth_files = glob ("${racktables_plugins_dir}/auth-methods/*.php"))
	foreach ($auth_files as $auth_file)
		 require_once $auth_file;

function call_auth_method_op ($method, $op)
{
	global $auth_methods;
	$handler = array_fetch ($auth_methods[$method], $op, NULL);
	if (is_callable ($handler))
	{
		$args = func_get_args();
		array_shift ($args);
		array_shift ($args);
		return call_user_func_array ($handler, $args);
	}
	else
		return NULL;
}

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
		$script_mode,
		$require_local_account,
		$auth_methods;
	// Phase 1. Assert basic pre-requisites, short-circuit the logout request.
	if (! isset ($user_auth_src) || ! isset ($require_local_account))
		throw new RackTablesError ('secret.php: either user_auth_src or require_local_account are missing', RackTablesError::MISCONFIGURED);
	if (! isset ($auth_methods[$user_auth_src]))
		throw new RackTablesError ("Invalid authentication source!", RackTablesError::MISCONFIGURED);
	if (isset ($_REQUEST['logout']))
	{
		call_auth_method_op ($user_auth_src, 'logout');
		throw new RackTablesError ('', RackTablesError::NOT_AUTHENTICATED); // Reset browser credentials cache.
	}
	// Phase 2. Do some method-specific processing, initialize $remote_username on success.
	$userinfo = NULL;
	if (! isset ($script_mode) || ! $script_mode || ! isset ($remote_username) || $remote_username == '')
		$remote_username = call_auth_method_op ($user_auth_src, 'get_username');
	// Phase 3. Handle local account requirement.
	if (isset ($remote_username) && strlen ($remote_username))
	{
		$userinfo = constructUserCell ($remote_username);
		if ($require_local_account && ! isset ($userinfo['user_id']))
			throw new RackTablesError ('', RackTablesError::NOT_AUTHENTICATED);
	}
	// Phase 4. Do the method-specific authentication.
	if ((! isset ($script_mode) || ! $script_mode) && ! call_auth_method_op ($user_auth_src, 'authenticate', $userinfo))
		throw new RackTablesError ('', RackTablesError::NOT_AUTHENTICATED);
	// Phase 5. Handle local account requirement. Again.
	if (! isset ($userinfo))
	{
		$userinfo = constructUserCell ($remote_username);
		if ($require_local_account && ! isset ($userinfo['user_id']))
			throw new RackTablesError ('', RackTablesError::NOT_AUTHENTICATED);
	}
	// Phase 6. Pull user tags into security context, set $remote_displayname
	$user_given_tags = $userinfo['etags'];
	$auto_tags = array_merge ($auto_tags, $userinfo['atags']);
	if ($userinfo['user_realname'] != '')
		$remote_displayname = $userinfo['user_realname']; // local value is most preferred
	if (! isset ($remote_displayname) || ! strlen ($remote_displayname))
		$remote_displayname = $remote_username;
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
