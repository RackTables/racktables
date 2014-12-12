<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Add new RS pool</h2>
		<?php $this->getH("PrintOpFormIntro", array('add')); ?>
		<table border=0 cellpadding=5 cellspacing=0 align=center>
		<tr><th class=tdright>Name:</th>
		<td class=tdleft><input type=text name=name tabindex=101></td><td>
		</td></tr><th class=tdright>Tags:</th><td class='tdleft'>
		<?php $this->TagsPicker ?>
		</td></tr>
		<tr><th class=tdright>VS config</th><td colspan=2><textarea name=vsconfig rows=10 cols=80 tabindex=102></textarea></td></tr>
		<tr><th class=tdright>RS config</th><td colspan=2><textarea name=rsconfig rows=10 cols=80 tabindex=103></textarea></td></tr>
		<tr><td colspan=2>
		<?php $this->getH("PrintImageHref", array('CREATE', 'create real server pool', TRUE, 104)); ?>
		</td></tr>
		</table></form>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>