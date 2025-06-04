-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2025 at 08:29 PM
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
-- Database: `mypetakom`
--

-- --------------------------------------------------------

--
-- Table structure for table `advisor`
--

CREATE TABLE `advisor` (
  `advisor_id` varchar(10) NOT NULL,
  `advisor_name` varchar(255) NOT NULL,
  `advisor_email` varchar(255) NOT NULL,
  `advisor_username` varchar(255) NOT NULL,
  `advisor_pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advisor`
--

INSERT INTO `advisor` (`advisor_id`, `advisor_name`, `advisor_email`, `advisor_username`, `advisor_pass`) VALUES
('A1', 'Iman', 'iman@gmail.com', 'Iman', 'iman456'),
('A2', 'Daniel', 'daniel@gmail.com', 'Daniel', 'daniel456'),
('A3', 'Hakim', 'hakim@gmail.com', 'Hakim', 'hakim456'),
('A4', 'Syafiqah', 'syafiqah@gmail.com', 'Syafiqah', 'syafiqah456'),
('A5', 'Hidayah', 'hidayah@gmail.com', 'Hidayah', 'hidayah456');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` varchar(10) NOT NULL,
  `attendee_date_time` datetime NOT NULL,
  `attendee_location` varchar(100) NOT NULL,
  `event_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `event_id` varchar(255) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_description` varchar(500) DEFAULT NULL,
  `event_date_time` datetime NOT NULL,
  `event_status` enum('Active','Postponed','Cancelled') NOT NULL DEFAULT 'Active',
  `event_location` varchar(255) NOT NULL,
  `approval_letter` varchar(255) NOT NULL,
  `merit_applied` enum('Yes','No') NOT NULL DEFAULT 'No',
  `qr_code_link` varchar(255) DEFAULT NULL,
  `advisor_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`event_id`, `event_name`, `event_description`, `event_date_time`, `event_status`, `event_location`, `approval_letter`, `merit_applied`, `qr_code_link`, `advisor_id`) VALUES
('EVT006', 'Cybersecurity Workshop', 'Hands-on workshop on cybersecurity basics', '2025-09-10 10:00:00', 'Active', '3.1500°N, 101.7000°E', '/files/EVT006_approval.pdf', 'Yes', '/qrcode/EVT006', 'A2');

-- --------------------------------------------------------

--
-- Table structure for table `event_committee`
--

