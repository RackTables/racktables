<?php if (defined("RS_TPL")) {?>
	<table border=0 cellpadding=10 class=cooltable>
	<tr><th class=tdleft>Location</th><th class=tdleft>Row</th><th class=tdleft>Racks</th></tr>
		<?php $this->startLoop("OverviewTable"); ?>
			<tr class=row_<?php $this->get("Order");?>>
				<th class=tdleft><?php $this->get("LocationTree"); ?></th>
				<th class=tdleft><?php $this->get("HrefToRow");?></th>
				<th class=tdleft><table border=0 cellspacing=5><tr><?php $this->get("RowOverview");?></tr></table></th>
			</tr>
		<?php $this->endLoop(); ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>