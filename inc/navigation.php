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
$tabextraclass = array();
$delayauth = array();
$msgcode = array();

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
$ophandler['rackspace']['edit']['addRow'] = 'addRow';
$ophandler['rackspace']['edit']['updateRow'] = 'updateRow';
$msgcode['addRow']['OK'] = 74;
$msgcode['addRow']['ERR'] = 100;
$msgcode['updateRow']['OK'] = 75;
$msgcode['updateRow']['ERR'] = 100;

$page['objects']['title'] = 'Objects';
$page['objects']['parent'] = 'index';
$tab['objects']['default'] = 'View';
$tab['objects']['addmore'] = 'Add more';
$tabhandler['objects']['default'] = 'renderObjectSpace';
$tabhandler['objects']['addmore'] = 'renderAddMultipleObjectsForm';
$ophandler['objects']['default']['deleteObject'] = 'deleteObject';
$msgcode['deleteObject']['OK'] = 76;
$msgcode['deleteObject']['ERR'] = 100;

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
$ophandler['row']['newrack']['addRack'] = 'addRack';
$msgcode['rollTags']['OK'] = 67;
$msgcode['rollTags']['ERR'] = 149;
$msgcode['addRack']['OK'] = 65;
$msgcode['addRack']['ERR1'] = 171;
$msgcode['addRack']['ERR2'] = 172;

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
$tab['rack']['files'] = 'Files';
$tabhandler['rack']['default'] = 'renderRackPage';
$tabhandler['rack']['edit'] = 'renderEditRackForm';
$tabhandler['rack']['design'] = 'renderRackDesign';
$tabhandler['rack']['problems'] = 'renderRackProblems';
$tabhandler['rack']['tags'] = 'renderEntityTags';
$tabhandler['rack']['files'] = 'renderFilesForEntity';
$trigger['rack']['tags'] = 'trigger_tags';
$ophandler['rack']['design']['updateRack'] = 'updateRackDesign';
$ophandler['rack']['problems']['updateRack'] = 'updateRackProblems';
$ophandler['rack']['edit']['updateRack'] = 'updateRack';
$ophandler['rack']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['rack']['files']['addFile'] = 'addFileToEntity';
$ophandler['rack']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['rack']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['rack']['files']['deleteFile'] = 'deleteFile';
$msgcode['updateRack']['OK'] = 68;
$msgcode['updateRack']['ERR'] = 177;
$msgcode['addFileToEntity']['OK'] = 69;
$msgcode['addFileToEntity']['ERR'] = 100;
$msgcode['updateFile']['OK'] = 70;
$msgcode['updateFile']['ERR'] = 100;
$msgcode['linkFileToEntity']['OK'] = 71;
$msgcode['linkFileToEntity']['ERR1'] = 178;
$msgcode['linkFileToEntity']['ERR2'] = 100;
$msgcode['unlinkFile']['OK'] = 72;
$msgcode['unlinkFile']['ERR'] = 100;
$msgcode['deleteFile']['OK'] = 73;
$msgcode['deleteFile']['ERR'] = 100;

