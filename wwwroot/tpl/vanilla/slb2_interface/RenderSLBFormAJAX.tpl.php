<?php if (defined("RS_TPL")) {?>
	<?php $this->getH("PrintOpFormIntro", array($this->_Action, $this->_Orig_Req)); ?>
	<table align=center><tr class="tdleft">
	<?php $this->startLoop('AllCells'); ?>	
		<td><?php $this->EntityCell ?></td>
	<?php $this->endLoop(); ?>
	<td><ul style="list-style: none">
	<?php $this->startLoop('AllPorts'); ?>	
		<li><label><input type=checkbox name="enabled_ports[]" value="<?php $this->Value ?>" checked><?php $this->Key ?></label></li>
	<?php $this->endLoop(); ?> 
	</ul></td>
	<td><ul style="list-style: none">
	<?php $this->startLoop('AllVips'); ?>	
		<li><label><input type=checkbox name="enabled_vips[]" value="<?php $this->Value ?>" checked><?php $this->Key ?></label></li>
	<?php $this->endLoop(); ?>
	</ul></td><td>
	<?php $this->getH('PrintImageHref', array('ADD', 'Configure LB', TRUE)); ?>
	</td>
	</tr></table>
	</form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>