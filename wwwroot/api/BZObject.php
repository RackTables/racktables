<?php
include ('DALObject.php');

class BZObject {
    static $debug_mode = False;
    //input : lable => object status name
    //output : array()
    public static function getSummary($label) {
        $_arg = 'Object Status';
        if (! empty($label))
             $_arg = $label;
        $_output = DALObject::getSummaryObjects($_arg);
        require_once('DALIP.php');
        $_output['IPs'] = DALIP::getSummaryIPs();
        require_once('DALSpace.php');
        $_output['Space'] = DALSpace::getSummarySpace();
        return $_output;
    }

    // parameter : Integer
    public static function getObjectDetail($assetId) {
        $ret = array();
        list($check,$data) = BZObject::getRackDataByAssetId($assetId);
        //print_r($data);
        if ($check) {
            $id = NULL;
            foreach($data as $asset_id => $objInfo) {
                $id = $objInfo['id'];
                $ret = $objInfo;
            }

            $containers = BZObject::common_containers_detail($id,NULL);
            if (!empty($containers)) {
                $ret['children'] = $containers['children'];
                $ret['parents'] = $containers['parents'];
                $ret['asset_keys'] = $containers['asset_keys'];
            }

            $ret['nat4'] = getNATv4ForObject($id);
            $ret['files'] = getFilesOfEntity('object', $id);

            $ret['ips'] = BZObject::common_network_detail($id);
        }
        return $ret;
    }

    //=========================================================
    //=======================  FiMo API  ======================
    //=========================================================
    public static function getObjectIdByAssetId($type,$assetId) {
       $ret = DALObject::getObjectIdByAssetId($type,$assetId);
       //print_r($ret);
       return $ret;
    }

    public static function getRackIdByAssetId($type,$assetId) {
        //echo "parameter: {$type},{$assetId}\n";
        $ret = DALObject::getRackIdByAssetId($type,$assetId);
        //print_r($ret);
        return $ret;
    }

    public static function setPropertyValue($objId,$attrId,$attrValue) {
        commitUpdateAttrValue($objId, $attrId, $attrValue);
        return array(True,'commit Attr('.$attrId.') value');
    }

    //internal call function
    public static function getPropertys($assetId) {
        //echo "parameter: {$type},{$assetId}\n";
        $check_result = False;
        $data = DALObject::getObjectIdByAssetId(NULL,$assetId);
        if (count($data)>0) {
            $check_result = True;
            $ret = DALObject::getObjectPropertys(intval($data[0]['object_id']));
        } else {
            $ret = "object not found";
        }
        //print_r($ret);
        return array($check_result,$ret);
    }

    public static function getHostLinkedAssetList($ids) {
        //echo "parameter: {$ids}\n";
        $check_result = False;
        $tmpIds = array();
        if (strpos($ids, ',') !== false) {
            $tmpIds = explode(",",$ids);
        } else {
            $tmpIds = array($ids);
        }
        if (count($tmpIds) == 0) array($check_result," parameter invalid");

        $assetIds = "'".implode("','",$tmpIds)."'";
        $data = DALObject::getObjectIdByAssetId(NULL,$assetIds,True);
        if (count($data)>0) {
            $check_result = True;;
        } else
            $data = array();
        return array(true,$data);
    }

    //wrapper FiMo getAssetsSummary
    public static function getRackDataByAssetId($ids) {
        //echo "parameter: {$ids}\n";
        $check_result = False;
        $tmpIds = array();
        if (strpos($ids, ',') !== false) {
            $tmpIds = explode(",",$ids);
        } else {
            $tmpIds = array($ids);
        }
        if (count($tmpIds) == 0) array($check_result," parameter invalid");

        $assetIds = "'".implode("','",$tmpIds)."'";
        $data = DALObject::getObjectRackData('asset',$assetIds);
        $objIdArr = array_keys($data);
        if (count($objIdArr)>0) {
            require_once('DALSpace.php');
            $objIds = "'".implode("','",$objIdArr)."'";
            $objProps = DALObject::getObjectPropertys($objIds,True);
            $ret = array();
            $locData = DALSpace::getLocationDataAll();
            foreach($data as $objId => $objData) {
                $assetId = $objData['assetId'];
                if (!empty($assetId)) {
                    //$ret[$assetId] = $objData;
                    //$ret[$assetId] = $objProps[$objId];
                    $ret[$assetId] = array_merge($objData, $objProps[$objId]);
                    //get location tree data;
                    $ret[$assetId]['locations'] = BZObject::common_location_tree($objData['location_id'],$locData);
                    unset($ret[$assetId]['location_id']);
                    unset($ret[$assetId]['location_name']);
                }
            }
            $check_result = True;
        } else {
            $ret = "object not found";
        }
        //print_r($ret);
        return array($check_result,$ret);
    }

    //internal call function
    public static function getPortLinks($linkData,&$objData,&$typeData,&$assetIdsData) {
        $ret = array();
        foreach($linkData as $link) {
            $objtname = NULL;
            $data = $link;
            $objtid = $link['objtype_id'];
            if (isset($typeData[$objtid]))
                $objtname = $typeData[$objtid];

            $remotetid = $link['remote_object_tid'];
            $remotetname = NULL;
            if (isset($typeData[$remotetid]))
                $remotetname = $typeData[$remotetid];

            $objinfo = array();
            $objid = $link['object_id'];
            if (isset($objData[$objid]))
                $objinfo = $objData[$objid];

            unset($objinfo['object_name']);

            $remoteinfo = array();
            $remoteid = $link['remote_object_id'];
            if (isset($objData[$remoteid]))
                $remoteinfo = $objData[$remoteid];

            unset($remoteinfo['object_name']);

            $newobject = array('id' => intval($objid),
                                'name' => $link['object_name'],
                                'host_name' => "",
                                'host_id' => "",
                                'asset_id' => NULL,
                                'type_id' => intval($link['objtype_id']),
                                'label' => NULL,
                                'location_id' => NULL,
                                'location_name' => "",
                                'rack_id' => NULL,
                                'rack_name' => "",
                                'row_id' => NULL,
                                'row_name' => "",
                                'units' => array(),
                                'type_name' => $objtname);

            if (isset($assetIdsData[$objid]))
                $newobject['asset_id'] = $assetIdsData[$objid];

            $data['object'] = array_merge($newobject, $objinfo);

            $newremote_object = array('id' => intval($remoteid),
                                      'name' => $link['remote_object_name'],
                                      'host_name' => "",
                                      'host_id' => "",
                                      'asset_id' => NULL,
                                      'type_id' => intval($link['remote_object_tid']),
                                      'label' => NULL,
                                      'location_id' => NULL,
                                      'location_name' => "",
                                      'rack_id' => NULL,
                                      'rack_name' => "",
                                      'row_id' => NULL,
                                      'row_name' => "",
                                      'units' => array(),
                                      'type_name' => $remotetname);

            if (isset($assetIdsData[$remoteid]))
                $newremote_object['asset_id'] = $assetIdsData[$remoteid];


            $data['remote_object'] = array_merge($newremote_object,  $remoteinfo);

            $data['ip4'] = NULL;
            unset($data['object_id']);
            unset($data['object_name']);
            unset($data['objtype_id']);
            unset($data['remote_object_id']);
            unset($data['remote_object_name']);
            unset($data['remote_object_tid']);
            unset($data['log_count']);
            unset($data['user']);
            unset($data['time']);
            $ret[] = $data;
        }
        return $ret;
    }

    //wrapper FiMo getTopologyByAssetId
    public static function getTopologyByAssetId($asset_id) {
        $data = DALObject::getObjectRackData('asset',$asset_id,TRUE);
        $object_data = array();
        if(! empty($data)) {
            $object_data = $data[0];
            $object_data['id'] = intval($object_data['object_id']);
            unset($object_data['object_id']);
            return BZObject::getTopologyData($object_data);
        }
        return array(True,$data);
    }

    //wrapper FiMo getTopologyByObjectId
    public static function getTopologyByObjectId($object_id) {
        $data = DALObject::getObjectRackData('object',$object_id,TRUE);
       // print_r($data);
        $object_data = array();
        if(! empty($data)) {
            $object_data = $data[0];
            $object_data['id'] = intval($object_data['object_id']);
            unset($object_data['object_id']);
            return BZObject::getTopologyData($object_data);
        }
        return array(True,$data);
    }

