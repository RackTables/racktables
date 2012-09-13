<?php

// RMS Ripe Database Update
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

$tab['depot']['parts'] = 'Parts Control';
//$tab['depot']['addparts'] = 'Add Parts';

// What Function should render the tab content
$tabhandler['depot']['parts'] = array('RMS_PARTS', 'getContent');

//$tabhandler['depot']['addparts'] = array('RMS_PARTS', 'getContent');

// Which Should we Call to handle post back
$ophandler['depot']['parts']['HandlePartsForm'] = array('RMS_PARTS', 'HandlePartsForm' );


class RMS_PARTS
{
	static public function getContent(){
		$obj = new RMS_PARTS();
		print $obj->Render();
		//$obj->getStatusForm();
		//echo $obj->getStatusHistory(0);
	}
	
	function updatePartTypeInfo() {
		foreach($_POST["Part_Type_ID"] as $Part_Type_ID ) {
			# Item Details	

			$Title = $_POST["Title"][$Part_Type_ID];
			$Description = $_POST["Description"][$Part_Type_ID];
			$Part_Cat_ID = $_POST["Part_Cat_ID"][$Part_Type_ID];
			$Part_Brand_ID = $_POST["Part_Brand_ID"][$Part_Type_ID];
			$PartNum = $_POST["PartNum"][$Part_Type_ID];

			if ($Title != '')
			{
				if ($Part_Type_ID == 0) 
				{
					$sqlCommand ="INSERT INTO `rms_Parts_Types`
								(Title,Description,Part_Cat_ID,Part_Brand_ID,PartNum) 
								VALUES 
								('$Title','$Description','$Part_Cat_ID','$Part_Brand_ID','$PartNum')";
				}
				else
				{
					$sqlCommand ="UPDATE `rms_Parts_Types`
									SET Title = '$Title',
									Description = '$Description',
									Part_Cat_ID = '$Part_Cat_ID',
									Part_Brand_ID = '$Part_Brand_ID',
									PartNum = '$PartNum'
									WHERE Part_Type_ID = $Part_Type_ID";
				}					
				$objresult = usePreparedSelectBlade ($sqlCommand);
			}
		
		}
	
	}
	
	function updatePartInfo() {
	
	
		foreach($_POST["Part_Item_ID"] as $Part_Item_ID ) {
			# Item Details	
			$Part_Type_ID = $_POST["Part_Type_ID"][$Part_Item_ID];
			$Added = $_POST["Added"][$Part_Item_ID];
			$Ordered = $_POST["Ordered"][$Part_Item_ID];
			$Recieved = $_POST["Recieved"][$Part_Item_ID];
			$WarExp = $_POST["WarExp"][$Part_Item_ID];
			$Manuf = $_POST["Manuf"][$Part_Item_ID];
			$Serial = $_POST["Serial"][$Part_Item_ID];
			$Cost = $_POST["Cost"][$Part_Item_ID];
			
			# Status Details
			$Obj_ID = $_POST["Obj_ID"][$Part_Item_ID];
			$Part_Status_Type_ID = $_POST["Part_Status_Type_ID"][$Part_Item_ID];
			
			
			 $objItemUpdate ="UPDATE `rms_Parts_Items`
							SET Part_Type_ID = $Part_Type_ID,
							Added = '$Added',
							Ordered = '$Ordered',
							Recieved = '$Recieved',
							WarExp = '$WarExp',
							Manuf = '$Manuf',
							Serial = '$Serial',
							Cost = '$Cost'
							WHERE Part_Item_ID = $Part_Item_ID";
								
								
			$objStatusUpdate =" UPDATE `rms_Parts_Status` SET Current = 0 WHERE 
								Part_Item_ID = $Part_Item_ID";
								
			$objStatusInsert ="INSERT INTO  `rms_Parts_Status` 
								(Part_Item_ID,Obj_ID,Part_Status_Type_ID,Current,Updated)
								VALUES
								($Part_Item_ID,$Obj_ID,$Part_Status_Type_ID,1,now())
								"
								;
			//print   $objItemUpdate;
			$objresult = usePreparedSelectBlade ($objItemUpdate);
			//print   $objStatusUpdate;
			$objresult = usePreparedSelectBlade ($objStatusUpdate);	
			//print   $objStatusInsert;
			$objresult = usePreparedSelectBlade ($objStatusInsert);	
		
		}
	
	}
	
