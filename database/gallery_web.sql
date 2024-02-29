-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 29, 2024 at 11:48 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gallery_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE `albums` (
  `albumID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `thumbnail_album` varchar(255) NOT NULL DEFAULT 'album_default.svg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `albums`
--

INSERT INTO `albums` (`albumID`, `userID`, `title`, `description`, `createdAt`, `thumbnail_album`) VALUES
(1, 1, 'First Album', '', '2024-02-21 13:03:46', 'album_default.jpg'),
(2, 3, 'Album ke 1', 'Test 1 edit', '2024-02-22 08:41:31', '1708479145_items-11.jpg'),
(4, 1, 'JKT Member', '', '2024-02-26 01:41:32', 'album_default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `commentID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `photoID` int(11) DEFAULT NULL,
  `commentText` text DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`commentID`, `userID`, `photoID`, `commentText`, `createdAt`) VALUES
(3, 3, 4, 'test 1 edit', '2024-02-22 08:35:36'),
(15, 5, 29, 'akwokwok edit', '2024-02-28 09:19:48');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `likeID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `photoID` int(11) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`likeID`, `userID`, `photoID`, `createdAt`) VALUES
(2, 1, 26, '2024-02-27 05:21:07');

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE `photos` (
  `photoID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `albumID` int(11) DEFAULT 0,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `photos`
--

