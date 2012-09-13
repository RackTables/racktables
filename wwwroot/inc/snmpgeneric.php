<?php

/********************************************
 *
 * RackTables 0.19.x snmpgeneric extension
 *
 *	sync an RackTables object with an SNMP device.
 *
 *	Should work with almost any SNMP capable device.
 *
 *	reads SNMP tables:
 *		- system
 *		- ifTable
 *		- ifxTable
 *		- ipAddrTable (ipv4 only)
 *		- ipAddressTable (ipv4 + ipv6)
 *
 *	Features:
 *		- update object attributes
 *		- create networks
 *		- create ports
 *		- add and bind ip addresses
 *
 *	Known to work with:
 *		- Enterasys SecureStacks, S-Series
 *		- cisco 2620XM (thx to Rob)
 *		- hopefully many others
 *
 *
 *	Usage:
 *
 *		1. select "SNMP generic sync" tap
 *		2. select your SNMP config (host, v1, v2c or v3, ...)
 *		3. hit "Show List"
 *		4. you will see a selection of all information that could be retrieved
 *		5. select what should be updated and/or created
 *		6. hit "Create" Button to make changes to RackTables
 *		7. repeat step 1. to 6. as often as you like / need
 *
 *
 * needs PHP 5
 *
 * TESTED on FreeBSD 9.0, nginx/1.0.12, php 5.3.10, NET-SNMP 5.7.1
 *	and RackTables 0.19.11
 *
 * (c)2012 Maik Ehinger <m.ehinger@ltur.de>
 */

/****
 * INSTALL
 *
 * add to inc/local.php
 *	include 'inc/snmpgeneric.php';
 *
 * or create inc/local.php with content
 *
 *	<?php
 *		include 'inc/snmpgeneric.php';
 *	?>
 *
 */

/* TODOs
 *
 *  - code cleanup
 *
 *  - update visible label on ifAlias change !?
 *
 *  - test if device supports mibs
 *  - gethostbyaddr / gethostbyname host list
 *  - correct iif_name display if != 1
 *
 *  - set more Object attributs / fields
 *
 */

/*************************

 * Change Log
 *
 * 09.12.11	minor cleanups
 * 10.02.12	make host selectable
 * 16.02.12	use getConfigVar('DEFAULT_SNMP_COMMUNITY');
 *		make snmp port types ignorable (see sg_ifType2oif_id array)
 *		add create_noconnector_ports
 * 17.02.12	changed operator & to &&
 *		make attributes, add port, add ip and port type changeable before create
 *		add sg_oid2attr
 *		add trigger (prepared only)
 *		add sg_ifType_ignore
 * 19.02.12	add ifAlias to visible label
 *		allow ifName input if empty (preset with ifDescr)
 * 20.02.12	change attribute code
 *		add $sg_known_sysObjectIDs (add $known_switches from snmp.php) to set HW Type
 *		added attribute processor function code
 * 21.02.12	add vendor / device specific ports
 *		change processor function code
 *			allow to return attributes and ports
 *		add check / uncheck all (doesn't work with IE)
 *		hide snmpv3 settings if not needed
 *		add ifInOctets and ifOutOctets to interface list
 * 22.02.12	add hrefs to l2address and ipaddr
 *		add readonly textfields for ifDescr and ifAlias
 * 23.02.12	change sysObjectID merge code
 *		change attrib processing code
 *		add regex processor function
 * 25.02.12	prefix global vars with sg_
 *		add SW version for Enterasys devices
 * 29.02.12	add snmpgeneric_pf_entitymib
 * 03.03.12	fix SNMPv3 support (tested with Enterasys only)
 *		snmpconfig form changes
 * 07.03.12	add commitUpdatePortl2address handling
 *		change to allow multiple ip addresses per interface
 *		get snmp ipv6 addresses ( !!! column order changed !!! ) (experimental)
 * 08.03.12	snmpconfig focus submit button onload
 *		add snmpgeneric_pf_swtype (experimental)
 * 09.03.12	add snmptranslate (set $sg_cmd_snmptranslate) (experimental)
 *		exclude ipv6 link-local addresses (fe80:) (experimental)
 *		handle reserved ip addresses
 *		destroy remaining foreach variables
 * 10.03.12	add missing IPv4 / IPv6 spaces (experimental)
 * 11.03.12	add bcast to ip address
 *		foreach by reference workaround (&$attr)
 *		changed ipv6 link-local addresses handling (fe80:) now ignoring ipv6z address type (experimental)
 * 12.03.12	add regex replacement
 *		changes snmpgeneric_pf_swtype (add oid, regex, replacement)
 *		fix update mac
 * 13.03.12	changed ipv6 link-local addresses handling (fe80:) again (experimental)
 *		update mac fix
 *		don't set attributes if values are equal
 * 20.03.12	correct broadcast calculation for ipaddresstable ipv4
 *		removed disabled checkboxes
 *		snmpgeneric_pf_catalyst multiline sysDescr fix
 *		add device pf
 * 26.03.12	ip spaces create by default
 *		set create button focus
 *			add confirm message
 *		ip space create fix (invalid ipv6 prefix)
 * 29.03.12	fix cisco OEM S/N 1
 * 02.04.12	add snmpgeneric_pf_ciscoflash
 * 03.04.12	add ciscoMemoryPoolMIB
 * 18.04.12	ciscomemory/flash round up values (ceil)
 * 16.07.12	add "ask me" to snmp host selection
 * 19.07.12	change description
 * 26.07.12	change INSTALL section
 * 01.08.12	fix interfaceserror handling in ifSNMP
 *		suppress SNMP "No Such Object available on this agent at this OID" warning
 *		fix whitespaces
 * 03.08.12	don't display ips with 0.0.0.0 netmask
 *
 */

require_once('snmp.php');

$tab['object']['snmpgeneric'] = 'SNMP Generic sync';
$tabhandler['object']['snmpgeneric'] = 'snmpgeneric_tabhandler';
//$trigger['object']['snmpgeneric'] = 'snmpgeneric_tabtrigger';

$ophandler['object']['snmpgeneric']['create'] = 'snmpgeneric_opcreate';

/* snmptranslate command */
$sg_cmd_snmptranslate = '/usr/local/bin/snmptranslate';

/* create ports without connector */
$sg_create_noconnector_ports = FALSE;

/* deselect add port for this snmp port types */
$sg_ifType_ignore = array(
	  '1',	/* other */
	 '24',	/* softwareLoopback */
	 '23',	/* ppp */
	 '33',	/* rs232 */
	 '34',	/* para */
	 '53',	/* propVirtual */
	 '77',	/* lapd */
	'131',	/* tunnel */
	'136',	/* l3ipvlan */
	'160',	/* usb */
	'161',	/* ieee8023adLag */
);

/* ifType to RT oif_id mapping */
$sg_ifType2oif_id = array(
	/* 440 causes SQLSTATE[23000]: Integrity constraint violation:
	 *				1452 Cannot add or update a child row:
	 *					a foreign key constraint fails
	 */
	//  '1' => 440,	/* other => unknown 440 */
	  '1' => 1469,	/* other => virutal port 1469 */
	  '6' => 24,	/* ethernetCsmacd => 1000BASE-T 24 */
	 '24' => 1469,	/* softwareLoopback => virtual port 1469 */
	 '33' => 1469,	/*  rs232 => RS-232 (DB-9) 681 */
	 '34' => 1469,	/* para => virtual port 1469 */
	 '53' => 1469,	/* propVirtual => virtual port 1469 */
	 '62' => 1195,	/* fastEther => 100BASE-FX 1195 */
	'131' => 1469,	/* tunnel => virtual port 1469 */
	'136' => 1469,	/* l3ipvlan => virtual port 1469 */
	'160' => 1469,	/* usb => virtual port 1469 */
	'161' => 1469,	/* ieee8023adLag => virtual port 1469 */
);

/* -------------------------------------------------- */

/* snmp vendor list http://www.iana.org/assignments/enterprise-numbers */

$sg_known_sysObjectIDs = array
(
	/* ------------ default ------------ */
	'default' => array
	(
	//	'text' => 'default',
		'pf' => array('snmpgeneric_pf_entitymib'),
		'attr' => array
		(
			 2 => array('pf' => 'snmpgeneric_pf_hwtype'),					/* HW Typ*/
			 3 => array('oid' => 'sysName.0'),
				/* FQDN check only if regex matches */
			 //3 => array('oid' => 'sysName.0', 'regex' => '/^[^ .]+(\.[^ .]+)+\.?/', 'uncheck' => 'no FQDN'),
			 4 => array('pf' => 'snmpgeneric_pf_swtype', 'uncheck' => 'experimental'),	/* SW type */
			 14 => array('oid' => 'sysContact.0'),						/* Contact person */
			// 1235 => array('value' => 'Constant'),
		),
		'port' => array
		(
			// 'AC-in' => array('porttypeid' => '1-16', 'uncheck' => 'uncheck reason/comment'),
			// 'name' => array('porttypeid' => '1-24', 'ifDescr' => 'visible label'),
		),
	),

	/* ------------ ciscoSystems --------------- */
/*	'9' => array
 *	(
 *		'text' => 'ciscoSystems',
 *	),
 */
	'9.1' => array
	(
		'text' => 'ciscoProducts',
		'attr' => array(
				4 => array('pf' => 'snmpgeneric_pf_catalyst'), /* SW type/version */
				16 => array('pf' => 'snmpgeneric_pf_ciscoflash'), /*  flash memory */

				),

	),
	/* ------------ Microsoft --------------- */
	'311' => array
	(
		'text' => 'Microsoft',
		'attr' => array(
				4 => array('pf' => 'snmpgeneric_pf_swtype', 'oid' => 'sysDescr.0', 'regex' => '/.* Windows Version (.*?) .*/', 'replacement' => 'Windows \\1', 'uncheck' => 'TODO RT matching'), /*SW type */
				),
	),
	/* ------------ Enterasys --------------- */
	'5624' => array
	(
		'text' => 'Enterasys',
		'attr' => array(
				4 => array('pf' => 'snmpgeneric_pf_enterasys'), /* SW type/version */
				),
	),

	/* Enterasys N3 */
	'5624.2.1.53' => array
	(
		'dict_key' => 50000,
		'text' => 'N3',
	),

	'5624.2.2.284' => array
	(
		'dict_key' => 50002,
		'text' => 'Securestack C2',
	),

	'5624.2.1.98' => array
	(
		'dict_key' => 50002,
		'text' => 'Securestack C3',
	),

	'5624.2.1.100' => array
	(
		'dict_key' => 50002,
		'text' => 'Securestack B3',
	),

	'5624.2.1.128' => array
	(
		'dict_key' => 50001,
		'text' => 'S-series',
	),

	'5624.2.1.129' => array
	(
		'dict_key' => 50001,
		'text' => 'S-series',
	),

	'5624.2.1.137' => array
	(
		'dict_key' => 50002,
		'text' => 'Securestack B5 POE',
	),

	/* S3 */
	'5624.2.1.131' => array
	(
		'dict_key' => 50001,
		'text' => 'S-series',
	),

	/* S4 */
	'5624.2.1.132' => array
	(
		'dict_key' => 50001,
		'text' => 'S-series'
	),

	/* S8 */
	'5624.2.1.133' => array
	(
		'dict_key' => 50001,
		'text' => 'S-series'
	),

	/* ------------ net-snmp --------------- */
	'8072' => array
	(
		'text' => 'net-snmp',
		'attr' => array(
				4 => array('pf' => 'snmpgeneric_pf_swtype', 'oid' => 'sysDescr.0', 'regex' => '/(.*?) .*? (.*?) .*/', 'replacement' => '\\1 \\2', 'uncheck' => 'TODO RT matching'), /*SW type */
				),
	),

	/* ------------ Frauenhofer FOKUS ------------ */
	'12325' => array
	(
		'text' => 'Fraunhofer FOKUS',
		'attr' => array(
				4 => array('pf' => 'snmpgeneric_pf_swtype', 'oid' => 'sysDescr.0', 'regex' => '/.*? .*? (.*? .*).*/', 'replacement' => '\\1', 'uncheck' => 'TODO RT matching'), /*SW type */
				),
	),

	'12325.1.1.2.1.1' => array
	(
		'dict_key' => 42, /* Server model noname/unknown */
		'text'	=> 'BSNMP - mini SNMP daemon (bsnmpd)',
	),

) + $known_switches;
/* add snmp.php known_switches */

