<?php if (defined("RS_TPL")) {?>

	<table border=0 class=objectview cellspacing=0 cellpadding=0>
	<tr><td class=pcleft width="50%">
	<div class=portlet><h2>schedule</h2>
	<?php
		$this->Sync_Schedule;
	 ?>
	 </div>
	<div class=portlet><h2>preview legend</h2>
		<table cellspacing=0 cellpadding=5 align=center class=widetable>
	<tr><th>status</th><th width="50%">color code</th></tr>
	<tr><td class=tdright>with template role:</td><td class=trbusy>&nbsp;</td></tr>
	<tr><td class=tdright>without template role:</td><td>&nbsp;</td></tr>
	<tr><td class=tdright>new data:</td><td class=trok>&nbsp;</td></tr>
	<tr><td class=tdright>warnings in new data:</td><td class=trwarning>&nbsp;</td></tr>
	<tr><td class=tdright>fatal errors in new data:</td><td class=trerror>&nbsp;</td></tr>
	<tr><td class=tdright>deleted data:</td><td class=trnull>&nbsp;</td></tr>
	</table>
	</div>

	<?php 
	if($this->is('Considerconfiguratedconstraint', TRUE)){

		?> <div class=portlet><h2>add/remove 802.1Q ports</h2>
		<?php $this->Sync_Ports; ?>
			</div>
		<?php }	?>

		</td><td class=pcright>
		<div class=portlet><h2>sync plan live preview</h2>
		<?php 
			if($this->is('R_Set', TRUE)){
				$this->Sync_Preview;
			}
			else{
				?> <p class=row_error>gateway error: <?php $this->Error; ?></p> <?php
			}
		?>

		</div></td></tr></table>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>