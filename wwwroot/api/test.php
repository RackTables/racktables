<?php
$script_mode = TRUE;
include ('../inc/init.php');
include ('../inc/interface.php');
include ('../inc/popup.php');
//include ('DALObject.php');
include ('BZObject.php');
$terms = 'test';
$object_id = 6779;
$objtype_id = 4;

$obj_port_name = "test";
$port_type_id = 24;
$port_l2address = "300ED501E0A3";
$port_label = "test_label";
//$port_option = getNewPortTypeOptions();
$ret = array();
include ('DALSpace.php');

//$rid = DALSpace::getZerouRackByObjectId(7070);
$is_create = (DALSpace::checkZerouObjectExists(7079))?False:True;
$ret = array('create'=>$is_create);

/*$json_data = json_decode($filter,true);
if ($json_data['in_rack'])
    echo "in rack\n";*/
/*$reulst_check = False;
try
{
    $result = linkPorts(15675,15794,'test');
    if ($result > 0) $reulst_check = True;
} catch (Exception $e)
{
    echo $e->getMessage() . "\n";
}*/

/*$port_info = getPortInfo (15932);
$object = spotEntity ('object', $port_info['object_id']);
if ($object['rack_id']) // the object itself is mounted in a rack
    $filter['racks'] = getProximateRacks ($object['rack_id'], getConfigVar ('PROXIMITY_RANGE'));
elseif ($object['container_id']) // the object is not mounted in a rack, but it's container may be
{
    $container = spotEntity ('object', $object['container_id']);
    if ($container['rack_id'])
        $filter['racks'] = getProximateRacks ($container['rack_id'], getConfigVar ('PROXIMITY_RANGE'));
}*/
//print_r($port_info);
//print_r($filter);
//$ret = findSparePorts ($port_info, $filter);

/*foreach ($port_option as $category => $options) {
    $children = array();
    foreach ($options as $key => $lable) {
        if (strpos($key, '-') !== False ) {
            list($iif_id,$iftype_id) = explode("-",$key);
            $children[] = array('id'=>$iftype_id,'label'=>$lable);
        }
    }
    $ret[] = array('id' => null,
                   'label' => $category,
                   'children'=> $children
    );
}*/
//$ret = $portOption;
//$id = commitAddPort($object_id,$obj_port_name,$port_type_id, $port_label, $port_l2address);
//$ret = array('id'=>$id);
//$id = 15649;
//DELETE LINK
//$retid = commitUnlinkPort($id);
//$ret = array('retid'=>$retid);

