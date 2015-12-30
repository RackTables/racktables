<?php

class PluginValidationTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider provider
	 */
	public function testFunctionExistence ($plugin)
	{
		global $racktables_plugins_dir;
		require_once "${racktables_plugins_dir}/${plugin}/plugin.php";
		$functions = array ('info', 'init', 'install', 'uninstall', 'upgrade');
		foreach ($functions as $function)
			if (is_callable ("plugin_${plugin}_${function}"))
				$this->assertTrue (TRUE);
			else
				$this->assertTrue (FALSE, "$plugin is missing the $function function");
	}

	/**
	 * @dataProvider provider
	 */
	public function testInstallPlugin ($plugin)
	{
		global $sic;
		$sic['name'] = $_REQUEST['name'] = $plugin;
		$this->expectOutputRegex("/SUCCESS: Installed plugin/");
		installPlugin ();
	}

	/**
	 * @dataProvider provider
	 * @depends testInstallPlugin
	 */
	public function testUninstallPlugin ($plugin)
	{
		global $sic;
		$sic['name'] = $_REQUEST['name'] = $plugin;
		$this->expectOutputRegex("/SUCCESS: Uninstalled plugin/");
		uninstallPlugin ();
	}

	public function testInstallMissingPlugin ()
	{
		global $sic;
		$sic['name'] = $_REQUEST['name'] = 'missingPlugin';
		$this->expectOutputRegex("/ERROR: Install failed/");
		installPlugin ();
	}

	function provider ()
	{
		$ret = array ();
		foreach (getPlugins () as $name => $info)
			$ret[] = array ($name);
		return $ret;
	}
}
?>
