<?php

// Make sure renderDepot does not throw any exceptions
class RenderDepotTest extends RTTestCase
{
	protected $shelf_id = NULL;
	protected $modem_id = NULL;

	public function setUp ()
	{
		@session_start();
		// create a nameless shelf that contains a modem (re: ticket #1115)
		$this->shelf_id = commitAddObject (NULL, NULL, 3, NULL);
		$this->modem_id = commitAddObject ($this->myString ('modem'), NULL, 13, NULL);
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
		$this->assertNotEquals ('', getOutputOf ('renderDepot'));
	}
}
