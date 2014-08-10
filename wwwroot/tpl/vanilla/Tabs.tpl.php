<?php if (defined("RS_TPL")) {?>
	<div class=greynavbar>
		<ul id=foldertab style='margin-bottom: 0px; padding-top: 10px;'>
			<?php $this->Tabs; ?>
		</ul>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>