<?php
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
$delayauth = array();

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
$tab['rackspace']['edit'] = 'Manage rows';
$tab['rackspace']['history'] = 'History';
$tabhandler['rackspace']['default'] = 'renderRackspace';
$tabhandler['rackspace']['edit'] = 'renderRackspaceRowEditor';
$tabhandler['rackspace']['history'] = 'renderRackspaceHistory';
$ophandler['rackspace']['edit']['addRow'] = 'tableHandler';
$ophandler['rackspace']['edit']['updateRow'] = 'tableHandler';
$ophandler['rackspace']['edit']['deleteRow'] = 'tableHandler';

$page['depot']['parent'] = 'index';
$page['depot']['title'] = 'Objects';
$tab['depot']['default'] = 'Browse';
$tab['depot']['addmore'] = 'Add more';
$tabhandler['depot']['default'] = 'renderDepot';
$tabhandler['depot']['addmore'] = 'renderAddMultipleObjectsForm';
$ophandler['depot']['addmore']['addObjects'] = 'addMultipleObjects';
$ophandler['depot']['addmore']['addLotOfObjects'] = 'addLotOfObjects';
$ophandler['depot']['addmore']['deleteObject'] = 'deleteObject';

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
$ophandler['rack']['edit']['updateRack'] = 'updateRack';
$ophandler['rack']['edit']['deleteRack'] = 'deleteRack';
$ophandler['rack']['log']['add'] = 'addObjectlog';
$ophandler['rack']['log']['del'] = 'deleteObjectLog';
$ophandler['rack']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['rack']['files']['addFile'] = 'addFileToEntity';
$ophandler['rack']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['rack']['files']['unlinkFile'] = 'unlinkFile';

