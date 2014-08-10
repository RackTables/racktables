<?php if (defined("RS_TPL")) {?>

<table border=0 class=objectview cellspacing=0 cellpadding=0>
<tr><td colspan=2 align=center><h1><?php $this->getH('NiftyString', array($this->_VstDescription, 30)) ?></h1><h2>
<tr><td class=pcleft width='50%'>
<?php $this->EntitySummary ?>
<?php $this->VstRules ?>
</td><td class=pcright>
<?php if($this->is('EmptySwitches', true)) {?> <div class=portlet><h2>no orders</h2> <?php } 

		else {  ?>
				<div class=portlet><h2>orders ( <?php echo count($this->_Switches); ?> )</h2>
				<table cellspacing=0 cellpadding=5 align=center class=widetable>
			<?php $this->startLoop('Order_id_array') ?>
				<tr class=row_<?php $this->Order; ?>><td>
				<?php $this->Render_cell; ?>
				</td></tr>
				<?php $this->endLoop(); ?>
				</table> <?php } ?>
				</div>
				</td></tr></table>
				
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>