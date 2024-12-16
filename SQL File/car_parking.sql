-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Dec 16, 2024 at 01:42 PM
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
-- Database: `car_parking`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `ID` int(10) NOT NULL,
  `AdminName` varchar(120) DEFAULT NULL,
  `UserName` varchar(120) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Password` varchar(120) DEFAULT NULL,
  `AdminRegdate` timestamp NULL DEFAULT current_timestamp(),
  `OTP` varchar(20) DEFAULT NULL,
  `OTPExpiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`ID`, `AdminName`, `UserName`, `MobileNumber`, `Email`, `Password`, `AdminRegdate`, `OTP`, `OTPExpiry`) VALUES
(1, 'Admin', 'admin', 7898799798, 'hainguyen.06102003@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2024-05-01 05:38:23', '228635', '2024-11-25 15:19:08');

-- --------------------------------------------------------

--
-- Table structure for table `tblbill`
--

CREATE TABLE `tblbill` (
  `ID` int(10) NOT NULL,
  `VehicleID` int(11) NOT NULL,
  `OwnerID` int(11) NOT NULL,
  `Amount` int(11) NOT NULL,
  `PaymentDate` int(11) NOT NULL,
  `PaymentMethod` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  `Note` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `ID` int(10) NOT NULL,
  `VehicleCat` varchar(120) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`ID`, `VehicleCat`, `CreationDate`) VALUES
(1, 'Four Wheeler Vehicle', '2024-05-03 11:06:50'),
(2, 'Two Wheeler Vehicle', '2024-05-03 11:06:50'),
(4, 'Bicycles', '2024-05-03 11:06:50'),
(6, 'Electric Vehicle', '2024-08-16 06:41:40');

-- --------------------------------------------------------

--
-- Table structure for table `tblregusers`
--

