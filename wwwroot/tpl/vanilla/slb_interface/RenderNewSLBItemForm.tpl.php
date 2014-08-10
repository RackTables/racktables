<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Add new</h2>
		<table cellspacing=0 cellpadding=5 align=center>
		<?php if ($this->is("printOpFormIntro", true)) { ?>
			<?php $this->getH("PrintOpFormIntro", array('addLB')); ?>
		<?php } ?> 
		
		<tr valign=top><th class=tdright><?php $this->realm1Name ?></th><td class=tdleft>
		<?php $this->getH("PrintSelect", array( $this->_realm1List, $this->_realm1Opt)); ?>
		</td><td class=tdcenter valign=middle rowspan=2>

		<?php if ($this->is('isAdd', true)) { ?>
			<?php $this->getH("PrintImageHref", array('ADD', 'Configure LB', TRUE, 120)); ?>
		<?php } else { ?> 
			<?php $this->getH("PrintImageHref", array('DENIED', $this->_Message, FALSE)); ?>
		<?php } ?>
		<tr valign=top><th class=tdright><?php $this->realm2Name ?></th><td class=tdleft>
		<?php $this->getH("PrintSelect", array( $this->_realm2List, $this->_realm2Opt)); ?>
		</td></tr>
		<tr><th class=tdright>VS config</th><td colspan=2><textarea tabindex=110 name=vsconfig rows=10 cols=80></textarea></td></tr>
		<tr><th class=tdright>RS config</th><td colspan=2><textarea tabindex=111 name=rsconfig rows=10 cols=80></textarea></td></tr>
		<tr><th class=tdright>Priority</th><td class=tdleft colspan=2><input tabindex=112 name=prio size=10></td></tr>
		</table></form>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>