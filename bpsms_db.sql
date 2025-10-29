-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Oct 17, 2025 at 11:41 AM
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
-- Database: `if0_40141531_motoease_7`
--

-- --------------------------------------------------------

--
-- Table structure for table `abc_analysis_view`
--

CREATE TABLE `abc_analysis_view` (
  `id` int(30) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `abc_category` enum('A','B','C') DEFAULT NULL,
  `price` float DEFAULT NULL,
  `reorder_point` int(11) DEFAULT NULL,
  `max_stock` int(11) DEFAULT NULL,
  `min_stock` int(11) DEFAULT NULL,
  `current_stock` double DEFAULT NULL,
  `total_ordered` double DEFAULT NULL,
  `available_stock` double DEFAULT NULL,
  `stock_status` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 8, 7, 9, '2025-10-11', '14:30:00', 'Click 125', '', 'confirmed', '2025-10-10 13:26:51', '2025-10-14 17:04:17'),
(4, 8, 70, 8, '2025-10-15', '09:30:00', '', '', 'confirmed', '2025-10-11 14:28:33', '2025-10-14 16:58:45'),
(0, 8, 52, 9, '2025-10-22', '14:30:00', '', '', 'confirmed', '2025-10-14 16:36:09', '2025-10-14 17:13:30'),
(0, 8, 53, 7, '2025-10-14', '15:00:00', '', '', 'confirmed', '2025-10-14 17:12:57', '2025-10-14 17:13:30'),
(0, 8, 52, 9, '2025-10-22', '15:30:00', '', '', 'pending', '2025-10-14 17:24:38', NULL),
(0, 8, 58, 8, '2025-10-15', '10:55:00', 'The scooter is used daily for commuting, around 30 km per day. Last maintenance was 3 months ago.', 'The engine feels rough when idling and there’s a squeaking noise from the rear brakes. Please check the oil, brakes, and overall performance.', 'pending', '2025-10-15 07:58:53', NULL),
(0, 8, 26, 9, '2025-10-15', '10:45:00', 'The scooter is used daily for commuting, around 30 km per day. Last maintenance was 3 months ago.', 'The engine feels rough when idling and there’s a squeaking noise from the rear brakes. Please check the oil, brakes, and overall performance.', 'pending', '2025-10-15 08:45:25', NULL),
(0, 8, 51, 9, '2025-10-16', '14:19:00', 'Used mainly for delivery purposes; clocked 25,000 km. Front tire shows visible wear', 'The front tire is bald and there’s reduced grip on wet roads. Also feels vibration when accelerating.', 'pending', '2025-10-15 09:19:28', NULL),
(0, 8, 52, 9, '2025-10-22', '16:30:00', '', '', 'pending', '2025-10-15 09:20:22', NULL),
(0, 8, 53, 7, '2025-10-18', '15:00:00', 'Used weekly for errands. Haven’t done maintenance for almost a year.', '', 'pending', '2025-10-15 09:36:08', NULL),
(0, 8, 52, 9, '2025-10-22', '02:34:00', '', '', 'pending', '2025-10-15 10:02:07', NULL),
(0, 8, 52, 9, '2025-10-22', '21:30:00', '', '', 'pending', '2025-10-15 13:27:27', NULL),
(0, 8, 7, 10, '2025-10-30', '08:31:00', '', '', 'pending', '2025-10-15 15:31:40', NULL),
(0, 8, 29, 8, '2025-10-15', '18:31:00', 'honda click year 2022', 'sira po yung break pwede po paayos', 'pending', '2025-10-15 17:31:21', NULL),
(0, 8, 44, 9, '2025-10-15', '15:00:00', 'Honda Click ABC123 sira brake yah', '', 'pending', '2025-10-15 17:32:46', NULL),
(0, 8, 54, 8, '2025-10-15', '20:44:00', '2022 Honda Click 125i', 'sira po yung brake muntik na ko ma aksidente', 'pending', '2025-10-15 19:44:53', NULL),
(0, 8, 52, 9, '2025-10-22', '14:03:00', '', '', 'confirmed', '2025-10-15 19:59:45', NULL),
(0, 8, 7, 7, '2025-11-05', '09:22:00', 'wewewewewewewwewe', 'wewewwewewewe', 'pending', '2025-10-16 09:18:12', NULL),
(0, 8, 53, 8, '2025-10-22', '10:24:00', 'awdwdwdawawdawawdaaw', 'awdwdawawdawwa', 'pending', '2025-10-16 10:21:36', NULL),
(0, 10, 7, 8, '2025-10-16', '11:00:00', 'click 125 year model 2022 sira ang battery', '', 'pending', '2025-10-16 21:25:25', NULL);

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
  `quantity` float NOT NULL DEFAULT 1,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_list`
--

INSERT INTO `cart_list` (`id`, `client_id`, `product_id`, `color`, `quantity`, `date_added`) VALUES
(54, 6, 78, NULL, 1, '2025-10-16 12:23:05'),
(57, 2, 95, NULL, 1, '2025-10-16 15:41:20'),
(58, 2, 98, NULL, 1, '2025-10-16 15:41:25'),
(70, 10, 40, 'Orange', 1, '2025-10-17 10:20:39'),
(71, 10, 102, NULL, 1, '2025-10-17 10:25:30'),
(79, 9, 45, 'Black', 1, '2025-10-17 12:12:05'),
(80, 9, 85, NULL, 1, '2025-10-17 12:12:11'),
(93, 13, 77, NULL, 1, '2025-10-17 17:17:59'),
(94, 13, 93, NULL, 1, '2025-10-17 17:20:47'),
(100, 3, 103, NULL, 2, '2025-10-17 17:34:32'),
(101, 3, 37, 'Red', 1, '2025-10-17 17:34:42'),
(102, 3, 86, NULL, 2, '2025-10-17 17:34:46');

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
(15, 'Genuine Oil', 1, 0, '2025-08-08 08:10:57');

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
  `address` text NOT NULL DEFAULT '',
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
  `or_document` text DEFAULT NULL,
  `cr_document` text DEFAULT NULL,
  `vehicle_brand` varchar(100) DEFAULT NULL,
  `vehicle_model` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_list`
--

