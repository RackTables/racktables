<?php if (defined("RS_TPL")) {?>
	<?php $this->getH("PrintOpFormIntro" , $this->_opFormIntroPara); ?> 
	
	<input type=hidden name=object_id value="<?php $this->object_id ?>">
	<input type=hidden name=vs_id value="<?php $this->vs_id ?>">
	<input type=hidden name=rspool_id value="<?php $this->rspool_id ?>">
	<p><label><input type=checkbox name=enabled<?php $this->isArray ?> > <?php $this->issetPortTxt ?> </label>

	<?php if ($this->is("issetPort",true)) { ?>
		<input type=hidden name=proto value="<?php $this->proto ?> ">
		<input type=hidden name=port value="<?php $this->vport ?> ">
	<?php } else {?>
		<input type=hidden name=vip value="<?php $this->vip ?> ">
		<p><label>Priority:<br><input type=text name=prio value="<?php $this->prio ?> "></label>
	<?php }?>
	
	<p><label>VS config:<br>
	<textarea name=vsconfig rows=3 cols=80><?php $this->vsconfig ?></textarea></label>
	<p><label>RS config:<br>
	<textarea name=rsconfig rows=3 cols=80><?php $this->rsconfig ?></textarea></label>
	<p align=center>
	<?php $this->getH("PrintImageHref", array('SAVE', 'Save changes', TRUE)); ?> 
	</form>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>