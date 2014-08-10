<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Found <?php $this->MatchCount; ?> matching VS</h2>
		<?php $this->getH('PrintOpFormIntro','convert'); ?>
		<?php if($this->is('UsedTags')) { ?>
			<p>Assign these tags to VS group:</p>
			<?php while($this->refLoop('UsedTags')) { ?>
				<p><label><input type=checkbox checked name="taglist[]" value="<?php $this->ID; ?>"> <?php $this->Tags; ?></label>
			<?php } ?>
		<?php } ?>
		<p>Import settings of these VS:</p>
		<table align=center>
		<tr>
		<?php while($this->refLoop('PortKeys')) { ?>
			<th><?php $this->Key; ?></th>
		<?php } ?>
		</tr>
		<tr>
			<?php $this->Grouped; ?>
		</tr>
		</form>
		<?php $this->getH('PrintImageHREF',array('next', "Import settings of the selected services", TRUE)); ?>
		</table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>