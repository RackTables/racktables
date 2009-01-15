<?php
/*
*
*  RackTables secrets are here
*
*/

$pdo_dsn = 'mysql:host=localhost;dbname=racktables';
$db_username = 'username';
$db_password = 'password';

// This is only necessary for 'ldap' USER_AUTH_SRC
$ldap_server = 'some.server';
$ldap_domain = 'some.domain';

// See http://racktables.org/trac/wiki/RackTablesLdapAuth for detailed explanation.
#$ldap_search_dn = 'ou=people,O=YourCompany';
$ldap_search_attr = 'uid';

?>
