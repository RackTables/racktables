<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

# The array below maps availability of particular commands for each particular
# device breed. Array values are functions implemented in deviceconfig.php, which
# is not normally included until any of the functions is actually called.
$breedfunc = array
(
	'ios12-getcdpstatus-main'  => 'ios12ReadCDPStatus',
	'ios12-getlldpstatus-main' => 'ios12ReadLLDPStatus',
	'ios12-get8021q-main'      => 'ios12ReadVLANConfig',
	'ios12-get8021q-swports'   => 'ios12ReadSwitchPortList',
	'ios12-get8021q-top'       => 'ios12ScanTopLevel',
	'ios12-get8021q-readport'  => 'ios12PickSwitchportCommand',
	'ios12-get8021q-readvlan'  => 'ios12PickVLANCommand',
	'ios12-getportstatus-main' => 'ciscoReadInterfaceStatus',
	'ios12-getmaclist-main'    => 'ios12ReadMacList',
	'ios12-getportmaclist-main'=> 'ios12ReadMacList',
	'ios12-xlatepushq-main'    => 'ios12TranslatePushQueue',
	'ios12-getallconf-main'    => 'ios12SpotConfigText',
	'fdry5-get8021q-main'      => 'fdry5ReadVLANConfig',
	'fdry5-get8021q-top'       => 'fdry5ScanTopLevel',
	'fdry5-get8021q-readvlan'  => 'fdry5PickVLANSubcommand',
	'fdry5-get8021q-readport'  => 'fdry5PickInterfaceSubcommand',
	'fdry5-xlatepushq-main'    => 'fdry5TranslatePushQueue',
	'fdry5-getallconf-main'    => 'fdry5SpotConfigText',
	'fdry5-getportstatus-main' => 'foundryReadInterfaceStatus',
	'fdry5-getmaclist-main'    => 'fdry5ReadMacList',
	'fdry5-getportmaclist-main'=> 'fdry5ReadMacList',
	'vrp53-getlldpstatus-main' => 'vrpReadLLDPStatus',
	'vrp53-get8021q-main'      => 'vrp53ReadVLANConfig',
	'vrp53-get8021q-top'       => 'vrp53ScanTopLevel',
	'vrp53-get8021q-readport'  => 'vrp53PickInterfaceSubcommand',
	'vrp53-getportstatus-main' => 'vrpReadInterfaceStatus',
	'vrp53-getmaclist-main'    => 'vrp53ReadMacList',
	'vrp53-getportmaclist-main'=> 'vrp53ReadMacList',
	'vrp53-xlatepushq-main'    => 'vrp53TranslatePushQueue',
	'vrp53-getallconf-main'    => 'vrpSpotConfigText',
	'vrp55-getlldpstatus-main' => 'vrpReadLLDPStatus',
	'vrp55-get8021q-main'      => 'vrp55Read8021QConfig',
	'vrp55-getportstatus-main' => 'vrpReadInterfaceStatus',
	'vrp55-getmaclist-main'    => 'vrpReadMacList',
	'vrp55-getportmaclist-main'=> 'vrpReadMacList',
	'vrp55-xlatepushq-main'    => 'vrp55TranslatePushQueue',
	'vrp55-getallconf-main'    => 'vrpSpotConfigText',
	'vrp85-getlldpstatus-main' => 'vrpReadLLDPStatus',
	'vrp85-get8021q-main'      => 'vrp85Read8021QConfig',
	'vrp85-getportstatus-main' => 'vrpReadInterfaceStatus',
	'vrp85-getmaclist-main'    => 'vrpReadMacList',
	'vrp85-getportmaclist-main'=> 'vrpReadMacList',
	'vrp85-xlatepushq-main'    => 'vrp85TranslatePushQueue',
	'vrp85-getallconf-main'    => 'vrpSpotConfigText',
	'nxos4-getcdpstatus-main'  => 'ios12ReadCDPStatus',
	'nxos4-getlldpstatus-main' => 'ios12ReadLLDPStatus',
	'nxos4-get8021q-main'      => 'ios12ReadVLANConfig',
	'nxos4-getportstatus-main' => 'ciscoReadInterfaceStatus',
	'nxos4-getmaclist-main'    => 'nxos4ReadMacList',
	'nxos4-getportmaclist-main'=> 'nxos4ReadMacList',
	'nxos4-xlatepushq-main'    => 'nxos4TranslatePushQueue',
	'nxos4-getallconf-main'    => 'nxos4SpotConfigText',
	'dlink-get8021q-main'      => 'dlinkReadVLANConfig',
	'dlink-get8021q-top'       => 'dlinkScanTopLevel',
	'dlink-get8021q-pickvlan'  => 'dlinkPickVLANCommand',
	'dlink-getportstatus-main' => 'dlinkReadInterfaceStatus',
	'dlink-getmaclist-main'    => 'dlinkReadMacList',
	'dlink-getportmaclist-main'=> 'dlinkReadMacList',
	'dlink-xlatepushq-main'    => 'dlinkTranslatePushQueue',
	'linux-get8021q-main'      => 'linuxReadVLANConfig',
	'linux-getportstatus-main' => 'linuxReadInterfaceStatus',
	'linux-getmaclist-main'    => 'linuxReadMacList',
	'linux-getportmaclist-main'=> 'linuxReadMacList',
	'linux-xlatepushq-main'    => 'linuxTranslatePushQueue',
	'xos12-getlldpstatus-main' => 'xos12ReadLLDPStatus',
	'xos12-get8021q-main'      => 'xos12Read8021QConfig',
	'xos12-xlatepushq-main'    => 'xos12TranslatePushQueue',
	'xos12-getallconf-main'    => 'xos12SpotConfigText',
	'xos12-getportstatus-main' => 'xos12ReadInterfaceStatus',
	'xos12-getmaclist-main'    => 'xos12ReadMacList',
	'xos12-getportmaclist-main' => 'xos12ReadMacList',
	'jun10-get8021q-main'      => 'jun10Read8021QConfig',
	'jun10-xlatepushq-main'    => 'jun10TranslatePushQueue',
	'jun10-getallconf-main'    => 'jun10SpotConfigText',
	'jun10-getlldpstatus-main' => 'jun10ReadLLDPStatus',
	'jun10-getportstatus-main' => 'jun10ReadInterfaceStatus',
	'ftos8-xlatepushq-main'    => 'ftos8TranslatePushQueue',
	'ftos8-getlldpstatus-main' => 'ftos8ReadLLDPStatus',
	'ftos8-getmaclist-main'    => 'ftos8ReadMacList',
	'ftos8-getportmaclist-main'=> 'ftos8ReadMacList',
	'ftos8-getportstatus-main' => 'ftos8ReadInterfaceStatus',
	'ftos8-get8021q-main'      => 'ftos8Read8021QConfig',
	'ftos8-getallconf-main'    => 'ftos8SpotConfigText',
	'air12-xlatepushq-main'    => 'air12TranslatePushQueue',
	'air12-getallconf-main'    => 'ios12SpotConfigText',
	'air12-getcdpstatus-main'  => 'ios12ReadCDPStatus',
	'eos4-getallconf-main'     => 'eos4SpotConfigText',
	'eos4-getmaclist-main'     => 'eos4ReadMacList',
	'eos4-getportmaclist-main' => 'eos4ReadMacList',
	'eos4-getportstatus-main'  => 'eos4ReadInterfaceStatus',
	'eos4-getlldpstatus-main'  => 'eos4ReadLLDPStatus',
	'eos4-get8021q-main'       => 'eos4Read8021QConfig',
	'eos4-xlatepushq-main'     => 'eos4TranslatePushQueue',
	'ros11-getallconf-main'    => 'ros11SpotConfigText',
	'ros11-xlatepushq-main'    => 'ros11TranslatePushQueue',
	'ros11-getlldpstatus-main' => 'ros11ReadLLDPStatus',
	'ros11-getportstatus-main' => 'ros11ReadInterfaceStatus',
	'ros11-getmaclist-main'    => 'ros11ReadMacList',
	'ros11-getportmaclist-main'=> 'ros11ReadMacList',
	'ros11-get8021q-main'      => 'ros11Read8021QConfig',
	'ros11-get8021q-scantop'   => 'ros11Read8021QScanTop',
	'ros11-get8021q-vlandb'    => 'ros11Read8021QVLANDatabase',
	'ros11-get8021q-readports' => 'ros11Read8021QPorts',
	'iosxr4-xlatepushq-main'   => 'iosxr4TranslatePushQueue',
	'iosxr4-getallconf-main'   => 'iosxr4SpotConfigText',
	'iosxr4-getlldpstatus-main'=> 'iosxr4ReadLLDPStatus',
	'iosxr4-getportstatus-main'=> 'iosxr4ReadInterfaceStatus',
	'ucs-xlatepushq-main'      => 'ucsTranslatePushQueue',
	'ucs-getinventory-main'    => 'ucsReadInventory',
);

