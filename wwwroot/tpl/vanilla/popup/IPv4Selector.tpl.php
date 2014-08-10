<?php if (defined("RS_TPL")) { ?>
	<link rel="stylesheet" type="text/css" href="?module=chrome&amp;uri=css/pi.css">
	<div style="background-color: #f0f0f0; border: 1px solid #3c78b5; padding: 10px; height: 100%; text-align: center; margin: 5px;">
	<h2>Choose a port:</h2><br><br>
	<form action="javascript:;">
	<input type=hidden id=ip>
	<select size='<?php $this->MaxElSize; ?>' id=addresses>
	<?php while($this->refLoop('PortOptions')) { //This is a small test of the new reference functions to improve loop-performance. ?>
		<option value='<?php $this->IP; ?>' onclick='getElementById("ip").value="<?php $this->IP; ?>";'><?php $this->ObjectName; ?> <?php $this->Name; ?> <?php $this->IP; ?></option>
	<?php } ?>
	</select>
	<br><br>
	<input type=submit value='proceed' onclick='if (getElementById("ip")!="") {	opener.document.getElementById("remoteip").value=getElementById("ip").value; window.close();}'>
	</form>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>