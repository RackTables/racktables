<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Modify</h2>
		<?php $this->getH("PrintOpFormIntro", array('upd')); ?>

		<table border=0 cellspacing=0 cellpadding=2 align=center>
		<tr><th class=tdright>Name:</th><td class=tdleft>
		<input type=text size=40 name=vlan_descr value='<?php $this->vlan_descr ?>'>
		</td></tr>
		<tr><th class=tdright>Type:</th><td class=tdleft>
		<?php $this->getH("PrintSelect", array($this->_vtoptions, 
		array ('name' => 'vlan_type', 'tabindex' => 102), $this->_vlan_prop)); ?>
		</td></tr>
		</table>
		<p>
		<input type="hidden" name="vdom_id" value="<?php $this->htmlspcDomainID ?>">
		<input type="hidden" name="vlan_id" value="<?php $this->htmlspcVlanID ?>">
		<?php $this->getH("PrintImageHref", array('SAVE', 'Update VLAN', TRUE)); ?>
		</form><p>
		<?php $this->reasonLink ?>
		<?php if ($this->is("isPortc",true)) { ?>
			<p><?php $this->getH("GetOpLink", array(
			array ('op' => 'clear'), 'remove', 'clear', "remove this VLAN from <?php $this->portc ?> ports")); ?>
			this VLAN from <?php $this->mkaPortc ?>
		<?php } ?> 
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>