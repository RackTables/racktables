<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>SNMPv1</h2>
		<?php $this->getH("PrintOpFormIntro", array('querySNMPData', array ('ver' => 1))); ?>
		<table cellspacing=0 cellpadding=5 align=center class=widetable>
		<tr><th class=tdright><label for=communityv1>Community: </label></th>
		<td class=tdleft><input type=text name=community id=communityv1 value='<?php $this->snmpcomm ?>'></td></tr>
		<tr><td colspan=2><input type=submit value="Try now"></td></tr>
		</table></form>
	</div>
	<div class=portlet>
		<h2>SNMPv2</h2>
		<?php $this->getH("PrintOpFormIntro", array('querySNMPData', array ('ver' => 2))); ?>
		<table cellspacing=0 cellpadding=5 align=center class=widetable>
		<tr><th class=tdright><label for=communityv2>Community: </label></th>
		<td class=tdleft><input type=text name=community id=communityv2 value='<?php $this->snmpcomm ?>'></td></tr>
		<tr><td colspan=2><input type=submit value="Try now"></td></tr>
		</table></form>
	</div>
	<div class=portlet>
		<h2>SNMPv3</h2>
		<?php $this->getH("PrintOpFormIntro", array('querySNMPData', array ('ver' => 3))); ?>
		<table cellspacing=0 cellpadding=5 align=center class=widetable>
			<tr>
				<th class=tdright><label for=sec_name>Security User:</label></th>
				<td class=tdleft><input type=text id=sec_name name=sec_name value='<?php $this->snmpcomm ?>'></td>
			</tr>
			<tr>
				<th class=tdright><label for="sec_level">Security Level:</label></th>
				<td class=tdleft><select id="sec_level" name="sec_level">
					<option value="noAuthNoPriv" selected="selected">noAuth and no Priv</option>
					<option value="authNoPriv" >auth without Priv</option>
					<option value="authPriv" >auth with Priv</option>
				</select></td>
			</tr>
			<tr>
				<th class=tdright><label for="auth_protocol_1">Auth Type:</label></th>
				<td class=tdleft>
				<input id=auth_protocol_1 name=auth_protocol type=radio value=md5 />
				<label for=auth_protocol_1>MD5</label>
				<input id=auth_protocol_2 name=auth_protocol type=radio value=sha />
				<label for=auth_protocol_2>SHA</label>
				</td>
			</tr>
			<tr>
				<th class=tdright><label for=auth_passphrase>Auth Key:</label></th>
				<td class=tdleft><input type=text id=auth_passphrase name=auth_passphrase></td>
			</tr>
			<tr>
				<th class=tdright><label for=priv_protocol_1>Priv Type:</label></th>
				<td class=tdleft>
				<input id=priv_protocol_1 name=priv_protocol type=radio value=DES />
				<label for=priv_protocol_1>DES</label>
				<input id=priv_protocol_2 name=priv_protocol type=radio value=AES />
				<label for=priv_protocol_2>AES</label>
				</td>
			</tr>
			<tr>
				<th class=tdright><label for=priv_passphrase>Priv Key</label></th>
				<td class=tdleft><input type=text id=priv_passphrase name=priv_passphrase></td>
			</tr>
			<tr><td colspan=2><input type=submit value="Try now"></td></tr>
		</table>
		</form>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>