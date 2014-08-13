<?php if (defined("RS_TPL")) {?>
	<?php 
	$Id = $this->_Port_Id;

	if($this->is('Port_Id')){ $this->addJS (
<<<END
			$(document).ready(function() {
				var anchor = document.getElementsByName('port-${Id}')[0];
					if (anchor)
			anchor.scrollIntoView(false);
			});
END
				, TRUE);
			}
	$port_config = $this->_Port_Config;
	$this->addJS(<<<END
$(document).ready(function(){
	var confData = $.parseJSON('${port_config}');
	applyConfData(confData);
	var menuItem = $('.context-menu-item.itemname-conf');
	menuItem.addClass($.contextMenu.disabledItemClassName);
	setItemIcon(menuItem[0], 'ok');
});
END
	, TRUE);
	?>

	<table cellspacing=0 cellpadding=5 align=center class=widetable width="100%">
	<?php if($this->is('Maxdecisions', TRUE)){
			?> <tr><th colspan=2>&nbsp;</th><th colspan=3>discard</th><th>&nbsp;</th></tr>
	<?php } ?>
	<tr valign=top><th>port</th><th width="40%">last&nbsp;saved&nbsp;version</th>

	<?php if($this->is('Maxdecisions', TRUE)){

			$this->addJS('js/racktables.js');
			$this->getH('PrintOpFormIntro', array('resolve8021QConflicts', array ('mutex_rev' => $vswitch['mutex_rev'])));
			$this->startLoop('Looparray2');

			?> <th class=tdcenter><input type=radio name=column_radio value=<?php $this->Position; ?> 
			onclick="checkColumnOfRadios('i_', <?php $this->Maxdecision; ?>, '_<?php $this->Position; ?>')"></th>
	<?php 	$this->endLoop(); }	?>

	<th width="40%">running&nbsp;version</th></tr>

	<?php while ($this->loop('Loop')) : ?>
		<tr class='<?php $this->Trclass; ?>'><td class='tdleft<?php $this->Td_Class; ?>' NOWRAP><a class='interactive-portname port-menu nolink' $anchor><?php $this->Port_Name; ?></a></td>
		<?php 
			if($this->is('Empty_Radioattrs', TRUE)){
				?> <td class='tdleft<?php $this->Left_Extra; ?>'><?php $this->Left_Text; ?></td> <?php
							if($this->is('Maxdecisions', TRUE)){
									?> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <?php 
							}
					?><td class='tdleft<?php $this->Right_Extra; ?>'><?php $this->Right_Text; ?></td>		<?php
			}
			else{
				?><td class='tdleft<?php $this->Left_Extra; ?>'><label for=i_<?php $this->Rownum; ?>_left><?php $this->Left_Text; ?></label></td><?php
					$this->startLoop('Looparray');
						?> <td><input id=i_<?php $this->Rownum; ?>_<?php $this->Position; ?> name=i_<?php $this->Rownum; ?> type=radio value=<?php $this->Position; $this->Attrs; ?>></td> <?php 
										$this->endLoop();
				?> <td class='tdleft<?php $this->Right_Extra; ?>'><label for=i_<?php $this->Rownum; ?>_right><?php $this->Right_Text; ?>label></td><?php
			}
		?>
		</tr>
		<?php
				if($this->is('Item_Mode')){
					?> <input type=hidden name=rm_<?php $this->Rownum; ?> value=<?php $this->Item_Mode; ?>> 
					<input type=hidden name=rn_<?php $this->Rownum; ?> value=<?php $this->Item_Native; ?>>	
					<?php
						$this->startLoop('Looparray2');
								?> <input type=hidden name=ra_<?php $this->Rownum; ?>[] value=<?php $this->A; ?>> <?php
						$this->endLoop();
						?> <input type=hidden name=pn_<?php $this->Rownum; ?> value='<?php $this->Html; ?>'> <?php
				}
		 ?>
	<?php endwhile ?>

	<?php if($this->is('Rownum_Set', TRUE)){ ?> <input type=hidden name=nrows value=<?php $this->Rownum; ?>> 
				<tr><td colspan=2>&nbsp;</td><td colspan=3 align=center class=tdcenter>
			<?php $this->getH('PrintImageHref', array('UNLOCK', 'resolve conflicts', TRUE));
			?> </td><td>&nbsp;</td></tr>
	<?php } ?>
	</table></form>


<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>