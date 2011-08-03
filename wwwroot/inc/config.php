<?php

/*
Bumping up of CODE_VERSION requires precise timing as described in the
Developer's Guide. Otherwise working copies updated from SVN (for example,
committers' copies) can run into issues:
1. The source is rendered unfunctional after "svn update", asking users to
   finish the "upgrade".
2. Once the batch for the "upgrade" is executed, the queries, which get added
   to the batch later, are likely to receive no real execution.
3. In case the executed part of such partial batch is found incorrect later,
   but before the release, fixing the wrong queries will be harder, hence they
   have already been executed.
*/

define ('CODE_VERSION', '0.19.7');

$max_dict_key = array
(
	'0.17.0' => 988,
	'0.17.1' => 988,
	'0.17.2' => 1150,
	'0.17.3' => 1150,
	'0.17.4' => 1150,
	'0.17.5' => 1322,
	'0.17.6' => 1326,
	'0.17.7' => 1326,
	'0.17.8' => 1334,
	'0.17.9' => 1334,
	'0.17.10' => 1349,
	'0.17.11' => 1349,
	'0.18.0' => 1349,
	'0.18.1' => 1352,
	'0.18.2' => 1352,
	'0.18.3' => 1356,
	'0.18.4' => 1364,
	'0.18.5' => 1370,
	'0.18.6' => 1370,
	'0.18.7' => 1370,
	'0.19.0' => 1559,
	'0.19.1' => 1559,
	'0.19.2' => 1559,
	'0.19.3' => 1559,
	'0.19.4' => 1559,
	'0.19.5' => 1559,
	'0.19.6' => 1559,
	'0.19.7' => 1590,
	'0.19.8' => 1594,
	'0.20.0' => 1594,
);

?>
