<?php if (defined("RS_TPL")) {?>
	<td class='atom state_<?php $this->state ?><?php $this->rackHL ?>'
	<?php if ($this->is("colspan")) { ?>
		 colspan=<?php $this->colspan ?>
	<?php } ?> 
	<?php if ($this->is("rowspan")) { ?>
		 rowspan=<?php $this->rowspan ?>
	<?php } ?>>
	<?php if ($this->is("state","T")) { ?>
		<?php $this->objectDetail ?>
	<?php } ?>
	<?php if ($this->is("state","A")) { ?>
		<div title="This rackspace does not exist">&nbsp;</div>
	<?php } ?> 
	<?php if ($this->is("state","F")) { ?>
		<div title="Free rackspace">&nbsp;</div>
	<?php } ?> 
	<?php if ($this->is("state","U")) { ?>
		<div title="Problematic rackspace, you CAN\'T mount here">&nbsp;</div>
	<?php } ?> 
	<?php if ($this->is("defaultState",true)) { ?>
		<div title="No data">&nbsp;</div>
	<?php } ?> 
	</td>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>