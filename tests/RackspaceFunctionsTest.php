<?php

class RackspaceFunctionsTest extends PHPUnit_Framework_TestCase
{
	const UNITS_PER_RACK = 42;
	private $row_id;
	private $row_name;

	// Add a temporary row with a few racks.
	public function setUp ()
	{
		$this->row_name = sprintf ('testrow-%s-%u', get_class(), getmypid());
		$this->row_id = commitAddObject ($this->row_name, NULL, 1561, NULL);
	}

	private function createObjectInRack ($prefix, $type_id, $rack_id, $unit_nos)
	{
		$object_id = commitAddObject (sprintf ('%s-%s-%u', $prefix, get_class(), getmypid()), NULL, $type_id, NULL);
		if (! count ($unit_nos))
			commitLinkEntities ('rack', $rack_id, 'object', $object_id);
		else
			foreach ($unit_nos as $unit_no)
				foreach (array ('front', 'interior', 'rear') as $atom)
					usePreparedInsertBlade
					(
						'RackSpace',
						array
						(
							'rack_id' => $rack_id,
							'unit_no' => $unit_no,
							'atom' => $atom,
							'state' => 'T',
							'object_id' => $object_id,
						)
					);
		return $object_id;
	}

	private function createSampleRacksAndObjects ($racklist)
	{
		$ret = array();
		$i = 0;
		foreach ($racklist as $rack_id => $objectlist)
		{
			$rack_name = sprintf ('rack%u-%s-%s', $i, get_class(), getmypid());
			$rack_id = commitAddObject ($rack_name, NULL, 1560, NULL);
			commitUpdateAttrValue ($rack_id, 27, self::UNITS_PER_RACK);
			commitLinkEntities ('row', $this->row_id, 'rack', $rack_id);
			$ret[$rack_id] = array();
			foreach ($objectlist as $each)
			{
				list ($prefix, $objtype_id, $unit_nos) = $each;
				$object_id = $this->createObjectInRack ("${prefix}-${rack_id}", $objtype_id, $rack_id, $unit_nos);
				$ret[$rack_id][$object_id] = count ($unit_nos);
			}
			$i++;
		}
		return $ret;
	}

	private function deleteSampleRacksAndObjects ($created)
	{
		foreach ($created as $rack_id => $objects)
		{
			foreach (array_keys ($objects) as $object_id)
				commitDeleteObject ($object_id);
			commitDeleteRack ($rack_id);
		}
	}

	/**
	 * @group small
	 */
	public function testGeneral ()
	{
		$row = getRowInfo ($this->row_id);
		$this->assertEquals ($this->row_id, $row['id']);
		$this->assertEquals ($this->row_name, $row['name']);
		$this->assertNull ($row['location_id']);
		$this->assertNull ($row['location']);
	}

	/**
	 * @group small
	 * @dataProvider providerSampleRows
	 */
	public function testSpecific ($racklist)
	{
		$this->assertEquals (0, getRowMountsCount ($this->row_id));
		$created = $this->createSampleRacksAndObjects ($racklist);
		$row_units = 0;
		$row_data = array();
		foreach ($created as $rack_id => $objects)
		{
			$rack_units = array_sum ($objects);
			$row_units += $rack_units;
			$rack = spotEntity ('rack', $rack_id);
			$row_data[$rack_id] = $rack;
			amplifyCell ($rack);
			$this->assertEquals ($rack_units / self::UNITS_PER_RACK, getRSUForRack ($rack));
			$this->assertEquals (count ($objects), getRackMountsCount ($rack_id));
		}
		$row_total_units = count ($created) * self::UNITS_PER_RACK;
		$row_rsu = $row_total_units == 0 ? 0 : ($row_units / $row_total_units);
		$this->assertEquals ($row_rsu, getRSUForRow ($row_data));
		$this->assertEquals (array_sum (array_map ('count', $created)), getRowMountsCount ($this->row_id));
		$row = getRowInfo ($this->row_id);
		$this->assertEquals (count ($created), $row['count']);
		$this->assertEquals ($row_total_units, $row['sum']);
		$this->deleteSampleRacksAndObjects ($created);
		$this->assertEquals (0, getRowMountsCount ($this->row_id));
	}

	public function providerSampleRows()
	{
		return array
		(
			array
			(
				array(), // an empty row
			),
			array
			(
				array // a row with one rack
				(
					array // a rack with one zero-U object
					(
						array ('server1', 4, array()),
					),
				),
			),
			array
			(
				array // a row with one rack
				(
					array // a rack with one 2U object
					(
						array ('server1', 4, array (1, 2)),
					),
				),
			),
			array
			(
				array // a row with one rack
				(
					array // a rack with one zero-U and a few 2U objects
					(
						array ('server1', 4, array (1, 2)),
						array ('server2', 4, array()),
						array ('server3', 4, array (11, 12)),
					),
				),
			),
			array
			(
				array // a row with a few racks, each filled in a different way
				(
					array
					(
						array ('server1', 4, array (1)),
						array ('server2', 4, array (2)),
						array ('server3', 4, array (3)),
						array ('server4', 4, array (4)),
						array ('server5', 4, array (5)),
						array ('server6', 4, array (6)),
						array ('server7', 4, array (7)),
						array ('server8', 4, array (8)),
						array ('server9', 4, array (9)),
						array ('server10', 4, array (10)),
						array ('server11', 4, array (11)),
						array ('server12', 4, array (12)),
						array ('server13', 4, array (13)),
						array ('server14', 4, array (14)),
						array ('server15', 4, array (15)),
					),
					array
					(
					),
					array
					(
						array ('router1', 7, array()),
						array ('router2', 7, array()),
						array ('switch', 8, array (30, 31, 32, 33)),
						array ('server1', 4, array (10, 11)),
						array ('server2', 4, array (12, 13)),
						array ('server3', 4, array (14, 15)),
						array ('server4', 4, array (16, 17)),
						array ('server5', 4, array (18, 19)),
					),
					array
					(
						array ('switch1', 8, range (1, 20)),
						array ('switch2', 8, range (21, 40)),
					),
					array
					(
						array ('router1', 7, array()),
						array ('router2', 7, array()),
						array ('router3', 7, array()),
						array ('router4', 7, array()),
					),
				),
			),
		);
	}

	public function tearDown ()
	{
		commitDeleteRow ($this->row_id);
	}
}

?>
