<?php if (defined("RS_TPL")) {?>
		<br>
		<center>
			<a target='_blank' href='?module=download&file_id=<?php $this->Id; ?>&asattach=1'>
				<?php $this->getH('PrintImageHREF','DOWNLOAD'); ?>
			</a>
		</center>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>