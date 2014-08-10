<?php if (defined("RS_TPL")) {?>
<tr class=row_<?php $this->Order; ?>><td class=tdleft><?php $this->Name; ?></td>
	<td class=tdleft><?php $this->AttrTypes; ?></td><td colspan=2 class=tdleft>
	<?php $this->AllAttrApps ?>	 
</td></tr>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>