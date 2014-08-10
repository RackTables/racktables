<?php if (defined("RS_TPL")) {?>
	<center><table border=0><tr valign=middle>
	<td><h2><?php $this->mkARowName ?> : </h2></td>
	<?php if ($this->is("isPrev",true)) { ?>
		<td><?php $this->mkAPrevImg ?></td>
	<?php } ?> 
	<td><h2><?php $this->mkAName ?></h2></td>
	<?php if ($this->is("isNext",true)) { ?>
		<td><?php $this->mkANextImg ?></td>
	<?php } ?> 
	</h2></td></tr></table>
	<table class=rack border=0 cellspacing=0 cellpadding=1>
	<tr><th width='10%'>&nbsp;</th><th width='20%'>Front</th>
	<th width='50%'>Interior</th><th width='20%'>Back</th></tr>
	<?php $this->RackLoopSpace ?>
	</table>
	<?php if ($this->is("hasZeroUObj",true)) { ?>
		<br><table width='75%' class=rack border=0 cellspacing=0 cellpadding=1>
		<tr><th>Zero-U:</th></tr>
		<?php $this->startLoop("allZeroUObj"); ?>	
			<tr><td class='atom state_<?php $this->state ?>'>
			<?php $this->objDetails ?>
			</td></tr>
		<?php $this->endLoop(); ?> 
		</table>
	<?php } ?> 
	</center>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>