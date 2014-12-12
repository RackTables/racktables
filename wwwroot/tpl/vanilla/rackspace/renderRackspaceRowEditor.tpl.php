<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Edit rows</h2>
		<table border=0 cellspacing=0 cellpadding=5 align=center class=widetable>
			<tr><th>&nbsp;</th><th>Location</th><th>Name</th><th>&nbsp;</th></tr>
			<?php if ($this->is('NewTop')) : ?>
				<?php $this->getH("Form","addRow"); ?>
				<tr><td>
					<input class="icon" type="image" border="0" title="Add new row" src="?module=chrome&uri=pix/tango-document-new.png" name="submit"></input>
				</td><td>
					<select name=location_id tabindex=100>
						<?php $this->LocationNewOptions; ?>
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
			<?php endif ?>
			<?php while ($this->loop('RowList')) : ?>
				<tr><td>
				<?php if($this->is("HasChildren")) { ?>
					<img width="16" height="16" border="0" title="<?php $this->RackCount; ?> rack(s) here" src="?module=chrome&uri=pix/tango-user-trash-16x16-gray.png"></img>
				<?php } else { ?>
					<a title="Delete row" href="?module=redirect&op=deleteRow&row_id=<?php $this->RowId; ?>&page=rackspace&tab=editrows">
    					<img width="16" height="16" border="0" title="Delete row" src="?module=chrome&uri=pix/tango-user-trash-16x16.png"></img>
					</a>
				<?php } ?>
				<form method=post id="updateRow" name="updateRow" action='?module=redirect&page=rackspace&tab=editrows&op=updateRow'>
					<input type=hidden name="row_id" value="<?php $this->RowId; ?>">
				</td><td>
					<select name=location_id tabindex=100>
						<?php $this->LocationEditOptions; ?>
					</select>
				</td><td>
					<input type=text name=name value='<?php $this->RowName; ?>' tabindex=100>
				</td><td>
					<input class="icon" type="image" border="0" title="Save changes" src="?module=chrome&uri=pix/tango-document-save-16x16.png" name="submit"></input>
				</form></td></tr>
			<?php endwhile ?>
			<?php if (!$this->is('NewTop')) : ?>
				<?php $this->getH("Form","addRow"); ?>
				<tr><td>
					<input class="icon" type="image" border="0" title="Add new row" src="?module=chrome&uri=pix/tango-document-new.png" name="submit"></input>
				</td><td>
					<select name=location_id tabindex=100>
						<?php $this->LocationNewOptions; ?>
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
			<?php endif ?>
		</table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>