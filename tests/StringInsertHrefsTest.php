<?php

class StringInsertHrefsTest extends RTTestCase
{
	protected static $detect_urls_var;

	public static function setUpBeforeClass ()
	{
		// make sure DETECT_URLS is set to yes
		self::$detect_urls_var = getConfigVar ('DETECT_URLS');
		if (self::$detect_urls_var != 'yes')
			setConfigVar ('DETECT_URLS', 'yes');
	}

	public static function tearDownAfterClass ()
	{
		// restore DETECT_URLS to original setting
		if (self::$detect_urls_var != 'yes')
			setConfigVar ('DETECT_URLS', self::$detect_urls_var);
	}

	/**
	 * @group small
	 * @dataProvider provider
	 */
	public function testStringInsertHrefs ($input, $output)
	{
		$this->assertEquals ($output, string_insert_hrefs ($input));
	}

	public function provider ()
	{
		return array
		(
			array
			(
				'This is a string with no links.',
				'This is a string with no links.'
			),
			array
			(
				'http://server/wiki/index.php/objectname',
				'<a href="http://server/wiki/index.php/objectname">http://server/wiki/index.php/objectname</a> [<a href="http://server/wiki/index.php/objectname" target="_blank">^</a>]'
			),
			array
			(
				'http://user:pass@www.example.tld/',
				'<a href="http://user:pass@www.example.tld/">http://user:pass@www.example.tld/</a> [<a href="http://user:pass@www.example.tld/" target="_blank">^</a>]'
			),
			array
			(
				'username@example.tld',
				'<a href="mailto:username@example.tld">username@example.tld</a>'
			)
		);
	}
}
