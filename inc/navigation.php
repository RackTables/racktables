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
$ophandler['row']['newrack']['addRack'] = 'addRack';
$msgcode['row']['tagroller']['rollTags']['OK'] = 67;
$msgcode['row']['tagroller']['rollTags']['ERR'] = 149;
$msgcode['row']['newrack']['addRack']['OK'] = 65;
$msgcode['row']['newrack']['addRack']['ERR1'] = 171;
$msgcode['row']['newrack']['addRack']['ERR2'] = 172;

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
$tabhandler['rack']['tags'] = 'renderRackTags';
$tabhandler['rack']['files'] = 'renderFilesForRack';
$trigger['rack']['tags'] = 'trigger_tags';
$ophandler['rack']['edit']['updateRack'] = 'updateRack';
$ophandler['rack']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['rack']['files']['addFile'] = 'addFileToEntity';
$ophandler['rack']['files']['updateFile'] = 'updateFile';
$ophandler['rack']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['rack']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['rack']['files']['deleteFile'] = 'deleteFile';
$msgcode['rack']['edit']['updateRack']['OK'] = 68;
$msgcode['rack']['edit']['updateRack']['ERR'] = 177;
$msgcode['rack']['files']['addFile']['OK'] = 69;
$msgcode['rack']['files']['addFile']['ERR'] = 100;
$msgcode['rack']['files']['updateFile']['OK'] = 70;
$msgcode['rack']['files']['updateFile']['ERR'] = 100;
$msgcode['rack']['files']['linkFile']['OK'] = 71;
$msgcode['rack']['files']['linkFile']['ERR'] = 100;
$msgcode['rack']['files']['unlinkFile']['OK'] = 72;
$msgcode['rack']['files']['unlinkFile']['ERR'] = 100;
$msgcode['rack']['files']['deleteFile']['OK'] = 73;
$msgcode['rack']['files']['deleteFile']['ERR'] = 100;

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
$tabhandler['object']['tags'] = 'renderObjectTags';
$tabhandler['object']['files'] = 'renderFilesForObject';
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
$ophandler['object']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['object']['files']['addFile'] = 'addFileToEntity';
$ophandler['object']['files']['updateFile'] = 'updateFile';
$ophandler['object']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['object']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['object']['files']['deleteFile'] = 'deleteFile';
$ophandler['object']['editrspvs']['addLB'] = 'addLoadBalancer';
$ophandler['object']['editrspvs']['delLB'] = 'deleteLoadBalancer';
$ophandler['object']['editrspvs']['updLB'] = 'updateLoadBalancer';
$ophandler['object']['lvsconfig']['submitSLBConfig'] = 'submitSLBConfig';
$ophandler['object']['snmpportfinder']['querySNMPData'] = 'querySNMPData';
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
$msgcode['object']['livevlans']['setPortVLAN']['ERR1'] = 156;
$msgcode['object']['tags']['saveTags']['OK'] = 22;
$msgcode['object']['tags']['saveTags']['ERR'] = 143;
$msgcode['object']['files']['addFile']['OK'] = $msgcode['rack']['files']['addFile']['OK'];
$msgcode['object']['files']['addFile']['ERR'] = $msgcode['rack']['files']['addFile']['ERR'];
$msgcode['object']['files']['updateFile']['OK'] = $msgcode['rack']['files']['updateFile']['OK'];
$msgcode['object']['files']['updateFile']['ERR'] = $msgcode['rack']['files']['updateFile']['ERR'];
$msgcode['object']['files']['linkFile']['OK'] = $msgcode['rack']['files']['linkFile']['OK'];
$msgcode['object']['files']['linkFile']['ERR'] = $msgcode['rack']['files']['linkFile']['ERR'];
$msgcode['object']['files']['unlinkFile']['OK'] = $msgcode['rack']['files']['unlinkFile']['OK'];
$msgcode['object']['files']['unlinkFile']['ERR'] = $msgcode['rack']['files']['unlinkFile']['ERR'];
$msgcode['object']['files']['deleteFile']['OK'] = $msgcode['rack']['files']['deleteFile']['OK'];
$msgcode['object']['files']['deleteFile']['ERR'] = $msgcode['rack']['files']['deleteFile']['ERR'];
$msgcode['rack']['tags']['saveTags'] = $msgcode['object']['tags']['saveTags'];
$msgcode['ipv4net']['tags']['saveTags'] = $msgcode['object']['tags']['saveTags'];
$msgcode['ipv4vs']['tags']['saveTags'] = $msgcode['object']['tags']['saveTags'];
$msgcode['ipv4rspool']['tags']['saveTags'] = $msgcode['object']['tags']['saveTags'];
$msgcode['user']['tags']['saveTags'] = $msgcode['object']['tags']['saveTags'];
$msgcode['ipv4vs']['editlblist'] = $msgcode['object']['editrspvs'];
$msgcode['ipv4rspool']['editlblist'] = $msgcode['object']['editrspvs'];
$msgcode['ipaddress']['assignment'] = $msgcode['object']['ipv4'];

