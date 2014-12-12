<?php if (defined("RS_TPL")) {?>
	<input type='text' data-tagit-valuename='<?php $this->Input_Name ?>' data-tagit='yes' placeholder='new tags here...' class='ui-autocomplete-input' autocomplete='off' role='textbox' aria-autocomplete='list' aria-haspopup='true'>
	<span title='show tag tree' class='icon-folder-open tagit_input_<?php $this->Input_Name ?>'></span>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>