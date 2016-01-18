<?php
    require_once ('./Db.php');
    
    static $data = array(
        'result' => '0',
        'msg' => '',
        'data' => ''
    );

    function saveDicrections($postData)
    {
        $textTitle = $postData["textTitle"];
        $textContent = $postData["textContent"];

        $db_login = DB::GetInstance();
        $connect_login = $db_login->GetConnection();

        $user_exist = $db_login->is_value_exist("dream_directions", "title", $textTitle);
        
        if($user_exist == FALSE){
            
            $sql = "INSERT INTO dream_directions (title, text) VALUES ('$textTitle', '$textContent')";
            $db_login->query($sql);
            
            echo $sql;
        }else{
            echo "Existed";
        }
    }
    
$params = array();
$params["textTitle"]  = $_POST["textTitle"];
$params["textContent"]  = $_POST["textContent"];

saveDicrections($params);
