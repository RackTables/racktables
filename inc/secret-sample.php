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

?>
