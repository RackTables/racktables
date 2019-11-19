<?php

class EmptySQLWhereTest extends RTTestCase
{
	protected $tag_id;

	public function setUp () : void
	{
		usePreparedInsertBlade ('TagTree', array ('tag' => $this->myString ('tag')));
		$this->tag_id = lastInsertID ();
	}

	public function tearDown () : void
	{
		usePreparedDeleteBlade ('TagTree', array ('id' => $this->tag_id));
	}

	/**
	 * @group small
	 */
	public function testMalformedDelete ()
	{
		$this->expectException (InvalidArgException::class);
		usePreparedDeleteBlade ('TagTree', NULL);
	}

	/**
	 * @group small
	 */
	public function testMalformedUpdate1 ()
	{
		$this->expectException (InvalidArgException::class);
		usePreparedUpdateBlade ('TagTree', array ('is_assignable' => 'yes'), NULL);
	}

	/**
	 * @group small
	 */
	public function testMalformedUpdate2 ()
	{
		$this->expectException (InvalidArgException::class);
		usePreparedUpdateBlade ('TagTree', NULL, NULL);
	}
}
