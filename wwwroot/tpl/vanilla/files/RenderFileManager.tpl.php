<?php if (defined("RS_TPL")) {?>
	<?php if($this->is('NewTop')) { ?>
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
	<?php } ?>
	<?php if($this->is('FileList')) { ?>
	<div class=portlet>
		<h2>Manage existing (<?php $this->Count; ?>)</h2>
		<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>File</th><th>Unlink</th><th>Destroy</th></tr>
			<?php while($this->loop('FileList')) { ?>
				<tr class=row_<?php $this->Order; ?> valign=top>
				<td class=tdleft><?php $this->Cell; ?></td>
				<td class=tdleft><?php $this->Links; ?></td>
				<td class=tdcenter valign=middle>
					<?php if($this->is('Deletable')) { ?>
						<?php $this->getH('GetOpLink',array(array('op'=>'deleteFile', 'file_id'=>$this->_Id), '', 'DESTROY', 'Delete file', 'need-confirmation')); ?>
					<?php } else { ?>
						<?php $this->getH('PrintImageHref',array('NODESTROY', 'References (' . $this->_Count . ')'))?>
					<?php } ?>
				</td>
				</tr>
			<?php } ?>
		</table>
	</div>		
	<?php } ?>
	<?php if(!$this->is('NewTop')) { ?>
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
	<?php } ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>