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
		$output = getOutputOf ($funcname);
		$this->assertNotEquals ('', $output);
		if ($tabno == 'integrity')
			$this->assertEquals ('No integrity violations found', strip_tags ($output));
	}

	public function providerReportFunctions ()
	{
		$ret = array();
		global $tabhandler;
		foreach ($tabhandler['reports'] as $tabno => $funcname)
			$ret[] = array ('reports', $tabno, $funcname);
		return $ret;
	}
}
