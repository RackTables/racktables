<?php

// Miscellaneous test helpers.
class TestHelper
{

	// Throws if the dsn name doesn't contain the string "_unittest".
	//
	// Assuming here that a production database would never contain
	// that string.  See tests/README for more details.
	public static function ensureUsingUnitTestDatabase()
	{
		global $pdo_dsn;
		if (stristr($pdo_dsn, '_unittest') === FALSE) {
			throw new Exception("Test must connect to unit testing database (see tests/README).");
		}
	}
}
?>
