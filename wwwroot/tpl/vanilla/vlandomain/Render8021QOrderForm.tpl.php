<?php if (defined("RS_TPL")) {?>
	<br><table border=0 cellspacing=0 cellpadding=5 align=center>
	<tr>
	<?php if ($this->is("isNoObject",true)) { ?>
		<th>switch</th>
	<?php } ?> 
	<?php if ($this->is("isNoVLANDomain",true)) { ?>
		<th>domain</th>
	<?php } ?>
	<?php if ($this->is("isNoVST",true)) { ?>
	 	<th>template</th>
	<?php } ?>
	<th>&nbsp;</th></tr>  
	<?php $this->AddNewTop ?>
	<?php $this->AllMinusLines ?>
	<?php $this->AddNewBottom ?>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>