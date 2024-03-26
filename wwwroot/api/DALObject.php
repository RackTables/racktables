<?php
class DALObject {
    //=========================================================
    //=======================  Temp API  ======================
    //=========================================================
    public static function getSummaryObjects ($alias)
    {
        $ret = array();
        $result = usePreparedSelectBlade ("SELECT O.name,O.label,OD.dict_value AS obj_type, AV.attr_id, A.name AS attr_name, A.type AS attr_type, C.name AS chapter_name, 
                                            C.id AS chapter_id, AV.uint_value, AV.float_value, AV.string_value, D.dict_value, O.id AS object_id FROM Object AS O
                                            LEFT JOIN Dictionary AS OD ON O.objtype_id = OD.dict_key
                                            LEFT JOIN AttributeMap AS AM ON O.objtype_id = AM.objtype_id
                                            LEFT JOIN Attribute AS A ON AM.attr_id = A.id
                                            LEFT JOIN AttributeValue AS AV ON AV.attr_id = AM.attr_id AND AV.object_id = O.id
                                            LEFT JOIN Dictionary AS D ON D.dict_key = AV.uint_value AND AM.chapter_id = D.chapter_id
                                            LEFT JOIN Chapter AS C ON AM.chapter_id = C.id
                                            WHERE A.name = ?
                                            ORDER BY object_id",array($alias)
                                          );
                                          
        $ret['Objects']['status'] = array('Active' => 0,
                                          'Planned' => 0,
                                          'Reserved' => 0,
                                          'Available' => 0,
                                          'Deprecated' => 0,
                                          'Unknow' => 0);
                                          
        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {
                $type = $row['obj_type'];
                if (isset($ret['Objects'][$type])) {
                    $ret['Objects'][$type]++;
                } else {
                    $ret['Objects'][$type] = 0;
                }

                $status_val = $row['dict_value'];
                if (isset($ret['Objects']['status'][$status_val])) {
                    $ret['Objects']['status'][$status_val]++;
                } else {
                    $ret['Objects']['status']['Unknow']++;
                }
                //$ret[] = $row;
        }
        unset($result);

        return $ret;
    }

    //=========================================================
    //=======================  FiMo API  ======================
    //=========================================================
    public static function getObjectIdByAssetId($type,$assetId,$is_multi=False)
    {
        $ret = array();        
        if (empty($type)) {
            if ($is_multi)
                $condition = "WHERE O.asset_no IN ($assetId)";
            else
                $condition = "WHERE O.asset_no='{$assetId}'";
        } else 
            $condition = "WHERE O.objtype_id={$type} AND O.asset_no='{$assetId}'";
        

        $result = usePreparedSelectBlade ("SELECT O.id AS object_id,O.asset_no AS asset_tag FROM Object AS O {$condition}");

        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {
            if ($is_multi) {
                $tag = $row['asset_tag'];
                $ret[$tag] = $row['object_id'];
            } else 
                $ret[] = $row;
        }
        unset($result);

        return $ret;
    }

    public static function getObjectRackData($realm,$ids,$simple_data=FALSE)
    {
        $ret = array();
        if ($realm == 'object') {
            $whereSQL = "WHERE O.id";
            if ($simple_data) 
                $whereSQL = $whereSQL . " = {$ids} LIMIT 1";
            else 
                $whereSQL = $whereSQL . " IN ({$ids})";
        } elseif ($realm == 'asset') {
            $whereSQL = "WHERE O.asset_no";
            if ($simple_data) 
                $whereSQL = $whereSQL . " = '{$ids}' LIMIT 1";
            else 
                $whereSQL = $whereSQL . " IN ($ids)";
        } else {
            
        }

        /*echo "SELECT O.id AS object_id,O.name AS object_name,O.objtype_id,O.label,O.asset_no AS assetId,
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
                                            {$whereSQL} ";*/

        /*$result = usePreparedSelectBlade ("SELECT O.id AS object_id,O.name AS object_name,O.label,O.asset_no AS assetId,R.location_id,R.location_name,R.row_id,R.row_name,RS.rack_id,R.name AS rack_name,RS.unit_no,RS.atom,RS.state FROM Object AS O 
                                           LEFT JOIN RackSpace AS RS ON RS.object_id = O.id 
                                           LEFT JOIN Rack AS R ON R.id = RS.rack_id 
                                            {$whereSQL} ");*/
        $result = usePreparedSelectBlade ("SELECT O.id AS object_id,O.name AS object_name,O.objtype_id,O.label,O.asset_no AS assetId,
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
                                            {$whereSQL} ");

        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            $row['zerou'] = ($row['zerou'] == 'Y')? True : False;

            //if (empty($row['rack_id'])) continue;
            if (! empty($row['rack_id'])) {
                $row['location_id'] = intval($row['location_id']);
                $row['row_id'] = intval($row['row_id']);
                $row['rack_id'] = intval($row['rack_id']);   
                if ($simple_data) {
                    $row['unit_no'] = array();
                    //unset($row['unit_no']);
                    unset($row['atom']);
                    unset($row['state']);
                    //unset($row['height']);
                    $ret[] = $row;
                } else {
                    if ($row['zerou'])
                        DALCommon::getRackZerou($ret,$row);
                    else 
                        DALCommon::getRackMolecule($ret,$row);
                }
            } else {
                unset($row['unit_no']);
                unset($row['atom']);
                unset($row['state']);
                //unset($row['height']);
                //$ret[] = $row;
                if ($simple_data) {
                    $ret[] = $row;
                } else {
                    $oid = $row['object_id'];
                    $ret[$oid] = $row;
                }
            }
        }
        unset($result);
        return $ret;
    }

