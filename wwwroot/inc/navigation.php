<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*

This file contains a series of arrays, which define RackTables as a tree
of discrete views ("pages"). Each such page may be in turn split info a set of
"tabs". In the latter case it is also possible to define a set of "operations"
for a particular combination of page and tab (location). These operations
represent actions requested by the user and often expect additional data
provided to be executed correctly (constraints on the additional data are
defined and validated in ophandlers.php).

Every page split into tabs must define a tab internally named "default". It is
assumed, that this default tab never defines any operations and thus remains
read-only.

*/

$page = array();
$tab = array();
$trigger = array();

$ophandler = array();
$tabhandler = array();
$hook = array();
$ophandlers_stack = array();
$tabhandlers_stack = array();
$hooks_stack = array();

$delayauth = array();
$svghandler = array();
$ajaxhandler = array();

$indexlayout = array
(
	array ('rackspace', 'depot', 'ipv4space', 'ipv6space'),
	array ('files', 'reports', 'ipv4slb', '8021q'),
	array ('config', 'objectlog', 'virtual'),
);

$page['index']['title'] = 'Main page';
$page['index']['handler'] = 'renderIndex';

$page['rackspace']['title'] = 'Rackspace';
$page['rackspace']['parent'] = 'index';
$tab['rackspace']['default'] = 'Browse';
$tab['rackspace']['editlocations'] = 'Manage locations';
$tab['rackspace']['editrows'] = 'Manage rows';
$tab['rackspace']['history'] = 'History';
$tabhandler['rackspace']['default'] = 'renderRackspace';
$tabhandler['rackspace']['editlocations'] = 'renderRackspaceLocationEditor';
$tabhandler['rackspace']['editrows'] = 'renderRackspaceRowEditor';
$tabhandler['rackspace']['history'] = 'renderRackspaceHistory';
$ophandler['rackspace']['editlocations']['addLocation'] = 'addLocation';
$ophandler['rackspace']['editlocations']['updateLocation'] = 'updateLocation';
$ophandler['rackspace']['editlocations']['deleteLocation'] = 'deleteLocation';
$ophandler['rackspace']['editrows']['addRow'] = 'addRow';
$ophandler['rackspace']['editrows']['updateRow'] = 'updateRow';
$ophandler['rackspace']['editrows']['deleteRow'] = 'deleteRow';

$page['depot']['parent'] = 'index';
$page['depot']['title'] = 'Objects';
$tab['depot']['default'] = 'Browse';
$tab['depot']['addmore'] = 'Add more';
$tabhandler['depot']['default'] = 'renderDepot';
$tabhandler['depot']['addmore'] = 'renderAddMultipleObjectsForm';
$ophandler['depot']['addmore']['addObjects'] = 'addMultipleObjects';
$ophandler['depot']['addmore']['addLotOfObjects'] = 'addLotOfObjects';
$ophandler['depot']['addmore']['deleteObject'] = 'deleteObject';

$page['location']['bypass'] = 'location_id';
$page['location']['bypass_type'] = 'uint';
$page['location']['parent'] = 'rackspace';
$tab['location']['default'] = 'View';
$tab['location']['edit'] = 'Properties';
$tab['location']['log'] = 'Log';
$tab['location']['tags'] = 'Tags';
$tab['location']['files'] = 'Files';
$tabhandler['location']['default'] = 'renderLocationPage';
$tabhandler['location']['edit'] = 'renderEditLocationForm';
$tabhandler['location']['log'] = 'renderObjectLogEditor';
$tabhandler['location']['tags'] = 'renderEntityTags';
$tabhandler['location']['files'] = 'renderFilesForEntity';
$trigger['location']['tags'] = 'trigger_tags';
$ophandler['location']['edit']['clearSticker'] = 'clearSticker';
$ophandler['location']['edit']['updateLocation'] = 'updateLocation';
$ophandler['location']['edit']['deleteLocation'] = 'deleteLocation';
$ophandler['location']['log']['add'] = 'addObjectlog';
$ophandler['location']['log']['del'] = 'tableHandler';
$ophandler['location']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['location']['files']['addFile'] = 'addFileToEntity';
$ophandler['location']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['location']['files']['unlinkFile'] = 'unlinkFile';

$page['row']['bypass'] = 'row_id';
$page['row']['bypass_type'] = 'uint';
$page['row']['parent'] = 'rackspace';
$tab['row']['default'] = 'View';
$tab['row']['editracks'] = 'Manage racks';
$tab['row']['newrack'] = 'Add new rack';
$tab['row']['tagroller'] = 'Tag roller';
$tabhandler['row']['default'] = 'renderRow';
$tabhandler['row']['editracks'] = 'renderRackSortForm';
$tabhandler['row']['newrack'] = 'renderNewRackForm';
$tabhandler['row']['tagroller'] = 'renderTagRollerForRow';
$ophandler['row']['tagroller']['rollTags'] = 'rollTags';
$ophandler['row']['newrack']['addRack'] = 'addRack';

