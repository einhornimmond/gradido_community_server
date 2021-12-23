CREATE TABLE `login_node_servers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `port` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned DEFAULT 0,
  `server_type` int(10) unsigned NOT NULL DEFAULT 0,
  `last_live_sign` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
