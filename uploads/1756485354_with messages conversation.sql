-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2025 at 05:55 AM
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
-- Database: `ctunav`
--

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `conversationID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `staffID` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`conversationID`, `userID`, `staffID`, `created_at`, `updated_at`) VALUES
(1, 5, 4, '2025-08-28 00:36:27', '2025-08-28 00:51:54'),
(2, 5, 3, '2025-08-28 00:36:43', '2025-08-28 00:36:43'),
(3, 5, 6, '2025-08-28 08:09:50', '2025-08-28 08:09:50');

-- --------------------------------------------------------

--
-- Table structure for table `departments_and_offices`
--

CREATE TABLE `departments_and_offices` (
  `departmentID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments_and_offices`
--

INSERT INTO `departments_and_offices` (`departmentID`, `name`, `description`, `location`) VALUES
(1, 'Registrar', '', ''),
(2, 'MIS', '', ''),
(3, 'Library', '', ''),
(4, 'Guidance', '', ''),
(5, 'Clinic', '', ''),
(6, 'IT Department', '', ''),
(7, 'Engineering Department', 'The Engineering Department is committed to producing innovative, technically skilled, and socially responsible engineers. ', 'near sb and agri building');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `faqID` int(11) NOT NULL,
  `departmentID` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`faqID`, `departmentID`, `question`, `answer`, `link`) VALUES
(1, 6, 'a', 'a', '');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `messageID` int(11) NOT NULL,
  `conversationID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `body` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`messageID`, `conversationID`, `userID`, `body`, `file_path`, `created_at`, `is_read`, `read_at`) VALUES
(1, 1, 5, 'Hello', NULL, '2025-08-28 00:51:54', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staffID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `fullName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `departmentID` int(11) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staffID`, `userID`, `fullName`, `email`, `departmentID`, `photo`) VALUES
(9, 4, 'Ange La Lai', 'angelai@gmail.com', 6, NULL),
(14, 6, 'a aa aaa', 'a@gmail.com', 1, NULL),
(15, 3, 'angelie Laput Jordao', 'angelie@gmail.com', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `first_name` char(255) NOT NULL,
  `middle_name` char(255) NOT NULL,
  `last_name` char(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('user','staff','admin') NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `role`, `created_at`, `profile_image`, `code`) VALUES
(1, 'Admin', 'admin', 'User', 'admin1@gmail.com', '$2y$10$IUGFZw0McOJyBocD3w6gHOAGMq3GydONaejsmhBKsfVQPeRAsrqMe', 'admin', '2025-07-10 14:05:44', NULL, NULL),
(3, 'angelie', 'Laput', 'Jordao', 'angelie@gmail.com', '$2y$10$.XihWQpuSI9w0MXH/oAHRuzTRsVGYKYI221Hpdyepd0P6dv7N8veq', 'staff', '2025-07-10 20:35:01', 'uploads/1753071382_download (2).jpg', NULL),
(4, 'Angelai', 'La', 'Lai', 'angelai@gmail.com', '$2y$10$w6CFPUWs15jSNpMy5r/Kg.xg6xd6mElw1eNDwGBr/aKcqWJpdpTby', 'staff', '2025-07-19 14:57:00', NULL, NULL),
(5, 'Cristy Jane', 'Banzon', 'Bayo', 'crstyjn@gmail.com', '$2y$10$r82zFTQAKHb6AOB1B1PQ3.pjWOqsWGc75dl3QW7zPC07Kat7JIN4y', 'user', '2025-08-14 17:33:25', NULL, '126724'),
(6, 'a', 'aa', 'aaa', 'a@gmail.com', '$2y$10$89hNQ46qFCmM/iQVsFno0.buB/maPbZD2/oo0w4li9KDqeB3oXQtS', 'user', '2025-08-24 08:43:57', 'uploads/1756098301_1018f07b-6cb5-4ed3-833a-70f5d1ae8124.jpg', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversationID`),
  ADD UNIQUE KEY `uniq_pair` (`userID`,`staffID`),
  ADD KEY `idx_updated` (`updated_at`),
  ADD KEY `fk_conv_staff` (`staffID`);

--
-- Indexes for table `departments_and_offices`
--
ALTER TABLE `departments_and_offices`
  ADD PRIMARY KEY (`departmentID`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`faqID`),
  ADD KEY `fk_department` (`departmentID`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`messageID`),
  ADD KEY `idx_conv_created` (`conversationID`,`created_at`),
  ADD KEY `fk_msg_sender` (`userID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staffID`),
  ADD KEY `departmentID` (`departmentID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `conversationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `departments_and_offices`
--
ALTER TABLE `departments_and_offices`
  MODIFY `departmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `faqID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `messageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `fk_conv_staff` FOREIGN KEY (`staffID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conv_user` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `faqs`
--
ALTER TABLE `faqs`
  ADD CONSTRAINT `fk_department` FOREIGN KEY (`departmentID`) REFERENCES `departments_and_offices` (`departmentID`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_conv` FOREIGN KEY (`conversationID`) REFERENCES `conversations` (`conversationID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`departmentID`) REFERENCES `departments_and_offices` (`departmentID`),
  ADD CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
