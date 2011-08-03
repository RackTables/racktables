<?php

global $iftable_processors;
$iftable_processors = array();

$iftable_processors['catalyst-chassis-mgmt'] = array
(
	'pattern' => '@^FastEthernet([[:digit:]])$@',
	'replacement' => 'fa\\1',
	'dict_key' => '1-19',
	'label' => 'mgmt',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-any-100TX'] = array
(
	'pattern' => '@^FastEthernet([[:digit:]]+/)?([[:digit:]]+)$@',
	'replacement' => 'fa\\1\\2',
	'dict_key' => 19,
	'label' => '\\2X',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-25-to-26-100FX/MT-RJ'] = array
(
	'pattern' => '@^FastEthernet([[:digit:]]+/)?(25|26)$@',
	'replacement' => 'fa\\1\\2',
	'dict_key' => 1195,
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-any-1000T'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?([[:digit:]]+)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => 24,
	'label' => '\\2X',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-uplinks-1000T'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?([[:digit:]]+)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => 24,
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-uplinks-1000SX'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?([[:digit:]]+)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '1-1202', // Gig-SX hardwired
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-any-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?([[:digit:]]+)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-any-1000GBIC'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?([[:digit:]]+)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '3-1078',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-1-to-2-combo-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(1|2)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077',
	'label' => '\\2',
	'try_next_proc' => TRUE,
);

$iftable_processors['catalyst-chassis-1-to-2-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(1|2)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-8-combo-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(8)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077',
	'label' => '\\2',
	'try_next_proc' => TRUE,
);

$iftable_processors['catalyst-chassis-21-to-24-combo-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(21|22|23|24)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077',
	'label' => '\\2',
	'try_next_proc' => TRUE,
);

$iftable_processors['catalyst-chassis-45-to-48-combo-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(45|46|47|48)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077',
	'label' => '\\2',
	'try_next_proc' => TRUE,
);

$iftable_processors['catalyst-chassis-uplinks-10000X2'] = array
(
	'pattern' => '@^TenGigabitEthernet([[:digit:]]+/)?([[:digit:]]+)$@',
	'replacement' => 'te\\1\\2',
	'dict_key' => '6-1080',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-25-to-28-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(25|26|27|28)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-49-to-52-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(49|50|51|52)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-blade-13-to-16-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(13|14|15|16)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-blade-21-to-24-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(21|22|23|24)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-blade-11-to-12-1000T'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(11|12)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '1-24',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-blade-17-to-20-1000T'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(17|18|19|20)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '1-24',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-blade-any-bp/1000T'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?([[:digit:]]+)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '1-1087',
	'label' => '',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-1-to-10-1000T'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(1|2|3|4|5|6|7|8|9|10)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '1-24',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-11-to-12-GBIC'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(11|12)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '3-1078',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['nexus-any-10000SFP+'] = array
(
	'pattern' => '@^Ethernet([[:digit:]]/[[:digit:]]+)$@',
	'replacement' => 'e\\1',
	'dict_key' => '9-1084',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['ftos-any-1000T'] = array
(
	'pattern' => '@^GigabitEthernet 0/(\d+)$@',
	'replacement' => 'gi0/\\1',
	'dict_key' => '1-24',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['ftos-44-to-47-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet 0/(44|45|46|47)$@',
	'replacement' => 'gi0/\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['ftos-any-10000SFP+'] = array
(
	'pattern' => '@^TenGigabitEthernet 0/(\d+)$@',
	'replacement' => 'te0/\\1',
	'dict_key' => '9-1084',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['ftos-any-QSFP+'] = array
(
	'pattern' => '@^fortyGigE 0/(\d+)$@',
	'replacement' => 'fo0/\\1',
	'dict_key' => '10-1588',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['ftos-mgmt'] = array
(
	'pattern' => '@^ManagementEthernet 0/0$@',
	'replacement' => 'ma0/0',
	'dict_key' => '1-19',
	'label' => 'ethernet',
	'try_next_proc' => FALSE,
);

$iftable_processors['nexus-mgmt'] = array
(
	'pattern' => '@^(mgmt[[:digit:]]+)$@',
	'replacement' => '\\1',
	'dict_key' => '1-24',
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

$iftable_processors['procurve-45-to-48-combo-1000SFP'] = array
(
	'pattern' => '@^(45|46|47|48)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['procurve-21-to-24-combo-1000SFP'] = array
(
	'pattern' => '@^(21|22|23|24)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
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

$iftable_processors['procurve-51-to-52-1000SFP'] = array
(
	'pattern' => '@^(51|52)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-chassis-any-1000T'] = array
(
	'pattern' => '@^Unit: 1 Slot: 0 Port: ([[:digit:]]+) Gigabit - Level$@',
	'replacement' => '\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-chassis-21-to-24-1000Tcombo'] = array
(
	'pattern' => '@^Unit: 1 Slot: 0 Port: ([[:digit:]]+) Gigabit - Level$@',
	'replacement' => '\\1',
	'dict_key' => 24,
	'label' => '\\1T',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-chassis-21-to-24-1000SFP'] = array
(
	'pattern' => '@^Unit: 1 Slot: 0 Port: ([[:digit:]]+) Gigabit - Level$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1F',
	'try_next_proc' => TRUE,
);

$iftable_processors['nortel-any-1000T'] = array
(
	'pattern' => '@^Ethernet Port on unit 1, port ([[:digit:]]+)$@',
	'replacement' => '\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['smc-combo-45-to-48'] = array
(
	'pattern' => '@^Ethernet Port on unit 1, port (45|46|47|48)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['smc2-combo-21-to-24'] = array
(
	'pattern' => '@^Port #(21|22|23|24)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['smc2-combo-23-to-24'] = array
(
	'pattern' => '@^Port #(23|24)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['smc2-combo-25-to-28'] = array
(
	'pattern' => '@^Port #(25|26|27|28)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['smc2-1000T-25-to-28'] = array
(
	'pattern' => '@^Port #(25|26|27|28)$@',
	'replacement' => '\\1',
	'dict_key' => '1-24',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['smc2-any-1000T'] = array
(
	'pattern' => '@^Port #(\d+)$@',
	'replacement' => '\\1',
	'dict_key' => '1-24',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['smc2-any-100TX'] = array
(
	'pattern' => '@^Port #(\d+)$@',
	'replacement' => '\\1',
	'dict_key' => '1-19',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['juniper-DPCE-R-4XGE-XFP'] = array
(
	'pattern' => '@^xe-([[:digit:]]+)/([[:digit:]]+/[[:digit:]]+)$@',
	'replacement' => '\\0',
	'dict_key' => '8-1082', // XFP/empty
	'label' => 'slot \\1 port \\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['juniper-ex-pic0-1000T'] = array
(
	'pattern' => '@^ge-([[:digit:]]+)/0/([[:digit:]]+)$@',
	'replacement' => '\\0',
	'dict_key' => '1-24',
	'label' => 'unit \\1 port \\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['juniper-ex-mgmt'] = array
(
	'pattern' => '/^me0$/',
	'replacement' => 'me0',
	'dict_key' => '1-24',
	'label' => 'MGMT',
	'try_next_proc' => FALSE,
);

$iftable_processors['quidway-21-to-24-comboT'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/[[:digit:]]+/)(21|22|23|24)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '1-24',
	'label' => '\\2',
	'try_next_proc' => TRUE,
);

$iftable_processors['quidway-21-to-24-comboSFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/[[:digit:]]+/)(21|22|23|24)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077',
	'label' => '\\2',
	'try_next_proc' => TRUE,
);

$iftable_processors['quidway-slot1-SFP'] = array
(
	'pattern' => '@^GigabitEthernet0/1/([[:digit:]]+)$@',
	'replacement' => 'gi0/1/\\1',
	'dict_key' => '4-1077',
	'label' => 'SFP\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['quidway-any-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/[[:digit:]]+/)([[:digit:]]+)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077', // empty SFP-1000
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['quidway-any-1000T'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/[[:digit:]]+/)([[:digit:]]+)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '1-24',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['quidway-XFP'] = array
(
	'pattern' => '@^XGigabitEthernet([[:digit:]]+/[[:digit:]]+/)([[:digit:]]+)$@',
	'replacement' => 'xg\\1\\2',
	'dict_key' => '8-1082',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['quidway-mgmt'] = array
(
	'pattern' => '@^MEth([[:digit:]]+/[[:digit:]]+/)([[:digit:]]+)$@',
	'replacement' => 'me\\1\\2',
	'dict_key' => '1-19',
	'label' => 'eth',
	'try_next_proc' => FALSE,
);

$iftable_processors['fgs-1-to-4-comboSFP'] = array
(
	# only 4 first copper ports of 1st unit of a stack
	'pattern' => '@^GigabitEthernet1/1/(1|2|3|4)$@',
	'replacement' => 'e1/1/\\1',
	'dict_key' => '4-1077',
	'label' => '\\1F',
	'try_next_proc' => TRUE,
);

$iftable_processors['fgs-any-1000T'] = array
(
	'pattern' => '@^GigabitEthernet(0|1)/1/([[:digit:]]+)$@',
	'replacement' => 'e\\1/1/\\2',
	'dict_key' => '1-24',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['fls624-combo'] = array
(
	'pattern' => '@^GigabitEthernet0/1/(21|22|23|24)$@',
	'replacement' => 'e0/1/\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['fls648-combo'] = array
(
	'pattern' => '@^GigabitEthernet0/1/(45|46|47|48)$@',
	'replacement' => 'e0/1/\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

# These can be CX4, but that is not handled here.
$iftable_processors['fls-uplinks'] = array
(
	'pattern' => '@^10GigabitEthernet0/([234])/1$@',
	'replacement' => 'e0/\\1/1',
	'dict_key' => '8-1082',
	'label' => 'Slot \\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['fgs-uplinks'] = array
(
	'pattern' => '@^10GigabitEthernet1/2/([[:digit:]]+)$@',
	'replacement' => 'e1/2/\\1',
	'dict_key' => '8-1082', // default is XFP, but may be overridden to CX4
	'label' => 'Slot2 \\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['fcx-uplinks'] = array
(
	'pattern' => '@^10GigabitEthernet1/2/([[:digit:]]+)$@',
	'replacement' => 'e1/2/\\1',
	'dict_key' => '9-1084',
	'label' => 'X\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['fcx-management'] = array
(
	'pattern' => '@^Management$@',
	'replacement' => 'management1',
	'dict_key' => '1-24',
	'label' => 'Management',
	'try_next_proc' => FALSE,
);

$iftable_processors['summit-25-to-26-XFP-uplinks'] = array
(
	'pattern' => '@^.+ Port (25|26)$@',
	'replacement' => '\\1',
	'dict_key' => '8-1082',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['summit-27-to-30-XFP-uplinks'] = array
(
	'pattern' => '@^.+ Port (27|28|29|30)$@',
	'replacement' => '\\1',
	'dict_key' => '8-1082',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['summit-21-to-24-comboSFP'] = array
(
	'pattern' => '@^.+ Port (21|22|23|24)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['summit-13-to-24-comboT'] = array
(
	'pattern' => '@^.+ Port (1[3456789]|2[01234])$@',
	'replacement' => '\\1',
	'dict_key' => '1-24',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['summit-any-1000T'] = array
(
	'pattern' => '@^.+ Port ([[:digit:]]+)$@',
	'replacement' => '\\1',
	'dict_key' => '1-24',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['summit-any-SFP'] = array
(
	'pattern' => '@^.+ Port ([[:digit:]]+)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['summit-management'] = array
(
	'pattern' => '@^Management Port$@',
	'replacement' => 'mgmt',
	'dict_key' => '1-19',
	'label' => 'management',
	'try_next_proc' => FALSE,
);

$iftable_processors['C3KX-NM-10000'] = array
(
	'pattern' => '@^TenGigabitEthernet1/(\d+)$@',
	'replacement' => 'te1/\\1',
	'dict_key' => '9-1084',
	'label' => 'NM TE\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['C3KX-NM-1000'] = array
(
	'pattern' => '@^GigabitEthernet1/(\d+)$@',
	'replacement' => 'gi1/\\1',
	'dict_key' => '4-1077',
	'label' => 'NM G\\1',
	'try_next_proc' => FALSE,
);

global $known_switches;
$known_switches = array // key is system OID w/o "enterprises" prefix
(
	'9.1.248' => array
	(
		'dict_key' => 393,
		'text' => 'WS-C3524-XL: 24 RJ-45/10-100TX + 2 GBIC/1000',
		'processors' => array ('catalyst-chassis-any-1000GBIC', 'catalyst-chassis-any-100TX'),
	),
	'9.1.324' => array
	(
		'dict_key' => 380,
		'text' => 'WS-C2950-24: 24 RJ-45/10-100TX',
		'processors' => array ('catalyst-chassis-any-100TX'),
	),
	'9.1.325' => array
	(
		'dict_key' => 382,
		'text' => 'WS-C2950C-24: 24 RJ-45/10-100TX + 2 MT-RJ/100FX fiber',
		'processors' => array ('catalyst-chassis-25-to-26-100FX/MT-RJ', 'catalyst-chassis-any-100TX'),
	),
	'9.1.559' => array
	(
		'dict_key' => 387,
		'text' => 'WS-C2950T-48: 48 RJ-45/10-100TX + 2 1000T uplinks',
		'processors' => array ('catalyst-chassis-uplinks-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.695' => array
	(
		'dict_key' => 1590,
		'text' => 'WS-C2960-48TC-L: 48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.793' => array
	(
		'dict_key' => 1575,
		'text' => 'WS-C3560E-24TD: 24 RJ-45/10-100-1000T(X) + 2 X2/10000 w/TwinGig + OOBM',
		'processors' => array
		(
			'catalyst-chassis-25-to-28-1000SFP', // TwinGig
			'catalyst-chassis-any-1000T',
			'catalyst-chassis-uplinks-10000X2',
			'catalyst-chassis-mgmt',
		),
	),
	'9.1.794' => array
	(
		'dict_key' => 1574,
		'text' => 'WS-C3560E-48TD: 48 RJ-45/10-100-1000T(X) + 2 X2/10000 w/TwinGig + OOBM',
		'processors' => array
		(
			'catalyst-chassis-49-to-52-1000SFP', // TwinGig
			'catalyst-chassis-any-1000T',
			'catalyst-chassis-uplinks-10000X2',
			'catalyst-chassis-mgmt',
		),
	),
	'9.1.799' => array
	(
		'dict_key' => 168,
		'text' => 'WS-C2960G-8TC-L: 7 RJ-45/10-100-1000T(X) + 1 combo-gig',
		'processors' => array ('catalyst-chassis-8-combo-1000SFP', 'catalyst-chassis-any-1000T'),
	),
	'9.1.696' => array
	(
		'dict_key' => 167,
		'text' => 'WS-C2960G-24TC-L: 20 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('catalyst-chassis-21-to-24-combo-1000SFP', 'catalyst-chassis-any-1000T'),
	),
	'9.1.697' => array
	(
		'dict_key' => 166,
		'text' => 'WS-C2960G-48TC-L: 44 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('catalyst-chassis-45-to-48-combo-1000SFP', 'catalyst-chassis-any-1000T'),
	),
	'9.1.716' => array
	(
		'dict_key' => 164,
		'text' => 'WS-C2960-24TT-L: 24 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-chassis-any-100TX', 'catalyst-chassis-any-1000T'),
	),
	'9.1.717' => array
	(
		'dict_key' => 1572,
		'text' => 'WS-C2960-48TT-L: 48 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-chassis-any-100TX', 'catalyst-chassis-any-1000T'),
	),
	'9.1.950' => array
	(
		'dict_key' => 1347,
		'text' => 'WS-C2960-24PC: 44 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.927' => array
	(
		'dict_key' => 140,
		'text' => 'WS-C2960-48TC-S: 48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.1005' => array
	(
		'dict_key' => 1573,
		'text' => 'WS-C2960-48TT-S: 48 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-chassis-any-100TX', 'catalyst-chassis-any-1000T'),
	),
	'9.1.527' => array
	(
		'dict_key' => 210,
		'text' => 'WS-C2970G-24T: 24 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-chassis-any-1000T'),
	),
	'9.1.561' => array
	(
		'dict_key' => 115,
		'text' => 'WS-C2970G-24TS: 24 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-chassis-25-to-28-1000SFP', 'catalyst-chassis-any-1000T'),
	),
	'9.1.633' => array
	(
		'dict_key' => 169,
		'text' => 'WS-C3560-24TS: 24 RJ-45/10-100TX + 2 SFP/1000',
		'processors' => array ('catalyst-chassis-any-1000SFP', 'catalyst-chassis-any-100TX'),
	),
	'9.1.634' => array
	(
		'dict_key' => 170,
		'text' => 'WS-C3560-48TS: 48 RJ-45/10-100TX + 4 SFP/1000',
		'processors' => array ('catalyst-chassis-any-1000SFP', 'catalyst-chassis-any-100TX'),
	),
	'9.1.563' => array
	(
		'dict_key' => 171,
		'text' => 'WS-C3560-24PS: 24 RJ-45/10-100TX + 2 SFP/1000',
		'processors' => array ('catalyst-chassis-any-1000SFP', 'catalyst-chassis-any-100TX'),
	),
	'9.1.564' => array
	(
		'dict_key' => 172,
		'text' => 'WS-C3560-48PS: 48 RJ-45/10-100TX + 4 SFP/1000',
		'processors' => array ('catalyst-chassis-any-1000SFP', 'catalyst-chassis-any-100TX'),
	),
	'9.1.614' => array
	(
		'dict_key' => 175,
		'text' => 'WS-C3560G-24PS: 24 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-chassis-25-to-28-1000SFP', 'catalyst-chassis-any-1000T'),
	),
	'9.1.615' => array
	(
		'dict_key' => 173,
		'text' => 'WS-C3560G-24TS: 24 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-chassis-25-to-28-1000SFP', 'catalyst-chassis-any-1000T'),
	),
	'9.1.616' => array
	(
		'dict_key' => 176,
		'text' => 'WS-C3560G-48PS: 48 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-chassis-49-to-52-1000SFP', 'catalyst-chassis-any-1000T'),
	),
	'9.1.617' => array
	(
		'dict_key' => 174,
		'text' => 'WS-C3560G-48TS: 48 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-chassis-49-to-52-1000SFP', 'catalyst-chassis-any-1000T'),
	),
	'9.1.626' => array
	(
		'dict_key' => 147,
		'text' => 'WS-C4948: 48 RJ-45/10-100-1000T(X) + 4 SFP/1000 + 1 RJ-45/100TX (OOB mgmt)',
		'processors' => array ('catalyst-chassis-49-to-52-1000SFP', 'catalyst-chassis-uplinks-1000T', 'catalyst-chassis-mgmt'),
	),
	'9.1.659' => array
	(
		'dict_key' => 377,
		'text' => 'WS-C4948-10GE: 48 RJ-45/10-100-1000T(X) + 2 X2/10000 + 1 RJ-45/100TX (OOB mgmt)',
		'processors' => array ('catalyst-chassis-uplinks-10000X2', 'catalyst-chassis-uplinks-1000T', 'catalyst-chassis-mgmt'),
	),
	'9.1.428' => array
	(
		'dict_key' => 389,
		'text' => 'WS-C2950G-24: 24 RJ-45/10-100TX + 2 GBIC/1000',
		'processors' => array ('catalyst-chassis-any-1000GBIC', 'catalyst-chassis-any-100TX'),
	),
	'9.1.429' => array
	(
		'dict_key' => 390,
		'text' => 'WS-C2950G-48: 48 RJ-45/10-100TX + 2 GBIC/1000',
		'processors' => array ('catalyst-chassis-any-1000GBIC', 'catalyst-chassis-any-100TX'),
	),
	'9.1.559' => array
	(
		'dict_key' => 387,
		'text' => 'WS-C2950T-48: 48 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-chassis-uplinks-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.560' => array
	(
		'dict_key' => 384,
		'text' => 'WS-C2950SX-48: 48 RJ-45/10-100TX + 2 1000Base-SX',
		'processors' => array ('catalyst-chassis-uplinks-1000SX', 'catalyst-chassis-any-100TX'),
	),
	'9.1.749' => array
	(
		'dict_key' => 989,
		'text' => 'WS-CBS3030-DEL: 10 internal/10-100-1000T(X) + 2 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-blade-11-to-12-1000T', 'catalyst-blade-13-to-16-1000SFP', 'catalyst-blade-any-bp/1000T'),
	),
	'9.1.920' => array
	(
		'dict_key' => 795,
		'text' => 'WS-CBS3032-DEL: 16 internal/10-100-1000T(X) + 4 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-blade-17-to-20-1000T', 'catalyst-blade-21-to-24-1000SFP', 'catalyst-blade-any-bp/1000T'),
	),
	'9.1.367' => array
	(
		'dict_key' => 404,
		'text' => 'WS-C3550-48: 48 RJ-45/10-100TX + 2 GBIC/1000',
		'processors' => array ('catalyst-chassis-any-100TX', 'catalyst-chassis-any-1000GBIC'),
	),
	'9.1.368' => array
	(
		'dict_key' => 398,
		'text' => 'WS-C3550-12T: 10 RJ-45/10-100-1000T(X) + 2 GBIC/1000',
		'processors' => array ('catalyst-1-to-10-1000T', 'catalyst-11-to-12-GBIC'),
	),
	'9.1.282' => array
	(
		'dict_key' => 154,
		'text' => 'WS-C6506: modular device (INCOMPLETE!)',
		'processors' => array ('catalyst-chassis-any-1000T'),
	),
	'9.1.1104' => array
	(
		'dict_key' => 1348,
		'text' => 'WS-C2350-48TD: 48 RJ-45/10-100-1000T(X) + 2 X2/10000 w/TwinGig option',
		'processors' => array
		(
			'catalyst-chassis-49-to-52-1000SFP', // TwinGig actually
			'catalyst-chassis-any-1000T',
			'catalyst-chassis-uplinks-10000X2',
			'catalyst-chassis-mgmt',
		),
	),
	'9.1.1016' => array
	(
		'dict_key' => 1370,
		'text' => 'WS-C2960-48PST-L: 48 RJ-45/10-100TX PoE + 2 SFP/1000 + 2 RJ-45/10-100-1000T(X)',
		'processors' => array
		(
			'catalyst-chassis-any-100TX',
			'catalyst-chassis-1-to-2-1000SFP',
			'catalyst-chassis-uplinks-1000T',
		),
	),
	'9.1.1227' => array
	(
		'dict_key' => 1577,
		'text' => 'WS-C3560X-48T: 48 RJ-45/10-100-1000T(X) + network module + OOBM',
		'processors' => array
		(
			'C3KX-NM-10000',
			'C3KX-NM-1000',
			'catalyst-chassis-any-1000T',
			'catalyst-chassis-mgmt',
		),
	),
	'9.12.3.1.3.719' => array
	(
		'dict_key' => 960,
		'text' => 'N5K-C5020: 40 SFP+/10000',
		'processors' => array ('nexus-any-10000SFP+', 'nexus-mgmt'),
	),
	'9.12.3.1.3.798' => array
	(
		'dict_key' => 959,
		'text' => 'N5K-C5010: 20 SFP+/10000',
		'processors' => array ('nexus-any-10000SFP+', 'nexus-mgmt'),
	),
	'11.2.3.7.11.19' => array
	(
		'dict_key' => 859,
		'text' => 'J4813A: 24 RJ-45/10-100TX + 2 modules of varying type',
		'processors' => array ('procurve-chassis-100TX'),
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
	'11.2.3.7.11.63' => array
	(
		'dict_key' => 868,
		'text' => 'J9021A: 20 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('procurve-21-to-24-combo-1000SFP', 'procurve-chassis-1000T'),
	),
	'11.2.3.7.11.65' => array
	(
		'dict_key' => 850,
		'text' => 'J9028A: 22 RJ-45/10-100-1000T(X) + 2 combo-gig',
		'processors' => array ('smc2-combo-23-to-24', 'smc2-any-1000T'),
	),
	'11.2.3.7.11.86' => array
	(
		'dict_key' => 1571,
		'text' => 'J9145A: 20 RJ-45/10-100-1000T(X) + 4 combo-gig + varying uplinks',
		'processors' => array ('procurve-21-to-24-combo-1000SFP', 'procurve-chassis-1000T'),
	),
	'11.2.3.7.11.87' => array
	(
		'dict_key' => 1349,
		'text' => 'J9147A: 44 RJ-45/10-100-1000T(X) + 4 combo-gig + varying uplinks',
		'processors' => array ('procurve-45-to-48-combo-1000SFP', 'procurve-chassis-1000T'),
	),
	'11.2.3.7.11.79' => array
	(
		'dict_key' => 863,
		'text' => 'J9089A: 48 RJ-45/10-100TX PoE + 2 1000T + 2 SFP-1000',
		'processors' => array ('procurve-49-to-50-1000T', 'procurve-51-to-52-1000SFP', 'procurve-chassis-100TX'),
	),
	'4526.100.2.2' => array
	(
		'dict_key' => 562,
		'text' => 'GSM7224: 20 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('netgear-chassis-21-to-24-1000SFP', 'netgear-chassis-21-to-24-1000Tcombo', 'netgear-chassis-any-1000T'),
	),
	'45.3.68.5' => array
	(
		'dict_key' => 1085,
		'text' => 'BES50GE-12T PWR: 12 RJ-45/10-100-1000T(X)',
		'processors' => array ('nortel-any-1000T'),
	),
	'2636.1.1.1.2.29' => array
	(
		'dict_key' => 925,
		'text' => 'MX240 modular router',
		'processors' => array ('juniper-DPCE-R-4XGE-XFP'),
	),
	// Juniper Networks assigns single SNMP OID per series:
	// EX2200 2636.1.1.1.1.43
	// EX3200 2636.1.1.1.2.30
	// EX4200 2636.1.1.1.2.31
	// EX4500 2636.1.1.1.1.44
	// There is a special workaround in code below to derive specific
	// product number from sysDescr string.
	'2636.1.1.1.2.31' => array
	(
		'dict_key' => 905,
		'text' => 'Juniper EX4200 series',
		'processors' => array ('juniper-ex-pic0-1000T', 'juniper-ex-mgmt'),
	),
	'2011.2.23.96' => array
	(
		'dict_key' => 1321,
		'text' => 'S5328C-EI-24S: 20 SFP-1000 + 4 combo-gig + 2 XFP slots',
		'processors' => array ('quidway-21-to-24-comboT', 'quidway-any-1000SFP', 'quidway-XFP', 'quidway-mgmt'),
	),
	'2011.2.23.103' => array
	(
		'dict_key' => 1341,
		'text' => 'S5352C-SI: 48 RJ-45/10-100-1000T(X) + optional 2xXFP/4xSFP slots',
		'processors' => array ('quidway-slot1-SFP', 'quidway-any-1000T', 'quidway-XFP', 'quidway-mgmt'),
	),
	'2011.2.23.102' => array
	(
		'dict_key' => 1339,
		'text' => 'S5328C-SI: 20 RJ-45/10-100-1000T(X) + 4 combo-gig + 2 XFP slots',
		'processors' => array ('quidway-21-to-24-comboSFP', 'quidway-any-1000T', 'quidway-XFP', 'quidway-mgmt'),
	),
	'1991.1.3.45.2.1.1.1' => array
	(
		'dict_key' => 127,
		'text' => 'FGS648P: 48 RJ-45/10-100-1000T(X) + 4 combo-gig + uplink slot',
		'processors' => array ('fgs-1-to-4-comboSFP', 'fgs-any-1000T', 'fgs-uplinks'),
	),
	'1991.1.3.45.2.2.1.1' => array
	(
		'dict_key' => 131,
		'text' => 'FGS648P-POE: 48 RJ-45/10-100-1000T(X) + 4 combo-gig + uplink slot',
		'processors' => array ('fgs-1-to-4-comboSFP', 'fgs-any-1000T', 'fgs-uplinks'),
	),
	'1991.1.3.54.2.4.1.1' => array
	(
		'dict_key' => 1362,
		'text' => 'FCX 648: 48 RJ-45/10-100-1000T(X) + uplink slot with 4 SFP+',
		'processors' => array ('fgs-any-1000T', 'fcx-uplinks', 'fcx-management'),
	),
	'1991.1.3.46.1.1.1.1' => array
	(
		'dict_key' => 413,
		'text' => 'FLS 624: 20 RJ-45/10-100-1000T(X) + 4 combo-gig + 3 optional 10G modules',
		'processors' => array ('fls624-combo', 'fgs-any-1000T', 'fls-uplinks'),
	),
	'1991.1.3.46.2.1.1.1' => array
	(
		'dict_key' => 414,
		'text' => 'FLS 648: 44 RJ-45/10-100-1000T(X) + 4 combo-gig + 2 optional 10G modules',
		'processors' => array ('fls648-combo', 'fgs-any-1000T', 'fls-uplinks'),
	),
	'1991.1.3.52.2.2.1.1' => array
	(
		'dict_key' => 1032,
		'text' => 'FWS648G: 4 combo-gig + 44 RJ-45/10-100-1000T(X)',
		'processors' => array ('fgs-1-to-4-comboSFP', 'fgs-any-1000T'),
	),
	'1916.2.71' => array
	(
		'dict_key' => 694,
		'text' => 'X450a-24t: 20 RJ-45/10-100-1000T(X) + 4 combo-gig + XFP uplinks slot',
		'processors' => array ('summit-25-to-26-XFP-uplinks', 'summit-21-to-24-comboSFP', 'summit-any-1000T', 'summit-management'),
	),
	'1916.2.139' => array
	(
		'dict_key' => 1353,
		'text' => 'X480-24x: 12 SFP-1000 + 12 combo-gig + 2 XFP + VIM slot',
		'processors' => array
		(
			'summit-27-to-30-XFP-uplinks',
			'summit-25-to-26-XFP-uplinks',
			'summit-13-to-24-comboT',
			'summit-any-SFP',
			'summit-management'
		),
	),
	'202.20.68' => array
	(
		'dict_key' => 1374,
		'text' => 'SMC8150L2: 46 RJ-45/10-100-1000T(X) + 4 combo ports',
		'processors' => array ('smc-combo-45-to-48', 'nortel-any-1000T'),
	),
	'6027.1.3.12' => array
	(
		'dict_key' => 1471,
		'text' => 'Force10 S60: 44 RJ-45/10-100-1000T(X) + 4 SFP-1000 ports + 0/2/4 SFP+ ports',
		'processors' => array ('ftos-44-to-47-1000SFP', 'ftos-any-1000T', 'ftos-any-10000SFP+', 'ftos-mgmt'),
	),
	'6027.1.3.13' => array
	(
		'dict_key' => 1470,
		'text' => 'Force10 S55: 44 RJ-45/10-100-1000T(X) + 4 SFP-1000 ports + 0/2/4 SFP+ ports',
		'processors' => array ('ftos-44-to-47-1000SFP', 'ftos-any-1000T', 'ftos-any-10000SFP+', 'ftos-mgmt'),
	),
	'6027.1.3.14' => array
	(
		'dict_key' => 1472,
		'text' => 'Force10 S4810: 48 SFP+-1000/10000 + 4 QSFP-40000 ports',
		'processors' => array ('ftos-any-10000SFP+', 'ftos-any-QSFP+', 'ftos-mgmt'),
	),
	'202.20.59' => array
	(
		'dict_key' => 1371,
		'text' => 'SMC8124L2: 20 RJ-45/10-100-1000T(X) + 4 combo ports',
		'processors' => array ('smc2-combo-21-to-24', 'smc2-any-1000T'),
	),
	'202.20.66' => array
	(
		'dict_key' => 1567,
		'text' => 'SMC6128L2: 24 RJ-45/10-100TX + 4 combo-gig ports',
		'processors' => array ('smc2-combo-25-to-28', 'smc2-1000T-25-to-28', 'smc2-any-100TX'),
	),
);

global $swtype_pcre;
$swtype_pcre = array
(
	'/Huawei Versatile Routing Platform Software.+VRP.+Software, Version 5.30 /s' => 1360,
	'/Huawei Versatile Routing Platform Software.+VRP.+Software, Version 5.50 /s' => 1361,
	'/Huawei Versatile Routing Platform Software.+VRP.+Software,\s*Version 5.70 /is' => 1369,
	// FIXME: get sysDescr for IronWare 5 and add a pattern
	'/^Brocade Communications Systems.+, IronWare Version 07\./' => 1364,
	'/^Juniper Networks,.+JUNOS 9\./' => 1366,
	'/^Juniper Networks,.+JUNOS 10\./' => 1367,
);

function updateStickerForCell ($cell, $attr_id, $new_value)
{
	if (!strlen ($cell['attrs'][$attr_id]['value']) && strlen ($new_value))
		commitUpdateAttrValue ($cell['id'], $attr_id, $new_value);
}

// Accept "X-Y" on input and make sure, that PortInterfaceCompat contains
// a record with IIF id = X and OIF id = Y.
function checkPIC ($port_type_id)
{
	$matches = array();
	switch (1)
	{
	case preg_match ('/^([[:digit:]]+)-([[:digit:]]+)$/', $port_type_id, $matches):
		$iif_id = $matches[1];
		$oif_id = $matches[2];
		break;
	case preg_match ('/^([[:digit:]]+)$/', $port_type_id, $matches):
		$iif_id = 1;
		$oif_id = $matches[1];
		break;
	default:
		return;
	}
	foreach (getPortInterfaceCompat() as $record)
		if ($record['iif_id'] == $iif_id and $record['oif_id'] == $oif_id)
			return;
	commitSupplementPIC ($iif_id, $oif_id);
}

$msgcode['doSNMPmining']['ERR1'] = 161;
$msgcode['doSNMPmining']['ERR2'] = 162;
$msgcode['doSNMPmining']['OK'] = 81;
function doSNMPmining ($object_id, $snmpsetup)
{
	$objectInfo = spotEntity ('object', $object_id);
	$objectInfo['attrs'] = getAttrValues ($object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		return showFuncMessage (__FUNCTION__, 'ERR1'); // endpoint not found
	if (count ($endpoints) > 1)
		return showFuncMessage (__FUNCTION__, 'ERR2'); // can't pick an address

	$device = new RTSNMPDevice ($endpoints[0], $snmpsetup);

	switch ($objectInfo['objtype_id'])
	{
	case 7:
	case 8:
		return doSwitchSNMPmining ($objectInfo, $device);
	case 2:
		return doPDUSNMPmining ($objectInfo, $device);
	}	
}

$msgcode['doSwitchSNMPmining']['ERR3'] = 188;
$msgcode['doSwitchSNMPmining']['ERR4'] = 189;
function doSwitchSNMPmining ($objectInfo, $device)
{
	global $known_switches, $iftable_processors;
	
	if (FALSE === ($sysObjectID = $device->snmpget ('sysObjectID.0')))
		return showFuncMessage (__FUNCTION__, 'ERR3'); // // fatal SNMP failure
	$sysObjectID = preg_replace ('/^.*(enterprises\.)([\.[:digit:]]+)$/', '\\2', $sysObjectID);
	$sysName = substr ($device->snmpget ('sysName.0'), strlen ('STRING: '));
	$sysDescr = substr ($device->snmpget ('sysDescr.0'), strlen ('STRING: '));
	$sysDescr = str_replace (array ("\n", "\r"), " ", $sysDescr);  // Make it one line
	if (!isset ($known_switches[$sysObjectID]))
		return showFuncMessage (__FUNCTION__, 'ERR4', array ($sysObjectID)); // unknown OID
	foreach (array_keys ($known_switches[$sysObjectID]['processors']) as $pkey)
		if (!array_key_exists ($known_switches[$sysObjectID]['processors'][$pkey], $iftable_processors))
		{
			showWarning ('processor "' . $known_switches[$sysObjectID]['processors'][$pkey] . '" not found');
			unset ($known_switches[$sysObjectID]['processors'][$pkey]);
		}
	updateStickerForCell ($objectInfo, 2, $known_switches[$sysObjectID]['dict_key']);
	updateStickerForCell ($objectInfo, 3, $sysName);
	showOneLiner (81, array ('generic'));
	switch (1)
	{
	case preg_match ('/^9\.1\./', $sysObjectID): // Catalyst
		$exact_release = preg_replace ('/^.*, Version ([^ ]+), .*$/', '\\1', $sysDescr);
		$major_line = preg_replace ('/^([[:digit:]]+\.[[:digit:]]+)[^[:digit:]].*/', '\\1', $exact_release);
		$ios_codes = array
		(
			'12.0' => 244,
			'12.1' => 251,
			'12.2' => 252,
		);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		if (array_key_exists ($major_line, $ios_codes))
			updateStickerForCell ($objectInfo, 4, $ios_codes[$major_line]);
		$sysChassi = $device->snmpget ('1.3.6.1.4.1.9.3.6.3.0');
		if ($sysChassi !== FALSE or $sysChassi !== NULL)
			updateStickerForCell ($objectInfo, 1, str_replace ('"', '', substr ($sysChassi, strlen ('STRING: '))));
		checkPIC ('1-29');
		commitAddPort ($objectInfo['id'], 'con0', '1-29', 'console', ''); // RJ-45 RS-232 console
		// blade devices are powered through internal circuitry of chassis
		if ($sysObjectID != '9.1.749' and $sysObjectID != '9.1.920')
		{
			checkPIC ('1-16'); // AC input
			commitAddPort ($objectInfo['id'], 'AC-in', '1-16', '', '');
		}
		showOneLiner (81, array ('catalyst-generic'));
		break;
	case preg_match ('/^9\.12\.3\.1\.3\./', $sysObjectID): // Nexus
		$exact_release = preg_replace ('/^.*, Version ([^ ]+), .*$/', '\\1', $sysDescr);
		$major_line = preg_replace ('/^([[:digit:]]+\.[[:digit:]]+)[^[:digit:]].*/', '\\1', $exact_release);
		$nxos_codes = array
		(
			'4.0' => 963,
			'4.1' => 964,
		);
		if (array_key_exists ($major_line, $nxos_codes))
			updateStickerForCell ($objectInfo, 4, $nxos_codes[$major_line]);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		checkPIC ('1-29');
		commitAddPort ($objectInfo['id'], 'con0', '1-29', 'console', ''); // RJ-45 RS-232 console
		checkPIC ('1-16'); // AC input
		commitAddPort ($objectInfo['id'], 'AC-in-1', '1-16', 'AC1', '');
		commitAddPort ($objectInfo['id'], 'AC-in-2', '1-16', 'AC2', '');
		showOneLiner (81, array ('nexus-generic'));
		break;
	case preg_match ('/^11\.2\.3\.7\.11\.(\d+)$/', $sysObjectID, $matches): // ProCurve
		$console_per_product = array
		(
			79 => '1-29', # RJ-45 RS-232
			86 => '1-29',
			87 => '1-29',
			63 => '1-29',
			19 => '1-681', # DB-9 RS-232
		);
		if (array_key_exists ($matches[1], $console_per_product))
		{
			checkPIC ($console_per_product[$matches[1]]);
			commitAddPort ($objectInfo['id'], '', $console_per_product[$matches[1]], 'Console', '');
		}
		$exact_release = preg_replace ('/^.* revision ([^ ]+), .*$/', '\\1', $sysDescr);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		showOneLiner (81, array ('procurve-generic'));
		break;
	case preg_match ('/^4526\.100\.2\./', $sysObjectID): // NETGEAR
		checkPIC ('1-681');
		commitAddPort ($objectInfo['id'], 'console', '1-681', 'console', ''); // DB-9 RS-232 console
		showOneLiner (81, array ('netgear-generic'));
		break;
	case preg_match ('/^2011\.2\.23\./', $sysObjectID): // Huawei
		detectSoftwareType ($objectInfo, $sysDescr);
		checkPIC ('1-681');
		commitAddPort ($objectInfo['id'], 'con0', '1-681', 'console', ''); // DB-9 RS-232 console
		showOneLiner (81, array ('huawei-generic'));
		break;
	case '2636.1.1.1.2.31' == $sysObjectID: // Juniper EX4200
		detectSoftwareType ($objectInfo, $sysDescr);
		checkPIC ('1-29');
		commitAddPort ($objectInfo['id'], 'con', '1-29', 'CON', ''); // RJ-45 RS-232 console
		// EX4200-24T is already in DB
		if (preg_match ('/^Juniper Networks, Inc. ex4200-48t internet router/', $sysDescr))
			updateStickerForCell ($objectInfo, 2, 907);
		showOneLiner (81, array ('juniper-ex'));
		break;
	case preg_match ('/^2636\.1\.1\.1\.2\./', $sysObjectID): // Juniper
		detectSoftwareType ($objectInfo, $sysDescr);
		checkPIC ('1-681');
		commitAddPort ($objectInfo['id'], 'console', '1-681', 'console', ''); // DB-9 RS-232 console
		showOneLiner (81, array ('juniper-generic'));
		break;
	case preg_match ('/^1991\.1\.3\.45\./', $sysObjectID): // snFGSFamily
	case preg_match ('/^1991\.1\.3\.46\./', $sysObjectID): // snFLSFamily
	case preg_match ('/^1991\.1\.3\.54\.2\.4\.1\.1$/', $sysObjectID): // FCX 648
		detectSoftwareType ($objectInfo, $sysDescr);
		$exact_release = preg_replace ('/^.*, IronWare Version ([^ ]+) .*$/', '\\1', $sysDescr);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		# FOUNDRY-SN-AGENT-MIB::snChasSerNum.0
		$sysChassi = $device->snmpget ('enterprises.1991.1.1.1.1.2.0');
		if ($sysChassi !== FALSE or $sysChassi !== NULL)
			updateStickerForCell ($objectInfo, 1, str_replace ('"', '', substr ($sysChassi, strlen ('STRING: '))));

		# Type of uplink module installed.
		# table: FOUNDRY-SN-AGENT-MIB::snAgentBrdMainBrdDescription
		# Possible part numbers are:
		# FGS-1XG1XGC (one fixed CX4 port)
		# FGS-2XGC (two fixed CX4 ports)
		# FGS-2XG (two XFP slots)
		# And for FLS result (which is not handled here) would be:
		# 1991.1.1.2.2.1.1.2.1 = STRING: "FLS-24G 24-port Management Module"
		# 1991.1.1.2.2.1.1.2.3 = STRING: "FLS-1XG 1-port 10G Module (1-XFP)"
		# 1991.1.1.2.2.1.1.2.4 = STRING: "FLS-1XG 1-port 10G Module (1-XFP)"
		# (assuming, that the device has 2 XFP modules in slots 3 and 4).

		foreach ($device->snmpwalkoid ('enterprises.1991.1.1.2.2.1.1.2') as $module_raw)
			if (preg_match ('/^STRING: "(FGS-1XG1XGC|FGS-2XGC) /i', $module_raw))
			{
				$iftable_processors['fgs-uplinks']['dict_key'] = '1-40'; // CX4
				break;
			}

		# AC inputs
		# table: FOUNDRY-SN-AGENT-MIB::snChasPwrSupplyDescription
		# "Power supply 1 "
		# "Power supply 2 "
		foreach ($device->snmpwalkoid ('enterprises.1991.1.1.1.2.1.1.2') as $PSU_raw)
		{
			$count = 0;
			$PSU_cooked = trim (preg_replace ('/^string: "(.+)"$/i', '\\1', $PSU_raw, 1, $count));
			if ($count)
			{
				checkPIC ('1-16');
				commitAddPort ($objectInfo['id'], $PSU_cooked, '1-16', '', '');
			}
		}
		# fixed console port
		checkPIC ('1-681');
		commitAddPort ($objectInfo['id'], 'console', '1-681', 'console', ''); // DB-9 RS-232 console
		showOneLiner (81, array ('brocade-generic'));
		break;
	case preg_match ('/^1916\.2\./', $sysObjectID): // Extreme Networks Summit
		$xos_release = preg_replace ('/^ExtremeXOS version ([[:digit:]]+)\..*$/', '\\1', $sysDescr);
		$xos_codes = array
		(
			'10' => 1350,
			'11' => 1351,
			'12' => 1352,
		);
		if (array_key_exists ($xos_release, $xos_codes))
			updateStickerForCell ($objectInfo, 4, $xos_codes[$xos_release]);
		checkPIC ('1-681');
		commitAddPort ($objectInfo['id'], 'console', '1-681', 'console', ''); // DB-9 RS-232
		checkPIC ('1-16');
		commitAddPort ($objectInfo['id'], 'AC-in', '1-16', '', '');
		showOneLiner (81, array ('summit-generic'));
		break;
	case preg_match ('/^6027\.1\./', $sysObjectID): # Force10
		commitAddPort ($objectInfo['id'], 'aux0', '1-29', 'RS-232', ''); // RJ-45 RS-232 console
		$m = array();
		if (preg_match ('/Force10 Application Software Version: ([\d\.]+)/', $sysDescr, $m))
		{
			updateStickerForCell ($objectInfo, 5, $m[1]);
			$ftos_release = preg_replace ('/^([678])\..+$/', '\\1', $m[1]);
			$ftos_codes = array
			(
				'6' => 1592,
				'7' => 1593,
				'8' => 1594,
			);
			if (array_key_exists ($ftos_release, $ftos_codes))
				updateStickerForCell ($objectInfo, 4, $ftos_codes[$ftos_release]);
		}
		# F10-S-SERIES-CHASSIS-MIB::chStackUnitSerialNumber.1
		$serialNo = $device->snmpget ('enterprises.6027.3.10.1.2.2.1.12.1');
		# F10-S-SERIES-CHASSIS-MIB::chSysPowerSupplyType.1.1
		if ($device->snmpget ('enterprises.6027.3.10.1.2.3.1.3.1.1') == 'INTEGER: 1')
		{
			checkPIC ('1-16');
			commitAddPort ($objectInfo['id'], 'PSU0', '1-16', 'PSU0', '');
		}
		# F10-S-SERIES-CHASSIS-MIB::chSysPowerSupplyType.1.2
		if ($device->snmpget ('enterprises.6027.3.10.1.2.3.1.3.1.2') == 'INTEGER: 1')
		{
			checkPIC ('1-16');
			commitAddPort ($objectInfo['id'], 'PSU1', '1-16', 'PSU1', '');
		}
		if (strlen ($serialNo))
			updateStickerForCell ($objectInfo, 1, str_replace ('"', '', substr ($serialNo, strlen ('STRING: '))));
		break;
	case preg_match ('/^202\.20\./', $sysObjectID): // SMC TigerSwitch
		checkPIC ('1-681');
		commitAddPort ($objectInfo['id'], 'console', '1-681', '', ''); // DB-9 RS-232
		checkPIC ('1-16');
		commitAddPort ($objectInfo['id'], 'AC-in', '1-16', '', '');
		$log = mergeLogs ($log, oneLiner (81, array ('smc-generic')));
		break;
	default: // Nortel...
		break;
	}
	$ifInfo = array();
	$tablename = 'ifDescr';
	foreach ($device->snmpwalkoid ($tablename) as $oid => $value)
	{
		$randomindex = preg_replace ("/^.*${tablename}\.(.+)\$/", '\\1', $oid);
		$value = trim (preg_replace ('/^.+: (.+)$/', '\\1', $value), '"');
		$ifInfo[$randomindex][$tablename] = $value;
	}
	$tablename = 'ifPhysAddress';
	foreach ($device->snmpwalkoid ($tablename) as $oid => $value)
	{
		$randomindex = preg_replace ("/^.*${tablename}\.(.+)\$/", '\\1', $oid);
		$value = trim ($value);
		// NET-SNMP may return MAC addresses in one of two (?) formats depending on
		// DISPLAY-HINT internal database. The best we can do about it is to accept both.
		// Bug originally reported by Walery Wysotsky against openSUSE 11.0.
		if (preg_match ('/^string: [0-9a-f]{1,2}(:[0-9a-f]{1,2}){5}/i', $value)) // STRING: x:yy:z:xx:y:zz
		{
			list ($dummy, $value) = explode (' ', $value);
			$addrbytes = explode (':', $value);
			foreach ($addrbytes as $bidx => $bytestr)
				if (strlen ($bytestr) == 1)
					$addrbytes[$bidx] = '0' . $bytestr;
		}
		elseif (preg_match ('/^hex-string:( [0-9a-f]{2}){6}/i', $value)) // Hex-STRING: xx yy zz xx yy zz
			$addrbytes = explode (' ', substr ($value, -17));
		elseif (preg_match ('/22[0-9a-f]{12}22$/', bin2hex ($value))) // STRING: "??????"
			$addrbytes = array (substr (bin2hex ($value), -14, 12));
		else
			continue; // martian format
		$ifInfo[$randomindex][$tablename] = implode ('', $addrbytes);
	}
	// process each interface only once regardless of how many processors we have to run
	foreach ($ifInfo as $iface)
		foreach ($known_switches[$sysObjectID]['processors'] as $processor_name)
		{
			$newname = preg_replace ($iftable_processors[$processor_name]['pattern'], $iftable_processors[$processor_name]['replacement'], $iface['ifDescr'], 1, $count);
			if ($newname === NULL)
			{
				showError ('PCRE pattern error, terminating');
				break 2;
			}
			if (!$count)
				continue; // try next processor on current port
			$newlabel = preg_replace ($iftable_processors[$processor_name]['pattern'], $iftable_processors[$processor_name]['label'], $iface['ifDescr'], 1, $count);
			checkPIC ($iftable_processors[$processor_name]['dict_key']);
			commitAddPort ($objectInfo['id'], $newname, $iftable_processors[$processor_name]['dict_key'], $newlabel, $iface['ifPhysAddress']);
			if (!$iftable_processors[$processor_name]['try_next_proc']) // done with this port
				continue 2;
		}
	foreach ($known_switches[$sysObjectID]['processors'] as $processor_name)
		showOneLiner (81, array ($processor_name));
	// No failure up to this point, thus leave current tab for the "Ports" one.
	return buildRedirectURL (NULL, 'ports');
}

function doPDUSNMPmining ($objectInfo, $hostname, $snmpsetup)
{
	global $known_APC_SKUs;
	$switch = new APCPowerSwitch ($hostname, $snmpsetup);
	if (FALSE !== ($dict_key = array_search ($switch->getHWModel(), $known_APC_SKUs)))
		updateStickerForCell ($objectInfo, 2, $dict_key);
	updateStickerForCell ($objectInfo, 1, $switch->getHWSerial());
	updateStickerForCell ($objectInfo, 3, $switch->getName());
	updateStickerForCell ($objectInfo, 5, $switch->getFWRev());
	checkPIC ('1-16');
	commitAddPort ($objectInfo['id'], 'input', '1-16', 'input', '');
	$portno = 1;
	foreach ($switch->getPorts() as $name => $port)
	{
		$label = mb_strlen ($port[0]) ? $port[0] : $portno;
		checkPIC ('1-1322');
		commitAddPort ($objectInfo['id'], $portno, '1-1322', $port[0], '');
		$portno++;
	}
	showSuccess ("Added ${portno} port(s)");
	return buildRedirectURL (NULL, 'ports');
}

// APC SNMP code by Russ Garrett
define('APC_STATUS_ON', 1);
define('APC_STATUS_OFF', 2);
define('APC_STATUS_REBOOT', 3);

class RTSNMPDevice {
    protected $snmp;

    function __construct($hostname, $snmpsetup) {
	if( isset($snmpsetup['community']) ) {
	    $this->snmp = new RTSNMPv2($hostname, $snmpsetup);
	}
	else {
	    $this->snmp = new RTSNMPv3($hostname, $snmpsetup);
	}

    }

    function getName() {
	return $this->getString('sysName.0');
    }
 
    function getDescription() {
	return $this->getString('sysDescr.0');
    }
    
    function snmpget($oid) {
	return $this->snmp->snmpget($oid);
    }
    
    function snmpwalkoid($oid) {
	return $this->snmp->snmpwalkoid($oid);
    }

    protected function snmpSet($oid, $type, $value) {
        return $this->snmp->snmpset($oid, $type, $value);
    }

    protected function getString($oid) {
	return trim(str_replace('STRING: ', '', $this->snmp->snmpget($oid)), '"');
    }
}

abstract class RTSNMP {
    protected $hostname;
    protected $snmpsetup;

    function __construct($hostname, $snmpsetup) {
	$this->hostname = $hostname;
	$this->snmpsetup = $snmpsetup;
    }
    
    abstract function snmpget($oid);
    abstract function snmpset($oid, $type, $value);
    abstract function snmpwalkoid($oid);
}

class RTSNMPv2 extends RTSNMP {
    function snmpget($oid) {
	return snmpget($this->hostname, $this->snmpsetup['community'], $oid);
    }

    function snmpset($oid, $type, $value) {
	return snmpset($this->hostname, $this->snmpsetup['community'], $oid, $type, $value);
    }
    
    function snmpwalkoid($oid) {
	return snmpwalkoid($this->hostname, $this->snmpsetup['community'], $oid);
    }
}

class RTSNMPv3 extends RTSNMP {
    function snmpget($oid) {
	return snmp3_get($this->hostname, $this->snmpsetup['sec_name'], $this->snmpsetup['sec_level'], $this->snmpsetup['auth_protocol'], $this->snmpsetup['auth_passphrase'], $this->snmpsetup['priv_protocol'], $this->snmpsetup['priv_passphrase'], $oid);
    }

    function snmpset($oid, $type, $value) {
	return snmp3_set($this->hostname, $this->snmpsetup['sec_name'], $this->snmpsetup['sec_level'], $this->snmpsetup['auth_protocol'], $this->snmpsetup['auth_passphrase'], $this->snmpsetup['priv_protocol'], $this->snmpsetup['priv_passphrase'], $oid, $type, $value);
    }
    
    function snmpwalkoid($oid) {
	return snmp3_real_walk($this->hostname, $this->snmpsetup['sec_name'], $this->snmpsetup['sec_level'], $this->snmpsetup['auth_protocol'], $this->snmpsetup['auth_passphrase'], $this->snmpsetup['priv_protocol'], $this->snmpsetup['priv_passphrase'], $oid);
    }
}

class APCPowerSwitch extends RTSNMPDevice {
    protected $snmpMib = 'SNMPv2-SMI::enterprises.318';

    function getPorts() {
        $data = $this->snmpwalk("{$this->snmpMib}.1.1.12.3.3.1.1.2");
        $status = $this->snmpwalk("{$this->snmpMib}.1.1.12.3.3.1.1.4");
        $out = array();
        foreach ($data as $id => $d) {
            $out[$id + 1] = array(trim(str_replace('STRING: ', '', $d), '"'), str_replace('INTEGER: ', '', $status[$id]));
        }
        return $out;
    }
    
    function getPortStatus($id) {
        return trim($this->snmpget("{$this->snmpMib}.1.1.12.3.3.1.1.4.$id"), 'INTEGER: ');
    }

    function getPortName($id) {
        return trim(str_replace('STRING: ', '', $this->snmpget("{$this->snmpMib}.1.1.12.3.3.1.1.2.$id")), '"');
    }

    function setPortName($id, $name) {
        return $this->snmpset("{$this->snmpMib}.1.1.4.5.2.1.3.$id", 's', $name);
    }

    function portOff($id) {
        return $this->snmpSet("{$this->snmpMib}.1.1.12.3.3.1.1.4.$id", 'i', APC_STATUS_OFF);
    }

    function portOn($id) {
        return $this->snmpSet("{$this->snmpMib}.1.1.12.3.3.1.1.4.$id", 'i', APC_STATUS_ON);
    }

    function portReboot($id) {
        return $this->snmpSet("{$this->snmpMib}.1.1.12.3.3.1.1.4.$id", 'i', APC_STATUS_REBOOT);
    }
	// rPDUIdentFirmwareRev.0 == .1.3.6.1.4.1.318.1.1.12.1.3.0 = STRING: "vN.N.N"
	function getFWRev()
	{
		return preg_replace ('/^STRING: "(.+)"$/', '\\1', $this->snmpget ("{$this->snmpMib}.1.1.12.1.3.0"));
	}
	// rPDUIdentSerialNumber.0 == .1.3.6.1.4.1.318.1.1.12.1.6.0 = STRING: "XXXXXXXXXXX"
	function getHWSerial()
	{
		return preg_replace ('/^STRING: "(.+)"$/', '\\1', $this->snmpget ("{$this->snmpMib}.1.1.12.1.6.0"));
	}
	// rPDUIdentModelNumber.0 == .1.3.6.1.4.1.318.1.1.12.1.5.0 = STRING: "APnnnn"
	function getHWModel()
	{
		return preg_replace ('/^STRING: "(.*)"$/', '\\1', $this->snmpget ("{$this->snmpMib}.1.1.12.1.5.0"));
	}
}

// Take address in the form XX:XX:XX:XX:XX:XX and return the next
// address in the same form.
function nextMACAddress ($addr)
{
	if ($addr == '')
		return '';
	$bytes = array();
	foreach (explode (':', $addr) as $hex)
		$bytes[] = hexdec ($hex);
	for ($i = 5; $i >= 0; $i--)
	{
		$bytes[$i] += 1;
		if ($bytes[$i] <= 255) // FF
			break; // no roll over
		$bytes[$i] = 0;
	}
	foreach (array_keys ($bytes) as $key)
		$bytes[$key] = sprintf ('%02X', $bytes[$key]);
	return implode (':', $bytes);
}

function generatePortsForCatModule ($object_id, $slotno = 1, $mtype = 'X6748', $mac_address = '')
{
	global $dbxlink;
	$mac_address = l2addressFromDatabase (l2addressForDatabase ($mac_address));
	switch ($mtype)
	{
	case 'WS-X6748-GE-TX':
		$dbxlink->beginTransaction();
		for ($i = 1; $i <= 48; $i++)
		{
			commitAddPort ($object_id, "gi${slotno}/${i}", '1-24', "slot ${slotno} port ${i}", $mac_address);
			$mac_address = nextMACAddress ($mac_address);
		}
		$dbxlink->commit();
		break;
	case 'WS-X6708-10GE':
		for ($i = 1; $i <= 8; $i++)
		{
			commitAddPort ($object_id, "te${slotno}/${i}", '6-1080', "slot ${slotno} port ${i}", $mac_address);
			$mac_address = nextMACAddress ($mac_address);
		}
		break;
	case 'WS-X6704-10GE':
		for ($i = 1; $i <= 4; $i++)
		{
			commitAddPort ($object_id, "te${slotno}/${i}", '5-1079', "slot ${slotno} port ${i}", $mac_address);
			$mac_address = nextMACAddress ($mac_address);
		}
		break;
	case 'VS-S720-10G':
		commitAddPort ($object_id, "gi${slotno}/1", '4-1077', "slot ${slotno} port 1", $mac_address);
		$mac_address = nextMACAddress ($mac_address);
		commitAddPort ($object_id, "gi${slotno}/2", '4-1077', "slot ${slotno} port 2", $mac_address);
		$mac_address = nextMACAddress ($mac_address);
		commitAddPort ($object_id, "gi${slotno}/3", '1-24',   "slot ${slotno} port 3", $mac_address);
		$mac_address = nextMACAddress ($mac_address);
		commitAddPort ($object_id, "te${slotno}/4", '6-1080', "slot ${slotno} port 4", $mac_address);
		$mac_address = nextMACAddress ($mac_address);
		commitAddPort ($object_id, "te${slotno}/5", '6-1080', "slot ${slotno} port 5", $mac_address);
		break;
	case '3750G-24TS':
		// MAC address of 1st port is the next one after switch's address
		$mac_address = nextMACAddress ($mac_address);
		for ($i = 1; $i <= 24; $i++)
		{
			commitAddPort ($object_id, "gi${slotno}/0/${i}", '1-24', "unit ${slotno} port ${i}", $mac_address);
			$mac_address = nextMACAddress ($mac_address);
		}
		for ($i = 25; $i <= 28; $i++)
		{
			commitAddPort ($object_id, "gi${slotno}/0/${i}", '4-1077', "unit ${slotno} port ${i}", $mac_address);
			$mac_address = nextMACAddress ($mac_address);
		}
		break;
	case '3750G-24T':
		$mac_address = nextMACAddress ($mac_address);
		for ($i = 1; $i <= 24; $i++)
		{
			commitAddPort ($object_id, "gi${slotno}/0/${i}", '1-24', "unit ${slotno} port ${i}", $mac_address);
			$mac_address = nextMACAddress ($mac_address);
		}
		break;
	case '3750G-16TD':
		$mac_address = nextMACAddress ($mac_address);
		for ($i = 1; $i <= 16; $i++)
		{
			commitAddPort ($object_id, "gi${slotno}/0/${i}", '1-24', "unit ${slotno} port ${i}", $mac_address);
			$mac_address = nextMACAddress ($mac_address);
		}
		commitAddPort ($object_id, "te${slotno}/0/1", '5-1079', "unit ${slotno} port ${i}", $mac_address);
		break;
	case 'LE02G48TA':
		for ($i = 0; $i <= 47; $i++)
			commitAddPort ($object_id, "gi${slotno}/0/${i}", '1-24', "slot ${slotno} port ${i}", $mac_address);
		break;
	case 'LE02X12SA':
		for ($i = 0; $i <= 11; $i++)
			commitAddPort ($object_id, "gi${slotno}/0/${i}", '9-1084', "slot ${slotno} port ${i}", $mac_address);
		break;
	}
}

function detectSoftwareType ($objectInfo, $sysDescr)
{
	global $swtype_pcre;
	foreach ($swtype_pcre as $pattern => $dict_key)
		if (preg_match ($pattern, $sysDescr))
		{
			updateStickerForCell ($objectInfo, 4, $dict_key);
			return;
		}
}
?>
