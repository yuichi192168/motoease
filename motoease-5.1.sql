-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Oct 14, 2025 at 07:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

-- create database if0_40141531_motoease_6;
use if0_40141531_motoease_6;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bpsms_db`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `abc_analysis_view`
-- (See below for the actual view)
--
CREATE TABLE `abc_analysis_view` (
`id` int(30)
,`name` text
,`abc_category` enum('A','B','C')
,`price` float
,`reorder_point` int(11)
,`max_stock` int(11)
,`min_stock` int(11)
,`current_stock` double
,`total_ordered` double
,`available_stock` double
,`stock_status` varchar(9)
);

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `service_type` int(11) NOT NULL,
  `mechanic_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `vehicle_info` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `client_id`, `service_type`, `mechanic_id`, `appointment_date`, `appointment_time`, `vehicle_info`, `notes`, `status`, `date_created`, `date_updated`) VALUES
(1, 8, 7, 9, '2025-10-11', '14:30:00', 'Click 125', '', 'pending', '2025-10-10 13:26:51', NULL),
(2, 8, 137, 10, '2025-10-15', '10:30:00', 'Type: Scooter; Name: Click; Registration: ABC123; Model: Honda', 'pa fix yah', 'pending', '2025-10-10 16:20:11', NULL),
(3, 8, 99, 8, '2025-10-16', '13:30:00', 'Click 125', '', 'pending', '2025-10-11 07:24:28', NULL),
(4, 8, 70, 8, '2025-10-15', '09:30:00', '', '', 'pending', '2025-10-11 14:28:33', NULL),
(5, 2, 199, 9, '2025-10-17', '15:00:00', 'click 125 sira ang wirings', '', 'pending', '2025-10-13 16:19:10', NULL);

-- --------------------------------------------------------
--
-- Table structure for table `cart_list`
--



-- Table structure for table `branches`


CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `address` text NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table structure for table `brand_list`

CREATE TABLE `brand_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `image_path` text NOT NULL,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `cart_list` (
  `id` int(30) NOT NULL,
  `client_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `quantity` float NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_list`
--

INSERT INTO `cart_list` (`id`, `client_id`, `product_id`, `color`, `quantity`, `date_added`) VALUES
(66, 8, 36, 'Red', 1, '2025-10-10 17:55:04'),
(67, 8, 34, 'Red Black', 1, '2025-10-11 06:24:02'),
(68, 8, 40, 'Khaki', 1, '2025-10-11 06:33:07'),
(69, 8, 49, 'Black', 1, '2025-10-11 06:39:41'),
(70, 8, 51, 'Red Black', 1, '2025-10-11 06:41:30'),
(71, 8, 39, 'Gray', 1, '2025-10-11 06:48:26'),
(72, 8, 43, 'Black', 1, '2025-10-11 07:31:18'),
(73, 8, 41, 'Black Blue', 1, '2025-10-11 08:02:23'),
(74, 8, 45, NULL, 3, '2025-10-11 09:00:22'),
(75, 8, 33, NULL, 1, '2025-10-11 09:00:40'),
(76, 8, 51, NULL, 1, '2025-10-11 09:10:33'),
(0, 10, 47, NULL, 2, '2025-10-12 18:23:42'),
(81, 8, 37, 'Gray', 1, '2025-10-11 13:02:16'),
(82, 8, 36, 'Blue', 1, '2025-10-11 13:02:23'),
(83, 8, 35, 'Red', 1, '2025-10-11 13:13:37'),
(84, 8, 45, 'Black', 2, '2025-10-11 13:13:39'),
(85, 8, 36, NULL, 1, '2025-10-11 13:18:05'),
(86, 8, 52, NULL, 1, '2025-10-11 13:18:09'),
(87, 8, 40, NULL, 1, '2025-10-11 14:26:42'),
(88, 8, 35, NULL, 1, '2025-10-12 22:36:11'),
(89, 13, 51, 'Black', 1, '2025-10-13 10:52:35');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(30) NOT NULL,
  `category` varchar(250) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category`, `status`, `delete_flag`, `date_created`) VALUES
(10, 'Motorcycles', 1, 0, '2025-08-08 08:07:31'),
(13, 'Motorcycle Parts', 1, 0, '2025-08-08 08:10:40'),
(15, 'Oils', 1, 0, '2025-08-08 08:10:57');

-- --------------------------------------------------------

--
-- Table structure for table `client_list`
--

