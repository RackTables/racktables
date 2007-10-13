<?
require 'inc/init.php';
header ('WWW-Authenticate: Basic realm="' . getConfigVar ('enterprise') . ' RackTables access"');
header ('HTTP/1.0 401 Unauthorized');
showError ('You are now logged out.');
?>
