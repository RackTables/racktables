<?php if (defined("RS_TPL")) {?>
	<?php $this->getH('PrintOpFormIntro',array('updateTag', array ('tag_id' => $this->_ID, 'tag_name' => $this->_Name))); ?>
	<input type=hidden name=is_assignable value=<?php $this->Assignable ?>>
	<tr>
		<td><?php $this->Name; ?></td>
		<td><?php $this->Select; ?></td>
		<td><?php $this->getH('PrintImageHREF',array('save', 'Save changes', TRUE)); ?></td>
	</tr>
	</form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>