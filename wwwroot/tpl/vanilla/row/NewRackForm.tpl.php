<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Add one</h2>
		<?php $this->getH('PrintOpFormIntro',array('addRack', array ('got_data' => 'TRUE'))); ?>
		<table border=0 align=center>
			<tr>
				<th class=tdright>Name (required):</th>
				<td class=tdleft><input type=text name=name tabindex=1></td>
				<td rowspan=4>Tags:<br>
					<?php $this->Tags; ?>
				</td>
			</tr>
			<tr>
				<th class=tdright>Height in units (required):</th>
				<td class=tdleft>
					<input type=text name=height1 tabindex=2 value='<?php $this->DefaultHeight; ?>'>
				</td>
			</tr>
			<tr>
				<th class=tdright>Asset Tag:</th>
				<td class=tdleft><input type=text name=asset_no tabindex=4></td>
			</tr>
			<tr>
				<td class=submit colspan=2>
					<?php $this->getH('PrintImageHref',array('CREATE', 'Add', TRUE)); ?>
				</td>
			</tr>
		</table>
		</form>
	</div>
	<div class=portlet>
		<h2>Add many</h2>
		<?php $this->getH('PrintOpFormIntro',array('addRack', array ('got_mdata' => 'TRUE'))); ?>
		<table border=0 align=center>
			<tr>
				<th class=tdright>Height in units (*):</th>
				<td class=tdleft><input type=text name=height2 value='<?php $this->DefaultHeight; ?>'></td>
				<td rowspan=3 valign=top>Assign tags:<br>
					<?php $this->Tags; ?>
				</td>
			</tr>
			<tr>
				<th class=tdright>Rack names (required):</th>
				<td class=tdleft><textarea name=names cols=40 rows=25></textarea></td>
			</tr>
			<tr>
				<td class=submit colspan=2>
					<?php $this->getH('PrintImageHref',array('CREATE', 'Add', TRUE)); ?>
				</td>
			</tr>
		</form>
		</table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>