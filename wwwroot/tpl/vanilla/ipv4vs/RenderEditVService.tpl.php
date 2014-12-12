<?php if (defined("RS_TPL")) {?>
	<?php $this->getH('PrintOpFormIntro', 'updIPv4VS'); ?>
	<table border=0 align=center>
	<tr><th class=tdright>VIP:</th><td class=tdleft><input tabindex=1 type=text name=vip value='<?php $this->Vip; ?>'></td></tr>
	<tr><th class=tdright>Port:</th><td class=tdleft><input tabindex=2 type=text name=vport value='<?php $this->Vport; ?>'></td></tr>
	<tr><th class=tdright>Proto:</th><td class=tdleft>
	<?php $this->Getselect; ?>
	</td></tr>
	<tr><th class=tdright>Name:</th><td class=tdleft><input tabindex=4 type=text name=name value='<?php $this->Name; ?>'></td></tr>
	<tr><th class=tdright>Tags:</th><td class=tdleft><?php $this->TagsPicker ?></td></tr>
	<tr><th class=tdright>VS config:</th><td class=tdleft><textarea tabindex=5 name=vsconfig rows=20 cols=80><?php $this->Vsconfig; ?></textarea></td></tr>
	<tr><th class=tdright>RS config:</th><td class=tdleft><textarea tabindex=6 name=rsconfig rows=20 cols=80><?php $this->Rsconfig; ?></textarea></td></tr>
	<tr><th class=submit colspan=2>
	<?php $this->getH('PrintImageHref', array('SAVE', 'Save changes', TRUE, 7)); ?>
	</td></tr>
	</table></form>
	
	<p class="centered">
	
	<?php $this->getH('GetOpLink', $this->_Refcnt > 0 ? array(NULL, 'Delete virtual service', 'nodestroy', "Could not delete: there are $this->Refcnt LB links") : array( array ('op' => 'del', 'id' => $this->_Id), 'Delete virtual service', 'destroy')); ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>