$page['object']['bypass'] = 'object_id';
$page['object']['bypass_type'] = 'uint';
$page['object']['bypass_tabs'] = array ('hl_port_id');
$page['object']['parent'] = 'depot';
$tab['object']['default'] = 'View';
$tab['object']['edit'] = 'Properties';
$tab['object']['log'] = 'Log';
$tab['object']['rackspace'] = 'Rackspace';
$tab['object']['ports'] = 'Ports';
$tab['object']['ipv4'] = 'IPv4';
$tab['object']['ipv6'] = 'IPv6';
$tab['object']['nat4'] = 'NATv4';
$tab['object']['livevlans'] = 'Live VLANs';
$tab['object']['liveports'] = 'Live ports';
$tab['object']['livecdp'] = 'Live CDP';
$tab['object']['livelldp'] = 'Live LLDP';
$tab['object']['snmpportfinder'] = 'SNMP sync';
$tab['object']['editrspvs'] = 'RS pools';
$tab['object']['lvsconfig'] = 'keepalived.conf';
$tab['object']['autoports'] = 'AutoPorts';
$tab['object']['tags'] = 'Tags';
$tab['object']['files'] = 'Files';
$tab['object']['8021qorder'] = '802.1Q order';
$tab['object']['8021qports'] = '802.1Q ports';
$tab['object']['8021qsync'] = '802.1Q sync';
$tab['object']['cactigraphs'] = 'Cacti Graphs';
$tabhandler['object']['default'] = 'renderObject';
$tabhandler['object']['edit'] = 'renderEditObjectForm';
$tabhandler['object']['log'] = 'renderObjectLogEditor';
$tabhandler['object']['rackspace'] = 'renderRackSpaceForObject';
$tabhandler['object']['ports'] = 'renderPortsForObject';
$tabhandler['object']['ipv4'] = 'renderIPv4ForObject';
$tabhandler['object']['ipv6'] = 'renderIPv6ForObject';
$tabhandler['object']['nat4'] = 'renderNATv4ForObject';
$tabhandler['object']['livevlans'] = 'renderVLANMembership';
$tabhandler['object']['liveports'] = 'renderPortsInfo';
$tabhandler['object']['livecdp'] = 'renderDiscoveredNeighbors';
$tabhandler['object']['livelldp'] = 'renderDiscoveredNeighbors';
$tabhandler['object']['snmpportfinder'] = 'renderSNMPPortFinder';
$tabhandler['object']['lvsconfig'] = 'renderLVSConfig';
$tabhandler['object']['autoports'] = 'renderAutoPortsForm';
$tabhandler['object']['tags'] = 'renderEntityTags';
$tabhandler['object']['files'] = 'renderFilesForEntity';
$tabhandler['object']['editrspvs'] = 'renderObjectSLB';
$tabhandler['object']['8021qorder'] = 'render8021QOrderForm';
$tabhandler['object']['8021qports'] = 'renderObject8021QPorts';
$tabhandler['object']['8021qsync'] = 'renderObject8021QSync';
$tabhandler['object']['cactigraphs'] = 'renderObjectCactiGraphs';
$trigger['object']['rackspace'] = 'trigger_rackspace';
$trigger['object']['ports'] = 'trigger_ports';
$trigger['object']['ipv4'] = 'trigger_ipv4';
$trigger['object']['ipv6'] = 'trigger_ipv6';
$trigger['object']['nat4'] = 'trigger_natv4';
$trigger['object']['livevlans'] = 'trigger_livevlans';
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
$trigger['object']['cactigraphs'] = 'triggerCactiGraphs';
$ophandler['object']['edit']['linkEntities'] = 'linkEntities';
$ophandler['object']['edit']['unlinkEntities'] = 'unlinkEntities';
$ophandler['object']['rackspace']['updateObjectAllocation'] = 'updateObjectAllocation';
$ophandler['object']['ports']['addPort'] = 'addPortForObject';
$ophandler['object']['ports']['editPort'] = 'editPortForObject';
$ophandler['object']['ports']['linkPort'] = 'linkPortForObject';
$ophandler['object']['ports']['addMultiPorts'] = 'addMultiPorts';
$ophandler['object']['ports']['addBulkPorts'] = 'addBulkPorts';
$ophandler['object']['ports']['useup'] = 'useupPort';
$ophandler['object']['ports']['delPort'] = 'tableHandler';
$ophandler['object']['ports']['deleteAll'] = 'tableHandler';
$ophandler['object']['ports']['unlinkPort'] = 'unlinkPort';
$ophandler['object']['ipv4']['updIPv4Allocation'] = 'updIPv4Allocation';
$ophandler['object']['ipv4']['addIPv4Allocation'] = 'addIPv4Allocation';
$ophandler['object']['ipv4']['delIPv4Allocation'] = 'delIPv4Allocation';
$ophandler['object']['ipv6']['updIPv6Allocation'] = 'updIPv6Allocation';
$ophandler['object']['ipv6']['addIPv6Allocation'] = 'addIPv6Allocation';
$ophandler['object']['ipv6']['delIPv6Allocation'] = 'delIPv6Allocation';
$ophandler['object']['edit']['clearSticker'] = 'clearSticker';
$ophandler['object']['edit']['update'] = 'updateObject';
$ophandler['object']['edit']['resetObject'] = 'resetObject';
$ophandler['object']['log']['add'] = 'addObjectlog';
$ophandler['object']['log']['del'] = 'tableHandler';
$ophandler['object']['nat4']['addNATv4Rule'] = 'addPortForwarding';
$ophandler['object']['nat4']['delNATv4Rule'] = 'delPortForwarding';
$ophandler['object']['nat4']['updNATv4Rule'] = 'updPortForwarding';
$ophandler['object']['livevlans']['setPortVLAN'] = 'setPortVLAN';
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
$ophandler['object']['cactigraphs']['add'] = 'tableHandler';
$ophandler['object']['cactigraphs']['del'] = 'tableHandler';
$delayauth['object-8021qports-save8021QConfig'] = TRUE;
$delayauth['object-livevlans-setPortVLAN'] = TRUE;
$delayauth['object-8021qorder-add'] = TRUE;
$delayauth['object-8021qorder-del'] = TRUE;

$page['ipv4space']['parent'] = 'index';
$tab['ipv4space']['default'] = 'Browse';
$tab['ipv4space']['newrange'] = 'Manage';
$tabhandler['ipv4space']['default'] = 'renderIPv4Space';
$tabhandler['ipv4space']['newrange'] = 'renderIPv4SpaceEditor';
$ophandler['ipv4space']['newrange']['addIPv4Prefix'] = 'addIPv4Prefix';
$ophandler['ipv4space']['newrange']['delIPv4Prefix'] = 'delIPv4Prefix';

$page['ipv6space']['parent'] = 'index';
$tab['ipv6space']['default'] = 'Browse';
$tab['ipv6space']['newrange'] = 'Manage';
$tabhandler['ipv6space']['default'] = 'renderIPv6Space';
$tabhandler['ipv6space']['newrange'] = 'renderIPv6SpaceEditor';
$ophandler['ipv6space']['newrange']['addIPv6Prefix'] = 'addIPv6Prefix';
$ophandler['ipv6space']['newrange']['delIPv6Prefix'] = 'delIPv6Prefix';

