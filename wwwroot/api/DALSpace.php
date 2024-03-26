<?php

class DALSpace {
    public static function getSummarySpace ()
    {
        $ret = array();
        $subject = array();
        $subject[] = array ('q' => 'select count(*) from Row', 'txt' => 'Rows');
        $subject[] = array ('q' => 'select count(*) from Rack', 'txt' => 'Racks');
        $subject[] = array ('q' => 'select avg(height) from Rack', 'txt' => 'Average Rack Height');
        $subject[] = array ('q' => 'select sum(height) from Rack', 'txt' => 'Total Rack Units');

        foreach ($subject as $item)
        {
            $result = usePreparedSelectBlade ($item['q']);
            $tmp = $result->fetchColumn();
            $ret[$item['txt']] = $tmp == '' ? 0 : $tmp;
            unset ($result);
        }
        unset($result);

        return $ret;
    }

    public static function getLocationIds($filter,$start,$limit) {
        $ret = array();
        $ids = NULL;
        if (isset($filter['location_id'])) {
            if (gettype($filter['location_id']) == 'array') {
                $ids = implode(",", $filter['location_id']);
            } else {
                $ids = $filter['location_id'];
            }
        }

        if (empty($ids))
           return array();

        $result = usePreparedSelectBlade ("SELECT L.id AS `lid`,R.id AS `rid`,L.name AS loc_name,R.name FROM Location L
                                           LEFT JOIN `Row` AS R ON L.id = R.location_id
                                           WHERE R.location_id IN ({$ids})
                                           ORDER BY R.location_id ASC , R.id ASC
                                           LIMIT {$start},{$limit}");

        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row)
        {
            $ret[] = $row;
        }
        unset($result);

        return $ret;
    }

