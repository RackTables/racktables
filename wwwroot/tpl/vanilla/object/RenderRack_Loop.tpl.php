<?php if (defined("RS_TPL")) {?>
	<tr><th><?php $this->inverseRack ?></th>
	<?php $this->AllLocIdx ?>
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>