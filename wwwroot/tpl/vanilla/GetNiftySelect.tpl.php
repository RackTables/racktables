<?php if (defined("RS_TPL")) {?>
	<?php if ($this->is("isTree",true)) { ?>
		<?php $this->addRequirement("Header","HeaderJsInclude",array("path"=>"js/jquery.optionTree.js")); ?>
		<?php $this->addRequirement("Header","HeaderJsInline",array("code"=>$this->script)); ?>
	<?php } else {?>
		<select
		<?php $this->startLoop("selectAttrs"); ?>	
		 	 <?php $this->attrName ?>=<?php $this->attrVal ?>  
		 <?php $this->endLoop(); ?>  
		>
		<?php $this->startLoop("groupListArr"); ?>	
			<optgroup label='<?php $this->groupName ?>'>
			<?php $this->optionList ?> 
			</optgroup>
		<?php $this->endLoop(); ?></select>
	<?php } ?> 

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>