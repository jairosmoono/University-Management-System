-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2026 at 08:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `university_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('upcoming','active','completed') NOT NULL DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `name`, `start_date`, `end_date`, `is_current`, `status`, `created_at`, `updated_at`) VALUES
(1, '2024/2025', '2024-08-01', '2025-07-31', 0, 'completed', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(2, '2025/2026', '2025-08-01', '2026-07-31', 1, 'active', '2026-06-05 19:10:22', '2026-06-07 14:00:54'),
(3, '2026/2027', '2026-08-01', '2027-07-31', 0, 'upcoming', '2026-06-05 19:10:22', '2026-06-07 14:00:54');

-- --------------------------------------------------------

--
-- Table structure for table `admissions`
--

CREATE TABLE `admissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_number` varchar(30) NOT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `semester_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `previous_school` varchar(255) DEFAULT NULL,
  `qualification_type` varchar(100) DEFAULT NULL,
  `year_completed` year(4) DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`documents`)),
  `status` enum('pending','approved','rejected','waitlisted') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admissions`
--

INSERT INTO `admissions` (`id`, `application_number`, `program_id`, `semester_id`, `first_name`, `last_name`, `middle_name`, `date_of_birth`, `gender`, `nationality`, `phone`, `email`, `address`, `previous_school`, `qualification_type`, `year_completed`, `grade`, `documents`, `status`, `rejection_reason`, `reviewed_by`, `reviewed_at`, `student_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'APP/2026/00001', 1, 4, 'james', 'banda', NULL, '2000-01-01', 'male', 'Zambian', '0966666666', 'james@university.com', NULL, 'mtti', 'Diploma', '2021', 'merit', NULL, 'rejected', NULL, 1, '2026-06-07 06:49:15', NULL, '2026-06-07 06:47:20', '2026-06-07 06:49:15', NULL),
(2, 'APP/2026/00002', 1, 4, 'james', 'banda', NULL, '2000-01-01', 'male', 'Zambian', '0966666666', 'james@university.com', NULL, 'mtti', 'Grade 12 Certificate', '2021', 'merit', NULL, 'approved', NULL, 1, '2026-06-07 06:51:44', NULL, '2026-06-07 06:51:10', '2026-06-07 06:51:44', NULL),
(3, 'APP/2026/00003', 1, 4, 'mercy', 'Phiri', NULL, '2009-01-07', 'female', 'Zambian', '+260971000005', 'mercy@university.com', 'wnl', 'mtti', 'Grade 12 Certificate', '2021', 'merit', '{\"certificates\":\"uploads\\/admissions\\/1780815419_certificates.pdf\",\"national_id\":\"uploads\\/admissions\\/1780815419_national_id.pdf\",\"photo\":\"uploads\\/admissions\\/1780815419_photo.jpeg\"}', 'approved', NULL, 1, '2026-06-07 06:57:26', NULL, '2026-06-07 06:56:59', '2026-06-07 06:57:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `alumni`
--

CREATE TABLE `alumni` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `graduation_year` year(4) NOT NULL,
  `current_employer` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `employment_status` enum('employed','self_employed','unemployed','further_studies') NOT NULL DEFAULT 'employed',
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `linkedin_url` varchar(500) DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alumni`
--

INSERT INTO `alumni` (`id`, `student_id`, `graduation_year`, `current_employer`, `job_title`, `employment_status`, `city`, `country`, `linkedin_url`, `biography`, `created_at`, `updated_at`) VALUES
(1, 1, '2026', 'ktti', 'software engineer', 'employed', 'klb', 'Zambia', NULL, NULL, '2026-06-06 13:05:29', '2026-06-06 13:05:29');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_by` bigint(20) UNSIGNED DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `send_email` tinyint(1) NOT NULL DEFAULT 0,
  `send_sms` tinyint(1) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `category` enum('academic','event','emergency','general') NOT NULL DEFAULT 'general',
  `priority` enum('normal','high','urgent') NOT NULL DEFAULT 'normal',
  `target_audience` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_audience`)),
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `publish_date` timestamp NULL DEFAULT NULL,
  `expiry_date` timestamp NULL DEFAULT NULL,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `user_id`, `is_published`, `published_by`, `published_at`, `send_email`, `send_sms`, `title`, `content`, `category`, `priority`, `target_audience`, `attachments`, `publish_date`, `expiry_date`, `views_count`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 0, 1, NULL, 0, 0, 'holiday notice', 'we are closing tomorrow', 'academic', 'normal', '[\"all\"]', NULL, NULL, '2026-06-07 22:47:00', 0, '2026-06-05 22:47:48', '2026-06-05 22:47:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `asset_code` varchar(50) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(12,2) DEFAULT NULL,
  `current_value` decimal(12,2) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `status` enum('active','maintenance','disposed','lost') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `department_id`, `name`, `asset_code`, `category`, `description`, `serial_number`, `purchase_date`, `purchase_price`, `current_value`, `location`, `warranty_expiry`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 5, 'Dell latitute laptop', 'AST 001', 'Electronics', '4 gb ram\r\n1tb hdd\r\n14\" screen\r\nblack color', '010005689', '2026-06-06', 10000.00, 10000.00, 'computer lab', '2026-06-20', 'active', '2026-06-06 12:43:30', '2026-06-06 12:43:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_offering_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `total_marks` decimal(5,2) NOT NULL DEFAULT 100.00,
  `status` enum('draft','published','closed') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assignment_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `submission_text` text DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `marks_obtained` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','submitted','graded','late') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_records`
--

CREATE TABLE `attendance_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `attendance_session_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL DEFAULT 'present',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_records`
--

INSERT INTO `attendance_records` (`id`, `attendance_session_id`, `student_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'present', NULL, '2026-06-06 17:51:04', '2026-06-06 17:51:04'),
(2, 2, 2, 'absent', NULL, '2026-06-06 18:40:06', '2026-06-06 18:40:06'),
(3, 3, 2, 'present', NULL, '2026-06-06 18:42:53', '2026-06-06 18:42:53'),
(4, 4, 1, 'present', NULL, '2026-06-06 18:45:25', '2026-06-06 18:45:25'),
(5, 5, 3, 'present', NULL, '2026-06-07 13:58:44', '2026-06-07 13:58:44'),
(6, 5, 4, 'present', NULL, '2026-06-07 13:58:44', '2026-06-07 13:58:44');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_sessions`
--

CREATE TABLE `attendance_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_offering_id` bigint(20) UNSIGNED DEFAULT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `session_type` enum('lecture','lab','tutorial') NOT NULL DEFAULT 'lecture',
  `topic` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_sessions`
--

