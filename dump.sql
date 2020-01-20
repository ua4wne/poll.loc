-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.7.25 - MySQL Community Server (GPL)
-- Операционная система:         Win64
-- HeidiSQL Версия:              10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица poll_db.eventlogs
DROP TABLE IF EXISTS `eventlogs`;
CREATE TABLE IF NOT EXISTS `eventlogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eventlogs_user_id_foreign` (`user_id`),
  CONSTRAINT `eventlogs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы poll_db.eventlogs: ~3 rows (приблизительно)
DELETE FROM `eventlogs`;
/*!40000 ALTER TABLE `eventlogs` DISABLE KEYS */;
INSERT INTO `eventlogs` (`id`, `type`, `user_id`, `text`, `ip`, `created_at`, `updated_at`) VALUES
	(1, 'logon', 1, 'Пользователь вошел в систему 2020-01-20 18:01:56', '127.0.0.1', '2020-01-20 18:01:56', '2020-01-20 18:01:56'),
	(2, 'logoff', 1, 'Пользователь вышел из системы 2020-01-20 18:11:49', NULL, '2020-01-20 18:11:49', '2020-01-20 18:11:49'),
	(3, 'logon', 1, 'Пользователь вошел в систему 2020-01-20 18:11:53', '127.0.0.1', '2020-01-20 18:11:53', '2020-01-20 18:11:53');
/*!40000 ALTER TABLE `eventlogs` ENABLE KEYS */;

-- Дамп структуры для таблица poll_db.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы poll_db.migrations: ~2 rows (приблизительно)
DELETE FROM `migrations`;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_resets_table', 1),
	(3, '2020_01_20_164513_create_eventlogs_table', 1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Дамп структуры для таблица poll_db.password_resets
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы poll_db.password_resets: ~0 rows (приблизительно)
DELETE FROM `password_resets`;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;

-- Дамп структуры для таблица poll_db.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `login` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sex` enum('male,female') COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login_unique` (`login`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы poll_db.users: ~0 rows (приблизительно)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `active`, `login`, `sex`, `image`, `auth_code`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 1, 'admin', 'male,female', NULL, NULL, 'Administrator', 'rogatnev@m-strana.ru', NULL, '$2y$10$LnedN6zQ3WcWCPtu71AdpuRW8ZsZBR4CsXqAHPIP7WakiTZoZh3YS', NULL, '2020-01-20 17:34:36', '2020-01-20 17:34:36');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
