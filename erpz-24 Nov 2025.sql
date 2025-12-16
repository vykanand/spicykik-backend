-- --------------------------------------------------------
-- Host:                         junction.proxy.rlwy.net
-- Server version:               9.5.0 - MySQL Community Server - GPL
-- Server OS:                    Linux
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for erpz
CREATE DATABASE IF NOT EXISTS `erpz` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `erpz`;

-- Dumping structure for table erpz.authorization
CREATE TABLE IF NOT EXISTS `authorization` (
  `groups` text COLLATE utf8mb4_unicode_ci,
  `roles` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='groups and roles management';

-- Dumping data for table erpz.authorization: ~1 rows (approximately)
REPLACE INTO `authorization` (`groups`, `roles`) VALUES
	('[{"id":1,"name":"Administrators","roles":["admin","manager"]},{"id":2,"name":"Users","roles":["user"]},{"id":3,"name":"sys admin","roles":["linux admin","fedora admin"]},{"id":5,"name":"hdmi","roles":["manager","fedora admin"]},{"id":6,"name":"group hr","roles":["manager","user","linux admin"]}]', '[{"id":1,"name":"admin","description":"Full system access","modules":["Delivery","Lmd"]},{"id":2,"name":"manager","description":"Management access","modules":["Delivery","Lmd"]},{"id":3,"name":"user","description":"Basic user access","modules":["Lmd"]},{"id":4,"name":"fedora admin","description":"newadmin for fedora","modules":["Delivery"]},{"id":5,"name":"linux admin","description":"","modules":["Delivery"]}]');

-- Dumping structure for table erpz.delivery
CREATE TABLE IF NOT EXISTS `delivery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment` text COLLATE utf8mb4_unicode_ci,
  `awb` text COLLATE utf8mb4_unicode_ci,
  `partner` text COLLATE utf8mb4_unicode_ci,
  `mode` text COLLATE utf8mb4_unicode_ci,
  `role` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table erpz.delivery: ~15 rows (approximately)
REPLACE INTO `delivery` (`id`, `shipment`, `awb`, `partner`, `mode`, `role`, `created_at`) VALUES
	(6, 'hikb', 'vguh', 'cgvjjh', 'kws', 'admin', '2025-02-09 23:27:45'),
	(7, 'fwejn', 'bj', 'kjnjk', 'kjn', 'admin', '2025-02-09 23:49:50'),
	(8, 'vjnw', 'hkb', 'jkbkj', 'njkn', 'admin', '2025-02-09 23:50:00'),
	(9, 'fewjn', 'jnjk', 'kbnbm', 'mnb', 'admin', '2025-02-09 23:50:10'),
	(10, 'fejn', 'kjnmnb', 'nbmnb', 'bjh', 'admin', '2025-02-09 23:50:22'),
	(11, 'few', 'mk', 'km', 'bjm', 'admin', '2025-02-09 23:50:38'),
	(12, 'vsa', 'gretdf', 'jknk', 'bjsc', 'admin', '2025-02-10 00:22:52'),
	(13, 'vsa', 'bhkj', 'hbjkvh', 'hb', 'admin', '2025-02-10 00:23:14'),
	(14, 'efw', 'jbk', 'bkjb', 'kjb', 'admin', '2025-02-10 00:23:44'),
	(15, 'vjh', 'gv', 'hjkb', 'kj', 'admin', '2025-02-10 00:29:47'),
	(16, 'bjk', 'grfsdvfb', 'okok', 'ojkl', 'admin', '2025-02-10 00:30:08'),
	(17, 'vsak', 'https://i.ibb.co/mC18xV4w/Whats-App-Image-2025-10-05-at-9-50-11-AM.jpg', 'jbkj', 'bkw', 'admin', '2025-02-10 00:48:06'),
	(18, 'https://www.experte.com/online-meeting?join=b1dfwp', '1111', 'jbkjn', 'hbjl', 'admin', '2025-02-10 19:59:44'),
	(21, 'gsankl', 'https://i.ibb.co/840vVSFv/PLUK.png', '444', 'ibjgken', 'admin', '2025-02-12 11:04:41'),
	(22, '1234r', 'bihj', 'plm', 'ibjgnrk', 'admin', '2025-02-12 12:18:16'),
	(23, 'QR', 'EAE64282040', 'capacitor', 'production', 'admin', '2025-09-13 09:13:43');

-- Dumping structure for table erpz.navigation
CREATE TABLE IF NOT EXISTS `navigation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nav` mediumtext COLLATE utf8mb4_unicode_ci,
  `urn` mediumtext COLLATE utf8mb4_unicode_ci,
  `plugin` longtext COLLATE utf8mb4_unicode_ci,
  `field_types` text COLLATE utf8mb4_unicode_ci,
  `field_required` text COLLATE utf8mb4_unicode_ci,
  `field_options` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table erpz.navigation: ~3 rows (approximately)
REPLACE INTO `navigation` (`id`, `nav`, `urn`, `plugin`, `field_types`, `field_required`, `field_options`, `created_at`) VALUES
	(18, 'Delivery', 'delivery/boot.html', NULL, NULL, NULL, NULL, '2024-12-28 19:56:31'),
	(125, 'News1', 'news1/boot.html', NULL, NULL, NULL, NULL, '2025-10-22 19:23:29'),
	(133, 'weather1', 'weather1/boot.html', '{"latitude":"Experte Meetings","country_code":"testplugin","population":"Calculator","admin2_id":"Blob Uploader"}', '{"name":"text","latitude":"text","longitude":"text","elevation":"text","feature_code":"text","country_code":"text","admin1_id":"email","timezone":"time","population":"text","country_id":"text","country":"checkbox","admin1":"text","admin2_id":"text","admin3_id":"date","postcodes":"number","admin2":"select","admin3":"radio"}', '{"admin1_id":"true","timezone":"true","admin3_id":"true","postcodes":"true","admin2":"true"}', '{"country":"India,Nepal,US","admin2":"Yes,No,Invalid","admin3":"Asia,America,Europe"}', '2025-10-22 20:36:53');

-- Dumping structure for table erpz.news1
CREATE TABLE IF NOT EXISTS `news1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author` text COLLATE utf8mb4_unicode_ci,
  `title` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `url` text COLLATE utf8mb4_unicode_ci,
  `source` text COLLATE utf8mb4_unicode_ci,
  `image` text COLLATE utf8mb4_unicode_ci,
  `category` text COLLATE utf8mb4_unicode_ci,
  `language` text COLLATE utf8mb4_unicode_ci,
  `country` text COLLATE utf8mb4_unicode_ci,
  `published_at` text COLLATE utf8mb4_unicode_ci,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table erpz.news1: ~0 rows (approximately)
REPLACE INTO `news1` (`id`, `author`, `title`, `description`, `url`, `source`, `image`, `category`, `language`, `country`, `published_at`, `role`, `created_at`) VALUES
	(1, 'hgnd', 'boj', 'hi k', 'oikn', 'pn', 'buoj', 'okn', 'b oj', 'poj', 'hrdf', 'admin', '2025-10-22 19:24:11');

-- Dumping structure for table erpz.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access` json DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loginstatus` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'False',
  `apikey` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'local',
  `provider_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_token` text COLLATE utf8mb4_unicode_ci,
  `access_token` text COLLATE utf8mb4_unicode_ci,
  `refresh_token` text COLLATE utf8mb4_unicode_ci,
  `token_expiry` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_groups` text COLLATE utf8mb4_unicode_ci,
  `user_roles` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=726 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table erpz.users: ~3 rows (approximately)
REPLACE INTO `users` (`id`, `email`, `access`, `phone`, `name`, `password`, `loginstatus`, `apikey`, `provider`, `provider_id`, `id_token`, `access_token`, `refresh_token`, `token_expiry`, `last_login`, `avatar`, `role`, `address`, `created_at`, `user_groups`, `user_roles`) VALUES
	(1, 'admin@demo.com', '["Delivery"]', '9829384775', 'Admin', 'demo', 'True', 'rBPCExGq2IJDz48OAleQ', 'local', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'A-120 Fng sec 2', '2021-08-03 13:36:13', '["Administrators","Users","sys admin"]', '["admin","manager","user","linux admin"]'),
	(717, 'user2@demo.com', '["Delivery"]', '9283938288', 'Ashley', 'demo', 'True', 'mA0GI0GRYyneeW2Gqn2X', 'local', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', '112 Kenington lake', '2021-08-09 13:36:13', '["users"]', '["admin","storekeeper"]'),
	(725, 'user@demo.com', '["Delivery"]', '9868787554', 'John Mac', 'demo', 'True', 'uqHZXhvynUDFYnIYPRnu', 'local', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', '22 F Bangalore india', '2021-08-08 13:36:13', '["sys admin"]', '["user"]');

-- Dumping structure for table erpz.weather1
CREATE TABLE IF NOT EXISTS `weather1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_unicode_ci,
  `latitude` text COLLATE utf8mb4_unicode_ci,
  `longitude` text COLLATE utf8mb4_unicode_ci,
  `elevation` text COLLATE utf8mb4_unicode_ci,
  `feature_code` text COLLATE utf8mb4_unicode_ci,
  `country_code` text COLLATE utf8mb4_unicode_ci,
  `admin1_id` text COLLATE utf8mb4_unicode_ci,
  `timezone` text COLLATE utf8mb4_unicode_ci,
  `population` text COLLATE utf8mb4_unicode_ci,
  `country_id` text COLLATE utf8mb4_unicode_ci,
  `country` text COLLATE utf8mb4_unicode_ci,
  `admin1` text COLLATE utf8mb4_unicode_ci,
  `admin2_id` text COLLATE utf8mb4_unicode_ci,
  `admin3_id` text COLLATE utf8mb4_unicode_ci,
  `postcodes` text COLLATE utf8mb4_unicode_ci,
  `admin2` text COLLATE utf8mb4_unicode_ci,
  `admin3` text COLLATE utf8mb4_unicode_ci,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table erpz.weather1: ~38 rows (approximately)
REPLACE INTO `weather1` (`id`, `name`, `latitude`, `longitude`, `elevation`, `feature_code`, `country_code`, `admin1_id`, `timezone`, `population`, `country_id`, `country`, `admin1`, `admin2_id`, `admin3_id`, `postcodes`, `admin2`, `admin3`, `role`, `created_at`) VALUES
	(1, 'Delhi', '28.65195', '77.23149', '227', 'PPLA', 'IN', '1273293', 'Asia/Kolkata', '11034555', '1269750', 'India', 'Delhi', NULL, NULL, NULL, NULL, NULL, 'admin', '2025-10-22 20:03:08'),
	(2, 'Delhi', '42.27814', '-74.91599', '418', 'PPLA2', 'US', '5128638', 'America/New_York', '3157', '6252001', 'United States', 'New York', '5114810', '5114826', 'Array', 'Delaware', 'Town of Delhi', 'admin', '2025-10-22 20:03:09'),
	(3, 'Delhi', '37.43216', '-120.77854', '36', 'PPL', 'US', '5332921', 'America/Los_Angeles', '10755', '6252001', 'United States', 'California', '5372259', NULL, 'Array', 'Merced', NULL, 'admin', '2025-10-22 20:03:11'),
	(4, 'Delhi', '32.45764', '-91.49317', '27', 'PPL', 'US', '4331987', 'America/Chicago', '2900', '6252001', 'United States', 'Louisiana', '4338711', NULL, 'Array', 'Richland', NULL, 'admin', '2025-10-22 20:03:12'),
	(5, 'Delhi', '42.42971', '-91.33098', '314', 'PPL', 'US', '4862182', 'America/Chicago', '471', '6252001', 'United States', 'Iowa', '4853709', '4853730', 'Array', 'Delaware', 'Delhi Township', 'admin', '2025-10-22 20:03:13'),
	(6, 'San Francisco', '37.77493', '-122.41942', '16', 'PPLA2', 'US', '5332921', 'America/Los_Angeles', '864816', '6252001', 'United States', 'California', '5391997', NULL, 'Array', 'San Francisco County', NULL, 'admin', '2025-10-22 20:37:32'),
	(7, 'San Francisco', '13.7', '-88.1', '270', 'PPLA', 'SV', '3584317', 'America/El_Salvador', '16152', '3585968', 'El Salvador', 'Departamento de Morazán', NULL, NULL, NULL, NULL, NULL, 'admin', '2025-10-22 20:37:34'),
	(8, 'San Francisco', '-31.42497', '-62.08404', '120', 'PPLA2', 'AR', '3860255', 'America/Argentina/Cordoba', '59062', '3865483', 'Argentina', 'Cordoba', '3837121', NULL, NULL, 'San Justo Department', NULL, 'admin', '2025-10-22 20:37:35'),
	(9, 'Sant Francesc de Formentera', '38.70566', '1.42893', '39', 'PPLA3', 'ES', '2521383', 'Europe/Madrid', '2656', '2510769', 'Spain', 'Balearic Islands', '6424360', '6356033', 'Array', 'Balearic Islands', 'Formentera', 'admin', '2025-10-22 20:37:36'),
	(10, 'San Francisco', '16.7978', '-89.93507', '218', 'PPLA2', 'GT', '3591410', 'America/Guatemala', '3954', '3595528', 'Guatemala', 'Petén', '3590233', NULL, NULL, 'Municipio de San Francisco', NULL, 'admin', '2025-10-22 20:37:38'),
	(11, 'San Francisco', '8.24541', '-80.97368', '90', 'PPLA2', 'PA', '3700159', 'America/Panama', '1785', '3703430', 'Panama', 'Veraguas Province', '3701474', '3701476', NULL, 'San Francisco District', 'Corregimiento San Francisco', 'admin', '2025-10-22 20:37:39'),
	(12, 'San Francisco', '8.53556', '125.95', '39', 'PPLA3', 'PH', '7521299', 'Asia/Manila', '18542', '1694008', 'Philippines', 'Caraga', '1731818', '1690024', NULL, 'Province of Agusan del Sur', 'Municipality of San Francisco', 'admin', '2025-10-22 20:37:40'),
	(13, 'San Francisco', '10.6461', '124.3816', '7', 'PPLA3', 'PH', '7521306', 'Asia/Manila', '8989', '1694008', 'Philippines', 'Central Visayas', '1717511', '1690021', NULL, 'Province of Cebu', 'San Francisco', 'admin', '2025-10-22 20:37:42'),
	(14, 'San Francisco', '10.06', '125.16056', '15', 'PPLA3', 'PH', '7521307', 'Asia/Manila', '2676', '1694008', 'Philippines', 'Eastern Visayas', '1685725', '1690022', NULL, 'Southern Leyte', 'Municipality of San Francisco', 'admin', '2025-10-22 20:37:43'),
	(15, 'San Francisco', '10.55363', '-71.70364', '54', 'PPLA2', 'VE', '3625035', 'America/Caracas', NULL, '3625428', 'Venezuela', 'Zulia', '8131515', NULL, NULL, 'San Francisco Municipality', NULL, 'admin', '2025-10-22 20:37:44'),
	(16, 'San Francisco', '5.96426', '-75.10165', '1276', 'PPLA2', 'CO', '3689815', 'America/Bogota', NULL, '3686110', 'Colombia', 'Antioquia', '9172258', NULL, NULL, 'San Francisco', NULL, 'admin', '2025-10-22 20:37:46'),
	(17, 'San Francisco', '9.77694', '125.42472', '8', 'PPLA3', 'PH', '7521299', 'Asia/Manila', NULL, '1694008', 'Philippines', 'Caraga', '1685215', '1690023', NULL, 'Province of Surigao del Norte', 'San Francisco (Anao-Aon)', 'admin', '2025-10-22 20:37:47'),
	(18, 'San Francisco', '8.99235', '-79.50818', '23', 'PPLA3', 'PA', '3703433', 'America/Panama', NULL, '3703430', 'Panama', 'Provincia de Panamá', '3703439', '3701475', NULL, 'Panamá District', 'Corregimiento San Francisco', 'admin', '2025-10-22 20:37:48'),
	(19, 'San Francisco', '-12.62456', '-73.78777', '433', 'PPLA3', 'PE', '3947018', 'America/Lima', NULL, '3932488', 'Peru', 'Ayacucho', '3937045', '8350042', NULL, 'La Mar Province', 'Ayna', 'admin', '2025-10-22 20:37:49'),
	(20, 'Dubai', '25.07725', '55.30927', '24', 'PPLA', 'AE', '292224', 'Asia/Dubai', '3790000', '290557', 'United Arab Emirates', 'Dubai', NULL, NULL, NULL, NULL, NULL, 'admin', '2025-10-22 20:40:47'),
	(21, 'Dubai', '31.681', '106.90117', '599', 'PPL', 'CN', '1794299', 'Asia/Shanghai', NULL, '1814991', 'China', 'Sichuan', '6643375', NULL, NULL, 'Bazhong', NULL, 'admin', '2025-10-22 20:40:49'),
	(22, 'Dubai', '28.7539', '101.252', '2454', 'PPL', 'CN', '1794299', 'Asia/Shanghai', NULL, '1814991', 'China', 'Sichuan', '1810269', NULL, NULL, 'Garzê', NULL, 'admin', '2025-10-22 20:40:51'),
	(23, 'Dubai', '26.31205', '80.75172', '123', 'PPL', 'IN', '1253626', 'Asia/Kolkata', NULL, '1269750', 'India', 'Uttar Pradesh', '1253748', '12683035', NULL, 'Unnao', 'Bighapur', 'admin', '2025-10-22 20:40:53'),
	(24, 'Dubai', '27.08365', '81.75133', '107', 'PPL', 'IN', '1253626', 'Asia/Kolkata', NULL, '1269750', 'India', 'Uttar Pradesh', '1270997', '12683164', NULL, 'Gonda', 'Colonelganj', 'admin', '2025-10-22 20:40:55'),
	(25, 'Dubai', '27.0843', '82.93179', '95', 'PPL', 'IN', '1253626', 'Asia/Kolkata', NULL, '1269750', 'India', 'Uttar Pradesh', '7626228', '12683169', NULL, 'Siddharth Nagar', 'Bansi', 'admin', '2025-10-22 20:40:57'),
	(26, 'Dubai', '30.24751', '103.78073', '559', 'MT', 'CN', '1794299', 'Asia/Shanghai', NULL, '1814991', 'China', 'Sichuan', '8537504', NULL, NULL, 'Meishan Shi', NULL, 'admin', '2025-10-22 20:40:59'),
	(27, 'Dubai Investments Park', '25.00827', '55.15682', NULL, 'PPLX', 'AE', '292224', 'Asia/Dubai', '160000', '290557', 'United Arab Emirates', 'Dubai', NULL, NULL, NULL, NULL, NULL, 'admin', '2025-10-22 20:41:01'),
	(28, 'Dubaia', '9.7277', '-12.06106', '131', 'PPL', 'SL', '2404798', 'Africa/Freetown', NULL, '2403846', 'Sierra Leone', 'Northern Province', NULL, NULL, NULL, NULL, NULL, 'admin', '2025-10-22 20:41:05'),
	(29, 'Dubaia', '9.51667', '-11.96667', '115', 'PPL', 'SL', '2404798', 'Africa/Freetown', NULL, '2403846', 'Sierra Leone', 'Northern Province', NULL, NULL, NULL, NULL, NULL, 'admin', '2025-10-22 20:41:07'),
	(30, 'Dubaia', '9.8712', '-12.31697', '120', 'PPL', 'SL', '2404798', 'Africa/Freetown', NULL, '2403846', 'Sierra Leone', 'Northern Province', NULL, NULL, NULL, NULL, NULL, 'admin', '2025-10-22 20:41:09'),
	(31, 'Temasek', '1.28967', '103.85007', '23', 'PPLC', 'SG', NULL, 'Asia/Singapore', '5638700', '1880251', 'Singapore', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', '2025-10-22 21:20:47'),
	(32, 'Singapore', '1.36667', '103.8', '42', 'PCLI', 'SG', NULL, 'Asia/Singapore', '5638676', '1880251', 'Singapore', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', '2025-10-22 21:20:49'),
	(33, 'Singapore', '-24.56233', '29.31502', '911', 'PPL', 'ZA', '1085597', 'Africa/Johannesburg', NULL, '953987', 'South Africa', 'Limpopo', '8347375', '8347406', NULL, 'Waterberg District Municipality', 'Mookgopong', 'admin', '2025-10-22 21:20:51'),
	(34, 'Singapore Island', '1.36667', '103.8', '42', 'ISL', 'SG', NULL, 'Asia/Singapore', NULL, '1880251', 'Singapore', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', '2025-10-22 21:20:53'),
	(35, 'Singapore Changi Airport', '1.35514', '103.99006', '6', 'AIRP', 'SG', 'sfac@fas.xom', '18:12:00', NULL, '1880251', 'Singapore', NULL, NULL, '2025-11-19', '532423', 'Yes', 'Asia', 'admin', '2025-10-22 21:20:55'),
	(36, 'Singapore United Plantation', '1.37917', '103.85778', '22', 'PPLL', 'SG', 'csac@sac.ca', 'Thu Jan 01 1970 05:57:00 GMT+0530 (India Standard Time)', NULL, '1880251', 'Singapore', NULL, NULL, '2025-11-07', '12431', 'No', 'Europe', 'admin', '2025-10-22 21:20:57'),
	(37, 'Singapore Tuas West Highway', 'https://www.experte.com/online-meeting?join=yvih6v', '103.64083', '14', 'AIRF', 'SG', 'fewfs@safs.com', '08:41', NULL, '1880251', 'Singapore', 'vsac', 'https://i.ibb.co/Y4fjmzNk/Whats-App-Image-2025-11-09-at-1-42-44-PM.jpg', '2025-11-12', '201299', 'Yes', 'America', 'admin', '2025-10-22 21:20:59'),
	(38, 'Singapore, Michigan Historical Marker', 'https://www.experte.com/online-meeting?join=zez57k', '-86.20317', '182', 'PRK', 'US', '5001836', 'America/Detroit', NULL, '6252001', 'United States', 'Michigan', '4983990', NULL, NULL, 'Allegan', NULL, 'admin', '2025-10-22 21:21:01'),
	(39, '', 'https://www.experte.com/online-meeting?join=gzzo9', '', '', '', '', 'btfvdsc@afnk.com', '09:12:00', '', '', '', '', 'ngwojef', '2025-11-07', '23523432', 'Yes', 'Europe', 'admin', '2025-11-22 00:41:25');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
