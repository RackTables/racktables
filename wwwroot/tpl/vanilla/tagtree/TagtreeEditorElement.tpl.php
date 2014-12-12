<?php if (defined("RS_TPL")) {?>
	<tr <?php if($this->is('Assignable',false)) { ?>
			<?php if ($this->is('hasChildren',true)) { ?>
				class=trnull
			<?php } else { ?>
				class=trwarning
			<?php } ?>
		<?php } ?>>
	<td align=left style='padding-left: <?php echo $this->_Level * 16; ?>px;'>
	<?php if ($this->is('hasChildren', true)) { ?>
		<img width="16" border="0" height="16" src="?module=chrome&uri=pix/node-expanded-static.png"></img>
	<?php } ?>
	<?php if ($this->is('hasReferences',true) || $this->is('hasChildren', true)) { ?>
		<img width="16" border="0" height="16" src="?module=chrome&uri=pix/tango-user-trash-16x16-gray.png" title="<?php $this->References; ?> references, <?php $this->Subtags; ?> subtags'?>"></img>
	<?php } else { ?>
		<?php $this->getH('GetOpLink',array(array ('op' => 'destroyTag', 'tag_id' => $this->_ID), '', 'destroy', 'Delete tag')); ?>	
	<?php } ?>
	</td><td>
	<?php $this->getH('PrintOpFormIntro',array('updateTag', array ('tag_id' => $this->_ID))) ; ?>
	<input type=text size=48 name=tag_name value="<?php $this->Tag; ?>">
	</td>
	<td class=tdleft>
		<?php if($this->is('References', true)) { ?>
			<?php $this->getH("PrintSelect", array(array ('yes' => 'yes'), array ('name' => 'is_assignable'))); ?>
		<?php } else {?>
			<?php $this->getH("PrintSelect", array(array ('yes' => 'yes', 'no' => 'no'), array ('name' => 'is_assignable'), $this->_AssignableInfo)); ?>
		<?php } ?>
	</td>
	<td class=tdleft>
		<?php $this->ParentSelect; ?>
	</td>
	<td>
		<?php $this->getH('PrintImageHREF',array('save', 'Save changes', TRUE)); ?> </form>
	</td>
	</tr>
	<?php $this->SubLeafs ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>