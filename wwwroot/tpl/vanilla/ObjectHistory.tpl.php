<?php if (defined("RS_TPL")) {?>
	<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
	<tr><th>change time</th><th>author</th><th>name</th><th>visible label</th><th>asset no</th><th>has problems?</th><th>comment</th></tr>
	<?php $this->startLoop('History'); ?>
		<tr class=row_<?php $this->Order; ?>>
			<td><?php $this->get(0); ?></td>
			<td><?php $this->get(1); ?></td>
			<td><?php $this->get(2); ?></td>
			<td><?php $this->get(3); ?></td>
			<td><?php $this->get(4); ?></td>
			<td><?php $this->get(5); ?></td>
			<td><?php $this->get(6); ?></td>
		</tr>
	<?php $this->endLoop(); ?>
	</table>
	<br />
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>