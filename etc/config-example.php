<?php

/*
 *
 */


define('TRIAL', true);  // 试用，试用的时候允许在同一机器上运行多次本计划任务程序，来测试宕机的情况。

return array(
    'engine'=>'pdo',
    'dsn'      => 'mysql:host=localhost;dbname=phpcron',
    'username' => 'root',
    'password' => '',
    'options'  => array(),
    'table'    => 'crontab',
    'log_table' => 'crontab_log'
);
