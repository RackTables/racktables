<?php if (defined("RS_TPL")) {?>
	</tr></table></th></tr>
	<tr class=row_<?php $this->get("RowOrder");?>><th class=tdleft></th><th class=tdleft><?php $this->get("RowName") ; ?> (continued)";
	</th><th class=tdleft><table border=0 cellspacing=5><tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>