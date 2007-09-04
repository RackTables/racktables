<?
/*
*
*  This file contains gateway functions for RackTables.
*  A gateway is an external executable, which provides
*  read-only or read-write access to some external entities.
*  Each gateway accepts its own list of command-line args
*  and then reads its stdin for requests. Each request consists
*  of one line and results in exactly one line of reply.
*  The replies must have the following syntax:
*  OK<space>any text up to the end of the line
*  ERR<space>any text up to the end of the line
*
*/


// This function launches specified gateway with specified
// command-line arguments and feeds it with the commands stored
// in the second arg as array.
// The answers are stored in another array, which is returned
// by this function. In the case when a gateway cannot be found,
// finishes prematurely or exits with non-zero return code,
// a single-item array is returned with the only "ERR" record,
// which explains the reason.
function queryGateway ($gwname, $arguments, $questions)
{
	$execpath = "./gateways/{$gwname}/main";
	$argline = implode (' ', $arguments);
	$dspec = array
	(
		0 => array ("pipe", "r"),
		1 => array ("pipe", "w"),
		2 => array ("file", "/dev/null", "a")
	);
	$pipes = array();
	$gateway = proc_open ("${execpath} ${argline}", $dspec, $pipes);
	if (!is_resource ($gateway))
		return array ('ERR proc_open() failed in queryGateway()');

// Dialogue starts. Send all questions.
	foreach ($questions as $q)
		fwrite ($pipes[0], "$q\n");
	fclose ($pipes[0]);

// Fetch replies.
	$answers = array();
	while (!feof($pipes[1]))
	{
		$a = fgets ($pipes[1]);
		if (empty ($a))
			continue;
		array_push ($answers, $a);
	}
	fclose($pipes[1]);

	$retval = proc_close ($gateway);
	if ($retval != 0)
		return array ("ERR gateway '${gwname}' returned ${retval}");
	return $answers;
}

?>
