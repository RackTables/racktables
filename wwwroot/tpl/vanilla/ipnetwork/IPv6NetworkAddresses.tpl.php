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
	if ($this->is('UserHasEditPerm')) { 
		$this->addJS('js/inplace-edit.js');	
	}
	?>
	
	<?php if ($this->is('HasPagination')) { ?>
		<center>
			<h3><?php $this->NumPages; ?> pages:</h3>
			<?php $this->startLoop('Pages'); ?>
				<?php $this->B; ?><a href='<?php $this->Link(); ?>'><?php $this->I; ?></a><?php $this->BEnd; ?>
			<?php $this->endLoop(); ?>
		</center>
	<?php } ?>
	
	<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center' width='100%'>
		<tr><th>Address</th><th>Name</th><th>Comment</th><th>Allocation</th></tr>
		<?php $this->IPList; ?>
		<?php if($this->is('BottomPager')) { ?>
			<tr><td colspan=3>
			<?php if ($this->is('BottomPagerrPrevLink')) { ?>
				<a href='<?php $this->BottomPagePrevLink; ?>'><< prev</a> 
			<?php } ?>
			<?php if ($this->is('BottomPagerNextLink')) { ?>
				<a href='<?php $this->BottomPagerNextLink; ?>'>next >></a>
			<?php } ?>
			</td></tr>
		<?php } ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>