/*
$json = '[{
    "HW Type": "Foxconn twins",
    "Visable Lable": "JCloudF4-Storage-HDD-22",
    "Common Name": null,
    "Tag": "KH-FiXo4",
    "Asset tag": "CN4038PCWR",
    "Model": "Twins",
    "PN": null,
    "Purpose": null,
    "Asset Name": "Twins-HDD Storage \u4f3a\u670d\u5668",
    "Asset ID": null,
    "Company ID": null,
    "Custodian Dept": null,
    "Custodian EID": "DC0013",
    "Custodian": "\u5433\u80b2\u9298",
    "Manufacturer": null,
    "Stock Date": null,
    "Status": null,
    "Confidentiality": null,
    "Integrity": null,
    "Availability": null,
    "Value": 0,
    "Weekness": null,
    "Threats": null,
    "Risk": 0,
    "UUID": null,
    "Serial Number": "CN4038PCWR",
    "Server Chassis Number": "CN4038PCWR",
    "Server Product Name": null,
    "Location": "\u9ad8\u8edf-B\u68df2F-\u96ea\u5c71\u96f2\u6a5f\u623f",
    "Row": "B",
    "Rack": "2F-B03",
    "Rack U": "13,14",
    "Object Type": "Server"
},    {
    "HW Type": "Foxconn groot",
    "Visable Lable": "JCloudF4-Compute-20",
    "Common Name": null,
    "Tag": "KH-FiXo4",
    "Asset tag": "CN4038PCY7",
    "Model": "Groot",
    "PN": null,
    "Purpose": null,
    "Asset Name": "Groot-Compute \u4f3a\u670d\u5668",
    "Asset ID": null,
    "Company ID": null,
    "Custodian Dept": null,
    "Custodian EID": "DC0013",
    "Custodian": "\u5433\u80b2\u9298",
    "Manufacturer": null,
    "Stock Date": null,
    "Status": null,
    "Confidentiality": null,
    "Integrity": null,
    "Availability": null,
    "Value": 0,
    "Weekness": null,
    "Threats": null,
    "Risk": 0,
    "UUID": null,
    "Serial Number": "CN4038PCY7",
    "Server Chassis Number": "CN4038PCY7",
    "Server Product Name": null,
    "Location": null,
    "Row": null,
    "Rack": null,
    "Rack U": 6,
    "Object Type": "Server"
},
{
    "HW Type": "Bitway S8810-32Q",
    "Visible Label": "SnowMT-B01-S8810-G1",
    "Common Name": "S8810_FER1AA7C00013",
    "Tag": "KH-FiXo4,40G",
    "Asset tag": "FER1AA7C00013",
    "Model": "S8810-32Q",
    "PN": "S8810-32Q",
    "Purpose": null,
    "Asset Name": null,
    "Asset ID": null,
    "Company ID": null,
    "Custodian Dept": null,
    "Custodian EID": null,
    "Custodian": null,
    "Manufacturer": null,
    "Stock Date": null,
    "Status": "\u6b63\u5e38\u53ef\u4f7f\u7528",
    "Confidentiality": null,
    "Integrity": null,
    "Availability": null,
    "Value": 0,
    "Weekness": null,
    "Threats": null,
    "Risk": 0,
    "Serial Number": "FER1AA7C00013",
    "Number of ports": 32,
    "SW version": "2.1.7",
    "Location": "\u9ad8\u8edf-B\u68df2F-\u96ea\u5c71\u96f2\u6a5f\u623f",
    "Row": "B",
    "Rack": "2F-B01",
    "Rack U": 50,
    "Object Type": "Network switch"
}
]';

$json = '[{
    "HW Type": "Bitway S8810-32Q",
    "Visible Label": "SnowMT-B01-S8810-G1",
    "Common Name": "S8810_FER1AA7C00013",
    "Tag": "KH-FiXo4,40G",
    "Asset tag": "FER1AA7C00013",
    "Model": "S8810-32Q",
    "PN": "S8810-32Q",
    "Purpose": null,
    "Asset Name": null,
    "Asset ID": null,
    "Company ID": null,
    "Custodian Dept": null,
    "Custodian EID": null,
    "Custodian": null,
    "Manufacturer": null,
    "Stock Date": null,
    "Status": "\u6b63\u5e38\u53ef\u4f7f\u7528",
    "Confidentiality": null,
    "Integrity": null,
    "Availability": null,
    "Value": 0,
    "Weekness": null,
    "Threats": null,
    "Risk": 0,
    "Serial Number": "FER1AA7C00013",
    "Number of ports": 32,
    "SW version": "2.1.7",
    "Location": "\u9ad8\u8edf-B\u68df2F-\u96ea\u5c71\u96f2\u6a5f\u623f",
    "Row": "B",
    "Rack": "2F-B01",
    "Rack U": 50,
    "Object Type": "Network switch"
}
]';

$json_data = json_decode($json,true);
//print_r($json_data);
$obj_types_arr = array();
$obj_tags_arr = array();
$obj_loc_arr = array();
$obj_row_arr = array();
$obj_rack_arr = array();
$position_count = 0;
$err_message = array();
$object_data = array();
$index = 0;
$objects = array();
foreach ($json_data AS $data) {
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
                } else $message = array('index'=> $index,'msg'=>"field \"{$field}\" length is too big");
            } else $message = array('index'=> $index,'msg'=>"field \"{$field}\" empty");

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
        $err_message[] = array('index'=> $index,'msg'=>"format invalid");
        $check_result = False;
    }

    if (! $check_result) {
        $index++;
        continue;
    }
    
    //collect tags procedure
    if (! empty($object_data['tag'])) {
        if (strpos($object_data['tag'], ',') !== false) {
            $tag_arr = explode(",",$object_data['tag']);
            $obj_tags_arr = array_merge($obj_tags_arr,$tag_arr);
            $object_data['tag'] = $tag_arr;
        } else 
            $obj_tags_arr[] = $object_data['tag'];
    }

    //collect position procedure
    if ((! empty($object_data['location'])) && (! empty($object_data['row'])) && (! empty($object_data['rack']))) {
        if (! in_array($object_data['location'], $obj_loc_arr)) $obj_loc_arr[] = $object_data['location'];
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
    
    //$ret = BZObject::get_objtype_hwlist($types)[1];
    $obj_hwtypes = BZObject::get_objtype_hwlist($types)[1];
    if (! empty($obj_tags_arr)) {
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
        //$ret = BZSpace::get_position($locations,$rows,$racks)[1];
    }

    //convert racktable format
    $new_objects = array();
    foreach ($objects AS $index => $data) {
        //print_r($data);
        $newobject = $object;
        $obj_type_name = $data['object type'];
        $obj_hw_name = $data['hw type'];

        //check object exists
        if (isset($obj_columns[$obj_type_name]['HW Type'])) {
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
                $newobject['name'] = (empty($data['common name']))? $data['visable lable'] : $data['common name'];
                $newobject['label'] = $data['visible label'];
                $newobject['asset_tag'] = $data['asset tag'];
                //set propertys
                $prop_index = 0;
                //print_r($obj_type_data);
                foreach ($obj_type_data as $key => $info) {
                    $field = strtolower($key);
                    if ($field == 'hw type') continue;                    
                    if (isset($data[$field])) {
                        $value = NULL;
                        if ($info['type'] == 'date') {
                            $tsdatetime = DateTime::createFromFormat('Y/m/d', $data[$field]);
                            $value = $tsdatetime->getTimestamp();
                        } elseif ($info['type'] == 'dict') {
                            $itme_value = $data[$field];
                            if (isset($obj_dicts[$field][$itme_value])) {                                
                                //print_r($obj_dicts[$field][$itme_value]);
                                $value = $obj_dicts[$field][$itme_value]['dict_key'];                                
                            }
                        } elseif ($info['type'] == 'float') {
                            $value = floatval($data[$field]);
                        } elseif ($info['type'] == 'uint') {
                            $value = intval($data[$field]);                   
                        } else {
                            $value = strval($data[$field]);
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
                    //print_r($obj_tags[$tag]);
                    if (is_array($tag)) {
                        foreach ($tag as $tag_item) {
                            $newobject['tags'][] = array('id' => $obj_tags[$tag_item]['id']);
                        }
                    } else {
                        if (isset($obj_tags[$tag]))                     
                            $newobject['tags'][] = array('id' => $obj_tags[$tag]['id']);
                    }
                }
                //set positions
                if ((! empty($data['location'])) && (! empty($data['row'])) && (! empty($data['rack']))) {
                    if (count($obj_positions) > 0) {
                        $location = $data['location'];
                        $row = $data['row'];
                        $rack = $data['rack'];
                        $ru = $data['rack u'];
                        //echo "{$location} , {$rack} , {$rack} , {$ru} \n";
                        if (isset($obj_positions[$location][$row][$rack])) {     
                            $position_data = $obj_positions[$location][$row][$rack];
                            $newobject['location_id'] = intval($position_data['location_id']);
                            $newobject['row_id'] = intval($position_data['row_id']);
                            $newobject['rack_id'] = intval($position_data['rack_id']);   
                         
                            if (! empty($ru)) {
                                $racku_empty = array("F"=>true, "I"=>true, "R"=>true); 
                               // echo json_encode($ru);
                                if (is_array($ru)) {
                                    ///echo "array ru\n";
                                    //print_r($ru);
                                    foreach ($ru as $racku) {
                                        if (BZSpace::check_position_namelist($data['location'],$data['row'],$data['rack'],$racku,$obj_positions))
                                            $newobject['units'][$racku] = $racku_empty;
                                    }
                                } else {
                                    $racku = $data['rack u'];
                                    if (BZSpace::check_position_namelist($data['location'],$data['row'],$data['rack'],$data['rack u'],$obj_positions))
                                        $newobject['units'][$racku] = $racku_empty;
                                }
                            }
                        }
                    }
                }

                
                $new_objects[] = $newobject;
            } else {
                $err_message[] = array('index'=> $index,'msg'=>"hardware type don't exists.");
            }
        } else {
            $err_message[] = array('index'=> $index,'msg'=>"object type don't exists.");
        }
        $index++;
    }
} else {
    $err_message[] = array('index'=> -1,'msg'=>"import abort(object type not found");
}

$ret = $new_objects;
//$ret = BZObject::create_objects(json_encode($new_objects));
*/


