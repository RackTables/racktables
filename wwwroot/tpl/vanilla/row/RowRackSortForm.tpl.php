<?php if (defined("RS_TPL")) {
//Needed JS
$js = <<<JSTXT
	$(document).ready(
		function () {
			$("#sortRacks").sortable({
				update : function () {
					serial = $('#sortRacks').sortable('serialize');
					$.ajax({
						url: 'index.php?module=ajax&ac=upd-rack-sort-order',
						type: 'post',
						data: serial,
					});
				}
			});
		}
	);
JSTXT;
	$this->addJS('js/jquery-1.4.4.min.js');
	$this->addJS('js/jquery-ui-1.8.21.min.js');
	$this->addJS($js,true);
?>

<div class=portlet>
	<h2>Racks</h2>
	<table border=0 cellspacing=0 cellpadding=5 align=center class=widetable>
		<tr><th>Drag to change order</th></tr>
		<tr>
			<td>
				<ul class='uflist' id='sortRacks'>
					<?php $this->startLoop('racklist'); ?>
						<li id=racks_<?php $this->RackId; ?>><?php $this->RackName; ?></li>
					<?php $this->endLoop(); ?>
				</ul>
			</td>
		</tr>
	</table>
</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>