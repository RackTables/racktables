<?php if (defined("RS_TPL")) {?>
	<tr>
		<?php $this->getH("Form","updateUser"); ?>
		<input type=hidden name="user_id" value="<?php $this->UserId; ?>">
		<td>
			<input type=text name=username value='<?php $this->Name; ?>' size=16>
		</td>
		<td>
			<input type=text name=realname value='<?php $this->RealName; ?>' size=24>
		</td>
		<td><input type=password name=password size=40></td><td>
		<input class="icon" type="image" border="0" title="Save changes" src="?module=chrome&uri=pix/tango-document-save-16x16.png" name="submit"></input>
		</td>
		</form>
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>