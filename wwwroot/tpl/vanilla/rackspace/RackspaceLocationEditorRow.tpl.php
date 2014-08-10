<?php if (defined("RS_TPL")) {?>
	<tr>
		<td align=left style='padding-left: <?php $this->Level; ?>px'>
		<?php if($this->is("HasSublocations")) { ?>
			<img src="?module=chrome&uri=pix/node-expanded-static.png" width="16" height="16">
		<?php } ?>
		<?php if($this->is("IsDeletable")) { ?>
			<a title="Delete location" href="?module=redirect&op=deleteLocation&location_id=<?php $this->LocationId; ?>&page=rackspace&tab=editlocations">
    			<img width="16" height="16" border="0" title="Delete location" src="?module=chrome&uri=pix/tango-user-trash-16x16.png">	    			
			</a>
		<?php } else { ?>
			<img width="16" height="16" border="0" src="?module=chrome&uri=pix/tango-user-trash-16x16-gray.png"></img>
		<?php } ?>
		</td>
		<td class=tdleft>
	<form method=post id="updateLocation" name="updateLocation" action='?module=redirect&page=rackspace&tab=editlocations&op=updateLocation'>
			<input type=hidden name="location_id" value="<?php $this->LocationId; ?>">
			<select name="parent_id" id="locationid_<?php $this->LocationId; ?>" class='locationlist-popup'>
				<?php $this->startLoop('Parentselect'); ?>
					<option value='<?php $this->ParenListId; ?>' <?php $this->ParentListSelected; ?>><?php $this->ParentListContent; ?></option>
				<?php $this->endLoop(); ?>
			</select>
		</td>
		<td class=tdleft>
		<input type=text size=48 name=name value='<?php $this->LocationName; ?>'>
		</td>
		<td>
			<input class="icon" type="image" border="0" title="Save changes" src="?module=chrome&uri=pix/tango-document-save-16x16.png" name="submit"></input>
		</td>
	</form>
	</tr>	
	<?php $this->LocationList; ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>


