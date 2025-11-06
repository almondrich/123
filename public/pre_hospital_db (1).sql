-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2025 at 09:29 AM
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
-- Database: `pre_hospital_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'user_login', 'User logged in: admin', '::1', '2025-11-05 02:17:52'),
(2, 1, 'form_created', 'Created form: PHC-20251105-E9BC9440 for patient: jhgfds', '::1', '2025-11-05 02:24:37'),
(3, 1, 'form_updated', 'Updated form: PHC-20251105-E9BC9440 for patient: jhgfds', '::1', '2025-11-05 03:37:12'),
(4, 1, 'form_updated', 'Updated form: PHC-20251105-E9BC9440 for patient: jhgfds', '::1', '2025-11-05 03:37:50'),
(5, 1, 'form_updated', 'Updated form: PHC-20251105-E9BC9440 for patient: jhgfds', '::1', '2025-11-05 03:46:37'),
(6, 1, 'form_updated', 'Updated form: PHC-20251105-E9BC9440 for patient: jhgfds', '::1', '2025-11-05 04:06:26'),
(7, 1, 'form_updated', 'Updated form: PHC-20251105-E9BC9440 for patient: SUYU', '::1', '2025-11-05 04:06:50'),
(8, 1, 'user_logout', 'User logged out', '::1', '2025-11-05 04:12:09'),
(9, 1, 'user_login', 'User logged in: admin', '::1', '2025-11-05 04:12:17'),
(10, 1, 'user_login', 'User logged in: admin', '::1', '2025-11-05 04:22:57'),
(11, 1, 'user_logout', 'User logged out', '::1', '2025-11-05 04:27:35'),
(12, 1, 'user_login', 'User logged in: admin', '::1', '2025-11-05 04:27:55'),
(13, 1, 'user_logout', 'User logged out', '::1', '2025-11-05 04:29:14'),
(14, 1, 'user_login', 'User logged in: admin', '::1', '2025-11-05 04:29:34'),
(15, 1, 'user_logout', 'User logged out', '::1', '2025-11-05 06:39:25'),
(16, 1, 'user_login', 'User logged in: admin', '::1', '2025-11-05 06:55:23'),
(17, 1, 'user_logout', 'User logged out', '::1', '2025-11-05 06:55:26'),
(18, 1, 'user_login', 'User logged in: admin', '::1', '2025-11-05 06:55:39'),
(19, 1, 'user_logout', 'User logged out', '::1', '2025-11-05 06:55:43'),
(20, 1, 'user_login', 'User logged in: admin', '::1', '2025-11-05 06:55:51'),
(21, 1, 'user_login', 'User logged in: admin', '::1', '2025-11-05 07:06:04'),
(22, 1, 'user_login', 'User logged in: admin', '::1', '2025-11-05 08:19:31'),
(23, 1, 'user_logout', 'User logged out', '::1', '2025-11-06 01:32:17'),
(24, 2, 'user_login', 'User logged in: rich', '::1', '2025-11-06 01:32:55'),
(25, 1, 'user_logout', 'User logged out', '::1', '2025-11-06 02:13:06'),
(26, 2, 'user_login', 'User logged in: rich', '::1', '2025-11-06 02:13:24'),
(27, 2, 'form_created', 'Created form: PHC-20251106-C2BB7EFC for patient: GFDSAsdfghjkl', '::1', '2025-11-06 02:41:18'),
(28, 2, 'form_updated', 'Updated form: PHC-20251106-C2BB7EFC for patient: GFDSAsdfghjkl', '::1', '2025-11-06 02:42:15'),
(29, 2, 'user_logout', 'User logged out', '::1', '2025-11-06 02:47:19'),
(30, 2, 'user_login', 'User logged in: rich', '::1', '2025-11-06 02:47:42'),
(31, 2, 'user_logout', 'User logged out', '::1', '2025-11-06 06:42:09'),
(32, 1, 'user_login', 'User logged in: admin', '::1', '2025-11-06 06:42:30');

-- --------------------------------------------------------

--
-- Stand-in structure for view `form_summary`
-- (See below for the actual view)
--
CREATE TABLE `form_summary` (
`id` int(10) unsigned
,`form_number` varchar(50)
,`form_date` date
,`patient_name` varchar(150)
,`age` int(10) unsigned
,`gender` enum('male','female')
,`vehicle_used` enum('ambulance','fireTruck','others')
,`status` enum('draft','completed','archived')
,`created_by_name` varchar(100)
,`created_at` timestamp
,`injury_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `injuries`
--

