<?php
class DALmeterselect {
    function removeKeysWithUnderscore(&$array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                removeKeysWithUnderscore($value); 
            }
            if (strpos($key, '_') !== false) {
                unset($array[$key]); 
            }
        }
    }

    #小駝峰顯示邏輯
    function convertKeysToCamelCase(&$array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                convertKeysToCamelCase($value); 
            }
            $newKey = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', str_replace('.', '', $key))))); 
            if ($newKey !== $key) {
                $array[$newKey] = $value; 
                unset($array[$key]);
            }
        }
    }

    function fixArrayKey(&$arr)
    {
        $arr = array_combine(
            array_map(
                function ($str) { //strtolower
                    return str_replace(" ", "", preg_replace('/[^A-Za-z0-9\-]/', '', strtolower($str)));
                },
                array_keys($arr)
            ),
            array_values($arr)
        );
    }
    
    function fileter_common(){
        $datas = array();
    
        $sql = "SELECT name From racktables.AttributeExtend atr where atr.group not in ('Common','System') or atr.name  ='Purpose'";
        $result = usePreparedSelectBlade($sql);
        #return $sql;
        if ($result) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                #return $row['name'];
                array_push($datas,$row['name']);      
                #return $datas;
            }
        }

        if(!empty($result)){
            return $datas;
        }
        else{
            $datas['message']['data'] ='nodata';
            return $datas;
        }
    }

    function getTagsDropDownSelect($arg){
        $datas = array();
        if (empty($arg['group'])){
            $w = "";
        }
        else{
        $w = " where `group`  in ('".implode("','",$arg['group'])."')";
        }

        $sql = " SELECT name,`group`  From racktables.AttributeExtend ".$w ;
        //return $sql;
        $result = usePreparedSelectBlade($sql);
        if ($result) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                //return $row;
                $outputarray = array('name' => $row['name'],'group' =>$row['group']);
                
                array_push($datas,$outputarray);      
            }
        }

        if(!empty($result)){
            return $datas;
        }
        else{
            $datas['message']['data'] ='nodata';
            return $datas;
        }
    }


    function get_powerMeteritems(){

        $datas = array();
        $sql = " SELECT name,asset_no,label,dict_value FROM `Object` Obj left Join Dictionary d on d.dict_key =Obj.objtype_id where dict_value = 'Power Meter'" ;
        $result = usePreparedSelectBlade($sql);

        if ($result) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                    $datas[$row['name']] = $row;
                }

        }
        if(!empty($datas)){
            return $datas;
        }
        else{
            return "Nodata";
            return $datas['message'] = 'Nodata' ;
        }

    }
    function getpurpose($arg){
        $sql="
        select d.dict_value as assettype,o.name ,av.string_value  FROM  racktables.AttributeValue av 
        left join racktables.`Object` o on o.id  = av.object_id 
        left Join racktables.Dictionary d on d.dict_key =o.objtype_id 
        WHERE  av.attr_id =(SELECT id from racktables.`Attribute` a  WHERE a.name ='purpose')
        and object_id in('".$arg['id']."')";
        $result = usePreparedSelectBlade($sql);
        $datas = [];

        if ($result) {

            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                $datas[] = $row;
            }

        }

        return $datas;
    }

    function getMetersconfig($arg){
        $sql = "
        SELECT d.dict_value AS assettype, o.name,o.asset_no, av.string_value FROM racktables.`Object` o
        LEFT JOIN racktables.AttributeValue av on o.id = av.object_id  
        LEFT JOIN racktables.Dictionary d ON d.dict_key = o.objtype_id
        WHERE av.attr_id = (
                SELECT id FROM racktables.`Attribute` a WHERE a.name = 'Metrics Config'
        )
        AND object_id = :id
        ";

        $params = array('id' => $arg['id']);
        $result = usePreparedSelectBlade($sql, $params);
        $datas = [];

        if ($result) {
            $datas = $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return $datas;
    }


    function get_common_Data($token){

        $datas = array();
        $sql = "
        SELECT objtype_id,name,asset_no,label,dict_value
         FROM `Object` Obj 
         left Join Dictionary d on d.dict_key =Obj.objtype_id 
         LEFT JOIN TagStorage ts on ts.entity_id = Obj.id
         LEFT JOIN TagTree tt on  tt.id = ts.tag_id
         where dict_value = 'Power Meter' and tt.tag ='".$token."'
        
        " ;
        #return $sql;
        
        //$result = mysqli_query($link,$sql);
        $result = usePreparedSelectBlade($sql);

        if ($result) {

                while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                    $datas[$row['name']] = $row;
                }

        }

        if(!empty($datas)){
            return $datas;
        }
        else{
            return "Nodata";
            return $datas['message'] = 'Nodata' ;
        }

    }


    function get_extend_Data($where_condition){
        
        $datas = array();
        $tmp_array_ =   array();
        
        $sql = "SELECT name,Attrrbute_Name,string_value FROM (
                    SELECT Obj.name as name,attr_col.name as Attrrbute_Name,attr.string_value,d.dict_value as dict_value   FROM `Object` Obj
                        LEFT JOIN Dictionary d on d.dict_key =Obj.objtype_id 
                        LEFT JOIN AttributeValue attr on attr.object_id  = Obj.id  
                        LEFT JOIN `Attribute` attr_col on attr_col.id  = attr.attr_id  
                        WHERE attr.string_value  is not null
                    UNION ALL
                    SELECT Obj.name as Object_Name,attr_col.name as Attrrbute_Name,d.dict_value as string_value ,d2.dict_value as dict_value FROM `Object` Obj
                        LEFT JOIN AttributeValue attr on attr.object_id  = Obj.id  
                        LEFT JOIN `Attribute` attr_col on attr_col.id  = attr.attr_id  
                        LEFT JOIN Dictionary d on d.dict_key = attr.uint_value 
                        LEFT JOIN Dictionary d2 on d2.dict_key =Obj.objtype_id 
                        WHERE d.dict_value is not null 
                )
                AS tbl 
                WHERE name in  (   
                SELECT name FROM (
                    SELECT Obj.name as name,attr_col.name as Attrrbute_Name,attr.string_value,d.dict_value as dict_value   FROM `Object` Obj
                        LEFT JOIN Dictionary d on d.dict_key =Obj.objtype_id 
                        LEFT JOIN AttributeValue attr on attr.object_id  = Obj.id  
                        LEFT JOIN `Attribute` attr_col on attr_col.id  = attr.attr_id  
                        WHERE attr.string_value  is not null
                    UNION ALL
                    SELECT Obj.name as Object_Name,attr_col.name as Attrrbute_Name,d.dict_value as string_value ,d2.dict_value as dict_value FROM `Object` Obj
                        LEFT JOIN AttributeValue attr on attr.object_id  = Obj.id  
                        LEFT JOIN `Attribute` attr_col on attr_col.id  = attr.attr_id  
                        LEFT JOIN Dictionary d on d.dict_key = attr.uint_value 
                        LEFT JOIN Dictionary d2 on d2.dict_key =Obj.objtype_id 
                        WHERE d.dict_value is not null 
                ) as tmp
            ".$where_condition."
                )
                and Attrrbute_Name in (SELECT name From racktables.AttributeExtend atr where atr.group not in ('Common','System') or atr.name  ='Purpose')     
                
                
        ";

        $result = usePreparedSelectBlade($sql);
        if ($result) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                    $tmp_array = array();
                    $tmp_array = array(
                        $row['Attrrbute_Name'] => $row['string_value']
                    );
                    if (array_key_exists($row['name'],$tmp_array_)){
                        $tmp_array_[$row['name']]  = array_merge($tmp_array_[$row['name']],$tmp_array);
                    }
                    else {
                        $tmp_array_[$row['name']] = $tmp_array;
                    }
                }
        }
            return $tmp_array_;
    }


    function up_and_down_stream(){
        
        $tmp_array_up = array();
        $tmp_array_down =   array();
        
        $sql = "  SELECT Obj.name as item,p1.name as port_link,Obj2.name  as down_stream FROM  Link l
        LEFT JOIN Port p1 on p1.id =l.portb
        LEFT JOIN Port p2 on p2.id =l.porta
        LEFT JOIN PortOuterInterface POI on POI.id  = p1.`type`
        LEFT JOIN `Object` Obj on p1.object_id = Obj.id
        LEFT JOIN `Object` Obj2 on p2.object_id = Obj2.id
        LEFT Join Dictionary d on d.dict_key =Obj.objtype_id
        WHERE POI.oif_name like 'Electric Cable up'
        ";

        $result = usePreparedSelectBlade($sql);

        if ($result) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)){                
    
                    if (array_key_exists($row['item'],$tmp_array_up)){
                        $tmp_array_up[$row['item']]  = array_merge($tmp_array_up[$row['item']],array($row['down_stream']));
                    }
                    else {
                        $tmp_array_up[$row['item']] = array($row['down_stream']);
                    }

                    if (array_key_exists($row['down_stream'],$tmp_array_down)){
                        $tmp_array_down[$row['down_stream']]  = array_merge($tmp_array_down[$row['down_stream']],array($row['item']));
                    }
                    else {
                        $tmp_array_down[$row['down_stream']] = array($row['item']);
                    }

               // }
                
            }
        }
            return [$tmp_array_up,$tmp_array_down];
    }

    function down_stream(){
        
        $tmp_array_up = array();
        $tmp_array_down =   array();
        
        $sql = " 

         
        SELECT * from  (
            SELECT Obj.name as item,p1.name as port_link,Obj2.name as down_stream FROM  Link l
           LEFT JOIN Port p1 on p1.id =l.portb
           LEFT JOIN Port p2 on p2.id =l.porta
           LEFT JOIN PortOuterInterface POI on POI.id  = p1.`type`
           
           LEFT JOIN `Object` Obj on p1.object_id = Obj.id
           LEFT JOIN `Object` Obj2 on p2.object_id = Obj2.id
           LEFT Join Dictionary d on d.dict_key =Obj.objtype_id
           WHERE POI.oif_name like  'Electric Cable Down'
           
           
           union  
           
            SELECT Obj2.name as item,p1.name as port_link,Obj.name as down_stream FROM  Link l
           LEFT JOIN Port p1 on p1.id =l.portb
           LEFT JOIN Port p2 on p2.id =l.porta
           LEFT JOIN PortOuterInterface POI on POI.id  = p2.`type`
           
           LEFT JOIN `Object` Obj on p1.object_id = Obj.id
           LEFT JOIN `Object` Obj2 on p2.object_id = Obj2.id
           LEFT Join Dictionary d on d.dict_key =Obj.objtype_id
           WHERE POI.oif_name like  'Electric Cable Down'
           
           )
           
           AS tbl


        ";

        //$result = mysqli_query($link,$sql);
        $result = usePreparedSelectBlade($sql);

        if ($result) {
            //if (mysqli_num_rows($result)>0) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                //while ($row = mysqli_fetch_assoc($result)) {
                
                    if (array_key_exists($row['down_stream'],$tmp_array_down)){
                        $tmp_array_down[$row['down_stream']]  = array_merge($tmp_array_down[$row['down_stream']],array($row['item']));
                    }
                    else {
                        $tmp_array_down[$row['down_stream']] = array($row['item']);
                    }
               // }
                
            }
        }
            //mysqli_free_result($result);
            return $tmp_array_down;
    }

    function up_stream(){
        
        $tmp_array_up = array();
        $tmp_array_down =   array();
        
        $sql = "  
         
        SELECT * from  (
           SELECT Obj.name as item,p1.name as port_link,Obj2.name as down_stream FROM  Link l
           LEFT JOIN Port p1 on p1.id =l.portb
           LEFT JOIN Port p2 on p2.id =l.porta
           LEFT JOIN PortOuterInterface POI on POI.id  = p2.`type`
           
           LEFT JOIN `Object` Obj on p1.object_id = Obj.id
           LEFT JOIN `Object` Obj2 on p2.object_id = Obj2.id
           LEFT Join Dictionary d on d.dict_key =Obj.objtype_id
           WHERE POI.oif_name like  'Electric Cable Up'

           union  
          
           SELECT Obj2.name as item,p1.name as port_link,Obj.name as down_stream FROM  Link l
           LEFT JOIN Port p1 on p1.id =l.portb
           LEFT JOIN Port p2 on p2.id =l.porta
           LEFT JOIN PortOuterInterface POI on POI.id  = p1.`type`
           
           LEFT JOIN `Object` Obj on p1.object_id = Obj.id
           LEFT JOIN `Object` Obj2 on p2.object_id = Obj2.id
           LEFT Join Dictionary d on d.dict_key =Obj.objtype_id
           WHERE POI.oif_name like  'Electric Cable Up'
           )
           AS tbl

        
        ";
        $result = usePreparedSelectBlade($sql);
        if ($result) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                    if (array_key_exists($row['item'],$tmp_array_up)){
                        $tmp_array_up[$row['item']]  = array_merge($tmp_array_up[$row['item']],array($row['down_stream']));
                    }
                    else {
                        $tmp_array_up[$row['item']] = array($row['down_stream']);
                    }
            }
        }
            return $tmp_array_up;
    }

    function getMeterList($token,$filter_tag,$filter_condition,$hosttagMap,$powermeteritmes){      

        $where_condition ="";           
        $where_condition_array = array();     
        $count = 0;
        if (isset($filter_condition["WhereConditions"])){
            if (isset($filter_condition['Method'])){
                if ($filter_condition['Method'] == 'equal'){
                        foreach ($filter_condition['WhereConditions'] as &$value) {
                            $count = $count+1;
                            $d =  array_keys($filter_condition['WhereConditions'],$value)[0];
                            $where_condition = "Attrrbute_Name = '".$d ."' and string_value in ('";
                            $where_condition  =$where_condition .implode("','",$filter_condition['WhereConditions'][$d])."')";
                            array_push($where_condition_array,$where_condition);
                        }
                        $where_condition =  "where ". implode(' or ',$where_condition_array) . " GROUP  by name having count(*)>=".$count;
                    }

                elseif($filter_condition['Method'] == 'like'){

                        foreach ($filter_condition['WhereConditions'] as &$value) {
                            $count = $count+1;
                            $d =  array_keys($filter_condition['WhereConditions'],$value)[0];
                            $where_condition = "Attrrbute_Name like '".$d ."' and string_value like '%";
                            $where_condition  =$where_condition .implode("%' or Attrrbute_Name like '".$d. "' and string_value like '%",$filter_condition['WhereConditions'][$d])."%' ";
                            array_push($where_condition_array,$where_condition);
                        }
                    }

                else{
                    return $filter_condition['Method']." not support";
                }
                $where_condition =  "where ". implode(' or ',$where_condition_array) . " GROUP  by name having count(*)>=".$count;
            }
            else{
                if (isset($filter_condition['WhereConditions'])==TRUE){

                foreach ($filter_condition['WhereConditions'] as &$value) {
                    if ($value['Method']  == 'equal'){
                            $count = $count+1;
                            $d =  array_keys($filter_condition['WhereConditions'],$value)[0];
                            $where_condition = "Attrrbute_Name = '".$d ."' and string_value in ('";
                            $where_condition  =$where_condition .implode("','",$filter_condition['WhereConditions'][$d]['Value'])."')";
                            array_push($where_condition_array,$where_condition);
                        }
                        if($value['Method'] == 'like'){
                            $count = $count+1;
                            $d =  array_keys($filter_condition['WhereConditions'],$value)[0];
                            $where_condition = "Attrrbute_Name like '".$d ."' and string_value like '%";
                            $where_condition  = $where_condition .implode("%' or Attrrbute_Name like '".$d. "' and string_value like '%",$filter_condition['WhereConditions'][$d]['Value'])."%' ";
                            array_push($where_condition_array,$where_condition);
                        }
                    }
                }
            }
        }
        else{
            $where_condition = "";
        }
        if ($count <>0){
            $where_condition =  "where ". implode(' or ',$where_condition_array) . " GROUP  by name having count(*)>=".$count;
        }  
        $all = array();
        $item_host_map = array();
        $datas_common = DALmeterselect::get_common_Data($token);    
        $datas = DALmeterselect::get_extend_Data($where_condition); 
        $down_stream_ =DALmeterselect::down_stream();                                                                                                                               
        $up_stream_ =DALmeterselect::up_stream();  
        #return $datas_common;                                                                                                                                                      
        foreach ($datas as $key => $value){     
            if (!in_array($key, $powermeteritmes)){
               continue;
            }                                                                                                                             
            $arr = array();                                                                                                                                                         
            $arr_up = array();                                                                                                                                                      
            if (array_key_exists($key,$down_stream_)){                                                                                                                              
                //                                                                                                                                                                  
                $arr = array(                                                                                                                                                       
                    'Downstream'  =>  $down_stream_[$key]                                                                                                                               
                );                                                                                                                                                                  
            }                                                                                                                                                                       
            else{                                                                                                                                                                   
                $arr = array(                                                                                                                                                       
                    'Downstream'  => []                                                                                                                                             
                );                                                                                                                                                                  
            }                                                                                                                                                                       
                                                                                                                                                                                    
            if (array_key_exists($key,$up_stream_)){                                                                                                                                
                $arr_up = array(                                                                                                                                                    
                'Upstream'  =>  $up_stream_[$key]                                                                                                                                   
                );                                                                                                                                                                  
            }                                                                                                                                                                       
            else{                                                                                                                                                                   
                $arr_up = array( 
                'Upstream'  => []                                                                                                                                               
                );                                                                                                                                                                  
            }                                                                                                                                                                       
                                                                                                                                                                                    
            $a = array_map('trim', array_keys($datas[$key]));                                                                                                                       
            $b = array_map('trim', $datas[$key]);                                                                                                                                   
            $datas[$key] = array_combine($a, $b);                                                                                                                             
                                                              
            
            if (array_key_exists('Metrics Config',$datas[$key])){    
                                                                                                                                                                                    
                $metrics = json_decode($datas[$key]['Metrics Config'],true);                                                                                                        
                $datas[$key]['Metrics Config'] = $metrics;                                                                                                                          
          
                $purpose = $datas[$key]['Purpose'];   
                unset($datas[$key]['Purpose']);                                                                                                      
                if  (array_key_exists($filter_tag,$metrics['Power'])){                                                                                                              
                    array_push($item_host_map,array('host'=>$key,'item'=> $metrics['Power'][$filter_tag][0]));                                                                      
                    $mag[$key]=array(                                                                                                                                               
                        'Name' =>$datas_common[$key]['name'],
                        'Description' =>$purpose,                                                                                                                
                        'Hosttag' =>$hosttagMap[$key],                                                                                                                            
                        'AssetAtrributes' =>$datas[$key]                                                                                                                            
                    );                                                                                                                                                              
                                                                                                                                                                                    
                    unset($mag[$key]['tags']['Description']);   
                                                                                                                                                                                                                                                                                        
                    $merge  = array_merge($mag[$key],$arr_up,$arr);                                                                                                                 
                    DALmeterselect::fixArrayKey($merge);                                                                                                                                                                                                                                                                                    
                    array_push($all,$merge);                                                                                                                                        
               }                                                                                                                                                                    
            }                                                                                                                                                                       
       }
        $outputarray = array('Config' => $all,'ItemName' =>$item_host_map);                                                                                                         
        DALmeterselect::fixArrayKey($outputarray);                                                                                                                                  
        return $outputarray;                                                                                                                                                        
    }

    #function getMeterInfo
    #hosttag -> tags 
    #rm -f assetatrributes

   function getMeterInfo($token,$filter_tag,$filter_condition,$hosttagMap,$powermeteritmes){      

        $where_condition ="";           
        $where_condition_array = array();     
        $count = 0;
        if (isset($filter_condition["WhereConditions"])){
            if (isset($filter_condition['Method'])){
                if ($filter_condition['Method'] == 'equal'){
                        foreach ($filter_condition['WhereConditions'] as &$value) {
                            $count = $count+1;
                            $d =  array_keys($filter_condition['WhereConditions'],$value)[0];
                            $where_condition = "Attrrbute_Name = '".$d ."' and string_value in ('";
                            $where_condition  =$where_condition .implode("','",$filter_condition['WhereConditions'][$d])."')";
                            array_push($where_condition_array,$where_condition);
                        }
                        $where_condition =  "where ". implode(' or ',$where_condition_array) . " GROUP  by name having count(*)>=".$count;
                    }

                elseif($filter_condition['Method'] == 'like'){

                        foreach ($filter_condition['WhereConditions'] as &$value) {
                            $count = $count+1;
                            $d =  array_keys($filter_condition['WhereConditions'],$value)[0];
                            $where_condition = "Attrrbute_Name like '".$d ."' and string_value like '%";
                            $where_condition  =$where_condition .implode("%' or Attrrbute_Name like '".$d. "' and string_value like '%",$filter_condition['WhereConditions'][$d])."%' ";
                            array_push($where_condition_array,$where_condition);
                        }
                    }

                else{
                    return $filter_condition['Method']." not support";
                }
                $where_condition =  "where ". implode(' or ',$where_condition_array) . " GROUP  by name having count(*)>=".$count;
            }
            else{
                if (isset($filter_condition['WhereConditions'])==TRUE){

                foreach ($filter_condition['WhereConditions'] as &$value) {
                    if ($value['Method']  == 'equal'){
                            $count = $count+1;
                            $d =  array_keys($filter_condition['WhereConditions'],$value)[0];
                            $where_condition = "Attrrbute_Name = '".$d ."' and string_value in ('";
                            $where_condition  =$where_condition .implode("','",$filter_condition['WhereConditions'][$d]['Value'])."')";
                            array_push($where_condition_array,$where_condition);
                        }
                        if($value['Method'] == 'like'){
                            $count = $count+1;
                            $d =  array_keys($filter_condition['WhereConditions'],$value)[0];
                            $where_condition = "Attrrbute_Name like '".$d ."' and string_value like '%";
                            $where_condition  = $where_condition .implode("%' or Attrrbute_Name like '".$d. "' and string_value like '%",$filter_condition['WhereConditions'][$d]['Value'])."%' ";
                            array_push($where_condition_array,$where_condition);
                        }
                    }
                }
            }
        }
        else{
            $where_condition = "";
        }
        if ($count <>0){
            $where_condition =  "where ". implode(' or ',$where_condition_array) . " GROUP  by name having count(*)>=".$count;
        }  
        $all = array();
        $item_host_map = array();
        $datas_common = DALmeterselect::get_common_Data($token);    
        $datas = DALmeterselect::get_extend_Data($where_condition); 
        $down_stream_ =DALmeterselect::down_stream();                                                                                                                               
        $up_stream_ =DALmeterselect::up_stream();  
        #return $datas_common;                                                                                                                                                      
        foreach ($datas as $key => $value){     
            if (!in_array($key, $powermeteritmes)){
               continue;
            }                                                                                                                             
            $arr = array();                                                                                                                                                         
            $arr_up = array();                                                                                                                                                      
            if (array_key_exists($key,$down_stream_)){                                                                                                                              
                //                                                                                                                                                                  
                $arr = array(                                                                                                                                                       
                    'Downstream'  =>  $down_stream_[$key]                                                                                                                               
                );                                                                                                                                                                  
            }                                                                                                                                                                       
            else{                                                                                                                                                                   
                $arr = array(                                                                                                                                                       
                    'Downstream'  => []                                                                                                                                             
                );                                                                                                                                                                  
            }                                                                                                                                                                       
                                                                                                                                                                                    
            if (array_key_exists($key,$up_stream_)){                                                                                                                                
                $arr_up = array(                                                                                                                                                    
                'Upstream'  =>  $up_stream_[$key]                                                                                                                                   
                );                                                                                                                                                                  
            }                                                                                                                                                                       
            else{                                                                                                                                                                   
                $arr_up = array( 
                'Upstream'  => []                                                                                                                                               
                );                                                                                                                                                                  
            }                                                                                                                                                                       
                                                                                                                                                                                    
            $a = array_map('trim', array_keys($datas[$key]));                                                                                                                       
            $b = array_map('trim', $datas[$key]);                                                                                                                                   
            $datas[$key] = array_combine($a, $b);                                                                                                                             
                                                              
            
            if (array_key_exists('Metrics Config',$datas[$key])){    
                                                                                                                                                                                    
                $metrics = json_decode($datas[$key]['Metrics Config'],true);                                                                                                        
                $datas[$key]['Metrics Config'] = $metrics; 
               
                $purpose = $datas[$key]['Purpose'];   
                unset($datas[$key]['Purpose']);                                                                                                      
                if  (array_key_exists($filter_tag,$metrics['Power'])){     
                    unset($datas[$key]['Metrics Config']);         
                    #DALmeterselect::fixArrayKey($hosttagMap[$key]); 
                    #DALmeterselect::convertKeysToCamelCase($datas[$key]);         
                    #DALmeterselect::fixArrayKey($datas[$key]);      
                    self::removeKeysWithUnderscore($hosttagMap[$key]);
                    if (!isset($hosttagMap[$key]))$hosttagMap[$key] =[];
                                                                                                                         
                                                                                                                   
                    array_push($item_host_map,array('host'=>$key,'item'=> $metrics['Power'][$filter_tag][0]));                                                                      
                    $mag[$key]=array(                                                                                                                                               
                        'Name' =>$datas_common[$key]['name'],                                                                                                                       
                        'Description' =>$purpose,                                                                                                                
                        'tags' =>array("zabbix"=> $hosttagMap[$key],"asset" =>$datas[$key])                                                                                                                            
                     #  'AssetAtrributes' =>$datas[$key]                                                                                                                            
                    );                                                                                                                                                              
                                                                                                                                                                                    
                    unset($mag[$key]['tags']['Description']);   
                                                                                                                                                                                                                                                                                        
                    $merge  = array_merge($mag[$key],$arr_up,$arr);                                                                                                                 
                    DALmeterselect::fixArrayKey($merge);                                                                                                                                                                                                                                                                                    
                    array_push($all,$merge);                                                                                                                                        
               }                                                                                                                                                                    
            }                                                                                                                                                                       
       }
        DALmeterselect::fixArrayKey($all);        
        $outputarray = array('Config' => $all,'ItemName' =>$item_host_map);                                                                                                         
        DALmeterselect::fixArrayKey($outputarray);                                                                                                                                  
        return $outputarray;                                                                                                                                                        
    }
    
}

?>