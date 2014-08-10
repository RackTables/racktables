<?php if (defined("RS_TPL")) {?>
	<select 
	<?php $this->startLoop("selectedList"); ?>	
		<?php $this->attr_name ?> = <?php $this->attr_val ?>  
	<?php $this->endLoop(); ?> 
	>
	<?php $this->startLoop("allOptions"); ?>	
		<option value='<?php $this->dict_key ?>'<?php $this->isSelected ?> ><?php $this->dict_val ?> </option>
	<?php $this->endLoop(); ?> 
	</select>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>