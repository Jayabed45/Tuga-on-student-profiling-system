-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 31, 2024 at 06:54 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tugaon_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `type_of_user` varchar(50) NOT NULL,
  `profile_picture` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`, `firstname`, `lastname`, `type_of_user`, `profile_picture`) VALUES
(1, 'admin@gmail.com', '123456789', 'Christine', 'The Admin', 'Admin', 'uploads/674ad80d02d0d.png');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `admin_id` int(50) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `admin_id`, `content`, `image_path`, `created_at`) VALUES
(8, 1, 'BSIT Orientation\r\n', 'uploads/675fc2d06976d.jpg', '2024-12-16 06:04:00'),
(13, 1, 'Hello Mga Gagues', '', '2024-12-22 07:11:58'),
(14, 1, 'The night is LIT', 'uploads/6767bc5722db6.jpg', '2024-12-22 07:14:31'),
(15, 1, 'hi', 'uploads/676810ae3a101.jpg', '2024-12-22 13:14:22'),
(16, 1, 'SKIBIDI TOILET SKRT', '', '2024-12-31 05:35:50'),
(17, 1, 'SKRRT', '', '2024-12-31 05:36:51'),
(18, 1, 'SKRRT', '', '2024-12-31 05:38:12'),
(19, 1, 'Hello', '', '2024-12-31 05:41:33'),
(20, 1, 'Hello', '', '2024-12-31 05:43:16'),
(21, 1, 'Hello', '', '2024-12-31 05:45:27'),
(22, 1, 'Hello', 'uploads/677385dc83c6a.jpg', '2024-12-31 05:49:16');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(50) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `age` varchar(50) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `student_year` varchar(50) NOT NULL,
  `section_of_student` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `type_of_user` varchar(50) NOT NULL,
  `profile_picture` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `admin_id`, `email`, `password`, `firstname`, `last_name`, `age`, `gender`, `student_year`, `section_of_student`, `address`, `type_of_user`, `profile_picture`) VALUES
(1, 0, 'christine@gmail.com', '123456789', 'Christine', 'Tuga-on', '20', 'Female', '3rd Year', 'Section E', 'Secret HAHA', 'Student', 'uploads/bsitlogo.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
