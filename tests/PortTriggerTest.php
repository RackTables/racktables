<?php

// This class tests that a non-empty L2 address may be assigned to more than one
// port only so long as all such ports belong to the same object.

class PortTriggerTest extends RTTestCase
{
	public function setUp ()
	{
		$this->object1_id = commitAddObject ('unit test object 1', NULL, 1, NULL);
		$this->object2_id = commitAddObject ('unit test object 2', NULL, 1, NULL);
		$this->port1_1_id = commitAddPort ($this->object1_id, 'port 1', '1-24', 'label 1', 'aabbccddee01');
		$this->port1_2_id = commitAddPort ($this->object1_id, 'port 2', '1-24', 'label 2', 'aabbccddee02');
		$this->port1_3_id = commitAddPort ($this->object1_id, 'port 8', '1-24', 'label 8', 'aabbccddee000008'); // WWN
		$this->port1_4_id = commitAddPort ($this->object1_id, 'port 5', '1-24', 'label 5', 'aabbccddee000000000000000000000000000005'); // IPoIB
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
		$this->assertGreaterThan (0, commitAddPort ($this->object1_id, 'port 3', '1-24', 'label 3', 'aabbccddee01'));
		$this->assertGreaterThan (0, commitAddPort ($this->object1_id, 'port 9', '1-24', 'label 9', 'aabbccddee000008'));
		$this->assertGreaterThan (0, commitAddPort ($this->object1_id, 'port 4', '1-24', 'label 4', 'aabbccddee000000000000000000000000000005'));
	}

	/**
	 * @group small
	 */
	public function testUniqueMacUpdateSame()
	{
		$this->assertNull (commitUpdatePort ($this->object1_id, $this->port1_2_id, 'port 2', '1-24', 'label 1', 'aabbccddee01', ''));
		$this->assertNull (commitUpdatePort ($this->object1_id, $this->port1_2_id, 'port 2', '1-24', 'label 1', 'aabbccddee000008', ''));
		$this->assertNull (commitUpdatePort ($this->object1_id, $this->port1_2_id, 'port 2', '1-24', 'label 1', 'aabbccddee000000000000000000000000000005', ''));
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
	public function testUniqueWWNAdd()
	{
		$port2_2_id = commitAddPort ($this->object2_id, 'port 2', '1-24', 'label 1', 'aabbccddee000008');
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testUniqueIPoIBAdd()
	{
		$port2_2_id = commitAddPort ($this->object2_id, 'port 2', '1-24', 'label 1', 'aabbccddee000000000000000000000000000005');
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testUniqueMacUpdate()
	{
		commitUpdatePort ($this->object2_id, $this->port2_1_id, 'port 1', '1-24', 'label 1', 'aabbccddee01', '');
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testUniqueWWNUpdate()
	{
		commitUpdatePort ($this->object2_id, $this->port2_1_id, 'port 1', '1-24', 'label 1', 'aabbccddee000008', '');
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testUniqueIPoIBUpdate()
	{
		commitUpdatePort ($this->object2_id, $this->port2_1_id, 'port 1', '1-24', 'label 1', 'aabbccddee000000000000000000000000000005', '');
	}
}