	function addPartInfo() {
	
	
		foreach($_POST["New_Item_ID"] as $NewItemID ) {
			# Item Details	
			$Part_Type_ID = $_POST["Part_Type_ID"][$NewItemID];
			$Added = $_POST["Added"][$NewItemID];
			$Ordered = $_POST["Ordered"][$NewItemID];
			$Recieved = $_POST["Recieved"][$NewItemID];
			$WarExp = $_POST["WarExp"][$NewItemID];
			$Manuf = $_POST["Manuf"][$NewItemID];
			$Serial = $_POST["Serial"][$NewItemID];
			$Cost = $_POST["Cost"][$NewItemID];
			
			# Status Details
			$Obj_ID = $_POST["Obj_ID"][$NewItemID];
			$Part_Status_Type_ID = $_POST["Part_Status_Type_ID"][$NewItemID];
			
			if ($Part_Status_Type_ID > 0 ){
			
			 $objItemInsert = "INSERT INTO `rms_Parts_Items`
							(Part_Type_ID,Added,Ordered,Recieved,WarExp,Manuf,Serial,Cost)
							VALUES
							($Part_Type_ID,'$Added','$Ordered','$Recieved','$WarExp','$Manuf','$Serial','$Cost')
							" ;
			
			
			//print   $objItemInsert;
			$objresult = usePreparedSelectBlade ($objItemInsert);
			
			$InsertID = lastInsertID();
			$objStatusInsert = "INSERT INTO  `rms_Parts_Status` 
								(Part_Item_ID,Obj_ID,Part_Status_Type_ID,Current,Updated)
								VALUES
								($InsertID,$Obj_ID,$Part_Status_Type_ID,1,now())
								"
								;
			
			//print $objStatusInsert;
			
			$objresult = usePreparedSelectBlade ($objStatusInsert);	
			}
		}
	
	}

	function updateBrand() {
		foreach($_POST["Part_Brand_ID"] as $Part_Brand_ID ) {
			
			$Brand = $_POST["Brand"][$Part_Brand_ID];
			$Status = $_POST["Status"][$Part_Brand_ID];
			if ($Status != 1) $Status = 0;
			
			if ($Brand != '')
			{
				if ($Part_Brand_ID == 0) 
				{
					$sqlCommand = 	"INSERT INTO `rms_Parts_Brands`
									(Brand,Status) 
									VALUES ('$Brand',$Status)";
				}
				else
				{
					$sqlCommand =	"UPDATE `rms_Parts_Brands`
									SET Brand = '$Brand',
									Status = $Status
									WHERE Part_Brand_ID = $Part_Brand_ID";
				}					
				$objresult = usePreparedSelectBlade ($sqlCommand);
			}
		
		}
	}
	


	function updateCategory() {

		foreach($_POST["Part_Cat_ID"] as $Part_Cat_ID ) {
			
			$Category = $_POST["Category"][$Part_Cat_ID];
			$Status = $_POST["Status"][$Part_Cat_ID];
			if ($Status != 1) $Status = 0;

			if ($Category != '')
			{
				if ($Part_Cat_ID == 0) 
				{
					$sqlCommand = 	"INSERT INTO `rms_Parts_Category`
									(Category,Status) 
									VALUES ('$Category',$Status)";
				}
				else
				{
					$sqlCommand =	"UPDATE `rms_Parts_Category`
									SET Category = '$Category',
									Status = $Status
									WHERE Part_Cat_ID = $Part_Cat_ID";
				}					
				$objresult = usePreparedSelectBlade ($sqlCommand);
			}
		
		}
	}
	
	
	
	function Render() {
		$output = "";
		$output .=   "<link rel=stylesheet type='text/css' href=/plugins/style.css />";
		$output .=   "<div id=\"rms_addonWrapper\">";
		$output .=  $this->getPartsNavigation();
		$PostAction = '';
		
		if (isset($_POST["Action"])) {
			$PostAction = $_POST["Action"];
		}
			switch ($PostAction){
					case "Update":
						$this->updatePartInfo();
						break;
					case "Add":
						$this->addPartInfo();
						break;
					case "Update Brand":
						$this->updateBrand();
						break;
					case "Update Category":
						$this->updateCategory();
						break;
					case "Update Part Type":
						$this->updatePartTypeInfo();
						break;				
					
			}
			
		$Action = '';
		if (isset($_GET["Action"])) {
			$Action = $_GET["Action"];
		}
		switch ($Action){
				case "Add":
					$output .= $this->getPartAddForm($_GET["Part_Type_ID"]);
					break;
				case "Manage":
					$output .= $this->getPartUpdateForm($_GET["Part_Type_ID"]);
					break;
				case "UpdateBrands":
					$output .= $this->getPartBrandForm();
					break;
				case "UpdateCategories":
					$output .= $this->getPartCategoryForm();
					break;
				case "UpdatePartTypes":
					$output .= $this->getPartTypeForm();
					break;
				case "Detail":
					$output .= $this->getPartForm($_GET["Part_Item_ID"]);
					break;	
				case "Update":
					$output .= $this->getPartForm($_GET["Part_Item_ID"]);
					break;	
				default:
					$output .= $this->getPartsList();
					break;
		}
		
		$output .=   "</div>";	
		return $output;
	}
	
