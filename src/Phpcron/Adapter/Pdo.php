<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 14-8-1
 * Time: 上午10:55
 */

namespace Phpcron\Adapter;
use Phpcron\Adapter;
use Phpcron\Utils;

class Pdo extends Adapter {


    public function fetch(){

        $sql=sprintf("/* file:%s line: %d */SELECT `id`, `command`, `exec_time`"
            .", `online_time`, `offline_time`, `cron_name`, `note` FROM %s", __FILE__, __LINE__, $this->options['table']);
        $query = $this->pdo->query($sql);
        return $query->fetchAll(\PDO::FETCH_OBJ);

    }

    public function __construct(array $options = array()){
        parent::__construct($options);
        $this->connect();
    }

    protected function connect()
    {
        $this->pdo = new \PDO(
            $this->options['dsn'],
            $this->options['username'],
            $this->options['password'],
            $this->options['options']
        );
        $this->pdo->query("SET NAMES utf8");
    }

    public function log(array $message = array()){

        $fields = array(
            'crontab_id'=>'0',
            'hostname'=> Utils::hostname(),
            'cron_name'=>'',
            'status'=>'0',
            'stdout'=>'',
            'stderr'=>'',
            'create_time'=>date('Y-m-d H:i:s'),
        );
        $message = $message + $fields;

        $keys = implode('`, `', array_keys($message));
        $values = trim(str_pad("", 3*count($message), ", ?"), ' ,');

        try {
            if(FALSE===$this->pdo->query('SELECT 1')){
                self::connect();
            }
        } catch (PDOException $e) {
            self::connect();
        }

        $sql = sprintf("/* file:%s line: %d */INSERT INTO `%s` (`%s`) values (%s)"
            , __FILE__, __LINE__, $this->options['log_table'], $keys, $values);

        $sth = $this->pdo->prepare($sql);

        $sth->execute(array_values($message));
    }


    public function __destruct()
    {
        $this->pdo = null;
    }
    protected function prepareSql(){

    }

    public function checkCurrentMinuteHasRun(){
        $sql = sprintf("/* file:%s line: %d */SELECT 1 FROM `%s` WHERE `crontab_id`='%s' AND `create_time`>='%s'"
            , __FILE__, __LINE__, $this->options['log_table'], '-2', date('Y-m-d H:i:00'));
        $query = $this->pdo->query($sql);
        return (boolean)$query->fetch();
    }
}
