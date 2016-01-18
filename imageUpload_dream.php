<?php

    // http://121.42.38.84/imageUpload.php/?userPhone=18500140212&imageName=18500140212.png&imageData=23423525a324234214342342412342134
//    require_once ('./Db.php');
//    require_once ('./Dreams.php');
//    
//    $params = array();
//    $params["user_id"]      = $_POST["user_id"];
//    $params["text"]         = $_POST["text"];
//    $params["time"]         = $_POST["time"];
//    $params["type"]         = $_POST["type"];
//    $params["imageName"]    = $_POST["imageName"];
//    $params["pic"]          = $_POST["pic"];
//    
//    $dream_api = new Dreams();
//    $ret = $dream_api->composeDream($params);
//    
//    file_put_contents("dream_imge.txt", $ret);
//    
//    exit($ret);

header("Content-Type: application/octet-stream");
     $byte=$_POST['pic'];
    
     $byte = str_replace(' ','',$byte);   //处理数据 
     $byte = str_ireplace("<",'',$byte);
     $byte = str_ireplace(">",'',$byte);
     $byte=pack("H*",$byte);      //16进制转换成二进制
     
     file_put_contents("test.png", $byte);