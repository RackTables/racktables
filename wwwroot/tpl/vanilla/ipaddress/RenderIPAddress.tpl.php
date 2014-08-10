<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview cellspacing=0 cellpadding=0>
	<tr><td colspan=2 align=center><h1><?php $this->IP; ?></h1></td></tr>
	<tr>
		<td class=pcleft>
			<?php $this->EntitySummary; ?>
			<?php if($this->is('VSGListCount')||$this->is('VSListCount')||$this->is('RSPListCount')) { ?>
				<div class=portlet>
					<?php if ($this->is('VSGListCount')) { ?>
						<h2>virtual service groups (<?php $this->VSGListCount; ?>):</h2>
						<?php $this->SLBPortlet1; ?>
					<?php } ?>
					<?php if ($this->is('VSListCount')) { ?>
						<h2>virtual services (<?php $this->VSListCount; ?>):</h2>
						<?php $this->SLBPortlet2; ?>
					<?php } ?>
					<?php if ($this->is('RSPListCount')) { ?>
						<h2>RS pools (<?php $this->RSPListCount; ?>):</h2>
						<?php $this->SLBPortlet3; ?>
					<?php } ?>
				</div>
			<?php } ?>
		</td>
	<td class=pcright>
		<?php if ($this->is('Allocations')) { ?>
			<div class=portlet>
				<h2>allocations</h2>
				<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>
				<tr><th>object</th><th>OS interface</th><th>allocation type</th></tr>
				<?php $this->startLoop('Allocations'); ?>
					<tr class='tdleft <?php $this->AddrClass; ?> <?php $this->Highlight; ?>'>
						<td><?php $this->IPAllocLink ?></td>
						<td><?php $this->Name; ?></td>
						<td><strong><?php $this->Type; ?></strong></td>
					</tr>
				<?php $this->endLoop(); ?>
				</table><br><br>
			</div>
		<?php } ?>
		
		<?php if ($this->is('RSPools')) { ?>
			<div class=portlet>
				<h2>RS pools:</h2>
				<?php $this->startLoop('RSPools'); ?>
					<?php $this->Pool; ?>
					<br />
				<?php $this->endLoop(); ?>
			</div>
		<?php } ?>
		
		<?php $this->VSGList; ?>
		
		<?php $this->VSList; ?>
		
		<?php if ($this->is('NATDeparting')) { ?>
			<div class=portlet>
				<h2>departing NAT rules</h2>
				<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>
					<tr><th>proto</th><th>from</th><th>to</th><th>comment</th></tr>
					<?php while($this->loop('NATDeparting')) { ?>
						<tr>
							<td><?php $this->Proto; ?></td>
							<td><a href="<?php $this->FromLink; ?>"><?php $this->FromIp; ?></a>:<?php $this->FromPort; ?></td>
							<td><a href="<?php $this->ToLink; ?>"><?php $this->ToIp; ?></a>:<?php $this->ToPort; ?></td>
							<td><?php $this->Description; ?></td>
						</tr>
					<?php } ?>
				</table>
			</div>
		<?php } ?>
		
		<?php if ($this->is('NATArriving')) { ?>
			<div class=portlet>
				<h2>arriving NAT rules</h2>
				<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center' width='100%'>
					<tr><th>proto</th><th>from</th><th>to</th><th>comment</th></tr>
					<?php while($this->loop('NATArriving')) { ?>
						<tr>
							<td><?php $this->Proto; ?></td>
							<td><a href="<?php $this->FromLink; ?>"><?php $this->FromIp; ?></a>:<?php $this->FromPort; ?></td>
							<td><a href="<?php $this->ToLink; ?>"><?php $this->ToIp; ?></a>:<?php $this->ToPort; ?></td>
							<td><?php $this->Description; ?></td>
						</tr>
					<?php } ?>
					<?php $this->NATArriving; ?>
				</table>
			</div>
		<?php } ?>
	</td></tr>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>