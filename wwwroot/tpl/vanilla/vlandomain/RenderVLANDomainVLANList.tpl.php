<?php if (defined("RS_TPL")) {?>
	<table cellspacing=0 cellpadding=5 align=center class=widetable>
	<tr><th>&nbsp;</th><th>ID</th><th>propagation</th><th>description</th><th>&nbsp;</th></tr>
	<?php if ($this->is('NewTop')) { ?>
		<?php $this->getH("PrintOpFormIntro", array('add')); ?>
			<tr>
				<td>
					<?php $this->getH("PrintImageHref", array('create', 'add VLAN', TRUE, 110)); ?>
				</td><td>
					<input type=text name=vlan_id size=4 tabindex=101>
				</td><td>
					<?php $this->getH("PrintSelect", array( $this->_VTOptions,  array ('name' => 'vlan_type', 'tabindex' => 102), 'ondemand')); ?>
				</td><td>
					<input type=text size=48 name=vlan_descr tabindex=103>
				</td><td>
					<?php $this->getH("PrintImageHref", array('create', 'add VLAN', TRUE, 110)); ?>
				</td>
			</tr>
		</form>
	<?php } ?>
	<?php $this->startLoop("allDomainVLANs"); ?>	
		<?php $this->opIntro ?>
		<tr><td>
		<?php $this->portc ?>
		</td><td class=tdright><tt><?php $this->vlan_id ?></tt></td><td>
		<?php $this->printSel ?>
		</td><td>
		<input name=vlan_descr type=text size=48 value="<?php $this->htmlSpecialChr ?>">
		</td><td>
		<?php $this->saveImg ?>
		</td></tr></form>
	<?php $this->endLoop(); ?> 
	<?php if (!$this->is('NewTop')) { ?>
		<?php $this->getH("PrintOpFormIntro", array('add')); ?>
		<tr>
			<td>
				<?php $this->getH("PrintImageHref", array('create', 'add VLAN', TRUE, 110)); ?>
			</td><td>
				<input type=text name=vlan_id size=4 tabindex=101>
			</td><td>
				<?php $this->getH("PrintSelect", array( $this->_VTOptions,  array ('name' => 'vlan_type', 'tabindex' => 102), 'ondemand')); ?>
			</td><td>
				<input type=text size=48 name=vlan_descr tabindex=103>
			</td><td>
				<?php $this->getH("PrintImageHref", array('create', 'add VLAN', TRUE, 110)); ?>
			</td>
		</tr>
		</form>
	<?php } ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>