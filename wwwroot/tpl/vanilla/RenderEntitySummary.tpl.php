<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2> <?php $this->Title ?> </h2>
			<table border=0 cellspacing=0 cellpadding=3 width='100%'>
				<?php $this->LoopMod ?>
			</table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>