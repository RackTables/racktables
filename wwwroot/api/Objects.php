<?php
include('common.php');
include('BZObject.php');

class Objects {
    //dashboard
    public static function Summary($args)
    {   
       // print_r($args);
        $data = BZObject::getSummary($args['label']);
        return outSuccess($data,count($data));
    }
  
    //### Base Methods ###
    public static function getObjectIdByAssetId($args) {
        $type = 4;
        if (isset($args['type']))
            $type = $args['type'];       

        $data = BZObject::getObjectIdByAssetId($type,$args['asset_id']);
        $count = count($data);
        if ($count > 0) {
            return outSuccess($data,$count);
        } else {
            return outFail('Object not found');
        }
    }

    //### Base Methods by v2 ###
    public static function get_objtype_columns($args,$path_params) {
        $objtype_id = (isset($args['objtype_id']))? $args['objtype_id'] : NULL;
        $objtype_namelist = (isset($args['objtype_namelist']))? $args['objtype_namelist'] : NULL;
        $data = BZObject::get_objtype_columns($objtype_id,$objtype_namelist);
        if (count($data)) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

    public static function get_common_attributes($args,$path_params) {
        
        $data = BZObject::get_common_attributes();
        if (count($data)) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }    

    public static function get_object_attrvalues($args,$path_params) {
        $attr_id = (isset($args['attr_id']))? $args['attr_id'] : NULL;
        list($success,$data) = BZObject::get_object_attrvalues($attr_id);
        return outSuccess($data,count($data));
    }

    public static function get_objtype_list($args,$path_params) {
        $hwtype_ids = (isset($args['hwtype_ids']))? $args['hwtype_ids'] : NULL;
        list($success,$data) = BZObject::get_objtype_list($hwtype_ids);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

    public static function get_manufacturer_list($args,$path_params) {
        list($success,$data) = BZObject::get_manufacturer_list($args['objtype_id']);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

    public static function get_hardware_type($args,$path_params) {
        $objtype_id = (isset($args['objtype_id']))? $args['objtype_id'] : NULL;
        list($success,$data) = BZObject::get_hardware_type($objtype_id);

        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

    public static function get_objtype_hwlist($args,$path_params) {
        $objtype_namelist = (isset($args['objtype_namelist']))? $args['objtype_namelist'] : NULL;

        list($success,$data) = BZObject::get_objtype_hwlist($objtype_namelist);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }
    
    public static function get_attrvalue_namelist($args,$path_params) {
        $objtype_id = (isset($args['objtype_id']))? $args['objtype_id'] : NULL;
        print_r($args);
        $name_list = (isset($args['name_list']))? $args['name_list'] : NULL;
        echo "fo:".$name_list;
        list($success,$data) = BZObject::getDictAttrValueListByName($objtype_id,$name_list);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

    public static function get_objtype_attributes($args,$path_params) {
        $objtype_id = NULL;
        if (! empty($path_params))
            $objtype_id = $path_params;
        else 
            $objtype_id = $args['objtype_id'];
        list($success,$data) = BZObject::get_objtype_attributes($objtype_id);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }
    //### Application Methods  by v1 ###
    public static function getObjectDetail($args)
    {
        $data = BZObject::getObjectDetail($args['asset_id']);
        return outSuccess($data,count($data));
    }
    
    public static function setPropertyValue($args) {
        try {
            list($success,$data) = BZObject::setPropertyValue($args['object_id'],$args['attr_id'],$args['attr_value']);
            if ($success) {
                return outSuccess($data,1);
            } else {
                return outFail($data);
            }
        } catch (Exception $e) {
            return outFail(array('error'=>$e->getMessage()));
        }
    }

    public static function getPropertys($args)
    {   
        list($success,$data) = BZObject::getPropertys($args['asset_id']);
        if ($success > 0) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

    public static function getMaintenanceLog($args)
    {   
       // print_r($args);
        $data = BZObject::getMaintenanceLog($args['id']);
        return outSuccess($data,count($data));
    }

    public static function getAcceseLog($args)
    {   
       // print_r($args);
        $data = BZObject::getAcceseLog($args['id']);
        return outSuccess($data,count($data));
    }

    public static function getRackDataByAssetId($args) {
        list($success,$data) = BZObject::getRackDataByAssetId($args['asset_ids']);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail('Object not found');
        }
    }

    public static function getTopologyByAssetId($args) {
        list($success,$data) = BZObject::getTopologyByAssetId($args['asset_id']);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail('Object not found');
        }
    }

    public static function getTopologyByObjectId($args) {
        list($success,$data) = BZObject::getTopologyByObjectId($args['object_id']);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail('Object not found');
        }
    }

    public static function getHostLinkedAssetList($args){
        list($success,$data) = BZObject::getHostLinkedAssetList($args['asset_ids']);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail('Object not found');
        }        
    }

    //### Application Methods by v2 ###
    public static function all_list($args,$path_params) {
        $keyword = NULL;
        if (! empty($args['keyword']))
            $keyword = $args['keyword'];        
        $filter = NULL;
        if (! empty($args['filter']))
            $filter = $args['filter'];
        $sort = NULL;
        if (! empty($args['sort']))
            $sort = $args['sort'];
        $start = NULL;
        if (! empty($args['start']))
            $start = $args['start'];
        $limit = NULL;
        if (! empty($args['limit']))
            $limit = $args['limit'];
            
        list($success,$total,$data) = BZObject::get_all_list($keyword,$filter,$sort,$start,$limit);
        if ($success) {
            return outSuccess($data,$total);
        } else {
            return outFail($data);
        }
    }

    public static function get_object_detail($args,$path_params) {
        list($success,$data) = BZObject::get_object_detail($args['object_id']);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }        
    }

    public static function create_objects($args,$path_params) {
        list($success,$data) = BZObject::create_procedure($args['content']);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

    public static function delete_objects($args,$path_params) {
        list($success,$data) = BZObject::delete_objects($args['object_ids']);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

    public static function update_objects($args,$path_params) {
        list($success,$data) = BZObject::update_procedure($args['content']);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

    public static function import_objects($args,$path_params) {        
        ini_set('max_execution_time', 0); 
        ignore_user_abort(true);
        list($success,$data) = BZObject::import_objects($args['content']);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    } 
   
    public static function check_objects($args,$path_params) {
        //echo "check_objects\n";
        list($success,$data) = BZObject::precheck_objects($args['content']);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

}
?>