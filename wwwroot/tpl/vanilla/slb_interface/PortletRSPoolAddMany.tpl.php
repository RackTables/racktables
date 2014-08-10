<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2> Add many</h2>
		<?php $this->getH("PrintOpFormIntro", array('addMany')); ?> 
		<table border=0 align=center>
		<tr><td>
		<?php if ($this->is("isGetConfig",true)) { ?>
			<?php $this->getH("PrintImageHREF", array('inservice', 'in service')); ?> 
		<?php } else {?> 
			<?php $this->getH("PrintImageHREF", array('notinservice', 'NOT in service')); ?>
		<?php } ?> 
		</td><td>Format: 
		<?php $this->printedSelect ?>
		</td><td><input type=submit value=Parse></td></tr>
		<tr><td colspan=3><textarea name=rawtext cols=100 rows=25></textarea></td></tr>
		</table>
	</div>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>