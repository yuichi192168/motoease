-- SQL Migration: Create customer_images table for service receptionist management
-- This table is separate from customer_purchase_images and is used for general customer images

CREATE TABLE IF NOT EXISTS `customer_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(30) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(500) NOT NULL,
  `delete_flag` tinyint(1) DEFAULT 0,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `delete_flag` (`delete_flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add foreign key constraint if client_list table exists
-- ALTER TABLE `customer_images` 
-- ADD CONSTRAINT `customer_images_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

-- Ensure promo_images table has delete_flag column
ALTER TABLE `promo_images` 
ADD COLUMN IF NOT EXISTS `delete_flag` tinyint(1) DEFAULT 0 AFTER `display_order`;

-- Add index for delete_flag on promo_images if not exists
ALTER TABLE `promo_images` 
ADD INDEX IF NOT EXISTS `idx_delete_flag` (`delete_flag`);


