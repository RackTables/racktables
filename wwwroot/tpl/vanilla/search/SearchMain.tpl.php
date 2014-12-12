<?php if (defined("RS_TPL")) {?>

	<center><h2> <?php $this->get("NHITS"); ?> result(s) found for '<?php $this->get("TERMS"); ?>'</h2></center>
	<?php $this->get("FoundItems"); ?>
	
	<?php //If no type declarated show default
	 if($this->is("FoundItems", null)){
		$this->whatCont;
		} ?>


<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>