<?php if (defined("RS_TPL")) {?>
	<table class=molecule cellspacing=0>
	<caption><?php $this->RackName ?></caption>
	<tr><th width='10%'>&nbsp;</th><th width='20%'>Front</th><th width='50%'>Interior</th><th width='20%'>Back</th></tr>
	<?php while($this->refLoop('AllRows')) { ?>
    	<tr><th><?php $this->InverseRack ?></th>
    	<?php $this->AllLocs ?>
    	</tr>
    <?php } ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>