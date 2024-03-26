<?php
class DALIP {
    public static function getSummaryIPs()
    {
        $ret = array();
        $subject = array();
        $subject[] = array ('q' => 'select count(id) from IPv4Network', 'txt' => 'IPv4 Address');
        $subject[] = array ('q' => 'select count(id) from IPv6Network', 'txt' => 'IPv6 Address');    

        foreach ($subject as $item)
        {
            $result = usePreparedSelectBlade ($item['q']);
            $ret[$item['txt']] = $result->fetchColumn();
            unset ($result);
        }
        return $ret;
    }

}
?>