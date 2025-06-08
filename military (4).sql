-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 12:13 PM
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
(1, 'Unfit'),
(2, 'Fit'),
(4, 'Sick'),
(5, 'Limited Duty'),
(6, 'Under Check-up');

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
(11, 'Earl Recometa', 43, '4366867900-', 'earlrecometa@yahoo.com', 5, 5, 'Active', NULL, NULL, 1, '1749372173_peter.jpg', NULL, 'active'),
(66, 'John M. Reyes', 24, '', '', 1, 1, 'Active', NULL, NULL, 2, '1749376663_John M. Reyes.jpg', '68455e0449f6d.pdf', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `ranks`
--

CREATE TABLE `ranks` (
  `id` int(11) NOT NULL,
  `rank_name` varchar(100) NOT NULL,
  `abbreviation` varchar(20) DEFAULT NULL,
  `pay_grade` varchar(20) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ranks`
--

INSERT INTO `ranks` (`id`, `rank_name`, `abbreviation`, `pay_grade`, `category`) VALUES
(1, 'Private', 'Pvt', 'E-1', 'Enlisted'),
(2, 'Sergeant', 'Sgt', 'E-5', 'Enlisted'),
(5, 'Colonel', 'Col', '0-6', 'Officer'),
(9, 'Commander', 'CDR', '0-5', 'Officer');

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
(1, 'Active', 'Currently serving in active military capacity'),
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
(230, '', 'John M. Reyes', 24, '68455e0449f6d.pdf', '2025-06-08 09:55:16', '2025-06-08 09:57:16', 'deployed'),
(231, '68455e0739e4f.jpg', 'Carlos B. Dela Cruz', 25, '68455e0a176d8.pdf', '2025-06-08 09:55:22', '2025-06-08 09:55:22', 'new'),
(232, '68455e0cd704e.jpg', 'Marco A. Ramos', 27, '68455e0f7d576.pdf', '2025-06-08 09:55:27', '2025-06-08 09:55:27', 'new'),
(233, '68455e12397ed.jpg', 'Daniel J. Mendoza', 25, '68455e1519e97.pdf', '2025-06-08 09:55:33', '2025-06-08 09:55:33', 'new'),
(234, '68455e17a2618.jpg', 'Nathan E. Torres', 24, '68455e1a61e63.pdf', '2025-06-08 09:55:38', '2025-06-08 09:55:38', 'new'),
(235, '68455e1ccfb06.jpg', 'Adrian L. Hernandez', 23, '68455e2002a19.pdf', '2025-06-08 09:55:44', '2025-06-08 09:55:44', 'new'),
(236, '68455e22a775e.jpg', 'Cassandra M. Santos', 23, '68455e256e29b.pdf', '2025-06-08 09:55:49', '2025-06-08 09:55:49', 'new'),
(237, '68455e27dfb2c.jpg', 'Jasmine C. Villanueva', 25, '68455e2a8f1e2.pdf', '2025-06-08 09:55:54', '2025-06-08 09:55:54', 'new'),
(238, '68455e2cefd93.jpg', 'Sophia L. Navarro', 27, '68455e2f7d1f1.pdf', '2025-06-08 09:55:59', '2025-06-08 09:55:59', 'new'),
(239, '68455e325d307.jpg', 'Kristine A. Bautista', 25, '68455e35295b9.pdf', '2025-06-08 09:56:05', '2025-06-08 09:56:05', 'new');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `unit_name` varchar(100) NOT NULL,
  `commander_id` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `unit_name`, `commander_id`, `location`) VALUES
(1, 'Navy', NULL, 'Philippines'),
(5, 'Army', 11, 'Philippines'),
(6, 'Air Force', 46, 'Philippines');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `people`
--
ALTER TABLE `people`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `ranks`
--
ALTER TABLE `ranks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `training`
--
ALTER TABLE `training`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
