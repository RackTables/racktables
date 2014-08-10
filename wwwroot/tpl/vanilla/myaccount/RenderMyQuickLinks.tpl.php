<?php if (defined("RS_TPL")) {?>

	<div class=portlet><h2>Items to display in page header</h2>
		<div style="text-align: left; display: inline-block;">
		<?php $this->getH('PrintOpFormIntro', 'save'); ?>
		<ul class="qlinks-form">
		<?php $this->startLoop('LoopArray'); ?>
			<li><label><input type='checkbox' name='page_list[]' value='<?php $this->PageNo; ?>' <?php $this->CheckedState; ?>> <?php $this->PageName; ?></label></li>
		<?php $this->endLoop(); ?>
		</ul>
		<?php $this-> getH('PrintImageHref', array('SAVE', 'Save changes', TRUE)); ?>
		</form></div></div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>