$page['objgroup']['title_handler'] = 'dynamic_title_objgroup';
$page['objgroup']['handler'] = 'renderObjectGroup';
$page['objgroup']['bypass'] = 'group_id';
$page['objgroup']['bypass_type'] = 'uint0';
$page['objgroup']['parent'] = 'objects';
$ophandler['objgroup']['default']['deleteObject'] = 'deleteObject';

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
$tab['object']['ipv4'] = 'IPv4';
$tab['object']['nat4'] = 'NATv4';
$tab['object']['livevlans'] = 'Live VLANs';
$tab['object']['snmpportfinder'] = 'SNMP port finder';
$tab['object']['editrspvs'] = 'RS pools';
$tab['object']['lvsconfig'] = 'LVS config';
$tab['object']['autoports'] = 'AutoPorts';
$tab['object']['tags'] = 'Tags';
$tab['object']['files'] = 'Files';
$tabhandler['object']['default'] = 'renderRackObject';
$tabhandler['object']['edit'] = 'renderEditObjectForm';
$tabhandler['object']['rackspace'] = 'renderRackSpaceForObject';
$tabhandler['object']['ports'] = 'renderPortsForObject';
$tabhandler['object']['ipv4'] = 'renderIPv4ForObject';
$tabhandler['object']['nat4'] = 'renderNATv4ForObject';
$tabhandler['object']['livevlans'] = 'renderVLANMembership';
$tabhandler['object']['snmpportfinder'] = 'renderSNMPPortFinder';
$tabhandler['object']['lvsconfig'] = 'renderLVSConfig';
$tabhandler['object']['autoports'] = 'renderAutoPortsForm';
$tabhandler['object']['tags'] = 'renderEntityTags';
$tabhandler['object']['files'] = 'renderFilesForEntity';
$tabhandler['object']['editrspvs'] = 'renderObjectSLB';
$tabextraclass['object']['snmpportfinder'] = 'attn';
$tabextraclass['object']['autoports'] = 'attn';
$trigger['object']['ipv4'] = 'trigger_ipv4';
$trigger['object']['nat4'] = 'trigger_natv4';
$trigger['object']['livevlans'] = 'trigger_livevlans';
$trigger['object']['snmpportfinder'] = 'trigger_snmpportfinder';
$trigger['object']['editrspvs'] = 'trigger_natv4';
$trigger['object']['lvsconfig'] = 'trigger_lvsconfig';
$trigger['object']['autoports'] = 'trigger_autoports';
$trigger['object']['tags'] = 'trigger_tags';
$ophandler['object']['rackspace']['updateObjectAllocation'] = 'updateObjectAllocation';
$ophandler['object']['ports']['addPort'] = 'addPortForObject';
$ophandler['object']['ports']['delPort'] = 'delPortFromObject';
$ophandler['object']['ports']['editPort'] = 'editPortForObject';
$ophandler['object']['ports']['linkPort'] = 'linkPortForObject';
$ophandler['object']['ports']['unlinkPort'] = 'unlinkPortForObject';
$ophandler['object']['ports']['addMultiPorts'] = 'addMultiPorts';
$ophandler['object']['ports']['useup'] = 'useupPort';
$ophandler['object']['ipv4']['updIPv4Allocation'] = 'updIPv4Allocation';
$ophandler['object']['ipv4']['addIPv4Allocation'] = 'addIPv4Allocation';
$ophandler['object']['ipv4']['delIPv4Allocation'] = 'delIPv4Allocation';
$ophandler['object']['edit']['clearSticker'] = 'clearSticker';
$ophandler['object']['edit']['update'] = 'updateObject';
$ophandler['object']['nat4']['addNATv4Rule'] = 'addPortForwarding';
$ophandler['object']['nat4']['delNATv4Rule'] = 'delPortForwarding';
$ophandler['object']['nat4']['updNATv4Rule'] = 'updPortForwarding';
$ophandler['object']['livevlans']['setPortVLAN'] = 'setPortVLAN';
$ophandler['object']['autoports']['generate'] = 'generateAutoPorts';
$ophandler['object']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['object']['files']['addFile'] = 'addFileToEntity';
$ophandler['object']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['object']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['object']['files']['deleteFile'] = 'deleteFile';
$ophandler['object']['editrspvs']['addLB'] = 'addLoadBalancer';
$ophandler['object']['editrspvs']['delLB'] = 'deleteLoadBalancer';
$ophandler['object']['editrspvs']['updLB'] = 'updateLoadBalancer';
$ophandler['object']['lvsconfig']['submitSLBConfig'] = 'submitSLBConfig';
$ophandler['object']['snmpportfinder']['querySNMPData'] = 'querySNMPData';
$delayauth['object']['livevlans']['setPortVLAN'] = TRUE;
$msgcode['addPortForwarding']['OK'] = 2;
$msgcode['addPortForwarding']['ERR'] = 100;
$msgcode['delPortForwarding']['OK'] = 3;
$msgcode['delPortForwarding']['ERR'] = 100;
$msgcode['updPortForwarding']['OK'] = 4;
$msgcode['updPortForwarding']['ERR'] = 100;
$msgcode['addPortForObject']['OK'] = 5;
$msgcode['addPortForObject']['ERR1'] = 101;
$msgcode['addPortForObject']['ERR2'] = 100;
$msgcode['editPortForObject']['OK'] = 6;
$msgcode['editPortForObject']['ERR1'] = 101;
$msgcode['editPortForObject']['ERR2'] = 100;
$msgcode['delPortFromObject']['OK'] = 7;
$msgcode['delPortFromObject']['ERR'] = 100;
$msgcode['linkPortForObject']['OK'] = 8;
$msgcode['linkPortForObject']['ERR'] = 100;
$msgcode['unlinkPortForObject']['OK'] = 9;
$msgcode['unlinkPortForObject']['ERR'] = 100;
$msgcode['addMultiPorts']['OK'] = 10;
$msgcode['addMultiPorts']['ERR'] = 123;
$msgcode['useupPort']['OK'] = 11;
$msgcode['useupPort']['ERR'] = 124;
$msgcode['updIPv4Allocation']['OK'] = 12;
$msgcode['updIPv4Allocation']['ERR'] = 100;
$msgcode['addIPv4Allocation']['OK'] = 13;
$msgcode['addIPv4Allocation']['ERR1'] = 170;
$msgcode['addIPv4Allocation']['ERR2'] = 100;
$msgcode['delIPv4Allocation']['OK'] = 14;
$msgcode['delIPv4Allocation']['ERR'] = 100;
$msgcode['clearSticker']['OK'] = 15;
$msgcode['clearSticker']['ERR'] = 120;
$msgcode['updateObject']['OK'] = 16;
$msgcode['updateObject']['ERR'] = 121;
$msgcode['addLoadBalancer']['OK'] = 18;
$msgcode['addLoadBalancer']['ERR'] = 137;
$msgcode['delLoadBalancer']['OK'] = 19;
$msgcode['deleteLoadBalancer']['ERR'] = 129;
$msgcode['updateLoadBalancer']['OK'] = 20;
$msgcode['updateLoadBalancer']['ERR'] = 134;
$msgcode['generateAutoPorts']['OK'] = 21;
$msgcode['generateAutoPorts']['ERR'] = 142;
$msgcode['setPortVLAN']['ERR1'] = 156;
$msgcode['saveEntityTags']['OK'] = 22;
$msgcode['saveEntityTags']['ERR'] = 143;

