<?php if (defined("RS_TPL")) {?>
	<?php $this->getH('PrintOpFormIntro', 'add'); ?>
	<tr><td colspan=2 class=tdleft>
	<select name=attr_id tabindex=100>
	<?php $this->startLoop('AllAttrMaps'); ?>
		<option value=<?php $this->Id; ?> >[<?php $this->Shorttype; ?>] <?php $this->Name; ?></option>
	<?php $this->endLoop(); ?>
	</select></td><td class=tdleft>
	<?php $this->getH('PrintImageHref', array('add', '', TRUE)); echo ' '; 
		  $this->Getselect;	?>
   <select name=chapter_no tabindex=102><option value=0>-- dictionary chapter for [D] attributes --</option>
	<?php $this->startLoop('AllChapters'); ?>
		<option value='<?php $this->Id; ?>'><?php $this->Name; ?></option>
	<?php $this->endLoop(); ?>
		</select></td></tr></form>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>