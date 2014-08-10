<?php if (defined("RS_TPL")) {?>
	<tr class='<?php $this->class ?>'>
	<td><?php $this->opLink ?></td>
	<td><?php $this->proto ?>/<?php $this->osif ?><?php $this->portpair_local_mod ?>
	<?php if ($this->is("local_addr_name")) { ?>
		(<?php $this->local_addr_name ?>)
	<?php } ?>
	<td><?php $this->portpair_remote_mod ?></td> 
	<td class='description'>
	<?php $this->mkAList ?>
	<?php if ($this->is("remote_addr_name")) { ?>
		(<?php $this->remote_addr_name ?>)
	<?php } ?> 
	<?php $this->opFormIntro ?>
	</td><td class='description'>
	<input type='text' name='description' value='<?php $this->description ?>'></td><td>
	<?php $this->saveImg ?>
	</td></form></tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>