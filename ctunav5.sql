-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2025 at 08:56 AM
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
(1, 5, 4, '2025-08-28 00:36:27', '2025-09-12 14:33:47'),
(2, 5, 3, '2025-08-28 00:36:43', '2025-08-28 00:36:43'),
(3, 5, 6, '2025-08-28 08:09:50', '2025-08-28 08:09:50'),
(4, 1, 4, '2025-08-29 14:08:15', '2025-08-29 14:08:15'),
(5, 1, 3, '2025-08-29 14:18:09', '2025-08-29 14:18:09'),
(6, 6, 4, '2025-08-29 15:15:08', '2025-08-29 15:15:08');

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
(1, 'Agriculture Department', '', ''),
(2, 'CAS Department', '', ''),
(3, 'BIT Department', '', ''),
(4, 'BSIT Department', '', ''),
(5, 'BSHM Department', '', ''),
(6, 'Education Department', '', ''),
(7, 'Engineering Department', '', ''),
(8, 'Admission Office', '', ''),
(10, 'Clinic', '', ''),
(11, 'MIS Office', '', ''),
(14, 'Registrar’s Office', '', ''),
(15, 'SAO Office', '', '');

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
(2, 11, 'How to view my COR and MY GRADES?', 'Go to the SIS portal. On the upper left under SIS 3.0 choose the appropriate Semester. After setting the appropriate semester, you can now open View My COR and View My Grades.', 'http://sis.tuburan.ctu.edu.ph/Account/Login?ReturnUrl=/home'),
(4, 11, 'What to do if I forgot my password in SIS Portal?', 'Click Forgot Password in the SIS Login Page and follow further instructions.\r\n\r\nhttp://sis.tuburan.ctu.edu.ph/', 'http://sis.tuburan.ctu.edu.ph/'),
(5, 11, 'Reasons why you can’t log in to the SIS PORTAL', 'Check the URL. Double check the proper capitalization and spacing of your password and email address.', 'http://sis.tuburan.ctu.edu.ph/Account/Login?ReturnUrl=/home'),
(10, 8, 'What documents do I need to apply in the Admission?', 'Form 138/Report Card, Good Moral Certificate, 2x2 Picture.', NULL),
(11, 8, 'Is there an entrance exam or interview?', 'Yes, especially for incoming 1st year students.', NULL),
(12, 8, 'How do I transfer to another Campus / Shift from another Program?', 'Kindly visit the Guidance Office for assistance regarding transferring or shifting.', NULL),
(13, 8, 'When can I apply in the Admission?', 'Just follow the Admission Facebook Page and keep posted. Please visit: CTU-Tuburan and Tabuelan Admissions Office.', NULL),
(14, 14, 'How to Request my Transcript of Records (TOR)', 'For TOR Request, kindly click the link.', 'https://ordir.ctu.edu.ph/registrar/'),
(15, 14, 'What are the requirements for registration/admission of new students?', 'Requirements for admission will be forwarded once you completed the online application.', NULL),
(16, 14, 'When is the deadline for online application?', 'The system automatically closes once the number of applicants for each program is reached.', NULL),
(17, 14, 'What are the requirements for enrollment of new students?', 'You may refer to the admission guidelines.', 'https://www.ctu.edu.ph/admission/'),
(18, 14, 'What are the requirements for enrollment of old students?', 'You may bring your ID and Certificate of Registration (COR).', NULL),
(19, 14, 'Who can avail of the free tuition policy?', 'Only undergraduate students from 1st to 5th year college can avail of the free tuition policy.', NULL),
(20, 14, 'What are the undergraduate courses offered in CTU?', 'You may refer to the list of undergraduate studies.', 'https://www.ctu.edu.ph/undergraduate-studies/'),
(21, 14, 'What are the graduate courses offered in CTU?', 'You may refer to the list of graduate studies.', 'https://www.ctu.edu.ph/graduate-studies/'),
(22, 14, 'What are the requirements in requesting for scholastic documents?', 'Kindly follow the online protocol for this.', NULL),
(23, 14, 'How long does it take for a TOR to be released from the date of request?', 'Releasing of documents will take 7-14 business days.', NULL);

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
(1, 1, 5, 'Hello', NULL, '2025-08-28 00:51:54', 1, '2025-08-30 09:59:36'),
(2, 1, 4, 'Hi, good afternoon. How may i help you?', '', '2025-08-29 13:19:31', 1, '2025-08-29 23:16:40'),
(3, 1, 5, '', 'uploads/1756477894_1018f07b-6cb5-4ed3-833a-70f5d1ae8124.jpg', '2025-08-29 22:31:34', 1, '2025-08-30 09:59:36'),
(4, 1, 5, '', NULL, '2025-08-29 22:31:34', 1, '2025-08-30 09:59:36'),
(5, 1, 4, '', 'uploads/1756478113_1018f07b-6cb5-4ed3-833a-70f5d1ae8124.jpg', '2025-08-29 22:35:13', 1, '2025-08-29 23:16:40'),
(6, 1, 4, 'Hi.', '', '2025-08-29 23:32:27', 1, '2025-08-30 00:03:56'),
(7, 1, 5, 'You can view your COR through sis.tuburan.edu.ph', NULL, '2025-08-30 00:12:12', 1, '2025-08-30 09:59:36'),
(8, 1, 5, 'You can view your COR through sis.tuburan.edu.ph', NULL, '2025-08-30 00:12:12', 1, '2025-08-30 09:59:36'),
(9, 1, 5, '', 'uploads/1756485354_menu final.jpg', '2025-08-30 00:35:54', 1, '2025-08-30 09:59:36'),
(10, 1, 5, '', 'uploads/1756485354_login.php', '2025-08-30 00:35:54', 1, '2025-08-30 09:59:36'),
(11, 1, 5, '', 'uploads/1756485354_with messages conversation.sql', '2025-08-30 00:35:54', 1, '2025-08-30 09:59:36'),
(12, 1, 5, '', NULL, '2025-08-30 00:35:54', 1, '2025-08-30 09:59:36'),
(13, 1, 5, 'Another text', NULL, '2025-08-30 00:50:51', 1, '2025-08-30 09:59:36'),
(14, 1, 5, 'Another text', NULL, '2025-08-30 00:50:51', 1, '2025-08-30 09:59:36'),
(15, 1, 5, '..', NULL, '2025-08-30 00:58:26', 1, '2025-08-30 09:59:36'),
(16, 1, 5, '..', NULL, '2025-08-30 00:58:26', 1, '2025-08-30 09:59:36'),
(17, 1, 4, '.', '', '2025-08-30 09:52:46', 1, '2025-08-30 09:59:17'),
(18, 1, 4, '.', '', '2025-08-30 09:52:57', 1, '2025-08-30 09:59:17'),
(19, 1, 5, '.', NULL, '2025-08-30 09:59:21', 1, '2025-08-30 09:59:36'),
(20, 1, 5, '.', NULL, '2025-08-30 09:59:21', 1, '2025-08-30 09:59:36'),
(21, 1, 5, '.', NULL, '2025-08-30 10:04:49', 1, '2025-08-30 10:04:58'),
(22, 1, 5, '.', NULL, '2025-08-30 10:04:49', 1, '2025-08-30 10:04:58'),
(23, 1, 5, 'hello', NULL, '2025-09-12 08:48:22', 1, '2025-09-12 14:31:17'),
(24, 1, 5, 'hello', NULL, '2025-09-12 08:48:22', 1, '2025-09-12 14:31:17'),
(25, 1, 5, '', 'uploads/1757638125_Book-Output-Based-on-the-Syllabus-Template.docx', '2025-09-12 08:48:45', 1, '2025-09-12 14:31:17'),
(26, 1, 5, '', NULL, '2025-09-12 08:48:45', 1, '2025-09-12 14:31:17'),
(27, 1, 5, 'afdafda', NULL, '2025-09-12 10:54:30', 1, '2025-09-12 14:31:17'),
(28, 1, 5, 'afdafda', NULL, '2025-09-12 10:54:30', 1, '2025-09-12 14:31:17'),
(29, 1, 5, 'nawa', NULL, '2025-09-12 14:33:42', 1, '2025-09-12 14:39:43'),
(30, 1, 5, 'nawa', NULL, '2025-09-12 14:33:42', 1, '2025-09-12 14:39:43'),
(31, 1, 5, 'nawaaa', NULL, '2025-09-12 14:33:47', 1, '2025-09-12 14:39:43'),
(32, 1, 5, 'nawaaa', NULL, '2025-09-12 14:33:47', 1, '2025-09-12 14:39:43');

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
(18, 3, 'Ange Laput Jordao', 'angeliemis@gmail.com', 11, NULL);

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
(3, 'Ange', 'Laput', 'Jordao', 'angeliemis@gmail.com', '$2y$10$.XihWQpuSI9w0MXH/oAHRuzTRsVGYKYI221Hpdyepd0P6dv7N8veq', 'staff', '2025-07-10 20:35:01', 'uploads/1753071382_download (2).jpg', NULL),
(4, 'Angelai', 'La', 'Lai', 'angelai@gmail.com', '$2y$10$w6CFPUWs15jSNpMy5r/Kg.xg6xd6mElw1eNDwGBr/aKcqWJpdpTby', 'staff', '2025-07-19 14:57:00', NULL, NULL),
(5, 'Cristy Jane', 'Banzon', 'Bayo', 'crstyjn@gmail.com', '$2y$10$.G4IU9P857JuPx6CBS7dJ.vn5a/ZRBdSNIZrx64b4u1fJVz.2/9Fq', 'user', '2025-08-14 17:33:25', 'uploads/1756934480_download.jpg', '126724'),
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
ALTER TABLE `faqs` ADD FULLTEXT KEY `question` (`question`);

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
  MODIFY `conversationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `departments_and_offices`
--
ALTER TABLE `departments_and_offices`
  MODIFY `departmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `faqID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `messageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
