<?php if (defined("RS_TPL")) {?>
	<br><table border=0 cellspacing=0 cellpadding=5 align=center>
	<tr>
	<?php if ($this->is("isNoObject",true)) { ?>
		<th>switch</th>
	<?php } ?> 
	<?php if ($this->is("isNoVLANDomain",true)) { ?>
		<th>domain</th>
	<?php } ?>
	<?php if ($this->is("isNoVST",true)) { ?>
	 	<th>template</th>
	<?php } ?>
	<th>&nbsp;</th></tr>  
	<?php $this->AddNewTop ?>
	<?php while ($this->loop('AllMinusLines')) : ?>
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
	<?php endwhile ?>
	<?php $this->AddNewBottom ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>