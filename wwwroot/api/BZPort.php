<?php
include ('DALPort.php');
class BZPort {    
    public static function getInterfaceTypeOptions()    
    {
        $port_option = getNewPortTypeOptions();
        $ret = array();
        foreach ($port_option as $category => $options) {
            $children = array();
            foreach ($options as $key => $lable) {
                if (strpos($key, '-') !== False ) {
                    //list($iif_id,$iftype_id) = explode("-",$key);
                    $children[] = array('id'=>$key,'label'=>$lable);
                }
            }
            $ret[] = array('id' => null,
                        'label' => $category,
                        'children'=> $children
            );
        }
        return $ret;
    }

    public static function addObjectInterfacePort($object_id,$port_name,$port_type_id, $port_label, $port_l2address) {        
        $check_result = False;
        $port_id = null;
        $error = array();
        if ((!empty($object_id)) && (!empty($port_name)) && (!empty($port_type_id))) {
            if (is_numeric($object_id)) {
                $iftype_id = NULL;
                if (strpos($port_type_id, '-') !== False )
                    list($iif_id,$iftype_id) = explode("-",$port_type_id);     

                if (! empty($iftype_id)) {
                    if (DALPort::checkPortUniqueness($port_name,$iftype_id,$object_id)) {
                        try
                        {
                            $port_id = commitAddPort(intval($object_id),$port_name,$port_type_id, $port_label, $port_l2address);
                            $check_result = True;
                        }catch (Exception $e)
                        {
                            $error[] = array('field'=>'object','msg'=>$e->getMessage());
                        }
                    } else {
                        $error[] = array('field'=>'object','msg'=>"Port name ($port_name) & interface already exist");
                    }
                } else {
                    $error[] = array('field'=>'port type','msg'=>"port type invalid");
                    $is_create = False;
                }                    
            } else {
                if (! is_numeric($port_type_id)) $error[] = array('field'=>'port type','msg'=>"port type invalid");
            }
        } else {
            if (empty($object_id)) $error[] = array('field'=>'object','msg'=>"object empty");
            if (empty($port_name)) $error[] = array('field'=>'name','msg'=>"port name empty");
            if (empty($port_type_id)) $error[] = array('field'=>'port type','msg'=>"interface type empty");
        }

        $ret = array('create'=> $check_result,'id'=>$port_id,'error'=> $error);
        return $ret;
    }

    public static function addObjectInterfaceMultiPort($object_id,$port_name,$port_type_id, $port_label, $start, $count) {        
        $check_result = False;
        $port_id = null;
        $error = array();
        $ret = array();
        if ((!empty($object_id)) && (!empty($port_name)) && (!empty($port_type_id)) && (!empty($start)) && (!empty($count))) {
            if (is_numeric($object_id) && is_numeric($start) && is_numeric($count)) {
                $port_index = intval($start);
                $port_count = $port_index+intval($count);
                $port_count= ($port_count > 999)? 999 : $port_count;                
                $is_create = True;

                $iftype_id = NULL;
                if (strpos($port_type_id, '-') !== False )
                    list($iif_id,$iftype_id) = explode("-",$port_type_id);         

                if (! empty($iftype_id)) {
                    for ( $i=$port_index ; $i<$port_count; $i++ ) {
                        $new_name = $port_name ."". $i;
                        $new_label = (empty($port_label))?null:($port_label ."". $i);                    
                        if (! DALPort::checkPortUniqueness($new_name,$iftype_id,$object_id)) {                    
                            $error[] = array('field'=>'port name','msg'=>"Port name ($new_name) & interface already exist");
                            $is_create = False;
                        }
                    }
                } else {
                    $error[] = array('field'=>'port type','msg'=>"id invalid");
                    $is_create = False;
                }

                if ($is_create) {
                    for ( $i=$port_index ; $i<$port_count; $i++ ) {
                        $err = NULL;
                        $check_result = False;
                        $new_name = $port_name ."". $i;
                        $new_label = (empty($port_label))?null:($port_label ."". $i);        
                        $port_id = null;
                        try
                        {                            
                            $port_id = commitAddPort(intval($object_id),$new_name,$port_type_id, $new_label, null);
                            $check_result = True;
                        }catch (Exception $e)
                        {
                            $err = array('field'=>'object','msg'=>$e->getMessage());
                        }
                        $ret[] = array('create'=> $check_result,'id'=>$port_id,'name'=>$new_name,'error'=> $err);
                    }
                    $error = array();
                }

            } else {
                if (! is_numeric($object_id)) $error[] = array('field'=>'object','msg'=>"object invalid");
                if (! is_numeric($port_type_id)) $error[] = array('field'=>'port type','msg'=>"port type invalid");
                if (! is_numeric($start)) $error[] = array('field'=>'start','msg'=>"port start invalid");
                if (! is_numeric($start)) $error[] = array('field'=>'type','msg'=>"port count invalid");                
            }
        } else {
            if (empty($object_id)) $error[] = array('field'=>'object','msg'=>"object empty");
            if (empty($port_name)) $error[] = array('field'=>'name','msg'=>"port name empty");
            if (empty($port_type_id)) $error[] = array('field'=>'port type','msg'=>"interface type empty");
            if (empty($start)) $error[] = array('field'=>'start','msg'=>"port start empty");
            if (empty($count)) $error[] = array('field'=>'count','msg'=>"port count empty");            
        }
        
        if (count($error)>0)
            $ret = array('create'=> $check_result,'error'=> $error);

        return $ret;
    }

