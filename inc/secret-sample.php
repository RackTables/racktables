<?php
/*
*
*  RackTables secrets are here
*
*/

$pdo_dsn = 'mysql:host=localhost;dbname=racktables';
$db_username = 'username';
$db_password = 'password';

// User authentication source: 'database', 'ldap', 'httpd'.
// See http://racktables.org/trac/wiki/RackTablesUserAuthentication for detailed explanation.
define ('USER_AUTH_SRC', 'database');

// This is only necessary for 'ldap' USER_AUTH_SRC
$ldap_server = 'some.server';
$ldap_domain = 'some.domain';

#$ldap_search_dn = 'ou=people,O=YourCompany';
$ldap_search_attr = 'uid';

?>
