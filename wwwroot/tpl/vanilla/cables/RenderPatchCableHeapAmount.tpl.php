<?php if (defined("RS_TPL")) {?>
	<table class=widetable border=0 cellpadding=5 cellspacing=0 align=center>
	<tr><th>&nbsp;</th><th>Amount</th><th>End 1</th><th>Cable type</th><th>End 2</th><th>Length</th><th>Description</th><th>&nbsp;</th></tr>
	<?php while($this->refLoop('AllHeaps')) { ?>
		<?php $this->getH("PrintOpFormIntro", array('set', array ('id' => $this->_HeapId))); ?>
		<tr>
		<td>
		<?php if ($this->_HeapAmount > 0) { ?>
			<?php $this->getH('GetOpLink', array(array ('op' => 'dec', 'id' => $this->_HeapId), '', 'delete', 'consume')); ?>
		<?php } else { ?>
			<?php $this->getH("PrintImageHref", array('nodelete')); ?>
		<?php } ?> 
		</td>
		<td><input type=text size=7 name=amount value='<?php $this->HeapAmount ?>'></td>
		<td><?php $this->getH("GetOpLink", array(array ('op' => 'inc', 'id' => $this->_HeapId), '', 'add', 'replenish')); ?></td>
		<td><?php $this->EndCon1_String ?></td>
		<td><?php $this->PCType_String ?></td>
		<td><?php $this->EndCon2_String ?></td>
		<td class=tdright><?php $this->HeapLength ?></td>
		<td><?php $this->HeapString ?></td>
		<td><?php $this->getH('PrintImageHref', array('save', 'Save changes', TRUE)); ?></td>
		</tr></form>
	<?php } ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>