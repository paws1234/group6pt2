-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2023 at 07:38 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `computer_lab_reservation`
--

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `computer_count` int(11) NOT NULL,
  `purpose` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$argon2id$v=19$m=65536,t=2,p=1$3Gdt99B1dtXLYWYcfLKLsQ$wn7YwmydLbaDwnQCFoUjaP9NkGE54pvj+DPbjAKeWmY', 'admin'),
(2, 'staff', '$argon2id$v=19$m=65536,t=2,p=1$W/F0ZDMJuSUDHg1p1MbZrQ$rwbGOPPD2ZBrW0On5ZMAt49RP6zauIj6cGnkwI1/3zI', 'staff'),
(3, 'paws', '$argon2id$v=19$m=65536,t=2,p=1$sC7zyG0ooXwT2vPpEK3Kzw$j2u1PcZ5WyORlGXAD7pCX+yiiew9QjgIsI9juVdXiwI', 'user'),
(32, 'carlos', '$argon2id$v=19$m=65536,t=2,p=1$0fvoiMGhr1DBZrpuQ1CvvA$zPOOIv5cGnT3sn6BVg0LBLFoRVg+ONFJpSp2mPuw0FA', 'user'),
(33, 'mike', '$argon2id$v=19$m=65536,t=2,p=1$INJDKtns1EBdcU+2llNx+Q$UYKkQ9hQt6klvuUOVs5o2KlrWbvPTP5JylzhHX1RI3o', 'user'),
(34, 'CARLOS', '$argon2id$v=19$m=65536,t=2,p=1$w1KCP2GlGP4xjPXXk8/dDA$a9hB8OC5SSK53UcwZtqJIREpaYaqBDSChZW00tt7LLg', 'user'),
(35, 'alex', '$argon2id$v=19$m=65536,t=2,p=1$ex3xaxvGnv6gdLsCGkekNw$S6Jbmix/H8uLvfWu0P8nElDs4gxw7ZIceOxYtqifv/4', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
