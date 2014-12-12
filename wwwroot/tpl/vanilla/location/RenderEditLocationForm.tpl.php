<?php if (defined("RS_TPL")) {?>
	
	<div class=portlet><h2>Attributes</h2>
	<?php 
		$this->getH('PrintOpFormIntro', 'updateLocation');
	?>
		<table border=0 align=center>
			<tr><td>&nbsp;</td><th class=tdright>Parent location:</th><td class=tdleft>
				<?php
					$this->Getselect;
				 ?>
			</td></tr>
			<tr><td>&nbsp;</td>
				<th class=tdright>Name (required):</th>
					<td class=tdleft><input type=text name=name value='<?php $this->Locationname; ?>'>
			</td></tr>
			<tr><td>&nbsp;</td><th class=tdright>Tags:</th><td class=tdleft><?php $this->TagsPicker ?></td></tr> 	
			<input type=hidden name=num_attrs value=<?php $this->Num_attrs; ?>>
			<?php $this->OptionalAttributes; ?>
			<tr><td>&nbsp;</td><th class=tdright>Has problems:</th><td class=tdleft><input type=checkbox name=has_problems <?php if($this->is('Has_Problems', TRUE)){ ?>checked<?php } ?> ></td></tr>

			<?php 
				if($this->is('Empty_Locations', TRUE)){
					?> <tr><td>&nbsp;</td><th class=tdright>Actions:</th><td class=tdleft> <?php 
						$this->getH('GetOpLink', array(array('op'=>'deleteLocation'), '', 'destroy', 'Delete location', 'need-confirmation'));
					?>	&nbsp;</td></tr> <?php
				}
			?>

			<tr><td colspan=3><b>Comment:</b><br><textarea name=comment rows=10 cols=80><?php $this->Location_Comment; ?></textarea></td></tr>
			<tr><td class=submit colspan=3>

			<?php
				$this->getH('PrintImageHref', array('SAVE', 'Save changes', TRUE));
			 ?>

			</td></tr>
			</form></table><br></div>
			<div class=portlet><h2>History</h2>
			<?php 
				$this->Objecthistory;
			 ?>
			</div>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>