    public static function updateObjectInterfacePort($object_id,$port_id,$port_name,$port_type_id, $port_label, $port_l2address,$comment) {        
        $check_result = False;
        $error = array();
        if ((!empty($object_id)) && (!empty($port_name)) && (!empty($port_type_id)) && (!empty($port_id))) {
            if (is_numeric($object_id)&&is_numeric($port_id)) {
                $port_data = DALPort::getOriginalPortNameL2Addr($port_id);
                $nocheck = False;
                if (! empty($port_data)) {
                    $orignal_data = $port_data[intval($port_id)];
                    if ($port_name == $orignal_data['name'])
                        $nocheck = True;
                }

                if (strpos($port_type_id, '-') !== False )
                    list($iif_id,$iftype_id) = explode("-",$port_type_id);         

                if (! empty($iftype_id)) {
                    if ($nocheck || DALPort::checkPortUniqueness($port_name,$iftype_id,$object_id)) {
                        try
                        {
                            //$prot_id = commitAddPort(intval($object_id),$obj_port_name,intval($port_type_id), $port_label, $port_l2address,$comment);
                            commitUpdatePort (intval($object_id), intval($port_id), $port_name, $port_type_id, $port_label, $port_l2address, $comment);                    
                            $check_result = True;
                        }catch (Exception $e)
                        {
                            $error[] = array('field'=>NULL,'msg'=> $e->getMessage());
                        }
                    } else {

                        $error[] = array('field'=>'object','msg'=>"Port name ($port_name) & interface already exist");
                    }
                } else {
                    $error[] = array('field'=>'port type','msg'=>"id invalid");
                    $is_create = False;
                }
            } else {
                if (! is_numeric($object_id)) $error[] = array('field'=>'object','msg'=>"object invalid");
                if (! is_numeric($port_type_id)) $error[] = array('field'=>'port type','msg'=>"port type invalid");   
                if (! is_numeric($port_id)) $error[] = array('field'=>'port','msg'=>"port invalid");
            }
        } else {            
            if (empty($object_id)) $error[] = array('field'=>'object','msg'=>"object empty");
            if (empty($port_id)) $error[] = array('field'=>'port','msg'=>"port empty");
            if (empty($port_name)) $error[] = array('field'=>'name','msg'=>"port name empty");
            if (empty($port_type_id)) $error[] = array('field'=>'port type','msg'=>"interface type empty");
        }

        $ret = array('updated'=> $check_result,'id'=>$port_id,'error'=> $error);
        return $ret;
    }

    public static function deleteObjectInterfacePort($object_id,$port_id) {        
        $check_result = False;
        $prot_id = null;
        $error = array();
        if (is_numeric($port_id)) {
            try
            {
                $prot_id = DALPort::deleteObjectPort($object_id,$port_id);
                $check_result = True;
            }catch (Exception $e)
            {
                $error = $e->getMessage();
            }
        } else {
            if (empty($port_id)) $error = "port empty"; 
        }

        $ret = array('delete'=> $check_result,'object_id'=>$object_id,'id'=>$port_id,'error'=> $error);
        return $ret;
    }

