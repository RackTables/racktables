<?php if (defined("RS_TPL")) {?>
	<script type="text/javascript">
	function locationeditor_showselectbox(e) {
		$(this).load('index.php', {module: 'ajax', ac: 'get-location-select', locationid: this.id});
		$(this).unbind('mousedown', locationeditor_showselectbox);
	}
	$(document).ready(function () {
		$('select.locationlist-popup').bind('mousedown', locationeditor_showselectbox);
	});
	</script>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>