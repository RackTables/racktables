<?php
// This is a test class for the core funcitons of the template engine
class TemplateEngineTest extends PHPUnit_Framework_TestCase
{
	protected static $tplm;

	public static function setUpBeforeClass () {
		// Set up template engine
		self::$tplm = TemplateManager::getInstance();
	}

	public static function tearDownAfterClass () {

	}

	// This test ensures that in a vanilla theme is there
	public function testVanillaReq() {
		$this->assertContains('vanilla', self::$tplm->getOrderedTemplateList());
	}

	// Test the output of an in-memory template
	public function testInMemory() {
		$inMod = self::$tplm->generateModule("HeaderCssInline", true, array('code' => 'TestCode'));
		$inMod2 = self::$tplm->generateModule("GetSelectInLine", true, array('selectName' => 'TestName',
																			 'keyValue' => 'TestKey',
																			 'value' => 'TestValue'));
		
		$this->expectOutputString($inMod->run() . $inMod2->run());
		echo "<style type=\"text/css\">" . "\nTestCode\n</style>\n";
		echo "<input type=hidden name=TestName id=TestName value=TestKey>TestValue";
	}

	// Test the output of a module
	public function testModule() {
		$mod = self::$tplm->generateModule("Tabs", false, array('Tabs' => 'This is test tabs'));
		$this->expectOutputString($mod->run());
		
		echo '<div class=greynavbar>';
		echo "<ul id=foldertab style='margin-bottom: 0px; padding-top: 10px;'>";
		echo 'This is test tabs';
		echo '</ul>';
		echo '</div>';
	}

	// Test the output of a submodule
	public function testSubModule() {
		$mod = self::$tplm->generateModule("Tabs", false);
		$submod = self::$tplm->generateSubmodule("Tabs", "HeaderCssInline", $mod, true, array('code' => 'TestCode'));
		$this->expectOutputString($mod->run());
		
		echo '<div class=greynavbar>';
		echo "<ul id=foldertab style='margin-bottom: 0px; padding-top: 10px;'><style type=\"text/css\">TestCode</style></ul></div>";
	}

	// Test the if funciton of the module 
	public function testIf() {
		$mod = self::$tplm->generateModule("GetOpLink", false, array('loadJS' => false,
																	 'href' => 'HREF',
																	 'issetParams' => true,
																	 'htmlComment' => 'HTMLComment',
																	 'loadImage' => false,
																	 'title' => 'TITLE'));
		$this->expectOutputString($mod->run());
		echo '<a href="HREF" title="HTMLComment">TITLE</a>';
	}

	// Test the loop function of the module
	public function testLoop() {
		$testArray = array(array('taginfo' => 'Info1', 'taginfoRefcnt' => 'Ref1', 'realms' => 'Realm1'),
						   array('taginfo' => 'Info2', 'taginfoRefcnt' => 'Ref2', 'realms' => 'Realm2'),
						   array('taginfo' => 'Info3', 'taginfoRefcnt' => 'Ref3', 'realms' => 'Realm3'));
		$mod = self::$tplm->generateModule('RenderTagStats', false, array('allTags' => $testArray)); 
		$this->expectOutputString($mod->run());

		echo '<table border=1><tr><th>tag</th><th>total</th><th>objects</th><th>IPv4 nets</th><th>IPv6 nets</th>';
		echo '<th>racks</th><th>IPv4 VS</th><th>IPv4 RS pools</th><th>users</th><th>files</th></tr>';
		foreach ($testArray as $row) {
			echo '<tr><td>' . $row['taginfo'] . '</td><td>' . $row['taginfoRefcnt'] . '</td>' . $row['realms'] . '</tr>';
		}
		echo '</table>';
	}
}
?>