define ('MAX_GW_LOGSIZE', 1024*1024); // do not store more than 1 MB of log data

$breed_by_swcode = array
(
	244  => 'ios12', // IOS 12.0
	251  => 'ios12', // IOS 12.1
	252  => 'ios12', // IOS 12.2
	254  => 'ios12', // IOS 12.0 (router OS)
	255  => 'ios12', // IOS 12.1 (router OS)
	256  => 'ios12', // IOS 12.2 (router OS)
	257  => 'ios12', // IOS 12.3 (router OS)
	258  => 'ios12', // IOS 12.4 (router OS)
	1901 => 'ios12', // IOS 15.0 (switch)
	2082 => 'ios12', // IOS 15.1 (switch)
	2142 => 'ios12', // IOS 15.2 (switch)
	2667 => 'ios12', // IOS 15.0 (router OS)
	1963 => 'ios12', // IOS 15.1 (router OS)
	2668 => 'ios12', // IOS 15.2 (router OS)
	2669 => 'ios12', // IOS 15.3 (router OS)
	2670 => 'ios12', // IOS 15.4 (router OS)
	2671 => 'ios12', // IOS 15.5 (router OS)
	963  => 'nxos4', // NX-OS 4.0
	964  => 'nxos4', // NX-OS 4.1
	1365 => 'nxos4', // NX-OS 4.2
	1410 => 'nxos4', // NX-OS 5.0
	1411 => 'nxos4', // NX-OS 5.1
	1809 => 'nxos4', // NX-OS 5.2
	1643 => 'nxos4', // NX-OS 6.0
	2028 => 'nxos4', // NX-OS 6.1
	1352 => 'xos12', // Extreme XOS 12
	1360 => 'vrp53', // Huawei VRP 5.3
	1361 => 'vrp55', // Huawei VRP 5.5
	1369 => 'vrp55', // Huawei VRP 5.7
	2080 => 'vrp55', // Huawei VRP 5.11
	2081 => 'vrp55', // Huawei VRP 5.12
	2027 => 'vrp85', // Huawei VRP 8.5
	1363 => 'fdry5', // IronWare 5
	1364 => 'fdry5', // Brocade FastIron LS648
	1367 => 'jun10', // JunOS 10, switch
	2151 => 'jun10', // JunOS 11, switch
	2152 => 'jun10', // JunOS 12, switch
	2397 => 'jun10', // JunOS 13, switch
	2398 => 'jun10', // JunOS 14, switch
	2399 => 'jun10', // JunOS 15, switch
	1597 => 'jun10', // JunOS 10
	1598 => 'jun10', // JunOS 11
	1599 => 'jun10', // JunOS 12
	2400 => 'jun10', // JunOS 13
	2401 => 'jun10', // JunOS 14
	2402 => 'jun10', // JunOS 15
	1594 => 'ftos8', // Force10 FTOS 8
	1673 => 'air12', // AIR IOS 12.3
	1674 => 'air12', // AIR IOS 12.4
	1675 => 'eos4',  // Arista EOS 4
	1759 => 'iosxr4', // Cisco IOS XR 4.2
	1786 => 'ros11', // Marvell ROS 1.1

	//... linux items added by the loop below
);

