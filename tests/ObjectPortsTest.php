<?php

class ObjectPorts extends PHPUnit_Framework_TestCase
{
	private $object_id;

	public function setUp ()
	{
		// Let it be a server as AutoPorts rows shall not get in the way of the test.
		$this->object_id = commitAddObject (sprintf ('testobject-%s-%u', get_class(), getmypid()), '', 4, '');
	}

	/**
	 * @group small
	 */
	public function testComment ()
	{
		$port_id = commitAddPort ($this->object_id, 'port 1', '1-24', '', '');

		$this->assertSame (NULL, getPortReservationComment ($port_id));
		commitUpdatePortComment ($port_id, 'test comment');
		$this->assertEquals ('test comment', getPortReservationComment ($port_id));
		commitUpdatePortComment ($port_id, ''); # empty string becomes NULL
		$this->assertSame (NULL, getPortReservationComment ($port_id));

		usePreparedDeleteBlade ('Port', array ('id' => $port_id));
	}

	/**
	 * @group small
	 * @dataProvider providerAddAndVerify
	 */
	public function testAddAndVerify ($ports)
	{
		$before = getPortsCount ($this->object_id);
		$added_ids = array();
		foreach ($ports as $port_name => $port_type_ids)
		{
			$this_ids = array();
			foreach ($port_type_ids as $port_type_id)
				$this_ids[] = commitAddPort ($this->object_id, $port_name, $port_type_id, '', '');
			$this->assertEquals ($this_ids, getPortIDs ($this->object_id, $port_name));
			$added_ids = array_merge ($added_ids, $this_ids);
		}
		$this->assertEquals ($before + count ($added_ids), getPortsCount ($this->object_id));
		foreach ($added_ids as $port_id)
			usePreparedDeleteBlade ('Port', array ('id' => $port_id));
		$this->assertEquals ($before, getPortsCount ($this->object_id));
	}

	public function providerAddAndVerify ()
	{
		return array
		(
			array
			(
				array
				(
					'port 1' => array ('1-24')
				),
			),
			array
			(
				array
				(
					'port 1' => array ('1-24'),
					'port 2' => array ('1-24'),
				),
			),
			array
			(
				array
				(
					'port 1' => array ('1-24'),
					'port 2' => array ('1-24'),
					'port 3' => array ('1-24', '3-1078', '4-1204'), // 1000Base-T, empty GBIC, SFP-1000/1000Base-LX
				),
			),
		);
	}

	public function tearDown ()
	{
		commitDeleteObject ($this->object_id);
	}
}

?>
