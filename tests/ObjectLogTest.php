<?php

// Create logs associated with various entities, make sure the report still works
// Inspired by ticket #1443
class ObjectLogTest extends PHPUnit_Framework_TestCase
{
	protected static $rack_id, $row_id, $location_id, $object_id;
	protected $rack_log_id = NULL;
	protected $row_log_id = NULL;
	protected $location_log_id = NULL;
	protected $object_log_id = NULL;

	public static function setUpBeforeClass ()
	{
		global $sic;
		$sic['logentry'] = $_REQUEST['logentry'] = 'unit test log entry';

		// add sample data
		self::$rack_id = commitAddObject ('unit test rack', NULL, 1560, NULL);
		self::$row_id = commitAddObject ('unit test row', NULL, 1561, NULL);
		self::$location_id = commitAddObject ('unit test location', NULL, 1562, NULL);
		self::$object_id = commitAddObject ('unit test object', NULL, 1, NULL);
	}

	public static function tearDownAfterClass ()
	{
		// remove sample data
		commitDeleteObject (self::$rack_id);
		commitDeleteObject (self::$row_id);
		commitDeleteObject (self::$location_id);
		commitDeleteObject (self::$object_id);
	}

	/**
	 * @group small
	 */
	public function testAddRackLogEntry ()
	{
		global $sic;
		$sic['rack_id'] = self::$rack_id;
		$this->rack_log_id = addObjectlog ();
		unset ($sic['rack_id']);
		$this->assertTrue (TRUE);
	}

	/**
	 * @group small
	 */
	public function testAddRowLogEntry ()
	{
		global $sic;
		$sic['row_id'] = self::$row_id;
		$this->row_log_id = addObjectlog ();
		unset ($sic['row_id']);
		$this->assertTrue (TRUE);
	}

	/**
	 * @group small
	 */
	public function testAddLocationLogEntry ()
	{
		global $sic;
		$sic['location_id'] = self::$location_id;
		$this->location_log_id = addObjectlog ();
		unset ($sic['location_id']);
		$this->assertTrue (TRUE);
	}

	/**
	 * @group small
	 */
	public function testAddObjectLogEntry ()
	{
		global $sic;
		$sic['object_id'] = self::$object_id;
		$this->object_log_id = addObjectlog ();
		unset ($sic['object_id']);
		$this->assertTrue (TRUE);
	}

	/**
	 * @group small
	 */
	public function testRenderLogRecords ()
	{
		try {
			ob_start ();
			allObjectLogs ();
			ob_end_clean ();
			$this->assertTrue (TRUE);
		}
 		catch (Exception $e) {
			ob_end_clean ();
	        $this->assertTrue (FALSE);
        }
	}
}
?>
