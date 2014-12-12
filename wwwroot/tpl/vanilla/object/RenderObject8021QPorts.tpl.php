<?php if (defined("RS_TPL")) {?>
	<table border=0 width="100%"><tr valign=top><td class=tdleft width="50%">
	<table cellspacing=0 cellpadding=5 align=center class=widetable>
	<tr><th>port</th><th>interface</th><th>link</th><th width="25%">last&nbsp;saved&nbsp;config</th>
	<?php if ($this->is('IsReqPortName')) { ?>
		<th width="25%">new&nbsp;config</th></tr>	
		<?php $this->getH("PrintOpFormIntro", array('save8021QConfig', array ('mutex_rev' => $this->_Vswitch, 'form_mode' => 'save'))); ?>	
	<?php } else { ?>
		<th>(zooming)</th></tr>
	<?php } ?> 
	<?php $this->JSScripts ?>
	<?php while ($this->loop('PortRows')) : ?>
		<tr class='
		<?php if ($this->is("HasErrors")) { ?>
			trerror
		<?php } else  { 
			$this->TextClass;
			} ?> 
		' valign=top><td<?php $this->TdExtra ?>
		<?php if ($this->is('HasPortName')) { ?>
		 	class="border_highlight"
		<?php } ?>  NOWRAP><a class='interactive-portname port-menu nolink' <?php if ($this->is('HasPortName')) { ?>
		 	name='port-<?php $this->PortId ?>'
		 <?php } ?>><?php $this->PortName ?></a></td><?php if ($this->is('NoSocketColumns')) { ?>
		 	<td>&nbsp;</td><td>&nbsp;</td>
		 <?php } else $this->SocketColumns; ?>
		<td<?php $this->TdExtra ?>><?php $this->TextLeft ?></td><td class=tdright nowrap<?php $this->TdExtra ?>><?php $this->TextRight ?></td></tr>
		<?php $this->SocketRows ?>
	<?php endwhile ?>
	<tr><td colspan=5 class=tdcenter><ul class="btns-8021q-sync">
	<?php if ($this->is("IsToSave")) { ?>
		<input type=hidden name=nports value=<?php $this->Nports ?>>
		<li><?php $this->getH("PrintImageHref", array('SAVE', 'save configuration', TRUE, 100)); ?></li>
	<?php } ?> 
	</form>
	<?php if ($this->is("RecalcPerm")) { ?>
		<li><?php $this->getH("GetOpLink", array(array ('op' => 'exec8021QRecalc'), '', 'RECALC', 'Recalculate uplinks and downlinks')); ?></li>
	<?php } ?> 
	</ul></td></tr></table>
	<?php if ($this->is('IsReqPortName')) { ?>
		</form>
	<?php } ?>
	</td>
	<?php if ($this->is("HasPortOpt")) { ?>
		<td>
		<?php if ($this->is('SinglePort')) { ?>
			&nbsp;
		<?php } else { ?>
		<div class=portlet>
			<h2>port duplicator</h2>
			<table border=0 align=center>
			<?php $this->getH('PrintOpFormIntro', array('save8021QConfig', array ('mutex_rev' => $this->_Vswitch, 'form_mode' => 'duplicate'))); ?>
			<tr><td><?php $this->getH("PrintSelect", array($this->_PortOpt, array ('name' => 'from_port'))); ?></td></tr>
			<tr><td>&darr; &darr; &darr;</td></tr>
			<tr><td><?php $this->getH("PrintSelect", array($this->_PortOpt, array ('name' => 'to_ports[]', 'size' => $this->_MaxSelSize, 'multiple' => 1))); ?></td></tr>
			<tr><td><?php $this->getH("PrintImageHref", array('COPY', 'duplicate', TRUE)); ?></td></tr>
			</form></table>
		</div>
		<?php } ?>
		</td>
	<?php } else {
		$this->TrunkPortlets;
		} ?> 
	</tr></table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>