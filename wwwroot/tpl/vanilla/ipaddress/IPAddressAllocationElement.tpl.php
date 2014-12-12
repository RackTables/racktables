<?php if (defined("RS_TPL")) {?>
	<tr class='<?php $this->Class; ?>'>
		<?php $this->getH('PrintOpFormIntro',array('upd', array ('object_id' => $this->_ObjectId))); ?>
			<td><?php $this->getH('GetOpLink',array(array ('op' => 'del', 'object_id' => $this->_ObjectId ), '', 'delete', 'Unallocate address')); ?>
			<td><a href='<?php $this->Link; ?>'><?php $this->ObjectName; ?></a></td>
			<td><input type='text' name='bond_name' value='<?php $this->BondName; ?>' size=10></td>
			<td><?php $this->TypeSelect; ?></td>
			<td><?php $this->getH('PrintImageHREF',array('save', 'Save changes', TRUE)); ?></td>
		</form>
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>