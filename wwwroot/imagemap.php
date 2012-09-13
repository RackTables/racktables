<?php

$db = mysql_connect('localhost', 'trunk', 'bill10gates');
mysql_select_db('trunk',$db);
$body = MakeImageMap($db);
$script = DrawCanvas($db);

$HTMLOutput="";

$HTMLOutput.="
<html>
<head>
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
<style type=\"text/css\">div.center > div{width:810px;} div#mapadjust{width:1040px;} #mapadjust div.heading > div{width:701px;} #mapadjust div.heading > div + div{width:95px;}</style>
  
  <script type=\"text/javascript\" src=\"scripts/jquery.min.js\"></script>
";

$HTMLOutput.=$script;

$HTMLOutput.="
</head>
<body onload=\"loadCanvas(),uselessie()\">
<div class=\"main\">

";

$HTMLOutput.=$body;


$HTMLOutput.="
</div>
</body>
</html>
";


print $HTMLOutput;

function MakeImageMap( $db ) {
	 $mapHTML = "";
	 $mapfile = "drawings/1DN.png";
	 if ( strlen( $mapfile) > 0 ) {
	   $mapfile = "drawings/HDC.png";
	   
	   if ( file_exists( $mapfile ) ) {
	     list($width, $height, $type, $attr)=getimagesize($mapfile);
	     $mapHTML.="<div class=\"canvas\">\n";
		 $mapHTML.="<img src=\"drawings/blank.gif\" usemap=\"#datacenter\" width=\"$width\" height=\"$height\" alt=\"clearmap over canvas\">\n";
	     $mapHTML.="<map name=\"datacenter\">\n";
	     
	     $selectSQL="	SELECT * FROM `AttributeValue`
						INNER JOIN Object ON
						Object.id = `AttributeValue`.object_id
						WHERE `attr_id` = 10007";

		 $result = mysql_query( $selectSQL, $db );
	     if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}
	     while ( $cabRow = mysql_fetch_array( $result ) ) {
	       $mapHTML.="<area href=\"/index.php?rack_id=". $cabRow["object_id"]. "&page=rack&tab=default \" shape=\"rect\" coords=\"" . $cabRow["string_value"] . "\" alt=\"".$cabRow["name"]."\" title=\"".$cabRow["name"]."\">\n";
	     }
	     
	     $mapHTML.="</map>\n";
	     $mapHTML.="<canvas id=\"mapCanvas\" width=\"$width\" height=\"$height\"></canvas>\n";
             
	     
	     $mapHTML .= "</div>\n";
	    }
	 }
	 return $mapHTML;
	}


