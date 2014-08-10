<?php if (defined("RS_TPL")) {?>
	<?php $this->startLoop('ValueIds'); ?>	
		<input type=hidden name=<?php $this->Input_Name ?>[] value=<?php $this->Id ?>>
	<?php $this->endLoop(); ?> 	
	<ul data-tagit='yes' data-tagit-valuename='<?php $this->Input_Name ?>' data-tagit-preselect='<?php $this->JSON ?>' class='tagit-vertical'></ul>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>