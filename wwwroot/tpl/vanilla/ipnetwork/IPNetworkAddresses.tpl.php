<?php if (defined("RS_TPL")) {
	if ($this->is('AutoScroll')) {
		$id = $this->_AutoScroll ;
		$this->addJS (<<<END
$(document).ready(function() {
	var anchor = document.getElementsByName('ip-$id')[0];
	if (anchor)
		anchor.scrollIntoView(false);
});
END
		, TRUE);
	}
	?>

	<?php if ($this->is('HasPagination')) { ?>
		<h3><?php $this->StartIP; ?> ~ <?php $this->EndIP; ?></h3>
	<?php } ?>
	
	<?php $this->Pager; ?>
	
	<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center' width='100%'>
		<tr><th>Address</th><th>Name</th><th>Comment</th><th>Allocation</th></tr>
		<?php $this->IPList; ?>
	</table>
	
	<?php if ($this->is('Pager')) { ?>
	<p><?php $this->Pager; ?></p>
	<?php } ?>
	
	<?php if ($this->is('UserHasEditPerm')) { 
		$this->addJS('js/inplace-edit.js');	
	} ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>