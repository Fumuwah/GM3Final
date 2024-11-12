-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 12, 2024 at 09:09 PM
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
-- Table structure for table `payroll`
--

DROP TABLE IF EXISTS `payroll`;
CREATE TABLE IF NOT EXISTS `payroll` (
  `payroll_id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
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
  `totalHrs` int DEFAULT NULL,
  `payroll_period` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `month` int NOT NULL,
  `days` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `year` int NOT NULL,
  PRIMARY KEY (`payroll_id`),
  KEY `employee_number` (`employee_id`),
  KEY `total_hrs` (`total_hrs`),
  KEY `other_ot` (`other_ot`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`payroll_id`, `employee_id`, `allowance`, `monthly`, `daily`, `hourly`, `total_hrs`, `other_ot`, `special_holiday`, `special_leave`, `gross`, `cash_adv`, `total_deduc`, `netpay`, `withhold_tax`, `sss_con`, `philhealth_con`, `pag_ibig_con`, `other_deduc`, `totalHrs`, `payroll_period`, `month`, `days`, `year`) VALUES
(1, 1, 0.00, 25000.00, 961.54, 120.19, 19.00, 3.00, 0.00, 0.00, 2644.18, 0.00, 0.00, 2644.18, 0.00, 0.00, 0.00, 0.00, 0.00, 22, '11/08-14/24', 11, '08-14', 2024),
(2, 2, 0.00, 50.00, 1.92, 0.24, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '11/08-14/24', 11, '08-14', 2024);

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
