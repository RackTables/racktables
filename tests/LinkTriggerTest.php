<?php

// Test the effectiveness of the INSERT and UPDATE triggers on the Link table
//   - porta != portb
//   - porta < portb
//   - porta is compatibile with portb
class LinkTriggerTest extends PHPUnit_Framework_TestCase
{
	protected $autoports_config_var = NULL;
	protected $object_id = NULL;
	protected $porta = NULL;
	protected $portb = NULL;
	protected $portc = NULL;
	protected $portc_type = NULL;

	public function setUp ()
	{
		// make sure AUTOPORTS_CONFIG is empty
		$this->autoports_config_var = getConfigVar ('AUTOPORTS_CONFIG'); 
		if ($this->autoports_config_var != '')
			setConfigVar ('AUTOPORTS_CONFIG', '');

		// find a port type that is incompatible with 1000Base-T
		$result = usePreparedSelectBlade
		(
			'SELECT type1 FROM PortCompat WHERE type1 != 24 AND type2 != 24 LIMIT 1'
		);
		$this->portc_type = $result->fetchColumn ();

		// add sample data
		//   - set port a & b's type to 1000Base-T
		//   - set port c's type to the incompatible one
		$this->object_id = commitAddObject ('unit test object', NULL, 4, NULL);
		$this->porta = commitAddPort ($this->object_id, 'test porta', '1-24', NULL, NULL);
		$this->portb = commitAddPort ($this->object_id, 'test portb', '1-24', NULL, NULL);
		$this->portc = commitAddPort ($this->object_id, 'test portc', $this->portc_type, NULL, NULL);
	}

	public function tearDown ()
	{
		// restore AUTOPORTS_CONFIG to original setting
		if ($this->autoports_config_var != '')
			setConfigVar ('AUTOPORTS_CONFIG', $this->autoports_config_var);

		// remove sample data
		commitDeleteObject ($this->object_id);
	}

	/**
	 * @expectedException PDOException
	 */
	public function testCreateLinkToSelf ()
	{
		usePreparedInsertBlade (
			'Link',
			array ('porta' => $this->porta, 'portb' => $this->porta)
		);
	}

	/**
	 * @expectedException PDOException
	 */
	public function testUpdateLinkToSelf ()
	{
		usePreparedInsertBlade (
			'Link',
			array ('porta' => $this->porta, 'portb' => $this->portb)
		);
		usePreparedUpdateBlade (
			'Link',
			array ('porta' => $this->porta, 'portb' => $this->porta),
			array ('porta' => $this->porta, 'portb' => $this->portb)
		);
	}

	public function testCreateLinkWithPortAGreaterThanPortB ()
	{
		usePreparedInsertBlade (
			'Link',
			array ('porta' => $this->portb, 'portb' => $this->porta)
		);
		$result = usePreparedSelectBlade
		(
			'SELECT COUNT(*) FROM Link WHERE porta=? AND portb=?',
			array ($this->porta, $this->portb)
		);
		$this->assertEquals ($result->fetchColumn (), 1);
	}

	public function testUpdateLinkWithPortAGreaterThanPortB ()
	{
		usePreparedInsertBlade (
			'Link',
			array ('porta' => $this->porta, 'portb' => $this->portb)
		);
		usePreparedUpdateBlade (
			'Link',
			array ('porta' => $this->portb, 'portb' => $this->porta),
			array ('porta' => $this->porta, 'portb' => $this->portb)
		);
		$result = usePreparedSelectBlade
		(
			'SELECT COUNT(*) FROM Link WHERE porta=? AND portb=?',
			array ($this->porta, $this->portb)
		);
		$this->assertEquals ($result->fetchColumn (), 1);
	}

	/**
	 * @expectedException PDOException
	 */
	public function testCreateLinkBetweenIncompatiblePorts ()
	{
		usePreparedInsertBlade (
			'Link',
			array ('porta' => $this->porta, 'portb' => $this->portc)
		);
	}

	/**
	 * @expectedException PDOException
	 */
	public function testUpdateLinkBetweenIncompatiblePorts ()
	{
		usePreparedInsertBlade (
			'Link',
			array ('porta' => $this->porta, 'portb' => $this->portb)
		);
		usePreparedUpdateBlade (
			'Link',
			array ('porta' => $this->porta, 'portb' => $this->portc),
			array ('porta' => $this->porta, 'portb' => $this->portb)
		);
	}
}
?>
