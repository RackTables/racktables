<?php

// Verify that some recursive functions return the correct data set
class GetChildrenListTest extends RTTestCase
{
	protected static $num_children = 10;
	protected static $objtype_id;
	protected static $locations = array();
	protected static $objects = array();
	protected static $links = array();
	protected static $tags = array();

	public static function setUpBeforeClass ()
	{
		// add sample locations
		self::$locations[] = $parent_location_id = commitAddObject (self::myStringStatic ('location 0', __CLASS__), NULL, 1562, NULL);
		for ($i=1; $i<=self::$num_children; $i++)
		{
			$child_location_id = commitAddObject (self::myStringStatic ("location ${i}", __CLASS__), NULL, 1562, NULL);
			self::$locations[] = $child_location_id;
			commitLinkEntities ('location', $parent_location_id, 'location', $child_location_id);
			self::$links[] = lastInsertID();
			$parent_location_id = $child_location_id;
		}

		// add sample objects
		usePreparedInsertBlade ('Dictionary', array ('chapter_id' => 1, 'dict_value' => self::myStringStatic ('type', __CLASS__)));
		self::$objtype_id = lastInsertID ();
		commitSupplementOPC (self::$objtype_id, self::$objtype_id);
		self::$objects[] = $parent_object_id = commitAddObject (self::myStringStatic ('object 0', __CLASS__), NULL, self::$objtype_id, NULL);
		for ($i=1; $i<=self::$num_children; $i++)
		{
			$child_object_id = commitAddObject (self::myStringStatic ("object ${i}", __CLASS__), NULL, self::$objtype_id, NULL);
			self::$objects[] = $child_object_id;
			commitLinkEntities ('object', $parent_object_id, 'object', $child_object_id);
			self::$links[] = lastInsertID();
			$parent_object_id = $child_object_id;
		}

		// add sample tags
		usePreparedInsertBlade ('TagTree', array ('tag' => self::myStringStatic ('tag 0', __CLASS__)));
		self::$tags[] = $parent_tag_id = lastInsertID ();
		for ($i=1; $i<=self::$num_children; $i++)
		{
			usePreparedInsertBlade ('TagTree', array ('parent_id' => $parent_tag_id, 'tag' => self::myStringStatic ("tag ${i}", __CLASS__)));
			$parent_tag_id = lastInsertID ();
			self::$tags[] = $parent_tag_id;
		}
		// Refresh the structured version of the table so that getTagDescendents() works as expected.
		global $taglist;
		$taglist = addTraceToNodes (getTagList());
	}

	public static function tearDownAfterClass ()
	{
		foreach (array_reverse (self::$links) as $each)
			commitUnlinkEntitiesByLinkID ($each);
		foreach (array_reverse (self::$objects) as $each)
			commitDeleteObject ($each);
		foreach (array_reverse (self::$locations) as $each)
			commitDeleteObject ($each);
		foreach (array_reverse (self::$tags) as $each)
			usePreparedDeleteBlade ('TagTree', array ('id' => $each));
		commitReduceOPC (self::$objtype_id, self::$objtype_id);
		usePreparedDeleteBlade ('Dictionary', array ('dict_key' => self::$objtype_id));
	}

	/**
	 * @group small
	 */
	public function testGetLocationChildrenList ()
	{
		$children = getLocationChildrenList (array_first (self::$locations));
		$this->assertCount (self::$num_children, $children);
	}

	/**
	 * @group small
	 */
	public function testGetObjectChildrenList ()
	{
		$children = getObjectContentsList (array_first (self::$objects));
		$this->assertCount (self::$num_children, $children);
	}

	/**
	 * @group small
	 */
	public function testGetTagChildrenList ()
	{
		$children = getTagDescendents (array_first (self::$tags));
		$this->assertCount (self::$num_children, $children);
	}
}
