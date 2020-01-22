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

-- Дамп структуры для таблица poll_db.actions
DROP TABLE IF EXISTS `actions`;
CREATE TABLE IF NOT EXISTS `actions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `actions_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы poll_db.actions: ~0 rows (приблизительно)
DELETE FROM `actions`;
/*!40000 ALTER TABLE `actions` DISABLE KEYS */;
INSERT INTO `actions` (`id`, `code`, `name`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'Полный доступ', '2020-01-22 18:15:49', '2020-01-22 18:15:49');
/*!40000 ALTER TABLE `actions` ENABLE KEYS */;

-- Дамп структуры для таблица poll_db.action_role
DROP TABLE IF EXISTS `action_role`;
CREATE TABLE IF NOT EXISTS `action_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `action_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `action_role_action_id_foreign` (`action_id`),
  KEY `action_role_role_id_foreign` (`role_id`),
  CONSTRAINT `action_role_action_id_foreign` FOREIGN KEY (`action_id`) REFERENCES `actions` (`id`),
  CONSTRAINT `action_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы poll_db.action_role: ~0 rows (приблизительно)
DELETE FROM `action_role`;
/*!40000 ALTER TABLE `action_role` DISABLE KEYS */;
INSERT INTO `action_role` (`id`, `action_id`, `role_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, '2020-01-22 18:17:06', '2020-01-22 21:17:06');
/*!40000 ALTER TABLE `action_role` ENABLE KEYS */;

-- Дамп структуры для таблица poll_db.eventlogs
DROP TABLE IF EXISTS `eventlogs`;
CREATE TABLE IF NOT EXISTS `eventlogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `eventlogs_user_id_foreign` (`user_id`),
  CONSTRAINT `eventlogs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы poll_db.eventlogs: ~20 rows (приблизительно)
DELETE FROM `eventlogs`;
/*!40000 ALTER TABLE `eventlogs` DISABLE KEYS */;
INSERT INTO `eventlogs` (`id`, `type`, `user_id`, `text`, `ip`, `created_at`, `updated_at`) VALUES
	(1, 'logon', 1, 'Пользователь вошел в систему 2020-01-20 18:01:56', '127.0.0.1', '2020-01-20 18:01:56', '2020-01-20 18:01:56'),
	(2, 'logoff', 1, 'Пользователь вышел из системы 2020-01-20 18:11:49', NULL, '2020-01-20 18:11:49', '2020-01-20 18:11:49'),
	(3, 'logon', 1, 'Пользователь вошел в систему 2020-01-20 18:11:53', '127.0.0.1', '2020-01-20 18:11:53', '2020-01-20 18:11:53'),
	(4, 'logoff', 1, 'Пользователь вышел из системы 2020-01-20 18:13:39', NULL, '2020-01-20 18:13:39', '2020-01-20 18:13:39'),
	(5, 'logon', 1, 'Пользователь вошел в систему 2020-01-21 15:30:19', '127.0.0.1', '2020-01-21 15:30:19', '2020-01-21 15:30:19'),
	(6, 'logoff', 1, 'Пользователь вышел из системы 2020-01-21 15:31:48', NULL, '2020-01-21 15:31:48', '2020-01-21 15:31:48'),
	(7, 'logon', 1, 'Пользователь вошел в систему 2020-01-21 15:31:58', '127.0.0.1', '2020-01-21 15:31:58', '2020-01-21 15:31:58'),
	(8, 'logoff', 1, 'Пользователь вышел из системы 2020-01-21 15:39:28', NULL, '2020-01-21 15:39:28', '2020-01-21 15:39:28'),
	(9, 'logon', 1, 'Пользователь вошел в систему 2020-01-21 16:11:57', '127.0.0.1', '2020-01-21 16:11:57', '2020-01-21 16:11:57'),
	(10, 'logoff', 1, 'Пользователь вышел из системы 2020-01-21 16:43:15', NULL, '2020-01-21 16:43:15', '2020-01-21 16:43:15'),
	(11, 'logon', 1, 'Пользователь вошел в систему 2020-01-21 16:55:54', '127.0.0.1', '2020-01-21 16:55:54', '2020-01-21 16:55:54'),
	(12, 'logoff', 1, 'Пользователь вышел из системы 2020-01-21 16:59:54', NULL, '2020-01-21 16:59:54', '2020-01-21 16:59:54'),
	(13, 'logon', 1, 'Пользователь вошел в систему 2020-01-21 17:02:18', '127.0.0.1', '2020-01-21 17:02:18', '2020-01-21 17:02:18'),
	(14, 'logoff', 1, 'Пользователь вышел из системы 2020-01-21 17:15:44', NULL, '2020-01-21 17:15:44', '2020-01-21 17:15:44'),
	(15, 'logon', 1, 'Пользователь вошел в систему 2020-01-21 17:16:26', '127.0.0.1', '2020-01-21 17:16:26', '2020-01-21 17:16:26'),
	(16, 'logoff', 1, 'Пользователь вышел из системы 2020-01-21 17:16:52', NULL, '2020-01-21 17:16:52', '2020-01-21 17:16:52'),
	(17, 'logon', 1, 'Пользователь вошел в систему 2020-01-21 17:26:35', '127.0.0.1', '2020-01-21 17:26:35', '2020-01-21 17:26:35'),
	(18, 'logoff', 1, 'Пользователь вышел из системы 2020-01-21 17:27:49', NULL, '2020-01-21 17:27:49', '2020-01-21 17:27:49'),
	(19, 'logon', 1, 'Пользователь вошел в систему 2020-01-21 17:28:42', '127.0.0.1', '2020-01-21 17:28:42', '2020-01-21 17:28:42'),
	(20, 'logon', 1, 'Пользователь вошел в систему 2020-01-22 14:51:54', '127.0.0.1', '2020-01-22 14:51:54', '2020-01-22 14:51:54'),
	(21, 'logoff', 1, 'Пользователь вышел из системы 2020-01-22 18:19:23', NULL, '2020-01-22 18:19:23', '2020-01-22 18:19:23');
