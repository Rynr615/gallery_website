-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2024 at 12:16 PM
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
  `thumbnail_album` varchar(255) NOT NULL DEFAULT 'album_default.svg',
  `acces_level` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `albums`
--

INSERT INTO `albums` (`albumID`, `userID`, `title`, `description`, `createdAt`, `thumbnail_album`, `acces_level`) VALUES
(1, 1, 'Genshin Impact', 'Game yang baik', '2024-03-07 05:34:30', '1709634209_items-5.jpg', 'private'),
(2, 2, 'Pemandangan', '', '2024-03-06 03:18:07', 'album_default.jpg', 'public'),
(3, 3, 'Animeee', 'Simpenan', '2024-03-07 11:16:08', 'items-17.jpg', 'public'),
(5, 2, 'test', '', '2024-03-06 01:43:40', 'album_default.jpg', 'public');

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
(1, 2, 1, 'Jos gandos', '2024-03-05 10:32:44'),
(2, 1, 7, 'Kiritooo', '2024-03-05 10:37:58'),
(9, 1, 11, 'hu tao👻', '2024-03-06 22:39:37');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `likeID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `photoID` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `category` varchar(255) NOT NULL,
  `acces_level` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `photos`
--

INSERT INTO `photos` (`photoID`, `userID`, `albumID`, `title`, `description`, `image_path`, `createdAt`, `category`, `acces_level`) VALUES
(1, 1, 1, 'Genshin Impek', 'Mantapkan banh', '1709634339_items-1.jpg', '2024-03-06 02:05:32', 'Game', 'public'),
(2, 1, 1, 'Genshin Impek', 'Mantapkan banh', '1709634339_items-2.jpg', '2024-03-06 02:05:32', 'Game', 'public'),
(3, 1, 1, 'Genshin Impek', 'Mantapkan banh', '1709634339_items-3.jpg', '2024-03-06 14:02:37', 'Food', 'private'),
(4, 1, 1, 'Genshin Impek', 'Mantapkan banh', '1709634339_items-4.png', '2024-03-06 02:05:32', 'Game', 'public'),
(6, 2, 2, 'Alam', '', '1709634694_items-13.jpg', '2024-03-06 02:05:32', 'Nature', 'public'),
(7, 3, 3, 'SAwO', 'Kirito', '1709634953_items-14.jpg', '2024-03-06 02:05:32', 'Anime', 'public'),
(8, 3, 3, 'SAwO', 'Kirito', '1709634953_items-15.jpg', '2024-03-06 02:05:32', 'Anime', 'public'),
(9, 3, 3, 'SAwO', 'Kirito', '1709634953_items-16.jpg', '2024-03-06 02:05:32', 'Anime', 'public'),
(10, 2, 5, 'test', '', '1709689974_Haerin.full.319794.jpg', '2024-03-07 05:36:14', 'Food', 'private'),
(11, 1, 1, 'Jenshin', '', '1709734449_101462757_p0.png', '2024-03-06 14:14:09', 'Game', 'public');

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
(1, 'Ryan Yanuar Pradana', 'Ryn', '$2y$10$afL11FP0XsdbsoViMln3T.Zz0WrhUCRZ8ThcH7KfqvAt9GgGsm.Ci', 'ryanyanuarpradana@gmail.com', 'super_admin', '2024-03-07 12:02:58', '2024-03-02 18:26:17', '1709634519profile_photo_1.jpg'),
(2, 'Muhammad Abdul Fathir', 'fxthir', '$2y$10$uHom.NeGhhwsNYBGGl4DQu9CO45jmk/71W7De.N6QXSZ6sTGd6Tm.', 'muhammadabdoelfathir@gmail.com', 'user', '2024-03-07 11:49:04', '2024-03-05 04:10:17', '1709634743profile_photo_2.jpg'),
(3, 'Rendi Raihanrai', 'raihanrei', '$2y$10$wAACMFQtBHGhnQiy9UvNkel/ZR0mzOCltsfcile0kwu/MdTE7JKAa', 'raihanrai.rendi@gmail.com', 'admin', '2024-03-06 03:59:56', '2024-03-05 04:22:31', '1709635016profile_photo_3.jpg'),
(4, '', 'reynaldi', '$2y$10$nNDrEnJfGsLk9tEehyww8uUcDcmF4mD3AT.XWSC9C6/Y4cnX3.y0W', 'rynldhi@gmail.com', 'user', '2024-03-06 04:00:59', '2024-03-05 20:29:30', 'default_profile.svg');

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
-- Indexes for table `reports_album`
--
ALTER TABLE `reports_album`
  ADD PRIMARY KEY (`reportID`),
  ADD KEY `FK_reports_album_albumID` (`albumID`),
  ADD KEY `fk_reportedBy` (`reportedBy`),
  ADD KEY `fk_reportedUser` (`reportedUser`);

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
  MODIFY `albumID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `commentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `likeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `photoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports_album`
--
ALTER TABLE `reports_album`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reset_password`
--
ALTER TABLE `reset_password`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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

--
-- Constraints for table `reports_album`
--
ALTER TABLE `reports_album`
  ADD CONSTRAINT `FK_reports_album_albumID` FOREIGN KEY (`albumID`) REFERENCES `albums` (`albumID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reportedBy` FOREIGN KEY (`reportedBy`) REFERENCES `users` (`userID`),
  ADD CONSTRAINT `fk_reportedUser` FOREIGN KEY (`reportedUser`) REFERENCES `users` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