$breed_by_hwcode = array (
	1362 => 'fdry5', // Brocade FastIron CX648
	//... dlink items added by the loop below
);

$breed_by_mgmtcode = array (
	1788 => 'ucs',
);

// add 'linux' items into $breed_by_swcode
$linux_sw_ranges = array (
	225,235,
	418,436,
	1331,1334,
	1395,1396,
	1417,1422,
	1704,1709,
);
for ($i = 0; $i + 1 < count ($linux_sw_ranges); $i += 2)
	for ($j = $linux_sw_ranges[$i]; $j <= $linux_sw_ranges[$i + 1]; $j++)
		$breed_by_swcode[$j] = 'linux';

// add 'dlink' items into $breed_by_hwcode
for ($i = 589; $i <= 637; $i++)
	$breed_by_hwcode[$i] = 'dlink';

function detectDeviceBreed ($object_id)
{
	global $breed_by_swcode, $breed_by_hwcode, $breed_by_mgmtcode;
	foreach (getAttrValues ($object_id) as $record)
		if ($record['id'] == 4 && array_key_exists ($record['key'], $breed_by_swcode))
			return $breed_by_swcode[$record['key']];
		elseif ($record['id'] == 2 && array_key_exists ($record['key'], $breed_by_hwcode))
			return $breed_by_hwcode[$record['key']];
		elseif ($record['id'] == 30 && array_key_exists ($record['key'], $breed_by_mgmtcode))
			return $breed_by_mgmtcode[$record['key']];
	return '';
}

function assertDeviceBreed ($object_id)
{
	if ('' == $breed = detectDeviceBreed ($object_id))
		throw new RTGatewayError ('Cannot determine device breed');
	return $breed;
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
		throw new RTGatewayError ("unsupported command '${command}' for breed '${breed}'");
	return $breedfunc["${breed}-${command}-main"];
}

