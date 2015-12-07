<?php

function renderSystemReports ()
{
	$tmp = array
	(
		array
		(
			'title' => 'Dictionary/objects',
			'type' => 'counters',
			'func' => 'getDictStats'
		),
		array
		(
			'title' => 'Rackspace',
			'type' => 'counters',
			'func' => 'getRackspaceStats'
		),
		array
		(
			'title' => 'Files',
			'type' => 'counters',
			'func' => 'getFileStats'
		),
		array
		(
			'title' => 'Tags top list',
			'type' => 'custom',
			'func' => 'renderTagStats'
		),
	);
	renderReports ($tmp);
}

function renderLocalReports ()
{
	global $localreports;
	renderReports ($localreports);
}

function renderRackCodeReports ()
{
	$tmp = array
	(
		array
		(
			'title' => 'Stats',
			'type' => 'counters',
			'func' => 'getRackCodeStats',
			'include' => 'code.php',
		),
		array
		(
			'title' => 'Warnings',
			'type' => 'messages',
			'func' => 'getRackCodeWarnings',
			'include' => 'code.php',
		),
	);
	renderReports ($tmp);
}

function renderIPv4Reports ()
{
	$tmp = array
	(
		array
		(
			'title' => 'Stats',
			'type' => 'counters',
			'func' => 'getIPv4Stats'
		),
	);
	renderReports ($tmp);
}

function renderIPv6Reports ()
{
	$tmp = array
	(
		array
		(
			'title' => 'Stats',
			'type' => 'counters',
			'func' => 'getIPv6Stats'
		),
	);
	renderReports ($tmp);
}

function renderPortsReport ()
{
	$tmp = array();
	foreach (getPortIIFOptions() as $iif_id => $iif_name)
		if (count (getPortIIFStats ($iif_id)))
			$tmp[] = array
			(
				'title' => $iif_name,
				'type' => 'meters',
				'func' => 'getPortIIFStats',
				'args' => $iif_id,
			);
	renderReports ($tmp);
}

function render8021QReport ()
{
	if (!count ($domains = getVLANDomainOptions()))
	{
		echo '<center><h3>(no VLAN configuration exists)</h3></center>';
		return;
	}
	$vlanstats = array();
	for ($i = VLAN_MIN_ID; $i <= VLAN_MAX_ID; $i++)
		$vlanstats[$i] = array();
	$header = '<tr><th>&nbsp;</th>';
	foreach ($domains as $domain_id => $domain_name)
	{
		foreach (getDomainVLANList ($domain_id) as $vlan_id => $vlan_info)
			$vlanstats[$vlan_id][$domain_id] = $vlan_info;
		$header .= '<th>' . mkA ($domain_name, 'vlandomain', $domain_id) . '</th>';
	}
	$header .= '</tr>';
	$output = $available = array();
	for ($i = VLAN_MIN_ID; $i <= VLAN_MAX_ID; $i++)
		if (!count ($vlanstats[$i]))
			$available[] = $i;
		else
			$output[$i] = FALSE;
	foreach (listToRanges ($available) as $span)
	{
		if ($span['to'] - $span['from'] < 4)
			for ($i = $span['from']; $i <= $span['to']; $i++)
				$output[$i] = FALSE;
		else
		{
			$output[$span['from']] = TRUE;
			$output[$span['to']] = FALSE;
		}
	}
	ksort ($output, SORT_NUMERIC);
	$header_delay = 0;
	startPortlet ('VLAN existence per domain');
	echo '<table border=1 cellspacing=0 cellpadding=5 align=center class=rackspace>';
	foreach ($output as $vlan_id => $tbc)
	{
		if (--$header_delay <= 0)
		{
			echo $header;
			$header_delay = 25;
		}
		echo '<tr class="state_' . (count ($vlanstats[$vlan_id]) ? 'T' : 'F');
		echo '"><th class=tdright>' . $vlan_id . '</th>';
		foreach (array_keys ($domains) as $domain_id)
		{
			echo '<td class=tdcenter>';
			if (array_key_exists ($domain_id, $vlanstats[$vlan_id]))
				echo mkA ('&exist;', 'vlan', "${domain_id}-${vlan_id}");
			else
				echo '&nbsp;';
			echo '</td>';
		}
		echo '</tr>';
		if ($tbc)
			echo '<tr class="state_A"><th>...</th><td colspan=' . count ($domains) . '>&nbsp;</td></tr>';
	}
	echo '</table>';
	finishPortlet();
}

