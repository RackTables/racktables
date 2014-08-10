<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Add new VS group</h2>
		<?php if ($this->is("isPrintOpFormIntro")) { ?>
			<?php $this->getH("PrintOpFormIntro", array('addLink')); ?>
		<?php } ?> 
		<table cellspacing=0 cellpadding=5 align=center>
		<tr valign=top><th class=tdright><?php $this->realm1Name ?></th><td class=tdleft>
		<?php $this->getH("PrintSelect", array( $this->_realm1List, $this->_realm1Opt)); ?>
		</td><td class=tdcenter valign=middle rowspan=2>
		<?php if ($this->is('isAdd')) { ?>
			<?php $this->getH("PrintImageHref", array('ADD', 'Configure LB', TRUE, 120)); ?>
		<?php } else { ?> 
			<?php $this->getH("PrintImageHref", array('DENIED', $this->_Message, FALSE)); ?>
		<?php } ?>
		<tr valign=top><th class=tdright><?php $this->realm2Name ?></th><td class=tdleft>
		<?php $this->getH("PrintSelect", array( $this->_realm2List, $this->_realm2Opt)); ?>
		</td></tr>
		</table></form>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>