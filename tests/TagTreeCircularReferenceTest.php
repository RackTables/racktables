<?php

// a tag's parent may not be one of its children
// commitUpdateTag should detect this and raise an exception
class TagTreeCircularReferenceTest extends PHPUnit_Framework_TestCase
{
	protected $taga_id, $tagb_id, $tagc_id;

	public function setUp ()
	{
		// add sample data
		usePreparedInsertBlade ('TagTree', array ('tag' => 'unit test tag a'));
		$this->taga_id = lastInsertID ();
		usePreparedInsertBlade ('TagTree', array ('tag' => 'unit test tag b'));
		$this->tagb_id = lastInsertID ();
		usePreparedInsertBlade ('TagTree', array ('tag' => 'unit test tag c'));
		$this->tagc_id = lastInsertID ();
	}

	public function tearDown ()
	{
		// remove sample data
		usePreparedExecuteBlade
		(
			'UPDATE TagTree SET parent_id = NULL WHERE id IN (?,?,?)',
			array ($this->taga_id, $this->tagb_id, $this->tagc_id)
		);
		usePreparedDeleteBlade ('TagTree', array ('id' => $this->taga_id));
		usePreparedDeleteBlade ('TagTree', array ('id' => $this->tagb_id));
		usePreparedDeleteBlade ('TagTree', array ('id' => $this->tagc_id));
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testCreateCircularReference ()
	{
		// set A as the parent of B, and B as the parent of C
		commitUpdateTag ($this->tagb_id, 'unit test tag b', $this->taga_id, 'yes');
		commitUpdateTag ($this->tagc_id, 'unit test tag c', $this->tagb_id, 'yes');
		// setting C as the parent of A should fail
		commitUpdateTag ($this->taga_id, 'unit test tag a', $this->tagc_id, 'yes');
	}
}
?>
