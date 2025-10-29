-- Add ABC category column to product_list table
ALTER TABLE `product_list` ADD COLUMN `abc_category` ENUM('A', 'B', 'C') DEFAULT 'C' AFTER `status`;

-- Add reorder_point and max_stock columns if they don't exist
ALTER TABLE `product_list` ADD COLUMN `reorder_point` INT(11) DEFAULT 10 AFTER `abc_category`;
ALTER TABLE `product_list` ADD COLUMN `max_stock` INT(11) DEFAULT 100 AFTER `reorder_point`;

-- Create inventory_alerts table if it doesn't exist
CREATE TABLE IF NOT EXISTS `inventory_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `alert_type` enum('LOW_STOCK','OUT_OF_STOCK','OVERSTOCK') NOT NULL,
  `current_stock` int(11) NOT NULL,
  `threshold_value` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_resolved` tinyint(1) DEFAULT 0,
  `resolved_by` int(11) DEFAULT NULL,
  `resolved_date` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `alert_type` (`alert_type`),
  KEY `is_resolved` (`is_resolved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create product_notifications table for out-of-stock notifications
CREATE TABLE IF NOT EXISTS `product_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  KEY `is_active` (`is_active`),
  UNIQUE KEY `unique_notification` (`product_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Update existing products to have default ABC category
UPDATE `product_list` SET `abc_category` = 'C' WHERE `abc_category` IS NULL OR `abc_category` = '';

-- Set default reorder points and max stock for existing products
UPDATE `product_list` SET `reorder_point` = 10 WHERE `reorder_point` IS NULL OR `reorder_point` = 0;
UPDATE `product_list` SET `max_stock` = 100 WHERE `max_stock` IS NULL OR `max_stock` = 0;
