#!/usr/local/bin/php
<?php

$script_mode = TRUE;
require 'inc/init.php';

function usage()
{
	echo "Usage: <this file> <options>\n";
	echo "\t\t--vdid=<VLAN domain ID>\n";
	echo "\t\t--mode=pull\n";
	echo "\t\t--mode=pullall\n";
	echo "\t\t--mode=push\n";
	echo "\t\t[--max=<max_to_do>]\n";
	echo "\t\t[--verbose]\n";
	exit (1);
}

$options = getopt ('', array ('vdid:', 'max::', 'mode:', 'verbose'));
if (!array_key_exists ('vdid', $options) or !array_key_exists ('mode', $options))
	usage();

switch ($options['mode'])
{
case 'pullall':
	$do_push = FALSE;
	break;
case 'pull':
	$do_push = FALSE;
	break;
case 'push':
	$do_push = TRUE;
	break;
default:
	usage();
}

$max = array_key_exists ('max', $options) ? $options['max'] : 0;
$verbose = array_key_exists ('verbose', $options);

if (NULL === $mydomain = getVLANDomain ($options['vdid']))
{
	echo "Cannot load domain data with ID ${options['vdid']}\n";
	exit (1);
}

$todo = array
(
	'pull' => array ('sync', 'resync'),
	'push' => array ('sync'),
	'pullall' => array ('sync', 'resync', 'aging', 'done'),
);

$switchesdone = 0;
foreach ($mydomain['switchlist'] as $switch)
	if (in_array (detectVLANSwitchQueue (getVLANSwitchInfo ($switch['object_id'])), $todo[$options['mode']]))
	{
		$object = spotEntity ('object', $switch['object_id']);
		$portsdone = exec8021QDeploy ($switch['object_id'], $do_push);
		if ($portsdone or $verbose)
			echo "Done '${object['dname']}': ${portsdone}\n";
		if (++$switchesdone == $max)
		{
			if ($verbose)
				echo "Maximum of ${max} items reached, terminating\n";
			break;
		}
	}

exit (0);
?>
