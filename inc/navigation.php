<?php
/*
*
*  This file implements generic navigation for RackTables.
*
*/

$page = array();
$tab = array();
$trigger = array();
$ophandler = array();
$tabhandler = array();

$page['index']['title'] = 'Main page';
$page['index']['handler'] = 'renderIndex';

$page['rackspace']['title'] = 'Rackspace';
$page['rackspace']['parent'] = 'index';
$tab['rackspace']['default'] = 'Browse';
$tab['rackspace']['history'] = 'History';
$tab['rackspace']['firstrow'] = 'Click me!';
$trigger['rackspace']['firstrow'] = 'trigger_emptyRackspace';
$tabhandler['rackspace']['default'] = 'renderRackspace';
$tabhandler['rackspace']['history'] = 'renderRackspaceHistory';
$tabhandler['rackspace']['firstrow'] = 'renderFirstRowForm';
$tabextraclass['rackspace']['firstrow'] = 'attn';

$page['objects']['title'] = 'Objects';
$page['objects']['parent'] = 'index';
$tab['objects']['default'] = 'View';
$tab['objects']['addmore'] = 'Add more';
$tabhandler['objects']['default'] = 'renderObjectSpace';
$tabhandler['objects']['addmore'] = 'renderAddMultipleObjectsForm';

$page['row']['title_handler'] = 'dynamic_title_row';
$page['row']['bypass'] = 'row_id';
$page['row']['bypass_type'] = 'uint';
$page['row']['parent'] = 'rackspace';
$tab['row']['default'] = 'View';
$tab['row']['newrack'] = 'Add new rack';
$tab['row']['tagroller'] = 'Tag roller';
$tabhandler['row']['default'] = 'renderRow';
$tabhandler['row']['newrack'] = 'renderNewRackForm';
$tabhandler['row']['tagroller'] = 'renderTagRollerForRow';
$ophandler['row']['tagroller']['rollTags'] = 'rollTags';

$page['rack']['title_handler'] = 'dynamic_title_rack';
$page['rack']['bypass'] = 'rack_id';
$page['rack']['bypass_type'] = 'uint';
$page['rack']['parent'] = 'row';
$page['rack']['tagloader'] = 'loadRackTags';
$page['rack']['autotagloader'] = 'loadRackAutoTags';
$tab['rack']['default'] = 'View';
$tab['rack']['edit'] = 'Properties';
$tab['rack']['design'] = 'Design';
$tab['rack']['problems'] = 'Problems';
$tab['rack']['tags'] = 'Tags';
$tabhandler['rack']['default'] = 'renderRackPage';
$tabhandler['rack']['edit'] = 'renderEditRackForm';
$tabhandler['rack']['design'] = 'renderRackDesign';
$tabhandler['rack']['problems'] = 'renderRackProblems';
$tabhandler['rack']['tags'] = 'renderRackTags';
$trigger['rack']['tags'] = 'trigger_tags';
$ophandler['rack']['tags']['saveTags'] = 'saveRackTags';

$page['objgroup']['title_handler'] = 'dynamic_title_objgroup';
$page['objgroup']['handler'] = 'renderObjectGroup';
$page['objgroup']['bypass'] = 'group_id';
$page['objgroup']['bypass_type'] = 'uint0';
$page['objgroup']['parent'] = 'objects';

