<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Upload new</h2>
		<?php $this->getH('PrintOpFormIntro',array('addFile', array (), TRUE)); ?>
		<table border=0 cellspacing=0 cellpadding='5' align='center'>
			<tr><th class=tdright>Comment:</th><td class=tdleft><textarea tabindex=101 name=comment rows=10 cols=80></textarea></td></tr>
			<tr><th class=tdright>Tags:</td><td class=tdleft>
				<?php $this->TagsPicker; ?>
			</td></tr>
			<tr><th class=tdright>File:</th><td class=tdleft><input type='file' size='10' name='file' tabindex=100></td></td>
			<tr><td colspan=2>
				<?php $this->getH('PrintImageHREF',array('CREATE', 'Upload file', TRUE, 102)); ?>
			</td></tr>
			</table></form><br>
	</div>		
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>