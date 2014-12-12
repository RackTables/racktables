<?php if (defined("RS_TPL")) {?>
	<div class=portlet><h2>Cacti servers (<?php $this->Count; ?>)</h2>
	<table cellspacing=0 cellpadding=5 align=center class=widetable>
		<tr><th>base URL</th><th>username</th><th>graph(s)</th></tr>
		<?php while($this->loop("ServerAttributes")) : ?>
			<tr align=left valign=top>
				<td><?php $this->BaseUrl; ?></td>
				<td><?php $this->Username; ?></td>
				<td class=tdright><?php $this->NumGraphs; ?></td>
			</tr>

		<?php endwhile ?></table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>