$page['object']['title_handler'] = 'dynamic_title_object';
$page['object']['bypass'] = 'object_id';
$page['object']['bypass_type'] = 'uint';
$page['object']['parent'] = 'objgroup';
$page['object']['tagloader'] = 'loadRackObjectTags';
$page['object']['autotagloader'] = 'loadRackObjectAutoTags';
$tab['object']['default'] = 'View';
$tab['object']['edit'] = 'Properties';
$tab['object']['rackspace'] = 'Rackspace';
$tab['object']['ports'] = 'Ports';
$tab['object']['network'] = 'IPv4';
$tab['object']['portfwrd'] = 'NATv4';
$tab['object']['livevlans'] = 'Live VLANs';
$tab['object']['snmpportfinder'] = 'SNMP port finder';
$tab['object']['slb'] = '[SLB]';
$tab['object']['lvsconfig'] = 'LVS configuration';
$tab['object']['autoports'] = 'AutoPorts';
$tab['object']['tags'] = 'Tags';
$tabhandler['object']['default'] = 'renderRackObject';
$tabhandler['object']['edit'] = 'renderEditObjectForm';
$tabhandler['object']['rackspace'] = 'renderRackSpaceForObject';
$tabhandler['object']['ports'] = 'renderPortsForObject';
$tabhandler['object']['network'] = 'renderNetworkForObject';
$tabhandler['object']['portfwrd'] = 'renderNATv4ForObject';
$tabhandler['object']['livevlans'] = 'renderVLANMembership';
$tabhandler['object']['snmpportfinder'] = 'renderSNMPPortFinder';
$tabhandler['object']['lvsconfig'] = 'renderLVSConfig';
$tabhandler['object']['autoports'] = 'renderAutoPortsForm';
$tabhandler['object']['tags'] = 'renderObjectTags';
$tabhandler['object']['slb'] = 'renderObjectSLB';
$tabextraclass['object']['snmpportfinder'] = 'attn';
$tabextraclass['object']['autoports'] = 'attn';
$trigger['object']['network'] = 'trigger_ipv4';
$trigger['object']['portfwrd'] = 'trigger_natv4';
$trigger['object']['livevlans'] = 'trigger_livevlans';
$trigger['object']['snmpportfinder'] = 'trigger_snmpportfinder';
$trigger['object']['slb'] = 'trigger_natv4';
$trigger['object']['lvsconfig'] = 'trigger_lvsconfig';
$trigger['object']['autoports'] = 'trigger_autoports';
$trigger['object']['tags'] = 'trigger_tags';
$ophandler['object']['ports']['addPort'] = 'addPortForObject';
$ophandler['object']['ports']['delPort'] = 'delPortFromObject';
$ophandler['object']['ports']['editPort'] = 'editPortForObject';
$ophandler['object']['ports']['linkPort'] = 'linkPortForObject';
$ophandler['object']['ports']['unlinkPort'] = 'unlinkPortForObject';
$ophandler['object']['ports']['addMultiPorts'] = 'addMultiPorts';
$ophandler['object']['ports']['useup'] = 'useupPort';
$ophandler['object']['network']['updIPv4Allocation'] = 'updIPv4Allocation';
$ophandler['object']['network']['addIPv4Allocation'] = 'addIPv4Allocation';
$ophandler['object']['network']['delIPv4Allocation'] = 'delIPv4Allocation';
$ophandler['object']['edit']['del'] = 'resetAttrValue';
$ophandler['object']['edit']['upd'] = 'updateAttrValues';
$ophandler['object']['portfwrd']['forwardPorts'] = 'addPortForwarding';
$ophandler['object']['portfwrd']['delPortForwarding'] = 'delPortForwarding';
$ophandler['object']['portfwrd']['updPortForwarding'] = 'updPortForwarding';
$ophandler['object']['livevlans']['setPortVLAN'] = 'setPortVLAN';
$ophandler['object']['autoports']['generate'] = 'generateAutoPorts';
$ophandler['object']['tags']['saveTags'] = 'saveObjectTags';
$delayauth['object']['livevlans']['setPortVLAN'] = TRUE;

$page['ipv4space']['title'] = 'IPv4 space';
$page['ipv4space']['parent'] = 'index';
$tab['ipv4space']['default'] = 'Browse';
$tab['ipv4space']['newrange'] = 'Manage';
$tabhandler['ipv4space']['default'] = 'renderAddressspace';
$tabhandler['ipv4space']['newrange'] = 'renderAddNewRange';
$ophandler['ipv4space']['newrange']['addIPv4Prefix'] = 'addIPv4Prefix';
$ophandler['ipv4space']['newrange']['delIPv4Prefix'] = 'delIPv4Prefix';

$page['iprange']['title_handler'] = 'dynamic_title_iprange';
$page['iprange']['parent'] = 'ipv4space';
$page['iprange']['bypass'] = 'id';
$page['iprange']['bypass_type'] = 'uint';
$page['iprange']['autotagloader'] = 'loadIPv4PrefixAutoTags';
$page['iprange']['tagloader'] = 'loadIPv4PrefixTags';
$tab['iprange']['default'] = 'Browse';
$tab['iprange']['properties'] = 'Properties';
$tab['iprange']['liveptr'] = 'Live PTR';
$tab['iprange']['tags'] = 'Tags';
$tabhandler['iprange']['default'] = 'renderIPRange';
$tabhandler['iprange']['properties'] = 'renderIPRangeProperties';
$tabhandler['iprange']['liveptr'] = 'renderLivePTR';
$tabhandler['iprange']['tags'] = 'renderIPv4PrefixTags';
$trigger['iprange']['tags'] = 'trigger_tags';
$ophandler['iprange']['properties']['editRange'] = 'editRange';
$ophandler['iprange']['liveptr']['importPTRData'] = 'importPTRData';
$ophandler['iprange']['tags']['saveTags'] = 'saveIPv4PrefixTags';