	function getPartsNavigation() {
		$output = "";
		$output .=  "<TABLE id=partnav >";
		$output .=  "<tr>";
		$output .=  "<td><a href='/index.php?page=depot&tab=parts&Action=PartsList'>PartsList</a></td>";
		$output .=  "<td><a href='/index.php?page=depot&tab=parts&Action=UpdateBrands'>Update Brands</a></td>";
		$output .=  "<td><a href='/index.php?page=depot&tab=parts&Action=UpdateCategories'>Update Categories</a></td>";
		$output .=  "<td><a href='/index.php?page=depot&tab=parts&Action=UpdatePartTypes'>Update Part Types</a></td>";
		$output .=  "</tr>";
		$output .=  "</TABLE>";
		return $output;
	}
	
	function getPartUpdateForm($Part_Type_ID) {
		$output = "";
		$objquery ="SELECT `rms_Parts_Items`.Part_Item_ID,Part_Type_ID,Added,Ordered,Recieved,WarExp,Manuf,Serial,Cost,Obj_ID,Part_Status_Type_ID
						FROM `rms_Parts_Items`
						LEFT OUTER JOIN `rms_Parts_Status`
						ON `rms_Parts_Status`.Part_Item_ID = `rms_Parts_Items`.Part_Item_ID AND `rms_Parts_Status`.Current
						WHERE Part_Type_ID = $Part_Type_ID
						";

		$objresult = usePreparedSelectBlade ($objquery);
		$objresult = $objresult->fetchall();
		if (count($objresult) > 0 )
		{
			$output .=  "<form method=post action=''>";
			$output .=  "<TABLE id=partupdateform >";
			$output .=  "<TR>";
			$output .=  "<TH>Part Type</TH>";
			$output .=  "<TH>Status</TH>";
			$output .=  "<TH>Location</TH>";
			$output .=  "<TH>Ordered<br/>Date<br/>YYYY-MM-DD</TH>";
			$output .=  "<TH>Recieved<br/>Date<br/>YYYY-MM-DD</TH>";
			$output .=  "<TH>Warranty<br/>Date<br/>YYYY-MM-DD</TH>";
			//$output .=  "<TH>Manufacture</TH>";
			$output .=  "<TH>Serial<br/>Number</TH>";
			$output .=  "<TH>Cost</TH>";
			$output .=  "<TH>Actions</TH>";
			$output .=  "</TR>";
			
			foreach ($objresult as $object)
			{
			
			$i = $object['Part_Item_ID'];
				$output .=  "<TR>";
				$output .=  "<input type=hidden name='Part_Item_ID[$i]' value='$i' />";
				$output .=  "<TD class=Part>".$this->getPartTypeDropDown($i,$object['Part_Type_ID'])."</TD>";
				$output .=  "<TD class=Status>".$this->getStatusDropDown($i,$object['Part_Status_Type_ID'])."</TD>";
				$output .=  "<TD class=Object>".$this->getObjectDropDown($i,$object['Obj_ID'])."</TD>";
				$output .=  "<TD class=Ordered><input size=8 type=text name='Ordered[$i]' value='".$object['Ordered']."' /></TD>";
				$output .=  "<TD class=Recieved><input size=8 type=text name='Recieved[$i]' value='".$object['Recieved']."' /></TD>";
				$output .=  "<TD class=WarExp><input size=8 type=text name='WarExp[$i]' value='".$object['WarExp']."' /></TD>";
				//$output .=  "<TD class=Manuf><input size=8 type=text name='Manuf[$i]' value='".$object['Manuf']."' /></TD>";
				$output .=  "<TD class=Serial><input size=12 type=text name='Serial[$i]' value='".$object['Serial']."' /></TD>";
				$output .=  "<TD class=Cost><input size=6 type=text name='Cost[$i]' value='".$object['Cost']."' /></TD>";
				$output .=  "<TD class=manage><a href='/index.php?page=depot&tab=parts&Part_Item_ID=$i&Action=Detail'>View Details</a></TD>";
				$output .=  "</TR>";
				$i ++;
			}
			$output .=  "</TABLE>";
			$output .=  "<input type=submit name='Action' value='Update'>";
			$output .=  "</form>";
		}
		else
		{
			$output .=  "No Parts of this type are available. Please <a href='/index.php?page=depot&tab=parts&Part_Type_ID=$Part_Type_ID&Action=Add'>Add</a> some";
		}
		return $output;				
						
	}
	
