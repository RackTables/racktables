<?php if (defined("RS_TPL")) {?>
	<td
	<?php if ($this->is("slotRows")) { ?>
		 rowspan=<?php $this->slotRows ?>
	<?php } ?> 
	<?php if ($this->is("slotCols")) { ?>
		 rowspan=<?php $this->slotCols ?>
	<?php } ?> 
	 class=<?php $this->slotClass ?>'><?php $this->slotTitle ?>
	<?php $this->mkASlotInfo ?>
	</div></td>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>