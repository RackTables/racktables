<?php

/*
A pure function is a function where the return value is only determined by its
input values, without observable side effects. An unary function is a function
that takes one argument. Binary and ternary functions take two and three
arguments respectively.

For every given combination of pure function name, argument(s) and expected
return value test that the actual return value is equal (assertEquals) or
identical (assertSame) to the expected return value.
*/

class PureFunctionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider providerUnaryEquals
	 */
	public function testUnaryEquals ($func, $input1, $output)
	{
		$this->assertEquals ($output, $func ($input1));
	}

	/**
	 * @dataProvider providerUnarySame
	 */
	public function testUnarySame ($func, $input1, $output)
	{
		$this->assertSame ($output, $func ($input1));
	}

	/**
	 * @dataProvider providerBinaryEquals
	 */
	public function testBinaryEquals ($func, $input1, $input2, $output)
	{
		$this->assertEquals ($output, $func ($input1, $input2));
	}

	/**
	 * @dataProvider providerBinarySame
	 */
/*
	public function testBinarySame ($func, $input1, $input2, $output)
	{
		$this->assertSame ($output, $func ($input1, $input2));
	}
*/

	/**
	 * @dataProvider providerTernaryEquals
	 */
/*
	public function testTernaryEquals ($func, $input1, $input2, $input3, $output)
	{
		$this->assertEquals ($output, $func ($input1, $input2, $input3));
	}
*/

	/**
	 * @dataProvider providerTernarySame
	 */