$page['ipv4space']['title'] = 'IPv4 space';
$page['ipv4space']['parent'] = 'index';
$tab['ipv4space']['default'] = 'Browse';
$tab['ipv4space']['newrange'] = 'Manage';
$tabhandler['ipv4space']['default'] = 'renderIPv4Space';
$tabhandler['ipv4space']['newrange'] = 'renderIPv4SpaceEditor';
$ophandler['ipv4space']['newrange']['addIPv4Prefix'] = 'addIPv4Prefix';
$ophandler['ipv4space']['newrange']['delIPv4Prefix'] = 'delIPv4Prefix';
$ophandler['ipv4space']['newrange']['updIPv4Prefix'] = 'updIPv4Prefix';
$msgcode['addIPv4Prefix']['OK'] = 23;
$msgcode['addIPv4Prefix']['ERR'] = 100;
$msgcode['addIPv4Prefix']['ERR1'] = 173;
$msgcode['addIPv4Prefix']['ERR2'] = 174;
$msgcode['addIPv4Prefix']['ERR3'] = 175;
$msgcode['addIPv4Prefix']['ERR4'] = 176;
$msgcode['delIPv4Prefix']['OK'] = 24;
$msgcode['delIPv4Prefix']['ERR'] = 100;

$page['ipv4net']['title_handler'] = 'dynamic_title_ipv4net';
$page['ipv4net']['parent'] = 'ipv4space';
$page['ipv4net']['bypass'] = 'id';
$page['ipv4net']['bypass_type'] = 'uint';
$page['ipv4net']['autotagloader'] = 'loadIPv4PrefixAutoTags';
$page['ipv4net']['tagloader'] = 'loadIPv4PrefixTags';
$tab['ipv4net']['default'] = 'Browse';
$tab['ipv4net']['properties'] = 'Properties';
$tab['ipv4net']['liveptr'] = 'Live PTR';
$tab['ipv4net']['tags'] = 'Tags';
$tab['ipv4net']['files'] = 'Files';
$tabhandler['ipv4net']['default'] = 'renderIPv4Network';
$tabhandler['ipv4net']['properties'] = 'renderIPv4NetworkProperties';
$tabhandler['ipv4net']['liveptr'] = 'renderLivePTR';
$tabhandler['ipv4net']['tags'] = 'renderEntityTags';
$tabhandler['ipv4net']['files'] = 'renderFilesForEntity';
$trigger['ipv4net']['tags'] = 'trigger_tags';
$ophandler['ipv4net']['properties']['editRange'] = 'updIPv4Prefix';
$ophandler['ipv4net']['liveptr']['importPTRData'] = 'importPTRData';
$ophandler['ipv4net']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['ipv4net']['files']['addFile'] = 'addFileToEntity';
$ophandler['ipv4net']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['ipv4net']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['ipv4net']['files']['deleteFile'] = 'deleteFile';
$msgcode['updIPv4Prefix']['OK'] = 25;
$msgcode['updIPv4Prefix']['ERR'] = 100;
$msgcode['importPTRData']['OK'] = 26;
$msgcode['importPTRData']['ERR'] = 141;

