<?php

class ObjectPortsTest extends PHPUnit_Framework_TestCase
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

		$this->assertNull (getPortReservationComment ($port_id));
		commitUpdatePortComment ($port_id, 'test comment');
		$this->assertEquals ('test comment', getPortReservationComment ($port_id));
		commitUpdatePortComment ($port_id, ''); # empty string becomes NULL
		$this->assertNull (getPortReservationComment ($port_id));

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

	/**
	 * @group small
	 */
	public function testTwoPorts ()
	{
		$port1_id = commitAddPort ($this->object_id, 'port 1', '1-24', 'label 1', 'aabbccddee01'); // 1000Base-T
		commitUpdatePortComment ($port1_id, 'comment 1');
		$port2_id = commitAddPort ($this->object_id, 'port 2', '4-1077', 'label 2', 'aabbccddee02'); // SFP-1000/empty
		commitUpdatePortComment ($port2_id, 'comment 2');

		$ports = getObjectPortsAndLinks ($this->object_id);
		$key1 = scanArrayForItem ($ports, 'id', $port1_id);
		$key2 = scanArrayForItem ($ports, 'id', $port2_id);
		$this->assertEquals ('port 1', $ports[$key1]['name']);
		$this->assertEquals ('AA:BB:CC:DD:EE:01', $ports[$key1]['l2address']);
		$this->assertEquals ('label 1', $ports[$key1]['label']);
		$this->assertEquals ('comment 1', $ports[$key1]['reservation_comment']);
		$this->assertEquals (1, $ports[$key1]['iif_id']);
		$this->assertEquals (24, $ports[$key1]['oif_id']);
		$this->assertEquals (0, $ports[$key1]['linked']);
		$this->assertNull ($ports[$key1]['cableid']);
		$this->assertNull ($ports[$key1]['remote_object_id']);
		$this->assertNull ($ports[$key1]['remote_id']);
		$this->assertEquals ('port 2', $ports[$key2]['name']);
		$this->assertEquals ('AA:BB:CC:DD:EE:02', $ports[$key2]['l2address']);
		$this->assertEquals ('label 2', $ports[$key2]['label']);
		$this->assertEquals ('comment 2', $ports[$key2]['reservation_comment']);
		$this->assertEquals (4, $ports[$key2]['iif_id']);
		$this->assertEquals (1077, $ports[$key2]['oif_id']);
		$this->assertEquals (0, $ports[$key2]['linked']);
		$this->assertNull ($ports[$key2]['cableid']);
		$this->assertNull ($ports[$key2]['remote_object_id']);
		$this->assertNull ($ports[$key2]['remote_id']);

		commitUpdatePortOIF ($port1_id, 19); // 100Base-TX
		commitUpdatePortOIF ($port2_id, 24); // 1000Base-T
		linkPorts ($port1_id, $port2_id);

		$ports = getObjectPortsAndLinks ($this->object_id);
		$key1 = scanArrayForItem ($ports, 'id', $port1_id);
		$key2 = scanArrayForItem ($ports, 'id', $port2_id);
		// A side effect of linkPorts() is unsetting the reservation comment.
		$this->assertNull ($ports[$key1]['reservation_comment']);
		$this->assertNull ($ports[$key2]['reservation_comment']);
		$this->assertEquals (1, $ports[$key1]['iif_id']);
		$this->assertEquals (19, $ports[$key1]['oif_id']);
		$this->assertEquals (1, $ports[$key1]['linked']);
		$this->assertNull ($ports[$key2]['cableid']);
		$this->assertEquals ($this->object_id, $ports[$key1]['remote_object_id']);
		$this->assertEquals ($port2_id, $ports[$key1]['remote_id']);
		$this->assertEquals (4, $ports[$key2]['iif_id']);
		$this->assertEquals (24, $ports[$key2]['oif_id']);
		$this->assertEquals (1, $ports[$key2]['linked']);
		$this->assertNull ($ports[$key2]['cableid']);
		$this->assertEquals ($this->object_id, $ports[$key2]['remote_object_id']);
		$this->assertEquals ($port1_id, $ports[$key2]['remote_id']);

		commitUpdatePortLink ($port1_id, 'cable ID 12345');

		$ports = getObjectPortsAndLinks ($this->object_id);
		$key1 = scanArrayForItem ($ports, 'id', $port1_id);
		$key2 = scanArrayForItem ($ports, 'id', $port2_id);
		$this->assertEquals ('cable ID 12345', $ports[$key1]['cableid']);
		$this->assertEquals ('cable ID 12345', $ports[$key2]['cableid']);

		commitUpdatePortLink ($port1_id, '');

		$ports = getObjectPortsAndLinks ($this->object_id);
		$key1 = scanArrayForItem ($ports, 'id', $port1_id);
		$key2 = scanArrayForItem ($ports, 'id', $port2_id);
		$this->assertNull ($ports[$key1]['cableid']); // converted from ''
		$this->assertNull ($ports[$key2]['cableid']); // idem

		commitUnlinkPort ($port1_id);

		$ports = getObjectPortsAndLinks ($this->object_id);
		$key1 = scanArrayForItem ($ports, 'id', $port1_id);
		$key2 = scanArrayForItem ($ports, 'id', $port2_id);
		$this->assertEquals (0, $ports[$key1]['linked']);
		$this->assertNull ($ports[$key1]['remote_object_id']);
		$this->assertNull ($ports[$key1]['remote_id']);
		$this->assertEquals (0, $ports[$key2]['linked']);
		$this->assertNull ($ports[$key2]['remote_object_id']);
		$this->assertNull ($ports[$key2]['remote_id']);

		commitUpdatePort ($this->object_id, $port1_id, 'port one', '3-1202', 'label one', 'eeeeeeee0001', 'reserved one');
		commitUpdatePort ($this->object_id, $port2_id, 'port two', '4-1202', 'label two', 'eeeeeeee0002', 'reserved two');

		$ports = getObjectPortsAndLinks ($this->object_id);
		$key1 = scanArrayForItem ($ports, 'id', $port1_id);
		$key2 = scanArrayForItem ($ports, 'id', $port2_id);
		$this->assertEquals ('port one', $ports[$key1]['name']);
		$this->assertEquals ('EE:EE:EE:EE:00:01', $ports[$key1]['l2address']);
		$this->assertEquals ('label one', $ports[$key1]['label']);
		$this->assertEquals ('reserved one', $ports[$key1]['reservation_comment']);
		$this->assertEquals (3, $ports[$key1]['iif_id']);
		$this->assertEquals (1202, $ports[$key1]['oif_id']);
		$this->assertEquals ('port two', $ports[$key2]['name']);
		$this->assertEquals ('EE:EE:EE:EE:00:02', $ports[$key2]['l2address']);
		$this->assertEquals ('label two', $ports[$key2]['label']);
		$this->assertEquals ('reserved two', $ports[$key2]['reservation_comment']);
		$this->assertEquals (4, $ports[$key2]['iif_id']);
		$this->assertEquals (1202, $ports[$key2]['oif_id']);

		usePreparedDeleteBlade ('Port', array ('id' => $port1_id));
		usePreparedDeleteBlade ('Port', array ('id' => $port2_id));
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
