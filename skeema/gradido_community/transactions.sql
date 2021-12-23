CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nr` bigint(20) unsigned NOT NULL,
  `state_group_id` int(10) unsigned DEFAULT NULL,
  `transaction_type_id` int(10) unsigned NOT NULL,
  `tx_hash` binary(48) DEFAULT NULL,
  `memo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `received` timestamp NOT NULL DEFAULT current_timestamp(),
  `blockchain_type_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `transaction_state_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nr` (`nr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