/* ------------ Sample function --------------- */
/*
 * Sample Precessing Function (pf)
 */
function snmpgeneric_pf_sample(&$snmp, &$sysObjectID, $attr_id) {

	$object = &$sysObjectID['object'];
	$attr = &$sysObjectID['attr'][$attr_id];

	if(!isset($attr['oid']))
		return;

	/* output success banner */
	showSuccess('Found sysObjectID '.$sysObjectID['value']);

	/* access attribute oid setting and do snmpget */
	$oid = $attr['oid'];
	$value = $snmp->get($oid);

	/* set new attribute value */
	$attr['value'] = $value;

	/* do not check attribute per default */
	$attr['uncheck'] = "comment";

	/* set informal comment */
	$attr['comment'] = "comment";

	/* add additional ports */
 //	$sysObjectID['port']['name'] = array('porttypeid' => '1-24', 'ifPhysAddress' => '001122334455', 'ifDescr' => 'visible label', 'uncheck' => 'comment', 'disabled' => 'porttypeid select disabled');

	/* set other attribute */
//	$sysObjectID['attr'][1234]['value'] = 'attribute value';

} /* snmpgeneric_pf_sample */

/* ------------ Enterasys --------------- */

function snmpgeneric_pf_enterasys(&$snmp, &$sysObjectID, $attr_id) {

		$attrs = &$sysObjectID['attr'];

		//snmpgeneric_pf_entitymib($snmp, $sysObjectID, $attr_id);

		/* TODO find correct way to get Bootroom and Firmware versions */

		/* Model */
		/*if(preg_match('/.*\.([^.]+)$/', $sysObjectID['value'], $matches)) {
		 *	showNotice('Device '.$matches[1]);
		 *}
		 */

		/* TODO SW type */
		//$attrs[4]['value'] = 'Enterasys'; /* SW type */

		/* set SW version only if not already set by entitymib */
		if(isset($attrs[5]['value']) && !empty($attrs[5]['value'])) {

			/* SW version from sysDescr */
			if(preg_match('/^Enterasys .* Inc\. (.+) [Rr]ev ([^ ]+) ?(.*)$/', $snmp->sysDescr, $matches)) {

				$attrs[5]['value'] = $matches[2]; /* SW version */

			//	showSuccess("Found Enterasys Model ".$matches[1]);
			}

		} /* SW version */

		/* add serial port */
		//$sysObjectID['port']['console'] = array('porttypeid' => '1-29',  'ifDescr' => 'console', 'disabled' => 'disabled');

}

/* ------------ Cisco --------------- */

/* logic from snmp.php */
function snmpgeneric_pf_catalyst(&$snmp, &$sysObjectID, $attr_id) {
		$attrs = &$sysObjectID['attr'];
		$ports = &$sysObjectID['port'];

		/* sysDescr multiline on C5200 */
                if(preg_match ('/.*, Version ([^ ]+), .*/', $snmp->sysDescr, $matches)) {
			$exact_release = $matches[1];
		$major_line = preg_replace ('/^([[:digit:]]+\.[[:digit:]]+)[^[:digit:]].*/', '\\1', $exact_release);

	                $ios_codes = array
		(
				'12.0' => 244,
				'12.1' => 251,
				'12.2' => 252,
		);

			$attrs[5]['value'] = $exact_release;

			if (array_key_exists ($major_line, $ios_codes))
				$attrs[4]['value'] = $ios_codes[$major_line];

		} /* sw type / version */

                $sysChassi = $snmp->get ('1.3.6.1.4.1.9.3.6.3.0');
                if ($sysChassi !== FALSE or $sysChassi !== NULL)
			$attrs[1]['value'] = str_replace ('"', '', $sysChassi);

		$ports['con0'] = array('porttypeid' => '1-29',  'ifDescr' => 'console'); // RJ-45 RS-232 console

		if (preg_match ('/Cisco IOS Software, C2600/', $snmp->sysDescr))
			$ports['aux0'] = array('porttypeid' => '1-29', 'ifDescr' => 'auxillary'); // RJ-45 RS-232 aux port

                // blade devices are powered through internal circuitry of chassis
                if ($sysObjectID['value'] != '9.1.749' and $sysObjectID['value'] != '9.1.920')
                {
			$ports['AC-in'] = array('porttypeid' => '1-16');
                }

} /* snmpgeneric_pf_catalyst */

/* -------------------------------------------------- */
function snmpgeneric_pf_ciscoflash(&$snmp, &$sysObjectID, $attr_id) {
	/*
	 * ciscoflashMIB = 1.3.6.1.4.1.9.9.10
	 */
	/*
		|   16 | uint   | flash memory, MB            |
	*/
	$attrs = &$sysObjectID['attr'];

	$ciscoflash = $snmp->walk('1.3.6.1.4.1.9.9.10.1.1.2'); /* ciscoFlashDeviceTable */

	$flash = array_keys($ciscoflash, 'flash');

	foreach($flash as $oid) {
		if(!preg_match('/(.*)?\.[^\.]+\.([^\.]+)$/',$oid,$matches))
			continue;

		$index = $matches[2];
		$prefix = $matches[1];

		showSuccess("Found Flash: ".$ciscoflash[$prefix.'.8.'.$index]." ".$ciscoflash[$prefix.'.2.'.$index]." bytes");

		$attrs[16]['value'] = ceil($ciscoflash[$prefix.'.2.'.$index] / 1024 / 1024); /* ciscoFlashDeviceSize */

	}

	/*
	 * ciscoMemoryPoolMIB = 1.3.6.1.4.1.9.9.48
	 *		ciscoMemoryPoolUsed .1.1.1.5
	 *		ciscoMemoryPoolFree .1.1.1.6
	 */

	$ciscomem = $snmp->walk('1.3.6.1.4.1.9.9.48');

	if(!empty($ciscomem)) {

		$used = 0;
		$free = 0;

		foreach($ciscomem as $oid => $value) {

			switch(preg_replace('/.*?(\.1\.1\.1\.[^\.]+)\.[^\.]+$/','\\1',$oid)) {
				case '.1.1.1.5':
					$used += $value;
					break;
				case '.1.1.1.6':
					$free += $value;
					break;
			}

		}

		$attrs[17]['value'] = ceil(($free + $used) / 1024 / 1024); /* RAM, MB */
	}

} /* snmpgeneric_pf_ciscoflash */

/* -------------------------------------------------- */
/* -------------------------------------------------- */

/* HW Type processor function */
function snmpgeneric_pf_hwtype(&$snmp, &$sysObjectID, $attr_id) {

	$attr = &$sysObjectID['attr'][$attr_id];

	if (isset($sysObjectID['dict_key'])) {

		$value = $sysObjectID['dict_key'];
		showSuccess("Found HW type dict_key: $value");

		/* return array of attr_id => attr_value) */
		$attr['value'] = $value;

	} else {
		showNotice("HW type dict_key not set - Unknown OID");
	}

} /* snmpgeneric_pf_hwtype */

/* -------------------------------------------------- */

/* SW type processor function */
/* experimental */
/* Find a way to match RT SW types !? */
function snmpgeneric_pf_swtype(&$snmp, &$sysObjectID, $attr_id) {

	/* 4 = SW type */

	$attr = &$sysObjectID['attr'][$attr_id];

	$object = &$sysObjectID['object'];

	$objtype_id = $object['objtype_id'];

	if(isset($attr['oid']))
		$oid = $attr['oid'];
	else
		$oid = 'sysDescr.0';

	$raw_value = $snmp->get($oid);

	$replacement = '\\1';

	if(isset($attr['regex'])) {
		$regex = $attr['regex'];

		if(isset($attr['replacement']))
			$replacement = $attr['replacement'];

	} else {
		$list = array('bsd','linux','centos','suse','fedora','ubuntu','windows','solaris','vmware');

		$regex = '/.* ([^ ]*('.implode($list,'|').')[^ ]*) .*/i';
		$replacement = '\\1';
	}

	$value = preg_replace($regex, $replacement, $raw_value, -1, $count);
	//$attr['value'] = $value;

	if(!empty($value) && $count > 0) {
		/* search dict_key for value in RT Dictionary */
		/* depends on object type server(13)/switch(14)/router(15) */
		$result = usePreparedSelectBlade
		(
			'SELECT dict_key,dict_value FROM Dictionary WHERE chapter_id in (13,14,15) and dict_value like ? order by dict_key desc limit 1',
			array ('%'.$value.'%')
		);
		$row = $result->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE|PDO::FETCH_COLUMN);

		if(!empty($row)) {
			$RTvalue = key($row);

			if(isset($attr['comment']))
				$attr['comment'] .= ", $value ($RTvalue) ".$row[$RTvalue];
			else
				$attr['comment'] = "$value ($RTvalue) ".$row[$RTvalue];

			showSuccess("Found SW type: $value ($RTvalue) ".$row[$RTvalue]);
			$value = $RTvalue;
		}

		/* set attr value */
		$attr['value'] = $value;
	//	unset($attr['uncheck']);

	}

	if(isset($attr['comment']))
		$attr['comment'] .= ' (experimental)';
	else
		$attr['comment'] = '(experimental)';

} /* snmpgeneric_pf_swtype */

