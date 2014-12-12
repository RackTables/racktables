<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview cellspacing=0 cellpadding=0>
	<tr><td colspan=2 align=center><h1><?php $this->formatVlanTxt ?></h1></td></tr>
	<tr><td class=pcleft width='50%'>
	<div class=portlet>
		<h2>summary</h2>
		<table border=0 cellspacing=0 cellpadding=3 width='100%'>
		<tr><th width='50%' class=tdright>Domain:</th><td class=tdleft>
		<?php $this->niftyStr_domain_descr ?></td></tr>
		<tr><th width='50%' class=tdright>VLAN ID:</th><td class=tdleft><?php $this->vlan_id ?></td></tr>
		<?php if ($this->is("isVlan_Descr", true)) { ?>
			<tr><th width='50%' class=tdright>Description:</th><td class=tdleft>
			<?php $this->niftyStr_vlan_descr ?></td></tr>
		<?php } ?> 
		<tr><th width='50%' class=tdright>Propagation:</th><td class=tdleft><?php $this->vtoptions ?></td></tr>
		<?php $this->startLoop("allOthers"); ?>	
			<tr><th class=tdright>Counterpart:</th><td class=tdleft>
			<?php $this->vlanHyperlinks ?>
			</td></tr>
		<?php $this->endLoop(); ?> 
		</table>
	</div>
	<?php if ($this->is("noNetworks",true)) { ?>
		<div class=portlet>
			<h2>no networks</h2>
		</div>
	<?php } else { ?> 
		<div class=portlet>
			<h2>networks (<?php $this->overallCount ?>)</h2>
			<table cellspacing=0 cellpadding=5 align=center class=widetable>
			<tr><th>
			<?php $this->getH("PrintImageHref", array('net')); ?>
			</th><th><?php $this->getH("PrintImageHref", array('text')); ?>
			</th></tr>
			<?php $this->startLoop("allNets"); ?>	
				<tr><td>
				<?php $this->renderedCell ?>
				</td><td><?php $this->niftyStr ?></td></tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?> 
	<?php if ($this->is("nonSwitchDev",true)) { ?>
		<div class=portlet>
			<h2>Non-switch devices</h2>
			<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>device</th><th>ports</th></tr>
			<?php $this->startLoop("allForgDev"); ?>	
				<tr class=row_<?php $this->order ?> valign=top><td>
				<?php $this->rendCell ?>
				</td><td><ul>
				<?php $this->ports ?>
				</ul></td></tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?> 
	</td><td class=pcright>
	<?php if ($this->is("noPorts",true)) { ?>
		<div class=portlet>
			<h2>no ports</h2>
		</div>
	<?php } else { ?> 
		<div class=portlet>
			<h2>Switch ports (<?php $this->countPorts ?>)</h2>
			<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>switch</th><th>ports</th></tr>
			<?php $this->startLoop("allConfports"); ?>	
				<tr class=row_<?php $this->order ?> valign=top><td>
				<?php $this->rendCell ?>
				</td><td class=tdleft><ul>
				<?php $this->portlist ?>
				</ul></td></tr>
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