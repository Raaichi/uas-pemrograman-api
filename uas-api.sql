-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table uas_api.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table uas_api.products: ~15 rows (approximately)
INSERT IGNORE INTO `products` (`id`, `name`, `description`, `price`, `stock`, `created_at`, `updated_at`) VALUES
	(1, 'Gojo Satoru Figure', 'Figure Gojo Satoru dari Jujutsu Kaisen ukuran 20cm', 450000.00, 10, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(2, 'Anya Forger Acrylic Stand', 'Acrylic stand karakter Anya Forger dari Spy x Family', 85000.00, 25, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(3, 'Naruto Keychain Konoha', 'Gantungan kunci Naruto logo desa Konoha', 35000.00, 30, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(4, 'Levi Ackerman Figure', 'Figure Levi Ackerman dari Attack on Titan limited edition', 520000.00, 8, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(5, 'Miku Hatsune Acrylic Stand', 'Standee anime Hatsune Miku aesthetic edition', 95000.00, 15, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(6, 'Luffy Gear 5 Keychain', 'Keychain Monkey D. Luffy Gear 5 berbahan acrylic', 40000.00, 40, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(7, 'Zero Two Figure', 'Figure Zero Two dari Darling in the Franxx', 475000.00, 12, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(8, 'Rem Re:Zero Pillow Plush', 'Boneka bantal karakter Rem dari Re:Zero', 150000.00, 20, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(9, 'Kakashi Sharingan Poster', 'Poster anime Kakashi Hatake ukuran A3', 50000.00, 18, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(10, 'Tanjiro Kamado Keychain', 'Keychain karakter Tanjiro dari Demon Slayer', 30000.00, 28, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(11, 'Nezuko Figure Demon Form', 'Figure Nezuko mode demon premium edition', 430000.00, 9, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(12, 'Bocchi The Rock Acrylic Stand', 'Acrylic stand karakter Bocchi aesthetic version', 90000.00, 14, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(13, 'Genshin Impact Hu Tao Keychain', 'Keychain Hu Tao chibi edition', 45000.00, 35, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(14, 'Itachi Uchiha Figure', 'Figure Itachi Uchiha Akatsuki version', 510000.00, 7, '2026-06-08 03:54:07', '2026-06-08 03:54:07'),
	(15, 'Mikasa Ackerman Poster', 'Poster Mikasa Ackerman Attack on Titan', 55000.00, 16, '2026-06-08 03:54:07', '2026-06-08 03:54:07');

-- Dumping structure for table uas_api.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key_plain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key_last4` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key_created_at` datetime DEFAULT NULL,
  `sample_seeded_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table uas_api.users: ~4 rows (approximately)
INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password_hash`, `api_key_plain`, `api_key_hash`, `api_key_last4`, `api_key_created_at`, `sample_seeded_at`, `created_at`, `updated_at`) VALUES
	(1, 'UAS Demo', 'uas-demo@example.com', '$2y$12$YKO1Q1ScNSlig3BSPshcUeyJA5H31VqkJHp8FcduG2hpSwMr7rfge', 'merch_653e85d1c50293babfe6622b0bdbb2e202fa42cf6fd0638a', '7a555b9d0faaa1a42d41fe9d93343e1033f93d5094a37cadce393189d77f7d30', '638A', '2026-06-08 06:57:16', '2026-06-08 03:37:33', '2026-06-08 03:21:28', '2026-06-08 06:57:16'),
	(2, 'raaichi', 'raaichi@pemapi.com', '$2y$12$sXIeeCnrdby8iDGCLFqBNOJgNVDX/muIbSfUnq4tTDMmOnM3.2HIC', 'merch_b49c7e3d256689b8e9d04ce96c055fdbbf0870931520b615', '94c98a5e3f08a2ede96fe2b1b6a85ec7dbacb735cb2c3442603ec9dbf70ef701', 'B615', '2026-06-08 06:57:16', '2026-06-08 03:38:26', '2026-06-08 03:35:17', '2026-06-08 06:57:16'),
	(3, 'UAS Demo 2', 'uas-demo2@example.com', '$2y$12$OVszLPYnqNiSZ79ZjuLJWOGOr5M.Xz2jLaJPltIt677YEAqNS0kTu', 'merch_a3d713d609cb96870365305ff65bd1060497d53ca2dd5463', 'd5c0baed276b901e13e3408faa76d5047c9e84f0cce011d3213c9c929f36975e', '5463', '2026-06-08 06:57:16', NULL, '2026-06-08 03:52:48', '2026-06-08 06:57:16'),
	(4, 'Zahra', 'zahra@pemapi.com', '$2y$12$kM7U7Mw8bH4Jm80.l8HNWuherbRs19M8CH7.4nZFpk/Gjjn.F2wqS', 'merch_c7039162c2a349eab254d954bbc87e3ee88eeb0d3e42390c', 'dbd10da7124d63ac757d71cd3538f85a31407a7928b9a8554b1a9d7a71c2eef1', '390C', '2026-06-10 11:25:07', NULL, '2026-06-10 11:25:07', '2026-06-10 11:25:07');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
