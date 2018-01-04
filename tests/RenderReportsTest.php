<?php

require_once '../wwwroot/inc/navigation.php';

// Make sure renderDepot does not throw any exceptions
class RenderReportsTest extends RTTestCase
{
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
