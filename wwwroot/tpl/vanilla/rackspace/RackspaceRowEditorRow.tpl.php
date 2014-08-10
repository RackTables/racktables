<?php if (defined("RS_TPL")) {?>
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
			<?php $this->Options; ?>
		</select>
	</td><td>
		<input type=text name=name value='<?php $this->RowName; ?>' tabindex=100>
	</td><td>
		<input class="icon" type="image" border="0" title="Save changes" src="?module=chrome&uri=pix/tango-document-save-16x16.png" name="submit"></input>
	</form></td></tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>