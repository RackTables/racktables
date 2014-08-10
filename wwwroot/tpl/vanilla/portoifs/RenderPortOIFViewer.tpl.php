<?php if (defined("RS_TPL")) {?>
	<br><table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>
	<tr><th>Origin</th><th>Key</th><th>Refcnt</th><th>Outer Interface</th></tr>
	<?php while($this->loop('AllOptions')) : ?>	
		<tr class=row_<?php $this->Order ?>>
		<td class=tdleft><?php $this->ImageHref ?></td>
		<td class=tdright><?php $this->Oif_id ?></td>
		<td class=tdright><?php $this->Refcnt ?></td>
		<td class=tdleft><?php $this->NiftyString ?></td>
		</tr>
	<?php endwhile ?> 
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>