$page['ipaddress']['title_handler'] = 'dynamic_title_ipaddress';
$page['ipaddress']['parent'] = 'iprange';
$page['ipaddress']['bypass'] = 'ip';
$page['ipaddress']['autotagloader'] = 'loadIPv4AddressAutoTags';
$tab['ipaddress']['default'] = 'Browse';
$tab['ipaddress']['properties'] = 'Properties';
$tab['ipaddress']['assignment'] = 'Allocation';
$tab['ipaddress']['editrslist'] = '[SLB real servers]';
$tabhandler['ipaddress']['default'] = 'renderIPAddress';
$tabhandler['ipaddress']['properties'] = 'renderIPAddressProperties';
$tabhandler['ipaddress']['assignment'] = 'renderIPAddressAssignment';
$ophandler['ipaddress']['properties']['editAddress'] = 'editAddress';
$ophandler['ipaddress']['assignment']['delIPv4Allocation'] = 'delIPv4Allocation';
$ophandler['ipaddress']['assignment']['updIPv4Allocation'] = 'updIPv4Allocation';
$ophandler['ipaddress']['assignment']['addIPv4Allocation'] = 'addIPv4Allocation';

$page['ipv4slb']['title'] = 'IPv4 SLB';
$page['ipv4slb']['parent'] = 'index';
$page['ipv4slb']['handler'] = 'renderIPv4SLB';

$page['ipv4vslist']['title'] = 'Virtual services';
$page['ipv4vslist']['parent'] = 'ipv4slb';
$tab['ipv4vslist']['default'] = 'View';
$tab['ipv4vslist']['edit'] = 'Edit';
$tabhandler['ipv4vslist']['default'] = 'renderVSList';
$tabhandler['ipv4vslist']['edit'] = 'renderVSListEditForm';
$ophandler['ipv4vslist']['edit']['add'] = 'addVService';
$ophandler['ipv4vslist']['edit']['del'] = 'deleteVService';
$ophandler['ipv4vslist']['edit']['upd'] = 'updateVService';

$page['ipv4vs']['title_handler'] = 'dynamic_title_vservice';
$page['ipv4vs']['parent'] = 'ipv4vslist';
$page['ipv4vs']['bypass'] = 'id';
$page['ipv4vs']['bypass_type'] = 'uint';
$page['ipv4vs']['tagloader'] = 'loadIPv4VSTags';
$page['ipv4vs']['autotagloader'] = 'loadIPv4VSAutoTags';
$tab['ipv4vs']['default'] = 'View';
$tab['ipv4vs']['edit'] = '[Edit]';
$tab['ipv4vs']['editlblist'] = '[Load balancers]';
$tab['ipv4vs']['tags'] = 'Tags';
$tabhandler['ipv4vs']['default'] = 'renderVirtualService';
$tabhandler['ipv4vs']['edit'] = 'renderEditVService';
$tabhandler['ipv4vs']['editlblist'] = 'renderEditLBsForVService';
$tabhandler['ipv4vs']['tags'] = 'renderIPv4VSTags';
$ophandler['ipv4vs']['tags']['saveTags'] = 'saveIPv4VSTags';
$trigger['ipv4vs']['tags'] = 'trigger_tags';

$page['ipv4rsplist']['title'] = 'RS pools';
$page['ipv4rsplist']['parent'] = 'ipv4slb';
$tab['ipv4rsplist']['default'] = 'View';
$tab['ipv4rsplist']['edit'] = 'Edit';
$tabhandler['ipv4rsplist']['default'] = 'renderRSPoolList';
$tabhandler['ipv4rsplist']['edit'] = 'editRSPools';
$ophandler['ipv4rsplist']['edit']['add'] = 'addRSPool';
$ophandler['ipv4rsplist']['edit']['del'] = 'deleteRSPool';
$ophandler['ipv4rsplist']['edit']['upd'] = 'updateRSPool';

