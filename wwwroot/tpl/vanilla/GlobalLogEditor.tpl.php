<?php if (defined("RS_TPL")) {?>
	<table with='80%' align=center border=0 cellpadding=5 cellspacing=0 align=center class=cooltable><tr valign=top>
	<?php $this->getH('PrintOpFormIntro', array('add')); ?>
	<th align=left>Name: <?php $this->Select ?></th>
	<tr><td align=left><table with=100% border=0 cellpadding=0 cellspacing=0><tr><td colspan=2><textarea name=logentry rows=3 cols=80></textarea></td></tr>
	<tr><td align=left></td><td align=right><?php $this->getH("PrintImageHref", array('CREATE', 'add record', TRUE)); ?></td>
	</tr></table></td></tr></form></table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>