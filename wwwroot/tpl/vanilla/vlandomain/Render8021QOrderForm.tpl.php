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
	<?php if ($this->is('AddNewTop',true)) { ?>
		<tr>
	 		<td><?php $this->NewSelect ?></td>
			<td><?php $this->getH("PrintImageHref", array('Attach', 'set', TRUE, 104)); ?></td>
		</tr></form>
	<?php } ?>
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
	<?php if ($this->is('AddNewTop',false)) { ?>
		<tr>
	 		<td><?php $this->NewSelect ?></td>
			<td><?php $this->getH("PrintImageHref", array('Attach', 'set', TRUE, 104)); ?></td>
		</tr></form>
	<?php } ?>
	</table>
	
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>