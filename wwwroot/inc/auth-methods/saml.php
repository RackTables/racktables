<?php

$auth_methods['saml'] = array
(
	'authenticate' => 'authenticated_via_saml',
	'logout' => 'saml_logout',
);


// a wrapper for SAML auth method
function authenticated_via_saml()
{
	global $SAML_options, $auto_tags, $remote_username, $remote_displayname;
	if (! file_exists ($SAML_options['simplesamlphp_basedir'] . '/lib/_autoload.php'))
		throw new RackTablesError ('Configured for SAML authentication, but simplesaml is not found.', RackTablesError::MISCONFIGURED);
	require_once ($SAML_options['simplesamlphp_basedir'] . '/lib/_autoload.php');
	$as = new SimpleSAML_Auth_Simple ($SAML_options['sp_profile']);
	if (! $as->isAuthenticated())
		$as->requireAuth();
	$attributes = $as->getAttributes();
	$remote_username = saml_getAttributeValue ($attributes, $SAML_options['usernameAttribute']);
	$remote_displayname = saml_getAttributeValue ($attributes, $SAML_options['fullnameAttribute']);
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
