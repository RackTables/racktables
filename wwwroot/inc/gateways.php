<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

// translating functions maps
$breedfunc = array
(
	'ios12-getcdpstatus-main'  => 'ios12ReadCDPStatus',
	'ios12-getlldpstatus-main' => 'ios12ReadLLDPStatus',
	'ios12-get8021q-main'      => 'ios12ReadVLANConfig',
	'ios12-get8021q-top'       => 'ios12ScanTopLevel',
	'ios12-get8021q-readport'  => 'ios12PickSwitchportCommand',
	'ios12-get8021q-readvlan'  => 'ios12PickVLANCommand',
	'ios12-getportstatus-main' => 'ciscoReadInterfaceStatus',
	'ios12-getmaclist-main'    => 'ios12ReadMacList',
	'ios12-xlatepushq-main'    => 'ios12TranslatePushQueue',
	'ios12-getallconf-main'    => 'ios12SpotConfigText',
	'fdry5-get8021q-main'      => 'fdry5ReadVLANConfig',
	'fdry5-get8021q-top'       => 'fdry5ScanTopLevel',
	'fdry5-get8021q-readvlan'  => 'fdry5PickVLANSubcommand',
	'fdry5-get8021q-readport'  => 'fdry5PickInterfaceSubcommand',
	'fdry5-xlatepushq-main'    => 'fdry5TranslatePushQueue',
	'fdry5-getallconf-main'    => 'fdry5SpotConfigText',
	'vrp53-getlldpstatus-main' => 'vrp5xReadLLDPStatus',
	'vrp53-get8021q-main'      => 'vrp53ReadVLANConfig',
	'vrp53-get8021q-top'       => 'vrp53ScanTopLevel',
	'vrp53-get8021q-readport'  => 'vrp53PickInterfaceSubcommand',
	'vrp53-getportstatus-main' => 'vrpReadInterfaceStatus',
	'vrp53-getmaclist-main'    => 'vrp53ReadMacList',
	'vrp53-xlatepushq-main'    => 'vrp53TranslatePushQueue',
	'vrp53-getallconf-main'    => 'vrp5xSpotConfigText',
	'vrp55-getlldpstatus-main' => 'vrp5xReadLLDPStatus',
	'vrp55-get8021q-main'      => 'vrp55Read8021QConfig',
	'vrp55-getportstatus-main' => 'vrpReadInterfaceStatus',
	'vrp55-getmaclist-main'    => 'vrp55ReadMacList',
	'vrp55-xlatepushq-main'    => 'vrp55TranslatePushQueue',
	'vrp55-getallconf-main'    => 'vrp5xSpotConfigText',
	'nxos4-getcdpstatus-main'  => 'ios12ReadCDPStatus',
	'nxos4-getlldpstatus-main' => 'nxos4ReadLLDPStatus',
	'nxos4-get8021q-main'      => 'nxos4Read8021QConfig',
	'nxos4-get8021q-top'       => 'nxos4ScanTopLevel',
	'nxos4-get8021q-readport'  => 'nxos4PickSwitchportCommand',
	'nxos4-get8021q-readvlan'  => 'nxos4PickVLANCommand',
	'nxos4-getportstatus-main' => 'ciscoReadInterfaceStatus',
	'nxos4-getmaclist-main'    => 'nxos4ReadMacList',
	'nxos4-xlatepushq-main'    => 'nxos4TranslatePushQueue',
	'nxos4-getallconf-main'    => 'nxos4SpotConfigText',
	'dlink-get8021q-main'      => 'dlinkReadVLANConfig',
	'dlink-get8021q-top'       => 'dlinkScanTopLevel',
	'dlink-get8021q-pickvlan'  => 'dlinkPickVLANCommand',
	'dlink-getportstatus-main' => 'dlinkReadInterfaceStatus',
	'dlink-getmaclist-main'    => 'dlinkReadMacList',
	'dlink-xlatepushq-main'    => 'dlinkTranslatePushQueue',
	'linux-get8021q-main'      => 'linuxReadVLANConfig',
	'linux-getportstatus-main' => 'linuxReadInterfaceStatus',
	'linux-getmaclist-main'    => 'linuxReadMacList',
	'linux-xlatepushq-main'    => 'linuxTranslatePushQueue',
	'xos12-getlldpstatus-main' => 'xos12ReadLLDPStatus',
	'xos12-get8021q-main'      => 'xos12Read8021QConfig',
	'xos12-xlatepushq-main'    => 'xos12TranslatePushQueue',
	'xos12-getallconf-main'    => 'xos12SpotConfigText',
	'jun10-get8021q-main'      => 'jun10Read8021QConfig',
	'jun10-xlatepushq-main'    => 'jun10TranslatePushQueue',
	'jun10-getallconf-main'    => 'jun10SpotConfigText',
	'ftos8-xlatepushq-main'    => 'ftos8TranslatePushQueue',
	'ftos8-getlldpstatus-main' => 'ftos8ReadLLDPStatus',
	'ftos8-getmaclist-main'    => 'ftos8ReadMacList',
	'ftos8-getportstatus-main' => 'ftos8ReadInterfaceStatus',
	'ftos8-get8021q-main'      => 'ftos8Read8021QConfig',
	'ftos8-getallconf-main'    => 'ftos8SpotConfigText',
	'air12-xlatepushq-main'    => 'air12TranslatePushQueue',
	'air12-getallconf-main'    => 'ios12SpotConfigText',
	'eos4-getallconf-main'     => 'eos4SpotConfigText',
	'eos4-getmaclist-main'     => 'eos4ReadMacList',
	'eos4-getportstatus-main'  => 'eos4ReadInterfaceStatus',
	'eos4-getlldpstatus-main'  => 'eos4ReadLLDPStatus',
	'eos4-get8021q-main'       => 'eos4Read8021QConfig',
	'eos4-xlatepushq-main'     => 'eos4TranslatePushQueue',
	'ros11-getallconf-main'    => 'ros11SpotConfigText',
	'ros11-xlatepushq-main'    => 'ros11TranslatePushQueue',
	'ros11-getlldpstatus-main' => 'ros11ReadLLDPStatus',
	'ros11-getportstatus-main' => 'ros11ReadInterfaceStatus',
	'ros11-getmaclist-main'    => 'ros11ReadMacList',
	'ros11-get8021q-main'      => 'ros11Read8021QConfig',
	'ros11-get8021q-scantop'   => 'ros11Read8021QScanTop',
	'ros11-get8021q-vlandb'    => 'ros11Read8021QVLANDatabase',
	'ros11-get8021q-readports' => 'ros11Read8021QPorts',
	'iosxr4-xlatepushq-main'    => 'iosxr4TranslatePushQueue',
	'iosxr4-getallconf-main'    => 'iosxr4SpotConfigText',
	'ucs-xlatepushq-main'      => 'ucsTranslatePushQueue',
	'ucs-getinventory-main'    => 'ucsReadInventory',
);

