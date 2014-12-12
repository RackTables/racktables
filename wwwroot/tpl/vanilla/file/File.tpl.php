<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview cellspacing=0 cellpadding=0>
	<tr><td colspan=2 align=center><h1><?php $this->Name; ?></h1></td></tr>
	<tr>
		<td class=pcleft>
			<?php $this->FileSummary; ?>
			<?php $this->FileLinks; ?>
		</td>
		<?php if($this->is('FilePreview')) { ?>
			<td class=pcright>
				<div class=portlet>
					<h2>preview</h2>
					<?php $this->FilePreview; ?>
				</div>
			</td>
		<?php } ?>
	</tr>
	</table>		
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>