	function getPartBrandForm() {
			$output = "";
			$objquery ="SELECT `rms_Parts_Brands`.Part_Brand_ID,`rms_Parts_Brands`.Brand,`rms_Parts_Brands`.Status 
			FROM `rms_Parts_Brands` 
			";



			$objresult = usePreparedSelectBlade ($objquery);
			$objresult = $objresult->fetchall();
			if (count($objresult) > 0 )
			{
				$output .=  "<form method=post action='/index.php?page=depot&tab=parts'>";	
				$output .=  "<TABLE id=brands>";
				$output .=  "<TR>";
				$output .=  "<TH>ID</TH>";
				$output .=  "<TH>Brand</TH>";
				$output .=  "<TH>Status</TH>";
				$output .=  "</TR>";
				
				foreach ($objresult as $object)
				{
						$FieldID = $object['Part_Brand_ID'];
						$Brand = $object['Brand'];
						if ($object['Status'] == 1 )
						$STATUS = "CHECKED";
						else
						$STATUS = "";
						
										
					$output .=  "<TR>";
					$output .=  "<TD class=ID>$FieldID</TD>\n";
					$output .=  "<TD class=Brand><input type='TEXT' SIZE=50 name='Brand[$FieldID]' value='$Brand' ></TD>";
					$output .=  "<TD class=Status><INPUT TYPE='CHECKBOX' NAME='Status[$FieldID]' value=1 $STATUS></TD>";
					$output .=  "<input type='HIDDEN' name='Part_Brand_ID[$FieldID]' value='$FieldID' ></TD>";
					$output .=  "</TR>";
					
					
				}
					$FieldID = 0;
					$output .=  "<TR>";
					$output .=  "<TD class=ID>Add New</TD>\n";
					$output .=  "<TD class=Brand><input type='TEXT' SIZE=50 name='Brand[$FieldID]' value='' ></TD>";
					$output .=  "<TD class=Status><INPUT TYPE='CHECKBOX' NAME='Status[$FieldID]' value=1 CHECKED></TD>";
					$output .=  "<input type='HIDDEN' name='Part_Brand_ID[$FieldID]' value='$FieldID' ></TD>";
					$output .=  "</TR>";
					
				$output .=  "</TABLE>";
				$output .=  "<input type='submit' name='Action' Value='Update Brand'>";
				
				$output .=  "</form>";
			}
			return $output;
	}

	function getPartCategoryForm() {
			$output = "";
			$objquery ="SELECT `rms_Parts_Category`.Part_Cat_ID,`rms_Parts_Category`.Category,`rms_Parts_Category`.Status 
			FROM `rms_Parts_Category` 
			";



			$objresult = usePreparedSelectBlade ($objquery);
			$objresult = $objresult->fetchall();
			if (count($objresult) > 0 )
			{
				$output .=  "<form method=post action='/index.php?page=depot&tab=parts'>";				
				$output .=  "<TABLE id=Category>";
				$output .=  "<TR>";
				$output .=  "<TH>ID</TH>";
				$output .=  "<TH>Category</TH>";
				$output .=  "<TH>Status</TH>";
				$output .=  "</TR>";
				
				foreach ($objresult as $object)
				{
						$FieldID = $object['Part_Cat_ID'];
						$value = $object['Category'];
						if ($object['Status'] == 1 )
						$STATUS = "CHECKED";
						else
						$STATUS = "";
						
										
					$output .=  "<TR>";
					$output .=  "<TD class=ID>$FieldID</TD>\n";
					$output .=  "<TD class=Category><input type='TEXT' SIZE=50 name='Category[$FieldID]' value='$value' ></TD>";
					$output .=  "<TD class=Status><INPUT TYPE='CHECKBOX' NAME='Status[$FieldID]' value=1 $STATUS></TD>";
					$output .=  "<input type='HIDDEN' name='Part_Cat_ID[$FieldID]' value='$FieldID' ></TD>";
					$output .=  "</TR>";
					
					
				}
				$FieldID = 0;
				$output .=  "<TR>";
				$output .=  "<TD class=ID>Add New</TD>\n";
				$output .=  "<TD class=Category><input type='TEXT' SIZE=50 name='Category[$FieldID]' value='' ></TD>";
				$output .=  "<TD class=Status><INPUT TYPE='CHECKBOX' NAME='Status[$FieldID]' value=1 CHECKED></TD>";
				$output .=  "<input type='HIDDEN' name='Part_Cat_ID[$FieldID]' value='$FieldID' ></TD>";
				$output .=  "</TR>";
					
				$output .=  "</TABLE>";
				$output .=  "<input type='submit' name='Action' Value='Update Category'>";
				$output .=  "</form>";
			}
			return $output;		
						
	}

