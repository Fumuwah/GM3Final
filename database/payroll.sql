-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 26, 2024 at 10:36 AM
-- Server version: 10.11.10-MariaDB
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
  `payroll_period` varchar(255) DEFAULT NULL,
  `month` int(11) NOT NULL,
  `days` varchar(255) NOT NULL,
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`payroll_id`, `employee_id`, `allowance`, `monthly`, `daily`, `hourly`, `total_hrs`, `other_ot`, `special_holiday`, `special_leave`, `gross`, `cash_adv`, `total_deduc`, `netpay`, `withhold_tax`, `sss_con`, `philhealth_con`, `pag_ibig_con`, `other_deduc`, `totalHrs`, `payroll_period`, `month`, `days`, `year`) VALUES
(1, 1, 0.00, 25000.00, 961.54, 120.19, 19.00, 3.00, 0.00, 0.00, 2644.18, 0.00, 0.00, 2644.18, 0.00, 0.00, 0.00, 0.00, 0.00, 22, '11/08-14/24', 11, '08-14', 2024),
(2, 2, 0.00, 50.00, 1.92, 0.24, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '11/08-14/24', 11, '08-14', 2024),
(3, 1, 0.00, 99999999.00, 3846153.81, 480769.23, 48.00, 0.00, 0.00, 0.00, 23076923.04, 0.00, 0.00, 23076923.04, 0.00, 0.00, 0.00, 0.00, 0.00, 48, '11/15-21/24', 11, '15-21', 2024),
(4, 32, 0.00, 14500.00, 557.69, 69.71, 19.00, 0.00, 0.00, 0.00, 1324.49, 0.00, 0.00, 1324.49, 0.00, 0.00, 0.00, 0.00, 0.00, 19, '11/11-13/24', 11, '11-13', 2024),
(5, 5, 0.00, 1000.00, 41.67, 5.21, 17.23, 3.23, 0.00, 0.00, 106.65, 0.00, 0.00, 106.65, 0.00, 0.00, 0.00, 0.00, 0.00, 20, '11/11-12/24', 11, '11-12', 2024),
(6, 2, 0.00, 18500.00, 711.54, 88.94, 18.00, 0.00, 0.00, 0.00, 1600.92, 0.00, 0.00, 1600.92, 0.00, 0.00, 0.00, 0.00, 0.00, 18, '11/11-25/24', 11, '11-25', 2024);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`payroll_id`),
  ADD KEY `employee_number` (`employee_id`),
  ADD KEY `total_hrs` (`total_hrs`),
  ADD KEY `other_ot` (`other_ot`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `payroll_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `employee_id_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
