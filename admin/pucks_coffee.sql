-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2025 at 12:08 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pucks_coffee`
--

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email_entered` varchar(120) DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`log_id`, `user_id`, `email_entered`, `success`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'admin@pucks.com', 0, NULL, NULL, '2025-12-26 15:15:21'),
(2, 1, 'admin@pucks.com', 0, NULL, NULL, '2025-12-26 15:20:15'),
(3, 1, 'admin@pucks.com', 0, NULL, NULL, '2025-12-26 15:24:39'),
(4, 1, 'admin@pucks.com', 0, NULL, NULL, '2025-12-26 15:25:18'),
(5, 1, 'admin@pucks.com', 0, NULL, NULL, '2025-12-26 15:28:33');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `reset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`reset_id`, `user_id`, `token_hash`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 1, 'da2cff9f32ec6709dd2350a489a0424b6010836206f7ad2bb1a338b74b8b416a', '2025-12-27 06:53:47', '2025-12-27 14:08:37', '2025-12-27 05:38:47'),
(2, 1, '9b2d55f56e8f310b19b672b67a91d3e7efc9c14ffe054049e5500374e4e546a5', '2025-12-27 07:01:33', '2025-12-27 14:08:37', '2025-12-27 05:46:33'),
(3, 1, 'dbc6b7ce9f3390dfaeb9f1009eeef352be5606bbd6576361003cec68ee2147ba', '2025-12-27 07:02:18', '2025-12-27 14:08:37', '2025-12-27 05:47:18'),
(4, 1, '22d00d4f8548d8314a8d2ba05681d1d8541abf62bbf0444cfd419303a133953a', '2025-12-27 07:57:37', '2025-12-27 15:07:41', '2025-12-27 06:42:37'),
(5, 1, 'd552908726bd8d5f987e4a0fc3f9caaa5a7837d0e36cd54f5830c5fb744115e2', '2025-12-27 08:22:41', '2025-12-27 15:08:22', '2025-12-27 07:07:41'),
(6, 1, '706b824a94e17ceaae6ac085b2b4be9b8dd1738b516f7ee84d81518d246d73d2', '2025-12-27 08:27:02', '2025-12-27 15:12:29', '2025-12-27 07:12:02'),
(7, 1, 'ce05fba00bc0a11a71dce5d2b4b6913a0ff800d0155b7078454434272036ab85', '2025-12-27 08:30:24', '2025-12-27 15:16:03', '2025-12-27 07:15:24'),
(8, 1, 'bd67dcb77f85d050c409cc12a69ba205bce04c4d3a01c9bb1184b6a25b8ba1e0', '2025-12-27 08:33:24', '2025-12-27 15:18:44', '2025-12-27 07:18:24'),
(9, 1, 'a583cdbb8fd51136e940515bb76054f25176552391393d3ee669708aaa099094', '2025-12-27 08:48:07', '2025-12-27 15:38:45', '2025-12-27 07:33:07'),
(10, 1, 'a38ac2a870cb7fa32f7d6c87a15946f9ab696388bf7400183aba799a72df63a6', '2025-12-27 08:53:45', '2025-12-27 15:39:01', '2025-12-27 07:38:45'),
(11, 1, '44e91f35192d615592a86492a96e333774bda39a105e191371457824e1e138ba', '2025-12-27 08:54:09', '2025-12-27 15:40:33', '2025-12-27 07:39:09'),
(12, 1, 'c6f5eed77de9a72135cba37f6b0329a199b246e23ac3d9e56166d198bea67a2b', '2025-12-27 08:55:46', NULL, '2025-12-27 07:40:46');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `username` varchar(60) DEFAULT NULL,
  `email` varchar(120) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','staff','customer') NOT NULL DEFAULT 'customer',
  `status` enum('active','inactive','banned') NOT NULL DEFAULT 'active',
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `last_login_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `username`, `email`, `phone`, `password_hash`, `role`, `status`, `profile_image`, `created_at`, `updated_at`, `last_login_at`) VALUES
(1, 'Favian dasda', 'admin', 'admin@pucks.com', '01458884343', '$2y$10$LKghD8cWGJ3mGoxyObrs1uQQCU/avhAN4BJXV1GL4KGFlzDTBfLge', 'admin', 'active', NULL, '2025-12-26 14:39:15', '2025-12-27 08:32:46', NULL),
(2, 'Staff Pucks', 'staff', 'staff@pucks.com', NULL, '$2y$10$67YDlbOkeRhELVIG53BFLu2QsZ4olmuK8xEvmV1iiBEeG7b2aJF6O', 'staff', 'active', NULL, '2025-12-26 14:39:15', '2025-12-26 16:02:55', NULL),
(3, 'Customer Pucks', 'customer', 'customer@pucks.com', NULL, '$2y$10$ZeaodMxEO5muQKCfug/sqOkHNy2gwVEEl47ABH7HqQGgTUh/0uqs.', 'customer', 'active', NULL, '2025-12-26 14:39:15', '2025-12-26 16:02:55', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `expires_at` (`expires_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
