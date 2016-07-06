<?php

class EmptySQLWhereTest extends PHPUnit_Framework_TestCase
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
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testMalformedDelete ()
	{
		usePreparedDeleteBlade ('TagTree');
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testMalformedUpdate1 ()
	{
		usePreparedUpdateBlade ('TagTree', array ('is_assignable' => 'yes'));
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testMalformedUpdate2 ()
	{
		usePreparedUpdateBlade ('TagTree');
	}
}

?>