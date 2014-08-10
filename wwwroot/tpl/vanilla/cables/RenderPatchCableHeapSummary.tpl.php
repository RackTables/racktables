<?php if (defined("RS_TPL")) {?>
<div class=portlet>
	<h2>Heaps</h2>
	<br><table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>
	<tr><th>Amount</th><th>End 1</th><th>Cable type</th><th>End 2</th><th>Length</th><th>Description</th><th>&nbsp;</th></tr>
	<?php while($this->refLoop('AllHeaps')) : ?>	
		<tr class=row_<?php $this->Order ?>>
		<td class=tdright><?php $this->HeapAmount ?></td>
		<td class=tdleft><?php $this->HeapEndCon1 ?></td>
		<td class=tdleft><?php $this->HeapPCType ?></td>
		<td class=tdleft><?php $this->HeapEndCon2 ?></td>
		<td class=tdright><?php $this->HeapLength ?></td>
		<td class=tdleft><?php $this->HeapDesc ?></td>
		<td><?php $this->HeapPatchCalbeLength ?></td>
		</tr>
	<?php endwhile ?> 
	</table>
</div>
<?php if ($this->is('ZoomOrEventLog')) { ?>
	<div class=portlet>
		<h2>Event log</h2>
		<br><table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>
		<tr><th>Date</th><th>User</th><th>Message</th></tr>
		<?php while($this->startLoop('AllEvents')) : ?>	
			<tr class=row_<?php $this->Order ?>>
			<td class=tdleft><?php $this->EventDate ?></td>
			<td class=tdleft><?php $this->EventUser ?></td>
			<td class=tdleft><?php $this->EventMessage ?></td>
			</tr>
		<?php endwhile ?> 
		</table>
	</div>
<?php } ?> 

	
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>