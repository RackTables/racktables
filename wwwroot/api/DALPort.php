<?php
class DALPort {
    public static function deleteObjectPort($object_id,$port_id)
    {
        $check=usePreparedDeleteBlade('Port', array ('object_id'=>$object_id,'id'=>$port_id));
        return ($check == 1)?True:False;
    }

    public static function getCompatibleLinkPortList($object_id,$port_id,$filter_type,$filter_objid)
    {
        $ret = array();
        $whereSQL = "";
        $groupSQL = "GROUP BY `TO`.objtype_id";
        $has_objtype = False;
        if ((! empty($filter_type)) && (is_numeric($filter_type))) {
            $whereSQL = " AND `TO`.objtype_id = {$filter_type}";
            $groupSQL = "GROUP BY TP.object_id";
            $has_objtype = True;
        }

        $has_object = False;
        if ($has_objtype && (! empty($filter_type)) && (is_numeric($filter_objid))) {
            $whereSQL = " AND TP.object_id = {$filter_objid}";
            $groupSQL = "";
            $has_object = True;
        }

        $result = usePreparedSelectBlade ("SELECT SP.`name`,SP.`type`,
                                           `TO`.objtype_id AS tp_objtype_id,TD.dict_value AS td_objtype ,TP.object_id AS tp_object_id,`TO`.`name` AS tp_obj_name ,TP.id AS tp_id, TP.name AS tp_name, TP.iif_id AS tp_iif_id, TPOI.oif_name ,TP.`type` AS tp_type 
                                           FROM Port AS SP
                                           INNER JOIN PortCompat AS PC ON SP.`type` = PC.type1
                                           LEFT JOIN Port AS TP ON PC.type2 = TP.`type`
                                           LEFT JOIN Object AS `TO` ON `TO`.id = TP.object_id
                                           LEFT JOIN Dictionary AS TD ON `TO`.objtype_id = TD.dict_key
                                           LEFT JOIN PortOuterInterface AS TPOI ON TPOI.id = TP.`type`                                            
                                           WHERE SP.id = {$port_id} AND TP.object_id != {$object_id} AND `TO`.objtype_id IS NOT NULL {$whereSQL}
                                            {$groupSQL}");

        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {           
            if ($has_objtype) {
                if  ($has_object) {
                    $ret[] = array('id'=>$row['tp_id'],'label'=>$row['tp_name'],'interface'=> $row['oif_name']);
                } else {
                    $ret[] = array('id'=>$row['tp_object_id'],'label'=>$row['tp_obj_name']);
                }
            } else {
                $ret[] = array('id'=>$row['tp_objtype_id'],'label'=>$row['td_objtype']);
            }                       
        }
        unset($result);

        return $ret;
    }

    public static function getAvaliablePortList($arr_ids,$assoc_key=True)
    {
        $ret = array();
        $key_map = array();
        $port_ids = implode(",",$arr_ids);
        /*echo "SELECT pa.id AS id_a, pa.name AS port_name_a, oa.name AS obj_name_a, 
        pb.id AS id_b, pb.name AS port_name_b, ob.name AS obj_name_b 
        FROM 
        Link INNER JOIN Port pa ON pa.id = Link.porta 
        INNER JOIN Port pb ON pb.id = Link.portb 
        INNER JOIN RackObject oa ON pa.object_id = oa.id 
        INNER JOIN RackObject ob ON pb.object_id = ob.id 
        WHERE Link.porta IN ({$port_ids}) OR Link.portb IN ({$port_ids})";*/
        $result = usePreparedSelectBlade ("SELECT pa.id AS id_a, pa.name AS port_name_a, oa.name AS obj_name_a, 
                                           pb.id AS id_b, pb.name AS port_name_b, ob.name AS obj_name_b 
                                           FROM 
                                           Link INNER JOIN Port pa ON pa.id = Link.porta 
                                           INNER JOIN Port pb ON pb.id = Link.portb 
                                           INNER JOIN RackObject oa ON pa.object_id = oa.id 
                                           INNER JOIN RackObject ob ON pb.object_id = ob.id 
                                           WHERE Link.porta IN ({$port_ids}) OR Link.portb IN ({$port_ids})");

        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {
            $is_a = True;
            foreach(array($row['id_a'],$row['id_b']) AS $id) {
                if(in_array($id,$arr_ids)) {
                    $data = array();
                    if ($is_a)
                        $data = array('id'=>$id,'port_name'=>$row['port_name_a'],'object_name'=>$row['obj_name_a']);
                    else 
                        $data = array('id'=>$id,'port_name'=>$row['port_name_b'],'object_name'=>$row['obj_name_b']);
                    
                    if ($assoc_key) {
                        $ret[$id] = $data;
                    } else {
                        $ret[] = $data;
                    }         
                }
                $is_a = False; 
            }
        }
        unset($result);

        return $ret;
    }

    public static function getOriginalPortNameL2Addr($port_id) {
        $ret = array();
        $result = usePreparedSelectBlade ("SELECT id,`name`,l2address FROM Port WHERE id={$port_id}");

        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {           
            $id = intval($row['id']);
            $ret[$id] = $row;
        }
        unset($result);

        return $ret;
    }

    public static function checkPortUniqueness($port_name,$port_type,$object_id) {
        $result = usePreparedSelectBlade("SELECT COUNT(id) FROM Port WHERE `name` = '{$port_name}' AND object_id = {$object_id} AND `type` ={$port_type}");        
        $count = $result->fetchColumn();        
        unset ($result);
        return ($count>0)?False:True;
    }

    public static function getObjectPortList($object_id,$source_port=NULL) {
        $ret = array();
        $tableSQL = "";
        $whereSQL = "";
        $joinSQL = "";
        if(! empty($source_port)) {
            $tableSQL = "Port AS SP";
            $joinSQL = " INNER JOIN PortCompat AS PC ON SP.`type` = PC.type1 ";
            $joinSQL =  $joinSQL . " LEFT JOIN Port AS TP ON PC.type2 = TP.`type` ";
            $whereSQL = " AND SP.id = {$source_port}";
        } else {
            $tableSQL = "Port AS TP";
        }

        /*echo "SELECT TP.object_id AS tp_object_id,`TO`.`name` AS tp_obj_name ,TP.id AS tp_id, TP.name AS tp_name, TP.iif_id AS tp_iif_id, TPOI.oif_name ,TP.`type` AS tp_type
        FROM {$tableSQL}
         {$joinSQL}                                            
        LEFT JOIN Object AS `TO` ON `TO`.id = TP.object_id
        LEFT JOIN PortOuterInterface AS TPOI ON TPOI.id = TP.`type`
        WHERE TP.object_id = {$object_id} {$whereSQL} AND `TO`.objtype_id IS NOT NULL \n";*/

        $result = usePreparedSelectBlade ("SELECT TP.object_id AS tp_object_id,`TO`.`name` AS tp_obj_name ,TP.id AS tp_id, TP.name AS tp_name, TP.iif_id AS tp_iif_id, TPOI.oif_name ,TP.`type` AS tp_type
                                           FROM {$tableSQL}
                                            {$joinSQL}                                            
                                           LEFT JOIN Object AS `TO` ON `TO`.id = TP.object_id
                                           LEFT JOIN PortOuterInterface AS TPOI ON TPOI.id = TP.`type`
                                           WHERE TP.object_id = {$object_id} {$whereSQL} AND `TO`.objtype_id IS NOT NULL ");

        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {   
            $ret[] = array('id'=>intval($row['tp_id']),'label'=>$row['tp_name'],'interface'=> $row['oif_name']);
        }
        unset($result);
        return $ret;        
    }

    public static function getObjectSparePortList($port_info,$filter,$sort,$start=0,$limit=15) {
        $qparams = array ();
        $query = "SELECT SQL_CALC_FOUND_ROWS p.id, p.name,p.label, p.iif_id, p.type as oif_id,
                    pii.iif_name,
                    poi.oif_name,
                    p.object_id,
                    o.objtype_id as object_tid,
                    dict.dict_value AS object_type,
                    o.name as object_name,
                    o.label AS object_label
                  FROM Port p
                  INNER JOIN Object o ON o.id = p.object_id
                  INNER JOIN Dictionary dict ON o.objtype_id =dict.dict_key
                  INNER JOIN PortInnerInterface pii ON p.iif_id = pii.id
                  INNER JOIN PortOuterInterface poi ON poi.id = p.type ";
        // porttype filter (non-strict match)
        $query .= " INNER JOIN (SELECT Port.id FROM Port
        INNER JOIN
        (
            SELECT DISTINCT	pic2.iif_id
            FROM PortInterfaceCompat pic2
            INNER JOIN PortCompat pc ON pc.type2 = pic2.oif_id ";
            if ($port_info['iif_id'] != 1)
            {
                $query .= " INNER JOIN PortInterfaceCompat pic ON pic.oif_id = pc.type1 WHERE pic.iif_id = ? AND ";
                $qparams[] = $port_info['iif_id'];
            } else {
                $query .= " WHERE pc.type1 = ? AND ";
                $qparams[] = $port_info['oif_id'];
            }
            $query .= " pic2.iif_id <> 1 ) AS sub1 USING (iif_id)
                        UNION
                        SELECT Port.id
                        FROM Port
                        INNER JOIN PortCompat ON type1 = type
                        WHERE
                            iif_id = 1 and type2 = ?
                        ) AS sub2 ON sub2.id = p.id";

        $qparams[] = $port_info['oif_id'];
            // self and linked ports filter
        $query .= " WHERE p.id <> ? " .
            "AND p.id NOT IN (SELECT porta FROM Link) " .
            "AND p.id NOT IN (SELECT portb FROM Link) ";
        $qparams[] = $port_info['id'];
        // rack filter
        if (! empty ($filter['racks']))
        {
            // objects directly mounted in the racks
            $query .= sprintf
            (
                'AND p.object_id IN (SELECT DISTINCT object_id FROM RackSpace WHERE rack_id IN (%s) ',
                questionMarks (count ($filter['racks']))
            );
            // children of objects directly mounted in the racks
            $query .= sprintf
            (
                "UNION SELECT child_entity_id FROM EntityLink WHERE parent_entity_type='object' AND child_entity_type = 'object' AND parent_entity_id IN (SELECT DISTINCT object_id FROM RackSpace WHERE rack_id IN (%s)) ",
                questionMarks (count ($filter['racks']))
            );
            // zero-U objects mounted to the racks
            $query .= sprintf
            (
                "UNION SELECT child_entity_id FROM EntityLink WHERE parent_entity_type='rack' AND child_entity_type='object' AND parent_entity_id IN (%s)) ",
                questionMarks (count ($filter['racks']))
            );
            $qparams = array_merge ($qparams, $filter['racks']);
            $qparams = array_merge ($qparams, $filter['racks']);
            $qparams = array_merge ($qparams, $filter['racks']);
        }

        /*if (! empty ($filter['type']))
        {
            $query .= 'AND dict.dict_value like ? ';
            $qparams[] = '%' . $filter['type'] . '%';
        }*/

        //print_r($filter);
        if (! empty ($filter['field'])) {
            $field = $filter['field'];

            if ($field == 'name') {
                $query .= 'AND o.name like ? ';
                $qparams[] = '%' . $filter['terms'] . '%';
            } elseif ($field == 'label') {
                $query .= 'AND o.label like ? ';
                $qparams[] = '%' . $filter['terms'] . '%';
            } elseif($field == 'port_name') {
                $query .= 'AND p.name LIKE ? ';
                $qparams[] = '%' . $filter['terms'] . '%';
            } elseif($field == 'port_label') {
                $query .= 'AND p.label LIKE ? ';
                $qparams[] = '%' . $filter['terms'] . '%';
            }
        }

        $order_field = "o.name";
        $order_by = "";

        //print_r($sort);
        if (! empty ($sort['field'])) {
            if ($sort['field'] == 'name') 
                $order_field = "o.name";
            elseif ($sort['field'] == 'label')
                $order_field = "o.label";
            elseif ($sort['field'] == 'port_name')
                $order_field = "p.name";
            elseif ($sort['field'] == 'port_label')
                $order_field = "p.label";            

            if (! empty($sort['sortby']))
                $order_by = $sort['sortby'];
        }

    	$query .= ' ORDER BY '. $order_field . " " . $order_by;
        $query .= ' LIMIT '. $start .','. $limit;
        //echo $query ."\n";
        //print_r($qparams);
        $ret = array();
        $result = usePreparedSelectBlade ($query, $qparams);                
        
        // fetch port rows from the DB
        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {   
            $ret[] = $row;
        }

        $result = usePreparedSelectBlade("SELECT FOUND_ROWS()");
        $foundRows = $result->fetchColumn();

        unset($result);
        return array($foundRows,$ret);
    }
}
?>