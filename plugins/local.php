<?php
$image['logo']['path'] = 'pix/redstone_managed_solutions.gif';
$image['logo']['width'] = 176;
$image['logo']['height'] = 60;
addCSS('css/redstone.css');
if($_POST)
{
#$_REQUEST['taglist'][] = 20;
	#$_POST['0_object_name'] = 'JT'. $_POST['0_object_name'];
	#print_r($_POST);
	#print_r($_GET);
#	exit;
}
if (in_array(array('tag'=>'$username_minesh.patel'),$auto_tags) or in_array(array('tag'=>'$username_RMSITTeam'),$auto_tags)) {
  $_REQUEST['cft'][] = 20;
} 

function renderIPv4SpaceRecords_RMS ($tree, $baseurl, $target = 0, $knight, $level = 1)
{
	$self = __FUNCTION__;
	static $vdomlist = NULL;
	if ($vdomlist == NULL and getConfigVar ('IPV4_TREE_SHOW_VLAN') == 'yes')
		$vdomlist = getVLANDomainOptions();

	// scroll page to the highlighted item
	if ($target && isset ($_REQUEST['hl_net']))
		addAutoScrollScript ("net-$target");

	foreach ($tree as $item)
	{
		if (getConfigVar ('IPV4_TREE_SHOW_USAGE') == 'yes')
			loadIPv4AddrList ($item); // necessary to compute router list and address counter
		else
		{
			$item['addrlist'] = array();
			$item['addrc'] = 0;
		}
		$used = $item['addrc'];
		$maxdirect = $item['addrt'];
		$maxtotal = binInvMaskFromDec ($item['mask']) + 1;
		if (isset ($item['id']))
		{
			$decor = array ('indent' => $level);
			if ($item['symbol'] == 'node-collapsed')
				$decor['symbolurl'] = "${baseurl}&eid=" . $item['id'];
			elseif ($item['symbol'] == 'node-expanded')
				$decor['symbolurl'] = $baseurl . ($item['parent_id'] ? "&eid=${item['parent_id']}" : '');
			echo "<tr valign=top class='ip_net_Live'>";
			if ($target == $item['id'] && isset ($_REQUEST['hl_net']))
				$decor['tdclass'] .= ' port_highlight';
			
			printIPNetInfoTDs_RMS($item, $decor);
			echo "<td class=tdcenter>";
			if (getConfigVar ('IPV4_TREE_SHOW_USAGE') == 'yes')
			{
				renderProgressBar ($maxdirect ? $used/$maxdirect : 0);
				echo "<br><small>${used}/${maxdirect}" . ($maxdirect == $maxtotal ? '' : "/${maxtotal}") . '</small>';
			}
			else
				echo "<small>${maxdirect}</small>";
			echo "</td>";
			if (getConfigVar ('IPV4_TREE_SHOW_VLAN') == 'yes')
			{
				echo '<td class=tdleft>';
				if (count ($item['8021q']))
				{
					echo '<ul>';
					foreach ($item['8021q'] as $binding)
					{
						echo '<li><a href="' . makeHref (array ('page' => 'vlan', 'vlan_ck' => $binding['domain_id'] . '-' . $binding['vlan_id'])) . '">';
						// FIXME: would formatVLANName() do this?
						echo $binding['vlan_id'] . '@' . niftyString ($vdomlist[$binding['domain_id']], 15) . '</a></li>';
					}
					echo '</ul>';
				}
				echo '</td>';
			}
			if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
				printRoutersTD (findRouters ($item['addrlist']), getConfigVar ('IPV4_TREE_RTR_AS_CELL'));
			echo "</tr>";
			if ($item['symbol'] == 'node-expanded' or $item['symbol'] == 'node-expanded-static')
				$self ($item['kids'], $baseurl, $target, $knight, $level + 1);
		}
		else
		{
			echo "<tr valign=top>";
			printIPNetInfoTDs_RMS($item, array ('indent' => $level, 'knight' => $knight, 'tdclass' => 'sparenetwork'));
			echo "<td class=tdcenter>";
			if (getConfigVar ('IPV4_TREE_SHOW_USAGE') == 'yes')
			{
				renderProgressBar ($used/$maxtotal, 'sparenetwork');
				echo "<br><small>${used}/${maxtotal}</small>";
			}
			else
				echo "<small>${maxtotal}</small>";
			if (getConfigVar ('IPV4_TREE_SHOW_VLAN') == 'yes')
				echo '</td><td>&nbsp;</td>';
			echo "</td><td>&nbsp;</td></tr>";
		}
	}
}



// Same as for routers, but produce two TD cells to lay the content out better.
function printIPNetInfoTDs_RMS ($netinfo, $decor = array())
{
        ########################################################################################
        #########   RMS Modification to support CSS stlying in IP tree view
        #########   James Tutton 01/04/2011
	#########   Add to top of printIPNetInfoTDs
        ########################################################################################

        foreach($netinfo['etags'] as $tag)
        {
                $decor['tdclass'] .= ' ip_net_' . str_replace(' ','_',$tag['tag']);
        } 

        foreach($netinfo['itags'] as $tag)
        {
                $decor['tdclass'] .= ' ip_net_' . str_replace(' ','_',$tag['tag']);
        }


        ########################################################################################
        ########     End Code
        ########################################################################################

	$ip_ver = is_a ($netinfo['ip_bin'], 'IPv6Address') ? 6 : 4;
	$formatted = $netinfo['ip'] . "/" . $netinfo['mask'];
	if ($netinfo['symbol'] == 'spacer')
	{
		$decor['indent']++;
		$netinfo['symbol'] = '';
	}
	echo '<td class="tdleft';
	if (array_key_exists ('tdclass', $decor))
		echo ' ' . $decor['tdclass'];
	echo '" style="padding-left: ' . ($decor['indent'] * 16) . 'px;">';
	if (strlen ($netinfo['symbol']))
	{
		if (array_key_exists ('symbolurl', $decor))
			echo "<a href='${decor['symbolurl']}'>";
		printImageHREF ($netinfo['symbol']);
		if (array_key_exists ('symbolurl', $decor))
			echo '</a>';
	}
	if (isset ($netinfo['id']))
		echo "<a name='net-${netinfo['id']}' href='index.php?page=ipv${ip_ver}net&id=${netinfo['id']}'>";
	echo $formatted;
	if (isset ($netinfo['id']))
		echo '</a>';
	echo '</td><td class="tdleft';
	if (array_key_exists ('tdclass', $decor))
		echo ' ' . $decor['tdclass'];
	echo '">';
	if (!isset ($netinfo['id']))
	{
		printImageHREF ('dragons', 'Here be dragons.');
		if ($decor['knight'])
		{
			echo '<a href="' . makeHref (array
			(
				'page' => "ipv${ip_ver}space",
				'tab' => 'newrange',
				'set-prefix' => $formatted,
			)) . '">';
			printImageHREF ('knight', 'create network here');
			echo '</a>';
		}
	}
	else
	{
		echo niftyString ($netinfo['name']);
		if (count ($netinfo['etags']))
			echo '<br><small>' . serializeTags ($netinfo['etags'], "index.php?page=ipv${ip_ver}space&tab=default&") . '</small>';
	}
	echo "</td>";
}

?>
