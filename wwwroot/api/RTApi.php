<?php
include ('../inc/init.php');
include ('../inc/dictionary.php');
include ('DALCommon.php');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$api_version = 'v1';
if ($uri[3] == 'v2' || $uri[3] == 'V2') {
    $api_class = $uri[4];
    $api_version = 'v2';
    $api_method = "";
    $params = NULL;    
    $api_act = strtolower($_SERVER['REQUEST_METHOD']);
    if ($api_act == 'post' || $api_act == 'put') { //CREATE
        //echo $api_act;
        if (count($uri) > 7) {
            $api_method = $uri[5] . "_" . $uri[6];
            $params = array();
            $_size = count($uri) - 1;
            for($i= 7; $i < $_size; $i++) {
                $params[]= $uri[$i];
            }

        } else {
            $api_method = $uri[5] . "_" . $uri[6];
        }
    } elseif ($api_act == 'delete') { //delete
        $params = array();
        $api_method = $uri[5] . "_" . $uri[6];
        for($i= 7; $i < $_size; $i++) {
            $params []= $uri[$i];
        }

    } else { // get
        //echo count($uri);
        if (count($uri) > 7) {
            $_size = count($uri) - 1;
            for($i= 5; $i < $_size; $i++) {
                $api_method = $api_method . "_" . $uri[$i];
            }

            $api_method = strtolower($_SERVER['REQUEST_METHOD']) . $api_method;
            $params = $uri[$_size];
        } else {
            $api_method = strtolower($_SERVER['REQUEST_METHOD']) . "_" . $uri[5] . "_" . $uri[6];
        }
    }

    if (empty($api_class) | empty($api_method)) {
        header("HTTP/1.1 404 Not Found");
        exit();
    }
 
} else {    
    if (!isset($uri[3]) | !isset($uri[4])) {
        header("HTTP/1.1 404 Not Found");
        exit();
    }

    $api_class = $uri[3];
    $api_method = $uri[4];
    $params = NULL;
}

//echo "$api_version,$api_class,$api_method,$params";

if (file_exists($api_class.'.php')) {
    loadClass($api_version,$api_class,$api_method,$params);
} else {
    header("HTTP/1.1 403 Forbidden");
    exit();
}


function loadClass($api_version,$api_class,$api_method,$path_params) {
    $data = array();
    $data['success'] = False;
    
    try {
        include($api_class.'.php');
        if (method_exists($api_class,$api_method)) {
            $params = array_merge($_GET, $_POST);            
            if ($api_version == 'v1') {
                $data = call_user_func(array($api_class, $api_method),$params);
            } else {
                $data = call_user_func(array($api_class, $api_method),$params,$path_params);
            }
        }
    } catch(Exception $e) {         
        $data['message'] = $e->getMessage();
    }    
    //var_dump($data);
    if (isset($data['header'])) {
        sendOutput($data, array('Content-Type: application/json', $data['header']));
        unset($data['header']);
    } else {
        if (isset($data['success'])) {
            sendOutput($data,array('Content-Type: application/json', 'HTTP/1.1 200 OK'));  
        } else {
            sendOutput($data, array('Content-Type: application/json', 'HTTP/1.1 500 Internal Server Error'));
        }
    }
}

function sendOutput($data, $httpHeaders=array())
{
        header_remove('Set-Cookie');
 
        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        } 
        echo json_encode($data);
        exit;
}

function getQueryStringParams()
{
    return parse_str($_SERVER['QUERY_STRING'], $query);
}

?>