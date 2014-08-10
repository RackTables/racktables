<?php if (defined("RS_TPL")) {?>
	<?php $this->getH("PrintOpFormIntro", array('add')); ?>
	<tr><td>
	<?php $this->getH("PrintImageHref", array('create', 'add VLAN', TRUE, 110)); ?>
	</td><td>
	<input type=text name=vlan_id size=4 tabindex=101>
	</td><td>
	<?php $this->getH("PrintSelect", array( $this->_Vtoptions,  array ('name' => 'vlan_type', 'tabindex' => 102), 'ondemand')); ?>
	</td><td>
	<input type=text size=48 name=vlan_descr tabindex=103>
	</td><td>
	<?php $this->getH("PrintImageHref", array('create', 'add VLAN', TRUE, 110)); ?>
	</td></tr></form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>