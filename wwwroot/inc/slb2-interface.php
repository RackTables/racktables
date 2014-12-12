<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

function renderVSGList ()
{
	$tplm = TemplateManager::getInstance();
		
	renderCellList ('ipvs', 'VS groups', FALSE, NULL, $tplm->getMainModule(), "Payload");
}

function formatVSPort ($port, $plain_text = FALSE)
{
	if ($port['proto'] == 'MARK')
		return 'fwmark ' . $port['vport'];
	$proto = strtolower ($port['proto']);
	$name = $port['vport'] . '/' . $proto;
	$srv = getservbyport ($port['vport'], $proto);

	if (!$plain_text && FALSE !== $srv){
		$tplm = TemplateManager::getInstance();

		$mod = $tplm->generateModule("formatVSPortInMemory",  true, array("name" => $name, "srv" => $srv));
		return $mod->run();
	}
	else
		return $name;
}

function formatVSIP ($vip, $plain_text = FALSE)
{
	$fmt_ip = ip_format ($vip['vip']);
	if ($plain_text)
		return $fmt_ip;
	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateModule("FormatVSIPInMem", true, array("href" => makeHref (array ('page' => 'ipaddress', 'ip' => $fmt_ip)), 
		   "fmt_ip" => $fmt_ip));

	return $mod->run();
}

function renderVS ($vsid)
{
	$vsinfo = spotEntity ('ipvs', $vsid);
	amplifyCell ($vsinfo);

	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateSubmodule('Payload', 'RenderVS');
	$mod->setNamespace('ipvs');
	
	if (strlen ($vsinfo['name']))
		$mod->addOutput('Name', $vsinfo['name']);

	$summary = array();
	$summary['Name'] = $vsinfo['name'] . getPopupSLBConfig ($vsinfo);
	$summary['tags'] = '';
	
	$smod = $tplm->generateModule('VSSLBList',true);
	
	foreach ($vsinfo['vips'] as $vip)
		$tplm->generateSubmodule('List', 'VSSLBListElement', $smod, true, array('Content'=>formatVSIP ($vip) . getPopupSLBConfig ($vip)));
	
	$summary['IPs'] = $smod->run();

	$smod = $tplm->generateModule('VSSLBList',true);
	foreach ($vsinfo['ports'] as $port)
		$tplm->generateSubmodule('List', 'VSSLBListElement', $smod, true, array('Content'=>formatVSPort ($port) . getPopupSLBConfig ($port)));
	
	$summary['Ports'] = $smod->run();

	renderEntitySummary ($vsinfo, 'Summary', $summary, $mod, 'Summary');

	renderSLBTriplets2 ($vsinfo, FALSE, NULL, $mod, 'SLBTriplets');
	renderFilesPortlet ('ipvs', $vsid, $mod, 'Files');
}

function renderTripletForm ($bypass_id)
{
	global $pageno, $etype_by_pageno;
	$cell = spotEntity ($etype_by_pageno[$pageno], $bypass_id);

	$tplm = TemplateManager::getInstance();
	
	renderSLBTriplets2 ($cell, TRUE, NULL, $tplm->getMainModule(), 'Payload');
}

