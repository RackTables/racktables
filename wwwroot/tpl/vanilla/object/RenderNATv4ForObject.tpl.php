<?php if (defined("RS_TPL")) {?>
	<center><h2>locally performed NAT</h2></center>
	<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>
	<tr><th></th><th>Match endpoint</th><th>Translate to</th><th>Target object</th><th>Comment</th><th>&nbsp;</th></tr>
	<?php $this->printNewItemTop_mod ?>
	<?php $this->AllNatv4Ports ?>
	<?php $this->printNewItemBottom_mod ?>
	</table><br><br>
	<?php if ($this->is("hasFocusNat4",true)) { ?>
		<center><h2>arriving NAT connections</h2></center>
		<table class='widetable' cellpadding=5 cellspacing=0 border=0 align='center'>
		<tr><th></th><th>Source</th><th>Source objects</th><th>Target</th><th>Description</th></tr>
		<?php $this->startLoop("allNatv4Focus"); ?>	
			<tr><td><?php $this->opLink ?></td>
			<td><?php $this->proto ?>/<?php $this->focus_portpair_local_mod ?></td>
			<td class="description"><?php $this->mkA ?></td>
			<td><?php $this->focus_portpair_remote_mod ?></td>
			<td class='description'><?php $this->description ?></td></tr>
		<?php $this->endLoop(); ?> 
		</table><br><br>
	<?php } ?> 
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>