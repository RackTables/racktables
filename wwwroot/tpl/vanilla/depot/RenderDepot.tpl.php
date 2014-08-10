<?php if (defined("RS_TPL")) { ?>
	<table border=0 class=objectview>
		<tr>
			<td class=pcleft>
				<?php if($this->is("NoObjects")) { ?>
					<h2>No objects exist</h2>
				<?php } else { ?>
				<div class=portlet>
					<h2> Objects (<?php $this->CountObjs ?>) </h2>
					<br><br><table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
					<tr><th>Common name</th><th>Visible label</th><th>Asset tag</th><th>Row/Rack or Container</th></tr>
					<?php $this->startLoop("AllObjects"); ?>	
						<tr class='row_<?php $this->Order ?> tdleft <?php $this->Problem ?>' valign=top><td> <?php $this->Mka ?> 
						<?php $this->RenderedTags ?>
						</td><td> <?php $this->Label ?> </td>
						<td><?php $this->Asset_no ?> </td>
						<td> <?php $this->Places ?> </td>
						</tr>
					<?php $this->endLoop(); ?> 
					</table>
				</div>
				<?php } ?>
			</td><td class=pcright width='25%'>
				<?php $this->CellFilterPortlet; ?>
			</td>
		</tr>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>