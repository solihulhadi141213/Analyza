-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 24, 2026 at 03:43 PM
-- Server version: 9.1.0
-- PHP Version: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `analyza`
--

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

DROP TABLE IF EXISTS `access`;
CREATE TABLE IF NOT EXISTS `access` (
  `id_access` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_access_group` int DEFAULT NULL,
  `access_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Nomor KTP',
  `access_ihs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Dari satu sehat',
  `access_password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `access_foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `access_client` tinyint(1) NOT NULL COMMENT 'If true, the account is a client.',
  `access_active` tinyint(1) NOT NULL COMMENT 'true or false',
  PRIMARY KEY (`id_access`),
  KEY `id_access_group` (`id_access_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `access_feature`
--

DROP TABLE IF EXISTS `access_feature`;
CREATE TABLE IF NOT EXISTS `access_feature` (
  `id_access_feature` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `feature_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `feature_category` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `feature_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `datetime_creat` timestamp NOT NULL,
  PRIMARY KEY (`id_access_feature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `access_group`
--

DROP TABLE IF EXISTS `access_group`;
CREATE TABLE IF NOT EXISTS `access_group` (
  `id_access_group` int NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_access_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `access_log`
--

DROP TABLE IF EXISTS `access_log`;
CREATE TABLE IF NOT EXISTS `access_log` (
  `id_access_log` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_access` int UNSIGNED NOT NULL,
  `log_datetime` datetime NOT NULL,
  `log_category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_access_log`),
  KEY `access_log_id_access_index` (`id_access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `access_login`
--

DROP TABLE IF EXISTS `access_login`;
CREATE TABLE IF NOT EXISTS `access_login` (
  `id_access_login` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_access` int UNSIGNED NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `datetime_creat` datetime NOT NULL,
  `datetime_expired` datetime NOT NULL,
  PRIMARY KEY (`id_access_login`),
  KEY `access_login_id_access_index` (`id_access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `access_permission`
--

DROP TABLE IF EXISTS `access_permission`;
CREATE TABLE IF NOT EXISTS `access_permission` (
  `id_permission` int NOT NULL AUTO_INCREMENT,
  `id_access` int UNSIGNED NOT NULL,
  `id_access_feature` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id_permission`),
  KEY `id_access` (`id_access`),
  KEY `id_access_feature` (`id_access_feature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `access_reference`
--

DROP TABLE IF EXISTS `access_reference`;
CREATE TABLE IF NOT EXISTS `access_reference` (
  `id_access_reference` int NOT NULL AUTO_INCREMENT,
  `id_access_group` int NOT NULL,
  `id_access_feature` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id_access_reference`),
  KEY `id_access_group` (`id_access_group`),
  KEY `id_access_fitures` (`id_access_feature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `access_reset`
--

DROP TABLE IF EXISTS `access_reset`;
CREATE TABLE IF NOT EXISTS `access_reset` (
  `id_access_reset` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_access` int UNSIGNED NOT NULL,
  `datetime_creat` datetime NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id_access_reset`),
  KEY `reset_to_access` (`id_access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `api_account`
--

DROP TABLE IF EXISTS `api_account`;
CREATE TABLE IF NOT EXISTS `api_account` (
  `id_api_account` int NOT NULL AUTO_INCREMENT,
  `api_name` varchar(255) NOT NULL COMMENT 'Nama Environment',
  `base_url_api` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `created_at` datetime NOT NULL,
  `duration_expired` bigint UNSIGNED NOT NULL COMMENT 'milisecond',
  PRIMARY KEY (`id_api_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `api_token`
--

DROP TABLE IF EXISTS `api_token`;
CREATE TABLE IF NOT EXISTS `api_token` (
  `id_api_token` int NOT NULL AUTO_INCREMENT,
  `id_api_account` int NOT NULL COMMENT 'From api_account',
  `token` text NOT NULL COMMENT 'Hasing',
  `created_at` datetime NOT NULL,
  `expired_at` datetime NOT NULL,
  PRIMARY KEY (`id_api_token`),
  KEY `token_to_account` (`id_api_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_configuration`
--

DROP TABLE IF EXISTS `app_configuration`;
CREATE TABLE IF NOT EXISTS `app_configuration` (
  `id_configuration` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_keyword` json NOT NULL,
  `app_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_favicon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_base_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_year` int NOT NULL,
  `app_company` json NOT NULL,
  PRIMARY KEY (`id_configuration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `captcha`
--

DROP TABLE IF EXISTS `captcha`;
CREATE TABLE IF NOT EXISTS `captcha` (
  `id_captcha` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `captcha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `datetime_creat` datetime NOT NULL,
  `datetime_expired` datetime NOT NULL,
  PRIMARY KEY (`id_captcha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `connection_satu_sehat`
--

DROP TABLE IF EXISTS `connection_satu_sehat`;
CREATE TABLE IF NOT EXISTS `connection_satu_sehat` (
  `id_connection_satu_sehat` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_connection_satu_sehat` varchar(255) NOT NULL COMMENT 'Ex: Development, Staging, Production',
  `url_connection_satu_sehat` varchar(255) NOT NULL COMMENT 'Dari Satu Sehat',
  `organization_id` varchar(255) NOT NULL COMMENT 'Dari Satu Sehat',
  `client_key` varchar(255) NOT NULL COMMENT 'Dari Satu Sehat',
  `secret_key` varchar(255) NOT NULL COMMENT 'Dari Satu Sehat',
  `token` varchar(255) NOT NULL,
  `datetime_expired` datetime DEFAULT NULL,
  `status_connection_satu_sehat` tinyint(1) NOT NULL COMMENT 'True Or False',
  PRIMARY KEY (`id_connection_satu_sehat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `connection_simrs`
--

DROP TABLE IF EXISTS `connection_simrs`;
CREATE TABLE IF NOT EXISTS `connection_simrs` (
  `id_connection_simrs` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_connection_simrs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'ex: Development, Staging, Local, Production',
  `url_connection_simrs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `client_key` varchar(255) NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `datetime_expired` datetime DEFAULT NULL,
  `status_connection_simrs` tinyint(1) NOT NULL COMMENT 'true or false',
  PRIMARY KEY (`id_connection_simrs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dokumentasi`
--

DROP TABLE IF EXISTS `dokumentasi`;
CREATE TABLE IF NOT EXISTS `dokumentasi` (
  `id_dokumentasi` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `dokumentasi_title` varchar(225) NOT NULL,
  `dokumentasi_category` varchar(255) NOT NULL,
  `dokumentasi_description` text NOT NULL COMMENT 'maksimal 1000 huruf',
  `dokumentasi_datetime` datetime NOT NULL,
  `dokumentasi_author` varchar(255) NOT NULL,
  `publish` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_dokumentasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dokumentasi_content`
--

DROP TABLE IF EXISTS `dokumentasi_content`;
CREATE TABLE IF NOT EXISTS `dokumentasi_content` (
  `id_dokumentasi_content` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_dokumentasi` int UNSIGNED NOT NULL,
  `order_content` int UNSIGNED NOT NULL,
  `type_content` enum('text','list','image','video','image_link','video_link') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `value_content` text NOT NULL,
  `file_size` int UNSIGNED DEFAULT NULL,
  `file_type` enum('image/jpeg','image/png','image/gif','image/webp','image/bmp','video/mp4','video/webm','video/ogg','video/quicktime','video/x-msvideo','video/x-matroska') DEFAULT NULL,
  PRIMARY KEY (`id_dokumentasi_content`),
  KEY `content_to_dokumentasi` (`id_dokumentasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `google_credential`
--

DROP TABLE IF EXISTS `google_credential`;
CREATE TABLE IF NOT EXISTS `google_credential` (
  `id_google_credential` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `credential_env` enum('Production','Staging','Development','') NOT NULL,
  `client_id` text NOT NULL,
  `client_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_google_credential`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laboratorium`
--

DROP TABLE IF EXISTS `laboratorium`;
CREATE TABLE IF NOT EXISTS `laboratorium` (
  `id_laboratorium` varchar(255) NOT NULL,
  `id_pasien` int NOT NULL,
  `id_kunjungan` int NOT NULL,
  `ihs_pasien` varchar(255) DEFAULT NULL,
  `id_encounter` varchar(255) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `gender` enum('Laki-laki','Perempuan') NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `tujuan` enum('Rajal','Ranap') NOT NULL,
  `pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'UMUM, BPJS',
  `fakses` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'RSU El-Syifa',
  `unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Poli Bedah',
  `priority` enum('routine','urgent','stat') NOT NULL,
  `kode_dokter_pengirim` varchar(255) DEFAULT NULL,
  `ihs_dokter_pengirim` varchar(255) DEFAULT NULL,
  `nama_dokter_pengirim` varchar(255) DEFAULT NULL,
  `kode_dokter_penerima` varchar(255) DEFAULT NULL,
  `ihs_dokter_penerima` varchar(255) DEFAULT NULL,
  `nama_dokter_penerima` varchar(255) DEFAULT NULL,
  `kode_petugas` varchar(255) DEFAULT NULL,
  `ihs_petugas` varchar(255) DEFAULT NULL,
  `nama_petugas` varchar(255) DEFAULT NULL,
  `diagnosis` json NOT NULL COMMENT '{"system": "","code": "","display": ""}',
  `puasa` tinyint(1) NOT NULL COMMENT 'Status puasa',
  `status` enum('Diminta','Ditolak','Dibatalkan','Diterima','Pengambilan Spesimen','Pemeriksaan Spesimen','Keluar Hasil','Selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `datetime_diminta` datetime DEFAULT NULL,
  `datetime_diterima` datetime DEFAULT NULL,
  `datetime_spesimen` datetime DEFAULT NULL,
  `datetime_hasil` datetime DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'Jika ada permintaan lain dalam pemeriksaan',
  `alasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'Alasan jika dibatalkan atau ditolak',
  `form_system` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'System yang melakukan permintaan',
  PRIMARY KEY (`id_laboratorium`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laboratorium_diagnostic`
--

DROP TABLE IF EXISTS `laboratorium_diagnostic`;
CREATE TABLE IF NOT EXISTS `laboratorium_diagnostic` (
  `id_laboratorium_diagnostic` varchar(255) NOT NULL,
  `id_laboratorium` varchar(255) NOT NULL,
  `conclusion` text COMMENT 'kesimpulan',
  `clinical` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'informasi klinis',
  `icd_10_code` varchar(255) DEFAULT NULL,
  `icd_10_display` varchar(255) DEFAULT NULL,
  `icd_10_system` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_laboratorium_diagnostic`),
  KEY `diagnostic_to_laboratorium` (`id_laboratorium`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laboratorium_procedure`
--

DROP TABLE IF EXISTS `laboratorium_procedure`;
CREATE TABLE IF NOT EXISTS `laboratorium_procedure` (
  `id_laboratorium_procedure` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_laboratorium` varchar(255) NOT NULL,
  `id_procedure` varchar(255) DEFAULT NULL COMMENT 'SATUSEHAT',
  `procedure_description` text COMMENT 'Penjelasan Prosedur Puasa',
  `procedure_display` varchar(255) NOT NULL,
  `procedure_code` varchar(255) NOT NULL,
  `procedure_system` text NOT NULL,
  `datetime_start` datetime DEFAULT NULL,
  `datetim_end` datetime DEFAULT NULL,
  PRIMARY KEY (`id_laboratorium_procedure`),
  KEY `procedure_to_laboratorium` (`id_laboratorium`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laboratorium_rincian`
--

DROP TABLE IF EXISTS `laboratorium_rincian`;
CREATE TABLE IF NOT EXISTS `laboratorium_rincian` (
  `id_laboratorium_rincian` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_laboratorium` varchar(255) NOT NULL,
  `id_referensi_pemeriksaan` int UNSIGNED DEFAULT NULL,
  `id_laboratorium_spesimen` int UNSIGNED DEFAULT NULL,
  `id_service_request` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'dari SATUSEHAT',
  `id_observation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'dari SATUSEHAT',
  `id_diagnostic_report` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'dari SATUSEHAT',
  `id_referensi_category` int UNSIGNED DEFAULT NULL,
  `id_referensi_range` int UNSIGNED DEFAULT NULL,
  `nama_pemeriksaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `category_pemeriksaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `metode_pemeriksaan` varchar(255) DEFAULT NULL,
  `metode_pemeriksaan_display` varchar(255) DEFAULT NULL,
  `metode_pemeriksaan_code` varchar(255) DEFAULT NULL,
  `metode_pemeriksaan_system` text,
  `hasil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `interpertasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `conclusion` varchar(255) DEFAULT NULL,
  `keterangan` text,
  PRIMARY KEY (`id_laboratorium_rincian`),
  KEY `rincian_to_laboratorium` (`id_laboratorium`),
  KEY `rincian_to_referensi_pemeriksaan` (`id_referensi_pemeriksaan`),
  KEY `rincian_to_spesimen` (`id_laboratorium_spesimen`),
  KEY `id_referensi_category` (`id_referensi_category`),
  KEY `id_referensi_range` (`id_referensi_range`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laboratorium_spesimen`
--

DROP TABLE IF EXISTS `laboratorium_spesimen`;
CREATE TABLE IF NOT EXISTS `laboratorium_spesimen` (
  `id_laboratorium_spesimen` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_laboratorium` varchar(255) NOT NULL,
  `id_speciment` varchar(255) NOT NULL COMMENT 'ID Speciment Dari SATUSEHAT',
  `datetime_spesimen` datetime NOT NULL COMMENT 'Waktu pengambilan spesimen',
  `nama_spesimen` varchar(255) NOT NULL,
  `display_spesimen` varchar(255) DEFAULT NULL,
  `code_spesimen` varchar(255) DEFAULT NULL,
  `system_spesimen` text,
  `nama_metode_sample` varchar(255) NOT NULL,
  `display_metode_sample` varchar(255) DEFAULT NULL,
  `code_metode_sample` varchar(255) DEFAULT NULL,
  `system_metode_sample` text,
  `bodysite_nama` varchar(255) DEFAULT NULL,
  `bodysite_display` varchar(255) DEFAULT NULL,
  `bodysite_code` varchar(255) DEFAULT NULL,
  `bodysite_system` text,
  `nama_container` varchar(255) NOT NULL,
  `display_container` varchar(255) DEFAULT NULL,
  `code_container` varchar(255) DEFAULT NULL,
  `system_container` text,
  `quantity_value` decimal(15,2) NOT NULL COMMENT 'Jumlah spesimen yang diambil',
  `quantity_unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Unit jumlah yang digunakan',
  `quantity_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Kode Unit jumlah yang digunakan',
  `quantity_system` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'Standar/Sistem Unit jumlah yang digunakan',
  `collector_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Nama petugas yang mengambil Spesimen',
  `collector_ihs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'ID IHS petugas yang mengambil Spesimen',
  PRIMARY KEY (`id_laboratorium_spesimen`),
  KEY `id_laboratorium` (`id_laboratorium`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referensi_body_site`
--

DROP TABLE IF EXISTS `referensi_body_site`;
CREATE TABLE IF NOT EXISTS `referensi_body_site` (
  `id_referensi_body_site` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `body_site_nama` varchar(255) NOT NULL COMMENT 'Nama BodySite dalam Indonesia',
  `body_site_display` varchar(255) NOT NULL COMMENT 'Nama BodySite sesuai standar',
  `body_site_code` varchar(255) NOT NULL COMMENT 'Kode BodySite Sesuai Standar',
  `body_site_system` text NOT NULL COMMENT 'Sistem Bodysite Yang Digunakan',
  PRIMARY KEY (`id_referensi_body_site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referensi_category`
--

DROP TABLE IF EXISTS `referensi_category`;
CREATE TABLE IF NOT EXISTS `referensi_category` (
  `id_referensi_category` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_referensi_pemeriksaan` int UNSIGNED NOT NULL,
  `id_referensi_usia` int UNSIGNED DEFAULT NULL COMMENT 'Jika Dikelompokan berdasarkan usia',
  `umur_kategori` varchar(255) DEFAULT NULL,
  `umur_min` int DEFAULT NULL,
  `umur_max` int DEFAULT NULL,
  `umur_unit` varchar(255) DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan','All') NOT NULL,
  `nilai_hasil` varchar(255) NOT NULL COMMENT 'Apapun hasilnya akan dianggap string',
  `label` varchar(255) NOT NULL COMMENT 'Interpertasi dalam bahasa indonesia',
  `fhir_display` varchar(255) DEFAULT NULL COMMENT 'Nama Interpertasi berdasarkan FHIR',
  `fhir_code` varchar(255) DEFAULT NULL COMMENT 'Kode Interpertasi berdasarkan FHIR',
  `fhir_system` text COMMENT 'http://snomed.info/sct',
  `normal_value` tinyint(1) NOT NULL COMMENT 'Menetapkan nilai ini sebagai normal',
  PRIMARY KEY (`id_referensi_category`),
  KEY `category_to_ref_pemeriksaan` (`id_referensi_pemeriksaan`),
  KEY `category_to_usia` (`id_referensi_usia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referensi_container`
--

DROP TABLE IF EXISTS `referensi_container`;
CREATE TABLE IF NOT EXISTS `referensi_container` (
  `id_referensi_container` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_container` varchar(255) NOT NULL,
  `display_container` varchar(255) NOT NULL,
  `code_container` varchar(255) NOT NULL,
  `system_container` text NOT NULL,
  `kapasitas_container` decimal(15,2) NOT NULL,
  `unit_container` varchar(255) NOT NULL,
  `code_unit_container` varchar(255) NOT NULL,
  `system_unit_container` text NOT NULL,
  PRIMARY KEY (`id_referensi_container`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referensi_jenis_spesimen`
--

DROP TABLE IF EXISTS `referensi_jenis_spesimen`;
CREATE TABLE IF NOT EXISTS `referensi_jenis_spesimen` (
  `id_referensi_jenis_spesimen` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_spesimen` varchar(255) NOT NULL,
  `display_spesimen` varchar(255) NOT NULL,
  `code_spesimen` varchar(255) NOT NULL,
  `system_spesimen` text NOT NULL,
  PRIMARY KEY (`id_referensi_jenis_spesimen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referensi_metode_pemeriksaan`
--

DROP TABLE IF EXISTS `referensi_metode_pemeriksaan`;
CREATE TABLE IF NOT EXISTS `referensi_metode_pemeriksaan` (
  `id_referensi_metode_pemeriksaan` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_metode_pemeriksaan` varchar(255) NOT NULL,
  `display_metode_pemeriksaan` varchar(255) NOT NULL,
  `code_metode_pemeriksaan` varchar(255) NOT NULL,
  `system_metode_pemeriksaan` text NOT NULL,
  PRIMARY KEY (`id_referensi_metode_pemeriksaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referensi_metode_sample`
--

DROP TABLE IF EXISTS `referensi_metode_sample`;
CREATE TABLE IF NOT EXISTS `referensi_metode_sample` (
  `id_referensi_metode_sample` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_metode_sample` varchar(255) NOT NULL,
  `display_metode_sample` varchar(255) NOT NULL,
  `code_metode_sample` varchar(255) NOT NULL,
  `system_metode_sample` text NOT NULL,
  PRIMARY KEY (`id_referensi_metode_sample`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referensi_pemeriksaan`
--

DROP TABLE IF EXISTS `referensi_pemeriksaan`;
CREATE TABLE IF NOT EXISTS `referensi_pemeriksaan` (
  `id_referensi_pemeriksaan` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_pemeriksaan` varchar(255) NOT NULL COMMENT 'Nama Bahasa Indonesia',
  `category_pemeriksaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Contoh Hematologi, Urin Dll',
  `code_pemeriksaan` varchar(255) NOT NULL COMMENT 'Kode LOINC',
  `display_pemeriksaan` varchar(255) NOT NULL COMMENT 'Display Loinc',
  `system_pemeriksaan` varchar(255) NOT NULL COMMENT 'https://',
  `unit` varchar(255) DEFAULT NULL,
  `unit_display` varchar(255) DEFAULT NULL,
  `unit_code` varchar(255) DEFAULT NULL,
  `unit_system` text,
  `result_type` enum('Numeric','Decimal','Coded','Text','Boolean') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Tipe data hasil',
  `result_interpertation_type` enum('Range','Category','None') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Range, Category, None',
  `allow_age` tinyint(1) DEFAULT NULL COMMENT 'Jika berkaitan dengan usia',
  `allow_sex` tinyint(1) DEFAULT NULL COMMENT 'Jika berkaitan dengan jenis kelamin',
  PRIMARY KEY (`id_referensi_pemeriksaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referensi_pemeriksaan_relasi`
--

DROP TABLE IF EXISTS `referensi_pemeriksaan_relasi`;
CREATE TABLE IF NOT EXISTS `referensi_pemeriksaan_relasi` (
  `id_referensi_pemeriksaan_relasi` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_referensi_pemeriksaan` int UNSIGNED NOT NULL,
  `id_referensi_metode_pemeriksaan` int UNSIGNED DEFAULT NULL,
  `id_referensi_jenis_spesimen` int UNSIGNED DEFAULT NULL,
  `id_referensi_metode_sample` int UNSIGNED DEFAULT NULL,
  `id_referensi_container` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_referensi_pemeriksaan_relasi`),
  KEY `relasi_pemeriksaan` (`id_referensi_pemeriksaan`),
  KEY `relasi_metode_pemeriksaan` (`id_referensi_metode_pemeriksaan`),
  KEY `relasi_jenis_spesimen` (`id_referensi_jenis_spesimen`),
  KEY `relasi_metode_sample` (`id_referensi_metode_sample`),
  KEY `relasi_container` (`id_referensi_container`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referensi_range`
--

DROP TABLE IF EXISTS `referensi_range`;
CREATE TABLE IF NOT EXISTS `referensi_range` (
  `id_referensi_range` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_referensi_pemeriksaan` int UNSIGNED NOT NULL,
  `id_referensi_usia` int UNSIGNED DEFAULT NULL,
  `umur_kategori` varchar(255) DEFAULT NULL COMMENT 'Prematur, Neonatus, Dewasa, Lansia DLL',
  `umur_min` int UNSIGNED DEFAULT NULL,
  `umur_max` int UNSIGNED DEFAULT NULL,
  `umur_unit` enum('Hari','Bulan','Tahun') DEFAULT NULL COMMENT 'Hari, Bulan, Tahun',
  `jenis_kelamin` enum('Laki-laki','Perempuan','All') NOT NULL COMMENT 'Laki-laki, Perempuan, All',
  `nilai_min` decimal(15,2) DEFAULT NULL,
  `nilai_max` decimal(15,2) DEFAULT NULL,
  `operator` enum('More','Between') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `label` varchar(255) NOT NULL COMMENT 'Normal, Tinggi, Rendah, Sedang, Abnormal',
  `fhir_display` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Low, High, Normal, DLL',
  `fhir_code` varchar(255) NOT NULL,
  `fhir_system` text COMMENT 'http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation',
  `conclusion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Normal, Abnormal',
  `normal_value` tinyint(1) NOT NULL COMMENT 'Jika Opsi ini adalah nilai normal',
  PRIMARY KEY (`id_referensi_range`),
  KEY `id_referensi_pemeriksaan` (`id_referensi_pemeriksaan`),
  KEY `range_to_usia` (`id_referensi_usia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referensi_satuan`
--

DROP TABLE IF EXISTS `referensi_satuan`;
CREATE TABLE IF NOT EXISTS `referensi_satuan` (
  `id_referensi_satuan` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_satuan` varchar(255) NOT NULL,
  `unit_satuan` varchar(255) NOT NULL,
  `code_satuan` varchar(255) NOT NULL,
  `system_satuan` text NOT NULL,
  PRIMARY KEY (`id_referensi_satuan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referensi_signature`
--

DROP TABLE IF EXISTS `referensi_signature`;
CREATE TABLE IF NOT EXISTS `referensi_signature` (
  `id_referensi_signature` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Kode Dokter RS',
  `ihs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'ID Practitioner Dari Satu Sehat',
  `nama` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL COMMENT 'Radiolog, Dokter Pemeriksa DLL',
  `base_64_ttd` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Base 64 TTD',
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_referensi_signature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referensi_usia`
--

DROP TABLE IF EXISTS `referensi_usia`;
CREATE TABLE IF NOT EXISTS `referensi_usia` (
  `id_referensi_usia` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_referensi_pemeriksaan` int UNSIGNED NOT NULL,
  `umur_kategori` varchar(255) NOT NULL,
  `umur_min` int NOT NULL,
  `umur_max` int NOT NULL,
  `umur_unit` varchar(255) NOT NULL,
  PRIMARY KEY (`id_referensi_usia`),
  KEY `usia_to_pemeriksaan` (`id_referensi_pemeriksaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_email_gateway`
--

DROP TABLE IF EXISTS `setting_email_gateway`;
CREATE TABLE IF NOT EXISTS `setting_email_gateway` (
  `id_setting_email_gateway` int NOT NULL AUTO_INCREMENT,
  `email_gateway` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `password_gateway` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `url_provider` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `port_gateway` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `nama_pengirim` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `url_service` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `validasi_email` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `redirect_validasi` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `pesan_validasi_email` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id_setting_email_gateway`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access`
--
ALTER TABLE `access`
  ADD CONSTRAINT `access_to_group` FOREIGN KEY (`id_access_group`) REFERENCES `access_group` (`id_access_group`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `access_log`
--
ALTER TABLE `access_log`
  ADD CONSTRAINT `log_to_access` FOREIGN KEY (`id_access`) REFERENCES `access` (`id_access`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `access_login`
--
ALTER TABLE `access_login`
  ADD CONSTRAINT `login_to_access` FOREIGN KEY (`id_access`) REFERENCES `access` (`id_access`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `access_permission`
--
ALTER TABLE `access_permission`
  ADD CONSTRAINT `permission_to_access` FOREIGN KEY (`id_access`) REFERENCES `access` (`id_access`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permission_to_features` FOREIGN KEY (`id_access_feature`) REFERENCES `access_feature` (`id_access_feature`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `access_reference`
--
ALTER TABLE `access_reference`
  ADD CONSTRAINT `reference_to_feature` FOREIGN KEY (`id_access_feature`) REFERENCES `access_feature` (`id_access_feature`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reference_to_group` FOREIGN KEY (`id_access_group`) REFERENCES `access_group` (`id_access_group`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `access_reset`
--
ALTER TABLE `access_reset`
  ADD CONSTRAINT `reset_to_access` FOREIGN KEY (`id_access`) REFERENCES `access` (`id_access`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `api_token`
--
ALTER TABLE `api_token`
  ADD CONSTRAINT `api_token_to_account` FOREIGN KEY (`id_api_account`) REFERENCES `api_account` (`id_api_account`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dokumentasi_content`
--
ALTER TABLE `dokumentasi_content`
  ADD CONSTRAINT `content_to_dokumentasi` FOREIGN KEY (`id_dokumentasi`) REFERENCES `dokumentasi` (`id_dokumentasi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `laboratorium_diagnostic`
--
ALTER TABLE `laboratorium_diagnostic`
  ADD CONSTRAINT `diagnostic_to_laboratorium` FOREIGN KEY (`id_laboratorium`) REFERENCES `laboratorium` (`id_laboratorium`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `laboratorium_procedure`
--
ALTER TABLE `laboratorium_procedure`
  ADD CONSTRAINT `procedure_to_laboratorium` FOREIGN KEY (`id_laboratorium`) REFERENCES `laboratorium` (`id_laboratorium`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `laboratorium_rincian`
--
ALTER TABLE `laboratorium_rincian`
  ADD CONSTRAINT `rincian_to_category` FOREIGN KEY (`id_referensi_category`) REFERENCES `referensi_category` (`id_referensi_category`) ON DELETE SET NULL,
  ADD CONSTRAINT `rincian_to_laboratorium` FOREIGN KEY (`id_laboratorium`) REFERENCES `laboratorium` (`id_laboratorium`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rincian_to_range` FOREIGN KEY (`id_referensi_range`) REFERENCES `referensi_range` (`id_referensi_range`) ON DELETE SET NULL,
  ADD CONSTRAINT `rincian_to_referensi_pemeriksaan` FOREIGN KEY (`id_referensi_pemeriksaan`) REFERENCES `referensi_pemeriksaan` (`id_referensi_pemeriksaan`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `rincian_to_spesimen` FOREIGN KEY (`id_laboratorium_spesimen`) REFERENCES `laboratorium_spesimen` (`id_laboratorium_spesimen`) ON DELETE SET NULL;

--
-- Constraints for table `laboratorium_spesimen`
--
ALTER TABLE `laboratorium_spesimen`
  ADD CONSTRAINT `spesimen_to_laboratorium` FOREIGN KEY (`id_laboratorium`) REFERENCES `laboratorium` (`id_laboratorium`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `referensi_category`
--
ALTER TABLE `referensi_category`
  ADD CONSTRAINT `category_to_ref_pemeriksaan` FOREIGN KEY (`id_referensi_pemeriksaan`) REFERENCES `referensi_pemeriksaan` (`id_referensi_pemeriksaan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `category_to_usia` FOREIGN KEY (`id_referensi_usia`) REFERENCES `referensi_usia` (`id_referensi_usia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `referensi_pemeriksaan_relasi`
--
ALTER TABLE `referensi_pemeriksaan_relasi`
  ADD CONSTRAINT `relasi_container` FOREIGN KEY (`id_referensi_container`) REFERENCES `referensi_container` (`id_referensi_container`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relasi_jenis_spesimen` FOREIGN KEY (`id_referensi_jenis_spesimen`) REFERENCES `referensi_jenis_spesimen` (`id_referensi_jenis_spesimen`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relasi_metode_pemeriksaan` FOREIGN KEY (`id_referensi_metode_pemeriksaan`) REFERENCES `referensi_metode_pemeriksaan` (`id_referensi_metode_pemeriksaan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relasi_metode_sample` FOREIGN KEY (`id_referensi_metode_sample`) REFERENCES `referensi_metode_sample` (`id_referensi_metode_sample`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relasi_pemeriksaan` FOREIGN KEY (`id_referensi_pemeriksaan`) REFERENCES `referensi_pemeriksaan` (`id_referensi_pemeriksaan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `referensi_range`
--
ALTER TABLE `referensi_range`
  ADD CONSTRAINT `range_to_ref_pemeriksaan` FOREIGN KEY (`id_referensi_pemeriksaan`) REFERENCES `referensi_pemeriksaan` (`id_referensi_pemeriksaan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `range_to_usia` FOREIGN KEY (`id_referensi_usia`) REFERENCES `referensi_usia` (`id_referensi_usia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `referensi_usia`
--
ALTER TABLE `referensi_usia`
  ADD CONSTRAINT `usia_to_pemeriksaan` FOREIGN KEY (`id_referensi_pemeriksaan`) REFERENCES `referensi_pemeriksaan` (`id_referensi_pemeriksaan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
