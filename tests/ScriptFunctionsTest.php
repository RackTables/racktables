<?php

class ScriptFunctionsTest extends PHPUnit_Framework_TestCase
{
	const SCRIPT_NAME = 'temp_test_script';
	const SCRIPT_TEXT = "abc\ndef\nghi";

	/**
	 * @group small
	 */
	public function testReadExisting ()
	{
		saveScript (self::SCRIPT_NAME, self::SCRIPT_TEXT);
		$this->assertEquals (self::SCRIPT_TEXT, loadScript (self::SCRIPT_NAME));
		deleteScript (self::SCRIPT_NAME);
	}

	/**
	 * @group small
	 */
	public function testReadNonexisting1 ()
	{
		saveScript (self::SCRIPT_NAME, self::SCRIPT_TEXT);
		deleteScript (self::SCRIPT_NAME);
		$this->assertNull (loadScript (self::SCRIPT_NAME));
	}

	/**
	 * @group small
	 */
	public function testReadNonexisting2 ()
	{
		saveScript (self::SCRIPT_NAME, self::SCRIPT_TEXT);
		saveScript (self::SCRIPT_NAME, NULL); // deletes
		$this->assertNull (loadScript (self::SCRIPT_NAME));
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testSaveEmptyName ()
	{
		saveScript ('', NULL);
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testSaveNULLName ()
	{
		saveScript (NULL, NULL);
	}
}

?>
