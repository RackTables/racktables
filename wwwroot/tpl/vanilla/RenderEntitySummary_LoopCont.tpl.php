<?php if (defined("RS_TPL")) {?>
	<?php if($this->is("SingleVal", true)){ ?>
		<?php $this->val ?>
	<?php } else {?>
		<?php if($this->is("ShowTags", true)) { ?>
			<?php $this->getH("PrintTagTRs", array( $this->_Cell, $this->_BaseUrl));  ?> 
		<?php } else {?>
			<tr><th width='50%' class='<?php $this->Class ?>'><?php $this->Name ?></th><td class=tdleft><?php $this->Val ?></td></tr> 
		<?php } ?>
	<?php } ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>