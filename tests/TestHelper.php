<?php

// Miscellaneous test helpers.
class TestHelper
{
	// Throws if the database name doesn't contain the string "_unittest".
	//
	// Assuming here that a production database would never contain
	// that string.  See tests/README for more details.
	public static function ensureUsingUnitTestDatabase ()
	{
		if (stristr (getDBName(), '_unittest') === FALSE)
			throw new Exception ('Test must connect to unit testing database (see tests/README).');
	}
}
?>
