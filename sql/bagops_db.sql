-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 02 Mar 2026 pada 06.19
-- Versi server: 10.11.14-MariaDB-0ubuntu0.24.04.1
-- Versi PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bagops_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `access_log`
--

CREATE TABLE `access_log` (
  `id` int(11) NOT NULL,
  `page` varchar(100) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_role` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `access_result` enum('granted','denied','redirected') NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `access_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Trigger `access_log`
--
DELIMITER $$
CREATE TRIGGER `log_page_access` AFTER INSERT ON `access_log` FOR EACH ROW BEGIN
    
    
END
$$
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `active_pages_by_role`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `active_pages_by_role` (
`page_key` varchar(100)
,`title` varchar(200)
,`description` text
,`target_role` enum('all','super_admin','admin','kabag_ops','kaur_ops','user')
,`page_type` enum('standard','dashboard','report','settings','profile')
,`layout_type` enum('default','full_width','sidebar','minimal')
,`meta_title` varchar(200)
,`meta_description` text
,`custom_css` text
,`custom_js` text
,`order_index` int(11)
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `personel_id` int(11) DEFAULT NULL,
  `operation_id` int(11) DEFAULT NULL,
  `role_assignment` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'assigned',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dynamic_jabatan`
--

CREATE TABLE `dynamic_jabatan` (
  `id` int(11) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL,
  `kode_jabatan` varchar(20) DEFAULT NULL,
  `level_jabatan` int(11) DEFAULT 3,
  `parent_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dynamic_jabatan`
--

INSERT INTO `dynamic_jabatan` (`id`, `nama_jabatan`, `kode_jabatan`, `level_jabatan`, `parent_id`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(19, 'KAPOLRES SAMOSIR', 'KAPSAM', 2, NULL, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:46:53'),
(20, 'WAKAPOLRES', 'WAK', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(21, 'KABAG OPS', 'KABOPS', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(22, 'PS. PAUR SUBBAGBINOPS', 'PS.PAUSUB', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:38'),
(23, 'BA MIN BAG OPS', 'BAMINBAGOP', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:38'),
(24, 'ASN BAG OPS', 'ASNBAGOPS', 3, 21, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(25, 'KA SPKT', 'KASPK', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(26, 'PAMAPTA 1', 'PAM1', 3, 80, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(27, 'PAMAPTA 2', 'PAM2', 3, 80, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(28, 'PAMAPTA 3', 'PAM3', 3, 80, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(29, 'BAMIN PAMAPTA 2', 'BAMPAM2', 5, 85, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(30, 'BAMIN PAMAPTA 3', 'BAMPAM3', 5, 85, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(31, 'BAMIN PAMAPTA 1', 'BAMPAM1', 5, 85, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(32, 'OP CALL CENTRE', 'OPCALCEN', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(33, 'PAURSUBBAGPROGAR', 'PAU', 3, 120, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(34, 'BA MIN BAG REN', 'BAMINBAGRE', 5, 120, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(35, 'PS. KABAG SDM', 'PS.KABSDM', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(36, 'PAURSUBBAGBINKAR', 'PAU1', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:38'),
(37, 'BA MIN BAG SDM', 'BAMINBAGSD', 4, 119, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(38, 'BA POLRES SAMOSIR', 'BAPOLSAM', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:38'),
(39, 'ADC KAPOLRES', 'ADCKAP', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(40, 'Plt. KASUBBAGBEKPAL', 'PLTKAS', 3, 121, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(41, 'BA MIN BAG LOG', 'BAMINBAGLO', 5, 121, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(42, 'PS. KASIUM', 'PS.KAS', 3, 119, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(43, 'BINTARA SIUM', 'BINSIU', 3, 127, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(44, 'PS. KASIKEU', 'PS.KAS1', 4, 122, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(45, 'BINTARA SIKEU', 'BINSIK', 5, 122, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(46, 'KASIDOKKES', 'KAS', 3, 119, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(47, 'BA SIDOKKES', 'BASID', 4, 119, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(48, 'Plt. KASIWAS', 'PLTKAS1', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:38'),
(49, 'BINTARA SIWAS', 'BINSIW', 4, 21, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(50, 'BINTARA SITIK', 'BINSIT', 4, 119, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(51, 'KASUBSIBANKUM', 'KAS1', 3, 119, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(52, 'BINTARA SIKUM', 'BINSIK1', 4, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:38'),
(53, 'PS. KASIPROPAM', 'PS.KAS2', 3, 21, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(54, 'PS. KANIT PROPOS', 'PS.KANPRO', 3, 21, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(55, 'PS. KANIT PAMINAL', 'PS.KANPAM', 3, 21, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(56, 'BINTARA SIPROPAM', 'BINSIP', 4, 21, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(57, 'BA PEMBINAAN', 'BAPEM', 4, 119, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(58, 'KASIHUMAS', 'KAS2', 3, 21, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(59, 'BINTARA SIHUMAS', 'BINSIH', 4, 21, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(60, 'KAURBINOPS', 'KAU', 3, 21, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(61, 'BINTARA SAT BINMAS', 'BINSATBIN', 4, 125, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(62, 'PS. KASAT INTELKAM', 'PS.KASINT', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(63, 'PS. KAURMINTU', 'PS.KAU', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:38'),
(64, 'PS. KANIT 3', 'PS.KAN3', 3, 123, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(65, 'PS. KANIT 1', 'PS.KAN1', 4, 123, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(66, 'PS. KANIT 2', 'PS.KAN2', 4, 123, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(67, 'BINTARA SAT INTELKAM', 'BINSATINT', 4, 123, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(68, 'BINTARA SATINTELKAM', 'BINSAT', 5, 123, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(69, 'KASAT RESKRIM', 'KASRES', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(70, 'KANITIDIK 3', 'KAN3', 3, 123, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(71, 'KANITIDIK 4', 'KAN4', 3, 123, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(72, 'KANITIDIK 1', 'KAN1', 3, 123, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(73, 'KANITIDIK 5', 'KAN5', 4, 123, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(74, 'PS. KANITIDIK 2', 'PS.KAN21', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:38'),
(75, 'PS. KANIT IDENTIFIKASI', 'PS.KANIDE', 3, 123, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(76, 'BINTARA SAT RESKRIM', 'BINSATRES', 4, 69, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(77, 'KASATRESNARKOBA', 'KAS3', 3, 69, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(78, 'PS.KANIT IDIK 1', 'PS.IDI1', 4, 123, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(79, 'BINTARA SATRESNARKOBA', 'BINSAT1', 4, 124, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(80, 'KASAT SAMAPTA', 'KASSAM', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(81, 'PS. KAURBINOPS', 'PS.KAU1', 3, 21, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(82, 'PS. KANIT DALMAS 2', 'PS.KANDAL2', 3, 80, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(83, 'PS. KANIT TURJAWALI', 'PS.KANTUR', 4, 90, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(84, 'BINTARA SAT SAMAPTA', 'BINSATSAM', 4, 80, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(85, 'KASAT PAMOBVIT', 'KASPAM', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(86, 'PS. KANITPAMWASTER', 'PS.KAN', 3, 85, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(87, 'PS. KANITPAMWISATA', 'PS.KAN4', 3, 85, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(88, 'PS. PANIT PAMWASTER', 'PS.PANPAM', 3, 85, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(89, 'BINTARA SAT PAMOBVIT', 'BINSATPAM', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:38'),
(90, 'KASAT LANTAS', 'KASLAN', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(91, 'KANITREGIDENT LANTAS', 'KANLAN', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:38'),
(92, 'PS. KANITGAKKUM', 'PS.KAN5', 3, 90, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(93, 'PS. KANITTURJAWALI', 'PS.KAN6', 4, 90, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(94, 'PS. KANITKAMSEL', 'PS.KAN7', 4, 90, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(95, 'BINTARA SAT LANTAS', 'BINSATLAN', 4, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:38'),
(96, 'BINTARA SATLANTAS', 'BINSAT2', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:38'),
(97, 'KASAT POLAIRUD', 'KASPOL', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(98, 'PS. KANITPATROLI', 'PS.KAN8', 3, 97, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(99, 'BINTARA SATPOLAIRUD', 'BINSAT3', 4, 97, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(100, 'PS. KASAT TAHTI', 'PS.KASTAH', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(101, 'BINTARA SAT TAHTI', 'BINSATTAH', 5, 126, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(102, 'PS. KAPOLSEK HARIAN BOHO', 'PS.KAPHARB', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(103, 'PS. KANIT INTELKAM', 'PS.KANINT', 3, 123, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(104, 'PS. KANIT BINMAS', 'PS.KANBIN', 3, 125, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:34'),
(105, 'PS. KANIT RESKRIM', 'PS.KANRES', 4, 69, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(106, 'PS.KANIT SAMAPTA', 'PS.SAM', 4, 80, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(107, 'BINTARA POLSEK', 'BINPOL', 4, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(108, 'KAPOLSEK PALIPI', 'KAPPAL', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(109, 'PS. KA SPKT 1', 'PS.KASPK1', 3, 25, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(110, 'PS. KANIT SAMAPTA', 'PS.KANSAM', 3, 80, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(111, 'PS. KA SPKT 2', 'PS.KASPK2', 4, 25, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(112, 'BINTARA  POLSEK', 'BINPOL1', 4, 117, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(113, 'PS. KAPOLSEK SIMANINDO', 'PS.KAPSIM', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(114, 'KANIT RESKRIM', 'KANRES', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:53:38'),
(115, 'PS. KANIT PROPAM', 'PS.KANPRO1', 3, 21, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(116, 'PS. KA SPKT 3', 'PS.KASPK3', 3, 25, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:52:00'),
(117, 'KAPOLSEK ONANRUNGGU', 'KAPONA', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(118, 'KAPOLSEK PANGURURAN', 'KAPPAN', 3, 19, 1, 1, '2026-03-02 03:46:53', '2026-03-02 03:50:15'),
(119, 'KABAG SDM', 'KABSDM', 2, 19, 1, 1, '2026-03-02 03:53:34', '2026-03-02 03:53:38'),
(120, 'KABAG REN', 'KABREN', 2, 19, 1, 1, '2026-03-02 03:53:34', '2026-03-02 03:53:38'),
(121, 'KABAG LOG', 'KABLOG', 2, 19, 1, 1, '2026-03-02 03:53:34', '2026-03-02 03:53:38'),
(122, 'KABAG SUMDA', 'KABSUM', 2, 19, 1, 1, '2026-03-02 03:53:34', '2026-03-02 03:53:38'),
(123, 'KASAT INTELKAM', 'KASINTEL', 2, 19, 1, 1, '2026-03-02 03:53:34', '2026-03-02 03:53:38'),
(124, 'KASAT NARKOBA', 'KASNARK', 2, 19, 1, 1, '2026-03-02 03:53:34', '2026-03-02 03:53:38'),
(125, 'KASAT BINMAS', 'KASBIN', 2, 19, 1, 1, '2026-03-02 03:53:34', '2026-03-02 03:53:38'),
(126, 'KASAT TAHTI', 'KASTAHTI', 2, 19, 1, 1, '2026-03-02 03:53:34', '2026-03-02 03:53:38'),
(127, 'KASAT SIUM', 'KASSIUM', 2, 19, 1, 1, '2026-03-02 03:53:34', '2026-03-02 03:53:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kantor`
--

CREATE TABLE `kantor` (
  `id` int(11) NOT NULL,
  `nama_kantor` varchar(100) NOT NULL,
  `tipe_kantor_polisi` varchar(50) DEFAULT NULL,
  `klasifikasi` varchar(50) DEFAULT NULL,
  `level_kompleksitas` varchar(20) DEFAULT NULL,
  `pimpinan_default_pangkat` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kantor`
--

INSERT INTO `kantor` (`id`, `nama_kantor`, `tipe_kantor_polisi`, `klasifikasi`, `level_kompleksitas`, `pimpinan_default_pangkat`, `created_at`, `updated_at`) VALUES
(1, 'POLRES SAMOSIR', 'POLRES', 'Kabupaten/Kota', 'Menengah', 'AKBP', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(2, 'POLSEK SIMANINDO', 'POLSEK', 'Kecamatan', 'Rendah', 'Kompol/AKP', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(3, 'POLSEK HARIAN BOHO', 'POLSEK', 'Kecamatan', 'Rendah', 'Kompol/AKP', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(4, 'POLSEK PALIPI', 'POLSEK', 'Kecamatan', 'Rendah', 'Kompol/AKP', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(5, 'POLSEK ONAN RUNGGU', 'POLSEK', 'Kecamatan', 'Rendah', 'Kompol/AKP', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(6, 'POLSEK PANGURURAN', 'POLSEK', 'Kecamatan', 'Rendah', 'Kompol/AKP', '2026-03-01 07:11:06', '2026-03-01 07:11:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_jabatan_pns`
--

CREATE TABLE `master_jabatan_pns` (
  `id` int(11) NOT NULL,
  `kode_jabatan` varchar(30) NOT NULL,
  `nama_jabatan` varchar(150) NOT NULL,
  `level_jabatan` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_jabatan_polri`
--

CREATE TABLE `master_jabatan_polri` (
  `id` int(11) NOT NULL,
  `kode_jabatan` varchar(30) NOT NULL,
  `nama_jabatan` varchar(150) NOT NULL,
  `nama_jabatan_singkat` varchar(100) DEFAULT NULL,
  `level_jabatan` int(11) NOT NULL,
  `kategori` enum('STRUKTURAL','FUNGSIONAL') NOT NULL,
  `eselon` varchar(20) DEFAULT NULL,
  `tingkat` varchar(50) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `parent_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_jabatan_polri`
--

INSERT INTO `master_jabatan_polri` (`id`, `kode_jabatan`, `nama_jabatan`, `nama_jabatan_singkat`, `level_jabatan`, `kategori`, `eselon`, `tingkat`, `urutan`, `parent_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'KAPOLRI', 'Kepala Kepolisian Negara Republik Indonesia', 'Kapolri', 1, 'STRUKTURAL', 'I.a', 'MABES POLRI', 1, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(2, 'WAKAPOLRI', 'Wakil Kepala Kepolisian Negara Republik Indonesia', 'Wakapolri', 2, 'STRUKTURAL', 'I.a', 'MABES POLRI', 2, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(3, 'IRWASUM', 'Inspektur Pengawasan Umum', 'Irwasum', 3, 'STRUKTURAL', 'I.a', 'MABES POLRI', 3, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(4, 'KABAINTELKAM', 'Kepala Badan Intelijen Keamanan', 'Kabaintelkam', 4, 'STRUKTURAL', 'I.a', 'MABES POLRI', 4, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(5, 'KABAHARKAM', 'Kepala Badan Pemelihara Keamanan', 'Kabaharkam', 5, 'STRUKTURAL', 'I.a', 'MABES POLRI', 5, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(6, 'KABARESKRIM', 'Kepala Badan Reserse Kriminal', 'Kabareskrim', 6, 'STRUKTURAL', 'I.a', 'MABES POLRI', 6, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(7, 'KALEMDIKLAT', 'Kepala Lembaga Pendidikan dan Pelatihan', 'Kalemdiklat', 7, 'STRUKTURAL', 'I.a', 'MABES POLRI', 7, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(8, 'DIRRESSIBER', 'Direktur Reserse Siber', 'Dirresiber', 8, 'STRUKTURAL', 'II.b', 'MABES POLRI', 8, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(9, 'KAPOLDA', 'Kepala Kepolisian Daerah', 'Kapolda', 9, 'STRUKTURAL', 'I.b', 'POLDA', 9, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(10, 'WAKAPOLDA', 'Wakil Kepolisian Daerah', 'Wakapolda', 10, 'STRUKTURAL', 'II.a', 'POLDA', 10, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(11, 'DIRKRIMUM_POLD', 'Direktur Reserse Kriminal Umum Polda', 'Dirkrimum', 11, 'STRUKTURAL', 'II.b', 'POLDA', 11, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(12, 'DIRRESNARKOBA_POLD', 'Direktur Reserse Narkoba Polda', 'Dirresnarkoba', 12, 'STRUKTURAL', 'II.b', 'POLDA', 12, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(13, 'DIRBINMAS_POLD', 'Direktur Pembinaan Masyarakat Polda', 'Dirbinmas', 13, 'STRUKTURAL', 'II.b', 'POLDA', 13, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(14, 'DIRSAMAPTA_POLD', 'Direktur Samapta Bhayangkara Polda', 'Dirsamapta', 14, 'STRUKTURAL', 'II.b', 'POLDA', 14, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(15, 'DIRLANTAS_POLD', 'Direktur Lalu Lintas Polda', 'Dirlantas', 15, 'STRUKTURAL', 'II.b', 'POLDA', 15, NULL, 1, '2026-03-01 19:27:47', '2026-03-01 19:27:47'),
(16, 'KAPOLRES', 'Kepala Kepolisian Resort', 'Kapolres', 16, 'STRUKTURAL', 'III.a', 'POLRES', 16, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(17, 'WAKAPOLRES', 'Wakil Kepolisian Resort', 'Wakapolres', 17, 'STRUKTURAL', 'IV.a', 'POLRES', 17, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(18, 'KABAGOPS_POLRES', 'Kepala Bagian Operasional Polres', 'Kabag Ops', 18, 'STRUKTURAL', 'IV.b', 'POLRES', 18, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(19, 'KABAGRENAK_POLRES', 'Kepala Bagian Perencanaan Polres', 'Kabag Ren', 19, 'STRUKTURAL', 'IV.b', 'POLRES', 19, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(20, 'KABAGSUMDA_POLRES', 'Kepala Bagian Sumber Daya Polres', 'Kabag Sumda', 20, 'STRUKTURAL', 'IV.b', 'POLRES', 20, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(21, 'KASATRESKRIM_POLRES', 'Kepala Satuan Reserse Kriminal Polres', 'Kasat Reskrim', 21, 'STRUKTURAL', 'IV.b', 'POLRES', 21, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(22, 'KASATLANTAS_POLRES', 'Kepala Satuan Lalu Lintas Polres', 'Kasat Lantas', 22, 'STRUKTURAL', 'IV.b', 'POLRES', 22, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(23, 'KASATINTELKAM_POLRES', 'Kepala Satuan Intelijen Polres', 'Kasat Intelkam', 23, 'STRUKTURAL', 'IV.b', 'POLRES', 23, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(24, 'KAPOLSEK', 'Kepala Kepolisian Sektor', 'Kapolsek', 24, 'STRUKTURAL', 'IV.b', 'POLSEK', 24, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(25, 'WAKAPOLSEK', 'Wakil Kepolisian Sektor', 'Wakapolsek', 25, 'STRUKTURAL', 'V.a', 'POLSEK', 25, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(26, 'KANITINTEL_POLSEK', 'Kepala Unit Intelijen Polsek', 'Kanit Intel', 26, 'STRUKTURAL', 'V.a', 'POLSEK', 26, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(27, 'KANITRESKRIM_POLSEK', 'Kepala Unit Reserse Kriminal Polsek', 'Kanit Reskrim', 27, 'STRUKTURAL', 'V.a', 'POLSEK', 27, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(28, 'KANITLANTAS_POLSEK', 'Kepala Unit Lalu Lintas Polsek', 'Kanit Lantas', 28, 'STRUKTURAL', 'V.a', 'POLSEK', 28, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(29, 'KASIUM_POLSEK', 'Kepala Seksi Umum', 'Kasium', 29, 'STRUKTURAL', 'V.a', 'POLSEK', 29, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(30, 'KAPOLSUBSEKTOR', 'Kepala Kepolisian Subsektor', 'Kapolsubsektor', 30, 'STRUKTURAL', 'V.b', 'POLSUBSEKTOR', 30, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(31, 'KAPOSPOL', 'Kepala Pos Polisi', 'Kapospol', 31, 'STRUKTURAL', 'VI.a', 'POSPOL', 31, NULL, 1, '2026-03-01 19:28:07', '2026-03-01 19:28:07'),
(32, 'WAIRWASUM', 'Wakil Inspektur Pengawasan Umum', 'Wairwasum', 32, 'STRUKTURAL', 'I.b', 'MABES POLRI', 32, NULL, 1, '2026-03-01 19:28:21', '2026-03-01 19:28:21'),
(33, 'WAKABAINTELKAM', 'Wakil Kepala Badan Intelijen Keamanan', 'Wakabaintelkam', 33, 'STRUKTURAL', 'I.b', 'MABES POLRI', 33, NULL, 1, '2026-03-01 19:28:21', '2026-03-01 19:28:21'),
(34, 'WAKABAHARKAM', 'Wakil Kepala Badan Pemelihara Keamanan', 'Wakabaharkam', 34, 'STRUKTURAL', 'I.b', 'MABES POLRI', 34, NULL, 1, '2026-03-01 19:28:21', '2026-03-01 19:28:21'),
(35, 'WAKABARESKRIM', 'Wakil Kepala Badan Reserse Kriminal', 'Wakabareskrim', 35, 'STRUKTURAL', 'I.b', 'MABES POLRI', 35, NULL, 1, '2026-03-01 19:28:21', '2026-03-01 19:28:21'),
(36, 'DIRINTELKAM', 'Direktur Intelijen Keamanan', 'Dirintelkam', 36, 'STRUKTURAL', 'II.a', 'MABES POLRI', 36, NULL, 1, '2026-03-01 19:28:22', '2026-03-01 19:28:22'),
(37, 'DIRBINMAS', 'Direktur Pembinaan Masyarakat', 'Dirbinmas', 37, 'STRUKTURAL', 'II.a', 'MABES POLRI', 37, NULL, 1, '2026-03-01 19:28:22', '2026-03-01 19:28:22'),
(38, 'DIRLANTAS', 'Direktur Lalu Lintas', 'Dirlantas', 38, 'STRUKTURAL', 'II.a', 'MABES POLRI', 38, NULL, 1, '2026-03-01 19:28:22', '2026-03-01 19:28:22'),
(39, 'DIRKRIMUM', 'Direktur Reserse Kriminal Umum', 'Dirkrimum', 39, 'STRUKTURAL', 'II.a', 'MABES POLRI', 39, NULL, 1, '2026-03-01 19:28:22', '2026-03-01 19:28:22'),
(40, 'DIRKRIMSUS', 'Direktur Reserse Kriminal Khusus', 'Dirkrimsus', 40, 'STRUKTURAL', 'II.a', 'MABES POLRI', 40, NULL, 1, '2026-03-01 19:28:22', '2026-03-01 19:28:22'),
(41, 'DIRPOLAIR', 'Direktur Polisi Air', 'Dirpolair', 41, 'STRUKTURAL', 'II.a', 'MABES POLRI', 41, NULL, 1, '2026-03-01 19:28:22', '2026-03-01 19:28:22'),
(42, 'DIRPOLUDARA', 'Direktur Polisi Udara', 'Dirpoludara', 42, 'STRUKTURAL', 'II.a', 'MABES POLRI', 42, NULL, 1, '2026-03-01 19:28:22', '2026-03-01 19:28:22'),
(43, 'DIRSAMAPTA', 'Direktur Samapta Bhayangkara', 'Dirsamapta', 43, 'STRUKTURAL', 'II.a', 'MABES POLRI', 43, NULL, 1, '2026-03-01 19:28:22', '2026-03-01 19:28:22'),
(48, 'KASATBINMAS_POLRES', 'Kepala Satuan Pembinaan Masyarakat Polres', 'Kasat Binmas', 48, 'STRUKTURAL', 'IV.b', 'POLRES', 48, NULL, 1, '2026-03-01 19:28:39', '2026-03-01 19:28:39'),
(49, 'KASATSAMAPTA_POLRES', 'Kepala Satuan Samapta Bhayangkara Polres', 'Kasat Samapta', 49, 'STRUKTURAL', 'IV.b', 'POLRES', 49, NULL, 1, '2026-03-01 19:28:39', '2026-03-01 19:28:39'),
(50, 'KANITBINMAS_POLSEK', 'Kepala Unit Pembinaan Masyarakat Polsek', 'Kanit Binmas', 50, 'STRUKTURAL', 'V.a', 'POLSEK', 50, NULL, 1, '2026-03-01 19:28:39', '2026-03-01 19:28:39'),
(51, 'KANITSAMAPTA_POLSEK', 'Kepala Unit Samapta Bhayangkara Polsek', 'Kanit Samapta', 51, 'STRUKTURAL', 'V.a', 'POLSEK', 51, NULL, 1, '2026-03-01 19:28:39', '2026-03-01 19:28:39'),
(52, 'KANITPROVOS_POLSEK', 'Kepala Unit Provost Polsek', 'Kanit Provos', 52, 'STRUKTURAL', 'V.a', 'POLSEK', 52, NULL, 1, '2026-03-01 19:28:39', '2026-03-01 19:28:39'),
(53, 'ANALIS_POLRI', 'Analis Kepolisian', 'Analis', 53, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 53, NULL, 1, '2026-03-01 19:28:39', '2026-03-01 19:28:39'),
(54, 'PENYIDIK_UTAMA', 'Penyidik Utama', 'Penyidik Utama', 54, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 54, NULL, 1, '2026-03-01 19:28:39', '2026-03-01 19:28:39'),
(55, 'PENYIDIK_MUDA', 'Penyidik Muda', 'Penyidik Muda', 55, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 55, NULL, 1, '2026-03-01 19:28:39', '2026-03-01 19:28:39'),
(56, 'PENYIDIK_PERTAMA', 'Penyidik Pertama', 'Penyidik Pertama', 56, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 56, NULL, 1, '2026-03-01 19:28:39', '2026-03-01 19:28:39'),
(57, 'PAMONG_PRAJA', 'Pamong Praja', 'Pamong Praja', 57, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 57, NULL, 1, '2026-03-01 19:28:39', '2026-03-01 19:28:39'),
(58, 'DIRRESNARKOBA', 'Direktur Reserse Narkoba', 'Dirresnarkoba', 54, 'STRUKTURAL', 'II.a', 'MABES POLRI', 54, NULL, 1, '2026-03-01 19:30:34', '2026-03-01 19:30:34'),
(59, 'DIRPAMOBVITAL', 'Direktur Pengamanan Objek Vital', 'Dirpam Obvit', 55, 'STRUKTURAL', 'II.a', 'MABES POLRI', 55, NULL, 1, '2026-03-01 19:30:34', '2026-03-01 19:30:34'),
(60, 'DIRTAHTABARBUK', 'Direktur Perawatan Tahanan dan Barang Bukti', 'Dirtahta Barbuk', 56, 'STRUKTURAL', 'II.a', 'MABES POLRI', 56, NULL, 1, '2026-03-01 19:30:34', '2026-03-01 19:30:34'),
(61, 'KASATBRIMOB', 'Kepala Satuan Brigade Mobil', 'Kasat Brimob', 57, 'STRUKTURAL', 'II.a', 'MABES POLRI', 57, NULL, 1, '2026-03-01 19:30:34', '2026-03-01 19:30:34'),
(62, 'KASEPOLNEGARA', 'Kepala Sekolah Polisi Negara', 'Kasepolnegara', 58, 'STRUKTURAL', 'II.a', 'MABES POLRI', 58, NULL, 1, '2026-03-01 19:30:34', '2026-03-01 19:30:34'),
(63, 'KABIDKEU', 'Kepala Bidang Keuangan', 'Kabidkeu', 59, 'STRUKTURAL', 'II.a', 'MABES POLRI', 59, NULL, 1, '2026-03-01 19:30:34', '2026-03-01 19:30:34'),
(64, 'KABIDKEDOKKES', 'Kepala Bidang Kedokteran dan Kesehatan', 'Kabiddokkes', 60, 'STRUKTURAL', 'II.a', 'MABES POLRI', 60, NULL, 1, '2026-03-01 19:30:34', '2026-03-01 19:30:34'),
(65, 'KABIDLABFOR', 'Kepala Bidang Laboratorium Forensik', 'Kabidlabfor', 61, 'STRUKTURAL', 'II.a', 'MABES POLRI', 61, NULL, 1, '2026-03-01 19:30:34', '2026-03-01 19:30:34'),
(66, 'WADIRKRIMUM_POLD', 'Wakil Direktur Reserse Kriminal Umum Polda', 'Wadirkrimum', 62, 'STRUKTURAL', 'III.a', 'POLDA', 62, NULL, 1, '2026-03-01 19:30:45', '2026-03-01 19:30:45'),
(67, 'WADIRRESNARKOBA_POLD', 'Wakil Direktur Reserse Narkoba Polda', 'Wadirresnarkoba', 63, 'STRUKTURAL', 'III.a', 'POLDA', 63, NULL, 1, '2026-03-01 19:30:45', '2026-03-01 19:30:45'),
(68, 'WADIRLANTAS_POLD', 'Wakil Direktur Lalu Lintas Polda', 'Wadirlantas', 64, 'STRUKTURAL', 'III.a', 'POLDA', 64, NULL, 1, '2026-03-01 19:30:45', '2026-03-01 19:30:45'),
(69, 'WADIRINTELKAM_POLD', 'Wakil Direktur Intelijen Polda', 'Wadirintelkam', 65, 'STRUKTURAL', 'III.a', 'POLDA', 65, NULL, 1, '2026-03-01 19:30:45', '2026-03-01 19:30:45'),
(70, 'WADIRBINMAS_POLD', 'Wakil Direktur Pembinaan Masyarakat Polda', 'Wadirbinmas', 66, 'STRUKTURAL', 'III.a', 'POLDA', 67, NULL, 1, '2026-03-01 19:30:45', '2026-03-01 19:30:45'),
(71, 'WADIRSAMAPTA_POLD', 'Wakil Direktur Samapta Bhayangkara Polda', 'Wadirsamapta', 67, 'STRUKTURAL', 'III.a', 'POLDA', 68, NULL, 1, '2026-03-01 19:30:45', '2026-03-01 19:30:45'),
(84, 'KABAGBINOPSNAL_POLD', 'Kepala Bagian Pembinaan Operasional Polda', 'Kabag Binopsnal', 80, 'STRUKTURAL', 'IV.a', 'POLDA', 80, NULL, 1, '2026-03-01 19:33:55', '2026-03-01 19:33:55'),
(87, 'KABAGWASSIDIK_POLD', 'Kepala Bagian Pengawasan Penyidikan Polda', 'Kabag Wassidik', 83, 'STRUKTURAL', 'IV.a', 'POLDA', 83, NULL, 1, '2026-03-01 19:33:55', '2026-03-01 19:33:55'),
(88, 'KABAGRENPROGAR_POLD', 'Kepala Bagian Perencanaan dan Anggaran Polda', 'Kabag Renproggar', 84, 'STRUKTURAL', 'IV.a', 'POLDA', 84, NULL, 1, '2026-03-01 19:33:55', '2026-03-01 19:33:55'),
(89, 'KASUBBAGPROG_POLD', 'Kepala Sub Bagian Program Polda', 'Kasubbag Prog', 85, 'STRUKTURAL', 'IV.b', 'POLDA', 85, NULL, 1, '2026-03-01 19:33:55', '2026-03-01 19:33:55'),
(90, 'KASUBBAGGAR_POLD', 'Kepala Sub Bagian Anggaran Polda', 'Kasubbag Gar', 86, 'STRUKTURAL', 'IV.b', 'POLDA', 86, NULL, 1, '2026-03-01 19:33:55', '2026-03-01 19:33:55'),
(91, 'KABAGDALPROGAR_POLD', 'Kepala Bagian Pengendalian Program dan Anggaran Polda', 'Kabag Dalproggar', 87, 'STRUKTURAL', 'IV.a', 'POLDA', 87, NULL, 1, '2026-03-01 19:33:55', '2026-03-01 19:33:55'),
(92, 'KASUBBAGDALPRO_POLD', 'Kepala Sub Bagian Pengendalian Program Polda', 'Kasubbag Dalpro', 88, 'STRUKTURAL', 'IV.b', 'POLDA', 88, NULL, 1, '2026-03-01 19:33:55', '2026-03-01 19:33:55'),
(93, 'KASUBBAGDALGAR_POLD', 'Kepala Sub Bagian Pengendalian Anggaran Polda', 'Kasubbag Dalgar', 89, 'STRUKTURAL', 'IV.b', 'POLDA', 89, NULL, 1, '2026-03-01 19:33:55', '2026-03-01 19:33:55'),
(114, 'DIRRES_PPA_POLD', 'Direktur Reserse Pelindungan Perempuan dan Anak Polda', 'Dirres PPA', 110, 'STRUKTURAL', 'II.b', 'POLDA', 110, NULL, 1, '2026-03-01 19:37:53', '2026-03-01 19:37:53'),
(115, 'WADIRRES_PPA_POLD', 'Wakil Direktur Reserse Pelindungan Perempuan dan Anak Polda', 'Wadirres PPA', 111, 'STRUKTURAL', 'III.a', 'POLDA', 111, NULL, 1, '2026-03-01 19:37:53', '2026-03-01 19:37:53'),
(116, 'KABIROOPS_POLD', 'Kepala Biro Operasi Polda', 'Kabirops', 112, 'STRUKTURAL', 'III.a', 'POLDA', 112, NULL, 1, '2026-03-01 19:37:53', '2026-03-01 19:37:53'),
(117, 'KABIRORENA_POLD', 'Kepala Biro Perencanaan Umum dan Anggaran Polda', 'Kabirorena', 113, 'STRUKTURAL', 'III.a', 'POLDA', 113, NULL, 1, '2026-03-01 19:37:53', '2026-03-01 19:37:53'),
(118, 'KABIROSDM_POLD', 'Kepala Biro Sumber Daya Manusia Polda', 'Kabiro SDM', 114, 'STRUKTURAL', 'III.a', 'POLDA', 114, NULL, 1, '2026-03-01 19:37:53', '2026-03-01 19:37:53'),
(119, 'KABIROLOG_POLD', 'Kepala Biro Logistik Polda', 'Kabirolog', 115, 'STRUKTURAL', 'III.a', 'POLDA', 115, NULL, 1, '2026-03-01 19:37:53', '2026-03-01 19:37:53'),
(120, 'KASUBDIT_I_PPA', 'Kepala Subdirektorat I PPA', 'Kasubdit I', 116, 'STRUKTURAL', 'III.b', 'POLDA', 116, NULL, 1, '2026-03-01 19:37:53', '2026-03-01 19:37:53'),
(121, 'KASUBDIT_II_PPA', 'Kepala Subdirektorat II PPA', 'Kasubdit II', 117, 'STRUKTURAL', 'III.b', 'POLDA', 117, NULL, 1, '2026-03-01 19:37:53', '2026-03-01 19:37:53'),
(122, 'KASUBDIT_III_PPA', 'Kepala Subdirektorat III PPA', 'Kasubdit III', 118, 'STRUKTURAL', 'III.b', 'POLDA', 118, NULL, 1, '2026-03-01 19:37:53', '2026-03-01 19:37:53'),
(123, 'KASUBDIT_SIBER', 'Kepala Subdirektorat Siber', 'Kasubdit Siber', 119, 'STRUKTURAL', 'III.b', 'POLDA', 119, NULL, 1, '2026-03-01 19:37:53', '2026-03-01 19:37:53'),
(124, 'KASUBDIT_I_KRIMUM', 'Kepala Subdirektorat I Reserse Kriminal Umum', 'Kasubdit I Krimum', 120, 'STRUKTURAL', 'III.b', 'POLDA', 120, NULL, 1, '2026-03-01 19:41:23', '2026-03-01 19:41:23'),
(125, 'KASUBDIT_II_KRIMUM', 'Kepala Subdirektorat II Reserse Kriminal Umum', 'Kasubdit II Krimum', 121, 'STRUKTURAL', 'III.b', 'POLDA', 121, NULL, 1, '2026-03-01 19:41:23', '2026-03-01 19:41:23'),
(126, 'KASUBDIT_III_KRIMUM', 'Kepala Subdirektorat III Reserse Kriminal Umum', 'Kasubdit III Krimum', 122, 'STRUKTURAL', 'III.b', 'POLDA', 122, NULL, 1, '2026-03-01 19:41:23', '2026-03-01 19:41:23'),
(127, 'KASUBDIT_IV_KRIMUM', 'Kepala Subdirektorat IV Reserse Kriminal Umum', 'Kasubdit IV Krimum', 123, 'STRUKTURAL', 'III.b', 'POLDA', 123, NULL, 1, '2026-03-01 19:41:23', '2026-03-01 19:41:23'),
(128, 'KASUBDIT_V_KRIMUM', 'Kepala Subdirektorat V Reserse Kriminal Umum', 'Kasubdit V Krimum', 124, 'STRUKTURAL', 'III.b', 'POLDA', 124, NULL, 1, '2026-03-01 19:41:23', '2026-03-01 19:41:23'),
(129, 'KASIIDENT_POLD', 'Kepala Seksi Identifikasi Polda', 'Kasiident', 125, 'STRUKTURAL', 'IV.a', 'POLDA', 125, NULL, 1, '2026-03-01 19:41:23', '2026-03-01 19:41:23'),
(130, 'KAYON_A_BRIMOB', 'Kepala Batalyon A Brigade Mobil', 'Kayon A Brimob', 126, 'STRUKTURAL', 'IV.a', 'POLDA', 126, NULL, 1, '2026-03-01 19:41:23', '2026-03-01 19:41:23'),
(131, 'KAYON_B_BRIMOB', 'Kepala Batalyon B Brigade Mobil', 'Kayon B Brimob', 127, 'STRUKTURAL', 'IV.a', 'POLDA', 127, NULL, 1, '2026-03-01 19:41:23', '2026-03-01 19:41:23'),
(132, 'KAYON_C_BRIMOB', 'Kepala Batalyon C Brigade Mobil', 'Kayon C Brimob', 128, 'STRUKTURAL', 'IV.a', 'POLDA', 128, NULL, 1, '2026-03-01 19:41:23', '2026-03-01 19:41:23'),
(133, 'KAYON_D_BRIMOB', 'Kepala Batalyon D Brigade Mobil', 'Kayon D Brimob', 129, 'STRUKTURAL', 'IV.a', 'POLDA', 129, NULL, 1, '2026-03-01 19:41:23', '2026-03-01 19:41:23'),
(134, 'KAUPT_POLDA', 'Kepala Unit Pelaksana Teknis Polda', 'Kaupt', 130, 'STRUKTURAL', 'IV.a', 'POLDA', 130, NULL, 1, '2026-03-01 19:41:23', '2026-03-01 19:41:23'),
(135, 'KASATKER_POLDA', 'Kepala Satuan Kerja Polda', 'Kasatker', 131, 'STRUKTURAL', 'IV.a', 'POLDA', 131, NULL, 1, '2026-03-01 19:41:23', '2026-03-01 19:41:23'),
(136, 'KASUBBAGMINOPSNAL_POLRES', 'Kepala Sub Bagian Administrasi Operasional Polres', 'Kasubbag Minopsnal', 140, 'STRUKTURAL', 'V.a', 'POLRES', 140, NULL, 1, '2026-03-01 19:43:27', '2026-03-01 19:43:27'),
(137, 'KASUBBAGANEV_POLRES', 'Kepala Sub Bagian Analisa dan Evaluasi Polres', 'Kasubbag Anev', 141, 'STRUKTURAL', 'V.a', 'POLRES', 141, NULL, 1, '2026-03-01 19:43:27', '2026-03-01 19:43:27'),
(138, 'URREN_POLRES', 'Urusan Perencanaan Polres', 'Urren', 142, 'STRUKTURAL', 'VI.a', 'POLRES', 142, NULL, 1, '2026-03-01 19:43:27', '2026-03-01 19:43:27'),
(139, 'URMINTU_POLRES', 'Urusan Administrasi dan Tata Usaha Polres', 'Urmintu', 143, 'STRUKTURAL', 'VI.a', 'POLRES', 143, NULL, 1, '2026-03-01 19:43:27', '2026-03-01 19:43:27'),
(140, 'URKEU_POLRES', 'Urusan Keuangan Polres', 'Urkeu', 144, 'STRUKTURAL', 'VI.a', 'POLRES', 144, NULL, 1, '2026-03-01 19:43:27', '2026-03-01 19:43:27'),
(141, 'PAUR_POLRES', 'Pembantu Urusan Administrasi Polres', 'Paur', 145, 'STRUKTURAL', 'VI.b', 'POLRES', 145, NULL, 1, '2026-03-01 19:43:27', '2026-03-01 19:43:27'),
(142, 'BAMIN_POLRES', 'Batur Pembantu Polres', 'Bamin', 146, 'STRUKTURAL', 'VI.b', 'POLRES', 146, NULL, 1, '2026-03-01 19:43:27', '2026-03-01 19:43:27'),
(143, 'KAURREN_POLDA', 'Kepala Urusan Perencanaan Polda', 'Kaurren', 105, 'STRUKTURAL', 'V.a', 'POLDA', 105, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(144, 'KAURMINTU_POLDA', 'Kepala Urusan Administrasi dan Tata Usaha Polda', 'Kaurmintu', 106, 'STRUKTURAL', 'V.a', 'POLDA', 106, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(145, 'KAURKEU_POLDA', 'Kepala Urusan Keuangan Polda', 'Kaurkeu', 107, 'STRUKTURAL', 'V.a', 'POLDA', 107, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(146, 'KAURREN_POLRES', 'Kepala Urusan Perencanaan Polres', 'Kaurren', 108, 'STRUKTURAL', 'VI.a', 'POLRES', 108, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(147, 'KAURMINTU_POLRES', 'Kepala Urusan Administrasi dan Tata Usaha Polres', 'Kaurmintu', 109, 'STRUKTURAL', 'VI.a', 'POLRES', 109, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(148, 'KAURKEU_POLRES', 'Kepala Urusan Keuangan Polres', 'Kaurkeu', 110, 'STRUKTURAL', 'VI.a', 'POLRES', 110, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(149, 'PANITINTEL_POLSEK', 'Pembantu Kepala Unit Intelijen Polsek', 'Panit Intel', 111, 'STRUKTURAL', 'VI.a', 'POLSEK', 111, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(150, 'PANITRESKRIM_POLSEK', 'Pembantu Kepala Unit Reserse Kriminal Polsek', 'Panit Reskrim', 112, 'STRUKTURAL', 'VI.a', 'POLSEK', 112, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(151, 'PANITLANTAS_POLSEK', 'Pembantu Kepala Unit Lalu Lintas Polsek', 'Panit Lantas', 113, 'STRUKTURAL', 'VI.a', 'POLSEK', 113, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(152, 'PANITBINMAS_POLSEK', 'Pembantu Kepala Unit Pembinaan Masyarakat Polsek', 'Panit Binmas', 114, 'STRUKTURAL', 'VI.a', 'POLSEK', 114, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(153, 'PANITSAMAPTA_POLSEK', 'Pembantu Kepala Unit Samapta Bhayangkara Polsek', 'Panit Samapta', 115, 'STRUKTURAL', 'VI.a', 'POLSEK', 115, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(154, 'PANITPROVOS_POLSEK', 'Pembantu Kepala Unit Provost Polsek', 'Panit Provos', 116, 'STRUKTURAL', 'VI.a', 'POLSEK', 116, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(155, 'PANITWASSIDIK_POLD', 'Pembantu Kepala Unit Pengawasan Penyidikan Polda', 'Panit Wassidik', 117, 'STRUKTURAL', 'V.a', 'POLDA', 117, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(156, 'PANITIDENT_POLD', 'Pembantu Kepala Unit Identifikasi Polda', 'Panit Ident', 118, 'STRUKTURAL', 'V.a', 'POLDA', 118, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(157, 'BANITWASSIDIK_POLD', 'Batur Pembantu Pengawasan Penyidikan Polda', 'Banit Wassidik', 119, 'STRUKTURAL', 'VI.b', 'POLDA', 119, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(158, 'BANITIDENT_POLD', 'Batur Pembantu Identifikasi Polda', 'Banit Ident', 120, 'STRUKTURAL', 'VI.b', 'POLDA', 120, NULL, 1, '2026-03-01 19:50:39', '2026-03-01 19:50:39'),
(159, 'KADIVPROPAM', 'Kepala Divisi Profesi dan Pengamanan', 'Kadivpropam', 121, 'STRUKTURAL', 'I.b', 'MABES POLRI', 121, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(160, 'KADIVKUM', 'Kepala Divisi Hukum', 'Kadivkum', 122, 'STRUKTURAL', 'I.b', 'MABES POLRI', 122, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(161, 'KADIVHUMAS', 'Kepala Divisi Humas', 'Kadivhumas', 123, 'STRUKTURAL', 'I.b', 'MABES POLRI', 123, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(162, 'KADIVHUBINTER', 'Kepala Divisi Hubungan Internasional', 'Kadivhubinter', 124, 'STRUKTURAL', 'I.b', 'MABES POLRI', 124, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(163, 'KADIVTI_POL', 'Kepala Divisi Teknologi Informasi Polri', 'Kadiv TI Pol', 125, 'STRUKTURAL', 'I.b', 'MABES POLRI', 125, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(164, 'WAKADIVPROPAM', 'Wakil Kepala Divisi Profesi dan Pengamanan', 'Wakadivpropam', 126, 'STRUKTURAL', 'II.a', 'MABES POLRI', 126, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(165, 'WAKADIVKUM', 'Wakil Kepala Divisi Hukum', 'Wakadivkum', 127, 'STRUKTURAL', 'II.a', 'MABES POLRI', 127, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(166, 'WAKADIVHUMAS', 'Wakil Kepala Divisi Humas', 'Wakadivhumas', 128, 'STRUKTURAL', 'II.a', 'MABES POLRI', 128, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(167, 'WAKADIVHUBINTER', 'Wakil Kepala Divisi Hubungan Internasional', 'Wakadivhubinter', 129, 'STRUKTURAL', 'II.a', 'MABES POLRI', 129, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(168, 'WAKADIVTI_POL', 'Wakil Kepala Divisi Teknologi Informasi Polri', 'Wakadiv TI Pol', 130, 'STRUKTURAL', 'II.a', 'MABES POLRI', 130, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(169, 'KAKORLANTAS', 'Kepala Korps Lalu Lintas', 'Kakorlantas', 131, 'STRUKTURAL', 'I.b', 'MABES POLRI', 131, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(170, 'KAKORBRIMOB', 'Kepala Korps Brigade Mobil', 'Kakorbrimob', 132, 'STRUKTURAL', 'I.b', 'MABES POLRI', 132, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(171, 'WAKAKORLANTAS', 'Wakil Kepala Korps Lalu Lintas', 'Wakakorlantas', 133, 'STRUKTURAL', 'II.a', 'MABES POLRI', 133, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(172, 'WAKAKORBRIMOB', 'Wakil Kepala Korps Brigade Mobil', 'Wakakorbrimob', 134, 'STRUKTURAL', 'II.a', 'MABES POLRI', 134, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(173, 'DANSATBRIMOB', 'Komandan Satuan Brigade Mobil', 'Dansatbrimob', 135, 'STRUKTURAL', 'II.b', 'MABES POLRI', 135, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(174, 'WADANSATBRIMOB', 'Wakil Komandan Satuan Brigade Mobil', 'Wadansatbrimob', 136, 'STRUKTURAL', 'III.a', 'MABES POLRI', 136, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(175, 'KAPUSLITBANG', 'Kepala Pusat Penelitian dan Pengembangan', 'Kapuslitbang', 137, 'STRUKTURAL', 'II.a', 'MABES POLRI', 137, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(176, 'KAPUSKEU', 'Kepala Pusat Keuangan', 'Kapuskeu', 138, 'STRUKTURAL', 'II.a', 'MABES POLRI', 138, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(177, 'KAPUSDOKKES', 'Kepala Pusat Kedokteran dan Kesehatan', 'Kapusdokkes', 139, 'STRUKTURAL', 'II.a', 'MABES POLRI', 139, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(178, 'KAPUSJARAH', 'Kepala Pusat Sejarah', 'Kapusjarah', 140, 'STRUKTURAL', 'II.b', 'MABES POLRI', 140, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(179, 'WAKAPUSLITBANG', 'Wakil Kepala Pusat Penelitian dan Pengembangan', 'Wakapuslitbang', 141, 'STRUKTURAL', 'III.a', 'MABES POLRI', 141, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(180, 'WAKAPUSKEU', 'Wakil Kepala Pusat Keuangan', 'Wakapuskeu', 142, 'STRUKTURAL', 'III.a', 'MABES POLRI', 142, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(181, 'WAKAPUSDOKKES', 'Wakil Kepala Pusat Kedokteran dan Kesehatan', 'Wakapusdokkes', 143, 'STRUKTURAL', 'III.a', 'MABES POLRI', 143, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(182, 'ASOPS_KAPOLRI', 'Asisten Kapolri Bidang Operasi', 'Asops Kapolri', 144, 'STRUKTURAL', 'II.a', 'MABES POLRI', 144, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(183, 'ASRENA_KAPOLRI', 'Asisten Kapolri Bidang Perencanaan Umum dan Anggaran', 'Asrena Kapolri', 145, 'STRUKTURAL', 'II.a', 'MABES POLRI', 145, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(184, 'ASSDM_KAPOLRI', 'Asisten Kapolri Bidang Sumber Daya Manusia', 'As SDM Kapolri', 146, 'STRUKTURAL', 'II.a', 'MABES POLRI', 146, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(185, 'ASSARPRAS_KAPOLRI', 'Asisten Kapolri Bidang Sarana dan Prasarana', 'Assarpras Kapolri', 147, 'STRUKTURAL', 'II.a', 'MABES POLRI', 147, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(186, 'SAHLI_KAPOLRI', 'Staf Ahli Kapolri', 'Sahli Kapolri', 148, 'STRUKTURAL', 'I.b', 'MABES POLRI', 148, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(187, 'KOORSAHLI_KAPOLRI', 'Koordinator Staf Ahli Kapolri', 'Koorsahli Kapolri', 149, 'STRUKTURAL', 'II.a', 'MABES POLRI', 149, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(188, 'KASEPAK', 'Kepala Sekolah Pembentukan Perwira', 'Kasetukpa', 150, 'STRUKTURAL', 'II.a', 'MABES POLRI', 150, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(189, 'KASESPIM', 'Kepala Sekolah Staf dan Pimpiman', 'Kasespim', 151, 'STRUKTURAL', 'II.a', 'MABES POLRI', 151, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(190, 'KETUA_STIK', 'Ketua Sekolah Tinggi Ilmu Kepolisian', 'Ketua STIK', 152, 'STRUKTURAL', 'II.a', 'MABES POLRI', 152, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(191, 'WAKAKETUA_STIK', 'Wakil Ketua Sekolah Tinggi Ilmu Kepolisian', 'Wakaketua STIK', 153, 'STRUKTURAL', 'III.a', 'MABES POLRI', 153, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(192, 'GUB_AKPOL', 'Gubernur Akademi Kepolisian', 'Gub Akpol', 154, 'STRUKTURAL', 'II.a', 'MABES POLRI', 154, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(193, 'WAKAGUB_AKPOL', 'Wakil Gubernur Akademi Kepolisian', 'Wakagub Akpol', 155, 'STRUKTURAL', 'III.a', 'MABES POLRI', 155, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(194, 'DIRPROG_STIK', 'Direktur Program Sekolah Tinggi Ilmu Kepolisian', 'Dirprog STIK', 156, 'STRUKTURAL', 'III.a', 'MABES POLRI', 156, NULL, 1, '2026-03-01 19:54:43', '2026-03-01 19:54:43'),
(195, 'WADIRRESSIBER_POLD', 'Wakil Direktur Reserse Siber Polda', 'Wadirressiber', 157, 'STRUKTURAL', 'III.a', 'POLDA', 157, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(196, 'ANALIS_MUDA', 'Analis Muda', 'Analis Muda', 158, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 158, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(197, 'ANALIS_MUDA_PERTAMA', 'Analis Muda Pertama', 'Analis Muda Pertama', 159, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 159, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(198, 'ANALIS_PERTAMA', 'Analis Pertama', 'Analis Pertama', 160, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 160, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(199, 'PENYIDIK_PEMBANTU', 'Penyidik Pembantu', 'Penyidik Pembantu', 161, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 161, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(200, 'PENYIDIK_PEGAWAI_SIPIL', 'Penyidik Pegawai Negeri Sipil', 'Penyidik PNS', 162, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 162, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(201, 'PENGAWAS_PENYIDIK', 'Pengawas Penyidik', 'Pengawas Penyidik', 163, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 163, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(202, 'PEMERIKSA_UTAMA', 'Pemeriksa Utama', 'Pemeriksa Utama', 164, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 164, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(203, 'PEMERIKSA_MUDA', 'Pemeriksa Muda', 'Pemeriksa Muda', 165, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 165, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(204, 'PEMERIKSA_PERTAMA', 'Pemeriksa Pertama', 'Pemeriksa Pertama', 166, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 166, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(205, 'AUDITOR_UTAMA', 'Auditor Utama', 'Auditor Utama', 167, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 167, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(206, 'AUDITOR_MUDA', 'Auditor Muda', 'Auditor Muda', 168, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 168, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(207, 'AUDITOR_PERTAMA', 'Auditor Pertama', 'Auditor Pertama', 169, 'FUNGSIONAL', NULL, 'FUNGSIONAL', 169, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(208, 'KADIVPROPAM_POLD', 'Kepala Divisi Profesi dan Pengamanan Polda', 'Kadivpropam Polda', 170, 'STRUKTURAL', 'II.a', 'POLDA', 170, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(209, 'KADIVKUM_POLD', 'Kepala Divisi Hukum Polda', 'Kadivkum Polda', 171, 'STRUKTURAL', 'II.a', 'POLDA', 171, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(210, 'KADIVHUMAS_POLD', 'Kepala Divisi Humas Polda', 'Kadivhumas Polda', 172, 'STRUKTURAL', 'II.a', 'POLDA', 172, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(211, 'KADIVHUBINTER_POLD', 'Kepala Divisi Hubungan Internasional Polda', 'Kadivhubinter Polda', 173, 'STRUKTURAL', 'II.a', 'POLDA', 173, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(212, 'KADIVTI_POLD', 'Kepala Divisi Teknologi Informasi Polda', 'Kadiv TI Polda', 174, 'STRUKTURAL', 'II.a', 'POLDA', 174, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(213, 'KAKORLANTAS_POLD', 'Kepala Korps Lalu Lintas Polda', 'Kakorlantas Polda', 175, 'STRUKTURAL', 'II.a', 'POLDA', 175, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(214, 'KAKORBRIMOB_POLD', 'Kepala Korps Brigade Mobil Polda', 'Kakorbrimob Polda', 176, 'STRUKTURAL', 'II.a', 'POLDA', 176, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(215, 'WAKADIVPROPAM_POLD', 'Wakil Kepala Divisi Profesi dan Pengamanan Polda', 'Wakadivpropam Polda', 177, 'STRUKTURAL', 'III.a', 'POLDA', 177, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(216, 'WAKADIVKUM_POLD', 'Wakil Kepala Divisi Hukum Polda', 'Wakadivkum Polda', 178, 'STRUKTURAL', 'III.a', 'POLDA', 178, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(217, 'WAKADIVHUMAS_POLD', 'Wakil Kepala Divisi Humas Polda', 'Wakadivhumas Polda', 179, 'STRUKTURAL', 'III.a', 'POLDA', 179, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(218, 'WAKADIVHUBINTER_POLD', 'Wakil Kepala Divisi Hubungan Internasional Polda', 'Wakadivhubinter Polda', 180, 'STRUKTURAL', 'III.a', 'POLDA', 180, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(219, 'WAKADIVTI_POLD', 'Wakil Kepala Divisi Teknologi Informasi Polda', 'Wakadiv TI Polda', 181, 'STRUKTURAL', 'III.a', 'POLDA', 181, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(220, 'WAKAKORLANTAS_POLD', 'Wakil Kepala Korps Lalu Lintas Polda', 'Wakakorlantas Polda', 182, 'STRUKTURAL', 'III.a', 'POLDA', 182, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(221, 'WAKAKORBRIMOB_POLD', 'Wakil Kepala Korps Brigade Mobil Polda', 'Wakakorbrimob Polda', 183, 'STRUKTURAL', 'III.a', 'POLDA', 183, NULL, 1, '2026-03-01 19:55:06', '2026-03-01 19:55:06'),
(222, 'KASUBDIT_I_INTELKAM', 'Kepala Subdirektorat I Intelijen', 'Kasubdit I Intel', 184, 'STRUKTURAL', 'III.b', 'POLDA', 184, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(223, 'KASUBDIT_II_INTELKAM', 'Kepala Subdirektorat II Intelijen', 'Kasubdit II Intel', 185, 'STRUKTURAL', 'III.b', 'POLDA', 185, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(224, 'KASUBDIT_III_INTELKAM', 'Kepala Subdirektorat III Intelijen', 'Kasubdit III Intel', 186, 'STRUKTURAL', 'III.b', 'POLDA', 186, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(225, 'KASUBDIT_I_BINMAS', 'Kepala Subdirektorat I Pembinaan Masyarakat', 'Kasubdit I Binmas', 187, 'STRUKTURAL', 'III.b', 'POLDA', 187, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(226, 'KASUBDIT_II_BINMAS', 'Kepala Subdirektorat II Pembinaan Masyarakat', 'Kasubdit II Binmas', 188, 'STRUKTURAL', 'III.b', 'POLDA', 188, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(227, 'KASUBDIT_I_LANTAS', 'Kepala Subdirektorat I Lalu Lintas', 'Kasubdit I Lantas', 189, 'STRUKTURAL', 'III.b', 'POLDA', 189, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(228, 'KASUBDIT_II_LANTAS', 'Kepala Subdirektorat II Lalu Lintas', 'Kasubdit II Lantas', 190, 'STRUKTURAL', 'III.b', 'POLDA', 190, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(229, 'KASUBDIT_I_SAMAPTA', 'Kepala Subdirektorat I Samapta', 'Kasubdit I Samapta', 191, 'STRUKTURAL', 'III.b', 'POLDA', 191, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(230, 'KASUBDIT_II_SAMAPTA', 'Kepala Subdirektorat II Samapta', 'Kasubdit II Samapta', 192, 'STRUKTURAL', 'III.b', 'POLDA', 192, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(231, 'KASI_INTELKAM_POLDA', 'Kepala Seksi Intelijen Polda', 'Kasi Intelkam Polda', 193, 'STRUKTURAL', 'IV.a', 'POLDA', 193, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(232, 'KASI_BINMAS_POLDA', 'Kepala Seksi Pembinaan Masyarakat Polda', 'Kasi Binmas Polda', 194, 'STRUKTURAL', 'IV.a', 'POLDA', 194, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(233, 'KASI_LANTAS_POLDA', 'Kepala Seksi Lalu Lintas Polda', 'Kasi Lantas Polda', 195, 'STRUKTURAL', 'IV.a', 'POLDA', 195, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(234, 'KASI_SAMAPTA_POLDA', 'Kepala Seksi Samapta Polda', 'Kasi Samapta Polda', 196, 'STRUKTURAL', 'IV.a', 'POLDA', 196, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(235, 'KASI_NARKOBA_POLDA', 'Kepala Seksi Narkoba Polda', 'Kasi Narkoba Polda', 197, 'STRUKTURAL', 'IV.a', 'POLDA', 197, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(236, 'KASI_PPA_POLDA', 'Kepala Seksi Pelindungan Perempuan dan Anak Polda', 'Kasi PPA Polda', 198, 'STRUKTURAL', 'IV.a', 'POLDA', 198, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(237, 'KASI_SIBER_POLDA', 'Kepala Seksi Siber Polda', 'Kasi Siber Polda', 199, 'STRUKTURAL', 'IV.a', 'POLDA', 199, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(238, 'KAUNIT_INTELKAM_POLDA', 'Kepala Unit Intelijen Polda', 'Kaunit Intelkam Polda', 200, 'STRUKTURAL', 'IV.a', 'POLDA', 200, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(239, 'KAUNIT_BINMAS_POLDA', 'Kepala Unit Pembinaan Masyarakat Polda', 'Kaunit Binmas Polda', 201, 'STRUKTURAL', 'IV.a', 'POLDA', 201, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(240, 'KAUNIT_LANTAS_POLDA', 'Kepala Unit Lalu Lintas Polda', 'Kaunit Lantas Polda', 202, 'STRUKTURAL', 'IV.a', 'POLDA', 202, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(241, 'KAUNIT_SAMAPTA_POLDA', 'Kepala Unit Samapta Polda', 'Kaunit Samapta Polda', 203, 'STRUKTURAL', 'IV.a', 'POLDA', 203, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(242, 'KAUNIT_NARKOBA_POLDA', 'Kepala Unit Narkoba Polda', 'Kaunit Narkoba Polda', 204, 'STRUKTURAL', 'IV.a', 'POLDA', 204, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(243, 'KAUNIT_PPA_POLDA', 'Kepala Unit Pelindungan Perempuan dan Anak Polda', 'Kaunit PPA Polda', 205, 'STRUKTURAL', 'IV.a', 'POLDA', 205, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(244, 'KAUNIT_SIBER_POLDA', 'Kepala Unit Siber Polda', 'Kaunit Siber Polda', 206, 'STRUKTURAL', 'IV.a', 'POLDA', 206, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(245, 'KASUBBAG_RENMIN_POLDA', 'Kepala Sub Bagian Perencanaan Minimal Polda', 'Kasubbag Renmin Polda', 207, 'STRUKTURAL', 'IV.b', 'POLDA', 207, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(246, 'KASUBBAG_SUMDA_POLDA', 'Kepala Sub Bagian Sumber Daya Polda', 'Kasubbag Sumda Polda', 208, 'STRUKTURAL', 'IV.b', 'POLDA', 208, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(247, 'KASUBBAG_UM_POLDA', 'Kepala Sub Bagian Umum Polda', 'Kasubbag Um Polda', 209, 'STRUKTURAL', 'IV.b', 'POLDA', 209, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(248, 'KASUBBAG_OPS_POLDA', 'Kepala Sub Bagian Operasional Polda', 'Kasubbag Ops Polda', 210, 'STRUKTURAL', 'IV.b', 'POLDA', 210, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(249, 'KASUBBAG_NARKOBA_POLDA', 'Kepala Sub Bagian Narkoba Polda', 'Kasubbag Narkoba Polda', 211, 'STRUKTURAL', 'IV.b', 'POLDA', 211, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(250, 'KASUBBAG_PPA_POLDA', 'Kepala Sub Bagian Pelindungan Perempuan dan Anak Polda', 'Kasubbag PPA Polda', 212, 'STRUKTURAL', 'IV.b', 'POLDA', 212, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(251, 'KASUBBAG_SIBER_POLDA', 'Kepala Sub Bagian Siber Polda', 'Kasubbag Siber Polda', 213, 'STRUKTURAL', 'IV.b', 'POLDA', 213, NULL, 1, '2026-03-01 19:55:38', '2026-03-01 19:55:38'),
(252, 'KASUBBAG_PROVOS_POLDA', 'Kepala Sub Bagian Provost Polda', 'Kasubbag Provos Polda', 214, 'STRUKTURAL', 'IV.b', 'POLDA', 214, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(253, 'KASUBBAG_WASSIDIK_POLDA', 'Kepala Sub Bagian Pengawasan Penyidikan Polda', 'Kasubbag Wassidik Polda', 215, 'STRUKTURAL', 'IV.b', 'POLDA', 215, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(254, 'KASUBBAG_IDENT_POLDA', 'Kepala Sub Bagian Identifikasi Polda', 'Kasubbag Ident Polda', 216, 'STRUKTURAL', 'IV.b', 'POLDA', 216, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(255, 'KASUBBAG_RENOPS_POLDA', 'Kepala Sub Bagian Perencanaan Operasional Polda', 'Kasubbag Renops Polda', 217, 'STRUKTURAL', 'IV.b', 'POLDA', 217, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(256, 'KASUBBAG_LAPORAN_POLDA', 'Kepala Sub Bagian Laporan Polda', 'Kasubbag Laporan Polda', 218, 'STRUKTURAL', 'IV.b', 'POLDA', 218, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(257, 'KASUBBAG_SUMDA_POLRES', 'Kepala Sub Bagian Sumber Daya Polres', 'Kasubbag Sumda Polres', 219, 'STRUKTURAL', 'V.a', 'POLRES', 219, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(258, 'KASUBBAG_UM_POLRES', 'Kepala Sub Bagian Umum Polres', 'Kasubbag Um Polres', 220, 'STRUKTURAL', 'V.a', 'POLRES', 220, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(259, 'KASUBBAG_OPS_POLRES', 'Kepala Sub Bagian Operasional Polres', 'Kasubbag Ops Polres', 221, 'STRUKTURAL', 'V.a', 'POLRES', 221, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(260, 'KASUBBAG_NARKOBA_POLRES', 'Kepala Sub Bagian Narkoba Polres', 'Kasubbag Narkoba Polres', 222, 'STRUKTURAL', 'V.a', 'POLRES', 222, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(261, 'KASUBBAG_PPA_POLRES', 'Kepala Sub Bagian Pelindungan Perempuan dan Anak Polres', 'Kasubbag PPA Polres', 223, 'STRUKTURAL', 'V.a', 'POLRES', 223, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(262, 'KASUBBAG_SIBER_POLRES', 'Kepala Sub Bagian Siber Polres', 'Kasubbag Siber Polres', 224, 'STRUKTURAL', 'V.a', 'POLRES', 224, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(263, 'KASUBBAG_PROVOS_POLRES', 'Kepala Sub Bagian Provost Polres', 'Kasubbag Provos Polres', 225, 'STRUKTURAL', 'V.a', 'POLRES', 225, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(264, 'KASUBBAG_WASSIDIK_POLRES', 'Kepala Sub Bagian Pengawasan Penyidikan Polres', 'Kasubbag Wassidik Polres', 226, 'STRUKTURAL', 'V.a', 'POLRES', 226, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(265, 'KASUBBAG_IDENT_POLRES', 'Kepala Sub Bagian Identifikasi Polres', 'Kasubbag Ident Polres', 227, 'STRUKTURAL', 'V.a', 'POLRES', 227, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(266, 'KASUBBAG_RENOPS_POLRES', 'Kepala Sub Bagian Perencanaan Operasional Polres', 'Kasubbag Renops Polres', 228, 'STRUKTURAL', 'V.a', 'POLRES', 228, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(267, 'KASUBBAG_LAPORAN_POLRES', 'Kepala Sub Bagian Laporan Polres', 'Kasubbag Laporan Polres', 229, 'STRUKTURAL', 'V.a', 'POLRES', 229, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(268, 'KASI_INTELKAM_POLRES', 'Kepala Seksi Intelijen Polres', 'Kasi Intelkam Polres', 230, 'STRUKTURAL', 'IV.a', 'POLRES', 230, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(269, 'KASI_BINMAS_POLRES', 'Kepala Seksi Pembinaan Masyarakat Polres', 'Kasi Binmas Polres', 231, 'STRUKTURAL', 'IV.a', 'POLRES', 231, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(270, 'KASI_LANTAS_POLRES', 'Kepala Seksi Lalu Lintas Polres', 'Kasi Lantas Polres', 232, 'STRUKTURAL', 'IV.a', 'POLRES', 232, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(271, 'KASI_SAMAPTA_POLRES', 'Kepala Seksi Samapta Polres', 'Kasi Samapta Polres', 233, 'STRUKTURAL', 'IV.a', 'POLRES', 233, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(272, 'KASI_NARKOBA_POLRES', 'Kepala Seksi Narkoba Polres', 'Kasi Narkoba Polres', 234, 'STRUKTURAL', 'IV.a', 'POLRES', 234, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(273, 'KASI_PPA_POLRES', 'Kepala Seksi Pelindungan Perempuan dan Anak Polres', 'Kasi PPA Polres', 235, 'STRUKTURAL', 'IV.a', 'POLRES', 235, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(274, 'KASI_SIBER_POLRES', 'Kepala Seksi Siber Polres', 'Kasi Siber Polres', 236, 'STRUKTURAL', 'IV.a', 'POLRES', 236, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(275, 'KASI_PROVOS_POLRES', 'Kepala Seksi Provost Polres', 'Kasi Provos Polres', 237, 'STRUKTURAL', 'IV.a', 'POLRES', 237, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(276, 'KASI_WASSIDIK_POLRES', 'Kepala Seksi Pengawasan Penyidikan Polres', 'Kasi Wassidik Polres', 238, 'STRUKTURAL', 'IV.a', 'POLRES', 238, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(277, 'KASI_IDENT_POLRES', 'Kepala Seksi Identifikasi Polres', 'Kasi Ident Polres', 239, 'STRUKTURAL', 'IV.a', 'POLRES', 239, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(278, 'KASI_RENOPS_POLRES', 'Kepala Seksi Perencanaan Operasional Polres', 'Kasi Renops Polres', 240, 'STRUKTURAL', 'IV.a', 'POLRES', 240, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(279, 'KASI_LAPORAN_POLRES', 'Kepala Seksi Laporan Polres', 'Kasi Laporan Polres', 241, 'STRUKTURAL', 'IV.a', 'POLRES', 241, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(280, 'KAUNIT_INTELKAM_POLRES', 'Kepala Unit Intelijen Polres', 'Kaunit Intelkam Polres', 242, 'STRUKTURAL', 'IV.a', 'POLRES', 242, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(281, 'KAUNIT_BINMAS_POLRES', 'Kepala Unit Pembinaan Masyarakat Polres', 'Kaunit Binmas Polres', 243, 'STRUKTURAL', 'IV.a', 'POLRES', 243, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(282, 'KAUNIT_LANTAS_POLRES', 'Kepala Unit Lalu Lintas Polres', 'Kaunit Lantas Polres', 244, 'STRUKTURAL', 'IV.a', 'POLRES', 244, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(283, 'KAUNIT_SAMAPTA_POLRES', 'Kepala Unit Samapta Polres', 'Kaunit Samapta Polres', 245, 'STRUKTURAL', 'IV.a', 'POLRES', 245, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(284, 'KAUNIT_NARKOBA_POLRES', 'Kepala Unit Narkoba Polres', 'Kaunit Narkoba Polres', 246, 'STRUKTURAL', 'IV.a', 'POLRES', 246, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(285, 'KAUNIT_PPA_POLRES', 'Kepala Unit Pelindungan Perempuan dan Anak Polres', 'Kaunit PPA Polres', 247, 'STRUKTURAL', 'IV.a', 'POLRES', 247, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(286, 'KAUNIT_SIBER_POLRES', 'Kepala Unit Siber Polres', 'Kaunit Siber Polres', 248, 'STRUKTURAL', 'IV.a', 'POLRES', 248, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(287, 'KAUNIT_PROVOS_POLRES', 'Kepala Unit Provost Polres', 'Kaunit Provos Polres', 249, 'STRUKTURAL', 'IV.a', 'POLRES', 249, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(288, 'KAUNIT_WASSIDIK_POLRES', 'Kepala Unit Pengawasan Penyidikan Polres', 'Kaunit Wassidik Polres', 250, 'STRUKTURAL', 'IV.a', 'POLRES', 250, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(289, 'KAUNIT_IDENT_POLRES', 'Kepala Unit Identifikasi Polres', 'Kaunit Ident Polres', 251, 'STRUKTURAL', 'IV.a', 'POLRES', 251, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(290, 'KAUNIT_RENOPS_POLRES', 'Kepala Unit Perencanaan Operasional Polres', 'Kaunit Renops Polres', 252, 'STRUKTURAL', 'IV.a', 'POLRES', 252, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(291, 'KAUNIT_LAPORAN_POLRES', 'Kepala Unit Laporan Polres', 'Kaunit Laporan Polres', 253, 'STRUKTURAL', 'IV.a', 'POLRES', 253, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(292, 'PANITINTEL_POLRES', 'Pembantu Kepala Unit Intelijen Polres', 'Panit Intel Polres', 254, 'STRUKTURAL', 'VI.a', 'POLRES', 254, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(293, 'PANITRESKRIM_POLRES', 'Pembantu Kepala Unit Reserse Kriminal Polres', 'Panit Reskrim Polres', 255, 'STRUKTURAL', 'VI.a', 'POLRES', 255, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(294, 'PANITLANTAS_POLRES', 'Pembantu Kepala Unit Lalu Lintas Polres', 'Panit Lantas Polres', 256, 'STRUKTURAL', 'VI.a', 'POLRES', 256, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(295, 'PANITBINMAS_POLRES', 'Pembantu Kepala Unit Pembinaan Masyarakat Polres', 'Panit Binmas Polres', 257, 'STRUKTURAL', 'VI.a', 'POLRES', 257, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(296, 'PANITSAMAPTA_POLRES', 'Pembantu Kepala Unit Samapta Polres', 'Panit Samapta Polres', 258, 'STRUKTURAL', 'VI.a', 'POLRES', 258, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(297, 'PANITNARKOBA_POLRES', 'Pembantu Kepala Unit Narkoba Polres', 'Panit Narkoba Polres', 259, 'STRUKTURAL', 'VI.a', 'POLRES', 259, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(298, 'PANITPPA_POLRES', 'Pembantu Kepala Unit Pelindungan Perempuan dan Anak Polres', 'Panit PPA Polres', 260, 'STRUKTURAL', 'VI.a', 'POLRES', 260, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(299, 'PANITSIBER_POLRES', 'Pembantu Kepala Unit Siber Polres', 'Panit Siber Polres', 261, 'STRUKTURAL', 'VI.a', 'POLRES', 261, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(300, 'PANITPROVOS_POLRES', 'Pembantu Kepala Unit Provost Polres', 'Panit Provos Polres', 262, 'STRUKTURAL', 'VI.a', 'POLRES', 262, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(301, 'PANITWASSIDIK_POLRES', 'Pembantu Kepala Unit Pengawasan Penyidikan Polres', 'Panit Wassidik Polres', 263, 'STRUKTURAL', 'VI.a', 'POLRES', 263, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(302, 'PANITIDENT_POLRES', 'Pembantu Kepala Unit Identifikasi Polres', 'Panit Ident Polres', 264, 'STRUKTURAL', 'VI.a', 'POLRES', 264, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(303, 'PANITRENOPS_POLRES', 'Pembantu Kepala Unit Perencanaan Operasional Polres', 'Panit Renops Polres', 265, 'STRUKTURAL', 'VI.a', 'POLRES', 265, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(304, 'PANITLAPORAN_POLRES', 'Pembantu Kepala Unit Laporan Polres', 'Panit Laporan Polres', 266, 'STRUKTURAL', 'VI.a', 'POLRES', 266, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(305, 'BANITINTEL_POLRES', 'Batur Pembantu Intelijen Polres', 'Banit Intel Polres', 267, 'STRUKTURAL', 'VI.b', 'POLRES', 267, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(306, 'BANITRESKRIM_POLRES', 'Batur Pembantu Reserse Kriminal Polres', 'Banit Reskrim Polres', 268, 'STRUKTURAL', 'VI.b', 'POLRES', 268, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(307, 'BANITLANTAS_POLRES', 'Batur Pembantu Lalu Lintas Polres', 'Banit Lantas Polres', 269, 'STRUKTURAL', 'VI.b', 'POLRES', 269, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(308, 'BANITBINMAS_POLRES', 'Batur Pembantu Pembinaan Masyarakat Polres', 'Banit Binmas Polres', 270, 'STRUKTURAL', 'VI.b', 'POLRES', 270, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(309, 'BANITSAMAPTA_POLRES', 'Batur Pembantu Samapta Polres', 'Banit Samapta Polres', 271, 'STRUKTURAL', 'VI.b', 'POLRES', 271, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(310, 'BANITNARKOBA_POLRES', 'Batur Pembantu Narkoba Polres', 'Banit Narkoba Polres', 272, 'STRUKTURAL', 'VI.b', 'POLRES', 272, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(311, 'BANITPPA_POLRES', 'Batur Pembantu Pelindungan Perempuan dan Anak Polres', 'Banit PPA Polres', 273, 'STRUKTURAL', 'VI.b', 'POLRES', 273, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(312, 'BANITSIBER_POLRES', 'Batur Pembantu Siber Polres', 'Banit Siber Polres', 274, 'STRUKTURAL', 'VI.b', 'POLRES', 274, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(313, 'BANITPROVOS_POLRES', 'Batur Pembantu Provost Polres', 'Banit Provos Polres', 275, 'STRUKTURAL', 'VI.b', 'POLRES', 275, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(314, 'BANITWASSIDIK_POLRES', 'Batur Pembantu Pengawasan Penyidikan Polres', 'Banit Wassidik Polres', 276, 'STRUKTURAL', 'VI.b', 'POLRES', 276, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(315, 'BANITIDENT_POLRES', 'Batur Pembantu Identifikasi Polres', 'Banit Ident Polres', 277, 'STRUKTURAL', 'VI.b', 'POLRES', 277, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(316, 'BANITRENOPS_POLRES', 'Batur Pembantu Perencanaan Operasional Polres', 'Banit Renops Polres', 278, 'STRUKTURAL', 'VI.b', 'POLRES', 278, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18'),
(317, 'BANITLAPORAN_POLRES', 'Batur Pembantu Laporan Polres', 'Banit Laporan Polres', 279, 'STRUKTURAL', 'VI.b', 'POLRES', 279, NULL, 1, '2026-03-01 19:56:18', '2026-03-01 19:56:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_pangkat_pns`
--

CREATE TABLE `master_pangkat_pns` (
  `id` int(11) NOT NULL,
  `kode_pangkat` varchar(20) NOT NULL,
  `nama_pangkat` varchar(100) NOT NULL,
  `golongan` varchar(20) DEFAULT NULL,
  `level_hierarki` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_pangkat_pns`
--

INSERT INTO `master_pangkat_pns` (`id`, `kode_pangkat`, `nama_pangkat`, `golongan`, `level_hierarki`, `created_at`) VALUES
(1, 'Ia', 'Juru Muda', 'Ia', 1, '2026-03-01 18:29:40'),
(2, 'Ib', 'Juru Muda Tingkat I', 'Ib', 2, '2026-03-01 18:29:40'),
(3, 'Ic', 'Juru', 'Ic', 3, '2026-03-01 18:29:40'),
(4, 'Id', 'Juru Tingkat I', 'Id', 4, '2026-03-01 18:29:40'),
(5, 'IIa', 'Pengatur Muda', 'IIa', 5, '2026-03-01 18:29:40'),
(6, 'IIb', 'Pengatur Muda Tingkat I', 'IIb', 6, '2026-03-01 18:29:40'),
(7, 'IIc', 'Pengatur', 'IIc', 7, '2026-03-01 18:29:40'),
(8, 'IId', 'Pengatur Tingkat I', 'IId', 8, '2026-03-01 18:29:40'),
(9, 'IIIa', 'Penata Muda', 'IIIa', 9, '2026-03-01 18:29:40'),
(10, 'IIIb', 'Penata Muda Tingkat I', 'IIIb', 10, '2026-03-01 18:29:40'),
(11, 'IIIc', 'Penata', 'IIIc', 11, '2026-03-01 18:29:40'),
(12, 'IIId', 'Penata Tingkat I', 'IIId', 12, '2026-03-01 18:29:40'),
(13, 'IVa', 'Pembina', 'IVa', 13, '2026-03-01 18:29:40'),
(14, 'IVb', 'Pembina Tingkat I', 'IVb', 14, '2026-03-01 18:29:40'),
(15, 'IVc', 'Pembina Muda', 'IVc', 15, '2026-03-01 18:29:40'),
(16, 'IVd', 'Pembina Madya', 'IVd', 16, '2026-03-01 18:29:40'),
(17, 'IVe', 'Pembina Utama', 'IVe', 17, '2026-03-01 18:29:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_pangkat_polri`
--

CREATE TABLE `master_pangkat_polri` (
  `id` int(11) NOT NULL,
  `kode_pangkat` varchar(20) NOT NULL,
  `nama_pangkat` varchar(100) NOT NULL,
  `kategori` enum('BINTARA','PERWIRA','PERWIRA_TINGGI','TAMTAMA') NOT NULL,
  `kategori_ext` varchar(50) DEFAULT NULL,
  `level_hierarki` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_pangkat_polri`
--

INSERT INTO `master_pangkat_polri` (`id`, `kode_pangkat`, `nama_pangkat`, `kategori`, `kategori_ext`, `level_hierarki`, `created_at`) VALUES
(89, 'JENDRAL_POLISI', 'Jenderal Polisi', 'PERWIRA_TINGGI', 'PERWIRA TINGGI', 1, '2026-03-01 18:35:25'),
(90, 'KOMJEN_POLISI', 'Komisaris Jenderal Polisi', 'PERWIRA_TINGGI', 'PERWIRA TINGGI', 2, '2026-03-01 18:35:25'),
(91, 'IRJEN_POLISI', 'Inspektur Jenderal Polisi', 'PERWIRA_TINGGI', 'PERWIRA TINGGI', 3, '2026-03-01 18:35:33'),
(92, 'BRIGJEN_POLISI', 'Brigadir Jenderal Polisi', 'PERWIRA_TINGGI', 'PERWIRA TINGGI', 4, '2026-03-01 18:35:33'),
(93, 'KOMBES_POLISI', 'Komisaris Besar Polisi', 'PERWIRA', 'PERWIRA MENENGAH', 5, '2026-03-01 18:35:33'),
(94, 'AKBP_POLISI', 'Ajun Komisaris Besar Polisi', 'PERWIRA', 'PERWIRA MENENGAH', 6, '2026-03-01 18:35:33'),
(95, 'KOMPOL', 'Komisaris Polisi', 'PERWIRA', 'PERWIRA MENENGAH', 7, '2026-03-01 18:35:33'),
(96, 'AKP', 'Ajun Komisaris Polisi', 'PERWIRA', 'PERWIRA PERTAMA', 8, '2026-03-01 18:35:33'),
(97, 'IPTU', 'Inspektur Polisi Satu', 'PERWIRA', 'PERWIRA PERTAMA', 9, '2026-03-01 18:35:33'),
(98, 'IPDA', 'Inspektur Polisi Dua', 'PERWIRA', 'PERWIRA PERTAMA', 10, '2026-03-01 18:35:33'),
(111, 'AIPTU', 'Ajun Inspektur Polisi Satu', 'BINTARA', 'BINTARA TINGGI', 11, '2026-03-01 18:35:41'),
(112, 'AIPDA', 'Ajun Inspektur Polisi Dua', 'BINTARA', 'BINTARA TINGGI', 12, '2026-03-01 18:35:41'),
(113, 'BRIPKA', 'Brigadir Polisi Kepala', 'BINTARA', 'BINTARA', 13, '2026-03-01 18:35:41'),
(114, 'BRIGPOL', 'Brigadir Polisi', 'BINTARA', 'BINTARA', 14, '2026-03-01 18:35:41'),
(115, 'BRIPTU', 'Brigadir Polisi Satu', 'BINTARA', 'BINTARA', 15, '2026-03-01 18:35:41'),
(116, 'BRIPDA', 'Brigadir Polisi Dua', 'BINTARA', 'BINTARA', 16, '2026-03-01 18:35:41'),
(133, 'ABRIP', 'Ajun Brigadir Polisi', 'TAMTAMA', 'TAMTAMA', 17, '2026-03-01 18:37:01'),
(134, 'ABRIPTU', 'Ajun Brigadir Polisi Satu', 'TAMTAMA', 'TAMTAMA', 18, '2026-03-01 18:37:01'),
(135, 'ABRIPDA', 'Ajun Brigadir Polisi Dua', 'TAMTAMA', 'TAMTAMA', 19, '2026-03-01 18:37:01'),
(136, 'BHARAKA', 'Bhayangkara Kepala', 'TAMTAMA', 'TAMTAMA', 20, '2026-03-01 18:37:01'),
(137, 'BHARATU', 'Bhayangkara Satu', 'TAMTAMA', 'TAMTAMA', 21, '2026-03-01 18:37:01'),
(138, 'BHARADA', 'Bhayangkara Dua', 'TAMTAMA', 'TAMTAMA', 22, '2026-03-01 18:37:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_status_jabatan`
--

CREATE TABLE `master_status_jabatan` (
  `id` int(11) NOT NULL,
  `status_jabatan` varchar(20) NOT NULL,
  `deskripsi` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_status_jabatan`
--

INSERT INTO `master_status_jabatan` (`id`, `status_jabatan`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'DEFINITIF', 'Jabatan Definitif', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(2, 'PLT', 'Pelaksana Tugas', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(3, 'PJS', 'Penjabat Sementara', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(4, 'PLH', 'Pelaksana Harian', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(5, 'PJ', 'Pejabat', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(6, 'PS', 'Penjabat Sementara', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(7, 'NON_POLRI', 'Non-Polri', '2026-03-01 07:11:06', '2026-03-01 07:11:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_tipe_kantor_polisi`
--

CREATE TABLE `master_tipe_kantor_polisi` (
  `id` int(11) NOT NULL,
  `tipe_kantor` varchar(50) NOT NULL,
  `klasifikasi` varchar(50) DEFAULT NULL,
  `level_kompleksitas` varchar(20) DEFAULT NULL,
  `pimpinan_default_pangkat` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_tipe_kantor_polisi`
--

INSERT INTO `master_tipe_kantor_polisi` (`id`, `tipe_kantor`, `klasifikasi`, `level_kompleksitas`, `pimpinan_default_pangkat`, `created_at`, `updated_at`) VALUES
(1, 'POLDA', 'Kepolisian Daerah', 'Tingkat Provinsi', 'Irjen Pol/Brigjen Pol', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(2, 'POLRESTABES', 'Kepolisian Resor Kota Besar', 'Tingkat Kota Besar', 'Kombes Pol', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(3, 'POLRESTA', 'Kepolisian Resor Kota', 'Tingkat Kota', 'AKBP', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(5, 'POLSEK', 'Kepolisian Sektor', 'Tingkat Kecamatan', 'Kompol/AKP', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(7, 'POLPOS', 'Kepolisian Pos', 'Tingkat Dusun/RT', 'AIPDA/AIPTU', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(8, 'MABES POLRI', 'Markas Besar Polri', 'Tingkat Pusat', 'Jenderal Pol', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(9, 'POLDA METRO', 'Polda Wilayah Metropolitan', 'Tingkat Provinsi', 'Irjen Pol', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(10, 'POLRES METRO', 'Polres Wilayah Metropolitan', 'Tingkat Kota', 'AKBP', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(11, 'POLSEK METRO', 'Polsek Wilayah Metropolitan', 'Tingkat Kecamatan', 'Kompol/AKP', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(12, 'POLSEK KOTA', 'Polsek Wilayah Kota', 'Tingkat Kecamatan', 'Kompol/AKP', '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(13, 'POLSEK DESA', 'Polsek Wilayah Desa', 'Tingkat Kecamatan', 'IPDA', '2026-03-01 07:11:06', '2026-03-01 07:11:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `order_index` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `menu`
--

INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `order_index`, `is_active`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'fas fa-tachometer-alt', 'dashboard', 1, 1, NULL, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(2, 'Data Master', 'fas fa-database', 'master', 2, 1, NULL, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(3, 'Personel', 'fas fa-users', 'personel', 3, 1, NULL, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(4, 'Operasi', 'fas fa-cogs', 'operations', 4, 1, NULL, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(5, 'Laporan', 'fas fa-file-alt', 'reports', 5, 1, NULL, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(6, 'Pengaturan', 'fas fa-cog', 'settings', 6, 1, NULL, '2026-03-01 14:33:49', '2026-03-01 14:33:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `m_jabatan`
--

CREATE TABLE `m_jabatan` (
  `id` int(11) NOT NULL,
  `kode_jabatan` varchar(20) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL,
  `level_jabatan` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `m_jabatan`
--

INSERT INTO `m_jabatan` (`id`, `kode_jabatan`, `nama_jabatan`, `level_jabatan`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'KAPOLRES', 'Kepala Kepolisian Resor', 1, NULL, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(2, 'WAKAPOLRES', 'Wakil Kepala Kepolisian Resor', 2, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(3, 'KABAG OPS', 'Kepala Bagian Operasional', 3, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(4, 'KASAT RESKRIM', 'Kepala Satuan Reserse Kriminal', 3, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(5, 'KASAT SAMAPTA', 'Kepala Satuan Samapta Bhayangkara', 3, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(6, 'KASAT LANTAS', 'Kepala Satuan Lalu Lintas', 3, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(7, 'KASAT RESNARKOBA', 'Kepala Satuan Reserse Narkoba', 3, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(8, 'KASAT PAMOBVIT', 'Kepala Satuan Pengamanan Objek Vital', 3, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(9, 'KASAT POLAIRUD', 'Kepala Satuan Polisi Air dan Udara', 3, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(10, 'KASIDOKKES', 'Kepala Seksi Dokter Kesehatan', 3, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(11, 'KASIHUMAS', 'Kepala Seksi Humas', 3, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(12, 'KASUBSIBANKUM', 'Kepala Subsi Pembinaan Kemitraan', 3, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(13, 'KAPOLSEK', 'Kepala Kepolisian Sektor', 4, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(14, 'KANIT RESKRIM', 'Kepala Unit Reserse Kriminal', 5, 14, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(15, 'KANIT SAMAPTA', 'Kepala Unit Samapta', 5, 14, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(16, 'KANIT LANTAS', 'Kepala Unit Lalu Lintas', 5, 14, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(17, 'KANIT INTELKAM', 'Kepala Unit Intelijen Keamanan', 5, 14, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(18, 'KANIT BINMAS', 'Kepala Unit Pembinaan Masyarakat', 5, 14, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(19, 'KANIT PROPAM', 'Kepala Unit Profesi dan Pengamanan', 5, 14, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(20, 'KASIWAS', 'Kepala Seksi Pengawasan', 3, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(21, 'ADC KAPOLRES', 'Aide de Camp Kapolres', 4, 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `page_key` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `target_role` enum('all','super_admin','admin','kabag_ops','kaur_ops','user') DEFAULT 'all',
  `is_active` tinyint(1) DEFAULT 1,
  `order_index` int(11) DEFAULT 0,
  `parent_page_id` int(11) DEFAULT NULL,
  `page_type` enum('standard','dashboard','report','settings','profile') DEFAULT 'standard',
  `layout_type` enum('default','full_width','sidebar','minimal') DEFAULT 'default',
  `requires_auth` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pages`
--

INSERT INTO `pages` (`id`, `page_key`, `title`, `description`, `target_role`, `is_active`, `order_index`, `parent_page_id`, `page_type`, `layout_type`, `requires_auth`, `created_at`, `updated_at`) VALUES
(1, 'dashboard', 'Dashboard Utama', 'Halaman dashboard dengan statistik real-time', 'all', 1, 1, NULL, 'dashboard', 'sidebar', 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14'),
(2, 'personel_ultra', 'Data Personel', 'Manajemen data personel kepolisian', 'all', 1, 2, NULL, 'standard', 'sidebar', 1, '2026-03-01 16:42:14', '2026-03-01 17:59:56'),
(3, 'operations', 'Data Operasi', 'Manajemen operasi kepolisian', 'all', 1, 3, NULL, 'standard', 'sidebar', 1, '2026-03-01 16:42:14', '2026-03-01 17:40:49'),
(4, 'reports', 'Laporan', 'Sistem pelaporan operasional', 'all', 1, 4, NULL, 'report', 'sidebar', 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14'),
(5, 'assignments', 'Tugas', 'Manajemen tugas dan penugasan', 'all', 1, 5, NULL, 'standard', 'sidebar', 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14'),
(6, 'settings', 'Pengaturan', 'Pengaturan sistem', 'super_admin', 1, 6, NULL, 'settings', 'sidebar', 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14'),
(7, 'profile', 'Profile', 'Profile pengguna', 'all', 1, 7, NULL, 'profile', 'minimal', 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14'),
(8, 'help', 'Bantuan', 'Panduan dan bantuan sistem', 'all', 1, 8, NULL, 'standard', 'full_width', 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14'),
(25, 'kantor', 'Data Kantor', 'Manajemen data kantor kepolisian', 'all', 1, 4, NULL, 'standard', 'sidebar', 1, '2026-03-02 05:32:27', '2026-03-02 05:32:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `page_details`
--

CREATE TABLE `page_details` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `content_data` longtext DEFAULT NULL,
  `template_file` varchar(255) DEFAULT NULL,
  `meta_title` varchar(200) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `custom_css` text DEFAULT NULL,
  `custom_js` text DEFAULT NULL,
  `layout_type` enum('default','full_width','sidebar','minimal') DEFAULT 'default',
  `sidebar_enabled` tinyint(1) DEFAULT 1,
  `header_enabled` tinyint(1) DEFAULT 1,
  `footer_enabled` tinyint(1) DEFAULT 1,
  `breadcrumb_enabled` tinyint(1) DEFAULT 1,
  `search_enabled` tinyint(1) DEFAULT 1,
  `notifications_enabled` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `page_details`
--

INSERT INTO `page_details` (`id`, `page_id`, `content_data`, `template_file`, `meta_title`, `meta_description`, `meta_keywords`, `custom_css`, `custom_js`, `layout_type`, `sidebar_enabled`, `header_enabled`, `footer_enabled`, `breadcrumb_enabled`, `search_enabled`, `notifications_enabled`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, 'Dashboard - BAGOPS POLRES SAMOSIR', 'Dashboard utama sistem BAGOPS POLRES SAMOSIR dengan statistik real-time', 'dashboard, bagops, polres, samosir, statistik', NULL, NULL, 'sidebar', 1, 1, 1, 1, 1, 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14'),
(2, 2, NULL, NULL, 'Data Personel - BAGOPS POLRES SAMOSIR', 'Manajemen data personel kepolisian POLRES SAMOSIR', 'personel, data, kepolisian, pegawai, bagops', NULL, NULL, 'sidebar', 1, 1, 1, 1, 1, 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14'),
(3, 3, NULL, NULL, 'Data Operasi - BAGOPS POLRES SAMOSIR', 'Manajemen operasi kepolisian POLRES SAMOSIR', 'operasi, kegiatan, polisi, bagops', NULL, NULL, 'sidebar', 1, 1, 1, 1, 1, 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14'),
(4, 4, NULL, NULL, 'Laporan - BAGOPS POLRES SAMOSIR', 'Sistem pelaporan operasional POLRES SAMOSIR', 'laporan, report, dokumentasi, bagops', NULL, NULL, 'sidebar', 1, 1, 1, 1, 1, 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14'),
(5, 5, NULL, NULL, 'Tugas - BAGOPS POLRES SAMOSIR', 'Manajemen tugas dan penugasan personel', 'tugas, assignment, penugasan, bagops', NULL, NULL, 'sidebar', 1, 1, 1, 1, 1, 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14'),
(6, 6, NULL, NULL, 'Pengaturan - BAGOPS POLRES SAMOSIR', 'Pengaturan sistem BAGOPS POLRES SAMOSIR', 'settings, pengaturan, konfigurasi, admin', NULL, NULL, 'sidebar', 1, 1, 1, 1, 1, 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14'),
(7, 7, NULL, NULL, 'Profile - BAGOPS POLRES SAMOSIR', 'Profile pengguna sistem BAGOPS', 'profile, user, pengguna, akun', NULL, NULL, 'minimal', 1, 1, 1, 1, 1, 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14'),
(8, 8, NULL, NULL, 'Bantuan - BAGOPS POLRES SAMOSIR', 'Panduan dan bantuan sistem BAGOPS', 'bantuan, help, panduan, tutorial', NULL, NULL, 'full_width', 1, 1, 1, 1, 1, 1, '2026-03-01 16:42:14', '2026-03-01 16:42:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `page_permissions`
--

CREATE TABLE `page_permissions` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `role_name` enum('super_admin','admin','kabag_ops','kaur_ops','user') NOT NULL,
  `permission_type` enum('view','create','edit','delete','export','import','manage','assign_personnel') NOT NULL,
  `is_granted` tinyint(1) DEFAULT 0,
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`conditions`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `page_permissions`
--

INSERT INTO `page_permissions` (`id`, `page_id`, `role_name`, `permission_type`, `is_granted`, `conditions`, `created_at`) VALUES
(1, 1, 'super_admin', 'view', 1, NULL, '2026-03-01 16:42:14'),
(2, 1, 'admin', 'view', 1, NULL, '2026-03-01 16:42:14'),
(3, 1, 'kabag_ops', 'view', 1, NULL, '2026-03-01 16:42:14'),
(4, 1, 'kaur_ops', 'view', 1, NULL, '2026-03-01 16:42:14'),
(5, 1, 'user', 'view', 1, NULL, '2026-03-01 16:42:14'),
(6, 2, 'super_admin', 'view', 1, NULL, '2026-03-01 16:42:14'),
(7, 2, 'super_admin', 'create', 1, NULL, '2026-03-01 16:42:14'),
(8, 2, 'super_admin', 'edit', 1, NULL, '2026-03-01 16:42:14'),
(9, 2, 'super_admin', 'delete', 1, NULL, '2026-03-01 16:42:14'),
(10, 2, 'super_admin', 'import', 1, NULL, '2026-03-01 16:42:14'),
(11, 2, 'super_admin', 'export', 1, NULL, '2026-03-01 16:42:14'),
(12, 2, 'admin', 'view', 1, NULL, '2026-03-01 16:42:14'),
(13, 2, 'admin', 'create', 1, NULL, '2026-03-01 16:42:14'),
(14, 2, 'admin', 'edit', 1, NULL, '2026-03-01 16:42:14'),
(15, 2, 'admin', 'delete', 1, NULL, '2026-03-01 16:42:14'),
(16, 2, 'admin', 'import', 1, NULL, '2026-03-01 16:42:14'),
(17, 2, 'admin', 'export', 1, NULL, '2026-03-01 16:42:14'),
(18, 2, 'kabag_ops', 'view', 1, NULL, '2026-03-01 16:42:14'),
(19, 2, 'kabag_ops', 'edit', 1, NULL, '2026-03-01 16:42:14'),
(20, 2, 'kabag_ops', 'export', 1, NULL, '2026-03-01 16:42:14'),
(21, 2, 'kaur_ops', 'view', 1, NULL, '2026-03-01 16:42:14'),
(22, 2, 'kaur_ops', 'export', 1, NULL, '2026-03-01 16:42:14'),
(23, 2, 'user', 'view', 1, NULL, '2026-03-01 16:42:14'),
(24, 3, 'super_admin', 'view', 1, NULL, '2026-03-01 16:42:14'),
(25, 3, 'super_admin', 'create', 1, NULL, '2026-03-01 16:42:14'),
(26, 3, 'super_admin', 'edit', 1, NULL, '2026-03-01 16:42:14'),
(27, 3, 'super_admin', 'delete', 1, NULL, '2026-03-01 16:42:14'),
(28, 3, 'admin', 'view', 1, NULL, '2026-03-01 16:42:14'),
(29, 3, 'admin', 'create', 1, NULL, '2026-03-01 16:42:14'),
(30, 3, 'admin', 'edit', 1, NULL, '2026-03-01 16:42:14'),
(31, 3, 'admin', 'delete', 1, NULL, '2026-03-01 16:42:14'),
(32, 3, 'kabag_ops', 'view', 1, NULL, '2026-03-01 16:42:14'),
(33, 3, 'kabag_ops', 'create', 1, NULL, '2026-03-01 16:42:14'),
(34, 3, 'kabag_ops', 'edit', 1, NULL, '2026-03-01 16:42:14'),
(35, 3, 'kabag_ops', 'delete', 1, NULL, '2026-03-01 16:42:14'),
(36, 3, 'kabag_ops', 'assign_personnel', 1, NULL, '2026-03-01 16:42:14'),
(37, 3, 'kaur_ops', 'view', 1, NULL, '2026-03-01 16:42:14'),
(38, 3, 'user', 'view', 0, NULL, '2026-03-01 16:42:14'),
(39, 6, 'super_admin', 'view', 1, NULL, '2026-03-01 16:42:14'),
(40, 6, 'super_admin', 'create', 1, NULL, '2026-03-01 16:42:14'),
(41, 6, 'super_admin', 'edit', 1, NULL, '2026-03-01 16:42:14'),
(42, 6, 'super_admin', 'delete', 1, NULL, '2026-03-01 16:42:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `page_requirements`
--

CREATE TABLE `page_requirements` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `requirement_type` enum('table','statistic','chart','filter','action','permission') NOT NULL,
  `requirement_key` varchar(100) NOT NULL,
  `requirement_value` text DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT 1,
  `order_index` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `page_requirements`
--

INSERT INTO `page_requirements` (`id`, `page_id`, `requirement_type`, `requirement_key`, `requirement_value`, `is_required`, `order_index`, `created_at`) VALUES
(1, 1, 'table', 'personel', 'personel table with active records', 1, 1, '2026-03-01 16:42:14'),
(2, 1, 'table', 'operations', 'operations table with active status', 1, 2, '2026-03-01 16:42:14'),
(3, 1, 'table', 'daily_reports', 'daily reports for today', 1, 3, '2026-03-01 16:42:14'),
(4, 1, 'table', 'assignments', 'pending assignments', 1, 4, '2026-03-01 16:42:14'),
(5, 1, 'statistic', 'total_personel', 'COUNT of active personel', 1, 10, '2026-03-01 16:42:14'),
(6, 1, 'statistic', 'active_operations', 'COUNT of active operations', 1, 11, '2026-03-01 16:42:14'),
(7, 1, 'statistic', 'today_reports', 'COUNT of today reports', 1, 12, '2026-03-01 16:42:14'),
(8, 1, 'statistic', 'pending_tasks', 'COUNT of pending assignments', 1, 13, '2026-03-01 16:42:14'),
(9, 1, 'chart', 'personel_chart', 'personel distribution by rank', 1, 20, '2026-03-01 16:42:14'),
(10, 1, 'chart', 'operations_chart', 'operations distribution by status', 1, 21, '2026-03-01 16:42:14'),
(11, 1, 'permission', 'view_dashboard', 'Basic dashboard access', 1, 30, '2026-03-01 16:42:14'),
(12, 2, 'table', 'personel', 'Complete personel data with joins', 1, 1, '2026-03-01 16:42:14'),
(13, 2, 'table', 'pangkat', 'Rank data for filters', 1, 2, '2026-03-01 16:42:14'),
(14, 2, 'table', 'jabatan', 'Position data for filters', 1, 3, '2026-03-01 16:42:14'),
(15, 2, 'table', 'kantor', 'Office data for filters', 1, 4, '2026-03-01 16:42:14'),
(16, 2, 'filter', 'unit', 'Office/unit filter dropdown', 1, 10, '2026-03-01 16:42:14'),
(17, 2, 'filter', 'pangkat', 'Rank filter dropdown', 1, 11, '2026-03-01 16:42:14'),
(18, 2, 'filter', 'status', 'Status filter (active/inactive)', 1, 12, '2026-03-01 16:42:14'),
(19, 2, 'action', 'create', 'Create new personel', 1, 20, '2026-03-01 16:42:14'),
(20, 2, 'action', 'edit', 'Edit existing personel', 1, 21, '2026-03-01 16:42:14'),
(21, 2, 'action', 'delete', 'Delete personel', 1, 22, '2026-03-01 16:42:14'),
(22, 2, 'action', 'import', 'Import personel from Excel', 1, 23, '2026-03-01 16:42:14'),
(23, 2, 'action', 'export', 'Export personel to Excel', 1, 24, '2026-03-01 16:42:14'),
(24, 2, 'permission', 'view_personel', 'View personel data', 1, 30, '2026-03-01 16:42:14'),
(25, 2, 'permission', 'manage_personel', 'Full personel management', 1, 31, '2026-03-01 16:42:14'),
(26, 3, 'table', 'operations', 'Operations with personnel count', 1, 1, '2026-03-01 16:42:14'),
(27, 3, 'table', 'operation_personnel', 'Personnel assigned to operations', 1, 2, '2026-03-01 16:42:14'),
(28, 3, 'table', 'operation_reports', 'Reports related to operations', 1, 3, '2026-03-01 16:42:14'),
(29, 3, 'filter', 'status', 'Operation status filter', 1, 10, '2026-03-01 16:42:14'),
(30, 3, 'filter', 'date_range', 'Date range filter', 1, 11, '2026-03-01 16:42:14'),
(31, 3, 'filter', 'type', 'Operation type filter', 1, 12, '2026-03-01 16:42:14'),
(32, 3, 'action', 'create', 'Create new operation', 1, 20, '2026-03-01 16:42:14'),
(33, 3, 'action', 'edit', 'Edit existing operation', 1, 21, '2026-03-01 16:42:14'),
(34, 3, 'action', 'delete', 'Delete operation', 1, 22, '2026-03-01 16:42:14'),
(35, 3, 'action', 'assign_personnel', 'Assign personnel to operation', 1, 23, '2026-03-01 16:42:14'),
(36, 3, 'permission', 'view_operations', 'View operations data', 1, 30, '2026-03-01 16:42:14'),
(37, 3, 'permission', 'manage_operations', 'Full operations management', 1, 31, '2026-03-01 16:42:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `personel`
--

CREATE TABLE `personel` (
  `id` int(11) NOT NULL,
  `nrp` varchar(20) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `pangkat` varchar(50) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `jabatan_asli` varchar(255) DEFAULT NULL,
  `unit` varchar(100) DEFAULT NULL,
  `kantor` varchar(100) DEFAULT NULL,
  `status_jabatan` varchar(20) DEFAULT NULL,
  `kategori_personil` varchar(20) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `personel`
--

INSERT INTO `personel` (`id`, `nrp`, `nama`, `pangkat`, `jabatan`, `jabatan_asli`, `unit`, `kantor`, `status_jabatan`, `kategori_personil`, `keterangan`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '84031648', 'RINA SRY NIRWANA TARIGAN, S.I.K., M.H.', 'AKBP', 'KAPOLRES', 'KAPOLRES SAMOSIR', NULL, 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(2, '83081648', 'BRISTON AGUS MUNTECARLO, S.T., S.I.K.', 'KOMPOL', 'WAKAPOLRES', 'WAKAPOLRES', NULL, 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(3, '68100259', 'EDUAR, S.H.', 'KOMPOL', 'KABAG OPS', 'KABAG OPS', 'BAG OPS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(4, '82080038', 'PATRI SIHALOHO', 'AIPDA', 'PAUR SUBBAGBINOPS', 'PS. PAUR SUBBAGBINOPS', 'BAG OPS', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(5, '02120141', 'AGUNG NUGRAHA NADAP-DAP', 'BRIPDA', 'BINTARA ADMINISTRASI', 'BA MIN BAG OPS', 'BAG OPS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(6, '03010386', 'ALDI PRANATA GINTING', 'BRIPDA', 'BINTARA ADMINISTRASI', 'BA MIN BAG OPS', 'BAG OPS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(7, '02040489', 'HENDRIKSON SILALAHI', 'BRIPDA', 'BINTARA ADMINISTRASI', 'BA MIN BAG OPS', 'BAG OPS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(8, '02071119', 'TOHONAN SITOHANG', 'BRIPDA', 'BINTARA ADMINISTRASI', 'BA MIN BAG OPS', 'BAG OPS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(9, '03101364', 'GILANG SUTOYO', 'BRIPDA', 'BINTARA ADMINISTRASI', 'BA MIN BAG OPS', 'BAG OPS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(10, '198112262024211002', 'FERNANDO SILALAHI, A.Md.', '-', 'ASN', 'ASN BAG OPS', 'BAG OPS', 'POLRES SAMOSIR', 'DEFINITIF', 'NON_POLRI', 'P3K/ BKO POLDA', 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(11, '76030248', 'HENDRI SIAGIAN, S.H.', 'IPDA', 'SUPPORT', 'KA SPKT', 'SPKT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(12, '87070134', 'DENI MUSTIKA SUKMANA, S.E.', 'IPDA', 'PAMAPTA', 'PAMAPTA 1', 'SPKT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(13, '85081770', 'JAMIL MUNTHE, S.H., M.H.', 'IPDA', 'PAMAPTA', 'PAMAPTA 2', 'SPKT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(14, '87030020', 'BULET MARS SWANTO LBN. BATU, S.H.', 'IPDA', 'PAMAPTA', 'PAMAPTA 3', 'SPKT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(15, '96010872', 'RAMADHAN PUTRA, S.H.', 'BRIPTU', 'BINTARA ADMINISTRASI', 'BAMIN PAMAPTA 2', 'SPKT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(16, '98090415', 'ABEDNEGO TARIGAN', 'BRIPTU', 'BINTARA ADMINISTRASI', 'BAMIN PAMAPTA 3', 'SPKT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(17, '00010166', 'EDY SUSANTO PARDEDE', 'BRIPTU', 'BINTARA ADMINISTRASI', 'BAMIN PAMAPTA 1', 'SPKT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(18, '98010470', 'BOBBY ANGGARA PUTRA SIREGAR', 'BRIPDA', 'BINTARA ADMINISTRASI', 'BAMIN PAMAPTA 1', 'SPKT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(19, '01070820', 'GABRIEL PAULIMA NADEAK', 'BRIPDA', 'SUPPORT', 'OP CALL CENTRE', 'SPKT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(20, '02091526', 'ANDRE OWEN PURBA', 'BRIPDA', 'SUPPORT', 'OP CALL CENTRE', 'SPKT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(21, '04070159', 'EDWARD FERDINAND SIDABUTAR', 'BRIPDA', 'SUPPORT', 'OP CALL CENTRE', 'SPKT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(22, '03060873', 'BIMA SANTO HUTAGAOL', 'BRIPDA', 'SUPPORT', 'OP CALL CENTRE', 'SPKT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(23, '03121291', 'KRISTIAN M. H. NABABAN', 'BRIPDA', 'SUPPORT', 'OP CALL CENTRE', 'SPKT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(24, '72100484', 'SURUNG SAGALA', 'IPDA', 'PAUR', 'PAURSUBBAGPROGAR', 'BAG REN', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(25, '96090857', 'ZAKHARIA S. I. SIMANJUNTAK, S.H.  ', 'BRIPTU', 'BINTARA ADMINISTRASI', 'BA MIN BAG REN', 'BAG REN', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(26, '03080202', 'GRENIEL WIARTO SIHITE', 'BRIPDA', 'BINTARA ADMINISTRASI', 'BA MIN BAG REN', 'BAG REN', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(27, '73010107', 'TARMIZI LUBIS, S.H.', 'AKP', 'KABAG SDM', 'PS. KABAG SDM', 'BAG SDM', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(28, '`198111252014122004', 'REYMESTA AMBARITA, S.Kom.', 'PENDA', 'PAUR', 'PAURSUBBAGBINKAR', 'BAG SDM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(29, '97090248', 'LAMTIO SINAGA, S.H.', 'BRIGPOL', 'BINTARA ADMINISTRASI', 'BA MIN BAG SDM', 'BAG SDM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(30, '97120490', 'DODI KURNIADI', 'BRIPTU', 'BINTARA ADMINISTRASI', 'BA MIN BAG SDM', 'BAG SDM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(31, '05070285', 'EFRANTA SAPUTRA SITEPU', 'BRIPDA', 'BINTARA ADMINISTRASI', 'BA MIN BAG SDM', 'BAG SDM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(32, '86070985', 'RADOS. S. TOGATOROP,S.H.', 'AIPDA', 'BA POLRES SAMOSIR', 'BA POLRES SAMOSIR', 'BAG SDM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', 'DIK SIP', 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(33, '00080579', 'REYSON YOHANNES SIMBOLON', 'BRIPDA', 'ADC KAPOLRES', 'ADC KAPOLRES', 'BAG SDM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(34, '03081525', 'YOLANDA NAULIVIA ARITONANG', 'BRIPDA', 'ADC KAPOLRES', 'ADC KAPOLRES', 'BAG SDM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(35, '95080918', 'SYAUQI LUTFI LUBIS, S.H., M.H.', 'BRIGPOL', 'BA POLRES SAMOSIR', 'BA POLRES SAMOSIR', 'BKO', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', 'POLDA SUMUT', 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(36, '97050575', 'DANIEL BRANDO SIDABUKKE', 'BRIGPOL', 'BA POLRES SAMOSIR', 'BA POLRES SAMOSIR', 'BKO', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', 'POLDA SUMUT', 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(37, '98010119', 'SUTRISNO BUTAR-BUTAR, S.H.', 'BRIPTU', 'BA POLRES SAMOSIR', 'BA POLRES SAMOSIR', 'BKO', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', 'ADC BUPATI', 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(38, '81110363', 'LEONARDO SINAGA', 'AIPDA', 'BA POLRES SAMOSIR', 'BA POLRES SAMOSIR', 'PERS MUTASI', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', 'BELUM MENGHADAP', 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(39, '76040221', 'AWALUDDIN', 'IPDA', 'KASUBBAGBEKPAL', 'Plt. KASUBBAGBEKPAL', 'BAG LOG', 'POLRES SAMOSIR', 'PLT', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(40, '97050588', 'EFRON SARWEDY SINAGA, S.H.', 'BRIPTU', 'BINTARA ADMINISTRASI', 'BA MIN BAG LOG', 'BAG LOG', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(41, '00010095', 'PRIADI MAROJAHAN HUTABARAT', 'BRIPTU', 'BINTARA ADMINISTRASI', 'BA MIN BAG LOG', 'BAG LOG', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(42, '03070263', 'CHRIST JERICHO SAPUTRA TAMPUBOLON ', 'BRIPDA', 'BINTARA ADMINISTRASI', 'BA MIN BAG LOG', 'BAG LOG', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(43, '86100287', 'EFRI PANDI', 'AIPDA', 'KASIUM', 'PS. KASIUM', 'SIUM', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(44, '04010804', 'YOGI ADE PRATAMA SITOHANG', 'BRIPDA', 'BINTARA FUNGSIONAL', 'BINTARA SIUM', 'SIUM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(45, '93100676', 'PENGEJAPEN, S.H.', 'BRIGPOL', 'KASIKEU', 'PS. KASIKEU', 'SIKEU', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(46, '97050876', 'MUHARRAM SYAHRI, S.H.', 'BRIPTU', 'BINTARA FUNGSIONAL', 'BINTARA SIKEU', 'SIKEU', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(47, '97100685', 'M.FATHUR RAHMAN, S.H.', 'BRIPTU', 'BINTARA FUNGSIONAL', 'BINTARA SIKEU', 'SIKEU', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(48, '03070010', 'HESKIEL WANDANA MELIALA', 'BRIPDA', 'BINTARA FUNGSIONAL', 'BINTARA SIKEU', 'SIKEU', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(49, '03040138', 'DANIEL RICARDO SARAGIH', 'BRIPDA', 'BINTARA FUNGSIONAL', 'BINTARA SIKEU', 'SIKEU', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(50, '197008291993032002', 'NENENG GUSNIARTI', 'PENATA', 'KASIDOKKES', 'KASIDOKKES', 'SIDOKKES', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(51, '84040532', 'EDDY SURANTA SARAGIH', 'BRIPKA', 'BA SIDOKKES', 'BA SIDOKKES', 'SIDOKKES', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(52, '75060617', 'BILMAR SITUMORANG', 'AIPTU', 'KASIWAS', 'Plt. KASIWAS', 'SIWAS', 'POLRES SAMOSIR', 'PLT', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(53, '94080815', 'YOHANES EDI SUPRIATNO, S.H., M.H.', 'BRIGPOL', 'BINTARA FUNGSIONAL', 'BINTARA SIWAS', 'SIWAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(54, '94080892', 'AGUSTIAWAN SINAGA', 'BRIGPOL', 'BINTARA FUNGSIONAL', 'BINTARA SIWAS', 'SIWAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(55, '93060444', 'LISTER BROUN SITORUS', 'BRIGPOL', 'BINTARA FUNGSIONAL', 'BINTARA SITIK', 'SITIK', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(56, '00070791', 'ANDREAS D. S. SITANGGANG', 'BRIPDA', 'BINTARA FUNGSIONAL', 'BINTARA SITIK', 'SITIK', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(57, '01101139', 'JACKSON SIDABUTAR', 'BRIPDA', 'BINTARA FUNGSIONAL', 'BINTARA SITIK', 'SITIK', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(58, '73050261', 'PARIMPUNAN SIREGAR', 'IPDA', 'KASUBSIBANKUM', 'KASUBSIBANKUM', 'SIKUM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(59, '95030599', 'DANIEL E. LUMBANTORUAN, S.H.', 'BRIGPOL', 'BINTARA FUNGSIONAL', 'BINTARA SIKUM', 'SIKUM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(60, '76120670', 'DENNI BOYKE H. SIREGAR, S.H.', 'IPDA', 'KASIPROPAM', 'PS. KASIPROPAM', 'SIPROPAM', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(61, '81010202', 'BENNI ARDINAL, S.H., M.H.', 'AIPDA', 'PS. KANIT PROPOS', 'PS. KANIT PROPOS', 'SIPROPAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(62, '85081088', 'AGUSTINUS SINAGA', 'AIPDA', 'PS. KANIT PAMINAL', 'PS. KANIT PAMINAL', 'SIPROPAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(63, '86081359', 'RAMBO CISLER NADEAK', 'BRIPKA', 'BINTARA FUNGSIONAL', 'BINTARA SIPROPAM', 'SIPROPAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(64, '95030796', 'PERY RAPEN YONES PARDOSI, S.H.', 'BRIGPOL', 'BINTARA FUNGSIONAL', 'BINTARA SIPROPAM', 'SIPROPAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(65, '97070014', 'DWI HETRIANDY, S.H. ', 'BRIGPOL', 'BINTARA FUNGSIONAL', 'BINTARA SIPROPAM', 'SIPROPAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(66, '97120554', 'TRY WIBOWO', 'BRIPTU', 'BINTARA FUNGSIONAL', 'BINTARA SIPROPAM', 'SIPROPAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(67, '00080343', 'SIMON TIGRIS SIAGIAN', 'BRIPTU', 'BINTARA FUNGSIONAL', 'BINTARA SIPROPAM', 'SIPROPAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(68, '01080575', 'FIRIAN JOSUA SITORUS', 'BRIPDA', 'BINTARA FUNGSIONAL', 'BINTARA SIPROPAM', 'SIPROPAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(69, '87030647', 'DION MAR\'YANSEN SILITONGA', 'BRIGPOL', 'BA PEMBINAAN', 'BA PEMBINAAN', 'SIPROPAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(70, '89080105', 'CLAUDIUS HARIS PARDEDE', 'BRIGPOL', 'BA PEMBINAAN', 'BA PEMBINAAN', 'SIPROPAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(71, '70010290', 'RADIAMAN SIMARMATA', 'AKP', 'KASIHUMAS', 'KASIHUMAS', 'SIHUMAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(72, '93030551', 'GUNAWAN SITUMORANG', 'BRIGPOL', 'BINTARA FUNGSIONAL', 'BINTARA SIHUMAS', 'SIHUMAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(73, '98091488', 'DANIEL BAHTERA SINAGA', 'BRIPTU', 'BINTARA FUNGSIONAL', 'BINTARA SIHUMAS', 'SIHUMAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(74, '75120560', 'HORAS LARIUS SITUMORANG', 'IPDA', 'KAUR', 'KAURBINOPS', 'SAT BINMAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(75, '95090650', 'JEFTA OCTAVIANUS NICO SIANTURI', 'BRIGPOL', 'BINTARA SAT BINMAS', 'BINTARA SAT BINMAS', 'SAT BINMAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(76, '94091146', 'SAHAT MARULI TUA SINAGA, S.H.', 'BRIGPOL', 'BINTARA SAT BINMAS', 'BINTARA SAT BINMAS', 'SAT BINMAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(77, '04020118', 'RONAL PARTOGI SITUMORANG', 'BRIPDA', 'BINTARA SAT BINMAS', 'BINTARA SAT BINMAS', 'SAT BINMAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(78, '82070670', 'DONAL P. SITANGGANG, S.H., M.H.', 'IPTU', 'KASAT INTELKAM', 'PS. KASAT INTELKAM', 'SAT INTELKAM', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(79, '85050489', 'MUHAMMAD YUNUS LUBIS, S.H.', 'IPDA', 'KAUR', 'KAURBINOPS', 'SAT INTELKAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(80, '80070348', 'MARBETA S. SIANIPAR, S.H.', 'AIPDA', 'KAURMINTU', 'PS. KAURMINTU', 'SAT INTELKAM', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(81, '87080112', 'SITARDA AKABRI SIBUEA', 'AIPDA', 'KANIT', 'PS. KANIT 3', 'SAT INTELKAM', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(82, '87051430', 'CINTER ROKHY SINAGA', 'BRIPKA', 'KANIT', 'PS. KANIT 1', 'SAT INTELKAM', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(83, '90080088', 'VANDU P. MARPAUNG', 'BRIPKA', 'KANIT', 'PS. KANIT 2', 'SAT INTELKAM', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(84, '93080556', 'ALFONSIUS GULTOM, S.H. ', 'BRIGPOL', 'BINTARA SAT INTELKAM', 'BINTARA SAT INTELKAM', 'SAT INTELKAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(85, '97040848', 'TRIFIKO P. NAINGGOLAN, S.H.', 'BRIPTU', 'BINTARA SATUAN', 'BINTARA SATINTELKAM', 'SAT INTELKAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(86, '98110618', 'ANDRI AFRIJAL SIMARMATA', 'BRIPTU', 'BINTARA SATUAN', 'BINTARA SATINTELKAM', 'SAT INTELKAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(87, '02030032', 'DIEN VAROSCY I. SITUMORANG', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATINTELKAM', 'SAT INTELKAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(88, '02120339', 'ARDY TRIANO MALAU', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATINTELKAM', 'SAT INTELKAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(89, '02040459', 'JUNEDI SAGALA', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATINTELKAM', 'SAT INTELKAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(90, '02101010', 'GABRIEL SEBASTIAN SIREGAR', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATINTELKAM', 'SAT INTELKAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(91, '04020209', 'RIO F. T ERENST PANJAITAN', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATINTELKAM', 'SAT INTELKAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(92, '04080118', 'AGHEO HARMANA JOUSTRA SINURAYA', 'BRIPDA', 'BINTARA SAT INTELKAM', 'BINTARA SAT INTELKAM', 'SAT INTELKAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(93, '04010801', 'SAMUEL RINALDI PAKPAHAN', 'BRIPDA', 'BINTARA SAT INTELKAM', 'BINTARA SAT INTELKAM', 'SAT INTELKAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(94, '04040520', 'RAYMONTIUS HAROMUNTE', 'BRIPDA', 'BINTARA SAT INTELKAM', 'BINTARA SAT INTELKAM', 'SAT INTELKAM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(95, '79120994', 'EDWARD SIDAURUK, S.E., M.M.', 'AKP', 'KASAT RESKRIM', 'KASAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(96, '76020196', 'DARMONO SAMOSIR, S.H. ', 'IPDA', 'KANIT', 'KANITIDIK 3', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(97, '83010825', 'ROYANTO PURBA, S.H.', 'IPDA', 'KANIT', 'KANITIDIK 4', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(98, '83120602', 'SUHADIYANTO, S.H.', 'IPDA', 'KANIT', 'KANITIDIK 1', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(99, '88060535', 'KUICAN SIMANJUNTAK', 'BRIPKA', 'KANIT', 'KANITIDIK 5', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(100, '79030434', 'MARTIN HABENSONY ARITONANG', 'AIPTU', 'KANITIDIK', 'PS. KANITIDIK 2', 'SAT RESKRIM', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(101, '83060084', 'HENRY SIPAKKAR', 'AIPTU', 'KANIT IDENTIFIKASI', 'PS. KANIT IDENTIFIKASI', 'SAT RESKRIM', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(102, '87011165', 'CHANDRA HUTAPEA', 'BRIPKA', 'KAURMINTU', 'PS. KAURMINTU', 'SAT RESKRIM', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(103, '89030401', 'CHANDRA BARIMBING', 'BRIPKA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(104, '87041596', 'DEDY SAOLOAN SIGALINGGING', 'BRIPKA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(105, '82050798', 'ISWAN LUKITO', 'BRIPKA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(106, '95030238', 'RONI HANSVERI BANJARNAHOR', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(107, '94020506', 'RODEN SUANDI TURNIP', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(108, '94121145', 'SAPUTRA, S.H.', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(109, '95100554', 'DIAN LESTARI GULTOM, S.H.', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(110, '95110886', 'ARGIO SIMBOLON', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(111, '97070616', 'EKO DAHANA PARDEDE, S.H.', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(112, '97040728', 'GIDEON AFRIADI LUMBAN RAJA', 'BRIPTU', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(113, '98090397', 'FACHRUL REZA SILALAHI', 'BRIPTU', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(114, '00030346', 'RIDHOTUA F. SITANGGANG', 'BRIPTU', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(115, '00110362', 'NICHO FERNANDO SARAGIH', 'BRIPTU', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(116, '00090499', 'ADI P.S. MARBUN', 'BRIPTU', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(117, '01120358', 'PRIYATAMA ABDILLAH HARAHAP', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(118, '01070839', 'RIZKI AFRIZAL SIMANJUNTAK', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(119, '01060553', 'MIDUK YUDIANTO SINAGA', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(120, '02110342', 'FRAN\'S ALEXANDER SIANIPAR ', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(121, '01110817', 'RAFFLES SIJABAT', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(122, '01091201', 'HERIANTA TARIGAN', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(123, '03030809', 'RICKY AGATHA GINTING', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(124, '03020368', 'CHRISTIAN PROSPEROUS SIMANUNGKALIT', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(125, '04020196', 'PINIEL RAJAGUKGUK', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(126, '03090568', 'REZA SIREGAR', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(127, '04060050', 'ANDRE YEHEZKIEL HUTABARAT', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(128, '04031206', 'RAYMOND VAN HEZEKIEL SIAHAAN', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(129, '05080602', 'M. ALAMSYAH PRAYOGA TAMBUNAN', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(130, '04090567', 'IRVAN SYAPUTRA MALAU', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT RESKRIM', 'SAT RESKRIM', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(131, '79060034', 'FERRY ARIANDY, S.H., M.H', 'AKP', 'KASATRESNARKOBA', 'KASATRESNARKOBA', 'SAT RESNARKOBA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(132, '88100591', 'ALVIUS KRISTIAN GINTING, S.H.', 'IPDA', 'KAUR', 'KAURBINOPS', 'SAT RESNARKOBA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(133, '89010155', 'BENNY SITUMORANG, S.H. ', 'BRIPKA', 'PS.KANIT IDIK 1', 'PS.KANIT IDIK 1', 'SAT RESNARKOBA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(134, '93050797', 'EKO PUTRA DAMANIK, S.H.', 'BRIGPOL', 'BINTARA SATRESNARKOBA', 'BINTARA SATRESNARKOBA', 'SAT RESNARKOBA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(135, '91050361', 'MAY FRANSISCO SIAGIAN, S.H.', 'BRIGPOL', 'BINTARA SATRESNARKOBA', 'BINTARA SATRESNARKOBA', 'SAT RESNARKOBA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(136, '94090839', 'ROBERTO MANALU', 'BRIPTU', 'BINTARA SATRESNARKOBA', 'BINTARA SATRESNARKOBA', 'SAT RESNARKOBA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(137, '98110378', 'M. RONALD FAHROZI HARAHAP, S.H.', 'BRIPTU', 'BINTARA SATRESNARKOBA', 'BINTARA SATRESNARKOBA', 'SAT RESNARKOBA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(138, '97020694', 'HERIANTO EFENDI, S.H.', 'BRIPTU', 'BINTARA SATRESNARKOBA', 'BINTARA SATRESNARKOBA', 'SAT RESNARKOBA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(139, '02120224', 'TEDDI PARNASIPAN TOGATOROP', 'BRIPDA', 'BINTARA SATRESNARKOBA', 'BINTARA SATRESNARKOBA', 'SAT RESNARKOBA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(140, '02090838', 'ONDIHON SIMBOLON', 'BRIPDA', 'BINTARA SATRESNARKOBA', 'BINTARA SATRESNARKOBA', 'SAT RESNARKOBA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(141, '05080131', 'IVAN SIGOP SIHOMBING', 'BRIPDA', 'BINTARA SATRESNARKOBA', 'BINTARA SATRESNARKOBA', 'SAT RESNARKOBA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(142, '80080676', 'NANDI BUTAR-BUTAR, S.H.', 'AKP', 'KASAT SAMAPTA', 'KASAT SAMAPTA', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(143, '80050867', 'BARTO ANTONIUS SIMALANGO', 'AIPTU', 'KAURBINOPS', 'PS. KAURBINOPS', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(144, '73040390', 'HASUDUNGAN SILITONGA', 'AIPDA', 'PS. KANIT DALMAS 2', 'PS. KANIT DALMAS 2', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(145, '85090954', 'JHONNY LEONARDO SILALAHI', 'BRIPKA', 'KANIT TURJAWALI', 'PS. KANIT TURJAWALI', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(146, '83081051', 'ASRIL', 'BRIPKA', 'BINTARA SATUAN', 'BINTARA SAT SAMAPTA', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(147, '94110350', 'INDIRWAN FRIDERICK, S.H. ', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT SAMAPTA', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(148, '93100793', 'EGIDIUM BRAUN SILITONGA', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT SAMAPTA', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(149, '97100701', 'DINAMIKA JAYA NEGARA SITANGGANG', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT SAMAPTA', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(150, '02051553', 'ZULKIFLI NASUTION', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT SAMAPTA', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(151, '05051087', 'WIRA HARZITA', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT SAMAPTA', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(152, '06100189', 'RAHMAT ANDRIAN TAMBUNAN', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT SAMAPTA', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(153, '07080045', 'JONATAN DWI SAPUTRA PARAPAT', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT SAMAPTA', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(154, '04051595', 'PERDANA NIKOLA SEMBIRING', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT SAMAPTA', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(155, '04081205', 'PETRUS SURIA HUGALUNG', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT SAMAPTA', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(156, '06010414', 'RAFAEL ARSANLILO SINULINGGA', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT SAMAPTA', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(157, '06090021', 'RAJASPER SIRINGORINGO', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT SAMAPTA', 'SAT SAMAPTA', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(158, '72100604', 'TANGIO HAOJAHAN SITANGGANG, S.H.', 'IPTU', 'KASAT PAMOBVIT', 'KASAT PAMOBVIT', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(159, '80100836', 'MARUBA NAINGGOLAN', 'AIPTU', 'PS. KANITPAMWASTER', 'PS. KANITPAMWASTER', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(160, '85030645', 'ROY HARIS ST. SIMAREMARE', 'AIPDA', 'PS. KANITPAMWISATA', 'PS. KANITPAMWISATA', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(161, '80050898', 'M. DENY WAHYU', 'AIPDA', 'PANIT PAMWASTER', 'PS. PANIT PAMWASTER', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(162, '83050202', 'HENRI F. SIANIPAR', 'AIPTU', 'BINTARA SATUAN', 'BINTARA SAT PAMOBVIT', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(163, '85121325', 'BUYUNG ANDRYANTO', 'BRIPKA', 'KAURMINTU', 'PS. KAURMINTU', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(164, '91110130', 'RIANTO SITANGGANG', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT PAMOBVIT', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(165, '94090948', 'ROY NANDA SEMBIRING KEMBAREN', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT PAMOBVIT', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(166, '96031057', 'CANDRA SILALAHI, S.H.', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT PAMOBVIT', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(167, '01060884', 'HORAS J.M. ARITONANG ', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT PAMOBVIT', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(168, '02100599', 'YUNUS SAMDIO SIDABUTAR ', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT PAMOBVIT', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(169, '03010565', 'RAINHEART SITANGGANG ', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT PAMOBVIT', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(170, '02011312', 'BONIFASIUS NAINGGOLAN', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT PAMOBVIT', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(171, '00080816', 'RAY YONDO SIAHAAN ', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT PAMOBVIT', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(172, '03040947', 'REDY EZRA JONATHAN', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT PAMOBVIT', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(173, '04100485', 'CHARLY H. ARITONANG', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT PAMOBVIT', 'SATPAMOBVIT', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(174, '79120800', 'NATANAIL SURBAKTI, S.H', 'AKP', 'KASAT LANTAS', 'KASAT LANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(175, '75080942', 'JUSUF KETAREN', 'IPDA', 'KANIT', 'KANITREGIDENT LANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(176, '80070492', 'ARON PERANGIN-ANGIN', 'AIPTU', 'PS. KANITGAKKUM', 'PS. KANITGAKKUM', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(177, '79060704', 'HERON GINTING', 'BRIPKA', 'PS. KANITTURJAWALI', 'PS. KANITTURJAWALI', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(178, '86030733', 'JEFRI KHADAFI SIREGAR, S.H.', 'BRIPKA', 'PS. KANITKAMSEL', 'PS. KANITKAMSEL', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(179, '89070031', 'HERIANTO TURNIP', 'BRIPKA', 'BINTARA SATUAN', 'BINTARA SAT LANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(180, '93020749', 'ROY GRIMSLAY, S.H.', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT LANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(181, '93090673', 'BAGUS DWI PRAKOSO, S.H.', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT LANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(182, '97040353', 'ICASANDRI MONANZA BR GINTING', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SAT LANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(183, '95021078', 'DIKI FEBRIAN SITORUS', 'BRIPTU', 'BINTARA SATUAN', 'BINTARA SAT LANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(184, '96031061', 'MARCHLANDA SITOHANG', 'BRIPTU', 'BINTARA SATUAN', 'BINTARA SAT LANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(185, '01080438', 'JULIVER SIDABUTAR', 'BRIPTU', 'BINTARA SATUAN', 'BINTARA SAT LANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(186, '01120281', 'FATHURROZI TINDAON', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT LANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(187, '02090891', 'ANDRE TARUNA SIMBOLON', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATLANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(188, '02111012', 'BENY BOY CHRISTIAN SIAHAAN', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATLANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(189, '02111051', 'RADOT NOVALDO PANDAPOTAN PURBA', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATLANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(190, '96061331', 'DIDI HOT BAGAS SITORUS', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATLANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(191, '05030251', 'MUHAMMAD ZIDHAN RIFALDI', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATLANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(192, '04050615', 'DANI INDRA PERMANA SINAGA', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATLANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(193, '05010048', 'HEZKIEL CAPRI SITINDAON', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATLANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(194, '04030824', 'BONARIS TSUYOKO DITASANI SINAGA', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATLANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(195, '05010014', 'ARY ANJAS SARAGIH', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATLANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(196, '04030805', 'GABRIEL VERY JUNIOR SITOHANG', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATLANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(197, '02121477', 'FIRMAN BAHTERA', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SATLANTAS', 'SAT LANTAS', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(198, '68120522', 'SULAIMAN PANGARIBUAN, S.H', 'AKP', 'KASAT POLAIRUD', 'KASAT POLAIRUD', 'SATPOLAIRUD', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(199, '83080822', 'EFENDI M.  SIREGAR', 'AIPDA', 'PS. KANITPATROLI', 'PS. KANITPATROLI', 'SATPOLAIRUD', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(200, '73120275', 'ROMEL LINDUNG SIAHAAN', 'AIPDA', 'KAURMINTU', 'PS. KAURMINTU', 'SATPOLAIRUD', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(201, '90060273', 'FRANS HOTMAN MANURUNG, S.H.', 'BRIPKA', 'BINTARA SATUAN', 'BINTARA SATPOLAIRUD', 'SATPOLAIRUD', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(202, '77070919', 'ANTONIUS SIPAYUNG', 'BRIGPOL', 'BINTARA SATUAN', 'BINTARA SATPOLAIRUD', 'SATPOLAIRUD', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(203, '82051018', 'SAUT H. SIAHAAN', 'AIPDA', 'KASAT TAHTI', 'PS. KASAT TAHTI', 'SAT TAHTI', 'POLRES SAMOSIR', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(204, '98050496', 'FERNANDO SIMBOLON', 'BRIPTU', 'BINTARA SATUAN', 'BINTARA SAT TAHTI', 'SAT TAHTI', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(205, '98030531', 'KURNIA PERMANA', 'BRIPTU', 'BINTARA SATUAN', 'BINTARA SAT TAHTI', 'SAT TAHTI', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(206, '05090232', 'STEVEN IMANUEL SITUMEANG', 'BRIPDA', 'BINTARA SATUAN', 'BINTARA SAT TAHTI', 'SAT TAHTI', 'POLRES SAMOSIR', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(207, '69090552', 'RAHMAT KURNIAWAN', 'IPTU', 'KAPOLSEK', 'PS. KAPOLSEK HARIAN BOHO', 'KAPOLSEK', 'POLSEK HARIAN BOHO', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(208, '79090296', 'MARUKKIL J.M. PASARIBU ', 'AIPTU', 'KANIT INTELKAM', 'PS. KANIT INTELKAM', 'UNIT INTELKAM', 'POLSEK HARIAN BOHO', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(209, '82070930', 'LANTRO LANDELINUS SAGALA', 'AIPDA', 'KANIT BINMAS', 'PS. KANIT BINMAS', 'UNIT BINMAS', 'POLSEK HARIAN BOHO', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(210, '87120701', 'ANDY DEDY SIHOMBING, S.H.', 'BRIPKA', 'KANIT RESKRIM', 'PS. KANIT RESKRIM', 'UNIT RESKRIM', 'POLSEK HARIAN BOHO', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(211, '86021428', 'RANGGA HATTA', 'BRIPKA', 'PS.KANIT SAMAPTA', 'PS.KANIT SAMAPTA', 'UNIT SABHARA', 'POLSEK HARIAN BOHO', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(212, '80120573', 'ARDIANSYAH BUTAR-BUTAR', 'BRIPKA', 'BINTARA POLSEK', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK HARIAN BOHO', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(213, '96120123', 'ADRYANTO SINAGA, S.H.', 'BRIGPOL', 'BINTARA POLSEK', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK HARIAN BOHO', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(214, '94040538', 'BROLIN ADFRIALDI HALOHO', 'BRIGPOL', 'BINTARA POLSEK', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK HARIAN BOHO', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(215, '95110806', 'SUGIANTO ERIK SIBORO', 'BRIGPOL', 'BINTARA POLSEK', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK HARIAN BOHO', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(216, '01020739', 'RISKO SIMBOLON', 'BRIPDA', 'BINTARA POLSEK', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK HARIAN BOHO', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(217, '70050412', 'MAXON NAINGGOLAN', 'AKP', 'KAPOLSEK', 'KAPOLSEK PALIPI', 'KAPOLSEK', 'POLSEK PALIPI', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(218, '78040213', 'H. SWANDI SINAGA', 'AIPTU', 'KA SPKT', 'PS. KA SPKT 1', 'SPKT', 'POLSEK PALIPI', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(219, '77030463', 'HARATUA GULTOM', 'AIPTU', 'KASIUM', 'PS. KASIUM', 'SIUM', 'POLSEK PALIPI', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(220, '76120606', 'ASA MELKI HUTABARAT', 'AIPDA', 'KANIT SAMAPTA', 'PS. KANIT SAMAPTA', 'UNIT SABHARA', 'POLSEK PALIPI', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(221, '78100741', 'JARIAHMAN SARAGIH', 'AIPDA', 'KANIT BINMAS', 'PS. KANIT BINMAS', 'UNIT BINMAS', 'POLSEK PALIPI', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(222, '87041134', 'MUHAMMAD SYAFEI RAMADHAN', 'AIPDA', 'KANIT RESKRIM', 'PS. KANIT RESKRIM', 'UNIT RESKRIM', 'POLSEK PALIPI', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(223, '86121371', 'RIJALUL FIKRI SINAGA', 'BRIPKA', 'KANIT INTELKAM', 'PS. KANIT INTELKAM', 'UNIT INTELKAM', 'POLSEK PALIPI', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(224, '85071450', 'TEGUH SYAHPUTRA', 'BRIPKA', 'KA SPKT', 'PS. KA SPKT 2', 'SPKT', 'POLSEK PALIPI', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(225, '85041500', 'RUDYANTO LUMBANRAJA', 'BRIPKA', 'BINTARA POLSEK', 'BINTARA  POLSEK', 'BINTARA  POLSEK', 'POLSEK PALIPI', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(226, '96031075', 'ZULPAN SYAHPUTRA DAMANIK', 'BRIPTU', 'BINTARA POLSEK', 'BINTARA  POLSEK', 'BINTARA  POLSEK', 'POLSEK PALIPI', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(227, '83061022', 'RAMADAN SIREGAR, S.H.', 'IPTU', 'KAPOLSEK', 'PS. KAPOLSEK SIMANINDO', 'KAPOLSEK', 'POLSEK SIMANINDO', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(228, '86071792', 'WIDODO KABAN, S.H.', 'IPDA', 'KANIT', 'KANIT RESKRIM', 'SPKT', 'POLSEK SIMANINDO', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(229, '75120864', 'GUNTAR TAMBUNAN', 'AIPTU', 'KA SPKT', 'PS. KA SPKT 1', 'SPKT', 'POLSEK SIMANINDO', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(230, '82040124', 'JEFRI RICARDO SAMOSIR', 'AIPTU', 'KANIT PROPAM', 'PS. KANIT PROPAM', 'UNIT PROPAM', 'POLSEK SIMANINDO', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(231, '84020306', 'JUITO SUPANOTO PERANGIN-ANGIN', 'AIPDA', 'KANIT BINMAS', 'PS. KANIT BINMAS', 'UNIT BINMAS', 'POLSEK SIMANINDO', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(232, '83080042', 'YOPPHY RHODEAR MUNTHE ', 'AIPDA', 'KA SPKT', 'PS. KA SPKT 3', 'SPKT', 'POLSEK SIMANINDO', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(233, '86010311', 'TUMBUR SITOHANG', 'AIPDA', 'KANIT INTELKAM', 'PS. KANIT INTELKAM', 'UNIT INTELKAM', 'POLSEK SIMANINDO', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(234, '84110202', 'DONI SURIANTO PURBA, S.H.', 'BRIPKA', 'KASIUM', 'PS. KASIUM', 'SIUM', 'POLSEK SIMANINDO', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(235, '89020409', 'PATAR F. ANRI SIAHAAN', 'BRIPKA', 'KANIT SAMAPTA', 'PS. KANIT SAMAPTA', 'UNIT SABHARA', 'POLSEK SIMANINDO', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(236, '94090490', 'KURNIAWAN, S.H.', 'BRIGPOL', 'BINTARA POLSEK', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK SIMANINDO', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(237, '95060432', 'ASHARI BUTAR-BUTAR, S.H.', 'BRIGPOL', 'BINTARA POLSEK', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK SIMANINDO', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(238, '68020268', 'MARLAN SILALAHI', 'KOMPOL', 'KAPOLSEK', 'KAPOLSEK ONANRUNGGU', 'KAPOLSEK', 'POLSEK ONAN RUNGGU', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(239, '82050839', 'HERMAWADI ', 'AIPDA', 'KANIT RESKRIM', 'PS. KANIT RESKRIM', 'UNIT RESKRIM', 'POLSEK ONAN RUNGGU', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(240, '84091124', 'BISSAR LUMBANTUNGKUP', 'AIPDA', 'KANIT BINMAS', 'PS. KANIT BINMAS', 'UNIT BINMAS', 'POLSEK ONAN RUNGGU', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(241, '70090340', 'BONAR JUBEL SIBARANI', 'BRIPKA', 'KANIT SAMAPTA', 'PS. KANIT SAMAPTA', 'UNIT SABHARA', 'POLSEK ONAN RUNGGU', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(242, '77020642', 'RAMLES SITANGGANG', 'BRIPKA', 'KANIT INTELKAM', 'PS. KANIT INTELKAM', 'UNIT INTELKAM', 'POLSEK ONAN RUNGGU', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(243, '83031377', 'LUHUT SIRINGO-RINGO', 'BRIGPOL', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK ONAN RUNGGU', 'POLSEK ONAN RUNGGU', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(244, '03100001', 'ANRIAN SIGALINGGING', 'BRIPDA', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK ONAN RUNGGU', 'POLSEK ONAN RUNGGU', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(245, '99110755', 'BONATUA LUMBANTUNGKUP', 'BRIPDA', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK ONAN RUNGGU', 'POLSEK ONAN RUNGGU', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(246, '03050116', 'ANDRE SUGIARTO MARPAUNG', 'BRIPDA', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK ONAN RUNGGU', 'POLSEK ONAN RUNGGU', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(247, '04030125', 'ERWIN KEVIN GULTOM', 'BRIPDA', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK ONAN RUNGGU', 'POLSEK ONAN RUNGGU', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(248, '70020298', 'BANGUN TUA DALIMUNTHE', 'AKP', 'KAPOLSEK', 'KAPOLSEK PANGURURAN', 'KAPOLSEK', 'POLSEK PANGURURAN', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(249, '81050713', 'LANCASTER ARIANTO CANDY PASARIBU, S.H.', 'AIPTU', 'KANIT RESKRIM', 'PS. KANIT RESKRIM', 'UNIT RESKRIM', 'POLSEK PANGURURAN', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(250, '80090905', 'RUDY SETYAWAN', 'AIPTU', 'KANIT INTELKAM', 'PS. KANIT INTELKAM', 'UNIT INTELKAM', 'POLSEK PANGURURAN', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(251, '80080892', 'MANGATUR TUA TINDAON', 'AIPDA', 'KANIT BINMAS', 'PS. KANIT BINMAS', 'UNIT BINMAS', 'POLSEK PANGURURAN', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46');
INSERT INTO `personel` (`id`, `nrp`, `nama`, `pangkat`, `jabatan`, `jabatan_asli`, `unit`, `kantor`, `status_jabatan`, `kategori_personil`, `keterangan`, `is_active`, `created_at`, `updated_at`) VALUES
(252, '87110154', 'RENO HOTMARULI TUA MANIK, S.H.', 'BRIPKA', 'KANIT SAMAPTA', 'PS. KANIT SAMAPTA', 'UNIT SABHARA', 'POLSEK PANGURURAN', 'PJS', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(253, '79020443', 'HERBINTUPA SITANGGANG ', 'BRIGPOL', 'BINTARA POLSEK', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK PANGURURAN', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(254, '85121751', 'IBRAHIM TARIGAN', 'BRIGPOL', 'BINTARA POLSEK', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK PANGURURAN', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(255, '98090406', 'AGUNG NUGRAHA HARIANJA, S.H. ', 'BRIPTU', 'BINTARA POLSEK', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK PANGURURAN', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(256, '98091274', 'DANI PUTRA RUMAHORBO', 'BRIPTU', 'BINTARA POLSEK', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK PANGURURAN', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46'),
(257, '01060198', 'KRISMAN JULU GULTOM', 'BRIPDA', 'BINTARA POLSEK', 'BINTARA POLSEK', 'BINTARA POLSEK', 'POLSEK PANGURURAN', 'DEFINITIF', 'POLRI', NULL, 1, '2026-03-01 07:11:46', '2026-03-01 07:11:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ranks`
--

CREATE TABLE `ranks` (
  `id` int(11) NOT NULL,
  `kode_pangkat` varchar(10) NOT NULL,
  `nama_pangkat` varchar(100) NOT NULL,
  `kategori` varchar(20) NOT NULL,
  `level` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ranks`
--

INSERT INTO `ranks` (`id`, `kode_pangkat`, `nama_pangkat`, `kategori`, `level`, `created_at`, `updated_at`) VALUES
(1, 'BRIPDA', 'Bintara Dua', 'BINTARA', 1, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(2, 'BRIGPOL', 'Brigadir Polisi', 'BINTARA', 2, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(3, 'BRIPTU', 'Bintara Satu', 'BINTARA', 3, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(4, 'BRIPKA', 'Bintara Tinggi Satu', 'BINTARA', 4, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(5, 'AIPDA', 'Ajun Inspektur Polisi Dua', 'BINTARA', 5, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(6, 'AIPTU', 'Ajun Inspektur Polisi Satu', 'BINTARA', 6, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(7, 'IPDA', 'Inspektur Polisi Dua', 'PERWIRA', 7, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(8, 'IPTU', 'Inspektur Polisi Satu', 'PERWIRA', 8, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(9, 'AKP', 'Ajun Komisaris Polisi', 'PERWIRA', 9, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(10, 'KOMPOL', 'Komisaris Polisi', 'PERWIRA', 10, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(11, 'AKBP', 'Ajun Komisaris Besar Polisi', 'PERWIRA', 11, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(12, 'KOMBES', 'Komisaris Besar Polisi', 'PERWIRA', 12, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(13, 'BRIGJEN', 'Brigadir Jenderal Polisi', 'PERWIRA TINGGI', 13, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(14, 'IRJEN', 'Inspektur Jenderal Polisi', 'PERWIRA TINGGI', 14, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(15, 'KOMJEN', 'Komisaris Jenderal Polisi', 'PERWIRA TINGGI', 15, '2026-03-01 07:11:06', '2026-03-01 07:11:06'),
(16, 'JENDRAL', 'Jenderal Polisi', 'PERWIRA TINGGI', 16, '2026-03-01 07:11:06', '2026-03-01 07:11:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `tanggal_laporan` date DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `operation_id` int(11) DEFAULT NULL,
  `jenis_laporan` varchar(50) DEFAULT NULL,
  `isi_laporan` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) DEFAULT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `submenus`
--

CREATE TABLE `submenus` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `order_index` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `menu_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `submenus`
--

INSERT INTO `submenus` (`id`, `name`, `icon`, `url`, `order_index`, `is_active`, `menu_id`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard Utama', 'fas fa-home', 'dashboard_main', 1, 1, 1, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(2, 'Data Kantor', 'fas fa-building', 'kantor', 1, 1, 2, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(3, 'Data Jabatan', 'fas fa-user-tag', 'jabatan', 2, 1, 2, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(4, 'Data Pangkat', 'fas fa-chevron-up', 'pangkat', 3, 1, 2, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(5, 'Data Personel', 'fas fa-users', 'personel_data', 1, 1, 3, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(6, 'Import Personel', 'fas fa-file-import', 'personel_import', 2, 1, 3, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(7, 'Data Operasi', 'fas fa-cogs', 'operations_data', 1, 1, 4, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(8, 'RENOPS', 'fas fa-clipboard-list', 'renops', 2, 1, 4, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(9, 'POSKO', 'fas fa-map-marker-alt', 'posko', 3, 1, 4, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(10, 'Laporan Harian', 'fas fa-calendar-day', 'daily_report', 1, 1, 5, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(11, 'Laporan Bulanan', 'fas fa-calendar-alt', 'monthly_report', 2, 1, 5, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(12, 'Pengguna', 'fas fa-user-shield', 'users', 1, 1, 6, '2026-03-01 14:33:49', '2026-03-01 14:33:49'),
(13, 'Struktur Organisasi', 'fas fa-sitemap', 'struktur', 2, 1, 6, '2026-03-01 14:33:49', '2026-03-01 14:33:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama`, `email`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@bagops.com', 'super_admin', 1, '2026-03-01 16:03:57', '2026-03-01 14:34:57', '2026-03-01 18:00:19'),
(2, 'user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Biasa', 'user@bagops.com', 'user', 1, '2026-03-01 16:03:57', '2026-03-01 14:34:57', '2026-03-01 18:00:19'),
(3, 'kabag_ops', '$2y$10$SVvfndmM6XfgNSzkckWPb.Ps0pNwO5BQFiIeuPCdO44kv4PIZakXy', 'Kepala Bagian Operasi', 'kabag_ops@bagops.com', 'kabag_ops', 1, '2026-03-01 16:03:57', '2026-03-01 14:34:57', '2026-03-01 17:28:49'),
(4, 'kaur_ops', '$2y$10$eFp.fLPiQ4b3bHNYAk9c2.SHwSeMiuDiB9xD6pbHs.DrOA8h5mgMS', 'Kepala Urusan Operasi', 'kaur_ops@bagops.com', 'admin', 1, '2026-03-01 16:03:57', '2026-03-01 14:34:57', '2026-03-01 17:28:57');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `user_accessible_pages`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `user_accessible_pages` (
`page_key` varchar(100)
,`title` varchar(200)
,`description` text
,`page_type` enum('standard','dashboard','report','settings','profile')
,`layout_type` enum('default','full_width','sidebar','minimal')
,`target_role` enum('all','super_admin','admin','kabag_ops','kaur_ops','user')
,`order_index` int(11)
,`meta_title` varchar(200)
,`custom_css` text
,`custom_js` text
);

-- --------------------------------------------------------

--
-- Struktur untuk view `active_pages_by_role`
--
DROP TABLE IF EXISTS `active_pages_by_role`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_pages_by_role`  AS SELECT `p`.`page_key` AS `page_key`, `p`.`title` AS `title`, `p`.`description` AS `description`, `p`.`target_role` AS `target_role`, `p`.`page_type` AS `page_type`, `p`.`layout_type` AS `layout_type`, `pd`.`meta_title` AS `meta_title`, `pd`.`meta_description` AS `meta_description`, `pd`.`custom_css` AS `custom_css`, `pd`.`custom_js` AS `custom_js`, `p`.`order_index` AS `order_index` FROM (`pages` `p` left join `page_details` `pd` on(`p`.`id` = `pd`.`page_id`)) WHERE `p`.`is_active` = 1 ORDER BY `p`.`target_role` ASC, `p`.`order_index` ASC ;

-- --------------------------------------------------------

--
-- Struktur untuk view `user_accessible_pages`
--
DROP TABLE IF EXISTS `user_accessible_pages`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_accessible_pages`  AS SELECT `p`.`page_key` AS `page_key`, `p`.`title` AS `title`, `p`.`description` AS `description`, `p`.`page_type` AS `page_type`, `p`.`layout_type` AS `layout_type`, `p`.`target_role` AS `target_role`, `p`.`order_index` AS `order_index`, `pd`.`meta_title` AS `meta_title`, `pd`.`custom_css` AS `custom_css`, `pd`.`custom_js` AS `custom_js` FROM (`pages` `p` left join `page_details` `pd` on(`p`.`id` = `pd`.`page_id`)) WHERE `p`.`is_active` = 1 AND (`p`.`target_role` = 'all' OR `p`.`target_role` = 'user') ORDER BY `p`.`order_index` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `access_log`
--
ALTER TABLE `access_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_access_time` (`access_time`),
  ADD KEY `idx_page` (`page`),
  ADD KEY `idx_user_role` (`user_role`),
  ADD KEY `idx_access_result` (`access_result`),
  ADD KEY `idx_access_log_time_result` (`access_time`,`access_result`);

--
-- Indeks untuk tabel `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `dynamic_jabatan`
--
ALTER TABLE `dynamic_jabatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_jabatan` (`kode_jabatan`),
  ADD KEY `idx_nama_jabatan` (`nama_jabatan`),
  ADD KEY `idx_kode_jabatan` (`kode_jabatan`),
  ADD KEY `idx_parent_id` (`parent_id`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indeks untuk tabel `kantor`
--
ALTER TABLE `kantor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kantor` (`nama_kantor`);

--
-- Indeks untuk tabel `master_jabatan_pns`
--
ALTER TABLE `master_jabatan_pns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_jabatan` (`kode_jabatan`);

--
-- Indeks untuk tabel `master_jabatan_polri`
--
ALTER TABLE `master_jabatan_polri`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_jabatan` (`kode_jabatan`);

--
-- Indeks untuk tabel `master_pangkat_pns`
--
ALTER TABLE `master_pangkat_pns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pangkat` (`kode_pangkat`);

--
-- Indeks untuk tabel `master_pangkat_polri`
--
ALTER TABLE `master_pangkat_polri`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pangkat` (`kode_pangkat`);

--
-- Indeks untuk tabel `master_status_jabatan`
--
ALTER TABLE `master_status_jabatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `status_jabatan` (`status_jabatan`);

--
-- Indeks untuk tabel `master_tipe_kantor_polisi`
--
ALTER TABLE `master_tipe_kantor_polisi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipe_kantor` (`tipe_kantor`);

--
-- Indeks untuk tabel `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indeks untuk tabel `m_jabatan`
--
ALTER TABLE `m_jabatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_jabatan` (`kode_jabatan`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indeks untuk tabel `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_key` (`page_key`),
  ADD KEY `parent_page_id` (`parent_page_id`),
  ADD KEY `idx_page_key` (`page_key`),
  ADD KEY `idx_target_role` (`target_role`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_order_index` (`order_index`),
  ADD KEY `idx_pages_active_role_order` (`is_active`,`target_role`,`order_index`);

--
-- Indeks untuk tabel `page_details`
--
ALTER TABLE `page_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_page_id` (`page_id`);

--
-- Indeks untuk tabel `page_permissions`
--
ALTER TABLE `page_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_page_role_permission` (`page_id`,`role_name`,`permission_type`),
  ADD KEY `idx_role_permission` (`role_name`,`permission_type`),
  ADD KEY `idx_page_permissions_role_granted` (`role_name`,`is_granted`);

--
-- Indeks untuk tabel `page_requirements`
--
ALTER TABLE `page_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_page_requirement` (`page_id`,`requirement_type`),
  ADD KEY `idx_requirement_key` (`requirement_key`),
  ADD KEY `idx_page_requirements_page_type` (`page_id`,`requirement_type`);

--
-- Indeks untuk tabel `personel`
--
ALTER TABLE `personel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nrp` (`nrp`);

--
-- Indeks untuk tabel `ranks`
--
ALTER TABLE `ranks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pangkat` (`kode_pangkat`);

--
-- Indeks untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indeks untuk tabel `submenus`
--
ALTER TABLE `submenus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `access_log`
--
ALTER TABLE `access_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dynamic_jabatan`
--
ALTER TABLE `dynamic_jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT untuk tabel `kantor`
--
ALTER TABLE `kantor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `master_jabatan_pns`
--
ALTER TABLE `master_jabatan_pns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT untuk tabel `master_jabatan_polri`
--
ALTER TABLE `master_jabatan_polri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=541;

--
-- AUTO_INCREMENT untuk tabel `master_pangkat_pns`
--
ALTER TABLE `master_pangkat_pns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `master_pangkat_polri`
--
ALTER TABLE `master_pangkat_polri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT untuk tabel `master_status_jabatan`
--
ALTER TABLE `master_status_jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `master_tipe_kantor_polisi`
--
ALTER TABLE `master_tipe_kantor_polisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `m_jabatan`
--
ALTER TABLE `m_jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `page_details`
--
ALTER TABLE `page_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `page_permissions`
--
ALTER TABLE `page_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT untuk tabel `page_requirements`
--
ALTER TABLE `page_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `personel`
--
ALTER TABLE `personel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=258;

--
-- AUTO_INCREMENT untuk tabel `ranks`
--
ALTER TABLE `ranks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `submenus`
--
ALTER TABLE `submenus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menu` (`id`);

--
-- Ketidakleluasaan untuk tabel `m_jabatan`
--
ALTER TABLE `m_jabatan`
  ADD CONSTRAINT `m_jabatan_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `m_jabatan` (`id`);

--
-- Ketidakleluasaan untuk tabel `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`parent_page_id`) REFERENCES `pages` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `page_details`
--
ALTER TABLE `page_details`
  ADD CONSTRAINT `page_details_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `page_permissions`
--
ALTER TABLE `page_permissions`
  ADD CONSTRAINT `page_permissions_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `page_requirements`
--
ALTER TABLE `page_requirements`
  ADD CONSTRAINT `page_requirements_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `submenus`
--
ALTER TABLE `submenus`
  ADD CONSTRAINT `submenus_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
