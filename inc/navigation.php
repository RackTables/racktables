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
$msgcode['row']['tagroller']['rollTags']['OK'] = 1;
$msgcode['row']['tagroller']['rollTags']['ERR'] = 149;

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
$tab['object']['ipv4'] = 'IPv4';
$tab['object']['nat4'] = 'NATv4';
$tab['object']['livevlans'] = 'Live VLANs';
$tab['object']['snmpportfinder'] = 'SNMP port finder';
$tab['object']['editrspvs'] = 'RS pools';
$tab['object']['lvsconfig'] = 'LVS configuration';
$tab['object']['autoports'] = 'AutoPorts';
$tab['object']['tags'] = 'Tags';
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
$tabhandler['object']['tags'] = 'renderObjectTags';
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
$ophandler['object']['edit']['updateStickers'] = 'updateStickers';
$ophandler['object']['edit']['update'] = 'updateObject';
$ophandler['object']['nat4']['addNATv4Rule'] = 'addPortForwarding';
$ophandler['object']['nat4']['delNATv4Rule'] = 'delPortForwarding';
$ophandler['object']['nat4']['updNATv4Rule'] = 'updPortForwarding';
$ophandler['object']['livevlans']['setPortVLAN'] = 'setPortVLAN';
$ophandler['object']['autoports']['generate'] = 'generateAutoPorts';
$ophandler['object']['tags']['saveTags'] = 'saveObjectTags';
$ophandler['object']['editrspvs']['addLB'] = 'addLoadBalancer';
$ophandler['object']['editrspvs']['delLB'] = 'deleteLoadBalancer';
$ophandler['object']['editrspvs']['updLB'] = 'updateLoadBalancer';
$ophandler['object']['lvsconfig']['submitSLBConfig'] = 'submitSLBConfig';
$delayauth['object']['livevlans']['setPortVLAN'] = TRUE;
$msgcode['object']['nat4']['addNATv4Rule']['OK'] = 2;
$msgcode['object']['nat4']['addNATv4Rule']['ERR'] = 100;
$msgcode['object']['nat4']['delNATv4Rule']['OK'] = 3;
$msgcode['object']['nat4']['delNATv4Rule']['ERR'] = 100;
$msgcode['object']['nat4']['updNATv4Rule']['OK'] = 4;
$msgcode['object']['nat4']['updNATv4Rule']['ERR'] = 100;
$msgcode['object']['ports']['addPort']['OK'] = 5;
$msgcode['object']['ports']['addPort']['ERR1'] = 101;
$msgcode['object']['ports']['addPort']['ERR2'] = 100;
$msgcode['object']['ports']['editPort']['OK'] = 6;
$msgcode['object']['ports']['editPort']['ERR1'] = 101;
$msgcode['object']['ports']['editPort']['ERR2'] = 100;
$msgcode['object']['ports']['delPort']['OK'] = 7;
$msgcode['object']['ports']['delPort']['ERR'] = 100;
$msgcode['object']['ports']['linkPort']['OK'] = 8;
$msgcode['object']['ports']['linkPort']['ERR'] = 100;
$msgcode['object']['ports']['unlinkPort']['OK'] = 9;
$msgcode['object']['ports']['unlinkPort']['ERR'] = 100;
$msgcode['object']['ports']['addMultiPorts']['OK'] = 10;
$msgcode['object']['ports']['addMultiPorts']['ERR'] = 123;
$msgcode['object']['ports']['useup']['OK'] = 11;
$msgcode['object']['ports']['useup']['ERR'] = 124;
$msgcode['object']['ipv4']['updIPv4Allocation']['OK'] = 12;
$msgcode['object']['ipv4']['updIPv4Allocation']['ERR'] = 100;
$msgcode['object']['ipv4']['addIPv4Allocation']['OK'] = 13;
$msgcode['object']['ipv4']['addIPv4Allocation']['ERR1'] = 170;
$msgcode['object']['ipv4']['addIPv4Allocation']['ERR2'] = 100;
$msgcode['object']['ipv4']['delIPv4Allocation']['OK'] = 14;
$msgcode['object']['ipv4']['delIPv4Allocation']['ERR'] = 100;
$msgcode['object']['edit']['clearSticker']['OK'] = 15;
$msgcode['object']['edit']['clearSticker']['ERR'] = 120;
$msgcode['object']['edit']['update']['OK'] = 16;
$msgcode['object']['edit']['update']['ERR'] = 121;
$msgcode['object']['edit']['updateStickers']['OK'] = 17;
$msgcode['object']['edit']['updateStickers']['ERR'] = 122;
$msgcode['object']['editrspvs']['addLB']['OK'] = 18;
$msgcode['object']['editrspvs']['addLB']['ERR'] = 137;
$msgcode['object']['editrspvs']['delLB']['OK'] = 19;
$msgcode['object']['editrspvs']['delLB']['ERR'] = 129;
$msgcode['object']['editrspvs']['updLB']['OK'] = 20;
$msgcode['object']['editrspvs']['updLB']['ERR'] = 134;
$msgcode['object']['autoports']['generate']['OK'] = 21;
$msgcode['object']['autoports']['generate']['ERR'] = 142;
$msgcode['object']['tags']['saveTags']['OK'] = 22;
$msgcode['object']['tags']['saveTags']['ERR'] = 143;
$msgcode['object']['livevlans']['setPortVLAN']['ERR1'] = 156;
$msgcode['rack']['tags']['saveTags'] = $msgcode['object']['tags']['saveTags'];
$msgcode['iprange']['tags']['saveTags'] = $msgcode['object']['tags']['saveTags'];
$msgcode['ipv4vs']['tags']['saveTags'] = $msgcode['object']['tags']['saveTags'];
$msgcode['ipv4rsp']['tags']['saveTags'] = $msgcode['object']['tags']['saveTags'];
$msgcode['user']['tags']['saveTags'] = $msgcode['object']['tags']['saveTags'];
$msgcode['ipv4vs']['editlblist'] = $msgcode['object']['editrspvs'];
$msgcode['ipv4rsp']['editlblist'] = $msgcode['object']['editrspvs'];
$msgcode['ipaddress']['assignment'] = $msgcode['object']['ipv4'];

