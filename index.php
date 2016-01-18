<?php
    header("Content-Type:text/html;charset=UTF-8");  
    
    require_once ('./Users.php');
    require_once ('./Dreams.php');
    require_once ('./Comments.php');
    
    $params = array();
    if ($_SERVER['REQUEST_METHOD'] == 'POST')  
    {
        foreach   ($_POST as $key=>$value)
        {
            $params[$key] = $value;
        }
    }else{
        foreach   ($_GET as $key=>$value)  
        {
            $params[$key] = $value;
        }
    }

    if(empty($params['api_uid'])){
        $output = array('data' => null, 'msg' => "没有接口名！", 'result' => 201);
        exit(json_encode($output));
    }
    
    //file_put_contents("dreams.txt", $params);
    
    switch($params['api_uid']){
        case "users":                         //登录校验
            $usersApi = new Users();
            $ret = $usersApi->chooseType($params);
            exit($ret);
            break;
        case "dreams":
            $dreamApi = new Dreams();
            $ret = $dreamApi->chooseType($params);
            exit($ret);
            break;
        case "comments";
            $commentApi = new Comments();
            $ret = $commentApi->chooseType($params);
            exit($ret);
        default:
            break;
    }
?>