<?php

class StringInsertHrefsTest extends PHPUnit_Framework_TestCase
{
	protected $detect_urls_var = NULL;

	public function setUp ()
	{
		// make sure DETECT_URLS is set to yes
		$this->detect_urls_var = getConfigVar ('DETECT_URLS'); 
		if ($this->detect_urls_var != 'yes')
			setConfigVar ('DETECT_URLS', 'yes');
	}

	public function tearDown ()
	{
		// restore DETECT_URLS to original setting
		if ($this->detect_urls_var != 'yes')
			setConfigVar ('DETECT_URLS', $this->detect_urls_var);
	}

	/**
	 * @dataProvider provider
	 */
	public function testStringInsertHrefs ($input, $output)
	{
		$this->assertEquals (string_insert_hrefs ($input), $output);
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
?>