    public static function getLocationData($args)
    {
        $sql = "";
        if (gettype($args) == 'array') {
            $ids = implode(",", $args);
            $sql = "WHERE R.location_id in ({$ids})";
        }

        /*echo "SELECT R.location_id,RK.location_name,R.id AS row_id,R.name AS row_name,RK.id AS rack_id,RK.name AS rack_name,RK.has_problems,RK.height,RK.`comment` FROM Row  AS R
        LEFT JOIN Rack AS RK ON R.id = RK.row_id
        LEFT JOIN Location AS L ON R.location_id = L.id
         {$sql}
        ORDER BY R.id ASC, RK.sort_order ASC \n";*/
        $result = usePreparedSelectBlade ("SELECT L.id AS location_id,L.name AS location_name,R.id AS row_id,R.name AS row_name,RK.id AS rack_id,RK.name AS rack_name,RK.has_problems,RK.height,RK.`comment` FROM Row  AS R
                                           LEFT JOIN Rack AS RK ON R.id = RK.row_id
                                           LEFT JOIN Location AS L ON R.location_id = L.id
                                            {$sql}
                                           ORDER BY R.id ASC, RK.sort_order ASC");
        $ret = array();
        $loc_dict = array();
        $row_dict = array();
        $lindex = 0;
        $location_total = 0;
        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {
            //$ret[] = $row;
            $loc_key = $row['location_id'];
            $row_key = $row['row_id'];
            if (! empty($row['rack_id'])) {
                $rack_data = array('rack_id' => $row['rack_id'],
                                   'rack_name' => $row['rack_name'],
                                   'has_problems' => $row['has_problems'],
                                   'height' => $row['height'],
                                   'comment' => $row['comment']);
            } else
                $rack_data = array();

            if (isset($loc_dict[$loc_key])) {
                $loc_index = $loc_dict[$loc_key]['loc_index'];
                if (isset($row_dict[$row_key])) {
                    $row_index = $row_dict[$row_key];
                    $ret[$loc_index]['children'][$row_index]['children'][] = $rack_data;
                    $space_total = $ret[$loc_index]['children'][$row_index]['total'];
                    $ret[$loc_index]['children'][$row_index]['total'] = $space_total + $rack_data['height'];
                } else {
                    $row_index = $loc_dict[$loc_key]['row_index'];
                    $row_index++;
                    $row_data = array('row_name' => $row['row_name'],
                                      'row_id' => $row_key,
                                      'total' => $row['height'],
                                      'children' => array());
                    if (! empty($rack_data))
                        $row_data['children'][] = $rack_data;

                    $ret[$loc_index]['children'][] = $row_data;
                    $row_dict[$row_key] = $row_index;
                    $loc_dict[$loc_key]['row_index'] = $row_index;
                }
            } else {
                $location_total = $location_total + $row['height'];
                $location_name = $row['location_name'];
                $row_data = array('row_name' => $row['row_name'],
                                  'row_id' => $row_key,
                                  'total' => $row['height'],
                                  'children' => array());

                if (! empty($rack_data))
                    $row_data['children'][] = $rack_data;

                $ret[] = array('location_name' => htmlspecialchars_decode($location_name),
                               'loc_total' => $location_total,
                               'location_id'=>$loc_key,
                               'children'=> array($row_data));

                $loc_dict[$loc_key]['loc_index'] = $lindex;
                $loc_dict[$loc_key]['row_index'] = 0;
                $row_dict[$row_key] = 0;
                $lindex++;
            }

        }
        unset($result);

        return $ret;

    }

    public static function getLocationInfo($lid)
    {
        $result = usePreparedSelectBlade ("SELECT R.id AS row_id,R.name AS row_name,RK.id AS rack_id,RK.name AS rack_name,RK.has_problems,RK.height,RK.`comment`,RK.location_name FROM Row  AS R
                                           LEFT JOIN Rack AS RK ON R.id = RK.row_id
                                           WHERE R.location_id = {$lid}
                                           ORDER BY R.id ASC, RK.sort_order ASC");
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        $ret = array();
        $keyDict = array();
        $index = 0;
        $location_name = "";
        $location_total = 0;
        foreach ($rows as $row)
        {
            //$ret[] = $row;
            $key = $row['row_id'];
            $location_total = $location_total + $row['height'];
            $rackData = array('rack_id' => $row['rack_id'],
                              'rack_name' => $row['rack_name'],
                              'has_problems' => $row['has_problems'],
                              'height' => $row['height'],
                              'comment' => $row['comment']);

            if (isset($keyDict[$key])) {
                $rindex = $keyDict[$key];
                $ret[$rindex]['total'] = $ret[$rindex]['total'] + $row['height'];
                $ret[$rindex]['children'][] = $rackData;
            } else {
                $space = array();
                $space['total'] = $row['height'];
                $space['children'] = array();
                $space['children'][] = $rackData;
                $space['row_name'] = $row['row_name'];
                $space['row_id'] = intval($row['row_id']);
                $ret[] = $space;
                $keyDict[$key] = $index;
                $index++;
            }
            $location_name = $row['location_name'];
        }
        unset($result);

        return array('location_name' => $location_name,'loc_total' => $location_total, 'location_id'=>$lid, 'row_data'=>$ret);
    }

    public static function getRowData($rid)
    {
        $result = usePreparedSelectBlade ("SELECT L.id AS location_id,L.name AS location_name,R.name AS row_name,RK.id AS rack_id,RK.name AS rack_name,RK.has_problems, RK.`comment`,RK.height FROM Row  AS R
                                           LEFT JOIN Rack AS RK ON R.id = RK.row_id
                                           LEFT JOIN Location AS L ON R.location_id = L.id
                                           WHERE R.id = {$rid}
                                           ORDER BY R.id ASC, RK.sort_order ASC");
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        $ret = array('row_id'=> $rid,'children' => array());
        $row_name = NULL;
        $location_id = NULL;
        $location_name = NULL;
        $keyDict = array();
        $total = 0;
        foreach ($rows as $row)
        {
            if (empty($row['rack_id'])) {
                $rack_data = array();
            } else {
                $rack_data = array('rack_id' => intval($row['rack_id']),
                                   'rack_name' => $row['rack_name'],
                                   'has_problems' => $row['has_problems'],
                                   'height' => intval($row['height']),
                                   'comment' => $row['comment']);
            }

            $total = $total + $row['height'];
            $row_name = $row['row_name'];
            $location_id = intval($row['location_id']);
            $location_name = htmlspecialchars_decode($row['location_name']);
            if (count($rack_data)>0)
                $ret['children'][] = $rack_data;
        }
        $ret['row_name'] = $row_name;
        $ret['total'] = $total;
        $ret['location_id'] = $location_id;
        $ret['location_name'] = $location_name;
        unset ($result);

        return $ret;
    }

    public static function getRackData($id)
    {
        $ret = array();
        $result = usePreparedSelectBlade("SELECT id,`name`,has_problems,`comment`,height,row_id,row_name FROM Rack WHERE id = {$id} LIMIT 1");

        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {
            $row['row_id'] = intval($row['row_id']);
            $ret = $row;
        }
        unset ($result);

        return $ret;
    }

    public static function getLocationDataAll() {

        $ret = array();
        $result = usePreparedSelectBlade("SELECT id,NAME,parent_id FROM `Location`");

        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {
            $id = $row['id'];
            $row['id'] = intval($row['id']);
            $ret[$id] = $row;
            unset($ret[$id]['id']);
        }
        unset ($result);

        return $ret;
    }

    public static function getRackMoleculeByObjectIds($ids,$show_height=False)
    {
        $ret = array();

        $result = usePreparedSelectBlade ("SELECT O.id AS object_id,O.objtype_id,
                                           IF(RS.rack_id IS NULL,IF(EK.id IS NULL ,'N','Y'),'N') AS zerou,
                                           IF(RS.rack_id IS NULL,EK.location_id,R.location_id) AS location_id,
                                           IF(RS.rack_id IS NULL,EK.location_name,R.location_name) AS location_name,
                                           IF(RS.rack_id IS NULL,EK.row_id,R.row_id) AS row_id,
                                           IF(RS.rack_id IS NULL,EK.row_name,R.row_name) AS row_name,
                                           IF(RS.rack_id IS NULL,EK.id,RS.rack_id) AS rack_id,
                                           IF(RS.rack_id IS NULL,EK.rack_name,R.name) AS rack_name,
                                           IF(RS.rack_id IS NULL,EK.height,R.height) AS height,
                                           RS.unit_no,RS.atom,RS.state FROM Object AS O
                                           LEFT JOIN RackSpace AS RS ON RS.object_id = O.id
                                           LEFT JOIN Rack AS R ON R.id = RS.rack_id
                                           LEFT JOIN (SELECT EL.child_entity_id AS object_id,RK.location_id,RK.location_name,RK.row_id,RK.row_name,RK.id,RK.name AS rack_name,RK.height FROM EntityLink AS EL INNER JOIN  Rack AS RK ON RK.id = EL.parent_entity_id WHERE EL.parent_entity_type = 'rack' AND child_entity_type = 'object') AS EK ON EK.object_id = O.id
                                           WHERE O.id IN ({$ids})");
        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            //print_r($row);

            $row['zerou'] = ($row['zerou'] == 'Y')? True : False;
            if (! empty($row['rack_id'])) {
                $row['object_id'] = intval($row['object_id']);
                $row['rack_id'] = intval($row['rack_id']);
                $row['location_id'] = intval($row['location_id']);
                $row['row_id'] = intval($row['row_id']);
                $row['rack_id'] = intval($row['rack_id']);
                if ($row['zerou'])
                    DALCommon::getRackZerou($ret,$row);
                else
                    DALCommon::getRackMolecule($ret,$row);
            }
        }
        unset($result);
        return $ret;
    }

    public static function getRowList($ids)
    {
        $ret = array();
        $whereSQL = "";
        if (!empty($ids)) {
            $whereSQL = "WHERE EL.parent_entity_id IN ({$ids})";
        }

        $result = usePreparedSelectBlade ("SELECT R.id, R.name FROM Row R
                                           INNER JOIN EntityLink EL ON EL.parent_entity_type = 'location'
                                           AND EL.child_entity_type = 'row'
                                           AND EL.child_entity_id = R.id
                                           {$whereSQL}
                                           ORDER BY R.name");

        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            $ret[] = array('id' => $row['id'], 'name'=> $row['name']);
        }
        unset($result);
        return $ret;
    }

    public static function getRackList($ids)
    {
        $ret = array();
        $whereSQL = "";
        if (!empty($ids)) {
            $whereSQL = "WHERE EL.parent_entity_id IN ({$ids})";
        }

        $result = usePreparedSelectBlade ("SELECT R.id, R.name FROM Rack AS R
                                           INNER JOIN EntityLink EL ON EL.parent_entity_type = 'row'
                                           AND EL.child_entity_type = 'rack'
                                           AND EL.child_entity_id = R.id
                                           {$whereSQL}
                                           ORDER BY R.name");

        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            $ret[] = array('id' => $row['id'], 'name'=> $row['name']);
        }
        unset($result);
        return $ret;
    }

    public static function checkLocationExists($id) {
        $result = usePreparedSelectBlade("SELECT COUNT(id) FROM `Location` WHERE id={$id}");
        $count = $result->fetchColumn();
        unset ($result);
        return ($count>0)?TRUE:FALSE;
    }

    public static function getPositionByName($locations,$rows,$racks) {
        $ret = array();

        /*echo "SELECT R.name,R.id,R.row_name,R.row_id,R.location_id,R.location_name,RS.unit_no,RS.object_id,ZU.object_id AS zuobject_id FROM `Rack` AS R
        LEFT JOIN `RackSpace` AS RS ON R.id = RS.rack_id
        LEFT JOIN (SELECT parent_entity_id AS rack_id,child_entity_id AS object_id FROM EntityLink WHERE parent_entity_type='rack' AND child_entity_type='object') AS ZU ON ZU.rack_id = RS.rack_id
        WHERE R.name IN ({$racks})
        AND R.row_name IN ({$rows})
        AND R.location_name IN ({$locations})\n";*/

        $result = usePreparedSelectBlade("SELECT R.name,R.id,R.row_name,R.row_id,R.location_id,R.location_name,RS.unit_no,RS.object_id,ZU.object_id AS zuobject_id FROM `Rack` AS R
                                          LEFT JOIN `RackSpace` AS RS ON R.id = RS.rack_id
                                          LEFT JOIN (SELECT parent_entity_id AS rack_id,child_entity_id AS object_id FROM EntityLink WHERE parent_entity_type='rack' AND child_entity_type='object') AS ZU ON ZU.rack_id = RS.rack_id
                                          WHERE R.name IN ({$racks})
                                          AND R.row_name IN ({$rows})
                                          AND R.location_name IN ({$locations})");
        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            $loc_name = $row['location_name'];
            $row_name = $row['row_name'];
            $rack_name = $row['name'];
            $u_no = intval($row['unit_no']);

            if (!isset($ret[$loc_name]))
                $ret[$loc_name] = array();

            if (!isset($ret[$loc_name][$row_name]))
                $ret[$loc_name][$row_name] = array();

            if (!isset($ret[$loc_name][$row_name][$rack_name]))
                $ret[$loc_name][$row_name][$rack_name] = array();

            if (!isset($ret[$loc_name][$row_name][$rack_name]['u'])) {
                $ret[$loc_name][$row_name][$rack_name] = array('location_id' => $row['location_id'],
                                                               'row_id' => $row['row_id'],
                                                               'rack_id' => $row['id'],
                                                               'u' => array());
            }

            if (empty($u_no)) {
                $ret[$loc_name][$row_name][$rack_name]['u'] = array();
            } else {
                //空陣列,表示目前無使用
                if (! isset($ret[$loc_name][$row_name][$rack_name]['u'][$u_no])) {
                    $ret[$loc_name][$row_name][$rack_name]['u'][$u_no] = array();
                    $u_objs = $ret[$loc_name][$row_name][$rack_name]['u'][$u_no];
                } else
                    $u_objs = $ret[$loc_name][$row_name][$rack_name]['u'][$u_no];
                //echo "row:";
                //print_r($row);
                //echo "u_objs:";
                //print_r($u_objs);
                //第一個為 rack 的物件
                if (! empty($row['object_id'])) {
                    //if (! in_array($row['object_id'],$u_objs))
                    $u_objs[] = $row['object_id'];
                }
                //為 zero-u 物件
                //if (! empty($row['zuobject_id']))
                //    $u_objs[] = $row['zuobject_id'];

                $ret[$loc_name][$row_name][$rack_name]['u'][$u_no] = $u_objs;
            }
            //print_r($ret);
        }

        unset ($result);
        return $ret;
    }

    public static function getZerouRackObjects($rack_id) {
        $ret = array();
        $result = usePreparedSelectBlade("SELECT O.id AS `object_id`,O.objtype_id AS `type_id`,D.dict_value AS `type`,O.name,O.label,O.asset_no AS asset_id FROM EntityLink AS EL
                                          INNER JOIN Object AS O ON O.id = EL.child_entity_id
                                          LEFT JOIN Dictionary AS D ON D.dict_key = O.objtype_id
                                          WHERE EL.parent_entity_type = 'rack'
                                          AND EL.child_entity_type = 'object'
                                          AND EL.parent_entity_id = {$rack_id}");
        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            $row['host_id'] = 0;
            $row['host_name'] = NULL;
            $row['type_id'] = intval($row['type_id']);
            $row['object_id'] = intval($row['object_id']);
            $ret[] = $row;
        }
        unset ($result);
        return $ret;
    }

    public static function addObjectToZerou($rack_id,$object_id) {
        usePreparedInsertBlade
        (
            'EntityLink',
            array
            (
                //'location','object','rack','row'
                'parent_entity_type' => 'rack',
                'parent_entity_id' => intval($rack_id),
                'child_entity_type' => 'object',
                'child_entity_id' => intval($object_id),
            )
        );
        return  lastInsertID();
    }

    public static function deleteZerouObject($rack_id,$object_id) {
        $check = usePreparedDeleteBlade('EntityLink',
                                        array (
                                            'parent_entity_type'=>'rack',
                                            'parent_entity_id'=>$rack_id,
                                            'child_entity_type' => 'object',
                                            'child_entity_id'=>$object_id)
                                    );
        return ($check == 1)?True:False;
    }

    public static function getZerouRackByObjectId($object_id) {
        $result = usePreparedSelectBlade("SELECT EL.parent_entity_id FROM EntityLink AS EL WHERE EL.parent_entity_type = 'rack' AND EL.child_entity_type = 'object' AND EL.child_entity_id = {$object_id}");
        $rack_id = $result->fetchColumn();
        unset ($result);
        return intval($rack_id);
    }

    public static function checkZerouObjectExists($object_id) {
       // echo "SELECT EL.child_entity_id FROM EntityLink AS EL WHERE EL.parent_entity_type = 'rack' AND EL.child_entity_type = 'object' AND EL.child_entity_id = {$object_id}\n";
        $result = usePreparedSelectBlade("SELECT COUNT(EL.child_entity_id) FROM EntityLink AS EL WHERE EL.parent_entity_type = 'rack' AND EL.child_entity_type = 'object' AND EL.child_entity_id = {$object_id}");
        $count = $result->fetchColumn();
        //echo "count:{$count}\n";
        unset ($result);
        return (intval($count)>0)?True:False;
    }
}
?>