<?php if (defined("RS_TPL")) {?>
	<tr valign=top class='<?php $this->Highlight; ?>'>
		<?php $this->ItemInfo; ?>
		<td class=tdcenter>
			<?php $this->Capacity; ?>
		</td>
		<?php $this->Routers; ?>
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>