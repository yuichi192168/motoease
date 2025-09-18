-- Customer Feedback & Engagement: reviews table
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `target_type` ENUM('product','service','dealership','order') NOT NULL,
  `target_id` INT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comment` TEXT NULL,
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_target` (`target_type`, `target_id`),
  INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Database Update Script for Missing Features
-- Run this script to add all missing tables and columns

-- 1. Create notifications table
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` json DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_read` (`is_read`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Create appointments table
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `service_type` int(11) NOT NULL,
  `mechanic_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `vehicle_info` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `service_type` (`service_type`),
  KEY `mechanic_id` (`mechanic_id`),
  KEY `appointment_date` (`appointment_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Create wishlist table
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_product` (`client_id`,`product_id`),
  KEY `client_id` (`client_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Add missing columns to client_list table
ALTER TABLE `client_list` 
ADD COLUMN IF NOT EXISTS `account_balance` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `password`,
ADD COLUMN IF NOT EXISTS `vehicle_brand` varchar(100) DEFAULT NULL AFTER `account_balance`,
ADD COLUMN IF NOT EXISTS `vehicle_model` varchar(100) DEFAULT NULL AFTER `vehicle_brand`,
ADD COLUMN IF NOT EXISTS `vehicle_plate_number` varchar(20) DEFAULT NULL AFTER `vehicle_model`,
ADD COLUMN IF NOT EXISTS `login_attempts` int(11) NOT NULL DEFAULT 0 AFTER `vehicle_plate_number`,
ADD COLUMN IF NOT EXISTS `is_locked` tinyint(1) NOT NULL DEFAULT 0 AFTER `login_attempts`,
ADD COLUMN IF NOT EXISTS `locked_until` datetime DEFAULT NULL AFTER `is_locked`,
ADD COLUMN IF NOT EXISTS `reset_token` varchar(255) DEFAULT NULL AFTER `locked_until`,
ADD COLUMN IF NOT EXISTS `reset_expires` datetime DEFAULT NULL AFTER `reset_token`,
ADD COLUMN IF NOT EXISTS `last_login` datetime DEFAULT NULL AFTER `reset_expires`;

-- 5. Add missing columns to users table (for admin notifications)
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `login_attempts` int(11) NOT NULL DEFAULT 0 AFTER `password`,
ADD COLUMN IF NOT EXISTS `is_locked` tinyint(1) NOT NULL DEFAULT 0 AFTER `login_attempts`,
ADD COLUMN IF NOT EXISTS `locked_until` datetime DEFAULT NULL AFTER `is_locked`,
ADD COLUMN IF NOT EXISTS `last_login` datetime DEFAULT NULL AFTER `locked_until`;

-- 6. Add missing columns to order_list table
ALTER TABLE `order_list` 
ADD COLUMN IF NOT EXISTS `delivery_address` text DEFAULT NULL AFTER `total_amount`;

-- 7. Add missing columns to service_requests table
ALTER TABLE `service_requests` 
ADD COLUMN IF NOT EXISTS `vehicle_name` varchar(100) DEFAULT NULL AFTER `service_type`,
ADD COLUMN IF NOT EXISTS `vehicle_registration_number` varchar(20) DEFAULT NULL AFTER `vehicle_name`;

-- 8. Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_customer_transactions_client_id` ON `customer_transactions` (`client_id`);
CREATE INDEX IF NOT EXISTS `idx_customer_transactions_date_created` ON `customer_transactions` (`date_created`);
CREATE INDEX IF NOT EXISTS `idx_or_cr_documents_client_id` ON `or_cr_documents` (`client_id`);
CREATE INDEX IF NOT EXISTS `idx_or_cr_documents_status` ON `or_cr_documents` (`status`);
CREATE INDEX IF NOT EXISTS `idx_order_list_client_id` ON `order_list` (`client_id`);
CREATE INDEX IF NOT EXISTS `idx_order_list_status` ON `order_list` (`status`);
CREATE INDEX IF NOT EXISTS `idx_service_requests_client_id` ON `service_requests` (`client_id`);
CREATE INDEX IF NOT EXISTS `idx_service_requests_status` ON `service_requests` (`status`);

-- 9. Insert sample data for testing (optional)
-- Insert sample notification settings
INSERT IGNORE INTO `system_info` (`meta_field`, `meta_value`) VALUES
('email_notifications', '1'),
('sms_notifications', '0'),
('notification_email', 'noreply@example.com');

-- 10. Create logs directory for notification logs
-- This will be handled by PHP when needed

-- 11. Add foreign key constraints (if not already present)
-- Note: These may fail if data doesn't match, so they're commented out
/*
ALTER TABLE `customer_transactions` 
ADD CONSTRAINT `fk_customer_transactions_client` 
FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

ALTER TABLE `or_cr_documents` 
ADD CONSTRAINT `fk_or_cr_documents_client` 
FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

ALTER TABLE `order_list` 
ADD CONSTRAINT `fk_order_list_client` 
FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

ALTER TABLE `service_requests` 
ADD CONSTRAINT `fk_service_requests_client` 
FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

ALTER TABLE `appointments` 
ADD CONSTRAINT `fk_appointments_client` 
FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

ALTER TABLE `wishlist` 
ADD CONSTRAINT `fk_wishlist_client` 
FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_wishlist_product` 
FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE;
*/

-- 12. Update existing data to set default values
UPDATE `client_list` SET `account_balance` = 0.00 WHERE `account_balance` IS NULL;
UPDATE `client_list` SET `login_attempts` = 0 WHERE `login_attempts` IS NULL;
UPDATE `client_list` SET `is_locked` = 0 WHERE `is_locked` IS NULL;

UPDATE `users` SET `login_attempts` = 0 WHERE `login_attempts` IS NULL;
UPDATE `users` SET `is_locked` = 0 WHERE `is_locked` IS NULL;

-- 13. Create view for customer dashboard data
CREATE OR REPLACE VIEW `customer_dashboard_view` AS
SELECT 
    c.id as client_id,
    c.firstname,
    c.lastname,
    c.email,
    c.account_balance,
    COUNT(DISTINCT o.id) as total_orders,
    COUNT(DISTINCT s.id) as total_services,
    COUNT(DISTINCT a.id) as total_appointments,
    COUNT(DISTINCT d.id) as total_documents,
    (SELECT COUNT(*) FROM notifications WHERE user_id = c.id AND is_read = 0) as unread_notifications
FROM client_list c
LEFT JOIN order_list o ON c.id = o.client_id
LEFT JOIN service_requests s ON c.id = s.client_id
LEFT JOIN appointments a ON c.id = a.client_id
LEFT JOIN or_cr_documents d ON c.id = d.client_id
WHERE c.delete_flag = 0
GROUP BY c.id;

-- 14. Create indexes for better search performance
CREATE INDEX IF NOT EXISTS `idx_product_list_search` ON `product_list` (`name`, `description`);
CREATE INDEX IF NOT EXISTS `idx_product_list_status` ON `product_list` (`status`, `delete_flag`);
CREATE INDEX IF NOT EXISTS `idx_brand_list_status` ON `brand_list` (`status`, `delete_flag`);
CREATE INDEX IF NOT EXISTS `idx_categories_status` ON `categories` (`status`, `delete_flag`);

-- 15. Add sample appointment time slots (optional)
-- This can be managed through the application interface

-- 16. Create notification templates table (optional)
CREATE TABLE IF NOT EXISTS `notification_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default notification templates
INSERT IGNORE INTO `notification_templates` (`name`, `type`, `subject`, `body`) VALUES
('order_status_update', 'email', 'Order Status Update', 'Dear {customer_name}, Your order {order_ref} has been updated to: {status}. Thank you for choosing our services!'),
('service_status_update', 'email', 'Service Request Update', 'Dear {customer_name}, Your service request #{service_id} has been updated to: {status}. We will keep you updated on the progress.'),
('product_availability', 'email', 'Product Available', 'Dear {customer_name}, Great news! The product {product_name} is now back in stock. Hurry up and place your order!'),
('appointment_reminder', 'email', 'Appointment Reminder', 'Dear {customer_name}, This is a friendly reminder about your upcoming appointment on {appointment_date} at {appointment_time}.');

-- 17. Create system settings for notifications
INSERT IGNORE INTO `system_info` (`meta_field`, `meta_value`) VALUES
('notification_email_enabled', '1'),
('notification_sms_enabled', '0'),
('notification_email_from', 'noreply@example.com'),
('notification_email_from_name', 'MotoEase System'),
('appointment_reminder_hours', '24'),
('product_availability_notification', '1');

-- 18. Add sample data for testing (optional - remove in production)
-- Insert sample appointment time slots
INSERT IGNORE INTO `system_info` (`meta_field`, `meta_value`) VALUES
('appointment_time_slots', '08:00,08:30,09:00,09:30,10:00,10:30,11:00,11:30,13:00,13:30,14:00,14:30,15:00,15:30,16:00,16:30,17:00');

-- 19. Create audit log table for tracking changes
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_type` enum('admin','client') NOT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `table_name` (`table_name`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 20. Final optimization
OPTIMIZE TABLE `client_list`, `order_list`, `service_requests`, `customer_transactions`, `or_cr_documents`;

-- End of database update script
