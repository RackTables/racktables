<?php

class ObjectAttributesTest extends PHPUnit_Framework_TestCase
{
	private $server_id, $switch_id;

	public function setUp ()
	{
		$this->server_id = commitAddObject (sprintf ('server-%s-%u', get_class(), getmypid()), NULL, 4, NULL);
		$this->switch_id = commitAddObject (sprintf ('switch-%s-%u', get_class(), getmypid()), NULL, 8, NULL);
	}

	public function tearDown ()
	{
		commitDeleteObject ($this->server_id);
		commitDeleteObject ($this->switch_id);
	}

	private function assertNoValues ()
	{
		foreach (getAttrValues ($this->server_id) as $each)
			$this->assertNull ($each['value']);
		foreach (getAttrValues ($this->switch_id) as $each)
			$this->assertNull ($each['value']);
	}

	/**
	 * @group small
	 */
	public function testCompatible ()
	{
		cacheAllObjectsAttributes();
		$this->assertNoValues();
		$testset = array
		(
			array
			(
				1 => '1234567890', // OEM S/N 1
				3 => 'server.example.com', // FQDN
				14 => 'servers administrator', // contact person
			),
			array
			(
				1 => 'ABCDEF',
				3 => 'switch.example.com',
				14 => 'networks administrator',
			),
		);
		foreach (array ($this->server_id, $this->switch_id) as $object_id)
		{
			// The first round tests INSERT, the second and any subsequent test UPDATE.
			foreach ($testset as $attrs)
				foreach ($attrs as $attr_id => $attr_value)
				{
					commitUpdateAttrValue ($object_id, $attr_id, $attr_value);
					$tmp = getAttrValues ($object_id);
					$this->assertEquals ($attr_value, $tmp[$attr_id]['value']);
				}
			// The final round tests DELETE.
			foreach (array_keys ($tmp) as $attr_id)
				commitUpdateAttrValue ($object_id, $attr_id);
		}
		$this->assertNoValues();
	}

	/**
	 * @group small
	 * @expectedException RTDatabaseError
	 */
	public function testIncompatible1 ()
	{
		// SW version attribute is not enabled for servers by default
		commitUpdateAttrValue ($this->server_id, 5, '1.2.3');
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testIncompatible2 ()
	{
		// no such attribute
		commitUpdateAttrValue ($this->server_id, 5000, 0);
	}

}

?>
