<?php if (defined("RS_TPL")) {?>
	<?php 
	$Id = $this->_Port_Id;

	if($this->is('Port_Id')){ $this->addJS (
<<<END
			$(document).ready(function() {
				var anchor = document.getElementsByName('port-${Id}')[0];
					if (anchor)
			anchor.scrollIntoView(false);
			});
END
				, TRUE);
			}
	$port_config = $this->_Port_Config;
	$this->addJS(<<<END
$(document).ready(function(){
	var confData = $.parseJSON('${port_config}');
	applyConfData(confData);
	var menuItem = $('.context-menu-item.itemname-conf');
	menuItem.addClass($.contextMenu.disabledItemClassName);
	setItemIcon(menuItem[0], 'ok');
});
END
	, TRUE);
	?>

	<table cellspacing=0 cellpadding=5 align=center class=widetable width="100%">
	<?php if($this->is('Maxdecisions', TRUE)){
			?> <tr><th colspan=2>&nbsp;</th><th colspan=3>discard</th><th>&nbsp;</th></tr>
	<?php } ?>
	<tr valign=top><th>port</th><th width="40%">last&nbsp;saved&nbsp;version</th>

	<?php if($this->is('Maxdecisions', TRUE)){

			$this->addJS('js/racktables.js');
			$this->getH('PrintOpFormIntro', array('resolve8021QConflicts', array ('mutex_rev' => $vswitch['mutex_rev'])));
			$this->startLoop('Looparray2');

			?> <th class=tdcenter><input type=radio name=column_radio value=<?php $this->Position; ?> 
			onclick="checkColumnOfRadios('i_', <?php $this->Maxdecision; ?>, '_<?php $this->Position; ?>')"></th>
	<?php 	$this->endLoop(); }	?>

	<th width="40%">running&nbsp;version</th></tr>

	<?php $this->Loop; ?>

	<?php if($this->is('Rownum_Set', TRUE)){ ?> <input type=hidden name=nrows value=<?php $this->Rownum; ?>> 
				<tr><td colspan=2>&nbsp;</td><td colspan=3 align=center class=tdcenter>
			<?php $this->getH('PrintImageHref', array('UNLOCK', 'resolve conflicts', TRUE));
			?> </td><td>&nbsp;</td></tr>
	<?php } ?>
	</table></form>


<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>