-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2024 at 10:13 AM
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
-- Table structure for table `dtr`
--

CREATE TABLE `dtr` (
  `dtr_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `total_hrs` float DEFAULT NULL,
  `other_ot` float DEFAULT NULL,
  `date` date DEFAULT NULL,
  `dtr_status` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dtr`
--

INSERT INTO `dtr` (`dtr_id`, `employee_id`, `day`, `month`, `year`, `time_in`, `time_out`, `total_hrs`, `other_ot`, `date`, `dtr_status`) VALUES
(35, 1, 28, 10, 2024, '19:53:00', '00:54:00', 18.9833, 0, '2024-10-28', NULL),
(36, 2, 28, 10, 2024, '07:00:00', '17:00:00', 10, 2, '2024-10-28', NULL),
(37, 1, NULL, NULL, NULL, '07:00:00', '17:00:00', 10, NULL, '2024-10-29', NULL),
(38, 3, 29, 10, 2024, '07:00:00', '18:00:00', 11, 3, '2024-10-29', NULL),
(39, 2, 29, 10, 2024, '07:00:00', '18:00:00', 11, 3, '2024-10-29', NULL),
(40, 4, 29, 10, 2024, '07:10:00', '18:00:00', 10.8333, 2.83, '2024-10-29', NULL),
(41, 5, 29, 10, 2024, '07:00:00', '18:00:00', 11, 3, '2024-10-29', NULL),
(42, 8, 29, 10, 2024, '07:00:00', NULL, 0, NULL, '2024-10-29', NULL),
(43, 9, 29, 10, 2024, '07:00:00', NULL, 0, NULL, '2024-10-29', NULL),
(44, 16, 29, 10, 2024, '17:21:00', NULL, 0, NULL, '2024-10-29', NULL),
(45, 7, 29, 10, 2024, '17:21:00', NULL, 0, NULL, '2024-10-29', NULL),
(46, 1, 8, 11, 2024, '08:00:00', '17:00:00', 9, 1, '2024-11-08', NULL),
(47, 1, 9, 11, 2024, '07:00:00', '17:00:00', 10, 2, '2024-11-09', NULL);

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
  `role_id` int(11) DEFAULT NULL,
  `project_name` varchar(100) DEFAULT NULL,
  `last_leave_reset_year` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `employee_number`, `lastname`, `firstname`, `middlename`, `contactno`, `address`, `birthdate`, `civil_status`, `religion`, `basic_salary`, `hire_date`, `employee_status`, `sss_no`, `philhealth_no`, `hdmf_no`, `tin_no`, `email`, `password`, `emergency_contactno`, `created_at`, `updated_at`, `position_id`, `role_id`, `project_name`, `last_leave_reset_year`, `image_path`) VALUES
