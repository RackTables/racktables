<?php if (defined("RS_TPL")) {?>
	<table width='100%'><tr>
	<?php if ($this->is("isLinkStatus",true)) { ?>
		<td valign='top' width='50%'>
		<div class=portlet>
			<h2>Link status</h2>
			<table width='80%' class='widetable' cellspacing=0 cellpadding='5px' align='center'><tr><th>Port<th><th>Link status<th>Link info</tr>
			<?php $this->startLoop("allLinkStatus"); ?>	
				<tr class='row_<?php $this->order ?>'>
				<td><?php $this->pn ?><td><img width=16 height=16 src="?module=chrome&uri=pix/<?php $this->img_filename ?>">
				<td><?php $this->linkStatus ?>
				<td><?php $this->info ?>
				</tr>
			<?php $this->endLoop(); ?> 
			</table></td>
		</div>
	<?php } ?>
	<?php if ($this->is("hasMacList",true)) { ?>
		<td valign='top' width='50%'>
		<div class=portlet>
			<h2>Learned MACs (<?php $this->macCount ?>)</h2>
			<table width='80%' class='widetable' cellspacing=0 cellpadding='5px' align='center'><tr><th>MAC<th>Vlan<th>Port</tr>
			<?php $this->startLoop("allMacs"); ?>	
				<tr class='row_<?php $this->order ?>'>
				<td style="font-family: monospace"><?php $this->item ?>
				<td><?php $this->vid ?>
				<td><?php $this->pn ?>
				</tr>
			<?php $this->endLoop(); ?> 
			</table></td>		
		</div>
	<?php } ?> 
	</td></tr></table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>