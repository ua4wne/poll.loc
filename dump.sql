-- --------------------------------------------------------
-- Хост:                         sql242.main-hosting.eu
-- Версия сервера:               10.2.30-MariaDB - MariaDB Server
-- Операционная система:         Linux
-- HeidiSQL Версия:              10.1.0.5464
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица u507718257_poll.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `login` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sex` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'male',
  `image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login_unique` (`login`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы u507718257_poll.users: ~12 rows (приблизительно)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `active`, `login`, `sex`, `image`, `auth_code`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 1, 'ircut', 'male', '/images/gangster.png', NULL, 'Рогатнев А.И.', 'rogatnev@m-strana.ru', NULL, '$2y$10$tzU57LomRf4EQxrbRo3pwumyzY7HF74fYap04p0RifwC07fRH.7dy', NULL, '2020-01-20 17:34:36', '2020-02-17 10:15:37'),
	(2, 1, 'babenko', 'male', '/images/male.png', NULL, 'Бабенко В.А', 'babenko@m-strana.ru', NULL, '$2y$10$DYhJEhD.JLYFVEPjoDM9.eyrKLAmOw8AlQQ6yeeJT.yoQ0RtqgXOC', NULL, '2020-01-23 13:06:32', '2020-02-17 07:42:33'),
	(3, 1, 'borisenko', 'male', '/images/male.png', 'HWaSseqDm1E30db9', 'Борисенко А.В.', 'borisenko@m-strana.ru', NULL, '$2y$10$r/QIxhRHKMMre5./Bgd7fujWqP63KBGF9jl0lj2EkQBb0ubLS.nJi', NULL, '2020-01-23 13:22:53', '2020-02-17 07:42:41'),
	(4, 1, 'lenshin', 'male', '/images/male.png', 'quQMtfVoXYDv68Hy', 'Л', 'oblako@mirkdt.com', NULL, '$2y$10$w9aR0uVe1QpsoJkCR11iiOp28diRXFl0qhPEYo0hvBVQV9FrQ44ZC', NULL, '2020-01-23 13:29:37', '2020-02-21 12:32:11'),
	(5, 1, 'konyahin', 'male', '/images/male.png', 'VJymo9xFgAIDMvTi', 'Коняхин Р.А.', 'konyahin@m-strana.ru', NULL, '$2y$10$Ar0ntM4aJYFdbIjgVfhI9eP7yLU8HcNfBY/VL2eET/HlCPrjWddV.', NULL, '2020-01-23 13:36:36', '2020-02-17 08:52:43'),
	(6, 1, 'pykhov', 'male', '/images/male.png', 'vX4ikaVAYZfBLIyM', 'Пыхов Н.Н.', 'pykhov@m-strana.ru', NULL, '$2y$10$bA2CdwedZEAtdW/bkTs4fOBRsCuNjkVJfbU9K4w.7/n6NeIQ1Mvzi', NULL, '2020-01-25 06:41:09', '2020-02-17 07:43:06'),
	(7, 1, 'antipin', 'male', '/images/male.png', 'aJpEXGb4u7ysAncK', 'Антипин А.Д.', 'antipin@m-strana.ru', NULL, '$2y$10$WdFSovVox763CCROgaQ7J.6LmnEXz471YUKB1mJxU9kYtUpxyIiji', NULL, '2020-01-27 08:30:53', '2020-02-17 07:43:16'),
	(8, 1, 'ovsienko', 'male', '/images/male.png', 'FOj4PTYyow8qNA65', 'Овсиенко А.А.', 'ovsienko@m-strana.ru', NULL, '$2y$10$oBp0710HifcKVWbKxeQYF.tPCELq9a35lSU.aj1A1IH2bbf8FdVNy', NULL, '2020-01-27 08:33:20', '2020-02-17 07:43:24'),
	(9, 1, 'stadler', 'female', '/images/female.png', 'RmEJagL8tv5nAy3x', 'Стадлер М.С.', 'stadler@m-strana.ru', NULL, '$2y$10$y6yJufFN1PabhbpA8.FH4OixqpByKNsf02fhHB6aUohSlhfML4hj.', NULL, '2020-01-27 08:38:38', '2020-02-17 07:43:34'),
	(10, 1, 'avdeev', 'male', '/images/male.png', NULL, 'Авдеев Д.И.', 'avdeev@m-strana.ru', NULL, '$2y$10$ESKZzBAESU3OwJGmijxMduSRmm0Ztla0RBrMIlQpVSKr/9EoI.Xfe', NULL, '2020-02-15 12:30:11', '2020-02-17 07:12:23'),
	(11, 1, 'operator', 'male', '/images/male.png', NULL, 'АЗ Пост №4', 'admin@m-strana.ru', NULL, '$2y$10$GpmQVXE9PGnqiK/wIjXwLe2BvWdquOXn0z9yv87kBPXIk/LEUZVgC', NULL, '2020-02-15 12:34:28', '2020-02-15 13:17:53'),
	(12, 1, 'shumilin', 'male', '/images/male.png', NULL, 'Шумилин А.В.', 'shumilin@m-strana.ru', NULL, '$2y$10$py8ZKkMzCVzHWdZmjq0JeeRWHQbAzM4gV5TtzakHZ9qvuxKWY9oCy', NULL, '2020-02-15 12:36:44', '2020-02-25 07:21:18'),
	(13, 1, 'demin', 'male', '/images/male.png', NULL, 'Демин П.И.', 'demin@m-strana.ru', NULL, '$2y$10$Tsk6RUe0tFt2WfQd7pbHPu0opdPGfq1zwtVXJ3oKDov3KjdECL2mO', NULL, '2020-02-15 12:38:23', '2020-02-17 07:22:53'),
	(14, 1, 'inter', 'male', '/images/male.png', NULL, 'Интервьюер', 'robot@m-strana.ru', NULL, '$2y$10$7V2rjpCSojKDO/DmGdRqf.TVb.o6tA9KodfNLaYo6.39NuO5LXcU.', NULL, '2020-02-26 10:01:16', '2020-02-26 10:10:10');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
