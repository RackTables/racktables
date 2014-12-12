<?php if (defined("RS_TPL")) {?>
	<table class=widetable border=0 cellpadding=5 cellspacing=0 align=center>
	<tr><th>Origin</th><th>Key</th><th><?php $this->ColHeader ?></th></tr>
	<?php while($this->refLoop('AllRows')) { ?>
		<tr>
		<td>
		<?php if ($this->is('OriginIsDefault')) { ?>
			<?php $this->getH('PrintImageHref', array('computer', 'default')); ?>
		<?php } else { ?>
			<?php $this->getH("PrintImageHref", array('favorite', 'custom')); ?>
		<?php } ?> 
		</td>
		<td class=tdright><?php $this->RowColumnKey ?></td>
		<td class=tdleft><?php $this->RowString ?></td>
		</tr>
	<?php } ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>