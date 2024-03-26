<?php
include ('common.php');
include('BZSpace.php');

class Space {
    public static function getPositionByAssetId($args) {
        $data = BZSpace::getPositionByAssetId($args['asset_id']);
        return outSuccess($data,count($data));
    }

    public static function getPositionByObjectId($args) {
        $data = BZSpace::getPositionByObjectId($args['object_id']);
        return outSuccess($data,count($data));
    }

    public static function getLocationData($args) {
        $data = BZSpace::getLocationData($args['location_id']);
        return outSuccess($data,count($data));
    }

    public static function getRowData($args) {
        $data = BZSpace::getRowData($args['row_id']);
        return outSuccess($data,count($data));
    }

    public static function getRackData($args) {
        $data = BZSpace::getRackData($args['rack_id']);
        return outSuccess($data,count($data));
    }

    //## v2 achiteture
    public static function get_location_tree($args,$path_params) {
        list($success,$data) = BZSpace::get_location_tree();
        return outSuccess($data,count($data));
    }

    public static function get_row_list($args,$path_params) {
        $location_id = (isset($args['location_ids']))? $args['location_ids'] : NULL;
        list($success,$data) = BZSpace::get_row_list($location_id);
        return outSuccess($data,count($data));
    }

    public static function get_rack_list($args,$path_params) {
        $row_id = (isset($args['row_ids']))? $args['row_ids'] : NULL;
        list($success,$data) = BZSpace::get_rack_list($row_id);
        return outSuccess($data,count($data));
    }

    public static function get_available_rack($args,$path_params) {
        $rack_id = (isset($args['rack_id']))? $args['rack_id'] : NULL;
        list($success,$data) = BZSpace::get_available_rack($rack_id);
        return outSuccess($data,count($data));
    }

    public static function get_position_namelist($args,$path_params) {
        $locations= (isset($args['locations']))? $args['locations'] : NULL;
        $rows = (isset($args['rows']))? $args['rows'] : NULL;
        $racks = (isset($args['racks']))? $args['racks'] : NULL;

        list($success,$data) = BZSpace::get_position_namelist($locations,$rows,$racks);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

    public static function getEMSLocationData() {
        $data = BZSpace::getEnMSLocationData();
        return outSuccess($data,count($data));
    }


}
?>