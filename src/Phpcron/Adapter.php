<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 14-8-1
 * Time: 上午10:47
 */

namespace Phpcron;


abstract class Adapter {
    protected $options = array();
    public $tasks = array();


    public function __construct(array $options = array()){
        $this->options = $options + $this->options;
    }

    public function getNeedsExecTasks(){
        $this->tasks = array();

        $rows = $this->fetch();

        foreach($rows as $row){
            if($row->online_time==NULL || strtotime($row->online_time)<=time()){
                if($row->offline_time==NULL || strtotime($row->offline_time)>=time()){

                    $cron = \Cron\CronExpression::factory($row->exec_time);
                    if($cron->isDue()){
                        $this->tasks[$row->id] = $row;
                    }
                }
            }
        }
    }
    /**
     * 获取计划任务列表
     *
     * @return array
     */
    abstract public function fetch();

    abstract public function checkCurrentMinuteHasRun();
}
