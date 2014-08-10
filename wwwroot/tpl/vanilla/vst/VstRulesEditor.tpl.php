<?php if (defined("RS_TPL")) {?>

	<?php
		
	
		$row_html  = "<td><a href='#' class='vst-del-rule'> <img width='16' height='16' border='0' title='delete rule' src='?module=chrome&uri=pix/tango-list-remove.png'> </a></td>"; 
		$row_html .= "<td><input type=text name=rule_no value='' size=3></td>";
		$row_html .= "<td><input type=text name=port_pcre value=''></td>";
		$row_html .= '<td>' . $this->_AccessSelect . '</td>';
		$row_html .= "<td><input type=text name=wrt_vlans value=''></td>";
		$row_html .= "<td><input type=text name=description value=''></td>";
		$row_html .= "<td><a href='#' class='vst-add-rule'> <img width='16' height='16' border='0' title='Duplicate rule' src='?module=chrome&uri=pix/tango-list-add.png'> </a></td>"; 
		$this->addJS("var new_vst_row = \"" . addslashes(trim(preg_replace('/\s+/', ' ', $row_html))) . "\";", TRUE);
		$this->addJS('js/vst_editor.js');
	?>
	<center><h1> <?php $this->Nifty; ?> </h1></center>

	<?php if($this->is('Count', true)){ ?>
	<div class=portlet><h2>clone another template</h2>
	<?php $this->getH('PrintOpFormIntro', 'clone'); ?>
	<input type=hidden name="mutex_rev" value=" <?php $this->VstMutexRef; ?> ">
	<table cellspacing=0 cellpadding=5 align=center class=widetable>
		<tr>
			<td> <?php $this->AccesSelectClone; ?> </td>
			<td> <?php $this->getH('PrintImageHref', array('COPY', 'copy from selected', TRUE)); ?> </td>
		</tr>
	</table>
	</form>
	</div>
	<div class=portlet><h2>add rules one by one</h2>
	<?php } ?>
		
	<?php $this->getH('PrintOpFormIntro', 'upd'); ?>
	<table cellspacing=0 cellpadding=5 align=center class="widetable template-rules">
		<tr><th class=tdright>Tags:</th><td class=tdleft style='border-top: none;'>
			<?php $this->TagsPicker ?>
		</td></tr>
		<tr><th></th><th>sequence</th><th>regexp</th><th>role</th><th>VLAN IDs</th><th>comment</th><th><a href="#" class="vst-add-rule initial"> <?php $this->getH('PrintImageHref', array('add', 'Add rule')); ?> </a></th></tr>
		<?php $this->startLoop('ItemArray'); ?>
			<tr>
				<td><a href="#" class="vst-del-rule"> <img width="16" height="16" border="0" title="delete rule" src="?module=chrome&uri=pix/tango-list-remove.png"> </a></td>
				<td><input type=text name=rule_no value="<?php $this->RuleNo; ?>" size=3></td>
				<td><input type=text name=port_pcre value="<?php $this->PortPCRE; ?>"></td>
				<td><?php $this->AccessSelectSingle ?></td>
				<td><input type=text name=wrt_vlans value="<?php $this->WRTVlans; ?>"></td>
				<td><input type=text name=description value="<?php $this->Description; ?>"></td>
				<td><a href="#" class="vst-add-rule"> <img width="16" height="16" border="0" title="Duplicate rule" src="?module=chrome&uri=pix/tango-list-add.png"> </a></td>
			</tr>
		<?php $this->endLoop(); ?>
	</table>
	<input type=hidden name="template_json">	
	<input type=hidden name="mutex_rev" value="<?php $this->MutexRev; ?>">
	<center> <?php $this->getH('PrintImageHref', array('SAVE', 'Save template', TRUE)); ?> </center>
	</form>
	<?php $this->VstRules; ?>
	<?php if($this->is('Count', TRUE)) { ?></div><?php } ?>
	
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>