<?php
include ('DALSpace.php');

class BZSpace {

    //input : racks => integer array
    //output : float array
    public static function getSpaceUsage($racks,$attach_objects=False)
    {
        $total_height = 0;
        $ret = array();
        $objects = array();
        foreach ($racks as $rack_id) {
            $counter = array ('A' => 0, 'U' => 0, 'T' => 0, 'W' => 0, 'F' => 0);

            $data  = spotEntity ('rack', intval($rack_id));
            amplifyCell ($data);
            $objects[$rack_id] = $data['mountedObjects'];
            $total_height += $data['height'];
            for ($unit_no = $data['height']; $unit_no > 0; $unit_no--)
                for ($locidx = 0; $locidx < 3; $locidx++)
                    $counter[$data[$unit_no][$locidx]['state']]++;
            $percentage = ($counter['T'] + $counter['W'] + $counter['U']) / ($counter['T'] + $counter['W'] + $counter['U'] + $counter['F']);
            $done = ((int) ($percentage * 100));
            $ret[$rack_id] = $done;
        }
        if ($attach_objects) {
            return array($ret,$objects);
        } else {
            return $ret;
        }
    }
    
    public static function recursion($data)
    {
        $childs = array();   // 定義儲存子級資料陣列
        if (! empty($child['kids'])) {
            $childs = self::recursion($child['kids']);   // 遞回呼叫，查詢當前資料的子級              
        } 
        $childs[] = array('label'=>$child['label'],'id'=>$child['id']);  // 把子級資料新增進陣列
        return $childs;
    }

    public static function getEnMSLocationData()
    {
        //        echo $lid . "\n";
        $tmpIds = array();
        $locationlist = listCells ('location');
        foreach ($locationlist as $loc) {
            $tmpIds[] = intval($loc['id']);

        }
        sort($tmpIds);

        $ret = DALSpace::getLocationData($tmpIds);
        if (count($ret)>0) {
            foreach($ret as &$loc_data) {
                //echo "loc:" . $loc_data['location_name'] . "\n";
                $loc_usage = 0;
                foreach($loc_data['children'] as &$row_data) {
                    //echo "row:" . $row_data['row_name'] . "\n";
                    $row_total_usage = 0;
                    $keys = array_column($row_data['children'], 'rack_id');
                    $arrUsages = BZSpace::getSpaceUsage($keys);
                    foreach($arrUsages as $key => $percentage) {
                        $index = array_search($key, $keys);
                        $id = $row_data['children'][$index]['rack_id'];
                        if (intval($id) == intval($key)) {
                            $row_data['children'][$index]['usage'] = $percentage;
                            $row_total_usage = $row_total_usage + $percentage;
                        }
                    }
                }
            }
        }
        return $ret;
    }

    public static function getEnMSLocationData_test()
    {
        //        echo $lid . "\n";
        $tmpIds = array();
        $locationlist = listCells ('location');
        foreach ($locationlist as $loc) {
            $tmpIds[] = intval($loc['id']);

        }

        $loc_children = array();
        $loc_childs = treeFromList (addTraceToNodes ($locationlist));
        foreach($loc_childs as $child) {
            $id = strval($child['id']);
            if (empty($child['kids'])) {
                $loc_children[$id] = array('label'=>$child['label'],'id'=>$child['id']);
            } else {
                $loc_children[$id] = self::recursion($child['kids']);
            }
        }
        sort($tmpIds);

        $ret = DALSpace::getLocationData($tmpIds);
        if (count($ret)>0) {
            foreach($ret as &$loc_data) {
                //echo "loc:" . $loc_data['location_name'] . "\n";
                $loc_usage = 0;
                foreach($loc_data['children'] as &$row_data) {
                    //echo "row:" . $row_data['row_name'] . "\n";
                    $row_total_usage = 0;
                    $keys = array_column($row_data['children'], 'rack_id');
                    $arrUsages = BZSpace::getSpaceUsage($keys);
                    foreach($arrUsages as $key => $percentage) {
                        $index = array_search($key, $keys);
                        $id = $row_data['children'][$index]['rack_id'];
                        if (intval($id) == intval($key)) {
                            $row_data['children'][$index]['usage'] = $percentage;
                            $row_total_usage = $row_total_usage + $percentage;
                        }
                    }
                }
            }
        }
        return $ret;
    }

