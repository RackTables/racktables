<?php if (defined("RS_TPL")) {?>

	<div class=portlet><h2>Cacti Graphs</h2>
	<?php 
		$this->NewTop;
	?>
	<table cellspacing="0" cellpadding="10" align="center" width="50%">
	<?php 
		$this->RowsTR;
	?>
	</table>
	<?php
		$this->NewBottom;
	 ?>
	 </div>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>