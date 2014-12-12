<?php if (defined("RS_TPL")) {?>

	<table cellspacing=0 cellpadding=5 align=center class=widetable>
	<tr><th>&nbsp;</th><th>description</th><th>&nbsp;</th></tr>
	
	<?php 
	if($this->is("AddTop", true)) : ?>

	<?php $this->getH('PrintOpFormIntro', 'add'); ?>
	<tr>
	<td> <?php $this->getH('PrintImageHref', array('create', 'create template', TRUE, 104)); ?> </td>
	<td><input type=text size=48 name=vst_descr tabindex=101></td>
	<td> <?php $this->getH('PrintImageHref', array('create', 'create template', TRUE, 103)); ?> </td>
	</tr></form>

	<?php endif;

	while($this->Loop("VstList")) : ?>
			<?php $this->getH('PrintOpFormIntro', array('upd', array ('vst_id' => $this->_Vst_id))); ?>
			<tr><td>
			<?php if($this->is('Switchc_Set', TRUE)) $this->getH('PrintImageHref', array('nodestroy', 'template used elsewhere')); 
				else $this->getH('GetOpLink', array(array ('op' => 'del', 'vst_id' => $this->_Vst_Id), '', 'destroy', 'delete template')); ?>
			</td>
			<td><input name=vst_descr type=text size=48 value="<?php $this->NiftyString; ?>"></td>
			<td> <?php $this->ImageHref; ?> </td>
			</tr></form>

	<?php endwhile;

	if($this->is("AddTop", false)) :
	?>
	<?php $this->getH('PrintOpFormIntro', 'add'); ?>
	<tr>
	<td> <?php $this->getH('PrintImageHref', array('create', 'create template', TRUE, 104)); ?> </td>
	<td><input type=text size=48 name=vst_descr tabindex=101></td>
	<td> <?php $this->getH('PrintImageHref', array('create', 'create template', TRUE, 103)); ?> </td>
	</tr></form>

<?php endif ?>
	
	
	</table>



<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>