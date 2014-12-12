<?php if (defined("RS_TPL")) {?>
	<?php $this->getH("PrintOpFormIntro", array('addPort')); ?>
	<tr><td>
	<?php $this->getH("PrintImageHref", array('add', 'add a port', TRUE)); ?>
	</td><td class='tdleft'><input type=text size=8 name=port_name tabindex=100></td>
	<td><input type=text name=port_label tabindex=101></td><td>
	<?php $this->niftySel ?>
	<td><input type=text name=port_l2address tabindex=103 size=18 maxlength=24></td>
	<td colspan=4>&nbsp;</td><td>
	<?php $this->getH("PrintImageHref", array('add', 'add a port', TRUE, 104)); ?>
	</td></tr></form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>