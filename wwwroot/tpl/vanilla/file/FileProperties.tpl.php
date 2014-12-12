<?php if (defined("RS_TPL")) {?>
		<?php $this->getH('PrintOpFormIntro','updateFile'); ?>
			<table border=0 align=center>
			<tr>
				<th class=tdright>MIME-type:</th>
				<td class=tdleft><input tabindex=101 type=text name=file_type value='<?php $this->Type; ?>'></td>
			</tr>
			<tr>
				<th class=tdright>Filename:</th>
				<td class=tdleft><input tabindex=102 type=text name=file_name value='<?php $this->Name; ?>'></td>
			</tr>
			<tr>
				<th class=tdright>Comment:</th>
				<td class=tdleft><textarea tabindex=103 name=file_comment rows=10 cols=80><?php $this->Comment; ?></textarea></td>
			</tr>
			<tr><th class=tdright>Actions:</th><td class=tdleft>
				<?php $this->getH('GetOpLink',array(array ('op'=>'deleteFile', 'page'=>'files', 'tab'=>'manage', 'file_id'=>$this->_Id), '', 'destroy', 'Delete file', 'need-confirmation')); ?>
			</td></tr>
			<tr><th class=submit colspan=2>
				<?php $this->getH('PrintImageHREF',array('SAVE', 'Save changes', TRUE, 102)); ?>
		</th></tr></table></form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>