$page['rack']['bypass'] = 'rack_id';
$page['rack']['bypass_type'] = 'uint';
$page['rack']['parent'] = 'row';
$tab['rack']['default'] = 'View';
$tab['rack']['edit'] = 'Properties';
$tab['rack']['log'] = 'Log';
$tab['rack']['design'] = 'Design';
$tab['rack']['problems'] = 'Problems';
$tab['rack']['tags'] = 'Tags';
$tab['rack']['files'] = 'Files';
$tabhandler['rack']['default'] = 'renderRackPage';
$tabhandler['rack']['edit'] = 'renderEditRackForm';
$tabhandler['rack']['log'] = 'renderObjectLogEditor';
$tabhandler['rack']['design'] = 'renderRackDesign';
$tabhandler['rack']['problems'] = 'renderRackProblems';
$tabhandler['rack']['tags'] = 'renderEntityTags';
$tabhandler['rack']['files'] = 'renderFilesForEntity';
$trigger['rack']['tags'] = 'trigger_tags';
$ophandler['rack']['design']['updateRack'] = 'updateRackDesign';
$ophandler['rack']['problems']['updateRack'] = 'updateRackProblems';
$ophandler['rack']['edit']['clearSticker'] = 'clearSticker';
$ophandler['rack']['edit']['updateRack'] = 'updateRack';
$ophandler['rack']['edit']['deleteRack'] = 'deleteRack';
$ophandler['rack']['log']['add'] = 'addObjectlog';
$ophandler['rack']['log']['del'] = 'tableHandler';
$ophandler['rack']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['rack']['files']['addFile'] = 'addFileToEntity';
$ophandler['rack']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['rack']['files']['unlinkFile'] = 'unlinkFile';

$page['object']['bypass'] = 'object_id';
$page['object']['bypass_type'] = 'uint';
$page['object']['bypass_tabs'] = array ('hl_port_id', 'hl_ip');
$page['object']['parent'] = 'depot';
$tab['object']['default'] = 'View';
$tab['object']['edit'] = 'Properties';
$tab['object']['log'] = 'Log';
$tab['object']['rackspace'] = 'Rackspace';
$tab['object']['ports'] = 'Ports';
$tab['object']['ip'] = 'IP';
$tab['object']['nat4'] = 'NATv4';
$tab['object']['liveports'] = 'Live ports';
$tab['object']['livecdp'] = 'Live CDP';
$tab['object']['livelldp'] = 'Live LLDP';
$tab['object']['snmpportfinder'] = 'SNMP sync';
$tab['object']['editrspvs'] = 'RS pools';
$tab['object']['lvsconfig'] = 'keepalived.conf';
$tab['object']['autoports'] = 'AutoPorts';
$tab['object']['tags'] = 'Tags';
$tab['object']['files'] = 'Files';
$tab['object']['ucs'] = 'UCS';
$tab['object']['8021qorder'] = '802.1Q order';
$tab['object']['8021qports'] = '802.1Q ports';
$tab['object']['8021qsync'] = '802.1Q sync';
$tab['object']['cacti'] = 'Cacti Graphs';
$tabhandler['object']['default'] = 'renderObject';
$tabhandler['object']['edit'] = 'renderEditObjectForm';
$tabhandler['object']['log'] = 'renderObjectLogEditor';
$tabhandler['object']['rackspace'] = 'renderRackSpaceForObject';
$tabhandler['object']['ports'] = 'renderPortsForObject';
$tabhandler['object']['ip'] = 'renderIPForObject';
$tabhandler['object']['nat4'] = 'renderNATv4ForObject';
$tabhandler['object']['liveports'] = 'renderPortsInfo';
$tabhandler['object']['livecdp'] = 'renderDiscoveredNeighbors';
$tabhandler['object']['livelldp'] = 'renderDiscoveredNeighbors';
$tabhandler['object']['snmpportfinder'] = 'renderSNMPPortFinder';
$tabhandler['object']['lvsconfig'] = 'renderLVSConfig';
$tabhandler['object']['autoports'] = 'renderAutoPortsForm';
$tabhandler['object']['tags'] = 'renderEntityTags';
$tabhandler['object']['files'] = 'renderFilesForEntity';
$tabhandler['object']['editrspvs'] = 'renderSLBEditTab';
$tabhandler['object']['8021qorder'] = 'render8021QOrderForm';
$tabhandler['object']['8021qports'] = 'renderObject8021QPorts';
$tabhandler['object']['8021qsync'] = 'renderObject8021QSync';
$tabhandler['object']['cacti'] = 'renderObjectCactiGraphs';
$tabhandler['object']['ucs'] = 'renderEditUCSForm';
$trigger['object']['rackspace'] = 'trigger_rackspace';
$trigger['object']['ports'] = 'trigger_ports';
$trigger['object']['ip'] = 'trigger_ip';
$trigger['object']['nat4'] = 'trigger_natv4';
$trigger['object']['liveports'] = 'trigger_liveports';
$trigger['object']['livecdp'] = 'trigger_LiveCDP';
$trigger['object']['livelldp'] = 'trigger_LiveLLDP';
$trigger['object']['snmpportfinder'] = 'trigger_snmpportfinder';
$trigger['object']['editrspvs'] = 'trigger_isloadbalancer';
$trigger['object']['lvsconfig'] = 'trigger_isloadbalancer';
$trigger['object']['autoports'] = 'trigger_autoports';
$trigger['object']['tags'] = 'trigger_tags';
$trigger['object']['8021qorder'] = 'trigger_object_8021qorder';
$trigger['object']['8021qports'] = 'trigger_object_8021qports';
$trigger['object']['8021qsync'] = 'trigger_object_8021qsync';
$trigger['object']['cacti'] = 'triggerCactiGraphs';
$trigger['object']['ucs'] = 'trigger_ucs';
$ophandler['object']['edit']['linkEntities'] = 'tableHandler';
$ophandler['object']['edit']['unlinkEntities'] = 'tableHandler';
$ophandler['object']['rackspace']['updateObjectAllocation'] = 'updateObjectAllocation';
$ophandler['object']['ports']['addPort'] = 'addPortForObject';
$ophandler['object']['ports']['editPort'] = 'editPortForObject';
$ophandler['object']['ports']['addMultiPorts'] = 'addMultiPorts';
$ophandler['object']['ports']['addBulkPorts'] = 'addBulkPorts';
$ophandler['object']['ports']['useup'] = 'tableHandler';
$ophandler['object']['ports']['delPort'] = 'tableHandler';
$ophandler['object']['ports']['deleteAll'] = 'tableHandler';
$ophandler['object']['ports']['unlinkPort'] = 'unlinkPort';
$ophandler['object']['ip']['upd'] = 'updIPAllocation';
$ophandler['object']['ip']['add'] = 'addIPAllocation';
$ophandler['object']['ip']['del'] = 'delIPAllocation';
$ophandler['object']['edit']['clearSticker'] = 'clearSticker';
$ophandler['object']['edit']['update'] = 'updateObject';
$ophandler['object']['edit']['resetObject'] = 'resetObject';
$ophandler['object']['log']['add'] = 'addObjectlog';
$ophandler['object']['log']['del'] = 'tableHandler';
$ophandler['object']['nat4']['addNATv4Rule'] = 'addPortForwarding';
$ophandler['object']['nat4']['delNATv4Rule'] = 'delPortForwarding';
$ophandler['object']['nat4']['updNATv4Rule'] = 'updPortForwarding';
$ophandler['object']['livecdp']['importDPData'] = 'importDPData';
$ophandler['object']['livelldp']['importDPData'] = 'importDPData';
$ophandler['object']['autoports']['generate'] = 'generateAutoPorts';
$ophandler['object']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['object']['files']['addFile'] = 'addFileToEntity';
$ophandler['object']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['object']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['object']['editrspvs']['addLB'] = 'addLoadBalancer';
$ophandler['object']['editrspvs']['delLB'] = 'tableHandler';
$ophandler['object']['editrspvs']['updLB'] = 'tableHandler';
$ophandler['object']['lvsconfig']['submitSLBConfig'] = 'submitSLBConfig';
$ophandler['object']['snmpportfinder']['querySNMPData'] = 'querySNMPData';
$ophandler['object']['8021qorder']['add'] = 'add8021QOrder';
$ophandler['object']['8021qorder']['del'] = 'del8021QOrder';
$ophandler['object']['8021qports']['save8021QConfig'] = 'save8021QPorts';
$ophandler['object']['8021qports']['exec8021QRecalc'] = 'process8021QRecalcRequest';
$ophandler['object']['8021qsync']['exec8021QPull'] = 'process8021QSyncRequest';
$ophandler['object']['8021qsync']['exec8021QPush'] = 'process8021QSyncRequest';
$ophandler['object']['8021qsync']['resolve8021QConflicts'] = 'resolve8021QConflicts';
$ophandler['object']['8021qsync']['addPort'] = 'create8021QPortConfig';
$ophandler['object']['8021qsync']['delPort'] = 'destroy8021QPortConfig';
$ophandler['object']['cacti']['add'] = 'tableHandler';
$ophandler['object']['cacti']['del'] = 'tableHandler';
$ophandler['object']['ucs']['autoPopulateUCS'] = 'autoPopulateUCS';
$ophandler['object']['ucs']['cleanupUCS'] = 'cleanupUCS';
$delayauth['object-8021qports-save8021QConfig'] = TRUE;
$delayauth['object-8021qorder-add'] = TRUE;
$delayauth['object-8021qorder-del'] = TRUE;

