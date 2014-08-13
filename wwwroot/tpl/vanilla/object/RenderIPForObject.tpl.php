<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Allocations</h2>
		<table cellspacing=0 cellpadding='5' align='center' class='widetable'><tr>
		<th>&nbsp;</th>
		<th>OS interface</th>
		<th>IP address</th>
		<?php if ($this->is("isExt_ipv4",true)) { ?>
			<th>network</th>
			<th>routed by</th>
		<?php } ?> 
		<th>type</th>
		<th>misc</th>
		<th>&nbsp;</th>
		</tr>
		<?php if ($this->is('isAddNewOnTop', true)) { ?>
			<?php $this->alloc_elems ?>
		<?php } ?> 
		
		<?php while ($this->loop('printNewItemTR_mod')) : ?>
			<?php $this->getH("PrintOpFormIntro", array('add')); ?>
			<tr><td>
			<?php $this->getH("PrintImageHref", array('add', 'allocate', TRUE)); ?>
			</td>
			<td class=tdleft><input type='text' size='10' name='bond_name' tabindex=100></td>
			<td class=tdleft><input type=text name='ip' tabindex=101></td>
			<?php if ($this->is("isExt_ipv4", true)) { ?>
				<td colspan=2>&nbsp;</td>
			<?php } ?> 
			<td><?php $this->bondPrintSel ?></td>
			</td><td>&nbsp;</td><td>
			<?php $this->getH("PrintImageHref", array('add', 'allocate', TRUE, 103)); ?>
			</td></tr></form>
		<?php endwhile ?>
		
		<?php if (!$this->is('isAddNewOnTop', true)) { ?>
			<?php $this->alloc_elems ?>
		<?php } ?>
		</table><br>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>