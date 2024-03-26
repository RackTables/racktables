<?php
class DALFile {
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

        foreach ($arr as $key => $val) {
            if (is_array($val)) {
               DALmeterselect::fixArrayKey($arr[$key]);
            }
        }
    }

    function getImg($name){

        //return $name;
        
        $sql = " SELECT * FROM File where name like '%".$name."%'";
        //return $sql
        //$result = mysqli_query($link,$sql);
        $result = usePreparedSelectBlade($sql);
        
        if ($result) {
            //if (mysqli_num_rows($result)>0) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                   // return "123";
                    return $row;
                }

        }

    }



    function GetImage($name){
        //require_once 'db_connect.php';
        return DALFile::getImg($name);
        //mysqli_close($link);
    }
    
}

?>