$page['ipv4space']['parent'] = 'index';
$tab['ipv4space']['default'] = 'Browse';
$tab['ipv4space']['newrange'] = 'Add';
$tab['ipv4space']['manage'] = 'Delete';
$tabhandler['ipv4space']['default'] = 'renderIPSpace';
$tabhandler['ipv4space']['newrange'] = 'renderIPNewNetForm';
$tabhandler['ipv4space']['manage'] = 'renderIPSpaceEditor';
$ophandler['ipv4space']['newrange']['add'] = 'addIPv4Prefix';
$ophandler['ipv4space']['manage']['del'] = 'delIPv4Prefix';

$page['ipv6space']['parent'] = 'index';
$tab['ipv6space']['default'] = 'Browse';
$tab['ipv6space']['newrange'] = 'Add';
$tab['ipv6space']['manage'] = 'Delete';
$tabhandler['ipv6space']['default'] = 'renderIPSpace';
$tabhandler['ipv6space']['newrange'] = 'renderIPNewNetForm';
$tabhandler['ipv6space']['manage'] = 'renderIPSpaceEditor';
$ophandler['ipv6space']['newrange']['add'] = 'addIPv6Prefix';
$ophandler['ipv6space']['manage']['del'] = 'delIPv6Prefix';

$page['ipv4net']['parent'] = 'ipv4space';
$page['ipv4net']['bypass'] = 'id';
$page['ipv4net']['bypass_type'] = 'uint';
$page['ipv4net']['bypass_tabs'] = array ('pg');
$tab['ipv4net']['default'] = 'Browse';
$tab['ipv4net']['properties'] = 'Properties';
$tab['ipv4net']['liveptr'] = 'Live PTR';
$tab['ipv4net']['tags'] = 'Tags';
$tab['ipv4net']['files'] = 'Files';
$tab['ipv4net']['8021q'] = '802.1Q';
$tabhandler['ipv4net']['default'] = 'renderIPNetwork';
$tabhandler['ipv4net']['properties'] = 'renderIPNetworkProperties';
$tabhandler['ipv4net']['liveptr'] = 'renderLivePTR';
$tabhandler['ipv4net']['tags'] = 'renderEntityTags';
$tabhandler['ipv4net']['files'] = 'renderFilesForEntity';
$tabhandler['ipv4net']['8021q'] = 'renderVLANIPLinks';
$trigger['ipv4net']['tags'] = 'trigger_tags';
$trigger['ipv4net']['8021q'] = 'trigger_ipv4net_vlanconfig';
$ophandler['ipv4net']['properties']['editRange'] = 'tableHandler';
$ophandler['ipv4net']['properties']['del'] = 'delIPv4Prefix';
$ophandler['ipv4net']['liveptr']['importPTRData'] = 'importPTRData';
$ophandler['ipv4net']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['ipv4net']['files']['addFile'] = 'addFileToEntity';
$ophandler['ipv4net']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['ipv4net']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['ipv4net']['8021q']['bind'] = 'bindVLANtoIPv4';
$ophandler['ipv4net']['8021q']['unbind'] = 'unbindVLANfromIPv4';

