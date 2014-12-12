<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Replace existing contents</h2>
		<?php $this->getH('PrintOpFormIntro',array('replaceFile', array (), TRUE)); ?>
		<input type=file size=10 name=file tabindex=100>&nbsp;
		<?php $this->getH('PrintImageHref',array('save', 'Save changes', TRUE, 101)); ?>
		</form>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>