$page['ipv4space']['title'] = 'IPv4 space';
$page['ipv4space']['parent'] = 'index';
$tab['ipv4space']['default'] = 'Browse';
$tab['ipv4space']['newrange'] = 'Manage';
$tabhandler['ipv4space']['default'] = 'renderAddressspace';
$tabhandler['ipv4space']['newrange'] = 'renderAddNewRange';
$ophandler['ipv4space']['newrange']['addIPv4Prefix'] = 'addIPv4Prefix';
$ophandler['ipv4space']['newrange']['delIPv4Prefix'] = 'delIPv4Prefix';
$msgcode['ipv4space']['newrange']['addIPv4Prefix']['OK'] = 23;
$msgcode['ipv4space']['newrange']['addIPv4Prefix']['ERR'] = 100;
$msgcode['ipv4space']['newrange']['delIPv4Prefix']['OK'] = 24;
$msgcode['ipv4space']['newrange']['delIPv4Prefix']['ERR'] = 100;

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
$msgcode['iprange']['properties']['editRange']['OK'] = 25;
$msgcode['iprange']['properties']['editRange']['ERR'] = 100;
$msgcode['iprange']['liveptr']['importPTRData']['OK'] = 26;
$msgcode['iprange']['liveptr']['importPTRData']['ERR'] = 141;

