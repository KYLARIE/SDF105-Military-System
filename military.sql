-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2025 at 03:09 PM
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
-- Database: `military`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$ZYgLNPM2cz7bz9iIJQSklebO0KfJPSyBUn8UDo0p/2IzzRgPuYsUO'),
(2, 'kyla', '$2y$10$HRHjdClEg4j6PuZrRH3PY.HrtmfiSkHKAl2qJ4AHBPte7jeAKsESu'),
(3, 'marie', '$2y$10$KyRJMEdQZhIBhge3X/NXLupytmzPDXb7BYhKW5oBk68EzsC6PxKeu'),
(4, 'we', '$2y$10$JHocptXYZoxDtWXuRimjCOXpH5CUGZIvTJJAtTkBTiRYMUkQ9vLS6'),
(5, 'qw', '$2y$10$c0/GJ7LZfPFUrXKnYqebF.Sqwb1UWhXNdtGLM5embcLplW7FqPAxC');

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
  `health_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `people`
--

INSERT INTO `people` (`id`, `name`, `age`, `contact`, `email`, `rank_id`, `unit_id`, `military_status`, `superior_id`, `file_upload`, `health_id`) VALUES
(1, 'Katigbak', 70, '', NULL, 1, 1, 'Active Duty', NULL, NULL, 1),
(2, 'RIZAL', 18, '', NULL, 3, 1, 'AWOL', 1, NULL, NULL),
(3, 'Avril', 18, '0909898766', 'yudob0005@gmail.com', 2, 1, 'Veteran', 1, '../uploads/1748520227_Final_Paper04.pdf', NULL),
(4, 'test', 20, '', NULL, NULL, NULL, 'Active Duty', NULL, NULL, 1),
(7, 'Karen', 22, '0909876755', NULL, 3, 1, 'Dishonorably Discharged', 1, '1748520628_Final_Paper04.pdf', 1);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `health`
--
ALTER TABLE `health`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `people`
--
ALTER TABLE `people`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

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