$page['ipaddress']['title_handler'] = 'dynamic_title_ipaddress';
$page['ipaddress']['parent'] = 'ipv4net';
$page['ipaddress']['bypass'] = 'ip';
$page['ipaddress']['bypass_type'] = 'inet4';
$page['ipaddress']['autotagloader'] = 'loadIPv4AddressAutoTags';
$tab['ipaddress']['default'] = 'Browse';
$tab['ipaddress']['properties'] = 'Properties';
$tab['ipaddress']['assignment'] = 'Allocation';
$tab['ipaddress']['editrslist'] = '[SLB real servers]';
$tabhandler['ipaddress']['default'] = 'renderIPv4Address';
$tabhandler['ipaddress']['properties'] = 'renderIPv4AddressProperties';
$tabhandler['ipaddress']['assignment'] = 'renderIPv4AddressAllocations';
$tabhandler['ipaddress']['editrslist'] = 'dragon';
$ophandler['ipaddress']['properties']['editAddress'] = 'editAddress';
$ophandler['ipaddress']['assignment']['delIPv4Allocation'] = 'delIPv4Allocation';
$ophandler['ipaddress']['assignment']['updIPv4Allocation'] = 'updIPv4Allocation';
$ophandler['ipaddress']['assignment']['addIPv4Allocation'] = 'addIPv4Allocation';
$msgcode['editAddress']['OK'] = 27;
$msgcode['editAddress']['ERR'] = 100;

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
$msgcode['addVService']['OK'] = 28;
$msgcode['addVService']['ERR1'] = 132;
$msgcode['addVService']['ERR2'] = 100;
$msgcode['deleteVService']['OK'] = 29;
$msgcode['deleteVService']['ERR'] = 130;
$msgcode['updateVService']['OK'] = 30;
$msgcode['updateVService']['ERR'] = 135;

$page['ipv4vs']['title_handler'] = 'dynamic_title_vservice';
$page['ipv4vs']['parent'] = 'ipv4vslist';
$page['ipv4vs']['bypass'] = 'vs_id';
$page['ipv4vs']['bypass_type'] = 'uint';
$page['ipv4vs']['tagloader'] = 'loadIPv4VSTags';
$page['ipv4vs']['autotagloader'] = 'loadIPv4VSAutoTags';
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
$ophandler['ipv4vs']['editlblist']['delLB'] = 'deleteLoadBalancer';
$ophandler['ipv4vs']['editlblist']['updLB'] = 'updateLoadBalancer';
$ophandler['ipv4vs']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['ipv4vs']['files']['addFile'] = 'addFileToEntity';
$ophandler['ipv4vs']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['ipv4vs']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['ipv4vs']['files']['deleteFile'] = 'deleteFile';

