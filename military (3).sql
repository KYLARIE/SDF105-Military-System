-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2025 at 03:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `military`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `role` varchar(100) DEFAULT 'Administrator',
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `department` varchar(100) DEFAULT 'IT Department'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `profile_photo`, `role`, `email`, `phone`, `department`) VALUES
(1, 'admin', '$2y$10$ZYgLNPM2cz7bz9iIJQSklebO0KfJPSyBUn8UDo0p/2IzzRgPuYsUO', '../uploads/profile_1_1748872223.png', 'Administrator', '', '', 'IT Department'),
(2, 'kyla', '$2y$10$HRHjdClEg4j6PuZrRH3PY.HrtmfiSkHKAl2qJ4AHBPte7jeAKsESu', NULL, 'Administrator', NULL, NULL, 'IT Department'),
(3, 'marie', '$2y$10$KyRJMEdQZhIBhge3X/NXLupytmzPDXb7BYhKW5oBk68EzsC6PxKeu', NULL, 'Administrator', NULL, NULL, 'IT Department'),
(4, 'we', '$2y$10$JHocptXYZoxDtWXuRimjCOXpH5CUGZIvTJJAtTkBTiRYMUkQ9vLS6', NULL, 'Administrator', NULL, NULL, 'IT Department'),
(5, 'qw', '$2y$10$c0/GJ7LZfPFUrXKnYqebF.Sqwb1UWhXNdtGLM5embcLplW7FqPAxC', NULL, 'Administrator', NULL, NULL, 'IT Department'),
(6, 'earlrecometa@gmail.com', '$2y$10$nf9jj7UJSZUQATiWIJoRwefbWOAZFF4a.gc1lsnAE6RaGfyMeRPuy', NULL, 'Administrator', NULL, NULL, 'IT Department');

-- --------------------------------------------------------

--
-- Table structure for table `export_requests`
--