$page['ipaddress']['title_handler'] = 'dynamic_title_ipaddress';
$page['ipaddress']['parent'] = 'iprange';
$page['ipaddress']['bypass'] = 'ip';
$page['ipaddress']['bypass_type'] = 'inet4';
$page['ipaddress']['autotagloader'] = 'loadIPv4AddressAutoTags';
$tab['ipaddress']['default'] = 'Browse';
$tab['ipaddress']['properties'] = 'Properties';
$tab['ipaddress']['assignment'] = 'Allocation';
$tab['ipaddress']['editrslist'] = '[SLB real servers]';
$tabhandler['ipaddress']['default'] = 'renderIPAddress';
$tabhandler['ipaddress']['properties'] = 'renderIPAddressProperties';
$tabhandler['ipaddress']['assignment'] = 'renderIPAddressAssignment';
$tabhandler['ipaddress']['editrslist'] = 'dragon';
$ophandler['ipaddress']['properties']['editAddress'] = 'editAddress';
$ophandler['ipaddress']['assignment']['delIPv4Allocation'] = 'delIPv4Allocation';
$ophandler['ipaddress']['assignment']['updIPv4Allocation'] = 'updIPv4Allocation';
$ophandler['ipaddress']['assignment']['addIPv4Allocation'] = 'addIPv4Allocation';
$msgcode['ipaddress']['properties']['editAddress']['OK'] = 27;
$msgcode['ipaddress']['properties']['editAddress']['ERR'] = 100;

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
$msgcode['ipv4vslist']['edit']['add']['OK'] = 28;
$msgcode['ipv4vslist']['edit']['add']['ERR1'] = 132;
$msgcode['ipv4vslist']['edit']['add']['ERR2'] = 100;
$msgcode['ipv4vslist']['edit']['del']['OK'] = 29;
$msgcode['ipv4vslist']['edit']['del']['ERR'] = 130;
$msgcode['ipv4vslist']['edit']['upd']['OK'] = 30;
$msgcode['ipv4vslist']['edit']['upd']['ERR'] = 135;
$msgcode['ipv4vs']['edit']['updIPv4VS'] = $msgcode['ipv4vslist']['edit']['upd'];

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
$tabhandler['ipv4vs']['default'] = 'renderVirtualService';
$tabhandler['ipv4vs']['edit'] = 'renderEditVService';
$tabhandler['ipv4vs']['editlblist'] = 'renderVServiceLBForm';
$tabhandler['ipv4vs']['tags'] = 'renderIPv4VSTags';
$ophandler['ipv4vs']['edit']['updIPv4VS'] = 'updateVService';
$ophandler['ipv4vs']['tags']['saveTags'] = 'saveIPv4VSTags';
$ophandler['ipv4vs']['editlblist']['addLB'] = 'addLoadBalancer';
$ophandler['ipv4vs']['editlblist']['delLB'] = 'deleteLoadBalancer';
$ophandler['ipv4vs']['editlblist']['updLB'] = 'updateLoadBalancer';
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
$msgcode['ipv4rsplist']['edit']['add']['OK'] = 31;
$msgcode['ipv4rsplist']['edit']['add']['ERR'] = 100;
$msgcode['ipv4rsplist']['edit']['del']['OK'] = 32;
$msgcode['ipv4rsplist']['edit']['del']['ERR'] = 138;
$msgcode['ipv4rsplist']['edit']['upd']['OK'] = 33;
$msgcode['ipv4rsplist']['edit']['upd']['ERR'] = 139;
$msgcode['ipv4rsp']['edit']['updIPv4RSP'] = $msgcode['ipv4rsplist']['edit']['upd'];