//$ret = DALObject::getDictAttrValueListByName(NULL,"'Status','HW type'");
/*
$data = DALSpace::getPositionByName("'備品庫','KH'","'MCDCT3','CPU'","'Rack12','AMD CPU','Intel CPU'");
$location = '備品庫';
$row = 'CPU';
$rack = 'Intel CPU';
$u = 42;
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
 
//$ret = DALObject::getObjectTypeList("50019,50050,50053,50015");*/
/*
$filter = array ('objtype' => array (0 => 4,
                                     1 => 8),
                 'hwtype' => array(0 => 1414),
                 'tags' => array (0 => 41,
                                  1 => 8),
                 'location' => array (1105),
                 'row' => array (),
                 'rack' => array ());


$ret = DALObject::getObjectList("",$filter,NULL,0,15);*/

//$ret = getAttrValues($object_id);
//$ret = spotEntity ('rack', 104);
//amplifyCell ($ret);

/*$rack_data = DALObject::getObjectRackData('object',$object_id);
if (! empty($rack_data))
    $ret = $rack_data[strval($object_id)]['units'];
else 
    $ret = array();*/
    
/*
$json_data = array (
                array (
                'asset_tag' => '72EFA4PECD',
                'locations' => 3,
                'row_id' => '7',
                'rack_id' => '104',
                'units' => 
                array (
                    28 => 
                    array (
                    'F' => true,
                    'I' => true,
                    'R' => true,
                    ),
                    29 => 
                    array (
                    'F' => true,
                    'I' => true,
                    'R' => true,
                    ),
                ),
                'name' => 'R1206_Compute-Terrance',
                'label' => 'R1206_R0TestTerrance',
                'objtype_id' => 4,
                'hardware_type_id' => 50010,
                'tags' => 
                array (
                    0 => 
                    array (
                    'id' => '41',
                    ),
                    1 => 
                    array (
                    'id' => '44',
                    ),
                ),
                'property' => 
                array (
                    0 => 
                    array (
                    'id' => '1',
                    'value' => NULL,
                    ),
                    1 => 
                    array (
                    'id' => '2',
                    'value' => 50010,
                    ),
                    2 => 
                    array (
                    'id' => '3',
                    'value' => 'Compute-Test',
                    ),
                    3 => 
                    array (
                    'id' => '4',
                    'value' => 2404,
                    ),
                    4 => 
                    array (
                    'id' => '14',
                    'value' => NULL,
                    ),
                    5 => 
                    array (
                    'id' => '21',
                    'value' => NULL,
                    ),
                    6 => 
                    array (
                    'id' => '25',
                    'value' => '72EFA4PECD',
                    ),
                    7 => 
                    array (
                    'id' => '26',
                    'value' => NULL,
                    ),
                    8 => 
                    array (
                    'id' => '28',
                    'value' => NULL,
                    ),
                    9 => 
                    array (
                    'id' => '10004',
                    'value' => NULL,
                    ),
                    10 => 
                    array (
                    'id' => '10011',
                    'value' => '72EFA4PECD',
                    ),
                    11 => 
                    array (
                    'id' => '10022',
                    'value' => NULL,
                    ),
                    12 => 
                    array (
                    'id' => '10025',
                    'value' => NULL,
                    ),
                    13 => 
                    array (
                    'id' => '10026',
                    'value' => NULL,
                    ),
                    14 => 
                    array (
                    'id' => '10027',
                    'value' => NULL,
                    ),
                    15 => 
                    array (
                    'id' => '10028',
                    'value' => NULL,
                    ),
                    16 => 
                    array (
                    'id' => '10029',
                    'value' => NULL,
                    ),
                    17 => 
                    array (
                    'id' => '10030',
                    'value' => NULL,
                    ),
                    18 => 
                    array (
                    'id' => '10031',
                    'value' => NULL,
                    ),
                    19 => 
                    array (
                    'id' => '10032',
                    'value' => NULL,
                    ),
                    20 => 
                    array (
                    'id' => '10033',
                    'value' => NULL,
                    ),
                    21 => 
                    array (
                    'id' => '10034',
                    'value' => NULL,
                    ),
                    22 => 
                    array (
                    'id' => '10035',
                    'value' => NULL,
                    ),
                    23 => 
                    array (
                    'id' => '10038',
                    'value' => NULL,
                    ),
                    24 => 
                    array (
                    'id' => '10039',
                    'value' => NULL,
                    ),
                    25 => 
                    array (
                    'id' => '10040',
                    'value' => NULL,
                    ),
                ),
                'children' => 
                array (
                    'HDD' => 
                    array (
                    0 => 
                    array (
                        'id' => '1355',
                        'name' => 'disk_00000004',
                        'label' => 'SG_HDD',
                        'objtype_id' => '50047',
                        'asset_tag' => NULL,
                    ),
                    1 => 
                    array (
                        'id' => '1356',
                        'name' => 'disk_00000005',
                        'label' => 'SG_HDD',
                        'objtype_id' => '50047',
                        'asset_tag' => NULL,
                    ),
                    ),
                ),
                'parents' => 
                array (
                    'Server chassis' => 
                    array (
                    0 => 
                    array (
                        'id' => '1122',
                        'name' => 'HGX-1-CIT-1',
                        'label' => NULL,
                        'asset_tag' => NULL,
                        'objtype_id' => '1502',
                    ),
                    1 => 
                    array (
                        'id' => '1335',
                        'name' => 'Yusheng1_serverchassis',
                        'label' => NULL,
                        'asset_tag' => NULL,
                        'type_id' => '1502',
                    ),
                    ),
                ),
                'nat4' => 
                array (
                    'out' => 
                    array (
                    ),
                    'in' => 
                    array (
                    ),
                ),
                'files' => 
                array (
                ),
                'ips' => 
                array (
                    0 => 
                    array (
                    'osif' => 'Data',
                    'type' => 'regular',
                    'ip' => '192.168.8.242',
                    'comment' => '',
                    'reserved' => '',
                    'network' => 
                    array (
                        'realm' => 'ipv4net',
                        'id' => '94',
                        'name' => 'New_JCloud_Storage',
                        'comment' => NULL,
                        'ip' => '192.168.8.0',
                        'mask' => '24',
                        'routed_by' => '',
                    ),
                    ),
                    1 => 
                    array (
                    'osif' => 'Host',
                    'type' => 'regular',
                    'ip' => '172.16.5.142',
                    'comment' => '',
                    'reserved' => '',
                    'network' => 
                    array (
                        'realm' => 'ipv4net',
                        'id' => '86',
                        'name' => 'New_JCloud_BMC',
                        'comment' => NULL,
                        'ip' => '172.16.5.0',
                        'mask' => '24',
                        'routed_by' => '',
                    ),
                    ),
                    2 => 
                    array (
                    'osif' => 'IPMI',
                    'type' => 'regular',
                    'ip' => '172.16.5.42',
                    'comment' => '',
                    'reserved' => '',
                    'network' => 
                    array (
                        'realm' => 'ipv4net',
                        'id' => '86',
                        'name' => 'New_JCloud_BMC',
                        'comment' => NULL,
                        'ip' => '172.16.5.0',
                        'mask' => '24',
                        'routed_by' => '',
                    ),
                    ),
                ),
                )
            );*/
