<?php if (defined("RS_TPL")) {?>
	<?php $this->getH("PrintOpFormIntro", array('add')); ?>
	<tr>
	<td class=tdleft><?php $this->getH("PrintImageHref", array('create', 'create new', TRUE, 200)); ?></td>
	<td>&nbsp;</td>
	<td><?php $this->Connector1Opt ?></td>
	<td><?php $this->TypeOpt ?></td>
	<td><?php $this->Connector2Opt ?></td>
	<td><input type=text size=6 name=length value="1.00" tabindex=140></td>
	<td><input type=text size=48 name=description tabindex=150></td>
	<td class=tdleft><?php $this->getH("PrintImageHref", array('create', 'create new', TRUE, 200)); ?></td>
	</tr></form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>