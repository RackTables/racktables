<?php if (defined("RS_TPL")) {?>
	<?php $this->getH('PrintOpFormIntro', 'updPortList'); ?>
	<table cellspacing=0 cellpadding=5 align=center class=widetable>
	<tr><td>
	<?php $this->Nifty_Select; ?>
	</td></tr>
	<tr><td><?php $this->getH('PrintImageHref', array('RECALC', 'process changes', TRUE)); ?></td></tr>
</table></form>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>