$page['ipv6net']['parent'] = 'ipv6space';
$page['ipv6net']['bypass'] = 'id';
$page['ipv6net']['bypass_type'] = 'uint';
$tab['ipv6net']['default'] = 'Browse';
$tab['ipv6net']['properties'] = 'Properties';
$tab['ipv6net']['tags'] = 'Tags';
$tab['ipv6net']['files'] = 'Files';
$tab['ipv6net']['8021q'] = '802.1Q';
$tabhandler['ipv6net']['default'] = 'renderIPNetwork';
$tabhandler['ipv6net']['properties'] = 'renderIPNetworkProperties';
$tabhandler['ipv6net']['tags'] = 'renderEntityTags';
$tabhandler['ipv6net']['files'] = 'renderFilesForEntity';
$tabhandler['ipv6net']['8021q'] = 'renderVLANIPLinks';
$trigger['ipv6net']['tags'] = 'trigger_tags';
$trigger['ipv6net']['8021q'] = 'trigger_ipv6net_vlanconfig';
$ophandler['ipv6net']['properties']['editRange'] = 'tableHandler';
$ophandler['ipv6net']['properties']['del'] = 'delIPv6Prefix';
$ophandler['ipv6net']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['ipv6net']['files']['addFile'] = 'addFileToEntity';
$ophandler['ipv6net']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['ipv6net']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['ipv6net']['8021q']['bind'] = 'bindVLANtoIPv6';
$ophandler['ipv6net']['8021q']['unbind'] = 'unbindVLANfromIPv6';

//$page['ipaddress']['parent'] = 'ipnet'; - this is commented intentionally, there is a special hack in getPath
$page['ipaddress']['bypass'] = 'ip';
$page['ipaddress']['bypass_type'] = 'inet';
$tab['ipaddress']['default'] = 'Browse';
$tab['ipaddress']['properties'] = 'Properties';
$tab['ipaddress']['assignment'] = 'Allocation';
$tab['ipaddress']['log'] = 'Change log';
$tabhandler['ipaddress']['default'] = 'renderIPAddress';
$tabhandler['ipaddress']['properties'] = 'renderIPAddressProperties';
$tabhandler['ipaddress']['assignment'] = 'renderIPAddressAllocations';
$tabhandler['ipaddress']['log'] = 'renderIPAddressLog';
$trigger['ipaddress']['log'] = 'triggerIPAddressLog';
$ophandler['ipaddress']['properties']['editAddress'] = 'editAddress';
$ophandler['ipaddress']['assignment']['del'] = 'delIPAllocation';
$ophandler['ipaddress']['assignment']['upd'] = 'updIPAllocation';
$ophandler['ipaddress']['assignment']['add'] = 'addIPAllocation';

$page['ipv4slb']['title'] = 'IP SLB';
$page['ipv4slb']['parent'] = 'index';
$tab['ipv4slb']['default'] = 'Virtual services';
$tab['ipv4slb']['lbs'] = 'Load balancers';
$tab['ipv4slb']['rspools'] = 'Real server pools';
$tab['ipv4slb']['rservers'] = 'Real servers';
$tab['ipv4slb']['defconfig'] = 'Default configs';
$tab['ipv4slb']['new_vs'] = 'new VS';
$tab['ipv4slb']['new_rs'] = 'new RS pool';
$tabhandler['ipv4slb']['default'] = 'renderVSList';
$tabhandler['ipv4slb']['lbs'] = 'renderLBList';
$tabhandler['ipv4slb']['rspools'] = 'renderRSPoolList';
$tabhandler['ipv4slb']['rservers'] = 'renderRealServerList';
$tabhandler['ipv4slb']['defconfig'] = 'renderSLBDefConfig';
$tabhandler['ipv4slb']['new_vs'] = 'renderNewVSForm';
$tabhandler['ipv4slb']['new_rs'] = 'renderNewRSPoolForm';
$ophandler['ipv4slb']['new_vs']['add'] = 'addVService';
$ophandler['ipv4slb']['new_rs']['add'] = 'addRSPool';
$ophandler['ipv4slb']['defconfig']['save'] = 'updateSLBDefConfig';