INSERT INTO `photos` (`photoID`, `userID`, `albumID`, `title`, `description`, `image_path`, `createdAt`, `category`) VALUES
(4, 1, NULL, 'Yoimiya', '', '1708522392_items-1.jpg', '2024-02-21 13:33:12', ''),
(13, 1, NULL, 'Test gtw keberapa', 'akowkwok', '1708868938_1708479145_items-11.jpg', '2024-02-25 13:48:58', ''),
(14, 1, NULL, 'Test gtw keberapa', 'akowkwok', '1708868938_1708522392_items-1.jpg', '2024-02-25 13:48:58', ''),
(15, 1, NULL, 'Test testo', 'dafadfa', '1708868963_1708479145_items-11.jpg', '2024-02-25 13:49:23', ''),
(16, 1, NULL, 'Test testo', 'dafadfa', '1708868963_1708519400_items-6.jpg', '2024-02-25 13:49:23', ''),
(17, 1, NULL, 'Test testo', 'dafadfa', '1708868963_1708519933_items-4.png', '2024-02-25 13:49:23', ''),
(18, 1, 1, NULL, NULL, '1708869439_1708479145_items-11.jpg', '2024-02-25 13:57:19', ''),
(20, 1, 1, NULL, NULL, '1708869439_1708519933_items-4.png', '2024-02-25 13:57:19', ''),
(21, 1, 4, NULL, NULL, '1708912114_jessica_chandra.jpg', '2024-02-26 01:48:34', ''),
(22, 1, 4, NULL, NULL, '1708912114_azizi_asadel.jpg', '2024-02-26 01:48:34', ''),
(23, 1, 4, NULL, NULL, '1708912114_amanda_sukma.jpg', '2024-02-26 01:48:34', ''),
(24, 1, 4, NULL, NULL, '1708912114_marsha_lenathea.jpg', '2024-02-26 01:48:34', ''),
(25, 1, 4, NULL, NULL, '1708912114_freya_jayawardana.jpg', '2024-02-26 01:48:34', ''),
(26, 1, NULL, 'test report', '', '1709003739_items-8.jpg', '2024-02-27 03:15:39', ''),
(28, 1, NULL, 'maomao', '', '1709085152_1708861458_items-9.jpg', '2024-02-28 02:15:00', 'Idol'),
(29, 1, 1, 'Waifu', 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Hic voluptates qui minus explicabo doloremque sed reiciendis ratione id inventore quasi!', '1709087479_items-3.jpg', '2024-02-28 02:35:37', 'Game'),
(30, 1, 1, 'Waifu', 'mehweewh', '1709087479_items-4.png', '2024-02-28 02:31:19', 'Game'),
(31, 1, 1, 'Waifu', 'mehweewh', '1709087479_items-5.jpg', '2024-02-28 02:31:19', 'Game');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `reportID` int(11) NOT NULL,
  `reportType` varchar(50) NOT NULL,
  `photoID` int(11) DEFAULT NULL,
  `reason` varchar(255) NOT NULL,
  `additionalInfo` text DEFAULT NULL,
  `reportedBy` int(11) NOT NULL,
  `reportedUser` int(11) NOT NULL,
  `reportedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`reportID`, `reportType`, `photoID`, `reason`, `additionalInfo`, `reportedBy`, `reportedUser`, `reportedAt`) VALUES
(6, 'spam', 26, 'too cute', '', 1, 0, '2024-02-27 11:56:00'),
(7, 'spam', 26, 'too cute', '', 1, 0, '2024-02-27 11:57:23'),
(8, 'spam', 26, 'apalah', '', 2, 0, '2024-02-27 12:00:36'),
(9, 'spam', 26, 'wkkw', '', 2, 1, '2024-02-27 12:02:59'),
(13, 'nudity', 29, 'too hot', '', 1, 1, '2024-02-28 02:38:20');

-- --------------------------------------------------------

--
-- Table structure for table `reports_album`
--

CREATE TABLE `reports_album` (
  `reportID` int(11) NOT NULL,
  `reportType` varchar(50) NOT NULL,
  `albumID` int(11) DEFAULT NULL,
  `reason` varchar(255) NOT NULL,
  `additionalInfo` text DEFAULT NULL,
  `reportedBy` int(11) NOT NULL,
  `reportedUser` int(11) NOT NULL,
  `reportedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reset_password`
--

CREATE TABLE `reset_password` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `reset_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reset_password`
--

INSERT INTO `reset_password` (`id`, `email`, `reset_code`) VALUES
(1, 'ryanyanuar184@gmail.com', '171456');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `access_level` varchar(50) DEFAULT 'user',
  `last_login` datetime DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_photo` varchar(255) DEFAULT 'default_profile.svg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `name`, `username`, `password`, `email`, `access_level`, `last_login`, `createdAt`, `profile_photo`) VALUES
(1, 'Ryan Yanuar Pradana', 'Ryn', '$2y$10$jV/2X2Ft3CoWZ9hJExamIeYii9pEhFnIdGAf9fs.EX.xlTHm6YWqq', 'ryanyanuarpradana@gmai.com', 'super_admin', '2024-02-29 11:22:58', '2024-02-20 17:20:41', '1709085397profile_photo_1.jpg'),
(2, '', 'raihanrei', '$2y$10$xS8NUCRafzGRh2EEP46aO.4NrKh/WdXka7BT1gcGSMS9NV.NNWWc6', 'raihanrei@gmail.com', 'user', '2024-02-27 14:00:42', '2024-02-21 06:42:50', 'default_profile.svg'),
(3, '', 'fxthir', '$2y$10$kR52EIBOS9dqoo1SpXK7J.acVAIvQ.dHeiCtjbeNh5/p5EgI0gSvG', 'muhammadabdulfathir@gmail.com', 'user', '2024-02-29 11:42:26', '2024-02-22 02:33:34', 'default_profile.svg'),
(4, '', 'reynaldi', '$2y$10$qLeyJMP/l2e.ItonRGp1seRZSG1ZtnCvpE07sffkqIXkEISAjVQxa', 'rynldh@gmail.com', 'user', '2024-02-26 03:54:14', '2024-02-25 20:54:14', 'default_profile.svg'),
(5, '', 'Ryan', '$2y$10$h2x.Na0iES1Z.R1iI7C6oezfTFe1hHjNLGttZ9cj6VFulKuT4Z1E.', 'ryanyanuar184@gmail.com', 'user', '2024-02-28 10:49:55', '2024-02-28 03:13:53', 'default_profile.svg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`albumID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`commentID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `photoID` (`photoID`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`likeID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `photoID` (`photoID`);

--
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`photoID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `albumID` (`albumID`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`reportID`),
  ADD KEY `fk_report_user` (`reportedBy`),
  ADD KEY `fk_report_photo` (`photoID`),
  ADD KEY `fk_reported_user` (`reportedUser`);

--
-- Indexes for table `reset_password`
--
ALTER TABLE `reset_password`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `albumID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `commentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `likeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `photoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `reset_password`
--
ALTER TABLE `reset_password`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `albums`
--
ALTER TABLE `albums`
  ADD CONSTRAINT `albums_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`photoID`) REFERENCES `photos` (`photoID`);

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`photoID`) REFERENCES `photos` (`photoID`);

--
-- Constraints for table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  ADD CONSTRAINT `photos_ibfk_2` FOREIGN KEY (`albumID`) REFERENCES `albums` (`albumID`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_report_photo` FOREIGN KEY (`photoID`) REFERENCES `photos` (`photoID`),
  ADD CONSTRAINT `fk_report_user` FOREIGN KEY (`reportedBy`) REFERENCES `users` (`userID`),
  ADD CONSTRAINT `fk_reported_user` FOREIGN KEY (`reportedUser`) REFERENCES `users` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
