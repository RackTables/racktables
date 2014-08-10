<?php if (defined("RS_TPL")) {?>
	<table class=objview border=0 width='100%'><tr><td class=pcleft>
	<?php $this->getH("H2",array("%%RackspaceOverviewHeadline")); ?>
	<table border=0 cellpadding=10 class=cooltable>
	<tr><th class=tdleft>Location</th><th class=tdleft>Row</th><th class=tdleft>Racks</th></tr>
		<?php while($this->loop("OverviewTable")) : ?>
			<tr class=row_<?php $this->get("Order");?>>
				<th class=tdleft><?php $this->get("LocationTree"); ?></th>
				<th class=tdleft><a href='<?php $this->HrefToRow; $this->CellFilterUrlExtra; ?>'><?php $this->RowName; ?></a></th>
				<th class=tdleft>
					<table border=0 cellspacing=5>
					<tr>
						<?php if ($this->is("Rackline")) : ?>
						<?php while($this->loop("Rackline")) : ?>
							<td align=center valign=bottom><a href='<?php $this->get("RackLink"); ?>'>
								<img border=0 width=<?php $this->get("RackImageWidth"); ?> height=<?php $this->get("RackImageHeight"); ?> title='<?php $this->get("RackHeight"); ?> units' src='?module=image&img=minirack&rack_id=<?php $this->get("RackId"); ?>'>
								<br><?php $this->get("RackName");?></a>
							</td>
							<?php if($this->is('NewLine')) : ?>
								</tr>
								</table>
								</th>
								</tr>
								<tr class=row_<?php $this->get("NewRowOrder");?>><th class=tdleft></th><th class=tdleft><?php $this->get("NewRowName") ; ?> (continued)";
								</th><th class=tdleft><table border=0 cellspacing=5><tr>
							<?php endif ?>
						<?php endwhile ?>
						<?php else : ?>
							<td>Empty Row</td>
						<?php endif ?>
					</tr>
					</table>
				</th>
			</tr>
		<?php endwhile ?>
	</table>
	</td><td class=pcright width="25%">
		<?php $this->get("CellFilter"); ?>
		<br />
		<?php $this->get("LocationFilter"); ?>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>