	function getPartTypeForm() {
			$output = "";
			$objquery ="SELECT Part_Type_ID,Title,Description,Part_Cat_ID,Part_Brand_ID,PartNum 
			FROM `rms_Parts_Types` 
			";



			$objresult = usePreparedSelectBlade ($objquery);
			$objresult = $objresult->fetchall();
			if (count($objresult) > 0 )
			{
				$output .=  "<form method=post action='/index.php?page=depot&tab=parts'>";
				$output .=  "<TABLE id=PartType>";
				$output .=  "<TR>";
				$output .=  "<TH>ID</TH>";
				$output .=  "<TH>Title</TH>";
				$output .=  "<TH>Description</TH>";
				$output .=  "<TH>PartNum</TH>";
				$output .=  "<TH>Category</TH>";
				$output .=  "<TH>Brand</TH>";
				$output .=  "</TR>";
				
				foreach ($objresult as $object)
				{
						$FieldID = $object['Part_Type_ID'];
						$Title = $object['Title'];
						$Description = $object['Description'];
						$catid = $object['Part_Cat_ID'];
						$brandid = $object['Part_Brand_ID'];
						$PartNum = $object['PartNum'];
						
						
						
					$output .=  "<TR>\n";
					$output .=  "<TD class=ID>$FieldID</TD>\n";
					$output .=  "<TD class=Title><input type='TEXT' SIZE=40 name='Title[$FieldID]' value='$Title' ></TD>\n";
					$output .=  "<TD class=Description><input type='TEXT' SIZE=40 name='Description[$FieldID]' value='$Description' ></TD>\n";
					$output .=  "<TD class=PartNum><input type='TEXT' SIZE=10 name='PartNum[$FieldID]' value='$PartNum' ></TD>\n";
					$output .=  "<TD class=Category>".$this->getPartCatDropDown($FieldID,$catid)."</TD>\n";
					$output .=  "<TD class=Brand>".$this->getPartBrandDropDown($FieldID,$brandid)."</TD>\n";
					$output .=  "<input type='HIDDEN' name='Part_Type_ID[$FieldID]' value='$FieldID' ></TD>\n";
					$output .=  "</TR>\n";
					
					
				}

					$FieldID = 0;
					$output .=  "<TR>\n";
					$output .=  "<TD class=ID>Add New</TD>\n";
					$output .=  "<TD class=Category><input type='TEXT' SIZE=40 name='Title[$FieldID]' value='' ></TD>\n";
					$output .=  "<TD class=Category><input type='TEXT' SIZE=40 name='Description[$FieldID]' value='' ></TD>\n";
					$output .=  "<TD class=Category><input type='TEXT' SIZE=10 name='PartNum[$FieldID]' value='' ></TD>\n";
					$output .=  "<TD class=Category>".$this->getPartCatDropDown($FieldID,0)."</TD>\n";
					$output .=  "<TD class=Category>".$this->getPartBrandDropDown($FieldID,0)."</TD>\n";
					$output .=  "<input type='HIDDEN' name='Part_Type_ID[$FieldID]' value='$FieldID' ></TD>\n";
					$output .=  "</TR>\n";
					
				$output .=  "</TABLE>";
				$output .=  "<input type=submit name='Action' value='Update Part Type'>";
				$output .=  "</form>";
			}
			return $output;		
						
	}

	function getPartAddForm() {

		$output .=  "<form method=post action=''>";
		$output .=  "<TABLE id=partaddform>";
		$output .=  "<TR>";
		$output .=  "<TH>Part Type</TH>";
		$output .=  "<TH>Status</TH>";
		$output .=  "<TH>Location</TH>";		
		$output .=  "<TH>Ordered<br/>Date<br/>YYYY-MM-DD</TH>";
		$output .=  "<TH>Recieved<br/>Date<br/>YYYY-MM-DD</TH>";
		$output .=  "<TH>Warranty<br/>Date<br/>YYYY-MM-DD</TH>";
		//$output .=  "<TH>Manufacture</TH>";
		$output .=  "<TH>Serial<br/>Number</TH>";
		$output .=  "<TH>Cost</TH>";
	
		$output .=  "</TR>";
		
		for ($i = 1; $i <= 10; $i++) {
			$output .=  "<TR>";
			$output .=  "<input type=hidden name='New_Item_ID[$i]' value='$i' />";
			$output .=  "<TD class=Part>".$this->getPartTypeDropDown($i,$_GET['Part_Type_ID'])."</TD>";
			$output .=  "<TD class=Status>".$this->getStatusDropDown($i,$_GET['Part_Status_Type_ID'])."</TD>";
			$output .=  "<TD class=Object>".$this->getObjectDropDown($i,$_GET['Obj_ID'])."</TD>";
			$output .=  "<TD class=Ordered><input size=8 type=text name='Ordered[$i]' value='' /></TD>";
			$output .=  "<TD class=Recieved><input size=8 type=text name='Recieved[$i]' value='' /></TD>";
			$output .=  "<TD class=WarExp><input size=8 type=text name='WarExp[$i]' value='' /></TD>";
			//$output .=  "<TD class=Manuf><input size=8 type=text name='Manuf[$i]' value='' /></TD>";
			$output .=  "<TD class=Serial><input size=12 type=text name='Serial[$i]' value='' /></TD>";
			$output .=  "<TD class=Cost><input size=6 type=text name='Cost[$i]' value='' /></TD>";

			$output .=  "</TR>";
			
		}
		$output .=  "</TABLE>";
		$output .=  "<input type=submit name='Action' value='Add'>";
		$output .=  "</form>";

		return $output;				
						
	}
				
