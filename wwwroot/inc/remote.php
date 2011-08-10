<?php

function queryDevice ($object_id, $command)
{
	$breed = detectDeviceBreed ($object_id);
	if (empty ($breed))
		throw new RTGatewayError ("Can not determine device breed");

	if (! validBreedFunction ($breed, $command))
		throw new RTGatewayError ("unsupported command '$command' for the breed '$breed'");

	require_once 'deviceconfig.php';
	global $breedfunc;
	if (! is_callable ($breedfunc["$breed-$command-main"]))
		throw new RTGatewayError ("undeclared function '" . $breedfunc["$breed-$command-main"] . "'");
	$query = translateDeviceCommands ($object_id, array (array ('opcode' => $command)));
	if ($command == 'xlatepushq')
		return $query;
	else
	{
		$answer = queryTerminal ($object_id, $query, FALSE);
		return $breedfunc["$breed-$command-main"] ($answer);
	}
}

function translateDeviceCommands ($object_id, $crq, $vlan_names = NULL)
{
	require_once 'deviceconfig.php';
	$breed = detectDeviceBreed ($object_id);
	if (empty ($breed))
		throw new RTGatewayError ("Can not determine device breed");

	if (! validBreedFunction ($breed, 'xlatepushq'))
		throw new RTGatewayError ("unsupported command 'xlatepushq' for the breed '$breed'");

	global $breedfunc;
	return $breedfunc["$breed-xlatepushq-main"] ($object_id, $crq, $vlan_names);
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

	// default telnet prompt specification
	$prompt = NULL;
	switch ($breed = detectDeviceBreed ($object_id))
	{
		case 'ios12': // FIXME: add telnet prompt regexp here
		case 'fdry5': // FIXME: add telnet prompt regexp here
		case 'ftos8': // FIXME: add telnet prompt regexp here
			break;
		case 'vrp53':
		case 'vrp55':
			$prompt = '^\[[^[\]]+\]$|^<[^<>]+>$|^(Username|Password):$|(?:\[Y\/N\]|\(Y\/N\)\[[YN]\]):?$';
			break;
		case 'nxos4':
			$prompt = '[>:#] $';
			break;
		case 'xos12':
			$prompt = ': $|\.\d+ # $';
			break;
		case 'jun10':
			$prompt = '^login: $|^Password:$|^\S+@\S+[>#] $';
			break;
	}

	// set the default settings before calling user-defined callback
	$settings = array
	(
		'hostname' => $endpoints[0],
		'protocol' => empty ($prompt) ? 'netcat' : 'telnet',
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
	if (is_callable ('terminal_settings'))
		call_user_func ('terminal_settings', $objectInfo, array (&$settings)); // override settings

	if (! isset ($settings['port']) and $settings['protocol'] == 'netcat')
		$settings['port'] = 23;

	$params = array	( $settings['hostname'] );
	$params_from_settings = array();
	switch ($settings['protocol'])
	{
		case 'telnet':
		case 'netcat':
			// prepend command list with vendor-specific disabling pager command
			switch ($breed)
			{
				case 'ftos8':
				case 'ios12':
					$commands = "terminal length 0\n" . $commands;
					break;
				case 'nxos4':
					$commands = "terminal length 0\nterminal no monitor\n" . $commands;
					break;
				case 'xos12':
					$commands = "disable clipaging\n" . $commands;
					break;
				case 'vrp55':
					$commands = "screen-length 0 temporary\n" . $commands;
					break;
				case 'fdry5':
					$commands = "skip-page-display\n" . $commands;
					break;
				case 'jun10':
					$commands = "set cli screen-length 0\n" . $commands;
					break;
				case 'ftos':
					$commands = "terminal length 0\n" . $commands;
					break;
			}
			// prepend telnet commands by credentials
			if (isset ($settings['password']))
				$commands = $settings['password'] . "\n" . $commands;
			if (isset ($settings['username']))
				$commands = $settings['username'] . "\n" . $commands;
			// command-line options are specific to client: telnet or netcat
			switch ($settings['protocol'])
			{
				case 'telnet':
					$params_from_settings['port'] = 'port';
					$params_from_settings['prompt'] = 'prompt';
					$params_from_settings['connect-timeout'] = 'connect_timeout';
					$params_from_settings['timeout'] = 'timeout';
					$params_from_settings['prompt-delay'] = 'prompt_delay';
					break;
				case 'netcat':
					$params_from_settings['p'] = 'port';
					$params_from_settings['w'] = 'timeout';
					$params_from_settings['b'] = 'ncbin';
					break;
			}
			break;
		case 'ssh':
			$params_from_settings['port'] = 'port';
			$params_from_settings['username'] = 'username';
			$params_from_settings['i'] = 'identity_file';
			$params_from_settings['sudo-user'] = 'sudo_user';
			$params_from_settings['connect-timeout'] = 'connect_timeout';
			break;
		default:
			throw RTGatewayError ("Invalid terminal protocol '${settings['protocol']}' specified");
	}
	foreach ($params_from_settings as $param_name => $setting_name)
		if (isset ($settings[$setting_name]))
			if (is_int ($param_name))
				$params[] = $settings[$setting_name];
			else
				$params[$param_name] = $settings[$setting_name];

	$ret_code = callScript ($settings['protocol'], $params, $commands, $out, $errors);
	if ($settings['protocol'] != 'ssh' || ! $tolerate_remote_errors)
	{
		if (! empty ($errors))
			throw new RTGatewayError ("${settings['protocol']} error: " . rtrim ($errors));
		elseif ($ret_code !== 0)
			throw new RTGatewayError ("${settings['protocol']} error: result code $ret_code");
	}
	elseif (! empty ($errors)) // ssh and not tolerate and non-empty $errors
		foreach (explode ("\n", $errors) as $line)
			if (strlen ($line))
				showWarning ("${settings['protocol']} ${settings['hostname']}: $line");
	return strtr($out, array("\r" => "")); // cut ^M symbols
}

function callScript ($gwname, $params, $in, &$out, &$errors)
{
	global $racktables_gwdir, $local_gwdir;
	if (isset ($local_gwdir) && file_exists ("$local_gwdir/$gwname"))
		$dir = $local_gwdir;
	elseif (isset ($racktables_gwdir) && file_exists ("$racktables_gwdir/$gwname"))
		$dir = $racktables_gwdir;
	if (! isset ($dir))
		throw new RTGatewayError ("Could not find the gateway file called '$gwname'");

	$cmd_line = "./$gwname";
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
		$dir
	);
	if (! is_resource ($child))
		throw new RTGatewayError ("cant execute $dir/$gwname");
	fwrite ($pipes[0], $in);
	fclose ($pipes[0]);
	$out = stream_get_contents ($pipes[1]);
	$errors = stream_get_contents ($pipes[2]);
	return proc_close ($child);
}

function getRunning8021QConfig ($object_id)
{
	$ret = queryDevice ($object_id, 'get8021q');
	// Once there is no default VLAN in the parsed data, it means
	// something else was parsed instead of config text.
	if (!in_array (VLAN_DFL_ID, $ret['vlanlist']) || empty ($ret['portconfig']))
		throw new RTGatewayError ('communication with device failed');
	return $ret;
}

function setDevice8021QConfig ($object_id, $pseudocode, $vlan_names)
{
	// FIXME: this is a perfect place to log intended changes
	// $object_id argument isn't used by default translating functions, but
	// may come in handy for overloaded versions of these.
	$commands = translateDeviceCommands ($object_id, $pseudocode, $vlan_names);
	queryTerminal ($object_id, $commands, FALSE);
}

?>
