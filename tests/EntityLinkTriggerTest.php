<?php

// Test the effectiveness of the INSERT and UPDATE triggers on the EntityLink table
//   - if parent and child entities are the same, parent_id != child_id
//   - if both parent and child are objects, an ObjectParentCompat rule must exist
//   - in some scenarios, only one-to-one links are allowed
class EntityLinkTriggerTest extends RTTestCase
{
	protected static $objtypea_id, $objtypeb_id, $objtypec_id, $objtyped_id;
	protected static $objecta_id, $objectb_id, $objectc_id, $objectd_id;
	protected static $locationa_id, $locationb_id, $locationc_id;
	protected static $rowa_id, $rowb_id;
	protected static $racka_id, $rackb_id;

	public static function setUpBeforeClass ()
	{
		// add sample data
		usePreparedInsertBlade ('Dictionary', array ('chapter_id' => 1, 'dict_value' => self::myStringStatic ('type a', __CLASS__)));
		self::$objtypea_id = lastInsertID ();
		usePreparedInsertBlade ('Dictionary', array ('chapter_id' => 1, 'dict_value' => self::myStringStatic ('type b', __CLASS__)));
		self::$objtypeb_id = lastInsertID ();
		usePreparedInsertBlade ('Dictionary', array ('chapter_id' => 1, 'dict_value' => self::myStringStatic ('type c', __CLASS__)));
		self::$objtypec_id = lastInsertID ();
		usePreparedInsertBlade ('Dictionary', array ('chapter_id' => 1, 'dict_value' => self::myStringStatic ('type d', __CLASS__)));
		self::$objtyped_id = lastInsertID ();
		commitSupplementOPC (self::$objtypea_id, self::$objtypeb_id);
		commitSupplementOPC (self::$objtypea_id, self::$objtypec_id);
		commitSupplementOPC (self::$objtypeb_id, self::$objtypea_id);
		commitSupplementOPC (self::$objtypeb_id, self::$objtypec_id);
		commitSupplementOPC (self::$objtypec_id, self::$objtypea_id);
		commitSupplementOPC (self::$objtypec_id, self::$objtypeb_id);
		self::$objecta_id = commitAddObject (self::myStringStatic ('object a', __CLASS__), NULL, self::$objtypea_id, NULL);
		self::$objectb_id = commitAddObject (self::myStringStatic ('object b', __CLASS__), NULL, self::$objtypeb_id, NULL);
		self::$objectc_id = commitAddObject (self::myStringStatic ('object c', __CLASS__), NULL, self::$objtypec_id, NULL);
		self::$objectd_id = commitAddObject (self::myStringStatic ('object d', __CLASS__), NULL, self::$objtyped_id, NULL);
		self::$locationa_id = commitAddObject (self::myStringStatic ('location a', __CLASS__), NULL, 1562, NULL);
		self::$locationb_id = commitAddObject (self::myStringStatic ('location b', __CLASS__), NULL, 1562, NULL);
		self::$locationc_id = commitAddObject (self::myStringStatic ('location c', __CLASS__), NULL, 1562, NULL);
		self::$rowa_id = commitAddObject (self::myStringStatic ('row a', __CLASS__), NULL, 1561, NULL);
		self::$rowb_id = commitAddObject (self::myStringStatic ('row b', __CLASS__), NULL, 1561, NULL);
		self::$racka_id = commitAddObject (self::myStringStatic ('rack a', __CLASS__), NULL, 1560, NULL);
		self::$rackb_id = commitAddObject (self::myStringStatic ('rack b', __CLASS__), NULL, 1560, NULL);
	}

	public static function tearDownAfterClass ()
	{
		// remove sample data
		commitDeleteObject (self::$objecta_id);
		commitDeleteObject (self::$objectb_id);
		commitDeleteObject (self::$objectc_id);
		commitDeleteObject (self::$objectd_id);
		commitReduceOPC (self::$objtypea_id, self::$objtypeb_id);
		commitReduceOPC (self::$objtypea_id, self::$objtypec_id);
		commitReduceOPC (self::$objtypeb_id, self::$objtypea_id);
		commitReduceOPC (self::$objtypeb_id, self::$objtypec_id);
		commitReduceOPC (self::$objtypec_id, self::$objtypea_id);
		commitReduceOPC (self::$objtypec_id, self::$objtypeb_id);
		usePreparedDeleteBlade ('Dictionary', array ('dict_key' => array (self::$objtypea_id, self::$objtypeb_id, self::$objtypec_id, self::$objtyped_id)));
		commitDeleteObject (self::$locationa_id);
		commitDeleteObject (self::$locationb_id);
		commitDeleteObject (self::$locationc_id);
		commitDeleteObject (self::$rowa_id);
		commitDeleteObject (self::$rowb_id);
		commitDeleteObject (self::$racka_id);
		commitDeleteObject (self::$rackb_id);
	}

