<?php if (defined("RS_TPL")) {?>

<div class=portlet><h2><a href='index.php?page=depot'>Objects</a></h2>
	<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
		<tr><th>what</th><th>why</th></tr>

		<?php $this->foundObject ?>
	</table>
</div>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>