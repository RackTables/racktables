<?php if (defined("RS_TPL")) {?>
	<td class="tdleft <?php $this->TDClass; ?>" style="padding-left: <?php $this->Indent; ?>px;">
	<?php if($this->is('Symbol')) { ?>
		<?php if ($this->is('SymbolLink')) { ?>
			<a href='<?php $this->SymbolLink; ?>'>
		<?php } ?>
			<?php $this->getH('PrintImageHREF',$this->_Symbol); ?>
		<?php if ($this->is('SymbolLink')) { ?>
			</a>
		<?php } ?>
	<?php } ?>
	<?php if($this->is('ID')) { ?>
		<a name='net-<?php $this->ID; ?>' href='index.php?page=ipv<?php $this->IPVersion; ?>net&id=<?php $this->ID; ?>'>
	<?php } ?>
		<?php $this->Formatted; ?>
	<?php if($this->is('ID')) { ?>
		</a>
	<?php } ?>
	<?php if($this->is('VLAN')) { ?>
		<br />
		<?php $this->VLAN; ?>
	<?php } ?>
	</td>
	<td class='tdleft <?php $this->TDClass; ?>'>
	<?php if($this->is('ID')) { ?>
		<?php $this->getH('NiftyString',$this->_Name); ?>
		<?php if($this->is('Tags')) { ?>
			<br><small><?php $this->Tags; ?></small>
		<?php } ?>
	<?php } else { ?>
		<?php $this->getH('PrintImageHREF',array('dragons', 'Here be dragons.')); ?>
		<?php if ($this->is('KnightLink')) { ?>
			<a href='<?php $this->KnightLink; ?>'><?php $this->getH('PrintImageHREF',array('knight','create network here')); ?></a>		
		<?php } ?>
	<?php } ?>
	</td>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>