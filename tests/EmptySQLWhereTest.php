<?php

class EmptySQLWhere extends PHPUnit_Framework_TestCase
{
	protected $tag_id;

	public function setUp ()
	{
		usePreparedInsertBlade ('TagTree', array ('tag' => 'unit test tag'));
		$this->tag_id = lastInsertID ();
	}

	public function tearDown ()
	{
		usePreparedDeleteBlade ('TagTree', array ('id' => $this->tag_id));
	}

	/**
	 * @expectedException InvalidArgException
	 */
	public function testMalformedDelete ()
	{
		usePreparedDeleteBlade ('TagTree');
	}

	/**
	 * @expectedException InvalidArgException
	 */
	public function testMalformedUpdate1 ()
	{
		usePreparedUpdateBlade ('TagTree', array ('is_assignable' => 'yes'));
	}

	/**
	 * @expectedException InvalidArgException
	 */
	public function testMalformedUpdate2 ()
	{
		usePreparedUpdateBlade ('TagTree');
	}
}

?>