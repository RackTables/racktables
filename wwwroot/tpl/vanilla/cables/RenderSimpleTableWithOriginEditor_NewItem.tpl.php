<?php if (defined("RS_TPL")) {?>
	<?php $this->getH('PrintOpFormIntro', array('add')); ?>
	<tr>
	<td>&nbsp;</td>
	<td class=tdleft><?php $this->getH('PrintImageHref', array('create', 'create new', TRUE, 200)); ?></td>
	<td><input type=text size=<?php $this->Width ?> name=<?php $this->Value ?> tabindex=100></td>
	<td class=tdleft><?php $this->getH('PrintImageHref', array('create', 'create new', TRUE, 200)); ?></td>
	</tr></form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>