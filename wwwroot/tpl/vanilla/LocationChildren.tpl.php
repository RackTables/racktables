<?php if (defined("RS_TPL")) {?>
	<option value=<?php $this->Id; ?> <?php $this->Selected; ?>>
		<?php $this->Content; ?>
	</option>
	<?php $this->Options; ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>

