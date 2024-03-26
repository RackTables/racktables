<?php
class DALTag {
    public static function addObjectTag($parent_id,$tag,$description,$assignable='yes') {
        usePreparedInsertBlade
        (
            'TagTree',
            array
            (
                'parent_id' => $parent_id,
                'tag' => nullIfEmptyStr ($tag),
                'is_assignable' => $assignable,
                'color' => NULL,
                'description' => nullIfEmptyStr ($description),
            )
        );
        return  lastInsertID();
    }   

    public static function deleteObjectTag($tag_id) {
        $check=usePreparedDeleteBlade('TagTree', array ('id'=>$tag_id));
        return ($check == 1)?True:False;
    }   

    public static function checkCanDeleteTag($tag_id) {
        $result = usePreparedSelectBlade ("SELECT COUNT(TT2.id) FROM TagTree AS TT
                                           INNER JOIN TagTree AS TT2 ON TT.id = TT2.parent_id
                                           WHERE TT.id = {$tag_id}");
        $count = $result->fetchColumn();
        unset ($result);
        return ($count>0)?False:True;
    }

    public static function checTagRefObjectExists($tag_id) {
        $result = usePreparedSelectBlade ("SELECT COUNT(TS.entity_id) FROM TagStorage AS TS WHERE TS.tag_id = {$tag_id}");
        $count = $result->fetchColumn();
        unset ($result);
        return ($count>0)?True:False;
    }

    public static function checkObjectTag($tag_id) {
        $check=usePreparedDeleteBlade('TagTree', array ('id'=>$tag_id));
        return ($check == 1)?True:False;
    }   
    
    public static function getTagByName($tags,$single=False)
    {
        $ret = array();
        $whereSQL = "TT.tag IN ($tags)";
        if ($single)
            $whereSQL = "TT.tag='$tags'";

        $result = usePreparedSelectBlade ("SELECT TT.id,TT.parent_id,TT.tag FROM TagTree AS TT
                                            WHERE {$whereSQL}");

        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            $name = $row['tag'];
            if (!isset($ret[$name]))
                $ret[$name] = array();
            $ret[$name] = $row['id'];
        }
        unset($result);
        return $ret;
    }

    

}
?>