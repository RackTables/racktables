<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview cellspacing=0 cellpadding=0>
		<tr><td colspan=2 align=center><h1><?php $this->IP; ?>/<?php $this->Mask; ?></h1><h2><?php $this->Name; ?></h2></td></tr>
		<tr>
			<td class=pcleft width='50%'>
				<?php $this->Summary; ?>
				<?php if ($this->is('Comment')) { ?>
					<div class=portlet>
						<h2>Comment</h2>
						<div class=commentblock>
							<?php $this->Comment; ?>
						</div>	
					</div>
				<?php } ?>
				<?php $this->Files; ?>
			</td>
			<td>
				<div class=portlet>
					<h2>details</h2>
					<?php $this->Addresslist; ?>
				</div>
			</td>
		</tr>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>