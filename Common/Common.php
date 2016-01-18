<?php
/**
 * Created by PhpStorm.
 * User: hdc-lwl
 * Date: 2015/8/31
 * Time: 10:45
 */

class APP_FILE{
        /**
     *  生成指定长度的随机数字
     * <a href="http://my.oschina.net/arthor" target="_blank" rel="nofollow">@author</a>   yangyingjie
     * @param $number
     * <a href="http://my.oschina.net/u/556800" target="_blank" rel="nofollow">@return</a>  string
     */
    function createRandNumberBySize($number)
    {
        $number = (int)$number;
        if ($number === 0) {
            return '';
        } else {
            $rankNumberString = "";
            for ($i = 0; $i < $number + 1; $i++) {
                if ($i !== 0 && $i % 2 === 0) {
                    $rankNumberString .= mt_rand(11, 99);
                }
            }

            if ($number % 2 === 0) {
                return $rankNumberString;
            } else {
                return $rankNumberString . mt_rand(1, 9);
            }

        }
    }
    
    static private $_instance;
    private $fileHanddler;

    function __construct(){
    }

    public function GetFileHanddler(){
        if(!self::$fileHanddler){

        }
    }

    static function GetInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
    }

    public function open($fileHanddler, $filePath){

    }
}