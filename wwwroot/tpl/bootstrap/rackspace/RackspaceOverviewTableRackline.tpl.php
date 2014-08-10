<?php if (defined("RS_TPL")) {?>
	<td align=center valign=bottom><a href='<?php $this->get("RackLink"); ?>'>
	<img border=0 width=<?php $this->get("RackImageWidth"); ?> height=<?php $this->get("RackImageHeight"); ?> title='<?php $this->get("RackHeight"); ?> units' src='?module=image&img=minirack&rack_id=<?php $this->get("RackId"); ?>'>
	<br><?php $this->get("RackName");?></a></td>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>