(1, '001', 'Caragan', 'Justin Kyle', 'O', '09090909099', 'Antipolo Rizal', '2024-09-03', 'Single', 'Catholic', 25000.00, '2024-09-03', 'Regular', '545256', '216548954', '12335135', '124124', 'caraganhr@example.com', '$2y$10$2/1gaZ1IR2tVttOabx/R9OLqD5851USH4dxeylMzSePTcWiB36WLO', '065411004', '2024-09-03 15:09:00', '2024-10-25 09:09:50', 1, 1, 'Jollibee', 2024, ''),
(2, '002', 'Bonifacio', 'Mianah Venice', 'M', '03030303033', 'Binangonan', '2024-09-03', 'Single', 'Christian', 50.00, '2024-09-03', 'Probationary', '562626', '15614658', '164984', '18494', 'bonifaciohr@example.com', '$2y$10$hHM.QVObuMYjF.j961hP3edLZrJn.o5JUV4HThMYJO8nrdwq3u.Na', '022020220202', '2024-09-03 15:21:30', '2024-10-25 09:27:46', 19, 1, 'Kenny Rogers', 2024, ''),
(3, '003', 'Magno', 'Jessy Ville', 'R', '0909090099099', 'Antipolo', '2024-09-04', 'Single', 'Christian', 25.00, '2024-09-04', 'Regular', '645646', '12412412', '35631', '3421', 'magnohr@example.com', '$2y$10$8nokcNH9xbU0wTP65h3LUOXhZwowg7Ao1R3oGAeABrrlBaxeLpKu6', '', '2024-09-04 12:25:17', '2024-10-28 17:35:21', 9, 2, 'McDo', 2024, ''),
(4, '004', 'Francisco', 'Christian Jake', 'F', '020909090909871', 'Taytay', '2024-09-04', 'Single', 'Christian', 25.00, '2024-09-04', 'Regular', '564654', '46546545', '546554', '5412', 'franciscohr@example.com', '$2y$10$rvJeJg9bID9uV5RNDIUmpOE4TXADKugjCg.XCtAdD6918OlO1145e', '', '2024-09-04 13:32:07', '2024-10-25 09:27:46', 10, 2, 'Jollibee', 2024, ''),
(5, '005', 'Ortiz', 'Kristoffer', 'C', '02121021122', 'Antipolo', '2024-09-05', 'Single', 'Catholic', 100.00, '2024-10-25', 'Regular', '641654', '54861251', '2165464', '1234', 'ortizemp@example.com', '$2y$10$2.aRuaCkMTEpcvxK3vwn0OYcA2tWLv8GOtCKcrRHq9im6JxXosixi', '', '2024-09-05 06:37:59', '2024-10-25 10:14:50', 17, 3, 'Kupal', 2024, ''),
(7, 'C', 'C', 'C', 'C', '02', 'C', '2024-09-18', 'Single', 'c', 1.00, '2024-09-18', 'Archived', '1', '1', '1', '1', 'chr@example.com', '$2y$10$ixLqdyPdrBB4C2FHmgdt2OX178gZABxekbVPrCgjvrBWRjXJ8XCtK', '1', '2024-09-18 07:56:57', '2024-10-25 09:27:46', 12, 3, NULL, 2024, ''),
(8, '008', 'Caragan', 'Kyle', 'C', '031012302', 'asdad', '2024-10-01', 'Single', 'catholic', 1000000.00, '2024-10-01', 'Archived', '1231', '124124124', '1251', '12412', 'caraganad@example.com', '$2y$10$1pxOVawT54qqLw1gXeFmEOiPM3RFn7TnYP.rvEftQJ.uoLV2ePdo2', '231321231321', '2024-10-01 14:44:14', '2024-10-25 09:27:46', 17, 3, NULL, 2024, ''),
(9, '09', 'a', 'a', 'a', '1', 'a', '2024-10-03', 'Single', 'a', 1.00, '2024-10-03', 'Regular', '1', '2', '2', '2', 'e@example.com', '$2y$10$Z37VUEn32N/yobcZ4OK1ZeSGW/Y/F9ACtdzEvqepxSdMKpVjY8yR.', '1', '2024-10-02 18:46:02', '2024-10-25 10:10:47', 3, 3, 'Mang Inasal', 2024, ''),
(16, '021', 'V', 'V', 'V', '1', 'V', '2024-10-25', 'Married', 'V', 1.00, '2024-10-25', 'Archived', '1', '1', '1', '1', 'v@example.com', '$2y$10$YeqhwO6AFKFqKfVFhCO2H.P93Qa5RxTJnptTCBB/Tn4D3EJ2xItpK', '', '2024-10-25 14:36:14', '2024-10-25 14:36:22', 18, 2, 'Kenny Rogers', NULL, ''),
(17, '0022', 'Silva', 'Austin', 'K', '0936225156', 'CA', '2024-10-31', 'Single', 'C', 100000.00, '2024-10-31', 'Regular', '1232567891234', '1234567891', '123456789123', '123456789132', 'silva@example.com', '$2y$10$nPurjxOnDB.MYmrYqFRp0.2olGjirqdDbA8Lc6SYlwZ7CJbSMMo8y', '', '2024-10-31 05:14:39', '2024-10-31 05:14:39', 7, 2, 'Kupal', NULL, ''),
(25, '018', 'D', 'D', 'D', '12', 'D', '2024-10-31', 'Single', 'D', 123.00, '2024-10-31', 'Probationary', '1', '1', '1', '1', 'sa@example.com', '$2y$10$ktMfvx2RR.7TuLyPUIKyG.w7rYOrR7Zf7Dkel5OOlGReaD9b.Ojiu', '', '2024-10-31 05:54:57', '2024-10-31 05:54:57', 13, 2, 'Kupal', NULL, ''),
(26, '050', '123', '123', '123', 'asd', '123', '2024-11-04', 'Single', '123', 0.00, '2024-11-04', 'Regular', 'asd', 'asd', 'asd', 'asd', 's@example.com', '$2y$10$wtZy/88vGIkwKTiluh4M1ueD128CuH6Up/BfmGrDQi7v8faLaU9lK', '', '2024-11-04 05:13:20', '2024-11-04 05:13:20', 12, 2, 'Kupal', NULL, '');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`leave_id`, `employee_id`, `sick_leave`, `vacation_leave`, `leave_without_pay`, `used_leave`, `role_id`) VALUES
(1, 1, 0, 6, 10, 8, 1),
(2, 2, 7, 7, 0, 0, 1),
(3, 3, 7, 7, 0, 0, 2),
(4, 4, 7, 7, 0, 0, 2),
(8, 5, 6, 6, 0, 0, 3),
(9, 7, 6, 6, 0, 0, 3),
(10, 8, 6, 6, 0, 0, 3),
(11, 9, 6, 6, 0, 0, 3),
(15, 16, 7, 7, 0, 0, 2),
(16, 17, 7, 7, 0, 0, 2),
(17, 25, 7, 7, 0, 0, 2),
(18, 26, 7, 7, 0, 0, 2);

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
  `status` enum('Pending','Approved','Declined') DEFAULT 'Pending',
  `reason` text DEFAULT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `leave_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`request_id`, `employee_id`, `leave_type`, `start_date`, `end_date`, `status`, `reason`, `request_date`, `leave_id`) VALUES
(13, 1, 'Sick Leave', '2024-10-30', '2024-10-31', 'Approved', 'Surgery', '2024-10-29 17:06:39', NULL),
(14, 2, 'Sick Leave', '2024-10-30', '2024-10-31', '', 'Surgery', '2024-10-29 21:13:47', NULL),
(17, 1, 'Vacation Leave', '2024-10-31', '2024-11-07', 'Approved', 'Palawan', '2024-10-31 10:34:19', NULL),
(18, 1, 'Sick Leave', '2024-10-31', '2024-11-06', 'Approved', 'Surgery', '2024-10-31 10:56:14', NULL),
(19, 1, 'Vacation Leave', '2024-11-04', '2024-11-09', '', 'Palawan', '2024-11-04 05:28:20', NULL),
(20, 1, 'Vacation Leave', '2024-11-04', '2024-11-06', 'Declined', 'Boracay', '2024-11-04 05:31:28', NULL),
(21, 1, 'Vacation Leave', '2024-11-04', '2024-11-04', 'Approved', 'Sinat', '2024-11-04 05:33:47', NULL),
(22, 1, 'Vacation Leave', '2024-11-04', '2024-11-04', 'Declined', 'walwal', '2024-11-04 05:36:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `request_type` enum('leave_request','profile_change') DEFAULT NULL,
  `request_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `payroll_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `allowance` decimal(10,2) DEFAULT NULL,
  `monthly` decimal(10,2) DEFAULT NULL,
  `daily` decimal(10,2) DEFAULT NULL,
  `hourly` decimal(10,2) DEFAULT NULL,
  `total_hrs` decimal(10,2) DEFAULT NULL,
  `other_ot` decimal(10,2) DEFAULT NULL,
  `special_holiday` decimal(10,2) DEFAULT NULL,
  `special_leave` decimal(10,2) DEFAULT NULL,
  `gross` decimal(10,2) DEFAULT NULL,
  `cash_adv` decimal(10,2) DEFAULT NULL,
  `total_deduc` decimal(10,2) DEFAULT NULL,
  `netpay` decimal(10,2) DEFAULT NULL,
  `withhold_tax` decimal(10,2) DEFAULT NULL,
  `sss_con` decimal(10,2) DEFAULT NULL,
  `philhealth_con` decimal(10,2) DEFAULT NULL,
  `pag_ibig_con` decimal(10,2) DEFAULT NULL,
  `other_deduc` decimal(10,2) DEFAULT NULL,
  `totalHrs` int(11) DEFAULT NULL,
  `payroll_period` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`payroll_id`, `employee_id`, `allowance`, `monthly`, `daily`, `hourly`, `total_hrs`, `other_ot`, `special_holiday`, `special_leave`, `gross`, `cash_adv`, `total_deduc`, `netpay`, `withhold_tax`, `sss_con`, `philhealth_con`, `pag_ibig_con`, `other_deduc`, `totalHrs`, `payroll_period`) VALUES
(1, 1, 0.00, 25000.00, 961.54, 120.19, 19.00, 3.00, 0.00, 0.00, 2644.18, 0.00, 0.00, 2644.18, 0.00, 0.00, 0.00, 0.00, 0.00, 22, '11/08-14/24'),
(2, 2, 0.00, 50.00, 1.92, 0.24, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '11/08-14/24');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `position_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`position_id`, `position_name`) VALUES
(1, 'Admin Specialist'),
(2, 'Architectural Designer'),
(3, 'Civil Engineer'),
(4, 'Driver'),
(5, 'Electrician'),
(6, 'Field Coordinator'),
(7, 'Foreman'),
(8, 'HR/Admin Manager'),
(9, 'HR Officer'),
(10, 'Helper'),
(11, 'Laborer'),
(12, 'Leadman'),
(13, 'Mechanical Engineer'),
(14, 'Mason'),
(15, 'Project Engineer'),
(16, 'Safety Officer'),
(17, 'Surveyor'),
(18, 'Warehouse Man'),
(19, 'Owner'),
(20, 'HR Specialist');

