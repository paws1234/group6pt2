-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2023 at 07:48 AM
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
  `room_count` int(11) NOT NULL,
  `purpose` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `room_count`, `purpose`, `status`, `time`) VALUES
(98, 43, 1, 'study', 'approved', '2023-11-25 05:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_groupmates`
--

CREATE TABLE `reservation_groupmates` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation_groupmates`
--

INSERT INTO `reservation_groupmates` (`id`, `reservation_id`, `user_id`) VALUES
(230, 98, 43),
(231, 98, 48),
(232, 98, 44),
(233, 98, 47),
(234, 98, 45),
(235, 98, 46);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','user') NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `age` int(11) NOT NULL,
  `course` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `email`, `mobile`, `gender`, `age`, `course`) VALUES
(1, 'admin', '$argon2id$v=19$m=65536,t=2,p=1$3Gdt99B1dtXLYWYcfLKLsQ$wn7YwmydLbaDwnQCFoUjaP9NkGE54pvj+DPbjAKeWmY', 'admin', '', '', 'male', 0, ''),
(2, 'staff', '$argon2id$v=19$m=65536,t=2,p=1$3Gdt99B1dtXLYWYcfLKLsQ$wn7YwmydLbaDwnQCFoUjaP9NkGE54pvj+DPbjAKeWmY', 'staff', '', '', 'male', 0, ''),
(43, 'paws', '$argon2id$v=19$m=65536,t=2,p=1$GCYFY779yaoqpn2hdJ8ySw$0KBHQhFpUH+Wt919oOwd8jEhdsEX/bSs9ibUaJdXgX0', 'user', 'pawsmedz@gmail.com', '1651651', 'male', 21, 'engineering'),
(44, 'mike', '$argon2id$v=19$m=65536,t=2,p=1$zMfnmHf/Qcso8PoW1ajdBA$oyjiYIbIphDldXeKuw/k6WpajDPsy3WNH9ZsGdUnTtk', 'user', 'mike@gmail.com', '654655416', 'male', 21, 'business'),
(45, 'alex', '$argon2id$v=19$m=65536,t=2,p=1$KUWiq59fUliC2VboENM5kQ$YE1G+Zv5TOeXiPyAOXXTs9bCjaVKb7GhTLvB9Fpw2Gg', 'user', 'alex@gmail.com', '65165165', 'male', 21, 'arts'),
(46, 'jesa', '$argon2id$v=19$m=65536,t=2,p=1$Tg4P5NP+qcPmmGiKc6x0FQ$fy8uncrg4Ly1RL8TV4BcviqJ/O1w2nfve8jxEfoHVlA', 'user', 'jesa@gmail.com', '61265165', 'male', 21, 'arts'),
(47, 'markus', '$argon2id$v=19$m=65536,t=2,p=1$LPW+y2eMbiIcJUG646mfrw$C/Ue/3GE8hoaQQMYHpfRdIuTPhU5oGVDgO+HtphFd6U', 'user', 'markus@gmail.com', '8165165156', 'male', 21, 'engineering'),
(48, 'kayla', '$argon2id$v=19$m=65536,t=2,p=1$qmxaH3v7LCUb1+rxUxnIWQ$nIV9+SbF/EPdiWerBz850BglayTvRqJKkc5GiAlYYJA', 'user', 'kayla@gmail.com', '4541651651', 'female', 21, 'business');

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
-- Indexes for table `reservation_groupmates`
--
ALTER TABLE `reservation_groupmates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `reservation_groupmates`
--
ALTER TABLE `reservation_groupmates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reservation_groupmates`
--
ALTER TABLE `reservation_groupmates`
  ADD CONSTRAINT `reservation_groupmates_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_groupmates_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
