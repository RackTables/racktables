#!/usr/bin/env php
<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

// A full RackTables upgrade includes reloading RackTables dictionary with the
// values from the PHP source code. This works well for upgrading a RackTables
// instance from one release to another, but RackTables development done in git
// repository clones usually requires to reload the dictionary more often than
// to run a full upgrade. This script does exactly that job: it only reloads
// the dictionary.

$script_mode = TRUE;
require_once 'inc/init.php';
require_once 'inc/dictionary.php';

// Try one row per query at a time so the user can see which values failed, if any.
$qlist = reloadDictionary (1);
$flist = array();
$dbxlink->beginTransaction();
foreach ($qlist as $query)
{
	try
	{
		$success = $dbxlink->exec ($query) !== FALSE;
	}
	catch (PDOException $e)
	{
		$success = FALSE;
	}
	if (! $success)
		$flist[] = $query;
}

if (count ($flist))
{
	$dbxlink->rollBack();
	foreach ($flist as $query)
		fprintf (STDERR, "FAILED: %s\n", $query);
	fprintf (STDERR, "Dictionary reload has been rolled back due to errors (%u queries failed out of %u).\n",
		count ($flist), count ($qlist));
	exit (1);
}
else
{
	$dbxlink->commit();
	printf ("Dictionary reload complete (%u SQL queries).\n", count ($qlist));
	exit (0);
}
