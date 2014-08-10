<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Current configuration</h2>
		<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center width="70%">
		<tr><th class=tdleft>Option</th><th class=tdleft>Value</th></tr>
		<?php $this->startLoop("allLoadConfigCache"); ?>	
			<tr class=row_<?php $this->order ?> }>
			<td nowrap valign=top class=tdright>
			<?php $this->renderedConfigVarName ?> 
			</td>
			<td valign=top class=tdleft><?php $this->varvalue ?> </td></tr>
		<?php $this->endLoop(); ?> 
		</table>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>