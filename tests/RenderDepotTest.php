<?php

// Make sure renderDepot does not throw any exceptions
class RenderDepotTest extends PHPUnit_Framework_TestCase
{
	protected $shelf_id = NULL;
	protected $modem_id = NULL;

	public function setUp ()
	{
		@session_start();
		// create a nameless shelf which contains a modem (re: ticket #1115)
		$this->shelf_id = commitAddObject (NULL, NULL, 3, NULL);
		$this->modem_id = commitAddObject ('unit test modem', NULL, 13, NULL);
		commitLinkEntities ('object', $this->shelf_id, 'object', $this->modem_id);
	}

	public function tearDown ()
	{
		// remove sample data
		commitDeleteObject ($this->modem_id);
		commitDeleteObject ($this->shelf_id);
	}

	/**
	 * @group small
	 */
	public function testRenderDepot ()
	{
		try {
			ob_start ();
			renderDepot ();
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
