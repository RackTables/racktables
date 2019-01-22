<?php

// Test the effectiveness of the INSERT and UPDATE triggers on the Link table
//   - porta != portb
//   - porta < portb
//   - porta is compatibile with portb
class LinkTriggerTest extends RTTestCase
{
	protected static $autoports_config_var;
	protected static $object_id;
	protected static $porta;
	protected static $portb;
	protected static $portc;
	protected static $portc_type;

	public static function setUpBeforeClass ()
	{
		// make sure AUTOPORTS_CONFIG is empty
		self::$autoports_config_var = getConfigVar ('AUTOPORTS_CONFIG');
		if (self::$autoports_config_var != '')
			setConfigVar ('AUTOPORTS_CONFIG', '');

		// find a port type that is incompatible with 1000Base-T
		$result = usePreparedSelectBlade
		(
			'SELECT type1 FROM PortCompat WHERE type1 != 24 AND type2 != 24 LIMIT 1'
		);
		self::$portc_type = $result->fetchColumn ();

		// add sample data
		//   - set port a & b's type to 1000Base-T
		//   - set port c's type to the incompatible one
		self::$object_id = commitAddObject (self::myStringStatic ('object', __CLASS__), NULL, 4, NULL);
		self::$porta = commitAddPort (self::$object_id, 'test porta', '1-24', NULL, NULL);
		self::$portb = commitAddPort (self::$object_id, 'test portb', '1-24', NULL, NULL);
		self::$portc = commitAddPort (self::$object_id, 'test portc', self::$portc_type, NULL, NULL);
	}

	public static function tearDownAfterClass ()
	{
		// restore AUTOPORTS_CONFIG to original setting
		if (self::$autoports_config_var != '')
			setConfigVar ('AUTOPORTS_CONFIG', self::$autoports_config_var);

		// remove sample data
		commitDeleteObject (self::$object_id);
	}

	public function tearDown ()
	{
		// delete any links created during the test
		usePreparedExecuteBlade
		(
			'DELETE FROM Link WHERE porta IN (?,?,?) OR portb IN (?,?,?)',
			array (self::$porta, self::$portb, self::$portc, self::$porta, self::$portb, self::$portc)
		);
	}

	/**
	 * @group small
	 * @expectedException PDOException
	 */
	public function testCreateLinkToSelf ()
	{
		usePreparedInsertBlade
		(
			'Link',
			array ('porta' => self::$porta, 'portb' => self::$porta)
		);
	}

	/**
	 * @group small
	 * @expectedException PDOException
	 */
	public function testUpdateLinkToSelf ()
	{
		usePreparedInsertBlade
		(
			'Link',
			array ('porta' => self::$porta, 'portb' => self::$portb)
		);
		usePreparedUpdateBlade
		(
			'Link',
			array ('porta' => self::$porta, 'portb' => self::$porta),
			array ('porta' => self::$porta, 'portb' => self::$portb)
		);
	}

	/**
	 * @group small
	 */
	public function testCreateLinkWithPortAGreaterThanPortB ()
	{
		usePreparedInsertBlade
		(
			'Link',
			array ('porta' => self::$portb, 'portb' => self::$porta)
		);
		$result = usePreparedSelectBlade
		(
			'SELECT COUNT(*) FROM Link WHERE porta=? AND portb=?',
			array (self::$porta, self::$portb)
		);
		$this->assertEquals (1, $result->fetchColumn ());
	}

	/**
	 * @group small
	 */
	public function testUpdateLinkWithPortAGreaterThanPortB ()
	{
		usePreparedInsertBlade
		(
			'Link',
			array ('porta' => self::$porta, 'portb' => self::$portb)
		);
		usePreparedUpdateBlade
		(
			'Link',
			array ('porta' => self::$portb, 'portb' => self::$porta),
			array ('porta' => self::$porta, 'portb' => self::$portb)
		);
		$result = usePreparedSelectBlade
		(
			'SELECT COUNT(*) FROM Link WHERE porta=? AND portb=?',
			array (self::$porta, self::$portb)
		);
		$this->assertEquals (1, $result->fetchColumn ());
	}

	/**
	 * @group small
	 * @expectedException PDOException
	 */
	public function testCreateLinkBetweenIncompatiblePorts ()
	{
		usePreparedInsertBlade
		(
			'Link',
			array ('porta' => self::$porta, 'portb' => self::$portc)
		);
	}

	/**
	 * @group small
	 * @expectedException PDOException
	 */
	public function testUpdateLinkBetweenIncompatiblePorts ()
	{
		usePreparedInsertBlade
		(
			'Link',
			array ('porta' => self::$porta, 'portb' => self::$portb)
		);
		usePreparedUpdateBlade
		(
			'Link',
			array ('porta' => self::$porta, 'portb' => self::$portc),
			array ('porta' => self::$porta, 'portb' => self::$portb)
		);
	}
}
