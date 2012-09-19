#!/usr/bin/env php
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

function print_message_line($text)
{
	echo gmdate (DATE_RFC1123) . ": ${text}\n";
}

$options = getopt ('', array ('vdid:', 'max::', 'mode:', 'verbose'));
if (!array_key_exists ('mode', $options))
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

$switch_list = array();
if (! isset ($options['vdid']))
	$switch_list = getVLANSwitches();
else
	try
	{
		$mydomain = getVLANDomain ($options['vdid']);
		foreach ($mydomain['switchlist'] as $switch)
			$switch_list[] = $switch['object_id'];
	}
	catch (RackTablesError $e)
	{
		print_message_line ("Cannot load domain data with ID ${options['vdid']}");
		print_message_line ($e->getMessage());
		exit (1);
	}

$todo = array
(
	'pull' => array ('sync_ready', 'resync_ready'),
	'push' => array ('sync_ready', 'resync_ready'),
	'pullall' => array ('sync_ready', 'resync_ready', 'sync_aging', 'resync_aging', 'done'),
);

$domain_key = isset ($options['vdid']) ? $options['vdid'] : 0;
$filename = '/var/tmp/RackTables-syncdomain-' . $domain_key . '.pid';
if (FALSE === $fp = @fopen ($filename, 'x+'))
{
	if (FALSE === $pidfile_mtime = filemtime ($filename))
	{
		print_message_line ("Failed to obtain mtime of ${filename}");
		exit (1);
	}
	$current_time = time();
	if ($current_time < $pidfile_mtime)
	{
		print_message_line ("Warning: pidfile ${filename} mtime is in future!");
		exit (1);
	}
	// don't indicate failure unless the pidfile is 15 minutes or more old
	if ($current_time < $pidfile_mtime + 15 * 60)
		exit (0);
	print_message_line ("Failed to lock ${filename}, already locked by PID " . mb_substr (file_get_contents ($filename), 0, 6));
	exit (1);
}

ftruncate ($fp, 0);
fwrite ($fp, getmypid() . "\n");
fclose ($fp);

// fetch all the needed data from DB (preparing for DB connection loss)
$switch_queue = array();
foreach ($switch_list as $object_id)
	if (in_array (detectVLANSwitchQueue (getVLANSwitchInfo ($object_id)), $todo[$options['mode']]))
	{
		$cell = spotEntity ('object', $object_id);
		if (considerConfiguredConstraint ($cell, 'SYNC_802Q_LISTSRC'))
			$switch_queue[] = $cell;
	}

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
				print_message_line ("Done '${object['dname']}': ${portsdone}");
		}
		catch (RackTablesError $e)
		{
			print_message_line ("FAILED '${object['dname']}': " . $e->getMessage());
		}
		if ($i_am_child)
			exit (0);
	}
	if (isset ($fork_res) and $fork_res > 0)
		++$switches_working;

	if (++$switchesdone == $max)
	{
		if ($verbose)
			print_message_line ("Maximum of ${max} items reached, terminating");
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
	print_message_line ("Failed removing pidfile ${filename}");
	exit (1);
}
exit (0);
?>
