<?php if (defined("RS_TPL")) {?>

	<div class=portlet><h2>Optional attributes</h2>
	<table cellspacing=0 cellpadding=5 align=center class=widetable>
	<tr><th>&nbsp;</th><th>Name</th><th>Type</th><th>&nbsp;</th></tr>
	<?php if($this->is('NewTop')) : ?>
		<?php $this->getH('PrintOpFormIntro', 'add'); ?>
		<tr><td>
			<?php $this->getH('PrintImageHref', array('create', 'Create attribute', TRUE)); ?>
		</td><td><input type=text tabindex=100 name=attr_name></td><td>
			<?php $this->CreateNewSelect; ?>
		</td><td>
			<?php $this->getH('PrintImageHref', array('create', 'Create attribute', TRUE, 102)); ?>
		</td></tr></form>
	<?php endif ?>
	<?php	$this->startLoop('AllAttrMaps'); ?>
		<?php $this->OpFormIntro ?>
		<tr><td>	
		<?php $this->DestroyImg ?>
		</td><td><input type=text name=attr_name value='<?php $this->Name; ?>'></td>
		<td class=tdleft><?php $this->Type; ?></td><td>
		<?php $this->SaveImg ?>
		</td></tr></form>
	<?php $this->endLoop(); ?>
	<?php if(!$this->is('NewTop')) : ?>
		<?php $this->getH('PrintOpFormIntro', 'add'); ?>
		<tr><td>
			<?php $this->getH('PrintImageHref', array('create', 'Create attribute', TRUE)); ?>
		</td><td><input type=text tabindex=100 name=attr_name></td><td>
			<?php $this->CreateNewSelect; ?>
		</td><td>
			<?php $this->getH('PrintImageHref', array('create', 'Create attribute', TRUE, 102)); ?>
		</td></tr></form>
	<?php endif ?>
	</table></div>
		
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>