    //input : $lid => integer
    //output : array()
    public static function getLocationData($lids)
    {
        //        echo $lid . "\n";
        $tmpIds = NULL;
        if (strpos($lids, ',') !== false) {
            $tmpIds = explode(",",$lids);
        } else {
            if (! empty($lids))
                $tmpIds = array($lids);
        }

        $ret = DALSpace::getLocationData($tmpIds);
        if (count($ret)>0) {
            foreach($ret as &$loc_data) {
                //echo "loc:" . $loc_data['location_name'] . "\n";
                $loc_usage = 0;
                foreach($loc_data['children'] as &$row_data) {
                    //echo "row:" . $row_data['row_name'] . "\n";
                    $row_total_usage = 0;
                    if (count($keys)>0) {
                        $keys = array_column($row_data['children'], 'rack_id');
                        //print_r($keys);
                        $arrUsages = BZSpace::getSpaceUsage($keys);
                        foreach($arrUsages as $key => $percentage) {
                            $index = array_search($key, $keys);
                            $id = $row_data['children'][$index]['rack_id'];
                            //echo "rack:" . $row_data['children'][$index]['rack_name'] . "\n";
                            if (intval($id) == intval($key)) {
                                $row_data['children'][$index]['usage'] = $percentage;
                                $row_total_usage = $row_total_usage + $percentage;
                            }
                        }

                        $row_data['row_usage'] = ((int) ($row_total_usage / count($keys)));
                        $loc_usage = $loc_usage + $row_data['row_usage'];
                    } else {
                        $row_data['row_usage'] = 0;
                        $loc_usage = 0;
                    }
                }
                if ($loc_usage>0)
                    $loc_data['loc_usage'] = (round($loc_usage / count($loc_data['children'])));
                else
                    $loc_data['loc_usage'] = 0;
            }
        }
        return $ret;
    }




    //input : $lid => integer
    //output : array()
    public static function getLocationList($lid)
    {
        //echo $lid . "\n";
        $ret = DALSpace::getLocationInfo($lid);
        $loc_usage = 0;
        if (isset($ret['row_data'])) {
            foreach($ret['row_data'] as &$row_data) {
                $row_total_usage = 0;
                $keys = array_column($row_data['children'], 'rack_id');
                $arrUsages = BZSpace::getSpaceUsage($keys);
                foreach($arrUsages as $key => $percentage) {
                    $index = array_search($key, $keys);
                    $id = $row_data['children'][$index]['rack_id'];
                    if (intval($id) == intval($key)) {
                        $row_data['children'][$index]['usage'] = $percentage;
                        $row_total_usage = $row_total_usage + $percentage;
                    }
                }

                $row_data['row_usage'] = ((int) ($row_total_usage / count($keys)));
                $loc_usage = $loc_usage + $row_data['row_usage'];
            }
            $ret['loc_usage'] = (round($loc_usage / count($ret['row_data'])));
        }
        return $ret;
    }

    public static function getRowData($rid)
    {
        $ret = DALSpace::getRowData($rid);
        if (isset($ret['children'])) {
            $row_total_usage = 0;
            if (count($ret['children'])> 0) {
                $keys = array_column($ret['children'], 'rack_id');
                $arrUsages = BZSpace::getSpaceUsage($keys);
                foreach($arrUsages as $key => $percentage) {
                    $index = array_search($key, $keys);
                    $id = $ret['children'][$index]['rack_id'];
                    if (intval($id) == intval($key)) {
                        $ret['children'][$index]['usage'] = $percentage;
                        $row_total_usage = $row_total_usage + $percentage;
                    }
                }
                $ret['row_usage'] = (round($row_total_usage / count($keys)));
            } else
            $ret['row_usage'] = 0;
        }
        return $ret;
    }