//echo json_encode($json_data);
//$ret = getLocationChildrenList(3);
//$ret = $json_data;


/*
$index = 0;
foreach ($json_data as $data) {
    //echo $data['asset_tag'] ."\n";
    $field_name = '';
    // cechk process
    // check attribute (system)
    $check_result  = False;
    if (empty($data['objtype_id'])) {
        $field_name = 'object type';
        continue;
    } elseif (empty($data['objtype_id'])) {
        continue;
    } elseif (strlen($data['name']) > 255) {
        $field_name = 'Common name';
        continue;
    } elseif (strlen($data['label']) > 255)  {
        $field_name = 'label';    
        continue;    
    } elseif (strlen($data['asset_tag']) > 64) {
        $field_name = 'asset_no';
        continue;
    }    

    // check attribute (common + object type)
    $objtype_id = intval($data['objtype_id']);
    $objectAttr = getObjTypeAttrMap($objtype_id);
    $check_result = True;
    $propertys = $data['property'];
    $has_position = False;
    foreach ($propertys as $property) {
        if (! empty($property['value'])) {
            $field_name = '';
            $attr_id = $property['id'];
            if (isset($objectAttr[$attr_id])) {
                $field_name = $objectAttr[$attr_id]['name'];
                $type = $objectAttr[$attr_id]['type'];
                $attr_value = $property['value'];
                // 'string','uint','float','dict','date'
                if ($type == 'dict') {
                    $attr_value = intval($attr_value);
                    $chapter_id = $objectAttr[$attr_id]['chapter_id'];                
                    $chapter_data = readChapter ($chapter_id, $style = '');
                    if (! empty($chapter_data)) {
                        if (! isset($chapter_data[$attr_value])){
                            $check_result  = False;
                            break;
                        }
                    }
                } elseif (($type == 'date') || ($type == 'uint') ||  ($type == 'float')) {
                    if (! is_numeric($attr_value)) {
                        $check_result  = False;
                        break;
                    }

                    if ($type == 'float') {
                        if (! is_float($attr_value)) {
                            $check_result  = False;
                            break;
                        }
                    } else {
                        $attr_value = intval($attr_value);
                        if ($attr_value > 4294967295) {
                            $check_result  = False;
                            break;
                        }
                    }


                } else {
                    $value_len =strlen($attr_value);
                    if ($value_len > 255) {
                        $check_result  = False;
                        break;
                    }
                }          
            }
        }
    }    

    // make error message 
    $result = array($index => array('id'=> NULL, 'created'=> $check_result, 'error' => ''));
    if ($check_result == false) {
        $result[$object_id]['created'] = false;   
        $result[$object_id]['error'] = $field_name . " invalid";
        continue;
    }     

    // check position
    $location_id = $data['locations'][0]['id'];       
    $check_position = False;
    $has_position = False;
    if (! empty($data['locations'])) {
        $check_position = False;
        $location_id = $data['locations'][0]['id'];
        $loc_list = getLocationChildrenList ($location_id, array ());
        $err_message = "";
        $field_name = "";
        //echo json_encode($loc_list);
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
        } elseif (count($loc_list) == 1) {
            $check_position = True;
        } 

        if ($check_position) {
            if (is_numeric($location_id)) { 
                $field_name = 'row_id';
                $err_message = "row invalid";
                $row_list = getRows($location_id);
                //echo json_encode($row_list);
                $row_id =  strval($data['row_id']);
                if (isset($row_list[$row_id])) {
                    $field_name = 'rack_id';
                    $err_message = "rack invalid";    
                    $rack_list = getRacks (intval($row_id));
                    //echo json_encode($rack_list);
                    $rack_id = strval($data['rack_id']);
                    if (isset($rack_list[$rack_id])) {
                        $field_name = "unit no:". $u_id;
                        $err_message = "unit position invalid";    
                        $racku_data = spotEntity ('rack', intval($rack_id));
                        amplifyCell ($racku_data);
                        //echo json_encode($racku_data);
                        $ucheck= True;
                        foreach ($data['units'] as $unit_no => $unit_atom) {                     
                            $u_id = strval($unit_no);
                            $poscheck = True;
                            if (isset($racku_data[$u_id])) {
                                foreach (array ('F', 'I', 'R') as $pos) {
                                    if ($pos == 'F')
                                        $ustate = $racku_data[$u_id][0]['state'];
                                    elseif ($pos == 'I') 
                                        $ustate = $racku_data[$u_id][1]['state'];
                                    else 
                                        $ustate = $racku_data[$u_id][2]['state'];
                                    //echo $ustate;
                                    if ($unit_atom[$pos] && $ustate != "F") {
                                        $poscheck = False;
                                    }
                                }
                            } else { //unit invalid
                                $ucheck = False;
                                break;
                            }
                            
                            if ($poscheck==False) {
                                $field_name = "unit no:". $u_id;
                                $err_message = "unit position invalid"; 
                                $ucheck = False;
                                break;
                            }              
                            //echo "poscheck:" . json_encode($poscheck);
                            //echo "ucheck:" . json_encode($ucheck);
                            if ($ucheck == False) break;
                        }

                        if ($ucheck) {
                            $field_name = "";
                            $err_message = ""; 
                            $check_position = True;
                        } else 
                            $check_position = False;
                    } 
                } 
            }
        }

        $has_position=($check_position)? True: False;
        if ($has_position == false) {
            $result[$object_id]['created'] = false;   
            $result[$object_id]['error'] = "field :". $field_name . " ," .$err_message;
            continue;
        } 
    }

    // create object process     
    $object_id = NULL;
    if ($check_result) {
		try
		{
            $objtype_id = $data['objtype_id'];
            $name = $data['name'];
            $label = $data['label'];
            $asset_no = $data['asset_tag'];
            $rack_id = $data['rack_id'];
            $taglist = NULL;
            // add object 
            $object_id = commitAddObject($name, $label, $objtype_id, $asset_no, $taglist);

            if (count($data['tags'])>0 && (! empty($object_id))) {
                $taglist = array();
                foreach ($data['tags'] as $tag) 
                    foreach ($tag as $key => $value) {
                        $tag_id = intval($value);
                        addTagForEntity ('object', $object_id, intval($tag_id));
                    }
            }            

            if (is_numeric($object_id))  {
                //write attribute process 
                $tmp = getAttrValues ($object_id);
                foreach ($propertys as $property) {                
                    if (! empty($property['value'])) {
                        $attr_value = $property['value'];
                        $attr_id = intval($property['id']);
                        commitUpdateAttrValue ($object_id, $attr_id, $attr_value);
                    }
                }

                //setting positions process 
                if ( $has_position && (! empty($rack_id))) {
                    foreach ($data['units'] as $unit_no => $nuit_atom) {
                        foreach (array ('F', 'I', 'R') as $pos) {
                            if (isset($nuit_atom[$pos])) {
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
            $check_result = true;
            $result[$index]['id'] = $object_id;
        } catch (RackTablesError $e)
		{
            if ($check_result == false) {
                $result[$index]['id'] = $object_id;
                $result[$index]['created'] = false;   
                $message = (empty($object_id))? "FAILED CREATE OBJECT,": "FAILED '${$object_id}': ";
                $result[$index]['error'] = $message . $e->getMessage();
                continue;
            }         
		}
    }
    $index++;
}

$ret = array_values($result);
*/

