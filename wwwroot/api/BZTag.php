<?php
include ('DALTag.php');

class BZTag {
    public static function addObjectTag($object_id,$parent_id,$tag,$description) {
        $check_result = False;
        $error = "";
        if (validTagName($tag,True)) {
            try {
                    $tag_id = DALTag::addObjectTag($parent_id,$tag,$description);
                    $check_result = True;
            } catch (RTDatabaseError $e) {
                $error = array('field'=>'tag name','msg'=> $e->getMessage());
            }
        } else {
            $error = array('field'=>'tag name','msg'=> 'Invalid tag name');
        }            
        if (! empty($object_id))
            spotEntity ('object', intval($object_id));

        $ret = array('create'=> $check_result,'id'=>intval($tag_id),'error'=> $error);
        return $ret;
    }

    public static function deleteObjectTag($tag_id,$force=False) {
        $check_result = False;
        $error = "";
        try {
            if (DALTag::checkCanDeleteTag($tag_id)){
                if (DALTag::checTagRefObjectExists($tag_id) == False) 
                    $check_result = DALTag::deleteObjectTag($tag_id);
                else 
                    $error = array('field'=>NULL,'msg'=> "Remove tag ({$tag}) failed, tag or child tag(s) still in connection with asset(s)");
            } else 
                $error = array('field'=>NULL,'msg'=> "Remove tag ({$tag}) failed, tag or child tag(s) still in connection with asset(s)");
        } catch (RTDatabaseError $e) {
            $error = array('field'=>NULL,'msg'=> $e->getMessage());
        }
        $ret = array('delete'=> $check_result,'id'=>intval($tag_id),'error'=> $error);
        return $ret;
    }
    
    public static function updateObjectTag($tag_id, $tag_name, $parent_id, $is_assignable='yes') {
        $check_result = False;
        $error = array();
        if ((!empty($tag_id)) && (!empty($tag_name))) {
            if (is_numeric($tag_id)) {
                if (validTagName($tag_name,True)) {              
                    try {
                        commitUpdateTag ($tag_id, $tag_name, $parent_id, $is_assignable,NULL);
                        $check_result = True;
                    } catch (RTDatabaseError $e) {
                        $error[] = array('field'=>NULL,'msg'=> $e->getMessage());
                    }
                } else {
                    $error = array('field'=>'tag name','msg'=> 'Invalid tag name');
                }                
            } else {
                $error[] = array('field'=>'id','msg'=>"tag id invalid");
            }
        } else {
            if (empty($tag_id)) $error[] = array('field'=>'id','msg'=>"tag id empty");
            if (empty($tag_name)) $error[] = array('field'=>'name','msg'=>"tag name empty");
        }

        $ret = array('updated'=> $check_result,'id'=>intval($tag_id),'error'=> $error);
        return $ret;
    }

    public static function getTagTree($is_detail=False) {
        $data = getTagUsage();
        foreach ($data AS &$tag) {
            //print_r($tag);
            if ($tag['is_assignable'] != 'yes')
                continue;
            $label = $tag['tag'];
            $tag['label'] = $label;
            $tag['id'] = intval($tag['id']);
            $tag['parent_id'] = intval($tag['parent_id']);
            $refcnt = intval($tag['refcnt']['total']);
            $tag['refcnt'] = $refcnt;
            unset($tag['tag']);            
            unset($tag['is_assignable']);
            unset($tag['color']);
            if (! $is_detail) {
                unset($tag['description']);    
                //unset($tag['refcnt']);    
            }
        }
        
        $ret = treeFromList ($data);
        
        if (count($ret)>0) {
            return array(True, $ret);
        } else {
            return array(True,array());
        }
    }

    public static function getTagNameList($tag_list) {
        $templist = explode(",",$tag_list);
        $tag_list  = "'".implode("','",$templist)."'"; 
        $ret = DALTag::getTagByName($tag_list);
        return array(True,$ret);
    }
}
?>