function queryDevice ($object_id, $command, $args = array())
{
	$ret = NULL;
	$request = array ('opcode' => $command);
	if (is_array ($args) && count ($args))
	{
		$i = 1;
		foreach ($args as $arg)
		{
			$request["arg$i"] = $arg;
			$i++;
		}
	}
	$query = translateDeviceCommands ($object_id, array ($request));
	if ($command == 'xlatepushq')
		return $query;
	$breed = assertDeviceBreed ($object_id);
	$funcname = assertBreedFunction ($breed, $command);
	require_once 'deviceconfig.php';
	if (! is_callable ($funcname))
		throw new RTGatewayError ("undeclared function '${funcname}'");

	global $current_query_breed;
	$current_query_breed = $breed; // this global is used to auto-detect breed in shortenIfName
	for ($i = 0; $i < 3; $i++)
		try
		{
			$ret = $funcname (queryTerminal ($object_id, $query, FALSE));
			break;
		}
		catch (ERetryNeeded $e)
		{
			// some devices (e.g. Cisco IOS) refuse to print running configuration
			// while they are busy. The best way of treating this is retry a few times
			// before failing the request
			sleep (3);
			continue;
		}
		catch (Exception $e)
		{
			$current_query_breed = NULL;
			throw $e;
		}
	$current_query_breed = NULL;

	if (NULL !== ($subst = callHook ('alterDeviceQueryResult', $ret, $object_id, $command)))
		$ret = $subst;
	if (! isset ($ret))
		throw new RTGatewayError ("No result from $command");
	return $ret;
}

function translateDeviceCommands ($object_id, $crq, $vlan_names = NULL)
{
	$breed = assertDeviceBreed ($object_id);
	$funcname = assertBreedFunction ($breed, 'xlatepushq');
	require_once 'deviceconfig.php';
	if (! is_callable ($funcname))
		throw new RTGatewayError ("undeclared function '${funcname}'");
	global $current_query_breed;
	$current_query_breed = $breed; // this global is used to auto-detect breed in shortenIfName
	try
	{
		$ret = $funcname ($object_id, $crq, $vlan_names);
	}
	catch (Exception $e)
	{
		$current_query_breed = NULL;
		throw $e;
	}
	$current_query_breed = NULL;
	return $ret;
}

// takes settings struct (declared in queryTerminal) and CLI commands (plain text) as input by reference
// returns an array of command-line parameters to $ref_settings[0]['protocol']
// this function is called by callHook, so you can override/chain it
// to customize command-line options to particular gateways.
function makeGatewayParams ($object_id, $tolerate_remote_errors, /*array(&)*/$ref_settings, /*array(&)*/$ref_commands)
{
	$ret = array();
	$settings = &$ref_settings[0];
	$commands = &$ref_commands[0];

	$prepend_credentials = FALSE;
	switch ($settings['protocol'])
	{
		case 'telnet':
			$prepend_credentials = TRUE;
			$params_from_settings['port'] = 'port';
			$params_from_settings['prompt'] = 'prompt';
			$params_from_settings['connect-timeout'] = 'connect_timeout';
			$params_from_settings['timeout'] = 'timeout';
			$params_from_settings['prompt-delay'] = 'prompt_delay';
			if (isset ($settings['proto']))
				switch ($settings['proto'])
				{
					case 4:
						$params_from_settings[] = '-4';
						break;
					case 6:
						$params_from_settings[] = '-6';
						break;
					default:
						throw new RTGatewayError ("Proto '${settings['proto']}' is invalid. Valid protocols are: '4', '6'");
				}
			$params_from_settings[] = $settings['hostname'];
			break;
		case 'netcat':
			$prepend_credentials = TRUE;
			$params_from_settings['p'] = 'port';
			$params_from_settings['w'] = 'timeout';
			$params_from_settings['b'] = 'ncbin';
			$params_from_settings[] = $settings['hostname'];
			break;
		case 'sshnokey':
			$prepend_credentials = TRUE;
			$params_from_settings['proto'] = 'proto';
			$params_from_settings['prompt'] = 'prompt';
			$params_from_settings['prompt-delay'] = 'prompt_delay';
			$params_from_settings['username'] = 'username';
			$params_from_settings['password'] = 'password';
			$params_from_settings[] = $settings['hostname'];
			break;
		case 'ssh':
			$params_from_settings['sudo-user'] = 'sudo_user';
			$params_from_settings[] = '--';
			$params_from_settings['p'] = 'port';
			$params_from_settings['l'] = 'username';
			$params_from_settings['i'] = 'identity_file';
			if (isset ($settings['proto']))
				switch ($settings['proto'])
				{
					case 4:
						$params_from_settings[] = '-4';
						break;
					case 6:
						$params_from_settings[] = '-6';
						break;
					default:
						throw new RTGatewayError ("Proto '${settings['proto']}' is invalid. Valid protocols are: '4', '6'");
				}
			if (isset ($settings['connect_timeout']))
				$params_from_settings[] = '-oConnectTimeout=' . $settings['connect_timeout'];
			$params_from_settings[] = '-T';
			$params_from_settings[] = '-oStrictHostKeyChecking=no';
			$params_from_settings[] = '-oBatchMode=yes';
			$params_from_settings[] = '-oCheckHostIP=no';
			$params_from_settings[] = '-oLogLevel=ERROR';
			$params_from_settings[] = $settings['hostname'];
			break;
		case 'ucssdk': # remote XML through a Python backend
			# UCS in its current implementation besides the terminal_settings() provides
			# an additional username/password feed through the HTML form. Whenever the
			# user provides the credentials through the form, use these instead of the
			# credentials [supposedly] set by terminal_settings().
			global $script_mode;
			if ($script_mode != TRUE && ! isCheckSet ('use_terminal_settings'))
			{
				$settings['username'] = assertStringArg ('ucs_login');
				$settings['password'] = assertStringArg ('ucs_password');
			}
			foreach (array ('hostname', 'username', 'password') as $item)
				if (empty ($settings[$item]))
					throw new RTGatewayError ("${item} not available, check terminal_settings()");
			$commands = "login ${settings['hostname']} ${settings['username']} ${settings['password']}\n" . $commands;
			break;
		default:
			throw new RTGatewayError ("Invalid terminal protocol '${settings['protocol']}' specified");
	}

	// prepend commands by credentials
	if ($prepend_credentials)
	{
		if (isset ($settings['password']))
			$commands = $settings['password'] . "\n" . $commands;
		if (isset ($settings['username']))
			$commands = $settings['username'] . "\n" . $commands;
	}

	foreach ($params_from_settings as $param_name => $setting_name)
		if (is_int ($param_name))
			$ret[] = $setting_name;
		elseif (isset ($settings[$setting_name]))
			$ret[$param_name] = $settings[$setting_name];

	return $ret;
}