    //internal call function
    public static function getTopologyData($object_data) {
        $check_result = False;
        $ret = $object_data;
        if (isset($object_data['id'])) {
            $object_id = $object_data['id'];
            $objectTypes = array();
            $ObjectIds = array();
            $assetIdsData = array();
            //echo "getLinkData id:{$object_id}\n";
            $linkData = DALObject::getLinkData($object_id);
            //echo "getLinkData after\n";
            $index = 0;
            $allObjectIds[] =  $object_id;
            //echo "local:".$object_id. "\n";

            foreach($linkData as $link) {
                $tid = intval($link['objtype_id']);
                if (! in_array($tid,$objectTypes))
                    $objectTypes[] = $link['objtype_id'];

                $tid = intval($link['remote_object_tid']);
                if (! in_array($tid,$objectTypes))
                    $objectTypes[] = $tid;

                $oid = $link['object_id'];
                if (! in_array($oid,$ObjectIds))
                    $ObjectIds[] = $oid;

                $oid = $link['remote_object_id'];
                if (! in_array($oid,$ObjectIds))
                    $ObjectIds[] = $oid;

                $allObjectIds[] = $link['object_id'];
                $allObjectIds[] = $link['remote_object_id'];
               // echo "object_id:". $link['object_id']. "\n";
                //echo "remote_object_id:".$link['remote_object_id']. "\n";

            }

            //echo "getTopologyByAssetId init before\n";
            $info = spotEntity ('object', $object_id);
            amplifyCell ($info);
            $ret['name'] = $info['name'];
            $ret['label'] = $info['label'];
            $ret['type_id'] = intval($info['objtype_id']);
            $ret['type_name'] = decodeObjectType ($info['objtype_id']);
            //echo "getParents before\n";
            $parents = getParents ($info, 'object');
           // echo "getParents after\n";
            $children = getChildren ($info, 'object');
            //print_r($children);
            // lookup the human-readable object type, sort by it
            foreach ($parents as $parent_id => $parent) {
                $parents[$parent_id]['object_type'] = decodeObjectType ($parent['objtype_id']);
                $objectTypes[] = $parent['objtype_id'];
                $ObjectIds[] = $parent['id'];
                $allObjectIds[] = $parent['id'];
               // echo "parent:". $parent['id']. "\n";
            }

            foreach ($children as $children_id => $child) {
                if (! empty($child['rack_id']))
                    $ObjectIds[] = $child['id'];
                $objectTypes[] = $child['objtype_id'];
                $allObjectIds[] = $child['id'];
                //echo "child:". $child['id'] . "\n";
            }

            //print_r($ObjectIds);
            //query rack info &&  object assetid

            //echo "getObjectRackData before\n";
            if (count($ObjectIds)>0) {
                $ObjectIds = array_filter($ObjectIds);
                $ids = implode(",",$ObjectIds);
                //echo trim($ids,",");
                $objData = DALObject::getObjectRackData('object',trim($ids,","));
            }
           // echo "getObjectRackData after\n";

            //echo "allObjectIds before\n";
            $asset_keys = array();
            if (count($allObjectIds)>0) {
                $allObjectIds = array_filter($allObjectIds);
                $allObjectIds = array_unique($allObjectIds);
                $ids = implode(",",$allObjectIds);
                //echo $ids . "\n";
                $assetIdsData = DALObject::getAssetIdByObject(trim($ids,","),False);
                foreach($assetIdsData as $objId => $assetId)
                    if (! empty($assetId))
                        $asset_keys[] = $assetId;
            }

           // echo "getObjectTypeSet before\n";
            $typeData = array();
            if (count($objectTypes)>0) {
                $objectTypes = array_filter($objectTypes);
                $ids = implode(",",$objectTypes);
                $typeData = DALObject::getObjectTypeSet(trim($ids,","));
            }


            $ret['asset_keys'] = $asset_keys;
            $ret['asset_id'] = NULL;
            if (isset($assetIdsData[$object_id]))
                $ret['asset_id'] = $assetIdsData[$object_id];

            //echo "groupBy before\n";
            $grouped_parents = groupBy ($parents, 'object_type');
            ksort ($grouped_parents);
            $ret['parents'] = array();
            foreach ($grouped_parents as $parents_group)
            {

                uasort ($parents_group, 'compare_name');
                $fmt_parents = array();
                foreach ($parents_group as $parent) {
                    $oid = $parent['id'];
                    $objtype_id = $parent['objtype_id'];
                    $temp_data = array('id' => intval($oid),
                                  'name' => $parent['name'],
                                  'label' => $parent['label'],
                                  'asset_id' => NULL,
                                  'type_id' => intval($objtype_id),
                                  'rack_id' => intval($parent['rack_id']),
                                  'host_name' => "",
                                  'host_id' => "",
                                  'ports' => array(),
                                  'rack_name' => "",                 //default
                                  'units' => array());               //default
                    if (isset($assetIdsData[$oid]))
                        $temp_data['asset_id'] = $assetIdsData[$oid];

                    if (!empty($oid)) {
                        $parentLinkData = DALObject::getLinkData($oid);
                        $temp_data['ports'] = BZObject::getPortLinks($parentLinkData,$objData,$typeData,$assetIdsData);
                    }

                    unset($temp_data['object_name']);
                    if (isset($objData[$oid]))
                        $fmt_parents[decodeObjectType ($objtype_id)][] = array_merge($temp_data, $objData[$oid]);
                    else
                        $fmt_parents[decodeObjectType ($objtype_id)][] = $temp_data;
                }
                $ret['parents'] = $fmt_parents;
            }
            $ret['children'] = array();
            foreach (groupBy ($children, 'objtype_id') as $objtype_id => $children_group)
            {
                uasort ($children_group, 'compare_name');
                $fmt_children = array();
                foreach ($children_group as $child) {
                    $oid = $child['id'];
                    $temp_data = array('id' => intval($oid),
                                       'name' => $child['name'],
                                       'label' => $child['label'],
                                       'type_id' => intval($child['objtype_id']),
                                       'rack_id' => intval($child['rack_id']),
                                       'asset_id' => NULL,
                                       'host_name' => "",
                                       'host_id' => "",
                                       'ports' => array(),
                                       'rack_name' => "",                 //default
                                       'units' => array());               //default
                    if (isset($assetIdsData[$oid]))
                        $temp_data['asset_id'] = $assetIdsData[$oid];

                    if (!empty($oid)) {
                        $childLinkData = DALObject::getLinkData($oid);
                        $temp_data['ports'] = BZObject::getPortLinks($childLinkData,$objData,$typeData,$assetIdsData);
                    }

                    unset($temp_data['object_name']);
                    if (isset($objData[$oid]))
                        $fmt_parents[] = array_merge($temp_data, $objData[$oid]);
                    else
                        $fmt_children[] = $temp_data;
                }
                $ret['children'][decodeObjectType ($objtype_id)] = $fmt_children;
            }
            $check_result = True;
            //echo "getPortLinks before\n";
            $ret['ports'] =  BZObject::getPortLinks($linkData,$objData,$typeData,$assetIdsData);
           // echo "getPortLinks after\n";

        }
        return array($check_result,$ret);
    }

    //###### v2 base api ######
    public static function get_all_list($keyword,$filter,$sort,$start,$limit) {
        $ret = array();
        //echo "sort:" . $sort;
        //handle filter conditions
        if (! empty($filter)) {
            if (! is_array($filter))
                $filter = json_decode($filter,True);

            if (isset($filter['property'])) {
                $prop_map = array();

                $index = 0;
                $delIndx = array();
                foreach ($filter['property'] as &$prop) {
                    if (empty($prop['v'])) {
                        $delIndx[] = $index;
                        continue;
                    }

                    $id = intval($prop['id']);
                    $prop['is_multiple'] = False;
                    if (count($prop['v']) > 1 ) {
                        $prop['is_multiple'] = True;
                    } else
                        $prop['v'] = $prop['v'][0];
                    $prop_map[$id] = $index;
                    $index++;
                }

                if (count($delIndx) > 0 )
                    foreach ($delIndx as $index)
                        unset($filter['property'][$index]);

                if (count($filter['property'])>0) {

                    $filter['property_multiple'] = False;
                    if (count($filter['property']) > 1) {
                        $filter['property_multiple'] = True;
                    }

                    $ids = array_keys($prop_map);
                    $attr_Ids = implode(",",$ids);
                    //echo $attr_Ids;
                    $attr_info = DALObject::getAttributeInfo($attr_Ids);
                    foreach ($attr_info as $attr_id => $attr_val) {
                        $type = $attr_val['type'];
                        $is_int = True;
                        if ($type == 'dict') {
                            $value_type = 'uint_value';
                        } elseif ($type == 'string') {
                            $value_type = 'string_value';
                            $is_int = False;
                        } elseif ($type == 'uint') {
                            $value_type = 'uint_value';
                        } elseif ($type == 'float') {
                            $value_type = 'float_value';
                        } elseif ($type == 'date') {
                            $value_type = 'uint_value';
                        }

                        $id = intval($attr_id);
                        if (isset($prop_map[$id])) {
                            $index = $prop_map[$id];
                            $filter['property'][$index]['id'] = $attr_id;
                            $filter['property'][$index]['value_type'] = $value_type;
                            $filter['property'][$index]['is_int'] = $is_int;
                        }
                    }
                   // $filter['property_ids'] = $attr_Ids;
                }
            }
        }        //print_r($filter);
        //return array(True, $total,$ret);
        //handle sort conditions
        if (! empty($sort)) {
            if (is_string($sort))
                $sort = json_decode($sort,True);

            if (isset($sort['id'])) {
                $sort['system'] = False;
                //print_r($sort);
                //echo gettype($sort['id']);
                if (is_numeric($sort['id'])) {
                    $sort['id'] = intval($sort['id']);
                } else {
                    if ($sort['id'] == 'hardware_type')
                        $sort['id'] = 2;
                    elseif ($sort['id'] == 'asset_tag')
                        $sort['id'] = 'asset_no';
                    $sort['system'] = True;
                }
            } else
                $sort = NULL;
        }
        //return array(True, 0,array());
        list($total,$data) = DALObject::getObjectList($keyword,$filter,$sort,$start,$limit);
        //print_r($data);
        if (count($data)>0) {
            require_once('DALSpace.php');
            $ids = array_keys($data);
            $objIds = implode(",",$ids);
            $obj_atts = DALObject::getObjectPropertys($objIds,True);
            //print_r($obj_atts);
            $obj_rack = DALSpace::getRackMoleculeByObjectIds($objIds);
            //print_r($obj_rack);
            $obj_container = DALObject::getObjectContainer($objIds);
            //print_r($obj_container);
            $empty_rack = array('location_id' => NULL,
                                'location_name' => NULL,
                                'row_id' => NULL,
                                'row_name' => NULL,
                                'rack_id' => NULL,
                                'rack_name' => NULL,
                                'units' => NULL);

            $empty_attrs = array('tags' => NULL,
                                 'property' => NULL,
                                 'hardware_type' => NULL,
                                 'hardware_type_id' => 0,
                                 'has_problems' => 'no',
                                 'object_type' => NULL);
            $locData = DALSpace::getLocationDataAll();
            foreach($data as $obj) {
                $id = intval($obj['id']);
                if (isset($obj_atts[$id]))
                    $obj = array_merge($obj,$obj_atts[$id]);
                else {
                    $obj = array_merge($obj,$empty_attrs);
                }

                if (isset($obj_rack[$id]))
                    $obj = array_merge($obj,$obj_rack[$id]);
                else
                    $obj = array_merge($obj,$empty_rack);

               if (isset($obj_container[$id]))
                    $obj['container'] = $obj_container[$id];
                else
                    $obj['container']  = array();
                $obj['locations'] = BZObject::common_location_tree($obj['location_id'],$locData);
                $obj['objtype_id'] = intval($obj['objtype_id']);
                unset($obj['location_id']);
                unset($obj['location_name']);
                $ret[] = $obj;
            }
        }

        return array(True, $total,$ret);
    }

    public static function get_objtype_columns($objtype_id,$objtype_namelist) {
        $is_id = True;
        if (empty($objtype_id)) {
            if (empty($objtype_namelist)) {
                return DALObject::getAttributeInfo();
            } else {
                $templist = explode(",",$objtype_namelist);
                $objtype  = "'".implode("','",$templist)."'";
                $is_id = False;
            }
        } else
             $objtype = $objtype_id;
        return DALObject::getObjtypeColumns($objtype,$is_id);
    }

    public static function get_common_attribues() {
        return DALObject::getAttributeInfo();
    }

