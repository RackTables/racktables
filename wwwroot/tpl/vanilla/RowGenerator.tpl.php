
<?php if (defined("RS_TPL")) {?>


<tr class=row_<?php $this->Order; ?>><td><?php $this->Row; ?></td>

	<?php 
		$this->startLoop('Looparray');
			?> <td> <?php
				$this->Content; ?>
				</td>
			<?php $this->endLoop();	
	 ?>

	 </tr>


<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>