CREATE TABLE `client_list` (
  `id` int(30) NOT NULL,
  `firstname` text NOT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` text NOT NULL,
  `gender` text NOT NULL,
  `contact` text NOT NULL,
  `address` text NOT NULL,
  `email` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `password` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_added` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `credit_application_completed` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Whether customer has completed Motorcentral Credit Application',
  `login_attempts` int(11) DEFAULT 0,
  `is_locked` tinyint(4) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `account_balance` decimal(10,2) DEFAULT 0.00,
  `vehicle_plate_number` varchar(20) DEFAULT NULL,
  `or_cr_number` varchar(50) DEFAULT NULL,
  `or_cr_release_date` date DEFAULT NULL,
  `or_cr_status` enum('pending','released','expired') DEFAULT 'pending',
  `or_cr_file_path` text DEFAULT NULL,
  `vehicle_brand` varchar(100) DEFAULT NULL,
  `vehicle_model` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_list`
--

INSERT INTO `client_list` (`id`, `firstname`, `middlename`, `lastname`, `gender`, `contact`, `address`, `email`, `avatar`, `password`, `status`, `delete_flag`, `date_created`, `date_added`, `last_login`, `credit_application_completed`, `login_attempts`, `is_locked`, `locked_until`, `reset_token`, `reset_expires`, `account_balance`, `vehicle_plate_number`, `or_cr_number`, `or_cr_release_date`, `or_cr_status`, `or_cr_file_path`, `vehicle_brand`, `vehicle_model`) VALUES
(2, 'Aiah', '', 'Arceta', 'Female', '09123456789', 'Blk 72 Lot 7 Phase 6 Mabuhay Mamatid Cabuyao Laguna', 'aiah@gmail.com', 'uploads/1758764437_68d49d955b39c.jpg', '6b3251cd488029543402df97cbc20500', 1, 0, '2025-04-23 22:34:17', '2025-10-13 16:18:20', '2025-10-13 16:18:20', 0, 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(3, 'Jhoanna', '', 'Robles', 'Female', '0901262004', 'Blk 8 Lot 88, Mabuhay Mamatid, Cabuyao City, Laguna, 4025', 'jhoanna@gmail.com', 'uploads/1758764543_68d49dff84700.jpg', '6172961ee1eccc046bd3810138cc68ee', 1, 0, '2025-08-07 22:02:24', '2025-09-25 09:42:23', '2025-08-15 11:04:32', 0, 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(4, 'Aljay', '', 'Plantado', 'Male', '09282346158', 'Blk 72 Lot 7 Phase 6 Mabuhay Mamatid Cabuyao Laguna', 'aljaywew@gmail.com', 'uploads/1758764489_68d49dc9d421a.png', 'f0bd1fc09c2cfe760c342571f040eae7', 1, 0, '2025-08-13 13:01:27', '2025-09-25 10:14:09', NULL, 0, 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(5, 'Gojo', '', 'Satoru', 'Male', '09282347890', 'Blk 82 Lot 9 Phase 2 Mabuhay Mamatid Cabuyao Laguna', 'gojo@gmail.com', 'uploads/1758764594_68d49e329282a.png', '383fdfc40a9aff292f5827357acd5f53', 1, 1, '2025-08-13 22:24:02', '2025-10-10 09:22:30', NULL, 0, 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(6, 'Mary', '', 'Clara', 'Female', '09052720021', 'Blk 88 Lot 8 Mabuhay Mamatid Cabuyao Laguna', 'maryclara@gmail.com', 'uploads/1758764278_68d49cf6993cc.png', 'fe149d3eddaf84487c5687ee6832969d', 1, 0, '2025-08-15 10:43:41', '2025-10-11 09:43:15', '2025-10-11 09:43:15', 0, 0, 0, NULL, NULL, NULL, 0.00, 'ABC 123', 'OR-2025-001234', '2025-08-03', 'pending', NULL, NULL, NULL),
(7, 'Test', NULL, 'User', '', '', '', 'test@example.com', NULL, 'cc03e747a6afbbcbf8be7668acfebee5', 1, 1, '2025-08-15 11:02:55', '2025-08-15 12:44:16', '2025-08-15 11:04:21', 0, 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(8, 'Crisostomo', '', 'Vergara', 'Female', '09091320021', 'Blk 8 Lot 99 Mabuhay Mamatid Cabuyao Laguna', 'crisostomovergara@gmail.com', 'uploads/1758764032_68d49c0073d68.png', 'cfd1ca6b84fc6360c003e01842457ca6', 1, 0, '2025-08-15 15:17:48', '2025-10-14 11:27:03', '2025-10-14 11:27:03', 1, 0, 0, NULL, NULL, NULL, 0.00, '123ABC', '', NULL, 'pending', NULL, 'Honda', 'Click'),
(9, 'Stacey', '', 'Sevilleja', 'Female', '09070132003', 'BLK 8 Lot 88 Mabuhay Nueva Vizcaya, Philippines', 'stacey@gmail.com', 'uploads/1758766681_68d4a65987fd0.jpg', 'b9066808309e7b228d070046223bdf38', 1, 0, '2025-09-25 10:18:01', '2025-09-25 10:21:07', '2025-09-25 10:21:07', 0, 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(10, 'Sheena ', '', 'Catacutan', 'Female', '09050920040', 'Blk 88 Lot 8 Santiago, Isabela', 'sheena@gmail.com', 'uploads/1758766984_68d4a7883710a.jpg', '1861d297772b6e5e36339b54ebd5a65d', 1, 0, '2025-09-25 10:23:04', NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(11, 'Gwen', '', 'Apuli', 'Female', '09061920030', 'Blk 888 Lot 8 Albay Philippines', 'gwen@gmail.com', 'uploads/1758767086_68d4a7eec49c0.jpg', 'ac3b3a08a7941208a59fe263cac1bbc5', 1, 0, '2025-09-25 10:24:46', NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(12, 'Maloi', '', 'Ricalde', 'Female', '09052720020', 'Blk 88 Lot 8888 Lemery Batangas', 'maloi@gmail.com', 'uploads/1758767178_68d4a84ab044b.jpg', 'b35fb828fbc25ce215922fd412492aae', 1, 0, '2025-09-25 10:26:18', NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(13, 'Saint', '', 'Lucky', 'Female', '09282345681', 'BLk 8 Lot 88 Phase 8 Mabuhay Mamatid Cabuyao Laguna', 'saintlucky@gmail.com', 'uploads/1760323646_68ec683eaa8d0.jpeg', '7879ebdb42025673b18ab77990be74b3', 1, 0, '2025-10-13 10:47:26', '2025-10-13 10:47:32', '2025-10-13 10:47:32', 0, 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `credit_applications`
--

CREATE TABLE `credit_applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `birth_date` date NOT NULL,
  `address` text NOT NULL,
  `employment_status` varchar(50) NOT NULL,
  `monthly_income` decimal(10,2) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `loan_amount` decimal(10,2) NOT NULL,
  `loan_purpose` varchar(100) NOT NULL,
  `loan_purpose_other` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected','under_review') NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `customer_dashboard_view`
-- (See below for the actual view)
--
CREATE TABLE `customer_dashboard_view` (
`client_id` int(30)
,`firstname` text
,`lastname` text
,`email` text
,`account_balance` decimal(10,2)
,`total_orders` bigint(21)
,`total_services` bigint(21)
,`total_appointments` bigint(21)
,`total_documents` bigint(21)
,`unread_notifications` bigint(21)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_purchase_images`
--

CREATE TABLE `customer_purchase_images` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `motorcycle_model` varchar(255) NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `image_path` varchar(500) NOT NULL,
  `testimonial` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `date_created` datetime DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_purchase_images`
--

INSERT INTO `customer_purchase_images` (`id`, `customer_name`, `motorcycle_model`, `purchase_date`, `image_path`, `testimonial`, `is_active`, `display_order`, `date_created`, `date_updated`) VALUES
(3, 'Jerome Ponce', '', '2025-10-10', 'uploads/customers/1760062260_536983513_1236575031602514_854110027462307653_n.jpg', 'ganda ng motor solid', 1, 0, '2025-10-10 10:11:58', '2025-10-10 11:30:24'),
(23, 'Marxist Salayo', 'Click 125', '2025-10-02', 'uploads/customers/1760068080_543502258_1249633436963340_200488473921873217_n.jpg', 'pwede na', 1, 0, '2025-10-10 11:48:11', '2025-10-10 11:48:11'),
(24, 'Ryan Manahan', 'PCX 160', '2025-10-01', 'uploads/customers/1760068080_543508537_1249633440296673_6198699492900397526_n.jpg', 'angas', 1, 0, '2025-10-10 11:48:11', '2025-10-10 11:48:11'),
(25, 'Vin Mendoza', 'PCX 160', '2025-09-11', 'uploads/customers/1760068080_544637433_1249633496963334_4151327654010061011_n.jpg', 'sobrang ganda maraming available na colors colors', 1, 0, '2025-10-10 11:48:11', '2025-10-10 11:49:34'),
(26, 'Christoper Garcia', 'Click 125', '2025-09-12', 'uploads/customers/1760068080_547427725_1257680622825288_4607782442785715765_n.jpg', 'may pang rides na rin', 1, 0, '2025-10-10 11:48:11', '2025-10-10 11:48:11'),
(27, 'Jude Garcia', 'Click 125', '2025-10-10', 'uploads/customers/1760068080_547275280_1257680626158621_1972945420816125557_n.jpg', 'sobrang ganda yah anlaki ng susi', 1, 0, '2025-10-10 11:48:11', '2025-10-10 11:53:42'),
(28, 'Justin Niko', 'Click 125', '2025-09-28', 'uploads/customers/1760068080_558975647_1276567260936624_7766521788424806927_n.jpg', 'maganda', 1, 0, '2025-10-10 11:48:11', '2025-10-10 11:48:11'),
(29, 'Cris Umali', 'Click 125', '2025-10-10', 'uploads/customers/1760068080_559433439_1276567117603305_3281497148600143452_n.jpg', 'may pang chicks na', 1, 0, '2025-10-10 11:48:11', '2025-10-10 11:49:47'),
(30, 'Roy Valmonte', 'Click 125', '2025-08-06', 'uploads/customers/1760068080_557642259_1276567120936638_1346363164477649387_n.jpg', 'ayos ayos', 1, 0, '2025-10-10 11:48:11', '2025-10-10 11:48:11'),
(31, 'Alvin Viana', 'Winner X', '2025-09-30', 'uploads/customers/1760068080_557628224_1276567107603306_4255020553343436578_n.jpg', 'maganda naman', 1, 0, '2025-10-10 11:48:11', '2025-10-10 11:53:35');

-- --------------------------------------------------------

--
-- Table structure for table `customer_transactions`
--

CREATE TABLE `customer_transactions` (
  `id` int(11) NOT NULL,
  `client_id` int(30) NOT NULL,
  `transaction_type` enum('payment','refund','adjustment','order_payment') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `reference_id` varchar(50) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_transactions`
--

INSERT INTO `customer_transactions` (`id`, `client_id`, `transaction_type`, `amount`, `description`, `reference_id`, `date_created`) VALUES
(4, 2, '', 100000.00, 'Balance adjustment: too much balance', 'ADJ-20250815-F3D85C', '2025-08-15 15:44:16'),
(5, 2, '', 98899999.99, 'Balance adjustment: too much', 'ADJ-20250815-731C4A', '2025-08-15 15:44:44');

-- --------------------------------------------------------

--
-- Table structure for table `customer_transactions_backup`
--

CREATE TABLE `customer_transactions_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `client_id` int(30) NOT NULL,
  `transaction_type` enum('payment','refund','adjustment','order_payment') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `reference_id` varchar(50) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_transactions_backup`
--

INSERT INTO `customer_transactions_backup` (`id`, `client_id`, `transaction_type`, `amount`, `description`, `reference_id`, `date_created`) VALUES
(4, 2, '', 100000.00, 'Balance adjustment: too much balance', 'ADJ-20250815-F3D85C', '2025-08-15 15:44:16'),
(5, 2, '', 98899999.99, 'Balance adjustment: too much', 'ADJ-20250815-731C4A', '2025-08-15 15:44:44');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_alerts`
--

CREATE TABLE `inventory_alerts` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `alert_type` enum('LOW_STOCK','OUT_OF_STOCK','REORDER_POINT','OVERSTOCK') NOT NULL,
  `current_stock` decimal(10,2) NOT NULL,
  `threshold_value` decimal(10,2) NOT NULL,
  `message` text NOT NULL,
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0,
  `resolved_by` int(11) DEFAULT NULL,
  `resolved_date` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_alerts`
--

INSERT INTO `inventory_alerts` (`id`, `product_id`, `alert_type`, `current_stock`, `threshold_value`, `message`, `is_resolved`, `resolved_by`, `resolved_date`, `date_created`) VALUES
(1, 33, 'OVERSTOCK', 99.00, 99.00, 'Overstock alert: ADV 160 has 99 units (Max stock: 99)', 1, NULL, '2025-09-24 16:34:33', '2025-09-23 18:14:04'),
(2, 38, 'OVERSTOCK', 1000.00, 500.00, 'Overstock alert: Beat has 1000 units (Max stock: 500)', 1, NULL, '2025-09-24 16:34:22', '2025-09-24 13:18:41'),
(3, 51, 'OVERSTOCK', 99.00, 20.00, 'Overstock alert: DIO has 99 units (Max stock: 20)', 1, NULL, '2025-09-24 16:34:20', '2025-09-24 13:19:47'),
(4, 52, 'OVERSTOCK', 99.00, 10.00, 'Overstock alert: CRF150L has 99 units (Max stock: 10)', 1, NULL, '2025-09-24 16:34:20', '2025-09-24 13:19:57'),
(5, 47, 'OVERSTOCK', 991.00, 20.00, 'Overstock alert: Airblade 150 has 991 units (Max stock: 20)', 1, NULL, '2025-09-24 16:34:19', '2025-09-24 13:20:16'),
(6, 25, 'OVERSTOCK', 1099.00, 20.00, 'Overstock alert: Honda PCX160 has 1099 units (Max stock: 20)', 1, NULL, '2025-09-24 16:34:26', '2025-09-24 13:20:35'),
(7, 46, 'OVERSTOCK', 909.00, 10.00, 'Overstock alert: PCX 150 has 909 units (Max stock: 10)', 1, NULL, '2025-09-24 16:34:16', '2025-09-24 13:21:03'),
(8, 45, 'OVERSTOCK', 99.00, 20.00, 'Overstock alert: PCX 160 ABS has 99 units (Max stock: 20)', 1, NULL, '2025-09-24 16:34:25', '2025-09-24 13:21:12'),
(9, 44, 'OVERSTOCK', 99.00, 30.00, 'Overstock alert: PCX 160 CBS has 99 units (Max stock: 30)', 1, NULL, '2025-09-24 16:34:24', '2025-09-24 13:21:32'),
(10, 35, 'LOW_STOCK', 99.00, 200.00, 'Low stock alert: TMX 125 ALPHA has 99 units remaining (Reorder point: 200)', 1, NULL, '2025-09-24 16:34:29', '2025-09-24 13:22:06'),
(11, 49, 'OVERSTOCK', 88.00, 10.00, 'Overstock alert: TMX SUPREMO has 88 units (Max stock: 10)', 1, NULL, '2025-09-24 16:34:28', '2025-09-24 13:22:13'),
(12, 43, 'OVERSTOCK', 98.00, 10.00, 'Overstock alert: Wave RSX (DISC)  has 98 units (Max stock: 10)', 1, NULL, '2025-09-24 16:34:31', '2025-09-24 13:22:27'),
(13, 48, 'OVERSTOCK', 99.00, 10.00, 'Overstock alert: XR 150i has 99 units (Max stock: 10)', 1, NULL, '2025-09-24 16:11:17', '2025-09-24 13:22:37'),
(14, 23, 'OVERSTOCK', 1089.00, 20.00, 'Overstock alert: Honda RS125 XRM has 1089 units (Max stock: 20)', 1, NULL, '2025-09-24 16:34:11', '2025-09-24 13:22:47'),
(15, 39, 'OVERSTOCK', 991.00, 300.00, 'Overstock alert: Click 125i SE has 991 units (Max stock: 300)', 1, NULL, '2025-09-24 16:34:08', '2025-09-24 13:23:20'),
(16, 39, 'OVERSTOCK', 992.00, 300.00, 'Overstock alert: Click 125i SE has 992 units (Max stock: 300)', 1, NULL, '2025-09-24 22:08:05', '2025-09-24 22:07:21'),
(17, 38, 'LOW_STOCK', 100.00, 300.00, 'Low stock alert: Beat has 100 units remaining (Reorder point: 300)', 1, NULL, '2025-09-25 08:11:25', '2025-09-25 08:11:19'),
(18, 38, 'LOW_STOCK', 300.00, 300.00, 'Low stock alert: Beat has 300 units remaining (Reorder point: 300)', 1, NULL, '2025-09-25 08:11:54', '2025-09-25 08:11:40'),
(19, 47, 'OVERSTOCK', 100.00, 20.00, 'Overstock alert: Airblade 150 has 100 units (Max stock: 20)', 1, NULL, '2025-09-25 10:36:44', '2025-09-25 10:35:56'),
(20, 46, 'OVERSTOCK', 100.00, 10.00, 'Overstock alert: PCX 150 has 100 units (Max stock: 10)', 1, NULL, '2025-09-25 10:36:43', '2025-09-25 10:36:11'),
(21, 16, 'OVERSTOCK', 100.00, 100.00, 'Overstock alert: Honda Genuine Coolant Oil has 100 units (Max stock: 100)', 1, NULL, '2025-09-25 10:37:38', '2025-09-25 10:37:35'),
(22, 47, 'OVERSTOCK', 40.00, 20.00, 'Overstock alert: Airblade 150 has 40 units (Max stock: 20)', 1, NULL, '2025-09-25 10:37:58', '2025-09-25 10:37:56'),
(23, 44, 'LOW_STOCK', 3.00, 5.00, 'Low stock alert: PCX 160 CBS has 3 units remaining (Reorder point: 5)', 1, NULL, '2025-09-25 10:38:27', '2025-09-25 10:38:24'),
(24, 46, 'OVERSTOCK', 50.00, 10.00, 'Overstock alert: PCX 150 has 50 units (Max stock: 10)', 1, NULL, '2025-10-01 10:11:21', '2025-10-01 10:11:17'),
(25, 37, 'LOW_STOCK', 0.00, 30.00, 'Low stock alert: Airblade 160 has 0 units remaining (Reorder point: 30)', 1, NULL, '2025-10-11 11:50:15', '2025-10-10 16:05:19'),
(26, 37, 'OUT_OF_STOCK', 0.00, 0.00, 'Out of stock: Airblade 160 is no longer available', 1, NULL, '2025-10-11 11:50:12', '2025-10-10 16:05:19'),
(27, 34, 'LOW_STOCK', 0.00, 3.00, 'Low stock alert: Click 160 has 0 units remaining (Reorder point: 3)', 0, NULL, NULL, '2025-10-12 23:08:24'),
(28, 34, 'OUT_OF_STOCK', 0.00, 0.00, 'Out of stock: Click 160 is no longer available', 0, NULL, NULL, '2025-10-12 23:08:24'),
(29, 37, 'LOW_STOCK', 0.00, 30.00, 'Low stock alert: Airblade 160 has 0 units remaining (Reorder point: 30)', 0, NULL, NULL, '2025-10-13 21:54:20'),
(30, 37, 'OUT_OF_STOCK', 0.00, 0.00, 'Out of stock: Airblade 160 is no longer available', 0, NULL, NULL, '2025-10-13 21:54:20');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_settings`
--

CREATE TABLE `inventory_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_settings`
--

INSERT INTO `inventory_settings` (`id`, `setting_key`, `setting_value`, `description`, `date_created`, `date_updated`) VALUES
(1, 'abc_category_a_threshold', '80', 'Percentage threshold for Category A items (top 80% of value)', '2025-08-15 16:55:48', NULL),
(2, 'abc_category_b_threshold', '95', 'Percentage threshold for Category B items (80-95% of value)', '2025-08-15 16:55:48', NULL),
(3, 'abc_category_c_threshold', '100', 'Percentage threshold for Category C items (remaining 5%)', '2025-08-15 16:55:48', NULL),
(4, 'low_stock_alert_percentage', '20', 'Alert when stock is below this percentage of reorder point', '2025-08-15 16:55:48', NULL),
(5, 'overstock_alert_percentage', '150', 'Alert when stock is above this percentage of max stock', '2025-08-15 16:55:48', NULL),
(6, 'auto_abc_classification', '1', 'Enable automatic ABC classification based on sales value', '2025-08-15 16:55:48', NULL),
(7, 'stock_movement_tracking', '1', 'Enable detailed stock movement tracking', '2025-08-15 16:55:48', NULL),
(8, 'product_recommendations', '1', 'Enable product recommendation system', '2025-08-15 16:55:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `order_id` int(30) NOT NULL,
  `service_request_id` int(30) DEFAULT NULL COMMENT 'For service invoices',
  `invoice_number` varchar(50) NOT NULL,
  `customer_id` int(30) NOT NULL,
  `transaction_type` enum('motorcycle_purchase','service','parts') NOT NULL DEFAULT 'motorcycle_purchase',
  `payment_type` enum('cash','installment') NOT NULL DEFAULT 'cash',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `vat_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('unpaid','paid','partial') NOT NULL DEFAULT 'unpaid',
  `pickup_location` varchar(255) NOT NULL DEFAULT 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna',
  `pickup_instructions` text DEFAULT NULL,
  `payment_instructions` text DEFAULT 'Payment must be completed in-store. No online payment available.',
  `generated_by` int(30) NOT NULL COMMENT 'Staff who generated the invoice',
  `generated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--


INSERT INTO `invoices` (`id`, `order_id`, `service_request_id`, `invoice_number`, `customer_id`, `transaction_type`, `payment_type`, `subtotal`, `vat_amount`, `total_amount`, `payment_status`, `pickup_location`, `pickup_instructions`, `payment_instructions`, `generated_by`, `generated_at`, `due_date`, `notes`) VALUES
(1, 27, NULL, 'INV-2025-0001', 8, 'motorcycle_purchase', 'cash', '149900.00', '17988.00', '167888.00', 'paid', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', NULL, 'Payment must be completed in-store. No online payment available. Please bring valid ID and payment method.', 9, '2025-10-10 08:23:16', '2025-10-17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `item_type` enum('motorcycle','part','service','accessory') NOT NULL,
  `item_id` int(30) NOT NULL COMMENT 'Reference to product_list, service_list, etc.',
  `item_name` varchar(255) NOT NULL,
  `item_description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items`
(`invoice_id`, `item_type`, `item_id`, `item_name`, `item_description`, `quantity`, `unit_price`, `total_price`, `created_at`)
VALUES
(0, 'motorcycle', 35, 'TMX 125 ALPHA', '<p>124.9cc air-cooled, 4-speed manual, Workhorse underbone, Drum Brakes</p>', 1, '0.00', '0.00', '2025-10-12 18:20:57'),
(0, 'motorcycle', 36, 'XRM 125 Dual Sport Fi', '<p>125cc air-cooled, SOHC, 4-speed semi-auto, On/Off-road utility</p>', 1, '0.00', '0.00', '2025-10-12 18:20:57'),
(0, 'motorcycle', 39, 'Click 125i SE', '<p>123cc liquid-cooled, 2-valve SOHC eSP, CBS, ACG Starter, Idling Stop</p>', 1, '0.00', '0.00', '2025-10-12 18:20:57'),
(0, 'motorcycle', 42, 'Supra GTR 150', '<p><strong>Complete Motorcycle</strong></p><p>Experience the thrill of the open road...</p>', 1, '0.00', '0.00', '2025-10-12 18:20:57'),
(0, 'motorcycle', 52, 'CRF150L', '<p>149.15cc air-cooled, 5-speed manual, Enduro/Trail bike, Electric Start</p>', 1, '0.00', '0.00', '2025-10-12 18:20:57'),
(0, 'motorcycle', 26, 'Honda Click 125i', '<p><strong>Honda Click 125i</strong></p><p>PGM-FI engine with Enhanced Smart Power...</p>', 1, '0.00', '0.00', '2025-10-12 18:50:42'),
(0, 'motorcycle', 39, 'Click 125i SE', '<p>123cc liquid-cooled, 2-valve SOHC eSP...</p>', 1, '0.00', '0.00', '2025-10-12 18:51:03');


-- --------------------------------------------------------

--
-- Table structure for table `invoice_settings`
--

CREATE TABLE `invoice_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Insert data safely
INSERT INTO `invoice_settings` (`setting_key`, `setting_value`, `description`, `updated_at`) VALUES
('invoice_prefix', 'INV', 'Prefix for invoice numbers', '2025-10-10 08:22:39'),
('receipt_prefix', 'RCPT', 'Prefix for receipt numbers', '2025-10-10 08:22:39'),
('vat_rate', '12', 'VAT rate percentage', '2025-10-10 08:22:39'),
('company_name', 'Star Honda Calamba', 'Company name for invoices', '2025-10-10 08:22:39'),
('company_address', 'National Highway Brgy. Parian, Calamba City, Laguna', 'Company address', '2025-10-10 08:22:40'),
('company_phone', '0948-235-3207', 'Company phone number', '2025-10-10 08:22:40'),
('company_email', 'starhondacalamba55@gmail.com', 'Company email', '2025-10-10 08:22:40'),
('pickup_location', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', 'Default pickup location', '2025-10-10 08:22:40'),
('payment_instructions', 'Payment must be completed in-store. No online payment available. Please bring valid ID and payment method.', 'Default payment instructions', '2025-10-10 08:22:40'),
('acknowledgment_note', 'Thank you for your purchase at Star Honda Calamba! We appreciate your business and look forward to serving you again.', 'Default acknowledgment note', '2025-10-10 08:22:40');

--
-- Table structure for table `mechanics_list`
--

CREATE TABLE `mechanics_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mechanics_list`
--

INSERT INTO `mechanics_list` (`id`, `name`, `contact`, `email`, `status`, `date_created`) VALUES
(7, 'Jan Jan Matanguihan', '09282346151', 'janjanmatanguihan@gmail.com', 1, '2025-09-18 16:44:09'),
(8, 'Aldrin Caldozo', '09065775184', 'aldrincaldozo@gmail.com', 1, '2025-09-18 16:44:53'),
(9, 'Fernando Rimando', '09505639564', 'fernandorimando@gmail.com', 1, '2025-09-18 16:45:36'),
(10, 'Ricardo Montalban', '09286594732', 'ricardomontalban@gmail.com', 1, '2025-09-18 16:46:05');

-- --------------------------------------------------------

--
-- Table structure for table `motorcycle_specifications`
--

CREATE TABLE `motorcycle_specifications` (
  `id` int(11) NOT NULL,
  `product_id` int(30) NOT NULL,
  `make` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `transmission` varchar(50) DEFAULT NULL,
  `engine_type` text DEFAULT NULL,
  `displacement` varchar(20) DEFAULT NULL,
  `seat_height` varchar(20) DEFAULT NULL,
  `brake_system_front` varchar(100) DEFAULT NULL,
  `brake_system_rear` varchar(100) DEFAULT NULL,
  `fuel_capacity` varchar(20) DEFAULT NULL,
  `front_tire` varchar(50) DEFAULT NULL,
  `rear_tire` varchar(50) DEFAULT NULL,
  `wheels_type` varchar(50) DEFAULT NULL,
  `starting_system` varchar(50) DEFAULT NULL,
  `overall_dimensions` varchar(100) DEFAULT NULL,
  `ground_clearance` varchar(20) DEFAULT NULL,
  `fuel_system` varchar(50) DEFAULT NULL,
  `headlight` varchar(50) DEFAULT NULL,
  `taillight` varchar(50) DEFAULT NULL,
  `maximum_power` varchar(50) DEFAULT NULL,
  `maximum_torque` varchar(50) DEFAULT NULL,
  `fuel_consumption` varchar(50) DEFAULT NULL,
  `compression_ratio` varchar(20) DEFAULT NULL,
  `ignition_type` varchar(50) DEFAULT NULL,
  `bore_stroke` varchar(50) DEFAULT NULL,
  `engine_oil_capacity` varchar(20) DEFAULT NULL,
  `battery_type` varchar(50) DEFAULT NULL,
  `gear_shift_pattern` varchar(50) DEFAULT NULL,
  `suspension_front` varchar(100) DEFAULT NULL,
  `suspension_rear` varchar(100) DEFAULT NULL,
  `wheelbase` varchar(20) DEFAULT NULL,
  `curb_weight` varchar(20) DEFAULT NULL,
  `frame_type` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `transmission_type` varchar(100) DEFAULT NULL,
  `ignition_system` varchar(50) DEFAULT NULL,
  `brake_type_front` varchar(100) DEFAULT NULL,
  `brake_type_rear` varchar(100) DEFAULT NULL,
  `tire_size_front` varchar(50) DEFAULT NULL,
  `tire_size_rear` varchar(50) DEFAULT NULL,
  `wheel_type` varchar(50) DEFAULT NULL,
  `overall_dimensions_lwh` varchar(100) DEFAULT NULL,
  `fuel_tank_capacity` varchar(20) DEFAULT NULL,
  `minimum_ground_clearance` varchar(20) DEFAULT NULL,
  `max_output` varchar(50) DEFAULT NULL,
  `front_suspension` varchar(100) DEFAULT NULL,
  `rear_suspension` varchar(100) DEFAULT NULL,
  `dry_weight` varchar(20) DEFAULT NULL,
  `wet_weight` varchar(20) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `specification_type` varchar(50) DEFAULT NULL,
  `suspension_type_front` varchar(100) DEFAULT NULL,
  `suspension_type_rear` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `motorcycle_specifications`
--

INSERT INTO `motorcycle_specifications` (`id`, `product_id`, `make`, `model`, `transmission`, `engine_type`, `displacement`, `seat_height`, `brake_system_front`, `brake_system_rear`, `fuel_capacity`, `front_tire`, `rear_tire`, `wheels_type`, `starting_system`, `overall_dimensions`, `ground_clearance`, `fuel_system`, `headlight`, `taillight`, `maximum_power`, `maximum_torque`, `fuel_consumption`, `compression_ratio`, `ignition_type`, `bore_stroke`, `engine_oil_capacity`, `battery_type`, `gear_shift_pattern`, `suspension_front`, `suspension_rear`, `wheelbase`, `curb_weight`, `frame_type`, `category`, `transmission_type`, `ignition_system`, `brake_type_front`, `brake_type_rear`, `tire_size_front`, `tire_size_rear`, `wheel_type`, `overall_dimensions_lwh`, `fuel_tank_capacity`, `minimum_ground_clearance`, `max_output`, `front_suspension`, `rear_suspension`, `dry_weight`, `wet_weight`, `date_created`, `date_updated`, `specification_type`, `suspension_type_front`, `suspension_type_rear`) VALUES
(1, 33, 'HONDA - The Power of Dreams', 'HONDA ADV 160 ABS', NULL, '4-Stroke, 4-Valve, SOHC, Liquid-Cooled, eSP+', '157 cc', '780 mm', NULL, NULL, NULL, NULL, NULL, NULL, 'Electric (ACG Starter)', NULL, '165 mm', 'PGM-FI', NULL, NULL, '11.8 kW @ 8,500 rpm', NULL, '45.0 km/L (WMTC Test Method)', NULL, NULL, NULL, NULL, '12V - 5Ah (MF-WET)', NULL, NULL, NULL, NULL, NULL, NULL, 'Adventure', NULL, 'Full Transisterized', 'Hydraulic Disc with ABS', 'Hydraulic Disc', '110/80-14M/C 53P (Tubeless)', '130/70-13M/C 57P (Tubeless)', 'Cast Wheel', '1,950 x 763 x 1,196 (mm)', '8.1 L', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-10 10:12:49', NULL, NULL, NULL, NULL),
(2, 34, 'HONDA - The Power of Dreams', 'HONDA CLICK 160', 'Automatic', '4-Stroke, 4-Valve, SOHC, Liquid Cooled, eSP+', '157cc', '778 mm', 'Hydraulic Disc', 'Mechanical Leading Trailing', '5.5 L', '100/80-14 M/C 48P (Tubeless)', '120/70-14 M/C 61P (Tubeless)', 'Cast Wheel', 'Electric (ACG Starter)', '1,929 x 678 x 1,062 (mm)', '138mm', 'PGM-Fi', NULL, NULL, '11.3kW @ 8,500rpm', '13.8Nm @ 7,000rpm', NULL, NULL, 'Full Transisterized', NULL, '0.9L', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pang Araw-Araw', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-10 10:12:49', NULL, NULL, NULL, NULL),
(3, 39, 'HONDA - The Power of Dreams', 'HONDA Click 125i', 'Automatic', '4-Stroke, 2-Valve, SOHC, Liquid-Cooled, eSP', '125 cc', '769 mm', 'Hydraulic Disc Brake', 'Mechanical Leading Trailing', '5.5 L', '80/90 - 14 M/C 40P', '90/90 - 14 M/C 46P', 'Cast Wheel', 'Electric / DECOMP', '1,919 x 679 x 1,062 (mm)', '132 mm', 'PGM-Fi', 'LED', 'LED', '8.2 kW @ 8,500 rpm', '10.8 Nm @ 5,000 rpm', '50.3 km/L (WMTC Test Method)', '11.0 : 1', 'Full Transisterized', '52.4 x 57.9 (mm)', '0.9L', '12V - 5Ah (MF-WET)', 'Automatic (V-Matic)', 'Telescopic', 'Unit Swing', '1,280 mm', '112 Kg', 'Scooter', 'Pang Araw-Araw', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-10 10:12:49', NULL, NULL, NULL, NULL),
(4, 52, 'HONDA - The Power of Dreams', 'HONDA CRF150L', NULL, '4-Stroke, 2 Valves, SOHC, Air-Cooled', '149 cc', '863 mm', 'Hydraulic Disc Brake', 'Hydraulic Disc Brake', '7.2 L', '70/100 - 21', '90/100 - 18', 'Spoke', 'Electric & Kick', '2,119 x 793 x 1,153 (mm)', '285 mm', 'PGM-Fi', NULL, NULL, NULL, NULL, '45.5 km/L', NULL, NULL, NULL, NULL, NULL, 'Manual (1-N-2-3-4-5)', NULL, NULL, NULL, NULL, NULL, 'Adventure', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-10 10:12:49', NULL, NULL, NULL, NULL),
(5, 51, 'HONDA - The Power of Dreams', 'HONDA DIO', 'Automatic', '4-Stroke, SOHC, Air-Cooled', '109 cc', '765 mm', 'Mechanical Drum Brake', 'Mechanical Drum Brake', '5.3 Liters', '90 / 100 – 10 53J (Tubeless)', '90 / 100 – 10 53J (Tubeless)', 'Steel Rims', NULL, '1,781 x 710 x 1,133 (mm)', '158 mm', 'Carburetor', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.8 Liter', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pang Araw-Araw', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-10 10:12:49', NULL, NULL, NULL, NULL),
(6, 46, 'HONDA - The Power of Dreams', 'HONDA PCX 150', 'Automatic', '153cc liquid-cooled single-cylinder four-stroke', NULL, '29.9 inches', 'Single 220mm disc with three-piston caliper and CBS', 'Drum with CBS', '8 Liters', '90/90-14', '100/90-14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full transistorized ignition', '58.0mm x 57.9mm', NULL, NULL, NULL, NULL, NULL, '51.8 inches', NULL, 'Scooter', 'Automatic', 'Honda V-Matic belt-converter automatic transmission', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-10 10:12:49', NULL, NULL, NULL, NULL),
(7, 45, 'HONDA - The Power of Dreams', 'HONDA The ALL-New PCX160 ABS', 'Automatic', '4-Stroke, 4-Valve, SOHC, Liquid-Cooled, eSP+', '157 cc', NULL, 'Hydraulic Disk', 'Hydraulic Disk', '8.1 L', NULL, NULL, 'Cast Wheel', NULL, '1,936 x 742 x 1,123 (mm)', NULL, 'PGM-FI', NULL, NULL, '11.8 kW @ 8,500 rpm', '14.7 Nm @ 6,500 rpm', NULL, '12.0 : 1', 'Full Transisterized', '60.0 x 55.5 mm', '0.9 L', NULL, NULL, NULL, NULL, '1,313 mm', NULL, NULL, 'Premium', 'Automatic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '134 mm', NULL, NULL, NULL, NULL, NULL, '2025-10-10 10:12:49', NULL, NULL, NULL, NULL),
(8, 44, 'HONDA - The Power of Dreams', 'HONDA The ALL-New PCX160 CBS', 'Automatic', '4-Stroke, 4-Valve, SOHC, Liquid-Cooled, eSP+', '157 cc', '764 mm', 'Hydraulic Disk', 'Hydraulic Disk', '8.1 L', NULL, '130/70 – 13MC 63P (Tubeless)', 'Cast Wheel', 'Electric', '1,936 x 742 x 1,123 (mm)', NULL, 'PGM-FI', NULL, NULL, '11.8 kW @ 8,500 rpm', '14.7 Nm @ 6,500 rpm', NULL, '12.0 : 1', 'Full Transisterized', '60.0 x 55.5 mm', '0.9 L', NULL, NULL, NULL, NULL, '1,313 mm', NULL, NULL, 'Premium', 'Automatic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '134 mm', NULL, 'Telescopic', 'Twin Shock', NULL, NULL, '2025-10-10 10:12:49', NULL, NULL, NULL, NULL),
(9, 41, 'HONDA - The Power of Dreams', 'HONDA RS125', NULL, '4-Stroke, SOHC, Air-Cooled', '125 cc', '767 mm', NULL, NULL, NULL, NULL, NULL, NULL, 'Electric & Kick', '1,909 x 685 x987 (mm)', '135 mm', 'PGM-Fi', NULL, NULL, '7.12 kW @ 7,500 rpm', '9.55 N.m @ 6,500 rpm', '67.5 Km/L', '9.3 : 1', NULL, '52.4 x 57.9 (mm)', '0.9L', '12V - 3 Ah MF-Wet', 'Rotary (N-1-2-3-4)', 'Telescopic', 'Twin', '1,228 mm', '104 Kg', NULL, 'Sport', 'Manual, 4-Speed, Constant Mesh', 'Full Transisterized', 'Hydraulic Disc', 'Drum', '70/90 - 17 M/C 38P', '80/90 - 17 M/C 50P', 'Spoke', NULL, '3.9 L', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-10 10:12:49', NULL, NULL, NULL, NULL),
(10, 42, 'HONDA - The Power of Dreams', 'HONDA Supra GTR 150', 'Manual', '4-Stroke, 4 Valves, DOHC, Liquid-Cooled w/ Auto Fan', '150', '786 mm', 'Hydraulic Disc', 'Hydraulic Disc', '4.5 L', '90/80 - 17MC 46P', NULL, 'Cast Wheel', 'Electric/Kick', '2,021 x 725 x 1,105 (mm)', NULL, 'PGM-FI', 'Dual Layer Led', NULL, '11.5 kW @ 9,000 rpm', '13.6 N.m @ 6,700 rpm', NULL, NULL, 'Full Transistorized', '57.3 x 57.8 (mm)', '1.1 L', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Sport', 'Manual 6-Speed Constant Mesh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Telescopic', NULL, NULL, NULL, '2025-10-10 10:12:49', NULL, NULL, NULL, NULL),
(11, 35, 'HONDA - The Power of Dreams', 'HONDA TMX Alpha', NULL, '4-Stroke, Over Head Valve (OHV)', '125 cc', '759 mm', NULL, NULL, NULL, NULL, NULL, NULL, 'Electric & Kick', '1,904 x 754 x 1,026 (mm)', '156 mm', 'Carburetor', NULL, NULL, NULL, NULL, '62.5km/L at 45Km/H constant speed', NULL, NULL, NULL, '1.1 L', '12 V - 5 Ah - MF - WET', '5-Speed, Constant Mesh (N-1-2-3-4-5)', NULL, NULL, NULL, '113 Kg', NULL, 'Pang Negosyo', NULL, 'AC - CDI Magnetic', 'Mechanical Leading Trailing (Drum Brake)', 'Mechanical Leading Trailing (Drum Brake)', '2.50 x 18 40L', '2.75 x 18 48L', 'Spoke', NULL, '8.6 L', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-10 10:12:49', NULL, NULL, NULL, NULL),
(12, 49, 'HONDA - The Power of Dreams', 'HONDA TMX Supremo (150 - 3rd Gen)', NULL, '4 Stroke, OHC, Air Cooled', '149.2cc', '771 mm', 'Mechanical Leading Trailing', 'Mechanical Leading Trailing', '14.3 Liters (Reserve', '80/100 - 18M/C 47P', '90/90 - 18M/C 51P', 'Spoke', 'Kick / Electric Starter', '2,037 mm x 778 mm x 1,068 mm', NULL, 'Unleaded Gasoline (93+ or above octane rating)', NULL, NULL, '7.85kW (10.7Ps) @ 7,000rpm', '11.58Nm (1.18kgfm) @5,000rpm', '62km/L', NULL, 'DC-CDI', '57.3mm x 57.8mm', '1.0 Liter', NULL, 'Manual / 1-2-3-4-5', NULL, NULL, '1,306 mm', NULL, 'Backbone', 'Pang Negosyo', 'Manual, 5-Speed Constant Mesh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '163 mm', NULL, 'Telescopic Fork', 'Twin', '120 Kg', NULL, '2025-10-10 10:12:49', NULL, NULL, NULL, NULL),
(13, 43, 'HONDA - The Power of Dreams', 'HONDA Wave 110R Disk', NULL, '4-Stroke, SOHC, Air-Cooled', '109cc', '760mm', 'Hydraulic Disc', 'Mechanical Leading Trailing', '4.0L', '70/90-17M/C 38P (Tube Type)', '80/90-17M/C 50P (Tube Type)', 'Spoke', 'Electric / Kick', '1,921 x 709 x 1,081 mm', NULL, 'PGM-Fi', NULL, NULL, '6.46 kW @ 7,500 rpm', '8.70 N.m @ 6,000 rpm', '69.5 km/l', '9.3:1', 'Full Transisterized', '50.0 x 55.6 (mm)', '1.0L', NULL, 'N-1-2-3-4-N (Rotary)', NULL, NULL, '1,227mm', NULL, 'Underbone', 'Pang Araw-Araw', 'Manual, 4-Speed Constant Mesh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '135mm', NULL, 'Telescopic', 'Twin Shock', '99kg', NULL, '2025-10-10 10:12:49', NULL, NULL, NULL, NULL),
(14, 26, 'HONDA - The Power of Dreams', 'HONDA Click 125i', 'Automatic', '4-Stroke, 2-Valve, SOHC, Liquid-Cooled, eSP', '125 cc', '769 mm', 'Hydraulic Disc Brake', 'Mechanical Leading Trailing', '5.5 L', '80/90 - 14 M/C 40P', '90/90 - 14 M/C 46P', 'Cast Wheel', 'Electric / DECOMP', '1,919 x 679 x 1,062 (mm)', '132 mm', 'PGM-Fi', 'LED', 'LED', '8.2 kW @ 8,500 rpm', '10.8 Nm @ 5,000 rpm', '50.3 km/L (WMTC Test Method)', '11.0 : 1', 'Full Transisterized', '52.4 x 57.9 (mm)', '0.9L', '12V - 5Ah (MF-WET)', 'Automatic (V-Matic)', 'Telescopic', 'Unit Swing', '1,280 mm', '112 Kg', 'Scooter', 'Pang Araw-Araw', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-10 13:31:09', NULL, NULL, NULL, NULL),
(15, 55, 'HONDA - The Power of Dreams', 'HONDA CLICK 160', 'Automatic', '4-Stroke, 4-Valve, SOHC, Liquid Cooled, eSP+', '157cc', '778 mm', 'Hydraulic Disc', 'Mechanical Leading Trailing', '5.5 L', '100/80-14 M/C 48P (Tubeless)', '120/70-14 M/C 61P (Tubeless)', 'Cast Wheel', 'Electric (ACG Starter)', '1,929 x 678 x 1,062 (mm)', '138mm', 'PGM-Fi', NULL, NULL, '11.3kW @ 8,500rpm', '13.8Nm @ 7,000rpm', NULL, NULL, 'Full Transisterized', NULL, '0.9L', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pang Araw-Araw', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-10 13:31:09', NULL, NULL, NULL, NULL),
(16, 56, 'HONDA - The Power of Dreams', 'HONDA CRF150L', NULL, '4-Stroke, 2 Valves, SOHC, Air-Cooled', '149 cc', '863 mm', 'Hydraulic Disc Brake', 'Hydraulic Disc Brake', '7.2 L', '70/100 - 21', '90/100 - 18', 'Spoke', 'Electric & Kick', '2,119 x 793 x 1,153 (mm)', '285 mm', 'PGM-Fi', NULL, NULL, NULL, NULL, '45.5 km/L', NULL, NULL, NULL, NULL, NULL, 'Manual (1-N-2-3-4-5)', NULL, NULL, NULL, NULL, NULL, 'Adventure', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-10 13:31:09', NULL, NULL, NULL, NULL),
(17, 14, 'HONDA - The Power of Dreams', 'HONDA DIO', 'Automatic', '4-Stroke, SOHC, Air-Cooled', '109 cc', '765 mm', 'Mechanical Drum Brake', 'Mechanical Drum Brake', '5.3 Liters', '90 / 100 – 10 53J (Tubeless)', '90 / 100 – 10 53J (Tubeless)', 'Steel Rims', NULL, '1,781 x 710 x 1,133 (mm)', '158 mm', 'Carburetor', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.8 Liter', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pang Araw-Araw', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-10 13:31:09', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(30) NOT NULL,
  `type` enum('payment_upcoming','payment_missed','order_status','general') NOT NULL DEFAULT 'general',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reference_id` int(30) DEFAULT NULL COMMENT 'Reference to order_id or other related record',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_read` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `reference_id`, `is_read`, `date_created`, `date_read`) VALUES
(1, 2, 'payment_upcoming', 'Payment Due Soon', 'Payment reminder: Your order 202504-00001 (₱12,500.00) is due in 2 day(s).', 9, 0, '2025-10-10 07:40:00', NULL),
(2, 6, 'payment_missed', 'Payment Overdue', 'Overdue payment: Your order 202508-00001 (₱150,000.00) is 5 day(s) overdue.', 12, 0, '2025-10-10 07:40:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(30) NOT NULL,
  `order_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `quantity` float NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `color`, `quantity`, `date_added`) VALUES
(21, 12, 13, NULL, 1, '2025-08-15 10:02:27'),
(22, 13, 27, NULL, 1, '2025-08-15 12:08:18'),
(23, 13, 17, NULL, 3, '2025-08-15 12:08:18'),
(24, 13, 19, NULL, 5, '2025-08-15 12:08:18'),
(26, 13, 20, NULL, 1, '2025-08-15 12:08:18'),
(28, 14, 25, NULL, 1, '2025-08-15 12:21:54'),
(29, 15, 26, NULL, 1, '2025-08-15 12:44:56'),
(30, 16, 26, NULL, 1, '2025-08-15 12:55:39'),
(31, 17, 25, NULL, 20, '2025-08-15 13:12:14'),
(32, 18, 12, NULL, 2, '2025-09-18 20:11:42'),
(34, 19, 39, 'Red', 1, '2025-09-24 16:31:10'),
(35, 20, 26, NULL, 1, '2025-09-24 22:32:50'),
(36, 20, 33, 'Black', 1, '2025-09-24 22:32:50'),
(37, 21, 38, 'Fashion Sport STD Black Red', 1, '2025-09-25 08:07:36'),
(38, 22, 38, 'Street STD Black', 1, '2025-09-25 08:17:38'),
(39, 23, 22, NULL, 1, '2025-09-25 08:20:54'),
(40, 23, 18, NULL, 1, '2025-09-25 08:20:54'),
(41, 23, 21, NULL, 1, '2025-09-25 08:20:54'),
(42, 23, 16, NULL, 1, '2025-09-25 08:20:54'),
(43, 23, 42, 'Black', 1, '2025-09-25 08:20:54'),
(44, 24, 38, 'Honda Beat Limited Edition', 1, '2025-09-25 10:19:12'),
(51, 26, 52, NULL, 1, '2025-10-10 08:04:31'),
(52, 26, 46, NULL, 1, '2025-10-10 08:04:31'),
(53, 26, 17, NULL, 1, '2025-10-10 08:04:31'),
(54, 26, 34, NULL, 1, '2025-10-10 08:04:31'),
(55, 26, 44, NULL, 1, '2025-10-10 08:04:31'),
(56, 26, 38, 'Fashion Sport STD', 1, '2025-10-10 08:04:31'),
(57, 26, 38, 'Fashion Sport STD Black Red', 1, '2025-10-10 08:04:31'),
(58, 26, 19, NULL, 2, '2025-10-10 08:04:31'),
(59, 26, 22, NULL, 1, '2025-10-10 08:04:31'),
(60, 26, 46, 'White', 1, '2025-10-10 08:04:31'),
(61, 27, 45, 'White', 1, '2025-10-10 08:12:26'),
(62, 28, 40, 'White', 1, '2025-10-13 21:04:07'),
(63, 29, 38, 'Fashion Sport STD', 1, '2025-10-13 21:55:34'),
(64, 29, 34, NULL, 1, '2025-10-13 21:55:34'),
(65, 29, 42, NULL, 1, '2025-10-13 21:55:34'),
(66, 29, 52, NULL, 1, '2025-10-13 21:55:34');

-- --------------------------------------------------------

--
-- Table structure for table `order_list`
--

CREATE TABLE `order_list` (
  `id` int(30) NOT NULL,
  `ref_code` varchar(100) NOT NULL,
  `client_id` int(30) NOT NULL,
  `total_amount` float NOT NULL DEFAULT 0,
  `delivery_address` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=pending,1 = packed, 2 = for delivery, 3 = on the way, 4 = delivered, 5=cancelled',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_list`
--

INSERT INTO `order_list` (`id`, `ref_code`, `client_id`, `total_amount`, `delivery_address`, `status`, `date_created`, `date_updated`) VALUES
(9, '202504-00001', 2, 12500, 'Blk 72 Lot 7 Phase 6 Mabuhay Mamatid Cabuyao Laguna', 0, '2025-04-23 22:49:13', '2025-04-23 22:49:13'),
(10, '202504-00002', 2, 12020, 'Blk 72 Lot 7 Phase 6 Mabuhay Mamatid Cabuyao Laguna', 0, '2025-04-24 08:13:00', '2025-04-24 08:13:00'),
(11, '202504-00003', 2, 17000, 'Blk 72 Lot 7 Phase 6 Mabuhay Mamatid Cabuyao Laguna', 0, '2025-04-24 08:27:35', '2025-04-24 08:27:35'),
(12, '202508-00001', 5, 150000, 'Blk 82 Lot 9 Phase 2 Mabuhay Mamatid Cabuyao Laguna', 1, '2025-08-15 10:02:27', '2025-10-11 08:42:28'),
(13, '202508-00002', 6, 73200, 'Blk 88 Lot 8 Mabuhay Mamatid Cabuyao Laguna', 1, '2025-08-15 12:08:17', '2025-08-15 12:08:49'),
(14, '202508-00003', 6, 112000, 'Blk 88 Lot 8 Mabuhay Mamatid Cabuyao Laguna', 1, '2025-08-15 12:21:54', '2025-08-15 12:22:27'),
(15, '202508-00004', 6, 88000, 'Blk 88 Lot 8 Mabuhay Mamatid Cabuyao Laguna', 1, '2025-08-15 12:44:56', '2025-08-15 12:45:59'),
(16, '202508-00005', 2, 88000, 'Blk 72 Lot 7 Phase 6 Mabuhay Mamatid Cabuyao Laguna', 1, '2025-08-15 12:55:39', '2025-08-15 12:56:32'),
(17, '202508-00006', 6, 2240000, 'Blk 88 Lot 8 Mabuhay Mamatid Cabuyao Laguna', 0, '2025-08-15 13:12:14', '2025-08-15 13:12:14'),
(18, 'ORD-20250918-A878BD', 6, 152500, 'Blk 88 Lot 8 Mabuhay Mamatid Cabuyao Laguna', 6, '2025-09-18 20:11:42', '2025-09-24 23:41:49'),
(19, 'ORD-20250924-35F781', 6, 55555, 'Blk 88 Lot 8 Mabuhay Mamatid Cabuyao Laguna', 6, '2025-09-24 16:31:10', '2025-10-12 18:51:03'),
(20, 'ORD-20250924-BAD697', 8, 1088000, 'Blk 8 Lot 99 Mabuhay Mamatid Cabuyao Laguna', 6, '2025-09-24 22:32:50', '2025-10-12 18:50:42'),
(21, 'ORD-20250925-51A569', 8, 98989, 'Blk 8 Lot 99 Mabuhay Mamatid Cabuyao Laguna', 0, '2025-09-25 08:07:36', NULL),
(22, 'ORD-20250925-6C8A0F', 8, 98989, 'Blk 8 Lot 99 Mabuhay Mamatid Cabuyao Laguna', 0, '2025-09-25 08:17:38', NULL),
(23, 'ORD-20250925-07B1E1', 8, 80582, 'Blk 8 Lot 99 Mabuhay Mamatid Cabuyao Laguna', 0, '2025-09-25 08:20:54', NULL),
(24, 'ORD-20250925-57A5A7', 9, 98989, 'BLK 8 Lot 88 Mabuhay Nueva Vizcaya, Philippines', 6, '2025-09-25 10:19:12', '2025-09-25 10:20:44'),
(26, 'ORD-20251010-35B615', 8, 764900, 'Blk 8 Lot 99 Mabuhay Mamatid Cabuyao Laguna', 0, '2025-10-10 08:04:31', NULL),
(27, 'ORD-20251010-4DD41C', 8, 149900, 'Blk 8 Lot 99 Mabuhay Mamatid Cabuyao Laguna', 6, '2025-10-10 08:12:26', '2025-10-10 08:23:27');

-- --------------------------------------------------------

--
-- Table structure for table `or_cr_documents`
--

CREATE TABLE `or_cr_documents` (
  `id` int(11) NOT NULL,
  `client_id` int(30) NOT NULL,
  `document_type` enum('or','cr') NOT NULL,
  `document_number` varchar(50) NOT NULL,
  `plate_number` varchar(20) DEFAULT NULL,
  `vehicle_model` varchar(100) DEFAULT NULL,
  `vehicle_brand` varchar(100) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('pending','released','expired') DEFAULT 'pending',
  `file_path` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `or_cr_documents`
--

INSERT INTO `or_cr_documents` (`id`, `client_id`, `document_type`, `document_number`, `plate_number`, `vehicle_model`, `vehicle_brand`, `release_date`, `expiry_date`, `status`, `file_path`, `remarks`, `date_created`, `date_updated`) VALUES
(0, 2, 'or', 'DOC-2025-00123', 'NCA 1234', 'Beat 110', 'Honda', '2025-10-14', '2026-10-14', 'released', 'uploads/documents/0.pdf?v=1760318873', '', '2025-10-12 18:27:53', '2025-10-12 18:27:53'),
(1, 6, 'cr', 'OR-2025-001234', 'ABC 1234', NULL, NULL, '2025-10-10', NULL, 'released', 'uploads/documents/1.pdf?v=1755242148', '', '2025-08-15 15:15:48', '2025-10-11 06:17:41'),
(2, 8, 'cr', 'OR-2025-001234', 'ABC 1234', 'Honda Beat', 'Honda', '2025-10-11', '2029-06-11', 'pending', 'uploads/documents/2.jpg?v=1760147441', '', '2025-10-11 09:50:41', '2025-10-11 09:50:41');

-- --------------------------------------------------------

--
-- Table structure for table `product_availability_notifications`
--

CREATE TABLE `product_availability_notifications` (
  `id` int(30) NOT NULL,
  `client_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `is_notified` tinyint(1) DEFAULT 0,
  `date_requested` datetime NOT NULL DEFAULT current_timestamp(),
  `date_notified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_availability_notifications`
--

INSERT INTO `product_availability_notifications` (`id`, `client_id`, `product_id`, `is_notified`, `date_requested`, `date_notified`) VALUES
(1, 8, 37, 0, '2025-10-10 16:09:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_color_images`
--

CREATE TABLE `product_color_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color` varchar(50) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_color_images`
--

INSERT INTO `product_color_images` (`id`, `product_id`, `color`, `image_path`, `date_created`) VALUES
(1, 33, 'Red', 'uploads/products/colors/33_red.webp?v=1758621663', '2025-09-23 18:01:03'),
(2, 33, 'White', 'uploads/products/colors/33_white.jpg?v=1758621663', '2025-09-23 18:01:03'),
(3, 33, 'Black', 'uploads/products/colors/33_black.jpg?v=1758621663', '2025-09-23 18:01:03'),
(4, 34, 'Silver Black', 'uploads/products/colors/34_silver_black.webp?v=1758688171', '2025-09-24 12:29:31'),
(5, 34, 'Red Black', 'uploads/products/colors/34_red_black.webp?v=1758688171', '2025-09-24 12:29:31'),
(6, 34, 'Black Orange', 'uploads/products/colors/34_black_orange.webp?v=1758688171', '2025-09-24 12:29:31'),
(7, 35, 'Black', 'uploads/products/colors/35_black.jpg?v=1758688317', '2025-09-24 12:31:57'),
(8, 35, 'Red', 'uploads/products/colors/35_red.jpg?v=1758688317', '2025-09-24 12:31:57'),
(9, 35, 'Gray', 'uploads/products/colors/35_gray.webp?v=1758688317', '2025-09-24 12:31:57'),
(10, 36, 'Yellow', 'uploads/products/colors/36_yellow.webp?v=1758688526', '2025-09-24 12:35:26'),
(11, 36, 'Green', 'uploads/products/colors/36_green.webp?v=1758688526', '2025-09-24 12:35:26'),
(12, 36, 'Blue', 'uploads/products/colors/36_blue.webp?v=1758688526', '2025-09-24 12:35:26'),
(13, 36, 'Red', 'uploads/products/colors/36_red.webp?v=1758688526', '2025-09-24 12:35:26'),
(14, 37, 'Red', 'uploads/products/colors/37_red.jpg?v=1758688625', '2025-09-24 12:37:05'),
(15, 37, 'Gray', 'uploads/products/colors/37_gray.jpg?v=1758688625', '2025-09-24 12:37:05'),
(16, 37, 'Dark Blue', 'uploads/products/colors/37_dark_blue.jpg?v=1758688625', '2025-09-24 12:37:05'),
(17, 38, 'Street STD Black', 'uploads/products/colors/38_street_std_black.jpg?v=1758688996', '2025-09-24 12:43:16'),
(18, 38, 'Street STD Gray', 'uploads/products/colors/38_street_std_gray.jpg?v=1758688996', '2025-09-24 12:43:16'),
(19, 38, 'Black Premium', 'uploads/products/colors/38_black_premium.webp?v=1758688996', '2025-09-24 12:43:16'),
(20, 38, 'White Premium', 'uploads/products/colors/38_white_premium.webp?v=1758688996', '2025-09-24 12:43:16'),
(21, 38, 'Playful Yellow', 'uploads/products/colors/38_playful_yellow.webp?v=1758688996', '2025-09-24 12:43:16'),
(22, 38, 'Playful Red', 'uploads/products/colors/38_playful_red.webp?v=1758688996', '2025-09-24 12:43:16'),
(23, 38, 'Playful Gray', 'uploads/products/colors/38_playful_gray.webp?v=1758688996', '2025-09-24 12:43:16'),
(24, 38, 'Playful Blue', 'uploads/products/colors/38_playful_blue.webp?v=1758688996', '2025-09-24 12:43:16'),
(25, 38, 'Fashion Sport STD Black Red', 'uploads/products/colors/38_fashion_sport_std_black_red.jpg?v=1758688996', '2025-09-24 12:43:16'),
(26, 38, 'Fashion Sport STD Black Orange', 'uploads/products/colors/38_fashion_sport_std_black_orange.jpg?v=1758688996', '2025-09-24 12:43:16'),
(27, 38, 'Fashion Sport STD', 'uploads/products/colors/38_fashion_sport_std.webp?v=1758688996', '2025-09-24 12:43:16'),
(28, 38, 'Beat 110 Premium 2', 'uploads/products/colors/38_beat_110_premium_2.jpg?v=1758688996', '2025-09-24 12:43:16'),
(29, 38, 'Honda Beat Limited Edition', 'uploads/products/colors/38_honda_beat_limited_edition.webp?v=1758688996', '2025-09-24 12:43:16'),
(30, 39, 'White', 'uploads/products/colors/39_white.webp?v=1758689161', '2025-09-24 12:46:01'),
(31, 39, 'Red', 'uploads/products/colors/39_red.webp?v=1758689161', '2025-09-24 12:46:01'),
(32, 39, 'Gray', 'uploads/products/colors/39_gray.webp?v=1758689161', '2025-09-24 12:46:01'),
(33, 39, 'Blue White', 'uploads/products/colors/39_blue_white.webp?v=1758689161', '2025-09-24 12:46:01'),
(34, 40, 'Black', 'uploads/products/colors/40_black.webp?v=1758689276', '2025-09-24 12:47:56'),
(35, 40, 'Orange', 'uploads/products/colors/40_orange.webp?v=1758689276', '2025-09-24 12:47:56'),
(36, 40, 'White', 'uploads/products/colors/40_white.webp?v=1758689276', '2025-09-24 12:47:56'),
(37, 40, 'Khaki', 'uploads/products/colors/40_khaki.webp?v=1758689276', '2025-09-24 12:47:56'),
(38, 41, 'Black Red', 'uploads/products/colors/41_black_red.jpg?v=1758689364', '2025-09-24 12:49:24'),
(39, 41, 'Black Blue', 'uploads/products/colors/41_black_blue.webp?v=1758689364', '2025-09-24 12:49:24'),
(40, 41, 'Black', 'uploads/products/colors/41_black.jpg?v=1758689364', '2025-09-24 12:49:24'),
(41, 42, 'Black', 'uploads/products/colors/42_black.jpg?v=1758689445', '2025-09-24 12:50:45'),
(42, 42, 'Red', 'uploads/products/colors/42_red.webp?v=1758689445', '2025-09-24 12:50:45'),
(43, 43, 'Black', 'uploads/products/colors/43_black.webp?v=1758689529', '2025-09-24 12:52:09'),
(44, 44, 'Red', 'uploads/products/colors/44_red.jpg?v=1758689721', '2025-09-24 12:55:21'),
(45, 44, 'White', 'uploads/products/colors/44_white.webp?v=1758689721', '2025-09-24 12:55:21'),
(46, 44, 'Black', 'uploads/products/colors/44_black.webp?v=1758689721', '2025-09-24 12:55:21'),
(47, 45, 'Black', 'uploads/products/colors/45_black.jpg?v=1758689859', '2025-09-24 12:57:39'),
(48, 45, 'White', 'uploads/products/colors/45_white.webp?v=1758689859', '2025-09-24 12:57:39'),
(49, 45, 'Red', 'uploads/products/colors/45_red.jpg?v=1758689859', '2025-09-24 12:57:39'),
(50, 46, 'White', 'uploads/products/colors/46_white.webp?v=1758689984', '2025-09-24 12:59:44'),
(51, 46, 'Black', 'uploads/products/colors/46_black.webp?v=1758689984', '2025-09-24 12:59:44'),
(52, 47, 'Blue', 'uploads/products/colors/47_blue.jpg?v=1758690346', '2025-09-24 13:05:46'),
(53, 47, 'Black', 'uploads/products/colors/47_black.jpg?v=1758690346', '2025-09-24 13:05:46'),
(54, 47, 'Red', 'uploads/products/colors/47_red.webp?v=1758690346', '2025-09-24 13:05:46'),
(55, 48, 'Red', 'uploads/products/colors/48_red.webp?v=1758690717', '2025-09-24 13:11:57'),
(56, 48, 'Black', 'uploads/products/colors/48_black.webp?v=1758690717', '2025-09-24 13:11:57'),
(57, 49, 'Red', 'uploads/products/colors/49_red.webp?v=1758690801', '2025-09-24 13:13:21'),
(58, 49, 'Black', 'uploads/products/colors/49_black.webp?v=1758690801', '2025-09-24 13:13:21'),
(59, 51, 'Red Black', 'uploads/products/colors/51_red_black.webp?v=1758690887', '2025-09-24 13:14:47'),
(60, 51, 'Black', 'uploads/products/colors/51_black.webp?v=1758690887', '2025-09-24 13:14:47'),
(61, 52, 'Red Black', 'uploads/products/colors/52_red_black.webp?v=1758690969', '2025-09-24 13:16:09'),
(62, 52, 'Black White', 'uploads/products/colors/52_black_white.webp?v=1758690969', '2025-09-24 13:16:09'),
(63, 69, 'Red Black', 'uploads/products/colors/69_red_black.webp?v=1760365507', '2025-10-13 22:25:07'),
(64, 69, 'Black Orange', 'uploads/products/colors/69_black_orange.webp?v=1760365507', '2025-10-13 22:25:07');

-- --------------------------------------------------------

--
-- Table structure for table `product_compatibility`
--

CREATE TABLE `product_compatibility` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `model_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_compatibility`
--

INSERT INTO `product_compatibility` (`id`, `product_id`, `model_name`) VALUES
(1, 69, 'Click 125i');

-- --------------------------------------------------------

--
-- Table structure for table `product_list`
--

CREATE TABLE `product_list` (
  `id` int(30) NOT NULL,
  `brand_id` int(30) NOT NULL,
  `category_id` int(30) NOT NULL,
  `name` text NOT NULL,
  `models` text NOT NULL,
  `available_colors` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `price` float NOT NULL DEFAULT 0,
  `abc_category` enum('A','B','C') DEFAULT 'C',
  `reorder_point` int(11) DEFAULT 0,
  `max_stock` int(11) DEFAULT 0,
  `min_stock` int(11) DEFAULT 0,
  `unit_cost` decimal(10,2) DEFAULT 0.00,
  `supplier_id` int(11) DEFAULT NULL,
  `lead_time_days` int(11) DEFAULT 7,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `image_path` text NOT NULL,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_list`
--
INSERT INTO `product_list` (`id`, `brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`, `date_updated`) VALUES
(11, 9, 10, 'Honda RS125', 'RS 125', 'Matte Axis Gray Metallic, Victory Red', '<p><strong>Honda RS125</strong></p><p>The New RS125, designed too dominate the road with its bold, fresh look and enhanced racing image. This powerful ride combines performance and style, making it a true street leader and standout choice. With two aggressive color variants available - Matte Axis Gray Metallic and Victory Red</p>', 75000, 'B', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/11.png?v=1755083959', 1, '2025-08-13 19:19:19', '2025-10-10 13:31:08'),
(12, 9, 10, 'Honda Scoopy Slant', 'Honda', NULL, '&lt;p&gt;&quot;Compact, stylish, and fuel-efficient, the Honda Scoopy Slant is perfect for urban riders. With its modern design, comfortable seating, and reliable engine, it delivers a smooth and fun riding experience every day.&quot;&lt;/p&gt;', 72500, 'C', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/12.png?v=1755096446', 1, '2025-08-13 22:47:26', '2025-09-24 13:26:05'),
(13, 9, 10, 'Honda ADV', 'Honda', NULL, '&lt;p&gt;&quot;Adventure-ready and versatile, the Honda ADV combines rugged style with powerful performance. Ideal for city streets and off-road journeys, it features a responsive engine, comfortable ergonomics, and advanced suspension for a smooth ride anywhere.&quot;&lt;/p&gt;', 150000, 'C', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/13.png?v=1755096549', 1, '2025-08-13 22:49:09', '2025-09-24 13:25:02'),
(14, 9, 10, 'Honda Dio', 'DIO', 'Red, White, Black, Blue', '<p><strong>Honda Dio</strong></p><p>This scooter delivers exceptional power and performance perfectly fits for your commuting with Stylish looks, aesthetic design, and functional features compact in one scooter.</p>', 66500, 'C', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/14.png?v=1755096602', 1, '2025-08-13 22:50:02', '2025-10-10 13:31:08'),
(15, 9, 10, 'Honda Air Blade', 'Honda', NULL, '&lt;p data-start=&quot;58&quot; data-end=&quot;321&quot;&gt;&lt;em data-start=&quot;80&quot; data-end=&quot;319&quot;&gt;&quot;Sleek, modern, and performance-driven, the Honda Air Blade offers a smooth and powerful ride for urban commuters. With its advanced engine technology, sporty design, and comfortable ergonomics, it&rsquo;s built for both style and efficiency.&quot;&lt;/em&gt;&lt;/p&gt;', 95000, 'C', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/15.png?v=1755096659', 1, '2025-08-13 22:50:59', '2025-09-24 13:25:37'),
(16, 9, 15, 'Honda Genuine Coolant Oil', 'Honda', NULL, '&lt;p&gt;&quot;Keep your motorcycle running smoothly with Honda Genuine Coolant Oil. Engineered to maintain optimal temperature, prevent corrosion, and protect your engine for long-lasting performance.&quot;&lt;/p&gt;', 650, 'C', 20, 100, 10, '0.00', NULL, 7, 1, 'uploads/products/16.png?v=1755096734', 1, '2025-08-13 22:52:14', '2025-10-10 16:03:12'),
(17, 9, 15, 'Honda Scooter Fully Synthetic Oil', 'Honda', NULL, '&lt;p&gt;&quot;Premium fully synthetic engine oil specially formulated for Honda scooters. Ensures maximum engine protection, smooth performance, and extended engine life even under heavy riding conditions.&quot;&lt;/p&gt;', 450, 'C', 20, 100, 10, '0.00', NULL, 7, 1, 'uploads/products/17.png?v=1755096820', 1, '2025-08-13 22:53:40', '2025-10-10 16:03:18'),
(18, 9, 15, 'Honda Scooter Gear Oil', 'Honda', NULL, '&lt;p&gt;&quot;High-quality gear oil designed for Honda scooters, providing smooth gear shifts, reducing wear and tear, and ensuring long-lasting transmission performance under all riding conditions.&quot;&lt;/p&gt;', 380, 'C', 20, 100, 10, '0.00', NULL, 7, 1, 'uploads/products/18.png?v=1755096880', 1, '2025-08-13 22:54:40', '2025-10-10 16:03:23'),
(19, 9, 13, 'Honda Bearing Click', 'Honda', NULL, '&lt;p&gt;&quot;Precision-engineered bearing for Honda motorcycles, ensuring smooth rotation, reduced friction, and reliable performance. Perfect for maintaining your bike&rsquo;s handling and longevity.&quot;&lt;/p&gt;', 320, 'C', 20, 100, 10, '0.00', NULL, 7, 1, 'uploads/products/19.png?v=1755096974', 1, '2025-08-13 22:56:14', '2025-10-10 16:03:28'),
(20, 9, 13, 'Honda Click Air Filter', 'Honda', NULL, '&lt;p&gt;&quot;High-quality air filter designed for Honda Click scooters. Ensures clean airflow to the engine, improves performance, and extends engine life by keeping dust and debris out.&quot;&lt;/p&gt;', 250, 'C', 20, 100, 10, '0.00', NULL, 7, 1, 'uploads/products/20.png?v=1755097056', 1, '2025-08-13 22:57:36', '2025-10-10 16:03:33'),
(21, 9, 13, 'Honda Scooter Belt Drive', 'Honda', NULL, '&lt;p&gt;&quot;Durable and high-performance drive belt for Honda scooters, engineered to provide smooth power transfer, reduce slippage, and ensure reliable acceleration for daily rides.&quot;&lt;/p&gt;', 900, 'C', 20, 100, 10, '0.00', NULL, 7, 1, 'uploads/products/21.png?v=1755097150', 1, '2025-08-13 22:59:10', '2025-10-10 16:03:38'),
(22, 9, 13, 'Honda Scooter Crankshaft', 'Honda', NULL, '&lt;p&gt;&quot;Precision-engineered crankshaft for Honda scooters, designed to ensure smooth engine rotation, optimal power delivery, and long-lasting durability for reliable performance.&quot;&lt;/p&gt;', 2500, 'C', 10, 50, 5, '0.00', NULL, 7, 1, 'uploads/products/22.png?v=1755097229', 1, '2025-08-13 23:00:29', '2025-10-10 16:03:50'),
(23, 9, 10, 'Honda RS125', 'RS 125', 'Matte Axis Gray Metallic, Victory Red', '<p><strong>Honda RS125</strong></p><p>The New RS125, designed too dominate the road with its bold, fresh look and enhanced racing image. This powerful ride combines performance and style, making it a true street leader and standout choice. With two aggressive color variants available - Matte Axis Gray Metallic and Victory Red</p>', 75000, 'B', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/23.png?v=1755097338', 1, '2025-08-13 23:02:18', '2025-10-10 13:31:08'),
(24, 9, 10, 'Honda Wave RSX (DISC)', 'WAVE RSX (DISC)', 'Red, White, Black', '<p><strong>Honda Wave RSX (DISC)</strong></p><p>The Wave RSX turns your riding experience into something remarkable. With its newest sporty dynamic design bringing out impressive stickers, functional features providing convenience, plus fuel efficiency upto 69.5 km/l powered by PGM-FI, this underbone lets you stands out wherever you go.</p>', 62500, 'C', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/24.png?v=1755097428', 1, '2025-08-13 23:03:48', '2025-10-10 13:31:08'),
(25, 9, 10, 'Honda PCX 160 ABS', 'PCX 160 ABS', 'Red, White, Black, Blue', '<p><strong>Honda PCX 160 ABS</strong></p><p>The Honda PCX 160 ABS is a premium maxi-scooter that blends elegant design, advanced technology, and powerful performance—perfect for both daily city rides and longer journeys. Equipped with a 157cc liquid-cooled, fuel-injected engine with eSP+ technology, it delivers smooth acceleration, impressive fuel efficiency, and a refined riding experience.</p><p>This model features Anti-Lock Braking System (ABS) and Honda Selectable Torque Control (HSTC) for enhanced safety and stability, especially on slippery roads. Its sleek LED headlight and taillight, fully digital instrument panel, and modern, aerodynamic body give it a premium and stylish appeal.</p>', 140000, 'A', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/25.png?v=1755097524', 1, '2025-08-13 23:05:24', '2025-10-10 13:31:08'),
(26, 9, 10, 'Honda Click 125i', 'CLICK 125i', 'Red, White, Black, Blue', '<p><strong>Honda Click 125i</strong></p><p>The New CLICK125 SE is powered by a 125cc Liquid-cooled, PGM-FI engine with Enhanced Smart Power and an ACG starter, making the model fuel efficient at 53 km/L. The model comes with the Combi Brake System and Park Brake Lock for added safety features.</p>', 85000, 'B', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/26.png?v=1755097625', 1, '2025-08-13 23:07:05', '2025-10-10 13:31:08'),
(27, 9, 10, 'Honda Beat', 'Honda', NULL, '&lt;p&gt;&quot;Compact, fuel-efficient, and reliable, the Honda Beat is perfect for daily commuting. Its lightweight frame, smooth engine, and comfortable ergonomics make it an ideal choice for city riders.&quot;&lt;/p&gt;', 60500, 'C', 1, 20, 7, '0.00', NULL, 7, 1, 'uploads/products/27.png?v=1755097690', 1, '2025-08-13 23:08:10', '2025-09-24 13:27:12'),
(33, 9, 10, 'ADV 160', 'ADV 160', 'Red, White, Black', '&lt;table&gt;\r\n&lt;thead&gt;\r\n&lt;tr&gt;\r\n&lt;th&gt;Category&lt;/th&gt;\r\n&lt;th&gt;Specification&lt;/th&gt;\r\n&lt;/tr&gt;\r\n&lt;/thead&gt;\r\n&lt;tbody&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;&lt;strong&gt;Engine &amp;amp; Powertrain&lt;/strong&gt;&lt;/td&gt;\r\n&lt;td&gt;&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Engine type&lt;/td&gt;\r\n&lt;td&gt;Single-cylinder, 4-stroke, 4-valve, SOHC, liquid-cooled, eSP+ (&lt;a href=&quot;https://www.topgear.com.ph/moto-sapiens/motorcycle-news/honda-adv-160-2023-ph-launch-a4354-20221014?utm_source=chatgpt.com&quot; title=&quot;Honda ADV 160 2023 unveiled in PH: Prices, Specs, Features&quot;&gt;https://www.topgear.com.ph&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Displacement&lt;/td&gt;\r\n&lt;td&gt;~157 cc (&lt;a href=&quot;https://www.topgear.com.ph/moto-sapiens/motorcycle-news/honda-adv-160-2023-ph-launch-a4354-20221014?utm_source=chatgpt.com&quot; title=&quot;Honda ADV 160 2023 unveiled in PH: Prices, Specs, Features&quot;&gt;https://www.topgear.com.ph&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Bore &times; Stroke&lt;/td&gt;\r\n&lt;td&gt;60.0 mm &times; 55.5 mm (&lt;a href=&quot;https://slmmotor.com/product/honda-adv-160/?utm_source=chatgpt.com&quot; title=&quot;HONDA ADV 160 - SLM Motorport&quot;&gt;SLM Motor&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Compression ratio&lt;/td&gt;\r\n&lt;td&gt;12.0 : 1 (&lt;a href=&quot;https://slmmotor.com/product/honda-adv-160/?utm_source=chatgpt.com&quot; title=&quot;HONDA ADV 160 - SLM Motorport&quot;&gt;SLM Motor&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Max power&lt;/td&gt;\r\n&lt;td&gt;~ &lt;strong&gt;11.8 kW&lt;/strong&gt; @ 8,500 rpm (&asymp; 16 PS) (&lt;a href=&quot;https://www.topgear.com.ph/moto-sapiens/motorcycle-news/honda-adv-160-2023-ph-launch-a4354-20221014?utm_source=chatgpt.com&quot; title=&quot;Honda ADV 160 2023 unveiled in PH: Prices, Specs, Features&quot;&gt;https://www.topgear.com.ph&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Max torque&lt;/td&gt;\r\n&lt;td&gt;~ &lt;strong&gt;14.7 Nm&lt;/strong&gt; @ 6,500 rpm (&lt;a href=&quot;https://www.topgear.com.ph/moto-sapiens/motorcycle-news/honda-adv-160-2023-ph-launch-a4354-20221014?utm_source=chatgpt.com&quot; title=&quot;Honda ADV 160 2023 unveiled in PH: Prices, Specs, Features&quot;&gt;https://www.topgear.com.ph&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Fuel system&lt;/td&gt;\r\n&lt;td&gt;PGM-FI (Programmed Fuel Injection) (&lt;a href=&quot;https://premiumbikes.ph/product/honda-adv160ap/?utm_source=chatgpt.com&quot; title=&quot;Honda ADV160AP - Premium Adventure Scooter | Premiumbikes&quot;&gt;Premiumbikes&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Ignition system&lt;/td&gt;\r\n&lt;td&gt;Full transistorized (&lt;a href=&quot;https://motorlandia.com.ph/motorcycle/honda-adv-160/?utm_source=chatgpt.com&quot; title=&quot;HONDA ADV 160 &ndash; Motorlandia&quot;&gt;Motorlandia&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Starter&lt;/td&gt;\r\n&lt;td&gt;Electric (ACG starter) (&lt;a href=&quot;https://motorlandia.com.ph/motorcycle/honda-adv-160/?utm_source=chatgpt.com&quot; title=&quot;HONDA ADV 160 &ndash; Motorlandia&quot;&gt;Motorlandia&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;/tbody&gt;&lt;/table&gt;', 166900, 'B', 3, 66, 1, '9999.00', NULL, 7, 1, 'uploads/products/33.webp?v=1758619970', 0, '2025-09-23 17:32:50', '2025-10-12 18:34:28'),
(34, 9, 10, 'Click 160', 'Click 160', 'Silver Black, Red Black, Black Orange', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;156.9cc liquid-cooled, 4-valve SOHC eSP+, ABS, Smart Key, Full LED&lt;/span&gt;&lt;/p&gt;', 122900, 'C', 3, 200, 1, '99.00', NULL, 14, 1, 'uploads/products/34.webp?v=1758688171', 0, '2025-09-24 12:29:31', '2025-09-25 10:48:29'),
(35, 9, 10, 'TMX 125 ALPHA', 'TMX 125 ALPHA', 'Black, Red, Gray', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;124.9cc air-cooled, 4-speed manual, Workhorse underbone, Drum Brakes&lt;/span&gt;&lt;/p&gt;', 68900, 'A', 20, 300, 1, '8000.00', NULL, 21, 1, 'uploads/products/35.jpg?v=1758688317', 0, '2025-09-24 12:31:57', '2025-10-12 18:34:28'),
(36, 9, 10, 'XRM 125 Dual Sport Fi', 'XRM 125 Dual Sport Fi', 'Yellow, Green, Blue, Red', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;125cc air-cooled, SOHC, 4-speed semi-auto, On/Off-road utility&lt;/span&gt;&lt;/p&gt;', 77900, 'A', 30, 100, 10, '777.00', NULL, 7, 1, 'uploads/products/36.webp?v=1758688526', 0, '2025-09-24 12:35:26', '2025-10-12 18:34:28'),
(37, 9, 10, 'Airblade 160', 'Airblade 160', 'Red, Gray, Dark Blue', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;156.9cc liquid-cooled, 4-valve SOHC eSP+, ABS, Smart Key, Traction Control&lt;/span&gt;&lt;/p&gt;', 149900, 'C', 30, 100, 10, '300.00', NULL, 14, 1, 'uploads/products/37.jpg?v=1758688624', 0, '2025-09-24 12:37:04', '2025-09-25 10:47:29'),
(38, 9, 10, 'Beat', 'Beat', 'Street STD Black, Street STD Gray, Black Premium, White Premium, Playful Yellow, Playful Red, Playful Gray, Playful Blue, Fashion Sport STD Black Red, Fashion Sport STD Black Orange, Fashion Sport STD, Beat 110 Premium 2, Honda Beat Limited Edition', '&lt;table&gt;\r\n&lt;thead&gt;\r\n&lt;tr&gt;\r\n&lt;th&gt;Category&lt;/th&gt;\r\n&lt;th&gt;Specification&lt;/th&gt;\r\n&lt;/tr&gt;\r\n&lt;/thead&gt;\r\n&lt;tbody&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;&lt;strong&gt;Engine &amp;amp; Powertrain&lt;/strong&gt;&lt;/td&gt;\r\n&lt;td&gt;&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Engine type&lt;/td&gt;\r\n&lt;td&gt;4-Stroke, SOHC, Air-Cooled, Enhanced Smart Power (eSP) (&lt;a href=&quot;https://www.hondaph.com/motor/beat-playful?utm_source=chatgpt.com&quot; title=&quot;BeAT (Playful) | Honda PH&quot;&gt;Honda PH&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Displacement&lt;/td&gt;\r\n&lt;td&gt;110 cc (&lt;a href=&quot;https://www.hondaph.com/motor/beat-playful?utm_source=chatgpt.com&quot; title=&quot;BeAT (Playful) | Honda PH&quot;&gt;Honda PH&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Bore &times; Stroke&lt;/td&gt;\r\n&lt;td&gt;47.0 &times; 63.1 mm (&lt;a href=&quot;https://www.hondaph.com/motor/beat-playful?utm_source=chatgpt.com&quot; title=&quot;BeAT (Playful) | Honda PH&quot;&gt;Honda PH&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Compression ratio&lt;/td&gt;\r\n&lt;td&gt;10.0 : 1 (&lt;a href=&quot;https://www.hondaph.com/motor/beat-playful?utm_source=chatgpt.com&quot; title=&quot;BeAT (Playful) | Honda PH&quot;&gt;Honda PH&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Maximum Power&lt;/td&gt;\r\n&lt;td&gt;6.63 kW @ 7,500 rpm (&asymp; 8.89 hp) (&lt;a href=&quot;https://www.topgear.com.ph/moto-sapiens/motorcycle-news/honda-beat-2024-launch-ph-a4354-20230923?utm_source=chatgpt.com&quot; title=&quot;Honda Beat 2024 unveiled in PH: Prices, Specs, Features&quot;&gt;https://www.topgear.com.ph&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Maximum Torque&lt;/td&gt;\r\n&lt;td&gt;9.30 Nm @ 6,000 rpm (&lt;a href=&quot;https://www.hondaph.com/motor/beat-playful?utm_source=chatgpt.com&quot; title=&quot;BeAT (Playful) | Honda PH&quot;&gt;Honda PH&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Fuel system&lt;/td&gt;\r\n&lt;td&gt;PGM-Fi (fuel injection) (&lt;a href=&quot;https://www.hondaph.com/motor/beat-playful?utm_source=chatgpt.com&quot; title=&quot;BeAT (Playful) | Honda PH&quot;&gt;Honda PH&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Starting system&lt;/td&gt;\r\n&lt;td&gt;Electric &amp;amp; Kick (ACG Starter) (&lt;a href=&quot;https://www.hondaph.com/motor/beat-playful?utm_source=chatgpt.com&quot; title=&quot;BeAT (Playful) | Honda PH&quot;&gt;Honda PH&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;/tbody&gt;&lt;/table&gt;', 98989, 'A', 50, 500, 100, '98000.00', NULL, 28, 1, 'uploads/products/38.jpg?v=1758688996', 0, '2025-09-24 12:43:16', '2025-09-25 10:36:57'),
(39, 9, 10, 'Click 125i SE', 'Click 125i SE', 'White, Red, Gray, Blue White', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;123cc liquid-cooled, 2-valve SOHC eSP, CBS, ACG Starter, Idling Stop&lt;/span&gt;&lt;/p&gt;', 78900, 'A', 20, 300, 10, '9000.00', NULL, 7, 1, 'uploads/products/39.webp?v=1758689161', 0, '2025-09-24 12:46:01', '2025-10-12 18:34:28'),
(40, 9, 10, 'Giorno+', 'Giorno+', 'Black, Orange, White, Khaki', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 44444, 'C', 20, 300, 5, '1000.00', NULL, 7, 1, 'uploads/products/40.webp?v=1758689276', 0, '2025-09-24 12:47:56', '2025-09-24 16:11:23'),
(41, 9, 10, 'RS 125', 'RS 125', 'Black Red, Black Blue, Black', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 88888, 'C', 30, 200, 10, '5000.00', NULL, 7, 1, 'uploads/products/41.jpg?v=1758689364', 0, '2025-09-24 12:49:24', '2025-09-24 16:11:23'),
(42, 9, 10, 'Supra GTR 150', 'Supra GTR 150', 'Black, Red', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 76152, 'A', 20, 100, 5, '9999.00', NULL, 7, 1, 'uploads/products/42.webp?v=1758689445', 0, '2025-09-24 12:50:45', '2025-10-12 18:34:28'),
(43, 9, 10, 'Wave RSX (DISC) ', 'Wave RSX(DISC)', 'Black', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;110cc air-cooled, 4-speed semi-auto, Front Disc Brake, Economy-focused&lt;/span&gt;&lt;/p&gt;', 64400, 'C', 3, 10, 5, '8989.00', NULL, 7, 1, 'uploads/products/43.webp?v=1758689529', 0, '2025-09-24 12:52:09', '2025-09-25 10:49:08'),
(44, 9, 10, 'PCX 160 CBS', 'PCX 160 CBS', 'Red, White, Black', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 131900, 'C', 5, 30, 1, '777.00', NULL, 7, 1, 'uploads/products/44.jpg?v=1758689721', 0, '2025-09-24 12:55:21', '2025-09-25 10:45:03'),
(45, 9, 10, 'PCX 160 ABS', 'PCX 160 ABS', 'Black, White, Red', '&lt;p&gt;&lt;br&gt;&lt;span class=&quot;&quot; data-state=&quot;closed&quot;&gt;&lt;/span&gt;&lt;/p&gt;', 149900, 'B', 5, 20, 1, '99.00', NULL, 7, 1, 'uploads/products/45.jpg?v=1758689859', 0, '2025-09-24 12:57:39', '2025-10-12 18:34:28'),
(46, 9, 10, 'PCX 150', 'PCX 150', 'White, Black', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 87766, 'A', 3, 10, 1, '88.00', NULL, 7, 1, 'uploads/products/46.webp?v=1758689984', 0, '2025-09-24 12:59:44', '2025-10-12 18:34:28'),
(47, 9, 10, 'Airblade 150', 'Airblade 150', 'Blue, Black, Red', '&lt;p&gt;&lt;b&gt;149.3cc liquid-cooled, 2-valve SOHC, CBS, Smart Key, LED Headlight&lt;/b&gt;&lt;/p&gt;', 119900, 'C', 3, 20, 1, '89.00', NULL, 7, 1, 'uploads/products/47.webp?v=1758690346', 0, '2025-09-24 13:05:46', '2025-09-25 10:46:57'),
(48, 9, 10, 'XR 150i', 'XR 150i', 'Red, Black', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 87123, 'C', 6, 10, 1, '9987.00', NULL, 7, 1, 'uploads/products/48.webp?v=1758690717', 0, '2025-09-24 13:11:57', '2025-09-25 10:35:01'),
(49, 9, 10, 'TMX SUPREMO', 'TMX SUPREMO', 'Red, Black', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 45345, 'C', 5, 10, 1, '787.00', NULL, 7, 1, 'uploads/products/49.webp?v=1758690801', 0, '2025-09-24 13:13:21', '2025-09-24 16:11:23'),
(51, 9, 10, 'DIO', 'DIO', 'Red Black, Black', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;110cc air-cooled, CVT, CBS, Practical and fuel-efficient&lt;/span&gt;&lt;/p&gt;', 70900, 'C', 15, 20, 10, '989.00', NULL, 21, 1, 'uploads/products/51.webp?v=1758690887', 0, '2025-09-24 13:14:47', '2025-09-25 10:50:30'),
(52, 9, 10, 'CRF150L', 'CRF150L', 'Red Black, Black White', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;149.15cc air-cooled, 5-speed manual, Enduro/Trail bike, Electric Start&lt;/span&gt;&lt;/p&gt;', 133000, 'A', 5, 10, 1, '8776.00', NULL, 7, 1, 'uploads/products/52.webp?v=1758690969', 0, '2025-09-24 13:16:09', '2025-10-12 18:34:28'),
(53, 9, 10, 'HONDA CLICK 125i', 'CLICK 125i', 'Red, White, Black, Blue', '<p><strong>HONDA CLICK 125i</strong></p><p>The New CLICK125 SE is powered by a 125cc Liquid-cooled, PGM-FI engine with Enhanced Smart Power and an ACG starter, making the model fuel efficient at 53 km/L. The model comes with the Combi Brake System and Park Brake Lock for added safety features.</p>', 75000, 'B', 5, 20, 2, '65000.00', NULL, 7, 1, 'uploads/products/click_125i.png', 1, '2025-10-10 10:15:52', '2025-10-10 10:19:41'),
(54, 9, 10, 'HONDA CLICK 125i', 'CLICK 125i', 'Red, White, Black, Blue', '<p><strong>HONDA CLICK 125i</strong></p><p>The New CLICK125 SE is powered by a 125cc Liquid-cooled, PGM-FI engine with Enhanced Smart Power and an ACG starter, making the model fuel efficient at 53 km/L. The model comes with the Combi Brake System and Park Brake Lock for added safety features.</p>', 75000, 'B', 5, 20, 2, '65000.00', NULL, 7, 1, 'uploads/products/click_125i.png', 1, '2025-10-10 10:16:18', '2025-10-10 10:19:32'),
(55, 9, 10, 'Honda Click 160', 'CLICK 160', 'Red, White, Black, Blue', '<p><strong>Honda Click 160</strong></p><p>The New CLICK160, now featuring a bold, dynamic, and aggressive stripe design that demands attention on the road and ensures you stand out with its innovative aesthetics.</p>', 95000, 'B', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/honda_click_160.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:12'),
(56, 9, 10, 'Honda CRF150L', 'CRF150L', 'Red, White', '<p><strong>Honda CRF150L</strong></p><p>Break your limitations and explore the world through The New CRF150L. Combined with powerful 149cc 4-Stroke, 2 Valves, SOHC, Air-cooled, PGM-Fi engine, advanced features such as digital meter panel, plus Showa brand Inverted Front Fork and Pro-Link Rear Suspension, and a lower Seat Height (863 mm) suitable for Filipino market. This motorcycle gives an excellent fuel efficiency up to 45.5 km/L, so you\'re sure to go further than your daily ride.</p>', 120000, 'A', 3, 15, 1, '0.00', NULL, 7, 1, 'uploads/products/honda_crf150l.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:31'),
(57, 9, 10, 'Honda PCX 150', 'PCX 150', 'Red, White, Black, Blue', '<p><strong>Honda PCX 150</strong></p><p>The Honda PCX 150 is a premium, stylish, and fuel-efficient scooter designed for both city commuting and longer rides. Known for its sleek and modern design, it features an aerodynamic body, LED lighting, and a comfortable step-through frame that gives it a sophisticated yet sporty look.</p><p>Powered by a 149cc liquid-cooled, fuel-injected engine, the PCX 150 delivers smooth acceleration and reliable performance while maintaining excellent fuel economy. Its smart key system, digital LCD display, and ample under-seat storage make it both convenient and practical for daily use.</p>', 135000, 'A', 3, 15, 1, '0.00', NULL, 7, 1, 'uploads/products/honda_pcx150.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:40'),
(58, 9, 10, 'Honda ADV 160 CBS', 'ADV 160 CBS', 'Red, White, Black', '<p><strong>Honda ADV 160 CBS</strong></p><p>Bringing elegance and superiority to the next level, PCX160 lets Filipino riders to stand out on the road and ride with pride with its all-new premium and elegant design, improved driving performance with comfortable and spacious riding, and the latest technology and security features.</p>', 155000, 'A', 3, 15, 1, '0.00', NULL, 7, 1, 'uploads/products/honda_adv160_cbs.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:02:24'),
(59, 9, 10, 'ADV 160 ABS', 'ADV 160', 'Red, White, Black', '&lt;p&gt;&lt;strong&gt;Honda ADV 160 ABS&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;The ADV160 is now equipped with a new generation 157cc, 4-Valve, Liquid-Cooled, eSP+ Engine, offering advanced technology with 4-valve mechanism and low friction technologies to provide excellent power output and environmental performance (Fuel Efficient). It delivers a maximum power of 11.8 kW @ 8,500 rpm and a top torque of 14.7 Nm @ 6,500 rpm, which proves more than enough for a reliable ride that takes you from daily commuting to leisure trips.&lt;/p&gt;', 165000, 'A', 3, 15, 1, '0.00', NULL, 7, 1, 'uploads/products/honda_adv160_abs.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:21'),
(60, 9, 10, 'Honda Supra GTR 150', 'SUPRA GTR 150', 'Red, White, Black', '<p><strong>Honda Supra GTR 150</strong></p><p>Honda Supra GTR150 is equipped with a 6-Speed DOHC 4-Valve Liquid-Cooled Engine for maximum performance, great handling, and better fuel efficiency of 42 km/liter when riding in highways. It also has a LED Headlight that ensures safety and clear sight on the road, as well as a Full Digital Meter Panel for ease of information in determining speed and distance.</p>', 110000, 'A', 3, 15, 1, '0.00', NULL, 7, 1, 'uploads/products/honda_supra_gtr150.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:49'),
(61, 9, 10, 'Honda Giorno', 'GIORNO', 'Red, White, Black, Blue', '<p><strong>Honda Giorno</strong></p><p>The All-New Giorno+ is designed adapted to fashion-forward Filipino customers that perfectly blends modern classic design with exceptional performance and innovative features with its 125cc, 4-Valve, Liquid-Cooled, eSP+ Engine, making it perfect fit for those who value both style and substance. Setting a new standard for high-performance scooters with its impressive curves and advanced technology making every ride into #ClassThatLast.</p>', 90000, 'B', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/honda_giorno.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:36'),
(62, 9, 10, 'Honda PCX 160 CBS', 'PCX 160 CBS', 'Red, White, Black, Blue', '<p><strong>Honda PCX 160 CBS</strong></p><p>The Honda PCX 160 CBS is a stylish and practical maxi-scooter designed for smooth and comfortable urban commuting. Powered by a 157cc liquid-cooled, fuel-injected engine with Honda\'s eSP+ technology, it delivers efficient performance and a refined riding experience.</p><p>Equipped with Combi Brake System (CBS), it automatically distributes braking force between the front and rear wheels for balanced stopping power and added safety. Its LED headlight and taillight, digital instrument panel, and elegant aerodynamic design give it a premium and modern look.</p>', 130000, 'A', 3, 15, 1, '0.00', NULL, 7, 1, 'uploads/products/honda_pcx160_cbs.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:44'),
(63, 9, 10, 'Honda Click 125 SE', 'CLICK 125 SE', 'Red, White, Black, Blue', '<p><strong>Honda Click 125 SE</strong></p><p>The New Click125 showcasing a fresh design featuring striking new two-tone colors and dynamic stripes for the Click125 Standard Variant, while complemented by a sophisticated 3D Emblem exclusive to Special Edition Variant.</p>', 87000, 'B', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/honda_click125_se.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:04'),
(64, 9, 10, 'Honda TMX Alpha', 'TMX ALPHA', 'Red, White, Black', '<p><strong>Honda TMX Alpha</strong></p><p>TMX125 Alpha is powered by the legendary Overhead Valve (OHV) engine, making it unique from other motorbikes. This OHV engine uses a push rod to balance acceleration and control for hours of easy and hassle-free operations while being fuel-efficient at 62.5km/L at 45Km/H constant speed. And to meet the customers\' requirement for best balance of engine power and acceleration, the rear sprocket is improved from 44T to 38T, making it perfect bike for daily commuting usage.</p>', 78000, 'B', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/honda_tmx_alpha.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:55'),
(65, 9, 10, 'Honda TMX Supremo', 'TMX SUPREMO', 'Red, White, Black', '<p><strong>Honda TMX Supremo</strong></p><p>The 3rd Generation TMX Supremo now boasts of enhanced features, such as its new and improved engine that maintains its fuel efficiency at 62km/L. It also comes with 18-inch tires, as well as a high ground clearance and a seat height that ensures the riders\' comfort despite the impact of rough roads. This makes the 3rd Generation TMX Supremo better suited for heavy-duty rides and climbs on demanding roads, even when carrying loads.</p>', 95000, 'B', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/honda_tmx_supremo.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:59'),
(66, 9, 10, 'Honda Wave RSX Drum', 'WAVE RSX DRUM', 'Red, White, Black', '<p><strong>Honda Wave RSX Drum</strong></p><p>The Wave RSX turns your riding experience into something remarkable. With its newest sporty dynamic design bringing out impressive stickers, functional features providing convenience, plus fuel efficiency upto 69.5 km/l powered by PGM-FI, this underbone lets you stands out wherever you go.</p>', 60000, 'C', 5, 20, 2, '0.00', NULL, 7, 1, 'uploads/products/honda_wave_rsx_drum.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:05:03'),
(67, 9, 10, 'Honda Winner X Premium', 'WINNER X PREMIUM', 'Red, White, Black', '<p><strong>Honda Winner X Premium</strong></p><p>It boasts outstanding performance through its 150cc, DOHC, 6-Speed, Liquid-Cooled Engine along with worthwhile features: USB Charging Port, Smart Key System, All LED Lighting System, Digital Meter Panel, Bank Angle Sensor, Assist & Slipper Clutch, Colored Cast Wheel, and Anti-Lock Braking System available in ABS Racing and ABS Premium variants only.</p>', 180000, 'A', 2, 10, 1, '0.00', NULL, 7, 1, 'uploads/products/honda_winner_x_premium.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:03:02'),
(68, 9, 10, 'Honda Winner X Standard', 'WINNER X STANDARD', 'Red, White, Black', '<p><strong>Honda Winner X Standard</strong></p><p>The All-New Winner X that is designed to let you #RideLikeAChampion is now here! This sports cub is sure to become one of another favorite among Filipino riders with its aggressive sports styling, powerful engine and advanced features.</p>', 160000, 'A', 2, 10, 1, '0.00', NULL, 7, 1, 'uploads/products/honda_winner_x_standard.png', 1, '2025-10-10 13:31:09', '2025-10-10 16:03:45'),
(0, 9, 10, 'ADV 161', 'ADV 160', 'Red, White, Black', '&lt;p&gt;tesing for database&lt;/p&gt;', 99999, 'C', 0, 0, 0, '0.00', NULL, 7, 1, 'uploads/products/0.jpg?v=1760246867', 0, '2025-10-11 22:27:47', '2025-10-12 18:34:28');

-- --------------------------------------------------------

--
-- Table structure for table `product_notifications`
--

CREATE TABLE `product_notifications` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `notified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_notifications`
--

INSERT INTO `product_notifications` (`id`, `product_id`, `user_id`, `is_active`, `created_at`, `notified_at`) VALUES
(1, 34, 8, 1, '2025-10-13 10:58:33', NULL),
(2, 37, 8, 1, '2025-10-13 21:54:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_recommendations`
--

CREATE TABLE `product_recommendations` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `recommended_product_id` int(11) NOT NULL,
  `recommendation_type` enum('SUBSTITUTE','COMPLEMENTARY','UPGRADE','CROSS_SELL') NOT NULL,
  `priority` int(11) DEFAULT 1,
  `reason` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_recommendations`
--

INSERT INTO `product_recommendations` (`id`, `product_id`, `recommended_product_id`, `recommendation_type`, `priority`, `reason`, `date_created`) VALUES
(1, 1, 3, 'COMPLEMENTARY', 1, 'Oil is often needed with crash guard installation', '2025-08-15 16:56:13'),
(2, 3, 4, 'SUBSTITUTE', 2, 'Alternative oil brand', '2025-08-15 16:56:13'),
(3, 4, 1, 'CROSS_SELL', 1, 'Crash guard often purchased with new tires', '2025-08-15 16:56:13');

-- --------------------------------------------------------

--
-- Table structure for table `promo_images`
--

CREATE TABLE `promo_images` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(500) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `date_created` datetime DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promo_images`
--

INSERT INTO `promo_images` (`id`, `title`, `description`, `image_path`, `is_active`, `display_order`, `date_created`, `date_updated`) VALUES
(2, 'Free 50K worth of Freebies and Benefits', 'The New TMX 125', 'uploads/promos/1760065200_553657371_1265226732070677_7630709761580500268_n.jpg', 1, 0, '2025-10-10 11:00:10', '2025-10-10 11:00:10'),
(3, 'Free 50K worth of Freebies and Benefits', 'The New Click125', 'uploads/promos/1760065320_554101419_1265226635404020_7100055049140153524_n.jpg', 1, 0, '2025-10-10 11:02:25', '2025-10-10 11:02:25'),
(4, 'Free 50K worth of Freebies and Benefits', 'The All-New ADV 160 Discover New Excitement', 'uploads/promos/1760065320_553544945_1265226555404028_2183418737660854968_n.jpg', 1, 0, '2025-10-10 11:02:25', '2025-10-10 11:31:10');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `receipt_number` varchar(50) NOT NULL,
  `customer_id` int(30) NOT NULL,
  `amount_paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','card','bank_transfer','check') NOT NULL DEFAULT 'cash',
  `payment_reference` varchar(100) DEFAULT NULL COMMENT 'Transaction reference, check number, etc.',
  `received_by` int(30) NOT NULL COMMENT 'Staff who received payment',
  `issued_at` datetime NOT NULL DEFAULT current_timestamp(),
  `acknowledgment_note` text DEFAULT 'Thank you for your purchase at Star Honda Calamba!',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`id`, `invoice_id`, `receipt_number`, `customer_id`, `amount_paid`, `payment_method`, `payment_reference`, `received_by`, `issued_at`, `acknowledgment_note`, `notes`) VALUES
(1, 1, 'RCPT-2025-0001', 8, 167888.00, 'cash', '', 9, '2025-10-10 08:25:02', 'Thank you for your purchase at Star Honda Calamba! We appreciate your business and look forward to serving you again.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `request_meta`
--

CREATE TABLE `request_meta` (
  `request_id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_meta`
--

INSERT INTO `request_meta` (`request_id`, `meta_field`, `meta_value`) VALUES
(7, 'vehicle_type', 'Scooter'),
(7, 'vehicle_name', 'Honda Click'),
(7, 'vehicle_registration_number', '123456789'),
(7, 'vehicle_model', 'Honda'),
(7, 'service_id', '7'),
(7, 'pickup_address', ''),
(8, 'vehicle_type', 'Scooter'),
(8, 'vehicle_name', 'Honda Click'),
(8, 'vehicle_registration_number', '98765'),
(8, 'vehicle_model', 'Honda'),
(8, 'service_id', '8'),
(8, 'pickup_address', ''),
(9, 'vehicle_type', 'Scooter'),
(9, 'vehicle_name', 'Honda Click'),
(9, 'vehicle_registration_number', '1234'),
(9, 'vehicle_model', 'click'),
(9, 'service_id', '7'),
(9, 'pickup_address', ''),
(10, 'vehicle_type', 'Motorcycle'),
(10, 'vehicle_name', 'Honda Click 125i'),
(10, 'vehicle_registration_number', 'ABC 1234'),
(10, 'vehicle_model', '2023'),
(10, 'service_id', '7'),
(10, 'pickup_address', ''),
(11, 'vehicle_type', 'Motorcycle'),
(11, 'vehicle_name', 'Honda Click 125i'),
(11, 'vehicle_registration_number', 'ABC 1234'),
(11, 'vehicle_model', 'Honda'),
(11, 'service_id', '6'),
(11, 'pickup_address', ''),
(12, 'vehicle_type', 'Motorcycle'),
(12, 'vehicle_name', 'Honda Click 125i'),
(12, 'vehicle_registration_number', 'AB123CD'),
(12, 'vehicle_model', 'Honda Click 160'),
(12, 'service_id', '6'),
(12, 'pickup_address', ''),
(16, 'vehicle_type', 'Scooter'),
(16, 'vehicle_name', 'Honda Beat'),
(16, 'vehicle_registration_number', 'LMN123'),
(16, 'vehicle_model', 'Honda Beat'),
(16, 'service_id', '7,5,6,8'),
(16, 'pickup_address', ''),
(14, 'vehicle_type', 'Scooter'),
(14, 'vehicle_name', 'Honda Click'),
(14, 'vehicle_registration_number', 'HIJ123'),
(14, 'vehicle_model', 'Honda'),
(14, 'service_id', '6'),
(14, 'pickup_address', ''),
(13, 'vehicle_type', 'Motorcycle'),
(13, 'vehicle_name', 'Honda Click 125i'),
(13, 'vehicle_registration_number', 'EFD321'),
(13, 'vehicle_model', 'Honda'),
(13, 'service_id', '8'),
(13, 'pickup_address', ''),
(17, 'vehicle_type', 'Scooter'),
(17, 'vehicle_name', 'Honda Beat'),
(17, 'vehicle_registration_number', 'QRS123'),
(17, 'vehicle_model', 'Honda Beat'),
(17, 'service_id', '5,6,8'),
(17, 'pickup_address', ''),
(19, 'service_id', '204'),
(19, 'pickup_address', ''),
(20, 'service_id', '70'),
(20, 'pickup_address', ''),
(6, 'service_id', '5'),
(6, 'pickup_address', ''),
(15, 'service_id', '5'),
(15, 'pickup_address', ''),
(21, 'service_id', '32,99,70,53,120,187,7,31,98,165,39,173,121'),
(21, 'pickup_address', ''),
(22, 'service_id', '66'),
(22, 'vehicle_info', 'click 125 electrical wiring '),
(22, 'service_description', 'sira ang electrical wiring'),
(22, 'pickup_address', '');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `target_type` enum('product','service','dealership','order') NOT NULL,
  `target_id` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `target_type`, `target_id`, `rating`, `comment`, `date_created`, `date_updated`) VALUES
(1, 6, 'product', 13, 5, '', '2025-09-18 19:25:31', NULL),
(2, 8, 'product', 38, 5, 'mabilis, muntik na ko sumemplang', '2025-09-25 08:16:11', '2025-09-25 08:41:35'),
(3, 8, 'product', 33, 5, 'mabilis, muntik na ko sumemplang', '2025-09-25 08:41:17', '2025-09-25 08:56:13');

-- --------------------------------------------------------

--
-- Table structure for table `service_list`
--

CREATE TABLE `service_list` (
  `id` int(30) NOT NULL,
  `service` text NOT NULL,
  `description` text NOT NULL,
  `estimated_minutes` decimal(6,2) DEFAULT NULL COMMENT 'Estimated completion time in minutes',
  `estimated_hours` decimal(4,2) DEFAULT NULL COMMENT 'Estimated completion time in hours',
  `service_amount` decimal(10,2) DEFAULT NULL COMMENT 'Service price in Philippine Pesos',
  `service_type` varchar(100) DEFAULT NULL COMMENT 'Type of service (e.g., TUNE UP, ADJUSTMENT)',
  `min_minutes` decimal(6,2) DEFAULT NULL COMMENT 'Minimum estimated minutes',
  `max_minutes` decimal(6,2) DEFAULT NULL COMMENT 'Maximum estimated minutes',
  `min_hours` decimal(4,2) DEFAULT NULL COMMENT 'Minimum estimated hours',
  `max_hours` decimal(4,2) DEFAULT NULL COMMENT 'Maximum estimated hours',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_list`
--

INSERT INTO `service_list` (`id`, `service`, `description`, `estimated_minutes`, `estimated_hours`, `service_amount`, `service_type`, `min_minutes`, `max_minutes`, `min_hours`, `max_hours`, `status`, `delete_flag`, `date_created`) VALUES
(5, 'Brake System Check & Replacement', '&lt;p&gt;Inspection of brake pads, discs, fluid levels, and overall brake performance. Includes replacement of worn-out components and brake fluid flushing if necessary.&lt;/p&gt;', '90.00', '1.00', '650.00', 'LIGHT DIAGNOSING TROUBLESHOOTING & REPAIR', '60.00', NULL, NULL, NULL, 1, 0, '2025-08-07 22:48:48'),
(6, 'Chain and Sprocket Maintenance', '&lt;p&gt;Cleaning, lubricating, adjusting, or replacing the motorcycle chain and sprockets to prevent wear, reduce noise, and ensure smooth power transfer.&lt;/p&gt;', '120.00', '0.80', '265.00', 'REPLACE / REPAIR / CLEAN PARTS', '48.00', NULL, NULL, NULL, 1, 0, '2025-08-07 22:49:51'),
(7, 'Battery Check & Replacement', '&lt;p&gt;Testing battery health, terminals, and voltage. Replacement of weak or dead batteries to ensure reliable engine starts and electrical functions.&lt;/p&gt;', '60.00', '1.00', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2025-08-07 22:50:10'),
(8, 'Spark Plug Replacement', '&lt;p&gt;Removing old or worn spark plugs and installing new ones to ensure smooth engine ignition and combustion.&lt;/p&gt;', '30.00', NULL, '50.00', 'ADJUSTMENT', '0.00', NULL, NULL, NULL, 1, 0, '2025-08-07 22:50:33'),
(9, 'Minor Tune Up', 'Complete minor tune-up service including basic adjustments and checks', '90.00', '1.30', '455.00', 'TUNE UP', '78.00', NULL, '78.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(10, 'Major Tune Up', 'Comprehensive major tune-up service with detailed engine adjustments', '90.00', '3.00', '950.00', 'TUNE UP', '180.00', NULL, '99.99', NULL, 1, 0, '2025-10-10 14:02:15'),
(11, 'Minor Tune Up (Change Oil & Tune Up)', 'Minor tune-up combined with oil change service', '90.00', '1.30', '455.00', 'CHANGE OIL & TUNE UP', '78.00', NULL, '78.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(12, 'Top Overhaul', 'Complete top engine overhaul service', NULL, '5.00', '1505.00', 'ADJUSTMENT', '300.00', NULL, '99.99', NULL, 1, 0, '2025-10-10 14:02:15'),
(13, 'Engine Overhaul', 'Full engine overhaul service', NULL, '5.00', '1505.00', 'ADJUSTMENT', '300.00', NULL, '99.99', NULL, 1, 0, '2025-10-10 14:02:15'),
(14, 'Carburetor Idle Adjustment', 'Carburetor idle speed adjustment and tuning', NULL, '0.25', '50.00', 'ADJUSTMENT', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(15, 'Spark Plug Adjustment', 'Spark plug gap adjustment and replacement', '30.00', '0.50', '50.00', 'ADJUSTMENT', NULL, NULL, '0.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(16, 'Shaft Drive Adjustment', 'Shaft drive system adjustment and alignment', NULL, NULL, '50.00', 'ADJUSTMENT', '0.00', NULL, '0.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(17, 'Sprocket (Rear) Replacement', 'Rear sprocket replacement and installation', '120.00', '0.25', '50.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(18, 'Fork Assembly (Rear) Replacement', 'Rear fork assembly replacement and installation', NULL, '0.33', '120.00', 'REPLACE / REPAIR / CLEAN PARTS', '20.00', NULL, '20.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(19, 'Oil Pump Replacement', 'Oil pump replacement and installation', NULL, '0.20', '45.00', 'REPLACE / REPAIR / CLEAN PARTS', '12.00', NULL, '12.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(20, 'Carburetor Replacement', 'Carburetor replacement and installation', NULL, '0.75', '305.00', 'REPLACE / REPAIR / CLEAN PARTS', '45.00', NULL, '45.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(21, 'Starter Motor Replacement', 'Starter motor replacement and installation', NULL, '0.25', '150.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(22, 'Drive Chain / Sprocket Replacement', 'Drive chain and sprocket replacement', '120.00', '2.00', '265.00', 'REPLACE / REPAIR / CLEAN PARTS', NULL, NULL, '48.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(23, 'Oil Seal (Crankshaft Bottom) Replacement', 'Crankshaft bottom oil seal replacement', NULL, '0.75', '300.00', 'REPLACE / REPAIR / CLEAN PARTS', '45.00', NULL, '45.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(24, 'Starter Idle Item Replacement', 'Starter idle component replacement', NULL, '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(25, 'Ignition Switch Replacement', 'Ignition switch replacement and installation', NULL, '0.83', '280.00', 'REPLACE / REPAIR / CLEAN PARTS', '50.00', NULL, '50.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(26, 'Brake Rear Panel Replacement', 'Rear brake panel replacement', '90.00', '0.75', '300.00', 'REPLACE / REPAIR / CLEAN PARTS', '45.00', NULL, '45.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(27, 'Seal Oil Pump Cleaner Replacement', 'Oil pump seal cleaner replacement', NULL, '0.75', '300.00', 'REPLACE / REPAIR / CLEAN PARTS', '45.00', NULL, '45.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(28, 'Valve (IN/EX) Replacement', 'Intake and exhaust valve replacement', NULL, '3.00', '950.00', 'REPLACE / REPAIR / CLEAN PARTS', '180.00', NULL, '99.99', NULL, 1, 0, '2025-10-10 14:02:15'),
(29, 'Gasket (Cylinder) Replacement', 'Cylinder gasket replacement', NULL, '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(30, 'Cover Pulsate Right Replacement', 'Right pulsate cover replacement', NULL, '1.00', '155.00', 'REPLACE / REPAIR / CLEAN PARTS', '60.00', NULL, '60.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(31, 'Bearing Axle Shaft Replacement', 'Axle shaft bearing replacement', NULL, '1.50', '650.00', 'REPLACE / REPAIR / CLEAN PARTS', '90.00', NULL, '90.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(32, 'Arm Brake (Stand Side) Replacement', 'Stand side brake arm replacement', '90.00', '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(33, 'Gear Starter Idle Replacement', 'Starter idle gear replacement', NULL, '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(34, 'Shaft Idle Replacement', 'Idle shaft replacement', NULL, '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(35, 'Disc Clutch Friction Replacement', 'Clutch friction disc replacement', NULL, '1.50', '650.00', 'REPLACE / REPAIR / CLEAN PARTS', '90.00', NULL, '90.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(36, 'Cover Gearcase Left Rear Replacement', 'Left rear gearcase cover replacement', NULL, '0.50', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '30.00', NULL, '30.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(37, 'Cover Crankcase Replacement', 'Crankcase cover replacement', NULL, '1.50', '650.00', 'REPLACE / REPAIR / CLEAN PARTS', '90.00', NULL, '90.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(38, 'Carrier Luggage Replacement', 'Luggage carrier replacement', NULL, '1.50', '650.00', 'REPLACE / REPAIR / CLEAN PARTS', '90.00', NULL, '90.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(39, 'Bearing Idle Shaft Replacement', 'Idle shaft bearing replacement', NULL, '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(40, 'Spring Starter Base Replacement', 'Starter base spring replacement', NULL, '0.50', '45.00', 'REPLACE / REPAIR / CLEAN PARTS', '30.00', NULL, '30.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(41, 'Switch Gear Change Replacement', 'Gear change switch replacement', NULL, '1.50', '650.00', 'REPLACE / REPAIR / CLEAN PARTS', '90.00', NULL, '90.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(42, 'Cylinder Front Brake Replacement', 'Front brake cylinder replacement', '90.00', '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(43, 'Cylinder Front Brake Master Replacement', 'Front brake master cylinder replacement', '90.00', '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(44, 'Bulb Headlight Replacement', 'Headlight bulb replacement', NULL, '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(45, 'Bulb Taillight Replacement', 'Taillight bulb replacement', NULL, '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(46, 'Spring Decomp Cam Replacement', 'Decompression cam spring replacement', NULL, '3.00', '950.00', 'REPLACE / REPAIR / CLEAN PARTS', '180.00', NULL, '99.99', NULL, 1, 0, '2025-10-10 14:02:15'),
(47, 'Case Meter Lower Replacement', 'Lower meter case replacement', NULL, '0.75', '300.00', 'REPLACE / REPAIR / CLEAN PARTS', '45.00', NULL, '45.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(48, 'Cable Throttle Replacement', 'Throttle cable replacement', NULL, '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(49, 'Cable Clutch Replacement', 'Clutch cable replacement', NULL, '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(50, 'Switch Starter Replacement', 'Starter switch replacement', NULL, '0.50', '180.00', 'REPLACE / REPAIR / CLEAN PARTS', '30.00', NULL, '30.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(51, 'Cap Spark Plug Replacement', 'Spark plug cap replacement', '30.00', '0.25', '45.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(52, 'Switch Clutch Replacement', 'Clutch switch replacement', NULL, '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(53, 'Base Stator Replacement', 'Stator base replacement', NULL, '0.75', '200.00', 'REPLACE / REPAIR / CLEAN PARTS', '45.00', NULL, '45.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(54, 'Bracket Handle Lever Left Replacement', 'Left handle lever bracket replacement', NULL, '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(55, 'Solenoid Assembly Replacement', 'Solenoid assembly replacement', NULL, '1.00', '300.00', 'REPLACE / REPAIR / CLEAN PARTS', '60.00', NULL, '60.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(56, 'Flasher Replacement', 'Flasher unit replacement', NULL, '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(57, 'Modified Steering Handle Replacement', 'Modified steering handle replacement', NULL, '0.75', '300.00', 'REPLACE / REPAIR / CLEAN PARTS', '45.00', NULL, '45.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(58, 'Brake Rear Drum Replacement', 'Rear brake drum replacement', '90.00', '0.75', '300.00', 'REPLACE / REPAIR / CLEAN PARTS', '45.00', NULL, '45.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(59, 'Key Set Replacement', 'Key set replacement and programming', NULL, NULL, '0.00', 'REPLACE / REPAIR / CLEAN PARTS', '0.00', NULL, '0.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(60, 'Switch Ignition and Lock Replacement', 'Ignition switch and lock replacement', NULL, '0.75', '300.00', 'REPLACE / REPAIR / CLEAN PARTS', '45.00', NULL, '45.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(61, 'Switch Combination and Lock Replacement', 'Combination switch and lock replacement', NULL, '0.75', '300.00', 'REPLACE / REPAIR / CLEAN PARTS', '45.00', NULL, '45.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(62, 'Bridge Fork Top Replacement', 'Top fork bridge replacement', NULL, '0.25', '100.00', 'REPLACE / REPAIR / CLEAN PARTS', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(63, 'General Repair', 'General motorcycle repair and troubleshooting', NULL, '1.00', '650.00', 'LIGHT DIAGNOSING TROUBLESHOOTING & REPAIR', '60.00', NULL, '60.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(64, 'Front/Rear Brakes Repair', 'Front and rear brake system repair', '90.00', '60.00', '650.00', 'LIGHT DIAGNOSING TROUBLESHOOTING & REPAIR', NULL, NULL, '60.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(65, 'Electrical Wiring Repair', 'Electrical wiring system repair and troubleshooting', NULL, '1.00', '650.00', 'LIGHT DIAGNOSING TROUBLESHOOTING & REPAIR', '60.00', NULL, '60.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(66, 'Electrical Wiring Lubrication', 'Electrical wiring lubrication service', NULL, '60.00', '650.00', 'LUBRICATE', NULL, NULL, '60.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(67, 'Cable Throttle Lubrication', 'Throttle cable lubrication service', NULL, '0.25', '105.00', 'LUBRICATE', '15.00', NULL, '15.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(68, 'Carburetor Idle Lubrication', 'Carburetor idle lubrication service', NULL, '1.00', '205.00', 'LUBRICATE', '60.00', NULL, '60.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(69, 'Cock Assembly (Fuel) Overhaul', 'Fuel cock assembly top overhaul', NULL, '0.33', '130.00', 'TOP OVERHAUL', '20.00', NULL, '20.00', NULL, 1, 0, '2025-10-10 14:02:15'),
(70, 'Arm Valve Exhaust Overhaul', 'Exhaust valve arm top overhaul', NULL, '2.00', '1105.00', 'TOP OVERHAUL', '120.00', NULL, '99.99', NULL, 1, 0, '2025-10-10 14:02:15'),
(71, 'Sprocket (Cam Chain) Overhaul', 'Cam chain sprocket top overhaul', '120.00', '3.00', '1200.00', 'TOP OVERHAUL', '180.00', NULL, '99.99', NULL, 1, 0, '2025-10-10 14:02:15'),
(72, 'Sprocket (Cam) Overhaul', 'Cam sprocket top overhaul', '120.00', '3.00', '950.00', 'TOP OVERHAUL', '180.00', NULL, '99.99', NULL, 1, 0, '2025-10-10 14:02:15'),
(73, 'Cylinder Overhaul', 'Cylinder top overhaul service', NULL, '3.00', '1200.00', 'TOP OVERHAUL', '180.00', NULL, '99.99', NULL, 1, 0, '2025-10-10 14:02:15'),
(74, 'Valve Spring and/or Stem Seal Overhaul', 'Valve spring and stem seal top overhaul', NULL, '3.00', '1200.00', 'TOP OVERHAUL', '180.00', NULL, '99.99', NULL, 1, 0, '2025-10-10 14:02:15'),
(75, 'Tensioner (Cam Chain) Replacement', 'Cam chain tensioner replacement', '120.00', '3.00', '1200.00', 'REPLACE / REPAIR / CLEAN PARTS', '180.00', NULL, '99.99', NULL, 1, 0, '2025-10-10 14:02:15');
-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `id` int(30) NOT NULL,
  `client_id` int(30) NOT NULL,
  `vehicle_type` varchar(100) DEFAULT NULL,
  `service_type` text NOT NULL,
  `vehicle_name` varchar(100) DEFAULT NULL,
  `vehicle_registration_number` varchar(20) DEFAULT NULL,
  `vehicle_model` varchar(100) DEFAULT NULL,
  `mechanic_id` int(30) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_requests`
--

INSERT INTO `service_requests` (`id`, `client_id`, `vehicle_type`, `service_type`, `vehicle_name`, `vehicle_registration_number`, `vehicle_model`, `mechanic_id`, `status`, `date_created`) VALUES
(6, 2, 'Scooter', 'Drop Off', 'Honda Click', '123', 'Honda', 10, 3, '2025-04-24 08:28:14'),
(7, 4, NULL, 'Drop Off', NULL, NULL, NULL, NULL, 0, '2025-08-13 14:12:36'),
(8, 4, NULL, 'Drop Off', NULL, NULL, NULL, NULL, 1, '2025-08-13 20:37:41'),
(9, 5, NULL, 'Drop Off', NULL, NULL, NULL, NULL, 1, '2025-08-14 16:48:10'),
(10, 6, NULL, 'Drop Off', NULL, NULL, NULL, NULL, 3, '2025-08-15 12:11:22'),
(11, 2, NULL, 'Drop Off', NULL, NULL, NULL, NULL, 2, '2025-08-15 12:56:09'),
(12, 6, NULL, 'Drop Off', NULL, NULL, NULL, 8, 4, '2025-08-15 14:09:35'),
(13, 6, NULL, 'Drop Off', NULL, NULL, NULL, 10, 1, '2025-09-18 19:38:50'),
(14, 8, NULL, 'Drop Off', NULL, NULL, NULL, 9, 3, '2025-09-24 22:33:45'),
(15, 8, NULL, '', NULL, NULL, NULL, 7, 0, '2025-09-24 22:38:39'),
(16, 8, NULL, '', NULL, NULL, NULL, 10, 2, '2025-09-25 08:21:58'),
(17, 9, NULL, '', NULL, NULL, NULL, 8, 1, '2025-09-25 10:18:51'),
(19, 8, 'Scooter', '', 'Honda Click', 'ABC123', 'Honda', 10, 2, '2025-10-11 08:06:31'),
(20, 8, 'Motorcycle', '', 'Honda Click 125', 'ABC123', 'Click 125', 7, 1, '2025-10-11 09:07:32');

-- --------------------------------------------------------

--
-- Table structure for table `stock_list`
--

CREATE TABLE `stock_list` (
  `id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `quantity` float NOT NULL DEFAULT 0,
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1= IN, 2= Out',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_list`
--

INSERT INTO `stock_list` (`id`, `product_id`, `quantity`, `type`, `date_created`) VALUES
(10, 13, 100, 1, '2025-08-14 17:03:29'),
(12, 15, 20, 1, '2025-08-15 11:56:05'),
(14, 19, 100, 1, '2025-08-15 11:56:26'),
(15, 27, 1100, 1, '2025-08-15 11:56:37'),
(16, 26, 20, 1, '2025-08-15 11:56:47'),
(17, 20, 200, 1, '2025-08-15 11:56:57'),
(18, 14, 30, 1, '2025-08-15 11:57:07'),
(19, 16, 100, 1, '2025-08-15 11:57:17'),
(20, 11, 10, 1, '2025-08-15 11:57:30'),
(21, 25, 1000, 1, '2025-08-15 11:58:06'),
(23, 23, 100, 1, '2025-08-15 11:58:33'),
(24, 12, 20, 1, '2025-08-15 11:58:44'),
(25, 21, 80, 1, '2025-08-15 11:58:58'),
(26, 22, 100, 1, '2025-08-15 11:59:09'),
(27, 17, 250, 1, '2025-08-15 11:59:22'),
(28, 18, 199, 1, '2025-08-15 11:59:40'),
(30, 24, 11, 1, '2025-08-15 12:00:02'),
(31, 33, 99, 1, '2025-09-23 18:14:04'),
(32, 38, 400, 1, '2025-09-24 13:18:41'),
(33, 34, 0, 1, '2025-09-24 13:19:25'),
(34, 40, 99, 1, '2025-09-24 13:19:35'),
(35, 51, 99, 1, '2025-09-24 13:19:47'),
(36, 52, 99, 1, '2025-09-24 13:19:57'),
(37, 47, 40, 1, '2025-09-24 13:20:16'),
(38, 25, 99, 1, '2025-09-24 13:20:35'),
(39, 37, 0, 1, '2025-09-24 13:20:48'),
(40, 46, 50, 1, '2025-09-24 13:21:03'),
(41, 45, 99, 1, '2025-09-24 13:21:12'),
(42, 44, 3, 1, '2025-09-24 13:21:32'),
(43, 41, 99, 1, '2025-09-24 13:21:43'),
(44, 42, 99, 1, '2025-09-24 13:21:53'),
(45, 35, 99, 1, '2025-09-24 13:22:06'),
(46, 49, 88, 1, '2025-09-24 13:22:13'),
(47, 43, 98, 1, '2025-09-24 13:22:27'),
(48, 48, 99, 1, '2025-09-24 13:22:37'),
(49, 23, 989, 1, '2025-09-24 13:22:47'),
(50, 36, 87, 1, '2025-09-24 13:22:59'),
(51, 39, 100, 1, '2025-09-24 13:23:20'),
(52, 39, 1, 1, '2025-09-24 22:07:21'),
(53, 37, 0, 1, '2025-10-11 11:50:06'),
(54, 34, 12, 1, '2025-10-13 20:56:25');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `movement_type` enum('IN','OUT','ADJUSTMENT','RETURN','DAMAGE') NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `previous_stock` decimal(10,2) NOT NULL,
  `new_stock` decimal(10,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `reference_id` varchar(50) DEFAULT NULL,
  `reference_type` enum('ORDER','PURCHASE','ADJUSTMENT','RETURN','DAMAGE') DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `movement_type`, `quantity`, `previous_stock`, `new_stock`, `reason`, `reference_id`, `reference_type`, `date_created`, `created_by`) VALUES
(1, 33, 'IN', 99.00, 0.00, 99.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-23 18:14:04', NULL),
(2, 38, 'IN', 1000.00, 0.00, 1000.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:18:41', NULL),
(3, 34, 'IN', 99.00, 0.00, 99.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:19:25', NULL),
(4, 40, 'IN', 99.00, 0.00, 99.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:19:35', NULL),
(5, 51, 'IN', 99.00, 0.00, 99.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:19:47', NULL),
(6, 52, 'IN', 99.00, 0.00, 99.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:19:57', NULL),
(7, 47, 'IN', 991.00, 0.00, 991.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:20:16', NULL),
(8, 25, 'IN', 99.00, 1000.00, 1099.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:20:35', NULL),
(9, 37, 'IN', 99.00, 0.00, 99.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:20:48', NULL),
(10, 46, 'IN', 909.00, 0.00, 909.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:21:03', NULL),
(11, 45, 'IN', 99.00, 0.00, 99.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:21:12', NULL),
(12, 44, 'IN', 99.00, 0.00, 99.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:21:32', NULL),
(13, 41, 'IN', 99.00, 0.00, 99.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:21:43', NULL),
(14, 42, 'IN', 99.00, 0.00, 99.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:21:53', NULL),
(15, 35, 'IN', 99.00, 0.00, 99.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:22:06', NULL),
(16, 49, 'IN', 88.00, 0.00, 88.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:22:13', NULL),
(17, 43, 'IN', 98.00, 0.00, 98.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:22:27', NULL),
(18, 48, 'IN', 99.00, 0.00, 99.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:22:37', NULL),
(19, 23, 'IN', 989.00, 100.00, 1089.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:22:47', NULL),
(20, 36, 'IN', 87.00, 0.00, 87.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:22:59', NULL),
(21, 39, 'IN', 991.00, 0.00, 991.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 13:23:20', NULL),
(22, 39, 'IN', 1.00, 991.00, 992.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-24 22:07:21', NULL),
(23, 39, 'ADJUSTMENT', -691.00, 992.00, 301.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-09-24 22:07:37', NULL),
(24, 39, 'ADJUSTMENT', -200.00, 301.00, 101.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-09-24 22:07:59', NULL),
(25, 38, 'ADJUSTMENT', -900.00, 1000.00, 100.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-09-25 08:11:19', NULL),
(26, 38, 'ADJUSTMENT', 200.00, 100.00, 300.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-09-25 08:11:40', NULL),
(27, 38, 'ADJUSTMENT', 100.00, 300.00, 400.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-09-25 08:11:51', NULL),
(28, 47, 'ADJUSTMENT', -891.00, 991.00, 100.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-09-25 10:35:56', NULL),
(29, 46, 'ADJUSTMENT', -809.00, 909.00, 100.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-09-25 10:36:11', NULL),
(30, 47, 'ADJUSTMENT', -50.00, 100.00, 50.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-09-25 10:36:40', NULL),
(31, 16, 'ADJUSTMENT', -200.00, 300.00, 100.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-09-25 10:37:35', NULL),
(32, 47, 'ADJUSTMENT', -10.00, 50.00, 40.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-09-25 10:37:56', NULL),
(33, 44, 'ADJUSTMENT', -96.00, 99.00, 3.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-09-25 10:38:24', NULL),
(34, 46, 'ADJUSTMENT', -50.00, 100.00, 50.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-10-01 10:11:17', NULL),
(35, 37, 'ADJUSTMENT', -99.00, 99.00, 0.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-10-10 16:05:19', NULL),
(36, 37, 'IN', 11.00, 0.00, 11.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-11 11:50:06', NULL),
(37, 34, 'ADJUSTMENT', -99.00, 99.00, 0.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-10-12 23:08:24', NULL),
(38, 34, 'IN', 12.00, 0.00, 12.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-13 20:56:25', NULL),
(39, 37, 'ADJUSTMENT', -11.00, 11.00, 0.00, 'Stock edit', 'STOCK_EDIT', 'ADJUSTMENT', '2025-10-13 21:54:20', NULL);

-- --------------------------------------------------------

--
--
-- Table structure for table `suppliers`


CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Star Honda Motorcycle Service Management System'),
(6, 'short_name', 'Star Honda Calamba'),
(11, 'logo', 'uploads/1760134800_Logo.png.png'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/1760134800_Gemini_Generated_Image_xhvvl1xhvvl1xhvv.png'),
(15, 'email_notifications', '1'),
(16, 'sms_notifications', '0'),
(17, 'notification_email', 'noreply@example.com'),
(18, 'main_logo', 'uploads/1760056920_Logo.png.png'),
(19, 'secondary_logo', 'uploads/1760056920_384549274_843563040829321_4297563294452634980_n.png'),
(20, 'promo_display_enabled', '1'),
(21, 'customer_images_enabled', '1'),
(22, 'promo_section_title', 'Special Promotions'),
(23, 'customer_section_title', 'Happy Customers'),
(24, 'max_promo_images', '5'),
(25, 'max_customer_images', '8'),
(26, 'promo_titles', 'Array'),
(27, 'promo_descriptions', 'Array'),
(28, 'customer_names', 'Array'),
(29, 'motorcycle_models', 'Array'),
(30, 'purchase_dates', 'Array'),
(31, 'customer_testimonials', 'Array');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `login_attempts` int(11) DEFAULT 0,
  `is_locked` tinyint(4) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `role_type` enum('admin','branch_supervisor','admin_assistant','stock_admin','service_admin','mechanic') DEFAULT 'admin',
  `branch_id` int(11) DEFAULT NULL,
  `permissions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `status`, `date_added`, `date_updated`, `login_attempts`, `is_locked`, `locked_until`, `role_type`, `branch_id`, `permissions`) VALUES
(9, 'Aljay', 'Plantado', 'aljay', 'f0bd1fc09c2cfe760c342571f040eae7', 'uploads/1755062580_ID FINAL.jpg', '2025-10-14 12:13:14', 1, 1, '2025-08-13 13:23:57', '2025-10-14 12:13:14', 0, 0, NULL, 'admin', NULL, NULL),
(10, 'Henry', 'Legaspi', 'henry', '027e4180beedb29744413a7ea6b84a42', 'uploads/1758764880_Fp920gfagAA8IDl.jpeg', '2025-10-11 14:30:22', 1, 1, '2025-09-24 22:28:02', '2025-10-11 14:30:22', 0, 0, NULL, 'admin', NULL, NULL),
(11, 'Euniel', 'Bandian', 'euniel', '1daa37b7282d1de0c3fcd736e3decaf0', 'uploads/1758765000_aiahsuit.jpg', '2025-09-25 10:13:09', 2, 1, '2025-09-24 22:28:06', '2025-10-09 20:26:04', 3, 1, '2025-10-09 20:27:04', 'service_admin', NULL, NULL),
(12, 'Mark', 'Pancho', 'mark', 'ea82410c7a9991816b5eeeebe195e20a', 'uploads/1758764940_GKWvJp6bwAAG1hk.jpeg', '2025-10-09 20:26:43', 2, 1, '2025-09-24 22:28:08', '2025-10-09 20:48:16', 3, 1, '2025-10-09 20:49:16', 'stock_admin', NULL, NULL),
(13, 'Joshua', 'Cansino', 'joshua', 'd1133275ee2118be63a577af759fc052', 'uploads/1758764940_GKWvJbEbMAAUdXJ.jpeg', '2025-09-25 09:51:09', 2, 1, '2025-09-24 22:28:12', '2025-09-25 09:51:09', 0, 0, NULL, 'stock_admin', NULL, NULL),
(14, 'Karen', 'Bautista', 'karen', 'ba952731f97fb058035aa399b1cb3d5c', 'uploads/1758763140_mikhasuit.jpg', '2025-10-02 16:29:26', 2, 1, '2025-09-24 22:28:17', '2025-10-02 16:29:26', 0, 0, NULL, 'service_admin', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `abc_analysis_view`
--
DROP TABLE IF EXISTS `abc_analysis_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `abc_analysis_view`  AS SELECT `p`.`id` AS `id`, `p`.`name` AS `name`, `p`.`abc_category` AS `abc_category`, `p`.`price` AS `price`, `p`.`reorder_point` AS `reorder_point`, `p`.`max_stock` AS `max_stock`, `p`.`min_stock` AS `min_stock`, coalesce(`s`.`total_stock`,0) AS `current_stock`, coalesce(`o`.`total_ordered`,0) AS `total_ordered`, coalesce(`s`.`total_stock`,0) - coalesce(`o`.`total_ordered`,0) AS `available_stock`, CASE WHEN coalesce(`s`.`total_stock`,0) - coalesce(`o`.`total_ordered`,0) <= `p`.`reorder_point` THEN 'LOW_STOCK' WHEN coalesce(`s`.`total_stock`,0) - coalesce(`o`.`total_ordered`,0) >= `p`.`max_stock` THEN 'OVERSTOCK' ELSE 'NORMAL' END AS `stock_status` FROM ((`product_list` `p` left join (select `stock_list`.`product_id` AS `product_id`,sum(`stock_list`.`quantity`) AS `total_stock` from `stock_list` where `stock_list`.`type` = 1 group by `stock_list`.`product_id`) `s` on(`p`.`id` = `s`.`product_id`)) left join (select `oi`.`product_id` AS `product_id`,sum(`oi`.`quantity`) AS `total_ordered` from (`order_items` `oi` join `order_list` `ol` on(`oi`.`order_id` = `ol`.`id`)) where `ol`.`status` <> 5 group by `oi`.`product_id`) `o` on(`p`.`id` = `o`.`product_id`)) WHERE `p`.`delete_flag` = 0 ;

-- --------------------------------------------------------

--
-- Structure for view `customer_dashboard_view`
--
DROP TABLE IF EXISTS `customer_dashboard_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `customer_dashboard_view`  AS SELECT `c`.`id` AS `client_id`, `c`.`firstname` AS `firstname`, `c`.`lastname` AS `lastname`, `c`.`email` AS `email`, `c`.`account_balance` AS `account_balance`, count(distinct `o`.`id`) AS `total_orders`, count(distinct `s`.`id`) AS `total_services`, count(distinct `a`.`id`) AS `total_appointments`, count(distinct `d`.`id`) AS `total_documents`, (select count(0) from `notifications` where `notifications`.`user_id` = `c`.`id` and `notifications`.`is_read` = 0) AS `unread_notifications` FROM ((((`client_list` `c` left join `order_list` `o` on(`c`.`id` = `o`.`client_id`)) left join `service_requests` `s` on(`c`.`id` = `s`.`client_id`)) left join `appointments` `a` on(`c`.`id` = `a`.`client_id`)) left join `or_cr_documents` `d` on(`c`.`id` = `d`.`client_id`)) WHERE `c`.`delete_flag` = 0 GROUP BY `c`.`id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_appointment_slot` (`appointment_date`,`appointment_time`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `service_type` (`service_type`),
  ADD KEY `mechanic_id` (`mechanic_id`),
  ADD KEY `appointment_date` (`appointment_date`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brand_list`
--
ALTER TABLE `brand_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart_list`
--
ALTER TABLE `cart_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_cart_client_product_color` (`client_id`,`product_id`,`color`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_list`
--
ALTER TABLE `client_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`) USING HASH,
  ADD KEY `idx_credit_application` (`credit_application_completed`);

--
-- Indexes for table `credit_applications`
--
ALTER TABLE `credit_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `date_created` (`date_created`);

--
-- Indexes for table `customer_purchase_images`
--
ALTER TABLE `customer_purchase_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_transactions`
--
ALTER TABLE `customer_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `idx_customer_transactions_client_id` (`client_id`),
  ADD KEY `idx_customer_transactions_date_created` (`date_created`);

--
-- Indexes for table `inventory_alerts`
--
ALTER TABLE `inventory_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `alert_type` (`alert_type`),
  ADD KEY `is_resolved` (`is_resolved`),
  ADD KEY `date_created` (`date_created`);

--
-- Indexes for table `inventory_settings`
--
ALTER TABLE `inventory_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `invoices`
--

SET @new_id = 0;
UPDATE invoices SET id = (@new_id := @new_id + 1) ORDER BY id;


ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `payment_status` (`payment_status`),
  ADD KEY `generated_at` (`generated_at`);





SET @new_id = 0;
UPDATE invoice_items SET id = (@new_id := @new_id + 1) ORDER BY id;
--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `item_type` (`item_type`),
  ADD KEY `item_id` (`item_id`);

SET FOREIGN_KEY_CHECKS = 1;

--
-- Indexes for table `invoice_settings`

ALTER TABLE `invoice_settings`
ADD PRIMARY KEY (`id`);


--
-- Indexes for table `mechanics_list`
--
ALTER TABLE `mechanics_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `motorcycle_specifications`
--
ALTER TABLE `motorcycle_specifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type` (`type`),
  ADD KEY `is_read` (`is_read`),
  ADD KEY `date_created` (`date_created`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_order_items_product` (`product_id`);

--
-- Indexes for table `order_list`
--

-- 1️⃣ Replace invalid or duplicate IDs
-- SET @i := 0;
-- UPDATE order_list
-- SET id = (@i := @i + 1)
-- ORDER BY date_created;

ALTER TABLE `order_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `idx_order_list_client_id` (`client_id`),
  ADD KEY `idx_order_list_status` (`status`);


--
-- Indexes for table `or_cr_documents`
--
ALTER TABLE `or_cr_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `idx_or_cr_documents_client_id` (`client_id`),
  ADD KEY `idx_or_cr_documents_status` (`status`);

--
-- Indexes for table `product_availability_notifications`
--
ALTER TABLE `product_availability_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_notification` (`client_id`,`product_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `is_notified` (`is_notified`);

--
-- Indexes for table `product_color_images`
--
ALTER TABLE `product_color_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `color` (`color`);

--
-- Indexes for table `product_compatibility`
--
ALTER TABLE `product_compatibility`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_model` (`product_id`,`model_name`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_list`
--
ALTER TABLE `product_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_product_list_abc_category` (`abc_category`),
  ADD KEY `idx_product_list_reorder_point` (`reorder_point`);

--
-- Indexes for table `product_notifications`
--
ALTER TABLE `product_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_notification` (`product_id`,`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `product_recommendations`
--
ALTER TABLE `product_recommendations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_recommendation` (`product_id`,`recommended_product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `recommended_product_id` (`recommended_product_id`);

--
-- Indexes for table `promo_images`
--
ALTER TABLE `promo_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_number` (`receipt_number`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `issued_at` (`issued_at`);

--
-- Indexes for table `request_meta`
--
ALTER TABLE `request_meta`
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_target` (`target_type`,`target_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `service_list`
--
ALTER TABLE `service_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `mechanic_id` (`mechanic_id`),
  ADD KEY `idx_service_requests_client_id` (`client_id`),
  ADD KEY `idx_service_requests_status` (`status`);

--
-- Indexes for table `stock_list`
--
ALTER TABLE `stock_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_stock_list_product_type` (`product_id`,`type`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `movement_type` (`movement_type`),
  ADD KEY `date_created` (`date_created`),
  ADD KEY `reference_id` (`reference_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `delete_flag` (`delete_flag`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `client_product` (`client_id`,`product_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brand_list`
--
ALTER TABLE `brand_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `cart_list`
--
ALTER TABLE `cart_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `client_list`
--
ALTER TABLE `client_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `credit_applications`
--
ALTER TABLE `credit_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_purchase_images`
--
ALTER TABLE `customer_purchase_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `customer_transactions`
--
ALTER TABLE `customer_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inventory_alerts`
--
ALTER TABLE `inventory_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `inventory_settings`
--
ALTER TABLE `inventory_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invoice_settings`
--
ALTER TABLE `invoice_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `mechanics_list`
--
ALTER TABLE `mechanics_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `motorcycle_specifications`
--
ALTER TABLE `motorcycle_specifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `order_list`
--
ALTER TABLE `order_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `or_cr_documents`
--
ALTER TABLE `or_cr_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_availability_notifications`
--
ALTER TABLE `product_availability_notifications`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_color_images`
--
ALTER TABLE `product_color_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `product_compatibility`
--
ALTER TABLE `product_compatibility`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_list`
--
ALTER TABLE `product_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `product_notifications`
--
ALTER TABLE `product_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_recommendations`
--
ALTER TABLE `product_recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `promo_images`
--
ALTER TABLE `promo_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `service_list`
--
ALTER TABLE `service_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `stock_list`
--
ALTER TABLE `stock_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_client` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_mechanic` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanics_list` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_ibfk_service` FOREIGN KEY (`service_type`) REFERENCES `service_list` (`id`) ON DELETE NO ACTION;

--
-- Constraints for table `cart_list`
--
ALTER TABLE `cart_list`
  ADD CONSTRAINT `cart_list_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_list_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `credit_applications`
--
ALTER TABLE `credit_applications`
  ADD CONSTRAINT `credit_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_list`
--
ALTER TABLE `order_list`
  ADD CONSTRAINT `order_list_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_availability_notifications`
--
ALTER TABLE `product_availability_notifications`
  ADD CONSTRAINT `product_availability_notifications_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_availability_notifications_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_list`
--
ALTER TABLE `product_list`
  ADD CONSTRAINT `product_list_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brand_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_list_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `request_meta`
--
ALTER TABLE `request_meta`
  ADD CONSTRAINT `request_meta_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD CONSTRAINT `service_requests_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_requests_ibfk_2` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanics_list` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_list`
--
ALTER TABLE `stock_list`
  ADD CONSTRAINT `stock_list_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE;
COMMIT;

----------------------------------------------------------------------------------------------------------------------------------


-- For appointments table
-- For order_items table  
ALTER TABLE `order_items` MODIFY `id` INT AUTO_INCREMENT PRIMARY KEY;
DELETE FROM `order_items` WHERE `id` = 0;

-- For service_requests table
ALTER TABLE `service_requests` MODIFY `id` INT AUTO_INCREMENT PRIMARY KEY;


-- Remove duplicate alert records
DELETE FROM `inventory_alerts` WHERE `id` IN (2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);
-- Keep only the first occurrence of each alert


-- Remove duplicate specification records
DELETE FROM `motorcycle_specifications` WHERE `id` BETWEEN 1 AND 17;
-- Re-insert unique records with proper IDs

UPDATE `customer_transactions` 
SET `transaction_type` = 'adjustment' 
WHERE `transaction_type` = '' OR `transaction_type` IS NULL;




/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
