-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2024 at 01:28 PM
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
-- Database: `gm3builders`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `employee_number` varchar(50) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `contactno` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `birthdate` date NOT NULL,
  `civil_status` enum('Single','Married','Widowed') NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `hire_date` date NOT NULL,
  `employee_status` enum('Regular','Contractual','Archived') NOT NULL,
  `sss_no` varchar(20) DEFAULT NULL,
  `philhealth_no` varchar(20) DEFAULT NULL,
  `hdmf_no` varchar(20) DEFAULT NULL,
  `tin_no` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `emergency_contactno` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `position_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `project_name` varchar(100) DEFAULT NULL,
  `last_leave_reset_year` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `daily_salary` decimal(10,2) DEFAULT NULL,
  `withhold_tax` decimal(10,2) DEFAULT NULL,
  `sss_con` decimal(10,2) DEFAULT NULL,
  `philhealth_con` decimal(10,2) DEFAULT NULL,
  `pag_ibig_con` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `employee_number`, `lastname`, `firstname`, `middlename`, `contactno`, `address`, `birthdate`, `civil_status`, `basic_salary`, `hire_date`, `employee_status`, `sss_no`, `philhealth_no`, `hdmf_no`, `tin_no`, `email`, `password`, `emergency_contactno`, `created_at`, `updated_at`, `position_id`, `role_id`, `project_name`, `last_leave_reset_year`, `image_path`, `daily_salary`, `withhold_tax`, `sss_con`, `philhealth_con`, `pag_ibig_con`) VALUES
(1, '001', 'Caragan', 'Justin Kyle', 'O', '09090909981', 'Binangonan Rizal', '2002-09-03', 'Single', 25000.00, '2024-09-03', 'Regular', '5452561231231', '2165489541231', '123351351312', '124124123131', 'zekruz21@gmail.com', '$2y$10$E2b5161FoanhfdC4b66tROiUkooreU8usO5x0PP2juVyPrqaUocla', '065411004', '2024-09-03 15:09:00', '2024-11-26 06:04:21', 19, 1, 'Jollibee', 2024, 'uploads/employee_1.jpg', 5000.00, 0.00, 0.00, 0.00, 0.00),
(2, '002', 'Bonifacio', 'Mianah Venice', 'M', '03030303033', 'Binangonan', '2024-09-03', 'Single', 505000.00, '2024-09-03', 'Contractual', '5626263123123', '1561465812312', '164984123123', '184941231231', 'bonifaciohr@example.com', '$2y$10$hHM.QVObuMYjF.j961hP3edLZrJn.o5JUV4HThMYJO8nrdwq3u.Na', '022020220202', '2024-09-03 15:21:30', '2024-11-26 05:42:10', 19, 1, 'Kenny Rogers', 2024, '', 500.00, 500.00, 500.00, 500.00, 500.00),
(3, '003', 'Magno', 'Jessy Ville', 'R', '09090900990', 'Antipolo', '2024-09-04', 'Single', 255555.00, '2024-09-04', 'Regular', '6456460000000', '1241241200000', '356310000000', '342100000000', 'magnohr@example.com', '$2y$10$9aVG8K2oboNxSpTKm3WDhu/UItP.D4Joioxc8ipa8gsXRHe4CNM/G', '', '2024-09-04 12:25:17', '2024-11-12 13:32:07', 9, 2, 'McDo', 2024, '', NULL, NULL, NULL, NULL, NULL),
(4, '004', 'Francisco', 'Christian Jake', 'F', '02090909090', 'Taytay', '2024-09-04', 'Single', 255500.00, '2024-09-04', 'Regular', '5646541231233', '4654654512332', '546554313131', '541231313131', 'franciscohr@example.com', '$2y$10$wGab6ZopBVJZqivb0uBjeOcxleGyY9VM47KXdNiof39L/etDwkR1i', '', '2024-09-04 13:32:07', '2024-11-26 10:29:31', 10, 4, 'Jollibee', 2024, '', 5000.00, 0.00, 0.00, 0.00, 0.00),
(5, '005', 'Ortiz', 'Kristoffer', 'C', '02121021122', 'Antipolo', '2024-09-05', 'Single', 100.00, '2024-10-25', 'Regular', '641654', '54861251', '2165464', '1234', 'ortizemp@example.com', '$2y$10$2.aRuaCkMTEpcvxK3vwn0OYcA2tWLv8GOtCKcrRHq9im6JxXosixi', '', '2024-09-05 06:37:59', '2024-11-12 15:02:18', 17, 3, 'McDo', 2024, '', NULL, NULL, NULL, NULL, NULL),
(7, 'C', 'C', 'C', 'C', '02', 'C', '2024-09-18', 'Single', 1.00, '2024-09-18', 'Archived', '1', '1', '1', '1', 'chr@example.com', '$2y$10$ixLqdyPdrBB4C2FHmgdt2OX178gZABxekbVPrCgjvrBWRjXJ8XCtK', '1', '2024-09-18 07:56:57', '2024-10-25 09:27:46', 12, 3, NULL, 2024, '', NULL, NULL, NULL, NULL, NULL),
(8, '008', 'Caragan', 'Kyle', 'C', '031012302', 'asdad', '2024-10-01', 'Single', 1000000.00, '2024-10-01', 'Regular', '1231', '124124124', '1251', '12412', 'caraganad@example.com', '$2y$10$1pxOVawT54qqLw1gXeFmEOiPM3RFn7TnYP.rvEftQJ.uoLV2ePdo2', '231321231321', '2024-10-01 14:44:14', '2024-11-11 17:42:34', 17, 3, NULL, 2024, '', NULL, NULL, NULL, NULL, NULL),
(9, '09', 'a', 'a', 'a', '1', 'a', '2024-10-03', 'Single', 1.00, '2024-10-03', 'Regular', '1', '2', '2', '2', 'e@example.com', '$2y$10$Z37VUEn32N/yobcZ4OK1ZeSGW/Y/F9ACtdzEvqepxSdMKpVjY8yR.', '1', '2024-10-02 18:46:02', '2024-11-11 17:43:23', 3, 3, 'Mang Inasal', 2024, '', NULL, NULL, NULL, NULL, NULL),
(16, '021', 'V', 'V', 'V', '1', 'V', '2024-10-25', 'Married', 1.00, '2024-10-25', 'Archived', '1', '1', '1', '1', 'v@example.com', '$2y$10$YeqhwO6AFKFqKfVFhCO2H.P93Qa5RxTJnptTCBB/Tn4D3EJ2xItpK', '', '2024-10-25 14:36:14', '2024-10-25 14:36:22', 18, 2, 'Kenny Rogers', NULL, '', NULL, NULL, NULL, NULL, NULL),
(17, '0022', 'Silva', 'Austin', 'K', '0936225156', 'CA', '2024-10-31', 'Single', 100000.00, '2024-10-31', 'Regular', '1232567891234', '1234567891', '123456789123', '123456789132', 'silva@example.com', '$2y$10$nPurjxOnDB.MYmrYqFRp0.2olGjirqdDbA8Lc6SYlwZ7CJbSMMo8y', '', '2024-10-31 05:14:39', '2024-11-11 16:07:24', 7, 3, 'Jollibee', 2024, '', NULL, NULL, NULL, NULL, NULL),
(25, '018', 'D', 'D', 'D', '12123213123', 'D', '2024-10-31', 'Single', 123000.00, '2024-10-31', 'Contractual', '1213123123123', '1312312312321', '123123123123', '112312312312', 'sa@example.com', '$2y$10$ktMfvx2RR.7TuLyPUIKyG.w7rYOrR7Zf7Dkel5OOlGReaD9b.Ojiu', '', '2024-10-31 05:54:57', '2024-11-26 05:42:27', 13, 2, 'Kupal', NULL, '', 23123.00, 0.00, 0.00, 0.00, 0.00),
(26, '050', '123', '123', '123', 'asd', '123', '2024-11-04', 'Single', 0.00, '2024-11-04', 'Regular', 'asd', 'asd', 'asd', 'asd', 's@example.com', '$2y$10$wtZy/88vGIkwKTiluh4M1ueD128CuH6Up/BfmGrDQi7v8faLaU9lK', '', '2024-11-04 05:13:20', '2024-11-04 05:13:20', 12, 2, 'Kupal', NULL, '', NULL, NULL, NULL, NULL, NULL),
(31, '0031', 'Mano', 'Mano', '', '09167772343', 'Blah', '2024-11-12', 'Single', 50000.00, '2024-11-12', 'Regular', '1111111111111', '1111111111111', '111111111111', '111111111111', 'mano@example.com', '$2y$10$mz3G0Y2gy8dhLXgMSZmk2.34.6cncBAEtUgDoNx9x2Tdr9VcmlMwS', '', '2024-11-11 17:50:37', '2024-11-11 17:51:26', 10, 2, 'Meowmeow', NULL, '', NULL, NULL, NULL, NULL, NULL),
(32, '032', 'Ca', 'Ca', 'C', '09111111111', 'dsad', '2024-11-23', 'Single', 20800.00, '2024-11-23', 'Regular', '1231241242141', '1241241241241', '124124124124', '124124124141', 'ca@gmail.com', '$2y$10$JYZs6FqB/k8xaTBtKbkbHOoDg15pCr4uskLLifLSucMqO8Yxhl7Yu', '', '2024-11-23 12:03:13', '2024-11-23 12:03:13', 6, 2, 'Wendy\'s', NULL, '', 800.00, 0.00, 500.00, 100.00, 100.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `employee_number` (`employee_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `employee_number_2` (`employee_number`),
  ADD KEY `hire_date` (`hire_date`),
  ADD KEY `basic_salary` (`basic_salary`),
  ADD KEY `fk_position` (`position_id`),
  ADD KEY `fk_role` (`role_id`),
  ADD KEY `fk_project_name` (`project_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`),
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `fk_position` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`),
  ADD CONSTRAINT `fk_project_name` FOREIGN KEY (`project_name`) REFERENCES `projects` (`project_name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
