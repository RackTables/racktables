<?php

// RMS Reports Object Types
// by James Tutton
// Version 0.1

// Installation:
// 1)  Add plugin to ./plugins/ folder;
// 2)  Make sure plugins folder is included in inc/local.php
/*

// Start Plugin Support to Racktables
foreach (glob($_SERVER['DOCUMENT_ROOT']."/plugins/*.php") as $filename) {
   include($filename);
}
// End Plugin Support to Racktables

*/

// Which Tab Section should we render on what should we Name the Tab
$tab['reports']['rmscircuits'] = 'Redstone Circuits';

// What Function should render the tab content
$tabhandler['reports']['rmscircuits'] = array('RMSCircuitReport', 'getContent');
// Which Should we Call to handle post back
// View only Report so post not required
// $ophandler['object']['mytab4']['addObjectlog'] = 'addObjectlog';
class RMSCircuitReport 
{
	static public function getContent () {
		$obj = new RMSCircuitReport();
		$obj->rmsGetRedstoneCircuits();
	}

	function rmsGetRedstoneCircuits () {
		$output = "";
		$objquery ="SELECT string_value AS  `CircuitRef` , name AS Object, `RackObject`.id as id
					FROM  `AttributeValue` 
					INNER JOIN  `RackObject` ON RackObject.id =  `AttributeValue`.object_id
					AND attr_id =10002
					AND string_value
					REGEXP  'RED[0-9]{5}'
					ORDER BY string_value DESC 
					LIMIT 0 , 30
					";
					
					

		echo "<link rel=stylesheet type='text/css' href=/plugins/style.css />";
		echo "<div id=\"rms_pluginWrapper\">";
		echo "<br/>";
		$objresult = usePreparedSelectBlade ($objquery);
		$objresult = $objresult->fetchall();
		if (count($objresult) > 0 )
		{
		$output .=  "<TABLE id=\"rmsobjects\">\n";
		$output .=  "<TR><TH class=\"ObjectHWType\">CircuitRef</TH><TH class=\"ObjectName\">Object</TH></TR>\n";
		$class = "even";
		foreach ($objresult as $object)
		{
		$class = ($class=='even') ? 'odd' : 'even';
			$output .=  "<TR class=\"$class\">";
			$output .=  "<TD class=\"ObjectHWType\">".$object['CircuitRef']."</TD>";
			$output .=  "<TD class=\"ObjectName\"><a href=\"/index.php?page=object&tab=linkmgmt&object_id=".$object['id']. "\">".$object['Object']."</a></TD>";
			$output .=  "</TR>\n";
		}
		$output .=  "</TABLE>\n";
		}
		echo $output;
		echo "</div>";
	}

}


?>