	function getPartForm ($Part_Item_ID) {
		$objquery ="SELECT `rms_Parts_Items`.Part_Item_ID,Part_Type_ID,Added,Ordered,Recieved,WarExp,Manuf,Serial,Cost,Obj_ID,Part_Status_Type_ID
						FROM `rms_Parts_Items`
						LEFT OUTER JOIN `rms_Parts_Status`
						ON `rms_Parts_Status`.Part_Item_ID = `rms_Parts_Items`.Part_Item_ID AND `rms_Parts_Status`.Current
						WHERE `rms_Parts_Items`.Part_Item_ID = $Part_Item_ID
						LIMIT 0 , 30 ";

		$objresult = usePreparedSelectBlade ($objquery);
		$objresult = $objresult->fetchall();
		if (count($objresult) > 0 )
		{
			foreach ($objresult as $object)
			{
			$i = $object['Part_Item_ID'];
			$output = "";
			$output .=   "<div id=iteminfo>";
			$output .=   "<form method=post action=''>";
			$output .=   "<ol>
					
						
					
					<li>
						<label for=\"Part_Type_ID[$Part_Item_ID]\">Part Type:</label>
						".$this->getPartTypeDropDown($i,$object['Part_Type_ID'])."
					</li>
					<li>
						<label for=\"Ordered\">Ordered:</label>
						<input size=8 type=text name='Ordered[$i]' value='".$object['Ordered']."' />
					</li>
					<li>
						<label for=\"Recieved\">Recieved:</label>
						<input size=8 type=text name='Recieved[$i]' value='".$object['Recieved']."' />
					</li>
					<li>
						<label for=\"WarExp\">WarExp:</label>
						<input size=8 type=text name='WarExp[$i]' value='".$object['WarExp']."' />
					</li>
					<li>
						<label for=\"Manuf\">Manuf:</label>
						<input size=8 type=text name='Manuf[$i]' value='".$object['Manuf']."' />
					</li>
					<li>
						<label for=\"Serial\">Serial:</label>
						<input size=8 type=text name='Serial[$i]' value='".$object['Serial']."' />
					</li>
					<li>					
						<label for=\"Cost\">Cost:</label>
						<input size=8 type=text name='Cost[$i]' value='".$object['Cost']."' />
					</li>";
			$output .=  "<li>
						<label for=\"Status\">Status:</label>" 
						.$this->getStatusDropDown($i,$object['Part_Status_Type_ID']).
						"</li>";
			$output .=  "<li>		
						<label for=\"Location\">Location:</label>"
						.$this->getObjectDropDown($i,$object['Obj_ID']).
						"</li>";
			$output .=  "<li>		
						<input type='submit' name='Action' Value='Update'>
						</li>";
			$output .=  "<li>"		
						.$this->getStatusHistory($i).
						"</li>";
						
			$output .= 	"</ol>";
			$output .= 	"<input size=8 type=hidden name='Part_Item_ID[$i]' value='$i' />";
			$output .=   "</form>";
			$output .=   "</div>";	
			}
		}
			return $output;
	}
	
