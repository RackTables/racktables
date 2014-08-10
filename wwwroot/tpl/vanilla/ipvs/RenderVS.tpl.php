<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview cellspacing=0 cellpadding=0>
	<?php if ($this->is('Name')) { ?>
		<tr><td colspan=2 align=center><h1><?php $this->Name; ?></h1></td></tr>
	<?php } ?>
	<tr>
		<td class=pcleft>
			<?php $this->Summary; ?>
		</td>
		<td class=pcright>
			<?php $this->SLBTriplets; ?>
		</td>
	</tr>
	<tr>
		<td colspawn=2>
			<?php $this->Files; ?>
		</td>
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>