// This function returns a text output received from the device
// You can override connection settings by implement a callback named 'terminal_settings'.
// Errors are thrown as exceptions if not $tolerate_remote_errors, and shown as warnings otherwise.
function queryTerminal ($object_id, $commands, $tolerate_remote_errors = TRUE)
{
	$objectInfo = spotEntity ('object', $object_id);
	$endpoints = findAllEndpoints ($object_id, $objectInfo['name']);
	if (count ($endpoints) == 0)
		throw new RTGatewayError ('no management address set');
	if (count ($endpoints) > 1)
		throw new RTGatewayError ('cannot pick management address');

	$hide_warnings = FALSE;

	// telnet prompt and mode specification
	switch ($breed = detectDeviceBreed ($object_id))
	{
		case 'ios12':
		case 'air12':
		case 'ftos8':
			$protocol = 'netcat'; // default is netcat mode
			if ($breed == 'air12')
				$protocol = 'telnet'; # Aironet IOS is broken
			$prompt = '^(Login|[Uu]sername|Password): $|^\S+[>#]$|\[[^][]*\]\? $|\?\s+\[[^][]*\]\s*$|\[confirm yes\/no\]: $'; // set the prompt in case user would like to specify telnet protocol
			$commands = "terminal length 0\nterminal no monitor\n" . $commands;
			break;
		case 'fdry5':
			$protocol = 'netcat'; // default is netcat mode
			$prompt = '^(Login|Username|Password|Please Enter Password): $|^\S+[>#]$'; // set the prompt in case user would like to specify telnet protocol
			$commands = "skip-page-display\n" . $commands;
			# using ssh and sshnokey we'll always receive 'Connection to $ip closed by remote host.' upon exit
			# let's hide the warnings
			$tolerate_remote_errors = TRUE;
			$hide_warnings = TRUE;
			break;
		case 'vrp55':
			$commands = "terminal echo-mode line\n" . $commands;
			/* fall-through */
		case 'vrp85':
			$commands = "screen-length 0 temporary\n" . $commands;
			/* fall-through */
		case 'vrp53':
			$protocol = 'telnet';
			$prompt = '^\[[^[\]]+\]$|^<[^<>]+>$|^(Username|Password):$|\[[Yy][^\[\]]*\]\s*:?\s*$';
			break;
		case 'nxos4':
			$protocol = 'telnet';
			$prompt = '(^([Ll]ogin|[Pp]assword):|[>#]) $';
			$commands = "terminal length 0\nterminal no monitor\n" . $commands;
			break;
		case 'xos12':
			$protocol = 'telnet';
			$prompt = ': $|\.\d+ # $|\?\s*\([Yy]\/[Nn]\)\s*$';
			$commands = "disable clipaging\n" . $commands;
			break;
		case 'jun10':
			$protocol = 'telnet';
			$prompt = '^login: $|^Password:$|^\S+@\S+[>#] $';
			$commands = "set cli screen-length 0\n" . $commands;
			break;
		case 'eos4':
			$protocol = 'telnet'; # strict RFC854 implementation, netcat won't work
			$prompt = '^\xf2?(login|Username|Password): $|^\S+[>#]$';
			$commands = "enable\nno terminal monitor\nterminal length 0\n" . $commands;
			break;
		case 'ros11':
			$protocol = 'netcat'; # see ftos8 case
			$prompt = '^(User Name|\rPassword):$|^\r?\S+# $';
			$commands = "terminal datadump\n" . $commands;
			$commands .= "\n\n"; # temporary workaround for telnet server
			break;
		case 'iosxr4':
			$protocol = 'telnet';
			$prompt = '^\r?(Login|Username|Password): $|^\r?\S+[>#]$';
			$commands = "terminal length 0\nterminal monitor disable\n" . $commands;
			break;
		case 'ucs':
			$protocol = 'ucssdk';
			break;
		case 'dlink':
			$protocol = 'netcat';
			$commands = "disable clipaging\n" . $commands;
			break;
	}
	if (! isset ($protocol))
		$protocol = 'netcat';
	if (! isset ($prompt))
		$prompt = NULL;

	// set the default settings before calling user-defined callback
	$settings = array
	(
		'hostname' => $endpoints[0],
		'protocol' => $protocol,
		'port' => NULL,
		'prompt' => $prompt,
		'username' => NULL,
		'password' => NULL,
		'timeout' => 15,
		'connect_timeout' => 2,
		'prompt_delay' => 0.001, # 1ms
		'sudo_user' => NULL,
		'identity_file' => NULL,
	);

	// override default settings
	if (is_callable ('terminal_settings'))
		call_user_func ('terminal_settings', $objectInfo, array (&$settings));
	// make gateway-specific CLI params out of settings
	$params = callHook ('makeGatewayParams', $object_id, $tolerate_remote_errors, array (&$settings), array (&$commands));
	// call gateway
	$ret_code = callScript ($settings['protocol'], $params, $commands, $out, $errors);

	if (substr($settings['protocol'],0,3) != 'ssh' || ! $tolerate_remote_errors)
	{
		if ($errors != '')
			throw new RTGatewayError ("${settings['protocol']} error: " . rtrim ($errors));
		elseif ($ret_code !== 0)
			throw new RTGatewayError ("${settings['protocol']} error: result code $ret_code");
	}
	elseif ($errors != '') // ssh and tolerate and non-empty $errors
		foreach (explode ("\n", $errors) as $line)
			if ($line != '' && ! $hide_warnings)
				showWarning ("${settings['protocol']} ${settings['hostname']}: $line");
	return strtr($out, array("\r" => "")); // cut ^M symbols
}

