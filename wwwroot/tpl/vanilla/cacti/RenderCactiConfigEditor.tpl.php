<?php if (defined("RS_TPL")) {?>
	<table cellspacing=0 cellpadding=5 align=center class=widetable>
		<tr><th>&nbsp;</th><th>base URL</th><th>username</th><th>password</th><th>graph(s)</th><th>&nbsp;</th></tr>
		<?php if($this->is('AddTop', true)) { ?>
			<?php $this->getH("Form","add"); ?>
			<tr>
				<td><input class="icon" type="image" border="0" title="add a new server" tabindex="112" src="?module=chrome&uri=pix/tango-document-new.png" name="submit"></input></td>
				<td><input type=text size=48 name=base_url tabindex=101></td>
				<td><input type=text size=24 name=username tabindex=102></td>
				<td><input type=password size=24 name=password tabindex=103></td>
				<td>&nbsp;</td>
				<td><input class="icon" type="image" border="0" title="add a new server" tabindex="112" src="?module=chrome&uri=pix/tango-document-new.png" name="submit"></input></td>
			</tr>
			</form>
		<?php } ?>
		<?php while($this->loop('CactiServers')) { ?>
			<?php $this->getH("Form","upd"); ?>
			<input type="hidden" name="id" value="<?php $this->Id; ?>">
				<tr>
					<td>
						<?php if($this->is("NumGraphs", true)) { ?>
							<img width="16" border="0" height="16" title="cannot delete, graphs exist" src="?module=chrome&uri=pix/tango-user-trash-16x16-gray.png"></img>
						<?php } else { ?>
							<a title="delete this server" href="?module=redirect&op=del&id=<?php $this->Id ?>&page=cacti&tab=servers">
    							<img width="16" border="0" height="16" title="delete this server" src="?module=chrome&uri=pix/tango-user-trash-16x16.png"></img>
							</a>
						<?php }?>
					</td>
					<td><input type=text size=48 name=base_url value="<?php $this->BaseUrl; ?>"></td>
					<td><input type=text size=24 name=username value="<?php $this->Username; ?>"></td>
					<td><input type=password size=24 name=password value="<?php $this->Password; ?>"></td>
					<td class=tdright><?php $this->NumGraphs; ?></td>
					<td><input class="icon" type="image" border="0" title="update this server" src="?module=chrome&uri=pix/tango-document-save-16x16.png" name="submit"></input></td>
					<td></td>
				</tr>
			</form>
		<?php } ?>
		<?php if($this->is('AddTop', false)) { ?>
			<?php $this->getH("Form","add"); ?>
			<tr>
				<td><input class="icon" type="image" border="0" title="add a new server" tabindex="112" src="?module=chrome&uri=pix/tango-document-new.png" name="submit"></input></td>
				<td><input type=text size=48 name=base_url tabindex=101></td>
				<td><input type=text size=24 name=username tabindex=102></td>
				<td><input type=password size=24 name=password tabindex=103></td>
				<td>&nbsp;</td>
				<td><input class="icon" type="image" border="0" title="add a new server" tabindex="112" src="?module=chrome&uri=pix/tango-document-new.png" name="submit"></input></td>
			</tr>
			</form>
		<?php } ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>