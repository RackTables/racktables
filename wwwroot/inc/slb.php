<?php

// *********************  Config-generating functions  *********************

// you may override the class name to make using your own triplet class
$triplet_class = 'SLBTriplet';

class SLBTriplet
{
	public $lb;
	public $vs;
	public $rs;
	public $slb;
	public $display_cells;

	function __construct ($lb_id, $vs_id, $rs_id, $db_row = NULL)
	{
		$this->lb = spotEntity ('object', $lb_id);
		$this->vs = spotEntity ('ipv4vs', $vs_id);
		$this->rs = spotEntity ('ipv4rspool', $rs_id);
		$this->display_cells = array ('lb', 'vs', 'rs');
		if (isset ($db_row))
			$this->slb = $db_row;
		else
		{
			$result = usePreparedSelectBlade
			(
				"SELECT prio, vsconfig, rsconfig FROM IPv4LB WHERE object_id = ? AND vs_id = ? AND rspool_id = ?",
				array ($lb_id, $vs_id, $rs_id)
			);
			if ($row = $result->fetch (PDO::FETCH_ASSOC))
				$this->slb = $row;
			else
				throw new RackTablesError ("SLB triplet not found in the DB");
		}
	}

	public static function getTriplets ($cell)
	{
		if (isset ($cell['ip']) and isset ($cell['version']) and $cell['version'] == 4)
			return self::getTripletsByIP ($cell['ip']);
		$ret = array();
		switch ($cell['realm'])
		{
			case 'object':
				$db_field = 'object_id';
				$order_fields = 'vs_id';
				$display_cells = array('vs', 'rs');
				break;
			case 'ipv4vs':
				$db_field = 'vs_id';
				$order_fields = 'rspool_id';
				$display_cells = array('rs', 'lb');
				break;
			case 'ipv4rspool':
				$db_field = 'rspool_id';
				$order_fields = 'vs_id';
				$display_cells = array('vs', 'lb');
				break;
			default:
				throw new InvalidArgException ('realm', $cell['realm']);
		}
		$result = usePreparedSelectBlade
		(
			"SELECT * FROM IPv4LB WHERE `$db_field` = ? ORDER BY $order_fields",
			array ($cell['id'])
		);
		$rows = $result->fetchAll (PDO::FETCH_ASSOC);
		unset ($result);
		global $triplet_class;
		foreach ($rows as $row)
		{
			$triplet = new $triplet_class ($row['object_id'], $row['vs_id'], $row['rspool_id'], $row);
			$triplet->display_cells = $display_cells;
			$ret[] = $triplet;
		}
		return $ret;
	}

	private static function getTripletsByIP ($ip)
	{
		$ret = array();
		$bin_ip = ip_quad2long ($ip);
		$result = usePreparedSelectBlade ("
SELECT DISTINCT IPv4LB.* 
FROM 
	IPv4LB INNER JOIN IPv4VS ON IPv4VS.id = IPv4LB.vs_id
	LEFT JOIN IPv4RS USING (rspool_id)
WHERE
	rsip = ? OR vip = ?
ORDER BY
	vs_id
	", array ($bin_ip, $bin_ip)
		);
		$rows = $result->fetchAll (PDO::FETCH_ASSOC);
		unset ($result);
		global $triplet_class;
		foreach ($rows as $row)
		{
			$triplet = new $triplet_class ($row['object_id'], $row['vs_id'], $row['rspool_id'], $row);
			$triplet->display_cells = array ('vs', 'lb', 'rs');
			$ret[] = $triplet;
		}
		return $ret;
	}

	protected function createParser()
	{
		return new MacroParser();
	}