$page['ipv4net']['parent'] = 'ipv4space';
$page['ipv4net']['bypass'] = 'id';
$page['ipv4net']['bypass_type'] = 'uint';
$tab['ipv4net']['default'] = 'Browse';
$tab['ipv4net']['properties'] = 'Properties';
$tab['ipv4net']['liveptr'] = 'Live PTR';
$tab['ipv4net']['tags'] = 'Tags';
$tab['ipv4net']['files'] = 'Files';
$tab['ipv4net']['8021q'] = '802.1Q';
$tabhandler['ipv4net']['default'] = 'renderIPv4Network';
$tabhandler['ipv4net']['properties'] = 'renderIPNetworkProperties';
$tabhandler['ipv4net']['liveptr'] = 'renderLivePTR';
$tabhandler['ipv4net']['tags'] = 'renderEntityTags';
$tabhandler['ipv4net']['files'] = 'renderFilesForEntity';
$tabhandler['ipv4net']['8021q'] = 'renderVLANIPLinks';
$trigger['ipv4net']['tags'] = 'trigger_tags';
$trigger['ipv4net']['8021q'] = 'trigger_ipv4net_vlanconfig';
$ophandler['ipv4net']['properties']['editRange'] = 'tableHandler';
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
$tabhandler['ipv6net']['default'] = 'renderIPv6Network';
$tabhandler['ipv6net']['properties'] = 'renderIPNetworkProperties';
$tabhandler['ipv6net']['tags'] = 'renderEntityTags';
$tabhandler['ipv6net']['files'] = 'renderFilesForEntity';
$tabhandler['ipv6net']['8021q'] = 'renderVLANIPLinks';
$trigger['ipv6net']['tags'] = 'trigger_tags';
$trigger['ipv6net']['8021q'] = 'trigger_ipv6net_vlanconfig';
$ophandler['ipv6net']['properties']['editRange'] = 'tableHandler';
$ophandler['ipv6net']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['ipv6net']['files']['addFile'] = 'addFileToEntity';
$ophandler['ipv6net']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['ipv6net']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['ipv6net']['8021q']['bind'] = 'bindVLANtoIPv6';
$ophandler['ipv6net']['8021q']['unbind'] = 'unbindVLANfromIPv6';

$page['ipaddress']['parent'] = 'ipv4net';
$page['ipaddress']['bypass'] = 'ip';
$page['ipaddress']['bypass_type'] = 'inet4';
$tab['ipaddress']['default'] = 'Browse';
$tab['ipaddress']['properties'] = 'Properties';
$tab['ipaddress']['assignment'] = 'Allocation';
$tab['ipaddress']['log'] = 'Change log';
$tabhandler['ipaddress']['default'] = 'renderIPAddress';
$tabhandler['ipaddress']['properties'] = 'renderIPAddressProperties';
$tabhandler['ipaddress']['assignment'] = 'renderIPAddressAllocations';
$tabhandler['ipaddress']['log'] = 'renderIPv4AddressLog';
$trigger['ipaddress']['log'] = 'triggerIPv4AddressLog';
$ophandler['ipaddress']['properties']['editAddress'] = 'editAddress';
$ophandler['ipaddress']['assignment']['delIPv4Allocation'] = 'delIPv4Allocation';
$ophandler['ipaddress']['assignment']['updIPv4Allocation'] = 'updIPv4Allocation';
$ophandler['ipaddress']['assignment']['addIPv4Allocation'] = 'addIPv4Allocation';

$page['ipv6address']['parent'] = 'ipv6net';
$page['ipv6address']['bypass'] = 'ip';
$page['ipv6address']['bypass_type'] = 'string';
$tab['ipv6address']['default'] = 'Browse';
$tab['ipv6address']['properties'] = 'Properties';
$tab['ipv6address']['assignment'] = 'Allocation';
$tabhandler['ipv6address']['default'] = 'renderIPAddress';
$tabhandler['ipv6address']['properties'] = 'renderIPAddressProperties';
$tabhandler['ipv6address']['assignment'] = 'renderIPAddressAllocations';
$ophandler['ipv6address']['properties']['editAddress'] = 'editv6Address';
$ophandler['ipv6address']['assignment']['delIPv6Allocation'] = 'delIPv6Allocation';
$ophandler['ipv6address']['assignment']['updIPv6Allocation'] = 'updIPv6Allocation';
$ophandler['ipv6address']['assignment']['addIPv6Allocation'] = 'addIPv6Allocation';

