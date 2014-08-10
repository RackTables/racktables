<?php if (defined("RS_TPL")) {?>
	<?php $this->getH("PrintOpFormIntro", array('upd', array ('ip' => $this->_addrinfo_ip))); ?>
	<tr class='<?php $this->tr_class ?>' valign=top>
	<td><?php $this->getH("GetOpLink", array(array ('op' => 'del', 'ip' => $this->_addrinfo_ip), '', 'delete', 'Delete this IP address')); ?></td>
	<td class=tdleft><input type='text' name='bond_name' value='<?php $this->osif ?>' size=10><?php $this->td_name_suffix ?></td>
	<?php $this->td_ip ?>
	<?php if ($this->is("isExt_ipv4", true)) { ?>
		<?php $this->td_network ?>
		<?php $this->td_routed_by ?>
	<?php } ?> 
	<td><?php $this->bond_type_mod ?></td>
	<?php $this->td_peers ?>
	<td><?php $this->getH("PrintImageHref", array('save', 'Save changes', TRUE)); ?></td>
	</form></tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>