<?php if (defined("RS_TPL")) {?>
	<table cellspacing=0 cellpadding=5 align=center class=widetable>
	<tr><th>&nbsp;</th><th>base URL</th><th>graph(s)</th><th>&nbsp;</th></tr>
	<?php if($this->is("AddTop", true)) : ?>
		<?php $this->getH("PrintOpFormIntro", array('add')); ?>
			<tr>
				<td><?php $this->getH("PrintImageHref", array('create', 'add a new server', TRUE, 112)); ?></td>
				<td><input type=text size=48 name=base_url tabindex=101></td>
				<td>&nbsp;</td>
				<td><?php $this->getH("PrintImageHref", array('create', 'add a new server', TRUE, 111)); ?></td>
			</tr></form>

		<?php endif ?>

	<?php $this->startLoop("allMuninServers"); ?>	
		<?php $this->FormIntro ?>
		<tr><td>
		<?php $this->DestroyImg ?>
		</td>
		<td><input type=text size=48 name=base_url value="<?php $this->SpecialCharSrv ?>"></td>
		<td class=tdright><?php $this->NumGraphs ?></td>
		<td><?php $this->ImageSave ?></td>
		</tr></form>
	<?php $this->endLoop(); ?> 
	<?php if($this->is("AddTop", false)) : ?>
	<?php $this->getH("PrintOpFormIntro", array('add')); ?>
			<tr>
				<td><?php $this->getH("PrintImageHref", array('create', 'add a new server', TRUE, 112)); ?></td>
				<td><input type=text size=48 name=base_url tabindex=101></td>
				<td>&nbsp;</td>
				<td><?php $this->getH("PrintImageHref", array('create', 'add a new server', TRUE, 111)); ?></td>
			</tr></form>

		<?php endif ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>