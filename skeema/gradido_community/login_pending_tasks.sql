CREATE TABLE `login_pending_tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT 0,
  `request` varbinary(2048) NOT NULL,
  `created` datetime NOT NULL,
  `finished` datetime DEFAULT '2000-01-01 00:00:00',
  `result_json` text DEFAULT NULL,
  `param_json` text DEFAULT NULL,
  `task_type_id` int(10) unsigned NOT NULL,
  `child_pending_task_id` int(10) unsigned DEFAULT 0,
  `parent_pending_task_id` int(10) unsigned DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
