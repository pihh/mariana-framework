<?php namespace Mariana\Framework;
/**
 * Created by PhpStorm.
 * User: filipe_2
 * Date: 12/18/2015
 * Time: 10:23 PM
 */

class Config{

    protected static $settings = array();

    public static function get($key){
        return isset(self::$settings[$key]) ?
            self::$settings[$key] :
            null;
    }

    public static function set($key,$value){
        self::$settings[$key] = $value;
    }

}