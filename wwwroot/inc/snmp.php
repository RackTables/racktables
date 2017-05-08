<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

global $iftable_processors;
$iftable_processors = array();

$iftable_processors['generic-e-any-100TX'] = array
(
	'pattern' => '@^e(\d+)$@',
	'replacement' => 'e\\1',
	'dict_key' => 19,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-fa-any-100TX'] = array
(
	'pattern' => '@^fa(\d+)$@',
	'replacement' => 'fa\\1',
	'dict_key' => 19,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-g12-and-g24-combo-1000SFP'] = array
(
	'pattern' => '@^g(12|24)$@',
	'replacement' => 'g\\1',
	'dict_key' => '4-1077',
	'label' => 'g\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-g45-to-g48-combo-1000SFP'] = array
(
	'pattern' => '@^g(45|46|47|48)$@',
	'replacement' => 'g\\1',
	'dict_key' => '4-1077',
	'label' => 'g\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-g-any-1000T'] = array
(
	'pattern' => '@^g(\d+)$@',
	'replacement' => 'g\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-g-1-to-2-1000T'] = array
(
	'pattern' => '@^g(1|2)$@',
	'replacement' => 'g\\1',
	'dict_key' => 24,
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-g-3-to-4-combo-1000SFP'] = array
(
	'pattern' => '@^g(3|4)$@',
	'replacement' => 'g\\1',
	'dict_key' => '4-1077',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-g-3-to-4-combo-1000T'] = array
(
	'pattern' => '@^g(3|4)$@',
	'replacement' => 'g\\1',
	'dict_key' => '1-24',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-gi-any-1000T'] = array
(
	'pattern' => '@^gi(\d+)$@',
	'replacement' => 'gi\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-gi-1-to-2-1000T'] = array
(
	'pattern' => '@^gi(\d+)$@',
	'replacement' => 'gi\\1',
	'dict_key' => 24,
	'label' => 'G\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-gi-1-to-2-combo-1000SFP'] = array
(
	'pattern' => '@^gi(1|2)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-gi-3-to-4-combo-1000SFP'] = array
(
	'pattern' => '@^gi(3|4)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-gi-3-to-4-combo-1000T'] = array
(
	'pattern' => '@^gi(3|4)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '1-24',
	'label' => 'G\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-gi-9-to-10-combo-1000SFP'] = array
(
	'pattern' => '@^gi(9|10)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-gi-9-to-10-combo-1000T'] = array
(
	'pattern' => '@^gi(9|10)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '1-24',
	'label' => 'G\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-gi-17-to-18-combo-1000SFP'] = array
(
	'pattern' => '@^gi(17|18)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-gi-17-to-18-combo-1000T'] = array
(
	'pattern' => '@^gi(17|18)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '1-24',
	'label' => 'G\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-gi-19-to-20-combo-1000SFP'] = array
(
	'pattern' => '@^gi(19|20)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-gi-19-to-20-combo-1000T'] = array
(
	'pattern' => '@^gi(19|20)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '1-24',
	'label' => 'G\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-gi-25-to-26-combo-1000SFP'] = array
(
	'pattern' => '@^gi(25|26)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-gi-25-to-26-combo-1000T'] = array
(
	'pattern' => '@^gi(25|26)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '1-24',
	'label' => 'G\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-gi-27-to-28-combo-1000SFP'] = array
(
	'pattern' => '@^gi(27|28)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-gi-27-to-28-combo-1000T'] = array
(
	'pattern' => '@^gi(27|28)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '1-24',
	'label' => 'G\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-gi-49-to-50-combo-1000SFP'] = array
(
	'pattern' => '@^gi(49|50)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-gi-49-to-50-combo-1000T'] = array
(
	'pattern' => '@^gi(49|50)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '1-24',
	'label' => 'G\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-gi-51-to-52-combo-1000SFP'] = array
(
	'pattern' => '@^gi(51|52)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['generic-gi-51-to-52-combo-1000T'] = array
(
	'pattern' => '@^gi(51|52)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '1-24',
	'label' => 'G\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-port-any-1000T'] = array
(
	'pattern' => '@^port([[:digit:]]+)$@',
	'replacement' => '\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-any-1000T'] = array
(
	'pattern' => '@^([[:digit:]]+)$@',
	'replacement' => '\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['generic-21-to-24-combo-1000SFP'] = array
(
	'pattern' => '@^(21|22|23|24)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['cisco-smb-any-100TX'] = array
(
	'pattern' => '@^FastEthernet(\d+)$@',
	'replacement' => 'fa\\1',
	'dict_key' => 19,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['cisco-smb-any-1000T'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)$@',
	'replacement' => 'gi\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['cisco-smb-1-to-2-combo-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet(1|2)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['cisco-smb-1-to-2-combo-1000T'] = array
(
	'pattern' => '@^GigabitEthernet(1|2)$@',
	'replacement' => 'gi\\1',
	'dict_key' => 24,
	'label' => 'G\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['cisco-smb-25-to-26-combo-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet(25|26)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['cisco-smb-25-to-26-combo-1000T'] = array
(
	'pattern' => '@^GigabitEthernet(25|26)$@',
	'replacement' => 'gi\\1',
	'dict_key' => 24,
	'label' => 'G\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['cisco-smb-49-to-50-combo-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet(49|50)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => 'G\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['cisco-smb-49-to-50-combo-1000T'] = array
(
	'pattern' => '@^GigabitEthernet(49|50)$@',
	'replacement' => 'gi\\1',
	'dict_key' => 24,
	'label' => 'G\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-any-10TX'] = array
(
	'pattern' => '@^([[:digit:]]+)$@',
	'replacement' => '\\1',
	'dict_key' => 18,
	'label' => '\\1x',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-any-100TX'] = array
(
	'pattern' => '@^FastEthernet(\d+)$@',
	'replacement' => 'fa\\1',
	'dict_key' => 19,
	'label' => 'fa\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-a-to-b-100TX'] = array
(
	'pattern' => '@^(A|B)$@',
	'replacement' => '\\1',
	'dict_key' => 19,
	'label' => '\\1x',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-any-1000T'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)$@',
	'replacement' => 'gi\\1',
	'dict_key' => 24,
	'label' => 'gi\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-9-to-12-combo-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet(9|10|11|12)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => 'gi\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['catalyst-9-to-12-1000T'] = array
(
	'pattern' => '@^GigabitEthernet(9|10|11|12)$@',
	'replacement' => 'gi\\1',
	'dict_key' => 24,
	'label' => 'gi\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-mgmt'] = array
(
	'pattern' => '@^FastEthernet([[:digit:]])$@',
	'replacement' => 'fa\\1',
	'dict_key' => '1-19',
	'label' => 'mgmt',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-25-to-26-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(25|26)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077',
	'label' => '\\2X',
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

$iftable_processors['catalyst-chassis-1-to-2-combo-1000T'] = array (
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(1|2)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => 24,
	'label' => '\\2',
	'try_next_proc' => TRUE,
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

$iftable_processors['catalyst-chassis-uplinks-10000SFP+'] = array
(
	'pattern' => '@^TenGigabitEthernet([[:digit:]]+/)?([[:digit:]]+)$@',
	'replacement' => 'te\\1\\2',
	'dict_key' => '9-1084',
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

$iftable_processors['catalyst-9-to-10-combo-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(9|10)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => '4-1077',
	'label' => '\\2',
	'try_next_proc' => TRUE,
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

$iftable_processors['catalyst-stack-any-1000T'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)/(\d+)/(\d+)$@',
	'replacement' => 'gi\\1/\\2/\\3',
	'dict_key' => '1-24',
	'label' => 'unit \\1 port \\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-stack-any-100TX'] = array
(
	'pattern' => '@^FastEthernet(\d+)/(\d+)/(\d+)$@',
	'replacement' => 'fa\\1/\\2/\\3',
	'dict_key' => 19,
	'label' => 'unit \\1 port \\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-stack-25-to-28-SFP'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)/(\d+)/(25|26|27|28)$@',
	'replacement' => 'gi\\1/\\2/\\3',
	'dict_key' => '4-1077',
	'label' => 'unit \\1 port \\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-stack-49-to-52-SFP'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)/(\d+)/(49|50|51|52)$@',
	'replacement' => 'gi\\1/\\2/\\3',
	'dict_key' => '4-1077',
	'label' => 'unit \\1 port \\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-stack-1-to-4-SFP'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)/(\d+)/([1-4])$@',
	'replacement' => 'gi\\1/\\2/\\3',
	'dict_key' => '4-1077',
	'label' => 'unit \\1 port \\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-stack-uplinks-10000SFP+'] = array
(
	'pattern' => '@^TenGigabitEthernet(\d+)/(\d+)/([1-4])$@',
	'replacement' => 'te\\1/\\2/\\3',
	'dict_key' => '9-1084',
	'label' => 'unit \\1 port \\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-2948-49-to-50-SFP'] = array
(
	'pattern' => '@^port 2/(49|50)$@',
	'replacement' => 'gi\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-2948-any-100TX'] = array
(
	'pattern' => '@^port 2/(\d+)$@',
	'replacement' => 'fa\\1',
	'dict_key' => 19,
	'label' => '\\1',
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


$iftable_processors['nexus-any-QSFP-split'] = array
(
	'pattern' => '@^Ethernet(([[:digit:]]+)/([[:digit:]]+)/([[:digit:]]+))$@',
	'replacement' => 'e\\1',
	'dict_key' => '9-1084',
	'label' => '\\2/\\3:\\4',
	'try_next_proc' => FALSE,
);

$iftable_processors['nexus-any-QSFP+'] = array
(
	'pattern' => '@^Ethernet([[:digit:]]/[[:digit:]]+)$@',
	'replacement' => 'e\\1',
	'dict_key' => '10-1588',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['nexus-3048-1000TX'] = array
(
	'pattern' => '@^Ethernet([[:digit:]]/[[:digit:]]+)$@',
	'replacement' => 'e\\1',
	'dict_key' => '24', // From database - check wwwroot/inc/install.php
	'label' => '\\1',
	'try_next_proc' => FALSE,
);
$iftable_processors['nexus-3048-49-to-52-1000SFP'] = array
(
	'pattern' => '@^Ethernet([[:digit:]]+/)?(49|50|51|52)$@',
	'replacement' => 'e\\1\\2',
	'dict_key' => '9-1084',
	'label' => '\\1\\2',
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

$iftable_processors['procurve-23-to-24-combo-1000SFP'] = array
(
	'pattern' => '@^(23|24)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['procurve-25-to-26-combo-1000SFP'] = array
(
	'pattern' => '@^(25|26)$@',
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

$iftable_processors['procurve-modular-1000T'] = array
(
	'pattern' => '@^([A-Z][[:digit:]]+)$@',
	'replacement' => '\\1',
	'dict_key' => 24,
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

$iftable_processors['procurve-9-to-10-combo-1000SFP'] = array
(
	'pattern' => '@^(9|10)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['procurve-9-to-10-1000T'] = array
(
	'pattern' => '@^(9|10)$@',
	'replacement' => '\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['procurve-27-to-28-1000T'] = array
(
	'pattern' => '@^(27|28)$@',
	'replacement' => '\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['procurve-27-to-28-combo-1000SFP'] = array
(
	'pattern' => '@^(27|28)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['procurve-27-to-28-1000SFP'] = array
(
	'pattern' => '@^(27|28)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
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

$iftable_processors['procurve-49-to-52-10000SFP+'] = array
(
	'pattern' => '@^(49|50|51|52)$@',
	'replacement' => '\\1',
	'dict_key' => '9-1084',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['gbe2csfp-1-to-16'] = array
(
	'pattern' => '@^Downlink(\d+)$@',
	'replacement' => '\\0',
	'dict_key' => '1-1603',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['gbe2csfp-17'] = array
(
	'pattern' => '@^Xconnect1$@',
	'replacement' => '\\0',
	'dict_key' => '1-1603',
	'label' => '17',
	'try_next_proc' => FALSE,
);

$iftable_processors['gbe2csfp-18'] = array
(
	'pattern' => '@^Xconnect2$@',
	'replacement' => '\\0',
	'dict_key' => '1-1603',
	'label' => '18',
	'try_next_proc' => FALSE,
);

$iftable_processors['gbe2csfp-19'] = array
(
	'pattern' => '@^Mgmt$@',
	'replacement' => '\\0',
	'dict_key' => '1-1604',
	'label' => '19',
	'try_next_proc' => FALSE,
);

$iftable_processors['gbe2csfp-20'] = array
(
	'pattern' => '@^Uplink1$@',
	'replacement' => '\\0',
	'dict_key' => '1-24',
	'label' => '20',
	'try_next_proc' => FALSE,
);

$iftable_processors['gbe2csfp-21sfp'] = array
(
	'pattern' => '@^Uplink2$@',
	'replacement' => '\\0',
	'dict_key' => '4-1077',
	'label' => '21',
	'try_next_proc' => TRUE,
);

$iftable_processors['gbe2csfp-21'] = array
(
	'pattern' => '@^Uplink2$@',
	'replacement' => '\\0',
	'dict_key' => '1-24',
	'label' => '21',
	'try_next_proc' => FALSE,
);

$iftable_processors['gbe2csfp-22sfp'] = array
(
	'pattern' => '@^Uplink3$@',
	'replacement' => '\\0',
	'dict_key' => '4-1077',
	'label' => '22',
	'try_next_proc' => TRUE,
);

$iftable_processors['gbe2csfp-22'] = array
(
	'pattern' => '@^Uplink3$@',
	'replacement' => '\\0',
	'dict_key' => '1-24',
	'label' => '22',
	'try_next_proc' => FALSE,
);

$iftable_processors['gbe2csfp-23sfp'] = array
(
	'pattern' => '@^Uplink4$@',
	'replacement' => '\\0',
	'dict_key' => '4-1077',
	'label' => '23',
	'try_next_proc' => TRUE,
);

$iftable_processors['gbe2csfp-23'] = array
(
	'pattern' => '@^Uplink4$@',
	'replacement' => '\\0',
	'dict_key' => '1-24',
	'label' => '23',
	'try_next_proc' => FALSE,
);

$iftable_processors['gbe2csfp-24sfp'] = array
(
	'pattern' => '@^Uplink5$@',
	'replacement' => '\\0',
	'dict_key' => '4-1077',
	'label' => '24',
	'try_next_proc' => TRUE,
);

$iftable_processors['gbe2csfp-24'] = array
(
	'pattern' => '@^Uplink5$@',
	'replacement' => '\\0',
	'dict_key' => '1-24',
	'label' => '24',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-fs726tp-100TX'] = array
(
	'pattern' => '@^Port (\d+): Fast Ethernet$@',
	'replacement' => '\\1',
	'dict_key' => 19,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-fs726tp-1000SFPcombo'] = array
(
	'pattern' => '@^Port (\d+): Gigabit Ethernet$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['netgear-fs726tp-1000T'] = array
(
	'pattern' => '@^Port (\d+): Gigabit Ethernet$@',
	'replacement' => '\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-23-to-24-1000SPFcombo'] = array
(
	'pattern' => '@^g(\d+)$@',
	'replacement' => 'g\\1',
	'dict_key' => '4-1077',
	'label' => 'g\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['netgear-any-1000T'] = array
(
	'pattern' => '@^g(\d+)$@',
	'replacement' => 'g\\1',
	'dict_key' => 24,
	'label' => 'g\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-chassis-any-1000T'] = array
(
	'pattern' => '@^Unit: (\d+) Slot: (\d+) Port: (\d+) Gigabit - Level$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => 24,
	'label' => '\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-chassis-any-1000SFP'] = array
(
	'pattern' => '@^Unit: (\d+) Slot: (\d+) Port: (\d+) Gigabit - Level$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => '4-1077',
	'label' => '\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-chassis-21-to-24-1000Tcombo'] = array
(
	'pattern' => '@^Unit: (\d+) Slot: (\d+) Port: (21|22|23|24) Gigabit - Level$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => 24,
	'label' => '\\3T',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-chassis-21-to-24-1000SFP'] = array
(
	'pattern' => '@^Unit: (\d+) Slot: (\d+) Port: (21|22|23|24) Gigabit - Level$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => '4-1077',
	'label' => '\\3F',
	'try_next_proc' => TRUE,
);

$iftable_processors['netgear-chassis-45-to-48-1000SFPcombo'] = array
(
	'pattern' => '@^Unit: (\d+) Slot: (\d+) Port: (45|46|47|48) Gigabit - Level$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => '4-1077',
	'label' => '\\3F',
	'try_next_proc' => TRUE,
);

$iftable_processors['netgear-chassis-any-1000SFPcombo'] = array
(
	'pattern' => '@^Unit: (\d+) Slot: (\d+) Port: (\d+) Gigabit - Level$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => '4-1077',
	'label' => '\\3F',
	'try_next_proc' => TRUE,
);

$iftable_processors['netgear-chassis-any-100TX'] = array
(
	'pattern' => '@^Unit: (\d+) Slot: (\d+) Port: (\d+) 10/100 Copper - Level$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => 19,
	'label' => '\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-chassis-any-SFP+'] = array
(
	'pattern' => '@^Unit: (\d+) Slot: (\d+) Port: (\d+) 10G - Level$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => '9-1084',
	'label' => '\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-49-to-50-combo-1000SFP'] = array
(
	'pattern' => '@^GbE_(49|50)$@',
	'replacement' => '\\1',
	'dict_key' => '4-1077',
	'label' => '\\1F',
	'try_next_proc' => TRUE,
);

$iftable_processors['netgear-49-to-50-combo-1000T'] = array
(
	'pattern' => '@^GbE_(49|50)$@',
	'replacement' => '\\1',
	'dict_key' => '1-24',
	'label' => '\\1T',
	'try_next_proc' => FALSE,
);

$iftable_processors['netgear-any-100TX'] = array
(
	'pattern' => '@^FE_(\d+)$@',
	'replacement' => '\\1',
	'dict_key' => 19,
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

$iftable_processors['quidway-any-100TX'] = array
(
	'pattern' => '@^Ethernet([[:digit:]]+/[[:digit:]]+/)([[:digit:]]+)$@',
	'replacement' => 'ether\\1\\2',
	'dict_key' => '1-19',
	'label' => '\\2',
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

$iftable_processors['hce-any-1000T'] = array
(
	'pattern' => '@^GE([[:digit:]]+/[[:digit:]]+/)([[:digit:]]+)$@',
	'replacement' => 'ge\\1\\2',
	'dict_key' => '1-24',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['hce-any-SFP'] = array
(
	'pattern' => '@^10GE([[:digit:]]+/[[:digit:]]+/)([[:digit:]]+)$@',
	'replacement' => '10ge\\1\\2',
	'dict_key' => '9-1084',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['hce-any-QSFP-split'] = array
(
	'pattern' => '@^40GE([[:digit:]]+/[[:digit:]]+/)([[:digit:]]+):([[:digit:]]+)$@',
	'replacement' => '40ge\\1\\2:\\3',
	'dict_key' => '9-1084',
	'label' => '\\2:\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['hce-any-QSFP'] = array
(
	'pattern' => '@^40GE([[:digit:]]+/[[:digit:]]+/)([[:digit:]]+)$@',
	'replacement' => '40ge\\1\\2',
	'dict_key' => '10-1588',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

// FIXME: use SFP28:25GbE instead of SFP+:10GbE
$iftable_processors['hce-any-QSFP28-split'] = array
(
	'pattern' => '@^100GE([[:digit:]]+/[[:digit:]]+/)([[:digit:]]+):([[:digit:]]+)$@',
	'replacement' => '100ge\\1\\2:\\3',
	'dict_key' => '9-1084',
	'label' => '\\2:\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['hce-any-QSFP28'] = array
(
	'pattern' => '@^100GE([[:digit:]]+/[[:digit:]]+/)([[:digit:]]+)$@',
	'replacement' => '100ge\\1\\2',
	'dict_key' => '15-1588',
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

$iftable_processors['turboiron-any-10000SFP+'] = array
(
	'pattern' => '@^10GigabitEthernet([[:digit:]]+)$@',
	'replacement' => 'e\\1',
	'dict_key' => '4-1077',
	'label' => 'Slot \\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['turboiron-any-10-100-1000T'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+)$@',
	'replacement' => 'e\\1',
	'dict_key' => '1-24',
	'label' => 'Slot \\1',
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

$iftable_processors['arista-any-1000T'] = array
(
	'pattern' => '@^Ethernet([[:digit:]]+)$@',
	'replacement' => 'e\\1',
	'dict_key' => '1-24',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['arista-49-to-52-SFP+'] = array
(
	'pattern' => '@^Ethernet(49|50|51|52)$@',
	'replacement' => 'e\\1',
	'dict_key' => '9-1084',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['arista-any-SFP+'] = array
(
	'pattern' => '@^Ethernet([[:digit:]]+)$@',
	'replacement' => 'e\\1',
	'dict_key' => '9-1084',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['arista-management'] = array
(
	'pattern' => '@^Management(1|2)$@',
	'replacement' => 'ma\\1',
	'dict_key' => '1-24',
	'label' => '<--->',
	'try_next_proc' => FALSE,
);

$iftable_processors['dell-33xx-any-combo-1000SFP'] = array
(
	'pattern' => '@^1/g(\d+)$@',
	'replacement' => 'g\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['dell-33xx-any-1000T'] = array
(
	'pattern' => '@^1/g(\d+)$@',
	'replacement' => 'g\\1',
	'dict_key' => '1-24',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['dell-33xx-any-100TX'] = array
(
	'pattern' => '@^1/e(\d+)$@',
	'replacement' => 'e\\1',
	'dict_key' => 19,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['dell-5224-21-to-24-combo-1000SFP'] = array
(
	'pattern' => '@^EtherNet Port on unit 1, port:(21|22|23|24)$@',
	'replacement' => 'g\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['dell-52xx-any-1000T'] = array
(
	'pattern' => '@^EtherNet Port on unit 1, port:(\d+)$@',
	'replacement' => 'g\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['dell-m6220-gigabitethernet'] = array
(
	'pattern' => '@Gi1/0/(\d+)$@',
	'replacement' => 'g\\1',
	'dict_key' => '1-24',
	'label' => 'g\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['dell-g1-to-g2-1000SFP'] = array
(
	'pattern' => '@^g(1|2)$@',
	'replacement' => 'g\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['dell-g3-to-g4-1000T'] = array
(
	'pattern' => '@^g(3|4)$@',
	'replacement' => 'g\\1',
	'dict_key' => '1-24',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['dell-g17-to-g24-combo-1000T'] = array
(
	'pattern' => '@^g(17|18|19|20|21|22|23|24)$@',
	'replacement' => 'g\\1',
	'dict_key' => '1-24',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['dell-g21-to-g24-combo-1000SFP'] = array
(
	'pattern' => '@^g(21|22|23|24)$@',
	'replacement' => 'g\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['dell-g23-to-g24-combo-1000SFP'] = array
(
	'pattern' => '@^g(23|24)$@',
	'replacement' => 'g\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['dell-any-1000SFP'] = array
(
	'pattern' => '@^g(\d+)$@',
	'replacement' => 'g\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['3com-27-to-28-1000T'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)/(\d+)/(27|28)$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => '1-24',
	'label' => '\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['3com-51-to-52-1000T'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)/(\d+)/(51|52)$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => '1-24',
	'label' => '\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['3com-25-to-26-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)/(\d+)/(25|26)$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => '4-1077',
	'label' => '\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['3com-27-to-28-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)/(\d+)/(27|28)$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => '4-1077',
	'label' => '\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['3com-49-to-50-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)/(\d+)/(49|50)$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => '4-1077',
	'label' => '\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['3com-49-to-50-1000T'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)/(\d+)/(49|50)$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => '1-24',
	'label' => '\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['3com-51-to-52-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)/(\d+)/(51|52)$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => '4-1077',
	'label' => '\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['3com-any-100TX'] = array
(
	'pattern' => '@^Ethernet(\d+)/(\d+)/(\d+)$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => '1-19',
	'label' => '\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['3com-any-1000T'] = array
(
	'pattern' => '@^GigabitEthernet(\d+)/(\d+)/(\d+)$@',
	'replacement' => '\\1/\\2/\\3',
	'dict_key' => 24,
	'label' => '\\3',
	'try_next_proc' => FALSE,
);

$iftable_processors['tplink-21-to-24-combo-1000SFP'] = array
(
	'pattern' => '@^.+ Port on unit .+ port (21|22|23|24)$@',
	'replacement' => 'g\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['tplink-25-to-28-1000T'] = array
(
	'pattern' => 'port (25|26|27|28): Gigabit Copper',
	'replacement' => 'g\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['tplink-27-to-28-combo-1000SFP'] = array
(
	'pattern' => 'port (27|28): Gigabit Copper',
	'replacement' => 'g\\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['tplink-any-100T'] = array
(
	'pattern' => 'port ([[:digit:]]+): 10/100 Copper',
	'replacement' => 'e\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
); 

$iftable_processors['tplink-any-1000T'] = array
(
	'pattern' => '@^.+ Port on unit .+ port ([[:digit:]]+)$@',
	'replacement' => 'g\\1',
	'dict_key' => 24,
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['motorola-rfs-any-1000T'] = array
(
	'pattern' => '@^ge(\d+)$@',
	'replacement' => 'ge\\1',
	'dict_key' => '1-24',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['motorola-rfs-uplink-comboSFP'] = array
(
	'pattern' => '@^up(\d+)$@',
	'replacement' => 'up\\1',
	'dict_key' => '4-1077',
	'label' => 'uplink',
	'try_next_proc' => TRUE,
);

$iftable_processors['motorola-rfs-uplink-comboT'] = array
(
	'pattern' => '@^up(\d+)$@',
	'replacement' => 'up\\1',
	'dict_key' => '1-24',
	'label' => 'uplink',
	'try_next_proc' => FALSE,
);

$iftable_processors['dlink-21-to-24-comboSFP'] = array
(
	'pattern' => '@^Slot0/(21|22|23|24)$@',
	'replacement' => '\\1F',
	'dict_key' => '4-1077',
	'label' => '\\1F',
	'try_next_proc' => TRUE,
);

$iftable_processors['dlink-21-to-24-comboT'] = array
(
	'pattern' => '@^Slot0/(21|22|23|24)$@',
	'replacement' => '\\1T',
	'dict_key' => '1-24',
	'label' => '\\1T',
	'try_next_proc' => FALSE,
);

$iftable_processors['dlink-any-1000T'] = array
(
	'pattern' => '@^Slot0/(\d+)$@',
	'replacement' => '\\1',
	'dict_key' => '1-24',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['dlink-rmon-any-100TX'] = array
(
	'pattern' => '@^RMON Port (\d+) on Unit (\d+)$@',
	'replacement' => '\\2/\\1',
	'dict_key' => '1-19',
	'label' => 'unit \\2 port \\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['dlink-rmon-49-to-50-comboSFP'] = array
(
	'pattern' => '@^RMON Port (49|50) on Unit (\d+)$@',
	'replacement' => '\\2/\\1',
	'dict_key' => '4-1077',
	'label' => 'unit \\2 port \\1',
	'try_next_proc' => TRUE,
);

$iftable_processors['dlink-rmon-49-to-50-comboT'] = array
(
	'pattern' => '@^RMON Port (49|50) on Unit (\d+)$@',
	'replacement' => '\\2/\\1',
	'dict_key' => '1-24',
	'label' => 'unit \\2 port \\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['dlink-rmon-51-to-52-1000T'] = array
(
	'pattern' => '@^RMON Port (51|52) on Unit (\d+)$@',
	'replacement' => '\\2/\\1',
	'dict_key' => 24,
	'label' => 'unit \\2 port \\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['nec-mgmt'] = array
(
	'pattern' => '@^MGMT0$@',
	'replacement' => 'mgmt 0', # note the space
	'dict_key' => '1-24',
	'label' => 'MNG',
	'try_next_proc' => FALSE,
);

$iftable_processors['nec-any-1000T'] = array
(
	'pattern' => '@^GigabitEther 0/(\d+)$@',
	'replacement' => 'gi 0/\\1', # note the space
	'dict_key' => '1-24',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['nec-any-SFP+'] = array
(
	'pattern' => '@^TenGigabitEther 0/(\d+)$@',
	'replacement' => 'te 0/\\1', # note the space
	'dict_key' => '9-1084',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['ibm-mgmt'] = array
(
	'pattern' => '@^Management1$@',
	'replacement' => 'mgmt',
	'dict_key' => '1-24',
	'label' => 'Mgmt',
	'try_next_proc' => FALSE,
);

$iftable_processors['ibm-any-1000T'] = array
(
	'pattern' => '@^Ethernet(\d+)$@',
	'replacement' => 'port \\1',
	'dict_key' => '1-24',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['ibm-49-to-52-SFP+'] = array
(
	'pattern' => '@^Ethernet(49|50|51|52)$@',
	'replacement' => 'port \\1',
	'dict_key' => '9-1084',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['ibm-45-to-48-SFP'] = array
(
	'pattern' => '@^Ethernet(45|46|47|48)$@',
	'replacement' => 'port \\1',
	'dict_key' => '4-1077',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['ibm-any-SFP+'] = array
(
	'pattern' => '@^Ethernet(\d+)$@',
	'replacement' => 'port \\1',
	'dict_key' => '9-1084',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['brocade-icx-64xx-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/2/)([[:digit:]]+)$@',
	'replacement' => '\\1\\2',
	'dict_key' => '4-1077', // empty SFP-1000
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['brocade-icx-64xx-10000SFP'] = array
(
	'pattern' => '@^10GigabitEthernet([[:digit:]]+/2/)([[:digit:]]+)$@',
	'replacement' => '\\1\\2',
	'dict_key' => '9-1084', // empty SFP+
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['brocade-icx-64xx-1000T'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/1/)([[:digit:]]+)$@',
	'replacement' => '\\1\\2',
	'dict_key' => '1-24',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

// In the following two declarations the leading zero is a placeholder -- in
// the CLI it may be another number but the SNMP agent does not report it.
$iftable_processors['brocade-vdx-QSFP+'] = array
(
	'pattern' => '@^FortyGigabitEthernet 0/(\d+)$@',
	'replacement' => 'fo 0/0/\\1',
	'dict_key' => '10-1588',
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['brocade-vdx-SFP+'] = array
(
	'pattern' => '@^TenGigabitEthernet 0/(\d+)$@',
	'replacement' => 'te 0/0/\\1',
	'dict_key' => '9-1084', // empty SFP+
	'label' => '\\1',
	'try_next_proc' => FALSE,
);

$iftable_processors['brocade-vdx-management'] = array
(
	'pattern' => '@^eth0$@',
	'replacement' => 'management',
	'dict_key' => '1-24',
	'label' => 'Management',
	'try_next_proc' => FALSE,
);

$iftable_processors['ubiquiti-chassis-any-1000T'] = array
(
	'pattern' => '@^Slot: (\d+) Port: (\d+) Gigabit - Level$@',
	'replacement' => '\\1/\\2',
	'dict_key' => 24,
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['ubiquiti-chassis-any-SFP+'] = array
(
	'pattern' => '@^Slot: (\d+) Port: (\d+) 10G - Level$@',
	'replacement' => '\\1/\\2',
	'dict_key' => '9-1084',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['ubiquiti-chassis-51-to-52-1000SFP'] = array
(
	'pattern' => '@^Slot: (\d+) Port: (51|52) Gigabit - Level$@',
	'replacement' => '\\1/\\2',
	'dict_key' => '4-1077',
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

global $known_switches;
$known_switches = array // key is system OID w/o "enterprises" prefix
(
	'9.1.217' => array
	(
		'dict_key' => 124,
		'text' => 'WS-C2924-XL: 24 RJ-45/10-100TX',
		'processors' => array ('catalyst-chassis-any-100TX'),
	),
	'9.1.220' => array
	(
		'dict_key' => 1719,
		'text' => 'WS-C2924M-XL: 24 RJ-45/10-100TX',
		'processors' => array ('catalyst-chassis-any-100TX'),
	),
	'9.1.246' => array
	(
		'dict_key' => 391,
		'text' => 'WS-C3508G-XL: 8 GBIC/1000',
		'processors' => array ('catalyst-chassis-any-1000GBIC'),
	),
	'9.1.248' => array
	(
		'dict_key' => 393,
		'text' => 'WS-C3524-XL: 24 RJ-45/10-100TX + 2 GBIC/1000',
		'processors' => array ('catalyst-chassis-any-1000GBIC', 'catalyst-chassis-any-100TX'),
	),
	'9.1.278' => array
	(
		'dict_key' => 395,
		'text' => 'WS-C3548-XL: 48 RJ-45/10-100TX + 2 GBIC/1000',
		'processors' => array ('catalyst-chassis-any-1000GBIC', 'catalyst-chassis-any-100TX'),
	),
	'9.1.282' => array
	(
		'dict_key' => 154,
		'text' => 'WS-C6506: modular device (INCOMPLETE!)',
		'processors' => array ('catalyst-chassis-any-1000T'),
	),
	'9.1.323' => array
	(
		'dict_key' => 381,
		'text' => 'WS-C2950-12 12 RJ-45/10-100TX',
		'processors' => array ('catalyst-chassis-any-100TX'),
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
	'9.1.359' => array
	(
		'dict_key' => 386,
		'text' => 'WS-C2950T-24: 24 RJ-45/10-100TX + 2 1000T uplinks',
		'processors' => array ('catalyst-chassis-uplinks-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.366' => array
	(
		'dict_key' => 400,
		'text' => 'WS-C3550-24: 24 RJ-45/10-100TX + 2 GBIC/1000',
		'processors' => array ('catalyst-chassis-any-1000GBIC', 'catalyst-chassis-any-100TX'),
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
	'9.1.431' => array
	(
		'dict_key' => 399,
		'text' => 'WS-C3550-12G: 10 GBIC/1000 + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-blade-11-to-12-1000T', 'catalyst-chassis-any-1000GBIC'),
	),
	'9.1.471' => array
	(
		'dict_key' => 270,
		'text' => '2651XM: 2 RJ-45/10-100TX',
		'processors' => array ('catalyst-chassis-any-100TX'),
	),
	'9.1.472' => array
	(
		'dict_key' => 383,
		'text' => 'WS-C2950G-24-DC 24 RJ-45/10-100TX + 2 GBIC/1000',
		'processors' => array ('catalyst-chassis-any-1000GBIC','catalyst-chassis-any-100TX'),
	),
	'9.1.480' => array
	(
		'dict_key' => 385,
		'text' => 'WS-C2950SX-24 24 RJ-45/10-100TX + 2 1000Base-SX',
		'processors' => array ('catalyst-chassis-uplinks-1000SX','catalyst-chassis-any-100TX'),
	),
	'9.1.527' => array
	(
		'dict_key' => 210,
		'text' => 'WS-C2970G-24T: 24 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-chassis-any-1000T'),
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
	'9.1.561' => array
	(
		'dict_key' => 115,
		'text' => 'WS-C2970G-24TS: 24 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-chassis-25-to-28-1000SFP', 'catalyst-chassis-any-1000T'),
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
	'9.1.569' => array
	(
		'dict_key' => 2024,
		'text' => 'Cisco 877 ISR: 4 RJ-45/10-100TX',
		'processors' => array ('catalyst-chassis-any-100TX'),
	),
	'9.1.570' => array
	(
		'dict_key' => 2025,
		'text' => 'Cisco 878 ISR: 4 RJ-45/10-100TX',
		'processors' => array ('catalyst-chassis-any-100TX'),
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
		'processors' => array ('catalyst-chassis-45-to-48-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-mgmt'),
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
	'9.1.659' => array
	(
		'dict_key' => 377,
		'text' => 'WS-C4948-10GE: 48 RJ-45/10-100-1000T(X) + 2 X2/10000 + 1 RJ-45/100TX (OOB mgmt)',
		'processors' => array ('catalyst-chassis-uplinks-10000X2', 'catalyst-chassis-uplinks-1000T', 'catalyst-chassis-mgmt'),
	),
	'9.1.694' => array
	(
		'dict_key' => 1710,
		'text' => 'WS-C2960-24TC-L: 24 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.695' => array
	(
		'dict_key' => 1590,
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
		'dict_key' => 1572,
		'text' => 'WS-C2960-48TT-L: 48 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-chassis-any-100TX', 'catalyst-chassis-any-1000T'),
	),
	'9.1.724' => array
	(
		'dict_key' => 160,
		'text' => 'WS-CE500-24TT: 24 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-any-100TX', 'catalyst-any-1000T'),
	),
	'9.1.726' => array
	(
		'dict_key' => 159,
		'text' => 'WS-CE500-24PC: 24 RJ-45/10-100TX PoE + 2 combo',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000T', 'catalyst-chassis-any-100TX', 'catalyst-chassis-mgmt'),
	),
	'9.1.727' => array
	(
		'dict_key' => 161,
		'text' => 'WS-CE500G-12TC: 8 RJ-45/10-100/1000T(X) + 4 combo',
		'processors' => array ('catalyst-9-to-12-combo-1000SFP', 'catalyst-9-to-12-1000T', 'catalyst-any-1000T'),
	),
	'9.1.749' => array
	(
		'dict_key' => 989,
		'text' => 'WS-CBS3030-DEL: 10 internal/10-100-1000T(X) + 2 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-blade-11-to-12-1000T', 'catalyst-blade-13-to-16-1000SFP', 'catalyst-blade-any-bp/1000T'),
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
	'9.1.797' => array
	(
		'dict_key' => 139,
		'text' => 'WS-C3560-8PC 8 RJ-45/10-100TX + 1 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.798' => array
	(
		'dict_key' => 165,
		'text' => 'WS-C2960-8TC-L 8 RJ-45/10-100TX + 1 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.799' => array
	(
		'dict_key' => 168,
		'text' => 'WS-C2960G-8TC-L: 7 RJ-45/10-100-1000T(X) + 1 combo-gig',
		'processors' => array ('catalyst-chassis-8-combo-1000SFP', 'catalyst-chassis-any-1000T'),
	),
	'9.1.920' => array
	(
		'dict_key' => 795,
		'text' => 'WS-CBS3032-DEL: 16 internal/10-100-1000T(X) + 4 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-blade-17-to-20-1000T', 'catalyst-blade-21-to-24-1000SFP', 'catalyst-blade-any-bp/1000T'),
	),
	'9.1.927' => array
	(
		'dict_key' => 140,
		'text' => 'WS-C2960-48TC-S: 48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.928' => array
	(
		'dict_key' => 1894,
		'text' => 'WS-C2960-24TC-S: 24 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-100TX'),
	),
	'9.1.930' => array
	(
		'dict_key' => 1607,
		'text' => 'WS-C3560E-12D-S: 12 X2/10000 w/TwinGig + OOBM',
		'processors' => array
		(
			'catalyst-chassis-any-1000SFP',
			'catalyst-chassis-uplinks-10000X2',
			'catalyst-chassis-mgmt',
		),
	),
	'9.1.950' => array
	(
		'dict_key' => 1347,
		'text' => 'WS-C2960-24PC-L: 24 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.956' => array
	(
		'dict_key' => 1721,
		'text' => 'WS-C3560E-12SD: 12 SFP/1000 +2 X2/10000 + OOBM',
		'processors' => array
		(
			'catalyst-chassis-any-1000SFP',
			'catalyst-chassis-uplinks-10000X2',
			'catalyst-chassis-mgmt',
		),
	),
	'9.1.999' => array
	(
		'dict_key' => 2038,
		'text' => 'WS-CBS3012-IBM 14 10-100-1000T + 1 10/100T + 4 RJ45/10/100/1000T(X)',
		'processors' => array ('catalyst-chassis-any-1000T','catalyst-chassis-any-100TX'),
	),
	'9.1.1000' => array
	(
		'dict_key' => 2038,
		'text' => 'WS-CBS3012-IBM-I 14 10-100-1000T + 1 10/100T + 4 RJ45/10/100/1000T(X)',
		'processors' => array ('catalyst-chassis-any-1000T','catalyst-chassis-any-100TX'),
	),
	'9.1.1005' => array
	(
		'dict_key' => 1573,
		'text' => 'WS-C2960-48TT-S: 48 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-chassis-any-100TX', 'catalyst-chassis-any-1000T'),
	),
	'9.1.1007' => array
	(
		'dict_key' => 2030,
		'text' => 'ME-3400EG-2CS-A: 2 combo ports + 4 SFP',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000T', 'catalyst-chassis-any-1000SFP', 'catalyst-chassis-mgmt'),
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
	'9.1.1021' => array
	(
		'dict_key' => 1806,
		'text' => 'WS-C3560V2-24PS: 24 RJ-45/10-100TX + 2 SFP/1000',
		'processors' => array ('catalyst-chassis-1-to-2-1000SFP', 'catalyst-chassis-any-100TX'),
	),
	'9.1.1024' => array
	(
		'dict_key' => 1805,
		'text' => 'WS-C3560V2-48TS: 48 RJ-45/10-100TX + 4 SFP/1000',
		'processors' => array ('catalyst-chassis-any-1000SFP', 'catalyst-chassis-any-100TX'),
	),
	'9.1.1025' => array
	(
		'dict_key' => 1807,
		'text' => 'WS-C3560V2-48PS: 48 RJ-45/10-100TX + 4 SFP/1000',
		'processors' => array ('catalyst-chassis-any-1000SFP', 'catalyst-chassis-any-100TX'),
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
	'9.1.1147' => array
	(
		'dict_key' => 1897,
		'text' => 'WS-C2960-24PC-S: 24 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.1208' => array
	(
		'dict_key' => 1394,
		'text' => 'WS-C2960S-24PS-L: 24 RJ-45/10-100-1000T(X) PoE+ + 4 SFP/1000',
		'processors' => array
		(
			'catalyst-chassis-mgmt',
			'catalyst-stack-25-to-28-SFP',
			'catalyst-stack-any-1000T',
		),
	),
	'9.1.1226' => array
	(
		'dict_key' => 1576,
		'text' => 'WS-C3560X-24T: 24 RJ-45/10-100-1000T(X) + network module + OOBM',
		'processors' => array
		(
			'C3KX-NM-10000',
			'C3KX-NM-1000',
			'catalyst-chassis-any-1000T',
			'catalyst-chassis-mgmt',
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
	'9.1.1245' => array
	(
		'dict_key' => 2211,
		'text' => 'CGS-2520-24TC: 24 RJ-45/10-100T(X) + 2 combo gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.1257' => array
	(
		'dict_key' => 1391,
		'text' => 'WS-C2960G-24TS-S: 24 RJ-45/10-100-1000T(X)',
		'processors' => array ('catalyst-chassis-25-to-26-1000SFP', 'catalyst-chassis-mgmt', 'catalyst-chassis-any-1000T'),
	),
	'9.1.1265' => array
	(
		'dict_key' => 1394,
		'text' => 'WS-C2960S-24PS-L: 24 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-stack-25-to-28-SFP', 'catalyst-chassis-mgmt', 'catalyst-stack-any-1000T'),
	),
	'9.1.1266' => array
	(
		'dict_key' => 1388,
		'text' => 'WS-C2960S-48TS-L: 48 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-stack-49-to-52-SFP', 'catalyst-chassis-mgmt', 'catalyst-stack-any-1000T'),
	),
	'9.1.1292' => array
	(
		'dict_key' => 1606,
		'text' => 'WS-C2360-48TD: 48 RJ-45 GigE + 4 SFP+/10000',
		'processors' => array ('catalyst-chassis-any-1000T', 'catalyst-chassis-mgmt', 'catalyst-chassis-uplinks-10000SFP+'),
	),
	'9.1.1316' => array
	(
		'dict_key' => 2029,
		'text' => 'WS-C2960CG-8TC-L: 8 RJ-45/10-100-1000T(X) + 2 combo ports',
		'processors' => array ('catalyst-9-to-10-combo-1000SFP', 'catalyst-chassis-any-1000T'),
	),
	'9.1.1317' => array
	(
		'dict_key' => 2059,
		'text' => 'WS-C3560CG-8PC-S: 8 RJ-45/10-100-1000T(X) + 2 combo ports',
		'processors' => array ('catalyst-9-to-10-combo-1000SFP', 'catalyst-chassis-any-1000T'),
	),
	'9.1.1327' => array
	(
		'dict_key' => 2026,
		'text' => 'WS-C4948E: 48 RJ-45/10-100-1000T(X) + 4 SFP+/10000 + 1 RJ-45/100TX (OOB mgmt)',
		'processors' => array ('catalyst-chassis-uplinks-10000SFP+', 'catalyst-chassis-uplinks-1000T', 'catalyst-chassis-mgmt'),
	),
	'9.1.1650' => array
	(
		'dict_key' => 1903,
		'text' => 'WS-C2960S-F48LPS-L: 48 RJ-45/10-100TX + 4 SFP/1000',
		'processors' => array ('catalyst-stack-1-to-4-SFP', 'catalyst-chassis-mgmt', 'catalyst-stack-any-100TX'),
	),
	'9.1.1693' => array
	(
		'dict_key' => 2112,
		'text' => 'WS-C2960X-24PD-L: 24 RJ-45/10-100-1000T(X) + 2 SFP+/10000',
		'processors' => array ('catalyst-stack-uplinks-10000SFP+', 'catalyst-chassis-mgmt', 'catalyst-stack-any-1000T'),

	),
	'9.1.1696' => array
	(
		'dict_key' => 2116,
		'text' => 'WS-C2960X-48LPS-L: 48 RJ-45/10-100-1000T(X) + 4 SFP/1000',
		'processors' => array ('catalyst-stack-49-to-52-SFP', 'catalyst-chassis-mgmt', 'catalyst-stack-any-1000T'),

	),
	'9.1.1697' => array
	(
		'dict_key' => 2117,
		'text' => 'WS-C2960X-24PS-L: 24 RJ-45/10-100-1000T(X) + 4 SFP+/10000',
		'processors' => array ('catalyst-stack-uplinks-10000SFP+', 'catalyst-chassis-mgmt', 'catalyst-stack-any-1000T'),

	),
	'9.1.1704' => array
	(
		'dict_key' => 2122,
		'text' => 'WS-C2960X-24TS-LL: 24 RJ-45/10-100-1000T(X) + 2 SFP/1000',
		'processors' => array ('catalyst-chassis-25-to-26-1000SFP', 'catalyst-chassis-mgmt', 'catalyst-chassis-any-1000T'),

	),
	'9.1.1750' => array
	(
		'dict_key' => 2135,
		'text' => 'WS-C2960+24LC-L: 24/48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.1755' => array
	(
		'dict_key' => 2140,
		'text' => 'WS-C2960+24LC-S: 24/48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.1749' => array
	(
		'dict_key' => 2134,
		'text' => 'WS-C2960+24PC-L: 24/48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.1754' => array
	(
		'dict_key' => 2139,
		'text' => 'WS-C2960+24PC-S: 24/48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.1752' => array
	(
		'dict_key' => 2137,
		'text' => 'WS-C2960+24TC-L: 24/48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.1757' => array
	(
		'dict_key' => 2141,
		'text' => 'WS-C2960+24TC-S: 24/48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.1748' => array
	(
		'dict_key' => 2132,
		'text' => 'WS-C2960+48PST-L: 24/48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.1753' => array
	(
		'dict_key' => 2138,
		'text' => 'WS-C2960+48PST-S: 24/48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.1.1756' => array
	(
		'dict_key' => 2219,
		'text' => 'WS-C2960+48TC-S: 24/48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('catalyst-chassis-1-to-2-combo-1000SFP', 'catalyst-chassis-any-1000T', 'catalyst-chassis-any-100TX'),
	),
	'9.5.18' => array
	(
		'dict_key' => 2094,
		'text' => 'WS-C1924-A: 24 RJ-45/10TX + 2 100TX',
		'processors' => array ('catalyst-a-to-b-100TX', 'catalyst-any-10TX'),
	),
	'9.5.42' => array
	(
		'dict_key' => 1796,
		'text' => 'WS-C2948G-L3: 48 RJ-45/10-100TX + 2 SFP/1000 ports',
		'processors' => array ('catalyst-2948-49-to-50-SFP', 'catalyst-2948-any-100TX'),
		'ifDescrOID' => 'entPhysicalName',
	),
	'9.6.1.82.8.1' => array
	(
		'dict_key' => 2374,
		'text' => 'SF 302-08: 8 RJ-45/10/100TX + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-1-to-2-combo-1000SFP',
			'generic-gi-1-to-2-1000T',
			'generic-fa-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.82.8.2' => array
	(
		'dict_key' => 2372,
		'text' => 'SF 302-08P: 8 RJ-45/10/100TX PWR + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-1-to-2-combo-1000SFP',
			'generic-gi-1-to-2-1000T',
			'generic-fa-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.82.8.3' => array
	(
		'dict_key' => 2091,
		'text' => 'SF 302-08MP: 8 RJ-45/10/100TX PWR + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-1-to-2-combo-1000SFP',
			'generic-gi-1-to-2-1000T',
			'generic-fa-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.82.24.1' => array
	(
		'dict_key' => 2373,
		'text' => 'SF 300-24: RJ-45/10/100 + 2 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-3-to-4-combo-1000SFP',
			'generic-gi-3-to-4-combo-1000T',
			'generic-gi-1-to-2-1000T',
			'generic-fa-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.82.24.2' => array
	(
		'dict_key' => 1784,
		'text' => 'SF 300-24P: RJ-45/10/100 + 2 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-3-to-4-combo-1000SFP',
			'generic-gi-3-to-4-combo-1000T',
			'generic-gi-1-to-2-1000T',
			'generic-fa-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.82.24.3' => array
	(
		'dict_key' => 2375,
		'text' => 'SF 300-24MP: RJ-45/10/100 + 2 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-3-to-4-combo-1000SFP',
			'generic-gi-3-to-4-combo-1000T',
			'generic-gi-1-to-2-1000T',
			'generic-fa-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.82.48.1' => array
	(
		'dict_key' => 1612,
		'text' => 'SF 300-48: 48 RJ-45/10/100 + 2 RJ-45/10-100-1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-3-to-4-combo-1000SFP',
			'generic-gi-3-to-4-combo-1000T',
			'generic-gi-1-to-2-1000T',
			'generic-fa-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.82.48.2' => array
	(
		'dict_key' => 2366,
		'text' => 'SF 300-48P: 48 RJ-45/10/100 + 2 RJ-45/10-100-1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-3-to-4-combo-1000SFP',
			'generic-gi-3-to-4-combo-1000T',
			'generic-gi-1-to-2-1000T',
			'generic-fa-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.83.10.1' => array
	(
		'dict_key' => 1785,
		'text' => 'SG 300-10: 8 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-9-to-10-combo-1000SFP',
			'generic-gi-9-to-10-combo-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.83.10.2' => array
	(
		'dict_key' => 2368,
		'text' => 'SG 300-10P: 8 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-9-to-10-combo-1000SFP',
			'generic-gi-9-to-10-combo-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.83.10.3' => array
	(
		'dict_key' => 2367,
		'text' => 'SG 300-10MP: 8 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-9-to-10-combo-1000SFP',
			'generic-gi-9-to-10-combo-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.83.10.5' => array
	(
		'dict_key' => 2376,
		'text' => 'SG 300-10SFP: 8 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-9-to-10-combo-1000SFP',
			'generic-gi-9-to-10-combo-1000T',
			'generic-gi-any-1000SFP',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.83.20.1' => array
	(
		'dict_key' => 2371,
		'text' => 'SG 300-20: 18 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-19-to-20-combo-1000SFP',
			'generic-gi-19-to-20-combo-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.83.28.1' => array
	(
		'dict_key' => 2370,
		'text' => 'SG 300-28: 26 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-27-to-28-combo-1000SFP',
			'generic-gi-27-to-28-combo-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.83.28.2' => array
	(
		'dict_key' => 2369,
		'text' => 'SG 300-28P: 26 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-27-to-28-combo-1000SFP',
			'generic-gi-27-to-28-combo-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.83.28.3' => array
	(
		'dict_key' => 2377,
		'text' => 'SG 300-28MP: 26 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-27-to-28-combo-1000SFP',
			'generic-gi-27-to-28-combo-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.83.52.1' => array
	(
		'dict_key' => 1783,
		'text' => 'SG 300-52: 50 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-51-to-52-combo-1000SFP',
			'generic-gi-51-to-52-combo-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.83.52.2' => array
	(
		'dict_key' => 2378,
		'text' => 'SG 300-52P: 50 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-51-to-52-combo-1000SFP',
			'generic-gi-51-to-52-combo-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.83.52.3' => array
	(
		'dict_key' => 2379,
		'text' => 'SG 300-52MP: 50 RJ-45/10/100/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-51-to-52-combo-1000SFP',
			'generic-gi-51-to-52-combo-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.84.24.1' => array
	(
		'dict_key' => 2357,
		'text' => 'SF 220-24: 24 RJ-45/10/100TX + 2 combo-gig',
		'processors' => array
		(
			'cisco-smb-1-to-2-combo-1000SFP',
			'cisco-smb-1-to-2-combo-1000T',
			'cisco-smb-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.84.24.2' => array
	(
		'dict_key' => 2356,
		'text' => 'SF 220-24P: 24 RJ-45/10/100TX + 2 combo-gig',
		'processors' => array
		(
			'cisco-smb-1-to-2-combo-1000SFP',
			'cisco-smb-1-to-2-combo-1000T',
			'cisco-smb-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.84.48.1' => array
	(
		'dict_key' => 2355,
		'text' => 'SF 220-48: 48 RJ-45/10/100TX + 2 combo-gig',
		'processors' => array
		(
			'cisco-smb-1-to-2-combo-1000SFP',
			'cisco-smb-1-to-2-combo-1000T',
			'cisco-smb-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.84.48.2' => array
	(
		'dict_key' => 2354,
		'text' => 'SF 220-48P: 48 RJ-45/10/100TX + 2 combo-gig',
		'processors' => array
		(
			'cisco-smb-1-to-2-combo-1000SFP',
			'cisco-smb-1-to-2-combo-1000T',
			'cisco-smb-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.84.26.1' => array
	(
		'dict_key' => 2353,
		'text' => 'SG 220-26: 24 RJ-45/10/100TX/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'cisco-smb-25-to-26-combo-1000SFP',
			'cisco-smb-25-to-26-combo-1000T',
			'cisco-smb-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.84.26.2' => array
	(
		'dict_key' => 2352,
		'text' => 'SG 220-26P: 24 RJ-45/10/100TX/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'cisco-smb-25-to-26-combo-1000SFP',
			'cisco-smb-25-to-26-combo-1000T',
			'cisco-smb-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.84.50.1' => array
	(
		'dict_key' => 2351,
		'text' => 'SG 220-50: 48 RJ-45/10/100TX/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'cisco-smb-49-to-50-combo-1000SFP',
			'cisco-smb-49-to-50-combo-1000T',
			'cisco-smb-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.84.50.2' => array
	(
		'dict_key' => 2350,
		'text' => 'SG 220-50P: 48 RJ-45/10/100TX/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'cisco-smb-49-to-50-combo-1000SFP',
			'cisco-smb-49-to-50-combo-1000T',
			'cisco-smb-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.87.24.1' => array
	(
		'dict_key' => 2345,
		'text' => 'SF 200-24: 24 RJ-45/10/100TX + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-1-to-2-combo-1000SFP',
			'generic-gi-1-to-2-1000T',
			'generic-fa-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.87.24.2' => array
	(
		'dict_key' => 2346,
		'text' => 'SF 200-24P: 24 RJ-45/10/100TX + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-1-to-2-combo-1000SFP',
			'generic-gi-1-to-2-1000T',
			'generic-fa-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.87.24.3' => array
	(
		'dict_key' => 2347,
		'text' => 'SF 200-24FP: 24 RJ-45/10/100TX + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-1-to-2-combo-1000SFP',
			'generic-gi-1-to-2-1000T',
			'generic-fa-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.87.48.1' => array
	(
		'dict_key' => 2348,
		'text' => 'SF 200-48: 48 RJ-45/10/100TX + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-1-to-2-combo-1000SFP',
			'generic-gi-1-to-2-1000T',
			'generic-fa-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.87.48.2' => array
	(
		'dict_key' => 2349,
		'text' => 'SF 200-48P: 48 RJ-45/10/100TX + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-1-to-2-combo-1000SFP',
			'generic-gi-1-to-2-1000T',
			'generic-fa-any-100TX',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.88.10.3' => array
	(
		'dict_key' => 2342,
		'text' => 'SG 200-10FP: 8 RJ-45/10/100TX/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-9-to-10-combo-1000SFP',
			'generic-gi-9-to-10-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.88.18.1' => array
	(
		'dict_key' => 2341,
		'text' => 'SG 200-18: 16 RJ-45/10/100TX/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-17-to-18-combo-1000SFP',
			'generic-gi-17-to-18-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.88.26.1' => array
	(
		'dict_key' => 2338,
		'text' => 'SG 200-26: 24 RJ-45/10/100TX/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-25-to-26-combo-1000SFP',
			'generic-gi-25-to-26-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.88.26.2' => array
	(
		'dict_key' => 2339,
		'text' => 'SG 200-26P: 24 RJ-45/10/100TX/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-25-to-26-combo-1000SFP',
			'generic-gi-25-to-26-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.88.26.3' => array
	(
		'dict_key' => 2340,
		'text' => 'SG 200-26FP: 24 RJ-45/10/100TX/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-25-to-26-combo-1000SFP',
			'generic-gi-25-to-26-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.88.50.1' => array
	(
		'dict_key' => 2335,
		'text' => 'SG 200-50: 48 RJ-45/10/100TX/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-49-to-50-combo-1000SFP',
			'generic-gi-49-to-50-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.88.50.2' => array
	(
		'dict_key' => 2336,
		'text' => 'SG 200-50P: 48 RJ-45/10/100TX/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-49-to-50-combo-1000SFP',
			'generic-gi-49-to-50-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
	),
	'9.6.1.88.50.3' => array
	(
		'dict_key' => 2337,
		'text' => 'SG 200-50FP: 48 RJ-45/10/100TX/1000T(X) + 2 combo-gig',
		'processors' => array
		(
			'generic-gi-49-to-50-combo-1000SFP',
			'generic-gi-49-to-50-1000T',
			'generic-gi-any-1000T',
		),
		'ifDescrOID' => 'ifName',
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
	'9.12.3.1.3.1008' => array
	(
		'dict_key' => 1412,
		'text' => 'N5K-C5548P: 32 SFP+/10000',
		'processors' => array ('nexus-any-10000SFP+', 'nexus-mgmt'),
	),
	'9.12.3.1.3.1084' => array
	(
		'dict_key' => 1412,
		'text' => 'N5K-C5548P: 32 SFP+/10000',
		'processors' => array ('nexus-any-10000SFP+', 'nexus-mgmt'),
	),
	'9.12.3.1.3.1106' => array
	(
		'dict_key' => 2333,
		'text' => 'N3K-3048P: 48 RJ-45/100-1000TX + 4 SFP+/1000-10000',
		'processors' => array ('nexus-3048-49-to-52-1000SFP', 'nexus-3048-1000TX', 'nexus-mgmt'),
	),
	'9.12.3.1.3.1417' => array
	(
		'dict_key' => 2331,
		'text' => 'Nexus 3132Q: 32 QSFP+',
		'processors' => array ('nexus-any-QSFP-split', 'nexus-any-QSFP+', 'nexus-mgmt'),
	),
	'11.2.3.7.11.9' => array
	(
		'dict_key' => 1086,
		'text' => 'J4121A: modular system',
		'processors' => array ('procurve-modular-100TX'),
	),
	'11.2.3.7.11.19' => array
	(
		'dict_key' => 859,
		'text' => 'J4813A: 24 RJ-45/10-100TX + 2 modules of varying type',
		'processors' => array ('procurve-chassis-100TX'),
	),
	'11.2.3.7.11.29' => array
	(
		'dict_key' => 866,
		'text' => 'J4899A: 48 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('procurve-49-to-50-1000T', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.31' => array
	(
		'dict_key' => 870,
		'text' => 'J4903A: 24 RJ-45/10-100-1000T(X)',
		'processors' => array ('procurve-chassis-1000T'),
	),
	'11.2.3.7.11.32' => array
	(
		'dict_key' => 871,
		'text' => 'J4904A: 48 RJ-45/10-100-1000T(X)',
		'processors' => array ('procurve-chassis-1000T'),
	),
	'11.2.3.7.11.33.4.1.1' => array
	(
		'dict_key' => 1605,
		'text' => 'HP GbE2c w/SFP',
		'processors' => array
		(
			'gbe2csfp-1-to-16',
			'gbe2csfp-17',
			'gbe2csfp-18',
			'gbe2csfp-19',
			'gbe2csfp-20',
			'gbe2csfp-21sfp',
			'gbe2csfp-22sfp',
			'gbe2csfp-23sfp',
			'gbe2csfp-24sfp',
			'gbe2csfp-21',
			'gbe2csfp-22',
			'gbe2csfp-23',
			'gbe2csfp-24',
		),
	),
	'11.2.3.7.11.34' => array
	(
		'dict_key' => 864,
		'text' => 'J4900A: 24 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('procurve-25-to-26-combo-1000SFP', 'procurve-25-to-26-1000T', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.35' => array
	(
		'dict_key' => 867,
		'text' => 'J8165A: 48 RJ-45/10-100TX PoE + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('procurve-49-to-50-1000T', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.36' => array
	(
		'dict_key' => 865,
		'text' => 'J8164A: 24 RJ-45/10-100TX PoE + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('procurve-25-to-26-1000T', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.44' => array
	(
		'dict_key' => 866,
		'text' => 'J4899B: 48 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X)',
		'processors' => array ('procurve-49-to-50-1000T', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.45' => array
	(
		'dict_key' => 864,
		'text' => 'J4900B: 24 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('procurve-25-to-26-combo-1000SFP', 'procurve-25-to-26-1000T', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.53' => array
	(
		'dict_key' => 881,
		'text' => 'J8773A: modular system',
		'processors' => array ('procurve-modular-1000T'),
	),
	'11.2.3.7.11.58' => array
	(
		'dict_key' => 876,
		'text' => 'J8692A: 20 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('procurve-21-to-24-combo-1000SFP', 'procurve-chassis-1000T'),
	),
	'11.2.3.7.11.59' => array
	(
		'dict_key' => 877,
		'text' => 'J8693A: 44 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('procurve-45-to-48-combo-1000SFP', 'procurve-chassis-1000T'),
	),
	'11.2.3.7.11.62' => array
	(
		'dict_key' => 855,
		'text' => 'J9020A: 48 RJ-45/10-100TX + 2 RJ-45/10-1000-1000T(X) + 2 SFP-1000',
		'processors' => array ('procurve-51-to-52-1000SFP', 'procurve-49-to-50-1000T', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.63' => array
	(
		'dict_key' => 868,
		'text' => 'J9021A: 20 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('procurve-21-to-24-combo-1000SFP', 'procurve-chassis-1000T'),
	),
	'11.2.3.7.11.64' => array
	(
		'dict_key' => 869,
		'text' => 'J9022A: 44 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('procurve-45-to-48-combo-1000SFP', 'procurve-chassis-1000T'),
	),
	'11.2.3.7.11.65' => array
	(
		'dict_key' => 850,
		'text' => 'J9028A: 22 RJ-45/10-100-1000T(X) + 2 combo-gig',
		'processors' => array ('smc2-combo-23-to-24', 'smc2-any-1000T'),
	),
	'11.2.3.7.11.68' => array
	(
		'dict_key' => 873,
		'text' => 'J9050A: 48 RJ-45/10-100-1000T',
		'processors' => array ('procurve-chassis-1000T'),
	),
	'11.2.3.7.11.76' => array
	(
		'dict_key' => 860,
		'text' => 'J9085A: 24 RJ-45/10-100TX + 2 1000T + 2 SFP-1000',
		'processors' => array ('procurve-25-to-26-1000T', 'procurve-27-to-28-1000SFP', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.78' => array
	(
		'dict_key' => 861,
		'text' => 'J9087A: 24 RJ-45/10-100TX PoE + 2 1000T + 2 SFP-1000',
		'processors' => array ('procurve-25-to-26-1000T', 'procurve-27-to-28-1000SFP', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.79' => array
	(
		'dict_key' => 863,
		'text' => 'J9089A: 48 RJ-45/10-100TX PoE + 2 1000T + 2 SFP-1000',
		'processors' => array ('procurve-49-to-50-1000T', 'procurve-51-to-52-1000SFP', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.80' => array
	(
		'dict_key' => 1570,
		'text' => 'J9086A: 24 RJ-45/10-100TX 12 PoE + 2 1000T + 2 SFP-1000',
		'processors' => array ('procurve-25-to-26-1000T', 'procurve-27-to-28-1000SFP', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.81' => array
	(
		'dict_key' => 850,
		'text' => 'J9028B: 22 RJ-45/10-100-1000T(X) + 2 combo-gig',
		'processors' => array ('smc2-combo-23-to-24', 'smc2-any-1000T'),
	),
	'11.2.3.7.11.84' => array
	(
		'dict_key' => 2096,
		'text' => 'J9146A: 20 RJ-45/10-100-1000T(X) + 4 combo-gig + varying uplinks',
		'processors' => array ('procurve-21-to-24-combo-1000SFP', 'procurve-chassis-1000T'),
	),
	'11.2.3.7.11.85' => array
	(
		'dict_key' => 1600,
		'text' => 'J9148A: 44 RJ-45/10-100-1000T(X) + 4 combo-gig + varying uplinks',
		'processors' => array ('procurve-45-to-48-combo-1000SFP', 'procurve-chassis-1000T'),
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
	'11.2.3.7.11.88' => array
	(
		'dict_key' => 1349,
		'text' => 'J9279A: 22 RJ-45/10-100-1000T(X) + 2 combo-gig + varying uplinks',
		'processors' => array ('procurve-23-to-24-combo-1000SFP', 'procurve-chassis-1000T'),
	),
	'11.2.3.7.11.89' => array
	(
		'dict_key' => 857,
		'text' => 'J9280A: 44 RJ-45/10-100-1000T(X) + 4 combo-gig + varying uplinks',
		'processors' => array ('procurve-45-to-48-combo-1000SFP', 'procurve-chassis-1000T'),
	),
	'11.2.3.7.11.94' => array
	(
		'dict_key' => 1967,
		'text' => 'J9137A: 8 RJ-45/10-100TX PoE + 2 combo-gig',
		'processors' => array ('procurve-9-to-10-combo-1000SFP', 'procurve-9-to-10-1000T', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.95' => array
	(
		'dict_key' => 1711,
		'text' => 'J9138A: 24 RJ-45/10-100TX PoE + 2 1000T + 2 combo-gig',
		'processors' => array ('procurve-25-to-26-1000T', 'procurve-27-to-28-combo-1000SFP', 'procurve-27-to-28-1000T', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.105' => array
	(
		'dict_key' => 1641,
		'text' => 'J9452A: 48 RJ-45/10-100-1000T + 2 SFP-10000+',
		'processors' => array ('procurve-49-to-52-10000SFP+', 'procurve-chassis-1000T'),
	),
	'11.2.3.7.11.131' => array
	(
		'dict_key' => 2396,
		'text' => 'J9625A: 24 RJ-45/10-100TX (12 PoE) + 2 1000T + 2 SFP-1000',
		'processors' => array ('procurve-25-to-26-1000T', 'procurve-27-to-28-1000SFP', 'procurve-chassis-100TX'),
	),
	'11.2.3.7.11.154' => array
	(
		'dict_key' => 2213,
		'text' => 'J9728A: 44 RJ-45/10-100-1000T + 4 combo-gig',
		'processors' => array ('procurve-45-to-48-combo-1000SFP', 'procurve-chassis-1000T'),
	),
	'43.1.16.4.3.8' => array
	(
		'dict_key' => 780,
		'text' => '5500G-EI 48-Port: 44 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('3com-49-to-50-1000SFP', '3com-51-to-52-1000SFP', '3com-any-1000T'),
	),
	'43.1.16.4.3.21' => array
	(
		'dict_key' => 766,
		'text' => '4500 26-port: 24 100TX + 2 SFP/1000T combo',
		'processors' => array ('3com-27-to-28-1000T', '3com-25-to-26-1000SFP', '3com-any-100TX'),
	),
	'43.1.16.4.3.22' => array
	(
		'dict_key' => 767,
		'text' => '4500 52-port: 48 100TX + 2 SFP/1000T combo',
		'processors' => array ('3com-49-to-50-1000SFP', '3com-51-to-52-1000T', '3com-any-100TX'),
	),
	'43.1.16.4.3.23' => array
	(
		'dict_key' => 768,
		'text' => '4500-PWR 26-port: 24 100TX + 2 SFP/1000T combo',
		'processors' => array ('3com-27-to-28-1000T', '3com-25-to-26-1000SFP', '3com-any-100TX'),
	),
	'43.1.16.4.3.24' => array
	(
		'dict_key' => 769,
		'text' => '4500-PWR 52-port: 48 100TX + 2 SFP/1000T combo',
		'processors' => array ('3com-49-to-50-1000SFP', '3com-51-to-52-1000T', '3com-any-100TX'),
	),
	'43.1.16.4.3.29' => array
	(
		'dict_key' => 758,
		'text' => '4200G 48-port: 44 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('3com-49-to-50-1000SFP', '3com-51-to-52-1000SFP', '3com-any-1000T'),
	),
	'43.1.16.4.3.31' => array
	(
		'dict_key' => 771,
		'text' => '4500G 48-port: 44 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('3com-49-to-50-1000SFP', '3com-51-to-52-1000SFP', '3com-any-1000T'),
	),
	'43.1.16.4.3.45' => array
	(
		'dict_key' => 760,
		'text' => '4210 52-port: 48 100TX + 2 1000T + 2 SFP',
		'processors' => array ('3com-49-to-50-1000T', '3com-51-to-52-1000SFP', '3com-any-100TX'),
	),
	'43.1.16.4.3.83' => array
	(
		'dict_key' => 2092,
		'text' => '4510G 24-port: 24 RJ-45/10-100-1000T(X) + 4 SFP combo-gig',
		'processors' => array ('3com-25-to-26-1000SFP', '3com-27-to-28-1000SFP', '3com-any-1000T'),
	),
	'45.3.68.5' => array
	(
		'dict_key' => 1085,
		'text' => 'BES50GE-12T PWR: 12 RJ-45/10-100-1000T(X)',
		'processors' => array ('nortel-any-1000T'),
	),
	'119.1.203.2.2.41' => array
	(
		'dict_key' => 1810,
		'text' => 'PF5240: 48 RJ-45/10-100-1000T(X) + 4 SFP+',
		'processors' => array ('nec-any-1000T', 'nec-any-SFP+', 'nec-mgmt'),
	),
	'171.10.63.8' => array
	(
		'dict_key' => 616,
		'text' => 'DES-3052: 48 RJ-45/10-100TX + 2 RJ-45/10-100-1000T(X) + 2 combo ports',
		'processors' => array ('dlink-rmon-49-to-50-comboSFP', 'dlink-rmon-49-to-50-comboT', 'dlink-rmon-51-to-52-1000T', 'dlink-rmon-any-100TX'),
	),
	'171.10.76.10' => array
	(
		'dict_key' => 1799,
		'text' => 'DGS-1210-24: 20 RJ-45/10-100-1000T(X) + 4 combo ports',
		'processors' => array ('dlink-21-to-24-comboSFP', 'dlink-21-to-24-comboT', 'dlink-any-1000T'),
		'ifDescrOID' => 'ifName',
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
	'202.20.68' => array
	(
		'dict_key' => 1374,
		'text' => 'SMC8150L2: 46 RJ-45/10-100-1000T(X) + 4 combo ports',
		'processors' => array ('smc-combo-45-to-48', 'nortel-any-1000T'),
	),
	'207.1.4' => array
	(
		'dict_key' => 2095,
		'text' => 'AT-GS950/24: 20 RJ-45/10-100-1000T(X) + 4 combo ports',
		'processors' => array ('generic-21-to-24-combo-1000SFP', 'generic-any-1000T'),
	),
	'207.1.14.53' => array
	(
		'dict_key' => 1720,
		'text' => 'AT9924T: 24 RJ-45/10-100-1000T(X) ports',
		'processors' => array ('generic-port-any-1000T'),
	),
	'388.18' => array
	(
		'dict_key' => 1795,
		'text' => 'RFS 4000: 5 RJ-45/10-100-1000T(X) + 1 combo uplink ports',
		'processors' => array ('motorola-rfs-uplink-comboSFP', 'motorola-rfs-uplink-comboT', 'motorola-rfs-any-1000T'),
	),
	'674.10895.4' => array
	(
		'dict_key' => 1622,
		'text' => 'PowerConnect 5224: 20 RJ-45/10-100-1000T(X) + 4 combo ports',
		'processors' => array ('dell-5224-21-to-24-combo-1000SFP', 'dell-52xx-any-1000T'),
	),
	'674.10895.3000' => array
	(
		'dict_key' => 1623,
		'text' => 'PowerConnect 6024F: 16 SFP + 8 combo ports',
		'processors' => array ('dell-g17-to-g24-combo-1000T', 'dell-any-1000SFP'),
		'ifDescrOID' => 'ifName',
	),
	'674.10895.3003' => array
	(
		'dict_key' => 1611,
		'text' => 'PowerConnect 3348: 48 RJ-45/10-100TX + 2 combo ports',
		'processors' => array ('dell-33xx-any-combo-1000SFP', 'dell-33xx-any-1000T', 'dell-33xx-any-100TX'),
		'ifDescrOID' => 'entPhysicalName',
	),
	'674.10895.3004' => array
	(
		'dict_key' => 349,
		'text' => 'PowerConnect 5324: 20 RJ-45/10-100-1000T(X) + 4 combo ports',
		'processors' => array ('dell-g21-to-g24-combo-1000SFP', 'generic-g-any-1000T'),
		'ifDescrOID' => 'ifName',
	),
	'674.10895.3007' => array
	(
		'dict_key' => 347,
		'text' => 'PowerConnect 3448: 48 RJ-45/10-100TX + 2 SFP/1000 + 2 RJ-45/10-100-1000T(X) ports',
		'processors' => array ('dell-g1-to-g2-1000SFP', 'dell-g3-to-g4-1000T', 'generic-e-any-100TX'),
		'ifDescrOID' => 'ifName',
	),
	'674.10895.3009' => array
	(
		'dict_key' => 348,
		'text' => 'PowerConnect 3448P: 48 RJ-45/10-100TX PoE + 2 SFP/1000 + 2 RJ-45/10-100-1000T(X) ports',
		'processors' => array ('dell-g1-to-g2-1000SFP', 'dell-g3-to-g4-1000T', 'generic-e-any-100TX'),
		'ifDescrOID' => 'ifName',
	),
	'674.10895.3010' => array
	(
		'dict_key' => 350,
		'text' => 'PowerConnect 6224: 20 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('netgear-chassis-21-to-24-1000SFP', 'netgear-chassis-21-to-24-1000Tcombo', 'netgear-chassis-any-1000T'),
	),
	'674.10895.3011' => array
	(
		'dict_key' => 353,
		'text' => 'PowerConnect 6248: 44 RJ-45/10-100-1000T(X) + 4 combo-gig + optional uplinks',
		# 10G uplinks actually may be SFP+, T, CX4 or XFP
		'processors' => array ('netgear-chassis-45-to-48-1000SFPcombo', 'netgear-chassis-any-1000T', 'netgear-chassis-any-SFP+'),
	),
	'674.10895.3014' => array
	(
		'dict_key' => 352,
		'text' => 'PowerConnect 6224F: 20 SFP + 4 combo-gig',
		'processors' => array ('netgear-chassis-21-to-24-1000SFP', 'netgear-chassis-21-to-24-1000Tcombo', 'netgear-chassis-any-1000SFP'),
	),
	'674.10895.3015' => array
	(
		'dict_key' => 1532,
		'text' => 'PowerConnect M6220 blade cabinet switch',
		'processors' => array ('dell-m6220-gigabitethernet'),
		'ifDescrOID' => 'ifDescr',
	),
	'674.10895.3017' => array
	(
		'dict_key' => 1067,
		'text' => 'PowerConnect 3548: 48 RJ-45/10-100TX + 2 SFP/1000 + 2 RJ-45/10-100-1000T(X) ports',
		'processors' => array ('dell-g1-to-g2-1000SFP', 'dell-g3-to-g4-1000T', 'generic-e-any-100TX'),
	),
	'674.10895.3019' => array
	(
		'dict_key' => 1068,
		'text' => 'PowerConnect 3548P: 48 RJ-45/10-100TX PoE + 2 SFP/1000 + 2 RJ-45/10-100-1000T(X) ports',
		'processors' => array ('dell-g1-to-g2-1000SFP', 'dell-g3-to-g4-1000T', 'generic-e-any-100TX'),
	),
	'674.10895.3020' => array
	(
		'dict_key' => 1069,
		'text' => 'PowerConnect 5424: 20 RJ-45/10-100-1000T(X) + 4 combo ports',
		'processors' => array ('dell-g21-to-g24-combo-1000SFP', 'generic-g-any-1000T'),
		'ifDescrOID' => 'ifName',
	),
	'674.10895.3021' => array
	(
		'dict_key' => 1070,
		'text' => 'PowerConnect 5448: 44 RJ-45/10-100-1000T(X) + 4 combo ports',
		'processors' => array ('generic-g45-to-g48-combo-1000SFP', 'generic-g-any-1000T'),
		'ifDescrOID' => 'ifName',
	),
	'674.10895.3028' => array
	(
		'dict_key' => 1063,
		'text' => 'PowerConnect 2824: 22 RJ-45/10-100-1000T(X) + 2 combo ports',
		'processors' => array ('dell-g23-to-g24-combo-1000SFP', 'generic-g-any-1000T'),
		'ifDescrOID' => 'ifName',
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
	'1991.1.3.53.1.2' => array
	(
		'dict_key' => 1044,
		'text' => 'TurboIron 24X : 4 10/100/1000T RJ-45, 24 10000 SFP+, 1 10/100/1000 RJ-45 + 1 Db9 Serial Mgmt',
		'processors' => array ('turboiron-any-10000SFP+', 'turboiron-any-10-100-1000T', 'fcx-management'),
	),
	'1991.1.3.54.2.4.1.1' => array # L2 software
	(
		'dict_key' => 1362,
		'text' => 'FCX 648: 48 RJ-45/10-100-1000T(X) + uplink slot with 4 SFP+',
		'processors' => array ('fgs-any-1000T', 'fcx-uplinks', 'fcx-management'),
	),
	'1991.1.3.54.2.4.1.3' => array # L3 software
	(
		'dict_key' => 1362,
		'text' => 'FCX 648: 48 RJ-45/10-100-1000T(X) + uplink slot with 4 SFP+',
		'processors' => array ('fgs-any-1000T', 'fcx-uplinks', 'fcx-management'),
	),
	'2011.2.23.94' => array
	(
		'dict_key' => 1619,
		'text' => 'S2352P-EI: 48 RJ-45/10-100TX + 4 SFP-1000',
		'processors' => array ('quidway-any-100TX', 'quidway-any-1000SFP'),
	),
	'2011.2.23.95' => array
	(
		'dict_key' => 1335,
		'text' => 'S5328C-EI: 20 RJ-45/10-100-1000T(X) + 4 combo-gig + 2 XFP slots',
		'processors' => array ('quidway-21-to-24-comboSFP', 'quidway-any-1000T', 'quidway-XFP', 'quidway-mgmt'),
	),
	'2011.2.23.96' => array
	(
		'dict_key' => 1321,
		'text' => 'S5328C-EI-24S: 20 SFP-1000 + 4 combo-gig + 2 XFP slots',
		'processors' => array ('quidway-21-to-24-comboT', 'quidway-any-1000SFP', 'quidway-XFP', 'quidway-mgmt'),
	),
	'2011.2.23.97' => array
	(
		'dict_key' => 1337,
		'text' => 'S5352C-EI: 48 RJ-45/10-100-1000T(X) + optional 2xXFP/4xSFP slots',
		'processors' => array ('quidway-slot1-SFP', 'quidway-any-1000T', 'quidway-XFP', 'quidway-mgmt'),
	),
	'2011.2.23.102' => array
	(
		'dict_key' => 1339,
		'text' => 'S5328C-SI: 20 RJ-45/10-100-1000T(X) + 4 combo-gig + 2 XFP slots',
		'processors' => array ('quidway-21-to-24-comboSFP', 'quidway-any-1000T', 'quidway-XFP', 'quidway-mgmt'),
	),
	'2011.2.23.103' => array
	(
		'dict_key' => 1341,
		'text' => 'S5352C-SI: 48 RJ-45/10-100-1000T(X) + optional 2xXFP/4xSFP slots',
		'processors' => array ('quidway-slot1-SFP', 'quidway-any-1000T', 'quidway-XFP', 'quidway-mgmt'),
	),
	'2011.2.23.119' => array
	(
		'dict_key' => 1914,
		'text' => 'S2700-52P-EI: 48 RJ-45/10-100TX + 4 SFP-1000',
		'processors' => array ('quidway-any-100TX', 'quidway-any-1000SFP'),
	),
	'2011.2.239.4' => array
	(
		'dict_key' => 1769,
		'text' => 'CE5850-48T4S2Q-EI: 48 RJ-45/10-100-1000T(X) + 4 SFP+ slots + 2 QSFP+ slots',
		'processors' => array ('hce-any-1000T', 'hce-any-SFP', 'hce-any-QSFP', 'quidway-mgmt'),
	),
	'2011.2.239.10' => array
	(
		'dict_key' => 1769,
		'text' => 'CE5850-48T4S2Q-HI: 48 RJ-45/10-100-1000T(X) + 4 SFP+ slots + 2 QSFP+ slots',
		'processors' => array ('hce-any-1000T', 'hce-any-SFP', 'hce-any-QSFP', 'quidway-mgmt'),
	),
	'2011.2.239.5' => array
	(
		'dict_key' => 1772,
		'text' => 'CE6850-48S4Q-EI: 48 SFP+ slots + 4 QSFP+ slots',
		'processors' => array ('hce-any-SFP', 'hce-any-QSFP', 'quidway-mgmt'),
	),
	'2011.2.239.32' => array
	(
		'dict_key' => 1772,
		'text' => 'CE6870-48S6CQ-EI: 48 SFP+ slots + 6 QSFP28 slots',
		'processors' => array ('hce-any-SFP', 'hce-any-QSFP28-split', 'hce-any-QSFP28', 'quidway-mgmt'),
	),
	'2011.2.239.11' => array
	(
		'dict_key' => 2226,
		'text' => 'CE7850-32Q-EI: 32 QSFP+ slots',
		'processors' => array ('hce-any-QSFP-split', 'hce-any-QSFP', 'quidway-mgmt'),
	),
	'2011.2.239.42' => array
	(
		'dict_key' => 2706,
		'text' => 'CE8850-32CQ-EI: 32 QSFP28 slots',
		'processors' => array ('hce-any-SFP', 'hce-any-QSFP28-split', 'hce-any-QSFP28', 'quidway-mgmt'),
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
	'2636.1.1.1.2.30' => array
	(
		'dict_key' => 900,
		'text' => 'Juniper EX3200 series',
		'processors' => array ('juniper-ex-pic0-1000T', 'juniper-ex-mgmt'),
	),
	'2636.1.1.1.2.31' => array
	(
		'dict_key' => 905,
		'text' => 'Juniper EX4200 series',
		'processors' => array ('juniper-ex-pic0-1000T', 'juniper-ex-mgmt'),
	),
	'2636.1.1.1.2.43' => array
	(
		'dict_key' => 2395,
		'text' => 'Juniper EX2200 series',
		'processors' => array ('juniper-ex-pic0-1000T', 'juniper-ex-mgmt'),
	),
	'3955.6.1.2024.1' => array
	(
		'dict_key' => 2212,
		'text' => 'Linksys SRW2024P: 22 RJ-45/10-100-1000T(X) + 2 combo ports',
		'processors' => array ('generic-g12-and-g24-combo-1000SFP', 'generic-g-any-1000T'),
		'ifDescrOID' => 'ifName',
	),
	'3955.6.1.2048.1' => array
	(
		'dict_key' => 1624,
		'text' => 'Linksys SRW2048: 44 RJ-45/10-100-1000T(X) + 4 combo ports',
		'processors' => array ('generic-g45-to-g48-combo-1000SFP', 'generic-g-any-1000T'),
		'ifDescrOID' => 'ifName',
	),
	'3955.6.5024' => array
	(
		'dict_key' => 1966,
		'text' => 'Linksys SRW224G4: 24-port 10/100 + 4-Port Gigabit Switch with WebView',
		'processors' => array ('generic-g-1-to-2-1000T', 'generic-g-3-to-4-combo-1000SFP', 'generic-g-3-to-4-combo-1000T', 'generic-e-any-100TX'),
		'ifDescrOID' => 'ifName',
	),
	'3955.6.5048' => array
	(
		'dict_key' => 2093,
		'text' => 'Linksys SRW248G4: 48-port 10/100 + 4-Port Gigabit Switch with WebView',
		'processors' => array ('generic-g-1-to-2-1000T', 'generic-g-3-to-4-combo-1000SFP', 'generic-g-3-to-4-combo-1000T', 'generic-e-any-100TX'),
		'ifDescrOID' => 'ifName',
	),
	'4526.100.1.1' => array
	(
		'dict_key' => 587,
		'text' => 'FSM7328S: 24 RJ-45/10-100TX + 4 combo-gig',
		'processors' => array ('netgear-chassis-any-100TX', 'netgear-chassis-any-1000SFPcombo', 'netgear-chassis-any-1000T'),
	),
	'4526.100.2.2' => array
	(
		'dict_key' => 562,
		'text' => 'GSM7224: 20 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('netgear-chassis-21-to-24-1000SFP', 'netgear-chassis-21-to-24-1000Tcombo', 'netgear-chassis-any-1000T'),
	),
	'4526.100.2.3' => array
	(
		'dict_key' => 559,
		'text' => 'GSM7212: 12 combo-gig',
		'processors' => array ('netgear-chassis-any-1000SFPcombo', 'netgear-chassis-any-1000T'),
	),
	'4526.100.1.13' => array
	(
		'dict_key' => 1601,
		'text' => 'GSM7328Sv2: 20 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('netgear-chassis-21-to-24-1000SFP', 'netgear-chassis-21-to-24-1000Tcombo', 'netgear-chassis-any-1000T'),
	),
	'4526.100.1.14' => array
	(
		'dict_key' => 1794,
		'text' => 'GSM7352Sv2: 44 RJ-45/10-100-1000T(X) + 4 combo-gig + SFP+ uplinks',
		'processors' => array ('netgear-chassis-45-to-48-1000SFPcombo', 'netgear-chassis-any-1000T', 'netgear-chassis-any-SFP+'),
	),
	'4526.100.4.6' => array
	(
		'dict_key' => 578,
		'text' => 'FS726TP: 24 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('netgear-fs726tp-1000SFPcombo', 'netgear-fs726tp-1000T', 'netgear-fs726tp-100TX'),
	),
	'4526.100.4.10' => array
	(
		'dict_key' => 565,
		'text' => 'GS724TP: 24 RJ-45/10-100-1000T + 2 combo-gig SFP',
		'ifDescrOID' => 'ifName',
		'processors' => array ('netgear-23-to-24-1000SPFcombo', 'netgear-any-1000T'),
	),
	'4526.100.11.1' => array
	(
		'dict_key' => 557,
		'text' => 'GSM7224R: 20 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('netgear-chassis-21-to-24-1000SFP', 'netgear-chassis-21-to-24-1000Tcombo', 'netgear-chassis-any-1000T'),
	),
	'4526.100.11.5' => array
	(
		'dict_key' => 1602,
		'text' => 'GSM7224v2: 20 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('netgear-chassis-21-to-24-1000SFP', 'netgear-chassis-21-to-24-1000Tcombo', 'netgear-chassis-any-1000T'),
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
	'10977.11825.11833.97.25451.12800.100.4.4' => array
	(
		'dict_key' => 577,
		'text' => 'FS750T2: 48 RJ-45/10-100TX + 2 combo-gig',
		'processors' => array ('netgear-49-to-50-combo-1000SFP', 'netgear-49-to-50-combo-1000T', 'netgear-any-100TX'),
	),
	'11863.1.1.1' => array
	(
		'dict_key' => 2058,
		'text' => 'TL-SL5428E: 24 RJ-45/10-100TX + 2 1000T + 2 combo ports',
		'processors' => array ('tplink-27-to-28-combo-1000SFP', 'tplink-25-to-28-1000T', 'tplink-any-100T'),
	),
	'11863.6.10.58' => array
	(
		'dict_key' => 1793,
		'text' => 'TL-SG5426: 22 RJ-45/10-100-1000T(X) + 4 combo ports',
		'processors' => array ('tplink-21-to-24-combo-1000SFP', 'tplink-any-1000T'),
	),
	'12356.101.1.3002'=> array
	(
		'dict_key' => 1609,
		'text' => 'FG310B: 10 RJ-45/10-1000T',
		'processors' => array('generic-port-any-1000T'),
	),
	'26543.1.7.6' => array
	(
		'dict_key' => 1888,
		'text' => 'G8264: 48 SFP+ + 4 QSFP+ w/breakout',
		'processors' => array ('ibm-any-SFP+', 'ibm-mgmt'),
	),
	'26543.1.7.7' => array
	(
		'dict_key' => 1887,
		'text' => 'G8052: 48 RJ-45/10-100-1000T(X) + 4 SFP+',
		'processors' => array ('ibm-49-to-52-SFP+', 'ibm-any-1000T'),
	),
	'30065.1.3011.7048.427.3648' => array
	(
		'dict_key' => 1726,
		'text' => 'DCS-7048T-A: 48 1000T + 4 SFP+/10000',
		'processors' => array ('arista-49-to-52-SFP+', 'arista-any-1000T', 'arista-management'),
	),
	'30065.1.3011.7050.3282.52' => array
	(
		'dict_key' => 1731,
		'text' => 'DCS-7050S-52: 52 SFP+',
		'processors' => array ('arista-any-SFP+', 'arista-management'),
	),
	'30065.1.3011.7124.3282' => array
	(
		'dict_key' => 1610,
		'text' => 'DCS-7124S: 24 SFP+/10000',
		'processors' => array ('arista-any-SFP+', 'arista-management'),
	),
	'1991.1.3.57.2.1.1.1' => array
	(
		'dict_key' => 2239,
		'text' => 'ICX6430-48: 48 1000T + 4 SFP+/1000',
		'processors' => array ('brocade-icx-64xx-1000T','brocade-icx-64xx-1000SFP', 'fcx-management'),
	),
	'1991.1.3.48.4.1' => array
	(
		'dict_key' => 2239,
		'text' => 'Stacked ICX6430-48: 48 1000T + 4 SFP+/1000',
		'processors' => array ('brocade-icx-64xx-1000T','brocade-icx-64xx-1000SFP', 'fcx-management'),
	),
	'1991.1.3.48.5.1' => array
	(
		'dict_key' => 2240,
		'text' => 'Stacked ICX6450-48: 48 1000T + 4 SFP+/10000',
		'processors' => array ('brocade-icx-64xx-1000T','brocade-icx-64xx-10000SFP', 'fcx-management'),
	),
	'25506.11.1.82' => array
	(
		'dict_key' => 2176,
		'text' => 'JE006A: 24 RJ-45/10-100-1000T(X) + 4 SFP-1000 ports',
		'processors' => array ('3com-25-to-26-1000SFP', '3com-27-to-28-1000SFP', '3com-any-1000T'),
	),
	'26543.1.7.1' => array
	(
		'dict_key' => 2241,
		'text' => 'IBM System Networking RackSwitch G8000',
		'processors' => array ('ibm-45-to-48-SFP','ibm-49-to-52-SFP+','ibm-any-1000T'),
	),
	'1588.3.3.1.131' => array
	(
		'dict_key' => 2665,
		'text' => 'Brocade VDX 6740',
		'processors' => array ('brocade-vdx-QSFP+','brocade-vdx-SFP+', 'brocade-vdx-management'),
	),
	'1991.1.3.62.2.1.1.1' => array
	(
		'dict_key' => 2666,
		'text' => 'ICX7250-48 48x1000T + 8 SFP+/1000',
		'processors' => array ('brocade-icx-64xx-1000T','brocade-icx-64xx-10000SFP', 'fcx-management'),
	),
	'4413' => array
	(
		'dict_key' => 2624,
		'text' => 'Ubiquiti EdgeSwitch ES-48-LITE',
		'processors' => array ('ubiquiti-chassis-51-to-52-1000SFP','ubiquiti-chassis-any-1000T','ubiquiti-chassis-any-SFP+'),
	),
);

global $swtype_pcre;
$swtype_pcre = array
(
	'/Huawei Versatile Routing Platform Software.+VRP.+Software, Version 5\.30 /s' => 1360,
	'/Huawei Versatile Routing Platform Software.+VRP.+Software, Version 5\.50 /s' => 1361,
	'/Huawei Versatile Routing Platform Software.+VRP.+Software,\s*Version 5\.70 /is' => 1369,
	'/Huawei Versatile Routing Platform Software.+VRP.+Software,\s*Version 5\.110 /is' => 2080,
	'/Huawei Versatile Routing Platform Software.+VRP.+Software,\s*Version 5\.120 /is' => 2081,
	'/Huawei Versatile Routing Platform Software.+VRP.+Software,\s*Version 5\.\d{3,} /is' => 2081, // fallback for all 5.120+
	'/Huawei Versatile Routing Platform Software.+VRP.+Software,\s*Version 8\./is' => 2027, // fallback for all 8.x
	'/^Foundry Networks.+, IronWare Version 05\./' => 1363, // Not an exact format, just a guess.
	'/^Brocade Communications Systems.+, IronWare Version 07\./' => 1364,
	'/^Juniper Networks,.+JUNOS 9\./' => 1366,
	'/^Juniper Networks,.+JUNOS 10\./' => 1367,
	'/^Arista Networks EOS version 4\./' => 1675,
	'/^Dell Force10 OS\b.*\bApplication Software Version: 8(\.\d+){3}/' => 1594,
	'/^Cisco NX-OS\b.*Version 4\.0\b.*/' => 963,
	'/^Cisco NX-OS\b.*Version 4\.1\b.*/' => 964,
	'/^Cisco NX-OS\b.*Version 4\.2\b.*/' => 1365,
	'/^Cisco NX-OS\b.*Version 5\.0\b.*/' => 1410,
	'/^Cisco NX-OS\b.*Version 5\.1\b.*/' => 1411,
	'/^Cisco NX-OS\b.*Version 5\.2\b.*/' => 1809,
	'/^Cisco NX-OS\b.*Version 6\.1\b.*/' => 2028,
	'/^Cisco NX-OS\b.*Version .*/' => 1643, // default 6.0
);

function updateStickerForCell ($cell, $attr_id, $new_value)
{
	if
	(
		isset ($cell['attrs'][$attr_id])
		and $cell['attrs'][$attr_id]['value'] == ''
		and $new_value != ''
	)
		commitUpdateAttrValue ($cell['id'], $attr_id, $new_value);
}

// Accept "X-Y" on input and make sure, that PortInterfaceCompat contains
// a record with IIF id = X and OIF id = Y.
function checkPIC ($port_type_id)
{
	// cache PortInterfaceCompat
	static $compat_array = NULL;
	if (! isset ($compat_array))
	{
		$compat_array = array();
		foreach (getPortInterfaceCompat() as $record)
			$compat_array[$record['iif_id'] . '-' . $record['oif_id']] = 1;
	}

	if (preg_match ('/^(?:(\d+)-)?(\d+)$/', $port_type_id, $m))
	{
		$iif_id = $m[1];
		$oif_id = $m[2];
		if ($iif_id == '')
		{
			$iif_id = 1;
			$port_type_id = '1-' . $port_type_id;
		}
		if (! array_key_exists ($port_type_id, $compat_array))
		{
			commitSupplementPIC ($iif_id, $oif_id);
			$compat_array[$port_type_id] = 1;
		}
	}
}

function doSNMPmining ($object_id, $snmpsetup)
{
	$objectInfo = spotEntity ('object', $object_id);
	$objectInfo['attrs'] = getAttrValues ($object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
	{
		showError ('Endpoint not found. Please either set FQDN attribute or assign an IP address to the object.');
		return;
	}
	if (count ($endpoints) > 1)
	{
		showError ('More than one IP address is assigned to this object, please configure FQDN attribute.');
		return;
	}

	switch ($objectInfo['objtype_id'])
	{
	case 7:   // Router
	case 8:   // Network switch
	case 965: // Wireless
		$device = new RTSNMPDevice ($endpoints[0], $snmpsetup);
		return doSwitchSNMPmining ($objectInfo, $device);
	case 2:
		$device = new APCPowerSwitch ($endpoints[0], $snmpsetup);
		return doPDUSNMPmining ($objectInfo, $device);
	}
}

function doSwitchSNMPmining ($objectInfo, $device)
{
	global $known_switches, $iftable_processors;

	if (FALSE === ($sysObjectID = $device->snmpget ('sysObjectID.0')))
	{
		showError ('Fatal SNMP failure');
		return;
	}
	$sysObjectID = preg_replace ('/^.*( \.1\.3\.6\.1\.|enterprises\.|joint-iso-ccitt\.)([\.[:digit:]]+)$/', '\\2', $sysObjectID);
	if (!isset ($known_switches[$sysObjectID]))
	{
		showError ("Unknown OID '{$sysObjectID}'");
		return;
	}
	$sysName = substr ($device->snmpget ('sysName.0'), strlen ('STRING: '));
	$sysDescr = substr ($device->snmpget ('sysDescr.0'), strlen ('STRING: '));
	$sysDescr = str_replace (array ("\n", "\r"), " ", $sysDescr);  // Make it one line
	$ifDescr_tablename = array_fetch ($known_switches[$sysObjectID], 'ifDescrOID', 'ifDescr');
	showSuccess ($known_switches[$sysObjectID]['text']);
	foreach (array_keys ($known_switches[$sysObjectID]['processors']) as $pkey)
		if (!array_key_exists ($known_switches[$sysObjectID]['processors'][$pkey], $iftable_processors))
		{
			showWarning ('processor "' . $known_switches[$sysObjectID]['processors'][$pkey] . '" not found');
			unset ($known_switches[$sysObjectID]['processors'][$pkey]);
		}
	updateStickerForCell ($objectInfo, 2, $known_switches[$sysObjectID]['dict_key']);
	updateStickerForCell ($objectInfo, 3, $sysName);
	detectSoftwareType ($objectInfo, $sysDescr);
	$desiredPorts = array();
	switch (1)
	{
	case preg_match ('/^9\.1\./', $sysObjectID): // Catalyst w/one AC port
	case preg_match ('/^9\.5\.18/', $sysObjectID):
		$exact_release = preg_replace ('/^.*, Version ([^ ]+), .*$/', '\\1', $sysDescr);
		$major_line = preg_replace ('/^([[:digit:]]+\.[[:digit:]]+)[^[:digit:]].*/', '\\1', $exact_release);
		$ios_codes = array
		(
			'12.0' => 244,
			'12.1' => 251,
			'12.2' => 252,
			'15.0' => 1901,
			'15.1' => 2082,
			'15.2' => 2142,
		);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		if (array_key_exists ($major_line, $ios_codes))
			updateStickerForCell ($objectInfo, 4, $ios_codes[$major_line]);
		$sysChassi = $device->snmpget ('1.3.6.1.4.1.9.3.6.3.0');
		if ($sysChassi !== FALSE || $sysChassi !== NULL)
			updateStickerForCell ($objectInfo, 1, str_replace ('"', '', substr ($sysChassi, strlen ('STRING: '))));
		checkPIC ('1-29');
		addDesiredPort ($desiredPorts, 'con0', '1-29', 'console', ''); // RJ-45 RS-232 console
		if (preg_match ('/Cisco IOS Software, C2600/', $sysDescr))
			addDesiredPort ($desiredPorts, 'aux0', '1-29', 'auxillary', ''); // RJ-45 RS-232 aux port
		if ($sysObjectID == '9.1.956')
		{
			// models with two AC inputs
			checkPIC ('1-16');
			addDesiredPort ($desiredPorts, 'AC-in-1', '1-16', 'AC1', '');
			addDesiredPort ($desiredPorts, 'AC-in-2', '1-16', 'AC2', '');
		}
		elseif ($sysObjectID != '9.1.749' && $sysObjectID != '9.1.920')
		{
			// assume the rest have one AC input, but exclude blade devices
			checkPIC ('1-16'); // AC input
			addDesiredPort ($desiredPorts, 'AC-in', '1-16', '', '');
		}
		break;
	case preg_match ('/^9\.5\.42/', $sysObjectID): // Catalyst 2948 running CatOS
	case preg_match ('/^9\.6\.1\./', $sysObjectID): // Cisco SMB series switches (200, 220, 300, 500)
	case preg_match ('/^2011\.2\.239?\./', $sysObjectID): // Huawei
		checkPIC ('1-681');
		addDesiredPort ($desiredPorts, 'con0', '1-681', 'console', ''); // DB-9 RS-232 console
		checkPIC ('1-16');
		addDesiredPort ($desiredPorts, 'AC-in', '1-16', '', '');
		break;
	case preg_match ('/^9\.12\.3\.1\.3\./', $sysObjectID): // Nexus
		$exact_release = preg_replace ('/^.*, Version ([^ ]+), .*$/', '\\1', $sysDescr);
		$major_line = preg_replace ('/^([[:digit:]]+\.[[:digit:]]+)[^[:digit:]].*/', '\\1', $exact_release);
		$nxos_codes = array
		(
			'4.0' => 963,
			'4.1' => 964,
			'4.2' => 1365,
			'5.0' => 1410,
			'5.1' => 1411,
			'5.2' => 1809,
			'6.0' => 1643,
			'6.1' => 2028,
		);
		if (array_key_exists ($major_line, $nxos_codes))
			updateStickerForCell ($objectInfo, 4, $nxos_codes[$major_line]);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		$sysChassi = $device->snmpget ('1.3.6.1.2.1.47.1.1.1.1.11.149');
		if ($sysChassi !== FALSE || $sysChassi !== NULL)
			updateStickerForCell ($objectInfo, 1, str_replace ('"', '', substr ($sysChassi, strlen ('STRING: '))));
		checkPIC ('1-29');
		addDesiredPort ($desiredPorts, 'con0', '1-29', 'console', ''); // RJ-45 RS-232 console
		checkPIC ('1-16'); // AC input
		addDesiredPort ($desiredPorts, 'AC-in-1', '1-16', 'AC1', '');
		addDesiredPort ($desiredPorts, 'AC-in-2', '1-16', 'AC2', '');
		break;
	case preg_match ('/^11\.2\.3\.7\.11\.(\d+)$/', $sysObjectID, $matches): // ProCurve
		$console_per_product = array
		(
			33 => '1-29', # RJ-45 RS-232
			63 => '1-29',
			78 => '1-29',
			79 => '1-29',
			80 => '1-29',
			84 => '1-29',
			86 => '1-29',
			87 => '1-29',
			94 => '1-29',
			95 => '1-29',
			154 => '1-29',
			19 => '1-681', # DB-9 RS-232
			31 => '1-681',
			34 => '1-681',
			58 => '1-681',
			59 => '1-681',
		);
		if (array_key_exists ($matches[1], $console_per_product))
		{
			checkPIC ($console_per_product[$matches[1]]);
			addDesiredPort ($desiredPorts, 'console', $console_per_product[$matches[1]], 'console', '');
		}
		$oom_per_product = array
		(
			33 => '1-24', # RJ-45 100Mb
		);
		if (array_key_exists ($matches[1], $oom_per_product))
		{
			checkPIC ($oom_per_product[$matches[1]]);
			addDesiredPort ($desiredPorts, 'mgmt', $oom_per_product[$matches[1]], 'mgmt', '');
		}
		checkPIC ('1-16');
		addDesiredPort ($desiredPorts, 'AC-in', '1-16', '', '');
		$exact_release = preg_replace ('/^.* revision ([^ ]+), .*$/', '\\1', $sysDescr);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		break;
	case preg_match ('/^2636\.1\.1\.1\.2\.(30|31|43)/', $sysObjectID): // Juniper EX2200/EX3200/EX4200
		$sw_version = preg_replace ('/^.*, kernel JUNOS ([^ ]+).*$/', '\\1', $sysDescr);
		updateStickerForCell ($objectInfo, 5, $sw_version);
		// one RJ-45 RS-232 and one AC port (it could be DC, but chances are it's AC)
		checkPIC ('1-29');
		addDesiredPort ($desiredPorts, 'con', '1-29', 'CON', ''); // RJ-45 RS-232 console
		checkPIC ('1-16');
		addDesiredPort ($desiredPorts, 'AC-in', '1-16', '', '');
		// Juniper uses the same sysObjectID for multiple HW models, override if necessary
		if (preg_match ('/^Juniper Networks, Inc. ex3200-48t internet router/', $sysDescr))
			updateStickerForCell ($objectInfo, 2, 902);
		if (preg_match ('/^Juniper Networks, Inc. ex4200-48t internet router/', $sysDescr))
			updateStickerForCell ($objectInfo, 2, 907);
		break;
	case preg_match ('/^1991\.1\.3\.53\.1\.2$/', $sysObjectID): // TurboIron 24X
	case preg_match ('/^2636\.1\.1\.1\.2\./', $sysObjectID): // Juniper
		checkPIC ('1-681');
		addDesiredPort ($desiredPorts, 'console', '1-681', 'console', ''); // DB-9 RS-232 console
		break;
	case preg_match ('/^1991\.1\.3\.45\./', $sysObjectID): // snFGSFamily
	case preg_match ('/^1991\.1\.3\.46\./', $sysObjectID): // snFLSFamily
	case preg_match ('/^1991\.1\.3\.54\.2\.4\.1\.1$/', $sysObjectID): // FCX 648
		$exact_release = preg_replace ('/^.*, IronWare Version ([^ ]+) .*$/', '\\1', $sysDescr);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		# FOUNDRY-SN-AGENT-MIB::snChasSerNum.0
		$sysChassi = $device->snmpget ('enterprises.1991.1.1.1.1.2.0');
		if ($sysChassi !== FALSE || $sysChassi !== NULL)
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
				addDesiredPort ($desiredPorts, $PSU_cooked, '1-16', '', '');
			}
		}
		# fixed console port
		checkPIC ('1-681');
		addDesiredPort ($desiredPorts, 'console', '1-681', 'console', ''); // DB-9 RS-232 console
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
		addDesiredPort ($desiredPorts, 'console', '1-681', 'console', ''); // DB-9 RS-232
		checkPIC ('1-16');
		addDesiredPort ($desiredPorts, 'AC-in', '1-16', '', '');
		break;
	case preg_match ('/^6027\.1\./', $sysObjectID): # Force10
		addDesiredPort ($desiredPorts, 'aux0', '1-29', 'RS-232', ''); // RJ-45 RS-232 console
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
			addDesiredPort ($desiredPorts, 'PSU0', '1-16', 'PSU0', '');
		}
		# F10-S-SERIES-CHASSIS-MIB::chSysPowerSupplyType.1.2
		if ($device->snmpget ('enterprises.6027.3.10.1.2.3.1.3.1.2') == 'INTEGER: 1')
		{
			checkPIC ('1-16');
			addDesiredPort ($desiredPorts, 'PSU1', '1-16', 'PSU1', '');
		}
		if ($serialNo != '')
			updateStickerForCell ($objectInfo, 1, str_replace ('"', '', substr ($serialNo, strlen ('STRING: '))));
		break;
	case preg_match ('/^171\.10\.63\.8/', $sysObjectID): // D-Link DES-3052
	case preg_match ('/^202\.20\./', $sysObjectID): // SMC TigerSwitch
	case preg_match ('/^674\.10895\.4/', $sysObjectID): // Dell PowerConnect
	case preg_match ('/^674\.10895\.300(3|4|7|9)/', $sysObjectID):
	case preg_match ('/^674\.10895\.301(0|4|7|9)/', $sysObjectID):
	case preg_match ('/^674\.10895\.302(0|1|8)/', $sysObjectID):
	case preg_match ('/^3955\.6\.1\.20(24|48)\.1/', $sysObjectID): // Linksys
	case preg_match ('/^3955\.6\.50(24|48)/', $sysObjectID): // Linksys
	case preg_match ('/^4526\.100\./', $sysObjectID): // NETGEAR (with console)
	case preg_match ('/^11863\.1\.1\.1/', $sysObjectID): // TP-Link
	case preg_match ('/^11863\.6\.10\.58/', $sysObjectID):
		// one DB-9 RS-232 and one AC port
		checkPIC ('1-681');
		addDesiredPort ($desiredPorts, 'console', '1-681', '', ''); // DB-9 RS-232
		checkPIC ('1-16');
		addDesiredPort ($desiredPorts, 'AC-in', '1-16', '', '');
		break;
	case preg_match ('/^388\.18/', $sysObjectID): // Motorola RFS 4000
		// one RJ-45 RS-232 and one AC port
		checkPIC ('1-29');
		addDesiredPort ($desiredPorts, 'console', '1-29', 'console', '');
		checkPIC ('1-16');
		addDesiredPort ($desiredPorts, 'AC-in', '1-16', '', '');
		break;
	case preg_match ('/^207\.1\.14\./', $sysObjectID): // Allied Telesyn
		// one RJ-45 RS-232 and two AC ports
		checkPIC ('1-29');
		addDesiredPort ($desiredPorts, 'console', '1-29', 'console', '');
		checkPIC ('1-16');
		addDesiredPort ($desiredPorts, 'AC-in-1', '1-16', 'AC1', '');
		addDesiredPort ($desiredPorts, 'AC-in-2', '1-16', 'AC2', '');
		break;
	case preg_match ('/^674\.10895\.3000/', $sysObjectID):
		// one DB-9 RS-232, one 100Mb OOB mgmt, and one AC port
		checkPIC ('1-681');
		addDesiredPort ($desiredPorts, 'console', '1-681', '', ''); // DB-9 RS-232
		checkPIC ('1-19');
		addDesiredPort ($desiredPorts, 'mgmt', '1-19', '', '');
		checkPIC ('1-16');
		addDesiredPort ($desiredPorts, 'AC-in', '1-16', '', '');
		break;
	case preg_match ('/^43\.1\.16\.4\.3\./', $sysObjectID): // 3Com
		$sw_version = preg_replace('/^.* Version 3Com OS ([^ ]+).*$/', '\\1', $sysDescr);
		updateStickerForCell ($objectInfo, 5, $sw_version);

		// one RJ-45 RS-232 and one AC port
		checkPIC ('1-29');
		addDesiredPort ($desiredPorts, 'console', '1-29', '', ''); // RJ-45 RS-232 console
		checkPIC ('1-16');
		addDesiredPort ($desiredPorts, 'AC-in', '1-16', '', '');
		break;
	case preg_match ('/^10977\.11825\.11833\.97\.25451\.12800\.100\.4\.4/', $sysObjectID): // Netgear
		$sw_version = preg_replace('/^.* V([^ ]+).*$/', '\\1', $sysDescr);
		updateStickerForCell ($objectInfo, 5, $sw_version);

		// one AC port, no console
		checkPIC ('1-16');
		addDesiredPort ($desiredPorts, 'AC-in', '1-16', '', '');
		break;
	case preg_match ('/^171\.10\.76\.10/', $sysObjectID): // D-Link DGS-1210-24
	case preg_match ('/^207\.1\.4\./', $sysObjectID): // Allied Telesyn AT-GS950/24
	case preg_match ('/^4526\.100\.4\.(6|10)/', $sysObjectID): // NETGEAR (without console)
		// one AC port, no console
		checkPIC ('1-16');
		addDesiredPort ($desiredPorts, 'AC-in', '1-16', '', '');
		break;
	case preg_match ('/^30065\.1\.3011\./', $sysObjectID): // Arista
		checkPIC ('1-29');
		addDesiredPort ($desiredPorts, 'console', '1-29', 'IOIOI', '');
		$sw_version = preg_replace ('/^Arista Networks EOS version (.+) running on .*$/', '\\1', $sysDescr);
		updateStickerForCell ($objectInfo, 5, $sw_version);
		if ('' != $serialNo = $device->snmpget ('mib-2.47.1.1.1.1.11.1')) # entPhysicalSerialNumber.1
			updateStickerForCell ($objectInfo, 1, str_replace ('"', '', substr ($serialNo, strlen ('STRING: '))));
		break;
	case preg_match ('/^119\.1\.203\.2\.2\./', $sysObjectID): # NEC
		checkPIC ('1-681');
		addDesiredPort ($desiredPorts, 'console 0', '1-681', 'console', '');
		checkPIC ('1-16');
		addDesiredPort ($desiredPorts, 'PS1', '1-16', '', '');
		addDesiredPort ($desiredPorts, 'PS2', '1-16', '', '');
		break;
	case preg_match ('/^26543\.1\.7\./', $sysObjectID): # IBM
		checkPIC ('1-29');
		addDesiredPort ($desiredPorts, 'console', '1-29', '', ''); # RJ-45 RS-232 console
		break;
	default: // Nortel...
		break;
	}
	$ifInfo = array();
	foreach ($device->snmpwalkoid ($ifDescr_tablename) as $oid => $value)
	{
		$randomindex = preg_replace ("/^.*${ifDescr_tablename}\.(.+)\$/", '\\1', $oid);
		$value = trim (preg_replace ('/^[^:]+: (.+)$/', '\\1', $value), '"');
		$ifInfo[$randomindex]['ifDescr'] = $value;
	}
	foreach ($device->snmpwalkoid ('ifPhysAddress') as $oid => $value)
	{
		$randomindex = preg_replace ("/^.*ifPhysAddress\.(.+)\$/", '\\1', $oid);
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
		$ifInfo[$randomindex]['ifPhysAddress'] = implode ('', $addrbytes);
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
			addDesiredPort ($desiredPorts, $newname, $iftable_processors[$processor_name]['dict_key'], $newlabel, $iface['ifPhysAddress']);
			if (!$iftable_processors[$processor_name]['try_next_proc']) // done with this port
				continue 2;
		}
	// Sync ports
	try
	{
		syncObjectPorts ($objectInfo['id'], $desiredPorts);
	}
	catch (InvalidArgException $iae)
	{
		throw $iae->newIRAE();
	}
	// No failure up to this point, thus leave current tab for the "Ports" one.
	return buildRedirectURL (NULL, 'ports');
}

function doPDUSNMPmining ($objectInfo, $switch)
{
	global $known_APC_SKUs;
	if (FALSE !== ($dict_key = array_search ($switch->getHWModel(), $known_APC_SKUs)))
		updateStickerForCell ($objectInfo, 2, $dict_key);
	updateStickerForCell ($objectInfo, 1, $switch->getHWSerial());
	updateStickerForCell ($objectInfo, 3, $switch->getName());
	updateStickerForCell ($objectInfo, 5, $switch->getFWRev());
	checkPIC ('1-16');
	$desiredPorts = array();
	addDesiredPort ($desiredPorts, 'input', '1-16', 'input', '');
	$portno = 1;
	foreach ($switch->getPorts() as $name => $port)
	{
		$label = $port[0] != '' ? $port[0] : $portno;
		checkPIC ('1-1322');
		addDesiredPort ($desiredPorts, $portno, '1-1322', $port[0], '');
		$portno++;
	}
	try
	{
		syncObjectPorts ($objectInfo['id'], $desiredPorts);
	}
	catch (InvalidArgException $iae)
	{
		throw $iae->newIRAE();
	}
	showSuccess ("Added ${portno} port(s)");
	return buildRedirectURL (NULL, 'ports');
}

// APC SNMP code by Russ Garrett
define('APC_STATUS_ON', 1);
define('APC_STATUS_OFF', 2);
define('APC_STATUS_REBOOT', 3);

class RTSNMPDevice
{
	protected $snmp;

	function __construct($hostname, $snmpsetup)
	{
		switch ($snmpsetup['version'])
		{
		case 1:
		default:
			$this->snmp = new RTSNMPv1($hostname, $snmpsetup);
			break;
		case 2:
			$this->snmp = new RTSNMPv2($hostname, $snmpsetup);
			break;
		case 3:
			$this->snmp = new RTSNMPv3($hostname, $snmpsetup);
			break;
		}
	}

	function getName()
	{
		return $this->getString('sysName.0');
	}

	function getDescription()
	{
		return $this->getString('sysDescr.0');
	}

	function snmpget($oid)
	{
		return $this->snmp->snmpget($oid);
	}

	function snmpwalkoid($oid)
	{
		return $this->snmp->snmpwalkoid($oid);
	}

	protected function snmpSet($oid, $type, $value)
	{
		return $this->snmp->snmpset($oid, $type, $value);
	}

	protected function getString($oid)
	{
		return trim(str_replace('STRING: ', '', $this->snmp->snmpget($oid)), '"');
	}
}

abstract class RTSNMP
{
	protected $hostname;
	protected $snmpsetup;

	function __construct($hostname, $snmpsetup)
	{
		$this->hostname = $hostname;
		$this->snmpsetup = $snmpsetup;
	}

	abstract function snmpget($oid);
	abstract function snmpset($oid, $type, $value);
	abstract function snmpwalkoid($oid);
}

class RTSNMPv1 extends RTSNMP
{
	function snmpget($oid)
	{
		return snmpget($this->hostname, $this->snmpsetup['community'], $oid);
	}

	function snmpset($oid, $type, $value)
	{
		return snmpset($this->hostname, $this->snmpsetup['community'], $oid, $type, $value);
	}

	function snmpwalkoid($oid)
	{
		return snmpwalkoid($this->hostname, $this->snmpsetup['community'], $oid);
	}
}

class RTSNMPv2 extends RTSNMP
{
	function snmpget($oid)
	{
		return snmp2_get($this->hostname, $this->snmpsetup['community'], $oid);
	}

	function snmpset($oid, $type, $value)
	{
		return snmp2_set($this->hostname, $this->snmpsetup['community'], $oid, $type, $value);
	}

	function snmpwalkoid($oid)
	{
		return snmp2_real_walk($this->hostname, $this->snmpsetup['community'], $oid);
	}
}

class RTSNMPv3 extends RTSNMP
{
	function snmpget($oid)
	{
		return snmp3_get($this->hostname, $this->snmpsetup['sec_name'], $this->snmpsetup['sec_level'], $this->snmpsetup['auth_protocol'], $this->snmpsetup['auth_passphrase'], $this->snmpsetup['priv_protocol'], $this->snmpsetup['priv_passphrase'], $oid);
	}

	function snmpset($oid, $type, $value)
	{
		return snmp3_set($this->hostname, $this->snmpsetup['sec_name'], $this->snmpsetup['sec_level'], $this->snmpsetup['auth_protocol'], $this->snmpsetup['auth_passphrase'], $this->snmpsetup['priv_protocol'], $this->snmpsetup['priv_passphrase'], $oid, $type, $value);
	}

	function snmpwalkoid($oid)
	{
		return snmp3_real_walk($this->hostname, $this->snmpsetup['sec_name'], $this->snmpsetup['sec_level'], $this->snmpsetup['auth_protocol'], $this->snmpsetup['auth_passphrase'], $this->snmpsetup['priv_protocol'], $this->snmpsetup['priv_passphrase'], $oid);
	}
}

class APCPowerSwitch extends RTSNMPDevice
{
	protected $snmpMib = 'SNMPv2-SMI::enterprises.318';

	function getPorts()
	{
		$data = $this->snmpwalkoid("{$this->snmpMib}.1.1.12.3.3.1.1.2");
		$status = $this->snmpwalkoid("{$this->snmpMib}.1.1.12.3.3.1.1.4");
		$out = array();
		foreach ($data as $id => $d)
			$out[] = array(trim(str_replace('STRING: ', '', $d), '"'), str_replace('INTEGER: ', '', $status[$id]));
		return $out;
	}

	function getPortStatus($id)
	{
		return trim($this->snmpget("{$this->snmpMib}.1.1.12.3.3.1.1.4.$id"), 'INTEGER: ');
	}

	function getPortName($id)
	{
		return trim(str_replace('STRING: ', '', $this->snmpget("{$this->snmpMib}.1.1.12.3.3.1.1.2.$id")), '"');
	}

	function setPortName($id, $name)
	{
		return $this->snmpset("{$this->snmpMib}.1.1.4.5.2.1.3.$id", 's', $name);
	}

	function portOff($id)
	{
		return $this->snmpSet("{$this->snmpMib}.1.1.12.3.3.1.1.4.$id", 'i', APC_STATUS_OFF);
	}

	function portOn($id)
	{
		return $this->snmpSet("{$this->snmpMib}.1.1.12.3.3.1.1.4.$id", 'i', APC_STATUS_ON);
	}

	function portReboot($id)
	{
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
	if (! preg_match ('/^[0-9a-f]{2}(:[0-9a-f]{2}){5}$/i', $addr))
		throw new InvalidArgException ('addr', $addr, 'invalid MAC address format');
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