$page['ipv4rsp']['title_handler'] = 'dynamic_title_rspool';
$page['ipv4rsp']['parent'] = 'ipv4rsplist';
$page['ipv4rsp']['bypass'] = 'pool_id';
$page['ipv4rsp']['bypass_type'] = 'uint';
$page['ipv4rsp']['tagloader'] = 'loadIPv4RSPoolTags';
$page['ipv4rsp']['autotagloader'] = 'loadIPv4RSPoolAutoTags';
$tab['ipv4rsp']['default'] = 'View';
$tab['ipv4rsp']['edit'] = 'Edit';
$tab['ipv4rsp']['editlblist'] = 'Load Balancers';
$tab['ipv4rsp']['editrslist'] = 'RS list';
$tab['ipv4rsp']['rsinservice'] = 'RS in service';
$tab['ipv4rsp']['tags'] = 'Tags';
$trigger['ipv4rsp']['rsinservice'] = 'trigger_poolrscount';
$trigger['ipv4rsp']['tags'] = 'trigger_tags';
$tabhandler['ipv4rsp']['default'] = 'renderRSPool';
$tabhandler['ipv4rsp']['edit'] = 'renderEditRSPool';
$tabhandler['ipv4rsp']['editrslist'] = 'renderRSPoolServerForm';
$tabhandler['ipv4rsp']['editlblist'] = 'renderRSPoolLBForm';
$tabhandler['ipv4rsp']['rsinservice'] = 'renderRSPoolRSInServiceForm';
$tabhandler['ipv4rsp']['tags'] = 'renderIPv4RSPoolTags';
$ophandler['ipv4rsp']['editrslist']['addRS'] = 'addRealServer';
$ophandler['ipv4rsp']['editrslist']['delRS'] = 'deleteRealServer';
$ophandler['ipv4rsp']['editrslist']['updRS'] = 'updateRealServer';
$ophandler['ipv4rsp']['editrslist']['addMany'] = 'addRealServers';
$ophandler['ipv4rsp']['editlblist']['addLB'] = 'addLoadBalancer';
$ophandler['ipv4rsp']['editlblist']['delLB'] = 'deleteLoadBalancer';
$ophandler['ipv4rsp']['editlblist']['updLB'] = 'updateLoadBalancer';
$ophandler['ipv4rsp']['rsinservice']['upd'] = 'updateRSInService';
$ophandler['ipv4rsp']['tags']['saveTags'] = 'saveIPv4RSPoolTags';
$ophandler['ipv4rsp']['edit']['updIPv4RSP'] = 'updateRSPool';
$msgcode['ipv4rsp']['editrslist']['addRS']['OK'] = 34;
$msgcode['ipv4rsp']['editrslist']['addRS']['ERR'] = 126;
$msgcode['ipv4rsp']['editrslist']['delRS']['OK'] = 35;
$msgcode['ipv4rsp']['editrslist']['delRS']['ERR'] = 128;
$msgcode['ipv4rsp']['editrslist']['updRS']['OK'] = 36;
$msgcode['ipv4rsp']['editrslist']['updRS']['ERR'] = 133;
$msgcode['ipv4rsp']['editrslist']['addMany']['OK'] = 37;
$msgcode['ipv4rsp']['editrslist']['addMany']['ERR1'] = 131;
$msgcode['ipv4rsp']['editrslist']['addMany']['ERR2'] = 127;
$msgcode['ipv4rsp']['rsinservice']['upd']['OK'] = 38;
$msgcode['ipv4rsp']['rsinservice']['upd']['ERR'] = 140;

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
$msgcode['userlist']['edit']['updateUser']['OK'] = 39;
$msgcode['userlist']['edit']['updateUser']['ERR1'] = 103;
$msgcode['userlist']['edit']['updateUser']['ERR1'] = 104;
$msgcode['userlist']['edit']['createUser']['OK'] = 40;
$msgcode['userlist']['edit']['createUser']['ERR'] = 102;
$msgcode['userlist']['edit']['disableUser']['OK'] = 41;
$msgcode['userlist']['edit']['disableUser']['ERR1'] = 107;
$msgcode['userlist']['edit']['disableUser']['ERR2'] = 106;
$msgcode['userlist']['edit']['enableUser']['OK'] = 42;
$msgcode['userlist']['edit']['enableUser']['ERR'] = 105;

$page['user']['title_handler'] = 'dynamic_title_user';
$page['user']['parent'] = 'userlist';
$page['user']['bypass'] = 'user_id';
$page['user']['bypass_type'] = 'uint';
$page['user']['tagloader'] = 'loadUserTags';
$page['user']['autotagloader'] = 'getUserAutoTags';
$tab['user']['default'] = 'View';
$tab['user']['tags'] = 'Tags';
$tabhandler['user']['default'] = 'renderUser';
$tabhandler['user']['tags'] = 'renderUserTags';
$ophandler['user']['tags']['saveTags'] = 'saveUserTags';

$page['perms']['title'] = 'Permissions';
$page['perms']['parent'] = 'config';
$tab['perms']['default'] = 'View';
$tab['perms']['edit'] = 'Edit';
$tabhandler['perms']['default'] = 'renderRackCodeViewer';
$tabhandler['perms']['edit'] = 'renderRackCodeEditor';
$ophandler['perms']['edit']['saveRackCode'] = 'saveRackCode';
$msgcode['perms']['edit']['saveRackCode']['OK'] = 43;
$msgcode['perms']['edit']['saveRackCode']['ERR1'] = 154;
$msgcode['perms']['edit']['saveRackCode']['ERR2'] = 155;

