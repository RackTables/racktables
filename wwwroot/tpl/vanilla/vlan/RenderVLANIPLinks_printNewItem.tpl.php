<?php if (defined("RS_TPL")) {?>
	<?php $this->getH("PrintOpFormIntro", array('bind', $this->_extra)); ?>
	<tr><td><?php $this->OptionTree ?>
	</td><td><?php $this->getH("PrintImageHref", array('ATTACH', 'bind', TRUE, 102)); ?></td></tr></form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>