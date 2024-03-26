<?php
include ('common.php');
include('BZPort.php');

class Port {
    public static function getInterfaceTypeOptions($args) {
        $data = BZPort::getInterfaceTypeOptions();
        return outSuccess($data,count($data));
    }

    public static function addObjectInterfacePort($args) {
        $data = BZPort::addObjectInterfacePort($args['object_id'],$args['name'],$args['type_id'],$args['label'],$args['l2address']);
        return outSuccess($data,count($data));
    }

    public static function addObjectInterfaceMultiPort($args) {
        $data = BZPort::addObjectInterfaceMultiPort($args['object_id'],$args['name'],$args['type_id'],$args['label'],$args['start'],$args['count']);
        return outSuccess($data,count($data));
    }

    public static function deleteObjectInterfacePort($args) {
        $data = BZPort::deleteObjectInterfacePort($args['object_id'],$args['port_id']);
        return outSuccess($data,count($data));
    }

    public static function clearObjectInterfaceLink($args) {
        $data = BZPort::clearObjectInterfaceLink($args['port_id']);
        return outSuccess($data,count($data));
    }

    public static function updateObjectInterfacePort($args) {
        $comment = (isset($args['comment']))?$args['comment']:NULL;
        $data = BZPort::updateObjectInterfacePort($args['object_id'],$args['port_id'],$args['name'],$args['type_id'],$args['label'],$args['l2address'],$comment);
        return outSuccess($data,count($data));
    }

    public static function linkObjectInterfacePort($args) {
        $cable = (isset($args['cable']))?$args['cable']:NULL;
        $data = BZPort::linkObjectInterfacePort($args['source_portid'],$args['target_portid'],$cable);
        return outSuccess($data,count($data));
    }    

    public static function getCompatibleLinkPortList($args) {
        list($success,$data) = BZPort::getCompatibleLinkPortList($args['object_id'],$args['port_id'],$args['filter_type'],$args['filter_objid']);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

    public static function getObjectPortList($args) {
        list($success,$data) = BZPort::getObjectPortList($args['object_id'],$args['port_id']);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

    public static function getObjectSparePortList($args) {
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
        //echo "start:".$args['start'];
        list($success,$total,$data) = BZPort::getObjectSparePortList($args['port_id'],$filter,$sort,$args['start'],$args['limit']);
        if ($success) {
            return outSuccess($data,$total);
        } else {
            return outFail($data);
        }
    }

    
}
?>