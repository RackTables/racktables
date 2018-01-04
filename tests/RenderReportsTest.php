<?php

require_once '../wwwroot/inc/navigation.php';

// Make sure renderDepot does not throw any exceptions
class RenderReportsTest extends RTTestCase
{
	public function setUp ()
	{
		// A minimal prop to make renderPortsReport() print something.
		$this->object_id = commitAddObject ($this->myString ('object'), NULL, 1, NULL);
		commitAddPort ($this->object_id, 'port 1', '1-24', 'label 1', 'aabbccffff01');
	}

	public function tearDown ()
	{
		commitDeleteObject ($this->object_id);
	}

	/**
	 * @group small
	 * @dataProvider providerReportFunctions
	 */
	public function testHandlerPrints ($pageno, $tabno, $funcname)
	{
		global $interface_requires;
		requireExtraFiles ($interface_requires, $pageno, $tabno);
		$this->assertNotEquals ('', getOutputOf ($funcname));
	}

	public function providerReportFunctions ()
	{
		$ret = array();
		global $tabhandler;
		foreach ($tabhandler['reports'] as $tabno => $funcname)
			if ($funcname != 'renderLocalReports') // That one returns an empty string by default.
				$ret[] = array ('reports', $tabno, $funcname);
		return $ret;
	}
}
