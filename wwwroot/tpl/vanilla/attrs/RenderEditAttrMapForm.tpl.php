<?php if (defined("RS_TPL")) {?>
<div class=portlet><h2>Attribute map</h2>
	<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>
		<tr><th class=tdleft>Attribute name</th><th class=tdleft>Attribute type</th><th class=tdleft>Applies to</th></tr>
		<?php if ($this->is('NewTop')) : ?>
			<?php $this->getH('PrintOpFormIntro', 'add'); ?>
			<tr>
				<td colspan=2 class=tdleft>
					<select name=attr_id tabindex=100>
						<?php $this->startLoop('CreateNewAttrMaps'); ?>
						<option value=<?php $this->Id; ?> >[<?php $this->Shorttype; ?>] <?php $this->Name; ?></option>
						<?php $this->endLoop(); ?>
					</select>
				</td>
				<td class=tdleft>
					<?php $this->getH('PrintImageHref', array('add', '', TRUE)); echo ' '; 
		 			 	  $this->CreateNewSelect;	?>
   					<select name=chapter_no tabindex=102><option value=0>-- dictionary chapter for [D] attributes --</option>
					<?php $this->startLoop('CreateNewChapters'); ?>
						<option value='<?php $this->Id; ?>'><?php $this->Name; ?></option>
					<?php $this->endLoop(); ?>
					</select>
				</td>
			</tr>
			</form>
		<?php endif ?>
		<?php while ($this->loop('AttrMap')) : ?>
			<tr class=row_<?php $this->Order; ?>>
				<td class=tdleft><?php $this->Name; ?></td>
				<td class=tdleft><?php $this->AttrTypes; ?></td>
				<td colspan=2 class=tdleft>
					<?php while($this->loop('AttrMapChilds')) : ?>
						<?php if ($this->is('Sticky', 'yes')) { ?>
							<?php $this->getH("PrintImageHref", array('nodelete', 'system mapping')); ?>	
						<?php } elseif ($this->is('RefCnt', true) ){ ?>
							<?php $this->getH("PrintImageHref", array('nodelete', $this->_RefCnt . ' value(s) stored for objects')); ?> 
						<?php } else { ?>
							<?php $this->getH("GetOpLink", array(array('op'=>'del', 'attr_id'=>$this->_Id, 'objtype_id'=>$this->_ObjId), '', 'delete', 'Remove mapping')); ?>
						<?php } ?>
						<?php $this->DecObj ?>
						<?php if ($this->is("Type",'dict')) { ?>
							(values from '<?php $this->ChapterName ?>')
						<?php } ?>
						<br />	
					<?php endwhile ?>	 
				</td>
			</tr>
		<?php endwhile ?>
		<?php if (!$this->is('NewTop')) : ?>
			<?php $this->getH('PrintOpFormIntro', 'add'); ?>
			<tr>
				<td colspan=2 class=tdleft>
					<select name=attr_id tabindex=100>
						<?php $this->startLoop('CreateNewAttrMaps'); ?>
						<option value=<?php $this->Id; ?> >[<?php $this->Shorttype; ?>] <?php $this->Name; ?></option>
						<?php $this->endLoop(); ?>
					</select>
				</td>
				<td class=tdleft>
					<?php $this->getH('PrintImageHref', array('add', '', TRUE)); echo ' '; 
		 			 	  $this->CreateNewSelect;	?>
   					<select name=chapter_no tabindex=102><option value=0>-- dictionary chapter for [D] attributes --</option>
					<?php $this->startLoop('CreateNewChapters'); ?>
						<option value='<?php $this->Id; ?>'><?php $this->Name; ?></option>
					<?php $this->endLoop(); ?>
					</select>
				</td>
			</tr>
			</form>
		<?php endif ?>
	</table>
</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>