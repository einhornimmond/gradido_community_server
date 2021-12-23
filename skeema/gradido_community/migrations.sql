CREATE TABLE `migrations` (
  `version` int(11) DEFAULT NULL,
  `fileName` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
