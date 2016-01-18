<?php

    // http://121.42.38.84/imageUpload.php/?userPhone=18500140212&imageName=18500140212.png&imageData=23423525a324234214342342412342134
    require_once ('./Db.php');
    require_once ('./config.php');
    
    static $data = array(
        'result' => '0',
        'msg' => '',
        'data' => ''
    );

    function saveImage($postData)
    {
        $user_phone = $postData["userPhone"];
        $image_name = $postData["imageName"];
        $image_data = $postData["imageData"];
        
        $image_data = str_replace(' ', '', $image_data);
        $image_data = str_ireplace("<", '', $image_data);
        $image_data = str_ireplace(">", '', $image_data);

        $db_login = DB::GetInstance();
        $connect_login = $db_login->GetConnection();

        $user_exist = $db_login->is_value_exist("users", "phone", $user_phone);
        
        if($user_exist){
         
            $image = $db_login->value_for_id("users", "image", "phone", $user_phone);
            
            if ($image){
                $imagePath = substr($image, strlen(Config::$web_host) + 1);
                file_put_contents("imageUpload.txt", $imagePath);
                $result = @unlink ($imagePath); 
            }
                
            
            $imageData = hex2bin($image_data);
            $imagePath = "./images/".$image_name;
            file_put_contents($imagePath, $imageData);
        
            $web_host = Config::$web_host;
            $image_name = "$web_host/images/".$image_name;
         
            $sql = "update users set image='$image_name' where phone='$user_phone'";
            $db_login->query($sql);
            
            $data['data'] = "Hello";
            $data['result'] = 200;
            $data['msg'] = '图片保存成功!';
        }
        else{
            //echo $image;
            $data['data'] = "Hello";
            $data['result'] = 201;
            $data['msg'] = '图片保存失败!';            
        } 
        return json_encode($data);
    }
    
$params = array();
$params["userPhone"]  = $_POST["userPhone"];
$params["imageName"]  = $_POST["imageName"];
$params["imageData"]  = $_POST["imageData"];

exit(saveImage($params));