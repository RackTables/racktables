<?php if (defined("RS_TPL")) {?>
	<?php if ($this->is("isAsset_no", true)) { ?>
		<div title='<?php $this->asset_no ?>
	<?php } else { ?> 
		<div title='no asset tag
	<?php } ?> 
	<?php if ($this->is("isUncommon_name", true)) { ?>
		, visible label is "<?php $this->label ?>"
	<?php } ?> 
	<?php if ($this->is("areObjectChildren",true)) { ?>
		, contains <?php $this->childNames ?>
	<?php } ?>'>
	<?php $this->mkA ?></div>
	<?php $this->tableCont ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>