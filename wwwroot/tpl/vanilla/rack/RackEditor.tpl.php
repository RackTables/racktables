<?php if (defined("RS_TPL")) {?>
	<?php $this->addJS('js/racktables.js'); ?>
	<div class=portlet>
		<h2>Attributes</h2>
		<?php $this->getH('PrintOpFormIntro','updateRack'); ?>
		<table border=0 align=center>
			<tr>
				<td>&nbsp;</td>
				<th class=tdright>Rack row:</th>
				<td class=tdleft><?php $this->RowSelect; ?></td>
			</tr>
			<tr><td>&nbsp;</td><th class=tdright>Name (required):</th><td class=tdleft><input type=text name=name value='<?php $this->Name; ?>'></td></tr>
			<tr><td>&nbsp;</td><th class=tdright>Height (required):</th><td class=tdleft><input type=text name=height value='<?php $this->Height; ?>'></td></tr>
			<tr><td>&nbsp;</td><th class=tdright>Asset tag:</th><td class=tdleft><input type=text name=asset_no value='<?php $this->AssetTag; ?>'></td></tr>
			<tr><td>&nbsp;</td><th class=tdright>Tags:</th><td class=tdleft>
				<?php $this->TagsPicker ?>
			</td></tr>
			<input type=hidden name=num_attrs value=<?php $this->NumAttrs; ?>>
			<?php while($this->loop('ExtraAttrs')) : ?>
				<input type=hidden name=<?php $this->I; ?>_attr_id value=<?php $this->Id; ?>>
				<tr>
				<td>
					<?php if($this->is('Deletable')) { ?>
						<?php $this->getH('GetOpLink',array(array('op'=>'clearSticker', 'attr_id'=>$this->_Id), '', 'clear', 'Clear value', 'need-confirmation')); ?>
					<?php } else { ?>
						&nbsp;
					<?php } ?>
				</td>
				<th class=sticker><?php $this->Name; ?></th>
				<td class=tdleft>
					<?php if($this->is('Type','dict')) { ?>
						<?php $this->DictSelect; ?>
					<?php } else { ?>
						<input type=text name=<?php $this->I?>_value value='<?php $this->Value; ?>'>
					<?php } ?>
				</td>
				</tr>
			<?php endwhile ?>
			<tr>
				<td>&nbsp;</td>
				<th class=tdright>Has problems:</th>
				<td class=tdleft><input type=checkbox name=has_problems <?php $this->HasProblems; ?>></td>
			</tr>
			<?php if($this->is('Deletable')) { ?>
				<tr>
					<td>&nbsp;</td>
					<th class=tdright>Actions:</th>
					<td class=tdleft>
						<?php $this->getH('GetOpLink',array(array ('op'=>'deleteRack'), '', 'destroy', 'Delete rack', 'need-confirmation'))?>&nbsp;
					</td>
				</tr>
			<?php } ?>
			<tr><td colspan=3><b>Comment:</b><br><textarea name=comment rows=10 cols=80><?php $this->Rack_Comment; ?></textarea></td></tr>
			<tr>
				<td class=submit colspan=3>
					<?php $this->getH('PrintImageHREF',array('SAVE','Save changes',TRUE)) ; ?>
				</td>
			</tr>
		</table>
	</div>
	<div class=portlet>
		<h2>History</h2>
		<?php $this->History; ?>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>