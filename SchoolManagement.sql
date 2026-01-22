-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jan 22, 2026 at 09:26 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `SchoolManagement`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int NOT NULL,
  `unit_id` int NOT NULL,
  `lecturer_id` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`assignment_id`, `unit_id`, `lecturer_id`, `title`, `description`, `file_path`, `due_date`, `created_at`) VALUES
(12, 26, 30, 'Writing', 'Write about a memorable visit', 'uploads/assignments/1768143892_HANDOUT_From_Experience_to_Research_Area.pdf', '2026-02-17', '2026-01-11 15:04:52'),
(16, 29, 25, 'Professional Portfolio Submission', '', 'uploads/assignments/1768751008_Text_F.pdf', '2026-03-26', '2026-01-18 15:43:28'),
(17, 28, 31, 'Software Project Delivery', 'Design, build, and test a functional software application, documenting your development process and final solution.', 'uploads/assignments/1768759504_App_Development_-_5.2.pdf', '2026-02-20', '2026-01-18 18:05:04');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_reminders`
--

CREATE TABLE `assignment_reminders` (
  `reminder_id` int NOT NULL,
  `assignment_id` int NOT NULL,
  `student_id` int NOT NULL,
  `reminder_time` datetime NOT NULL,
  `is_sent` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `badge_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `points_reward` int NOT NULL DEFAULT '0',
  `icon_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `behaviour_notes`
--

CREATE TABLE `behaviour_notes` (
  `note_id` int NOT NULL,
  `student_id` int NOT NULL,
  `teacher_id` int NOT NULL,
  `note_type` enum('Positive','Negative') COLLATE utf8mb4_general_ci NOT NULL,
  `severity` tinyint NOT NULL,
  `category` enum('Participation','Homework','Discipline') COLLATE utf8mb4_general_ci NOT NULL,
  `note_text` text COLLATE utf8mb4_general_ci NOT NULL,
  `follow_up` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int NOT NULL,
  `course_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `course_description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `course_description`, `created_at`) VALUES
(5, 'Economics', NULL, '2025-12-24 16:09:37'),
(10, 'English', NULL, '2025-12-26 14:33:56'),
(13, 'Interaction Design', NULL, '2026-01-18 11:40:22'),
(14, 'Introduction to Human–Computer Interaction', NULL, '2026-01-18 14:03:34');

-- --------------------------------------------------------

--
-- Table structure for table `course_timetable`
--

CREATE TABLE `course_timetable` (
  `id` int NOT NULL,
  `course_id` int NOT NULL,
  `class_day` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `subject_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `room_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `class_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int NOT NULL,
  `submission_id` int NOT NULL,
  `lecturer_id` int NOT NULL,
  `mark` decimal(5,2) DEFAULT NULL,
  `feedback` text COLLATE utf8mb4_general_ci,
  `graded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`grade_id`, `submission_id`, `lecturer_id`, `mark`, `feedback`, `graded_at`) VALUES
(6, 9, 31, 69.00, 'Good effort in designing and implementing the project. Your application functions as required, and your documentation clearly explains your development process and testing approach.', '2026-01-18 18:08:14');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `lecturer_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `surname` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `name`, `surname`, `email`) VALUES
(9, 25, 'Alice', 'Johnson', 'alice.johnson@gmail.com'),
(11, 30, 'Alma', 'Hasen', 'alma@gmail.com'),
(13, 31, 'Vincent', 'Magro', 'vincmag@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_units`
--

CREATE TABLE `lecturer_units` (
  `lecturer_unit_id` int NOT NULL,
  `lecturer_id` int DEFAULT NULL,
  `unit_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_units`
--

INSERT INTO `lecturer_units` (`lecturer_unit_id`, `lecturer_id`, `unit_id`) VALUES
(89, 30, 26),
(90, 30, 27),
(92, 25, 29),
(93, 31, 28);

-- --------------------------------------------------------

--
-- Table structure for table `lesson_summaries`
--

CREATE TABLE `lesson_summaries` (
  `id` int NOT NULL,
  `lesson_id` int NOT NULL,
  `summary_text` text COLLATE utf8mb4_general_ci,
  `outcomes` json DEFAULT NULL,
  `what_worked` text COLLATE utf8mb4_general_ci,
  `what_to_improve` text COLLATE utf8mb4_general_ci,
  `published` tinyint(1) DEFAULT '0',
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int NOT NULL,
  `parent_id` int NOT NULL,
  `student_id` int NOT NULL,
  `notification_type` enum('Behaviour Alert','General') COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parent_child_notes`
--

CREATE TABLE `parent_child_notes` (
  `note_id` int NOT NULL,
  `parent_id` int NOT NULL,
  `student_id` int NOT NULL,
  `teacher_id` int NOT NULL,
  `note_type` enum('Positive','Negative') COLLATE utf8mb4_general_ci NOT NULL,
  `severity` tinyint NOT NULL,
  `category` enum('Participation','Homework','Discipline') COLLATE utf8mb4_general_ci NOT NULL,
  `note_text` text COLLATE utf8mb4_general_ci NOT NULL,
  `follow_up` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performance_trends`
--

CREATE TABLE `performance_trends` (
  `trend_id` int NOT NULL,
  `student_id` int NOT NULL,
  `assignment_id` int NOT NULL,
  `unit_id` int NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `class_average` decimal(5,2) NOT NULL,
  `recorded_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_parent`
--

CREATE TABLE `student_parent` (
  `student_id` int NOT NULL,
  `parent_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_units`
--

CREATE TABLE `student_units` (
  `student_unit_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `unit_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_units`
--

INSERT INTO `student_units` (`student_unit_id`, `student_id`, `unit_id`) VALUES
(44, 28, 26),
(45, 26, 28),
(46, 28, 28),
(47, 17, 28),
(48, 27, 28);

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `submission_id` int NOT NULL,
  `assignment_id` int NOT NULL,
  `student_id` int NOT NULL,
  `submission_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`submission_id`, `assignment_id`, `student_id`, `submission_date`) VALUES
(7, 12, 28, '2026-01-11 15:11:06'),
(9, 17, 17, '2026-01-18 18:05:52');

-- --------------------------------------------------------

--
-- Table structure for table `submission_files`
--

CREATE TABLE `submission_files` (
  `file_id` int NOT NULL,
  `submission_id` int NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `upload_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submission_files`
--

INSERT INTO `submission_files` (`file_id`, `submission_id`, `file_path`, `upload_date`) VALUES
(5, 7, 'uploads/submissions/1768144266_0_news9.png', '2026-01-11 15:11:06'),
(7, 9, 'uploads/submissions/1768759552_0_Text_A.pdf', '2026-01-18 18:05:52');

-- --------------------------------------------------------

--
-- Table structure for table `thematic_items`
--

CREATE TABLE `thematic_items` (
  `id` int NOT NULL,
  `plan_id` int NOT NULL,
  `topic_title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `planned_date` date DEFAULT NULL,
  `order_index` int DEFAULT '0',
  `learning_objectives` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thematic_plans`
--

CREATE TABLE `thematic_plans` (
  `id` int NOT NULL,
  `unit_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int NOT NULL,
  `subject_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class_day` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `start_time` time NOT NULL,
  `room_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `class_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `subject_name`, `class_day`, `start_time`, `room_number`, `class_type`, `user_id`, `course_id`, `end_time`) VALUES
(1, 'Computer Science', 'Monday', '09:00:00', 'Room 101', '', 7, 13, '00:00:00'),
(2, 'Mathematics', 'Monday', '11:00:00', 'Room 204', '', 7, 13, '00:00:00'),
(3, 'Web Development', 'Wednesday', '14:00:00', 'Lab 3', '', 7, 13, '00:00:00'),
(4, 'English', 'Monday', '08:00:00', 'Room 123', '', 7, 13, '00:00:00'),
(5, 'Programming I', 'Monday', '09:00:00', 'Lab 102', 'Lecture', NULL, 13, '10:30:00'),
(6, 'Mathematics', 'Monday', '11:00:00', 'Room 305', 'Seminar', NULL, 13, '12:30:00'),
(7, 'Web Development', 'Tuesday', '10:00:00', 'Online', 'Workshop', NULL, 13, '11:30:00'),
(8, 'Logic & Algorithms', 'Tuesday', '13:00:00', 'Room 201', 'Lecture', NULL, 13, '14:30:00'),
(9, 'Software Project', 'Wednesday', '09:00:00', 'Lab 105', 'Practical', NULL, 13, '12:00:00'),
(10, 'Database Systems', 'Wednesday', '14:00:00', 'Room 402', 'Lecture', NULL, 13, '15:30:00'),
(11, 'User Interface Design', 'Thursday', '09:00:00', 'Online', 'Lecture', NULL, 13, '10:30:00'),
(12, 'UI Design Lab', 'Thursday', '11:00:00', 'Lab 202', 'Practical', NULL, 13, '12:30:00'),
(13, 'Mobile App Development', 'Friday', '10:00:00', 'Lab 108', 'Workshop', NULL, 13, '13:00:00'),
(14, 'Professional Practice', 'Friday', '15:00:00', 'Room 101', 'Seminar', NULL, 13, '16:30:00'),
(25, 'Marketing', 'Tuesday', '08:00:00', '216', 'Seminar', NULL, 5, '09:30:00'),
(26, 'General English', 'Wednesday', '08:00:00', '116', 'Practical', NULL, 10, '08:50:00');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `unit_id` int NOT NULL,
  `unit_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `unit_description` text COLLATE utf8mb4_general_ci,
  `course_id` int DEFAULT NULL,
  `created_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_id`, `unit_name`, `unit_description`, `course_id`, `created_by`) VALUES
(26, 'General English', 'to learn English grammar, vocabulary, spelling, and general English-speaking practice sessions', 10, 15),
(27, '	User Interface Design', 'Interaction Design focuses on how users interact with digital products. It aims to make interfaces intuitive, useful, and enjoyable to use. The goal is smooth communication between people and technology.', 13, 15),
(28, '	Software Project', '', 13, 15),
(29, '	Professional Practice', 'Introduction to Human–Computer Interaction focuses on understanding human behavior to create more usable and accessible technologies', 14, 15);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('Admin','Lecturer','Student','Parents') COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `surname` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `course_id` int DEFAULT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `name`, `surname`, `date_of_birth`, `email`, `created_at`, `course_id`, `profile_photo`) VALUES
(7, 'musa', '$2y$10$tbuo00m4RU/50fNeo.UXhOU6aPQ9tHMdSW/urF0P8fWXuR5sBXhmK', 'Student', 'Musa', 'Nurtaza', '2000-08-04', 'musa@gmail.com', '2025-12-19 17:01:49', 5, NULL),
(10, 'vinc', '$2y$10$8ODJqeI.lbUSCWoJ4yhJL.j8PB..I4lD7olnj4VNSwi9VMOZgqwjq', 'Student', 'Wer', 'Sag', '2001-06-30', 'skinc@gmail.com', '2025-12-19 18:13:24', NULL, NULL),
(11, 'joe', '$2y$10$NuLq.IxTCOAQtJU1jkZjEuQJZDBDZeoNk8ebAsCCQGWK1Wj8e3E5G', 'Student', 'Joe', 'Mag', '1992-07-07', 'joe@gmail.com', '2025-12-19 19:04:50', 5, NULL),
(12, 'asia', '$2y$10$9L6230F1x.9QTJC0S5nV2Oq1rkoTaaAw5z1jXHy4JErqkcqu4Ji2q', 'Student', 'Asia', 'Iska', '1990-06-25', 'soe@gmail.com', '2025-12-19 19:43:17', 10, NULL),
(13, 'biken', '$2y$10$4SX7xBZCtX710j1Tvszd9e4Lv3QCfBhY67wFQC1OKg44Aj8NXg0Fa', 'Student', 'Biken', 'Nurad', '2000-05-06', 'sah@gmail.com', '2025-12-19 20:07:06', 5, NULL),
(14, 'sana', '$2y$10$Iej8kSHlTVoeClr8V29rreKcJ.fAQsENeP.pG//crQRUSXdkywbj2', 'Student', 'Sana', 'Kand', '2002-04-19', 'sana@mail.caom', '2025-12-20 08:34:23', 5, NULL),
(15, 'nora', '$2y$10$HgwFGLb.RS5pF1LB2ZNYPe7XYCaA8AbnMu81ZgP5jO5yPZ0vKvQ62', 'Admin', 'Norra', 'Nurt', '1980-07-03', 'nora@mail.caom', '2025-12-20 08:56:04', NULL, 'includes/uploads/profile_photos/user_15_1768144188.png'),
(17, 'alim', '$2y$10$qtZl8JpwWH3NPUTq15BJtupDL.ijTq3TBK/R3Bj.zNMYDYMvXu2kS', 'Student', 'Alim', 'Alibek', '1999-11-04', 'alim@gmail.com', '2025-12-30 18:01:40', 13, 'includes/uploads/profile_photos/user_17_1767975388.jpg'),
(25, 'alicejohnson', '$2y$10$Z7bw5Pn5tKLZCBF2WNVNoOzJOt4YoVYbKgCG8cMZ2pI58Ovv4sF9u', 'Lecturer', 'Alice', 'Johnson', '1975-03-02', 'alice.johnson@gmail.com', '2026-01-04 12:32:24', NULL, 'includes/uploads/profile_photos/user_25_1767975532.webp'),
(26, 'humo', '$2y$10$MtgpE8M7ffVG3AU6KmXiM.hRF.GPzH4NQNnjeJSfL2rSIUWMpQzB6', 'Student', 'Humo', 'Seidov', '1998-12-07', 'humo@gmail.com', '2026-01-10 06:59:25', 13, 'includes/uploads/profile_photos/user_26_1768639257.webp'),
(27, 'max', '$2y$10$AzrPdSa6ykcajDaGt9EqMeZxbaRzZzUGwNCPFbPhoS2bwWhtoaUpG', 'Student', 'Maxim', 'Samouil', '2026-07-30', 'max@gmail.com', '2026-01-10 07:01:41', 13, 'includes/uploads/profile_photos/user_27_1768060053.jpg'),
(28, 'mustaha', '$2y$10$f3zGsTkM4Peu81nLihcl8uu.wQ1UmfTBGmk2dv7JN1M.L1L2FROtO', 'Student', 'Mustafa', 'Machm', '1995-09-20', 'must@gmail.com', '2026-01-10 17:43:14', 13, 'includes/uploads/profile_photos/user_28_1768144069.jpg'),
(30, 'almahas', '$2y$10$4oIjvm95Qlm/jmcMXewTDO.1d1zVo0b68dpempXJa2hDpbAbixuYy', 'Lecturer', 'Alma', 'Hasen', '1980-07-31', 'alma@gmail.com', '2026-01-11 14:52:12', NULL, 'includes/uploads/profile_photos/user_30_1768143281.png'),
(31, 'vincmag', '$2y$10$t/E9D8b7ZdKuGPv8c1QZR.sq142UrbUBgYmy9yAwn1wy47Cv9byta', 'Lecturer', 'Vincent', 'Magro', '1966-11-11', 'vincmag@gmail.com', '2026-01-18 17:52:49', NULL, 'includes/uploads/profile_photos/user_31_1768758786.png');

-- --------------------------------------------------------

--
-- Table structure for table `user_badges`
--

CREATE TABLE `user_badges` (
  `user_badge_id` int NOT NULL,
  `user_id` int NOT NULL,
  `badge_id` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `awarded_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_points`
--

CREATE TABLE `user_points` (
  `user_point_id` int NOT NULL,
  `user_id` int NOT NULL,
  `points` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `fk_assign_unit` (`unit_id`),
  ADD KEY `fk_assign_lecturer` (`lecturer_id`);

--
-- Indexes for table `assignment_reminders`
--
ALTER TABLE `assignment_reminders`
  ADD PRIMARY KEY (`reminder_id`),
  ADD KEY `fk_ar_assignment` (`assignment_id`),
  ADD KEY `fk_ar_student` (`student_id`);

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`badge_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `behaviour_notes`
--
ALTER TABLE `behaviour_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `fk_bn_student` (`student_id`),
  ADD KEY `fk_bn_teacher` (`teacher_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `course_timetable`
--
ALTER TABLE `course_timetable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD UNIQUE KEY `uq_grades_submission` (`submission_id`),
  ADD KEY `fk_g_lecturer` (`lecturer_id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`lecturer_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `lecturer_units`
--
ALTER TABLE `lecturer_units`
  ADD PRIMARY KEY (`lecturer_unit_id`),
  ADD KEY `fk_lu_lecturer` (`lecturer_id`),
  ADD KEY `fk_lu_unit` (`unit_id`);

--
-- Indexes for table `lesson_summaries`
--
ALTER TABLE `lesson_summaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ls_lesson` (`lesson_id`),
  ADD KEY `fk_ls_creator` (`created_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_n_parent` (`parent_id`),
  ADD KEY `fk_n_student` (`student_id`);

--
-- Indexes for table `parent_child_notes`
--
ALTER TABLE `parent_child_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `fk_pcn_parent` (`parent_id`),
  ADD KEY `fk_pcn_student` (`student_id`),
  ADD KEY `fk_pcn_teacher` (`teacher_id`);

--
-- Indexes for table `performance_trends`
--
ALTER TABLE `performance_trends`
  ADD PRIMARY KEY (`trend_id`),
  ADD KEY `fk_pt_assignment` (`assignment_id`),
  ADD KEY `fk_pt_student` (`student_id`),
  ADD KEY `fk_pt_unit` (`unit_id`);

--
-- Indexes for table `student_parent`
--
ALTER TABLE `student_parent`
  ADD PRIMARY KEY (`student_id`,`parent_id`),
  ADD KEY `fk_sp_parent` (`parent_id`);

--
-- Indexes for table `student_units`
--
ALTER TABLE `student_units`
  ADD PRIMARY KEY (`student_unit_id`),
  ADD KEY `fk_su_student` (`student_id`),
  ADD KEY `fk_su_unit` (`unit_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `fk_sub_assignment` (`assignment_id`),
  ADD KEY `fk_sub_student` (`student_id`);

--
-- Indexes for table `submission_files`
--
ALTER TABLE `submission_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `fk_sf_submission` (`submission_id`);

--
-- Indexes for table `thematic_items`
--
ALTER TABLE `thematic_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ti_plan` (`plan_id`);

--
-- Indexes for table `thematic_plans`
--
ALTER TABLE `thematic_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tp_unit` (`unit_id`),
  ADD KEY `fk_tp_creator` (`created_by`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_timetable_course` (`course_id`),
  ADD KEY `idx_timetable_user` (`user_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unit_id`),
  ADD KEY `fk_units_course` (`course_id`),
  ADD KEY `fk_unit_admin` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_user_course` (`course_id`);

--
-- Indexes for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`user_badge_id`),
  ADD KEY `fk_userbadges_user` (`user_id`),
  ADD KEY `fk_userbadges_badge` (`badge_id`);

--
-- Indexes for table `user_points`
--
ALTER TABLE `user_points`
  ADD PRIMARY KEY (`user_point_id`),
  ADD KEY `fk_userpoints_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `assignment_reminders`
--
ALTER TABLE `assignment_reminders`
  MODIFY `reminder_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `badge_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `behaviour_notes`
--
ALTER TABLE `behaviour_notes`
  MODIFY `note_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `course_timetable`
--
ALTER TABLE `course_timetable`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `lecturer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lecturer_units`
--
ALTER TABLE `lecturer_units`
  MODIFY `lecturer_unit_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `lesson_summaries`
--
ALTER TABLE `lesson_summaries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parent_child_notes`
--
ALTER TABLE `parent_child_notes`
  MODIFY `note_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performance_trends`
--
ALTER TABLE `performance_trends`
  MODIFY `trend_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_units`
--
ALTER TABLE `student_units`
  MODIFY `student_unit_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `submission_files`
--
ALTER TABLE `submission_files`
  MODIFY `file_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `thematic_items`
--
ALTER TABLE `thematic_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `thematic_plans`
--
ALTER TABLE `thematic_plans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `unit_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `user_badges`
--
ALTER TABLE `user_badges`
  MODIFY `user_badge_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_points`
--
ALTER TABLE `user_points`
  MODIFY `user_point_id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `fk_assign_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_assign_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

--
-- Constraints for table `assignment_reminders`
--
ALTER TABLE `assignment_reminders`
  ADD CONSTRAINT `fk_ar_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ar_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `badges`
--
ALTER TABLE `badges`
  ADD CONSTRAINT `badges_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `behaviour_notes`
--
ALTER TABLE `behaviour_notes`
  ADD CONSTRAINT `fk_bn_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bn_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `fk_g_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_g_submission` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`) ON DELETE CASCADE;

--
-- Constraints for table `lecturer_units`
--
ALTER TABLE `lecturer_units`
  ADD CONSTRAINT `fk_lu_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lu_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_summaries`
--
ALTER TABLE `lesson_summaries`
  ADD CONSTRAINT `fk_ls_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ls_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `thematic_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_n_parent` FOREIGN KEY (`parent_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_n_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `parent_child_notes`
--
ALTER TABLE `parent_child_notes`
  ADD CONSTRAINT `fk_pcn_parent` FOREIGN KEY (`parent_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pcn_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pcn_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `performance_trends`
--
ALTER TABLE `performance_trends`
  ADD CONSTRAINT `fk_pt_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pt_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pt_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_parent`
--
ALTER TABLE `student_parent`
  ADD CONSTRAINT `fk_sp_parent` FOREIGN KEY (`parent_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sp_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_units`
--
ALTER TABLE `student_units`
  ADD CONSTRAINT `fk_su_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_su_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `fk_sub_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sub_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `submission_files`
--
ALTER TABLE `submission_files`
  ADD CONSTRAINT `fk_sf_submission` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`) ON DELETE CASCADE;

--
-- Constraints for table `thematic_items`
--
ALTER TABLE `thematic_items`
  ADD CONSTRAINT `fk_ti_plan` FOREIGN KEY (`plan_id`) REFERENCES `thematic_plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `thematic_plans`
--
ALTER TABLE `thematic_plans`
  ADD CONSTRAINT `fk_tp_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tp_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `fk_unit_admin` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_unit_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE SET NULL;

--
-- Constraints for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD CONSTRAINT `fk_userbadges_badge` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`badge_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_userbadges_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_points`
--
ALTER TABLE `user_points`
  ADD CONSTRAINT `fk_userpoints_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
