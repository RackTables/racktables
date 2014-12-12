<?php if (defined("RS_TPL")) {?>
	<center><h1><?php $this->Ip; ?></h1></center>
	<div class=portlet>
		<h2>update</h2>
		<table border=0 cellpadding=10 cellpadding=1 align='center'>
		<?php $this->getH('PrintOpFormIntro','editAddress'); ?>
			<tr>
				<td class=tdright><label for=id_name>Name:</label></td>
				<td class=tdleft><input type=text name=name id=id_name size=20 value='<?php $this->Name; ?>'>
			</tr>
			<tr>
				<td class=tdright><label for=id_comment>Comment:</label></td>
				<td class=tdleft><input type=text name=comment id=id_comment size=20 value='<?php $this->Comment; ?>'>
			</tr>
			<tr>
				<td class=tdright><label for=id_reserved>Reserved:</label></td>
				<td class=tdleft><input type=checkbox name=reserved id=id_reserved size=20 <?php $this->Checked; ?>>
			</tr>
			<tr>
				<td class=tdleft>
					<?php $this->getH('PrintImageHREF',array('SAVE', 'Save changes', TRUE)); ?>
				</td>
			</form>
				<td class=tdright>
					<?php if($this->is('Undeletable')) { ?>
						<?php $this->getH('PrintImageHREF','CLEAR gray'); ?>
					<?php } else { ?>	
						<?php $this->getH('PrintOpFormIntro', array('editAddress', array ('name' => '', 'reserved' => '', 'comment' => '')));
			     			  $this->getH('PrintImageHREF',   array('CLEAR', 'Release', TRUE));	?>
						</form>		
					<?php } ?>
				</td>
			</tr>
		</table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>