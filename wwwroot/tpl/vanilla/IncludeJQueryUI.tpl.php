<?php if (defined("RS_TPL")) {?>
	<?php $this->addJS ('js/jquery-1.4.4.min.js');?>
	<?php $this->addJS ('js/jquery-ui-1.8.21.min.js');?>
	<?php if ($this->is("Do_css",true)) { 
		$this->addCSS ('css/jquery-ui-1.8.22.redmond.css');
	} ?> 
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>