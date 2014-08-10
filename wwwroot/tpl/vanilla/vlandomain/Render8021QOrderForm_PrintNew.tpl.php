<?php if (defined("RS_TPL")) {?>
	<?php $this->getH("PrintOpFormIntro", array( 'add')); ?>
	<tr>
	<?php if ($this->is("isNoObject",true)) { ?>
		<td><?php $this->selected ?></td>
	<?php } ?> 
	<?php if ($this->is("isNoVLANDomain",true)) { ?>
		<td><?php $this->getVLDSelect ?></td>
	<?php } ?>
	<?php if ($this->is("isNoVST",true)) { ?>
	 	<td><?php $this->getVSTSelect ?></td>
	<?php } ?>  
	<td><?php $this->getH("PrintImageHref", array('Attach', 'set', TRUE, 104)); ?></td></tr></form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>