-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Sep 24, 2025 at 04:04 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `address` text NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brand_list`
--

CREATE TABLE `brand_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `image_path` text NOT NULL,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brand_list`
--

INSERT INTO `brand_list` (`id`, `name`, `image_path`, `delete_flag`, `status`, `date_created`, `date_updated`) VALUES
(9, 'Honda', 'uploads/brands/9.png?v=1755083500', 0, 1, '2025-08-13 19:11:39', '2025-08-13 19:11:40'),
(10, 'Kawasaki', 'uploads/brands/10.png?v=1755091465', 0, 1, '2025-08-13 21:24:25', '2025-08-13 21:24:25'),
(11, 'Suzuki', 'uploads/brands/11.png?v=1755091485', 0, 1, '2025-08-13 21:24:33', '2025-08-13 21:24:45'),
(12, 'Yamaha', 'uploads/brands/12.png?v=1755091497', 0, 1, '2025-08-13 21:24:57', '2025-08-13 21:24:57'),
(13, 'Amahay', '', 1, 1, '2025-08-13 21:36:54', '2025-08-13 21:58:00'),
(14, 'Suzuka', 'uploads/brands/14.png?v=1755093677', 1, 1, '2025-08-13 22:01:17', '2025-08-13 22:01:36'),
(15, '', 'uploads/brands/15.png?v=1755094096', 1, 1, '2025-08-13 22:08:16', '2025-08-13 22:10:08'),
(16, 'Zusuka', 'uploads/brands/16.png?v=1755094204', 1, 1, '2025-08-13 22:10:04', '2025-08-13 22:10:12');

-- --------------------------------------------------------

--
-- Table structure for table `cart_list`
--

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
(38, 8, 7, NULL, 1, '2025-08-15 15:18:34'),
(39, 8, 8, NULL, 1, '2025-08-15 15:18:41'),
(41, 8, 26, NULL, 1, '2025-09-23 18:07:16'),
(42, 8, 33, 'Black', 1, '2025-09-23 18:15:18');

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
(11, 'Tires', 1, 0, '2025-08-08 08:10:00'),
(12, 'Accessories', 1, 0, '2025-08-08 08:10:25'),
(13, 'Motorcycle Parts', 1, 0, '2025-08-08 08:10:40'),
(14, 'Mugs', 1, 0, '2025-08-08 08:10:49'),
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
  `password` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_added` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `last_login` datetime DEFAULT NULL,
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

