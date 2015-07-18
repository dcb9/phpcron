CREATE TABLE `crontab` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `command` varchar(255) NOT NULL COMMENT '需要执行的命令',
  `exec_time` varchar(64) NOT NULL COMMENT '[分] [时] [日] [月] [年] 执行的计划任务周期',
  `online_time` datetime DEFAULT NULL COMMENT '该计划任务从何时开始允许被执行',
  `offline_time` datetime DEFAULT NULL COMMENT '该计划任务截止何时不再执行',
  `cron_name` varchar(255) NOT NULL COMMENT '给该计划任务取个名字吧',
  `note` text COMMENT '备注，关于计划任务更多的说明信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='计划任务列表';

CREATE TABLE `crontab_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crontab_id` int(11) NOT NULL,
  `hostname` varchar(255) DEFAULT NULL COMMENT '执行该计划任务的主机名',
  `cron_name` varchar(255) DEFAULT NULL COMMENT '计划任务执行的命令',
  `status` varchar(255) DEFAULT NULL COMMENT '状态  执行时的退出状态',
  `stdout` text COMMENT '执行后的正常输出',
  `stderr` varchar(255) DEFAULT NULL COMMENT '执行完成后的错误输出',
  `create_time` datetime NOT NULL COMMENT '插入的时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='计划任务执行完之后的输出记录表';


INSERT INTO `crontab` (`id`, `command`, `exec_time`, `online_time`, `offline_time`, `cron_name`, `note`) VALUES
(1, 'ls /', '* * * * * *', NULL, NULL, '列出根目录下的所有文件', '测试。。。');
