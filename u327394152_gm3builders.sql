-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 29, 2024 at 07:22 PM
-- Server version: 10.11.8-MariaDB-cll-lve
-- PHP Version: 7.2.34

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
-- Table structure for table `dtr`
--

CREATE TABLE `dtr` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `total_hrs` float DEFAULT NULL,
  `other_ot` float DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dtr`
--

INSERT INTO `dtr` (`id`, `employee_id`, `day`, `month`, `year`, `time_in`, `time_out`, `total_hrs`, `other_ot`, `date`, `time`) VALUES
(22, 1, NULL, NULL, NULL, '08:00:00', '20:00:00', 12, 4, '2024-09-25', NULL),
(23, 1, NULL, NULL, NULL, '08:00:00', '22:00:00', 14, 6, '2024-09-26', NULL),
(24, 2, NULL, NULL, NULL, '08:00:00', '21:00:00', 13, 5, '2024-09-26', NULL),
(25, 4, NULL, NULL, NULL, '08:00:00', '23:00:00', 15, 7, '2024-09-26', NULL),
(26, 3, NULL, NULL, NULL, '08:00:00', '20:00:00', 12, 4, '2024-09-26', NULL),
(28, 5, NULL, NULL, NULL, '07:00:00', '17:00:00', 10, 2, '2024-09-29', NULL),
(29, 1, NULL, NULL, NULL, '06:00:00', '21:00:00', 15, 7, '2024-09-29', NULL),
(30, 1, NULL, NULL, NULL, '08:00:00', '20:00:00', 12, 4, '2024-09-28', NULL),
(31, 2, NULL, NULL, NULL, '12:27:00', NULL, 0, NULL, '2024-10-12', NULL),
(32, 2, NULL, NULL, NULL, '10:00:00', NULL, 0, NULL, '2024-10-24', NULL),
(33, 1, 28, 10, 2024, '08:00:00', '19:00:00', 11, 3, '2024-10-28', NULL),
(34, 2, 29, 10, 2024, '07:00:00', '17:00:00', 10, 2, '2024-10-29', NULL),
(35, 1, 29, 10, 2024, '00:44:00', NULL, 0, NULL, '2024-10-29', NULL),
(36, 1, 30, 10, 2024, '00:45:00', NULL, 0, NULL, '2024-10-30', NULL);

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
  `religion` varchar(100) DEFAULT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `hire_date` date NOT NULL,
  `employee_status` enum('Regular','Probationary','Archived') NOT NULL,
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
  `project_name` varchar(100) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `last_leave_reset_year` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT 'assets/images/account.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `employee_number`, `lastname`, `firstname`, `middlename`, `contactno`, `address`, `birthdate`, `civil_status`, `religion`, `basic_salary`, `hire_date`, `employee_status`, `sss_no`, `philhealth_no`, `hdmf_no`, `tin_no`, `email`, `password`, `emergency_contactno`, `created_at`, `updated_at`, `archived`, `position_id`, `project_name`, `role_id`, `last_leave_reset_year`) VALUES
(1, '001', 'Caragan', 'Justin Kyle', 'O', '09586758493', 'Antipolo Rizal BLK 15 LOT 13 DALIG ', '2024-10-29', 'Single', 'Catholic', 10000.00, '2024-09-03', 'Regular', '0038475859411', '3213214432424', '135153313151', '312415151223', 'caraganhr@example.com', '$2y$10$D3nBti4qjCEudihylthGM.ctpDQsvdAaxfprqPiprkJoVYxC7v75K', '065411004', '2024-09-03 15:09:00', '2024-10-29 11:05:39', 0, 1, 'Jollibee', 1, 2024),
(2, '002', 'Bonifacio', 'Mianah Venice', 'M', '03030303033', 'Binangonan', '2024-10-31', 'Single', 'Christian', 50.00, '2024-09-03', 'Regular', '5626263123123', '1561465832141', '164984312321', '184942132132', 'bonifaciohr@example.com', '$2y$10$PhMCUBRU3TIl5KYBq1SMtu1urgqXGZy02SW5U3zlB64anQbDKYs1a', '022020220202', '2024-09-03 15:21:30', '2024-10-29 09:11:33', 0, 19, 'Jollibee', 1, 2024),
(3, 'ewqe', 'Magno', 'Jessy Ville', 'R', '0909090099099', 'Antipolo', '2024-09-04', 'Single', 'Christian', 25.00, '2024-09-04', 'Regular', '645646', '12412412', '35631', '3421', 'magnohr@example.com', '$2y$10$ToCxVkul4k8fkuRejIgh4uZwDmrfegGintT2KUxrXS96kdbVxjve.', '', '2024-09-04 12:25:17', '2024-10-29 09:43:50', 0, 17, 'Jollibee', 2, 2024),
(4, '003', 'Francisco', 'Christian Jake', 'F', '02136457894', 'Taytay', '2024-09-04', 'Single', 'Christian', 25.00, '2024-09-04', 'Regular', '5646541245711', '4654654511111', '546554111111', '541211111111', 'franciscohr@example.com', '$2y$10$aZRoIda4koMEq2hRYw/6FuH61X96AEhPPyGMzUN2qPGXAWwBxUEfW', '', '2024-09-04 13:32:07', '2024-10-29 09:12:30', 0, 7, 'Jollibee', 2, NULL),
(5, 'dfds', 'Ortiz', 'Kristoffer', 'C', '02121021122', 'Antipolo', '2024-09-05', 'Single', 'Catholic', 100.00, '2024-09-05', 'Regular', '641654', '54861251', '2165464', '1234', 'ortizemp@example.com', '$2y$10$k4i0HO8yV5DMLAywTbd7YOgewS/Xcny0h7T0CFg2g6.gv3W7QArKS', '', '2024-09-05 06:37:59', '2024-10-25 15:37:37', 0, 5, 'Jollibee', 3, NULL),
(6, 'C', 'C', 'C', 'C', '02', 'C', '2024-09-18', 'Single', 'c', 1.00, '2024-09-18', 'Archived', '1', '1', '1', '1', 'chr@example.com', '$2y$10$ixLqdyPdrBB4C2FHmgdt2OX178gZABxekbVPrCgjvrBWRjXJ8XCtK', '1', '2024-09-18 07:56:57', '2024-10-24 06:31:00', 0, 12, NULL, 1, NULL),
(7, '008', 'Caragan', 'Kyle', 'C', '031012302', 'asdad', '2024-10-01', 'Single', 'catholic', 1000000.00, '2024-10-01', 'Archived', '1231', '124124124', '1251', '12412', 'caraganad@example.com', '$2y$10$1pxOVawT54qqLw1gXeFmEOiPM3RFn7TnYP.rvEftQJ.uoLV2ePdo2', '231321231321', '2024-10-01 14:44:14', '2024-10-24 06:31:03', 0, 17, NULL, 2, NULL),
(8, '09', 'a', 'a', 'a', '1', 'a', '2024-10-03', 'Single', 'a', 1.00, '2024-10-03', 'Archived', '1', '2', '2', '2', 'e@example.com', '$2y$10$OUW59v9PPB2.9sesh32tSOo/v6SXm669tg6lMeuOpZ8OnQVcaQBKa', '1', '2024-10-02 18:46:02', '2024-10-24 06:31:08', 0, 8, 'Jollibee', 3, NULL),
(9, '10', 'Piodo', 'John ', 'Arnuco', '09307292755', '4-3 Westbank rd. Maybunga Pasig City', '2003-08-23', 'Single', 'Christian', 20000.00, '2024-08-01', 'Probationary', '123456', '01-021684564-1', 'awdai', '056+46526545', 'arnoldpiodo25@gmail.com', '$2y$10$KmDLl75C2p8vBFtmmpmYq.EWvRQT1hUj2UUe8zTdAxDhPvmPwfTSC', 'awdipjdaw', '2024-10-12 04:15:38', '2024-10-24 06:31:11', 0, 15, 'Jollibee', 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `leave_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `sick_leave` int(11) DEFAULT 0,
  `vacation_leave` int(11) DEFAULT 0,
  `leave_without_pay` int(11) DEFAULT 0,
  `used_leave` int(11) DEFAULT 0,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`leave_id`, `employee_id`, `sick_leave`, `vacation_leave`, `leave_without_pay`, `used_leave`, `role_id`) VALUES