INSERT INTO `client_list` (`id`, `firstname`, `middlename`, `lastname`, `gender`, `contact`, `address`, `email`, `password`, `status`, `delete_flag`, `date_created`, `date_added`, `last_login`, `login_attempts`, `is_locked`, `locked_until`, `reset_token`, `reset_expires`, `account_balance`, `vehicle_plate_number`, `or_cr_number`, `or_cr_release_date`, `or_cr_status`, `or_cr_file_path`, `vehicle_brand`, `vehicle_model`) VALUES
(2, 'Aiah', '', 'Arceta', 'Female', '09123456789', 'Blk 72 Lot 7 Phase 6 Mabuhay Mamatid Cabuyao Laguna', 'aiah@gmail.com', '6b3251cd488029543402df97cbc20500', 1, 0, '2025-04-23 22:34:17', '2025-09-18 19:28:47', '2025-08-15 12:55:22', 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(3, 'Jhoanna', '', 'Robles', 'Female', '0901262004', 'Blk 8 Lot 88, Mabuhay Mamatid, Cabuyao City, Laguna, 4025', 'jhoanna@gmail.com', '6172961ee1eccc046bd3810138cc68ee', 1, 0, '2025-08-07 22:02:24', '2025-09-18 19:28:47', '2025-08-15 11:04:32', 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(4, 'Aljay', '', 'Plantado', 'Male', '09282346158', 'Blk 72 Lot 7 Phase 6 Mabuhay Mamatid Cabuyao Laguna', 'yuichi192168@gmail.com', 'f0bd1fc09c2cfe760c342571f040eae7', 1, 0, '2025-08-13 13:01:27', '2025-09-18 19:28:47', NULL, 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(5, 'Gojo', '', 'Satoru', 'Male', '09282347890', 'Blk 82 Lot 9 Phase 2 Mabuhay Mamatid Cabuyao Laguna', 'gojo@gmail.com', '383fdfc40a9aff292f5827357acd5f53', 1, 0, '2025-08-13 22:24:02', '2025-09-18 19:28:47', NULL, 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(6, 'Mary', '', 'Clara', 'Female', '09052720021', 'Blk 88 Lot 8 Mabuhay Mamatid Cabuyao Laguna', 'maryclara@gmail.com', 'fe149d3eddaf84487c5687ee6832969d', 1, 0, '2025-08-15 10:43:41', '2025-09-18 19:28:47', '2025-09-18 19:20:19', 0, 0, NULL, NULL, NULL, 0.00, 'ABC 123', 'OR-2025-001234', '2025-08-03', 'pending', NULL, NULL, NULL),
(7, 'Test', NULL, 'User', '', '', '', 'test@example.com', 'cc03e747a6afbbcbf8be7668acfebee5', 1, 1, '2025-08-15 11:02:55', '2025-08-15 12:44:16', '2025-08-15 11:04:21', 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(8, 'Crisostomo', '', 'Vergara', 'Female', '09091320021', 'Blk 8 Lot 99 Mabuhay Mamatid Cabuyao Laguna', 'crisostomovergara@gmail.com', 'cfd1ca6b84fc6360c003e01842457ca6', 1, 0, '2025-08-15 15:17:48', '2025-09-23 17:34:15', '2025-09-23 17:34:15', 0, 0, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL);

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
);

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
(1, 33, 'OVERSTOCK', 99.00, 99.00, 'Overstock alert: ADV 160 has 99 units (Max stock: 99)', 0, NULL, NULL, '2025-09-23 18:14:04');

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
(3, 'Sheena Catacutan', '0905092004', 'sheena@gmail.com', 1, '2025-08-07 22:39:27'),
(4, 'Gwen Apuli', '0906192003', 'gwen@gmail.com', 1, '2025-08-07 22:40:01'),
(7, 'Jan Jan Matanguihan', '09282346151', 'janjanmatanguihan@gmail.com', 1, '2025-09-18 16:44:09'),
(8, 'Aldrin Caldozo', '09065775184', 'aldrincaldozo@gmail.com', 1, '2025-09-18 16:44:53'),
(9, 'Fernando Rimando', '09505639564', 'fernandorimando@gmail.com', 1, '2025-09-18 16:45:36'),
(10, 'Ricardo Montalban', '09286594732', 'ricardomontalban@gmail.com', 1, '2025-09-18 16:46:05');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(25, 13, 7, NULL, 1, '2025-08-15 12:08:18'),
(26, 13, 20, NULL, 1, '2025-08-15 12:08:18'),
(27, 13, 10, NULL, 1, '2025-08-15 12:08:18'),
(28, 14, 25, NULL, 1, '2025-08-15 12:21:54'),
(29, 15, 26, NULL, 1, '2025-08-15 12:44:56'),
(30, 16, 26, NULL, 1, '2025-08-15 12:55:39'),
(31, 17, 25, NULL, 20, '2025-08-15 13:12:14'),
(32, 18, 12, NULL, 2, '2025-09-18 20:11:42'),
(33, 18, 10, NULL, 1, '2025-09-18 20:11:42');

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
(12, '202508-00001', 5, 150000, 'Blk 82 Lot 9 Phase 2 Mabuhay Mamatid Cabuyao Laguna', 0, '2025-08-15 10:02:27', '2025-08-15 10:02:27'),
(13, '202508-00002', 6, 73200, 'Blk 88 Lot 8 Mabuhay Mamatid Cabuyao Laguna', 1, '2025-08-15 12:08:17', '2025-08-15 12:08:49'),
(14, '202508-00003', 6, 112000, 'Blk 88 Lot 8 Mabuhay Mamatid Cabuyao Laguna', 1, '2025-08-15 12:21:54', '2025-08-15 12:22:27'),
(15, '202508-00004', 6, 88000, 'Blk 88 Lot 8 Mabuhay Mamatid Cabuyao Laguna', 1, '2025-08-15 12:44:56', '2025-08-15 12:45:59'),
(16, '202508-00005', 2, 88000, 'Blk 72 Lot 7 Phase 6 Mabuhay Mamatid Cabuyao Laguna', 1, '2025-08-15 12:55:39', '2025-08-15 12:56:32'),
(17, '202508-00006', 6, 2240000, 'Blk 88 Lot 8 Mabuhay Mamatid Cabuyao Laguna', 0, '2025-08-15 13:12:14', '2025-08-15 13:12:14'),
(18, 'ORD-20250918-A878BD', 6, 152500, 'Blk 88 Lot 8 Mabuhay Mamatid Cabuyao Laguna', 0, '2025-09-18 20:11:42', NULL);

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
(1, 6, 'cr', 'OR-2025-001234', 'ABC 1234', NULL, NULL, '2025-07-27', NULL, 'pending', 'uploads/documents/1.pdf?v=1755242148', '', '2025-08-15 15:15:48', '2025-08-15 15:15:48');

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
(3, 33, 'Black', 'uploads/products/colors/33_black.jpg?v=1758621663', '2025-09-23 18:01:03');

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
(7, 9, 12, 'Alpinestars SM5 Helmet - Honda - Black/Red Glossy', 'Honda Click', NULL, '&lt;p&gt;&quot;Ride in style and safety with the Alpinestars SM5 helmet, featuring Honda&rsquo;s signature colors in a sleek glossy finish, engineered for maximum comfort and protection.&quot;&lt;/p&gt;', 2000, 'C', 10, 50, 5, 0.00, NULL, 7, 1, 'uploads/products/7.jpeg?v=1755083567', 0, '2025-08-13 19:12:47', '2025-08-15 17:02:41'),
(8, 9, 12, 'Honda Riding Gloves', 'Honda Click', NULL, '&lt;p&gt;&quot;Comfortable and protective riding gloves featuring a breathable design, strong grip, and premium materials &mdash; ideal for every Honda rider&rsquo;s journey.&quot;&lt;/p&gt;', 1800, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/8.jpg?v=1755083734', 0, '2025-08-13 19:15:34', '2025-08-15 16:56:08'),
(9, 9, 12, 'Honda Side Mirror', 'Honda Click', NULL, '&lt;p&gt;&quot;Sleek and durable side mirror crafted for Honda motorcycles, offering clear visibility and a perfect fit to enhance both safety and style on the road.&quot;&lt;/p&gt;', 1200, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/9.jpg?v=1755083780', 0, '2025-08-13 19:16:20', '2025-08-15 16:56:08'),
(10, 9, 12, 'Honda Alloy Wheel 17', 'Honda Click', NULL, '&lt;p&gt;&quot;Durable 17-inch alloy wheel designed for superior strength and performance, perfect for enhancing your Honda motorcycle&rsquo;s style and handling.&quot;&lt;/p&gt;', 7500, 'C', 10, 50, 5, 0.00, NULL, 7, 1, 'uploads/products/10.jpeg?v=1755083835', 0, '2025-08-13 19:17:15', '2025-08-15 17:02:41'),
(11, 9, 10, 'Honda New RS125', 'Honda Click', NULL, '&lt;p&gt;&quot;A stylish and dependable underbone motorcycle built for both city commutes and long rides. The Honda New RS125 combines sporty looks, fuel efficiency, and a responsive 125cc engine, giving riders a perfect balance of performance and practicality.&quot;&lt;/p&gt;', 75000, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/11.png?v=1755083959', 0, '2025-08-13 19:19:19', '2025-08-15 17:02:41'),
(12, 9, 10, 'Honda Scoopy Slant', 'Honda', NULL, '&lt;p&gt;&quot;Compact, stylish, and fuel-efficient, the Honda Scoopy Slant is perfect for urban riders. With its modern design, comfortable seating, and reliable engine, it delivers a smooth and fun riding experience every day.&quot;&lt;/p&gt;', 72500, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/12.png?v=1755096446', 0, '2025-08-13 22:47:26', '2025-08-15 17:02:41'),
(13, 9, 10, 'Honda ADV', 'Honda', NULL, '&lt;p&gt;&quot;Adventure-ready and versatile, the Honda ADV combines rugged style with powerful performance. Ideal for city streets and off-road journeys, it features a responsive engine, comfortable ergonomics, and advanced suspension for a smooth ride anywhere.&quot;&lt;/p&gt;', 150000, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/13.png?v=1755096549', 0, '2025-08-13 22:49:09', '2025-08-15 17:02:41'),
(14, 9, 10, 'Honda Dio', 'Honda', NULL, '&lt;p&gt;&quot;Sporty, compact, and easy to maneuver, the Honda Dio is perfect for city commuting. With its stylish design, fuel-efficient engine, and comfortable ride, it&rsquo;s a top choice for daily riders.&quot;&lt;/p&gt;', 66500, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/14.png?v=1755096602', 0, '2025-08-13 22:50:02', '2025-08-15 17:02:41'),
(15, 9, 10, 'Honda Air Blade', 'Honda', NULL, '&lt;p data-start=&quot;58&quot; data-end=&quot;321&quot;&gt;&lt;em data-start=&quot;80&quot; data-end=&quot;319&quot;&gt;&quot;Sleek, modern, and performance-driven, the Honda Air Blade offers a smooth and powerful ride for urban commuters. With its advanced engine technology, sporty design, and comfortable ergonomics, it&rsquo;s built for both style and efficiency.&quot;&lt;/em&gt;&lt;/p&gt;', 95000, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/15.png?v=1755096659', 0, '2025-08-13 22:50:59', '2025-08-15 17:02:41'),
(16, 9, 15, 'Honda Genuine Coolant Oil', 'Honda', NULL, '&lt;p&gt;&quot;Keep your motorcycle running smoothly with Honda Genuine Coolant Oil. Engineered to maintain optimal temperature, prevent corrosion, and protect your engine for long-lasting performance.&quot;&lt;/p&gt;', 650, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/16.png?v=1755096734', 0, '2025-08-13 22:52:14', '2025-08-15 16:56:08'),
(17, 9, 15, 'Honda Scooter Fully Synthetic Oil', 'Honda', NULL, '&lt;p&gt;&quot;Premium fully synthetic engine oil specially formulated for Honda scooters. Ensures maximum engine protection, smooth performance, and extended engine life even under heavy riding conditions.&quot;&lt;/p&gt;', 450, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/17.png?v=1755096820', 0, '2025-08-13 22:53:40', '2025-08-15 16:56:08'),
(18, 9, 15, 'Honda Scooter Gear Oil', 'Honda', NULL, '&lt;p&gt;&quot;High-quality gear oil designed for Honda scooters, providing smooth gear shifts, reducing wear and tear, and ensuring long-lasting transmission performance under all riding conditions.&quot;&lt;/p&gt;', 380, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/18.png?v=1755096880', 0, '2025-08-13 22:54:40', '2025-08-15 16:56:08'),
(19, 9, 13, 'Honda Bearing Click', 'Honda', NULL, '&lt;p&gt;&quot;Precision-engineered bearing for Honda motorcycles, ensuring smooth rotation, reduced friction, and reliable performance. Perfect for maintaining your bike&rsquo;s handling and longevity.&quot;&lt;/p&gt;', 320, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/19.png?v=1755096974', 0, '2025-08-13 22:56:14', '2025-08-15 16:56:08'),
(20, 9, 13, 'Honda Click Air Filter', 'Honda', NULL, '&lt;p&gt;&quot;High-quality air filter designed for Honda Click scooters. Ensures clean airflow to the engine, improves performance, and extends engine life by keeping dust and debris out.&quot;&lt;/p&gt;', 250, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/20.png?v=1755097056', 0, '2025-08-13 22:57:36', '2025-08-15 16:56:08'),
(21, 9, 13, 'Honda Scooter Belt Drive', 'Honda', NULL, '&lt;p&gt;&quot;Durable and high-performance drive belt for Honda scooters, engineered to provide smooth power transfer, reduce slippage, and ensure reliable acceleration for daily rides.&quot;&lt;/p&gt;', 900, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/21.png?v=1755097150', 0, '2025-08-13 22:59:10', '2025-08-15 16:56:08'),
(22, 9, 13, 'Honda Scooter Crankshaft', 'Honda', NULL, '&lt;p&gt;&quot;Precision-engineered crankshaft for Honda scooters, designed to ensure smooth engine rotation, optimal power delivery, and long-lasting durability for reliable performance.&quot;&lt;/p&gt;', 2500, 'C', 10, 50, 5, 0.00, NULL, 7, 1, 'uploads/products/22.png?v=1755097229', 0, '2025-08-13 23:00:29', '2025-08-15 17:02:41'),
(23, 9, 10, 'Honda RS125 XRM', 'Honda', NULL, '&lt;p&gt;&quot;Sporty and agile, the Honda RS125 XRM is built for city streets and weekend adventures. With its lightweight frame, responsive 125cc engine, and modern design, it offers an exciting and reliable ride for every rider.&quot;&lt;/p&gt;', 76500, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/23.png?v=1755097338', 0, '2025-08-13 23:02:18', '2025-08-15 17:02:41'),
(24, 9, 10, 'Honda Wave 110', 'Honda', NULL, '&lt;p&gt;&quot;Reliable and fuel-efficient, the Honda Wave 110 is perfect for daily commuting. Its lightweight design, smooth 110cc engine, and comfortable ergonomics make it an ideal choice for city riders.&quot;&lt;/p&gt;', 62500, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/24.png?v=1755097428', 0, '2025-08-13 23:03:48', '2025-08-15 17:02:41'),
(25, 9, 10, 'Honda PCX160', 'Honda', NULL, '&lt;p&gt;&quot;Modern, stylish, and powerful, the Honda PCX160 is a premium scooter designed for urban commuting. Featuring a smooth 160cc engine, advanced fuel efficiency, and comfortable ergonomics, it delivers a refined and enjoyable ride.&quot;&lt;/p&gt;', 112000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/25.png?v=1755097524', 0, '2025-08-13 23:05:24', '2025-08-15 17:02:41'),
(26, 9, 10, 'Honda Click 150i', 'Honda', NULL, '&lt;p&gt;&quot;Sporty and efficient, the Honda Click 150i offers a smooth and powerful 150cc engine, modern design, and comfortable ride. Perfect for city commutes and weekend trips alike.&quot;&lt;/p&gt;', 88000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/26.png?v=1755097625', 0, '2025-08-13 23:07:05', '2025-08-15 17:02:41'),
(27, 9, 10, 'Honda Beat', 'Honda', NULL, '&lt;p&gt;&quot;Compact, fuel-efficient, and reliable, the Honda Beat is perfect for daily commuting. Its lightweight frame, smooth engine, and comfortable ergonomics make it an ideal choice for city riders.&quot;&lt;/p&gt;', 60500, 'C', 1, 20, 7, 0.00, NULL, 7, 1, 'uploads/products/27.png?v=1755097690', 0, '2025-08-13 23:08:10', '2025-08-15 17:02:41'),
(31, 9, 12, 'wew', 'wew', NULL, '&lt;p&gt;wew&lt;/p&gt;', 12312, 'C', 0, 0, 0, 0.00, NULL, 7, 1, '', 1, '2025-08-15 16:25:33', '2025-08-15 16:25:49'),
(32, 9, 12, 'wjagkwda', 'wew', NULL, '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 1, 'C', 0, 0, 0, 0.00, NULL, 7, 1, 'uploads/products/32.png?v=1755246598', 1, '2025-08-15 16:29:58', '2025-08-15 16:30:34'),
(33, 9, 10, 'ADV 160', 'ADV 160', 'Red, White, Black', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 999999, 'A', 3, 99, 10, 9999.00, NULL, 7, 1, 'uploads/products/33.webp?v=1758619970', 0, '2025-09-23 17:32:50', '2025-09-23 18:13:37');

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
(6, 'vehicle_type', 'Scooter'),
(6, 'vehicle_name', 'Honda Click'),
(6, 'vehicle_registration_number', '123'),
(6, 'vehicle_model', 'Honda'),
(6, 'service_id', '5'),
(6, 'pickup_address', ''),
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
(13, 'vehicle_type', 'Motorcycle'),
(13, 'vehicle_name', 'Honda Click 125i'),
(13, 'vehicle_registration_number', 'EFD321'),
(13, 'vehicle_model', 'Honda'),
(13, 'service_id', '8'),
(13, 'pickup_address', '');

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
(1, 6, 'product', 13, 5, '', '2025-09-18 19:25:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_list`
--

CREATE TABLE `service_list` (
  `id` int(30) NOT NULL,
  `service` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_list`
--

INSERT INTO `service_list` (`id`, `service`, `description`, `status`, `delete_flag`, `date_created`) VALUES
(5, 'Brake System Check & Replacement', '&lt;p&gt;Inspection of brake pads, discs, fluid levels, and overall brake performance. Includes replacement of worn-out components and brake fluid flushing if necessary.&lt;/p&gt;', 1, 0, '2025-08-07 22:48:48'),
(6, 'Chain and Sprocket Maintenance', '&lt;p&gt;Cleaning, lubricating, adjusting, or replacing the motorcycle chain and sprockets to prevent wear, reduce noise, and ensure smooth power transfer.&lt;/p&gt;', 1, 0, '2025-08-07 22:49:51'),
(7, 'Battery Check & Replacement', '&lt;p&gt;Testing battery health, terminals, and voltage. Replacement of weak or dead batteries to ensure reliable engine starts and electrical functions.&lt;/p&gt;', 1, 0, '2025-08-07 22:50:10'),
(8, 'Spark Plug Replacement', '&lt;p&gt;Removing old or worn spark plugs and installing new ones to ensure smooth engine ignition and combustion.&lt;/p&gt;', 1, 0, '2025-08-07 22:50:33');

-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `id` int(30) NOT NULL,
  `client_id` int(30) NOT NULL,
  `service_type` text NOT NULL,
  `vehicle_name` varchar(100) DEFAULT NULL,
  `vehicle_registration_number` varchar(20) DEFAULT NULL,
  `mechanic_id` int(30) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_requests`
--

INSERT INTO `service_requests` (`id`, `client_id`, `service_type`, `vehicle_name`, `vehicle_registration_number`, `mechanic_id`, `status`, `date_created`) VALUES
(6, 2, 'Drop Off', NULL, NULL, 4, 0, '2025-04-24 08:28:14'),
(7, 4, 'Drop Off', NULL, NULL, 4, 0, '2025-08-13 14:12:36'),
(8, 4, 'Drop Off', NULL, NULL, 3, 1, '2025-08-13 20:37:41'),
(9, 5, 'Drop Off', NULL, NULL, 3, 1, '2025-08-14 16:48:10'),
(10, 6, 'Drop Off', NULL, NULL, 3, 3, '2025-08-15 12:11:22'),
(11, 2, 'Drop Off', NULL, NULL, 3, 2, '2025-08-15 12:56:09'),
(12, 6, 'Drop Off', NULL, NULL, 8, 4, '2025-08-15 14:09:35'),
(13, 6, 'Drop Off', NULL, NULL, NULL, 0, '2025-09-18 19:38:50');

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
(11, 7, 100, 1, '2025-08-15 11:55:54'),
(12, 15, 20, 1, '2025-08-15 11:56:05'),
(13, 10, 100, 1, '2025-08-15 11:56:16'),
(14, 19, 100, 1, '2025-08-15 11:56:26'),
(15, 27, 1100, 1, '2025-08-15 11:56:37'),
(16, 26, 20, 1, '2025-08-15 11:56:47'),
(17, 20, 200, 1, '2025-08-15 11:56:57'),
(18, 14, 30, 1, '2025-08-15 11:57:07'),
(19, 16, 300, 1, '2025-08-15 11:57:17'),
(20, 11, 10, 1, '2025-08-15 11:57:30'),
(21, 25, 1000, 1, '2025-08-15 11:58:06'),
(22, 8, 60, 1, '2025-08-15 11:58:20'),
(23, 23, 100, 1, '2025-08-15 11:58:33'),
(24, 12, 20, 1, '2025-08-15 11:58:44'),
(25, 21, 80, 1, '2025-08-15 11:58:58'),
(26, 22, 100, 1, '2025-08-15 11:59:09'),
(27, 17, 250, 1, '2025-08-15 11:59:22'),
(28, 18, 199, 1, '2025-08-15 11:59:40'),
(29, 9, 399, 1, '2025-08-15 11:59:51'),
(30, 24, 11, 1, '2025-08-15 12:00:02'),
(31, 33, 99, 1, '2025-09-23 18:14:04');

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
(1, 33, 'IN', 99.00, 0.00, 99.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-09-23 18:14:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

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

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_person`, `email`, `phone`, `address`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 'Yamaha Philippines', 'John Smith', 'john.smith@yamaha.ph', '+63 912 345 6789', 'Makati City, Philippines', 1, 0, '2025-08-15 16:56:02', NULL),
(2, 'Kawasaki Philippines', 'Maria Garcia', 'maria.garcia@kawasaki.ph', '+63 923 456 7890', 'Quezon City, Philippines', 1, 0, '2025-08-15 16:56:02', NULL),
(3, 'BMW Motorrad Philippines', 'Robert Johnson', 'robert.johnson@bmw.ph', '+63 934 567 8901', 'Taguig City, Philippines', 1, 0, '2025-08-15 16:56:02', NULL),
(4, 'Generic Parts Supplier', 'Generic Contact', 'contact@genericparts.ph', '+63 945 678 9012', 'Manila, Philippines', 1, 0, '2025-08-15 16:56:02', NULL);

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
(11, 'logo', 'uploads/1744257240_starhonda-removebg-preview.png'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/1755095700_starhondacoverpage.png'),
(15, 'email_notifications', '1'),
(16, 'sms_notifications', '0'),
(17, 'notification_email', 'noreply@example.com');

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

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`, `login_attempts`, `is_locked`, `locked_until`, `role_type`, `branch_id`, `permissions`) VALUES
(1, 'Adminstrator', 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', 'uploads/1744257000_aiahsuit.jpg', NULL, 1, '2021-01-20 14:02:37', '2025-08-13 10:18:32', 0, 0, NULL, 'admin', NULL, NULL),
(7, 'Mikha', 'Lim', 'mikha@gmail.com', '8baf8e5afa5668bb28016fb9e1cb947b', 'uploads/1745507400_mikhasuit.jpg', '2025-08-15 13:37:38', 2, '2025-04-24 23:10:45', '2025-08-15 13:37:38', 0, 0, NULL, 'admin', NULL, NULL),
(8, 'Colet', 'Vergara', 'colet', 'cc15b9590c89836686088c3826adb108', 'uploads/1754577420_coletsuit.jpg', NULL, 2, '2025-08-07 22:37:31', NULL, 0, 0, NULL, 'admin', NULL, NULL),
(9, 'Aljay', 'Plantado', 'aljay', 'f0bd1fc09c2cfe760c342571f040eae7', 'uploads/1755062580_ID FINAL.jpg', '2025-09-23 17:21:14', 1, '2025-08-13 13:23:57', '2025-09-23 17:21:14', 0, 0, NULL, 'admin', NULL, NULL);

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
  ADD UNIQUE KEY `email` (`email`) USING HASH;

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
-- Indexes for table `mechanics_list`
--
ALTER TABLE `mechanics_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_read` (`is_read`),
  ADD KEY `type` (`type`);

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
-- Indexes for table `product_color_images`
--
ALTER TABLE `product_color_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `color` (`color`);

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
-- Indexes for table `product_recommendations`
--
ALTER TABLE `product_recommendations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_recommendation` (`product_id`,`recommended_product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `recommended_product_id` (`recommended_product_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `client_list`
--
ALTER TABLE `client_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customer_transactions`
--
ALTER TABLE `customer_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inventory_alerts`
--
ALTER TABLE `inventory_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_settings`
--
ALTER TABLE `inventory_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mechanics_list`
--
ALTER TABLE `mechanics_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `order_list`
--
ALTER TABLE `order_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `or_cr_documents`
--
ALTER TABLE `or_cr_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_color_images`
--
ALTER TABLE `product_color_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_list`
--
ALTER TABLE `product_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `product_recommendations`
--
ALTER TABLE `product_recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `service_list`
--
ALTER TABLE `service_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `stock_list`
--
ALTER TABLE `stock_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_list`
--
ALTER TABLE `cart_list`
  ADD CONSTRAINT `cart_list_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_list_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
