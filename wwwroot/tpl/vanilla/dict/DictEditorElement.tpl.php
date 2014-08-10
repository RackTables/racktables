<?php if (defined("RS_TPL")) {?>
	<?php $this->getH('PrintOpFormIntro',array(array('chapter_no'=>$this->_ChapterId))); ?>
		<tr>
			<td>
				<?php if(!$this->is('NoDestroyMessage','')) { ?>
					<?php $this->getH('PrintImageHREF',array('nodestroy',$this->_NoDestroyMessage)); ?>
				<?php } else { ?>
					<?php $this->getH('GetOpLink',array(array('op'=>'del', 'chapter_no'=>$this->_ChapterId), '', 'destroy', 'Remove chapter')); ?>
				<?php } ?>
			</td>
			<td><input type=text name=chapter_name value='<?php $this->Name; ?>' <?php $this->Disabled; ?>></td>
			<td class=tdleft><?php  $this->Wordcount; ?></td>
			<td>
			<?php if ($this->is('Sticky',true)) { ?>
				&nbsp;
			<?php } else { ?>
				<?php $this->getH('PrintImageHREF',array('save', 'Save changes', TRUE)); ?>
			<?php } ?>
			</td>
		</tr>
	</form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>