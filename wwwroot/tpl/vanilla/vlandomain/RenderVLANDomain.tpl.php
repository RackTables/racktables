<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview cellspacing=0 cellpadding=0>
	<tr><td colspan=2 align=center><h1><?php $this->niftyStr ?></h1></td></tr> 
	<tr><td class=pcleft width='50%'>
	<?php if ($this->is("areDomains", false)) { ?>
		<div class=portlet>
			<h2>no orders</h2>
		</div>
	<?php } else { ?> 
		<div class=portlet>
			<h2>orders (<?php $this->countDomains ?>)</h2>
			<table cellspacing=0 cellpadding=5 align=center class=widetable>
			<tr><th>switch</th><th>template</th><th>status</th></tr>
			<?php $this->startLoop("allDomainSwitch"); ?>	
				<tr class=row_<?php $this->order ?> ><td>
				<?php $this->renderedCell ?> 
				</td><td class=tdleft><?php $this->vstlist ?></td><td>
				<?php $this->imageHREF ?>  
				</td></tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?>
	</td><td class=pcright>
	<?php if ($this->is("areVLANDomains",false)) { ?>
		<div class=portlet>
			<h2>no VLANs</h2>
		</div>
	<?php } else { ?> 
		<div class=portlet>
			<h2>VLANs (<?php $this->countMyVLANs ?>)</h2>
			<table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>
			<tr><th>VLAN ID</th><th>propagation</th><th>
			<?php $this->getH("PrintImageHref", array('net', 'IPv4 networks linked')); ?> 
			</th><th>ports</th><th>description</th></tr>
			<?php $this->startLoop("allMyVLANs"); ?>	
				<tr class=row_<?php $this->order ?>>
				<td class=tdright><?php $this->mkA ?></td>
				<td><?php $this->vtdecoder ?></td>
				<td class=tdright><?php $this->infoNetc ?></td>
				<td class=tdright><?php $this->infoPortc ?></td>
				<td class=tdleft><?php $this->infoDescr ?></td>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?>
	</td></tr></table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>