/*$has_position = False;
$err_message = "";
$field_name = "";
foreach ($json_data as $data) {
    $check_position = False;
    $location_id = $data['locations'][0]['id'];
    $loc_list = getLocationChildrenList ($location_id, array ());
    //echo json_encode($loc_list);
    //print_r($loc_list);
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

    if ($check_position) {
        if (is_numeric($location_id)) { 
            $field_name = 'row_id';
            $err_message = "row invalid";
            $row_list = getRows($location_id);
            //echo json_encode($row_list);
            $row_id =  strval($data['row_id']);
            if (isset($row_list[$row_id])) {
                $field_name = 'rack_id';
                $err_message = "rack invalid";    
                $rack_list = getRacks (intval($row_id));
                //echo json_encode($rack_list);
                $rack_id = strval($data['rack_id']);
                if (isset($rack_list[$rack_id])) {
                    $field_name = "unit no:". $u_id;
                    $err_message = "unit position invalid";    
                    $racku_data = spotEntity ('rack', intval($rack_id));
                    amplifyCell ($racku_data);
                    //echo json_encode($racku_data);
                    $ucheck= True;
                    foreach ($data['units'] as $unit_no => $unit_atom) {                     
                        $u_id = strval($unit_no);
                        $poscheck = True;
                        if (isset($racku_data[$u_id])) {
                            foreach (array ('F', 'I', 'R') as $pos) {
                                if ($pos == 'F')
                                    $ustate = $racku_data[$u_id][0]['state'];
                                elseif ($pos == 'I') 
                                    $ustate = $racku_data[$u_id][1]['state'];
                                else 
                                    $ustate = $racku_data[$u_id][2]['state'];
                                //echo $ustate;
                                if ($unit_atom[$pos] && $ustate != "F") {
                                    $poscheck = False;
                                }
                            }
                        } else { //unit invalid
                            $ucheck = False;
                            break;
                        }
                        
                        if ($poscheck==False) {
                            $field_name = "unit no:". $u_id;
                            $err_message = "unit position invalid"; 
                            $ucheck = False;
                            break;
                        }              
                        //echo "poscheck:" . json_encode($poscheck);
                        //echo "ucheck:" . json_encode($ucheck);
                        if ($ucheck == False) break;

                    }

                    if ($ucheck) {
                        $field_name = "";
                        $err_message = ""; 
                        $check_position = True;
                    } else 
                        $check_position = False;
                } 
            } 
        }
    }

    $has_position=($check_position)? True: False;
}
$ret = array('success'=> $has_position,'field'=> $field_name,'error'=> $err_message);*/


