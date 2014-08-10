<?php if (defined("RS_TPL")) {?>

	<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
	<tr><th>change time</th><th>author</th><th>name</th><th>visible label</th><th>asset no</th><th>has problems?</th><th>comment</th></tr>
	<?php $this->Row; ?>
</table><br>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>