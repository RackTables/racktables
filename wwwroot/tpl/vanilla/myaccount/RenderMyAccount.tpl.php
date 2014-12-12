<?php if (defined("RS_TPL")) {?>
	

	<div class=portlet><h2>Current user info</h2>
	<div style="text-align: left; display: inline-block;">
	<table>
	<tr><th>Login:</th><td><?php $this->UserName; ?><td></tr>
	<tr><th>Name:</th><td><?php $this->DisplayName; ?></td></tr>
	<tr><th>Explicit tags:</th><td><?php $this->getH("SerializeTags", array( $this->_Serialize1)); ?></td></tr>
	<tr><th>Implicit tags:</th><td><?php $this->getH("SerializeTags", array( $this->_Serialize2)); ?><td></tr>
	<tr><th>Automatic tags:</th><td><?php $this->getH("SerializeTags", array( $this->_Serialize3)); ?><td></tr>
	</table></div>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>