function callScript ($gwname, $params, $in, &$out, &$errors)
{
	global $racktables_gwdir, $local_gwdir, $gateway_log, $script_child_res;
	if (isset ($gateway_log))
		$gateway_log = '';

	$cwd = NULL;
	if ('/' === substr ($gwname, 0, 1))
		// absolute path to executable
		$binary = $gwname;
	else
	{
		// path relative to one of RackTables' gwdirs
		if (isset ($local_gwdir) && file_exists ("$local_gwdir/$gwname"))
			$cwd = $local_gwdir;
		elseif (isset ($racktables_gwdir) && file_exists ("$racktables_gwdir/$gwname"))
			$cwd = $racktables_gwdir;
		if (! isset ($cwd))
			throw new RTGatewayError ("Could not find the gateway file called '$gwname'");
		$binary = "./$gwname";
	}

	$cmd_line = $binary;
	foreach ($params as $key => $value)
	{
		if (! isset ($value))
			continue;
		if (preg_match ('/^\d+$/', $key))
			$cmd_line .= " " . escapeshellarg ($value);
		else
		{
			if (strlen ($key) == 1)
				$cmd_line .= " " . escapeshellarg ("-$key") . " " . escapeshellarg ($value);
			else
				$cmd_line .= " " . escapeshellarg("--$key=$value");
		}
	}

	$pipes = array();
	$child = proc_open
	(
		$cmd_line,
		array (
			0 => array ('pipe', 'r'),
			1 => array ('pipe', 'w'),
			2 => array ('pipe', 'w'),
		),
		$pipes,
		$cwd
	);
	if (! is_resource ($child))
		throw new RTGatewayError ("cant execute $binary");
	$script_child_res = $child;

	$buff_size = 4096;
	$write_left = array ($pipes[0]);
	$read_left = array ($pipes[1], $pipes[2]);
	$write_fd = $write_left;
	$read_fd = $read_left;
	$except_fd = array();
	$out = '';
	$errors = '';
	$write_cursor = 0;
	while ((! empty ($read_fd) || ! empty ($write_fd)) && stream_select ($read_fd, $write_fd, $except_fd, NULL))
	{
		foreach ($write_fd as $fd)
		{
			if (0 != $written = fwrite ($fd, substr ($in, $write_cursor, $buff_size), $buff_size))
			{
				// log all communication data into global var
				if (isset ($gateway_log))
					$gateway_log .= preg_replace ('/^/m', '> ', substr ($in, $write_cursor, $written));

				$write_cursor += $written;
			}
			else
				$write_cursor = strlen ($in);

			if ($write_cursor >= strlen ($in))
			{
				// close input fd
				$write_left = array_diff ($write_left, array ($fd));
				fclose ($fd);
			}
		}
		foreach ($read_fd as $fd)
			if ('' == $str = fread ($fd, $buff_size))
			{
				// close output fd
				$read_left = array_diff ($read_left, array ($fd));
				fclose ($fd);
			}
			else
			{
				// log all communication data into global var
				if (isset ($gateway_log))
					$gateway_log .= $str;

				if ($fd == $pipes[1])
					$out .= $str;
				elseif ($fd == $pipes[2])
					$errors .= $str;
			}

		$write_fd = $write_left;
		$read_fd = $read_left;

		// limit the size of gateway_log
		if (isset ($gateway_log) && strlen ($gateway_log) > MAX_GW_LOGSIZE * 1.1)
			$gateway_log = substr ($gateway_log, -MAX_GW_LOGSIZE);

	}
	// we need to destroy our global link to the resource here.
	// PHP's proc_close implementation does nothing itself: it only returns
	// the value saved by the resource destructor. If the resource was not
	// destroyed (refcnt > 0), the return value is incorrect.
	$script_child_res = NULL;

	return proc_close ($child);
}

