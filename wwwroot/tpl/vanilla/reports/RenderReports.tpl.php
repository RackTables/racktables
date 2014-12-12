<?php if (defined("RS_TPL")) {?>
	<table align=center>
	<?php $this->startLoop("ItemContent"); ?>	
		<tr><th colspan=2><h3><?php $this->Title; ?> </h3></th></tr>
		<?php $this->Cont; ?> 
		<tr><td colspan=2><hr></td></tr>
	<?php $this->endLoop(); ?> 
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>