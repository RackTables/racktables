<?php

// This covers the unary form of the function, which depends on a configuration
// variable. The binary form is pure and is covered by PureFunctionTest.
class SQLDateFromDateStrTest extends RTTestCase
{
	protected static $old_format;

	public static function setUpBeforeClass () : void
	{
		self::$old_format = getConfigVar ('DATEONLY_FORMAT');
		setConfigVar ('DATEONLY_FORMAT', '%Y-%m-%d');
	}

	public static function tearDownAfterClass () : void
	{
		setConfigVar ('DATEONLY_FORMAT', self::$old_format);
	}

	/**
	 * @group small
	 * @dataProvider providerNormal
	 */
	public function testUnaryNormal ($input, $output)
	{
		$this->assertEquals ($output, SQLDateFromDateStr ($input));
	}

	/**
	 * @group small
	 * @dataProvider providerUnaryIAE
	 */
	public function testUnaryIAE ($input)
	{
		$this->expectException (InvalidArgException::class);
		SQLDateFromDateStr ($input);
	}

	public function providerNormal ()
	{
		return array
		(
			array ('2017-1-1', '2017-01-01'),
			array ('2017-01-01', '2017-01-01'),
		);
	}

	public function providerUnaryIAE ()
	{
		return array
		(
			array (NULL),
			array (0),
			array (''),
			array ('abcdef'),
			array ('12/21/2000'),
			array ('20.05.1973'),
			array ('2001-19-4'),
			array ('2001-02-29'),
			array ('768-11-15'),
		);
	}
}
?>
