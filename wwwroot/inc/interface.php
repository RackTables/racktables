<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*
*
*  This file contains frontend functions for RackTables.
*
*/

require_once 'ajax-interface.php';
require_once 'slb-interface.php';

// Interface function's special.
$nextorder['odd'] = 'even';
$nextorder['even'] = 'odd';

// address allocation type
$aat = array
       (
           'regular' => 'Connected',
           'virtual' => 'Loopback',
           'shared' => 'Shared',
           'router' => 'Router',
           'point2point' => 'Point-to-point',
       );
// address allocation code, IPv4 addresses and objects view
$aac_right = array
             (
                 'regular' => '',
                 'virtual' => '<span class="aac-right" title="' . $aat['virtual'] . '">L</span>',
                 'shared' => '<span class="aac-right" title="' . $aat['shared'] . '">S</span>',
                 'router' => '<span class="aac-right" title="' . $aat['router'] . '">R</span>',
                 'point2point' => '<span class="aac-right" title="' . $aat['point2point'] . '">P</span>',
             );
// address allocation code, IPv4 networks view
$aac_left = array
            (
                'regular' => '',
                'virtual' => '<span class="aac-left" title="' . $aat['virtual'] . '">L:</span>',
                'shared' => '<span class="aac-left" title="' . $aat['shared'] . '">S:</span>',
                'router' => '<span class="aac-left" title="' . $aat['router'] . '">R:</span>',
                'point2point' => '<span class="aac-left" title="' . $aat['point2point'] . '">P:</span>',
            );

$vtdecoder = array
             (
                 'ondemand' => '',
                 'compulsory' => 'P',
#	'alien' => 'NT',
             );

$vtoptions = array
             (
                 'ondemand' => 'auto',
                 'compulsory' => 'permanent',
#	'alien' => 'never touch',
             );

// This may be populated later onsite, report rendering function will use it.
// See the $systemreport for structure.
$localreports = array();

$CodePressMap = array
                (
                    'sql' => 'sql',
                    'php' => 'php',
                    'html' => 'htmlmixed',
                    'css' => 'css',
                    'js' => 'javascript',
                );

$attrtypes = array
             (
                 'uint' => '[U] unsigned integer',
                 'float' => '[F] floating point',
                 'string' => '[S] string',
                 'dict' => '[D] dictionary record',
                 'date' => '[T] date'
             );

function showLogoutURL ()
{
    $https = (isset ($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') ? 's' : '';
    $port = (! in_array ($_SERVER['SERVER_PORT'], array (80, 443))) ? ':' . $_SERVER['SERVER_PORT'] : '';
    $pathinfo = pathinfo ($_SERVER['REQUEST_URI']);
    $dirname = $pathinfo['dirname'];
    // add a trailing slash if the installation resides in a subdirectory
    if ($dirname != '/')
        $dirname .= '/';
    printf ('http%s://logout@%s%s?logout', $https, $_SERVER['SERVER_NAME'], $dirname);
}

$quick_links = NULL; // you can override this in your local.php, but first initialize it with getConfiguredQuickLinks()

function renderQuickLinks()
{
    global $quick_links;

    if (! isset ($quick_links))
        $quick_links = getConfiguredQuickLinks();

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Quicklinks_Table", "Quicklinks");

    $quicklinks_data = array();
    foreach ($quick_links as $link)
    {
        //Generating the QuickLinks Array
        $quicklinks_row_data = array();
        $quicklinks_row_data['href'] = $link['href'];
        $quicklinks_row_data['title'] = str_replace (' ', '&nbsp;', $link['title']) ;
        $quicklinks_data[] = $quicklinks_row_data ;
    }
    $mod->addOutput("Quicklinks_Data", $quicklinks_data);
}

function renderInterfaceHTML ($pageno, $tabno, $payload)
{
    ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
                <head><title><?php echo getTitle ($pageno);
    ?></title>
    <?php printPageHeaders();
    ?>
    </head>
    <body>
    <div class="maintable">
                   <div class="mainheader">
                                  <div style="float: right" class=greeting><a href='index.php?page=myaccount&tab=default'><?php global $remote_displayname;
    echo $remote_displayname ?></a> [ <a href='<?php showLogoutURL(); ?>'>logout</a> ]</div>
    <?php echo getConfigVar ('enterprise') ?> RackTables <a href="http://racktables.org" title="Visit RackTables site"><?php echo CODE_VERSION ?></a><?php renderQuickLinks() ?>
            </div>
            <div class="menubar"><?php showPathAndSearch ($pageno, $tabno);
    ?></div>
    <div class="tabbar"><?php showTabs ($pageno, $tabno);
    ?></div>
    <div class="msgbar"><?php showMessageOrError();
    ?></div>
    <div class="pagebar"><?php echo $payload;
    ?></div>
    </div>
    </body>
    </html>
    <?php
}

// Main menu.
// Not used with templates
function renderIndexItem ($ypageno)
{
    echo (! permitted ($ypageno)) ? "          <td>&nbsp;</td>\n" :
    "          <td>\n" .
    "            <h1><a href='" . makeHref (array ('page' => $ypageno)) . "'>" .
    getPageName ($ypageno) . "<br>\n" . getImageHREF ($ypageno) .
    "</a></h1>\n" .
    "          </td>\n";

}

function renderIndex()
{
    global $indexlayout;

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderIndex");
    $mod->setNamespace("index");

    foreach ($indexlayout as $row)
    {
        $rowMod = $tplm->generatePseudoSubmodule("Rows", $mod);
        $rowsCont = array();
        foreach ($row as $column)
            if ($column === NULL)
                $rowsCont[] = array('IsNull' => $tplm->generateModule('EmptyTableCell', true)->run());
        else
        {
            if((!permitted ($column)))
                $rowsCont[] = array('Permitted' => $tplm->generateModule('EmptyTableCell', true)->run());
            else
                $rowsCont[] = array(
                                  'IndexItem' => $tplm->generateModule('IndexItemMod', true, array(
                                              'Href' => makeHref (array ('page' => $column)),
                                              'PageName' => getPageName ($column),
                                              'Image' => getImageHREF ($column))
                                                                      )->run());
        }
        $rowMod->setOutput("Cols", $rowsCont);
    }
}

function getRenderedAlloc ($object_id, $alloc)
{
    $ret = array
           (
               'tr_class' => '',
               'td_name_suffix' => '',
               'td_ip' => '',
               'td_network' => '',
               'td_routed_by' => '',
               'td_peers' => '',
           );
    $dottedquad = $alloc['addrinfo']['ip'];
    $ip_bin = $alloc['addrinfo']['ip_bin'];
    $hl_ip_bin = NULL;

    $tplm = TemplateManager::getInstance();

    if (isset ($_REQUEST['hl_ip']))
    {
        $hl_ip_bin = ip_parse ($_REQUEST['hl_ip']);
        addAutoScrollScript ("ip-" . $_REQUEST['hl_ip'], $tplm->getMainModule(), 'Payload');
    }

    $ret['tr_class'] = $alloc['addrinfo']['class'];
    if ($hl_ip_bin === $ip_bin)
        $ret['tr_class'] .= ' highlight';

    // render IP change history
    $ip_title = '';
    $ip_class = '';
    if (isset ($alloc['addrinfo']['last_log']))
    {
        $log = $alloc['addrinfo']['last_log'];
        $ip_title = "title='" .
                    htmlspecialchars
                    (
                        $log['user'] . ', ' . formatAge ($log['time']),
                        ENT_QUOTES
                    ) . "'";
        $ip_class = 'hover-history underline';
    }

    // render IP address td
    global $aac_right;
    $netinfo = spotNetworkByIP ($ip_bin);
    $td_ip_mod = $tplm->generateModule('RenderedAllocTdIp', true);

    if (isset ($netinfo))
    {
        $title = $dottedquad;
        if (getConfigVar ('EXT_IPV4_VIEW') != 'yes')
            $title .= '/' . $netinfo['mask'];

        $tplm->generateSubmodule('Info', 'RenderedAllocTdIpNetInfo', $td_ip_mod, true,
                                 array( 'Dottequad' => $dottedquad,
                                        'IpClass' => $ip_class,
                                        'IpTitle' => $ip_title,
                                        'Href' => makeHref (
                                            array
                                            (
                                                'page' => 'ipaddress',
                                                'hl_object_id' => $object_id,
                                                'ip' => $dottedquad,
                                            )),
                                        'Title' => $title));
    }
    else
    {
        $tplm->generateSubmodule('Info', 'RenderedAllocTdIpNoNetInfo', $td_ip_mod, true,
                                 array( 'Dottequad' => $dottedquad,
                                        'IpClass' => $ip_class,
                                        'IpTitle' => $ip_title));
    }

    $td_ip_mod->setOutput('Aac', $aac_right[$alloc['type']]);

    if (strlen ($alloc['addrinfo']['name']))
        $td_ip_mod->setOutput('NiftyStr',  '(' . niftyString ($alloc['addrinfo']['name']) . ')');
    $ret['td_ip'] = $td_ip_mod->run();

    // render network and routed_by tds
    $td_class = 'tdleft';
    if (! isset ($netinfo))
    {
        $ret['td_network'] = $tplm->generateModule('RenderedAllocNetworkNoNetinfo', true,
                             array('TdClass' => $td_class))->run();
        $ret['td_routed_by'] = $ret['td_network'];
    }
    else
    {
        $ret['td_network'] = $tplm->generateModule('RenderedAllocNetworkNetinfo', true,
                             array('TdClass' => $td_class,
                                   'InfoCell' => renderCell($netinfo)))->run();
        // render "routed by" td
        if ($display_routers = (getConfigVar ('IPV4_TREE_RTR_AS_CELL') == 'none'))
            $ret['td_routed_by'] = '<td>&nbsp;</td>';
        else
        {
            loadIPAddrList ($netinfo);
            $other_routers = array();
            foreach (findRouters ($netinfo['own_addrlist']) as $router)
                if ($router['id'] != $object_id)
                    $other_routers[] = $router;
            if (count ($other_routers))
                $ret['td_routed_by'] = printRoutersTD($other_routers, $display_routers);
            else
                $ret['td_routed_by'] = $tplm->generateModule('RenderedAllocRoutedByOnly', true,
                                       array('TdClass' => $td_class))->run();
        }
    }

    // render peers td
    $td_peers_mod = $tplm->generateModule('RenderedAllocPeers', true, array('TdClass' => $td_class));
    $prefix = '';
    $separator = '; ';
    if ($alloc['addrinfo']['reserved'] == 'yes')
    {
        $td_peers_mod->addOutput('Prefix', $prefix);
        $tplm->generateSubmodule('Strong', 'StrongElement', $td_peers_mod, true, array('Cont' => 'RESERVED'));
        $prefix = $separator;
    }
    foreach ($alloc['addrinfo']['allocs'] as $allocpeer)
    {
        if ($allocpeer['object_id'] != $object_id)
        {
            $tplm->generateSubmodule('LocPeers', 'GlobalPlaceholder', $td_peers_mod, true, array(
                                         'Cont' => $prefix . makeIPAllocLink ($ip_bin, $allocpeer)));
            $prefix = $separator;
        }
        elseif ($allocpeer['type'] == 'point2point' && isset ($netinfo))
        {
            // show PtP peers in the IP network
            if (! isset ($netinfo['own_addrlist']))
                loadIPAddrList ($netinfo);
            foreach (getPtPNeighbors ($ip_bin, $netinfo['own_addrlist']) as $p_ip_bin => $p_alloc_list)
                foreach ($p_alloc_list as $p_alloc)
                {
                    $tplm->generateSubmodule('LocPeers', 'GlobalPlaceholder', $td_peers_mod, true, array(
                                                 'Cont' => $prefix . '&harr;&nbsp;' . makeIPAllocLink ($p_ip_bin, $p_alloc)));
                    $prefix = $separator;
                }
        }
    }

    $ret['td_peers'] = $td_peers_mod->run();
    return $ret;
}

function renderLocationFilterPortlet (TemplateModule $parent = null,$placeholder = "")
{
    // Recursive function used to build the location tree
    function renderLocationCheckbox (TemplateModule $tpl, $subtree, $level = 0)
    {
        $self = __FUNCTION__;

        $tplm = TemplateManager::getInstance();
        foreach ($subtree as $location_id => $location)
        {
            $checked = (! isset ($_SESSION['locationFilter']) || in_array ($location['id'], $_SESSION['locationFilter'])) ? 'checked' : '';

            $smod = $tplm->generateSubmodule("Locations", "LocationFilterPortletCheckbox", $tpl);
            $smod->addOutput("Name", $location["name"]);
            $smod->setOutput("Id",$location["id"]);
            $smod->setOutput("Checked",$checked);
            $smod->setOutput("LevelSpace",$level*16);
            $smod->setOutput("Level",$level);

            if ($location['kidc'])
            {
                $smod->setOutput("Kidc",true);
                $self ($smod, $location['kids'], $level + 1);
            }
        }
    }
    $tplm = TemplateManager::getInstance();

    if($parent == null )
    {
        $mod = $tplm->generateModule("LocationFilterPortlet");
    }
    else
        $mod = $tplm->generateSubmodule($placeholder, "LocationFilterPortlet", $parent);
    $mod->setNamespace("");
    $mod->setLock(true);
  
    $locationlist = listCells ('location');
    if (count ($locationlist))
    {
        $mod->addOutput("LocationsExist", true);
        renderLocationCheckbox($mod,treeFromList($locationlist));
    }
    else
    {
        $mod->addOutput("LocationsExist", false);
    }
    if($parent == null)
        return $mod->run();
}

function renderRackspace ()
{
    // Handle the location filter
    @session_start();
    if (isset ($_REQUEST['changeLocationFilter']))
        unset ($_SESSION['locationFilter']);
    if (isset ($_REQUEST['location_id']))
        $_SESSION['locationFilter'] = $_REQUEST['location_id'];
    session_commit();

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "renderRackspace");
    $mod->setNamespace("rackspace", true);

    $found_racks = array();
    $cellfilter = getCellFilter();
    if (! ($cellfilter['is_empty'] && !isset ($_SESSION['locationFilter']) && renderEmptyResults ($cellfilter, 'racks', getEntitiesCount ('rack'))))
    {
        $rows = array();
        $rackCount = 0;
        foreach (getAllRows() as $row_id => $rowInfo)
        {
            $rackList = applyCellFilter ('rack', $cellfilter, $row_id);
            $found_racks = array_merge ($found_racks, $rackList);
            $rows[] = array (
                          'location_id' => $rowInfo['location_id'],
                          'location_name' => $rowInfo['location_name'],
                          'row_id' => $row_id,
                          'row_name' => $rowInfo['name'],
                          'racks' => $rackList
                      );
            $rackCount += count($rackList);
        }

        if (! renderEmptyResults($cellfilter, 'racks', $rackCount))
        {
            // generate thumb gallery
            global $nextorder;
            $rackwidth = getRackImageWidth();
            // Zero value effectively disables the limit.
            $maxPerRow = getConfigVar ('RACKS_PER_ROW');
            $order = 'odd';
            if (count ($rows))
            {
                //Generate the table module instead.
                $row_objects = array();
                foreach ($rows as $row)
                {
                    $location_id = $row['location_id'];
                    $row_id = $row['row_id'];
                    $row_name = $row['row_name'];
                    $rackList = $row['racks'];

                    if (
                        $location_id != '' and isset ($_SESSION['locationFilter']) and !in_array ($location_id, $_SESSION['locationFilter']) or
                        empty ($rackList) and ! $cellfilter['is_empty']
                    )
                        continue;

                    $rowo = array();
                    $rowo["Order"] = $order;

                    $rackListIdx = 0;
                    $locationIdx = 0;
                    $locationTree = '';

                    while ($location_id)
                    {
                        if ($locationIdx == 20)
                        {
                            showWarning ("Warning: There is likely a circular reference in the location tree.  Investigate location ${location_id}.");
                            break;
                        }
                        $parentLocation = spotEntity ('location', $location_id);
                        $locationTree = "&raquo; <a href='" .
                                        makeHref(array('page'=>'location', 'location_id'=>$parentLocation['id'])) .
                                        "${cellfilter['urlextra']}'>${parentLocation['name']}</a> " .
                                        $locationTree;
                        $location_id = $parentLocation['parent_id'];
                        $locationIdx++;
                    }
                    $locationTree = substr ($locationTree, 8);

                    $rowo["LocationTree"] = $locationTree;
                    $rowo["HrefToRow"] = makeHref(array('page'=>'row', 'row_id'=>$row_id));
                    $rowo["RowName"] = $row_name;
                    $rowo["CellFilterUrlExtra"] = $cellfilter['urlextra']; 

                    if (count ($rackList))
                    {
                        $rowo["Rackline"] = array();
                        foreach ($rackList as $rack)
                        {
                            $output = array("RackLink"=>makeHref(array('page'=>'rack', 'rack_id'=>$rack['id'])),
                                            "RackImageWidth"=>$rackwidth,
                                            "RackImageHeight"=>getRackImageHeight ($rack['height']),
                                            "RackId"=>$rack['id'],
                                            "RackName"=>$rack['name'],
                                            "RackHeight"=>$rack['height']
                                           );
                            if ($rackListIdx > 0 and $maxPerRow > 0 and $rackListIdx % $maxPerRow == 0)
                            {
                                $output = array("NewLine"=>true,"RowOrder"=>$order,"RowName",$row_name);
                            }
                            $rowo["Rackline"][] = $output;
                            $rackListIdx++;
                        }
                        $order = $nextorder[$order];
                    }
                    $row_objects[] = $rowo;
                }
                $mod->addOutput("OverviewTable", $row_objects);

            }
        }
        else
        {
            $mod->setOutput("RackspaceOverviewTable", "");
            $mod->setOutput("RackspaceOverviewHeadline", "No rows found.");
        }
    }
    renderCellFilterPortlet ($cellfilter, 'rack', $found_racks, array(), $mod, 'CellFilter');
    renderLocationFilterPortlet($mod, 'LocationFilter');
}

function renderLocationRowForEditor ($parentmod,$subtree, $level = 0)
{
    $tplm = TemplateManager::getInstance();
    $self = __FUNCTION__;
    foreach ($subtree as $locationinfo)
    {
        $smod = $tplm->generatePseudoSubmodule("LocationList",$parentmod);
        if ($locationinfo['kidc'])
            $smod->addOutput("HasSublocations", true);
        if (!($locationinfo['refcnt'] > 0 || $locationinfo['kidc'] > 0))
            $smod->addOutput("IsDeletable",true);
   
        $smod->addOutput("LocationName", $locationinfo['name']);
        $smod->addOutput("LocationId", $locationinfo['id']);
        $smod->setOutput('Level', ($locationinfo['kidc'] ? $level : ($level + 1) * 16));
        $parent = isset ($locationinfo['parent_id']) ? $locationinfo['parent_id'] : 0;
   
        $plist = array ( $parent => $parent ? htmlspecialchars ($locationinfo['parent_name']) : '-- NONE --');
        $outarr = array();
        foreach ($plist as $key => $value)
        {
            $outarr[] = array("ParentListId"=>$key,"ParentListContent"=>$value,"ParentListSelected"=> ($key == $parent ? "selected" : ""));
        }
        $smod->addOutput("Parentselect",$outarr);
        if ($locationinfo['kidc'])
            $self ($parentmod,$locationinfo['kids'], $level + 1);
    }
}

function renderLocationSelectTree ($selected_id = NULL, $parentmod = null, $placeholder = 'Options')
{
    if ($parentmod != null)
    {
        $tplm = TemplateManager::getInstance();
        $locationlist = listCells ('location');

        $mod = $tplm->generateSubmodule($placeholder, "LocationChildren", $parentmod);
        $mod->defNamespace();
        $mod->addOutput('Content','-- None --');
        $mod->setOutput('Id',0);

        foreach (treeFromList ($locationlist) as $location)
        {
            $mod = $tplm->generateSubmodule("Options", "LocationChildrenBold", $parentmod);
            $mod->setNamespace('',true);
            $mod->setLock(true);

            if ($location['id'] == $selected_id )
                $mod->addOutput('Selected', 'selected');
            $mod->addOutput('Content',$location['name']);
            $mod->setOutput("Id", $location['id']) ;
            printLocationChildrenSelectOptions ($location, 0, $selected_id, $mod);
        }
    }
}

function renderRackspaceLocationEditor ()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "renderRackspaceLocationEditor");
    $mod->setNamespace('rackspace',true);

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
    {
        $mod->setOutput('renderNewTop', true);
    }
    renderLocationSelectTree (NULL, $mod, 'RenderNewFormOptions');

    $locations = listCells ('location');
    renderLocationRowForEditor ($mod,treeFromList ($locations));
}

function renderRackspaceRowEditor ()
{
    function printNewItemTR ($plc,$parentmod)
    {
        $tplm = TemplateManager::getInstance();
        $mod = $tplm->generateSubmodule($plc, "RackspaceRowEditorNew", $parentmod);
        renderLocationSelectTree (null,$mod);

    }

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "renderRackspaceRowEditor");
    $mod->setNamespace("rackspace",true);

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->setOutput('NewTop',true);

    renderLocationSelectTree (null,$mod,'LocationNewOptions');
    foreach (getAllRows() as $row_id => $rowInfo)
    {
        $smod = $tplm->generatePseudoSubmodule("RowList",$mod);
        if ($rc = $rowInfo['rackc'])
            $smod->addOutput("HasChildren", true);
        $smod->addOutput("RowId",$row_id);
        $smod->addOutput("RackCount",$rc);
        $smod->addOutput("RowName",$rowInfo['name']);
        renderLocationSelectTree ($rowInfo['location_id'],$smod,'LocationEditOptions');
    }
}

function renderRow ($row_id)
{
    $rowInfo = getRowInfo ($row_id);
    $cellfilter = getCellFilter();
    $rackList = applyCellFilter ('rack', $cellfilter, $row_id);

    $summary = array ();
    $summary['Name'] = $rowInfo['name'];
    if ($rowInfo['location_id'])
        $summary['Location'] = mkA ($rowInfo['location'], 'location', $rowInfo['location_id']);
    $summary['Racks'] = $rowInfo['count'];
    $summary['Units'] = $rowInfo['sum'];
    $summary['% used'] = getProgressBar (getRSUforRow ($rackList));
    foreach (getAttrValuesSorted ($row_id) as $record)
        if
        (
            $record['value'] != '' and
            permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $record['id'])))
        )
            $summary['{sticker}' . $record['name']] = formatAttributeValue ($record);

    // Main layout starts.
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'Row');
    $mod->setNamespace('row',true);
    $mod->addOutput('RowName', $rowInfo['name']);

    // Left portlet with row information.
    renderEntitySummary ($rowInfo, 'Summary', $summary, $mod, 'EntitySummary');
    renderCellFilterPortlet ($cellfilter, 'rack', $rackList, array ('row_id' => $row_id), $mod);
    $mod->addOutput('FilesPortlet', renderFilesPortlet('row',$row_id));

    global $nextorder;
    $rackwidth = getRackImageWidth() * getConfigVar ('ROW_SCALE');
    // Maximum number of racks per row is proportionally less, but at least 1.
    $maxPerRow = max (floor (getConfigVar ('RACKS_PER_ROW') / getConfigVar ('ROW_SCALE')), 1);
    $rackListIdx = 0;
    $order = 'odd';
    foreach ($rackList as $rack)
    {
        $smod = $tplm->generateSubmodule('allRacks', 'RowRack', $mod);
        if ($rackListIdx % $maxPerRow == 0)
        {
            if ($rackListIdx > 0)
                $smod->addOutput('EndOfLine', true);
            $smod->addOutput('NewLine', true);
        }
        $class = ($rack['has_problems'] == 'yes') ? 'error' : $order;

        $smod->addOutput('Class', $class);
        $smod->addOutput('Link', makeHref(array('page'=>'rack', 'rack_id'=>$rack['id'])));
        $smod->addOutput('ImgWidth',$rackwidth);
        $smod->addOutput('ImgHeight', getRackImageHeight ($rack['height']) * getConfigVar ('ROW_SCALE'));
        $smod->addOutput('Id', $rack['id']);
        $smod->addOutput('Name', $rack['name']);
        $smod->addOutput('RowScale', getConfigVar ('ROW_SCALE'));

        $order = $nextorder[$order];
        $rackListIdx++;
    }
}

function renderEditRowForm ($row_id)
{
    $row = getRowInfo ($row_id);

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderEditRowForm');
    $mod->setNamespace('row',true);

    $locations = array ();
    $locations[0] = '-- NOT SET --';
    foreach (listCells ('location') as $id => $locationInfo)
        $locations[$id] = $locationInfo['name'];
    natcasesort ($locations);

    $mod->setOutput('Location_ID', $row['location_id']);
    $mod->setOutput('Lacations', $locations);
    $mod->setOutput('Row_Name', $row['name']);

    // optional attributes
    $values = getAttrValuesSorted ($row_id);
    $num_attrs = count ($values);
    $mod->setOutput('Num_atts', $num_attrs);
    $i = 0;
    $allRecordsOut = array();
    foreach ($values as $record)
    {
        $singleRow = array('I' => $i, 'Record_ID' => $record['id'], 'Record_Name' => $record['name']);
        if (strlen ($record['value']))
            $singleRow['HasValue'] = true;
        
        switch ($record['type'])
        {
        case 'uint':
        case 'float':
        case 'string':
            $singleRow['PrintInput'] = true;
            $singleRow['RecordValue'] = $record['value'];
            break;
        case 'dict':
            $chapter = readChapter ($record['chapter_id'], 'o');
            $chapter[0] = '-- NOT SET --';
            $chapter = cookOptgroups ($chapter, 1562, $record['key']);
            $singleRow['PrintInput'] = false;
            $singleRow['NifitySelChapter'] = printNiftySelect ($chapter, array ('name' => "${i}_value"), $record['key']);
            break;
        }
        $allRecordsOut[] = $singleRow;
        $i++;
    }
    $mod->addOutput('AllRecords', $allRecordsOut);

    if ($row['count'] == 0)
    {
        $mod->setOutput('hasRows', true);
    }
    renderObjectHistory ($row_id, $mod, 'ObjectHistory');
}

// Used by renderRack()
function printObjectDetailsForRenderRack ($object_id, $hl_obj_id = 0, $parent = null, $placeholder)
{
    // Don't use again might better use helper function
    $tplm = TemplateManager::getInstance();
    
    if($parent==null)
        $mod = $tplm->generateModule("PrintObjectDetailsForRenderRack");
    else
        $mod = $tplm->generateSubmodule($placeholder, "PrintObjectDetailsForRenderRack", $parent);

    $mod->setNamespace("object");

    $objectData = spotEntity ('object', $object_id);
    if (strlen ($objectData['asset_no']))
    {
        $mod->addOutput("isAsset_no", true);
        $mod->addOutput("asset_no", $objectData['asset_no']);
    }
    
    // Don't tell about label, if it matches common name.
    if ($objectData['name'] != $objectData['label'] and strlen ($objectData['label']))
    {
        $mod->addOutput("label", $objectData['label']);
        $mod->addOutput("isUncommon_name", true);
    }

    // Display list of child objects, if any
    $objectChildren = getEntityRelatives ('children', 'object', $objectData['id']);
    $slotRows = $slotCols = $slotInfo = $slotData = $slotTitle = $slotClass = array ();
    if (count($objectChildren) > 0)
    {
        $mod->addOutput("areObjectChildren", true);

        foreach ($objectChildren as $child)
        {
            $childNames[] = $child['name'];
            $childData = spotEntity ('object', $child['entity_id']);
            $attrData = getAttrValues ($child['entity_id']);
            $numRows = $numCols = 1;
            if (isset ($attrData[2])) // HW type
            {
                extractLayout ($attrData[2]);
                if (isset ($attrData[2]['rows']))
                {
                    $numRows = $attrData[2]['rows'];
                    $numCols = $attrData[2]['cols'];
                }
            }
            if (isset ($attrData['28'])) // slot number
            {
                $slot = $attrData['28']['value'];
                if (preg_match ('/\d+/', $slot, $matches))
                    $slot = $matches[0];
                $slotRows[$slot] = $numRows;
                $slotCols[$slot] = $numCols;
                $slotInfo[$slot] = $child['name'];
                $slotData[$slot] = $child['entity_id'];

                $slotTitleMod = $tplm->generateModule("PrintObjectDetailsForRenderRack_SlotTitle");
                $slotTitleMod->setNamespace('object');

                if (strlen ($childData['asset_no']))
                {
                    $slotTitleMod->setOutput('asset_no', $childData['asset_no']);
                }

                if (strlen ($childData['label']) and $childData['label'] != $child['name'])
                {
                    $slotTitleMod->setOutput('label', $childData['label']);
                }

                $slotTitle[$slot] = $slotTitleMod->run();
                $slotClass[$slot] = 'state_T';
                if ($childData['has_problems'] == 'yes')
                    $slotClass[$slot] = 'state_Tw';
                if ($child['entity_id'] == $hl_obj_id)
                    $slotClass[$slot] = 'state_Th';
            }
        }
        natsort($childNames);
        $mod->addOutput("childNames", implode(', ', $childNames));
    }
    $mod->addOutput("mkA", mkA ($objectData['dname'], 'object', $objectData['id']));

    if (in_array ($objectData['objtype_id'], array (1502,1503))) // server chassis, network chassis
    {
        $objAttr = getAttrValues ($objectData['id']);
        if (isset ($objAttr[2])) // HW type
        {
            extractLayout ($objAttr[2]);
            if (isset ($objAttr[2]['rows']))
            {
                $rows = $objAttr[2]['rows'];
                $cols = $objAttr[2]['cols'];
                $layout = $objAttr[2]['layout'];

                $tablemod = $tplm->generateSubmodule("tableCont","FullWidthTable", $mod, true);

                for ($r = 0; $r < $rows; $r++)
                {
                    $rowmod = $tplm->generateSubmodule("cont","StdTableRow", $tablemod, true);
                    for ($c = 0; $c < $cols; $c++)
                    {
                        $s = ($r * $cols) + $c + 1;
                        if (isset ($slotData[$s]))
                        {
                            if ($slotData[$s] >= 0)
                            {
                                for ($lr = 0; $lr < $slotRows[$s]; $lr++)
                                    for ($lc = 0; $lc < $slotCols[$s]; $lc++)
                                    {
                                        $skip = ($lr * $cols) + $lc;
                                        if ($skip > 0)
                                            $slotData[$s + $skip] = -1;
                                    }

                                $slotDataMod = $tplm->generateSubmodule('cont',"PrintObjectDetailsForRenderRack_SlotData", $rowmod, true);
                                $slotDataMod->setNamespace('object');

                                if ($slotRows[$s] > 1)
                                    $slotDataMod->setOutput('slotRow', $slotRows[$s]);
                                if ($slotCols[$s] > 1)
                                    $slotDataMod->setOutput('slotCols', $slotCols[$s]);
                                $slotDataMod->setOutput('slotClass', $slotClass[$s]);
                                $slotDataMod->setOutput('slotTitle', $slotTitle[$s]);
                                if ($layout == 'V')
                                {
                                    $tmp = substr ($slotInfo[$s], 0, 1);
                                    foreach (str_split (substr ($slotInfo[$s], 1)) as $letter)
                                        $tmp .= '<br>' . $letter;
                                    $slotInfo[$s] = $tmp;
                                }
                                $slotDataMod->setOutput('mkASlotInfo', mkA ($slotInfo[$s], 'object', $slotData[$s]));
                            }
                        }
                        else
                            $tplm->generateSubmodule("cont","ObjectFreeSolt", $rowmod, true);
                    }
                }
            }
        }
    }
    if($parent==null)
        return $mod->run();
}

// This function renders rack as HTML table.
function renderRack ($rack_id, $hl_obj_id = 0, $parent = null, $placeholder = "RenderedRack")
{
    $rackData = spotEntity ('rack', $rack_id);
    amplifyCell ($rackData);
    markAllSpans ($rackData);
    if ($hl_obj_id > 0)
        highlightObject ($rackData, $hl_obj_id);
    $prev_id = getPrevIDforRack ($rackData['row_id'], $rack_id);
    $next_id = getNextIDforRack ($rackData['row_id'], $rack_id);

    $tplm = TemplateManager::getInstance();
    
    if($parent==null)
        $mod = $tplm->generateModule("RenderRack");
    else
        $mod = $tplm->generateSubmodule($placeholder, "RenderRack", $parent);

    $mod->setNamespace("object", true);
    $mod->addOutput("mkARowName", mkA ($rackData['row_name'], 'row', $rackData['row_id']));

    if ($prev_id != NULL)
    {
        $mod->addOutput("isPrev", true);
        $mod->addOutput("mkAPrevImg", mkA (getImageHREF ('prev', 'previous rack'), 'rack', $prev_id));
    }
    $mod->addOutput("mkAName", mkA ($rackData['name'], 'rack', $rackData['id']));
    if ($next_id != NULL)
    {
        $mod->addOutput("isNext", true);
        $mod->addOutput("mkANextImg", mkA (getImageHREF ('next', 'next rack'), 'rack', $next_id));
    }

    for ($i = $rackData['height']; $i > 0; $i--)
    {
        $singleRow = $tplm->generatePseudoSubmodule("RackLoopSpace", $mod);
        $singleRow->addOutput("inverseRack", inverseRackUnit ($i, $rackData));

        for ($locidx = 0; $locidx < 3; $locidx++)
        {
            if (isset ($rackData[$i][$locidx]['skipped']))
                continue;
            $state = $rackData[$i][$locidx]['state'];

            $singleLocId = array('state' => $state,
                                 'rackHL' => $rackData[$i][$locidx]['hl'],
                                 'colspan' => $rackData[$i][$locidx]['colspan'],
                                 'rowspan' => $rackData[$i][$locidx]['rowspan'],
                                );
            if($state == 'T')
                $singleLocId['objectDetail'] = printObjectDetailsForRenderRack ($rackData[$i][$locidx]['object_id'], $hl_obj_id);
            switch ($state)
            {
            case 'T':
                break;
            case 'A':
                break;
            case 'F':
                break;
            case 'U':
                break;
            default:
                $singleLocId['defaultState'] = true;
                break;
            }
            $tplm->generatePseudoSubmodule('AllLocIdx', $singleRow, $singleLocId);
        }
    }

    // Get a list of all of objects Zero-U mounted to this rack
    $zeroUObjects = getEntityRelatives('children', 'rack', $rack_id);
    if (count ($zeroUObjects) > 0)
    {
        $mod->addOutput("hasZeroUObj", true);

        $allZeroUObjOut = array();
        foreach ($zeroUObjects as $zeroUObject)
        {
            $state = ($zeroUObject['entity_id'] == $hl_obj_id) ? 'Th' : 'T';
            $allZeroUObjOut[] = array('state' => $state,
                                      'objDetails' => printObjectDetailsForRenderRack($zeroUObject['entity_id']));
        }
        $mod->addOutput("allZeroUObj", $allZeroUObjOut);
    }

    if($parent==null)
        return $mod->run();
}

function renderRackSortForm ($row_id)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RowRackSortForm');
    $mod->setNamespace('row',true);

    $arr = array();
    foreach (getRacks($row_id) as $rack_id => $rackInfo)
        $arr[] = array('RackId'=>$rack_id,'RackName'=>$rackInfo['name']);

    $mod->addOutput('racklist', $arr);
}

function renderNewRackForm ($row_id)
{
    $default_height = getConfigVar ('DEFAULT_RACK_HEIGHT');
    if ($default_height == 0)
        $default_height = '';

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'NewRackForm');
    $mod->setNamespace('row');
    $mod->addOutput('DefaultHeight', $default_height);

    printTagsPicker (null, $mod, 'Tags');
}

function renderEditObjectForm()
{
    global $pageno;
    $object_id = getBypassValue();
    $object = spotEntity ('object', $object_id);
    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule("Payload","RenderEditObjectForm");
    $mod->setNamespace("object");
    // static attributes
    $mod->setOutput('PrintOptSel', printSelect (getObjectTypeChangeOptions ($object['id']), array ('name' => 'object_type_id'), $object['objtype_id']));
    // baseline info
    $mod->addOutput("object_name", $object['name']);
    $mod->addOutput("object_label", $object['label']);
    $mod->addOutput("object_asset_no", $object['asset_no']);
    printTagsPicker (null, $mod);

    // parent selection
    if (objectTypeMayHaveParent ($object['objtype_id']))
    {
        $mod->addOutput("haveParent", true);

        $parents = getEntityRelatives ('parents', 'object', $object_id);
        $allParentsOut = array();
        foreach ($parents as $link_id => $parent_details)
        {
            if (!isset($label))
                $label = count($parents) > 1 ? 'Containers:' : 'Container:';
            $allParentsOut[] = array('label' => $label, 'mkA' => mkA ($parent_details['name'], 'object', $parent_details['entity_id']),
                                     'parentsOpLink' => getOpLink (array('op'=>'unlinkObjects', 'link_id'=>$link_id), '', 'cut', 'Unlink container'));

            $label = '&nbsp;';
        }
        $mod->addOutput("allParents", $allParentsOut);

        $helper_args = array ('object_id' => $object_id);
        $mod->addOutput('ObjID', $object_id);
    }

    // optional attributes
    $i = 0;
    $values = getAttrValuesSorted ($object_id);
    if (count($values) > 0)
    {
        $mod->addOutput("areValues", true);

        foreach ($values as $record)
        {
            if (! permitted (NULL, NULL, NULL, array (
                                 array ('tag' => '$attr_' . $record['id']),
                                 array ('tag' => '$any_op'),
                             )))
                continue;
            $singleVal = array('i' => $i, 'id' => $record['id'], 'name' => $record['name']);
            if (strlen ($record['value']))
                $singleVal['value_link'] = getOpLink (array('op'=>'clearSticker', 'attr_id'=>$record['id']), '', 'clear', 'Clear value', 'need-confirmation');
            else
                $singleVal['value_link'] = '&nbsp;';
            if ($record['type'] == 'date')
            {
                $singleVal['dateFormatTime'] = datetimeFormatHint (getConfigVar ('DATETIME_FORMAT'));
            }
            $singleVal['type'] = $record['type'];
            switch ($record['type'])
            {
            case 'uint':
            case 'float':
            case 'string':
                $singleVal['value'] = $record['value'];
                break;
            case 'dict':
                $chapter = readChapter ($record['chapter_id'], 'o');
                $chapter[0] = '-- NOT SET --';
                $chapter = cookOptgroups ($chapter, $object['objtype_id'], $record['key']);
                $singleVal['niftyStr'] = printNiftySelect ($chapter, array ('name' => "${i}_value"), $record['key']);
            
                break;
            case 'date':
                $date_value = $record['value'] ? datetimestrFromTimestamp ($record['value']) : '';
                $singleVal['date_value'] = $date_value;
                break;
            }
            $allObjMod = $tplm->generatePseudoSubmodule('AllObjValues', $mod, $singleVal);
            $i++;
        }
    }
    $mod->addOutput("i", $i);

    if ($object['has_problems'] == 'yes')
        $mod->addOutput("hasProblems", true);

    getOpLink (array ('op'=>'deleteObject', 'page'=>'depot', 'tab'=>'addmore', 'object_id'=>$object_id), ''
               ,'destroy', 'Delete object', 'need-confirmation', $mod, 'deleteObjLink');
    getOpLink (array ('op'=>'resetObject'), '' ,'clear', 'Reset (cleanup) object',
               'need-confirmation', $mod, 'resObjLink');
    $mod->addOutput("Obj_comment", $object['comment']);

    renderObjectHistory ($object_id, $mod, 'objectHistoryMod');
}

function renderEditRackForm ($rack_id)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RackEditor');
    $mod->setNamespace('rack');

    global $pageno;
    $rack = spotEntity ('rack', $rack_id);
    amplifyCell ($rack);

    foreach (getAllRows () as $row_id => $rowInfo)
    {
        $trail = getLocationTrail ($rowInfo['location_id'], FALSE);
        $rows[$row_id] = empty ($trail) ? $rowInfo['name'] : $rowInfo['name'] . ' [' . $trail . ']';
    }

    natcasesort ($rows);
    getSelect ($rows, array ('name' => 'row_id'), $rack['row_id'], TRUE, $mod, 'RowSelect');
    $mod->addOutput('Name', $rack['name']);
    $mod->addOutput('Height', $rack['height']);
    $mod->addOutput('AssetTag', $rack['asset_no']);
    printTagsPicker (null, $mod);

    // optional attributes
    $values = getAttrValuesSorted ($rack_id);
    $num_attrs = count($values);
    $num_attrs = $num_attrs-2; // subtract for the 'height' and 'sort_order' attributes
    $mod->addOutput('NumAttrs', $num_attrs);
    $i = 0;
    foreach ($values as $record)
    {
        // Skip the 'height' attribute as it's already displayed as a required field
        // Also skip the 'sort_order' attribute
        if ($record['id'] == 27 or $record['id'] == 29)
            continue;

        $smod = $tplm->generatePseudoSubmodule('ExtraAttrs', $mod);
        $smod->addOutput('Id', $record['id']);
        $smod->addOutput('I', $i);
        $smod->addOutput('Value', $record['value']);
        $smod->addOutput('Name', $record['name']);

        if (strlen ($record['value']))
            $smod->addOutput('Deletable', true);
        //else
        if ($record['type'] == 'dict')
        {
            $chapter = readChapter ($record['chapter_id'], 'o');
            $chapter[0] = '-- NOT SET --';
            $chapter = cookOptgroups ($chapter, 1560, $record['key']);
            printNiftySelect ($chapter, array ('name' => "${i}_value"), $record['key'], false , $mod, 'DictSelect');
            $smod->addOutput('Type', 'dict');
        }

        $i++;
    }
    if ($rack['has_problems'] == 'yes')
        $mod->addOutput('HasProblems', 'checked');
    if ($rack['isDeletable'])
    {
        $mod->addOutput('Deletable', true);
    }
    $mod->addOutput("Rack_Comment", $rack['comment']);

    renderObjectHistory ($rack_id,$mod,'History');
}

// populates the $summary array with the sum of power attributes of the objects mounted into the rack
function populateRackPower ($rackData, &$summary)
{
    $power_attrs = array(
                       7, // 'float','max. current, Ampers'
                       13, // 'float','max power, Watts'
                   );
    $sum = array();
    if (! isset ($rackData['mountedObjects']))
        amplifyCell ($rackData);
    foreach ($rackData['mountedObjects'] as $object_id)
    {
        $attrs = getAttrValues ($object_id);
        foreach ($power_attrs as $attr_id)
            if (isset ($attrs[$attr_id]) && $attrs[$attr_id]['type'] == 'float')
            {
                if (! isset ($sum[$attr_id]))
                {
                    $sum[$attr_id]['sum'] = 0.0;
                    $sum[$attr_id]['name'] = $attrs[$attr_id]['name'];
                }
                $sum[$attr_id]['sum'] += $attrs[$attr_id]['value'];
            }
    }
    foreach ($sum as $attr)
        if ($attr['sum'] > 0.0)
            $summary[$attr['name']] = $attr['sum'];
}

// used by renderGridForm() and renderRackPage()
function renderRackInfoPortlet ($rackData, $parent = null, $placeholder = 'Payload')
{
    $summary = array();
    $summary['Rack row'] = mkA ($rackData['row_name'], 'row', $rackData['row_id']);
    $summary['Name'] = $rackData['name'];
    $summary['Height'] = $rackData['height'];

    if (strlen ($rackData['asset_no']))
        $summary['Asset tag'] = $rackData['asset_no'];
    if ($rackData['has_problems'] == 'yes')
        $summary[] = array ('<tr><td colspan=2 class=msg_error>Has problems</td></tr>');
    populateRackPower ($rackData, $summary);
    // Display populated attributes, but skip 'height' since it's already displayed above
    // and skip 'sort_order' because it's modified using AJAX
    foreach (getAttrValuesSorted ($rackData['id']) as $record)
        if ($record['id'] != 27 && $record['id'] != 29 && strlen ($record['value']))
            $summary['{sticker}' . $record['name']] = formatAttributeValue ($record);
    $summary['% used'] = getProgressBar (getRSUforRack ($rackData));
    $summary['Objects'] = count ($rackData['mountedObjects']);
    $summary['tags'] = '';

    $tplm = TemplateManager::getInstance();

    if ($parent == null)
    {
        $parent = $tplm->getMainModule();
    }

    renderEntitySummary ($rackData, 'summary', $summary, $parent, $placeholder);
    if ($rackData['comment'] != '')
    {
        $tplm->generateSubmodule($placeholder, 'CommentPortlet', $parent, true, array(
                                     'Title' => 'Comment',
                                     'Comment' => string_insert_hrefs ($rackData['comment'])));
    }
}

// This is a universal editor of rack design/waste.
// FIXME: switch to using printOpFormIntro()
function renderGridForm ($rack_id, $filter, $header, $submit, $state1, $state2)
{
    $rackData = spotEntity ('rack', $rack_id);
    amplifyCell ($rackData);
    $filter ($rackData);

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'GridForm');
    $mod->setNamespace('');

    $mod->addOutput('Header', $header);
    $mod->addOutput('Name', $rackData['name']);
    $mod->addOutput('Id', $rack_id);
    $mod->addOutput('Height', $rackData['height']);
    $mod->addOutput('Submit', $submit);

    // Render the result whatever it is.
    // Main layout.

    // Left column with information portlet.
    renderRackInfoPortlet ($rackData,$mod,'InfoPortlet');

    // Grid form.
    markupAtomGrid ($rackData, $state2);
    renderAtomGrid ($rackData, $mod, 'AtomGrid');
}

function renderRackDesign ($rack_id)
{
    renderGridForm ($rack_id, 'applyRackDesignMask', 'Rack design', 'Set rack design', 'A', 'F');
}

function renderRackProblems ($rack_id)
{
    renderGridForm ($rack_id, 'applyRackProblemMask', 'Rack problems', 'Mark unusable atoms', 'F', 'U');
}

function renderObjectPortRow ($port, $is_highlighted, $parent = null, $placeholder = "RenderedObjectPort")
{
    $tplm = TemplateManager::getInstance();

    if($parent==null)
        $mod = $tplm->generateModule('RenderObjectPortRow');
    else
        $mod = $tplm->generateSubmodule($placeholder, 'RenderObjectPortRow', $parent);

    $mod->setNamespace('object');

    // highlight port name with yellow if it's name is not canonical
    $canon_pn = shortenPortName ($port['name'], $port['object_id']);
    $name_class = $canon_pn == $port['name'] ? '' : 'trwarning';

    if ($is_highlighted)
        $mod->addOutput('IsHighlighted', true);

    $a_class = isEthernetPort ($port) ? 'port-menu' : '';

    $mod->addOutput('PortId', $port['id']);
    $mod->setOutput('Name_Class', $name_class);
    $mod->addOutput('AClass', $a_class);
    $mod->addOutput('PortLabel', $port['label']);
    $mod->addOutput('PortName', $port['name']);
    $mod->addOutput('PortL2address', $port['l2address']);
    $mod->addOutput('FormatedPort', formatPortIIFOIF ($port));

    if ($port['remote_object_id'])
    {
        $editable = permitted ('object', 'ports', 'editPort')
                    ? 'editable'
                    : '';

        $mod->addOutput('FormatedPortLink', formatPortLink ($port['remote_object_id'], $port['remote_object_name'], $port['remote_id'], NULL));
        $mod->addOutput('FormatedLoggSpan', formatLoggedSpan ($port['last_log'], $port['remote_name'], 'underline'));
        $mod->addOutput('Editable', $editable);
        $mod->addOutput('PortCableId', $port['cableid']);
    }
    else
        $mod->addOutput('FormatedReservation', implode ('', formatPortReservation ($port)));

    if($parent==null)
        return $mod->run();
}

function renderObject ($object_id)
{
    global $nextorder, $virtual_obj_types;
    $info = spotEntity ('object', $object_id);
    amplifyCell ($info);

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderObject");
    $mod->setNamespace("object");

    // Main layout starts.
    $mod->addOutput("infoDName", $info['dname']);
    // left column with uknown number of portlets

    // display summary portlet
    $summary  = array();
    if (strlen ($info['name']))
        $summary['Common name'] = $info['name'];
    elseif (considerConfiguredConstraint ($info, 'NAMEWARN_LISTSRC'))
        $summary[] = array ('<tr><td colspan=2 class=msg_error>Common name is missing.</td></tr>');
    
    $summary['Object type'] = '<a href="' . makeHref (array (
                                  'page' => 'depot',
                                  'tab' => 'default',
                                  'cfe' => '{$typeid_' . $info['objtype_id'] . '}'
                              )) . '">' .  decodeObjectType ($info['objtype_id'], 'o') . '</a>';
    if (strlen ($info['label']))
        $summary['Visible label'] = $info['label'];
    if (strlen ($info['asset_no']))
        $summary['Asset tag'] = $info['asset_no'];
    elseif (considerConfiguredConstraint ($info, 'ASSETWARN_LISTSRC'))
    $summary[] = array ('<tr><td colspan=2 class=msg_error>Asset tag is missing.</td></tr>');
    $parents = getEntityRelatives ('parents', 'object', $object_id);
    if (count ($parents))
    {
        $fmt_parents = array();
        foreach ($parents as $parent)
            $fmt_parents[] =  "<a href='".makeHref(array('page'=>$parent['page'], $parent['id_name'] => $parent['entity_id']))."'>${parent['name']}</a>";
        $summary[count($parents) > 1 ? 'Containers' : 'Container'] = implode ('<br>', $fmt_parents);
    }
    $children = getEntityRelatives ('children', 'object', $object_id);
    if (count ($children))
    {
        $fmt_children = array();
        foreach ($children as $child)
            $fmt_children[] = "<a href='".makeHref(array('page'=>$child['page'], $child['id_name']=>$child['entity_id']))."'>${child['name']}</a>";
        $summary['Contains'] = implode ('<br>', $fmt_children);
    }
    if ($info['has_problems'] == 'yes')
        $summary[] = array ('<tr><td colspan=2 class=msg_error>Has problems</td></tr>');
    
    foreach (getAttrValuesSorted ($object_id) as $record)
        if
        (
            strlen ($record['value']) and
            permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $record['id'])))
        )
            $summary['{sticker}' . $record['name']] = formatAttributeValue ($record);
    $summary[] = array (getOutputOf ('printTagTRs',
                                     $info,
                                     makeHref
                                     (
                                         array
                                         (
                                                 'page'=>'depot',
                                                 'tab'=>'default',
                                                 'andor' => 'and',
                                                 'cfe' => '{$typeid_' . $info['objtype_id'] . '}',
                                         )
                                     )."&"
                                    ));
    renderEntitySummary ($info, 'summary', $summary, $mod, "InfoSummary");

    if (strlen ($info['comment']))
    {
        $tplm->generateSubmodule('InfoComment', 'CommentPortlet', $mod, true, array(
                                     'Title' => 'Comment',
                                     'Comment' => string_insert_hrefs ($info['comment'])));
    }

    $logrecords = getLogRecordsForObject ($_REQUEST['object_id']);
    if (count ($logrecords))
    {
        $mod->addOutput("areLogRecords", true);

        $order = 'odd';
        $allLogrecordsOut = array();
        foreach ($logrecords as $row)
        {
            $singleRecord = array('order' => $order, 'date' => $row['date'], 'user' => $row['user']);
            $singleRecord['cont'] = string_insert_hrefs (htmlspecialchars ($row['content'], ENT_NOQUOTES));
            $allLogrecordsOut[] = $singleRecord;
            $order = $nextorder[$order];
        }
        $mod->addOutput("allLogrecords", $allLogrecordsOut);
    }

    switchportInfoJS ($object_id, $mod, 'switchportJS'); // load JS code to make portnames interactive
    renderFilesPortlet ('object', $object_id, $mod, "filesPortlet");

    if (count ($info['ports']))
    {
        $mod->addOutput("isInfoPorts", true);
        $hl_port_id = 0;

        if (isset ($_REQUEST['hl_port_id']))
        {
            assertUIntArg ('hl_port_id');
            $hl_port_id = $_REQUEST['hl_port_id'];
            addAutoScrollScript ("port-$hl_port_id");
        }
        foreach ($info['ports'] as $port)
            callHook ('renderObjectPortRow', $port, ($hl_port_id == $port['id']), $mod, 'RenderedObjectPorts');
        if (permitted (NULL, 'ports', 'set_reserve_comment'))
            $mod->addOutput("loadInplaceEdit", true);
    }

    if (count ($info['ipv4']) + count ($info['ipv6']))
    {
        $mod->addOutput("isInfoIP", true);

        if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
            $mod->addOutput("isExt_ipv4_view", true);

        // group IP allocations by interface name instead of address family
        $allocs_by_iface = array();
        foreach (array ('ipv4', 'ipv6') as $ip_v)
            foreach ($info[$ip_v] as $ip_bin => $alloc)
                $allocs_by_iface[$alloc['osif']][$ip_bin] = $alloc;

        // sort allocs array by portnames
        $allPortsOut = array();
        foreach (sortPortList ($allocs_by_iface) as $iface_name => $alloclist)
        {
            $is_first_row = TRUE;
            foreach ($alloclist as $alloc)
            {
                $rendered_alloc = callHook ('getRenderedAlloc', $object_id, $alloc);
                $singlePort = array('tr_class' => $rendered_alloc['tr_class']);

                // display iface name, same values are grouped into single cell
                if ($is_first_row)
                {
                    $rowspan = count ($alloclist) > 1 ? 'rowspan="' . count ($alloclist) . '"' : '';
                    $firstRowMod = $tplm->generateModule('TDLeftCell', true, array(
                            'rowspan' => $rowspan,
                            'cont' => $iface_name . $rendered_alloc['td_name_suffix']));
                    $singlePort['FristMod'] = $firstRowMod->run();
                    $is_first_row = FALSE;
                }
                $singlePort['td_ip'] = $rendered_alloc['td_ip'];
                if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
                {
                    $singlePort['td_network'] = $rendered_alloc['td_network'];
                    $singlePort['td_routed_by'] = $rendered_alloc['td_routed_by'];
                }
                $singlePort['td_peers'] = $rendered_alloc['td_peers'];

                $allPortsOut[] = $singlePort;
            }
        }
        $mod->addOutput("allPorts", $allPortsOut);
    }

    $forwards = $info['nat4'];
    if (count($forwards['in']) or count($forwards['out']))
    {
        $mod->addOutput("isForwarding", true);

        if (count($forwards['out']))
        {
            $mod->addOutput("isFwdOut", true);

            $allFwdsOut = array();
            foreach ($forwards['out'] as $pf)
            {

                $class = 'trerror';
                $osif = '';
                if (isset ($alloclist [$pf['localip']]))
                {
                    $class = $alloclist [$pf['localip']]['addrinfo']['class'];
                    $osif = $alloclist [$pf['localip']]['osif'] . ': ';
                }
                $singleFwd = array('class' => $class, 'proto' => $pf['proto'], 'oisf' => $osif,
                                   'rendLocalIP' => getRenderedIPPortPair ($pf['localip'], $pf['localport']),
                                   'rendRemoteIP' => getRenderedIPPortPair ($pf['remoteip'], $pf['remoteport']));
                $address = getIPAddress (ip4_parse ($pf['remoteip']));
                $singleFwd['mkAs'] = '';
                if (count ($address['allocs']))
                    foreach($address['allocs'] as $bond)
                    $singleFwd['mkAs'] .= mkA ("${bond['object_name']}(${bond['name']})", 'object', $bond['object_id']) . ' ';

                elseif (strlen ($pf['remote_addr_name']))
                {
                    $remoteAddrNameMod = $tplm->generateModule('RoundBracketsMod', true, array('cont' => $pf['remote_addr_name']));
                    $singleFwd['RemAddrName'] = $remoteAddrNameMod->run();
                }
                $singleFwd['description'] = $pf['description'];
                $allFwdsOut[] = $singleFwd;
            }
            $mod->addOutput("allOutFwds", $allFwdsOut);
        }

        if (count($forwards['in']))
        {
            $mod->addOutput("isFwdIn", true);

            $allFwdsOut = array();
            foreach ($forwards['in'] as $pf)
            {
                $singleFwd = array('proto' => $pf['proto'], 'description' => $pf['description'],
                                   'mkA' => mkA ($pf['object_name'], 'object', $pf['object_id']),
                                   'rendLocalIP' => getRenderedIPPortPair ($pf['localip'], $pf['localport']),
                                   'rendRemoteIP' => getRenderedIPPortPair ($pf['remoteip'], $pf['remoteport']));
                $allFwdsOut[] = $singleFwd;
            }
            $mod->addOutput("allInFwds", $allFwdsOut);
        }
    }

    renderSLBTriplets2 ($info, FALSE, NULL, $mod, "SlbTriplet2");
    renderSLBTriplets ($info, $mod, "SlbTriplet");

    // After left column we have (surprise!) right column with rackspace portlet only.
    if (!in_array($info['objtype_id'], $virtual_obj_types))
    {
        $mod->addOutput("isRackspacePortlet", true);

        // rackspace portlet
        foreach (getResidentRacksData ($object_id, FALSE) as $rack_id)
            renderRack ($rack_id, $object_id, $mod, "renderedRackSpace");
    }
}

function renderRackMultiSelect ($sname, $racks, $selected, $parent = null, $placeholder = "rackMultiSelect")
{
    // Transform the given flat list into a list of groups, each representing a rack row.
    $rdata = array();
    foreach ($racks as $rack)
    {
        $trail = getLocationTrail ($rack['location_id'], FALSE);
        if(!empty ($trail))
            $row_name = $trail . ' : ' . $rack['row_name'];
        else
            $row_name = $rack['row_name'];
        $rdata[$row_name][$rack['id']] = $rack['name'];
    }

    $tplm = TemplateManager::getInstance();
    
    if($parent==null)
        $mod = $tplm->generateModule("RenderRackMultiSelect");
    else
        $mod = $tplm->generateSubmodule($placeholder, "RenderRackMultiSelect", $parent);

    $mod->setNamespace("object");
    $mod->addOutput("sname", $sname);
    $mod->addOutput("maxselsize", getConfigVar ('MAXSELSIZE'));

    $row_names = array_keys ($rdata);
    natsort ($row_names);
    $allRowDataOut = array();
    foreach ($row_names as $optgroup)
    {
        $singleOptGroup = array('GroupLabel' => $optgroup);
        $singleOptGroup['RackEntries'] = '';
        foreach ($rdata[$optgroup] as $rack_id => $rack_name)
        {
            $singleOptGroup['RackEntries'] .= $tplm->generateModule('StdOptionTemplate', true,
                                              array(	'RackId' => $rack_id,
                                                      'IsSelected' => ((array_search ($rack_id, $selected) === FALSE) ? '' : 'selected'),
                                                      'RackName' => $rack_name))->run();
        }
        $allRowDataOut[] = $singleOptGroup;
    }
    $mod->addOutput("allRowData", $allRowDataOut);

    if($parent==null)
        return $mod->run();
}

// This function renders a form for port edition.
function renderPortsForObject ($object_id)
{
    $prefs = getPortListPrefs();
    function printNewItemTR ($prefs, $parent, $placeholder)
    {
        $tplm = TemplateManager::getInstance();

        $mod = $tplm->generateSubmodule($placeholder,"RenderPortsForObject_printNew", $parent);
        $mod->setNamespace("object");

        printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type_id', 'tabindex' => 102), $prefs['selected'], false, $mod, "niftySel");
    }

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderPortsForObject");
    $mod->setNamespace("object");

    if (getConfigVar('ENABLE_MULTIPORT_FORM') == 'yes' || getConfigVar('ENABLE_BULKPORT_FORM') == 'yes' )
        $mod->setOutput("isEnableMultiport", true);

    //else
    $object = spotEntity ('object', $object_id);
    amplifyCell ($object);
    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes' && getConfigVar('ENABLE_BULKPORT_FORM') == 'yes')
    {
        $mod->addOutput("isAddnewTop", true);
        printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type_id', 'tabindex' => 107), $prefs['selected'], false, $mod, 'niftySelAddNewT');
    }

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        printNewItemTR ($prefs, $mod, "AddNewTopMod");
    
    // clear ports link
    getOpLink (array ('op'=>'deleteAll'), 'Clear port list', 'clear', '', 'need-confirmation', $mod, 'clearPortLink');

    // rename ports link
    $n_ports_to_rename = 0;
    foreach ($object['ports'] as $port)
        if ($port['name'] != shortenPortName ($port['name'], $object['id']))
            $n_ports_to_rename++;
    if ($n_ports_to_rename)
        echo '<p>' . getOpLink (array ('op'=>'renameAll'), "Auto-rename $n_ports_to_rename ports", 'recalc', 'Use RackTables naming convention for this device type') . '</p>';

    if (isset ($_REQUEST['hl_port_id']))
    {
        assertUIntArg ('hl_port_id');
        $hl_port_id = intval ($_REQUEST['hl_port_id']);
        addAutoScrollScript ("port-$hl_port_id");
    }
    switchportInfoJS ($object_id, $mod, 'switchPortJS'); // load JS code to make portnames interactive

    foreach ($object['ports'] as $port)
    {
        // highlight port name with yellow if it's name is not canonical
        $canon_pn = shortenPortName ($port['name'], $port['object_id']);
        $name_class = $canon_pn == $port['name'] ? '' : 'trwarning';

        $tr_class = isset ($hl_port_id) && $hl_port_id == $port['id'] ? 'class="highlight"' : '';
        $singlePort = array('port_id' => $port['id'], 'href_process' => makeHrefProcess(array('op'=>'delPort', 'port_id'=>$port['id'])),
                            'port_name' => $port['name'], 'port_label' => $port['label'], 'tr_class' => $tr_class);

        $singlePort['opFormIntro'] = printOpFormIntro ('editPort', array ('port_id' => $port['id']));
        $singlePort['deleteImg'] = printImageHREF ('delete', 'Unlink and Delete this port');
        $a_class = isEthernetPort ($port) ? 'port-menu' : '';
        $singlePort['Name_Class'] = $name_class;
        $singlePort['a_class'] = $a_class;

        if ($port['iif_id'] != 1)
            $singlePort['iif_name'] = $port['iif_name'];
        $singlePort['printSelExType'] = printSelect (getExistingPortTypeOptions ($port['id']), array ('name' => 'port_type_id'), $port['oif_id']);
      
        // 18 is enough to fit 6-byte MAC address in its longest form,
        // while 24 should be Ok for WWN
        $singlePort['l2address'] = $port['l2address'];
        if ($port['remote_object_id'])
        {
            $singlePort['isRemoteObj'] = true;
            $singlePort['logged_span_rem_obj_id'] = formatLoggedSpan ($port['last_log'], formatPortLink ($port['remote_object_id'], $port['remote_object_name'], $port['remote_id'], NULL));
            $singlePort['logged_span_rem_name'] = formatLoggedSpan ($port['last_log'], $port['remote_name'], 'underline');
            $singlePort['cableid'] = $port['cableid'];
            $singlePort['unlink_op_link'] = getOpLink (array('op'=>'unlinkPort', 'port_id'=>$port['id'], ), '', 'cut', 'Unlink this port');
        }
        elseif (strlen ($port['reservation_comment']))
        {
            $singlePort['hasReservation_comment'] = true;
            $singlePort['logged_span_rem_reserved'] = formatLoggedSpan ($port['last_log'], 'Reserved:', 'strong underline');
            $singlePort['reservation_comment'] = $port['reservation_comment'];
            $singlePort['use_up_op_link'] = getOpLink (array('op'=>'useup', 'port_id'=>$port['id']), '', 'clear', 'Use up this port');
        }
        else
        {

            $in_rack = getConfigVar ('NEAREST_RACKS_CHECKBOX');
            $helper_args = array
                           (
                               'port' => $port['id'],
                               'in_rack' => ($in_rack == "yes" ? "on" : "")
                           );
            $singlePort['href_helper_portlist'] = makeHrefForHelper ('portlist', $helper_args);
            $singlePort['link_img'] = printImageHREF ('plug', 'Link this port');
        }
        $singlePort['save_img'] = printImageHREF ('save', 'Save changes', TRUE);
        $singlePortMod = $tplm->generateSubmodule('singlePorts', 'RenderPortsForObject_SinglePort', $mod, false, $singlePort);
        $singlePortMod->setNamespace('object');
    }

    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
        printNewItemTR ($prefs, $mod, 'AddNewTopMod2');
    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes' && getConfigVar('ENABLE_BULKPORT_FORM') == 'yes')
    {
        $mod->addOutput("isBulkportForm", true);
        printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type_id', 'tabindex' => 107), $prefs['selected'], false, $mod, 'bulkPortsNiftySel');
    }
    if (getConfigVar('ENABLE_MULTIPORT_FORM') == 'yes')
    {
        $mod->addOutput("isShowAddMultiPorts", true);
    }
    else
    {
        $mod->addOutput("isShowAddMultiPorts", false);
        return;
    }

    printNiftySelect (getNewPortTypeOptions(), array ('name' => 'port_type', 'tabindex' => 202), $prefs['selected'], false, $mod, 'portTypeNiftySel');
}

function renderIPForObject ($object_id)
{
    global $aat;

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderIPForObject");
    $mod->setNamespace("object");

    if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
    {
        $mod->addOutput("isExt_ipv4", true);
    }

    $alloc_list = ''; // most of the output is stored here
    $used_alloc_types = array();
    foreach (getObjectIPAllocations ($object_id) as $alloc)
    {
        if (! isset ($used_alloc_types[$alloc['type']]))
            $used_alloc_types[$alloc['type']] = 0;
        $used_alloc_types[$alloc['type']]++;

        $rendered_alloc = callHook ('getRenderedAlloc', $object_id, $alloc);

        $alloc_elem_mod = $tplm->generateSubmodule("alloc_elems", "RenderIPForObject_Alloc_Element", $mod);
        $alloc_elem_mod->setNamespace('object');
        $alloc_elem_mod->setOutput('addrinfo_ip', $alloc['addrinfo']['ip']);
        $alloc_elem_mod->setOutput('tr_class', $rendered_alloc['tr_class']);
        $alloc_elem_mod->setOutput('td_name_suffix', $rendered_alloc['td_name_suffix']);
        $alloc_elem_mod->setOutput('osif', $alloc['osif']);
        $alloc_elem_mod->setOutput('td_ip', $rendered_alloc['td_ip']);

        if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
        {
            $alloc_elem_mod->setOutput('isExt_ipv4', true);
            $alloc_elem_mod->setOutput('td_network', $rendered_alloc['td_network']);
            $alloc_elem_mod->setOutput('td_routed_by', $rendered_alloc['td_routed_by']);
        }
        printSelect($aat, array ('name' => 'bond_type'), $alloc['type'], $alloc_elem_mod, 'bond_type_mod');
        $alloc_elem_mod->setOutput('td_peers', $rendered_alloc['td_peers']);
    }
    asort ($used_alloc_types, SORT_NUMERIC);
    $most_popular_type = empty ($used_alloc_types) ? 'regular' : array_last (array_keys ($used_alloc_types));

    if ($list_on_top = (getConfigVar ('ADDNEW_AT_TOP') != 'yes'))
        $mod->addOutput("isAddNewOnTop", true);

    $newmod = $tplm->generatePseudoSubmodule("RenderIPForObject_printNew", $mod);

    if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
        $newmod->addOutput("isExt_ipv4", true);
    printSelect ($aat, array ('name' => 'bond_type', 'tabindex' => 102), $most_popular_type, $newmod, "bondPrintSel"); // type
}

// This function is deprecated. Do not rely on its internals,
// it will probably be removed in the next major relese.
// Use new showError, showWarning, showSuccess functions.
// Log array is stored in global $log_messages. Its format is simple: plain ordered array
// with values having keys 'c' (both message code and severity) and 'a' (sprintf arguments array)
function showMessageOrError ($tpl = false)
{
    global $log_messages;

    @session_start();
    if (isset ($_SESSION['log']))
    {
        $log_messages = array_merge ($_SESSION['log'], $log_messages);
        unset ($_SESSION['log']);
    }
    session_commit();

    if (empty ($log_messages))
        return;
    $msginfo = array
               (
// records 0~99 with success messages
                   0 => array ('code' => 'success', 'format' => '%s'),
                   5 => array ('code' => 'success', 'format' => 'added record "%s" successfully'),
                   6 => array ('code' => 'success', 'format' => 'updated record "%s" successfully'),
                   7 => array ('code' => 'success', 'format' => 'deleted record "%s" successfully'),
                   8 => array ('code' => 'success', 'format' => 'Port %s successfully linked with %s'),
                   10 => array ('code' => 'success', 'format' => 'Added %u ports, updated %u ports, encountered %u errors.'),
                   21 => array ('code' => 'success', 'format' => 'Generation complete'),
                   26 => array ('code' => 'success', 'format' => 'updated %u records successfully'),
                   37 => array ('code' => 'success', 'format' => 'added %u records successfully'),
                   38 => array ('code' => 'success', 'format' => 'removed %u records successfully'),
                   43 => array ('code' => 'success', 'format' => 'Saved successfully.'),
                   44 => array ('code' => 'success', 'format' => '%s failures and %s successfull changes.'),
                   48 => array ('code' => 'success', 'format' => 'added a record successfully'),
                   49 => array ('code' => 'success', 'format' => 'deleted a record successfully'),
                   51 => array ('code' => 'success', 'format' => 'updated a record successfully'),
                   57 => array ('code' => 'success', 'format' => 'Reset complete'),
                   63 => array ('code' => 'success', 'format' => '%u change request(s) have been processed'),
                   67 => array ('code' => 'success', 'format' => "Tag rolling done, %u objects involved"),
                   71 => array ('code' => 'success', 'format' => 'File "%s" was linked successfully'),
                   72 => array ('code' => 'success', 'format' => 'File was unlinked successfully'),
                   82 => array ('code' => 'success', 'format' => "Bulk port creation was successful. %u ports created, %u failed"),
                   87 => array ('code' => 'success', 'format' => '802.1Q recalculate: %d ports changed on %d switches'),
// records 100~199 with fatal error messages
                   100 => array ('code' => 'error', 'format' => '%s'),
                   109 => array ('code' => 'error', 'format' => 'failed updating a record'),
                   131 => array ('code' => 'error', 'format' => 'invalid format requested'),
                   141 => array ('code' => 'error', 'format' => 'Encountered %u errors, updated %u record(s)'),
                   149 => array ('code' => 'error', 'format' => 'Turing test failed'),
                   150 => array ('code' => 'error', 'format' => 'Can only change password under DB authentication.'),
                   151 => array ('code' => 'error', 'format' => 'Old password doesn\'t match.'),
                   152 => array ('code' => 'error', 'format' => 'New passwords don\'t match.'),
                   154 => array ('code' => 'error', 'format' => "Verification error: %s"),
                   155 => array ('code' => 'error', 'format' => 'Save failed.'),
                   159 => array ('code' => 'error', 'format' => 'Permission denied moving port %s from VLAN%u to VLAN%u'),
                   161 => array ('code' => 'error', 'format' => 'Endpoint not found. Please either set FQDN attribute or assign an IP address to the object.'),
                   162 => array ('code' => 'error', 'format' => 'More than one IP address is assigned to this object, please configure FQDN attribute.'),
                   170 => array ('code' => 'error', 'format' => 'There is no network for IP address "%s"'),
                   172 => array ('code' => 'error', 'format' => 'Malformed request'),
                   179 => array ('code' => 'error', 'format' => 'Expired form has been declined.'),
                   188 => array ('code' => 'error', 'format' => "Fatal SNMP failure"),
                   189 => array ('code' => 'error', 'format' => "Unknown OID '%s'"),
                   191 => array ('code' => 'error', 'format' => "deploy was blocked due to conflicting configuration versions"),

// records 200~299 with warnings
                   200 => array ('code' => 'warning', 'format' => '%s'),
                   201 => array ('code' => 'warning', 'format' => 'nothing happened...'),
                   206 => array ('code' => 'warning', 'format' => '%s is not empty'),
                   207 => array ('code' => 'warning', 'format' => 'File upload failed, error: %s'),

// records 300~399 with notices
                   300 => array ('code' => 'neutral', 'format' => '%s'),

               );

    $tplm = TemplateManager::getInstance();
    // Handle the arguments. Is there any better way to do it?
    foreach ($log_messages as $record)
    {
        if (!isset ($record['c']) or !isset ($msginfo[$record['c']]))
        {
            $prefix = isset ($record['c']) ? $record['c'] . ': ' : '';
            $tplm->generateSubmodule("Message","MessageNeutral",null,true,array("Message"=>"(${prefix}this message was lost)"));
            
            continue;
        }
        if (isset ($record['a']))
            switch (count ($record['a']))
            {
            case 1:
                $msgtext = sprintf
                           (
                               $msginfo[$record['c']]['format'],
                               $record['a'][0]
                           );
                break;
            case 2:
                $msgtext = sprintf
                           (
                               $msginfo[$record['c']]['format'],
                               $record['a'][0],
                               $record['a'][1]
                           );
                break;
            case 3:
                $msgtext = sprintf
                           (
                               $msginfo[$record['c']]['format'],
                               $record['a'][0],
                               $record['a'][1],
                               $record['a'][2]
                           );
                break;
            case 4:
            default:
                $msgtext = sprintf
                           (
                               $msginfo[$record['c']]['format'],
                               $record['a'][0],
                               $record['a'][1],
                               $record['a'][2],
                               $record['a'][3]
                           );
                break;
            }
        else
            $msgtext = $msginfo[$record['c']]['format'];
        
        $modname = "Message" . ucfirst($msginfo[$record['c']]['code']);
        $tplm->generateSubmodule("Message",$modname,null,true,array("Message"=>$msgtext));       
    }
    $log_messages = array();
}

// renders two tables: port link status and learned MAC list
function renderPortsInfo($object_id)
{
    try
    {
        if (permitted (NULL, NULL, 'get_link_status'))
            $linkStatus = queryDevice ($object_id, 'getportstatus');
        else
            showWarning ("You don't have permission to view ports link status");

        if (permitted (NULL, NULL, 'get_mac_list'))
            $macList = sortPortList (queryDevice ($object_id, 'getmaclist'));
        else
            showWarning ("You don't have permission to view learned MAC list");
    }
    catch (RTGatewayError $e)
    {
        showError ($e->getMessage());
        return;
    }

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderPortsInfo");
    $mod->setNamespace("object");

    global $nextorder;
    if (! empty ($linkStatus))
    {
        $mod->addOutput("isLinkStatus", true);

        $order = 'even';
        $allLinkStatusOut = array();
        foreach ($linkStatus as $pn => $link)
        {
            switch ($link['status'])
            {
            case 'up':
                $img_filename = 'link-up.png';
                break;
            case 'down':
                $img_filename = 'link-down.png';
                break;
            case 'disabled':
                $img_filename = 'link-disabled.png';
                break;
            default:
                $img_filename = '1x1t.gif';
            }
            $singleLinkStatus = array('order' => $order, 'img_filename' => $img_filename, 'pn' => $pn,
                                      'linkStatus' => $link['status'] );
            $order = $nextorder[$order];
            $info = '';
            if (isset ($link['speed']))
                $info .= $link['speed'];
            if (isset ($link['duplex']))
            {
                if (! empty ($info))
                    $info .= ', ';
                $info .= $link['duplex'];
            }
            $singleLinkStatus['info'] = $info;
            $allLinkStatusOut[] = $singleLinkStatus;
        }
        $mod->addOutput("allLinkStatus", $allLinkStatusOut);
    }

    if (! empty ($macList))
    {
        $mod->addOutput("hasMacList", true);
        $rendered_macs = '';
        $mac_count = 0;
        
        $order = 'even';
        $allMacsOut = array();
        foreach ($macList as $pn => $list)
        {
            $order = $nextorder[$order];
            foreach ($list as $item)
            {
                ++$mac_count;
                $allMacsOut[] = array('item' => $item['mac'], 'vid' => $item['vid'], 'pn' => $pn, 'order' => $order);
            }
        }
        $mod->addOutput("allMacs", $allMacsOut);
        $mod->addOutput("macCount", $mac_count);

    }

}

/*
The following conditions must be met:
1. We can mount onto free atoms only. This means: if any record for an atom
already exists in RackSpace, it can't be used for mounting.
2. We can't unmount from 'W' atoms. Operator should review appropriate comments
and either delete them before unmounting or refuse to unmount the object.
*/
function renderRackSpaceForObject ($object_id)
{
    // Always process occupied racks plus racks chosen by user. First get racks with
    // already allocated rackspace...
    $workingRacksData = getResidentRacksData ($object_id);
    // ...and then add those chosen by user (if any).
    if (isset($_REQUEST['rackmulti']))
        foreach ($_REQUEST['rackmulti'] as $cand_id)
            if (!isset ($workingRacksData[$cand_id]))
            {
                $rackData = spotEntity ('rack', $cand_id);
                amplifyCell ($rackData);
                $workingRacksData[$cand_id] = $rackData;
            }

    // Get a list of all of this object's parents,
    // then trim the list to only include parents that are racks
    $objectParents = getEntityRelatives('parents', 'object', $object_id);
    $parentRacks = array();
    foreach ($objectParents as $parentData)
        if ($parentData['entity_type'] == 'rack')
            $parentRacks[] = $parentData['entity_id'];

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderRackSpaceForObject");
    $mod->setNamespace("object");


    // Main layout starts.
    // Left portlet with rack list.
    $allRacksData = listCells ('rack');

    // filter rack list to match only racks having common tags with the object (reducing $allRacksData)
    if (! isset ($_REQUEST['show_all_racks']) and getConfigVar ('FILTER_RACKLIST_BY_TAGS') == 'yes')
    {
        $matching_racks = array();
        $object = spotEntity ('object', $object_id);
        $matched_tags = array();
        foreach ($allRacksData as $rack)
            foreach ($object['etags'] as $tag)
                if (tagOnChain ($tag, $rack['etags']) or tagOnChain ($tag, $rack['itags']))
                {
                    $matching_racks[$rack['id']] = $rack;
                    $matched_tags[$tag['id']] = $tag;
                    break;
                }
        // add current object's racks even if they dont match filter
        foreach ($workingRacksData as $rack_id => $rack)
            if (! isset ($matching_racks[$rack_id]))
                $matching_racks[$rack_id] = $rack;
        // if matching racks found, and rack list is reduced, show 'show all' link
        if (count ($matching_racks) and count ($matching_racks) != count ($allRacksData))
        {
            $filter_text = '';
            foreach ($matched_tags as $tag)
                $filter_text .= (empty ($filter_text) ? '' : ' or ') . '{' . $tag['tag'] . '}';
            $href_show_all = trim($_SERVER['REQUEST_URI'], '&');
            $href_show_all .= htmlspecialchars('&show_all_racks=1');
            $mod->addOutput("isShowAllAndMatching", true);
            $mod->addOutput("filter_text", $filter_text);
            $mod->addOutput("href_show_all", $href_show_all);
            $allRacksData = $matching_racks;
        }
    }

    if (count ($allRacksData) <= getConfigVar ('RACK_PRESELECT_THRESHOLD'))
        foreach ($allRacksData as $rack)
            if (!array_key_exists ($rack['id'], $workingRacksData))
            {
                amplifyCell ($rack);
                $workingRacksData[$rack['id']] = $rack;
            }
    foreach (array_keys ($workingRacksData) as $rackId)
        applyObjectMountMask ($workingRacksData[$rackId], $object_id);
    renderRackMultiSelect ('rackmulti[]', $allRacksData, array_keys ($workingRacksData), $mod, "RackMultiSet");

    // Right portlet with rendered racks. If this form submit is not final, we have to
    // reflect the former state of the grid in current form.
    includeJQueryUI (false, $mod, 'jquery_code');
    $allWorkingDataOut = array();
    foreach ($workingRacksData as $rack_id => $rackData)
    {
        // Order is important here: only original allocation is highlighted.
        highlightObject ($rackData, $object_id);
        markupAtomGrid ($rackData, 'T');
        // If we have a form processed, discard user input and show new database
        // contents.
        if (isset ($_REQUEST['rackmulti'][0])) // is an update
            mergeGridFormToRack ($rackData);

        $singleDataSet = array('name' => $rackData['name'],
                               'rack_id' => $rack_id,
                               'height' => $rackData['height'],
                               'AtomGrid' => renderAtomGrid ($rackData) );
        // Determine zero-u checkbox status.
        // If form has been submitted, use form data, otherwise use DB data.
        if (isset($_REQUEST['op']))
            $checked = isset($_REQUEST['zerou_'.$rack_id]) ? 'checked' : '';
        else
            $checked = in_array($rack_id, $parentRacks) ? 'checked' : '';
        $singleDataSet['checked'] = $checked;

        $allWorkingDataOut[] = $singleDataSet;
    }
    $mod->addOutput("allWorkingData", $allWorkingDataOut);
}

function renderMolecule ($mdata, $object_id, $parent = null, $placeholder = 'RenderedMolecule')
{
    // sort data out
    $rackpack = array();
    global $loclist;
    foreach ($mdata as $rua)
    {
        $rack_id = $rua['rack_id'];
        $unit_no = $rua['unit_no'];
        $atom = $rua['atom'];
        if (!isset ($rackpack[$rack_id]))
        {
            $rackData = spotEntity ('rack', $rack_id);
            amplifyCell ($rackData);
            for ($i = $rackData['height']; $i > 0; $i--)
                for ($locidx = 0; $locidx < 3; $locidx++)
                    $rackData[$i][$locidx]['state'] = 'F';
            $rackpack[$rack_id] = $rackData;
        }
        $rackpack[$rack_id][$unit_no][$loclist[$atom]]['state'] = 'T';
        $rackpack[$rack_id][$unit_no][$loclist[$atom]]['object_id'] = $object_id;
    }
    // now we have some racks to render

    $tplm = TemplateManager::getInstance();

    if($parent==null)
        $containerMod = $tplm->generateModule("GlobalPlaceholder", true);

    foreach ($rackpack as $rackData)
    {
        markAllSpans ($rackData);
        if($parent == null)
        {
            $mod = $tplm->generateSubmodule('Cont', 'RenderMolecule', $containerMod);
        }
        else
            $mod = $tplm->generateSubmodule($placeholder, 'RenderMolecule', $parent);

        $mod->setOutput('RackName', $rackData['name']);
        $allRowsOut = array();
        for ($i = $rackData['height']; $i > 0; $i--)
        {
            $atomsStates = '';
            for ($locidx = 0; $locidx < 3; $locidx++)
            {
                $state = $rackData[$i][$locidx]['state'];
                $atomsStates .= $tplm->generateModule('TdAtomState', true, array('State' =>  $state))->run();
            }
            $allRowsOut[] = array('AllLocs' => $atomsStates, 'InverseRack' => inverseRackUnit ($i, $rackData));
        }
        $mod->setOutput('AllRows', $allRowsOut);
    }

    if($parent==null)
        return $containerMod->run();
}

function renderDepot ()
{
    global $pageno, $nextorder;
    $cellfilter = getCellFilter();
    $objects = array();
    $objects_count = getEntitiesCount ('object');

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderDepot");
    $mod->setNamespace("depot",true);

    if ($objects_count == 0)
        $mod->addOutput("NoObjects", true);
    // 1st attempt: do not fetch all objects if cellfilter is empty and rendering empty result is enabled
    elseif (! ($cellfilter['is_empty'] && renderEmptyResults ($cellfilter, 'objects', $objects_count, $mod, 'Content')))
    {
        $objects = applyCellFilter ('object', $cellfilter);
        // 2nd attempt: do not render all fetched objects if rendering empty result is enabled
        if (! renderEmptyResults ( $cellfilter, 'objects', count($objects), $mod, 'Content'))
        {
            $mod->setOutput("CountObjs", count($objects));

            $order = 'odd';
# gather IDs of all objects and fetch rackspace info in one pass
            $idlist = array();
            foreach ($objects as $obj)
                $idlist[] = $obj['id'];
            $mountinfo = getMountInfo ($idlist);
            $containerinfo = getContainerInfo ($idlist);

            $objectsOutArray = array();
            foreach ($objects as $obj)
            {
                $problem = ($obj['has_problems'] == 'yes') ? 'has_problems' : '';
                $singleObj = array('Order' => $order,
                                   'Mka' => mkA ("<strong>${obj['dname']}</strong>",'object', $obj['id']),
                                   'Problem' => $problem );

                if (count ($obj['etags']))
                {
                    $tagsLineMod = $tplm->generateModule('ETagsLine',true, array(
                            'cont' => serializeTags ($obj['etags'], makeHref(array('page'=>$pageno, 'tab'=>'default')) . '&')));
                    $singleObj["RenderedTags"] = $tagsLineMod->run();
                }

                $singleObj['Label']	= $obj['label'];
                $singleObj['Asset_no']	= $obj['asset_no'];

                $places = array();
                if (array_key_exists ($obj['id'], $containerinfo))
                    foreach ($containerinfo[$obj['id']] as $ci)
                        $places[] = mkA ($ci['container_dname'], 'object', $ci['container_id']);
                if (array_key_exists ($obj['id'], $mountinfo))
                    foreach ($mountinfo[$obj['id']] as $mi)
                        $places[] = mkA ($mi['row_name'], 'row', $mi['row_id']) . '/' . mkA ($mi['rack_name'], 'rack', $mi['rack_id']);
                if (! count ($places))
                    $places[] = 'Unmounted';
                $singleObj["Places"] = implode (', ', $places);
                $order = $nextorder[$order];
                $objectsOutArray[] = $singleObj;
            }
            $mod->setOutput("AllObjects", $objectsOutArray);
        }
    }

    renderCellFilterPortlet ($cellfilter, 'object', $objects, array(), $mod);
}

// This function returns TRUE if the result set is too big to be rendered, and no filter is set.
// In this case it renders the describing message instead.
function renderEmptyResults($cellfilter, $entities_name, $count = NULL, $pmod = null, $placeholder = '')
{
    if (!$cellfilter['is_empty'])
        return FALSE;
    if (isset ($_REQUEST['show_all_objects']))
        return FALSE;
    $max = intval(getConfigVar('MAX_UNFILTERED_ENTITIES'));
    if (0 == $max || $count <= $max)
        return FALSE;

    $href_show_all = trim($_SERVER['REQUEST_URI'], '&');
    $href_show_all .= htmlspecialchars('&show_all_objects=1');

    $tplm = TemplateManager::getInstance();
    if($pmod==null)
        $mod = $tplm->generateModule("EmptyResults",   true);
    else
        $mod = $tplm->generateSubmodule($placeholder, "EmptyResults", $pmod, true);

    $suffix = isset ($count) ? " ($count)" : '';
    $mod->addOutput("Name", $entities_name);
    $mod->addOutput("Suffix", $suffix);
    $mod->addOutput("ShowAll", $href_show_all);

    if($pmod==null)
        $mod->run();
    return TRUE;
}

// History viewer for history-enabled simple dictionaries.
function renderObjectHistory ($object_id, $parent = NULL, $placeholder = 'History')
{
    $tplm = TemplateManager::getInstance();

    if ($parent == null)
    {
        $mod = $tplm->generateModule('ObjectHistory');
    }
    else
    {
        $mod = $tplm->generateSubmodule($placeholder, 'ObjectHistory', $parent);
    }
    $mod->defNamespace();

    $order = 'odd';
    global $nextorder;

    $result = usePreparedSelectBlade
              (
                  'SELECT ctime, user_name, name, label, asset_no, has_problems, comment FROM ObjectHistory WHERE id=? ORDER BY ctime',
                  array ($object_id)
              );
    $output = array();
    while ($row = $result->fetch (PDO::FETCH_NUM))
    {
        $row['Order'] = $order;
        $output[] = $row;
        $order = $nextorder[$order];
    }
    $mod->addOutput('History', $output);
}

function renderRackspaceHistory ()
{
    global $nextorder, $pageno, $tabno;
    $order = 'odd';
    $history = getRackspaceHistory();
    // Show the last operation by default.
    if (isset ($_REQUEST['op_id']))
        $op_id = $_REQUEST['op_id'];
    elseif (isset ($history[0]['mo_id']))
    $op_id = $history[0]['mo_id'];
    else $op_id = NULL;

    $omid = NULL;
    $nmid = NULL;
    $object_id = 1;
    if ($op_id)
        list ($omid, $nmid) = getOperationMolecules ($op_id);

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","renderRackspaceHistory");
    $mod->setNamespace("rackspace",true);

    // Main layout starts.
    // Left top portlet with old allocation.
    if ($omid)
    {
        $oldMolecule = getMolecule ($omid);
        //renderMolecule ($oldMolecule, $object_id)
        $mod->setOutput("OldAlloc", renderMolecule ($oldMolecule, $object_id ));
    }
    else
        $mod->setOutput("OldAlloc","nothing");

    // Right top portlet with new allocation
    if ($nmid)
    {
        $newMolecule = getMolecule ($nmid);
        $mod->setOutput("NewAlloc",renderMolecule ($newMolecule, $object_id));
    }
    else
        $mod->setOutput("NewAlloc","nothing");

    // Bottom portlet with list
    foreach ($history as $row)
    {
        $smod = $tplm->generateSubmodule("HistoryRows","RackspaceHistoryRow",$mod);
        if ($row['mo_id'] == $op_id)
            $class = 'hl';
        else
            $class = "row_" . $order;
        $smod->addOutput("Class",$class);
        $smod->addOutput("Link",makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'op_id'=>$row['mo_id'])));
        $smod->addOutput("Time",$row['ctime']);
        $smod->addOutput("UserName",$row['user_name']);
        $smod->addOutput("RenderedCell",renderCell (spotEntity ('object', $row['ro_id'])));
        $smod->addOutput("Comment",$row['comment']);

        $order = $nextorder[$order];
    }

}

function renderIPSpaceRecords ($tree, $baseurl, $target = 0, $level = 1, $parent, $placeholder)
{
    $self = __FUNCTION__;
    $knight = (getConfigVar ('IPV4_ENABLE_KNIGHT') == 'yes');

    $tplm = TemplateManager::getInstance();

    // scroll page to the highlighted item
    if ($target && isset ($_REQUEST['hl_net']))
        addAutoScrollScript ("net-$target");

    foreach ($tree as $item)
    {
        if ($display_routers = (getConfigVar ('IPV4_TREE_RTR_AS_CELL') != 'none'))
            loadIPAddrList ($item); // necessary to compute router list and address counter

        if (isset ($item['id']))
        {
            $mod = $tplm->generateSubmodule($placeholder, 'IPSpaceRecord', $parent);
            $mod->setNamespace('ipspace');

            $decor = array ('indent' => $level);
            if ($item['symbol'] == 'node-collapsed')
                $decor['symbolurl'] = "${baseurl}&eid=${item['id']}&hl_net=1";
            elseif ($item['symbol'] == 'node-expanded')
            $decor['symbolurl'] = $baseurl . ($item['parent_id'] ? "&eid=${item['parent_id']}&hl_net=1" : '');
            $tr_class = '';
            if ($target == $item['id'] && isset ($_REQUEST['hl_net']))
            {
                $decor['tdclass'] = ' highlight';
                $mod->addOutput('Highlight', 'highlight');
            }
            printIPNetInfoTDs ($item, $decor, $mod, 'ItemInfo');

            // capacity and usage
            $mod->addOutput('Capacity', getRenderedIPNetCapacity ($item));

            if ($display_routers)
                printRoutersTD (findRouters ($item['own_addrlist']), getConfigVar ('IPV4_TREE_RTR_AS_CELL'), $mod, 'Routers');
            if ($item['symbol'] == 'node-expanded' or $item['symbol'] == 'node-expanded-static')
                $self ($item['kids'], $baseurl, $target, $level + 1, $parent, $placeholder);
        }
        else
        {
            // non-allocated (spare) IP range
            $mod = $tplm->generateSubmodule($placeholder, 'IPSpaceRecordNoAlloc', $parent);
            $mod->setNamespace('ipspace');

            printIPNetInfoTDs ($item, array ('indent' => $level, 'knight' => $knight, 'tdclass' => 'sparenetwork'), $mod, 'IPNetInfo');

            // capacity and usage
            $mod->addOutput('IPNetCapacity', getRenderedIPNetCapacity ($item));
            if ($display_routers)
                $mod->addOutput('hasRouterCell', $display_routers);
        }
    }
}

function renderIPSpace()
{
    global $pageno, $tabno;
    $realm = ($pageno == 'ipv4space' ? 'ipv4net' : 'ipv6net');
    $cellfilter = getCellFilter();

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'IPSpace');
    $mod->setNamespace('ipspace');

    // expand request can take either natural values or "ALL". Zero means no expanding.
    $eid = isset ($_REQUEST['eid']) ? $_REQUEST['eid'] : 0;
    $netlist = array();

    if (! ($cellfilter['is_empty'] && ! $eid && renderEmptyResults($cellfilter, 'IP nets', getEntitiesCount ($realm), $mod, 'EmptyResults')))
    {
        $top = NULL;
        foreach (listCells ($realm) as $net)
        {
            if (isset ($top) and IPNetContains ($top, $net))
                ;
            elseif (! count ($cellfilter['expression']) or judgeCell ($net, $cellfilter['expression']))
            $top = $net;
            else
                continue;
            $netlist[$net['id']] = $net;
        }
        $netcount = count ($netlist);
        $tree = prepareIPTree ($netlist, $eid);

        if (! renderEmptyResults($cellfilter, 'IP nets', count($tree),$mod,'EmptyResults'))
        {
            $mod->addOutput('hasResults', true);
            $mod->addOutput("NetCount", $netcount);
            $mod->addOutput("TreeThreshold", getConfigVar ('TREE_THRESHOLD'));
            $mod->addOutput('ExpandAll', makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'eid'=>'ALL')) . $cellfilter['urlextra']);
            $mod->addOutput('CollapseAll', makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'eid'=>'NONE')) .	$cellfilter['urlextra']);
            $mod->addOutput('CollapseAuto', makeHref(array('page'=>$pageno, 'tab'=>$tabno)) . $cellfilter['urlextra']);

            if ($eid === 0)
                $mod->addOutput('CollapseExpandOptions', 'allnone');

            elseif ($eid === 'ALL')
            $mod->addOutput('CollapseExpandOptions', 'all');
            elseif ($eid === 'NONE')
            $mod->addOutput('CollapseExpandOptions', 'none');
            else
            {
                try
                {
                    $netinfo = spotEntity ($realm, $eid);
                    $mod->addOutput('ExpandIP', $netinfo['ip']);
                    $mod->addOutput('ExpandMask', $netinfo['mask']);
                }
                catch (EntityNotFoundException $e)
                {
                    // ignore invalid eid error
                }
            }

            if (getConfigVar ('IPV4_TREE_RTR_AS_CELL') != 'none')
                $mod->addOutput('AddRouted', true);
            $baseurl = makeHref(array('page'=>$pageno, 'tab'=>$tabno)) . $cellfilter['urlextra'];
            renderIPSpaceRecords ($tree, $baseurl, $eid, 1, $mod, 'IPRecords');
        }
    }

    renderCellFilterPortlet ($cellfilter, $realm, $netlist, array(), $mod, 'CellFilter');
}

function renderIPSpaceEditor()
{
    global $pageno;
    $realm = ($pageno == 'ipv4space' ? 'ipv4net' : 'ipv6net');
    $net_page = $realm; // 'ipv4net', 'ipv6net'
    $addrspaceList = listCells ($realm);

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderIPSpaceEditor");
    $mod->setNamespace("ipspace");
    $mod->addOutput("countAddrspaceList", count ($addrspaceList));

    if (count ($addrspaceList))
    {
        $mod->addOutput("hasAddrspaceList", true);

        $allNetinfoOut = array();
        foreach ($addrspaceList as $netinfo)
        {
            $singleNetinfo = array( 'mkAIpmask' => mkA ("${netinfo['ip']}/${netinfo['mask']}", $net_page, $netinfo['id']),
                                    'name' => niftyString ($netinfo['name']));
            if (! isIPNetworkEmpty ($netinfo))
                $singleNetinfo['destroyItem'] = printImageHREF ('nodestroy', 'There are ' . count ($netinfo['addrlist']) . ' allocations inside');
            else
                $singleNetinfo['destroyItem'] = getOpLink (array	('op' => 'del', 'id' => $netinfo['id']), '', 'destroy', 'Delete this prefix');
            if (count ($netinfo['etags']))
            {
                $etagsMod = $tplm->generateModule('ETagsLine', true, array('cont' => serializeTags ($netinfo['etags'])));
                $singleNetinfo['RendTags'] = $etagsMod->run();
            }
            $singleNetinfo['ipnetCap'] = getRenderedIPNetCapacity ($netinfo);
            $allNetinfoOut[] = $singleNetinfo;
        }
        $mod->addOutput("allNetinfo", $allNetinfoOut);
    }
}

function renderIPNewNetForm ()
{
    global $pageno;
    if ($pageno == 'ipv6space')
    {
        $realm = 'ipv6net';
        $regexp = '^[a-fA-F0-9:]*:[a-fA-F0-9:\.]*/\d{1,3}$';
    }
    else
    {
        $realm = 'ipv4net';
        $regexp = '^(\d{1,3}\.){3}\d{1,3}/\d{1,2}$';
    }
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderIPNewNetForm");
    $mod->setNamespace("ipspace");

    // IP prefix validator
    $regexp = addslashes ($regexp);
    $mod->addOutput("regexp", $regexp);

    // tags column
    // inputs column
    $prefix_value = empty ($_REQUEST['set-prefix']) ? '' : $_REQUEST['set-prefix'];
    $mod->addOutput("prefix_value", $prefix_value);
    getOptionTree ('vlan_ck', getAllVLANOptions(), array ('select_class' => 'vertical', 'tabindex' => 2), $mod, 'optionTree');
    printTagsPicker (null, $mod);
}

function getRenderedIPNetBacktrace ($range)
{
    if (getConfigVar ('EXT_IPV4_VIEW') != 'yes')
        return array();

    $v = ($range['realm'] == 'ipv4net') ? 4 : 6;
    $space = "ipv${v}space"; // ipv4space, ipv6space
    $tag = "\$ip${v}netid_"; // $ip4netid_, $ip6netid_

    $ret = array();
    // Build a backtrace from all parent networks.
    $clen = $range['mask'];
    $backtrace = array();
    $backtrace['&rarr;'] = $range;
    $key = '';

    $tplm = TemplateManager::getInstance();
    while (NULL !== ($upperid = getIPAddressNetworkId ($range['ip_bin'], $clen)))
    {
        $upperinfo = spotEntity ($range['realm'], $upperid);
        $clen = $upperinfo['mask'];
        $key .= '&uarr;';
        $backtrace[$key] = $upperinfo;
    }
    foreach (array_reverse ($backtrace) as $arrow => $ainfo)
    {
        $mod = $tplm->generateModule('IPNetBacktraceLink',true,array('Title'=>$arrow));
        $mod->addOutput('Link', makeHref(array (
                                             'page' => $space,
                                             'tab' => 'default',
                                             'clear-cf' => '',
                                             'cfe' => '{' . $tag . $ainfo['id'] . '}',
                                             'hl_net' => 1,
                                             'eid' => $range['id'])));
        $ret[] = array ($mod->run(), renderCell($ainfo)); //getOutputOf ('renderCell', $ainfo));
    }
    return $ret;
}

function renderIPNetwork ($id)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'IPNetwork');
    $mod->setNamespace('ipnetwork');

    global $pageno;
    $realm = $pageno; // 'ipv4net', 'ipv6net'
    $range = spotEntity ($realm, $id);
    loadIPAddrList ($range);

    $mod->addOutput('IP', $range['ip']);
    $mod->addOutput('Mask', $range['mask']);
    $mod->addOutput('Name', htmlspecialchars ($range['name'], ENT_QUOTES, 'UTF-8'));

    // render summary portlet
    $summary = array();
    $summary['%% used'] = getRenderedIPNetCapacity ($range);
    $summary = getRenderedIPNetBacktrace ($range) + $summary;
    if ($realm == 'ipv4net')
    {
        $summary[] = array ('Netmask:', ip4_format ($range['mask_bin']));
        $summary[] = array ('Netmask:', "0x" . strtoupper (implode ('', unpack ('H*', $range['mask_bin']))));
        $summary['Wildcard bits'] = ip4_format ( ~ $range['mask_bin']);
    }

    $reuse_domain = considerConfiguredConstraint ($range, '8021Q_MULTILINK_LISTSRC');
    $domainclass = array();
    foreach (array_count_values (reduceSubarraysToColumn ($range['8021q'], 'domain_id')) as $domain_id => $vlan_count)
        $domainclass[$domain_id] = $vlan_count == 1 ? '' : ($reuse_domain ? '{trwarning}' : '{trerror}');
    foreach ($range['8021q'] as $item)
        $summary[] = array ($domainclass[$item['domain_id']] . 'VLAN:', formatVLANAsHyperlink (getVLANInfo ($item['domain_id'] . '-' . $item['vlan_id'])));
    if (getConfigVar ('EXT_IPV4_VIEW') == 'yes' and count ($routers = findRouters ($range['addrlist'])))
    {
        $summary['Routed by'] = '';
        foreach ($routers as $rtr)
            $summary['Routed by'] .= renderRouterCell($rtr['ip_bin'], $rtr['iface'], spotEntity ('object', $rtr['id']));
    }
    $summary['tags'] = '';
    renderEntitySummary ($range, 'summary', $summary, $mod, 'Summary');

    if (strlen ($range['comment']))
    {
        $mod->addOutput('Comment', string_insert_hrefs (htmlspecialchars ($range['comment'], ENT_QUOTES, 'UTF-8')));
    }

    renderFilesPortlet($realm, $id, $mod, 'Files');
    renderIPNetworkAddresses ($range, $mod, 'AddressList');
}

// Used solely by renderSeparator
function renderEmptyIPv6 ($ip_bin, $hl_ip, $parent, $placeholder)
{
    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule($placeholder, 'IPv6Separator', $parent, true);
    $class = '';
    if ($ip_bin === $hl_ip)
        $class .= 'highlight';
    $mod->addOutput('Highlight', $class);
    $mod->addOutput('FMT', ip6_format ($ip_bin));
    $mod->addOutput('Link',makeHref (array ('page' => 'ipaddress', 'ip' => $fmt)));
    
    $editable = permitted ('ipaddress', 'properties', 'editAddress')
                ? 'editable'
                : '';
    $mod->addOutput('Editable', $editable);
}

// Renders empty table line to shrink empty IPv6 address ranges.
// If the range consists of single address, renders the address instead of empty line.
// Renders address $hl_ip inside the range.
// Used solely by renderIPv6NetworkAddresses
function renderSeparator ($first, $last, $hl_ip, $parent, $placeholder)
{
    $self = __FUNCTION__;
    if (strcmp ($first, $last) > 0)
        return;
    if ($first == $last)
        renderEmptyIPv6 ($first, $hl_ip);
    elseif (isset ($hl_ip) && strcmp ($hl_ip, $first) >= 0 && strcmp ($hl_ip, $last) <= 0)
    {
        $self ($first, ip_prev ($hl_ip), $hl_ip);
        renderEmptyIPv6 ($hl_ip, $hl_ip);
        $self (ip_next ($hl_ip), $last, $hl_ip);
    }
    else
    {
        $tplm = TemplateManager::getInstance();
        $mod = $tplm->generateSubmodule($placeholder, 'IPv6SeparatorPlain', $parent, true);

    }
}

// calculates page number that contains given $ip (used by renderIPv6NetworkAddresses)
function getPageNumOfIPv6 ($list, $ip_bin, $maxperpage)
{
    if (intval ($maxperpage) <= 0 || count ($list) <= $maxperpage)
        return 0;
    $keys = array_keys ($list);
    for ($i = 1; $i <= count ($keys); $i++)
        if (strcmp ($keys[$i-1], $ip_bin) >= 0)
            return intval ($i / $maxperpage);
    return intval (count ($list) / $maxperpage);
}

function renderIPNetworkAddresses ($range, $parent, $placeholder)
{
    switch (strlen ($range['ip_bin']))
    {
    case 4:
        return renderIPv4NetworkAddresses ($range, $parent, $placeholder);
    case 16:
        return renderIPv6NetworkAddresses ($range, $parent, $placeholder);
    default:
        throw new InvalidArgException ("range['ip_bin']", $range['ip_bin']);
    }
}

function renderIPv4NetworkAddresses ($range, $parent, $placeholder)
{
    global $pageno, $tabno, $aac_left;
    $startip = ip4_bin2int ($range['ip_bin']);
    $endip = ip4_bin2int (ip_last ($range));

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule($placeholder, 'IPNetworkAddresses', $parent);
    $mod->setNamespace('ipnetwork',true);

    if (isset ($_REQUEST['hl_ip']))
    {
        $hl_ip = ip4_bin2int (ip4_parse ($_REQUEST['hl_ip']));
        $mod->addOutput('AutoScroll', $hl_ip);
    }

    // pager
    $maxperpage = getConfigVar ('IPV4_ADDRS_PER_PAGE');
    $address_count = $endip - $startip + 1;
    $page = 0;
    $rendered_pager = '';
    if ($address_count > $maxperpage && $maxperpage > 0)
    {
        $page = isset ($_REQUEST['pg']) ? $_REQUEST['pg'] : (isset ($hl_ip) ? intval (($hl_ip - $startip) / $maxperpage) : 0);
        if ($numpages = ceil ($address_count / $maxperpage))
        {
            $mod->addOutput('HasPagination', true);
            $mod->addOutput('StartIP', ip4_format (ip4_int2bin ($startip)));
            $mod->addOutput('EndIP', ip4_format (ip4_int2bin ($endip)));
            $pagesarray = array();
            $smod = $tplm->generateSubmodule('Pager', 'IPNetworkAddressesPager', $mod);
            for ($i = 0; $i < $numpages; $i++)
                if ($i == $page)
                {
                    $pagesarray[] = array(
                                        'B' => '<b>',
                                        'BEnd' => '</b>',
                                        'i' => $i,
                                        'Link' => makeHref (array ('page' => $pageno, 'tab' => $tabno, 'id' => $netinfo['id'], 'pg' => $i))
                                    );
                }
                else
                {
                    $pagesarray[] = array(
                                        'i' => $i,
                                        'Link' => makeHref (array ('page' => $pageno, 'tab' => $tabno, 'id' => $netinfo['id'], 'pg' => $i))
                                    );
                }
            $smod->addOutput('Pages', $pagesarray);
        }
        $startip = $startip + $page * $maxperpage;
        $endip = min ($startip + $maxperpage - 1, $endip);
    }

    markupIPAddrList ($range['addrlist']);
    for ($ip = $startip; $ip <= $endip; $ip++)
    {
        $ip_bin = ip4_int2bin ($ip);
        $dottedquad = ip4_format ($ip_bin);
        $tr_class = (isset ($hl_ip) && $hl_ip == $ip ? 'highlight' : '');
        if (isset ($range['addrlist'][$ip_bin]))
            $addr = $range['addrlist'][$ip_bin];
        else
        {
            $editable = permitted ('ipaddress', 'properties', 'editAddress') ? 'editable' : '';

            $smod = $tplm->generateSubmodule('IPList', 'IPNetworkAddressEmpty',$mod);
            $smod->setNamespace('ipnetwork',true);
            $smod->addOutput('Link', makeHref(array('page'=>'ipaddress', 'ip' => $dottedquad)));
            $smod->addOutput('IP', $dottedquad);
            $smod->addOutput('Editable', $editable);
            $smod->addOutput('TrClass', $tr_class);
            continue;
        }
        // render IP change history
        $title = '';
        $history_class = '';

        $smod = $tplm->generateSubmodule('IPList', 'IPv6NetworkAddress', $mod);
        $smod->setNamespace('ipnetwork');

        if (isset ($addr['last_log']))
        {
            $smod->addOutput('Title', htmlspecialchars ($addr['last_log']['user'] . ', ' . formatAge ($addr['last_log']['time']) , ENT_QUOTES));
            $smod->addOutput('Class', 'hover-history underline');
        }
        $smod->addOutput('RowClass', $addr['class']);
        $smod->addOutput('Highlighted', (isset($hl_ip) && $hl_ip === $ip_bin ? 'highlight' : ''));
        $smod->addOutput('IP', $dottedquad);
        $smod->addOutput('Link', makeHref(array('page'=>'ipaddress', 'ip'=>$addr['ip'])));
        $smod->addOutput('PrintedIP', $addr['ip']);

        $editable =	(empty ($addr['allocs']) || !empty ($addr['name']) || !empty ($addr['comment']))
                    && permitted ('ipaddress', 'properties', 'editAddress')
                    ? 'editable'
                    : '';

        $smod->addOutput('Editable', $editable);
        $smod->addOutput('Name', $addr['name']);
        $smod->addOutput('Comment', $addr['comment']);

        if ( $addr['reserved'] == 'yes')
        {
            $smod->addOutput('Reserved', true);
        }
        $outarr = array();
        foreach ($addr['allocs'] as $ref)
        {
            $name = $ref['name'] . (!strlen ($ref['name']) ? '' : '@') . $ref['object_name'];
            $outarr[] = array(
                            'Type'=> $delim . $aac_left[$ref['type']],
                            'IPAllocLink'=> makeIPAllocLink ($ip_bin, $ref, TRUE)
                        );
        }

        if (count($outarr)>0)
        {
            $smod->addOutput('Allocs', $outarr);
        }

        $outarr = array();
        foreach ($addr['vslist'] as $vs_id)
        {
            $vs = spotEntity ('ipv4vs', $vs_id);
            $outarr[] = array('Link'=>mkA ("${vs['name']}:${vs['vport']}/${vs['proto']}", 'ipv4vs', $vs['id']));
        }
        if (count($outarr)>0)
        {
            $smod->addOutput('VSList', $outarr);
        }

        $outarr = array();
        foreach ($addr['vsglist'] as $vs_id)
        {
            $vs = spotEntity ('ipvs', $vs_id);
            $outarr[] = array('Link'=>mkA ($vs['name'], 'ipvs', $vs['id']));
        }
        if (count($outarr)>0)
        {
            $smod->addOutput('VSGList', $outarr);
        }

        $outarr = array();
        foreach ($addr['rsplist'] as $rsp_id)
        {
            $rsp = spotEntity ('ipv4rspool', $rsp_id);
            $outarr[] = array('Link'=>mkA ($rsp['name'], 'ipv4rspool', $rsp['id']));
        }

        if (count($outarr)>0)
        {
            $smod->addOutput('RSPList', $outarr);
        }
    }
    // end of iteration
    if (permitted (NULL, NULL, 'set_reserve_comment'))
        $mod->addOutput('UserHasEditPerm', true);
}

function renderIPv6NetworkAddresses ($netinfo, $parent, $placeholder)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule($placeholder, 'IPv6NetworkAddresses', $parent);
    $mod->setNamespace('ipnetwork');

    global $pageno, $tabno, $aac_left;

    $hl_ip = NULL;
    if (isset ($_REQUEST['hl_ip']))
    {
        $hl_ip = ip6_parse ($_REQUEST['hl_ip']);
        $mod->addOutput('AutoScroll', ip6_format($hl_ip));
    }

    $addresses = $netinfo['addrlist'];
    ksort ($addresses);
    markupIPAddrList ($addresses);

    // pager
    $maxperpage = getConfigVar ('IPV4_ADDRS_PER_PAGE');
    if (count ($addresses) > $maxperpage && $maxperpage > 0)
    {
        $mod->addOutput('HasPagination', true);
        $page = isset ($_REQUEST['pg']) ? $_REQUEST['pg'] : (isset ($hl_ip) ? getPageNumOfIPv6 ($addresses, $hl_ip, $maxperpage) : 0);
        $numpages = ceil (count ($addresses) / $maxperpage);
        $mod->addOutput('NumPages', $numpages);
        $pagesarray = array();
        for ($i=0; $i<$numpages; $i++)
        {
            if ($i == $page)
            {
                $pagesarray[] = array(
                                    'B' => '<b>',
                                    'BEnd' => '</b>',
                                    'I' => $i,
                                    'Link' => makeHref (array ('page' => $pageno, 'tab' => $tabno, 'id' => $netinfo['id'], 'pg' => $i))
                                );
            }
            else
            {
                $pagesarray[] = array(
                                    'I' => $i,
                                    'Link' => makeHref (array ('page' => $pageno, 'tab' => $tabno, 'id' => $netinfo['id'], 'pg' => $i))
                                );
            }
            $mod->addOutput('Pages', $pagesarray);
        }
    }

    $i = 0;
    $interruped = FALSE;
    $prev_ip = ip_prev ($netinfo['ip_bin']);
    foreach ($addresses as $ip_bin => $addr)
    {
        if (isset ($page))
        {
            ++$i;
            if ($i <= $maxperpage * $page)
                continue;
            elseif ($i > $maxperpage * ($page + 1))
            {
                $interruped = TRUE;
                break;
            }
        }

        if ($ip_bin != ip_next ($prev_ip))
            renderSeparator (ip_next ($prev_ip), ip_prev ($ip_bin), $hl_ip, $mod, 'IPList');
        $prev_ip = $ip_bin;

        $smod = $tplm->generateSubmodule('IPList', 'IPv6NetworkAddress', $mod);
        $smod->setNamespace('ipnetwork');

        // render IP change history
        $title = '';
        $history_class = '';
        if (isset ($addr['last_log']))
        {
            $title = htmlspecialchars ($addr['last_log']['user'] . ', ' . formatAge ($addr['last_log']['time']) , ENT_QUOTES);
            $history_class = 'hover-history underline';
        }

        $smod->addOutput('Title', $title);
        $smod->addOutput('RowClass', $addr['class']);
        $smod->addOutput('Highlighted', (isset($hl_ip) && $hl_ip === $ip_bin ? 'highlight' : ''));
        $smod->addOutput('Class', $history_class);
        $smod->addOutput('Link', makeHref (array ('page' => 'ipaddress', 'ip' => $addr['ip'])));
        $smod->addOutput('IP', $addr['ip']);
        $smod->addOutput('PrintedIP', $addr['ip']);

        $editable =
            (empty ($addr['allocs']) || !empty ($addr['name'])
             && permitted ('ipaddress', 'properties', 'editAddress')
             ? 'editable'
             : '');
        $smod->addOutput('Editable', $editable);
        $smod->addOutput('Name', $addr['name']);
        $smod->addOutput('Comment', $addr['comment']);

        $delim = '';
        if ( $addr['reserved'] == 'yes')
        {
            $smod->addOutput('Reserved', true);
            $delim = '; ';
        }

        $outarr = array();
        foreach ($addr['allocs'] as $ref)
        {
            $outarr[] = array(
                            'Type'=> $delim . $aac_left[$ref['type']],
                            'IPAllocLink'=> makeIPAllocLink ($ip_bin, $ref, TRUE));
            $delim = '; ';
        }

        if (count($outarr)>0)
        {
            $smod->addOutput('Allocs', $outarr);
        }

        $outarr = array();
        foreach ($addr['vslist'] as $vs_id)
        {
            $vs = spotEntity ('ipv4vs', $vs_id);
            $outarr[] = array('Link'=>mkA ("${vs['name']}:${vs['vport']}/${vs['proto']}", 'ipv4vs', $vs['id']));
        }
        if (count($outarr)>0)
        {
            $smod->addOutput('VSList', $outarr);
        }

        $outarr = array();
        foreach ($addr['vsglist'] as $vs_id)
        {
            $vs = spotEntity ('ipvs', $vs_id);
            $outarr[] = array('Link'=>mkA ($vs['name'], 'ipvs', $vs['id']));
        }
        if (count($outarr)>0)
        {
            $smod->addOutput('VSGList', $outarr);
        }

        $outarr = array();
        foreach ($addr['rsplist'] as $rsp_id)
        {
            $rsp = spotEntity ('ipv4rspool', $rsp_id);
            $outarr[] = array('Link'=>mkA ($rsp['name'], 'ipv4rspool', $rsp['id']));
        }

        if (count($outarr)>0)
        {
            $smod->addOutput('RSPList', $outarr);
        }
    }
    if (! $interruped)
        renderSeparator (ip_next ($prev_ip), ip_last ($netinfo), $hl_ip, $mod, 'IPList');
    if (isset ($page))
    {
        // bottom pager
        $mod->addOutput('BottomPager', true);
        if ($page > 0)
            $mod->addOutput('BottomPagerPrevLink', makeHref (array ('page' => $pageno, 'tab' => $tabno, 'id' => $netinfo['id'], 'pg' => $page - 1)));
        if ($page < $numpages - 1)
            $mod->addOutput('BottomPagerNextLink', makeHref (array ('page' => $pageno, 'tab' => $tabno, 'id' => $netinfo['id'], 'pg' => $page + 1)));
    }
    if (permitted (NULL, NULL, 'set_reserve_comment'))
        $mod->addOutput('UserHasEditPerm', true);
}

function renderIPNetworkProperties ($id)
{
    global $pageno;
    $netdata = spotEntity ($pageno, $id);

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'IPNetworkProperties');
    $mod->setNamespace('ipnetwork');
    $mod->setLock(true);
    $mod->addOutput('IP', $netdata['ip']);
    $mod->addOutput('Mask', $netdata['mask']);
    $mod->addOutput('Name', htmlspecialchars ($netdata['name'], ENT_QUOTES, 'UTF-8'));
    $mod->addOutput('Comment', htmlspecialchars ($netdata['comment'], ENT_QUOTES, 'UTF-8'));
    printTagsPicker (null, $mod, 'TagsPicker');

    if (! isIPNetworkEmpty ($netdata))
    {
        $mod->addOutput('NotEmpty', true);
        $mod->addOutput('AllocCount', count($netdata['addrlist']));
    }
    {
        $mod->addOutput('NotEmpty', false);
        $mod->addOutput('ID', $id);
    }
}

function renderIPAddress ($ip_bin)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderIPAddress');
    $mod->setNamespace('ipaddress');

    global $aat, $nextorder;
    $address = getIPAddress ($ip_bin);
   
    $mod->addOutput('IP', $address['ip']);

    $summary = array();
    if (strlen ($address['name']))
        $summary['Name'] = $address['name'];
    if (strlen ($address['comment']))
        $summary['Comment'] = $address['comment'];
    $summary['Reserved'] = $address['reserved'];
    $summary['Allocations'] = count ($address['allocs']);
    if (isset ($address['outpf']))
        $summary['Originated NAT connections'] = count ($address['outpf']);
    if (isset ($address['inpf']))
        $summary['Arriving NAT connections'] = count ($address['inpf']);
    renderEntitySummary ($address, 'summary', $summary, $mod, 'EntitySummary');

    // render SLB portlet
    if (! empty ($address['vslist']) or ! empty ($address['vsglist']) or ! empty ($address['rsplist']))
    {
        if (! empty ($address['vsglist']))
        {
            $mod->addOutput('VSGListCount', count ($address['vsglist']));
            foreach ($address['vsglist'] as $vsg_id)
                renderSLBEntityCell (spotEntity ('ipvs', $vsg_id), FALSE, $mod, 'SLBPortlet1');
        }

        if (! empty ($address['vslist']))
        {
            $mod->addOutput('VSListCount', count ($address['vslist']));
            foreach ($address['vslist'] as $vs_id)
                renderSLBEntityCell (spotEntity ('ipv4vs', $vs_id), FALSE, $mod, 'SLBPortlet2');
        }

        if (! empty ($address['rsplist']))
        {
            $mod->addOutput('RSPListCount', count ($address['rsplist']));
            foreach ($address['rsplist'] as $rsp_id)
                renderSLBEntityCell (spotEntity ('ipv4rspool', $rsp_id), FALSE, $mod, 'SLBPortlet3');
        }
    }


    if (isset ($address['class']) and ! empty ($address['allocs']))
    {
        // render all allocation records for this address the same way
        $out = array();
        foreach ($address['allocs'] as $bond)
        {
            $out[] = array(
                         'AddrClass'=>$address['class'],
                         'Highlight'=>((($_REQUEST['hl_object_id']) and $_REQUEST['hl_object_id'] == $bond['object_id']) ? 'hightlight' : ''),
                         'IPAllocLink'=>makeIPAllocLink ($ip_bin, $bond),
                         'ObjName'=>$bond['object_name'],
                         'Name'=>$bond['name'],
                         'Type'=>$aat[$bond['type']]
                     );

        }
        $mod->addOutput('Allocations',$out);
    }

    if (! empty ($address['rsplist']))
    {
        $out = array();
        foreach ($address['rsplist'] as $rsp_id)
        {
            $out[] = array('Pool'=>renderSLBEntityCell (spotEntity ('ipv4rspool', $rsp_id)));
        }
        $mod->addOutput('RSPools', $out);
    }

    if (! empty ($address['vsglist']))
        foreach ($address['vsglist'] as $vsg_id)
            renderSLBTriplets2 (spotEntity ('ipvs', $vsg_id), FALSE, $ip_bin, $mod, 'VSGList');

    if (! empty ($address['vslist']))
        renderSLBTriplets ($address, $mod, 'VSList');

    foreach (array ('outpf' => 'departing NAT rules', 'inpf' => 'arriving NAT rules') as $key => $title)
        if (! empty ($address[$key]))
        {
            $placeholder = ($key == 'outpf' ? 'NATDeparting' : 'NATArriving');

            foreach ($address[$key] as $rule)
            {
                $smod = $tplm->generatePseudoSubmodule($placeholder, $mod);
                $smod->setNamespace('ipaddress');
                $smod->addOutput('Proto', $rule['proto']);
                $smod->addOutput('FromIp', $rule['localip']);
                $smod->addOutput('FromPort', $rule['localport']);
                $smod->addOutput('FromLink', makeHref (array ('page' => 'ipaddress',  'tab'=>'default', 'ip' => $rule['localip'])));
                $smod->addOutput('ToIp', $rule['remoteip']);
                $smod->addOutput('ToPort', $rule['remoteport']);
                $smod->addOutput('ToLink', makeHref (array ('page' => 'ipaddress',  'tab'=>'default', 'ip' => $rule['remoteip'])));
                $smod->addOutput('Description', $rule['description']);
            }
        }
}

function renderIPAddressProperties ($ip_bin)
{
    $address = getIPAddress ($ip_bin);

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderIPAddressProperties');
    $mod->setNamespace('ipaddress',true);
    $mod->setLock();

    $mod->addOutput('Name', $address['name']);
    $mod->addOutput('Comment', $address['Comment']);
    $mod->addOutput('Checked', ($address['reserved']=='yes') ? 'checked' : '');
    $mod->addOutput('Ip', $address['ip']);

    if (!strlen ($address['name']) and $address['reserved'] == 'no')
        $mod->addOutput('Undeletable', true);
}

function renderIPAddressAllocations ($ip_bin)
{
    global $aat;
    $address = getIPAddress ($ip_bin);

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderIPAddressAllocations');
    $mod->setNamespace('ipaddress',true);
    $mod->setLock();

    $mod->addOutput('Ip', $address['ip']);

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput('NewTop', true);

    $mod->addOutput('CreateNewObjectSelect', getSelect(getNarrowObjectList ('IPV4OBJ_LISTSRC'), array ('name' => 'object_id', 'tabindex' => 100)));
    $mod->addOutput('CreateNewTypeSelect', getSelect ($aat, array ('name' => 'bond_type', 'tabindex' => 102, 'regular')));

    if (isset ($address['class']))
    {
        $class = $address['class'];
        $mod->addOutput('Class', $class);
        if ($address['reserved'] == 'yes')
            $mod->addOutput('Reserved', true);
        
        foreach ($address['allocs'] as $bond)
        {
            $smod = $tplm->generatePseudoSubmodule('AddressList', $mod);
            $smod->addOutput('Class', $class);
            $smod->addOutput('ObjectId', $bond['object_id']);
            $smod->addOutput('ObjectName', $bond['object_name']);
            $smod->addOutput('BondName', $bond['name']);
            $smod->addOutput('Link', makeHref (array ('page' => 'object', 'object_id' => $bond['object_id'], 'hl_ip' => $address['ip'])));
            $smod->addOutput('TypeSelect', getSelect($aat, array ('name' => 'bond_type'), $bond['type']));
        }
    }
}

function renderNATv4ForObject ($object_id)
{
    function printNewItemTR ($alloclist, $parent, $placeholder)
    {
        $tplm = TemplateManager::getInstance();
        $mod = $tplm->generateSubmodule($placeholder,"RenderNATv4ForObject_printNew", $parent);
        $mod->setNamespace("object");

        printSelect (array ('TCP' => 'TCP', 'UDP' => 'UDP', 'ALL' => 'ALL'), array ('name' => 'proto'), NULL, $mod, 'printTcpUdpSel');

        $allAllocOut = array();

        foreach ($alloclist as $ip_bin => $alloc)
        {
            $ip = $alloc['addrinfo']['ip'];
            $name = (!isset ($alloc['addrinfo']['name']) or !strlen ($alloc['addrinfo']['name'])) ? '' : (' (' . niftyString ($alloc['addrinfo']['name']) . ')');
            $osif = (!isset ($alloc['osif']) or !strlen ($alloc['osif'])) ? '' : ($alloc['osif'] . ': ');
            $allAllocOut[] = array('ip' => $ip, 'osif' => $osif, 'name' => $name);
        }
        $mod->addOutput("allAlloc", $allAllocOut);
        $mod->addOutput("hrefForHelper", makeHrefForHelper ('inet4list'));
    }

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderNATv4ForObject");
    $mod->setNamespace("object");

    $focus = spotEntity ('object', $object_id);
    amplifyCell ($focus);
 
    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        printNewItemTR ($focus['ipv4'], $mod, 'printNewItemTop_mod');
 
    foreach ($focus['nat4']['out'] as $pf)
    {
        $class = 'trerror';
        $osif = '';
        $localip_bin = ip4_parse ($pf['localip']);
        if (isset ($focus['ipv4'][$localip_bin]))
        {
            $class = $focus['ipv4'][$localip_bin]['addrinfo']['class'];
            $osif = $focus['ipv4'][$localip_bin]['osif'] . ': ';
        }

        $singlePort = array('class' => $class, 'proto' => $pf['proto'], 'osif' => $osif);
        $singlePort['opLink'] = getOpLink  (
                                    array (
                                        'op'=>'delNATv4Rule',
                                        'localip'=>$pf['localip'],
                                        'localport'=>$pf['localport'],
                                        'remoteip'=>$pf['remoteip'],
                                        'remoteport'=>$pf['remoteport'],
                                        'proto'=>$pf['proto'],
                                    ), '', 'delete', 'Delete NAT rule'
                                );
       
        $singlePort['portpair_local_mod'] = getRenderedIPPortPair ($pf['localip'], $pf['localport']);
        if (strlen ($pf['local_addr_name']))
            $singlePort['local_addr_name'] = $pf['local_addr_name'];
        
        $address = getIPAddress (ip4_parse ($pf['remoteip']));
        $singlePort['mkAList'] = '';
        
        if (count ($address['allocs']))
            foreach ($address['allocs'] as $bond)
                $singlePort['mkAList'] .= mkA ("${bond['object_name']}(${bond['name']})", 'object', $bond['object_id']) . ' ';
        
        elseif (strlen ($pf['remote_addr_name']))
        $singlePort['remote_addr_name'] = $pf['remote_addr_name'];
        
        $singlePort['opFormIntro'] =
            printOpFormIntro
            (
                'updNATv4Rule',
                array
                (
                    'localip' => $pf['localip'],
                    'localport' => $pf['localport'],
                    'remoteip' => $pf['remoteip'],
                    'remoteport' => $pf['remoteport'],
                    'proto' => $pf['proto']
                )
            );   
        $singlePort['saveImg'] = printImageHREF ('save', 'Save changes', TRUE);
        $singlePort['description'] = $pf['description'];

        //Using loop array style paramter for output
        $tplm->generatePseudoSubmodule('AllNatv4Ports', $mod, $singlePort);

        getRenderedIPPortPair ($pf['remoteip'], $pf['remoteport'], $vorPorts, 'portpair_remote_mod');
        getRenderedIPPortPair ($pf['localip'], $pf['localport'], $vorPorts, 'portpair_local_mod');
    }

    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
        printNewItemTR ($focus['ipv4'], $mod, 'printNewItemBottom_mod');

    if (!count ($focus['nat4']))
        return;
    $mod->addOutput("hasFocusNat4", true);

    $allNatv4FocusOut = array();
    foreach ($focus['nat4']['in'] as $pf)
    {
        $singleFocus = array('proto' => $pf['proto'], 'description' => $pf['description']);
        $singleFocus['opLink'] = getOpLink (
                                     array(
                                         'op'=>'delNATv4Rule',
                                         'localip'=>$pf['localip'],
                                         'localport'=>$pf['localport'],
                                         'remoteip'=>$pf['remoteip'],
                                         'remoteport'=>$pf['remoteport'],
                                         'proto'=>$pf['proto'],
                                     ), '', 'delete', 'Delete NAT rule');
        $singleFocus['mkA'] = mkA ($pf['object_name'], 'object', $pf['object_id']);
        $singleFocus['focus_portpair_local_mod'] = getRenderedIPPortPair ($pf['localip'], $pf['localport']);
        $singleFocus['focus_portpair_remote_mod'] = getRenderedIPPortPair ($pf['remoteip'], $pf['remoteport']);
        $allNatv4FocusOut[] = $singleFocus;
    }

    $mod->addOutput("allNatv4Focus", $allNatv4FocusOut);
}

function renderAddMultipleObjectsForm ()
{
    $typelist = readChapter (CHAP_OBJTYPE, 'o');
    $typelist[0] = 'select type...';
    $typelist = cookOptgroups ($typelist);
    $max = getConfigVar ('MASSCOUNT');
    $tabindex = 100;

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","AddMultipleObjects");
    $mod->setNamespace("depot");

    // exclude location-related object types
    global $location_obj_types;
    foreach ($typelist['other'] as $key => $value)
        if ($key > 0 && in_array($key, $location_obj_types))
            unset($typelist['other'][$key]);

    $mod->setOutput("formIntro", printOpFormIntro ('addObjects'));
    $objectListOutput = array();
    for ($i = 0; $i < $max; $i++)
    {
        $singleEntry = array();
        // Don't employ DEFAULT_OBJECT_TYPE to avoid creating ghost records for pre-selected empty rows.
        $singleEntry['niftySelect'] = printNiftySelect ($typelist, array ('name' => "${i}_object_type_id", 'tabindex' => $tabindex), 0);
        $singleEntry['i'] = $i;
        $singleEntry['tabindex'] = $tabindex;

        if ($i == 0)
        {
            $singleEntry['max'] = $max;
            $singleEntry['tagsPicker'] = printTagsPicker ();
        }
        else
            $singleEntry['tagsPicker'] = "";

        $tabindex++;
        $objectListOutput[] = $singleEntry;
    }
    $mod->setOutput("objectListData", $objectListOutput);
    $mod->setOutput("formIntroLotOfObjects", printOpFormIntro ('addLotOfObjects'));

    printNiftySelect ($typelist, array ('name' => 'global_type_id'), getConfigVar ('DEFAULT_OBJECT_TYPE'), false, $mod, "sameTypeSameTagSelect");
    printTagsPicker (null, $mod, 'tagsPicker');
}

function searchHandler()
{
    //Handles the search strings and genererats a result website
    $terms = trim ($_REQUEST['q']);
    if (!strlen ($terms))
        throw new InvalidRequestArgException('q', $_REQUEST['q'], 'Search string cannot be empty.');
    renderSearchResults ($terms, searchEntitiesByText ($terms));
}

function renderSearchResults ($terms, $summary)
{
    //Changed for template engine
    //Initalising
    $tplm = TemplateManager::getInstance();
    // calculate the number of found objects
    $nhits = 0;
    foreach ($summary as $realm => $list)
        $nhits += count ($list);

    if ($nhits == 0)
    {
        $params = array("Terms" => $terms );
        $mod = $tplm->generateSubmodule("Payload", "NoSearchItemFound", null, true, $params);
        return;
    }
    elseif ($nhits == 1)
    {
        foreach ($summary as $realm => $record)
        {
            if (is_array ($record))
                $record = array_shift ($record);
            break;
        }
        $url = buildSearchRedirectURL ($realm, $record);
        if (isset ($url))
            redirectUser ($url);
    }
    global $nextorder;
    $order = 'odd';

    $mod = $tplm->generateSubmodule("Payload", "SearchMain");
    $mod->setNamespace("search",true);
    $mod->setOutput("NHITS", $nhits);
    $mod->setOutput("TERMS", $terms);

    foreach ($summary as $where => $what)
    switch ($where)
    {
    case 'object':
        $allObjects = $tplm->generateSubmodule("FoundItems", "SearchAllObjects", $mod);

        foreach ($what as $obj)
        {
            $foundObject = $tplm->generateSubmodule("foundObject", "SearchObject", $allObjects);
            $object = spotEntity ('object', $obj['id']);

            $foundObject->setOutput("objImage", renderCell($object));
            $foundObject->setOutput("rowOrder", $order);

            if (isset ($obj['by_attr']))
            {
                // only explain non-obvious reasons for listing

                $outArray = array();
                $foundObject->setOutput('ObjectsByAttr', true);

                foreach ($obj['by_attr'] as $attr_name)
                    if ($attr_name != 'name')
                        $outArray[] = array("Attr_Name" => $attr_name);


                $foundObject->setOutput("Objects_Attr", $outArray);
            }

            if (isset ($obj['by_sticker']))
            {
                $outArray = array();
                $foundObject->setOutput('ObjectsBySticker', true);

                $aval = getAttrValues ($obj['id']);
                foreach ($obj['by_sticker'] as $attr_id)
                {
                    $record = $aval[$attr_id];
                    $outArray[] = array('Name' => $record['name'],
                                        'AttrValue' => formatAttributeValue ($record));
                }
                $foundObject->setOutput("Objects_Sticker", $outArray);
            }

            if (isset ($obj['by_port']))
            {
                $outArray = array();
                $foundObject->setOutput('ObjectsByPort', true);

                amplifyCell ($object);
                foreach ($obj['by_port'] as $port_id => $text)
                    foreach ($object['ports'] as $port)
                        if ($port['id'] == $port_id)
                        {
                            $port_href = '<a href="' . makeHref (array
                                                                 (
                                                                         'page' => 'object',
                                                                         'object_id' => $object['id'],
                                                                         'hl_port_id' => $port_id
                                                                 )) . '">port ' . $port['name'] . '</a>';
                            $outArray[] = array('Href' =>  $port_href,
                                                'Text' => $text );
                            break; // next reason
                        }
                $foundObject->setOutput("Objects_Port", $outArray);
            }

            if (isset ($obj['by_iface']))
            {
                $outArray = array();
                $foundObject->setOutput('ObjectsByIface', true);

                foreach ($obj['by_iface'] as $ifname)
                    $outArray[] = array( 'Ifname' => $ifname);
                $foundObject->setOutput("Objects_Iface", $outArray);
            }

            if (isset ($obj['by_nat']))
            {
                $outArray = array();
                $foundObject->setOutput('ObjectsByNAT', true);

                foreach ($obj['by_nat'] as $comment)
                    $outArray[] = array('Comment' => $comment);
                $foundObject->setOutput("Objects_NAT", $outArray);
            }

            if (isset ($obj['by_cableid']))
            {
                $outArray = array();
                $foundObject->setOutput('ObjectsByCableID', true);

                foreach ($obj['by_cableid'] as $cableid)
                    $outArray[] = array('CableID' => $cableid);
                $foundObject->setOutput("Objects_CableID", $outArray);
            }
            $order = $nextorder[$order];
        }
        break;
    case 'ipv4net':
    case 'ipv6net':
        $foundIPVNet = $tplm->generateSubmodule("FoundItems", "SearchIpv6net", $mod);

        if ($where == 'ipv4net')
        {
            $foundIPVNet->setOutput("IpvSpace", "ipv4space");
            $foundIPVNet->setOutput("IpvSpaceName", "IPv4 networks");
        }
        elseif ($where == 'ipv6net')
        {
            $foundIPVNet->setOutput("IpvSpace", "ipv6space");
            $foundIPVNet->setOutput("IpvSpaceName", "IPv6 networks");
        }
        $ipvOutArray = array();

        foreach ($what as $cell)
        {
            $ipvOutArray[] = array('rowOrder' => $order,
                                   'rendCell' => renderCell($cell));
            $order = $nextorder[$order];
        }

        $foundIPVNet->setOutput("IPVNetObjs",$ipvOutArray);
        break;
    case 'ipv4addressbydescr':
    case 'ipv6addressbydescr':
        $foundIPVAddress = $tplm->generateSubmodule("FoundItems", "SearchIpv6address", $mod);

        if ($where == 'ipv4addressbydescr')
            $foundIPVAddress->setOutput("sectionHeader", 'IPv4 addresses');
        elseif ($where == 'ipv6addressbydescr')
        $foundIPVAddress->setOutput("sectionHeader", 'IPv6 addresses');
        // FIXME: address, parent network, routers (if extended view is enabled)
        foreach ($what as $addr)
        {
            $fmt = ip_format ($addr['ip']);
            $parentnet = getIPAddressNetworkId ($addr['ip']);

            $singleAddr = array( 'rowOrder' => $order,
                                 'rowLink' => makeHref (array ( 'page' => strlen ($addr['ip']) == 16 ? 'ipv6net' : 'ipv4net',
                                         'id' => $parentnet,
                                         'tab' => 'default',
                                         'hl_ip' => $fmt)),
                                 'rowFmt' => $fmt,
                                 'rowAddr' => $addr['name'],
                                 'parentNetSet' => $parentnet !== NULL);
            $tplm->generateSubmodule('AllSearchAddrs','SearchIpv6address_Object', $foundIPVAddress, false, $singleAddr);
            $order = $nextorder[$order];
        }
        break;
    case 'ipv4rspool':
        $foundIPVSpool = $tplm->generateSubmodule("FoundItems", "SearchStdType", $mod);
        $foundIPVSpool->setOutput("page", "ipv4slb&tab=rspools");
        $foundIPVSpool->setOutput("title", "RS pools");

        $ipvOutArray = array();
        foreach ($what as $cell)
        {
            $ipvOutArray[] = array( 'rowOrder' => $order,
                                    'renderedCell' => renderCell ($cell));
            $order = $nextorder[$order];
        }
        
        $foundIPVSpool->setOutput("searchLoopObjs", $ipvOutArray);
        break;
    case 'ipvs':
        $foundIPVS = $tplm->generateSubmodule("FoundItems", "SearchStdType", $mod);
        $foundIPVS->setOutput("page", "ipv4slb&tab=vs");
        $foundIPVS->setOutput("title", "VS groups");

        $ipvOutArray = array();
        foreach ($what as $cell)
        {
            $ipvOutArray[] = array( 'rowOrder' => $order,
                                    'renderedCell' => renderCell ($cell));
            $order = $nextorder[$order];
        }

        $foundIPVS->setOutput("searchLoopObjs", $ipvOutArray);
        break;
    case 'ipv4vs':
        $foundIP4vs = $tplm->generateSubmodule("FoundItems", "SearchStdType", $mod);
        $foundIP4vs->setOutput("page", "ipv4slb&tab=default");
        $foundIP4vs->setOutput("title", "Virtual services");

        $ipvOutArray = array();
        foreach ($what as $cell)
        {
            $ipvOutArray[] = array( 'rowOrder' => $order,
                                    'renderedCell' => renderCell ($cell));
            $order = $nextorder[$order];
        }
        
        $foundIP4vs->setOutput("searchLoopObjs", $ipvOutArray);
        break;
    case 'user':
        $foundUser = $tplm->generateSubmodule("FoundItems", "SearchStdType", $mod);
        $foundUser->setOutput("page", "userlist");
        $foundUser->setOutput("title", "Users");

        $userOutArray = array();
        foreach ($what as $item)
        {
            $userOutArray[] = array( 'rowOrder' => $order,
                                     'renderedCell' => renderCell ($item));
            $order = $nextorder[$order];
        }

        $foundUser->setOutput("searchLoopObjs", $userOutArray);
        break;
    case 'file':
        $foundFile = $tplm->generateSubmodule("FoundItems", "SearchStdType", $mod);
        $foundFile->setOutput("page", "files");
        $foundFile->setOutput("title", "Files");

        $fileOutArray = array();
        foreach ($what as $cell)
        {
            $fileOutArray[] = array( 'rowOrder' => $order,
                                     'renderedCell' => renderCell ($cell));
            $order = $nextorder[$order];
        }

        $foundFile->setOutput("searchLoopObjs", $fileOutArray);
        break;
    case 'rack':
        $foundRack = $tplm->generateSubmodule("FoundItems", "SearchStdType", $mod);
        $foundRack->setOutput("page", "rackspace");
        $foundRack->setOutput("title", "Racks");

        $rackOutArray = array();
        foreach ($what as $cell)
        {
            $rackOutArray[] = array( 'rowOrder' => $order,
                                     'renderedCell' => renderCell ($cell));
            $order = $nextorder[$order];
        }

        $foundRack->setOutput("searchLoopObjs", $rackOutArray);
        break;
    case 'row':
        $foundRow = $tplm->generateSubmodule("FoundItems", "SearchStdType", $mod);
        $foundRow->setOutput("page", "rackspace");
        $foundRow->setOutput("title", "Rack rows");

        $rowOutArray = array();
        foreach ($what as $cell)
        {
            $rowOutArray[] = array( 'rowOrder' => $order,
                                    'renderedCell' => mkCellA ($cell));
            $order = $nextorder[$order];
        }

        $foundRow->setOutput("searchLoopObjs", $rowOutArray);
        break;
    case 'location':
        $foundLoc = $tplm->generateSubmodule("FoundItems", "SearchStdType", $mod);
        $foundLoc->setOutput("page", "rackspace");
        $foundLoc->setOutput("title", "Locations");

        $locOutArray = array();
        foreach ($what as $cell)
        {
            $locOutArray[] = array( 'rowOrder' => $order,
                                    'renderedCell' => renderCell ($cell));
            $order = $nextorder[$order];
        }

        $foundLoc->setOutput("searchLoopObjs", $locOutArray);
        break;
    case 'vlan':
        $foundVLan = $tplm->generateSubmodule("FoundItems", "SearchStdType", $mod);
        $foundVLan->setOutput("page", "8021q");
        $foundVLan->setOutput("title", "VLANs");

        $vlanOutArray = array();
        foreach ($what as $vlan)
        {
            $vlanOutArray[] = array( 'rowOrder' => $order,
                                     'renderedCell' => formatVLANAsHyperlink (getVLANInfo ($vlan['id'])) ."");
            $order = $nextorder[$order];
        }

        $foundVLan->setOutput("searchLoopObjs", $vlanOutArray);
        break;
    default: // you can use that in your plugins to add some non-standard search results
        $mod->setOutput("whatCont", $what);
    }
}

// This function prints a table of checkboxes to aid the user in toggling mount atoms
// from one state to another. The first argument is rack data as
// produced by amplifyCell(), the second is the value used for the 'unckecked' state
// and the third is the value used for 'checked' state.
// Usage contexts:
// for mounting an object:             printAtomGrid ($data, 'F', 'T')
// for changing rack design:           printAtomGrid ($data, 'A', 'F')
// for adding rack problem:            printAtomGrid ($data, 'F', 'U')
// for adding object problem:          printAtomGrid ($data, 'T', 'W')
function renderAtomGrid ($data, $parent = null, $placeholder = 'AtomGrid')
{
    $rack_id = $data['id'];

    $tplm = TemplateManager::getInstance();

    if($parent == null)
        $output = '';

    for ($unit_no = $data['height']; $unit_no > 0; $unit_no--)
    {
        if($parent == null)
            $trow = $tplm->generateModule('GridRow');
        else
            $trow = $tplm->generateSubmodule($placeholder, 'GridRow', $parent);

        $trow->setNamespace("");
        $trow->addOutput('RackId', $rack_id);
        $trow->addOutput('UnitNo', $unit_no);
        $trow->addOutput('Inversed', inverseRackUnit ($unit_no, $data));

        for ($locidx = 0; $locidx < 3; $locidx++)
        {
            $name = "atom_${rack_id}_${unit_no}_${locidx}";
            $state = $data[$unit_no][$locidx]['state'];

            $tatom = $tplm->generateSubmodule('Atoms', 'GridElement', $trow);
            $tatom->setNamespace("");
            $tatom->addOutput('State', $state);
            $tatom->addOutput('Name', $name);

            if (isset ($data[$unit_no][$locidx]['hl']))
                $tatom->addOutput('Hl', $data[$unit_no][$locidx]['hl']);
            if (!($data[$unit_no][$locidx]['enabled'] === TRUE))
                $tatom->addOutput('Disabled', true);
            else
                $tatom->addOutput('Checked', $data[$unit_no][$locidx]['checked']);
        }
        if($parent == null)
            $output .= $trow->run();
    }
    if($parent == null)
        return $output;
}

function renderCellList ($realm = NULL, $title = 'items', $do_amplify = FALSE, $celllist = NULL, $parent = NULL, $placeholder = "CellList")
{
    if ($realm === NULL)
    {
        global $pageno;
        $realm = $pageno;
    }
    global $nextorder;
    $order = 'odd';
    $cellfilter = getCellFilter();
    if (! isset ($celllist))
        $celllist = applyCellFilter ($realm, $cellfilter);
    else
        $celllist = filterCellList ($celllist, $cellfilter['expression']);

    $tplm = TemplateManager::getInstance();
    if($parent === NULL)
    {
        $mod = $tplm->generateModule("CellList");
    }
    else
    {
        $mod = $tplm->generateSubmodule($placeholder, "CellList", $parent);
    }
    $mod->setNamespace("",true);
    $mod->setLock();

    if ($realm != 'file' || ! renderEmptyResults ($cellfilter, 'files', count($celllist), $mod, "EmptyResults "))
    {
        if ($do_amplify)
            array_walk ($celllist, 'amplifyCell');
        $mod->addOutput("Title", $title);
        $mod->addOutput("CellCount", count($celllist));
        $cells = array();
        foreach ($celllist as $cell)
        {
            $singleCell = array();
            $singleCell["Order"] = $order;
            $singleCell["CellContent"] = renderCell($cell);
        
            $order = $nextorder[$order];
            $cells[] = $singleCell;
        }
        $mod->addOutput("CellListContent", $cells);

    }

    renderCellFilterPortlet ($cellfilter, $realm, $celllist, array(), $mod );
    if($parent == null)
        return $mod->run();
}

function renderUserList ()
{
    $tplm = TemplateManager::getInstance();
    $main = $tplm->getMainModule();
    renderCellList ('user', 'User accounts',FALSE, NULL, $main, 'Payload');
}

function renderUserListEditor ()
{
    function printNewItemTR ($parent,$placeholder)
    {
        $tplm = TemplateManager::getInstance();
        $smod2 = $tplm->generateSubmodule($placeholder, "UserListEditorNew", $parent);
        $smod2->setNamespace('userlist');
        printTagsPicker (null, $smod2, 'TagsPicker');
    }
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "renderUserListEditor");
    $mod->setNamespace("userlist");

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput('NewTop', true);
    printTagsPicker (null, $smod2, 'TagsPicker');

    $accounts = listCells ('user');

    $mod->addOutput("Count", count($accounts));
    foreach ($accounts as $account)
    {
        $smod = $tplm->generatePseudoSubmodule("Users", $mod);
        $smod->setNamespace('userlist');
        $smod->addOutput("UserId", $account['user_id']);
        $smod->addOutput("Name", $account['user_name']);
        $smod->addOutput("RealName", $account['user_realname']);

    }
}

function renderOIFCompatViewer()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderOIFCompatViewer");
    $mod->setNamespace("portmap",true);

    global $nextorder;
    $order = 'odd';
    $last_left_oif_id = NULL;
    $allPortCompatOut = array();
    foreach (getPortOIFCompat() as $pair)
    {
        if ($last_left_oif_id != $pair['type1'])
        {
            $order = $nextorder[$order];
            $last_left_oif_id = $pair['type1'];
        }
        $allPortCompatOut[] = array(	'Order' => $order,
                                        'Type1' => $pair['type1name'],
                                        'Type2' => $pair['type2name']);
    }
    $mod->addOutput("AllPortCompat", $allPortCompatOut);
}

function renderOIFCompatEditor()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderOIFCompatEditor");
    $mod->setNamespace("portmap",true);

    global $nextorder, $wdm_packs;

    $order = 'odd';
    $arr = array();
    foreach ($wdm_packs as $codename => $packinfo)
    {
        $arr[] = array(
                     'Order' => $order,
                     'Title' => $packinfo['title'],
                     'Codename' => $codename); //@XXX XXX XXX No helpers within old loops
        $order = $nextorder[$order];
    }
    $mod->addOutput('WDMPacks',$arr);
    $mod->addOutput('CreateNewType1', getSelect (getPortOIFOptions(), array ('name' => 'type1')));
    $mod->addOutput('CreateNewType2', getSelect (getPortOIFOptions(), array ('name' => 'type2')));

    $last_left_oif_id = NULL;
    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput('NewTop',true);

    $arr = array();
    foreach (getPortOIFCompat() as $pair)
    {
        if ($last_left_oif_id != $pair['type1'])
        {
            $order = $nextorder[$order];
            $last_left_oif_id = $pair['type1'];
        }
        $arr[] = array(
                     'Order' => $order,
                     'Type1' => $pair['type1'],
                     'Type2' => $pair['type2'],
                     'Type1name' => $pair['type1name'],
                     'Type2name' => $pair['type2name']);
    }
    $mod->addOutput('Interfaces',$arr);
}

function renderObjectParentCompatViewer()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderObjectParentCompatViewer");
    $mod->setNamespace("parentmap",true);

    global $nextorder;
    $order = 'odd';
    $last_left_parent_id = NULL;
    foreach (getObjectParentCompat() as $pair)
    {
        if ($last_left_parent_id != $pair['parent_objtype_id'])
        {
            $order = $nextorder[$order];
            $last_left_parent_id = $pair['parent_objtype_id'];
        }

        $mod->addOutput('Looparray', array(
                            'Order' => $order,
                            'Parentname' => $pair['parent_name'],
                            'Childname' => $pair['child_name']));
    }
}

function renderObjectParentCompatEditor()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderObjectParentCompatEditor");
    $mod->setNamespace("parentmap");

    function printNewitemTR()
    {
        $chapter = readChapter (CHAP_OBJTYPE);
        // remove rack, row, location
        unset ($chapter['1560'], $chapter['1561'], $chapter['1562']);
        $mod->setOutput('Parent', getSelect ($chapter,array ('name' => 'parent_objtype_id'), NULL));
        $mod->setOutput('Child', getSelect ($chapter,array ('name' => 'child_objtype_id'), NULL));
    }

    global $nextorder;
    $last_left_parent_id = NULL;
    $order = 'odd';
    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
    {
        $mod->addOutput("AddTop", true);
        printNewitemTR();

    }

    foreach (getObjectParentCompat() as $pair)
    {
        if ($last_left_parent_id != $pair['parent_objtype_id'])
        {
            $order = $nextorder[$order];
            $last_left_parent_id = $pair['parent_objtype_id'];
        }
        $mod->addOutput('Looparray', array(
                            'Order' => $order,
                            'Parentname' => $pair['parent_name'],
                            'Childname' => $pair['child_name'],
                            'Image' => ($pair['count'] > 0 ?
                                        getImageHREF ('nodelete', $pair['count'] . ' relationship(s) stored'):
                                        getOpLink (array ('op' => 'del', 'parent_objtype_id' => $pair['parent_objtype_id'],
                                                'child_objtype_id' => $pair['child_objtype_id']), '', 'delete', 'remove pair'))));

    }
    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
    {
        $mod->addOutput("AddTop", false);
        printNewitemTR();
    }
}

// Find direct sub-pages and dump as a list.
// FIXME: assume all config kids to have static titles at the moment,
// but use some proper abstract function later.
function renderConfigMainpage ()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderConfigMainPage");
    $mod->setNamespace("config",true);

    global $pageno, $page;
    $allPagesOut = array();
    foreach ($page as $cpageno => $cpage)
    {
        if (isset ($cpage['parent']) and $cpage['parent'] == $pageno  && permitted($cpageno))
            $allPagesOut[] = array(	'Cpageno' => $cpageno,
                                    'Title' => $cpage['title']);
    }
    $mod->addOutput("allPages", $allPagesOut);
}

function renderLocationPage ($location_id)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderLocationPage");
    $mod->setNamespace("location",true);

    $locationData = spotEntity ('location', $location_id);
    amplifyCell ($locationData);

    // Left column with information.
    $summary = array();
    $summary['Name'] = $locationData['name'];
    if (! empty ($locationData['parent_id']))
        $summary['Parent location'] = mkA ($locationData['parent_name'], 'location', $locationData['parent_id']);
    $summary['Child locations'] = count($locationData['locations']);
    $summary['Rows'] = count($locationData['rows']);
    if ($locationData['has_problems'] == 'yes')
        $summary[] = array ('<tr><td colspan=2 class=msg_error>Has problems</td></tr>');
    foreach (getAttrValuesSorted ($locationData['id']) as $record)
        if
        (
            $record['value'] != '' and
            permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $record['id'])))
        )
            $summary['{sticker}' . $record['name']] = formatAttributeValue ($record);
        $summary['tags'] = '';

    if (strlen ($locationData['comment']))
        $summary['Comment'] = $locationData['comment'];
    renderEntitySummary ($locationData, 'Summary', $summary, $mod, 'EntitySummary');
    renderFilesPortlet ('location', $location_id, $mod, 'FilesPortlet');

    if ($locationData['comment'] != '')
    {
        $tplm->generateSubmodule('LocComment', 'CommentPortlet', $mod, true, array(
                                 'Title' => 'Comment',
                                 'Comment' => string_insert_hrefs ($locationData['comment'])));
    }

    // Right column with list of rows and child locations
    $mod->addOutput('CountRows', count ($locationData['rows']));
    $helperarray = array();
    foreach ($locationData['rows'] as $row_id => $name)
        $helperarray[] = array('Link'=> mkA ($name, 'row', $row_id));

    if(count($helperarray)>0)
    {
        $mod->addOutput('Rows', $helperarray);
    }

    $mod->addOutput('CountLocations', count ($locationData['locations']));
    $helperarray = array();
    foreach ($locationData['locations'] as $location_id => $name)
        $helperarray[] = array('LocationLink' => mkA($name, 'location', $location_id) );

    if(count($helperarray) > 0 )
    {
        $mod->addOutput('ChildLocations', $helperarray);
    }
}

function renderEditLocationForm ($location_id)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderEditLocationForm");
    $mod->setNamespace("location");
    $mod->setLock(true);

    global $pageno;
    $location = spotEntity ('location', $location_id);
    amplifyCell ($location);

    $locations = array ();
    $locations[0] = '-- NOT SET --';
    foreach (listCells ('location') as $id => $locationInfo)
        $locations[$id] = $locationInfo['name'];
    natcasesort($locations);

    $mod->addOutput('Getselect', getSelect ($locations, array ('name' => 'parent_id'), $location['parent_id']));
    $mod->addOutput('Locationname', $location['name']);
    printTagsPicker (null, $mod, 'TagsPicker');

    // optional attributes
    $values = getAttrValuesSorted ($location_id);
    $num_attrs = count($values);
    $mod->addOutput('Num_attrs', $num_attrs);
    $i = 0;
    foreach ($values as $record)
    {
        $submod = $tplm->generateSubmodule('OptionalAttributes', 'OptionalAttribute', $mod);
        $submod->setNamespace('location');
        $submod->addOutput('Record_Id', $record['id']);
        $submod->addOutput('Index', $i);

        if (strlen ($record['value']))
            $submod->addOutput('Deletable', TRUE);

        $submod->addOutput('Record_Name', $record['name']);
        $submod->setOutput('Record_Value', $record['value']);
        switch ($record['type'])
        {
        case 'uint':
        case 'float':
        case 'string':
            $submod->addOutput('Switch_Option', 'ONE');
            break;
        case 'dict':
            $submod->addOutput('Switch_Option', 'TWO');
            $chapter = readChapter ($record['chapter_id'], 'o');
            $chapter[0] = '-- NOT SET --';
            $chapter = cookOptgroups ($chapter, 1562, $record['key']);
            $submod->addOutput('Nifty_Select', getNiftySelect( $chapter, array ('name' => "${i}_value"), $record['key']));
            break;
        }
        $i++;
    }
    if ($location['has_problems'] == 'yes')
        $mod->setOutput('Has_Problems', TRUE);
    if (count ($location['locations']) == 0 and count ($location['rows']) == 0)
    {
        $mod->setOutput('Empty_Locations', TRUE);
    }
    $mod->addOutput('Location_Comment', $location['comment']);

    renderObjectHistory ($location_id, $mod, 'Objecthistory');
}

function renderRackPage ($rack_id)
{
    $rackData = spotEntity ('rack', $rack_id);
    amplifyCell ($rackData);
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderRackPage");
    $mod->setNamespace("rack");

    // Left column with information.
    renderRackInfoPortlet ($rackData, $mod, "InfoPortlet");
    renderFilesPortlet ('rack', $rack_id, $mod, "FilesPortlet");

    // Right column with rendered rack.
    renderRack ($rack_id, 0, $mod, "RenderedRack");
}

function renderDictionary ()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderDictionary');
    $mod->setNamespace('dict');

    $chapterListOut = array();
    foreach (getChapterList() as $chapter_no => $chapter)
        $chapterListOut[] = array('Link' => mkA ($chapter['name'], 'chapter', $chapter_no), 'Records' => $chapter['wordc']);
    $mod->addOutput("ChapterList", $chapterListOut);
}

function renderChapter ($tgt_chapter_no)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderChapter');
    $mod->setNamespace('chapter', true);

    global $nextorder;
    $words = readChapter ($tgt_chapter_no, 'a');
    $wc = count ($words);
    $mod->addOutput('recordCount', $wc);

    $refcnt = getChapterRefc ($tgt_chapter_no, array_keys ($words));
    $attrs = getChapterAttributes($tgt_chapter_no);
    $order = 'odd';
    foreach ($words as $key => $value)
    {
        $submod = $tplm->generatePseudoSubmodule('ChapterList', $mod);
        $submod->addOutput('order', $order);
        $submod->addOutput('ImageType', $key < 50000 ? 'computer' : 'favorite');
        $submod->addOutput('key', $key);
        $submod->addOutput('refcnt', $refcnt[$key]);

        if ($refcnt[$key])
        {
            $cfe = '';
            foreach ($attrs as $attr_id)
            {
                if (! empty($cfe))
                    $cfe .= ' or ';

                $cfe .= '{$attr_' . $attr_id . '_' . $key . '}';
            }

            if (! empty($cfe))
            {
                $href = makeHref
                        (
                            array
                            (
                                'page'=>'depot',
                                'tab'=>'default',
                                'andor' => 'and',
                                'cfe' => $cfe
                            )
                        );

                $submod->setOutput('cfe', true);
                $submod->addOutput('href', $href);
            }
        }
        else
            $submod->setOutput('refcnt', 0);
        $submod->addOutput('value', $value);

        $order = $nextorder[$order];
    }
}

function renderChapterEditor ($tgt_chapter_no)
{
    global $nextorder;

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderChapterEditor');
    $mod->setNamespace('chapter', true);
    $words = readChapter ($tgt_chapter_no);

    $refcnt = getChapterRefc ($tgt_chapter_no, array_keys ($words));
    $order = 'odd';
    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput('NewTop',true);

    foreach ($words as $key => $value)
    {
        $submod = $tplm->generatePseudoSubmodule('ChapterList', $mod);
        $order = $nextorder[$order];
        $submod->addOutput('order', $order);
        $submod->addOutput('key', $key);
        $submod->addOutput('value', $value);
        $submod->addOutput('refcnt', $refcnt[$key]);
        // Show plain row for stock records, render a form for user's ones.
        if ($key < 50000)
        {
            $submod->addOutput('lowkey', true);
            continue;
        }
    }
}


function renderPortOIFViewer()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderPortOIFViewer');
    $mod->setNamespace('portoifs', true);

    global $nextorder;
    $order = 'odd';
    $refcnt = getPortOIFRefc();
    $allOptionsOut = array();
    foreach (getPortOIFOptions() as $oif_id => $oif_name)
    {
        $allOptionsOut[] = array('Order' => $order,
                                 'ImageHref' => getImageHREF ($oif_id < 2000 ? 'computer' : 'favorite'),
                                 'Oif_id' => $oif_id,
                                 'Refcnt' => ($refcnt[$oif_id] ? $refcnt[$oif_id] : '&nbsp;'),
                                 'NiftyString' => niftyString ($oif_name, 48));
        $order = $nextorder[$order];
    }
    $mod->setOutput('AllOptions', $allOptionsOut);
}

// Need to port?
function renderPortOIFEditor()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderPortOIFEditor');
    $mod->setNamespace('portoifs', true);

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput('NewTop');

    $refcnt = getPortOIFRefc();
    $allOptionsOut = array();
    foreach (getPortOIFOptions() as $oif_id => $oif_name)
    {
        $singleOption = array('Oif_Id' => $oif_id,
                              'NiftyString' => niftyString ($oif_name, 48));
        if ($oif_id < 2000)
        {
            $singleOption['SmallOif'] = true;
            $singleOption['ComputerImg'] = getImageHREF ('computer');
            $singleOption['Refcnt'] = ($refcnt[$oif_id] ? $refcnt[$oif_id] : '&nbsp;');

        }
        else
        {
            $singleOption['UpdOpFormInto'] = printOpFormIntro ('upd', array ('id' => $oif_id));
            $singleOption['FavImg'] = getImageHREF ('favorite');
            if ($refcnt[$oif_id])
            {
                $singleOption['Refcnt'] = $refcnt[$oif_id];
                $singleOption['NoDestroyImg'] = getImageHREF ('nodestroy', 'cannot remove');
            }
            else
            {
                $singleOption['DestroyLink'] = getOpLink (array ('op' => 'del', 'id' => $oif_id), '', 'destroy', 'remove');
            }
            $singleOption['
                          SaveImg'] = getImageHREF ('save', 'Save changes', TRUE);
        }
        $allOptionsOut[] = $singleOption;
    }
    $mod->setOutput('AllOptions', $allOptionsOut);
}

// We don't allow to rename/delete a sticky chapter and we don't allow
// to delete a non-empty chapter.
function renderChaptersEditor ()
{
    $dict = getChapterList();
    foreach (array_keys ($dict) as $chapter_no)
        $dict[$chapter_no]['mapped'] = FALSE;
    foreach (getAttrMap() as $attrinfo)
        if ($attrinfo['type'] == 'dict')
            foreach ($attrinfo['application'] as $app)
                $dict[$app['chapter_no']]['mapped'] = TRUE;

    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule('Payload', 'RenderDictEditor');
    $mod->setNamespace('dict');
    $mod->setLock();

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput('NewTop');

    foreach ($dict as $chapter_id => $chapter)
    {
        $wordcount = $chapter['wordc'];
        $sticky = $chapter['sticky'] == 'yes';

        $submod = $tplm->generatePseudoSubmodule('DictList', $mod);
        $submod->setNamespace('dict');
        if ($sticky)
            $submod->setOutput('NoDestroyMessage', 'system chapter');
        elseif ($wordcount > 0)
        $submod->setOutput('NoDestroyMessage', 'contains ' . $wordcount . ' word(s)');
        elseif ($chapter['mapped'])
        $submod->setOutput('NoDestroyMessage', 'used in attribute map');
        else
            $submod->setOutput('NoDestroyMessage', '');

        $submod->addOutput('Name', $chapter['name']);
        $submod->addOutput('ChapterId', $chapter_id);
        $submod->addOutput('Disabled', ($sticky ? ' disabled' : ''));
        $submod->addOutput('Sticky', $sticky);
        $submod->addOutput('Wordcount', $wordcount);
    }
}

function renderAttributes ()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderAttributes');
    $mod->setNamespace('attrs', true);

    global $nextorder, $attrtypes;
    $order = 'odd';
    $allAttrsOut = array();
    foreach (getAttrMap() as $attr)
    {
        $singleAttr =  array(
                           'Order' => $order,
                           'Name'  => $attr['name'],
                           'Type'  => $attrtypes[$attr['type']]
                       );
        if (count ($attr['application']) == 0)
            $singleAttr['ApplicationSet'] = '&nbsp;';
        else
        {
            $allAppAttrsOut = array();
            foreach ($attr['application'] as $app)
            {
                $singleAppAttr = array('ObjType' => decodeObjectType ($app['objtype_id'], 'a'), 'Chapter_name' => $app['chapter_name']);

                //Could be done in a inmemory template in need of change
                if ($attr['type'] == 'dict')
                    $singleAppAttr['DictCont'] = " (values from '${app['chapter_name']}')";

                $allAppAttrsOut[] = $singleAppAttr;
            }
            $singleAttr['AllAttrsMap'] = $allAppAttrsOut;
        }
        $allAttrsOut[] = $singleAttr;
        $order = $nextorder[$order];
    }
    $mod->addOutput("AllAttrs", $allAttrsOut);

}

function renderEditAttributesForm ()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderEditAttributesForm');
    $mod->setNamespace('attrs', true);

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput('NewTop',true);

    global $attrtypes;
    $mod->addOutput('CreateNewSelect', getSelect($attrtypes, array ('name' => 'attr_type', 'tabindex' => 101)));

    $allAttrMapsOut = array();
    foreach (getAttrMap() as $attr)
    {
        $singleAttrMap = array( 'Name' => $attr['name'],
                                'Type' =>$attr['type'],
                                'OpFormIntro' => printOpFormIntro ('upd', array ('attr_id' => $attr['id'])));

        if($attr['id'] < 10000)
            $singleAttrMap['DestroyImg'] = printImageHREF ('nodestroy', 'system attribute');
        elseif (count ($attr['application']))
        $singleAttrMap['DestroyImg'] = printImageHREF ('nodestroy', count ($attr['application']) . ' reference(s) in attribute map');
        else
            $singleAttrMap['DestroyImg'] = getOpLink (array('op'=>'del', 'attr_id'=>$attr['id']), '', 'destroy', 'Remove attribute');
        $singleAttrMap['SaveImg'] = printImageHREF ('save', 'Save changes', TRUE);
        $allAttrMapsOut[] = $singleAttrMap;
    }
    $mod->addOutput("AllAttrMaps", $allAttrMapsOut);
}

function renderEditAttrMapForm ()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderEditAttrMapForm');
    $mod->setNamespace('attrs', true);

    function printNewItemTR ($placeholder, $mod, $attrMap)
    {
        $tplm = TemplateManager::getInstance();
        $submod = $tplm->generateSubmodule($placeholder, 'RenderEditAttrMapForm_PrintNewItemTR', $mod);
        $submod->setNamespace('attrs', true);
        $shortType['uint'] = 'U';
        $shortType['float'] = 'F';
        $shortType['string'] = 'S';
        $shortType['dict'] = 'D';
        $shortType['date'] = 'T';
        $allAttrMapsOut = array();
     
        foreach ($attrMap as $attr)
           $allAttrMapsOut[] = array('Id' => $attr['id'], 'Shorttype' => $shortType[$attr['type']], 'Name' => $attr['name']);
        $submod->addOutput("AllAttrMaps", $allAttrMapsOut);

        $objtypes = readChapter (CHAP_OBJTYPE, 'o');
        $groupList = cookOptgroups ($objtypes);
        $submod->addOutput('Getselect', getSelect ($groupList['other'], array ('name' => 'objtype_id', 'tabindex' => 101), NULL));
        $allChaptersOut = array();

        foreach (getChapterList() as $chapter)
        {
            if ($chapter['sticky'] != 'yes')
                $allChaptersOut[] = array('Id' => $chapter['id'], 'Name' => $chapter['name']);
            $submod->setOutput('AllChapters', $allChaptersOut);
        }

    }
    global $attrtypes, $nextorder;
    $order = 'odd';
    $attrMap = getAttrMap();
    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput('NewTop',true);

    $shortType['uint'] = 'U';
    $shortType['float'] = 'F';
    $shortType['string'] = 'S';
    $shortType['dict'] = 'D';
    $shortType['date'] = 'T';
    $allAttrMapsOut = array();
    
    foreach ($attrMap as $attr)
        $allAttrMapsOut[] = array('Id' => $attr['id'], 'Shorttype' => $shortType[$attr['type']], 'Name' => $attr['name']);
    $mod->addOutput("CreateNewAttrMaps", $allAttrMapsOut);

    $objtypes = readChapter (CHAP_OBJTYPE, 'o');
    $groupList = cookOptgroups ($objtypes);
    $mod->addOutput('CreateNewSelect', getSelect ($groupList['other'], array ('name' => 'objtype_id', 'tabindex' => 101), NULL));
    $allChaptersOut = array();

    foreach (getChapterList() as $chapter)
    {
        if ($chapter['sticky'] != 'yes')
            $allChaptersOut[] = array('Id' => $chapter['id'], 'Name' => $chapter['name']);
        $submod->setOutput('CreateNewChapters', $allChaptersOut);
    }


    foreach ($attrMap as $attr)
    {
        if (!count ($attr['application']))
        {
            continue;
        }

        $submod = $tplm->generatePseudoSubmodule('AttrMap', $mod);
        $submod->addOutput('AttrTypes', $attrtypes[$attr['type']]);
        $submod->addOutput('Name', $attr['name']);
        $submod->addOutput('Order', $order);

        foreach ($attr['application'] as $app)
        {
            $singleAttrApp = $tplm->generatePseudoSubmodule('AttrMapChilds', $submod,
                             array('Sticky' => $app['sticky'],
                                   'RefCnt' => $app['refcnt'],
                                   'Id' => $attr['id'],
                                   'ObjId' => $app['objtype_id'],
                                   'Type' => $attr['type'],
                                   'ChapterName' => $app['chapter_name'],
                                   'DecObj' => decodeObjectType ($app['objtype_id'], 'o')));
        }
        $order = $nextorder[$order];
    }
}

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
                   'func' => 'getRackCodeStats'
               ),
               array
               (
                   'title' => 'Warnings',
                   'type' => 'messages',
                   'func' => 'getRackCodeWarnings'
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
    $tplm = TemplateManager::getInstance();

    if (!count ($domains = getVLANDomainOptions()))
    {
        $mod = $tplm->generateSubmodule("Payload","NoVLANConfig", true);
        return;
    }
    $mod = $tplm->generateSubmodule("Payload","Render8021QReport");
    $mod->setNamespace("reports");

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

    $outputArray = array();
    foreach ($output as $vlan_id => $tbc)
    {
        $singleElemOut = array();
        if (--$header_delay <= 0)
        {
            $singleElemOut['Header'] = $header;
            $header_delay = 25;
        }
        else
            $singleElemOut['Header'] = '';

        $singleElemOut['CountStats'] = (count ($vlanstats[$vlan_id]) ? 'T' : 'F');
        $singleElemOut['VlanId'] = $vlan_id;
        $singleElemOut['Domains'] = '';
        foreach (array_keys ($domains) as $domain_id)
        {

            $singleCell = $tplm->generateModule("StdCenterTableCell", true);
            if (array_key_exists ($domain_id, $vlanstats[$vlan_id]))
                $singleCell->setOutput("Cont", mkA ('&exist;', 'vlan', "${domain_id}-${vlan_id}"));
            else
                $singleCell->setOutput("Cont", '&nbsp;');
            $singleElemOut['Domains'] = $singleElemOut['Domains'] . $singleCell->run();
        }
        if ($tbc)
        {
            $singleElemOut['TbcLine'] = $tplm->generateModule('TbcLineMod',true, array('CountDomains' => count ($domains)))->run();

        }
        $outputArray[] = $singleElemOut;
    }
    $mod->setOutput("OutputArr", $outputArray);
}

function renderReports ($what)
{
    if (!count ($what))
        return;
    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule("Payload","RenderReports");
    $mod->setNamespace("reports");

    $itemContArr = array();
    foreach ($what as $item)
    {
        $singleItemArr = array('Title' => $item['title']);
        $singleItemArr['Cont'] = '';
        switch ($item['type'])
        {
        case 'counters':
            if (array_key_exists ('args', $item))
                $data = $item['func'] ($item['args']);
            else
                $data = $item['func'] ();
            foreach ($data as $header => $data)
            {
                $singleMod = $tplm->generateModule("ReportsCounter", true);
                $singleMod->setOutput("Header", $header);
                $singleMod->setOutput("Data", $data);
                $singleItemArr['Cont'] .= $singleMod->run();
            }
            break;
        case 'messages':
            if (array_key_exists ('args', $item))
                $data = $item['func'] ($item['args']);
            else
                $data = $item['func'] ();

            foreach ($data as $msg)
            {
                $singleMod = $tplm->generateModule("ReportsMessages", true);
                $singleMod->setOutput("Class", $msg['class']);
                $singleMod->setOutput("Header", $msg['header']);
                $singleMod->setOutput("Text", $msg['text']);
                $singleItemArr['Cont'] .= $singleMod->run();
            }
            break;
        case 'meters':
            if (array_key_exists ('args', $item))
                $data = $item['func'] ($item['args']);
            else
                $data = $item['func'] ();
            foreach ($data as $meter)
            {
                $singleMod = $tplm->generateModule("ReportsMeters", true);
                $singleMod->setOutput("Title", $meter['title']);
                $singleMod->setOutput("ProgressBar", getProgressBar ($meter['max'] ? $meter['current'] / $meter['max'] : 0));
                $singleMod->setOutput("IsMax", ($meter['max'] ? $meter['current'] . '/' . $meter['max'] : '0'));
                $singleItemArr['Cont'] .= $singleMod->run();
            }
            break;
        case 'custom':
            $singleMod = $tplm->generateModule("ReportsCustom", true);
            $singleMod->setOutput("ItemCont", "" . $item['func']());
            $singleItemArr['Cont'] .= $singleMod->run();
            break;
        default:
            throw new InvalidArgException ('type', $item['type']);
        }
        $itemContArr[] = $singleItemArr;
    }
    $mod->setOutput("ItemContent", $itemContArr);
}

function renderTagStats ()
{

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateModule("RenderTagStats");

    global $taglist;
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
    $allTagsOut = array();
    foreach (getTagChart (getConfigVar ('TAGS_TOPLIST_SIZE')) as $taginfo)
    {
        $singleTag = array('taginfo' => $taginfo['tag'], 'taginfoRefcnt' => $taginfo['refcnt']['total']);
        $singleTag['realms'] = '';
        foreach (array ('object', 'ipv4net', 'ipv6net', 'rack', 'ipv4vs', 'ipv4rspool', 'user', 'file') as $realm)
        {
            $realmMod = $tplm->generateModule('StdTableCell', true);

            if (!isset ($taginfo['refcnt'][$realm]))
                $realmMod->setOutput('cont', '&nbsp;');
            else
            {
                $realmLinkMod = $tplm->generateSubmodule('cont', 'RenderTagStatsALink', $realmMod, true, array('Pagerealm' => $pagebyrealm[$realm],
                                'TaginfoID' => $taginfo['id'], 'Taginfo' => $taginfo['refcnt'][$realm]));
           }
            $singleTag['realms'] .= $realmMod->run();
        }
        $allTagsOut[] = $singleTag;
    }
    $mod->setOutput("allTags", $allTagsOut);

    return $mod->run();
}

function dragon ()
{
    startPortlet ('Here be dragons');
?>
<div class=dragon><pre><font color="#00ff33">
                 \||/
                 |  <font color="#ff0000">@</font>___oo
       /\  /\   / (__<font color=yellow>,,,,</font>|
      ) /^\) ^\/ _)
      )   /^\/   _)
      )   _ /  / _)
  /\  )/\/ ||  | )_)
 &lt;  &gt;      |(<font color=white>,,</font>) )__)
  ||      /    \)___)\
  | \____(      )___) )___
   \______(_______<font color=white>;;;</font> __<font color=white>;;;</font>

</font></pre></div>
<?php
    finishPortlet();
}

// $v is a $configCache item
// prints HTML-formatted varname and description
function renderConfigVarName ($v)
{
    $tplm = TemplateManager::getInstance(); 
    $mod = $tplm->generateModule("RenderConfigVarName", true);
    $mod->addOutput("vname", $v['varname']);
    $mod->addOutput("desAndIsDefined",  $v['description'] . ($v['is_userdefined'] == 'yes' ? '' : ' (system-wide)'));
    return $mod->run();
}

function renderUIConfig ()
{
    global $nextorder;
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderUiConfig");
    $mod->setNamespace("ui");

    $order = 'odd';
    $allLoadConfigCacheOut = array();
    foreach (loadConfigCache() as $v)
    {
        if ($v['is_hidden'] != 'no')
            continue;
        $singleCache = array('order' => $order, 'varvalue' => $v['varvalue']);
        $singleCache['renderedConfigVarName'] = renderConfigVarName ($v);
        //renderConfigVarName ($v);
        $order = $nextorder[$order];
        $allLoadConfigCacheOut[] = $singleCache;
    }
    $mod->addOutput("allLoadConfigCache", $allLoadConfigCacheOut);
}

function renderSNMPPortFinder ($object_id)
{
    $tplm = TemplateManager::getInstance();

    if (!extension_loaded ('snmp'))
    {
        $mod = $tplm->generateSubmodule("Payload","RenderSNMPPortFinder_NoExt", null, true);
        return;
    }

    $mod = $tplm->generateSubmodule("Payload","RenderSNMPPortFinder");
    $mod->setNamespace("object");

    $snmpcomm = getConfigVar('DEFAULT_SNMP_COMMUNITY');
    if (empty($snmpcomm))
        $snmpcomm = 'public';

    $mod->addOutput("snmpcomm", $snmpcomm);
}

function renderUIResetForm()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderUiResetForm");
    $mod->setNamespace("ui");
}

function renderLivePTR ($id)
{
    if (isset($_REQUEST['pg']))
        $page = $_REQUEST['pg'];
    else
        $page=0;
    global $pageno, $tabno;
    $maxperpage = getConfigVar ('IPV4_ADDRS_PER_PAGE');
    $range = spotEntity ('ipv4net', $id);
    loadIPAddrList ($range);

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'renderLivePTR');
    $mod->setNamespace('ipnetwork',true);

    $mod->addOutput('IP', $range['ip']);
    $mod->addOutput('Mask', $range['mask']);
    $mod->addOutput('Name', $range['name']);

    $can_import = permitted (NULL, NULL, 'importPTRData');

    $startip = ip4_bin2int ($range['ip_bin']);
    $endip = ip4_bin2int (ip_last ($range));
    $numpages = 0;
    if ($endip - $startip > $maxperpage)
    {
        $numpages = ($endip - $startip) / $maxperpage;
        $startip = $startip + $page * $maxperpage;
        $endip = $startip + $maxperpage - 1;
    }
    if ($numpages)
    {
        $mod->addOutput('Paged', true);
        $mod->addOutput('StartIP', ip4_format (ip4_int2bin ($startip)));
        $mod->addOutput('EndIP', ip4_format (ip4_int2bin ($endip)));
    }
    for ($i=0; $i<$numpages; $i++)
    {
        if ($i == $page)
        {
            $smod = $tplm->generateSubmodule('Pages', 'IPNetworkAddressesPager', $mod);
            $smod->addOutput('B', '<b>');
            // TODO: fix redundant placholder name B
            $smod->addOutput('B', '</b>');
            $smod->addOutput('I', $i);
            $smod->addOutput('Link', makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'id'=>$id, 'pg'=>$i)));
        }
        else
        {
            $smod = $tplm->generateSubmodule('Pages', 'IPNetworkAddressesPager', $mod);
            $smod->addOutput('I', $i);
            $smod->addOutput('Link', makeHref(array('page'=>$pageno, 'tab'=>$tabno, 'id'=>$id, 'pg'=>$i)));
        }
    }

    // FIXME: address counter could be calculated incorrectly in some cases
    if ($can_import)
    {
        $mod->setOutput('AddrCount', ($endip - $startip + 1));
        $mod->setOutput('IsImport', true);
        $idx = 1;
        $box_counter = 1;
    }

    $cnt_match = $cnt_mismatch = $cnt_missing = 0;
    for ($ip = $startip; $ip <= $endip; $ip++)
    {
        // Find the (optional) DB name and the (optional) DNS record, then
        // compare values and produce a table row depending on the result.
        $ip_bin = ip4_int2bin ($ip);
        $addr = isset ($range['addrlist'][$ip_bin]) ? $range['addrlist'][$ip_bin] : array ('name' => '', 'reserved' => 'no');
        $straddr = ip4_format ($ip_bin);
        $ptrname = gethostbyaddr ($straddr);
        if ($ptrname == $straddr)
            $ptrname = '';

        $smod = $tplm->generatePseudoSubmodule('IPList', $mod);

        $smod->addOutput('IDx', $idx);
        $smod->addOutput('StrAddr', $straddr);
        $smod->addOutput('PtrName', $ptrname);
        $smod->addOutput('Reserved', $addr['reserved']);

        if ($can_import)
            $smod->setOutput('IsImport', true);

        $print_cbox = FALSE;
        // Ignore network and broadcast addresses
        if (($ip == $startip && $addr['name'] == 'network') || ($ip == $endip && $addr['name'] == 'broadcast'))
            $smod->setOutput('CSSClass', 'trbusy');
        if ($addr['name'] == $ptrname)
        {
            if (strlen ($ptrname))
            {
                $smod->setOutput('CSSClass', 'trok');
                $cnt_match++;
            }
        }
        elseif (!strlen ($addr['name']) or !strlen ($ptrname))
        {
            $smod->setOutput('CSSClass', 'trwarning');
            $print_cbox = TRUE;
            $cnt_missing++;
        }
        else
        {
            $smod->setOutput('CSSClass', 'trerror');
            $print_cbox = TRUE;
            $cnt_mismatch++;
        }
        if (isset ($range['addrlist'][$ip_bin]['class']) and strlen ($range['addrlist'][$ip_bin]['class']))
            $smod->addOutput('CSSTDClass', $range['addrlist'][$ip_bin]['class']);
        $smod->addOutput('Link', mkA ($straddr, 'ipaddress', $straddr));
        $smod->addOutput('Name', $addr['name']);
        if ($can_import)
        {
            if ($print_cbox)
            {
                $smod->setOutput('BoxCounter', $box_counter++);
            }
            $idx++;
        }
    }
    if ($can_import && $box_counter > 1)
    {
        if(--$box_counter)
        {
            $mod->addOutput('BoxCounterJS', $box_counter);
        }
    }

    $mod->addOutput('Match', $cnt_match);
    $mod->addOutput('Missing', $cnt_missing);
    if ($cnt_mismatch)
        $mod->addOutput('MisMatch', $cnt_mismatch);
}

function renderAutoPortsForm ($object_id)
{
    $info = spotEntity ('object', $object_id);
    $ptlist = getPortOIFOptions();

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderAutoPortsForm");
    $mod->setNamespace("object");

    $allAutoPortsOut = array();
    foreach (getAutoPorts ($info['objtype_id']) as $autoport)
        $allAutoPortsOut[] = array('type' => $ptlist[$autoport['type']], 'name' => $autoport['name']);
    $mod->addOutput("allAutoPorts", $allAutoPortsOut);
}

function renderTagRowForViewer ($taginfo, $level = 0, $parent)
{
    $self = __FUNCTION__;

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generatePseudoSubmodule("Taglist", $parent);

    $statsdecoder = array
                    (
                        'total' => ' total records linked',
                        'object' => ' object(s)',
                        'rack' => ' rack(s)',
                        'file' => ' file(s)',
                        'user' => ' user account(s)',
                        'ipv6net' => ' IPv6 network(s)',
                        'ipv4net' => ' IPv4 network(s)',
                        'ipv4vs' => ' IPv4 virtual service(s)',
                        'ipv4rspool' => ' IPv4 real server pool(s)',
                        'vst' => ' VLAN switch template(s)',
                    );

    $trclass = '';
    if ($level == 0)
        $trclass .= ' separator';
    $trclass .= $taginfo['is_assignable'] == 'yes' ? '' : ($taginfo['kidc'] ? ' trnull' : ' trwarning');
    if (!count ($taginfo['kids']))
        $level++; // Shift instead of placing a spacer. This won't impact any nested nodes.
    $refc = $taginfo['refcnt']['total'];
  
    if ($taginfo['is_assignable'] == 'yes')
        $mod->addOutput('Assignable', true);
    else
        $mod->addOutput('Assignable', false);

    $mod->setOutput('HasChildren', (count ($taginfo['kids'])));
    $mod->setOutput('Trclass', $trclass);
    $stats = array ("tag ID = ${taginfo['id']}");
    if ($taginfo['refcnt']['total'])
        foreach ($taginfo['refcnt'] as $article => $count)
            if (array_key_exists ($article, $statsdecoder))
                $stats[] = $count . $statsdecoder[$article];
    $mod->addOutput('Stats', implode(', ', $stats));
    $mod->addOutput('SpanClass', getTagClassName($taginfo['id']));
    $mod->addOutput('Tag', $taginfo['tag']);
    $mod->addOutput('Refc', $refc ? $refc : '');
    $mod->addOutput('Level', $level );

    foreach ($taginfo['kids'] as $kid)
        $self ($kid, $level + 1, $parent);
}

function renderTagRowForEditor ($taginfo, $level = 0, $parent, $placeholder)
{
    $self = __FUNCTION__;
    global $taglist;

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generatePseudoSubmodule($placeholder, $parent);
    $mod->setNamespace('tagtree');

    if (!count ($taginfo['kids']))
        $level++; // Idem

    $mod->addOutput('Assignable', $taginfo['is_assignable'] == 'yes' ? true : false);
    $mod->addOutput('AssignableInfo', $taginfo['is_assignable']);
    $mod->addOutput('hasChildren', $taginfo['kidc'] ? true : false);
    $mod->addOutput('hasReferences', ($taginfo['refcnt']['total'] > 0 || $taginfo['kidc']));
    $mod->setOutput('Level', $level);
    $mod->addOutput('ID', $taginfo['id']);
    $mod->addOutput('Tag', $taginfo['tag']);
    $mod->addOutput('References', $taginfo['refcnt']['total']);
    $mod->addOutput('Subtags', $taginfo['kidc']);
    
    $parent_id = $taginfo['parent_id'] ? $taginfo['parent_id'] : 0;
    $parent_name = $taginfo['parent_id'] ? htmlspecialchars ($taglist[$taginfo['parent_id']]['tag']) : '-- NONE --';
    $mod->addOutput('ParentSelect', getSelect
                    (
                        array ($parent_id => $parent_name),
                        array ('name' => 'parent_id', 'id' => 'tagid_' . $taginfo['id'], 'class' => 'taglist-popup'),
                        $taginfo['parent_id'],
                        FALSE
                    ));

    foreach ($taginfo['kids'] as $kid)
        $self ($kid, $level + 1, $mod, 'Taglist');
}

function renderTagTree ()
{
    global $tagtree;
    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule('Payload', 'RenderTagtree');
    $mod->setNamespace('tagtree',true);

    foreach ($tagtree as $taginfo)
        renderTagRowForViewer ($taginfo, 0, $mod);
}

function renderTagTreeEditor ()
{
    global $taglist, $tagtree;

    $options = array (0 => '-- NONE --');
    foreach ($taglist as $taginfo)
        $options[$taginfo['id']] = htmlspecialchars ($taginfo['tag']);
    $mod->setOutput('CreateNewOptions', $options);

    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule('Payload', 'renderTagtreeEditor');
    $mod->setNamespace('tagtree');

    $otags = getOrphanedTags();
    if (count ($otags))
    {
        foreach ($otags as $taginfo)
        {
            $smod = $tplm->generatePseudoSubmodule('OTags', $mod);
            $smod->setNamespace('tagtree');
            $smod->addOutput('Name', $taginfo['tag']);
            $smod->addOutput('ID', $taginfo['id']);
            $smod->addOutput('Assignable', $taginfo['is_assignable']);
            $smod->addOutput('Select', getSelect ($options, array ('name' => 'parent_id'), $taglist[$taginfo['id']]['parent_id']));

        }
    }


    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput("NewTop", true);

    foreach ($tagtree as $taginfo)
        renderTagRowForEditor ($taginfo, 0, $mod, 'Taglist');  
}

# Return a list of items representing tags with checkboxes.
function buildTagCheckboxRows ($inputname, $preselect, $neg_preselect, $taginfo, $refcnt_realm = '', $level = 0)
{
    static $is_first_time = TRUE;
    $inverted = tagOnChain ($taginfo, $neg_preselect);
    $selected = tagOnChain ($taginfo, $preselect);
    $ret = array
           (
               'tr_class' => ($level == 0 && $taginfo['id'] > 0 && ! $is_first_time) ? 'separator' : '',
               'td_class' => 'tagbox',
               'level' => $level,
# calculate HTML classnames for separators feature
               'input_class' => $level ? 'tag-cb' : 'tag-cb root',
               'input_value' => $taginfo['id'],
               'text_tagname' => $taginfo['tag'],
           );
    $is_first_time = FALSE;
    $prepared_inputname = $inputname;
    if ($inverted)
    {
        $ret['td_class'] .= ' inverted';
        $prepared_inputname = preg_replace ('/^cf/', 'nf', $prepared_inputname);
    }
    $ret['input_name'] = $prepared_inputname;
    if ($selected)
    {
        $ret['td_class'] .= $inverted ? ' selected-inverted' : ' selected';
        $ret['input_extraattrs'] = 'checked';
    }
    if (array_key_exists ('is_assignable', $taginfo) and $taginfo['is_assignable'] == 'no')
    {
        $ret['input_extraattrs'] = 'disabled';
        $ret['tr_class'] .= (array_key_exists ('kidc', $taginfo) and $taginfo['kidc'] == 0) ? ' trwarning' : ' trnull';
    }
    if (strlen ($refcnt_realm) and isset ($taginfo['refcnt'][$refcnt_realm]))
        $ret['text_refcnt'] = $taginfo['refcnt'][$refcnt_realm];
    $ret = array ($ret);
    if (array_key_exists ('kids', $taginfo))
        foreach ($taginfo['kids'] as $kid)
            $ret = array_merge ($ret, call_user_func (__FUNCTION__, $inputname, $preselect, $neg_preselect, $kid, $refcnt_realm, $level + 1));
    return $ret;
}

# generate HTML from the data produced by the above function
function printTagCheckboxTable ($input_name, $preselect, $neg_preselect, $taglist, $realm = '', TemplateModule $addto = null, $placeholder = "tagCheckbox")
{
    $tplm = TemplateManager::getInstance();
    foreach ($taglist as $taginfo)
        foreach (buildTagCheckboxRows ($input_name, $preselect, $neg_preselect, $taginfo, $realm) as $row)
        {

            $tag_class = isset ($taginfo['id']) && isset ($taginfo['refcnt']) ? getTagClassName ($row['input_value']) : '';

            if ($addto != null)
            {
                if($placeholder == "")
                    $tagobj = $tplm->generateSubmodule("checkbox", "TagTreeCell", $addto);
                else
                    $tagobj = $tplm->generateSubmodule($placeholder, "TagTreeCell", $addto);
            }
            else
            {
                $tagobj = $tplm->generateModule("TagTreeCell");
            }
            $tagobj->setNamespace("",true);
            $tagobj->setLock();
            $tagobj->setOutput("TrClass", 		$row['tr_class']);
            $tagobj->setOutput("TdClass", 		$row['td_class']);
            $tagobj->setOutput("LevelPx", 		$row['level'] * 16);
            $tagobj->setOutput("InputClass",	$row['input_class']);
            $tagobj->setOutput("InputName",		$row['input_name']);
            $tagobj->setOutput("InputValue",	$row['input_value']);
            if (array_key_exists ('input_extraattrs', $row))
            {
                $tagobj->setOutput("ExtraAttrs", ' ' . $row['input_extraattrs']);
            }
            else
            {
                $tagobj->setOutput("ExtraAttrs","");
            }
            $tagobj->setOutput("TagClass",		$tag_class);
            $tagobj->setOutput("TagName", 		$row['text_tagname']);

            if (array_key_exists ('text_refcnt', $row))
            {
                $tagobj->setOutput("RefCnt", 	$row['text_refcnt']);
            }
        }

    if($addto == null)
    {
        return $tagobj->run();
    }
}

function renderEntityTagsPortlet ($title, $tags, $preselect, $realm, TemplateModule $parent = null, $placeholder = "RenderedEntityTagsPortlet")
{
    $tplm = TemplateManager::getInstance();
    if($parent==null)
        $mod = $tplm->generateModule("RenderEntityTagsPortlet");
    else
        $mod = $tplm->generateSubmodule($placeholder, "RenderEntityTagsPortlet", $parent);
    $mod->setOutput("title", $title);
    
    printTagCheckboxTable('taglist', $preselect, array(), $tags, $realm, $mod, "TagCheckbox");
    if (!count ($preselect))
        $mod->setOutput("preSelect", false);

    if($parent==null)
        return $mod->run();
}

function renderEntityTags ($entity_id)
{
    global $tagtree, $taglist, $target_given_tags, $pageno, $etype_by_pageno;

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderEntityTags");

    if (count ($taglist) > getConfigVar ('TAGS_QUICKLIST_THRESHOLD'))
    {
        $minilist = getTagChart (getConfigVar ('TAGS_QUICKLIST_SIZE'), $etype_by_pageno[$pageno], $target_given_tags);
        // It could happen, that none of existing tags have been used in the current realm.
        if (count ($minilist))
        {
            $js_code = "tag_cb.setTagShortList ({";
            $is_first = TRUE;
            foreach ($minilist as $tag)
            {
                if (! $is_first)
                    $js_code .= ",";
                $is_first = FALSE;
                $js_code .= "\n\t${tag['id']} : 1";
            }
            $js_code .= "\n});\n$(document).ready(tag_cb.compactTreeMode);";
            $mod->setOutput("JsCode", $js_code);
        }
    }

    // do not do anything about empty tree, trigger function ought to work this out
    //renderEntityTagsPortlet ('Tag tree', $tagtree, $target_given_tags, $etype_by_pageno[$pageno], $mod);
    renderEntityTagsPortlet ('Tag tree', $tagtree, $target_given_tags, $etype_by_pageno[$pageno], $mod, "RenderedEnityTags");
}

// This one is going to replace the tag filter.
function renderCellFilterPortlet ($preselect, $realm, $cell_list = array(), $bypass_params = array(), $parent = null, $parentplaceholder = "CellFilterPortlet")
{
    global $pageno, $tabno, $taglist, $tagtree;
    $filterc =
        (
            count ($preselect['tagidlist']) +
            count ($preselect['pnamelist']) +
            (mb_strlen ($preselect['extratext']) ? 1 : 0)
        );
    $title = $filterc ? "Tag filters (" . $filterc . ")" : 'Tag filters';

    $tplm = TemplateManager::getInstance();

    if($parent == null)
    {
        $mod = $tplm->generateModule("CellFilterPortlet");
    }
    else
        $mod = $tplm->generateSubmodule($parentplaceholder, "CellFilterPortlet", $parent);
    $mod->setNamespace("");
    $mod->setLock(true);
    $mod->setOutput("PortletTitle", $title);

    $rulerfirst = true;
    // "reset filter" button only gets active when a filter is applied
    $enable_reset = FALSE;
    // "apply filter" button only gets active when there are checkbox/textarea inputs on the roster
    $enable_apply = FALSE;
    // and/or block
    if (getConfigVar ('FILTER_SUGGEST_ANDOR') == 'yes' or strlen ($preselect['andor']))
    {
        if (!$rulerfirst)
        {
            $tplm->generateSubmodule("TableContent", "CellFilterSpacer", $mod, true);
        }
        else
            $rulerfirst = false;
        $andormod = $tplm->generateSubmodule("TableContent", "CellFilterAndOr",$mod);

        $andor = strlen ($preselect['andor']) ? $preselect['andor'] : getConfigVar ('FILTER_DEFAULT_ANDOR');
        $cells = array();
        foreach (array ('and', 'or') as $boolop)
        {
            $arr = array();
            $arr["Selected"] = ($andor == $boolop ? 'selected' : '');
            $arr["Boolop"] = $boolop;
            $arr["Checked"] = ($andor == $boolop ? 'checked' : '');
            $cells[] = $arr;
        }
        $andormod->addOutput("AndOr", $cells);
    }

    $negated_chain = array();
    foreach ($preselect['negatedlist'] as $key)
        $negated_chain[] = array ('id' => $key);
    // tags block
    if (getConfigVar ('FILTER_SUGGEST_TAGS') == 'yes' or count ($preselect['tagidlist']))
    {
        if (count ($preselect['tagidlist']))
            $enable_reset = TRUE;

        if (!$rulerfirst)
        {
            $tplm->generateSubmodule("TableContent", "CellFilterSpacer", $mod, true);
        }
        else
            $rulerfirst = false;
        // Show a tree of tags, pre-select according to currently requested list filter.
        $objectivetags = getShrinkedTagTree($cell_list, $realm, $preselect);
        if (!count ($objectivetags))
            $tplm->generateSubmodule("TableContent", "CellFilterNoTags", $mod, true);
        else
        {
            $enable_apply = TRUE;
            printTagCheckboxTable ('cft', buildTagChainFromIds ($preselect['tagidlist']), $negated_chain, $objectivetags, $realm, $mod, "TableContent");
        }

        if (getConfigVar('SHRINK_TAG_TREE_ON_CLICK') == 'yes')
            $mod->setOutput('EnableSubmitOnClick', true);
    }
    // predicates block
    if (getConfigVar ('FILTER_SUGGEST_PREDICATES') == 'yes' or count ($preselect['pnamelist']))
    {
        if (count ($preselect['pnamelist']))
            $enable_reset = TRUE;

        if (!$rulerfirst)
        {
            $tplm->generateSubmodule("TableContent", "CellFilterSpacer", $mod, true);
        }
        else
            $rulerfirst = false;
        global $pTable;
        $myPredicates = array();
        $psieve = getConfigVar ('FILTER_PREDICATE_SIEVE');
        // Repack matching predicates in a way, which tagOnChain() understands.
        foreach (array_keys ($pTable) as $pname)
            if (preg_match ("/${psieve}/", $pname))
                $myPredicates[] = array ('id' => $pname, 'tag' => $pname);
        if (!count ($myPredicates))
            $tplm->generateSubmodule("TableContent", "CellFilterNoPredicates", $mod, true);
        else
        {
            $enable_apply = TRUE;
            // Repack preselect likewise.
            $myPreselect = array();
            foreach ($preselect['pnamelist'] as $pname)
                $myPreselect[] = array ('id' => $pname);
            printTagCheckboxTable ('cfp', $myPreselect, $negated_chain, $myPredicates, '',  $mod, "TableContent");
        }
    }
    // extra code
    $enable_textify = FALSE;
    if (getConfigVar ('FILTER_SUGGEST_EXTRA') == 'yes' or strlen ($preselect['extratext']))
    {
        $enable_textify = !empty ($preselect['text']) || !empty($preselect['extratext']);
        $enable_apply = TRUE;
        if (strlen ($preselect['extratext']))
            $enable_reset = TRUE;

        if (!$rulerfirst)
        {
            $tplm->generateSubmodule("TableContent", "CellFilterSpacer", $mod, true);
            $rulerfirst = false;
        }
        $class = isset ($preselect['extraclass']) ? 'class=' . $preselect['extraclass'] : '';
        $tplm->generateSubmodule("TableContent", "CellFilterExtraText", $mod, true, array("Class"=>$class,"Extratext"=>$preselect["extratext"]));
    }
    // submit block
    {
        if (!$rulerfirst)
        {
            $tplm->generateSubmodule("TableContent", "CellFilterSpacer", $mod, true);
            $rulerfirst = false;
        }
        // "apply"
        $mod->setOutput('PageNo', $pageno);
        $mod->setOutput('TabNo', $tabno);
        $bypass_out = '';
        foreach ($bypass_params as $bypass_name => $bypass_value)
            $bypass_out .= '<input type=hidden name="' . htmlspecialchars ($bypass_name, ENT_QUOTES) . '" value="' . htmlspecialchars ($bypass_value, ENT_QUOTES) . '">' . "\n";
        $mod->setOutput("HiddenParams", $bypass_out);
        // FIXME: The user will be able to "submit" the empty form even without a "submit"
        // input. To make things consistent, it is necessary to avoid pritning both <FORM>
        // and "and/or" radio-buttons, when enable_apply isn't TRUE.
        if (!$enable_apply)
            $mod->setOutput("EnableApply", false);
        else
            $mod->setOutput("EnableApply", true);
        if ($enable_textify)
        {
            $text = empty ($preselect['text']) || empty ($preselect['extratext'])
                    ? $preselect['text']
                    : '(' . $preselect['text'] . ')';
            $text .= !empty ($preselect['extratext']) && !empty ($preselect['text'])
                     ? ' ' . $preselect['andor'] . ' '
                     : '';
            $text .= empty ($preselect['text']) || empty ($preselect['extratext'])
                     ? $preselect['extratext']
                     : '(' . $preselect['extratext'] . ')';
            $text = addslashes ($text);
            $submod = $tplm->generateSubmodule("Textify", "CellFilterPortletTextify", $mod);
            $submod->setOutput("Text", $text);
        }
        // "reset"
        if (!$enable_reset)
            $mod->setOutput("EnableReset",false);
        else
        {
            $mod->setOutput('PageNo', $pageno);
            $mod->setOutput('TabNo', $tabno);
            $mod->setOutput("EnableReset",true);
            $bypass_out = '';
            foreach ($bypass_params as $bypass_name => $bypass_value)
                $bypass_out .= '<input type=hidden name="' . htmlspecialchars ($bypass_name, ENT_QUOTES) . '" value="' . htmlspecialchars ($bypass_value, ENT_QUOTES) . '">' . "\n";
            $mod->setOutput("HiddenParamsReset",$bypass_out);
        }
    }

    if($parent == null)
        return $mod->run();
}

function renderTagRollerForRow ($row_id)
{
    $a = rand (1, 20);
    $b = rand (1, 20);
    $sum = $a + $b;

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'TagRoller');
    $mod->setNamespace('row');
    $mod->setLock(true);
    $mod->addOutput('a', $a);
    $mod->addOutput('b', $b);
    $mod->addOutput('sum', $sum);

    printTagsPicker (null, $mod, 'Tags');
}

function renderRackCodeViewer ()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderRackCodeViewer");
    $mod->setNamespace("perms");

    $text = loadScript ('RackCode');
    $lineno = 1;
    $allLinesOut = array();
    foreach (explode ("\n", $text) as $line)
    {
        $singleLine =  array('lineno' => $lineno, 'line' => $line);
        $lineno++;
        $allLinesOut[] = $singleLine;
    }
    $mod->addOutput("allLines", $allLinesOut);
}

function renderRackCodeEditor ()
{
    $jsRawCode = <<<ENDJAVASCRIPT
                 function verify()
    {
        $.ajax(
        {
type: "POST",
url: "index.php",
data: {'module': 'ajax', 'ac': 'verifyCode', 'code': $("#RCTA").text()},
success: function (data)
            {
                arr = data.split("\\n");
                if (arr[0] == "ACK")
                {
                    $("#SaveChanges")[0].disabled = "";
                    $("#ShowMessage")[0].innerHTML = "Code verification OK, don't forget to save the code";
                    $("#ShowMessage")[0].className = "msg_success";
                }
                else
                {
                    $("#SaveChanges")[0].disabled = "disabled";
                    $("#ShowMessage")[0].innerHTML = arr[1];
                    $("#ShowMessage")[0].className = "msg_warning";
                }
            }
        });
    }

    $(document).ready(function()
    {
        $("#SaveChanges")[0].disabled = "disabled";
        $("#ShowMessage")[0].innerHTML = "";
        $("#ShowMessage")[0].className = "";

        var rackCodeMirror = CodeMirror.fromTextArea(document.getElementById("RCTA"),
        {
mode:'rackcode',
lineNumbers:true
        });
        rackCodeMirror.on("change",function(cm,cmChangeObject)
        {
            $("#RCTA").text(cm.getValue());
        });
    });
ENDJAVASCRIPT;
    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule("Payload","RenderRackCodeEditor");
    $mod->setNamespace("perms");
    $mod->setOutput("jsRawCode", $jsRawCode);

    $text = loadScript ('RackCode');
    $mod->addOutput("text", $text);
}

function renderUser ($user_id)
{
    $userinfo = spotEntity ('user', $user_id);

    $summary = array();
    $summary['Account name'] = $userinfo['user_name'];
    $summary['Real name'] = $userinfo['user_realname'];
    $summary['tags'] = '';

    $tplm = TemplateManager::getInstance();
    renderEntitySummary ($userinfo, 'summary', $summary, $tplm->getMainModule(), 'Payload');
}

function renderMyPasswordEditor ()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderMyPasswordEditor");
    $mod->setNamespace("myaccount");
}

function renderConfigEditor ()
{
    global $pageno;
    $per_user = ($pageno == 'myaccount');
    global $configCache;

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderConfigEditor");
    $mod->setNamespace("myaccount");

    $i = 0;
    $allConfigsPerUser = array();
    foreach ($per_user ? $configCache : loadConfigCache() as $v)
    {
        if ($v['is_hidden'] != 'no')
            continue;
        if ($per_user && $v['is_userdefined'] != 'yes')
            continue;

        $singleConfig = array(
                            'RenderConfig' => renderConfigVarName ($v),
                            'HtmlSpecialChars' => htmlspecialchars ($v['varvalue'], ENT_QUOTES),
                            'VarName' => $v['varname'],
                            'Index' => $i
                        );
        if ($per_user && $v['is_altered'] == 'yes')
            $singleConfig['OpLink'] = getOpLink (array('op'=>'reset', 'varname'=>$v['varname']), 'reset');
        else
            $singleConfig['OpLink'] = '';
        $i++;
        $allConfigsPerUser[] = $singleConfig;
    }
    $mod->addOutput('LoopArray', $allConfigsPerUser);
    $mod->addOutput("Index", $i);
}

function renderMyAccount ()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderMyAccount");
    $mod->setNamespace("myaccount", true);

    global $remote_username, $remote_displayname, $expl_tags, $impl_tags, $auto_tags;

    $mod->setOutput('UserName', $remote_username);
    $mod->setOutput('DisplayName', $remote_displayname);
    $mod->setOutput('Serialize1', getExplicitTagsOnly ($expl_tags));
    $mod->setOutput('Serialize2', $impl_tags);
    $mod->setOutput('Serialize3', $auto_tags);
}

function renderMyQuickLinks ()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderMyQuickLinks");
    $mod->setNamespace("myaccount");

    global $indexlayout, $page;
    $active_items = explode (',', getConfigVar ('QUICK_LINK_PAGES'));
    $rowarray = array();
    foreach ($indexlayout as $row)
        foreach ($row as $ypageno)
        {
            $checked_state = in_array ($ypageno, $active_items) ? 'checked' : '';
            $rowarray[] = array('PageName' => getPageName ($ypageno), 'PageNo' => $ypageno, 'CheckedState' =>  $checked_state );
        }
    $mod->setOutput('LoopArray', $rowarray);
}

function renderFileSummary ($file, $parent = null, $placeholder = 'FileSummary')
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateModule('FileSummaryDownloadLink',true);
    $mod->addOutput('Id', $file['id']);

    $summary = array();
    $summary['Type'] = niftyString ($file['type']);
    $summary['Size'] =
        (
            isolatedPermission ('file', 'download', $file) ?
            (
                $mod->run()
            ) : ''
        ) . formatFileSize ($file['size']);
    $summary['Created'] = $file['ctime'];
    $summary['Modified'] = $file['mtime'];
    $summary['Accessed'] = $file['atime'];
    $summary['tags'] = '';
    if (strlen ($file['comment']))
    {
        $mod = $tplm->generateModule('FileSummaryComment',true);
        $mod->addOutput('Comment', string_insert_hrefs (htmlspecialchars ($file['comment'])));
        $summary['Comment'] = $mod->run();
    }
    renderEntitySummary ($file, 'summary', $summary, $parent, $placeholder);
}

function renderFileLinks ($links, $parent, $placeholder)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule($placeholder, 'FileLinks', $parent);
    $mod->setNamespace('file');
    $mod->addOutput("Count", count ($links));

    foreach ($links as $link)
    {
        $cell = spotEntity ($link['entity_type'], $link['entity_id']);
        switch ($link['entity_type'])
        {
        case 'user':
        case 'ipv4net':
        case 'rack':
        case 'ipvs':
        case 'ipv4vs':
        case 'ipv4rspool':
        case 'object':
            $tplm->generateSubmodule('Links','StdTableRowClass',$mod,true, array('Content'=>renderCell ($cell), 'Class' => 'tdleft'));
            break;
        default:
            $tplm->generateSubmodule('Links', 'FileLinksObjLink', $mod, true, array('Name'=>formatRealmName ($link['entity_type']),'Link'=>mkCellA ($cell)));
            break;
        }
    }
}

function renderFilePreview ($pcode)
{
    startPortlet ('preview');
    echo $pcode;
    finishPortlet();
}

// File-related functions
function renderFile ($file_id)
{
    $file = spotEntity ('file', $file_id);

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'File');
    $mod->setNamespace('file');

    $mod->addOutput('Name', htmlspecialchars ($file['name']));
    callHook ('renderFileSummary', $file, $mod, 'FileSummary');

    $links = getFileLinks ($file_id);
    if (count ($links))
        callHook ('renderFileLinks', $links, $mod, 'FileLinks');

    if (isolatedPermission ('file', 'download', $file)) //and '' != ($pcode = getFilePreviewCode ($file)))
    {
        getFilePreviewCode ($file,$mod,'FilePreview');
    }
}

function renderFileReuploader ()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'FileReuploader');
    $mod->setNamespace('file');
}

function renderFileDownloader ($file_id)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'FileDownloader');
    $mod->setNamespace('file');
    $mod->addOutput('Id', $file_id);
}

function renderFileProperties ($file_id)
{
    $file = spotEntity ('file', $file_id);
    $tplm = TemplateManageR::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'FileProperties');
    $mod->setNamespace('file');
    
    $mod->addOutput('Type', htmlspecialchars ($file['type']));
    $mod->addOutput('Name', htmlspecialchars ($file['name']));
    $mod->addOutput('Comment', htmlspecialchars ($file['comment']));
    $mod->addOutput('Id', $file_id);
}

function renderFileBrowser ()
{
    $tplm = TemplateManageR::getInstance();
    renderCellList ('file', 'Files', TRUE, NULL, $tplm->getMainModule(), 'Payload');
}

// Like renderFileBrowser(), but with the option to delete files
function renderFileManager ()
{
    // Used for uploading a parentless file
    function printNewItemTR ($parent, $placeholder)
    {
        $tplm = TemplateManager::getInstance();
        $mod = $tplm->generateSubmodule($placeholder, 'FileManagerNew', $parent);
        $mod->setNamespace('files');

        printTagsPicker (null, $mod, 'TagsPicker');
    }

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderFileManager');
    $mod->setNamespace('files');

    printTagsPicker (null, $mod, 'TagsPicker');
    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput('NewTop',true);

    if (count ($files = listCells ('file')))
    {
        $mod->addOutput('Count', count($files));
        global $nextorder;
        $order = 'odd';
        foreach ($files as $file)
        {
            $smod = $tplm->generatePseudoSubmodule('FileList', $mod);
            $smod->setNamespace('files');
            $smod->addOutput('Count',count ($file['links']));
            $smod->addOutput('Order',$order);
            $smod->addOutput('Cell', renderCell ($file));

            // Don't load links data earlier to enable special processing.
            amplifyCell ($file);
            $smod->addOutput('Links', serializeFileLinks ($file['links'], TRUE));
            $smod->setOutput('Count',count ($file['links']));
            $smod->setOutput('Id', $file['id']);

            if (!count ($file['links']))
                $smod->addOutput('Deletable', true);
            $order = $nextorder[$order];
        }
    }
}

function renderFilesPortlet ($entity_type = NULL, $entity_id = 0, $parent = null, $placeholder = "FilesPortlet")
{
    $files = getFilesOfEntity ($entity_type, $entity_id);
    if (count ($files))
    {
        $tplm = TemplateManager::getInstance();

        if($parent == null)
            $mod = $tplm->generateModule("RenderFilesPortlet",  false);
        else
            $mod = $tplm->generateSubmodule($placeholder, "RenderFilesPortlet", $parent, false);

        $mod->setOutput("countFiles", count($files));

        $filesOutArray = array();
        foreach ($files as $file)
        {
            $fileArray = array();
            // That's a bit of overkill and ought to be justified after
            // getFilesOfEntity() returns a standard cell list.
            $file = spotEntity ('file', $file['id']);
            $fileArray["fileCell"] = renderCell ($file);
            $fileArray["comment"] = $file["comment"];
            if (isolatedPermission ('file', 'download', $file) and '' != ($pcode = getFilePreviewCode ($file)))
            {
                $pCodeMod = $tplm->generateModule('PCodeLine', true, array('pcode' => $pcode));
                $fileArray["pcode"] = $pCodeMod->run();
            }

            $filesOutArray[] = $fileArray;
        }
        $mod->setOutput("filesOutArray", $filesOutArray);

        if($parent == null)
            return $mod->run();
    }
    return "";
}

function renderFilesForEntity ($entity_id)
{
    global $pageno, $etype_by_pageno;
    // Now derive entity_type from pageno.
    $entity_type = $etype_by_pageno[$pageno];
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderFilesForEntity");

    $files = getAllUnlinkedFiles ($entity_type, $entity_id);
    if (count ($files))
    {
        $mod->setOutput("ShowFiles", true);
        $mod->setOutput("CountFiles", count ($files));
        $mod->setOutput("PrintedSelect", printSelect ($files, array ('name' => 'file_id')));
    }

    $filelist = getFilesOfEntity ($entity_type, $entity_id);
    if (count ($filelist))
    {
        $mod->setOutput("ShowFileList", true);
        $mod->setOutput("CountFileList", count ($filelist));

        $fileListOutArray = array();
        foreach ($filelist as $file_id => $file)
        {
            $fileOutArray = array();
            $fileOutArray['FileCell'] = renderCell (spotEntity ('file', $file_id));
            $fileOutArray['Comment'] = $file['comment'];

            $fileOutArray['OpLink'] = getOpLink (array('op'=>'unlinkFile', 'link_id'=>$file['link_id']), '', 'CUT', 'Unlink file');
            $fileListOutArray[] = $fileOutArray;
        }
        $mod->setOutput("FilelistsOutput", $fileListOutArray);
    }
}


// Iterate over what findRouters() returned and output some text suitable for a TD element.
function printRoutersTD ($rlist, $as_cell = 'yes', $parent = null, $placeholder = 'RoutersTD')
{
    $rtrclass = 'tdleft';
    foreach ($rlist as $rtr)
    {
        $tmp = getIPAddress ($rtr['ip_bin']);
        if ($tmp['class'] == 'trerror')
        {
            $rtrclass = 'tdleft trerror';
            break;
        }
    }
    $tplm = TemplateManager::getInstance();

    if($parent == null)
        $mod = $tplm->generateModule('IPSpaceRecordRouter');
    else
        $mod = $tplm->generateSubmodule($placeholder, 'IPSpaceRecordRouter', $parent);

    $mod->setNamespace("ipspace");
    $mod->addOutput("TRClass", $rtrclass);

    if ($as_cell == 'yes')
    {
        $mod->setOutput('printCell', true);
    }
   
    $outarr = array();
    foreach ($rlist as $rtr)
    {
        $rinfo = spotEntity ('object', $rtr['id']);
        if ($as_cell == 'yes')
            $outarr[] = array('Cell'=>renderRouterCell ($rtr['ip_bin'], $rtr['iface'], $rinfo));
        else
            $outarr[] = array('Link'=>makeHref (array ('page' => 'object', 'object_id' => $rtr['id'], 'tab' => 'default', 'hl_ip' => ip_format ($rtr['ip_bin']))), 'Name'=>$rinfo['dname']);
    }
    $mod->addOutput('RouterList', $outarr);

    if($parent == null)
        return $mod->run();
}

// Same as for routers, but produce two TD cells to lay the content out better.
function printIPNetInfoTDs ($netinfo, $decor = array(), $parent, $placeholder)
{
    $ip_ver = strlen ($netinfo['ip_bin']) == 16 ? 6 : 4;
    $formatted = $netinfo['ip'] . "/" . $netinfo['mask'];
    if ($netinfo['symbol'] == 'spacer')
    {
        $decor['indent']++;
        $netinfo['symbol'] = '';
    }

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule($placeholder, 'IPSpaceRecordInfo', $parent);
    $mod->setNamespace("ipspace");

    if (array_key_exists ('tdclass', $decor))
        $mod->addOutput('TDClass', $decor['tdclass']);
    $mod->addOutput('Indent', $decor['indent'] * 16);

    if (strlen ($netinfo['symbol']))
    {
        if (array_key_exists ('symbolurl', $decor))
            $mod->addOutput('SymbolLikn', $decor['symbolurl']);
        $mod->addOutput('Symbol', $netinfo['symbol']);
    }
    if (isset ($netinfo['id']))
        $mod->addOutput('ID', $netinfo['id']);

    $mod->addOutput('IPVersion', $ip_ver);
    $mod->addOutput('Formatted', $formatted);
    
    if (getConfigVar ('IPV4_TREE_SHOW_VLAN') == 'yes' and ! empty ($netinfo['8021q']))
    {
        $mod->addOutput('VLAN', renderNetVLAN ($netinfo));
    }
    if (array_key_exists ('tdclass', $decor))
        $mod->addOutput('TDClass', $decor['tdclass']);
    if (!isset ($netinfo['id']))
    {
        printImageHREF ('dragons', 'Here be dragons.');
        if ($decor['knight'])
        {
            $mod->addOutput('KnightLink', makeHref (array
                                                    (
                                                    'page' => "ipv${ip_ver}space",
                                                    'tab' => 'newrange',
                                                    'set-prefix' => $formatted,
                                                    )));
        }
    }
    else
    {
        $mod->addOutput('Name', $netinfo['name']);
        if (count ($netinfo['etags']))
            serializeTags ($netinfo['etags'], "index.php?page=ipv${ip_ver}space&tab=default&", $mod, 'Tags');
    }
}

function renderCell ($cell)
{
    //Use TemplateEngine
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateModule("RenderCell");

    switch ($cell['realm'])
    {
    case 'user':
        $mod->setOutput("typeUser", true);
        $mod->setOutput('UserRef', mkA ($cell['user_name'], 'user', $cell['user_id']) );

        if (strlen ($cell['user_realname']))
        {
            $mod->setOutput("hasUserRealname", true);
            $mod->setOutput("userRealname", niftyString($cell['user_realname']));
        }
        else
        {
            $mod->setOutput("hasUserRealname", false);
        }

        if (!isset ($cell['etags']))
            $cell['etags'] = getExplicitTagsOnly (loadEntityTags ('user', $cell['user_id']));
        if(count ($cell['etags']))
        {
            $smallMod = $tplm->generateSubmodule('UserTags', 'SmallElement', $mod, true);
            serializeTags($cell['etags'], '', $smallMod, 'Cont');
        }
        else
        {
            $mod->setOutput('UserTags', '&nbsp;');
        }
        break;
    case 'file':
        $mod->setOutput("typeFile", true);
        switch ($cell['type'])
        {
        case 'text/plain':
            $mod->setOutput("fileImgSpace", printImageHREF ('text file'));
            break;
        case 'image/jpeg':
        case 'image/png':
        case 'image/gif':
            $mod->setOutput("fileImgSpace", printImageHREF ('image file'));
            break;
        default:
            $mod->setOutput("fileImgSpace", printImageHREF ('empty file'));
            break;
        }
        $mod->setOutput("nameAndID", mkA ('<strong>' . niftyString ($cell['name']) . '</strong>', 'file', $cell['id']) );

        if (isset ($cell['links']) and count ($cell['links']))
            $mod->setOutput("serializedLinks", serializeFileLinks ($cell['links']));

        $mod->setOutput("fileCount", count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;');
        if (isolatedPermission ('file', 'download', $cell))
        {
            // FIXME: reuse renderFileDownloader()
            $mod->setOutput("isolatedPerm", true);
            $mod->setOutput("cellID", $cell['id']);
            $mod->setOutput("isoPermImg", printImageHREF ('download', 'Download file'));
        }
        $mod->setOutput("fileSize", formatFileSize ($cell['size']));
        break;
    case 'ipv4vs':
    case 'ipvs':
    case 'ipv4rspool':
        $mod->setOutput("typeIPV4RSPool", true);
        $mod->setOutput("ipv4ImgSpace", renderSLBEntityCell ($cell));
        break;
    case 'ipv4net':
    case 'ipv6net':
        $mod->setOutput("typeIPNet", true);
        $mod->setOutput("mkACell",mkA ("${cell['ip']}/${cell['mask']}", $cell['realm'], $cell['id']) );
        $mod->setOutput("renderdIPNetCap",getRenderedIPNetCapacity ($cell) );

        if (strlen ($cell['name']))
        {
            $mod->setOutput("cellName",true );
            $mod->setOutput("niftyCellName", niftyString ($cell['name']) );
        }

        // render VLAN
        $mod->setOutput("renderedVLan",renderNetVLAN ($cell) );
        $mod->setOutput("etags", count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;');
        break;
    case 'rack':
        $mod->setOutput("typeRack", true);

        $thumbwidth = getRackImageWidth();
        $thumbheight = getRackImageHeight ($cell['height']);

        $mod->setOutput("thumbWidth", $thumbwidth);
        $mod->setOutput("thumbHeight", $thumbheight);
        $mod->setOutput("cellHeight", $cell['height']);
        $mod->setOutput("cellID", $cell['id']);
        $mod->setOutput("cellName", $cell['name']);
        $mod->setOutput("cellComment", niftyString ($cell['comment']));
        $mod->setOutput("mkACell",mkA ('<strong>' . niftyString ($cell['name']) . '</strong>', 'rack', $cell['id']) );
        $mod->setOutput("etags", count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;');
        break;
    case 'location':
        $mod->setOutput("typeLocation", true);
        $mod->setOutput("cellName", $cell['name']);
        $mod->setOutput("cellID", $cell['id']);
        $mod->setOutput("cellComment", niftyString ($cell['comment']));
        $mod->setOutput("mkACell",mkA ('<strong>' . niftyString ($cell['name']) . '</strong>', 'location', $cell['id']) );
        $mod->setOutput("etags", count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;');
        break;
    case 'object':
        $mod->setOutput("typeObject", true);
        $mod->setOutput("cellDName", $cell['dname']);
        $mod->setOutput("cellID", $cell['id']);
        $mod->setOutput("mkACell",mkA ('<strong>' . niftyString ($cell['dname']) . '</strong>', 'object', $cell['id']) );
        $mod->setOutput("etags", count ($cell['etags']) ? ("<small>" . serializeTags ($cell['etags']) . "</small>") : '&nbsp;');
        break;
    default:
        throw new InvalidArgException ('realm', $cell['realm']);
    }

    return $mod->run();
}

function renderRouterCell ($ip_bin, $ifname, $cell, $parent = null, $placeholder = '')
{
    $dottedquad = ip_format ($ip_bin);

    $tplm = TemplateManager::getInstance();

    if ($parent === null)
        $mod = $tplm->generateModule('RouterCell');
    else
        $mod = $tplm->generateSubmodule($placeholder, 'RouterCell', $parent);
    $mod->setNamespace('ipnetwork');

    $mod->addOutput('IP', $dottedquad);

    if (strlen( $ifname))
        $mod->addOutput('ifname', '@' . $ifname);

    $mod->addOutput('Name', $cell['dname']);
    $mod->addOutput('Id', $cell['id']);

    if (count($cell['etags']))
        serializeTags ($cell['etags'], '', $mod, 'Tags');

    if ($parent === null)
    {
        return $mod->run();
    }
}

// Return HTML code necessary to show a preview of the file give. Return an empty string,
// if a preview cannot be shown
function getFilePreviewCode ($file, $parent, $mod)
{
    $tplm = TemplateManager::getInstance();
    $ret = '';
    
    switch ($file['type'])
    {
    // "These types will be automatically detected if your build of PHP supports them: JPEG, PNG, GIF, WBMP, and GD2."
    case 'image/jpeg':
    case 'image/png':
    case 'image/gif':
        $file = getFile ($file['id']);
        $image = imagecreatefromstring ($file['contents']);
        $width = imagesx ($image);
        $height = imagesy ($image);

        if ($width < getConfigVar ('PREVIEW_IMAGE_MAXPXS') and $height < getConfigVar ('PREVIEW_IMAGE_MAXPXS'))
            $resampled = FALSE;
        else
        {
            $ratio = getConfigVar ('PREVIEW_IMAGE_MAXPXS') / max ($width, $height);
            $width = $width * $ratio;
            $height = $height * $ratio;
            $resampled = TRUE;
        }

        $mod = $tplm->generateSubmodule($placeholder, 'FilePreviewImage', $parent);
        $mod->addOutput('Height', $height);
        $mod->addOutput('Width', $width);
        $mod->addOutput('Id', $file['id']);
        $mod->addOutput('Resampled', $resampled);
        break;
    case 'text/plain':
        if ($file['size'] < getConfigVar ('PREVIEW_TEXT_MAXCHARS'))
        {
            $file = getFile ($file['id']);

            $mod = $tplm->generateSubmodule($placeholder, 'FilePreviewText', $parent);
            $mod->setNamespace('file');
            $mod->addOutput('Rows', getConfigVar ('PREVIEW_TEXT_ROWS'));
            $mod->addOutput('Cols', getConfigVar ('PREVIEW_TEXT_COLS'));
            $mod->addOutput('Content', $file['contents']);
        }
        break;
    default:
        break;
    }
    return $ret;
}

function renderTextEditor ($file_id)
{
    global $CodePressMap;
    $fullInfo = getFile ($file_id);
    preg_match('/.+\.([^.]*)$/', $fullInfo['name'], $matches);
# get file extension
    if (isset ($matches[1]) && isset ($CodePressMap[$matches[1]]))
        $syntax = $CodePressMap[$matches[1]];
    else
        $syntax = "text";

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'TextEditor');
    $mod->addOutput('MTime', $fullInfo['mtime']);
    $mod->addOutput('Content', htmlspecialchars ($fullInfo['contents']));
    $mod->addOutput('Syntax', $syntax);
}

function showPathAndSearch ($pageno, $tabno, $tpl = false)
{
    // This function returns array of page numbers leading to the target page
    // plus page number of target page itself. The first element is the target
    // page number and the last element is the index page number.
    function getPath ($targetno)
    {
        $self = __FUNCTION__;
        global $page;
        $path = array();
        $page_name = preg_replace ('/:.*/', '', $targetno);
        // Recursion breaks at first parentless page.
        if ($page_name == 'ipaddress')
        {
            // case ipaddress is a universal v4/v6 page, it has two parents and requires special handling
            $ip_bin = ip_parse ($_REQUEST['ip']);
            $parent = (strlen ($ip_bin) == 16 ? 'ipv6net' : 'ipv4net');
            $path = $self ($parent);
            $path[] = $targetno;
        }
        elseif (!isset ($page[$page_name]['parent']))
        $path = array ($targetno);
        else
        {
            $path = $self ($page[$page_name]['parent']);
            $path[] = $targetno;
        }
        return $path;
    }
    global $page, $tab;
    // Path.

    $path = getPath ($pageno);
    $items = array();

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->getMainModule();

    foreach (array_reverse ($path) as $no)
    {
        if (preg_match ('/(.*):(.*)/', $no, $m) && isset ($tab[$m[1]][$m[2]]))
            $title = array
                     (
                         'name' => $tab[$m[1]][$m[2]],
                         'params' => array('page' => $m[1], 'tab' => $m[2]),
                     );
        elseif (isset ($page[$no]['title']))
        $title = array
                 (
                     'name' => $page[$no]['title'],
                     'params' => array()
                 );
        else
            $title = callHook ('dynamic_title_decoder', $no);
        $item = $tplm->generateModule("PathLink", true);
        $item->setOutput('Delimiter', ':');

        if (! isset ($title['params']['page']))
            $title['params']['page'] = $no;
        if (! isset ($title['params']['tab']))
            $title['params']['tab'] = 'default';
        $is_first = TRUE;
        $anchor_tail = '';
        $params = '';
        foreach ($title['params'] as $param_name => $param_value)
        {
            if ($param_name == '#')
            {
                $anchor_tail = '#' . $param_value;
                continue;
            }
            $params .= ($is_first ? '' : '&') . "${param_name}=${param_value}";
            $is_first = FALSE;
        }
        $item->addOutput("Params", $params);
        $item->addOutput("AnchorTail", $anchor_tail);
        $item->addOutput("Name", $title['name']);
        $items[] = $item;

        // insert location bread crumbs
        switch ($no)
        {
        case 'object':
            $object = spotEntity ('object', $title['params']['object_id']);
            if ($object['rack_id'])
            {
                $rack = spotEntity ('rack', $object['rack_id']);
                $items[] = ' : ' . mkA ($rack['name'], 'rack', $rack['id']);
                $items[] = ' : ' . mkA ($rack['row_name'], 'row', $rack['row_id']);
                if ($rack['location_id'])
                {
                    $trail = ' : ' . getLocationTrail ($rack['location_id']);
                    if (! empty ($trail))
                        $items[] = $trail;
                }
            }
            break;
        case 'row':
            $trail = ' : ' . getLocationTrail ($title['params']['location_id']);
            if (! empty ($trail))
                $items[] = $trail;
            break;
        case 'location':
            // overwrite the bread crumb for current location with whole path
            $itemsc[count ($items)-1] = ' : ' . getLocationTrail ($title['params']['location_id']);
        }
    }
    //Hide the first :
    $items[count($items)-1]->setOutput('Delimiter', '');
    $mod->addOutput("Path", array_reverse($items));

    // Search form.
    // This input will be the first, if we don't add ports or addresses.
    $mod->addOutput("PageNo", $pageno);
    $mod->addOutput("TabNo", $tabno);
    $mod->addOutput("SearchValue", (isset ($_REQUEST['q']) ? htmlspecialchars ($_REQUEST['q'], ENT_QUOTES) : '')) ;
}

function getTitle ($pageno)
{
    global $page;
    if (isset ($page[$pageno]['title']))
        return $page[$pageno]['title'];
    $tmp = callHook ('dynamic_title_decoder', $pageno);
    return $tmp['name'];
}

function showTabs ($pageno, $tabno)
{
    global $tab, $page, $trigger;
    if (!isset ($tab[$pageno]['default']))
        return;

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->getMainModule();

    foreach ($tab[$pageno] as $tabidx => $tabtitle)
    {
        // Hide forbidden tabs.
        if (!permitted ($pageno, $tabidx))
        {
            continue;
        }
        // Dynamic tabs should only be shown in certain cases (trigger exists and returns true).
        if (!isset ($trigger[$pageno][$tabidx]))
            $tabclass = 'TabInactive';
        elseif (strlen ($tabclass2 = call_user_func ($trigger[$pageno][$tabidx])))
        {
            switch ($tabclass2)
            {
            case 'std':
                $tabclass = "TabInactive";
                break;
            case 'attn':
                $tabclass = "TabAttention";
                break;
            default:
                $tabclass = "TabInactive";
            }
        }
        else
        {
            continue;
        }

        if ($tabidx == $tabno)
            $tabclass = 'TabActive'; // override any class for an active selection
        $args = array();
        fillBypassValues ($pageno, $args);
        $extraargs = "";
        foreach ($args as $param_name => $param_value)
            $extraargs.= "&" . urlencode ($param_name) . '=' . urlencode ($param_value);
        $params = array("Page"=>$pageno,
                        "Tab"=>$tabidx,
                        "Args"=>$extraargs,
                        "Title"=>$tabtitle);
        $tplm->generateSubmodule("Tabs", $tabclass, $mod, true, $params);
    }
}

// Arg is path page number, which can be different from the primary page number,
// for example title for 'ipv4net' can be requested to build navigation path for
// both IPv4 network and IPv4 address. Another such page number is 'row', which
// fires for both row and its racks. Use pageno for decision in such cases.
function dynamic_title_decoder ($path_position)
{
    global $sic, $page_by_realm;
    static $net_id;
    try
    {
        switch ($path_position)
        {
        case 'index':
            return array
                   (
                       'name' => '/' . getConfigVar ('enterprise'),
                       'params' => array()
                   );
        case 'chapter':
            $chapter_no = assertUIntArg ('chapter_no');
            $chapters = getChapterList();
            $chapter_name = isset ($chapters[$chapter_no]) ? $chapters[$chapter_no]['name'] : 'N/A';
            return array
                   (
                       'name' => "Chapter '${chapter_name}'",
                       'params' => array ('chapter_no' => $chapter_no)
                   );
        case 'user':
            $userinfo = spotEntity ('user', assertUIntArg ('user_id'));
            return array
                   (
                       'name' => "Local user '" . $userinfo['user_name'] . "'",
                       'params' => array ('user_id' => $userinfo['user_id'])
                   );
        case 'ipv4rspool':
            $pool_info = spotEntity ('ipv4rspool', assertUIntArg ('pool_id'));
            return array
                   (
                       'name' => !strlen ($pool_info['name']) ? 'ANONYMOUS' : $pool_info['name'],
                       'params' => array ('pool_id' => $pool_info['id'])
                   );
        case 'ipv4vs':
            $vs_info = spotEntity ('ipv4vs', assertUIntArg ('vs_id'));
            return array
                   (
                       'name' => $vs_info['dname'],
                       'params' => array ('vs_id' => $vs_info['id'])
                   );
        case 'ipvs':
            $vs_info = spotEntity ('ipvs', assertUIntArg ('vs_id'));
            return array
                   (
                       'name' => $vs_info['name'],
                       'params' => array ('vs_id' => $vs_info['id'])
                   );
        case 'object':
            $object = spotEntity ('object', assertUIntArg ('object_id'));
            return array
                   (
                       'name' => $object['dname'],
                       'params' => array ('object_id' => $object['id'])
                   );
        case 'location':
            $location = spotEntity ('location', assertUIntArg ('location_id'));
            return array
                   (
                       'name' => $location['name'],
                       'params' => array ('location_id' => $location['id'])
                   );
        case 'row':
            global $pageno;
            switch ($pageno)
            {
            case 'rack':
                $rack = spotEntity ('rack', assertUIntArg ('rack_id'));
                return array
                       (
                           'name' => $rack['row_name'],
                           'params' => array ('row_id' => $rack['row_id'], 'location_id' => $rack['location_id'])
                       );
            case 'row':
                $row_info = getRowInfo (assertUIntArg ('row_id'));
                return array
                       (
                           'name' => $row_info['name'],
                           'params' => array ('row_id' => $row_info['id'], 'location_id' => $row_info['location_id'])
                       );
            default:
                break;
            }
        case 'rack':
            $rack_info = spotEntity ('rack', assertUIntArg ('rack_id'));
            return array
                   (
                       'name' => $rack_info['name'],
                       'params' => array ('rack_id' => $rack_info['id'])
                   );
        case 'search':
            if (isset ($_REQUEST['q']))
                return array
                       (
                           'name' => "search results for '${_REQUEST['q']}'",
                           'params' => array ('q' => $_REQUEST['q'])
                       );
            else
                return array
                       (
                           'name' => 'search results',
                           'params' => array()
                       );
        case 'file':
            $file = spotEntity ('file', assertUIntArg ('file_id'));
            return array
                   (
                       'name' => niftyString ($file['name'], 30, FALSE),
                       'params' => array ('file_id' => $_REQUEST['file_id'])
                   );
        case 'ipaddress':
            $address = getIPAddress (ip_parse ($_REQUEST['ip']));
            return array
                   (
                       'name' => niftyString ($address['ip'] . ($address['name'] != '' ? ' (' . $address['name'] . ')' : ''), 50, FALSE),
                       'params' => array ('ip' => $address['ip'])
                   );
        case 'ipv4net':
        case 'ipv6net':
            global $pageno;
            switch ($pageno)
            {
            case 'ipaddress':
                $net = spotNetworkByIP (ip_parse ($_REQUEST['ip']));
                $ret = array
                       (
                           'name' => $net['ip'] . '/' . $net['mask'],
                           'params' => array
                           (
                               'id' => $net['id'],
                               'page' => $net['realm'], // 'ipv4net', 'ipv6net'
                               'hl_ip' => $_REQUEST['ip'],
                           )
                       );
                return ($ret);
            default:
                $net = spotEntity ($path_position, assertUIntArg ('id'));
                return array
                       (
                           'name' => $net['ip'] . '/' . $net['mask'],
                           'params' => array ('id' => $net['id'])
                       );
            }
            break;
        case 'ipv4space':
        case 'ipv6space':
            global $pageno;
            switch ($pageno)
            {
            case 'ipaddress':
                $net_id = getIPAddressNetworkId (ip_parse ($_REQUEST['ip']));
                break;
            case 'ipv4net':
            case 'ipv6net':
                $net_id = $_REQUEST['id'];
                break;
            default:
                $net_id = NULL;
            }
            $params = array();
            if (isset ($net_id))
                $params = array ('eid' => $net_id, 'hl_net' => 1, 'clear-cf' => '');
            unset ($net_id);
            $ip_ver = preg_replace ('/[^\d]*/', '', $path_position);
            return array
                   (
                       'name' => "IPv$ip_ver space",
                       'params' => $params,
                   );
        case 'vlandomain':
            global $pageno;
            switch ($pageno)
            {
            case 'vlandomain':
                $vdom_id = $_REQUEST['vdom_id'];
                break;
            case 'vlan':
                list ($vdom_id, $dummy) = decodeVLANCK ($_REQUEST['vlan_ck']);
                break;
            default:
                break;
            }
            $vdlist = getVLANDomainOptions();
            if (!array_key_exists ($vdom_id, $vdlist))
                throw new EntityNotFoundException ('VLAN domain', $vdom_id);
            return array
                   (
                       'name' => niftyString ("domain '" . $vdlist[$vdom_id] . "'", 20, FALSE),
                       'params' => array ('vdom_id' => $vdom_id)
                   );
        case 'vlan':
            return array
                   (
                       'name' => formatVLANAsPlainText (getVLANInfo ($sic['vlan_ck'])),
                       'params' => array ('vlan_ck' => $sic['vlan_ck'])
                   );
        case 'vst':
            $vst = spotEntity ('vst', $sic['vst_id']);
            return array
                   (
                       'name' => niftyString ("template '" . $vst['description'] . "'", 50, FALSE),
                       'params' => array ('vst_id' => $sic['vst_id'])
                   );
        case 'dqueue':
            global $dqtitle;
            return array
                   (
                       'name' => 'queue "' . $dqtitle[$sic['dqcode']] . '"',
                       'params' => array ('qcode' => $sic['dqcode'])
                   );
        default:
            break;
        }

        // default behaviour is throwing an exception
        throw new RackTablesError ('dynamic_title decoding error', RackTablesError::INTERNAL);
    } // end-of try block
    catch (RackTablesError $e)
    {
        return array
               (
                   'name' => __FUNCTION__ . '() failure',
                   'params' => array()
               );
    }
}

function renderTwoColumnCompatTableViewer ($compat, $left, $right)
{
    global $nextorder;
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderIIFOIFCompat");
    $mod->setNamespace("portifcompat");

    $last_lkey = NULL;
    $order = 'odd';
    $allCompatsOut = array();

    $mod->setOutput('LeftHeader', $left['header']);
    $mod->setOutput('RightHeader', $right['header']);

    foreach ($compat as $item)
    {
        if ($last_lkey !== $item[$left['key']])
        {
            $order = $nextorder[$order];
            $last_lkey = $item[$left['key']];
        }

        $singleRecord = array('Order' => $order,
                              'ItemLeftKey' => $item[$left['key']],
                              'LeftString' => niftyString ($item[$left['value']], $left['width']),
                              'ItemRightKey' => $item[$right['key']],
                              'RightString' => niftyString ($item[$right['value']], $right['width']));
        
        $allCompatsOut[] = $singleRecord;
    }
    $mod->addOutput("AllCompats", $allCompatsOut);
}

function renderIIFOIFCompat()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderIIFOIFCompat");
    $mod->setNamespace("portifcompat");

    renderTwoColumnCompatTableViewer
    (
        getPortInterfaceCompat(),
        array
        (
            'header' => 'Inner interface',
            'key' => 'iif_id',
            'value' => 'iif_name',
            'width' => 16,
        ),
        array
        (
            'header' => 'Outer interface',
            'key' => 'oif_id',
            'value' => 'oif_name',
            'width' => 48,
        ), $mod, 'TableViewer'
    );
}

function renderTwoColumnCompatTableEditor ($compat, $left, $right, $parent = null, $placeholder = 'TwoColumnCompatTableEditor')
{
    function printNewitemTR ($lkey, $loptions, $rkey, $roptions, $parent = null, $placeholder = 'NewitemTR')
    {
        $tplm = TemplateManager::getInstance();
        if($parent == null)
            $mod = $tplm->generateModule("TwoColumnCompatTableEditor_PrintNew");
        else
            $mod = $tplm->generateSubmodule($placeholder,"TwoColumnCompatTableEditor_PrintNew", $parent);
        $mod->setNamespace("portifcompat");

        printSelect ($loptions, array ('name' => $lkey, 'tabindex' => 100), NULL, $mod, "lOptions");
        printSelect ($roptions, array ('name' => $rkey, 'tabindex' => 110), NULL, $mod, "rOptions");
        
        if($parent == null)
            return $mod->run();
    }

    global $nextorder;
    $last_lkey = NULL;
    $order = 'even';

    $tplm = TemplateManager::getInstance();

    if($parent == null)
        $mod = $tplm->generateSubmodule("Payload","TwoColumnCompatTableEditor");
    else
        $mod = $tplm->generateSubmodule($placeholder,"TwoColumnCompatTableEditor", $parent);
    $mod->setNamespace("portifcompat");

    $mod->setOutput('LeftHeader', $left['header']);
    $mod->setOutput('RightHeader', $right['header']);
    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        printNewitemTR ($left['key'], $left['options'], $right['key'], $right['options'], $mod, "AddNewTop");

    $allCompatsOut = array();
    foreach ($compat as $item)
    {
        if ($last_lkey !== $item[$left['key']])
        {
            $order = $nextorder[$order];
            $last_lkey = $item[$left['key']];
        }
        $singleCompat = array('Order' => $order,
                              'OpLink' => getOpLink (array ('op' => 'del', $left['key'] => $item[$left['key']], $right['key'] => $item[$right['key']]), '', 'delete', 'remove pair'),
                              'LeftValue' => niftyString ($item[$left['value']], $left['width']),
                              'RightValue' => niftyString ($item[$right['value']], $right['width']));
        
        $allCompatsOut[] = $singleCompat;
    }
    $mod->setOutput('AllCompats', $allCompatsOut);
    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
        printNewitemTR ($left['key'], $left['options'], $right['key'], $right['options'], $mod, "AddNewBottom");
}

function renderIIFOIFCompatEditor()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderIIFOIFCompatEditor");
    $mod->setNamespace("portifcompat");

    $iif = getPortIIFOptions();
    global $nextorder, $wdm_packs;
    $order = 'odd';
    $allWDM_PacksOut = array();
    foreach ($wdm_packs as $codename => $packinfo)
    {
        $singlePack = array('PackInfo' => $packinfo['title']);
        $singlePack['IifCont'] = '';
        foreach ($packinfo['iif_ids'] as $iif_id)
        {
            $order = $nextorder[$order];

            $iif_id_mod = $tplm->generateModule("RenderIIFOIFCompatEditor_Iif_id");
            $iif_id_mod->setNamespace("portifcompat");

            $iif_id_mod->addOutput('order', $order);
            $iif_id_mod->addOutput('iif_iif_id', $iif[$iif_id]);
            $iif_id_mod->addOutput('codename', $codename);
            $iif_id_mod->addOutput('iif_id', $iif_id);

            $singlePack['IifCont'] .= $iif_id_mod->run();
        }
    }

    renderTwoColumnCompatTableEditor
    (
        getPortInterfaceCompat(),
        array
        (
            'header' => 'inner interface',
            'key' => 'iif_id',
            'value' => 'iif_name',
            'width' => 16,
            'options' => getPortIIFOptions(),
        ),
        array
        (
            'header' => 'outer interface',
            'key' => 'oif_id',
            'value' => 'oif_name',
            'width' => 48,
            'options' => getPortOIFOptions()
        ),
        $mod,
        'TwoColumnCompatTableEditor'
    );
}

function render8021QOrderForm ($some_id)
{
    function printNewItemTR ()
    {
        $all_vswitches = getVLANSwitches();
        global $pageno;
        $hintcodes = array ('prev_vdid' => 'DEFAULT_VDOM_ID', 'prev_vstid' => 'DEFAULT_VST_ID', 'prev_objid' => NULL);
        $focus = array();
        foreach ($hintcodes as $hint_code => $option_name)
            if (array_key_exists ($hint_code, $_REQUEST))
            {
                assertUIntArg ($hint_code);
                $focus[$hint_code] = $_REQUEST[$hint_code];
            }
            elseif ($option_name != NULL)
                $focus[$hint_code] = getConfigVar ($option_name);
            else
                $focus[$hint_code] = NULL;

        $tplm = TemplateManager::getInstance();
        $mod = $tplm->generateModule("Render8021QOrderForm_PrintNew");
        $mod->setNamespace("vlandomain");

        if ($pageno != 'object')
        {
            $mod->setOutput("isNoObject", true);
            // hide any object that is already in the table
            $options = array();
            foreach (getNarrowObjectList ('VLANSWITCH_LISTSRC') as $object_id => $object_dname)
                if (!in_array ($object_id, $all_vswitches))
                {
                    $ctx = getContext();
                    spreadContext (spotEntity ('object', $object_id));
                    $decision = permitted (NULL, NULL, 'del');
                    restoreContext ($ctx);
                    if ($decision)
                        $options[$object_id] = $object_dname;
                }
            $mod->addOutput("selected", getSelect ($options, array ('name' => 'object_id', 'tabindex' => 101, 'size' => getConfigVar ('MAXSELSIZE')), $focus['prev_objid']));
        }
        if ($pageno != 'vlandomain')
        {
            $mod->setOutput("isNoVLANDomain", true);
            $mod->addOutput("getVLDSelect", getSelect (getVLANDomainOptions(), array ('name' => 'vdom_id', 'tabindex' => 102, 'size' => getConfigVar ('MAXSELSIZE')), $focus['prev_vdid']));
        }
        if ($pageno != 'vst')
        {
            $mod->setOutput("isNoVST", true);

            $options = array();
            foreach (listCells ('vst') as $nominee)
            {
                $ctx = getContext();
                spreadContext ($nominee);
                $decision = permitted (NULL, NULL, 'add');
                restoreContext ($ctx);
                if ($decision)
                    $options[$nominee['id']] = niftyString ($nominee['description'], 30, FALSE);
            }
            $mod->addOutput("getVSTSelect", getSelect ($options, array ('name' => 'vst_id', 'tabindex' => 103, 'size' => getConfigVar ('MAXSELSIZE')), $focus['prev_vstid']));
        }
        return $mod->run();
    }

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","Render8021QOrderForm");
    $mod->setNamespace("vlandomain");

    global $pageno;
    $minuslines = array(); // indexed by object_id, which is unique
    switch ($pageno)
    {
    case 'object':
        if (NULL !== $vswitch = getVLANSwitchInfo ($some_id))
            $minuslines[$some_id] = array
                                    (
                                        'vdom_id' => $vswitch['domain_id'],
                                        'vst_id' => $vswitch['template_id'],
                                    );
        break;
    case 'vlandomain':
        $vlandomain = getVLANDomain ($some_id);
        foreach ($vlandomain['switchlist'] as $vswitch)
            $minuslines[$vswitch['object_id']] = array
                                                 (
                                                         'vdom_id' => $some_id,
                                                         'vst_id' => $vswitch['template_id'],
                                                 );
        break;
    case 'vst':
        $vst = spotEntity ('vst', $some_id);
        amplifyCell ($vst);
        foreach ($vst['switches'] as $vswitch)
            $minuslines[$vswitch['object_id']] = array
                                                 (
                                                         'vdom_id' => $vswitch['domain_id'],
                                                         'vst_id' => $some_id,
                                                 );
        break;
    default:
        throw new InvalidArgException ('pageno', $pageno, 'this function only works for a fixed set of values');
    }

    if ($pageno != 'object')
    {
        $mod->setOutput("isNoObject", true);
    }
    if ($pageno != 'vlandomain')
    {
        $mod->setOutput("isNoVLANDomain", true);
    }
    if ($pageno != 'vst')
    {
        $mod->setOutput("isNoVST", true);
    }
    // object_id is a UNIQUE in VLANSwitch table, so there is no sense
    // in a "plus" row on the form, when there is already a "minus" one
    if
    (
        getConfigVar ('ADDNEW_AT_TOP') == 'yes' and
        ($pageno != 'object' or !count ($minuslines))
    )
        $mod->addOutput("AddNewTop", printNewItemTR());

    $vdomlist = getVLANDomainOptions();
    $vstlist = getVSTOptions();

    foreach ($minuslines as $item_object_id => $item)
    {
        $ctx = getContext();

        if ($pageno != 'object')
            spreadContext (spotEntity ('object', $item_object_id));
        if ($pageno != 'vst')
            spreadContext (spotEntity ('vst', $item['vst_id']));
        if (! permitted (NULL, NULL, 'del'))
            $cutblock = getImageHREF ('Cut gray', 'permission denied');
        else
        {
            $args = array
                    (
                        'op' => 'del',
                        'object_id' => $item_object_id,
# Extra args below are only necessary for redirect and permission
# check to work, actual deletion uses object_id only.
                        'vdom_id' => $item['vdom_id'],
                        'vst_id' => $item['vst_id'],
                    );
            $cutblock = getOpLink ($args, '', 'Cut', 'unbind');
        }
        restoreContext ($ctx);
        $singleMinusLine = array('cutblock' => $cutblock);

        if ($pageno != 'object')
        {
            $singleMinusLine['isNoObject'] = true;
            $object = spotEntity ('object', $item_object_id);
            $singleMinusLine['objMkA'] = mkA ($object['dname'], 'object', $object['id']);
        }
        if ($pageno != 'vlandomain')
        {
            $singleMinusLine['isNoVLANDomain'] = true;
            $singleMinusLine['vlanDMkA'] = mkA ($vdomlist[$item['vdom_id']], 'vlandomain', $item['vdom_id']);
        }
        if ($pageno != 'vst')
        {
            $singleMinusLine['isNoVST'] = true;
            $singleMinusLine['vstMkA'] = mkA ($vstlist[$item['vst_id']], 'vst', $item['vst_id']);
        }

        $singleLine = $tplm->generatePseudoSubmodule('AllMinusLines', $mod, $singleMinusLine);
        $singleLine->setNamespace('vlandomain');
    }

    if
    (
        getConfigVar ('ADDNEW_AT_TOP') != 'yes' and
        ($pageno != 'object' or !count ($minuslines))
    )
        $mod->addOutput("AddNewBottom", printNewItemTR());
}

function render8021QStatus ()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","Render8021QStatus");
    $mod->setNamespace("8021q");

    global $dqtitle;
    if (!count ($vdlist = getVLANDomainStats()))
        $mod->setOutput("areVLANDomains", true);
    else
    {
        $mod->setOutput("countVDList", count ($vdlist));
        $stats = array();
        $columns = array ('vlanc', 'switchc', 'ipv4netc', 'portc');
        foreach ($columns as $cname)
            $stats[$cname] = 0;

        $allVDListOut = array();
        foreach ($vdlist as $vdom_id => $dominfo)
        {
            $singleDomInfo = array();
            foreach ($columns as $cname)
                $stats[$cname] += $dominfo[$cname];
            $singleDomInfo['mkA'] = mkA (niftyString ($dominfo['description']), 'vlandomain', $vdom_id);
            $singleDomInfo['columnOut'] = '';
            foreach ($columns as $cname)
            {
                $columnMod = $tplm->generateModule("StdTableCell", true, array('cont' => $dominfo[$cname]));
                $singleDomInfo['columnOut'] .= $columnMod->run();
            }
            $allVDListOut[] = $singleDomInfo;
        }
        $mod->setOutput("vdListOut", $allVDListOut);

        if (count ($vdlist) > 1)
        {
            $mod->setOutput("isVDList", true);
            $allColumsOut = array();
            foreach ($columns as $cname)
                $allColumsOut[] = array('cName' => $stats[$cname]);
        }
        $mod->setOutput("TotalColumnOut", $allColumsOut);
    }

    if (!count ($vstlist = listCells ('vst')))
        $mod->setOutput("areVSTCells", true);
    else
    {
        $mod->setOutput("countVSTList", count ($vstlist));
        $vstlistOut = array();
        foreach ($vstlist as $vst_id => $vst_info)
        {
            $singleVST_ID = array('mkA' =>  mkA (niftyString ($vst_info['description']), 'vst', $vst_id), 'areTags' => count ($vst_info['etags']));
            if (count ($vst_info['etags']))
            {
                $etagsMod = $tplm->generateModule('ETagsLine', true, array('cont' => serializeTags ($vst_info['etags'])));
                $singleVST_ID['serializedTags'] = $etagsMod->run();
            }
            $singleVST_ID['rulec'] = $vst_info['rulec'];
            $singleVST_ID['switchc'] = $vst_info['switchc'];
            $vstlistOut[] = $singleVST_ID;
        }
        $mod->setOutput("vstListOut", $vstlistOut);
    }

    $total = 0;

    $allDeployQueuesOut = array();
    foreach (get8021QDeployQueues() as $qcode => $qitems)
    {
        $allDeployQueuesOut[] = array('mkA' => mkA ($dqtitle[$qcode], 'dqueue', $qcode), 'countItems' => count ($qitems));
        $total += count ($qitems);
    }

    $mod->setOutput("allDeployQueues", $allDeployQueuesOut);
    $mod->setOutput("total", $total);
}

function renderVLANDomainListEditor ()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderVLANDomainListEditor");
    $mod->setNamespace("8021q");

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput("isAddNew", true);

    $allDomainStatsOut = array();
    foreach (getVLANDomainStats() as $vdom_id => $dominfo)
    {
        $singleDomainStat = array('formIntro' => printOpFormIntro ('upd', array ('vdom_id' => $vdom_id)));
        if ($dominfo['switchc'] or $dominfo['vlanc'] > 1)
        {
            $singleDomainStat['imageNoDestroy'] = printImageHREF ('nodestroy', 'domain used elsewhere');
        }
        else
        {
            $singleDomainStat['linkDestroy'] = getOpLink (array ('op' => 'del', 'vdom_id' => $vdom_id), '', 'destroy', 'delete domain');
        }

        $singleDomainStat['niftyStr'] = niftyString ($dominfo['description'], 0);
        $singleDomainStat['imageUpdate'] = printImageHREF ('save', 'update description', TRUE);
        $allDomainStatsOut[] = $singleDomainStat;
    }

    $mod->addOutput("allDomainStats", $allDomainStatsOut);

    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
        $mod->addOutput("isAddNew", false);
}

function renderVLANDomain ($vdom_id)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderVLANDomain");
    $mod->setNamespace("vlandomain");

    global $nextorder;
    $mydomain = getVLANDomain ($vdom_id);
    $mod->addOutput("niftyStr", niftyString ($mydomain['description']));

    if (!count ($mydomain['switchlist']))
        $mod->addOutput("areDomains", false);
    else
    {
        $mod->addOutput("countDomains", count ($mydomain['switchlist']));
        $order = 'odd';
        $vstlist = getVSTOptions();
        global $dqtitle;
        $allDomainSwitchOut = array();
        foreach ($mydomain['switchlist'] as $switchinfo)
        {
            $singleDomain = array('order' => $order, 'renderedCell' => renderCell (spotEntity ('object', $switchinfo['object_id'])));
            $singleDomain['vstlist'] = $vstlist[$switchinfo['template_id']];
            $qcode = detectVLANSwitchQueue (getVLANSwitchInfo ($switchinfo['object_id']));
            $singleDomain['imageHREF'] = printImageHREF ("DQUEUE ${qcode}", $dqtitle[$qcode]);
            
            $order = $nextorder[$order];
            $allDomainSwitchOut[] = $singleDomain;
        }
        $mod->addOutput("allDomainSwitch", $allDomainSwitchOut);
    }

    if (!count ($myvlans = getDomainVLANs ($vdom_id)))
        $mod->addOutput("areVLANDomains", false);
    else
    {
        $mod->addOutput("countMyVLANs", count ($myvlans));
        $order = 'odd';
        global $vtdecoder;
        $allMyVLANsOut = array();
        foreach ($myvlans as $vlan_id => $vlan_info)
        {
            $singleMyVLANs = array('order' => $order, 'mkA' => mkA ($vlan_id, 'vlan', "${vdom_id}-${vlan_id}"));
            $singleMyVLANs['vtdecoder'] = $vtdecoder[$vlan_info['vlan_type']];
            $singleMyVLANs['infoNetc']  = ($vlan_info['netc'] ? $vlan_info['netc'] : '&nbsp;');
            $singleMyVLANs['infoPortc'] = ($vlan_info['portc'] ? $vlan_info['portc'] : '&nbsp;');
            $singleMyVLANs['infoDescr'] = $vlan_info['vlan_descr'];
            $allMyVLANsOut[] = $singleMyVLANs;
            $order = $nextorder[$order];
        }
        $mod->addOutput("allMyVLANs", $allMyVLANsOut);
    }
}

function renderVLANDomainVLANList ($vdom_id)
{
    function printNewItemTR ($parent, $placeholder)
    {
        global $vtoptions;

        $tplm = TemplateManager::getInstance();
        $mod = $tplm->generateSubmodule($placeholder, "RenderVLANDomainVLANList_printNew", $parent, false, array('Vtoptions' => $vtoptions,
                                        "PrintSel" => printSelect ($vtoptions, array ('name' => 'vlan_type', 'tabindex' => 102), 'ondemand')));
        $mod->setNamespace("vlandomain");
    }

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderVLANDomainVLANList");
    $mod->setNamespace("vlandomain");

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        printNewItemTR($mod, "AddNewTop");

    global $vtoptions;
    $allDomainVLANsOut = array();
    foreach (getDomainVLANs ($vdom_id) as $vlan_id => $vlan_info)
    {
        $singleDomainVLAN = array('opIntro' => printOpFormIntro ('upd', array ('vlan_id' => $vlan_id)));
        if ($vlan_info['portc'] or $vlan_id == VLAN_DFL_ID)
            $singleDomainVLAN['portc'] = printImageHREF ('nodestroy', $vlan_info['portc'] . ' ports configured');
        else
            $singleDomainVLAN['portc'] = getOpLink (array ('op' => 'del', 'vlan_id' => $vlan_id), '', 'destroy', 'delete VLAN');
        $singleDomainVLAN['vlan_id'] = $vlan_id;
        $singleDomainVLAN['printSel'] = printSelect ($vtoptions, array ('name' => 'vlan_type'), $vlan_info['vlan_type']);
        $singleDomainVLAN['htmlSpecialChr'] = htmlspecialchars ($vlan_info['vlan_descr']);
        $singleDomainVLAN['saveImg'] = printImageHREF ('save', 'update description', TRUE);
        
        $allDomainVLANsOut[] = $singleDomainVLAN;
    }
    $mod->addOutput("allDomainVLANs", $allDomainVLANsOut);

    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
        printNewItemTR($mod, "AddNewBottom");
}

function get8021QPortTrClass ($port, $domain_vlans, $desired_mode = NULL)
{
    if (isset ($desired_mode) && $desired_mode != $port['mode'])
        return 'trwarning';
    if (count (array_diff ($port['allowed'], array_keys ($domain_vlans))))
        return 'trwarning';
    return 'trbusy';
}

// Show a list of 802.1Q-eligible ports in any way, but when one of
// them is selected as current, also display a form for its setup.
function renderObject8021QPorts ($object_id)
{
    global $pageno, $tabno, $sic;
    $vswitch = getVLANSwitchInfo ($object_id);
    $vdom = getVLANDomain ($vswitch['domain_id']);
    $req_port_name = array_fetch ($sic, 'port_name', '');
    $desired_config = apply8021QOrder ($vswitch, getStored8021QConfig ($object_id, 'desired'));
    $cached_config = getStored8021QConfig ($object_id, 'cached');
    $desired_config = sortPortList	($desired_config);
    $uplinks = filter8021QChangeRequests ($vdom['vlanlist'], $desired_config, produceUplinkPorts ($vdom['vlanlist'], $desired_config, $vswitch['object_id']));

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderObject8021QPorts');

    $mod->setNamespace('object');

    // port list
    $req_port_name == '' ? '<th width="25%">new&nbsp;config</th></tr>' : '<th>(zooming)</th></tr>';
    if ($req_port_name != '')
    {
        $mod->addOutput('IsReqPortName', true);
    }
 
    $mod->addOutput("Vswitch", $vswitch['mutex_rev']);

    $object = spotEntity ('object', $object_id);
    amplifyCell ($object);
    $sockets = array();
    if (isset ($_REQUEST['hl_port_id']))
    {
        assertUIntArg ('hl_port_id');
        $hl_port_id = intval ($_REQUEST['hl_port_id']);
        $hl_port_name = NULL;
        addAutoScrollScript ("port-$hl_port_id", $mod, 'JSScripts');
    }
    foreach ($object['ports'] as $port)
        if (mb_strlen ($port['name']) and array_key_exists ($port['name'], $desired_config))
        {
            if (isset ($hl_port_id) and $hl_port_id == $port['id'])
                $hl_port_name = $port['name'];
            $socket = array ('interface' => formatPortIIFOIF ($port));
            if ($port['remote_object_id'])
                $socket['link'] = formatLoggedSpan ($port['last_log'], formatLinkedPort ($port));
            elseif (strlen ($port['reservation_comment']))
            $socket['link'] = formatLoggedSpan ($port['last_truelog'], 'Rsv:', 'strong underline') . ' ' .
                              formatLoggedSpan ($port['last_log'], $port['reservation_comment']);
            else
                $socket['link'] = '&nbsp;';
            $sockets[$port['name']][] = $socket;
        }
    unset ($object);
    $nports = 0; // count only access ports
    switchportInfoJS ($object_id, $mod, 'JSScripts'); // load JS code to make portnames interactive

    foreach ($desired_config as $port_name => $port)
    {

        $text_left = formatVLANPackDiff ($cached_config[$port_name], $port);
        // decide on row class
        switch ($port['vst_role'])
        {
        case 'none':
            if ($port['mode'] == 'none')
                continue 2; // early miss
            $text_right = '&nbsp;';
            $trclass = 'trerror'; // stuck ghost port
            break;
        case 'downlink':
            $text_right = '(downlink)';
            $trclass = get8021QPortTrClass ($port, $vdom['vlanlist'], 'trunk');
            break;
        case 'uplink':
            $text_right = '(uplink)';
            $trclass = same8021QConfigs ($port, $uplinks[$port_name]) ? 'trbusy' : 'trwarning';
            break;
        case 'trunk':
            $text_right = getTrunkPortCursorCode ($object_id, $port_name, $req_port_name);
            $trclass = get8021QPortTrClass ($port, $vdom['vlanlist'], 'trunk');
            break;
        case 'access':
            $text_right = getAccessPortControlCode ($req_port_name, $vdom, $port_name, $port, $nports);
            $trclass = get8021QPortTrClass ($port, $vdom['vlanlist'], 'access');
            break;
        case 'anymode':
            $text_right = getAccessPortControlCode ($req_port_name, $vdom, $port_name, $port, $nports);
            $text_right .= '&nbsp;';
            $text_right .= getTrunkPortCursorCode ($object_id, $port_name, $req_port_name);
            $trclass = get8021QPortTrClass ($port, $vdom['vlanlist'], NULL);
            break;
        default:
            throw new InvalidArgException ('vst_role', $port['vst_role']);
        }

        $rowMod = $tplm->generatePseudoSubmodule('PortRows', $mod);
        $rowMod->addOutput('TextRight', $text_right);
        $rowMod->addOutput('TextLeft', $text_left);
        $rowMod->addOutput('TextClass', $trclass);
        $rowMod->addOutput('PortName', $port_name);

        if (!checkPortRole ($vswitch, $port_name, $port))
            $rowMod->addOutput('HasErrors', true);

        if (!array_key_exists ($port_name, $sockets))
        {
            $rowMod->addOutput('NoSocketColumns', true);
        }
        else
        {
            $td_extra = count ($sockets[$port_name]) > 1 ? (' rowspan=' . count ($sockets[$port_name])) : '';
            $rowMod->addOutput("TdExtra", $td_extra);
            
            foreach ($sockets[$port_name][0] as $tmp)
                $tplm->generateSubmodule('SocketColumns', 'StdTableCell', $rowMod, true, array('cont' => $tmp));
        }

        if (isset ($hl_port_name) and $hl_port_name == $port_name)
        {
            $rowMod->addOutput('HasPortName', true);
            $rowMod->addOutput('PortId', $hl_port_id);
        }

        if (!array_key_exists ($port_name, $sockets))
            continue;
        $first_socket = TRUE;
        foreach ($sockets[$port_name] as $socket)
            if ($first_socket)
                $first_socket = FALSE;
            else
            {
                $socketRowMod = $tplm->generateSubmodule('SocketRows', 'StdTableRowClass', $rowMod, true, array('Class' => $trclass));
                foreach ($socket as $tmp)
                    $tplm->generateSubmodule('Cont', 'StdTableCell', $socketRowMod, true, array('cont' => $tmp));
            }
    }
    if ($req_port_name == '' and $nports)
    {
        $mod->addOutput("IsToSave", true);
        $mod->addOutput("Nports", $nports);

    }
    if (permitted (NULL, NULL, NULL, array (array ('tag' => '$op_recalc8021Q'))))
        $mod->addOutput("RecalcPerm", true);

    // configuration of currently selected port, if any
    if (!array_key_exists ($req_port_name, $desired_config))
    {
        $mod->addOutput('HasPortOpt', true);

        $port_options = array();
        foreach ($desired_config as $pn => $portinfo)
            if (editable8021QPort ($portinfo))
                $port_options[$pn] = same8021QConfigs ($desired_config[$pn], $cached_config[$pn]) ?
                                     $pn : "${pn} (*)";
            if (count ($port_options) < 2)
                $mod->addOutput('SinglePort', true);
            else
            {
                $mod->addOutput("PortOpt", $port_options);
                $mod->addOutput("MaxSelSize", getConfigVar ('MAXSELSIZE'));
            }
    }
    else
        renderTrunkPortControls
        (
            $vswitch,
            $vdom,
            $req_port_name,
            $desired_config[$req_port_name],
            $mod,
            'TrunkPortlets'
        );
}

// Return the text to place into control column of VLAN ports list
// and modify $nports, when this text was a series of INPUTs.
function getAccessPortControlCode ($req_port_name, $vdom, $port_name, $port, &$nports)
{
    static $permissions_cache = array();
    // don't render a form for access ports, when a trunk port is zoomed
    if ($req_port_name != '')
        return '&nbsp;';
    if
    (
        array_key_exists ($port['native'], $vdom['vlanlist']) and
        $vdom['vlanlist'][$port['native']]['vlan_type'] == 'alien'
    )
        return formatVLANAsLabel ($vdom['vlanlist'][$port['native']]);

    static $vlanpermissions = array();
    if (!array_key_exists ($port['native'], $vlanpermissions))
    {
        $vlanpermissions[$port['native']] = array();
        foreach (array_keys ($vdom['vlanlist']) as $to)
        {
            $from_key = 'from_' . $port['native'];
            $to_key = 'to_' . $to;
            if (isset ($permissions_cache[$from_key]))
                $allowed_from = $permissions_cache[$from_key];
            else
                $allowed_from = $permissions_cache[$from_key] = permitted (NULL, NULL, 'save8021QConfig', array (array ('tag' => '$fromvlan_' . $port['native']), array ('tag' => '$vlan_' . $port['native'])));
            if ($allowed_from)
            {
                if (isset ($permissions_cache[$to_key]))
                    $allowed_to = $permissions_cache[$to_key];
                else
                    $allowed_to = $permissions_cache[$to_key] = permitted (NULL, NULL, 'save8021QConfig', array (array ('tag' => '$tovlan_' . $to), array ('tag' => '$vlan_' . $to)));

                if ($allowed_to)
                    $vlanpermissions[$port['native']][] = $to;
            }
        }
    }
    $ret = "<input type=hidden name=pn_${nports} value=${port_name}>";
    $ret .= "<input type=hidden name=pm_${nports} value=access>";
    $options = array();
    // Offer only options that are listed in domain and fit into VST.
    // Never offer immune VLANs regardless of VST filter for this port.
    // Also exclude current VLAN from the options, unless current port
    // mode is "trunk" (in this case it should be possible to set VST-
    // approved mode without changing native VLAN ID).
    foreach ($vdom['vlanlist'] as $vlan_id => $vlan_info)
        if
        (
            ($vlan_id != $port['native'] or $port['mode'] == 'trunk') and
            $vlan_info['vlan_type'] != 'alien' and
            in_array ($vlan_id, $vlanpermissions[$port['native']]) and
            matchVLANFilter ($vlan_id, $port['wrt_vlans'])
        )
            $options[$vlan_id] = formatVLANAsOption ($vlan_info);
    ksort ($options);
    $options['same'] = '-- no change --';
    $ret .= getSelect ($options, array ('name' => "pnv_${nports}"), 'same');
    $nports++;
    return $ret;
}

function getTrunkPortCursorCode ($object_id, $port_name, $req_port_name)
{
    global $pageno, $tabno;
    $linkparams = array
                  (
                      'page' => $pageno,
                      'tab' => $tabno,
                      'object_id' => $object_id,
                  );
    if ($port_name == $req_port_name)
    {
        $imagename = 'Zooming';
        $imagetext = 'zoom out';
    }
    else
    {
        $imagename = 'Zoom';
        $imagetext = 'zoom in';
        $linkparams['port_name'] = $port_name;
    }
    return "<a href='" . makeHref ($linkparams) . "'>"  .
           getImageHREF ($imagename, $imagetext) . '</a>';
}

function renderTrunkPortControls ($vswitch, $vdom, $port_name, $vlanport, $parent = null, $placeholder = 'Payload')
{
    $tplm = TemplateManager::getInstance();

    if($parent==null)
        $mod = $tplm->generateSubmodule($placeholder,'RenderTrunkPortControls');
    else
        $mod = $tplm->generateSubmodule($placeholder, 'RenderTrunkPortControls', $parent);

    $mod->setNamespace('object');

    if($parent==null)
        return $mod->run();
    if (!count ($vdom['vlanlist']))
    {
        $mod->addOutput('NoList', true);
        return;
    }
    $formextra = array
                 (
                     'mutex_rev' => $vswitch['mutex_rev'],
                     'nports' => 1,
                     'pn_0' => $port_name,
                     'pm_0' => 'trunk',
                     'form_mode' => 'save',
                 );
    $mod->addOutput('Save8021QConfig', printOpFormIntro ('save8021QConfig', $formextra));

    // Present all VLANs of the domain and all currently configured VLANs
    // (regardless if these sets intersect or not).
    $allowed_options = array();
    foreach ($vdom['vlanlist'] as $vlan_id => $vlan_info)
        $allowed_options[$vlan_id] = array
                                     (
                                         'vlan_type' => $vlan_info['vlan_type'],
                                         'text' => formatVLANAsLabel ($vlan_info),
                                     );
    foreach ($vlanport['allowed'] as $vlan_id)
        if (!array_key_exists ($vlan_id, $allowed_options))
            $allowed_options[$vlan_id] = array
                                         (
                                             'vlan_type' => 'none',
                                             'text' => "unlisted VLAN ${vlan_id}",
                                         );
    ksort ($allowed_options);
    $allAllowedOptionsOut = array();
    foreach ($allowed_options as $vlan_id => $option)
    {
        $selected = '';
        $class = 'tagbox';
        if (in_array ($vlan_id, $vlanport['allowed']))
        {
            $selected = ' checked';
            $class .= ' selected';
        }

        // A real relation to an alien VLANs is shown for a
        // particular port, but it cannot be changed by user.
        if ($option['vlan_type'] == 'alien')
            $selected .= ' disabled';
        $allAllowedOptionsOut[] = array('Class' => $class,
                                        'Vlan_Id' => $vlan_id,
                                        'Selected' => $selected,
                                        'OptionTxt' => $option['text']);
    }
    $mod->setOutput('AllowedOptions', $allAllowedOptionsOut);

    if (count ($vlanport['allowed']))
    {
        $mod->addOutput('Vlan_Port_Allowed', true);

        $native_options = array (0 => array ('vlan_type' => 'none', 'text' => '-- NONE --'));
        foreach ($vlanport['allowed'] as $vlan_id)
            $native_options[$vlan_id] = array_key_exists ($vlan_id, $vdom['vlanlist']) ? array
                                        (
                                            'vlan_type' => $vdom['vlanlist'][$vlan_id]['vlan_type'],
                                            'text' => formatVLANAsLabel ($vdom['vlanlist'][$vlan_id]),
                                        ) : array
                                        (
                                            'vlan_type' => 'none',
                                            'text' => "unlisted VLAN ${vlan_id}",
                                        );

        $allNativeOptOut = array();
        foreach ($native_options as $vlan_id => $option)
        {
            $selected = '';
            $class = 'tagbox';
            if ($vlan_id == $vlanport['native'])
            {
                $selected = ' checked';
                $class .= ' selected';
            }
            // When one or more alien VLANs are present on port's list of allowed VLANs,
            // they are shown among radio options, but disabled, so that the user cannot
            // break traffic of these VLANs. In addition to that, when port's native VLAN
            // is set to one of these alien VLANs, the whole group of radio buttons is
            // disabled. These measures make it harder for the system to break a VLAN
            // that is explicitly protected from it.
            if
            (
                $native_options[$vlanport['native']]['vlan_type'] == 'alien' or
                $option['vlan_type'] == 'alien'
            )
                $selected .= ' disabled';
            $allNativeOptOut[] = array('Class' => $class,
                                       'Vlan_Id' => $vlan_id,
                                       'Selected' => $selected,
                                       'OptionTxt' => $option['text']);
        }
        $mod->addOutput('NativeOpts', $allNativeOptOut);
    }
}

function renderVLANInfo ($vlan_ck)
{
    global $vtoptions, $nextorder;
    $vlan = getVLANInfo ($vlan_ck);

    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule("Payload","RenderVLANInfo");
    $mod->setNamespace("vlan");

    $mod->addOutput("formatVlanTxt", formatVLANAsRichText ($vlan));

    $mod->addOutput("niftyStr_domain_descr", niftyString ($vlan['domain_descr'], 0));
    $mod->addOutput("vlan_id", $vlan['vlan_id'] );
    if (strlen ($vlan['vlan_descr']))
    {
        $mod->addOutput("isVlan_Descr", true);
        $mod->addOutput("niftyStr_vlan_descr", niftyString ($vlan['vlan_descr'], 0));
    }
    $mod->addOutput("vtoptions", $vtoptions[$vlan['vlan_prop']]);
    $others = getSearchResultByField
              (
                  'VLANDescription',
                  array ('domain_id'),
                  'vlan_id',
                  $vlan['vlan_id'],
                  'domain_id',
                  1
              );
    $allOthersOut = array();
    foreach ($others as $other)
        if ($other['domain_id'] != $vlan['domain_id'])
            $allOthersOut[] = array('vlanHyperlinks' => formatVLANAsHyperlink (getVLANInfo ("${other['domain_id']}-${vlan['vlan_id']}")) );
    $mod->addOutput("allOthers", $allOthersOut);

    if (0 == count ($vlan['ipv4nets']) + count ($vlan['ipv6nets']))
    {
        $mod->addOutput("noNetworks", true);
    }
    else
    {
        $mod->addOutput("overallCount", (count ($vlan['ipv4nets']) + count ($vlan['ipv6nets'])));
        $order = 'odd';
        $allNetsOut = array();
        foreach (array ('ipv4net', 'ipv6net') as $nettype)
            foreach ($vlan[$nettype . 's'] as $netid)
            {
                $net = spotEntity ($nettype, $netid);
                $allNetsOut[] = array('renderedCell' => renderCell ($net),
                                      'niftyStr' => (mb_strlen ($net['comment']) ? niftyString ($net['comment']) : '&nbsp;'));
                $order = $nextorder[$order];
            }
        $mod->addOutput("allNets", $allNetsOut);
    }

    $confports = getVLANConfiguredPorts ($vlan_ck);

    // get non-switch device list
    $foreign_devices = array();
    foreach ($confports as $switch_id => $portlist)
    {
        $object = spotEntity ('object', $switch_id);
        foreach ($portlist as $port_name)
            if ($portinfo = getPortinfoByName ($object, $port_name))
                if ($portinfo['linked'] && ! isset ($confports[$portinfo['remote_object_id']]))
                    $foreign_devices[$portinfo['remote_object_id']][] = $portinfo;
    }
    if (! empty ($foreign_devices))
    {
        $mod->addOutput("nonSwitchDev", true);

        $order = 'odd';
        $allForgDevOut = array();
        foreach ($foreign_devices as $cell_id => $ports)
        {
            $cell = spotEntity ('object', $cell_id);
            $singleDev = array('order' => $order, 'rendCell' => renderCell ($cell), 'ports' => '');
            foreach ($ports as $portinfo)
            {
                $singPort = $tplm->generateModule("StdListElem", true, array('cont' =>
                                                  formatPortLink ($portinfo['remote_object_id'], NULL, $portinfo['remote_id'], $portinfo['remote_name']) . ' &mdash; ' . formatPort ($portinfo)))	;
                $singleDev['ports'] .= $singPort->run();
            }
            $allForgDev[] = $singleDev;
            $order = $nextorder[$order];

        }
        $mod->addOutput("allForgDev", $allForgDevOut);
    }

    if (!count ($confports))
    {
        $mod->addOutput("noPorts", true);
    }
    else
    {
        $mod->addOutput("countPorts", count ($confports));

        global $nextorder;
        $order = 'odd';
        $allConfportsOut = array();
        foreach ($confports as $switch_id => $portlist)
        {
            usort_portlist ($portlist);
            $singlePort = array('order' => $order);
            $object = spotEntity ('object', $switch_id);
            
            $singlePort['rendCell'] = renderCell ($object);
            $singlePort['portlist'] = '';
            foreach ($portlist as $port_name)
            {
                $singlePortlistElem = $tplm->generateModule("StdListElem", true);
                if ($portinfo = getPortinfoByName ($object, $port_name))
                    $singlePortlistElem->setOutput('cont', formatPortLink ($object['id'], NULL, $portinfo['id'], $portinfo['name']));
                else
                    $singlePortlistElem->setOutput('cont', $port_name);
                $singlePort['portlist'] .= $singlePortlistElem->run();
            }
            $allConfportsOut[] = $singlePort;
            $order = $nextorder[$order];
        }
        $mod->addOutput("allConfports", $allConfportsOut);
    }
}

function renderVLANIPLinks ($some_id)
{
    global $pageno, $tabno;

    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule("Payload","RenderVLANIPLinks");
    $mod->setNamespace("vlan");


    // fill $minuslines, $plusoptions, $select_name
    $minuslines = array();
    $plusoptions = array();
    $extra = array();
    switch ($pageno)
    {
    case 'vlan':
        $mod->addOutput("IsVLAN", true);

        $ip_ver = $tabno == 'ipv6' ? 'ipv6' : 'ipv4';
        $vlan = getVLANInfo ($some_id);
        $domainclass = array ($vlan['domain_id'] => 'trbusy');
        foreach ($vlan[$ip_ver . "nets"] as $net_id)
            $minuslines[] = array
                            (
                                'net_id' => $net_id,
                                'domain_id' => $vlan['domain_id'],
                                'vlan_id' => $vlan['vlan_id'],
                            );
        // Any VLAN can link to any network that isn't yet linked to current domain.
        // get free IP nets
        $netlist_func  = $ip_ver == 'ipv6' ? 'getVLANIPv6Options' : 'getVLANIPv4Options';
        foreach ($netlist_func ($vlan['domain_id']) as $net_id)
        {
            $netinfo = spotEntity ($ip_ver . 'net', $net_id);
            if (considerConfiguredConstraint ($netinfo, 'VLANIPV4NET_LISTSRC'))
                $plusoptions['other'][$net_id] =
                    $netinfo['ip'] . '/' . $netinfo['mask'] . ' ' . $netinfo['name'];
        }
        $select_name = 'id';
        $extra = array ('vlan_ck' => $vlan['domain_id'] . '-' . $vlan['vlan_id']);
        break;
    case 'ipv4net':
    case 'ipv6net':
        $mod->addOutput("IsIpv6Net", true);

        $netinfo = spotEntity ($pageno, $some_id);
        $reuse_domain = considerConfiguredConstraint ($netinfo, '8021Q_MULTILINK_LISTSRC');
# For each of the domains linked to the network produce class name based on
# number of VLANs linked and the current "reuse" setting.
        $domainclass = array();
        foreach (array_count_values (reduceSubarraysToColumn ($netinfo['8021q'], 'domain_id')) as $domain_id => $vlan_count)
            $domainclass[$domain_id] = $vlan_count == 1 ? 'trbusy' : ($reuse_domain ? 'trwarning' : 'trerror');
# Depending on the setting and the currently linked VLANs reduce the list of new
# options by either particular VLANs or whole domains.
        $except = array();
        foreach ($netinfo['8021q'] as $item)
        {
            if ($reuse_domain)
                $except[$item['domain_id']][] = $item['vlan_id'];
            elseif (! array_key_exists ($item['domain_id'], $except))
            $except[$item['domain_id']] = range (VLAN_MIN_ID, VLAN_MAX_ID);
            $minuslines[] = array
                            (
                                'net_id' => $netinfo['id'],
                                'domain_id' => $item['domain_id'],
                                'vlan_id' => $item['vlan_id'],
                            );
        }
        $plusoptions = getAllVLANOptions ($except);
        $select_name = 'vlan_ck';
        $extra = array ('id' => $netinfo['id']);
        break;
    }
    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes' )
    {
        $mod->addOutput("AddTop", true);

        if (count ($plusoptions))
        {
            $mod->addOutput("OptionTree", getOptionTree ($select_name, $plusoptions, array ('tabindex' => 101)));
        }
        $mod->addOutput("extra", $extra);
    }

    $allMinuslinesOut = array();
    foreach ($minuslines as $item)
    {
        $singleMinusLine = array('domainclass' => $domainclass[$item['domain_id']]);
        switch ($pageno)
        {
        case 'vlan':
            $singleMinusLine['RenderedCell'] = renderCell (spotEntity ($ip_ver . 'net', $item['net_id']));
            break;
        case 'ipv4net':
        case 'ipv6net':
            $vlaninfo = getVLANInfo ($item['domain_id'] . '-' . $item['vlan_id']);
            $singleMinusLine['VlanRichTxt'] = formatVLANAsRichText ($vlaninfo);
            break;
        }
        $singleMinusLine['OpLink'] = getOpLink (array ('id' => $some_id, 'op' => 'unbind', 'id' => $item['net_id'],
                                                'vlan_ck' => $item['domain_id'] . '-' . $item['vlan_id']), '', 'Cut', 'unbind');
        $allMinuslinesOut[] = $singleMinusLine;
    }
    $mod->addOutput("AllMinusLines", $allMinuslinesOut);

    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
    {
        $mod->addOutput("AddTop", false);
        if(!count($plusoptions))
            $mod->addOutput("OptionTree", getOptionTree ($select_name, $plusoptions, array ('tabindex' => 101)));

        $mod->addOutput("extra", $extra);
    }
}

function renderObject8021QSync ($object_id)
{
    $vswitch = getVLANSwitchInfo ($object_id);
    $object = spotEntity ('object', $object_id);
    amplifyCell ($object);
    $maxdecisions = 0;
    $D = getStored8021QConfig ($vswitch['object_id'], 'desired');
    $C = getStored8021QConfig ($vswitch['object_id'], 'cached');
    try
    {
        $R = getRunning8021QConfig ($object_id);
        $plan = apply8021QOrder ($vswitch, get8021QSyncOptions ($vswitch, $D, $C, $R['portdata']));
        foreach ($plan as $port)
            if
            (
                $port['status'] == 'delete_conflict' or
                $port['status'] == 'merge_conflict' or
                $port['status'] == 'add_conflict' or
                $port['status'] == 'martian_conflict'
            )
                $maxdecisions++;
    }
    catch (RTGatewayError $re)
    {
        $error = $re->getMessage();
        $R = NULL;
    }

    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule("Payload","RenderObject8021QSync");
    $mod->setNamespace("object", TRUE);

    renderObject8021QSyncSchedule ($object, $vswitch, $maxdecisions, 'Sync_Schedule', $mod);
    if (considerConfiguredConstraint ($object, '8021Q_EXTSYNC_LISTSRC'))
    {
        $mod->setOutput('Considerconfiguratedconstraint', TRUE);
        renderObject8021QSyncPorts ($object, $D, 'Sync_Ports', $mod);
    }
    if ($R !== NULL)
    {
        $mod->setOutput('R_Set', TRUE);
        renderObject8021QSyncPreview ($object, $vswitch, $plan, $C, $R, $maxdecisions, 'Sync_Preview', $mod);
    }
    else
    {
        $mod->setOutput('Error', $error);
    }
}

function renderObject8021QSyncSchedule ($object, $vswitch, $maxdecisions, $placeholder, $parent)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule($placeholder , "RenderObject8021QSyncSchedule", $parent);

    // FIXME: sort rows newest event last
    $rows = array();
    if (! considerConfiguredConstraint ($object, 'SYNC_802Q_LISTSRC'))
        $rows['auto sync'] = '<span class="trerror">disabled by operator</span>';
    $rows['last local change'] = datetimestrFromTimestamp ($vswitch['last_change']) . ' (' . formatAge ($vswitch['last_change']) . ')';
    $rows['device out of sync'] = $vswitch['out_of_sync'];
    if ($vswitch['out_of_sync'] == 'no')
    {
        $push_duration = $vswitch['last_push_finished'] - $vswitch['last_push_started'];
        $rows['last sync session with device'] = datetimestrFromTimestamp ($vswitch['last_push_started']) . ' (' . formatAge ($vswitch['last_push_started']) .
                ', ' . ($push_duration < 0 ?  'interrupted' : "lasted ${push_duration}s") . ')';
    }
    if ($vswitch['last_errno'])
        $rows['failed'] = datetimestrFromTimestamp ($vswitch['last_error_ts']) . ' (' . strerror8021Q ($vswitch['last_errno']) . ')';

    if (NULL !== $new_rows = callHook ('alter8021qSyncSummaryItems', $rows))
        $rows = $new_rows;

    $rowgen = array();
    foreach ($rows as $th => $td)
    {
        $rowgen[] = array('Th' => $th, 'Td' => $td);
    }
    $mod->setOutput('Looparray', $rowgen);

    if ($maxdecisions)
    {
        $mod->setOutput('Maxdecision', TRUE);
    }
}

function renderObject8021QSyncPreview ($object, $vswitch, $plan, $C, $R, $maxdecisions, $placeholder, $parent)
{
    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule($placeholder , "RenderObject8021QSyncPreview", $parent);
    $mod->setNamespace('object');
    if (isset ($_REQUEST['hl_port_id']))
    {
        $mod->setOutput('Port_Id', $hl_port_id);
        assertUIntArg ('hl_port_id');
        $hl_port_id = intval ($_REQUEST['hl_port_id']);
        $hl_port_name = NULL;
        $mod->setOutput('Port_Id', $hl_port_id);
       
        foreach ($object['ports'] as $port)
            if (mb_strlen ($port['name']) && $port['id'] == $hl_port_id)
            {
                $hl_port_name = $port['name'];
                break;
            }
        unset ($object);
    }

    switchportInfoJS ($vswitch['object_id'], $mod); // load JS code to make portnames interactive
    // initialize one of three popups: we've got data already
    $mod->addOutput('Port_Config', addslashes (json_encode (formatPortConfigHints ($vswitch['object_id'], $R))));
    
    if ($maxdecisions)
    {
        $mod->setOutput('Maxdecisions', TRUE);
    
    }
    
    if ($maxdecisions)
    {
        $position = array();
        foreach (array ('left', 'asis', 'right') as $pos)
        {
            $position[] = array('Position' => $pos, 'Maxdecision' => $maxdecisions);
        }
        $mod->addOutput('Looparray2', $positions);
    }
    $rownum = 0;
    $plan = sortPortList ($plan);
    $domvlans = array_keys (getDomainVLANList ($vswitch['domain_id']));
    $default_port = array
                    (
                        'mode' => 'access',
                        'allowed' => array (VLAN_DFL_ID),
                        'native' => VLAN_DFL_ID,
                    );
    foreach ($plan as $port_name => $item)
    {
        $smod = $tplm->generatePseudoSubmodule('Loop', $mod);
        $trclass = $left_extra = $right_extra = $left_text = $right_text = '';
        $radio_attrs = array();
        switch ($item['status'])
        {
        case 'ok_to_delete':
            $left_text = serializeVLANPack ($item['left']);
            $right_text = 'none';
            $left_extra = ' trnull';
            $right_extra = ' trok'; // no confirmation is necessary
            break;
        case 'delete_conflict':
            $trclass = 'trbusy';
            $left_extra = ' trerror'; // can be fixed on request
            $right_extra = ' trnull';
            $left_text = formatVLANPackDiff ($item['lastseen'], $item['left']);
            $right_text = '&nbsp;';
            $radio_attrs = array ('left' => '', 'asis' => ' checked', 'right' => ' disabled');
            // dummy setting to suppress warnings in resolve8021QConflicts()
            $item['right'] = $default_port;
            break;
        case 'add_conflict':
            $trclass = 'trbusy';
            $right_extra = ' trerror';
            $left_text = '&nbsp;';
            $right_text = serializeVLANPack ($item['right']);
            break;
        case 'ok_to_add':
            $trclass = 'trbusy';
            $right_extra = ' trok';
            $left_text = '&nbsp;';
            $right_text = serializeVLANPack ($item['right']);
            break;
        case 'ok_to_merge':
            $trclass = 'trbusy';
            $left_extra = ' trok';
            $right_extra = ' trok';
        // fall through
        case 'in_sync':
            $trclass = 'trbusy';
            $left_text = $right_text = serializeVLANPack ($item['both']);
            break;
        case 'ok_to_pull':
            // at least one of the sides is not in the default state
            $trclass = 'trbusy';
            $right_extra = ' trok';
            $left_text = serializeVLANPack ($item['left']);
            $right_text = serializeVLANPack ($item['right']);
            break;
        case 'ok_to_push':
            $trclass = ' trbusy';
            $left_extra = ' trok';
            $left_text = formatVLANPackDiff ($C[$port_name], $item['left']);
            $right_text = serializeVLANPack ($item['right']);
            break;
        case 'merge_conflict':
            $trclass = 'trbusy';
            $left_extra = ' trerror';
            $right_extra = ' trerror';
            $left_text = formatVLANPackDiff ($C[$port_name], $item['left']);
            $right_text = serializeVLANPack ($item['right']);
            // enable, but consider each option independently
            // Don't accept running VLANs not in domain, and
            // don't offer anything, that VST will deny.
            // Consider domain and template constraints.
            $radio_attrs = array ('left' => '', 'asis' => ' checked', 'right' => '');
            if
            (
                !acceptable8021QConfig ($item['right']) or
                count (array_diff ($item['right']['allowed'], $domvlans)) or
                !goodModeForVSTRole ($item['right']['mode'], $item['vst_role'])
            )
                $radio_attrs['left'] = ' disabled';
            break;
        case 'ok_to_push_with_merge':
            $trclass = 'trbusy';
            $left_extra = ' trok';
            $right_extra = ' trwarning';
            $left_text = formatVLANPackDiff ($C[$port_name], $item['left']);
            $right_text = serializeVLANPack ($item['right']);
            break;
        case 'none':
            $left_text = '&nbsp;';
            $right_text = '&nbsp;';
            break;
        case 'martian_conflict':
            if ($item['right']['mode'] == 'none')
                $right_text = '&nbsp;';
            else
            {
                $right_text = serializeVLANPack ($item['right']);
                $right_extra = ' trerror';
            }
            if ($item['left']['mode'] == 'none')
                $left_text = '&nbsp;';
            else
            {
                $left_text = serializeVLANPack ($item['left']);
                $left_extra = ' trerror';
                $radio_attrs = array ('left' => '', 'asis' => ' checked', 'right' => ' disabled');
                // idem, see above
                $item['right'] = $default_port;
            }
            break;
        default:
            $trclass = 'trerror';
            $left_text = $right_text = 'internal rendering error';
            break;
        }

        $anchor = '';
        $td_class = '';
        if (isset ($hl_port_name) and $hl_port_name == $port_name)
        {
            $anchor = "name='port-$hl_port_id'";
            $td_class = ' border_highlight';
        }
        $smod->addOutput('Trclass', $trclass);
        $smod->addOutput('Tdclass', $td_class);
        $smod->addOutput('Port_Name', $port_name);
        $smod->addOutput('Left_Extra', $left_extra);
        $smod->addOutput('Left_Text', $left_text);
        $smod->addOutput('Right_Extra', $right_extra);
        $smod->addOutput('Right_Text', $right_text);
        
        if (!count ($radio_attrs))
        {
            $smod->addOutput('Empty_Radioattrs', TRUE);
            if ($maxdecisions)
                $smod->addOutput('Maxdecisions', TRUE);
        }
        else
        {
            $tdloop = array();
            foreach ($radio_attrs as $pos => $attrs)
            {
                $tdloop[] = array(  'Rownum' => $rownum,
                                    'Position' => $pos,
                                    'Attrs' => $attrs);
            }
            $smod->addOutput('Looparray', $tdloop);
        }
        
        if (count ($radio_attrs))
        {
            $smod->addOutput('Item_Mode', $item['right']['mode']);
            $smod->addOutput('Item_Native', $item['right']['native']);
        
            $input = array();
            foreach ($item['right']['allowed'] as $a)
            {
                $input[] = array('Rownum' => $rownum,
                                 'A' => $a);
            }
            $smod->addOutput('Looparray2', $input);
            $smod->addOutput('Html', htmlspecialchars ($port_name));
        }
        $rownum += count ($radio_attrs) ? 1 : 0;
    }
    if ($rownum) // normally should be equal to $maxdecisions
    {
        $mod->addOutput('Rownum_Set', TRUE);
    }
}

function renderObject8021QSyncPorts ($object, $D, $placeholder, $parent)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule($placeholder ,"RenderObject8021QSyncPorts", $parent);

    $allethports = array();
    foreach (array_filter ($object['ports'], 'isEthernetPort') as $port)
        $allethports[$port['name']] = formatPortIIFOIF ($port);
    $enabled = array();
# OPTIONSs for existing 802.1Q ports
    foreach (sortPortList ($D) as $portname => $portconfig)
        $enabled["disable ${portname}"] = "${portname} ("
                                          . array_fetch ($allethports, $portname, 'N/A')
                                          . ') ' . serializeVLANPack ($portconfig);
# OPTIONs for potential 802.1Q ports
    $disabled = array();
    foreach (sortPortList ($allethports) as $portname => $iifoif)
        if (! array_key_exists ("disable ${portname}", $enabled))
            $disabled["enable ${portname}"] = "${portname} (${iifoif})";

    $mod->setOutput('Nifty_Select', getNiftySelect (array ('select ports to disable 802.1Q' => $enabled, 'select ports to enable 802.1Q' => $disabled),
                    array ('name' => 'ports[]', 'multiple' => 1, 'size' => getConfigVar ('MAXSELSIZE')),
                    NULL));
}

function renderVSTListEditor()
{
    $tplm  =TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderVSTListEditor');
    $mod->setNamespace('vst', TRUE);

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput("AddTop", true);

    $vstList = array();

    foreach (listCells ('vst') as $vst_id => $vst_info)
    {
        $vstListItem = array();
        
        $vstListItem['Vst_Id'] = $vst_id;
        if ($vst_info['switchc'])
            $vstListItem['Switchc_Set'] = TRUE;

        $vstListItem['NiftyString'] = niftyString ($vst_info['description'], 0);

        $vstListItem['ImageHref'] = getImageHREF ('save', 'update template', TRUE);

        $vstList[] = $vstListItem;
    }
    $mod->addOutput("VstList", $vstList);

    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
        $mod->addOutput("AddTop", false);
}

function renderVSTRules ($rules, $title = NULL, $parent = null, $placeholder = 'Payload')
{
    $tplm = TemplateManager::getInstance();
  
    if($parent == null)
        $mod = $tplm->generateSubmodule($placeholder, 'RenderVSTRules');
    else
        $mod = $tplm->generateSubmodule($placeholder, 'RenderVSTRules', $parent);
    $mod->setNamespace('vst', TRUE);

    if (!count ($rules))
    {
        $mod->addOutput('Rules_empty', TRUE);
        $mod->addOutput('Title', isset($title)? $title: 'no rules');
    }
    else
    {
        global $port_role_options, $nextorder;
        $mod->addOutput('Title', isset($title)? $title: 'rules (' . count ($rules) . ')');
        $order = 'odd';

        $vstRows = array();

        foreach ($rules as $item)
        {
            $vstRows[] = array('Order' => $order,
                               'Rule_no' => $item['rule_no'],
                               'Port_pcre' => $item['port_pcre'],
                               'Port_role' => $port_role_options[$item['port_role']],
                               'Wrt_vlans' => $item['wrt_vlans'],
                               'Description' => $item['description']);
            $order = $nextorder[$order];
        }
        $mod->addOutput("VstRows", $vstRows);
    }
}

function renderVST ($vst_id)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderVST');
    $mod->setNamespace('vst', true);

    $vst = spotEntity ('vst', $vst_id);
    amplifyCell ($vst);
    $mod->addOutput('Vst', $vst);
    $mod->addOutput('VstDescription', $vst['description']);
    $mod->addOutput('Switches', $vst['switches']);

    renderEntitySummary ($vst, 'summary', array ('tags' => ''), $mod, 'EntitySummary');
    renderVSTRules ($vst['rules'], null, $mod, 'VstRules');

    if (!count ($vst['switches']))
        $mod->addOutput('EmptySwitches', true);
    else
    {
        global $nextorder;
        $order = 'odd';
        $arr = array();
        foreach (array_keys ($vst['switches']) as $object_id)
        {
            $arr[] = array('Render_cell' => renderCell (spotEntity ('object', $object_id)), 'Order' => $order);
            $order = $nextorder[$order];
        }
        $mod->addOutput('Order_id_array',$arr);
    }
}

function renderVSTRulesEditor ($vst_id)
{
    $vst = spotEntity ('vst', $vst_id);
    amplifyCell ($vst);
    if ($vst['rulec'])
        $source_options = array();
    else
    {
        $source_options = array();
        foreach (listCells ('vst') as $vst_id => $vst_info)
            if ($vst_info['rulec'])
                $source_options[$vst_id] = niftyString ('(' . $vst_info['rulec'] . ') ' . $vst_info['description']);
    }

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'VstRulesEditor');
    $mod->setNamespace('vst',true);
    $mod->setLock();
    $mod->addOutput('Nifty', niftyString ($vst['description']));

    if (count ($source_options))
    {
        $mod->addOutput('Count', true);
        $mod->addOutput('VstMutexRev', $vst['mutex_rev']);
        $mod->addOutput('AccessSelectClone', getSelect ($source_options, array ('name' => 'from_id')));
    }
    printTagsPicker (null, $mod, 'TagsPicker');
    global $port_role_options;
    $mod->addOutput('AccessSelect',  getSelect ($port_role_options, array ('name' => 'port_role'), 'anymode'));

    @session_start();

    $arr = array();
    foreach (isset ($_SESSION['vst_edited']) ? $_SESSION['vst_edited'] : $vst['rules'] as $item)
    {
        $arr[] = array('RuleNo' => $item['rule_no'], 'PortPCRE' => htmlspecialchars($item['port_pcre'], ENT_QUOTES), 'AccessSelectSingle' => getSelect ($port_role_options, array ('name' => 'port_role'), $item['port_role']), 'WRTVlans' => $item['wrt_vlans'], 'Description' => $item['description']);
    };
    $mod->addOutput('ItemArray',$arr);
    $mod->addOutput('MutexRev', $vst['mutex_rev']);
    if (isset ($_SESSION['vst_edited']))
    {
        // draw current template
        renderVSTRules ($vst['rules'], 'currently saved tamplate', $mod, 'VstRules');
        unset ($_SESSION['vst_edited']);
    }
    session_commit();

    if (count ($source_options))
        $mod->addOutput('CountSourceOption', TRUE);
}

function renderDeployQueue()
{
    global $nextorder, $dqtitle;
    $order = 'odd';
    $dqcode = getBypassValue();
    $allq = get8021QDeployQueues();

    $tplm = TemplateManager::getInstance();

    foreach ($allq as $qcode => $data)
        if ($dqcode == $qcode)
        {
            $mod = $tplm->generateSubmodule("Payload","DeployQueue");
            $mod->setNamespace("", true);

            $mod->setOutput("dqTitle",$dqtitle[$qcode]);
            $mod->setOutput("countData", count ($data));

            if (! count ($data))
            {
                $mod->setOutput("continue", true);
                continue;
            }
            $mod->setOutput("continue", false);
            $dataArr =array();
            foreach ($data as $item)
            {
                $dataArr[] = array("order" => $order, "renderedCell" => renderCell (spotEntity ('object', $item['object_id'])),
                                   "formatedAge" => formatAge ($item['last_change']));

                $order = $nextorder[$order];
            }
            $mod->setOutput("dataArr", $dataArr);
        }
}

function renderDiscoveredNeighbors ($object_id)
{
    global $tabno;

    $opcode_by_tabno = array
                       (
                           'livecdp' => 'getcdpstatus',
                           'livelldp' => 'getlldpstatus',
                       );
    try
    {
        $neighbors = queryDevice ($object_id, $opcode_by_tabno[$tabno]);
        $neighbors = sortPortList ($neighbors);
    }
    catch (RTGatewayError $e)
    {
        showError ($e->getMessage());
        return;
    }
    $mydevice = spotEntity ('object', $object_id);
    amplifyCell ($mydevice);

    // reindex by port name
    $myports = array();
    foreach ($mydevice['ports'] as $port)
        if (mb_strlen ($port['name']))
            $myports[$port['name']][] = $port;

    // scroll to selected port
    if (isset ($_REQUEST['hl_port_id']))
    {
        assertUIntArg('hl_port_id');
        $hl_port_id = intval ($_REQUEST['hl_port_id']);
        addAutoScrollScript ("port-$hl_port_id");
    }

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderDiscoveredNeighbors");
    $mod->setNamespace("object");

    switchportInfoJS($object_id, $mod, 'switchPortScripts'); // load JS code to make portnames interactive
    $inputno = 0;
    foreach ($neighbors as $local_port => $remote_list)
    {
        $initial_row = TRUE; // if port has multiple neighbors, the first table row is initial
        // array of local ports with the name specified by DP
        $local_ports = isset($myports[$local_port]) ? $myports[$local_port] : array();
        foreach ($remote_list as $dp_neighbor) // step over DP neighbors
        {
            $error_message = NULL;
            $link_matches = FALSE;
            $portinfo_local = NULL;
            $portinfo_remote = NULL;
            $variants = array();

            do   // once-cyle fake loop used only to break out of it
            {
                if (! empty($local_ports))
                    $portinfo_local = $local_ports[0];

                // find remote object by DP information
                $dp_remote_object_id = searchByMgmtHostname ($dp_neighbor['device']);
                if (! $dp_remote_object_id)
                    $dp_remote_object_id = lookupEntityByString ('object', $dp_neighbor['device']);
                if (! $dp_remote_object_id)
                {
                    $error_message = "No such neighbor <i>${dp_neighbor['device']}</i>";
                    break;
                }
                $dp_remote_object = spotEntity ('object', $dp_remote_object_id);
                amplifyCell($dp_remote_object);
                $dp_neighbor['port'] = shortenIfName ($dp_neighbor['port'], NULL, $dp_remote_object['id']);

                // get list of ports that have name matching CDP portname
                $remote_ports = array(); // list of remote (by DP info) ports
                foreach ($dp_remote_object['ports'] as $port)
                    if ($port['name'] == $dp_neighbor['port'])
                    {
                        $portinfo_remote = $port;
                        $remote_ports[] = $port;
                    }

                // check if ports with such names exist on devices
                if (empty ($local_ports))
                {
                    $error_message = "No such local port <i>$local_port</i>";
                    break;
                }
                if (empty ($remote_ports))
                {
                    $error_message = "No such port on "
                                     . formatPortLink ($dp_remote_object['id'], $dp_remote_object['name'], NULL, NULL);
                    break;
                }

                // determine match or mismatch of local link
                foreach ($local_ports as $portinfo_local)
                    if ($portinfo_local['remote_id'])
                    {
                        if
                        (
                            $portinfo_local['remote_object_id'] == $dp_remote_object_id
                            and $portinfo_local['remote_name'] == $dp_neighbor['port']
                        )
                        {
                            // set $portinfo_remote to corresponding remote port
                            foreach ($remote_ports as $portinfo_remote)
                                if ($portinfo_remote['id'] == $portinfo_local['remote_id'])
                                    break;
                            $link_matches = TRUE;
                            unset ($error_message);
                        }
                        elseif ($portinfo_local['remote_object_id'] != $dp_remote_object_id)
                        $error_message = "Remote device mismatch - port linked to "
                                         . formatLinkedPort ($portinfo_local);
                        else // ($portinfo_local['remote_name'] != $dp_neighbor['port'])
                            $error_message = "Remote port mismatch - port linked to "
                                             . formatPortLink ($portinfo_local['remote_object_id'], NULL, $portinfo_local['remote_id'], $portinfo_local['remote_name']);;
                        break 2;
                    }

                // no local links found, try to search for remote links
                foreach ($remote_ports as $portinfo_remote)
                    if ($portinfo_remote['remote_id'])
                    {
                        $remote_link_html = formatLinkedPort ($portinfo_remote);
                        $remote_port_html = formatPortLink ($portinfo_remote['object_id'], NULL, $portinfo_remote['id'], $portinfo_remote['name']);
                        $error_message = "Remote port $remote_port_html is already linked to $remote_link_html";
                        break 2;
                    }

                // no links found on both sides, search for a compatible port pair
                $port_types = array();
                foreach (array ('left' => $local_ports, 'right' => $remote_ports) as $side => $port_list)
                    foreach ($port_list as $portinfo)
                    {
                        $tmp_types = ($portinfo['iif_id'] == 1) ?
                                     array ($portinfo['oif_id'] => $portinfo['oif_name']) :
                                     getExistingPortTypeOptions ($portinfo['id']);
                        foreach ($tmp_types as $oif_id => $oif_name)
                            $port_types[$side][$oif_id][] = array ('id' => $oif_id, 'name' => $oif_name, 'portinfo' => $portinfo);
                    }

                foreach ($port_types['left'] as $left_id => $left)
                    foreach ($port_types['right'] as $right_id => $right)
                        if (arePortTypesCompatible ($left_id, $right_id))
                            foreach ($left as $left_port)
                                foreach ($right as $right_port)
                                    $variants[] = array ('left' => $left_port, 'right' => $right_port);
                if (! count ($variants)) // no compatible ports found
                    $error_message = "Incompatible port types";
            }
            while (FALSE);   // do {

            $tr_class = $link_matches ? 'trok' : (isset ($error_message) ? 'trerror' : 'trwarning');
            $singleNeighbor = array('tr_class' => $tr_class);

            if ($initial_row)
            {
                $count = count ($remote_list);
                $td_class = '';

                $singleNeighbor['isInitialRow'] = true;

                if (isset ($hl_port_id) and $hl_port_id == $portinfo_local['id'])
                    $singleNeighbor['td_class'] = "class='border_highlight'";

                if($portinfo_local)
                    formatPortLink ($mydevice['id'], NULL, $portinfo_local['id'], $portinfo_local['name'], 'interactive-portname port-menu', $mod, 'id_port_link_local');
                else
                    $singlePort['localport'] = $localport;

                $initial_row = FALSE;
            }
            $singleNeighbor['portIIFOIFLocal'] = ($portinfo_local ?  formatPortIIFOIF ($portinfo_local) : '&nbsp;');
            formatIfTypeVariants ($variants, "ports_${inputno}", $mod, "ifTypeVariants");
            $singleNeighbor['device'] = $dp_neighbor['device'];
            if($portinfo_remote)
                formatPortLink ($dp_remote_object_id, NULL, $portinfo_remote['id'], $portinfo_remote['name'], $mod, 'id_port_link_remote');
            else
                $singlePort['port'] = $dp_neighbor['port'];
            $singleNeighbor['portIIFOIFRemote'] = ($portinfo_remote ?  formatPortIIFOIF ($portinfo_remote) : '&nbsp;');
            if (! empty ($variants))
            {
                $singleNeighbor['inputno'] = $inputno;
                $inputno++;
            }

            if (isset ($error_message))
                $singleNeighbor['error_message'] = $error_message;

            //Using array generated for possible array
            $tplm->generatePseudoSubmodule('AllNeighbors', $mod, $singleNeighbor);
        }
    }

    if ($inputno)
    {
        $mod->addOutput("inputno", $inputno);
    }
}

// $variants is an array of items like this:
// array (
//	'left' => array ('id' => oif_id, 'name' => oif_name, 'portinfo' => $port_info),
//	'left' => array ('id' => oif_id, 'name' => oif_name, 'portinfo' => $port_info),
// )
function formatIfTypeVariants ($variants, $select_name, $parent = null, $placeholder = "ifTypeVariants" )
{
    if (empty ($variants))
        return;
    static $oif_usage_stat = NULL;
    $select = array();
    $creating_transceivers = FALSE;
    $most_used_count = 0;
    $selected_key = NULL;
    $multiple_left = FALSE;
    $multiple_right = FALSE;

    $seen_ports = array();
    foreach ($variants as $item)
    {
        if (isset ($seen_ports['left']) && $item['left']['portinfo']['id'] != $seen_ports['left'])
            $multiple_left = TRUE;
        if (isset ($seen_ports['right']) && $item['right']['portinfo']['id'] != $seen_ports['right'])
            $multiple_right = TRUE;
        $seen_ports['left'] = $item['left']['portinfo']['id'];
        $seen_ports['right'] = $item['right']['portinfo']['id'];
    }

    if (! isset ($oif_usage_stat))
        $oif_usage_stat = getPortTypeUsageStatistics();

    foreach ($variants as $item)
    {
        // format text label for selectbox item
        $left_text = ($multiple_left ? $item['left']['portinfo']['iif_name'] . '/' : '') . $item['left']['name'];
        $right_text = ($multiple_right ? $item['right']['portinfo']['iif_name'] . '/' : '') . $item['right']['name'];
        $text = $left_text;
        if ($left_text != $right_text && strlen ($right_text))
        {
            if (strlen ($text))
                $text .= " | ";
            $text .= $right_text;
        }

        // fill the $params: port ids and port types
        $params = array
                  (
                      'a_id' => $item['left']['portinfo']['id'],
                      'b_id' => $item['right']['portinfo']['id'],
                  );
        $popularity_count = 0;
        foreach (array ('left' => 'a', 'right' => 'b') as $side => $letter)
        {
            $params[$letter . '_oif'] = $item[$side]['id'];
            $type_key = $item[$side]['portinfo']['iif_id'] . '-' . $item[$side]['id'];
            if (isset ($oif_usage_stat[$type_key]))
                $popularity_count += $oif_usage_stat[$type_key];
        }

        $key = ''; // key sample: a_id:id,a_oif:id,b_id:id,b_oif:id
        foreach ($params as $i => $j)
            $key .= "$i:$j,";
        $key = trim($key, ",");
        $select[$key] = (count ($variants) == 1 ? '' : $text); // empty string if there is simple single variant
        $weights[$key] = $popularity_count;
    }
    arsort ($weights, SORT_NUMERIC);
    $sorted_select = array();
    foreach (array_keys ($weights) as $key)
        $sorted_select[$key] = $select[$key];
    if($parent == null)
        return getSelect ($sorted_select, array('name' => $select_name));
    else
        getSelect ($sorted_select, array('name' => $select_name), NULL, TRUE, $parent, $placeholder);
}

function formatAttributeValue ($record)
{
    if ('date' == $record['type'])
        return datetimestrFromTimestamp ($record['value']);

    if (! isset ($record['key'])) // if record is a dictionary value, generate href with autotag in cfe
    {
        if ($record['id'] == 3) // FQDN attribute
            foreach (getMgmtProtosConfig() as $proto => $filter)
                try
                {
                    if (considerGivenConstraint (NULL, $filter))
                    {
                        $blank = (preg_match ('/^https?$/', $proto) ? 'target=_blank' : '');
                        return "<a $blank title='Open $proto session' class='mgmt-link' href='" . $proto . '://' . $record['a_value'] . "'>${record['a_value']}</a>";
                    }
                }
                catch (RackTablesError $e)
                {
                    // syntax error in $filter
                    continue;
                }
        return isset ($record['href']) ? "<a href=\"".$record['href']."\">${record['a_value']}</a>" : $record['a_value'];
    }

    $href = makeHref
            (
                array
                (
                    'page'=>'depot',
                    'tab'=>'default',
                    'andor' => 'and',
                    'cfe' => '{$attr_' . $record['id'] . '_' . $record['key'] . '}',
                )
            );
    $result = "<a href='$href'>" . $record['a_value'] . "</a>";
    if (isset ($record['href']))
        $result .= "&nbsp;<a class='img-link' href='${record['href']}'>" . getImageHREF ('html', 'vendor&apos;s info page') . "</a>";
    return $result;
}

function addAutoScrollScript ($anchor_name, $parent = null, $placeholder = "autoScrollScript")
{
    $tplm = TemplateManager::getInstance();

    if($parent==null)
        $mod = $tplm->generateModule("AddAutoScrollScript");
    else
        $mod = $tplm->generateSubmodule($placeholder, "AddAutoScrollScript", $parent);

    $mod->setNamespace("");
    $mod->setOutput('AnchorName', $anchor_name);
    if($parent==null)
        return $mod->run();
}

//
// Display object level logs
//
function renderObjectLogEditor ()
{
    $tplm = TemplateManager::getInstance();   
    $mod = $tplm->generateSubmodule('Payload', 'RenderObjectLogEditor');
    $mod->setNamespace('location',true);

    global $nextorder;
    $mod->addOutput('Image_Href', getImageHREF ('CREATE', 'add record', TRUE, 101));

    $order = 'even';
    foreach (getLogRecordsForObject (getBypassValue()) as $row)
    {
        $submod = $tplm->generateSubmodule('Rows', 'RowGenerator', $mod);
        $submod->setOutput('Order', $order);
        $submod->setOutput('Date', $row['date']);
        $submod->setOutput('User', $row['user']);
        $submod->setOutput('Hrefs', string_insert_hrefs (htmlspecialchars ($row['content'], ENT_NOQUOTES)));
        $submod->setOutput('Id', $row['id']);
        $order = $nextorder[$order];
    }
}

//
// Display form and All log entries
//
function allObjectLogs ()
{
    $tplm = TemplateManager::getInstance();
    $logs = getLogRecords ();

    if (count($logs) > 0)
    {
        $mod = $tplm->generateSubmodule("Payload", "AllObjectLogs");
        $mod->setNamespace("objectlog",true);

        global $nextorder;
        $order = 'odd';

        $log_data_array = array();

        foreach ($logs as $row)
        {
            $row_data = array('order' => $order);
            // Link to a different page if the object is a Rack
            if ($row['objtype_id'] == 1560)
            {
                $text = $row['name'];
                $entity = 'rack';
            }
            else
            {
                $object = spotEntity ('object', $row['object_id']);
                $text = $object['dname'];
                $entity = 'object';
            }

            $row_data["Object_id"] = mkA ($text, $entity, $row['object_id'], 'log');
            $row_data["Date"] = $row['date'];
            $row_data["User"] = $row['user'];
            $row_data["Logentry"] = string_insert_hrefs (htmlspecialchars ($row['content'], ENT_NOQUOTES));

            $order = $nextorder[$order];

            $log_data_array[] = $row_data;
        }

        $mod->setOutput("IMAGE_HREF", getImageHREF('text'));
        $mod->addOutput("LogTableData", $log_data_array);
    }
    else
        $tplm->generateSubmodule("Payload", "NoObjectLogFound", null, true);

}

function renderGlobalLogEditor()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","GlobalLogEditor");
    $mod->setOutput('Select', getSelect (getNarrowObjectList(), array ('name' => 'object_id')));
}

function renderVirtualResourcesSummary ()
{
    global $pageno, $nextorder;
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderVirtualResourcesSummary");
    $mod->setNamespace("", true);


    $clusters = getVMClusterSummary ();
    $mod->setOutput("countClusters", count($clusters));

    if (count($clusters) > 0)
    {
        $mod->setOutput("areClusters", true);

        $order = 'odd';
        $clustersArr = array();
        foreach ($clusters as $cluster)
        {
            $total_vms = $cluster['cluster_vms'] + $cluster['resource_pool_vms'];

            $clustersArr[] = array("order" => $order, "mka" => mkA ("<strong>${cluster['name']}</strong>", 'object', $cluster['id']),
                                   "clusterHypervisors" => $cluster['hypervisors'], "clusterResPools" => $cluster['resource_pools'],
                                   "clusterVM" => $cluster['cluster_vms'], "clusterResPoolVMs" => $cluster['resource_pool_vms'],
                                   "totalVMs" => $total_vms);
            $order = $nextorder[$order];
        }
        $mod->setOutput("clusterArray", $clustersArr);

    }

    $pools = getVMResourcePoolSummary ();
    $mod->setOutput("countResPools", count($pools));

    if (count($pools) > 0)
    {
        $mod->setOutput("areResPools", true);

        $order = 'odd';
        $poolsArr = array();
        foreach ($pools as $pool)
        {
            $singPool = array("order" => $order, "mka" => mkA ("<strong>${pool['name']}</strong>", 'object', $pool['id']),
                              "poolVMs" => $pool['VMs']);
            if ($pool['cluster_id'])
                $singPool['clusterID'] = mkA ("<strong>${pool['cluster_name']}</strong>", 'object', $pool['cluster_id']);
            $poolsArr[] = $singPool;
            $order = $nextorder[$order];

        }
        $mod->setOutput("poolsArray", $poolsArr);

    }

    $hypervisors = getVMHypervisorSummary ();
    $mod->setOutput("hypervisorCount", count($hypervisors));

    if (count($hypervisors) > 0)
    {
        $mod->setOutput("areHypervisors", true);

        $order = 'odd';
        $hypersArr = array();
        foreach ($hypervisors as $hypervisor)
        {
            $singHyper = array("order" => $order, "mka" => mkA ("<strong>${hypervisor['name']}</strong>", 'object', $hypervisor['id']),
                               "hyperVMs" => $hypervisor['VMs']);
            if ($hypervisor['cluster_id'])
                $singHyper['hyperID'] = mkA ("<strong>${hypervisor['cluster_name']}</strong>", 'object', $hypervisor['cluster_id']);
            $hypersArr[] = $singHyper;
            $order = $nextorder[$order];
        }
        $mod->setOutput("hypersArray", $hypersArr);

    }

    $switches = getVMSwitchSummary ();
    $mod->setOutput("countSwitches", count($switches));

    if (count($switches) > 0)
    {
        $mod->setOutput("areSwitches", true);

        $order = 'odd';
        $switchesArr = array();
        foreach ($switches as $switch)
        {
            $switchesArr[] = array("order" => $order, "mka" => mkA ("<strong>${switch['name']}</strong>", 'object', $switch['id']));
            $order = $nextorder[$order];
        }
        $mod->setOutput("switchesArray", $switchesArr);

    }
}

function switchportInfoJS($object_id, $parent = null, $placeholder = "switchportinfoJS")
{
    $available_ops = array
                     (
                         'link' => array ('op' => 'get_link_status', 'gw' => 'getportstatus'),
                         'conf' => array ('op' => 'get_port_conf', 'gw' => 'get8021q'),
                         'mac' =>  array ('op' => 'get_mac_list', 'gw' => 'getmaclist'),
                         'portmac' => array ('op' => 'get_port_mac_list', 'gw' => 'getportmaclist'),
                     );
    $breed = detectDeviceBreed ($object_id);
    $allowed_ops = array();
    foreach ($available_ops as $prefix => $data)
        if
        (
            permitted ('object', 'liveports', $data['op']) and
            validBreedFunction ($breed, $data['gw'])
        )
            $allowed_ops[] = $prefix;

    // make JS array with allowed items
    $list = '';
    foreach ($allowed_ops as $item)
        $list .= "'" . addslashes ($item) . "', ";
    $list = trim ($list, ", ");

    $tplm = TemplateManager::getInstance();
    if($parent==null)
        $mod = $tplm->generateModule("SwitchPortInfoJS");
    else
        $mod = $tplm->generateSubmodule($placeholder, "SwitchPortInfoJS", $parent);

    $mod->setNamespace("");
    $mod->setOutput('List', $list);

    if($parent==null)
        return $mod->run();
}

// Formats VLAN packs: if they are different, the old appears stroken, and the new appears below it
// If comparing the two sets seems being complicated for human, this function generates a diff between old and new packs
function formatVLANPackDiff ($old, $current)
{
    $ret = '';
    $new_pack = serializeVLANPack ($current);
    $new_size = substr_count ($new_pack, ',');
    if (! same8021QConfigs ($old, $current))
    {
        $old_pack = serializeVLANPack ($old);
        $old_size = substr_count ($old_pack, ',');
        $ret .= '<s>' . $old_pack . '</s><br>';
        // make diff
        $added = groupIntsToRanges (array_diff ($current['allowed'], $old['allowed']));
        $removed = groupIntsToRanges (array_diff ($old['allowed'], $current['allowed']));
        if ($old['mode'] == $current['mode'] && $current['mode'] == 'trunk')
        {
            if (! empty ($added))
                $ret .= '<span class="vlan-diff diff-add">+ ' . implode (', ', $added) . '</span><br>';
            if (! empty ($removed))
                $ret .= '<span class="vlan-diff diff-rem">- ' . implode (', ', $removed) . '</span><br>';
        }
    }
    $ret .= $new_pack;
    return $ret;
}

function renderIPAddressLog ($ip_bin)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderIPAddressLog');
    $mod->setNamespace('ipaddress',true);
    $mod->setLock();

    $odd = FALSE;
    $out = array();
    foreach (array_reverse (fetchIPLogEntry ($ip_bin)) as $line)
    {
        $tr_class = $odd ? 'row_odd' : 'row_even';
        $out[] = array('Class'=>$tr_class,'Date'=>$line['date'],'User'=>$line['user'],'Message'=>$line['message']);
        $odd = !$odd;
    }
    $mod->addOutput('Messages', $out);
}

function renderObjectCactiGraphs ($object_id)
{
    function printNewItemTR ($options, $placeholder)
    {
        $smod = $tplm->generateSubmodule($placeholder, 'RenderObjectMuninCactiGraphs_PrintNew', $mod);
        $smod->addOutput('Getselect', getSelect ($options, array ('name' => 'server_id')));

    }

    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule("Payload","RenderObjectCactiGraphs");
    $mod->setNamespace("object", TRUE);

    if (!extension_loaded ('curl'))
        throw new RackTablesError ("The PHP cURL extension is not loaded.", RackTablesError::MISCONFIGURED);

    $servers = getCactiServers();
    $options = array();
    foreach ($servers as $server)
        $options[$server['id']] = "${server['id']}: ${server['base_url']}";
    
    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes' && permitted('object','cacti','add'))
        printNewItemTR ($options, 'NewTop');
    foreach (getCactiGraphsForObject ($object_id) as $graph_id => $graph)
    {
        $submod = $tplm->generatePseudoSubmodule('RowsTR', $mod);
  
        $submod->addOutput('Cacti_Url', $servers[$graph['server_id']]['base_url']);
        $submod->addOutput('Graph_Id', $graph_id);
        $submod->addOutput('Object_Id', $object_id);
        $submod->addOutput('Server_Id', $graph['server_id']);
        $submod->addOutput('Caption', $graph['caption']);

        if(permitted('object','cacti','del'))
            $submod->setOutput('Permitted', TRUE);
    }
   
    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes' && permitted('object','cacti','add'))
        printNewItemTR ($options, 'NewBottom');
}

function renderObjectMuninGraphs ($object_id)
{
    function printNewItem ($options, $placeholder)
    {
        $smod = $tplm->generateSubmodule($placeholder, 'RenderObjectMuninCactiGraphs_PrintNew', $mod);
        $smod->addOutput('Getselect', getSelect ($options, array ('name' => 'server_id')));
    }

    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule("Payload","RenderObjectMuninGraphs");
    $mod->setNamespace("object", TRUE);

    if (!extension_loaded ('curl'))
        throw new RackTablesError ("The PHP cURL extension is not loaded.", RackTablesError::MISCONFIGURED);

    $servers = getMuninServers();
    $options = array();
    foreach ($servers as $server)
        $options[$server['id']] = "${server['id']}: ${server['base_url']}";
    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        printNewItem ($options, 'NewTop');

    $object = spotEntity ('object', $object_id);
    list ($host, $domain) = preg_split ("/\./", $object['dname'], 2);

    foreach (getMuninGraphsForObject ($object_id) as $graph_name => $graph)
    {
        $submod = $tplm->generatePseudoSubmodule('Rows', $mod);
        $submod->addOutput('Munin_Url', $servers[$graph['server_id']]['base_url']);
        $submod->addOutput('Domain', $domain);
        $submod->addOutput('Dname', $object['dname']);
        $submod->addOutput('Graph_Name', $graph_name);
        $submod->addOutput('Object_Id', $object_id);
        $submod->addOutput('Server_Id', $graph['server_id']);
        $submod->addOutput('Caption', $graph['caption']);
    }
    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
        printNewItem ($options, 'NewBottom');
}

function renderEditVlan ($vlan_ck)
{
    global $vtoptions;
    $vlan = getVLANInfo ($vlan_ck);

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload","RenderEditVlan");
    $mod->setNamespace("vlan");

    $mod->addOutput("vlan_descr", $vlan['vlan_descr']);
    $mod->addOutput("vtoptions", $vtoptions);
    $mod->addOutput("vlan_prop", $vlan['vlan_prop']);

    $mod->addOutput("htmlspcDomainID", htmlspecialchars ($vlan['domain_id'], ENT_QUOTES));
    $mod->addOutput("htmlspcVlanID", htmlspecialchars ($vlan['vlan_id'], ENT_QUOTES));
    // get configured ports count
    $portc = 0;
    foreach (getVLANConfiguredPorts ($vlan_ck) as $subarray)
        $portc += count ($subarray);

    $clear_line = '';
    $delete_line = '';
    if ($portc)
    {
        $mod->addOutput("port", $portc);
        $mod->addOutput("mkaPortc", mkA ("${portc} ports", 'vlan', $vlan_ck));
        $mod->addOutput("isPortc", true);
    }

    $reason = '';
    if ($vlan['vlan_id'] == VLAN_DFL_ID)
        $reason = "You can not delete default VLAN";
    elseif ($portc)
    $reason = "Can not delete: $portc ports configured";
    if (! empty ($reason))
        $mod->addOutput("reasonLink", getOpLink (NULL, 'delete VLAN', 'nodestroy', $reason));
    else
        $mod->addOutput("reasonLink", getOpLink (array ('op' => 'del', 'vlan_ck' => $vlan_ck), 'delete VLAN', 'destroy'));
}

function renderExpirations ()
{
    global $nextorder;
    $breakdown = array();
    $breakdown[21] = array
                     (
                         array ('from' => -365, 'to' => 0, 'class' => 'has_problems_', 'title' => 'has expired within last year'),
                         array ('from' => 0, 'to' => 30, 'class' => 'row_', 'title' => 'expires within 30 days'),
                         array ('from' => 30, 'to' => 60, 'class' => 'row_', 'title' => 'expires within 60 days'),
                         array ('from' => 60, 'to' => 90, 'class' => 'row_', 'title' => 'expires within 90 days'),
                     );
    $breakdown[22] = $breakdown[21];
    $breakdown[24] = $breakdown[21];
    $attrmap = getAttrMap();

    $tplm = TemplateManager::getInstance();

    foreach ($breakdown as $attr_id => $sections)
    {
        $mod = $tplm->generateSubmodule("Payload","RenderExpirations");
        $mod->setNamespace("reports");
        $mod->setOutput('AttrId', $attrmap[$attr_id]['name']);
        $allSectsOut = array();
        foreach ($sections as $section)
        {
            $singleSectOut = array();
            $count = 1;
            $order = 'odd';
            $result = scanAttrRelativeDays ($attr_id, $section['from'], $section['to']);

            $singleSectOut['Title'] = $section['title'];

            if (! count ($result))
            {
                $singleSectOut['CountMod'] = $tplm->generateModule('ExpirationsNoSection', true)->run();
                $allSectsOut[] = $singleSectOut;
                continue;
            }
            $allExpirationResultsOut = array();
            foreach ($result as $row)
            {
                $date_value = datetimestrFromTimestamp ($row['uint_value']);

                $object = spotEntity ('object', $row['object_id']);
                $attributes = getAttrValues ($object['id']);
                $oem_sn_1 = array_key_exists (1, $attributes) ? $attributes[1]['a_value'] : '&nbsp;';

                $allExpirationResultsOut[] = array(
                                                 "ClassOrder" => $section['class'] . $order,
                                                 "Count" => $count,
                                                 "Mka" => mkA ($object['dname'], 'object', $object['id']),
                                                 "AssetNo" => $object['asset_no'],
                                                 "OemSn1" => $oem_sn_1,
                                                 "DateValue" => $date_value );

                $order = $nextorder[$order];
                $count++;
            }

            $singleSectOut['Expiration_Results'] = $allExpirationResultsOut;
            $allSectsOut[] = $singleSectOut;
        }

        $mod->setOutput("AllSects", $allSectsOut);
    }
}

// Do we need to port?
// returns an array with two items - each is HTML-formatted <TD> tag
function formatPortReservation ($port)
{
    $ret = array();
    $ret[] = '<td class=tdleft>' .
             (strlen ($port['reservation_comment']) ? formatLoggedSpan ($port['last_log'], 'Reserved:', 'strong underline') : '').
             '</td>';
    $editable = permitted ('object', 'ports', 'editPort')
                ? 'editable'
                : '';
    $ret[] = '<td class=tdleft>' .
             formatLoggedSpan ($port['last_log'], $port['reservation_comment'], "rsvtext $editable id-${port['id']} op-upd-reservation-port") .
             '</td>';
    return $ret;
}

function renderEditUCSForm()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderEditUCSForm");
    $mod->setNamespace("object",true);

}

function renderCactiConfig()
{
    $servers = getCactiServers();

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderCactiConfig");
    $mod->setNamespace("cacti",true);
    $mod->setLock();

    $mod->addOutput("Count", count($servers));

    $singlServerAttrs = array();
    foreach ($servers as $server)
    {
        $singleServerAttrs[] = array('BaseUrl' => $server['base_url'], 'Username' => $server['username'], 'NumGraphs' => $server['num_graphs']);
    }

    $mod->addOutput("ServerAttributes", $singleServerAttrs);
}

function renderCactiServersEditor()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', 'RenderCactiConfigEditor');
    $mod->setNamespace('cacti',true);
    $mod->setLock();

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput("AddTop", true);

    $cactiServerList = array();
    foreach (getCactiServers() as $server)
    {
        $cactiServerList[] = array('NumGraphs' => $server['num_graphs'],
                                   'BaseUrl' => htmlspecialchars ($server['base_url'], ENT_QUOTES, 'UTF-8'),
                                   'Username' =>  htmlspecialchars ($server['username'], ENT_QUOTES, 'UTF-8'),
                                   'Password' => htmlspecialchars ($server['password'], ENT_QUOTES, 'UTF-8'),
                                   'Id' => $server['id']);
    }
    $mod->addOutput('CactiServers', $cactiServerList);

    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
        $mod->addOutput("AddTop", false);
}

function renderMuninConfig()
{
    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule("Payload","RenderMuninConfig");
    $mod->setNamespace("munin");

    $servers = getMuninServers();
    $mod->addOutput("ServerCount", count ($servers));

    $allServersOut = array();
    foreach ($servers as $server)
    {
        $allServersOut[] = array('NiftyStr' => niftyString ($server['base_url']), 'NumGraphs' => $server['num_graphs'] );
    }
    $mod->addOutput("allServers", $allServersOut);
}

function renderMuninServersEditor()
{
    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule("Payload","RenderMuninServersEditor");
    $mod->setNamespace("munin");

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput("AddTop", true);

    $allMuninServersOut = array();
    foreach (getMuninServers() as $server)
    {
        $singleServer = array(  'FormIntro' => printOpFormIntro ('upd', array ('id' => $server['id'])),
                                'SpecialCharSrv' => htmlspecialchars ($server['base_url'], ENT_QUOTES, 'UTF-8'),
                                'ImageSave' => getImageHREF ('save', 'update this server', TRUE),
                                'NumGraphs' => $server['num_graphs']);

        if ($server['num_graphs'])
            $singleServer['DestroyImg'] = printImageHREF ('nodestroy', 'cannot delete, graphs exist');
        else
            $singleServer['DestroyImg'] = getOpLink (array ('op' => 'del', 'id' => $server['id']), '', 'destroy', 'delete this server');
        $allMuninServersOut[] = $singleServer;
    }
    $mod->addOutput("allMuninServers", $allMuninServersOut);

    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
        $mod->addOutput("AddTop", false);
 }

// The validity of some data cannot be guaranteed using foreign keys.
// Display any invalid rows that have crept in.
// Possible enhancements:
//    - check for IP addresses whose subnet does not exist in IPvXNetwork (X = 4 or 6)
//        - IPvXAddress, IPvXAllocation, IPvXLog, IPvXRS, IPvXVS
//    - provide links/buttons to delete invalid rows
//    - verify that the current DDL is correct for each DB element
//        - columns, indexes, character sets
function renderDataIntegrityReport ()
{
    global $nextorder;
    $violations = FALSE;

    $tplm = TemplateManager::getInstance();

    $mod = $tplm->generateSubmodule("Payload", "RenderDataIntegrityReport");
    $mod->setNamespace("reports");

    // check 1: EntityLink rows referencing not-existent relatives
    // check 1.1: children
    $realms = array
              (
                  'location' => 'Location',
                  'object' => 'RackObject',
                  'rack' => 'Rack',
                  'row' => 'Row'
              );
    $orphans = array ();
    foreach ($realms as $realm => $table)
    {
        $result = usePreparedSelectBlade
                  (
                      'SELECT EL.* FROM EntityLink EL ' .
                      "LEFT JOIN ${table} ON EL.child_entity_id = ${table}.id " .
                      "WHERE EL.child_entity_type = ? AND ${table}.id IS NULL",
                      array ($realm)
                  );
        $rows = $result->fetchAll (PDO::FETCH_ASSOC);
        unset ($result);
        $orphans = array_merge ($orphans, $rows);
    }

    if (count ($orphans))
    {
        $mod->setOutput("ChildrenViolation", true);
        $mod->setOutput("ChildrenCount", count ($orphans));


        $violations = TRUE;
        $order = 'odd';
        $allChildrenOrphansOut = array();
        foreach ($orphans as $orphan)
        {
            $singleOrphanOut = array('Order' => $order, 'RealmName' => $realm_name);

            $realm_name = formatRealmName ($orphan['parent_entity_type']);
            $parent = spotEntity ($orphan['parent_entity_type'], $orphan['parent_entity_id']);
            $singleOrphanOut['ElemName'] = $parent['name'];
            $singleOrphanOut['EntityType'] = $orphan['child_entity_type'];
            $singleOrphanOut['EntityId'] = $orphan['child_entity_id'];

            $order = $nextorder[$order];
            $allChildrenOrphansOut[] = $singleOrphanOut;
        }
        $mod->setOutput("ChildrenOrphans", $allChildrenOrphansOut);
    }

    // check 1.2: parents
    $orphans = array ();
    foreach ($realms as $realm => $table)
    {
        $result = usePreparedSelectBlade
                  (
                      'SELECT EL.* FROM EntityLink EL ' .
                      "LEFT JOIN ${table} ON EL.parent_entity_id = ${table}.id " .
                      "WHERE EL.parent_entity_type = ? AND ${table}.id IS NULL",
                      array ($realm)
                  );
        $rows = $result->fetchAll (PDO::FETCH_ASSOC);
        unset ($result);
        $orphans = array_merge ($orphans, $rows);
    }
    if (count ($orphans))
    {
        $mod->setOutput("ParentsViolation", true);
        $mod->setOutput("ParentsCount", count ($orphans));

        $violations = TRUE;
        $order = 'odd';
        $allParentsOrphansOut = array();
        foreach ($orphans as $orphan)
        {
            $singleOrphanOut = array('order' => $order, 'realm_name' => $realm_name);

            $realm_name = formatRealmName ($orphan['child_entity_type']);
            $child = spotEntity ($orphan['child_entity_type'], $orphan['child_entity_id']);

            $singleOrphanOut['elemName'] = $child['name'];
            $singleOrphanOut['entity_type'] = $orphan['parent_entity_type'];
            $singleOrphanOut['entity_id'] = $orphan['parent_entity_id'];

            $order = $nextorder[$order];
            $allParentsOrphansOut[] = $singleOrphanOut;
        }
        $mod->setOutput("ParentOrphans", $allParentsOrphansOut);
    }

    // check 3: multiple tables referencing non-existent dictionary entries
    // check 3.1: AttributeMap
    $orphans = array ();
    $result = usePreparedSelectBlade
              (
                  'SELECT AM.*, A.name AS attr_name, C.name AS chapter_name ' .
                  'FROM AttributeMap AM ' .
                  'LEFT JOIN Attribute A ON AM.attr_id = A.id ' .
                  'LEFT JOIN Chapter C ON AM.chapter_id = C.id ' .
                  'LEFT JOIN Dictionary D ON AM.objtype_id = D.dict_key ' .
                  'WHERE D.dict_key IS NULL'
              );
    $orphans = $result->fetchAll (PDO::FETCH_ASSOC);
    unset ($result);

    if (count ($orphans))
    {
        $mod->setOutput("AttrMapViolation", true);
        $mod->setOutput("AttrMapCount", count ($orphans));

        $violations = TRUE;
        $order = 'odd';
        $allAttrMapOrphansOut = array();
        foreach ($orphans as $orphan)
        {
            $singleOrphanOut = array('Order' => $order);
            $singleOrphanOut['AttrName'] = $orphan['attr_name'];
            $singleOrphanOut['ChapterName'] = $orphan['chapter_name'];
            $singleOrphanOut['ObjtypeId'] = $orphan['objtype_id'];
            $allAttrMapOrphansOut[] = $singleOrphanOut;
            $order = $nextorder[$order];
        }
        $mod->setOutput("AttrMapOrphans", $allAttrMapOrphansOut);
    }

    // check 3.2: Object
    $orphans = array ();
    $result = usePreparedSelectBlade
              (
                  'SELECT O.* FROM Object O ' .
                  'LEFT JOIN Dictionary D ON O.objtype_id = D.dict_key ' .
                  'WHERE D.dict_key IS NULL'
              );
    $orphans = $result->fetchAll (PDO::FETCH_ASSOC);
    unset ($result);

    if (count ($orphans))
    {
        $mod->setOutput("ObjectViolation", true);
        $mod->setOutput("ObjectCount", count ($orphans));

        $violations = TRUE;
        $order = 'odd';
        $allObjectsOut = array();
        foreach ($orphans as $orphan)
        {
            $singleOrphanOut = array('Order' => $order);
            $singleOrphanOut['Id'] = $orphan['id'];
            $singleOrphanOut['Name'] = $orphan['name'];
            $singleOrphanOut['ObjtypeId'] = $orphan['objtype_id'];
            $allObjectsOut[] = $singleOrphanOut;
            $order = $nextorder[$order];
        }
        $mod->setOutput("AllObjectsOrphans", $allObjectsOut);
    }

    // check 3.3: ObjectHistory
    $orphans = array ();
    $result = usePreparedSelectBlade
              (
                  'SELECT OH.* FROM ObjectHistory OH ' .
                  'LEFT JOIN Dictionary D ON OH.objtype_id = D.dict_key ' .
                  'WHERE D.dict_key IS NULL'
              );
    $orphans = $result->fetchAll (PDO::FETCH_ASSOC);
    unset ($result);
    if (count ($orphans))
    {
        $mod->setOutput("ObjectHistViolation", true);
        $mod->setOutput("ObjectHistCount", count ($orphans));

        $violations = TRUE;
        $order = 'odd';
        $allObjectHistsOut = array();
        foreach ($orphans as $orphan)
        {
            $singleOrphanOut = array('Order' => $order);
            $singleOrphanOut['Id'] = $orphan['id'];
            $singleOrphanOut['Name'] = $orphan['name'];
            $singleOrphanOut['ObjtypeId'] = $orphan['objtype_id'];
            $allObjectHistsOut[] = $singleOrphanOut;
            $order = $nextorder[$order];
        }
        $mod->setOutput("AllObjectHistsOrphans", $allObjectHistsOut);
    }

    // check 3.4: ObjectParentCompat
    $orphans = array ();
    $result = usePreparedSelectBlade
              (
                  'SELECT OPC.*, PD.dict_value AS parent_name, CD.dict_value AS child_name '.
                  'FROM ObjectParentCompat OPC ' .
                  'LEFT JOIN Dictionary PD ON OPC.parent_objtype_id = PD.dict_key ' .
                  'LEFT JOIN Dictionary CD ON OPC.child_objtype_id = CD.dict_key ' .
                  'WHERE PD.dict_key IS NULL OR CD.dict_key IS NULL'
              );
    $orphans = $result->fetchAll (PDO::FETCH_ASSOC);
    unset ($result);
    if (count ($orphans))
    {
        $mod->setOutput("ObjectParViolation", true);
        $mod->setOutput("ObjectParCount", count ($orphans));

        $violations = TRUE;
        $order = 'odd';
        $allObjectParsOut = array();
        foreach ($orphans as $orphan)
        {
            $singleOrphanOut = array('Order' => $order);
            $singleOrphanOut['ParentName'] = $orphan['parent_name'];
            $singleOrphanOut['ParentObjtypeId'] = $orphan['parent_objtype_id'];
            $singleOrphanOut['ChildName'] = $orphan['child_name'];
            $singleOrphanOut['ChildObjtypeId'] = $orphan['child_objtype_id'];
            $allObjectParsOut[] = $singleOrphanOut;
            $order = $nextorder[$order];
        }
        $mod->setOutput("AllObjectParsOrphans", $allObjectParsOut);
    }

    // check 4: relationships that violate ObjectParentCompat Rules
    $invalids = array ();
    $result = usePreparedSelectBlade
              (
                  'SELECT CO.id AS child_id, CO.objtype_id AS child_type_id, CD.dict_value AS child_type, CO.name AS child_name, ' .
                  'PO.id AS parent_id, PO.objtype_id AS parent_type_id, PD.dict_value AS parent_type, PO.name AS parent_name ' .
                  'FROM Object CO ' .
                  'LEFT JOIN EntityLink EL ON CO.id = EL.child_entity_id ' .
                  'LEFT JOIN Object PO ON EL.parent_entity_id = PO.id ' .
                  'LEFT JOIN ObjectParentCompat OPC ON PO.objtype_id = OPC.parent_objtype_id ' .
                  'LEFT JOIN Dictionary PD ON PO.objtype_id = PD.dict_key ' .
                  'LEFT JOIN Dictionary CD ON CO.objtype_id = CD.dict_key ' .
                  "WHERE EL.parent_entity_type = 'object' AND EL.child_entity_type = 'object' " .
                  'AND OPC.parent_objtype_id IS NULL'
              );
    $invalids = $result->fetchAll (PDO::FETCH_ASSOC);
    unset ($result);
    if (count ($invalids))
    {
        $mod->setOutput("ObjectParRuleViolation", true);
        $mod->setOutput("ObjectParRuleCount", count ($invalids));

        $violations = TRUE;
        $order = 'odd';
        $allObjectParRulesOut = array();
        foreach ($invalids as $invalid)
        {
            $singleOrphanOut = array('Order' => $order);
            $singleOrphanOut['ChildName'] = $invalid['child_name'];
            $singleOrphanOut['Child_type'] = $invalid['child_type'];
            $singleOrphanOut['ParentName'] = $invalid['parent_name'];
            $singleOrphanOut['ParentType'] = $invalid['parent_type'];
            $allObjectParRulesOut[] = $singleOrphanOut;
            $order = $nextorder[$order];
        }

        $mod->setOutput("AllObjectParRulesOrphans", $allObjectParRulesOut);
    }

    // check 5: Links that violate PortCompat Rules
    $invalids = array ();
    $result = usePreparedSelectBlade
              (
                  'SELECT OA.id AS obja_id, OA.name AS obja_name, L.porta AS porta_id, PA.name AS porta_name, POIA.oif_name AS porta_type, ' .
                  'OB.id AS objb_id, OB.name AS objb_name, L.portb AS portb_id, PB.name AS portb_name, POIB.oif_name AS portb_type ' .
                  'FROM Link L ' .
                  'LEFT JOIN Port PA ON L.porta = PA.id ' .
                  'LEFT JOIN Object OA ON PA.object_id = OA.id ' .
                  'LEFT JOIN PortOuterInterface POIA ON PA.type = POIA.id ' .
                  'LEFT JOIN Port PB ON L.portb = PB.id ' .
                  'LEFT JOIN Object OB ON PB.object_id = OB.id ' .
                  'LEFT JOIN PortOuterInterface POIB ON PB.type = POIB.id ' .
                  'LEFT JOIN PortCompat PC on PA.type = PC.type1 AND PB.type = PC.type2 ' .
                  'WHERE PC.type1 IS NULL OR PC.type2 IS NULL'
              );
    $invalids = $result->fetchAll (PDO::FETCH_ASSOC);
    unset ($result);
    if (count ($invalids))
    {
        $mod->setOutput("PortCompatRuleViolation", true);
        $mod->setOutput("PortCompatRuleCount", count ($invalids));

        $violations = TRUE;
        $order = 'odd';
        $allPortCompatRulesOut = array();
        foreach ($invalids as $invalid)
        {
            $singleOrphanOut = array('Order' => $order);
            $singleOrphanOut['ObjaName'] = $invalid['obja_name'];
            $singleOrphanOut['PortaName'] = $invalid['porta_name'];
            $singleOrphanOut['PortaType'] = $invalid['porta_type'];
            $singleOrphanOut['ObjbName'] = $invalid['objb_name'];
            $singleOrphanOut['PortbName'] = $invalid['portb_name'];
            $singleOrphanOut['PortbType'] = $invalid['portb_type'];

            $allPortCompatRulesOut[] = $singleOrphanOut;
            $order = $nextorder[$order];
        }
        echo "</table>\n";
        finishPortLet ();

        $mod->setOutput("AllPortCompatRulesOrphans", $allPortCompatRulesOut);
    }

    // check 6: TagStorage rows referencing non-existent parents
    $realms = array
              (
                  'file' => array ('table' => 'File', 'column' => 'id'),
                  'ipv4net' => array ('table' => 'IPv4Network', 'column' => 'id'),
                  'ipv4rspool' => array ('table' => 'IPv4RSPool', 'column' => 'id'),
                  'ipv4vs' => array ('table' => 'IPv4VS', 'column' => 'id'),
                  'ipv6net' => array ('table' => 'IPv6Network', 'column' => 'id'),
                  'ipvs' => array ('table' => 'VS', 'column' => 'id'),
                  'location' => array ('table' => 'Location', 'column' => 'id'),
                  'object' => array ('table' => 'RackObject', 'column' => 'id'),
                  'rack' => array ('table' => 'Rack', 'column' => 'id'),
                  'user' => array ('table' => 'UserAccount', 'column' => 'user_id'),
                  'vst' => array ('table' => 'VLANSwitchTemplate', 'column' => 'id'),
              );
    $orphans = array ();
    foreach ($realms as $realm => $details)
    {
        $result = usePreparedSelectBlade
                  (
                      'SELECT TS.*, TT.tag FROM TagStorage TS ' .
                      'LEFT JOIN TagTree TT ON TS.tag_id = TT.id ' .
                      "LEFT JOIN ${details['table']} ON TS.entity_id = ${details['table']}.${details['column']} " .
                      "WHERE TS.entity_realm = ? AND ${details['table']}.${details['column']} IS NULL",
                      array ($realm)
                  );
        $rows = $result->fetchAll (PDO::FETCH_ASSOC);
        unset ($result);
        $orphans = array_merge ($orphans, $rows);
    }
    if (count ($orphans))
    {
        $mod->setOutput("TagStorageViolation", true);
        $mod->setOutput("TagStorageCount", count ($orphans));

        $violations = TRUE;
        $order = 'odd';
        $allTagStoragesOut = array();

        foreach ($orphans as $orphan)
        {
            $realm_name = formatRealmName ($orphan['entity_realm']);
            $singleOrphanOut = array('Order' => $order);
            $singleOrphanOut['RealmName'] = $realm_name;
            $singleOrphanOut['Tag'] = $orphan['tag'];
            $singleOrphanOut['EntityId'] = $orphan['entity_id'];

            $allTagStoragesOut[] = $singleOrphanOut;
            $order = $nextorder[$order];
        }
        finishPortLet ();
        $mod->setOutput("AllTagStoragesOrphans", $allTagStoragesOut);
    }

    // check 7: FileLink rows referencing non-existent parents
    // re-use the realms list from the TagStorage check, with a few mods
    unset ($realms['file'], $realms['vst']);
    $realms['row'] = array ('table' => 'Row', 'column' => 'id');
    $orphans = array ();
    foreach ($realms as $realm => $details)
    {
        $result = usePreparedSelectBlade
                  (
                      'SELECT FL.*, F.name FROM FileLink FL ' .
                      'LEFT JOIN File F ON FL.file_id = F.id ' .
                      "LEFT JOIN ${details['table']} ON FL.entity_id = ${details['table']}.${details['column']} " .
                      "WHERE FL.entity_type = ? AND ${details['table']}.${details['column']} IS NULL",
                      array ($realm)
                  );
        $rows = $result->fetchAll (PDO::FETCH_ASSOC);
        unset ($result);
        $orphans = array_merge ($orphans, $rows);
    }
    if (count ($orphans))
    {
        $mod->setOutput("FileLinkViolation", true);
        $mod->setOutput("FileLinkCount", count ($orphans));

        $violations = TRUE;
        $order = 'odd';
        $allFileLinksOut = array();
        foreach ($orphans as $orphan)
        {
            $realm_name = formatRealmName ($orphan['entity_type']);
            $singleOrphanOut = array('Order' => $order);
            $singleOrphanOut['Name'] = $orphan['name'];
            $singleOrphanOut['RealmName'] = $realm_name;
            $singleOrphanOut['EntityId'] = $orphan['entity_id'];

            $allFileLinksOut[] = $singleOrphanOut;
            $order = $nextorder[$order];
        }
        $mod->setOutput("AllFileLinksOrphans", $allFileLinksOut);
    }

    // check 8: missing triggers
    $triggers= array
               (
                   'Link-before-insert' => 'Link',
                   'Link-before-update' => 'Link'
               );
    $result = usePreparedSelectBlade
              (
                  'SELECT TRIGGER_NAME, EVENT_OBJECT_TABLE ' .
                  'FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = SCHEMA()'
              );
    $rows = $result->fetchAll (PDO::FETCH_ASSOC);
    unset ($result);
    $existing_triggers = $missing_triggers = array ();
    foreach ($rows as $row)
        $existing_triggers[$row['TRIGGER_NAME']] = $row['EVENT_OBJECT_TABLE'];
    foreach ($triggers as $trigger => $table)
        if (! array_key_exists ($trigger, $existing_triggers))
            $missing_triggers[$trigger] = $table;
    if (count ($missing_triggers))
    {
        $violations = TRUE;
        $mod->setOutput('MissingTriggers', count ($missing_triggers));
        $order = 'odd';
        $allTriggersOut = array();
        foreach ($missing_triggers as $trigger => $table)
        {
            $allTriggersOut[] = array('Order' => $order,
                                      'Table' => $table,
                                      'Trigger' => $trigger);
            $order = $nextorder[$order];
        }
        $mod->setOutput('AllTriggers', $allTriggersOut);
    }

    // check 9: missing foreign keys
    $fkeys= array
            (
                'Atom-FK-molecule_id' => 'Atom',
                'Atom-FK-rack_id' => 'Atom',
                'AttributeMap-FK-chapter_id' => 'AttributeMap',
                'AttributeMap-FK-attr_id' => 'AttributeMap',
                'AttributeValue-FK-map' => 'AttributeValue',
                'AttributeValue-FK-object' => 'AttributeValue',
                'CachedPAV-FK-object-port' => 'CachedPAV',
                'CachedPAV-FK-vlan_id' => 'CachedPAV',
                'CachedPNV-FK-compound' => 'CachedPNV',
                'CachedPVM-FK-object_id' => 'CachedPVM',
                'CactiGraph-FK-server_id' => 'CactiGraph',
                'CactiGraph-FK-server_id' => 'CactiGraph',
                'Dictionary-FK-chapter_id' => 'Dictionary',
                'FileLink-File_fkey' => 'FileLink',
                'IPv4Allocation-FK-object_id' => 'IPv4Allocation',
                'IPv4LB-FK-vs_id' => 'IPv4LB',
                'IPv4LB-FK-object_id' => 'IPv4LB',
                'IPv4LB-FK-rspool_id' => 'IPv4LB',
                'IPv4NAT-FK-object_id' => 'IPv4NAT',
                'IPv4RS-FK' => 'IPv4RS',
                'IPv6Allocation-FK-object_id' => 'IPv6Allocation',
                'Link-FK-a' => 'Link',
                'Link-FK-b' => 'Link',
                'MountOperation-FK-object_id' => 'MountOperation',
                'MountOperation-FK-old_molecule_id' => 'MountOperation',
                'MountOperation-FK-new_molecule_id' => 'MountOperation',
                'MuninGraph-FK-server_id' => 'MuninGraph',
                'MuninGraph-FK-server_id' => 'MuninGraph',
                'ObjectHistory-FK-object_id' => 'ObjectHistory',
                'ObjectLog-FK-object_id' => 'ObjectLog',
                'Port-FK-iif-oif' => 'Port',
                'Port-FK-object_id' => 'Port',
                'PortAllowedVLAN-FK-object-port' => 'PortAllowedVLAN',
                'PortAllowedVLAN-FK-vlan_id' => 'PortAllowedVLAN',
                'PortCompat-FK-oif_id1' => 'PortCompat',
                'PortCompat-FK-oif_id2' => 'PortCompat',
                'PortInterfaceCompat-FK-iif_id' => 'PortInterfaceCompat',
                'PortInterfaceCompat-FK-oif_id' => 'PortInterfaceCompat',
                'PortLog_ibfk_1' => 'PortLog',
                'PortNativeVLAN-FK-compound' => 'PortNativeVLAN',
                'PortVLANMode-FK-object-port' => 'PortVLANMode',
                'RackSpace-FK-rack_id' => 'RackSpace',
                'RackSpace-FK-object_id' => 'RackSpace',
                'TagStorage-FK-TagTree' => 'TagStorage',
                'TagTree-K-parent_id' => 'TagTree',
                'UserConfig-FK-varname' => 'UserConfig',
                'VLANDescription-FK-domain_id' => 'VLANDescription',
                'VLANDescription-FK-vlan_id' => 'VLANDescription',
                'VLANIPv4-FK-compound' => 'VLANIPv4',
                'VLANIPv4-FK-ipv4net_id' => 'VLANIPv4',
                'VLANIPv6-FK-compound' => 'VLANIPv6',
                'VLANIPv6-FK-ipv6net_id' => 'VLANIPv6',
                'VLANSTRule-FK-vst_id' => 'VLANSTRule',
                'VLANSwitch-FK-domain_id' => 'VLANSwitch',
                'VLANSwitch-FK-object_id' => 'VLANSwitch',
                'VLANSwitch-FK-template_id' => 'VLANSwitch',
                'VSEnabledIPs-FK-object_id' => 'VSEnabledIPs',
                'VSEnabledIPs-FK-rspool_id' => 'VSEnabledIPs',
                'VSEnabledIPs-FK-vs_id-vip' => 'VSEnabledIPs',
                'VSEnabledPorts-FK-object_id' => 'VSEnabledPorts',
                'VSEnabledPorts-FK-rspool_id' => 'VSEnabledPorts',
                'VSEnabledPorts-FK-vs_id-proto-vport' => 'VSEnabledPorts',
                'VSIPs-vs_id' => 'VSIPs',
                'VS-vs_id' => 'VSPorts'
            );
    $result = usePreparedSelectBlade
              (
                  'SELECT CONSTRAINT_NAME, TABLE_NAME ' .
                  'FROM information_schema.TABLE_CONSTRAINTS ' .
                  "WHERE CONSTRAINT_SCHEMA = SCHEMA() AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
              );
    $rows = $result->fetchAll (PDO::FETCH_ASSOC);
    unset ($result);
    $existing_fkeys = $missing_fkeys = array ();
    foreach ($rows as $row)
        $existing_fkeys[$row['CONSTRAINT_NAME']] = $row['TABLE_NAME'];
    foreach ($fkeys as $fkey => $table)
        if (! array_key_exists ($fkey, $existing_fkeys))
            $missing_fkeys[$fkey] = $table;
    if (count ($missing_fkeys))
    {
        $mod->setOutput('MissingKeys', count ($missing_fkeys));
        $violations = TRUE;

        $order = 'odd';
        $allKeysOut = array();
        foreach ($missing_fkeys as $fkey => $table)
        {
            $allKeysOut[] = array('Order' => $order,
                                  'Table' => $table,
                                  'FKey' => $fkey);
            $order = $nextorder[$order];
        }
        $mod->setOutput('AllKeys', $allKeysOut);
    }

    // check 10: circular references
    //     - all affected members of the tree are displayed
    //     - it would be beneficial to only display the offending records
    // check 10.1: locations
    $invalids = array ();
    $locations = listCells ('location');
    foreach ($locations as $location)
    {
        try
        {
            $children = getLocationChildrenList ($location['id']);
        }
        catch (RackTablesError $e)
        {
            $invalids[] = $location;
        }
    }
    if (count ($invalids))
    {
        $violations = TRUE;
        $mod->setOutput('Invalids', count ($invalids));
        
        $order = 'odd';
        $allInvalidsOut = array();
        foreach ($invalids as $invalid)
        {
            $allInvalidsOut[] = array('Order' => $order,
                                      'Id' => $invalid['id'],
                                      'Name' => $invalid['name'],
                                      'Parent_Id' => $invalid['parent_id'],
                                      'Parent_Name' => $invalid['parent_name']);
            $order = $nextorder[$order];
        }
        $mod->setOutput('AllInvalids', $allInvalidsOut);
    }

    // check 10.2: objects
    $invalids = array ();
    $objects = listCells ('object');
    foreach ($objects as $object)
    {
        try
        {
            $children = getObjectContentsList ($object['id']);
        }
        catch (RackTablesError $e)
        {
            $invalids[] = $object;
        }
    }
    if (count ($invalids))
    {
        $violations = TRUE;
        $mod->setOutput('InvalidObjs', count ($invalids));
        
        $order = 'odd';
        $allInvalidObjsOut = array();
        foreach ($invalids as $invalid)
        {
            $allInvalidObjsOut[] = array('Order' => $order,
                                         'Id' => $invalid['id'],
                                         'Name' => $invalid['name'],
                                         'Parent_Id' => $invalid['parent_id'],
                                         'Parent_Name' => $invalid['parent_name']);
            $order = $nextorder[$order];
        }
        $mod->setOutput('AllInvalidObjs', $allInvalidObjsOut);
    }

    // check 10.3: tags
    $invalids = array ();
    $tags = getTagList ();
    foreach ($tags as $tag)
    {
        try
        {
            $children = getTagChildrenList ($tag['id']);
        }
        catch (RackTablesError $e)
        {
            $invalids[] = $tag;
        }
    }
    if (count ($invalids))
    {
        $violations = TRUE;
        $mod->setOutput('InvalidTags', count ($invalids));
        
        $order = 'odd';
        $allInvalidTagsOut = array();
        foreach ($invalids as $invalid)
        {
            $allInvalidTagsOut[] = array('Order' => $order,
                                         'Id' => $invalid['id'],
                                         'Tag' => $invalid['tag'],
                                         'Parent_Id' => $invalid['parent_id'],
                                         'Parent_Tag' => $tags[$invalid['parent_id']]['tag']);
            $order = $nextorder[$order];
        }
        $mod->setOutput('AllInvalidTags', $allInvalidTagsOut);
    }

    if (! $violations)
        $mod->setOutput("NoViolations", true);
}


function renderUserProperties ($user_id)
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderUserProperties");
    printTagsPicker (null, $mod, 'TagsPicker');
}

function getPatchCableHeapCursorCode ($heap, $zoom_heap_id)
{
    $tplm = TemplateManager::getInstance();
    global $pageno, $tabno;
    if ($heap['logc'] == 0)
        return '&nbsp;';
    $linkparams = array
                  (
                      'page' => $pageno,
                      'tab' => $tabno,
                  );
    if ($heap['id'] == $zoom_heap_id)
    {
        $imagename = 'Zooming';
        $imagetext = 'hide event log';
    }
    else
    {
        $imagename = 'Zoom';
        $imagetext = 'display event log';
        $linkparams['zoom_heap_id'] = $heap['id'];
    }
    return $tplm->generateModule('MkAInMemory', true, array('link' => makeHref ($linkparams),
                                 'text' => getImageHREF ($imagename, $imagetext)))->run();
}

function renderPatchCableHeapSummary()
{
    $summary = getPatchCableHeapSummary();
    if (! count ($summary))
        return;
    global $nextorder;
    $order = 'odd';

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule("Payload", "RenderPatchCableHeapSummary");
    $mod->setNamespace('cables');

    $zoom_heap_id = array_key_exists ('zoom_heap_id', $_REQUEST) ? genericAssertion ('zoom_heap_id', 'uint') : NULL;
    $allHeapsOut = array();
    foreach ($summary as $heap)
    {
        $allHeapsOut[] = array('Order' => $order,
                               'HeapAmount' => $heap['amount'],
                               'HeapEndCon1' => $heap['end1_connector'],
                               'HeapPCType' => $heap['pctype'],
                               'HeapEndCon2' => $heap['end2_connector'],
                               'HeapLength' => $heap['length'],
                               'HeapDesc' => $heap['description'],
                               'HeapPatchCalbeLength' => getPatchCableHeapCursorCode ($heap, $zoom_heap_id));

        $order = $nextorder[$order];
    }
    $mod->setOutput('AllHeaps', $allHeapsOut);

    if ($zoom_heap_id === NULL)
        return;
    if (! count ($eventlog = getPatchCableHeapLogEntries ($zoom_heap_id)))
        return;
    $mod->setOutput('ZoomOrEventLog', true);

    $order = 'odd';
    $allEventsOut = array();
    foreach ($eventlog as $event)
    {
        $allEventsOut[] = array('Order' => $order,
                                'EventDate' => $event['date'],
                                'EventUser' => niftyString ($event['user'], 255),
                                'EventMessage' => niftyString ($event['message'], 255));
        $order = $nextorder[$order];
    }
    $mod->setOutput('AllEvent', $allEventsOut);
}

function renderPatchCableHeapEditor()
{
    function printNewitemTR($parent, $placeholder)
    {
        $tplm = TemplateManager::getInstance();
        $mod = $tplm->generateSubmodule($placeholder, "RenderPatchCableHeapEditor_NewItem", $parent);
        $mod->setNamespace('cables');

        $mod->setOutput('Connector1Opt', getSelect (getPatchCableConnectorOptions(), array ('name' => 'end1_conn_id', 'tabindex' => 110)));
        $mod->setOutput('TypeOpt', getSelect (getPatchCableTypeOptions(), array ('name' => 'pctype_id', 'tabindex' => 120)));
        $mod->setOutput('Connector2Opt', getSelect (getPatchCableConnectorOptions(), array ('name' => 'end2_conn_id', 'tabindex' => 130)));
    }
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', "RenderPatchCableHeapEditor");
    $mod->setNamespace('cables');
    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        printNewitemTR($mod, 'AddNewTop');

    $allHeapsOut = array();
    foreach (getPatchCableHeapSummary() as $heap)
    {
        $allHeapsOut[] = array('HeapId' => $heap['id'],
                               'HeapAmount' => $heap['amount'],
                               'HeapLength' => $heap['length'],
                               'HeapString' => niftyString ($heap['description'], 255),
                               'EndCon1_Select' => getSelect (getPatchCableConnectorOptions(), array ('name' => 'end1_conn_id'), $heap['end1_conn_id']),
                               'PCType_Select' => getSelect (getPatchCableTypeOptions(), array ('name' => 'pctype_id'), $heap['pctype_id']),
                               'EndCon2_Select' => getSelect (getPatchCableConnectorOptions(), array ('name' => 'end2_conn_id'), $heap['end2_conn_id']));
    }
    $mod->setOutput('AllHeaps', $allHeapsOut);
    if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
        printNewitemTR($mod, 'AddNewBottom');
}

function renderPatchCableHeapAmount()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', "RenderPatchCableHeapAmount");
    $mod->setNamespace('cables');
    $allHeapsOut = array();
    foreach (getPatchCableHeapSummary() as $heap)
    {
        $allHeapsOut[] = array('HeapId' => $heap['id'],
                               'HeapAmount' => $heap['amount'],
                               'HeapLength' => $heap['length'],
                               'HeapString' => niftyString ($heap['description'], 255),
                               'EndCon1_String' => niftyString ($heap['end1_connector'], 32),
                               'PCType_String' => niftyString ($heap['pctype'], 255),
                               'EndCon2_String' => niftyString ($heap['end2_connector'], 32));
    }
    $mod->setOutput('AllHeaps', $allHeapsOut);
}

function renderSimpleTableWithOriginViewer ($rows, $column, $parent = null, $placeholder = 'SimpleTableWithOriginViewer')
{
    $tplm = TemplateManager::getInstance();
    if($parent == null)
        $mod = $tplm->generateModule("RenderSimpleTableWithOriginViewer");
    else
        $mod = $tplm->generateSubmodule('Payload', "RenderSimpleTableWithOriginViewer", $parent);

    $mod->setNamespace('cables');
    $mod->setOutput('ColHeader', $column['header']);
    $allRowsOut = array();
    foreach ($rows as $row)
    {
        $allRowsOut[] = array('OriginIsDefault' => ($row['origin'] == 'default'),
                              'RowColumnKey' => $row[$column['key']],
                              'RowString' => niftyString ($row[$column['value']], $column['width']));
    }
    $mod->setOutput('AllRows', $allRowsOut);
    if($parent == null)
        return $mod->run();
}

function renderSimpleTableWithOriginEditor ($rows, $column, $parent = null, $placeholder = 'SimpleTableWithOriginEditor')
{
    $tplm = TemplateManager::getInstance();
    if($parent == null)
        $mod = $tplm->generateModule("RenderSimpleTableWithOriginEditor");
    else
        $mod = $tplm->generateSubmodule($placeholder, "RenderSimpleTableWithOriginEditor", $parent);

    $mod->setNamespace('cables');
    $mod->setOutput('ColHeader', $column['header']);

    $mod->setOutput('ColumnWidth', $column['width']);
    $mod->setOutput('ColumnValue', $column['value']);

    if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
        $mod->addOutput('NewTop', true);

    $allRowsOut = array();
    foreach ($rows as $row)
    {
        $singleRow = array('OriginIsDefault' => ($row['origin'] == 'default'),
                           'RowValueWidth' => niftyString ($row[$column['value']], $column['width']));
        if($row['origin'] != 'default')
        {
            $singleRow['Key'] = $column['key'];
            $singleRow['ColumnKey'] = $row[$column['key']];
            $singleRow['Width'] = $column['width'];
            $singleRow['Value'] = $column['value'];
        }
        $allRowsOut[] = $singleRow;
    }
    $mod->setOutput('AllRows', $allRowsOut);

    if($parent == null)
        return $mod->run();
}

function renderPatchCableConfiguration()
{
    global $nextorder;

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', "RenderPatchCableConfiguration");
    $mod->setNamespace('cableconf');


    renderSimpleTableWithOriginViewer
    (
        getPatchCableConnectorList(),
        array
        (
            'header' => 'Connector',
            'key' => 'id',
            'value' => 'connector',
            'width' => 32,
        ), $mod, 'ConnectorViewer'
    );

    renderTwoColumnCompatTableViewer
    (
        getPatchCableConnectorCompat(),
        array
        (
            'header' => 'Cable type',
            'key' => 'pctype_id',
            'value' => 'pctype',
            'width' => 64,
        ),
        array
        (
            'header' => 'Connector',
            'key' => 'connector_id',
            'value' => 'connector',
            'width' => 32,
        ), $mod, 'CompViewr'
    );


    renderSimpleTableWithOriginViewer
    (
        getPatchCableTypeList(),
        array
        (
            'header' => 'Cable type',
            'key' => 'id',
            'value' => 'pctype',
            'width' => 64,
        ), $mod, 'TypeViewer'
    );

    renderTwoColumnCompatTableViewer
    (
        getPatchCableOIFCompat(),
        array
        (
            'header' => 'Cable type',
            'key' => 'pctype_id',
            'value' => 'pctype',
            'width' => 64,
        ),
        array
        (
            'header' => 'Outer interface',
            'key' => 'oif_id',
            'value' => 'oif_name',
            'width' => 48,
        ), $mod, 'InterfacesViewer'
    );

}

function renderPatchCableConnectorEditor()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', "PatchCableEditor", null, true);

    renderSimpleTableWithOriginEditor
    (
        getPatchCableConnectorList(),
        array
        (
            'header' => 'Connector',
            'key' => 'id',
            'value' => 'connector',
            'width' => 32,
        ), $mod, 'CableEditor'
    );
}

function renderPatchCableTypeEditor()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', "PatchCableEditor", null, true);
    renderSimpleTableWithOriginEditor
    (
        getPatchCableTypeList(),
        array
        (
            'header' => 'Cable type',
            'key' => 'id',
            'value' => 'pctype',
            'width' => 64,
        ), $mod, 'CableEditor'
    );
}

function renderPatchCableConnectorCompatEditor()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', "PatchCableEditor", null, true);
    renderTwoColumnCompatTableEditor
    (
        getPatchCableConnectorCompat(),
        array
        (
            'header' => 'Cable type',
            'key' => 'pctype_id',
            'value' => 'pctype',
            'width' => 64,
            'options' => getPatchCableTypeOptions(),
        ),
        array
        (
            'header' => 'Connector',
            'key' => 'connector_id',
            'value' => 'connector',
            'width' => 32,
            'options' => getPatchCableConnectorOptions()
        ), $mod, 'CableEditor'
    );
}

function renderPatchCableOIFCompatEditor()
{
    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateSubmodule('Payload', "PatchCableEditor", null, true);
    renderTwoColumnCompatTableEditor
    (
        getPatchCableOIFCompat(),
        array
        (
            'header' => 'Cable type',
            'key' => 'pctype_id',
            'value' => 'pctype',
            'width' => 64,
            'options' => getPatchCableTypeOptions(),
        ),
        array
        (
            'header' => 'Outer interface',
            'key' => 'oif_id',
            'value' => 'oif_name',
            'width' => 48,
            'options' => getPortOIFOptions()
        ), $mod, 'CableEditor'
    );
}

?>