    //internal call function
    public static function common_network_detail($id) {
        $ret = array();
        $ipa = array();
        $allocs_by_iface = array();
        $ipa['ipv4'] = getObjectIPv4Allocations($id);
        $ipa['ipv6'] = getObjectIPv6Allocations($id);
        foreach (array ('ipv4', 'ipv6') as $ip_v)
        {
            foreach ($ipa[$ip_v] as $ip_bin => $alloc) {
                $allocs_by_iface[$alloc['osif']][$ip_bin] = $alloc;
            }
        }

        $ret = array();

        if (empty($allocs_by_iface)) return $ret;

        foreach (sortPortList ($allocs_by_iface) as $iface_name => $alloclist)
        {
            $is_first_row = TRUE;
            foreach ($alloclist as $alloc)
            {
                $entity = array();
                $entity['osif'] = $iface_name;
                $entity['type'] = $alloc['type'];
                $entity['ip'] = $alloc['addrinfo']['ip'];
                $commont = "";
                if (isset($alloc['comment']))
                        $commont =  $alloc['comment'];
                $entity['comment'] = $commont;

                $reserved = "";
                if (isset($alloc['reserved']))
                        $reserved = $alloc['comment'];
                $entity['reserved'] = $reserved;
                $ip_bin = $alloc['addrinfo']['ip_bin'];
                $network = spotNetworkByIP($ip_bin);
                //print_r( $network);
                $entity['network'] = array();
                $entity['network']['realm'] = $network['realm'];
                $entity['network']['id'] = $network['id'];
                $entity['network']['name'] = $network['name'];
                $entity['network']['comment'] = $network['comment'];
                $entity['network']['ip'] = $network['ip'];
                $entity['network']['mask'] = $network['mask'];

                $other_routers = array();
                $entity['network']['routed_by'] = "";
                if ($display_routers = (getConfigVar ('IPV4_TREE_RTR_AS_CELL') != 'none')) {
                    foreach (findNetRouters ($network) as $router)
                        if ($router['id'] != $object_id)
                            $other_routers[] = $router;
                    if (count ($other_routers))
                        $entity['network']['routed_by'] = getOutputOf ('printRoutersTD', $other_routers, $display_routers);

                }

                $ret[] = $entity;
            }
        }
        return $ret;
    }

    //internal call function
    public static function common_containers_detail($id,$obj_info) {

        $ret = array();
        if (!empty($id)) {

            if (empty($obj_info)) {

                $obj_info = spotEntity ('object', $id);
                amplifyCell ($obj_info);
            }

            //print_r($obj_info);
            $parents = getParents ($obj_info, 'object');
            $children = getChildren ($obj_info, 'object');
            //print_r($children);
            // lookup the human-readable object type, sort by it
            $allObjectIds = array();

            foreach ($parents as $parent_id => $parent) {
                $parents[$parent_id]['object_type'] = decodeObjectType ($parent['objtype_id']);
                $allObjectIds[] = $parent['id'];
            }

            foreach ($children as $children_id => $child) {
                $allObjectIds[] = $child['id'];
            }

            $asset_keys = array();
            if (count($allObjectIds)>0) {

                $allObjectIds = array_unique($allObjectIds);
                $allObjectIds = array_filter($allObjectIds);
                $ids = implode(",",$allObjectIds);
                //echo $ids . "\n";
                $assetIdsData = DALObject::getAssetIdByObject($ids,False);
                foreach($assetIdsData as $objId => $assetId)
                    if (! empty($assetId))
                        $asset_keys[] = $assetId;
            }

            $ret['asset_keys'] = $asset_keys;
            $grouped_parents = groupBy ($parents, 'object_type');
            ksort ($grouped_parents);
            $ret['parents'] = array();
            foreach ($grouped_parents as $parents_group)
            {
                uasort ($parents_group, 'compare_name');
                $fmt_parents = array();
                foreach ($parents_group as $parent) {
                    $oid = $parent['id'];
                    $objtype_id = $parent['objtype_id'];
                    $temp_data = array('id' => $oid,
                                'name' => $parent['name'],
                                'label' => $parent['label'],
                                'asset_id' => NULL,
                                'type_id' => $parent['objtype_id'],
                                'rack_id' => $parent['rack_id'],
                                'host_name' => "",
                                'host_id' => "");               //default
                    if (isset($assetIdsData[$oid]))
                        $temp_data['asset_id'] = $assetIdsData[$oid];

                    $fmt_parents[decodeObjectType ($objtype_id)][] = $temp_data;
                }
                $ret['parents'] = $fmt_parents;
            }

            $ret['children'] = array();
            foreach (groupBy ($children, 'objtype_id') as $objtype_id => $children_group)
            {
                uasort ($children_group, 'compare_name');
                $fmt_children = array();
                foreach ($children_group as $child) {
                    $oid = $child['id'];
                    $temp_data = array('id' => $oid,
                                    'name' => $child['name'],
                                    'label' => $child['label'],
                                    'type_id' => $child['objtype_id'],
                                    'rack_id' => $child['rack_id'],
                                    'asset_id' => NULL,
                                    'host_name' => "",
                                    'host_id' => "");               //default
                    if (isset($assetIdsData[$oid]))
                        $temp_data['asset_id'] = $assetIdsData[$oid];

                    $fmt_children[] = $temp_data;
                }
                $ret['children'][decodeObjectType ($objtype_id)] = $fmt_children;
            }
        }
        return $ret;
    }

    //internal call function
    public static function common_location_tree($cid,$locData=NULL) {
        $locations = array();

        if (! empty($cid)) {
            if (empty($locData)) {
                //echo "load Space data\n";
                require_once('DALSpace.php');
                $locData = DALSpace::getLocationDataAll();
            }
            $loop_level = 20;
            $index_level = 0;
            $locationData = array();
            while(true) {
                if ($index_level>$loop_level || empty($cid))
                    break;
                $pid = $locData[$cid]['parent_id'];
                $locationData[$cid ] = array('id'=> intval($cid),'name' => htmlspecialchars_decode($locData[$cid]['name']));
                $cid = $pid;
                $index_level++;
            }
            $locations = array_reverse($locationData);
        }
        return $locations;
    }

    public static function get_object_detail($id,$has_simple=False) {
        $ret = array();
        $obj_id = intval($id);
        $obj_atts = DALObject::getObjectPropertys($obj_id);
        if (! empty($obj_atts)) {
            require_once('DALSpace.php');
            $obj_rack = DALSpace::getRackMoleculeByObjectIds($obj_id,True);
            //print_r($obj_rack);
            $obj = $obj_atts[0];
            $empty_rack = array('location_id' => NULL,
                                'location_name' => NULL,
                                'row_id' => NULL,
                                'row_name' => NULL,
                                'rack_id' => NULL,
                                'rack_name' => NULL,
                                'units' => NULL);
            if (isset($obj_rack[$id]))
                $obj = array_merge($obj,$obj_rack[$id]);
            else
                $obj = array_merge($obj,$empty_rack);

            $info = spotEntity ('object', $id);
            amplifyCell ($info);

            if ($has_simple)
                return array(True, $obj);

            //print_r($info);
            $obj['objtype_id'] = intval($info['objtype_id']);
            if (! empty($info)) {
                $containers = BZObject::common_containers_detail($obj_id,$info);
                //print_r($containers);
                if (!empty($containers)) {
                    $obj['children'] = $containers['children'];
                    $obj['parents'] = $containers['parents'];
                    $obj['asset_keys'] = $containers['asset_keys'];
                }
            }

            $obj['ips'] = BZObject::common_network_detail($obj_id);
            $obj['nat4'] = getNATv4ForObject($obj_id);
            $obj['files'] = getFilesOfEntity('object', $obj_id);
            $obj['locations'] = BZObject::common_location_tree($obj['location_id']);
            unset($obj['location_id']);
            unset($obj['location_name']);
            $ret = $obj;
        }

        return array(True, $ret);
    }

    public static function get_objtype_attributes($objtype_id) {
        //$ret = getObjTypeAttrMap(intval($objtype_id));
        $ret = array();
        $data = DALObject::getObjectAttrs(intval($objtype_id),true);
        if (! empty($data['dicts'])) {
            $arrIds = array_keys($data['dicts']);
            $arrIds = array_filter($arrIds);
            $ids = implode(",",$arrIds);
            //echo $ids;
            $dict_map = DALObject::getObjectAttrsDict($ids,True);

            foreach ($data['dicts'] as $chapter_id => $attr_id) {
                if (isset($data['attributes'][$attr_id])) {
                    $data['attributes'][$attr_id]['items'] = (isset($dict_map[$chapter_id]))? $dict_map[$chapter_id] : NULL;
                }
            }
        }
        $ret = array_values($data['attributes']);

        return array(True, array_values($ret));

    }

    public static function get_object_attrvalues($attr_id) {
        if (! is_numeric($attr_id))
            return array(False,'parameter invalid');
        $ret = array();
        $ret = DALObject::getAttrValueList(intval($attr_id));
        return array(True, $ret);

    }

    public static function get_objtype_list($hwtype_ids) {
        list($check,$resp) = verifyIntegerArrary($hwtype_ids);
        if ($check) {
            $data = DALObject::getObjectTypeList($resp);
        } else
            $data = $resp;
        return array(True, $data);
    }

    public static function get_hardware_type($objtype_ids) {
        if (strpos($objtype_ids, ',') !== False )
            $objtype_ids = explode(",",$objtype_ids);
        elseif ($objtype_ids == 'null')
            $objtype_ids = NULL;

        $data = DALObject::getHardwareType($objtype_ids);
        $ret['items'] = $data;
        $ret['id'] = 2;
        $ret['type'] = "dict";
        $ret['name'] = "HW type";
        if (count($ret)>0) {
            return array(True, $ret);
        } else {
            return array(False,'object type empty');
        }
    }

    public static function get_objtype_hwlist($objtype_namelist) {
        $templist = explode(",",$objtype_namelist);
        $objtype  = "'".implode("','",$templist)."'";
        $ret = DALObject::getHardwareTypeByObjType($objtype);
        return array(True,$ret);
    }

    public static function getDictAttrValueListByName($objtype_ids,$name_list) {
        $objtype = NULL;
        if (! empty($objtype_ids)) {
            $templist = explode(",",$objtype_ids);
            $objtype  = "'".implode("','",$templist)."'";
        }

        $attr_name_list = NULL;
        if (! empty($name_list)) {
            $templist = explode(",",$name_list);
            $attr_name_list  = "'".implode("','",$templist)."'";
        }
        $ret = DALObject::getDictAttrValueListByName($objtype,$attr_name_list);
        return array(True,$ret);
    }

    public static function getCommonNameByName($objtype_list,$name_list){
        $templist = explode(",",$objtype_list);
        $objtypes  = "'".implode("','",$templist)."'";

        $templist = explode(",",$name_list);
        $commnames  = "'".implode("','",$templist)."'";
        $ret = DALObject::getCommonNameByName($objtypes,$commnames);
        return array(True,$ret);
    }