CREATE TABLE `tblregusers` (
  `ID` int(5) NOT NULL,
  `FullName` varchar(250) DEFAULT NULL,
  `Birth` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `MobileNumber` bigint(10) DEFAULT NULL,
  `CCCD` varchar(20) NOT NULL,
  `Email` varchar(250) DEFAULT NULL,
  `Password` varchar(250) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp(),
  `OTP` varchar(6) DEFAULT NULL,
  `OTPExpiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tblregusers`
--

INSERT INTO `tblregusers` (`ID`, `FullName`, `Birth`, `MobileNumber`, `CCCD`, `Email`, `Password`, `RegDate`, `OTP`, `OTPExpiry`) VALUES
(2, 'Võ Nguyên', '2024-11-25 12:25:37', 1234567890, '052209000000', 'wwwnguyendoan123@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2024-06-01 18:05:56', '960235', '2024-11-25 04:40:10'),
(3, 'Ngô Mạnh Tường', '2024-11-25 12:25:23', 987654321, '030056784521', 'Tuongmanhngo231@gmail.com', '25f9e794323b453885f5181f1b624d0b', '2024-11-25 08:23:34', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblschedule`
--

CREATE TABLE `tblschedule` (
  `ID` int(10) NOT NULL,
  `OwnerID` int(10) NOT NULL,
  `CategoryID` int(10) NOT NULL,
  `PhoneNum` char(10) NOT NULL,
  `CCCD` char(30) NOT NULL,
  `RegistrationNumber` varchar(20) NOT NULL,
  `DateSchedule` date NOT NULL,
  `ExpectDateOut` date DEFAULT NULL,
  `Status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblschedule`
--

INSERT INTO `tblschedule` (`ID`, `OwnerID`, `CategoryID`, `PhoneNum`, `CCCD`, `RegistrationNumber`, `DateSchedule`, `ExpectDateOut`, `Status`) VALUES
(2, 2, 1, '', '', '77AD-05598', '2024-11-23', '2024-11-30', 'Done'),
(3, 2, 1, '', '', '77AE-12345', '2024-11-01', '2024-12-08', 'Done');

-- --------------------------------------------------------

--
-- Table structure for table `tblschedule_unregistered`
--

CREATE TABLE `tblschedule_unregistered` (
  `ID` int(11) NOT NULL,
  `OwnerName` varchar(255) NOT NULL,
  `CCCD` varchar(30) NOT NULL,
  `PhoneNumber` char(10) NOT NULL,
  `VehicleCategory` varchar(30) NOT NULL,
  `RegistrationNumber` varchar(10) NOT NULL,
  `DateSchedule` date NOT NULL,
  `ExpectDateOut` date NOT NULL,
  `Status` varchar(10) NOT NULL,
  `ActionBy` int(11) NOT NULL,
  `Note` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblvehicle`
--

CREATE TABLE `tblvehicle` (
  `ID` int(11) NOT NULL,
  `OwnerID` int(10) NOT NULL,
  `RegistrationNumber` varchar(15) NOT NULL,
  `CategoryID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblvehicle`
--

INSERT INTO `tblvehicle` (`ID`, `OwnerID`, `RegistrationNumber`, `CategoryID`) VALUES
(1, 2, '77AD-05598', 2),
(2, 3, '77F1-75192', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblvehiclelogs`
--

CREATE TABLE `tblvehiclelogs` (
  `ID` int(10) NOT NULL,
  `ParkingNumber` varchar(120) DEFAULT NULL,
  `RegistrationNumber` varchar(120) DEFAULT NULL,
  `VehicleID` int(10) DEFAULT NULL,
  `InTime` timestamp NULL DEFAULT current_timestamp(),
  `OutTime` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `ParkingCharge` varchar(120) NOT NULL,
  `Remark` mediumtext NOT NULL,
  `Status` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblvehiclelogs`
--

INSERT INTO `tblvehiclelogs` (`ID`, `ParkingNumber`, `RegistrationNumber`, `VehicleID`, `InTime`, `OutTime`, `ParkingCharge`, `Remark`, `Status`) VALUES
(1, '125061388', '77AD-05598', 1, '2024-08-16 06:42:36', '2024-11-26 04:54:28', '50000', 'NA', 'Out'),
(2, '787303637', '77AD-05598', 1, '2024-08-16 06:47:23', '2024-11-20 01:27:57', '20000', 'NA', 'Out'),
(3, '901288727', '77AD-05598', 1, '2024-08-16 06:58:34', '2024-11-23 02:27:37', '50000', 'NA', 'Out'),
(18, '223423', '77AD-05598', 1, '2024-11-16 06:58:32', '2024-11-16 10:58:31', '20000', '', 'Out'),
(20, NULL, '29L1-43883', NULL, '2024-11-23 04:14:10', NULL, '', '', 'In'),
(21, NULL, '77F1-75192', 2, '2024-11-25 09:12:21', '2024-11-26 04:34:21', '2000', '', 'Out'),
(23, NULL, '77F1-75192', 2, '2024-11-25 09:17:48', '2024-11-26 04:34:19', '2000', '', 'Out'),
(24, NULL, '77F1-75192', 2, '2024-11-25 13:33:44', '2024-11-26 04:34:17', '2000', '', 'Out'),
(25, NULL, '77F1-75192', 2, '2024-11-25 13:34:16', '2024-11-26 04:34:14', '2000', '', 'Out'),
(26, NULL, '77F1-75192', 2, '2024-11-25 13:42:41', '2024-11-26 04:34:12', '2000', '', 'Out'),
(27, NULL, '77F1-75192', 2, '2024-11-26 04:33:27', '2024-11-26 04:33:32', '2000', '', 'Out'),
(28, NULL, '77F1-75192', 2, '2024-11-26 05:33:49', '2024-11-26 05:33:54', '2000', '', 'Out'),
(29, NULL, '77F1-75192', 2, '2024-11-26 07:32:59', '2024-11-26 07:33:34', '2000', '', 'Out');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblbill`
--
ALTER TABLE `tblbill`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `VehicleCat` (`VehicleCat`);

--
-- Indexes for table `tblregusers`
--
ALTER TABLE `tblregusers`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `MobileNumber` (`MobileNumber`);

--
-- Indexes for table `tblschedule`
--
ALTER TABLE `tblschedule`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblschedule_unregistered`
--
ALTER TABLE `tblschedule_unregistered`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblvehicle`
--
ALTER TABLE `tblvehicle`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblvehiclelogs`
--
ALTER TABLE `tblvehiclelogs`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblbill`
--
ALTER TABLE `tblbill`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblregusers`
--
ALTER TABLE `tblregusers`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblschedule`
--
ALTER TABLE `tblschedule`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblschedule_unregistered`
--
ALTER TABLE `tblschedule_unregistered`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblvehicle`
--
ALTER TABLE `tblvehicle`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblvehiclelogs`
--
ALTER TABLE `tblvehiclelogs`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
