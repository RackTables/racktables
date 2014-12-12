<?php if (defined("RS_TPL")) {?>
	<?php $this->JQuery ?>
	<?php $this->addCSS ('css/tagit.css');?>
	<?php $this->addJS ('js/tag-it.js');?>
	<?php $this->addJS ('js/tag-it-local.js');?>
	<?php if ($this->is('Taglist')) { 
		$this->addJS ('var taglist = ' . $this->_Taglist . ' ;', TRUE);
	} ?> 
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>