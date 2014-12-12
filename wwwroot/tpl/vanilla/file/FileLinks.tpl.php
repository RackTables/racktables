<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Links (<?php $this->Count; ?>)</h2>
		<table cellspacing=0 cellpadding='5' align='center' class='widetable'>
		<?php $this->Links ?>
		</table>
	</div>		
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>