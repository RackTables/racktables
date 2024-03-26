<?php
class DALCommon {
    public static function getRackMolecule(&$ret,$row) {
        $oid = $row['object_id'];
        $unit_key = NULL;            
        if ($row['atom'] == 'front') {
            $unit_key = 'F';
        } elseif ($row['atom'] == 'interior'){
            $unit_key = 'I';
        } elseif ($row['atom'] == 'rear') {
            $unit_key = 'R';
        }
        $unit_no =  $row['unit_no'];
        if (isset($ret[$oid])) {
            if (! empty($unit_key))
                $ret[$oid]['units'][$unit_no][$unit_key] = True;                
        } else {     
            $ret[$oid] = $row;
            if(isset($row['height']))
                $ret[$oid]['rack_height'] = $row['height'];
            $ret[$oid]['units'] = array();
            $ret[$oid]['units'][$unit_no] = array('F'=> False,'I'=> False,'R'=> False);   
            if (! empty($unit_key))
                $ret[$oid]['units'][$unit_no][$unit_key] = True;         
            unset($ret[$oid]['atom']);
            unset($ret[$oid]['height']);
            unset($ret[$oid]['state']);                        
            unset($ret[$oid]['unit_no']);
            unset($ret[$oid]['object_id']);
        }
    }

    public static function getRackZerou(&$ret,$row) {
        $oid = $row['object_id'];
        $ret[$oid] = $row;
        $ret[$oid]['rack_height'] = $row['height'];
        $ret[$oid]['units'] = NULL;
        unset($ret[$oid]['unit_no']);
        unset($ret[$oid]['atom']);
        unset($ret[$oid]['height']);
        unset($ret[$oid]['state']);                        
        unset($ret[$oid]['object_id']);
    }    
}

?>