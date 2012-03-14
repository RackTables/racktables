#!/usr/local/bin/php
<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

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

try
{
	$mydomain = getVLANDomain ($options['vdid']);
}
catch (RackTablesError $e)
{
	echo "Cannot load domain data with ID ${options['vdid']}\n";
	echo $e->getMessage() . "\n";
	exit (1);
}

$todo = array
(
	'pull' => array ('sync_ready', 'resync_ready'),
	'push' => array ('sync_ready', 'resync_ready'),
	'pullall' => array ('sync_ready', 'resync_ready', 'sync_aging', 'resync_aging', 'done'),
);

$filename = '/var/tmp/RackTables-syncdomain-' . $options['vdid'] . '.pid';
if (FALSE === $fp = @fopen ($filename, 'x+'))
{
	if (FALSE === $pidfile_mtime = filemtime ($filename))
	{
		echo "Failed to obtain mtime of ${filename}\n";
		exit (1);
	}
	$current_time = time();
	if ($current_time < $pidfile_mtime)
	{
		echo "Warning: pidfile ${filename} mtime is in future!\n";
		exit (1);
	}
	// don't indicate failure unless the pidfile is 15 minutes or more old
	if ($current_time < $pidfile_mtime + 15 * 60)
		exit (0);
	echo "Failed to lock ${filename}, already locked by PID " . mb_substr (file_get_contents ($filename), 0, 6) . "\n";
	exit (1);
}

ftruncate ($fp, 0);
fwrite ($fp, getmypid() . "\n");
fclose ($fp);

// fetch all the needed data from DB (preparing for DB connection loss)
$switch_queue = array();
foreach ($mydomain['switchlist'] as $switch)
	if (in_array (detectVLANSwitchQueue (getVLANSwitchInfo ($switch['object_id'])), $todo[$options['mode']]))
		$switch_queue[] = spotEntity ('object', $switch['object_id']);

// YOU SHOULD NOT USE DB FUNCTIONS BELOW IN THE PARENT PROCESS
// THE PARENT'S DB CONNECTION IS LOST DUE TO RECONNECTING IN THE CHILD
$fork_slots = getConfigVar ('SYNCDOMAIN_MAX_PROCESSES');
$do_fork = ($fork_slots > 1) and extension_loaded ('pcntl');
if ($fork_slots > 1 and ! $do_fork)
	throw new RackTablesError ('PHP extension \'pcntl\' not found, can not use childs', RackTablesError::MISCONFIGURED);
$switches_working = 0;
$switchesdone = 0;
foreach ($switch_queue as $object)
{
	if ($do_fork)
	{
		// wait for the next free slot
		while ($fork_slots <= $switches_working)
		{
			pcntl_waitpid (-1, $wait_status);
			--$switches_working;
		}
		$i_am_child = (0 === $fork_res = pcntl_fork());
	}
	if (! $do_fork or $i_am_child)
	{
		try
		{
			// make a separate DB connection for correct concurrent transactions handling
			if ($i_am_child)
				connectDB();
			$portsdone = exec8021QDeploy ($object['id'], $do_push);
			if ($portsdone or $verbose)
				echo "Done '${object['dname']}': ${portsdone}\n";
		}
		catch (RackTablesError $e)
		{
			echo "FAILED '${object['dname']}': " . $e->getMessage() . "\n";
		}
		if ($i_am_child)
			exit (0);
	}
	if (isset ($fork_res) and $fork_res > 0)
		++$switches_working;

	if (++$switchesdone == $max)
	{
		if ($verbose)
			echo "Maximum of ${max} items reached, terminating\n";
		break;
	}
}

// wait for all childs to exit
while ($switches_working > 0)
{
	--$switches_working;
	pcntl_waitpid (-1, $wait_status);
}

if (FALSE === unlink ($filename))
{
	echo "Failed removing pidfile ${filename}\n";
	exit (1);
}
exit (0);
?>