	function getPartsList () {
			$output = "";
			$objquery ="SELECT `rms_Parts_Types`.Part_Type_ID,Title,Description,Category,Brand, IFNULL(StockLevels.Qty,0) as Stock 
			FROM `rms_Parts_Types` 
			INNER JOIN `rms_Parts_Category` 
			ON `rms_Parts_Category`.Part_Cat_ID = `rms_Parts_Types`.Part_Cat_ID 
			INNER JOIN `rms_Parts_Brands` 
			ON `rms_Parts_Brands`.Part_Brand_ID = `rms_Parts_Types`.Part_Brand_ID 
			LEFT OUTER JOIN ( 
			
			SELECT count(*) as Qty,Part_Type_ID 
			FROM `rms_Parts_Items` INNER JOIN
			`rms_Parts_Status` 
			ON `rms_Parts_Status`.Part_Item_ID = `rms_Parts_Items`.Part_Item_ID AND Current = 1
			INNER JOIN `rms_Parts_Status_Types`
			ON `rms_Parts_Status_Types`.Part_Status_Type_ID = `rms_Parts_Status`.Part_Status_Type_ID
			AND  `rms_Parts_Status_Types`.Available = 1
			GROUP BY Part_Type_ID 
			)  StockLevels 
			ON StockLevels.Part_Type_ID = `rms_Parts_Types`.Part_Type_ID
			WHERE 1";



			$objresult = usePreparedSelectBlade ($objquery);
			$objresult = $objresult->fetchall();
			if (count($objresult) > 0 )
			{
				$output .=  "<TABLE id=partslist>";
				$output .=  "<TR>";
				$output .=  "<TH>Title</TH>";
				$output .=  "<TH>Description</TH>";
				$output .=  "<TH>Category</TH>";
				$output .=  "<TH>Brand</TH>";
				$output .=  "<TH>Available</TH>";
				$output .=  "<TH colspan=2>Stock Control</TH>";
				$output .=  "</TR>";
				
				foreach ($objresult as $object)
				{
					$output .=  "<TR>";
					$output .=  "<TD class=title>".$object['Title']."</TD>";
					$output .=  "<TD class=desc>".$object['Description']."</TD>";
					$output .=  "<TD class=cat>".$object['Category']."</TD>";
					$output .=  "<TD class=brand>".$object['Brand']."</TD>";
					$output .=  "<TD class=stock>".$object['Stock']."</TD>";
					$output .=  "<TD class=add><a href='/index.php?page=depot&tab=parts&Part_Type_ID=".$object['Part_Type_ID']."&Action=Add'>Add</a></TD>";
					$output .=  "<TD class=manage><a href='/index.php?page=depot&tab=parts&Part_Type_ID=".$object['Part_Type_ID']."&Action=Manage'>Manage</a></TD>";
					$output .=  "</TR>";
				}
				$output .=  "</TABLE>";
			}
			return $output;
	}
	
	function getStatusHistory ($Part_Item_ID) {
		$output = "";
		$objquery ="SELECT rms_Parts_Status.*, rms_Parts_Status_Types.Description,RackObject.*
					FROM rms_Parts_Status
					INNER JOIN rms_Parts_Status_Types
					ON rms_Parts_Status_Types.Part_Status_Type_ID = rms_Parts_Status.Part_Status_Type_ID
					LEFT OUTER JOIN RackObject 
					ON RackObject.id = rms_Parts_Status.Obj_ID
					WHERE Part_Item_ID = ?
					ORDER BY Updated DESC
					";
					
					

		$objresult = usePreparedSelectBlade ($objquery, array($Part_Item_ID));
		$objresult = $objresult->fetchall();
		$output .= "<div><h3>Part Status History</h3><div>";
		if (count($objresult) > 0 )
		{
		$output .=  "<TABLE id=\"statushistory\">\r\n";
		$output .=  "<TR>
		<TH class=\"Status\">Status</TH>
		<TH class=\"Updated\">Updated</TH>
		<TH class=\"Location\">Location</TH>
		<TH class=\"HWType\">HW Type</TH>
		<TH class=\"Label\">Device Label</TH>
		</TR>\r\n";
		$class = "even";
		foreach ($objresult as $object)
		{
		$class = ($class=='even') ? 'odd' : 'even';
			$output .=  "<TR class=\"$class\">";
			$output .=  "<TD>".$object['Description']."</TD>";
			$output .=  "<TD >".$object['Updated']."</TD>";
			$output .=  "<TD><a href=\"index.php?page=object&object_id=".$object['Obj_ID']. "\">".$object['name']."</a></TD>";
			$output .=  "<TD>".execGMarker(parseWikiLink($object['HWType'],'o'))."</TD>";
			$output .=  "<TD >".$object['label']."</TD>";
			
			
			
			$output .=  "</TR>";
		}
		$output .=  "</TABLE>";
		}
		return $output;
	}

	function getPartCatDropDown($FieldID,$Part_Cat_ID) {
		$output = "";
		$objquery ="SELECT Part_Cat_ID, Category
					FROM rms_Parts_Category
					ORDER BY Category ";
					
					

		$objresult = usePreparedSelectBlade ($objquery);
		$objresult = $objresult->fetchall();
		if (count($objresult) > 0 )
		{
		$output .=  "<SELECT Name=\"Part_Cat_ID[$FieldID]\" >";
		$output .=  "<OPTION value=\"0\">Please Select</OPTION>";
		foreach ($objresult as $object)
		{
			
		$output .=  "<OPTION value='".$object['Part_Cat_ID']."'";
		
		if ($Part_Cat_ID == $object['Part_Cat_ID']) 
		{
			$output .= " selected " ;
		}
		
		$output .=   ">".$object['Category']."</OPTION>";
		}
		$output .=  "</SELECT>";
		}
		return $output;
	}
	
