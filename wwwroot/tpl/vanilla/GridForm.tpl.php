<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview cellspacing=0 cellpadding=0>
		<tr><td colspan=2 align=center><h1><?php $this->Name; ?></h1></td></tr>
		<tr>
			<td class=pcleft height='1%' width='50%'>
				<?php $this->InfoPortlet; ?>
			</td>
			<td class=pcright>
				<div class=portlet>
					<h2><?php $this->Header; ?></h2>
					<center>
						<table class=rack border=0 cellspacing=0 cellpadding=1>
							<tr>
								<th width='10%'>&nbsp;</th>
								<th width='20%'><a href='javascript:;' onclick="toggleColumnOfAtoms('<?php $this->Id; ?>', '0', <?php $this->Height; ?>)">Front</a></th>
								<th width='50%'><a href='javascript:;' onclick="toggleColumnOfAtoms('<?php $this->Id; ?>', '1', <?php $this->Height; ?>)">Interior</a></th>
								<th width='20%'><a href='javascript:;' onclick="toggleColumnOfAtoms('<?php $this->Id; ?>', '2', <?php $this->Height; ?>)">Back</a></th>
							</tr>
							<?php $this->getH('PrintOpFormIntro','updateRack'); ?>
							<?php $this->AtomGrid; ?>
						</table>
					</center>
					<br><input type=submit name=do_update value='<?php $this->Submit; ?>'>
					</form>
					<br>
					<br>
				</div>		
			</td>
		</tr>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>