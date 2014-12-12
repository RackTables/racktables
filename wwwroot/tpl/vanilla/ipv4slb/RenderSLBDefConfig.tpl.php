<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>SLB default configs</h2>
		<table cellspacing=0 cellpadding=5 align=center>
		<?php $this->getH("PrintOpFormIntro", array('save')); ?>
		<tr><th class=tdright>VS config</th><td colspan=2><textarea tabindex=103 name=vsconfig rows=10 cols=80><?php $this->htmlspecVSconfig ?></textarea></td>
		<td rowspan=2>
		<?php $this->getH("PrintImageHref", array('SAVE', 'Save changes', TRUE)); ?>
		</td></tr>
		<tr><th class=tdright>RS config</th><td colspan=2><textarea tabindex=104 name=rsconfig rows=10 cols=80><?php $this->htmlspecRSconfig ?></textarea></td>
		</form></table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>