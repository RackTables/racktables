<?php
global $pdo_dsn, $db_username, $db_password;
global $remote_username, $SQLSchema, $configCache, $script_mode;

$script_mode = TRUE;
require_once '../wwwroot/inc/ophandlers.php';
require_once '../wwwroot/inc/init.php';
require_once '../wwwroot/inc/interface.php';
require_once '../wwwroot/inc/snmp.php';
require_once './TestHelper.php';

// Sanity check DB connection
TestHelper::ensureUsingUnitTestDatabase ();
?>