INSERT INTO `attendance_sessions` (`id`, `course_offering_id`, `program_id`, `date`, `session_type`, `topic`, `created_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 4, '2026-06-06', 'tutorial', NULL, NULL, '2026-06-06 17:51:04', '2026-06-06 17:51:04'),
(2, 6, 4, '2026-06-06', 'lecture', NULL, NULL, '2026-06-06 18:40:06', '2026-06-06 18:40:06'),
(3, 7, 4, '2026-06-06', 'lecture', NULL, NULL, '2026-06-06 18:42:53', '2026-06-06 18:42:53'),
(4, 8, 2, '2026-06-06', 'lecture', 'types of computers', NULL, '2026-06-06 18:45:25', '2026-06-06 18:45:25'),
(5, 8, 2, '2026-06-07', 'lecture', 'malware types', NULL, '2026-06-07 13:58:44', '2026-06-07 13:58:44');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(20) NOT NULL,
  `model_type` varchar(100) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `request_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_data`)),
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `url`, `ip_address`, `user_agent`, `request_data`, `old_values`, `new_values`, `created_at`) VALUES
(1, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Test', NULL, NULL, NULL, NULL),
(2, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, NULL, NULL, NULL),
(3, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, NULL, NULL, NULL),
(4, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, NULL, NULL, NULL),
(5, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, NULL, NULL, NULL),
(6, 10, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, NULL, NULL, NULL),
(7, 10, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, NULL, NULL, NULL),
(8, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', NULL, NULL, NULL, NULL),
(9, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(10, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(11, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(12, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(13, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(14, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(15, 16, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(16, 16, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(17, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(18, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(19, 10, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(20, 10, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(21, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(22, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(23, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(24, 10, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(25, 10, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(26, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(27, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(28, 16, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(29, 16, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(30, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(31, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(32, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(33, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(34, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(35, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(36, 4, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(37, 4, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(38, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(39, 4, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(40, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(41, 2, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(42, 2, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(43, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(44, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(45, 7, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(46, 7, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(47, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(48, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(49, 7, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(50, 7, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(51, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(52, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(53, 8, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(54, 8, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(55, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(56, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(57, 8, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(58, 8, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(59, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(60, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(61, 6, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(62, 6, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(63, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(64, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(65, 6, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(66, 6, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(67, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(68, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(69, 6, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(70, 6, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(71, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(72, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(73, 6, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(74, 6, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(75, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(76, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(77, 3, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(78, 3, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(79, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(80, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(81, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(82, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(83, 3, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(84, 3, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(85, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(86, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(87, 4, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(88, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(89, 4, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
(90, 10, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bill_items`
--

CREATE TABLE `bill_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_bill_id` bigint(20) UNSIGNED NOT NULL,
  `fee_type` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bill_items`
--

INSERT INTO `bill_items` (`id`, `student_bill_id`, `fee_type`, `description`, `amount`, `discount`, `created_at`, `updated_at`) VALUES
(1, 1, 'BSSE Semester 2 Fees 2025/26', 'BSSE Semester 2 Fees 2025/26', 8500.00, NULL, '2026-06-06 09:58:38', '2026-06-06 09:58:38'),
(2, 2, 'BSSE Semester 2 Fees 2025/26', 'BSSE Semester 2 Fees 2025/26', 8500.00, NULL, '2026-06-06 09:58:38', '2026-06-06 09:58:38'),
(3, 3, 'BBA Semester 1 Fees 2025/26', 'BBA Semester 1 Fees 2025/26', 7500.00, NULL, '2026-06-07 17:07:46', '2026-06-07 17:07:46');

-- --------------------------------------------------------

--
-- Table structure for table `book_borrowings`
--

CREATE TABLE `book_borrowings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `library_book_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `fine_amount` decimal(8,2) DEFAULT NULL,
  `fine_paid` tinyint(1) NOT NULL DEFAULT 0,
  `fine_paid_at` timestamp NULL DEFAULT NULL,
  `status` enum('borrowed','returned','overdue','lost') NOT NULL DEFAULT 'borrowed',
  `issued_by` bigint(20) UNSIGNED DEFAULT NULL,
  `returned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fine_collected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `fine_waived` tinyint(1) NOT NULL DEFAULT 0,
  `fine_waive_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `book_borrowings`
--

INSERT INTO `book_borrowings` (`id`, `library_book_id`, `student_id`, `user_id`, `issue_date`, `due_date`, `return_date`, `fine_amount`, `fine_paid`, `fine_paid_at`, `status`, `issued_by`, `returned_to`, `created_at`, `updated_at`, `fine_collected_by`, `fine_waived`, `fine_waive_reason`) VALUES
(1, 8, 1, NULL, '2026-06-06', '2026-06-20', '2026-06-06', NULL, 0, NULL, 'returned', 1, 1, '2026-06-06 11:39:38', '2026-06-06 11:41:21', NULL, 0, NULL),
(2, 8, 1, NULL, '2026-06-06', '2026-06-20', NULL, NULL, 0, NULL, 'borrowed', 1, NULL, '2026-06-06 11:42:18', '2026-06-06 11:42:18', NULL, 0, NULL),
(3, 5, 2, NULL, '2026-06-08', '2026-06-09', NULL, NULL, 0, NULL, 'borrowed', 1, NULL, '2026-06-08 14:24:34', '2026-06-08 14:24:34', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `book_categories`
--

CREATE TABLE `book_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `book_categories`
--

INSERT INTO `book_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Computer Science', 'Programming, algorithms, artificial intelligence', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(2, 'Engineering', 'Civil, Mechanical, Electrical Engineering', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(3, 'Business & Management', 'Management, marketing, entrepreneurship', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(4, 'Accounting & Finance', 'Financial accounting, auditing, taxation', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(5, 'Mathematics', 'Pure and applied mathematics, statistics', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(6, 'Health Sciences', 'Nursing, medicine, public health', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(7, 'Law', 'Legal texts, case studies, jurisprudence', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(8, 'General Reference', 'Dictionaries, encyclopedias, atlases', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(9, 'African Studies', 'African history, culture, literature', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(10, 'Journals & Periodicals', 'Academic journals and research publications', '2026-06-05 19:10:25', '2026-06-05 19:10:25');

-- --------------------------------------------------------

--
-- Table structure for table `continuous_assessments`
--

CREATE TABLE `continuous_assessments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_offering_id` bigint(20) UNSIGNED NOT NULL,
  `ca_score` decimal(5,2) DEFAULT NULL,
  `max_ca_score` decimal(5,2) NOT NULL DEFAULT 30.00,
  `entered_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `credits` tinyint(4) NOT NULL DEFAULT 3,
  `level` varchar(10) DEFAULT NULL,
  `course_type` varchar(50) NOT NULL DEFAULT 'core',
  `description` text DEFAULT NULL,
  `prerequisites` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `department_id`, `code`, `name`, `credits`, `level`, `course_type`, `description`, `prerequisites`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 6, 'SE101', 'Introduction to Software Engineering', 3, '100', 'core', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-05 19:10:27', NULL),
(2, 6, 'SE201', 'Object-Oriented Programming', 4, '200', 'core', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-05 19:10:27', NULL),
(3, 6, 'SE301', 'Software Design Patterns', 3, '300', 'core', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-05 19:10:27', NULL),
(4, 6, 'SE401', 'Final Year Project', 6, '400', 'core', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-05 19:10:27', NULL),
(5, 5, 'CS101', 'Introduction to Computer Science', 3, '100', 'practical', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-07 13:44:26', NULL),
(6, 5, 'CS201', 'Data Structures and Algorithms', 4, '200', 'theory', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-07 13:47:48', NULL),
(7, 3, 'BA101', 'Principles of Management', 3, '100', 'practical', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-07 13:38:13', NULL),
(8, 4, 'AC101', 'Financial Accounting I', 3, '100', 'theory', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-07 13:37:48', NULL),
(9, 6, 'SE211', 'Web Development', 3, '200', 'elective', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-05 19:10:27', NULL),
(10, 6, 'SE212', 'Mobile Application Development', 3, '200', 'elective', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-05 19:10:27', NULL),
(11, 5, 'CS301', 'Database Management Systems', 3, '300', 'core', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-05 19:10:27', NULL),
(12, 5, 'CS302', 'Operating Systems', 3, '300', 'core', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-05 19:10:27', NULL),
(13, 7, 'IS301', 'Network Administration', 3, '300', 'core', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-05 19:10:27', NULL),
(14, 6, 'SE150', 'Programming Fundamentals Lab', 2, '100', 'lab', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-05 19:10:27', NULL),
(15, 5, 'MATH101', 'Calculus for Engineers', 4, '100', 'core', NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-05 19:10:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_offerings`
--

CREATE TABLE `course_offerings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `semester_id` bigint(20) UNSIGNED NOT NULL,
  `lecturer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `schedule` varchar(255) DEFAULT NULL,
  `max_students` int(11) NOT NULL DEFAULT 50,
  `enrolled_students` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','cancelled','completed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_offerings`
--

INSERT INTO `course_offerings` (`id`, `course_id`, `academic_year_id`, `semester_id`, `lecturer_id`, `venue`, `schedule`, `max_students`, `enrolled_students`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 4, 1, 'Lecture Theatre 2', NULL, 60, 47, 'active', '2026-06-05 19:10:27', '2026-06-07 14:08:00'),
(3, 11, 2, 4, 1, 'Room 301', NULL, 50, 39, 'active', '2026-06-05 19:10:27', '2026-06-07 14:08:00'),
(4, 6, 2, 4, 1, 'Lecture Theatre 1', NULL, 60, 53, 'active', '2026-06-05 19:10:27', '2026-06-07 14:08:00'),
(6, 8, 2, 4, 1, NULL, NULL, 50, 1, 'active', '2026-06-06 16:09:52', '2026-06-06 19:40:05'),
(7, 7, 2, 4, 1, NULL, NULL, 50, 1, 'active', '2026-06-06 16:29:25', '2026-06-06 19:39:30'),
(8, 5, 2, 4, 1, NULL, NULL, 50, 3, 'active', '2026-06-06 18:31:40', '2026-06-08 18:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `course_program`
--

CREATE TABLE `course_program` (
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `year_of_study` tinyint(4) DEFAULT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_registrations`
--

CREATE TABLE `course_registrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_offering_id` bigint(20) UNSIGNED NOT NULL,
  `registered_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('registered','dropped','completed','failed') NOT NULL DEFAULT 'registered',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_registrations`
--

INSERT INTO `course_registrations` (`id`, `student_id`, `course_offering_id`, `registered_by`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 8, 1, 'dropped', '2026-06-06 19:38:16', '2026-06-06 19:43:13'),
(2, 2, 7, 1, 'registered', '2026-06-06 19:39:30', '2026-06-06 19:39:30'),
(3, 2, 6, 1, 'registered', '2026-06-06 19:40:05', '2026-06-06 19:40:05'),
(4, 8, 1, 1, 'registered', '2026-06-07 07:55:04', '2026-06-07 07:55:04'),
(5, 8, 8, 1, 'registered', '2026-06-07 13:57:22', '2026-06-07 13:57:22'),
(6, 2, 1, 1, 'registered', '2026-06-07 14:08:00', '2026-06-07 14:08:00'),
(7, 2, 3, 1, 'registered', '2026-06-07 14:08:00', '2026-06-07 14:08:00'),
(8, 2, 4, 1, 'registered', '2026-06-07 14:08:00', '2026-06-07 14:08:00'),
(9, 1, 8, 1, 'registered', '2026-06-08 18:18:57', '2026-06-08 18:18:57'),
(10, 3, 8, 1, 'registered', '2026-06-08 18:19:24', '2026-06-08 18:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faculty_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `hod_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `faculty_id`, `name`, `code`, `hod_id`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Department of Civil Engineering', 'DCE', 1, 'Structural, geotechnical and transportation engineering', 'active', '2026-06-05 19:10:23', '2026-06-07 12:58:47', NULL),
(2, 1, 'Department of Electrical Engineering', 'DEE', NULL, 'Power systems, electronics and communications', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(3, 2, 'Department of Business Administration', 'DBA', NULL, 'Management, marketing and entrepreneurship', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(4, 2, 'Department of Accounting and Finance', 'DAF', NULL, 'Financial accounting, auditing and finance', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(5, 3, 'Department of Computer Science', 'DCS', NULL, 'Algorithms, data structures and theoretical computing', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(6, 4, 'Department of Software Engineering', 'DSE', NULL, 'Software development, testing and project management', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(7, 4, 'Department of Information Systems', 'DIS', NULL, 'Database systems, networking and cybersecurity', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(8, 5, 'Department of Primary Education', 'DPE', NULL, 'Primary school teacher training', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(9, 6, 'Department of Nursing', 'DON', NULL, 'Registered nursing and midwifery programs', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(10, 6, 'Department of Public Health', 'DPH', NULL, 'Epidemiology, health promotion and environmental health', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(11, 4, 'Business Department', 'BD', 1, NULL, 'active', '2026-06-07 13:00:01', '2026-06-07 13:00:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('transcript','certificate','id_card','admission_letter','other') NOT NULL DEFAULT 'other',
  `category` varchar(100) DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `download_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` varchar(20) NOT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `employment_type` enum('permanent','contract','part-time') NOT NULL DEFAULT 'permanent',
  `join_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive','terminated') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `department_id`, `employee_id`, `designation`, `employment_type`, `join_date`, `contract_end_date`, `basic_salary`, `bank_name`, `bank_account`, `national_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 6, 'EMP-2024-001', 'Senior Lecturer', 'permanent', '2024-01-15', NULL, 12500.00, NULL, NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-05 19:10:27', NULL),
(2, 8, 3, 'EMP-2024-002', 'HR Officer', 'permanent', '2024-03-01', NULL, 9500.00, NULL, NULL, NULL, 'active', '2026-06-05 19:10:27', '2026-06-05 19:10:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_allowances`
--

CREATE TABLE `employee_allowances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `allowance_type` varchar(60) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_allowances`
--

INSERT INTO `employee_allowances` (`id`, `employee_id`, `allowance_type`, `description`, `amount`, `percentage`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'housing', NULL, 0.00, 25.00, 1, '2026-06-07 19:16:17', '2026-06-07 19:16:17');

-- --------------------------------------------------------

--
-- Table structure for table `employee_appointments`
--

CREATE TABLE `employee_appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position` varchar(255) NOT NULL,
  `appointment_date` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `salary` decimal(12,2) DEFAULT NULL,
  `contract_type` varchar(50) NOT NULL DEFAULT 'permanent',
  `notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_deductions`
--

CREATE TABLE `employee_deductions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `deduction_type` varchar(60) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_deductions`
--

INSERT INTO `employee_deductions` (`id`, `employee_id`, `deduction_type`, `description`, `amount`, `is_recurring`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'madson_insurance', NULL, 120.00, 1, 1, '2026-06-07 19:16:51', '2026-06-07 19:16:51');

-- --------------------------------------------------------

--
-- Table structure for table `employment_listings`
--

CREATE TABLE `employment_listings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `employment_type` varchar(50) NOT NULL DEFAULT 'full-time',
  `vacancies` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `deadline` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employment_listings`
--

INSERT INTO `employment_listings` (`id`, `title`, `department_id`, `description`, `requirements`, `employment_type`, `vacancies`, `deadline`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Lecturer', 11, 'we are looking for ethusiastic and qulified personel to fill up the advertized vacancy', 'g12, diploma in any displine', 'full-time', 1, '2026-06-12', 'open', '2026-06-07 18:24:35', '2026-06-07 18:24:35');

-- --------------------------------------------------------

--
-- Table structure for table `examinations`
--

CREATE TABLE `examinations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('mid_term','final','supplementary','resit') NOT NULL DEFAULT 'final',
  `course_offering_id` bigint(20) UNSIGNED NOT NULL,
  `exam_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `invigilator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `max_marks` decimal(5,2) NOT NULL DEFAULT 100.00,
  `passing_marks` decimal(5,2) NOT NULL DEFAULT 40.00,
  `status` enum('scheduled','ongoing','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `examinations`
--

INSERT INTO `examinations` (`id`, `name`, `type`, `course_offering_id`, `exam_date`, `start_time`, `end_time`, `venue`, `invigilator_id`, `max_marks`, `passing_marks`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mock', 'mid_term', 8, '2026-06-07', '20:53:00', '20:53:00', 'Room1', 1, 100.00, 50.00, 'scheduled', '2026-06-06 18:53:47', '2026-06-08 18:42:52'),
(2, 'End of Term Exam', 'final', 8, '2026-06-06', '21:00:00', '21:00:00', 'Room1', 1, 100.00, 50.00, 'scheduled', '2026-06-06 19:00:54', '2026-06-08 18:42:33'),
(3, 'ASS1', 'supplementary', 8, '2026-06-08', '19:43:00', '19:43:00', 'Room1', 1, 100.00, 50.00, 'scheduled', '2026-06-08 17:43:30', '2026-06-08 17:44:48'),
(4, 'ASS 2', 'supplementary', 8, '2026-06-08', '20:44:00', '21:44:00', 'Room1', 1, 100.00, 50.00, 'scheduled', '2026-06-08 17:44:27', '2026-06-08 17:44:27');

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `examination_id` bigint(20) UNSIGNED NOT NULL,
  `course_offering_id` bigint(20) UNSIGNED NOT NULL,
  `marks_obtained` decimal(5,2) DEFAULT NULL,
  `grade_points` decimal(4,2) DEFAULT NULL,
  `grade` varchar(5) DEFAULT NULL,
  `is_absent` tinyint(1) NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `entered_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_results`
--

INSERT INTO `exam_results` (`id`, `student_id`, `examination_id`, `course_offering_id`, `marks_obtained`, `grade_points`, `grade`, `is_absent`, `remarks`, `entered_by`, `created_at`, `updated_at`) VALUES
(1, 8, 3, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 17:58:02', '2026-06-08 18:39:55'),
(2, 8, 4, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 17:58:18', '2026-06-08 18:40:13'),
(3, 2, 1, 6, 60.00, NULL, 'B-', 0, NULL, 4, '2026-06-08 17:58:32', '2026-06-08 17:58:32'),
(4, 8, 2, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 17:58:57', '2026-06-08 18:40:36'),
(5, 1, 3, 8, 50.00, NULL, 'C-', 0, NULL, 1, '2026-06-08 18:20:00', '2026-06-08 19:14:08'),
(6, 3, 3, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 18:20:00', '2026-06-08 18:45:25'),
(7, 1, 4, 8, 50.00, NULL, 'C-', 0, NULL, 1, '2026-06-08 18:20:28', '2026-06-08 19:14:23'),
(8, 3, 4, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 18:20:28', '2026-06-08 18:45:51'),
(9, 1, 2, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 18:21:22', '2026-06-08 18:46:40'),
(10, 3, 2, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 18:21:22', '2026-06-08 18:46:40'),
(11, 1, 1, 8, 50.00, NULL, 'C-', 0, NULL, 1, '2026-06-08 18:43:17', '2026-06-08 19:14:41'),
(12, 3, 1, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 18:43:17', '2026-06-08 18:43:17'),
(13, 8, 1, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 18:43:17', '2026-06-08 18:43:17');

-- --------------------------------------------------------

--
-- Table structure for table `exam_types`
--

CREATE TABLE `exam_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `category` enum('ca','exam','other') NOT NULL DEFAULT 'other',
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_types`
--

INSERT INTO `exam_types` (`id`, `name`, `code`, `category`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Assessment 2', 'assessment_2', 'ca', 'Continuous assessment test', 1, 2, '2026-06-08 18:56:02', '2026-06-08 19:10:35'),
(2, 'Midterm Examination', 'mid_term', 'ca', 'Mid-semester examination', 1, 2, '2026-06-08 18:56:02', '2026-06-08 19:11:01'),
(3, 'Final Examination', 'final', 'exam', 'End of semester examination', 1, 3, '2026-06-08 18:56:02', '2026-06-08 18:56:02'),
(4, 'Quiz', 'quiz', 'other', 'Short in-class quiz', 1, 4, '2026-06-08 18:56:02', '2026-06-08 19:09:40'),
(5, 'Assignment 1', 'assignment_1', 'ca', 'Take-home assignment', 1, 1, '2026-06-08 18:56:02', '2026-06-08 19:10:02'),
(6, 'Practical', 'practical', 'other', 'Lab or practical session', 1, 6, '2026-06-08 18:56:02', '2026-06-08 18:56:02');

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `dean_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`id`, `name`, `code`, `dean_id`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Faculty of Engineering', 'FOE', NULL, 'Engineering programs including Civil, Mechanical, Electrical and Computer Engineering', 'active', '2026-06-05 19:10:23', '2026-06-07 12:47:27', NULL),
(2, 'Faculty of Business Studies', 'FBS', NULL, 'Business administration, accounting, finance and economics programs', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(3, 'Faculty of Science', 'FOS', NULL, 'Natural sciences including Biology, Chemistry, Physics and Mathematics', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(4, 'Faculty of Information Technology', 'FOIT', NULL, 'Computer science, software engineering and information systems', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(5, 'Faculty of Education', 'FOEd', NULL, 'Teacher education and educational management programs', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(6, 'Faculty of Health Sciences', 'FOHS', NULL, 'Nursing, public health and biomedical science programs', 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fee_items`
--

CREATE TABLE `fee_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fee_structure_id` bigint(20) UNSIGNED NOT NULL,
  `fee_type` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_items`
--

INSERT INTO `fee_items` (`id`, `fee_structure_id`, `fee_type`, `description`, `amount`, `is_mandatory`, `created_at`, `updated_at`) VALUES
(1, 1, 'Tuition', 'Semester 1 Tuition Fees', 6500.00, 1, '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(2, 1, 'Library', 'Library Access Fee', 250.00, 1, '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(3, 1, 'Technology', 'Computer Lab & IT Fee', 500.00, 1, '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(4, 1, 'Medical', 'Student Health Insurance', 200.00, 1, '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(5, 1, 'Sports', 'Sports & Recreation Fee', 150.00, 0, '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(6, 1, 'Examination', 'Examination Registration Fee', 500.00, 1, '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(7, 1, 'Registration', 'Semester Registration Fee', 400.00, 1, '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(8, 7, 'Tuition', '', 2500.00, 1, '2026-06-07 17:06:31', '2026-06-07 17:06:31'),
(9, 7, 'Accommodation', '', 1500.00, 1, '2026-06-07 17:06:31', '2026-06-07 17:06:31'),
(10, 7, 'Medical', '', 200.00, 1, '2026-06-07 17:06:31', '2026-06-07 17:06:31'),
(11, 7, 'Examination', '', 520.00, 1, '2026-06-07 17:06:31', '2026-06-07 17:06:31'),
(12, 7, 'Registration', '', 150.00, 1, '2026-06-07 17:06:32', '2026-06-07 17:06:32');

-- --------------------------------------------------------

--
-- Table structure for table `fee_structures`
--

CREATE TABLE `fee_structures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `semester_id` bigint(20) UNSIGNED DEFAULT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_type` enum('local','international','both') NOT NULL DEFAULT 'both',
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_structures`
--

INSERT INTO `fee_structures` (`id`, `name`, `academic_year_id`, `semester_id`, `program_id`, `student_type`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 'BSSE Semester 1 Fees 2025/26', 2, 3, 1, 'local', 8500.00, 'active', '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(2, 'BSSE Semester 2 Fees 2025/26', 2, 4, 1, 'local', 8500.00, 'active', '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(3, 'BBA Semester 1 Fees 2025/26', 2, 3, 3, 'local', 7500.00, 'active', '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(4, 'BSN Semester 1 Fees 2025/26', 2, 3, 6, 'local', 9500.00, 'active', '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(5, 'BSSE International Sem 1 2025/26', 2, 3, 1, 'international', 15000.00, 'active', '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(7, '2026/2027 Regular fees', 3, 3, NULL, 'local', 4870.00, 'active', '2026-06-07 16:54:47', '2026-06-07 17:06:31');

-- --------------------------------------------------------

--
-- Table structure for table `final_results`
--

CREATE TABLE `final_results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_offering_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `semester_id` bigint(20) UNSIGNED NOT NULL,
  `ca_score` decimal(5,2) DEFAULT NULL,
  `exam_score` decimal(5,2) DEFAULT NULL,
  `total_score` decimal(5,2) DEFAULT NULL,
  `grade` varchar(5) DEFAULT NULL,
  `grade_points` decimal(3,1) DEFAULT NULL,
  `status` enum('pending','pass','fail','incomplete') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `final_results`
--

INSERT INTO `final_results` (`id`, `student_id`, `course_offering_id`, `academic_year_id`, `semester_id`, `ca_score`, `exam_score`, `total_score`, `grade`, `grade_points`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 6, 2, 4, 8.00, 0.00, 8.00, 'F', 0.0, 'fail', '2026-06-08 18:34:28', '2026-06-08 19:32:47'),
(2, 8, 8, 2, 4, 40.00, 60.00, 100.00, 'A+', 4.0, 'pass', '2026-06-08 18:34:29', '2026-06-08 19:32:57'),
(3, 1, 8, 2, 4, 20.00, 60.00, 80.00, 'A-', 3.7, 'pass', '2026-06-08 18:34:29', '2026-06-08 19:32:41'),
(4, 3, 8, 2, 4, 40.00, 60.00, 100.00, 'A+', 4.0, 'pass', '2026-06-08 18:34:29', '2026-06-08 19:32:52');

-- --------------------------------------------------------

--
-- Table structure for table `gpa_records`
--

CREATE TABLE `gpa_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `semester_id` bigint(20) UNSIGNED NOT NULL,
  `gpa` decimal(4,2) NOT NULL DEFAULT 0.00,
  `cgpa` decimal(4,2) NOT NULL DEFAULT 0.00,
  `credits_earned` int(11) NOT NULL DEFAULT 0,
  `total_credits_earned` int(11) NOT NULL DEFAULT 0,
  `academic_standing` varchar(50) NOT NULL DEFAULT 'Good Standing',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gpa_records`
--

INSERT INTO `gpa_records` (`id`, `student_id`, `academic_year_id`, `semester_id`, `gpa`, `cgpa`, `credits_earned`, `total_credits_earned`, `academic_standing`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 4, 3.70, 3.70, 3, 3, 'Dean\'s List', '2026-06-08 19:32:41', '2026-06-08 19:32:41'),
(2, 2, 2, 4, 0.00, 0.00, 3, 3, 'Academic Dismissal', '2026-06-08 19:32:47', '2026-06-08 19:32:47'),
(3, 3, 2, 4, 4.00, 4.00, 3, 3, 'Dean\'s List', '2026-06-08 19:32:52', '2026-06-08 19:32:52'),
(4, 8, 2, 4, 4.00, 4.00, 3, 3, 'Dean\'s List', '2026-06-08 19:32:57', '2026-06-08 19:32:57');

-- --------------------------------------------------------

--
-- Table structure for table `grade_scales`
--

CREATE TABLE `grade_scales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `grade` varchar(5) NOT NULL,
  `min_score` decimal(5,2) NOT NULL,
  `grade_points` decimal(4,2) NOT NULL DEFAULT 0.00,
  `label` varchar(60) DEFAULT NULL,
  `is_pass` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grade_scales`
--

INSERT INTO `grade_scales` (`id`, `grade`, `min_score`, `grade_points`, `label`, `is_pass`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'A+', 90.00, 4.00, 'Distinction', 1, 1, '2026-06-08 18:56:02', '2026-06-08 18:56:02'),
(2, 'A', 85.00, 4.00, 'Excellent', 1, 2, '2026-06-08 18:56:02', '2026-06-08 18:56:02'),
(3, 'A-', 80.00, 3.70, 'Very Good', 1, 3, '2026-06-08 18:56:02', '2026-06-08 18:56:02'),
(4, 'B+', 75.00, 3.30, 'Good', 1, 4, '2026-06-08 18:56:02', '2026-06-08 18:56:02'),
(5, 'B', 70.00, 3.00, 'Good', 1, 5, '2026-06-08 18:56:02', '2026-06-08 18:56:02'),
(6, 'B-', 65.00, 2.70, 'Above Average', 1, 6, '2026-06-08 18:56:02', '2026-06-08 18:56:02'),
(7, 'C+', 60.00, 2.30, 'Average', 1, 7, '2026-06-08 18:56:02', '2026-06-08 18:56:02'),
(8, 'C', 55.00, 2.00, 'Average', 1, 8, '2026-06-08 18:56:02', '2026-06-08 18:56:02'),
(9, 'C-', 50.00, 1.70, 'Pass', 1, 9, '2026-06-08 18:56:02', '2026-06-08 18:56:02'),
(10, 'D+', 45.00, 1.30, 'Pass', 1, 10, '2026-06-08 18:56:02', '2026-06-08 18:56:02'),
(11, 'D', 40.00, 1.00, 'Pass', 1, 11, '2026-06-08 18:56:02', '2026-06-08 18:56:02'),
(12, 'D-', 35.00, 0.70, 'Marginal', 0, 12, '2026-06-08 18:56:02', '2026-06-08 18:56:02'),
(13, 'F', 0.00, 0.00, 'Fail', 0, 13, '2026-06-08 18:56:02', '2026-06-08 18:56:02');

-- --------------------------------------------------------

--
-- Table structure for table `hostels`
--

CREATE TABLE `hostels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('male','female','mixed') NOT NULL DEFAULT 'mixed',
  `warden_id` bigint(20) UNSIGNED DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hostels`
--

INSERT INTO `hostels` (`id`, `name`, `type`, `warden_id`, `location`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Block A - Male Hostel', 'male', NULL, 'North Campus', 'Modern 3-storey male student accommodation with 120 rooms', 'active', '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(2, 'Block B - Female Hostel', 'female', NULL, 'South Campus', 'Modern 3-storey female student accommodation with 100 rooms', 'active', '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(3, 'Postgraduate Residences', 'mixed', NULL, 'East Campus', 'Self-contained postgraduate and international student accommodation', 'active', '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(4, 'Luangianga', 'male', 1, 'kalabo', 'male spacious hostel', 'active', '2026-06-06 10:41:25', '2026-06-06 10:41:25');

-- --------------------------------------------------------

--
-- Table structure for table `hostel_rooms`
--

CREATE TABLE `hostel_rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hostel_id` bigint(20) UNSIGNED NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `floor` tinyint(4) NOT NULL DEFAULT 1,
  `room_type` enum('single','double','triple') NOT NULL DEFAULT 'double',
  `capacity` tinyint(4) NOT NULL DEFAULT 2,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `status` enum('available','occupied','maintenance','reserved') NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hostel_rooms`
--

INSERT INTO `hostel_rooms` (`id`, `hostel_id`, `room_number`, `floor`, `room_type`, `capacity`, `amenities`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'A-101', 1, 'double', 2, NULL, 'available', '2026-06-05 19:10:25', '2026-06-08 13:26:34'),
(2, 1, 'A-102', 1, 'double', 2, NULL, 'available', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(3, 1, 'A-103', 1, 'single', 1, NULL, 'available', '2026-06-05 19:10:25', '2026-06-06 11:10:00'),
(4, 1, 'A-201', 2, 'double', 2, NULL, 'occupied', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(5, 1, 'A-202', 2, 'triple', 3, NULL, 'occupied', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(6, 2, 'B-101', 1, 'double', 2, NULL, 'available', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(7, 2, 'B-102', 1, 'double', 2, NULL, 'available', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(8, 2, 'B-201', 2, 'single', 1, NULL, 'occupied', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(9, 3, 'PG-101', 1, 'single', 1, NULL, 'available', '2026-06-05 19:10:25', '2026-06-05 19:10:25'),
(10, 3, 'PG-102', 1, 'single', 1, NULL, 'available', '2026-06-05 19:10:25', '2026-06-05 19:10:25');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `employee_id`, `leave_type_id`, `start_date`, `end_date`, `reason`, `attachment`, `status`, `approved_by`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 5, '2026-06-08', '2026-06-13', 'going for residentials', NULL, 'rejected', NULL, 'reshedule you appointment to other weeks as this leave did not give us chance to plan for how we should work in your absencia. next time request on time.', '2026-06-07 17:23:33', '2026-06-07 17:25:45'),
(2, 2, 2, '2026-06-07', '2026-06-08', 'am on medcs', NULL, 'approved', NULL, 'Approved', '2026-06-07 17:26:28', '2026-06-07 17:26:31'),
(3, 1, 5, '2026-05-10', '2026-05-16', 'study residentials', NULL, 'approved', NULL, 'Approved', '2026-06-07 17:31:34', '2026-06-07 17:31:46'),
(4, 1, 2, '2026-06-08', '2026-06-09', 'hi', NULL, 'pending', NULL, NULL, '2026-06-08 12:28:09', '2026-06-08 12:28:09');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `days_allowed` int(11) NOT NULL DEFAULT 14,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `name`, `days_allowed`, `is_paid`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Annual Leave', 30, 1, 'Annual vacation leave entitlement', '2026-06-05 19:10:26', '2026-06-05 19:10:26'),
(2, 'Sick Leave', 30, 1, 'Medical sick leave with doctor certificate', '2026-06-05 19:10:26', '2026-06-05 19:10:26'),
(3, 'Maternity Leave', 90, 1, 'Maternity leave for female employees', '2026-06-05 19:10:26', '2026-06-05 19:10:26'),
(4, 'Paternity Leave', 5, 1, 'Paternity leave for male employees on birth of child', '2026-06-05 19:10:26', '2026-06-05 19:10:26'),
(5, 'Study Leave', 10, 1, 'Academic study or examination leave', '2026-06-05 19:10:26', '2026-06-05 19:10:26'),
(6, 'Compassionate Leave', 5, 1, 'Bereavement or family emergency leave', '2026-06-05 19:10:26', '2026-06-05 19:10:26'),
(7, 'Unpaid Leave', 30, 0, 'Leave without pay - subject to HOD approval', '2026-06-05 19:10:26', '2026-06-05 19:10:26');

-- --------------------------------------------------------

--
-- Table structure for table `library_books`
--

CREATE TABLE `library_books` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `book_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `title` varchar(500) NOT NULL,
  `author` varchar(255) NOT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `publication_year` year(4) DEFAULT NULL,
  `edition` varchar(20) DEFAULT NULL,
  `copies_total` int(11) NOT NULL DEFAULT 1,
  `copies_available` int(11) NOT NULL DEFAULT 1,
  `shelf_location` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `library_books`
--

INSERT INTO `library_books` (`id`, `book_category_id`, `isbn`, `title`, `author`, `publisher`, `publication_year`, `edition`, `copies_total`, `copies_available`, `shelf_location`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '978-0-13-468599-1', 'Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', 'Prentice Hall', '2008', '1st', 5, 4, 'CS-A1', NULL, '2026-06-05 19:10:26', '2026-06-05 19:10:26', NULL),
(2, 1, '978-0-596-51774-8', 'JavaScript: The Good Parts', 'Douglas Crockford', 'O\'Reilly Media', '2008', '1st', 3, 3, 'CS-A2', NULL, '2026-06-05 19:10:26', '2026-06-05 19:10:26', NULL),
(3, 1, '978-0-13-110362-7', 'The C Programming Language', 'Brian Kernighan & Dennis Ritchie', 'Prentice Hall', '1988', '2nd', 4, 4, 'CS-A3', NULL, '2026-06-05 19:10:26', '2026-06-05 19:10:26', NULL),
(4, 3, '978-0-07-340183-7', 'Principles of Management', 'Harold Koontz', 'McGraw-Hill', '2017', '14th', 6, 5, 'BM-B1', NULL, '2026-06-05 19:10:26', '2026-06-05 19:10:26', NULL),
(5, 4, '978-0-07-811100-6', 'Financial Accounting', 'Jan Williams', 'McGraw-Hill', '2021', '17th', 8, 6, 'AF-C1', NULL, '2026-06-05 19:10:26', '2026-06-08 14:24:35', NULL),
(6, 5, '978-0-13-110362-7', 'Calculus: Early Transcendentals', 'James Stewart', 'Cengage Learning', '2020', '9th', 5, 5, 'MATH-D1', NULL, '2026-06-05 19:10:26', '2026-06-05 19:10:26', NULL),
(7, 2, '978-0-07-339811-7', 'Mechanics of Materials', 'Ferdinand Beer', 'McGraw-Hill', '2020', '8th', 4, 4, 'ENG-E1', NULL, '2026-06-05 19:10:26', '2026-06-05 19:10:26', NULL),
(8, 1, '151520', 'HTML BASICS', 'Jairos Sibusenga', 'zamco', '2020', NULL, 1, 0, NULL, 'for beginers', '2026-06-06 11:17:17', '2026-06-06 11:42:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sender_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `receiver_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `attachment` varchar(255) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `subject`, `content`, `parent_id`, `sender_deleted`, `receiver_deleted`, `attachment`, `read_at`, `is_read`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 10, 1, 'i want to enroll', 'i want to enroll but i cant see the enrollment page', NULL, 0, 0, NULL, '2026-06-07 10:37:14', 1, '2026-06-07 10:36:02', '2026-06-07 10:37:14', NULL),
(2, 1, 10, 'Re: i want to enroll', 'ok', 1, 0, 0, NULL, '2026-06-07 11:00:02', 1, '2026-06-07 10:51:23', '2026-06-07 11:00:02', NULL),
(3, 4, 1, 'food shortage', 'boi food wala', NULL, 0, 0, NULL, NULL, 0, '2026-06-08 12:35:50', '2026-06-08 12:35:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_01_01_000001_create_users_table', 1),
(2, '2024_01_01_000002_create_password_reset_tokens_table', 1),
(3, '2024_01_01_000003_create_sessions_table', 1),
(4, '2024_01_01_000004_create_personal_access_tokens_table', 1),
(5, '2024_01_01_000005_create_permission_tables', 1),
(6, '2024_01_02_000001_create_academic_tables', 1),
(7, '2024_01_02_000002_create_student_staff_tables', 1),
(8, '2024_01_02_000003_create_course_management_tables', 1),
(9, '2024_01_02_000004_create_results_tables', 1),
(10, '2024_01_02_000005_create_finance_tables', 1),
(11, '2024_01_02_000006_create_hostel_tables', 1),
(12, '2024_01_02_000007_create_library_tables', 1),
(13, '2024_01_02_000008_create_hr_tables', 1),
(14, '2024_01_02_000009_create_other_tables', 1),
(15, '2026_06_07_131324_add_sponsor_to_students_table', 2),
(16, '2026_06_07_153513_change_course_type_to_varchar_in_courses_table', 3),
(17, '2026_06_07_194516_create_employment_listings_table', 4),
(18, '2026_06_07_194516_create_salary_advances_table', 5),
(19, '2026_06_07_194517_create_employee_appointments_table', 6),
(20, '2026_06_07_204910_create_payroll_configurations_table', 7),
(21, '2026_06_07_204911_create_employee_allowances_table', 7),
(22, '2026_06_07_204912_create_employee_deductions_table', 7),
(23, '2026_06_07_210432_create_payroll_item_types_table', 8),
(24, '2026_06_07_213454_add_payroll_date_to_payroll_configurations', 9),
(25, '2026_06_08_130929_add_phone_address_to_users_table', 10),
(26, '2026_06_08_143227_add_category_is_public_to_documents_table', 11),
(27, '2026_06_08_141446_add_fine_tracking_to_book_borrowings_table', 12),
(28, '2026_06_08_192434_add_grade_columns_to_exam_results_table', 13),
(29, '2026_06_08_205526_create_grade_scales_and_exam_types_tables', 14);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 2),
(4, 'App\\Models\\User', 3),
(6, 'App\\Models\\User', 4),
(7, 'App\\Models\\User', 5),
(7, 'App\\Models\\User', 10),
(7, 'App\\Models\\User', 11),
(7, 'App\\Models\\User', 12),
(7, 'App\\Models\\User', 13),
(7, 'App\\Models\\User', 14),
(7, 'App\\Models\\User', 15),
(7, 'App\\Models\\User', 16),
(8, 'App\\Models\\User', 6),
(9, 'App\\Models\\User', 7),
(10, 'App\\Models\\User', 8),
(11, 'App\\Models\\User', 9);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `action_url` varchar(500) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `data`, `action_url`, `url`, `read_at`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 5, 'Result Published', 'Your result for Introduction to Computer Science has been published. Grade: A-.', 'result', '[]', 'http://127.0.0.1:8000/academic/results/student/1', NULL, NULL, 0, '2026-06-08 19:32:41', '2026-06-08 19:32:41'),
(2, 10, 'Result Published', 'Your result for Financial Accounting I has been published. Grade: F.', 'result', '[]', 'http://127.0.0.1:8000/academic/results/student/2', NULL, NULL, 0, '2026-06-08 19:32:47', '2026-06-08 19:32:47'),
(3, 11, 'Result Published', 'Your result for Introduction to Computer Science has been published. Grade: A+.', 'result', '[]', 'http://127.0.0.1:8000/academic/results/student/3', NULL, NULL, 0, '2026-06-08 19:32:53', '2026-06-08 19:32:53'),
(4, 16, 'Result Published', 'Your result for Introduction to Computer Science has been published. Grade: A+.', 'result', '[]', 'http://127.0.0.1:8000/academic/results/student/8', NULL, NULL, 0, '2026-06-08 19:32:57', '2026-06-08 19:32:57');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_bill_id` bigint(20) UNSIGNED NOT NULL,
  `reference_number` varchar(50) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'Cash',
  `transaction_reference` varchar(100) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','verified','reversed') NOT NULL DEFAULT 'verified',
  `recorded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `student_bill_id`, `reference_number`, `amount`, `payment_method`, `transaction_reference`, `payment_date`, `notes`, `status`, `recorded_by`, `verified_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'PAY/20260608/V4MLEX', 8500.00, 'Airtel Money', 'txn123', '2026-06-08', 'full payments towards school fees', 'verified', 1, NULL, '2026-06-08 14:55:29', '2026-06-08 14:55:29');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `month` tinyint(4) NOT NULL,
  `year` year(4) NOT NULL,
  `basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `allowances` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_date` date DEFAULT NULL,
  `status` enum('pending','processed','paid') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `employee_id`, `month`, `year`, `basic_salary`, `allowances`, `deductions`, `tax`, `net_pay`, `payment_date`, `status`, `notes`, `processed_by`, `created_at`, `updated_at`) VALUES
(1, 1, 6, '2026', 12500.00, 4375.00, 4936.88, 4093.13, 11938.13, '2026-06-06', 'processed', NULL, 1, '2026-06-06 12:24:22', '2026-06-06 12:34:57'),
(2, 2, 6, '2026', 9500.00, 3325.00, 3215.63, 2574.38, 9609.38, NULL, 'pending', NULL, 1, '2026-06-06 12:24:22', '2026-06-06 12:24:22'),
(3, 1, 1, '2026', 12500.00, 4375.00, 4936.88, 4093.13, 11938.13, NULL, 'pending', NULL, 1, '2026-06-06 15:33:32', '2026-06-06 15:33:32'),
(4, 2, 1, '2026', 9500.00, 3325.00, 3215.63, 2574.38, 9609.38, NULL, 'pending', NULL, 1, '2026-06-06 15:33:32', '2026-06-06 15:33:32'),
(5, 1, 7, '2026', 12500.00, 3125.00, 5515.21, 3624.38, 10109.79, NULL, 'pending', 'PAYE: 3,624.38 | NAPSA: 781.25 | NHIMA: 156.25 | Advance repayment: 833.33 | Other deductions: 120.00', 1, '2026-06-07 19:17:59', '2026-06-07 19:17:59'),
(6, 2, 7, '2026', 9500.00, 0.00, 1897.50, 1327.50, 7602.50, NULL, 'pending', 'PAYE: 1,327.50 | NAPSA: 475.00 | NHIMA: 95.00', 1, '2026-06-07 19:17:59', '2026-06-07 19:17:59');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_configurations`
--

CREATE TABLE `payroll_configurations` (
  `key` varchar(60) NOT NULL,
  `label` varchar(150) NOT NULL,
  `value` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `group` varchar(50) NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_configurations`
--

INSERT INTO `payroll_configurations` (`key`, `label`, `value`, `description`, `group`, `created_at`, `updated_at`) VALUES
('napsa_cap', 'NAPSA Monthly Cap (ZMW)', '1073', 'Maximum monthly NAPSA deduction', 'napsa', '2026-06-07 18:51:14', '2026-06-07 18:51:14'),
('napsa_rate', 'NAPSA Employee Contribution (%)', '5', 'Employee share of NAPSA contribution', 'napsa', '2026-06-07 18:51:14', '2026-06-07 18:51:14'),
('nhima_cap', 'NHIMA Monthly Cap (ZMW, 0=no cap)', '0', 'Maximum monthly NHIMA deduction (0=no cap)', 'nhima', '2026-06-07 18:51:14', '2026-06-07 18:51:14'),
('nhima_rate', 'NHIMA Contribution Rate (%)', '1', 'Employee share of NHIMA contribution', 'nhima', '2026-06-07 18:51:14', '2026-06-07 18:51:14'),
('paye_band1_max', 'PAYE Band 1 Upper Limit (Monthly)', '4800', 'Income up to this amount is tax-free', 'paye', '2026-06-07 18:51:14', '2026-06-07 18:51:14'),
('paye_band2_max', 'PAYE Band 2 Upper Limit (Monthly)', '6900', 'Upper limit for 25% tax band', 'paye', '2026-06-07 18:51:14', '2026-06-07 18:51:14'),
('paye_band2_rate', 'PAYE Band 2 Rate (%)', '25', 'Tax rate for band 2 income', 'paye', '2026-06-07 18:51:14', '2026-06-07 18:51:14'),
('paye_band3_max', 'PAYE Band 3 Upper Limit (Monthly)', '9200', 'Upper limit for 30% tax band', 'paye', '2026-06-07 18:51:14', '2026-06-07 18:51:14'),
('paye_band3_rate', 'PAYE Band 3 Rate (%)', '30', 'Tax rate for band 3 income', 'paye', '2026-06-07 18:51:14', '2026-06-07 18:51:14'),
('paye_band4_rate', 'PAYE Band 4 Rate (%)', '37.5', 'Tax rate for income above band 3', 'paye', '2026-06-07 18:51:14', '2026-06-07 18:51:14'),
('payroll_date', 'Monthly Payroll Date', '20', 'Day of the month on which payroll is processed (1–31)', 'general', '2026-06-07 19:35:35', '2026-06-07 19:37:49');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_item_types`
--

CREATE TABLE `payroll_item_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `category` varchar(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_item_types`
--

INSERT INTO `payroll_item_types` (`id`, `name`, `slug`, `category`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Housing Allowance', 'housing', 'allowance', 1, '2026-06-07 19:05:32', '2026-06-07 19:05:32'),
(2, 'Transport Allowance', 'transport', 'allowance', 1, '2026-06-07 19:05:32', '2026-06-07 19:05:32'),
(3, 'Medical Allowance', 'medical', 'allowance', 1, '2026-06-07 19:05:32', '2026-06-07 19:05:32'),
(4, 'Meal Allowance', 'meal', 'allowance', 1, '2026-06-07 19:05:32', '2026-06-07 19:05:32'),
(5, 'Entertainment Allowance', 'entertainment', 'allowance', 1, '2026-06-07 19:05:32', '2026-06-07 19:05:32'),
(6, 'Other Allowance', 'other_allowance', 'allowance', 1, '2026-06-07 19:05:32', '2026-06-07 19:05:32'),
(7, 'Loan Repayment', 'loan_repayment', 'deduction', 1, '2026-06-07 19:05:32', '2026-06-07 19:05:32'),
(8, 'Union Dues', 'union_dues', 'deduction', 1, '2026-06-07 19:05:32', '2026-06-07 19:05:32'),
(9, 'Other Deduction', 'other_deduction', 'deduction', 1, '2026-06-07 19:05:32', '2026-06-07 19:05:32'),
(10, 'Madson Insurance', 'madson_insurance', 'deduction', 1, '2026-06-07 19:11:57', '2026-06-07 19:11:57');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view-dashboard', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(2, 'manage-users', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(3, 'manage-roles', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(4, 'view-audit-logs', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(5, 'manage-settings', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(6, 'manage-academic', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(7, 'view-academic', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(8, 'manage-students', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(9, 'view-students', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(10, 'manage-admissions', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(11, 'view-admissions', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(12, 'manage-results', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(13, 'view-results', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(14, 'manage-exams', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(15, 'manage-attendance', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(16, 'view-attendance', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(17, 'manage-finance', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(18, 'view-finance', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(19, 'manage-billing', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(20, 'manage-payments', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(21, 'manage-scholarships', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(22, 'manage-hostel', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(23, 'view-hostel', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(24, 'manage-library', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(25, 'view-library', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(26, 'manage-hr', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(27, 'view-hr', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(28, 'manage-payroll', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(29, 'manage-assets', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(30, 'manage-research', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(31, 'view-research', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(32, 'create-announcement', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(33, 'manage-announcements', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(34, 'manage-documents', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(35, 'manage-support', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(36, 'manage-alumni', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(37, 'view-reports', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(38, 'manage-backup', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `level` enum('undergraduate','postgraduate','diploma','certificate') NOT NULL DEFAULT 'undergraduate',
  `duration_years` tinyint(4) NOT NULL DEFAULT 4,
  `credit_hours_required` int(11) NOT NULL DEFAULT 120,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `department_id`, `name`, `code`, `level`, `duration_years`, `credit_hours_required`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 6, 'Bachelor of Science in Software Engineering', 'BSSE', 'undergraduate', 4, 120, NULL, 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(2, 5, 'Bachelor of Science in Computer Science', 'BSCS', 'undergraduate', 4, 120, NULL, 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(3, 3, 'Bachelor of Business Administration', 'BBA', 'undergraduate', 4, 120, NULL, 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(4, 4, 'Bachelor of Accountancy', 'BAcc', 'undergraduate', 4, 120, NULL, 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(5, 1, 'Bachelor of Engineering (Civil)', 'BECiv', 'undergraduate', 5, 150, NULL, 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(6, 9, 'Bachelor of Science in Nursing', 'BSN', 'undergraduate', 4, 130, NULL, 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL),
(7, 6, 'Master of Science in Software Engineering', 'MSSE', 'postgraduate', 2, 60, NULL, 'active', '2026-06-05 19:10:23', '2026-06-07 13:04:55', NULL),
(8, 3, 'Diploma in Business Management', 'DBM', 'diploma', 2, 60, NULL, 'active', '2026-06-05 19:10:23', '2026-06-05 19:10:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `publications`
--

CREATE TABLE `publications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `research_project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(500) NOT NULL,
  `type` enum('journal','conference','book','thesis','report') NOT NULL DEFAULT 'journal',
  `publisher` varchar(255) DEFAULT NULL,
  `publication_year` year(4) NOT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `research_projects`
--

CREATE TABLE `research_projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(500) NOT NULL,
  `abstract` text NOT NULL,
  `principal_investigator_id` bigint(20) UNSIGNED NOT NULL,
  `co_investigators` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT NULL,
  `funding_source` varchar(255) DEFAULT NULL,
  `keywords` varchar(500) DEFAULT NULL,
  `status` enum('proposal','ongoing','completed','suspended') NOT NULL DEFAULT 'proposal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_projects`
--

INSERT INTO `research_projects` (`id`, `title`, `abstract`, `principal_investigator_id`, `co_investigators`, `start_date`, `end_date`, `budget`, `funding_source`, `keywords`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Graduate tracer study 2025', 'Assesing graduate employerbility and educational continuation for 2022-2024 cohorts', 1, 'moono', '2025-08-16', '2025-11-06', 5000.00, 'institutional', 'social science', 'completed', '2026-06-06 12:49:52', '2026-06-06 12:49:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(2, 'admin', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(3, 'registrar', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(4, 'finance-officer', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(5, 'finance-manager', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(6, 'lecturer', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(7, 'student', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(8, 'librarian', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(9, 'hostel-manager', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(10, 'hr-officer', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(11, 'it-admin', 'web', '2026-06-05 19:10:22', '2026-06-05 19:10:22');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(7, 6),
(7, 7),
(8, 1),
(8, 6),
(9, 1),
(9, 6),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(13, 6),
(13, 7),
(14, 1),
(14, 6),
(15, 1),
(16, 1),
(17, 1),
(17, 4),
(18, 1),
(18, 4),
(19, 1),
(19, 4),
(20, 1),
(20, 4),
(21, 1),
(22, 1),
(22, 9),
(23, 1),
(23, 7),
(23, 9),
(24, 1),
(24, 8),
(25, 1),
(25, 7),
(25, 8),
(26, 1),
(26, 10),
(27, 1),
(27, 10),
(28, 1),
(28, 4),
(29, 1),
(30, 1),
(30, 6),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(37, 7),
(37, 9),
(38, 1);

-- --------------------------------------------------------

--
-- Table structure for table `room_allocations`
--

CREATE TABLE `room_allocations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `hostel_room_id` bigint(20) UNSIGNED NOT NULL,
  `allocation_date` date NOT NULL,
  `expected_vacate_date` date DEFAULT NULL,
  `actual_vacate_date` date DEFAULT NULL,
  `status` enum('active','vacated','transferred') NOT NULL DEFAULT 'active',
  `allocated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_allocations`
--

INSERT INTO `room_allocations` (`id`, `student_id`, `hostel_room_id`, `allocation_date`, `expected_vacate_date`, `actual_vacate_date`, `status`, `allocated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-06-06', '2026-06-06', '2026-06-08', 'vacated', 1, '2026-06-06 11:09:01', '2026-06-08 13:26:34'),
(2, 2, 3, '2026-06-06', '2026-06-06', '2026-06-06', 'vacated', 1, '2026-06-06 11:09:46', '2026-06-06 11:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `salary_advances`
--

CREATE TABLE `salary_advances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `amount_requested` decimal(12,2) NOT NULL,
  `amount_approved` decimal(12,2) DEFAULT NULL,
  `reason` text NOT NULL,
  `request_date` date NOT NULL,
  `repayment_start_date` date DEFAULT NULL,
  `repayment_months` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `salary_advances`
--

INSERT INTO `salary_advances` (`id`, `employee_id`, `amount_requested`, `amount_approved`, `reason`, `request_date`, `repayment_start_date`, `repayment_months`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 2500.00, 2500.00, 'school fees', '2026-06-07', '2026-06-20', 3, 'approved', 'you have been awarded an advance successfully', '2026-06-07 17:59:01', '2026-06-07 18:00:17');

-- --------------------------------------------------------

--
-- Table structure for table `scholarships`
--

CREATE TABLE `scholarships` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('merit','need','sports','government','other') NOT NULL DEFAULT 'merit',
  `description` text DEFAULT NULL,
  `coverage_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `coverage_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_recipients` int(11) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scholarships`
--

INSERT INTO `scholarships` (`id`, `name`, `type`, `description`, `coverage_type`, `coverage_value`, `max_recipients`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Vice Chancellor Scholarship', 'merit', 'Full scholarship for top 5 students per faculty based on GPA ≥ 3.8', 'percentage', 100.00, 30, 'active', '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(2, 'Need-Based Financial Aid', 'need', 'Partial scholarship for financially disadvantaged students', 'percentage', 50.00, 100, 'active', '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(3, 'Government Bursary', 'government', 'Government of Zambia Higher Education Bursary', 'percentage', 75.00, 200, 'active', '2026-06-05 19:10:24', '2026-06-05 19:10:24'),
(4, 'Sports Excellence Award', 'sports', 'For student athletes representing the university at national level', 'fixed', 2500.00, 20, 'active', '2026-06-05 19:10:24', '2026-06-05 19:10:24');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_awards`
--

CREATE TABLE `scholarship_awards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `scholarship_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `award_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('active','suspended','completed') NOT NULL DEFAULT 'active',
  `awarded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scholarship_awards`
--

INSERT INTO `scholarship_awards` (`id`, `scholarship_id`, `student_id`, `award_date`, `notes`, `status`, `awarded_by`, `created_at`, `updated_at`) VALUES
(1, 3, 6, '2026-06-07', NULL, 'active', 1, '2026-06-07 13:19:10', '2026-06-07 13:19:10');

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `registration_start` date DEFAULT NULL,
  `registration_end` date DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('upcoming','registration','active','exam','completed') NOT NULL DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `academic_year_id`, `name`, `start_date`, `end_date`, `registration_start`, `registration_end`, `is_current`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Semester 1 - 2024/25', '2024-08-12', '2024-12-15', '2024-07-15', '2024-08-09', 0, 'completed', '2026-06-05 19:10:23', '2026-06-05 19:10:23'),
(2, 1, 'Semester 2 - 2024/25', '2025-01-13', '2025-05-30', '2024-12-16', '2025-01-10', 0, 'completed', '2026-06-05 19:10:23', '2026-06-05 19:10:23'),
(3, 2, 'Semester 1 - 2025/26', '2026-06-01', '2026-06-30', '2025-07-14', '2025-08-08', 0, 'active', '2026-06-05 19:10:23', '2026-06-07 13:55:59'),
(4, 2, 'Semester 2 - 2025/26', '2026-06-01', '2026-06-13', '2025-12-15', '2026-01-09', 1, 'active', '2026-06-05 19:10:23', '2026-06-07 13:55:59');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `qualifications` text DEFAULT NULL,
  `status` enum('active','inactive','on_leave') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `user_id`, `staff_id`, `department_id`, `designation`, `specialization`, `qualifications`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 'STAFF-2024-001', 6, 'Senior Lecturer', 'Software Engineering, Mobile Development', NULL, 'active', '2026-06-05 19:10:24', '2026-06-05 19:10:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL,
  `expected_graduation` date DEFAULT NULL,
  `year_of_study` tinyint(4) DEFAULT 1,
  `student_type` enum('local','international') NOT NULL DEFAULT 'local',
  `gender` enum('male','female','other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `sponsor` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended','graduated','withdrawn') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `student_id`, `program_id`, `enrollment_date`, `expected_graduation`, `year_of_study`, `student_type`, `gender`, `date_of_birth`, `nationality`, `national_id`, `phone`, `address`, `sponsor`, `photo`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 5, 'STU-2025-001', 3, '2025-08-11', NULL, 2, 'local', 'male', NULL, 'Zambian', NULL, '+260971000005', NULL, 'AP', 'students/photos/W318Xh4Ki4HncEdmwrsp10waTtBAWwhNmYyqQHRP.png', 'active', '2026-06-05 19:10:24', '2026-06-07 11:25:30', NULL),
(2, 10, 'STU00002', 2, '2026-06-06', NULL, 1, 'local', 'male', NULL, 'Zambian', NULL, '0966666666', 'winela', 'Liuwa CDF', 'students/photos/hGASoaJ6hAszdZXUbvUry72gfE4JoRNftqGlri48.png', 'active', '2026-06-06 08:25:47', '2026-06-08 18:16:23', NULL),
(3, 11, 'STU00003', 2, '2026-06-07', NULL, 1, 'local', 'female', NULL, 'Zambian', NULL, '977552233', 'Plot 12,Lusaka', 'Self', NULL, 'active', '2026-06-07 07:36:17', '2026-06-07 11:24:35', NULL),
(4, 12, 'STU00004', 2, '2026-06-07', NULL, 1, 'local', 'male', NULL, 'Zambian', NULL, '975096323', 'Plot 12,Lusaka', 'Liuwa CDF', NULL, 'active', '2026-06-07 07:36:18', '2026-06-07 11:24:08', NULL),
(5, 13, 'STU00005', 4, '2026-06-07', NULL, 1, 'local', 'male', NULL, 'Zambian', NULL, '755996644', 'ktti house no.1', 'Kalabo central CDF', NULL, 'active', '2026-06-07 07:45:39', '2026-06-07 11:23:10', NULL),
(6, 14, 'STU00006', 2, '2026-06-07', NULL, 1, 'local', 'female', NULL, 'Zambian', NULL, '755996674', 'ktti house no.1', 'Liuwa CDF', NULL, 'active', '2026-06-07 07:45:39', '2026-06-08 18:15:50', NULL),
(7, 15, 'STU00007', 4, '2026-06-07', NULL, 1, 'local', 'female', NULL, 'Zambian', NULL, '755996684', 'ktti house no.1', 'Sikongo CDF', NULL, 'active', '2026-06-07 07:45:40', '2026-06-07 11:22:37', NULL),
(8, 16, 'STU00008', 4, '2026-06-07', NULL, 1, 'local', 'male', NULL, 'Zambian', NULL, '755996694', 'ktti house no.1', 'GRZ', NULL, 'active', '2026-06-07 07:45:41', '2026-06-07 11:22:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_bills`
--

CREATE TABLE `student_bills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `fee_structure_id` bigint(20) UNSIGNED DEFAULT NULL,
  `academic_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `semester_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_bills`
--

INSERT INTO `student_bills` (`id`, `student_id`, `fee_structure_id`, `academic_year_id`, `semester_id`, `total_amount`, `amount_paid`, `balance`, `due_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 2, 4, 8500.00, 0.00, 8500.00, '2026-02-09', 'unpaid', '2026-06-06 09:58:38', '2026-06-06 09:58:38'),
(2, 2, 2, 2, 4, 8500.00, 8500.00, 0.00, '2026-02-09', 'paid', '2026-06-06 09:58:38', '2026-06-08 14:55:29'),
(3, 1, 3, 2, 3, 7500.00, 0.00, 7500.00, '2026-06-29', 'unpaid', '2026-06-07 17:07:46', '2026-06-07 17:07:46');

-- --------------------------------------------------------

--
-- Table structure for table `student_guardians`
--

CREATE TABLE `student_guardians` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_emergency_contact` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_guardians`
--

INSERT INTO `student_guardians` (`id`, `student_id`, `name`, `relationship`, `phone`, `email`, `address`, `is_emergency_contact`, `created_at`, `updated_at`) VALUES
(1, 8, 'Jairos', 'father', '+260971000005', 'j@gmail.com', 'kalabo trades\r\nkalabo', 1, '2026-06-07 12:30:17', '2026-06-07 12:30:17');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_number` varchar(20) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'other',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `ticket_number`, `user_id`, `subject`, `description`, `category`, `priority`, `status`, `assigned_to`, `attachment`, `created_at`, `updated_at`) VALUES
(1, 'TKT/20260608/XPUCG', 4, 'Forgotten PC Password', 'please i need your assistant so that i can do a presentation', 'technical', 'urgent', 'resolved', NULL, NULL, '2026-06-08 12:37:07', '2026-06-08 12:50:39'),
(2, 'TKT/20260608/TLURY', 4, 'i want to enroll', 'i want to enroll for my daughter', 'academic', 'medium', 'resolved', NULL, NULL, '2026-06-08 12:56:48', '2026-06-08 12:58:10');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_responses`
--

CREATE TABLE `ticket_responses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `support_ticket_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `response` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_responses`
--

INSERT INTO `ticket_responses` (`id`, `support_ticket_id`, `user_id`, `response`, `attachment`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'give me your session ID', NULL, '2026-06-08 12:48:52', '2026-06-08 12:48:52'),
(2, 1, 4, '102033', NULL, '2026-06-08 12:49:51', '2026-06-08 12:49:51'),
(3, 1, 1, 'am sure now its done, goodday', NULL, '2026-06-08 12:50:25', '2026-06-08 12:50:25'),
(4, 1, 4, 'thanks sir', NULL, '2026-06-08 12:54:31', '2026-06-08 12:54:31'),
(5, 1, 1, 'sure', NULL, '2026-06-08 12:55:40', '2026-06-08 12:55:40'),
(6, 2, 1, 'https://', NULL, '2026-06-08 12:58:04', '2026-06-08 12:58:04');

-- --------------------------------------------------------

--
-- Table structure for table `timetables`
--

CREATE TABLE `timetables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_offering_id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `timetables`
--

INSERT INTO `timetables` (`id`, `course_offering_id`, `day_of_week`, `start_time`, `end_time`, `venue`, `created_at`, `updated_at`) VALUES
(1, 1, 'Monday', '10:00:00', '11:00:00', NULL, '2026-06-06 08:46:50', '2026-06-06 08:46:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `address`, `avatar`, `status`, `deleted_at`, `is_active`, `last_login_at`, `last_login_ip`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrator', 'admin@university.com', '2026-06-05 19:10:22', '$2y$12$UnPYywMLf0ub16/FROH6l.ZQTRiV87y0L1M89lOoH7LwOReyNsgVm', '+260971000001', NULL, 'avatars/i7zw0lfQCepYYMh5FwYurwI0bOxly6MDtVm1J1Px.jpg', 'active', NULL, 1, '2026-06-08 16:18:31', '127.0.0.1', NULL, '2026-06-05 19:10:22', '2026-06-08 16:18:31'),
(2, 'Dr. James Mwale', 'registrar@university.com', '2026-06-05 19:10:22', '$2y$12$5A06YQQSkTVtHN4P58E9YuwjKBrIkt3dAwUqQLB0xXjxNY7.dr1pW', '+260971000002', NULL, NULL, 'active', NULL, 1, '2026-06-08 13:03:43', '127.0.0.1', NULL, '2026-06-05 19:10:22', '2026-06-08 13:03:43'),
(3, 'Mrs. Grace Banda', 'finance@university.com', '2026-06-05 19:10:22', '$2y$12$/3Gh3kPwlxCcZehtKR.vseVvEirxI1asjYbx2BWLuYO9D7WaEDk7G', '+260971000003', NULL, NULL, 'active', NULL, 1, '2026-06-08 15:18:07', '127.0.0.1', NULL, '2026-06-05 19:10:22', '2026-06-08 15:18:07'),
(4, 'Prof. David Tembo', 'lecturer@university.com', '2026-06-05 19:10:22', '$2y$12$c9RCECW7Z4RNcJ2LQKmtpeHeQzDqorjziihiFXcgfhSMyVx1pXfVG', '+260971000004', NULL, NULL, 'active', NULL, 1, '2026-06-08 16:17:34', '127.0.0.1', NULL, '2026-06-05 19:10:22', '2026-06-08 16:17:34'),
(5, 'John Phiri', 'student@university.com', '2026-06-05 19:10:22', '$2y$12$zqlyA2UPBn8m//07sy4yMu0pJPXKBYt1kv8ZxwQiDO7cpayweXlZK', '+260971000005', NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(6, 'Mr. Patrick Ngosa', 'librarian@university.com', '2026-06-05 19:10:22', '$2y$12$NBc.xqiVCbjXMIW5Uordh.Pc/J0JwAnMZPgCa7AT71GLBXHzAskPC', '+260971000006', NULL, NULL, 'active', NULL, 1, '2026-06-08 14:25:04', '127.0.0.1', NULL, '2026-06-05 19:10:22', '2026-06-08 14:25:04'),
(7, 'Mrs. Ruth Zulu', 'hostel@university.com', '2026-06-05 19:10:22', '$2y$12$9EfxvNdzdfaM1AFe27NPKOIh8AHcYJm3fwGsTo9cNtJk0zkli8Pd2', '+260971000007', NULL, NULL, 'active', NULL, 1, '2026-06-08 13:18:19', '127.0.0.1', NULL, '2026-06-05 19:10:22', '2026-06-08 13:18:19'),
(8, 'Mr. Charles Sikazwe', 'hr@university.com', '2026-06-05 19:10:22', '$2y$12$DtbkHa2tepNXgMCYyti9XeLVYaANiw0Otg2AhnZAOQ/Z8vcGnvBY.', '+260971000008', NULL, NULL, 'active', NULL, 1, '2026-06-08 13:57:52', '127.0.0.1', NULL, '2026-06-05 19:10:22', '2026-06-08 13:57:52'),
(9, 'IT Administrator', 'it@university.com', '2026-06-05 19:10:22', '$2y$12$zqlyA2UPBn8m//07sy4yMu0pJPXKBYt1kv8ZxwQiDO7cpayweXlZK', '+260971000009', NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-05 19:10:22', '2026-06-05 19:10:22'),
(10, 'student 1', 'student1@gmail.com', NULL, '$2y$12$ou463pvVUHJ1tfZing/Zy.q0p9XzV98sltS17UH.lxr03OiqCinbO', '0966666666', NULL, NULL, 'active', NULL, 1, '2026-06-08 19:44:32', '127.0.0.1', NULL, '2026-06-06 08:25:47', '2026-06-08 19:44:32'),
(11, 'Jane Banda', 'janebanda@university.com', NULL, '$2y$12$dFLjTQcCRPmTLOJ4zHcGPe86sSunqOQS2l0wZtpGfXA22ECtRNLdO', '977552233', NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-07 07:36:17', '2026-06-07 07:36:17'),
(12, 'moses tembo', 'mose@university.com', NULL, '$2y$12$WTpAKAQ36k01nG.g6Fi6/uf6s4T4AWnO7mUoi8YrE1I6g8WrfuJAG', '975096323', NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-07 07:36:17', '2026-06-07 07:36:17'),
(13, 'jack muso', 'jack@university.com', NULL, '$2y$12$vEna5XrERSgomfYwJD5A6udY1dP7.bZyhksaXJP/Up4spBrlrFzwe', '755996644', NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-07 07:45:39', '2026-06-07 07:45:39'),
(14, 'monde muso', 'mond@university.com', NULL, '$2y$12$9Hb0uExblsq4bgPQzUdw6ur3YxCjQgjmyA.GGSpf89ggfJlFNeMka', '755996674', NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-07 07:45:39', '2026-06-07 07:45:39'),
(15, 'mary muso', 'mary@university.com', NULL, '$2y$12$jSaq5GeaRcJyzpFn5yl8v.Rj7d/M.0goZUVrqaw92wKQ4lEuBtz26', '755996684', NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-07 07:45:40', '2026-06-07 07:45:40'),
(16, 'terry muso', 'terry@university.com', NULL, '$2y$12$Ld0PUlz5ItSUkcGgDle40eC9h1Z9ssgmcazp2f5K7BIj1PEsZdEB2', '755996694', NULL, NULL, 'active', NULL, 1, '2026-06-07 12:31:39', '127.0.0.1', NULL, '2026-06-07 07:45:41', '2026-06-07 12:31:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `academic_years_name_unique` (`name`);

--
-- Indexes for table `admissions`
--
ALTER TABLE `admissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admissions_application_number_unique` (`application_number`),
  ADD KEY `admissions_program_id_foreign` (`program_id`);

--
-- Indexes for table `alumni`
--
ALTER TABLE `alumni`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alumni_student_id_unique` (`student_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcements_user_id_foreign` (`user_id`);

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assets_asset_code_unique` (`asset_code`),
  ADD KEY `assets_department_id_foreign` (`department_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignments_course_offering_id_foreign` (`course_offering_id`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assignment_submissions_assignment_student_unique` (`assignment_id`,`student_id`),
  ADD KEY `assignment_submissions_student_id_foreign` (`student_id`);

--
-- Indexes for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_records_session_student_unique` (`attendance_session_id`,`student_id`),
  ADD KEY `attendance_records_student_id_foreign` (`student_id`);

--
-- Indexes for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_sessions_course_offering_id_foreign` (`course_offering_id`),
  ADD KEY `fk_att_sess_program` (`program_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_index` (`user_id`),
  ADD KEY `audit_logs_created_at_index` (`created_at`);

--
-- Indexes for table `bill_items`
--
ALTER TABLE `bill_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_items_student_bill_id_foreign` (`student_bill_id`);

--
-- Indexes for table `book_borrowings`
--
ALTER TABLE `book_borrowings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_borrowings_book_id_foreign` (`library_book_id`),
  ADD KEY `book_borrowings_student_id_foreign` (`student_id`),
  ADD KEY `book_borrowings_fine_collected_by_foreign` (`fine_collected_by`);

--
-- Indexes for table `book_categories`
--
ALTER TABLE `book_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `continuous_assessments`
--
ALTER TABLE `continuous_assessments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ca_student_offering_unique` (`student_id`,`course_offering_id`),
  ADD KEY `ca_course_offering_id_foreign` (`course_offering_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courses_code_unique` (`code`),
  ADD KEY `courses_department_id_foreign` (`department_id`);

--
-- Indexes for table `course_offerings`
--
ALTER TABLE `course_offerings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_offerings_course_id_foreign` (`course_id`),
  ADD KEY `course_offerings_semester_id_foreign` (`semester_id`),
  ADD KEY `course_offerings_lecturer_id_foreign` (`lecturer_id`),
  ADD KEY `fk_offering_acyear` (`academic_year_id`);

--
-- Indexes for table `course_program`
--
ALTER TABLE `course_program`
  ADD PRIMARY KEY (`course_id`,`program_id`);

--
-- Indexes for table `course_registrations`
--
ALTER TABLE `course_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_registrations_student_offering_unique` (`student_id`,`course_offering_id`),
  ADD KEY `course_registrations_course_offering_id_foreign` (`course_offering_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_code_unique` (`code`),
  ADD KEY `departments_faculty_id_foreign` (`faculty_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_student_id_foreign` (`student_id`),
  ADD KEY `documents_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_id_unique` (`employee_id`),
  ADD UNIQUE KEY `employees_user_id_unique` (`user_id`),
  ADD KEY `employees_department_id_foreign` (`department_id`);

--
-- Indexes for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_allowances_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `employee_appointments`
--
ALTER TABLE `employee_appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_appointments_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_appointments_department_id_foreign` (`department_id`);

--
-- Indexes for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_deductions_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `employment_listings`
--
ALTER TABLE `employment_listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employment_listings_department_id_foreign` (`department_id`);

--
-- Indexes for table `examinations`
--
ALTER TABLE `examinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `examinations_course_offering_id_foreign` (`course_offering_id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_results_student_exam_unique` (`student_id`,`examination_id`),
  ADD KEY `exam_results_examination_id_foreign` (`examination_id`);

--
-- Indexes for table `exam_types`
--
ALTER TABLE `exam_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_types_code_unique` (`code`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faculties_code_unique` (`code`);

--
-- Indexes for table `fee_items`
--
ALTER TABLE `fee_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_items_fee_structure_id_foreign` (`fee_structure_id`);

--
-- Indexes for table `fee_structures`
--
ALTER TABLE `fee_structures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_structures_academic_year_id_foreign` (`academic_year_id`);

--
-- Indexes for table `final_results`
--
ALTER TABLE `final_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `final_results_student_offering_unique` (`student_id`,`course_offering_id`),
  ADD KEY `final_results_course_offering_id_foreign` (`course_offering_id`),
  ADD KEY `final_results_semester_id_foreign` (`semester_id`);

--
-- Indexes for table `gpa_records`
--
ALTER TABLE `gpa_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gpa_records_student_semester_unique` (`student_id`,`semester_id`),
  ADD KEY `gpa_records_academic_year_id_foreign` (`academic_year_id`);

--
-- Indexes for table `grade_scales`
--
ALTER TABLE `grade_scales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grade_scales_grade_unique` (`grade`);

--
-- Indexes for table `hostels`
--
ALTER TABLE `hostels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hostel_rooms`
--
ALTER TABLE `hostel_rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hostel_rooms_hostel_room_unique` (`hostel_id`,`room_number`),
  ADD KEY `hostel_rooms_hostel_id_foreign` (`hostel_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_requests_employee_id_foreign` (`employee_id`),
  ADD KEY `leave_requests_leave_type_id_foreign` (`leave_type_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `library_books`
--
ALTER TABLE `library_books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `library_books_category_id_foreign` (`book_category_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_receiver_id_foreign` (`receiver_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_reference_number_unique` (`reference_number`),
  ADD KEY `payments_student_bill_id_foreign` (`student_bill_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payroll_employee_month_year_unique` (`employee_id`,`month`,`year`),
  ADD KEY `payroll_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `payroll_configurations`
--
ALTER TABLE `payroll_configurations`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `payroll_item_types`
--
ALTER TABLE `payroll_item_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payroll_item_types_slug_unique` (`slug`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `programs_code_unique` (`code`),
  ADD KEY `programs_department_id_foreign` (`department_id`);

--
-- Indexes for table `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publications_staff_id_foreign` (`staff_id`);

--
-- Indexes for table `research_projects`
--
ALTER TABLE `research_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `research_projects_pi_id_foreign` (`principal_investigator_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `fk_rhp_role` (`role_id`);

--
-- Indexes for table `room_allocations`
--
ALTER TABLE `room_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_allocations_student_id_foreign` (`student_id`),
  ADD KEY `room_allocations_hostel_room_id_foreign` (`hostel_room_id`);

--
-- Indexes for table `salary_advances`
--
ALTER TABLE `salary_advances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salary_advances_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scholarship_awards`
--
ALTER TABLE `scholarship_awards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scholarship_awards_scholarship_id_foreign` (`scholarship_id`),
  ADD KEY `scholarship_awards_student_id_foreign` (`student_id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `semesters_academic_year_id_foreign` (`academic_year_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_staff_id_unique` (`staff_id`),
  ADD UNIQUE KEY `staff_user_id_unique` (`user_id`),
  ADD KEY `staff_department_id_foreign` (`department_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_student_id_unique` (`student_id`),
  ADD UNIQUE KEY `students_user_id_unique` (`user_id`),
  ADD KEY `students_program_id_foreign` (`program_id`);

--
-- Indexes for table `student_bills`
--
ALTER TABLE `student_bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_bills_student_id_foreign` (`student_id`),
  ADD KEY `student_bills_academic_year_id_foreign` (`academic_year_id`),
  ADD KEY `fk_bill_fee_structure` (`fee_structure_id`);

--
-- Indexes for table `student_guardians`
--
ALTER TABLE `student_guardians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_guardians_student_id_foreign` (`student_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `support_tickets_ticket_number_unique` (`ticket_number`),
  ADD KEY `support_tickets_user_id_foreign` (`user_id`);

--
-- Indexes for table `ticket_responses`
--
ALTER TABLE `ticket_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_responses_ticket_id_foreign` (`support_ticket_id`),
  ADD KEY `ticket_responses_user_id_foreign` (`user_id`);

--
-- Indexes for table `timetables`
--
ALTER TABLE `timetables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `timetables_course_offering_id_foreign` (`course_offering_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admissions`
--
ALTER TABLE `admissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `alumni`
--
ALTER TABLE `alumni`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_records`
--
ALTER TABLE `attendance_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `bill_items`
--
ALTER TABLE `bill_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `book_borrowings`
--
ALTER TABLE `book_borrowings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `book_categories`
--
ALTER TABLE `book_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `continuous_assessments`
--
ALTER TABLE `continuous_assessments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `course_offerings`
--
ALTER TABLE `course_offerings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `course_registrations`
--
ALTER TABLE `course_registrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employee_appointments`
--
ALTER TABLE `employee_appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employment_listings`
--
ALTER TABLE `employment_listings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `examinations`
--
ALTER TABLE `examinations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `exam_types`
--
ALTER TABLE `exam_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `fee_items`
--
ALTER TABLE `fee_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `fee_structures`
--
ALTER TABLE `fee_structures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `final_results`
--
ALTER TABLE `final_results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gpa_records`
--
ALTER TABLE `gpa_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `grade_scales`
--
ALTER TABLE `grade_scales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `hostels`
--
ALTER TABLE `hostels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hostel_rooms`
--
ALTER TABLE `hostel_rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `library_books`
--
ALTER TABLE `library_books`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payroll_item_types`
--
ALTER TABLE `payroll_item_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `publications`
--
ALTER TABLE `publications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `research_projects`
--
ALTER TABLE `research_projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `room_allocations`
--
ALTER TABLE `room_allocations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `salary_advances`
--
ALTER TABLE `salary_advances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `scholarships`
--
ALTER TABLE `scholarships`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `scholarship_awards`
--
ALTER TABLE `scholarship_awards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `student_bills`
--
ALTER TABLE `student_bills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_guardians`
--
ALTER TABLE `student_guardians`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ticket_responses`
--
ALTER TABLE `ticket_responses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `timetables`
--
ALTER TABLE `timetables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `fk_announce_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD CONSTRAINT `fk_att_sess_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `book_borrowings`
--
ALTER TABLE `book_borrowings`
  ADD CONSTRAINT `book_borrowings_fine_collected_by_foreign` FOREIGN KEY (`fine_collected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_borrow_book` FOREIGN KEY (`library_book_id`) REFERENCES `library_books` (`id`),
  ADD CONSTRAINT `fk_borrow_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `fk_course_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `course_offerings`
--
ALTER TABLE `course_offerings`
  ADD CONSTRAINT `fk_offering_acyear` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  ADD CONSTRAINT `fk_offering_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `fk_offering_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_offering_sem` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`);

--
-- Constraints for table `course_registrations`
--
ALTER TABLE `course_registrations`
  ADD CONSTRAINT `fk_reg_offering` FOREIGN KEY (`course_offering_id`) REFERENCES `course_offerings` (`id`),
  ADD CONSTRAINT `fk_reg_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `fk_dept_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_emp_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_emp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD CONSTRAINT `employee_allowances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_appointments`
--
ALTER TABLE `employee_appointments`
  ADD CONSTRAINT `employee_appointments_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_appointments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  ADD CONSTRAINT `employee_deductions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employment_listings`
--
ALTER TABLE `employment_listings`
  ADD CONSTRAINT `employment_listings_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `final_results`
--
ALTER TABLE `final_results`
  ADD CONSTRAINT `fk_fr_offering` FOREIGN KEY (`course_offering_id`) REFERENCES `course_offerings` (`id`),
  ADD CONSTRAINT `fk_fr_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `hostel_rooms`
--
ALTER TABLE `hostel_rooms`
  ADD CONSTRAINT `fk_room_hostel` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`id`);

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `fk_leave_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `fk_leave_type` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `fk_mhp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `fk_mhr_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_bill` FOREIGN KEY (`student_bill_id`) REFERENCES `student_bills` (`id`);

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `fk_payroll_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `fk_prog_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `fk_rhp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rhp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_allocations`
--
ALTER TABLE `room_allocations`
  ADD CONSTRAINT `fk_alloc_room` FOREIGN KEY (`hostel_room_id`) REFERENCES `hostel_rooms` (`id`),
  ADD CONSTRAINT `fk_alloc_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `salary_advances`
--
ALTER TABLE `salary_advances`
  ADD CONSTRAINT `salary_advances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `semesters`
--
ALTER TABLE `semesters`
  ADD CONSTRAINT `fk_sem_acyear` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `fk_staff_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_staff_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_student_prog` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_bills`
--
ALTER TABLE `student_bills`
  ADD CONSTRAINT `fk_bill_fee_structure` FOREIGN KEY (`fee_structure_id`) REFERENCES `fee_structures` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_bill_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `student_guardians`
--
ALTER TABLE `student_guardians`
  ADD CONSTRAINT `fk_guardian_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
