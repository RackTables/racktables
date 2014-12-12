<?php if (defined("RS_TPL")) {?>
	<tr class='tdleft <?php $this->TrClass; ?>'>
		<td class=tdleft><a name='ip-<?php $this->IP; ?>' href='<?php $this->Link; ?>'><?php $this->IP; ?></a></td>
		<td><span class='rsvtext <?php $this->Editable; ?> id-<?php $this->IP; ?> op-upd-ip-name'></span></td>
		<td><span class='rsvtext <?php $this->Editable; ?> id-<?php $this->IP; ?> op-upd-ip-comment'></span></td><td></td>
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>