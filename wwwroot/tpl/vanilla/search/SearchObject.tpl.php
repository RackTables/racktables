<?php if (defined("RS_TPL")) {?>

	<tr class=row_<?php $this->get("rowOrder"); ?> valign=top>
	<td> <?php $this->get("objImage") ?> </td><td class=tdleft>

	<?php if ($this->is("ObjectsByAttr",true)) { ?>
		<ul>
			<?php $this->startLoop("Objects_Attr"); ?>
				<li><?php $this->get("Attr_Name"); ?> matched</li>
			<?php $this->endLoop(); ?>
		</ul>
	<?php } ?>
		
	<?php if ($this->is("ObjectsBySticker",true)) { ?>
		<table>
			<?php $this->startLoop("Objects_Sticker"); ?>

			<tr><th width='50%' class=sticker><?php $this->get("Name");?>: </th>
				<td class=sticker>" <?php $this->get("AttrValue"); ?> "</td></tr>
			<?php $this->endLoop(); ?>
		</table>
	<?php } ?>

	<?php if ($this->is("ObjectsByPort",true)) { ?>
		<table>
			<?php $this->startLoop("Objects_Port"); ?>
			<tr><td><?php $this->get("Href"); ?></td>
				<td class=tdleft><?php $this->get("Text"); ?></td></tr>
			<?php $this->endLoop(); ?>
		</table>				
	<?php } ?>

	<?php if ($this->is("ObjectsByIface",true)) { ?>
		<ul>
			<?php $this->startLoop("LogTableData"); ?>
			<li>interface <?php $this->get("Ifname"); ?></li>
			<?php $this->endLoop(); ?>
		</ul>
	<?php } ?>

		<!-- <?php $this->get("ObjectsByNAT");?> -->
	<?php if ($this->is("ObjectsByNAT",true)) { ?>
		<ul>
			<?php $this->startLoop("Objects_NAT"); ?>
			<li><?php $this->get("Comment"); ?></li>
			<?php $this->endLoop(); ?>
		</ul>			
	<?php } ?>

	<?php if ($this->is("ObjectsByCableID",true)) { ?>
		<ul>
			<?php $this->startLoop("Objects_CableID"); ?>
			<li>link cable ID: <?php $this->get("CableID"); ?></li>
			<?php $this->endLoop(); ?>
		</ul>
	<?php } ?>	
	</td></tr>	


<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>