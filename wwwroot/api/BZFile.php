<?php
require_once 'DALFile.php';

class BZFile {
    public static function ImgSelect($name){
        return DALFile::GetImage($name);
    }
    
}