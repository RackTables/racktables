<?php if (defined("RS_TPL")) {?>
	<tr class=row_<?php $this->Order; ?> valign=top><td class=tdleft>
		<?php $this->Cell; ?>
	</td><td class=tdleft>
		<?php $this->Links; ?>
	</td>
		<td class=tdcenter valign=middle>
		<?php if($this->is('Deletable')) { ?>
			<?php $this->getH('GetOpLink',array(array('op'=>'deleteFile', 'file_id'=>$this->_Id), '', 'DESTROY', 'Delete file', 'need-confirmation')); ?>
		<?php } else { ?>
			<?php $this->getH('PrintImageHref',array('NODESTROY', 'References (' . $this->_Count . ')'))?>
		<?php } ?>
	</td>
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>