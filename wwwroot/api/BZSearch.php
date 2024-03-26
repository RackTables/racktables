<?php

class BZSearch {
    public static function SearchContext($terms,$reverse_filter=array('vlan','ipv4rspool','ipvs','ipv4vs',))
    {
        try
        {
            parseSearchTerms ($terms);
            // Discard the return value as searchEntitiesByText() and its retriever
            // functions expect the original string as the parameter.
        }
        catch (InvalidArgException $iae)
        {
            showError ($iae->getMessage());
        }
        
        $ret = array();
        foreach(searchEntitiesByText($terms) as $entity_key => $entity_info) {
            if (in_array($entity_key,$reverse_filter))
                continue;

            if ($entity_key == 'ipv4addressbydescr') {
                $ret[$entity_key] = array();
                foreach($entity_info as $ip_bin => $ip_info) {
                    $ip_text = ip_format ($ip_bin);
                    $net_id = getIPAddressNetworkId($ip_bin);
                    $tmp_info = array('address' => $ip_text,
                                      'name' => $ip_info['name'],
                                      'network_id' => $net_id,
                                      'comment' => $ip_info['comment']);
                    $ret[$entity_key][] = $tmp_info;
                }
                //print_r($ret['ipv4addressbydescr']);
            } elseif ($entity_key == 'ipv4net') {
                foreach($entity_info as $v4net_id => $v4net_info) {
                    $tmp_info = $v4net_info;
                    unset( $tmp_info['ip_bin']);
                    unset( $tmp_info['mask_bin']);
                    unset( $tmp_info['spare_ranges']);
                    /*                    if (! empty($tmp_info['spare_ranges'])) {
                        $tmp_spares = array();
                        foreach ($tmp_info['spare_ranges'] as $mask => $list)
                        {
                            $spare_mask = $mask;
                            // align spare IPv6 nets by nibble boundary
                            if (strlen ($netdata['ip_bin']) == 16 && $mask % 4)
                                $spare_mask = $mask + 4 - ($mask % 4);

                            foreach ($list as $ip_bin) {
                                foreach (splitNetworkByMask (constructIPRange ($ip_bin, $mask), $spare_mask) as $spare) {
                                    $tmp_spares = $spare + array('kids' => array(), 'kidc' => 0, 'name' => '');
                                    unset($tmp_spares['ip_bin'])
                                    unset($tmp_spares['mask_bin']);
                                }
                            }
                            print_r($tmp_spares);
                        }                        
                    }                    */
                    $ret[$entity_key][] = $tmp_info;
                }
            } else {
                $ret[$entity_key] = $entity_info;
            }
        }
        print_r($ret);
        return array();
    }
}
?>
