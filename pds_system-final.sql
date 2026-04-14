-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 14, 2026 at 02:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pds_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `person_id` int(11) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `house` varchar(50) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `subdivision` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `person_id`, `type`, `house`, `street`, `subdivision`, `barangay`, `city`, `province`, `zip`) VALUES
(39, 32, 'Residential', '', '', '', '', '', '', ''),
(40, 32, 'Permanent', '', '', '', '', '', '', ''),
(53, 41, 'residential', '', '', '', '', '', '', ''),
(54, 41, 'permanent', '', '', '', '', '', '', ''),
(55, 42, 'residential', '', '', '', '', '', '', ''),
(56, 42, 'permanent', '', '', '', '', '', '', ''),
(57, 43, 'residential', '', '', '', '', '', '', ''),
(58, 43, 'permanent', '', '', '', '', '', '', ''),
(61, 45, 'residential', '', '', '', '', '', '', ''),
(62, 45, 'permanent', '', '', '', '', '', '', ''),
(63, 46, 'residential', '', '', '', '', '', '', ''),
(64, 46, 'permanent', '', '', '', '', '', '', ''),
(79, 35, 'residential', '', '', '', '', '', '', ''),
(80, 35, 'permanent', '', '', '', '', '', '', ''),
(81, 53, 'residential', '', '', '', '', '', '', ''),
(82, 53, 'permanent', '', '', '', '', '', '', ''),
(83, 54, 'residential', '', '', '', '', '', '', ''),
(84, 54, 'permanent', '', '', '', '', '', '', ''),
(85, 55, 'residential', 'Blk 10 Lot 5', 'Kenneth', 'N/A', 'Pinagbuhatan', 'Pasig', 'Batangas, Lipa', '1602'),
(86, 55, 'permanent', 'Blk 10 Lot 5', 'Kenneth', 'N/A', 'Pinagbuhatan', 'Pasig', 'Batangas, Lipa', '1602'),
(89, 57, 'residential', 'BLK 72 LOT 38', 'DOÑA AURORA', 'N/A', 'RIZAL', 'TAGUIG CITY', 'METRO MANILA', '1208'),
(90, 57, 'permanent', 'BLK 72 LOT 38', 'DOÑA AURORA', 'N/A', 'RIZAL', 'TAGUIG CITY', 'METRO MANILA', '1208'),
(93, 59, 'residential', 'Blk 10 Lot 5', 'Kenneth', 'N/A', 'Pinagbuhatan', 'Pasig', 'Batangas, Lipa', '1602'),
(94, 59, 'permanent', 'Blk 10 Lot 5', 'Kenneth', 'n/a', 'Pinagbuhatan', 'n/a', 'Batangas,Lipa', '1602'),
(123, 74, 'residential', 'BLK 72 LOT 38', 'DONA AURORA', 'N/A', 'RIZAL', 'TAGUIG CITY', 'MANILA', '1208'),
(124, 74, 'permanent', 'BLK 72 LOT 38', 'DONA AURORA', 'N/A', 'RIZAL', 'TAGUIG CITY', 'MANILA', '1208'),
(125, 75, 'residential', 'Blk 5 Road 11', 'Planters Berm West Bank', 'n/a', 'San Andres', 'Cainta', 'Rizal', '1900'),
(126, 75, 'permanent', 'Blk 5 Road 11', 'Planters Berm West Bank', 'n/a', 'San Andres', 'Cainta', 'Rizal', '1900');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `person_id` int(11) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `action` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `person_id`, `username`, `action`, `description`, `created_at`) VALUES
(1, 32, 'admin1', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBOS MARIA', '2026-03-25 05:32:47'),
(2, 32, 'admin1', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBOS MARIA', '2026-03-25 05:32:49'),
(3, 32, 'admin1', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBOS MARIA', '2026-03-25 05:33:04'),
(4, 32, 'admin1', 'UPDATE', 'Updated PDS record for LESLIE DG MANGOBO MARIA', '2026-03-25 05:33:07'),
(5, 32, 'admin1', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-03-25 05:33:07'),
(6, 39, 'admin1', 'CREATE', 'Created PDS record for Yasmin Jade Pilapil', '2026-03-25 06:21:32'),
(7, 40, 'Yasmin', 'CREATE', 'Created PDS record for Yasmin Jade Pilapil', '2026-03-25 06:30:00'),
(8, 37, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-03-25 06:30:07'),
(9, 37, 'Yasmin', 'DELETE', 'Deleted PDS record with ID 37', '2026-03-25 06:30:10'),
(10, 38, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-03-25 06:30:11'),
(11, 38, 'Yasmin', 'DELETE', 'Deleted PDS record with ID 38', '2026-03-25 06:30:14'),
(12, 39, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-03-25 06:30:16'),
(13, 39, 'Yasmin', 'DELETE', 'Deleted PDS record with ID 39', '2026-03-25 06:30:18'),
(14, 43, 'Yasmin', 'CREATE', 'Created PDS record for Mabini  Apolinario', '2026-03-30 02:31:57'),
(15, 44, 'Yasmin', 'CREATE', 'Created PDS record for Jahmell  Zaragoza', '2026-03-30 02:32:40'),
(16, 45, 'Yasmin', 'CREATE', 'Created PDS record for Leslie Mangobos Montejo', '2026-03-30 02:33:26'),
(17, 45, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Leslie Mangobos Montejo', '2026-03-30 02:33:46'),
(18, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-30 08:40:20'),
(19, 32, 'charles', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-03-31 02:24:48'),
(20, 32, 'charles', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-03-31 02:24:51'),
(21, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 02:58:20'),
(22, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 02:59:05'),
(23, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 05:16:39'),
(24, 32, 'charles', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-03-31 05:26:52'),
(25, 32, 'charles', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-03-31 05:26:52'),
(26, 32, 'charles', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-03-31 05:26:52'),
(27, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 05:50:07'),
(28, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:32:58'),
(29, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:40:01'),
(30, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:40:01'),
(31, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:40:18'),
(32, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:40:19'),
(33, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:42:01'),
(34, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:42:02'),
(35, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:43:22'),
(36, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:43:22'),
(37, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:43:22'),
(38, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:43:23'),
(39, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:43:23'),
(40, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:43:23'),
(41, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:43:23'),
(42, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:43:23'),
(43, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:43:24'),
(44, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:43:24'),
(45, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:43:24'),
(46, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-03-31 08:43:24'),
(47, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 01:50:50'),
(48, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 01:50:52'),
(49, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 01:50:53'),
(50, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 01:50:54'),
(51, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 01:50:54'),
(52, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 01:50:54'),
(53, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 01:50:55'),
(54, 42, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 01:50:55'),
(55, 42, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 01:50:55'),
(56, 42, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 01:50:56'),
(57, 42, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 01:50:56'),
(58, 42, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 01:50:57'),
(59, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 02:38:40'),
(60, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 02:39:02'),
(61, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 02:39:06'),
(62, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 02:39:07'),
(63, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 02:39:08'),
(64, 46, 'charles', 'CREATE', 'Created PDS record for ', '2026-04-06 02:41:04'),
(65, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 02:41:26'),
(66, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 02:41:27'),
(67, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 02:41:29'),
(68, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 02:41:29'),
(69, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 02:41:29'),
(70, 47, 'charles', 'CREATE', 'Created PDS record for test  test', '2026-04-06 02:43:55'),
(71, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  test', '2026-04-06 02:44:00'),
(72, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 02:44:30'),
(73, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 02:44:30'),
(74, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 02:44:31'),
(75, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 02:46:52'),
(76, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 02:46:52'),
(77, 45, 'charles', 'OPEN_EDIT', 'Opened edit page for Leslie Mangobos Montejo', '2026-04-06 02:55:02'),
(78, 45, 'charles', 'OPEN_EDIT', 'Opened edit page for Leslie Mangobos Montejo', '2026-04-06 02:56:52'),
(79, 44, 'charles', 'OPEN_EDIT', 'Opened edit page for Jahmell  Zaragoza', '2026-04-06 02:57:00'),
(80, 44, 'charles', 'OPEN_EDIT', 'Opened edit page for Jahmell  Zaragoza', '2026-04-06 02:58:58'),
(81, 44, 'charles', 'UPDATE', 'Updated PDS record for Jahmell  Zaragoza', '2026-04-06 03:00:49'),
(82, 44, 'charles', 'OPEN_EDIT', 'Opened edit page for Jahmell  Zaragoza', '2026-04-06 03:00:49'),
(83, 44, 'charles', 'UPDATE', 'Updated PDS record for Jahmell  Zaragoza', '2026-04-06 03:01:34'),
(84, 44, 'charles', 'OPEN_EDIT', 'Opened edit page for Jahmell  Zaragoza', '2026-04-06 03:01:34'),
(85, 44, 'charles', 'OPEN_EDIT', 'Opened edit page for Jahmell  Zaragoza', '2026-04-06 03:02:41'),
(86, 44, 'charles', 'OPEN_EDIT', 'Opened edit page for Jahmell  Zaragoza', '2026-04-06 03:03:07'),
(87, 44, 'charles', 'OPEN_EDIT', 'Opened edit page for Jahmell  Zaragoza', '2026-04-06 03:03:22'),
(88, 44, 'charles', 'OPEN_EDIT', 'Opened edit page for Jahmell  Zaragoza', '2026-04-06 03:03:34'),
(89, 44, 'charles', 'DELETE', 'Deleted PDS record with ID 44', '2026-04-06 03:03:55'),
(90, 44, 'charles', 'DELETE', 'Deleted PDS record with ID 44', '2026-04-06 03:04:00'),
(91, 48, 'charles', 'CREATE', 'Created PDS record for Jahmell  Zaragoza', '2026-04-06 03:07:03'),
(92, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 03:09:41'),
(93, 48, 'charles', 'OPEN_EDIT', 'Opened edit page for Jahmell  Zaragoza', '2026-04-06 03:09:44'),
(94, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 03:11:52'),
(95, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 03:12:26'),
(96, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 03:52:14'),
(97, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 03:52:47'),
(98, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 03:52:48'),
(99, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 03:52:48'),
(100, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  test', '2026-04-06 03:52:57'),
(101, 47, 'charles', 'UPDATE', 'Updated PDS record for test  tes', '2026-04-06 03:53:02'),
(102, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 03:53:02'),
(103, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 03:53:43'),
(104, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 03:53:51'),
(105, 47, 'charles', 'UPDATE', 'Updated PDS record for test  tes', '2026-04-06 03:56:21'),
(106, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 03:56:22'),
(107, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 05:40:21'),
(108, 49, 'Yasmin', 'CREATE', 'Created PDS record for ', '2026-04-06 05:55:32'),
(109, 49, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 05:55:49'),
(110, 49, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 05:55:51'),
(111, 49, 'Yasmin', 'DELETE', 'Deleted PDS record with ID 49', '2026-04-06 05:57:58'),
(112, 41, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-06 05:58:58'),
(113, 50, 'Yasmin', 'CREATE', 'Created PDS record for ', '2026-04-06 06:33:53'),
(114, 50, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 06:34:13'),
(115, 50, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 06:35:42'),
(116, 51, 'Yasmin', 'CREATE', 'Created PDS record for ', '2026-04-06 06:37:38'),
(117, 51, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 06:37:50'),
(118, 52, 'Yasmin', 'CREATE', 'Created PDS record for ', '2026-04-06 06:40:36'),
(119, 48, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Jahmell  Zaragoza', '2026-04-06 06:41:38'),
(120, 50, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 06:42:31'),
(121, 32, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 06:42:58'),
(122, 32, 'Yasmin', 'UPDATE', 'Updated PDS record for LESLIE DG MANGOBO MARIA', '2026-04-06 06:43:57'),
(123, 32, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 06:43:57'),
(124, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 06:44:08'),
(125, 32, 'Yasmin', 'UPDATE', 'Updated PDS record for LESLIE DG MANGOBO MARIA', '2026-04-06 06:44:55'),
(126, 32, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 06:44:55'),
(127, 32, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 06:45:01'),
(128, 32, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 06:45:16'),
(129, 32, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 06:45:41'),
(130, 32, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 06:46:14'),
(131, 32, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 06:46:22'),
(132, 32, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 06:46:33'),
(133, 32, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 06:49:39'),
(134, 32, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 06:50:00'),
(135, 32, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 06:50:04'),
(136, 48, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Jahmell  Zaragoza', '2026-04-06 06:51:18'),
(137, 48, 'Yasmin', 'DELETE', 'Deleted PDS record with ID 48', '2026-04-06 06:51:37'),
(138, 48, 'Yasmin', 'DELETE', 'Deleted PDS record with ID 48', '2026-04-06 06:51:50'),
(139, 48, 'Yasmin', 'DELETE', 'Deleted PDS record with ID 48', '2026-04-06 06:51:56'),
(140, 48, 'Yasmin', 'DELETE', 'Deleted PDS record with ID 48', '2026-04-06 06:53:27'),
(141, 35, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for asas sasa lesli Jr', '2026-04-06 06:53:36'),
(142, 35, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for asas sasa lesli Jr', '2026-04-06 06:54:27'),
(143, 35, 'Yasmin', 'UPDATE', 'Updated PDS record for asas sasa lesli Jr', '2026-04-06 06:55:11'),
(144, 35, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for asas sasa lesli Jr', '2026-04-06 06:55:11'),
(145, 35, 'Yasmin', 'UPDATE', 'Updated PDS record for asas sasa lesli Jr', '2026-04-06 06:55:27'),
(146, 35, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for asas sasa lesli Jr', '2026-04-06 06:55:27'),
(147, 35, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for asas sasa lesli Jr', '2026-04-06 06:55:46'),
(148, 35, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for asas sasa lesli Jr', '2026-04-06 06:55:50'),
(149, 35, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for asas sasa lesli Jr', '2026-04-06 06:55:53'),
(150, 35, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for asas sasa lesli Jr', '2026-04-06 06:56:05'),
(151, 35, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for asas sasa lesli Jr', '2026-04-06 06:56:36'),
(152, 35, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for asas sasa lesli Jr', '2026-04-06 06:56:47'),
(153, 35, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for asas sasa lesli Jr', '2026-04-06 06:57:11'),
(154, 35, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for asas sasa lesli Jr', '2026-04-06 06:57:47'),
(155, 32, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 06:57:56'),
(156, 46, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 06:58:15'),
(157, 46, 'Yasmin', 'UPDATE', 'Updated PDS record for Dorias  Jahmelll Gregorio', '2026-04-06 06:58:35'),
(158, 46, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Dorias  Jahmelll Gregorio', '2026-04-06 06:58:35'),
(159, 46, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Dorias  Jahmelll Gregorio', '2026-04-06 06:58:39'),
(160, 46, 'Yasmin', 'UPDATE', 'Updated PDS record for Dorias  Jahmelll Gregorio', '2026-04-06 06:59:24'),
(161, 46, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Dorias  Jahmelll Gregorio', '2026-04-06 06:59:24'),
(162, 46, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Dorias  Jahmelll Gregorio', '2026-04-06 06:59:26'),
(163, 46, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Dorias  Jahmelll Gregorio', '2026-04-06 06:59:59'),
(164, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:08:20'),
(165, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:14:57'),
(166, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:14:57'),
(167, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:14:57'),
(168, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:14:57'),
(169, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:16:29'),
(170, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:16:29'),
(171, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:16:30'),
(172, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:16:30'),
(173, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:16:30'),
(174, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:16:30'),
(175, 53, 'Yasmin', 'CREATE', 'Created PDS record for test  Charles', '2026-04-06 07:23:58'),
(176, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:24:01'),
(177, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:24:02'),
(178, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:24:02'),
(179, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:24:02'),
(180, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:24:02'),
(181, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:24:02'),
(182, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:24:31'),
(183, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:24:31'),
(184, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:24:31'),
(185, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:24:32'),
(186, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:24:32'),
(187, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 07:24:32'),
(188, 46, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Dorias  Jahmelll Gregorio', '2026-04-06 07:40:12'),
(189, 54, 'Yasmin', 'CREATE', 'Created PDS record for Jahmell  Dorias', '2026-04-06 07:50:51'),
(190, 54, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Jahmell  Dorias', '2026-04-06 07:51:42'),
(191, 54, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Jahmell  Dorias', '2026-04-06 07:52:37'),
(192, 54, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Jahmell  Dorias', '2026-04-06 07:52:42'),
(193, 54, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Jahmell  Dorias', '2026-04-06 07:52:44'),
(194, 45, 'charles', 'OPEN_EDIT', 'Opened edit page for Leslie Mangobos Montejo', '2026-04-06 07:56:06'),
(195, 50, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-06 07:57:17'),
(196, 32, 'charles', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-06 08:14:12'),
(197, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:24:41'),
(198, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:25:09'),
(199, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:25:10'),
(200, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:27:26'),
(201, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:37:45'),
(202, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:38:14'),
(203, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:39:33'),
(204, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:45:14'),
(205, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:45:39'),
(206, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:47:35'),
(207, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:47:55'),
(208, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:54:49'),
(209, 47, 'charles', 'UPDATE', 'Updated PDS record for test  tes', '2026-04-06 08:55:02'),
(210, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:55:02'),
(211, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-06 08:56:17'),
(212, 55, 'charles', 'CREATE', 'Created PDS record for Wonwoo  Park', '2026-04-06 09:32:40'),
(213, 40, 'charles', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-06 09:33:33'),
(214, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-07 02:06:16'),
(215, 56, 'charles', 'CREATE', 'Created PDS record for N/A N/A N/A N/A', '2026-04-07 02:12:34'),
(216, 47, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-07 02:29:01'),
(217, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-07 02:29:15'),
(218, 47, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-07 02:29:24'),
(219, 47, 'charles', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-07 02:29:25'),
(220, 47, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for test  tes', '2026-04-07 02:29:26'),
(221, 47, 'Yasmin', 'DELETE', 'Deleted PDS record with ID 47', '2026-04-07 02:31:37'),
(222, 47, 'Yasmin', 'DELETE', 'Deleted PDS record with ID 47', '2026-04-07 02:32:21'),
(223, 50, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-07 02:32:37'),
(224, 50, 'Yasmin', 'DELETE', 'Deleted PDS record with ID 50', '2026-04-07 02:32:47'),
(225, 40, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for Yasmin Jade Pilapil', '2026-04-07 02:33:41'),
(226, 40, 'Yasmin', 'DELETE', 'Deleted PDS record with ID 40', '2026-04-07 02:33:56'),
(227, 56, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for N/A N/A N/A N/A', '2026-04-07 02:34:06'),
(228, 56, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for N/A N/A N/A N/A', '2026-04-07 02:34:26'),
(229, 57, 'charles', 'CREATE', 'Created PDS record for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 02:39:52'),
(230, 56, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for N/A N/A N/A N/A', '2026-04-07 02:40:28'),
(231, 57, 'Yasmin', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 02:40:30'),
(232, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 02:41:15'),
(233, 59, 'charles', 'CREATE', 'Created PDS record for Jasper n/a Mangobos n/a', '2026-04-07 03:32:41'),
(234, 59, 'charles', 'OPEN_EDIT', 'Opened edit page for Jasper n/a Mangobos n/a', '2026-04-07 03:34:03'),
(235, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 03:42:36'),
(236, 51, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-07 05:39:12'),
(237, 51, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-07 05:39:14'),
(238, 51, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-07 05:39:56'),
(239, 59, 'charles', 'OPEN_EDIT', 'Opened edit page for Jasper n/a Mangobos n/a', '2026-04-07 05:40:19'),
(240, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 05:41:28'),
(241, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 05:42:29'),
(242, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 05:43:02'),
(243, 57, 'charles', 'UPDATE', 'Updated PDS record for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 05:46:47'),
(244, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 05:46:47'),
(245, 57, 'charles', 'UPDATE', 'Updated PDS record for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 05:46:52'),
(246, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 05:46:52'),
(247, 57, 'charles', 'UPDATE', 'Updated PDS record for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 05:47:02'),
(248, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 05:47:02'),
(249, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 05:47:10'),
(250, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 05:49:17'),
(251, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 05:49:21'),
(252, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 05:49:24'),
(253, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 07:49:31'),
(254, 64, 'charles', 'CREATE', 'Created PDS record for Dinace Ignacio Natividad NA', '2026-04-07 07:55:22'),
(255, 64, 'charles', 'OPEN_EDIT', 'Opened edit page for Dinace Ignacio Natividad NA', '2026-04-07 07:55:44'),
(256, 64, 'charles', 'OPEN_EDIT', 'Opened edit page for Dinace Ignacio Natividad NA', '2026-04-07 07:56:25'),
(257, 64, 'charles', 'OPEN_EDIT', 'Opened edit page for Dinace Ignacio Natividad NA', '2026-04-07 07:56:35'),
(258, 64, 'charles', 'OPEN_EDIT', 'Opened edit page for Dinace Ignacio Natividad NA', '2026-04-07 07:56:56'),
(259, 64, 'charles', 'OPEN_EDIT', 'Opened edit page for Dinace Ignacio Natividad NA', '2026-04-07 07:57:18'),
(260, 64, 'charles', 'DELETE', 'Deleted PDS record with ID 64', '2026-04-07 07:57:23'),
(261, 51, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-07 08:20:15'),
(262, 51, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-07 08:22:35'),
(263, 51, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-07 08:23:16'),
(264, 51, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-07 09:25:11'),
(265, 51, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-07 09:36:26'),
(266, 56, 'charles', 'OPEN_EDIT', 'Opened edit page for N/A N/A N/A N/A', '2026-04-07 09:40:31'),
(267, 56, 'charles', 'DELETE', 'Deleted PDS record with ID 56', '2026-04-07 09:40:35'),
(268, 51, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-07 09:40:39'),
(269, 51, 'charles', 'DELETE', 'Deleted PDS record with ID 51', '2026-04-07 09:40:43'),
(270, 52, 'charles', 'OPEN_EDIT', 'Opened edit page for ', '2026-04-07 09:40:49'),
(271, 52, 'charles', 'DELETE', 'Deleted PDS record with ID 52', '2026-04-07 09:40:53'),
(272, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 09:41:26'),
(273, 57, 'charles', 'UPDATE', 'Updated PDS record for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 09:41:40'),
(274, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 09:41:40'),
(275, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 09:47:04'),
(276, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 09:51:30'),
(277, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 09:54:56'),
(278, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for Dorias  Jahmelll Gregorio', '2026-04-07 10:02:23'),
(279, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 10:06:17'),
(280, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 10:06:54'),
(281, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 10:06:55'),
(282, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 10:06:55'),
(283, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 10:06:55'),
(284, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 10:06:55'),
(285, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 10:06:55'),
(286, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 10:06:55'),
(287, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 10:07:19'),
(288, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 10:07:38'),
(289, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-07 10:07:50'),
(290, 32, 'charles', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-07 10:09:22'),
(291, 32, 'charles', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-07 10:14:59'),
(292, 32, 'charles', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-07 10:15:01'),
(293, 32, 'charles', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-07 10:15:01'),
(294, 32, 'charles', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-07 10:15:01'),
(295, 59, 'charles', 'OPEN_EDIT', 'Opened edit page for Jasper n/a Mangobos n/a', '2026-04-07 10:15:52'),
(296, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 10:18:16'),
(297, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-07 10:19:25'),
(298, 59, 'charles', 'OPEN_EDIT', 'Opened edit page for Jasper n/a Mangobos n/a', '2026-04-07 10:19:37'),
(299, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for Dorias  Jahmelll Gregorio', '2026-04-07 10:19:54'),
(300, 69, 'charles', 'CREATE', 'Created PDS record for xcvjbk rtuyu dxfcghvhbk na', '2026-04-07 10:30:23'),
(301, 69, 'charles', 'OPEN_EDIT', 'Opened edit page for xcvjbk rtuyu dxfcghvhbk na', '2026-04-07 10:30:40'),
(302, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 00:01:40'),
(303, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 00:06:40'),
(304, 54, 'charles', 'OPEN_EDIT', 'Opened edit page for Jahmell  Dorias', '2026-04-08 00:07:37'),
(305, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-08 00:08:46'),
(306, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 00:18:01'),
(307, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 00:19:23'),
(308, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 00:32:19'),
(309, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 01:00:45'),
(310, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 01:22:14'),
(311, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 01:26:41'),
(312, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-08 01:26:50'),
(313, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-08 01:27:12'),
(314, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 01:33:19'),
(315, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-08 02:18:18'),
(316, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-08 02:20:47'),
(317, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-08 02:21:05'),
(318, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-08 02:22:12'),
(319, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-08 02:29:29'),
(320, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-08 02:37:45'),
(321, 53, 'charles', 'OPEN_EDIT', 'Opened edit page for test  Charles', '2026-04-08 02:42:04'),
(322, 53, 'charles', 'OPEN_EDIT', 'Opened edit page for test  Charles', '2026-04-08 02:53:02'),
(323, 53, 'charles', 'OPEN_EDIT', 'Opened edit page for test  Charles', '2026-04-08 02:53:16'),
(324, 53, 'charles', 'OPEN_EDIT', 'Opened edit page for test  Charles', '2026-04-08 02:53:17'),
(325, 53, 'charles', 'OPEN_EDIT', 'Opened edit page for test  Charles', '2026-04-08 02:55:09'),
(326, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-08 02:56:43'),
(327, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 03:22:27'),
(328, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 03:22:48'),
(329, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-08 03:23:23'),
(330, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 04:21:40'),
(331, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 04:22:13'),
(332, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 04:22:31'),
(333, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 04:22:58'),
(334, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 04:28:43'),
(335, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 04:29:01'),
(336, 74, 'charles', 'CREATE', 'Created PDS record for YASMIN JADE ORTALIZA PILAPIL II', '2026-04-08 04:51:34'),
(337, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL II', '2026-04-08 04:51:53'),
(338, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL II', '2026-04-08 04:52:04'),
(339, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL II', '2026-04-08 04:52:56'),
(340, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL II', '2026-04-08 04:53:02'),
(341, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-08 05:00:04'),
(342, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL N/A', '2026-04-08 05:02:14'),
(343, 57, 'charles', 'UPDATE', 'Updated PDS record for YAEL JOSH ORTALIZA PILAPIL', '2026-04-08 05:03:29'),
(344, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL II', '2026-04-08 05:07:21'),
(345, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL II', '2026-04-08 05:07:30'),
(346, 74, 'charles', 'UPDATE', 'Updated PDS record for YASMIN JADE ORTALIZA PILAPIL IIII', '2026-04-08 05:07:47'),
(347, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL IIII', '2026-04-08 05:07:47'),
(348, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL IIII', '2026-04-08 05:08:48'),
(349, 74, 'charles', 'UPDATE', 'Updated PDS record for YASMIN JADE ORTALIZA PILAPIL V', '2026-04-08 05:08:59'),
(350, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL V', '2026-04-08 05:08:59'),
(351, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL V', '2026-04-08 05:09:08'),
(352, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL V', '2026-04-08 05:16:17'),
(353, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for Dorias  Jahmelll Gregorio', '2026-04-08 05:48:50'),
(354, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for Dorias  Jahmelll Gregorio', '2026-04-08 05:49:57'),
(355, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for Dorias  Jahmelll Gregorio', '2026-04-08 05:50:05'),
(356, 32, 'charles', 'OPEN_EDIT', 'Opened edit page for LESLIE DG MANGOBO MARIA', '2026-04-08 06:01:55'),
(357, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 06:02:00'),
(358, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 06:26:25'),
(359, 35, 'charles', 'OPEN_EDIT', 'Opened edit page for asas sasa lesli Jr', '2026-04-08 06:27:13'),
(360, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 07:44:35'),
(361, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 07:47:31'),
(362, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 07:54:03'),
(363, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL V', '2026-04-08 07:54:14'),
(364, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL V', '2026-04-08 07:55:11'),
(365, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 07:56:51'),
(366, 74, 'charles', 'OPEN_EDIT', 'Opened edit page for YASMIN JADE ORTALIZA PILAPIL V', '2026-04-08 07:57:05'),
(367, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 08:01:43'),
(368, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 08:02:13'),
(369, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 08:02:47'),
(370, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 08:44:17'),
(371, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 08:44:57'),
(372, 42, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 08:45:23'),
(373, 46, 'charles', 'OPEN_EDIT', 'Opened edit page for Dorias  Jahmelll Gregorio', '2026-04-08 08:45:34'),
(374, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 08:46:39'),
(375, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 09:33:28'),
(376, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-08 09:35:43'),
(377, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-10 13:50:27'),
(378, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-10 13:51:28'),
(379, 69, 'charles', 'OPEN_EDIT', 'Opened edit page for xcvjbk rtuyu dxfcghvhbk na', '2026-04-10 13:51:33'),
(380, 69, 'charles', 'DELETE', 'Deleted PDS record with ID 69', '2026-04-10 13:51:41'),
(381, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-10 13:51:58'),
(382, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-10 13:53:48'),
(383, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-10 14:00:32'),
(384, 75, 'charles', 'CREATE', 'Created PDS record for Jose Alonso Rizal', '2026-04-13 02:54:46'),
(385, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 02:54:59'),
(386, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 03:05:08'),
(387, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 03:06:54'),
(388, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 03:07:32'),
(389, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 03:11:34'),
(390, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-13 03:16:22'),
(391, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:20:51'),
(392, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:21:59'),
(393, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:24:46'),
(394, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:26:34'),
(395, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:27:06'),
(396, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-13 05:30:12'),
(397, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-13 05:30:26'),
(398, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:31:00'),
(399, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:31:12'),
(400, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:31:19'),
(401, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:34:17'),
(402, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:49:06'),
(403, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:49:20'),
(404, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:55:44'),
(405, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:55:53'),
(406, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:55:53'),
(407, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 05:59:48'),
(408, 59, 'charles', 'OPEN_EDIT', 'Opened edit page for Jasper n/a Mangobos n/a', '2026-04-13 06:00:54'),
(409, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 06:01:12'),
(410, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL', '2026-04-13 06:01:47'),
(411, 57, 'charles', 'OPEN_EDIT', 'Opened edit page for YAEL JOSH ORTALIZA PILAPIL', '2026-04-13 06:12:05'),
(412, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 06:12:12'),
(413, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 06:12:27'),
(414, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 06:13:01'),
(415, 75, 'charles', 'UPDATE', 'Updated PDS record for Jose Alonso Rizal', '2026-04-13 06:13:26'),
(416, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 06:13:27'),
(417, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 06:16:01'),
(418, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 06:20:19'),
(419, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 06:23:46'),
(420, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-13 06:23:57'),
(421, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 06:42:53'),
(422, 41, 'charles', 'OPEN_EDIT', 'Opened edit page for Mabini  Apolinario', '2026-04-13 07:04:35'),
(423, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 08:07:36'),
(424, 75, 'charles', 'UPDATE', 'Updated PDS record for Jose Alonso Rizal', '2026-04-13 08:07:40'),
(425, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 08:07:40'),
(426, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 08:20:44'),
(427, 75, 'charles', 'OPEN_EDIT', 'Opened edit page for Jose Alonso Rizal', '2026-04-13 08:25:38');

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(11) NOT NULL,
  `person_id` int(11) DEFAULT NULL,
  `education_level` varchar(50) DEFAULT NULL,
  `school_name` varchar(200) DEFAULT NULL,
  `course` varchar(200) DEFAULT NULL,
  `edu_from` date DEFAULT NULL,
  `edu_to` date DEFAULT NULL,
  `units` varchar(100) DEFAULT NULL,
  `year_graduated` varchar(10) DEFAULT NULL,
  `honors` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `person_id`, `education_level`, `school_name`, `course`, `edu_from`, `edu_to`, `units`, `year_graduated`, `honors`) VALUES
(24, 46, 'College', 'Rtu', 'BSIT', '2026-04-01', '2026-04-15', 'None', '2027', ''),
(45, 57, 'College', 'RTU', 'BSIT', '2026-04-09', '2026-04-10', '40', '2027', 'WALA'),
(46, 57, 'Secondary', 'JYFKYF', 'JYFUYF', '2026-04-22', '2026-04-30', 'JHHFF', '2021', '2027'),
(48, 74, 'Elementary', 'PEMBO ELEMENTARY SCHOOL', 'N/A', '2026-04-01', '2026-04-30', 'N/A', '2021', 'N/A'),
(51, 75, 'College', 'Rizal Technological University', 'Bachelor of Science in Information Technology', '2026-04-13', '2026-04-17', '84', '2027', 'Latina');

-- --------------------------------------------------------

--
-- Table structure for table `eligibility`
--

CREATE TABLE `eligibility` (
  `id` int(11) NOT NULL,
  `person_id` int(11) DEFAULT NULL,
  `career_service` varchar(200) DEFAULT NULL,
  `rating` varchar(20) DEFAULT NULL,
  `exam_date` varchar(50) DEFAULT NULL,
  `exam_place` varchar(200) DEFAULT NULL,
  `license` varchar(100) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `valid_until` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eligibility`
--

INSERT INTO `eligibility` (`id`, `person_id`, `career_service`, `rating`, `exam_date`, `exam_place`, `license`, `license_number`, `valid_until`) VALUES
(1, 46, '', '', '2026-04-06', '', '', '', '2026-04-15'),
(4, 59, 'CES', '50', '2021-11-20', 'Boni', 'n/a', '9865849', '2028-12-12'),
(7, 74, 'CSC', '100', '2023-03-02', 'MARIKINA', 'DIVER\'S LICENSE', '000000000000', '2027-08-20'),
(9, 75, 'CSC', '100', '2025-12-10', 'Dyan lang', 'Drinking Licence', '099999999', '2026-10-01');

-- --------------------------------------------------------

--
-- Table structure for table `personal_info`
--

CREATE TABLE `personal_info` (
  `id` int(11) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `extension` varchar(10) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `birth_place` varchar(150) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `civil_status` varchar(20) DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `weight` varchar(10) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `umid` varchar(50) DEFAULT NULL,
  `pagibig` varchar(50) DEFAULT NULL,
  `philhealth` varchar(50) DEFAULT NULL,
  `philsys` varchar(50) DEFAULT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `agency_employee` varchar(50) DEFAULT NULL,
  `citizenship` varchar(50) DEFAULT NULL,
  `dual_country` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `photo` longblob DEFAULT NULL,
  `photo_type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personal_info`
--

INSERT INTO `personal_info` (`id`, `surname`, `firstname`, `middlename`, `extension`, `dob`, `birth_place`, `sex`, `civil_status`, `height`, `weight`, `blood_type`, `umid`, `pagibig`, `philhealth`, `philsys`, `tin`, `agency_employee`, `citizenship`, `dual_country`, `telephone`, `mobile`, `email`, `photo`, `photo_type`) VALUES
(32, 'MANGOBO', 'LESLIE', 'DG', 'MARIA', '2026-03-11', 'PASIG CITY', 'Female', 'Married', '168cm', '60kg', 'A+', '0123143', '1312312', '423432', '131231', '3123213', '7894564512', 'Filipino', 'CHINESE', '789456123645', '2133235353', 'yasminpilapil1620@gmail.com', NULL, NULL),
(35, 'lesli', 'asas', 'sasa', 'Jr', '2026-03-23', 'sasas', 'Male', 'Single', '165', '60kg', 'O', '54131231', '1312312', '423432', '131231', '3123213', '7894564512', 'Filipino', 'Philippines', '52456+', '9052120289', 'yasminpilapil1620@gmail.com', NULL, NULL),
(41, 'Apolinario', 'Mabini', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(42, 'Apolinario', 'Mabini', '', '', '2026-03-04', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(43, 'Apolinario', 'Mabini', '', '', '2026-03-04', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(45, 'Montejo', 'Leslie', 'Mangobos', '', '2026-03-12', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(46, 'Jahmelll', 'Dorias', '', 'Gregorio', '2026-04-01', 'dyan lang', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0xffd8ffe000104a46494600010100000100010000ffdb008400090607080706090807080a0a090b0d160f0d0c0c0d1b14151016201d2222201d1f1f2428342c242631271f1f2d3d2d3135373a3a3a232b3f443f384334393a37010a0a0a0d0c0d1a0f0f1a37251f253737373737373737373737373737373737373737373737373737373737373737373737373737373737373737373737373737ffc000110800ef00d303012200021101031101ffc4001c0000020203010100000000000000000000030405060102070008ffc40046100001010406040b0506050403010000000201000304120511212231f0324142510613526162718191a1b1c1142372d1e107158292a2f1243343b2d25363c2f21644e225ffc4001a010002030101000000000000000000000002030104050006ffc40029110002020104010303050100000000000000010211030412213141052251133242143361718191ffda000c03010002110311003f00e7d55f94fe1dfafe5ebd68e433890efe9746db39b9db674e867ff2ddaf1ebc2bd6cd55a320e73e4d60c1c997c23c2ef8d3bf7b01efa99f74ee401bba55b01d0497b6b3831c16fe93714e73e0d966d23e4e6b6d80768ca5cf6ee6daa1ce7a9b0a725dfd9a4af6cdc6f9dc97e22ddad8a28201b3d1f4ab7b0033abbd8956c9916ad2fab412dd2364bfb2cc3b1d2e4f87cf2aca13e175a6423f11278f86519088e1241c219492bc7bc99b5f5ae1dbbda1c9219834d932bf6a2cee1dce1c9c25ec5ad7d7c59876e67bc776f5d98adaebb30f5e76a1b9e12d2af4c64f6411d1e2c895752556ee67cb8494aba8694e065297f98eea31c31a9512ac131ad90e7c9b11f4ec95d96ff669259eef44aa5eaf4ee46dc1273bf2fe115dfadb9abee105311065390ca429a44be2bd95feccd40c6c7000943c63b2e8f185aaddff0025561b1ab41248e8a8ebfc9bc0ee4d3cf3f9eb6af513c2f74f4c616901276fec112224bd8599ee6b3393711005c51116cebcd8d1b85cb4f283e51a0a5cff001fdbadb0aeca429cb2b8d59d6c617241c911b344513b1b651ccac243888afe5c3476bcf9bb9b45493febf4b59a7aee7e574b53682eee5fe7cd59c1a45b42e8eb3ab0e65c6d6d45d48736974a64d5af56fddf56d10b229e1561fb3078bd110e6bde56f63480fa1651be324b28e8e57cfe4c320e2b4252e56ad7e3dbad997a3206d17c355eefdeca996d5e2e95bbf5d98e3956242e4d266a423b7b3a38228a7357d8c07ab3ddbb7ab9aea57d79e6661067dafd5e3e5946d49d49f172446ad69e3f4dcd16411e5256bc615ed775b2cd48ebfdbf0f9361a48dc8ae08746f67775315dbb92f4b9c2c5d6da88e7c3d558e29207486afc5fbb5a2b4a5c51b2fcb9f1c5b6742219ab556d849b93776bd5bc8bff6f36913d8542cccdaec4d9ecadb52392f6d17536c3996af45613aa8ca2c9b3b2c85214c38840b8533de48dbaace6af0b191a723f8a3f667534c5744479fd1a1c9c715ef5eff003fc33d4ca94fe0d8d27a6ee5bf27fc0cf5fc6521a644245b255a76a226ed4c07e650864ea1c4470bd322915888b6ae15e2a88da244bd03f74578b48a6b7c5a468ea123a90398257730dd195514b9abc3cf5b29cab991b318452db144183f7e1b4437b3866c66a1a937f0e7303d22c6ed995c5ba2d0df667ed17a2debc1e50cc9cdcdced6385fb34a300045e913c11d1f5dfcddcc89eaf12e06ac790e2cf6278dfe94bb576be6aea60ccf672d22f3e6adbbe9700687904652bb5cb7975e362d9e0c37bc0281e245d00ccea6bd36352ee5aac60fd6e35e097864f9b3883a8cb84ea21d0bc12d99be7db634841d2d13092940c63f712f4ace7454c3bf7b7508bfb377060430e42f0792f2b451e7454dde35b4045fd9b453a0f74ea6c66949154535f3aee4dcc4b538a40fd39a0303c387e128d20e048769f0955da8982f5b5da8d7ee290722fe11e89090f2adee6e571bc17a45d197b3ba27984c2555436d889ddb9b3c1ca4295e0e463af6875c5b827882445626add8a5b8d5bd8ed77162278233f14cebcf20f912b22f9ddfceeaf1d4d6285275170c2f61e52121bc3ac584709cb66269f467cf0b8f0c8376eb96c17aeb4a49aed578b3e2d2efa12439434b39d6cbbd763a3747bfb71cdadc2a488b7a939f7ccca139bf9cf67534913ada3f8af578d4cb2ba2bdf17cb0622bc90bf177346697f4f82e6a6d1f8ed7473d7831de1907c53736fc137322f84bf0cd2eaddce96ae799b88485de25f5bdfa9bcd8270f6b5d25e7e2d17c551b2d20fd320853a39dcc60dafcbe7a9b5412933d4c514cf5b5829c8d9464e497fc6c6d2a92f5de96f62a24fa77a5d11b2ef57836a43b3a3df9546eb39441bc22e56ca75f632149523ec4e48ae93d2d1dfbedcf3b3afa59263d211f45f1c55a9516f8a2230af4d315d9766df3fa32a4cd6d169937ba5e0908421e389fd203311694b55dab04d795613f7bf7b4797b238944b4466dca98aaea63460baf661906f10e88dab5eab7aaaeeaf5b5ab8114589809443abc5de385689abb7b1abce6a11dc6e45397b43f05381ee9ecaf62c78c9b6bb6bb156ba96c44a937b74aa3e858184fe538bdca2b5bd47c3ba74e445d08cb2a3480b64e4cd29b2f471a82e020a3153a6c1426dd0d92749056f23094db286c2c1a37556f54deadbcc24027b0ce1ecdc6ba12f8851a3e2e83a3a201e8bd867642f0652ba97935d6ad28aacbbd78d29bf9092b39bf0868da6b832f8a91a1dfbf8981b38d778a82262bbd52cf1c1a5782bc39754c18b88e1e2e27fb9accf8a7094f47687948dc5f865443da069e17b0244edc3cbeea5b245d628b5f3d9cd5ee6d3d36572f6bec4ea302dbcf4770219cefb2d12927e2a9a9fc0ae1a388b72ea06967bc5c4e8bb7855221f6e08bccd757c826d7eecc5cb8dc786444500806966a5f068f229c36ae96959da88ab9c5a462c2fcb28fe2ce7bda35e26c8688ed6573634f8294eec41e974487bbd3cd832cfc99ba55e15259cc8b9b59978221fe3821589af99b429403938e96155abf3f168392172499556625af5d9f26f310886b5ba5f953e6de69e402b82921dfce73831125f84735fab6a2ba3ca6d8524097ab7fa67c9ac941337059f39dcda3c97f52f9ef6f295c97a32f8eeec6c8698f60f3ebb3d5818fc681462f150c447c959bb96cb756aeaafb284e0cb8e2201d22e4ead7dad7ba6107eed7e465a2eeede4d498a77b505c24864403f8b39c5819b9a2fb49335f723c6de29936b555877b742e02444f0ce84e69a5d22dac2aaabd582f6a373c76ec9e84b295e2eab6ab7adba3fd9ad1640e5ebd7a328ecea9d56cad6dd4889ab5f3354d4d2c668e1fdc3a0c2683328ad876ec818a01a22d92683665b5ad892b6101a1a06d036dc55bcaedbd530b4758445e5b654e46029305e1b451ca1619e3f651e3d926bcda9a94ec1793370f8c12066f47e26acf0ee03dba8427a1a4e4a61c355bafaaa69d7a853ed67061c724f06fc7fdb5f266425b649879217068e3310e46e95d94aaeab7d2db7d5bb870791efdc90ded1a5c5a6ce728dc860610639f40c19cb371d2174af59dedd9c938a86750c1a22ed07b92a6db8f279cd64a92447c494e7736b36328f07f0f97378f9b34f00af767e2645f149f174bceb46232803d2fc5c9cebd5dcc9bc2bfc91e95896b1df167ab0f3f365deadfb9a3fdb625bab57a3720246b566df9379864255aff93798bfd17441874ff48d7fbb1547f17c58671cab69536c833ec9675f535828a7660473fb31007487fe49536147feac610cf730b1aa7c8953d72872fd33756ab37a57fb3521c00ddd11973875ad4d76e1227ff8e45b2355db2f5b62b54a8d77c6bee88fe9b51195237744ef1aa24a01e491ee84048a51512e48aaa2d49d79d6dd7b81f0c5f76ba23bb35ed1aa6b12d5dcdc8e01d89d24e8439c446dbd595aabdfe0ddbb83ae44201d08727649b3b5b2e1235b4abb64c80b6f2b791186fd5e807ba1e30b644b0eb55dcd46c7b66e6f042f195de5160d0519c33a0209f714f69176456dd762a7df5609cf834753d45fb584d4c5265c57fa2eeb04eab16b5edc5a931f46500ea6938c2e55e4e7dd8dbcecdc7183ed9db65e0e910dc30a022342917130e94c59a9a61dbc751002f5d3d17825a24255f8b71981a3e877b132871825a4333cd1d586753749e0b0388186941fcd36cea15e661cd1847a0e3095593a4ed804e99b4316c54c84d3394a8495cb0d5cb66928e7508044652ddd16e5b4ef0969f88892f648c1867137bb21a926b71d76fcd9b8f139ba431cdc55b3a49438b2efa0cbe2d96e61074af099ede08e2297a48a982ef5b6d54a91acf45f0829f840128b86f6b71a4452564295737c98e5a771f28e8e7b2b341c217fe7e4e0251e26288b456aaa6524d78e16f32b7577eeeff2736f535238230ee290e1852b49ba22e2865301e72154545f1eb56bcc42c86459adb5a1f6d9e7b5b5be88f377b5b534b9ad5a38c3f568eaefb6dd5e0d286257a7d2d1f5c19178573a5ebe8c451688f78237bf57edd9af732c97ce69466ca2af37d559b78ee73cfae6d60abbe8f9d79fa3122bcf91693a5fa91b2c6fc05f9951bcdd4415b4d06288ecfd18619bdd4c444d1da2d16b0675d1b220e7eacc3a12bd2677d4da024fa1fdcccb9023395d0cdd11f5dddec1269763b142591d4508d38e08e8789da9453cd306a0421f141f169637be68dd7a36887ef68a8b70506f3de3931bb52e28a9ab0eea9ab2fb803130940b8a4405e71a4e44de39c142b145545e6d4d55e7c6fc9e9349a7c9086d92a21a8259e98123129a5eb4c77f678b777a15cf1506e87f15ec706e1bc0e123a61d5d111b04b79556e2b66b6ef10053b919392d435cfdc91a5817b58dd4c8d2914f5d39fe1c667ba23ad9e9586f9d0bd02136a4c745abe4e7749b99268ca79fcc45a10eef1fa3447de31c604fdd51c430cec672e2dd56a2896ade54ddf46bdc4705e1b8e18974e85e3f129a57981732b4f8140bd807b0a6eb89121512772a5435a2a59a95ade1d8fb3b2ce5f89c4a269a71ed830749d1d1708fc850844827bab812225a9626af26b05124412fb3c4cc3a423322f72eb468ca4b81e70f4dbd8cfbc657fb310ecd4366a5a916a54596b4a92ca9acfc1ba25d04a30eebdc391400dd675b4ea3e9ede3b19a7792db975f24c51d12f76e6bb56d56d34edfdc651c381039407459a78ee47252725a88d9b4d941e16522f4df710eaf3d78523b1f5f1641d508e21dc89473f17f1242933bd5aaadc995662924929e17a7a2eeb9475ebafad5ac30dc07fbcf8a7f49bf294866e25d92d435d5ded730c5b5480cf351eca73cfbb8261742ec487fdd1af5eaea46668ea66268f8c757a683229484abac6bd5679b42fda0f07ded1913190d05470dd7c24e2206c5e2a444aaa4b16b2555afb1b7a2e8c897501ed3c690c1bc7923b76f8ab596a4ad5570aebaeaec567cf0c76db6578666dd34745e0b51e30b0716fc652f6e8957b36f44a91157758899569189219c4a61e8b6f4700851b062134a2e4659b66cc153d1978c4bf7ef35c87da8c2d47deec03c1bf37f712324fdde96ce3bb76fefcab48128fe2f96ed4c83c3be3a52cba5df65b8e2c6559742af52e7c3fa92dafb594331ba5d24ce2ccbd2b84377a3e0cabc0b936d4b9d6c4915a4cd6bccb57ab65873f4b3ded869d806e2be03b5a59fd98ee847239cab6a899cf7b15ddc67941be03bb09da791f8d0fc1ef6ce2bde95ed14dfd5cfe6d0d0c6338cf7466caf6548d314cb8f68a2a0e1425bc423d8895afa3676b9f48f43e89157262fc1de164671c2316e889d3cdad62b535d5e44ba8b867ae80aebc76ba3ce952b54a9b481a268a75ee8792223859af3b99de09913e86f6c39b46e8e2b6eaeea9b2f224fdd13d2c57c94ee0ad1fc5709e32166985dbcbd85ead12c45ed5cd4dd62154401a9b46506f6138494852af659622ae2866ad46b44aebeeabb11ad004dda89ee927fc138b154792544a76f2a32aecd8d3322c170a32f41a1292a39ebd0d2bb6dd69c252603c7646dc331c9c594b794389c48dd988b38eb6b3c1c3fb3b91741773bd9808490d8860dcdbf2365913e0d1db32493832eec84ce5da66874185099d9cff852e3d9e31d44ba12221af7d5bd9882a66950bf0efca52bd295a9dcb834870a5cdc9a59af26d347514ee73cebf466a9b49166a3256c2d251149d20e4462e0e19fca53093ca8edaf5549ada38e8c893862e374bfa728d4235608829626b6bc413ab8375b788871e24aeb4bcd26574a29d511bc1e7dc6d0ee394ec540bad16cf0ab16344ba9e5f8bc56b68ea23f878c8c71fea0a18dedcab877f934ccb38136c69e7bf1a6616b71a86668887e85caf8bc6cafb7cda35fbcd9fa638dad291d31defd3dff003f16887c77c6f4c59f0f9b5848c9cae8d1e10f4b4af7cadd5f34655f3e13ba128f9f5ab6f10772e14c45c9c7e95d8c9aaf2393a3327367b58d22bce409e28cead863ca3c96cb4d01be44527e2e4b62b9f3e4de5f87f566d6c2b38a2d8776bcbd96b6bb1f68a1219f87f4de2110f26d545f1adaa005916b1f076345d397f0cf449e38795cbd155ad7b9b3f5d07285af06e7a2678c32b8b7d8bf0a600a908c8674134b2a4a5ab9aaad37d7ded60e0c5ca378839449de90e7556c577461180de9a5bcede632ebc75a7d5b30d0430264f4de93f7ef2b9b1bcd8d29dc769ebe2b93cf0ca72e4e8f9309dc4487c96691c91de3ba45b3f4ef659f38bffdcca2dc5a649c3bd9d9c76ad0b0c4412b4ac33c9f4eee6d681192343682d9a9bc8d956ea2b9a3c5100bf745a023692237c4e212f7efbd5894d473d33f6687d22f9347d161ec8f89d4414a457bde7a2ab73e4b38a1b55c893a29cbd0d3698460392100d21f06563a947508729897e11d261e45cae72e0cd310bed70cf5d72857e8d56e0f3c2089270f4657aeeededaaaab518d15c258c887dc542435dda617b4ba3a6dd13a2127bc5fbd112ad0553559aed6349d723629ae19708621905b31c6210c44cac3c40804d334553d4889b9966bbb57985305637298ad18ff00da29eb83a4e566efaf5f57834dbeb8659ce2d5fe04b97af6322626edd193e145cab58636e4db5fb6adedb5a48b8e3313d4da796d1111cf076e5f8879adc1a19f3cbf739f65738b3918f8b442598ab9a6eadd5e6b68d44bff008747958e1bf776b5e8ae0f3d967c82222d8296ef5cbcde6da2a697fcb9f5fab18964f8beacb124e72cb7b4b46bc729dec6915a4c213db56fe7bdbcc1bbcacf7b79a6d11b191c2adb3066bec51263b2b490514b9334841bde28c4bf0fceced68e1563b998ce5cf734b568ec72707c16584a4e25d04b0e53095e95e15bd48be9ced24149c63dfe5430bbc049e3cb12dc575d7bea6ac392175b5a3d2d2af578348c1c54e6227a32f2b5578feed9b93458eecf4ba6f56cbb14654d9341c6c3c48cef78c1783a52d529226ae666d42e3244bc680c9b37aef326aecf3690056ccd562509f1d1e8345a87971dbecf3a74ce3974c014becdba6ac589b6151928f7e5fca0da670964640a59c9e9b0c9818d736c142c2c978f48b69b4a4a8a1a421b8a31fd35f7361d526e8df10ba94a5d12f9319dbe7ef76775ed5ced174c6bdc5693800e2798232247e13b3bb061c4147519125030e4316e9d8a14af2aaf0b6addfbb59ded37030fee8dfcc435090bb15397aea65dcbb71efe25ebf77c5beda2aaac3aedea66bc927f7030bbb39bd331b48d2064e0def10e26bcedcd4885db557e355ad33c16a30a11ccd78aea73ad88955ab6af5b331144ba07dc7ba217f315e212ad3b5a46122c5d04a637a563c996e2a2ba1f0c7ceef211f1106d4a5d99a9a069688290ba59dcd2f1111ee7e2d1fa6753475150bf79f085c383bce9cfbd32eac13b5597861ba6907967f4f1b932dbc1981fbb284742f7f9af06779bfa95831ef273da96de66948d7b206cb57a39e7e1cadbe3e2ade8211a548f1daacb6c8e8e21bb27e668e5290e6d99b47af3e0c5897b7e6d1f8adb31d59c19222bf285e22da6b0918d927c84352fd29bea14b75e7c9b522d2e48e7cab6f295cbfc95e7c2a609917c394cf6ab48b4fc9a93f955538d1b3a2ad96cd65916f34136fe08a226dc1b4546dd1339ea6210fa082c676ac14b8c6743d2ce7cd8c5791f121d1bbcabbce9fbb37041cbf87d190749a2537e1ce2d26ecbfcbe9e6ca99730f76cb0d1a2325c299a501d4976ef47a970681a3e276b3dfd8cd52b4b0c21c0bc9488a651d78596eed6d9bacc7ba17f07a6f4ccd52dbf24b22318159772f05e871a05749b655bed906eb5632f1a2e9d02380274ea6f7977b2bb6a668a2080efb1911d3d312e4b0f9052da51d382549ba327b034893822da96b5d55e2d1914ea9ff69283a42987e436688c8269afafaba9ba2c6450c3b57e3e2dd3d027510e849d16cf2579b77d19cb34bc8dc707277455c60a918494a11eb8792d4536bd752d6982d8c78a8ca54dc94902534ba4268bdebeb5331100ea7941fca3d21b71eb6ca71a1eeb8d72223a3312d65dc8dca57da459712aa548d3543bee35e893b278537bb24aaadca95b4bfdf31d112bd3a3ae96d08f6a57577332ee8f7510fbf887a4ff00e1af7ad95fc9a69d3a91cdc195d08cbcccc9e483fc558a84251777c1164f247331cc37745ad7c1383f6780e3decbc6bebd36b14d48d50a42221822619d4590bb753213c9b6452baebafa9ba12bd10831274424ea5ba4ec92aeca99fa2c5f93287a967fc442938819ee16d67a9abb16f88ce50d19b92b5556d9577b49463cf5dd57363ada1a28b91b5a3afcb759e0dad13c8ea2762710939feabdcdbb3bd829287f76736311768a5dad2f3abc5848939cb7af697d77fd19e67be59a4f3dde92faeaaf3637b403666f8b4adaf1ecf06daa9395a5cf8615f6a2b68837e63da241f1f9309c0aacdade62483ca1fcc8de69dc46dfe48c25cf6b64533636a6be9f2f9b6c2dc0780a2c50bff859715ffb75b15d16d67ea8c56428122ea50d32e8fc29bfcd895901ff00cb95b959317d706f68f493d3b588af6e6d7e64dde7877ab2db2d46049c1c4c863a5368f7f57534470aa9d2ff00c868c8102945dd44f775e55f14a9198725fe3e3f46a9dea43854f5effbca217752598e1632b24534ecd3f4d72fa9fd1d66009ec11cc05c6382d21e4f3a677b4eb978e9e84ceb449a2e86023807447b429dd9a9b2fa1dec39f1f097794ef042f92b79e92a67b07522509c89b6c0ee40b9fdac94052ee220f8a3f76f7fd32b15a491e8f298285cb72e1893f849fa4cabda09d3d967bbf8bcda5c9e0b65546469489fab24577ff19830da22fc7dcd87f41ba7b2cffa5ac2a8c27a421b4d3c92b237c1050b420b97d70bff009615326ea0a0caf688cc5d8d2515493a7405214c4d5e07054f52ae9c7f484a77a5d146e8c5ca543549c63b9f839df0c5d3f38970f5e8ca4f06611215bb855cd84ba9a7fecd7853c507dd910f6574375dcc3cd6f9329f6ad14e1ed370ce1d0ca4e4662979eaa93cfb9a92204e8f8d7574addd8d5b96acef6dfc58ef12af060e696e9bbf276e8c7e33bd9087c39fe6d164739fc23ccd55a0b84a47c543520f7de95ce339d1131efd7f55b254324da58679ff007c59f0a3cf6aa1384a99aa2497a5f87ebccdacc37ba5b2388f7f9aaab6cf5ee89067d75b0d7946457bafbfc98ca969740d467feedfbfb95b24970476795dabcfced950dae4f7d5657e4dad4201f08f6e1bbe7b93a984940e41d9e32a6cb6dc688d9361dbe28d869b646d890c259f4a9b69873d99ec60a2c8d912bf9d495b49db4357995b6152ba3f97d1820573f7d5cf5b7a69259e6e8decdac2d8c8631843e5917e5cd7f4661da326af4426232976b9eac6dee65a229e70eaeba1278f74651aaf2f667168b2d4314e4b844cbd8b182867afdeecd7b5561e587ab46fd99417b77086f914a2ed6696bdca9a96bd78b57e94a49fc704a7744af4a39b7f66ba7d902491918f792f047e1454b7ad832a6a0ecd3d262d9c376ce8744dc8694ffa64432f52ad4cf3c11933b9932961e987eeaf0f197f5ebb30d5833a7a19ef6c0cb1a9347a0c6ed22b94c43899cc17487936755bbd921a463a1f6b8c1ee51dd6eb6997ce48f3cec118319d945db542cea979ff009b33b2e9596eeaf05ef69484a41d7faa3f9985ec432688e7ada22928670e8cbdd0f4b7342488a52e09e8aa4864b859ea683898c9ef1bff00d4d5a8b2233b976f73d7655af5b46450bd0398ca6e897a599b59f0c37e45b928744e4652633f150f313d2ff4c6bd78579c51ae347427dc34213d881fe31f0cc6455563668d7cc96f7b447d9ff068a71a56901f743fc8764154cb8ccbe88c9fdac7093d9e0c68e872f7f105297445315af7da9dfccd77161ae176ca5a8cfbbfa396d331df78537191d34ce88a5021c254b12ae65b56be76d1c2ce79bdf2fab284ba2203776599802bfd2f06d887b6a2644dddb378e86f72e9fbabb765d1d68a95d5e186e67687e1444c17ba8b99e0ecf66fc7d7c589109fc06c94a49294bd75d5d7e88d0ef004cefb03802d4671a92b4749808d868e09a1de8cd2cd2ce95d5876a616e0ccca324b35dd9f9e166ab1b97c13e7f026250ef487a3a8be4b6e28d64a3785c5fca8e745359784aac12bb6cb70f9b45c97650cbe9f5ce3e8b23c59cfa3a5cdfb5bbbad846633ca1a3d22ecc70ecebdcda3b8e7116e6684222c6edb592ad7f5c1b54e997ff2abbead76375d945e370b4cdb8ee793a36d8de614ceb907d83679379a283e487e33a5b5caca36e9b45caaf7304c840263fd5679f62322fe31eff484470bc5dddfd6c4331e9e53f1c122fe25d3a988c87c73fba345c553053cb0f2ebbc42b56ecfab24f94a7be5c6172bb59725cccd3b4bd8f0423fc9e7f1715107ef5e97c3862ce51aee4027ba328cb779ebf46417f34c5f46955f750c2ebaa62e57631c12b1d3baa00fd78d32bbe8d7bfb2ab87185ca7883dc29657dad41af69af7f65fff00b9b3fc40ddfc3654ba997aafdb0f0fdc74ea7dd49ec749868bbbaf4a5d115ab575a3388a260241a3ca66ddb818aa349c18dd2196f67562d1141c47b4400cff00cd764a05d68ad89ab8d35235b03e28de21db0043f2fc99e7a9f89967ad44b9162b15132357e290a22f18fc22d36f9df1ba7f95a3231461c263966fee638a19bb6909172c3867d1a4b81dc1a2a5a27ef18e19619dbc4276ee54a8952dad79ab6de81e0fbda7a27da622ec1897e7ea4d5537443e228f83b8222239ec6d0c38a96e651cf96dd222784d4c38a1e8d27e72caec793e88df3b52f483da4e927f18f7fa85eec65d14b6ab75d75d7daad67fb46e12fdf71e5070e5fc33b2f785369732f722feed48326d2c10af7bff000cecd3fc518566a8f2f7d306755ac929647c6af9b3102befb3b99b7c886b8256294b892231dd2f6d78f5b2223e9bd9e7a93b92f8525bba552eaef64404a42bba3d15d595672ec08be0f186d675ab2abf2da66d7932e51957c9207298722f81907e0240461c13e9826e288909e8ed59b957756aa88d7083a4dd4739743c68cda432d7295496d58d4b6e1e6d4453be5a39f9d58b15d3dbfb5a4857495130aabb35d4b8eeadab79232e18e554fbf93a0962ba2d96a9b9a79f0ba142b55131b7e6de6928fe8a411e2c92ce5c63dda94acaebddab0659ebd9eedebb55ec7bfc1b77d733cf9ef651e2c92b3516903329eee6d65de3cb8c579a1ddebf260a874ba32e73e2d21a0d47013d7c4463308f27b6aaf9ac675f2dcf898b083c54188f2af177599e65654c99b08f029cae46015af3f65c65c744c92ff392efe1f16a24d7f3e6d77fb2c3923225d00e9121786aaecad93acfdb1d87ee3b840e84ad4ea3a20a8fe154751911a2f8a777badb6b46aef0afed01fd134d8c1c0cbee465312aaf2d68aa98575d49e2c68ca7dc708dcb8a5607ddd210e482fdcd958d95a1254b6a6295e0d979a1ba14696374efc1d05e2326f7feac850dc20711c1c53d2e2dfe8de2d2b31afc6a6927e2d94d570cb90642c5bf9335ead55e6c68fa3e8f7b4f47f15fd01bc65c9de89a99a8c02a422461a1ef61316283d6bd6d72a2201d51f06e9c3a1965d22d64bad55ae69f16e76c5e7cb4a907848471050c2e1c8ca239b77b733fb54e167b27f0308f667af2b1944b5fd1ae1c34e103aa068a7af6694a5597a5f36f9d6948e7f49c63d8e8b2f7a5a3b85352266d6d3c78f7baf08cf9cf6afe4549480259af6d7497b99722bf9cd58b6e65a5dac0226b527f056465567672052fe7760c93b59d9c845be3d2f56144be89d109c25e50aecd7aabab3b9a3154af0def26b04280e9195db0be1cd5e08d0116923e7a3d259729dacf8f6221f02e6f33cacd6c37877f3e5ab16d1ebcd2e4fecde55d2f8b499529161204637fbdbd5901e7cd88a59b35d9530e423ff95dceaa996c346f3fc5f97eade61485926f34db3a89c7ebc8f4cafd7bd279c9cd8c77b7eada14c39f0decb9cdd1d1e4b19590224ce73dcdb397339e7eadebb2d5af7f73495150eaa4af912b44baaa8b52e3a98d23a52a566f18725d0f86f555d888c83d4cf523394897be21e492f7b204573bc59dd2170e8c2af2ff00b5ae3f66afc9d536f46696ecc5d8d4c76539b4f7059f939a79c0cb313c7449a5a2896eeb6daac6ada8578d96317de9111c248e288e10d27127a44f8e5bda36ad49bac4b39d8d44d231308f85ec1091448d524db49659cc95a22f773b46c5de897af7fd6227845aed55544af166e102b0e35c9f160ec65e32d9b1fddab35c16ae9f075988fb44e0cc598baa4e0c9c3f11bf10e46bb77a2256aa95f32b0e3e9fe0dc0871a74c46c4ba21bae445704b6b5af0c75f56e6e34693c4fbd2af66dad7d79eb63472a028a3a54aeaaaab6adff4609608b0a3928ebf407da6f07a1e245d3a837e2e8b49f3c245514c12c45c1ba344d3b06ea00a338d94076be5fb37ca70f792b9ca41aabe95a95a7359ccd71e12f089fbe8186a261fddbb9111e94cba4ba93c6d6efa6d34a244a6aad82e1a7090b8474a914dfc1bb2597a56d95d7a91ab4f4898ca22e8047a99726bca1f4e3b514dcb73b0645b3b3a5e1530c8a7399b62bedeab39ec653091b3b5665c2305d3bbf78376d6f6651539394638af921b2e30aba3a5a29cf67a3562962fe31ffc4bbfc5a5612248e03a52cb9cef687a5915dc51a2aaa2a163bd6c5d4bcec562b1c7dcc416f9e728d855f4f1f36ca26d7e1f5cf537b4cfe2abd7e8cb2cf9354ff931846ffea6f0ba53d4376f36e8b25d5199a546ceb35aba2de6de579bd7bdbcd209ffd9, 'image/jpeg'),
(53, 'Charles', 'test', '', '', '2005-01-08', 'cainta', 'Male', 'Single', '169', '82', 'o', '86195262262', '851541', '512651', '22822885858', '26541254', '54126541', 'Filipino', '', '4454545', '9845494', 'mayanicharles10@gmail.com', NULL, NULL),
(54, 'Dorias', 'Jahmell', '', '', '2002-05-05', 'Leyte', '', '', '164cm', '60kg', '', 'adwda3123', 'dawdaw', 'dawdawa', 'dawda', 'dawdaw', 'Charles', '', '', '312312312', 'awdawdawd', 'jahmelldorias17dawdawdawd', NULL, NULL),
(55, 'Park', 'Wonwoo', '', '', '2000-06-24', 'Pasig', 'Male', 'Single', '6\'00', '70kg', 'O', '9846-6518-5848', '3548-6452-6849-', '351898400', '5498-6118-6511', '3154-5951-6897', '848-9848-8541', 'Filipino', '', '+654987845', '095487485452', 'yasminpilapil1620@gmail.com', NULL, NULL),
(57, 'PILAPIL', 'YAEL JOSH', 'ORTALIZA', '', '2011-07-16', 'MAKATI CITY', 'Male', 'Single', '165', '60', 'A+', '878465123', '8465312', '86452130', '6542146', '000-000-000-000', '86451320', 'Filipino', '', '02 8123 4567', '09454057565', 'yasminpilapil1620@gmail.com', NULL, NULL),
(59, 'Mangobos', 'Jasper', 'n/a', 'n/a', '2006-03-10', 'Batangas', 'Male', 'Single', '6\'00', '70kg', 'o', '84651311654', '98465132', '984651', '9641365', '99846151315', '9/7846513', 'Filipino', '', '097415826', '09520568452', 'mangobosleslie22@gmail.com', NULL, NULL),
(74, 'PILAPIL', 'YASMIN JADE', 'ORTALIZA', 'V', '2005-08-20', 'Taguig City', 'Female', 'Single', '165', '60', 'B+', '54131231', '1312312', '423432', '65421', '000-000-000-000', '7894564512', 'Filipino', 'CHINESE', '1236541', '09052120289', 'yasminpilapil1620@gmail.com', NULL, NULL),
(75, 'Rizal', 'Jose', 'Alonso', '', '1861-06-19', 'Calamba Laguna', 'Male', 'Single', '165', '82', 'O+', '111111111111', '1111111111111111', '1111111111111', '11111111111111111', '000-000-000-000', '11111111111111', 'Filipino', '', '02-8123-4567', '09297437470', 'mayanicharles10@gmail.com', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `training`
--

CREATE TABLE `training` (
  `id` int(11) NOT NULL,
  `person_id` int(11) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `training_from` varchar(50) DEFAULT NULL,
  `training_to` varchar(50) DEFAULT NULL,
  `hours` varchar(20) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `sponsor` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training`
--

INSERT INTO `training` (`id`, `person_id`, `title`, `training_from`, `training_to`, `hours`, `type`, `sponsor`) VALUES
(11, 46, '', '2026-04-13', '2026-04-08', '67', '', ''),
(14, 59, 'Ojt', '2026-03-01', '2026-04-07', '300', 'n/a', 'n/a'),
(15, 75, 'OJT', '2026-04-06', '2026-04-15', '300', 'TEST', 'PCGG');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `reset_token` varchar(10) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `reset_token`, `reset_expiry`, `role`) VALUES
(4, 'charles', '', '$2y$10$k3XNCeCwwSieWEdgrJ3PaOtwlzwUeqYuvHZBXp3/aL88UknLEge6S', NULL, NULL, 'admin'),
(8, 'leslie', 'mangobosleslie22@gmail.com', '$2y$10$ryaAYHjWpYFb2yCY87z5t.l7nJpm.BY46qkFmh8x2c1r3lJdjSDsG', '112427', '2026-03-23 03:24:02', 'user'),
(9, 'Yasmin', 'yasminpilapil1620@gmail.com', '$2y$10$oq5naMy1dx6DTuF2/1Ei7O0JAhbYLIcTEX21rGAoOfb4fkZdRq7hy', '198459', '2026-03-24 09:15:13', 'user'),
(10, 'admin2', 'mayanicharles10@gmail.com', '$2y$10$p6Hr6FGIY7P1i52naCV.cedg9xvZ3VRuUe/4BnBlHQ1gJur4XUSgm', NULL, NULL, 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `eligibility`
--
ALTER TABLE `eligibility`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `personal_info`
--
ALTER TABLE `personal_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training`
--
ALTER TABLE `training`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email_2` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=428;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `eligibility`
--
ALTER TABLE `eligibility`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `personal_info`
--
ALTER TABLE `personal_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `training`
--
ALTER TABLE `training`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `personal_info` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `education_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `personal_info` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `eligibility`
--
ALTER TABLE `eligibility`
  ADD CONSTRAINT `eligibility_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `personal_info` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `training`
--
ALTER TABLE `training`
  ADD CONSTRAINT `training_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `personal_info` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
