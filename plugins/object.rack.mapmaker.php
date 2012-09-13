<?php



$tab['rack']['mapmaker'] = 'Map';
registerTabHandler ('rack', 'mapmaker', 'getMAPMaker');
function getMAPMaker($object_id)
{

	$coords_attr_id=10007;
	$mi_attr_id=10009;
	
        $attrs = getAttrValues ($object_id);
	$mapimage = @$attrs[$mi_attr_id]['value'];

	$height=0;
	$width=0;
	 
	$mapfile = "drawings/$mapimage";

	if(strlen($mapimage) >0){
		
		if(file_exists($mapfile)){
	 		list($width, $height, $type, $attr)=getimagesize($mapfile);
			// There is a bug in the excanvas shim that can set the width of the canvas to 10x the width of the image
			$ie8fix='
<script type="text/javascript">
	function uselessie(){
		document.getElementById(\'mapCanvas\').className = "mapCanvasiefix";
	}
</script>
<style type="text/css">
.main {
	    width: '.($width+42).'px !important;
}
</style>';
		}
	}
	$height+=60; //Offset for text on header
	$width+=10; //Don't remember why I need this

	// Base sizes for calculations
	// 206px for coordinate box
	// 580px for header 
	// 1030px for page
	if($width>800){
		$offset=($width-800);
		$screenadjustment="<style type=\"text/css\">div#mapadjust { width:".($offset+1030)."px;} .mapmaker > div { width:".($offset+580)."px;} .mapmaker div + div { width:206px;}</style>\n";
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>openDCIM Data Center Information Management</title>
  <link rel="stylesheet" href="css/imagemap.css" type="text/css">
  <link rel="stylesheet" href="css/imgareaselect-default.css" type="text/css">
  <script type="text/javascript" src="scripts/jquery.min.js"></script>
  <script type="text/javascript" src="scripts/jquery.imgareaselect.pack.js"></script>
  <!--[if lt IE 9]>
  <link rel="stylesheet"  href="css/ie.css" type="text/css">
    <?php if(isset($ie8fix)){echo $ie8fix;} ?>
  <![endif]-->
  <?php if(isset($screenadjustment)){echo $screenadjustment;} ?>
  
<script type="text/javascript">
function preview(img, selection) {
    if (!selection.width || !selection.height)
        return;
//    $('#x1').val(selection.x1);
//    $('#y1').val(selecti on.y1);
//    $('#x2').val(selection.x2);
//    $('#y2').val(selection.y2);
    $('#coords').val(selection.x1 + ', ' + selection.y1 + ', ' + selection.x2 + ', ' + selection.y2);
}
$(document).ready(function() {
	$('#map').imgAreaSelect( {
<?php
	$attrs = getAttrValues ($object_id);
	$coords = @$attrs[$coords_attr_id]['value'];
	
	$MapX1=substr($coords,0,3);
	$MapY1=substr($coords,5,3);
	$MapX2=substr($coords,10,3);
	$MapY2=substr($coords,15,3);

	printf( "x1: %d, x2: %d, y1: %d, y2: %d,\n", $MapX1, $MapX2, $MapY1, $MapY2 );

?>
		handles: true,
		onSelectChange: preview
	});
});
</script>
</head>
<body>
<div class="page" id="mapadjust">

<div class="main">
<div class="mapmaker">
<div>
<h3>Map Selector</h3>
</div>

	<div class="table">
        <div class="title">Coordinates</div> 
	<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
    <div class="table"> 
	<input type="hidden" name="cabinetid" value="<?php echo $CabinetID; ?>">
<!--
        <div> 
          <div><b>X<sub>1</sub>:</b></div> 
 		      <div><input type="text" name="x1" id="x1" value="<?php echo $MapX1; ?>"></div> 
        </div> 
        <div> 
          <div><b>Y<sub>1</sub>:</b></div> 
          <div><input type="text" name="y1" id="y1" value="<?php echo $MapY1; ?>"></div> 
        </div> 
        <div> 
          <div><b>X<sub>2</sub>:</b></div> 
          <div><input type="text" name="x2" id="x2" value="<?php echo $MapX2; ?>"></div> 
          <div></div> 
          <div></div> 
        </div> 
        <div> 
          <div><b>Y<sub>2</sub>:</b></div> 
          <div><input type="text" name="y2" id="y2" value="<?php echo $MapY2; ?>"></div> 
          <div></div> 
          <div></div> 
        </div>
  -->
     <div>
          <div>Coords:</b></div>
          <div><input type="text" name="coords" id="coords" value="495, 375, 596, 436"></div>
          <div></div>
          <div></div>
        </div>
	<div class="caption">
	  <input type="submit" name="action" value="Submit">
	  <button type="reset" onclick="document.location.href='cabnavigator.php?cabinetid=<?php echo $CabinetID; ?>'; return false;">Cancel</button>
	</div>
    </div> <!-- END div.table --> 
	</form>
	</div>
</div> <!-- END div.mapmaper -->

<div class="center"><div>
<?php echo "<img src=\"drawings/blank.gif\" height=$height width=$width>"; ?>
<div class="container demo"> 
  <div style="float: left; width: 70%;"> 
    <p class="instructions">Click and drag on the image to select an area for cabinet <?php echo $cab->Location; ?>.</p> 
 
    <div class="frame" style="margin: 0 0.3em; width: 300px; height: 300px;"> 
      <img id="map" src="<?php echo $mapfile; ?>" /> 
    </div> 
  </div> 
 
  <div style="float: left; width: 30%;"> 
    <p style="font-size: 110%; font-weight: bold; padding-left: 0.1em;">Selection Preview</p> 
  
  </div> 
</div> 
</body>
</html>

<?php
}
?>