$page['ipv4space']['title'] = 'IPv4 space';
$page['ipv4space']['parent'] = 'index';
$tab['ipv4space']['default'] = 'Browse';
$tab['ipv4space']['newrange'] = 'Manage';
$tabhandler['ipv4space']['default'] = 'renderIPv4Space';
$tabhandler['ipv4space']['newrange'] = 'renderIPv4SpaceEditor';
$ophandler['ipv4space']['newrange']['addIPv4Prefix'] = 'addIPv4Prefix';
$ophandler['ipv4space']['newrange']['delIPv4Prefix'] = 'delIPv4Prefix';
$ophandler['ipv4space']['newrange']['updIPv4Prefix'] = 'updIPv4Prefix';
$msgcode['ipv4space']['newrange']['addIPv4Prefix']['OK'] = 23;
$msgcode['ipv4space']['newrange']['addIPv4Prefix']['ERR'] = 100;
$msgcode['ipv4space']['newrange']['addIPv4Prefix']['ERR1'] = 173;
$msgcode['ipv4space']['newrange']['addIPv4Prefix']['ERR2'] = 174;
$msgcode['ipv4space']['newrange']['addIPv4Prefix']['ERR3'] = 175;
$msgcode['ipv4space']['newrange']['addIPv4Prefix']['ERR4'] = 176;
$msgcode['ipv4space']['newrange']['delIPv4Prefix']['OK'] = 24;
$msgcode['ipv4space']['newrange']['delIPv4Prefix']['ERR'] = 100;

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
$tabhandler['ipv4net']['tags'] = 'renderIPv4PrefixTags';
$tabhandler['ipv4net']['files'] = 'renderFilesForIPv4Network';
$trigger['ipv4net']['tags'] = 'trigger_tags';
$ophandler['ipv4net']['properties']['editRange'] = 'updIPv4Prefix';
$ophandler['ipv4net']['liveptr']['importPTRData'] = 'importPTRData';
$ophandler['ipv4net']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['ipv4net']['files']['addFile'] = 'addFileToEntity';
$ophandler['ipv4net']['files']['updateFile'] = 'updateFile';
$ophandler['ipv4net']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['ipv4net']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['ipv4net']['files']['deleteFile'] = 'deleteFile';
$msgcode['ipv4net']['properties']['editRange']['OK'] = 25;
$msgcode['ipv4net']['properties']['editRange']['ERR'] = 100;
$msgcode['ipv4net']['liveptr']['importPTRData']['OK'] = 26;
$msgcode['ipv4net']['liveptr']['importPTRData']['ERR'] = 141;
$msgcode['ipv4space']['newrange']['updIPv4Prefix'] = $msgcode['ipv4net']['properties']['editRange'];
$msgcode['ipv4net']['files']['addFile']['OK'] = $msgcode['rack']['files']['addFile']['OK'];
$msgcode['ipv4net']['files']['addFile']['ERR'] = $msgcode['rack']['files']['addFile']['ERR'];
$msgcode['ipv4net']['files']['updateFile']['OK'] = $msgcode['rack']['files']['updateFile']['OK'];
$msgcode['ipv4net']['files']['updateFile']['ERR'] = $msgcode['rack']['files']['updateFile']['ERR'];
$msgcode['ipv4net']['files']['linkFile']['OK'] = $msgcode['rack']['files']['linkFile']['OK'];
$msgcode['ipv4net']['files']['linkFile']['ERR'] = $msgcode['rack']['files']['linkFile']['ERR'];
$msgcode['ipv4net']['files']['unlinkFile']['OK'] = $msgcode['rack']['files']['unlinkFile']['OK'];
$msgcode['ipv4net']['files']['unlinkFile']['ERR'] = $msgcode['rack']['files']['unlinkFile']['ERR'];
$msgcode['ipv4net']['files']['deleteFile']['OK'] = $msgcode['rack']['files']['deleteFile']['OK'];
$msgcode['ipv4net']['files']['deleteFile']['ERR'] = $msgcode['rack']['files']['deleteFile']['ERR'];

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
$tab['ipv4vs']['files'] = 'Files';
$tabhandler['ipv4vs']['default'] = 'renderVirtualService';
$tabhandler['ipv4vs']['edit'] = 'renderEditVService';
$tabhandler['ipv4vs']['editlblist'] = 'renderVServiceLBForm';
$tabhandler['ipv4vs']['tags'] = 'renderIPv4VSTags';
$tabhandler['ipv4vs']['files'] = 'renderFilesForVService';
$trigger['ipv4vs']['tags'] = 'trigger_tags';
$ophandler['ipv4vs']['edit']['updIPv4VS'] = 'updateVService';
$ophandler['ipv4vs']['editlblist']['addLB'] = 'addLoadBalancer';
$ophandler['ipv4vs']['editlblist']['delLB'] = 'deleteLoadBalancer';
$ophandler['ipv4vs']['editlblist']['updLB'] = 'updateLoadBalancer';
$ophandler['ipv4vs']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['ipv4vs']['files']['addFile'] = 'addFileToEntity';
$ophandler['ipv4vs']['files']['updateFile'] = 'updateFile';
$ophandler['ipv4vs']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['ipv4vs']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['ipv4vs']['files']['deleteFile'] = 'deleteFile';
$msgcode['ipv4vs']['files']['addFile']['OK'] = $msgcode['rack']['files']['addFile']['OK'];
$msgcode['ipv4vs']['files']['addFile']['ERR'] = $msgcode['rack']['files']['addFile']['ERR'];
$msgcode['ipv4vs']['files']['updateFile']['OK'] = $msgcode['rack']['files']['updateFile']['OK'];
$msgcode['ipv4vs']['files']['updateFile']['ERR'] = $msgcode['rack']['files']['updateFile']['ERR'];
$msgcode['ipv4vs']['files']['linkFile']['OK'] = $msgcode['rack']['files']['linkFile']['OK'];
$msgcode['ipv4vs']['files']['linkFile']['ERR'] = $msgcode['rack']['files']['linkFile']['ERR'];
$msgcode['ipv4vs']['files']['unlinkFile']['OK'] = $msgcode['rack']['files']['unlinkFile']['OK'];
$msgcode['ipv4vs']['files']['unlinkFile']['ERR'] = $msgcode['rack']['files']['unlinkFile']['ERR'];
$msgcode['ipv4vs']['files']['deleteFile']['OK'] = $msgcode['rack']['files']['deleteFile']['OK'];
$msgcode['ipv4vs']['files']['deleteFile']['ERR'] = $msgcode['rack']['files']['deleteFile']['ERR'];

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
$msgcode['ipv4rspool']['edit']['updIPv4RSP'] = $msgcode['ipv4rsplist']['edit']['upd'];

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
$tabhandler['ipv4rspool']['tags'] = 'renderIPv4RSPoolTags';
$tabhandler['ipv4rspool']['files'] = 'renderFilesForRSPool';
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
$ophandler['ipv4rspool']['files']['updateFile'] = 'updateFile';
$ophandler['ipv4rspool']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['ipv4rspool']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['ipv4rspool']['files']['deleteFile'] = 'deleteFile';
$msgcode['ipv4rspool']['editrslist']['addRS']['OK'] = 34;
$msgcode['ipv4rspool']['editrslist']['addRS']['ERR'] = 126;
$msgcode['ipv4rspool']['editrslist']['delRS']['OK'] = 35;
$msgcode['ipv4rspool']['editrslist']['delRS']['ERR'] = 128;
$msgcode['ipv4rspool']['editrslist']['updRS']['OK'] = 36;
$msgcode['ipv4rspool']['editrslist']['updRS']['ERR'] = 133;
$msgcode['ipv4rspool']['editrslist']['addMany']['OK'] = 37;
$msgcode['ipv4rspool']['editrslist']['addMany']['ERR1'] = 131;
$msgcode['ipv4rspool']['editrslist']['addMany']['ERR2'] = 127;
$msgcode['ipv4rspool']['rsinservice']['upd']['OK'] = 38;
$msgcode['ipv4rspool']['rsinservice']['upd']['ERR'] = 140;
$msgcode['ipv4rspool']['files']['addFile']['OK'] = $msgcode['rack']['files']['addFile']['OK'];
$msgcode['ipv4rspool']['files']['addFile']['ERR'] = $msgcode['rack']['files']['addFile']['ERR'];
$msgcode['ipv4rspool']['files']['updateFile']['OK'] = $msgcode['rack']['files']['updateFile']['OK'];
$msgcode['ipv4rspool']['files']['updateFile']['ERR'] = $msgcode['rack']['files']['updateFile']['ERR'];
$msgcode['ipv4rspool']['files']['linkFile']['OK'] = $msgcode['rack']['files']['linkFile']['OK'];
$msgcode['ipv4rspool']['files']['linkFile']['ERR'] = $msgcode['rack']['files']['linkFile']['ERR'];
$msgcode['ipv4rspool']['files']['unlinkFile']['OK'] = $msgcode['rack']['files']['unlinkFile']['OK'];
$msgcode['ipv4rspool']['files']['unlinkFile']['ERR'] = $msgcode['rack']['files']['unlinkFile']['ERR'];
$msgcode['ipv4rspool']['files']['deleteFile']['OK'] = $msgcode['rack']['files']['deleteFile']['OK'];
$msgcode['ipv4rspool']['files']['deleteFile']['ERR'] = $msgcode['rack']['files']['deleteFile']['ERR'];

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
$tab['user']['files'] = 'Files';
$tabhandler['user']['default'] = 'renderUser';
$tabhandler['user']['tags'] = 'renderUserTags';
$tabhandler['user']['files'] = 'renderFilesForUser';
$ophandler['user']['tags']['saveTags'] = 'saveEntityTags';
$ophandler['user']['files']['addFile'] = 'addFileToEntity';
$ophandler['user']['files']['updateFile'] = 'updateFile';
$ophandler['user']['files']['linkFile'] = 'linkFileToEntity';
$ophandler['user']['files']['unlinkFile'] = 'unlinkFile';
$ophandler['user']['files']['deleteFile'] = 'deleteFile';
$msgcode['user']['files']['addFile']['OK'] = $msgcode['rack']['files']['addFile']['OK'];
$msgcode['user']['files']['addFile']['ERR'] = $msgcode['rack']['files']['addFile']['ERR'];
$msgcode['user']['files']['updateFile']['OK'] = $msgcode['rack']['files']['updateFile']['OK'];
$msgcode['user']['files']['updateFile']['ERR'] = $msgcode['rack']['files']['updateFile']['ERR'];
$msgcode['user']['files']['linkFile']['OK'] = $msgcode['rack']['files']['linkFile']['OK'];
$msgcode['user']['files']['linkFile']['ERR'] = $msgcode['rack']['files']['linkFile']['ERR'];
$msgcode['user']['files']['unlinkFile']['OK'] = $msgcode['rack']['files']['unlinkFile']['OK'];
$msgcode['user']['files']['unlinkFile']['ERR'] = $msgcode['rack']['files']['unlinkFile']['ERR'];
$msgcode['user']['files']['deleteFile']['OK'] = $msgcode['rack']['files']['deleteFile']['OK'];
$msgcode['user']['files']['deleteFile']['ERR'] = $msgcode['rack']['files']['deleteFile']['ERR'];

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
$msgcode['files']['addmore']['addFile']['OK'] = 69;
$msgcode['files']['addmore']['addFile']['ERR'] = 100;

$page['filesbylink']['title_handler'] = 'dynamic_title_file';
$page['filesbylink']['handler'] = 'renderFilesByLink'; //renderObjectGroup
$page['filesbylink']['bypass'] = 'entity_type';
$page['filesbylink']['bypass_type'] = 'string';
$page['filesbylink']['parent'] = 'files';
$ophandler['filesbylink']['default']['deleteFile'] = 'deleteFile';

$page['file']['title'] = 'File';
$page['file']['bypass'] = 'file_id';
$page['file']['bypass_type'] = 'uint';
$page['file']['parent'] = 'index';
$page['file']['tagloader'] = 'loadFileTags';
$page['file']['autotagloader'] = 'loadFileAutoTags';
$tab['file']['default'] = 'View';
$tab['file']['tags'] = 'Tags';
$trigger['file']['tags'] = 'trigger_tags';
$tabhandler['file']['tags'] = 'renderFileTags';
$tabhandler['file']['default'] = 'renderFile';
$ophandler['file']['tags']['saveTags'] = 'saveEntityTags';
$msgcode['file']['tags']['saveTags']['OK'] = 22;
$msgcode['file']['tags']['saveTags']['ERR'] = 143;

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
