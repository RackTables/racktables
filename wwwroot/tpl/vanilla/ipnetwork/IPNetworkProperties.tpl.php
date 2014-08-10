<?php if (defined("RS_TPL")) {?>
	<center><h1><?php $this->IP; ?>/<?php $this->Mask; ?></h1></center>
	<?php $this->getH('PrintOpFormIntro',array('editRange')); ?>
		<table border=0 cellpadding=10 cellpadding=1 align='center'>
			<tr>
				<td class=tdright><label for=nameinput>Name:</label></td>
				<td class=tdleft><input type=text name=name id=nameinput size=80 maxlength=255 value='<?php $this->Name; ?>'></td>
			</tr>
			<tr><th class=tdright>Tags:</th><td class=tdleft><?php $this->TagsPicker ?></td></tr>
			<tr>
				<td class=tdright><label for=commentinput>Comment:</label></td>
				<td class=tdleft><textarea name=comment id=commentinput cols=80 rows=25><?php $this->Comment; ?></textarea></td>
			</tr>
			<tr>
				<td colspan=2 class=tdcenter>
					<?php $this->getH('PrintImageHREF',array('SAVE', 'Save changes', TRUE)); ?>
				</td>
			</tr>
	</table></form>
	<center>
		<?php if ($this->is('NotEmpty',true)) { ?>
			<?php $this->getH('GetOpLink',array(NULL, 'delete this prefix', 'nodestroy', 'There are ' . $this->_AllocCount . ' allocations inside')); ?>
		<?php } else { ?>
			<?php $this->getH('GetOpLink',array(array('op'=>'del','id'=>$this->_ID), 'delete this prefix', 'destroy')); ?>		
		<?php } ?>
	</center>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>