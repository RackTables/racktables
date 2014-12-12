<?php if (defined("RS_TPL")) {?>

		<tr class=row_<?php $this->Order ?> valign=top>

			<td class=tdleft><?php $this->Date; ?><br><?php $this->User; ?></td>
			<td class="logentry"><?php $this->Hrefs; ?></td>
			<td class=tdleft>
			<?php $this->getH('GetOpLink', array(array('op'=>'del', 'log_id'=>$this->_Id), '', 'DESTROY', 'Delete log entry')); ?>
			</td>
		</tr>


<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>