<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Log messages</h2>
		<table class="widetable" cellspacing="0" cellpadding="5" align="center" width="50%">
			<tr>
				<th>Date &uarr;</th>
				<th>User</th>
				<th>Log message</th>
			<tr>
			<?php $this->startLoop('Messages'); ?>
				<tr class='<?php $this->Class; ?>'>
					<td><?php $this->Date; ?></td>
					<td><?php $this->User; ?></td>
					<td><?php $this->Message; ?></td>
				</tr>
			<?php $this->endLoop(); ?>
		</table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>