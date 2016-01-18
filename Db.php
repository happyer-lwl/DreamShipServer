<?php
header("Content-Type:text/html;charset=UTF-8"); 
class DB{
    //运行模式
    static $DEBUG_MODE = true;

	//连接主机、数据库username、密码、数据库名称、编码形式
	private $_dbConfig = array(
        'host' => 'localhost:3306',
        'user' => 'root',
        'password' => 'root',
        'database' => 'dreamfly'
    );
    static private $_connectSource;
    static private $_instance;

	//构造函数
	function __construct(){
	}

    static  function GetInstance(){
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self();
            self::$_instance->connect();
        }
        return self::$_instance;
    }

    static function GetConnection(){
        return self::$_connectSource;
    }
    
    //数据库连接
    public function connect()
    {
        //单例模式
        if (!self::$_connectSource) {
            self::$_connectSource = mysql_connect($this->_dbConfig['host'], $this->_dbConfig['user'], $this->_dbConfig['password']);
           
            if (!self::$_connectSource) {
                die('mysql connect error' . mysql_error());
            }

            mysql_select_db($this->_dbConfig['database'], self::$_connectSource);
            mysql_query("SET NAMES UTF8", self::$_connectSource);
        }

        return self::$_connectSource;
	}

	function query($sql, $type = ''){
            if(!($query = mysql_query($sql)))
            {
    //            if (self::$DEBUG_MODE == true)
    //                self::$_instance->show("Say:", $sql);
            }
	return $query;
	}

	function show($message = '', $sql = ''){
		if(!$sql)
			echo $message;
		else{
			echo $message.'<br>'.$sql;
		}
	}

	function affected_rows(){
		return mysql_affected_rows();
	}

	function result($query, $row){
		return mysql_result($query, $row);
	}

	function num_rows($query){
		return @mysql_num_rows($query);
	}

	function num_fields($query){
		return mysql_num_fields($query);
	}

	function insert_id(){
		return mysql_insert_id();
	}

	function fetch_row($query){
		return mysql_fetch_row($query);
	}

	function version(){
		return mysql_get_server_info();
	}

	function close(){
		return mysql_close();
	}

	function fn_insert($table, $name, $value){
        self::$_instance->query("insert into $table ($name) value ($value)");
	}

	function fn_delete($table, $id, $value){
        self::$_instance->query("delete form $table where $id=$value");
		echo "id 为".$id." 的记录被成功删除!";
	}
        
        function is_value_exist($table, $name, $value){
            $sql = "select * from $table where $name=$value";
            
            $result = self::$_instance->query($sql);
            $nums = self::$_instance->num_rows($result);
            
            if($nums == 0)
                return false;
            else
                return true;
        }
        
        function is_double_value_exist($table, $condition){
            $sql = "select * from $table where $condition";
            
            file_put_contents("collection.txt", "hello");
            
            $result = self::$_instance->query($sql);
            $nums = self::$_instance->num_rows($result);
            
            if($nums == 0)
                return false;
            else
                return true;
        }
        
        function  value_for_id($table, $key, $keyCon, $valueCon){
            $retValue = "";
            $sql = "select $key from $table where $keyCon = $valueCon;";
            
            $result = self::$_instance->query($sql);
            while ($row = mysql_fetch_array($result)){
                $retValue = $row[0];
            }
            
            return $retValue;
        }
}

?>