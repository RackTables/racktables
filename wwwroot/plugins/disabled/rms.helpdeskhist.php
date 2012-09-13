<?php

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
$tab['object']['RMSHelpdesk'] = 'RMS Ticket History';

// What Function should render the tab content
$tabhandler['object']['RMSHelpdesk'] = array('RMSHelpdesk', 'getContent');
// Which Should we Call to handle post back
# View only Report so post not required
#$ophandler['object']['mytab4']['addObjectlog'] = 'addObjectlog';
class RMSHelpdesk 
{
	static public function getContent () {
		$obj = new RMSHelpdesk();
		$obj->rmsGetTickets();
	}

	function rmsGetTickets ()
	{
		
		
		echo "<link rel=stylesheet type='text/css' href=/rms_addons/style.css />";
		echo "<div id=\"rms_addonWrapper\">";
		echo "<br/>";
		$this->ConnectToRMS();
		
		echo "</div>";
	}
	function ConnectToRMS () {
		if ($_GET["connected"] == 1)
		{
			echo "Connected as we Requested";
			$link = mssql_connect('10.5.5.42', 'accesstorms', 'slamdunk');
			if (!$link) {
				die('Something went wrong while connecting to MSSQL');
			}else
			{
				$query = mssql_query('SELECT TOP 10 * FROM RMS.dbo.calldetails WITH(NOLOCK) WHERE assignedteam = \'SEC\' ');

				// Check if there were any records
				if (!mssql_num_rows($query)) {
					echo 'No records found';
				} else {
					$output .=  "<TABLE id=\"rmsobjects\">\n";
					$output .=  "<TR><TH class=\"ObjectHWType\">callno</TH><TH class=\"ObjectName\">description</TH><TH class=\"ObjectLabel\">statuscode</TH></TR>\n";
					$class = "even";
					while ($row = mssql_fetch_array($query,  MYSQL_ASSOC)) {
						$class = ($class=='even') ? 'odd' : 'even';
						
						$output .=  "<TR class=\"$class\">";
						$output .=  "<TD class=\"ObjectHWType\">".$row["callno"]."</TD>";
						$output .=  "<TD class=\"ObjectName\">".$row["description"]."</a></TD>";
						$output .=  "<TD class=\"ObjectLabel\">".$row["statuscode"]."</TD>";
						$output .=  "</TR>\n";
					}
					$output .=  "</TABLE>\n";
				}

				// Free the query result
				mssql_free_result($query);
			}
			mssql_close($link);
			echo $output;
		}
		else
		{
			echo "Wont Actually Connect as we would need clearance to be 'connected' to RMS";
		}
	}
	
	function rmsGetObjectsByType ($objtype_id) {
		$output = "";
		$objquery ="SELECT RackObject.* , Dictionary.dict_value as HWType
					FROM RackObject 
					LEFT OUTER JOIN AttributeValue 
					ON AttributeValue.object_id = RackObject.id 
					AND attr_id = 2 
					LEFT OUTER JOIN Dictionary 
					ON AttributeValue.uint_value =  Dictionary.dict_key	 
					WHERE`objtype_id` =$objtype_id
					ORDER  BY Dictionary.dict_value";
					
					

		$objresult = useSelectBlade ($objquery, __FUNCTION__);
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
