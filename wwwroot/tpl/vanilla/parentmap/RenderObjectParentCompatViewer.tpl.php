<?php if (defined("RS_TPL")) {?>

	<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>
	<tr><th>Parent</th><th>Child</th></tr>
	<?php $this->startLoop('Looparray'); ?>
	<tr class=row_<?php $this->Order; ?>><td><?php $this->Parentname; ?></td><td><?php $this->Childname; ?></td></tr>
	<?php $this->endLoop(); ?>
	</table>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>