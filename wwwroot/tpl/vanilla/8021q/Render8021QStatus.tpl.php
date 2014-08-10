<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview cellspacing=0 cellpadding=0>	
	<tr valign=top><td class=pcleft width="40%">
	<?php if ($this->is("areVLANDomains",true)) { ?>
		<div class=portlet>
			<h2>no VLAN domains</h2>
		</div>
	<?php } else { ?> 
		<div class=portlet>
			<h2>VLAN domains (<?php $this->countVDList ?>)</h2>
			<table cellspacing=0 cellpadding=5 align=center class=widetable>
			<tr><th>description</th><th>VLANs</th><th>switches</th><th>
			<?php $this->getH("PrintImageHref", array( 'net')); ?></th><th>ports</th></tr>
			<?php $this->startLoop("vdListOut"); ?>	
				<tr align=left><td><?php $this->mkA ?></td>
				<?php $this->columnOut ?>
				</tr>  
			<?php $this->endLoop(); ?> 
			<?php if ($this->is("isVDList",true)) { ?>
				<tr align=left><td>total:</td>
				<?php $this->startLoop("TotalColumnOut"); ?>	
					<td><?php $this->cName ?> </td>
				<?php $this->endLoop(); ?> 
				</tr>
				</table>
			<?php } ?> 
		</div>
	<?php } ?>	

	</td><td class=pcleft width="40%">
	<?php if ($this->is("areVSTCells")){ ?>	
		<div class=portlet>
			<h2>no switch templates</h2>
		</div>
	<?php } else { ?> 
		<div class=portlet>
			<h2>switch templates (<?php $this->countVSTList ?>)</h2>
			<table cellspacing=0 cellpadding=5 align=center class=widetable>
			<tr><th>description</th><th>rules</th><th>switches</th></tr>
			<?php $this->startLoop("vstListOut"); ?>	
				<tr align=left valign=top><td>
				<?php $this->mkA ?>
				<?php $this->serializedTags ?>
				</td>
				<td><?php $this->rulec ?></td><td><?php $this->switchc ?></td></tr> 
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php  } ?> 
	</td><td class=pcright>
	<div class=portlet>
		<h2>deploy queues</h2>
		<table border=0 cellspacing=0 cellpadding=3 width="100%">
		<?php $this->startLoop("allDeployQueues"); ?>	
			<tr><th width="50%" class=tdright><?php $this->mkA ?></th>
			<td class=tdleft><?php $this->countItems ?></td></tr>  
		<?php $this->endLoop(); ?> 
		<tr><th width="50%" class=tdright>Total:</th>
		<td class=tdleft><?php $this->total ?></td></tr>
		</table>
	</div>
	</td></tr></table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>