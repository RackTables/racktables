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
		commitAddPort ($this->object_id, 'port 1', '1-24', '', '');
		commitAddPort ($this->object_id, 'port 2', '1-24', '', '');
		$this->assertEquals (2, getPortsCount ($this->object_id));
	}

	public function tearDown ()
	{
		commitDeleteObject ($this->object_id);
	}
}

?>
