CREATE TABLE `state_user_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state_user_id` int(10) unsigned NOT NULL,
  `transaction_id` int(10) unsigned NOT NULL,
  `transaction_type_id` int(10) unsigned NOT NULL,
  `balance` bigint(20) DEFAULT 0,
  `balance_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
