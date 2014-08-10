<?php if (defined("RS_TPL")) {?>
	<?php if ($this->is('NoList',true)) { ?>
		<td colspan=2>(configured VLAN domain is empty)</td>
	<?php } else { ?>
	<?php $this->Save8021QConfig ?>
	<td width="35%">
	<table border=0 cellspacing=0 cellpadding=3 align=center>
	<tr><th colspan=2>allowed</th></tr>
	<?php $this->startLoop('AllowedOptions'); ?>	
		<tr><td nowrap colspan=2 class='<?php $this->Class ?>'>
		<label><input type=checkbox name='pav_0[]' value='<?php $this->Vlan_Id ?>' <?php $this->Selected ?>>
		<?php $this->OptionTxt ?></label></td></tr>
	<?php $this->endLoop(); ?> 
	</table>
	</td><td width="35%">
	<table border=0 cellspacing=0 cellpadding=3 align=center>
	<tr><th colspan=2>native</th></tr>
	<?php if (!$this->is('Vlan_Port_Allowed', true)) { ?>
		<tr><td colspan=2>(no allowed VLANs for this port)</td></tr>
	<?php } else { ?>
		<?php $this->startLoop('NativeOpts'); ?>	
			<tr><td nowrap colspan=2 class='<?php $this->Class ?>'>
			<label><input type=radio name='pnv_0' value='<?php $this->Vlan_Id ?>' <?php $this->Selected ?>>
			<?php $this->OptionTxt ?></label></td></tr>
		<?php $this->endLoop(); ?> 
	<?php } ?>
	<tr><td class=tdleft>
	<?php $this->getH('PrintImageHref', array('SAVE', 'Save changes', TRUE)); ?>
	</form></td><td class=tdright>
	<?php if (!$this->is('Vlan_Port_Allowed', true)) { ?>
		<?php $this->getH('PrintImageHref', array('CLEAR gray')); ?>
	<?php } else { ?>
		<?php $this->Save8021QConfig ?>
		<?php $this->getH('PrintImageHref', array('CLEAR', 'Unassign all VLANs', TRUE)); ?>
		</form>
	<?php } ?>
	</td></tr></table>
	</td>
	<?php } ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>