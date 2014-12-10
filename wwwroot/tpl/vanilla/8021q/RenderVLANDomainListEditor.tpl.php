<?php if (defined("RS_TPL")) {?>
	<table cellspacing=0 cellpadding=5 align=center class=widetable>
	<tr><th>&nbsp;</th><th>description</th><th>&nbsp;</th></tr>
	<?php if ($this->is("isAddNew", true)) : ?>
		<?php $this->getH("PrintOpFormIntro", array('add')); ?>
		<tr>
			<td>
				<?php $this->getH("PrintImageHREF", array( 'create', 'create domain', TRUE, 104)); ?> 
			</td>
			<td>
				<input type=text size=48 name=vdom_descr tabindex=102>
			</td>
			<td>
				<?php $this->getH("PrintImageHREF", array( 'create', 'create domain', TRUE, 103)); ?>
			</td>
		</tr>
	</form> 
	<?php endif ?>
	<?php while($this->loop("allDomainStats")): ?>	
		<?php $this->FormIntro ?> 
		<tr><td>
		<?php $this->ImageNoDestroy ?> 	
		<?php $this->LinkDestroy ?> 
		</td><td><input name=vdom_descr type=text size=48 value=<?php $this->NiftyStr ?>> 
		</td><td>
		<?php $this->ImageUpdate ?> 
		</td></tr></form>
	<?php endwhile ?> 
	<?php if ($this->is("isAddNew", false)) : ?>
		<?php $this->getH("PrintOpFormIntro", array('add')); ?>
			<tr>
				<td>
					<?php $this->getH("PrintImageHREF", array( 'create', 'create domain', TRUE, 104)); ?> 
				</td>
				<td>
					<input type=text size=48 name=vdom_descr tabindex=102>
				</td>
				<td>
					<?php $this->getH("PrintImageHREF", array( 'create', 'create domain', TRUE, 103)); ?>
				</td>
			</tr>
		</form>
	<?php endif ?> 
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>