$page['portmap']['title'] = 'Port compatibility map';
$page['portmap']['parent'] = 'config';
$tab['portmap']['default'] = 'View';
$tab['portmap']['edit'] = 'Change';
$tabhandler['portmap']['default'] = 'renderPortMapViewer';
$tabhandler['portmap']['edit'] = 'renderPortMapEditor';
$ophandler['portmap']['edit']['save'] = 'savePortMap';
$msgcode['portmap']['edit']['save']['OK'] = 44;
$msgcode['portmap']['edit']['save']['ERR'] = 108;

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
$msgcode['attrs']['editattrs']['add']['OK'] = 45;
$msgcode['attrs']['editattrs']['add']['ERR'] = 116;
$msgcode['attrs']['editattrs']['upd']['OK'] = 46;
$msgcode['attrs']['editattrs']['upd']['ERR'] = 115;
$msgcode['attrs']['editattrs']['del']['OK'] = 47;
$msgcode['attrs']['editattrs']['del']['ERR'] = 117;
$msgcode['attrs']['editmap']['add']['OK'] = 48;
$msgcode['attrs']['editmap']['add']['ERR'] = 118;
$msgcode['attrs']['editmap']['del']['OK'] = 49;
$msgcode['attrs']['editmap']['del']['ERR'] = 119;

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
$msgcode['dict']['edit']['del']['OK'] = 50;
$msgcode['dict']['edit']['del']['ERR'] = 111;
$msgcode['dict']['edit']['upd']['OK'] = 51;
$msgcode['dict']['edit']['upd']['ERR'] = 109;
$msgcode['dict']['edit']['add']['OK'] = 52;
$msgcode['dict']['edit']['add']['ERR'] = 110;
$msgcode['dict']['chapters']['del']['OK'] = 53;
$msgcode['dict']['chapters']['del']['ERR'] = 114;
$msgcode['dict']['chapters']['upd']['OK'] = 54;
$msgcode['dict']['chapters']['upd']['ERR'] = 113;
$msgcode['dict']['chapters']['add']['OK'] = 55;
$msgcode['dict']['chapters']['add']['ERR'] = 112;

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
$msgcode['ui']['edit']['upd']['OK'] = 56;
$msgcode['ui']['edit']['upd']['ERR'] = 125;
$msgcode['ui']['reset']['go']['OK'] = 57;

$page['tagtree']['title'] = 'Tag tree';
$page['tagtree']['parent'] = 'config';
$tab['tagtree']['default'] = 'View';
$tab['tagtree']['edit'] = 'Edit';
$tabhandler['tagtree']['default'] = 'renderTagTree';
$tabhandler['tagtree']['edit'] = 'renderTagTreeEditor';
$ophandler['tagtree']['edit']['destroyTag'] = 'destroyTag';
$ophandler['tagtree']['edit']['createTag'] = 'createTag';
$ophandler['tagtree']['edit']['updateTag'] = 'updateTag';
$msgcode['tagtree']['edit']['destroyTag']['OK'] = 58;
$msgcode['tagtree']['edit']['destroyTag']['ERR'] = 144;
$msgcode['tagtree']['edit']['createTag']['OK'] = 59;
$msgcode['tagtree']['edit']['createTag']['ERR1'] = 145;
$msgcode['tagtree']['edit']['createTag']['ERR2'] = 146;
$msgcode['tagtree']['edit']['createTag']['ERR3'] = 147;
$msgcode['tagtree']['edit']['updateTag']['OK'] = 60;
$msgcode['tagtree']['edit']['updateTag']['ERR1'] = 145;
$msgcode['tagtree']['edit']['updateTag']['ERR2'] = 148;

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
$msgcode['myaccount']['mypassword']['changeMyPassword']['OK'] = 61;
$msgcode['myaccount']['mypassword']['changeMyPassword']['ERR1'] = 150;
$msgcode['myaccount']['mypassword']['changeMyPassword']['ERR2'] = 151;
$msgcode['myaccount']['mypassword']['changeMyPassword']['ERR3'] = 152;
$msgcode['myaccount']['mypassword']['changeMyPassword']['ERR4'] = 153;

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