CREATE TABLE `injuries` (
  `id` int(10) UNSIGNED NOT NULL,
  `form_id` int(10) UNSIGNED NOT NULL,
  `injury_number` int(11) NOT NULL,
  `injury_type` enum('laceration','fracture','burn','contusion','abrasion','other') NOT NULL,
  `body_view` enum('front','back') NOT NULL,
  `coordinate_x` int(11) NOT NULL,
  `coordinate_y` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prehospital_forms`
--

CREATE TABLE `prehospital_forms` (
  `id` int(10) UNSIGNED NOT NULL,
  `form_number` varchar(50) NOT NULL,
  `form_date` date NOT NULL,
  `departure_time` time DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `vehicle_used` enum('ambulance','fireTruck','others') DEFAULT NULL,
  `vehicle_details` varchar(100) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `arrival_scene_location` varchar(255) DEFAULT NULL,
  `arrival_scene_time` time DEFAULT NULL,
  `departure_scene_location` varchar(255) DEFAULT NULL,
  `departure_scene_time` time DEFAULT NULL,
  `arrival_hospital_name` varchar(255) DEFAULT NULL,
  `arrival_hospital_time` time DEFAULT NULL,
  `departure_hospital_location` varchar(255) DEFAULT NULL,
  `departure_hospital_time` time DEFAULT NULL,
  `arrival_station_time` time DEFAULT NULL,
  `persons_present` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`persons_present`)),
  `patient_name` varchar(150) NOT NULL,
  `date_of_birth` date NOT NULL,
  `age` int(10) UNSIGNED NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `civil_status` enum('single','married') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `zone` varchar(50) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `place_of_incident` varchar(255) DEFAULT NULL,
  `zone_landmark` varchar(100) DEFAULT NULL,
  `incident_time` time DEFAULT NULL,
  `informant_name` varchar(150) DEFAULT NULL,
  `informant_address` text DEFAULT NULL,
  `arrival_type` enum('walkIn','call') DEFAULT NULL,
  `call_arrival_time` time DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `relationship_victim` varchar(100) DEFAULT NULL,
  `personal_belongings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`personal_belongings`)),
  `other_belongings` text DEFAULT NULL,
  `emergency_medical` tinyint(1) DEFAULT 0,
  `emergency_medical_details` text DEFAULT NULL,
  `emergency_trauma` tinyint(1) DEFAULT 0,
  `emergency_trauma_details` text DEFAULT NULL,
  `emergency_ob` tinyint(1) DEFAULT 0,
  `emergency_ob_details` text DEFAULT NULL,
  `emergency_general` tinyint(1) DEFAULT 0,
  `emergency_general_details` text DEFAULT NULL,
  `care_management` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`care_management`)),
  `oxygen_lpm` varchar(100) DEFAULT NULL,
  `other_care` text DEFAULT NULL,
  `initial_time` time DEFAULT NULL,
  `initial_bp` varchar(20) DEFAULT NULL,
  `initial_temp` decimal(4,1) DEFAULT NULL,
  `initial_pulse` int(11) DEFAULT NULL,
  `initial_resp_rate` int(11) DEFAULT NULL,
  `initial_pain_score` int(11) DEFAULT NULL,
  `initial_spo2` int(11) DEFAULT NULL,
  `initial_spinal_injury` enum('yes','no') DEFAULT NULL,
  `initial_consciousness` enum('alert','verbal','pain','unconscious') DEFAULT NULL,
  `initial_helmet` enum('ab','none') DEFAULT NULL,
  `followup_time` time DEFAULT NULL,
  `followup_bp` varchar(20) DEFAULT NULL,
  `followup_temp` decimal(4,1) DEFAULT NULL,
  `followup_pulse` int(11) DEFAULT NULL,
  `followup_resp_rate` int(11) DEFAULT NULL,
  `followup_pain_score` int(11) DEFAULT NULL,
  `followup_spo2` int(11) DEFAULT NULL,
  `followup_spinal_injury` enum('yes','no') DEFAULT NULL,
  `followup_consciousness` enum('alert','verbal','pain','unconscious') DEFAULT NULL,
  `chief_complaints` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`chief_complaints`)),
  `other_complaints` text DEFAULT NULL,
  `fast_face_drooping` enum('positive','negative') DEFAULT NULL,
  `fast_arm_weakness` enum('positive','negative') DEFAULT NULL,
  `fast_speech_difficulty` enum('positive','negative') DEFAULT NULL,
  `fast_time_to_call` enum('positive','negative') DEFAULT NULL,
  `fast_sample_details` text DEFAULT NULL,
  `ob_baby_status` varchar(100) DEFAULT NULL,
  `ob_delivery_time` time DEFAULT NULL,
  `ob_placenta` enum('in','out') DEFAULT NULL,
  `ob_lmp` date DEFAULT NULL,
  `ob_aog` varchar(50) DEFAULT NULL,
  `ob_edc` date DEFAULT NULL,
  `team_leader_notes` text DEFAULT NULL,
  `team_leader` varchar(100) DEFAULT NULL,
  `data_recorder` varchar(100) DEFAULT NULL,
  `logistic` varchar(100) DEFAULT NULL,
  `first_aider` varchar(100) DEFAULT NULL,
  `second_aider` varchar(100) DEFAULT NULL,
  `endorsement` varchar(255) DEFAULT NULL,
  `hospital_name` varchar(255) DEFAULT NULL,
  `received_by` varchar(100) DEFAULT NULL,
  `endorsement_datetime` datetime DEFAULT NULL,
  `endorsement_attachment` varchar(500) DEFAULT NULL COMMENT 'File path to endorsement attachment image (relative to uploads directory)',
  `waiver_patient_signature` varchar(255) DEFAULT NULL,
  `waiver_witness_signature` varchar(255) DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('draft','completed','archived') DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prehospital_forms`
--

INSERT INTO `prehospital_forms` (`id`, `form_number`, `form_date`, `departure_time`, `arrival_time`, `vehicle_used`, `vehicle_details`, `driver_name`, `arrival_scene_location`, `arrival_scene_time`, `departure_scene_location`, `departure_scene_time`, `arrival_hospital_name`, `arrival_hospital_time`, `departure_hospital_location`, `departure_hospital_time`, `arrival_station_time`, `persons_present`, `patient_name`, `date_of_birth`, `age`, `gender`, `civil_status`, `address`, `zone`, `occupation`, `place_of_incident`, `zone_landmark`, `incident_time`, `informant_name`, `informant_address`, `arrival_type`, `call_arrival_time`, `contact_number`, `relationship_victim`, `personal_belongings`, `other_belongings`, `emergency_medical`, `emergency_medical_details`, `emergency_trauma`, `emergency_trauma_details`, `emergency_ob`, `emergency_ob_details`, `emergency_general`, `emergency_general_details`, `care_management`, `oxygen_lpm`, `other_care`, `initial_time`, `initial_bp`, `initial_temp`, `initial_pulse`, `initial_resp_rate`, `initial_pain_score`, `initial_spo2`, `initial_spinal_injury`, `initial_consciousness`, `initial_helmet`, `followup_time`, `followup_bp`, `followup_temp`, `followup_pulse`, `followup_resp_rate`, `followup_pain_score`, `followup_spo2`, `followup_spinal_injury`, `followup_consciousness`, `chief_complaints`, `other_complaints`, `fast_face_drooping`, `fast_arm_weakness`, `fast_speech_difficulty`, `fast_time_to_call`, `fast_sample_details`, `ob_baby_status`, `ob_delivery_time`, `ob_placenta`, `ob_lmp`, `ob_aog`, `ob_edc`, `team_leader_notes`, `team_leader`, `data_recorder`, `logistic`, `first_aider`, `second_aider`, `endorsement`, `hospital_name`, `received_by`, `endorsement_datetime`, `endorsement_attachment`, `waiver_patient_signature`, `waiver_witness_signature`, `created_by`, `created_at`, `updated_at`, `status`) VALUES
(1, 'PHC-20251105-E9BC9440', '2025-11-08', '10:18:00', '00:00:00', 'ambulance', '{&amp;amp;amp;amp;amp;quot;type&amp;amp;amp;amp;amp;quot;:&amp;amp;amp;amp;amp;quot;ambulance&amp;am', NULL, NULL, '05:44:00', NULL, '05:25:00', NULL, '00:00:00', NULL, '00:00:00', '00:00:00', '[\"police\"]', 'SUYU', '1998-10-17', 27, 'male', 'single', 'ljhgfhjkl;', NULL, '][poiuyt', 'oiuytr', NULL, '00:00:00', 'khjhfhjkhuj', NULL, NULL, '06:32:00', '35688925498', 'mother in law', '[\"watch\"]', NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, '[]', NULL, NULL, '05:40:00', '160/90', 36.5, 95, NULL, NULL, 99, NULL, NULL, NULL, '00:00:00', NULL, 0.0, NULL, NULL, NULL, NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '00:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-11-05 02:24:37', '2025-11-05 04:06:50', 'completed'),
(2, 'PHC-20251106-C2BB7EFC', '2025-11-06', '10:37:00', '15:37:00', 'ambulance', '{&amp;quot;type&amp;quot;:&amp;quot;ambulance&amp;quot;,&amp;quot;id&amp;quot;:&amp;quot;V2&amp;quot', NULL, NULL, '10:38:00', NULL, '00:00:00', NULL, '10:38:00', NULL, '00:00:00', '00:00:00', '[]', 'GFDSAsdfghjkl', '1975-11-15', 49, 'male', 'single', 'jhgfdsa', '2', 'KJHGRFWQ', NULL, NULL, '10:38:00', NULL, NULL, 'call', '10:11:00', NULL, NULL, '[\"wallet\"]', NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, '[]', NULL, NULL, '10:39:00', '120/80', 36.5, 95, NULL, 10, NULL, NULL, 'unconscious', NULL, '10:39:00', '120/80', 36.5, NULL, NULL, NULL, NULL, NULL, 'alert', '[]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '00:00:00', NULL, NULL, NULL, NULL, NULL, 'KOKOY', 'KOKOY', 'LLL', 'KOKOY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2025-11-06 02:41:18', '2025-11-06 02:42:15', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL COMMENT 'IPv4 or IPv6 address',
  `action` varchar(100) NOT NULL COMMENT 'Action being rate limited (e.g., login, api_call)',
  `attempt_count` int(10) UNSIGNED DEFAULT 1 COMMENT 'Number of attempts in current window',
  `window_start` int(10) UNSIGNED NOT NULL COMMENT 'Unix timestamp of window start',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='IP-based rate limiting';

--
-- Dumping data for table `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `ip_address`, `action`, `attempt_count`, `window_start`, `created_at`, `updated_at`) VALUES
(1, '::1', 'login', 1, 1762411350, '2025-11-06 02:13:24', '2025-11-06 06:42:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','user','viewer') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `failed_attempts` int(10) UNSIGNED DEFAULT 0 COMMENT 'Number of failed login attempts',
  `locked_until` datetime DEFAULT NULL COMMENT 'Account locked until this time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `role`, `status`, `last_login`, `created_at`, `updated_at`, `failed_attempts`, `locked_until`) VALUES
