-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2026 at 10:19 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sms`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `level` int(11) NOT NULL,
  `max_students` int(11) DEFAULT 40,
  `current_students` int(11) DEFAULT 0,
  `headmaster_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`, `level`, `max_students`, `current_students`, `headmaster_id`) VALUES
(1, 'Darasa la kwanza', 1, 45, 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `mark_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `test1` int(11) DEFAULT NULL,
  `test2` int(11) DEFAULT NULL,
  `groupwork1` int(11) DEFAULT NULL,
  `groupwork2` int(11) DEFAULT NULL,
  `exam` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `grade` varchar(2) NOT NULL,
  `approved_by_headmaster` tinyint(1) DEFAULT 0,
  `locked` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`mark_id`, `student_id`, `subject_id`, `teacher_id`, `test1`, `test2`, `groupwork1`, `groupwork2`, `exam`, `total`, `grade`, `approved_by_headmaster`, `locked`) VALUES
(17, 1, 2, 2, 15, 14, 9, 8, 35, 81, 'A', 1, 1),
(18, 2, 2, 2, 3, 5, 5, 7, 32, 52, 'C', 1, 1),
(19, 3, 2, 2, 7, 8, 9, 4, 22, 50, 'C', 1, 1),
(20, 5, 2, 2, 5, 4, 7, 3, 12, 31, 'F', 1, 1),
(21, 1, 1, 3, 15, 14, 9, 8, 35, 81, 'A', 1, 1),
(22, 2, 1, 3, 3, 5, 5, 7, 32, 52, 'C', 1, 1),
(23, 3, 1, 3, 7, 8, 9, 4, 22, 50, 'C', 1, 1),
(24, 5, 1, 3, 5, 4, 7, 3, 12, 31, 'F', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `headmaster_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `class_id`, `headmaster_id`) VALUES
(1, 7, 1, 2),
(2, 8, 1, 2),
(3, 9, 1, 2),
(4, 10, 1, 2),
(5, 11, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(50) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `headmaster_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_name`, `class_id`, `headmaster_id`) VALUES
(1, 'Engilish', 1, 2),
(2, 'Mathematics', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `headmaster_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `user_id`, `subject`, `class_id`, `headmaster_id`) VALUES
(2, 6, NULL, NULL, 2),
(3, 12, NULL, NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subjects`
--

CREATE TABLE `teacher_subjects` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_subjects`
--

INSERT INTO `teacher_subjects` (`id`, `teacher_id`, `subject_id`, `class_id`) VALUES
(1, 2, 2, 1),
(2, 3, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `role` enum('admin','headmaster','teacher','student') DEFAULT NULL,
  `reg_no` varchar(20) DEFAULT NULL,
  `employee_no` varchar(20) DEFAULT NULL,
  `nida` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `sex`, `email`, `date_of_birth`, `role`, `reg_no`, `employee_no`, `nida`, `password`, `created_at`) VALUES
(1, 'Said Hemed', 'Male', 'admin@school.com', '1980-01-01', 'admin', NULL, 'ADM001', '1234567890', 'e86f78a8a3caf0b60d8e74e5942aa6d86dc150cd3c03338aef25b7d2d7e3acc7', '2026-06-03 08:03:56'),
(2, 'Chibu hamza', 'Male', 'yathribhamza636@gmail.com', '2011-01-03', 'headmaster', NULL, 'H-12345', '12345678901234567891', 'c797bd812e10e4ba5c25c916946a7a6a36306cf1831c20ccaf082b1de2912ddd', '2026-06-03 08:05:12'),
(6, 'Halima Sururu', 'Female', 'teacher@gmail.com', '2003-02-03', 'teacher', NULL, 'T-002', '12345678901234567890', 'c797bd812e10e4ba5c25c916946a7a6a36306cf1831c20ccaf082b1de2912ddd', '2026-06-03 19:23:11'),
(7, 'Samweli juma omary', 'Male', 'student@gmail.com', '2015-06-03', 'student', 'S001', NULL, NULL, 'c797bd812e10e4ba5c25c916946a7a6a36306cf1831c20ccaf082b1de2912ddd', '2026-06-03 19:25:04'),
(8, 'Festus hamadi', 'Male', 'festushamza123@gmail.com', '2016-01-04', 'student', 'S002', NULL, NULL, 'c797bd812e10e4ba5c25c916946a7a6a36306cf1831c20ccaf082b1de2912ddd', '2026-06-04 10:15:15'),
(9, 'Bruno mlelwa', 'Male', 'brunomlelwa@gmail.com', '2013-06-04', 'student', 'S003', NULL, NULL, 'c797bd812e10e4ba5c25c916946a7a6a36306cf1831c20ccaf082b1de2912ddd', '2026-06-04 10:16:36'),
(10, 'feliste ngaiza', 'Female', 'felisterngaiza@gmail.com', '2016-03-04', 'student', 'S004', NULL, NULL, 'c797bd812e10e4ba5c25c916946a7a6a36306cf1831c20ccaf082b1de2912ddd', '2026-06-04 10:17:41'),
(11, 'Kipepeo mhina', 'Female', 'kipepeomhina@gmail.com', '2014-02-04', 'student', 'S005', NULL, NULL, 'c797bd812e10e4ba5c25c916946a7a6a36306cf1831c20ccaf082b1de2912ddd', '2026-06-04 10:18:40'),
(12, 'Chibu hamza', 'Male', 'hamza@gmail.com', '1997-02-04', 'teacher', NULL, 'T-001', '09876543210987654321', 'c797bd812e10e4ba5c25c916946a7a6a36306cf1831c20ccaf082b1de2912ddd', '2026-06-04 12:06:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`mark_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `mark_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `marks`
--
ALTER TABLE `marks`
  ADD CONSTRAINT `marks_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `marks_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`),
  ADD CONSTRAINT `marks_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`);

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`);

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `teachers_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`);

--
-- Constraints for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD CONSTRAINT `teacher_subjects_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_subjects_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