$page['ipv4slb']['title'] = 'IPv4 SLB';
$page['ipv4slb']['parent'] = 'index';
$tab['ipv4slb']['default'] = 'Browse';
$tab['ipv4slb']['defconfig'] = 'Default configs';
$tabhandler['ipv4slb']['default'] = 'renderIPv4SLB';
$tabhandler['ipv4slb']['defconfig'] = 'renderSLBDefConfig';
$ophandler['ipv4slb']['defconfig']['save'] = 'updateSLBDefConfig';

$page['ipv4vslist']['title'] = 'Virtual services';
$page['ipv4vslist']['parent'] = 'ipv4slb';
$tab['ipv4vslist']['default'] = 'View';
$tab['ipv4vslist']['edit'] = 'Edit';
$tabhandler['ipv4vslist']['default'] = 'renderVSList';
$tabhandler['ipv4vslist']['edit'] = 'renderVSListEditForm';
$ophandler['ipv4vslist']['edit']['add'] = 'addVService';
$ophandler['ipv4vslist']['edit']['del'] = 'deleteVService';

$page['ipv4vs']['parent'] = 'ipv4vslist';
$page['ipv4vs']['bypass'] = 'vs_id';
$page['ipv4vs']['bypass_type'] = 'uint';
$tab['ipv4vs']['default'] = 'View';
$tab['ipv4vs']['edit'] = 'Edit';
$tab['ipv4vs']['editlblist'] = 'Load balancers';
$tab['ipv4vs']['tags'] = 'Tags';
$tab['ipv4vs']['files'] = 'Files';
$tabhandler['ipv4vs']['default'] = 'renderVirtualService';
$tabhandler['ipv4vs']['edit'] = 'renderEditVService';
$tabhandler['ipv4vs']['editlblist'] = 'renderVServiceLBForm';
$tabhandler['ipv4vs']['tags'] = 'renderEntityTags';
$tabhandler['ipv4vs']['files'] = 'renderFilesForEntity';
$trigger['ipv4vs']['tags'] = 'trigger_tags';
$ophandler['ipv4vs']['edit']['updIPv4VS'] = 'updateVService';
$ophandler['ipv4vs']['editlblist']['addLB'] = 'addLoadBalancer';
$ophandler['ipv4vs']['editlblist']['delLB'] = 'tableHandler';
$ophandler['ipv4vs']['editlblist']['updLB'] = 'tableHandler';
$ophandler['ipv4vs']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['ipv4vs']['files']['addFile'] = 'addFileToEntity';
$ophandler['ipv4vs']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['ipv4vs']['files']['unlinkFile'] = 'unlinkFile';

$page['ipv4rsplist']['title'] = 'RS pools';
$page['ipv4rsplist']['parent'] = 'ipv4slb';
$tab['ipv4rsplist']['default'] = 'View';
$tab['ipv4rsplist']['edit'] = 'Edit';
$tabhandler['ipv4rsplist']['default'] = 'renderRSPoolList';
$tabhandler['ipv4rsplist']['edit'] = 'editRSPools';
$ophandler['ipv4rsplist']['edit']['add'] = 'addRSPool';
$ophandler['ipv4rsplist']['edit']['del'] = 'deleteRSPool';