/* -------------------------------------------------- */
/* try to set SW version
 * and add some AC ports
 *
 */
/* needs more testing */
function snmpgeneric_pf_entitymib(&$snmp, &$sysObjectID, $attr_id) {

	/* $attr_id == NULL -> device pf */

	$attrs = &$sysObjectID['attr'];
	$ports = &$sysObjectID['port'];

	$entPhysicalClass = $snmp->walk('.1.3.6.1.2.1.47.1.1.1.1.5'); /* entPhysicalClass */

	if(empty($entPhysicalClass))
		return;

	showNotice("Found Entity Table (Experimental)");

/*		PhysicalClass
 *		1:other
 *		2:unknown
 *		3:chassis
 *		4:backplane
 *		5:container
 *		6:powerSupply
 *		7:fan
 *		8:sensor
 *		9:module
 *		10:port
 *		11:stack
 *		12:cpu
 */

	/* chassis */

	/* always index = 1 ??? */
	$chassis = array_keys($entPhysicalClass, '3'); /* 3 chassis */

	if(0)
	if(!empty($chassis)) {
		echo '<table>';

		foreach($chassis as $key => $oid) {
			/* get index */
			if(!preg_match('/\.(\d+)$/',$oid, $matches))
				continue;

			$index = $matches[1];

			$name = $snmp->get(".1.3.6.1.2.1.47.1.1.1.1.7.$index");
			$serialnum = $snmp->get(".1.3.6.1.2.1.47.1.1.1.1.11.$index");
			$mfgname = $snmp->get(".1.3.6.1.2.1.47.1.1.1.1.12.$index");
			$modelname = $snmp->get(".1.3.6.1.2.1.47.1.1.1.1.13.$index");

			//showNotice("$name $mfgname $modelname $serialnum");

			echo("<tr><td>$name</td><td>$mfgname</td><td>$modelname</td><td>$serialnum</td>");
		}
		unset($key);
		unset($oid);

		echo '</table>';
	} /* chassis */



	/* modules */

	$modules = array_keys($entPhysicalClass, '9'); /* 9 Modules */

	if(!empty($modules)) {

		echo '<br><br>Modules<br><table>';
		echo("<tr><th>Name</th><th>MfgName</th><th>ModelName</th><th>HardwareRev</th><th>FirmwareRev</th><th>SoftwareRev</th><th>SerialNum</th>");

		foreach($modules as $key => $oid) {

			/* get index */
			if(!preg_match('/\.(\d+)$/',$oid, $matches))
				continue;

			$index = $matches[1];

			$name = $snmp->get(".1.3.6.1.2.1.47.1.1.1.1.7.$index");
			$hardwarerev = $snmp->get(".1.3.6.1.2.1.47.1.1.1.1.8.$index");
			$firmwarerev = $snmp->get(".1.3.6.1.2.1.47.1.1.1.1.9.$index");
			$softwarerev = $snmp->get(".1.3.6.1.2.1.47.1.1.1.1.10.$index");
			$serialnum = $snmp->get(".1.3.6.1.2.1.47.1.1.1.1.11.$index");
			$mfgname = $snmp->get(".1.3.6.1.2.1.47.1.1.1.1.12.$index");
			$modelname = $snmp->get(".1.3.6.1.2.1.47.1.1.1.1.13.$index");

			//showNotice("$name $mfgname $modelname $hardwarerev $firmwarerev $softwarerev $serialnum");

			echo("<tr><td>".(empty($name) ? '-' : $name )."</td><td>$mfgname</td><td>$modelname</td><td>$hardwarerev</td><td>$firmwarerev</td><td>$softwarerev</td><td>$serialnum</td>");

			/* set SW version to first module software version */
			if($key == 0 ) {

				$attrs[5]['value'] = $softwarerev; /* SW version */
				$attrs[5]['comment'] = 'entity MIB';
			}

		}
		unset($key);
		unset($oid);

		echo '</table>';
	}


	/* add AC ports */
	$powersupply = array_keys($entPhysicalClass, '6'); /* 6 powerSupply */
	$count = 1;
	foreach($powersupply as $oid) {

		/* get index */
		if(!preg_match('/\.(\d+)$/',$oid, $matches))
			continue;

		$index = $matches[1];
		$descr = $snmp->get(".1.3.6.1.2.1.47.1.1.1.1.2.$index");

		$ports['AC-'.$count] = array('porttypeid' => '1-16', 'ifDescr' => $descr, 'comment' => 'entity MIB', 'uncheck' => '');
		$count++;
	}
	unset($oid);
}

/* -------------------------------------------------- */

/*
 * regex processor function
 * needs 'oid' and  'regex'
 * uses first back reference as attribute value
 */
function snmpgeneric_pf_regex(&$snmp, &$sysObjectID, $attr_id) {

	$attr = &$sysObjectID['attr'][$attr_id];

	if (isset($attr['oid']) && isset($attr['regex'])) {

		$oid = $attr['oid'];
		$regex = $attr['regex'];

		$raw_value = $snmp->get($oid);


		if(isset($attr['replacement']))
			$replace = $attr['replacement'];
		else
			$replace = '\\1';

		$value = preg_replace($regex,$replace, $raw_value);

		/* return array of attr_id => attr_value) */
		$attr['value'] = $value;

	}
	// else Warning ??

} /* snmpgeneric_pf_regex */

/* -------------------------------------------------- */

$sg_portiifoptions= getPortIIFOptions();
$sg_portiifoptions[-1] = 'sfp'; /* generic sfp */

$sg_portoifoptions= getPortOIOptions();

/* -------------------------------------------------- */
/* -------------------------------------------------- */

function snmpgeneric_tabhandler($object_id) {

	if(isset($_POST['snmpconfig'])) {
		if($_POST['snmpconfig'] == '1') {
			snmpgeneric_list($object_id);
		}
	} else {
		snmpgeneric_snmpconfig($object_id);
	}
} /* snmpgeneric_tabhandler */

/* -------------------------------------------------- */

//function snmpgeneric_tabtrigger() {
//     return 'std';
//} /* snmpgeneric_tabtrigger */

/* -------------------------------------------------- */

