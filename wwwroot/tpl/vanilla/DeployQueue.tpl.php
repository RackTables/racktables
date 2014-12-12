<?php if (defined("RS_TPL")) {?>
	<h2 align=center>Queue <?php $this->dqTitle ?> (<?php $this->countData ?>) </h2>
	<?php if ($this->is("continue", false)) { ?>
		<table cellspacing=0 cellpadding=5 align=center class=widetable>
		<tr><th>switch</th><th>changed</th><th>
		<?php $this->startLoop("dataArr"); ?>	
			<tr class=row_<?php $this->order ?> ><td>
			<?php $this->renderedCell ?> 
			</td><td><?php $this->formatedAge ?> </td></tr>
		<?php $this->endLoop(); ?> 
		</table>
	<?php } ?> 
	
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>