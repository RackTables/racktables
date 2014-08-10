<?php if (defined("RS_TPL")) {?>
	<form method=post id=addLocation name=addLocation action='?module=redirect&page=rackspace&tab=editlocations&op=addLocation'>
		<tr>
			<td>
				<input class="icon" type="image" border="0" title="Add new location" src="?module=chrome&uri=pix/tango-document-new.png" name="submit"></input>
			</td>
			<td>
				<select name=parent_id tabindex=100>
				<?php $this->Options; ?>
				</select>
			</td>
			<td>
				<input type=text size=48 name=name tabindex=101>
			</td>
			<td>
				<input class="icon" type="image" border="0" title="Add new location" src="?module=chrome&uri=pix/tango-document-new.png" name="submit"></input>
			</td>
		</tr>
	</form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>