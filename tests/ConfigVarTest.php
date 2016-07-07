<?php

class ConfigVarTest extends PHPUnit_Framework_TestCase
{
	private $varname;

	public function setUp ()
	{
		$this->varname = sprintf ('testvar-%s-%u', get_class(), getmypid());
		usePreparedInsertBlade
		(
			'Config',
			array
			(
				'varname' => $this->varname,
				'varvalue' => '0', // satisfies any constraints
				'vartype' => 'string',
				'emptyok' => 'yes',
				'is_hidden' => 'no',
				'is_userdefined' => 'no',
			)
		);
	}

	public function tearDown ()
	{
		usePreparedDeleteBlade ('Config', array ('varname' => $this->varname));
	}

	/**
	 * @group small
	 * @dataProvider providerNormal
	 */
	public function testSystemNormal ($varvalue, $columns)
	{
		global $configCache;
		$columns['is_userdefined'] = 'no';
		$columns['is_hidden'] = 'no';
		usePreparedUpdateBlade ('Config', $columns, array ('varname' => $this->varname));
		$configCache = loadConfigDefaults();
		setConfigVar ($this->varname, $varvalue);
		$this->assertSame ($varvalue, getConfigVar ($this->varname));
		setConfigVar ($this->varname, '0');
	}

	/**
	 * @group small
	 * @dataProvider providerIAE1
	 * @expectedException InvalidArgException
	 */
	public function testSystemIAE1 ($varvalue, $columns)
	{
		global $configCache;
		$columns['is_userdefined'] = 'no';
		usePreparedUpdateBlade ('Config', $columns, array ('varname' => $this->varname));
		$configCache = loadConfigDefaults();
		setConfigVar ($this->varname, $varvalue);
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testSystemIAE2 ()
	{
		setConfigVar ('x' . $this->varname, 'no such variable');
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testSystemIAE3 ()
	{
		getConfigVar ('x' . $this->varname);
	}

	/**
	 * @group small
	 * @dataProvider providerNormal
	 */
	public function testUserNormal ($varvalue, $columns)
	{
		global $configCache;
		$tmpvalue = '12345678';
		$columns['is_userdefined'] = 'yes';
		$columns['is_hidden'] = 'no';
		usePreparedUpdateBlade ('Config', $columns, array ('varname' => $this->varname));
		$configCache = loadConfigDefaults();
		alterConfigWithUserPreferences();

		setConfigVar ($this->varname, $tmpvalue);
		$this->assertSame ($tmpvalue, getConfigVar ($this->varname));
		setUserConfigVar ($this->varname, $varvalue);
		$this->assertSame ($varvalue, getConfigVar ($this->varname));
		setConfigVar ($this->varname, $tmpvalue);
		$this->assertSame ($varvalue, getConfigVar ($this->varname));
		resetUserConfigVar ($this->varname);
		$this->assertSame ($tmpvalue, getConfigVar ($this->varname));
	}

	/**
	 * @group small
	 * @dataProvider providerIAE1
	 * @expectedException InvalidArgException
	 */
	public function testUserIAE1 ($varvalue, $columns)
	{
		global $configCache;
		$columns['is_userdefined'] = 'yes';
		usePreparedUpdateBlade ('Config', $columns, array ('varname' => $this->varname));
		$configCache = loadConfigDefaults();
		setUserConfigVar ($this->varname, $varvalue);
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testUserIAE2 ()
	{
		setUserConfigVar ('x' . $this->varname, 'no such variable');
	}

	public function providerNormal ()
	{
		return array
		(
			array ('', array ('vartype' => 'string', 'emptyok' => 'yes')),
			array ('a string', array ('vartype' => 'string', 'emptyok' => 'yes')),
			array ('a string', array ('vartype' => 'string', 'emptyok' => 'no')),

			array ('', array ('vartype' => 'uint', 'emptyok' => 'yes')),
			array ('0', array ('vartype' => 'uint', 'emptyok' => 'yes')),
			array ('0', array ('vartype' => 'uint', 'emptyok' => 'no')),
			array ('100', array ('vartype' => 'uint', 'emptyok' => 'yes')),
			array ('100', array ('vartype' => 'uint', 'emptyok' => 'no')),
		);
	}

	public function providerIAE1 ()
	{
		return array
		(
			array ('abc', array ('vartype' => 'string', 'emptyok' => 'yes', 'is_hidden' => 'yes')),
			array ('', array ('vartype' => 'string', 'emptyok' => 'no', 'is_hidden' => 'no')),

			array ('', array ('vartype' => 'uint', 'emptyok' => 'no', 'is_hidden' => 'no')),
			array ('-1', array ('vartype' => 'uint', 'emptyok' => 'no', 'is_hidden' => 'no')),
			array ('-1', array ('vartype' => 'uint', 'emptyok' => 'yes', 'is_hidden' => 'no')),
			array ('def', array ('vartype' => 'uint', 'emptyok' => 'no', 'is_hidden' => 'no')),
			array ('def', array ('vartype' => 'uint', 'emptyok' => 'yes', 'is_hidden' => 'no')),
		);
	}
}

?>
