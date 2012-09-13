<?php
$tab['location']['map'] = 'Map';
registerTabHandler ('location', 'map', 'getMAPContent');

function getMAPContent($location_id)
{
$db = mysql_connect('localhost', 'trunk', 'bill10gates');
mysql_select_db('trunk',$db);
$body = MakeImageMap($location_id,$db);
$script = DrawCanvas($location_id,$db);

$HTMLOutput="";

$HTMLOutput.="
<!--[if lte IE 8]>
    <link rel=\"stylesheet\"  href=\"css/ie.css\" type=\"text/css\">
    
<script type=\"text/javascript\">
	function uselessie(){
		document.getElementById(\'mapCanvas\').className = \"mapCanvasiefix\";
	}
</script>
<style type=\"text/css\">
.mapCanvasiefix {
	    width: 800px !important;
}
</style>    <script src=\"scripts/excanvas.js\"></script>
  <![endif]-->
  <link rel=\"stylesheet\" href=\"css/imagemap.css\" type=\"text/css\">
  
  <script type=\"text/javascript\" src=\"scripts/jquery.min.js\"></script>
";

$HTMLOutput.=$script;

$HTMLOutput.="
</head>
<body onload=\"loadCanvas(),uselessie()\">
";

$HTMLOutput.=$body;


$HTMLOutput.="
</body>
</html>
";


print $HTMLOutput;
}


function MakeImageMap( $location_id, $db) {
	$coords_attr_id = 10007;
	$mi_attr_id = 10009;
        $attrs = getAttrValues ($location_id);
        $mapimage = @$attrs[$mi_attr_id]['value'];

	 $mapHTML = "";
	 $mapfile = "drawings/$mapimage";
	 if ( strlen( $mapimage) > 0 ) {
	   
	   if ( file_exists( $mapfile ) ) {
	     list($width, $height, $type, $attr)=getimagesize($mapfile);
	     $mapHTML.="<div class=\"canvas\">\n";
		 $mapHTML.="<img src=\"drawings/blank.gif\" usemap=\"#datacenter\" width=\"$width\" height=\"$height\" alt=\"clearmap over canvas\">\n";
	     $mapHTML.="<map name=\"datacenter\">\n";


	//Get All Racks 
	foreach (getRows ($location_id) as $row_id => $rowInfo)
	{
		foreach (getRacks ($row_id) as $rack_id => $rackInfo)
		{
			$attrs = getAttrValues ($rack_id);
			$coords = @$attrs[$coords_attr_id]['value'];
			if (@$attrs[$mi_attr_id]['value']==$mapimage && $coords)					
			{
			$mapHTML.="<area href=\"/index.php?rack_id=". $rack_id. "&page=rack&tab=default \" shape=\"rect\" coords=\"" . $coords  . "\" alt=\"".$rackInfo["name"]."\" title=\"".$rackInfo["name"]."\">\n";
			}
		}
	}


	     
	     $mapHTML.="</map>\n";
	     $mapHTML.="<canvas id=\"mapCanvas\" width=\"$width\" height=\"$height\"></canvas>\n";
             
	     
	     $mapHTML .= "</div>\n";
	    }
	 }
	 return $mapHTML;
	}


function DrawCanvas( $location_id, $db){
		$script="";	
		// check to see if map was set
		$coords_attr_id = 10007;
		$mi_attr_id = 10009;
		$weight_attr_id = 10010;
		$mw_attr_id = 10011;
        	$attrs = getAttrValues ($location_id);
		$mapimage = @$attrs[$mi_attr_id]['value'];
	        $mapfile = "drawings/$mapimage";

		if(strlen($mapimage)){

			// map was set in config check to ensure a file exists before we attempt to use it
			if(file_exists($mapfile)){
				//$this->dcconfig=new Config($db);
				//$dev=new Device();
				//$templ=new DeviceTemplate();
				//$cab=new Cabinet();
				
				// get all color codes and limits for use with loop below
				$CriticalColor=html2rgb("#CC0000");
				$CautionColor=html2rgb("#CCCC00");
				$GoodColor=html2rgb("#00AA00");
				$SpaceRed=intval(80);
				$SpaceYellow=intval(60);
				$WeightRed=intval(80);
				$WeightYellow=intval(60);
				$PowerRed=intval(80);
				$PowerYellow=intval(60);
				
				$script.="  <script type=\"text/javascript\">\n	function loadCanvas(){\n";
				$space="	function space(){\n";
				$weight="	function weight(){\n";
				$power="	function power(){\n";
				$space.="	
					var mycanvas=document.getElementById(\"mapCanvas\");\n		
					var width = mycanvas.width;\n		
					mycanvas.width = width + 1;\n		
					width = mycanvas.width;\n		
					mycanvas.width = width - 1;\n		
					var context=mycanvas.getContext('2d');\n
					";

				$weight.="	
					var mycanvas=document.getElementById(\"mapCanvas\");\n		
					var width = mycanvas.width;\n		
					mycanvas.width = width + 1;\n		
					width = mycanvas.width;\n		
					mycanvas.width = width - 1;\n		
					var context=mycanvas.getContext('2d');\n
					";

				$power.="	
					var mycanvas=document.getElementById(\"mapCanvas\");\n		
					var width = mycanvas.width;\n		
					mycanvas.width = width + 1;\n		
					width = mycanvas.width;\n		
					mycanvas.width = width - 1;\n		
					var context=mycanvas.getContext('2d');\n
					";

				$script.="	
					var mycanvas=document.getElementById(\"mapCanvas\");\n		
					var width = mycanvas.width;\n		
					mycanvas.width = width + 1;\n		
					width = mycanvas.width;\n		
					mycanvas.width = width - 1;\n		
					var context=mycanvas.getContext('2d');\n
					";
				
				// get image file attributes and type
				list($width, $height, $type, $attr)=getimagesize($mapfile);
				$script.="
					context.globalCompositeOperation = 'destination-over';\n		
					var img=new Image();\n		
					img.onload=function(){\n			
					context.drawImage(img,0,0);\n		
					}\n		
					img.src=\"$mapfile\";\n
					";

				$space.="		
					context.globalCompositeOperation = 'destination-over';\n		
					var img=new Image();\n		
					img.onload=function(){\n			
						context.drawImage(img,0,0);\n		
					}\n		
					img.src=\"$mapfile\";\n
					";

				$weight.="		
					context.globalCompositeOperation = 'destination-over';\n		
					var img=new Image();\n		
					img.onload=function(){\n			
						context.drawImage(img,0,0);\n		
					}\n		
					img.src=\"$mapfile\";\n
					";

				$power.="		
					context.globalCompositeOperation = 'destination-over';\n		
					var img=new Image();\n		
					img.onload=function(){\n			
						context.drawImage(img,0,0);\n		
					}\n		
					img.src=\"$mapfile\";\n
					";


				
				// read all Racks and draw image map
				foreach (getRows ($location_id) as $row_id => $rowInfo)
				{
					foreach (getRacks ($row_id) as $rack_id => $rackInfo)
					{
					$rack_attrs = getAttrValues ($rack_id);
					$coords = @$rack_attrs[$coords_attr_id]['value'];
 

					if (@$attrs[$mi_attr_id]['value']==$mapimage && $coords)					
					{
					


					$totalWatts = $totalWeight = $totalMoment =0;

					/*
					$cab->CabinetID=$cabRow["CabinetID"];
					$cab->GetCabinet($db);
		        		$dev->Cabinet=$cab->CabinetID;
    			    		$devList=$dev->ViewDevicesByCabinet( $db );
					$currentHeight = $cab->CabinetHeight;
        				
					
					while(list($devID,$device)=each($devList)){
        	        			$templ->TemplateID=$device->TemplateID;
			            	    	$templ->GetTemplateByID($db);

						if($device->NominalWatts >0){
							$totalWatts += $device->NominalWatts;
						}else{
							$totalWatts += $templ->Wattage;
						}
              
		  				$totalWeight+=$templ->Weight;
			                	$totalMoment+=($templ->Weight *($device->Position +($device->Height /2)));
					}
					$CenterofGravity=@round($totalMoment /$totalWeight);
*/

/*
	 //Calculate Weight of Rack and All Objects Within It
        $result = usePreparedSelectBlade
        (
                'SELECT DISTINCT object_id ' .
                'FROM RackSpace ' .
                'WHERE rack_id = ?',
                array($rack_id)
        );
        while ($object = $result->fetch (PDO::FETCH_ASSOC))
	 {
				$obj_attrs = getAttrValues ($object['object_id']);
				$totalWeight+=@$obj_attrs[$weight_attr_id]['value'];

	 }


*/

					$rackData = spotEntity ('rack', $rack_id);
					  amplifyCell ($rackData);



foreach ($rackData['mountedObjects'] as $object_id)
{
				$obj_attrs = getAttrValues ($object_id);
				$totalWeight+=@$obj_attrs[$weight_attr_id]['value'];
}
$totalWeight+=@$rack_attrs[$weight_attr_id]['value'];
$MaxWeight = @$rack_attrs[$mw_attr_id]['value'];





					$SpaceUsed = getRSUforRack ($rackData);
					
					$SpacePercent=number_format($SpaceUsed * 100,0);


					// check to make sure there is a weight limit set to keep errors out of logs
					if(!isset($MaxWeight)||$MaxWeight==0){$WeightPercent=0;}else{$WeightPercent=number_format($totalWeight /$MaxWeight *100,0);}

/*
					// check to make sure there is a kilowatt limit set to keep errors out of logs
		    	    		if(!isset($cab->MaxKW)||$cab->MaxKW==0){$PowerPercent=0;}else{$PowerPercent=number_format(($totalWatts /1000 ) /$cab->MaxKW *100,0);}

*/					
					

					$PowerPercent = rand(0,0);

					//Decide which color to paint on the canvas depending on the thresholds
					if($SpacePercent>$SpaceRed){$scolor=$CriticalColor;}elseif($SpacePercent>$SpaceYellow){$scolor=$CautionColor;}else{$scolor=$GoodColor;}
					if($WeightPercent>$WeightRed){$wcolor=$CriticalColor;}elseif($WeightPercent>$WeightYellow){$wcolor=$CautionColor;}else{$wcolor=$GoodColor;}
					if($PowerPercent>$PowerRed){$pcolor=$CriticalColor;}elseif($PowerPercent>$PowerYellow){$pcolor=$CautionColor;}else{$pcolor=$GoodColor;}
					if($SpacePercent>$SpaceRed || $WeightPercent>$WeightRed || $PowerPercent>$PowerRed){$color=$CriticalColor;}
					elseif($SpacePercent>$SpaceYellow || $WeightPercent>$WeightYellow || $PowerPercent>$PowerYellow){$color=$CautionColor;}else{$color=$GoodColor;}

					$MapX1=substr($coords,0,3);
					$MapY1=substr($coords,5,3);
					$MapX2=substr($coords,10,3);
					$MapY2=substr($coords,15,3);

					$width=$MapX2-$MapX1;
					$height=$MapY2-$MapY1;

					
					
					$script.="		context.fillStyle=\"rgba(".$color[0].", ".$color[1].", ".$color[2].", 0.35)\";\n		context.fillRect($MapX1,$MapY1,$width,$height);\n";
					$space.="		context.fillStyle=\"rgba(".$scolor[0].", ".$scolor[1].", ".$scolor[2].", 0.35)\";\n		context.fillRect($MapX1,$MapY1,$width,$height);\n";
					$weight.="		context.fillStyle=\"rgba(".$wcolor[0].", ".$wcolor[1].", ".$wcolor[2].", 0.35)\";\n		context.fillRect($MapX1,$MapY1,$width,$height);\n";
					$power.="		context.fillStyle=\"rgba(".$pcolor[0].", ".$pcolor[1].", ".$pcolor[2].", 0.35)\";\n		context.fillRect($MapX1,$MapY1,$width,$height);\n";
					}	
					}
				}
			}
			$space.="	}\n";
			$weight.="	}\n";
			$power.="	}\n";
			$script.="	}\n";
			$script.=$space.$weight.$power;
			$script.="	</script>\n";
		}
		return $script;
	}


function html2rgb($color)
{
    if ($color[0] == '#')
        $color = substr($color, 1);

    if (strlen($color) == 6)
        list($r, $g, $b) = array($color[0].$color[1],
                                 $color[2].$color[3],
                                 $color[4].$color[5]);
    elseif (strlen($color) == 3)
        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
    else
        return false;

    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

    return array($r, $g, $b);
}
?>
