<?php

$auth_methods['httpd'] = array
(
	'authenticate' => 'authenticated_via_httpd',
);

$auth_methods['database'] = array
(
	'get_username' => 'auth_database_get_username',
	'authenticate' => 'authenticated_via_database',
);

function authenticated_via_httpd()
{
	if
	(
		! isset ($_SERVER['REMOTE_USER']) or
		$_SERVER['REMOTE_USER'] == ''
	)
		throw new RackTablesError ('The web-server didn\'t authenticate the user, although ought to do.', RackTablesError::MISCONFIGURED);
	$remote_username = $_SERVER['REMOTE_USER'];
}

function auth_database_get_username()
{
	assertHTTPCredentialsReceived();
	return $_SERVER['PHP_AUTH_USER'];
}

function authenticated_via_database ($userinfo)
{
	if (! isset ($userinfo['user_id'])) // not a local account
		return FALSE;
	return $userinfo['user_password_hash'] == sha1 ($_SERVER['PHP_AUTH_PW']);
}
