<?php if (defined("RS_TPL")) {?>
	<table width="100%" border=0>
	<?php $this->startLoop("allLines"); ?>	
		<tr><td class=tdright><a name=line${lineno}><?php $this->lineno ?> </a></td>
		<td class=tdleft><?php $this->line ?> </td></tr>
	<?php $this->endLoop(); ?> 
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>