    public static function common_verify_system_field($data,$is_create=True) {
        //print_r($data);
        $check_result = True;
        $err_message = array();
        if (empty($data['objtype_id'])) {
            $check_result = False;
            $err_message[] = array('field'=>'object_type', 'msg'=>"object \"type\" empty");
        } elseif (strlen($data['name']) > 255) {
            $check_result = False;
            $err_message[] = array('field'=>'name', 'msg'=>"object \"Common Name\" length is too big");
        } elseif (strlen($data['label']) > 255)  {
            $check_result = False;
            $err_message[] = array('field'=>'label', 'msg'=>"object \"label\" length is too big");
        } elseif (strlen($data['asset_tag']) > 64) {
            $check_result = False;
            $err_message[] = array('field'=>'asset_tag', 'msg'=>"object \"asset_tag\" length is too big");
        } else if (! is_numeric($data['objtype_id'])) {
            $check_result = False;
            $err_message[] = array('field'=>'object_type', 'msg'=>"object \"type\" invalid");
        } else if (! is_numeric($data['hardware_type_id'])) {
            $check_result = False;
            $err_message[] = array('field'=>'hardware_type', 'msg'=>"object \"hardware_type\" invalid");
        }

        if ($is_create) {
            // check Common name column Uniqueness
            if (DALObject::checkObjectColunmExists('name',$data['name'])){
                $check_result = False;
                $err_message[] = array('field'=>'name', 'msg'=>"object \"name\" already exists in system");

            }
            // check Common name column Uniqueness
            if (DALObject::checkObjectColunmExists('asset_no',$data['asset_tag'])){
                $check_result = False;
                $err_message[] = array('field'=>'asset_tag', 'msg'=>"object \"asset_tag\" already exists in system");

            }
        } else {
            if (! isset($data['id']))  {
                $err_message[] = array('field'=>'object id', 'msg'=>"object \"object id\" not found");
                $check_result = False;
            }
        }
        return array($check_result,$err_message);
    }

    //internal call function
    public static function common_verify_field($objtype_id,$propertys) {
        $objectAttr = getObjTypeAttrMap($objtype_id);
        $field_name = NULL;
        $check_result = True;
        $err_message = array();
        foreach ($propertys as $property) {
            if (! empty($property['value'])) {
                $field_name = '';
                $attr_id = $property['id'];
                if (isset($objectAttr[$attr_id])) {
                    $field_name = $objectAttr[$attr_id]['name'];
                    $type = $objectAttr[$attr_id]['type'];
                    $attr_value = $property['value'];
                    if ($type == 'dict') {
                        $attr_value = intval($attr_value);
                        $chapter_id = $objectAttr[$attr_id]['chapter_id'];
                        $chapter_data = readChapter ($chapter_id, $style = '');
                        if (! empty($chapter_data)) {
                            if (! isset($chapter_data[$attr_value])){
                                $err_message[] = array('field'=>$field_name, 'msg'=>"attribute \"{$field_name}\" item not found");
                                continue;
                            }
                        }
                    } elseif (($type == 'date') || ($type == 'uint') ||  ($type == 'float')) {
                        if (! is_numeric($attr_value)) {
                            $check_result  = False;
                            $err_message[] = array('field'=>$field_name, 'msg'=>"Invalid \"{$field_name}\" field format(numeric)");
                            continue;
                        }

                        if ($type == 'float') {
                            if (! is_float($attr_value)) {
                                $check_result  = False;
                                $err_message[] = array('field'=>$field_name, 'msg'=>"Invalid \"{$field_name}\" field format(float)");
                                continue;
                            }
                        } else {
                            $attr_value = intval($attr_value);
                            if ($attr_value > 4294967295) {
                                $check_result  = False;
                                $err_message[] = array('field'=>$field_name, 'msg'=>"\"{$field_name}\" value is too big");
                                continue;
                            }
                        }
                    } else {
                        $value_len =strlen($attr_value);
                        /*if ($value_len > 255) {
                            $check_result  = False;
                            $err_message[] = array('field'=>$field_name, 'msg'=>"\"{$field_name}\" length is too big");
                            continue;
                            }*/
                    }
                } else {
                    $err_message[] = array('field'=>$field_name, 'msg'=>"attribue \"{$field_name}\" can't access ");
                }
            }
        }
        return array($check_result,$field_name);
    }

    //internal call function
    public static function common_verify_position($location_id,$row_id,$rack_id,$zerou,$units,$object_id=0) {
        /*$location_id = $data['locations'][0]['id'];
        $loc_list = getLocationChildrenList ($location_id, array ());
        $err_message = "";
        $field_name = "";
        echo json_encode($loc_list);
        //print_r($loc_list);
        if (count($loc_list) > 1){
            foreach ($data['locations'] as $loc_data) {
                $id = strval($loc_data['id']);
                if ($location_id == $id) continue; // avoid first recrod
                if (! in_array($id,$loc_list)) {
                    $check_position = False;
                    //echo 'break';
                    $field_name = 'location_id';
                    $err_message = "location invalid";
                    break;
                }
                $check_position = True;
                $location_id = intval($id);
            }
        } else {
            $check_position = True;
        }*/
        $err_message = array();
        require_once('DALSpace.php');
        if (DALSpace::checkLocationExists($location_id))
            $check_position = True;
        else
            $err_message = array('field'=>'location', 'msg'=>'location invalid');

        $field_name = NULL;
        if ($check_position) {
            $is_create = ($object_id == 0)? True : False;
            if (is_numeric($location_id)) {
                $row_list = getRows($location_id);
                //echo json_encode($row_list). "\n";
                $row_id =  strval($row_id);
                //echo json_encode(array('id'=>$row_id)) . "\n";
                if (isset($row_list[$row_id])) {
                    $rack_list = getRacks ($row_id);
                    //echo json_encode($rack_list). "\n";
                    $rack_id = strval($rack_id);
                    if (isset($rack_list[$rack_id])) {
                        if (empty($units) == False) {
                            $field_name = "rack u";
                            $racku_data = spotEntity ('rack', intval($rack_id));
                            amplifyCell ($racku_data);
                            // echo json_encode($racku_data). "\n";
                            // use rack data
                            if ($is_create == False) {
                                $userack_data = DALObject::getObjectRackData('object',$object_id);
                                //echo "used units:" . json_encode($userack_data[strval($object_id)]['units']). "\n";
                                if (empty($userack_data[strval($object_id)]['units'])) $is_create = True;
                                //echo json_encode(array('is create'=>$is_create)). "\n";
                            }
                            //check space free
                            $ucheck= True;
                            //echo "update object id: {$object_id} \n";
                            // echo "dest units:" . json_encode($units). "\n";
                            $dict_used = array();
                            foreach ($units as $unit_no => $unit_atom) {
                                $u_id = strval($unit_no);
                                $poscheck = True;
                                //echo "dest racku_data:" .json_encode($racku_data[$u_id]) . "\n";
                                if (isset($racku_data[$u_id])) {
                                    foreach (array ('F', 'I', 'R') as $pos) {
                                        $pos_index = 2;
                                        if ($pos == 'F') {
                                            $atom = 'front';
                                            $pos_index = 0;
                                        } elseif ($pos == 'I') {
                                            $atom = 'interior';
                                            $pos_index = 1;
                                        } else {
                                            $atom = 'rear';
                                            $pos_index = 2;
                                        }
                                        $ustate = $racku_data[$u_id][$pos_index]['state'];
                                        //echo "no:{$unit_no} pos:{$pos} state {$ustate} \n";
                                        if ( $is_create) {
                                            // create condition
                                            if ($unit_atom[$pos] && $ustate != "F") {
                                                $poscheck = False;
                                                $err_message[] = array('field'=>$field_name, 'msg'=>"create rack:{$rack_id} u:{$unit_no} atom:{$atom} cannot mount here");
                                            } else {
                                                $dict_used[$u_id][$pos_index]['state'] = "T";
                                            }

                                        } else {
                                            // update condition
                                            if ($unit_atom[$pos] && ($ustate != "F")) {
                                                if ($unit_atom[$pos] && $ustate == "T") {
                                                    $used_objid = intval($racku_data[$u_id][$pos_index]['object_id']);
                                                    //echo "obj id:{$used_objid} no:{$unit_no} pos:{$pos} state T \n";
                                                    if ($object_id != $used_objid) {
                                                        $err_message[] = array('field'=>$field_name, 'msg'=>"update rack:{$rack_id} u:{$unit_no} position:{$atom} invalid");
                                                        //echo json_encode($err_message);
                                                        $poscheck = False;
                                                    }
                                                } else {
                                                    $err_message[] = array('field'=>$field_name, 'msg'=>"update rack:{$rack_id} u:{$unit_no} position:{$atom} invalid");
                                                    $poscheck = False;
                                                }
                                            }
                                        }
                                    }
                                    //print_r($dict_used);
                                } else { //unit invalid
                                    $err_message[] = array('field'=>'rack', 'msg'=>'rack u invalid');
                                    $ucheck = False;
                                }

                                if ($poscheck==False) {
                                    $ucheck = False;
                                    break;
                                }
                                //echo "poscheck:" . json_encode($poscheck);
                                //echo "ucheck:" . json_encode($ucheck);
                            }

                            if ($ucheck) {
                                $check_position = True;
                            } else
                                $check_position = False;
                        } else {
                            if ($zerou) {
                                if ($object_id != 0) {
                                    require_once('DALSpace.php');
                                    //物件存在zerou就非新建
                                    $is_create = (DALSpace::checkZerouObjectExists($object_id))? False : True;
                                }
                                $check_position = True;
                            } else {
                                $check_position = False;
                                $err_message[] = array('field'=>'rack u', 'msg'=>'rack u empty');
                            }
                        }
                    } else {
                        $check_position = False;
                        $err_message[] = array('field'=>'rack', 'msg'=>'rack invalid');
                    }
                } else {
                    $check_position = False;
                    $err_message[] = array('field'=>'row', 'msg'=>'row invalid');
                }
            }
        }
        $has_position=($check_position)? True: False;
        if ($object_id == 0)
            return array($has_position,$err_message);
        else
            return array($has_position,$is_create,$err_message);

    }

