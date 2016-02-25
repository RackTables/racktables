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
	 * @group small
	 * @dataProvider providerUnaryEquals
	 */
	public function testUnaryEquals ($func, $input1, $output)
	{
		$this->assertEquals ($output, $func ($input1));
	}

	/**
	 * @group small
	 * @dataProvider providerUnarySame
	 */
	public function testUnarySame ($func, $input1, $output)
	{
		$this->assertSame ($output, $func ($input1));
	}

	/**
	 * @group small
	 * @dataProvider providerBinaryEquals
	 */
	public function testBinaryEquals ($func, $input1, $input2, $output)
	{
		$this->assertEquals ($output, $func ($input1, $input2));
	}

	/**
	 * @group small
	 * @dataProvider providerBinarySame
	 */
	public function testBinarySame ($func, $input1, $input2, $output)
	{
		$this->assertSame ($output, $func ($input1, $input2));
	}

	/**
	 * @group small
	 * @dataProvider providerTernaryEquals
	 */
	public function testTernaryEquals ($func, $input1, $input2, $input3, $output)
	{
		$this->assertEquals ($output, $func ($input1, $input2, $input3));
	}

	/**
	 * @group small
	 * @dataProvider providerTernarySame
	 */
/*
	public function testTernarySame ($func, $input1, $input2, $input3, $output)
	{
		$this->assertSame ($output, $func ($input1, $input2, $input3));
	}
*/

	// There is a separate test/provider method pair for each exception class as
	// $this->expectException() is only available in later versions of PHPUnit.

	/**
	 * @group small
	 * @dataProvider providerNaryIAE
	 * @expectedException InvalidArgException
	 */
	public function testNaryIAE ($func, $input)
	{
		call_user_func_array ($func, $input);
	}

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

			array ('questionMarks', 1, '?'),
			array ('questionMarks', 2, '?, ?'),
			array ('questionMarks', 3, '?, ?, ?'),

			array
			(
				'formatPortIIFOIF',
				array ('iif_id' => 1, 'iif_name' => 'hardwired', 'oif_id' => 24, 'oif_name' => '1000Base-T'),
				'1000Base-T'
			),
			array
			(
				'formatPortIIFOIF',
				array ('iif_id' => 1, 'iif_name' => 'hardwired', 'oif_id' => 40, 'oif_name' => '10GBase-CX4'),
				'10GBase-CX4'
			),
			array
			(
				'formatPortIIFOIF',
				array ('iif_id' => 8, 'iif_name' => 'XFP', 'oif_id' => 30, 'oif_name' => '10GBase-SR'),
				'XFP/10GBase-SR'
			),
			array
			(
				'formatPortIIFOIF',
				array ('iif_id' => 10, 'iif_name' => 'QSFP+', 'oif_id' => 1663, 'oif_name' => '40GBase-SR4'),
				'QSFP+/40GBase-SR4'
			),

			array ('acceptable8021QConfig', array ('mode' => 'unknown'), FALSE),
			array ('acceptable8021QConfig', array ('mode' => 'trunk', 'native' => 0, 'allowed' => array (1, 2, 3)), TRUE),
			array ('acceptable8021QConfig', array ('mode' => 'trunk', 'native' => 1, 'allowed' => array (1, 2, 3)), TRUE),
			array ('acceptable8021QConfig', array ('mode' => 'trunk', 'native' => 2, 'allowed' => array()), FALSE),
			array ('acceptable8021QConfig', array ('mode' => 'trunk', 'native' => 4, 'allowed' => array (1, 2, 3)), FALSE),
			array ('acceptable8021QConfig', array ('mode' => 'access', 'native' => 2, 'allowed' => array (2)), TRUE),
			array ('acceptable8021QConfig', array ('mode' => 'access', 'native' => 0, 'allowed' => array (3)), FALSE),
			array ('acceptable8021QConfig', array ('mode' => 'access', 'native' => 2, 'allowed' => array (3)), FALSE),
			array ('acceptable8021QConfig', array ('mode' => 'access', 'native' => 1, 'allowed' => array (1, 2, 3)), FALSE),
			array ('acceptable8021QConfig', array ('mode' => 'access', 'native' => 3, 'allowed' => array()), FALSE),
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

			// implicit 2nd argument
			array ('groupIntsToRanges', array(), array()),
			array ('groupIntsToRanges', array (1, 2, 3, 4, 5), array ('1-5')),
			array ('groupIntsToRanges', array (11, 12, 13, 20, 21), array ('11-13', '20-21')),
			array ('groupIntsToRanges', array (1, 2, 3, 15, 16, 17, 18, 23, 24), array ('1-3', '15-18', '23-24')),
			array ('groupIntsToRanges', array (10), array (10)),
			array ('groupIntsToRanges', array (10, 14), array (10, 14)),
			array ('groupIntsToRanges', array (10, 12, 14), array (10, 12, 14)),
			array ('groupIntsToRanges', array (10, 12, 13, 14), array (10, '12-14')),
			array ('groupIntsToRanges', array (10, 11, 12, 14), array ('10-12', 14)),
			array ('groupIntsToRanges', array (10, 11, 13, 15, 16), array ('10-11', 13, '15-16')),

			array ('HTTPDateToUnixTime', '', FALSE),
			array ('HTTPDateToUnixTime', 'now', FALSE),
			array ('HTTPDateToUnixTime', 'next Tuesday', FALSE),
			# RFC 822 (1123)
			array ('HTTPDateToUnixTime', 'Thu, 01 Jan 1970 00:00:00 +0000', 0), # OK
			array ('HTTPDateToUnixTime', 'Thu, 15 Feb 2007 15:00:00 +0000', 1171551600), # OK
			array ('HTTPDateToUnixTime', 'Thu, 29 Feb 2000 00:00:00 GMT', 951782400), # OK, leap year
			array ('HTTPDateToUnixTime', 'Thu, 29 Feb 2001 00:00:00 GMT', FALSE), # invalid date
			array ('HTTPDateToUnixTime', 'Thu, 29 Feb 2000 12:20:60 GMT', FALSE), # invalid time
			array ('HTTPDateToUnixTime', 'Thu, 01 January 1970 00:00:00 +0000', FALSE), # invalid month name
			# RFC 850 (1036)
			array ('HTTPDateToUnixTime', 'Sunday, 21-Feb-07 15:00:00 -0000', 1172070000), # OK
			array ('HTTPDateToUnixTime', 'Monday, 15-Oct-07 19:00:00 +0000', 1192474800), # OK
			array ('HTTPDateToUnixTime', 'Tuesda, 15-Oct-07 19:00:00 +0000', FALSE), # invalid week day name
			array ('HTTPDateToUnixTime', 'Monday, 15-Oct-07 19:00:00 JST', FALSE), # invalid time zone
			array ('HTTPDateToUnixTime', 'Thursday, 01-Jan-98 00:00:00 GMT', 883612800), # OK
			# asctime()
			array ('HTTPDateToUnixTime', 'Thu Mar  8 12:00:00 2007', 1173355200), # OK
			array ('HTTPDateToUnixTime', 'Tus Mar  8 12:00:00 2007', FALSE), # invalid week day name
			array ('HTTPDateToUnixTime', 'Wed Dec 31 23:59:59 1997', 883612799), # OK
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

			array ('cmp_array_sizes', array(), array(), 0),
			array ('cmp_array_sizes', array (NULL), array (FALSE), 0),
			array ('cmp_array_sizes', array (0, TRUE), array (FALSE, ''), 0),
			array ('cmp_array_sizes', array (0), array(), 1),
			array ('cmp_array_sizes', array (2), array(1, 1), -1),
		);
	}

	public function providerBinarySame ()
	{
		return array
		(
			// explicit 2nd argument
			array ('groupIntsToRanges', array(), NULL, array()),
			array ('groupIntsToRanges', array (1, 2, 3, 4, 5), NULL, array ('1-5')),
			array ('groupIntsToRanges', array (11, 12, 13, 20, 21), NULL, array ('11-13', '20-21')),
			array ('groupIntsToRanges', array (1, 2, 3, 15, 16, 17, 18, 23, 24), NULL, array ('1-3', '15-18', '23-24')),
			array ('groupIntsToRanges', array (10), NULL, array (10)),
			array ('groupIntsToRanges', array (10, 14), NULL, array (10, 14)),
			array ('groupIntsToRanges', array (10, 12, 14), NULL, array (10, 12, 14)),
			array ('groupIntsToRanges', array (10, 12, 13, 14), NULL, array (10, '12-14')),
			array ('groupIntsToRanges', array (10, 11, 12, 14), NULL, array ('10-12', 14)),
			array ('groupIntsToRanges', array (10, 11, 13, 15, 16), NULL, array ('10-11', 13, '15-16')),

			array ('groupIntsToRanges', array (1, 2, 3, 4, 5), 1, array ('2-5')),
			array ('groupIntsToRanges', array (1, 2, 3, 4, 5), 2, array (1, '3-5')),
			array ('groupIntsToRanges', array (1, 2, 3, 4, 5), 3, array ('1-2', '4-5')),
			array ('groupIntsToRanges', array (1, 2, 3, 4, 5), 4, array ('1-3', 5)),
			array ('groupIntsToRanges', array (1, 2, 3, 4, 5), 5, array ('1-4')),
		);
	}

	public function providerTernaryEquals ()
	{
		return array
		(
			array ('array_fetch', array(), 1, 'default', 'default'),
			array ('array_fetch', array (3 => 'three', 2 => 'two', 1 => 'one'), 1, 'four', 'one'),
			array ('array_fetch', array (3 => 'three', 2 => 'two', 1 => 'one'), 5, 'five', 'five'),
			array ('array_fetch', array (1 => 'one', 2 => 'two'), 3, NULL, NULL),
			// PHP array keying uses equality, not identity
			array ('array_fetch', array (1 => 'one', 2 => 'two'), '1', 'default', 'one'),
		);
	}

	public function providerTernarySame ()
	{
		return array
		(
		);
	}

	public function providerNaryIAE ()
	{
		return array
		(
			array ('questionMarks', array (0)),
		);
	}
}
?>
