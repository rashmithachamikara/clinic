-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2024 at 08:02 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clinic`
--

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `patient_id` varchar(16) NOT NULL,
  `patient_name` varchar(128) DEFAULT NULL,
  `patient_registered_date` datetime DEFAULT current_timestamp(),
  `patient_type` int(8) NOT NULL,
  `patient_supervisor_id` int(11) DEFAULT NULL,
  `patient_birthday` date DEFAULT NULL,
  `patient_phone1` varchar(64) DEFAULT NULL,
  `patient_phone2` varchar(64) DEFAULT NULL,
  `patient_email` varchar(128) DEFAULT NULL,
  `patient_address` text DEFAULT NULL,
  `patient_district` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `patient_attendance`
--

CREATE TABLE `patient_attendance` (
  `patient_attendance_id` int(11) NOT NULL,
  `patient_attendance_date` date NOT NULL DEFAULT current_timestamp(),
  `patient_attendance_time` time NOT NULL DEFAULT current_timestamp(),
  `patient_id` int(11) NOT NULL COMMENT 'Use system id here instead of given id',
  `patient_attendance_queue_id` int(11) DEFAULT NULL,
  `patient_attendance_supervisor_id` int(11) DEFAULT NULL,
  `patient_attendance_position` int(11) NOT NULL,
  `patient_attendance_remark` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `patient_attendance_queue`
--

CREATE TABLE `patient_attendance_queue` (
  `patient_attendance_queue_id` int(11) NOT NULL,
  `patient_attendance_queue_name` varchar(64) NOT NULL,
  `patient_attendance_queue_description` text NOT NULL,
  `patient_attendance_queue_color` varchar(16) NOT NULL DEFAULT '#d9d9d9',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `patient_files`
--

CREATE TABLE `patient_files` (
  `patient_file_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_file_version` int(11) NOT NULL DEFAULT 1,
  `patient_file_date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `patient_file_body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `patient_types`
--

CREATE TABLE `patient_types` (
  `patient_type_id` int(11) NOT NULL,
  `patient_type_name` varchar(64) NOT NULL,
  `patient_type_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `patient_types`
--

INSERT INTO `patient_types` (`patient_type_id`, `patient_type_name`, `patient_type_description`) VALUES
(1, 'Clinic', 'Basic clinical patient'),
(2, 'Appointment', 'Doctors appointment');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `staff_name` varchar(128) NOT NULL,
  `staff_role_id` int(16) NOT NULL DEFAULT 3,
  `staff_birthday` date NOT NULL,
  `staff_phone1` varchar(32) NOT NULL,
  `staff_phone2` varchar(32) NOT NULL,
  `staff_email` varchar(128) NOT NULL,
  `staff_address` text NOT NULL,
  `staff_color` varchar(16) NOT NULL DEFAULT '#203040'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `staff_role`
--

CREATE TABLE `staff_role` (
  `staff_role_id` int(11) NOT NULL,
  `staff_role_name` varchar(128) NOT NULL,
  `staff_role_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `staff_role`
--

INSERT INTO `staff_role` (`staff_role_id`, `staff_role_name`, `staff_role_description`) VALUES
(1, 'Doctor', 'A physician, medical practitioner, medical doctor, or simply doctor, is a health professional who practices medicine, which is concerned with promoting, maintaining or restoring health through the study, diagnosis, prognosis and treatment of disease, injury, and other physical and mental impairments.'),
(2, 'Nurse', 'A person who cares for the sick or infirm specifically: a licensed health-care professional who practices independently or is supervised by a physician, surgeon, or dentist and who is skilled in promoting and maintaining health — see licensed practical nurse, licensed vocational nurse, a registered nurse.'),
(3, 'Member', 'Default role to assign anyone to the system.'),
(21, 'Manager', 'Manages the clinic');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patient_id` (`patient_id`);

--
-- Indexes for table `patient_attendance`
--
ALTER TABLE `patient_attendance`
  ADD PRIMARY KEY (`patient_attendance_id`),
  ADD UNIQUE KEY `patient_attendance_date` (`patient_attendance_date`,`patient_id`);

--
-- Indexes for table `patient_attendance_queue`
--
ALTER TABLE `patient_attendance_queue`
  ADD PRIMARY KEY (`patient_attendance_queue_id`);

--
-- Indexes for table `patient_files`
--
ALTER TABLE `patient_files`
  ADD PRIMARY KEY (`patient_file_id`);

--
-- Indexes for table `patient_types`
--
ALTER TABLE `patient_types`
  ADD PRIMARY KEY (`patient_type_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `staff_role`
--
ALTER TABLE `staff_role`
  ADD PRIMARY KEY (`staff_role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_attendance`
--
ALTER TABLE `patient_attendance`
  MODIFY `patient_attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_attendance_queue`
--
ALTER TABLE `patient_attendance_queue`
  MODIFY `patient_attendance_queue_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_files`
--
ALTER TABLE `patient_files`
  MODIFY `patient_file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_types`
--
ALTER TABLE `patient_types`
  MODIFY `patient_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_role`
--
ALTER TABLE `staff_role`
  MODIFY `staff_role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
