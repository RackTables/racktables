<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2><?php $this->AttrId ?> </h2>
		<?php while($this->refLoop('AllSects')) { ?>
			<table align=center width=60% border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
			<caption><?php $this->Title ?></caption>
			<?php $this->CountMod ?>
			<?php //$this->ResOut ?>
			<?php if (!$this->is('Expiration_Results', array())) { ?>
				<tr valign=top><th align=center>Count</th><th align=center>Name</th>
				<th align=center>Asset Tag</th><th align=center>OEM S/N 1</th><th align=center>Date Warranty <br> Expires</th></tr>
				<?php while($this->refLoop('Expiration_Results')) { ?>
					<tr class=<?php $this->ClassOrder; ?> valign=top>
					<td><?php $this->Count ?> </td>
					<td><?php $this->Mka ?> </td>
					<td><?php $this->AssetNo ?> </td>
					<td><?php $this->OemSn1 ?> </td>
					<td><?php $this->DateValue ?> </td>
					</tr>
				<?php } ?> 
			<?php } ?> 
			</table><br>
		<?php } ?>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>