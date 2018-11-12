<?php

// a tag's parent may not be one of its children
// commitUpdateTag should detect this and raise an exception
class TagTreeCircularReferenceTest extends RTTestCase
{
	protected $taga_id, $tagb_id, $tagc_id;

	public function setUp ()
	{
		// add sample data
		usePreparedInsertBlade ('TagTree', array ('tag' => $this->myString ('tag a')));
		$this->taga_id = lastInsertID ();
		usePreparedInsertBlade ('TagTree', array ('tag' => $this->myString ('tag b')));
		$this->tagb_id = lastInsertID ();
		usePreparedInsertBlade ('TagTree', array ('tag' => $this->myString ('tag c')));
		$this->tagc_id = lastInsertID ();
	}

	public function tearDown ()
	{
		// remove sample data
		$ids = array ($this->taga_id, $this->tagb_id, $this->tagc_id);
		usePreparedUpdateBlade ('TagTree', array ('parent_id' => NULL), array ('id' => $ids));
		usePreparedDeleteBlade ('TagTree', array ('id' => $ids));
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testCreateCircularReference ()
	{
		// set A as the parent of B, and B as the parent of C
		commitUpdateTag ($this->tagb_id, $this->myString ('tag b'), $this->taga_id, 'yes');
		commitUpdateTag ($this->tagc_id, $this->myString ('tag c'), $this->tagb_id, 'yes');
		// setting C as the parent of A should fail
		commitUpdateTag ($this->taga_id, $this->myString ('tag a'), $this->tagc_id, 'yes');
	}
}
