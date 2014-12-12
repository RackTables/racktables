<?php if (defined("RS_TPL")) {?>
<tr class=row_<?php $this->order; ?>><td>
<?php $this->getH('PrintImageHref','%%ImageType'); ?>
</td><td class=tdright><?php $this->key; ?></td><td>

<?php if(!$this->is('refcnt', 0)) { ?> 
	<?php if($this->is('cfe')) { ?>
		<a href="<?php $this->href; ?>"><?php $this->refcnt; ?></a>
	<?php } ?>
	<?php $this->refcnt ?>
<?php } ?>

</td><td><?php $this->value; ?></td></tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>