-- --------------------------------------------------------

--
-- Table structure for table `profile_change_requests`
--

CREATE TABLE `profile_change_requests` (
  `request_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `request_type` enum('password','contactno','address') DEFAULT NULL,
  `old_data` text DEFAULT NULL,
  `new_data` text DEFAULT NULL,
  `status` enum('pending','approved','declined') DEFAULT 'pending',
  `request_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profile_change_requests`
--

INSERT INTO `profile_change_requests` (`request_id`, `employee_id`, `request_type`, `old_data`, `new_data`, `status`, `request_date`) VALUES
(1, NULL, 'password', NULL, '123654', 'pending', '2024-11-06 20:35:52'),
(2, NULL, 'contactno', NULL, '09586758493', 'pending', '2024-11-06 20:35:52'),
(3, NULL, 'address', NULL, 'Antipolo Rizal BLK 15 LOT 13 DALIG ', 'pending', '2024-11-06 20:35:52'),
(4, NULL, 'password', NULL, '123654', 'pending', '2024-11-06 20:36:37'),
(5, NULL, 'contactno', NULL, '09586758493', 'pending', '2024-11-06 20:36:37'),
(6, NULL, 'address', NULL, 'Antipolo Rizal BLK 15 LOT 13 DALIG ', 'pending', '2024-11-06 20:36:37');

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
('Coron'),
('Jollibee'),
('Kenny Rogers'),
('Kupal'),
('Mang Inasal'),
('McDo'),
('Wendy\'s');

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
  ADD PRIMARY KEY (`dtr_id`),
  ADD KEY `fk_employee` (`employee_id`);

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
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `fk_leave_id` (`leave_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`payroll_id`),
  ADD KEY `employee_number` (`employee_id`),
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
  ADD PRIMARY KEY (`position_id`);

--
-- Indexes for table `profile_change_requests`
--
ALTER TABLE `profile_change_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `employee_id` (`employee_id`);

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
  MODIFY `dtr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `payroll_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `profile_change_requests`
--
ALTER TABLE `profile_change_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  ADD CONSTRAINT `dtr_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `fk_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`),
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `fk_position` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`),
  ADD CONSTRAINT `fk_project_name` FOREIGN KEY (`project_name`) REFERENCES `projects` (`project_name`);

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `fk_employee_leave` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `fk_leave_id` FOREIGN KEY (`leave_id`) REFERENCES `leaves` (`leave_id`),
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `employee_id_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `fk_permission_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