//add tag

//$tagidlist = array (41,43);

//$ret = buildTagChainFromIds ($tagidlist);
//$result = rebuildTagChainForEntity('object',1458, buildTagChainFromIds ($tagidlist),true);
//$ret = array('result'=> $result);


/*
1: {
    realm: "location",
    etags: [ ],
    id: "1",
    name: "DCT IDC@Kaohsiung",
    has_problems: "no",
    comment: null,
    parent_id: null,
    parent_name: null,
    refcnt: "7",
    itags: [ ],
    atags: [
    {
    tag: "$locationid_1"
    },
    {
    tag: "$any_location"
    }
    ]
    },*/

/*$locationlist = listCells ('location');
foreach ($locationlist as &$loc) {
    $label = $loc['name'];
    $loc['label'] = $label;
    unset($loc['parent_name']);
    unset($loc['name']);
    unset($loc['realm']);    
    unset($loc['atags']);    
    unset($loc['itags']); 
    unset($loc['etags']); 
    unset($loc['comment']);
    unset($loc['refcnt']);dd
}

$ret = treeFromList (addTraceToNodes ($locationlist));*/
/*$data = getTagUsage();ddafds
foreach ($data AS &$tag) {
    if ($tag['is_assignable'] != 'yes')
        continue;
    $label = $tag['tag'];
    $tag['label'] = $label;
    unset($tag['tag']);
    unset($tag['is_assignable']);
    unset($tag['color']);
    unset($tag['description']);    
    unset($tag['refcnt']);    
}

$ret = treeFromList ($data);*/

//treeFromList (getTagUsage());

/*$data = getRows (3, $children = array ());
foreach ($data  as $id => $name) {
    $ret[] = array('id' => $id,'label'=> $name);
}*/
/*($row_id = 5;
$data = getRacks($row_id);
foreach ($data  as $id => $rack) {
    $ret[] = array('id' => $id,'label'=> $rack['name']);
}*/

/*$data = DALObject::getObjectAttrs(4,true);
if (! empty($data['dicts'])) {
    $arrIds = array_keys($data['dicts']);
    $arrIds = array_filter($arrIds);
    $ids = implode(",",$arrIds);
    //echo $ids;
    $dict_map = DALObject::getObjectAttrsDict($ids,True);
    //$ret = DALObject::getObjectAttrsDict($ids,True);

    foreach ($data['dicts'] as $chapter_id => $attr_id) {
        if (isset($data['attributes'][$attr_id])) {            
            $data['attributes'][$attr_id]['items'] = (isset($dict_map[$chapter_id]))? $dict_map[$chapter_id] : NULL;
        }
    }
    $ret = array_values($data['attributes']);
}*/
//list($success,$ret) = BZObject::get_object_detail($id);

/*try
{
    parseSearchTerms ($terms);
    // Discard the return value as searchEntitiesByText() and its retriever
    // functions expect the original string as the parameter.
}
catch (InvalidArgException $iae)
{
    showError ($iae->getMessage());
}

renderSearchResults ($terms, searchEntitiesByText ($terms));
*/


//$ret = searchEntitiesByText ($terms);
//foreach(searchEntitiesByText($terms) as $entity_key => $entity_info) {
//    echo "key: {$entity_key}   ";

    //print_r($entity_info);

//}

//$ret = readChapter (1);
//$ret = getPatchCableOIFCompat();
//$ret = getPatchCableConnectorCompat();
//$ret = getPatchCableHeapSummary();
//$ret = getChapterAttributes(10001);
//$ret = getPortInfo (67);
//ret = getPortIIFStats(1);
//$ret = getPortInterfaceCompat();
//$ret = getChapterList();
//$ret = getTagUsage();
//$ret = loadEntityTags ('object', 959);
//$ret = getAttrValues(959);
//$ret = fetchAttrsForObjects();
//$ret = getChapterRefc(CHAP_OBJTYPE,NULL);
//$ret = getResidentRackIDs(959);
//$ret = getMoleculeForObject(959);
//$ret = getRackMountsCount(9);
//$ret = getObjectContentsList(959);
//$ret = getLocationChildrenList(3);
//$ret = getTagChildrenList(86);
/*$objtype_id = 4;
$data = array('objtype_id' => 4,
              'name' => "test server object",
              'label' => "test server vs",
              'has_problems' => False,
              'tags' => array(),
              'asset_no'=> "",
              'comment' => NULL,
              'attributes' => array( array('id'=> 10004, 'val'=> ''),
                                     array('id'=> 10011, 'val'=> 'TEST UUID'),
              ));


//if (empty($data['name']) 
try
{
   
   $asset_no = (empty($data['asset_no']))? NULL : $data['asset_no'];
   
   if (!empty($data['tags'])) 
       $taglist = explode(",",$_POST["taglist"]); 
   else 
       $taglist = array();
    $attr_map = getObjTypeAttrMap(intval($data['objtype_id']));
    $attr_dict = array();
    
    foreach($attr_map as $attr) {
            $key = $attr['id'];        
            $attr_dict[$key] = $attr;
            unset($attr_dict[$key]['id']);
    }    
    
    foreach()*/

   //$retid = commitAddObject($name, $label, $objtype_id, $asset_no, $taglist);
  /*
   $ret = spotEntity('object', $object_id);
   echo json_encode($ret);

   global $dbxlink;
   $dbxlink->beginTransaction();
   
   $dbxlink->commit();*/

