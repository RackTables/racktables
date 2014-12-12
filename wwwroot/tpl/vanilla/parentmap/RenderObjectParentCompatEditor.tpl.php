<?php if (defined("RS_TPL")) {?>

	<br><table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>
		<tr><th>&nbsp;</th><th>Parent</th><th>Child</th></tr>
		<?php if($this->is("AddTop", true)) : ?>
			<?php $this->getH('PrintOpFormIntro', 'add'); ?>
			<tr><th class=tdleft>
			<?php $this->getH('PrintImageHref', array('add', 'add pair', TRUE)); ?>
			</th><th class=tdleft>
			<?php $this->Parent; ?>
			</th><th class=tdleft>
			<?php $this->Child; ?>
			</th></tr></form>

		<?php endif ?>
		
		<?php $this->startLoop('Looparray'); ?>
		<tr class=row_<?php $this->Order; ?>><td>
		<?php $this->Image; ?>
		</td><td class=tdleft><?php $this->Parentname; ?></td><td class=tdleft><?php $this->Childname; ?></td></tr>
		<?php $this->endLoop(); ?>
		<?php if($this->is("AddTop", false)) : ?>
			
			<?php $this->getH('PrintOpFormIntro', 'add'); ?>
			<tr><th class=tdleft>
			<?php $this->getH('PrintImageHref', array('add', 'add pair', TRUE)); ?>
			</th><th class=tdleft>
			<?php $this->Parent; ?>
			</th><th class=tdleft>
			<?php $this->Child; ?>
			</th></tr></form>

		<?php endif ?>
		</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>