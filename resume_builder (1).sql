-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2025 at 11:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `resume_builder`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `action`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'resume_created', 'Created new resume: Software Engineer Resume', NULL, '2025-05-25 17:56:53'),
(2, 1, 'resume_analyzed', 'Analyzed resume against job description', NULL, '2025-05-25 17:56:53'),
(3, 1, 'resume_downloaded', 'Downloaded resume as PDF', NULL, '2025-05-25 17:56:53'),
(4, 1, 'template_viewed', 'Viewed Modern Professional template', NULL, '2025-05-25 17:56:53'),
(5, 3, 'signup', 'User account created', '::1', '2025-05-25 20:23:55'),
(6, 3, 'login', 'User logged in', '::1', '2025-05-25 20:24:17'),
(7, 3, 'logout', 'User logged out', '::1', '2025-05-25 20:25:13'),
(8, 3, 'login', 'User logged in', '::1', '2025-05-25 20:25:34'),
(9, 3, 'logout', 'User logged out', '::1', '2025-05-25 20:25:47'),
(10, 3, 'login', 'User logged in', '::1', '2025-05-25 21:10:14');

-- --------------------------------------------------------

--
-- Table structure for table `resumes`
--

CREATE TABLE `resumes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `resume_name` varchar(255) NOT NULL,
  `template_name` varchar(100) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `status` enum('draft','completed','published') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resumes`
--

INSERT INTO `resumes` (`id`, `user_id`, `resume_name`, `template_name`, `file_path`, `status`, `created_at`, `updated_at`) VALUES
(2, 1, 'Marketing Manager CV', 'Creative Design', NULL, 'draft', '2025-05-25 17:56:53', '2025-05-25 17:56:53'),
(3, 1, 'Data Scientist Resume', 'Minimal Clean', NULL, 'published', '2025-05-25 17:56:53', '2025-05-25 17:56:53'),
(4, 1, 'Product Manager CV', 'Executive Style', NULL, 'completed', '2025-05-25 17:56:53', '2025-05-25 17:56:53'),
(5, 3, 'Enes Nimani Resume', 'Template 1', NULL, 'draft', '2025-05-25 21:12:01', '2025-05-25 21:12:01');

-- --------------------------------------------------------

--
-- Table structure for table `resume_analytics`
--

CREATE TABLE `resume_analytics` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `resume_id` int(11) DEFAULT NULL,
  `job_description` text DEFAULT NULL,
  `match_score` int(11) DEFAULT 0,
  `ai_probability` int(11) DEFAULT 0,
  `missing_keywords` text DEFAULT NULL,
  `analysis_result` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resume_analytics`
--

INSERT INTO `resume_analytics` (`id`, `user_id`, `resume_id`, `job_description`, `match_score`, `ai_probability`, `missing_keywords`, `analysis_result`, `created_at`) VALUES
(1, 1, NULL, NULL, 85, 15, NULL, NULL, '2025-05-25 17:56:53'),
(2, 1, 2, NULL, 72, 25, NULL, NULL, '2025-05-25 17:56:53'),
(3, 1, 3, NULL, 91, 10, NULL, NULL, '2025-05-25 17:56:53'),
(4, 1, 4, NULL, 78, 20, NULL, NULL, '2025-05-25 17:56:53');

-- --------------------------------------------------------

--
-- Table structure for table `resume_data`
--

CREATE TABLE `resume_data` (
  `id` int(11) NOT NULL,
  `resume_id` int(11) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resume_data`
--

INSERT INTO `resume_data` (`id`, `resume_id`, `data`, `created_at`, `updated_at`) VALUES
(1, 5, '{\"full_name\":\"Enes Nimani\",\"title\":\"Web-developer\",\"email\":\"enesnimani87@gmail.com\",\"phone\":\"043891582\",\"location\":\"New York\",\"linkedin\":\"enesnimani\",\"website\":\"\",\"summary\":\"asdasdasdadsasd\",\"experience\":[{\"title\":\"asdads\",\"company\":\"asdasda\",\"duration\":\"sdasdadsasda\",\"description\":\"sdasdasdasd\"}],\"education\":[{\"degree\":\"asdasdasdasd\",\"school\":\"asdasdasd\",\"year\":\"asdasdasd\"}],\"skills\":[]}', '2025-05-25 21:12:01', '2025-05-25 21:12:01');

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `id` int(11) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `template_description` text DEFAULT NULL,
  `template_file` varchar(255) DEFAULT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `is_premium` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`id`, `template_name`, `template_description`, `template_file`, `preview_image`, `is_premium`, `created_at`) VALUES
(1, 'Modern Professional', 'Clean and modern design perfect for tech professionals', NULL, NULL, 0, '2025-05-25 17:56:53'),
(2, 'Creative Design', 'Colorful and creative layout for designers and marketers', NULL, NULL, 0, '2025-05-25 17:56:53'),
(3, 'Minimal Clean', 'Simple and elegant design that works for any industry', NULL, NULL, 0, '2025-05-25 17:56:53'),
(4, 'Executive Style', 'Professional layout for senior positions and executives', NULL, NULL, 1, '2025-05-25 17:56:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `created_at`, `updated_at`) VALUES
(1, 'johndoe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', '2025-05-25 17:56:53', '2025-05-25 17:56:53'),
(2, 'janedoe', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Doe', '2025-05-25 17:56:53', '2025-05-25 17:56:53'),
(3, 'enesnimani978', 'enesnimani87@gmail.com', '$2y$10$/zQ7kAil2GSWWqZv14pHe.e15OcP4djevmouQBPaisxbbPJ03A6HG', 'Enes Nimani', '2025-05-25 20:23:55', '2025-05-25 21:10:14');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_activity` (`user_id`),
  ADD KEY `idx_activity_date` (`created_at`);

--
-- Indexes for table `resumes`
--
ALTER TABLE `resumes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_resumes` (`user_id`);

--
-- Indexes for table `resume_analytics`
--
ALTER TABLE `resume_analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_analytics` (`user_id`),
  ADD KEY `idx_resume_analytics` (`resume_id`);

--
-- Indexes for table `resume_data`
--
ALTER TABLE `resume_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_resume_data` (`resume_id`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `idx_user_sessions` (`user_id`),
  ADD KEY `idx_session_token` (`session_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `resumes`
--
ALTER TABLE `resumes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `resume_analytics`
--
ALTER TABLE `resume_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `resume_data`
--
ALTER TABLE `resume_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resumes`
--
ALTER TABLE `resumes`
  ADD CONSTRAINT `resumes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resume_analytics`
--
ALTER TABLE `resume_analytics`
  ADD CONSTRAINT `resume_analytics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resume_analytics_ibfk_2` FOREIGN KEY (`resume_id`) REFERENCES `resumes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `resume_data`
--
ALTER TABLE `resume_data`
  ADD CONSTRAINT `resume_data_ibfk_1` FOREIGN KEY (`resume_id`) REFERENCES `resumes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