$page['rspool']['title_handler'] = 'dynamic_title_rspool';
$page['rspool']['parent'] = 'ipv4rsplist';
$page['rspool']['bypass'] = 'pool_id';
$page['rspool']['bypass_type'] = 'uint';
$page['rspool']['tagloader'] = 'loadIPv4RSPoolTags';
$page['rspool']['autotagloader'] = 'loadIPv4RSPoolAutoTags';
$tab['rspool']['default'] = 'View';
$tab['rspool']['edit'] = '[Edit]';
$tab['rspool']['editlblist'] = 'Load Balancers';
$tab['rspool']['editrslist'] = 'RS list';
$tab['rspool']['rsinservice'] = 'RS in service';
$tab['rspool']['tags'] = 'Tags';
$trigger['rspool']['rsinservice'] = 'trigger_poolrscount';
$trigger['rspool']['tags'] = 'trigger_tags';
$tabhandler['rspool']['default'] = 'renderRSPool';
$tabhandler['rspool']['edit'] = 'renderEditRSPool';
$tabhandler['rspool']['editrslist'] = 'renderRSPoolServerForm';
$tabhandler['rspool']['editlblist'] = 'renderRSPoolLBForm';
$tabhandler['rspool']['rsinservice'] = 'renderRSPoolRSInServiceForm';
$tabhandler['rspool']['tags'] = 'renderIPv4RSPoolTags';
$ophandler['rspool']['editrslist']['addRS'] = 'addRealServer';
$ophandler['rspool']['editrslist']['delRS'] = 'deleteRealServer';
$ophandler['rspool']['editrslist']['updRS'] = 'updateRealServer';
$ophandler['rspool']['editrslist']['addMany'] = 'addRealServers';
$ophandler['rspool']['editlblist']['addLB'] = 'addLoadBalancer';
$ophandler['rspool']['editlblist']['delLB'] = 'deleteLoadBalancer';
$ophandler['rspool']['editlblist']['updLB'] = 'updateLoadBalancer';
$ophandler['rspool']['rsinservice']['upd'] = 'updateRSInService';
$ophandler['rspool']['tags']['saveTags'] = 'saveIPv4RSPoolTags';

$page['rservers']['title'] = 'Real servers';
$page['rservers']['parent'] = 'ipv4slb';
$page['rservers']['handler'] = 'renderRealServerList';

$page['lbs']['title'] = 'Load balancers';
$page['lbs']['parent'] = 'ipv4slb';
$page['lbs']['handler'] = 'renderLBList';

$page['search']['title_handler'] = 'dynamic_title_search';
$page['search']['handler'] = 'renderSearchResults';
$page['search']['parent'] = 'index';
$page['search']['bypass'] = 'q';

$page['config']['title'] = 'Configuration';
$page['config']['handler'] = 'renderConfigMainpage';
$page['config']['parent'] = 'index';

$page['userlist']['title'] = 'Users';
$page['userlist']['parent'] = 'config';
$tab['userlist']['default'] = 'View';
$tab['userlist']['edit'] = 'Edit';
$tabhandler['userlist']['default'] = 'renderUserList';
$tabhandler['userlist']['edit'] = 'renderUserListEditor';
$ophandler['userlist']['edit']['updateUser'] = 'updateUser';
$ophandler['userlist']['edit']['createUser'] = 'createUser';
$ophandler['userlist']['edit']['disableUser'] = 'disableUser';
$ophandler['userlist']['edit']['enableUser'] = 'enableUser';

$page['user']['title_handler'] = 'dynamic_title_user';
$page['user']['parent'] = 'userlist';
$page['user']['bypass'] = 'user_id';
$page['user']['bypass_type'] = 'uint';
$page['user']['tagloader'] = 'loadUserTags';
$page['user']['autotagloader'] = 'getUserAutoTags';
$tab['user']['default'] = 'View';
$tab['user']['password'] = 'Change password';
$tab['user']['tags'] = 'Tags';
$tabhandler['user']['default'] = 'renderUser';
$tabhandler['user']['password'] = 'renderUserPasswordEditor';
$tabhandler['user']['tags'] = 'renderUserTags';
$ophandler['user']['tags']['saveTags'] = 'saveUserTags';
$ophandler['user']['password']['changePassword'] = 'changePassword';

