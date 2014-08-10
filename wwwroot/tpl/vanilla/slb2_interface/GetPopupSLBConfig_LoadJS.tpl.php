<?php if (defined("RS_TPL")) {?>
	<?php $this->addRequirement("Header","HeaderJsInclude",array("path"=>"js/jquery.thumbhover.js")); ?>
	<?php $this->addRequirement("Header","HeaderJsInline",array("code"=>"
		$(document).ready (function () {
	    $('.slbconf-btn').each (function () {
		$(this).thumbPopup($(this).siblings('.slbconf.popup-box'), { showFreezeHint: false });
	    });
	    });")); ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>