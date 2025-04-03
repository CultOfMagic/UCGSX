-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2025 at 12:46 PM
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
-- Database: `ucgs`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('User','Administrator') NOT NULL,
  `ministry` enum('UCM','CWA','CHOIR','PWT','CYF') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('Active','Deactivated') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `ministry`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Jay Neri Gasilao', 'jnanunuevo@gmail.com', '$2y$10$sCRznaOEBq8q6H4NEQ83Qet1ldthoHBH6C6EiMn4RDosY3ZkLG7mO', 'Administrator', 'CHOIR', '2025-03-24 16:53:33', NULL, 'Active'),
(2, 'Susan Gasilao', 'susangasilao@yahoo.com', '$2y$10$OnO3uDHU8Gg4udHRomlsTOkWro7MUNAWXXL1s1KWuqygZLtoLsf16', 'User', 'CHOIR', '2025-03-24 16:53:54', '2025-03-25 11:03:34', 'Active'),
(4, 'John Michael Montes', 'johnmichaelmontes@gmail.com', '$2y$10$l/xrWyNSJGkWXRLo2iWpAehf5HWWfkuc.100CpxzhYu3Gjp2xUcAe', 'Administrator', 'PWT', '2025-03-24 17:03:17', NULL, 'Active'),
(5, 'Nerizza Joy Mabazza', 'nerijoyanonuevo@gmail.com', '$2y$10$EZ9UDHzES2QxPA9NHe/xJuV1rbeRpWrnhqgthliWBxQN9s5MdhTNy', 'User', 'CYF', '2025-03-24 19:31:24', NULL, 'Active'),
(6, 'Benito Mussolini', 'benitomussolini@gmail.com', '$2y$10$7zQYaKZi2mO2bgL2wT1poe2hI/ygl0Q8YJtYzV3o9GPsRaz8F2Fdu', 'User', 'UCM', '2025-03-24 20:05:50', NULL, 'Active'),
(7, 'Peanut', 'peanutbutter@gmail.com', '$2y$10$qhyLUSurql.bMWZqZymFFeJxJ81oIMKqYVuilVaSaaLR0ezN5QBxm', 'Administrator', 'CWA', '2025-03-24 20:48:33', NULL, 'Active'),
(8, 'Diamond', 'diamond@gmail.com', '$2y$10$zN0QPUmD0g1HnkU9/JrKROSp1BWczvgNRvuY.OinVF71Xiz/ERgxO', 'User', 'PWT', '2025-03-24 22:02:39', NULL, 'Active');
(9, 'SampleAccountName1', 'SampleEmail@gmail.com', '$2y$10$6IBC5nHRcO1zftbm6fAyaeZ0bc7EU1zWXQ65CI/YS8P7rIpEWfbSa', 'User', 'UCM', '2025-03-24 23:56:12', NULL, 'Active'),
(10, 'SampleAccountName2', 'SampleAccountEmail2@mail.com', '$2y$10$NA3tbWKXYouOfVA0Zo3S...dulnLzOUZ2LBeQTShkbRNQTZEz5iA6', 'Administrator', 'PWT', '2025-03-25 08:09:44', NULL, 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
