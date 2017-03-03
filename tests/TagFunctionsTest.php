<?php

class TagFunctionsTest extends PHPUnit_Framework_TestCase
{
	protected $a_obj_id, $b_obj_id, $ab_obj_id;
	protected $a_tag_ids, $b_tag_ids;
	const NUM_TAGS = 4;

	public function setUp()
	{
		global $taglist;
		$sfx = get_class() . '-' . getmypid();

		$this->a_tag_ids = array();
		$this->b_tag_ids = array();
		for ($i = 0; $i < self::NUM_TAGS; $i++)
		{
			usePreparedInsertBlade ('TagTree', array ('tag' => "tag a${i} ${sfx}"));
			$this->a_tag_ids[] = lastInsertID();
			usePreparedInsertBlade ('TagTree', array ('tag' => "tag b${i} ${sfx}"));
			$this->b_tag_ids[] = lastInsertID();
		}
		$taglist = addTraceToNodes (getTagList());

		$this->a_obj_id = commitAddObject ("server a ${sfx}", NULL, 4, NULL, $this->a_tag_ids);
		$this->b_obj_id = commitAddObject ("server b ${sfx}", NULL, 4, NULL, $this->b_tag_ids);
		$this->ab_obj_id = commitAddObject ("server ab ${sfx}", NULL, 4, NULL, array_merge ($this->a_tag_ids, $this->b_tag_ids));
	}

	public function tearDown()
	{
		// Objects first to release the foreign key.
		commitDeleteObject ($this->a_obj_id);
		commitDeleteObject ($this->b_obj_id);
		commitDeleteObject ($this->ab_obj_id);
		foreach (array_merge ($this->a_tag_ids, $this->b_tag_ids) as $tag_id)
			usePreparedDeleteBlade ('TagTree', array ('id' => $tag_id));
	}

	/**
	 * @group small
	 */
	public function testFixtures()
	{
		$this->assertEquals (self::NUM_TAGS, count ($this->a_tag_ids));
		$this->assertEquals (self::NUM_TAGS, count ($this->b_tag_ids));
	}

	/**
	 * @group small
	 */
	public function testLoad()
	{
		$this->assertEquals (array_values ($this->a_tag_ids), array_values (reduceSubarraysToColumn (loadEntityTags ('object', $this->a_obj_id), 'id')));
		$this->assertEquals (array_values ($this->b_tag_ids), array_values (reduceSubarraysToColumn (loadEntityTags ('object', $this->b_obj_id), 'id')));
		$this->assertEquals (array_values (array_merge ($this->a_tag_ids, $this->b_tag_ids)), array_values (reduceSubarraysToColumn (loadEntityTags ('object', $this->ab_obj_id), 'id')));
	}

	/**
	 * @group small
	 */
	public function testTransform()
	{
		$this->assertEquals ($this->a_tag_ids, buildTagIdsFromChain (buildTagChainFromIds ($this->a_tag_ids)));
	}
}

?>
