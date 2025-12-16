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


-- Dumping database structure for spicykik
CREATE DATABASE IF NOT EXISTS `spicykik` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `spicykik`;

-- Dumping structure for table spicykik.appsthink_crm
CREATE TABLE IF NOT EXISTS `appsthink_crm` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` text COLLATE utf8mb4_unicode_ci,
  `phone_number` text COLLATE utf8mb4_unicode_ci,
  `company_name` text COLLATE utf8mb4_unicode_ci,
  `address` text COLLATE utf8mb4_unicode_ci,
  `name` text COLLATE utf8mb4_unicode_ci,
  `client_name` text COLLATE utf8mb4_unicode_ci,
  `designation` text COLLATE utf8mb4_unicode_ci,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spicykik.appsthink_crm: ~2 rows (approximately)
REPLACE INTO `appsthink_crm` (`id`, `email`, `phone_number`, `company_name`, `address`, `name`, `client_name`, `designation`, `role`, `created_at`) VALUES
	(1, 'wfasf', 'fea', 'egdvs', 'gdv', 'gdvs', 'wevds', 'rbfsvc', 'admin', '2025-12-03 19:51:16'),
	(2, 'njlBJ', 'boj', 'boj', 'nokmp', 'pijbjk', 'vjb', 'xcfvh', 'admin', '2025-12-07 20:37:17');

-- Dumping structure for table spicykik.authorization
CREATE TABLE IF NOT EXISTS `authorization` (
  `groups` text COLLATE utf8mb4_unicode_ci,
  `roles` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='groups and roles management';

-- Dumping data for table spicykik.authorization: ~2 rows (approximately)
REPLACE INTO `authorization` (`groups`, `roles`) VALUES
	('[{"id":1,"name":"Administrators","roles":["admin","manager"]},{"id":2,"name":"Users","roles":["user"]},{"id":3,"name":"sys admin","roles":["linux admin","fedora admin"]},{"id":5,"name":"hdmi","roles":["manager","fedora admin"]},{"id":6,"name":"group hr","roles":["manager","user","linux admin"]}]', '[{"id":1,"name":"admin","description":"Full system access","modules":["Delivery","Lmd"]},{"id":2,"name":"manager","description":"Management access","modules":["Delivery","Lmd"]},{"id":3,"name":"user","description":"Basic user access","modules":["Lmd"]},{"id":4,"name":"fedora admin","description":"newadmin for fedora","modules":["Delivery"]},{"id":5,"name":"linux admin","description":"","modules":["Delivery"]}]'),
	('[{"id":1,"name":"Administrators","roles":["admin","manager"]},{"id":2,"name":"Users","roles":["user"]},{"id":3,"name":"sys admin","roles":["linux admin","fedora admin"]},{"id":5,"name":"hdmi","roles":["manager","fedora admin"]},{"id":6,"name":"group hr","roles":["manager","user","linux admin"]}]', '[{"id":1,"name":"admin","description":"Full system access","modules":["Delivery","Lmd"]},{"id":2,"name":"manager","description":"Management access","modules":["Delivery","Lmd"]},{"id":3,"name":"user","description":"Basic user access","modules":["Lmd"]},{"id":4,"name":"fedora admin","description":"newadmin for fedora","modules":["Delivery"]},{"id":5,"name":"linux admin","description":"","modules":["Delivery"]}]');

-- Dumping structure for table spicykik.crimping_process
CREATE TABLE IF NOT EXISTS `crimping_process` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Wire_number` text COLLATE utf8mb4_unicode_ci,
  `Wire_colour` text COLLATE utf8mb4_unicode_ci,
  `SAP_ID` text COLLATE utf8mb4_unicode_ci,
  `SAB_Wire_detail` text COLLATE utf8mb4_unicode_ci,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spicykik.crimping_process: ~1 rows (approximately)
REPLACE INTO `crimping_process` (`id`, `Wire_number`, `Wire_colour`, `SAP_ID`, `SAB_Wire_detail`, `role`, `created_at`) VALUES
	(1, '43546576', 'fghvjbaf', '34356', '2345df', 'admin', '2025-12-13 11:09:23');

-- Dumping structure for table spicykik.delivery
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

-- Dumping data for table spicykik.delivery: ~16 rows (approximately)
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

