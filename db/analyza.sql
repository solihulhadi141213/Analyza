-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 11, 2026 at 11:25 AM
-- Server version: 9.1.0
-- PHP Version: 8.0.30

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access`
--

INSERT INTO `access` (`id_access`, `id_access_group`, `access_name`, `access_email`, `access_contact`, `access_nik`, `access_ihs`, `access_password`, `access_foto`, `access_client`, `access_active`) VALUES
(1, 1, 'Solihul Hadi', 'dhiforester@gmail.com', '089601154726', '3208274501950004', '10001742501', '$2y$10$KnOYcmK1U3iE8ta.PnDefOTr1h5Cz1LaGHfyM5wBqg1vuqqg1i5le', 'ca6526b10323e5ffc519def7f71e10.jpg', 0, 1),
(2, 1, 'Dewi Widiastuti', 'dewiwidiastuti@gmail.com', '08975657467', '3208274501950004', '10001742501', '$2y$10$YW/wCElX7HYlfipjFo80eO89RkvlUZ9iIOwZk4lK.Cf/BR8ypeygm', '4522beb0ae8aabe337284b439dcc79.png', 0, 1),
(8, 1, 'Bayu Anugrah', 'bayu88aaa@gmail.com', '085693168595', '3208170809940006', '10004122691', '$2y$10$gNbRZTnQ8lPJtrg5TGCyoe0N2k7EcFKI1znNWu8XI/UkuCJA4S8Ae', '', 0, 0);

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

--
-- Dumping data for table `access_feature`
--

INSERT INTO `access_feature` (`id_access_feature`, `feature_name`, `feature_category`, `feature_description`, `datetime_creat`) VALUES
('36grsDsU11UKOCFPKlh5Gx7K2YbR6XpRHJ5y', 'Koneksi SIMRS', 'Koneksi', 'Pengaturan parameter koneksi dengan SIMRS', '2025-12-16 20:07:41'),
('5a7yRbkFPs6fXNHQf8a7bI79IZcbbIaijE0E', 'Koneksi Satu Sehat', 'Koneksi', 'Pengaturan parameter koneksi ke Satu Sehat Platform', '2025-12-17 18:47:14'),
('6W5aMQEkhaBfwBGXQOEQx7M04Iv9h8IXOEsT', 'Referensi Metode Pemeriksaan', 'Referensi', 'Halaman untuk mengelola metode pemeriksaan', '2026-02-09 19:41:38'),
('8bOwARsJKZ5Dc0VxJwXdWdiP2KPfxFjVqgbu', 'Referensi Kemasan (Container)', 'Referensi', 'Halaman yang mengelola berbagai jenis kemasan kontainer spesimen', '2026-02-08 07:20:57'),
('Dnd2UZLzazCqJ9WfuzQKlIOpYueb2fXxNHXA', 'Bantuan', 'Lainnya', 'Halaman untuk mengelola konten bantuan atau dokumentasi', '2025-09-06 14:36:36'),
('H8lByxYVLw1zYg9hIYkZxtNNgkBH8Gi8h6Vv', 'Referensi Jenis Spesimen', 'Referensi', 'Halaman ini digunakan untuk mengelola daftar Jenis Spesimen', '2026-02-09 11:32:50'),
('Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH', 'Pengaturan Umum', 'Pengaturan', 'Halaman yang berfungsi untuk mengatur aplikasi secara umum', '2025-09-01 19:27:07'),
('aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY', 'Email Gateway', 'Pengaturan', 'Halaman yang berguna untuk menyimpan pengaturan email gateway', '2025-09-01 19:32:54'),
('fErKPHIY6bEuhp7sOivMHglXHOP2gVubzGyw', 'Daftar Pertanyaan', 'Referensi', 'Halaman untuk mengelola daftar pertanyaan dalam assesment radiologi', '2025-12-30 20:58:40'),
('jO3M0NopVQeXi4VuDHpvD9SRJzntpUGAe6Sw', 'Akses Pengguna', 'Akses', 'Halaman untuk mengelola akun akses pengguna', '2025-08-31 20:23:54'),
('lInyeHHg924zNLaXZ3SmjjnuyCOYBnUyUuTD', 'Entitas Akses Pengguna', 'Akses', 'Halaman untuk mengelola entitas/group/level pengguna', '2025-08-31 20:23:01'),
('lgG3CggWuy9Bd3m4eaXXx6tjKQonITqt4MOe', 'Jenis Pemeriksaan', 'Referensi', 'Halaman untuk mengelola jenis pemeriksaan', '2026-02-07 14:56:05'),
('nSYinRWpCF9MHNUIlW7Up5vTip70gNNLlrqv', 'Fitur Aplikasi', 'Akses', 'Halaman untuk mengelola fitur aplikasi', '2025-08-31 20:21:48'),
('nkYXm3U8XWpOt1cD3PNeCwDQzesMYmmUUbee', 'API Key', 'Koneksi', 'Halaman untuk mengelola data API key untuk aplikasi lain agar terhubung Ke Redix', '2025-12-19 16:28:20'),
('vA2qgCIl2YHVsxGmocRcv5293dcXh5oDXVYt', 'Referensi Satuan', 'Referensi', 'Halaman untuk mengelola daftar referensi satuan/Unit', '2026-02-07 22:54:04'),
('wW79JNUwhM5nxRymMuxQrycBpUkBRAt2r2UU', 'Referensi Pengambilan Spesimen', 'Referensi', 'Halaman yang berfungsi untuk mengelola daftar referensi metode / cara pengambilan spesimen', '2026-02-09 10:03:48');

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access_group`
--

