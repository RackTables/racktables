<?php if (defined("RS_TPL")) {?>
	<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center'>
	<caption>The following ports can be quickly added:</caption>
	<tr><th>type</th><th>name</th></tr>
	<?php $this->startLoop("allAutoPorts"); ?>	
		<tr><td><?php $this->type ?></td><td><?php $this->name ?></td></tr>
	<?php $this->endLoop(); ?> 
	<?php $this->getH("PrintOpFormIntro", array('generate')); ?>
	<tr><td colspan=2 align=center>
	<input type=submit value='Generate'>
	</td></tr>
	</table></form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>