        // input assetIds : 
    public static function getLinkData($objectId)
    {
        //echo $assetIds . "\n";
        $ret = array();

        $result = usePreparedSelectBlade ("SELECT
                                                Port.id,
                                                Port.name,
                                                Port.object_id,
                                                Object.name AS object_name,
                                                Object.objtype_id,
                                                Port.l2address,
                                                Port.label,
                                                Port.reservation_comment,
                                                Port.iif_id,
                                                Port.type AS oif_id,
                                                (SELECT PortInnerInterface.iif_name FROM PortInnerInterface WHERE PortInnerInterface.id = Port.iif_id) AS iif_name,
                                                (SELECT PortOuterInterface.oif_name FROM PortOuterInterface WHERE PortOuterInterface.id = Port.type) AS oif_name,
                                                IF(la.porta, la.cable, lb.cable) AS cableid,
                                                IF(la.porta, pa.id, pb.id) AS remote_id,
                                                IF(la.porta, pa.name, pb.name) AS remote_name,
                                                IF(la.porta, pa.label, pb.label) AS remote_label,
                                                IF(la.porta, pa.l2address, pb.l2address) AS remote_l2address,
                                                IF(la.porta, pa.object_id, pb.object_id) AS remote_object_id,
                                                IF(la.porta, pa.type, pb.type) AS remote_oif_id,
                                                IF(la.porta, oa.name, ob.name) AS remote_object_name,
                                                IF(la.porta, oa.objtype_id, ob.objtype_id) AS remote_object_tid,
                                                (SELECT PortOuterInterface.oif_name FROM PortOuterInterface WHERE PortOuterInterface.id = IF(la.porta, pa.type, pb.type)) AS remote_oif_name,
                                                (SELECT COUNT(*) FROM PortLog WHERE PortLog.port_id = Port.id) AS log_count,
                                                PortLog.user,
                                                UNIX_TIMESTAMP(PortLog.date) as time
                                            FROM
                                                Port
                                                INNER JOIN Object ON Port.object_id = Object.id
                                                LEFT JOIN Link AS la ON la.porta = Port.id
                                                LEFT JOIN Port AS pa ON pa.id = la.portb
                                                LEFT JOIN Object AS oa ON pa.object_id = oa.id
                                                LEFT JOIN Link AS lb on lb.portb = Port.id
                                                LEFT JOIN Port AS pb ON pb.id = lb.porta
                                                LEFT JOIN Object AS ob ON pb.object_id = ob.id
                                                LEFT JOIN PortLog ON PortLog.id = (SELECT id FROM PortLog WHERE PortLog.port_id = Port.id ORDER BY date DESC LIMIT 1)
                                            WHERE
                                        Object.id = {$objectId}
                                        ORDER BY LENGTH(Port.name), Port.name");

        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {
            $ret[] = $row;

        }
        unset($result);
        return $ret;
    }

    public static function getObjectTypeSet($objtype_ids,$hwtype_ids=NULL)
    {
        $ret = array();
        $result = usePreparedSelectBlade ("SELECT dict_key,dict_value FROM Dictionary WHERE dict_key IN ({$objtype_ids})");

        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {
            $id = $row['dict_key'];
            $ret[$id] = $row['dict_value'];
        
        }
        unset($result);

        return $ret;
    }

    public static function getObjectPropertys($objectIds,$assoc_key=FALSE)
    {
        $ret = array();
        $result = usePreparedSelectBlade ("SELECT O.id AS `object_id`,O.objtype_id, OBJT.obj_type AS `type`,O.name,O.label,O.asset_no,O.has_problems,A.id AS attr_id, A.name AS attr_name, A.type AS attr_type, C.name AS chapter_name, 
                                                    C.id AS chapter_id, AV.uint_value, AV.float_value, AV.string_value, D.dict_value,AE.`group`,AE.sort,TAGS.id AS tag_id,TAGS.tag AS tag_name FROM `Object` AS O 
                                           LEFT JOIN AttributeMap AS AM ON O.objtype_id = AM.objtype_id
                                           LEFT JOIN Attribute AS A ON AM.attr_id = A.id
                                           LEFT JOIN AttributeValue AS AV ON AV.attr_id = AM.attr_id AND AV.object_id = O.id
                                           LEFT JOIN Dictionary AS D ON D.dict_key = AV.uint_value AND AM.chapter_id = D.chapter_id
                                           LEFT JOIN Chapter AS C ON AM.chapter_id = C.id
                                           LEFT JOIN (SELECT TS.entity_id, TT.id, TT.tag FROM TagStorage AS TS INNER JOIN TagTree AS TT ON TS.tag_id = TT.id  WHERE TS.entity_realm = 'object') AS TAGS ON TAGS.entity_id = O.id
                                           LEFT JOIN (SELECT O.id, D.dict_value AS obj_type FROM RackObject AS O  LEFT JOIN Dictionary AS D ON  O.objtype_id = D.dict_key  LEFT JOIN Chapter AS C ON D.chapter_id = C.id WHERE C.name = 'ObjectType') AS OBJT ON OBJT.id = O.id 
                                           LEFT JOIN AttributeExtend AS AE ON A.`name` = AE.`name` 
                                           WHERE O.id IN ({$objectIds})
                                           ORDER BY O.id,AE.`group`,AE.sort ASC");
        $dict_key = array();
        $dict_prop = array();
        $dict_tag = array();
        $objIdx = 0;
        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {   
            
            $group = (empty($row['group']))? $row['type']:$row['group']; 
            $sort = $row['sort'];             
            $attr_vkey = (empty($row['uint_value']))? NULL:$row['uint_value'];
            $oid = $row['object_id']; 
            $attr_id = $row['attr_id'];            
            $tag_id = $row['tag_id'];
            $tags = array();
            $property = array();
            $attr_value = NULL;
            // settings properts
            $is_hwtype = False;
            $href = NULL;
            $attr_dict_val = "";
            
            if ($row['attr_type'] == 'dict') {
                $attr_value = $row['dict_value'];                    
            } elseif ($row['attr_type'] == 'string') {
                $attr_value = $row['string_value'];
            } elseif ($row['attr_type'] == 'uint') {
                $attr_value = $row['uint_value'];
            } elseif ($row['attr_type'] == 'float') {
                $attr_value = $row['float_value'];
            } elseif ($row['attr_type'] == 'date') {                    
                $attr_value = (empty($row['uint_value']))? NULL : $row['uint_value'];
            } 
 
           // settings tags
           if ((! isset($dict_tag[$oid][$tag_id])) && (! empty($tag_id))) {
                $tags = array('id'=> intval($tag_id),
                              'name'=> $row['tag_name']);

                if (! isset($dict_tag[$oid]))
                    $dict_tag[$oid] = array();

                $dict_tag[$oid][$tag_id] = true;
            }

            // settings properts
            if ((! isset($dict_prop[$oid][$attr_id])) && (! empty($attr_id))) {
                if ($row['attr_type'] == 'dict') {
                    if (preg_match ('/^\[\[(.+)\]\]$/', $attr_value, $matches))
                    {
                        $s = explode ('|', $matches[1]);
                        if (isset ($s[1]))
                            $href = trim ($s[1]);
                        $attr_value = trim ($s[0]);
                    }
        
                    if (preg_match('/\%GPASS\%/', $attr_value)) {
                        $dict_val_arr = explode("%GPASS%", $attr_value);
                        $attr_dict_val = implode(" ",$dict_val_arr);
                    } elseif (preg_match('/\%GSKIP\%/', $attr_value)) {
                        $dict_val_arr = explode("%GSKIP%",$attr_value);   
                        $attr_dict_val = implode(" ",$dict_val_arr);
                    } else {
                        $attr_dict_val = $attr_value;
                    }
                }

                //HW Type parser
                if (strtolower($row['attr_name']) == 'hw type') {
                    $is_hwtype = True;                            
                } else {
                    $attr_name = $row['attr_name'];
                    $property = array('id'=> intval($attr_id),
                                   'name'=> $attr_name,
                                   'type' => $row['attr_type'],
                                   'sort'=> $sort);
                    
                    if ($row['attr_type'] == 'dict') {
                        $property['dict_id'] = intval($attr_vkey);
                        $property['value'] = $attr_dict_val;
                    } else {
                        $property['value'] = $attr_value;
                    }
                }

                if (! isset($dict_prop[$oid]))
                    $dict_prop[$oid] = array();

                $dict_prop[$oid][$attr_id] = true;
            }

            //settings object
            if (isset($dict_key[$oid])) {
                if(! empty($attr_id)) {
                    if ($assoc_key)
                        $index = $oid;
                    else 
                        $index = $dict_key[$oid];

                    if (! empty($property)) {
                        if (! isset($ret[$index]['property'][$group]))
                            $ret[$index]['property'][$group] = array();
                        $ret[$index]['property'][$group][] = $property;
                    }

                    if ($is_hwtype) {
                        $ret[$index]['hardware_type_id'] = intval($attr_vkey);
                        $ret[$index]['hardware_type'] = $attr_dict_val;
                    }               

                    if (! empty($tags)) {
                        $ret[$index]['tags'][] = $tags;
                    }
                }
            } else {
                $rdata = array('id' => intval($oid),
                               'name' => $row['name'],
                               'label' => $row['label'],
                               'asset_tag' => $row['asset_no'],
                               'has_problems' => $row['has_problems'],
                               'object_type' => $row['type'],
                               'objtype_id' => intval($row['objtype_id']),
                               'tags' => array(),
                               'property' =>  NULL);

                if (! empty($property)) {                     
                    $rdata['property']=($is_hwtype)? array($property) : array($group => array($property));
                }                   
                
                if (! empty($tags)) $rdata['tags'][] = $tags;

                if ($is_hwtype) {
                    $rdata['hardware_type_id'] = intval($attr_vkey);
                    $rdata['hardware_type'] = $attr_dict_val;
                }
                
                if ($assoc_key) {
                    $ret[$oid] = $rdata;
                } else {
                    $ret[] = $rdata;
                }                                
                $dict_key[$oid] = $objIdx;
                $objIdx++;

            }
        }
        unset($result);
        return $ret;
    }
    

    public static function getAssetIdByObject($Ids,$has_objInfo=False)
    {
        //echo $assetIds . "\n";
        $ret = array();

        /*$result = usePreparedSelectBlade ("SELECT AV.object_id,AV.string_value AS asset_id,R.location_id,R.location_name,R.row_id,R.row_name,RS.rack_id,R.name AS rack_name,RS.unit_no,RS.atom,RS.state,R.height FROM Attribute AS A
                                           LEFT JOIN AttributeValue AV ON A.id = AV.attr_id
                                           LEFT JOIN RackSpace RS ON AV.object_id = RS.object_id
                                           LEFT JOIN Rack R ON R.id = RS.rack_id
                                           WHERE A.name='FiMo AssetID' AND {$whereSQL}");*/
        $result = usePreparedSelectBlade ("SELECT O.id AS object_id,D.dict_value AS objtype,O.name,O.label,O.objtype_id, O.asset_no AS asset_id FROM Object AS O
                                           LEFT JOIN Dictionary AS D ON  O.objtype_id = D.dict_key  
                                           WHERE O.id IN ({$Ids})");

        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {
            $id = $row['object_id'];
            if (! isset($ret[$id])) {
                if ($has_objInfo) {
                    $ret[$id] = $row;
                    //if ($row['key_name'] != 'FiMo AssetID')
                    //    $ret[$id]['asset_id']  = NULL;
                    unset($ret[$id]['object_id']);
                } else {
                    //if ($row['key_name'] == 'FiMo AssetID')
                    //    $ret[$id] = $row['asset_id'];
                    //else 
                        $ret[$id] = NULL;
                }           
            }        
        }
        unset($result);

        return $ret;
    }

    //### Develop II Base API
    public static function getObjectList($keyword,$filter,$sort,$start=0,$limit=15)
    {
        if (! is_numeric($start))
            $start=0;
        if (! is_numeric($limit))
            $limit=15;

            $ret = array();
            $orderSQL = "";
            $joinSQL = "";
            $querySQL = "";
            $groupSQL = "GROUP BY O.id";
            $filterSQL = "O.objtype_id NOT IN (1560,1561,1562)";
            $selectSQL = "SQL_CALC_FOUND_ROWS O.id,O.objtype_id,O.name,O.label,O.asset_no AS asset_tag";
            if (! empty($keyword))
                $querySQL = " AND (O.name LIKE '%{$keyword}%' OR O.label LIKE '%{$keyword}%')";      
    
            $join_arr = array();
            $is_sort = False;
            if (! empty($sort)) {            
                if ($sort['system']) {
                    if ($sort['id'] == 2) {
                        $selectSQL = $selectSQL . ",HW.dict_value AS hw_type";
                        $join_arr[] ="INNER JOIN (SELECT HAV.object_id,HAV.attr_id,D.dict_value,D.dict_key FROM `AttributeValue` AS HAV LEFT JOIN `Dictionary` AS D ON HAV.uint_value = D.dict_key WHERE HAV.attr_id=2) AS HW ON HW.object_id = O.id";
                        $orderSQL = "ORDER BY IF(ISNULL(HW.dict_value),1,0),HW.dict_value {$sort['sortby']}";
                    } elseif ($sort['id'] == 'object_type') {
                        $selectSQL = $selectSQL . ",DTYPE.dict_value";
                        $join_arr[] ="INNER JOIN `Dictionary` AS DTYPE ON O.objtype_id = DTYPE.dict_key AND DTYPE.chapter_id = 1";
                        $orderSQL = "ORDER BY IF(ISNULL(DTYPE.dict_value),1,0),DTYPE.dict_value {$sort['sortby']}";
                    } else 
                        
                        $orderSQL = "ORDER BY IF({$sort['id']}='' OR ISNULL({$sort['id']}),1,0),{$sort['id']} {$sort['sortby']}";        
                } else {
                    $selectSQL = "SQL_CALC_FOUND_ROWS O.id,O.objtype_id,O.name,O.label,O.asset_no AS asset_tag,SORT.attr_id,SORT.`type`,
                                  (CASE SORT.`type`
                                        WHEN 'string' THEN SORT.string_value
                                        WHEN 'uint' THEN SORT.uint_value
                                        WHEN 'float' THEN SORT.float_value
                                        WHEN 'dict' THEN SORT.dict_value
                                        WHEN 'date' THEN SORT.uint_value
                                        END) AS sort_field";
    
                    $join_arr[] ="LEFT JOIN (SELECT AM.objtype_id,AV.object_id,A.id,AV.attr_id,A.`type`,AV.string_value,AV.uint_value,AV.float_value,D.dict_value FROM `AttributeMap` AS AM
                                            LEFT JOIN `Attribute` AS A ON A.id = AM.attr_id
                                            LEFT JOIN `AttributeValue` AS AV ON AM.attr_id = AV.attr_id
                                            LEFT JOIN `Dictionary` AS D ON AV.uint_value = D.dict_key 
                                  ) AS SORT ON SORT.objtype_id = O.objtype_id AND O.id = SORT.object_id AND SORT.attr_id={$sort['id']}";
                    $orderSQL = "ORDER BY IF(ISNULL(sort_field),1,0),sort_field {$sort['sortby']}";        
                    $groupSQL = "GROUP BY O.id";
                }
                $is_sort = True;
            }
    
            if (! empty($filter)) {
                if (!empty($filter['objtype'])) {
                    $objtype = implode(",",$filter['objtype']);
                    $filterSQL = "O.objtype_id IN ({$objtype})";
                } 
                
                if (!empty($filter['hwtype'])) {
                    $hwtype = implode(",",$filter['hwtype']);
                    $add_join = True;
                    if ($is_sort) {
                        if ($sort['id'] == 2)
                            $add_join = False;
                    }
    
                    if ($add_join)
                        $join_arr[] ="INNER JOIN (SELECT HAV.object_id,HAV.attr_id,D.dict_value,D.dict_key FROM `AttributeValue` AS HAV LEFT JOIN `Dictionary` AS D ON HAV.uint_value = D.dict_key WHERE HAV.attr_id=2) AS HW ON HW.object_id = O.id";
                    $filterSQL = $filterSQL . " AND ( HW.dict_key IN ({$hwtype}))";
                } 
    
                if (! empty($filter['property'])) {
                    $filter_arr = array();
                    $conditionSQL = "";
                    foreach ($filter['property'] as $prop) {
                        //print_r($prop);
                        if ($prop['is_multiple']) {
                            if ($prop['is_int'])
                                $field_vals = implode(",",$prop['v']);
                            else 
                                $field_vals = "'" . implode("','",$prop['v']) . "'";
                            $fieldSQL = " IN ($field_vals)";
                        } else {
                            $field_val = ($prop['is_int'])? intval($prop['v']): "'".strval($prop['v'])."'";
                            $fieldSQL = " = " . $field_val;
                        }
                        $field_alias = "FAV_".$prop['id'];
    
                        $join_arr[] ="INNER JOIN `AttributeValue` AS {$field_alias} ON {$field_alias}.object_id = O.id AND {$field_alias}.attr_id={$prop['id']}";
                        
                        $op = (empty($prop['op']))? " AND " : " ". $prop['op'] ." ";
                        $filterSQL = $filterSQL . " {$op} {$field_alias}.{$prop['value_type']} {$fieldSQL}";
                    }
                }
    
                if (!empty($filter['tags'])) {
                    $tags = implode(",",$filter['tags']);
                    $join_arr[] = " LEFT JOIN `TagStorage` AS TS ON TS.entity_id = O.id AND TS.entity_realm ='object' ";
                    $filterSQL = $filterSQL . "AND tag_id IN ({$tags})";
                }
    
                if (!empty($filter['location']) || !empty($filter['row']) || !empty($filter['rack'])) {
                    $filter_arr = array();
                    if (!empty($filter['location'])) {
                        $location = implode(",",$filter['location']);
                        $filter_arr[] = "POS.location_id IN ({$location})";
                    } 
    
                    if (!empty($filter['row'])) {
                        $row = implode(",",$filter['row']);
                        $filter_arr[] = "POS.row_id IN ({$row})";
                    }
    
                    if (!empty($filter['rack'])) {
                        $rack = implode(",",$filter['rack']);
                        $filter_arr[] = "POS.id IN ({$rack})";
                    }

                    if (isset($filter['only_zerou']) && (!empty($filter['only_zerou']))) {
                        $join_arr[] = " INNER JOIN (SELECT R.location_id,R.row_id,R.id,RK.child_entity_id AS object_id FROM Rack AS R
                                                    INNER JOIN EntityLink AS RK ON R.id = RK.parent_entity_id AND RK.parent_entity_type = 'rack'
                                                    ) AS POS ON POS.object_id = O.id";
                    } else {
                        $join_arr[] = " INNER JOIN (SELECT R.location_id,R.row_id,R.id,RK.child_entity_id AS object_id FROM Rack AS R 
                                                    INNER JOIN EntityLink AS RK ON R.id = RK.parent_entity_id AND RK.parent_entity_type = 'rack' 
                                                    UNION 
                                                    SELECT R.location_id,R.row_id,R.id,RS.object_id FROM Rack AS R 
                                                    INNER JOIN RackSpace AS RS ON RS.rack_id = R.id 
                                                   ) AS POS ON POS.object_id = O.id ";
                    }
                    $filterSQL = "{$filterSQL} AND " . implode(" AND ", $filter_arr);
                }         
            }
    
            if (! empty($join_arr)) 
                $joinSQL = implode("  ", $join_arr);       
    
            if (!empty($filterSQL) || !empty($querySQL))
                $whereSQL = "WHERE {$filterSQL} {$querySQL}";

            /*echo "SELECT {$selectSQL} FROM `Object` AS O 
              {$joinSQL}
              {$whereSQL} 
              {$groupSQL}
              {$orderSQL}
            LIMIT {$start},{$limit}";*/
        //return array(0,$ret);

        $result = usePreparedSelectBlade ("SELECT {$selectSQL} FROM `Object` AS O 
                                            {$joinSQL}
                                            {$whereSQL} 
                                            {$groupSQL}
                                            {$orderSQL}
                                           LIMIT {$start},{$limit}");
        $dict_key = array();        
        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {   
            $id = strval($row['id']);
            if (! isset($dict_key[$id])) {
                if(isset($row['type'])) {
                    unset($row['type']);
                    unset($row['sort_field']);
                }

                $ret[$id] = $row;
                $dict_key[$id] = True;
            }
        }
        
        /*echo "SELECT COUNT(O.id) FROM `Object` AS O 
        WHERE O.objtype_id NOT IN (1560,1561,1562) $whereSQL \n";*/        
        $result = usePreparedSelectBlade("SELECT FOUND_ROWS()");
        $foundRows = $result->fetchColumn();
        unset($result);
        
        return array($foundRows,$ret);
    }

     //temp
    public static function getObjectContainerInfo($ids)
    {
        $ret = array();
        /*echo "SELECT O.id,OP.id AS op_id,OP.name AS op_name,OP.label AS op_label,OP.objtype_id AS op_objtypeid,OC.id AS oc_id,OC.name AS op_name,OC.label AS op_label,OC.objtype_id AS op_objtypeid FROM Object AS O 
                LEFT JOIN EntityLink AS ELC ON O.id = ELC.parent_entity_id
                LEFT JOIN Object AS OC ON ELC.child_entity_id = OC.id
                LEFT JOIN EntityLink AS ELP ON O.id = ELP.child_entity_id
                LEFT JOIN Object AS OP ON ELP.parent_entity_id = OP.id
                WHERE O.id IN ({$ids})
                AND (OP.id IS NOT NULL OR OC.id IS NOT NULL)";*/
        
        $result = usePreparedSelectBlade ("SELECT O.id,OP.id AS op_id,OP.name AS op_name,OP.label AS op_label,OP.objtype_id AS op_objtypeid,OC.id AS oc_id,OC.name AS op_name,OC.label AS op_label,OC.objtype_id AS op_objtypeid FROM Object AS O 
                                           LEFT JOIN EntityLink AS ELC ON O.id = ELC.parent_entity_id
                                           LEFT JOIN Object AS OC ON ELC.child_entity_id = OC.id
                                           LEFT JOIN EntityLink AS ELP ON O.id = ELP.child_entity_id
                                           LEFT JOIN Object AS OP ON ELP.parent_entity_id = OP.id
                                           WHERE O.id IN ({$ids})
                                           AND (OP.id IS NOT NULL OR OC.id IS NOT NULL)");

        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            $oid = intval($row['O.id']);
            
            
            #$ret['container'] = 
            #$ret['contains'] = 

            $row['id'] = intval($row['parent_id']);
            unset($row['child_id']);
            unset($row['parent_id']);
            if(isset($ret[$oid]))
                $ret[$oid][] = $row;
            else 
                $ret[$oid] = array($row);
        }
        unset($result);
        return $ret;
    }

    public static function getObjectContainer($ids)
    {
        $ret = array();
        /*echo "SELECT EL.child_entity_id AS child_id, EL.parent_entity_id AS parent_id, O.`name` AS name,O.label ,O.has_problems FROM EntityLink AS EL
        LEFT JOIN `Object` AS O ON O.id = EL.parent_entity_id
        WHERE EL.parent_entity_type = 'object' AND EL.child_entity_id IN ({$ids})";*/
        
        $result = usePreparedSelectBlade ("SELECT EL.child_entity_id AS child_id, EL.parent_entity_id AS parent_id, O.`name` AS name,O.label ,O.has_problems FROM EntityLink AS EL
                                           LEFT JOIN `Object` AS O ON O.id = EL.parent_entity_id
                                           WHERE EL.parent_entity_type = 'object' AND EL.child_entity_id IN ({$ids})");

        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            $oid = intval($row['child_id']);            
            $row['id'] = intval($row['parent_id']);
            unset($row['child_id']);
            unset($row['parent_id']);
            if(isset($ret[$oid]))
                $ret[$oid][] = $row;
            else 
                $ret[$oid] = array($row);
        }
        unset($result);
        return $ret;
    }

    public static function getObjtypeColumns($objtype,$is_id=True)
    {
        $ret = array();

        $selectSQL = "A.id,A.`type`,A.name";
        $whereSQL = "";
        $joinSQL = "";
        if ($is_id) {
            $whereSQL = "WHERE AM.objtype_id={$objtype}";
        } else {
            $selectSQL = "A.id,A.`type`,A.name,AM.objtype_id,D.dict_value";
            $joinSQL = "INNER JOIN Dictionary AS D ON AM.objtype_id = D.dict_key";
            $whereSQL = "WHERE D.dict_value IN ({$objtype})";

        }
        /*echo "SELECT {$selectSQL} AS AM
                {$joinSQL}
               LEFT JOIN Attribute AS A ON A.id = AM.attr_id
                {$whereSQL}";*/

        $result = usePreparedSelectBlade ("SELECT {$selectSQL} FROM AttributeMap AS AM 
                                            {$joinSQL}
                                           LEFT JOIN Attribute AS A ON A.id = AM.attr_id
                                            {$whereSQL}");

        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            if ($is_id)
                $ret[] = $row;
            else {                
                $objtype_name = $row['dict_value'];
                if (! isset($ret[$objtype_name]))
                    $ret[$objtype_name] = array();
                unset($row['dict_value']);

                $name = $row['name'];
                if (! isset($ret[$objtype_name][$name]))
                    $ret[$objtype_name][$name] = array();
                $ret[$objtype_name][$name] = $row;                
            }
        }
        unset($result);
        return $ret;
    }

    public static function getAttributeInfo($id=NULL)
    {
        $ret = array();
        $assoc_key = False;
        $whereSQL = "id NOT IN (2,27,29)";
        $joinSQL = "";
        $groupSQL = "";
        if (! empty($id)) {
            if (is_int($id))
                $whereSQL = "id = $id";
            else {
                $assoc_key = True;
                $whereSQL = "id IN ($id)";
            }
        } else {
            $joinSQL = "INNER JOIN AttributeMap AS AM ON A.id = AM.attr_id";
            $groupSQL = "GROUP BY AM.attr_id";
        }


        //echo "SELECT * FROM Attribute WHERE {$whereSQL} \n";
        $result = usePreparedSelectBlade ("SELECT * FROM Attribute AS A
                                            {$joinSQL}
                                           WHERE $whereSQL
                                            {$groupSQL} ");
                                            
        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            $id = intval($row['id']);
            $row['id'] = $id;
            if ($assoc_key) {                
                $ret[$id] = $row;
            } else 
                $ret[] = $row;
        }
        unset($result);
        return $ret;
    }

    public static function getManufacturerList($objtype=NULL)
    {
        $ret = array();
        $whereSQL = " WHERE AM.attr_id = 2 ";
        if (! empty($objtype))
            $whereSQL = $whereSQL . "AND AM.objtype_id = {$objtype}";

        $result = usePreparedSelectBlade ("SELECT D.*, IF(POSITION('%G' IN D.dict_value),SUBSTR(D.dict_value, 1, POSITION('%G' IN D.dict_value)-1),D.dict_value) AS manufacturer
                                           FROM AttributeMap AS AM
                                           LEFT JOIN Dictionary AS D ON AM.chapter_id = D.chapter_id 
                                           {$whereSQL}
                                           GROUP BY manufacturer");
        $dict_map = array();
        while ($row = $result->fetch (PDO::FETCH_ASSOC)) 
        {
            $id = $row['chapter_id'];
            $manufacturer = $row['manufacturer'];
            //echo $manufacturer. "\n";
            if (preg_match('/\[\[(.*)\s(.*)\s\|\s(.*)]]/', $manufacturer, $match_array)) {
                $manufacturer = $match_array[1];
            } else {
                $manufacturer = trim($manufacturer,"[[");              
            }

            //echo  $manufacturer;
            if (!in_array($manufacturer,$dict_map)) {
                //echo $manufacturer . "\n";
                $ret[] = array('id' => intval($id),
                               'manufacturer' => $manufacturer);
                $dict_map[] = $manufacturer;
            } 
        }
        unset($dict_map);
        unset($result);

        return $ret;
    }

    public static function getObjectTypeList($hwtype_ids=NULL)
    {
        //echo $assetIds . "\n";
        $ret = array();
        $selectSQL = "";
        $joinSQL = "";
        $whereSQL = "";
        $groupSQL = "";
        $is_filter = False;
        if (! empty($hwtype_ids)) {
            $selectSQL = "AM.objtype_id,D2.dict_value AS objtype_name";
            $joinSQL = "INNER JOIN AttributeMap AS AM ON D.chapter_id = AM.chapter_id
                        LEFT JOIN Dictionary AS D2 ON AM.objtype_id = D2.dict_key";
            $whereSQL = "AM.attr_id=2 AND D.dict_key IN ({$hwtype_ids})";
            $groupSQL = " GROUP BY AM.objtype_id " ;
        } else {
            $selectSQL = "D.dict_key AS objtype_id, D.dict_value AS objtype_name ";
            $whereSQL = "D.chapter_id = 1";
        }

        /*echo "SELECT {$selectSQL} FROM Dictionary AS D 
                                           {$joinSQL} 
                                           WHERE {$whereSQL} 
                                           {$groupSQL} "
        */
        $result = usePreparedSelectBlade ("SELECT {$selectSQL} FROM Dictionary AS D 
                                           {$joinSQL} 
                                           WHERE {$whereSQL} 
                                           {$groupSQL} ");


        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {            
            $id = intval($row['objtype_id']);
            $ret[] = array('id' =>  $id,
                           'name' => $row['objtype_name']);       
        }
        unset($result);

        return $ret;
    }

    public static function getHardwareType($objtype_ids)
    {
        $ret = array();
        $whereSQL = "";
        if ($objtype_ids) {            
            if (is_array($objtype_ids)) {
                $ids = implode(",",$objtype_ids);
            } else 
                $ids = $objtype_ids;

            $whereSQL = " AND AM.objtype_id IN ({$ids})";
        }

        $result = usePreparedSelectBlade ("SELECT D.chapter_id,AM.attr_id,D.dict_key, D.dict_value FROM Attribute AS A
                                           LEFT JOIN AttributeMap AS AM ON A.id = AM.attr_id
                                           LEFT JOIN Dictionary AS D ON D.chapter_id = AM.chapter_id
                                           WHERE A.id=2 $whereSQL");

        $group_map = array();
        $gindex = 0;
        while ($row = $result->fetch (PDO::FETCH_ASSOC)) 
        {            
            $id = $row['dict_key'];
            $original_value = $row['dict_value'];
            $href = NULL;
            if (preg_match ('/^\[\[(.+)\]\]$/', $row['dict_value'], $matches))
            {
                $s = explode ('|', $matches[1]);
                if (isset ($s[1]))
                    $href = trim ($s[1]);
                $original_value = trim ($s[0]);
            }

            if (preg_match('/\%GPASS\%/', $original_value)) {
                $hw_valarr = explode("%GPASS%", $original_value);
                $manufacturer = $hw_valarr[0];
                $hardware_type = $hw_valarr[1];
            } elseif (preg_match('/\%GSKIP\%/', $original_value)) {
                $hw_valarr = explode("%GSKIP%",$original_value);   
                $manufacturer = $hw_valarr[0];      
                $hardware_type = $hw_valarr[1];      
            } else {
                $manufacturer = 'other';
                $hardware_type = $original_value;
            }

            if (isset($group_map[$manufacturer])) {
                $index = $group_map[$manufacturer];
                $ret[$index]['children'][] = array('label' => $hardware_type,
                                                   'id' =>  intval($id));
            }else {                     
                $ret[] = array('label' => $manufacturer,
                               'id' => NULL,
                                'children' => array());

                $ret[$gindex]['children'][] = array('label' => $hardware_type,
                                                    'id' => intval($id));
                $group_map[$manufacturer] = $gindex;
                $gindex++;
            }
        }        
        unset($result);

        return $ret;
    }

    public static function getHardwareTypeByObjType($name_list){
        $ret = array();

        /*echo "SELECT AM.objtype_id,D.dict_value AS `objtype_name`,D2.chapter_id,D2.dict_key,D2.dict_value FROM AttributeMap AS AM
        LEFT JOIN Dictionary AS D ON AM.objtype_id = D.dict_key
        LEFT JOIN Attribute AS A ON A.id = AM.attr_id
        LEFT JOIN Dictionary AS D2 ON D2.chapter_id = AM.chapter_id
        WHERE A.id=2 AND D.dict_value IN ({$name_list})";*/

        $result = usePreparedSelectBlade ("SELECT AM.objtype_id,D.dict_value AS `objtype_name`,D2.chapter_id,D2.dict_key,D2.dict_value FROM AttributeMap AS AM
                                            LEFT JOIN Dictionary AS D ON AM.objtype_id = D.dict_key
                                            LEFT JOIN Attribute AS A ON A.id = AM.attr_id
                                            LEFT JOIN Dictionary AS D2 ON D2.chapter_id = AM.chapter_id
                                            WHERE A.id=2 AND D.dict_value IN ({$name_list})");

        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            $id = $row['dict_key'];
            $name = $row['objtype_name'];
            $original_value = $row['dict_value'];
            if (preg_match ('/^\[\[(.+)\]\]$/', $row['dict_value'], $matches))
            {
                $s = explode ('|', $matches[1]);
                if (isset ($s[1]))
                    $href = trim ($s[1]);
                $original_value = trim ($s[0]);
            }

            if (preg_match('/\%GPASS\%/', $original_value)) {
                $hw_valarr = explode("%GPASS%", $original_value);
                $hardware_type = implode(" ",$hw_valarr);
            } elseif (preg_match('/\%GSKIP\%/', $original_value)) {
                $hw_valarr = explode("%GSKIP%",$original_value);   
                $hardware_type = implode(" ",$hw_valarr);
            } else {
                $hardware_type = $original_value;
            }

            if (! isset($ret[$name]))
                $ret[$name] = array();

            if (! isset($ret[$name][$hardware_type]))
                $ret[$name][$hardware_type] = array();

            $ret[$name][$hardware_type] = array('objtype_id'=>$row['objtype_id'],
                                                'dict_key'=> $row['dict_key']);
        }
        unset($result);
        return $ret;
    }

    public static function getCommonNameByName($objtype_list,$name_list)
    {
        $ret = array();

        $result = usePreparedSelectBlade ("SELECT O.id,D.dict_value AS objtype_name,O.name FROM Object AS O 
                                           JOIN Dictionary AS D ON O.objtype_id = D.dict_key 
                                           WHERE O.name IN ({$name_list}) AND D.dict_value IN ({$objtype_list})");

        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            $objtype_id = $row['objtype_name'];
            $name = $row['name'];
            $alias = $objtype_id ."_". $name;
            if (!isset($ret[$alias]))
                $ret[$alias] = $row['id'];            
        }
        unset($result);
        return $ret;
    }

    
    public static function getAssetTagByName($name_list)
    {
        $ret = array();
        $result = usePreparedSelectBlade ("SELECT asset_no FROM `Object` WHERE asset_no IN ($name_list)");

        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            $asset_no = $row['asset_no'];
            if (!isset($ret[$asset_no]))
                $ret[$asset_no] = array();
            $ret[$asset_no] = True;
        }
        unset($result);
        return $ret;
    }


    public static function getObjectAttrs($objtype_id,$has_dict=False)
    {
        $ret = ($has_dict)?array('attributes'=>array(),'dicts'=>array()) : array();

        $result = usePreparedSelectBlade ("SELECT A.id,A.`type`,A.name,AM.chapter_id FROM AttributeMap AS AM 
                                           LEFT JOIN Attribute AS A ON AM.attr_id = A.id
                                           WHERE AM.objtype_id = {$objtype_id} AND A.id != 2");       

        while ($row = $result->fetch (PDO::FETCH_ASSOC)) 
        {   

            $id = $row['id'];
            $row['id'] = intval($id);
            if ($has_dict) {
                if ($row['type'] == 'dict') {                    
                    $ret['dicts'][$row['chapter_id']] = $id;
                    $row['items'] = NULL;
                }
                unset($row['chapter_id']);
                $ret['attributes'][$id] = $row;
            } else 
                $ret[] = $row;
        }
        unset($result);
        return $ret;
    }

    public static function getObjectAttrsDict($ids,$assoc_key=false)
    {
        $ret = array();
        /*echo "SELECT chapter_id,dict_key,dict_value FROM Dictionary D
        WHERE D.chapter_id IN ({$ids})";*/
        $result = usePreparedSelectBlade ("SELECT chapter_id,dict_key,dict_value FROM Dictionary D
                                           WHERE D.chapter_id IN ({$ids})");

        $group_map = array();
        $gindex = 0;
        $oldcid = 0;
        while ($row = $result->fetch (PDO::FETCH_ASSOC)) 
        {   
            if ($assoc_key) {                
                $cid = $row['chapter_id'];
                $id = $row['dict_key'];
                $original_value = $row['dict_value'];    

                if ($oldcid != $cid) {
                    $oldcid = $cid;
                    $gindex = 0;
                    $group_map = array();
                }
                //echo $cid . " : " . $original_value . "\n";

                $is_other = False;     
                if (preg_match ('/^\[\[(.+)\]\]$/', $row['dict_value'], $matches))
                {
                    $s = explode ('|', $matches[1]);
                    $original_value = trim ($s[0]);
                }

                if (preg_match('/\%GPASS\%/', $original_value)) {
                    $hw_valarr = explode("%GPASS%", $original_value);
                    $group = $hw_valarr[0];
                    $child = $hw_valarr[1];
                } elseif (preg_match('/\%GSKIP\%/', $original_value)) {
                    $hw_valarr = explode("%GSKIP%",$original_value);   
                    $group = $hw_valarr[0];      
                    $child = $hw_valarr[1];      
                } else {                
                    $group= 'other';
                    $child = $original_value;
                }

                if(! isset($ret[$cid]))
                    $ret[$cid] = array();
                unset($row['chapter_id']); 
                
                if (isset($group_map[$group])) {
                    $index = $group_map[$group];
                    $ret[$cid][$index]['children'][] = array('label' => $child,
                                                             'id' => intval($id));
                }else {                     
                    $ret[$cid][] = array('label' => $group,
                                         'id' => NULL,
                                         'children' => array());

                    $ret[$cid][$gindex]['children'][] = array('label' => $child,
                                                              'id' => intval($id));
                    $group_map[$group] = $gindex;
                    $gindex++;
                }

                
            } else 
                $ret[] = $row;
        }
        unset($result);
        return $ret;
    }

    public static function checkObjectColunmExists($column,$value) {        
        $result = usePreparedSelectBlade("SELECT COUNT(id) FROM Object WHERE `{$column}`='{$value}'");
        $count = $result->fetchColumn();
        unset ($result);
        return ($count>0)?TRUE:FALSE;
    }

    public static function getAttrValueList($id)
    {
        $ret = array();
        $result = usePreparedSelectBlade ("SELECT AV.attr_id,A.`type`,D.dict_key,  (CASE A.`type`
                                                    WHEN 'string' THEN AV.string_value
                                                    WHEN 'uint' THEN AV.uint_value
                                                    WHEN 'float' THEN AV.float_value
                                                    WHEN 'dict' THEN D.dict_value
                                                    WHEN 'date' THEN AV.uint_value
                                                    END) AS attr_val
                                           FROM `AttributeValue` AS AV
                                           LEFT JOIN `Attribute` AS A ON A.id = AV.attr_id
                                           LEFT JOIN `Dictionary` AS D ON AV.uint_value = D.dict_key 
                                           WHERE AV.attr_id = {$id}
                                           GROUP BY attr_val");

        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {           
            $value = "";
            $key = NULL;
            if ($row['type'] == 'string')
                $value = $row['attr_val'];
            elseif ($row['type'] == 'float')
                $value = $row['attr_val'];                
            else {
                $value = $row['attr_val'];
                if ($row['type'] == 'dict')
                    $key = $row['dict_key'];
            }
            $ret[] = array('type'=>$row['type'],'key'=> $key, 'value'=> $value);
        }
        unset($result);

        return $ret;
    }
    
    public static function getDictAttrValueListByName($objtype_id,$name_list) {
        $ret = array();
        $whereSQL = "A.`name` IN ($name_list)";
        if (! empty($objtype_id))
            $whereSQL = "AM.objtype_id = {$objtype_id} AND " . $whereSQL;

        /*echo "SELECT A.id,A.`type`,A.`name`,AM.chapter_id,D.dict_key,D.dict_value FROM Attribute AS A 
        JOIN AttributeMap AS AM ON A.id = AM.attr_id
        JOIN Dictionary AS D ON AM.chapter_id = D.chapter_id
        WHERE {$whereSQL}
        GROUP BY D.dict_key\n";*/
        $result = usePreparedSelectBlade ("SELECT A.id,A.`type`,A.`name`,AM.chapter_id,D.dict_key,D.dict_value FROM Attribute AS A 
                                           JOIN AttributeMap AS AM ON A.id = AM.attr_id
                                           JOIN Dictionary AS D ON AM.chapter_id = D.chapter_id
                                           WHERE {$whereSQL}
                                           GROUP BY D.dict_key");

        while ($row = $result->fetch (PDO::FETCH_ASSOC))
        {           
            $name = strtolower($row['name']);
            $original_value = $row['dict_value'];
            if (preg_match ('/^\[\[(.+)\]\]$/', $row['dict_value'], $matches))
            {
                $s = explode ('|', $matches[1]);
                if (isset ($s[1]))
                    $href = trim ($s[1]);
                $original_value = trim ($s[0]);
            }

            if (preg_match('/\%GPASS\%/', $original_value)) {
                $dict_valarr = explode("%GPASS%", $original_value);
                $dict_val = implode(" ",$dict_valarr);
            } elseif (preg_match('/\%GSKIP\%/', $original_value)) {
                $dict_valarr = explode("%GSKIP%",$original_value);   
                $dict_val = implode(" ",$dict_valarr);
            } else {
                $dict_val = $original_value;
            }

            if (! isset($ret[$name]))
                $ret[$name] = array();
            
            if (! isset($ret[$name][$dict_val]))
                $ret[$name][$dict_val] = array('id' => $row['id'],
                                               'chapter_id' => $row['chapter_id'],
                                               'type' => $row['type'],
                                               'dict_key' => $row['dict_key']);

        }
        unset($result);

        return $ret;
    }
}
?>