	public function tearDown ()
	{
		// delete any links created during the test
		usePreparedExecuteBlade
		(
			"DELETE FROM EntityLink WHERE parent_entity_type='object' AND child_entity_type='object' " .
			'AND (parent_entity_id IN (?,?,?) OR child_entity_id IN (?,?,?))',
			array (self::$objecta_id, self::$objectb_id, self::$objectc_id, self::$objecta_id, self::$objectb_id, self::$objectc_id)
		);
		usePreparedExecuteBlade
		(
			"DELETE FROM EntityLink WHERE parent_entity_type='location' AND child_entity_type='location' " .
			'AND (parent_entity_id IN (?,?,?) OR child_entity_id IN (?,?,?))',
			array (self::$locationa_id, self::$locationb_id, self::$locationc_id, self::$locationa_id, self::$locationb_id, self::$locationc_id)
		);
		usePreparedExecuteBlade
		(
			"DELETE FROM EntityLink WHERE (child_entity_type='row' AND child_entity_id IN (?,?)) " .
			"OR (child_entity_type='rack' AND child_entity_id IN (?,?))",
			array (self::$rowa_id, self::$rowb_id, self::$racka_id, self::$rackb_id)
		);
	}

	/**
	 * @group small
	 * @expectedException PDOException
	 */
	public function testLinkObjectToSelfByInsert ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'object', 'parent_entity_id' => self::$objecta_id,
				'child_entity_type' => 'object', 'child_entity_id' => self::$objecta_id
			)
		);
	}

	/**
	 * @group small
	 * @expectedException PDOException
	 */
	public function testLinkObjectToSelfByUpdate ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'object', 'parent_entity_id' => self::$objecta_id,
				'child_entity_type' => 'object', 'child_entity_id' => self::$objectb_id
			)
		);
		usePreparedUpdateBlade
		(
			'EntityLink',
			array ('child_entity_id' => self::$objecta_id),
			array ('id' => lastInsertID ())
		);
	}

	/**
	 * @group small
	 */
	public function testCreateLinkBetweenCompatibleObjects ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'object', 'parent_entity_id' => self::$objecta_id,
				'child_entity_type' => 'object', 'child_entity_id' => self::$objectb_id
			)
		);
		$this->assertTrue (TRUE);
	}

	/**
	 * @group small
	 */
	public function testUpdateLinkBetweenCompatibleObjects ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'object', 'parent_entity_id' => self::$objecta_id,
				'child_entity_type' => 'object', 'child_entity_id' => self::$objectb_id
			)
		);
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'object', 'parent_entity_id' => self::$objectb_id,
				'child_entity_type' => 'object', 'child_entity_id' => self::$objectc_id
			)
		);
		usePreparedUpdateBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_id' => self::$objecta_id,
				'child_entity_id' => self::$objectc_id
			),
			array ('id' => lastInsertID ())
		);
		$this->assertTrue (TRUE);
	}

	/**
	 * @group small
	 * @expectedException PDOException
	 */
	public function testCreateLinkBetweenIncompatibleObjects ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'object', 'parent_entity_id' => self::$objecta_id,
				'child_entity_type' => 'object', 'child_entity_id' => self::$objectd_id
			)
		);
	}

	/**
	 * @group small
	 * @expectedException PDOException
	 */
	public function testUpdateLinkBetweenIncompatibleObjects ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'object', 'parent_entity_id' => self::$objecta_id,
				'child_entity_type' => 'object', 'child_entity_id' => self::$objectb_id
			)
		);
		usePreparedUpdateBlade
		(
			'EntityLink',
			array ('child_entity_id' => self::$objectd_id),
			array ('id' => lastInsertID ())
		);
	}

	/**
	 * @group small
	 */
	public function testCreateLinkBetweenLocations ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'location', 'parent_entity_id' => self::$locationa_id,
				'child_entity_type' => 'location', 'child_entity_id' => self::$locationb_id
			)
		);
		$this->assertTrue (TRUE);
	}

	/**
	 * @group small
	 * @expectedException PDOException
	 */
	public function testLinkLocationToMultipleLocations ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'location', 'parent_entity_id' => self::$locationa_id,
				'child_entity_type' => 'location', 'child_entity_id' => self::$locationb_id
			)
		);
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'location', 'parent_entity_id' => self::$locationc_id,
				'child_entity_type' => 'location', 'child_entity_id' => self::$locationb_id
			)
		);
	}

	/**
	 * @group small
	 */
	public function testUpdateLinkBetweenLocations ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'location', 'parent_entity_id' => self::$locationa_id,
				'child_entity_type' => 'location', 'child_entity_id' => self::$locationb_id
			)
		);
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'location', 'parent_entity_id' => self::$locationb_id,
				'child_entity_type' => 'location', 'child_entity_id' => self::$locationc_id
			)
		);
		usePreparedUpdateBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_id' => self::$locationa_id,
				'child_entity_id' => self::$locationc_id
			),
			array ('id' => lastInsertID ())
		);
		$this->assertTrue (TRUE);
	}

	/**
	 * @group small
	 */
	public function testLinkRowToLocation ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'location', 'parent_entity_id' => self::$locationa_id,
				'child_entity_type' => 'row', 'child_entity_id' => self::$rowa_id
			)
		);
		$this->assertTrue (TRUE);
	}

	/**
	 * @group small
	 * @expectedException PDOException
	 */
	public function testLinkRowToMultipleLocations ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'location', 'parent_entity_id' => self::$locationa_id,
				'child_entity_type' => 'row', 'child_entity_id' => self::$rowa_id
			)
		);
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'location', 'parent_entity_id' => self::$locationb_id,
				'child_entity_type' => 'row', 'child_entity_id' => self::$rowa_id
			)
		);
	}

	/**
	 * @group small
	 */
	public function testUpdateRowLink ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'location', 'parent_entity_id' => self::$locationa_id,
				'child_entity_type' => 'row', 'child_entity_id' => self::$rowa_id
			)
		);
		usePreparedUpdateBlade
		(
			'EntityLink',
			array ('parent_entity_id' => self::$locationb_id),
			array ('id' => lastInsertID ())
		);
		$this->assertTrue (TRUE);
	}

	/**
	 * @group small
	 * @expectedException PDOException
	 */
	public function testInvalidateRowLink ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'location', 'parent_entity_id' => self::$locationa_id,
				'child_entity_type' => 'row', 'child_entity_id' => self::$rowa_id
			)
		);
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'location', 'parent_entity_id' => self::$locationb_id,
				'child_entity_type' => 'row', 'child_entity_id' => self::$rowb_id
			)
		);
		usePreparedUpdateBlade
		(
			'EntityLink',
			array ('child_entity_id' => self::$rowa_id),
			array ('id' => lastInsertID ())
		);
	}

	/**
	 * @group small
	 */
	public function testLinkRackToRow ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'row', 'parent_entity_id' => self::$rowa_id,
				'child_entity_type' => 'rack', 'child_entity_id' => self::$racka_id
			)
		);
		$this->assertTrue (TRUE);
	}

	/**
	 * @group small
	 * @expectedException PDOException
	 */
	public function testLinkRackToMultipleRows ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'row', 'parent_entity_id' => self::$rowa_id,
				'child_entity_type' => 'rack', 'child_entity_id' => self::$racka_id
			)
		);
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'row', 'parent_entity_id' => self::$rowb_id,
				'child_entity_type' => 'rack', 'child_entity_id' => self::$racka_id
			)
		);
	}

	/**
	 * @group small
	 */
	public function testUpdateRackLink ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'row', 'parent_entity_id' => self::$rowa_id,
				'child_entity_type' => 'rack', 'child_entity_id' => self::$racka_id
			)
		);
		usePreparedUpdateBlade
		(
			'EntityLink',
			array ('parent_entity_id' => self::$rowb_id),
			array ('id' => lastInsertID ())
		);
		$this->assertTrue (TRUE);
	}

	/**
	 * @group small
	 * @expectedException PDOException
	 */
	public function testInvalidateRackLink ()
	{
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'row', 'parent_entity_id' => self::$rowa_id,
				'child_entity_type' => 'rack', 'child_entity_id' => self::$racka_id
			)
		);
		usePreparedInsertBlade
		(
			'EntityLink',
			array
			(
				'parent_entity_type' => 'row', 'parent_entity_id' => self::$rowb_id,
				'child_entity_type' => 'rack', 'child_entity_id' => self::$rackb_id
			)
		);
		usePreparedUpdateBlade
		(
			'EntityLink',
			array ('child_entity_id' => self::$racka_id),
			array ('id' => lastInsertID ())
		);
	}
}