    //internal call function
    public static function common_check_object($index,$data,$object_id=0) {
        $check_result = True;
        if ($object_id == 0) {
            $verfity_common_name = True;
            $flag = 'created';
        } else {
            $verfity_common_name = False;
            $flag = 'updated';
        }

        $row_index = (isset($data['row_index']))? $data['row_index'] : $index;
        $row_objtype = (isset($data['row_objtype']))? $data['row_objtype'] : decodeObjectType($data['objtype_id']);
        $row_name = $data['name'];
        //echo json_encode(array('verfity_common_name' => $verfity_common_name));
        $result = array('objtype'=> $row_objtype,'row'=>$row_index,'name'=>$row_name,'id'=> $object_id, $flag=> $check_result, 'error' => array());

        //符合 ui 顯示一致性
        if ($object_id != 0)
            $result['ignore'] = False;

        list($state,$err_data) = BZObject::common_verify_system_field($data,$verfity_common_name);
        if ($state == False) {
            $result[$flag] = false;
            $result['error'] = array_merge($result['error'],$err_data);
            $check_result  = False;
        }
        // check attribute (common + object type)
        list($state,$err_data) = BZObject::common_verify_field(intval($data['objtype_id']),$data['property']);
        // make error message
        if ($state == False) {
            $result[$flag] = False;
            $result['error'] = array_merge($result['error'],$err_data);
            $check_result  = False;
        }

        // check position (common + object type)
        $location_id = $data['location_id'];
        $row_id = $data['row_id'];
        $rack_id = $data['rack_id'];
        $rack_zerou = (isset($data['zerou']))?$data['zerou'] : False;
        $rack_units = $data['units'];
        $has_position = False;
        $pos_create = True;
        // check position

        if ((! empty($location_id)) && (! empty($row_id)) && (! empty($rack_id))) {
            //print_r($data);
            if ((!empty($rack_units)) && ($rack_zerou))  //both assign
                $result['error'][] = array('field'=>'position', 'msg'=>'asset cannot be located on both Zero-U and any rack unit');
            elseif ((empty($rack_units)) && (! $rack_zerou)) //empty
                $result['error'][] = array('field'=>'position', 'msg'=>'please assign the asset to a specific rack unit or to Zero-U');
            else {

                if (preg_match('!^[1-9][0-9]*$!', $location_id) && preg_match('!^[1-9][0-9]*$!', $row_id) &&  preg_match('!^[1-9][0-9]*$!', $rack_id)){
                    if ($object_id == 0) //create
                        list($has_position,$err_data) = BZObject::common_verify_position($location_id,$row_id,$rack_id,$rack_zerou,$rack_units);
                    else //update
                        list($has_position,$pos_create,$err_data) = BZObject::common_verify_position($location_id,$row_id,$rack_id,$rack_zerou,$rack_units,intval($object_id));

                    if (count($err_data)>0){
                        $result['error'] = array_merge($result['error'],$err_data);
                    }
                } else {
                    if (! preg_match('!^[1-9][0-9]*$!', $location_id)) $result['error'][] = array('field'=>'location', 'msg'=>'location invalid');
                    if (! preg_match('!^[1-9][0-9]*$!', $row_id)) $result['error'][] = array('field'=>'row', 'msg'=>'row invalid');
                    if (! preg_match('!^[1-9][0-9]*$!', $rack_id)) $result['error'][] = array('field'=>'rack', 'msg'=>'rack invalid');
                }
            }

            if ($has_position == False) {
                $result[$flag] = False;
                //$result[$index]['error'] = array_merge($result[$index]['error'],$err_data);
                $check_result  = False; // position
            }
        } else {
            //handle position exists =>  null
            //if ($object_id == 0) {
            if (! empty($location_id)) {
                if (empty($row_id)) $result['error'][] = array('field'=>'row', 'msg'=>'row empty');
                if (empty($rack_id)) $result['error'][] = array('field'=>'rack', 'msg'=>'rack empty');
                if (count($result['error'])>0) {
                    $result[$flag] = False;
                    $check_result  = False;
                }
            } else {
                if (empty($location_id) && $rack_zerou) $result['error'][] = array('field'=>'location', 'msg'=>'location empty');

            }
            //}
        }
        $ret = array($check_result,$has_position,$pos_create,$result);
        //if ($object_id == 6533)
        //    echo json_encode($data)."\n";
        //echo json_encode($ret)."\n";
        return $ret;
    }

    public static function precheck_objects($content) {
        //echo "precheck_objects\n";
        $json_data = json_decode($content,True);
        //print_r($json_data);
        $ret = array();
        $index = 0;
        if ($json_data) {
            foreach ($json_data as $data) {
                //print_r($data);
                list($check_result,$has_position,$pos_create,$result) = BZObject::common_check_object($index,$data,0);
                //print_r($result);
                $ret = array_merge($ret,array_values($result));
                $index++;
            }
        }
        return array(True,$ret);
    }

    //internal call function
    public static function common_check_object_format($data,$is_extend_attr) {

        if (isset($data['comment'])) unset($data['comment']);
        if (isset($data['row_objtype'])) unset($data['row_objtype']);
        if (isset($data['row_index'])) unset($data['row_index']);

        //print_r($data['property']);
        $propertys = array();
        if (! empty($data['property'])) {
            foreach ($data['property'] as $item) {
                if ($is_extend_attr) {
                    foreach ($item as $prop) {
                        $value = $prop['value'];
                        if ($prop['type'] == 'dict')
                            $value = $prop['dict_id'];
                        $propertys[] = array("id"=>intval($prop['id']),"value"=>strval($value));
                    }

                } else
                    $propertys[] = array("id"=>intval($item['id']),"value"=>strval($item['value']));
            }

            usort($propertys, function($a, $b)
            {
                return ($a['id'] > $b['id']);
            });
        }

        $tags = array();
        if (! empty($data['tags'])) {
            foreach ($data['tags'] as $item) {
                $tags[] = array("id"=>intval($item['id']));
            }

            usort($tags, function($a, $b)
            {
                return (intval($a['id']) > intval($b['id']));
            });
        }

        if (empty($data['units']))
            $data['units'] = array();
        else {
            ksort($data['units']);
        }

        return array('id'=>intval($data['id']),
                     'name'=> $data['name'],
                     'label'=> $data['label'],
                     'objtype_id'=> intval($data['objtype_id']),
                     'asset_tag'=> $data['asset_tag'],
                     'hardware_type_id'=> intval($data['hardware_type_id']),
                     'location_id'=> intval($data['location_id']),
                     'row_id'=> intval($data['row_id']),
                     'rack_id'=> intval($data['rack_id']),
                     'tags'=> $tags,
                     'property'=> $propertys,
                     'units'=> $data['units'],
                     'zerou'=>isset($data['zerou'])?$data['zerou']:False);

    }

    //internal call function
    public static function common_check_object_diff($object_id,$new_data) {
        //echo "$object_id";
        $objInfo = BZObject::get_object_detail($object_id,True)[1];
        $is_extend_attr = True;
        $rawInfo = BZObject::common_check_object_format($objInfo,$is_extend_attr);
        $is_extend_attr = False;
        $new_data = BZObject::common_check_object_format($new_data,$is_extend_attr);

        $ids = array_flip(array_column($new_data['property'], 'id'));
        //echo json_encode($ids) . "\n";
        $propertys = array();
        foreach ($rawInfo['property'] as $prop) {
            $id = strval($prop['id']);
            if (isset($ids[$id]))
                $propertys[] = $prop;

        }
        //echo json_encode($propertys) . "\n";
        usort($propertys, function($a, $b)
        {
            return ($a['id'] > $b['id']);
        });
        $rawInfo['property'] =$propertys;

        $check_equal = False;
        ksort($rawInfo);
        ksort($new_data);
        $arr1 =json_encode($rawInfo);
        $arr2 =json_encode($new_data);
        if ($arr1 == $arr2) {
            $check_equal = True;
            //echo "{$object_id} are equal\n";
        } else {
            //echo "{$object_id} not equal\n";
           // echo "rowinfo:". $arr1 . "\n";
            //echo "update:" . $arr2 . "\n";
            $check_equal = False;
        }
        return $check_equal;
    }

    public static function create_procedure($content) {
        $index = 0;
        $json_data = json_decode($content,True);
        $ret = array();
        if ($json_data) {
            foreach ($json_data as $data) {
                // create object process
                //print_r($data);
                $row_index = (isset($data['row_index']))? $data['row_index'] : $index;
                list($check_result,$has_position,$pos_create,$result) = BZObject::common_check_object($row_index,$data,0);
                //return array(True,array('has_position'=>$has_position,'result'=>$result));
                //continue;
                $object_id = NULL;
                if ($check_result) {
                    try
                    {
                        $object_id = BZObject::create_object($has_position,$data);
                        $result['id'] = $object_id;
                        if(empty($object_id))  {
                            $result[$flag] = False;
                            $result['objtype'] = (isset($data['row_objtype']))? $data['row_objtype'] : decodeObjectType($data['objtype_id']);
                            $result['row'] = $row_index;
                            $result['error'][] = array('field'=>NULL, 'msg'=> 'create object fail');
                        }
                    }catch (Exception $e)
                    {
                        $result = array();
                        $result['id'] = $object_id;
                        commitDeleteObject($object_id);
                        $result['created'] = False;
                        $result['objtype'] = (isset($data['row_objtype']))? $data['row_objtype'] : decodeObjectType($data['objtype_id']);
                        $result['row'] = $row_index;
                        //$message = (empty($object_id))? "FAILED CREATE OBJECT,": "FAILED '${$object_id}': ";
                        $result['error'][] = array('field'=>NULL, 'msg'=>$e->getMessage());
                        // continue;
                    }
                }

                $index++;
                $ret[] = $result;
            }
        } else {
            array(False,'parameter format is invalid');
        }
        return array(True,$ret);
    }

    public static function import_procedure($content,$err_rows=array()) {
        $index = 0;
        //print_r($err_rows);
        //$json_data = json_decode($content,True);
        $ret = array();
        if ($content) {
            foreach ($content as $data) {
                $is_create = True;
                // create object process
                $object_id = 0;
                $check_ignore = False;
                if (!empty($data['id'])) {
                    $new_data = $data;
                    $object_id = intval($data['id']);
                    $check_ignore = BZObject::common_check_object_diff($object_id,$new_data);
                    $is_create = False;
                    //break;*/
                }
                //echo json_encode($data). "\n";
                //echo json_encode(BZObject::common_check_object($index,$data,$object_id))."\n";
                $row_index = (isset($data['row_index']))? $data['row_index'] : $index;
                if ($check_ignore == False) {
                    list($check_result,$has_position,$pos_create,$result) = BZObject::common_check_object($index,$data,$object_id);
                    //print_r($data);
                    //if (self::$debug_mode)
                        //continue;
                    $flag = ($is_create)?'created':'updated';
                    if ($check_result) {
                        try
                        {
                            //$result['id'] = $object_id;
                            if ($is_create) {
                                $object_id = BZObject::create_object($has_position,$data);
                                if(empty($object_id))  {
                                    $result[$flag] = False;
                                    $result['objtype'] = (isset($data['row_objtype']))? $data['row_objtype'] : decodeObjectType($data['objtype_id']);
                                    $result['row'] = $row_index;
                                    $result['error'][] = array('field'=>NULL, 'msg'=> 'create object fail');
                                }
                            } else {
                                BZObject::update_object($has_position,$pos_create,$object_id,$data);
                            }
                        }catch (Exception $e)
                        {
                            $result = array();
                            $result['id'] = $object_id;
                            if ($is_create)
                                commitDeleteObject($object_id);
                            $result[$flag] = False;
                            $result['objtype'] = (isset($data['row_objtype']))? $data['row_objtype'] : decodeObjectType($data['objtype_id']);
                            $result['row'] = $row_index;
                            $result['error'][] = array('field'=>NULL, 'msg'=>$e->getMessage());
                        }
                    }
                } else {
                    $result = array();
                    $result['id'] = $object_id;
                    $result['ignore'] = True;
                    $result['updated'] = False;
                    $result['objtype'] = (isset($data['row_objtype']))? $data['row_objtype'] : decodeObjectType($data['objtype_id']);
                    $result['row'] = $row_index;
                    $result['error'] = array();
                     //print_r($result);
                }
                $index++;
                $ret[] = $result;            }
        } else {
            array(False,'parameter format is invalid');
        }

        if (count($err_rows)>0) {
            $index = 0;
            //print_r($ret);
            //echo "ret:";print_r($ret);
            $err_rows = array_values($err_rows);
            //echo "err_rows:";print_r($err_rows);
            $err_dict = array();
            foreach ($err_rows as $row){
                $dict_key = $row['objtype'] ."_". strval($row['row']);
                $err_dict[$dict_key] = $index;
                $index++;
            }

            foreach ($ret as &$ret_info) {
                $com_key = $ret_info['objtype'] ."_". strval($ret_info['row']);
                if(isset($err_dict[$com_key])) {
                    $rid = $err_dict[$com_key];
                    $ret_info['error'] = array_merge($ret_info['error'],$err_rows[$rid]['error']);
                    unset($err_dict[$com_key]);
                }
            }
            //echo "err_dict:";print_r($err_dict);
            foreach ($err_dict as $key => $rid)
                $ret[] = $err_rows[$rid];
            //$ret = array_merge($ret,array_values($err_rows));
        }

        return array(True,$ret);
    }

