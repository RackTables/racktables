<?php if (defined("RS_TPL")) {?>
	<?php $this->addRequirement("Header","HeaderJsInclude",array("path"=>"js/codemirror/codemirror.js")); ?>
	<?php $this->addRequirement("Header","HeaderJsInclude",array("path"=>"js/codemirror/rackcode.js")); ?>
	<?php $this->addRequirement("Header","HeaderCssInclude",array("path"=>"js/codemirror/codemirror.css")) ; ?>
	<?php $this->addRequirement("Header","HeaderJsInline",array("code"=>$this->_jsRawCode)); ?>

	<?php $this->getH("PrintOpFormIntro", array('saveRackCode')); ?> 
	<table style="width:100%;border:1px;" border=0 align=center>
	<tr><td><textarea rows=40 cols=100 name=rackcode id=RCTA class='codepress rackcode'><?php $this->text ?></textarea></td></tr>
	<tr><td align=center>
	<div id="ShowMessage"></div>
	<input type='button' value='Verify' onclick='verify();'>
	<input type='submit' value='Save' disabled='disabled' id='SaveChanges' onclick='$(RCTA).toggleEditor();'>
	</td></tr>
	</table>
	</form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>