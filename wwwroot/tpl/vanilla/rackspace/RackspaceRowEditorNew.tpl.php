<?php if (defined("RS_TPL")) {?>
	<?php $this->getH("Form","addRow"); ?>
	<tr><td>
		<input class="icon" type="image" border="0" title="Add new row" src="?module=chrome&uri=pix/tango-document-new.png" name="submit"></input>
	</td><td><select name=location_id tabindex=100>
		<?php $this->Options; ?>
		</select>
	</td>
	<td>
		<input type=text name=name tabindex=100>
	</td>
	<td>
		<input class="icon" type="image" border="0" title="Add new row" tabindex="102" src="?module=chrome&uri=pix/tango-document-new.png" name="submit"></input>
	</td>
	</tr>
	</form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>