INSERT INTO `client_list` (`id`, `firstname`, `middlename`, `lastname`, `gender`, `contact`, `address`, `email`, `avatar`, `password`, `status`, `delete_flag`, `date_created`, `date_added`, `last_login`, `credit_application_completed`, `login_attempts`, `is_locked`, `locked_until`, `reset_token`, `reset_expires`, `account_balance`, `vehicle_plate_number`, `or_cr_number`, `or_cr_release_date`, `or_cr_status`, `or_cr_file_path`, `or_document`, `cr_document`, `vehicle_brand`, `vehicle_model`) VALUES
(2, 'Aiah', '', 'Arceta', 'Female', '09123456789', '', 'aiah@gmail.com', 'uploads/1758764437_68d49d955b39c.jpg', '6b3251cd488029543402df97cbc20500', 1, 0, '2025-04-23 22:34:17', '2025-10-16 15:41:10', '2025-10-16 15:39:39', 1, 0, 0, NULL, NULL, NULL, 100000.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL),
(3, 'Jhoanna', '', 'Robles', 'Female', '0901262004', '', 'jhoanna@gmail.com', 'uploads/1758764543_68d49dff84700.jpg', '6172961ee1eccc046bd3810138cc68ee', 1, 0, '2025-08-07 22:02:24', '2025-10-17 17:22:04', '2025-10-17 17:22:04', 0, 0, 0, NULL, NULL, NULL, 10000.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL),
(6, 'Mary', '', 'Clara', 'Female', '09052720021', '', 'maryclara@gmail.com', 'uploads/1758764278_68d49cf6993cc.png', 'fe149d3eddaf84487c5687ee6832969d', 1, 0, '2025-08-15 10:43:41', '2025-10-17 16:50:16', '2025-10-17 11:01:51', 0, 3, 1, '2025-10-17 16:51:16', NULL, NULL, 99000.00, 'ABC 123', 'OR-2025-001234', '2025-08-03', 'pending', NULL, NULL, NULL, NULL, NULL),
(8, 'Crisostomo', '', 'Vergara', 'Female', '09091320021', 'BLK 8 Lot 88 Phase 8 Mabuhay Mamatid Cabuyao Laguna', 'crisostomovergara@gmail.com', 'uploads/1758764032_68d49c0073d68.png', 'cfd1ca6b84fc6360c003e01842457ca6', 1, 0, '2025-08-15 15:17:48', '2025-10-17 16:41:27', '2025-10-17 16:35:30', 1, 4, 1, '2025-10-17 16:36:46', 'effd8b3435e120f2c406cae116fee92c84402f55a6edd396265c653274a6a474', '2025-10-17 17:41:27', 3000000.00, 'C0 L3T', '9132002', NULL, 'released', NULL, NULL, NULL, 'Honda', 'Click'),
(9, 'Stacey', '', 'Sevilleja', 'Female', '09070132003', '', 'stacey@gmail.com', 'uploads/1758766681_68d4a65987fd0.jpg', 'b9066808309e7b228d070046223bdf38', 1, 0, '2025-09-25 10:18:01', '2025-10-17 11:48:54', '2025-10-17 11:48:54', 1, 0, 0, NULL, NULL, NULL, 100000.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL),
(10, 'Sheena ', '', 'Catacutan', 'Female', '09050920040', '', 'sheena@gmail.com', 'uploads/1758766984_68d4a7883710a.jpg', '1861d297772b6e5e36339b54ebd5a65d', 1, 0, '2025-09-25 10:23:04', '2025-10-17 10:16:26', '2025-10-17 10:16:26', 1, 0, 0, NULL, NULL, NULL, 300000.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL),
(11, 'Gwen', '', 'Apuli', 'Female', '09061920030', '', 'gwen@gmail.com', 'uploads/1758767086_68d4a7eec49c0.jpg', 'ac3b3a08a7941208a59fe263cac1bbc5', 1, 0, '2025-09-25 10:24:46', '2025-10-16 16:09:36', '2025-10-16 16:09:36', 0, 0, 0, NULL, NULL, NULL, 30000.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL),
(12, 'Maloi', '', 'Ricalde', 'Female', '09052720020', '', 'maloi@gmail.com', 'uploads/1758767178_68d4a84ab044b.jpg', 'b35fb828fbc25ce215922fd412492aae', 1, 0, '2025-09-25 10:26:18', '2025-10-17 13:43:18', NULL, 0, 2, 0, NULL, NULL, NULL, 50000.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL),
(13, 'Saint', '', 'Lucky', 'Female', '09282345681', '', 'saintlucky@gmail.com', 'uploads/1760323646_68ec683eaa8d0.jpeg', '7879ebdb42025673b18ab77990be74b3', 1, 0, '2025-10-13 10:47:26', '2025-10-17 17:11:40', '2025-10-17 17:11:40', 1, 0, 0, NULL, '9f82db5cf67fb4d419094a22a2c89f4761c8e513c92c9fdfe777246bb5842760', '2025-10-17 16:55:08', 100000.00, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL);

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
(5, 2, '', 98899999.99, 'Balance adjustment: too much', 'ADJ-20250815-731C4A', '2025-08-15 15:44:44'),
(0, 11, 'payment', 30000.00, 'Balance adjustment: ', 'ADJ-20251016-161A29', '2025-10-16 14:16:09'),
(0, 3, 'payment', 10000.00, 'Balance adjustment: cash in', 'ADJ-20251016-2F9431', '2025-10-16 14:23:45'),
(0, 6, '', 1000.00, 'Balance adjustment: minus', 'ADJ-20251016-7D151A', '2025-10-16 14:31:10'),
(0, 10, 'payment', 300000.00, 'Balance adjustment: cash in', 'ADJ-20251016-4E3153', '2025-10-16 14:38:54'),
(0, 9, 'payment', 100000.00, 'Balance adjustment: cash in', 'ADJ-20251016-A2B909', '2025-10-16 14:39:45'),
(0, 2, 'payment', 100000.00, 'Balance adjustment: cash in', 'ADJ-20251016-4AA9E0', '2025-10-16 14:40:03'),
(0, 13, 'payment', 100000.00, 'Balance adjustment: Cash In', 'ADJ-20251016-BF8E3D', '2025-10-16 14:40:29'),
(0, 4, 'payment', 1000000.00, 'Balance adjustment: Cash In', 'ADJ-20251016-CE378B', '2025-10-16 14:40:47'),
(0, 12, 'payment', 50000.00, 'Balance adjustment: cash in', 'ADJ-20251016-9A78EF', '2025-10-16 14:41:03'),
(0, 8, 'payment', 3000000.00, 'Balance adjustment: Cash In', 'ADJ-20251016-CDF374', '2025-10-16 14:41:21'),
(0, 6, 'payment', 100000.00, 'Balance adjustment: Cash In', 'ADJ-20251016-03D13B', '2025-10-16 14:41:44');

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
(27, 34, 'LOW_STOCK', 0.00, 3.00, 'Low stock alert: Click 160 has 0 units remaining (Reorder point: 3)', 1, NULL, '2025-10-16 09:48:09', '2025-10-12 23:08:24'),
(28, 34, 'OUT_OF_STOCK', 0.00, 0.00, 'Out of stock: Click 160 is no longer available', 0, NULL, NULL, '2025-10-12 23:08:24'),
(0, 83, 'OVERSTOCK', 20.00, 20.00, 'Overstock alert: Air Filter has 20 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 16:59:16'),
(0, 80, 'OVERSTOCK', 20.00, 20.00, 'Overstock alert: Belt Drive has 20 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:00:57'),
(0, 78, 'OVERSTOCK', 20.00, 20.00, 'Overstock alert: Brake Pad has 20 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:01:09'),
(0, 77, 'OVERSTOCK', 40.00, 30.00, 'Overstock alert: Brake Shoe has 40 units (Max stock: 30)', 0, NULL, NULL, '2025-10-15 17:01:22'),
(0, 89, 'LOW_STOCK', 20.00, 20.00, 'Low stock alert: Center Spring Main Stand has 20 units remaining (Reorder point: 20)', 0, NULL, NULL, '2025-10-15 17:01:36'),
(0, 89, 'OVERSTOCK', 40.00, 30.00, 'Overstock alert: Center Spring Main Stand has 40 units (Max stock: 30)', 0, NULL, NULL, '2025-10-15 17:01:38'),
(0, 81, 'OVERSTOCK', 20.00, 20.00, 'Overstock alert: Chain and Sprocket Kit has 20 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:03:19'),
(0, 92, 'OVERSTOCK', 40.00, 30.00, 'Overstock alert: Clutch Springs has 40 units (Max stock: 30)', 0, NULL, NULL, '2025-10-15 17:04:49'),
(0, 88, 'OVERSTOCK', 20.00, 20.00, 'Overstock alert: Damper Set Wheel has 20 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:05:36'),
(0, 95, 'OVERSTOCK', 20.00, 20.00, 'Overstock alert: Disk Clutch Friction Set has 20 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:05:43'),
(0, 90, 'LOW_STOCK', 20.00, 20.00, 'Low stock alert: Drain Plug and Washer 12MM has 20 units remaining (Reorder point: 20)', 0, NULL, NULL, '2025-10-15 17:05:50'),
(0, 90, 'OVERSTOCK', 40.00, 30.00, 'Overstock alert: Drain Plug and Washer 12MM has 40 units (Max stock: 30)', 0, NULL, NULL, '2025-10-15 17:05:50'),
(0, 86, 'OVERSTOCK', 20.00, 20.00, 'Overstock alert: Element Air Filter has 20 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:05:57'),
(0, 93, 'OVERSTOCK', 20.00, 20.00, 'Overstock alert: Face Drive has 20 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:06:05'),
(0, 99, 'OVERSTOCK', 30.00, 20.00, 'Overstock alert: Honda Genuine All Season Pre-Mix Coolant TYPE-1 1L has 30 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:06:11'),
(0, 98, 'OVERSTOCK', 30.00, 30.00, 'Overstock alert: Honda Genuine Oil 4T SL 10W30 MB (Blue) Fully Synthetic Scooter Oil 1L has 30 units (Max stock: 30)', 0, NULL, NULL, '2025-10-15 17:06:17'),
(0, 91, 'OVERSTOCK', 20.00, 20.00, 'Overstock alert: Outer Comp Clutch has 20 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:06:30'),
(0, 100, 'OVERSTOCK', 50.00, 50.00, 'Overstock alert: Pro Honda Genuine 10W30 MA (Black) Fully Synthetic for 1L has 50 units (Max stock: 50)', 0, NULL, NULL, '2025-10-15 17:07:35'),
(0, 102, 'OVERSTOCK', 50.00, 20.00, 'Overstock alert: Pro Honda Genuine 10W30 MA (Gold) for 1L has 50 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:07:42'),
(0, 101, 'OVERSTOCK', 50.00, 30.00, 'Overstock alert: Pro Honda Genuine 10W30 MB (Silver) Scooter Oil 0.8L has 50 units (Max stock: 30)', 0, NULL, NULL, '2025-10-15 17:07:48'),
(0, 85, 'OVERSTOCK', 40.00, 20.00, 'Overstock alert: Roller Set Weight has 40 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:07:53'),
(0, 87, 'OVERSTOCK', 30.00, 20.00, 'Overstock alert: Slider Set has 30 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:07:58'),
(0, 97, 'OVERSTOCK', 100.00, 30.00, 'Overstock alert: Spark Plug has 100 units (Max stock: 30)', 0, NULL, NULL, '2025-10-15 17:08:04'),
(0, 79, 'OVERSTOCK', 30.00, 20.00, 'Overstock alert: Throttle Cable has 30 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:08:09'),
(0, 82, 'OVERSTOCK', 30.00, 20.00, 'Overstock alert: Throttle Grip has 30 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:08:15'),
(0, 94, 'OVERSTOCK', 30.00, 20.00, 'Overstock alert: Timing Chain has 30 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:08:20'),
(0, 84, 'OVERSTOCK', 29.00, 20.00, 'Overstock alert: Wire Harness has 29 units (Max stock: 20)', 0, NULL, NULL, '2025-10-15 17:08:26'),
(0, 33, 'OVERSTOCK', 123230.00, 66.00, 'Overstock alert: ADV 160 has 123230 units (Max stock: 66)', 0, NULL, NULL, '2025-10-16 09:58:17'),
(0, 103, 'LOW_STOCK', 10.00, 10.00, 'Low stock alert: Pro Honda Genuine 10W30 MA (Black) Fully Synthetic for 2L has 10 units remaining (Reorder point: 10)', 0, NULL, NULL, '2025-10-16 11:19:25');

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
(1, 27, NULL, 'INV-2025-0001', 8, 'motorcycle_purchase', 'cash', 149900.00, 17988.00, 167888.00, 'paid', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', NULL, 'Payment must be completed in-store. No online payment available. Please bring valid ID and payment method.', 9, '2025-10-10 08:23:16', '2025-10-17', NULL),
(0, 0, NULL, 'INV-2025-0002', 8, 'motorcycle_purchase', 'cash', 521156.00, 62538.72, 583694.72, 'paid', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', NULL, 'Payment must be completed in-store. No online payment available. Please bring valid ID and payment method.', 9, '2025-10-15 15:13:07', '2025-10-22', NULL),
(0, 15, NULL, 'INV-2025-0002', 6, 'motorcycle_purchase', 'cash', 88000.00, 10560.00, 98560.00, 'paid', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', NULL, 'Payment must be completed in-store. No online payment available. Please bring valid ID and payment method.', 9, '2025-10-15 17:48:07', '2025-10-22', NULL),
(0, 13, NULL, 'INV-2025-0002', 6, 'motorcycle_purchase', 'cash', 73200.00, 8784.00, 81984.00, 'paid', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', NULL, 'Payment must be completed in-store. No online payment available. Please bring valid ID and payment method.', 9, '2025-10-15 18:43:51', '2025-10-22', NULL),
(0, 33, NULL, 'INV-2025-0002', 6, 'motorcycle_purchase', 'cash', 120.00, 14.40, 134.40, 'paid', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', NULL, 'Payment must be completed in-store. No online payment available. Please bring valid ID and payment method.', 9, '2025-10-15 19:16:03', '2025-10-22', NULL),
(0, 34, NULL, 'INV-2025-0002', 8, 'motorcycle_purchase', 'cash', 379.00, 45.48, 424.48, 'paid', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', NULL, 'Payment must be completed in-store. No online payment available. Please bring valid ID and payment method.', 9, '2025-10-15 19:16:14', '2025-10-22', NULL),
(0, 39, NULL, 'INV-2025-0002', 6, 'motorcycle_purchase', 'cash', 70900.00, 8508.00, 79408.00, 'unpaid', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', NULL, 'Payment must be completed in-store. No online payment available. Please bring valid ID and payment method.', 9, '2025-10-17 11:37:00', '2025-10-24', NULL),
(0, 43, NULL, 'INV-2025-0002', 8, 'motorcycle_purchase', 'cash', 1200.00, 144.00, 1344.00, 'unpaid', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', NULL, 'Payment must be completed in-store. No online payment available. Please bring valid ID and payment method.', 9, '2025-10-17 11:39:35', '2025-10-24', NULL);

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

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_type`, `item_id`, `item_name`, `item_description`, `quantity`, `unit_price`, `total_price`, `created_at`) VALUES
(1, 1, 'motorcycle', 45, 'PCX 160 ABS', '&lt;p&gt;&lt;strong data-start=&quot;1379&quot; data-end=&quot;1388&quot;&gt;Specs&lt;/strong&gt; (for ABS / typical PCX160)&lt;br data-start=&quot;1415&quot; data-end=&quot;1418&quot;&gt;\r\n&ensp;&bull; Engine: 157 cc, liquid-cooled, 4-valve, eSP+ &lt;span class=&quot;&quot; data-state=&quot;closed&quot;&gt;&lt;span class=&quot;ms-1 inline-flex max-w-full items-center relative top-[-0.094rem] animate-[show_150ms_ease-in]&quot; data-testid=&quot;webpage-citation-pill&quot;&gt;&lt;a href=&quot;https://motortrade.com.ph/motorcycles/honda-pcx-160-abs-motortrade-motorcycles-hondaph/?utm_source=chatgpt.com&quot; target=&quot;_blank&quot; rel=&quot;noopener&quot; alt=&quot;https://motortrade.com.ph/motorcycles/honda-pcx-160-abs-motortrade-motorcycles-hondaph/?utm_source=chatgpt.com&quot; class=&quot;flex h-4.5 overflow-hidden rounded-xl px-2 text-[9px] font-medium transition-colors duration-150 ease-in-out text-token-text-secondary! bg-[#F4F4F4]! dark:bg-[#303030]!&quot;&gt;&lt;span class=&quot;relative start-0 bottom-0 flex h-full w-full items-center&quot;&gt;&lt;span class=&quot;flex h-4 w-full items-center justify-between absolute&quot;&gt;&lt;span class=&quot;max-w-[15ch] grow truncate overflow-hidden text-center&quot;&gt;MotoDeal&lt;/span&gt;&lt;span class=&quot;-me-1 flex h-full items-center rounded-full px-1 text-[#8F8F8F]&quot;&gt;+3&lt;/span&gt;&lt;/span&gt;&lt;span class=&quot;flex h-4 w-full items-center justify-between&quot;&gt;&lt;span class=&quot;max-w-[15ch] grow truncate overflow-hidden text-center&quot;&gt;Motortrade&lt;/span&gt;&lt;span class=&quot;-me-1 flex h-full items-center rounded-full px-1 text-[#8F8F8F]&quot;&gt;+3&lt;/span&gt;&lt;/span&gt;&lt;span class=&quot;flex h-4 w-full items-center justify-between absolute&quot;&gt;&lt;span class=&quot;max-w-[15ch] grow truncate overflow-hidden text-center&quot;&gt;Premiumbikes&lt;/span&gt;&lt;span class=&quot;-me-1 flex h-full items-center rounded-full px-1 text-[#8F8F8F]&quot;&gt;+3&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/span&gt;&lt;/span&gt;&lt;br data-start=&quot;1505&quot; data-end=&quot;1508&quot;&gt;\r\n&ensp;&bull; Max power / torque: ~ 11.8 kW / 14.7 Nm &lt;span class=&quot;&quot; data-state=&quot;closed&quot;&gt;&lt;span class=&quot;ms-1 inline-flex max-w-full items-center relative top-[-0.094rem] animate-[show_150ms_ease-in]&quot; data-testid=&quot;webpage-citation-pill&quot;&gt;&lt;a href=&quot;https://premiumbikes.ph/product/honda-pcx160-abs/?utm_source=chatgpt.com&quot; target=&quot;_blank&quot; rel=&quot;noopener&quot; alt=&quot;https://premiumbikes.ph/product/honda-pcx160-abs/?utm_source=chatgpt.com&quot; class=&quot;flex h-4.5 overflow-hidden rounded-xl px-2 text-[9px] font-medium transition-colors duration-150 ease-in-out text-token-text-secondary! bg-[#F4F4F4]! dark:bg-[#303030]!&quot;&gt;&lt;span class=&quot;relative start-0 bottom-0 flex h-full w-full items-center&quot;&gt;&lt;span class=&quot;flex h-4 w-full items-center justify-between&quot;&gt;&lt;span class=&quot;max-w-[15ch] grow truncate overflow-hidden text-center&quot;&gt;Premiumbikes&lt;/span&gt;&lt;span class=&quot;-me-1 flex h-full items-center rounded-full px-1 text-[#8F8F8F]&quot;&gt;+2&lt;/span&gt;&lt;/span&gt;&lt;span class=&quot;flex h-4 w-full items-center justify-between absolute&quot;&gt;&lt;span class=&quot;max-w-[15ch] grow truncate overflow-hidden text-center&quot;&gt;Motortrade&lt;/span&gt;&lt;span class=&quot;-me-1 flex h-full items-center rounded-full px-1 text-[#8F8F8F]&quot;&gt;+2&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/span&gt;&lt;/span&gt;&lt;br data-start=&quot;1590&quot; data-end=&quot;1593&quot;&gt;\r\n&ensp;&bull; Seat height: 764 mm &lt;span class=&quot;&quot; data-state=&quot;closed&quot;&gt;&lt;span class=&quot;ms-1 inline-flex max-w-full items-center relative top-[-0.094rem] animate-[show_150ms_ease-in]&quot; data-testid=&quot;webpage-citation-pill&quot;&gt;&lt;a href=&quot;https://motortrade.com.ph/motorcycles/honda-pcx-160-abs-motortrade-motorcycles-hondaph/?utm_source=chatgpt.com&quot; target=&quot;_blank&quot; rel=&quot;noopener&quot; alt=&quot;https://motortrade.com.ph/motorcycles/honda-pcx-160-abs-motortrade-motorcycles-hondaph/?utm_source=chatgpt.com&quot; class=&quot;flex h-4.5 overflow-hidden rounded-xl px-2 text-[9px] font-medium transition-colors duration-150 ease-in-out text-token-text-secondary! bg-[#F4F4F4]! dark:bg-[#303030]!&quot;&gt;&lt;span class=&quot;relative start-0 bottom-0 flex h-full w-full items-center&quot;&gt;&lt;span class=&quot;flex h-4 w-full items-center justify-between&quot;&gt;&lt;span class=&quot;max-w-[15ch] grow truncate overflow-hidden text-center&quot;&gt;Motortrade&lt;/span&gt;&lt;span class=&quot;-me-1 flex h-full items-center rounded-full px-1 text-[#8F8F8F]&quot;&gt;+2&lt;/span&gt;&lt;/span&gt;&lt;span class=&quot;flex h-4 w-full items-center justify-between absolute&quot;&gt;&lt;span class=&quot;max-w-[15ch] grow truncate overflow-hidden text-center&quot;&gt;Zigwheels&lt;/span&gt;&lt;span class=&quot;-me-1 flex h-full items-center rounded-full px-1 text-[#8F8F8F]&quot;&gt;+2&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/span&gt;&lt;/span&gt;&lt;br data-start=&quot;1655&quot; data-end=&quot;1658&quot;&gt;\r\n&ensp;&bull; Fuel tank: 8.0 L &lt;span class=&quot;&quot; data-state=&quot;closed&quot;&gt;&lt;span class=&quot;ms-1 inline-flex max-w-full items-center relative top-[-0.094rem] animate-[show_150ms_ease-in]&quot; data-testid=&quot;webpage-citation-pill&quot;&gt;&lt;a href=&quot;https://motortrade.com.ph/motorcycles/honda-pcx-160-abs-motortrade-motorcycles-hondaph/?utm_source=chatgpt.com&quot; target=&quot;_blank&quot; rel=&quot;noopener&quot; alt=&quot;https://motortrade.com.ph/motorcycles/honda-pcx-160-abs-motortrade-motorcycles-hondaph/?utm_source=chatgpt.com&quot; class=&quot;flex h-4.5 overflow-hidden rounded-xl px-2 text-[9px] font-medium transition-colors duration-150 ease-in-out text-token-text-secondary! bg-[#F4F4F4]! dark:bg-[#303030]!&quot;&gt;&lt;span class=&quot;relative start-0 bottom-0 flex h-full w-full items-center&quot;&gt;&lt;span class=&quot;flex h-4 w-full items-center justify-between&quot;&gt;&lt;span class=&quot;max-w-[15ch] grow truncate overflow-hidden text-center&quot;&gt;Motortrade&lt;/span&gt;&lt;span class=&quot;-me-1 flex h-full items-center rounded-full px-1 text-[#8F8F8F]&quot;&gt;+2&lt;/span&gt;&lt;/span&gt;&lt;span class=&quot;flex h-4 w-full items-center justify-between absolute&quot;&gt;&lt;span class=&quot;max-w-[15ch] grow truncate overflow-hidden text-center&quot;&gt;Sarimall&lt;/span&gt;&lt;span class=&quot;-me-1 flex h-full items-center rounded-full px-1 text-[#8F8F8F]&quot;&gt;+2&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/span&gt;&lt;/span&gt;&lt;br data-start=&quot;1717&quot; data-end=&quot;1720&quot;&gt;\r\n&ensp;&bull; Brakes: disc front + disc rear (with ABS in ABS variant) &lt;span class=&quot;&quot; data-state=&quot;closed&quot;&gt;&lt;span class=&quot;ms-1 inline-flex max-w-full items-center relative top-[-0.094rem] animate-[show_150ms_ease-in]&quot; data-testid=&quot;webpage-citation-pill&quot;&gt;&lt;a href=&quot;https://www.sarimall.ph/motorcycle/honda/honda-pcx-160-abs-ww160ap.html?utm_source=chatgpt.com&quot; target=&quot;_blank&quot; rel=&quot;noopener&quot; alt=&quot;https://www.sarimall.ph/motorcycle/honda/honda-pcx-160-abs-ww160ap.html?utm_source=chatgpt.com&quot; class=&quot;flex h-4.5 overflow-hidden rounded-xl px-2 text-[9px] font-medium transition-colors duration-150 ease-in-out text-token-text-secondary! bg-[#F4F4F4]! dark:bg-[#303030]!&quot;&gt;&lt;span class=&quot;relative start-0 bottom-0 flex h-full w-full items-center&quot;&gt;&lt;span class=&quot;flex h-4 w-full items-center justify-between&quot;&gt;&lt;span class=&quot;max-w-[15ch] grow truncate overflow-hidden text-center&quot;&gt;Sarimall&lt;/span&gt;&lt;span class=&quot;-me-1 flex h-full items-center rounded-full px-1 text-[#8F8F8F]&quot;&gt;+2&lt;/span&gt;&lt;/span&gt;&lt;span class=&quot;flex h-4 w-full items-center justify-between absolute&quot;&gt;&lt;span class=&quot;max-w-[15ch] grow truncate overflow-hidden text-center&quot;&gt;Motortrade&lt;/span&gt;&lt;span class=&quot;-me-1 flex h-full items-center rounded-full px-1 text-[#8F8F8F]&quot;&gt;+2&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/span&gt;&lt;/span&gt;&lt;br data-start=&quot;1819&quot; data-end=&quot;1822&quot;&gt;\r\n&ensp;&bull; Curb / kerb weight: ~131 kg &lt;span class=&quot;&quot; data-state=&quot;closed&quot;&gt;&lt;span class=&quot;ms-1 inline-flex max-w-full items-center relative top-[-0.094rem] animate-[show_150ms_ease-in]&quot; data-testid=&quot;webpage-citation-pill&quot;&gt;&lt;a href=&quot;https://premiumbikes.ph/product/honda-pcx160-abs/?utm_source=chatgpt.com&quot; target=&quot;_blank&quot; rel=&quot;noopener&quot; alt=&quot;https://premiumbikes.ph/product/honda-pcx160-abs/?utm_source=chatgpt.com&quot; class=&quot;flex h-4.5 overflow-hidden rounded-xl px-2 text-[9px] font-medium transition-colors duration-150 ease-in-out text-token-text-secondary! bg-[#F4F4F4]! dark:bg-[#303030]!&quot;&gt;&lt;span class=&quot;relative start-0 bottom-0 flex h-full w-full items-center&quot;&gt;&lt;span class=&quot;flex h-4 w-full items-center justify-between&quot;&gt;&lt;span class=&quot;max-w-[15ch] grow truncate overflow-hidden text-center&quot;&gt;Premiumbikes&lt;/span&gt;&lt;span class=&quot;-me-1 flex h-full items-center rounded-full px-1 text-[#8F8F8F]&quot;&gt;+1&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/span&gt;&lt;/span&gt;&lt;br data-start=&quot;1892&quot; data-end=&quot;1895&quot;&gt;\r\n&ensp;&bull; Dimensions: 1,935 &times; 742 &times; 1,108 mm (L &times; W &times; H) &lt;span class=&quot;&quot; data-state=&quot;closed&quot;&gt;&lt;span class=&quot;ms-1 inline-flex max-w-full items-center relative top-[-0.094rem] animate-[show_150ms_ease-in]&quot; data-testid=&quot;webpage-citation-pill&quot;&gt;&lt;a href=&quot;https://www.sarimall.ph/motorcycle/honda/honda-pcx-160-abs-ww160ap.html?utm_source=chatgpt.com&quot; target=&quot;_blank&quot; rel=&quot;noopener&quot; alt=&quot;https://www.sarimall.ph/motorcycle/honda/honda-pcx-160-abs-ww160ap.html?utm_source=chatgpt.com&quot; class=&quot;flex h-4.5 overflow-hidden rounded-xl px-2 text-[9px] font-medium transition-colors duration-150 ease-in-out text-token-text-secondary! bg-[#F4F4F4]! dark:bg-[#303030]!&quot;&gt;&lt;span class=&quot;relative start-0 bottom-0 flex h-full w-full items-center&quot;&gt;&lt;span class=&quot;flex h-4 w-full items-center justify-between overflow-hidden&quot;&gt;&lt;span class=&quot;max-w-[15ch] grow truncate overflow-hidden text-center&quot;&gt;Sarimall&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;', 1, 0.00, 0.00, '2025-10-10 08:23:16'),
(0, 0, 'motorcycle', 45, 'PCX 160 ABS', '&lt;p&gt;&lt;br&gt;&lt;span class=&quot;&quot; data-state=&quot;closed&quot;&gt;&lt;/span&gt;&lt;/p&gt;', 2, 0.00, 0.00, '2025-10-15 15:13:07'),
(0, 0, 'motorcycle', 48, 'XR 150i', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 1, 0.00, 0.00, '2025-10-15 15:13:07'),
(0, 0, 'motorcycle', 49, 'TMX SUPREMO', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 1, 0.00, 0.00, '2025-10-15 15:13:07'),
(0, 0, 'motorcycle', 41, 'RS 125', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 1, 0.00, 0.00, '2025-10-15 15:13:07'),
(0, 0, 'motorcycle', 26, 'Honda Click 125i', '<p><strong>Honda Click 125i</strong></p><p>The New CLICK125 SE is powered by a 125cc Liquid-cooled, PGM-FI engine with Enhanced Smart Power and an ACG starter, making the model fuel efficient at 53 km/L. The model comes with the Combi Brake System and Park Brake Lock for added safety features.</p>', 1, 0.00, 0.00, '2025-10-15 17:48:08'),
(0, 0, 'motorcycle', 27, 'Honda Beat', '&lt;p&gt;&quot;Compact, fuel-efficient, and reliable, the Honda Beat is perfect for daily commuting. Its lightweight frame, smooth engine, and comfortable ergonomics make it an ideal choice for city riders.&quot;&lt;/p&gt;', 1, 0.00, 0.00, '2025-10-15 18:43:51'),
(0, 0, 'motorcycle', 17, 'Honda Scooter Fully Synthetic Oil', '&lt;p&gt;&quot;Premium fully synthetic engine oil specially formulated for Honda scooters. Ensures maximum engine protection, smooth performance, and extended engine life even under heavy riding conditions.&quot;&lt;/p&gt;', 3, 0.00, 0.00, '2025-10-15 18:43:51'),
(0, 0, 'motorcycle', 19, 'Honda Bearing Click', '&lt;p&gt;&quot;Precision-engineered bearing for Honda motorcycles, ensuring smooth rotation, reduced friction, and reliable performance. Perfect for maintaining your bike&rsquo;s handling and longevity.&quot;&lt;/p&gt;', 5, 0.00, 0.00, '2025-10-15 18:43:51'),
(0, 0, 'motorcycle', 20, 'Honda Click Air Filter', '&lt;p&gt;&quot;High-quality air filter designed for Honda Click scooters. Ensures clean airflow to the engine, improves performance, and extends engine life by keeping dust and debris out.&quot;&lt;/p&gt;', 1, 0.00, 0.00, '2025-10-15 18:43:51'),
(0, 0, 'motorcycle', 82, 'Throttle Grip', '&lt;p&gt;Handlebar grip used to control the throttle opening.&lt;/p&gt;', 1, 0.00, 0.00, '2025-10-15 19:16:03'),
(0, 0, 'motorcycle', 98, 'Honda Genuine Oil 4T SL 10W30 MB (Blue) Fully Synthetic Scooter Oil 1L', '&lt;ul style=&quot;-webkit-font-smoothing: antialiased; margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style: none; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;Honda Genuine Oil 4T SL 10W30 MB (Blue) Fully Synthetic Scooter Oil 1L&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;08234-2MB-K1LP&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;Honda Genuine Oil 4T SL 10W30 MB Fully Synthetic is a 100% is a superior-quality synthetic engine oil designed and approved by Honda for modern high performance scooters.&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;This Fully Synthetic oil gives the highest engine protection with its most advanced additives making it best oil for Honda Scooters.&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;100% Fully Synthetic Engine Oil&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;Four-stroke (4T) Engine Oil&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;API Service Rating : SL&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;SAE Viscosity Grade: 10W-30&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative;&quot;&gt;JASO MB&lt;/li&gt;&lt;/ul&gt;', 1, 0.00, 0.00, '2025-10-15 19:16:14'),
(0, 0, 'motorcycle', 51, 'DIO', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;110cc air-cooled, CVT, CBS, Practical and fuel-efficient&lt;/span&gt;&lt;/p&gt;', 1, 0.00, 0.00, '2025-10-17 11:37:00'),
(0, 0, 'motorcycle', 80, 'Belt Drive', '&lt;p&gt;Primary component in the continuously variable transmission (CVT) system.&lt;/p&gt;', 1, 0.00, 0.00, '2025-10-17 11:39:35');

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

--
-- Dumping data for table `invoice_settings`
--

INSERT INTO `invoice_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'invoice_prefix', 'INV', 'Prefix for invoice numbers', '2025-10-10 08:22:39'),
(2, 'receipt_prefix', 'RCPT', 'Prefix for receipt numbers', '2025-10-10 08:22:39'),
(3, 'vat_rate', '12', 'VAT rate percentage', '2025-10-10 08:22:39'),
(4, 'company_name', 'Star Honda Calamba', 'Company name for invoices', '2025-10-10 08:22:39'),
(5, 'company_address', 'National Highway Brgy. Parian, Calamba City, Laguna', 'Company address', '2025-10-10 08:22:40'),
(6, 'company_phone', '0948-235-3207', 'Company phone number', '2025-10-10 08:22:40'),
(7, 'company_email', 'starhondacalamba55@gmail.com', 'Company email', '2025-10-10 08:22:40'),
(8, 'pickup_location', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', 'Default pickup location', '2025-10-10 08:22:40'),
(9, 'payment_instructions', 'Payment must be completed in-store. No online payment available. Please bring valid ID and payment method.', 'Default payment instructions', '2025-10-10 08:22:40'),
(10, 'acknowledgment_note', 'Thank you for your purchase at Star Honda Calamba! We appreciate your business and look forward to serving you again.', 'Default acknowledgment note', '2025-10-10 08:22:40');

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
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mechanics_list`
--

INSERT INTO `mechanics_list` (`id`, `name`, `contact`, `email`, `status`, `date_created`, `avatar`) VALUES
(7, 'Jan Jan Matanguihan', '09282346151', 'janjanmatanguihan@gmail.com', 1, '2025-09-18 16:44:09', 'uploads/mechanics/7.png?v=1760487918'),
(8, 'Aldrin Caldozo', '09065775184', 'aldrincaldozo@gmail.com', 1, '2025-09-18 16:44:53', 'uploads/mechanics/8.png?v=1760486530'),
(9, 'Fernando Rimando', '09505639564', 'fernandorimando@gmail.com', 1, '2025-09-18 16:45:36', 'uploads/mechanics/9.png?v=1760486590'),
(10, 'Jeffrey Capunitan', '09286594732', 'jeffreycapunitan@gmail.com', 1, '2025-09-18 16:46:05', 'uploads/mechanics/10.png?v=1760487878');

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
(0, 0, 45, NULL, 2, '2025-10-14 23:40:28'),
(0, 0, 48, NULL, 1, '2025-10-14 23:40:28'),
(0, 0, 49, NULL, 1, '2025-10-14 23:40:28'),
(0, 0, 41, NULL, 1, '2025-10-14 23:40:28'),
(0, 0, 43, NULL, 1, '2025-10-15 15:30:49'),
(0, 0, 41, NULL, 1, '2025-10-15 15:30:49'),
(0, 0, 82, NULL, 1, '2025-10-15 17:33:52'),
(0, 0, 38, 'Fashion Sport STD Black Red', 1, '2025-10-15 17:33:52'),
(0, 0, 91, NULL, 1, '2025-10-15 17:33:52'),
(0, 0, 94, NULL, 1, '2025-10-15 17:33:52'),
(0, 0, 86, NULL, 1, '2025-10-15 17:33:52'),
(0, 0, 78, NULL, 1, '2025-10-15 17:33:52'),
(0, 0, 79, NULL, 1, '2025-10-15 17:33:52'),
(0, 0, 38, 'Beat 110 Premium 2', 2, '2025-10-15 17:41:54'),
(0, 0, 79, NULL, 1, '2025-10-15 17:41:54'),
(0, 0, 88, NULL, 1, '2025-10-15 17:41:54'),
(0, 0, 38, NULL, 1, '2025-10-15 17:46:16'),
(0, 0, 37, NULL, 1, '2025-10-15 17:55:15'),
(0, 0, 88, NULL, 1, '2025-10-15 18:20:01'),
(0, 0, 101, NULL, 1, '2025-10-15 18:37:29'),
(0, 0, 33, NULL, 1, '2025-10-15 18:40:38'),
(0, 33, 82, NULL, 1, '2025-10-15 19:10:47'),
(0, 34, 98, NULL, 1, '2025-10-15 19:14:06'),
(0, 37, 47, NULL, 1, '2025-10-16 10:42:51'),
(0, 37, 86, NULL, 1, '2025-10-16 10:42:51'),
(0, 38, 38, 'Playful Gray', 1, '2025-10-16 12:03:19'),
(0, 39, 51, 'Red Black', 1, '2025-10-16 12:21:12'),
(0, 40, 85, NULL, 1, '2025-10-16 12:22:46'),
(0, 41, 35, 'Red', 1, '2025-10-16 15:41:12'),
(0, 42, 40, 'Orange', 1, '2025-10-16 21:19:17'),
(0, 42, 95, NULL, 1, '2025-10-16 21:19:17'),
(0, 42, 91, NULL, 1, '2025-10-16 21:19:17'),
(0, 42, 80, NULL, 1, '2025-10-16 21:19:17'),
(0, 42, 86, NULL, 1, '2025-10-16 21:19:17'),
(0, 42, 85, NULL, 1, '2025-10-16 21:19:17'),
(0, 43, 80, NULL, 1, '2025-10-16 21:33:59'),
(0, 44, 47, 'Blue', 1, '2025-10-17 11:24:30'),
(0, 45, 52, 'Red Black', 1, '2025-10-17 11:49:19'),
(0, 46, 91, NULL, 1, '2025-10-17 11:52:43'),
(0, 47, 37, 'Red', 1, '2025-10-17 12:10:10'),
(0, 47, 103, NULL, 1, '2025-10-17 12:10:10'),
(0, 48, 98, NULL, 1, '2025-10-17 13:33:56'),
(0, 49, 47, 'Blue', 1, '2025-10-17 13:47:56'),
(0, 49, 89, NULL, 2, '2025-10-17 13:47:56'),
(0, 50, 100, NULL, 1, '2025-10-17 13:56:20'),
(0, 51, 77, NULL, 1, '2025-10-17 13:57:15'),
(0, 52, 86, NULL, 1, '2025-10-17 13:59:38'),
(0, 53, 92, NULL, 1, '2025-10-17 14:16:34'),
(0, 54, 35, 'Red', 1, '2025-10-17 14:18:47');

-- --------------------------------------------------------

--
-- Table structure for table `order_list`
--

CREATE TABLE `order_list` (
  `id` int(30) NOT NULL,
  `ref_code` varchar(100) NOT NULL,
  `client_id` int(30) NOT NULL,
  `total_amount` float NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=pending,1 = packed, 2 = for delivery, 3 = on the way, 4 = delivered, 5=cancelled',
  `requires_credit` tinyint(1) NOT NULL DEFAULT 0,
  `agreed_to_terms` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_list`
--

INSERT INTO `order_list` (`id`, `ref_code`, `client_id`, `total_amount`, `status`, `requires_credit`, `agreed_to_terms`, `date_created`, `date_updated`) VALUES
(18, 'ORD-20250918-A878BD', 6, 152500, 6, 0, 0, '2025-09-18 20:11:42', '2025-09-24 23:41:49'),
(19, 'ORD-20250924-35F781', 6, 55555, 6, 0, 0, '2025-09-24 16:31:10', '2025-10-14 16:49:33'),
(20, 'ORD-20250924-BAD697', 8, 1088000, 6, 0, 0, '2025-09-24 22:32:50', '2025-10-14 17:15:34'),
(21, 'ORD-20250925-51A569', 8, 98989, 6, 0, 0, '2025-09-25 08:07:36', '2025-10-14 16:57:52'),
(22, 'ORD-20250925-6C8A0F', 8, 98989, 5, 0, 0, '2025-09-25 08:17:38', '2025-10-14 16:57:40'),
(23, 'ORD-20250925-07B1E1', 8, 80582, 6, 0, 0, '2025-09-25 08:20:54', '2025-10-13 05:39:17'),
(24, 'ORD-20250925-57A5A7', 9, 98989, 6, 0, 0, '2025-09-25 10:19:12', '2025-09-25 10:20:44'),
(26, 'ORD-20251010-35B615', 8, 764900, 6, 0, 0, '2025-10-10 08:04:31', '2025-10-14 17:14:05'),
(27, 'ORD-20251010-4DD41C', 8, 149900, 6, 0, 0, '2025-10-10 08:12:26', '2025-10-10 08:23:27'),
(33, 'ORD-20251015-465419', 6, 120, 6, 0, 0, '2025-10-15 19:10:47', '2025-10-15 19:16:03'),
(34, 'ORD-20251015-E3906B', 8, 379, 6, 0, 0, '2025-10-15 19:14:06', '2025-10-15 19:16:14'),
(37, 'ORD-20251016-AC1489', 6, 120350, 0, 0, 0, '2025-10-16 10:42:51', NULL),
(38, 'ORD-20251016-0030BD', 8, 98989, 0, 0, 0, '2025-10-16 12:03:19', NULL),
(39, 'ORD-20251016-1E0033', 6, 70900, 1, 0, 0, '2025-10-16 12:21:12', '2025-10-17 11:38:46'),
(40, 'ORD-20251016-B00798', 6, 345, 1, 0, 0, '2025-10-16 12:22:46', '2025-10-17 11:34:35'),
(41, 'ORD-20251016-442171', 2, 68900, 0, 1, 1, '2025-10-16 15:41:12', NULL),
(42, 'ORD-20251016-D75FF3', 10, 48179, 0, 1, 1, '2025-10-16 21:19:17', NULL),
(43, 'ORD-20251016-9697AC', 8, 1200, 1, 1, 1, '2025-10-16 21:33:59', '2025-10-17 11:39:38'),
(44, 'ORD-20251017-4C6135', 9, 119900, 0, 1, 1, '2025-10-17 11:24:30', NULL),
(45, 'ORD-20251017-DDA6CA', 9, 133000, 5, 1, 1, '2025-10-17 11:49:19', '2025-10-17 11:52:58'),
(46, 'ORD-20251017-1C2F8B', 9, 750, 5, 1, 1, '2025-10-17 11:52:43', '2025-10-17 11:52:47'),
(47, 'ORD-20251017-A60734', 9, 150380, 5, 1, 1, '2025-10-17 12:10:10', '2025-10-17 12:10:17'),
(48, 'ORD-20251017-FDC38F', 3, 379, 0, 0, 1, '2025-10-17 13:33:56', NULL),
(49, 'ORD-20251017-D5EE66', 13, 119944, 5, 1, 1, '2025-10-17 13:47:56', '2025-10-17 13:54:51'),
(50, 'ORD-20251017-FB8996', 13, 480, 0, 0, 1, '2025-10-17 13:56:20', NULL),
(51, 'ORD-20251017-7D5D45', 13, 450, 5, 1, 1, '2025-10-17 13:57:15', '2025-10-17 13:59:22'),
(52, 'ORD-20251017-142105', 13, 450, 0, 1, 1, '2025-10-17 13:59:38', NULL),
(53, 'ORD-20251017-9CA8BE', 13, 180, 0, 1, 1, '2025-10-17 14:16:34', NULL),
(54, 'ORD-20251017-DDD822', 13, 68900, 0, 1, 1, '2025-10-17 14:18:47', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `order_status_summary`
-- (See below for the actual view)
--
CREATE TABLE `order_status_summary` (
`id` int(30)
,`ref_code` varchar(100)
,`client_id` int(30)
,`client_name` mediumtext
,`total_amount` float
,`status` tinyint(1)
,`status_text` varchar(16)
,`date_created` datetime
,`date_updated` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `or_cr_documents`
--

CREATE TABLE `or_cr_documents` (
  `id` int(30) NOT NULL,
  `client_id` int(30) NOT NULL,
  `document_type` enum('or','cr') NOT NULL,
  `document_number` varchar(100) NOT NULL,
  `plate_number` varchar(20) DEFAULT NULL,
  `vehicle_model` varchar(100) DEFAULT NULL,
  `vehicle_brand` varchar(100) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('pending','released','expired') NOT NULL DEFAULT 'pending',
  `file_path` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `or_cr_documents`
--

INSERT INTO `or_cr_documents` (`id`, `client_id`, `document_type`, `document_number`, `plate_number`, `vehicle_model`, `vehicle_brand`, `release_date`, `expiry_date`, `status`, `file_path`, `remarks`, `date_created`, `date_updated`) VALUES
(5, 8, 'cr', 'OR-2025-001234', 'ABC 1234', NULL, NULL, '2025-10-30', NULL, 'pending', 'uploads/documents/5.pdf?v=1760599170', '', '2025-10-16 15:19:29', '2025-10-16 15:19:30');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(128) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(62, 52, 'Black White', 'uploads/products/colors/52_black_white.webp?v=1758690969', '2025-09-24 13:16:09');

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
(3, 39, 'Click 125i'),
(1, 73, 'Airblade 160'),
(2, 74, 'Airblade 160'),
(4, 75, 'Airblade 150'),
(5, 76, 'Airblade 160'),
(6, 98, 'Beat'),
(7, 98, 'Click 125i'),
(8, 98, 'Click 125i SE'),
(9, 98, 'Click 160'),
(10, 99, 'Beat'),
(11, 99, 'Click 125i'),
(12, 99, 'Click 125i SE'),
(13, 99, 'Click 160'),
(14, 100, 'Beat'),
(15, 100, 'Click 125i'),
(16, 100, 'Click 125i SE'),
(17, 100, 'Click 160'),
(18, 101, 'ADV 160'),
(19, 101, 'Airblade 150'),
(20, 101, 'Click 125i'),
(21, 101, 'PCX 150'),
(22, 102, 'TMX 125 ALPHA'),
(23, 102, 'TMX SUPREMO'),
(24, 102, 'Wave RSX(DISC)'),
(25, 102, 'XRM 125 Dual Sport Fi'),
(26, 103, 'Beat'),
(27, 103, 'Click 125i'),
(28, 103, 'Click 160');

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
(11, 9, 10, 'Honda RS125', 'RS 125', 'Matte Axis Gray Metallic, Victory Red', '<p><strong>Honda RS125</strong></p><p>The New RS125, designed too dominate the road with its bold, fresh look and enhanced racing image. This powerful ride combines performance and style, making it a true street leader and standout choice. With two aggressive color variants available - Matte Axis Gray Metallic and Victory Red</p>', 75000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/11.png?v=1755083959', 0, '2025-08-13 19:19:19', '2025-10-15 10:33:49'),
(12, 9, 10, 'Honda Scoopy Slant', 'Honda', NULL, '&lt;p&gt;&quot;Compact, stylish, and fuel-efficient, the Honda Scoopy Slant is perfect for urban riders. With its modern design, comfortable seating, and reliable engine, it delivers a smooth and fun riding experience every day.&quot;&lt;/p&gt;', 72500, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/12.png?v=1755096446', 0, '2025-08-13 22:47:26', '2025-10-15 10:33:49'),
(13, 9, 10, 'Honda ADV', 'Honda', NULL, '&lt;p&gt;&quot;Adventure-ready and versatile, the Honda ADV combines rugged style with powerful performance. Ideal for city streets and off-road journeys, it features a responsive engine, comfortable ergonomics, and advanced suspension for a smooth ride anywhere.&quot;&lt;/p&gt;', 150000, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/13.png?v=1755096549', 0, '2025-08-13 22:49:09', '2025-10-15 10:33:49'),
(14, 9, 10, 'Honda Dio', 'DIO', 'Red, White, Black, Blue', '<p><strong>Honda Dio</strong></p><p>This scooter delivers exceptional power and performance perfectly fits for your commuting with Stylish looks, aesthetic design, and functional features compact in one scooter.</p>', 66500, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/14.png?v=1755096602', 0, '2025-08-13 22:50:02', '2025-10-15 10:33:49'),
(15, 9, 10, 'Honda Air Blade', 'Honda', NULL, '&lt;p data-start=&quot;58&quot; data-end=&quot;321&quot;&gt;&lt;em data-start=&quot;80&quot; data-end=&quot;319&quot;&gt;&quot;Sleek, modern, and performance-driven, the Honda Air Blade offers a smooth and powerful ride for urban commuters. With its advanced engine technology, sporty design, and comfortable ergonomics, it&rsquo;s built for both style and efficiency.&quot;&lt;/em&gt;&lt;/p&gt;', 95000, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/15.png?v=1755096659', 0, '2025-08-13 22:50:59', '2025-10-15 10:33:49'),
(16, 9, 15, 'Honda Genuine Coolant Oil', 'Honda', NULL, '&lt;p&gt;&quot;Keep your motorcycle running smoothly with Honda Genuine Coolant Oil. Engineered to maintain optimal temperature, prevent corrosion, and protect your engine for long-lasting performance.&quot;&lt;/p&gt;', 650, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/16.png?v=1755096734', 1, '2025-08-13 22:52:14', '2025-10-10 16:03:12'),
(17, 9, 15, 'Honda Scooter Fully Synthetic Oil', 'Honda', NULL, '&lt;p&gt;&quot;Premium fully synthetic engine oil specially formulated for Honda scooters. Ensures maximum engine protection, smooth performance, and extended engine life even under heavy riding conditions.&quot;&lt;/p&gt;', 450, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/17.png?v=1755096820', 1, '2025-08-13 22:53:40', '2025-10-10 16:03:18'),
(18, 9, 15, 'Honda Scooter Gear Oil', 'Honda', NULL, '&lt;p&gt;&quot;High-quality gear oil designed for Honda scooters, providing smooth gear shifts, reducing wear and tear, and ensuring long-lasting transmission performance under all riding conditions.&quot;&lt;/p&gt;', 380, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/18.png?v=1755096880', 1, '2025-08-13 22:54:40', '2025-10-10 16:03:23'),
(19, 9, 13, 'Honda Bearing Click', 'Honda', NULL, '&lt;p&gt;&quot;Precision-engineered bearing for Honda motorcycles, ensuring smooth rotation, reduced friction, and reliable performance. Perfect for maintaining your bike&rsquo;s handling and longevity.&quot;&lt;/p&gt;', 320, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/19.png?v=1755096974', 1, '2025-08-13 22:56:14', '2025-10-10 16:03:28'),
(20, 9, 13, 'Honda Click Air Filter', 'Honda', NULL, '&lt;p&gt;&quot;High-quality air filter designed for Honda Click scooters. Ensures clean airflow to the engine, improves performance, and extends engine life by keeping dust and debris out.&quot;&lt;/p&gt;', 250, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/20.png?v=1755097056', 1, '2025-08-13 22:57:36', '2025-10-10 16:03:33'),
(21, 9, 13, 'Honda Scooter Belt Drive', 'Honda', NULL, '&lt;p&gt;&quot;Durable and high-performance drive belt for Honda scooters, engineered to provide smooth power transfer, reduce slippage, and ensure reliable acceleration for daily rides.&quot;&lt;/p&gt;', 900, 'C', 20, 100, 10, 0.00, NULL, 7, 1, 'uploads/products/21.png?v=1755097150', 1, '2025-08-13 22:59:10', '2025-10-10 16:03:38'),
(22, 9, 13, 'Honda Scooter Crankshaft', 'Honda', NULL, '&lt;p&gt;&quot;Precision-engineered crankshaft for Honda scooters, designed to ensure smooth engine rotation, optimal power delivery, and long-lasting durability for reliable performance.&quot;&lt;/p&gt;', 2500, 'C', 10, 50, 5, 0.00, NULL, 7, 1, 'uploads/products/22.png?v=1755097229', 1, '2025-08-13 23:00:29', '2025-10-10 16:03:50'),
(23, 9, 10, 'Honda RS125', 'RS 125', 'Matte Axis Gray Metallic, Victory Red', '<p><strong>Honda RS125</strong></p><p>The New RS125, designed too dominate the road with its bold, fresh look and enhanced racing image. This powerful ride combines performance and style, making it a true street leader and standout choice. With two aggressive color variants available - Matte Axis Gray Metallic and Victory Red</p>', 75000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/23.png?v=1755097338', 1, '2025-08-13 23:02:18', '2025-10-10 13:31:08'),
(24, 9, 10, 'Honda Wave RSX (DISC)', 'WAVE RSX (DISC)', 'Red, White, Black', '<p><strong>Honda Wave RSX (DISC)</strong></p><p>The Wave RSX turns your riding experience into something remarkable. With its newest sporty dynamic design bringing out impressive stickers, functional features providing convenience, plus fuel efficiency upto 69.5 km/l powered by PGM-FI, this underbone lets you stands out wherever you go.</p>', 62500, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/24.png?v=1755097428', 1, '2025-08-13 23:03:48', '2025-10-10 13:31:08'),
(25, 9, 10, 'Honda PCX 160 ABS', 'PCX 160 ABS', 'Red, White, Black, Blue', '<p><strong>Honda PCX 160 ABS</strong></p><p>The Honda PCX 160 ABS is a premium maxi-scooter that blends elegant design, advanced technology, and powerful performance—perfect for both daily city rides and longer journeys. Equipped with a 157cc liquid-cooled, fuel-injected engine with eSP+ technology, it delivers smooth acceleration, impressive fuel efficiency, and a refined riding experience.</p><p>This model features Anti-Lock Braking System (ABS) and Honda Selectable Torque Control (HSTC) for enhanced safety and stability, especially on slippery roads. Its sleek LED headlight and taillight, fully digital instrument panel, and modern, aerodynamic body give it a premium and stylish appeal.</p>', 140000, 'A', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/25.png?v=1755097524', 1, '2025-08-13 23:05:24', '2025-10-10 13:31:08'),
(26, 9, 10, 'Honda Click 125i', 'CLICK 125i', 'Red, White, Black, Blue', '<p><strong>Honda Click 125i</strong></p><p>The New CLICK125 SE is powered by a 125cc Liquid-cooled, PGM-FI engine with Enhanced Smart Power and an ACG starter, making the model fuel efficient at 53 km/L. The model comes with the Combi Brake System and Park Brake Lock for added safety features.</p>', 85000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/26.png?v=1755097625', 1, '2025-08-13 23:07:05', '2025-10-10 13:31:08'),
(27, 9, 10, 'Honda Beat', 'Honda', NULL, '&lt;p&gt;&quot;Compact, fuel-efficient, and reliable, the Honda Beat is perfect for daily commuting. Its lightweight frame, smooth engine, and comfortable ergonomics make it an ideal choice for city riders.&quot;&lt;/p&gt;', 60500, 'C', 1, 20, 7, 0.00, NULL, 7, 1, 'uploads/products/27.png?v=1755097690', 1, '2025-08-13 23:08:10', '2025-09-24 13:27:12'),
(33, 9, 10, 'ADV 160', 'ADV 160', 'Red, White, Black', '&lt;table&gt;\r\n&lt;thead&gt;\r\n&lt;tr&gt;\r\n&lt;th&gt;Category&lt;/th&gt;\r\n&lt;th&gt;Specification&lt;/th&gt;\r\n&lt;/tr&gt;\r\n&lt;/thead&gt;\r\n&lt;tbody&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;&lt;strong&gt;Engine &amp;amp; Powertrain&lt;/strong&gt;&lt;/td&gt;\r\n&lt;td&gt;&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Engine type&lt;/td&gt;\r\n&lt;td&gt;Single-cylinder, 4-stroke, 4-valve, SOHC, liquid-cooled, eSP+ (&lt;a href=&quot;https://www.topgear.com.ph/moto-sapiens/motorcycle-news/honda-adv-160-2023-ph-launch-a4354-20221014?utm_source=chatgpt.com&quot; title=&quot;Honda ADV 160 2023 unveiled in PH: Prices, Specs, Features&quot;&gt;https://www.topgear.com.ph&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Displacement&lt;/td&gt;\r\n&lt;td&gt;~157 cc (&lt;a href=&quot;https://www.topgear.com.ph/moto-sapiens/motorcycle-news/honda-adv-160-2023-ph-launch-a4354-20221014?utm_source=chatgpt.com&quot; title=&quot;Honda ADV 160 2023 unveiled in PH: Prices, Specs, Features&quot;&gt;https://www.topgear.com.ph&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Bore &times; Stroke&lt;/td&gt;\r\n&lt;td&gt;60.0 mm &times; 55.5 mm (&lt;a href=&quot;https://slmmotor.com/product/honda-adv-160/?utm_source=chatgpt.com&quot; title=&quot;HONDA ADV 160 - SLM Motorport&quot;&gt;SLM Motor&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Compression ratio&lt;/td&gt;\r\n&lt;td&gt;12.0 : 1 (&lt;a href=&quot;https://slmmotor.com/product/honda-adv-160/?utm_source=chatgpt.com&quot; title=&quot;HONDA ADV 160 - SLM Motorport&quot;&gt;SLM Motor&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Max power&lt;/td&gt;\r\n&lt;td&gt;~ &lt;strong&gt;11.8 kW&lt;/strong&gt; @ 8,500 rpm (&asymp; 16 PS) (&lt;a href=&quot;https://www.topgear.com.ph/moto-sapiens/motorcycle-news/honda-adv-160-2023-ph-launch-a4354-20221014?utm_source=chatgpt.com&quot; title=&quot;Honda ADV 160 2023 unveiled in PH: Prices, Specs, Features&quot;&gt;https://www.topgear.com.ph&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Max torque&lt;/td&gt;\r\n&lt;td&gt;~ &lt;strong&gt;14.7 Nm&lt;/strong&gt; @ 6,500 rpm (&lt;a href=&quot;https://www.topgear.com.ph/moto-sapiens/motorcycle-news/honda-adv-160-2023-ph-launch-a4354-20221014?utm_source=chatgpt.com&quot; title=&quot;Honda ADV 160 2023 unveiled in PH: Prices, Specs, Features&quot;&gt;https://www.topgear.com.ph&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Fuel system&lt;/td&gt;\r\n&lt;td&gt;PGM-FI (Programmed Fuel Injection) (&lt;a href=&quot;https://premiumbikes.ph/product/honda-adv160ap/?utm_source=chatgpt.com&quot; title=&quot;Honda ADV160AP - Premium Adventure Scooter | Premiumbikes&quot;&gt;Premiumbikes&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Ignition system&lt;/td&gt;\r\n&lt;td&gt;Full transistorized (&lt;a href=&quot;https://motorlandia.com.ph/motorcycle/honda-adv-160/?utm_source=chatgpt.com&quot; title=&quot;HONDA ADV 160 &ndash; Motorlandia&quot;&gt;Motorlandia&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr&gt;\r\n&lt;td&gt;Starter&lt;/td&gt;\r\n&lt;td&gt;Electric (ACG starter) (&lt;a href=&quot;https://motorlandia.com.ph/motorcycle/honda-adv-160/?utm_source=chatgpt.com&quot; title=&quot;HONDA ADV 160 &ndash; Motorlandia&quot;&gt;Motorlandia&lt;/a&gt;)&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;/tbody&gt;&lt;/table&gt;', 166900, 'A', 3, 66, 1, 9999.00, NULL, 7, 1, 'uploads/products/33.webp?v=1758619970', 1, '2025-09-23 17:32:50', '2025-10-16 10:59:02'),
(34, 9, 10, 'Click 160', 'Click 160', 'Silver Black, Red Black, Black Orange', '&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Make&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;HONDA - The Power of Dreams&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Model&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;HONDA CLICK 160&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Transmission&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Automatic&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Ignition Type&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Full Transisterized&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Engine Type&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;4-Stroke, 4-Valve, SOHC, Liquid Cooled, eSP+&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Seat Height&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;778 mm&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Brake System (Front / Rear)&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Hydraulic Disc / Mechanical Leading Trailing&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Fuel Capacity (L)&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;5.5 L&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Displacement (cc)&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;157cc&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Engine Oil Capacity (L)&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;0.9L&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Front Tire&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;100/80-14 M/C 48P (Tubeless)&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Wheels Type&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Cast Wheel&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Starting System&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Electric (ACG Starter)&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;Maximum Horse Power&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:4pt;&quot;&gt;&lt;span id=&quot;docs-internal-guid-1bed71b2-7fff-9f92-aeeb-11c8b3d24235&quot;&gt;&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:11pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;11.3kW @ 8,500rpm&lt;/span&gt;&lt;/p&gt;', 122900, 'C', 3, 200, 1, 99.00, NULL, 14, 1, 'uploads/products/34.webp?v=1758688171', 0, '2025-09-24 12:29:31', '2025-10-15 08:20:58'),
(35, 9, 10, 'TMX 125 ALPHA', 'TMX 125 ALPHA', 'Black, Red, Gray', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;124.9cc air-cooled, 4-speed manual, Workhorse underbone, Drum Brakes&lt;/span&gt;&lt;/p&gt;', 68900, 'C', 20, 300, 1, 8000.00, NULL, 21, 1, 'uploads/products/35.jpg?v=1758688317', 0, '2025-09-24 12:31:57', '2025-09-25 10:50:55'),
(36, 9, 10, 'XRM 125 Dual Sport Fi', 'XRM 125 Dual Sport Fi', 'Yellow, Green, Blue, Red', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;125cc air-cooled, SOHC, 4-speed semi-auto, On/Off-road utility&lt;/span&gt;&lt;/p&gt;', 77900, 'C', 30, 100, 10, 777.00, NULL, 7, 1, 'uploads/products/36.webp?v=1758688526', 1, '2025-09-24 12:35:26', '2025-10-13 20:39:23');
INSERT INTO `product_list` (`id`, `brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`, `date_updated`) VALUES
(37, 9, 10, 'Airblade 160', 'Airblade 160', 'Red, Gray, Dark Blue', '&lt;table class=&quot;table table-bordered&quot; style=&quot;caption-side: bottom; --bs-table-color-type: initial; --bs-table-bg-type: initial; --bs-table-color-state: initial; --bs-table-bg-state: initial; --bs-table-color: var(--bs-body-color); --bs-table-bg: transparent; --bs-table-border-color: var(--bs-border-color); --bs-table-accent-bg: transparent; --bs-table-striped-color: #212529; --bs-table-striped-bg: rgba(0, 0, 0, 0.05); --bs-table-active-color: #212529; --bs-table-active-bg: rgba(0, 0, 0, 0.1); --bs-table-hover-color: #212529; --bs-table-hover-bg: rgba(0, 0, 0, 0.075); width: 593px; vertical-align: top; color: rgb(33, 37, 41); background-color: rgb(255, 255, 255); font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;tbody style=&quot;border-style: solid; border-width: 0px; vertical-align: inherit;&quot;&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;CATEGORY&lt;/span&gt;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;SPECIFICATIONS&lt;/span&gt;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;ENGINE TYPE&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;4-Stroke, Liquid-Cooled, Single Overhead Cam (SOHC)&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;DISPLACEMENT&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;157 cc&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;TRANSMISSION TYPE&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;V - Belt Automatic&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span id=&quot;docs-internal-guid-daca6b0c-7fff-0d3e-5392-92624ce51818&quot;&gt;&lt;span style=&quot;font-size: 10pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;STARTING SYSTEM &lt;/font&gt;&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Electric (ACG Starter)&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;POWER OUTLET&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot; face=&quot;Arial&quot;&gt;Equipped&lt;/font&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;SUSPENSION TYPE (Front)&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Telescopic&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;SUSPENSION TYPE (Rear)&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Twin Shock&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;BRAKE TYPE (Front)&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;Hydraulic Disc with ABS&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;BRAKE TYPE (Rear)&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Mechanical Leading Trailing&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;TIRE SIZE (Front)&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;90/80-14 M/C&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;TIRE SIZE (Rear)&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;100/80 - 14 M/C&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;WHEEL TYPE&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Cast Wheel&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;WHEEL BASE&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;1,286 mm&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;OVERALL DIMENSION (LxWxH)&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;1,890 x 686 x 1,116(mm)&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;SEAT HEIGHT&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;775 mm&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;GROUND CLEARANCE&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;138 mm&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;FUEL TANK CAPACITY (L)&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;4.4 L&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;FUEL CONSUMPTION&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;47 km/L (PGM-FI)&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;ENGINE OIL CAPACITY&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;0.9 L&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;BATTERY TYPE&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;12V - 5Ah (MF-WET)&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;FUEL CONSUMPTION&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;47 km/L&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;MAXIMUM POWER&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;11.2 kW @ 8,000 rpm&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;MAXIMUM TORQUE&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;14.6 N.m @ 6,500 rpm&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;&lt;p style=&quot;font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;p style=&quot;font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 18px;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;INSTALLMENT PLAN&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;table class=&quot;table table-bordered&quot; style=&quot;caption-side: bottom; --bs-table-color-type: initial; --bs-table-bg-type: initial; --bs-table-color-state: initial; --bs-table-bg-state: initial; --bs-table-color: var(--bs-body-color); --bs-table-bg: transparent; --bs-table-border-color: var(--bs-border-color); --bs-table-accent-bg: transparent; --bs-table-striped-color: #212529; --bs-table-striped-bg: rgba(0, 0, 0, 0.05); --bs-table-active-color: #212529; --bs-table-active-bg: rgba(0, 0, 0, 0.1); --bs-table-hover-color: #212529; --bs-table-hover-bg: rgba(0, 0, 0, 0.075); width: 673.013px; vertical-align: top; color: rgb(33, 37, 41); background-color: rgb(255, 255, 255); font-family: Arial, Helvetica, sans-serif; text-align: center;&quot;&gt;&lt;tbody style=&quot;border-style: solid; border-width: 0px; vertical-align: inherit;&quot;&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;DOWN PAYMENT&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;12 MONTHS&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;18 MONTHS&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;24 MONTHS&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;30 MONTHS&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;36 MONTHS&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;48 MONTHS&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;9,000&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;13,965&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;10,415&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;8,300&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;7,300&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;6,700&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;N/A&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;12,700&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;13,490&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;9,576&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;8,100&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;p&gt;7,074&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;6,500&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;5,600&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;19,100&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;12,701&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;9,327&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;7,543&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;6,333&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;5,950&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;5,100&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;25,400&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;11,977&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;8,761&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;7,037&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;6,109&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;5,700&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;4,900&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;38,100&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;10,376&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;7,742&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;6,224&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;5,406&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;5,100&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;4,500&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;', 149900, 'A', 30, 100, 10, 300.00, NULL, 14, 1, 'uploads/products/37.jpg?v=1758688624', 0, '2025-09-24 12:37:04', '2025-10-16 15:55:56');
INSERT INTO `product_list` (`id`, `brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`, `date_updated`) VALUES
(38, 9, 10, 'Beat', 'Beat', 'Street STD Black, Street STD Gray, Black Premium, White Premium, Playful Yellow, Playful Red, Playful Gray, Playful Blue, Fashion Sport STD Black Red, Fashion Sport STD Black Orange, Fashion Sport STD, Beat 110 Premium 2, Honda Beat Limited Edition', '&lt;p style=&quot;-webkit-font-smoothing: antialiased; font-family: -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, &amp;quot;Helvetica Neue&amp;quot;, Arial, &amp;quot;Noto Sans&amp;quot;, &amp;quot;Liberation Sans&amp;quot;, sans-serif, &amp;quot;Apple Color Emoji&amp;quot;, &amp;quot;Segoe UI Emoji&amp;quot;, &amp;quot;Segoe UI Symbol&amp;quot;, &amp;quot;Noto Color Emoji&amp;quot;;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 18px;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Introducing The New BeAT &ndash; a vibrant fusion of style, fun, and functionality that&#039;s perfect for every ride!&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;-webkit-font-smoothing: antialiased; font-family: -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, &amp;quot;Helvetica Neue&amp;quot;, Arial, &amp;quot;Noto Sans&amp;quot;, &amp;quot;Liberation Sans&amp;quot;, sans-serif, &amp;quot;Apple Color Emoji&amp;quot;, &amp;quot;Segoe UI Emoji&amp;quot;, &amp;quot;Segoe UI Symbol&amp;quot;, &amp;quot;Noto Color Emoji&amp;quot;;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 18px;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;With its New Gold Emblem for Premium Type and New Sticker Design for Playful Type, this scooter is designed for those who value both sophistication and fun. It boasts with 110 cc, 4-Stroke, SOHC, Air-Cooled, eSP engine that ensures a smooth and comfortable ride while maximizing fuel efficiency. This comes with innovative features like Semi-Digital Meter Panel with Eco Indicator, and Combined Braking System (CBS), promising a ride that prioritizes convenience and safety on every journey.&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;div dir=&quot;ltr&quot; align=&quot;left&quot; style=&quot;font-family: Arial, Helvetica, sans-serif; margin-left: 0pt;&quot;&gt;&lt;table style=&quot;caption-side: bottom; border: none;&quot;&gt;&lt;colgroup&gt;&lt;col width=&quot;247&quot;&gt;&lt;col width=&quot;372&quot;&gt;&lt;/colgroup&gt;&lt;tbody style=&quot;border-style: solid; border-width: 0px;&quot;&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;CATEGORY&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;SPECIFICATIONS&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 56.5pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;ENGINE TYPE&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;4-Stroke, Air-Cooled, Single Overhead Cam (SOHC), eSP&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;DISPLACEMENT&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;110 cc&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;TRANSMISSION TYPE&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Automatic (V-Matic)&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;BORE &amp;amp; STROKE&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;47.0 x 63.1 mm&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;IGNITION TYPE&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Full Transisterized&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;STARTING SYSTEM&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Electric &amp;amp; Kick (ACG Starter)&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;COMPRESSION RATIO&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;10.0 : 1&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;SUSPENSION TYPE (Front)&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Telescopic&amp;nbsp; Fork&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;SUSPENSION TYPE (Rear)&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Mono Shock - Unit Swing&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;BRAKE TYPE (Front)&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Hydraulic Disc&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;BRAKE TYPE (Rear)&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Mechanical Leading Trailing&amp;nbsp;Drum&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;TIRE SIZE (Front)&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;80/90 - 14 M/C 40P (Tubeless)&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;TIRE SIZE (Rear)&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;90/90 14 M/C 46P (Tubeless)&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;WHEEL TYPE&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Cast Wheel&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;WHEEL BASE&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;1,255 mm&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 56.5pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;OVERALL DIMENSION (LxWxH)&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;1,877 x 669 x 1,074 mm&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;SEAT HEIGHT&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;742 mm&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;GROUND CLEARANCE&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;147 mm&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;FUEL TANK CAPACITY (L)&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;4.2 L&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;FUEL CONSUMPTION&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;58.2 km/L&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;ENGINE OIL CAPACITY&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;0.8 L&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;BATTERY TYPE&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;12V - 3Ah (MF-WET)&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;CURB WEIGHT&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;89 kg&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;MAXIMUM POWER&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;6.63 kW @ 7,500 rpm&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 42.25pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;MAXIMUM TORQUE&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 12pt;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;9.30 Nm @ 6,000 rpm&lt;/font&gt;&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;&lt;/div&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 12pt; font-family: Arial, Helvetica, sans-serif; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 13.5pt; font-family: Arial, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;br&gt;&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 12pt; font-family: Arial, Helvetica, sans-serif; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 13.5pt; font-family: Arial, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;INSTALLMENT PLAN&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 12pt; font-family: Arial, Helvetica, sans-serif; line-height: 1.38;&quot;&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 12pt; font-family: Arial, Helvetica, sans-serif; line-height: 1.38;&quot;&gt;&lt;span style=&quot;font-size: 13.5pt; font-family: Arial, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;span id=&quot;docs-internal-guid-cf57e646-7fff-ad54-4a65-8c012048ad73&quot;&gt;&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;div dir=&quot;ltr&quot; align=&quot;left&quot; style=&quot;font-family: Arial, Helvetica, sans-serif; margin-left: 0pt;&quot;&gt;&lt;br&gt;&lt;span id=&quot;docs-internal-guid-64c5a286-7fff-5ff3-49ea-5f61ebd23768&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 12pt; line-height: 1.38;&quot;&gt;&lt;/p&gt;&lt;div dir=&quot;ltr&quot; align=&quot;left&quot; style=&quot;margin-left: 0pt;&quot;&gt;&lt;table style=&quot;caption-side: bottom; border: none;&quot;&gt;&lt;colgroup&gt;&lt;col width=&quot;92&quot;&gt;&lt;col width=&quot;89&quot;&gt;&lt;col width=&quot;89&quot;&gt;&lt;col width=&quot;89&quot;&gt;&lt;col width=&quot;89&quot;&gt;&lt;col width=&quot;89&quot;&gt;&lt;col width=&quot;89&quot;&gt;&lt;/colgroup&gt;&lt;tbody style=&quot;border-style: solid; border-width: 0px;&quot;&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px; height: 61pt;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38; text-align: center;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Roboto, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;DOWN PAYMENT&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38; text-align: center;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Roboto, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;12 MONTHS&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38; text-align: center;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Roboto, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;18 MONTHS&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38; text-align: center;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Roboto, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;24 MONTHS&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38; text-align: center;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Roboto, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;30 MONTHS&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38; text-align: center;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Roboto, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;36 MONTHS&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;&lt;p dir=&quot;ltr&quot; style=&quot;margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38; text-align: center;&quot;&gt;&lt;span style=&quot;font-size: 12pt; font-family: Roboto, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;48 MONTHS&lt;/span&gt;&lt;/p&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;3,800&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;9,085&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;6,818&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;5,654&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;4,973&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;4,495&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;N/A&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;5,600&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;8,518&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;6,373&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;5,268&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;4,637&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;4,208&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;N/A&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;7,500&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;8,229&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;6,076&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;4,974&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;4,353&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;3,929&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;3,441&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;11,200&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;7,770&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;5,727&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;4,641&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;4,048&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;3,644&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;3,174&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 0px;&quot;&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;14,900&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;7,344&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;5,393&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;4,348&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;3,785&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;3,400&lt;/td&gt;&lt;td style=&quot;border-color: rgb(222, 226, 230); border-style: solid; border-width: 0.6pt; text-align: center; vertical-align: middle; padding: 5pt; overflow: hidden; overflow-wrap: break-word;&quot;&gt;2,952&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;&lt;/div&gt;&lt;/span&gt;&lt;/div&gt;', 98989, 'A', 50, 500, 100, 98000.00, NULL, 28, 1, 'uploads/products/38.jpg?v=1758688996', 0, '2025-09-24 12:43:16', '2025-10-16 15:57:33');
INSERT INTO `product_list` (`id`, `brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`, `date_updated`) VALUES
(39, 9, 10, 'Click 125i SE', 'Click 125i SE', 'White, Red, Gray, Blue White', '&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;margin-top:0pt;margin-bottom:0pt;&quot;&gt;&lt;span style=&quot;font-size:10pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;The New CLICK125 SE&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span id=&quot;docs-internal-guid-1dacdc92-7fff-0ed5-6f3f-0f6083c5c107&quot;&gt;&lt;/span&gt;&lt;/p&gt;&lt;p dir=&quot;ltr&quot; style=&quot;line-height:1.38;background-color:#ffffff;margin-top:0pt;margin-bottom:19pt;&quot;&gt;&lt;span style=&quot;font-size:10pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;&quot;&gt;is powered by a 125cc Liquid-cooled, PGM-FI engine with Enhanced Smart Power and an ACG starter, making the model fuel efficient at 53 km/L. The model comes with the Combi Brake System and Park Brake Lock for added safety features.&lt;/span&gt;&lt;/p&gt;', 78900, 'B', 20, 300, 10, 9000.00, NULL, 7, 1, 'uploads/products/39.webp?v=1758689161', 0, '2025-09-24 12:46:01', '2025-10-15 08:16:03'),
(40, 9, 10, 'Giorno+', 'Giorno+', 'Black, Orange, White, Khaki', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 44444, 'C', 20, 300, 5, 1000.00, NULL, 7, 1, 'uploads/products/40.webp?v=1758689276', 0, '2025-09-24 12:47:56', '2025-09-24 16:11:23'),
(41, 9, 10, 'RS 125', 'RS 125', 'Black Red, Black Blue, Black', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 88888, 'C', 30, 200, 10, 5000.00, NULL, 7, 1, 'uploads/products/41.jpg?v=1758689364', 0, '2025-09-24 12:49:24', '2025-09-24 16:11:23'),
(42, 9, 10, 'Supra GTR 150', 'Supra GTR 150', 'Black, Red', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 76152, 'C', 20, 100, 5, 9999.00, NULL, 7, 1, 'uploads/products/42.webp?v=1758689445', 0, '2025-09-24 12:50:45', '2025-10-01 10:09:57'),
(43, 9, 10, 'Wave RSX (DISC) ', 'Wave RSX(DISC)', 'Black', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;110cc air-cooled, 4-speed semi-auto, Front Disc Brake, Economy-focused&lt;/span&gt;&lt;/p&gt;', 64400, 'C', 3, 10, 5, 8989.00, NULL, 7, 1, 'uploads/products/43.webp?v=1758689529', 0, '2025-09-24 12:52:09', '2025-09-25 10:49:08'),
(44, 9, 10, 'PCX 160 CBS', 'PCX 160 CBS', 'Red, White, Black', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 131900, 'C', 5, 30, 1, 777.00, NULL, 7, 1, 'uploads/products/44.jpg?v=1758689721', 0, '2025-09-24 12:55:21', '2025-09-25 10:45:03'),
(45, 9, 10, 'PCX 160 ABS', 'PCX 160 ABS', 'Black, White, Red', '&lt;p&gt;&lt;br&gt;&lt;span class=&quot;&quot; data-state=&quot;closed&quot;&gt;&lt;/span&gt;&lt;/p&gt;', 149900, 'C', 5, 20, 1, 99.00, NULL, 7, 1, 'uploads/products/45.jpg?v=1758689859', 0, '2025-09-24 12:57:39', '2025-10-10 12:06:12'),
(46, 9, 10, 'PCX 150', 'PCX 150', 'White, Black', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 87766, 'C', 3, 10, 1, 88.00, NULL, 7, 1, 'uploads/products/46.webp?v=1758689984', 0, '2025-09-24 12:59:44', '2025-09-24 16:11:23'),
(47, 9, 10, 'Airblade 150', 'Airblade 150', 'Blue, Black, Red', '&lt;p style=&quot;font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 18px;&quot;&gt;﻿&lt;/span&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span id=&quot;docs-internal-guid-ad936fcd-7fff-c74b-74b1-c596bce50e4f&quot;&gt;&lt;span style=&quot;font-size: 18px; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;The All-New Airblade150 is designed as &ldquo;The Cutting Edge,&rdquo; aimed to bring a new level of riding experience for young riders, looking for an AT Bike with a standout look, power and cutting-edge comfort in shared riding. The All-New Airblade150 is packed with a powerful 150cc SOHC liquid-cooled engine for superior function in stop-and-go situations and prime acceleration without lag for &lt;/span&gt;&lt;/span&gt;&lt;span style=&quot;font-family: Arial; font-size: 18px; white-space-collapse: preserve;&quot;&gt;hassle-free long rides. This AT Bike also offers a 22.7L U-Box that can accommodate full-faced helmets and enough room for the rider&rsquo;s personal belongings. It is also equipped with other cutting-edge features, such as Smart Key, Power Socket for recharging devices and smartphones, and a front-wheel Anti-lock Braking System (ABS) which helps determine and prevent wheels from locking up.&lt;/span&gt;&lt;/font&gt;&lt;/p&gt;&lt;div style=&quot;font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;span style=&quot;color: rgb(86, 86, 86); font-family: Montserrat, sans-serif; font-size: 11.5pt; white-space-collapse: preserve;&quot;&gt;&lt;br&gt;&lt;/span&gt;&lt;/div&gt;&lt;p style=&quot;font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;p style=&quot;font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;p style=&quot;font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;p style=&quot;font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;/p&gt;&lt;p style=&quot;font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;/p&gt;&lt;p style=&quot;font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;/p&gt;&lt;p style=&quot;font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;/p&gt;&lt;table class=&quot;table table-bordered&quot; style=&quot;caption-side: bottom; --bs-table-color-type: initial; --bs-table-bg-type: initial; --bs-table-color-state: initial; --bs-table-bg-state: initial; --bs-table-color: var(--bs-body-color); --bs-table-bg: transparent; --bs-table-border-color: var(--bs-border-color); --bs-table-accent-bg: transparent; --bs-table-striped-color: #212529; --bs-table-striped-bg: rgba(0, 0, 0, 0.05); --bs-table-active-color: #212529; --bs-table-active-bg: rgba(0, 0, 0, 0.1); --bs-table-hover-color: #212529; --bs-table-hover-bg: rgba(0, 0, 0, 0.075); width: 593px; vertical-align: top; color: rgb(33, 37, 41); background-color: rgb(255, 255, 255); font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;tbody style=&quot;border-style: solid; border-width: 0px; vertical-align: inherit;&quot;&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;CATEGORY&lt;/span&gt;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;SPECIFICATIONS&lt;/span&gt;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;ENGINE TYPE&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;4-Stroke, Liquid-Cooled, Single Overhead Cam (SOHC)&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;DISPLACEMENT&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;150 cc&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;TRANSMISSION TYPE&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;V - Belt Automatic&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span id=&quot;docs-internal-guid-daca6b0c-7fff-0d3e-5392-92624ce51818&quot;&gt;&lt;span style=&quot;font-size: 10pt; font-family: Arial; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;STARTING SYSTEM &lt;/font&gt;&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Electric (ACG Starter)&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;POWER OUTLET&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot; face=&quot;Arial&quot;&gt;Equipped&lt;/font&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;SUSPENSION TYPE (Front)&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Telescopic&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;SUSPENSION TYPE (Rear)&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Twin Shock&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;BRAKE TYPE (Front)&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;Hydraulic Disc with ABS&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;BRAKE TYPE (Rear)&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Mechanical Leading Trailing&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;TIRE SIZE (Front)&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;90/80-14 M/C&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;TIRE SIZE (Rear)&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;100/80 - 14 M/C&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;WHEEL TYPE&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;Cast Wheel&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;WHEEL BASE&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;1,286 mm&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;OVERALL DIMENSION (LxWxH)&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;1,870 x 686 x 1,112 (mm)&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;SEAT HEIGHT&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;773 mm&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;GROUND CLEARANCE&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;139 mm&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;FUEL TANK CAPACITY (L)&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;4.4 L&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;FUEL CONSUMPTION&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;PGM-FI&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;ENGINE OIL CAPACITY&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;0.9 L&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;BATTERY TYPE&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;12V - 5Ah (MF-WET)&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;FUEL CONSUMPTION&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;47 km/L&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;MAXIMUM POWER&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;9.6 kW @ 8,500 rpm&amp;nbsp;&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;MAXIMUM TORQUE&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-family: Arial;&quot;&gt;&lt;font color=&quot;#000000&quot;&gt;13.3 N.m @ 5,000 rpm&lt;/font&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;&lt;p style=&quot;font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;p style=&quot;font-family: Arial, Helvetica, sans-serif;&quot;&gt;&lt;span style=&quot;font-family: Arial; font-size: 18px;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;INSTALLMENT PLAN&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;table class=&quot;table table-bordered&quot; style=&quot;caption-side: bottom; --bs-table-color-type: initial; --bs-table-bg-type: initial; --bs-table-color-state: initial; --bs-table-bg-state: initial; --bs-table-color: var(--bs-body-color); --bs-table-bg: transparent; --bs-table-border-color: var(--bs-border-color); --bs-table-accent-bg: transparent; --bs-table-striped-color: #212529; --bs-table-striped-bg: rgba(0, 0, 0, 0.05); --bs-table-active-color: #212529; --bs-table-active-bg: rgba(0, 0, 0, 0.1); --bs-table-hover-color: #212529; --bs-table-hover-bg: rgba(0, 0, 0, 0.075); width: 673.013px; vertical-align: top; color: rgb(33, 37, 41); background-color: rgb(255, 255, 255); font-family: Arial, Helvetica, sans-serif; text-align: center;&quot;&gt;&lt;tbody style=&quot;border-style: solid; border-width: 0px; vertical-align: inherit;&quot;&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;DOWN PAYMENT&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;12 MONTHS&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;18 MONTHS&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;24 MONTHS&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;30 MONTHS&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;36 MONTHS&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;span style=&quot;font-weight: bolder;&quot;&gt;48 MONTHS&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;8,400&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;12,303&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;9,181&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;7,576&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;6,655&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;6,032&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;N/A&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;11,100&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;11,889&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;8,755&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;7,150&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;&lt;p&gt;6,246&lt;/p&gt;&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;5,630&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;4,918&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;16,700&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;11,198&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;8,229&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;6,652&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;5,791&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;5,203&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;4,521&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;22,200&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;10565&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;7,734&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;6,217&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;5,401&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;4,843&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;4,193&lt;/td&gt;&lt;/tr&gt;&lt;tr style=&quot;border-style: solid; border-width: 1px 0px;&quot;&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;33,300&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;9,341&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;6,844&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;5,506&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;4,786&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;4,294&lt;/td&gt;&lt;td style=&quot;border-top-width: 0px; border-bottom-width: 0px; padding: 0.5rem; color: var(--bs-table-color-state,var(--bs-table-color-type,var(--bs-table-color))); background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); vertical-align: top;&quot;&gt;3,721&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;', 119900, 'A', 3, 20, 1, 89.00, NULL, 7, 1, 'uploads/products/47.webp?v=1758690346', 0, '2025-09-24 13:05:46', '2025-10-16 15:58:20');
INSERT INTO `product_list` (`id`, `brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`, `date_updated`) VALUES
(48, 9, 10, 'XR 150i', 'XR 150i', 'Red, Black', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 87123, 'C', 6, 10, 1, 9987.00, NULL, 7, 1, 'uploads/products/48.webp?v=1758690717', 0, '2025-09-24 13:11:57', '2025-09-25 10:35:01'),
(49, 9, 10, 'TMX SUPREMO', 'TMX SUPREMO', 'Red, Black', '&lt;p&gt;&lt;strong&gt;Complete Motorcycle&lt;/strong&gt;&lt;/p&gt;\r\n        &lt;p&gt;Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:&lt;/p&gt;\r\n        &lt;ul&gt;\r\n            &lt;li&gt;Powerful engine for smooth acceleration&lt;/li&gt;\r\n            &lt;li&gt;Durable frame and suspension for stability&lt;/li&gt;\r\n            &lt;li&gt;Fuel-efficient design for longer rides&lt;/li&gt;\r\n            &lt;li&gt;Modern styling with aerodynamic design&lt;/li&gt;\r\n            &lt;li&gt;Equipped with essential safety features like brakes and lights&lt;/li&gt;\r\n        &lt;/ul&gt;\r\n        &lt;p&gt;Perfect choice for both daily commuting and weekend adventures.&lt;/p&gt;', 45345, 'C', 5, 10, 1, 787.00, NULL, 7, 1, 'uploads/products/49.webp?v=1758690801', 0, '2025-09-24 13:13:21', '2025-09-24 16:11:23'),
(51, 9, 10, 'DIO', 'DIO', 'Red Black, Black', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;110cc air-cooled, CVT, CBS, Practical and fuel-efficient&lt;/span&gt;&lt;/p&gt;', 70900, 'C', 15, 20, 10, 989.00, NULL, 21, 1, 'uploads/products/51.webp?v=1758690887', 0, '2025-09-24 13:14:47', '2025-09-25 10:50:30'),
(52, 9, 10, 'CRF150L', 'CRF150L', 'Red Black, Black White', '&lt;p&gt;&lt;span style=&quot;color: rgb(249, 250, 251); font-family: quote-cjk-patch, Inter, system-ui, -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, Oxygen, Ubuntu, Cantarell, &amp;quot;Open Sans&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, sans-serif; font-size: 15px; background-color: rgb(21, 21, 23);&quot;&gt;149.15cc air-cooled, 5-speed manual, Enduro/Trail bike, Electric Start&lt;/span&gt;&lt;/p&gt;', 133000, 'C', 5, 10, 1, 8776.00, NULL, 7, 1, 'uploads/products/52.webp?v=1758690969', 0, '2025-09-24 13:16:09', '2025-09-25 10:50:00'),
(53, 9, 10, 'HONDA CLICK 125i', 'CLICK 125i', 'Red, White, Black, Blue', '<p><strong>HONDA CLICK 125i</strong></p><p>The New CLICK125 SE is powered by a 125cc Liquid-cooled, PGM-FI engine with Enhanced Smart Power and an ACG starter, making the model fuel efficient at 53 km/L. The model comes with the Combi Brake System and Park Brake Lock for added safety features.</p>', 75000, 'B', 5, 20, 2, 65000.00, NULL, 7, 1, 'uploads/products/click_125i.png', 1, '2025-10-10 10:15:52', '2025-10-10 10:19:41'),
(54, 9, 10, 'HONDA CLICK 125i', 'CLICK 125i', 'Red, White, Black, Blue', '<p><strong>HONDA CLICK 125i</strong></p><p>The New CLICK125 SE is powered by a 125cc Liquid-cooled, PGM-FI engine with Enhanced Smart Power and an ACG starter, making the model fuel efficient at 53 km/L. The model comes with the Combi Brake System and Park Brake Lock for added safety features.</p>', 75000, 'B', 5, 20, 2, 65000.00, NULL, 7, 1, 'uploads/products/click_125i.png', 1, '2025-10-10 10:16:18', '2025-10-10 10:19:32'),
(55, 9, 10, 'Honda Click 160', 'CLICK 160', 'Red, White, Black, Blue', '<p><strong>Honda Click 160</strong></p><p>The New CLICK160, now featuring a bold, dynamic, and aggressive stripe design that demands attention on the road and ensures you stand out with its innovative aesthetics.</p>', 95000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/honda_click_160.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:12'),
(56, 9, 10, 'Honda CRF150L', 'CRF150L', 'Red, White', '<p><strong>Honda CRF150L</strong></p><p>Break your limitations and explore the world through The New CRF150L. Combined with powerful 149cc 4-Stroke, 2 Valves, SOHC, Air-cooled, PGM-Fi engine, advanced features such as digital meter panel, plus Showa brand Inverted Front Fork and Pro-Link Rear Suspension, and a lower Seat Height (863 mm) suitable for Filipino market. This motorcycle gives an excellent fuel efficiency up to 45.5 km/L, so you\'re sure to go further than your daily ride.</p>', 120000, 'A', 3, 15, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_crf150l.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:31'),
(57, 9, 10, 'Honda PCX 150', 'PCX 150', 'Red, White, Black, Blue', '<p><strong>Honda PCX 150</strong></p><p>The Honda PCX 150 is a premium, stylish, and fuel-efficient scooter designed for both city commuting and longer rides. Known for its sleek and modern design, it features an aerodynamic body, LED lighting, and a comfortable step-through frame that gives it a sophisticated yet sporty look.</p><p>Powered by a 149cc liquid-cooled, fuel-injected engine, the PCX 150 delivers smooth acceleration and reliable performance while maintaining excellent fuel economy. Its smart key system, digital LCD display, and ample under-seat storage make it both convenient and practical for daily use.</p>', 135000, 'A', 3, 15, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_pcx150.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:40'),
(58, 9, 10, 'Honda ADV 160 CBS', 'ADV 160 CBS', 'Red, White, Black', '<p><strong>Honda ADV 160 CBS</strong></p><p>Bringing elegance and superiority to the next level, PCX160 lets Filipino riders to stand out on the road and ride with pride with its all-new premium and elegant design, improved driving performance with comfortable and spacious riding, and the latest technology and security features.</p>', 155000, 'A', 3, 15, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_adv160_cbs.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:02:24'),
(59, 9, 10, 'ADV 160 ABS', 'ADV 160', 'Red, White, Black', '&lt;p&gt;&lt;strong&gt;Honda ADV 160 ABS&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;The ADV160 is now equipped with a new generation 157cc, 4-Valve, Liquid-Cooled, eSP+ Engine, offering advanced technology with 4-valve mechanism and low friction technologies to provide excellent power output and environmental performance (Fuel Efficient). It delivers a maximum power of 11.8 kW @ 8,500 rpm and a top torque of 14.7 Nm @ 6,500 rpm, which proves more than enough for a reliable ride that takes you from daily commuting to leisure trips.&lt;/p&gt;', 165000, 'A', 3, 15, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_adv160_abs.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:21'),
(60, 9, 10, 'Honda Supra GTR 150', 'SUPRA GTR 150', 'Red, White, Black', '<p><strong>Honda Supra GTR 150</strong></p><p>Honda Supra GTR150 is equipped with a 6-Speed DOHC 4-Valve Liquid-Cooled Engine for maximum performance, great handling, and better fuel efficiency of 42 km/liter when riding in highways. It also has a LED Headlight that ensures safety and clear sight on the road, as well as a Full Digital Meter Panel for ease of information in determining speed and distance.</p>', 110000, 'A', 3, 15, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_supra_gtr150.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:49'),
(61, 9, 10, 'Honda Giorno', 'GIORNO', 'Red, White, Black, Blue', '<p><strong>Honda Giorno</strong></p><p>The All-New Giorno+ is designed adapted to fashion-forward Filipino customers that perfectly blends modern classic design with exceptional performance and innovative features with its 125cc, 4-Valve, Liquid-Cooled, eSP+ Engine, making it perfect fit for those who value both style and substance. Setting a new standard for high-performance scooters with its impressive curves and advanced technology making every ride into #ClassThatLast.</p>', 90000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/honda_giorno.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:36'),
(62, 9, 10, 'Honda PCX 160 CBS', 'PCX 160 CBS', 'Red, White, Black, Blue', '<p><strong>Honda PCX 160 CBS</strong></p><p>The Honda PCX 160 CBS is a stylish and practical maxi-scooter designed for smooth and comfortable urban commuting. Powered by a 157cc liquid-cooled, fuel-injected engine with Honda\'s eSP+ technology, it delivers efficient performance and a refined riding experience.</p><p>Equipped with Combi Brake System (CBS), it automatically distributes braking force between the front and rear wheels for balanced stopping power and added safety. Its LED headlight and taillight, digital instrument panel, and elegant aerodynamic design give it a premium and modern look.</p>', 130000, 'A', 3, 15, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_pcx160_cbs.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:44'),
(63, 9, 10, 'Honda Click 125 SE', 'CLICK 125 SE', 'Red, White, Black, Blue', '<p><strong>Honda Click 125 SE</strong></p><p>The New Click125 showcasing a fresh design featuring striking new two-tone colors and dynamic stripes for the Click125 Standard Variant, while complemented by a sophisticated 3D Emblem exclusive to Special Edition Variant.</p>', 87000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/honda_click125_se.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:04'),
(64, 9, 10, 'Honda TMX Alpha', 'TMX ALPHA', 'Red, White, Black', '<p><strong>Honda TMX Alpha</strong></p><p>TMX125 Alpha is powered by the legendary Overhead Valve (OHV) engine, making it unique from other motorbikes. This OHV engine uses a push rod to balance acceleration and control for hours of easy and hassle-free operations while being fuel-efficient at 62.5km/L at 45Km/H constant speed. And to meet the customers\' requirement for best balance of engine power and acceleration, the rear sprocket is improved from 44T to 38T, making it perfect bike for daily commuting usage.</p>', 78000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/honda_tmx_alpha.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:55'),
(65, 9, 10, 'Honda TMX Supremo', 'TMX SUPREMO', 'Red, White, Black', '<p><strong>Honda TMX Supremo</strong></p><p>The 3rd Generation TMX Supremo now boasts of enhanced features, such as its new and improved engine that maintains its fuel efficiency at 62km/L. It also comes with 18-inch tires, as well as a high ground clearance and a seat height that ensures the riders\' comfort despite the impact of rough roads. This makes the 3rd Generation TMX Supremo better suited for heavy-duty rides and climbs on demanding roads, even when carrying loads.</p>', 95000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/honda_tmx_supremo.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:04:59'),
(66, 9, 10, 'Honda Wave RSX Drum', 'WAVE RSX DRUM', 'Red, White, Black', '<p><strong>Honda Wave RSX Drum</strong></p><p>The Wave RSX turns your riding experience into something remarkable. With its newest sporty dynamic design bringing out impressive stickers, functional features providing convenience, plus fuel efficiency upto 69.5 km/l powered by PGM-FI, this underbone lets you stands out wherever you go.</p>', 60000, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/honda_wave_rsx_drum.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:05:03'),
(67, 9, 10, 'Honda Winner X Premium', 'WINNER X PREMIUM', 'Red, White, Black', '<p><strong>Honda Winner X Premium</strong></p><p>It boasts outstanding performance through its 150cc, DOHC, 6-Speed, Liquid-Cooled Engine along with worthwhile features: USB Charging Port, Smart Key System, All LED Lighting System, Digital Meter Panel, Bank Angle Sensor, Assist & Slipper Clutch, Colored Cast Wheel, and Anti-Lock Braking System available in ABS Racing and ABS Premium variants only.</p>', 180000, 'A', 2, 10, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_winner_x_premium.png', 1, '2025-10-10 13:31:08', '2025-10-10 16:03:02'),
(68, 9, 10, 'Honda Winner X Standard', 'WINNER X STANDARD', 'Red, White, Black', '<p><strong>Honda Winner X Standard</strong></p><p>The All-New Winner X that is designed to let you #RideLikeAChampion is now here! This sports cub is sure to become one of another favorite among Filipino riders with its aggressive sports styling, powerful engine and advanced features.</p>', 160000, 'A', 2, 10, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_winner_x_standard.png', 1, '2025-10-10 13:31:09', '2025-10-10 16:03:45'),
(69, 9, 15, 'Pro Honda Motorcycle Mineral 10W-30 SL MA', 'XRM 125 Dual Sport Fi', '', '&lt;ul style=&quot;-webkit-font-smoothing: antialiased; font-family: -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, &amp;quot;Helvetica Neue&amp;quot;, Arial, &amp;quot;Noto Sans&amp;quot;, &amp;quot;Liberation Sans&amp;quot;, sans-serif, &amp;quot;Apple Color Emoji&amp;quot;, &amp;quot;Segoe UI Emoji&amp;quot;, &amp;quot;Segoe UI Symbol&amp;quot;, &amp;quot;Noto Color Emoji&amp;quot;;&quot;&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;Mineral Engine Oil&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;Four-stroke (4T) Engine Oil&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;API Service Rating: SL&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;SAE Viscosity Grade: 10W-30&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;JASO MA&lt;/li&gt;&lt;/ul&gt;', 230, 'A', 0, 30, 10, 0.00, NULL, 7, 1, 'uploads/products/0.png?v=1760411341', 1, '2025-10-13 20:05:44', '2025-10-14 17:45:17'),
(70, 9, 15, 'Pro Honda Premium Fully Synthetic 10W-30 SL MA', 'Supra GTR 150', '', '&lt;ul style=&quot;-webkit-font-smoothing: antialiased; font-family: -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, &amp;quot;Helvetica Neue&amp;quot;, Arial, &amp;quot;Noto Sans&amp;quot;, &amp;quot;Liberation Sans&amp;quot;, sans-serif, &amp;quot;Apple Color Emoji&amp;quot;, &amp;quot;Segoe UI Emoji&amp;quot;, &amp;quot;Segoe UI Symbol&amp;quot;, &amp;quot;Noto Color Emoji&amp;quot;;&quot;&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;100% Fully Synthetic Engine Oil&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;4T (Stroke) Engine Oil&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;API Service Rating: SL&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;SAE Viscosity Grade: 10W-30&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;JASO MA&lt;/li&gt;&lt;/ul&gt;', 460, 'A', 0, 30, 10, 0.00, NULL, 7, 1, 'uploads/products/0.png?v=1760411341', 1, '2025-10-13 20:09:01', '2025-10-14 17:45:41'),
(71, 9, 15, 'Pro Honda Scooter Fully Synthetic 10W-30 SL MB', 'Airblade 150', '', '&lt;div&gt;&lt;font face=&quot;-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, Liberation Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji&quot;&gt;&amp;nbsp;GENUINE OIL&amp;nbsp;&lt;/font&gt;&lt;/div&gt;&lt;div&gt;&lt;ul style=&quot;-webkit-font-smoothing: antialiased; font-family: -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, &amp;quot;Helvetica Neue&amp;quot;, Arial, &amp;quot;Noto Sans&amp;quot;, &amp;quot;Liberation Sans&amp;quot;, sans-serif, &amp;quot;Apple Color Emoji&amp;quot;, &amp;quot;Segoe UI Emoji&amp;quot;, &amp;quot;Segoe UI Symbol&amp;quot;, &amp;quot;Noto Color Emoji&amp;quot;;&quot;&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;100% Fully Synthetic Engine Oil&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;Four-stroke (4T) Engine Oil&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;API Service Rating: SL&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;SAE Viscosity Grade: 10W-30&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;JASO MB&lt;/li&gt;&lt;/ul&gt;&lt;/div&gt;', 10041000, 'A', 0, 30, 1, 100.00, NULL, 7, 1, 'uploads/products/0.png?v=1760411341', 1, '2025-10-13 06:36:11', '2025-10-14 17:45:37'),
(72, 9, 15, 'Pro Honda Scooter Mineral 10W-30 SL MB', 'Beat', '', '&lt;p style=&quot;-webkit-font-smoothing: antialiased; font-family: -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, &amp;quot;Helvetica Neue&amp;quot;, Arial, &amp;quot;Noto Sans&amp;quot;, &amp;quot;Liberation Sans&amp;quot;, sans-serif, &amp;quot;Apple Color Emoji&amp;quot;, &amp;quot;Segoe UI Emoji&amp;quot;, &amp;quot;Segoe UI Symbol&amp;quot;, &amp;quot;Noto Color Emoji&amp;quot;;&quot;&gt;&lt;span style=&quot;-webkit-font-smoothing: antialiased; font-weight: bolder;&quot;&gt;GENUINE OIL&lt;/span&gt;&lt;/p&gt;&lt;ul style=&quot;-webkit-font-smoothing: antialiased; font-family: -apple-system, BlinkMacSystemFont, &amp;quot;Segoe UI&amp;quot;, Roboto, &amp;quot;Helvetica Neue&amp;quot;, Arial, &amp;quot;Noto Sans&amp;quot;, &amp;quot;Liberation Sans&amp;quot;, sans-serif, &amp;quot;Apple Color Emoji&amp;quot;, &amp;quot;Segoe UI Emoji&amp;quot;, &amp;quot;Segoe UI Symbol&amp;quot;, &amp;quot;Noto Color Emoji&amp;quot;;&quot;&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;Mineral Engine Oil&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;Four-stroke (4T) Engine Oil&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;API Service Rating: SL&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;SAE Viscosity Grade: 10W-30&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased;&quot;&gt;JASO MB&lt;/li&gt;&lt;/ul&gt;', 275, 'A', 5, 30, 10, 0.00, NULL, 7, 1, 'uploads/products/0.png?v=1760411341', 1, '2025-10-13 19:59:14', '2025-10-14 17:45:31'),
(73, 9, 13, 'Pantropiko Dance Break Dela Cruz', 'ADV 160', 'Red, White, Black', '&lt;p&gt;wadwddawawdw&lt;/p&gt;', 231231, 'A', 3, 20, 10, 300.00, NULL, 7, 1, 'uploads/products/73.webp?v=1760428464', 1, '2025-10-14 15:54:24', '2025-10-14 15:59:10'),
(74, 9, 15, 'Oils', 'ADV 160', 'red', '&lt;p&gt;wewew&lt;/p&gt;', 9999, 'A', 5, 10, 1, 55.00, NULL, 7, 1, 'uploads/products/74.jpg?v=1760448998', 1, '2025-10-14 21:36:38', '2025-10-15 08:19:44'),
(75, 9, 15, 'Coolant Oil', 'ADV 160', '', '&lt;p&gt;oilwewewewwwwewewew eeawewaew w w aweaw e&lt;/p&gt;', 283, 'A', 3, 10, 1, 200.00, NULL, 7, 1, 'uploads/products/75.webp?v=1760498336', 1, '2025-10-15 11:18:56', '2025-10-15 11:19:10'),
(76, 9, 13, 'Hindi ko alam tawag dito', 'ADV 160', '', '&lt;p&gt;hindi ko alam description dito&lt;/p&gt;', 1, 'A', 0, 0, 0, 0.00, NULL, 7, 1, 'uploads/products/76.png?v=1760516221', 1, '2025-10-15 16:17:01', '2025-10-15 16:20:38'),
(77, 9, 13, 'Brake Shoe', 'TMX SUPREMO', '', '&lt;p&gt;Component for drum brake systems; provides braking friction.&lt;/p&gt;', 450, 'C', 10, 30, 5, 0.00, NULL, 7, 1, 'uploads/products/77.png?v=1760516746', 0, '2025-10-15 16:25:46', '2025-10-15 16:25:46'),
(78, 9, 13, 'Brake Pad', 'Click 125i', '', '&lt;p&gt;Component for disc brake systems; provides braking friction.&lt;/p&gt;', 700, 'B', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/78.png?v=1760516841', 0, '2025-10-15 16:27:21', '2025-10-15 16:27:21'),
(79, 9, 13, 'Throttle Cable', 'TMX 125 ALPHA', '', '&lt;p&gt;Connects the throttle grip to the carburetor/throttle body to control engine speed.&lt;/p&gt;', 390, 'C', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/79.png?v=1760516892', 0, '2025-10-15 16:28:12', '2025-10-15 16:28:12'),
(80, 9, 13, 'Belt Drive', 'Click 125i', '', '&lt;p&gt;Primary component in the continuously variable transmission (CVT) system.&lt;/p&gt;', 1200, 'B', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/80.png?v=1760516954', 0, '2025-10-15 16:29:14', '2025-10-15 16:29:14'),
(81, 9, 13, 'Chain and Sprocket Kit', 'ADV 160', '', '&lt;p&gt;Set of drive chain, front sprocket, and rear sprocket for power transmission.&lt;/p&gt;', 870, 'A', 10, 20, 5, 870.00, NULL, 7, 1, 'uploads/products/81.png?v=1760517008', 0, '2025-10-15 16:30:08', '2025-10-15 16:30:08'),
(82, 9, 13, 'Throttle Grip', 'Click 125i', '', '&lt;p&gt;Handlebar grip used to control the throttle opening.&lt;/p&gt;', 120, 'C', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/82.png?v=1760517051', 0, '2025-10-15 16:30:51', '2025-10-15 16:30:51'),
(83, 9, 13, 'Air Filter', 'PCX 150', '', '&lt;p&gt;Filters dust and debris from the air intake to protect the engine.&lt;/p&gt;', 550, 'C', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/83.png?v=1760517137', 0, '2025-10-15 16:32:17', '2025-10-15 16:32:17'),
(84, 9, 13, 'Wire Harness', 'ADV 160', '', '&lt;p&gt;The main set of wires and connectors for the motorcycle&#039;s electrical system.&lt;/p&gt;', 900, 'B', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/84.png?v=1760517187', 0, '2025-10-15 16:33:07', '2025-10-15 16:33:07'),
(85, 9, 13, 'Roller Set Weight', 'ADV 160', '', '&lt;p&gt;Components in the variator (part of the CVT) that affect shift timing.&lt;/p&gt;', 345, 'C', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/85.png?v=1760517237', 0, '2025-10-15 16:33:57', '2025-10-15 16:33:57'),
(86, 9, 13, 'Element Air Filter', 'ADV 160', '', '&lt;p&gt;Filters dust and debris from the air intake to protect the engine.&lt;/p&gt;', 450, 'C', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/86.png?v=1760517277', 0, '2025-10-15 16:34:37', '2025-10-15 16:34:37'),
(87, 9, 13, 'Slider Set', 'Click 125i', '', '&lt;p&gt;Small components in the variator that help guide the movement of the movable drive face.&lt;/p&gt;', 250, 'C', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/87.png?v=1760517332', 0, '2025-10-15 16:35:32', '2025-10-15 16:35:32'),
(88, 9, 13, 'Damper Set Wheel', 'Wave RSX(DISC)', '', '&lt;p&gt;Rubber cushions in the rear hub to absorb drive train shock.&lt;/p&gt;', 250, 'C', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/88.png?v=1760517377', 0, '2025-10-15 16:36:17', '2025-10-15 16:36:17'),
(89, 9, 13, 'Center Spring Main Stand', 'ADV 160', '', '&lt;p&gt;Spring that keeps the motorcycle&#039;s center stand in the upright or deployed position.&lt;/p&gt;', 22, 'C', 20, 30, 10, 0.00, NULL, 7, 1, 'uploads/products/89.png?v=1760517548', 0, '2025-10-15 16:39:08', '2025-10-15 16:39:08'),
(90, 9, 13, 'Drain Plug and Washer 12MM', 'PCX 150', '', '&lt;p&gt;Plug and sealing washer for draining engine oil.&lt;/p&gt;', 145, 'C', 20, 30, 10, 0.00, NULL, 7, 1, 'uploads/products/90.png?v=1760517592', 0, '2025-10-15 16:39:52', '2025-10-15 16:39:52'),
(91, 9, 13, 'Outer Comp Clutch', 'Beat', '', '&lt;p&gt;The bell/housing that the clutch shoes engage with in the CVT system.&lt;/p&gt;', 750, 'B', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/91.png?v=1760517646', 0, '2025-10-15 16:40:46', '2025-10-15 16:40:46'),
(92, 9, 13, 'Clutch Springs', 'Click 125i', '', '&lt;p&gt;Springs that determine the engagement speed of the centrifugal clutch.&lt;/p&gt;', 180, 'C', 10, 30, 5, 0.00, NULL, 7, 1, 'uploads/products/92.jpg?v=1760517691', 0, '2025-10-15 16:41:31', '2025-10-15 16:41:31'),
(93, 9, 13, 'Face Drive', 'PCX 150', '', '&lt;p&gt;The fixed or movable pulley face component in the CVT system.&lt;/p&gt;', 420, 'C', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/93.png?v=1760517743', 0, '2025-10-15 16:42:23', '2025-10-15 16:42:23'),
(94, 9, 13, 'Timing Chain', 'ADV 160', '', '&lt;p&gt;Links the crankshaft and camshaft to synchronize valve timing&lt;/p&gt;', 745, 'C', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/94.png?v=1760517796', 0, '2025-10-15 16:43:16', '2025-10-15 16:43:16'),
(95, 9, 13, 'Disk Clutch Friction Set', 'Wave RSX(DISC)', '', '&lt;p&gt;Friction plates that are part of the wet multi-plate clutch assembly.&lt;/p&gt;', 990, 'B', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/95.png?v=1760517846', 0, '2025-10-15 16:44:06', '2025-10-15 16:44:06'),
(96, 9, 13, 'Lifter Tensioner', 'XR 150i', '', '&lt;p&gt;Mechanism that maintains the correct tension on the timing chain.&lt;/p&gt;', 1350, 'B', 10, 30, 5, 0.00, NULL, 7, 1, 'uploads/products/96.png?v=1760517892', 0, '2025-10-15 16:44:52', '2025-10-15 16:44:52'),
(97, 9, 13, 'Spark Plug', 'Click 125i', '', '&lt;p&gt;Ignites the air-fuel mixture in the combustion chamber.&lt;/p&gt;', 380, 'C', 20, 30, 10, 0.00, NULL, 7, 1, 'uploads/products/97.png?v=1760517978', 0, '2025-10-15 16:46:18', '2025-10-15 16:46:18'),
(98, 9, 15, 'Honda Genuine Oil 4T SL 10W30 MB (Blue) Fully Synthetic Scooter Oil 1L', 'Click 125i', '', '&lt;ul style=&quot;-webkit-font-smoothing: antialiased; margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style: none; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;Honda Genuine Oil 4T SL 10W30 MB (Blue) Fully Synthetic Scooter Oil 1L&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;08234-2MB-K1LP&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;Honda Genuine Oil 4T SL 10W30 MB Fully Synthetic is a 100% is a superior-quality synthetic engine oil designed and approved by Honda for modern high performance scooters.&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;This Fully Synthetic oil gives the highest engine protection with its most advanced additives making it best oil for Honda Scooters.&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;100% Fully Synthetic Engine Oil&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;Four-stroke (4T) Engine Oil&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;API Service Rating : SL&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;SAE Viscosity Grade: 10W-30&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative;&quot;&gt;JASO MB&lt;/li&gt;&lt;/ul&gt;', 379, 'B', 20, 30, 10, 0.00, NULL, 7, 1, 'uploads/products/98.png?v=1760518196', 0, '2025-10-15 16:49:56', '2025-10-15 16:49:56'),
(99, 9, 15, 'Honda Genuine All Season Pre-Mix Coolant TYPE-1 1L', 'ADV 160', '', '&lt;ul style=&quot;-webkit-font-smoothing: antialiased; margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style: none; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;Honda Genuine All Season Pre-Mix Coolant TYPE-1 1L&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;08C04-EX100&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;Honda Genuine All Season Pre-Mix Coolant TYPE-1 1L best for Honda Cars&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;Vehicle Service Type: All-terrain-vehicles, utility-vehicles, street, sport-motorcycles, off-road-motorcycles, street-cruiser-motorcycles, street-touring-motorcycles,street-motor-scooters.&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative; margin-bottom: 5px;&quot;&gt;Directions: Use for top up and replace.&lt;/li&gt;&lt;li style=&quot;-webkit-font-smoothing: antialiased; position: relative;&quot;&gt;Should change coolant every 40000 km or 2 years.&lt;/li&gt;&lt;/ul&gt;', 395, 'B', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/99.png?v=1760518305', 0, '2025-10-15 16:51:45', '2025-10-15 16:51:45'),
(100, 9, 15, 'Pro Honda Genuine 10W30 MA (Black) Fully Synthetic for 1L', 'ADV 160', '', '&lt;p&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Pro Honda Genuine Oil 4T SL 10W30 MA (Black) Fully Synthetic for Motorcycle 1L&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;08234-2MA-K1LP1&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Recommended Application: Big Bikes, CBR150, XR150, CRF150, CRF250, Supra GTR150&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;100% Synthetic Premium Oil Approved by Honda R&amp;amp;D for on Road, High-Performance, Four-Cycle Sports Bikes of Honda&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;This Fully Synthetic Motorcycle Oil with Cutting-Edge Additive Technology is Designed to Provide Excellent Performance in Honda Motorcycle Engines&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Excellent Engine Performance&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Excellent Oxidation Resistance for Better High Heat Performance&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Quicker Flow to Vital Engine Parts at Low Temperature, Especially during Start Up&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Maximum Power Due to Less Frictional Drag from Oil&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Greater Fuel Economy&lt;/span&gt;&lt;/p&gt;', 480, 'B', 20, 50, 10, 0.00, NULL, 7, 1, 'uploads/products/100.png?v=1760518479', 0, '2025-10-15 16:54:39', '2025-10-15 16:54:39'),
(101, 9, 15, 'Pro Honda Genuine 10W30 MB (Silver) Scooter Oil 0.8L', 'Click 125i', '', '&lt;p&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Pro Honda Genuine Oil 4T SL 10W30 MB Fully Synthetic Scooter Oil 0.8L&lt;/span&gt;&lt;br style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;08232-2MB-K8LPP&lt;/span&gt;&lt;br style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Recommended Application: ADV150, PCX150, Airblade150, Click150i, Click125i&lt;/span&gt;&lt;br style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Honda Genuine Oil 4T SL 10W30 MA is A Premium Friction-Modified Gasoline Engine Oil for Four-Stroke Motorcycles&lt;/span&gt;&lt;br style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Its SLAPI Rating Offers Higher Fuel Efficiency, Less Harmful Emissions, Better Cleaning Performance and Oxidation Protection&lt;/span&gt;&lt;br style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;It Also Has Special Molybdenum Disulfide Additives for a Smoother Engine Performance&lt;/span&gt;&lt;br style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;This is Environment-Friendly Oil Protects the Engine from Wear and Prolongs Engine Life While Saving More Fuel&lt;/span&gt;&lt;br style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Recommended for Use in Scooters; not to be used where JASO MA oil is required&lt;/span&gt;&lt;/p&gt;', 300, 'B', 20, 30, 10, 0.00, NULL, 7, 1, 'uploads/products/101.png?v=1760518585', 0, '2025-10-15 16:56:25', '2025-10-15 16:56:25'),
(102, 9, 15, 'Pro Honda Genuine 10W30 MA (Gold) for 1L', 'XR 150i', '', '&lt;p&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Pro Honda Genuine Oil 4T SL 10W30 MA (Gold ) for Motorcycle 1L&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;08232-2MA-K1LP1&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Recommended Application: XRM125 (DS, DSX, Motard), Wave110, RS125 Fi, TMX Supremo, TMX125 Alpha&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Honda Genuine Oil 4T SL 10W30 MA is A Premium Multigrade Gasoline Engine Oil for Motorcycles&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Its SLAPI Rating Offers Higher Fuel Efficiency, Less Harmful Emissions, Better Cleaning Performance and Oxidation Protection&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;This is Environment-Friendly Oil Protects the Engine from Wear and Prolongs Engine Life While Saving More Fuel&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Recommended for Use in Four-Stroke Motorcycle with Wet Clutches&lt;/span&gt;&lt;/p&gt;', 299, 'A', 10, 20, 5, 0.00, NULL, 7, 1, 'uploads/products/102.png?v=1760518654', 0, '2025-10-15 16:57:34', '2025-10-15 16:57:34'),
(103, 9, 15, 'Pro Honda Genuine 10W30 MA (Black) Fully Synthetic for 2L', 'ADV 160', '', '&lt;p&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Pro Honda Genuine Oil 4T SL 10W30 MA (Black) Fully Synthetic for Motorcycle 1L&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;08234-2MA-K1LP1&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Recommended Application: Big Bikes, CBR150, XR150, CRF150, CRF250, Supra GTR150&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;100% Synthetic Premium Oil Approved by Honda R&amp;amp;D for on Road, High-Performance, Four-Cycle Sports Bikes of Honda&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;This Fully Synthetic Motorcycle Oil with Cutting-Edge Additive Technology is Designed to Provide Excellent Performance in Honda Motorcycle Engines&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Excellent Engine Performance&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Excellent Oxidation Resistance for Better High Heat Performance&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Quicker Flow to Vital Engine Parts at Low Temperature, Especially during Start Up&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Maximum Power Due to Less Frictional Drag from Oil&lt;/span&gt;&lt;br data-mce-fragment=&quot;1&quot; style=&quot;-webkit-font-smoothing: antialiased; color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;&lt;span style=&quot;color: rgb(11, 11, 12); font-family: &amp;quot;Rethink Sans&amp;quot;, sans-serif; font-size: 13px;&quot;&gt;Greater Fuel Economy&lt;/span&gt;&lt;/p&gt;', 480, 'C', 10, 20, 5, 100.00, NULL, 7, 1, 'uploads/products/103.png?v=1760529122', 0, '2025-10-15 19:52:02', '2025-10-15 19:52:02'),
(104, 9, 15, 'Honda Genuine Coolant Oio', '', '', '&lt;p&gt;wadawdawdawdawdawdwadwaawdaw&lt;/p&gt;', 1231320000, 'A', 0, 2, 1, 0.00, NULL, 7, 1, 'uploads/products/104.png?v=1760584558', 1, '2025-10-16 11:15:58', '2025-10-16 11:16:10');

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
(0, 34, 2, 1, '2025-10-12 22:50:54', NULL),
(0, 70, 8, 1, '2025-10-14 16:33:29', NULL),
(0, 103, 8, 1, '2025-10-16 10:10:30', NULL);

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
(1, 1, 'RCPT-2025-0001', 8, 167888.00, 'cash', '', 9, '2025-10-10 08:25:02', 'Thank you for your purchase at Star Honda Calamba! We appreciate your business and look forward to serving you again.', NULL),
(0, 0, 'RCPT-2025-0002', 8, 424.48, 'cash', '', 9, '2025-10-15 20:00:32', 'Thank you for your purchase at Star Honda Calamba! We appreciate your business and look forward to serving you again.', NULL);

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
(12, 'vehicle_type', 'Motorcycle'),
(12, 'vehicle_name', 'Honda Click 125i'),
(12, 'vehicle_registration_number', 'AB123CD'),
(12, 'vehicle_model', 'Honda Click 160'),
(12, 'service_id', '6'),
(12, 'pickup_address', ''),
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
(20, 'service_id', '70'),
(20, 'pickup_address', ''),
(6, 'service_id', '5'),
(6, 'pickup_address', ''),
(0, 'service_id', '58,5'),
(19, 'service_id', '70'),
(15, 'service_id', '5'),
(11, 'service_id', '6'),
(16, 'service_id', '7,5,6,8'),
(7, 'service_id', '7'),
(21, 'service_id', '32,70,53,7,31,39');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `target_type` varchar(50) NOT NULL,
  `target_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `target_type`, `target_id`, `rating`, `comment`, `date_created`, `date_updated`) VALUES
(1, 6, 'product', 13, 5, '', '2025-09-18 19:25:31', NULL),
(2, 8, 'product', 38, 5, 'mabilis, muntik na ko sumemplang', '2025-09-25 08:16:11', '2025-09-25 08:41:35'),
(3, 8, 'product', 33, 5, 'mabilis, muntik na ko sumemplang', '2025-09-25 08:41:17', '2025-09-25 08:56:13'),
(4, 10, 'product', 38, 5, 'maganda, maraming colors', '2025-10-17 10:46:03', NULL),
(5, 6, 'product', 38, 5, 'malakas hatak ng motor lalo na pag hindi hinulugan ng isang buwan', '2025-10-17 10:54:28', NULL);

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
(5, 'Brake System Check & Replacement', '&lt;p&gt;Inspection of brake pads, discs, fluid levels, and overall brake performance. Includes replacement of worn-out components and brake fluid flushing if necessary.&lt;/p&gt;', 90.00, 1.00, 650.00, 'LIGHT DIAGNOSING TROUBLESHOOTING & REPAIR', 60.00, NULL, NULL, NULL, 1, 0, '2025-08-07 22:48:48'),
(6, 'Chain and Sprocket Maintenance', '&lt;p&gt;Cleaning, lubricating, adjusting, or replacing the motorcycle chain and sprockets to prevent wear, reduce noise, and ensure smooth power transfer.&lt;/p&gt;', 120.00, 0.80, 265.00, 'REPLACE / REPAIR / CLEAN PARTS', 48.00, NULL, NULL, NULL, 1, 0, '2025-08-07 22:49:51'),
(7, 'Battery Check & Replacement', '&lt;p&gt;Testing battery health, terminals, and voltage. Replacement of weak or dead batteries to ensure reliable engine starts and electrical functions.&lt;/p&gt;', 60.00, 1.00, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2025-08-07 22:50:10'),
(8, 'Spark Plug Replacement', '&lt;p&gt;Removing old or worn spark plugs and installing new ones to ensure smooth engine ignition and combustion.&lt;/p&gt;', 30.00, NULL, 50.00, 'ADJUSTMENT', 0.00, NULL, NULL, NULL, 1, 0, '2025-08-07 22:50:33'),
(9, 'Minor Tune Up', 'Complete minor tune-up service including basic adjustments and checks', 90.00, 1.30, 455.00, 'TUNE UP', 78.00, NULL, 78.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(10, 'Major Tune Up', 'Comprehensive major tune-up service with detailed engine adjustments', 90.00, 3.00, 950.00, 'TUNE UP', 180.00, NULL, 99.99, NULL, 1, 0, '2025-10-10 14:02:15'),
(11, 'Minor Tune Up (Change Oil & Tune Up)', 'Minor tune-up combined with oil change service', 90.00, 1.30, 455.00, 'CHANGE OIL & TUNE UP', 78.00, NULL, 78.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(12, 'Top Overhaul', 'Complete top engine overhaul service', NULL, 5.00, 1505.00, 'ADJUSTMENT', 300.00, NULL, 99.99, NULL, 1, 0, '2025-10-10 14:02:15'),
(13, 'Engine Overhaul', 'Full engine overhaul service', NULL, 5.00, 1505.00, 'ADJUSTMENT', 300.00, NULL, 99.99, NULL, 1, 0, '2025-10-10 14:02:15'),
(14, 'Carburetor Idle Adjustment', 'Carburetor idle speed adjustment and tuning', NULL, 0.25, 50.00, 'ADJUSTMENT', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(15, 'Spark Plug Adjustment', 'Spark plug gap adjustment and replacement', 30.00, 0.50, 50.00, 'ADJUSTMENT', NULL, NULL, 0.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(16, 'Shaft Drive Adjustment', 'Shaft drive system adjustment and alignment', NULL, NULL, 50.00, 'ADJUSTMENT', 0.00, NULL, 0.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(17, 'Sprocket (Rear) Replacement', 'Rear sprocket replacement and installation', 120.00, 0.25, 50.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(18, 'Fork Assembly (Rear) Replacement', 'Rear fork assembly replacement and installation', NULL, 0.33, 120.00, 'REPLACE / REPAIR / CLEAN PARTS', 20.00, NULL, 20.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(19, 'Oil Pump Replacement', 'Oil pump replacement and installation', NULL, 0.20, 45.00, 'REPLACE / REPAIR / CLEAN PARTS', 12.00, NULL, 12.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(20, 'Carburetor Replacement', 'Carburetor replacement and installation', NULL, 0.75, 305.00, 'REPLACE / REPAIR / CLEAN PARTS', 45.00, NULL, 45.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(21, 'Starter Motor Replacement', 'Starter motor replacement and installation', NULL, 0.25, 150.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(22, 'Drive Chain / Sprocket Replacement', 'Drive chain and sprocket replacement', 120.00, 2.00, 265.00, 'REPLACE / REPAIR / CLEAN PARTS', NULL, NULL, 48.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(23, 'Oil Seal (Crankshaft Bottom) Replacement', 'Crankshaft bottom oil seal replacement', NULL, 0.75, 300.00, 'REPLACE / REPAIR / CLEAN PARTS', 45.00, NULL, 45.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(24, 'Starter Idle Item Replacement', 'Starter idle component replacement', NULL, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(25, 'Ignition Switch Replacement', 'Ignition switch replacement and installation', NULL, 0.83, 280.00, 'REPLACE / REPAIR / CLEAN PARTS', 50.00, NULL, 50.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(26, 'Brake Rear Panel Replacement', 'Rear brake panel replacement', 90.00, 0.75, 300.00, 'REPLACE / REPAIR / CLEAN PARTS', 45.00, NULL, 45.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(27, 'Seal Oil Pump Cleaner Replacement', 'Oil pump seal cleaner replacement', NULL, 0.75, 300.00, 'REPLACE / REPAIR / CLEAN PARTS', 45.00, NULL, 45.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(28, 'Valve (IN/EX) Replacement', 'Intake and exhaust valve replacement', NULL, 3.00, 950.00, 'REPLACE / REPAIR / CLEAN PARTS', 180.00, NULL, 99.99, NULL, 1, 0, '2025-10-10 14:02:15'),
(29, 'Gasket (Cylinder) Replacement', 'Cylinder gasket replacement', NULL, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(30, 'Cover Pulsate Right Replacement', 'Right pulsate cover replacement', NULL, 1.00, 155.00, 'REPLACE / REPAIR / CLEAN PARTS', 60.00, NULL, 60.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(31, 'Bearing Axle Shaft Replacement', 'Axle shaft bearing replacement', NULL, 1.50, 650.00, 'REPLACE / REPAIR / CLEAN PARTS', 90.00, NULL, 90.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(32, 'Arm Brake (Stand Side) Replacement', 'Stand side brake arm replacement', 90.00, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(33, 'Gear Starter Idle Replacement', 'Starter idle gear replacement', NULL, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(34, 'Shaft Idle Replacement', 'Idle shaft replacement', NULL, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(35, 'Disc Clutch Friction Replacement', 'Clutch friction disc replacement', NULL, 1.50, 650.00, 'REPLACE / REPAIR / CLEAN PARTS', 90.00, NULL, 90.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(36, 'Cover Gearcase Left Rear Replacement', 'Left rear gearcase cover replacement', NULL, 0.50, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 30.00, NULL, 30.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(37, 'Cover Crankcase Replacement', 'Crankcase cover replacement', NULL, 1.50, 650.00, 'REPLACE / REPAIR / CLEAN PARTS', 90.00, NULL, 90.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(38, 'Carrier Luggage Replacement', 'Luggage carrier replacement', NULL, 1.50, 650.00, 'REPLACE / REPAIR / CLEAN PARTS', 90.00, NULL, 90.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(39, 'Bearing Idle Shaft Replacement', 'Idle shaft bearing replacement', NULL, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(40, 'Spring Starter Base Replacement', 'Starter base spring replacement', NULL, 0.50, 45.00, 'REPLACE / REPAIR / CLEAN PARTS', 30.00, NULL, 30.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(41, 'Switch Gear Change Replacement', 'Gear change switch replacement', NULL, 1.50, 650.00, 'REPLACE / REPAIR / CLEAN PARTS', 90.00, NULL, 90.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(42, 'Cylinder Front Brake Replacement', 'Front brake cylinder replacement', 90.00, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(43, 'Cylinder Front Brake Master Replacement', 'Front brake master cylinder replacement', 90.00, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(44, 'Bulb Headlight Replacement', 'Headlight bulb replacement', NULL, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(45, 'Bulb Taillight Replacement', 'Taillight bulb replacement', NULL, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(46, 'Spring Decomp Cam Replacement', 'Decompression cam spring replacement', NULL, 3.00, 950.00, 'REPLACE / REPAIR / CLEAN PARTS', 180.00, NULL, 99.99, NULL, 1, 0, '2025-10-10 14:02:15'),
(47, 'Case Meter Lower Replacement', 'Lower meter case replacement', NULL, 0.75, 300.00, 'REPLACE / REPAIR / CLEAN PARTS', 45.00, NULL, 45.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(48, 'Cable Throttle Replacement', 'Throttle cable replacement', NULL, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(49, 'Cable Clutch Replacement', 'Clutch cable replacement', NULL, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(50, 'Switch Starter Replacement', 'Starter switch replacement', NULL, 0.50, 180.00, 'REPLACE / REPAIR / CLEAN PARTS', 30.00, NULL, 30.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(51, 'Cap Spark Plug Replacement', 'Spark plug cap replacement', 30.00, 0.25, 45.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(52, 'Switch Clutch Replacement', 'Clutch switch replacement', NULL, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(53, 'Base Stator Replacement', 'Stator base replacement', NULL, 0.75, 200.00, 'REPLACE / REPAIR / CLEAN PARTS', 45.00, NULL, 45.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(54, 'Bracket Handle Lever Left Replacement', 'Left handle lever bracket replacement', NULL, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(55, 'Solenoid Assembly Replacement', 'Solenoid assembly replacement', NULL, 1.00, 300.00, 'REPLACE / REPAIR / CLEAN PARTS', 60.00, NULL, 60.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(56, 'Flasher Replacement', 'Flasher unit replacement', NULL, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(57, 'Modified Steering Handle Replacement', 'Modified steering handle replacement', NULL, 0.75, 300.00, 'REPLACE / REPAIR / CLEAN PARTS', 45.00, NULL, 45.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(58, 'Brake Rear Drum Replacement', 'Rear brake drum replacement', 90.00, 0.75, 300.00, 'REPLACE / REPAIR / CLEAN PARTS', 45.00, NULL, 45.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(59, 'Key Set Replacement', 'Key set replacement and programming', NULL, NULL, 0.00, 'REPLACE / REPAIR / CLEAN PARTS', 0.00, NULL, 0.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(60, 'Switch Ignition and Lock Replacement', 'Ignition switch and lock replacement', NULL, 0.75, 300.00, 'REPLACE / REPAIR / CLEAN PARTS', 45.00, NULL, 45.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(61, 'Switch Combination and Lock Replacement', 'Combination switch and lock replacement', NULL, 0.75, 300.00, 'REPLACE / REPAIR / CLEAN PARTS', 45.00, NULL, 45.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(62, 'Bridge Fork Top Replacement', 'Top fork bridge replacement', NULL, 0.25, 100.00, 'REPLACE / REPAIR / CLEAN PARTS', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(63, 'General Repair', 'General motorcycle repair and troubleshooting', NULL, 1.00, 650.00, 'LIGHT DIAGNOSING TROUBLESHOOTING & REPAIR', 60.00, NULL, 60.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(64, 'Front/Rear Brakes Repair', 'Front and rear brake system repair', 90.00, 60.00, 650.00, 'LIGHT DIAGNOSING TROUBLESHOOTING & REPAIR', NULL, NULL, 60.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(65, 'Electrical Wiring Repair', 'Electrical wiring system repair and troubleshooting', NULL, 1.00, 650.00, 'LIGHT DIAGNOSING TROUBLESHOOTING & REPAIR', 60.00, NULL, 60.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(66, 'Electrical Wiring Lubrication', 'Electrical wiring lubrication service', NULL, 60.00, 650.00, 'LUBRICATE', NULL, NULL, 60.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(67, 'Cable Throttle Lubrication', 'Throttle cable lubrication service', NULL, 0.25, 105.00, 'LUBRICATE', 15.00, NULL, 15.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(68, 'Carburetor Idle Lubrication', 'Carburetor idle lubrication service', NULL, 1.00, 205.00, 'LUBRICATE', 60.00, NULL, 60.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(69, 'Cock Assembly (Fuel) Overhaul', 'Fuel cock assembly top overhaul', NULL, 0.33, 130.00, 'TOP OVERHAUL', 20.00, NULL, 20.00, NULL, 1, 0, '2025-10-10 14:02:15'),
(70, 'Arm Valve Exhaust Overhaul', 'Exhaust valve arm top overhaul', NULL, 2.00, 1105.00, 'TOP OVERHAUL', 120.00, NULL, 99.99, NULL, 1, 0, '2025-10-10 14:02:15'),
(71, 'Sprocket (Cam Chain) Overhaul', 'Cam chain sprocket top overhaul', 120.00, 3.00, 1200.00, 'TOP OVERHAUL', 180.00, NULL, 99.99, NULL, 1, 0, '2025-10-10 14:02:15'),
(72, 'Sprocket (Cam) Overhaul', 'Cam sprocket top overhaul', 120.00, 3.00, 950.00, 'TOP OVERHAUL', 180.00, NULL, 99.99, NULL, 1, 0, '2025-10-10 14:02:15'),
(73, 'Cylinder Overhaul', 'Cylinder top overhaul service', NULL, 3.00, 1200.00, 'TOP OVERHAUL', 180.00, NULL, 99.99, NULL, 1, 0, '2025-10-10 14:02:15'),
(74, 'Valve Spring and/or Stem Seal Overhaul', 'Valve spring and stem seal top overhaul', NULL, 3.00, 1200.00, 'TOP OVERHAUL', 180.00, NULL, 99.99, NULL, 1, 0, '2025-10-10 14:02:15'),
(75, 'Tensioner (Cam Chain) Replacement', 'Cam chain tensioner replacement', 120.00, 3.00, 1200.00, 'REPLACE / REPAIR / CLEAN PARTS', 180.00, NULL, 99.99, NULL, 1, 0, '2025-10-10 14:02:15');

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
(6, 2, 'Scooter', 'Drop Off', 'Honda Click', '123', 'Honda', 8, 2, '2025-04-24 08:28:14'),
(7, 4, 'Scooter', 'Drop Off', 'Honda Click', '123456789', 'Honda', 8, 2, '2025-08-13 14:12:36'),
(8, 4, NULL, 'Drop Off', NULL, NULL, NULL, NULL, 1, '2025-08-13 20:37:41'),
(9, 5, NULL, 'Drop Off', NULL, NULL, NULL, NULL, 1, '2025-08-14 16:48:10'),
(10, 6, NULL, 'Drop Off', NULL, NULL, NULL, NULL, 3, '2025-08-15 12:11:22'),
(11, 2, 'Motorcycle', 'Drop Off', 'Honda Click 125i', 'ABC 1234', 'Honda', 9, 2, '2025-08-15 12:56:09'),
(12, 6, NULL, 'Drop Off', NULL, NULL, NULL, 8, 4, '2025-08-15 14:09:35'),
(13, 6, NULL, 'Drop Off', NULL, NULL, NULL, 10, 1, '2025-09-18 19:38:50'),
(15, 8, 'Scooter', '', 'Honda Click', 'AB123CD', 'Honda', 7, 3, '2025-09-24 22:38:39'),
(16, 8, 'Scooter', '', 'Honda Beat', 'LMN123', 'Honda Beat', 10, 1, '2025-09-25 08:21:58'),
(17, 9, NULL, '', NULL, NULL, NULL, 8, 1, '2025-09-25 10:18:51'),
(19, 8, 'Scooter', '', 'Honda Click', 'ABC123', 'Honda', 10, 1, '2025-10-11 08:06:31'),
(21, 8, 'Motorcycle', '', 'Honda Click', 'DAC123', 'Honda', 7, 2, '2025-10-11 11:48:17');

-- --------------------------------------------------------

--
-- Stand-in structure for view `service_request_summary`
-- (See below for the actual view)
--
CREATE TABLE `service_request_summary` (
`id` int(30)
,`client_id` int(30)
,`client_name` mediumtext
,`service_type` text
,`status` tinyint(1)
,`status_text` varchar(11)
,`date_created` datetime
,`mechanic_name` text
);

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
(53, 37, 11, 1, '2025-10-11 11:50:06'),
(0, 34, 20, 1, '2025-10-15 16:58:52'),
(0, 34, 20, 1, '2025-10-15 16:58:54'),
(0, 83, 20, 1, '2025-10-15 16:59:16'),
(0, 83, 20, 1, '2025-10-15 16:59:17'),
(0, 80, 20, 1, '2025-10-15 17:00:57'),
(0, 80, 20, 1, '2025-10-15 17:00:58'),
(0, 78, 20, 1, '2025-10-15 17:01:09'),
(0, 78, 20, 1, '2025-10-15 17:01:11'),
(0, 77, 20, 1, '2025-10-15 17:01:21'),
(0, 77, 20, 1, '2025-10-15 17:01:22'),
(0, 89, 20, 1, '2025-10-15 17:01:36'),
(0, 89, 20, 1, '2025-10-15 17:01:38'),
(0, 81, 20, 1, '2025-10-15 17:03:19'),
(0, 81, 20, 1, '2025-10-15 17:03:21'),
(0, 92, 20, 1, '2025-10-15 17:04:48'),
(0, 92, 20, 1, '2025-10-15 17:04:49'),
(0, 88, 20, 1, '2025-10-15 17:05:35'),
(0, 88, 20, 1, '2025-10-15 17:05:37'),
(0, 95, 20, 1, '2025-10-15 17:05:43'),
(0, 95, 20, 1, '2025-10-15 17:05:44'),
(0, 90, 20, 1, '2025-10-15 17:05:50'),
(0, 90, 20, 1, '2025-10-15 17:05:50'),
(0, 86, 20, 1, '2025-10-15 17:05:57'),
(0, 86, 20, 1, '2025-10-15 17:05:57'),
(0, 93, 20, 1, '2025-10-15 17:06:05'),
(0, 93, 20, 1, '2025-10-15 17:06:05'),
(0, 99, 30, 1, '2025-10-15 17:06:11'),
(0, 99, 30, 1, '2025-10-15 17:06:11'),
(0, 98, 30, 1, '2025-10-15 17:06:17'),
(0, 96, 29, 1, '2025-10-15 17:06:23'),
(0, 91, 20, 1, '2025-10-15 17:06:30'),
(0, 100, 50, 1, '2025-10-15 17:07:35'),
(0, 102, 50, 1, '2025-10-15 17:07:42'),
(0, 101, 50, 1, '2025-10-15 17:07:48'),
(0, 85, 40, 1, '2025-10-15 17:07:53'),
(0, 87, 30, 1, '2025-10-15 17:07:58'),
(0, 97, 100, 1, '2025-10-15 17:08:04'),
(0, 79, 30, 1, '2025-10-15 17:08:09'),
(0, 82, 30, 1, '2025-10-15 17:08:15'),
(0, 94, 30, 1, '2025-10-15 17:08:20'),
(0, 84, 29, 1, '2025-10-15 17:08:26'),
(0, 98, 2, 1, '2025-10-15 19:52:31'),
(0, 33, 123131, 1, '2025-10-16 09:58:17'),
(0, 83, 2, 1, '2025-10-16 11:00:59'),
(0, 83, 22, 1, '2025-10-16 11:05:37'),
(0, 103, 10, 1, '2025-10-16 11:19:25'),
(0, 83, 1, 1, '2025-10-17 11:34:10');

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
(0, 34, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 16:58:52', NULL),
(0, 34, 'IN', 20.00, 20.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 16:58:54', NULL),
(0, 83, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 16:59:16', NULL),
(0, 83, 'IN', 20.00, 20.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 16:59:17', NULL),
(0, 80, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:00:57', NULL),
(0, 80, 'IN', 20.00, 20.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:00:58', NULL),
(0, 78, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:01:09', NULL),
(0, 78, 'IN', 20.00, 20.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:01:11', NULL),
(0, 77, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:01:21', NULL),
(0, 77, 'IN', 20.00, 20.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:01:22', NULL),
(0, 89, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:01:36', NULL),
(0, 89, 'IN', 20.00, 20.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:01:38', NULL),
(0, 81, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:03:19', NULL),
(0, 81, 'IN', 20.00, 20.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:03:21', NULL),
(0, 92, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:04:48', NULL),
(0, 92, 'IN', 20.00, 20.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:04:49', NULL),
(0, 88, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:05:36', NULL),
(0, 88, 'IN', 20.00, 20.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:05:37', NULL),
(0, 95, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:05:43', NULL),
(0, 95, 'IN', 20.00, 20.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:05:44', NULL),
(0, 90, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:05:50', NULL),
(0, 90, 'IN', 20.00, 20.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:05:50', NULL),
(0, 86, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:05:57', NULL),
(0, 86, 'IN', 20.00, 20.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:05:57', NULL),
(0, 93, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:06:05', NULL),
(0, 93, 'IN', 20.00, 20.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:06:05', NULL),
(0, 99, 'IN', 30.00, 0.00, 30.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:06:11', NULL),
(0, 99, 'IN', 30.00, 30.00, 60.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:06:11', NULL),
(0, 98, 'IN', 30.00, 0.00, 30.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:06:17', NULL),
(0, 96, 'IN', 29.00, 0.00, 29.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:06:23', NULL),
(0, 91, 'IN', 20.00, 0.00, 20.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:06:30', NULL),
(0, 100, 'IN', 50.00, 0.00, 50.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:07:35', NULL),
(0, 102, 'IN', 50.00, 0.00, 50.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:07:42', NULL),
(0, 101, 'IN', 50.00, 0.00, 50.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:07:48', NULL),
(0, 85, 'IN', 40.00, 0.00, 40.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:07:53', NULL),
(0, 87, 'IN', 30.00, 0.00, 30.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:07:58', NULL),
(0, 97, 'IN', 100.00, 0.00, 100.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:08:04', NULL),
(0, 79, 'IN', 30.00, 0.00, 30.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:08:09', NULL),
(0, 82, 'IN', 30.00, 0.00, 30.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:08:15', NULL),
(0, 94, 'IN', 30.00, 0.00, 30.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:08:20', NULL),
(0, 84, 'IN', 29.00, 0.00, 29.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 17:08:26', NULL),
(0, 98, 'IN', 2.00, 30.00, 32.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-15 19:52:31', NULL),
(0, 33, 'IN', 123131.00, 99.00, 123230.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-16 09:58:17', NULL),
(0, 83, 'IN', 2.00, 40.00, 42.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-16 11:00:59', NULL),
(0, 83, 'IN', 22.00, 42.00, 64.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-16 11:05:37', NULL),
(0, 103, 'IN', 10.00, 0.00, 10.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-16 11:19:25', NULL),
(0, 83, 'IN', 1.00, 64.00, 65.00, 'Stock addition', 'STOCK_ADD', 'PURCHASE', '2025-10-17 11:34:10', NULL);

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
  `email` varchar(255) DEFAULT NULL,
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
  `role_type` enum('admin','branch_supervisor','admin_assistant','stock_admin','service_admin','mechanic','inventory','service_receptionist') DEFAULT 'admin',
  `branch_id` int(11) DEFAULT NULL,
  `permissions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `email`, `password`, `avatar`, `last_login`, `type`, `status`, `date_added`, `date_updated`, `login_attempts`, `is_locked`, `locked_until`, `role_type`, `branch_id`, `permissions`) VALUES
(9, 'Aljay', 'Plantado', 'aljay', 'aljay@gmail.com', 'f0bd1fc09c2cfe760c342571f040eae7', 'uploads/1755062580_ID FINAL.jpg', '2025-10-17 16:43:44', 1, 1, '2025-08-13 13:23:57', '2025-10-17 17:12:21', 3, 1, '2025-10-17 17:13:21', 'admin', NULL, NULL),
(10, 'Henry', 'Legaspi', 'henry_admin', 'henry@gmail.com', '027e4180beedb29744413a7ea6b84a42', 'uploads/1760457600_Screenshot 2025-10-15 000007.png', '2025-10-16 22:44:08', 1, 1, '2025-09-24 22:28:02', '2025-10-16 22:44:08', 0, 0, NULL, 'admin', NULL, NULL),
(11, 'Euniel', 'Bandian', 'euniel_service', 'euniel@gmail.com', '1daa37b7282d1de0c3fcd736e3decaf0', 'uploads/1760457780_Screenshot 2025-10-15 000314.png', '2025-10-16 21:45:01', 2, 1, '2025-09-24 22:28:06', '2025-10-16 22:40:18', 0, 0, NULL, 'service_admin', NULL, NULL),
(12, 'Mark', 'Pancho', 'mark_inventory', 'mark@gmail.com', 'ea82410c7a9991816b5eeeebe195e20a', 'uploads/1760457720_Screenshot 2025-10-15 000231.png', '2025-10-16 21:52:46', 2, 1, '2025-09-24 22:28:08', '2025-10-16 22:37:52', 0, 0, NULL, 'inventory', NULL, NULL),
(13, 'Joshua', 'Cansino', 'joshua_inventory', 'joshua@gmail.com', 'd1133275ee2118be63a577af759fc052', 'uploads/1760457720_Screenshot 2025-10-15 000159.png', '2025-10-16 22:34:21', 2, 1, '2025-09-24 22:28:12', '2025-10-16 22:35:39', 0, 0, NULL, 'inventory', NULL, NULL),
(14, 'Karen', 'Bautista', 'karen_service', 'karen@gmail.com', 'ba952731f97fb058035aa399b1cb3d5c', 'uploads/1760457660_Screenshot 2025-10-15 000123.png', '2025-10-16 22:36:00', 2, 1, '2025-09-24 22:28:17', '2025-10-16 22:36:27', 0, 0, NULL, 'service_admin', NULL, NULL);

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
-- Structure for view `order_status_summary`
--
-- DROP TABLE IF EXISTS `order_status_summary`;

-- CREATE ALGORITHM=UNDEFINED DEFINER=`if0_40141531`@`sql212.infinityfree.com` SQL SECURITY DEFINER VIEW `order_status_summary`  AS SELECT `o`.`id` AS `id`, `o`.`ref_code` AS `ref_code`, `o`.`client_id` AS `client_id`, concat(`c`.`lastname`,', ',`c`.`firstname`,' ',coalesce(`c`.`middlename`,'')) AS `client_name`, `o`.`total_amount` AS `total_amount`, `o`.`status` AS `status`, CASE WHEN `o`.`status` = 0 THEN 'Pending' WHEN `o`.`status` = 1 THEN 'Ready for Pickup' WHEN `o`.`status` = 2 THEN 'For Delivery' WHEN `o`.`status` = 3 THEN 'On the Way' WHEN `o`.`status` = 4 THEN 'Delivered' WHEN `o`.`status` = 6 THEN 'Claimed' WHEN `o`.`status` = 5 THEN 'Cancelled' ELSE 'Unknown' END AS `status_text`, `o`.`date_created` AS `date_created`, `o`.`date_updated` AS `date_updated` FROM (`order_list` `o` join `client_list` `c` on(`o`.`client_id` = `c`.`id`)) WHERE `c`.`delete_flag` = 0 ;

-- --------------------------------------------------------
-- SELECT * FROM order_status_summary
--
-- Structure for view `service_request_summary`
--
-- DROP TABLE IF EXISTS `service_request_summary`;

-- CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `service_request_summary`  AS SELECT `s`.`id` AS `id`, `s`.`client_id` AS `client_id`, concat(`c`.`lastname`,', ',`c`.`firstname`,' ',coalesce(`c`.`middlename`,'')) AS `client_name`, `s`.`service_type` AS `service_type`, `s`.`status` AS `status`, CASE WHEN `s`.`status` = 0 THEN 'Pending' WHEN `s`.`status` = 1 THEN 'Confirmed' WHEN `s`.`status` = 2 THEN 'On-progress' WHEN `s`.`status` = 3 THEN 'Done' WHEN `s`.`status` = 4 THEN 'Cancelled' ELSE 'Unknown' END AS `status_text`, `s`.`date_created` AS `date_created`, `m`.`name` AS `mechanic_name` FROM ((`service_requests` `s` join `client_list` `c` on(`s`.`client_id` = `c`.`id`)) left join `mechanics_list` `m` on(`s`.`mechanic_id` = `m`.`id`)) WHERE `c`.`delete_flag` = 0 ;

--
-- Indexes for dumped tables
--

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
  ADD KEY `idx_client_id` (`client_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_client_product` (`client_id`,`product_id`),
  ADD KEY `idx_cart_list_client_id` (`client_id`),
  ADD KEY `idx_cart_list_product_id` (`product_id`),
  ADD KEY `idx_cart_list_date_added` (`date_added`);

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
  ADD KEY `idx_client_reset_token` (`reset_token`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD KEY `idx_order_items_order_id` (`order_id`),
  ADD KEY `idx_order_items_product_id` (`product_id`);

--
-- Indexes for table `order_list`
--
ALTER TABLE `order_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_list_client_id` (`client_id`),
  ADD KEY `idx_order_list_status` (`status`),
  ADD KEY `idx_order_list_date_created` (`date_created`);

--
-- Indexes for table `or_cr_documents`
--
ALTER TABLE `or_cr_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_or_cr_documents_client_id` (`client_id`),
  ADD KEY `idx_or_cr_documents_status` (`status`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_resets_email` (`email`),
  ADD KEY `idx_resets_token` (`token`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD KEY `idx_service_requests_client_id` (`client_id`),
  ADD KEY `idx_service_requests_status` (`status`),
  ADD KEY `idx_service_requests_date_created` (`date_created`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brand_list`
--
ALTER TABLE `brand_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `cart_list`
--
ALTER TABLE `cart_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `client_list`
--
ALTER TABLE `client_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `order_list`
--
ALTER TABLE `order_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `or_cr_documents`
--
ALTER TABLE `or_cr_documents`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_compatibility`
--
ALTER TABLE `product_compatibility`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `product_list`
--
ALTER TABLE `product_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_list`
--
ALTER TABLE `cart_list`
  ADD CONSTRAINT `fk_cart_list_client` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_list_product` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `or_cr_documents`
--
ALTER TABLE `or_cr_documents`
  ADD CONSTRAINT `fk_or_cr_documents_client` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