    public static function update_procedure($content) {
        $ret = array();
        $json_data = json_decode($content,True);
        $index = 0;
        if ($json_data) {
            foreach ($json_data as $data) {
                //update object process
                $object_id = $data['id'];
                $new_data = $data;
                $check_ignore = BZObject::common_check_object_diff(intval($object_id),$new_data);
                $row_index = (isset($data['row_index']))? $data['row_index'] : $index;
                if ($check_ignore == False) {
                    list($check_result,$has_position,$pos_create,$result) = BZObject::common_check_object($index,$data,$object_id);
                    $rack_id = $data['rack_id'];
                    $zerou = (isset($data['zerou']))?$data['zerou']: False;
                    //return array(True,array('hp'=>$has_position,'ic'=>$pos_create,'rs'=>$result,'rk'=> $data['rack_id']));
                    //continue;
                    if ($check_result) {
                        try
                        {
                            BZObject::update_object($has_position,$pos_create,$object_id,$data);
                        } catch (Exception $e)
                        {
                            $result = array();
                            $result['id'] = $object_id;
                        // commitDeleteObject($object_id);
                            $result['updated'] = False;
                            $result['objtype'] = (isset($data['row_objtype']))? $data['row_objtype'] : decodeObjectType($data['objtype_id']);
                            $result['row'] = $row_index;
                            $result['error'][] = array('field'=>NULL, 'msg'=>$e->getMessage());
                            //continue;
                        }
                    }
                    $index++;
                    $ret[] = $result;
                } else {
                    $result = array();
                    $result['id'] = $object_id;
                    $result['ignore'] = True;
                    $result['updated'] = False;
                    $result['objtype'] = (isset($data['row_objtype']))? $data['row_objtype'] : decodeObjectType($data['objtype_id']);
                    $result['row'] = $row_index;
                    $result['error'] = array();
                }
            }
        } else {
            array(False,'parameter format is invalid');
        }
        return array(True,$ret);
    }

    //internal call function
    public static function create_object($has_position,$data) {
        // declare variable
        $objtype_id = $data['objtype_id'];
        $name = $data['name'];
        $label = $data['label'];
        $asset_no = $data['asset_tag'];
        $location_id = $data['location_id'];
        $row_id = $data['row_id'];
        $rack_id = $data['rack_id'];
        $zerou = (isset($data['zerou']))?$data['zerou']: False;
        $taglist = array();

        // add object
        $object_id = commitAddObject($name, $label, $objtype_id, $asset_no, $taglist);
        if (is_numeric($object_id)) {
            $comment = NULL;
            if (isset($data['comment'])) {
                $comment = (empty($data['comment']))? NULL:$data['comment'];
                commitUpdateObject ($object_id,$name, $label, 'no', $asset_no, $comment);
            }

            if ((! empty($data['tags'])) && (! empty($object_id))) {
                //spotEntity ('object', intval($object_id));

                $taglist = array();
                foreach ($data['tags'] as $tag)
                    foreach ($tag as $key => $value) {
                        $tag_id = intval($value);
                        $taglist[] = $tag_id;
                        addTagForEntity ('object', $object_id, intval($tag_id));
                    }
            }

            //write attribute process
            if (! empty($data['hardware_type_id']))
                $data['property'][] = array('id'=> 2, 'value' => $data['hardware_type_id']);

            $propertys = $data['property'];
            foreach ($propertys as $property) {
                if (! empty($property['value'])) {
                    $attr_value = $property['value'];
                    $attr_id = intval($property['id']);
                    commitUpdateAttrValue ($object_id, $attr_id, $attr_value);
                }
            }

            //setting positions process
            if ( $has_position && (! empty($rack_id))) {
                if ($zerou) {
                    require_once('DALSpace.php');
                    //create zero u
                    $entity_id = DALSpace::addObjectToZerou($rack_id,$object_id);
                } else {
                    //create rack u
                    foreach ($data['units'] as $unit_no => $nuit_atom) {
                        foreach (array ('F', 'I', 'R') as $pos) {
                            if (isset($nuit_atom[$pos])) {
                                if ($nuit_atom[$pos] == False) continue;
                                if ($pos == 'F') $atom = 'front';
                                elseif ($pos == 'I') $atom = 'interior';
                                else $atom = 'rear';
                                if (self::$debug_mode) {
                                    putLog( array
                                    (
                                        'rack_id' => $rack_id,
                                        'unit_no' => $unit_no,
                                        'atom' => $atom,
                                        'state' => 'T',
                                        'label' => $label
                                    ));
                                }

                                usePreparedInsertBlade
                                (
                                    'RackSpace',
                                    array
                                    (
                                        'rack_id' => $rack_id,
                                        'unit_no' => $unit_no,
                                        'atom' => $atom,
                                        'state' => 'T',
                                        'object_id' => $object_id,
                                    )
                                );
                            }
                        }
                    }
                }
            }
        }
        return $object_id;
    }

    //internal call function
    public static function update_object($has_position,$is_create,$object_id,$data) {
        $objtype_id = $data['objtype_id'];
        $name = $data['name'];
        $label = $data['label'];
        $asset_no = $data['asset_tag'];
        $location_id = $data['location_id'];
        $row_id = $data['row_id'];
        $rack_id = $data['rack_id'];
        $zerou = (isset($data['zerou']))?$data['zerou']: False;

        $comment = NULL;
        if (isset($data['comment'])) {
            $comment = (empty($data['comment']))? NULL:$data['comment'];
        }

        $taglist = array();
        // update object
        if (! empty($object_id)) {
            commitUpdateObject ($object_id,$name, $label, 'no', $asset_no, $comment);
            $taglist = array();
            if(!empty($data['tags'])) {
                foreach ($data['tags'] as $tag) {
                    foreach ($tag as $key => $value) {
                        $tag_id = intval($value);
                        $taglist[] = $tag_id;
                    }
                }
            }
            rebuildTagChainForEntity ('object', $object_id, buildTagChainFromIds ($taglist), TRUE);

            //write attribute process
            //echo "write attribute process \n";
            if (! empty($data['hardware_type_id']))
                $data['property'][] = array('id'=> 2, 'value' => $data['hardware_type_id']);

            $objAttrVals = getAttrValues($object_id);
            $propertys = $data['property'];
            foreach ($propertys as $property) {
                $attr_id = intval($property['id']);
                $attr_value = (empty($property['value']))? "" : $property['value'];
                commitUpdateAttrValue ($object_id, $attr_id, $attr_value);
            }

            //setting positions process
            //echo json_encode(array('has_position' => $has_position, 'rid'=> $rack_id));
            require_once('DALSpace.php');
            $position_to_empty = False;
            if ( $has_position && (! empty($rack_id))) {
                //echo "setting positions process ";
                if (empty($data['units'])) {
                    if ($zerou) {
                        if ($is_create) {
                            $delcount = 0;
                            //check has rack u
                            $userack_data = DALObject::getObjectRackData('object',$object_id);
                            if (! empty($userack_data[$object_id]['units'])) {
                                //rack u -> zerou
                                $delcount =1;
                                //echo "Delete rack u data\n";
                                $delcount = usePreparedDeleteBlade ('RackSpace', array ('object_id' => $object_id));
                                if ($delcount > 0)
                                    //echo "add Object To Zerou\n";
                                    $entity_id = DALSpace::addObjectToZerou($rack_id,$object_id);
                            } else {
                                //crete zero u
                                //echo "add Object To Zerou\n";
                                $entity_id = DALSpace::addObjectToZerou($rack_id,$object_id);
                            }
                        } else {
                            $origi_rack_id = DALSpace::getZerouRackByObjectId($object_id);
                            if ($origi_rack_id != intval($rack_id)) {
                                //old zerou -> new zerou
                                //echo "Delete old zerou data\n";
                                if (DALSpace::deleteZerouObject($origi_rack_id,$object_id))
                                    $entity_id = DALSpace::addObjectToZerou($rack_id,$object_id);
                            } else {
                                  //old zerou = new zerou
                                  //echo "{$origi_rack_id} nothing to do\n";
                            }
                        }
                    }  else {
                        $position_to_empty = True;
                    }
                } else {
                    $delcount = 0;
                    $is_racku_link = True;
                    if ($is_create) {
                        if (DALSpace::checkZerouObjectExists($object_id)) {
                            //zerou -> rack u
                            $origi_rack_id = DALSpace::getZerouRackByObjectId($object_id);
                            if (DALSpace::deleteZerouObject($origi_rack_id,$object_id) == False) { //avoid zerou and rack u same object
                                $is_racku_link = False;
                                //echo "Delete zerou exists\n";
                            }
                        } else {
                            //empty -> rack u
                            //echo "create new rack u\n";
                        }
                    } else {
                        //檢查 rack 內容是否有變動
                        $rack_data = DALObject::getObjectRackData('object',$object_id);
                        if (! empty($rack_data[$object_id]['units'])) {
                            $is_delete = False;
                            $rack_data = $rack_data[$object_id]['units'];
                            ksort($rack_data);
                            ksort($data['units']);
                            //print_r($rack_data);
                            //print_r($data['units']);
                            $rack1 = serialize($rack_data);
                            $rack2 = serialize($data['units']);
                            if ($rack1 != $rack2)
                                $is_delete = True;
                             //移除舊的rack u 資訊,填入新的rack u 資訊
                            if ($is_delete) {
                                //echo "delete rack u info\n";
                                $delcount = usePreparedDeleteBlade ('RackSpace', array ('object_id' => $object_id));
                            } else {
                                //echo "rack u nothing to do\n";
                                $is_racku_link = False;
                            }
                        }
                    }

                    if ($delcount > 0 || $is_racku_link) {
                        //echo "create rack u info\n";
                        foreach ($data['units'] as $unit_no => $nuit_atom) {
                            foreach (array ('F', 'I', 'R') as $pos) {
                                if (isset($nuit_atom[$pos])) {
                                    if ($nuit_atom[$pos] == False) continue;
                                    if ($pos == 'F')
                                        $atom = 'front';
                                    elseif ($pos == 'I')
                                        $atom = 'interior';
                                    else
                                        $atom = 'rear';
                                    usePreparedInsertBlade
                                    (
                                        'RackSpace',
                                        array
                                        (
                                            'rack_id' => $rack_id,
                                            'unit_no' => $unit_no,
                                            'atom' => $atom,
                                            'state' => 'T',
                                            'object_id' => $object_id,
                                        )
                                    );
                                }
                            }
                        }
                    }
                }
            } else {
                $position_to_empty = True;
            }

            if ($position_to_empty) {
                //原本有資訊取消所有位置資訊
                if (DALSpace::checkZerouObjectExists($object_id)) {
                    $origi_rack_id = DALSpace::getZerouRackByObjectId($object_id);
                    //echo "origi_rack_id:{$origi_rack_id}\n";
                    $delete_result = DALSpace::deleteZerouObject($origi_rack_id,$object_id);
                    //echo "Clear zero u info\n";
                }

                $userack_data = DALObject::getObjectRackData('object',$object_id);
                if (! empty($userack_data[$object_id]['units'])) {
                    //echo "Clear rack u info\n";
                    $delnum = usePreparedDeleteBlade ('RackSpace', array ('object_id' => $object_id));
                }
            }
        }
    }

