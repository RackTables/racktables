<?php if (defined("RS_TPL")) {?>
	<?php $this->addRequirement("Header","HeaderJsInclude",array("path"=>"js/live_validation.js")); ?>
	<?php
		$myregexp = $this->_regexp ; 
		$this->addRequirement("Header","HeaderJsInline",array("code"=>"$(document).ready(function () {
			$('form#add input[name=\"range\"]').attr('match', '${myregexp}');
			Validate.init();
		});")); ?>
	<div class=portlet>
		<h2>Add new</h2><?php $this->getH("PrintOpFormIntro", array('add')); ?>
		<table border=0 cellpadding=5 cellspacing=0 align=center>
		<tr><td rowspan=5><h3>assign tags</h3>
		<?php $this->rendNewEntityTags ?>
		</td>
		<th class=tdright>Prefix</th><td class=tdleft><input type=text name='range' size=36 class='live-validate' tabindex=1 value='<?php $this->prefix_value ?>'></td>
		<tr><th class=tdright>VLAN</th><td class=tdleft>
		<?php $this->optionTree ?><tr>
		<th class=tdright>Name:</th><td class=tdleft><input type=text name='name' size='20' tabindex=3></td></tr>
		<tr><th class=tdright>Tags:</th><td class="tdleft">
		<?php $this->TagsPicker ?>
		</td></tr>
		<tr><td class=tdright><input type=checkbox name="is_connected" tabindex=4></td><th class=tdleft>reserve subnet-router anycast address</th></tr>
		<tr><td colspan=2>
		<?php $this->getH("PrintImageHref", array('CREATE', 'Add a new network', TRUE, 5)); ?>
		</td></tr>
		</table></form><br><br>
	</div>	
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>