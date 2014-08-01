<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 14-8-1
 * Time: 上午12:02
 */

namespace Phpcron;


class Utils{
    static $_hostname=null;
    /**
     * 每一次运行时的唯一编号。
     *
     * @var null
     */
    static $_current_token = null;

    public static function log(array $message=array()){
        global $config;
        $logger = new \Phpcron\Adapter\Pdo($config);
        $logger->log($message);
        $logger=null;
    }

    public static function hostname(){
        if(is_null(self::$_hostname)){
            self::$_hostname = exec('hostname');
            if(TRIAL){
                self::$_hostname .= '_'.self::currentToken();
            }
        }
        return self::$_hostname;
    }

    public static function currentToken(){
        if(is_null(self::$_current_token)){
            self::$_current_token = time().rand(111, 999);
        }
        return self::$_current_token;
    }

    public static function setRole($argv){

        $role = Phpcron::ROLE_MASTER;
        if(isset($argv[1])){
            $argv1 = $argv[1];
            if($argv1 =='--backend')
                $role = Phpcron::ROLE_BACKEND;
        }

        define('ROLE', $role);
    }
}