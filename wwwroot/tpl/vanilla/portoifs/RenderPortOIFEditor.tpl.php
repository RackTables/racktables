<?php if (defined("RS_TPL")) {?>
	<table class=widetable border=0 cellpadding=5 cellspacing=0 align=center>
		<tr><th class=tdleft>Origin</th><th>Key</th><th>Refcnt</th><th>&nbsp;</th><th>Outer Interface</th><th>&nbsp;</th></tr>
		<?php if($this->is('NewTop')) : ?>
			<?php $this->getH("PrintOpFormIntro", array('add')); ?>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td class=tdleft><?php $this->getH("PrintImageHref", array('create', 'create new', TRUE, 110)); ?></td>
					<td class=tdleft><input type=text size=48 name=oif_name tabindex=100></td>
					<td class=tdleft><?php $this->getH("PrintImageHref", array('create', 'create new', TRUE, 110)); ?></td>
				</tr>
			</form>
		<?php endif ?>
		<?php while($this->loop('AllOptions')) : ?>	
			<tr>
				<?php if ($this->is('SmallOif')) { ?>
					<td class=tdleft><?php $this->ComputerImg ?></td>
					<td class=tdleft><?php $this->NiftyS ?></td>
					<td class=tdright><?php $this->Oif_Id ?></td>
					<td>&nbsp;</td>
					<td class=tdleft><?php $this->NiftyString ?></td>
					<td>&nbsp;</td>
				<?php } else { ?>
					<?php $this->UpdOpFormInto ?>
					<td class=tdleft><?php $this->FavImg ?></td>
					<td class=tdleft><?php $this->Oif_Id ?></td>
					<?php if ($this->is('Refcnt')) { ?>
						<td class=tdright><?php $this->Refcnt ?></td>#
						<td class=tdleft><?php $this->NoDestroyImg ?></td>
					<?php } else { ?>
						<td>&nbsp;</td>
						<td class=tdleft><?php $this->DestroyLink ?></td>
					<?php } ?> 
					<td class=tdleft><input type=text size=48 name=oif_name value="<?php $this->NiftyString ?>"></td>
					<td><?php $this->SaveImg ?></td>
					</form>
				<?php } ?> 
			</tr>
		<?php endwhile ?> 
		<?php if(!$this->is('NewTop')) : ?>
			<?php $this->getH("PrintOpFormIntro", array('add')); ?>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td class=tdleft><?php $this->getH("PrintImageHref", array('create', 'create new', TRUE, 110)); ?></td>
					<td class=tdleft><input type=text size=48 name=oif_name tabindex=100></td>
					<td class=tdleft><?php $this->getH("PrintImageHref", array('create', 'create new', TRUE, 110)); ?></td>
				</tr>
			</form>
		<?php endif ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>