<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*
*
* This file contains the login page handler for RackTables.
* It provides a minimal HTML login form that replaces HTTP basic auth.
*
*/

# Login page handler - renders the login form
function renderLoginPage ()
{
	global $error_message, $configCache;

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
	echo '<head><title>RackTables: Login</title>';
	echo '<style type="text/css">';
	echo 'body { font-family: Arial, Sans-Serif, sans-serif; font-size: small; background-color: #f0f0f0; margin: 0; padding: 0; }';
	echo '.login-box { width: 320px; margin: 100px auto; background-color: #ffffff; border: 1px solid #a0a0a0; padding: 20px; }';
	echo '.login-box h2 { text-align: center; margin: 0 0 20px 0; color: #333333; }';
	echo '.login-box table { width: 100%; }';
	echo '.login-box td { padding: 4px 0; }';
	echo '.login-box th { text-align: right; padding: 4px 8px 4px 0; color: #333333; }';
	echo '.login-box input[type="text"], .login-box input[type="password"] { width: 100%; padding: 4px; box-sizing: border-box; }';
	echo '.login-box input[type="submit"] { width: 100%; padding: 6px; cursor: pointer; }';
	echo '.login-error { background-color: #ffa0a0; border: 1px solid red; color: black; padding: 8px; text-align: center; margin-bottom: 10px; font-weight: bold; }';
	echo '.login-footer { text-align: center; margin-top: 10px; color: #666666; font-size: x-small; }';
	echo '</style>';
	echo '</head>';
	echo '<body>';
	echo '<div class="login-box">';
	echo '<h2>RackTables Login</h2>';

	if (isset ($error_message))
		echo '<div class="login-error">' . htmlspecialchars ($error_message) . '</div>';

	echo '<form method="post" action="index.php?module=login&amp;action=authenticate">';
	echo '<table>';
	echo '<tr><th>Username:</th><td><input type="text" name="username" autofocus /></td></tr>';
	echo '<tr><th>Password:</th><td><input type="password" name="password" /></td></tr>';
	echo '</table>';
	echo '<table border=0 cellspacing=0 cellpadding=0 width="100%"><tr><td class="submit"><input type="submit" value="Login" /></td></tr></table>';
	echo '</form>';
	$enterprise = getConfigVar ('enterprise');
	echo '<div class="login-footer">' . $enterprise . ' RackTables ' . CODE_VERSION . '</div>';
	echo '</div>';
	echo '</body>';
	echo '</html>';
}

# Login form submission handler
function loginAuthenticate ()
{
	global $remote_username, $remote_displayname, $require_local_account;

	$username = isset ($_REQUEST['username']) ? trim ($_REQUEST['username']) : '';
	$password = isset ($_REQUEST['password']) ? $_REQUEST['password'] : '';

	if ($username === '' || $password === '')
	{
		global $error_message;
		$error_message = 'Please enter both username and password.';
		renderLoginPage ();
		return;
	}

	try
	{
		# Get user info from database (already connected via init.php)
		$userinfo = constructUserCell ($username);

		# Check if local account is required and user exists
		if ($require_local_account && ! isset ($userinfo['user_id']))
		{
			global $error_message;
			$error_message = 'Authentication failed: invalid username or password.';
			renderLoginPage ();
			return;
		}

		# Verify password against database hash
		if (! authenticated_via_database ($userinfo, $password))
		{
			global $error_message;
			$error_message = 'Authentication failed: invalid username or password.';
			renderLoginPage ();
			return;
		}

		# Authentication successful - set session globals
		$remote_username = $username;
		$remote_displayname = $userinfo['user_realname'] != '' ? $userinfo['user_realname'] : $username;

		# Store credentials in $_SERVER so authenticate() downstream is satisfied
		$_SERVER['PHP_AUTH_USER'] = $username;
		$_SERVER['PHP_AUTH_PW'] = $password;

		# Start session and store user info
		startSession();
		$_SESSION['rt_authenticated'] = TRUE;
		$_SESSION['rt_username'] = $username;
		$_SESSION['rt_displayname'] = $remote_displayname;
		session_commit();

		# Redirect to main interface
		redirectUser ('index.php?page=index&tab=default');
	}
	catch (Exception $e)
	{
		global $error_message;
		$error_message = 'Authentication error: ' . $e->getMessage ();
		renderLoginPage ();
	}
}

# Logout handler - clears session
function loginLogout ()
{
	startSession();
	$_SESSION = array();
	session_destroy();
	session_commit();

	# Redirect to login page
	redirectUser ('index.php?module=login');
}
