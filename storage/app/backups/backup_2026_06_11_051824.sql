-- Database Backup: university_management_system
-- Generated: 2026-06-11 05:18:24
-- Laravel College Management System

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone="+00:00";

-- Table: `academic_years`
DROP TABLE IF EXISTS `academic_years`;
CREATE TABLE `academic_years` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('upcoming','active','completed') NOT NULL DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_years_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `academic_years` (`id`, `name`, `start_date`, `end_date`, `is_current`, `status`, `created_at`, `updated_at`) VALUES
  (1, '2024/2025', '2024-08-01', '2025-07-31', 0, 'completed', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (2, '2025/2026', '2025-08-01', '2026-07-31', 1, 'active', '2026-06-05 21:10:22', '2026-06-07 16:00:54'),
  (3, '2026/2027', '2026-08-01', '2027-07-31', 0, 'upcoming', '2026-06-05 21:10:22', '2026-06-07 16:00:54');

-- Table: `admissions`
DROP TABLE IF EXISTS `admissions`;
CREATE TABLE `admissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `application_number` varchar(30) NOT NULL,
  `program_id` bigint(20) unsigned DEFAULT NULL,
  `semester_id` bigint(20) unsigned DEFAULT NULL,
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
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admissions_application_number_unique` (`application_number`),
  KEY `admissions_program_id_foreign` (`program_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admissions` (`id`, `application_number`, `program_id`, `semester_id`, `first_name`, `last_name`, `middle_name`, `date_of_birth`, `gender`, `nationality`, `phone`, `email`, `address`, `previous_school`, `qualification_type`, `year_completed`, `grade`, `documents`, `status`, `rejection_reason`, `reviewed_by`, `reviewed_at`, `student_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 'APP/2026/00001', 1, 4, 'james', 'banda', NULL, '2000-01-01', 'male', 'Zambian', 0966666666, 'james@university.com', NULL, 'mtti', 'Diploma', 2021, 'merit', NULL, 'rejected', NULL, 1, '2026-06-07 08:49:15', NULL, '2026-06-07 08:47:20', '2026-06-07 08:49:15', NULL),
  (2, 'APP/2026/00002', 1, 4, 'james', 'banda', NULL, '2000-01-01', 'male', 'Zambian', 0966666666, 'james@university.com', NULL, 'mtti', 'Grade 12 Certificate', 2021, 'merit', NULL, 'approved', NULL, 1, '2026-06-07 08:51:44', NULL, '2026-06-07 08:51:10', '2026-06-07 08:51:44', NULL),
  (3, 'APP/2026/00003', 1, 4, 'mercy', 'Phiri', NULL, '2009-01-07', 'female', 'Zambian', +260971000005, 'mercy@university.com', 'wnl', 'mtti', 'Grade 12 Certificate', 2021, 'merit', '{\"certificates\":\"uploads\\/admissions\\/1780815419_certificates.pdf\",\"national_id\":\"uploads\\/admissions\\/1780815419_national_id.pdf\",\"photo\":\"uploads\\/admissions\\/1780815419_photo.jpeg\"}', 'approved', NULL, 1, '2026-06-07 08:57:26', NULL, '2026-06-07 08:56:59', '2026-06-07 08:57:26', NULL),
  (4, 'APP/2026/00004', 1, 4, 'sibweengwa', 'matron', NULL, '2000-08-18', 'female', 'Zambian', 0975096323, 'sibweengwam@gmail.com', 'klb,winela', 'kalabo secondary school', 'Grade 12 Certificate', 2018, 'credit', '{\"certificates\":\"uploads\\/admissions\\/1781102135_certificates.pdf\",\"national_id\":\"uploads\\/admissions\\/1781102135_national_id.pdf\",\"photo\":\"uploads\\/admissions\\/1781102135_photo.jpeg\"}', 'approved', NULL, 1, '2026-06-10 16:39:18', NULL, '2026-06-10 16:35:35', '2026-06-10 16:39:18', NULL),
  (5, 'APP/2026/00005', 1, 4, 'patson', 'moono', NULL, '2026-06-10', 'male', 'Zambian', 0966666666, 'patson@gmail.com', 'mununga,kalabo', 'kalabo secondary school', 'Grade 12 Certificate', 2026, 'merit', '{\"certificates\":\"uploads\\/admissions\\/1781103133_certificates.pdf\",\"national_id\":\"uploads\\/admissions\\/1781103133_national_id.pdf\",\"photo\":\"uploads\\/admissions\\/1781103133_photo.jpeg\"}', 'pending', NULL, NULL, NULL, NULL, '2026-06-10 16:52:13', '2026-06-10 16:52:13', NULL);

-- Table: `alumni`
DROP TABLE IF EXISTS `alumni`;
CREATE TABLE `alumni` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `graduation_year` year(4) NOT NULL,
  `current_employer` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `employment_status` enum('employed','self_employed','unemployed','further_studies') NOT NULL DEFAULT 'employed',
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `linkedin_url` varchar(500) DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumni_student_id_unique` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `alumni` (`id`, `student_id`, `graduation_year`, `current_employer`, `job_title`, `employment_status`, `city`, `country`, `linkedin_url`, `biography`, `created_at`, `updated_at`) VALUES
  (1, 1, 2026, 'ktti', 'software engineer', 'employed', 'klb', 'Zambia', NULL, NULL, '2026-06-06 15:05:29', '2026-06-06 15:05:29');

-- Table: `announcements`
DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_by` bigint(20) unsigned DEFAULT NULL,
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
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_user_id_foreign` (`user_id`),
  CONSTRAINT `fk_announce_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `announcements` (`id`, `user_id`, `is_published`, `published_by`, `published_at`, `send_email`, `send_sms`, `title`, `content`, `category`, `priority`, `target_audience`, `attachments`, `publish_date`, `expiry_date`, `views_count`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 1, 0, 1, NULL, 0, 0, 'holiday notice', 'we are closing tomorrow', 'academic', 'normal', '[\"all\"]', NULL, NULL, '2026-06-08 00:47:00', 0, '2026-06-06 00:47:48', '2026-06-06 00:47:48', NULL),
  (2, 1, 0, 1, NULL, 0, 0, 'New Academic Year Registration Open', 'Good news, our 2027 january intake application window is open now. come one come all kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkddddddddddddddddddddddddddkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh', 'general', 'normal', '[\"all\"]', NULL, NULL, NULL, 0, '2026-06-10 17:02:47', '2026-06-10 17:13:22', NULL),
  (3, 1, 0, 1, NULL, 0, 0, 'sports kuchalo', 'hhoza', 'general', 'normal', '[\"all\"]', '[\"announcements\\/ot9xbpOROGjpB1bWMFJkGiCp4NNOjfvcrduHT4DM.pdf\"]', NULL, '2026-07-10 17:22:00', 0, '2026-06-10 17:22:57', '2026-06-10 17:22:57', NULL);

-- Table: `assets`
DROP TABLE IF EXISTS `assets`;
CREATE TABLE `assets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned DEFAULT NULL,
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
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assets_asset_code_unique` (`asset_code`),
  KEY `assets_department_id_foreign` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `assets` (`id`, `department_id`, `name`, `asset_code`, `category`, `description`, `serial_number`, `purchase_date`, `purchase_price`, `current_value`, `location`, `warranty_expiry`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 5, 'Dell latitute laptop', 'AST 001', 'Electronics', '4 gb ram
1tb hdd
14\" screen
black color', 010005689, '2026-06-06', 10000.00, 10000.00, 'computer lab', '2026-06-20', 'active', '2026-06-06 14:43:30', '2026-06-06 14:43:30', NULL);

-- Table: `assignment_submissions`
DROP TABLE IF EXISTS `assignment_submissions`;
CREATE TABLE `assignment_submissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assignment_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `submission_text` text DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `marks_obtained` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_by` bigint(20) unsigned DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','submitted','graded','late') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assignment_submissions_assignment_student_unique` (`assignment_id`,`student_id`),
  KEY `assignment_submissions_student_id_foreign` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (no rows in `assignment_submissions`)

-- Table: `assignments`
DROP TABLE IF EXISTS `assignments`;
CREATE TABLE `assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_offering_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `total_marks` decimal(5,2) NOT NULL DEFAULT 100.00,
  `status` enum('draft','published','closed') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignments_course_offering_id_foreign` (`course_offering_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (no rows in `assignments`)

-- Table: `attendance_records`
DROP TABLE IF EXISTS `attendance_records`;
CREATE TABLE `attendance_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attendance_session_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL DEFAULT 'present',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_records_session_student_unique` (`attendance_session_id`,`student_id`),
  KEY `attendance_records_student_id_foreign` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `attendance_records` (`id`, `attendance_session_id`, `student_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
  (1, 1, 2, 'present', NULL, '2026-06-06 19:51:04', '2026-06-06 19:51:04'),
  (2, 2, 2, 'absent', NULL, '2026-06-06 20:40:06', '2026-06-06 20:40:06'),
  (3, 3, 2, 'present', NULL, '2026-06-06 20:42:53', '2026-06-06 20:42:53'),
  (4, 4, 1, 'present', NULL, '2026-06-06 20:45:25', '2026-06-06 20:45:25'),
  (5, 5, 3, 'present', NULL, '2026-06-07 15:58:44', '2026-06-07 15:58:44'),
  (6, 5, 4, 'present', NULL, '2026-06-07 15:58:44', '2026-06-07 15:58:44');

-- Table: `attendance_sessions`
DROP TABLE IF EXISTS `attendance_sessions`;
CREATE TABLE `attendance_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_offering_id` bigint(20) unsigned DEFAULT NULL,
  `program_id` bigint(20) unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `session_type` enum('lecture','lab','tutorial') NOT NULL DEFAULT 'lecture',
  `topic` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_sessions_course_offering_id_foreign` (`course_offering_id`),
  KEY `fk_att_sess_program` (`program_id`),
  CONSTRAINT `fk_att_sess_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `attendance_sessions` (`id`, `course_offering_id`, `program_id`, `date`, `session_type`, `topic`, `created_by`, `created_at`, `updated_at`) VALUES
  (1, NULL, 4, '2026-06-06', 'tutorial', NULL, NULL, '2026-06-06 19:51:04', '2026-06-06 19:51:04'),
  (2, 6, 4, '2026-06-06', 'lecture', NULL, NULL, '2026-06-06 20:40:06', '2026-06-06 20:40:06'),
  (3, 7, 4, '2026-06-06', 'lecture', NULL, NULL, '2026-06-06 20:42:53', '2026-06-06 20:42:53'),
  (4, 8, 2, '2026-06-06', 'lecture', 'types of computers', NULL, '2026-06-06 20:45:25', '2026-06-06 20:45:25'),
  (5, 8, 2, '2026-06-07', 'lecture', 'malware types', NULL, '2026-06-07 15:58:44', '2026-06-07 15:58:44');

-- Table: `audit_logs`
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(20) NOT NULL,
  `model_type` varchar(100) DEFAULT NULL,
  `model_id` bigint(20) unsigned DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `request_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_data`)),
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_index` (`user_id`),
  KEY `audit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  (90, 10, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (91, 10, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (92, 10, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (93, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (94, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (95, 10, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (96, 10, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (97, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (98, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (99, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (100, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL);
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `url`, `ip_address`, `user_agent`, `request_data`, `old_values`, `new_values`, `created_at`) VALUES
  (101, 16, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (102, 16, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (103, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (104, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (105, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (106, 2, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (107, 2, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (108, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (109, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (110, 16, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (111, 16, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (112, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (113, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (114, 16, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (115, 16, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (116, 16, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (117, 16, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (118, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (119, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (120, 4, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (121, 16, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (122, 16, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (123, 4, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (124, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (125, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (126, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (127, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (128, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (129, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (130, 1, 'LOGOUT', NULL, NULL, 'http://127.0.0.1:8000/logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (131, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (132, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL),
  (133, 1, 'LOGIN', NULL, NULL, 'http://127.0.0.1:8000/login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', NULL, NULL, NULL, NULL);

-- Table: `bill_items`
DROP TABLE IF EXISTS `bill_items`;
CREATE TABLE `bill_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_bill_id` bigint(20) unsigned NOT NULL,
  `fee_type` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bill_items_student_bill_id_foreign` (`student_bill_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bill_items` (`id`, `student_bill_id`, `fee_type`, `description`, `amount`, `discount`, `created_at`, `updated_at`) VALUES
  (1, 1, 'BSSE Semester 2 Fees 2025/26', 'BSSE Semester 2 Fees 2025/26', 8500.00, NULL, '2026-06-06 11:58:38', '2026-06-06 11:58:38'),
  (2, 2, 'BSSE Semester 2 Fees 2025/26', 'BSSE Semester 2 Fees 2025/26', 8500.00, NULL, '2026-06-06 11:58:38', '2026-06-06 11:58:38'),
  (3, 3, 'BBA Semester 1 Fees 2025/26', 'BBA Semester 1 Fees 2025/26', 7500.00, NULL, '2026-06-07 19:07:46', '2026-06-07 19:07:46');

-- Table: `book_borrowings`
DROP TABLE IF EXISTS `book_borrowings`;
CREATE TABLE `book_borrowings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `library_book_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `fine_amount` decimal(8,2) DEFAULT NULL,
  `fine_paid` tinyint(1) NOT NULL DEFAULT 0,
  `fine_paid_at` timestamp NULL DEFAULT NULL,
  `status` enum('borrowed','returned','overdue','lost') NOT NULL DEFAULT 'borrowed',
  `issued_by` bigint(20) unsigned DEFAULT NULL,
  `returned_to` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fine_collected_by` bigint(20) unsigned DEFAULT NULL,
  `fine_waived` tinyint(1) NOT NULL DEFAULT 0,
  `fine_waive_reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `book_borrowings_book_id_foreign` (`library_book_id`),
  KEY `book_borrowings_student_id_foreign` (`student_id`),
  KEY `book_borrowings_fine_collected_by_foreign` (`fine_collected_by`),
  CONSTRAINT `book_borrowings_fine_collected_by_foreign` FOREIGN KEY (`fine_collected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_borrow_book` FOREIGN KEY (`library_book_id`) REFERENCES `library_books` (`id`),
  CONSTRAINT `fk_borrow_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `book_borrowings` (`id`, `library_book_id`, `student_id`, `user_id`, `issue_date`, `due_date`, `return_date`, `fine_amount`, `fine_paid`, `fine_paid_at`, `status`, `issued_by`, `returned_to`, `created_at`, `updated_at`, `fine_collected_by`, `fine_waived`, `fine_waive_reason`) VALUES
  (1, 6, 1, NULL, '2026-06-07', '2026-06-09', '2026-06-10', 5.00, 0, NULL, 'returned', 1, 1, '2026-06-10 21:49:07', '2026-06-10 21:50:44', NULL, 0, NULL),
  (2, 5, 2, NULL, '2026-05-31', '2026-06-06', '2026-06-10', 20.00, 1, '2026-06-10 21:55:38', 'returned', 1, 1, '2026-06-10 21:49:48', '2026-06-10 21:55:38', 1, 0, NULL),
  (3, 6, 3, NULL, '2026-06-08', '2026-06-09', NULL, NULL, 0, NULL, 'borrowed', 1, NULL, '2026-06-10 21:51:40', '2026-06-10 21:51:40', NULL, 0, NULL);

-- Table: `book_categories`
DROP TABLE IF EXISTS `book_categories`;
CREATE TABLE `book_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `book_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
  (1, 'Computer Science', 'Programming, algorithms, artificial intelligence', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (2, 'Engineering', 'Civil, Mechanical, Electrical Engineering', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (3, 'Business & Management', 'Management, marketing, entrepreneurship', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (4, 'Accounting & Finance', 'Financial accounting, auditing, taxation', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (5, 'Mathematics', 'Pure and applied mathematics, statistics', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (6, 'Health Sciences', 'Nursing, medicine, public health', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (7, 'Law', 'Legal texts, case studies, jurisprudence', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (8, 'General Reference', 'Dictionaries, encyclopedias, atlases', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (9, 'African Studies', 'African history, culture, literature', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (10, 'Journals & Periodicals', 'Academic journals and research publications', '2026-06-05 21:10:25', '2026-06-05 21:10:25');

-- Table: `continuous_assessments`
DROP TABLE IF EXISTS `continuous_assessments`;
CREATE TABLE `continuous_assessments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `course_offering_id` bigint(20) unsigned NOT NULL,
  `ca_score` decimal(5,2) DEFAULT NULL,
  `max_ca_score` decimal(5,2) NOT NULL DEFAULT 30.00,
  `entered_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ca_student_offering_unique` (`student_id`,`course_offering_id`),
  KEY `ca_course_offering_id_foreign` (`course_offering_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (no rows in `continuous_assessments`)

-- Table: `course_offerings`
DROP TABLE IF EXISTS `course_offerings`;
CREATE TABLE `course_offerings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned NOT NULL,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `semester_id` bigint(20) unsigned NOT NULL,
  `lecturer_id` bigint(20) unsigned DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `schedule` varchar(255) DEFAULT NULL,
  `max_students` int(11) NOT NULL DEFAULT 50,
  `enrolled_students` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','cancelled','completed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_offerings_course_id_foreign` (`course_id`),
  KEY `course_offerings_semester_id_foreign` (`semester_id`),
  KEY `course_offerings_lecturer_id_foreign` (`lecturer_id`),
  KEY `fk_offering_acyear` (`academic_year_id`),
  CONSTRAINT `fk_offering_acyear` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  CONSTRAINT `fk_offering_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `fk_offering_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_offering_sem` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `course_offerings` (`id`, `course_id`, `academic_year_id`, `semester_id`, `lecturer_id`, `venue`, `schedule`, `max_students`, `enrolled_students`, `status`, `created_at`, `updated_at`) VALUES
  (1, 2, 2, 4, 1, 'Lecture Theatre 2', NULL, 60, 47, 'active', '2026-06-05 21:10:27', '2026-06-07 16:08:00'),
  (3, 11, 2, 4, 1, 'Room 301', NULL, 50, 39, 'active', '2026-06-05 21:10:27', '2026-06-07 16:08:00'),
  (4, 6, 2, 4, 1, 'Lecture Theatre 1', NULL, 60, 53, 'active', '2026-06-05 21:10:27', '2026-06-07 16:08:00'),
  (6, 8, 2, 4, 1, NULL, NULL, 50, 1, 'active', '2026-06-06 18:09:52', '2026-06-06 21:40:05'),
  (7, 7, 2, 4, 1, NULL, NULL, 50, 1, 'active', '2026-06-06 18:29:25', '2026-06-06 21:39:30'),
  (8, 5, 2, 4, 1, NULL, NULL, 50, 3, 'active', '2026-06-06 20:31:40', '2026-06-08 20:19:24');

-- Table: `course_program`
DROP TABLE IF EXISTS `course_program`;
CREATE TABLE `course_program` (
  `course_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `year_of_study` tinyint(4) DEFAULT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`course_id`,`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (no rows in `course_program`)

-- Table: `course_registrations`
DROP TABLE IF EXISTS `course_registrations`;
CREATE TABLE `course_registrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `course_offering_id` bigint(20) unsigned NOT NULL,
  `registered_by` bigint(20) unsigned DEFAULT NULL,
  `status` enum('registered','dropped','completed','failed') NOT NULL DEFAULT 'registered',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_registrations_student_offering_unique` (`student_id`,`course_offering_id`),
  KEY `course_registrations_course_offering_id_foreign` (`course_offering_id`),
  CONSTRAINT `fk_reg_offering` FOREIGN KEY (`course_offering_id`) REFERENCES `course_offerings` (`id`),
  CONSTRAINT `fk_reg_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `course_registrations` (`id`, `student_id`, `course_offering_id`, `registered_by`, `status`, `created_at`, `updated_at`) VALUES
  (1, 2, 8, 1, 'dropped', '2026-06-06 21:38:16', '2026-06-06 21:43:13'),
  (2, 2, 7, 1, 'registered', '2026-06-06 21:39:30', '2026-06-06 21:39:30'),
  (3, 2, 6, 1, 'registered', '2026-06-06 21:40:05', '2026-06-06 21:40:05'),
  (4, 8, 1, 1, 'registered', '2026-06-07 09:55:04', '2026-06-07 09:55:04'),
  (5, 8, 8, 1, 'registered', '2026-06-07 15:57:22', '2026-06-07 15:57:22'),
  (6, 2, 1, 1, 'registered', '2026-06-07 16:08:00', '2026-06-07 16:08:00'),
  (7, 2, 3, 1, 'registered', '2026-06-07 16:08:00', '2026-06-07 16:08:00'),
  (8, 2, 4, 1, 'registered', '2026-06-07 16:08:00', '2026-06-07 16:08:00'),
  (9, 1, 8, 1, 'registered', '2026-06-08 20:18:57', '2026-06-08 20:18:57'),
  (10, 3, 8, 1, 'registered', '2026-06-08 20:19:24', '2026-06-08 20:19:24');

-- Table: `courses`
DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned DEFAULT NULL,
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
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courses_code_unique` (`code`),
  KEY `courses_department_id_foreign` (`department_id`),
  CONSTRAINT `fk_course_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `courses` (`id`, `department_id`, `code`, `name`, `credits`, `level`, `course_type`, `description`, `prerequisites`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 6, 'SE101', 'Introduction to Software Engineering', 3, 100, 'core', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-05 21:10:27', NULL),
  (2, 6, 'SE201', 'Object-Oriented Programming', 4, 200, 'core', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-05 21:10:27', NULL),
  (3, 6, 'SE301', 'Software Design Patterns', 3, 300, 'core', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-05 21:10:27', NULL),
  (4, 6, 'SE401', 'Final Year Project', 6, 400, 'core', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-05 21:10:27', NULL),
  (5, 5, 'CS101', 'Introduction to Computer Science', 1, 100, 'practical', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-09 09:50:48', NULL),
  (6, 5, 'CS201', 'Data Structures and Algorithms', 4, 200, 'theory', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-07 15:47:48', NULL),
  (7, 3, 'BA101', 'Principles of Management', 3, 100, 'practical', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-07 15:38:13', NULL),
  (8, 4, 'AC101', 'Financial Accounting I', 3, 100, 'theory', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-07 15:37:48', NULL),
  (9, 6, 'SE211', 'Web Development', 3, 200, 'elective', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-05 21:10:27', NULL),
  (10, 6, 'SE212', 'Mobile Application Development', 3, 200, 'elective', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-05 21:10:27', NULL),
  (11, 5, 'CS301', 'Database Management Systems', 3, 300, 'core', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-05 21:10:27', NULL),
  (12, 5, 'CS302', 'Operating Systems', 3, 300, 'core', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-05 21:10:27', NULL),
  (13, 7, 'IS301', 'Network Administration', 3, 300, 'core', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-05 21:10:27', NULL),
  (14, 6, 'SE150', 'Programming Fundamentals Lab', 2, 100, 'lab', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-05 21:10:27', NULL),
  (15, 5, 'MATH101', 'Calculus for Engineers', 4, 100, 'core', NULL, NULL, 'active', '2026-06-05 21:10:27', '2026-06-05 21:10:27', NULL);

-- Table: `departments`
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `faculty_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `hod_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_code_unique` (`code`),
  KEY `departments_faculty_id_foreign` (`faculty_id`),
  CONSTRAINT `fk_dept_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `departments` (`id`, `faculty_id`, `name`, `code`, `hod_id`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 1, 'Department of Civil Engineering', 'DCE', 1, 'Structural, geotechnical and transportation engineering', 'active', '2026-06-05 21:10:23', '2026-06-07 14:58:47', NULL),
  (2, 1, 'Department of Electrical Engineering', 'DEE', NULL, 'Power systems, electronics and communications', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (3, 2, 'Department of Business Administration', 'DBA', NULL, 'Management, marketing and entrepreneurship', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (4, 2, 'Department of Accounting and Finance', 'DAF', NULL, 'Financial accounting, auditing and finance', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (5, 3, 'Department of Computer Science', 'DCS', NULL, 'Algorithms, data structures and theoretical computing', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (6, 4, 'Department of Software Engineering', 'DSE', NULL, 'Software development, testing and project management', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (7, 4, 'Department of Information Systems', 'DIS', NULL, 'Database systems, networking and cybersecurity', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (8, 5, 'Department of Primary Education', 'DPE', NULL, 'Primary school teacher training', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (9, 6, 'Department of Nursing', 'DON', NULL, 'Registered nursing and midwifery programs', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (10, 6, 'Department of Public Health', 'DPH', NULL, 'Epidemiology, health promotion and environmental health', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (11, 4, 'Business Department', 'BD', 1, NULL, 'active', '2026-06-07 15:00:01', '2026-06-07 15:00:01', NULL);

-- Table: `documents`
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('transcript','certificate','id_card','admission_letter','other') NOT NULL DEFAULT 'other',
  `category` varchar(100) DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `download_count` int(10) unsigned NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_student_id_foreign` (`student_id`),
  KEY `documents_uploaded_by_foreign` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (no rows in `documents`)

-- Table: `elearning_courses`
DROP TABLE IF EXISTS `elearning_courses`;
CREATE TABLE `elearning_courses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_offering_id` bigint(20) unsigned NOT NULL,
  `description` text DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `elearning_courses_course_offering_id_unique` (`course_offering_id`),
  KEY `elearning_courses_created_by_foreign` (`created_by`),
  CONSTRAINT `elearning_courses_course_offering_id_foreign` FOREIGN KEY (`course_offering_id`) REFERENCES `course_offerings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `elearning_courses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `elearning_courses` (`id`, `course_offering_id`, `description`, `is_published`, `created_by`, `created_at`, `updated_at`) VALUES
  (2, 1, 'basics of computer programing', 1, 4, '2026-06-09 10:16:13', '2026-06-09 10:16:13');

-- Table: `elearning_lesson_completions`
DROP TABLE IF EXISTS `elearning_lesson_completions`;
CREATE TABLE `elearning_lesson_completions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `lesson_id` bigint(20) unsigned NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `elearning_lesson_completions_student_id_lesson_id_unique` (`student_id`,`lesson_id`),
  KEY `elearning_lesson_completions_lesson_id_foreign` (`lesson_id`),
  CONSTRAINT `elearning_lesson_completions_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `elearning_lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `elearning_lesson_completions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `elearning_lesson_completions` (`id`, `student_id`, `lesson_id`, `completed_at`, `created_at`, `updated_at`) VALUES
  (1, 8, 1, '2026-06-09 11:43:02', '2026-06-09 11:43:02', '2026-06-09 11:43:02');

-- Table: `elearning_lesson_items`
DROP TABLE IF EXISTS `elearning_lesson_items`;
CREATE TABLE `elearning_lesson_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `content_type` enum('video_url','pdf_upload','text_html','external_link') NOT NULL DEFAULT 'text_html',
  `content` text NOT NULL,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `elearning_lesson_items_lesson_id_foreign` (`lesson_id`),
  CONSTRAINT `elearning_lesson_items_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `elearning_lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `elearning_lesson_items` (`id`, `lesson_id`, `title`, `content_type`, `content`, `sort_order`, `created_at`, `updated_at`) VALUES
  (1, 1, 'html explained', 'video_url', 'https://www.youtube.com/watch?v=it1rTvBcfRg', 1, '2026-06-09 10:40:11', '2026-06-09 10:40:11'),
  (2, 2, 'understanding css', 'video_url', 'https://www.youtube.com/watch?v=AGDDdsiZ0Ko&list=PLP9IO4UYNF0UCaUSF3XNZ1U9f01E5h5PM', 1, '2026-06-09 11:52:41', '2026-06-09 11:52:41');

-- Table: `elearning_lessons`
DROP TABLE IF EXISTS `elearning_lessons`;
CREATE TABLE `elearning_lessons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `elearning_course_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `elearning_lessons_elearning_course_id_foreign` (`elearning_course_id`),
  CONSTRAINT `elearning_lessons_elearning_course_id_foreign` FOREIGN KEY (`elearning_course_id`) REFERENCES `elearning_courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `elearning_lessons` (`id`, `elearning_course_id`, `title`, `description`, `sort_order`, `is_published`, `created_at`, `updated_at`) VALUES
  (1, 2, 'introduction to HTML', 'what is HTML?
html stands for hypertext markup language. it is used to buil any webpage', 1, 1, '2026-06-09 10:18:48', '2026-06-09 10:20:24'),
  (2, 2, 'css', 'painting', 2, 1, '2026-06-09 11:51:18', '2026-06-09 11:51:18');

-- Table: `elearning_question_options`
DROP TABLE IF EXISTS `elearning_question_options`;
CREATE TABLE `elearning_question_options` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint(20) unsigned NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `elearning_question_options_question_id_foreign` (`question_id`),
  CONSTRAINT `elearning_question_options_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `elearning_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `elearning_question_options` (`id`, `question_id`, `option_text`, `is_correct`, `sort_order`, `created_at`, `updated_at`) VALUES
  (21, 6, 'hood of tony mike lane', 0, 0, '2026-06-09 12:39:58', '2026-06-09 12:39:58'),
  (22, 6, 'hypertext markup language', 1, 1, '2026-06-09 12:39:58', '2026-06-09 12:39:58'),
  (23, 6, 'none', 0, 2, '2026-06-09 12:39:58', '2026-06-09 12:39:58'),
  (24, 6, 'all above', 0, 3, '2026-06-09 12:39:58', '2026-06-09 12:39:58'),
  (25, 7, 'cascading style sheet', 1, 0, '2026-06-09 12:41:04', '2026-06-09 12:41:04'),
  (26, 7, 'common canoe cop', 0, 1, '2026-06-09 12:41:04', '2026-06-09 12:41:04'),
  (27, 7, 'code', 0, 2, '2026-06-09 12:41:04', '2026-06-09 12:41:04'),
  (28, 7, 'coca co se', 0, 3, '2026-06-09 12:41:04', '2026-06-09 12:41:04');

-- Table: `elearning_questions`
DROP TABLE IF EXISTS `elearning_questions`;
CREATE TABLE `elearning_questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint(20) unsigned NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('single_choice','true_false') NOT NULL DEFAULT 'single_choice',
  `marks` smallint(5) unsigned NOT NULL DEFAULT 1,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `elearning_questions_quiz_id_foreign` (`quiz_id`),
  CONSTRAINT `elearning_questions_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `elearning_quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `elearning_questions` (`id`, `quiz_id`, `question_text`, `question_type`, `marks`, `sort_order`, `created_at`, `updated_at`) VALUES
  (6, 2, 'what is html', 'single_choice', 1, 1, '2026-06-09 12:39:58', '2026-06-09 12:39:58'),
  (7, 2, 'css stands for?', 'single_choice', 1, 2, '2026-06-09 12:41:04', '2026-06-09 12:41:04');

-- Table: `elearning_quiz_attempts`
DROP TABLE IF EXISTS `elearning_quiz_attempts`;
CREATE TABLE `elearning_quiz_attempts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `quiz_id` bigint(20) unsigned NOT NULL,
  `attempt_number` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `passed` tinyint(1) NOT NULL DEFAULT 0,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers`)),
  `started_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `elearning_quiz_attempts_student_id_foreign` (`student_id`),
  KEY `elearning_quiz_attempts_quiz_id_foreign` (`quiz_id`),
  CONSTRAINT `elearning_quiz_attempts_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `elearning_quizzes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `elearning_quiz_attempts_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `elearning_quiz_attempts` (`id`, `student_id`, `quiz_id`, `attempt_number`, `score`, `passed`, `answers`, `started_at`, `submitted_at`, `created_at`, `updated_at`) VALUES
  (1, 8, 1, 1, 50.00, 1, '{\"1\":\"1\",\"2\":\"5\",\"3\":\"9\",\"4\":\"13\"}', '2026-06-09 11:43:07', '2026-06-09 11:44:07', '2026-06-09 11:44:07', '2026-06-09 11:44:07'),
  (2, 8, 2, 1, 100.00, 1, '{\"6\":\"22\",\"7\":\"25\"}', '2026-06-09 12:41:14', '2026-06-09 12:42:14', '2026-06-09 12:42:14', '2026-06-09 12:42:14');

-- Table: `elearning_quizzes`
DROP TABLE IF EXISTS `elearning_quizzes`;
CREATE TABLE `elearning_quizzes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `elearning_course_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `time_limit_minutes` smallint(5) unsigned DEFAULT NULL,
  `passing_score` tinyint(3) unsigned NOT NULL DEFAULT 50,
  `max_attempts` tinyint(3) unsigned NOT NULL DEFAULT 3,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `elearning_quizzes_elearning_course_id_foreign` (`elearning_course_id`),
  CONSTRAINT `elearning_quizzes_elearning_course_id_foreign` FOREIGN KEY (`elearning_course_id`) REFERENCES `elearning_courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `elearning_quizzes` (`id`, `elearning_course_id`, `title`, `description`, `time_limit_minutes`, `passing_score`, `max_attempts`, `is_published`, `created_at`, `updated_at`) VALUES
  (1, 2, 'Chapter 1 Quizz', 'answer all questions', NULL, 5, 3, 1, '2026-06-09 10:28:00', '2026-06-09 10:28:00'),
  (2, 2, 'Chapter 1 Quizz', 'answer all questions', NULL, 50, 3, 1, '2026-06-09 12:38:13', '2026-06-09 12:38:13');

-- Table: `employee_allowances`
DROP TABLE IF EXISTS `employee_allowances`;
CREATE TABLE `employee_allowances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `allowance_type` varchar(60) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_allowances_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_allowances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `employee_allowances` (`id`, `employee_id`, `allowance_type`, `description`, `amount`, `percentage`, `is_active`, `created_at`, `updated_at`) VALUES
  (1, 1, 'housing', NULL, 0.00, 25.00, 1, '2026-06-07 21:16:17', '2026-06-07 21:16:17');

-- Table: `employee_appointments`
DROP TABLE IF EXISTS `employee_appointments`;
CREATE TABLE `employee_appointments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `position` varchar(255) NOT NULL,
  `appointment_date` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `salary` decimal(12,2) DEFAULT NULL,
  `contract_type` varchar(50) NOT NULL DEFAULT 'permanent',
  `notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_appointments_employee_id_foreign` (`employee_id`),
  KEY `employee_appointments_department_id_foreign` (`department_id`),
  CONSTRAINT `employee_appointments_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_appointments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (no rows in `employee_appointments`)

-- Table: `employee_deductions`
DROP TABLE IF EXISTS `employee_deductions`;
CREATE TABLE `employee_deductions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `deduction_type` varchar(60) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_deductions_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_deductions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `employee_deductions` (`id`, `employee_id`, `deduction_type`, `description`, `amount`, `is_recurring`, `is_active`, `created_at`, `updated_at`) VALUES
  (1, 1, 'madson_insurance', NULL, 120.00, 1, 1, '2026-06-07 21:16:51', '2026-06-07 21:16:51');

-- Table: `employee_documents`
DROP TABLE IF EXISTS `employee_documents`;
CREATE TABLE `employee_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `document_type` enum('nrc','cv','qualification','accreditation') NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` bigint(20) unsigned NOT NULL DEFAULT 0,
  `mime_type` varchar(255) DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_documents_employee_id_foreign` (`employee_id`),
  KEY `employee_documents_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `employee_documents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `employee_documents` (`id`, `employee_id`, `document_type`, `title`, `file_path`, `file_name`, `file_size`, `mime_type`, `uploaded_by`, `created_at`, `updated_at`) VALUES
  (1, 3, 'nrc', 'Certified NRC', 'employee_documents/3/Ot1dUFBrLSMc3NoeAXqXuwTKZOxiq0FUpadIqC6o.pdf', 'Nrc.pdf', 1364463, 'application/pdf', 1, '2026-06-11 02:18:39', '2026-06-11 02:18:39');

-- Table: `employees`
DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
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
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_id_unique` (`employee_id`),
  UNIQUE KEY `employees_user_id_unique` (`user_id`),
  KEY `employees_department_id_foreign` (`department_id`),
  CONSTRAINT `fk_emp_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_emp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `employees` (`id`, `user_id`, `department_id`, `employee_id`, `designation`, `employment_type`, `join_date`, `contract_end_date`, `basic_salary`, `bank_name`, `bank_account`, `national_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 4, 6, 'EMP-2024-001', 'Senior Lecturer', 'permanent', '2024-01-15', NULL, 12500.00, 'Natsave Bank', 10045856, NULL, 'active', '2026-06-05 21:10:27', '2026-06-10 21:15:54', NULL),
  (2, 8, 3, 'EMP-2024-002', 'HR Officer', 'permanent', '2024-03-01', NULL, 9500.00, 'FNB zambia', 01004521, NULL, 'active', '2026-06-05 21:10:27', '2026-06-10 21:15:22', NULL),
  (3, 17, 11, 'EMP/2026/0003', 'Lecturer', 'contract', '2026-06-11', NULL, 7000.00, NULL, NULL, '141414/14/1', 'active', '2026-06-11 02:18:07', '2026-06-11 02:18:07', NULL);

-- Table: `employment_listings`
DROP TABLE IF EXISTS `employment_listings`;
CREATE TABLE `employment_listings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `employment_type` varchar(50) NOT NULL DEFAULT 'full-time',
  `vacancies` smallint(5) unsigned NOT NULL DEFAULT 1,
  `deadline` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employment_listings_department_id_foreign` (`department_id`),
  CONSTRAINT `employment_listings_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `employment_listings` (`id`, `title`, `department_id`, `description`, `requirements`, `employment_type`, `vacancies`, `deadline`, `status`, `created_at`, `updated_at`) VALUES
  (1, 'Lecturer', 11, 'we are looking for ethusiastic and qulified personel to fill up the advertized vacancy', 'g12, diploma in any displine', 'full-time', 1, '2026-06-12', 'open', '2026-06-07 20:24:35', '2026-06-07 20:24:35');

-- Table: `exam_results`
DROP TABLE IF EXISTS `exam_results`;
CREATE TABLE `exam_results` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `examination_id` bigint(20) unsigned NOT NULL,
  `course_offering_id` bigint(20) unsigned NOT NULL,
  `marks_obtained` decimal(5,2) DEFAULT NULL,
  `grade_points` decimal(4,2) DEFAULT NULL,
  `grade` varchar(5) DEFAULT NULL,
  `is_absent` tinyint(1) NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `entered_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exam_results_student_exam_unique` (`student_id`,`examination_id`),
  KEY `exam_results_examination_id_foreign` (`examination_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `exam_results` (`id`, `student_id`, `examination_id`, `course_offering_id`, `marks_obtained`, `grade_points`, `grade`, `is_absent`, `remarks`, `entered_by`, `created_at`, `updated_at`) VALUES
  (1, 8, 3, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 19:58:02', '2026-06-08 20:39:55'),
  (2, 8, 4, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 19:58:18', '2026-06-08 20:40:13'),
  (3, 2, 1, 6, 60.00, NULL, 'B-', 0, NULL, 4, '2026-06-08 19:58:32', '2026-06-08 19:58:32'),
  (4, 8, 2, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 19:58:57', '2026-06-08 20:40:36'),
  (5, 1, 3, 8, 50.00, NULL, 'C-', 0, NULL, 1, '2026-06-08 20:20:00', '2026-06-08 21:14:08'),
  (6, 3, 3, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 20:20:00', '2026-06-08 20:45:25'),
  (7, 1, 4, 8, 50.00, NULL, 'C-', 0, NULL, 1, '2026-06-08 20:20:28', '2026-06-08 21:14:23'),
  (8, 3, 4, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 20:20:28', '2026-06-08 20:45:51'),
  (9, 1, 2, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 20:21:22', '2026-06-08 20:46:40'),
  (10, 3, 2, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 20:21:22', '2026-06-08 20:46:40'),
  (11, 1, 1, 8, 50.00, NULL, 'C-', 0, NULL, 1, '2026-06-08 20:43:17', '2026-06-08 21:14:41'),
  (12, 3, 1, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 20:43:17', '2026-06-08 20:43:17'),
  (13, 8, 1, 8, 100.00, NULL, 'A+', 0, NULL, 1, '2026-06-08 20:43:17', '2026-06-08 20:43:17');

-- Table: `exam_types`
DROP TABLE IF EXISTS `exam_types`;
CREATE TABLE `exam_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `category` enum('ca','exam','other') NOT NULL DEFAULT 'other',
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exam_types_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `exam_types` (`id`, `name`, `code`, `category`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
  (1, 'Assessment 2', 'assessment_2', 'ca', 'Continuous assessment test', 1, 2, '2026-06-08 20:56:02', '2026-06-08 21:10:35'),
  (2, 'Midterm Examination', 'mid_term', 'ca', 'Mid-semester examination', 1, 2, '2026-06-08 20:56:02', '2026-06-08 21:11:01'),
  (3, 'Final Examination', 'final', 'exam', 'End of semester examination', 1, 3, '2026-06-08 20:56:02', '2026-06-08 20:56:02'),
  (4, 'Quiz', 'quiz', 'other', 'Short in-class quiz', 1, 4, '2026-06-08 20:56:02', '2026-06-08 21:09:40'),
  (5, 'Assignment 1', 'assignment_1', 'ca', 'Take-home assignment', 1, 1, '2026-06-08 20:56:02', '2026-06-08 21:10:02'),
  (6, 'Practical', 'practical', 'other', 'Lab or practical session', 1, 6, '2026-06-08 20:56:02', '2026-06-08 20:56:02');

-- Table: `examinations`
DROP TABLE IF EXISTS `examinations`;
CREATE TABLE `examinations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('mid_term','final','supplementary','resit') NOT NULL DEFAULT 'final',
  `course_offering_id` bigint(20) unsigned NOT NULL,
  `exam_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `invigilator_id` bigint(20) unsigned DEFAULT NULL,
  `max_marks` decimal(5,2) NOT NULL DEFAULT 100.00,
  `passing_marks` decimal(5,2) NOT NULL DEFAULT 40.00,
  `status` enum('scheduled','ongoing','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `examinations_course_offering_id_foreign` (`course_offering_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `examinations` (`id`, `name`, `type`, `course_offering_id`, `exam_date`, `start_time`, `end_time`, `venue`, `invigilator_id`, `max_marks`, `passing_marks`, `status`, `created_at`, `updated_at`) VALUES
  (1, 'Mock', 'mid_term', 8, '2026-06-07', '20:53:00', '20:53:00', 'Room1', 1, 100.00, 50.00, 'scheduled', '2026-06-06 20:53:47', '2026-06-08 20:42:52'),
  (2, 'End of Term Exam', 'final', 8, '2026-06-06', '21:00:00', '21:00:00', 'Room1', 1, 100.00, 50.00, 'scheduled', '2026-06-06 21:00:54', '2026-06-08 20:42:33'),
  (3, 'ASS1', 'supplementary', 8, '2026-06-08', '19:43:00', '19:43:00', 'Room1', 1, 100.00, 50.00, 'scheduled', '2026-06-08 19:43:30', '2026-06-08 19:44:48'),
  (4, 'ASS 2', 'supplementary', 8, '2026-06-08', '20:44:00', '21:44:00', 'Room1', 1, 100.00, 50.00, 'scheduled', '2026-06-08 19:44:27', '2026-06-08 19:44:27');

-- Table: `faculties`
DROP TABLE IF EXISTS `faculties`;
CREATE TABLE `faculties` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `dean_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `faculties_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `faculties` (`id`, `name`, `code`, `dean_id`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 'Faculty of Engineering', 'FOE', NULL, 'Engineering programs including Civil, Mechanical, Electrical and Computer Engineering', 'active', '2026-06-05 21:10:23', '2026-06-07 14:47:27', NULL),
  (2, 'Faculty of Business Studies', 'FBS', NULL, 'Business administration, accounting, finance and economics programs', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (3, 'Faculty of Science', 'FOS', NULL, 'Natural sciences including Biology, Chemistry, Physics and Mathematics', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (4, 'Faculty of Information Technology', 'FOIT', NULL, 'Computer science, software engineering and information systems', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (5, 'Faculty of Education', 'FOEd', NULL, 'Teacher education and educational management programs', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (6, 'Faculty of Health Sciences', 'FOHS', NULL, 'Nursing, public health and biomedical science programs', 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL);

-- Table: `fee_items`
DROP TABLE IF EXISTS `fee_items`;
CREATE TABLE `fee_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fee_structure_id` bigint(20) unsigned NOT NULL,
  `fee_type` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_items_fee_structure_id_foreign` (`fee_structure_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `fee_items` (`id`, `fee_structure_id`, `fee_type`, `description`, `amount`, `is_mandatory`, `created_at`, `updated_at`) VALUES
  (1, 1, 'Tuition', 'Semester 1 Tuition Fees', 6500.00, 1, '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (2, 1, 'Library', 'Library Access Fee', 250.00, 1, '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (3, 1, 'Technology', 'Computer Lab & IT Fee', 500.00, 1, '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (4, 1, 'Medical', 'Student Health Insurance', 200.00, 1, '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (5, 1, 'Sports', 'Sports & Recreation Fee', 150.00, 0, '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (6, 1, 'Examination', 'Examination Registration Fee', 500.00, 1, '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (7, 1, 'Registration', 'Semester Registration Fee', 400.00, 1, '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (8, 7, 'Tuition', '', 2500.00, 1, '2026-06-07 19:06:31', '2026-06-07 19:06:31'),
  (9, 7, 'Accommodation', '', 1500.00, 1, '2026-06-07 19:06:31', '2026-06-07 19:06:31'),
  (10, 7, 'Medical', '', 200.00, 1, '2026-06-07 19:06:31', '2026-06-07 19:06:31'),
  (11, 7, 'Examination', '', 520.00, 1, '2026-06-07 19:06:31', '2026-06-07 19:06:31'),
  (12, 7, 'Registration', '', 150.00, 1, '2026-06-07 19:06:32', '2026-06-07 19:06:32');

-- Table: `fee_structures`
DROP TABLE IF EXISTS `fee_structures`;
CREATE TABLE `fee_structures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `academic_year_id` bigint(20) unsigned DEFAULT NULL,
  `semester_id` bigint(20) unsigned DEFAULT NULL,
  `program_id` bigint(20) unsigned DEFAULT NULL,
  `student_type` enum('local','international','both') NOT NULL DEFAULT 'both',
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_structures_academic_year_id_foreign` (`academic_year_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `fee_structures` (`id`, `name`, `academic_year_id`, `semester_id`, `program_id`, `student_type`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
  (1, 'BSSE Semester 1 Fees 2025/26', 2, 3, 1, 'local', 8500.00, 'active', '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (2, 'BSSE Semester 2 Fees 2025/26', 2, 4, 1, 'local', 8500.00, 'active', '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (3, 'BBA Semester 1 Fees 2025/26', 2, 3, 3, 'local', 7500.00, 'active', '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (4, 'BSN Semester 1 Fees 2025/26', 2, 3, 6, 'local', 9500.00, 'active', '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (5, 'BSSE International Sem 1 2025/26', 2, 3, 1, 'international', 15000.00, 'active', '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (7, '2026/2027 Regular fees', 3, 3, NULL, 'local', 4870.00, 'active', '2026-06-07 18:54:47', '2026-06-07 19:06:31');

-- Table: `final_results`
DROP TABLE IF EXISTS `final_results`;
CREATE TABLE `final_results` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `course_offering_id` bigint(20) unsigned NOT NULL,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `semester_id` bigint(20) unsigned NOT NULL,
  `ca_score` decimal(5,2) DEFAULT NULL,
  `exam_score` decimal(5,2) DEFAULT NULL,
  `total_score` decimal(5,2) DEFAULT NULL,
  `grade` varchar(5) DEFAULT NULL,
  `grade_points` decimal(3,1) DEFAULT NULL,
  `status` enum('pending','pass','fail','incomplete') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `final_results_student_offering_unique` (`student_id`,`course_offering_id`),
  KEY `final_results_course_offering_id_foreign` (`course_offering_id`),
  KEY `final_results_semester_id_foreign` (`semester_id`),
  CONSTRAINT `fk_fr_offering` FOREIGN KEY (`course_offering_id`) REFERENCES `course_offerings` (`id`),
  CONSTRAINT `fk_fr_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `final_results` (`id`, `student_id`, `course_offering_id`, `academic_year_id`, `semester_id`, `ca_score`, `exam_score`, `total_score`, `grade`, `grade_points`, `status`, `created_at`, `updated_at`) VALUES
  (1, 2, 6, 2, 4, 8.00, 0.00, 8.00, 'F', 0.0, 'fail', '2026-06-08 20:34:28', '2026-06-08 21:32:47'),
  (2, 8, 8, 2, 4, 40.00, 60.00, 100.00, 'A+', 4.0, 'pass', '2026-06-08 20:34:29', '2026-06-08 21:32:57'),
  (3, 1, 8, 2, 4, 20.00, 60.00, 80.00, 'A-', 3.7, 'pass', '2026-06-08 20:34:29', '2026-06-08 21:32:41'),
  (4, 3, 8, 2, 4, 40.00, 60.00, 100.00, 'A+', 4.0, 'pass', '2026-06-08 20:34:29', '2026-06-08 21:32:52');

-- Table: `gpa_records`
DROP TABLE IF EXISTS `gpa_records`;
CREATE TABLE `gpa_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `semester_id` bigint(20) unsigned NOT NULL,
  `gpa` decimal(4,2) NOT NULL DEFAULT 0.00,
  `cgpa` decimal(4,2) NOT NULL DEFAULT 0.00,
  `credits_earned` int(11) NOT NULL DEFAULT 0,
  `total_credits_earned` int(11) NOT NULL DEFAULT 0,
  `academic_standing` varchar(50) NOT NULL DEFAULT 'Good Standing',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gpa_records_student_semester_unique` (`student_id`,`semester_id`),
  KEY `gpa_records_academic_year_id_foreign` (`academic_year_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `gpa_records` (`id`, `student_id`, `academic_year_id`, `semester_id`, `gpa`, `cgpa`, `credits_earned`, `total_credits_earned`, `academic_standing`, `created_at`, `updated_at`) VALUES
  (1, 1, 2, 4, 3.70, 3.70, 3, 3, 'Dean\'s List', '2026-06-08 21:32:41', '2026-06-08 21:32:41'),
  (2, 2, 2, 4, 0.00, 0.00, 3, 3, 'Academic Dismissal', '2026-06-08 21:32:47', '2026-06-08 21:32:47'),
  (3, 3, 2, 4, 4.00, 4.00, 3, 3, 'Dean\'s List', '2026-06-08 21:32:52', '2026-06-08 21:32:52'),
  (4, 8, 2, 4, 4.00, 4.00, 3, 3, 'Dean\'s List', '2026-06-08 21:32:57', '2026-06-08 21:32:57');

-- Table: `grade_scales`
DROP TABLE IF EXISTS `grade_scales`;
CREATE TABLE `grade_scales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `grade` varchar(5) NOT NULL,
  `min_score` decimal(5,2) NOT NULL,
  `grade_points` decimal(4,2) NOT NULL DEFAULT 0.00,
  `label` varchar(60) DEFAULT NULL,
  `is_pass` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grade_scales_grade_unique` (`grade`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `grade_scales` (`id`, `grade`, `min_score`, `grade_points`, `label`, `is_pass`, `sort_order`, `created_at`, `updated_at`) VALUES
  (1, 'A+', 90.00, 4.00, 'Distinction', 1, 1, '2026-06-08 20:56:02', '2026-06-08 20:56:02'),
  (2, 'A', 85.00, 4.00, 'Excellent', 1, 2, '2026-06-08 20:56:02', '2026-06-08 20:56:02'),
  (3, 'A-', 80.00, 3.70, 'Very Good', 1, 3, '2026-06-08 20:56:02', '2026-06-08 20:56:02'),
  (4, 'B+', 75.00, 3.30, 'Good', 1, 4, '2026-06-08 20:56:02', '2026-06-08 20:56:02'),
  (5, 'B', 70.00, 3.00, 'Good', 1, 5, '2026-06-08 20:56:02', '2026-06-08 20:56:02'),
  (6, 'B-', 65.00, 2.70, 'Above Average', 1, 6, '2026-06-08 20:56:02', '2026-06-08 20:56:02'),
  (7, 'C+', 60.00, 2.30, 'Average', 1, 7, '2026-06-08 20:56:02', '2026-06-08 20:56:02'),
  (8, 'C', 55.00, 2.00, 'Average', 1, 8, '2026-06-08 20:56:02', '2026-06-08 20:56:02'),
  (9, 'C-', 50.00, 1.70, 'Pass', 1, 9, '2026-06-08 20:56:02', '2026-06-08 20:56:02'),
  (10, 'D+', 45.00, 1.30, 'Pass', 1, 10, '2026-06-08 20:56:02', '2026-06-08 20:56:02'),
  (11, 'D', 40.00, 1.00, 'Pass', 1, 11, '2026-06-08 20:56:02', '2026-06-08 20:56:02'),
  (12, 'D-', 35.00, 0.70, 'Marginal', 0, 12, '2026-06-08 20:56:02', '2026-06-08 20:56:02'),
  (13, 'F', 0.00, 0.00, 'Fail', 0, 13, '2026-06-08 20:56:02', '2026-06-08 20:56:02');

-- Table: `graduation_applications`
DROP TABLE IF EXISTS `graduation_applications`;
CREATE TABLE `graduation_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `ceremony_id` bigint(20) unsigned DEFAULT NULL,
  `cgpa` decimal(4,2) NOT NULL DEFAULT 0.00,
  `credits_earned` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` enum('pending','under_review','cleared','approved','rejected','graduated') NOT NULL DEFAULT 'pending',
  `finance_cleared` tinyint(1) NOT NULL DEFAULT 0,
  `library_cleared` tinyint(1) NOT NULL DEFAULT 0,
  `academic_cleared` tinyint(1) NOT NULL DEFAULT 0,
  `cleared_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `graduation_date` date DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `graduation_applications_student_id_unique` (`student_id`),
  KEY `graduation_applications_program_id_foreign` (`program_id`),
  KEY `graduation_applications_academic_year_id_foreign` (`academic_year_id`),
  KEY `graduation_applications_ceremony_id_foreign` (`ceremony_id`),
  KEY `graduation_applications_approved_by_foreign` (`approved_by`),
  CONSTRAINT `graduation_applications_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `graduation_applications_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `graduation_applications_ceremony_id_foreign` FOREIGN KEY (`ceremony_id`) REFERENCES `graduation_ceremonies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `graduation_applications_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `graduation_applications_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (no rows in `graduation_applications`)

-- Table: `graduation_ceremonies`
DROP TABLE IF EXISTS `graduation_ceremonies`;
CREATE TABLE `graduation_ceremonies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `ceremony_date` date NOT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `dress_code` varchar(255) DEFAULT NULL,
  `max_graduates` int(10) unsigned DEFAULT NULL,
  `status` enum('planned','confirmed','completed','cancelled') NOT NULL DEFAULT 'planned',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `graduation_ceremonies_academic_year_id_foreign` (`academic_year_id`),
  KEY `graduation_ceremonies_created_by_foreign` (`created_by`),
  CONSTRAINT `graduation_ceremonies_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `graduation_ceremonies_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (no rows in `graduation_ceremonies`)

-- Table: `hostel_rooms`
DROP TABLE IF EXISTS `hostel_rooms`;
CREATE TABLE `hostel_rooms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hostel_id` bigint(20) unsigned NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `floor` tinyint(4) NOT NULL DEFAULT 1,
  `room_type` enum('single','double','triple') NOT NULL DEFAULT 'double',
  `capacity` tinyint(4) NOT NULL DEFAULT 2,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `status` enum('available','occupied','maintenance','reserved') NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hostel_rooms_hostel_room_unique` (`hostel_id`,`room_number`),
  KEY `hostel_rooms_hostel_id_foreign` (`hostel_id`),
  CONSTRAINT `fk_room_hostel` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hostel_rooms` (`id`, `hostel_id`, `room_number`, `floor`, `room_type`, `capacity`, `amenities`, `status`, `created_at`, `updated_at`) VALUES
  (1, 1, 'A-101', 1, 'double', 2, NULL, 'available', '2026-06-05 21:10:25', '2026-06-08 15:26:34'),
  (2, 1, 'A-102', 1, 'double', 2, NULL, 'available', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (3, 1, 'A-103', 1, 'single', 1, NULL, 'available', '2026-06-05 21:10:25', '2026-06-06 13:10:00'),
  (4, 1, 'A-201', 2, 'double', 2, NULL, 'occupied', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (5, 1, 'A-202', 2, 'triple', 3, NULL, 'occupied', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (6, 2, 'B-101', 1, 'double', 2, NULL, 'available', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (7, 2, 'B-102', 1, 'double', 2, NULL, 'available', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (8, 2, 'B-201', 2, 'single', 1, NULL, 'occupied', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (9, 3, 'PG-101', 1, 'single', 1, NULL, 'available', '2026-06-05 21:10:25', '2026-06-05 21:10:25'),
  (10, 3, 'PG-102', 1, 'single', 1, NULL, 'available', '2026-06-05 21:10:25', '2026-06-05 21:10:25');

-- Table: `hostels`
DROP TABLE IF EXISTS `hostels`;
CREATE TABLE `hostels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('male','female','mixed') NOT NULL DEFAULT 'mixed',
  `warden_id` bigint(20) unsigned DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hostels` (`id`, `name`, `type`, `warden_id`, `location`, `description`, `status`, `created_at`, `updated_at`) VALUES
  (1, 'Block A - Male Hostel', 'male', NULL, 'North Campus', 'Modern 3-storey male student accommodation with 120 rooms', 'active', '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (2, 'Block B - Female Hostel', 'female', NULL, 'South Campus', 'Modern 3-storey female student accommodation with 100 rooms', 'active', '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (3, 'Postgraduate Residences', 'mixed', NULL, 'East Campus', 'Self-contained postgraduate and international student accommodation', 'active', '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (4, 'Luangianga', 'male', 1, 'kalabo', 'male spacious hostel', 'active', '2026-06-06 12:41:25', '2026-06-06 12:41:25');

-- Table: `leave_requests`
DROP TABLE IF EXISTS `leave_requests`;
CREATE TABLE `leave_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `leave_type_id` bigint(20) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_requests_employee_id_foreign` (`employee_id`),
  KEY `leave_requests_leave_type_id_foreign` (`leave_type_id`),
  CONSTRAINT `fk_leave_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk_leave_type` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `leave_requests` (`id`, `employee_id`, `leave_type_id`, `start_date`, `end_date`, `reason`, `attachment`, `status`, `approved_by`, `remarks`, `created_at`, `updated_at`) VALUES
  (1, 1, 5, '2026-06-08', '2026-06-13', 'going for residentials', NULL, 'rejected', NULL, 'reshedule you appointment to other weeks as this leave did not give us chance to plan for how we should work in your absencia. next time request on time.', '2026-06-07 19:23:33', '2026-06-07 19:25:45'),
  (2, 2, 2, '2026-06-07', '2026-06-08', 'am on medcs', NULL, 'approved', NULL, 'Approved', '2026-06-07 19:26:28', '2026-06-07 19:26:31'),
  (3, 1, 5, '2026-05-10', '2026-05-16', 'study residentials', NULL, 'approved', NULL, 'Approved', '2026-06-07 19:31:34', '2026-06-07 19:31:46'),
  (4, 1, 2, '2026-06-08', '2026-06-09', 'hi', NULL, 'pending', NULL, NULL, '2026-06-08 14:28:09', '2026-06-08 14:28:09');

-- Table: `leave_types`
DROP TABLE IF EXISTS `leave_types`;
CREATE TABLE `leave_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `days_allowed` int(11) NOT NULL DEFAULT 14,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `leave_types` (`id`, `name`, `days_allowed`, `is_paid`, `description`, `created_at`, `updated_at`) VALUES
  (1, 'Annual Leave', 30, 1, 'Annual vacation leave entitlement', '2026-06-05 21:10:26', '2026-06-05 21:10:26'),
  (2, 'Sick Leave', 30, 1, 'Medical sick leave with doctor certificate', '2026-06-05 21:10:26', '2026-06-05 21:10:26'),
  (3, 'Maternity Leave', 90, 1, 'Maternity leave for female employees', '2026-06-05 21:10:26', '2026-06-05 21:10:26'),
  (4, 'Paternity Leave', 5, 1, 'Paternity leave for male employees on birth of child', '2026-06-05 21:10:26', '2026-06-05 21:10:26'),
  (5, 'Study Leave', 10, 1, 'Academic study or examination leave', '2026-06-05 21:10:26', '2026-06-05 21:10:26'),
  (6, 'Compassionate Leave', 5, 1, 'Bereavement or family emergency leave', '2026-06-05 21:10:26', '2026-06-05 21:10:26'),
  (7, 'Unpaid Leave', 30, 0, 'Leave without pay - subject to HOD approval', '2026-06-05 21:10:26', '2026-06-05 21:10:26');

-- Table: `library_books`
DROP TABLE IF EXISTS `library_books`;
CREATE TABLE `library_books` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `book_category_id` bigint(20) unsigned DEFAULT NULL,
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
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `library_books_category_id_foreign` (`book_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `library_books` (`id`, `book_category_id`, `isbn`, `title`, `author`, `publisher`, `publication_year`, `edition`, `copies_total`, `copies_available`, `shelf_location`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 1, '978-0-13-468599-1', 'Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', 'Prentice Hall', 2008, '1st', 5, 4, 'CS-A1', NULL, '2026-06-05 21:10:26', '2026-06-10 21:47:55', NULL),
  (2, 1, '978-0-596-51774-8', 'JavaScript: The Good Parts', 'Douglas Crockford', 'O\'Reilly Media', 2008, '1st', 3, 3, 'CS-A2', NULL, '2026-06-05 21:10:26', '2026-06-05 21:10:26', NULL),
  (3, 1, '978-0-13-110362-7', 'The C Programming Language', 'Brian Kernighan & Dennis Ritchie', 'Prentice Hall', 1988, '2nd', 4, 4, 'CS-A3', NULL, '2026-06-05 21:10:26', '2026-06-10 21:34:47', NULL),
  (4, 3, '978-0-07-340183-7', 'Principles of Management', 'Harold Koontz', 'McGraw-Hill', 2017, '14th', 6, 5, 'BM-B1', NULL, '2026-06-05 21:10:26', '2026-06-05 21:10:26', NULL),
  (5, 4, '978-0-07-811100-6', 'Financial Accounting', 'Jan Williams', 'McGraw-Hill', 2021, '17th', 8, 7, 'AF-C1', NULL, '2026-06-05 21:10:26', '2026-06-10 21:50:33', NULL),
  (6, 5, '978-0-13-110362-7', 'Calculus: Early Transcendentals', 'James Stewart', 'Cengage Learning', 2020, '9th', 5, 4, 'MATH-D1', NULL, '2026-06-05 21:10:26', '2026-06-10 21:51:40', NULL),
  (7, 2, '978-0-07-339811-7', 'Mechanics of Materials', 'Ferdinand Beer', 'McGraw-Hill', 2020, '8th', 4, 4, 'ENG-E1', NULL, '2026-06-05 21:10:26', '2026-06-05 21:10:26', NULL),
  (8, 1, 151520, 'HTML BASICS', 'Jairos Sibusenga', 'zamco', 2020, NULL, 1, 0, NULL, 'for beginers', '2026-06-06 13:17:17', '2026-06-10 21:22:04', NULL);

-- Table: `messages`
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) unsigned NOT NULL,
  `receiver_id` bigint(20) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `sender_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `receiver_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `attachment` varchar(255) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  CONSTRAINT `fk_msg_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `subject`, `content`, `parent_id`, `sender_deleted`, `receiver_deleted`, `attachment`, `read_at`, `is_read`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 10, 1, 'i want to enroll', 'i want to enroll but i cant see the enrollment page', NULL, 0, 0, NULL, '2026-06-07 12:37:14', 1, '2026-06-07 12:36:02', '2026-06-07 12:37:14', NULL),
  (2, 1, 10, 'Re: i want to enroll', 'ok', 1, 0, 0, NULL, '2026-06-07 13:00:02', 1, '2026-06-07 12:51:23', '2026-06-07 13:00:02', NULL),
  (3, 4, 1, 'food shortage', 'boi food wala', NULL, 0, 0, NULL, NULL, 0, '2026-06-08 14:35:50', '2026-06-08 14:35:50', NULL);

-- Table: `migrations`
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  (29, '2026_06_08_205526_create_grade_scales_and_exam_types_tables', 14),
  (30, '2026_06_09_000001_create_research_papers_table', 15),
  (31, '2026_06_09_000002_create_graduation_tables', 16),
  (32, '2026_06_09_000003_create_elearning_tables', 17),
  (33, '2026_06_11_015844_create_employee_documents_table', 18);

-- Table: `model_has_permissions`
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `fk_mhp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (no rows in `model_has_permissions`)

-- Table: `model_has_roles`
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `fk_mhr_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- Table: `notifications`
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `action_url` varchar(500) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `data`, `action_url`, `url`, `read_at`, `is_read`, `created_at`, `updated_at`) VALUES
  (1, 5, 'Result Published', 'Your result for Introduction to Computer Science has been published. Grade: A-.', 'result', '[]', 'http://127.0.0.1:8000/academic/results/student/1', NULL, NULL, 0, '2026-06-08 21:32:41', '2026-06-08 21:32:41'),
  (2, 10, 'Result Published', 'Your result for Financial Accounting I has been published. Grade: F.', 'result', '[]', 'http://127.0.0.1:8000/academic/results/student/2', NULL, NULL, 0, '2026-06-08 21:32:47', '2026-06-08 21:32:47'),
  (3, 11, 'Result Published', 'Your result for Introduction to Computer Science has been published. Grade: A+.', 'result', '[]', 'http://127.0.0.1:8000/academic/results/student/3', NULL, NULL, 0, '2026-06-08 21:32:53', '2026-06-08 21:32:53'),
  (4, 16, 'Result Published', 'Your result for Introduction to Computer Science has been published. Grade: A+.', 'result', '[]', 'http://127.0.0.1:8000/academic/results/student/8', NULL, NULL, 0, '2026-06-08 21:32:57', '2026-06-08 21:32:57'),
  (5, 8, 'Payroll Processed', 'Your payroll for July 2026 has been processed. Net pay: ZMW 7,602.50.', 'payment', '[]', 'http://127.0.0.1:8000/hr/payroll', NULL, NULL, 0, '2026-06-10 21:06:27', '2026-06-10 21:06:27'),
  (6, 8, 'Payroll Processed', 'Your payroll for January 2026 has been processed. Net pay: ZMW 9,609.38.', 'payment', '[]', 'http://127.0.0.1:8000/hr/payroll', NULL, NULL, 0, '2026-06-10 21:06:44', '2026-06-10 21:06:44'),
  (7, 4, 'Payroll Processed', 'Your payroll for January 2026 has been processed. Net pay: ZMW 11,938.13.', 'payment', '[]', 'http://127.0.0.1:8000/hr/payroll', NULL, NULL, 0, '2026-06-10 21:08:07', '2026-06-10 21:08:07'),
  (8, 4, 'Payroll Processed', 'Your payroll for July 2026 has been processed. Net pay: ZMW 10,109.79.', 'payment', '[]', 'http://127.0.0.1:8000/hr/payroll', NULL, NULL, 0, '2026-06-10 21:08:13', '2026-06-10 21:08:13'),
  (9, 8, 'Payroll Processed', 'Your payroll for June 2026 has been processed. Net pay: ZMW 9,609.38.', 'payment', '[]', 'http://127.0.0.1:8000/hr/payroll', NULL, NULL, 0, '2026-06-10 21:08:20', '2026-06-10 21:08:20');

-- Table: `password_reset_tokens`
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
  ('admin@university.com', '$2y$12$D5De5KzIoToWtUEdeeXDGeWv40CKoOuwTIYH8ArvajbaNN.U8Zltq', '2026-06-10 19:30:26');

-- Table: `payments`
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_bill_id` bigint(20) unsigned NOT NULL,
  `reference_number` varchar(50) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'Cash',
  `transaction_reference` varchar(100) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','verified','reversed') NOT NULL DEFAULT 'verified',
  `recorded_by` bigint(20) unsigned DEFAULT NULL,
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_reference_number_unique` (`reference_number`),
  KEY `payments_student_bill_id_foreign` (`student_bill_id`),
  CONSTRAINT `fk_payment_bill` FOREIGN KEY (`student_bill_id`) REFERENCES `student_bills` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `payments` (`id`, `student_bill_id`, `reference_number`, `amount`, `payment_method`, `transaction_reference`, `payment_date`, `notes`, `status`, `recorded_by`, `verified_by`, `created_at`, `updated_at`) VALUES
  (1, 2, 'PAY/20260608/V4MLEX', 8500.00, 'Airtel Money', 'txn123', '2026-06-08', 'full payments towards school fees', 'verified', 1, NULL, '2026-06-08 16:55:29', '2026-06-08 16:55:29');

-- Table: `payroll`
DROP TABLE IF EXISTS `payroll`;
CREATE TABLE `payroll` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
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
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_employee_month_year_unique` (`employee_id`,`month`,`year`),
  KEY `payroll_employee_id_foreign` (`employee_id`),
  CONSTRAINT `fk_payroll_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `payroll` (`id`, `employee_id`, `month`, `year`, `basic_salary`, `allowances`, `deductions`, `tax`, `net_pay`, `payment_date`, `status`, `notes`, `processed_by`, `created_at`, `updated_at`) VALUES
  (1, 1, 6, 2026, 12500.00, 4375.00, 4936.88, 4093.13, 11938.13, '2026-06-06', 'processed', NULL, 1, '2026-06-06 14:24:22', '2026-06-06 14:34:57'),
  (2, 2, 6, 2026, 9500.00, 3325.00, 3215.63, 2574.38, 9609.38, '2026-06-20', 'processed', NULL, 1, '2026-06-06 14:24:22', '2026-06-10 21:08:20'),
  (3, 1, 1, 2026, 12500.00, 4375.00, 4936.88, 4093.13, 11938.13, '2026-01-20', 'processed', NULL, 1, '2026-06-06 17:33:32', '2026-06-10 21:08:07'),
  (4, 2, 1, 2026, 9500.00, 3325.00, 3215.63, 2574.38, 9609.38, '2026-01-20', 'processed', NULL, 1, '2026-06-06 17:33:32', '2026-06-10 21:06:44'),
  (5, 1, 7, 2026, 12500.00, 3125.00, 5515.21, 3624.38, 10109.79, '2026-07-20', 'processed', 'PAYE: 3,624.38 | NAPSA: 781.25 | NHIMA: 156.25 | Advance repayment: 833.33 | Other deductions: 120.00', 1, '2026-06-07 21:17:59', '2026-06-10 21:08:13'),
  (6, 2, 7, 2026, 9500.00, 0.00, 1897.50, 1327.50, 7602.50, '2026-07-20', 'processed', 'PAYE: 1,327.50 | NAPSA: 475.00 | NHIMA: 95.00', 1, '2026-06-07 21:17:59', '2026-06-10 21:06:27'),
  (7, 1, 2, 2026, 12500.00, 3125.00, 5515.21, 3624.38, 10109.79, '2026-02-20', 'pending', 'PAYE: 3,624.38 | NAPSA: 781.25 | NHIMA: 156.25 | Advance repayment: 833.33 | Other deductions: 120.00', 1, '2026-06-10 21:18:41', '2026-06-10 21:18:41'),
  (8, 2, 2, 2026, 9500.00, 0.00, 1897.50, 1327.50, 7602.50, '2026-02-20', 'pending', 'PAYE: 1,327.50 | NAPSA: 475.00 | NHIMA: 95.00', 1, '2026-06-10 21:18:41', '2026-06-10 21:18:41');

-- Table: `payroll_configurations`
DROP TABLE IF EXISTS `payroll_configurations`;
CREATE TABLE `payroll_configurations` (
  `key` varchar(60) NOT NULL,
  `label` varchar(150) NOT NULL,
  `value` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `group` varchar(50) NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `payroll_configurations` (`key`, `label`, `value`, `description`, `group`, `created_at`, `updated_at`) VALUES
  ('napsa_cap', 'NAPSA Monthly Cap (ZMW)', 1073, 'Maximum monthly NAPSA deduction', 'napsa', '2026-06-07 20:51:14', '2026-06-07 20:51:14'),
  ('napsa_rate', 'NAPSA Employee Contribution (%)', 5, 'Employee share of NAPSA contribution', 'napsa', '2026-06-07 20:51:14', '2026-06-07 20:51:14'),
  ('nhima_cap', 'NHIMA Monthly Cap (ZMW, 0=no cap)', 0, 'Maximum monthly NHIMA deduction (0=no cap)', 'nhima', '2026-06-07 20:51:14', '2026-06-07 20:51:14'),
  ('nhima_rate', 'NHIMA Contribution Rate (%)', 1, 'Employee share of NHIMA contribution', 'nhima', '2026-06-07 20:51:14', '2026-06-07 20:51:14'),
  ('paye_band1_max', 'PAYE Band 1 Upper Limit (Monthly)', 4800, 'Income up to this amount is tax-free', 'paye', '2026-06-07 20:51:14', '2026-06-07 20:51:14'),
  ('paye_band2_max', 'PAYE Band 2 Upper Limit (Monthly)', 6900, 'Upper limit for 25% tax band', 'paye', '2026-06-07 20:51:14', '2026-06-07 20:51:14'),
  ('paye_band2_rate', 'PAYE Band 2 Rate (%)', 25, 'Tax rate for band 2 income', 'paye', '2026-06-07 20:51:14', '2026-06-07 20:51:14'),
  ('paye_band3_max', 'PAYE Band 3 Upper Limit (Monthly)', 9200, 'Upper limit for 30% tax band', 'paye', '2026-06-07 20:51:14', '2026-06-07 20:51:14'),
  ('paye_band3_rate', 'PAYE Band 3 Rate (%)', 30, 'Tax rate for band 3 income', 'paye', '2026-06-07 20:51:14', '2026-06-07 20:51:14'),
  ('paye_band4_rate', 'PAYE Band 4 Rate (%)', 37.5, 'Tax rate for income above band 3', 'paye', '2026-06-07 20:51:14', '2026-06-07 20:51:14'),
  ('payroll_date', 'Monthly Payroll Date', 20, 'Day of the month on which payroll is processed (1–31)', 'general', '2026-06-07 21:35:35', '2026-06-07 21:37:49');

-- Table: `payroll_item_types`
DROP TABLE IF EXISTS `payroll_item_types`;
CREATE TABLE `payroll_item_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `category` varchar(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_item_types_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `payroll_item_types` (`id`, `name`, `slug`, `category`, `is_active`, `created_at`, `updated_at`) VALUES
  (1, 'Housing Allowance', 'housing', 'allowance', 1, '2026-06-07 21:05:32', '2026-06-07 21:05:32'),
  (2, 'Transport Allowance', 'transport', 'allowance', 1, '2026-06-07 21:05:32', '2026-06-07 21:05:32'),
  (3, 'Medical Allowance', 'medical', 'allowance', 1, '2026-06-07 21:05:32', '2026-06-07 21:05:32'),
  (4, 'Meal Allowance', 'meal', 'allowance', 1, '2026-06-07 21:05:32', '2026-06-07 21:05:32'),
  (5, 'Entertainment Allowance', 'entertainment', 'allowance', 1, '2026-06-07 21:05:32', '2026-06-07 21:05:32'),
  (6, 'Other Allowance', 'other_allowance', 'allowance', 1, '2026-06-07 21:05:32', '2026-06-07 21:05:32'),
  (7, 'Loan Repayment', 'loan_repayment', 'deduction', 1, '2026-06-07 21:05:32', '2026-06-07 21:05:32'),
  (8, 'Union Dues', 'union_dues', 'deduction', 1, '2026-06-07 21:05:32', '2026-06-07 21:05:32'),
  (9, 'Other Deduction', 'other_deduction', 'deduction', 1, '2026-06-07 21:05:32', '2026-06-07 21:05:32'),
  (10, 'Madson Insurance', 'madson_insurance', 'deduction', 1, '2026-06-07 21:11:57', '2026-06-07 21:11:57');

-- Table: `permissions`
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
  (1, 'view-dashboard', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (2, 'manage-users', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (3, 'manage-roles', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (4, 'view-audit-logs', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (5, 'manage-settings', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (6, 'manage-academic', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (7, 'view-academic', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (8, 'manage-students', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (9, 'view-students', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (10, 'manage-admissions', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (11, 'view-admissions', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (12, 'manage-results', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (13, 'view-results', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (14, 'manage-exams', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (15, 'manage-attendance', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (16, 'view-attendance', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (17, 'manage-finance', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (18, 'view-finance', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (19, 'manage-billing', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (20, 'manage-payments', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (21, 'manage-scholarships', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (22, 'manage-hostel', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (23, 'view-hostel', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (24, 'manage-library', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (25, 'view-library', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (26, 'manage-hr', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (27, 'view-hr', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (28, 'manage-payroll', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (29, 'manage-assets', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (30, 'manage-research', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (31, 'view-research', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (32, 'create-announcement', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (33, 'manage-announcements', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (34, 'manage-documents', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (35, 'manage-support', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (36, 'manage-alumni', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (37, 'view-reports', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (38, 'manage-backup', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22');

-- Table: `personal_access_tokens`
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (no rows in `personal_access_tokens`)

-- Table: `programs`
DROP TABLE IF EXISTS `programs`;
CREATE TABLE `programs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `level` enum('undergraduate','postgraduate','diploma','certificate') NOT NULL DEFAULT 'undergraduate',
  `duration_years` tinyint(4) NOT NULL DEFAULT 4,
  `credit_hours_required` int(11) NOT NULL DEFAULT 120,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `programs_code_unique` (`code`),
  KEY `programs_department_id_foreign` (`department_id`),
  CONSTRAINT `fk_prog_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `programs` (`id`, `department_id`, `name`, `code`, `level`, `duration_years`, `credit_hours_required`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 6, 'Bachelor of Science in Software Engineering', 'BSSE', 'undergraduate', 4, 120, NULL, 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (2, 5, 'Bachelor of Science in Computer Science', 'BSCS', 'undergraduate', 4, 120, NULL, 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (3, 3, 'Bachelor of Business Administration', 'BBA', 'undergraduate', 4, 120, NULL, 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (4, 4, 'Bachelor of Accountancy', 'BAcc', 'undergraduate', 4, 120, NULL, 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (5, 1, 'Bachelor of Engineering (Civil)', 'BECiv', 'undergraduate', 5, 150, NULL, 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (6, 9, 'Bachelor of Science in Nursing', 'BSN', 'undergraduate', 4, 130, NULL, 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL),
  (7, 6, 'Master of Science in Software Engineering', 'MSSE', 'postgraduate', 2, 60, NULL, 'active', '2026-06-05 21:10:23', '2026-06-07 15:04:55', NULL),
  (8, 3, 'Diploma in Business Management', 'DBM', 'diploma', 2, 60, NULL, 'active', '2026-06-05 21:10:23', '2026-06-05 21:10:23', NULL);

-- Table: `publications`
DROP TABLE IF EXISTS `publications`;
CREATE TABLE `publications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` bigint(20) unsigned NOT NULL,
  `research_project_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(500) NOT NULL,
  `type` enum('journal','conference','book','thesis','report') NOT NULL DEFAULT 'journal',
  `publisher` varchar(255) DEFAULT NULL,
  `publication_year` year(4) NOT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `publications_staff_id_foreign` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (no rows in `publications`)

-- Table: `research_papers`
DROP TABLE IF EXISTS `research_papers`;
CREATE TABLE `research_papers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `research_project_id` bigint(20) unsigned DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `authors` text DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_original_name` varchar(255) NOT NULL,
  `file_size` bigint(20) unsigned DEFAULT NULL,
  `file_mime` varchar(100) DEFAULT NULL,
  `category` enum('journal_article','conference_paper','thesis','technical_report','book_chapter','other') NOT NULL DEFAULT 'other',
  `publication_year` varchar(4) DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `research_papers_research_project_id_foreign` (`research_project_id`),
  KEY `research_papers_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `research_papers_research_project_id_foreign` FOREIGN KEY (`research_project_id`) REFERENCES `research_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `research_papers_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `research_papers` (`id`, `research_project_id`, `uploaded_by`, `title`, `authors`, `abstract`, `keywords`, `file_path`, `file_original_name`, `file_size`, `file_mime`, `category`, `publication_year`, `doi`, `is_public`, `created_at`, `updated_at`) VALUES
  (1, 1, 1, 'GRADUATE TRACER STUDY REPORT', 'Moono,Mulubwa', 'to assess the employability and curriculum relevance of Tevet programs', 'social science', 'research_papers/MVZYwYCxkMwYrMfQVVktzlYjamQr60p7N7ryDI4h.pdf', 'hostel_occupancy_20260608.pdf', 1278923, 'application/pdf', 'thesis', 2025, NULL, 1, '2026-06-09 07:54:12', '2026-06-09 07:54:12');

-- Table: `research_projects`
DROP TABLE IF EXISTS `research_projects`;
CREATE TABLE `research_projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `abstract` text NOT NULL,
  `principal_investigator_id` bigint(20) unsigned NOT NULL,
  `co_investigators` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT NULL,
  `funding_source` varchar(255) DEFAULT NULL,
  `keywords` varchar(500) DEFAULT NULL,
  `status` enum('proposal','ongoing','completed','suspended') NOT NULL DEFAULT 'proposal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `research_projects_pi_id_foreign` (`principal_investigator_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `research_projects` (`id`, `title`, `abstract`, `principal_investigator_id`, `co_investigators`, `start_date`, `end_date`, `budget`, `funding_source`, `keywords`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 'Graduate tracer study 2025', 'Assesing graduate employerbility and educational continuation for 2022-2024 cohorts', 1, 'moono', '2025-08-16', '2025-11-06', 5000.00, 'institutional', 'social science', 'completed', '2026-06-06 14:49:52', '2026-06-06 14:49:52', NULL);

-- Table: `role_has_permissions`
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `fk_rhp_role` (`role_id`),
  CONSTRAINT `fk_rhp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rhp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- Table: `roles`
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
  (1, 'super-admin', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (2, 'admin', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (3, 'registrar', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (4, 'finance-officer', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (5, 'finance-manager', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (6, 'lecturer', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (7, 'student', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (8, 'librarian', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (9, 'hostel-manager', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (10, 'hr-officer', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (11, 'it-admin', 'web', '2026-06-05 21:10:22', '2026-06-05 21:10:22');

-- Table: `room_allocations`
DROP TABLE IF EXISTS `room_allocations`;
CREATE TABLE `room_allocations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `hostel_room_id` bigint(20) unsigned NOT NULL,
  `allocation_date` date NOT NULL,
  `expected_vacate_date` date DEFAULT NULL,
  `actual_vacate_date` date DEFAULT NULL,
  `status` enum('active','vacated','transferred') NOT NULL DEFAULT 'active',
  `allocated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `room_allocations_student_id_foreign` (`student_id`),
  KEY `room_allocations_hostel_room_id_foreign` (`hostel_room_id`),
  CONSTRAINT `fk_alloc_room` FOREIGN KEY (`hostel_room_id`) REFERENCES `hostel_rooms` (`id`),
  CONSTRAINT `fk_alloc_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `room_allocations` (`id`, `student_id`, `hostel_room_id`, `allocation_date`, `expected_vacate_date`, `actual_vacate_date`, `status`, `allocated_by`, `created_at`, `updated_at`) VALUES
  (1, 1, 1, '2026-06-06', '2026-06-06', '2026-06-08', 'vacated', 1, '2026-06-06 13:09:01', '2026-06-08 15:26:34'),
  (2, 2, 3, '2026-06-06', '2026-06-06', '2026-06-06', 'vacated', 1, '2026-06-06 13:09:46', '2026-06-06 13:10:00');

-- Table: `salary_advances`
DROP TABLE IF EXISTS `salary_advances`;
CREATE TABLE `salary_advances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `amount_requested` decimal(12,2) NOT NULL,
  `amount_approved` decimal(12,2) DEFAULT NULL,
  `reason` text NOT NULL,
  `request_date` date NOT NULL,
  `repayment_start_date` date DEFAULT NULL,
  `repayment_months` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_advances_employee_id_foreign` (`employee_id`),
  CONSTRAINT `salary_advances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `salary_advances` (`id`, `employee_id`, `amount_requested`, `amount_approved`, `reason`, `request_date`, `repayment_start_date`, `repayment_months`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
  (1, 1, 2500.00, 2500.00, 'school fees', '2026-06-07', '2026-06-20', 3, 'approved', 'you have been awarded an advance successfully', '2026-06-07 19:59:01', '2026-06-07 20:00:17');

-- Table: `scholarship_awards`
DROP TABLE IF EXISTS `scholarship_awards`;
CREATE TABLE `scholarship_awards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scholarship_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `award_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('active','suspended','completed') NOT NULL DEFAULT 'active',
  `awarded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scholarship_awards_scholarship_id_foreign` (`scholarship_id`),
  KEY `scholarship_awards_student_id_foreign` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `scholarship_awards` (`id`, `scholarship_id`, `student_id`, `award_date`, `notes`, `status`, `awarded_by`, `created_at`, `updated_at`) VALUES
  (1, 3, 6, '2026-06-07', NULL, 'active', 1, '2026-06-07 15:19:10', '2026-06-07 15:19:10');

-- Table: `scholarships`
DROP TABLE IF EXISTS `scholarships`;
CREATE TABLE `scholarships` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('merit','need','sports','government','other') NOT NULL DEFAULT 'merit',
  `description` text DEFAULT NULL,
  `coverage_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `coverage_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_recipients` int(11) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `scholarships` (`id`, `name`, `type`, `description`, `coverage_type`, `coverage_value`, `max_recipients`, `status`, `created_at`, `updated_at`) VALUES
  (1, 'Vice Chancellor Scholarship', 'merit', 'Full scholarship for top 5 students per faculty based on GPA ≥ 3.8', 'percentage', 100.00, 30, 'active', '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (2, 'Need-Based Financial Aid', 'need', 'Partial scholarship for financially disadvantaged students', 'percentage', 50.00, 100, 'active', '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (3, 'Government Bursary', 'government', 'Government of Zambia Higher Education Bursary', 'percentage', 75.00, 200, 'active', '2026-06-05 21:10:24', '2026-06-05 21:10:24'),
  (4, 'Sports Excellence Award', 'sports', 'For student athletes representing the university at national level', 'fixed', 2500.00, 20, 'active', '2026-06-05 21:10:24', '2026-06-05 21:10:24');

-- Table: `semesters`
DROP TABLE IF EXISTS `semesters`;
CREATE TABLE `semesters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `registration_start` date DEFAULT NULL,
  `registration_end` date DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('upcoming','registration','active','exam','completed') NOT NULL DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `semesters_academic_year_id_foreign` (`academic_year_id`),
  CONSTRAINT `fk_sem_acyear` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `semesters` (`id`, `academic_year_id`, `name`, `start_date`, `end_date`, `registration_start`, `registration_end`, `is_current`, `status`, `created_at`, `updated_at`) VALUES
  (1, 1, 'Semester 1 - 2024/25', '2024-08-12', '2024-12-15', '2024-07-15', '2024-08-09', 0, 'completed', '2026-06-05 21:10:23', '2026-06-05 21:10:23'),
  (2, 1, 'Semester 2 - 2024/25', '2025-01-13', '2025-05-30', '2024-12-16', '2025-01-10', 0, 'completed', '2026-06-05 21:10:23', '2026-06-05 21:10:23'),
  (3, 2, 'Semester 1 - 2025/26', '2026-06-01', '2026-06-30', '2025-07-14', '2025-08-08', 0, 'active', '2026-06-05 21:10:23', '2026-06-07 15:55:59'),
  (4, 2, 'Semester 2 - 2025/26', '2026-06-01', '2026-06-13', '2025-12-15', '2026-01-09', 1, 'active', '2026-06-05 21:10:23', '2026-06-07 15:55:59');

-- Table: `sessions`
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (no rows in `sessions`)

-- Table: `staff`
DROP TABLE IF EXISTS `staff`;
CREATE TABLE `staff` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `qualifications` text DEFAULT NULL,
  `status` enum('active','inactive','on_leave') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_staff_id_unique` (`staff_id`),
  UNIQUE KEY `staff_user_id_unique` (`user_id`),
  KEY `staff_department_id_foreign` (`department_id`),
  CONSTRAINT `fk_staff_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_staff_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `staff` (`id`, `user_id`, `staff_id`, `department_id`, `designation`, `specialization`, `qualifications`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 4, 'STAFF-2024-001', 6, 'Senior Lecturer', 'Software Engineering, Mobile Development', NULL, 'active', '2026-06-05 21:10:24', '2026-06-05 21:10:24', NULL);

-- Table: `student_bills`
DROP TABLE IF EXISTS `student_bills`;
CREATE TABLE `student_bills` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `fee_structure_id` bigint(20) unsigned DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned DEFAULT NULL,
  `semester_id` bigint(20) unsigned DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_bills_student_id_foreign` (`student_id`),
  KEY `student_bills_academic_year_id_foreign` (`academic_year_id`),
  KEY `fk_bill_fee_structure` (`fee_structure_id`),
  CONSTRAINT `fk_bill_fee_structure` FOREIGN KEY (`fee_structure_id`) REFERENCES `fee_structures` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_bill_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `student_bills` (`id`, `student_id`, `fee_structure_id`, `academic_year_id`, `semester_id`, `total_amount`, `amount_paid`, `balance`, `due_date`, `status`, `created_at`, `updated_at`) VALUES
  (1, 1, 2, 2, 4, 8500.00, 0.00, 8500.00, '2026-02-09', 'unpaid', '2026-06-06 11:58:38', '2026-06-06 11:58:38'),
  (2, 2, 2, 2, 4, 8500.00, 8500.00, 0.00, '2026-02-09', 'paid', '2026-06-06 11:58:38', '2026-06-08 16:55:29'),
  (3, 1, 3, 2, 3, 7500.00, 0.00, 7500.00, '2026-06-29', 'unpaid', '2026-06-07 19:07:46', '2026-06-07 19:07:46');

-- Table: `student_guardians`
DROP TABLE IF EXISTS `student_guardians`;
CREATE TABLE `student_guardians` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_emergency_contact` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_guardians_student_id_foreign` (`student_id`),
  CONSTRAINT `fk_guardian_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `student_guardians` (`id`, `student_id`, `name`, `relationship`, `phone`, `email`, `address`, `is_emergency_contact`, `created_at`, `updated_at`) VALUES
  (1, 8, 'Jairos', 'father', +260971000005, 'j@gmail.com', 'kalabo trades
kalabo', 1, '2026-06-07 14:30:17', '2026-06-07 14:30:17');

-- Table: `students`
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `program_id` bigint(20) unsigned DEFAULT NULL,
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
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_student_id_unique` (`student_id`),
  UNIQUE KEY `students_user_id_unique` (`user_id`),
  KEY `students_program_id_foreign` (`program_id`),
  CONSTRAINT `fk_student_prog` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `students` (`id`, `user_id`, `student_id`, `program_id`, `enrollment_date`, `expected_graduation`, `year_of_study`, `student_type`, `gender`, `date_of_birth`, `nationality`, `national_id`, `phone`, `address`, `sponsor`, `photo`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 5, 'STU-2025-001', 3, '2025-08-11', NULL, 2, 'local', 'male', NULL, 'Zambian', NULL, +260971000005, NULL, 'AP', 'students/photos/W318Xh4Ki4HncEdmwrsp10waTtBAWwhNmYyqQHRP.png', 'active', '2026-06-05 21:10:24', '2026-06-07 13:25:30', NULL),
  (2, 10, 'STU00002', 2, '2026-06-06', NULL, 1, 'local', 'male', NULL, 'Zambian', NULL, 0966666666, 'winela', 'Liuwa CDF', 'students/photos/hGASoaJ6hAszdZXUbvUry72gfE4JoRNftqGlri48.png', 'active', '2026-06-06 10:25:47', '2026-06-08 20:16:23', NULL),
  (3, 11, 'STU00003', 2, '2026-06-07', NULL, 1, 'local', 'female', NULL, 'Zambian', NULL, 977552233, 'Plot 12,Lusaka', 'Self', NULL, 'active', '2026-06-07 09:36:17', '2026-06-07 13:24:35', NULL),
  (4, 12, 'STU00004', 2, '2026-06-07', NULL, 1, 'local', 'male', NULL, 'Zambian', NULL, 975096323, 'Plot 12,Lusaka', 'Liuwa CDF', NULL, 'active', '2026-06-07 09:36:18', '2026-06-07 13:24:08', NULL),
  (5, 13, 'STU00005', 4, '2026-06-07', NULL, 1, 'local', 'male', NULL, 'Zambian', NULL, 755996644, 'ktti house no.1', 'Kalabo central CDF', NULL, 'active', '2026-06-07 09:45:39', '2026-06-07 13:23:10', NULL),
  (6, 14, 'STU00006', 2, '2026-06-07', NULL, 1, 'local', 'female', NULL, 'Zambian', NULL, 755996674, 'ktti house no.1', 'Liuwa CDF', NULL, 'active', '2026-06-07 09:45:39', '2026-06-08 20:15:50', NULL),
  (7, 15, 'STU00007', 4, '2026-06-07', NULL, 1, 'local', 'female', NULL, 'Zambian', NULL, 755996684, 'ktti house no.1', 'Sikongo CDF', NULL, 'active', '2026-06-07 09:45:40', '2026-06-07 13:22:37', NULL),
  (8, 16, 'STU00008', 4, '2026-06-07', NULL, 1, 'local', 'male', NULL, 'Zambian', NULL, 755996694, 'ktti house no.1', 'GRZ', NULL, 'active', '2026-06-07 09:45:41', '2026-06-07 13:22:01', NULL);

-- Table: `support_tickets`
DROP TABLE IF EXISTS `support_tickets`;
CREATE TABLE `support_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(20) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'other',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_tickets_ticket_number_unique` (`ticket_number`),
  KEY `support_tickets_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `support_tickets` (`id`, `ticket_number`, `user_id`, `subject`, `description`, `category`, `priority`, `status`, `assigned_to`, `attachment`, `created_at`, `updated_at`) VALUES
  (1, 'TKT/20260608/XPUCG', 4, 'Forgotten PC Password', 'please i need your assistant so that i can do a presentation', 'technical', 'urgent', 'resolved', NULL, NULL, '2026-06-08 14:37:07', '2026-06-08 14:50:39'),
  (2, 'TKT/20260608/TLURY', 4, 'i want to enroll', 'i want to enroll for my daughter', 'academic', 'medium', 'resolved', NULL, NULL, '2026-06-08 14:56:48', '2026-06-08 14:58:10');

-- Table: `ticket_responses`
DROP TABLE IF EXISTS `ticket_responses`;
CREATE TABLE `ticket_responses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `response` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_responses_ticket_id_foreign` (`support_ticket_id`),
  KEY `ticket_responses_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ticket_responses` (`id`, `support_ticket_id`, `user_id`, `response`, `attachment`, `created_at`, `updated_at`) VALUES
  (1, 1, 1, 'give me your session ID', NULL, '2026-06-08 14:48:52', '2026-06-08 14:48:52'),
  (2, 1, 4, 102033, NULL, '2026-06-08 14:49:51', '2026-06-08 14:49:51'),
  (3, 1, 1, 'am sure now its done, goodday', NULL, '2026-06-08 14:50:25', '2026-06-08 14:50:25'),
  (4, 1, 4, 'thanks sir', NULL, '2026-06-08 14:54:31', '2026-06-08 14:54:31'),
  (5, 1, 1, 'sure', NULL, '2026-06-08 14:55:40', '2026-06-08 14:55:40'),
  (6, 2, 1, 'https://', NULL, '2026-06-08 14:58:04', '2026-06-08 14:58:04');

-- Table: `timetables`
DROP TABLE IF EXISTS `timetables`;
CREATE TABLE `timetables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_offering_id` bigint(20) unsigned NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `timetables_course_offering_id_foreign` (`course_offering_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `timetables` (`id`, `course_offering_id`, `day_of_week`, `start_time`, `end_time`, `venue`, `created_at`, `updated_at`) VALUES
  (1, 1, 'Monday', '10:00:00', '11:00:00', NULL, '2026-06-06 10:46:50', '2026-06-06 10:46:50');

-- Table: `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `address`, `avatar`, `status`, `deleted_at`, `is_active`, `last_login_at`, `last_login_ip`, `remember_token`, `created_at`, `updated_at`) VALUES
  (1, 'Super Administrator', 'admin@university.com', '2026-06-05 21:10:22', '$2y$12$UnPYywMLf0ub16/FROH6l.ZQTRiV87y0L1M89lOoH7LwOReyNsgVm', +260971000001, NULL, 'avatars/i7zw0lfQCepYYMh5FwYurwI0bOxly6MDtVm1J1Px.jpg', 'active', NULL, 1, '2026-06-11 05:06:56', '127.0.0.1', NULL, '2026-06-05 21:10:22', '2026-06-11 05:06:56'),
  (2, 'Dr. James Mwale', 'registrar@university.com', '2026-06-05 21:10:22', '$2y$12$5A06YQQSkTVtHN4P58E9YuwjKBrIkt3dAwUqQLB0xXjxNY7.dr1pW', +260971000002, NULL, NULL, 'active', NULL, 1, '2026-06-09 07:26:17', '127.0.0.1', NULL, '2026-06-05 21:10:22', '2026-06-09 07:26:17'),
  (3, 'Mrs. Grace Banda', 'finance@university.com', '2026-06-05 21:10:22', '$2y$12$/3Gh3kPwlxCcZehtKR.vseVvEirxI1asjYbx2BWLuYO9D7WaEDk7G', +260971000003, NULL, NULL, 'active', NULL, 1, '2026-06-08 17:18:07', '127.0.0.1', NULL, '2026-06-05 21:10:22', '2026-06-08 17:18:07'),
  (4, 'Prof. David Tembo', 'lecturer@university.com', '2026-06-05 21:10:22', '$2y$12$c9RCECW7Z4RNcJ2LQKmtpeHeQzDqorjziihiFXcgfhSMyVx1pXfVG', +260971000004, NULL, NULL, 'active', NULL, 1, '2026-06-09 10:14:39', '127.0.0.1', NULL, '2026-06-05 21:10:22', '2026-06-09 10:14:39'),
  (5, 'John Phiri', 'student@university.com', '2026-06-05 21:10:22', '$2y$12$zqlyA2UPBn8m//07sy4yMu0pJPXKBYt1kv8ZxwQiDO7cpayweXlZK', +260971000005, NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (6, 'Mr. Patrick Ngosa', 'librarian@university.com', '2026-06-05 21:10:22', '$2y$12$NBc.xqiVCbjXMIW5Uordh.Pc/J0JwAnMZPgCa7AT71GLBXHzAskPC', +260971000006, NULL, NULL, 'active', NULL, 1, '2026-06-08 16:25:04', '127.0.0.1', NULL, '2026-06-05 21:10:22', '2026-06-08 16:25:04'),
  (7, 'Mrs. Ruth Zulu', 'hostel@university.com', '2026-06-05 21:10:22', '$2y$12$9EfxvNdzdfaM1AFe27NPKOIh8AHcYJm3fwGsTo9cNtJk0zkli8Pd2', +260971000007, NULL, NULL, 'active', NULL, 1, '2026-06-08 15:18:19', '127.0.0.1', NULL, '2026-06-05 21:10:22', '2026-06-08 15:18:19'),
  (8, 'Mr. Charles Sikazwe', 'hr@university.com', '2026-06-05 21:10:22', '$2y$12$DtbkHa2tepNXgMCYyti9XeLVYaANiw0Otg2AhnZAOQ/Z8vcGnvBY.', +260971000008, NULL, NULL, 'active', NULL, 1, '2026-06-08 15:57:52', '127.0.0.1', NULL, '2026-06-05 21:10:22', '2026-06-08 15:57:52'),
  (9, 'IT Administrator', 'it@university.com', '2026-06-05 21:10:22', '$2y$12$zqlyA2UPBn8m//07sy4yMu0pJPXKBYt1kv8ZxwQiDO7cpayweXlZK', +260971000009, NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-05 21:10:22', '2026-06-05 21:10:22'),
  (10, 'student 1', 'student1@gmail.com', NULL, '$2y$12$ou463pvVUHJ1tfZing/Zy.q0p9XzV98sltS17UH.lxr03OiqCinbO', 0966666666, NULL, NULL, 'active', NULL, 1, '2026-06-09 05:48:58', '127.0.0.1', NULL, '2026-06-06 10:25:47', '2026-06-09 05:48:58'),
  (11, 'Jane Banda', 'janebanda@university.com', NULL, '$2y$12$dFLjTQcCRPmTLOJ4zHcGPe86sSunqOQS2l0wZtpGfXA22ECtRNLdO', 977552233, NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-07 09:36:17', '2026-06-07 09:36:17'),
  (12, 'moses tembo', 'mose@university.com', NULL, '$2y$12$WTpAKAQ36k01nG.g6Fi6/uf6s4T4AWnO7mUoi8YrE1I6g8WrfuJAG', 975096323, NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-07 09:36:17', '2026-06-07 09:36:17'),
  (13, 'jack muso', 'jack@university.com', NULL, '$2y$12$vEna5XrERSgomfYwJD5A6udY1dP7.bZyhksaXJP/Up4spBrlrFzwe', 755996644, NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-07 09:45:39', '2026-06-07 09:45:39'),
  (14, 'monde muso', 'mond@university.com', NULL, '$2y$12$9Hb0uExblsq4bgPQzUdw6ur3YxCjQgjmyA.GGSpf89ggfJlFNeMka', 755996674, NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-07 09:45:39', '2026-06-07 09:45:39'),
  (15, 'mary muso', 'mary@university.com', NULL, '$2y$12$jSaq5GeaRcJyzpFn5yl8v.Rj7d/M.0goZUVrqaw92wKQ4lEuBtz26', 755996684, NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-07 09:45:40', '2026-06-07 09:45:40'),
  (16, 'terry muso', 'terry@university.com', NULL, '$2y$12$Ld0PUlz5ItSUkcGgDle40eC9h1Z9ssgmcazp2f5K7BIj1PEsZdEB2', 755996694, NULL, NULL, 'active', NULL, 1, '2026-06-09 11:42:37', '127.0.0.1', NULL, '2026-06-07 09:45:41', '2026-06-09 11:42:37'),
  (17, 'Moono jay', 'mj@gmail.com', NULL, '$2y$12$4IB/uwGiPSHOFNRF.An1hedPWDvDq134L3Skt9kK0rskciztRS7Yy', NULL, NULL, NULL, 'active', NULL, 1, NULL, NULL, NULL, '2026-06-11 02:18:07', '2026-06-11 02:18:07');

SET FOREIGN_KEY_CHECKS=1;