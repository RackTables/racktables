<?php

class TagFunctionsTest extends RTTestCase
{
	protected $a_obj_id, $b_obj_id, $ab_obj_id;
	protected $a_tag_ids, $b_tag_ids;
	const NUM_TAGS = 4;
	const COLOR_A = 0x559900, COLOR_B = 0x112233;

	protected function assertObjectColors ($object_id, $uint_colors)
	{
		$obj = spotEntity ('object', $object_id);
		setEntityColors ($obj);
		$html_colors = array_map ('HTMLColorFromDatabase', $uint_colors);
		sort ($html_colors);
		sort ($obj['colors']);
		$this->assertEquals ($html_colors, $obj['colors']);
	}

	public function setUp() : void
	{
		global $taglist;

		$this->a_tag_ids = array();
		$this->b_tag_ids = array();
		for ($i = 0; $i < self::NUM_TAGS; $i++)
		{
			usePreparedInsertBlade ('TagTree', array ('tag' => $this->myString ("tag a${i}"), 'color' => self::COLOR_A + $i));
			$this->a_tag_ids[] = lastInsertID();
			usePreparedInsertBlade ('TagTree', array ('tag' => $this->myString ("tag b${i}"), 'color' => self::COLOR_B));
			$this->b_tag_ids[] = lastInsertID();
		}
		$taglist = addTraceToNodes (getTagList());

		$this->a_obj_id = commitAddObject ($this->myString ('server a'), NULL, 4, NULL, $this->a_tag_ids);
		$this->b_obj_id = commitAddObject ($this->myString ('server b'), NULL, 4, NULL, $this->b_tag_ids);
		$this->ab_obj_id = commitAddObject ($this->myString ('server ab'), NULL, 4, NULL, array_merge ($this->a_tag_ids, $this->b_tag_ids));
	}

	public function tearDown() : void
	{
		// Objects first to release the foreign key.
		commitDeleteObject ($this->a_obj_id);
		commitDeleteObject ($this->b_obj_id);
		commitDeleteObject ($this->ab_obj_id);
		usePreparedDeleteBlade ('TagTree', array ('id' => array_merge ($this->a_tag_ids, $this->b_tag_ids)));
	}

	/**
	 * @group small
	 */
	public function testReadOnly()
	{
		// fixtures
		$this->assertEquals (self::NUM_TAGS, count ($this->a_tag_ids));
		$this->assertEquals (self::NUM_TAGS, count ($this->b_tag_ids));

		// loading
		$this->assertEquals (array_values ($this->a_tag_ids), array_values (reduceSubarraysToColumn (loadEntityTags ('object', $this->a_obj_id), 'id')));
		$this->assertEquals (array_values ($this->b_tag_ids), array_values (reduceSubarraysToColumn (loadEntityTags ('object', $this->b_obj_id), 'id')));
		$this->assertEquals (array_values (array_merge ($this->a_tag_ids, $this->b_tag_ids)), array_values (reduceSubarraysToColumn (loadEntityTags ('object', $this->ab_obj_id), 'id')));

		// transforming
		$this->assertEquals ($this->a_tag_ids, buildTagIdsFromChain (buildTagChainFromIds ($this->a_tag_ids)));

		// colors
		$a_colors = range (self::COLOR_A, self::COLOR_A + self::NUM_TAGS - 1);
		$this->assertObjectColors ($this->a_obj_id, $a_colors);
		$b_colors = array (self::COLOR_B);
		$this->assertObjectColors ($this->b_obj_id, $b_colors);
		$ab_colors = array_merge ($a_colors, $b_colors);
		$this->assertObjectColors ($this->ab_obj_id, $ab_colors);
	}
}
