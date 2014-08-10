<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview>
	<tr><td class=pcleft>
	<div class=portlet>
		<h2>Clusters (<?php $this->countClusters ?>) </h2>
		<?php if ($this->is("areClusters",true)) { ?>
			<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Cluster</th><th>Hypervisors</th><th>Resource Pools</th><th>Cluster VMs</th><th>RP VMs</th><th>Total VMs</th></tr>
			<?php $this->startLoop("clustersArray"); ?>	
				<tr class=row_<?php $this->order ?> valign=top>
				<td class="tdleft"> <?php $this->mka ?> </td>
				<td class='tdleft'><?php $this->clusterHypervisor ?> </td>
				<td class='tdleft'><?php $this->clusterResPools ?> </td>
				<td class='tdleft'><?php $this->clusterVM ?> </td>
				<td class='tdleft'><?php $this->clusterResPoolVMs ?> </td>
				<td class='tdleft'><?php $this->totatlVMs ?> </td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		<?php } else { ?>
			<b>No clusters exist</b>
		<?php } ?> 
	</div>	

	</td><td class=pcright>
	<div class=portlet>
		<h2>Resource Pools (<?php $this->countResPools ?>) </h2>
		<?php if ($this->is("areResPools",true)) { ?>
			<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Pool</th><th>Cluster</th><th>VMs</th></tr>
			<?php $this->startLoop("poolsArray"); ?>	
				<tr class=row_<?php $this->order ?>  valign=top>
				<td class="tdleft"><?php $this->mka ?> </td>
				<td class="tdleft">
				<?php $this->clusterID ?> 
				</td>
				<td class='tdleft'><?php $this->poolVMs ?> </td>
				</tr>
			<?php $this->endLoop(); ?>
			</table> 
		<?php } else { ?>
			<b>No pools exist</b>
		<?php } ?>
	</div>
	</td></tr><tr><td class=pcleft>
	<div class=portlet>
		<h2>Hypervisors (<?php $this->hypervisorCount ?>) </h2>
		<?php if ($this->is("areHypervisors",true)) { ?>
			<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Hypervisor</th><th>Cluster</th><th>VMs</th></tr>
			<?php $this->startLoop("hypersArray"); ?>	
				<tr class=row_<?php $this->order ?>  valign=top>
				<td class="tdleft"><?php $this->mka ?> </td>
				<td class="tdleft">
				<?php $this->hyperID ?> 
				</td>
				<td class='tdleft'><?php $this->hyperVMs ?> </td>
				</tr>
			<?php $this->endLoop(); ?>
			</table> 
		<?php } else { ?>
			<b>No hypervisors exist</b>
		<?php } ?>
	</div>
	</td><td class=pcright>
	<div class=portlet>
		<h2>Virtual Switches (<?php $this->countSwitches ?>)</h2>
		<?php if ($this->is("areSwitches",true)) { ?>
			<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Name</th></tr>
			<?php $this->startLoop("switchesArray"); ?>	
				<tr class=row_<?php $this->order ?>  valign=top>
				<td class="tdleft"><?php $this->mka ?> </td>
				</tr>
			<?php $this->endLoop(); ?>
			</table> 
		<?php } else { ?>
			<b>No virtual switches exist</b>
		<?php } ?>
	</div>
	</td></tr></table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>