<?php

require_once ('./Db.php');
header("Content-Type:text/html;charset=UTF-8"); 
class Users{

    public static $data = array(
        'result' => '0',
        'msg' => '',
        'data' => ''
    );

    static private $_db_login;
    static private $_connect_login;

    public function __construct(){
        if(!self::$_db_login){
            self::$_db_login = DB::GetInstance();
            self::$_connect_login = self::$_db_login->GetConnection();
        }
    }

    //判断具体是哪个接口
    public function chooseType($postData = array()){
        $type = $postData['api_type'];

        switch($type){
            case "loginIn":
                return self::loginInDeal($postData);
                break;
            case "regUser":
                return self::regUser($postData);
                break;
            case "saveImage":
                return self::saveImage($postData);
                break;
            case "modUser":
                return self::modUser($postData);
                break;
            case "getFocusedUser":
                return self::getFocusedUser($postData);
                break;
            case "getAllUsers":
                return self::getAllUsers($postData);
                break;
            case "createFocus":
                return self::createFocus($postData);
                break;
            case "getFocusStatus":
                return self::getFocusStatus($postData);
                break;
            case "cancelFocus":
                return self::cancelFocus($postData);
                break;
            default:
                break;
        }
    }


    //已注册用户校验
    public function loginInDeal($postData = array()){
        $data = self::$data;

        $user_pnone = $postData['phone'];
        $user_pwd  = $postData['pwd'];

        $sql = "select * from users where phone='$user_pnone' and pwd = '$user_pwd'";
        $result = self::$_db_login->query($sql, self::$_connect_login);
        $countRight = self::$_db_login->num_rows($result);

        $file = fopen("./Log/userLogin.txt", "w") or die("Unable to open file!");
        fwrite($file, $sql);

        $sqlName = "select * from users where phone='$user_pnone'";
        $resultName = self::$_db_login->query($sqlName, self::$_connect_login);
        $countName = self::$_db_login->num_rows($resultName);

        if($countRight == 0 && $countName == 0) {
            $data['result'] = 201;
            $data['msg'] = 'User Not Exist！';
            return json_encode($data);
        }
        else if($countRight == 0 && $countName != 0)
        {
            $data['result'] = 202;
            $data['msg'] = 'Password Error!';
            return json_encode($data);
        }
        else {
            $userInfo = array();
            while($row = mysql_fetch_array($resultName)){
                $userInfo['id'] = $row[0];
                $userInfo['mail'] = $row[3];
                $userInfo['name'] = $row[4];
                $userInfo['image'] = $row[5];
                $userInfo['image'] = $row[5];
                $userInfo['sex'] = $row[6];
                $userInfo['words'] = $row[7];
                $userInfo['addr'] = $row[8];
            }
            $data['data'] = $userInfo;
            $data['result'] = 200;
            $data['msg'] = 'User Exist,Successful!';
            return json_encode($data);
        }
    }

    //用户信息注册
    public function regUser($postData = array()){
        $data = self::$data;

        $user_name = $postData['name'];
        $user_pwd = $postData['pwd'];
        $user_mail = $postData['mail'];
        $user_image = $postData['image'];
        $user_phone = $postData['phone'];

        $sql = "select * from users where phone='$user_phone'";
        $result = self::$_db_login->query($sql, self::$_connect_login);
        $count = self::$_db_login->num_rows($result);

        if($count != 0){
            $data['result'] = 201;
            $data['msg'] = '用户名已被占用，请重新输入！';
            return json_encode($data);
        }

        $sql = "insert into users (name, pwd, mail, image, phone) values ('$user_name', '$user_pwd', '$user_mail', '$user_image', '$user_phone')";
        $file = fopen("./Log/userReg.txt", "w") or die("Unable to open file!");
        fwrite($file, $sql);
        $result = self::$_db_login->query($sql, self::$_connect_login);
        if ($result){
            $user_id = self::$_db_login->value_for_id("users", "id", "phone", $user_phone);
        
            $sql = "insert into dream_points (user_id, total_points, dream_point, comment_point) values ('$user_id', 0, 0, 0)";
            $result = self::$_db_login->query($sql, self::$_connect_login);
            if ($result){
                $data['result'] = 200;
                $data['msg'] = '用户创建成功！';
            }else{
                $data['result'] = 201;
                $data['msg'] = '用户创建失败！';
            }
        }else{
            $data['result'] = 201;
            $data['msg'] = '用户创建失败！';
        }
        return json_encode($data);
    }

    public function saveImage($postData = array()){
        $data = self::$data;
        $user_phone = $postData["phone"];
        $imageData = $postData["photo"];
        
        $user_exist = self::$_db_login->is_value_exist("users", "phone", $user_name);
        
        if($user_exist){
            $result = self::$_db_login->query("select * from users where phone=$user_phone");
            $userInfo = array();
            while($row = mysql_fetch_array($result)){
                $userInfo['mail'] = $row[3];
                $userInfo['name'] = $row[4];
                $userInfo['image'] = $row[5];
                $userInfo['sexy'] = $row[6];
            }
            $data['data'] = $userInfo;
            $data['result'] = 200;
            $data['msg'] = '图片保存成功!';
        }
        else{
            //echo $image;
            $data['result'] = 201;
            $data['msg'] = '图片保存失败!';            
        }
        
        return json_encode($data);
    }
    
