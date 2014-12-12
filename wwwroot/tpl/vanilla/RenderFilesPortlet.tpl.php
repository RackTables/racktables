<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2> files (<?php $this->countFiles ?>) </h2>
		<table cellspacing=0 cellpadding='5' align='center' class='widetable'>
		<tr><th>File</th><th>Comment</th></tr>
		<?php $this->startLoop("filesOutArray"); ?>	
			<tr valign=top><td class=tdleft>
			<?php $this->fileCell ?>
			</td><td class=tdleft><?php $this->comment ?> </td></tr> 
			<?php $this->pcode ?> 
		<?php $this->endLoop(); ?> 
		</table><br>
	</div>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>