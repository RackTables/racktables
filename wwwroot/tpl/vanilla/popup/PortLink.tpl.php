<?php if (defined("RS_TPL")) {
	if ($this->is('Close'))
	{ 
		$this->addJS (<<<END
window.opener.location.reload(true);
window.close();
END
		, TRUE);		
	}
	else
	{
		$js_table = $this->_JSTable;
		$this->addJS(<<<END
POIFC = {};
$js_table
$(document).ready(function () {
	$('select.porttype').change(onPortTypeChange);
	onPortTypeChange();
});
function onPortTypeChange() {
	var key = $('*[name=port_type]')[0].value + '-' + $('*[name=remote_port_type]')[0].value;
	if (POIFC[key] == 1)
	{
		$('#hint-not-compat').hide();
		$('#hint-compat').show();
	}
	else
	{
		$('#hint-compat').hide();
		$('#hint-not-compat').show();
	}
}
END
		, TRUE);
	$this->addCSS (<<<END
.compat-hint {
	display: none;
	font-size: 125%;
}
.compat-hint#hint-compat {
	color: green;
}
.compat-hint#hint-not-compat {
	color: #804040;
}
END
		, TRUE);
?>
<link rel="stylesheet" type="text/css" href="?module=chrome&amp;uri=css/pi.css">
<div style="background-color: #f0f0f0; border: 1px solid #3c78b5; padding: 10px; height: 100%; text-align: center; margin: 5px;">
<form method=GET>
	<input type=hidden name="module" value="popup">
	<input type=hidden name="helper" value="portlist">
	<input type=hidden name="port" value="<?php $this->ID; ?>">
	<input type=hidden name="remote_port" value="<?php $this->RemoteID; ?>">
	<input type=hidden name="cable" value="<?php $this->Cable; ?>">
	<p>The ports you have selected are not compatible. Please select a compatible transceiver pair.
	<p>
	<?php $this->Port; ?>
	<?php if($this->is('IIFOIF')) { ?>
		<?php $this->IIFOIF; ?>
		<input type=hidden name="port_type" value="<?php $this->OIFID; ?>">
	<?php } else { ?>
		<label><?php $this->IIFName; ?>
		<?php $this->PortTypeOptions; ?>
		</label>
	<?php } ?>
	 &mdash; 
	<?php if($this->is('RemoteIIFOIF')) { ?>
		<?php $this->RemoteIIFOIF; ?>
		<input type=hidden name="remote_port_type" value="<?php $this->RemoteOIFID; ?>">
	<?php } else { ?>
		<label><?php $this->RemoteIIFName; ?>
		<?php $this->RemotePortTypeOptions; ?>
		</label>
	<?php } ?>	
	<?php $this->RemotePort; ?>
	</p>
	<p class="compat-hint" id="hint-not-compat">&#10005; Not compatible port types</p>
	<p class="compat-hint" id="hint-compat">&#10004; Compatible port types</p>
	<p><input type=submit name="do_link" value="Link">
	</form>
</div>
<?php } } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>