	function generateConfig()
	{
		// fill the predefined macros
		$parser = $this->createParser();
		$parser->addMacro ('LB_ID', $this->lb['id']);
		$parser->addMacro ('LB_NAME', $this->lb['name']);
		$parser->addMacro ('VS_ID', $this->vs['id']);
		$parser->addMacro ('VS_NAME', $this->vs['name']);
		$parser->addMacro ('RSP_ID', $this->rs['id']);
		$parser->addMacro ('RSP_NAME', $this->rs['name']);
		$parser->addMacro ('VIP', $this->vs['vip']);
		$parser->addMacro ('VPORT', $this->vs['vport']);
		$parser->addMacro ('PROTO', $this->vs['proto']);
		$parser->addMacro ('PRIO', $this->slb['prio']);

		$defaults = getSLBDefaults (TRUE);
		$parser->addMacro ('GLOBAL_VS_CONF', dos2unix ($defaults['vs']));
		$parser->addMacro ('RSP_VS_CONF', dos2unix ($this->rs['vsconfig']));
		$parser->addMacro ('VS_VS_CONF', dos2unix ($this->vs['vsconfig']));
		$parser->addMacro ('SLB_VS_CONF', dos2unix ($this->slb['vsconfig']));

		// return the expanded VS template using prepared $macros array
		$ret = $parser->expand ("
# LB (id == %LB_ID%): %LB_NAME%
# VS (id == %VS_ID%): %VS_NAME%
# RS (id == %RSP_ID%): %RSP_NAME%
virtual_server %VIP% %VPORT% {
	protocol %PROTO%
	%GLOBAL_VS_CONF%
	%RSP_VS_CONF%
	%VS_VS_CONF%
	%SLB_VS_CONF%
");
		foreach (getRSListInPool ($this->rs['id']) as $rs)
		{
			if ($rs['inservice'] != 'yes')
				continue;
			$parser->pushdefs(); // backup macros
			$parser->addMacro ('RSIP', $rs['rsip']);
			$parser->addMacro ('RSPORT', isset ($rs['rsport']) ? $rs['rsport'] : $this->vs['vport']); // VS port is a default value for RS port

			$parser->addMacro ('GLOBAL_RS_CONF', dos2unix ($defaults['rs']));
			$parser->addMacro ('VS_RS_CONF', dos2unix ($this->vs['rsconfig']));
			$parser->addMacro ('RSP_RS_CONF', dos2unix ($this->rs['rsconfig']));
			$parser->addMacro ('SLB_RS_CONF', dos2unix ($this->slb['rsconfig']));
			$parser->addMacro ('RS_RS_CONF', $rs['rsconfig']);

			$ret .= $parser->expand ("
	real_server %RSIP% %RSPORT% {
		%GLOBAL_RS_CONF%
		%VS_RS_CONF%
		%RSP_RS_CONF%
		%SLB_RS_CONF%
		%RS_RS_CONF%
	}
");
			$parser->popdefs(); // restore original (VS-driven) macros
		}
		$ret .= "}\n";
		return $ret;
	}
}

class MacroParser
{
	protected $macros;
	protected $stack;

	function __construct()
	{
		$this->macros = array();
		$this->stack = array();
	}

	function pushdefs()
	{
		$this->stack[] = $this->macros;
	}

	function popdefs()
	{
		$this->macros = array_pop ($this->stack);
	}

	// cuts the subsequent defines from $value and stores the $name-$value define
	// if $value is unset, returns immediately
	public function addMacro ($name, $value)
	{
		if (! isset ($value))
			return;
		$new_value = ''; // value without defines
		$macro_deep = 0;
		foreach (explode ("\n", $value) as $line)
		{
			if (! $macro_deep)
			{
				if (preg_match ('/^([A-Za-z_0-9]+)=(.*)/', $line, $m))
				{
					// found macro definition
					$mname = $m[1];
					$mvalue = ltrim ($m[2]);
					if (substr ($mvalue, 0, 1) == '`') // quoted define value
					{
						$line = substr ($mvalue, 1);
						$mvalue = '';
						$macro_deep++;
					}
					else
					{
						$this->addMacro ($mname, rtrim ($mvalue));
					}
				}
				else
					$new_value .= $line . "\n";
			}

			if ($macro_deep)
			{
				for ($i = 0; $i < strlen ($line); $i++)
				{
					$c = $line[$i]; 
					if ($c == "'" and 0 == --$macro_deep)
					{
						$this->addMacro ($mname, $mvalue);
						$rest = substr ($line, $i + 1);
						if (preg_match ('/\S/', $rest))
							$new_value .= $rest . "\n";
						break;
					}
					elseif ($c == "`")
						$macro_deep++;
					$mvalue .= $c;
				}
				if ($macro_deep)
					$mvalue .= "\n";
			}
		}
		$this->macros[$name] = substr ($new_value, 0, -1); // trim last \n
	}

	// replaces all macro expansions (like %THIS%) by the results of expandMacro calls
	// Has some formatting logic:
	//  * indent each line of expansion if the expanding line contains only the macro reference
	//  * do not add empty line to the output if the expanding line contains only the macro reference and expands to empty string
	//  * trim last newline of expansion if there already is newline in source string after the macro reference
	public function expand ($text)
	{
		$ret = '';
		foreach (explode ("\n", $text) as $line)
		{
			$line .= "\n";
			$prev = '';
			while (preg_match ('/(.*?)%([A-Za-z_0-9]+)%(.*)/s', $line, $m))
			{
				$prev .= $m[1];
				$mname = $m[2];
				$line = $m[3];
				$mvalue = $this->expandMacro ($mname);
				$before_empty = preg_match ('/^\s*$/', $prev);
				$after_empty = preg_match ('/^[\s\n]*$/s', $line);
				$macro_empty = preg_match ('/^[\s\n]*$/s', $mvalue);
				$prev .= $mvalue;
				if ($before_empty and $after_empty)
				{
					if ($macro_empty)
					{
						$line = '';
						break;
					}
					// indent every line in $mvalue by $m[1]
					$mvalue = preg_replace ('/^/m', $m[1], $mvalue);
					$m[1] = '';
				}
				if ($after_empty and substr ($mvalue, -1, 1) == "\n")
					$mvalue = substr ($mvalue, 0, -1);
				$ret .= $m[1] . $mvalue;
			}
			$ret .= $line;
		}
		return substr ($ret, 0, -1); // trim last \n
	}

	// returns the result of expanding the named define, or '' if unset
	public function expandMacro ($name)
	{
		if (isset ($this->macros[$name]))
			return $this->expand ($this->macros[$name]);
		else
			return '';
	}
}

function buildEntityLVSConfig ($cell)
{
	$newconfig = "#\n#\n# This configuration has been generated automatically by RackTables\n#\n#\n";
	foreach (SLBTriplet::getTriplets ($cell) as $slb)
		$newconfig .= $slb->generateConfig();
	return $newconfig;
}

function buildLVSConfig ($object_id)
{
	return buildEntityLVSConfig (spotEntity ('object', $object_id));
}

// *********************  Database functions  *********************

function getIPv4VSOptions ()
{
	$ret = array();
	foreach (listCells ('ipv4vs') as $vsid => $vsinfo)
		$ret[$vsid] = $vsinfo['dname'] . (!strlen ($vsinfo['name']) ? '' : " (${vsinfo['name']})");
	return $ret;
}

function getIPv4RSPoolOptions ()
{
	$ret = array();
	foreach (listCells ('ipv4rspool') as $pool_id => $poolInfo)
		$ret[$pool_id] = $poolInfo['name'];
	return $ret;
}

function addRStoRSPool ($pool_id = 0, $rsip = '', $rsport = 0, $inservice = 'no', $rsconfig = '', $comment = '')
{
	return usePreparedExecuteBlade
	(
		'INSERT INTO IPv4RS (rsip, rsport, rspool_id, inservice, rsconfig, comment) VALUES (INET_ATON(?), ?, ?, ?, ?, ?)',
		array
		(
			$rsip,
			(!strlen ($rsport) or $rsport === 0) ? NULL : $rsport,
			$pool_id,
			$inservice == 'yes' ? 'yes' : 'no',
			!strlen ($rsconfig) ? NULL : $rsconfig,
			!strlen ($comment) ? NULL : $comment,
		)
	);
}

function addLBtoRSPool ($pool_id = 0, $object_id = 0, $vs_id = 0, $vsconfig = '', $rsconfig = '', $prio = '')
{
	usePreparedInsertBlade
	(
		'IPv4LB',
		array
		(
			'object_id' => $object_id,
			'rspool_id' => $pool_id,
			'vs_id' => $vs_id,
			'vsconfig' => (!strlen ($vsconfig) ? NULL : $vsconfig),
			'rsconfig' => (!strlen ($rsconfig) ? NULL : $rsconfig),
			'prio' => (!strlen ($prio) ? NULL : $prio),
		)
	);
}

function commitDeleteVS ($id = 0)
{
	releaseFiles ('ipv4vs', $id);
	destroyTagsForEntity ('ipv4vs', $id);
	usePreparedDeleteBlade ('IPv4VS', array ('id' => $id));
}

function commitUpdateRS ($rsid = 0, $rsip = '', $rsport = 0, $inservice = 'yes', $rsconfig = '', $comment = '')
{
	if (long2ip (ip2long ($rsip)) !== $rsip)
		throw new InvalidArgException ('$rsip', $rsip);
	usePreparedExecuteBlade
	(
		'UPDATE IPv4RS SET rsip=INET_ATON(?), rsport=?, inservice=?, rsconfig=?, comment=? WHERE id=?',
		array
		(
			$rsip,
			(!strlen ($rsport) or $rsport === 0) ? NULL : $rsport,
			$inservice,
			!strlen ($rsconfig) ? NULL : $rsconfig,
			!strlen ($comment) ? NULL : $comment,
			$rsid,
		)
	);
}

function commitUpdateVS ($vsid = 0, $vip = '', $vport = 0, $proto = '', $name = '', $vsconfig = '', $rsconfig = '')
{
	if (!strlen ($vip))
		throw new InvalidArgException ('$vip', $vip);
	if ($vport <= 0)
		throw new InvalidArgException ('$vport', $vport);
	if (!strlen ($proto))
		throw new InvalidArgException ('$proto', $proto);
	usePreparedExecuteBlade
	(
		'UPDATE IPv4VS SET vip=INET_ATON(?), vport=?, proto=?, name=?, vsconfig=?, rsconfig=? WHERE id=?',
		array
		(
			$vip,
			$vport,
			$proto,
			!strlen ($name) ? NULL : $name,
			!strlen ($vsconfig) ? NULL : $vsconfig,
			!strlen ($rsconfig) ? NULL : $rsconfig,
			$vsid,
		)
	);
}

function commitCreateRSPool ($name = '', $vsconfig = '', $rsconfig = '', $taglist = array())
{
	$new_pool_id = FALSE;
	if (usePreparedInsertBlade
	(
		'IPv4RSPool',
		array
		(
			'name' => (!strlen ($name) ? NULL : $name),
			'vsconfig' => (!strlen ($vsconfig) ? NULL : $vsconfig),
			'rsconfig' => (!strlen ($rsconfig) ? NULL : $rsconfig)
		)
	))
		$new_pool_id = lastInsertID();
	produceTagsForLastRecord ('ipv4rspool', $taglist);
	return $new_pool_id;
}

function commitDeleteRSPool ($pool_id = 0)
{
	releaseFiles ('ipv4rspool', $pool_id);
	destroyTagsForEntity ('ipv4rspool', $pool_id);
	usePreparedDeleteBlade ('IPv4RSPool', array ('id' => $pool_id));
}

function commitUpdateSLBDefConf ($data)
{
	saveScript('DefaultVSConfig', $data['vs']);
	saveScript('DefaultRSConfig', $data['rs']);
}

function getSLBDefaults ($do_cache_result = FALSE) {
	static $ret = array();

	if (! $do_cache_result)
		$ret = array();
	elseif (! empty ($ret))
		return $ret;

	$ret['vs'] = loadScript('DefaultVSConfig');
	$ret['rs'] = loadScript('DefaultRSConfig');
	return $ret;
}

// Return the list of all currently configured load balancers with their pool count.
function getLBList ()
{
	$result = usePreparedSelectBlade
	(
		"select object_id, count(rspool_id) as poolcount " .
		"from IPv4LB group by object_id order by object_id"
	);
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['object_id']] = $row['poolcount'];
	return $ret;
}

function getRSList ()
{
	$result = usePreparedSelectBlade
	(
		"select id, inservice, inet_ntoa(rsip) as rsip, rsport, rspool_id, rsconfig " .
		"from IPv4RS order by rspool_id, IPv4RS.rsip, rsport"
	);
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach (array ('inservice', 'rsip', 'rsport', 'rspool_id', 'rsconfig') as $cname)
			$ret[$row['id']][$cname] = $row[$cname];
	return $ret;
}

function getRSListInPool ($rspool_id)
{
	$ret = array();
	$query = "select id, inservice, inet_ntoa(rsip) as rsip, rsport, rsconfig, comment from " .
		"IPv4RS where rspool_id = ? order by IPv4RS.rsip, rsport";
	$result = usePreparedSelectBlade ($query, array ($rspool_id));
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		$ret[$row['id']] = $row;
	return $ret;
}

?>