CREATE TABLE `export_requests` (
  `id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `filters` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `health`
--

CREATE TABLE `health` (
  `id` int(11) NOT NULL,
  `health_status_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `health`
--

INSERT INTO `health` (`id`, `health_status_name`) VALUES
(1, 'Unfit');

-- --------------------------------------------------------

--
-- Table structure for table `people`
--

CREATE TABLE `people` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rank_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `military_status` varchar(100) NOT NULL,
  `superior_id` int(11) DEFAULT NULL,
  `file_upload` varchar(255) DEFAULT NULL,
  `health_id` int(11) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `document` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `people`
--

INSERT INTO `people` (`id`, `name`, `age`, `contact`, `email`, `rank_id`, `unit_id`, `military_status`, `superior_id`, `file_upload`, `health_id`, `profile_image`, `document`, `status`) VALUES
(1, 'Katigbak', 70, '', NULL, 1, 1, 'Active Duty', NULL, NULL, 1, NULL, NULL, 'active'),
(2, 'RIZAL', 18, '', NULL, 3, 1, 'AWOL', 1, NULL, NULL, NULL, NULL, 'active'),
(3, 'Avril', 18, '0909898766', 'yudob0005@gmail.com', 2, 1, 'Veteran', 1, '../uploads/1748520227_Final_Paper04.pdf', NULL, NULL, NULL, 'active'),
(7, 'Karen', 22, '0909876755', NULL, 3, 1, 'Dishonorably Discharged', 1, '1748520628_Final_Paper04.pdf', 1, NULL, NULL, 'active'),
(11, 'Earl Recometa', 43, '4366867900-', 'earlrecometa@yahoo.com', 2, NULL, 'Active Duty', NULL, NULL, NULL, '1748578787_WIN_20230928_14_28_27_Pro.jpg', NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `ranks`
--

CREATE TABLE `ranks` (
  `id` int(11) NOT NULL,
  `rank_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ranks`
--

INSERT INTO `ranks` (`id`, `rank_name`) VALUES
(1, 'General'),
(2, 'Colonel'),
(3, 'Private');

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

CREATE TABLE `statuses` (
  `id` int(11) NOT NULL,
  `status_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` (`id`, `status_name`, `description`) VALUES
(1, 'Active Duty', 'Currently serving in active military capacity'),
(2, 'Reserve', 'Part-time military service obligation'),
(3, 'National Guard', 'State-based reserve military force'),
(4, 'Veteran', 'Honorably discharged from service'),
(5, 'Retired', 'Completed full military career'),
(6, 'Dishonorably Discharged', 'Separated from service under negative conditions'),
(7, 'AWOL', 'Absent Without Official Leave'),
(8, 'Inactive Reserve', 'Not currently training but may be recalled');

-- --------------------------------------------------------

--
-- Table structure for table `training`
--

CREATE TABLE `training` (
  `id` int(11) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(3) NOT NULL,
  `document` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training`
--

INSERT INTO `training` (`id`, `profile_image`, `name`, `age`, `document`, `created_at`, `updated_at`, `status`) VALUES
(25, '6839b6da3cad1', 'John M. Reyes', 24, '', '2025-05-30 13:47:09', '2025-05-30 14:58:34', 'deployed'),
(26, '6839b6ddee7f1', 'Carlos B. Dela Cruz', 25, '', '2025-05-30 13:47:12', '2025-05-30 15:59:16', 'deployed'),
(27, '6839b6e0a68b6', 'Marco A. Ramos', 27, '', '2025-05-30 13:47:15', '2025-05-30 14:57:57', 'deployed'),
(100, '683ee5fd3c9e3.jpg', 'John M. Reyes', 24, '683ee60066889.pdf', '2025-06-03 12:09:36', '2025-06-03 12:09:36', 'new'),
(101, '683ee60352b8f.jpg', 'Carlos B. Dela Cruz', 25, '683ee6068cb30.pdf', '2025-06-03 12:09:42', '2025-06-03 12:09:42', 'new'),
(102, '683ee609a2be1.jpg', 'Marco A. Ramos', 27, '683ee60d901ea.pdf', '2025-06-03 12:09:49', '2025-06-03 12:09:49', 'new'),
(103, '683ee610e1a91.jpg', 'Daniel J. Mendoza', 25, '683ee613e0d49.pdf', '2025-06-03 12:09:55', '2025-06-03 12:09:55', 'new'),
(104, '683ee6171eea5.jpg', 'Nathan E. Torres', 24, '683ee61ac44ac.pdf', '2025-06-03 12:10:02', '2025-06-03 12:10:02', 'new'),
(105, '683ee61e53e23.jpg', 'Adrian L. Hernandez', 23, '683ee6213bede.pdf', '2025-06-03 12:10:09', '2025-06-03 12:10:09', 'new'),
(106, '683ee6243ecd8.jpg', 'Cassandra M. Santos', 23, '683ee62817aa0.pdf', '2025-06-03 12:10:16', '2025-06-03 12:10:16', 'new'),
(107, '683ee62ada69f.jpg', 'Jasmine C. Villanueva', 25, '683ee62df1fc9.pdf', '2025-06-03 12:10:22', '2025-06-03 12:10:22', 'new'),
(108, '683ee631b2868.jpg', 'Sophia L. Navarro', 27, '683ee634bc3fc.pdf', '2025-06-03 12:10:28', '2025-06-03 12:10:28', 'new'),
(109, '683ee6375f174.jpg', 'Kristine A. Bautista', 25, '683ee63a64f56.jpg', '2025-06-03 12:10:34', '2025-06-03 12:10:34', 'new');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `unit_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `unit_name`) VALUES
(1, 'Navy');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `export_requests`
--
ALTER TABLE `export_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requester_id` (`requester_id`);

--
-- Indexes for table `health`
--
ALTER TABLE `health`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rank_id` (`rank_id`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `superior_id` (`superior_id`),
  ADD KEY `health_id` (`health_id`);

--
-- Indexes for table `ranks`
--
ALTER TABLE `ranks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training`
--
ALTER TABLE `training`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `export_requests`
--
ALTER TABLE `export_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `health`
--
ALTER TABLE `health`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `people`
--
ALTER TABLE `people`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `ranks`
--
ALTER TABLE `ranks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `training`
--
ALTER TABLE `training`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `export_requests`
--
ALTER TABLE `export_requests`
  ADD CONSTRAINT `export_requests_ibfk_1` FOREIGN KEY (`requester_id`) REFERENCES `people` (`id`);

--
-- Constraints for table `people`
--
ALTER TABLE `people`
  ADD CONSTRAINT `people_ibfk_1` FOREIGN KEY (`rank_id`) REFERENCES `ranks` (`id`),
  ADD CONSTRAINT `people_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`),
  ADD CONSTRAINT `people_ibfk_3` FOREIGN KEY (`superior_id`) REFERENCES `people` (`id`),
  ADD CONSTRAINT `people_ibfk_4` FOREIGN KEY (`health_id`) REFERENCES `health` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
