CREATE TABLE `login_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(190) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `host` varchar(255) DEFAULT '/',
  `home` varchar(255) DEFAULT '/',
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