-- Dumping structure for table spicykik.extract_contact
CREATE TABLE IF NOT EXISTS `extract_contact` (
  `id` int NOT NULL AUTO_INCREMENT,
  `emails_value` text COLLATE utf8mb4_unicode_ci,
  `phoneNumbers_value` text COLLATE utf8mb4_unicode_ci,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spicykik.extract_contact: ~5 rows (approximately)
REPLACE INTO `extract_contact` (`id`, `emails_value`, `phoneNumbers_value`, `role`, `created_at`) VALUES
	(1, 'info@logunova.com', '(213) 338-2332', 'admin', '2025-12-01 20:25:53'),
	(2, 'support@logunova.com', '+1 (213) 338-2332', 'admin', '2025-12-01 20:25:54'),
	(3, '', '213 338 2332', 'admin', '2025-12-01 20:25:56'),
	(4, 'fdesc', 'bgfvd', 'admin', '2025-12-01 20:41:44'),
	(5, 'nlno', 'bodjn', 'admin', '2025-12-02 17:51:34');

-- Dumping structure for table spicykik.navigation
CREATE TABLE IF NOT EXISTS `navigation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nav` mediumtext COLLATE utf8mb4_unicode_ci,
  `urn` mediumtext COLLATE utf8mb4_unicode_ci,
  `plugin` longtext COLLATE utf8mb4_unicode_ci,
  `field_types` text COLLATE utf8mb4_unicode_ci,
  `field_required` text COLLATE utf8mb4_unicode_ci,
  `field_options` text COLLATE utf8mb4_unicode_ci,
  `parent` json DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spicykik.navigation: ~9 rows (approximately)
REPLACE INTO `navigation` (`id`, `nav`, `urn`, `plugin`, `field_types`, `field_required`, `field_options`, `parent`, `created_at`) VALUES
	(18, 'Delivery', 'delivery/boot.html', NULL, NULL, NULL, NULL, '{"pos": 0, "parents": []}', '2024-12-28 19:56:31'),
	(125, 'News1', 'news1/boot.html', NULL, NULL, NULL, '1', '{"pos": 0, "parents": [133]}', '2025-10-22 19:23:29'),
	(133, 'weather1', 'weather1/boot.html', '{"latitude":"Experte Meetings","country_code":"testplugin","population":"Calculator","admin2_id":"Blob Uploader"}', '{"name":"text","latitude":"text","longitude":"text","elevation":"text","feature_code":"text","country_code":"text","admin1_id":"email","timezone":"time","population":"text","country_id":"text","country":"checkbox","admin1":"text","admin2_id":"text","admin3_id":"date","postcodes":"number","admin2":"select","admin3":"radio"}', '{"admin1_id":"true","timezone":"true","country":"true","admin3_id":"true","postcodes":"true","admin2":"true"}', '{"country":"Argentina,China,Colombia,El Salvador,Guatemala,India,Panama,Peru,Philippines,SG,Sierra Leone,Singapore,South Africa,Spain,United Arab Emirates,United States,Venezuela","admin2":"Allegan,Balearic Islands,Bazhong,Delaware,Garz\\u00ea,Gonda,La Mar Province,Meishan Shi,Merced,Municipio de San Francisco,No,Panam\\u00e1 District,Province of Agusan del Sur,Province of Cebu,Province of Surigao del Norte,Richland,San Francisco,San Francisco County,San Francisco District,San Francisco Municipality,San Justo Department,Siddharth Nagar,Southern Leyte,Unnao,Waterberg District Municipality,Yes","admin3":"America,Asia,Ayna,Bansi,Bighapur,Colonelganj,Corregimiento San Francisco,Delhi Township,Europe,Formentera,Mookgopong,Municipality of San Francisco,San Francisco,San Francisco (Anao-Aon),Town of Delhi"}', '{"pos": 0, "parents": [18]}', '2025-10-22 20:36:53'),
	(138, 'Yelp_1', 'yelp_1/boot.html', NULL, NULL, NULL, NULL, NULL, '2025-11-30 21:41:03'),
	(139, 'Extract_contact', 'extract_contact/boot.html', '"{}"', '"{\\"emails_value\\":\\"text\\",\\"phoneNumbers_value\\":\\"text\\"}"', '"{}"', '"{}"', '{"pos": 0, "parents": [138]}', '2025-12-01 20:25:46'),
	(140, 'Realtime_news', 'realtime_news/boot.html', NULL, NULL, NULL, NULL, NULL, '2025-12-01 20:57:57'),
	(143, 'Appsthink_crm', 'appsthink_crm/boot.html', NULL, NULL, NULL, NULL, NULL, '2025-12-03 19:48:57'),
	(145, 'Crimping_process', 'crimping_process/boot.html', NULL, NULL, NULL, NULL, '{"pos": 0, "parents": [146]}', '2025-12-13 11:08:11'),
	(146, 'Purchase_requisition', 'purchase_requisition/boot.html', '"{\\"storage_location\\":\\"Gigafile Uploader\\"}"', '"{\\"material_name\\":\\"text\\",\\"quantity\\":\\"text\\",\\"storage_location\\":\\"text\\",\\"requisitioner\\":\\"text\\",\\"department\\":\\"text\\"}"', '"{\\"storage_location\\":true}"', '"{}"', NULL, '2025-12-14 16:51:54');

-- Dumping structure for table spicykik.news1
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

-- Dumping data for table spicykik.news1: ~0 rows (approximately)
REPLACE INTO `news1` (`id`, `author`, `title`, `description`, `url`, `source`, `image`, `category`, `language`, `country`, `published_at`, `role`, `created_at`) VALUES
	(1, 'hgnd', 'boj', 'hi k', 'oikn', 'pn', 'buoj', 'okn', 'b oj', 'poj', 'hrdf', 'admin', '2025-10-22 19:24:11');

-- Dumping structure for table spicykik.purchase_requisition
CREATE TABLE IF NOT EXISTS `purchase_requisition` (
  `id` int NOT NULL AUTO_INCREMENT,
  `material_name` text COLLATE utf8mb4_unicode_ci,
  `quantity` text COLLATE utf8mb4_unicode_ci,
  `storage_location` text COLLATE utf8mb4_unicode_ci,
  `requisitioner` text COLLATE utf8mb4_unicode_ci,
  `department` text COLLATE utf8mb4_unicode_ci,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spicykik.purchase_requisition: ~1 rows (approximately)
REPLACE INTO `purchase_requisition` (`id`, `material_name`, `quantity`, `storage_location`, `requisitioner`, `department`, `role`, `created_at`) VALUES
	(1, 'steel tube', '5', 'delhi', 'shivam', 'material mgmnt', 'admin', '2025-12-14 16:53:23');

-- Dumping structure for table spicykik.realtime_news
CREATE TABLE IF NOT EXISTS `realtime_news` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8mb4_unicode_ci,
  `url` text COLLATE utf8mb4_unicode_ci,
  `snippet` text COLLATE utf8mb4_unicode_ci,
  `photo` text COLLATE utf8mb4_unicode_ci,
  `thumbnail` text COLLATE utf8mb4_unicode_ci,
  `published_datetime_utc` text COLLATE utf8mb4_unicode_ci,
  `authors_0_` text COLLATE utf8mb4_unicode_ci,
  `source_url` text COLLATE utf8mb4_unicode_ci,
  `source_name` text COLLATE utf8mb4_unicode_ci,
  `source_logo_url` text COLLATE utf8mb4_unicode_ci,
  `source_favicon_url` text COLLATE utf8mb4_unicode_ci,
  `source_publication_id` text COLLATE utf8mb4_unicode_ci,
  `related_topics_0__topic_id` text COLLATE utf8mb4_unicode_ci,
  `related_topics_0__topic_name` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_0__article_id` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_0__title` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_0__link` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_0__photo_url` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_0__thumbnail_url` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_0__published_datetime_utc` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_0__authors` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_0__source_url` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_0__source_name` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_0__source_logo_url` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_0__source_favicon_url` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_0__source_publication_id` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_0__related_topics` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__article_id` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__title` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__link` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__photo_url` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__thumbnail_url` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__published_datetime_utc` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__authors_0_` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__source_url` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__source_name` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__source_logo_url` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__source_favicon_url` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__source_publication_id` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__related_topics_0__topic_id` text COLLATE utf8mb4_unicode_ci,
  `sub_articles_1__related_topics_0__topic_name` text COLLATE utf8mb4_unicode_ci,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spicykik.realtime_news: ~25 rows (approximately)
REPLACE INTO `realtime_news` (`id`, `title`, `url`, `snippet`, `photo`, `thumbnail`, `published_datetime_utc`, `authors_0_`, `source_url`, `source_name`, `source_logo_url`, `source_favicon_url`, `source_publication_id`, `related_topics_0__topic_id`, `related_topics_0__topic_name`, `sub_articles_0__article_id`, `sub_articles_0__title`, `sub_articles_0__link`, `sub_articles_0__photo_url`, `sub_articles_0__thumbnail_url`, `sub_articles_0__published_datetime_utc`, `sub_articles_0__authors`, `sub_articles_0__source_url`, `sub_articles_0__source_name`, `sub_articles_0__source_logo_url`, `sub_articles_0__source_favicon_url`, `sub_articles_0__source_publication_id`, `sub_articles_0__related_topics`, `sub_articles_1__article_id`, `sub_articles_1__title`, `sub_articles_1__link`, `sub_articles_1__photo_url`, `sub_articles_1__thumbnail_url`, `sub_articles_1__published_datetime_utc`, `sub_articles_1__authors_0_`, `sub_articles_1__source_url`, `sub_articles_1__source_name`, `sub_articles_1__source_logo_url`, `sub_articles_1__source_favicon_url`, `sub_articles_1__source_publication_id`, `sub_articles_1__related_topics_0__topic_id`, `sub_articles_1__related_topics_0__topic_name`, `role`, `created_at`) VALUES
	(1, 'Texas A&M drops, Ohio State solidifies No. 1 status in latest AP Top 25 college football rankings', 'https://www.cbssports.com/college-football/news/ap-top-25-college-football-rankings-texas-a-m-ohio-state-week-15/', 'The formerly top-three Aggies dropped four spots to No. 7 in the latest AP Top 25 poll following their 27-17 rivalry week loss to Texas. It was ...', 'https://sportshub.cbsistatic.com/i/r/2025/12/01/4c727db4-7c36-4ee4-9f2b-c6236135c524/thumbnail/640x360/b64057e6158b60a9c70d90182a1a9300/gettyimages-2249107491-1920x1080.jpg', 'https://news.google.com/api/attachments/CC8iK0NnNWFNRmd6ZGxaeVltZExUa3huVFJEb0FoaUFCU2dLTWdhbEZvSUxsZ28=-w200-h200-p-df-rw', '2025-11-30T19:08:00.000Z', 'Will Backus', 'https://www.cbssports.com', 'CBS Sports', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.cbssports.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.cbssports.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqKAgKIiJDQklTRXdnTWFnOEtEV05pYzNOd2IzSjBjeTVqYjIwb0FBUAE', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZ0ZHpCNmEyeDVhQklDWlc0b0FBUAE', 'Will Backus', 'CBMisgFBVV95cUxPeXNvbUczalVEcmk1bGM4Wm5PY2tiUlNzVE1mZHpvRXIwd19oaURTTEIzcGpKRnVCSWU3NUhVS0pzYkREbHhlOE9ZeDFzMGpsQzhPbVJzSzhqM0NHa1NuZVhrakpGaG5CbWlnZTdUaFoxcHI2aTQ1dnR1NjB6SllGdmY4NWlQZUh5MXAyVlhJa2dKRUpOUGFaTEU0UHNaUFJ1M2ZHWVFJMHl6XzIxY3puckdn', 'Dawgs, Ducks, Tech climb in poll; A&M drops to 7', 'https://www.espn.com/college-football/story/_/id/47153460/ohio-state-indiana-lead-ap-football-poll-texas-falls-7', 'https://a4.espncdn.com/combiner/i?img=%2Fphoto%2F2025%2F1116%2Fr1576098_1296x729_16%2D9.jpg', 'https://news.google.com/api/attachments/CC8iK0NnNW1Na1ZSUnpoWGNqQjJUalZKVFJDZkF4amlCU2dLTWdZQlFJaHVwQWM=-w200-h200-p-df-rw', '2025-11-30T19:05:00.000Z', '[]', 'https://www.espn.com', 'ESPN', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.espn.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.espn.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqIQgKIhtDQklTRGdnTWFnb0tDR1Z6Y0c0dVkyOXRLQUFQAQ', '[]', 'CBMivwFBVV95cUxQN0dyaUNsN19XWkZmRDRwZVZsQVgtM0ROLVRWX0UzWjlMTXJGbjljWDJPbDNQSHdqcHZpbThDT0RlOGtfNGtnQm8yZTVPR0NRelpCTmliTzlQNkdBLXVaOGhyajFnTTVvM1daMktMN1IwOTF0alRJVEVpamtUTjlNbVYxdmZWZ0FYMFFNT2ZlcnVyUUh5TzlFZW5ZNHlQTVZBXzh1TzQ1VnljU1ZHVHA4QUdnelRJUXR5WjdmRzAwRQ', 'College Football Rankings: Joel Klatt releases updated Top 15 with top five shakeup after Week 14', 'https://www.on3.com/news/college-football-rankings-joel-klatt-releases-updated-top-15-with-top-five-shakeup-after-week-14/', 'https://on3static.com/uploads/dev/assets/cms/2024/12/21133239/Joel-Klatt.jpg', 'https://news.google.com/api/attachments/CC8iK0NnNUdSelpQV0ZVM05teHVOVVIwVFJDSEF4aVBCaWdLTWdZSlNvamtLZ2M=-w200-h200-p-df-rw', '2025-12-01T19:08:40.000Z', 'Steve Samra', 'https://www.on3.com', 'On3', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.on3.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.on3.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqIAgKIhpDQklTRFFnTWFna0tCMjl1TXk1amIyMG9BQVAB', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEYyY3pRMWFEbG5haElDWlc0b0FBUAE', 'Steve Samra', 'admin', '2025-12-01 20:58:03'),
	(2, 'Rodney Harrison’s ‘Sunday Night Football’ freeze-up sparks concern from fans', 'https://nypost.com/2025/12/01/sports/rodney-harrisons-sunday-night-football-freeze-up-sparks-concern/', 'NBC Sports analyst Rodney Harrison raised eyebrows when he appeared to lose his train of thought in the pregame show ahead of the “Sunday ...', 'https://nypost.com/wp-content/uploads/sites/2/2025/12/newspress-collage-ua0nlzbac-1764597772314.jpg?quality=75&strip=all&1764579838&w=1200', 'https://news.google.com/api/attachments/CC8iK0NnNTZXa2hKUmtkeGRUbDZiVnBGVFJERUF4aW5CU2dLTWdhbFZZenNLUWM=-w200-h200-p-df-rw', '2025-12-01T14:16:00.000Z', 'Jenna Lemoncelli', 'https://nypost.com', 'New York Post', 'https://encrypted-tbn3.gstatic.com/faviconV2?url=https://nypost.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn3.gstatic.com/faviconV2?url=https://nypost.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqJAgKIh5DQklTRUFnTWFnd0tDbTU1Y0c5emRDNWpiMjBvQUFQAQ', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZuZUhCMmVuRjVOaElDWlc0b0FBUAE', 'Jenna Lemoncelli', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:04'),
	(3, 'Florida football coach Jon Sumrall press conference live updates, highlights', 'https://www.gainesville.com/story/sports/college/football/2025/12/01/florida-football-coach-jon-sumrall-press-conference-live-updates-highlights/87544138007/', 'Florida football hired Jon Sumrall as its 31st coach in school history on Nov. 30. Here are updates from his introductory press conference.', 'https://www.gainesville.com/gcdn/authoring/authoring-images/2025/12/01/NTGS/87553558007-gai-sumrallands-83733.JPEG?crop=2644,1487,x0,y0&width=660&height=371&format=pjpg&auto=webp', 'https://news.google.com/api/attachments/CC8iI0NnNUZabEF5VkRsSlRYZFBhakJYVFJEekFoaVVCU2dLTWdB=-w200-h200-p-df-rw', '2025-12-01T20:20:13.000Z', '', 'https://www.gainesville.com', 'Gainesville Sun', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.gainesville.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.gainesville.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqKggKIiRDQklTRlFnTWFoRUtEMmRoYVc1bGMzWnBiR3hsTG1OdmJTZ0FQAQ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:05'),
	(4, 'Fantasy Football Waiver Wire Week 14: Adonai Mitchell and Dontayvion Wicks spike weeks', 'https://www.nbcsports.com/fantasy/football/news/fantasy-football-waiver-wire-week-14-adonai-mitchell-and-dontayvion-wicks-spike-weeks', 'The Jets and Packers have both needed a receiver to step up in recent weeks. Adonai Mitchell and Dontayvion Wicks answered the call in Week ...', 'https://nbcsports.brightspotcdn.com/dims4/default/fe441a2/2147483647/strip/false/crop/3600x2025+0+0/resize/1200x675!/quality/90/?url=https%3A%2F%2Fnbc-sports-production-nbc-sports.s3.us-east-1.amazonaws.com%2Fbrightspot%2F02%2F2a%2F7533388448ca88ad0de9c9724af5%2Fhttps-api-imagn.com%2Frest%2Fdownload%2FimageID%3D27711062', 'https://news.google.com/api/attachments/CC8iK0NnNVhlbmhRYzBWWFFtZGpibEo0VFJDZkF4ampCU2dLTWdhZEI1QktEZ3M=-w200-h200-p-df-rw', '2025-12-01T16:57:21.000Z', 'Kyle Dvorchak', 'https://www.nbcsports.com', 'NBC Sports', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.nbcsports.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.nbcsports.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqKAgKIiJDQklTRXdnTWFnOEtEVzVpWTNOd2IzSjBjeTVqYjIwb0FBUAE', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZuWjI1cWFHTmpjQklDWlc0b0FBUAE', 'Kyle Dvorchak', 'CBMiuAFBVV95cUxPZUo3UjdKMnpvS0RlbkFNM0tHeVd2VFFoQUZpem4wZUNYNzI3ZTlfSVBkOThvcWpaM3d2UFE5Q3FoeE9SbE9ZbjVMWW8tT3JDWmFVR0ZuLUljY1h3aWJvc2lodXZGMXdEaXJBbmR1Mi1DaEw5WW9Ja0hGNHNReE5iUjF5UzA0MFdJc2pnWnpwX2ZSS3oxTGFOTWxEaDZlY2EyOWxvYjVOQWhla084bm94dHo4dlJPcXF5', 'Fantasy football free agents: Kyle Monangai and Bam Knight are essential pickups', 'https://www.espn.com/fantasy/football/story/_/id/47164184/fantasy-football-free-agent-pickups-waiver-wire-nfl-week-14', 'https://a.espncdn.com/photo/2025/1201/r1583332_1296x729_16-9.jpg', 'https://news.google.com/api/attachments/CC8iI0NnNXFOV3RpUkVFNGRsVjNOMHAwVFJDb0FSaXNBaWdCTWdB=-w200-h200-p-df-rw', '2025-12-01T16:33:00.000Z', '', 'https://www.espn.com', 'ESPN', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.espn.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.espn.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqIQgKIhtDQklTRGdnTWFnb0tDR1Z6Y0c0dVkyOXRLQUFQAQ', '', 'CBMitAFBVV95cUxQZENKZnltbHlfSTJKcEZ1azFjMkVxR3NTOVdDellQanVSWEw1dk4tNllKN1dvMWNib1FGOUwtQm50bnpENk91TGpBY296Uk5CZG1VVjRpRkJEYmktOWZWcHZHSF9ocGY2eHk4VXc0RkFaLVFPZFc0ZWc2WWd3UURtSE5kaXhLQWVjUTA0aGEya0VDRS1JVXBKM2g4eEp1WEpQdFdqV2FuUnMxU0JkQjh5cXFVV0fSAboBQVVfeXFMTk4xQjlOVUhkdEFEUnBvOGo2T0NzYkdUd0dTNnp0RWJTMU1LdHlKajJtZm50TnBfQnk0eHRVdEJFYjlETE4wWTkwczU1U2Njb2k2MXUzOThPWHRJX2M5SEFtYWFhYjg4OXZ5S2h5YkZmeTU4aFIxWUY2VmYwNFJaM01rdF9xaC1sVjRUN3UwZ2NHdXBGWnptekM4THJGUFhTR2NvWHpHSDBpVkNDel9ncm9wbkw4a0lYdDV3', 'Fantasy Football Waiver Wire Advice: Pickups to Target, Stash & Drop (Week 14)', 'https://www.fantasypros.com/2025/12/fantasy-football-waiver-wire-advice-pickups-to-target-stash-drop-week-14-adds/', 'https://cdn.fantasypros.com/wp-content/images/waiver_wire_week_14_fantasy_football/1470x650.jpg', 'https://news.google.com/api/attachments/CC8iK0NnNWFaQzFrU1RkTVV6bGtNaTA0VFJEd0FoakJCaWdLTWdhWk1vNlFGUWs=-w200-h200-p-df-rw', '2025-12-01T14:26:15.000Z', 'Pat Fitzmaurice', 'https://www.fantasypros.com', 'FantasyPros', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.fantasypros.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.fantasypros.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqKggKIiRDQklTRlFnTWFoRUtEMlpoYm5SaGMzbHdjbTl6TG1OdmJTZ0FQAQ', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZtTUhseWJtWXpjaElDWlc0b0FBUAE', 'Pat Fitzmaurice', 'admin', '2025-12-01 20:58:06'),
	(5, 'Report: Nick Marsh to enter transfer portal after MSU football change', 'https://www.freep.com/story/sports/college/michigan-state/spartans/2025/12/01/nick-marsh-transfer-portal-michigan-state-football/87554736007/', 'Nick Marsh, from River Rouge, led Michigan State Spartans with 59 catches for 662 yards, 6 TDs in 2025. MSU expected to hire Pat Fitzgerald ...', 'https://www.freep.com/gcdn/authoring/authoring-images/2025/11/30/PDTF/87534286007-20251129-msu-v-maryland-1-fg-087.jpg?crop=5999,3375,x0,y312&width=660&height=371&format=pjpg&auto=webp', 'https://news.google.com/api/attachments/CC8iK0NnNVZXVkZrTFVONU1HdG5ZbnA2VFJDb0FSaXNBaWdCTWdhZE5wZ3JuZ2s=-w200-h200-p-df-rw', '2025-12-01T20:00:19.000Z', 'Chris Solari', 'https://www.freep.com', 'Detroit Free Press', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.freep.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.freep.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqIggKIhxDQklTRHdnTWFnc0tDV1p5WldWd0xtTnZiU2dBUAE', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZuWW1jM2JtNXhNQklDWlc0b0FBUAE', 'Chris Solari', 'CBMi2gFBVV95cUxQMklCUHM0OHBoeW1qaVpoWG1HOWxpUUFXbnN5UWhpdGFDUVpjVmNCUXpsSEJGd0F2d0pYVHljZGVCTzJSUmNSYjZoVXhvY3dfTUlVeW9ndkxJZnhNQ0hmN0pBOERvZThUUjRFVGFUZGxpMzlFN05EQ24yM0V3NjRHOXNIUWI4azc5WFpETTRNVGdxNnpPcGhGQ0JnX0licm5EMENpZ0hsRjBlUlZYOV90NFZsSkVVbjBVMmlzOUFEWGVVekVjMmZ2VU5HcnhRdEtULXNRdUtYOXBkUQ', 'Pat Fitzgerald has baggage, but he could be perfect for Michigan State', 'https://www.freep.com/story/sports/columnists/shawn-windsor/2025/12/01/michigan-state-football-and-pat-fitzgerald-need-each-other/87543679007/', 'https://www.freep.com/gcdn/authoring/images/smg/2025/02/01/SOSU/78124895007-32-62203.jpeg?crop=5999,3376,x0,y205&width=660&height=371&format=pjpg&auto=webp', 'https://news.google.com/api/attachments/CC8iK0NnNXVlRXBmYlRCWmJVeG5lVXB2VFJEekFoaVVCU2dLTWdZQkVaanVtQWs=-w200-h200-p-df-rw', '2025-12-01T10:12:32.000Z', '', 'https://www.freep.com', 'Detroit Free Press', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.freep.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.freep.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqIggKIhxDQklTRHdnTWFnc0tDV1p5WldWd0xtTnZiU2dBUAE', '', 'CBMigwJBVV95cUxQdVFld3JPendVa21XMXNtNmhGdl9nNUlNWUpXZ2NHT3ZmbXFLb0FYd0FoSHR1Vng2WkgzMUNvVXpTdG44VkZOaTViX2FnVHNhMXRNU19vVmk5RzNycVBpTmROUzJZcDFrcTczelV0VS1oUTlacnc4dGdNQ1RvSVNHcWpuZ2laUElZbklLYUptMWtsR1A5UnAxdkVXTnV3b3dpT1Facmd2eXMtNkxUN2Z0dkZUSV9pM1ZaQnNfU2J3UnRudzJrZU9HeE84NTljWEZ5U2VHdFNiQW1ZTTRtSXpDTFJJYkhmR016MlgyakFGNDZJbW1ESnhGaU91cjc3ZWFDaEZr', 'Tom Izzo previews Iowa, gives thoughts on MSU football coaching change', 'https://spartanswire.usatoday.com/story/sports/college/spartans/mens-basketball/2025/12/01/tom-izzo-previews-iowa-gives-thoughts-on-msu-football-coaching-change/87554760007/', 'https://spartanswire.usatoday.com/gcdn/authoring/authoring-images/2025/10/25/SMSU/86907345007-1052610730.jpg?crop=5471,3079,x0,y284&width=660&height=371&format=pjpg&auto=webp', 'https://news.google.com/api/attachments/CC8iK0NnNURXVmxpUzNoeFNtbzJaVWRuVFJDb0FSaXNBaWdCTWdhdE5KaVBIUWs=-w200-h200-p-df-rw', '2025-12-01T20:12:10.000Z', 'Cory Linsner', 'https://spartanswire.usatoday.com', 'Spartans Wire', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://spartanswire.usatoday.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://spartanswire.usatoday.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqOAgKIjJDQklTSHdnTWFoc0tHWE53WVhKMFlXNXpkMmx5WlM1MWMyRjBiMlJoZVM1amIyMG9BQVAB', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZ6WkhZelpERTBhaElDWlc0b0FBUAE', 'Cory Linsner', 'admin', '2025-12-01 20:58:07'),
	(6, 'Stoops fired after 13 years at UK, owed $37.7M', 'https://www.espn.com/college-football/story/_/id/47159529/kentucky-expected-fire-football-coach-mark-stoops', 'Kentucky has fired football coach Mark Stoops, who had just completed his 13th season with a 5-7 record.', 'https://a.espncdn.com/photo/2025/1201/r1583137_1296x729_16-9.jpg', 'https://news.google.com/api/attachments/CC8iK0NnNWxOMmhUVTNkdWRrRk9kMmgzVFJDZkF4ampCU2dLTWdZQkFLS0hsZ28=-w200-h200-p-df-rw', '2025-12-01T03:09:00.000Z', 'Pete Thamel', 'https://www.espn.com', 'ESPN', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.espn.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.espn.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqIQgKIhtDQklTRGdnTWFnb0tDR1Z6Y0c0dVkyOXRLQUFQAQ', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZtWmpGM1l6RnpOQklDWlc0b0FBUAE', 'Pete Thamel', 'CBMitAFBVV95cUxPTTRnLVphS01odHhOVGFXaTFhdnJCZFVwWlQ1a1FUUi1tMllTdk4zbU5RQklJcmtJVGFUMm9aQVlDVUlpQUVBUkN4Uk0wQTZLX3FQWGZhYjZfb1EtVUJybThqRUtIZm5lOWtySWN5cHZyb3I2c3pDZV80bERleGJKZ3h4aVgxYmI3dnpBczZ5Q3pvbUxYaWhyMnA0Tjk5aERYZ09XOEthTTl5dGhIV3Vrc1o0SVnSAboBQVVfeXFMTkJBbDNPZ2V1TkdneXpTZlBJNE5MMWZrX0U5WEp3VFNmSEhHTXBUZGdGRjE0bndLblpCR3FLLTAyOHZhRVNtVm5yMk1uemhkeWZXT0p0enF4VGtvY0MtMW16VW1aV3BYLVBZUGpSajBVUXZsZXo0OEVtdy1IaExNWm5QNGpiQUNMdlJDYjU5SzNjVzFSQVdHNUswT2R6TGVBZW9fczVCTkZQSmhSeTlZcktMUm55cUpMMFhn', 'Kentucky fires head football coach Mark Stoops after he made bold statement: reports', 'https://www.foxnews.com/sports/kentucky-fires-head-football-coach-mark-stoops-after-he-made-bold-statement-reports', 'https://a57.foxnews.com/static.foxnews.com/foxnews.com/content/uploads/2025/12/1280/720/kentucky-mark-stoops-120125-3.jpg?ve=1&tl=1', 'https://news.google.com/api/attachments/CC8iK0NnNVBOMGhWTjNjNVowVXhkbmxhVFJDZkF4ampCU2dLTWdZdE5aYXVJUWc=-w200-h200-p-df-rw', '2025-12-01T14:29:49.000Z', '', 'https://www.foxnews.com', 'Fox News', 'https://encrypted-tbn3.gstatic.com/faviconV2?url=https://www.foxnews.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn3.gstatic.com/faviconV2?url=https://www.foxnews.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqJQgKIh9DQklTRVFnTWFnMEtDMlp2ZUc1bGQzTXVZMjl0S0FBUAE', '', 'CBMi6gFBVV95cUxQQUZjc3pSbm5zR2hqR0k2YmEycjVlaEpqOFRNSmdtQTlicnNhd3M3YkpCMWlKNDFhWjBLZjB0SkxOYnROYjB0VWF2MEl4LUE3Q1VheTlnb0NKa0hsa2Q0WEd0djNDLWQyZjlJLWdTQkphRTRESW5NYmRvRkN6MlRWT3NoWHVFWUVRY1VTOUFTTWl3dDNob0FkUkRZd1o0aGxQQTlmc051UkFhSDI0dkVJSzFXT2JLd2htOUhCYlNjSktPeXpEdG4wT3ZaOWswYkdCTFktM2phOV81NE8xYk4ycDVfX3BzTzk4ZFE', 'The 3 likeliest candidates to become next Kentucky Football head coach', 'https://www.aseaofblue.com/kentucky-wildcats-news/155065/3-likeliest-candidates-to-become-next-uk-football-head-coach-brian-hartline-dan-mullen-will-stein', 'https://platform.aseaofblue.com/wp-content/uploads/sites/13/2025/12/imagn-22955141.jpg?quality=90&strip=all&crop=0.0083696016069652%2C0%2C99.983260796786%2C100&w=2400', 'https://news.google.com/api/attachments/CC8iI0NnNXhkM3A2YjBGTFh5MWZNbWMzVFJER0F4aWpCU2dLTWdB=-w200-h200-p-df-rw', '2025-12-01T17:55:03.000Z', '', 'https://www.aseaofblue.com', 'A Sea Of Blue', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.aseaofblue.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.aseaofblue.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqKQgKIiNDQklTRkFnTWFoQUtEbUZ6WldGdlptSnNkV1V1WTI5dEtBQVAB', '', '', 'admin', '2025-12-01 20:58:08'),
	(7, 'College Football Power Rankings: Miami, Texas in focus as College Football Playoff debates grow louder', 'https://www.cbssports.com/college-football/news/college-football-power-rankings-miami-texas-college-football-playoff-debates/', 'The Hurricanes and Longhorns are staring down a nerve-racking weekend as CFP selection looms.', 'https://sportshub.cbsistatic.com/i/r/2025/11/30/37c69637-abae-40ce-aca8-a2acda56fd73/thumbnail/640x360/e6d71e6ebfc7cf5138abfa177cbbddf0/gettyimages-2248384520-1920x1080.jpg', 'https://news.google.com/api/attachments/CC8iK0NnNHlkVGxJYTJOT1Uzb3hTMUZtVFJEb0FoaUFCU2dLTWdaQklaQzFtQWc=-w200-h200-p-df-rw', '2025-12-01T18:00:57.000Z', 'Brandon Marcello', 'https://www.cbssports.com', 'CBS Sports', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.cbssports.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.cbssports.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqKAgKIiJDQklTRXdnTWFnOEtEV05pYzNOd2IzSjBjeTVqYjIwb0FBUAE', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZvWW5RMmNXWndlaElDWlc0b0FBUAE', 'Brandon Marcello', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:09'),
	(8, 'College Football Playoff Bracket Projections and Predictions after Week 14', 'https://bleacherreport.com/articles/25309552-college-football-playoff-bracket-projections-and-predictions-after-week-14', 'Although the 2025 regular season is over, debates surrounding the College Football Playoff are only heating up.', 'https://gsp-image-cdn.wmsports.io/cms/prod/bleacher-report/2025-11/national_cfb_playoffbracket_16x9-(7).png', 'https://news.google.com/api/attachments/CC8iMkNnNXdiWFJNVmtveFdWZHBUWEpTVFJDZkF4ampCU2dLTWdzSkVZaEhFZXFaREVvUll3=-w200-h200-p-df-rw', '2025-12-01T12:18:15.000Z', '', 'https://bleacherreport.com', 'Bleacher Report', 'https://encrypted-tbn3.gstatic.com/faviconV2?url=https://bleacherreport.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn3.gstatic.com/faviconV2?url=https://bleacherreport.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqLggKIihDQklTR0FnTWFoUUtFbUpzWldGamFHVnljbVZ3YjNKMExtTnZiU2dBUAE', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:10'),
	(9, 'The Vibe Around the UNC Football Program', 'https://247sports.com/college/north-carolina/article/north-carolina-unc-tar-heels-football-coach-bill-belichick-season-one-4-8-record-2-6-acc-fired-staff-changes-return-264107045/', 'Year one of the Bill Belichick experiment at UNC is over. So, what\'s next for the program and school? Here is the TarHeel247 report after gathering intel ...', 'https://s3media.247sports.com/Uploads/Assets/627/458/13458627.jpg?width=1200&height=628&crop=1.91:1&fit=cover', 'https://news.google.com/api/attachments/CC8iK0NnNU9lWFJoWnpsV09XZERaelZmVFJDUkF4ajlCU2dLTWdZcFlvb3RxUWM=-w200-h200-p-df-rw', '2025-12-01T06:39:21.000Z', 'Andrew Jones', 'https://247sports.com', '247Sports', 'https://encrypted-tbn3.gstatic.com/faviconV2?url=https://247sports.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn3.gstatic.com/faviconV2?url=https://247sports.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqKAgKIiJDQklTRXdnTWFnOEtEVEkwTjNOd2IzSjBjeTVqYjIwb0FBUAE', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZ6YlhReWVYRTFOQklDWlc0b0FBUAE', 'Andrew Jones', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:11'),
	(10, 'Week 15 Bowl Projections & College Football Playoff Predictions', 'https://collegefootballnews.com/college-football/bowl-projections-college-football-playoff-predictions-week-15-2025', 'Before the conference championships happen, what are the latest bowl projections and College Football Playoff predictions for Week 15?', 'https://collegefootballnews.com/.image/ar_16:9%2Cc_fill%2Ccs_srgb%2Cfl_progressive%2Cq_auto:good%2Cw_1200/MjE5NTUxNDI5MTQzMTEwNzI2/usatsi_27701781.jpg', 'https://news.google.com/api/attachments/CC8iL0NnNW5XREJ4TWw5Mk1qTmhMVTFmVFJDZkF4ampCU2dLTWdrQklJWnZHQ1NMTWdF=-w200-h200-p-df-rw', '2025-12-01T08:29:56.000Z', 'Pete Fiutak', 'https://collegefootballnews.com', 'College Football News', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://collegefootballnews.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://collegefootballnews.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqNQgKIi9DQklTSFFnTWFoa0tGMk52Ykd4bFoyVm1iMjkwWW1Gc2JHNWxkM011WTI5dEtBQVAB', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZuWW1wbU1YUmpZaElDWlc0b0FBUAE', 'Pete Fiutak', 'CBMiuAFBVV95cUxPdmo2eTA2OWNNMllrV1czUjV1d2tYS3hRTFhmVjdHbmNJSDQwdTZOWTNzMlQ3dGlUcWZUeTF3bkR1LWRPY1l5VGRTR1QwcDUxWnYwaXMyU09CR25uRXg2QS1RUU02ZWVESzBsQUJ3Q2V0VUdmRm02TW1vcE1PSlF0V1VDcVZSX0RsdlB0ektObHFBcGpSMmR3YUR2ZU9MMXdxcWJLLXR0UVZRaHJQTjBXbWlmd2VQX0ln', 'Predicting every postseason matchup as regular season ends', 'https://www.espn.com/college-football/story/_/id/47150538/college-football-playoff-bowl-projections-following-week-14', 'https://a4.espncdn.com/combiner/i?img=%2Fphoto%2F2025%2F1130%2Fr1582824_1296x729_16%2D9.jpg', 'https://news.google.com/api/attachments/CC8iK0NnNHlheTE1UVhOSldVbEZaVXhGVFJDZkF4ampCU2dLTWdZQk1vZ1JtUWM=-w200-h200-p-df-rw', '2025-11-30T18:10:00.000Z', '', 'https://www.espn.com', 'ESPN', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.espn.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.espn.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqIQgKIhtDQklTRGdnTWFnb0tDR1Z6Y0c0dVkyOXRLQUFQAQ', '', 'CBMitgFBVV95cUxOY2MtaFdzdUNmNnV3TkhWN3I2MFNpdUhWT01mekNaT1lZR3Q0LUtFWTZlQThjLURMdXVYMzl1M1dtOTdXVzRvWGRCYUNvTDlxZnROZHZFSnA4MGNRTnFIeFc3TC1vOW81WWltbjVBamtvRmNPSmgzckRCZ1B3NDNUTTZKZDZlVnM2S19oZGx1M2ZhaW1KSnBIb1RrZm9VTjRhMDExWmVuazRkb0FScDAtZDEzRkg2UQ', 'Weekly Michigan football bowl projections following loss to Ohio State', 'https://www.si.com/college/michigan/football/weekly-michigan-football-bowl-projections-following-loss-to-ohio-state', 'https://images2.minutemediacdn.com/image/upload/c_crop,w_4500,h_2531,x_0,y_200/c_fill,w_720,ar_16:9,f_auto,q_auto,g_auto/images/ImagnImages/mmsport/wolverine_digest/01kbdpa4sxz5q5v365fh.jpg', 'https://news.google.com/api/attachments/CC8iJ0NnNXVVRmxOV0RoNVRGaEdUVUpqVFJDVkF4alFCU2dLTWdNQkFCWQ=-w200-h200-p-df-rw', '2025-12-01T20:00:00.000Z', '', 'https://www.si.com', 'Sports Illustrated', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.si.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.si.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqHggKIhhDQklTREFnTWFnZ0tCbk5wTG1OdmJTZ0FQAQ', '', '', 'admin', '2025-12-01 20:58:12'),
	(11, 'College Football Brasil to Make History as First College Football Game Played in South America', 'https://theacc.com/news/2025/12/1/college-football-brasil-to-make-history-as-first-college-football-game-played-in-south-america.aspx', 'Los Angeles (December 1, 2025) — College Football Brasil will make history as the first-ever FBS college football game played in South America.', 'http://theacc.com/images/2025/12/1/_1600_900_PR_X_LinkedIn_Logo_WITH_Helmets_on_Background_Banner_1_.png?preset=large.storyimage', 'https://news.google.com/api/attachments/CC8iK0NnNXhhRzFvUzNWVVZYWTFlRkpsVFJDdEF4akxCU2dLTWdZVlU0N01vUWM=-w200-h200-p-df-rw', '2025-12-01T13:13:05.000Z', '', 'https://theacc.com', 'Atlantic Coast Conference', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://theacc.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://theacc.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqJAgKIh5DQklTRUFnTWFnd0tDblJvWldGall5NWpiMjBvQUFQAQ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:14'),
	(12, 'Lane Kiffin introduced as LSU football head coach — Live updates', 'https://www.theadvertiser.com/story/sports/college/lsu/2025/12/01/lane-kiffin-lsu-football-introductory-press-conference/87553417007/', 'LSU football is introducing Lane Kiffin as its next head coach. Follow live updates for Kiffin\'s first remarks as LSU\'s coach here.', 'https://www.theadvertiser.com/gcdn/authoring/authoring-images/2025/12/01/USAT/87545007007-usatsi-27692611.jpg?crop=4158,2339,x0,y169&width=660&height=371&format=pjpg&auto=webp', 'https://news.google.com/api/attachments/CC8iK0NnNHlabU0zUWxWaWJIRXhRVTlTVFJEekFoaVVCU2dLTWdhbE1Zek5IUWs=-w200-h200-p-df-rw', '2025-12-01T20:32:50.000Z', 'Cory Diaz', 'https://www.theadvertiser.com', 'The Daily Advertiser | Lafayette, Louisiana', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.theadvertiser.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.theadvertiser.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqLQgKIidDQklTRndnTWFoTUtFWFJvWldGa2RtVnlkR2x6WlhJdVkyOXRLQUFQAQ', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZxTVhoNmVIazNOeElDWlc0b0FBUAE', 'Cory Diaz', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:15'),
	(13, 'College Football Playoff bracket, based on the current committee rankings', 'https://www.ncaa.com/news/football/article/2025-11-28/college-football-playoff-bracket-based-current-committee-rankings', 'Here\'s how the 2025-26 College Football Playoff bracket looks now, using the CFP selection committee\'s top 25 rankings as announced on ...', 'https://www.ncaa.com/_flysystem/public-s3/styles/large_16x9/public-s3/images/2025-11/college-football-playoff-bracket-nov.-25-rankings.jpg?h=d1cb525d&itok=o0GNII6z', 'https://news.google.com/api/attachments/CC8iL0NnNWxjbWhDV1dWWFQxTnFhVlppVFJDZkF4ampCU2dLTWdrQkFZeHJqV3ZCcmdF=-w200-h200-p-df-rw', '2025-12-01T19:05:05.000Z', '', 'https://www.ncaa.com', 'NCAA.com', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.ncaa.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.ncaa.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqIQgKIhtDQklTRGdnTWFnb0tDRzVqWVdFdVkyOXRLQUFQAQ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:16'),
	(14, 'Nebraska football parts ways with defensive coordinator John Butler', 'https://www.1011now.com/2025/12/01/nebraska-football-parts-ways-with-defensive-coordinator-john-butler/', '(KOLN) - Nebraska has parted ways with John Butler after he served one season as defensive coordinator. Head coach Matt Rhule sent a statement ...', 'https://gray-koln-prod.gtv-cdn.com/resizer/v2/GR3S46BZYBGJ5PUQWYPFDK6JB4.jpg?auth=806888b7eda338fc10f2910c1d9d0374ca0e45cdcaee652ec28ad6ea8c40f860&width=1200&height=600&smart=true', 'https://news.google.com/api/attachments/CC8iK0NnNTBOMUZyZUV3d2NXUkliR0pKVFJDSEF4aVBCaWdLTWdheFJJcVBxUVU=-w200-h200-p-df-rw', '2025-12-01T18:02:00.000Z', '', 'https://www.1011now.com', 'KOLN | Nebraska Local News, Weather, Sports | Lincoln, NE', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.1011now.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.1011now.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqJQgKIh9DQklTRVFnTWFnMEtDekV3TVRGdWIzY3VZMjl0S0FBUAE', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:17'),
	(15, 'Oregon State football transfer portal tracker: Who’s in, who’s out for the Beavers?', 'https://www.oregonlive.com/beavers/2025/12/oregon-state-football-transfer-portal-tracker-whos-in-whos-out-for-the-beavers.html', 'The Oregonian/OregonLive has set up a transfer portal tracker for the Beavers. Check back every day and refresh this page for updates on Oregon ...', 'https://www.oregonlive.com/resizer/v2/QZU3H4GDONDQRI6VFAGL2EHCSI.jpg?auth=b7076e71fd72fbca340440b271b80668d5e1422a129e74e5a9c81b75a5c3babb&width=1280&smart=true&quality=90', 'https://news.google.com/api/attachments/CC8iJ0NnNUtXVzgwYTJ0UFFXTXhZM042VFJERUF4aW1CU2dLTWdNQk1BUQ=-w200-h200-p-df-rw', '2025-12-01T16:30:00.000Z', 'Ryan Clarke', 'https://www.oregonlive.com', 'OregonLive.com', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.oregonlive.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.oregonlive.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqKQgKIiNDQklTRkFnTWFoQUtEbTl5WldkdmJteHBkbVV1WTI5dEtBQVAB', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZ1TUdOak56UjJlQklDWlc0b0FBUAE', 'Ryan Clarke', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:18'),
	(16, 'UCLA football hiring Bob Chesney from James Madison as school\'s new coach', 'https://www.usatoday.com/story/sports/ncaaf/bigten/2025/12/01/ucla-bob-chesney-football-coach-james-madison/87534564007/', 'Bob Chesney reportedly will be the new football coach at UCLA after spending two successful seasons with James Madison.', 'https://www.usatoday.com/gcdn/authoring/authoring-images/2025/12/01/USAT/87552775007-2247840310.jpg?crop=3706,2085,x183,y420&width=660&height=371&format=pjpg&auto=webp', 'https://news.google.com/api/attachments/CC8iI0NnNWFXSE5YZHpGUWExRmFiR2Q0VFJDb0FSaXNBaWdCTWdB=-w200-h200-p-df-rw', '2025-12-01T19:30:00.000Z', 'Jordan Mendoza', 'https://www.usatoday.com', 'USA Today', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.usatoday.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.usatoday.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqJggKIiBDQklTRWdnTWFnNEtESFZ6WVhSdlpHRjVMbU52YlNnQVAB', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZ0YUhoNk1EVnJiQklDWlc0b0FBUAE', 'Jordan Mendoza', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:19'),
	(17, 'In a bold move, UCLA is set to hire Bob Chesney as its next head football coach', 'https://www.latimes.com/sports/ucla/story/2025-12-01/ucla-hires-bob-chesney-to-become-its-next-head-football-coach', 'UCLA will hire James Madison coach Bob Chesney on a five-year deal to lead the Bruins. · Chesney brings a 131-51 career record and championships ...', 'https://ca-times.brightspotcdn.com/dims4/default/df8d9db/2147483647/strip/true/crop/3764x2509+0+0/resize/1200x800!/quality/75/?url=https%3A%2F%2Fcalifornia-times-brightspot.s3.amazonaws.com%2Fe8%2F6a%2F176a7da947f8bbcbd78816617fd4%2Fwashington-st-james-madison-football-14321.jpg', 'https://news.google.com/api/attachments/CC8iI0NnNVhUM2hmZEV4S2MxRnZVRmQwVFJERUF4aW1CU2dLTWdB=-w200-h200-p-df-rw', '2025-12-01T19:19:00.000Z', 'Ben Bolch', 'https://www.latimes.com', 'Los Angeles Times', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.latimes.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.latimes.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqJQgKIh9DQklTRVFnTWFnMEtDMnhoZEdsdFpYTXVZMjl0S0FBUAE', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZtYUhOeGVqYzFPUklDWlc0b0FBUAE', 'Ben Bolch', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:20'),
	(18, 'Ryan Clark Reacts to LSU Football Hiring Lane Kiffin in Unprecedented Decision', 'https://www.si.com/college/lsu/football/ryan-clark-reacts-to-lsu-football-hiring-lane-kiffin-in-unprecedented-decision-01kbdn5afj59', 'Kiffin has made his decision to move to LSU, unprecedented situation unfolding in the Southeastern Conference.', 'https://images2.minutemediacdn.com/image/upload/c_crop,w_8017,h_4509,x_0,y_257/c_fill,w_720,ar_16:9,f_auto,q_auto,g_auto/images/ImagnImages/mmsport/lsu_country/01kbdn9cz297b7t0420p.jpg', 'https://news.google.com/api/attachments/CC8iI0NnNU1UbkJVVEVwMkxWOUxRVnB4VFJDVkF4alFCU2dLTWdB=-w200-h200-p-df-rw', '2025-12-01T19:30:00.000Z', 'Zack Nagy', 'https://www.si.com', 'Sports Illustrated', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.si.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.si.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqHggKIhhDQklTREFnTWFnZ0tCbk5wTG1OdmJTZ0FQAQ', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZ3ZGpSME1YUXlNaElDWlc0b0FBUAE', 'Zack Nagy', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:21'),
	(19, 'College Football Brasil to Make History as First College Football Game Played in South America', 'https://gopack.com/news/2025/12/1/college-football-brasil-to-make-history-as-first-college-football-game-played-in-south-america', 'Los Angeles : NC State Football will open the 2026 season by taking part in the historic College Football Brasil - the first-ever FBS ...', 'https://images.sidearmdev.com/resize?url=https%3A%2F%2Fdxbhsrqyrr690.cloudfront.net%2Fsidearm.nextgen.sites%2Fgopack.com%2Fimages%2F2025%2F11%2F30%2F2.jpeg&width=1600&type=jpeg', 'https://news.google.com/api/attachments/CC8iL0NnNTVXREpwWW1GdGVXUnNOakZmVFJDZkF4ampCU2dLTWdrUmNJaVVyQ1NhVUFF=-w200-h200-p-df-rw', '2025-12-01T13:10:07.000Z', '', 'https://gopack.com', 'GoPack.com', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://gopack.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://gopack.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqJAgKIh5DQklTRUFnTWFnd0tDbWR2Y0dGamF5NWpiMjBvQUFQAQ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:22'),
	(20, 'Week 14 takeaways: A chaotic tiebreaker in the ACC, plus surprising wins', 'https://www.espn.com/college-football/story/_/page/mondaytakeaways120125/2025-college-football-week-14-takeaways-texas-duke', 'Our college football experts break down key storylines and performances from Week 14.', 'https://a.espncdn.com/combiner/i?img=%2Fphoto%2F2025%2F1201%2Fr1583087_1296x729_16%2D9.jpg', 'https://news.google.com/api/attachments/CC8iK0NnNVJjVm81VEhKRlZuVTNjekJLVFJDZkF4ampCU2dLTWdZTkU0aXRrUVU=-w200-h200-p-df-rw', '2025-12-01T12:55:00.000Z', '', 'https://www.espn.com', 'ESPN', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.espn.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.espn.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqIQgKIhtDQklTRGdnTWFnb0tDR1Z6Y0c0dVkyOXRLQUFQAQ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:23'),
	(21, 'Joel Klatt\'s College Football Rankings: OSU Hits Next Level, Analyzing the Bubble', 'https://www.foxsports.com/stories/college-football/joel-klatts-college-football-rankings-osu-hits-next-level-analyzing-bubble', 'Here\'s a full look at Klatt\'s top 15 teams in the nation following Week 14\'s rivalry games, to help answer those questions.', 'https://a57.foxsports.com/statics.foxsports.com/www.foxsports.com/content/uploads/2025/12/1280/1280/ohioh1.jpg?ve=1&tl=1', 'https://news.google.com/api/attachments/CC8iK0NnNDBjRTlVY1RKVFZsUkZlRmswVFJDcUJCaXFCQ2dLTWdhWk00U09uUWM=-w200-h200-p-df-rw', '2025-12-01T16:42:00.000Z', '', 'https://www.foxsports.com', 'FOX Sports', 'https://encrypted-tbn3.gstatic.com/faviconV2?url=https://www.foxsports.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn3.gstatic.com/faviconV2?url=https://www.foxsports.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqKAgKIiJDQklTRXdnTWFnOEtEV1p2ZUhOd2IzSjBjeTVqYjIwb0FBUAE', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:24'),
	(22, 'U.S. Marshals join hunt for fugitive Virginia football coach, warn he may be armed', 'https://www.nbcnews.com/news/us-news/us-marshals-join-hunt-fugitive-virginia-football-coach-warn-may-armed-rcna246782', 'Federal authorities joined the search for a fugitive high school football coach being sought in connection to a child sex abuse image and ...', 'https://media-cldnry.s-nbcnews.com/image/upload/rockcms/2025-11/251124-Travis-L-Turner-gk-8662d5.jpg', 'https://news.google.com/api/attachments/CC8iK0NnNXZNVE14Y1haNVJucE9TbTV2VFJERUF4aW1CU2dLTWdhZFZwQUtLZ2M=-w200-h200-p-df-rw', '2025-12-01T16:28:54.000Z', 'David K. Li', 'https://www.nbcnews.com', 'NBC News', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.nbcnews.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.nbcnews.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqJQgKIh9DQklTRVFnTWFnMEtDMjVpWTI1bGQzTXVZMjl0S0FBUAE', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZ5Ympaa05ISnRkaElDWlc0b0FBUAE', 'David K. Li', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:25'),
	(23, '2025-26 College Football Playoff schedule, dates, TV channel, sites', 'https://www.ncaa.com/news/football/article/2025-11-04/2025-26-college-football-playoff-schedule-dates-tv-channel-sites', 'Here\'s the complete schedule for the College Football Playoff for the 2025-26 season, including game dates, how to watch and teams.', 'https://www.ncaa.com/_flysystem/public-s3/styles/large_16x9/public-s3/images/2023-08/college-football-playoff-logo-field.jpg?h=b69e0e0e&itok=eHUmvcog', 'https://news.google.com/api/attachments/CC8iK0NnNWpiamxNTjNKYWMwdGZRMnhFVFJDZkF4ampCU2dLTWdZaFk1cXRxUWc=-w200-h200-p-df-rw', '2025-12-01T15:54:45.000Z', '', 'https://www.ncaa.com', 'NCAA.com', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.ncaa.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn1.gstatic.com/faviconV2?url=https://www.ncaa.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqIQgKIhtDQklTRGdnTWFnb0tDRzVqWVdFdVkyOXRLQUFQAQ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:26'),
	(24, 'College football Top 25 rankings prediction for US LBM Coaches Poll after Week 14', 'https://www.usatoday.com/story/sports/ncaaf/2025/11/30/college-football-top-25-poll-rankings-prediction-week-14/87533575007/', 'College football Top 25 rankings prediction for US LBM Coaches Poll after Week 14 · 1. Ohio State (12-0) · 2. Indiana (12-0) · 3. Georgia (11-1) · 4 ...', 'https://www.usatoday.com/gcdn/authoring/authoring-images/2025/11/29/USAT/87518982007-usatsi-27696614.jpg?crop=5994,3372,x0,y510&width=660&height=371&format=pjpg&auto=webp', 'https://news.google.com/api/attachments/CC8iK0NnNTJTV2cxYnpoUVRFSnZWVUphVFJEekFoaVVCU2dLTWdZQkFKem9EQXM=-w200-h200-p-df-rw', '2025-12-01T20:39:27.000Z', 'Paul Myerberg', 'https://www.usatoday.com', 'USA Today', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.usatoday.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn0.gstatic.com/faviconV2?url=https://www.usatoday.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqJggKIiBDQklTRWdnTWFnNEtESFZ6WVhSdlpHRjVMbU52YlNnQVAB', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZtYWpSNWJERnVOaElDWlc0b0FBUAE', 'Paul Myerberg', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:27'),
	(25, 'CBS Sports 136: Ohio State finishes regular season as unanimous No. 1 in college football rankings', 'https://www.cbssports.com/college-football/news/college-football-rankings-ohio-state-unanimous-no-1-cbs-sports-136/', 'CBS Sports 136: Ohio State finishes regular season as unanimous No. 1 in college football rankings · Share Video · Biggest movers.', 'https://sportshub.cbsistatic.com/i/r/2025/11/29/11ef58a7-ef90-41ca-a45d-bd256cf59f4d/thumbnail/640x360/b56a0db7a07c7b5172f0b17fcfc07db7/gettyimages-2249080792.jpg', 'https://news.google.com/api/attachments/CC8iK0NnNXNNM2RtYkZneVExZDJUM1Z0VFJEb0FoaUFCU2dLTWdZeEZZaXVsUWs=-w200-h200-p-df-rw', '2025-12-01T15:39:39.000Z', 'Chip Patterson', 'https://www.cbssports.com', 'CBS Sports', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.cbssports.com&client=NEWS_360&size=256&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'https://encrypted-tbn2.gstatic.com/faviconV2?url=https://www.cbssports.com&client=NEWS_360&size=96&type=FAVICON&fallback_opts=TYPE,SIZE,URL', 'CAAqKAgKIiJDQklTRXdnTWFnOEtEV05pYzNOd2IzSjBjeTVqYjIwb0FBUAE', 'CAAqKAgKIiJDQkFTRXdvTkwyY3ZNVEZtTUhONWR6VTJhQklDWlc0b0FBUAE', 'Chip Patterson', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'admin', '2025-12-01 20:58:28');

-- Dumping structure for table spicykik.users
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

-- Dumping data for table spicykik.users: ~3 rows (approximately)
REPLACE INTO `users` (`id`, `email`, `access`, `phone`, `name`, `password`, `loginstatus`, `apikey`, `provider`, `provider_id`, `id_token`, `access_token`, `refresh_token`, `token_expiry`, `last_login`, `avatar`, `role`, `address`, `created_at`, `user_groups`, `user_roles`) VALUES
	(1, 'admin@demo.com', '["Delivery"]', '9829384775', 'Admin', 'demo', 'True', 'rBPCExGq2IJDz48OAleQ', 'local', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'A-120 Fng sec 2', '2021-08-03 13:36:13', '["Administrators","Users","sys admin"]', '["admin","manager","user","linux admin"]'),
	(717, 'user2@demo.com', '["Delivery"]', '9283938288', 'Ashley', 'demo', 'True', 'mA0GI0GRYyneeW2Gqn2X', 'local', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', '112 Kenington lake', '2021-08-09 13:36:13', '["users"]', '["admin","storekeeper"]'),
	(725, 'user@demo.com', '["Delivery"]', '9868787554', 'John Mac', 'demo', 'True', 'uqHZXhvynUDFYnIYPRnu', 'local', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', '22 F Bangalore india', '2021-08-08 13:36:13', '["sys admin"]', '["user"]');

-- Dumping structure for table spicykik.weather1
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

-- Dumping data for table spicykik.weather1: ~39 rows (approximately)
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
	(37, 'Singapore Tuas West Highway', 'https://www.experte.com/online-meeting?join=yvih6v', '103.64083', '14', 'AIRF', 'SG', 'fewfs@safs.com', '08:41', NULL, '1880251', 'Singapore,South Africa', 'vsac', 'https://i.ibb.co/Y4fjmzNk/Whats-App-Image-2025-11-09-at-1-42-44-PM.jpg', '2025-11-12', '201299', 'Yes', 'America', 'admin', '2025-10-22 21:20:59'),
	(38, 'Singapore, Michigan Historical Marker', 'https://www.experte.com/online-meeting?join=oqoi4', '-86.20317', '182', 'PRK', 'US', 'hjdsj@cnsm.com', 'America/Detroit', NULL, '6252001', 'United States', 'Michigan', '4983990', '2025-12-11', '726182', 'Allegan', 'Municipality of San Francisco', 'admin', '2025-10-22 21:21:01'),
	(39, '', 'https://www.experte.com/online-meeting?join=gzzo9', '', '', '', '', 'btfvdsc@afnk.com', '09:12:00', '', '', 'SG,Sierra Leone,Singapore', '', 'ngwojef', '2025-11-07', '23523432', 'Yes', 'Delhi Township', 'admin', '2025-11-22 00:41:25');

-- Dumping structure for table spicykik.yelp_1
CREATE TABLE IF NOT EXISTS `yelp_1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `review` text COLLATE utf8mb4_unicode_ci,
  `text_language` text COLLATE utf8mb4_unicode_ci,
  `review_type` text COLLATE utf8mb4_unicode_ci,
  `reviewCreatedAt` text COLLATE utf8mb4_unicode_ci,
  `rating` text COLLATE utf8mb4_unicode_ci,
  `author_encid` text COLLATE utf8mb4_unicode_ci,
  `author_isFollowedByLoggedInUser` text COLLATE utf8mb4_unicode_ci,
  `author_profilePhoto_encid` text COLLATE utf8mb4_unicode_ci,
  `author_profilePhoto_photoUrl_mediaItemSrcUrl` text COLLATE utf8mb4_unicode_ci,
  `author_displayName` text COLLATE utf8mb4_unicode_ci,
  `author_displayLocation` text COLLATE utf8mb4_unicode_ci,
  `author_currentTruncatedEliteYear` text COLLATE utf8mb4_unicode_ci,
  `author___typename` text COLLATE utf8mb4_unicode_ci,
  `author_reviewCount` text COLLATE utf8mb4_unicode_ci,
  `author_friendCount` text COLLATE utf8mb4_unicode_ci,
  `author_businessPhotoCount` text COLLATE utf8mb4_unicode_ci,
  `review_photos_0_` text COLLATE utf8mb4_unicode_ci,
  `review_photos_1_` text COLLATE utf8mb4_unicode_ci,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spicykik.yelp_1: ~1 rows (approximately)
REPLACE INTO `yelp_1` (`id`, `review`, `text_language`, `review_type`, `reviewCreatedAt`, `rating`, `author_encid`, `author_isFollowedByLoggedInUser`, `author_profilePhoto_encid`, `author_profilePhoto_photoUrl_mediaItemSrcUrl`, `author_displayName`, `author_displayLocation`, `author_currentTruncatedEliteYear`, `author___typename`, `author_reviewCount`, `author_friendCount`, `author_businessPhotoCount`, `review_photos_0_`, `review_photos_1_`, `role`, `created_at`) VALUES
	(1, 'biub', 'ub', 'kboj', 'no', 'boj', 'bi', 'vj', 'nonio', 'vuo', 'bo', 'jni', 'oo', 'bu', 'bo', 'jnoi', 'no', 'ibu', 'b', 'admin', '2025-11-30 21:43:52');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
