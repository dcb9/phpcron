phpcron
=======

PHP版计划任务，本程序是Croon的扩展功能延伸。

## 功能
- 在两台机器上运行该程序来防止一台机器宕机之后产生的严重后果
- 可以规定该计划任务的上线时间和下线时间
- 计划任务的时间里面可以指定年
- 将执行结果、标准正确输出、标准错误输出统一到数据库，方便检索

## 依赖

- PHP 5.4.0+
- ext-pcntl
- ext-posix
- [Composer](http://getcomposer.org)

库依赖（使用`composer install`自动安装）
- [croon/croon](https://github.com/hfcorriez/croon)
- [mtdowling/cron-expression](https://github.com/mtdowling/cron-expression)

## 安装
``` 请保证两台服务器上所有的代码一致，包括配置文件，最好采用共享存储 ```
代码克隆及依赖的安装
```
git clone https://github.com/bobchengbin/phpcron.git
cd phpcron
composer install
```
配置相应的数据表
```
$ cd src   // 进入到src目录

## 创建一个phpcron库，并创建相应的存储表
mysql> CREATE DATABASE `phpcron`;
mysql> source phpcron.sql;
```
修改配置文件
```
$ vim etc/config.php
修改 host dbname username 及 password
```

## 运行
主机      角色
server1   主
server2   备主（主要是该执行的时候不执行，它就顶上去）
```
server1 $ nohup bin/croon.php &
server2 $ nohup bin/croon.php --backend &
```

## 添加和修改计划任务
计划任务列表信息全部存储在数据库，所以添加或修改计划任务直接用程序进程修改即可。

## 日志
日志见 ```crontab_log``` 表