/*
	public function testTernarySame ($func, $input1, $input2, $input3, $output)
	{
		$this->assertSame ($output, $func ($input1, $input2, $input3));
	}
*/

	public function providerUnaryEquals ()
	{
		return array
		(
			// Test the implicit 2nd argument (not the same as the 2-ary tests below).
			array
			(
				'formatVSIP',
				array ('vip' => "\x5d\xb8\xd8\x22"),
				'<a href="index.php?page=ipaddress&ip=93.184.216.34">93.184.216.34</a>'
			),
			array
			(
				'formatVSIP',
				array ('vip' => "\x26\x06\x28\x00\x02\x20\x00\x01\x02\x48\x18\x93\x25\xc8\x19\x46"),
				'<a href="index.php?page=ipaddress&ip=2606%3A2800%3A220%3A1%3A248%3A1893%3A25c8%3A1946">2606:2800:220:1:248:1893:25c8:1946</a>'
			),

			array ('numSign', -100, -1),
			array ('numSign', -1, -1),
			array ('numSign', 0, 0),
			array ('numSign', 1, 1),
			array ('numSign', 100, 1),

			array ('dos2unix', "", ""),
			array ('dos2unix', "\r\n", "\n"),
			array ('dos2unix', "line1\r\nline2\r\nline3", "line1\nline2\nline3"),
			array ('dos2unix', "line1\r\n\r\n\r\nline2\r\n", "line1\n\n\nline2\n"),
			array ('dos2unix', "line1\n\rline2\n\r", "line1\n\rline2\n\r"), // Mac style

			array ('unix2dos', "", ""),
			array ('unix2dos', "\n", "\r\n"),
			array ('unix2dos', "line1\nline2", "line1\r\nline2"),
			array ('unix2dos', "\n\nline1\n\nline2\n\n\n", "\r\n\r\nline1\r\n\r\nline2\r\n\r\n\r\n"),
			array ('unix2dos', "line1", "line1"),
		);
	}

	public function providerUnarySame ()
	{
		return array
		(
			// coalescing functions
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

			array ('array_first', array (1, 2, 3), 1),
			array ('array_first', array (FALSE, NULL, 0), FALSE),
			array ('array_first', array (-1, 0, 1), -1),
			array ('array_first', array (), NULL), // not an exception in the current implementation

			array ('array_last', array (1, 2, 3), 3),
			array ('array_last', array (FALSE, NULL, 0), 0),
			array ('array_last', array (-1, 0, 1), 1),
			array ('array_last', array (), NULL), // not an exception in the current implementation
		);
	}

	public function providerBinaryEquals ()
	{
		return array
		(
			array
			(
				'getSelectOptions',
				array
				(
					1 => 'one',
					2 => 'two',
					3 => 'three',
				),
				NULL,
				'<option value=\'1\'>one</option>' .
				'<option value=\'2\'>two</option>' .
				'<option value=\'3\'>three</option>'
			),
			array
			(
				'getSelectOptions',
				array
				(
					1 => 'one',
					2 => 'two',
					3 => 'three',
				),
				2,
				'<option value=\'1\'>one</option>' .
				'<option value=\'2\' selected>two</option>' .
				'<option value=\'3\'>three</option>'
			),
			array
			(
				'getSelectOptions',
				array
				(
					1 => 'one',
					2 => 'two',
					3 => 'three',
				),
				array(),
				'<option value=\'1\'>one</option>' .
				'<option value=\'2\'>two</option>' .
				'<option value=\'3\'>three</option>'
			),
			array
			(
				'getSelectOptions',
				array
				(
					1 => 'one',
					2 => 'two',
					3 => 'three',
				),
				array (2, 3),
				'<option value=\'1\'>one</option>' .
				'<option value=\'2\' selected>two</option>' .
				'<option value=\'3\' selected>three</option>'
			),
			array
			(
				'getSelectOptions',
				array
				(
					1 => '<one>',
					2 => '&two;',
					3 => '\'"three"\'',
					4 => '    ',
				),
				NULL,
				'<option value=\'1\'>&lt;one&gt;</option>' .
				'<option value=\'2\'>&amp;two;</option>' .
				'<option value=\'3\'>&#039;&quot;three&quot;&#039;</option>' .
				'<option value=\'4\'>    </option>'
			),

			array
			(
				'formatVSIP',
				array ('vip' => "\x5d\xb8\xd8\x22"),
				TRUE,
				'93.184.216.34'
			),
			array
			(
				'formatVSIP',
				array ('vip' => "\x5d\xb8\xd8\x22"),
				FALSE,
				'<a href="index.php?page=ipaddress&ip=93.184.216.34">93.184.216.34</a>'
			),
			array
			(
				'formatVSIP',
				array ('vip' => "\x26\x06\x28\x00\x02\x20\x00\x01\x02\x48\x18\x93\x25\xc8\x19\x46"),
				TRUE,
				'2606:2800:220:1:248:1893:25c8:1946'
			),
			array
			(
				'formatVSIP',
				array ('vip' => "\x26\x06\x28\x00\x02\x20\x00\x01\x02\x48\x18\x93\x25\xc8\x19\x46"),
				FALSE,
				'<a href="index.php?page=ipaddress&ip=2606%3A2800%3A220%3A1%3A248%3A1893%3A25c8%3A1946">2606:2800:220:1:248:1893:25c8:1946</a>'
			),

			array ('numCompare', 100, 0, 1),
			array ('numCompare', -100, 1, -1),
			array ('numCompare', 100, 100, 0),
			array ('numCompare', 0, -100, 1),
			array ('numCompare', 0, 100, -1),

			array
			(
				'tagOnChain',
				array ('id' => 1),
				array(),
				FALSE
			),
			array
			(
				'tagOnChain',
				array ('id' => 1),
				array (array ('id' => 1)),
				TRUE
			),
			array
			(
				'tagOnChain',
				array ('id' => 4),
				array (array ('id' => 1), array ('id' => 2), array ('id' => 3)),
				FALSE
			),
			array
			(
				'tagOnChain',
				array ('id' => 4),
				array (array ('id' => 1), array ('id' => 2), array ('id' => 3), array ('id' => 4)),
				TRUE
			),
			array
			(
				'tagOnChain',
				array ('key' => 1), // wrong keying
				array (array ('id' => 1)),
				FALSE
			),

			array
			(
				'tagNameOnChain',
				'one',
				array(),
				FALSE
			),
			array
			(
				'tagNameOnChain',
				'one',
				array (array ('tag' => 'one')),
				TRUE
			),
			array
			(
				'tagNameOnChain',
				'four',
				array (array ('tag' => 'one'), array ('tag' => 'two'), array ('tag' => 'FOUR')), // case-sensitive
				FALSE
			),
			array
			(
				'tagNameOnChain',
				'four',
				array (array ('tag' => 'one'), array ('tag' => 'two'), array ('tag' => 'three'), array ('tag' => 'four')),
				TRUE
			),

			array
			(
				'array_values_same',
				array(),
				array(),
				TRUE
			),
			array
			(
				'array_values_same',
				array (1, 2, 3, 4, 5),
				array (5, 4, 3, 2, 1),
				TRUE
			),
			array
			(
				'array_values_same',
				array (1, 2, 3, 4, 5),
				array (1, 2, 3, 4),
				FALSE
			),
			array
			(
				'array_values_same',
				array (1, 2, 3),
				array (1, 2, 3, 3),
				TRUE // a non-obvious but valid behaviour
			),
			array
			(
				'array_values_same',
				array (1 => 'a', 2 => 'a', 3 => 'a', 4 => 'b', 5 => 'b'),
				array (6 => 'a', 7 => 'b'),
				TRUE // idem
			),
			array
			(
				'array_values_same',
				array ('0', '1'),
				array (1, 0),
				TRUE
			),
			array
			(
				'array_values_same',
				array (0, 1),
				array (FALSE, TRUE),
				FALSE
			),
			array
			(
				'array_values_same',
				array (NULL),
				array (0),
				FALSE
			),
			array
			(
				'array_values_same',
				array (FALSE),
				array (NULL),
				TRUE // string representation is the same
			),
			array
			(
				'array_values_same',
				array (TRUE),
				array (1),
				TRUE // idem
			),
		);
	}

	public function providerBinarySame ()
	{
		return array
		(
		);
	}

	public function providerTernaryEquals ()
	{
		return array
		(
		);
	}

	public function providerTernarySame ()
	{
		return array
		(
		);
	}
}
?>
