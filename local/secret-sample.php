<?php
/*
*
*  RackTables secrets are here
*
*/

$pdo_dsn = 'mysql:host=localhost;dbname=racktables';
$db_username = 'username';
$db_password = 'password';

// More info: http://racktables.org/trac/wiki/RackTablesUserAuthentication
$user_auth_src = 'database';
$require_valid_user = TRUE;

// This is only necessary for 'ldap' authentication source
$ldap_server = 'some.server';
$ldap_domain = 'some.domain';

#$ldap_search_dn = 'ou=people,O=YourCompany';
$ldap_search_attr = 'uid';

// LDAP cache, values in seconds. Refresh, retry and expiry values are
// treated exactly as for DNS SOA record.
// Unconditionally remeber success for 5 minutes, then contact server, if
// possible. If this didn't work for whatever reason, repeat attempts each
// 15 seconds. After 10 minutes from the first successful authentication
// discard the cache for that user.
$ldap_cache_refresh = 300;
$ldap_cache_retry = 15;
$ldap_cache_expiry = 600;

?>
