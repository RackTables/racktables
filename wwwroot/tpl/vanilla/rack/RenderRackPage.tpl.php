<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview cellspacing=0 cellpadding=0><tr>
		<td class=pcleft>
			<?php $this->InfoPortlet ?>
			<?php $this->FilesPortlet ?>
		</td>
		<td class=pcright>
		<div class=portlet>
				<h2>Rack diagram</h2>
				<?php $this->RenderedRack ?>
			</div>	
		</td>
	</tr></table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>