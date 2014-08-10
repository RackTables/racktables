<?php if (defined("RS_TPL")) {?>

	<table class='<?php $this->get("tableClass")?>' >
	<?php if ($this->is("typeObject",true)) { ?>
		<tr><td><a class='<?php $this->get("aClass") ?>' 
			href='index.php?page=object&object_id=<?php $this->cellID ?>'><?php $this->cellDName ?></a>
			</td></tr><tr><td>
			<?php $this->getH("PrintImageHref",array('LB'));?>
			</td></tr>
	<?php } ?>

	<?php if ($this->is("typeIPV4s",true)) { ?>
		<tr><td rowspan=3 width='5%'>
		<?php $this->getH("PrintImageHref",array('VS'));?>
		</td><td>
		<a class='<?php $this->aClass ?>' href='index.php?page=ipv4vs&vs_id=<?php $this->cellID ?>'>
		<?php $this->get("cellDName")?> </a></td></tr><tr><td>
		<?php $this->get("cellName")?> </td></tr>
	<?php } ?>
	
	<?php if ($this->is("typeIPVs",true)) { ?>
		<tr><td rowspan=3 width='5%'>
		<?php $this->getH("PrintImageHref",array('VS'));?>
		</td><td>
		<a class='<?php $this->aClass ?>' href='index.php?page=ipvs&vs_id=<?php $this->cellID ?>'>
		<?php $this->get("cellName")?> </a></td></tr>

	<?php } ?>
	
	<?php if ($this->is("typeIPV4rspool",true)) { ?>
		<tr><td>
		<a class='<?php $this->aClass ?>' href='index.php?page=ipv4rspool&pool_id=<?php $this->cellID ?>'>
		<?php $this->get("cellName");?>
		</a></td></tr><tr><td>

		<?php $this->getH("PrintImageHref",array("RS pool"));?>

		<?php if ($this->is("showRSCount",true)) { ?>
			<small>(<?php $this->get("cellRSCount") ?>)</small>
 		<?php } ?>
 		</td></tr>
	<?php } ?>
	
	<tr><td>

	<?php $this->get("cellETags") ?>

	</td></tr></table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>