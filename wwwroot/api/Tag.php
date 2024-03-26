<?php
include ('common.php');
include('BZTag.php');

class Tag {
    public static function getTagTree($args) {
        $is_detail = False;
        if (! empty($args['is_detail']))
            $is_detail = True;

        list($success,$data) = BZTag::getTagTree($is_detail);
        return outSuccess($data,count($data));
    }

    public static function getTagNameList($args) {
        $name_list = (isset($args['name_list']))? $args['name_list'] : NULL;

        list($success,$data) = BZTag::getTagNameList($name_list);
        if ($success) {
            return outSuccess($data,count($data));
        } else {
            return outFail($data);
        }
    }

    public static function addObjectTag($args) {
        $parent_id = NULL;
        if (! empty($args['parent_id']))
            $parent_id = $args['parent_id'];
        $tag = NULL;
        if (! empty($args['tag']))
            $tag = $args['tag'];
        $description = NULL;
        if (! empty($args['description']))
            $description = $args['description'];

        $data = BZTag::addObjectTag(NULL,$parent_id,$tag,$description);
        return outSuccess($data,1);
    }

    public static function updateObjectTag($args) {
        $parent_id = NULL;
        if (! empty($args['parent_id']))
            $parent_id = $args['parent_id'];
        $tag = NULL;
            if (! empty($args['tag']))
                $tag = $args['tag'];            
        $data = BZTag::updateObjectTag($args['tag_id'],$tag,$parent_id);
        return outSuccess($data,1);
    }

    public static function deleteObjectTag($args) {
        $data = BZTag::deleteObjectTag($args['tag_id']);
        return outSuccess($data,1);
    }    
}
?>