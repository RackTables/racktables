<?php if (defined("RS_TPL")) {?>
	<tr
	<?php if ($this->is('IsHighlighted', true)) { ?>
		 class=highlight
	<?php } ?>
	><td class='tdleft <?php $this->Name_Class ?>' NOWRAP><a name='port-<?php $this->PortId ?>' 
	class='interactive-portname nolink <?php $this->AClass ?>'><?php $this->PortName ?></a></td>
	<td class=tdleft><?php $this->PortLabel ?></td>
	<td class=tdleft><?php $this->FormatedPort ?></td><td class=tdleft><tt><?php $this->PortL2address ?></tt></td>
	<?php if ($this->is('Editable')) { ?>
		<td class=tdleft><?php $this->FormatedPortLink ?></td>
		<td class=tdleft><?php $this->FormatedLoggSpan ?></td>
		<td class=tdleft><span class='rsvtext <?php $this->Editable ?> id-<?php $this->PortId ?> op-upd-reservation-cable'><?php $this->PortCableId ?></span></td>
	<?php } else { ?>
		<?php $this->FormatedReservation ?><td></td>
	<?php } ?>	
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>