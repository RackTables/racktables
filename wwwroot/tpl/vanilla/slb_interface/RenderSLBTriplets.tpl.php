<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2> VS instances (<?php $this->countTriplets ?>) </h2>
		<table cellspacing=0 cellpadding=5 align=center class=widetable><tr>
			<?php $this->startLoop("cellRealmHeaders"); ?>	
				<th><?php $this->header ?> </th>
			<?php $this->endLoop(); ?> 

			<?php $this->startLoop("cellHeaders"); ?>	
				<th><?php $this->header ?> </th>
			<?php $this->endLoop(); ?> 
			
			<?php $this->startLoop("tripletsOutArray"); ?>	
				<tr valign=top class='row_<?php $this->order ?>  triplet-row'>
				<?php $this->cellsOutput ?>
				<td class=slbconf> <?php $this->vsconfig ?> </td>
				<td class=slbconf> <?php $this->rsconfig ?> </td>
				<td class=slbconf> <?php $this->prio ?> </td>	 
				</tr>
			<?php $this->endLoop(); ?> 
		</table>
	</div>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>