function snmpgeneric_snmpconfig($object_id) {

	echo '<body onload="document.getElementById(\'submitbutton\').focus();">';

	$object = spotEntity ('object', $object_id);
	//$object['attr'] = getAttrValues($object_id);
        $endpoints = findAllEndpoints ($object_id, $object['name']);

	addJS('function showsnmpv3(element) {
				if(element.value != \''.SNMPgeneric::VERSION_3.'\') {
					style = \'none\';
					document.getElementById(\'snmp_community_label\').style.display=\'\';
				} else {
					style = \'\';
					document.getElementById(\'snmp_community_label\').style.display=\'none\';
				}

				elements = document.getElementsByName(\'snmpv3\');
				for(i=0;i<elements.length;i++) {
					elements[i].style.display=style;
				}
			};',TRUE);

	addJS('function checkInput() {
				host = document.getElementById(\'host\');

				if(host.value == "-1") {
					var newvalue = prompt("Enter Hostname or IP Address","");
					if(newvalue != "") {
						host.options[host.options.length] = new Option(newvalue, newvalue);
						host.value = newvalue;
					}
				}

				if(host.value != "-1" && host.value != "")
					return true;
				else
					return false;
			};',TRUE);

	foreach( $endpoints as $key => $value) {
		$endpoints[$value] = $value;
		unset($endpoints[$key]);
	}
	unset($key);
	unset($value);

	foreach( getObjectIPv4Allocations($object_id) as $ip => $value) {

		if(!in_array($ip, $endpoints))
			$endpoints[$ip] = $ip;
	}
	unset($ip);
	unset($value);

	foreach( getObjectIPv6Allocations($object_id) as $value) {
		$ip = $value['addrinfo']['ip'];

		if(!in_array($ip, $endpoints))
			$endpoints[$ip] = $ip;
	}
	unset($value);

	/* ask for ip/host name on submit see js checkInput() */
	$endpoints['-1'] = 'ask me';

	$snmpconfig = $_POST;

	if(!isset($snmpconfig['host'])) {
		$snmpconfig['host'] = -1;

		/* try to find first FQDN or IP */
		foreach($endpoints as $value) {
			if(preg_match('/^[^ .]+(\.[^ .]+)+\.?/',$value)) {
				$snmpconfig['host'] = $value;
				break;
			}
		}
		unset($value);
	}

//	sg_var_dump_html($endpoints);

	if(!isset($snmpconfig['snmpversion']))
		$snmpconfig['version'] = mySNMP::SNMP_VERSION;

	if(!isset($snmpconfig['community']))
		$snmpconfig['community'] = getConfigVar('DEFAULT_SNMP_COMMUNITY');

	if(empty($snmpconfig['community']))
		$snmpconfig['community'] = mySNMP::SNMP_COMMUNITY;

	if(!isset($snmpconfig['sec_level']))
		$snmpconfig['sec_level'] = NULL;

	if(!isset($snmpconfig['auth_protocol']))
		$snmpconfig['auth_protocol'] = NULL;

	if(!isset($snmpconfig['auth_passphrase']))
		$snmpconfig['auth_passphrase'] = NULL;

	if(!isset($snmpconfig['priv_protocol']))
		$snmpconfig['priv_protocol'] = NULL;

	if(!isset($snmpconfig['priv_passphrase']))
		$snmpconfig['priv_passphrase'] = NULL;

	echo '<h1 align=center>SNMP Config</h1>';
	echo '<form method=post name="snmpconfig" onsubmit="return checkInput()" action='.$_SERVER['REQUEST_URI'].' />';

        echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>
	<tr><th class=tdright>Host:</th><td>';

	echo getSelect ($endpoints, array ('id' => 'host','name' => 'host'), $snmpconfig['host'], FALSE);

	echo'</td></tr>
	<tr>
                <th class=tdright><label for=snmpversion>Version:</label></th>
                <td class=tdleft>';

	echo getSelect (array(SNMPgeneric::VERSION_1 => 'v1', SNMPgeneric::VERSION_2C => 'v2c', SNMPgeneric::VERSION_3 => 'v3'),
			 array ('name' => 'version', 'id' => 'snmpversion', 'onchange' => 'showsnmpv3(this)'),
			 $snmpconfig['version'], FALSE);

	echo '</td>
        </tr>
        <tr>
                <th id="snmp_community_label" class=tdright><label for=community>Community:</label></th>
                <th name="snmpv3" style="display:none" class=tdright><label for=community>Security Name:</label></th>
                <td class=tdleft><input type=text name=community value='.$snmpconfig['community'].' ></td>
        </tr>
        <tr name="snmpv3" style="display:none;">
		<th></th>
        </tr>
        <tr name="snmpv3" style="display:none;">
                <th class=tdright><label">Security Level:</label></th>
                <td class=tdleft>';

	echo getSelect (array('noAuthNoPriv' => 'no Auth and no Priv', 'authNoPriv'=> 'auth without Priv', 'authPriv' => 'auth with Priv'),
			 array ('name' => 'sec_level'),
			 $snmpconfig['sec_level'], FALSE);

	echo '</td></tr>
        <tr name="snmpv3" style="display:none;">
                <th class=tdright><label>Auth Type:</label></th>
                <td class=tdleft>
                <input name=auth_protocol type=radio value=MD5 '.($snmpconfig['auth_protocol'] == 'MD5' ? ' checked="checked"' : '').'/><label>MD5</label>
                <input name=auth_protocol type=radio value=SHA '.($snmpconfig['auth_protocol'] == 'SHA' ? ' checked="checked"' : '').'/><label>SHA</label>
                </td>
        </tr>
        <tr name="snmpv3" style="display:none;">
                <th class=tdright><label>Auth Key:</label></th>
                <td class=tdleft><input type=password id=auth_passphrase name=auth_passphrase value="'.$snmpconfig['auth_passphrase'].'"></td>
        </tr>
        <tr name="snmpv3" style="display:none;">
                <th class=tdright><label>Priv Type:</label></th>
                <td class=tdleft>
                <input name=priv_protocol type=radio value=DES '.($snmpconfig['priv_protocol'] == 'DES' ? ' checked="checked"' : '').'/><label>DES</label>
                <input name=priv_protocol type=radio value=AES '.($snmpconfig['priv_protocol'] == 'AES' ? ' checked="checked"' : '').'/><label>AES</label>
                </td>
        </tr>
        <tr name="snmpv3" style="display:none;">
                <th class=tdright><label>Priv Key</label></th>
                <td class=tdleft><input type=password name=priv_passphrase value="'.$snmpconfig['priv_passphrase'].'"></td>
        </tr>
	</tr>
	<td colspan=2>

        <input type=hidden name=snmpconfig value=1>
	<input type=submit id="submitbutton" tabindex="1" value="Show List"></td></tr>

        </table></form>';

} /* snmpgeneric_snmpconfig */

function snmpgeneric_list($object_id) {

	global $sg_create_noconnector_ports, $sg_known_sysObjectIDs, $sg_portoifoptions, $sg_ifType_ignore;

	if(isset($_POST['snmpconfig'])) {
		$snmpconfig = $_POST;
	} else {
		showError("Missing SNMP Config");
		return;
	}

	echo '<body onload="document.getElementById(\'createbutton\').focus();">';

	addJS('function setchecked(classname) { var boxes = document.getElementsByClassName(classname);
				 value = document.getElementById(classname).checked;
				 for(i=0;i<boxes.length;i++) {
					if(boxes[i].disabled == false)
						boxes[i].checked=value;
				 }
		};', TRUE);

	$object = spotEntity ('object', $object_id);

	$object['attr'] = getAttrValues($object_id);

	$snmpdev = new mySNMP($snmpconfig['version'], $snmpconfig['host'], $snmpconfig['community']);

	if($snmpconfig['version'] == SNMPgeneric::VERSION_3 ) {
		$snmpdev->setSecurity( $snmpconfig['sec_level'],
					$snmpconfig['auth_protocol'],
					$snmpconfig['auth_passphrase'],
					$snmpconfig['priv_protocol'],
					$snmpconfig['priv_passphrase']
					);
	}

	$snmpdev->init();

	if($snmpdev->getErrno()) {
		showError($snmpdev->getError());
		return;
	}

	/* SNMP connect successfull */

	showSuccess("SNMP v".$snmpconfig['version']." connect to ${snmpconfig['host']} successfull");

	echo '<form name=CreatePorts method=post action='.$_SERVER['REQUEST_URI'].'&module=redirect&op=create>';

	echo "<strong>System Informations</strong>";
	echo "<table>";
//	echo "<tr><th>OID</th><th>Value</th></tr>";

	$systemoids = array('sysDescr', 'sysObjectID', 'sysUpTime', 'sysContact', 'sysName', 'sysLocation');
	foreach ($systemoids as $shortoid) {

		$value = $snmpdev->{$shortoid};

		if($shortoid == 'sysUpTime') {
			/* in hundredths of a second */
			$secs = (int)($value / 100);
			$days = (int)($secs / (60 * 60 * 24));
			$secs -= $days * 60 *60 * 24;
			$hours = (int)($secs / (60 * 60));
			$secs -= $hours * 60 * 60;
			$mins = (int)($secs / (60));
			$secs -= $mins * 60;
			$value = "$value ($days $hours:$mins:$secs)";
		}

		echo "<tr><td title=\"".$snmpdev->lastgetoid."\" align=\"right\">$shortoid: </td><td>$value</td></tr>";

	}
	unset($shortoid);

	echo "</table>";

	/* sysObjectID Attributes and Ports */
	$sysObjectID['object'] = &$object;

	/* get sysObjectID */
	$sysObjectID['raw_value'] = $snmpdev->sysObjectID;
	//$sysObjectID['raw_value'] = 'NET-SNMP-MIB::netSnmpAgentOIDs.10';

	$sysObjectID['value'] = preg_replace('/^.*enterprises\.([\.[:digit:]]+)$/','\\1', $sysObjectID['raw_value']);

	/* try snmptranslate to numeric */
	if(preg_match('/[^\.0-9]+/',$sysObjectID['value'])) {
		$numeric_value = $snmpdev->translatetonumeric($sysObjectID['value']);

		if(!empty($numeric_value)) {
			showSuccess("sysObjectID: ".$sysObjectID['value']." translated to $numeric_value");
			$sysObjectID['value'] = preg_replace('/^.1.3.6.1.4.1.([\.[:digit:]]+)$/','\\1', $numeric_value);
		}
	}

	/* array_merge doesn't work with numeric keys !! */
	$sysObjectID['attr'] = array();
	$sysObjectID['port'] = array();

	$sysobjid = $sysObjectID['value'];

	$count = 1;

	while($count) {

		if(isset($sg_known_sysObjectIDs[$sysobjid])) {
			$sysObjectID = $sysObjectID + $sg_known_sysObjectIDs[$sysobjid];

			if(isset($sg_known_sysObjectIDs[$sysobjid]['attr']))
				$sysObjectID['attr'] = $sysObjectID['attr'] + $sg_known_sysObjectIDs[$sysobjid]['attr'];

			if(isset($sg_known_sysObjectIDs[$sysobjid]['port']))
				$sysObjectID['port'] = $sysObjectID['port'] + $sg_known_sysObjectIDs[$sysobjid]['port'];

			if(isset($sg_known_sysObjectIDs[$sysobjid]['text'])) {
				showSuccess("found sysObjectID ($sysobjid) ".$sg_known_sysObjectIDs[$sysobjid]['text']);
			}
		}

		$sysobjid = preg_replace('/\.[[:digit:]]+$/','',$sysobjid, 1, $count);

		/* add default sysobjectid */
		if($count == 0 && $sysobjid != 'default') {
			$sysobjid = 'default';
			$count = 1;
		}
	}

	$sysObjectID['vendor_number'] = $sysobjid;

	/* device pf */
	if(isset($sysObjectID['pf']))
		foreach($sysObjectID['pf'] as $function) {
			if(function_exists($function)) {
				/* call device pf */
				$function($snmpdev, $sysObjectID, NULL);
			} else {
				showWarning("Missing processor function ".$function." for device $sysobjid");
			}
		}


	/* sort attributes maintain numeric keys */
	ksort($sysObjectID['attr']);

	/* DEBUG */
	//sg_var_dump_html($sysObjectID['attr'], "Before processing");

	/* needs PHP >= 5 foreach call by reference */
	/* php 5.1.6 doesn't seem to work */
	//foreach($sysObjectID['attr'] as $attr_id => &$attr) {
	foreach($sysObjectID['attr'] as $attr_id => $value) {

		$attr = &$sysObjectID['attr'][$attr_id];

		if(isset($object['attr'][$attr_id])) {

			switch(TRUE) {

				case isset($attr['pf']):
					if(function_exists($attr['pf'])) {

						$attr['pf']($snmpdev, $sysObjectID, $attr_id);

					} else {
						showWarning("Missing processor function ".$attr['pf']." for attribute $attr_id");
					}

					break;

				case isset($attr['oid']):

					$attrvalue = $snmpdev->get($attr['oid']);

					if(isset($attr['regex'])) {
						$regex = $attr['regex'];

						if(isset($attr['replacement'])) {
							$replacement = $attr['replacement'];
							$attrvalue = preg_replace($regex, $replacement, $attrvalue);
						} else {
							if(!preg_match($regex, $attrvalue)) {
								if(!isset($attr['uncheck']))
									$attr['uncheck'] = "regex doesn't match";
							} else
								unset($attr['uncheck']);
						}
					}

					$attr['value'] = $attrvalue;

					break;

				case isset($attr['value']):
					break;

				default:
					showError("Error handling attribute id: $attr_id");

			}

		} else {
			showWarning("Object has no attribute id: $attr_id");
			unset($sysObjectID['attr'][$attr_id]);
		}

	}
	unset($attr_id);

	/* sort again in case there where attribs added ,maintain numeric keys */
	ksort($sysObjectID['attr']);

	/* print attributes */
	echo '<br>Attributes<br><table>';
	echo '<tr><th><input type="checkbox" id="attribute" checked="checked" onclick="setchecked(this.id)"></td>';
	echo '<th>Name</th><th>Current Value</th><th>new value</th></tr>';

	/* DEBUG */
	//sg_var_dump_html($sysObjectID['attr'], "After processing");

	foreach($sysObjectID['attr'] as $attr_id => &$attr) {

		if(isset($object['attr'][$attr_id]) && isset($attr['value'])) {

			if($attr['value'] == $object['attr'][$attr_id]['value'])
				$attr['uncheck'] = 'Current = new value';

			$value = $attr['value'];

			$val_key = (isset($object['attr'][$attr_id]['key']) ? ' ('.$object['attr'][$attr_id]['key'].')' : '' );
			$comment = '';

			if(isset($attr['comment'])) {
				if(!empty($attr['comment']))
					$comment = $attr['comment'];
			}

			if(isset($attr['uncheck'])) {
				$checked = '';
				$comment .= ', '.$attr['uncheck'];
			} else {
				$checked = ' checked="checked"';
			}

			$updateattrcheckbox = '<b style="background-color:#00ff00">'
					 .'<input style="background-color:#00ff00" class="attribute" type="checkbox" name="updateattr['.$attr_id.']" value="'.$value.'"'
					.$checked.'></b>';

			$comment = trim($comment,', ');

			echo "<tr><td>$updateattrcheckbox</td><td title=\"id: $attr_id\">"
				.$object['attr'][$attr_id]['name'].'</td><td style="background-color:#d8d8d8">'
				.$object['attr'][$attr_id]['value'].$val_key.'</td><td>'.$value.'</td>'
				.'<td style="color:#888888">'.$comment.'</td></tr>';
		}
	}
	unset($attr_id);

	echo '</table>';

	/* ports */

	/* get ports */
	amplifyCell($object);

	/* set array key to port name */
	foreach($object['ports'] as $key => $values) {
		$object['ports'][$values['name']] = $values;
		unset($object['ports'][$key]);
	}

	$newporttypeoptions = getNewPortTypeOptions();

//	sg_var_dump_html($sysObjectID['port']);

	if(!empty($sysObjectID['port'])) {

		echo '<br>Vendor / Device specific ports<br>';
		echo '<table><tr><th><input type="checkbox" id="moreport" checked="checked" onclick="setchecked(this.id)"></th><th>ifName</th><th>porttypeid</th></tr>';

		foreach($sysObjectID['port'] as $name => $port) {

			if(array_key_exists($name,$object['ports']))
				$disableport = TRUE;
			else
				$disableport = FALSE;

			$comment = '';

			if(isset($port['comment'])) {
				if(!empty($port['comment']))
					$comment = $port['comment'];
			}
			if(isset($port['uncheck'])) {
				$checked = '';
				$comment .= ', '.$port['uncheck'];
			} else {
				$checked = ' checked="checked"';
			}

			$portcreatecheckbox = '<b style="background-color:'.($disableport ? '#ff0000' : '#00ff00')
					.'"><input style="background-color:'.($disableport ? '#ff0000' : '#00ff00').'" class="moreport" type="checkbox" name="portcreate['.$name.']" value="'.$name.'"'
					.($disableport ? ' disabled="disbaled"' : $checked ).'></b>';

			$formfield = '<input type="hidden" name="ifName['.$name.']" value="'.$name.'">';
			echo "<tr>$formfield<td>$portcreatecheckbox</td><td>$name</td>";

			if(isset($port['disabled'])) {
				$disabledselect = array('disabled' => "disabled");
			} else
				$disabledselect = array();


			foreach($port as $key => $value) {

				if($key == 'uncheck' || $key == 'comment')
					continue;

				/* TODO iif_name */
				if($key == 'porttypeid')
					$displayvalue = getNiftySelect($newporttypeoptions,
							 array('name' => "porttypeid[$name]") + $disabledselect, $value);
											/* disabled formfied won't be submitted ! */
				else
					$displayvalue = $value;

				$formfield = '<input type="hidden" name="'.$key.'['.$name.']" value="'.$value.'">';
				echo "$formfield<td>$displayvalue</td>";
			}

			$comment = trim($comment,', ');
			echo "<td style=\"color:#888888\">$comment</td></tr>";
		}
		unset($name);
		unset($port);

		echo '</table>';
	}

	/* snmp ports */

	$ifsnmp = new ifSNMP($snmpdev);

	/* ip spaces */

	$ipspace = NULL;
	foreach($ifsnmp->ipaddress as $ifindex => $ipaddresses) {

		foreach($ipaddresses as $ipaddr => $value) {
			$addrtype = $value['addrtype'];
			$netaddr = $value['net'];
			$maskbits = $value['maskbits'];
			$netid = NULL;
			$linklocal = FALSE;

			/* check for ip space */
			switch($addrtype) {
				case 'ipv4':
				case 'ipv4z':
					$netid = getIPv4AddressNetworkId($ipaddr);
					break;

				case 'ipv6':
					/* convert to IPv6Address->parse format */
					$ipaddr =  preg_replace('/((..):(..))/','\\2\\3',$ipaddr);
					$ipaddr =  preg_replace('/%.*$/','',$ipaddr);

					$ipv6 = new IPv6Address();
					if($ipv6->parse($ipaddr)) {
						$netid = getIPv6AddressNetworkId($ipv6);
						$netaddr = $ipv6->get_first_subnet_address($maskbits)->format();
						$linklocal = ($ipv6->get_first_subnet_address(10)->format() == 'fe80::');
					}
					break;

				case 'ipv6z':
					/* link local */
					$netid = 'ignore';
					break;
				default:
			}

			if(empty($netid) && $netaddr != '::1' && $netaddr != '127.0.0.1' && $netaddr != '127.0.0.0' && $netaddr != '0.0.0.0' && !$linklocal) {

				$netaddr .= "/$maskbits";
				$ipspace[$netaddr] = $addrtype;
			}
		}
		unset($ipaddr);
		unset($value);
		unset($addrtype);
	}
	unset($ifindex);
	unset($ipaddresses);

	/* print ip spaces table */
	if(!empty($ipspace)) {
		echo '<br><br>Create IP Spaces';
		echo '<table><tr><th><input type="checkbox" id="ipspace" onclick="setchecked(this.id)" checked=\"checked\"></th>';
		echo '<th>Type</th><th>prefix</th><th>name</th><th width=150 title="reserve network and router addresses">reserve network / router addresses</th></tr>';

		$i = 1;
		foreach($ipspace as $prefix => $addrtype) {

			$netcreatecheckbox = '<b style="background-color:#00ff00">'
				.'<input class="ipspace" style="background-color:#00ff00" type="checkbox" name="netcreate['
				.$i.']" value="'.$addrtype.'" checked=\"checked\"></b>';

			$netprefixfield = '<input type="text" size=50 name="netprefix['.$i.']" value="'.$prefix.'">';

			$netnamefield = '<input type="text" name="netname['.$i.']">';

			$netreservecheckbox = '<input type="checkbox" name="netreserve['.$i.']">';

			echo "<tr><td>$netcreatecheckbox</td><td style=\"color:#888888\">$addrtype</td><td>$netprefixfield</td><td>$netnamefield</td><td>$netreservecheckbox</td></tr>";

			$i++;
		}
		unset($prefix);
		unset($addrtype);
		unset($i);

		echo '</table>';
	}


	echo "<br><br>ifNumber: ".$ifsnmp->ifNumber."<br><table><tbody valign=\"top\">";

	$portcompat = getPortInterfaceCompat();

	$ipnets = array();

	$ifsnmp->printifInfoTableHeader("<th>add ip</th><th>add port</th><th title=\"update mac\">upd mac</th><th>porttypeid</th><th>comment</th></tr>");

	echo '<tr><td colspan="11"></td>
		<td><input type="checkbox" id="ipaddr" onclick="setchecked(this.id)">IPv4<br>
		<input type="checkbox" id="ipv6addr" onclick="setchecked(this.id)">IPv6</td>
		<td><input type="checkbox" id="ports" onclick="setchecked(this.id)"></td>
		<td><input type="checkbox" id="mac" onclick="setchecked(this.id)" checked="checked"></td></tr>';

	foreach($ifsnmp as $if) {

		$createport = TRUE;
		$disableport = FALSE;
		$ignoreport = FALSE;
		$port_info = NULL;

		$updatemaccheckbox = '';

		$hrefs = array();

		$comment = "";

		if(trim($ifsnmp->ifName($if)) == '') {
			$comment .= "no ifName";
			$createport = FALSE;
		} else {

			if(array_key_exists($ifsnmp->ifName($if),$object['ports'])){
				$port_info = &$object['ports'][$ifsnmp->ifName($if)];
				$comment .= "Name exists";
				$createport = FALSE;
				$disableport = TRUE;
			}
		}

		if($ifsnmp->ifPhysAddress($if) != '' ) {

			$ifPhysAddress = $ifsnmp->ifPhysAddress($if);

			$l2port =  sg_checkL2Address($ifPhysAddress);

			//if(alreadyUsedL2Address($ifPhysAddress, $object_id)) {

			if(!empty($l2port)) {
				$l2object_id = key($l2port);

				$porthref = makeHref(array('page'=>'object', 'tab' => 'ports',
								 'object_id' => $l2object_id, 'hl_port_id' => $l2port[$l2object_id]));

				$comment .= ", L2Address exists";
				$hrefs['ifPhysAddress'] = $porthref;
				$createport = FALSE;
			//	$disableport = TRUE;
				$updatemaccheckbox = '';
			}

			$disablemac = true;
			if($disableport) {
				if($port_info !== NULL) {
					if(str_replace(':','',$port_info['l2address']) != $ifPhysAddress)
						$disablemac = false;
					else
						$disablemac = true;
				}
			} else {
				/* port create always updates mac */
				$updatemaccheckbox = '<b style="background-color:#00ff00">'
					.'<input style="background-color:'
					.'#00ff00" type="checkbox"'
					.' checked="checked"'
					.' disabled=\"disabled\"></b>';
			}

			if(!$disablemac)
				$updatemaccheckbox = '<b style="background-color:'.($disablemac ? '#ff0000' : '#00ff00').'">'
					.'<input class="mac" style="background-color:'
					.($disablemac ? '#ff0000' : '#00ff00').'" type="checkbox" name="updatemac['.$if.']" value="'
					.$object['ports'][$ifsnmp->ifName($if)]['id'].'" checked="checked"'
					.($disablemac ? ' disabled=\"disabled\"' : '' ).'></b>';

		}


		$porttypeid = guessRToif_id($ifsnmp->ifType($if), $ifsnmp->ifDescr($if));

		if(in_array($ifsnmp->ifType($if),$sg_ifType_ignore)) {
			$comment .= ", ignore if type";
			$createport = FALSE;
			$ignoreport = TRUE;
		}

		/* ignore ports without an Connector */
		if(!$sg_create_noconnector_ports && ($ifsnmp->ifConnectorPresent($if) == 2)) {
			$comment .= ", no Connector";
			$createport = FALSE;
		}


		/* Allocate IPs ipv4 and ipv6 */

		$ipaddresses = $ifsnmp->ipaddress($if);

		if(!empty($ipaddresses)) {

			$ipaddrcell = '<table>';

			foreach($ipaddresses as $ipaddr => $value) {
				$createipaddr = FALSE;
				$disableipaddr = FALSE;
				$ipaddrhref = '';
				$linklocal = FALSE;

				$addrtype = $value['addrtype'];
				$maskbits = $value['maskbits'];
				$bcast = $value['bcast'];

				switch($addrtype) {
					case 'ipv4z':
					case 'ipv4':
						$inputname = 'ip';
						break;

					case 'ipv6z':
						$disableipaddr = TRUE;
					case 'ipv6':
						$inputname = 'ipv6';

						/* convert to IPv6Address->parse format */
						$ipaddr =  preg_replace('/((..):(..))/','\\2\\3',$ipaddr);
						$ipaddr =  preg_replace('/%.*$/','',$ipaddr);

						$ipv6 = new IPv6Address();
						if(!$ipv6->parse($ipaddr)) {
							$disableipaddr = TRUE;
							$comment .= ' ipv6 parse failed';
						} else {
							$ipaddr = $ipv6->format();
							$linklocal = ($ipv6->get_first_subnet_address(10)->format() == 'fe80::');
						}

						$createipaddr = FALSE;
						break;

				}

				$address = getIPAddress($ipaddr);

				/* only if ip not already allocated */
				if(empty($address['allocs'])) {
					if(!$ignoreport)
						$createipaddr = TRUE;
				} else {
					$disableipaddr = TRUE;

					$ipobject_id = $address['allocs'][0]['object_id'];

					$ipaddrhref = makeHref(array('page'=>'object',
									 'object_id' => $ipobject_id, 'hl_ipv4_addr' => $ipaddr));

				}

				/* reserved addresses */
				if($address['reserved'] == 'yes') {
					$comment .= ', '.$address['ip'].' reserved '.$address['name'];
					$createipaddr = FALSE;
				//	$disableipaddr = TRUE;
				}

				if($ipaddr == '127.0.0.1' || $ipaddr == '0.0.0.0' || $ipaddr == '::1' || $ipaddr == '::' || $linklocal) {
					$createipaddr = FALSE;
					$disableipaddr = TRUE;
				}

				if($ipaddr === $bcast) {
					$comment .= ", $ipaddr broadcast";
					$createipaddr = FALSE;
					$disableipaddr = TRUE;
				}


			if(!$disableipaddr)
				$ipaddrcheckbox = '<b style="background-color:'.($disableipaddr ? '#ff0000' : '#00ff00')
					.'"><input class="'.$inputname.'addr" style="background-color:'
					.($disableipaddr ? '#ff0000' : '#00ff00')
					.'" type="checkbox" name="'.$inputname.'addrcreate['.$ipaddr.']" value="'.$if.'"'
					.($disableipaddr ? ' disabled="disabled"' : '')
					.($createipaddr ? ' checked="checked"' : '').'></b>';
			else
				$ipaddrcheckbox = '';

				$ipaddrcell .= "<tr><td>$ipaddrcheckbox</td>";

				if(!empty($ipaddrhref))
					$ipaddrcell .= "<td><a href=$ipaddrhref>$ipaddr/$maskbits</a></td></tr>";
				else
					$ipaddrcell .= "<td>$ipaddr/$maskbits</td></tr>";

			}
			unset($ipaddr);
			unset($value);

			$ipaddrcell .= '</table>';

		 } else {
			$ipaddrcreatecheckbox = '';
			$ipaddrcell = '';

		}


		/* checkboxes for add port and add ip */
		/* FireFox needs <b style=..>, IE and Opera work with <td style=..> */
		if(!$disableport)
			$portcreatecheckbox = '<b style="background-color:'.($disableport ? '#ff0000' : '#00ff00')
					.'"><input class="ports" style="background-color:'.($disableport ? '#ff0000' : '#00ff00')
					.'" type="checkbox" name="portcreate['.$if.']" value="'.$if.'"'
					.($disableport ? ' disabled="disbaled"' : '').($createport ? ' checked="checked"' : '').'></b>';
		else
			$portcreatecheckbox = '';

		/* port type id */
		/* add port type to newporttypeoptions if missing */
		if(strpos(serialize($newporttypeoptions),$porttypeid) === FALSE) {

			$portids = explode('-',$porttypeid);
			$oif_name = $sg_portoifoptions[$portids[1]];

			$newporttypeoptions['auto'] = array($porttypeid => "*$oif_name");
		}

		$selectoptions = array('name' => "porttypeid[$if]");

		if($disableport)
			$selectoptions['disabled'] = "disabled";

		$porttypeidselect = getNiftySelect($newporttypeoptions, $selectoptions, $porttypeid);

		$comment = trim($comment,', ');

		$ifsnmp->printifInfoTableRow($if,"<td>$ipaddrcell</td><td>$portcreatecheckbox</td><td>$updatemaccheckbox</td><td>$porttypeidselect</td><td nowrap=\"nowrap\">$comment</td>", $hrefs);

	}
	unset($if);

	/* preserve snmpconfig */
	foreach($_POST as $key => $value) {
		echo '<input type=hidden name='.$key.' value='.$value.' />';
	}
	unset($key);
	unset($value);

	echo '<tr><td colspan=15 align="right"><p><input id="createbutton" type=submit value="Create Ports and IPs" onclick="return confirm(\'Create selected items?\')"></p></td></tr></tbody></table></form>';

}

/* -------------------------------------------------- */
function snmpgeneric_opcreate() {

	$object_id = $_REQUEST['object_id'];
	$attr = getAttrValues($object_id);

//	sg_var_dump_html($_REQUEST);
//	sg_var_dump_html($attr);

	/* commitUpdateAttrValue ($object_id, $attr_id, $new_value); */
	if(isset($_POST['updateattr'])) {
		foreach($_POST['updateattr'] as $attr_id => $value) {
		//	if(empty($attr[$attr_id]['value']))
				if(!empty($value)) {
					commitUpdateAttrValue ($object_id, $attr_id, $value);
					showSuccess("Attribute ".$attr[$attr_id]['name']." set to $value");
				}
		}
		unset($attr_id);
		unset($value);
	}
	/* updateattr */

	/* create ports */
	if(isset($_POST['portcreate'])) {
		foreach($_POST['portcreate'] as $if => $value) {

			$ifName = (isset($_POST['ifName'][$if]) ? trim($_POST['ifName'][$if]) : '' );
			$ifPhysAddress = (isset($_POST['ifPhysAddress'][$if]) ? trim($_POST['ifPhysAddress'][$if]) : '' );
			$ifAlias = (isset($_POST['ifAlias'][$if]) ? trim($_POST['ifAlias'][$if]) : '' );
			$ifDescr = (isset($_POST['ifDescr'][$if]) ? trim($_POST['ifDescr'][$if]) : '' );

			$visible_label = (empty($ifAlias) ? '' : $ifAlias.'; ').$ifDescr;

			if(empty($ifName)) {
				showError('Port without ifName '.$_POST['porttypeid'][$if].', '.$visible_label.', '.$ifPhysAddress);
			} else {
				commitAddPort ($object_id, $ifName, $_POST['porttypeid'][$if], $visible_label, $ifPhysAddress);
				showSuccess('Port created '.$ifName.', '.$_POST['porttypeid'][$if].', '.$visible_label.', '.$ifPhysAddress);
			}
		}
		unset($if);
		unset($value);
	}
	/* portcreate */

	/* net create */
	if(isset($_POST['netcreate'])) {
		foreach($_POST['netcreate'] as $id => $addrtype) {
			$range = $_POST['netprefix'][$id];
			$name = $_POST['netname'][$id];
			$is_reserved = isset($_POST['netreserve'][$id]);

			if($addrtype == 'ipv4' || $addrtype == 'ipv4z')
				createIPv4Prefix($range, $name, $is_reserved);
			else
				createIPv6Prefix($range, $name, $is_reserved);

			showSuccess("$range $name created");

		}
		unset($id);
		unset($addrtype);
	}
	/* netcreate */

	/* allocate ipv6 adresses */
	if(isset($_POST['ipv6addrcreate'])) {
		foreach($_POST['ipv6addrcreate'] as $ipaddr => $if) {

			$ip = new IPv6Address();
			if($ip->parse($ipaddr)) {

				bindIPv6ToObject($ip, $object_id,$_POST['ifName'][$if], 1); /* connected */
				showSuccess("$ipaddr allocated");
			} else
				showError("$ipaddr parse failed!");
		}
		unset($ipaddr);
		unset($if);
	}
	/* allocate ip adresses */
	if(isset($_POST['ipaddrcreate'])) {
		foreach($_POST['ipaddrcreate'] as $ipaddr => $if) {

			bindIpToObject($ipaddr, $object_id,$_POST['ifName'][$if], 1); /* connected */
			showSuccess("$ipaddr allocated");
		}
		unset($ipaddr);
		unset($if);
	}
	/* ipaddrecreate */

	/* update mac addresses only */
	if(isset($_POST['updatemac'])) {
		foreach($_POST['updatemac'] as $if => $port_id) {

			$ifPhysAddress = (isset($_POST['ifPhysAddress'][$if]) ? trim($_POST['ifPhysAddress'][$if]) : '' );

			sg_commitUpdatePortl2address($object_id, $port_id, $ifPhysAddress);

			$ifName = (isset($_POST['ifName'][$if]) ? trim($_POST['ifName'][$if]) : '' );
			showSuccess("l2address updated on $ifName to $ifPhysAddress");
		}
		unset($if);
		unset($port_id);
	}
	/* updatemac */

} /* snmpgeneric_opcreate */

/* -------------------------------------------------- */

/* returns RT interface type depending on ifType, ifDescr, .. */
function guessRToif_id($ifType,$ifDescr = NULL) {
	global $sg_ifType2oif_id;
	global $sg_portiifoptions;
	global $sg_portoifoptions;

	/* default value */
	$retval = '24'; /* 1000BASE-T */

	if(isset($sg_ifType2oif_id[$ifType])) {
		$retval = $sg_ifType2oif_id[$ifType];
	}

	if(strpos($retval,'-') === FALSE)
		$retval = "1-$retval";

	/* no ethernetCsmacd */
	if($ifType != 6)
		return $retval;


	/* try to identify outer and inner interface type from ifDescr */

	/**********************
	 * ifDescr samples
	 *
	 * Enterasys C3
	 *
	 * Unit: 1 1000BASE-T RJ45 Gigabit Ethernet Frontpanel Port 45 - no sfp inserted
	 * Unit: 1 1000BASE-T RJ45 Gigabit Ethernet Frontpanel Port 47 - sfp 1000BASE-SX inserted
	 *
	 *
	 * Enterasys S4
	 *
         * Enterasys Networks, Inc. 1000BASE Gigabit Ethernet Port; No GBIC/MGBIC Inserted
	 * Enterasys Networks, Inc. 1000BASE-SX Mini GBIC w/LC connector
	 * Enterasys Networks, Inc. 10GBASE SFP+ 10-Gigabit Ethernet Port; No SFP+ Inserted
	 * Enterasys Networks, Inc. 10GBASE-SR SFP+ 10-Gigabit Ethernet Port (850nm Short Wavelength, 33/82m MMF, LC)
	 * Enterasys Networks, Inc. 1000BASE Gigabit Ethernet Port; Unknown GBIC/MGBIC Inserted
	 *
	 */

	foreach($sg_portiifoptions as $iif_id => $iif_type) {

		/* TODO better matching */


		/* find iif_type */
		if(preg_match('/(.*?)('.preg_quote($iif_type).')(.*)/i',$ifDescr,$matches)) {

			$oif_type = "empty ".$iif_type;

			$no = preg_match('/ no $/i', $matches[1]);

			if(preg_match('/(\d+[G]?)BASE[^ ]+/i', $matches[1], $basematch)) {
				$oif_type=$basematch[0];
			} else {
				if(preg_match('/(\d+[G]?)BASE[^ ]+/i', $matches[3], $basematch)) {
					$oif_type=$basematch[0];
				}
			}

			if($iif_id == -1) {
				/* 2 => SFP-100 or 4 => SFP-1000 */

				if(isset($basematch[1])) {
					switch($basematch[1]) {
						case '100' :
							$iif_id = 2;
							$iif_type = "SFP-100";
							break;
						default:
						case '1000' :
							$iif_id = 4;
							$iif_type = "SFP-1000";
							break;
					}
				}

			}

			if($no) {
				$oif_type = "empty ".$iif_type;
			}

			$oif_type = preg_replace('/BASE/',"Base",$oif_type);

			$oif_id = array_search($oif_type,$sg_portoifoptions);

			if($oif_id != '') {
				$retval = "$iif_id-$oif_id";
			}

			/* TODO check port compat */

			/* stop foreach */
			break;
		}
	}
	unset($iif_id);
	unset($iif_type);

	if(strpos($retval,'-') === FALSE)
		$retval = "1-$retval";

	return $retval;

}

/* --------------------------------------------------- */

function sg_commitUpdatePortl2address($object_id, $port_id, $port_l2address)
{
        $db_l2address = l2addressForDatabase ($port_l2address);

        global $dbxlink;
        $dbxlink->exec ('LOCK TABLES Port WRITE');
        if (alreadyUsedL2Address ($db_l2address, $object_id))
        {
                $dbxlink->exec ('UNLOCK TABLES');
                // FIXME: it is more correct to throw InvalidArgException here
                // and convert it to InvalidRequestArgException at upper level,
                // when there is a mean to do that.
                throw new InvalidRequestArgException ('port_l2address', $db_l2address, 'address belongs to another object');
        }
        usePreparedUpdateBlade
        (
                'Port',
                array
                (
                        'l2address' => ($db_l2address === '') ? NULL : $db_l2address,
                ),
                array
                (
                        'id' => $port_id,
                        'object_id' => $object_id
                )
        );
        $dbxlink->exec ('UNLOCK TABLES');
} /* sg_commitUpdatePortl2address */

/* ----------------------------------------------------- */

/* returns object_id and port_id to a given l2address */
function sg_checkL2Address ($address)
{
        $result = usePreparedSelectBlade
        (
                'SELECT object_id,id FROM Port WHERE BINARY l2address = ?',
                array ($address)
        );
        $row = $result->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE|PDO::FETCH_COLUMN);
        return $row;
}

/* returns oi_id and name */
function getPortOIOptions()
{
        $result = usePreparedSelectBlade
        (
		'SELECT dict_key,dict_value from Dictionary where chapter_id = 2',
                array ()
        );
        $row = $result->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE|PDO::FETCH_COLUMN);
        return $row;
}

/* ------------------------------------------------------- */
class SNMPgeneric {

	protected $host;
	protected $version;

	/* SNMPv1 and SNMPv2c */
	protected $community;

	/* SNMPv3 */
	protected $sec_level;
	protected $auth_protocol;
	protected $auth_passphrase;
	protected $priv_protocol;
	protected $priv_passphrase;
//	protected $contextName;
//	protected $contextEngineID;

	const VERSION_1 = 1;
	const VERSION_2C = 2;
	const VERSION_3 = 3;

	protected $result;

	function __construct($version, $host, $community) {

		$this->host = $host;
		$this->version = $version;
		$this->community = $community;

		set_error_handler(array($this,'ErrorHandler'), E_WARNING);
	}

	function setSecurity($sec_level, $auth_protocol = 'md5', $auth_passphrase = '', $priv_protocol = 'des', $priv_passphrase = '') {
		$this->sec_level = $sec_level;
		$this->auth_protocol = $auth_protocol;
		$this->auth_passphrase = $auth_passphrase;
		$this->priv_protocol = $priv_protocol;
		$this->priv_passphrase = $priv_passphrase;
	}

	function walk( $oid, $suffix_as_key = FALSE) {

		switch($this->version) {
			case self::VERSION_1:
				if($suffix_as_key){
					$this->result = snmpwalk($this->host,$this->community,$oid);
				} else {
					$this->result = snmprealwalk($this->host,$this->community,$oid);
				}
				break;

			case self::VERSION_2C:
				if($suffix_as_key){
					$this->result = snmp2_walk($this->host,$this->community,$oid);
				} else {
					$this->result = snmp2_real_walk($this->host,$this->community,$oid);
				}
				break;

			case self::VERSION_3:
				if($suffix_as_key){
					$this->result = snmp3_walk($this->host,$this->community, $this->sec_level, $this->auth_protocol, $this->auth_passphrase, $this->priv_protocol, $this->priv_passphrase,$oid);
				} else {
					$this->result = snmp3_real_walk($this->host,$this->community, $this->sec_level, $this->auth_protocol, $this->auth_passphrase, $this->priv_protocol, $this->priv_passphrase,$oid);
				}
				break;
		}

		return $this->result;

	}

	private function __snmpget($object_id) {

		$retval = FALSE;

		switch($this->version) {
			case self::VERSION_1:
				$retval = snmpget($this->host,$this->community,$object_id);
				break;

			case self::VERSION_2C:
				$retval = snmp2_get($this->host,$this->community,$object_id);
				break;

			case self::VERSION_3:
				$retval = snmp3_get($this->host,$this->community, $this->sec_level, $this->auth_protocol, $this->auth_passphrase, $this->priv_protocol, $this->priv_passphrase,$object_id);
				break;
		}

		return $retval;
	}

	function get($object_id, $preserve_keys = false) {

		if(is_array($object_id)) {

			if( $preserve_keys ) {
				foreach($object_id as $oid) {
					$this->result[$oid] = $this->__snmpget($oid);
				}
				unset($oid);
			} else {
				foreach($object_id as $oid) {
					$result_oid = preg_replace('/.\d$/','',$oid);
					$this->result[$result_oid] = $this->__snmpget($oid);
				}
				unset($oid);
			}
		} else {
			$this->result = $this->__snmpget($object_id);
		}

		return $this->result;

	}

	function close() {
	}

	function getErrno() {
		return ($this->result === FALSE);
	}

	function getError() {
		$var = error_get_last();
		return $var['message'];
	}

	function Errorhandler($errno, $errstr, $errfile, $errline) {
		switch(TRUE) {
			case (False !== strpos($errstr,'No Such Object available on this agent at this OID')):
					/* no further error processing */
					return true;
				break;
		}

		/* proceed with default error handling */
		return false;
	}
} /* SNMPgeneric */

/* ------------------------------------------------------- */
/*
 * SNMP with system OIDs
 */
class mySNMP extends SNMPgeneric implements Iterator {

	const SNMP_VERSION = SNMPgeneric::VERSION_2C;
	const SNMP_COMMUNITY = 'public';

	public $lastgetoid;

	//private $sysInfo;
	private $system;

	/* is system table available ? */
	private $systemerror = TRUE;

	function __construct($version, $host, $community) {
		parent::__construct($version, $host, $community);

		//snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);

		/* Return values without SNMP type hint */
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);

		/* needs php >= 5.2.0 */
	//	snmp_set_oid_output_format(SNMP_OID_OUTPUT_FULL);

	//	snmp_set_quick_print(1);

	} /* __construct */

	function init() {
		/* .iso.org.dod.internet.mgmt.mib-2.system */
		$this->system = $this->walk(".1.3.6.1.2.1.1");

		$this->systemerror = $this->getErrno() || empty($this->system);
	} /* init() */

	/* get value from system cache */
	private function _getvalue($object_id) {

		/* TODO better matching  */

		if( isset($this->system["SNMPv2-MIB::$object_id"])) {
			$this->lastgetoid = "SNMPv2-MIB::$object_id";
			return $this->system["SNMPv2-MIB::$object_id"];
		} else {
			if( isset($this->system[".iso.org.dod.internet.mgmt.mib-2.system.$object_id"])) {
				$this->lastgetoid = ".iso.org.dod.internet.mgmt.mib-2.system.$object_id";
				return $this->system[".iso.org.dod.internet.mgmt.mib-2.system.$object_id"];
			} else {
				if( isset($this->system[$object_id])) {
					$this->lastgetoid = $object_id;
					return $this->system[$object_id];
				} else {
					foreach($this->system as $key => $value) {
						if(strpos($key, $object_id)) {
							$this->lastgetoid = $key;
							return $value;
						}
					}
					unset($key);
					unset($value);
				}
			}
		}

		return NULL;
	}

	function get($object_id, $preserve_keys = false) {

		if(!$this->systemerror)
			$retval = $this->_getvalue($object_id);
		else
			$retval = NULL;

		if($retval === NULL) {
			$this->lastgetoid = $object_id;
			$retval = parent::get($object_id,$preserve_keys);
		}

		return $retval;

	} /* get */

	function translatetonumeric($oid) {
		global $sg_cmd_snmptranslate;

		$val = exec(escapeshellcmd($sg_cmd_snmptranslate).' -On '.escapeshellarg($oid), $output, $retval);

		if($retval == 0)
			return $val;

		return FALSE;

	} /* translatetonumeric */
/*
	function get_new($object_id, $preserve_keys = false) {
		$result = parent::get($object_id,$preserve_keys);
		return $this->removeDatatype($result);
	}

	function walk_new($object_id) {
		$result = parent::walk($object_id);
		return $this->removeDatatype($result);
	}

*/
	/* use snmp_set_valueretrieval(SNMP_VALUE_PLAIN) instead */
/*	function removeDatatype($val) {
		return preg_replace('/^\w+: /','',$val);
	}
*/
	/* make something like $class->sysDescr work */
	function __get($name) {
		if($this->systemerror) {
			return;
		}

		$retval = $this->_getvalue($name);

		if($retval === NULL) {

			$trace = debug_backtrace();
			trigger_error(
					'Undefinierte Eigenschaft für __call(): ' . $name .
					' in ' . $trace[0]['file'] .
					' Zeile ' . $trace[0]['line'],
					E_USER_NOTICE);
		}

		return $retval;
	}


	/* Iteration through all system OIDs */
	/* Iterator */

	function current() {
		if($this->systemerror)
			return;

		return current($this->system);
	}

	function key() {
		if($this->systemerror)
			return;

		return key($this->system);
	}

	function next() {
		return next($this->system);
	}

	function valid() {
		return ($this->current() !== FALSE) && ($this->systemerror !== TRUE);
	}

	function rewind() {
		if($this->systemerror)
			return;

		reset($this->system);
	}

	/* END Iterator */

} /* mySNMP */

/* ------------------------------------------------------- */

class ifSNMP implements Iterator {
	private $snmpdevice;
	private $ifNumber = 0;
	private $ifTable;

	private $interfaceserror = TRUE;

	function __construct(&$snmpdevice) {
		$this->snmpdevice = $snmpdevice;

		$this->ifNumber = intval($this->snmpdevice->get('ifNumber.0'));

		$this->interfaceserror = $this->snmpdevice->getErrno();

		if(!$this->interfaceserror) {
			$this->getifTable();
		}
	}

	function getifTable() {
		$this->ifTable['ifIndex'] = $this->snmpdevice->walk('ifIndex',TRUE);
		$this->ifTable['ifDescr'] = $this->snmpdevice->walk('ifDescr',TRUE);
		$this->ifTable['ifAlias'] = $this->snmpdevice->walk('ifAlias',TRUE);
		$this->ifTable['ifName'] =  $this->snmpdevice->walk('ifName',TRUE);

		$this->ifTable['ifType'] =  $this->snmpdevice->walk('ifType',TRUE);

		$this->ifTable['ifSpeed'] =  $this->snmpdevice->walk('ifSpeed',TRUE);

		/* notation changes when SNMP_VALUE_PLAIN is set string -> hex!! */
		$this->ifTable['ifPhysAddress'] =  $this->snmpdevice->walk('ifPhysAddress',TRUE);

		$this->ifTable['ifOperStatus'] =  $this->snmpdevice->walk('ifOperStatus',TRUE);

		$this->ifTable['ifInOctets'] =  $this->snmpdevice->walk('ifInOctets',TRUE);
		$this->ifTable['ifOutOctets'] =  $this->snmpdevice->walk('ifOutOctets',TRUE);

		$this->ifTable['ifConnectorPresent'] =  $this->snmpdevice->walk('ifConnectorPresent',TRUE);

		$this->ifTable['ipaddress'] = array();

		/* ip address v4 only ipaddrtable */
		$ipAdEntIfIndex =  $this->snmpdevice->walk('ipAdEntIfIndex');

		if(!empty($ipAdEntIfIndex)) {
			$ipAdEntNetMask =  $this->snmpdevice->walk('ipAdEntNetMask');

			/* all addresses per interface */

			reset($ipAdEntNetMask);
			foreach($ipAdEntIfIndex as $oid => $value) {

				$netmask = current($ipAdEntNetMask);
				next($ipAdEntNetMask);

				$ipaddr = preg_replace('/.*\.([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)$/','$1',$oid);

				$ifindex =  array_search($value,$this->ifTable['ifIndex']);

				if($netmask != '0.0.0.0') {
					$maskbits = 32-log((ip2long($netmask) ^ 0xffffffff)+1,2);
					$net = ip2long($ipaddr) & ip2long($netmask);
					$bcast = $net | ( ip2long($netmask) ^ 0xffffffff);

					$this->ifTable['ipaddress'][$ifindex][$ipaddr] = array(
										'addrtype' => 'ipv4',
										'maskbits' => $maskbits,
										'net' => long2ip($net),
										'bcast' => long2ip($bcast)
										);
				}

			}
			unset($oid);
			unset($value);

	//		sg_var_dump_html($this->ipaddress);
	//		sg_var_dump_html($ipAdEntIfIndex);
	//		sg_var_dump_html($ipAdEntNetMask);
		} /* ipadentifindex */

		/* ipv4 ipv6 ipaddresstable */
		/* also overwrites ipv4 from ipaddrtable */
		$ipAddressIfIndex =  $this->snmpdevice->walk('ipAddressIfIndex');

		if(!empty($ipAddressIfIndex)) {
			$ipAddressPrefix =  $this->snmpdevice->walk('ipAddressPrefix');
			$ipAddressType =  $this->snmpdevice->walk('ipAddressType'); /* 1 unicast, 2 anycast, 3 braodcast */

			reset($ipAddressPrefix);
			reset($ipAddressType);
			foreach($ipAddressIfIndex as $oid => $value) {

				$prefix = current($ipAddressPrefix);
				next($ipAddressPrefix);

				$type = current($ipAddressType);
				next($ipAddressType);

				if(!preg_match('/.*\.(ipv[46]z?)\.\"(.*)"$/',$oid, $matches))
					continue;

				/* ipv4 or ipv6 address */
				$ifindex =  array_search($value,$this->ifTable['ifIndex']);

				$net = NULL;
				$maskbits = NULL;
				$bcast = NULL;

				if(preg_match('/\."([\.0-9a-fA-F:%]+)"\.([0-9]+)$/',$prefix,$prefixmatches)) {
					$net = $prefixmatches[1];
					$maskbits = $prefixmatches[2];

					/* ipv4 */
					if($matches[1] == 'ipv4z' || $matches[1] == 'ipv4') {
						$intnetmask = (int)(0xffffffff << (32 - $maskbits));
						$intnet = ip2long($net) & $intnetmask;
						$intbcast = $intnet | ( $intnetmask ^ 0xffffffff);

						$net = long2ip($intnet);
						$bcast = long2ip($intbcast);
					}
				}

				$this->ifTable['ipaddress'][$ifindex][$matches[2]] = array(
										'addrtype' => $matches[1],
										'maskbits' => $maskbits,
										'net' => $net,
										'bcast' => $bcast
										);
			}
			unset($oid);
			unset($value);

	//		sg_var_dump_html($ipAddressIfIndex);
	//		sg_var_dump_html($ipAddressPrefix);
		} /* ipaddressifindex */

	}

	function printifInfoTableHeader($suffix = "") {
		if($this->interfaceserror) {
			return;
		}

		echo "<tr>";
		foreach ($this->ifTable as $key => $value) {

			switch($key) {
				case 'ifOperStatus':
					$displayvalue = 'if Oper Status';
					break;
				case 'ifConnectorPresent':
					$displayvalue = 'if Con Pres';
					break;
				case 'ipaddress':
				case 'ipaddressv6':
					$displayvalue='';
					break;
				default:
					$displayvalue = $key;
			}

			if(!empty($displayvalue))
				echo "<th title=\"$key\">$displayvalue</th>";
		}
		unset($key);
		unset($value);

		echo "$suffix</tr>";
	}

	private $formfieldlist = array('ifName', 'ifDescr', 'ifAlias', 'ifPhysAddress');

	function printifInfoTableRow($ifIndex, $suffix = "", $hrefs = NULL) {
		if($this->interfaceserror) {
			return;
		}

		echo "<tr".($ifIndex % 2 ? ' style="background-color:#d8d8d8"' : '' ).">";

		foreach ($this->ifTable as $key => $value) {

			if($key == 'ipaddress' || $key == 'ipaddressv6')
				continue;

			$textfield = FALSE;

			/* minimize posted data to necessary fields */
			if(in_array($key,$this->formfieldlist)) {

				/* $value would contain raw values; $this->{$key}($ifIndex) post processed values */
				$fieldvalue = $this->{$key}($ifIndex);

				if(!empty($fieldvalue)) {
					if($key == 'ifDescr' || $key == 'ifAlias') {
						$formfield = '<input readonly="readonly" type="text" size="15" name="'.$key.'['.$ifIndex.']" value="'
								.$this->$key($ifIndex).'">';
						$textfield = TRUE;
					} else {
						$formfield = '<input type="hidden" name="'.$key.'['.$ifIndex.']" value="'.$fieldvalue.'">';
						echo $formfield;
					}
				} else {
					if($key == 'ifName') {
						/* create textfield set to ifDescr */
						$formfield = '<input type="text" size="8" name="'.$key.'['.$ifIndex.']" value="'
								.$this->ifDescr($ifIndex).'">';
						$textfield = TRUE;
					}

				}

			}


			if($textfield)
				$displayvalue=$formfield;
			else {
				$displayvalue = $this->{$key}($ifIndex);

				if(isset($hrefs) && isset($hrefs[$key])) {
					$displayvalue = "<a href=".$hrefs[$key].">$displayvalue</a>";
				}
			}

			echo "<td nowrap=\"nowrap\">$displayvalue</td>";
		}
		unset($key);
		unset($value);

		echo "$suffix</tr>";
	}

	function formatMACAddr($addr) {

		$retval = '';

		/* TODO test origin format */
		if(strlen($addr)== 6 ) {
			$retval =  unpack('H12',$addr);
			$retval = $retval[1];
		}

		/* often used as loopback on Enterasys switches */
		if($retval == '000000000000') {
			$retval = '';
		}

		return $retval;
	}

	function ifPhysAddress($index) {

		if(isset($this->ifTable['ifPhysAddress'][$index-1])) {
			return strtoupper($this->formatMACAddr($this->ifTable['ifPhysAddress'][$index-1]));
		}
	}

	function ipaddress($index) {
		if(isset($this->ifTable['ipaddress'][$index-1])) {
			return $this->ifTable['ipaddress'][$index-1];
		}

	}

	function &__get($name) {

		switch($name) {
			case 'ifNumber':
				return $this->{$name};
				break;
			case 'ipaddress':
				return $this->ifTable['ipaddress'];
				break;
		}

		$trace = debug_backtrace();

		trigger_error(
			'Undefinierte Eigenschaft für __get(): ' . $name .
			' in ' . $trace[0]['file'] .
			' Zeile ' . $trace[0]['line'],
			E_USER_NOTICE);

		return NULL;
	}

	/* $obj->ifDescr(3) = $ifTable[$name][$arg]*/
	function __call($name,$args) {

		if($this->interfaceserror)
			return;

		if(isset($this->ifTable[$name])) {
			if(isset($this->ifTable[$name][$args[0]-1])) {
				return $this->ifTable[$name][$args[0]-1];
			}
		} else {

			/* for debug */
			$trace = debug_backtrace();

			trigger_error(
				'Undefinierte Methode für __call(): ' . $name .
				' in ' . $trace[0]['file'] .
				' Zeile ' . $trace[0]['line'],
				E_USER_NOTICE);
		}

	return NULL;

	} /* __call */

	/* Iterator */

	private $IteratorIndex = 1;

	function current() {
		return $this->IteratorIndex;
	}

	function key() {
	}

	function next() {
		$this->IteratorIndex++;
	}

	function valid() {
		return ($this->IteratorIndex<=$this->ifNumber);
	}

	function rewind() {
		$this->IteratorIndex = 1;
	}

	/* END Iterator */
}

/* ------------------------------------------------------- */
/* ------------------------------------------------------- */
/* for debugging */
function sg_var_dump_html(&$var, $text = '') {

	echo "<pre>------------------Start Var Dump - $text -----------------------\n";
	var_dump($var);
	echo "\n---------------------END Var Dump - $text -----------------------</pre>";
}
?>
