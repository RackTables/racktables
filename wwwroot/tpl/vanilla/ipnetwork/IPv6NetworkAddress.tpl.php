<?php if (defined("RS_TPL")) { ?>
	<tr class='<?php $this->RowClass; ?> tdleft <?php $this->Highlighted; ?>'>
		<td><a class='<?php $this->Class; ?>' title='<?php $this->Title; ?>' name='ip-<?php $this->IP; ?>' href='<?php $this->Link; ?>'><?php $this->PrintedIP; ?></a></td>
		<td><span class='rsvtext <?php $this->Editable; ?> id-<?php $this->IP; ?> op-upd-ip-name'><?php $this->Name; ?></span></td>
		<td><span class='rsvtext <?php $this->Editable; ?> id-<?php $this->IP; ?> op-upd-ip-comment'><?php $this->Comment; ?></span></td>
		<td>
			<?php if ($this->is('Reserved')) { ?>
				<strong>RESERVED</strong>
			<?php } ?>
			<?php if ($this->is('Allocs')) { ?>
				<?php $this->startLoop('Allocs'); ?>
					<?php $this->Type; ?>
					<?php $this->IPAllocLink ?>
				<?php $this->endLoop(); ?>
			<?php } ?>
			<?php if ($this->is('VSList')) { ?>
				<br />
				<?php $this->startLoop('VSList'); ?>
					<?php $this->Link; ?> &rarr; <br />
				<?php $this->endLoop(); ?>
			<?php } ?>
			<?php if ($this->is('VSGList')) { ?>
				<br />
				<?php $this->startLoop('VSGList'); ?>
					<?php $this->Link; ?> &rarr; <br />
				<?php $this->endLoop(); ?>
			<?php } ?>
			<?php if ($this->is('RSPList')) { ?>
				<br />
				<?php $this->startLoop('RSPList'); ?>
					&rarr; <?php $this->Link; ?> <br />
				<?php $this->endLoop(); ?>
			<?php } ?>
		</td>
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>