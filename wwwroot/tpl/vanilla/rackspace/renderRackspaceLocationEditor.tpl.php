<?php
	
	if (defined("RS_TPL")) {
	$js = <<<JS
	<script type="text/javascript">
	function locationeditor_showselectbox(e) {
		$(this).load('index.php', {module: 'ajax', ac: 'get-location-select', locationid: this.id});
		$(this).unbind('mousedown', locationeditor_showselectbox);
	}
	$(document).ready(function () {
		$('select.locationlist-popup').bind('mousedown', locationeditor_showselectbox);
	});
	</script>
JS;
	$this->addJS($js,true);?>
	<div class=portlet><h2>Locations</h2>
		<table border=0 cellspacing=0 cellpadding=5 align=center class=widetable>
			<tr><th>&nbsp;</th><th>Parent</th><th>Name</th><th>&nbsp;</th></tr>
			<?php if($this->is('renderNewTop')) : ?>
				<form method=post id=addLocation name=addLocation action='?module=redirect&page=rackspace&tab=editlocations&op=addLocation'>
				<tr>
					<td>
						<input class="icon" type="image" border="0" title="Add new location" src="?module=chrome&uri=pix/tango-document-new.png" name="submit"></input>
					</td>
					<td>
						<select name=parent_id tabindex=100>
							<?php $this->RenderNewFormOptions; ?>
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
			<?php endif ?>
			<?php while($this->loop('LocationList')) : ?>
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
						<?php while ($this->loop('Parentselect')) : ?>
							<option value='<?php $this->ParenListId; ?>' <?php $this->ParentListSelected; ?>><?php $this->ParentListContent; ?></option>
						<?php endwhile ?>
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
			<?php endwhile ?>
			<?php if(!$this->is('renderNewTop')) : ?>
				<form method=post id=addLocation name=addLocation action='?module=redirect&page=rackspace&tab=editlocations&op=addLocation'>
				<tr>
					<td>
						<input class="icon" type="image" border="0" title="Add new location" src="?module=chrome&uri=pix/tango-document-new.png" name="submit"></input>
					</td>
					<td>
						<select name=parent_id tabindex=100>
							<?php $this->RenderNewFormOptions; ?>
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
			<?php endif ?>
		</table><br />
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>