-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versione server:              10.3.38-MariaDB - mariadb.org binary distribution
-- S.O. server:                  Win64
-- HeidiSQL Versione:            12.2.0.6576
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dump della struttura di tabella bt_setrep.app
DROP TABLE IF EXISTS `app`;
CREATE TABLE IF NOT EXISTS `app` (
  `id_app` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `app_code` varchar(250) NOT NULL,
  PRIMARY KEY (`id_app`),
  UNIQUE KEY `app_code_unique` (`app_code`),
  KEY `app_code` (`app_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Dump dei dati della tabella bt_setrep.app: ~0 rows (circa)

-- Dump della struttura di tabella bt_setrep.app_sections
DROP TABLE IF EXISTS `app_sections`;
CREATE TABLE IF NOT EXISTS `app_sections` (
  `id_app_sections` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_app` bigint(20) unsigned NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `section_code` varchar(50) NOT NULL,
  PRIMARY KEY (`id_app_sections`),
  UNIQUE KEY `id_app_section_name` (`id_app`,`section_name`),
  UNIQUE KEY `id_app_section_code` (`id_app`,`section_code`),
  KEY `id_app_section` (`id_app_sections`),
  KEY `id_app` (`id_app`),
  KEY `section_code` (`section_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Dump dei dati della tabella bt_setrep.app_sections: ~0 rows (circa)

-- Dump della struttura di tabella bt_setrep.app_sections_settings
DROP TABLE IF EXISTS `app_sections_settings`;
CREATE TABLE IF NOT EXISTS `app_sections_settings` (
  `id_app_sections_settings` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_app_sections` bigint(20) unsigned NOT NULL,
  `setting_name` varchar(50) NOT NULL,
  `setting_code` varchar(50) NOT NULL,
  PRIMARY KEY (`id_app_sections_settings`),
  UNIQUE KEY `id_app_sections_setting_name` (`id_app_sections`,`setting_name`),
  UNIQUE KEY `id_app_sections_setting_code` (`id_app_sections`,`setting_code`),
  KEY `id_app_sections_settings` (`id_app_sections_settings`),
  KEY `setting_code` (`setting_code`),
  KEY `id_app_sections` (`id_app_sections`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Dump dei dati della tabella bt_setrep.app_sections_settings: ~0 rows (circa)

-- Dump della struttura di tabella bt_setrep.app_setting
DROP TABLE IF EXISTS `app_setting`;
CREATE TABLE IF NOT EXISTS `app_setting` (
  `id_app_setting` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_app_sections_settings` bigint(20) unsigned NOT NULL,
  `id_user` bigint(20) unsigned NOT NULL,
  `setting_value` text DEFAULT NULL,
  PRIMARY KEY (`id_app_setting`),
  UNIQUE KEY `id_app_sections_settings_id_user` (`id_app_sections_settings`,`id_user`),
  KEY `id_app_setting` (`id_app_setting`),
  KEY `id_app_sections_settings` (`id_app_sections_settings`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Dump dei dati della tabella bt_setrep.app_setting: ~0 rows (circa)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
