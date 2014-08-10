<?php if (defined("RS_TPL")) {?>
	<tr>
	<?php if ($this->is("isNoObject",true)) { ?>
		<td><?php $this->objMkA ?></td>
	<?php } ?> 
	<?php if ($this->is("isNoVLANDomain",true)) { ?>
		<td><?php $this->vlanDMkA ?></td>
	<?php } ?> 
	<?php if ($this->is("isNoVST",true)) { ?>
		<td><?php $this->vstMkA ?></td>
	<?php } ?> 
	<td><?php $this->cutblock ?></td></tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>