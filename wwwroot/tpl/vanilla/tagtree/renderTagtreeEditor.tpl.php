<?php if (defined("RS_TPL")) {?>
		<?php $js = 
<<<JS
function tageditor_showselectbox(e) {
	$(this).load('index.php', {module: 'ajax', ac: 'get-tag-select', tagid: this.id});
	$(this).unbind('mousedown', tageditor_showselectbox);
}
$(document).ready(function () {
	$('select.taglist-popup').bind('mousedown', tageditor_showselectbox);
});			
JS;
	$this->addJS($js,true);
?>
		<?php if($this->is('OTags')) { ?>
			<div class=portlet>
				<h2>fallen leaves</h2>
				<table cellspacing=0 cellpadding=5 align=center class=widetable>
					<tr class=trerror><th>tag name</th><th>parent tag</th><th>&nbsp;</th></tr>
					<?php $this->OTags; ?>
					<?php while($this->loop('OTags')) : ?>
						<?php $this->getH('PrintOpFormIntro',array('updateTag', array ('tag_id' => $this->_ID, 'tag_name' => $this->_Name))); ?>
						<input type=hidden name=is_assignable value=<?php $this->Assignable ?>>
							<tr>
								<td><?php $this->Name; ?></td>
								<td><?php $this->Select; ?></td>
								<td><?php $this->getH('PrintImageHREF',array('save', 'Save changes', TRUE)); ?></td>
							</tr>
						</form>
					<?php endwhile ?>
				</table>
			</div>
		<?php } ?>
			<div class=portlet>
				<h2>tag tree</h2>
				<table cellspacing=0 cellpadding=5 align=center class=widetable>
					<tr><th>&nbsp;</th><th>tag name</th><th>assignable</th><th>parent tag</th><th>&nbsp;</th></tr>
					<?php if($this->is('NewTop')) : ?>
						<?php $this->getH('PrintOpFormIntro','createTag'); ?>
						<tr>
							<td align=left style="padding-left: 16px;"><?php $this->getH('PrintImageHref',array('create', 'Create tag', TRUE)); ?></td>
							<td><input type=text size=48 name=tag_name tabindex=100></td>
							<td class=tdleft> <?php $this->getH("PrintSelect", array(array ('yes' => 'yes', 'no' => 'no'), array ('name' => 'is_assignable', 'tabindex' => 105), 'yes')); ?></td>
							<td><?php $this->getH("PrintSelect", array($this->_CreateNewOptions, array ('name' => 'parent_id', 'tabindex' => 110))); ?></td>
							<td><?php $this->getH('PrintImageHref',array('create', 'Create tag', TRUE, 120)); ?></td>
						</tr>
						</form>
					<?php endif?>
					
					<?php while($this->loop('Taglist')): ?>
						<tr <?php if($this->is('Assignable',false)) { ?>
							<?php if ($this->is('hasChildren',true)) { ?>
								class=trnull
							<?php } else { ?>
								class=trwarning
							<?php } ?>
						<?php } ?>>
							<td align=left style='padding-left: <?php echo $this->_Level * 16; ?>px;'>
								<?php if ($this->is('hasChildren', true)) { ?>
									<img width="16" border="0" height="16" src="?module=chrome&uri=pix/node-expanded-static.png"></img>
								<?php } ?>
								<?php if ($this->is('hasReferences',true) || $this->is('hasChildren', true)) { ?>
									<img width="16" border="0" height="16" src="?module=chrome&uri=pix/tango-user-trash-16x16-gray.png" title="<?php $this->References; ?> references, <?php $this->Subtags; ?> subtags'?>"></img>
								<?php } else { ?>
									<?php $this->getH('GetOpLink',array(array ('op' => 'destroyTag', 'tag_id' => $this->_ID), '', 'destroy', 'Delete tag')); ?>	
								<?php } ?>
							</td><td>
								<?php $this->getH('PrintOpFormIntro',array('updateTag', array ('tag_id' => $this->_ID))) ; ?>
								<input type=text size=48 name=tag_name value="<?php $this->Tag; ?>">
							</td>
							<td class=tdleft>
								<?php if($this->is('References', true)) { ?>
									<?php $this->getH("PrintSelect", array(array ('yes' => 'yes'), array ('name' => 'is_assignable'))); ?>
								<?php } else {?>
									<?php $this->getH("PrintSelect", array(array ('yes' => 'yes', 'no' => 'no'), array ('name' => 'is_assignable'), $this->_AssignableInfo)); ?>
								<?php } ?>
							</td>
							<td class=tdleft>
								<?php $this->ParentSelect; ?>
							</td>
							<td>
								<?php $this->getH('PrintImageHREF',array('save', 'Save changes', TRUE)); ?> </form>
							</td>
						</tr>
					<?php endwhile ?>					
					
					<?php if(!$this->is('NewTop')) : ?>
						<?php $this->getH('PrintOpFormIntro','createTag'); ?>
						<tr>
							<td align=left style="padding-left: 16px;"><?php $this->getH('PrintImageHref',array('create', 'Create tag', TRUE)); ?></td>
							<td><input type=text size=48 name=tag_name tabindex=100></td>
							<td class=tdleft> <?php $this->getH("PrintSelect", array(array ('yes' => 'yes', 'no' => 'no'), array ('name' => 'is_assignable', 'tabindex' => 105), 'yes')); ?></td>
							<td><?php $this->getH("PrintSelect", array($this->_CreateNewOptions, array ('name' => 'parent_id', 'tabindex' => 110))); ?></td>
							<td><?php $this->getH('PrintImageHref',array('create', 'Create tag', TRUE, 120)); ?></td>
						</tr>
						</form>
					<?php endif?>
				</table>
			</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>