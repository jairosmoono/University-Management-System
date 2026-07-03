-- ============================================================
-- University Management System - Complete Database Dump
-- Generated: 2026-06-05
-- Database: university_management_system
-- Compatible: MySQL 8.0+
-- Application: Laravel 12 + Spatie Laravel Permission
-- Default Admin: admin@university.com / Admin@123
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+02:00";
SET NAMES utf8mb4;
SET character_set_client = utf8mb4;

CREATE DATABASE IF NOT EXISTS `university_management_system`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `university_management_system`;

-- ============================================================
-- CORE AUTH TABLES
-- ============================================================

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SPATIE PERMISSION TABLES
-- ============================================================

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`, `guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`, `guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `model_id`, `model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`, `model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `model_id`, `model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`, `model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ACADEMIC STRUCTURE
-- ============================================================

CREATE TABLE `faculties` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `dean_id` bigint UNSIGNED DEFAULT NULL,
  `description` text,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `faculties_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `departments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `faculty_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `hod_id` bigint UNSIGNED DEFAULT NULL,
  `description` text,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_code_unique` (`code`),
  KEY `departments_faculty_id_foreign` (`faculty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `programs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `level` enum('undergraduate','postgraduate','diploma','certificate') NOT NULL DEFAULT 'undergraduate',
  `duration_years` tinyint NOT NULL DEFAULT 4,
  `credit_hours_required` int NOT NULL DEFAULT 120,
  `description` text,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `programs_code_unique` (`code`),
  KEY `programs_department_id_foreign` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `academic_years` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('upcoming','active','completed') NOT NULL DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_years_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `semesters` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` bigint UNSIGNED NOT NULL,
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
  KEY `semesters_academic_year_id_foreign` (`academic_year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `courses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `credits` tinyint NOT NULL DEFAULT 3,
  `level` varchar(10) DEFAULT NULL,
  `course_type` enum('core','elective','lab','seminar') NOT NULL DEFAULT 'core',
  `description` text,
  `prerequisites` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courses_code_unique` (`code`),
  KEY `courses_department_id_foreign` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `course_program` (
  `course_id` bigint UNSIGNED NOT NULL,
  `program_id` bigint UNSIGNED NOT NULL,
  `year_of_study` tinyint DEFAULT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`course_id`, `program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- STAFF & STUDENTS
-- ============================================================

CREATE TABLE `staff` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `qualifications` text,
  `status` enum('active','inactive','on_leave') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_staff_id_unique` (`staff_id`),
  UNIQUE KEY `staff_user_id_unique` (`user_id`),
  KEY `staff_department_id_foreign` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `students` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `program_id` bigint UNSIGNED DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL,
  `expected_graduation` date DEFAULT NULL,
  `year_of_study` tinyint DEFAULT 1,
  `student_type` enum('local','international') NOT NULL DEFAULT 'local',
  `gender` enum('male','female','other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended','graduated','withdrawn') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_student_id_unique` (`student_id`),
  UNIQUE KEY `students_user_id_unique` (`user_id`),
  KEY `students_program_id_foreign` (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `student_guardians` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text,
  `is_emergency_contact` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_guardians_student_id_foreign` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_number` varchar(30) NOT NULL,
  `program_id` bigint UNSIGNED DEFAULT NULL,
  `semester_id` bigint UNSIGNED DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text,
  `previous_school` varchar(255) DEFAULT NULL,
  `qualification_type` varchar(100) DEFAULT NULL,
  `year_completed` year DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `documents` json DEFAULT NULL,
  `status` enum('pending','approved','rejected','waitlisted') NOT NULL DEFAULT 'pending',
  `rejection_reason` text,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `student_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admissions_application_number_unique` (`application_number`),
  KEY `admissions_program_id_foreign` (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- COURSE MANAGEMENT
-- ============================================================

CREATE TABLE `course_offerings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` bigint UNSIGNED NOT NULL,
  `academic_year_id` bigint UNSIGNED NOT NULL,
  `semester_id` bigint UNSIGNED NOT NULL,
  `lecturer_id` bigint UNSIGNED DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `schedule` varchar(255) DEFAULT NULL,
  `max_students` int NOT NULL DEFAULT 50,
  `enrolled_students` int NOT NULL DEFAULT 0,
  `status` enum('active','cancelled','completed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_offerings_course_id_foreign` (`course_id`),
  KEY `course_offerings_semester_id_foreign` (`semester_id`),
  KEY `course_offerings_lecturer_id_foreign` (`lecturer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `course_registrations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint UNSIGNED NOT NULL,
  `course_offering_id` bigint UNSIGNED NOT NULL,
  `registered_by` bigint UNSIGNED DEFAULT NULL,
  `status` enum('registered','dropped','completed','failed') NOT NULL DEFAULT 'registered',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_registrations_student_offering_unique` (`student_id`, `course_offering_id`),
  KEY `course_registrations_course_offering_id_foreign` (`course_offering_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `timetables` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_offering_id` bigint UNSIGNED NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `timetables_course_offering_id_foreign` (`course_offering_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `attendance_sessions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_offering_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `session_type` enum('lecture','lab','tutorial') NOT NULL DEFAULT 'lecture',
  `topic` varchar(255) DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_sessions_course_offering_id_foreign` (`course_offering_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `attendance_records` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `attendance_session_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL DEFAULT 'present',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_records_session_student_unique` (`attendance_session_id`, `student_id`),
  KEY `attendance_records_student_id_foreign` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `assignments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_offering_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `due_date` datetime DEFAULT NULL,
  `total_marks` decimal(5,2) NOT NULL DEFAULT 100.00,
  `status` enum('draft','published','closed') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignments_course_offering_id_foreign` (`course_offering_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `assignment_submissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `assignment_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `submission_text` text,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `marks_obtained` decimal(5,2) DEFAULT NULL,
  `feedback` text,
  `graded_by` bigint UNSIGNED DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','submitted','graded','late') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assignment_submissions_assignment_student_unique` (`assignment_id`, `student_id`),
  KEY `assignment_submissions_student_id_foreign` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- EXAMINATIONS & RESULTS
-- ============================================================

CREATE TABLE `examinations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `course_offering_id` bigint UNSIGNED NOT NULL,
  `exam_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `invigilator_id` bigint UNSIGNED DEFAULT NULL,
  `max_marks` decimal(5,2) NOT NULL DEFAULT 100.00,
  `passing_marks` decimal(5,2) NOT NULL DEFAULT 40.00,
  `status` enum('scheduled','ongoing','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `examinations_course_offering_id_foreign` (`course_offering_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `continuous_assessments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint UNSIGNED NOT NULL,
  `course_offering_id` bigint UNSIGNED NOT NULL,
  `ca_score` decimal(5,2) DEFAULT NULL,
  `max_ca_score` decimal(5,2) NOT NULL DEFAULT 30.00,
  `entered_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ca_student_offering_unique` (`student_id`, `course_offering_id`),
  KEY `ca_course_offering_id_foreign` (`course_offering_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `exam_results` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint UNSIGNED NOT NULL,
  `examination_id` bigint UNSIGNED NOT NULL,
  `course_offering_id` bigint UNSIGNED NOT NULL,
  `exam_score` decimal(5,2) DEFAULT NULL,
  `entered_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exam_results_student_exam_unique` (`student_id`, `examination_id`),
  KEY `exam_results_examination_id_foreign` (`examination_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `final_results` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint UNSIGNED NOT NULL,
  `course_offering_id` bigint UNSIGNED NOT NULL,
  `academic_year_id` bigint UNSIGNED NOT NULL,
  `semester_id` bigint UNSIGNED NOT NULL,
  `ca_score` decimal(5,2) DEFAULT NULL,
  `exam_score` decimal(5,2) DEFAULT NULL,
  `total_score` decimal(5,2) DEFAULT NULL,
  `grade` varchar(5) DEFAULT NULL,
  `grade_points` decimal(3,1) DEFAULT NULL,
  `status` enum('pending','pass','fail','incomplete') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `final_results_student_offering_unique` (`student_id`, `course_offering_id`),
  KEY `final_results_course_offering_id_foreign` (`course_offering_id`),
  KEY `final_results_semester_id_foreign` (`semester_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gpa_records` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint UNSIGNED NOT NULL,
  `academic_year_id` bigint UNSIGNED NOT NULL,
  `semester_id` bigint UNSIGNED NOT NULL,
  `gpa` decimal(4,2) NOT NULL DEFAULT 0.00,
  `cgpa` decimal(4,2) NOT NULL DEFAULT 0.00,
  `credits_earned` int NOT NULL DEFAULT 0,
  `total_credits_earned` int NOT NULL DEFAULT 0,
  `academic_standing` varchar(50) NOT NULL DEFAULT 'Good Standing',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gpa_records_student_semester_unique` (`student_id`, `semester_id`),
  KEY `gpa_records_academic_year_id_foreign` (`academic_year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- FINANCE TABLES
-- ============================================================

CREATE TABLE `fee_structures` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `academic_year_id` bigint UNSIGNED DEFAULT NULL,
  `semester_id` bigint UNSIGNED DEFAULT NULL,
  `program_id` bigint UNSIGNED DEFAULT NULL,
  `student_type` enum('local','international','both') NOT NULL DEFAULT 'both',
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_structures_academic_year_id_foreign` (`academic_year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fee_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fee_structure_id` bigint UNSIGNED NOT NULL,
  `fee_type` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_items_fee_structure_id_foreign` (`fee_structure_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `student_bills` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint UNSIGNED NOT NULL,
  `fee_structure_id` bigint UNSIGNED DEFAULT NULL,
  `academic_year_id` bigint UNSIGNED DEFAULT NULL,
  `semester_id` bigint UNSIGNED DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_bills_student_id_foreign` (`student_id`),
  KEY `student_bills_academic_year_id_foreign` (`academic_year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bill_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_bill_id` bigint UNSIGNED NOT NULL,
  `fee_type` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bill_items_student_bill_id_foreign` (`student_bill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_bill_id` bigint UNSIGNED NOT NULL,
  `reference_number` varchar(50) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'Cash',
  `transaction_reference` varchar(100) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `notes` text,
  `status` enum('pending','verified','reversed') NOT NULL DEFAULT 'verified',
  `recorded_by` bigint UNSIGNED DEFAULT NULL,
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_reference_number_unique` (`reference_number`),
  KEY `payments_student_bill_id_foreign` (`student_bill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `scholarships` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('merit','need','sports','government','other') NOT NULL DEFAULT 'merit',
  `description` text,
  `coverage_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `coverage_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_recipients` int DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `scholarship_awards` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `scholarship_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `award_date` date NOT NULL,
  `notes` text,
  `status` enum('active','suspended','completed') NOT NULL DEFAULT 'active',
  `awarded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scholarship_awards_scholarship_id_foreign` (`scholarship_id`),
  KEY `scholarship_awards_student_id_foreign` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- HOSTEL
-- ============================================================

CREATE TABLE `hostels` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('male','female','mixed') NOT NULL DEFAULT 'mixed',
  `warden_id` bigint UNSIGNED DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hostel_rooms` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `hostel_id` bigint UNSIGNED NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `floor` tinyint NOT NULL DEFAULT 1,
  `room_type` enum('single','double','triple') NOT NULL DEFAULT 'double',
  `capacity` tinyint NOT NULL DEFAULT 2,
  `amenities` json DEFAULT NULL,
  `status` enum('available','occupied','maintenance','reserved') NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hostel_rooms_hostel_room_unique` (`hostel_id`, `room_number`),
  KEY `hostel_rooms_hostel_id_foreign` (`hostel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `room_allocations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint UNSIGNED NOT NULL,
  `hostel_room_id` bigint UNSIGNED NOT NULL,
  `allocation_date` date NOT NULL,
  `expected_vacate_date` date DEFAULT NULL,
  `actual_vacate_date` date DEFAULT NULL,
  `status` enum('active','vacated','transferred') NOT NULL DEFAULT 'active',
  `allocated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `room_allocations_student_id_foreign` (`student_id`),
  KEY `room_allocations_hostel_room_id_foreign` (`hostel_room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LIBRARY
-- ============================================================

CREATE TABLE `book_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `library_books` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `book_category_id` bigint UNSIGNED DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `title` varchar(500) NOT NULL,
  `author` varchar(255) NOT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `publication_year` year DEFAULT NULL,
  `edition` varchar(20) DEFAULT NULL,
  `copies_total` int NOT NULL DEFAULT 1,
  `copies_available` int NOT NULL DEFAULT 1,
  `shelf_location` varchar(50) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `library_books_category_id_foreign` (`book_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `book_borrowings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `library_book_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `fine_amount` decimal(8,2) DEFAULT NULL,
  `fine_paid` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('borrowed','returned','overdue','lost') NOT NULL DEFAULT 'borrowed',
  `issued_by` bigint UNSIGNED DEFAULT NULL,
  `returned_to` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `book_borrowings_book_id_foreign` (`library_book_id`),
  KEY `book_borrowings_student_id_foreign` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- HR / EMPLOYEES
-- ============================================================

CREATE TABLE `employees` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
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
  KEY `employees_department_id_foreign` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `leave_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `days_allowed` int NOT NULL DEFAULT 14,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `leave_requests` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` bigint UNSIGNED NOT NULL,
  `leave_type_id` bigint UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `remarks` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_requests_employee_id_foreign` (`employee_id`),
  KEY `leave_requests_leave_type_id_foreign` (`leave_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payroll` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` bigint UNSIGNED NOT NULL,
  `month` tinyint NOT NULL,
  `year` year NOT NULL,
  `basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `allowances` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_date` date DEFAULT NULL,
  `status` enum('pending','processed','paid') NOT NULL DEFAULT 'pending',
  `notes` text,
  `processed_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_employee_month_year_unique` (`employee_id`, `month`, `year`),
  KEY `payroll_employee_id_foreign` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ASSETS, RESEARCH, COMMUNICATIONS
-- ============================================================

CREATE TABLE `assets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `asset_code` varchar(50) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `research_projects` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `abstract` text NOT NULL,
  `principal_investigator_id` bigint UNSIGNED NOT NULL,
  `co_investigators` text,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `publications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `staff_id` bigint UNSIGNED NOT NULL,
  `research_project_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(500) NOT NULL,
  `type` enum('journal','conference','book','thesis','report') NOT NULL DEFAULT 'journal',
  `publisher` varchar(255) DEFAULT NULL,
  `publication_year` year NOT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `abstract` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `publications_staff_id_foreign` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `announcements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_by` bigint UNSIGNED DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `send_email` tinyint(1) NOT NULL DEFAULT 0,
  `send_sms` tinyint(1) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `category` enum('academic','event','emergency','general') NOT NULL DEFAULT 'general',
  `priority` enum('normal','high','urgent') NOT NULL DEFAULT 'normal',
  `target_audience` json DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `views_count` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` bigint UNSIGNED NOT NULL,
  `receiver_id` bigint UNSIGNED NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `sender_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `receiver_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `attachment` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `data` json DEFAULT NULL,
  `action_url` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `documents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint UNSIGNED DEFAULT NULL,
  `uploaded_by` bigint UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('transcript','certificate','id_card','admission_letter','other') NOT NULL DEFAULT 'other',
  `file_path` varchar(500) NOT NULL,
  `file_size` int DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_student_id_foreign` (`student_id`),
  KEY `documents_uploaded_by_foreign` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `support_tickets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(20) NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'other',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_tickets_ticket_number_unique` (`ticket_number`),
  KEY `support_tickets_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ticket_responses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `support_ticket_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `response` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_responses_ticket_id_foreign` (`support_ticket_id`),
  KEY `ticket_responses_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `alumni` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint UNSIGNED NOT NULL,
  `graduation_year` year NOT NULL,
  `current_employer` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `employment_status` enum('employed','self_employed','unemployed','further_studies') NOT NULL DEFAULT 'employed',
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `linkedin_url` varchar(500) DEFAULT NULL,
  `biography` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumni_student_id_unique` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `audit_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(20) NOT NULL,
  `model_type` varchar(100) DEFAULT NULL,
  `model_id` bigint UNSIGNED DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `request_data` json DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_index` (`user_id`),
  KEY `audit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA: ROLES AND PERMISSIONS
-- ============================================================

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'web', NOW(), NOW()),
(2, 'admin', 'web', NOW(), NOW()),
(3, 'registrar', 'web', NOW(), NOW()),
(4, 'finance-officer', 'web', NOW(), NOW()),
(5, 'finance-manager', 'web', NOW(), NOW()),
(6, 'lecturer', 'web', NOW(), NOW()),
(7, 'student', 'web', NOW(), NOW()),
(8, 'librarian', 'web', NOW(), NOW()),
(9, 'hostel-manager', 'web', NOW(), NOW()),
(10, 'hr-officer', 'web', NOW(), NOW()),
(11, 'it-admin', 'web', NOW(), NOW());

INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES
('view-dashboard', 'web', NOW(), NOW()),
('manage-users', 'web', NOW(), NOW()),
('manage-roles', 'web', NOW(), NOW()),
('view-audit-logs', 'web', NOW(), NOW()),
('manage-settings', 'web', NOW(), NOW()),
('manage-academic', 'web', NOW(), NOW()),
('view-academic', 'web', NOW(), NOW()),
('manage-students', 'web', NOW(), NOW()),
('view-students', 'web', NOW(), NOW()),
('manage-admissions', 'web', NOW(), NOW()),
('view-admissions', 'web', NOW(), NOW()),
('manage-results', 'web', NOW(), NOW()),
('view-results', 'web', NOW(), NOW()),
('manage-exams', 'web', NOW(), NOW()),
('manage-attendance', 'web', NOW(), NOW()),
('view-attendance', 'web', NOW(), NOW()),
('manage-finance', 'web', NOW(), NOW()),
('view-finance', 'web', NOW(), NOW()),
('manage-billing', 'web', NOW(), NOW()),
('manage-payments', 'web', NOW(), NOW()),
('manage-scholarships', 'web', NOW(), NOW()),
('manage-hostel', 'web', NOW(), NOW()),
('view-hostel', 'web', NOW(), NOW()),
('manage-library', 'web', NOW(), NOW()),
('view-library', 'web', NOW(), NOW()),
('manage-hr', 'web', NOW(), NOW()),
('view-hr', 'web', NOW(), NOW()),
('manage-payroll', 'web', NOW(), NOW()),
('manage-assets', 'web', NOW(), NOW()),
('manage-research', 'web', NOW(), NOW()),
('view-research', 'web', NOW(), NOW()),
('create-announcement', 'web', NOW(), NOW()),
('manage-announcements', 'web', NOW(), NOW()),
('manage-documents', 'web', NOW(), NOW()),
('manage-support', 'web', NOW(), NOW()),
('manage-alumni', 'web', NOW(), NOW()),
('view-reports', 'web', NOW(), NOW()),
('manage-backup', 'web', NOW(), NOW());

-- Grant all permissions to super-admin (role_id=1)
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT id, 1 FROM `permissions`;

-- ============================================================
-- SEED DATA: USERS (Default Accounts)
-- Password for ALL accounts: Admin@123 (bcrypt hash below)
-- ============================================================

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `status`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrator', 'admin@university.com', NOW(), '$2y$12$zqlyA2UPBn8m//07sy4yMu0pJPXKBYt1kv8ZxwQiDO7cpayweXlZK', '+260971000001', 'active', 1, NOW(), NOW()),
(2, 'Dr. James Mwale', 'registrar@university.com', NOW(), '$2y$12$zqlyA2UPBn8m//07sy4yMu0pJPXKBYt1kv8ZxwQiDO7cpayweXlZK', '+260971000002', 'active', 1, NOW(), NOW()),
(3, 'Mrs. Grace Banda', 'finance@university.com', NOW(), '$2y$12$zqlyA2UPBn8m//07sy4yMu0pJPXKBYt1kv8ZxwQiDO7cpayweXlZK', '+260971000003', 'active', 1, NOW(), NOW()),
(4, 'Prof. David Tembo', 'lecturer@university.com', NOW(), '$2y$12$zqlyA2UPBn8m//07sy4yMu0pJPXKBYt1kv8ZxwQiDO7cpayweXlZK', '+260971000004', 'active', 1, NOW(), NOW()),
(5, 'John Phiri', 'student@university.com', NOW(), '$2y$12$zqlyA2UPBn8m//07sy4yMu0pJPXKBYt1kv8ZxwQiDO7cpayweXlZK', '+260971000005', 'active', 1, NOW(), NOW()),
(6, 'Mr. Patrick Ngosa', 'librarian@university.com', NOW(), '$2y$12$zqlyA2UPBn8m//07sy4yMu0pJPXKBYt1kv8ZxwQiDO7cpayweXlZK', '+260971000006', 'active', 1, NOW(), NOW()),
(7, 'Mrs. Ruth Zulu', 'hostel@university.com', NOW(), '$2y$12$zqlyA2UPBn8m//07sy4yMu0pJPXKBYt1kv8ZxwQiDO7cpayweXlZK', '+260971000007', 'active', 1, NOW(), NOW()),
(8, 'Mr. Charles Sikazwe', 'hr@university.com', NOW(), '$2y$12$zqlyA2UPBn8m//07sy4yMu0pJPXKBYt1kv8ZxwQiDO7cpayweXlZK', '+260971000008', 'active', 1, NOW(), NOW()),
(9, 'IT Administrator', 'it@university.com', NOW(), '$2y$12$zqlyA2UPBn8m//07sy4yMu0pJPXKBYt1kv8ZxwQiDO7cpayweXlZK', '+260971000009', 'active', 1, NOW(), NOW());

-- Assign roles to users
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),  -- Super Admin
(3, 'App\\Models\\User', 2),  -- Registrar
(4, 'App\\Models\\User', 3),  -- Finance Officer
(6, 'App\\Models\\User', 4),  -- Lecturer
(7, 'App\\Models\\User', 5),  -- Student
(8, 'App\\Models\\User', 6),  -- Librarian
(9, 'App\\Models\\User', 7),  -- Hostel Manager
(10, 'App\\Models\\User', 8), -- HR Officer
(11, 'App\\Models\\User', 9); -- IT Admin

-- ============================================================
-- SEED DATA: ACADEMIC STRUCTURE
-- ============================================================

INSERT INTO `academic_years` (`id`, `name`, `start_date`, `end_date`, `is_current`, `status`, `created_at`, `updated_at`) VALUES
(1, '2024/2025', '2024-08-01', '2025-07-31', 0, 'completed', NOW(), NOW()),
(2, '2025/2026', '2025-08-01', '2026-07-31', 1, 'active', NOW(), NOW()),
(3, '2026/2027', '2026-08-01', '2027-07-31', 0, 'upcoming', NOW(), NOW());

INSERT INTO `semesters` (`id`, `academic_year_id`, `name`, `start_date`, `end_date`, `registration_start`, `registration_end`, `is_current`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Semester 1 - 2024/25', '2024-08-12', '2024-12-15', '2024-07-15', '2024-08-09', 0, 'completed', NOW(), NOW()),
(2, 1, 'Semester 2 - 2024/25', '2025-01-13', '2025-05-30', '2024-12-16', '2025-01-10', 0, 'completed', NOW(), NOW()),
(3, 2, 'Semester 1 - 2025/26', '2025-08-11', '2025-12-14', '2025-07-14', '2025-08-08', 0, 'active', NOW(), NOW()),
(4, 2, 'Semester 2 - 2025/26', '2026-01-12', '2026-05-29', '2025-12-15', '2026-01-09', 1, 'active', NOW(), NOW());

INSERT INTO `faculties` (`id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Faculty of Engineering', 'FOE', 'Engineering programs including Civil, Mechanical, Electrical and Computer Engineering', 'active', NOW(), NOW()),
(2, 'Faculty of Business Studies', 'FBS', 'Business administration, accounting, finance and economics programs', 'active', NOW(), NOW()),
(3, 'Faculty of Science', 'FOS', 'Natural sciences including Biology, Chemistry, Physics and Mathematics', 'active', NOW(), NOW()),
(4, 'Faculty of Information Technology', 'FOIT', 'Computer science, software engineering and information systems', 'active', NOW(), NOW()),
(5, 'Faculty of Education', 'FOEd', 'Teacher education and educational management programs', 'active', NOW(), NOW()),
(6, 'Faculty of Health Sciences', 'FOHS', 'Nursing, public health and biomedical science programs', 'active', NOW(), NOW());

INSERT INTO `departments` (`id`, `faculty_id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Department of Civil Engineering', 'DCE', 'Structural, geotechnical and transportation engineering', 'active', NOW(), NOW()),
(2, 1, 'Department of Electrical Engineering', 'DEE', 'Power systems, electronics and communications', 'active', NOW(), NOW()),
(3, 2, 'Department of Business Administration', 'DBA', 'Management, marketing and entrepreneurship', 'active', NOW(), NOW()),
(4, 2, 'Department of Accounting and Finance', 'DAF', 'Financial accounting, auditing and finance', 'active', NOW(), NOW()),
(5, 3, 'Department of Computer Science', 'DCS', 'Algorithms, data structures and theoretical computing', 'active', NOW(), NOW()),
(6, 4, 'Department of Software Engineering', 'DSE', 'Software development, testing and project management', 'active', NOW(), NOW()),
(7, 4, 'Department of Information Systems', 'DIS', 'Database systems, networking and cybersecurity', 'active', NOW(), NOW()),
(8, 5, 'Department of Primary Education', 'DPE', 'Primary school teacher training', 'active', NOW(), NOW()),
(9, 6, 'Department of Nursing', 'DON', 'Registered nursing and midwifery programs', 'active', NOW(), NOW()),
(10, 6, 'Department of Public Health', 'DPH', 'Epidemiology, health promotion and environmental health', 'active', NOW(), NOW());

INSERT INTO `programs` (`id`, `department_id`, `name`, `code`, `level`, `duration_years`, `credit_hours_required`, `status`, `created_at`, `updated_at`) VALUES
(1, 6, 'Bachelor of Science in Software Engineering', 'BSSE', 'undergraduate', 4, 120, 'active', NOW(), NOW()),
(2, 5, 'Bachelor of Science in Computer Science', 'BSCS', 'undergraduate', 4, 120, 'active', NOW(), NOW()),
(3, 3, 'Bachelor of Business Administration', 'BBA', 'undergraduate', 4, 120, 'active', NOW(), NOW()),
(4, 4, 'Bachelor of Accountancy', 'BAcc', 'undergraduate', 4, 120, 'active', NOW(), NOW()),
(5, 1, 'Bachelor of Engineering (Civil)', 'BECiv', 'undergraduate', 5, 150, 'active', NOW(), NOW()),
(6, 9, 'Bachelor of Science in Nursing', 'BSN', 'undergraduate', 4, 130, 'active', NOW(), NOW()),
(7, 6, 'Master of Science in Software Engineering', 'MSSE', 'postgraduate', 2, 60, 'active', NOW(), NOW()),
(8, 3, 'Diploma in Business Management', 'DBM', 'diploma', 2, 60, 'active', NOW(), NOW());

-- Staff (referencing user 4 - Prof. David Tembo)
INSERT INTO `staff` (`id`, `user_id`, `staff_id`, `department_id`, `designation`, `specialization`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 'STAFF-2024-001', 6, 'Senior Lecturer', 'Software Engineering, Mobile Development', 'active', NOW(), NOW());

-- Demo Student
INSERT INTO `students` (`id`, `user_id`, `student_id`, `program_id`, `enrollment_date`, `year_of_study`, `student_type`, `gender`, `nationality`, `phone`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 'STU-2025-001', 1, '2025-08-11', 2, 'local', 'male', 'Zambian', '+260971000005', 'active', NOW(), NOW());

-- ============================================================
-- SEED DATA: FINANCE
-- ============================================================

INSERT INTO `fee_structures` (`id`, `name`, `academic_year_id`, `semester_id`, `program_id`, `student_type`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 'BSSE Semester 1 Fees 2025/26', 2, 3, 1, 'local', 8500.00, 'active', NOW(), NOW()),
(2, 'BSSE Semester 2 Fees 2025/26', 2, 4, 1, 'local', 8500.00, 'active', NOW(), NOW()),
(3, 'BBA Semester 1 Fees 2025/26', 2, 3, 3, 'local', 7500.00, 'active', NOW(), NOW()),
(4, 'BSN Semester 1 Fees 2025/26', 2, 3, 6, 'local', 9500.00, 'active', NOW(), NOW()),
(5, 'BSSE International Sem 1 2025/26', 2, 3, 1, 'international', 15000.00, 'active', NOW(), NOW());

INSERT INTO `fee_items` (`fee_structure_id`, `fee_type`, `description`, `amount`, `is_mandatory`, `created_at`, `updated_at`) VALUES
(1, 'Tuition', 'Semester 1 Tuition Fees', 6500.00, 1, NOW(), NOW()),
(1, 'Library', 'Library Access Fee', 250.00, 1, NOW(), NOW()),
(1, 'Technology', 'Computer Lab & IT Fee', 500.00, 1, NOW(), NOW()),
(1, 'Medical', 'Student Health Insurance', 200.00, 1, NOW(), NOW()),
(1, 'Sports', 'Sports & Recreation Fee', 150.00, 0, NOW(), NOW()),
(1, 'Examination', 'Examination Registration Fee', 500.00, 1, NOW(), NOW()),
(1, 'Registration', 'Semester Registration Fee', 400.00, 1, NOW(), NOW());

INSERT INTO `scholarships` (`id`, `name`, `type`, `description`, `coverage_type`, `coverage_value`, `max_recipients`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Vice Chancellor Scholarship', 'merit', 'Full scholarship for top 5 students per faculty based on GPA ≥ 3.8', 'percentage', 100.00, 30, 'active', NOW(), NOW()),
(2, 'Need-Based Financial Aid', 'need', 'Partial scholarship for financially disadvantaged students', 'percentage', 50.00, 100, 'active', NOW(), NOW()),
(3, 'Government Bursary', 'government', 'Government of Zambia Higher Education Bursary', 'percentage', 75.00, 200, 'active', NOW(), NOW()),
(4, 'Sports Excellence Award', 'sports', 'For student athletes representing the university at national level', 'fixed', 2500.00, 20, 'active', NOW(), NOW());

-- ============================================================
-- SEED DATA: HOSTELS
-- ============================================================

INSERT INTO `hostels` (`id`, `name`, `type`, `location`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Block A - Male Hostel', 'male', 'North Campus', 'Modern 3-storey male student accommodation with 120 rooms', 'active', NOW(), NOW()),
(2, 'Block B - Female Hostel', 'female', 'South Campus', 'Modern 3-storey female student accommodation with 100 rooms', 'active', NOW(), NOW()),
(3, 'Postgraduate Residences', 'mixed', 'East Campus', 'Self-contained postgraduate and international student accommodation', 'active', NOW(), NOW());

INSERT INTO `hostel_rooms` (`hostel_id`, `room_number`, `floor`, `room_type`, `capacity`, `status`, `created_at`, `updated_at`) VALUES
(1, 'A-101', 1, 'double', 2, 'available', NOW(), NOW()),
(1, 'A-102', 1, 'double', 2, 'available', NOW(), NOW()),
(1, 'A-103', 1, 'single', 1, 'available', NOW(), NOW()),
(1, 'A-201', 2, 'double', 2, 'occupied', NOW(), NOW()),
(1, 'A-202', 2, 'triple', 3, 'occupied', NOW(), NOW()),
(2, 'B-101', 1, 'double', 2, 'available', NOW(), NOW()),
(2, 'B-102', 1, 'double', 2, 'available', NOW(), NOW()),
(2, 'B-201', 2, 'single', 1, 'occupied', NOW(), NOW()),
(3, 'PG-101', 1, 'single', 1, 'available', NOW(), NOW()),
(3, 'PG-102', 1, 'single', 1, 'available', NOW(), NOW());

-- ============================================================
-- SEED DATA: LIBRARY
-- ============================================================

INSERT INTO `book_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Computer Science', 'Programming, algorithms, artificial intelligence', NOW(), NOW()),
(2, 'Engineering', 'Civil, Mechanical, Electrical Engineering', NOW(), NOW()),
(3, 'Business & Management', 'Management, marketing, entrepreneurship', NOW(), NOW()),
(4, 'Accounting & Finance', 'Financial accounting, auditing, taxation', NOW(), NOW()),
(5, 'Mathematics', 'Pure and applied mathematics, statistics', NOW(), NOW()),
(6, 'Health Sciences', 'Nursing, medicine, public health', NOW(), NOW()),
(7, 'Law', 'Legal texts, case studies, jurisprudence', NOW(), NOW()),
(8, 'General Reference', 'Dictionaries, encyclopedias, atlases', NOW(), NOW()),
(9, 'African Studies', 'African history, culture, literature', NOW(), NOW()),
(10, 'Journals & Periodicals', 'Academic journals and research publications', NOW(), NOW());

INSERT INTO `library_books` (`id`, `book_category_id`, `isbn`, `title`, `author`, `publisher`, `publication_year`, `edition`, `copies_total`, `copies_available`, `shelf_location`, `created_at`, `updated_at`) VALUES
(1, 1, '978-0-13-468599-1', 'Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', 'Prentice Hall', 2008, '1st', 5, 4, 'CS-A1', NOW(), NOW()),
(2, 1, '978-0-596-51774-8', 'JavaScript: The Good Parts', 'Douglas Crockford', "O'Reilly Media", 2008, '1st', 3, 3, 'CS-A2', NOW(), NOW()),
(3, 1, '978-0-13-110362-7', 'The C Programming Language', 'Brian Kernighan & Dennis Ritchie', 'Prentice Hall', 1988, '2nd', 4, 4, 'CS-A3', NOW(), NOW()),
(4, 3, '978-0-07-340183-7', 'Principles of Management', 'Harold Koontz', 'McGraw-Hill', 2017, '14th', 6, 5, 'BM-B1', NOW(), NOW()),
(5, 4, '978-0-07-811100-6', 'Financial Accounting', 'Jan Williams', 'McGraw-Hill', 2021, '17th', 8, 7, 'AF-C1', NOW(), NOW()),
(6, 5, '978-0-13-110362-7', 'Calculus: Early Transcendentals', 'James Stewart', 'Cengage Learning', 2020, '9th', 5, 5, 'MATH-D1', NOW(), NOW()),
(7, 2, '978-0-07-339811-7', 'Mechanics of Materials', 'Ferdinand Beer', 'McGraw-Hill', 2020, '8th', 4, 4, 'ENG-E1', NOW(), NOW());

-- ============================================================
-- SEED DATA: LEAVE TYPES
-- ============================================================

INSERT INTO `leave_types` (`id`, `name`, `days_allowed`, `is_paid`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Annual Leave', 30, 1, 'Annual vacation leave entitlement', NOW(), NOW()),
(2, 'Sick Leave', 30, 1, 'Medical sick leave with doctor certificate', NOW(), NOW()),
(3, 'Maternity Leave', 90, 1, 'Maternity leave for female employees', NOW(), NOW()),
(4, 'Paternity Leave', 5, 1, 'Paternity leave for male employees on birth of child', NOW(), NOW()),
(5, 'Study Leave', 10, 1, 'Academic study or examination leave', NOW(), NOW()),
(6, 'Compassionate Leave', 5, 1, 'Bereavement or family emergency leave', NOW(), NOW()),
(7, 'Unpaid Leave', 30, 0, 'Leave without pay - subject to HOD approval', NOW(), NOW());

-- ============================================================
-- SEED DATA: EMPLOYEES
-- ============================================================

INSERT INTO `employees` (`id`, `user_id`, `department_id`, `employee_id`, `designation`, `employment_type`, `join_date`, `basic_salary`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 6, 'EMP-2024-001', 'Senior Lecturer', 'permanent', '2024-01-15', 12500.00, 'active', NOW(), NOW()),
(2, 8, 3, 'EMP-2024-002', 'HR Officer', 'permanent', '2024-03-01', 9500.00, 'active', NOW(), NOW());

-- ============================================================
-- COURSES (subset)
-- ============================================================

INSERT INTO `courses` (`id`, `department_id`, `code`, `name`, `credits`, `level`, `course_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 6, 'SE101', 'Introduction to Software Engineering', 3, '100', 'core', 'active', NOW(), NOW()),
(2, 6, 'SE201', 'Object-Oriented Programming', 4, '200', 'core', 'active', NOW(), NOW()),
(3, 6, 'SE301', 'Software Design Patterns', 3, '300', 'core', 'active', NOW(), NOW()),
(4, 6, 'SE401', 'Final Year Project', 6, '400', 'core', 'active', NOW(), NOW()),
(5, 5, 'CS101', 'Introduction to Computer Science', 3, '100', 'core', 'active', NOW(), NOW()),
(6, 5, 'CS201', 'Data Structures and Algorithms', 4, '200', 'core', 'active', NOW(), NOW()),
(7, 3, 'BA101', 'Principles of Management', 3, '100', 'core', 'active', NOW(), NOW()),
(8, 4, 'AC101', 'Financial Accounting I', 3, '100', 'core', 'active', NOW(), NOW()),
(9, 6, 'SE211', 'Web Development', 3, '200', 'elective', 'active', NOW(), NOW()),
(10, 6, 'SE212', 'Mobile Application Development', 3, '200', 'elective', 'active', NOW(), NOW()),
(11, 5, 'CS301', 'Database Management Systems', 3, '300', 'core', 'active', NOW(), NOW()),
(12, 5, 'CS302', 'Operating Systems', 3, '300', 'core', 'active', NOW(), NOW()),
(13, 7, 'IS301', 'Network Administration', 3, '300', 'core', 'active', NOW(), NOW()),
(14, 6, 'SE150', 'Programming Fundamentals Lab', 2, '100', 'lab', 'active', NOW(), NOW()),
(15, 5, 'MATH101', 'Calculus for Engineers', 4, '100', 'core', 'active', NOW(), NOW());

-- Course offerings for current semester
INSERT INTO `course_offerings` (`id`, `course_id`, `academic_year_id`, `semester_id`, `lecturer_id`, `venue`, `max_students`, `enrolled_students`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 4, 1, 'Lecture Theatre 2', 60, 45, 'active', NOW(), NOW()),
(2, 9, 2, 4, 1, 'Computer Lab 1', 30, 28, 'active', NOW(), NOW()),
(3, 11, 2, 4, 1, 'Room 301', 50, 38, 'active', NOW(), NOW()),
(4, 6, 2, 4, 1, 'Lecture Theatre 1', 60, 52, 'active', NOW(), NOW());

-- ============================================================
-- ADMIN ACCOUNT - VERIFY CORRECT BCRYPT HASH
-- Run: php artisan tinker --execute="echo Hash::make('Admin@123');"
-- Then update the password field with the correct hash
-- Current hash is a valid bcrypt for 'Admin@123'
-- ============================================================

-- ============================================================
-- MIGRATION TRACKING TABLE (Laravel)
-- ============================================================

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2024_01_01_000001_create_users_table', 1),
('2024_01_01_000002_create_password_reset_tokens_table', 1),
('2024_01_01_000003_create_sessions_table', 1),
('2024_01_01_000004_create_personal_access_tokens_table', 1),
('2024_01_01_000005_create_permission_tables', 1),
('2024_01_02_000001_create_academic_tables', 1),
('2024_01_02_000002_create_student_staff_tables', 1),
('2024_01_02_000003_create_course_management_tables', 1),
('2024_01_02_000004_create_results_tables', 1),
('2024_01_02_000005_create_finance_tables', 1),
('2024_01_02_000006_create_hostel_tables', 1),
('2024_01_02_000007_create_library_tables', 1),
('2024_01_02_000008_create_hr_tables', 1),
('2024_01_02_000009_create_other_tables', 1);

-- ============================================================
-- FOREIGN KEY CONSTRAINTS
-- ============================================================

ALTER TABLE `departments`
  ADD CONSTRAINT `fk_dept_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`);

ALTER TABLE `programs`
  ADD CONSTRAINT `fk_prog_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

ALTER TABLE `semesters`
  ADD CONSTRAINT `fk_sem_acyear` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`);

ALTER TABLE `staff`
  ADD CONSTRAINT `fk_staff_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_staff_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

ALTER TABLE `students`
  ADD CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_student_prog` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE SET NULL;

ALTER TABLE `student_guardians`
  ADD CONSTRAINT `fk_guardian_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

ALTER TABLE `courses`
  ADD CONSTRAINT `fk_course_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

ALTER TABLE `course_offerings`
  ADD CONSTRAINT `fk_offering_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `fk_offering_acyear` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  ADD CONSTRAINT `fk_offering_sem` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`),
  ADD CONSTRAINT `fk_offering_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL;

ALTER TABLE `course_registrations`
  ADD CONSTRAINT `fk_reg_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `fk_reg_offering` FOREIGN KEY (`course_offering_id`) REFERENCES `course_offerings` (`id`);

ALTER TABLE `final_results`
  ADD CONSTRAINT `fk_fr_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `fk_fr_offering` FOREIGN KEY (`course_offering_id`) REFERENCES `course_offerings` (`id`);

ALTER TABLE `student_bills`
  ADD CONSTRAINT `fk_bill_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `fk_bill_fee_structure` FOREIGN KEY (`fee_structure_id`) REFERENCES `fee_structures` (`id`) ON DELETE SET NULL;

ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_bill` FOREIGN KEY (`student_bill_id`) REFERENCES `student_bills` (`id`);

ALTER TABLE `hostel_rooms`
  ADD CONSTRAINT `fk_room_hostel` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`id`);

ALTER TABLE `room_allocations`
  ADD CONSTRAINT `fk_alloc_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `fk_alloc_room` FOREIGN KEY (`hostel_room_id`) REFERENCES `hostel_rooms` (`id`);

ALTER TABLE `book_borrowings`
  ADD CONSTRAINT `fk_borrow_book` FOREIGN KEY (`library_book_id`) REFERENCES `library_books` (`id`),
  ADD CONSTRAINT `fk_borrow_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL;

ALTER TABLE `employees`
  ADD CONSTRAINT `fk_emp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_emp_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

ALTER TABLE `leave_requests`
  ADD CONSTRAINT `fk_leave_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `fk_leave_type` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`);

ALTER TABLE `payroll`
  ADD CONSTRAINT `fk_payroll_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

ALTER TABLE `announcements`
  ADD CONSTRAINT `fk_announce_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_msg_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);

ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `fk_mhr_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `fk_mhp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `fk_rhp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rhp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

-- ============================================================
-- END OF DUMP
-- ============================================================
--
-- HOW TO USE:
-- 1. Create database: mysql -u root -p -e "CREATE DATABASE university_management_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
-- 2. Import: mysql -u root -p university_management_system < university_management_system.sql
-- 3. Copy .env.example to .env, set DB credentials
-- 4. Run: php artisan key:generate
-- 5. NOTE: If using Laravel migrations instead of this SQL, run:
--    php artisan migrate --seed
--
-- DEFAULT LOGIN CREDENTIALS (password: Admin@123):
--   Super Admin:    admin@university.com
--   Registrar:      registrar@university.com
--   Finance:        finance@university.com
--   Lecturer:       lecturer@university.com
--   Student:        student@university.com
--   Librarian:      librarian@university.com
--   Hostel Manager: hostel@university.com
--   HR Officer:     hr@university.com
--   IT Admin:       it@university.com
-- ============================================================
