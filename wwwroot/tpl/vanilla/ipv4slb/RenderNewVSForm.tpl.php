<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Add new virtual service</h2>
		<?php $this->getH("PrintOpFormIntro", array('add')); ?>
		<table border=0 cellpadding=10 cellspacing=0 align=center>
		<tr><th class=tdright>VIP:</th><td class=tdleft><input type=text name=vip tabindex=101></td>
		<tr><th class=tdright>Port:</th><td class=tdleft>	
		<input type=text name=vport size=5 value='<?php $this->Default_port ?>' tabindex=102></td></tr>
		<tr><th class=tdright>Proto:</th><td class=tdleft>
		<?php $this->getH('PrintSelect',array( $this->_Vs_proto, array ('name' => 'proto'), array_shift (array_keys ($this->_vs_keys))))?>
		</td></tr>";
		<tr><th class=tdright>Name:</th><td class=tdleft><input type=text name=name tabindex=104></td><td>
		<tr><th class=tdright>Tags:</th><td class=tdleft>
		<?php $this->TagsPicker ?>
		</td></tr>
		<tr><th class=tdrigh>VS configuration:</th><td class=tdleft><textarea name=vsconfig rows=10 cols=80></textarea></td></tr>
		<tr><th class=tdrigh>RS configuration:</th><td class=tdleft><textarea name=rsconfig rows=10 cols=80></textarea></td></tr>
		<tr><td colspan=2>
		<?php $this->getH("PrintImageHref", array('CREATE', 'create virtual service', TRUE, 105)); ?>
		</td></tr>
		</table></form>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>