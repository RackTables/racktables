<?php if (defined("RS_TPL")) {?>
<?php if($this->is('recordCount', 0)) { ?>	
	<center><h2>(no records)</h2></center>
<?php } else { ?>
	<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>
	<tr><th colspan=4><?php $this->recordCount ?> record(s)</th></tr>
	<tr><th>Origin</th><th>Key</th><th>Refcnt</th><th>Word</th></tr>
	<?php while($this->loop('ChapterList')) { ?>
		<tr class=row_<?php $this->order; ?>>
			<td>
				<?php $this->getH('PrintImageHref','%%ImageType'); ?>
			</td>
			<td class=tdright><?php $this->key; ?></td>
			<td>
				<?php if(!$this->is('refcnt', 0)) { ?> 
					<?php if($this->is('cfe')) { ?>
						<a href="<?php $this->href; ?>"><?php $this->refcnt; ?></a>
					<?php } ?>
					<?php $this->refcnt ?>
				<?php } ?>
			</td>
			<td><?php $this->value; ?></td>
		</tr>
	<?php } ?>
	</table><br>
<?php } ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>