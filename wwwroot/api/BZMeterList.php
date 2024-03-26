<?php
require_once 'DALMeterList.php';

class BZMeterList {

    public static function  MeterSelect($args){
      return DALMeterSelect::getMeterList($args['token'],$args['filter_tag'],$args['filter_conditions'],$args['host_tag_map'],$args['powermeteritems']);
    }
    public static function  getMeterInfo($args){
      return DALMeterSelect::getMeterInfo($args['token'],$args['filter_tag'],$args['filter_conditions'],$args['host_tag_map'],$args['powermeteritems']);
    }
    public static function  PowerMeteritems($args){
      return  DALMeterSelect::get_powerMeteritems();
    }
    public static function TagsDropDownSelect($args) {
      return  DALMeterSelect::getTagsDropDownSelect($args);
    } 
    public static function getMetersconfig($args) {
      $result =  DALMeterSelect::getMetersconfig($args);
      $result['purpose'] = DALMeterSelect::getpurpose($args);
      return $result;
    } 
} ?>