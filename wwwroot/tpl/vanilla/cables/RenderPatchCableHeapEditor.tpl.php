<?php if (defined("RS_TPL")) {?>
	<table class=widetable border=0 cellpadding=5 cellspacing=0 align=center>
	<tr><th>&nbsp;</th><th>Amount</th><th>End 1</th><th>Cable type</th><th>End 2</th><th>Length</th><th>Description</th><th>&nbsp;</th></tr>
	<?php $this->AddNewTop ?>
	<?php while($this->refLoop('AllHeaps')) { ?>
		<?php $this->getH("PrintOpFormIntro", array('upd', array ('id' => $this->_HeapId))); ?>
		<tr>
		<td><?php $this->getH("GetOpLink", array(array ('op' => 'del', 'id' => $this->_HeapId), '', 'destroy', 'remove')); ?></td>
		<td class=tdright><?php $this->HeapAmount ?></td>
		<td><?php $this->EndCon1_Select ?></td>
		<td><?php $this->PCType_Select ?></td>
		<td><?php $this->EndCon2_Select ?></td>
		<td><input type=text size=6 name=length value='<?php $this->HeapLength ?>'></td>
		<td><input type=text size=48 name=description value="<?php $this->HeapString ?>"></td>
		<td><?php $this->getH("PrintImageHref", array('save', 'Save changes', TRUE)); ?></td>
		</tr>
		</form>
	<?php } ?>
	<?php $this->AddNewBottom ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>