<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Add new VS group</h2>
		<?php $this->getH("PrintOpFormIntro", array('add')); ?>
		<table border=0 cellpadding=5 cellspacing=0 align=center>
		<tr valign=bottom><th>Name:</th><td class="tdleft">
		<input type=text name=name></td></tr>
		<tr><th>Tags:</th><td class="tdleft">
		<?php $this->TagsPicker ?>
		</td></tr>
		</table>
		<?php $this->getH("PrintImageHref", array('CREATE', 'create virtual service', TRUE)); ?>
		</form>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>