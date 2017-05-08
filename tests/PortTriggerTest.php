<?php

// We should prohibit reusing an l2address on different objects
class PortTriggerTest extends PHPUnit_Framework_TestCase
{
	public function setUp ()
	{
		$this->object1_id = commitAddObject ('unit test object 1', NULL, 1, NULL);
		$this->object2_id = commitAddObject ('unit test object 2', NULL, 1, NULL);
		$this->port1_1_id = commitAddPort ($this->object1_id, 'port 1', '1-24', 'label 1', 'aabbccddee01');
		$this->port1_2_id = commitAddPort ($this->object1_id, 'port 2', '1-24', 'label 2', 'aabbccddee02');
		$this->port2_1_id = commitAddPort ($this->object2_id, 'port 1', '1-24', 'label 1', 'aabbccddee03');
	}

	public function tearDown ()
	{
		commitDeleteObject ($this->object1_id);
		commitDeleteObject ($this->object2_id);
	}

	/**
	 * @group small
	 */
	public function testUniqueMacAddSame()
	{
		$port1_3_id = commitAddPort ($this->object1_id, 'port 3', '1-24', 'label 3', 'aabbccddee01');
	}

	/**
	 * @group small
	 */
	public function testUniqueMacUpdateSame()
	{
		commitUpdatePort ($this->object1_id, $this->port1_2_id, 'port 2', '1-24', 'label 1', 'aabbccddee01', '');
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testUniqueMacAdd()
	{
		$port2_2_id = commitAddPort ($this->object2_id, 'port 2', '1-24', 'label 1', 'aabbccddee01');
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testUniqueMacUpdate()
	{
		commitUpdatePort ($this->object2_id, $this->port2_1_id, 'port 1', '1-24', 'label 1', 'aabbccddee01', '');
	}
}
