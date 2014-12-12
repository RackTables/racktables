<?php if (defined("RS_TPL")) {?>
	<br><table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>
	<tr><th>&nbsp;</th><th class=tdleft><?php $this->LeftHeader ?></th><th class=tdleft><?php $this->RightHeader ?></th></tr>
	<?php while ($this->loop('AddNewTop')) { ?>
		<?php $this->getH("PrintOpFormIntro", array('add')); ?> 
		<tr><th class=tdleft>
		<?php $this->getH("PrintImageHref", array('add', 'add pair', TRUE, 200)); ?> 
		</th><th class=tdleft>
		<?php $this->lOptions ?> 
		</th><th class=tdleft>
		<?php $this->rOptions ?> 
		</th></tr></form>
	<?php } ?>
	<?php while($this->refLoop("AllCompats")) { ?>	
			<tr class=row_<?php $this->Order ?>><td>
			<?php $this->OpLink ?>
			</td><td class=tdleft><?php $this->LeftValue ?></td><td class=tdleft><?php $this->RightValue ?></td></tr>
		<?php } ?> 
	<?php while ($this->loop('AddNewBottom')) { ?>
		<?php $this->getH("PrintOpFormIntro", array('add')); ?> 
		<tr><th class=tdleft>
		<?php $this->getH("PrintImageHref", array('add', 'add pair', TRUE, 200)); ?> 
		</th><th class=tdleft>
		<?php $this->lOptions ?> 
		</th><th class=tdleft>
		<?php $this->rOptions ?> 
		</th></tr></form>
	<?php } ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>