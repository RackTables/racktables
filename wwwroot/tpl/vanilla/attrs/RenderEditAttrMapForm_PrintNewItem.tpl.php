<?php if (defined("RS_TPL")) {?>

	<?php $this->getH('PrintOpFormIntro', 'add'); ?>
	<tr><td>
	<?php $this->getH('PrintImageHref', array('create', 'Create attribute', TRUE)); ?>
	</td><td><input type=text tabindex=100 name=attr_name></td><td>
	<?php $this->GetSelect; ?>
	</td><td>
	<?php $this->getH('PrintImageHref', array('create', 'Create attribute', TRUE, 102)); ?>
	</td></tr></form>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>