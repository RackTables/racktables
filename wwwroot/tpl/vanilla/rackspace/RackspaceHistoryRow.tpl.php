<?php if (defined("RS_TPL")) {?>
	<tr class='<?php $this->Class; ?>'>
		<td>
			<a href='<?php $this->Link; ?>'><?php $this->Time; ?></a>
		</td>
		<td>
			<?php $this->UserName; ?>
		</td>
		<td>
			<?php $this->RenderedCell; ?>
		</td>
		<td>
			<?php $this->getH("NiftyString","%%Comment"); ?>
		</td>
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>