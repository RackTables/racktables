<?php

$iftable_processors = array();
$iftable_processors['catalyst-any-100TX'] = array
(
	'pattern' => '^FastEthernet(0/[[:digit:]]+)$',
	'replacement' => 'fa\\1',
	'dict_key' => 19,
);

$iftable_processors['catalyst-any-1000T'] = array
(
	'pattern' => '^GigabitEthernet(0/[[:digit:]]+)$',
	'replacement' => 'gi\\1',
	'dict_key' => 24,
);

$iftable_processors['catalyst-any-1000SFP'] = array
(
	'pattern' => '^GigabitEthernet(0/[[:digit:]]+)$',
	'replacement' => 'gi\\1',
	'dict_key' => 440,
);

$iftable_processors['catalyst-25-to-28-1000SFP'] = array
(
	'pattern' => '^GigabitEthernet(0/(25|26|27|28))$',
	'replacement' => 'gi\\1',
	'dict_key' => 440,
);

$iftable_processors['catalyst-49-to-52-1000SFP'] = array
(
	'pattern' => '^GigabitEthernet(0/(49|50|51|52))$',
	'replacement' => 'gi\\1',
	'dict_key' => 440,
);

$iftable_processors['catalyst-13-to-16-1000SFP'] = array
(
	'pattern' => '^GigabitEthernet(0/(13|14|15|16))$',
	'replacement' => 'gi\\1',
	'dict_key' => 440,
);

$iftable_processors['catalyst-21-to-24-1000SFP'] = array
(
	'pattern' => '^GigabitEthernet(0/(21|22|23|24))$',
	'replacement' => 'gi\\1',
	'dict_key' => 440,
);

$iftable_processors['nexus-any-10000SFP+'] = array
(
	'pattern' => '^Ethernet([[:digit:]]/[[:digit:]]+)$',
	'replacement' => 'e\\1',
	'dict_key' => 440,
);

$iftable_processors['procurve-any-100TX'] = array
(
	'pattern' => '^([[:digit:]]+)$',
	'replacement' => '\\1',
	'dict_key' => 19,
);

$iftable_processors['procurve-25-to-26-1000T'] = array
(
	'pattern' => '^(25|26)$',
	'replacement' => '\\1',
	'dict_key' => 24,
);

$iftable_processors['procurve-49-to-50-1000T'] = array
(
	'pattern' => '^(49|50)$',
	'replacement' => '\\1',
	'dict_key' => 24,
);

$iftable_processors['netgear-any-1000T'] = array
(
	'pattern' => '^Unit: 1 Slot: 0 Port: ([[:digit:]]+) Gigabit - Level$',
	'replacement' => '\\1',
	'dict_key' => 24,
);

