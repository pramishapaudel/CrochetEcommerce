-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2024 at 03:32 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rideready`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `adminID` int(50) NOT NULL,
  `adminName` varchar(255) NOT NULL,
  `adminNum` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`adminID`, `adminName`, `adminNum`, `password`) VALUES
(1, 'admin1', '9801234567', '12345678');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderID` int(50) NOT NULL,
  `vehicleID` int(50) NOT NULL,
  `userID` int(50) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `fordate` date NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--

-- Table structure for table `products`
--

CREATE TABLE `products` (
  `vehicleID` int(50) NOT NULL,
  `vehicleName` varchar(255) NOT NULL,
  `vehicleDes` varchar(255) NOT NULL,
  `vehicleImg` varchar(255) NOT NULL,
  `price` int(50) NOT NULL,
  `vehicleQuantity` int(50) NOT NULL,
  `vehicleLeft` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`vehicleID`, `vehicleName`, `vehicleDes`, `vehicleImg`, `price`, `vehicleQuantity`, `vehicleLeft`) VALUES
(1, 'Pulsar N160', 'Description Here', './img/6675a5488356c9.39091678.png', 1500, 5, 0),
(2, 'FZ V3', 'Description', 'img/66756a9a04e9e4.03732151.png', 2000, 2, 0),
(4, 'FZ V2', 'test description', 'img/6675a5488356c9.39091678.png', 1000, 8, 0),
(5, 'pulsar 220', 'dest', 'img/66756a9a04e9e4.03732151.png', 1000, 10, 0),
(7, 'duke', 'eraasdf', 'img/Screenshot 2024-03-08 180015.png', 2000, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(50) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `DOB` date NOT NULL,
  `Gender` varchar(10) NOT NULL,
  `Contact` varchar(10) NOT NULL,
  `Citizenship_no` varchar(100) NOT NULL,
  `License_no` varchar(100) NOT NULL,
  `CitizenshipImg` varchar(255) DEFAULT NULL,
  `LicenseImg` varchar(255) DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `vehicleID` (`vehicleID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`vehicleID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `adminID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `orderID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `vehicleID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`vehicleID`) REFERENCES `products` (`vehicleID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
