<?php if (defined("RS_TPL")) {?>
<?php $this->getH('PrintOpFormIntro', 'add'); ?>
<tr><td>&nbsp;</td><td>&nbsp;</td><td>
<?php $this->getH('PrintImageHref', array('add', 'Add new', TRUE)); ?>
</td>
<td class=tdleft><input type=text name=dict_value size=64 tabindex=100></td><td>
<?php $this->getH('PrintImageHref',array('add', 'Add new', TRUE, 101)); ?>
</td></tr></form>



<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>