$known_switches = array // key is system OID w/o "enterprises" prefix
(
	'9.1.324' => array
	(
		'dict_key' => 380,
		'text' => 'WS-C2950-24: 24 RJ-45/10-100TX',
		'processors' => array ('catalyst-any-100TX'),
	),
	'9.1.325' => array
	(
		'dict_key' => 382,
		'text' => 'WS-C2950C-24: 24 RJ-45/10-100TX + 2 MT-RJ/100FX fiber',
		'processors' => array ('catalyst-25-to-26-100FX/MT-RJ', 'catalyst-any-Nx100TX'),
	),
	'9.1.696' => array
	(
		'dict_key' => 167,
		'text' => 'WS-C2960G-24TC-L: 20 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('catalyst-any-1000T'),
	),
	'9.1.697' => array
	(
		'dict_key' => 166,
		'text' => 'WS-C2960G-48TC-L: 44 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('catalyst-any-1000T'),
	),
	'9.1.716' => array
	(
		'dict_key' => 164,
		'text' => 'WS-C2960-24TT-L: 24 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-any-100TX', 'catalyst-any-1000T'),
	),
	'9.1.717' => array
	(
		'dict_key' => 162,
		'text' => 'WS-C2960-48TT-L: 48 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-any-100TX', 'catalyst-any-1000T'),
	),
	'9.1.527' => array
	(
		'dict_key' => 210,
		'text' => 'WS-C2970G-24T: 24 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-any-1000T'),
	),
	'9.1.561' => array
	(
		'dict_key' => 115,
		'text' => 'WS-C2970G-24TS: 24 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-25-to-28-1000SFP', 'catalyst-any-1000T'),
	),
	'9.1.633' => array
	(
		'dict_key' => 169,
		'text' => 'WS-C3560-24TS: 24 RJ-45/10-100TX + 2 SFP/1000',
		'processors' => array ('catalyst-any-1000SFP', 'catalyst-any-100TX'),
	),
	'9.1.634' => array
	(
		'dict_key' => 170,
		'text' => 'WS-C3560-48TS: 48 RJ-45/10-100TX + 4 SFP/1000',
		'processors' => array ('catalyst-any-1000SFP', 'catalyst-any-100TX'),
	),
	'9.1.563' => array
	(
		'dict_key' => 171,
		'text' => 'WS-C3560-24PS: 24 RJ-45/10-100TX + 2 SFP/1000',
		'processors' => array ('catalyst-any-1000SFP', 'catalyst-any-100TX'),
	),
	'9.1.564' => array
	(
		'dict_key' => 172,
		'text' => 'WS-C3560-48PS: 48 RJ-45/10-100TX + 4 SFP/1000',
		'processors' => array ('catalyst-any-1000SFP', 'catalyst-any-100TX'),
	),
	'9.1.516' => array
	(
		'dict_key' => 179,
		'text' => 'WS-C3750-xxPS: 24 or 48 RJ-45/10-100TX + 4 SFP/1000',
		'processors' => array ('catalyst-any-1000SFP', 'catalyst-any-100TX'),
	),
	'9.1.614' => array
	(
		'dict_key' => 175,
		'text' => 'WS-C3560G-24PS: 24 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-25-to-28-1000SFP', 'catalyst-any-1000T'),
	),
	'9.1.615' => array
	(
		'dict_key' => 173,
		'text' => 'WS-C3560G-24TS: 24 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-25-to-28-1000SFP', 'catalyst-any-1000T'),
	),
	'9.1.616' => array
	(
		'dict_key' => 176,
		'text' => 'WS-C3560G-48PS: 48 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-49-to-52-1000SFP', 'catalyst-any-1000T'),
	),
	'9.1.617' => array
	(
		'dict_key' => 174,
		'text' => 'WS-C3560G-48TS: 48 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-49-to-52-1000SFP', 'catalyst-any-1000T'),
	),
	'9.1.624' => array
	(
		'dict_key' => 143,
		'text' => 'WS-C3750G-24TS: 24 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-25-to-28-1000SFP', 'catalyst-any-1000T'),
	),
	'9.1.626' => array
	(
		'dict_key' => 147,
		'text' => 'WS-C4948: 48 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-49-to-52-1000SFP', 'catalyst-any-1000T'),
	),
	'9.1.659' => array
	(
		'dict_key' => 377,
		'text' => 'WS-C4948-10GE: 48 RJ-45/10-100-1000T(X) + 2 X2/10000',
		'processors' => array ('catalyst-any-10000X2', 'catalyst-any-1000T'),
	),
	'9.1.428' => array
	(
		'dict_key' => 389,
		'text' => 'WS-C2950G-24: 24 RJ-45/10-100TX + 2 GBIC/1000',
		'processors' => array ('catalyst-any-1000GBIC', 'catalyst-any-100TX'),
	),
	'9.1.429' => array
	(
		'dict_key' => 390,
		'text' => 'WS-C2950G-48: 48 RJ-45/10-100TX + 2 GBIC/1000',
		'processors' => array ('catalyst-any-1000GBIC', 'catalyst-any-100TX'),
	),
	'9.1.559' => array
	(
		'dict_key' => 387,
		'text' => 'WS-C2950T-48: 48 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-any-1000T', 'catalyst-any-100TX'),
	),
	'9.1.749' => array
	(
		'dict_key' => 989,
		'text' => 'WS-CBS3030-DEL: 10 internal/10-100-1000T(X) + 2 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-11-to-12-1000T', 'catalyst-13-to-16-1000SFP', 'catalyst-any-1000Tbp'),
	),
	'9.1.920' => array
	(
		'dict_key' => 795,
		'text' => 'WS-CBS3032-DEL: 16 internal/10-100-1000T(X) + 4 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-17-to-20-1000T', 'catalyst-21-to-24-1000SFP', 'catalyst-any-1000Tbp'),
	),
	'9.12.3.1.3.719' => array
	(
		'dict_key' => 960,
		'text' => 'N5K-C5020: 40 SFP+/10000',
		'processors' => array ('nexus-any-10000SFP+'),
	),
	'11.2.3.7.11.36' => array
	(
		'dict_key' => 865,
		'text' => 'J8164A: 24 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('procurve-25-to-26-1000T', 'procurve-any-100TX'),
	),
	'11.2.3.7.11.35' => array
	(
		'dict_key' => 867,
		'text' => 'J8165A: 48 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('procurve-49-to-50-1000T', 'procurve-any-100TX'),
	),
	'4526.100.2.2' => array
	(
		'dict_key' => 562,
		'text' => 'GSM7224: 24 RJ-45/10-100-1000T(X)',
		'processors' => array ('netgear-any-1000T'),
	),
);