$page['perms']['title'] = 'Permissions';
$page['perms']['parent'] = 'config';
$tab['perms']['default'] = 'View';
$tab['perms']['edit'] = 'Edit';
$tabhandler['perms']['default'] = 'renderRackCodeViewer';
$tabhandler['perms']['edit'] = 'renderRackCodeEditor';
$ophandler['perms']['edit']['saveRackCode'] = 'saveRackCode';

$page['portmap']['title'] = 'Port compatibility map';
$page['portmap']['parent'] = 'config';
$tab['portmap']['default'] = 'View';
$tab['portmap']['edit'] = 'Change';
$tabhandler['portmap']['default'] = 'renderPortMapViewer';
$tabhandler['portmap']['edit'] = 'renderPortMapEditor';
$ophandler['portmap']['edit']['save'] = 'savePortMap';

$page['attrs']['title'] = 'Attributes';
$page['attrs']['parent'] = 'config';
$tab['attrs']['default'] = 'View';
$tab['attrs']['editattrs'] = 'Edit attributes';
$tab['attrs']['editmap'] = 'Edit map';
$tabhandler['attrs']['default'] = 'renderAttributes';
$tabhandler['attrs']['editattrs'] = 'renderEditAttributesForm';
$tabhandler['attrs']['editmap'] = 'renderEditAttrMapForm';
$ophandler['attrs']['editattrs']['add'] = 'createAttribute';
$ophandler['attrs']['editattrs']['upd'] = 'changeAttribute';
$ophandler['attrs']['editattrs']['del'] = 'deleteAttribute';
$ophandler['attrs']['editmap']['add'] = 'supplementAttrMap';
$ophandler['attrs']['editmap']['del'] = 'reduceAttrMap';

$page['dict']['title'] = 'Dictionary';
$page['dict']['parent'] = 'config';
$tab['dict']['default'] = 'View';
$tab['dict']['edit'] = 'Edit words';
$tab['dict']['chapters'] = 'Manage chapters';
$tabhandler['dict']['default'] = 'renderDictionary';
$tabhandler['dict']['edit'] = 'renderDictionaryEditor';
$tabhandler['dict']['chapters'] = 'renderChaptersEditor';
$ophandler['dict']['edit']['del'] = 'reduceDictionary';
$ophandler['dict']['edit']['upd'] = 'updateDictionary';
$ophandler['dict']['edit']['add'] = 'supplementDictionary';
$ophandler['dict']['chapters']['del'] = 'delChapter';
$ophandler['dict']['chapters']['upd'] = 'updateChapter';
$ophandler['dict']['chapters']['add'] = 'addChapter';

$page['ui']['title'] = 'User interface';
$page['ui']['parent'] = 'config';
$tab['ui']['default'] = 'View';
$tab['ui']['edit'] = 'Change';
$tab['ui']['reset'] = 'Reset';
$tabhandler['ui']['default'] = 'renderUIConfig';
$tabhandler['ui']['edit'] = 'renderUIConfigEditForm';
$tabhandler['ui']['reset'] = 'renderUIResetForm';
$ophandler['ui']['edit']['upd'] = 'updateUI';
$ophandler['ui']['reset']['go'] = 'resetUIConfig';

$page['tagtree']['title'] = 'Tag tree';
$page['tagtree']['parent'] = 'config';
$tab['tagtree']['default'] = 'View';
$tab['tagtree']['edit'] = 'Edit';
$tabhandler['tagtree']['default'] = 'renderTagTree';
$tabhandler['tagtree']['edit'] = 'renderTagTreeEditor';
$ophandler['tagtree']['edit']['destroyTag'] = 'destroyTag';
$ophandler['tagtree']['edit']['createTag'] = 'createTag';
$ophandler['tagtree']['edit']['updateTag'] = 'updateTag';

$page['reports']['title'] = 'Reports';
$page['reports']['parent'] = 'index';
$page['reports']['handler'] = 'renderReportSummary';

// This function returns array if page numbers leading to the target page
// plus page number of target page itself. The first element is the target
// page number and the last element is the index page number.
function getPath ($targetno)
{
	global $page;
	$path = array();
	// Recursion breaks at first parentless page.
	if (!isset ($page[$targetno]['parent']))
		$path = array ($targetno);
	else
	{
		$path = getPath ($page[$targetno]['parent']);
		$path[] = $targetno;
	}
	return $path;
}

