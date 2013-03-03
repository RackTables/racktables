<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

$vs_proto = array (
	'TCP' => 'TCP',
	'UDP' => 'UDP',
	'MARK' => 'MARK',
);

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

	public function __construct ($lb_id, $vs_id, $rs_id, $db_row = NULL)
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

	static public function getTriplets ($cell)
	{
		if (isset ($cell['ip_bin']) and isset ($cell['vslist']))
			// cell is IPAddress
			return self::getTripletsByIP ($cell['ip_bin']);
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

	static public function getTripletsByIP ($ip_bin)
	{
		$ret = array();
		$result = usePreparedSelectBlade ("
SELECT DISTINCT IPv4LB.*
FROM
	IPv4LB INNER JOIN IPv4VS ON IPv4VS.id = IPv4LB.vs_id
	LEFT JOIN IPv4RS USING (rspool_id)
WHERE
	rsip = ? OR vip = ?
ORDER BY
	vs_id
	", array ($ip_bin, $ip_bin)
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

	// this method is here to allow using of custom MacroParser implementation
	// override this function in the ancestor of SLBTriplet and return an instance 
	// of your custom parser (probably ancested of MacroParser)
	protected function createParser ($triplet)
	{
		return new MacroParser();
	}

	// creates parser and fills it with pre-defined macros
	public function prepareParser()
	{
		// fill the predefined macros
		$parser = $this->createParser ($this);
		$parser->addMacro ('LB_ID', $this->lb['id']);
		$parser->addMacro ('LB_NAME', $this->lb['name']);
		$parser->addMacro ('VS_ID', $this->vs['id']);
		$parser->addMacro ('VS_NAME', $this->vs['name']);
		$parser->addMacro ('RSP_ID', $this->rs['id']);
		$parser->addMacro ('RSP_NAME', $this->rs['name']);
		$parser->addMacro ('VIP', $this->vs['vip']);
		$parser->addMacro ('VPORT', $this->vs['vport']);
		$parser->addMacro ('PRIO', $this->slb['prio']);
		$parser->addMacro ('IP_VER', (strlen ($this->vs['vip_bin']) == 16) ? 6 : 4);

		if ($this->vs['proto'] == 'MARK')
		{
			$parser->addMacro ('PROTO', 'TCP');
			$mark = implode ('', unpack ('N', substr ($this->vs['vip_bin'], 0, 4)));
			$parser->addMacro ('MARK', $mark);
			$parser->addMacro ('VS_HEADER', "fwmark $mark");
		}
		else
		{
			$parser->addMacro ('VS_HEADER', $this->vs['vip'] . ' ' . $this->vs['vport']);
			$parser->addMacro ('PROTO', $this->vs['proto']);
		}

		$defaults = getSLBDefaults (TRUE);
		$parser->addMacro ('GLOBAL_VS_CONF', dos2unix ($defaults['vs']));
		$parser->addMacro ('RSP_VS_CONF', dos2unix ($this->rs['vsconfig']));
		$parser->addMacro ('VS_VS_CONF', dos2unix ($this->vs['vsconfig']));
		$parser->addMacro ('SLB_VS_CONF', dos2unix ($this->slb['vsconfig']));

		return $parser;
	}

	// fills the existing parser with RS-specific pre-defined macros.
	// $parser is the result of prepareParser, $rs_row - an item of getRSListInPool() result
	public function prepareParserForRS (&$parser, $rs_row)
	{
		$parser->addMacro ('RS_HEADER',  ($this->vs['proto'] == 'MARK' ? '%RSIP%' : '%RSIP% %RSPORT%'));
		$parser->addMacro ('RSIP', $rs_row['rsip']);
		$parser->addMacro ('RSPORT', isset ($rs_row['rsport']) ? $rs_row['rsport'] : $this->vs['vport']); // VS port is a default value for RS port
		$parser->addMacro ('RS_COMMENT', $rs_row['comment']);

		$defaults = getSLBDefaults (TRUE);
		$parser->addMacro ('GLOBAL_RS_CONF', dos2unix ($defaults['rs']));
		$parser->addMacro ('VS_RS_CONF', dos2unix ($this->vs['rsconfig']));
		$parser->addMacro ('RSP_RS_CONF', dos2unix ($this->rs['rsconfig']));
		$parser->addMacro ('SLB_RS_CONF', dos2unix ($this->slb['rsconfig']));
		$parser->addMacro ('RS_RS_CONF', $rs_row['rsconfig']);
	}

	public function generateConfig()
	{
		$parser = $this->prepareParser();

		// return the expanded VS template using prepared $macros array
		$ret = $parser->expand ("
# LB (id == %LB_ID%): %LB_NAME%
# VS (id == %VS_ID%): %VS_NAME%
# RS (id == %RSP_ID%): %RSP_NAME%
virtual_server %VS_HEADER% {
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
			$this->prepareParserForRS ($parser, $rs);
			// do not add v6 reals into v4 service and vice versa
			$rsip_bin = ip_checkparse ($parser->expandMacro ('RSIP'));
			if ($rsip_bin !== FALSE && strlen ($rsip_bin) == strlen ($this->vs['vip_bin']))
				foreach (explode (',', $parser->expandMacro ('RSPORT')) as $rsp_token)
				{
					$port_range = explode ('-', $rsp_token);
					if (count ($port_range) < 1)
						throw new InvalidArgException ('RSPORT', $rsp_token, "invalid RS port range");
					if (count ($port_range) < 2)
						$port_range[] = $port_range[0];
					if ($port_range[0] > $port_range[1])
						throw new InvalidArgException ('RSPORT', $rsp_token, "invalid RS port range");

					for ($rsport = $port_range[0]; $rsport <= $port_range[1]; $rsport++)
					{
						$parser->pushdefs();
						$parser->addMacro ('RSPORT', $rsport);
						$ret .= $parser->expand ("
	%RS_PREPEND%
	real_server %RS_HEADER% {
		%GLOBAL_RS_CONF%
		%VS_RS_CONF%
		%RSP_RS_CONF%
		%SLB_RS_CONF%
		%RS_RS_CONF%
	}
");
						$parser->popdefs();
					}
				}
			$parser->popdefs(); // restore original (VS-driven) macros
		}
		$ret .= "}\n";
		return $ret;
	}
}

class MacroParser
{
	protected $macros; // current macro context
	protected $stack; // macro contexts saved by pushdefs()
	protected $trace; // recursive macro expansion path

	public function __construct()
	{
		$this->macros = array();
		$this->stack = array();
		$this->trace = array();
	}

	public function pushdefs()
	{
		$this->stack[] = $this->macros;
	}

	public function popdefs()
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
		array_push ($this->trace, $name);
		if (isset ($this->macros[$name]))
			$ret = $this->expand ($this->macros[$name]);
		else
			$ret = '';
		array_pop ($this->trace);
		return $ret;
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

function addRStoRSPool ($pool_id, $rsip_bin, $rsport = 0, $inservice = 'no', $rsconfig = '', $comment = '')
{
	return usePreparedInsertBlade
	(
		'IPv4RS',
		array
		(
			'rspool_id' => $pool_id,
			'rsip' => $rsip_bin,
			'rsport' => (!strlen ($rsport) or $rsport === 0) ? NULL : $rsport,
			'inservice' => $inservice == 'yes' ? 'yes' : 'no',
			'rsconfig' => !strlen ($rsconfig) ? NULL : $rsconfig,
			'comment' => !strlen ($comment) ? NULL : $comment,
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

function commitUpdateRS ($rsid, $rsip_bin, $rsport = 0, $inservice = 'yes', $rsconfig = '', $comment = '')
{
	usePreparedExecuteBlade
	(
		'UPDATE IPv4RS SET rsip=?, rsport=?, inservice=?, rsconfig=?, comment=? WHERE id=?',
		array
		(
			$rsip_bin,
			(!strlen ($rsport) or $rsport === 0) ? NULL : $rsport,
			$inservice,
			!strlen ($rsconfig) ? NULL : $rsconfig,
			!strlen ($comment) ? NULL : $comment,
			$rsid,
		)
	);
}

// $vport is ignored if $proto == 'MARK'
function commitUpdateVS ($vsid, $vip_bin, $vport = 0, $proto = '', $name = '', $vsconfig = '', $rsconfig = '')
{
	if ($proto != 'MARK' && $vport <= 0)
		throw new InvalidArgException ('vport', $vport);
	if (!strlen ($proto))
		throw new InvalidArgException ('proto', $proto);
	return usePreparedUpdateBlade
	(
		'IPv4VS',
		array
		(
			'vip' => $vip_bin,
			'vport' => ($proto == 'MARK' ? NULL : $vport),
			'proto' => $proto,
			'name' => !strlen ($name) ? NULL : $name,
			'vsconfig' => !strlen ($vsconfig) ? NULL : $vsconfig,
			'rsconfig' => !strlen ($rsconfig) ? NULL : $rsconfig,
		),
		array ('id' => $vsid)
	);
}

function commitCreateRSPool ($name = '', $vsconfig = '', $rsconfig = '', $tagidlist = array())
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
	produceTagsForNewRecord ('ipv4rspool', $tagidlist, $new_pool_id);
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

function getSLBDefaults ($do_cache_result = FALSE)
{
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
		"select id, inservice, rsip as rsip_bin, rsport, rspool_id, rsconfig " .
		"from IPv4RS order by rspool_id, IPv4RS.rsip, rsport"
	);
	$ret = array ();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$row['rsip'] = ip_format ($row['rsip_bin']);
		foreach (array ('inservice', 'rsip_bin', 'rsip', 'rsport', 'rspool_id', 'rsconfig') as $cname)
			$ret[$row['id']][$cname] = $row[$cname];
	}
	return $ret;
}

function getRSListInPool ($rspool_id)
{
	$ret = array();
	$query = "select id, inservice, rsip as rsip_bin, rsport, rsconfig, comment from " .
		"IPv4RS where rspool_id = ? order by IPv4RS.rsip, rsport";
	$result = usePreparedSelectBlade ($query, array ($rspool_id));
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
	{
		$row['rsip'] = ip_format ($row['rsip_bin']);
		$ret[$row['id']] = $row;
	}
	return $ret;
}

?>
