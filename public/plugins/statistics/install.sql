CREATE TABLE IF NOT EXISTS `mk_app_statistics_day_count` (
  `day` date NOT NULL COMMENT '统计当日',
  `ip` int(11) NOT NULL COMMENT '1ip/1pv = 100%跳出率',
  `pv` int(11) NOT NULL COMMENT '访客深度(页)',
  `uv` int(11) NOT NULL COMMENT '独立访客',
  `duration` int(11) NOT NULL COMMENT '在线时长（秒）'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `mk_app_statistics_day_ip` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'pv',
  `ip` varchar(16) NOT NULL COMMENT 'ip',
  `user_id` int(11) NOT NULL COMMENT 'uv',
  `broswer` varchar(255) NOT NULL COMMENT '浏览器内核',
  `os` varchar(255) NOT NULL COMMENT '操作系统',
  `mobile` tinyint(1) NOT NULL COMMENT '1为手机',
  `newuser` tinyint(1) NOT NULL COMMENT '1为新用户(在今天之前未出现过的ip)',
  `url` varchar(255) NOT NULL COMMENT '受访页面',
  `title` varchar(255) NOT NULL COMMENT '受访页面标题',
  `referrer` varchar(255) NOT NULL COMMENT '网站来源',
  `keyword` varchar(255) NOT NULL COMMENT '最新搜索词',
  `keyword_from` varchar(255) NOT NULL COMMENT '搜索词来源（搜索引擎）',
  `duration` int(11) NOT NULL COMMENT '访问时长',
  `country` varchar(255) NOT NULL COMMENT '国家',
  `province` varchar(255) NOT NULL COMMENT '省份',
  `city` varchar(255) NOT NULL COMMENT '城市',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;