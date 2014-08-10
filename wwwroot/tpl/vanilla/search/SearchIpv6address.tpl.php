<?php if (defined("RS_TPL")) {?>
	<div class=portlet><h2> <?php $this->sectionHeader ?></h2>
		<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
		<tr><th>Address</th><th>Description</th></tr>
		<?php $this->AllSearchAddrs ?>
		</table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>