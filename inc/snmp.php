<?php

$iftable_processors = array();
$iftable_processors['catalyst-any-100TX'] = array
(
	'pattern' => '@^FastEthernet(([[:digit:]]+/)?[[:digit:]]+)$@',
	'replacement' => 'fa\\1',
	'dict_key' => 19,
	'label' => '\\1X',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-25-to-26-100FX/MT-RJ'] = array
(
	'pattern' => '@^FastEthernet([[:digit:]]+/(25|26))$@',
	'replacement' => 'fa\\1',
	'dict_key' => 1083,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-any-1000T'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/[[:digit:]]+)$@',
	'replacement' => 'gi\\1',
	'dict_key' => 24,
	'label' => '\\1X',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-any-bp/1000T'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/[[:digit:]]+)$@',
	'replacement' => 'gi\\1',
	'dict_key' => 1087,
	'label' => '',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-any-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/[[:digit:]]+)$@',
	'replacement' => 'gi\\1',
	'dict_key' => 1077,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-any-1000GBIC'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/[[:digit:]]+)$@',
	'replacement' => 'gi\\1',
	'dict_key' => 1078,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-21-to-24-combo-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/(21|22|23|24))$@',
	'replacement' => 'gi\\1',
	'dict_key' => 1077,
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['catalyst-45-to-48-combo-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/(45|46|47|48))$@',
	'replacement' => 'gi\\1',
	'dict_key' => 1077,
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['catalyst-any-10000X2'] = array
(
	'pattern' => '@^TenGigabitEthernet([[:digit:]]+/[[:digit:]]+)$@',
	'replacement' => 'te\\1',
	'dict_key' => 1080,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-25-to-28-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet(0/(25|26|27|28))$@',
	'replacement' => 'gi\\1',
	'dict_key' => 1077,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-49-to-52-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet(0/(49|50|51|52))$@',
	'replacement' => 'gi\\1',
	'dict_key' => 1077,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-13-to-16-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet(0/(13|14|15|16))$@',
	'replacement' => 'gi\\1',
	'dict_key' => 1077,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-21-to-24-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet(0/(21|22|23|24))$@',
	'replacement' => 'gi\\1',
	'dict_key' => 1077,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['nexus-any-10000SFP+'] = array
(
	'pattern' => '@^Ethernet([[:digit:]]/[[:digit:]]+)$@',
	'replacement' => 'e\\1',
	'dict_key' => 1084,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['procurve-chassis-100TX'] = array
(
	'pattern' => '@^([[:digit:]]+)$@',
	'replacement' => '\\1',
	'dict_key' => 19,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['procurve-chassis-1000T'] = array
(
	'pattern' => '@^([[:digit:]]+)$@',
	'replacement' => '\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['procurve-modular-100TX'] = array
(
	'pattern' => '@^([A-Z][[:digit:]]+)$@',
	'replacement' => '\\1',
	'dict_key' => 19,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['procurve-25-to-26-1000T'] = array
(
	'pattern' => '@^(25|26)$@',
	'replacement' => '\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['procurve-49-to-50-1000T'] = array
(
	'pattern' => '@^(49|50)$@',
	'replacement' => '\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-any-1000T'] = array
(
	'pattern' => '@^Unit: 1 Slot: 0 Port: ([[:digit:]]+) Gigabit - Level$@',
	'replacement' => '\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['nortel-any-1000T'] = array
(
	'pattern' => '@^Ethernet Port on unit 1, port ([[:digit:]]+)$@',
	'replacement' => '\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
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
		'processors' => array ('catalyst-25-to-26-100FX/MT-RJ', 'catalyst-any-100TX'),
	),
	'9.1.696' => array
	(
		'dict_key' => 167,
		'text' => 'WS-C2960G-24TC-L: 20 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('catalyst-21-to-24-combo-1000SFP', 'catalyst-any-1000T'),
	),
	'9.1.697' => array
	(
		'dict_key' => 166,
		'text' => 'WS-C2960G-48TC-L: 44 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('catalyst-45-to-48-combo-1000SFP', 'catalyst-any-1000T'),
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
		'text' => 'WS-C4948: 48 RJ-45/10-100-1000T(X) + 4 SFP/1000 + 1 RJ-45/100TX (OOB mgmt)',
		'processors' => array ('catalyst-49-to-52-1000SFP', 'catalyst-any-1000T', 'catalyst-any-100TX'),
	),
	'9.1.659' => array
	(
		'dict_key' => 377,
		'text' => 'WS-C4948-10GE: 48 RJ-45/10-100-1000T(X) + 2 X2/10000 + 1 RJ-45/100TX (OOB mgmt)',
		'processors' => array ('catalyst-any-10000X2', 'catalyst-any-1000T', 'catalyst-any-100TX'),
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
		'processors' => array ('catalyst-11-to-12-1000T', 'catalyst-13-to-16-1000SFP', 'catalyst-any-bp/1000T'),
	),
	'9.1.920' => array
	(
		'dict_key' => 795,
		'text' => 'WS-CBS3032-DEL: 16 internal/10-100-1000T(X) + 4 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-17-to-20-1000T', 'catalyst-21-to-24-1000SFP', 'catalyst-any-bp/1000T'),
	),
	'9.12.3.1.3.719' => array
	(
		'dict_key' => 960,
		'text' => 'N5K-C5020: 40 SFP+/10000',
		'processors' => array ('nexus-any-10000SFP+'),
	),
	'11.2.3.7.11.32' => array
	(
		'dict_key' => 871,
		'text' => 'J4904A: 48 RJ-45/10-100-1000T(X)',
		'processors' => array ('procurve-chassis-1000T'),
	),
	'11.2.3.7.11.36' => array
	(
		'dict_key' => 865,
		'text' => 'J8164A: 24 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('procurve-25-to-26-1000T', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.35' => array
	(
		'dict_key' => 867,
		'text' => 'J8165A: 48 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('procurve-49-to-50-1000T', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.9' => array
	(
		'dict_key' => 1086,
		'text' => 'J4121A: modular system',
		'processors' => array ('procurve-modular-100TX'),
	),
	'4526.100.2.2' => array
	(
		'dict_key' => 562,
		'text' => 'GSM7224: 24 RJ-45/10-100-1000T(X)',
		'processors' => array ('netgear-any-1000T'),
	),
	'45.3.68.5' => array
	(
		'dict_key' => 1085,
		'text' => 'BES50GE-12T PWR: 12 RJ-45/10-100-1000T(X)',
		'processors' => array ('nortel-any-1000T'),
	),
);

// This function is only kept here for reference, it will be removed after we
// make sure, that the new code performs everything correctly.
function doSNMPmining_old ($object_id, $community)
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

	switch ($sysObjectID)
	{
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
		default:
			$log[] = array ('code' => 'error', 'message' => "Unexpected sysObjectID '${sysObjectID}'");
	}
	$error = commitAddPort ($object_id, 'con0', 29, 'console', '');
}

function updateStickerForCell ($cell, $attr_id, $new_value)
{
	if (!strlen ($cell['attrs'][$attr_id]['value']) && strlen ($new_value))
		commitUpdateAttrValue ($cell['id'], $attr_id, $new_value);
}

function doSNMPmining ($object_id, $community)
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
	$sysName = substr (snmpget ($endpoints[0], $community, 'sysName.0'), strlen ('STRING: '));
	$sysDescr = substr (snmpget ($endpoints[0], $community, 'sysDescr.0'), strlen ('STRING: '));
	$sysDescr = str_replace (array ("\n", "\r"), " ", $sysDescr);  // Make it one line
	if (!isset ($known_switches[$sysObjectID]))
		return oneLiner (189, array ($sysObjectID)); // unknown OID
	updateStickerForCell ($objectInfo, 2, $known_switches[$sysObjectID]['dict_key']);
	updateStickerForCell ($objectInfo, 3, $sysName);
	$log = mergeLogs ($log, oneLiner (81, array ('generic')));
	switch (1)
	{
	case preg_match ('/^9\.1\./', $sysObjectID): // Catalyst
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
		$log = mergeLogs ($log, oneLiner (81, array ('catalyst-generic')));
		break;
	case preg_match ('/^9\.12\.3\.1\.3\./', $sysObjectID): // Nexus
		$exact_release = ereg_replace ('^.*, Version ([^ ]+), .*$', '\\1', $sysDescr);
		$major_line = ereg_replace ('^([[:digit:]]+\.[[:digit:]]+)[^[:digit:]].*', '\\1', $exact_release);
		$nxos_codes = array
		(
			'4.0' => 963,
			'4.1' => 964,
		);
		updateStickerForCell ($objectInfo, 4, $nxos_codes[$major_line]);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		$log = mergeLogs ($log, oneLiner (81, array ('nexus-generic')));
		break;
	case preg_match ('/^11\.2\.3\.7\.11\./', $sysObjectID): // ProCurve
		$exact_release = ereg_replace ('^.* revision ([^ ]+), .*$', '\\1', $sysDescr);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		$log = mergeLogs ($log, oneLiner (81, array ('procurve-generic')));
		break;
	default: // Nortel, NETGEAR...
		break;
	}
	$ifInfo = array();
	$tablename = 'ifDescr';
	foreach (snmpwalkoid ($endpoints[0], $community, $tablename) as $oid => $value)
	{
		$randomindex = ereg_replace ("^.*${tablename}\.(.+)\$", '\\1', $oid);
		$value = trim (ereg_replace ('^.+: (.+)$', '\\1', $value), '"');
		$ifInfo[$randomindex][$tablename] = $value;
	}
	$tablename = 'ifPhysAddress';
	foreach (snmpwalkoid ($endpoints[0], $community, $tablename) as $oid => $value)
	{
		$randomindex = ereg_replace ("^.*${tablename}\.(.+)\$", '\\1', $oid);
		$value = trim ($value);
		// NET-SNMP may return MAC addresses in one of two (?) formats depending on
		// DISPLAY-HINT internal database. The best we can do about it is to accept both.
		// Bug originally reported by Walery Wysotsky against openSUSE 11.0.
		if (preg_match ('/^string: /i', $value)) // STRING: x:yy:z:xx:y:zz
		{
			list ($dummy, $value) = explode (' ', $value);
			$addrbytes = explode (':', $value);
			foreach ($addrbytes as $bidx => $bytestr)
				if (strlen ($bytestr) == 1)
					$addrbytes[$bidx] = '0' . $bytestr;
		}
		elseif (preg_match ('/^hex-string: /i', $value)) // Hex-STRING: xx yy zz xx yy zz
			$addrbytes = explode (' ', substr ($value, -17));
		else
			continue; // martian format
		$ifInfo[$randomindex][$tablename] = implode ('', $addrbytes);
	}
	// process each interface only once regardless of how many processors we have to run
	foreach ($ifInfo as $iface)
		foreach ($known_switches[$sysObjectID]['processors'] as $processor_name)
		{
//			echo "subject == '" . $iface['ifDescr'] . "'<br>";
//			echo "pattern == '" . $iftable_processors[$processor_name]['pattern'] . "', replacement == '" . $iftable_processors[$processor_name]['replacement'] . "'<br>";
			$newname = preg_replace ($iftable_processors[$processor_name]['pattern'], $iftable_processors[$processor_name]['replacement'], $iface['ifDescr'], 1, $count);
			if (!$count)
				continue; // try next processor on current port
			$newlabel = preg_replace ($iftable_processors[$processor_name]['pattern'], $iftable_processors[$processor_name]['label'], $iface['ifDescr'], 1, $count);
			commitAddPort ($object_id, $newname, $iftable_processors[$processor_name]['dict_key'], $newlabel, $iface['ifPhysAddress']);
			if (!$iftable_processors[$processor_name]['try_next_proc']) // done with this port
				continue 2;
		}
	foreach ($known_switches[$sysObjectID]['processors'] as $processor_name)
		$log = mergeLogs ($log, oneLiner (81, array ($processor_name)));
	return $log;
}

?>
