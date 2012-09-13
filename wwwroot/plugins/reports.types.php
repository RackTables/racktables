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
$tab['reports']['rmsobjecttypes'] = 'Object Types';

// What Function should render the tab content
$tabhandler['reports']['rmsobjecttypes'] = array('RMSObjectTypeReport', 'getContent');
// Which Should we Call to handle post back
// View only Report so post not required
// $ophandler['object']['mytab4']['addObjectlog'] = 'addObjectlog';
class RMSObjectTypeReport 
{
	static public function getContent () {
		$obj = new RMSObjectTypeReport();
		$obj->rmsGetObjectsTypes();
	}

	function rmsGetObjectsTypes ()
	{
		echo "<link rel=stylesheet type='text/css' href=/plugins/style.css />";
		echo "<div id=\"rms_pluginWrapper\">";
		echo "<br/>";
		$output = "";
		$objecttypes= "SELECT *
						FROM Dictionary 
						WHERE chapter_id = ?";
		$obtyperesult = usePreparedSelectBlade ($objecttypes, array (1));
		$obtyperesult = $obtyperesult->fetchall();
		foreach ($obtyperesult as $objtyperow)
		{
			$objtype_id = $objtyperow['dict_key'];
			
			$objectdetials =  $this->rmsGetObjectsByType($objtype_id);
			if ($objectdetials <> "") {
			$output .=  "<div class=\"rmsobjecttypes\"> ".$objtyperow['dict_value']."</div>\n";
			$output .=  $objectdetials;
			
			}
			
			
		}
		echo $output;
		echo "</div>";
	}

	function rmsGetObjectsByType ($objtype_id) {
		$output = "";
		$objquery ="SELECT RackObject.* , Dictionary.dict_value as HWType
					FROM RackObject 
					LEFT OUTER JOIN AttributeValue 
					ON AttributeValue.object_id = RackObject.id 
					AND attr_id = ? 
					LEFT OUTER JOIN Dictionary 
					ON AttributeValue.uint_value =  Dictionary.dict_key	 
					WHERE`objtype_id` = ?
					ORDER  BY Dictionary.dict_value";
					
					

		$objresult = usePreparedSelectBlade ($objquery, array(2,$objtype_id));
		$objresult = $objresult->fetchall();
		if (count($objresult) > 0 )
		{
		$output .=  "<TABLE id=\"rmsobjects\">\n";
		$output .=  "<TR><TH class=\"ObjectHWType\">HW Type</TH><TH class=\"ObjectName\">Name</TH><TH class=\"ObjectLabel\">Device Label</TH></TR>\n";
		$class = "even";
		foreach ($objresult as $object)
		{
		$class = ($class=='even') ? 'odd' : 'even';
			$output .=  "<TR class=\"$class\">";
			$output .=  "<TD class=\"ObjectHWType\">".execGMarker(parseWikiLink($object['HWType'],'o'))."</TD>";
			$output .=  "<TD class=\"ObjectName\"><a href=\"index.php?page=object&object_id=".$object['id']. "\">".$object['name']."</a></TD>";
			$output .=  "<TD class=\"ObjectLabel\">".$object['label']."</TD>";
			
			$output .=  "</TR>\n";
		}
		$output .=  "</TABLE>\n";
		}
		return $output;
	}

}


?>