// returns the empty structure to be returned from getRunning8021QConfig
function constructRunning8021QConfig()
{
	return array
	(
		'vlanlist' => array(),   // unindexed list of integer VIDs
		'portdata' => array(),   // portconf structures indexed by portname
		'portconfig' => array(), // config text lines indexed by portname.
		'vlannames' => array(),  // vlan names indexed by VID
	);
}

function getRunning8021QConfig ($object_id)
{
	$ret = queryDevice ($object_id, 'get8021q');
	// Once there is no default VLAN in the parsed data, it means
	// something else was parsed instead of config text.
	if (!in_array (VLAN_DFL_ID, $ret['vlanlist']) || empty ($ret['portdata']))
		throw new RTGatewayError ('communication with device failed');
	return $ret;
}

function setDevice8021QConfig ($object_id, $pseudocode, $vlan_names)
{
	// FIXME: this is a perfect place to log intended changes
	// $object_id argument isn't used by default translating functions, but
	// may come in handy for overloaded versions of these.
	$commands = translateDeviceCommands ($object_id, $pseudocode, $vlan_names);
	$breed = detectDeviceBreed ($object_id);
	$output = queryTerminal ($object_id, $commands, FALSE);

	// throw an exception if Juniper did not allow to enter config mode or to commit changes
	if ($breed == 'jun10')
	{
		if (preg_match ('/>\s*configure exclusive\s*$[^#>]*?^error:/sm', $output))
			throw new RTGatewayError ("Configuration is locked by other user");
		elseif (preg_match ('/#\s*commit\s*$([^#]*?^error: .*?)$/sm', $output, $m))
			throw new RTGatewayError ("Commit failed: ${m[1]}");
	}
}

// if both $breed and $object_id are omitted, the breed could be auto-detected
// in case shortenIfName is called from within queryDevice
// (i.e. some function in deviceconfig.php)
function shortenIfName ($if_name, $breed = NULL, $object_id = NULL)
{
	// this is a port name we invented in snmp.php, do not translate it
	if (preg_match ('/^AC-in(-[12])?$/', $if_name))
		return $if_name;

	global $current_query_breed;
	if (! isset ($breed))
	{
		if (isset ($object_id))
			$breed = detectDeviceBreed ($object_id);
		elseif (isset ($current_query_breed))
			$breed = $current_query_breed;
	}

	switch ($breed)
	{
		case 'ios12':
			return ios12ShortenIfName_real ($if_name);
		case 'nxos4':
			return nxos4ShortenIfName ($if_name);
		case 'vrp53':
		case 'vrp55':
			return vrp5xShortenIfName ($if_name);
		case 'vrp85':
			return vrp85ShortenIfName ($if_name);
		case 'iosxr4':
			return iosxr4ShortenIfName ($if_name);
	}
	// default case is outside of switch()
	return ios12ShortenIfName ($if_name);
}

