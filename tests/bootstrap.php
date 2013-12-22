<?php
WARNING: Do not run these tests against a production instance of RackTables!
WARNING: Data may be added, modified or deleted as part of the tests.
WARNING: If you understand the risk, delete these warning lines.
global $pdo_dsn, $db_username, $db_password;
global $remote_username, $SQLSchema, $configCache, $script_mode;
$script_mode = TRUE;
require_once '../wwwroot/inc/init.php';
require_once '../wwwroot/inc/interface.php';
?>