(1, 1, 7, 7, 0, 0, 1),
(2, 2, 7, 7, 0, 0, 1),
(3, 4, 7, 7, 0, 0, 1),
(4, 6, 7, 7, 0, 0, 1),
(5, 3, 7, 7, 0, 0, 2),
(6, 7, 7, 7, 0, 0, 2),
(8, 5, 6, 6, 0, 0, 3),
(9, 8, 6, 6, 0, 0, 3),
(10, 9, 6, 6, 0, 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `request_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type` varchar(50) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `reason` text DEFAULT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`request_id`, `employee_id`, `leave_type`, `start_date`, `end_date`, `status`, `reason`, `request_date`) VALUES
(14, 1, 'Vacation Leave', '2024-10-30', '2024-11-07', 'Pending', 'Boracay', '2024-10-29 17:10:26');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `employee_id` int(11) NOT NULL,
  `employee_number` varchar(50) NOT NULL,
  `position_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `allowance` decimal(10,2) DEFAULT NULL,
  `monthly` decimal(10,2) DEFAULT NULL,
  `daily` decimal(10,2) DEFAULT NULL,
  `hourly` decimal(10,2) DEFAULT NULL,
  `total_hrs` decimal(10,2) DEFAULT NULL,
  `other_ot` decimal(10,2) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `special_holiday` decimal(10,2) DEFAULT NULL,
  `special_leave` decimal(10,2) DEFAULT NULL,
  `incentives` decimal(10,2) DEFAULT NULL,
  `gross` decimal(10,2) DEFAULT NULL,
  `less_cont` decimal(10,2) DEFAULT NULL,
  `cash_adv` decimal(10,2) DEFAULT NULL,
  `total_deduc` decimal(10,2) DEFAULT NULL,
  `netpay` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`employee_id`, `employee_number`, `position_id`, `role_id`, `basic_salary`, `allowance`, `monthly`, `daily`, `hourly`, `total_hrs`, `other_ot`, `salary`, `special_holiday`, `special_leave`, `incentives`, `gross`, `less_cont`, `cash_adv`, `total_deduc`, `netpay`) VALUES
(1, '001', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(2, '002', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `can_view_own_data` tinyint(1) DEFAULT 0,
  `can_view_team_data` tinyint(1) DEFAULT 0,
  `can_edit_data` tinyint(1) DEFAULT 0,
  `can_manage_roles` tinyint(1) DEFAULT 0,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `can_view_own_data`, `can_view_team_data`, `can_edit_data`, `can_manage_roles`, `role_id`) VALUES
(1, 1, 1, 1, 1, 1),
(2, 1, 1, 1, 0, 2),
(3, 1, 0, 0, 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `position_id` int(11) NOT NULL,
  `position_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`position_id`, `position_name`) VALUES