CREATE TABLE `event_committee` (
  `committee_id` varchar(255) NOT NULL,
  `committee_position` varchar(100) NOT NULL,
  `merit_point` int(11) NOT NULL,
  `event_id` varchar(255) NOT NULL,
  `student_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_qr_code`
--

CREATE TABLE `event_qr_code` (
  `qr_code_id` varchar(255) NOT NULL,
  `qr_data` varchar(255) NOT NULL,
  `qr_date` date NOT NULL,
  `event_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

CREATE TABLE `membership` (
  `membership_id` varchar(10) NOT NULL,
  `membership_name` varchar(255) DEFAULT NULL,
  `status` enum('approved','rejected','pending') NOT NULL,
  `upload_card` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merit`
--

CREATE TABLE `merit` (
  `merit_id` varchar(255) NOT NULL,
  `student_id` varchar(10) NOT NULL,
  `event_id` varchar(255) NOT NULL,
  `advisor_id` varchar(10) NOT NULL,
  `position` varchar(50) DEFAULT NULL,
  `merit_score` varchar(50) DEFAULT NULL,
  `semester` varchar(10) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `approval_status` varchar(10) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merit_application`
--

CREATE TABLE `merit_application` (
  `merit_application_id` varchar(255) NOT NULL,
  `application_status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `submission_date` date NOT NULL,
  `approval_date` date DEFAULT NULL,
  `event_id` varchar(255) NOT NULL,
  `advisor_id` varchar(10) NOT NULL,
  `staff_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merit_claim`
--

CREATE TABLE `merit_claim` (
  `claim_id` varchar(10) NOT NULL,
  `student_id` varchar(10) NOT NULL,
  `event_id` varchar(255) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_level` varchar(50) NOT NULL,
  `position` varchar(50) NOT NULL,
  `support_doc` varchar(255) DEFAULT NULL,
  `claim_status` enum('ACCEPTED','REJECTED','PENDING') DEFAULT 'PENDING',
  `claim_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `merit_claim`
--

INSERT INTO `merit_claim` (`claim_id`, `student_id`, `event_id`, `event_name`, `event_level`, `position`, `support_doc`, `claim_status`, `claim_date`) VALUES
('C1903', 'cd24324', 'EVT006', 'Cybersecurity Workshop', 'International', 'Main', 'uploads/6830a8d358aeb_BCS2243_Project Instruction_SEM II_2024_25_250503_205120.pdf', 'PENDING', '2025-05-23'),
('C2754', 'cd24324', 'EVT006', 'Cybersecurity Workshop', 'International', 'Main', 'uploads/6830b4635c1df_BCS2173_Sem II_20242025_Group Project_250417_080550_080607 (1).pdf', 'PENDING', '2025-05-23'),
('C4120', 'qwe', 'EVT006', 'Cybersecurity Workshop', 'International', 'Main', 'uploads/6830be3619c49_BCS2243_Project Instruction_SEM II_2024_25_250503_205120.pdf', 'PENDING', '2025-05-23');

-- --------------------------------------------------------

--
-- Table structure for table `petakom_coordinator`
--

CREATE TABLE `petakom_coordinator` (
  `staff_id` varchar(10) NOT NULL,
  `staff_name` varchar(255) NOT NULL,
  `staff_email` varchar(255) NOT NULL,
  `staff_username` varchar(255) NOT NULL,
  `staff_pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` varchar(10) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_course` varchar(50) NOT NULL,
  `student_username` varchar(255) NOT NULL,
  `student_pass` varchar(255) NOT NULL,
  `student_email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `student_name`, `student_course`, `student_username`, `student_pass`, `student_email`) VALUES
('asdd', 'asdd', 'asdd', 'asdd', '$2y$10$/nzZ3iBQBCTFCqCmj6FxfuMNh/Dzu5b/.g.lxxtnhu4Y4wGWE8DCW', 'asdd@a'),
('cd24324', 'Asd', 'asd', 'asd', '$2y$10$sFYTM4zR/V5F8ayRaHxTW.JZY2.WNNzZRA7t/e2YACShw9p2cOsNG', 'asd@asd'),
('qwe', 'qwe', 'qwe', 'qwe', '$2y$10$jXesexALrDV.HKdmoL2hh.8ceU9lulyVO/ba.OPBY80v/hAm6h0cO', 'qwe@qwe');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advisor`
--
ALTER TABLE `advisor`
  ADD PRIMARY KEY (`advisor_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `advisor_id` (`advisor_id`);

--
-- Indexes for table `event_committee`
--
ALTER TABLE `event_committee`
  ADD PRIMARY KEY (`committee_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `event_qr_code`
--
ALTER TABLE `event_qr_code`
  ADD PRIMARY KEY (`qr_code_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `membership`
--
ALTER TABLE `membership`
  ADD PRIMARY KEY (`membership_id`);

--
-- Indexes for table `merit`
--
ALTER TABLE `merit`
  ADD PRIMARY KEY (`merit_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `advisor_id` (`advisor_id`);

--
-- Indexes for table `merit_application`
--
ALTER TABLE `merit_application`
  ADD PRIMARY KEY (`merit_application_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `advisor_id` (`advisor_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `merit_claim`
--
ALTER TABLE `merit_claim`
  ADD PRIMARY KEY (`claim_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `petakom_coordinator`
--
ALTER TABLE `petakom_coordinator`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`);

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`advisor_id`) REFERENCES `advisor` (`advisor_id`);

--
-- Constraints for table `event_committee`
--
ALTER TABLE `event_committee`
  ADD CONSTRAINT `event_committee_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`),
  ADD CONSTRAINT `event_committee_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);

--
-- Constraints for table `event_qr_code`
--
ALTER TABLE `event_qr_code`
  ADD CONSTRAINT `event_qr_code_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`);

--
-- Constraints for table `merit`
--
ALTER TABLE `merit`
  ADD CONSTRAINT `merit_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `merit_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`),
  ADD CONSTRAINT `merit_ibfk_3` FOREIGN KEY (`advisor_id`) REFERENCES `advisor` (`advisor_id`);

--
-- Constraints for table `merit_application`
--
ALTER TABLE `merit_application`
  ADD CONSTRAINT `merit_application_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`),
  ADD CONSTRAINT `merit_application_ibfk_2` FOREIGN KEY (`advisor_id`) REFERENCES `advisor` (`advisor_id`),
  ADD CONSTRAINT `merit_application_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `petakom_coordinator` (`staff_id`);

--
-- Constraints for table `merit_claim`
--
ALTER TABLE `merit_claim`
  ADD CONSTRAINT `merit_claim_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `merit_claim_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