// either $port of $vip argument should be NULL
function renderPopupTripletForm ($triplet, $port, $vip, $row, TemplateModule $parent = NULL, $placeholder = "RenderedPopupTripletForm")
{
	//Port to template engine
	$tplm = TemplateManager::getInstance();
	
	if($parent === NULL)	
		$mod = $tplm->generateModule("RenderPopupTripletForm");
	else
		$mod = $tplm->generateSubmodule($placeholder, "RenderPopupTripletForm", $parent);

	$mod->setNamespace("slb2_interface");

	$mod->setOutput("opFormIntroPara", (isset ($port) ? 'updPort' : 'updIp'));
	$mod->setOutput("object_id", htmlspecialchars ($triplet['object_id'], ENT_QUOTES));
	$mod->setOutput("vs_id", htmlspecialchars ($triplet['vs_id'], ENT_QUOTES));
	$mod->setOutput("rspool_id", htmlspecialchars ($triplet['rspool_id'], ENT_QUOTES));
	$mod->setOutput("isArray", (is_array ($row) ? ' checked' : ''));
	$mod->setOutput("issetPortTxt", (isset ($port) ? 'Enable port' : 'Enable IP'));
	
	if (isset ($port))
	{
		$mod->setOutput("issetPort", true);
		$mod->setOutput("prot", htmlspecialchars ($port['proto'], ENT_QUOTES));
		$mod->setOutput("prot", htmlspecialchars ($port['vport'], ENT_QUOTES));	 	 	 
	}
	else
	{
		$mod->setOutput("vip",  htmlspecialchars (ip_format ($vip['vip']), ENT_QUOTES));
		$mod->setOutput("prio",  htmlspecialchars (is_array ($row) ? $row['prio'] : '', ENT_QUOTES));
	}

	$mod->setOutput("vsconfig", htmlspecialchars (is_array ($row) ? $row['vsconfig'] : ''));
	$mod->setOutput("rsconfig", htmlspecialchars (is_array ($row) ? $row['rsconfig'] : ''));
	//Ends form created bys FormIntro
	if($parent == NULL)
		return $mod->run();
}

function renderPopupVSPortForm ($port, $used = 0, $parent = null, $placeholder = 'PopupVSPortForm')
{
	$keys = array ('proto' => $port['proto'], 'port' => $port['vport']);
	$title = 'remove port ' . formatVSPort ($port) . ($used ? " (used $used times)" : '');

	$tplm = TemplateManager::getInstance();
	
	if($parent==null)	
		$mod = $tplm->generateModule('PopupVSPortForm');
	else
		$mod = $tplm->generateSubmodule($placeholder, 'PopupVSPortForm', $parent);
	
	$mod->setNamespace('');
	
	$mod->addOutput('Keys', $keys);
	$mod->addOutput('Title', $title);
	$mod->addOutput('Used', $used);
	$mod->addOutput('Vsconfig', htmlspecialchars ($port['vsconfig']));
	$mod->addOutput('Rsconfig', htmlspecialchars ($port['rsconfig']));

	if($parent==null)
		return $mod->run();
}

function renderPopupVSVIPForm ($vip, $used = 0,$parent = null, $placeholder = 'PopupVSPortForm')
{
	$fmt_ip = ip_format ($vip['vip']);
	$title = 'remove IP ' . formatVSIP ($vip) . ($used ? " (used $used times)" : '');

	$tplm = TemplateManager::getInstance();
	
	if($parent==null)	
		$mod = $tplm->generateModule('PopupVSVIPForm');
	else
		$mod = $tplm->generateSubmodule($placeholder, 'PopupVSVIPForm', $parent);
	
	$mod->setNamespace('');
	
	$mod->addOutput('Fmt_ip', $fmt_ip);
	$mod->addOutput('Title', $title);
	$mod->addOutput('Used', $used);
	$mod->addOutput('Vsconfig', htmlspecialchars ($vip['vsconfig']));
	$mod->addOutput('Rsconfig', htmlspecialchars ($vip['rsconfig']));
		 
	if($parent==null)
		return $mod->run();
}

