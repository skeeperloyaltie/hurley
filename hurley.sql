-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2024 at 10:22 PM
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
  `CustomerID` int(11) NOT NULL,
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
  `role` varchar(255) NOT NULL
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
  `FeedbackID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `FeedbackDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Comments` text DEFAULT NULL,
  `Rating` int(11) DEFAULT NULL CHECK (`Rating` >= 1 and `Rating` <= 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `InventoryID` int(11) NOT NULL,
  `MenuItemID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `LastUpdated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`InventoryID`, `MenuItemID`, `Quantity`, `LastUpdated`) VALUES
(0, 0, 21, '2024-07-20 00:47:18');

-- --------------------------------------------------------

--
-- Table structure for table `menuitems`
--

CREATE TABLE `menuitems` (
  `MenuItemID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Category` varchar(50) DEFAULT NULL,
  `Available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitems`
--

INSERT INTO `menuitems` (`MenuItemID`, `Name`, `Description`, `Price`, `Category`, `Available`) VALUES
(1, 'Cheeseburger', 'A juicy beef patty with cheese, lettuce, and tomato.', 8.99, 'Burgers', 1),
(2, 'Margherita Pizza', 'Classic pizza with tomatoes, mozzarella, and basil.', 12.99, 'Pizzas', 1),
(3, 'Caesar Salad', 'Crisp romaine lettuce with Caesar dressing, croutons, and Parmesan cheese.', 7.49, 'Salads', 1),
(4, 'Spaghetti Carbonara', 'Spaghetti with a creamy egg-based sauce, pancetta, and Parmesan cheese.', 14.99, 'Pasta', 0),
(5, 'Chocolate Cake', 'Rich and moist chocolate cake with chocolate frosting.', 5.99, 'Desserts', 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu_combinations`
--

CREATE TABLE `menu_combinations` (
  `CombinationID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `Items` text NOT NULL,
  `Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_combinations`
--

INSERT INTO `menu_combinations` (`CombinationID`, `Name`, `Description`, `Items`, `Price`) VALUES
(1, 'Breakfast', 'morning food', '0', 32.00),
(2, 'Breakfast', 'morning food', '0', 32.00);

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `OrderItemID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `MenuItemID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`OrderItemID`, `OrderID`, `MenuItemID`, `Quantity`, `Price`) VALUES
(0, 0, 1, 21, 8.99);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `OrderDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `StaffID` int(11) DEFAULT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `Status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `CustomerID`, `OrderDate`, `StaffID`, `TotalAmount`, `Status`) VALUES
(0, 1, '2024-07-22 11:51:55', NULL, 188.79, 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `PaymentID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `PaymentDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Amount` decimal(10,2) NOT NULL,
  `PaymentMethod` varchar(50) DEFAULT NULL,
  `TransactionID` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`PaymentID`, `OrderID`, `PaymentDate`, `Amount`, `PaymentMethod`, `TransactionID`) VALUES
(0, 0, '2024-07-22 12:03:59', 1233.00, 'Debit Card', '12ASASSA');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `ReservationID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `ReservationDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `NumberOfGuests` int(11) NOT NULL,
  `SpecialRequests` text DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`ReservationID`, `CustomerID`, `ReservationDate`, `NumberOfGuests`, `SpecialRequests`, `Status`) VALUES
(0, 11, '2024-07-22 19:54:57', 23, 'Proposal', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `StaffID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Username` varchar(30) NOT NULL,
  `Role` enum('Admin','Cook','Waiter','Manager') NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PhoneNumber` varchar(15) DEFAULT NULL,
  `HireDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Password` varchar(255) NOT NULL,
  `IsBlacklisted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`StaffID`, `FirstName`, `LastName`, `Username`, `Role`, `Email`, `PhoneNumber`, `HireDate`, `Password`, `IsBlacklisted`) VALUES
(11, 'Hurley', 'Admin', 'admin', 'Admin', 'admin@hurley.com', '1234567890', '2024-07-21 10:39:36', '$2y$10$qsKtkaLOu2cpN6NVGXXzCeQlN/OXJFJ17ZMsxygmdqjiFcI0Z/vre', 0),
(14, 'Julias', 'Ochieng', 'OchiengJ', 'Waiter', 'julias@ochieng', '12345678', '2024-07-21 11:14:54', '$2y$10$PHuTGBNHHNKtg4H2gafa0uXk21GMJxcagL7w4X.49YqoNjYoSlIcy', 0),
(20, 'Mike', 'Okumu', 'MikeO', 'Manager', 'mikeokumu@gmail.com', '0723456789', '2024-07-22 19:48:40', '$2y$10$Kj6Z.TsPP6g18Z0yy1xEFeBv1sGe0xZCTxF7iFtsa4JkDTr8ewdkW', 0);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TransactionID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `StaffID` int(11) NOT NULL,
  `TransactionDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `TransactionAmount` decimal(10,2) NOT NULL,
  `TransactionType` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`CustomerID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`FeedbackID`),
  ADD KEY `CustomerID` (`CustomerID`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`InventoryID`),
  ADD KEY `MenuItemID` (`MenuItemID`);

--
-- Indexes for table `menuitems`
--
ALTER TABLE `menuitems`
  ADD PRIMARY KEY (`MenuItemID`);

--
-- Indexes for table `menu_combinations`
--
ALTER TABLE `menu_combinations`
  ADD PRIMARY KEY (`CombinationID`);

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`OrderItemID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `MenuItemID` (`MenuItemID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `StaffID` (`StaffID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `OrderID` (`OrderID`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`ReservationID`),
  ADD KEY `CustomerID` (`CustomerID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`StaffID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu_combinations`
--
ALTER TABLE `menu_combinations`
  MODIFY `CombinationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `StaffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
