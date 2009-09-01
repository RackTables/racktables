<?php
/*
*
*  RackTables secrets are here
*
*/

$pdo_dsn = 'mysql:host=localhost;dbname=racktables';
$db_username = 'username';
$db_password = 'password';

// Default setting is to authenticate users locally, but it is possible to
// employ existing LDAP or Apache userbase. Uncommenting below two lines MAY
// help in switching authentication to LDAP completely.
// More info: http://racktables.org/trac/wiki/RackTablesAdminGuide
#$user_auth_src = 'ldap';
#$require_local_account = FALSE;

// This is only necessary for 'ldap' authentication source
$LDAP_options = array
(
	'server' => 'some.server',
	'domain' => 'some.domain',
#	'search_dn' => 'ou=people,O=YourCompany',
	'search_attr' => 'uid',
#	'displayname_attrs' => 'givenname familyname',

// LDAP cache, values in seconds. Refresh, retry and expiry values are
// treated exactly as those for DNS SOA record. Example values 300-15-600:
// unconditionally remeber successful auth for 5 minutes, after that still
// permit user access, but try to revalidate username and password on the
// server (not more often, than once in 15 seconds). After 10 minutes of
// unsuccessful retries give up and deny access, so someone goes to fix
// LDAP server.
	'cache_refresh' => 300,
	'cache_retry' => 15,
	'cache_expiry' => 600,
// Uncomment line below to work around ActiveDirectory+PHP problem to perform
// search right in the base DN (not in one of OUs/CNs below).
#	'options' => array (LDAP_OPT_PROTOCOL_VERSION => 3, LDAP_OPT_REFERRALS => 0),
);

?>