    public static function getRackData($id) {
        $ret = DALSpace::getRackData($id);
        if (isset($ret['id'])) {
            /*$key = $ret['id'];
            list($arrUsages,$arrObjs) = BZSpace::getSpaceUsage(array($key),true);
            if (isset($arrUsages[$key])) {
                $ret['usage'] = $arrUsages[$key];
                $ret['objects'] = count($arrObjs[$key]);
            }*/
            $ret = BZSpace::getRackDetail($ret['id']);
        }
        return $ret;
    }

    public static function getRackDetail($rack_id,$selectId=0)
    {
        $counter = array ('A' => 0, 'U' => 0, 'T' => 0, 'W' => 0, 'F' => 0);
        $data  = spotEntity ('rack', intval($rack_id));
        amplifyCell ($data);
        markAllSpans ($data);
        //print_r($data);
        $ret = array('rack_id' => intval($data['id']),
                     'rack_name' => $data['name'],
                     'height' => intval($data['height']),
                     'row_id' => intval($data['row_id']),
                     'row_name' => $data['row_name'],
                     'location_id' => intval($data['location_id']),
                     'location_name' => htmlspecialchars_decode($data['location_name']));
            //get location tree data;
        $ret['locations'] = array();
        if (!empty($data['location_id'])) {
            $locData = DALSpace::getLocationDataAll();
            $loop_limit = 30;
            $loop_index = 0;
            $locationData = array();
            $cid = $data['location_id'];
            while(true) {
                if ($loop_index>$loop_limit || empty($cid))
                    break;
                $pid = $locData[$cid]['parent_id'];
                $locationData[$cid ] = array('id'=> intval($cid),'name' => htmlspecialchars_decode($locData[$cid]['name']));
                $cid = $pid;
                $loop_index++;
            }
            $ret['locations'] = array_reverse($locationData);
        }
        unset($ret['location_id']);
        unset($ret['location_name']);

        $total_height = intval($data['height']);
        //print_r($data['mountedObjects']);
        $ret['objects'] = count($data['mountedObjects']);
        $objIds = implode(",",$data['mountedObjects']);
        require_once('DALObject.php');
        $assetData = (empty($objIds))? array() : DALObject::getAssetIdByObject(trim($objIds,","),True);
        $assetKeys = array();
        $rows = array();
        /*foreach($assetData as $key => $data )
            echo $key." " . json_encode($data) . "\n";
        */
        $dict_objmap = array();
        for ($unit_no = $data['height']; $unit_no > 0; $unit_no--) {
            $unit = array();
            for ($locidx = 0; $locidx < 3; $locidx++) {
                $state = $data[$unit_no][$locidx]['state'];
                $counter[$data[$unit_no][$locidx]['state']]++;

                if($state == 'T') {
                    //$object_data = spotEntity('object', $data[$unit_no][$locidx]['object_id']);
                    $id = intval($data[$unit_no][$locidx]['object_id']);
                    $highlight = False;
                    if($selectId == $id) {
                        $highlight = True;
                    }

                    if (isset($assetData[$id])) {
                        $data[$unit_no][$locidx]['object_id'] = intval($id);
                        $data[$unit_no][$locidx]['name'] = $assetData[$id]['name'];
                        $data[$unit_no][$locidx]['label'] = $assetData[$id]['label'];
                        $data[$unit_no][$locidx]['type_id'] = intval($assetData[$id]['objtype_id']);
                        $data[$unit_no][$locidx]['type'] = $assetData[$id]['objtype'];
                        $data[$unit_no][$locidx]['asset_id'] = $assetData[$id]['asset_id'];
                        if (! empty($assetData[$id]['asset_id'])) {
                            if(! in_array($assetData[$id]['asset_id'],$assetKeys))
                                $assetKeys[] = $assetData[$id]['asset_id'];
                        }
                        $data[$unit_no][$locidx]['highlight'] = $highlight;
                        $data[$unit_no][$locidx]['host_name'] = "";
                        $data[$unit_no][$locidx]['host_id'] = 0;

                    } else {
                        $object_data = spotEntity('object', $id);
                        $data[$unit_no][$locidx]['object_id'] = intval($id);
                        $data[$unit_no][$locidx]['name'] = $object_data['name'];
                        $data[$unit_no][$locidx]['label'] = $object_data['label'];
                        $data[$unit_no][$locidx]['type_id'] = intval($object_data['objtype_id']);
                        $data[$unit_no][$locidx]['type'] = decodeObjectType ($object_data['objtype_id']);
                        $data[$unit_no][$locidx]['asset_id'] = $object_data['asset_no'];
                        $data[$unit_no][$locidx]['highlight'] = $highlight;
                        $data[$unit_no][$locidx]['host_name'] = "";
                        $data[$unit_no][$locidx]['host_id'] = 0;

                    }
                    //echo json_encode($object_data) . "\n";
                } else {
                    unset($data[$unit_no][$locidx]['object_id']);
                }
                unset($data[$unit_no][$locidx]['hl']);
                $unit[] = $data[$unit_no][$locidx];
            }
            $rows[] = $unit;
        }
        if(!empty($selectId)) {
            $objData = DALObject::getObjectRackData('object',"'". $selectId ."'");
            if(isset($objData[$selectId])) {
                $ret['position'] = $objData[$selectId]['units'];
                $ret['name'] = $objData[$selectId]['object_name'];
                $ret['label'] = $objData[$selectId]['label'];
            }
        }

        $ret['asset_keys'] = $assetKeys;
        $ret['table'] = $rows;
        $percentage = ($counter['T'] + $counter['W'] + $counter['U']) / ($counter['T'] + $counter['W'] + $counter['U'] + $counter['F']);
        $ret['usage']  = ((int) ($percentage * 100));
        return $ret;
    }


