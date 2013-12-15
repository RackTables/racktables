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

	public function testStringInsertHrefs ()
	{
		$cases = array
		(
			'no_href' => array
			(
				'input'  => 'This is a string with no links.',
				'output' => 'This is a string with no links.'
			),
			'short_hostname' => array
			(
				'input'  => 'http://server/wiki/index.php/objectname',
				'output' => '<a href="http://server/wiki/index.php/objectname">http://server/wiki/index.php/objectname</a> [<a href="http://server/wiki/index.php/objectname" target="_blank">^</a>]'
			),
			'auth_creds' => array
			(
				'input'  => 'http://user:pass@www.example.tld/',
				'output' => '<a href="http://user:pass@www.example.tld/">http://user:pass@www.example.tld/</a> [<a href="http://user:pass@www.example.tld/" target="_blank">^</a>]'
			),
			'mailto' => array
			(
				'input'  => 'username@example.tld',
				'output' => '<a href="mailto:username@example.tld">username@example.tld</a>'
			)
		);
		foreach ($cases as $case)
			$this->assertEquals (string_insert_hrefs ($case['input']), $case['output']);
	}
}
?>
