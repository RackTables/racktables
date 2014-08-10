<?php if (defined("RS_TPL")) {?>
	<?php $this->addJS('js/racktables.js'); ?>
	<tr>
		<th>
			<a href='javascript:;' onclick="toggleRowOfAtoms('<?php $this->RackId; ?>','<?php $this->UnitNo; ?>')"><?php $this->Inversed; ?></a>
		</th>
		<?php $this->Atoms; ?>
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>