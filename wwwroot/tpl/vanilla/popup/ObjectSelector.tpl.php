<?php if (defined("RS_TPL")) { ?>
	<link rel="stylesheet" type="text/css" href="?module=chrome&amp;uri=css/pi.css">
	<div style="background-color: #f0f0f0; border: 1px solid #3c78b5; padding: 10px; height: 100%; text-align: center; margin: 5px;">
	<h2>Choose a container:</h2>
	<form action="javascript:;">
		<?php $this->ObjectSelect; ?>
		<br>
		<input type=submit value='Proceed' onclick='if (getElementById("parents").value != "") { 
													opener.location="?module=redirect&page=object&tab=edit&op=linkEntities&object_id=<?php $this->ObjectID; ?>&child_entity_type=object&child_entity_id=<?php $this->ObjectID; ?>&parent_entity_type=object&parent_entity_id="+getElementById("parents").value;
													window.close();}'>
	</form>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>