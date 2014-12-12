<?php if (defined("RS_TPL")) {?>
	<?php $this->getH('PrintOpFormIntro','add'); ?>
	<tr>
		<td>
			<?php $this->getH('PrintImageHREF',array('add','allocate',TRUE)) ; ?>
		</td>
		<td>
			<?php $this->Select; ?>
		</td>
		<td>
			<input type=text tabindex=101 name=bond_name size=10>
		</td>
		<td>
			<?php $this->TypeSelect; ?>
		</td>
		<td>
			<?php $this->getH('PrintImageHREF',array('add','allocate',TRUE,103)) ; ?>
		</td>
	</tr>
	</form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>