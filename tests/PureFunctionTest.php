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
	public function testTernarySame ($func, $input1, $input2, $input3, $output)
	{
		$this->assertSame ($output, $func ($input1, $input2, $input3));
	}

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

	/**
	 * @group small
	 * @dataProvider providerMakeWhereSQL
	 */
	public function testMakeWhereSQL ($where_columns, $conjunction, $expected_str, $expected_params)
	{
		$actual_str = makeWhereSQL ($where_columns, $conjunction, $actual_params);
		$this->assertEquals ($expected_str, $actual_str);
		$this->assertSame ($expected_params, $actual_params);
	}

	// This test requires a custom function to pass a parameter by reference.

	/**
	 * @group small
	 * @dataProvider providerTreeApplyFuncIAE
	 * @expectedException InvalidArgException
	 */
	public function testTreeApplyFuncIAE ($func, $stopfunc)
	{
		$forest_byref = array();
		treeApplyFunc ($forest_byref, $func, $stopfunc);
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

			array ('makeSetSQL', array ('one'), 'one=?'),
			array ('makeSetSQL', array ('one', 'two'), 'one=?, two=?'),
			array ('makeSetSQL', array ('one', 'two', 'three'), 'one=?, two=?, three=?'),

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

			// XXX: The data set below covers only two modes though the function can accept more. It is not
			// clear whether those additional branches are dead or they need to be tested as well. Also there
			// are no tests for invalid input as the function does not throw an exception on error and that
			// should be fixed first.
			array ('serializeVLANPack', array ('mode' => 'access', 'allowed' => array (290), 'native' => 290), 'A290'),
			array ('serializeVLANPack', array ('mode' => 'trunk', 'allowed' => array (290), 'native' => 290), 'T290'),
			array ('serializeVLANPack', array ('mode' => 'trunk', 'allowed' => array (291, 292, 293), 'native' => 0), 'T+291-293'),
			array ('serializeVLANPack', array ('mode' => 'trunk', 'allowed' => array (294, 300, 305), 'native' => 305), 'T305+294, 300'),
			array ('serializeVLANPack', array ('mode' => 'trunk', 'allowed' => array (2, 3, 4, 5, 6, 7, 8, 9), 'native' => 5), 'T5+2-4, 6-9'),

			array ('listToRanges', array(), array()),
			array ('listToRanges', array (7), array (array ('from' => 7, 'to' => 7))),
			array ('listToRanges', array (2, 4, 3, 5, 1), array (array ('from' => 1, 'to' => 5))),
			array ('listToRanges', array (12, 24, 23, 25, 11), array (array ('from' => 11, 'to' => 12), array ('from' => 23, 'to' => 25))),
			array ('listToRanges', array (22, 24, 23, 25, 11), array (array ('from' => 11, 'to' => 11), array ('from' => 22, 'to' => 25))),

			array ('iosParseVLANString', '10', array (10)),
			array ('iosParseVLANString', '10,20', array (10, 20)),
			array ('iosParseVLANString', '10, 20, 30', array (10, 20, 30)),
			array ('iosParseVLANString', '3-8', array (3, 4, 5, 6, 7, 8)),
			array ('iosParseVLANString', '10, 20-25, 30', array (10, 20, 21, 22, 23, 24, 25, 30)),

			array ('textareaCooked', '', array()),
			array ('textareaCooked', '  ', array()),
			array ('textareaCooked', "\r\n\r\n", array()),
			array ('textareaCooked', "\n \n\t\n \n", array()),
			array ('textareaCooked', 'abcd', array ('abcd')),
			array ('textareaCooked', "\nabcd\n", array ('abcd')),
			array ('textareaCooked', "\r\n  \r\nabcd\r\n  \t\t         \r\n", array ('abcd')),
			array ('textareaCooked', "abcd\r\n   efgh\r\nijkl        \r\n", array ('abcd', 'efgh', 'ijkl')),

			array ('l2addressForDatabase', '', ''),
			array ('l2addressForDatabase', ' ', ''),
			array ('l2addressForDatabase', ' 010203abcdef ', '010203ABCDEF'), // RE_L2_SOLID
			array ('l2addressForDatabase', '1:2:3:ab:cd:ef ', '010203ABCDEF'), // RE_L2_IFCFG
			array ('l2addressForDatabase', ' 0102.03ab.cdef', '010203ABCDEF'), // RE_L2_CISCO
			array ('l2addressForDatabase', '0102-03ab-cdef', '010203ABCDEF'), // RE_L2_HUAWEI
			array ('l2addressForDatabase', '01-02-03-ab-cd-ef', '010203ABCDEF'), // RE_L2_IPCFG
			array ('l2addressForDatabase', '000000000000', ''), // a special case
			array ('l2addressForDatabase', '0102030405abcdef  ', '0102030405ABCDEF'), // RE_L2_WWN_SOLID
			array ('l2addressForDatabase', ' 01-02-03-04-05-ab-cd-ef', '0102030405ABCDEF'), // RE_L2_WWN_HYPHEN
			array ('l2addressForDatabase', ' 1:2:3:4:5:ab:cd:ef ', '0102030405ABCDEF'), // RE_L2_WWN_COLON

			array ('nextMACAddress', '', ''),
			array ('nextMACAddress', '12:34:56:78:90:ab', '12:34:56:78:90:AC'),
			array ('nextMACAddress', '12:34:56:78:90:ff', '12:34:56:78:91:00'),
			array ('nextMACAddress', '12:34:56:78:ff:ff', '12:34:56:79:00:00'),
			array ('nextMACAddress', '12:34:56:ff:ff:ff', '12:34:57:00:00:00'),
			array ('nextMACAddress', '12:34:56:fe:ff:ff', '12:34:56:FF:00:00'),

			// implicit 2nd argument
			array ('validTagName', 'tag', TRUE),
			array ('validTagName', 'another tag', TRUE),
			array ('validTagName', 'another-tag', TRUE),
			array ('validTagName', 'another_tag', TRUE),
			array ('validTagName', 'abc 123 def', TRUE),
			array ('validTagName', '100%', TRUE),
			array ('validTagName', 'racks 20~39', TRUE),
			array ('validTagName', 'racks 40+', TRUE),
			array ('validTagName', 'racks 10:', TRUE),
			array ('validTagName', '2nd tag', TRUE),
			array ('validTagName', 'left+right', TRUE),
			array ('validTagName', '2015', TRUE),
			array ('validTagName', '2015-2016', TRUE),
			array ('validTagName', '-2016', TRUE),
			array ('validTagName', '-50%', TRUE),
			array ('validTagName', 'size: XL', TRUE),
			array ('validTagName', 'size: XL+', TRUE),
			array ('validTagName', 'iqn.domain', TRUE),
			array ('validTagName', '', FALSE),
			array ('validTagName', ' tag ', FALSE),
			array ('validTagName', 'tag?', FALSE),
			array ('validTagName', 'tag!', FALSE),
			array ('validTagName', 'tag_', FALSE),
			array ('validTagName', 'tag~', FALSE),
			array ('validTagName', 'tag.', FALSE),
			array ('validTagName', '$tag', FALSE),
			array ('validTagName', '2015-', FALSE),
			array ('validTagName', 'iqn.domain.', FALSE),

			array ('parseSearchTerms', '', array ()),
			array ('parseSearchTerms', 'sixty', array ('sixty')),
			array ('parseSearchTerms', '"sixty "', array ('sixty')),
			array ('parseSearchTerms', 'seventy сімдесят', array ('seventy', 'сімдесят')),
			array ('parseSearchTerms', '"seventy" сімдесят', array ('seventy', 'сімдесят')),
			array ('parseSearchTerms', 'seventy " сімдесят"', array ('seventy', 'сімдесят')),
			array ('parseSearchTerms', '"seventy" "сімдесят"', array ('seventy', 'сімдесят')),
			array ('parseSearchTerms', 'eighty вісімдесят восемьдесят', array ('eighty', 'вісімдесят', 'восемьдесят')),
			array ('parseSearchTerms', '" seventy one "', array ('seventy one')),
			array ('parseSearchTerms', '"seventy one" сімдесят', array ('seventy one', 'сімдесят')),
			array ('parseSearchTerms', 'seventy "сімдесят один"', array ('seventy', 'сімдесят один')),
			array ('parseSearchTerms', '"eighty one" вісімдесят восемьдесят', array ('eighty one', 'вісімдесят', 'восемьдесят')),
			array ('parseSearchTerms', 'eighty "вісімдесят один" восемьдесят', array ('eighty', 'вісімдесят один', 'восемьдесят')),
			array ('parseSearchTerms', 'eighty вісімдесят "восемьдесят один"', array ('eighty', 'вісімдесят', 'восемьдесят один')),
			array ('parseSearchTerms', '"eighty one" "вісімдесят один" "восемьдесят один"', array ('eighty one', 'вісімдесят один', 'восемьдесят один')),
		);
	}

	public function providerUnarySame ()
	{
		$ten_kilo = array_fill_keys (range (0, 10000), 0);
		unset ($ten_kilo[0]);
		$ten_kilo_less_3 = $ten_kilo;
		unset ($ten_kilo_less_3[1560]);
		unset ($ten_kilo_less_3[1561]);
		unset ($ten_kilo_less_3[1562]);
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
			array ('HTTPDateToUnixTime', 'Thu Mar 8 12:00:00 2007', FALSE), # missing space
			array ('HTTPDateToUnixTime', 'Tus Mar  8 12:00:00 2007', FALSE), # invalid week day name
			array ('HTTPDateToUnixTime', 'Wed Dec 31 23:59:59 1997', 883612799), # OK

			array ('ip4_mask', 0, "\x00\x00\x00\x00"),
			array ('ip4_mask', 24, "\xFF\xFF\xFF\x00"),
			array ('ip4_mask', 32, "\xFF\xFF\xFF\xFF"),

			array ('ip6_mask', 0, "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"),
			array ('ip6_mask', 80, "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x00\x00\x00\x00\x00\x00"),
			array ('ip6_mask', 128, "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"),

			array ('ip_get_arpa', "\x0A\x0B\x0C\x0D", '13.12.11.10.in-addr.arpa'),
			array ('ip_get_arpa', "\xC0\x00\x02\xFF", '255.2.0.192.in-addr.arpa'),
			array ('ip_get_arpa', "\xC0\xA8\xC0\xFF", '255.192.168.192.in-addr.arpa'),
			array ('ip_get_arpa', "\xAC\x11\xBB\x00", '0.187.17.172.in-addr.arpa'),
			array
			(
				'ip_get_arpa',
				"\x20\x01\x0D\xB8\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x01",
				'1.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.8.b.d.0.1.0.0.2.ip6.arpa'
			),
			array
			(
				'ip_get_arpa',
				"\x20\x01\x0D\xB8\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C",
				'c.0.b.0.a.0.9.0.8.0.7.0.6.0.5.0.4.0.3.0.2.0.1.0.8.b.d.0.1.0.0.2.ip6.arpa'
			),
			array
			(
				'ip_get_arpa',
				"\x20\x01\x0D\xB8\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF",
				'f.f.f.f.f.f.f.f.f.f.f.f.f.f.f.f.f.f.f.f.f.f.f.f.8.b.d.0.1.0.0.2.ip6.arpa'
			),

			array ('withoutLocationTypes', array(), array()),
			array ('withoutLocationTypes', array (1560 => 'rack', 1561 => 'row', 1562 => 'location'), array()),
			array ('withoutLocationTypes', $ten_kilo, $ten_kilo_less_3),

			// implicit 2nd and 3rd arguments
			array ('reindexById', array(), array()),
			array
			(
				'reindexById',
				array
				(
					array ('id' => 1, 'name' => 'one'),
					array ('id' => 2, 'name' => 'two'),
					array ('id' => 3, 'name' => 'three'),
					array ('id' => 4, 'name' => 'four'),
				),
				array
				(
					1 => array ('id' => 1, 'name' => 'one'),
					2 => array ('id' => 2, 'name' => 'two'),
					3 => array ('id' => 3, 'name' => 'three'),
					4 => array ('id' => 4, 'name' => 'four'),
				),
			),
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

			array ('buildVLANFilter', 'access', '', array (array ('from' => 1, 'to' => 4094))),
			array ('buildVLANFilter', 'access', '100-200', array (array ('from' => 100, 'to' => 200))),
			array ('buildVLANFilter', 'access', '0-200', array (array ('from' => 1, 'to' => 200))),
			array ('buildVLANFilter', 'access', '100-20000', array (array ('from' => 100, 'to' => 4094))),
			array ('buildVLANFilter', 'access', '0-20000', array (array ('from' => 1, 'to' => 4094))),
			array ('buildVLANFilter', 'trunk', '', array (array ('from' => 2, 'to' => 4094))),
			array ('buildVLANFilter', 'trunk', '100-200', array (array ('from' => 100, 'to' => 200))),
			array ('buildVLANFilter', 'trunk', '0-200', array (array ('from' => 2, 'to' => 200))),
			array ('buildVLANFilter', 'trunk', '100-20000', array (array ('from' => 100, 'to' => 4094))),
			array ('buildVLANFilter', 'trunk', '0-20000', array (array ('from' => 2, 'to' => 4094))),
			array ('buildVLANFilter', 'uplink', '', array (array ('from' => 2, 'to' => 4094))),
			array ('buildVLANFilter', 'uplink', '100-200', array (array ('from' => 100, 'to' => 200))),
			array ('buildVLANFilter', 'uplink', '0-200', array (array ('from' => 2, 'to' => 200))),
			array ('buildVLANFilter', 'uplink', '100-20000', array (array ('from' => 100, 'to' => 4094))),
			array ('buildVLANFilter', 'uplink', '0-20000', array (array ('from' => 2, 'to' => 4094))),
			array ('buildVLANFilter', 'downlink', '', array (array ('from' => 2, 'to' => 4094))),
			array ('buildVLANFilter', 'downlink', '100-200', array (array ('from' => 100, 'to' => 200))),
			array ('buildVLANFilter', 'downlink', '0-200', array (array ('from' => 2, 'to' => 200))),
			array ('buildVLANFilter', 'downlink', '100-20000', array (array ('from' => 100, 'to' => 4094))),
			array ('buildVLANFilter', 'downlink', '0-20000', array (array ('from' => 2, 'to' => 4094))),
			array ('buildVLANFilter', 'anymode', '', array (array ('from' => 2, 'to' => 4094))),
			array ('buildVLANFilter', 'anymode', '100-200', array (array ('from' => 100, 'to' => 200))),
			array ('buildVLANFilter', 'anymode', '0-200', array (array ('from' => 2, 'to' => 200))),
			array ('buildVLANFilter', 'anymode', '100-20000', array (array ('from' => 100, 'to' => 4094))),
			array ('buildVLANFilter', 'anymode', '0-20000', array (array ('from' => 2, 'to' => 4094))),
			array ('buildVLANFilter', 'none', '', array ()),
			array ('buildVLANFilter', 'none', '100-200', array ()),
			array ('buildVLANFilter', 'none', '0-200', array ()),
			array ('buildVLANFilter', 'none', '100-20000', array ()),
			array ('buildVLANFilter', 'none', '0-20000', array ()),
			array ('buildVLANFilter', 'abcde', '', array ()), // this is a bug, ought to be InvalidArgException

			array ('matchVLANFilter', 5, array (array ('from' => 10, 'to' => 20), array ('from' => 30, 'to' => 40), array ('from' => 45, 'to' => 45)), FALSE),
			array ('matchVLANFilter', 10, array (array ('from' => 10, 'to' => 20), array ('from' => 30, 'to' => 40), array ('from' => 45, 'to' => 45)), TRUE),
			array ('matchVLANFilter', 20, array (array ('from' => 10, 'to' => 20), array ('from' => 30, 'to' => 40), array ('from' => 45, 'to' => 45)), TRUE),
			array ('matchVLANFilter', 25, array (array ('from' => 10, 'to' => 20), array ('from' => 30, 'to' => 40), array ('from' => 45, 'to' => 45)), FALSE),
			array ('matchVLANFilter', 35, array (array ('from' => 10, 'to' => 20), array ('from' => 30, 'to' => 40), array ('from' => 45, 'to' => 45)), TRUE),
			array ('matchVLANFilter', 41, array (array ('from' => 10, 'to' => 20), array ('from' => 30, 'to' => 40), array ('from' => 45, 'to' => 45)), FALSE),
			array ('matchVLANFilter', 45, array (array ('from' => 10, 'to' => 20), array ('from' => 30, 'to' => 40), array ('from' => 45, 'to' => 45)), TRUE),

			array ('filterVLANList', array (5, 6, 7, 8, 16, 20, 21), array (array ('from' => 7, 'to' => 7), array ('from' => 10, 'to' => 20)), array (7, 16, 20)),
			array ('filterVLANList', array (8, 30), array (array ('from' => 7, 'to' => 7), array ('from' => 10, 'to' => 20)), array()),
			array ('filterVLANList', array (14, 19), array (array ('from' => 7, 'to' => 7), array ('from' => 10, 'to' => 20)), array (14, 19)),

			// explicit 2nd argument
			array ('listToRanges', array(), 0, array()),
			array ('listToRanges', array (7), 0, array (array ('from' => 7, 'to' => 7))),
			array ('listToRanges', array (2, 4, 3, 5, 1), 0, array (array ('from' => 1, 'to' => 5))),
			array ('listToRanges', array (12, 24, 23, 25, 11), 0, array (array ('from' => 11, 'to' => 12), array ('from' => 23, 'to' => 25))),
			array ('listToRanges', array (22, 24, 23, 25, 11), 0, array (array ('from' => 11, 'to' => 11), array ('from' => 22, 'to' => 25))),

			array ('listToRanges', array (2, 3, 1), 1, array (array ('from' => 1, 'to' => 1), array ('from' => 2, 'to' => 2), array ('from' => 3, 'to' => 3))),
			array ('listToRanges', array (10, 11, 12), 4, array (array ('from' => 10, 'to' => 12))),
			array ('listToRanges', array (10, 11, 12, 13, 14, 15, 16, 17), 4, array (array ('from' => 10, 'to' => 13), array ('from' => 14, 'to' => 17))),

			array ('mergeTagChains', array(), array(), array()),
			array
			(
				'mergeTagChains',
				array (array ('id' => 1, 'tag' => 'one')),
				array (array ('id' => 10, 'tag' => 'ten')),
				array (1 => array ('id' => 1, 'tag' => 'one'), 10 => array ('id' => 10, 'tag' => 'ten')),
			),
			array
			(
				'mergeTagChains',
				array(),
				array (array ('id' => 10, 'tag' => 'ten')),
				array (10 => array ('id' => 10, 'tag' => 'ten')),
			),
			array
			(
				'mergeTagChains',
				array (array ('id' => 1, 'tag' => 'one')),
				array(),
				array (1 => array ('id' => 1, 'tag' => 'one')),
			),
			array
			(
				'mergeTagChains',
				array (array ('id' => 1, 'tag' => 'one'), array ('id' => 2, 'tag' => 'two')),
				array (array ('id' => 10, 'tag' => 'ten'), array ('id' => 2, 'tag' => 'two')),
				array (1 => array ('id' => 1, 'tag' => 'one'), 2 => array ('id' => 2, 'tag' => 'two'), 10 => array ('id' => 10, 'tag' => 'ten')),
			),

			// explicit 2nd argument
			array ('validTagName', 'tag', FALSE, TRUE),
			array ('validTagName', 'another tag', FALSE, TRUE),
			array ('validTagName', 'another-tag', FALSE, TRUE),
			array ('validTagName', 'another_tag', FALSE, TRUE),
			array ('validTagName', 'abc 123 def', FALSE, TRUE),
			array ('validTagName', '100%', FALSE, TRUE),
			array ('validTagName', 'racks 20~39', FALSE, TRUE),
			array ('validTagName', 'racks 40+', FALSE, TRUE),
			array ('validTagName', 'racks 10:', FALSE, TRUE),
			array ('validTagName', '2nd tag', FALSE, TRUE),
			array ('validTagName', 'left+right', FALSE, TRUE),
			array ('validTagName', '2015', FALSE, TRUE),
			array ('validTagName', '2015-2016', FALSE, TRUE),
			array ('validTagName', '-2016', FALSE, TRUE),
			array ('validTagName', '-50%', FALSE, TRUE),
			array ('validTagName', 'size: XL', FALSE, TRUE),
			array ('validTagName', 'size: XL+', FALSE, TRUE),
			array ('validTagName', 'iqn.domain', FALSE, TRUE),
			array ('validTagName', '', FALSE, FALSE),
			array ('validTagName', ' tag ', FALSE, FALSE),
			array ('validTagName', 'tag?', FALSE, FALSE),
			array ('validTagName', 'tag!', FALSE, FALSE),
			array ('validTagName', 'tag_', FALSE, FALSE),
			array ('validTagName', 'tag~', FALSE, FALSE),
			array ('validTagName', 'tag.', FALSE, FALSE),
			array ('validTagName', '$tag', FALSE, FALSE),
			array ('validTagName', '2015-', FALSE, FALSE),
			array ('validTagName', 'iqn.domain.', FALSE, FALSE),

			array ('validTagName', '$tag', TRUE, TRUE),
			array ('validTagName', '$another tag', TRUE, TRUE),
			array ('validTagName', '$another tag', TRUE, TRUE),
			array ('validTagName', '$another-tag', TRUE, TRUE),
			array ('validTagName', '$another_tag', TRUE, TRUE),
			array ('validTagName', '$abc 123 def', TRUE, TRUE),
			array ('validTagName', '$100%', TRUE, TRUE),
			array ('validTagName', '$racks 20~39', TRUE, TRUE),
			array ('validTagName', '$racks 40+', TRUE, TRUE),
			array ('validTagName', '$racks 10:', TRUE, TRUE),
			array ('validTagName', '$2nd tag', TRUE, TRUE),
			array ('validTagName', '$left+right', TRUE, TRUE),
			array ('validTagName', '$2015', TRUE, TRUE),
			array ('validTagName', '$2015-2016', TRUE, TRUE),
			array ('validTagName', '$-2016', TRUE, TRUE),
			array ('validTagName', '$-50%', TRUE, TRUE),
			array ('validTagName', '$size: XL', TRUE, TRUE),
			array ('validTagName', '$size: XL+', TRUE, TRUE),
			array ('validTagName', '$iqn.domain', TRUE, TRUE),
			array ('validTagName', '', TRUE, FALSE),
			array ('validTagName', '$ tag ', TRUE, FALSE),
			array ('validTagName', '$tag?', TRUE, FALSE),
			array ('validTagName', '$tag!', TRUE, FALSE),
			array ('validTagName', '$tag_', TRUE, FALSE),
			array ('validTagName', '$tag~', TRUE, FALSE),
			array ('validTagName', '$tag.', TRUE, FALSE),
			array ('validTagName', 'tag', TRUE, TRUE), // not quite expected but as far as the function goes is correct
			array ('validTagName', '$2015-', TRUE, FALSE),
			array ('validTagName', '$iqn.domain.', TRUE, FALSE),

			array ('goodModeForVSTRole', 'access', 'access', TRUE),
			array ('goodModeForVSTRole', 'access', 'trunk', FALSE),
			array ('goodModeForVSTRole', 'access', 'anymode', TRUE),
			array ('goodModeForVSTRole', 'access', 'uplink', FALSE),
			array ('goodModeForVSTRole', 'access', 'downlink', FALSE),
			array ('goodModeForVSTRole', 'trunk', 'access', FALSE),
			array ('goodModeForVSTRole', 'trunk', 'trunk', TRUE),
			array ('goodModeForVSTRole', 'trunk', 'anymode', TRUE),
			array ('goodModeForVSTRole', 'trunk', 'uplink', TRUE),
			array ('goodModeForVSTRole', 'trunk', 'downlink', TRUE),
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

			// implicit 3rd argument
			array
			(
				'reindexById',
				array
				(
					array ('id' => 1, 'name' => 'one'),
					array ('id' => 2, 'name' => 'two'),
					array ('id' => 3, 'name' => 'three'),
					array ('id' => 4, 'name' => 'four'),
				),
				'id',
				array
				(
					1 => array ('id' => 1, 'name' => 'one'),
					2 => array ('id' => 2, 'name' => 'two'),
					3 => array ('id' => 3, 'name' => 'three'),
					4 => array ('id' => 4, 'name' => 'four'),
				),
			),
			array
			(
				'reindexById',
				array
				(
					array ('id' => 1, 'name' => 'one'),
					array ('id' => 2, 'name' => 'two'),
					array ('id' => 3, 'name' => 'three'),
					array ('id' => 4, 'name' => 'four'),
				),
				'name',
				array
				(
					'one' => array ('id' => 1, 'name' => 'one'),
					'two' => array ('id' => 2, 'name' => 'two'),
					'three' => array ('id' => 3, 'name' => 'three'),
					'four' => array ('id' => 4, 'name' => 'four'),
				),
			),

			array
			(
				'reduceSubarraysToColumn',
				array
				(
					10 => array ('id' => 1, 'name' => 'one'),
					20 => array ('id' => 2, 'name' => 'two'),
					30 => array ('id' => 3, 'name' => 'three'),
					40 => array ('id' => 4, 'name' => 'four'),
				),
				'id',
				array (10 => 1, 20 => 2, 30 => 3, 40 => 4),
			),
			array
			(
				'reduceSubarraysToColumn',
				array
				(
					'a' => array ('id' => 1, 'name' => 'one'),
					'b' => array ('id' => 2, 'name' => 'two'),
					'c' => array ('id' => 3, 'name' => 'three'),
					'd' => array ('id' => 4, 'name' => 'four'),
				),
				'name',
				array ('a' => 'one', 'b' => 'two', 'c' => 'three', 'd' => 'four'),
			),
			array
			(
				'reduceSubarraysToColumn',
				array
				(
					array ('id' => 1, 'name' => 'number'),
					array ('id' => 2, 'name' => 'number'),
					array ('id' => 3, 'name' => 'number'),
					array ('id' => 4, 'name' => 'number'),
				),
				'name',
				array ('number', 'number', 'number', 'number'),
			),

			array
			(
				'groupBy',
				array(),
				'test',
				array(),
			),
			array
			(
				'groupBy',
				array
				(
					1 => array ('value' => 'low', 'parity' => 'odd'),
					2 => array ('value' => 'low', 'parity' => 'even'),
					301 => array ('value' => 'high', 'parity' => 'odd'),
					302 => array ('value' => 'high', 'parity' => 'even'),
				),
				'parity',
				array
				(
					'odd' => array
					(
						1 => array ('value' => 'low', 'parity' => 'odd'),
						301 => array ('value' => 'high', 'parity' => 'odd'),
					),
					'even' => array
					(
						2 => array ('value' => 'low', 'parity' => 'even'),
						302 => array ('value' => 'high', 'parity' => 'even'),
					),
				),
			),
			array
			(
				'groupBy',
				array
				(
					1 => array ('value' => 'low', 'parity' => 'odd'),
					2 => array ('value' => 'low', 'parity' => 'even'),
					301 => array ('value' => 'high', 'parity' => 'odd'),
					302 => array ('value' => 'high', 'parity' => 'even'),
				),
				'value',
				array
				(
					'low' => array
					(
						1 => array ('value' => 'low', 'parity' => 'odd'),
						2 => array ('value' => 'low', 'parity' => 'even'),
					),
					'high' => array
					(
						301 => array ('value' => 'high', 'parity' => 'odd'),
						302 => array ('value' => 'high', 'parity' => 'even'),
					),
				),
			),
			array
			(
				'groupBy',
				array
				(
					1 => array ('value' => 'low', 'parity' => 'odd'),
					2 => array ('value' => 'low', 'parity' => 'even'),
					301 => array ('value' => 'high', 'parity' => 'odd'),
					302 => array ('value' => 'high', 'parity' => 'even'),
				),
				'unknown',
				array
				(
					'' => array
					(
						1 => array ('value' => 'low', 'parity' => 'odd'),
						2 => array ('value' => 'low', 'parity' => 'even'),
						301 => array ('value' => 'high', 'parity' => 'odd'),
						302 => array ('value' => 'high', 'parity' => 'even'),
					),
				),
			),
			array
			(
				'groupBy',
				array
				(
					1 => array ('value' => 'low', 'parity' => 'odd'),
					2 => array ('value' => 'low', 'parity' => 'even'),
					150 => array ('value' => 'medium'),
					160 => array ('parity' => 'even'),
					170 => array(),
					301 => array ('value' => 'high', 'parity' => 'odd'),
					302 => array ('value' => 'high', 'parity' => 'even'),
				),
				'parity',
				array
				(
					'odd' => array
					(
						1 => array ('value' => 'low', 'parity' => 'odd'),
						301 => array ('value' => 'high', 'parity' => 'odd'),
					),
					'even' => array
					(
						2 => array ('value' => 'low', 'parity' => 'even'),
						160 => array ('parity' => 'even'),
						302 => array ('value' => 'high', 'parity' => 'even'),
					),
					'' => array
					(
						150 => array ('value' => 'medium'),
						170 => array(),
					),
				),
			),
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
		$a = array
		(
			array ('id' => 10, 'name' => 'ten'), // 0
			array ('id' => 20, 'name' => 'twenty'), // 1
			array ('id' => 30, 'name' => 'thirty'), // 2
		);
		return array
		(
			array ('scanArrayForItem', $a, 'id', 10, 0),
			array ('scanArrayForItem', $a, 'id', 30, 2),
			array ('scanArrayForItem', $a, 'name', 'twenty', 1),
			array ('scanArrayForItem', $a, 'id', 40, NULL),

			// all arguments
			array
			(
				'reindexById',
				array
				(
					array ('id' => 1, 'name' => 'one'),
					array ('id' => 2, 'name' => 'two'),
					array ('id' => 3, 'name' => 'three'),
					array ('id' => 4, 'name' => 'four'),
				),
				'name',
				FALSE,
				array
				(
					'one' => array ('id' => 1, 'name' => 'one'),
					'two' => array ('id' => 2, 'name' => 'two'),
					'three' => array ('id' => 3, 'name' => 'three'),
					'four' => array ('id' => 4, 'name' => 'four'),
				),
			),
			array
			(
				'reindexById',
				array
				(
					array ('id' => 1, 'parity' => 'odd'),
					array ('id' => 2, 'parity' => 'even'),
					array ('id' => 3, 'parity' => 'odd'),
					array ('id' => 4, 'parity' => 'even'),
				),
				'parity',
				TRUE,
				array
				(
					'odd' => array ('id' => 1, 'parity' => 'odd'),
					'even' => array ('id' => 2, 'parity' => 'even'),
				),
			),
		);
	}

	public function providerNaryIAE ()
	{
		return array
		(
			array ('questionMarks', array (0)),

			array ('array_values_same', array ('', array (1, 2, 3))),
			array ('array_values_same', array (0, array ())),
			array ('array_values_same', array (array (1), '1')),
			array ('array_values_same', array (NULL, NULL)),

			array ('iosParseVLANString', array ('')),
			array ('iosParseVLANString', array ('x')),
			array ('iosParseVLANString', array ('1,x,3')),
			array ('iosParseVLANString', array ('1-3,x,10')),

			array ('ip4_mask', array (-1)),
			array ('ip4_mask', array (33)),

			array ('ip6_mask', array (-1)),
			array ('ip6_mask', array (129)),

			array ('l2addressForDatabase', array ('010203abcd')), // invalid length
			array ('l2addressForDatabase', array ('010203abcdefff')), // invalid length
			array ('l2addressForDatabase', array ('010203abcdeh')), // length OK but not hexadecimal
			array ('l2addressForDatabase', array ('0102:03ab:cdef')), // not a known format
			array ('l2addressForDatabase', array ('01.02.03.ab.cd.ef')), // not a known format
			array ('l2addressForDatabase', array (' 1. 2. 3.ab.cd.ef')), // not a known format
			array ('l2addressForDatabase', array ('01.02.03-ab-cd:ef')), // not a known format

			array ('nextMACAddress', array ('010203abcdef')),
			array ('nextMACAddress', array ('0102.03ab.cdef')),
			array ('nextMACAddress', array ('01-02-03-ab-cd-ef')),
			array ('nextMACAddress', array ('abcd')),
			array ('nextMACAddress', array ('01:02:03:ab:cd:gg')),
			array ('nextMACAddress', array ('01:02:03:ab:cd')),
			array ('nextMACAddress', array ('1:2:3:ab:cd:ef')),

			array ('makeWhereSQL', array (array ('abc' => NULL), 'NOT')),
			array ('makeWhereSQL', array (array(), 'AND')),

			array ('ip_get_arpa', array ("\xAC\x11\xBB")),
			array ('ip_get_arpa', array ("\xAC\x11\xBB\x00\x00")),

			array ('goodModeForVSTRole', array (NULL, NULL)),
			array ('goodModeForVSTRole', array (FALSE, NULL)),
			array ('goodModeForVSTRole', array ('', NULL)),
			array ('goodModeForVSTRole', array ('unknown', NULL)),

			array ('makeSetSQL', array (array())),

			// not an array
			array ('reindexById', array (NULL)),
			array ('reindexById', array (FALSE)),
			array ('reindexById', array ('')),
			array ('reindexById', array (0)),
			// no such key, implicit 2nd and 3rd arguments
			array ('reindexById', array (array (array ('id' => 1, 'name' => 'one'), array ('id' => 2, 'name' => 'two'), array ('name' => 'three')))),
			// no such key, implicit 3rd argument
			array ('reindexById', array (array (array ('id' => 1, 'name' => 'one'), array ('id' => 2, 'name' => 'two'), array ('name' => 'three')), 'key')),
			// duplicate key, implicit 3rd argument
			array ('reindexById', array (array (array ('id' => 1, 'name' => 'one'), array ('id' => 1, 'name' => 'two'), array ('name' => 'three')), 'id')),
			// duplicate key, all arguments
			array ('reindexById', array (array (array ('id' => 1, 'name' => 'one'), array ('id' => 1, 'name' => 'two'), array ('name' => 'three')), 'id', FALSE)),

			array ('reduceSubarraysToColumn', array (NULL, 'name')),
			array ('reduceSubarraysToColumn', array (FALSE, 'name')),
			array ('reduceSubarraysToColumn', array ('', 'name')),
			array ('reduceSubarraysToColumn', array (0, 'name')),
			array ('reduceSubarraysToColumn', array (array (array ('id' => 1, 'name' => 'one'), array ('id' => 2)), 'name')),

			array ('groupBy', array (NULL, 'test')),
			array ('groupBy', array (FALSE, 'test')),
			array ('groupBy', array ('', 'test')),
			array ('groupBy', array (0, 'test')),
			array ('groupBy', array (array (array ('id' => 1, 'name' => 'one'), 2), 'name')),

			array ('parseSearchTerms', array ('one two three"')),
			array ('parseSearchTerms', array ('"one two three')),
			array ('parseSearchTerms', array ('one "two" "three')),
			array ('parseSearchTerms', array ('one "" three')),
			array ('parseSearchTerms', array ('""')),
		);
	}

	public function providerMakeWhereSQL ()
	{
		return array
		(
			array (array ('one' => 1), 'AND', 'one=?', array (1)),
			array (array ('one' => NULL), 'AND', 'one IS NULL', array()),
			array (array ('one' => 1, 'two' => 2), 'AND', 'one=? AND two=?', array (1, 2)),
			array (array ('one' => NULL, 'two' => 2), 'AND', 'one IS NULL AND two=?', array (2)),
			array (array ('one' => 1, 'two' => 2, 'three' => 3), 'OR', 'one=? OR two=? OR three=?', array (1, 2, 3)),
			array
			(
				array ('a' => array ('a.1', 'a.2', 'a.3'), 'b' => array (0, 10, 20, 30)),
				'OR',
				'a IN(?, ?, ?) OR b IN(?, ?, ?, ?)',
				array ('a.1', 'a.2', 'a.3', 0, 10, 20, 30)
			),
		);
	}

	public function providerTreeApplyFuncIAE ()
	{
		return array
		(
			array ('treeApplyFunc', array (NULL, NULL)),
			array ('treeApplyFunc', array ('no_such_function', NULL)),
			array ('treeApplyFunc', array ('count', 'no_such_function')),
		);
	}
}
?>