(1, 'admin', '$2y$10$aC.wgMsZLIilm7XcElLtSe11m0bSeegGh8g/70RQ1KA3.1QM2mTc2', 'admin@prehospital.local', 'System Administrator', 'admin', 'active', '2025-11-06 14:42:30', '2025-11-05 02:14:40', '2025-11-06 06:42:30', 0, NULL),
(2, 'rich', '$2y$10$CZgIjeOw.EcQpvunn8Go5uTFw4dElugbIfAMfwSDg5s73xXZ4krdG', 'rrrr@gmail.com', 'richmond', 'user', 'active', '2025-11-06 10:47:42', '2025-11-06 01:24:23', '2025-11-06 02:47:42', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(10) UNSIGNED NOT NULL,
  `vehicle_id` varchar(20) NOT NULL,
  `vehicle_type` enum('ambulance','fire_truck') NOT NULL,
  `vehicle_subtype` varchar(50) DEFAULT NULL,
  `plate_number` varchar(20) NOT NULL,
  `status` enum('available','in_use','maintenance') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_id`, `vehicle_type`, `vehicle_subtype`, `plate_number`, `status`, `created_at`, `updated_at`) VALUES
(1, 'V1', 'ambulance', NULL, 'ABC1234', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40'),
(2, 'V2', 'ambulance', NULL, 'DEF5678', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40'),
(3, 'V3', 'ambulance', NULL, 'GHI9012', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40'),
(4, 'V4', 'ambulance', NULL, 'JKL3456', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40'),
(5, 'V5', 'ambulance', NULL, 'MNO7890', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40'),
(6, 'V6', 'ambulance', NULL, 'PQR1234', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40'),
(7, 'V7', 'ambulance', NULL, 'STU5678', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40'),
(8, 'V8', 'ambulance', NULL, 'VWX9012', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40'),
(9, 'V9', 'ambulance', NULL, 'YZA3456', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40'),
(10, 'V10', 'ambulance', NULL, 'BCD7890', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40'),
(11, 'V11', 'ambulance', NULL, 'EFG1234', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40'),
(12, 'V12', 'ambulance', NULL, 'HIJ5678', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40'),
(13, 'FT1', 'fire_truck', 'penetrator', 'FTP9999', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40'),
(14, 'FT2', 'fire_truck', 'tanker', 'FTT8888', 'available', '2025-11-05 02:14:40', '2025-11-05 02:14:40');

-- --------------------------------------------------------

--
-- Structure for view `form_summary`
--
DROP TABLE IF EXISTS `form_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `form_summary`  AS SELECT `f`.`id` AS `id`, `f`.`form_number` AS `form_number`, `f`.`form_date` AS `form_date`, `f`.`patient_name` AS `patient_name`, `f`.`age` AS `age`, `f`.`gender` AS `gender`, `f`.`vehicle_used` AS `vehicle_used`, `f`.`status` AS `status`, `u`.`full_name` AS `created_by_name`, `f`.`created_at` AS `created_at`, count(`i`.`id`) AS `injury_count` FROM ((`prehospital_forms` `f` left join `users` `u` on(`f`.`created_by` = `u`.`id`)) left join `injuries` `i` on(`f`.`id` = `i`.`form_id`)) GROUP BY `f`.`id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `injuries`
--
ALTER TABLE `injuries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_form_id` (`form_id`),
  ADD KEY `idx_injury_type` (`injury_type`);

--
-- Indexes for table `prehospital_forms`
--
ALTER TABLE `prehospital_forms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `form_number` (`form_number`),
  ADD KEY `idx_form_date` (`form_date`),
  ADD KEY `idx_patient_name` (`patient_name`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_endorsement_attachment` (`endorsement_attachment`);

--
-- Indexes for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_ip_action` (`ip_address`,`action`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_window_start` (`window_start`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_failed_attempts` (`failed_attempts`),
  ADD KEY `idx_locked_until` (`locked_until`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `idx_vehicle_type` (`vehicle_type`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `injuries`
--
ALTER TABLE `injuries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prehospital_forms`
--
ALTER TABLE `prehospital_forms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `injuries`
--
ALTER TABLE `injuries`
  ADD CONSTRAINT `injuries_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `prehospital_forms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prehospital_forms`
--
ALTER TABLE `prehospital_forms`
  ADD CONSTRAINT `prehospital_forms_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
