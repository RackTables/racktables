<?php

// An object's parent may not be one of its children.
// The same principle applies to locations, which are stored in the DB as objects.
// commitLinkEntities and commitUpdateEntityLink should each detect this and raise an exception
class ObjectCircularReferenceTest extends RTTestCase
{
	protected static $objtype_id;
	protected static $objecta_id, $objectb_id, $objectc_id;
	protected static $locationa_id, $locationb_id, $locationc_id;

	public static function setUpBeforeClass ()
	{
		// add sample data
		usePreparedInsertBlade ('Dictionary', array ('chapter_id' => 1, 'dict_value' => self::myStringStatic ('type', __CLASS__)));
		self::$objtype_id = lastInsertID ();
		commitSupplementOPC (self::$objtype_id, self::$objtype_id);
		self::$objecta_id = commitAddObject (self::myStringStatic ('object a', __CLASS__), NULL, self::$objtype_id, NULL);
		self::$objectb_id = commitAddObject (self::myStringStatic ('object b', __CLASS__), NULL, self::$objtype_id, NULL);
		self::$objectc_id = commitAddObject (self::myStringStatic ('object c', __CLASS__), NULL, self::$objtype_id, NULL);
		self::$locationa_id = commitAddObject (self::myStringStatic ('location a', __CLASS__), NULL, 1562, NULL);
		self::$locationb_id = commitAddObject (self::myStringStatic ('location b', __CLASS__), NULL, 1562, NULL);
		self::$locationc_id = commitAddObject (self::myStringStatic ('location c', __CLASS__), NULL, 1562, NULL);
	}

	public static function tearDownAfterClass ()
	{
		// remove sample data
		commitDeleteObject (self::$objecta_id);
		commitDeleteObject (self::$objectb_id);
		commitDeleteObject (self::$objectc_id);
		commitReduceOPC (self::$objtype_id, self::$objtype_id);
		usePreparedDeleteBlade ('Dictionary', array ('dict_key' => self::$objtype_id));
		commitDeleteObject (self::$locationa_id);
		commitDeleteObject (self::$locationb_id);
		commitDeleteObject (self::$locationc_id);
	}

	public function tearDown ()
	{
		// delete any links created during the test
		usePreparedExecuteBlade
		(
			"DELETE FROM EntityLink WHERE parent_entity_type='location' AND child_entity_type='location' " .
			'AND (parent_entity_id IN (?,?,?) OR child_entity_id IN (?,?,?))',
			array
			(
				self::$locationa_id, self::$locationb_id, self::$locationc_id,
				self::$locationa_id, self::$locationb_id, self::$locationc_id
			)
		);
		usePreparedExecuteBlade
		(
			"DELETE FROM EntityLink WHERE parent_entity_type='object' AND child_entity_type='object' " .
			'AND (parent_entity_id IN (?,?,?) OR child_entity_id IN (?,?,?))',
			array
			(
				self::$objecta_id, self::$objectb_id, self::$objectc_id,
				self::$objecta_id, self::$objectb_id, self::$objectc_id
			)
		);
	}

	/**
	 * @group small
	 * @expectedException RackTablesError
	 */
	public function testCreateObjectCircularReference ()
	{
		// set A as the parent of B, and B as the parent of C
		commitLinkEntities ('object', self::$objecta_id, 'object', self::$objectb_id);
		commitLinkEntities ('object', self::$objectb_id, 'object', self::$objectc_id);
		// setting C as the parent of A should fail
		commitLinkEntities ('object', self::$objectc_id, 'object', self::$objecta_id);
	}

	/**
	 * @group small
	 * @expectedException RackTablesError
	 */
	public function testUpdateObjectCircularReference ()
	{
		// set A as the parent of B, and B as the parent of C
		commitLinkEntities ('object', self::$objecta_id, 'object', self::$objectb_id);
		commitLinkEntities ('object', self::$objectb_id, 'object', self::$objectc_id);
		// reversing the link between B and C should fail
		commitUpdateEntityLink
		(
			'object', self::$objectb_id, 'object', self::$objectc_id,
			'object', self::$objectc_id, 'object', self::$objectb_id
		);
	}

	/**
	 * @group small
	 * @expectedException RackTablesError
	 */
	public function testCreateLocationCircularReference ()
	{
		// set A as the parent of B, and B as the parent of C
		commitLinkEntities ('location', self::$locationa_id, 'location', self::$locationb_id);
		commitLinkEntities ('location', self::$locationb_id, 'location', self::$locationc_id);
		// setting C as the parent of A should fail
		commitLinkEntities ('location', self::$locationc_id, 'location', self::$locationa_id);
	}

	/**
	 * @group small
	 * @expectedException RackTablesError
	 */
	public function testUpdateLocationCircularReference ()
	{
		// set A as the parent of B, and B as the parent of C
		commitLinkEntities ('location', self::$locationa_id, 'location', self::$locationb_id);
		commitLinkEntities ('location', self::$locationb_id, 'location', self::$locationc_id);
		// reversing the link between B and C should fail
		commitUpdateEntityLink
		(
			'location', self::$locationb_id, 'location', self::$locationc_id,
			'location', self::$locationc_id, 'location', self::$locationb_id
		);
	}
}
