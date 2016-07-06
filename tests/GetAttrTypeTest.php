<?php

class GetAttrTypeTest extends PHPUnit_Framework_TestCase
{
	private $attr;
	public function setUp ()
	{
		$this->attr = array();
		foreach (array ('string', 'uint', 'float', 'dict', 'date') as $attr_type)
		{
			$attr_name = sprintf ('%sattr_%s_%u', $attr_type, get_class(), getmypid());
			usePreparedInsertBlade ('Attribute', array ('type' => $attr_type, 'name' => $attr_name));
			$this->attr[lastInsertID()] = $attr_type;
		}
	}

	/**
	 * @group small
	 */
	public function testAll ()
	{
		foreach ($this->attr as $attr_id => $attr_type)
			$this->assertEquals ($attr_type, getAttrType ($attr_id));
	}

	public function tearDown ()
	{
		foreach (array_keys ($this->attr) as $attr_id)
			usePreparedDeleteBlade ('Attribute', array ('id' => $attr_id));
	}
}

?>
