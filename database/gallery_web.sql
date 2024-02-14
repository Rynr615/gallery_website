-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2024 at 02:01 PM
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
  `userID` int(11) DEFAULT 0,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `thumbnail_album` varchar(255) NOT NULL DEFAULT 'album_default.svg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `albums`
--

INSERT INTO `albums` (`albumID`, `userID`, `title`, `description`, `createdAt`, `thumbnail_album`) VALUES
(1, 1, 'Album 1', 'Uji Coba', '2024-02-11 04:02:03', 'album_default.svg'),
(2, 1, 'Album 2', 'Cuba Lagi ges', '2024-02-11 04:02:03', 'album_default.svg'),
(4, 4, 'Album 4', 'Uji Coba 4', '2024-02-11 04:02:03', 'album_default.svg'),
(5, 2, 'Album 5', 'Uji Coba 5', '2024-02-11 04:02:03', 'album_default.svg'),
(6, 1, 'Mobil', '', '2024-02-11 04:02:03', 'album_default.svg'),
(7, 5, 'Album 6', 'Uji coba 6', '2024-02-11 04:02:03', 'album_default.svg'),
(8, 1, 'Album 7', 'Uji Coba 7', '2024-02-11 04:02:03', 'album_default.svg'),
(9, 1, 'Album 8', 'Uji coba 8', '2024-02-11 04:02:03', 'album_default.svg'),
(10, 1, 'Album 9', 'Uji Coba 9', '2024-02-11 04:02:03', 'album_default.svg'),
(11, 1, 'Album 10', 'Uji Coba 10', '2024-02-11 03:45:01', 'wallpaperbetter(1).jpg'),
(12, 1, 'Album 11', 'Uji Coba 11', '2024-02-11 03:45:09', '1707622721_wallpaperbetter(1).jpg'),
(13, 1, 'Album 12', 'Uji Coba 1', '2024-02-12 01:07:34', 'album_default.svg'),
(14, 2, 'Album 13', 'Uji Coba 13', '2024-02-11 04:02:50', 'album_default.svg'),
(15, 2, 'Album 14', 'Uji Coba 14', '2024-02-11 03:53:46', '1707623626_1161816.jpg'),
(16, 2, 'Album 15', 'Uji Coba 15', '2024-02-11 03:57:45', '1707623865_Furina.full.3984101.jpg'),
(17, 2, 'New Jeans', 'Bunniesss', '2024-02-12 12:38:02', 'items-11.jpg');

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
(1, 1, 14, 'stress njir', '2024-01-31 07:20:58'),
(2, 1, 14, 'dehel bro\r\n', '2024-01-31 08:03:09'),
(3, 2, 13, 'Stress cuk', '2024-02-05 04:05:12'),
(5, 2, 20, 'Punya sy bg', '2024-02-07 04:39:48'),
(6, 1, 20, 'Punya kita', '2024-02-09 02:59:36'),
(7, 3, 20, 'stress anying', '2024-02-09 02:59:56'),
(8, 2, 19, 'mmm', '2024-02-12 01:45:40');

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
(1, 1, 13, '2024-01-30 21:35:30'),
(2, 2, 13, '2024-01-30 21:38:58'),
(3, 3, 13, '2024-01-30 21:39:59'),
(4, 1, 14, '2024-01-31 01:20:47'),
(7, 1, 9, '2024-02-05 05:59:53'),
(9, 2, 20, '2024-02-06 22:39:39'),
(10, 1, 18, '2024-02-07 00:50:17'),
(11, 1, 19, '2024-02-08 18:48:19'),
(12, 2, 18, '2024-02-08 20:51:11'),
(13, 2, 10, '2024-02-08 20:58:55'),
(14, 3, 20, '2024-02-08 20:59:59');

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE `photos` (
  `photoID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `albumID` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `photos`
--

INSERT INTO `photos` (`photoID`, `userID`, `albumID`, `title`, `description`, `image_path`, `createdAt`) VALUES
(9, 1, NULL, 'Furina Wangy', 'Mantap Wak', '1706109042_items-3.jpg', '2024-01-24 15:10:42'),
(10, 1, NULL, 'Arlecchino ( tiris )', 'Shesssh', '1706109496_items-2.jpg', '2024-01-24 15:18:16'),
(13, 1, NULL, 'Hu Tao Slurrp', 'Best Waifu', '1706184375_items-5.jpg', '2024-02-07 01:04:49'),
(14, 3, NULL, 'Ayangka by Rendi', 'Jenong', '1706684602_items-6.jpg', '2024-01-31 07:03:22'),
(15, 1, NULL, 'Ayangka + Yoimiya', 'Hehehe', '1707134428_items-4.png', '2024-02-05 12:00:28'),
(17, 2, NULL, 'Naganohara Yoimiya', 'Istri ', '1707279784_items-7.jpg', '2024-02-07 04:23:04'),
(18, 2, 17, 'Haerin ', 'She is actually a cat', '1707280681_Haerin.full.319814.jpg', '2024-02-11 11:43:35'),
(19, 2, 17, 'Hanni', 'NWJNS', '1707280661_hanni-newjeans-omg-4k-wallpaper-uhdpaper.com-246@0@i.jpg', '2024-02-11 11:43:45'),
(20, 2, 16, 'Maomao', 'Waifu Material', '1707280769_items-9.jpg', '2024-02-11 04:12:52');

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
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_photo` varchar(255) DEFAULT 'default_profile.svg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `name`, `username`, `password`, `email`, `access_level`, `createdAt`, `profile_photo`) VALUES
(1, '', 'rynr', '$2y$10$IuMzJbYLTw74iLchLlSeT.QPvR8qHxjil1U2sB6l6aVRDzwAZZlC.', 'ryanyanuar184@gmail.com', 'user', '2024-02-12 06:21:49', 'default_profile.svg'),
(2, 'Ryan Yanuar Pradana', 'Ryn', '$2y$10$eo1VNxksX6y12dMlYfm5PuoOV4iJmaw405Hy31fil49JcuSCedRIm', 'ryanyanuarpradana@gmail.com', 'user', '2024-02-12 06:21:38', 'default_profile.svg'),
(3, '', 'raihan', '$2y$10$Wy4rxu24tx/SC9l4Bl1UX.jT24.uiE3pvnM4zFZMsX9kr01MBKSF2', 'raihanrai@gmail.com', 'user', '2024-02-12 06:22:01', 'default_profile.svg'),
(4, '', 'fxthirr', '$2y$10$Nhi47F8mRmknIlDs2qr1JuHxXFqFz69OVAESWiOcmFWdngvjR/O7e', 'fathir@gmail.com', 'user', '2024-02-12 06:22:12', 'default_profile.svg'),
(5, '', 'DeezNut', '$2y$10$46zbUy5LOjGqyUBuiB/JJuYSTYl0wV9WmujCpNOKsM6iyudWYMH4y', 'deeznut@gmail.com', 'user', '2024-02-12 06:22:21', 'default_profile.svg');

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `albumID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `commentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `likeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `photoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
