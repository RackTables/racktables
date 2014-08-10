<?php if (defined("RS_TPL")) {?>
	<?php $this->addRequirement("Header","HeaderJsInline",array("code"=>"<<<END
$(document).ready(function() {
	var anchor = document.getElementsByName('<?php $this->_AnchorName ?>')[0];
	if (anchor)
		anchor.scrollIntoView(false);
});
END")); ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>