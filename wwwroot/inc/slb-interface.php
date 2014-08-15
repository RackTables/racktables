<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

require_once 'slb2-interface.php';

function renderSLBDefConfig()
{
	$defaults = getSLBDefaults();
	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateSubmodule("Payload","RenderSLBDefConfig");
	$mod->setNamespace("ipv4slb");
		
	$mod->addOutput("htmlspecVSconfig", htmlspecialchars($defaults['vsconfig']));
	
	$mod->addOutput("htmlspecRSconfig", htmlspecialchars($defaults['rsconfig']));
}

function renderSLBEntityCell ($cell, $highlighted = FALSE, $parent = null, $placeholder = 'RenderedSLBEntityCell')
{
	$tplm = TemplateManager::getInstance();
	if($parent==null){
		$mod = $tplm->generateModule("RenderSLBEntityCell");
	}
	else
		$mod = $tplm->generateSubmodule($placeholder, "RenderSLBEntityCell", $parent);
	$mod->setNamespace("slb_interface");


	$class = "slbcell realm-${cell['realm']} id-${cell['id']}";
	$a_class = $highlighted ? 'highlight' : '';
	$mod->setOutput("tableClass", $class);
	$mod->setOutput("aClass", $a_class);
	
	switch ($cell['realm'])
	{
	case 'object':
		$mod->setOutput("typeObject",true);
		$mod->setOutput("cellID", $cell['id']);
		$mod->setOutput("cellDName", $cell['dname']);
		break;
	case 'ipv4vs':
		$mod->setOutput("typeIPV4s",true);
		$mod->setOutput("cellID", $cell['id']);
		$mod->setOutput("cellDName", $cell['dname']);
		$mod->setOutput("cellName", $cell['name']);
		break;
	case 'ipvs':
		$mod->setOutput("typeIPVs",true);
		$mod->setOutput("cellID", $cell['id']);
		$mod->setOutput("cellName", $cell['name']);
		break;
	case 'ipv4rspool':
		$mod->setOutput("typeIPV4rspool",true);
		$mod->setOutput("cellID", $cell['id']);
		$mod->setOutput("cellName", !strlen ($cell['name']) ? "ANONYMOUS pool [${cell['id']}]" : niftyString ($cell['name']));

		if ($cell['rscount']){
			$mod->setOutput("showRSCount", true);
			$mod->setOutput("cellRSCount", $cell['rscount']);
		}
		break;
	}
	$mod->setOutput("cellETags", count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;');

	if($parent === null)
		return $mod->run();
}

function renderSLBEditTab ($entity_id)
{
	global $pageno;
	renderSLBTripletsEdit (spotEntity ($pageno, $entity_id));
}

// called exclusively by renderSLBTripletsEdit. Renders form to add new SLB link.
// realms 1 and 2 are realms to draw inputs for
function renderNewSLBItemForm ($realm1, $realm2, $parent = null, $placeholder = 'NewSLBItemForm')
{
	/**
	 * Returns a list of values, a human readable name and options
	 * for the selecttag for a given realm.
	 */
	function get_realm_data ($realm)
	{
		$name = NULL;
		$list = array();
		$options = array();
		switch ($realm)
		{
			case 'object':
				$name = 'Load balancer';
				$list = getNarrowObjectList ('IPV4LB_LISTSRC');
				$options = array ('name' => 'object_id', 'tabindex' => 100);
				break;
			case 'ipv4vs':
				$name = 'Virtual service';
				$list = formatEntityList (listCells ('ipv4vs'));
				$options = array ('name' => 'vs_id', 'tabindex' => 101);
				break;
			case 'ipv4rspool':
				$name = 'RS pool';
				$list = formatEntityList (listCells ('ipv4rspool'));
				$options = array ('name' => 'pool_id', 'tabindex' => 102);
				break;
			default:
				throw new InvalidArgException('realm', $realm);
		}
		return array ('name' => $name, 'list' => $list, 'options' => $options);
	}

	$realm1_data = get_realm_data ($realm1);
	$realm2_data = get_realm_data ($realm2);

	$tplm = TemplateManager::getInstance();
	
	if($parent==null)	
		$mod = $tplm->generateModule("RenderNewSLBItemForm");
	else
		$mod = $tplm->generateSubmodule($placeholder, "RenderNewSLBItemForm", $parent);
	
	$mod->setNamespace("slb_interface");
	
	if (count ($realm1_data['list']) && count ($realm2_data['list']))
		$mod->addOutput('printOpFormIntro', true);

	$mod->addOutput('realm1Name', $realm1_data['name']);
	$mod->addOutput('realm1List', $realm1_data['list']);
	$mod->addOutput('realm1Opt', $realm1_data['options']);

	if (count ($realm1_data['list']) && count ($realm2_data['list']))
		$mod->addOutput('isAdd', true);
	else
	{
		$names = array();
		if (! count ($realm1_data['list']))
			$names[] = 'a ' . $realm1_data['name'];
		if (! count ($realm2_data['list']))
			$names[] = 'a ' . $realm2_data['name'];
		$message = 'Please create ' . (implode (' and ', $names)) . '.';
		showNotice ($message);
		$mod->addOutput('message', $message);
	}
	$mod->addOutput('realm2Name', $realm2_data['name']);
	$mod->addOutput('realm2List', $realm2_data['list']);
	$mod->addOutput('realm2Opt', $realm2_data['options']);

	if($parent==null)
		return $mod->run();
}

// supports object, ipv4vs, ipv4rspool, ipaddress cell types
function renderSLBTriplets ($cell, TemplateModule $parent = null, $placeholder = "RenderedSLBTriplets")
{
	$is_cell_ip = (isset ($cell['ip_bin']) && isset ($cell['vslist']));
	$additional_js_params = $is_cell_ip ? '' : ", {'" . $cell['realm'] . "': " . $cell['id'] . '}';
	$triplets = SLBTriplet::getTriplets ($cell);
	if (count ($triplets))
	{
		$tplm = TemplateManager::getInstance();

		if($parent==null)	
			$mod = $tplm->generateModule("RenderSLBTriplets",  false);
		else
			$mod = $tplm->generateSubmodule($placeholder, "RenderSLBTriplets", $parent);
		$mod->setNamespace("slb_interface");

		$cells = array();
		foreach ($triplets[0]->display_cells as $field)
			$cells[] = $triplets[0]->$field;

		// render table header
		$mod->setOutput("countTriplets", count ($triplets));
			 
		$headers = array
		(
			'object' => 'LB',
			'ipv4vs' => 'VS',
			'ipv4rspool' => 'RS pool',
		);
		$cellHeaderArray = array();
		foreach ($cells as $slb_cell)
			$cellHeaderArray[] = array("header" => $headers[$slb_cell['realm']]);
		$mod->setOutput("cellRealmHeaders", $cellHeaderArray);

		$cellHeaderArray = array();
		foreach (array ('VS config', 'RS config', 'Prio') as $header)
			$cellHeaderArray[] = array("header" => $header);
		$mod->setOutput("cellHeaders", $cellHeaderArray);

		// render table rows
		global $nextorder;
		$order = 'odd';
		$tripletsOutArray = array();
		foreach ($triplets as $slb)
		{
			$tripletArray = array();
			$cells = array();
			foreach ($slb->display_cells as $field)
				$cells[] = $slb->$field;
			$tripletArray["order"] = $order;
			$cellsCont = '';
			foreach ($cells as $slb_cell)
			{
				$highlighted = $is_cell_ip &&
				(
					$slb_cell['realm'] == 'ipv4vs' && $slb->vs['vip_bin'] == $cell['ip_bin'] ||
					$slb_cell['realm'] == 'ipv4rspool' && $slb->vs['vip_bin'] != $cell['ip_bin']
				);
				$cellsCont .= $tplm->generateModule('TDLeftCell',true, array('cont' => renderSLBEntityCell ($slb_cell, $highlighted)))->run();
			}
			$tripletArray["cellsOutput"] = $cellsCont;

			$tripletArray = array_merge($tripletArray, array("vsconfig" => htmlspecialchars ($slb->slb['vsconfig']),
											 "rsconfig" => htmlspecialchars ($slb->slb['rsconfig']),
											 "prio" => htmlspecialchars ($slb->slb['prio'])));
			$order = $nextorder[$order];
			$tripletsOutArray[] = $tripletArray;
		}
		$mod->setOutput("tripletsOutArray", $tripletsOutArray);
			 
		if($parent==null)
			return $mod->run();
	}
}

// renders a list of slb links. it is called from 3 different pages, wich compute their links lists differently.
// each triplet in $triplets array contains balancer, pool, VS cells and config values for triplet: RS, VS configs and pair.
function renderSLBTripletsEdit ($cell)
{
	list ($realm1, $realm2) = array_values (array_diff (array ('object', 'ipv4vs', 'ipv4rspool'), array ($cell['realm'])));
	
	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateSubmodule("Payload","RenderSLBTripletsEdit");
	$mod->setNamespace("slb_interface");

	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		renderNewSLBItemForm( $realm1, $realm2, $mod, 'NewSLBItemFormTop');

	$triplets = SLBTriplet::getTriplets ($cell);
	if (count ($triplets))
	{	

		$cells = array();
		foreach ($triplets[0]->display_cells as $field)
			$cells[] = $triplets[0]->$field;

		$mod->setOutput("tripletsCount", count ($triplets));
			 
		global $nextorder;
		$order = 'odd';
		$allTripletsOutArray = array();
		foreach ($triplets as $slb)
		{
			$tripletOut = array();
			$cells = array();
			foreach ($slb->display_cells as $field)
				$cells[] = $slb->$field;
			$ids = array
			(
				'object_id' => $slb->lb['id'],
				'vs_id' => $slb->vs['id'],
				'pool_id' => $slb->rs['id'],
			);
			$del_params = $ids;
			$del_params['op'] = 'delLB';
			
			$tripletOut['order'] = $order;
			$tripletOut['entitiyCell1'] = renderSLBEntityCell ($cells[0]);
			$tripletOut['entitiyCell2'] = renderSLBEntityCell ($cells[1]);
			$tripletOut['vsconfig'] = htmlspecialchars ($slb->slb['vsconfig']);
			$tripletOut['rsconfig'] = htmlspecialchars ($slb->slb['rsconfig']);
			$tripletOut['prio'] = htmlspecialchars ($slb->slb['prio']);
			$tripletOut['OpFormIntro'] = printOpFormIntro ('updLB', $ids);
			$tripletOut['OpLink'] = getOpLink ($del_params, '', 'DELETE', 'Unconfigure');
			$tripletOut['ImgHref'] = printImageHREF ('SAVE', 'Save changes', TRUE);
			
			$order = $nextorder[$order];
			$allTripletsOutArray[] = $tripletOut;
		}
		$mod->setOutput("allTripletsOutput",$allTripletsOutArray);
	}

	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		renderNewSLBItemForm( $realm1, $realm2, $mod, 'NewSLBItemFormBot');
}

function renderLBList ()
{
	$cells = array();
	foreach (scanRealmByText('object', getConfigVar ('IPV4LB_LISTSRC')) as $object)
		$cells[$object['id']] = $object;
	
	$tplm = TemplateManager::getInstance();
		
	renderCellList ('object', 'items', FALSE, $cells, $tplm->getMainModule(), "Payload");
}

function renderRSPool ($pool_id)
{	
	$poolInfo = spotEntity ('ipv4rspool', $pool_id);
	
	$tplm = TemplateManager::getInstance();

	$mod = $tplm->generateSubmodule("Payload","RenderRSPool");
	$mod->setNamespace("slb_interface");

	if (strlen ($poolInfo['name'])){
		$mod->setOutput("PoolInfo", $poolInfo['name']);
	}

	$summary = array();
	$summary['Pool name'] = $poolInfo['name'];
	$summary['Real servers'] = $poolInfo['rscount'];
	$summary['VS instances'] = $poolInfo['refcnt'];
	$summary['tags'] = '';
	$summary['VS configuration'] = '<div class="dashed slbconf">' . htmlspecialchars ($poolInfo['vsconfig']) . '</div>';
	$summary['RS configuration'] = '<div class="dashed slbconf">' . htmlspecialchars ($poolInfo['rsconfig']) . '</div>';

	renderEntitySummary ($poolInfo, 'Summary', $summary, $mod, 'RenderedEntity');
 
	callHook ('portletRSPoolSrv', $pool_id, $mod, 'RSPoolSrvPortlet');
	
	$mod->setOutput("RenderedSLBTrip2", renderSLBTriplets2 ($poolInfo));
	$mod->setOutput("RenderedSLBTrip", renderSLBTriplets ($poolInfo));	
	$mod->setOutput("RenderedFiles", renderFilesPortlet ('ipv4rspool', $pool_id)); 
}

function portletRSPoolSrv ($pool_id, $parent = null, $placeholder = 'RSPoolSrvPortlet')
{
	$poolInfo = spotEntity ('ipv4rspool', $pool_id);
	if ($poolInfo['rscount'])
	{
		$tplm = TemplateManager::getInstance();
		
		if($parent==null)	
			$mod = $tplm->generateModule('PortletRSPoolSrv');
		else
			$mod = $tplm->generateSubmodule($placeholder, 'PortletRSPoolSrv', $parent);
		
		$mod->setNamespace('slb_interface');
		
		$rs_list = getRSListInPool ($poolInfo['id']);
		$rs_table = callHook ('prepareRealServersTable', $rs_list);
		$mod->addOutput("RsCount", $poolInfo['rscount']);
			 
		foreach ($rs_table['columns'] as $title)
			$tplm->generateSubmodule('TableHeads', 'StdTableHead', $mod, true, array('Cont' => $title) );
		$allRowsContOut = array();
		foreach ($rs_table['rows'] as $rs)
		{
			$rowCont = '';
			foreach (array_keys ($rs_table['columns']) as $field)
			{
				switch ($field)
				{
					case 'inservice':
						$field_mod = $tplm->generateModule('RSPoolSrvInservice', true);
						if ($rs['inservice'] == 'yes')
							$field_mod->setOutput('ImgCont', printImageHREF ('inservice', 'in service'));
						else
							$field_mod->setOutput('ImgCont', printImageHREF ('notinservice', 'NOT in service'));
						break;
					case 'rsip':
						$field_mod = $tplm->generateModule('RSPoolSrvDefault',
						 true, array('Cont' => mkA ($rs[$field], 'ipaddress', $rs[$field])));
						break;
					case 'rsconfig':
						$field_mod = $tplm->generateModule('RSPoolSrvRsconfig',
						 true, array('Cont' => $rs[$field]));
						break;
					default:
						$field_mod = $tplm->generateModule('RSPoolSrvDefault',
						 true, array('Cont' =>  $rs[$field]));
						break;
				}

				$rowCont .= $field_mod->run();
			}
			$allRowsContOut[] = array('RowCont' => $rowCont);
		}
		$mod->addOutput("AllRowsCont", $allRowsContOut);

		if($parent==null)
			return $mod->run();	
	}
}

function prepareRealServersTable ($rs_list)
{
	$columns = array
	(
		'inservice' => '',
		'rsip' => 'address',
		'rsport' => 'port',
		'rsconfig' => 'RS config',
		'comment' => 'comment',
	);
	$not_seen = $columns;
	foreach ($rs_list as $rs)
		foreach ($rs as $key => $value)
			if (! empty ($value) and isset ($not_seen[$key]))
				unset ($not_seen[$key]);
	foreach (array_keys ($not_seen) as $key)
		if ($key != 'rsip')
			unset ($columns[$key]);
	return array
		(
			'columns' => $columns,
			'rows' => $rs_list,
		);
}

function renderEditRSList ($rs_list, TemplateModule $parent = null)
{
	global $nextorder;
	$tplm = TemplateManager::getInstance();

	if($parent==null)	
		$mod = $tplm->generateModule("RenderEditRSList",  false);
	else
		$mod = $tplm->generateSubmodule("RenderedEditRSList", "RenderEditRSList", $parent);
	$mod->setNamespace("slb_interface");

	// new RS form
	$default_port = getConfigVar ('DEFAULT_SLB_RS_PORT');
	if ($default_port == 0)
		$default_port = '';

	$mod->setOutput("default_port", $default_port);
	$checked = (getConfigVar ('DEFAULT_IPV4_RS_INSERVICE') == 'yes') ? 'checked' : '';
	$mod->setOutput("checked", $checked);	 

	$order = 'even';
	$rs_outTable = array();
	foreach ($rs_list as $rsid => $rs)
	{
		$rs_element = array( 'OpFormIntro' => printOpFormIntro ('updRS', array ('rs_id' => $rsid)),
							 'OpLink' => getOpLink (array('op'=>'delRS', 'id' => $rsid), '', 'delete', 'Delete this real server'),
							 'ImgHref' => printImageHREF ('SAVE', 'Save changes', TRUE),
							 'rs_id' => $rsid,
							 'order' => $order, 
							 'rs_rsip' => $rs['rsip'],
							 'rs_rsport' => $rs['rsport'],
							 'rs_comment' => $rs['comment'],
							 'rs_rsconfig' => $rs['rs_rsconfig']);
				
		$checked = $rs['inservice'] == 'yes' ? 'checked' : '';
		$rs_element['checked'] = $checked;
		$order = $nextorder[$order];
		$rs_outTable[] = $rs_element;
	}
	$mod->setOutput("rs_outTable", $rs_outTable); 

	if($parent==null)
		return $mod->run();
}

function portletRSPoolAddMany ($pool_id, TemplateModule $parent = null)
{
	$tplm = TemplateManager::getInstance();

	if($parent==null)	
		$mod = $tplm->generateModule("PortletRSPoolAddMany",  false);
	else
		$mod = $tplm->generateSubmodule("PortletRSPoolAddMany", "PortletRSPoolAddMany", $parent);
	
	$mod->setNamespace("slb_interface");

	if (getConfigVar ('DEFAULT_IPV4_RS_INSERVICE') == 'yes')
		$mod->setOutput("isGetConfig", true);

	$formats = callHook ('getBulkRealsFormats');
	$mod->setOutput("printedSelect", printSelect ($formats, array ('name' => 'format'))); 

	if($parent==null)
		return $mod->run();
}

function renderRSPoolServerForm ($pool_id)
{
	$poolInfo = spotEntity ('ipv4rspool', $pool_id);
	$tplm = TemplateManager::getInstance();

	$mod = $tplm->generateSubmodule("Payload","RenderRSPoolServerForm");
	$mod->setNamespace("slb_interface");
	$mod->setOutput("PoolInfoRSCount", $poolInfo['rscount']);
	$mod->setOutput("RenderedRSList", renderEditRSList (getRSListInPool ($pool_id)) );	 

	$mod->setOutput("RenderedAddManyPortlet", portletRSPoolAddMany ($pool_id));

}

function getBulkRealsFormats()
{
	return array
	(
		'ssv_1' => 'SSV: &lt;IP address&gt;',
		'ssv_2' => 'SSV: &lt;IP address&gt; &lt;port&gt;',
		'ipvs_2' => 'ipvsadm -l -n (address and port)',
		'ipvs_3' => 'ipvsadm -l -n (address, port and weight)',
	);
}

function renderRSPoolList ()
{
	$tplm = TemplateManager::getInstance();
	
	renderCellList('ipv4rspool', 'RS pools', FALSE, NULL, $tplm->getMainModule(), "Payload");
}

function renderRealServerList ()
{
	global $nextorder;
	$rslist = getRSList ();
	$pool_list = listCells ('ipv4rspool');
	
	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateSubmodule("Payload","RenderRealServerList");
	$mod->setNamespace("ipv4slb");
		
	$order = 'even';
	$last_pool_id = 0;
	$allRslistOut = array();
	foreach ($rslist as $rsinfo)
	{
		if ($last_pool_id != $rsinfo['rspool_id'])
		{
			$order = $nextorder[$order];
			$last_pool_id = $rsinfo['rspool_id'];
		}
		$singleRsinfo = array('order' => $order);
		$dname = strlen ($pool_list[$rsinfo['rspool_id']]['name']) ? $pool_list[$rsinfo['rspool_id']]['name'] : 'ANONYMOUS';
		$singleRsinfo['mkADname'] = mkA ($dname, 'ipv4rspool', $rsinfo['rspool_id']);
		if ($rsinfo['inservice'] == 'yes')
			$singleRsinfo['inserviceImg'] = printImageHREF ('inservice', 'in service');
		else
			$singleRsinfo['inserviceImg'] = printImageHREF ('notinservice', 'NOT in service');
		$singleRsinfo['mkARsinfo'] = mkA ($rsinfo['rsip'], 'ipaddress', $rsinfo['rsip']);
		$singleRsinfo['rsport'] = $rsinfo['rsport'];
		$singleRsinfo['rsconfig'] = $rsinfo['rsconfig'];
		$allRslistOut[] = $singleRsinfo;
	}
	$mod->addOutput("allRslist", $allRslistOut);	 
}


function renderNewRSPoolForm ()
{
	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateSubmodule("Payload","RenderNewRSPoolForm");
	$mod->setNamespace("ipv4slb");
	printTagsPicker (null, $mod, 'TagsPicker');
}

function renderVirtualService ($vsid)
{
	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateSubmodule('Payload', 'RenderVirtualServices');
	$mod->setNamespace('ipv4vs', true);
	
	
	$vsinfo = spotEntity ('ipv4vs', $vsid);
	
	$mod->addOutput('Name', $vsinfo['name']); 
	
	$summary = array();
	$summary['Name'] = $vsinfo['name'];
	$summary['Protocol'] = $vsinfo['proto'];
	$summary['Virtual IP address'] = mkA ($vsinfo['vip'], 'ipaddress', $vsinfo['vip']);
	$summary['Virtual port'] = $vsinfo['vport'];
	$summary['tags'] = '';
	$summary['VS configuration'] = '<div class="dashed slbconf">' . $vsinfo['vsconfig'] . '</div>';
	$summary['RS configuration'] = '<div class="dashed slbconf">' . $vsinfo['rsconfig'] . '</div>';
	$mod->addOutput('Summary', renderEntitySummary ($vsinfo, 'Summary', $summary));

	$mod->addOutput('Slb', renderSLBTriplets ($vsinfo));
	$mod->addOutput('Files', renderFilesPortlet ('ipv4vs', $vsid));
}

function renderVSList ()
{
	$tplm = TemplateManager::getInstance();	
	renderCellList ('ipv4vs', 'Virtual services', FALSE, NULL, $tplm->getMainModule(), "Payload");
}

function renderNewVSForm ()
{

	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateSubmodule("Payload","RenderNewVSForm");
	$mod->setNamespace("ipv4slb");
	
	$default_port = getConfigVar ('DEFAULT_SLB_VS_PORT');
	global $vs_proto;
	if ($default_port == 0)
		$default_port = '';
	
	$mod->addOutput("Default_port", $default_port);
		 
	global $vs_proto;
	$vs_keys = array_keys ($vs_proto);
	$mod->setOutput("Vs_proto", $vs_proto);
	$mod->setOutput("Vs_keys", array_shift ($vs_keys));
	
	printTagsPicker (null, $mod, 'TagsPicker');
}

function renderEditRSPool ($pool_id)
{
	$poolinfo = spotEntity ('ipv4rspool', $pool_id);
	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateSubmodule("Payload", "RenderEditRSPool");
	$mod->setNamespace("slb_interface");

	$mod->setOutput("PoolInfoName", $poolinfo['name']);
	$mod->setOutput("PoolInfoVSConfig", $poolinfo['vsconfig']);
	$mod->setOutput("PoolInfoRSConfig", $poolinfo['rsconfig']);
	printTagsPicker (null, $mod, 'TagsPicker');		

	// clone link
	$mod->setOutput("PoolInfoID", $poolinfo['id']);

	// delete link
	if ($poolinfo['refcnt'] > 0){
		$mod->setOutput("PoolInfoRefcnt", $poolinfo['refcnt']);
	}
}

function renderEditVService ($vsid)
{
	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateSubmodule("Payload", "RenderEditVService");
	$mod->setNamespace("ipv4vs", true);
	
	$vsinfo = spotEntity ('ipv4vs', $vsid);

	$mod->addOutput('Vip', $vsinfo['vip']);
	$mod->addOutput('Vport', $vsinfo['vport']);
	$mod->addOutput('Name', $vsinfo['name']);
	$mod->addOutput('Vsconfig', $vsinfo['vsconfig']);
	$mod->addOutput('Rsconfig', $vsinfo['rsconfig']);			
	
	global $vs_proto;
	$mod->addOutput('Getselect', getSelect ($vs_proto, array ('name' => 'proto'), $vsinfo['proto']));
	printTagsPicker (null, $mod, 'TagsPicker');

	// delete link
	
	$mod->addOutput('Refcnt', $vsinfo['refcnt']);
	$mod->addOutput('Id', $vsinfo['id']);
}

function renderLVSConfig ($object_id)
{
	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateSubmodule("Payload","RenderLVSConfig");
	$mod->setNamespace("object");
	$mod->addOutput("lvsConfig", buildLVSConfig ($object_id));
}
?>