$page['ipv4rsplist']['title'] = 'RS pools';
$page['ipv4rsplist']['parent'] = 'ipv4slb';
$tab['ipv4rsplist']['default'] = 'View';
$tab['ipv4rsplist']['edit'] = 'Edit';
$tabhandler['ipv4rsplist']['default'] = 'renderRSPoolList';
$tabhandler['ipv4rsplist']['edit'] = 'editRSPools';
$ophandler['ipv4rsplist']['edit']['add'] = 'addRSPool';
$ophandler['ipv4rsplist']['edit']['del'] = 'deleteRSPool';
$ophandler['ipv4rsplist']['edit']['upd'] = 'updateRSPool';
$msgcode['addRSPool']['OK'] = 31;
$msgcode['addRSPool']['ERR'] = 100;
$msgcode['deleteRSPool']['OK'] = 32;
$msgcode['deleteRSPool']['ERR'] = 138;
$msgcode['updateRSPool']['OK'] = 33;
$msgcode['updateRSPool']['ERR'] = 139;

$page['ipv4rspool']['title_handler'] = 'dynamic_title_rspool';
$page['ipv4rspool']['parent'] = 'ipv4rsplist';
$page['ipv4rspool']['bypass'] = 'pool_id';
$page['ipv4rspool']['bypass_type'] = 'uint';
$page['ipv4rspool']['tagloader'] = 'loadIPv4RSPoolTags';
$page['ipv4rspool']['autotagloader'] = 'loadIPv4RSPoolAutoTags';
$tab['ipv4rspool']['default'] = 'View';
$tab['ipv4rspool']['edit'] = 'Edit';
$tab['ipv4rspool']['editlblist'] = 'Load Balancers';
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
$ophandler['ipv4rspool']['edit']['updIPv4RSP'] = 'updateRSPool';
$ophandler['ipv4rspool']['editrslist']['addRS'] = 'addRealServer';
$ophandler['ipv4rspool']['editrslist']['delRS'] = 'deleteRealServer';
$ophandler['ipv4rspool']['editrslist']['updRS'] = 'updateRealServer';
$ophandler['ipv4rspool']['editrslist']['addMany'] = 'addRealServers';
$ophandler['ipv4rspool']['editlblist']['addLB'] = 'addLoadBalancer';
$ophandler['ipv4rspool']['editlblist']['delLB'] = 'deleteLoadBalancer';
$ophandler['ipv4rspool']['editlblist']['updLB'] = 'updateLoadBalancer';
$ophandler['ipv4rspool']['rsinservice']['upd'] = 'updateRSInService';
$ophandler['ipv4rspool']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['ipv4rspool']['files']['addFile'] = 'addFileToEntity';
$ophandler['ipv4rspool']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['ipv4rspool']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['ipv4rspool']['files']['deleteFile'] = 'deleteFile';
$msgcode['addRealServer']['OK'] = 34;
$msgcode['addRealServer']['ERR'] = 126;
$msgcode['deleteRealServer']['OK'] = 35;
$msgcode['deleteRealServer']['ERR'] = 128;
$msgcode['updateRealServer']['OK'] = 36;
$msgcode['updateRealServer']['ERR'] = 133;
$msgcode['addRealServers']['OK'] = 37;
$msgcode['addRealServers']['ERR1'] = 131;
$msgcode['addRealServers']['ERR2'] = 127;
$msgcode['updateRSInService']['OK'] = 38;
$msgcode['updateRSInService']['ERR'] = 140;

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
$msgcode['updateUser']['OK'] = 39;
$msgcode['updateUser']['ERR1'] = 103;
$msgcode['updateUser']['ERR1'] = 104;
$msgcode['createUser']['OK'] = 40;
$msgcode['createUser']['ERR'] = 102;

$page['user']['title_handler'] = 'dynamic_title_user';
$page['user']['parent'] = 'userlist';
$page['user']['bypass'] = 'user_id';
$page['user']['bypass_type'] = 'uint';
$page['user']['tagloader'] = 'loadUserTags';
$page['user']['autotagloader'] = 'getUserAutoTags';
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
$ophandler['user']['files']['deleteFile'] = 'deleteFile';

