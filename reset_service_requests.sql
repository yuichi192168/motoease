-- WARNING: This will DELETE all service requests and related metadata.
-- Run in the `bpsms_db` database.

SET FOREIGN_KEY_CHECKS = 0;

-- Drop child table first to avoid FK issues
DROP TABLE IF EXISTS `request_meta`;
DROP TABLE IF EXISTS `service_requests`;

SET FOREIGN_KEY_CHECKS = 1;

-- Recreate service_requests per latest schema
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

-- Recreate request_meta
CREATE TABLE `request_meta` (
  `request_id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Indexes
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `mechanic_id` (`mechanic_id`),
  ADD KEY `idx_service_requests_client_id` (`client_id`),
  ADD KEY `idx_service_requests_status` (`status`);

ALTER TABLE `request_meta`
  ADD KEY `request_id` (`request_id`);

-- Auto-increments
ALTER TABLE `service_requests`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;

-- Foreign keys (will silently fail if referenced tables are missing; that's okay)
ALTER TABLE `service_requests`
  ADD CONSTRAINT `service_requests_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_requests_ibfk_2` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanics_list` (`id`) ON DELETE SET NULL;

ALTER TABLE `request_meta`
  ADD CONSTRAINT `request_meta_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- Done

