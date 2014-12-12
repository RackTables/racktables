<?php if (defined("RS_TPL")) {?>
	<?php $this->getH('PrintOpFormIntro', array('updIP', array ('ip' => $this->_Fmt_ip))); ?>
		<p align=center>
		<?php $this->getH("GetOpLink", array( array ('op' => 'delIP', 'ip' => $this->_Fmt_ip), $this->_Title, 'destroy', '', ($this->_Used ? 'del-used-slb' : ''))); ?>
		<p><label>VS config:<br>
		<textarea name=vsconfig rows=3 cols=80><?php $this->Vsconfig ?></textarea></label>
		<p><label>RS config:<br>
		<textarea name=rsconfig rows=3 cols=80><?php $this->Rsconfig ?></textarea></label>
		<p align=center><?php $this->getH('PrintImageHref', array('SAVE', 'Save changes', TRUE)); ?>
	</form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>