function renderReports ($what)
{
	if (!count ($what))
		return;
	echo "<table align=center>\n";
	foreach ($what as $item)
	{
		if (isset ($item['include']))
			require_once $item['include'];

		echo "<tr><th colspan=2><h3>${item['title']}</h3></th></tr>\n";
		switch ($item['type'])
		{
			case 'counters':
				if (array_key_exists ('args', $item))
					$data = $item['func'] ($item['args']);
				else
					$data = $item['func'] ();
				foreach ($data as $header => $data)
					echo "<tr><td class=tdright>${header}:</td><td class=tdleft>${data}</td></tr>\n";
				break;
			case 'messages':
				if (array_key_exists ('args', $item))
					$data = $item['func'] ($item['args']);
				else
					$data = $item['func'] ();
				foreach ($data as $msg)
					echo "<tr class='msg_${msg['class']}'><td class=tdright>${msg['header']}:</td><td class=tdleft>${msg['text']}</td></tr>\n";
				break;
			case 'meters':
				if (array_key_exists ('args', $item))
					$data = $item['func'] ($item['args']);
				else
					$data = $item['func'] ();
				foreach ($data as $meter)
				{
					echo "<tr><td class=tdright>${meter['title']}:</td><td class=tdcenter>";
					renderProgressBar ($meter['max'] ? $meter['current'] / $meter['max'] : 0);
					echo '<br><small>' . ($meter['max'] ? $meter['current'] . '/' . $meter['max'] : '0') . '</small></td></tr>';
				}
				break;
			case 'custom':
				echo "<tr><td colspan=2>";
				$item['func'] ();
				echo "</td></tr>\n";
				break;
			default:
				throw new InvalidArgException ('type', $item['type']);
		}
		echo "<tr><td colspan=2><hr></td></tr>\n";
	}
	echo "</table>\n";
}

function renderTagStats ()
{
	global $taglist;
	echo '<table border=1><tr><th>tag</th><th>total</th><th>objects</th><th>IPv4 nets</th><th>IPv6 nets</th>';
	echo '<th>racks</th><th>IPv4 VS</th><th>IPv4 RS pools</th><th>users</th><th>files</th></tr>';
	$pagebyrealm = array
	(
		'file' => 'files&tab=default',
		'ipv4net' => 'ipv4space&tab=default',
		'ipv6net' => 'ipv6space&tab=default',
		'ipv4vs' => 'ipv4slb&tab=default',
		'ipv4rspool' => 'ipv4slb&tab=rspools',
		'object' => 'depot&tab=default',
		'rack' => 'rackspace&tab=default',
		'user' => 'userlist&tab=default'
	);
	foreach (getTagChart (getConfigVar ('TAGS_TOPLIST_SIZE')) as $taginfo)
	{
		echo "<tr><td>${taginfo['tag']}</td><td>" . $taginfo['refcnt']['total'] . "</td>";
		foreach (array ('object', 'ipv4net', 'ipv6net', 'rack', 'ipv4vs', 'ipv4rspool', 'user', 'file') as $realm)
		{
			echo '<td>';
			if (!isset ($taginfo['refcnt'][$realm]))
				echo '&nbsp;';
			else
			{
				echo "<a href='index.php?page=" . $pagebyrealm[$realm] . "&cft[]=${taginfo['id']}'>";
				echo $taginfo['refcnt'][$realm] . '</a>';
			}
			echo '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
}

?>