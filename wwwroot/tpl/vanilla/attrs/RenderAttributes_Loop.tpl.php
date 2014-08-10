<?php if (defined("RS_TPL")) {?>
	<?php $this->startLoop('AllAppAttrs'); ?>
		<?php $this->ObjType ?>
		<?php $this->DictCont ?>
		<br> 
	<?php $this->endLoop(); ?> 
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>