<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview>
		<tr>
			<td class=pcleft>
				<?php if(!$this->is("EmptyResults")) { ?>
				<div class=portlet><h2><?php $this->Title; ?> (<?php $this->CellCount; ?>) </h2>
						<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>
						<?php $this->startLoop("CellListContent"); ?>
							<tr class=row_<?php $this->Order; ?>>
								<td>
									<?php $this->CellContent; ?>
								</td>
							</tr>
						<?php $this->endLoop(); ?>
						</table>
					<?php } else { ?>
						<?php $this->EmptyResults; ?>
					<?php } ?>
				</div>
			</td>
			<td class=pcright>
				<?php $this->CellFilterPortlet; ?>
			</td> 
		</tr>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>