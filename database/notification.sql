-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 11, 2024 at 12:46 PM
-- Server version: 8.3.0
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u327394152_gm3builders`
--

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `request_type` enum('leave_request','profile_change','','') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `message`, `is_read`, `request_type`, `timestamp`) VALUES
(1, 'testasdfa', 0, 'leave_request', '2024-11-11 11:03:04'),
(2, 'gasdfasgsadfasdgfasr', 0, 'leave_request', '2024-11-11 11:03:31'),
(3, '  Request Leave Approval', 0, 'leave_request', '2024-11-11 12:27:39'),
(4, '  Request Leave Approval', 0, 'leave_request', '2024-11-11 12:27:50'),
(5, '  Request Leave Approval', 0, 'leave_request', '2024-11-11 12:28:01'),
(6, '  Request Leave Approval', 0, 'leave_request', '2024-11-11 12:29:25'),
(7, '  Request Leave Approval', 0, 'leave_request', '2024-11-11 12:32:47'),
(8, '  Request Leave Approval', 0, 'leave_request', '2024-11-11 12:35:58'),
(9, '  Request Leave Approval', 0, 'leave_request', '2024-11-11 12:41:16'),
(10, '  Request Leave Approval', 0, 'leave_request', '2024-11-11 12:41:18'),
(11, '  Request Leave Approval', 0, 'leave_request', '2024-11-11 12:41:29'),
(12, 'Justin Kyle Caragan Request Leave Approval', 0, 'leave_request', '2024-11-11 12:44:07');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
