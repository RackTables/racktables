<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview cellspacing=0 cellpadding=0>
	<tr><td colspan=2 align=center><h1><?php $this->infoDName ?></h1></td></tr>
	<tr><td class=pcleft>
	<?php $this->InfoSummary ?>
	<?php $this->InfoComment ?>
	<?php if ($this->is("areLogRecords",true)) { ?>
	 	<div class=portlet>
	 		<h2>log records</h2>
	 		<table cellspacing=0 cellpadding=5 align=center class=widetable width='100%'>
	 		<?php $this->startLoop("allLogrecords"); ?>	
	 			<tr class=row_<?php $this->order ?> valign=top>
	 			<td class=tdleft><?php $this->date ?><br><?php $this->user ?></td>
	 			<td class="logentry"><?php $this->cont ?></td>
	 			</tr>
	 		<?php $this->endLoop(); ?> 
	 		</table>
	 	</div>
	<?php } ?> 
	<?php $this->switchportJS ?>
	<?php $this->filesPortlet ?>
	<?php if ($this->is("isInfoPorts",true)) { ?>
		<div class=portlet>
			<h2>ports and links</h2>
			<table cellspacing=0 cellpadding='5' align='center' class='widetable'>
			<tr><th class=tdleft>Local name</th><th class=tdleft>Visible label</th>
			<th class=tdleft>Interface</th><th class=tdleft>L2 address</th>
			<th class=tdcenter colspan=2>Remote object and port</th>
			<th class=tdleft>Cable ID</th></tr>
			<?php $this->RenderedObjectPorts ?>
			<?php if ($this->is("loadInplaceEdit", true)) { ?>
				<?php $this->addRequirement("Header","HeaderJsInclude",array("path"=>"js/inplace-edit.js")); ?>
			<?php } ?>
			</table> 
		</div>
	<?php } ?> 
	<?php if ($this->is("isInfoIP",true)) { ?>
		<div class=portlet>
			<h2>IP addresses</h2>
			<table cellspacing=0 cellpadding='5' align='center' class='widetable'>
			<?php if ($this->is("isExt_ipv4_view", true)) { ?>
				<tr class=tdleft><th>OS interface</th><th>IP address</th><th>network</th><th>routed by</th><th>peers</th></tr>
			<?php } else { ?>
                <tr class=tdleft><th>OS interface</th><th>IP address</th><th>peers</th></tr>
			<?php } ?>
			<?php $this->startLoop("allPorts"); ?>	
				<tr class='<?php $this->tr_class ?>' valign=top>
				<?php $this->FristMod ?>
				<?php $this->td_ip ?>
				<?php $this->td_network ?>
				<?php $this->td_routed_by ?>
				<?php $this->td_peers ?>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?> 
	<?php if ($this->is("isForwarding", true)) { ?>
		<div class=portlet>
			<h2>NATv4</h2>
			<?php if ($this->is("isFwdOut", true)) { ?>
				<h3>locally performed NAT</h3>
				<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>
				<tr><th>Proto</th><th>Match endpoint</th><th>Translate to</th><th>Target object</th><th>Rule comment</th></tr>
                <?php $this->startLoop("allOutFwds"); ?>	
                    <tr class='<?php $this->class ?>'>
                    <td><?php $this->proto ?></td><td class=tdleft><?php $this->oisf ?><?php $this->rendLocalIP ?></td>
                    <td class=tdleft><?php $this->rendRemoteIP ?></td>
                    <td class='description'>
                    <?php $this->mkAs ?>
                    <?php $this->RemAddrName ?>
                    </td><td class='description'><?php $this->description ?></td></tr>
                <?php $this->endLoop(); ?> 
				</table><br><br>
			<?php } ?> 
			<?php if ($this->is("isFwdIn",true)) { ?>
				<h3>arriving NAT connections</h3>
				<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>
				<tr><th>Matched endpoint</th><th>Source object</th><th>Translated to</th><th>Rule comment</th></tr>
				<?php $this->startLoop("allInFwds"); ?>	
					<tr>
						<td><?php $this->proto ?>/<?php $this->rendLocalIP ?></td>
						<td class="description"><?php $this->mkA ?></td>
						<td><?php $this->rendRemoteIP ?></td>
					    <td class='description'><?php $this->description ?></td></tr>
					</tr>
				<?php $this->endLoop(); ?> 
				</table><br><br>
			<?php } ?> 
		</div>
	<?php } ?>
	<?php $this->SlbTriplet2 ?>
	<?php $this->SlbTriplet ?>
	</td>
	<td class=pcright>
	<?php if ($this->is("isRackspacePortlet",true)) { ?>
		<div class=portlet>
			<h2>rackspace allocation</h2>
			<?php $this->renderedRackSpace ?>
			<br>
		</div>
	<?php } ?> 
	</td></tr>
	</table>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>