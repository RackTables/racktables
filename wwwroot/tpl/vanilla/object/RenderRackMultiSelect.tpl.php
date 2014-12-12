<?php if (defined("RS_TPL")) {?>
	<select name=<?php $this->sname ?> multiple size=<?php $this->maxselsize ?> onchange='getElementsByName("updateObjectAllocation")[0].submit()'>
	<?php $this->startLoop("allRowData"); ?>	
		<optgroup label='<?php $this->GroupLabel ?>'>
		<?php $this->RackEntries ?>
	<?php $this->endLoop(); ?> 
	</select>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>