(19, 'Admin Specialist'),
(18, 'Architectural Designer'),
(5, 'Civil Engineer'),
(14, 'Driver'),
(11, 'Electrician'),
(9, 'Field Coordinator'),
(3, 'Foreman'),
(17, 'Helper'),
(8, 'HR Officer'),
(20, 'HR Specialist'),
(2, 'HR/Admin Manager'),
(7, 'Laborer'),
(4, 'Leadman'),
(12, 'Mason'),
(6, 'Mechanical Engineer'),
(1, 'Owner'),
(15, 'Project Engineer'),
(16, 'Safety Officer'),
(13, 'Surveyor'),
(10, 'Warehouse Man');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_name`) VALUES
('Jollibee'),
('Kenny Rogers');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(2, 'Admin'),
(3, 'Employee'),
(1, 'Super Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtr`
--
ALTER TABLE `dtr`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

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
  ADD KEY `fk_project_name` (`project_name`),
  ADD KEY `fk_role` (`role_id`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`leave_id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `employee_number` (`employee_number`),
  ADD KEY `position_id` (`position_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `total_hrs` (`total_hrs`),
  ADD KEY `other_ot` (`other_ot`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD KEY `fk_permission_role` (`role_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`position_id`),
  ADD UNIQUE KEY `position_name` (`position_name`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtr`
--
ALTER TABLE `dtr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dtr`
--
ALTER TABLE `dtr`
  ADD CONSTRAINT `fk_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_position` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`),
  ADD CONSTRAINT `fk_project_name` FOREIGN KEY (`project_name`) REFERENCES `projects` (`project_name`),
  ADD CONSTRAINT `fk_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `fk_employee_leave` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `payroll_ibfk_2` FOREIGN KEY (`employee_number`) REFERENCES `employees` (`employee_number`),
  ADD CONSTRAINT `payroll_ibfk_3` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`),
  ADD CONSTRAINT `payroll_ibfk_4` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `fk_permission_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
