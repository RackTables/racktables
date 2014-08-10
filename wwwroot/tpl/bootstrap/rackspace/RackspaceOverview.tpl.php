<?php if (defined("RS_TPL")) {?>
	
<div class="container">
<div class="row" style="padding-top: 0.5cm">

	<div class="col-md-8" >
	<?php $this->getH("H2",array("%%RackspaceOverviewHeadline")); ?>
	<?php $this->get("RackspaceOverviewTable"); ?>
	</div>	

	<div class="col-md-4">
	<?php $this->get("CellFilterPortlet");?>
	<br />
	<?php $this->get("LocationFilterPortlet");?>
	</div>
</div>
</div>
	
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>