function doSNMPmining ($object_id, $community)
{
	return; // overhaul in progress
	// FIXME: switch to message log version 2
	$log = array();
// IDs: http://cisco.com/en/US/products/sw/cscowork/ps2064/products_device_support_table09186a0080803bb4.html
// 2950: http://www.cisco.com/en/US/products/hw/switches/ps628/prod_models_home.html
// 2960: http://www.cisco.com/en/US/products/ps6406/prod_models_comparison.html
// 2970: http://cisco.com/en/US/products/hw/switches/ps5206/products_qanda_item09186a00801b1750.shtml
// 3030: http://www.cisco.com/en/US/products/ps6764/index.html 
// 3500XL: http://cisco.com/en/US/products/hw/switches/ps637/products_eol_models.html
// 3560: http://cisco.com/en/US/products/hw/switches/ps5528/products_data_sheet09186a00801f3d7f.html
// 3750: http://cisco.com/en/US/products/hw/switches/ps5023/products_data_sheet09186a008016136f.html

	// Cisco sysObjectID to model (not product number, i.e. image code is missing) decoder
	$verb_model = array
	(
		'9.1.278' => 'WS-C3548-XL (48 Ethernet 10/100 ports and 2 10/100/1000 uplinks)',
		'9.1.283' => 'WS-C6509-E (9-slot system)',
		'9.1.324' => 'WS-C2950-24 (24 Ethernet 10/100 ports)',
		'9.1.325' => 'WS-C2950C-24 (24 Ethernet 10/100 ports and 2 100FX uplinks)',
#		'9.1.694' => 'WS-C2960-24TC-L (24 Ethernet 10/100 ports and 2 dual-purpose uplinks)',
#		'9.1.695' => 'WS-C2960-48TC-L (48 Ethernet 10/100 ports and 2 dual-purpose uplinks)',
		'9.1.696' => 'WS-C2960G-24TC-L (20 Ethernet 10/100/1000 ports and 4 dual-purpose uplinks)',
		'9.1.697' => 'WS-C2960G-48TC-L (44 Ethernet 10/100/1000 ports and 4 dual-purpose uplinks)',
		'9.1.716' => 'WS-C2960-24TT-L (24 Ethernet 10/100 ports and 2 10/100/1000 uplinks)',
		'9.1.717' => 'WS-C2960-48TT-L (48 Ethernet 10/100 ports and 2 10/100/1000 uplinks)',
		'9.1.527' => 'WS-C2970G-24T (24 Ethernet 10/100/1000 ports)',
		'9.1.561' => 'WS-C2970G-24TS (24 Ethernet 10/100/1000 ports and 4 10/100/1000 SFP uplinks)',
		'9.1.633' => 'WS-C3560-24TS (24 Ethernet 10/100 ports and 2 10/100/1000 SFP uplinks)',
		'9.1.634' => 'WS-C3560-48TS (48 Ethernet 10/100 ports and 4 10/100/1000 SFP uplinks)',
		'9.1.563' => 'WS-C3560-24PS (24 Ethernet 10/100 POE ports and 2 10/100/1000 SFP uplinks)',
		'9.1.564' => 'WS-C3560-48PS (48 Ethernet 10/100 POE ports and 4 10/100/1000 SFP uplinks)',
		'9.1.516' => 'WS-C3750-XXPS (24 or 48 Ethernet 10/100 POE ports and 4 10/100/1000 SFP uplinks)',
		'9.1.614' => 'WS-C3560G-24PS (24 Ethernet 10/100/1000 POE ports and 4 10/100/1000 SFP uplinks)',
		'9.1.615' => 'WS-C3560G-24TS (24 Ethernet 10/100/1000 ports and 4 10/100/1000 SFP uplinks)',
		'9.1.616' => 'WS-C3560G-48PS (48 Ethernet 10/100/1000 POE ports and 4 10/100/1000 SFP uplinks)',
		'9.1.617' => 'WS-C3560G-48TS (48 Ethernet 10/100/1000 ports and 4 10/100/1000 SFP uplinks)',
		'9.1.624' => 'WS-C3750G-24TS (24 Ethernet 10/100/1000 ports and 4 10/100/1000 SFP uplinks)',
		'9.1.58'  => 'WS-C4503 (3-slot system)',
		'9.1.503' => '4503 (3-slot system)',
		'9.1.59'  => 'WS-C4506 (6-slot system)',
		'9.1.502' => '4506 (6-slot system)',
		'9.1.626' => 'WS-C4948 (48 Ethernet 10/100/1000 ports and 4 10/100/1000 SFP uplinks)',
		'9.1.659' => 'WS-C4948-10GE (48 Ethernet 10/100/1000 ports and 2 10Gb X2 uplinks)',
		'9.1.428' => 'WS-C2950G-24 (24 Ethernet 10/100 ports and 2 1000 GBIC uplinks)',
		'9.1.429' => 'WS-C2950G-48 (48 Ethernet 10/100 ports and 2 1000 GBIC uplinks)',
		'9.1.559' => 'WS-C2950T-48 (48 Ethernet 10/100 ports and 2 10/100/1000 uplinks)',
		'9.1.749' => 'WS-CBS3030-DEL (12 Ethernet 10/100/1000 and 4 10/100/1000 SFP uplinks)',
		'9.1.920' => 'WS-CBS3032-DEL-F (16 Ethernet 10/100/1000 and up to 8 10/100/1000 uplinks)',
		'9.12.3.1.3.719' => 'N5K-C5020 (40-ports system)',
		'11.2.3.7.11.36' => 'HP J8164A (24 Ethernet 10/100 ports and 2 100/1000 uplinks)',
		'11.2.3.7.11.35' => 'HP J8165A (48  Ethernet 10/100 ports and 2 100/1000 uplinks)',
		'4526.100.2.2' => 'NETGEAR GSM7224 (24 Ethernet 10/100/1000 ports)',
	);
	// Cisco sysObjectID to Dictionary dict_key map
	$hwtype = array
	(
		'9.1.278' => 395,
		'9.1.283' => 148,
		'9.1.324' => 380,
		'9.1.325' => 382,
		'9.1.696' => 167,
		'9.1.697' => 166,
		'9.1.527' => 210,
		'9.1.561' => 115,
		'9.1.633' => 169,
		'9.1.634' => 170,
		'9.1.563' => 171,
		'9.1.564' => 172,
		'9.1.614' => 175,
		'9.1.615' => 173,
		'9.1.616' => 176,
		'9.1.617' => 174,
		'9.1.624' => 143,
		'9.1.58' => 145,
		'9.1.503' => 145,
		'9.1.59' => 156,
		'9.1.502' => 156,
		'9.1.626' => 147,
		'9.1.659' => 377,
		'9.1.428' => 389,
		'9.1.429' => 390,
		'9.1.559' => 387,
		'9.1.516' => 179,
		'9.1.716' => 164,
		'9.1.717' => 162,
		'9.1.920' => 795,
		'9.12.3.1.3.719' => 960,
		'9.1.749' => 989,
		'11.2.3.7.11.36' => 865,
		'11.2.3.7.11.35' => 867,
		'4526.100.2.2' => 562,
	);
	// Cisco portType to Dictionary dict_key map
	$porttype = array
	(
		18 => 19,  // 10/100BaseT      => RJ-45/100Base-T
		28 => 25,  // 1000BaseSX       => SC/1000Base-SX
		31 => 440, // No Transceiver   => unknown
		61 => 24,  // 10/100/1000BaseT => RJ-45/1000Base-T
	);

	// TODO: to make all processing purely OID-based, it may help to call:
	// snmp_set_oid_output_format (SNMP_OID_OUTPUT_NUMERIC) (in PHP 5.2+)
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	$sysName = @snmpget ($endpoints[0], $community, 'sysName.0');
	if ($sysName === FALSE)
	{
		$log[] = array ('code' => 'error', 'message' => 'SNMP error connecting to "' . $endpoints[0] . '"');
		return $log;
	}
	$sysName = substr ($sysName, strlen ('STRING: '));
	$sysDescr = snmpget ($endpoints[0], $community, 'sysDescr.0');
	// Don't generate error for agents other than IOS.
	$sysChassi = @snmpget ($endpoints[0], $community, '1.3.6.1.4.1.9.3.6.3.0');
	if ($sysChassi === FALSE or $sysChassi == NULL)
		$sysChassi = '';
	else
		$sysChassi = str_replace ('"', '', substr ($sysChassi, strlen ('STRING: ')));
	// Strip the object type, it's always string here.
	$sysDescr = substr ($sysDescr, strlen ('STRING: '));
	$sysDescr = str_replace(array("\n", "\r"), "", $sysDescr);  // Make it one line
	if (FALSE !== ereg ('^(Cisco )?(Internetwork Operating System Software )?IOS .+$', $sysDescr))
	{
		$swfamily = 'IOS';
		$swversion = ereg_replace ('^.*, Version ([^ ]+), .*$', '\\1', $sysDescr);
		$swrelease = ereg_replace ('^([[:digit:]]+\.[[:digit:]]+)[^[:digit:]].*', '\\1', $swversion);
	}
	elseif (FALSE !== ereg ('^Cisco NX-OS.+$', $sysDescr))
	{
		$swfamily = 'NX-OS';
		$swversion = ereg_replace ('^.*, Version ([^ ]+), .*$', '\\1', $sysDescr);
		$swrelease = ereg_replace ('^([[:digit:]]+\.[[:digit:]]+)[^[:digit:]].*', '\\1', $swversion);
	}
	elseif
	(
		FALSE !== ereg ('^HP [[:alnum:]]+ ProCurve Switch', $sysDescr) or
		FALSE !== ereg ('^ProCurve [[:alnum:]]+ Switch', $sysDescr)
	)
	{
		$swfamily = 'HP';
		$swversion = ereg_replace ('^.* revision ([^ ]+), .*$', '\\1', $sysDescr);
		$swrelease = 'HP';
	}
	elseif (FALSE !== ereg ('^GSM[[:alnum:]]+ L2 Managed Gigabit Switch$', $sysDescr))
	{
		$swfamily = 'NETGEAR';
		$swrelease = 'NETGEAR';
	}
	else
		$log[] = array ('code' => 'error', 'message' => 'No idea how to handle ' . $sysDescr);
	$attrs = getAttrValues ($object_id);
	// Only fill in attribute values, if they are not set.
	// FIXME: this is hardcoded

	if (!strlen ($attrs[3]['value']) && strlen ($sysName)) // FQDN
	{
		$error = commitUpdateAttrValue ($object_id, 3, $sysName);
		if ($error == TRUE)
			$log[] = array ('code' => 'success', 'message' => 'FQDN set to ' . $sysName);
		else
			$log[] = array ('code' => 'error', 'message' => 'Failed settig FQDN: ' . $error);
	}

	if (!strlen ($attrs[5]['value']) and strlen ($swversion) > 0) // SW version
	{
		$error = commitUpdateAttrValue ($object_id, 5, $swversion);
		if ($error == TRUE)
			$log[] = array ('code' => 'success', 'message' => 'SW version set to ' . $swversion);
		else
			$log[] = array ('code' => 'error', 'message' => 'Failed settig SW version: ' . $error);
	}

	if (!strlen ($attrs[1]['value']) and strlen ($sysChassi) > 0) // OEM Serial #1
	{
		$error = commitUpdateAttrValue ($object_id, 1, $sysChassi);
		if ($error == TRUE)
			$log[] = array ('code' => 'success', 'message' => 'OEM S/N 1 set to ' . $sysChassi);
		else
			$log[] = array ('code' => 'error', 'message' => 'Failed settig OEM S/N 1: ' . $error);
	}

	if (!strlen ($attrs[4]['value']) and ($swfamily == 'IOS' or $swfamily == 'NX-OS')) // switch OS type
	{
		switch ($swfamily . '-' . $swrelease)
		{
			case 'IOS-12.2':
				$error = commitUpdateAttrValue ($object_id, 4, 252);
				break;
			case 'IOS-12.1':
				$error = commitUpdateAttrValue ($object_id, 4, 251);
				break;
			case 'IOS-12.0':
				$error = commitUpdateAttrValue ($object_id, 4, 244);
				break;
			case 'NX-OS-4.0':
				$error = commitUpdateAttrValue ($object_id, 4, 963);
				break;
			case 'NX-OS-4.1':
				$error = commitUpdateAttrValue ($object_id, 4, 964);
				break;
			default:
				$log[] = array ('code' => 'error', 'message' => "Unknown SW version ${swversion}");
				// The logic for 'error' is backwards...
				// This should be set 'FALSE' if there is an error
				$error = FALSE;
				break;
		}
		if ($error == TRUE)
			$log[] = array ('code' => 'success', 'message' => "Switch OS type set to ${swfamily} ${swrelease}");
		else
			$log[] = array ('code' => 'error', 'message' => 'Failed setting Switch OS type');
	}

	$sysObjectID = snmpget ($endpoints[0], $community, 'sysObjectID.0');
	// Transform OID
	$sysObjectID = ereg_replace ('^.*(enterprises\.)([\.[:digit:]]+)$', '\\2', $sysObjectID);
	if (!isset ($verb_model[$sysObjectID]))
	{
		$log[] = array ('code' => 'error', 'message' => 'Could not guess exact HW model (system OID is ' . $sysObjectID . ')!');
		return $log;
	}
	$log[] = array ('code' => 'success', 'message' => 'HW is ' . $verb_model[$sysObjectID]);
	if (!strlen ($attrs[2]['value']) and isset ($hwtype[$sysObjectID])) // switch HW type
	{
		$error = commitUpdateAttrValue ($object_id, 2, $hwtype[$sysObjectID]);
		if ($error == TRUE)
			$log[] = array ('code' => 'success', 'message' => 'HW type updated Ok');
		else
			$log[] = array ('code' => 'error', 'message' => 'Failed settig HW type: ' . $error);
	}
	// Now fetch ifType, ifDescr and ifPhysAddr and let model-specific code sort the data out.
	$ifType = snmpwalkoid ($endpoints[0], $community, 'ifType');
	$ifDescr = snmpwalkoid ($endpoints[0], $community, 'ifDescr');
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
		$val = trim ($val);
		list ($dummy, $ifIndex) = explode ('.', $key);
		// NET-SNMP may return MAC addresses in one of two (?) formats depending on
		// DISPLAY-HINT internal database. The best we can do about it is to accept both.
		// Bug originally reported by Walery Wysotsky against openSUSE 11.0.
		if (preg_match ('/^string: /i', $val)) // STRING: x:yy:z:xx:y:zz
		{
			list ($dummy, $val) = explode (' ', $val);
			$addrbytes = explode (':', $val);
			foreach ($addrbytes as $bidx => $bytestr)
				if (strlen ($bytestr) == 1)
					$addrbytes[$bidx] = '0' . $bytestr;
		}
		elseif (preg_match ('/^hex-string: /i', $val)) // Hex-STRING: xx yy zz xx yy zz
			$addrbytes = explode (' ', substr ($val, -17));
		else
			continue; // martian format
		$ifList1[$ifIndex]['phyad'] = implode ('', $addrbytes);
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
		case '9.1.697': // WS-C2960G-48TC-L
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
		case '9.1.325': // WS-C2950C-24
			for ($i = 1; $i <= 26; $i++)
			{
				$label = "${i}X"; 
				$error = commitAddPort ($object_id, 'fa0/' . $i, 19, $label, $ifList2["FastEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			break;
		case '9.1.324': // WS-C2950-24
			for ($i = 1; $i <= 24; $i++)
			{
				$label = "${i}X"; 
				$error = commitAddPort ($object_id, 'fa0/' . $i, 19, $label, $ifList2["FastEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			break;
		case '9.1.624': // WS-C3750-24TS
		case '9.1.696': // WS-C2960G-24TC-L
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
		case '9.1.716': // WS-C2960-24TT-L
		case '9.1.563': // WS-C3560-24PS
		case '9.1.633': // WS-C3560-24TS
		case '9.1.428': // WS-C2950G-24
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
		case '9.1.717': // WS-C2960-48TT-L
		case '9.1.429': // WS-C2950G-48
		case '9.1.559': // WS-C2950T-48
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
		case '9.1.516': // WS-C3750G-24TS OR WS-C3750-48PS
			// FIXME: only handles 2 models of 3750, make it handle all of them
			// see if it has 24 or 48 ports
			$numports = substr (snmpget ($endpoints[0], $community, '.1.3.6.1.4.1.9.5.1.3.1.1.14.1'), strlen('INTEGER: '));

			if ($numports == 28) // has 24 ports (+4 SFP) meaning it's a WS-C3750G-24TS
			{
				for ($i = 1; $i <= 28; $i++)
				{
					$label = "${i}";
					$error = commitAddPort ($object_id, 'gi1/0/' . $i, 24, $label, $ifList2["GigabitEthernet1/0/${i}"]['phyad']);
					if ($error == '')
						$newports++;
					else
						$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
				}
			}
			elseif ($numports == 52) // has 48 ports (+4 SFP) meaning it's a WS-C3750-48PS
			{
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
			}
			break;
		case '9.1.564': // WS-C3560-48PS
		case '9.1.634': // WS-C3560-48TS
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
		case '9.1.614': // WS-C3560G-24PS
		case '9.1.615': // WS-C3560G-24TS
		case '9.1.527': // WS-C2970G-24T
		case '9.1.561': // WS-C2970G-24TS
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
		case '9.1.616': // WS-C3560G-48PS
		case '9.1.617': // WS-C3560G-48TS
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
		case '9.1.626': // WS-C4948
		case '9.1.659': // WS-C4948-10GE
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
	// For modular devices we issue a separate SNMP query to determine port type,
	// then extract blade & port numbers from the results.
		case '9.1.58':  // WS-C4503
		case '9.1.503': // WS-C4503
		case '9.1.59':  // WS-C4506
		case '9.1.502': // WS-C4506
		case '9.1.283': // WS-C6509-E
			// get slot #, port # and port type using Cisco's MIB
			$portType = snmpwalkoid ($endpoints[0], $community, '.1.3.6.1.4.1.9.5.1.4.1.1.5');
			$ifList = array();
			$i = 0;
			foreach ($portType as $key => $val)
			{
				// slot = $portIndex[8]
				// port = $portIndex[9]
				$portIndex = explode('.', $key);
				$ifList[$i]['slotno'] = $portIndex[8];
				$ifList[$i]['portno'] = $portIndex[9];

				// note the Cisco port type and corresponding RackTables port type
				list ($dummy, $cptype) = explode (' ', $val);
				$ifList[$i]['cptype'] = $cptype;
				if (array_key_exists($cptype, $porttype))
					$ifList[$i]['ptype'] = $porttype[$cptype];
				else
					$ifList[$i]['ptype'] = null;
				$i++;
			}

			// use Cisco's ifIndex attribute to map Cisco table to standard SNMP table
			$ifIndex  = snmpwalkoid ($endpoints[0], $community, '.1.3.6.1.4.1.9.5.1.4.1.1.11');
			$i = 0;
			foreach ($ifIndex as $val)
			{
				if (is_null($ifList[$i]['ptype']))
				{
					$log[] = array ('code' => 'error', 'message' => 'Unknown port type: ' . $ifList[$i]['cptype']);
				} else {
					switch ($ifList[$i]['ptype'])
					{
						case 19: // fast eth
							$prefix = 'fa';
							break;
						case 28: // 1000base-sx
						case 61: // gig eth
							$prefix = 'gi';
							break;
						default: // unknown, default to gig eth
							$prefix = 'gi';
					}
					$pname = "{$prefix}{$ifList[$i]['slotno']}/{$ifList[$i]['portno']}";
					$label = "slot {$ifList[$i]['slotno']} port {$ifList[$i]['portno']}";
					list($dummy, $index) = explode(' ', $val);

					// if l2address already exists in DB, nullify value so new row gets added without error
					if (!is_null(searchByl2address($ifList1[$index]['phyad']))) $ifList1[$index]['phyad'] = null;
					
					$error = commitAddPort ($object_id, $pname, $ifList[$i]['ptype'], $label, $ifList1[$index]['phyad']);
					if ($error == '')
						$newports++;
					else
						$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $pname . ': ' . $error);
				}
				$i++;
			}
			break;
		case '9.1.278': // WS-C3548-XL
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
		case '9.1.920': // WS-CBS3032-DEL-F
			for ($i = 1; $i <= 24; $i++)
			{
				$label = "${i}";
				$error = commitAddPort ($object_id, 'gi0/' . $i, 24, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			break;
		case '9.1.749': // WS-CBS3030-DEL-F (or WS-CBS3030-DEL-S)
			for ($i = 1; $i <= 16; $i++)
			{
				$label = "${i}";
				$error = commitAddPort ($object_id, 'gi0/' . $i, 24, $label, $ifList2["GigabitEthernet0/${i}"]['phyad']);
				if ($error == '')
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => 'Failed to add port ' . $label . ': ' . $error);
			}
			break;
		case '9.12.3.1.3.719': // N5K-C5020
			break;
		case '11.2.3.7.11.35':
			$n100 = 48;
			// fall through
		case '11.2.3.7.11.36':
			if ($sysObjectID == '11.2.3.7.11.36')
				$n100 = 24;
			$n1000 = 2;
			for ($i = 1; $i <= $n100; $i++)
				if ('' == ($error = commitAddPort ($object_id, $i, 19, $i, $ifList2[$i]['phyad'])))
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => "Failed to add port ${i}: " . $error);
			for ($i = $n100 + 1; $i <= $n100 + $n1000; $i++)
				if ('' == ($error = commitAddPort ($object_id, $i, 24, $i, $ifList2[$i]['phyad'])))
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => "Failed to add port ${i}: " . $error);
			break;
		case '4526.100.2.2':
			for ($i = 1; $i <= 24; $i++)
				if ('' == ($error = commitAddPort ($object_id, $i, 24, '', $ifList2["Unit: 1 Slot: 0 Port: ${i} Gigabit - Level"]['phyad'])))
					$newports++;
				else
					$log[] = array ('code' => 'error', 'message' => "Failed to add port ${label}: ${error}");
			break;
		default:
			$log[] = array ('code' => 'error', 'message' => "Unexpected sysObjectID '${sysObjectID}'");
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


function updateStickerForCell ($cell, $attr_id, $new_value)
{
	if (!strlen ($cell['attrs'][$attr_id]['value']) && strlen ($new_value))
		commitUpdateAttrValue ($cell['id'], $attr_id, $new_value);
}

function doSNMPmining_new ($object_id, $community)
{
	$log = emptyLog();
	global $known_switches, $iftable_processors;
	
	$objectInfo = spotEntity ('object', $object_id);
	$objectInfo['attrs'] = getAttrValues ($object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		return oneLiner (161); // endpoint not found
	if (count ($endpoints) > 1)
		return oneLiner (162); // can't pick an address
	
	if (FALSE === ($sysObjectID = snmpget ($endpoints[0], $community, 'sysObjectID.0')))
		return oneLiner (188); // fatal SNMP failure
	$sysObjectID = ereg_replace ('^.*(enterprises\.)([\.[:digit:]]+)$', '\\2', $sysObjectID);
	$sysName = snmpget ($endpoints[0], $community, 'sysName.0');
	$sysDescr = substr (snmpget ($endpoints[0], $community, 'sysDescr.0'), strlen ('STRING: '));
	$sysDescr = str_replace (array ("\n", "\r"), " ", $sysDescr);  // Make it one line
	if (!isset ($known_switches[$sysObjectID]))
		return oneLiner (189, array ($sysObjectID)); // unknown OID
	updateStickerForCell ($objectInfo, 2, $hwtype[$sysObjectID]);
	updateStickerForCell ($objectInfo, 3, $sysName);
	switch (1)
	{
	case preg_match ('^9\.1\.', $sysObjectID): // Catalyst
		$exact_release = ereg_replace ('^.*, Version ([^ ]+), .*$', '\\1', $sysDescr);
		$major_line = ereg_replace ('^([[:digit:]]+\.[[:digit:]]+)[^[:digit:]].*', '\\1', $exact_release);
		$ios_codes = array
		(
			'12.0' => 244,
			'12.1' => 251,
			'12.2' => 252,
		);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		updateStickerForCell ($objectInfo, 4, $ios_codes[$major_line]);
		$sysChassi = snmpget ($endpoints[0], $community, '1.3.6.1.4.1.9.3.6.3.0');
		if ($sysChassi !== FALSE or $sysChassi !== NULL)
			updateStickerForCell ($objectInfo, 1, str_replace ('"', '', substr ($sysChassi, strlen ('STRING: '))));
		break;
	case preg_match ('^9\.12\.3\.1\.3\.', $sysObjectID): // Nexus
		$exact_release = ereg_replace ('^.*, Version ([^ ]+), .*$', '\\1', $sysDescr);
		$major_line = ereg_replace ('^([[:digit:]]+\.[[:digit:]]+)[^[:digit:]].*', '\\1', $exact_release);
		$nxos_codes = array
		(
			'4.0' => 963,
			'4.1' => 964,
		);
		updateStickerForCell ($objectInfo, 4, $nxos_codes[$major_line]);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		break;
	case preg_match ('^11\.2\.3\.7\.11\.', $sysObjectID): // ProCurve
		$exact_release = ereg_replace ('^.* revision ([^ ]+), .*$', '\\1', $sysDescr);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		break;
	case preg_match ('^4526\.100\.2\.', $sysObjectID): // NETGEAR
		break;
	}
}

?>
