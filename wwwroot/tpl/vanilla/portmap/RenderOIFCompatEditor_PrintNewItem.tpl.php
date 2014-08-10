<?php if (defined("RS_TPL")) {?>

	<?php $this->getH('PrintOpFormIntro', 'add'); ?>
	<tr><th class=tdleft>
	<?php $this->getH('PrintImageHref', array('add', 'add pair', TRUE)); ?>
	</th><th class=tdleft>
	<?php $this->Type1; ?>
	</th><th class=tdleft>
	<?php $this->Type2; ?>
	</th></tr></form>


<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>