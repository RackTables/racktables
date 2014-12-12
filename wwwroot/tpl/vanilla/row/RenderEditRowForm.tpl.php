<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Attributes</h2>
		<?php $this->getH('PrintOpFormIntro', array('updateRow')); ?>
		<table border=0 align=center>
		<tr><td>&nbsp;</td><th class=tdright>Location:</th><td class=tdleft>
		<?php $this->getH("PrintSelect", array($this->_Locations, array ('name' => 'location_id'), $this->_Location_ID)); ?></td></tr>
		<tr><td>&nbsp;</td><th class=tdright>Name (required):</th><td class=tdleft><input type=text name=name value='<?php $this->this->Row_name ?>'></td></tr>
		
		<?php $this->startLoop('AllRecords'); ?>
			<input type=hidden name=<?php $this->I ?>_attr_id value=<?php $this->Record_ID ?>><tr><td>
			<?php if ($this->is('HasValue', true)) { ?>
				<?php $this->getH('GetOpLink', array(array('op'=>'clearSticker', 'attr_id'=>$this->_Record_ID), '', 'clear', 'Clear value', 'need-confirmation')); ?>
			<?php } else { ?>
				&nbsp;
			<?php } ?>
			</td><th class=sticker><?php $this->Record_Name ?>:</th><td class=tdleft>
			<?php if ($this->is('PrintInput', true)) { ?>
				<input type=text name=<?php $this->I ?>_value value='<?php $this->RecordValue ?>'>
			<?php } else { ?>
				<?php $this->NifitySelChapter ?>
			<?php } ?> 
			</td></tr>
		<?php $this->endLoop(); ?>

		<?php if ($this->is("hasRows")) { ?>
			<tr><td>&nbsp;</td><th class=tdright>Actions:</th><td class=tdleft>
			<?php $this->getH("GetOpLink", array(array ('op'=>'deleteRow'), '', 'destroy', 'Delete row', 'need-confirmation')); ?>
			&nbsp;</td></tr>
		<?php } ?> 
		<tr><td class=submit colspan=3><?php $this->getH("PrintImageHref", array('SAVE', 'Save changes', TRUE)); ?></td></tr>
		</form></table><br>
	</div>
	<div class=portlet>
		<h2>History</h2>
		<?php $this->ObjectHistory ?>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>