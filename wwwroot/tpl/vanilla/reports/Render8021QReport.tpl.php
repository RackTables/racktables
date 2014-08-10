<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>VLAN existence per domain</h2>
		<table border=1 cellspacing=0 cellpadding=5 align=center class=rackspace>
		<?php $this->startLoop("OutputArr"); ?>	
			<?php $this->Header; ?>
			<tr class="state_<?php $this->CountStats ?>"><th class=tdright><?php $this->VlanId ?></th>
			<?php $this->Domains; ?> 
			</tr>
			<?php $this->TbcLine ?>  
		<?php $this->endLoop(); ?> 
		</table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>