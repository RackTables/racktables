<?php if (defined("RS_TPL")) {?>
	<?php $this->getH('PrintOpFormIntro','add'); ?>
	<tr>
		<td>
			<?php $this->getH('PrintImageHREF',array('create', 'Add new', TRUE)); ?>
		</td>
		<td><input type=text name=chapter_name tabindex=100></td><td>&nbsp;</td>
		<td>
			<?php $this->getH('PrintImageHREF',array('create', 'Add new', TRUE)); ?>
		</td>
	</tr>
	</form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>