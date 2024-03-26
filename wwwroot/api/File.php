<?php
include('common.php');
include('BZFile.php');

class File {
    public static function getImage($args) {
        $file = BZFile::ImgSelect($args['name']);
        //header("Content-type: image/jpeg;base64");

       // print($file);
      // header("Content-length: 365367");
       
      // header("Content-type: image/jpeg");

      // echo $file['contents'];
       echo '<img src="data:image/jpeg;base64,'.base64_encode($file['contents']).'" />';
        exit;
    }
}

?>