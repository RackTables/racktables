<?php if (defined("RS_TPL")) {?>
	<center><h1><?php $this->Ip; ?></h1></center>
	<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>
		<tr><th>&nbsp;</th><th>object</th><th>OS interface</th><th>allocation type</th><th>&nbsp;</th></tr>
		<?php if($this->is('NewTop')) { ?>
			<?php $this->getH('PrintOpFormIntro','add'); ?>
			<tr>
				<td><?php $this->getH('PrintImageHREF',array('add','allocate',TRUE)) ; ?></td>
				<td><?php $this->CreateNewObjectSelect; ?></td>
				<td><input type=text tabindex=101 name=bond_name size=10></td>
				<td><?php $this->CreateNewTypeSelect; ?></td>
				<td><?php $this->getH('PrintImageHREF',array('add','allocate',TRUE,103)) ; ?></td>
			</tr>
			</form>
		<?php } ?>
		<?php if($this->is('Reserved')) { ?>
		<tr class='<?php $this->Class; ?>'><td colspan=3>&nbsp;</td><td class=tdleft><strong>RESERVED</strong></td><td>&nbsp;</td></tr>
		<?php } ?>
		<?php while($this->loop('AddressList')) { ?>
			<tr class='<?php $this->Class; ?>'>
				<?php $this->getH('PrintOpFormIntro',array('upd', array ('object_id' => $this->_ObjectId))); ?>
					<td><?php $this->getH('GetOpLink',array(array ('op' => 'del', 'object_id' => $this->_ObjectId ), '', 'delete', 'Unallocate address')); ?>
					<td><a href='<?php $this->Link; ?>'><?php $this->ObjectName; ?></a></td>
					<td><input type='text' name='bond_name' value='<?php $this->BondName; ?>' size=10></td>
					<td><?php $this->TypeSelect; ?></td>
					<td><?php $this->getH('PrintImageHREF',array('save', 'Save changes', TRUE)); ?></td>
				</form>
			</tr>
		<?php } ?>
		<?php if(!$this->is('NewTop')) { ?>
			<?php $this->getH('PrintOpFormIntro','add'); ?>
			<tr>
				<td><?php $this->getH('PrintImageHREF',array('add','allocate',TRUE)) ; ?></td>
				<td><?php $this->CreateNewObjectSelect; ?></td>
				<td><input type=text tabindex=101 name=bond_name size=10></td>
				<td><?php $this->CreateNewTypeSelect; ?></td>
				<td><?php $this->getH('PrintImageHREF',array('add','allocate',TRUE,103)) ; ?></td>
			</tr>
			</form>
		<?php } ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>