-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 07, 2023 at 08:51 AM
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
-- Database: `undp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bootcamp`
--

CREATE TABLE `bootcamp` (
  `bootcamp_name` varchar(20) NOT NULL,
  `location` varchar(20) NOT NULL,
  `cost` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `branch_name` varchar(20) NOT NULL,
  `country` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employeeID` int(11) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `phone_number` int(11) NOT NULL,
  `branch_name` varchar(20) DEFAULT NULL,
  `bootcampname` varchar(20) DEFAULT NULL,
  `secretoryID` int(11) DEFAULT NULL,
  `Password` int(11) NOT NULL DEFAULT 123
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `managerID` int(11) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `branch` varchar(20) DEFAULT NULL,
  `Password` varchar(20) NOT NULL DEFAULT 'hamza'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `secretory`
--

CREATE TABLE `secretory` (
  `secretoryID` int(11) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `phone_number` int(11) NOT NULL,
  `branch_name` varchar(20) DEFAULT NULL,
  `managerID` int(11) DEFAULT NULL,
  `Password` varchar(20) NOT NULL DEFAULT 'hamza'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sponsor`
--

CREATE TABLE `sponsor` (
  `Company_name` varchar(20) NOT NULL,
  `bootcampname` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sponsorship`
--

CREATE TABLE `sponsorship` (
  `Company_name` varchar(20) NOT NULL,
  `budget` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `managerID` int(11) DEFAULT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volunteer`
--

CREATE TABLE `volunteer` (
  `volunteerID` int(11) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `phone_number` int(11) NOT NULL,
  `branch_name` varchar(20) DEFAULT NULL,
  `bootcampname` varchar(20) DEFAULT NULL,
  `secretoryID` int(11) DEFAULT NULL,
  `Password` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteer`
--

INSERT INTO `volunteer` (`volunteerID`, `first_name`, `last_name`, `phone_number`, `branch_name`, `bootcampname`, `secretoryID`, `Password`) VALUES
(1, 'haidar', 'hamdan', 71454545, NULL, NULL, NULL, '$2y$10$HMWEC/yU5ohDSQLUyJcvZ.RI4oBgFZp3Y');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bootcamp`
--
ALTER TABLE `bootcamp`
  ADD PRIMARY KEY (`bootcamp_name`);

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`branch_name`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employeeID`),
  ADD KEY `branch_name` (`branch_name`),
  ADD KEY `secretoryID` (`secretoryID`),
  ADD KEY `bootcampname` (`bootcampname`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`managerID`),
  ADD KEY `branch` (`branch`);

--
-- Indexes for table `secretory`
--
ALTER TABLE `secretory`
  ADD PRIMARY KEY (`secretoryID`),
  ADD KEY `branch_name` (`branch_name`),
  ADD KEY `managerID` (`managerID`);

--
-- Indexes for table `sponsor`
--
ALTER TABLE `sponsor`
  ADD PRIMARY KEY (`Company_name`,`bootcampname`),
  ADD KEY `bootcampname` (`bootcampname`);

--
-- Indexes for table `sponsorship`
--
ALTER TABLE `sponsorship`
  ADD PRIMARY KEY (`Company_name`),
  ADD KEY `managerID` (`managerID`);

--
-- Indexes for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD PRIMARY KEY (`volunteerID`),
  ADD KEY `branch_name` (`branch_name`),
  ADD KEY `secretoryID` (`secretoryID`),
  ADD KEY `bootcampname` (`bootcampname`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employeeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `managerID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `secretory`
--
ALTER TABLE `secretory`
  MODIFY `secretoryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `volunteer`
--
ALTER TABLE `volunteer`
  MODIFY `volunteerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`branch_name`) REFERENCES `branch` (`branch_name`),
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`secretoryID`) REFERENCES `secretory` (`secretoryID`),
  ADD CONSTRAINT `employees_ibfk_3` FOREIGN KEY (`bootcampname`) REFERENCES `bootcamp` (`bootcamp_name`);

--
-- Constraints for table `manager`
--
ALTER TABLE `manager`
  ADD CONSTRAINT `manager_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branch` (`branch_name`);

--
-- Constraints for table `secretory`
--
ALTER TABLE `secretory`
  ADD CONSTRAINT `secretory_ibfk_1` FOREIGN KEY (`branch_name`) REFERENCES `branch` (`branch_name`),
  ADD CONSTRAINT `secretory_ibfk_2` FOREIGN KEY (`managerID`) REFERENCES `manager` (`managerID`);

--
-- Constraints for table `sponsor`
--
ALTER TABLE `sponsor`
  ADD CONSTRAINT `sponsor_ibfk_1` FOREIGN KEY (`bootcampname`) REFERENCES `bootcamp` (`bootcamp_name`),
  ADD CONSTRAINT `sponsor_ibfk_2` FOREIGN KEY (`Company_name`) REFERENCES `sponsorship` (`Company_name`);

--
-- Constraints for table `sponsorship`
--
ALTER TABLE `sponsorship`
  ADD CONSTRAINT `sponsorship_ibfk_1` FOREIGN KEY (`managerID`) REFERENCES `manager` (`managerID`);

--
-- Constraints for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD CONSTRAINT `volunteer_ibfk_1` FOREIGN KEY (`branch_name`) REFERENCES `branch` (`branch_name`),
  ADD CONSTRAINT `volunteer_ibfk_2` FOREIGN KEY (`secretoryID`) REFERENCES `secretory` (`secretoryID`),
  ADD CONSTRAINT `volunteer_ibfk_3` FOREIGN KEY (`bootcampname`) REFERENCES `bootcamp` (`bootcamp_name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
