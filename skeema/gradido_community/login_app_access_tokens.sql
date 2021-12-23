CREATE TABLE `login_app_access_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `access_code` bigint(20) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_code` (`access_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
