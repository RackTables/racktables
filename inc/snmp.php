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

$iftable_processors['catalyst-any-bp/1000T'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?([[:digit:]]+)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => 1087,
	'label' => '',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-any-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?([[:digit:]]+)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => 1077,
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-any-1000GBIC'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?([[:digit:]]+)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => 1078,
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-21-to-24-combo-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(21|22|23|24)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => 1077,
	'label' => '\\2',
	'try_next_proc' => TRUE,
);

$iftable_processors['catalyst-chassis-45-to-48-combo-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(45|46|47|48)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => 1077,
	'label' => '\\2',
	'try_next_proc' => TRUE,
);

$iftable_processors['catalyst-chassis-uplinks-10000X2'] = array
(
	'pattern' => '@^TenGigabitEthernet([[:digit:]]+/)?([[:digit:]]+)$@',
	'replacement' => 'te\\1\\2',
	'dict_key' => 1080,
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-25-to-28-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(25|26|27|28)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => 1077,
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-chassis-49-to-52-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(49|50|51|52)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => 1077,
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-13-to-16-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(13|14|15|16)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => 1077,
	'label' => '\\2',
	'try_next_proc' => FALSE,
);

$iftable_processors['catalyst-21-to-24-1000SFP'] = array
(
	'pattern' => '@^GigabitEthernet([[:digit:]]+/)?(21|22|23|24)$@',
	'replacement' => 'gi\\1\\2',
	'dict_key' => 1077,
	'label' => '\\2',
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
	'dict_key' => 1077,
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

$known_switches = array // key is system OID w/o "enterprises" prefix
(
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
		'text' => 'GSM7224: 20 RJ-45/10-100-1000T(X) + 4 combo-gig',
		'processors' => array ('netgear-chassis-21-to-24-1000SFP', 'netgear-chassis-21-to-24-1000Tcombo', 'netgear-chassis-any-1000T'),
	),
	'45.3.68.5' => array
	(
		'dict_key' => 1085,
		'text' => 'BES50GE-12T PWR: 12 RJ-45/10-100-1000T(X)',
		'processors' => array ('nortel-any-1000T'),
	),
);

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
		if (array_key_exists ($major_line, $ios_codes))
			updateStickerForCell ($objectInfo, 4, $ios_codes[$major_line]);
		$sysChassi = snmpget ($endpoints[0], $community, '1.3.6.1.4.1.9.3.6.3.0');
		if ($sysChassi !== FALSE or $sysChassi !== NULL)
			updateStickerForCell ($objectInfo, 1, str_replace ('"', '', substr ($sysChassi, strlen ('STRING: '))));
		commitAddPort ($object_id, 'con0', 29, 'console', ''); // RJ-45 RS-232 console
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
		if (array_key_exists ($major_line, $nxos_codes))
			updateStickerForCell ($objectInfo, 4, $nxos_codes[$major_line]);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		$log = mergeLogs ($log, oneLiner (81, array ('nexus-generic')));
		break;
	case preg_match ('/^11\.2\.3\.7\.11\./', $sysObjectID): // ProCurve
		$exact_release = ereg_replace ('^.* revision ([^ ]+), .*$', '\\1', $sysDescr);
		updateStickerForCell ($objectInfo, 5, $exact_release);
		$log = mergeLogs ($log, oneLiner (81, array ('procurve-generic')));
		break;
	case preg_match ('/^4526\.100\.2\./', $sysObjectID): // NETGEAR
		commitAddPort ($object_id, 'console', 681, 'console', ''); // DB-9 RS-232 console
		$log = mergeLogs ($log, oneLiner (81, array ('netgear-generic')));
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

// APC SNMP code by Russ Garrett

function doPDUSNMPmining ($object_id, $community)
{
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	$switch = new APCPowerSwitch($endpoints[0], 'public');
	$log = array();
	$sysName = $switch->getName();
	$error = commitUpdateAttrValue ($object_id, 3, $sysName);
	if ($error == TRUE)
		$log[] = array ('code' => 'success', 'message' => 'FQDN set to ' . $sysName);
	else
		$log[] = array ('code' => 'error', 'message' => 'Failed settig FQDN: ' . $error);
	
	$updated = 0;
	$ports = $switch->getPorts();
	foreach ($ports as $name => $port) {
		if (commitUpdatePortLabels($object_id, $name, 16, $port[0])) {
			$updated++;
		}
	}
	if ($updated > 0)
		$log[] = array ('code' => 'success', 'message' => "Updated ${updated} port(s)");

	return $log;
}

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
}

?>
