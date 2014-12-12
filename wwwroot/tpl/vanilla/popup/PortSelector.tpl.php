<?php if (defined("RS_TPL")) { ?>
<link rel="stylesheet" type="text/css" href="?module=chrome&amp;uri=css/pi.css">
<div style="background-color: #f0f0f0; border: 1px solid #3c78b5; padding: 10px; height: 100%; text-align: center; margin: 5px;">
Link <?php $this->Port; ?> to...
<form method=GET>
	<div class=portlet><h2>Port list filter</h2>
		<input type=hidden name="module" value="popup">
		<input type=hidden name="helper" value="portlist">
		<input type=hidden name="port" value="<?php $this->PortID; ?>">
		<table align="center" valign="bottom">
			<tr>
				<td class="tdleft"><label>Object name:<br><input type=text size=8 name="filter-obj" value="<?php $this->ObjectName; ?>"></label></td>
				<td class="tdleft"><label>Asset tag:<br><input type=text size=8 name="filter-asset_no" value="<?php $this->AssetTag; ?>"></label></td>
				<td class="tdleft"><label>Port name:<br><input type=text size=6 name="filter-port" value="<?php $this->PortName; ?>"></label></td>
				<td class="tdleft" valign="bottom"><label><input type=checkbox name="in_rack" <?php $this->RackChecked; ?>>Nearest racks</label></td>
				<td valign="bottom"><input type=submit value="show ports"></td>
			</tr>
		</table>
	</div>
	<div class=portlet><h2>Compatible spare ports</h2>
		<?php if($this->is('Select')) { ?>
			<?php $this->Select; ?>
			<p>Cable ID: <input type=text id=cable name=cable>
			<?php if ($this->is('PatchSelect')) { ?>
				<p>Patch cable: <?php $this->PatchSelect ?>
			<?php } ?> 
			<p><input type='submit' value='Link' name='do_link'>
		<?php } else { ?>
			(nothing found)
		<?php } ?>
	</div>
</form>
</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>