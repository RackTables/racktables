<?php

$iftable_processors = array();

$iftable_processors['catalyst-4948-mgmt'] = array
(
	'pattern' => '@^FastEthernet1$@',
	'replacement' => 'fa1',
	'dict_key' => 19,
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
	'dict_key' => 1083,
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

$iftable_processors['juniper-DPCE-R-4XGE-XFP'] = array
(
	'pattern' => '@^xe-([[:digit:]]+)/([[:digit:]]+/[[:digit:]]+)$@',
	'replacement' => '\\0',
	'dict_key' => '8-1082', // XFP/empty
	'label' => 'slot \\1 port \\2',
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
	'pattern' => '@^GigabitEthernet1/1/([[:digit:]]+)$@',
	'replacement' => 'e1/1/\\1',
	'dict_key' => '1-24',
	'label' => '\\1',
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
	'9.1.695' => array
	(
		'dict_key' => 140,
		'text' => 'WS-C2960-48TC-L: 48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
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
		'dict_key' => 162,
		'text' => 'WS-C2960-48TT-L: 48 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
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
		'processors' => array ('catalyst-chassis-49-to-52-1000SFP', 'catalyst-chassis-uplinks-1000T', 'catalyst-4948-mgmt'),
	),
	'9.1.659' => array
	(
		'dict_key' => 377,
		'text' => 'WS-C4948-10GE: 48 RJ-45/10-100-1000T(X) + 2 X2/10000 + 1 RJ-45/100TX (OOB mgmt)',
		'processors' => array ('catalyst-chassis-uplinks-10000X2', 'catalyst-chassis-uplinks-1000T', 'catalyst-4948-mgmt'),
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
	'11.2.3.7.11.87' => array
	(
		'dict_key' => 1349,
		'text' => 'J9147A: 44 RJ-45/10-100-1000T(X) + 4 combo-gig)',
		'processors' => array ('procurve-45-to-48-combo-1000SFP', 'procurve-chassis-1000T'),
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
	'2011.2.23.96' => array
	(
		'dict_key' => 1321,
		'text' => 'S5328C-EI-24S: 20 SFP-1000 + 4 combo-gig + 2 XFP slots',
		'processors' => array ('quidway-21-to-24-comboT', 'quidway-any-1000SFP', 'quidway-XFP', 'quidway-mgmt'),
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
function doSNMPmining ($object_id, $community)
{
	$objectInfo = spotEntity ('object', $object_id);
	$objectInfo['attrs'] = getAttrValues ($object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		return buildRedirectURL (__FUNCTION__, 'ERR1'); // endpoint not found
	if (count ($endpoints) > 1)
		return buildRedirectURL (__FUNCTION__, 'ERR2'); // can't pick an address

	switch ($objectInfo['objtype_id'])
	{
	case 7:
	case 8:
		return doSwitchSNMPmining ($objectInfo, $endpoints[0], $community);
	case 2:
		return doPDUSNMPmining ($objectInfo, $endpoints[0], $community);
	}	
}

$msgcode['doSwitchSNMPmining']['ERR3'] = 188;
$msgcode['doSwitchSNMPmining']['ERR4'] = 189;
function doSwitchSNMPmining ($objectInfo, $hostname, $community)
{
	$log = emptyLog();
	global $known_switches, $iftable_processors;
	
	if (FALSE === ($sysObjectID = @snmpget ($hostname, $community, 'sysObjectID.0')))
		return buildRedirectURL (__FUNCTION__, 'ERR3'); // // fatal SNMP failure
	$sysObjectID = preg_replace ('/^.*(enterprises\.)([\.[:digit:]]+)$/', '\\2', $sysObjectID);
	$sysName = substr (@snmpget ($hostname, $community, 'sysName.0'), strlen ('STRING: '));
	$sysDescr = substr (@snmpget ($hostname, $community, 'sysDescr.0'), strlen ('STRING: '));
	$sysDescr = str_replace (array ("\n", "\r"), " ", $sysDescr);  // Make it one line
	if (!isset ($known_switches[$sysObjectID]))
		return buildRedirectURL (__FUNCTION__, 'ERR4', array ($sysObjectID)); // unknown OID
	foreach (array_keys ($known_switches[$sysObjectID]['processors']) as $pkey)
		if (!array_key_exists ($known_switches[$sysObjectID]['processors'][$pkey], $iftable_processors))
		{
			$log = mergeLogs ($log, oneLiner (200, array ('processor "' . $known_switches[$sysObjectID]['processors'][$pkey] . '" not found')));
			unset ($known_switches[$sysObjectID]['processors'][$pkey]);
		}
	updateStickerForCell ($objectInfo, 2, $known_switches[$sysObjectID]['dict_key']);
	updateStickerForCell ($objectInfo, 3, $sysName);
	$log = mergeLogs ($log, oneLiner (81, array ('generic')));
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
		$sysChassi = @snmpget ($hostname, $community, '1.3.6.1.4.1.9.3.6.3.0');
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
		$log = mergeLogs ($log, oneLiner (81, array ('catalyst-generic')));
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
		$log = mergeLogs ($log, oneLiner (81, array ('nexus-generic')));
		break;
	case preg_match ('/^11\.2\.3\.7\.11\./', $sysObjectID): // ProCurve
		$exact_release = preg_replace ('/^.* revision ([^ ]+), .*$/', '\\1', $sysDescr);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		$log = mergeLogs ($log, oneLiner (81, array ('procurve-generic')));
		break;
	case preg_match ('/^4526\.100\.2\./', $sysObjectID): // NETGEAR
		checkPIC ('1-681');
		commitAddPort ($objectInfo['id'], 'console', '1-681', 'console', ''); // DB-9 RS-232 console
		$log = mergeLogs ($log, oneLiner (81, array ('netgear-generic')));
		break;
	case preg_match ('/^2011\.2\.23\./', $sysObjectID): // Huawei
		checkPIC ('1-681');
		commitAddPort ($objectInfo['id'], 'console', '1-681', 'console', ''); // DB-9 RS-232 console
		$log = mergeLogs ($log, oneLiner (81, array ('huawei-generic')));
		break;
	case preg_match ('/^2636\.1\.1\.1\.2\./', $sysObjectID): // Juniper
		checkPIC ('1-681');
		commitAddPort ($objectInfo['id'], 'console', '1-681', 'console', ''); // DB-9 RS-232 console
		$log = mergeLogs ($log, oneLiner (81, array ('juniper-generic')));
		break;
	case preg_match ('/^1991\.1\.3\.45\./', $sysObjectID): // snFGSFamily
		$exact_release = preg_replace ('/^.*, IronWare Version ([^ ]+) .*$/', '\\1', $sysDescr);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		# FOUNDRY-SN-AGENT-MIB::snChasSerNum.0
		$sysChassi = @snmpget ($hostname, $community, 'enterprises.1991.1.1.1.1.2.0');
		if ($sysChassi !== FALSE or $sysChassi !== NULL)
			updateStickerForCell ($objectInfo, 1, str_replace ('"', '', substr ($sysChassi, strlen ('STRING: '))));

		# Type of uplink module installed.
		# table: FOUNDRY-SN-AGENT-MIB::snAgentBrdMainBrdDescription
		# Possible part numbers are:
		# FGS-1XG1XGC (one fixed CX4 port)
		# FGS-2XGC (two fixed CX4 ports)
		# FGS-2XG (two XFP slots)
		foreach (@snmpwalkoid ($hostname, $community, 'enterprises.1991.1.1.2.2.1.1.2') as $module_raw)
			if (preg_match ('/^STRING: "(FGS-1XG1XGC|FGS-2XGC) /i', $module_raw))
			{
				$iftable_processors['fgs-uplinks']['dict_key'] = '1-40'; // CX4
				break;
			}

		# AC inputs
		# table: FOUNDRY-SN-AGENT-MIB::snChasPwrSupplyDescription
		# "Power supply 1 "
		# "Power supply 2 "
		foreach (@snmpwalkoid ($hostname, $community, 'enterprises.1991.1.1.1.2.1.1.2') as $PSU_raw)
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
		$log = mergeLogs ($log, oneLiner (81, array ('brocade-generic')));
		break;
	default: // Nortel...
		break;
	}
	$ifInfo = array();
	$tablename = 'ifDescr';
	foreach (snmpwalkoid ($hostname, $community, $tablename) as $oid => $value)
	{
		$randomindex = preg_replace ("/^.*${tablename}\.(.+)\$/", '\\1', $oid);
		$value = trim (preg_replace ('/^.+: (.+)$/', '\\1', $value), '"');
		$ifInfo[$randomindex][$tablename] = $value;
	}
	$tablename = 'ifPhysAddress';
	foreach (snmpwalkoid ($hostname, $community, $tablename) as $oid => $value)
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
			if (!$count)
				continue; // try next processor on current port
			$newlabel = preg_replace ($iftable_processors[$processor_name]['pattern'], $iftable_processors[$processor_name]['label'], $iface['ifDescr'], 1, $count);
			checkPIC ($iftable_processors[$processor_name]['dict_key']);
			commitAddPort ($objectInfo['id'], $newname, $iftable_processors[$processor_name]['dict_key'], $newlabel, $iface['ifPhysAddress']);
			if (!$iftable_processors[$processor_name]['try_next_proc']) // done with this port
				continue 2;
		}
	foreach ($known_switches[$sysObjectID]['processors'] as $processor_name)
		$log = mergeLogs ($log, oneLiner (81, array ($processor_name)));
	// No failure up to this point, thus leave current tab for the "Ports" one.
	return buildWideRedirectURL ($log, NULL, 'ports');
}

$msgcode['doPDUSNMPmining']['OK'] = 0;
function doPDUSNMPmining ($objectInfo, $hostname, $community)
{
	$log = emptyLog();
	global $known_APC_SKUs;
	$switch = new APCPowerSwitch ($hostname, $community);
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
	$log = mergeLogs ($log, oneLiner (0, array ("Added ${portno} port(s)")));
	return buildWideRedirectURL ($log, NULL, 'ports');
}

// APC SNMP code by Russ Garrett
define('APC_STATUS_ON', 1);
define('APC_STATUS_OFF', 2);
define('APC_STATUS_REBOOT', 3);

class SNMPDevice {
    protected $hostname;
    protected $community;

    function __construct($hostname, $community) {
        $this->hostname = $hostname;
        $this->community = $community;
    }

    function getName() {
	return $this->getString('sysName.0');
    }
 
    function getDescription() {
	return $this->getString('sysDescr.0');
    }

    protected function snmpSet($oid, $type, $value) {
        return snmpset($this->hostname, $this->community, $oid, $type, $value);
    }

    protected function getString($oid) {
	return trim(str_replace('STRING: ', '', snmpget($this->hostname, $this->community, $oid)), '"');
    }
}

class APCPowerSwitch extends SNMPDevice {
    protected $snmpMib = 'SNMPv2-SMI::enterprises.318';

    function getPorts() {
        $data = snmpwalk($this->hostname, $this->community, "{$this->snmpMib}.1.1.12.3.3.1.1.2");
        $status = snmpwalk($this->hostname, $this->community, "{$this->snmpMib}.1.1.12.3.3.1.1.4");
        $out = array();
        foreach ($data as $id => $d) {
            $out[$id + 1] = array(trim(str_replace('STRING: ', '', $d), '"'), str_replace('INTEGER: ', '', $status[$id]));
        }
        return $out;
    }
    
    function getPortStatus($id) {
        return trim(snmpget($this->hostname, $this->community, "{$this->snmpMib}.1.1.12.3.3.1.1.4.$id"), 'INTEGER: ');
    }

    function getPortName($id) {
        return trim(str_replace('STRING: ', '', snmpget($this->hostname, $this->community, "{$this->snmpMib}.1.1.12.3.3.1.1.2.$id")), '"');
    }

    function setPortName($id, $name) {
        return snmpset($this->hostname, $this->community, "{$this->snmpMib}.1.1.4.5.2.1.3.$id", 's', $name);
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
		return preg_replace ('/^STRING: "(.+)"$/', '\\1', snmpget ($this->hostname, $this->community, "{$this->snmpMib}.1.1.12.1.3.0"));
	}
	// rPDUIdentSerialNumber.0 == .1.3.6.1.4.1.318.1.1.12.1.6.0 = STRING: "XXXXXXXXXXX"
	function getHWSerial()
	{
		return preg_replace ('/^STRING: "(.+)"$/', '\\1', snmpget ($this->hostname, $this->community, "{$this->snmpMib}.1.1.12.1.6.0"));
	}
	// rPDUIdentModelNumber.0 == .1.3.6.1.4.1.318.1.1.12.1.5.0 = STRING: "APnnnn"
	function getHWModel()
	{
		return preg_replace ('/^STRING: "(.*)"$/', '\\1', snmpget ($this->hostname, $this->community, "{$this->snmpMib}.1.1.12.1.5.0"));
	}
}

?>