function DrawCanvas($db){
		$script="";	
		// check to see if map was set
		$mapfile = "drawings/1DN.png";
		if(strlen($mapfile)){

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
				$space.="	var mycanvas=document.getElementById(\"mapCanvas\");\n		var width = mycanvas.width;\n		mycanvas.width = width + 1;\n		width = mycanvas.width;\n		mycanvas.width = width - 1;\n		var context=mycanvas.getContext('2d');\n";
				$weight.="	var mycanvas=document.getElementById(\"mapCanvas\");\n		var width = mycanvas.width;\n		mycanvas.width = width + 1;\n		width = mycanvas.width;\n		mycanvas.width = width - 1;\n		var context=mycanvas.getContext('2d');\n";
				$power.="	var mycanvas=document.getElementById(\"mapCanvas\");\n		var width = mycanvas.width;\n		mycanvas.width = width + 1;\n		width = mycanvas.width;\n		mycanvas.width = width - 1;\n		var context=mycanvas.getContext('2d');\n";
				$script.="	var mycanvas=document.getElementById(\"mapCanvas\");\n		var width = mycanvas.width;\n		mycanvas.width = width + 1;\n		width = mycanvas.width;\n		mycanvas.width = width - 1;\n		var context=mycanvas.getContext('2d');\n";
				
				// get image file attributes and type
				list($width, $height, $type, $attr)=getimagesize($mapfile);
				$script.="		context.globalCompositeOperation = 'destination-over';\n		var img=new Image();\n		img.onload=function(){\n			context.drawImage(img,0,0);\n		}\n		img.src=\"$mapfile\";\n";
				$space.="		context.globalCompositeOperation = 'destination-over';\n		var img=new Image();\n		img.onload=function(){\n			context.drawImage(img,0,0);\n		}\n		img.src=\"$mapfile\";\n";
				$weight.="		context.globalCompositeOperation = 'destination-over';\n		var img=new Image();\n		img.onload=function(){\n			context.drawImage(img,0,0);\n		}\n		img.src=\"$mapfile\";\n";
				$power.="		context.globalCompositeOperation = 'destination-over';\n		var img=new Image();\n		img.onload=function(){\n			context.drawImage(img,0,0);\n		}\n		img.src=\"$mapfile\";\n";

			     $selectSQL="	SELECT * FROM `AttributeValue`
						INNER JOIN Object ON
						Object.id = `AttributeValue`.object_id
						WHERE `attr_id` = 10007";

				$result=mysql_query($selectSQL,$db);
				
				// read all cabinets and draw image map
				while($cabRow=mysql_fetch_array($result)){
					/*
					$cab->CabinetID=$cabRow["CabinetID"];
					$cab->GetCabinet($db);
		        		$dev->Cabinet=$cab->CabinetID;
    			    		$devList=$dev->ViewDevicesByCabinet( $db );
					$currentHeight = $cab->CabinetHeight;
        				$totalWatts = $totalWeight = $totalMoment =0;
        							
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

        				$used=$cab->CabinetOccupancy($cab->CabinetID,$db);
        				$SpacePercent=number_format($used /$cab->CabinetHeight *100,0);

					*/

/*
					// check to make sure there is a weight limit set to keep errors out of logs
					if(!isset($cab->MaxWeight)||$cab->MaxWeight==0){$WeightPercent=0;}else{$WeightPercent=number_format($totalWeight /$cab->MaxWeight *100,0);}
					// check to make sure there is a kilowatt limit set to keep errors out of logs
		    	    		if(!isset($cab->MaxKW)||$cab->MaxKW==0){$PowerPercent=0;}else{$PowerPercent=number_format(($totalWatts /1000 ) /$cab->MaxKW *100,0);}

*/					
					
$WeightPercent = rand(0,90);
$SpacePercent = rand(0,90);
$PowerPercent = rand(0,90);

					//Decide which color to paint on the canvas depending on the thresholds
					if($SpacePercent>$SpaceRed){$scolor=$CriticalColor;}elseif($SpacePercent>$SpaceYellow){$scolor=$CautionColor;}else{$scolor=$GoodColor;}
					if($WeightPercent>$WeightRed){$wcolor=$CriticalColor;}elseif($WeightPercent>$WeightYellow){$wcolor=$CautionColor;}else{$wcolor=$GoodColor;}
					if($PowerPercent>$PowerRed){$pcolor=$CriticalColor;}elseif($PowerPercent>$PowerYellow){$pcolor=$CautionColor;}else{$pcolor=$GoodColor;}
					if($SpacePercent>$SpaceRed || $WeightPercent>$WeightRed || $PowerPercent>$PowerRed){$color=$CriticalColor;}elseif($SpacePercent>$SpaceYellow || $WeightPercent>$WeightYellow || $PowerPercent>$PowerYellow){$color=$CautionColor;}else{$color=$GoodColor;}

					$MapX1=substr($cabRow["string_value"],0,3);
					$MapY1=substr($cabRow["string_value"],5,3);
					$MapX2=substr($cabRow["string_value"],10,3);
					$MapY2=substr($cabRow["string_value"],15,3);

					$width=$MapX2-$MapX1;
					$height=$MapY2-$MapY1;

					
					
					$script.="		context.fillStyle=\"rgba(".$color[0].", ".$color[1].", ".$color[2].", 0.35)\";\n		context.fillRect($MapX1,$MapY1,$width,$height);\n";
					$space.="		context.fillStyle=\"rgba(".$scolor[0].", ".$scolor[1].", ".$scolor[2].", 0.35)\";\n		context.fillRect($MapX1,$MapY1,$width,$height);\n";
					$weight.="		context.fillStyle=\"rgba(".$wcolor[0].", ".$wcolor[1].", ".$wcolor[2].", 0.35)\";\n		context.fillRect($MapX1,$MapY1,$width,$height);\n";
					$power.="		context.fillStyle=\"rgba(".$pcolor[0].", ".$pcolor[1].", ".$pcolor[2].", 0.35)\";\n		context.fillRect($MapX1,$MapY1,$width,$height);\n";
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
