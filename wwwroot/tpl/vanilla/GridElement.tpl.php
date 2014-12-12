<?php if (defined("RS_TPL")) { ?>
	<td class='atom state_<?php $this->State; ?><?php $this->Hl; ?>'>
	<input id=<?php $this->Name; ?> type=checkbox
		<?php if ($this->is('Disabled')) { ?>
			 disabled >
		<?php } else { ?>
			name=<?php $this->Name; ?> <?php $this->Checked; ?> >
		<?php } ?>
	</td>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>