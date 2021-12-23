CREATE TABLE `login_elopage_buys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `elopage_user_id` int(11) DEFAULT NULL,
  `affiliate_program_id` int(11) NOT NULL,
  `publisher_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_price` int(11) NOT NULL,
  `payer_email` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `publisher_email` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `payed` tinyint(4) NOT NULL,
  `success_date` datetime NOT NULL,
  `event` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
