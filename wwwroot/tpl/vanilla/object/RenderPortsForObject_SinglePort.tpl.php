<?php if (defined("RS_TPL")) {?>
	<?php $this->opFormIntro ?>
	<tr <?php $this->tr_class ?>><td><a name='port-<?php $this->port_id ?>' href='<?php $this->href_process ?>'>
	<?php $this->deleteImg ?>
	</a></td>
	<td class='tdleft <?php $this->Name_Class ?>' NOWRAP><input type=text name=name class='interactive-portname <?php $this->a_class ?>' value='<?php $this->port_name ?>' size=8></td>
	<td><input type=text name=label value='<?php $this->port_label ?>'></td>
	<td>
	<?php if ($this->is("iif_name")) { ?>
		<label><?php $this->iif_name ?>
	<?php } ?> 
	<?php $this->printSelExType ?>
	<?php if ($this->is("iif_name")) { ?>
		</label>
	<?php } ?>
	</td>

	<td><input type=text name=l2address value='<?php $this->l2address ?>' size=18 maxlength=24></td>
	<?php if ($this->is("isRemoteObj",true)) { ?>
		<td><?php $this->logged_span_rem_obj_id ?></td>
		<td><?php $this->logged_span_rem_name ?><input type=hidden name=reservation_comment value=''></td>
		<td><input type=text name=cable value='<?php $this->cableid ?>'></td>
		<td class=tdcenter><?php $this->unlink_op_link ?></td>
	<?php } elseif ($this->is("hasReservation_comment",true)) { ?>
		<td><?php $this->logged_span_rem_reserved ?></td>
		<td><input type=text name=reservation_comment value='<?php $this->reservation_comment ?>'></td>
		<td></td>
		<td class=tdcenter>
		<?php $this->use_up_op_link ?>
		</td>
	<?php } else {?>
		<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class=tdcenter><span
		ondblclick='window.open("<?php $this->href_helper_portlist ?>", "findlink", "height=700, width=400, location=no, menubar=no, resizable=yes, scrollbars=yes, status=no, titlebar=no, toolbar=no");'
		onclick='window.open("<?php $this->href_helper_portlist ?>", "findlink", "height=700, width=400, location=no, menubar=no, resizable=yes, scrollbars=yes, status=no, titlebar=no, toolbar=no");'>
		<?php $this->link_img ?>
		</span>
		<input type=text name=reservation_comment></td>
	<?php } ?>
	<td>
	<?php $this->save_img ?>
	</td></form></tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>