    public function modUser($postData = array()){
        $data = self::$data;

        $user_pnone = $postData['phone'];
        $mod_key = $postData['mod_key'];
        $mod_value = $postData['mod_value'];

        $sqlName = "update users set $mod_key='$mod_value' where phone='$user_pnone'";

        $file = fopen("./Log/modUser.txt", "w");
        fwrite($file, $sqlName);

        self::$_db_login->query($sqlName);

        if(0) {
            $data['result'] = 201;
            $data['msg'] = 'Modify failed！';
            return json_encode($data);
        }
        else {
            $data['result'] = 200;
            $data['msg'] = 'Modify Exist,Successful!';
            return json_encode($data);
        }
    }
    
    //被关注的用户
    public function getFocusedUser($postData = array()){
        $data = self::$data;

        $user_id = $postData['user_id'];

        $sql = "select * from users where id in (select focused_user_id from user_relation where user_id = $user_id)";
        $result = self::$_db_login->query($sql, self::$_connect_login);
        $count = self::$_db_login->num_rows($result);

        if($count == 0){
            $data['result'] = 201;
            $data['msg'] = '用户名已被占用，请重新输入！';
            return json_encode($data);
        }
        else {
            $info = array();
            while($row = mysql_fetch_array($result)){
                $userInfo = array();
                $userInfo['user_id'] = $row[0];
                $userInfo['name'] = $row[1];
                $userInfo['userMail'] = $row[3];
                $userInfo['userRealName'] = $row[4];
                $userInfo['image'] = $row[5];
                $userInfo['sex'] = $row[6];
                $userInfo['userWords'] = $row[7];
                
                array_push($info, $userInfo);
            }
            $data['data'] = $info;
            $data['result'] = 200;
            $data['msg'] = 'User Exist,Successful!';
            return json_encode($data);
            
            
        }
    }
    
    //所有的用户
    public function getAllUsers($postData = array()){
        $data = self::$data;

        $sql = "select * from users";
        $result = self::$_db_login->query($sql, self::$_connect_login);
        $count = self::$_db_login->num_rows($result);

        if($count == 0){
            $data['result'] = 201;
            $data['msg'] = '当前没有任何用户！';
            return json_encode($data);
        }
        else {
            $info = array();
            while($row = mysql_fetch_array($result)){
                $userInfo = array();
                $userInfo['user_id'] = $row[0];
                $userInfo['name'] = $row[1];
                $userInfo['userMail'] = $row[3];
                $userInfo['userRealName'] = $row[4];
                $userInfo['image'] = $row[5];
                $userInfo['sex'] = $row[6];
                $userInfo['userWords'] = $row[7];
                $userInfo['userAddr'] = $row[8];
                array_push($info, $userInfo);
            }
            $data['data'] = $info;
            $data['result'] = 200;
            $data['msg'] = 'Get UsersInfo, Successful!';
            return json_encode($data);
        }
    }
    
    // 添加关注
    public function createFocus($postData = array()){
        $data = self::$data;
        
        $user_id = $postData['user_id_from'];
        $focused_user_id = $postData['user_id_to'];
        
        $sql_condition = "user_id = $user_id and focused_user_id = $focused_user_id";
        $IsExisted = self::$_db_login->is_double_value_exist("user_relation", $sql_condition);
        if ($IsExisted){
            $data['result'] = 200;
            $data['msg'] = '用户已关注！';
        }else{
            $sql = "INSERT INTO user_relation (user_id, focused_user_id) VALUES ($user_id, $focused_user_id)";
        
            file_put_contents("\Log\usersCreateFocus.txt", $sql);

            $result = self::$_db_login->query($sql, self::$_connect_login);
            if ($result){
                $data['result'] = 200;
                $data['msg'] = '用户关注成功！';
            }else{
                $data['result'] = 201;
                $data['msg'] = '用户关注失败!';
            }
        }
        
        return json_encode($data);
    }
    
    // 添加关注
    public function getFocusStatus($postData = array()){
        $data = self::$data;
        
        $user_id = $postData['user_id_from'];
        $focused_user_id = $postData['user_id_to'];
        
        $sql_condition = "user_id = $user_id and focused_user_id = $focused_user_id";
        $IsExisted = self::$_db_login->is_double_value_exist("user_relation", $sql_condition);
        if ($IsExisted){
            $data['result'] = 200;
            $data['msg'] = '用户已关注！';
        }else{
            $data['result'] = 201;
            $data['msg'] = '用户创建成功！';
        }
        
        return json_encode($data);
    }
    
    // 添加关注
    public function cancelFocus($postData = array()){
        $data = self::$data;
        
        $user_id = $postData['user_id_from'];
        $focused_user_id = $postData['user_id_to'];
        
        $sql = "delete from user_relation where user_id = $user_id and focused_user_id = $focused_user_id";
        $result = self::$_db_login->query($sql, self::$_connect_login);
        
        if ($result){
            $data['result'] = 200;
            $data['msg'] = '用户已取消关注!';
        }else{
            $data['result'] = 201;
            $data['msg'] = '用户取消关注失败!';
        }
        
        return json_encode($data);
    }
}
?>