    public static function delete_objects($object_ids) {
        $ret = array();
        if (! empty($object_ids)) {
            $objects = explode(",",$object_ids);
            foreach ($objects as $obj_id) {
                try{
                    commitDeleteObject($obj_id);
                    $ret[] = array('id' => $obj_id,
                                   'deleted' => TRUE,
                                   'message' => 'success');
                } catch (RackTablesError $e)
                {
                    $ret[] = array('id' => $obj_id,
                                   'deleted' => False,
                                   'message' =>  $e->getMessage());
                }
            }
            return array(TRUE,$ret);
        } else {
            return array(FALSE,$ret);
        }
    }

    public static function import_objects($content) {
        $json_data = (is_array($content))? $content : $json_data = json_decode($content,True);
       /*if (self::$debug_mode) {
            putLog($json_data);
        }*/
        //print_r($json_data);
        $obj_types_arr = array();
        $obj_comname_arr = array();
        $obj_tags_arr = array();
        $obj_loc_arr = array();
        $obj_row_arr = array();
        $obj_rack_arr = array();
        $position_count = 0;

        $object_data = array();
        $index = 0;
        $objects = array();
        $row_index =0;
        $err_rows = array();
        $dict_rack = array();
        foreach ($json_data AS $data) {
            $err_message = array();
            $row_index = (isset($data['row_index']))?intval($data['row_index']) : $index;
            $row_objtype = $data['row_objtype'];
            $check_result = True;
            $object_data = array();
            foreach ($data as $key => $value) {
                //check require field procedure
                $field = strtolower($key);
                $check_data = False;
                if ($field == strtolower("Object Type")) {
                    $check_data = True;
                } elseif ($field == strtolower("HW Type")) {
                    $check_data = True;
                }

                if ($check_data) {
                    $check = False;
                    if (! empty($value)) {
                        if (strlen($value) < 255) {
                            if ($field == strtolower('Object Type')) {
                                $type = $value;
                                if (! isset($obj_types_arr[$type]))
                                    $obj_types_arr[$type] = array();
                                $obj_types_arr[$type][] = $index;
                            }
                            $check = True;
                        } else $message = array('msg'=>"field \"{$field}\" length is too big");
                    } else $message = array('msg'=>"field \"{$field}\" empty");

                    if (!$check) {
                        $check_result = False;
                        $err_message[] = $message;
                    }
                }
                //all key to lower
                $object_data[$field] = $value;
            }
            //print_r($object_data);
            //check require field exists
            if ((! isset($object_data['object type'])) || (! isset($object_data['hw type']))) {
                $err_message[] = array('row'=> $index,'msg'=>"format invalid");
                $check_result = False;
            }

            if (! $check_result) {
                $err_rows[] = array('objtype'=>$row_objtype,'row'=> $row_index,'error'=>$err_message);
                $index++;
                continue;
            }

            $obj_comname_arr[] = $object_data['common name'];
            //collect tags procedure
            if (! empty($object_data['tag'])) {
                //echo $object_data['tag']."\n";
                if (strpos($object_data['tag'], ',') !== false) {
                    $tag_arr = explode(",",$object_data['tag']);
                    $obj_tags_arr = array_merge($obj_tags_arr,$tag_arr);
                    $object_data['tag'] = $tag_arr;
                } else
                    $obj_tags_arr[] = $object_data['tag'];
            }

            //collect position procedure
            //echo htmlspecialchars($object_data['location']).",".$object_data['row'].",".$object_data['rack']."\n";
            if ((! empty($object_data['location'])) && (! empty($object_data['row'])) && (! empty($object_data['rack']))) {
                if (! in_array($object_data['location'], $obj_loc_arr)) $obj_loc_arr[] = htmlspecialchars($object_data['location']);
                if (! in_array($object_data['row'], $obj_row_arr)) $obj_row_arr[] = $object_data['row'];
                if (! in_array($object_data['rack'], $obj_rack_arr)) $obj_rack_arr[] = $object_data['rack'];

                // parser rack u data
                if (! empty($object_data['rack u'])) {
                    if (strpos($object_data['rack u'], ',') !== false) {
                        $u_arr = explode(",",$object_data['rack u']);
                        $object_data['rack u'] = $u_arr;

                    }
                }
                $position_count++;
            }

            $objects[$index] = $object_data;
            $index++;
        }
        ///$ret = $objects;
        if (count($obj_types_arr) > 0) {
            $object = array('name' => NULL,
                            'label' => NULL,
                            'objtype_id' => NULL,
                            'asset_tag' => NULL,
                            'hardware_type_id' => NULL,
                            "location_id" => NULL,
                            "row_id" => NULL,
                            "rack_id" => NULL,
                            'comment' => NULL,
                            'tags' => NULL,
                            'property' => array(),
                            'units' => array());

            $type_keys = array_keys($obj_types_arr);
            $types = implode(",",$type_keys);
            //$ret =  BZObject::get_objtype_columns(NULL,$types);
            $obj_columns = BZObject::get_objtype_columns(NULL,$types);

            $attr_dicts = array();
            $obj_dicts = array();
            if (! empty($obj_columns)) {
                foreach ($obj_columns as $obj_type => $attrs) {
                    foreach ($attrs as $attr => $attr_info) {
                        $field = strtolower($attr);
                        if ($field == 'hw type') continue;
                        if ($attr_info['type'] == 'dict') {
                            if (! in_array($field,$attr_dicts))
                                $attr_dicts[] = $field;
                        }
                    }
                }
                //print_r($attr_dicts);
                if (count($attr_dicts)>0) {
                    $attr_names = implode(",",$attr_dicts);
                    $obj_dicts = BZObject::getDictAttrValueListByName(NULL,$attr_names)[1];
                    //$ret = BZObject::getDictAttrValueListByName(NULL,$attr_names)[1];
                }
            }

            $namekeys = implode(",",$obj_comname_arr);
            $obj_objtype_comname = BZObject::getCommonNameByName($types,$namekeys)[1];

            //$ret = BZObject::get_objtype_hwlist($types)[1];
            $obj_hwtypes = BZObject::get_objtype_hwlist($types)[1];
            if (! empty($obj_tags_arr)) {
                require_once('BZTag.php');
                $tags = implode(",",$obj_tags_arr);
                //$ret = BZTag::getTagNameList($tags)[1];
                $obj_tags = BZTag::getTagNameList($tags)[1];
            }
            //echo "position_count:{$position_count}\n";
            if ($position_count > 0) {
                $locations = implode(",",$obj_loc_arr);
                //echo $locations."\n";
                $rows = implode(",",$obj_row_arr);
                //echo $rows."\n";
                $racks = implode(",",$obj_rack_arr);
                //echo $racks."\n";
                require_once('BZSpace.php');

                $obj_positions = BZSpace::get_position_namelist($locations,$rows,$racks)[1];
                //print_r($obj_positions);
                //$ret = BZSpace::get_position_namelist($locations,$rows,$racks)[1];
            }

            //convert racktable format
            $new_objects = array();
            $object_rack = array();
            $object_comname = array();
            $object_assetno = array();
            $batch_tags = array();
            foreach ($objects AS $index => $data) {
                $err_message = array();
                //print_r($data);
                $row_index = intval($data['row_index']);
                $row_objtype = $data['row_objtype'];
                $row_commname = $data['common name'];
                $row_assetno = (isset($data['asset tag']))?$data['asset tag']:NULL;
                $newobject = $object;
                $obj_type_name = $data['object type'];
                $obj_hw_name = $data['hw type'];
                $next_check = True;

                if (isset($object_comname[$row_commname])) {
                    $next_check = False;
                    $err_message[] = array('field'=> 'Common Name','msg'=>"name already exists");
                } else {
                    $object_comname[$row_commname] = true;
                }
                if (! empty($row_assetno)) {
                    if (isset($object_assetno[$row_assetno])) {
                        $next_check = False;
                        $err_message[] = array('field'=> 'Asset tag','msg'=>"asset tag already exists");
                    } else {
                        $object_assetno[$row_assetno] = true;
                    }
                }

                /*if (self::$debug_mode) {
                    putLog($obj_columns);
                    putLog(array('next_check'=>$next_check));
                }*/
                //check object exists
                if ($next_check && isset($obj_columns[$obj_type_name]['HW Type'])) {
                    //set object type id
                    $obj_type_data = $obj_columns[$obj_type_name];
                    $newobject['objtype_id'] = intval($obj_type_data['HW Type']['objtype_id']);
                    $hw_exsits = False;
                    //check hw exists
                    if (isset($obj_hwtypes[$obj_type_name])) {
                        foreach ($obj_hwtypes[$obj_type_name] as $key => $value) {
                            //echo "obj_hw_name:[".strtolower($obj_hw_name) ."]==[". strtolower($key)."]";
                            if (strtolower($obj_hw_name) == strtolower($key)) {
                                //set object hw type id
                                $newobject['hardware_type_id'] = intval($value['dict_key']);
                                $hw_exsits = True;
                                break;
                            }
                        }
                    }

                    if ($hw_exsits) {
                        //set system field
                        //print_r($data);
                        $newobject['name'] = $data['common name'];
                        $newobject['label'] = (empty($data['visible label']))? NULL : $data['visible label'];
                        $newobject['asset_tag'] = (empty($data['asset tag']))? NULL : $data['asset tag'];
                        $newobject['row_index'] = $data['row_index'];
                        $newobject['row_objtype'] = $data['row_objtype'];
                        //$newobject['location'] =  $data['location'];
                        //echo "name:".$newobject['name'] .",loc:".$newobject['location']."\n";
                       // $newobject['row_id'] =  (empty($data['row_id']))? NULL: ntval($position_data['row_id']);
                       // $newobject['rack_id'] =  (empty($data['rack_id']))? NULL: intval($position_data['rack_id']);
                        //set propertys
                        $prop_index = 0;
                        //print_r($obj_type_data);
                        foreach ($obj_type_data as $key => $info) {
                            $field = strtolower($key);
                            if ($field == 'hw type') continue;
                            if (isset($data[$field])) {
                                $value = NULL;
                                if ($info['type'] == 'date') {
                                    //$tsdatetime = DateTime::createFromFormat('Y/m/d', $data[$field]);
                                    //$value = $tsdatetime->getTimestamp();
                                    $value = (empty($data[$field]))?NULL:intval($data[$field]);
                                } elseif ($info['type'] == 'dict') {
                                    $itme_value = $data[$field];
                                    if (isset($obj_dicts[$field][$itme_value])) {
                                        //print_r($obj_dicts[$field][$itme_value]);
                                        $value = $obj_dicts[$field][$itme_value]['dict_key'];
                                    }
                                } elseif ($info['type'] == 'float') {
                                    $value = (empty($data[$field]))?NULL:floatval($data[$field]);
                                } elseif ($info['type'] == 'uint') {
                                    $value = (empty($data[$field]))?NULL:intval($data[$field]);
                                } else {
                                    $value = (empty($data[$field]))?NULL:strval($data[$field]);
                                }

                                $newobject['property'][] = array('id' => intval($info['id']),
                                                                 'value' => $value);
                                $prop_index++;
                            }
                        }
                        //set tag
                        if (! empty($data['tag'])) {
                            $newobject['tags'] = array();
                            $tag = $data['tag'];

                            if (is_array($tag)) {
                                foreach ($tag as $tag_item) {
                                    if (isset($obj_tags[$tag_item])) {
                                        $newobject['tags'][] = array('id'=>$obj_tags[$tag_item]);
                                    } else {
                                        //$err_message[] = array('field'=>'tag', 'msg'=>'tag:'.$tag_item.' not found');
                                        if (! isset($batch_tags[$tag_item])) {
                                            require_once('DALTag.php');
                                            $tag_name_arr = DALTag::getTagByName($tag_item,true);
                                            if (! isset($tag_name_arr[$tag_item])) {
                                               $tag_id = DALTag::addObjectTag(NULL,$tag_item,NULL);
                                                if (! empty($tag_id)) {
                                                    $batch_tags[$tag_item] = $tag_id;
                                                    $newobject['tags'][] = array('id'=>$tag_id);
                                                } else {
                                                    $err_message[] = array('field'=>'tag', 'msg'=>'tag create fail');
                                                }

                                            }
                                        } else {
                                            $newobject['tags'][] = array('id'=> $batch_tags[$tag_item]);
                                        }
                                    }
                                }
                            } else {
                                if (isset($obj_tags[$tag])) {
                                    $newobject['tags'][] = array('id' => $obj_tags[$tag]);
                                } else {
                                    //$err_message[] = array('field'=>'tag', 'msg'=>'tag:'.$tag_item.' not found');
                                    if (! isset($batch_tags[$tag])) {
                                        require_once('DALTag.php');
                                        $tag_name_arr = DALTag::getTagByName($tag,true);
                                        if (! isset($tag_name_arr[$tag])) {
                                            $tag_id = DALTag::addObjectTag(NULL,$tag,NULL);
                                            if (! empty($tag_id)) {
                                                $batch_tags[$tag] = $tag_id;
                                                $newobject['tags'][] = array('id'=>$tag_id);
                                            } else {
                                                $err_message[] = array('field'=>'tag', 'msg'=>'tag create fail');
                                            }
                                        }
                                    } else {
                                        $newobject['tags'][] = array('id'=> $batch_tags[$tag]);
                                    }
                                }
                            }
                        }

                        //check create/update procedure
                        $idendify = $obj_type_name. "_" . $row_commname;
                        $newobject['id'] = (isset($obj_objtype_comname[$idendify]))? $obj_objtype_comname[$idendify] : NULL;

                        //set positions
                        if ((! empty($data['location'])) && (! empty($data['row'])) && (! empty($data['rack']))) {
                        //if ((! empty($data['location'])) && (! empty($data['row'])) && (! empty($data['rack'])) && (! empty($data['rack u'])) ) {
                            if (count($obj_positions) > 0) {
                                $location = htmlspecialchars($data['location']);
                                $row = $data['row'];
                                $rack = $data['rack'];
                                $ru = $data['rack u'];
                                //echo "{$location} , {$rack} , {$rack} , {$ru} \n";
                                if (isset($obj_positions[$location][$row][$rack])) {
                                    $position_data = $obj_positions[$location][$row][$rack];
                                    $location_id = intval($position_data['location_id']);
                                    $newobject['location_id'] = $location_id;
                                    $row_id = intval($position_data['row_id']);
                                    $newobject['row_id'] = $row_id;
                                    $rack_id = intval($position_data['rack_id']);
                                    $newobject['rack_id'] = $rack_id;

                                    if (! empty($ru) || $ru == '0') {
                                        $newobject['zerou'] = False;
                                        if ($ru == '0')
                                            $newobject['zerou'] = True;

                                        if ($newobject['zerou'] == False) { //no zerou
                                            $racku_empty = array("F"=>true, "I"=>true, "R"=>true);
                                            if (! is_array($ru))
                                                $ru = array($data['rack u']);

                                            foreach ($ru as $racku) {
                                                if(! preg_match('!^[1-9][0-9]*$!', $racku)) {
                                                    $err_message[] = array('field'=>'rack u', 'msg'=>'format is invalid');
                                                    break;
                                                } elseif(intval($racku) == 0) {
                                                    $err_message[] = array('field'=>'rack u', 'msg'=>'cannot mount here');
                                                    break;
                                                }
                                                //echo "racku:". $racku . "\n";
                                                if (BZSpace::check_position_namelist($data['location'],$data['row'],$data['rack'],$racku,$obj_positions)) {
                                                    if (! isset($object_rack[$location_id][$row_id][$rack_id][$racku])) {
                                                        $object_rack[$location_id][$row_id][$rack_id][$racku] = True;
                                                        if (isset($data['front']) && isset($data['interior']) && isset($data['back'])) {
                                                            $racku_atoms = array();
                                                            if ((is_bool($data['front']))&&(is_bool($data['interior']))&&(is_bool($data['back']))) {
                                                                $racku_atoms['F'] = $data['front'];
                                                                $racku_atoms['I'] = $data['interior'];
                                                                $racku_atoms['R'] = $data['back'];
                                                            } else {
                                                                if ($data['front'] == '1' || $data['front'] == '0') {
                                                                        $racku_atoms['F'] = ($data['front']=='1')?True:False;
                                                                    if ($data['interior'] == '1' || $data['interior'] == '0')
                                                                        $racku_atoms['I'] = ($data['interior']=='1')?True:False;
                                                                    if ($data['back'] == '1' || $data['back'] == '0')
                                                                        $racku_atoms['R'] = ($data['back']=='1')?True:False;
                                                                } else {
                                                                    if ($data['front'] == 'true' || $data['front'] == 'false')
                                                                        $racku_atoms['F'] = ($data['front']=='true')?True:False;
                                                                    if ($data['interior'] == 'true' || $data['interior'] == 'false')
                                                                        $racku_atoms['I'] = ($data['interior']=='true')?True:False;
                                                                    if ($data['back'] == 'true' || $data['back'] == 'false')
                                                                        $racku_atoms['R'] = ($data['back']=='true')?True:False;
                                                                }
                                                            }

                                                            if (empty($racku_atoms)){
                                                                $err_message[] = array('field'=>'rack u', 'msg'=>'front / interior / back invalid');
                                                                break;
                                                            } else {
                                                                if ($racku_atoms['F']==False && $racku_atoms['I']==False && $racku_atoms['R']==False)
                                                                    $newobject['units'][$racku] = $racku_empty;
                                                                else
                                                                    $newobject['units'][$racku] = $racku_atoms;
                                                            }
                                                        } else {
                                                            $err_message[] = array('field'=>'rack u', 'msg'=>'cannot mount here');
                                                            break;
                                                        }
                                                        //echo "racku:". $racku;
                                                        //print_r($racku_atoms);
                                                    } else {
                                                        $err_message[] = array('field'=>'rack u', 'msg'=>'cannot mount here');
                                                        break;
                                                    }
                                                }//[else] next process trow error
                                            }
                                        } else {
                                            $newobject['zerou'] = True;
                                        }
                                    } else {
                                        $err_message[] = array('field'=>'rack u', 'msg'=>'Rack u not empty');
                                    }
                                } else {
                                    if (! isset($obj_positions[$location])) $err_message[] = array('field'=>'location', 'msg'=>'Location  did not exist');
                                    if (! isset($obj_positions[$location][$row])) $err_message[] = array('field'=>'row', 'msg'=>'Row does  did not exist');
                                    if (! isset($obj_positions[$location][$row][$rack])) $err_message[] = array('field'=>'rack', 'msg'=>'Rack  did not exist');
                                    if(! preg_match('!^[1-9][0-9]*$!', $racku)) $err_message[] = array('field'=>'rack u', 'msg'=>'format is invalid');
                                }
                            } else {
                                $err_message[] = array('field'=>'positions', 'msg'=>'Positions did not exist in db');
                            }
                            //*** import set new object (convert format) ***//
                            if (empty($err_message))
                                $new_objects[] = $newobject;
                        } else {
                            $is_verify = True;
                            if (! empty($data['location'])) {
                                if (empty($data['row'])){
                                    $is_verify = False;
                                    $err_message[] = array('field'=>'row', 'msg'=>'row is empty');
                                }

                                if (empty($data['rack'])) {
                                    $err_message[] = array('field'=>'rack', 'msg'=>'rack is empty');
                                    $is_verify = False;
                                }

                                if (empty($data['rack u'])) {
                                    /*if ($data['rack u'] == 0 || $data['rack u'] == '0') {
                                        if ($is_verify)
                                            $newobject['zerou'] = True;
                                    } else {*/
                                        $err_message[] = array('field'=>'rack u', 'msg'=>'rack u is empty');
                                        $is_verify = False;
                                    //}
                                }
                            }

                            if ($is_verify)
                                $new_objects[] = $newobject;

                            //$new_objects[] = $newobject;
                        }
                    } else {
                        $err_message[] = array('field'=> 'hardware type','msg'=>"hardware type don't exists.");
                    }
                } else {
                    if($next_check)
                        $err_message[] = array('fied'=> 'object type','msg'=>"object type don't exists.");
                }

                if (count($err_message)>0)
                    $err_rows[] = array('objtype'=>$row_objtype,'created'=>False,'name'=>$row_commname,'row'=> $row_index,'error'=>$err_message);

                $index++;
               // print_r($newobject);
            }
        } else {
            $err_message[] = array('fied'=> -1,'msg'=>"import abort(object type not found");
        }
        //$ret = $new_objects;
        //print_r($err_rows);
        /*if (self::$debug_mode) {
            putLog($new_objects);
        }*/

        return BZObject::import_procedure($new_objects,$err_rows);
    }

}
?>
