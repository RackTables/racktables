<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2> <?php $this->title ?> </h2>
		<a class="toggleTreeMode" style="display:none" href="#"></a>
		<table border=0 cellspacing=0 cellpadding=3 align=center class="tagtree">
		<?php $this->getH("PrintOpFormIntro", array('saveTags')); ?> 
		<?php $this->get("TagCheckbox"); ?>
		<tr><td class=tdleft>
		<?php $this->getH("PrintImageHref", array('SAVE', 'Save changes', TRUE)); ?> 
		</form></td><td class=tdright>
		<?php if ($this->is("preSelect",false)) { ?>
			<?php $this->getH("PrintImageHref", array('CLEAR gray')); ?> 
		<?php } else {?> 
			<?php $this->getH("PrintOpFormIntro", array('saveTags', array ('taglist[]' => ''))); ?> 
			<?php $this->getH("PrintImageHref", array( 'CLEAR', 'Reset all tags', TRUE)); ?> 
			</form>
		<?php } ?>
		</td></tr></table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>