<?php if (defined("RS_TPL")) {?>
	<tr>
		<?php $this->startLoop("AndOr"); ?>
			<td class='tagbox <?php $this->get("Selected"); ?>' >
				<label>
					<input type=radio name=andor value=<?php $this->get("Boolop");?> <?php $this->get("Checked"); ?>><?php $this->get("Boolop"); ?></input>
				</label>
			</td>
		<?php $this->endLoop(); ?>
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>