function renderEditVS ($vs_id)
{
	global $vs_proto;
	$vsinfo = spotEntity ('ipvs', $vs_id);
	amplifyCell ($vsinfo);
	$triplets = getTriplets ($vsinfo);

	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateSubmodule('Payload', 'RenderEditVS');
	$mod->setNamespace('ipvs');
	
	$mod->addOutput('Name', htmlspecialchars ($vsinfo['name'], ENT_QUOTES));
	$mod->addOutput('VSConfig', htmlspecialchars ($vsinfo['vsconfig']));
	$mod->addOutput('RSConfig', htmlspecialchars ($vsinfo['rsconfig']));
	// first form - common VS settings

	printTagsPicker(null, $mod, 'TagsPicker');

	// delete link
	$triplets = getTriplets ($vsinfo);
	if (count ($triplets) > 0)
		$mod->addOutput('TripletCount',count($triplets));
	else
	{
		$mod->addOutput('ID', $vsinfo['id']);
		$mod->addOutput('Deletable', true);
	}

	// second form - ports and IPs settings

	getSelect ($vs_proto, array ('name' => 'proto'), null, true, $mod, 'NewPortSelect');

	$outarr = array();
	foreach ($vsinfo['ports'] as $port)
	{
		$used = 0;
		foreach ($triplets as $triplet)
			if (isPortEnabled ($port, $triplet['ports']))
				$used++;
		$outarr[] = array('Port'=>formatVSPort ($port),
						  'SLBConfig'=> getPopupSLBConfig ($port), 'PopupVsPort' => renderPopupVSPortForm ($port, $used));
	}
	$mod->addOutput('VSPorts', $outarr);


	$outarr = array();
	foreach ($vsinfo['vips'] as $vip)
	{
		$used = 0;
		foreach ($triplets as $triplet)
			if (isVIPEnabled ($vip, $triplet['vips']))
				$used++;
		$outarr[] = array('IP'=>formatVSIP($vip),
						  'SLBConfig'=>getPopupSLBConfig ($vip), 'PopupVSVIP' => renderPopupVSVIPForm ($vip, $used));
	}
	$mod->addOutput('VSIps', $outarr);

}

// returns sorted (grouped) array of elements from $tr_list.
// Sets 'span' key for grouped rows, indexed by grouped realms,
// with the value = number of rows in the group
function groupTriplets ($tr_list)
{
	$self = __FUNCTION__;

	// index triplets by ids of fields
	$index = array();
	foreach ($tr_list as $tr)
	{
		if (! isset ($tr['key']))
			$tr['key'] = implode ('-', array ($tr['vs_id'], $tr['rspool_id'], $tr['object_id']));
		$index[$tr['key']] = $tr;
	}

	$ret = array();
	while ($index)
	{
		$seen = array();
		foreach ($index as $tr)
			foreach (array ('ipvs' => 'vs_id', 'ipv4rspool' => 'rspool_id', 'object' => 'object_id') as $realm => $key)
				$seen[$realm . '-' . $tr[$key]][] = $tr;
		// sort $seen by count in groups, desc
		uasort ($seen, 'cmp_array_sizes');
		$seen = array_reverse ($seen, TRUE);
		foreach ($seen as $group_key => $group)
		{
			$group_by = preg_replace ('/-.*/', '', $group_key);
			$first_tr = array_first ($group);
			if (isset ($first_tr['span'][$group_by]))
				// if already grouped by this field, take next group
				continue;
			elseif (count ($group) == 1)
			{
				// dont create groups of 1 element
				if (isset ($index[$first_tr['key']]))
				{
					unset ($index[$first_tr['key']]);
					$ret[] = $first_tr;
				}
			}
			else
			{
				foreach ($group as &$tr_ref)
					$tr_ref['span'][$group_by] = count ($group);
				foreach ($self ($group) as $tr)
					if (isset ($index[$tr['key']]))
					{
						unset ($index[$tr['key']]);
						$ret[] = $tr;
					}
				break;
			}
		}
	}
	return $ret;
}

