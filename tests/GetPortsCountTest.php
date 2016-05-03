<?php

class GetPortsCount extends PHPUnit_Framework_TestCase
{
	private $object_id;
	public function setUp ()
	{
		$this->object_id = commitAddObject
		(
			sprintf ('testobject-%s-%u', get_class(), getmypid()),
			'',
			11, # spacer should not trigger AutoPorts
			''
		);
	}

	/**
	 * @group small
	 */
	public function testAll ()
	{
		$this->assertEquals (0, getPortsCount ($this->object_id));
		$port_ids = array
		(
			'port 1' => array (commitAddPort ($this->object_id, 'port 1', '1-24', '', '')),
			'port 2' => array (commitAddPort ($this->object_id, 'port 2', '1-24', '', '')),
			'port 3' => array
			(
				commitAddPort ($this->object_id, 'port 3', '1-24', '', ''), # 1000Base-T
				commitAddPort ($this->object_id, 'port 3', '3-1078', '', ''), # empty GBIC
				commitAddPort ($this->object_id, 'port 3', '4-1204', '', ''), # SFP-1000/1000Base-LX
			),
		);
		$this->assertEquals (5, getPortsCount ($this->object_id));
		foreach ($port_ids as $port_name => $idlist)
			$this->assertEquals ($idlist, getPortIDs ($this->object_id, $port_name));
		$this->assertSame (NULL, getPortReservationComment (array_first ($port_ids['port 1'])));
		commitUpdatePortComment (array_first ($port_ids['port 1']), 'test comment');
		$this->assertEquals ('test comment', getPortReservationComment (array_first ($port_ids['port 1'])));
		commitUpdatePortComment (array_first ($port_ids['port 1']), ''); # empty string becomes NULL
		$this->assertSame (NULL, getPortReservationComment (array_first ($port_ids['port 1'])));
	}

	public function tearDown ()
	{
		commitDeleteObject ($this->object_id);
	}
}

?>
