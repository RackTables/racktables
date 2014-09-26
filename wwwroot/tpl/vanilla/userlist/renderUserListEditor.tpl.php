<?php if (defined("RS_TPL")) {?>
	<?php if($this->is('NewTop')) : ?>
		<div class=portlet><h2>Add new</h2>
		<?php $this->getH("Form","createUser"); ?>
		<table cellspacing=0 cellpadding=5 align=center>
		<tr><th>&nbsp;</th><th>&nbsp;</th><th>Tags</th></tr>
		<tr><th class=tdright>Username</th><td class=tdleft><input type=text size=64 name=username tabindex=100></td>
		<td rowspan=4>
		<?php $this->TagsPicker; ?> 
		</td></tr>
		<tr><th class=tdright>Real name</th><td class=tdleft><input type=text size=64 name=realname tabindex=101></td></tr>
		<tr><th class=tdright>Password</th><td class=tdleft><input type=password size=64 name=password tabindex=102></td></tr>
		<tr><td colspan=2>
		<input class="icon" type="image" border="0" title="Add new account" tabindex="103" src="?module=chrome&uri=pix/tango-document-new-big.png" name="submit"></input>
		</td></tr>
		</table></form>
		</div>
	<?php endif?>
		<div class=portlet><h2>Manage existing (<?php $this->Count; ?>)</h2>
		<table cellspacing=0 cellpadding=5 align=center class=widetable>
			<tr><th>Username</th><th>Real name</th><th>Password</th><th>&nbsp;</th></tr>
			<?php while($this->loop('Users')) : ?>
				<tr>
					<?php $this->getH("Form","updateUser"); ?>
					<input type=hidden name="user_id" value="<?php $this->UserId; ?>">
					<td>
						<input type=text name=username value='<?php $this->Name; ?>' size=16>
					</td>
					<td>
						<input type=text name=realname value='<?php $this->RealName; ?>' size=24>
					</td>
					<td>
						<input type=password name=password size=40>
					</td>
					<td>
					<input class="icon" type="image" border="0" title="Save changes" src="?module=chrome&uri=pix/tango-document-save-16x16.png" name="submit"></input>
					</td>
					</form>
				</tr>
			<?php endwhile ?>
		</table>
		<br>
		</div>
	<?php if(!$this->is('NewTop')) : ?>
		<div class=portlet><h2>Add new</h2>
		<?php $this->getH("Form","createUser"); ?>
		<table cellspacing=0 cellpadding=5 align=center>
		<tr><th>&nbsp;</th><th>&nbsp;</th><th>Tags</th></tr>
		<tr><th class=tdright>Username</th><td class=tdleft><input type=text size=64 name=username tabindex=100></td>
		<td rowspan=4>
		<?php $this->TagsPicker; ?> 
		</td></tr>
		<tr><th class=tdright>Real name</th><td class=tdleft><input type=text size=64 name=realname tabindex=101></td></tr>
		<tr><th class=tdright>Password</th><td class=tdleft><input type=password size=64 name=password tabindex=102></td></tr>
		<tr><td colspan=2>
		<input class="icon" type="image" border="0" title="Add new account" tabindex="103" src="?module=chrome&uri=pix/tango-document-new-big.png" name="submit"></input>
		</td></tr>
		</table></form>
		</div>
	<?php endif?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>