INSERT INTO `access_group` (`id_access_group`, `group_name`, `group_description`) VALUES
(1, 'Admin', 'Pihak yang berwenang melakukan akses ke semua fitur');

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
) ENGINE=InnoDB AUTO_INCREMENT=483 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access_log`
--

INSERT INTO `access_log` (`id_access_log`, `id_access`, `log_datetime`, `log_category`, `log_description`) VALUES
(1, 1, '2025-09-12 11:38:46', 'Entitas Akses', 'Edit Entitas Akses'),
(2, 1, '2025-09-12 11:41:08', 'Bantuan', 'Tambah Konten Bantuan'),
(3, 1, '2025-09-12 13:08:04', 'Login', 'Login Berhasil'),
(4, 1, '2025-09-12 13:20:16', 'Bantuan', 'Hapus Konten Bantuan'),
(5, 1, '2025-09-12 13:20:24', 'Bantuan', 'Edit Konten Bantuan'),
(6, 1, '2025-09-13 07:18:55', 'Login', 'Login Berhasil'),
(7, 2, '2025-09-13 07:31:01', 'Login', 'Login Berhasil'),
(8, 2, '2025-09-13 08:49:27', 'Login', 'Login Berhasil'),
(9, 1, '2025-09-14 13:27:21', 'Login', 'Login Berhasil'),
(10, 1, '2025-09-14 17:35:33', 'Login', 'Login Berhasil'),
(11, 1, '2025-09-14 18:39:49', 'Kelas', 'Input Kelas Berhasil'),
(12, 1, '2025-09-14 19:02:10', 'Login', 'Login Berhasil'),
(13, 1, '2025-09-14 19:34:30', 'Login', 'Login Berhasil'),
(14, 1, '2025-09-15 01:16:03', 'Login', 'Login Berhasil'),
(15, 1, '2025-09-17 21:07:10', 'Login', 'Login Berhasil'),
(16, 1, '2025-09-18 03:42:08', 'Login', 'Login Berhasil'),
(17, 1, '2025-09-18 12:59:24', 'Login', 'Login Berhasil'),
(18, 1, '2025-09-18 16:19:50', 'Login', 'Login Berhasil'),
(19, 1, '2025-09-18 18:05:00', 'Login', 'Login Berhasil'),
(20, 1, '2025-09-18 21:38:06', 'Login', 'Login Berhasil'),
(21, 1, '2025-09-19 18:55:59', 'Login', 'Login Berhasil'),
(22, 1, '2025-09-19 21:29:03', 'Login', 'Login Berhasil'),
(23, 1, '2025-09-20 17:14:16', 'Login', 'Login Berhasil'),
(24, 1, '2025-09-20 19:49:57', 'Login', 'Login Berhasil'),
(25, 1, '2025-09-21 00:13:59', 'Login', 'Login Berhasil'),
(26, 1, '2025-09-23 00:12:26', 'Login', 'Login Berhasil'),
(27, 1, '2025-09-23 01:31:31', 'Login', 'Login Berhasil'),
(28, 1, '2025-09-23 16:04:17', 'Login', 'Login Berhasil'),
(29, 1, '2025-09-23 18:11:19', 'Login', 'Login Berhasil'),
(30, 1, '2025-09-24 22:51:52', 'Login', 'Login Berhasil'),
(31, 1, '2025-09-25 03:16:42', 'Login', 'Login Berhasil'),
(32, 1, '2025-09-25 14:49:50', 'Login', 'Login Berhasil'),
(33, 1, '2025-09-25 17:10:02', 'Login', 'Login Berhasil'),
(34, 1, '2025-09-26 00:15:18', 'Login', 'Login Berhasil'),
(35, 1, '2025-10-14 13:02:47', 'Login', 'Login Berhasil'),
(36, 1, '2025-10-14 13:04:26', 'Login', 'Login Berhasil'),
(37, 1, '2025-10-19 12:58:26', 'Login', 'Login Berhasil'),
(38, 1, '2025-10-21 03:37:45', 'Login', 'Login Berhasil'),
(39, 1, '2025-10-21 04:05:41', 'Periode Akademik', 'Input Periode Akademik Akses'),
(40, 1, '2025-10-21 18:55:35', 'Login', 'Login Berhasil'),
(41, 1, '2025-10-21 23:33:54', 'Login', 'Login Berhasil'),
(42, 1, '2025-10-21 23:34:48', 'Login', 'Login Berhasil'),
(43, 1, '2025-10-22 00:50:16', 'Login', 'Login Berhasil'),
(44, 1, '2025-10-22 13:58:24', 'Login', 'Login Berhasil'),
(45, 1, '2025-10-23 00:13:45', 'Login', 'Login Berhasil'),
(46, 1, '2025-10-24 17:59:21', 'Login', 'Login Berhasil'),
(47, 1, '2025-10-24 20:59:47', 'Login', 'Login Berhasil'),
(48, 1, '2025-10-25 00:37:08', 'Login', 'Login Berhasil'),
(49, 1, '2025-10-25 10:52:13', 'Login', 'Login Berhasil'),
(50, 1, '2025-10-25 16:35:46', 'Login', 'Login Berhasil'),
(51, 1, '2025-10-25 16:46:02', 'Akses', 'Input Fitur Akses'),
(52, 1, '2025-10-25 16:46:22', 'Entitas Akses', 'Edit Entitas Akses'),
(53, 1, '2025-10-25 18:32:09', 'Login', 'Login Berhasil'),
(54, 1, '2025-10-25 21:54:05', 'Login', 'Login Berhasil'),
(55, 1, '2025-10-26 11:39:30', 'Login', 'Login Berhasil'),
(56, 1, '2025-10-26 19:24:26', 'Login', 'Login Berhasil'),
(57, 1, '2025-10-27 15:56:35', 'Login', 'Login Berhasil'),
(58, 1, '2025-10-27 19:06:53', 'Login', 'Login Berhasil'),
(59, 1, '2025-10-27 22:02:31', 'Login', 'Login Berhasil'),
(60, 1, '2025-10-27 23:11:15', 'Login', 'Login Berhasil'),
(61, 1, '2025-10-28 01:34:30', 'Login', 'Login Berhasil'),
(62, 1, '2025-10-28 01:52:48', 'Pembayaran', 'Input Pembayaran Berhasil'),
(63, 1, '2025-10-28 22:38:22', 'Login', 'Login Berhasil'),
(64, 1, '2025-10-29 00:02:42', 'Pembayaran', 'Input Pembayaran Berhasil'),
(65, 1, '2025-10-29 00:02:50', 'Pembayaran', 'Input Pembayaran Berhasil'),
(66, 1, '2025-10-29 00:02:59', 'Pembayaran', 'Input Pembayaran Berhasil'),
(67, 1, '2025-10-29 00:03:04', 'Pembayaran', 'Input Pembayaran Berhasil'),
(68, 1, '2025-10-29 22:17:23', 'Login', 'Login Berhasil'),
(69, 1, '2025-10-30 00:09:55', 'Login', 'Login Berhasil'),
(70, 1, '2025-10-30 01:46:55', 'Login', 'Login Berhasil'),
(71, 1, '2025-10-30 14:01:24', 'Login', 'Login Berhasil'),
(72, 1, '2025-10-30 17:28:11', 'Login', 'Login Berhasil'),
(73, 1, '2025-10-31 19:40:25', 'Login', 'Login Berhasil'),
(74, 1, '2025-10-31 22:00:34', 'Login', 'Login Berhasil'),
(75, 1, '2025-11-01 00:44:29', 'Login', 'Login Berhasil'),
(76, 1, '2025-11-01 02:39:31', 'Login', 'Login Berhasil'),
(77, 1, '2025-11-01 19:34:03', 'Login', 'Login Berhasil'),
(78, 1, '2025-11-01 23:19:42', 'Login', 'Login Berhasil'),
(79, 1, '2025-11-02 19:41:33', 'Login', 'Login Berhasil'),
(80, 1, '2025-11-03 00:31:51', 'Login', 'Login Berhasil'),
(81, 1, '2025-11-05 01:49:08', 'Login', 'Login Berhasil'),
(82, 1, '2025-11-05 21:19:23', 'Login', 'Login Berhasil'),
(83, 1, '2025-11-06 01:51:46', 'Login', 'Login Berhasil'),
(84, 1, '2025-11-06 02:07:37', 'Siswa', 'Edit Siswa Berhasil'),
(85, 1, '2025-11-06 02:08:21', 'Siswa', 'Edit Siswa Berhasil'),
(86, 1, '2025-11-06 02:08:31', 'Siswa', 'Edit Siswa Berhasil'),
(87, 1, '2025-11-06 02:14:00', 'Siswa', 'Edit Siswa Berhasil'),
(88, 1, '2025-11-06 02:14:08', 'Siswa', 'Edit Siswa Berhasil'),
(89, 1, '2025-11-06 02:41:17', 'Siswa', 'Edit Siswa Berhasil'),
(90, 1, '2025-11-06 02:41:25', 'Siswa', 'Edit Siswa Berhasil'),
(91, 1, '2025-11-06 02:41:34', 'Siswa', 'Edit Siswa Berhasil'),
(92, 1, '2025-11-06 02:41:40', 'Siswa', 'Edit Siswa Berhasil'),
(93, 1, '2025-11-06 02:41:45', 'Siswa', 'Edit Siswa Berhasil'),
(94, 1, '2025-11-06 12:27:02', 'Login', 'Login Berhasil'),
(95, 1, '2025-11-06 16:08:40', 'Login', 'Login Berhasil'),
(96, 1, '2025-11-06 18:58:23', 'Login', 'Login Berhasil'),
(97, 1, '2025-11-07 22:10:23', 'Login', 'Login Berhasil'),
(98, 1, '2025-11-08 19:47:18', 'Login', 'Login Berhasil'),
(99, 1, '2025-11-09 00:03:44', 'Login', 'Login Berhasil'),
(100, 1, '2025-11-09 01:31:22', 'Login', 'Login Berhasil'),
(101, 1, '2025-11-09 04:31:09', 'Pembayaran', 'Input Pembayaran Berhasil'),
(102, 1, '2025-11-09 05:04:47', 'Pembayaran', 'Input Pembayaran Berhasil'),
(103, 1, '2025-11-09 17:23:44', 'Login', 'Login Berhasil'),
(104, 1, '2025-11-09 18:52:33', 'Pembayaran', 'Input Pembayaran Berhasil'),
(105, 1, '2025-11-09 19:04:01', 'Pembayaran', 'Input Pembayaran Berhasil'),
(106, 1, '2025-11-09 22:05:41', 'Login', 'Login Berhasil'),
(107, 1, '2025-11-10 15:36:49', 'Login', 'Login Berhasil'),
(108, 1, '2025-11-10 17:21:36', 'Login', 'Login Berhasil'),
(109, 1, '2025-11-11 15:41:24', 'Login', 'Login Berhasil'),
(110, 1, '2025-11-11 15:42:08', 'Pembayaran', 'Input Pembayaran Berhasil'),
(111, 1, '2025-11-11 15:54:29', 'Login', 'Login Berhasil'),
(112, 1, '2025-11-11 19:03:18', 'Login', 'Login Berhasil'),
(113, 1, '2025-11-11 20:32:07', 'Login', 'Login Berhasil'),
(114, 1, '2025-11-11 21:55:55', 'Login', 'Login Berhasil'),
(115, 1, '2025-11-11 22:57:28', 'Login', 'Login Berhasil'),
(116, 1, '2025-11-11 23:58:19', 'Login', 'Login Berhasil'),
(117, 1, '2025-11-13 04:44:46', 'Login', 'Login Berhasil'),
(118, 1, '2025-11-13 12:52:38', 'Login', 'Login Berhasil'),
(119, 1, '2025-11-14 00:16:43', 'Login', 'Login Berhasil'),
(120, 1, '2025-11-14 17:06:58', 'Login', 'Login Berhasil'),
(121, 1, '2025-11-14 20:01:55', 'Login', 'Login Berhasil'),
(122, 1, '2025-11-14 20:02:49', 'Login', 'Login Berhasil'),
(123, 1, '2025-11-15 00:53:50', 'Login', 'Login Berhasil'),
(124, 1, '2025-11-15 00:54:12', 'Pembayaran', 'Input Pembayaran Berhasil'),
(125, 1, '2025-11-15 00:54:26', 'Pembayaran', 'Input Pembayaran Berhasil'),
(126, 1, '2025-11-15 01:47:03', 'Pembayaran', 'Input Pembayaran Berhasil'),
(127, 1, '2025-11-15 01:48:06', 'Pembayaran', 'Input Pembayaran Berhasil'),
(128, 1, '2025-11-15 01:48:24', 'Pembayaran', 'Input Pembayaran Berhasil'),
(129, 1, '2025-11-15 03:18:09', 'Komponen Biaya', 'Input Komponen Biaya Berhasil'),
(130, 1, '2025-11-15 03:18:50', 'Komponen Biaya', 'Input Komponen Biaya Berhasil'),
(131, 1, '2025-11-15 03:19:16', 'Komponen Biaya', 'Input Komponen Biaya Berhasil'),
(132, 1, '2025-11-15 03:23:28', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(133, 1, '2025-11-15 03:24:42', 'Komponen Biaya', 'Hapus Komponen Biaya'),
(134, 1, '2025-11-15 03:25:18', 'Komponen Biaya', 'Hapus Komponen Biaya'),
(135, 1, '2025-11-15 03:43:06', 'Komponen Biaya', 'Hapus Komponen Biaya'),
(136, 1, '2025-11-15 18:18:57', 'Login', 'Login Berhasil'),
(137, 1, '2025-11-15 19:53:55', 'Komponen Biaya', 'Hapus Komponen Biaya'),
(138, 1, '2025-11-15 20:01:49', 'Komponen Biaya', 'Hapus Komponen Biaya'),
(139, 1, '2025-11-15 21:42:52', 'Login', 'Login Berhasil'),
(140, 1, '2025-11-16 00:08:35', 'Login', 'Login Berhasil'),
(141, 1, '2025-11-16 00:54:04', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(142, 1, '2025-11-16 10:36:54', 'Login', 'Login Berhasil'),
(143, 1, '2025-11-17 01:03:32', 'Login', 'Login Berhasil'),
(144, 1, '2025-11-17 21:37:56', 'Login', 'Login Berhasil'),
(145, 1, '2025-11-18 01:06:31', 'Login', 'Login Berhasil'),
(146, 1, '2025-11-18 02:37:56', 'Login', 'Login Berhasil'),
(147, 1, '2025-11-18 02:54:45', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(148, 1, '2025-11-18 02:56:00', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(149, 1, '2025-11-18 02:56:04', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(150, 1, '2025-11-18 02:57:15', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(151, 1, '2025-11-18 02:57:19', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(152, 1, '2025-11-18 02:57:25', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(153, 1, '2025-11-18 02:57:32', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(154, 1, '2025-11-18 18:06:55', 'Login', 'Login Berhasil'),
(155, 1, '2025-11-18 19:48:48', 'Login', 'Login Berhasil'),
(156, 1, '2025-11-19 01:50:17', 'Login', 'Login Berhasil'),
(157, 1, '2025-11-19 15:09:21', 'Login', 'Login Berhasil'),
(158, 1, '2025-11-19 16:05:59', 'Pembayaran', 'Input Pembayaran Berhasil'),
(159, 1, '2025-11-19 16:59:42', 'Pembayaran', 'Input Pembayaran Berhasil'),
(160, 1, '2025-11-19 16:59:50', 'Pembayaran', 'Input Pembayaran Berhasil'),
(161, 1, '2025-11-19 17:07:29', 'Pembayaran', 'Input Pembayaran Berhasil'),
(162, 1, '2025-11-19 17:07:41', 'Pembayaran', 'Input Pembayaran Berhasil'),
(163, 1, '2025-11-19 18:24:40', 'Login', 'Login Berhasil'),
(164, 1, '2025-11-20 01:40:02', 'Login', 'Login Berhasil'),
(165, 1, '2025-11-20 14:02:25', 'Login', 'Login Berhasil'),
(166, 1, '2025-11-20 23:54:13', 'Login', 'Login Berhasil'),
(167, 1, '2025-11-22 22:15:01', 'Login', 'Login Berhasil'),
(168, 1, '2025-11-23 01:13:07', 'Login', 'Login Berhasil'),
(169, 1, '2025-11-23 14:11:17', 'Login', 'Login Berhasil'),
(170, 1, '2025-11-24 00:53:29', 'Login', 'Login Berhasil'),
(171, 1, '2025-11-25 00:41:26', 'Login', 'Login Berhasil'),
(172, 1, '2025-11-25 02:16:42', 'Pembayaran', 'Input Pembayaran Berhasil'),
(173, 1, '2025-11-25 02:17:25', 'Pembayaran', 'Input Pembayaran Berhasil'),
(174, 1, '2025-11-25 04:28:16', 'Login', 'Login Berhasil'),
(175, 1, '2025-11-26 00:35:18', 'Login', 'Login Berhasil'),
(176, 1, '2025-11-26 01:27:51', 'Pembayaran', 'Input Pembayaran Berhasil'),
(177, 1, '2025-11-28 01:07:11', 'Login', 'Login Berhasil'),
(178, 1, '2025-11-28 03:28:18', 'Pembayaran', 'Input Pembayaran Berhasil'),
(179, 1, '2025-11-28 03:36:08', 'Pembayaran', 'Input Pembayaran Berhasil'),
(180, 1, '2025-11-28 03:39:45', 'Pembayaran', 'Input Pembayaran Berhasil'),
(181, 1, '2025-11-28 03:40:54', 'Pembayaran', 'Input Pembayaran Berhasil'),
(182, 1, '2025-11-28 03:42:31', 'Pembayaran', 'Input Pembayaran Berhasil'),
(183, 1, '2025-11-28 14:45:10', 'Login', 'Login Berhasil'),
(184, 1, '2025-11-28 20:20:00', 'Login', 'Login Berhasil'),
(185, 1, '2025-11-28 20:53:50', 'Pembayaran', 'Input Pembayaran Berhasil'),
(186, 1, '2025-11-29 14:32:11', 'Login', 'Login Berhasil'),
(187, 1, '2025-11-29 15:05:25', 'Pembayaran', 'Input Pembayaran Berhasil'),
(188, 1, '2025-11-29 16:45:43', 'Login', 'Login Berhasil'),
(189, 1, '2025-11-29 18:23:07', 'Pembayaran', 'Input Pembayaran Berhasil'),
(190, 1, '2025-11-29 18:45:34', 'Pembayaran', 'Input Pembayaran Berhasil'),
(191, 1, '2025-11-29 18:45:55', 'Pembayaran', 'Input Pembayaran Berhasil'),
(192, 1, '2025-11-29 18:48:41', 'Pembayaran', 'Input Pembayaran Berhasil'),
(193, 1, '2025-11-29 18:50:13', 'Pembayaran', 'Hapus Pembayaran Berhasil'),
(194, 1, '2025-11-29 18:51:34', 'Pembayaran', 'Input Pembayaran Berhasil'),
(195, 1, '2025-11-29 18:58:14', 'Tagihan', 'Hapus Tagihan Berhasil'),
(196, 1, '2025-11-29 19:00:40', 'Tagihan', 'Hapus Tagihan Berhasil'),
(197, 1, '2025-11-29 19:01:40', 'Tagihan', 'Hapus Tagihan Berhasil'),
(198, 1, '2025-11-29 20:33:40', 'Login', 'Login Berhasil'),
(199, 1, '2025-11-29 21:15:17', 'Pembayaran', 'Input Pembayaran Berhasil'),
(200, 1, '2025-11-29 22:12:51', 'Pembayaran', 'Input Pembayaran Berhasil'),
(201, 1, '2025-11-29 22:32:01', 'Pembayaran', 'Input Pembayaran Berhasil'),
(202, 1, '2025-11-29 22:34:09', 'Pembayaran', 'Input Pembayaran Berhasil'),
(203, 1, '2025-11-29 22:34:23', 'Pembayaran', 'Input Pembayaran Berhasil'),
(204, 1, '2025-11-29 22:34:31', 'Pembayaran', 'Input Pembayaran Berhasil'),
(205, 1, '2025-11-30 00:40:36', 'Login', 'Login Berhasil'),
(206, 1, '2025-11-30 03:55:18', 'Login', 'Login Berhasil'),
(207, 1, '2025-11-30 05:05:53', 'Login', 'Login Berhasil'),
(208, 1, '2025-11-30 19:09:11', 'Login', 'Login Berhasil'),
(209, 1, '2025-11-30 21:03:09', 'Periode Akademik', 'Input Periode Akademik Akses'),
(210, 1, '2025-11-30 21:06:24', 'Tahun Akademik', 'Hapus Tahun Akademik Berhasil'),
(211, 1, '2025-11-30 21:14:48', 'Periode Akademik', 'Update Periode Akademik ID 4'),
(212, 1, '2025-11-30 21:15:02', 'Periode Akademik', 'Update Periode Akademik ID 4'),
(213, 1, '2025-11-30 22:09:22', 'Periode Akademik', 'Update Periode Akademik ID 4'),
(214, 1, '2025-12-02 03:10:38', 'Login', 'Login Berhasil'),
(215, 1, '2025-12-02 03:52:47', 'Login', 'Login Berhasil'),
(216, 1, '2025-12-02 12:32:33', 'Login', 'Login Berhasil'),
(217, 1, '2025-12-02 16:18:25', 'Login', 'Login Berhasil'),
(218, 1, '2025-12-02 21:00:38', 'Login', 'Login Berhasil'),
(219, 1, '2025-12-03 09:13:27', 'Login', 'Login Berhasil'),
(220, 1, '2025-12-03 14:34:57', 'Login', 'Login Berhasil'),
(221, 1, '2025-12-03 15:55:54', 'Login', 'Login Berhasil'),
(222, 1, '2025-12-03 21:25:39', 'Login', 'Login Berhasil'),
(223, 1, '2025-12-04 00:35:27', 'Login', 'Login Berhasil'),
(224, 1, '2025-12-04 13:45:10', 'Login', 'Login Berhasil'),
(225, 1, '2025-12-05 01:04:09', 'Login', 'Login Berhasil'),
(226, 1, '2025-12-05 19:20:45', 'Login', 'Login Berhasil'),
(227, 1, '2025-12-05 21:41:07', 'Login', 'Login Berhasil'),
(228, 1, '2025-12-06 01:02:23', 'Login', 'Login Berhasil'),
(229, 1, '2025-12-06 16:24:01', 'Login', 'Login Berhasil'),
(230, 1, '2025-12-07 08:03:21', 'Login', 'Login Berhasil'),
(231, 1, '2025-12-07 22:41:32', 'Login', 'Login Berhasil'),
(232, 1, '2025-12-08 01:04:42', 'Login', 'Login Berhasil'),
(233, 1, '2025-12-08 18:00:43', 'Login', 'Login Berhasil'),
(234, 1, '2025-12-08 18:32:11', 'Fitur Akses', 'Hapus Fitur Akses'),
(235, 1, '2025-12-08 18:33:33', 'Akses', 'Input Fitur Akses'),
(236, 1, '2025-12-13 21:02:42', 'Login', 'Login Berhasil'),
(237, 1, '2025-12-14 02:12:40', 'Login', 'Login Berhasil'),
(238, 1, '2025-12-14 21:19:42', 'Login', 'Login Berhasil'),
(239, 1, '2025-12-15 03:08:36', 'Login', 'Login Berhasil'),
(240, 1, '2025-12-17 00:05:51', 'Login', 'Login Berhasil'),
(241, 1, '2025-12-17 01:15:29', 'Fitur Akses', 'Hapus Fitur Akses'),
(242, 1, '2025-12-17 01:15:38', 'Fitur Akses', 'Hapus Fitur Akses'),
(243, 1, '2025-12-17 01:15:42', 'Fitur Akses', 'Hapus Fitur Akses'),
(244, 1, '2025-12-17 01:15:46', 'Fitur Akses', 'Hapus Fitur Akses'),
(245, 1, '2025-12-17 01:15:53', 'Fitur Akses', 'Hapus Fitur Akses'),
(246, 1, '2025-12-17 01:16:00', 'Fitur Akses', 'Hapus Fitur Akses'),
(247, 1, '2025-12-17 01:16:05', 'Fitur Akses', 'Hapus Fitur Akses'),
(248, 1, '2025-12-17 03:07:41', 'Akses', 'Input Fitur Akses'),
(249, 1, '2025-12-17 03:07:56', 'Entitas Akses', 'Edit Entitas Akses'),
(250, 1, '2025-12-17 05:26:34', 'Login', 'Login Berhasil'),
(251, 1, '2025-12-17 16:17:16', 'Login', 'Login Berhasil'),
(252, 1, '2025-12-17 20:05:17', 'Login', 'Login Berhasil'),
(253, 1, '2025-12-17 21:10:02', 'Login', 'Login Berhasil'),
(254, 1, '2025-12-17 23:17:00', 'Login', 'Login Berhasil'),
(255, 1, '2025-12-18 01:10:49', 'Login', 'Login Berhasil'),
(256, 1, '2025-12-18 01:47:14', 'Akses', 'Input Fitur Akses'),
(257, 1, '2025-12-18 01:47:23', 'Entitas Akses', 'Edit Entitas Akses'),
(258, 1, '2025-12-18 02:55:19', 'Login', 'Login Berhasil'),
(259, 1, '2025-12-18 04:30:17', 'Akses', 'Input Fitur Akses'),
(260, 1, '2025-12-18 04:32:00', 'Entitas Akses', 'Hapus Entitas Akses'),
(261, 1, '2025-12-18 04:32:30', 'Entitas Akses', 'Edit Entitas Akses'),
(262, 1, '2025-12-19 16:35:46', 'Login', 'Login Berhasil'),
(263, 1, '2025-12-19 18:11:53', 'Login', 'Login Berhasil'),
(264, 1, '2025-12-19 23:22:57', 'Login', 'Login Berhasil'),
(265, 1, '2025-12-19 23:28:20', 'Akses', 'Input Fitur Akses'),
(266, 1, '2025-12-19 23:31:22', 'Entitas Akses', 'Edit Entitas Akses'),
(267, 1, '2025-12-20 02:17:09', 'Login', 'Login Berhasil'),
(268, 1, '2025-12-20 03:02:10', 'Akses', 'Input Fitur Akses'),
(269, 1, '2025-12-20 03:02:18', 'Entitas Akses', 'Edit Entitas Akses'),
(270, 1, '2025-12-20 22:05:27', 'Login', 'Login Berhasil'),
(271, 1, '2025-12-20 23:50:18', 'Login', 'Login Berhasil'),
(272, 1, '2025-12-21 03:28:14', 'Akses', 'Input Fitur Akses'),
(273, 1, '2025-12-21 03:30:49', 'Entitas Akses', 'Edit Entitas Akses'),
(274, 1, '2025-12-21 08:25:08', 'Login', 'Login Berhasil'),
(275, 1, '2025-12-21 08:27:29', 'Akses', 'Input Fitur Akses'),
(276, 1, '2025-12-21 11:18:43', 'Login', 'Login Berhasil'),
(277, 1, '2025-12-21 11:26:35', 'Entitas Akses', 'Edit Entitas Akses'),
(278, 1, '2025-12-21 18:29:50', 'Login', 'Login Berhasil'),
(279, 1, '2025-12-21 20:33:36', 'Login', 'Login Berhasil'),
(280, 1, '2025-12-22 04:57:27', 'Login', 'Login Berhasil'),
(281, 1, '2025-12-22 15:55:24', 'Login', 'Login Berhasil'),
(282, 1, '2025-12-22 21:11:27', 'Login', 'Login Berhasil'),
(283, 1, '2025-12-23 10:33:12', 'Login', 'Login Berhasil'),
(284, 1, '2025-12-23 14:15:35', 'Login', 'Login Berhasil'),
(285, 1, '2025-12-24 00:34:52', 'Login', 'Login Berhasil'),
(286, 1, '2025-12-24 05:33:52', 'Login', 'Login Berhasil'),
(287, 1, '2025-12-24 09:56:32', 'Login', 'Login Berhasil'),
(288, 8, '2025-12-24 10:13:53', 'Login', 'Login Berhasil'),
(289, 8, '2025-12-24 11:37:20', 'Login', 'Login Berhasil'),
(290, 8, '2025-12-24 11:38:04', 'Login', 'Login Berhasil'),
(291, 1, '2025-12-24 18:50:16', 'Login', 'Login Berhasil'),
(292, 1, '2025-12-25 03:06:28', 'Login', 'Login Berhasil'),
(293, 1, '2025-12-25 09:31:59', 'Login', 'Login Berhasil'),
(294, 1, '2025-12-25 14:47:50', 'Login', 'Login Berhasil'),
(295, 1, '2025-12-25 23:50:36', 'Login', 'Login Berhasil'),
(296, 1, '2025-12-26 13:44:17', 'Login', 'Login Berhasil'),
(297, 1, '2025-12-27 01:30:10', 'Login', 'Login Berhasil'),
(298, 1, '2025-12-27 08:09:39', 'Login', 'Login Berhasil'),
(299, 8, '2025-12-27 09:12:23', 'Login', 'Login Berhasil'),
(300, 8, '2025-12-27 10:54:42', 'Login', 'Login Berhasil'),
(301, 1, '2025-12-27 11:03:38', 'Login', 'Login Berhasil'),
(302, 8, '2025-12-27 13:04:43', 'Login', 'Login Berhasil'),
(303, 1, '2025-12-27 15:22:22', 'Login', 'Login Berhasil'),
(304, 1, '2025-12-27 18:57:48', 'Login', 'Login Berhasil'),
(305, 1, '2025-12-28 03:11:36', 'Login', 'Login Berhasil'),
(306, 1, '2025-12-28 08:27:38', 'Login', 'Login Berhasil'),
(307, 1, '2025-12-28 11:49:27', 'Login', 'Login Berhasil'),
(308, 1, '2025-12-28 15:37:22', 'Login', 'Login Berhasil'),
(309, 1, '2025-12-29 00:26:16', 'Login', 'Login Berhasil'),
(310, 1, '2025-12-29 01:51:49', 'Login', 'Login Berhasil'),
(311, 8, '2025-12-29 08:32:01', 'Login', 'Login Berhasil'),
(312, 1, '2025-12-29 09:00:57', 'Login', 'Login Berhasil'),
(313, 1, '2025-12-29 11:41:22', 'Login', 'Login Berhasil'),
(314, 1, '2025-12-29 11:58:06', 'Login', 'Login Berhasil'),
(315, 8, '2025-12-29 12:20:12', 'Login', 'Login Berhasil'),
(316, 1, '2025-12-29 12:30:43', 'Login', 'Login Berhasil'),
(317, 1, '2025-12-29 13:07:03', 'Login', 'Login Berhasil'),
(318, 1, '2025-12-29 15:35:14', 'Login', 'Login Berhasil'),
(319, 1, '2025-12-29 17:51:46', 'Login', 'Login Berhasil'),
(320, 1, '2025-12-30 05:07:06', 'Login', 'Login Berhasil'),
(321, 8, '2025-12-30 08:45:56', 'Login', 'Login Berhasil'),
(322, 8, '2025-12-30 13:38:01', 'Login', 'Login Berhasil'),
(323, 1, '2025-12-30 19:12:31', 'Login', 'Login Berhasil'),
(324, 1, '2025-12-30 20:26:02', 'Login', 'Login Berhasil'),
(325, 1, '2025-12-31 03:12:37', 'Login', 'Login Berhasil'),
(326, 1, '2025-12-31 03:58:40', 'Akses', 'Input Fitur Akses'),
(327, 1, '2025-12-31 04:01:02', 'Entitas Akses', 'Edit Entitas Akses'),
(328, 8, '2025-12-31 08:33:58', 'Login', 'Login Berhasil'),
(329, 1, '2025-12-31 14:34:50', 'Login', 'Login Berhasil'),
(330, 1, '2025-12-31 18:21:22', 'Login', 'Login Berhasil'),
(331, 1, '2026-01-01 06:27:29', 'Login', 'Login Berhasil'),
(332, 1, '2026-01-01 10:42:55', 'Login', 'Login Berhasil'),
(333, 1, '2026-01-01 12:53:29', 'Login', 'Login Berhasil'),
(334, 1, '2026-01-01 16:18:34', 'Login', 'Login Berhasil'),
(335, 1, '2026-01-01 17:35:06', 'Login', 'Login Berhasil'),
(336, 1, '2026-01-01 20:20:18', 'Login', 'Login Berhasil'),
(337, 1, '2026-01-01 22:10:07', 'Login', 'Login Berhasil'),
(338, 1, '2026-01-02 02:16:47', 'Login', 'Login Berhasil'),
(339, 1, '2026-01-02 16:47:54', 'Login', 'Login Berhasil'),
(340, 1, '2026-01-02 19:02:34', 'Login', 'Login Berhasil'),
(341, 1, '2026-01-02 20:03:57', 'Login', 'Login Berhasil'),
(342, 8, '2026-01-03 09:55:34', 'Login', 'Login Berhasil'),
(343, 1, '2026-01-03 16:55:05', 'Login', 'Login Berhasil'),
(344, 1, '2026-01-03 22:25:50', 'Login', 'Login Berhasil'),
(345, 1, '2026-01-04 08:16:52', 'Login', 'Login Berhasil'),
(346, 1, '2026-01-04 09:30:52', 'Login', 'Login Berhasil'),
(347, 1, '2026-01-04 14:12:53', 'Login', 'Login Berhasil'),
(348, 1, '2026-01-04 22:34:48', 'Login', 'Login Berhasil'),
(349, 1, '2026-01-05 02:19:43', 'Login', 'Login Berhasil'),
(350, 1, '2026-01-05 02:24:53', 'Login', 'Login Berhasil'),
(351, 1, '2026-01-05 04:55:27', 'Akses', 'Input Fitur Akses'),
(352, 1, '2026-01-05 04:56:48', 'Entitas Akses', 'Edit Entitas Akses'),
(353, 1, '2026-01-05 16:44:06', 'Login', 'Login Berhasil'),
(354, 1, '2026-01-06 05:47:27', 'Login', 'Login Berhasil'),
(355, 8, '2026-01-06 11:00:59', 'Login', 'Login Berhasil'),
(356, 8, '2026-01-06 13:08:30', 'Login', 'Login Berhasil'),
(357, 1, '2026-01-06 17:56:36', 'Login', 'Login Berhasil'),
(358, 1, '2026-01-06 22:20:55', 'Login', 'Login Berhasil'),
(359, 1, '2026-01-07 00:19:50', 'Login', 'Login Berhasil'),
(360, 1, '2026-01-07 01:32:34', 'Login', 'Login Berhasil'),
(361, 1, '2026-01-07 09:15:24', 'Login', 'Login Berhasil'),
(362, 1, '2026-01-07 10:23:51', 'Login', 'Login Berhasil'),
(363, 1, '2026-01-07 10:58:13', 'Login', 'Login Berhasil'),
(364, 1, '2026-01-07 11:06:07', 'Login', 'Login Berhasil'),
(365, 8, '2026-01-07 11:23:23', 'Login', 'Login Berhasil'),
(366, 1, '2026-01-07 12:35:43', 'Login', 'Login Berhasil'),
(367, 1, '2026-01-07 13:13:48', 'Login', 'Login Berhasil'),
(368, 1, '2026-01-07 14:43:57', 'Login', 'Login Berhasil'),
(369, 1, '2026-01-07 14:58:42', 'Login', 'Login Berhasil'),
(370, 1, '2026-01-07 16:18:00', 'Login', 'Login Berhasil'),
(371, 1, '2026-01-07 16:29:13', 'Login', 'Login Berhasil'),
(372, 8, '2026-01-08 11:35:27', 'Login', 'Login Berhasil'),
(373, 1, '2026-01-08 17:43:41', 'Login', 'Login Berhasil'),
(374, 1, '2026-01-08 20:10:22', 'Login', 'Login Berhasil'),
(375, 1, '2026-01-08 22:57:10', 'Login', 'Login Berhasil'),
(376, 1, '2026-01-09 01:29:56', 'Login', 'Login Berhasil'),
(377, 1, '2026-01-09 02:18:15', 'Fitur Akses', 'Hapus Fitur Akses'),
(378, 1, '2026-01-09 02:18:25', 'Fitur Akses', 'Hapus Fitur Akses'),
(379, 1, '2026-01-09 02:19:07', 'Akses', 'Input Fitur Akses'),
(380, 1, '2026-01-09 02:33:26', 'Entitas Akses', 'Edit Entitas Akses'),
(381, 1, '2026-01-09 02:33:48', 'Fitur Akses', 'Hapus Fitur Akses'),
(382, 1, '2026-01-09 02:33:54', 'Fitur Akses', 'Hapus Fitur Akses'),
(383, 1, '2026-01-09 02:34:01', 'Fitur Akses', 'Hapus Fitur Akses'),
(384, 1, '2026-01-10 16:14:48', 'Login', 'Login Berhasil'),
(385, 1, '2026-01-10 17:45:53', 'Login', 'Login Berhasil'),
(386, 1, '2026-01-10 21:32:15', 'Login', 'Login Berhasil'),
(387, 1, '2026-01-11 04:15:52', 'Akses', 'Input Fitur Akses'),
(388, 1, '2026-01-11 04:16:47', 'Entitas Akses', 'Edit Entitas Akses'),
(389, 1, '2026-01-11 17:17:53', 'Login', 'Login Berhasil'),
(390, 1, '2026-01-11 20:33:55', 'Login', 'Login Berhasil'),
(391, 1, '2026-01-11 22:13:20', 'Akses', 'Input Fitur Akses'),
(392, 1, '2026-01-11 22:14:17', 'Entitas Akses', 'Edit Entitas Akses'),
(393, 1, '2026-01-12 00:34:25', 'Login', 'Login Berhasil'),
(394, 1, '2026-01-12 01:38:06', 'Login', 'Login Berhasil'),
(395, 1, '2026-01-12 04:07:20', 'Login', 'Login Berhasil'),
(396, 1, '2026-01-12 04:13:44', 'Akses', 'Input Fitur Akses'),
(397, 1, '2026-01-12 04:13:52', 'Entitas Akses', 'Edit Entitas Akses'),
(398, 1, '2026-01-12 23:05:03', 'Login', 'Login Berhasil'),
(399, 1, '2026-01-13 01:46:37', 'Login', 'Login Berhasil'),
(400, 1, '2026-01-13 14:40:02', 'Login', 'Login Berhasil'),
(401, 1, '2026-01-13 16:17:12', 'Login', 'Login Berhasil'),
(402, 1, '2026-01-13 20:44:19', 'Login', 'Login Berhasil'),
(403, 1, '2026-01-16 03:02:50', 'Login', 'Login Berhasil'),
(404, 1, '2026-01-19 18:18:56', 'Login', 'Login Berhasil'),
(405, 1, '2026-01-24 01:23:36', 'Login', 'Login Berhasil'),
(406, 1, '2026-01-24 02:50:05', 'Akses', 'Input Fitur Akses'),
(407, 1, '2026-01-24 02:50:39', 'Entitas Akses', 'Edit Entitas Akses'),
(408, 1, '2026-01-24 06:17:17', 'Login', 'Login Berhasil'),
(409, 1, '2026-01-24 06:34:53', 'Login', 'Login Berhasil'),
(410, 1, '2026-01-24 20:37:46', 'Login', 'Login Berhasil'),
(411, 1, '2026-01-25 00:28:19', 'Login', 'Login Berhasil'),
(412, 1, '2026-01-25 02:46:28', 'Login', 'Login Berhasil'),
(413, 1, '2026-01-25 05:45:52', 'Login', 'Login Berhasil'),
(414, 1, '2026-01-25 21:26:54', 'Login', 'Login Berhasil'),
(415, 1, '2026-01-25 21:54:40', 'Login', 'Login Berhasil'),
(416, 1, '2026-01-26 00:58:51', 'Akses', 'Input Fitur Akses'),
(417, 1, '2026-01-26 01:05:41', 'Entitas Akses', 'Edit Entitas Akses'),
(418, 1, '2026-01-26 21:36:41', 'Login', 'Login Berhasil'),
(419, 1, '2026-01-26 22:51:05', 'Login', 'Login Berhasil'),
(420, 1, '2026-01-27 20:56:05', 'Login', 'Login Berhasil'),
(421, 1, '2026-01-27 22:31:52', 'Entitas Akses', 'Input Entitas Akses'),
(422, 1, '2026-01-28 01:55:44', 'Login', 'Login Berhasil'),
(423, 1, '2026-01-28 05:24:35', 'Login', 'Login Berhasil'),
(424, 1, '2026-01-28 19:33:00', 'Login', 'Login Berhasil'),
(425, 1, '2026-01-28 21:05:23', 'Login', 'Login Berhasil'),
(426, 1, '2026-01-28 22:10:11', 'Login', 'Login Berhasil'),
(427, 1, '2026-01-28 23:56:25', 'Akses', 'Input Fitur Akses'),
(428, 1, '2026-01-28 23:59:44', 'Entitas Akses', 'Edit Entitas Akses'),
(429, 1, '2026-01-29 01:45:55', 'Akses', 'Input Fitur Akses'),
(430, 1, '2026-01-29 01:48:22', 'Entitas Akses', 'Edit Entitas Akses'),
(431, 1, '2026-01-29 05:24:11', 'Login', 'Login Berhasil'),
(432, 1, '2026-01-29 05:25:55', 'Login', 'Login Berhasil'),
(433, 1, '2026-01-30 00:41:33', 'Login', 'Login Berhasil'),
(434, 1, '2026-01-30 20:06:49', 'Login', 'Login Berhasil'),
(435, 1, '2026-01-30 21:30:55', 'Login', 'Login Berhasil'),
(436, 1, '2026-01-31 00:33:10', 'Login', 'Login Berhasil'),
(437, 1, '2026-02-05 05:20:36', 'Login', 'Login Berhasil'),
(438, 1, '2026-02-06 04:59:08', 'Login', 'Login Berhasil'),
(439, 1, '2026-02-06 05:04:12', 'Login', 'Login Berhasil'),
(440, 1, '2026-02-06 23:46:10', 'Login', 'Login Berhasil'),
(441, 1, '2026-02-07 00:54:44', 'Login', 'Login Berhasil'),
(442, 1, '2026-02-07 02:16:43', 'Login', 'Login Berhasil'),
(443, 1, '2026-02-07 02:20:23', 'Fitur Akses', 'Hapus Fitur Akses'),
(444, 1, '2026-02-07 02:20:27', 'Fitur Akses', 'Hapus Fitur Akses'),
(445, 1, '2026-02-07 02:20:31', 'Fitur Akses', 'Hapus Fitur Akses'),
(446, 1, '2026-02-07 02:20:36', 'Fitur Akses', 'Hapus Fitur Akses'),
(447, 1, '2026-02-07 02:20:40', 'Fitur Akses', 'Hapus Fitur Akses'),
(448, 1, '2026-02-07 02:20:44', 'Fitur Akses', 'Hapus Fitur Akses'),
(449, 1, '2026-02-07 02:20:48', 'Fitur Akses', 'Hapus Fitur Akses'),
(450, 1, '2026-02-07 02:20:52', 'Fitur Akses', 'Hapus Fitur Akses'),
(451, 1, '2026-02-07 02:21:10', 'Entitas Akses', 'Hapus Entitas Akses'),
(452, 1, '2026-02-07 02:21:14', 'Entitas Akses', 'Hapus Entitas Akses'),
(453, 1, '2026-02-07 02:21:17', 'Entitas Akses', 'Hapus Entitas Akses'),
(454, 1, '2026-02-07 02:48:55', 'Login', 'Login Berhasil'),
(455, 1, '2026-02-07 03:04:38', 'Login', 'Login Berhasil'),
(456, 1, '2026-02-07 20:30:54', 'Login', 'Login Berhasil'),
(457, 1, '2026-02-07 21:51:54', 'Login', 'Login Berhasil'),
(458, 1, '2026-02-07 21:56:05', 'Akses', 'Input Fitur Akses'),
(459, 1, '2026-02-07 22:13:15', 'Entitas Akses', 'Edit Entitas Akses'),
(460, 1, '2026-02-08 00:41:46', 'Login', 'Login Berhasil'),
(461, 1, '2026-02-08 05:54:04', 'Akses', 'Input Fitur Akses'),
(462, 1, '2026-02-08 05:55:59', 'Entitas Akses', 'Edit Entitas Akses'),
(463, 1, '2026-02-08 13:00:09', 'Login', 'Login Berhasil'),
(464, 1, '2026-02-08 14:20:57', 'Akses', 'Input Fitur Akses'),
(465, 1, '2026-02-08 22:17:34', 'Login', 'Login Berhasil'),
(466, 1, '2026-02-08 22:45:34', 'Entitas Akses', 'Edit Entitas Akses'),
(467, 1, '2026-02-09 01:40:37', 'Login', 'Login Berhasil'),
(468, 1, '2026-02-09 04:50:23', 'Login', 'Login Berhasil'),
(469, 1, '2026-02-09 14:09:24', 'Login', 'Login Berhasil'),
(470, 1, '2026-02-09 16:51:15', 'Login', 'Login Berhasil'),
(471, 1, '2026-02-09 17:03:48', 'Akses', 'Input Fitur Akses'),
(472, 1, '2026-02-09 17:04:01', 'Entitas Akses', 'Edit Entitas Akses'),
(473, 1, '2026-02-09 18:32:50', 'Akses', 'Input Fitur Akses'),
(474, 1, '2026-02-09 18:32:58', 'Entitas Akses', 'Edit Entitas Akses'),
(475, 1, '2026-02-10 02:11:39', 'Login', 'Login Berhasil'),
(476, 1, '2026-02-10 02:41:38', 'Akses', 'Input Fitur Akses'),
(477, 1, '2026-02-10 02:44:44', 'Entitas Akses', 'Edit Entitas Akses'),
(478, 1, '2026-02-10 20:00:02', 'Login', 'Login Berhasil'),
(479, 1, '2026-02-10 22:19:56', 'Login', 'Login Berhasil'),
(480, 1, '2026-02-11 01:23:22', 'Login', 'Login Berhasil'),
(481, 1, '2026-02-11 12:55:33', 'Login', 'Login Berhasil'),
(482, 1, '2026-02-11 17:31:53', 'Login', 'Login Berhasil');

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
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access_login`
--

