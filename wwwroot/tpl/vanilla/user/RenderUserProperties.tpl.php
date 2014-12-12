<?php if (defined("RS_TPL")) {?>
	<?php $this->getH("PrintOpFormIntro", array('edit')); ?>
	<table border=0 align=center>
	<tr><th class=tdright>Tags:</th><td class=tdleft>
	<?php $this->TagsPicker ?></td></tr>
	<tr><th class=submit colspan=2>
	<?php $this->getH("PrintImageHref", array('SAVE', 'Save changes', TRUE, 102)); ?>
	</th></tr></table></form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>