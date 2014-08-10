<?php if (defined("RS_TPL")) {?>
	<td>
		<table>
			<?php while($this->refLoop('List')) { ?>
				<tr><td><input type=checkbox name="vs_list[]" checked value="<?php $this->ID; ?>"></td><td>
					<?php $this->SLBEntityCell; ?>
				</td></tr>
			<?php } ?>
		</table>
	</td>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>