/*!40000 ALTER TABLE `eventlogs` ENABLE KEYS */;

-- Дамп структуры для таблица poll_db.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы poll_db.migrations: ~7 rows (приблизительно)
DELETE FROM `migrations`;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_resets_table', 1),
	(3, '2020_01_20_164513_create_eventlogs_table', 1),
	(4, '2020_01_22_150006_create_roles_table', 2),
	(5, '2020_01_22_150105_create_actions_table', 2),
	(6, '2020_01_22_150130_create_role_user_table', 2),
	(7, '2020_01_22_150150_create_action_role_table', 2);
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

-- Дамп структуры для таблица poll_db.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы poll_db.roles: ~0 rows (приблизительно)
DELETE FROM `roles`;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `code`, `name`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'Администратор', '2020-01-22 17:59:20', '2020-01-22 17:59:20');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Дамп структуры для таблица poll_db.role_user
DROP TABLE IF EXISTS `role_user`;
CREATE TABLE IF NOT EXISTS `role_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  KEY `role_user_user_id_foreign` (`user_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы poll_db.role_user: ~0 rows (приблизительно)
DELETE FROM `role_user`;
/*!40000 ALTER TABLE `role_user` DISABLE KEYS */;
INSERT INTO `role_user` (`id`, `role_id`, `user_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, '2020-01-22 18:17:06', '2020-01-22 21:17:06');
/*!40000 ALTER TABLE `role_user` ENABLE KEYS */;

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
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login_unique` (`login`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы poll_db.users: ~1 rows (приблизительно)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `active`, `login`, `sex`, `image`, `auth_code`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 1, 'admin', 'male,female', NULL, NULL, 'Administrator', 'rogatnev@m-strana.ru', NULL, '$2y$10$LnedN6zQ3WcWCPtu71AdpuRW8ZsZBR4CsXqAHPIP7WakiTZoZh3YS', NULL, '2020-01-20 17:34:36', '2020-01-20 17:34:36');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
