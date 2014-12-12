<?php if (defined("RS_TPL")) {?>
	<table class=objview border=0 width="100%"><tr><td class=pcleft>
	<div class=portlet>
		<h2>Connectors</h2>
		<?php $this->ConnectorViewer ?>
	</div>
	<div class=portlet>
		<h2>Connector compatibility</h2>
		<?php $this->CompViewr ?>
	</div>
	</td><td class=pcright>
	<div class=portlet>
		<h2>Cable types</h2>
		<?php $this->TypeViewer ?>
	</div>
	<div class=portlet>
		<h2>Cable types and port outer interfaces</h2>
		<?php $this->InterfacesViewer ?>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>