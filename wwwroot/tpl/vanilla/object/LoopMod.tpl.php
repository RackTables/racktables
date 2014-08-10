<?php if (defined("RS_TPL")) {?>


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


<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>