    //=========================================================
    //=======================  FiMo API  ======================
    //=========================================================
    public static function getPositionByAssetId($asset_id)
    {
        $ret = array();
        require_once('DALObject.php');
        $rack_data = DALObject::getObjectRackData('asset',$asset_id,TRUE);
        //echo json_encode($rack_data) . "\n";
        if (! empty($rack_data)) {
            $data = BZSpace::getObjectRackDetail($rack_data);
            $ret['rack_table'] = $data;
            $ret['rack_files'] = array();
        }
        return $ret;
    }

    public static function getPositionByObjectId($object_id)
    {
        $ret = array();
        require_once('DALObject.php');
        $rack_data = DALObject::getObjectRackData('object',$object_id,TRUE);
        if (! empty($rack_data)) {
            //echo json_encode($rack_data) . "\n";

            $data = BZSpace::getObjectRackDetail($rack_data);
            $ret['rack_table'] = $data;
            $ret['rack_files'] = array();
        }
        return $ret;
    }

    public static function getObjectRackDetail($object_data)
    {
        $data = array();
        foreach($object_data as $objrack) {
            //print_r($objrack);
        //foreach (getResidentRackIDs ($object_id) as $rack_id)
        //{
            if ((!empty($objrack['rack_id'])) && (!empty($objrack['object_id']))) {
                $object_id = intval($objrack['object_id']);
                $zerou_object = array();
                //if ($objrack['zerou']) {
                $zerou_object = DALSpace::getZerouRackObjects($objrack['rack_id']);
                foreach ($zerou_object as &$object) {
                    $highlight = ($object['object_id'] == $object_id)? True : False;
                    $object['highlight'] = $highlight;
                }
                //}
                $rackData = BZSpace::getRackDetail($objrack['rack_id'],$object_id);
                $rackData['zerou_objects'] = $zerou_object;
            } else {
                $rackData = array('rack_id' => NULL,
                                  'rack_name' =>  NULL,
                                  'height' =>  NULL,
                                  'row_id' =>  NULL,
                                  'row_name' =>  NULL,
                                  'locations' =>  array(),
                                  'objects' => 0,
                                  'position' => array(),
                                  'table' => array(),
                                  'usage' => 0);
            }
            //print_r($info);
            //amplifyCell ($info);
            $rackData['zerou'] = $objrack['zerou'];
            $rackData['id'] = (isset($objrack['id']))? $objrack['id']:$objrack['object_id'];
            $rackData['id'] = intval($rackData['id']);
            $rackData['name'] = (isset($objrack['name']))? $objrack['name']:$objrack['object_name'];
            $rackData['label'] = $objrack['label'];
            $rackData['type_id'] = (isset($objrack['objtype_id']))?$objrack['objtype_id']:$objrack['type_id'];
            $rackData['type_id'] = intval($rackData['type_id']);
            $rackData['type'] = decodeObjectType ($rackData['type_id']);
            $data[] = $rackData;
        }
        return $data;
    }

