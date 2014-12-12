<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview cellspacing=0 cellpadding=0>
	<tr>
		<td class=pcleft>
		<div class=portlet>
			<h2>Old allocation</h2>
			<?php $this->OldAlloc; ?>
		</div>
		</td>
		<td class=pcright>
		<div class=portlet>
			<h2>New allocation</h2>
			<?php $this->NewAlloc; ?>
		</div>
		</td>	
	</tr>
	<tr>
		<td colspan=2>
		<div class=portlet>
			<h2>Rackspace allocation history</h2>
			<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
				<tr><th>timestamp</th><th>author</th><th>object</th><th>comment</th></tr>
				<?php $this->HistoryRows; ?>
			</table>
		</div>
		</td>
	</tr>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>