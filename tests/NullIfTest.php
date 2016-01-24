<?php

class NullIfTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider provider
	 */
	public function testCoalescing ($func, $input, $output)
	{
		$this->assertSame ($output, $func ($input));
	}

	public function provider ()
	{
		return array
		(
			array ('nullIfEmptyStr', 'abc', 'abc'),
			array ('nullIfEmptyStr', '', NULL), // intended use case: '' == ''
			array ('nullIfEmptyStr', '0', '0'),
			array ('nullIfEmptyStr', 0, NULL), // type conversion: 0 == ''
			array ('nullIfEmptyStr', NULL, NULL), // type conversion: NULL == ''
			array ('nullIfEmptyStr', FALSE, NULL), // type conversion: FALSE == ''

			array ('nullIfFalse', '', ''),
			array ('nullIfFalse', '0', '0'),
			array ('nullIfFalse', 0, 0),
			array ('nullIfFalse', FALSE, NULL), // intended use case: FALSE === FALSE
			array ('nullIfFalse', NULL, NULL),

			array ('nullIfZero', 'abc', NULL), // type conversion: 'abc' == 0
			array ('nullIfZero', '', NULL), // type conversion: '' == 0
			array ('nullIfZero', '0', NULL), // type conversion: '0' == 0
			array ('nullIfZero', '1', '1'),
			array ('nullIfZero', 0, NULL), // intended use case: 0 == 0
			array ('nullIfZero', 1, 1),
			array ('nullIfZero', NULL, NULL), // type conversion: NULL == 0
			array ('nullIfZero', FALSE, NULL), // type conversion: FALSE == 0
		);
	}
}
?>
