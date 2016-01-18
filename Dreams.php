<?php
require_once ('./Db.php');
require_once ('./config.php');

//header("Content-Type:text/html;charset=UTF-8"); 
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Dreams{
    public static $data = array(
        'result' => '0',
        'msg' => '',
        'data' => ''
    );

    static private $_db_data;
    static private $_connect_data;

    public function __construct(){
        if(!self::$_db_data){
            self::$_db_data = DB::GetInstance();
            self::$_connect_data = self::$_db_data->GetConnection();
        }
    }
    
        //判断具体是哪个接口
    public function chooseType($postData = array()){
        $data_type = $postData['api_type'];

        switch($data_type){
            case "getData":
                return self::getDreamInfo($postData);
            case "composeDream":
                return self::composeDream($postData);
                break;
            case "getDirections":
                return self::getDirections($postData);
                break;
            case "getCollections";
                return self::getCollections($postData);
                break;
            case "cancelCollectedDream":
                return self::cancelCollectedDream($postData);
                break;
            case "collectDream":
                return self::collectDream($postData);
                break;
            default:
                break;
        }
    }
    
    //获取数据   http://192.168.1.101/?api_uid=getDreams&api_type=getData
    public function getDreamInfo($postData = array()){
        $data = self::$data;
        $user_id = $postData['user_id'];
        $dream_type = $postData['dream_type'];
        $cur_user_id = $postData['cur_user_id'];
        
        $sql = "";
        if ($user_id == "all"){
            if($dream_type == 0){
                $sql = "select * from user_dreams as ud left join users as u on ud.user_id = u.id left join dream_types as dt on dt.type_id = ud.type left join dream_collections as dc on dc.user_id = $cur_user_id and ud.id = dc.dream_id order by ud.id desc";
            }else{
                $sql = "select * from user_dreams as ud left join users as u on ud.user_id = u.id left join dream_types as dt on dt.type_id = ud.type left join dream_collections as dc on dc.user_id = $cur_user_id and ud.id = dc.dream_id where ud.type = $dream_type order by ud.id desc";
            }
        }else{
            $sql = "select * from user_dreams as ud left join users as u on ud.user_id = u.id left join dream_types as dt on dt.type_id = ud.type left join dream_collections as dc on dc.user_id = $cur_user_id and ud.id = dc.dream_id where ud.user_id = $user_id order by ud.id desc";
        }
            
        $result = self::$_db_data->query($sql, self::$_connect_data);
        $countRight = self::$_db_data->num_rows($result);

        $file = fopen("log.txt", "w") or die("Unable to open file!");
        fwrite($file, $sql);
        
        if($countRight == 0) {
            $data['result'] = 201;
            $data['msg'] = 'Data Not Exist！';
            return json_encode($data);
        }
        else {
            $info = array();
            while($row = mysql_fetch_array($result)){
                $dream = array();
                $dream['idStr'] = $row[0];
                $dream['text'] = $row[1];
                $dream['pic_url'] = $row[2];
                $dream['time'] = $row[3];
                $dream['support_count'] = $row[6];
                $dream['unsupport_count'] = $row[7];
                $dream['comment_count'] = $row[8];
                $dream['type'] = $row[19];
                $collectionStatus = $row[22];
                if ($collectionStatus != NULL)
                    $dream['collection'] = "1";
                else
                    $dream['collection'] = "0";

                $userInfo = array();
                $userInfo['user_id'] = $row[5];
                $userInfo['name'] = $row[10];
                $image = $row[14];
                if($image != "")
                    $userInfo['image'] = $row[14];
                else
                    $userInfo['image'] = "";
                $userInfo['userMail'] = $row[12];
                $userInfo['userRealName'] = $row[13];
                $userInfo['userSex'] = $row[15];
                $userInfo['userWords'] = $row[16];
                $userInfo['userAddr'] = $row[17];
                
                $dream['user'] = $userInfo;
                
                array_push($info, $dream);
            }

            $data['result'] = 200;
            $data['msg'] = 'User Exist,Successful!';
            $data['data'] = $info;
            return json_encode($data);
        }
    }
    
    // 发表梦想  http://192.168.1.101/?api_uid=getDreams&api_type=composeDream&user_id=3&text=你来打我呀
    public function composeDream($postData = array()){
        $data = self::$data;
        $user_id=$postData['user_id'];
        $dream_text = $postData['text'];
        $dream_time = $postData['time'];
        $image_data = $postData['pic'];
        $image_name = $postData['imageName'];
        $dream_type = $postData['type'];
        
        $web_host = Config::$web_host;
        
        if ($image_data){
            $image_data = str_replace(' ', '', $image_data);
            $image_data = str_ireplace("<", '', $image_data);
            $image_data = str_ireplace(">", '', $image_data);
            
            $imageData = hex2bin($image_data);
            $imagePath = "./images/".$image_name;
            file_put_contents($imagePath, $imageData);
            
            $image_name = "$web_host/images/".$image_name;
         
        }else{
            $image_name = "";
            //file_put_contents("images.txt", "world");
        }
        
        $sql = "INSERT INTO user_dreams (text, pic_url, time, type, user_id) values('$dream_text', '$image_name', '$dream_time', $dream_type, $user_id);";
        $result = self::$_db_data->query($sql);
        
//        file_put_contents("dreams.txt", $sql);
        
        if(!$result) {
            $data['result'] = 201;
            $data['msg'] = 'Data Not Exist！';
            return json_encode($data);
        }
        else {

            $data['result'] = 200;
            $data['msg'] = 'Dream Composed,Successful!';
            $data['data'] = "";
            return json_encode($data);
        }
    } 
    
    // 获取梦想鸡汤
    public function getDirections($postData = array()){
        $data = self::$data;
        
        $sql = "select * from dream_directions;";
            
        $result = self::$_db_data->query($sql, self::$_connect_data);
        $countRight = self::$_db_data->num_rows($result);

        $file = fopen("log.txt", "w") or die("Unable to open file!");
        fwrite($file, $sql);
        
        if($countRight == 0) {
            $data['result'] = 201;
            $data['msg'] = 'Data Not Exist！';
            return json_encode($data);
        }
        else {
            $info = array();
            while($row = mysql_fetch_array($result)){
                $direction = array();
                
                $direction['idStr'] = $row[0];
                $direction['title'] = $row[1];
                $direction['text'] = $row[2];
                array_push($info, $direction);
            }

            $data['result'] = 200;
            $data['msg'] = 'Directions Get,Successful!';
            $data['data'] = $info;
            return json_encode($data);
        }
    }
    
    // 获取收藏的梦想
    public function getCollections($postData = array()){
        $data = self::$data;
        
        $user_id = $postData['user_id'];
        
        $sql = "select * from user_dreams as ud left join users as u on ud.user_id = u.id left join dream_types as dt on dt.type_id = ud.type left join dream_collections as dc on dc.user_id = 1 and ud.id = dc.dream_id where ud.id in (select dream_id from dream_collections where user_id = $user_id) order by ud.id desc;";
            
        $result = self::$_db_data->query($sql, self::$_connect_data);
        $countRight = self::$_db_data->num_rows($result);

        $file = fopen("dream_getCollections.txt", "w") or die("Unable to open file!");
        fwrite($file, $sql);
        
        if($countRight == 0) {
            $data['result'] = 201;
            $data['msg'] = 'Data Not Exist！';
            return json_encode($data);
        }
        else {
            $info = array();
            while($row = mysql_fetch_array($result)){
                $dream = array();
                $dream['idStr'] = $row[0];
                $dream['text'] = $row[1];
                $dream['pic_url'] = $row[2];
                $dream['time'] = $row[3];
                $dream['support_count'] = $row[6];
                $dream['unsupport_count'] = $row[7];
                $dream['comment_count'] = $row[8];
                $dream['type'] = $row[19];
                $collectionStatus = $row[22];
                if ($collectionStatus != NULL)
                    $dream['collection'] = "1";
                else
                    $dream['collection'] = "0";
                
                $user = array();
                $user['user_id'] = $row[5];
                $user['name'] = $row[13];
                $image = $row[14];
                if($image != "")
                    $user['image'] = $row[14];
                else
                    $user['image'] = "";
                $user['mail'] = $row[12];
                $user['phone'] = $row[10];
                $user['sex'] = $row[15];
                $user['words'] = $row[16];
                $user['addr'] = $row[17];
                $dream['user'] = $user;
                
                array_push($info, $dream);
            }

            $data['result'] = 200;
            $data['msg'] = 'User Exist,Successful!';
            $data['data'] = $info;
            return json_encode($data);
        }
    }
    
    // 收藏梦想
    public function collectDream($postData = array()){
        $data = self::$data;
        
        $user_id = $postData['user_id'];
        $dream_id = $postData['dream_id'];
        
        $sql_condition = "user_id = $user_id and dream_id = $dream_id";
        $IsExisted = self::$_db_data->is_double_value_exist("dream_collections", $sql_condition);
        if ($IsExisted){
            $data['result'] = 200;
            $data['msg'] = '梦想已收藏！';
        }else{
            $sql = "INSERT INTO dream_collections (user_id, dream_id) VALUES ($user_id, $dream_id)";
        
            //file_put_contents("users_focus.txt", $sql);

            $result = self::$_db_data->query($sql, self::$_connect_data);
            if ($result){
                $data['result'] = 200;
                $data['msg'] = '收藏成功！';
            }else{
                $data['result'] = 201;
                $data['msg'] = '收藏失败!';
            }
        }
        
        return json_encode($data);
    }
    
    // 取消收藏梦想
    public function cancelCollectedDream($postData = array()){
        $data = self::$data;
        
        $user_id =$postData['user_id'];
        $dream_id = $postData['dream_id'];
  
        $sql = "delete from dream_collections where user_id = $user_id and dream_id = $dream_id";
        file_put_contents("cancelCollection.txt", $sql);
        $result = self::$_db_data->query($sql, self::$_connect_data);
        
        file_put_contents("cancelCollection1.txt", $sql);
        
        if ($result){
            $data['result'] = 200;
            $data['msg'] = '取消收藏成功！';
        }else{
            $data['result'] = 201;
            $data['msg'] = '取消收藏失败!';
        }
        
        return json_encode($data);
    }
}