    public static function clearObjectInterfaceLink($port_id) {        
        $check_result = False;
        $prot_id = null;
        $error = array();
        if (is_numeric($port_id)) {
            try
            {
                $recordNum = commitUnlinkPort($port_id);
                if ($recordNum>0)
                    $check_result = True;
                else { 
                    $error = "port not found";    
                    $check_result = False;
                }
            }catch (Exception $e)
            {
                $error = $e->getMessage();
            }
        } else {
            if (empty($port_id)) $error = "port invalid";    
        }

        $ret = array('unlink'=> $check_result,'id'=>$port_id,'error'=> $error);
        return $ret;
    }

    public static function getCompatibleLinkPortList($object_id,$port_id,$filter_type,$filter_objid) {        
        $check_result = False;
        $prot_id = null;
        $error = array();
        if (is_numeric($object_id) && is_numeric($port_id)) {
            $ret = DALPort::getCompatibleLinkPortList($object_id,$port_id,$filter_type,$filter_objid);
            $arr_ids = array_column($ret,'id');
            if (count($arr_ids)>0) {
                $filter_list = DALPort::getAvaliablePortList($arr_ids);
                if (count($filter_list)>0) {
                    foreach ($filter_list as $key => $info) {
                        $index = array_search($key, $arr_ids);
                        unset($ret[$index]);
                    }
                    $ret = array_values($ret);       
                }
            }            
        } else {
            if (empty($object_id)) $error[] = array('field'=>'object type','msg'=>"object type invalid");
            if (empty($port_id)) $error[] = array('field'=>'object','msg'=>"object invalid");
            $ret = array(False,$error);
        }
        
        return array(True,$ret);
    }

    public static function getObjectPortList($object_id,$port_id) {        
        $check_result = False;
        $prot_id = null;
        $error = array();
        if (is_numeric($object_id)) {
            $ret = DALPort::getObjectPortList($object_id,$port_id);
            if (is_numeric($port_id)) {
                $arr_ids = array_column($ret,'id');
                if (! empty($arr_ids)) {
                    $filter_list = DALPort::getAvaliablePortList($arr_ids);
                    if (count($filter_list)>0) {
                        foreach ($filter_list as $key => $info) {
                            $index = array_search($key, $arr_ids);
                            unset($ret[$index]);
                        }
                        $ret = array_values($ret);       
                    }
                }
            }            
        } else {
            $ret = array(False,"object invalid");
        }        
        return array(True,$ret);
    }

    public static function linkObjectInterfacePort($source_portid,$target_portid,$cable = NULL) {
        $check_result = False;
        $error = "";
        if (is_numeric($source_portid) && is_numeric($target_portid)) {
            try
                {
                    $result = linkPorts($source_portid,$target_portid,$cable);
                    $check_result = True;
                } catch (Exception $e)
                {
                    $error = $e->getMessage();
                }
        } else {
            $error = 'parameter invalid';
        }
        
        $ret = array('link'=> $check_result,'source_id'=>$source_portid,'target_id'=>$target_portid,'error'=> $error);
        return $ret;
    }
    
    public static function getObjectSparePortList($port_id,$filter,$sort,$start,$limit) {
        $check_result = False;
        $prot_id = null;
        $error = array();
        $total = 0;
        if (is_numeric($port_id)) {
            $port_info = getPortInfo ($port_id);
            if (! empty($port_info)) {

                if (!is_array($filter))
                    $filter = json_decode($filter,true);

                if (!is_array($sort))
                    $sort = json_decode($sort,true);

                if ($filter['in_rack'])
                {
                    $object = spotEntity ('object', $port_info['object_id']);
                    if ($object['rack_id']) // the object itself is mounted in a rack
                        $filter['racks'] = getProximateRacks ($object['rack_id'], getConfigVar ('PROXIMITY_RANGE'));
                    elseif ($object['container_id']) // the object is not mounted in a rack, but it's container may be
                    {
                        $container = spotEntity ('object', $object['container_id']);
                        if ($container['rack_id'])
                            $filter['racks'] = getProximateRacks ($container['rack_id'], getConfigVar ('PROXIMITY_RANGE'));
                    }
                }
                //echo "start:".$start;
                list($total,$ret) = DALPort::getObjectSparePortList($port_info,$filter,$sort,$start,$limit);
            }
        } else {
            $ret = array(False,$total,"port invalid");
        }        
        return array(True,$total,$ret);
    }
    


}
?>
