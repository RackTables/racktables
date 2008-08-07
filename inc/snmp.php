<?php

function doSNMPmining ($object_id, $community)
{
	// FIXME: switch to message log version 2
	$log = array();
// IDs: http://cisco.com/en/US/products/sw/cscowork/ps2064/products_device_support_table09186a0080803bb4.html
// 2950: http://www.cisco.com/en/US/products/hw/switches/ps628/prod_models_home.html
// 2960: http://www.cisco.com/en/US/products/ps6406/prod_models_comparison.html
// 2970: http://cisco.com/en/US/products/hw/switches/ps5206/products_qanda_item09186a00801b1750.shtml
// 3500XL: http://cisco.com/en/US/products/hw/switches/ps637/products_eol_models.html
// 3560: http://cisco.com/en/US/products/hw/switches/ps5528/products_data_sheet09186a00801f3d7f.html
// 3750: http://cisco.com/en/US/products/hw/switches/ps5023/products_data_sheet09186a008016136f.html

	// Cisco sysObjectID to model (not product number, i.e. image code is missing) decoder
	$ciscomodel = array
	(
		283 => 'WS-C6509-E (9-slot system)',
#		694 => 'WS-C2960-24TC-L (24 Ethernet 10/100 ports and 2 dual-purpose uplinks)',
#		695 => 'WS-C2960-48TC-L (48 Ethernet 10/100 ports and 2 dual-purpose uplinks)',
		696 => 'WS-C2960G-24TC-L (20 Ethernet 10/100/1000 ports and 4 dual-purpose uplinks)',
		697 => 'WS-C2960G-48TC-L (44 Ethernet 10/100/1000 ports and 4 dual-purpose uplinks)',
		716 => 'WS-C2960-24TT-L (24 Ethernet 10/100 ports and 2 10/100/1000 uplinks)',
		717 => 'WS-C2960-48TT-L (48 Ethernet 10/100 ports and 2 10/100/1000 uplinks)',
		527 => 'WS-C2970G-24T (24 Ethernet 10/100/1000 ports)',
		561 => 'WS-C2970G-24TS (24 Ethernet 10/100/1000 ports and 4 10/100/1000 SFP uplinks)',
		633 => 'WS-C3560-24TS (24 Ethernet 10/100 ports and 2 10/100/1000 SFP uplinks)',
		634 => 'WS-C3560-48TS (48 Ethernet 10/100 ports and 4 10/100/1000 SFP uplinks)',
		563 => 'WS-C3560-24PS (24 Ethernet 10/100 POE ports and 2 10/100/1000 SFP uplinks)',
		564 => 'WS-C3560-48PS (48 Ethernet 10/100 POE ports and 4 10/100/1000 SFP uplinks)',
		516 => 'WS-C3750-48PS (48 Ethernet 10/100 POE ports and 4 10/100/1000 SFP uplinks)',
		614 => 'WS-C3560G-24PS (24 Ethernet 10/100/1000 POE ports and 4 10/100/1000 SFP uplinks)',
		615 => 'WS-C3560G-24TS (24 Ethernet 10/100/1000 ports and 4 10/100/1000 SFP uplinks)',
		616 => 'WS-C3560G-48PS (48 Ethernet 10/100/1000 POE ports and 4 10/100/1000 SFP uplinks)',
		617 => 'WS-C3560G-48TS (48 Ethernet 10/100/1000 ports and 4 10/100/1000 SFP uplinks)',
		58 => 'WS-C4503 (3-slot system)',
		503 => '4503 (3-slot system)',
		59 => 'WS-C4506 (6-slot system)',
		502 => '4506 (6-slot system)',
		626 => 'WS-C4948 (48 Ethernet 10/100/1000 ports and 4 10/100/1000 SFP uplinks)',
		659 => 'WS-C4948-10GE (48 Ethernet 10/100/1000 ports and 2 10Gb X2 uplinks)',
		428 => 'WS-C2950G-24 (24 Ethernet 10/100 ports and 2 1000 GBIC uplinks)',
		429 => 'WS-C2950G-48 (48 Ethernet 10/100 ports and 2 1000 GBIC uplinks)',
		559 => 'WS-C2950T-48 (48 Ethernet 10/100 ports and 2 10/100/1000 uplinks)',
	);
	// Cisco sysObjectID to Dictionary dict_key map
	$hwtype = array
	(
		283 => 148,
		696 => 167,
		697 => 166,
		527 => 210,
		561 => 115,
		633 => 169,
		634 => 170,
		563 => 171,
		564 => 172,
		614 => 175,
		615 => 173,
		616 => 176,
		617 => 174,
		58 => 145,
		503 => 145,
		59 => 156,
		502 => 156,
		626 => 147,
		659 => 377,
		428 => 389,
		429 => 390,
		559 => 387,
		516 => 179,
		716 => 164,
		717 => 162,
	);

	$objectInfo = getObjectInfo ($object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	$sysName = substr (snmpget ($endpoints[0], $community, 'sysName.0'), strlen ('STRING: '));
	$sysDescr = snmpget ($endpoints[0], $community, 'sysDescr.0');
	$sysChassi = snmpget ($endpoints[0], $community, '1.3.6.1.4.1.9.3.6.3.0');
	// Strip the object type, it's always string here.
	$sysDescr = substr ($sysDescr, strlen ('STRING: '));
	$IOSversion = ereg_replace ('^.*, Version ([^ ]+), .*$', '\\1', $sysDescr);
	$sysChassi = str_replace ('"', '', substr ($sysChassi, strlen ('STRING: ')));
	if (strpos ($sysDescr, 'Cisco IOS Software') === 0 or strpos ($sysDescr, 'Cisco Internetwork Operating System Software') === 0)
		$log[] = array ('code' => 'success', 'message' => 'Seems to be a Cisco box');
	else
	{
		$log[] = array ('code' => 'error', 'message' => 'No idea how to handle ' . $sysDescr);
		return $log;
	}

	// It's a Cisco box. Go on.
	$attrs = getAttrValues ($object_id);
	// Only fill in attribute values, if they are not set.
	// FIXME: this is hardcoded

	if (empty ($attrs[3]['value']) && !empty ($sysName)) // FQDN
	{
		$error = commitUpdateAttrValue ($object_id, 3, $sysName);
		if ($error == TRUE)
			$log[] = array ('code' => 'success', 'message' => 'FQDN set to ' . $sysName);
		else
			$log[] = array ('code' => 'error', 'message' => 'Failed settig FQDN: ' . $error);
	}

	if (empty ($attrs[5]['value']) and strlen ($IOSversion) > 0) // SW version
	{
		$error = commitUpdateAttrValue ($object_id, 5, $IOSversion);
		if ($error == TRUE)
			$log[] = array ('code' => 'success', 'message' => 'SW version set to ' . $IOSversion);
		else
			$log[] = array ('code' => 'error', 'message' => 'Failed settig SW version: ' . $error);
	}

	if (empty ($attrs[1]['value']) and strlen ($sysChassi) > 0) // OEM Serial #1
	{
		$error = commitUpdateAttrValue ($object_id, 1, $sysChassi);
		if ($error == TRUE)
			$log[] = array ('code' => 'success', 'message' => 'OEM S/N 1 set to ' . $sysChassi);
		else
			$log[] = array ('code' => 'error', 'message' => 'Failed settig OEM S/N 1: ' . $error);
	}

	if (empty ($attrs[4]['value'])) // switch OS type
	{
		switch (substr ($IOSversion, 0, 4))
		{
			case '12.2':
				$error = commitUpdateAttrValue ($object_id, 4, 252);
				break;
			case '12.1':
				$error = commitUpdateAttrValue ($object_id, 4, 251);
				break;
			case '12.0':
				$error = commitUpdateAttrValue ($object_id, 4, 244);
				break;
			default:
				$log[] = array ('code' => 'error', 'message' => "Unknown IOS version ${IOSversion}");
				$error = TRUE;
				break;
		}
		if ($error == TRUE)
			$log[] = array ('code' => 'success', 'message' => 'Switch OS type set to Cisco IOS ' . substr ($IOSversion, 0, 4));
		else
			$log[] = array ('code' => 'error', 'message' => 'Failed setting Switch OS type');
	}

	$sysObjectID = snmpget ($endpoints[0], $community, 'sysObjectID.0');
	// Transform OID
	$sysObjectID = substr ($sysObjectID, strlen ('OID: SNMPv2-SMI::enterprises.9.1.'));
	if (!isset ($ciscomodel[$sysObjectID]))
	{
		$log[] = array ('code' => 'error', 'message' => 'Could not guess exact HW model!');
		return $log;
	}
	$log[] = array ('code' => 'success', 'message' => 'HW is ' . $ciscomodel[$sysObjectID]);
	if (empty ($attrs[2]['value']) and isset ($hwtype[$sysObjectID])) // switch HW type
	{
		$error = commitUpdateAttrValue ($object_id, 2, $hwtype[$sysObjectID]);
		if ($error == TRUE)
			$log[] = array ('code' => 'success', 'message' => 'HW type updated Ok');
		else
			$log[] = array ('code' => 'error', 'message' => 'Failed settig HW type: ' . $error);
	}
	// Now fetch ifType, ifDescr and ifPhysAddr and let model-specific code sort the data out.
	$ifType = snmpwalkoid ($endpoints[0], $community, 'ifType');
	$ifDescr = snmpwalkoid ($endpoints[0], $community, 'ifdescr');
	$ifPhysAddress = snmpwalkoid ($endpoints[0], $community, 'ifPhysAddress');
	// Combine 3 tables into 1...
	$ifList1 = array();
	foreach ($ifType as $key => $val)
	{
		list ($dummy, $ifIndex) = explode ('.', $key);
		list ($dummy, $type) = explode (' ', $val);
		$ifList1[$ifIndex]['type'] = $type;
	}
	foreach ($ifDescr as $key => $val)
	{
		list ($dummy, $ifIndex) = explode ('.', $key);
		list ($dummy, $descr) = explode (' ', $val);
		$ifList1[$ifIndex]['descr'] = trim ($descr, '"');
	}
	foreach ($ifPhysAddress as $key => $val)
	{
		list ($dummy, $ifIndex) = explode ('.', $key);
		list ($dummy, $addr) = explode (':', $val);
		$addr = str_replace (' ', '', $addr);
		$ifList1[$ifIndex]['phyad'] = $addr;
	}
	// ...and then reverse it inside out to make description the key.
	$ifList2 = array();
	foreach ($ifList1 as $ifIndex => $data)
	{
		$ifList2[$data['descr']]['type'] = $data['type'];
		$ifList2[$data['descr']]['phyad'] = $data['phyad'];
		$ifList2[$data['descr']]['idx'] = $ifIndex;
	}
	$newports = 0;
	// Now we can directly pick necessary ports from the table accordingly
	// to our known hardware model.
	switch ($sysObjectID)
	{
	// FIXME: chassis edge switches often share a common naming scheme, so
	// the sequences below have to be generalized. Let's have some duplicated
	// code for the time being, as this is the first implementation ever.
		case '697': // WS-C2960G-48TC-L
			// 44 copper ports: 1X, 2X, 3X...
			// 4 combo ports: 45, 46, 47, 48. Don't list SFP connectors atm, as it's not
			// clear how to fit them into current Ports table structure.
			for ($i = 1; $i <= 48; $i++)
			{
				$label = ($i >= 45) ? "${i}" : "${i}X";
				$error = commitAddPort ($object_id, 'gi0/' . $i, 24, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			break;
		case '696': // WS-C2960G-24TC-L
			// Quite similar to the above.
			for ($i = 1; $i <= 24; $i++)
			{
				$label = ($i >= 21) ? "${i}" : "${i}X";
				$error = commitAddPort ($object_id, 'gi0/' . $i, 24, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			break;
		case '716': // WS-C2960-24TT-L
		case '563': // WS-C3560-24PS
		case '633': // WS-C3560-24TS
		case '428': // WS-C2950G-24
			for ($i = 1; $i <= 24; $i++)
			{
				$label = "${i}X";
				$error = commitAddPort ($object_id, 'fa0/' . $i, 19, $label, $ifList2["FastEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			for ($i = 1; $i <= 2; $i++)
			{
				$label = "${i}";
				$error = commitAddPort ($object_id, 'gi0/' . $i, 24, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			break;
		case '717': // WS-C2960-48TT-L
		case '429': // WS-C2950G-48
		case '559': // WS-C2950T-48
			for ($i = 1; $i <= 48; $i++)
			{
				$label = "${i}X";
				$error = commitAddPort ($object_id, 'fa0/' . $i, 19, $label, $ifList2["FastEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			for ($i = 1; $i <= 2; $i++)
			{
				$label = "${i}";
				$error = commitAddPort ($object_id, 'gi0/' . $i, 24, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			break;
		case '516': // WS-C3750-48PS
			for ($i = 1; $i <= 48; $i++)
			{
				$label = "${i}X";
				$error = commitAddPort ($object_id, 'fa1/0/' . $i, 19, $label, $ifList2["FastEthernet1/0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			for ($i = 1; $i <= 4; $i++)
			{
				$label = "${i}";
				$error = commitAddPort ($object_id, 'gi1/0/' . $i, 24, $label, $ifList2["GigabitEthernet1/0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			break;
		case '564': // WS-C3560-48PS
		case '634': // WS-C3560-48TS
			for ($i = 1; $i <= 48; $i++)
			{
				$label = "${i}X";
				$error = commitAddPort ($object_id, 'fa0/' . $i, 19, $label, $ifList2["FastEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			for ($i = 1; $i <= 4; $i++)
			{
				$label = "${i}";
				$error = commitAddPort ($object_id, 'gi0/' . $i, 24, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			break;
		case '614': // WS-C3560G-24PS
		case '615': // WS-C3560G-24TS
		case '527': // WS-C2970G-24T
		case '561': // WS-C2970G-24TS
			for ($i = 1; $i <= 24; $i++)
			{
				$label = "${i}X";
				$error = commitAddPort ($object_id, 'gi0/' . $i, 24, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			break;
		case '616': // WS-C3560G-48PS
		case '617': // WS-C3560G-48TS
			for ($i = 1; $i <= 48; $i++)
			{
				$label = "${i}X";
				$error = commitAddPort ($object_id, 'gi0/' . $i, 24, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			break;
		case '626': // WS-C4948
		case '659': // WS-C4948-10GE
			for ($i = 1; $i <= 48; $i++)
			{
				$label = "${i}X";
				$error = commitAddPort ($object_id, 'gi1/' . $i, 24, $label, $ifList2["GigabitEthernet1/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			break;
	// For modular devices we don't iterate over all possible port names,
	// but use the first list to pick everything that looks legitimate
	// for this hardware. It would be correct to fetch the list of modules
	// installed to generate lists of ports, but who is going to implement
	// this?
		case '503': // 4503
		case '58': // WS-C4503
		case '502': // 4506
		case '59': // WS-C4506
		case '283': // WS-C6509-E
			foreach ($ifList1 as $port)
			{
				if ($port['type'] != 'ethernet-csmacd(6)')
					continue;
				// Copper Fa/Gi harvesting is relatively simple, while 10Gig ports can
				// have random samples of transciever units.
				if (strpos ($port['descr'], 'FastEthernet') === 0) // Fa
				{
					$prefix = 'fa';
					$ptype = 19; // RJ-45/100Base-TX
					list ($slotno, $portno) = explode ('/', substr ($port['descr'], strlen ('FastEthernet')));
				}
				elseif (strpos ($port['descr'], 'GigabitEthernet') === 0) // Gi
				{
					$prefix = 'gi';
					$ptype = 24; // RJ-45/1000Base-T
					list ($slotno, $portno) = explode ('/', substr ($port['descr'], strlen ('GigabitEthernet')));
				}
				else continue;
				$label = "slot ${slotno} port ${portno}";
				$pname = "${prefix}${slotno}/${portno}";
				$error = commitAddPort ($object_id, $pname, $ptype, $label, $port['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $pname . ': ' . $error);
			}
			break;
		default:
			showError ("Unexpected sysObjectID '${sysObjectID}'", __FUNCTION__);
	}
	$error = commitAddPort ($object_id, 'con0', 29, 'console', '');
	if ($error == '')
		$newports++;
	else
		$log[] = array ('code' => 'error', 'message' => 'Failed to add console port : ' . $error);
	if ($newports > 0)
		$log[] = array ('code' => 'success', 'message' => "Added ${newports} new ports");
	return $log;
}

?>