    //###### v2 base api ######
    public static function get_location_tree() {
        $ret = array();
        $locationlist = listCells ('location');
        foreach ($locationlist as &$loc) {
            $label = htmlspecialchars_decode($loc['name']);
            $loc['label'] = $label;
            $loc['id'] = intval($loc['id']);
            $loc['parent_id'] = intval($loc['parent_id']);
            unset($loc['name']);
            unset($loc['parent_name']);
            unset($loc['realm']);
            unset($loc['atags']);
            unset($loc['itags']);
            unset($loc['etags']);
            unset($loc['comment']);
            unset($loc['refcnt']);
        }

        $ret = treeFromList (addTraceToNodes ($locationlist));
        //print_r();
        if (count($ret)>0) {
            return array(True, $ret);
        } else {
            return array(False,'object type empty');
        }
    }

    public static function get_row_list($location_ids) {
        $ret = array();
        list($check,$resp) = verifyIntegerArrary($location_ids);
        if ($check) {
            $data = DALSpace::getRowList($resp);
            if(count($data)>0) {
                foreach ($data  as $row) {
                    $ret[] = array('id' => intval($row['id']),'label'=> $row['name']);
                }
            }
        } else
          $ret = $resp;
        return array($check, $ret);
    }

    public static function get_rack_list($row_ids) {
        $ret = array();
        list($check,$resp) = verifyIntegerArrary($row_ids);
        if ($check) {
            $data = DALSpace::getRackList($resp);
            if(count($data)>0) {
                foreach ($data  as $rack) {
                    $ret[] = array('id' => intval($rack['id']),'label'=> $rack['name']);
                }
            }
        } else
          $ret = $resp;
        return array($check, $ret);
    }

    public static function get_available_rack($rack_id) {
        $ret = array();
        if (! empty($rack_id)) {
            $ret = BZSpace::getRackDetail($rack_id);
        }
        return array(True, $ret);
    }

    public static function get_position_namelist($locations,$rows,$racks) {
        $ret = array();
        if (!empty($locations) && (!empty($rows)) && (!empty($racks))) {
            $templist = explode(",",$locations);
            $locations = "'".implode("','",$templist)."'";

            $templist = explode(",",$rows);
            $rows = "'".implode("','",$templist)."'";

            $templist = explode(",",$racks);
            $racks = "'".implode("','",$templist)."'";

            $ret = DALSpace::getPositionByName($locations,$rows,$racks);
        }
        return array(True, $ret);
    }

    public static function check_position_namelist($location,$row,$rack,$u,$data=array()) {
        if (isset($data[$location])) {
            if (isset($data[$location][$row])) {
                if (isset($data[$location][$row][$rack])) {
                    if (empty($data[$location][$row][$rack]['u'])) {
                        $ret = array('check'=> true);
                    } else {
                        $u = strval($u);
                        if (isset($data[$location][$row][$rack ]['u'][$u])) {
                            $ret = array('check'=> False,'error'=>'Rack u invalid');
                        } else {
                            $ret = array('check'=> true);
                        }
                    }
                } else $ret = array('check'=> false,'error'=>'Rack invalid');
            } else $ret = array('check'=> false,'error'=>'Row invalid');
        } else $ret = array('check'=> false,'error'=>'Location invalid');
        return $ret;
    }

}
?>
