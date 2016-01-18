<?php
require_once ('./Db.php');
header("Content-Type:text/html;charset=UTF-8"); 
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Comments{
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
            case "getComments":
                return self::getComments($postData);
            case "support":
                return self::support($postData);
            case "unsupport":
                return self::unsupport($postData);
            case "commit":
                return self::commitComments($postData);
                break;
            default:
                break;
        }
    }
    
        //获取评论   http://192.168.1.101/?api_uid=getDreams&api_type=getComments&dream_id=3
    public function getComments($postData = array()){
        $data = self::$data;
        $dream_id=$postData['dream_id'];
        
        $sql = "select * from dream_comments as dc left join user_dreams as ud on ud.id = dc.dream_id left join users as u on u.id = dc.comment_user_id where dc.text <> '' and dc.dream_id=$dream_id";
        $result = self::$_db_data->query($sql, self::$_connect_data);
        $countRight = self::$_db_data->num_rows($result);
   
//        $file = fopen("comments.txt", "w") or die("Unable to open file!");
//        fwrite($file, $sql);
        
        if($countRight == 0) {
            $data['result'] = 201;
            $data['msg'] = 'Data Not Exist！';
            return json_encode($data);
        }
        else {
            $info = array();
            while($row = mysql_fetch_array($result)){
                $comments = array();
                $comments['idStr'] = $row[0];
                $comments['text'] = $row[1];
                $comments['time'] = $row[2];
                
                $user = array();
                $user['user_id'] = $row[14];
                $user['name'] = $row[18];
                $image = $row[19];
                if($image != "")
                    $user['image'] = $row[19];
                else
                    $user['image'] = "";
                $user['mail'] = $row[17];
                $user['phone'] = $row[15];
                $user['sex'] = $row[20];
                $user['words'] = $row[21];
                $user['addr'] = $row[22];
                $comments['user'] = $user;
                
                array_push($info, $comments);
            }

            $data['result'] = 200;
            $data['msg'] = 'User Exist,Successful!';
            $data['data'] = $info;
            return json_encode($data);
        }
    } 
    
    //获取数据   http://192.168.1.101/?api_uid=getDreams&api_type=getData
    public function support($postData = array()){
        $data = self::$data;

        $comment_user_id = $postData['user_id'];
        $dream_id = $postData['dream_id'];
        $support_time = $postData['time'];
       
        $sql_support = "select support from dream_support where comment_user_id = $comment_user_id and dream_id = $dream_id";
        $result_support = self::$_db_data->query($sql_support, self::$_connect_data);
        
        $support = 0;
        if ($row = mysql_fetch_array($result_support)){
            $support = $row[0];
        }else{
            $support = 0;
        }
        
        $sql = "";
        $sqlDream = "";
        switch ($support) {
            case 0:
                $sql = "insert dream_support (support, time, dream_id, comment_user_id) values (1, '$support_time', $dream_id, $comment_user_id)";
                $sqlDream = "update user_dreams set support_count = support_count + 1 where id = $dream_id";
                break;
            case 1:
                break;
            case 2:
                $sql = "update dream_support set support=1 where comment_user_id = $comment_user_id and dream_id = $dream_id";
                $sqlDream = "update user_dreams set support_count = support_count + 1, unsupport_count = unsupport_count - 1 where id = $dream_id";
                break;
            default:
                break;
        }
        
        self::$_db_data->query($sql);
        self::$_db_data->query($sqlDream);
        
        $sqlQuery = "select * from user_dreams as ud left join users as u on ud.user_id = u.id left join dream_types as dt on dt.type_id = ud.type where ud.id = $dream_id order by ud.id desc";
        $result = self::$_db_data->query($sqlQuery);
        $countRight = self::$_db_data->num_rows($result);
//        
//        $file = fopen("detail.txt", "wr");
//        fwrite($file, $sql_support);
        
        if ($countRight  != 0){
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
                $dream['type'] = $row[18];
                
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
        }else{
            $data['result'] = 201;
            $data['msg'] = "Data Error,Successful!";
            $data['data'] = " ";
            return json_encode($data);
        }
    }

    public function unsupport($postData = array()){
        $data = self::$data;

        $comment_user_id = $postData['user_id'];
        $dream_id = $postData['dream_id'];
        $support_time = $postData['time'];
       
        $sql_support = "select support from dream_support where comment_user_id = $comment_user_id and dream_id = $dream_id";
        $result_support = self::$_db_data->query($sql_support, self::$_connect_data);
        
        $support = 0;
        if ($row = mysql_fetch_array($result_support)){
            $support = $row[0];
        }else{
            $support = 0;
        }
        
        $sql = "";
        $sqlDream = "";
        switch ($support) {
            case 0:
                $sql = "insert dream_support (support, time, dream_id, comment_user_id) values (2, '$support_time', $dream_id, $comment_user_id)";
                $sqlDream = "update user_dreams set unsupport_count = unsupport_count + 1 where id = $dream_id";
                break;
            case 1:
                $sql = "update dream_support set support=2 where comment_user_id = $comment_user_id and dream_id = $dream_id";
                $sqlDream = "update user_dreams set support_count = support_count - 1, unsupport_count = unsupport_count + 1 where id = $dream_id";
                break;
            case 2:
                break;
            default:
                break;
        }
            
        self::$_db_data->query($sql);
        self::$_db_data->query($sqlDream);
        
        $sqlQuery = "select * from user_dreams as ud left join users as u on ud.user_id = u.id left join dream_types as dt on dt.type_id = ud.type where ud.id = $dream_id order by ud.id desc";
        $result = self::$_db_data->query($sqlQuery, self::$_connect_data);
        $countRight = self::$_db_data->num_rows($result);
        
//        file_put_contents("detail.txt", $sqlQuery);
        
        if ($countRight  != 0){
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
                $dream['type'] = $row[18];
                
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
        }else{
            $data['result'] = 201;
            $data['msg'] = "Data Error,Successful!";
            $data['data'] = " ";
            return json_encode($data);
        }
    }
    
    //获取评论   http://192.168.1.101/?api_uid=getDreams&api_type=getComments&dream_id=3
    public function commitComments($postData = array()){
        $data = self::$data;

        $comment_user_id = $postData['user_id'];
        $dream_id        = $postData['dream_id'];
        $time            = $postData['time'];
        $text            = $postData['text'];
        

        $sql = "insert dream_comments (text, time, dream_id, comment_user_id) values ('$text', '$time', $dream_id, $comment_user_id)";
        self::$_db_data->query($sql);
        
        $sqlDream = "update user_dreams set comment_count = comment_count + 1 where id = $dream_id";
        self::$_db_data->query($sqlDream);
        
        $file = fopen("comments.txt", "wr");
        fwrite($file, $sqlDream);
        
        if (1){
            $data['result'] = 200;
            $data['msg'] = 'User Exist,Successful!';
            $data['data'] = $info;
            return json_encode($data);
        }
    } 
}
