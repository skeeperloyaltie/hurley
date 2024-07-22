-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2024 at 11:29 PM
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
-- Database: `hurley`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `CustomerID` int(11) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `PhoneNumber` varchar(15) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(50) DEFAULT NULL,
  `State` varchar(50) DEFAULT NULL,
  `ZipCode` varchar(10) DEFAULT NULL,
  `RegistrationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(255) NOT NULL,
  PRIMARY KEY (`CustomerID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`CustomerID`, `FirstName`, `LastName`, `Email`, `Username`, `Password`, `PhoneNumber`, `Address`, `City`, `State`, `ZipCode`, `RegistrationDate`, `role`) VALUES
(1, 'Skeeper', 'Loyaltie', 'skeepertech@gmail.com', 'skeepertech@gmail.com', '$2y$10$4izxi9v9HtYnKf2pzsmU9es7U4tEV69SKuQ8Zs8k9FXEYD.ndy49G', '0702940509', '154 00625', 'Add', 'nasjaks', '2121', '2024-07-21 00:38:09', 'customer'),
(2, 'Martin ', 'Ling', 'martinkingojungo@gmail.com', 'Guy084', '$2y$10$rOWTTVSdaJUwMizOGCHDv.8AkGHw/B8tjF2E1aVrdlDcJcKjt01Em', '0748998816', '33334', 'Nairobi', 'Kenya', '+254', '2024-07-22 08:57:37', 'customer'),
(3, 'babe', 'babe', 'babe@gmaiil.com', 'babe@gmail.com', '$2y$10$cqSL828lLpE.GEn6NwjMd.epZIbJH46eUTI/C3zyQC1hHcInL4Buy', '07434334', '145 k', 'Nairobi', 'Nairobi', 'Nairobi', '2024-07-22 14:41:28', 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `FeedbackID` int(11) NOT NULL AUTO_INCREMENT,
  `CustomerID` int(11) NOT NULL,
  `FeedbackDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Comments` text DEFAULT NULL,
  `Rating` int(11) DEFAULT NULL CHECK (`Rating` >= 1 and `Rating` <= 5),
  PRIMARY KEY (`FeedbackID`),
  KEY `CustomerID` (`CustomerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `InventoryID` int(11) NOT NULL AUTO_INCREMENT,
  `MenuItemID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `LastUpdated` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`InventoryID`),
  KEY `MenuItemID` (`MenuItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menuitems`
--

CREATE TABLE `menuitems` (
  `MenuItemID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Category` varchar(50) DEFAULT NULL,
  `Available` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`MenuItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitems`
--

INSERT INTO `menuitems` (`MenuItemID`, `Name`, `Description`, `Price`, `Category`, `Available`) VALUES
(1, 'Mandazi', 'Mandazi - breakfast', 150.00, 'Breakfast', 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu_combinations`
--

CREATE TABLE `menu_combinations` (
  `CombinationID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `Items` text NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`CombinationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `OrderItemID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` int(11) NOT NULL,
  `MenuItemID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`OrderItemID`),
  KEY `OrderID` (`OrderID`),
  KEY `MenuItemID` (`MenuItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL AUTO_INCREMENT,
  `CustomerID` int(11) NOT NULL,
  `OrderDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `StaffID` int(11) DEFAULT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `Status` varchar(50) DEFAULT 'Pending',
  PRIMARY KEY (`OrderID`),
  KEY `CustomerID` (`CustomerID`),
  KEY `StaffID` (`StaffID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `CustomerID`, `OrderDate`, `StaffID`, `TotalAmount`, `Status`) VALUES
(1, 1, '2024-07-22 11:51:55', NULL, 188.79, 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `PaymentID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` int(11) NOT NULL,
  `PaymentDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Amount` decimal(10,2) NOT NULL,
  `PaymentMethod` varchar(50) DEFAULT NULL,
  `TransactionID` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`PaymentID`),
  KEY `OrderID` (`OrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`PaymentID`, `OrderID`, `PaymentDate`, `Amount`, `PaymentMethod`, `TransactionID`) VALUES
(1, 1, '2024-07-22 12:03:59', 1233.00, 'Debit Card', '12ASASSA');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `ReservationID` int(11) NOT NULL AUTO_INCREMENT,
  `CustomerID` int(11) NOT NULL,
  `ReservationDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `NumberOfGuests` int(11) NOT NULL,
  `SpecialRequests` text DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'Pending',
  PRIMARY KEY (`ReservationID`),
  KEY `CustomerID` (`CustomerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `StaffID` int(11) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Username` varchar(30) NOT NULL,
  `Role` enum('Admin','Cook','Waiter','Manager') NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PhoneNumber` varchar(15) DEFAULT NULL,
  `HireDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Password` varchar(255) NOT NULL,
  `IsBlacklisted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`StaffID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`StaffID`, `FirstName`, `LastName`, `Username`, `Role`, `Email`, `PhoneNumber`, `HireDate`, `Password`, `IsBlacklisted`) VALUES
(1, 'Hurley', 'Admin', 'admin', 'Admin', 'admin@hurley.com', '1234567890', '2024-07-21 10:39:36', '$2y$10$qsKtkaLOu2cpN6NVGXXzCeQlN/OXJFJ17ZMsxygmdqjiFcI0Z/vre', 0),
(2, 'Julias', 'Ochieng', 'OchiengJ', 'Waiter', 'julias@ochieng', '12345678', '2024-07-21 11:14:54', '$2y$10$PHuTGBNHHNKtg4H2gafa0uXk21GMJxcagL7w4X.49YqoNjYoSlIcy', 0),
(3, 'Mike', 'Okumu', 'MikeO', 'Manager', 'mikeokumu@gmail.com', '0723456789', '2024-07-22 19:48:40', '$2y$10$Kj6Z.TsPP6g18Z0yy1xEFeBv1sGe0xZCTxF7iFtsa4JkDTr8ewdkW', 0),
(4, 'Admin', 'User', 'admin', 'Admin', 'admin@hurley.com', '1234567890', '2024-07-22 20:34:30', '$2y$10$PHp1E51YKyyR2ooSOXRsqeqrmc3qarsu5lnlJs0SlaswTgK.EoZ5m', 0);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TransactionID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` int(11) NOT NULL,
  `StaffID` int(11) NOT NULL,
  `TransactionDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `TransactionAmount` decimal(10,2) NOT NULL,
  `TransactionType` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`TransactionID`),
  KEY `OrderID` (`OrderID`),
  KEY `StaffID` (`StaffID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
