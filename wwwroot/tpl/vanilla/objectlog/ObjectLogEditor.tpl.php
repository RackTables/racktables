<?php if (defined("RS_TPL")) {?>
	<center><h2>Log records for this object (<a href=?page=objectlog>complete list</a>)</h2></center>
	<table with=80% align=center border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
		<?php $this->getH('PrintOpFormIntro','add'); ?>
		<tr valign=top class=row_odd>
			<td class=tdcenter><?php $this->getH('PrintImageHREF',array('CREATE', 'add record', TRUE, 101));?></td>
			<td><textarea name=logentry rows=10 cols=80 tabindex=100></textarea></td>
			<td class=tdcenter><?php $this->getH('PrintImageHREF',array('CREATE', 'add record', TRUE, 101));?></td>
		</tr>
		</form>
		<?php $this->Elements; ?>
	</table>
	<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>