INSERT INTO `access_login` (`id_access_login`, `id_access`, `token`, `datetime_creat`, `datetime_expired`) VALUES
(51, 2, 'D4hbO8ZH3g4UZWJXy6ZhcWt1qzu8DEX2ILFx', '2025-09-13 08:49:27', '2025-09-13 10:33:46'),
(306, 8, '1GNwTfgziYVhHj8QPubz96G0LJlocAGfSaz4', '2026-01-08 11:35:26', '2026-01-08 12:36:36'),
(371, 1, 'Ptcx3WZW5JC6sobim4fSq7L5bnIGF8NAKE5B', '2026-02-11 17:31:52', '2026-02-11 19:24:35');

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
) ENGINE=InnoDB AUTO_INCREMENT=498 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access_permission`
--

INSERT INTO `access_permission` (`id_permission`, `id_access`, `id_access_feature`) VALUES
(407, 2, 'jO3M0NopVQeXi4VuDHpvD9SRJzntpUGAe6Sw'),
(408, 2, 'lInyeHHg924zNLaXZ3SmjjnuyCOYBnUyUuTD'),
(409, 2, 'nSYinRWpCF9MHNUIlW7Up5vTip70gNNLlrqv'),
(410, 2, 'nkYXm3U8XWpOt1cD3PNeCwDQzesMYmmUUbee'),
(411, 2, '5a7yRbkFPs6fXNHQf8a7bI79IZcbbIaijE0E'),
(412, 2, '36grsDsU11UKOCFPKlh5Gx7K2YbR6XpRHJ5y'),
(413, 2, 'Dnd2UZLzazCqJ9WfuzQKlIOpYueb2fXxNHXA'),
(414, 2, 'aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY'),
(415, 2, 'Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH'),
(416, 2, 'fErKPHIY6bEuhp7sOivMHglXHOP2gVubzGyw'),
(482, 1, 'jO3M0NopVQeXi4VuDHpvD9SRJzntpUGAe6Sw'),
(483, 1, 'lInyeHHg924zNLaXZ3SmjjnuyCOYBnUyUuTD'),
(484, 1, 'nSYinRWpCF9MHNUIlW7Up5vTip70gNNLlrqv'),
(485, 1, 'nkYXm3U8XWpOt1cD3PNeCwDQzesMYmmUUbee'),
(486, 1, '5a7yRbkFPs6fXNHQf8a7bI79IZcbbIaijE0E'),
(487, 1, '36grsDsU11UKOCFPKlh5Gx7K2YbR6XpRHJ5y'),
(488, 1, 'Dnd2UZLzazCqJ9WfuzQKlIOpYueb2fXxNHXA'),
(489, 1, 'aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY'),
(490, 1, 'Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH'),
(491, 1, 'fErKPHIY6bEuhp7sOivMHglXHOP2gVubzGyw'),
(492, 1, 'lgG3CggWuy9Bd3m4eaXXx6tjKQonITqt4MOe'),
(493, 1, 'H8lByxYVLw1zYg9hIYkZxtNNgkBH8Gi8h6Vv'),
(494, 1, '8bOwARsJKZ5Dc0VxJwXdWdiP2KPfxFjVqgbu'),
(495, 1, '6W5aMQEkhaBfwBGXQOEQx7M04Iv9h8IXOEsT'),
(496, 1, 'wW79JNUwhM5nxRymMuxQrycBpUkBRAt2r2UU'),
(497, 1, 'vA2qgCIl2YHVsxGmocRcv5293dcXh5oDXVYt');

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
) ENGINE=InnoDB AUTO_INCREMENT=140 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access_reference`
--