function ios12ShortenIfName_real ($ifname)
{
	$ifname = preg_replace ('@^FastEthernet(.+)$@', 'fa\\1', $ifname);
	$ifname = preg_replace ('@^GigabitEthernet(.+)$@', 'gi\\1', $ifname);
	$ifname = preg_replace ('@^TenGigabitEthernet(.+)$@', 'te\\1', $ifname);
	$ifname = preg_replace ('@^port-channel(.+)$@i', 'po\\1', $ifname);
	$ifname = strtolower ($ifname);
	$ifname = preg_replace ('/^(fa|gi|te|po)\s+(\d.*)/', '$1$2', $ifname);
	return $ifname;
}

function nxos4ShortenIfName ($ifname)
{
	$ifname = preg_replace ('@^(Ethernet|Eth)(.+)$@', 'e\\2', $ifname);
	$ifname = preg_replace ('@^port-channel(.+)$@i', 'po\\1', $ifname);
	$ifname = preg_replace ('@^mgmt(.+)$@i', 'mgmt\\1', $ifname);
	$ifname = strtolower ($ifname);
	$ifname = preg_replace ('/^(e|po|mgmt)\s+(\d.*)/', '$1$2', $ifname);
	return $ifname;
}

function vrp5xShortenIfName ($ifname)
{
	if (preg_match ('@^eth-trunk(\d+)$@i', $ifname, $m))
		return "Eth-Trunk${m[1]}";
	$ifname = preg_replace ('@^MEth(.+)$@', 'me\\1', $ifname);
	$ifname = preg_replace ('@^(?:Ethernet|Eth)(.+)$@', 'ether\\1', $ifname);
	$ifname = preg_replace ('@^(?:GigabitEthernet|GE)(.+)$@', 'gi\\1', $ifname);
	$ifname = preg_replace ('@^(?:XGigabitEthernet|XGE)(.+)$@', 'xg\\1', $ifname);
	$ifname = strtolower ($ifname);
	return $ifname;
}

function vrp85ShortenIfName ($ifname)
{
	if (preg_match ('@^eth-trunk(\d+)$@i', $ifname, $m))
		return "Eth-Trunk${m[1]}";
	// VRP 8.5 has already shortened ifNames
	$ifname = preg_replace ('@^MEth(.+)$@', 'me\\1', $ifname);
	$ifname = strtolower ($ifname);
	return $ifname;
}

function iosxr4ShortenIfName ($ifname)
{
	$ifname = preg_replace ('@^Mg(?:mtEth)?\s*(.*)$@', 'mg\\1', $ifname);
	$ifname = preg_replace ('@^FastEthernet\s*(.+)$@', 'fa\\1', $ifname);
	$ifname = preg_replace ('@^GigabitEthernet\s*(.+)$@', 'gi\\1', $ifname);
	$ifname = preg_replace ('@^TenGigE\s*(.*)$@', 'te\\1', $ifname);
	$ifname = preg_replace ('@^BE\s*(\d+)$@', 'bundle-ether\\1', $ifname);
	$ifname = preg_replace ('@^HundredGigE\s*(.+)$@i', 'hu\\1', $ifname);
	$ifname = strtolower ($ifname);
	return $ifname;
}

// this function should be kept as-is for compatibility.
// It is trying hard to complement every known breed.
function ios12ShortenIfName ($ifname)
{
	if (preg_match ('@^eth-trunk(\d+)$@i', $ifname, $m))
		return "Eth-Trunk${m[1]}";
	$ifname = preg_replace ('@^(?:[Ee]thernet|Eth)(.+)$@', 'e\\1', $ifname);
	$ifname = preg_replace ('@^FastEthernet(.+)$@', 'fa\\1', $ifname);
	$ifname = preg_replace ('@^(?:GigabitEthernet|GE)\s*(.+)$@', 'gi\\1', $ifname);
	$ifname = preg_replace ('@^TenGigabitEthernet(.+)$@', 'te\\1', $ifname);
	$ifname = preg_replace ('@^port-channel(.+)$@i', 'po\\1', $ifname);
	$ifname = preg_replace ('@^(?:XGigabitEthernet|XGE)(.+)$@', 'xg\\1', $ifname);
	$ifname = preg_replace ('@^LongReachEthernet(.+)$@', 'lo\\1', $ifname);
	$ifname = preg_replace ('@^Management(?:Ethernet)\s(.+)$@', 'ma\\1', $ifname);
	$ifname = preg_replace ('@^Et(\d.*)$@', 'e\\1', $ifname);
	$ifname = preg_replace ('@^TenGigE(.*)$@', 'te\\1', $ifname); // IOS XR4
	$ifname = preg_replace ('@^Mg(?:mtEth)?(.*)$@', 'mg\\1', $ifname); // IOS XR4
	$ifname = preg_replace ('@^BE(\d+)$@', 'bundle-ether\\1', $ifname); // IOS XR4
	$ifname = strtolower ($ifname);
	$ifname = preg_replace ('/^(e|fa|gi|te|po|xg|lo|ma)\s+(\d.*)/', '$1$2', $ifname);
	return $ifname;
}

?>
