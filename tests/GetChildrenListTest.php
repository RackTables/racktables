<?php

// Verify that some recursive functions return the correct data set 
class GetChildrenListTest extends PHPUnit_Framework_TestCase
{
	protected static $num_children = 10;
	protected static $objtype_id;
	protected static $first_location_id, $last_location_id;
	protected static $first_object_id, $last_object_id;
	protected static $first_tag_id, $last_tag_id;

	public static function setUpBeforeClass ()
	{
		// add sample locations
		self::$first_location_id = $parent_location_id = commitAddObject ('unit test location 0', NULL, 1562, NULL);
		for ($i=1; $i<=self::$num_children; $i++)
		{
			$child_location_id = commitAddObject ("unit test location ${i}", NULL, 1562, NULL);
			commitLinkEntities ('location', $parent_location_id, 'location', $child_location_id);
			$parent_location_id = $child_location_id;
		}
		self::$last_location_id = $parent_location_id;

		// add sample objects
		usePreparedInsertBlade ('Dictionary', array ('chapter_id' => 1, 'dict_value' => 'unit test object type'));
		self::$objtype_id = lastInsertID ();
		commitSupplementOPC (self::$objtype_id, self::$objtype_id);
		self::$first_object_id = $parent_object_id = commitAddObject ('unit test object 0', NULL, self::$objtype_id, NULL);
		for ($i=1; $i<=self::$num_children; $i++)
		{
			$child_object_id = commitAddObject ("unit test object ${i}", NULL, self::$objtype_id, NULL);
			commitLinkEntities ('object', $parent_object_id, 'object', $child_object_id);
			$parent_object_id = $child_object_id;
		}
		self::$last_object_id = $parent_object_id;

		// add sample tags
		usePreparedInsertBlade ('TagTree', array ('tag' => 'unit test tag 0'));
		self::$first_tag_id = $parent_tag_id = lastInsertID ();
		for ($i=1; $i<=self::$num_children; $i++)
		{
			usePreparedInsertBlade ('TagTree', array ('parent_id' => $parent_tag_id, 'tag' => "unit test tag ${i}"));
			$parent_tag_id = lastInsertID ();
		}
		self::$last_tag_id = $parent_tag_id;
		// Refresh the structured version of the table so that getTagDescendents() works as expected.
		global $taglist;
		$taglist = addTraceToNodes (getTagList());
	}

	public static function tearDownAfterClass ()
	{
		usePreparedExecuteBlade ('SET foreign_key_checks=0');

		// remove sample locations
		usePreparedExecuteBlade
		(
			'DELETE FROM Object WHERE id BETWEEN ? AND ?',
			array (self::$first_location_id, self::$last_location_id)
		);
		usePreparedExecuteBlade
		(
			"DELETE FROM EntityLink WHERE parent_entity_type='location' AND child_entity_type='location' " .
			'AND ((parent_entity_id BETWEEN ? AND ?) OR (child_entity_id BETWEEN ? AND ?))',
			array
			(
				self::$first_location_id, self::$last_location_id,
				self::$first_location_id, self::$last_location_id
			)
		);

		// remove sample objects
		usePreparedExecuteBlade
		(
			'DELETE FROM Object WHERE id BETWEEN ? AND ?',
			array (self::$first_object_id, self::$last_object_id)
		);
		usePreparedExecuteBlade
		(
			"DELETE FROM EntityLink WHERE parent_entity_type='object' AND child_entity_type='object' " .
			'AND ((parent_entity_id BETWEEN ? AND ?) OR (child_entity_id BETWEEN ? AND ?))',
			array
			(
				self::$first_object_id, self::$last_object_id,
				self::$first_object_id, self::$last_object_id
			)
		);
		commitReduceOPC (self::$objtype_id, self::$objtype_id);
		usePreparedDeleteBlade ('Dictionary', array ('dict_key' => self::$objtype_id));

		// remove sample tags
		usePreparedExecuteBlade
		(
			'DELETE FROM TagTree WHERE id BETWEEN ? AND ?',
			array (self::$first_tag_id, self::$last_tag_id)
		);
		usePreparedExecuteBlade ('SET foreign_key_checks=1');
	}

	public function testGetLocationChildrenList ()
	{
		$children = getLocationChildrenList (self::$first_location_id);
		$this->assertCount (self::$num_children, $children);
	}

	public function testGetObjectChildrenList ()
	{
		$children = getObjectContentsList (self::$first_object_id);
		$this->assertCount (self::$num_children, $children);
	}

	public function testGetTagChildrenList ()
	{
		$children = getTagDescendents (self::$first_tag_id);
		$this->assertCount (self::$num_children, $children);
	}
}
?>
