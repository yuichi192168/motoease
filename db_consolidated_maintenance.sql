-- Consolidated maintenance for bpsms_db
-- Safe to run multiple times. Requires MariaDB 10.5+/MySQL 8.0+ for IF NOT EXISTS clauses.

-- Use your database
-- USE bpsms_db;

SET FOREIGN_KEY_CHECKS = 0;

-- 1) Ensure service_list has estimated_hours (stored as minutes per current UI)
ALTER TABLE `service_list`
  ADD COLUMN IF NOT EXISTS `estimated_hours` DECIMAL(5,2) DEFAULT NULL COMMENT 'Estimated completion time (minutes stored per UI)'
  AFTER `description`;

-- 2) Ensure cart_list has unique constraint and required columns
ALTER TABLE `cart_list`
  ADD COLUMN IF NOT EXISTS `color` varchar(50) DEFAULT NULL,
  MODIFY `quantity` float NOT NULL;

CREATE UNIQUE INDEX IF NOT EXISTS `uniq_cart_client_product_color`
  ON `cart_list` (`client_id`,`product_id`,`color`);

-- 3) Ensure FKs on cart_list
ALTER TABLE `cart_list`
  ADD CONSTRAINT `cart_list_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_list_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

-- 4) Ensure service_requests table exists
CREATE TABLE IF NOT EXISTS `service_requests` (
  `id` int(30) NOT NULL,
  `client_id` int(30) NOT NULL,
  `service_type` text NOT NULL,
  `vehicle_name` varchar(100) DEFAULT NULL,
  `vehicle_registration_number` varchar(20) DEFAULT NULL,
  `mechanic_id` int(30) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5) Ensure request_meta table exists
CREATE TABLE IF NOT EXISTS `request_meta` (
  `request_id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6) Indexes for service_requests and request_meta
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `mechanic_id` (`mechanic_id`),
  ADD KEY `idx_service_requests_client_id` (`client_id`),
  ADD KEY `idx_service_requests_status` (`status`);

ALTER TABLE `request_meta`
  ADD KEY `request_id` (`request_id`);

-- 7) Auto-increment for service_requests
ALTER TABLE `service_requests`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;

-- 8) Foreign keys for service_requests and request_meta
ALTER TABLE `service_requests`
  ADD CONSTRAINT `service_requests_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_requests_ibfk_2` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanics_list` (`id`) ON DELETE SET NULL;

ALTER TABLE `request_meta`
  ADD CONSTRAINT `request_meta_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- 9) Appointments schema normalization
CREATE TABLE IF NOT EXISTS `appointments` (
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

ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `service_type` (`service_type`),
  ADD KEY `mechanic_id` (`mechanic_id`),
  ADD KEY `appointment_date` (`appointment_date`),
  ADD KEY `status` (`status`);

ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE UNIQUE INDEX IF NOT EXISTS `uniq_appointment_slot`
  ON `appointments` (`appointment_date`,`appointment_time`);

ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_client` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_mechanic` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanics_list` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_ibfk_service` FOREIGN KEY (`service_type`) REFERENCES `service_list` (`id`) ON DELETE RESTRICT;

SET FOREIGN_KEY_CHECKS = 1;

-- 10) Optional data hygiene
-- Remove orphan request_meta if any remain (for older DBs without FK)
DELETE rm FROM request_meta rm
LEFT JOIN service_requests sr ON sr.id = rm.request_id
WHERE sr.id IS NULL;

-- 11) Optional: Backfill estimated_hours defaults only where NULL (minutes)
UPDATE `service_list` SET `estimated_hours` = 60.00
WHERE `estimated_hours` IS NULL AND `service` LIKE '%Change Oil%';