$page['ipv4vs']['parent'] = 'ipv4slb';
$page['ipv4vs']['bypass'] = 'vs_id';
$page['ipv4vs']['bypass_type'] = 'uint';
$tab['ipv4vs']['default'] = 'View';
$tab['ipv4vs']['edit'] = 'Edit';
$tab['ipv4vs']['editlblist'] = 'Load balancers';
$tab['ipv4vs']['tags'] = 'Tags';
$tab['ipv4vs']['files'] = 'Files';
$tabhandler['ipv4vs']['default'] = 'renderVirtualService';
$tabhandler['ipv4vs']['edit'] = 'renderEditVService';
$tabhandler['ipv4vs']['editlblist'] = 'renderSLBEditTab';
$tabhandler['ipv4vs']['tags'] = 'renderEntityTags';
$tabhandler['ipv4vs']['files'] = 'renderFilesForEntity';
$trigger['ipv4vs']['tags'] = 'trigger_tags';
$ophandler['ipv4vs']['edit']['updIPv4VS'] = 'updateVService';
$ophandler['ipv4vs']['edit']['del'] = 'deleteVService';
$ophandler['ipv4vs']['editlblist']['addLB'] = 'addLoadBalancer';
$ophandler['ipv4vs']['editlblist']['delLB'] = 'tableHandler';
$ophandler['ipv4vs']['editlblist']['updLB'] = 'tableHandler';
$ophandler['ipv4vs']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['ipv4vs']['files']['addFile'] = 'addFileToEntity';
$ophandler['ipv4vs']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['ipv4vs']['files']['unlinkFile'] = 'unlinkFile';

$page['ipv4rspool']['parent'] = 'ipv4slb';
$page['ipv4rspool']['bypass'] = 'pool_id';
$page['ipv4rspool']['bypass_type'] = 'uint';
$tab['ipv4rspool']['default'] = 'View';
$tab['ipv4rspool']['edit'] = 'Edit';
$tab['ipv4rspool']['editlblist'] = 'Load balancers';
$tab['ipv4rspool']['editrslist'] = 'RS list';
$tab['ipv4rspool']['tags'] = 'Tags';
$tab['ipv4rspool']['files'] = 'Files';
$trigger['ipv4rspool']['tags'] = 'trigger_tags';
$tabhandler['ipv4rspool']['default'] = 'renderRSPool';
$tabhandler['ipv4rspool']['edit'] = 'renderEditRSPool';
$tabhandler['ipv4rspool']['editrslist'] = 'renderRSPoolServerForm';
$tabhandler['ipv4rspool']['editlblist'] = 'renderSLBEditTab';
$tabhandler['ipv4rspool']['tags'] = 'renderEntityTags';
$tabhandler['ipv4rspool']['files'] = 'renderFilesForEntity';
$ophandler['ipv4rspool']['edit']['updIPv4RSP'] = 'tableHandler';
$ophandler['ipv4rspool']['edit']['cloneIPv4RSP'] = 'cloneRSPool';
$ophandler['ipv4rspool']['edit']['del'] = 'deleteRSPool';
$ophandler['ipv4rspool']['editrslist']['addRS'] = 'addRealServer';
$ophandler['ipv4rspool']['editrslist']['delRS'] = 'tableHandler';
$ophandler['ipv4rspool']['editrslist']['updRS'] = 'updateRealServer';
$ophandler['ipv4rspool']['editrslist']['addMany'] = 'addRealServers';
$ophandler['ipv4rspool']['editlblist']['addLB'] = 'addLoadBalancer';
$ophandler['ipv4rspool']['editlblist']['delLB'] = 'tableHandler';
$ophandler['ipv4rspool']['editlblist']['updLB'] = 'tableHandler';
$ophandler['ipv4rspool']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['ipv4rspool']['files']['addFile'] = 'addFileToEntity';
$ophandler['ipv4rspool']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['ipv4rspool']['files']['unlinkFile'] = 'unlinkFile';

$page['search']['handler'] = 'searchHandler';
$page['search']['parent'] = 'index';
$page['search']['bypass'] = 'q';

$page['config']['title'] = 'Configuration';
$page['config']['handler'] = 'renderConfigMainpage';
$page['config']['parent'] = 'index';

$page['userlist']['title'] = 'Local users';
$page['userlist']['parent'] = 'config';
$tab['userlist']['default'] = 'View';
$tab['userlist']['edit'] = 'Edit';
$tabhandler['userlist']['default'] = 'renderUserList';
$tabhandler['userlist']['edit'] = 'renderUserListEditor';
$ophandler['userlist']['edit']['updateUser'] = 'updateUser';
$ophandler['userlist']['edit']['createUser'] = 'createUser';

$page['user']['parent'] = 'userlist';
$page['user']['bypass'] = 'user_id';
$page['user']['bypass_type'] = 'uint';
$tab['user']['default'] = 'View';
$tab['user']['tags'] = 'Tags';
$tab['user']['files'] = 'Files';
$tabhandler['user']['default'] = 'renderUser';
$tabhandler['user']['tags'] = 'renderEntityTags';
$tabhandler['user']['files'] = 'renderFilesForEntity';
$ophandler['user']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['user']['files']['addFile'] = 'addFileToEntity';
$ophandler['user']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['user']['files']['unlinkFile'] = 'unlinkFile';