// supports object, ipvs, ipv4rspool cell types
function renderSLBTriplets2 ($cell, $editable = FALSE, $hl_ip = NULL, TemplateModule $parent = null , $placeholder = "RenderedSLBTriplets2")
{
	$tplm = TemplateManager::getInstance();

	if($parent == null)	
		$mod = $tplm->generateModule("RenderSLBTriplets2");
	else
		$mod = $tplm->generateSubmodule($placeholder, "RenderSLBTriplets2", $parent);
	$mod->setNamespace('slb2_interface');

	list ($realm1, $realm2) = array_values (array_diff (array ('object', 'ipvs', 'ipv4rspool'), array ($cell['realm'])));

	if ($editable && getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		callHook ('renderNewTripletForm', $realm1, $realm2, $mod, 'NewTripletFormTop');

	$fields = array
	(
		'ipvs' => 'vs_id',
		'object' => 'object_id',
		'ipv4rspool' => 'rspool_id',
	);

	$headers = array
	(
		'ipvs' => 'VS',
		'object' => 'LB',
		'ipv4rspool' => 'RS pool',
	);

	$triplets = groupTriplets (getTriplets ($cell));

	// sort $headers by number of grouped cells
	$new_headers = array();
	$grouped_by = array
	(
		'ipvs' => 0,
		'object' => 0,
		'ipv4rspool' => 0,
	);
	foreach ($triplets as $slb)
		if (isset ($slb['span']))
			foreach (array_keys ($slb['span']) as $realm)
				$grouped_by[$realm]++;
	arsort ($grouped_by, SORT_NUMERIC);
	foreach (array_keys ($grouped_by) as $realm)
		$new_headers[$realm] = $headers[$realm];
	$headers = $new_headers;

	// render table header

	if (count ($triplets))
	{
		$mod->setOutput("showTriplets", true);
		$headersArray = array();
		$mod->setOutput("countTriplets", count($triplets));

		foreach ($headers as $realm => $header)
			if ($realm != $cell['realm'])
				$headersArray[] = array("header" => $header);
		$mod->setOutput("headersArray", $headersArray);
	}

	$class = 'slb-checks';
	if ($editable)
		$class .= ' editable';

	// render table rows
	global $nextorder;
	$order = 'odd';
	$span = array();


	foreach ($triplets as $slb)
	{

		$vs_cell = spotEntity ('ipvs', $slb['vs_id']);
		amplifyCell ($vs_cell);

		$tripletArray = array("order" => $order, "slb_object_id" => $slb['object_id'], "slb_vs_id" => $slb['vs_id'], "slb_rspool_id" => $slb['rspool_id']);

		$cellOutputArray = array();
		foreach (array_keys ($headers) as $realm)
		{
			if ($realm == $cell['realm'])
				continue;
			if (isset ($span[$realm]))
			{
				if (--$span[$realm] <= 0)
					unset ($span[$realm]);
			}
			else
			{
				$span_html = '';
				if (isset ($slb['span'][$realm]))
				{
					$span[$realm] = $slb['span'][$realm] - 1;
					$span_html = sprintf ("rowspan=%d", $slb['span'][$realm]);
				}

				$slb_cell = spotEntity ($realm, $slb[$fields[$realm]]);
				$cellOutputArray[] = array("span_html" => $span_html, "slb_cell" => renderSLBEntityCell ($slb_cell));
			}
		}
		$tripletArray["cellOutputArray"] = $cellOutputArray;

		// render ports
		$tripletArray["class"] = $class;
		$portOutputArray = array();
		foreach ($vs_cell['ports'] as $port)
		{
			$row = isPortEnabled ($port, $slb['ports']);
			$singlePort = array("Row_Class" => (($row) ? 'enabled' : 'disabled'));

			$singlePort["formatedVSPort"] = formatVSPort ($port);
			$singlePort["popupSLBConfig"] = getPopupSLBConfig ($row);

			if ($editable)
				$singlePort["tripletForm"] = renderPopupTripletForm ($slb, $port, NULL, $row);
			$portOutputArray[] = $singlePort;
		}
		$tripletArray["portOutputArray"] = $portOutputArray;		

		// render VIPs
		$vipAllOutput = array();
		foreach ($vs_cell['vips'] as $vip)
		{
			$vipOutput = array();
			$row = isVIPEnabled ($vip, $slb['vips']);
			$li_class = $row ? 'enabled' : 'disabled';
			if ($vip['vip'] === $hl_ip && $li_class == 'enabled')
				$li_class .= ' highlight';

			$vipOutput["li_class"] = $li_class;
			$vipOutput["formated_VISP"] = formatVSIP ($vip);

			if (is_array ($row) && !empty ($row['prio']))
			{
				$prio_class 			= 'slb-prio slb-prio-' . preg_replace ('/\s.*/', '', $row['prio']);
				$vipOutput["FormatedPrioSpan"] = $tplm->generateModule('StdSpan', true, array(
						'Class' => htmlspecialchars ($prio_class, ENT_QUOTES),
						'Cont'  => htmlspecialchars($row['prio'])))->run();
			}

			$vipOutput["popupSLBConfig"] = getPopupSLBConfig ($row);
			if ($editable)
				$vipOutput["tripletForm"] = renderPopupTripletForm ($slb, NULL, $vip, $row);
			else
				$vipOutput["tripletForm"] = '';
			$vipAllOutput[] = $vipOutput;
		}
		$tripletArray["vipAllOutput"] = $vipAllOutput;

		if ($editable)
		{
			$tripletArray["editable"] = true;
			$tripletArray["formIntroPara"] = array('del', array (
				'object_id' => $slb['object_id'],
				'vs_id' => $slb['vs_id'],
				'rspool_id' => $slb['rspool_id'],
			));
		}

		$order = $nextorder[$order];
		$smod = $tplm->generatePseudoSubmodule('AllTriplets', $mod, $tripletArray);
	}

	if ($editable && getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		callHook ('renderNewTripletForm', $realm1, $realm2, $mod, 'NewTripletFormBot');

	if($parent == null)
		return $mod->run();
}

function renderSLBFormAJAX()
{
	global $pageno, $tabno;
	parse_str (assertStringArg ('form'), $orig_request);
	parse_str (ltrim (assertStringArg ('action'), '?'), $action);
	$pageno = $action['page'];
	$tabno = $action['tab'];

	$tplm = TemplateManager::getInstance();
	$mod = $tplm->generateSubmodule('Payload', 'RenderSLBFormAJAX');
	$mod->setNamespace('slb2_interface');

	$mod->setOutput('Action', $action['op']);
	$mod->setOutput('Orig_Req', $orig_request);
	$realm_list = array_diff (array ('ipvs', 'object', 'ipv4rspool'), array ($pageno));
	$allCellsOut = array();
	foreach ($realm_list as $realm)
	{
		switch ($realm)
		{
			case 'object':
				$slb_cell = spotEntity ('object', $orig_request['object_id']);
				break;
			case 'ipv4rspool':
				$slb_cell = spotEntity ('ipv4rspool', $orig_request['rspool_id']);
				break;
			case 'ipvs':
				$slb_cell = spotEntity ('ipvs', $orig_request['vs_id']);
				break;
		}
		$allCellsOut[] = array('EntityCell' => renderSLBEntityCell ($slb_cell));
	}
	$mod->setOutput('AllCells', $allCellsOut);

	$vsinfo = spotEntity ('ipvs', $orig_request['vs_id']);
	amplifyCell ($vsinfo);

	$allPortsOut = array();
	foreach ($vsinfo['ports'] as $port)
	{
		$key = $port['proto'] . '-' . $port['vport'];
		$allPortsOut[] = array('Key' => $key, 'Value' => htmlspecialchars ($key, ENT_QUOTES));
	}
	$mod->setOutput('AllPorts', $allPortsOut);

	$allVipsOut = array();
	foreach ($vsinfo['vips'] as $vip)
	{
		$key = ip_format ($vip['vip']);
		$allVipsOut[] = array('Key' => $key, 'Value' => htmlspecialchars ($key, ENT_QUOTES));
	}
	$mod->setOutput('AllVips', $allVipsOut);
}

function getTripletConfigAJAX()
{
	$tr_list = fetchTripletRows
	(
		array (
			'object_id' => assertUIntArg ('object_id'),
			'vs_id' => assertUIntArg ('vs_id'),
			'rspool_id' => assertUIntArg ('rspool_id'),
		)
	);
	$tplm = TemplateManager::getInstance();
	$tplm->generateSubmodule('Payload','TripletConfigAJAX', $tplm->createMainModule(), true, array('SLB2_Config' => htmlspecialchars (generateSLBConfig2 ($tr_list))));
}

function renderNewTripletForm ($realm1, $realm2, $parent = null, $placeholder = 'NewTripletForm')
{
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
			case 'ipvs':
				$name = 'Virtual service';
				$list = formatEntityList (listCells ('ipvs'));
				$options = array ('name' => 'vs_id', 'tabindex' => 101);
				break;
			case 'ipv4rspool':
				$name = 'RS pool';
				$list = formatEntityList (listCells ('ipv4rspool'));
				$options = array ('name' => 'rspool_id', 'tabindex' => 102);
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
		$mod = $tplm->generateModule('RenderNewTripletForm');
	else
		$mod = $tplm->generateSubmodule($placeholder, 'RenderNewTripletForm', $parent);
	
	$mod->setNamespace('slb2_interface');
	
	if (count ($realm1_data['list']) && count ($realm2_data['list']))
		$mod->addOutput('isPrintOpFormIntro', true);
		 
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
		$mod->addOutput('Message', $message);
	}
	$mod->addOutput('realm2Name', $realm2_data['name']);
	$mod->addOutput('realm2List', $realm2_data['list']);
	$mod->addOutput('realm2Opt', $realm2_data['options']);
	
	if($parent==null)
		return $mod->run();
}

function getPopupSLBConfig ($row, TemplateModule $parent = null, $placeholder = "GetPopupSLBConfig")
{
	$do_vs = (isset ($row) && isset ($row['vsconfig']) && strlen ($row['vsconfig']));
	$do_rs = (isset ($row) && isset ($row['rsconfig']) && strlen ($row['rsconfig']));
	if (!$do_vs && !$do_rs)
		return '';
	
	$tplm = TemplateManager::getInstance();
	
	if($parent==null)	
		$mod = $tplm->generateModule("GetPopupSLBConfig");
	else
		$mod = $tplm->generateSubmodule($placeholder, "GetPopupSLBConfig", $parent);

	$mod->setNamespace("slb2_interface");

	if ($do_vs)
	{
		$mod->setOutput("row_vsconfig", $row['vsconfig']);
	}
	if ($do_rs)
	{
		$mod->setOutput("row_rsconfig", $row['rsconfig']);
	}

	static $js_added = FALSE;
	if (!$js_added)
	{	
		//Add JS Code to the main module
		$tplm->createMainModule();
		$jsMod = $tplm->generateSubmodule('Payload', 'GetPopupSLBConfig_LoadJS');
		$jsMod->setNamespace("slb2_interface");
	}
	if($parent == null)
		return $mod->run();

}

function trigger_ipvs_convert ()
{
	return count (callHook ('getVSIDsByGroup', getBypassValue())) ? 'std' : '';
}

function renderIPVSConvert ($vs_id)
{
	$old_vs_list = callHook ('getVSIDsByGroup', $vs_id);

	$grouped = array();
	$used_tags = array();
	foreach ($old_vs_list as $old_vs_id)
	{
		$vsinfo = spotEntity ('ipv4vs', $old_vs_id);
		foreach ($vsinfo['etags'] as $taginfo)
			$used_tags[$taginfo['id']] = $taginfo;
		$port_key = $vsinfo['proto'] . '-' . $vsinfo['vport'];
		$grouped[$port_key][] = $vsinfo;
	}
	
	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateSubmodule('Payload', 'RenderIPVSConvert');
	
	$mod->setNamespace('ipvs',true);

	if (count ($used_tags))
	{
		$arr = array();
		foreach ($used_tags as $taginfo)
			$arr[] = array('ID'=>htmlspecialchars ($taginfo['id'], ENT_QUOTES), 'Tags'=>serializeTags (array ($taginfo)));
		$mod->addOutput('UsedTags', $arr);
	}

	$arr = array();
	foreach ($grouped as $port_key => $list)
		$arr[] = array('Key'=>$port_key);
	$mod->addOutput('PortKeys', $arr);
	foreach ($grouped as $port_key => $list)
	{
		$smod = $tplm->generateSubmodule('Grouped', 'RenderIPVSConvertGroupedKey', $mod);
		$arr = array();
		foreach ($list as $vsinfo)
		{
			$arr[] = array('ID'=>htmlspecialchars ($vsinfo['id'], ENT_QUOTES), 'SLBEntityCell'=>renderSLBEntityCell ($vsinfo));
		}
		$smod->addOutput('List', $arr);
	}
}

function renderNewVSGForm ()
{

	$tplm = TemplateManager::getInstance();
	
	$mod = $tplm->generateSubmodule('Payload','RenderNewVSGForm');
	$mod->setNamespace("ipv4slb");
	printTagsPicker (null, $mod, 'TagsPicker');
}
