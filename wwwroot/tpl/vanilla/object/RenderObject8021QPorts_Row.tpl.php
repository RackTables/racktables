<?php if (defined("RS_TPL")) {?>
	<tr class='
	<?php if ($this->is("HasErrors")) { ?>
		trerror
	<?php } else  { 
		$this->TextClass;
		} ?> 
	' valign=top><td<?php $this->TdExtra ?>
	<?php if ($this->is('HasPortName')) { ?>
	 	class="border_highlight"
	<?php } ?>  NOWRAP><a class='interactive-portname port-menu nolink' <?php if ($this->is('HasPortName')) { ?>
	 	name='port-<?php $this->PortId ?>'
	 <?php } ?>><?php $this->PortName ?></a></td><?php if ($this->is('NoSocketColumns')) { ?>
	 	<td>&nbsp;</td><td>&nbsp;</td>
	 <?php } else $this->SocketColumns; ?>
	<td<?php $this->TdExtra ?>><?php $this->TextLeft ?></td><td class=tdright nowrap<?php $this->TdExtra ?>><?php $this->TextRight ?></td></tr>
	<?php $this->SocketRows ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>