$page['perms']['title'] = 'Permissions';
$page['perms']['parent'] = 'config';
$tab['perms']['default'] = 'View';
$tab['perms']['edit'] = 'Edit';
$tabhandler['perms']['default'] = 'renderRackCodeViewer';
$tabhandler['perms']['edit'] = 'renderRackCodeEditor';
$ophandler['perms']['edit']['saveRackCode'] = 'saveRackCode';
$msgcode['saveRackCode']['OK'] = 43;
$msgcode['saveRackCode']['ERR1'] = 154;
$msgcode['saveRackCode']['ERR2'] = 155;

$page['portmap']['title'] = 'Port compatibility map';
$page['portmap']['parent'] = 'config';
$tab['portmap']['default'] = 'View';
$tab['portmap']['edit'] = 'Change';
$tabhandler['portmap']['default'] = 'renderPortMapViewer';
$tabhandler['portmap']['edit'] = 'renderPortMapEditor';
$ophandler['portmap']['edit']['save'] = 'savePortMap';
$msgcode['savePortMap']['OK'] = 44;
$msgcode['savePortMap']['ERR'] = 108;

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
$msgcode['createAttribute']['OK'] = 45;
$msgcode['createAttribute']['ERR'] = 116;
$msgcode['changeAttribute']['OK'] = 46;
$msgcode['changeAttribute']['ERR'] = 115;
$msgcode['deleteAttribute']['OK'] = 47;
$msgcode['deleteAttribute']['ERR'] = 117;
$msgcode['supplementAttrMap']['OK'] = 48;
$msgcode['supplementAttrMap']['ERR'] = 118;
$msgcode['reduceAttrMap']['OK'] = 49;
$msgcode['reduceAttrMap']['ERR'] = 119;

$page['dict']['title'] = 'Dictionary';
$page['dict']['parent'] = 'config';
$tab['dict']['default'] = 'View';
$tab['dict']['chapters'] = 'Manage chapters';
$tabhandler['dict']['default'] = 'renderDictionary';
$tabhandler['dict']['chapters'] = 'renderChaptersEditor';
$ophandler['dict']['chapters']['del'] = 'delChapter';
$ophandler['dict']['chapters']['upd'] = 'updateChapter';
$ophandler['dict']['chapters']['add'] = 'addChapter';
$msgcode['delChapter']['OK'] = 53;
$msgcode['delChapter']['ERR'] = 114;
$msgcode['updateChapter']['OK'] = 54;
$msgcode['updateChapter']['ERR'] = 113;
$msgcode['addChapter']['OK'] = 55;
$msgcode['addChapter']['ERR'] = 112;

$page['chapter']['title_handler'] = 'dynamic_title_chapter';
$page['chapter']['parent'] = 'dict';
$page['chapter']['bypass'] = 'chapter_no';
$page['chapter']['bypass_type'] = 'uint';
$tab['chapter']['default'] = 'View';
$tab['chapter']['edit'] = 'Edit';
$tabhandler['chapter']['default'] = 'renderChapter';
$tabhandler['chapter']['edit'] = 'renderChapterEditor';
$ophandler['chapter']['edit']['del'] = 'reduceDictionary';
$ophandler['chapter']['edit']['upd'] = 'updateDictionary';
$ophandler['chapter']['edit']['add'] = 'supplementDictionary';
$msgcode['reduceDictionary']['OK'] = 50;
$msgcode['reduceDictionary']['ERR'] = 111;
$msgcode['updateDictionary']['OK'] = 51;
$msgcode['updateDictionary']['ERR'] = 109;
$msgcode['supplementDictionary']['OK'] = 52;
$msgcode['supplementDictionary']['ERR'] = 110;

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
$msgcode['updateUI']['OK'] = 56;
$msgcode['updateUI']['ERR'] = 125;
$msgcode['resetUIConfig']['OK'] = 57;

$page['tagtree']['title'] = 'Tag tree';
$page['tagtree']['parent'] = 'config';
$tab['tagtree']['default'] = 'View';
$tab['tagtree']['edit'] = 'Edit';
$tabhandler['tagtree']['default'] = 'renderTagTree';
$tabhandler['tagtree']['edit'] = 'renderTagTreeEditor';
$ophandler['tagtree']['edit']['destroyTag'] = 'destroyTag';
$ophandler['tagtree']['edit']['createTag'] = 'createTag';
$ophandler['tagtree']['edit']['updateTag'] = 'updateTag';
$msgcode['destroyTag']['OK'] = 58;
$msgcode['destroyTag']['ERR'] = 144;
$msgcode['createTag']['OK'] = 59;
$msgcode['createTag']['ERR1'] = 145;
$msgcode['createTag']['ERR2'] = 146;
$msgcode['createTag']['ERR3'] = 147;
$msgcode['updateTag']['OK'] = 60;
$msgcode['updateTag']['ERR1'] = 145;
$msgcode['updateTag']['ERR2'] = 148;

