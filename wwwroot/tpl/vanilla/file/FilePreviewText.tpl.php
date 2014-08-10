<?php if (defined("RS_TPL")) {?>
	<textarea readonly rows=<?php $this->Rows; ?> cols=<?php $this->Cols; ?>>
		<?php $this->Content; ?>
	</textarea>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>