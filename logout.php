<?
require 'inc/init.php';
global $enterprise;
header ("WWW-Authenticate: Basic realm=\"${enterprise} RackTables access\"");
header ('HTTP/1.0 401 Unauthorized');
showError ('You are now logged out.');
?>