$page['perms']['title'] = 'Permissions';
$page['perms']['parent'] = 'config';
$tab['perms']['default'] = 'View';
$tab['perms']['edit'] = 'Edit';
$tabhandler['perms']['default'] = 'renderRackCodeViewer';
$tabhandler['perms']['edit'] = 'renderRackCodeEditor';
$ophandler['perms']['edit']['saveRackCode'] = 'saveRackCode';

$page['parentmap']['title'] = 'Object container compatibility';
$page['parentmap']['parent'] = 'config';
$tab['parentmap']['default'] = 'View';
$tab['parentmap']['edit'] = 'Edit';
$tabhandler['parentmap']['default'] = 'renderObjectParentCompatViewer';
$tabhandler['parentmap']['edit'] = 'renderObjectParentCompatEditor';
$ophandler['parentmap']['edit']['add'] = 'tableHandler';
$ophandler['parentmap']['edit']['del'] = 'tableHandler';

$page['portmap']['title'] = 'Port compatibility';
$page['portmap']['parent'] = 'config';
$tab['portmap']['default'] = 'View';
$tab['portmap']['edit'] = 'Edit';
$tabhandler['portmap']['default'] = 'renderOIFCompatViewer';
$tabhandler['portmap']['edit'] = 'renderOIFCompatEditor';
$ophandler['portmap']['edit']['add'] = 'tableHandler';
$ophandler['portmap']['edit']['del'] = 'tableHandler';
$ophandler['portmap']['edit']['addPack'] = 'addOIFCompatPack';
$ophandler['portmap']['edit']['delPack'] = 'delOIFCompatPack';

$page['portifcompat']['title'] = 'Enabled port types';
$page['portifcompat']['parent'] = 'config';
$tab['portifcompat']['default'] = 'View';
$tab['portifcompat']['edit'] = 'Edit';
$tabhandler['portifcompat']['default'] = 'renderIIFOIFCompat';
$tabhandler['portifcompat']['edit'] = 'renderIIFOIFCompatEditor';
$ophandler['portifcompat']['edit']['add'] = 'addIIFOIFCompat';
$ophandler['portifcompat']['edit']['del'] = 'tableHandler';
$ophandler['portifcompat']['edit']['addPack'] = 'addIIFOIFCompatPack';
$ophandler['portifcompat']['edit']['delPack'] = 'delIIFOIFCompatPack';

$page['attrs']['title'] = 'Attributes';
$page['attrs']['parent'] = 'config';
$tab['attrs']['default'] = 'View';
$tab['attrs']['editattrs'] = 'Edit attributes';
$tab['attrs']['editmap'] = 'Edit map';
$tabhandler['attrs']['default'] = 'renderAttributes';
$tabhandler['attrs']['editattrs'] = 'renderEditAttributesForm';
$tabhandler['attrs']['editmap'] = 'renderEditAttrMapForm';
$ophandler['attrs']['editattrs']['add'] = 'tableHandler';
$ophandler['attrs']['editattrs']['del'] = 'tableHandler';
$ophandler['attrs']['editattrs']['upd'] = 'tableHandler';
$ophandler['attrs']['editmap']['add'] = 'supplementAttrMap';
$ophandler['attrs']['editmap']['del'] = 'tableHandler';

$page['dict']['title'] = 'Dictionary';
$page['dict']['parent'] = 'config';
$tab['dict']['default'] = 'View';
$tab['dict']['chapters'] = 'Manage chapters';
$tabhandler['dict']['default'] = 'renderDictionary';
$tabhandler['dict']['chapters'] = 'renderChaptersEditor';
$ophandler['dict']['chapters']['add'] = 'tableHandler';
$ophandler['dict']['chapters']['del'] = 'tableHandler';
$ophandler['dict']['chapters']['upd'] = 'tableHandler';

$page['chapter']['parent'] = 'dict';
$page['chapter']['bypass'] = 'chapter_no';
$page['chapter']['bypass_type'] = 'uint';
$tab['chapter']['default'] = 'View';
$tab['chapter']['edit'] = 'Edit';
$tabhandler['chapter']['default'] = 'renderChapter';
$tabhandler['chapter']['edit'] = 'renderChapterEditor';
$ophandler['chapter']['edit']['add'] = 'tableHandler';
$ophandler['chapter']['edit']['del'] = 'tableHandler';
$ophandler['chapter']['edit']['upd'] = 'tableHandler';

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
$ophandler['tagtree']['edit']['createTag'] = 'tableHandler';
$ophandler['tagtree']['edit']['destroyTag'] = 'tableHandler';
$ophandler['tagtree']['edit']['updateTag'] = 'tableHandler';

$page['myaccount']['title'] = 'My account';
$page['myaccount']['parent'] = 'config';
$tab['myaccount']['default'] = 'Info';
$tab['myaccount']['mypassword'] = 'Password change';
$tab['myaccount']['interface'] = 'Interface preferences';
$tab['myaccount']['qlinks'] = 'Quick links';
$trigger['myaccount']['mypassword'] = 'trigger_passwdchange';
$tabhandler['myaccount']['default'] = 'renderMyAccount';
$tabhandler['myaccount']['mypassword'] = 'renderMyPasswordEditor';
$tabhandler['myaccount']['interface'] = 'renderMyPreferences';
$tabhandler['myaccount']['qlinks'] = 'renderMyQuickLinks';
$ophandler['myaccount']['mypassword']['changeMyPassword'] = 'changeMyPassword';
$ophandler['myaccount']['interface']['upd'] = 'saveMyPreferences';
$ophandler['myaccount']['interface']['reset'] = 'resetMyPreference';
$ophandler['myaccount']['qlinks']['save'] = 'saveQuickLinks';