INSERT INTO `access_reference` (`id_access_reference`, `id_access_group`, `id_access_feature`) VALUES
(124, 1, 'jO3M0NopVQeXi4VuDHpvD9SRJzntpUGAe6Sw'),
(125, 1, 'lInyeHHg924zNLaXZ3SmjjnuyCOYBnUyUuTD'),
(126, 1, 'nSYinRWpCF9MHNUIlW7Up5vTip70gNNLlrqv'),
(127, 1, 'nkYXm3U8XWpOt1cD3PNeCwDQzesMYmmUUbee'),
(128, 1, '5a7yRbkFPs6fXNHQf8a7bI79IZcbbIaijE0E'),
(129, 1, '36grsDsU11UKOCFPKlh5Gx7K2YbR6XpRHJ5y'),
(130, 1, 'Dnd2UZLzazCqJ9WfuzQKlIOpYueb2fXxNHXA'),
(131, 1, 'aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY'),
(132, 1, 'Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH'),
(133, 1, 'fErKPHIY6bEuhp7sOivMHglXHOP2gVubzGyw'),
(134, 1, 'lgG3CggWuy9Bd3m4eaXXx6tjKQonITqt4MOe'),
(135, 1, 'H8lByxYVLw1zYg9hIYkZxtNNgkBH8Gi8h6Vv'),
(136, 1, '8bOwARsJKZ5Dc0VxJwXdWdiP2KPfxFjVqgbu'),
(137, 1, '6W5aMQEkhaBfwBGXQOEQx7M04Iv9h8IXOEsT'),
(138, 1, 'wW79JNUwhM5nxRymMuxQrycBpUkBRAt2r2UU'),
(139, 1, 'vA2qgCIl2YHVsxGmocRcv5293dcXh5oDXVYt');

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access_reset`
--

INSERT INTO `access_reset` (`id_access_reset`, `id_access`, `datetime_creat`, `token`) VALUES
(1, 1, '2025-11-10 01:39:04', 'hMUik7SIDyT9H1a00wUlKnAAd2N3OiKQrCz8'),
(2, 1, '2025-11-10 01:48:27', 'Q7v2lJCcwr78O91DtB79ufKYd1dMharLJEhP'),
(3, 1, '2025-11-10 01:49:24', 'jUM1YQByDUmhIZMp5eXLGqc7qr2IwRDL165l');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `app_configuration`
--

INSERT INTO `app_configuration` (`id_configuration`, `app_title`, `app_keyword`, `app_description`, `app_favicon`, `app_logo`, `app_base_url`, `app_author`, `app_year`, `app_company`) VALUES
(1, 'Analyza V1.0', '[\"Laboratory Information Management System\"]', 'A Laboratory Information Management System (LIMS) is software designed to modernize laboratory operations by automating workflows, tracking samples from collection to disposal, and managing data', '6b8c6fafef7972e19e51471d9a1386.png', '26802d057d82c89ca18070029ede00.png', 'http://localhost/Analyza', 'Solihul Hadi', 2026, '{\"company_code\": \"0124R006\", \"company_name\": \"RSU El-Syifa Kuningan\", \"company_email\": \"hallo.rsuelsyifa@gmail.com\", \"company_address\": \"Jalan RE Martadinata No.128 Kelurahan Ancaran Kabupaten Kuningan\", \"company_contact\": \"(0232) 876240\"}');

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
) ENGINE=InnoDB AUTO_INCREMENT=5816 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `connection_satu_sehat`
--

INSERT INTO `connection_satu_sehat` (`id_connection_satu_sehat`, `name_connection_satu_sehat`, `url_connection_satu_sehat`, `organization_id`, `client_key`, `secret_key`, `token`, `datetime_expired`, `status_connection_satu_sehat`) VALUES
(1, 'Sanbox', 'https://api-satusehat-stg.dto.kemkes.go.id', '2fb97f51-a536-4fc1-a4ff-2f7abbbc54aa', 'OzSEGR88d1fbTrX3eYIfg05qAFuwe4mGvhebeavID6H1aazj', 'dJlUWRW5eP01dpiDwGs2LGbIVUOEa2avaWWbQ2a7rbolGd7HfJPVYWjBudkz3BcG', '', NULL, 0),
(2, 'Production', 'https://api-satusehat.kemkes.go.id', '100026947', 'FRHoqgpmrnCcJ3rNAP0kBFsGphWAsAC19EY1f1yRBYvS6CPn', '1yj3cj2eG1h1zrcGSd6yVmGv1FJfBTf62LmPk5540tD9pryzFEMgoN9NF5XK5QEO', 'OyBOliuPzQbjN33HlN6fMviRpG8w', '2026-01-31 09:01:28', 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `connection_simrs`
--

INSERT INTO `connection_simrs` (`id_connection_simrs`, `name_connection_simrs`, `url_connection_simrs`, `client_id`, `client_key`, `token`, `datetime_expired`, `status_connection_simrs`) VALUES
(1, 'Development', 'http://localhost/SIMRS-ELSYIFA2', 'QsNNfBNpnOyusA13lj4GLILcFFs5ibdLuEXu', 'cwOhnoIl1UuXJj3ICUpu8H2QXvXCXZj3nPKJ', '', '0000-00-00 00:00:00', 0),
(2, 'Production', 'http://localhost/SIMRS-ELSYIFA2', 'wl9bzskrr6mC7U8kivWkSLcwcXrs0tzLdKwp', 'xrI9lAI14TTy2TD4wmDo3w09ovzqw5MYsJ6P', '', '0000-00-00 00:00:00', 0),
(5, 'Staging', 'http://localhost/SIMRS-ELSYIFA2', 'wl9bzskrr6mC7U8kivWkSLcwcXrs0tzLdKwp', 'xrI9lAI14TTy2TD4wmDo3w09ovzqw5MYsJ6P', '0f2f67f896745ff4d97a9f24c4dc9752', '2026-02-05 22:25:46', 1);

-- --------------------------------------------------------

--
-- Table structure for table `referensi_category`
--

DROP TABLE IF EXISTS `referensi_category`;
CREATE TABLE IF NOT EXISTS `referensi_category` (
  `id_referensi_category` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_referensi_pemeriksaan` int UNSIGNED NOT NULL,
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
  PRIMARY KEY (`id_referensi_category`),
  KEY `category_to_ref_pemeriksaan` (`id_referensi_pemeriksaan`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `referensi_category`
--

INSERT INTO `referensi_category` (`id_referensi_category`, `id_referensi_pemeriksaan`, `umur_kategori`, `umur_min`, `umur_max`, `umur_unit`, `jenis_kelamin`, `nilai_hasil`, `label`, `fhir_display`, `fhir_code`, `fhir_system`) VALUES
(1, 6, '', 0, 0, '', 'All', 'A+', 'Golongan A Rh Positif', 'Blood group A Rh positive', '112144000', 'http://snomed.info/sct'),
(2, 6, '', 0, 0, '', 'All', 'B+', 'Golongan B Rh Positif', 'Blood group B Rh positive', '112149005', 'http://snomed.info/sct'),
(3, 6, '', 0, 0, '', 'All', 'AB+', 'Golongan Darah AB+', 'Blood group AB Rh positive', '165743006', 'http://snomed.info/sct');

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `referensi_container`
--

INSERT INTO `referensi_container` (`id_referensi_container`, `nama_container`, `display_container`, `code_container`, `system_container`, `kapasitas_container`, `unit_container`, `code_unit_container`, `system_unit_container`) VALUES
(1, 'Tabung EDTA (Ungu)', 'Blood specimen tube with EDTA', '706054008', 'http://snomed.info/sct', 2.00, 'mL', 'mL', 'http://unitsofmeasure.org'),
(2, 'Tabung Serum (Merah)', 'Blood specimen tube with clot activator', '706047007', 'http://snomed.info/sct', 5.00, 'mL', 'mL', 'http://unitsofmeasure.org'),
(3, 'Tabung Sitrat (Biru)', 'Blood specimen tube with sodium citrate', '706055009', 'http://snomed.info/sct', 2.70, 'mL', 'mL', 'http://unitsofmeasure.org'),
(4, 'Tabung Heparin (Hijau)', 'Blood specimen tube with lithium heparin', '706051000', 'http://snomed.info/sct', 4.00, 'mL', 'mL', 'http://unitsofmeasure.org'),
(5, 'Tabung Glukosa (Abu)', 'Blood specimen tube with fluoride', '706044000', 'http://snomed.info/sct', 2.00, 'mL', 'mL', 'http://unitsofmeasure.org'),
(7, 'Wadah Urin Bersih', 'Urine specimen container', '706058006', 'http://snomed.info/sct', 50.00, 'mL', 'mL', 'http://unitsofmeasure.org');

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `referensi_jenis_spesimen`
--

INSERT INTO `referensi_jenis_spesimen` (`id_referensi_jenis_spesimen`, `nama_spesimen`, `display_spesimen`, `code_spesimen`, `system_spesimen`) VALUES
(1, 'Darah Lengkap', 'Whole blood specimen', '119297000', 'http://snomed.info/sct'),
(2, 'Serum', 'Serum specimen', '119364003', 'http://snomed.info/sct'),
(3, 'Plasma', 'Plasma specimen', '119361006', 'http://snomed.info/sct');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `referensi_metode_pemeriksaan`
--

INSERT INTO `referensi_metode_pemeriksaan` (`id_referensi_metode_pemeriksaan`, `nama_metode_pemeriksaan`, `display_metode_pemeriksaan`, `code_metode_pemeriksaan`, `system_metode_pemeriksaan`) VALUES
(1, 'Uji Golongan Darah', 'Blood grouping test', '104177005', 'http://snomed.info/sct'),
(2, 'Aglutinasi Slide', 'Slide agglutination test', '252275004', 'http://snomed.info/sct');

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `referensi_metode_sample`
--

INSERT INTO `referensi_metode_sample` (`id_referensi_metode_sample`, `nama_metode_sample`, `display_metode_sample`, `code_metode_sample`, `system_metode_sample`) VALUES
(1, 'Aspirasi', 'Aspiration - action', '129304002', 'http://snomed.info/sct'),
(2, 'Biopsi', 'Biopsy - action', '129314006', 'http://snomed.info/sct'),
(5, 'Pungsi Vena (Phlebotomy)', 'Venipuncture - action', '28520004', 'http://snomed.info/sct'),
(6, 'Kerokan (Scraping)', 'Scraping - action', '129323009', 'http://snomed.info/sct'),
(7, 'Eksisi', 'Excision - action', '129300006', 'http://snomed.info/sct'),
(8, 'Usap (Swab)', 'Swabbing - action', '129316008', 'http://snomed.info/sct'),
(9, 'Pengumpulan Urine (Voided)', 'Collection of urinary specimen, voided', '225271002', 'http://snomed.info/sct'),
(10, 'Kateterisasi', 'Catheterization - action', '129307009', 'http://snomed.info/sct'),
(11, 'Insisi', 'Incision - action', '129303008', 'http://snomed.info/sct'),
(12, 'Drainase', 'Drainage - action', '129311007', 'http://snomed.info/sct');

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `referensi_pemeriksaan`
--

INSERT INTO `referensi_pemeriksaan` (`id_referensi_pemeriksaan`, `nama_pemeriksaan`, `category_pemeriksaan`, `code_pemeriksaan`, `display_pemeriksaan`, `system_pemeriksaan`, `unit`, `unit_display`, `unit_code`, `unit_system`, `result_type`, `result_interpertation_type`, `allow_age`, `allow_sex`) VALUES
(5, 'Hemoglobin', 'Hematologi', '718-7', 'Hemoglobin [Mass/volume] in Blood', 'http://loinc.org', 'Gram per Desiliter', 'g/dL', 'g/dL', 'http://unitsofmeasure.org', 'Decimal', 'Range', 1, 0),
(6, 'Golongan Darah', 'Hematologi', '882-1', 'ABO and Rh group [Type] in Blood', 'http://loinc.org', NULL, NULL, NULL, NULL, 'Coded', 'Category', NULL, NULL),
(7, 'Warna Urin', 'Urin', '5778-6', 'Color of Urine', 'http://loinc.org', NULL, NULL, NULL, NULL, 'Text', 'None', 1, 1),
(8, 'Glukosa Puasa', 'Hematologi', '1558-6', 'Glucose [Mass/volume] in Serum or Plasma --fasting', 'http://loinc.org', 'Miligram per Desiliter', 'mg/dL', 'mg/dL', 'http://unitsofmeasure.org', 'Decimal', 'Range', 0, 0),
(9, 'Trombosit', 'Hematologi', '777-3', 'Platelet Count (Automated)', 'http://loinc.org', 'Ribuan per Mikroliter', '10^3/µL', '10*3/uL', 'http://unitsofmeasure.org', 'Decimal', 'Range', 0, 0);

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
  `umur_kategori` varchar(255) DEFAULT NULL COMMENT 'Prematur, Neonatus, Dewasa, Lansia DLL',
  `umur_min` int UNSIGNED DEFAULT NULL,
  `umur_max` int UNSIGNED DEFAULT NULL,
  `umur_unit` enum('Hari','Bulan','Tahun') DEFAULT NULL COMMENT 'Hari, Bulan, Tahun',
  `jenis_kelamin` enum('Laki-laki','Perempuan','All') NOT NULL COMMENT 'Laki-laki, Perempuan, All',
  `nilai_min` decimal(15,2) DEFAULT NULL,
  `nilai_max` decimal(15,2) DEFAULT NULL,
  `operator` enum('<','>','between','<=','>=','-') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `label` varchar(255) NOT NULL COMMENT 'Normal, Tinggi, Rendah, Sedang, Abnormal',
  `fhir_display` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Low, High, Normal, DLL',
  `fhir_code` varchar(255) NOT NULL,
  `fhir_system` text COMMENT 'http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation',
  `conclusion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Normal, Abnormal',
  PRIMARY KEY (`id_referensi_range`),
  KEY `id_referensi_pemeriksaan` (`id_referensi_pemeriksaan`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `referensi_range`
--

INSERT INTO `referensi_range` (`id_referensi_range`, `id_referensi_pemeriksaan`, `umur_kategori`, `umur_min`, `umur_max`, `umur_unit`, `jenis_kelamin`, `nilai_min`, `nilai_max`, `operator`, `label`, `fhir_display`, `fhir_code`, `fhir_system`, `conclusion`) VALUES
(1, 5, 'Neonatus', 0, 7, 'Hari', 'All', 14.50, 22.50, '-', 'Normal', 'Normal', 'N', 'http://snomed.info/sct', 'Normal'),
(2, 5, 'Neonatus', 0, 7, 'Hari', 'All', 14.50, 0.00, '<', 'Anemia Neonatal', 'Anemia Neonatal', '271737000', 'http://snomed.info/sct', 'Abnormal'),
(3, 5, 'Neonatus', 0, 7, 'Hari', 'All', 0.00, 22.50, '>', 'Polisitemia', 'Polisitemia', '109992005', 'http://snomed.info/sct', 'Abnormal'),
(5, 9, '', 0, 0, '', 'All', 20000.00, 0.00, '<', 'Sangat Rendah', 'Critical low', 'LL', 'http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation', 'Trombositopenia Berat'),
(6, 9, '', 0, 0, '', 'All', 20000.00, 150000.00, '-', 'Rendah', 'Low', 'L', 'http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation', 'Trombositopenia'),
(7, 9, '', 0, 0, '', 'All', 150000.00, 450000.00, 'between', 'Normal', 'Normal', 'N', 'http://snomed.info/sct', 'Normal'),
(8, 9, '', 0, 0, '', 'All', 0.00, 450000.00, '>=', 'Tinggi', 'High', 'H', 'http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation', 'Trombositosis');

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
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `referensi_satuan`
--

INSERT INTO `referensi_satuan` (`id_referensi_satuan`, `nama_satuan`, `unit_satuan`, `code_satuan`, `system_satuan`) VALUES
(1, 'Gram per Desiliter', 'g/dL', 'g/dL', 'http://unitsofmeasure.org'),
(2, 'Miligram per Desiliter', 'mg/dL', 'mg/dL', 'http://unitsofmeasure.org'),
(3, 'Miligram per 24 Jam', 'mg/24h', 'mg/24h', 'http://unitsofmeasure.org'),
(4, 'Mikrogram per Desiliter', 'µg/dL', 'ug/dL', 'http://unitsofmeasure.org'),
(5, 'Unit per Liter', 'U/L', 'U/L', 'http://unitsofmeasure.org'),
(6, 'Miliekuivalen per Liter', 'mEq/L', 'meq/L', 'http://unitsofmeasure.org'),
(7, 'Milimol per Liter', 'mmol/L', 'mmol/L', 'http://unitsofmeasure.org'),
(8, 'Persen', '%', '%', 'http://unitsofmeasure.org'),
(9, 'Jutaan per Mikroliter', '10^6/µL', '10*6/uL', 'http://unitsofmeasure.org'),
(10, 'Ribuan per Mikroliter', '10^3/µL', '10*3/uL', 'http://unitsofmeasure.org'),
(11, 'Femtoliter', 'fL', 'fL', 'http://unitsofmeasure.org'),
(12, 'Pikogram', 'pg', 'pg', 'http://unitsofmeasure.org'),
(13, 'Unit Internasional', 'IU/mL', '[IU]/mL', 'http://unitsofmeasure.org'),
(14, 'Kopi per Mililiter', 'copies/mL', '{copies}/mL', 'http://unitsofmeasure.org'),
(15, 'Milimol per Mol', 'mmol/mol', 'mmol/mol', 'http://unitsofmeasure.org'),
(16, 'Milimeter per Jam', 'mm/h', 'mm/h', 'http://unitsofmeasure.org'),
(17, 'Milimeter Air Raksa', 'mmHg', 'mm[Hg]', 'http://unitsofmeasure.org'),
(18, 'Nanogram per Mililiter', 'ng/mL', 'ng/mL', 'http://unitsofmeasure.org'),
(19, 'Mikromol per Liter', 'µmol/L', 'umol/L', 'http://unitsofmeasure.org'),
(20, 'Miliosmol per Kilogram', 'mOsm/kg', 'mosm/kg', 'http://unitsofmeasure.org'),
(21, 'Gram', 'g', 'g', 'http://unitsofmeasure.org'),
(22, 'Titer', 'Titer', '{titer}', 'http://unitsofmeasure.org'),
(23, 'Indeks', 'Index', '{index}', 'http://unitsofmeasure.org'),
(24, 'Rasio', 'Ratio', '{ratio}', 'http://unitsofmeasure.org'),
(25, 'Unit per Mililiter', 'U/mL', 'U/mL', 'http://unitsofmeasure.org'),
(26, 'Logaritma (Basis 10)', 'log10', '[log10]', 'http://unitsofmeasure.org'),
(27, 'Kilopascal', 'kPa', 'kPa', 'http://unitsofmeasure.org'),
(28, 'Mikroampere', 'µA', 'uA', 'http://unitsofmeasure.org'),
(30, 'Unit Internasional per Liter', 'IU/L', '[IU]/L', 'http://unitsofmeasure.org'),
(31, 'Liter', 'L', 'L', 'http://unitsofmeasure.org'),
(32, 'Mililiter', 'mL', 'mL', 'http://unitsofmeasure.org'),
(33, 'Mikroliter', 'µL', 'uL', 'http://unitsofmeasure.org'),
(34, 'Miligram', 'mg', 'mg', 'http://unitsofmeasure.org'),
(35, 'Mikrogram', 'µg', 'ug', 'http://unitsofmeasure.org'),
(36, 'Kilogram', 'kg', 'kg', 'http://unitsofmeasure.org'),
(37, 'Meter', 'm', 'm', 'http://unitsofmeasure.org'),
(38, 'Sentimeter', 'cm', 'cm', 'http://unitsofmeasure.org'),
(39, 'Milimeter', 'mm', 'mm', 'http://unitsofmeasure.org'),
(40, 'Mol', 'mol', 'mol', 'http://unitsofmeasure.org'),
(41, 'Milimol', 'mmol', 'mmol', 'http://unitsofmeasure.org'),
(42, 'Detik', 's (sec)', 's', 'http://unitsofmeasure.org'),
(43, 'Menit', 'min', 'min', 'http://unitsofmeasure.org'),
(44, 'Jam', 'h', 'h', 'http://unitsofmeasure.org'),
(45, 'Hari', 'd', 'd', 'http://unitsofmeasure.org');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `setting_email_gateway`
--

INSERT INTO `setting_email_gateway` (`id_setting_email_gateway`, `email_gateway`, `password_gateway`, `url_provider`, `port_gateway`, `nama_pengirim`, `url_service`, `validasi_email`, `redirect_validasi`, `pesan_validasi_email`) VALUES
(1, 'admin@kdmppadamukti.web.id', 'Padamukti1971#@', 'smtp.hostinger.com', '465', 'Admin Pay Siswa', 'https://mailer.kdmppadamukti.web.id/', 'No', '', 'Berikut ini kami kirimkan URL untuk melakukan validasi pendaftaran anda');

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
-- Constraints for table `referensi_category`
--
ALTER TABLE `referensi_category`
  ADD CONSTRAINT `category_to_ref_pemeriksaan` FOREIGN KEY (`id_referensi_pemeriksaan`) REFERENCES `referensi_pemeriksaan` (`id_referensi_pemeriksaan`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `range_to_ref_pemeriksaan` FOREIGN KEY (`id_referensi_pemeriksaan`) REFERENCES `referensi_pemeriksaan` (`id_referensi_pemeriksaan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
