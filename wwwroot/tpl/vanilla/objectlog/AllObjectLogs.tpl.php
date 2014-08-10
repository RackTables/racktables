<?php if (defined("RS_TPL")) {?>

<br><table width='80%' align=center border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
<tr valign=top><th class=tdleft>Object</th><th class=tdleft>Date/user</th>
	<th class=tdcenter> <?php $this->get("Image_Href"); ?>  </th></tr>
	
	<?php $this->startLoop("LogTableData"); ?>
	<tr class=row_<?php $this->order ?> valign=top>
		<td class=tdleft> <?php $this->get("Object_id"); ?> </td>
		<td class=tdleft> <?php $this->get("User"); ?> <br> <?php $this->get("Date"); ?> </td>
		<td class="logentry"> <?php $this->get("Logentry"); ?> </td>
	</tr>
	<?php $this->endLoop(); ?>


</table>	

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>