$page['cacti']['title'] = 'Cacti';
$page['cacti']['parent'] = 'config';
$tab['cacti']['default'] = 'View';
$tab['cacti']['servers'] = 'Manage servers';
$tabhandler['cacti']['default'] = 'renderCactiConfig';
$tabhandler['cacti']['servers'] = 'renderCactiServersEditor';
$ophandler['cacti']['servers']['add'] = 'tableHandler';
$ophandler['cacti']['servers']['del'] = 'tableHandler';
$ophandler['cacti']['servers']['upd'] = 'tableHandler';

$page['reports']['title'] = 'Reports';
$page['reports']['parent'] = 'index';
$tab['reports']['default'] = 'System';
$tab['reports']['rackcode'] = 'RackCode';
$tab['reports']['ipv4'] = 'IPv4';
$tab['reports']['ipv6'] = 'IPv6';
$tab['reports']['ports'] = 'Ports';
$tab['reports']['8021q'] = '802.1Q';
$tab['reports']['warranty'] = 'Expirations';
$tab['reports']['local'] = 'local'; // this one is set later in init.php
$trigger['reports']['local'] = 'trigger_localreports';
$tabhandler['reports']['default'] = 'renderSystemReports';
$tabhandler['reports']['rackcode'] = 'renderRackCodeReports';
$tabhandler['reports']['ipv4'] = 'renderIPv4Reports';
$tabhandler['reports']['ipv6'] = 'renderIPv6Reports';
$tabhandler['reports']['ports'] = 'renderPortsReport';
$tabhandler['reports']['8021q'] = 'render8021QReport';
$tabhandler['reports']['warranty'] = 'renderExpirations';
$tabhandler['reports']['local'] = 'renderLocalReports';

$page['files']['title'] = 'Files';
$page['files']['parent'] = 'index';
$tab['files']['default'] = 'Browse';
$tab['files']['manage'] = 'Manage';
$tabhandler['files']['default'] = 'renderFileBrowser';
$tabhandler['files']['manage'] = 'renderFileManager';
$ophandler['files']['manage']['addFile'] = 'addFileWithoutLink';
$ophandler['files']['manage']['unlinkFile'] = 'unlinkFile';
$ophandler['files']['manage']['deleteFile'] = 'deleteFile';

$page['file']['bypass'] = 'file_id';
$page['file']['bypass_type'] = 'uint';
$page['file']['parent'] = 'files';
$tab['file']['default'] = 'View';
$tab['file']['edit'] = 'Properties';
$tab['file']['tags'] = 'Tags';
$tab['file']['editText'] = 'Edit text';
$tab['file']['replaceData'] = 'Upload replacement';
$tab['file']['download'] = 'Download';
$trigger['file']['tags'] = 'trigger_tags';
$trigger['file']['editText'] = 'trigger_file_editText';
$tabhandler['file']['default'] = 'renderFile';
$tabhandler['file']['edit'] = 'renderFileProperties';
$tabhandler['file']['tags'] = 'renderEntityTags';
$tabhandler['file']['editText'] = 'renderTextEditor';
$tabhandler['file']['replaceData'] = 'renderFileReuploader';
$tabhandler['file']['download'] = 'renderFileDownloader';
$ophandler['file']['edit']['updateFile'] = 'tableHandler';
$ophandler['file']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['file']['editText']['updateFileText'] = 'updateFileText';
$ophandler['file']['replaceData']['replaceFile'] = 'replaceFile';

$page['8021q']['title'] = '802.1Q';
$page['8021q']['parent'] = 'index';
$tab['8021q']['default'] = 'Status';
$tab['8021q']['vdlist'] = 'Manage domains';
$tab['8021q']['vstlist'] = 'Manage templates';
$tabhandler['8021q']['default'] = 'render8021QStatus';
$tabhandler['8021q']['vdlist'] = 'renderVLANDomainListEditor';
$tabhandler['8021q']['vstlist'] = 'renderVSTListEditor';
$ophandler['8021q']['vdlist']['add'] = 'createVLANDomain';
$ophandler['8021q']['vdlist']['del'] = 'tableHandler';
$ophandler['8021q']['vdlist']['upd'] = 'tableHandler';
$ophandler['8021q']['vstlist']['add'] = 'tableHandler';
$ophandler['8021q']['vstlist']['del'] = 'tableHandler';
$ophandler['8021q']['vstlist']['upd'] = 'tableHandler';

