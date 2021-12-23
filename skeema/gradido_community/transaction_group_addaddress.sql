CREATE TABLE `transaction_group_addaddress` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` int(10) unsigned NOT NULL,
  `address_type_id` int(10) unsigned NOT NULL,
  `remove_from_group` tinyint(1) DEFAULT 0,
  `public_key` binary(32) NOT NULL,
  `state_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
