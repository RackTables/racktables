<?php if (defined("RS_TPL")) {?>
	<table border=1><tr><th>tag</th><th>total</th><th>objects</th><th>IPv4 nets</th><th>IPv6 nets</th>
	<th>racks</th><th>IPv4 VS</th><th>IPv4 RS pools</th><th>users</th><th>files</th></tr>
	<?php $this->startLoop("allTags"); ?>	
		<tr>
		<td><?php $this->taginfo ?></td><td><?php $this->taginfoRefcnt ?></td>
		<?php $this->realms ?>
		</tr>
	<?php $this->endLoop(); ?> 
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>