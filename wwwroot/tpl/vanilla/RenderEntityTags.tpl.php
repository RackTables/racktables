<?php if (defined("RS_TPL")) {?>

	<table border=0 width="100%"><tr>
		<?php $this->addRequirement("Header","HeaderJsInclude",array("path"=>"js/tag-cb.js")); ?>
		<?php $this->addRequirement("Header","HeaderJsInline",array("code"=>$this->_JsCode)); ?>
		<td class=pcright>
		<?php $this->RenderedEnityTags; ?> 
		</td>
		</tr></table>
		
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>