/*} catch (RTDatabaseError $e)
{
    $ret = array('status'=> 1,
                 'count' => 0,
                 'ret'=> spotEntity ('object', $object_id),
                 'message'=> 'Error creating object'.$new_name.' '. $e->getMessage());
}*/

//include ('DALObject.php');
//$object_id = 1415;
//$object_id = 1416;
//$object_id = 764;
//$object_id = 1113;

/*$summary = array();
$info = spotEntity ('object', $object_id);
amplifyCell ($info);
print_r($info);
$parents = getParents ($info, 'object');
// lookup the human-readable object type, sort by it
foreach ($parents as $parent_id => $parent)
    $parents[$parent_id]['object_type'] = decodeObjectType ($parent['objtype_id']);
$grouped_parents = groupBy ($parents, 'object_type');
ksort ($grouped_parents);
foreach ($grouped_parents as $parents_group)
{
    uasort ($parents_group, 'compare_name');
    $fmt_parents = array();
    foreach ($parents_group as $parent)
        $fmt_parents[] = array('id' => $parent['id'],
                               'name' => $parent['name'],
                               'label' => $parent['label'],
                               'type_id' => $parent['objtype_id'],
                               'type' =>  $parent['object_type'],
                               'rack_id' => $parent['rack_id']);
    $summary['parents'] = $fmt_parents;
}
$children = getChildren ($info, 'object');
foreach (groupBy ($children, 'objtype_id') as $objtype_id => $children_group)
{
    uasort ($children_group, 'compare_name');
    $fmt_children = array();
    foreach ($children_group as $child) {
        $fmt_children[] = $child;
    }    
    $summary['children'][] = $fmt_children;
}*/
/*
$assetId = "1A32L9P0040400004";

$workingRacksData = array();
require_once('BZSpace.php');
$objectRack = DALObject::getObjectRackByAssetId("'".$assetId."'");
foreach($objectRack as $objrack) {
//foreach (getResidentRackIDs ($object_id) as $rack_id)
//{
    $rackData = BZSpace::getRackDetail2($objrack['rack_id'],intval($objrack['object_id']));
    $workingRacksData[] = $rackData;
}*/
//}
//print_r($workingRacksData);
/*$assetData = DALObject::getObjectIdByAssetId(NULL,$assetId);
if (isset($assetData[0]['object_id'])) {
    
require_once('BZSpace.php');
$ret['rack'] = BZSpace::getRackDetail(871);
unset($ret['location']);
unset($ret['rack_id']);
unset($ret['row']);*/

//$ret = BZObject::getTopologyByAssetId($assetId);
//$object['attr'] = getAttrValues($object_id);

/* get ports */
/* calls getObjectPortsAndLinks */
//amplifyCell ($object);
//::getRackDataByAssetId

/*$assetData = DALObject::getObjectIdByAssetId(NULL,$assetId);
if (isset($assetData[0]['object_id'])) {
    $object_id = intval($assetData[0]['object_id']);
    $objectTypes = array();
    $ObjectIds = array();
    $linkData = DALObject::getLinkData($object_id);
    $index = 0;
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
    }

    $ids = implode(",",$objectTypes);
    $typeData = DALObject::getObjectTypeSet($ids);

    //print_r($ObjectIds);
    if (count($ObjectIds)>0) {
        $ids = implode(",",$ObjectIds);
        $objData = DALObject::getObjectRackData('object',$ids);
    }

    foreach($linkData as $link) {
        $objtname = NULL;
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

        $remoteinfo = array();
        $remoteid = $link['remote_object_id'];    
        if (isset($objData[$remoteid])) 
            $remoteinfo = $objData[$remoteid];

        $data = $link;
        $newobject = array('id' => $link['object_id'],
                            'name' => $link['object_name'],
                            'type_id' => $link['objtype_id'],                            
                            'type_name' => $objtname);

        $data['object'] = array_merge($newobject, $objinfo);

        $newremote_object = array('id' => $link['remote_object_id'],
                                'name' => $link['remote_object_name'],                            
                                'type_id' => $link['remote_objtype_id'],
                                'type_name' => $remotetname);

        $data['remote_object'] = array_merge($newremote_object,  $remoteinfo);                            
        $data['ip4'] = NULL;
        unset($data['object_id']);
        unset($data['object_name']);
        unset($data['objtype_id']);
        unset($data['remote_object_id']);
        unset($data['remote_object_name']);
        unset($data['remote_objtype_id']);
        unset($data['log_count']);
        unset($data['user']);
        unset($data['time']);
        $ret[] = $data;
    }
}*/
#$ret = $object['ports'];



//$html = renderRack(877);
//echo $html;
//$assetId = "1A32L9P0040400004";
//$ret = BZObject::getObjectPropertys('7CE529P09B');

/*$ids = "7CE524P0EL";

$tmpIds = array();
if (strpos($ids, ',') !== false) {
    $tmpIds = explode(",",$ids);
} else {
    $tmpIds = array($ids);
}

$assetIds = "'".implode("','",$tmpIds)."'";

$data = DALObject::getObjectRackData('asset','asset',$assetIds);
//print_r($data);
$objIdArr = array_keys($data);
if (count($objIdArr)>0) {
    require_once('DALSpace.php');
    require_once('DALObject.php');
    $objIds = "'".implode("','",$objIdArr)."'";    
    $ret = array();
    foreach($data as $objId => $objData) {    
        $assetId = $objData['assetId'];
        if (!empty($assetId)) {        
            $ret[$assetId] = $objData;
            //get location tree data; 
            $ret[$assetId]['locations'] = array();
            if (!empty($objData['location_id'])) {                    
                $locData = DALSpace::getLocationDataAll();
                $loop_limit = 30;
                $loop_index = 0;
                $locationData = array();
                $cid = $objData['location_id'];
                while(true) {
                    if ($loop_index>$loop_limit || empty($cid))
                        break;
                    $pid = $locData[$cid]['parent_id'];     
                    $locationData[$cid ] = array('id'=> $cid,'name' => $locData[$cid]['name']);
                    $cid = $pid;
                    $loop_index++;
                }
                $ret[$assetId]['locations'] = array_reverse($locationData);            
            }
            unset($ret[$assetId]['location_id']);
            unset($ret[$assetId]['location_name']);
        }
    }
}
*/

