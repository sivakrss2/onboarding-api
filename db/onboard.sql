-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2020 at 02:25 PM
-- Server version: 10.1.19-MariaDB
-- PHP Version: 7.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `onboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `joinee_id` int(10) UNSIGNED NOT NULL,
  `activity` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_id` int(11) NOT NULL,
  `designation_id` int(11) NOT NULL,
  `date_of_birth` date NOT NULL,
  `date_of_join` date NOT NULL,
  `father_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cold_calling_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commitment_agreement` tinyint(1) NOT NULL,
  `joining_agreement` tinyint(1) NOT NULL,
  `recruiter_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requirement_details` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requirement_type` int(11) NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `requirement_lead_id` int(11) NOT NULL,
  `contact_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `candidate_accomodation` tinyint(1) DEFAULT NULL,
  `assigned_consultant_work` tinyint(1) DEFAULT '0',
  `consultant_lead_id` int(11) DEFAULT '0',
  `techinical_lead_id` int(11) DEFAULT '0',
  `buddy_coach_id` int(11) DEFAULT '0',
  `guid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `system_requirements` text COLLATE utf8mb4_unicode_ci,
  `onboarding` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidate_joinee_documents`
--

CREATE TABLE `candidate_joinee_documents` (
  `id` int(11) NOT NULL,
  `candidate_id` int(10) UNSIGNED NOT NULL,
  `open_time` date DEFAULT NULL,
  `close_time` date DEFAULT NULL,
  `contract` tinyint(1) NOT NULL,
  `joining_commitement` tinyint(1) NOT NULL,
  `salary_break_up` tinyint(1) NOT NULL,
  `joining_bonus` tinyint(1) NOT NULL,
  `back_up_lead` tinyint(1) NOT NULL,
  `contract_comment` text,
  `joining_commitement_comment` text,
  `salary_break_up_comment` text,
  `joining_bonus_comment` text,
  `back_up_lead_comment` text,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `candidate_joinee_documents_details`
--

CREATE TABLE `candidate_joinee_documents_details` (
  `id` int(11) NOT NULL,
  `candidate_joinee_document_id` int(11) NOT NULL,
  `file_mime` varchar(100) DEFAULT NULL,
  `file_data` longblob,
  `file_name` varchar(255) DEFAULT NULL,
  `type` tinyint(1) NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `candidate_resume`
--

CREATE TABLE `candidate_resume` (
  `id` int(10) UNSIGNED NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `resume_path` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `resume_name` text COLLATE utf8mb4_unicode_ci,
  `resume_data` longblob NOT NULL,
  `resume_mime` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidate_tasks`
--

CREATE TABLE `candidate_tasks` (
  `candidate_id` int(10) UNSIGNED NOT NULL,
  `task_details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lead_id` int(11) NOT NULL,
  `document_path` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cold_calling_status`
--

CREATE TABLE `cold_calling_status` (
  `id` int(10) UNSIGNED NOT NULL,
  `candidate_id` int(10) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `department` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department`, `description`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(9, 'HR', 'HR', 0, 0, NULL, NULL),
(10, 'Marketing', 'Marketing', 0, 0, NULL, NULL),
(11, 'Accounts', 'Accounts', 0, 0, NULL, NULL),
(12, 'Technical', 'Technical', 0, 0, NULL, NULL),
(13, 'Administraion', 'Administraion', 0, 0, NULL, NULL),
(14, 'HR', 'HR', 0, 0, NULL, NULL),
(15, 'Marketing', 'Marketing', 0, 0, NULL, NULL),
(16, 'Accounts', 'Accounts', 0, 0, NULL, NULL),
(17, 'Technical', 'Technical', 0, 0, NULL, NULL),
(18, 'Administraion', 'Administraion', 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` int(10) UNSIGNED NOT NULL,
  `designation_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`id`, `designation_name`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Junior software developer', 0, 0, NULL, NULL),
(2, 'Software developer', 0, 0, NULL, NULL),
(3, 'Associate group lead', 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_queues`
--

CREATE TABLE `email_queues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `from` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Comma Separated',
  `cc` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Comma Separated',
  `bcc` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Comma Separated',
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `template` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `template_details` text COLLATE utf8mb4_unicode_ci,
  `attachments` text COLLATE utf8mb4_unicode_ci COMMENT 'Comma Separated',
  `error` int(11) NOT NULL DEFAULT '0',
  `error_message` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` enum('1','2','3') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '1 => Notifications, 2=> Reminders, 3=> Wishes & One Year Completion',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 => Pending, 1=> Processed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fact_sheet`
--

CREATE TABLE `fact_sheet` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pos_applied` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phonenumber` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_address` text COLLATE utf8mb4_unicode_ci,
  `town` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `father_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_contact` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_occupation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mother_contact` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marital_status` int(11) DEFAULT NULL,
  `date_joining` date NOT NULL,
  `blood_group` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spouse_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spouse_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spouse_occupation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `religion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `edit_state` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fact_sheet`
--

INSERT INTO `fact_sheet` (`id`, `name`, `pos_applied`, `email`, `phonenumber`, `mobile`, `age`, `dob`, `address`, `permanent_address`, `town`, `state`, `father_name`, `father_contact`, `father_occupation`, `mother_name`, `mother_contact`, `marital_status`, `date_joining`, `blood_group`, `spouse_name`, `spouse_number`, `spouse_occupation`, `religion`, `edit_state`, `created_at`, `updated_at`, `updated_by`) VALUES
(9, 'test1', NULL, 'testemail5212@gmail.com', '8796543210', '6987543210', NULL, '1989-06-10', 'home address', 'address of permant', NULL, NULL, 'daddy', '547893210', NULL, 'mom', '7456893210', NULL, '1970-01-01', 'A+', 'wife', '9876543210', NULL, NULL, NULL, '2020-03-07 04:43:55', '2020-03-07 04:43:55', 4);

-- --------------------------------------------------------

--
-- Table structure for table `file_type`
--

CREATE TABLE `file_type` (
  `id` int(11) NOT NULL,
  `file_type_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `file_type`
--

INSERT INTO `file_type` (`id`, `file_type_name`) VALUES
(1, 'contractFile'),
(2, 'joiningCommitementFile'),
(3, 'salaryBreakUpFile');

-- --------------------------------------------------------

--
-- Table structure for table `joinee_child_info`
--

CREATE TABLE `joinee_child_info` (
  `id` int(11) NOT NULL,
  `joinee_id` int(11) NOT NULL,
  `child_name` varchar(50) NOT NULL,
  `child_gender` varchar(50) NOT NULL,
  `child_dob` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `joinee_documents`
--

CREATE TABLE `joinee_documents` (
  `id` int(11) NOT NULL,
  `guid` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_data` longblob,
  `file_mime` varchar(255) DEFAULT NULL,
  `type` int(11) DEFAULT NULL COMMENT '1-photo,2-aadhar,3-pancard,4-passport,5-offer,6-relieve,7-experience,8-form_16,9-payslip,10-salary_cert',
  `check_box` int(11) NOT NULL DEFAULT '0',
  `reason` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `joinee_document_details`
--

CREATE TABLE `joinee_document_details` (
  `id` int(11) NOT NULL,
  `guid` varchar(255) NOT NULL,
  `photo` int(11) NOT NULL DEFAULT '0',
  `aadhar_card` int(11) NOT NULL DEFAULT '0',
  `pan_card` int(11) NOT NULL DEFAULT '0',
  `passport` int(11) NOT NULL DEFAULT '0',
  `relieve_letter` int(11) NOT NULL DEFAULT '0',
  `offer_letter` int(11) NOT NULL DEFAULT '0',
  `experience_letter` int(11) NOT NULL DEFAULT '0',
  `form_16` int(11) NOT NULL DEFAULT '0',
  `pay_slips` int(11) NOT NULL DEFAULT '0',
  `salary_slip` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `joinee_personal_info`
--

CREATE TABLE `joinee_personal_info` (
  `joinee_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `alternate_number` varchar(15) DEFAULT NULL,
  `father_name` varchar(30) NOT NULL,
  `father_contact_number` varchar(15) DEFAULT NULL,
  `mother_name` varchar(30) NOT NULL,
  `mother_contact_number` varchar(15) DEFAULT NULL,
  `marital_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0- UnMarried, 1- Married',
  `spouse_name` varchar(30) DEFAULT NULL,
  `spouse_contact_number` varchar(15) DEFAULT NULL,
  `spouse_dob` date DEFAULT NULL,
  `child` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0- no, 1- yes',
  `present_address` text NOT NULL,
  `permanent_address` text NOT NULL,
  `date_of_birth` date NOT NULL,
  `date_of_join` date NOT NULL,
  `blood_group` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `guid` varchar(100) NOT NULL,
  `landmark` text,
  `uan_no` varchar(50) DEFAULT NULL,
  `is_link_disabled` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `joinee_personal_reference`
--

CREATE TABLE `joinee_personal_reference` (
  `refrence_id` int(11) NOT NULL,
  `guid` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `phone_no` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL,
  `relation` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `joinee_previous_company`
--

CREATE TABLE `joinee_previous_company` (
  `id` int(11) NOT NULL,
  `guid` varchar(100) NOT NULL,
  `hr_name` varchar(50) NOT NULL,
  `hr_designation` varchar(100) NOT NULL,
  `hr_phone_no` varchar(15) NOT NULL,
  `hr_email` varchar(100) NOT NULL,
  `ra_name` varchar(50) NOT NULL,
  `ra_designation` varchar(100) NOT NULL,
  `ra_phone_no` varchar(15) NOT NULL,
  `ra_email` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `joinee_professional_reference`
--

CREATE TABLE `joinee_professional_reference` (
  `refrence_id` int(11) NOT NULL,
  `guid` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `phone_no` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL,
  `relation` varchar(30) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int(10) UNSIGNED NOT NULL,
  `emp_id` int(15) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `emp_id`, `name`, `designation`, `email_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(11, 0, 'Edoardo', 'Lead 1', 'Gennaro.Quitzon@hotmail.com', 1, 1, '2020-02-21 04:07:47', '2020-02-21 04:07:47'),
(12, 0, 'Marcella', 'Lead 2', 'Ernest_Hermann85@yahoo.com', 1, 1, '2020-02-21 04:07:47', '2020-02-21 04:07:47'),
(13, 0, 'Elena', 'Lead 3', 'Delbert.Beier@hotmail.com', 1, 1, '2020-02-21 04:07:47', '2020-02-21 04:07:47'),
(14, 0, 'Elisa', 'Lead 4', 'Keaton12@yahoo.com', 1, 1, '2020-02-21 04:07:47', '2020-02-21 04:07:47'),
(15, 0, 'Romano', 'Lead 5', 'Corbin64@yahoo.com', 1, 1, '2020-02-21 04:07:47', '2020-02-21 04:07:47'),
(16, 3, 'Ravin Kumar J', 'GM-HR', 'ravin@cgvakindia.com', 1, 1, '2020-07-04 01:38:02', '2020-07-04 01:38:02'),
(17, 2, 'Antony', 'Project Manager', 'antony@cgvakindia.com', 1, 1, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(18, 72, 'Vaiyammal S', 'Associate Team Lead', 'vaiyammal@cgvakindia.com', 1, 1, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(19, 382, 'Dhanajeyan', 'Associate PM', 'dhanajeyan@cgvakindia.com', 1, 1, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(20, 398, 'Nagalingam', 'Project Manager', 'nagalingam@cgvakindia.com', 1, 1, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(21, 405, 'Jyothikumar', 'Project Manager', 'jyothikumar@cgvakindia.com', 1, 1, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(22, 422, 'Vidhyasagar C', 'Associate PL', 'vidhyasagar@cgvakindia.com', 1, 1, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(23, 439, 'Shanmugadoss', 'Group Leader', 'shanmugadoss@cgvakindia.com', 1, 1, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(24, 448, 'George', 'Associate Group Leader', 'georgethomas@cgvakindia.com', 1, 1, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(25, 450, 'Saravana Kumar N', 'Associate PL', 'saravanakumar@cgvakindia.com', 1, 1, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(26, 503, 'Nandini B', 'Team Lead', 'nicole@cgvakindia.com', 1, 1, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(27, 600, 'Vijayabaskar R', 'Project Leader', 'vijayabaskar@cgvakindia.com', 1, 1, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(28, 655, 'Dharmalingam', 'Associate Group Leader', 'dharmalingam@cgvakindia.com', 1, 1, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(29, 681, 'Preena', 'Group Leader', 'preena@cgvakindia.com', 1, 1, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(30, 711, 'Selwyn Shahil S', 'Group Leader', 'selwynshahil@cgvakindia.com', 1, 1, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(31, 740, 'Balakumar P', 'Associate Group Leader', 'balakumar@cgvakindia.com', 1, 1, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(32, 742, 'Balaji K S', 'Associate Group Leader', 'balajiks@cgvakindia.com', 1, 1, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(33, 750, 'Mrudhul Suresh', 'Senior PL', 'mrudhulsuresh@cgvakindia.com', 1, 1, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(34, 761, 'Shankar Ganesh M', 'Associate PL', 'shankarganesh@cgvakindia.com', 1, 1, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(35, 765, 'Hari Balaram R J', 'Associate PL', 'haribalaram@cgvakindia.com', 1, 1, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(36, 797, 'Eric Shajil D', 'Sr.IT Recruiter', 'eric@cgvakindia.com', 1, 1, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(37, 810, 'Pradeep Kumar K', 'Lead-Web Designer', 'Pradeep@cgvakindia.com', 1, 1, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(38, 815, 'Rajesh Kumar', 'Associate Team Lead', 'robert@cgvakindia.com', 1, 1, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(39, 816, 'Santhosh M', 'ITRecruiter', 'shannon@cgvakindia.com   ', 1, 1, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(40, 824, 'Vetrivel M', 'Group Leader', 'vetrivel@cgvakindia.com', 1, 1, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(41, 855, 'Saravanakumar S', 'Associate Team Lead', 'sanford@cgvakindia.com', 1, 1, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(42, 867, 'Ram Kumar S', 'Project Leader', 'ramkumar.s@cgvakindia.com', 1, 1, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(43, 894, 'Dhinesh Kumar S', 'Associate Group Leader', 'dhineshkumar@cgvakindia.com', 1, 1, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(44, 898, 'Selvaraj K', 'Project Leader', 'selvarajk@cgvakindia.com', 1, 1, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(45, 904, 'Kumaresan C', 'Associate PL', 'kumaresan@cgvakindia.com', 1, 1, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(46, 908, 'Ranjith K', 'Talent Acquisition Executive', 'ranjith@cgvakindia.com', 1, 1, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(47, 922, 'Prabakaran M', 'Associate Group Leader', 'prabakaran@cgvakindia.com', 1, 1, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(48, 931, 'Premalatha S', 'Project Manager', 'premalatha@cgvakindia.com', 1, 1, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(49, 935, 'Ranjith S', 'Associate PL', 'ranjith.s@cgvakindia.com', 1, 1, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(50, 941, 'Kannan G', 'Talent Acquisition Executive', 'kannan.g@cgvakindia.com', 1, 1, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(51, 942, 'Santhoon V', 'Group Leader', 'santhoon@cgvakindia.com', 1, 1, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(52, 970, 'Subash Starlin E', 'Associate Group Leader', 'subash@cgvakindia.com', 1, 1, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(53, 981, 'Jaganathan M', 'Technical Lead', 'jaganathan@cgvakindia.com', 1, 1, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(54, 988, 'Prakash T', 'Technical Lead', 'prakash.t@cgvakindia.com', 1, 1, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(55, 1018, 'Ramesh M', 'Associate PL', 'rameshmaran@cgvakindia.com', 1, 1, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(56, 1056, 'Palanikumar K', 'Lead-Digital Marketing', 'palanikumar@cgvakindia.com', 1, 1, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(57, 1060, 'Binu M', 'Associate Group Leader', 'binu@cgvakindia.com', 1, 1, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(58, 1064, 'Kalaivani G', 'Talent Acquisition Executive', 'kalaivani@cgvakindia.com', 1, 1, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(59, 1082, 'Sunitha James', 'Talent Acquisition Executive', 'sunitha@cgvakindia.com', 1, 1, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(60, 109, 'Parameswaran', 'Project Manager', 'parameswaran@cgvakindia.com', 1, 1, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(61, 1095, 'Sowmiya E', 'Talent Acquisition Executive', 'sowmiya@cgvakindia.com', 1, 1, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(62, 1131, 'Sathishkumar K', 'Technical Lead', 'sathishkumark@cgvakindia.com', 1, 1, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(63, 1132, 'Dinakaran Arumairaj', 'Technical Lead', 'dinakaran@cgvakindia.com', 1, 1, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(64, 1133, 'Vellingiri Arumugam', 'Lead Devops Engineer', 'vellingiri@cgvakindia.com', 1, 1, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(65, 255, 'Rajeesh', 'Project Leader', 'rajeesh@cgvakindia.com', 1, 1, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(66, 296, 'Manikandan J', 'Senior PL', 'manikandan.j@cgvakindia.com', 1, 1, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(67, 70, 'Lalitha', 'Senior PL', 'lalithah@cgvakindia.com', 1, 1, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(68, 73, '<b>GS</b>', 'CEO', 'suresh@cgvakindia.com', 1, 1, '2020-07-04 01:38:06', '2020-07-04 01:38:06');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_07_22_114135_create_departments_table', 1),
(4, '2019_07_26_092835_create_leads_table', 1),
(5, '2019_07_30_105647_create_designations_table', 1),
(6, '2019_08_17_054643_create_candidates_table', 1),
(8, '2019_08_17_084349_create_tasks_table', 1),
(9, '2019_08_17_094658_create_task_status_table', 1),
(10, '2019_08_17_143102_entrust_setup_tables', 1),
(11, '2019_08_30_171405_create_positions_table', 1),
(13, '2019_08_22_143311_create_factsheet_table', 2),
(14, '2019_08_22_150403_create_joinee_siblings_table', 2),
(15, '2019_08_22_150427_create_joinee_education_table', 2),
(16, '2019_08_22_150447_create_joinee_certification_table', 2),
(17, '2019_08_22_150530_create_joinee_software_rating_table', 2),
(18, '2019_08_22_150546_create_joinee_experience_table', 2),
(19, '2019_08_22_150618_create_joinee_remuneration_table', 2),
(20, '2019_08_22_150949_create_joinee_visa_details_table', 2),
(21, '2019_08_23_144546_create_passport_status_table', 2),
(22, '2019_08_29_123757_create_high_school_table', 2),
(23, '2019_08_30_162218_create_town_table', 2),
(24, '2019_08_30_162237_create_state_table', 2),
(25, '2019_08_30_165824_create_status_table', 2),
(26, '2019_08_30_170518_create_job_details_table', 2),
(27, '2019_09_25_103039_create_languages_table', 2),
(28, '2019_09_25_134731_create_activities_table', 2),
(30, '2020_02_21_094416_create_cold_calling_status', 3),
(32, '2020_02_21_104655_update_candidate_table_for_new_fields', 4),
(33, '2020_02_24_093651_update_candidate_add_new_fields_accomodation', 4),
(34, '2020_02_24_104221_create_requirement_type_table', 5),
(37, '2020_02_24_134022_update_cadidate_add_new_field', 6);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Adding Candidate', 'Add Candidate', 'Adding a new candidate into pre-onboarding.', NULL, NULL),
(2, 'Updating Candidate View', 'Update Candidate View', 'Viewing an existing candidate in the pre-onboarding.', NULL, NULL),
(3, 'Update Candidate', 'Updating a candidate.', 'Updating an existing candidate', NULL, NULL),
(4, 'Add Joinee Document', 'Add Joinee Document.', 'Adding Joinee Document.', NULL, NULL),
(5, 'Add Techinical Document', 'Add Techinical Document', 'Adding Techinical Document.', NULL, NULL),
(6, 'Joinee Document View', 'Joinee Document View', 'View the joinee document files.', NULL, NULL),
(7, 'Techinical Document View', 'Techinical Document View', 'View the Techinical document files.', NULL, NULL),
(8, 'Assign Techinical Lead', 'Assign Techinical Lead', 'Assign Techinical Lead and Buddy coach.', NULL, NULL),
(9, 'Add Techinical Task', 'Add Techinical Task', 'Adding Techinical Task description.', NULL, NULL),
(10, 'Add Synergy Details', 'Add Synergy Details', 'Add Synergy Details', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`id`, `permission_id`, `role_id`) VALUES
(1, 1, 2),
(6, 3, 2),
(7, 3, 4),
(8, 3, 5),
(9, 4, 4),
(10, 5, 3),
(11, 5, 4),
(12, 6, 2),
(14, 7, 2),
(15, 7, 5),
(16, 8, 5),
(17, 10, 4),
(18, 2, 6),
(22, 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `proficiency_rating`
--

CREATE TABLE `proficiency_rating` (
  `id` int(10) UNSIGNED NOT NULL,
  `rating_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remuneration`
--

CREATE TABLE `remuneration` (
  `joinee_id` int(10) UNSIGNED NOT NULL,
  `take_home_sal` decimal(12,2) DEFAULT NULL,
  `deductions` text COLLATE utf8mb4_unicode_ci,
  `monthly_ctc` decimal(12,2) DEFAULT NULL,
  `yearly_ctc` decimal(12,2) DEFAULT NULL,
  `others` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requirement_type`
--

CREATE TABLE `requirement_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requirement_type`
--

INSERT INTO `requirement_type` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Internal', '2020-02-24 05:14:38', '2020-02-24 05:14:38'),
(2, 'Buffer', '2020-02-24 05:14:38', '2020-02-24 05:14:38'),
(3, 'Replacement', '2020-02-24 05:14:38', '2020-02-24 05:14:38'),
(4, 'Client', '2020-02-24 05:14:38', '2020-02-24 05:14:38');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Admin', 'Admin Description', '2019-10-19 04:08:39', '2019-11-02 00:08:34'),
(2, 'Recruiter', 'Recruiter', 'Recruiter Description', '2019-11-02 00:20:05', '2020-07-23 09:21:54'),
(3, 'Techinical Lead', 'Techinical Lead', 'Tehinical Lead Description', '2020-04-14 02:35:58', '2020-07-23 11:58:46'),
(4, 'Generalist', 'Generalist', 'Generalist Description.', NULL, '2020-07-23 09:13:23'),
(5, 'Unit Head', 'Unit Head', 'Unit Head Description.', NULL, '2020-07-23 09:23:21'),
(6, 'System Admin', NULL, NULL, '2020-07-23 08:40:12', '2020-07-23 08:40:12');

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`id`, `user_id`, `role_id`) VALUES
(2, 5, 2),
(5, 14, 3),
(12, 6, 5),
(18, 66, 4),
(21, 4, 1),
(32, 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` int(10) UNSIGNED NOT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `state`) VALUES
(1, 'Andaman and Nicobar Islands'),
(2, 'Andra Pradesh'),
(3, 'Arunachal Pradesh'),
(4, 'Assam'),
(5, 'Bihar'),
(6, 'Chandigarh'),
(7, 'Chhattisgarh'),
(8, 'Dadar and Nagar Haveli'),
(9, 'Daman and Diu'),
(10, 'Delhi'),
(11, 'Goa'),
(12, 'Gujarat'),
(13, 'Haryana'),
(14, 'Himachal Pradesh'),
(15, 'Jammu and Kashmir'),
(16, 'Jharkhand'),
(17, 'Karnataka'),
(18, 'Kerala'),
(19, 'Lakshadeep'),
(20, 'Madya Pradesh'),
(21, 'Maharashtra'),
(22, 'Manipur'),
(23, 'Meghalaya'),
(24, 'Mizoram'),
(25, 'Nagaland'),
(26, 'Orissa'),
(27, 'Punjab'),
(28, 'Pondicherry'),
(29, 'Rajasthan'),
(30, 'Sikkim'),
(31, 'Tamil Nadu'),
(32, 'Telagana'),
(33, 'Tripura'),
(34, 'Uttaranchal'),
(35, 'Uttar Pradesh'),
(36, 'West Bengal');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(10) UNSIGNED NOT NULL,
  `status_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `status_name`) VALUES
(1, 'Yes'),
(2, 'No');

-- --------------------------------------------------------

--
-- Table structure for table `task_status`
--

CREATE TABLE `task_status` (
  `id` int(10) UNSIGNED NOT NULL,
  `status_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `techinical_task`
--

CREATE TABLE `techinical_task` (
  `id` int(11) NOT NULL,
  `candidate_id` int(10) UNSIGNED NOT NULL,
  `task_id` enum('0','1') NOT NULL COMMENT '0- Consultant, 1- Training',
  `client_name` text,
  `task_assigned` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `techinical_task_details`
--

CREATE TABLE `techinical_task_details` (
  `id` int(11) NOT NULL,
  `techinical_task_id` int(11) NOT NULL,
  `task_detail` text NOT NULL,
  `task_start_date` date NOT NULL,
  `task_end_date` date NOT NULL,
  `task_status` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `towns`
--

CREATE TABLE `towns` (
  `id` int(10) UNSIGNED NOT NULL,
  `town` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `towns`
--

INSERT INTO `towns` (`id`, `town`) VALUES
(1, 'Agartala'),
(2, 'Agra'),
(3, 'Ahmedabad'),
(4, 'Aizwal'),
(5, 'Ajmer'),
(6, 'Allahabad'),
(7, 'Alleppey'),
(8, 'Alibaug'),
(9, 'Almora');

-- --------------------------------------------------------

--
-- Table structure for table `unit_head`
--

CREATE TABLE `unit_head` (
  `id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `destination` varchar(191) NOT NULL,
  `email_id` varchar(191) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `unit_head`
--

INSERT INTO `unit_head` (`id`, `name`, `destination`, `email_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Nagalingam', 'Unit Head', 'nagalingam@cgvakindia.com', 0, 0, NULL, NULL),
(2, 'Parameswaran Subbaiyan', 'Unit Head', 'Parameswaran@cgvakindia.com', 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'System Admin', 'SA', 'systemadmin@gmail.com', '$2y$10$.hMvNSTKBbi5gwZ9stOiR.NatOoikzUjcLJEG73V0b01jsAoAzYrK', NULL, NULL, NULL),
(2, 'Prabakaran M', 'prabakaranm', 'prabakaran@cgvakindia.com', '$2y$10$4wb7aLDxUbJFok77UL0h/Of7Jt73F7agsEB4qix4om4K9GxFOkiyW', NULL, '2019-10-19 06:36:34', '2020-07-04 01:38:05'),
(3, 'Ranjith  S', 'ranjiths', 'ranjith.s@cgvakindia.com', '$2y$10$NPmX0L.beiatD29LuCvSYeHVfMuvyusGmsVarlPp.YngBzWz.8h/a', NULL, '2019-10-19 06:41:51', '2020-07-04 01:38:05'),
(4, 'Siva Chandru', 'sivachandruk', 'sivachandru@cgvakindia.com', '$2y$10$.hMvNSTKBbi5gwZ9stOiR.NatOoikzUjcLJEG73V0b01jsAoAzYrK', NULL, NULL, NULL),
(5, 'vidya', 'vidya', 'vidya@cgvakindia.com', '$2y$10$.hMvNSTKBbi5gwZ9stOiR.NatOoikzUjcLJEG73V0b01jsAoAzYrK', NULL, NULL, NULL),
(6, 'Sam', 'Sam', 'sam@gmail.com', '$2y$10$.hMvNSTKBbi5gwZ9stOiR.NatOoikzUjcLJEG73V0b01jsAoAzYrK', NULL, NULL, NULL),
(14, 'Elisa', 'Elisa', 'elisa@gmail.com', '$2y$10$.hMvNSTKBbi5gwZ9stOiR.NatOoikzUjcLJEG73V0b01jsAoAzYrK', NULL, NULL, NULL),
(15, 'Ravin Kumar J', 'ravinhr', 'ravin@cgvakindia.com', '$2y$10$uG.mmoGnXXU7fmfKorvyJ.TzVH7BQPDi70Ksf2tv.kGaabYb3i1V.', NULL, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(16, 'Antony D', 'antony', 'antony@cgvakindia.com', '$2y$10$LYHma6K15k12WwvjV1p9eeUTbQSq7n4Qv2UKmlmBvRNgOZqKyFUCO', NULL, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(17, 'Vaiyammal S', 'vaiyammal', 'vaiyammal@cgvakindia.com', '$2y$10$HNbEkBmCL40iAjN279vWWOoiYDI3zzKnmOK6kdyRv0nmTF0NpI0Tu', NULL, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(18, 'Dhanajeyan Manohar', 'Dhanajeyan', 'dhanajeyan@cgvakindia.com', '$2y$10$EMFzu6sh.Tygn36WPi6s2e4UtSKgthwk5hhncj.1s06Zvd3bufsqq', NULL, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(19, 'Nagalingam V', 'nagalingam', 'nagalingam@cgvakindia.com', '$2y$10$7EMEkSbJQZTkQu8wmmyxye1.m.TiXJH0OdSrZDatFvc/oBLMPyF.e', NULL, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(20, 'Jyothi Kumar K', 'jyothi', 'jyothikumar@cgvakindia.com', '$2y$10$9MkasXfenNij9MH9sSAgFO7P0QajQdWnXpanyah8gAeKO.2bbmuba', NULL, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(21, 'Vidhyasagar C', 'vsagar.c', 'vidhyasagar@cgvakindia.com', '$2y$10$ZOmXli3Qu4NEn7YF/v8c6uCV/Je3t0cJbIV5hLOtaewM3n4fegmUa', NULL, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(22, 'Shanmugadoss A', 'shan.doss', 'shanmugadoss@cgvakindia.com', '$2y$10$m3wuH9Ll9V2T9YUY9posQuwqsmpIJ77J7cGGMOqzw4mNiRw/VMPpS', NULL, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(23, 'George Thomas T T', 'george', 'georgethomas@cgvakindia.com', '$2y$10$aMgdAUGBY5db8k3i/ifaROsZKb.di6EyKjLz3YM8nNaV45DrtmpqO', NULL, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(24, 'Saravana Kumar N', 'saravana.n', 'saravanakumar@cgvakindia.com', '$2y$10$9xMx/fvpHRSXHfRLPgWkt.4hrwG3UgxFWCSn5zgJJMXVMe67LO9Zm', NULL, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(25, 'Nandini Bhattacharjee', 'nandini', 'nicole@cgvakindia.com', '$2y$10$gpyL/hZCPvEbh8yiH6qi8ePQQYNdw9ZWyXxvlXhnlBKGsK2IgPFF2', NULL, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(26, 'Vijaya Baskar', 'vijayabaskar', 'vijayabaskar@cgvakindia.com', '$2y$10$Mo5CRz1IDK/hu7qkZ12ube5cSNcEMBOZLcvIru5aAOMsYfnhrE/pC', NULL, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(27, 'Dharmalingam  P', 'dharmalingam', 'dharmalingam@cgvakindia.com', '$2y$10$Eoe.ctTLPwW3szUIdn4j1eXbrsGQQegEg.Jto7kVicbihtzshUyxK', NULL, '2020-07-04 01:38:03', '2020-07-04 01:38:03'),
(28, 'James Thadeus  Virgin Preena', 'preena', 'preena@cgvakindia.com', '$2y$10$9EUveAdRfu3caf9gzTnMFelzUKXeNi.8Y0AUQgM60syoArI.hcFnC', NULL, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(29, 'Selwyn Shahil S', 'selwynshahil', 'selwynshahil@cgvakindia.com', '$2y$10$ORCXwV6niRqP5TdK1MzV6O9fEGW93Fe9FZPHwhCiIGdt09ShN0XLq', NULL, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(30, 'Balakumar P', 'balap', 'balakumar@cgvakindia.com', '$2y$10$0.ZpS0NX5ria0Q8DkRvUVuuvLMtwvoyu.ZRdbQ6XwTzRa5xmuACYS', NULL, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(31, 'Balaji K S', 'balajiks', 'balajiks@cgvakindia.com', '$2y$10$gUtHGJzaFZHCCOgQ4Ycz7eoJ6gkGxpo.qZaoK3aC9JEG8Pvaqrus.', NULL, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(32, 'Mrudhul Suresh', 'mrudhul', 'mrudhulsuresh@cgvakindia.com', '$2y$10$6fzlleFoKQwXSZ70Dztlk.uAMa301BCUv0qP4QNW.OXgNod1Z.xI6', NULL, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(33, 'Shankar Ganesh M', 'shankarm', 'shankarganesh@cgvakindia.com', '$2y$10$IJekFUIhW2JPAXhZGAdFouY7EbRS9Ekyn1I39//gD7fEm1McbbGBe', NULL, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(34, 'Hari Balaram R J', 'haribalaramrj', 'haribalaram@cgvakindia.com', '$2y$10$5MG4tsdoDKTLkAQTxwixS.3Yeq9OpKYL1pxbVoaLbZDfOLMPkBwzq', NULL, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(35, 'Eric Shajil  D', 'ericd', 'eric@cgvakindia.com', '$2y$10$X4CFbJtBmLxEqTymnwimL.8o7afuOXwGLzIkL.Lvc7zMyMc5I/pJi', NULL, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(36, 'Pradeep  Kumar K', 'pradeepk', 'Pradeep@cgvakindia.com', '$2y$10$Z67HTFOCKE4.llZpOW3i3uZg5NFNgXv4u2qpipdwWe3KwjVhH6OYK', NULL, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(37, 'Rajesh Kumar K', 'rajeshk', 'robert@cgvakindia.com', '$2y$10$vweMRhhyfU4SyYopmnfkce/deOTrJcdD/CnHT.4iT3QhUUuXfFA6e', NULL, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(38, 'Santhosh M', 'santhoshm', 'shannon@cgvakindia.com   ', '$2y$10$tvqnPwtWFRdde83L0MAwwu2tD5Vlc/xwBbyfEHxbSJL2ak/0gPIJm', NULL, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(39, 'Vetrivel M', 'vetrivelm', 'vetrivel@cgvakindia.com', '$2y$10$tXoMFJ9ssATJnajziFbsienMg4Mxkpeogb2NmK745CSuXuR5X7i1O', NULL, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(40, 'Saravanakumar S', 'saravanakumars', 'sanford@cgvakindia.com', '$2y$10$FXvnugIikle6jS6jgbkUueoXtOuOTmrgz5RVF.76qdxNTZfgVreJ.', NULL, '2020-07-04 01:38:04', '2020-07-04 01:38:04'),
(41, 'Ram Kumar S', 'ramkumar', 'ramkumar.s@cgvakindia.com', '$2y$10$Xl4ysjj2sKnJSq1c0HoZXuXwfVJx/N4Kh2t1cAEcIl/k8lN.VvfsO', NULL, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(42, 'Dhinesh Kumar S', 'dhineshkumars', 'dhineshkumar@cgvakindia.com', '$2y$10$VRCG4Lq3cgjEWhofED5.fuNs1RN4Rr03ApUYRnrugkOkjV/TtSS0e', NULL, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(43, 'Selvaraj K', 'selvarajk', 'selvarajk@cgvakindia.com', '$2y$10$0QjzmW2MY4aIdvDeSudhvOoDkzY7jwqucNDyaGXFQ9XVpQyUxL/aW', NULL, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(44, 'Kumaresan C', 'kumaresanc', 'kumaresan@cgvakindia.com', '$2y$10$ZRMmYtXKxh/Qd028G6sM2eW/M8hIRaB72CmQXlhjGhuC6eaa68foK', NULL, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(45, 'Ranjith K', 'ranjithk', 'ranjith@cgvakindia.com', '$2y$10$mCOxnHVw0QQ8gE9WWa1HQuF41UnOtl1XvVPnqQQr.X9VVecr9h5Ke', NULL, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(46, 'Premalatha  S', 'premalathas', 'premalatha@cgvakindia.com', '$2y$10$lEx.65YHfwYk1NO.JB/BTuBejbENGZlb7cGbIEpm85lsJPo9lEqam', NULL, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(47, 'Kannan G', 'kannang', 'kannan.g@cgvakindia.com', '$2y$10$42eGcYiqbQCsanaE9/hnJeioVHqiXJHff6Kx7kJRUoUZA6zzDdzii', NULL, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(48, 'Santhoon  V', 'santhoonv', 'santhoon@cgvakindia.com', '$2y$10$EQfvRqPQHkpahUwKinFSWOAjrk7.JFzen3yNwtHySQNdLW2VH8TmO', NULL, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(49, 'Subash Starlin E', 'subashstarlin', 'subash@cgvakindia.com', '$2y$10$JJviXNRw57XM3Sb9VfDvD.Txjv8SZa7MgwJGi/fEJj30YN3ZUMer2', NULL, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(50, 'Jaganathan M', 'jaganathanm', 'jaganathan@cgvakindia.com', '$2y$10$FHfrzJG4ijJAr.jQKiVM.efZg6lhfsw3oIFQD/s/vjumwunnV/Nf6', NULL, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(51, 'Prakash T', 'prakashthangarasu', 'prakash.t@cgvakindia.com', '$2y$10$X8OfmpGIi74HadejdEIhj.T4dQ9dY0Wbb7HU6Vt23f258qr.wPQ5K', NULL, '2020-07-04 01:38:05', '2020-07-04 01:38:05'),
(52, 'Ramesh Maran', 'rameshmaran', 'rameshmaran@cgvakindia.com', '$2y$10$xn3lJDpTulSe6o/WN4wk4OuI2myjF49MsyPpeYMXUwS.YHNjvLPSe', NULL, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(53, 'Palanikumar K', 'palanikumar', 'palanikumar@cgvakindia.com', '$2y$10$oGTmWuITjTKQly2bTLPuoO.i1H51jXgL9oUQrIpz7MyCwNdj5nHe6', NULL, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(54, 'Binu M', 'binum', 'binu@cgvakindia.com', '$2y$10$tkKWdWA05oLsztZ5RAciFeunmoZgJ4p9QoRK5ayPkBu0mKT.Xrl8u', NULL, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(55, 'Kalaivani G', 'kalaivanig', 'kalaivani@cgvakindia.com', '$2y$10$4ngs.m9Id6yQ4yP.fROpbOsj5uD5sHXbZOElZ9ZEWoLCq5luyPlHq', NULL, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(56, 'Sunitha James', 'sunithajames', 'sunitha@cgvakindia.com', '$2y$10$3JzONf.5d87ZRLUpGal3/Ovc18aYk.KUlPKHJdot0KN8jf4Wh7Vwm', NULL, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(57, 'Parameswaran S', 'param', 'parameswaran@cgvakindia.com', '$2y$10$rCC9ModWC84L0/PTIaPISeUhGRNwEWEyDelS08j.wv0SkOW6vmWAm', NULL, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(58, 'Sowmiya E', 'sowmiya', 'sowmiya@cgvakindia.com', '$2y$10$qWYaUfiDWfWL0HsVfZtEV.escqzPQHFgB.tX.uUUjhxOLCTKyxWQO', NULL, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(59, 'Sathishkumar K', 'sathishkumar', 'sathishkumark@cgvakindia.com', '$2y$10$et4e96k1zGEi5IsH9q8ZZeZyhSYi35ZDUQkEgnnJcN7eB1enEfbhe', NULL, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(60, 'Dinakaran  Arumairaj', 'dinakaran', 'dinakaran@cgvakindia.com', '$2y$10$spjp8ZN1f3eNDi5E6ll5XeoJvWynSk.Kg.NrRKl9XZsEFiic4fFW.', NULL, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(61, 'Vellingiri Arumugam', 'vellingiri', 'vellingiri@cgvakindia.com', '$2y$10$P2XyrfjJ4QZmI3MFkK9Zzu.V6pVUELBA6uSckPgLW.fOfqtv/tfqG', NULL, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(62, 'Rajeesh K', 'rajeesh', 'rajeesh@cgvakindia.com', '$2y$10$aG2Y1oFVCU5BX3qKrFGbQuNrzNTzSRAGXzPgfntoABB.EvutKZmnW', NULL, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(63, 'Manikandan J', 'Manij', 'manikandan.j@cgvakindia.com', '$2y$10$iblZy.57DwS3WBjGEsWBCOeLdvx1Q88Yxk2RjHqsVGjzLDBIjA79.', NULL, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(64, 'Lalitha Mahesh', 'Lalitha', 'lalithah@cgvakindia.com', '$2y$10$9TSEbbWHuMPCIotMcAC7.eKLEetN8D7wPYmt1XEQ/zEy7z3Mgiv0y', NULL, '2020-07-04 01:38:06', '2020-07-04 01:38:06'),
(65, 'Suresh (GS) G', 'suresh', 'suresh@cgvakindia.com', '$2y$10$un/N//OXT/0Zdhp2/PXgiuzUpvKA4WdYmkawrhAHLU3Ud6GsCYydS', NULL, '2020-07-04 01:38:07', '2020-07-04 01:38:07'),
(66, 'Generailst', 'Generailst', 'genarailist@gmail.com', '$2y$10$.hMvNSTKBbi5gwZ9stOiR.NatOoikzUjcLJEG73V0b01jsAoAzYrK', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `visa_details`
--

CREATE TABLE `visa_details` (
  `joinee_id` int(10) UNSIGNED NOT NULL,
  `visa_applied` int(11) DEFAULT NULL,
  `reject_reason` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD KEY `activities_joinee_id_foreign` (`joinee_id`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `candidates_email_unique` (`email`),
  ADD UNIQUE KEY `guid` (`guid`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `designation_id` (`designation_id`);

--
-- Indexes for table `candidate_joinee_documents`
--
ALTER TABLE `candidate_joinee_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- Indexes for table `candidate_joinee_documents_details`
--
ALTER TABLE `candidate_joinee_documents_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_joinee_document_id` (`candidate_joinee_document_id`);

--
-- Indexes for table `candidate_resume`
--
ALTER TABLE `candidate_resume`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `candidate_tasks`
--
ALTER TABLE `candidate_tasks`
  ADD KEY `candidate_tasks_candidate_id_foreign` (`candidate_id`);

--
-- Indexes for table `cold_calling_status`
--
ALTER TABLE `cold_calling_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cold_calling_status_candidate_id_foreign` (`candidate_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_queues`
--
ALTER TABLE `email_queues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_queues_priority_status_index` (`priority`,`status`);

--
-- Indexes for table `fact_sheet`
--
ALTER TABLE `fact_sheet`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fact_sheet_email_unique` (`email`),
  ADD UNIQUE KEY `fact_sheet_mobile_unique` (`mobile`),
  ADD UNIQUE KEY `fact_sheet_phonenumber_unique` (`phonenumber`);

--
-- Indexes for table `file_type`
--
ALTER TABLE `file_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `joinee_child_info`
--
ALTER TABLE `joinee_child_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `joinee_id` (`joinee_id`);

--
-- Indexes for table `joinee_documents`
--
ALTER TABLE `joinee_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `joinee_document_details`
--
ALTER TABLE `joinee_document_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `guid` (`guid`);

--
-- Indexes for table `joinee_personal_info`
--
ALTER TABLE `joinee_personal_info`
  ADD PRIMARY KEY (`joinee_id`);

--
-- Indexes for table `joinee_personal_reference`
--
ALTER TABLE `joinee_personal_reference`
  ADD PRIMARY KEY (`refrence_id`),
  ADD KEY `guid` (`guid`);

--
-- Indexes for table `joinee_previous_company`
--
ALTER TABLE `joinee_previous_company`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guid` (`guid`);

--
-- Indexes for table `joinee_professional_reference`
--
ALTER TABLE `joinee_professional_reference`
  ADD PRIMARY KEY (`refrence_id`),
  ADD KEY `guid` (`guid`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `proficiency_rating`
--
ALTER TABLE `proficiency_rating`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `remuneration`
--
ALTER TABLE `remuneration`
  ADD KEY `remuneration_joinee_id_index` (`joinee_id`);

--
-- Indexes for table `requirement_type`
--
ALTER TABLE `requirement_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_status`
--
ALTER TABLE `task_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `techinical_task`
--
ALTER TABLE `techinical_task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- Indexes for table `techinical_task_details`
--
ALTER TABLE `techinical_task_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `techinical_task_id` (`techinical_task_id`);

--
-- Indexes for table `towns`
--
ALTER TABLE `towns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `unit_head`
--
ALTER TABLE `unit_head`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_name_unique` (`name`);

--
-- Indexes for table `visa_details`
--
ALTER TABLE `visa_details`
  ADD KEY `visa_details_joinee_id_foreign` (`joinee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `candidate_joinee_documents`
--
ALTER TABLE `candidate_joinee_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `candidate_joinee_documents_details`
--
ALTER TABLE `candidate_joinee_documents_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `candidate_resume`
--
ALTER TABLE `candidate_resume`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cold_calling_status`
--
ALTER TABLE `cold_calling_status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `email_queues`
--
ALTER TABLE `email_queues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `fact_sheet`
--
ALTER TABLE `fact_sheet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `file_type`
--
ALTER TABLE `file_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `joinee_child_info`
--
ALTER TABLE `joinee_child_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `joinee_documents`
--
ALTER TABLE `joinee_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `joinee_document_details`
--
ALTER TABLE `joinee_document_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `joinee_personal_info`
--
ALTER TABLE `joinee_personal_info`
  MODIFY `joinee_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `joinee_personal_reference`
--
ALTER TABLE `joinee_personal_reference`
  MODIFY `refrence_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `joinee_previous_company`
--
ALTER TABLE `joinee_previous_company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `joinee_professional_reference`
--
ALTER TABLE `joinee_professional_reference`
  MODIFY `refrence_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `permission_role`
--
ALTER TABLE `permission_role`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `proficiency_rating`
--
ALTER TABLE `proficiency_rating`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `requirement_type`
--
ALTER TABLE `requirement_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `role_user`
--
ALTER TABLE `role_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `task_status`
--
ALTER TABLE `task_status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `techinical_task`
--
ALTER TABLE `techinical_task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;
--
-- AUTO_INCREMENT for table `techinical_task_details`
--
ALTER TABLE `techinical_task_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `towns`
--
ALTER TABLE `towns`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `unit_head`
--
ALTER TABLE `unit_head`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_joinee_id_foreign` FOREIGN KEY (`joinee_id`) REFERENCES `fact_sheet` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `candidate_joinee_documents`
--
ALTER TABLE `candidate_joinee_documents`
  ADD CONSTRAINT `candidate_joinee_documents_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`);

--
-- Constraints for table `candidate_tasks`
--
ALTER TABLE `candidate_tasks`
  ADD CONSTRAINT `candidate_tasks_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cold_calling_status`
--
ALTER TABLE `cold_calling_status`
  ADD CONSTRAINT `cold_calling_status_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`);

--
-- Constraints for table `joinee_child_info`
--
ALTER TABLE `joinee_child_info`
  ADD CONSTRAINT `joinee_child_info_ibfk_1` FOREIGN KEY (`joinee_id`) REFERENCES `joinee_personal_info` (`joinee_id`);

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `remuneration`
--
ALTER TABLE `remuneration`
  ADD CONSTRAINT `remuneration_joinee_id_foreign` FOREIGN KEY (`joinee_id`) REFERENCES `fact_sheet` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
