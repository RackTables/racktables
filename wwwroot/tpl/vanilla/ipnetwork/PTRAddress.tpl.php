<?php if (defined("RS_TPL")) { ?>
	<?php if ($this->is('IsImport')) { ?>
		<input type=hidden name=addr_<?php $this->IDx; ?> value=<?php $this->StrAddr; ?>>
		<input type=hidden name=descr_<?php $this->IDx; ?> value=<?php $this->PtrName; ?>>
		<input type=hidden name=rsvd_<?php $this->IDx; ?> value=<?php $this->Reserved; ?>>
	<?php } ?>
	<tr class='<?php $this->CSSClass; ?>'>
		<td class='tdleft <?php $this->CSSTDClass; ?>'>
			<?php $this->Link; ?>
		</td>
		<td class=tdleft><?php $this->Name; ?></td>
		<td class=tdleft><?php $this->PtrName; ?></td>
		<?php if ($this->is('IsImport')) { ?>
			<td>
			<?php if($this->is('BoxCounter')) { ?>
				<input type=checkbox name=import_<?php $this->IDx; ?> tabindex=<?php $this->IDx; ?> id=atom_1_<?php $this->BoxCounter; ?>_1>
			<?php } else { ?>&nbsp;<?php } ?>
			</td>
		<?php } ?>
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>