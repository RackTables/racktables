<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Munin servers (<?php $this->ServerCount ?>)</h2>
		<table cellspacing=0 cellpadding=5 align=center class=widetable>
		<tr><th>base URL</th><th>graph(s)</th></tr>
		<?php $this->startLoop("allServers"); ?>	
			<tr align=left valign=top><td><?php $this->NiftyStr ?></td>
			<td class=tdright><?php $this->NumGraphs ?></td></tr>
		<?php $this->endLoop(); ?> 
		</table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>