	function getPartBrandDropDown($FieldID,$Part_Brand_ID) {
		$output = "";
		$objquery ="SELECT Part_Brand_ID, Brand
					FROM rms_Parts_Brands
					ORDER BY Brand ";
					
					

		$objresult = usePreparedSelectBlade ($objquery);
		$objresult = $objresult->fetchall();
		if (count($objresult) > 0 )
		{
		$output .=  "<SELECT Name=\"Part_Brand_ID[$FieldID]\" >";
		$output .=  "<OPTION value=\"0\">Please Select</OPTION>";
		foreach ($objresult as $object)
		{
			
		$output .=  "<OPTION value='".$object['Part_Brand_ID']."'";
		
		if ($Part_Brand_ID == $object['Part_Brand_ID']) 
		{
			$output .= " selected " ;
		}
		
		$output .=   ">".$object['Brand']."</OPTION>";
		}
		$output .=  "</SELECT>";
		}
		return $output;
	}

	function getPartTypeDropDown($FieldID,$Part_Type_ID) {
		$output = "";
		$objquery ="SELECT Part_Type_ID, Title
					FROM rms_Parts_Types 
					ORDER BY Title ";
					
					

		$objresult = usePreparedSelectBlade ($objquery);
		$objresult = $objresult->fetchall();
		if (count($objresult) > 0 )
		{
		$output .=  "<SELECT Name=\"Part_Type_ID[$FieldID]\" >";
		$output .=  "<OPTION value=\"0\">Please Select</OPTION>";
		foreach ($objresult as $object)
		{
			
		$output .=  "<OPTION value='".$object['Part_Type_ID']."'";
		
		if ($Part_Type_ID == $object['Part_Type_ID']) 
		{
			$output .= " selected " ;
		}
		
		$output .=   ">".$object['Title']."</OPTION>";
		}
		$output .=  "</SELECT>";
		}
		return $output;
	}
	
	function getStatusDropDown($FieldID,$Part_Status_Type_ID) {
		$output = "";
		$objquery ="SELECT Part_Status_Type_ID, Description
					FROM rms_Parts_Status_Types 
					WHERE Status = 1
					ORDER BY Description ";
					
					

		$objresult = usePreparedSelectBlade ($objquery);
		$objresult = $objresult->fetchall();
		if (count($objresult) > 0 )
		{
		$output .=  "<SELECT Name=\"Part_Status_Type_ID[$FieldID]\" >";
		$output .=  "<OPTION value=\"0\">Please Select</OPTION>";
		foreach ($objresult as $object)
		{
			$output .=  "<OPTION value='".$object['Part_Status_Type_ID']."'";
			if ($Part_Status_Type_ID == $object['Part_Status_Type_ID']) 
			{
				$output .= " selected " ;
			}
			$output .= ">".$object['Description']."</OPTION>";
		}
		$output .=  "</SELECT>";
		}
		return $output;
	}
	
	function getObjectDropDown($FieldID,$Obj_ID) {
		$output = "";
		$objquery ="SELECT id,name, label
					FROM RackObject 
					WHERE not name = ''
					AND objtype_id = 50018
					ORDER BY Name 
					";
		
		$objresult = usePreparedSelectBlade ($objquery);
		$results = $objresult->fetchall();
		
		$objquery2 ="(SELECT id,name, label
					FROM RackObject 
					WHERE not name = ''
					AND not objtype_id = 50018
					ORDER BY Name )
					";
		
		$objresult2 = usePreparedSelectBlade ($objquery2);
		$results2 = $objresult2->fetchall();
		
		$output .=  "<SELECT Name=\"Obj_ID[$FieldID]\" style=\"width: 200px\" >\r\n";
		$output .=  "<OPTION value=\"0\">Please Select</OPTION>";
		if (count($results) > 0 || count($results2) > 0 )
		{

			if (count($results) > 0 )
			{
				$output .=  "<optgroup label='Storage Locations'>";
				foreach ($results as $object)
				{
					$output .=  "<OPTION value='".$object['id']."'";
					if ($Obj_ID == $object['id']) 
					{
						$output .= " selected " ;
					}
					$output .=  ">".$object['name']."(".$object['label'].")</OPTION>\r\n";
				}
				$output .=  "</optgroup >";
			}
			
			
			if (count($results2) > 0 )
			{
				$output .=  "<optgroup label='Devices'>";
				foreach ($results2 as $object)
				{
					$output .=  "<OPTION value='".$object['id']."'";
					if ($Obj_ID == $object['id']) 
					{
						$output .= " selected " ;
					}
					$output .=  ">".$object['name']."(".$object['label'].")</OPTION>\r\n";
				}
				$output .=  "</optgroup>";
			}
			
		}
		$output .=  "</SELECT>\r\n";
		return $output;
	}
	
	function HandlePartsForm () {
		print "<br/>RESPONSE<br/>";
		print "<textarea cols=\"100\" rows=\"20\">";
		print $result; 
		print "</textarea>";
		return buildWideRedirectURL();
	}

	
	
}







?>
