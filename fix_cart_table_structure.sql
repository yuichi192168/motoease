-- Fix cart_list table structure to prevent ID 0 issues

-- 1. First, let's check if we need to add AUTO_INCREMENT to the id field
-- This will ensure proper ID generation

-- Add AUTO_INCREMENT to the id field if it doesn't exist
ALTER TABLE `cart_list` MODIFY COLUMN `id` int(30) NOT NULL AUTO_INCREMENT;

-- 2. Add primary key if it doesn't exist
ALTER TABLE `cart_list` ADD PRIMARY KEY (`id`);

-- 3. Add indexes for better performance
ALTER TABLE `cart_list` ADD INDEX `idx_client_id` (`client_id`);
ALTER TABLE `cart_list` ADD INDEX `idx_product_id` (`product_id`);
ALTER TABLE `cart_list` ADD INDEX `idx_client_product` (`client_id`, `product_id`);

-- 4. Ensure proper constraints
ALTER TABLE `cart_list` MODIFY COLUMN `quantity` float NOT NULL DEFAULT 1;
ALTER TABLE `cart_list` MODIFY COLUMN `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- 5. Remove any existing items with ID 0 (just in case)
DELETE FROM `cart_list` WHERE `id` = 0;

-- 6. Reset AUTO_INCREMENT to start from 1
ALTER TABLE `cart_list` AUTO_INCREMENT = 1;
