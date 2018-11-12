<?php

class EmptySQLWhereTest extends RTTestCase
{
	protected $tag_id;

	public function setUp ()
	{
		usePreparedInsertBlade ('TagTree', array ('tag' => $this->myString ('tag')));
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
		usePreparedDeleteBlade ('TagTree', NULL);
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testMalformedUpdate1 ()
	{
		usePreparedUpdateBlade ('TagTree', array ('is_assignable' => 'yes'), NULL);
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testMalformedUpdate2 ()
	{
		usePreparedUpdateBlade ('TagTree', NULL, NULL);
	}
}
