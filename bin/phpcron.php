#!/usr/bin/env php
<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$config  = require dirname(__DIR__).'/etc/config.php';


$phpcron = new \Phpcron\Phpcron($config);

$phpcron->on('run', function () {
    Phpcron\Utils::log(array(
        'cron_name'=>'计划任务服务启动',
        'crontab_id'=>-1,
    ));
});

$phpcron->on('getNeedsExecTasks', function () {
    Phpcron\Utils::log(array(
        'cron_name'=>'获取当前需要执行的计划任务',
        'crontab_id'=>-2,
    ));
});

$phpcron->on('execute', function ($task) {

    Phpcron\Utils::log(array(
        'cron_name'=>$task->cron_name,
        'crontab_id'=> $task->id,
        'status'=>'-1',
        'stdout'=>'开始执行'
    ));
});

$phpcron->on('executed', function ($task, $output) {
    Phpcron\Utils::log(array(
        'cron_name'=>$task->cron_name,
        'crontab_id'=>$task->id,
        'status'=>$output[0],
        'stdout'=>$output[1],
        'stderr'=>$output[2],
    ));
});


$phpcron->run();