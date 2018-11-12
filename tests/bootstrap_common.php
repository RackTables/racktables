<?php
global $pdo_dsn, $db_username, $db_password;
global $remote_username, $SQLSchema, $configCache, $script_mode;

$script_mode = TRUE;
require_once '../wwwroot/inc/ophandlers.php';
require_once '../wwwroot/inc/init.php';
require_once '../wwwroot/inc/interface.php';
require_once '../wwwroot/inc/snmp.php';

class RTTestCase extends RTTestCaseShim
{
	protected function myString ($s)
	{
		return sprintf ('%s-%s-%u', $s, get_class ($this), getmypid());
	}
	protected static function myStringStatic ($s, $c)
	{
		return sprintf ('%s-%s-%u', $s, $c, getmypid());
	}
}

// Sanity check DB connection
// Assuming here that a production database would never contain
// that string.  See tests/README for more details.

if (stristr (getDBName(), '_unittest') === FALSE)
	throw new Exception ('Test must connect to unit testing database (see tests/README).');