//$assetId = "7CE524P0CN";

//$ret = BZObject::getDetail2($assetId);
/*
$rackData = spotEntity ('rack', 871);
amplifyCell ($rackData);
markAllSpans ($rackData);
setEntityColors ($rackData);*/
/*require_once('BZSpace.php');
$ret['rack'] = BZSpace::getRackDetail(871);
unset($ret['location']);
unset($ret['rack_id']);
unset($ret['row']);*/


/*$rackId = 877;
//A This rackspace does not exist / U Problematic rackspace, you CAN\'T mount here / T to used / F Free rackspace / W Current Object 
$counter = array ('A' => 0, 'U' => 0, 'T' => 0, 'W' => 0, 'F' => 0);
$data  = spotEntity ('rack', intval($rackId));
amplifyCell ($data);
$ret = array('rack_id' => $data['id'],
             'rack_name' => $data['name'],
             'height' => $data['height'],
             'row_id' => $data['row_id'],
             'row_name' => $data['row_name'],
             'location_id' => $data['location_id'],
             'location_name' => $data['location_name']);
$total_height += $data['height'];
$ret['objects'] = count($data['mountedObjects']);
$rows = array();
for ($unit_no = $data['height']; $unit_no > 0; $unit_no--) {    
    $unit = array();
    for ($locidx = 0; $locidx < 3; $locidx++) {         
         $state = $data[$unit_no][$locidx]['state'];         
         if($state == 'T') {
            $objectData = spotEntity('object', $data[$unit_no][$locidx]['object_id']);
            $data[$unit_no][$locidx]['name'] = $objectData['name'];
            //echo json_encode($objectData) . "\n";
        } else {
            unset($data[$unit_no][$locidx]['object_id']);
        }
        unset($data[$unit_no][$locidx]['hl']);
        $unit[] = $data[$unit_no][$locidx];
    }
    $rows[] = $unit;
}
$ret['table'] = $rows;
$percentage = ($counter['T'] + $counter['W'] + $counter['U']) / ($counter['T'] + $counter['W'] + $counter['U'] + $counter['F']);
$ret['usage']  = ((int) ($percentage * 100));
*/
//list($ret,$objects) = BZSpace::getSpaceUsage(array(877),true);
//print_r($objects);

//$ret = BZSpace::getRackInfo(877);
/*if (isset($ret['id'])) {
    $key = $ret['id'];
    list($arrUsages,$arrObjs) = BZSpace::getSpaceUsage(array($key),true);
    if (isset($arrUsages[$key])) {
        $ret['usage'] = $arrUsages[$key];
        $ret['objects'] = count($arrObjs[$key]);
    }
}*/

//$ret = BZSpace::getRowInfo(5);
/*if (isset($ret['children'])) {
    $row_total_usage = 0;
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
}*/

//$ret = BZSpace::getLocationInfo(3);
/*
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
    $ret['loc_usage'] = ((int) ($loc_usage / count($ret['row_data'])));
    
}*/
//echo "\n";

//$output = scanRealmByText ('row'); // 5.1

//$output = scanRealmByText ('rack'); // 5.1
/*$ret = array();
$total_height = 0;
foreach (array(1190,1191,1192,1193) as $rackId) {
    $counter = array ('A' => 0, 'U' => 0, 'T' => 0, 'W' => 0, 'F' => 0);
    
    $data  = spotEntity ('rack', $rackId);
    amplifyCell ($data);
    //echo json_encode($data);
    $total_height += $data['height'];
    for ($unit_no = $data['height']; $unit_no > 0; $unit_no--)
        for ($locidx = 0; $locidx < 3; $locidx++)
            $counter[$data[$unit_no][$locidx]['state']]++;

    $percentage = ($counter['T'] + $counter['W'] + $counter['U']) / ($counter['T'] + $counter['W'] + $counter['U'] + $counter['F']);
    $done = ((int) ($percentage * 100));
    $ret[$rackId] = $done;
}*/
/*
if (count($data) > 0) {
    $id = $data[0]['id'];
    $ret = $data[0];
    $ret['ports'] = getObjectPortsAndLinks($id, FALSE);
    $ret['nat4'] = getNATv4ForObject($id);
    $ret['files'] = getFilesOfEntity('object', $id);
    
    $ipa = array();    
    $ipa['ipv4'] = getObjectIPv4Allocations($id);
    $ipa['ipv6'] = getObjectIPv6Allocations($id);    
	foreach (array ('ipv4', 'ipv6') as $ip_v)
    {
        
        foreach ($ipa[$ip_v] as $ip_bin => $alloc) {
            $allocs_by_iface[$alloc['osif']][$ip_bin] = $alloc;            
        }
    }
    
    $ret['ips'] = array();
    foreach (sortPortList ($allocs_by_iface) as $iface_name => $alloclist)
	{
			$is_first_row = TRUE;                                    
			foreach ($alloclist as $alloc)
			{               
                $entity = array();
                $entity['osif'] = $iface_name;
                $entity['type'] = $alloc['type'];
                $entity['ip'] = $alloc['addrinfo']['ip'];
                $entity['comment'] = $alloc['comment'];
                $entity['reserved'] = $alloc['reserved'];
                $ip_bin = $alloc['addrinfo']['ip_bin'];
                    $network = spotNetworkByIP($ip_bin);
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
                    
                $ret['ips'][] = $entity;
			}
                    
    }                
}*/

//var_dump($ret);
//print_r($ret);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($ret);

?>