$page['ipv4rspool']['parent'] = 'ipv4rsplist';
$page['ipv4rspool']['bypass'] = 'pool_id';
$page['ipv4rspool']['bypass_type'] = 'uint';
$tab['ipv4rspool']['default'] = 'View';
$tab['ipv4rspool']['edit'] = 'Edit';
$tab['ipv4rspool']['editlblist'] = 'Load balancers';
$tab['ipv4rspool']['editrslist'] = 'RS list';
$tab['ipv4rspool']['rsinservice'] = 'RS in service';
$tab['ipv4rspool']['tags'] = 'Tags';
$tab['ipv4rspool']['files'] = 'Files';
$trigger['ipv4rspool']['rsinservice'] = 'trigger_poolrscount';
$trigger['ipv4rspool']['tags'] = 'trigger_tags';
$tabhandler['ipv4rspool']['default'] = 'renderRSPool';
$tabhandler['ipv4rspool']['edit'] = 'renderEditRSPool';
$tabhandler['ipv4rspool']['editrslist'] = 'renderRSPoolServerForm';
$tabhandler['ipv4rspool']['editlblist'] = 'renderRSPoolLBForm';
$tabhandler['ipv4rspool']['rsinservice'] = 'renderRSPoolRSInServiceForm';
$tabhandler['ipv4rspool']['tags'] = 'renderEntityTags';
$tabhandler['ipv4rspool']['files'] = 'renderFilesForEntity';
$ophandler['ipv4rspool']['edit']['updIPv4RSP'] = 'tableHandler';
$ophandler['ipv4rspool']['editrslist']['addRS'] = 'addRealServer';
$ophandler['ipv4rspool']['editrslist']['delRS'] = 'tableHandler';
$ophandler['ipv4rspool']['editrslist']['updRS'] = 'updateRealServer';
$ophandler['ipv4rspool']['editrslist']['addMany'] = 'addRealServers';
$ophandler['ipv4rspool']['editlblist']['addLB'] = 'addLoadBalancer';
$ophandler['ipv4rspool']['editlblist']['delLB'] = 'tableHandler';
$ophandler['ipv4rspool']['editlblist']['updLB'] = 'tableHandler';
$ophandler['ipv4rspool']['rsinservice']['upd'] = 'updateRSInService';
$ophandler['ipv4rspool']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['ipv4rspool']['files']['addFile'] = 'addFileToEntity';
$ophandler['ipv4rspool']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['ipv4rspool']['files']['unlinkFile'] = 'unlinkFile';

$page['rservers']['title'] = 'Real servers';
$page['rservers']['parent'] = 'ipv4slb';
$page['rservers']['handler'] = 'renderRealServerList';

$page['lbs']['title'] = 'Load balancers';
$page['lbs']['parent'] = 'ipv4slb';
$page['lbs']['handler'] = 'renderLBList';

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
$ophandler['dict']['chapters']['del'] = 'delChapter';
$ophandler['dict']['chapters']['upd'] = 'updateChapter';

$page['chapter']['parent'] = 'dict';
$page['chapter']['bypass'] = 'chapter_no';
$page['chapter']['bypass_type'] = 'uint';
$tab['chapter']['default'] = 'View';
$tab['chapter']['edit'] = 'Edit';
$tabhandler['chapter']['default'] = 'renderChapter';
$tabhandler['chapter']['edit'] = 'renderChapterEditor';
$ophandler['chapter']['edit']['add'] = 'tableHandler';
$ophandler['chapter']['edit']['del'] = 'tableHandler';
$ophandler['chapter']['edit']['upd'] = 'updateDictionary';

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

$page['reports']['title'] = 'Reports';
$page['reports']['parent'] = 'index';
$tab['reports']['default'] = 'System';
$tab['reports']['rackcode'] = 'RackCode';
$tab['reports']['ipv4'] = 'IPv4';
$tab['reports']['ipv6'] = 'IPv6';
$tab['reports']['ports'] = 'Ports';
$tab['reports']['8021q'] = '802.1Q';
$tab['reports']['local'] = 'local'; // this one is set later in init.php
$trigger['reports']['local'] = 'trigger_localreports';
$tabhandler['reports']['default'] = 'renderSystemReports';
$tabhandler['reports']['rackcode'] = 'renderRackCodeReports';
$tabhandler['reports']['ipv4'] = 'renderIPv4Reports';
$tabhandler['reports']['ipv6'] = 'renderIPv6Reports';
$tabhandler['reports']['ports'] = 'renderPortsReport';
$tabhandler['reports']['8021q'] = 'render8021QReport';
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
$ophandler['8021q']['vdlist']['del'] = 'destroyVLANDomain';
$ophandler['8021q']['vdlist']['upd'] = 'tableHandler';
$ophandler['8021q']['vstlist']['add'] = 'addVLANSwitchTemplate';
$ophandler['8021q']['vstlist']['del'] = 'delVLANSwitchTemplate';
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
$tab['vlan']['ipv4'] = 'IPv4';
$tab['vlan']['ipv6'] = 'IPv6';
$trigger['vlan']['ipv4'] = 'trigger_vlan_ipv4net';
$trigger['vlan']['ipv6'] = 'trigger_vlan_ipv6net';
$tabhandler['vlan']['default'] = 'renderVLANInfo';
$tabhandler['vlan']['ipv4'] = 'renderVLANIPLinks';
$tabhandler['vlan']['ipv6'] = 'renderVLANIPLinks';
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

?>