function detectDeviceBreed ($object_id)
{
	$breed_by_swcode = array
	(
		251 => 'ios12',
		252 => 'ios12',
		254 => 'ios12',
		963 => 'nxos4', // NX-OS 4.0
		964 => 'nxos4', // NX-OS 4.1
		1365 => 'nxos4', // NX-OS 4.2
		1410 => 'nxos4', // NX-OS 5.0, seems compatible
		1411 => 'nxos4', // NX-OS 5.1
		1643 => 'nxos4', // NX-OS 6.0
		1352 => 'xos12',
		1360 => 'vrp53',
		1361 => 'vrp55',
		1369 => 'vrp55', // VRP versions 5.5 and 5.7 seem to be compatible
		1363 => 'fdry5',
		1367 => 'jun10', # 10S
		1597 => 'jun10', # 10R
		1598 => 'jun10', # 11R
		1599 => 'jun10', # 12R
		1594 => 'ftos8',
		1673 => 'air12', # AIR IOS 12.3
		1674 => 'air12', # AIR IOS 12.4
		1675 => 'eos4',
		1759 => 'iosxr4', # Cisco IOS XR 4.2
		1786 => 'ros11', # Marvell ROS 1.1
		242 => 'linux',
		243 => 'linux',
		1331 => 'linux',
		1332 => 'linux',
		1333 => 'linux',
		1334 => 'linux',
		1395 => 'linux',
		1396 => 'linux',
	);
	for ($i = 225; $i <= 235; $i++)
		$breed_by_swcode[$i] = 'linux';
	for ($i = 418; $i <= 436; $i++)
		$breed_by_swcode[$i] = 'linux';
	for ($i = 1417; $i <= 1422; $i++)
		$breed_by_swcode[$i] = 'linux';
	$breed_by_hwcode = array();
	for ($i = 589; $i <= 637; $i++)
		$breed_by_hwcode[$i] = 'dlink';
	$breed_by_mgmtcode = array (1788 => 'ucs');
	foreach (getAttrValues ($object_id) as $record)
		if ($record['id'] == 4 and array_key_exists ($record['key'], $breed_by_swcode))
			return $breed_by_swcode[$record['key']];
		elseif ($record['id'] == 2 and array_key_exists ($record['key'], $breed_by_hwcode))
			return $breed_by_hwcode[$record['key']];
		elseif ($record['id'] == 30 and array_key_exists ($record['key'], $breed_by_mgmtcode))
			return $breed_by_mgmtcode[$record['key']];
	return '';
}

function validBreedFunction ($breed, $command)
{
	global $breedfunc;
	return array_key_exists ("${breed}-${command}-main", $breedfunc);
}

function assertBreedFunction ($breed, $command)
{
	global $breedfunc;
	if (! validBreedFunction ($breed, $command))
		throw new RTGatewayError ('unsupported command for this breed');
}

?>