$page['vlandomain']['parent'] = '8021q';
$page['vlandomain']['bypass'] = 'vdom_id';
$page['vlandomain']['bypass_type'] = 'uint';
$tab['vlandomain']['default'] = 'View';
$tab['vlandomain']['vlanlist'] = 'VLAN list';
$tab['vlandomain']['8021qorder'] = '802.1Q orders';
$trigger['vlandomain']['8021qorder'] = 'trigger_8021q_configured';
$tabhandler['vlandomain']['default'] = 'renderVLANDomain';
$tabhandler['vlandomain']['8021qorder'] = 'render8021QOrderForm';
$tabhandler['vlandomain']['vlanlist'] = 'renderVLANDomainVLANList';
$ophandler['vlandomain']['8021qorder']['add'] = 'add8021QOrder';
$ophandler['vlandomain']['8021qorder']['del'] = 'del8021QOrder';
$ophandler['vlandomain']['vlanlist']['add'] = 'tableHandler';
$ophandler['vlandomain']['vlanlist']['del'] = 'tableHandler';
$ophandler['vlandomain']['vlanlist']['upd'] = 'tableHandler';
$delayauth['vlandomain-8021qorder-add'] = TRUE;
$delayauth['vlandomain-8021qorder-del'] = TRUE;

$page['vlan']['parent'] = 'vlandomain';
$page['vlan']['bypass'] = 'vlan_ck';
$page['vlan']['bypass_type'] = 'string';
$tab['vlan']['default'] = 'View';
$tab['vlan']['edit'] = 'Edit';
$tab['vlan']['ipv4'] = 'IPv4';
$tab['vlan']['ipv6'] = 'IPv6';
$trigger['vlan']['ipv4'] = 'trigger_vlan_ipv4net';
$trigger['vlan']['ipv6'] = 'trigger_vlan_ipv6net';
$tabhandler['vlan']['default'] = 'renderVLANInfo';
$tabhandler['vlan']['edit'] = 'renderEditVlan';
$tabhandler['vlan']['ipv4'] = 'renderVLANIPLinks';
$tabhandler['vlan']['ipv6'] = 'renderVLANIPLinks';
$ophandler['vlan']['edit']['del'] = 'deleteVlan';
$ophandler['vlan']['edit']['clear'] = 'clearVlan';
$ophandler['vlan']['edit']['upd'] = 'tableHandler';
$ophandler['vlan']['ipv4']['bind'] = 'bindVLANtoIPv4';
$ophandler['vlan']['ipv4']['unbind'] = 'unbindVLANfromIPv4';
$ophandler['vlan']['ipv6']['bind'] = 'bindVLANtoIPv6';
$ophandler['vlan']['ipv6']['unbind'] = 'unbindVLANfromIPv6';

$page['vst']['parent'] = '8021q';
$page['vst']['bypass'] = 'vst_id';
$page['vst']['bypass_type'] = 'uint';
$tab['vst']['default'] = 'View';
$tab['vst']['editrules'] = 'Edit';
$tab['vst']['8021qorder'] = '802.1Q orders';
$tab['vst']['tags'] = 'Tags';
$trigger['vst']['editrules'] = 'trigger_vst_editrules';
$trigger['vst']['8021qorder'] = 'trigger_8021q_configured';
$trigger['vst']['tags'] = 'trigger_tags';
$tabhandler['vst']['default'] = 'renderVST';
$tabhandler['vst']['editrules'] = 'renderVSTRulesEditor';
$tabhandler['vst']['8021qorder'] = 'render8021QOrderForm';
$tabhandler['vst']['tags'] = 'renderEntityTags';
$ophandler['vst']['editrules']['clone'] = 'cloneVST';
$ophandler['vst']['editrules']['upd'] = 'updVSTRule';
$ophandler['vst']['8021qorder']['add'] = 'add8021QOrder';
$ophandler['vst']['8021qorder']['del'] = 'del8021QOrder';
$ophandler['vst']['tags']['saveTags'] = 'saveEntityTags';
$delayauth['vst-8021qorder-add'] = TRUE;
$delayauth['vst-8021qorder-del'] = TRUE;

$page['dqueue']['parent'] = '8021q';
$page['dqueue']['bypass'] = 'dqcode';
$page['dqueue']['bypass_type'] = 'enum/dqcode';
$tab['dqueue']['default'] = 'View';
$tabhandler['dqueue']['default'] = 'renderDeployQueue';

$page['objectlog']['title'] = 'Log records';
$page['objectlog']['parent'] = 'index';
$tab['objectlog']['default'] = 'View';
$tabhandler['objectlog']['default'] = 'allObjectLogs';

$page['virtual']['title'] = 'Virtual Resources';
$page['virtual']['parent'] = 'index';
$tab['virtual']['default'] = 'Summary';
$tabhandler['virtual']['default'] = 'renderVirtualResourcesSummary';

$ajaxhandler['get-tag-select'] = 'getTagSelectAJAX';
$ajaxhandler['get-location-select'] = 'getLocationSelectAJAX';
$ajaxhandler['verifyCode'] = 'verifyCodeAJAX';
$ajaxhandler['get-port-link'] = 'getPortInfoAJAX';
$ajaxhandler['get-port-mac'] = 'getPortInfoAJAX';
$ajaxhandler['get-port-conf'] = 'getPortInfoAJAX';
$ajaxhandler['upd-ip-name'] = 'updateIPNameAJAX';
$ajaxhandler['upd-ip-comment'] = 'updateIPCommentAJAX';
$ajaxhandler['upd-rack-sort-order'] = 'updateRackSortOrderAJAX';
$ajaxhandler['upd-reservation-port'] = 'updatePortRsvAJAX';
$ajaxhandler['upd-reservation-cable'] = 'updateCableIdAJAX';
$ajaxhandler['net-usage'] = 'getNetUsageAJAX';

?>
