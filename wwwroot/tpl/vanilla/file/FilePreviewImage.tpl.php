<?php if (defined("RS_TPL")) {?>
	<?php if ($this->is('Resampled',true)) { ?>
		<a href='?module=download&file_id=<?php $this->Id; ?>&asattach=no'>
	<?php } ?>
		<img width=<?php $this->Width; ?> height=<?php $this->Height; ?> src='?module=image&img=preview&file_id=<?php $this->Id; ?>'>
	<?php if ($this->is('Resampled',true)) { ?>
		</a><br>(click to zoom)
	<?php } ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>