<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Real servers (<?php $this->RsCount ?>)</h2>
		<table cellspacing=0 cellpadding=5 align=center class=widetable>
		<tr>
			<?php $this->TableHeads ?>
		</tr>
		<?php $this->startLoop('AllRowsCont'); ?>	
			<tr valign=top><?php $this->RowCont ?></tr>
		<?php $this->endLoop(); ?> 
		</table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>