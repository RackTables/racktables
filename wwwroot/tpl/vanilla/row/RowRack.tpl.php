<?php if (defined("RS_TPL")) {?>
	<?php if ($this->is('EndOfLine')) { ?></tr><?php } ?>
	<?php if ($this->is('NewLine')) { ?><tr><?php } ?>
	<td align=center valign=bottom class=row_<?php $this->Class; ?>>
		<a href='<?php $this->Link ?>'>
			<img border=0 width=<?php $this->ImgWidth; ?> height=<?php $this->ImgHeight; ?> title='<?php $this->RackHeight; ?> units' src='?module=image&img=midirack&rack_id=<?php $this->Id; ?>&scale=<?php $this->RowScale; ?>'>
			<br>
		<?php $this->Name; ?></a></td>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>