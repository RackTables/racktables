<?php if (defined("RS_TPL")) {?>
	<tr class=row_<?php $this->order ?>><th class=tdleft><?php $this->iif_iif_id ?></th><td> 
	<?php $this->getH("GetOpLink", array(array ('op' => 'addPack', 'standard' => $this->_codename, 'iif_id' => $this->_iif_id), '', 'add')); ?> 
	</td><td>
	<?php $this->getH("GetOpLink", array(array ('op' => 'delPack', 'standard' => $this->_codename, 'iif_id' => $this->_iif_id), '', 'delete')); ?> 
	</td></tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>