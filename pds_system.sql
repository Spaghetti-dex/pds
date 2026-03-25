-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2026 at 10:29 AM
-- Server version: 8.0.44
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
  `id` int NOT NULL,
  `person_id` int DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `house` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `street` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subdivision` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `barangay` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `zip` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `person_id`, `type`, `house`, `street`, `subdivision`, `barangay`, `city`, `province`, `zip`) VALUES
(17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 32, 'Residential', '', '', '', '', '', '', ''),
(40, 32, 'Permanent', '', '', '', '', '', '', ''),
(41, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 40, 'residential', '', '', '', '', '', '', ''),
(52, 40, 'permanent', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int NOT NULL,
  `person_id` int DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `action` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(13, 39, 'Yasmin', 'DELETE', 'Deleted PDS record with ID 39', '2026-03-25 06:30:18');

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int NOT NULL,
  `person_id` int DEFAULT NULL,
  `education_level` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `school_name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `course` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `edu_from` date DEFAULT NULL,
  `edu_to` date DEFAULT NULL,
  `units` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `year_graduated` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `honors` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `person_id`, `education_level`, `school_name`, `course`, `edu_from`, `edu_to`, `units`, `year_graduated`, `honors`) VALUES
(10, 32, 'Elementary', '', '', '2026-03-23', '2026-03-23', '', '', ''),
(12, 40, 'Elementary', '', '', '2026-03-26', '2026-03-25', '', '4242', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `eligibility`
--

CREATE TABLE `eligibility` (
  `id` int NOT NULL,
  `person_id` int DEFAULT NULL,
  `career_service` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rating` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `exam_date` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `exam_place` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `license` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `license_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `valid_until` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_development`
--

CREATE TABLE `learning_development` (
  `id` int NOT NULL,
  `person_id` int DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hours` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `learning_development`
--

INSERT INTO `learning_development` (`id`, `person_id`, `title`, `hours`) VALUES
(2, 32, '', 36);

-- --------------------------------------------------------

--
-- Table structure for table `personal_info`
--

CREATE TABLE `personal_info` (
  `id` int NOT NULL,
  `surname` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `firstname` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `middlename` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `extension` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `birth_place` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sex` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `civil_status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `height` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `weight` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `blood_type` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `umid` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pagibig` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `philhealth` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `philsys` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tin` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `agency_employee` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `citizenship` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dual_country` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personal_info`
--

INSERT INTO `personal_info` (`id`, `surname`, `firstname`, `middlename`, `extension`, `dob`, `birth_place`, `sex`, `civil_status`, `height`, `weight`, `blood_type`, `umid`, `pagibig`, `philhealth`, `philsys`, `tin`, `agency_employee`, `citizenship`, `dual_country`, `telephone`, `mobile`, `email`) VALUES
(32, 'MANGOBO', 'LESLIE', 'DG', 'MARIA', '2026-03-11', 'PASIG CITY', 'Female', 'Married', '168cm', '60kg', 'A+', '0123143', '1312312', '423432', '131231', '3123213', '7894564512', 'Filipino', 'CHINESE', '789456123645', '2133235353', 'yasminpilapil1620@gmail.com'),
(35, 'lesli', 'asas', 'sasa', 'Jr', '2026-03-23', 'sasas', 'Male', 'Single', '165', '60kg', 'O', '54131231', '1312312', '423432', '131231', '3123213', '7894564512', 'Filipino', 'Philippines', '52456+', '9052120289', 'yasminpilapil1620@gmail.com'),
(40, 'Pilapil', 'Yasmin', 'Jade', '', '2026-03-25', 'Taguig City', '', '', '', '', '', '', '', '', '', '', '', '', 'Philippines', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `service_eligibility`
--

CREATE TABLE `service_eligibility` (
  `id` int NOT NULL,
  `person_id` int DEFAULT NULL,
  `career_service` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rating` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `training`
--

CREATE TABLE `training` (
  `id` int NOT NULL,
  `person_id` int DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `training_from` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `training_to` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hours` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sponsor` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training`
--

INSERT INTO `training` (`id`, `person_id`, `title`, `training_from`, `training_to`, `hours`, `type`, `sponsor`) VALUES
(2, 40, '', '2026-03-24', '2026-03-25', '45', '', ''),
(3, 40, '', '2026-03-25', '2026-03-25', '45', '', ''),
(4, 40, '', '2026-03-25', '2026-03-26', '45', '', ''),
(5, 40, '', '2026-03-26', '2026-03-26', '45', '', ''),
(6, 40, '', '2026-03-25', '2026-03-24', '45', '', ''),
(7, 40, '', '2026-03-24', '2026-03-24', '12', '', ''),
(8, 40, '', '2026-03-23', '2026-03-25', '18', '', ''),
(9, 40, '', '2026-03-23', '2026-03-05', '88', '', ''),
(10, 40, '', '2026-03-26', '2026-03-26', '10', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `role` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user'
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
-- Indexes for table `learning_development`
--
ALTER TABLE `learning_development`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_info`
--
ALTER TABLE `personal_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_eligibility`
--
ALTER TABLE `service_eligibility`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `eligibility`
--
ALTER TABLE `eligibility`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_development`
--
ALTER TABLE `learning_development`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personal_info`
--
ALTER TABLE `personal_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `service_eligibility`
--
ALTER TABLE `service_eligibility`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `training`
--
ALTER TABLE `training`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
