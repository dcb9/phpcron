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

     /**
     *
     * Exec the command and return code
     *
     * @param string $cmd
     * @param string $stdout
     * @param string $stderr
     * @param int    $timeout
     * @return int|null
     */
    public static function exec($cmd, &$stdout, &$stderr, $timeout = 3600)
    {
        if ($timeout <= 0) $timeout = 3600;
        $descriptors = array
        (
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );
        $stdout = $stderr = $status = null;
        $process = proc_open($cmd, $descriptors, $pipes);
        $time_end = time() + $timeout;
        if (is_resource($process)) {
            do {
                $time_left = $time_end - time();
                $read = array($pipes[1]);
                stream_select($read, $null, $null, $time_left, NULL);
                $stdout .= fread($pipes[1], 2048);
            } while (!feof($pipes[1]) && $time_left > 0);
            fclose($pipes[1]);
            if ($time_left <= 0) {
                proc_terminate($process);
                $stderr = 'process terminated for timeout.';
                return -1;
            }
            while (!feof($pipes[2])) {
                $stderr .= fread($pipes[2], 2048);
            }
            fclose($pipes[2]);
            $status = proc_close($process);
        }
        return $status;
    }
}
