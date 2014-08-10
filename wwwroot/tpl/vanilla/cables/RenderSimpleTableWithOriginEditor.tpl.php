<?php if (defined("RS_TPL")) {?>
	<table class=widetable border=0 cellpadding=5 cellspacing=0 align=center>
	<tr><th>Origin</th><th>Key</th><th><?php $this->ColHeader ?></th></tr>
	<?php if($this->is('NewTop')) { ?>
		<?php $this->getH('PrintOpFormIntro', array('add')); ?>
		<tr>
			<td>&nbsp;</td>
			<td class=tdleft><?php $this->getH('PrintImageHref', array('create', 'create new', TRUE, 200)); ?></td>
			<td><input type=text size=<?php $this->ColumnWidth ?> name=<?php $this->ColumnValue ?> tabindex=100></td>
			<td class=tdleft><?php $this->getH('PrintImageHref', array('create', 'create new', TRUE, 200)); ?></td>
		</tr>
		</form>
	<?php } ?>
	<?php while($this->refLoop('AllRows')) { ?>
		<tr>
		<?php if ($this->is('OriginIsDefault')) { ?>
			<?php $this->getH('PrintImageHref', array('computer', 'default')); ?>
			<td>&nbsp;</td>
			<td><?php $this->RowValueWidth ?></td>
			<td>&nbsp;</td>
		<?php } else { ?>
			<?php $this->getH('PrintOpFormIntro', array('upd', array ($this->_Key => $this->_ColumnKey))); ?>
			<?php $this->getH("PrintImageHref", array('favorite', 'custom')); ?>
			<td><?php $this->getH('GetOpLink', array(array ('op' => 'del', $this->_Key => $this->_ColumnKey), '', 'destroy', 'remove')); ?></td>
			<td><input type=text size=<?php $this->Width ?> name=<?php $this->Value ?> value='<?php $this->RowValueWidth ?>'></td>
			<td><?php $this->getH('PrintImageHref', array('save', 'Save changes', TRUE)); ?></td>
			</form>
		<?php } ?> 
		</tr>
	<?php } ?>
	<?php if(!$this->is('NewTop')) { ?>
		<?php $this->getH('PrintOpFormIntro', array('add')); ?>
		<tr>
			<td>&nbsp;</td>
			<td class=tdleft><?php $this->getH('PrintImageHref', array('create', 'create new', TRUE, 200)); ?></td>
			<td><input type=text size=<?php $this->ColumnWidth ?> name=<?php $this->ColumnValue ?> tabindex=100></td>
			<td class=tdleft><?php $this->getH('PrintImageHref', array('create', 'create new', TRUE, 200)); ?></td>
		</tr>
		</form>
	<?php } ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>