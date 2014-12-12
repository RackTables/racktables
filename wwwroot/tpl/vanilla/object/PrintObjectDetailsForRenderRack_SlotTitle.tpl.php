<?php if (defined("RS_TPL")) {?>
	<?php if ($this->is("asset_no", null)) { ?>
		<div title='no asset tag
	<?php } else { ?>
		<div title='<?php $this->asset_no ?>
	<?php } ?> 
	<?php if ($this->is("label")) { ?>
		, visible label is "<?php $this->label ?>"
	<?php } ?>'>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>