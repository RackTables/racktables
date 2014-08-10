<?php if (defined("RS_TPL")) {?>
	<form method=post id=<?php $this->opname ?>  name=<?php $this->opname ?> 
		action='?module=redirect&page=<?php $this->pageno ?>&tab=<?php $this->tabno ?>&op=<?php $this->opname ?>'
		<?php if ($this->is("isUpload",true)) { ?>
			enctype='multipart/form-data'
		<?php } ?> 
	>
	<?php $this->startLoop("loopArray"); ?>	
		<input type=hidden name="<?php $this->name ?>" value="<?php $this->val ?>">
	<?php $this->endLoop(); ?> 

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>