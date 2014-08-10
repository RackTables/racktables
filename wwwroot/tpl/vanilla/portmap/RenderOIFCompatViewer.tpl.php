<?php if (defined("RS_TPL")) {?>
	<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>
		<tr><th>From interface</th><th>To interface</th></tr>
		<?php $this->startLoop('AllPortCompat'); ?>
			<tr class=row_<?php $this->Order; ?> > <td><?php $this->Type1; ?></td><td><?php $this->Type2; ?></td></tr>
		<?php $this->endLoop(); ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>