function showPathAndSearch ($pageno)
{
	global $root, $page;
	// Path.
	echo "<td class=activemenuitem width='99%'>" . getConfigVar ('enterprise');
	$path = getPath ($pageno);
	foreach ($path as $no)
	{
		$title['params'] = array();
		if (isset ($page[$no]['title']))
			$title['name'] = $page[$no]['title'];
		elseif (isset ($page[$no]['title_handler']))
			$title = $page[$no]['title_handler']($no);
		else
			$title['name'] = '[N/A]';
		echo ": <a href='${root}?page=${no}";
		foreach ($title['params'] as $param_name => $param_value)
			echo "&${param_name}=${param_value}";
		echo "'>" . $title['name'] . "</a>";
	}
	echo "</td>";
	// Search form.
	echo "<td><table border=0 cellpadding=0 cellspacing=0><tr><td>Search:</td>";
	echo "<form name=search method=get action='${root}'><td>";
	echo '<input type=hidden name=page value=search>';
	// This input will be the first, if we don't add ports or addresses.
	echo "<input type=text name=q size=20 tabindex=1000></td></form></tr></table></td>";
}

function getTitle ($pageno, $tabno)
{
	global $page;
	if (isset ($page[$pageno]['title']))
		return $page[$pageno]['title'];
	elseif (isset ($page[$pageno]['title_handler']))
	{
		$tmp = $page[$pageno]['title_handler']($pageno);
		return $tmp['name'];
	}
	else
		return getConfigVar ('enterprise');
}

function showTabs ($pageno, $tabno)
{
	global $tab, $root, $page, $remote_username, $trigger, $tabextraclass;
	if (!isset ($tab[$pageno]['default']))
		return;
	echo "<td><div class=greynavbar><ul id=foldertab style='margin-bottom: 0px; padding-top: 10px;'>";
	foreach ($tab[$pageno] as $tabidx => $tabtitle)
	{
		// Hide forbidden tabs.
		if (!permitted ($pageno, $tabidx))
			continue;
		// Dynamic tabs should only be shown in certain cases (trigger exists and returns true).
		if (isset ($trigger[$pageno][$tabidx]))
		{
//			$t1 = microtime (TRUE);
			$ok = $trigger[$pageno][$tabidx] ();
//			$t2 = microtime (TRUE);
//			echo 'DEBUG: ' . $trigger[$pageno][$tabidx] . ': ' . sprintf ('%0.4f', $t2 - $t1) . '<br>';
			if (!$ok)
				continue;
		}
		$class = ($tabidx == $tabno) ? 'current' : 'std';
		$extra = (isset ($tabextraclass[$pageno][$tabidx])) ? $tabextraclass[$pageno][$tabidx] : '';
		echo "<li><a class=${class}{$extra}";
		echo " href='${root}?page=${pageno}&tab=${tabidx}";
		if (isset ($page[$pageno]['bypass']) and isset ($_REQUEST[$page[$pageno]['bypass']]))
		{
			$bpname = $page[$pageno]['bypass'];
			$bpval = $_REQUEST[$bpname];
			echo "&${bpname}=${bpval}";
		}
		echo "'>${tabtitle}</a></li>\n";
	}
	echo "</ul></div></td>\n";
}

// This function returns pages, which are direct children of the requested
// page and are accessible by the current user.
function getDirectChildPages ($pageno)
{
	global $page, $remote_username;
	$children = array();
	foreach ($page as $cpageno => $cpage)
		if
		(
			isset ($cpage['parent']) and
			$cpage['parent'] == $pageno and
			accessibleSubpage ($cpageno) == TRUE
		)
			$children[$cpageno] = $cpage;
	return $children;
}

function getAllChildPages ($parent)
{
	global $page;
	// Array pointer is global, so if we don't create local copies of
	// the global array, we can't advance any more after nested call
	// of getAllChildPages returns.
	$mypage = $page;
	$mykids = array();
	foreach ($mypage as $ctitle => $cpage)
		if (isset ($cpage['parent']) and $cpage['parent'] == $parent)
			$mykids[] = array ('title' => $ctitle, 'kids' => getAllChildPages ($ctitle));
	return $mykids;
}

?>
