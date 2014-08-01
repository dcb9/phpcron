#!/usr/bin/env php
<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$config  = require dirname(__DIR__).'/etc/config.php';
Phpcron\Utils::setRole($argv);

$phpcron = new \Phpcron\Phpcron($config);

$phpcron->on('run', function () use($phpcron) {
    Phpcron\Utils::log(array(
        'cron_name'=>$phpcron->role[ROLE].'计划任务服务启动',
        'crontab_id'=>-1,
    ));
});

$phpcron->on('getNeedsExecTasks', function () use ($phpcron){
    Phpcron\Utils::log(array(
        'cron_name'=>$phpcron->role[ROLE].'获取当前需要执行的计划任务',
        'crontab_id'=>-2,
    ));
});

$phpcron->on('execute', function ($task) use($phpcron) {

    Phpcron\Utils::log(array(
        'cron_name'=>$phpcron->role[ROLE].$task->cron_name,
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