<?php if (defined("RS_TPL")) {?>
	<table class=slbcell>
		<tr>
			<td rowspan=3><?php $this->IP; ?><?php $this->ifname; ?></td>
			<td><a href='index.php?page=object&object_id=<?php $this->Id; ?>&hl_ip=<?php $this->IP; ?>'><strong><?php $this->Name; ?></strong></a></td>
		</tr>
		<tr>
			<td colspan=2>
				<?php $this->getH('PrintImageHref', array('router')); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php if($this->is('Tags')); { ?>
					<small><?php $this->Tags; ?></small>
				<?php } ?>
			</td>
		</tr>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>