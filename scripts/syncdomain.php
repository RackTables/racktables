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
	echo "\t\t[--nolock]\n";
	echo "\t\t[--stderr]\n";
	exit (1);
}

define ('PML_VERBOSE', 1 << 0); // display message only if --verbose option specified
define ('PML_NOTICE',  1 << 1); // the message is informational, do not write to STDERR
function print_message_line($text, $flags = 0)
{
	global $options;
	if (! array_key_exists ('verbose', $options) and $flags & PML_VERBOSE)
		return;
	$buff = date (DATE_RFC1123) . ": ${text}\n";
	echo $buff;
	if (array_key_exists ('stderr', $options) and ! ($flags & PML_NOTICE))
		fwrite (STDERR, $buff);
}

$options = getopt ('', array ('vdid:', 'max::', 'mode:', 'verbose', 'nolock', 'stderr'));
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

$max = array_fetch ($options, 'max', 0);
$nolock = array_key_exists ('nolock', $options);

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

if (! $nolock)
{
	$domain_key = isset ($options['vdid']) ? $options['vdid'] : 0;
	$filename = '/var/tmp/RackTables-syncdomain-' . $domain_key . '.pid';
	if (FALSE === $fp = @fopen ($filename, 'c+'))
	{
		print_message_line ("Failed to open ${filename}");
		exit (1);
	}
	$wouldblock = 0;
	if (! flock ($fp, LOCK_EX|LOCK_NB, $wouldblock) || $wouldblock)
	{
		$current_time = time();
		$stat = fstat ($fp);
		if (! isset ($stat['mtime']))
		{
			print_message_line ("Failed to obtain mtime of ${filename}");
			exit (1);
		}
		$pidfile_mtime = $stat['mtime'];
		if ($current_time < $pidfile_mtime)
		{
			print_message_line ("Warning: pidfile ${filename} mtime is in future!");
			exit (1);
		}
		// don't indicate failure unless the pidfile is 15 minutes or more old
		if ($current_time < $pidfile_mtime + 15 * 60)
			exit (0);
		print_message_line ("Failed to lock ${filename}, already locked by PID " . trim (fgets ($fp, 10)));
		exit (1);
	}

	ftruncate ($fp, 0);
	fwrite ($fp, getmypid() . "\n");
	// don't close $fp yet: we need to keep an flock
}

// fetch all the needed data from DB (preparing for DB connection loss)
$switch_queue = array();
foreach ($switch_list as $object_id)
{
	$cell = spotEntity ('object', $object_id);
	$new_disabled = ! considerConfiguredConstraint ($cell, 'SYNC_802Q_LISTSRC');
	$queue = detectVLANSwitchQueue (getVLANSwitchInfo ($object_id));
	if ($queue == 'disabled' xor $new_disabled)
		usePreparedExecuteBlade
		(
			'UPDATE VLANSwitch SET out_of_sync="yes", last_error_ts=NOW(), last_errno=? WHERE object_id=?',
			array ($new_disabled ? E_8021Q_SYNC_DISABLED : E_8021Q_NOERROR, $object_id)
		);
	elseif (in_array ($queue, $todo[$options['mode']]))
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
			$flags = PML_NOTICE;
			if (! $portsdone)
				$flags |= PML_VERBOSE;
			print_message_line ("Done '${object['dname']}': ${portsdone}", $flags);
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
		print_message_line ("Maximum of ${max} items reached, terminating", PML_NOTICE|PML_VERBOSE);
		break;
	}
}

// wait for all childs to exit
while ($switches_working > 0)
{
	--$switches_working;
	pcntl_waitpid (-1, $wait_status);
}

if (! $nolock)
{
	flock ($fp, LOCK_UN); // explicitly unlock file as PHP 5.3.2 made it mandatory
	if (FALSE === unlink ($filename))
	{
		print_message_line ("Failed removing pidfile ${filename}");
		exit (1);
	}
}
exit (0);
?>