$page['myaccount']['title'] = 'My account';
$page['myaccount']['parent'] = 'config';
$tab['myaccount']['default'] = 'Info';
$tab['myaccount']['mypassword'] = 'Password change';
$tab['myaccount']['myrealname'] = '[Real name change]';
$trigger['myaccount']['mypassword'] = 'trigger_passwdchange';
$tabhandler['myaccount']['default'] = 'renderMyAccount';
$tabhandler['myaccount']['mypassword'] = 'renderMyPasswordEditor';
$tabhandler['myaccount']['myrealname'] = 'dragon';
$ophandler['myaccount']['mypassword']['changeMyPassword'] = 'changeMyPassword';
$msgcode['changeMyPassword']['OK'] = 61;
$msgcode['changeMyPassword']['ERR1'] = 150;
$msgcode['changeMyPassword']['ERR2'] = 151;
$msgcode['changeMyPassword']['ERR3'] = 152;
$msgcode['changeMyPassword']['ERR4'] = 153;

$page['reports']['title'] = 'Reports';
$page['reports']['parent'] = 'index';
$tab['reports']['default'] = 'System';
$tab['reports']['rackcode'] = 'RackCode';
$tab['reports']['ipv4'] = 'IPv4';
$tab['reports']['local'] = 'Local';
$trigger['reports']['local'] = 'trigger_localreports';
$tabhandler['reports']['default'] = 'renderSystemReports';
$tabhandler['reports']['rackcode'] = 'renderRackCodeReports';
$tabhandler['reports']['ipv4'] = 'renderIPv4Reports';
$tabhandler['reports']['local'] = 'renderLocalReports';

$page['files']['title'] = 'Files';
$page['files']['parent'] = 'index';
$tab['files']['default'] = 'View';
$tab['files']['addmore'] = 'Upload';
$tabhandler['files']['default'] = 'renderFileSpace';
$tabhandler['files']['addmore'] = 'renderFileUploadForm';
$ophandler['files']['addmore']['addFile'] = 'addFileWithoutLink';
$msgcode['addFileWithoutLink']['OK'] = 69;
$msgcode['addFileWithoutLink']['ERR'] = 100;

$page['filesbylink']['title_handler'] = 'dynamic_title_file';
$page['filesbylink']['handler'] = 'renderFilesByLink'; //renderObjectGroup
$page['filesbylink']['bypass'] = 'entity_type';
$page['filesbylink']['bypass_type'] = 'string';
$page['filesbylink']['parent'] = 'files';
$ophandler['filesbylink']['default']['deleteFile'] = 'deleteFile';

$page['file']['title_handler'] = 'dynamic_title_file';
$page['file']['bypass'] = 'file_id';
$page['file']['bypass_type'] = 'uint';
$page['file']['parent'] = 'files';
$page['file']['tagloader'] = 'loadFileTags';
$page['file']['autotagloader'] = 'loadFileAutoTags';
$tab['file']['default'] = 'View';
$tab['file']['edit'] = 'Properties';
$tab['file']['tags'] = 'Tags';
$trigger['file']['tags'] = 'trigger_tags';
$tabhandler['file']['default'] = 'renderFile';
$tabhandler['file']['edit'] = 'renderFileProperties';
$tabhandler['file']['tags'] = 'renderEntityTags';
$ophandler['file']['default']['replaceFile'] = 'replaceFile';
$ophandler['file']['edit']['updateFile'] = 'updateFile';
$ophandler['file']['tags']['saveTags'] = 'saveEntityTags';

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
		echo ": <a href='${root}?page=${no}&tab=default";
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
			$ok = $trigger[$pageno][$tabidx] ();
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
			$cpage['parent'] == $pageno
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
