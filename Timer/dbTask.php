<?php

    require_once('./../DB.php');

    ignore_user_abort();//关闭浏览器后，继续执行php代码
    set_time_limit(0);//程序执行时间无限制
    
    $sleep_time = 60;//多长时间执行一次
    $switch = include 'switch.php';
    
    $db_data = DB::GetInstance();
    $db_connect = $db_data->GetConnection();
    
    while($switch){
        $switch = include 'switch.php';

        $insertSql = "INSERT INTO user_dreams (text, pic_url, time, type, user_id) VALUES  ('很多人之所以一事无成，最大的毛病就是缺乏敢于决断的手段，总是左顾右盼、思前想后，从而错失成功的最佳时机。成大事者在看到事情的成功可能性到来时，敢于做出重大决断，因此取得先机。', '', '2015/12/22 10:11:22', '1', '1')";
        $result = $db_data->query($insertSql);
        $insertSql = "INSERT INTO user_dreams (text, pic_url, time, type, user_id) VALUES  ('人人都有弱点，不能成大事者总是固守自己的弱点，一生都不会发生重大转变；能成大事者总是善于从自己的弱点上开刀，去把自己变成一个能力超强的人。一个连自己的缺陷都不能纠正的人，只能是失败者！', '', '2015/12/22 10:11:22', '3', '2')";
        $result = $db_data->query($insertSql); 
        $insertSql = "INSERT INTO user_dreams (text, pic_url, time, type, user_id) VALUES  ('人生总要面临各种困境的挑战，甚至可以说困境就是“鬼门关”。一般人会在困境面前浑身发抖，而成大事者则能把困境变为成功的有力跳板。', '', '2015/12/22 10:11:22', '3', '3')";
        $result = $db_data->query($insertSql); 
        $insertSql = "INSERT INTO user_dreams (text, pic_url, time, type, user_id) VALUES  ('机遇就是人生最大的财富。有些人浪费机遇轻而易举，所以一个个有巨大潜力的机遇都悄然溜跑，成大事都是绝对不允许溜走，并且能纵身扑向机遇。', '', '2015/12/22 10:11:22', '2', '4')";
        $result = $db_data->query($insertSql); 
        $insertSql = "INSERT INTO user_dreams (text, pic_url, time, type, user_id) VALUES  ('一个能力极弱的人肯定难以打开人生局面，他必定是人生舞台上重量级选手的牺牲品；成大事者关于在自己要做的事情上，充分施展才智，一步一步地拓宽成功之路。', '', '2015/12/22 10:11:22', '1', '5')";
        $result = $db_data->query($insertSql); 
        $insertSql = "INSERT INTO user_dreams (text, pic_url, time, type, user_id) VALUES  ('心态消极的人，无论如何都挑不起生活和重担，因为他们无法直面一个个人生挫折，成大事者则关于高速心态，即使在毫无希望时，也能看到一线成功的亮光。', '', '2015/12/22 10:11:22', '2', '6')";
        $result = $db_data->query($insertSql);        
        
        $fp = fopen('test.txt','a+');
        fwrite($fp,"这是一个php博客：phpddt.com $switch \n");
        fclose($fp);
        
        sleep($sleep_time);
    }
?>