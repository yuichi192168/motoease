-- Fix cart_list table structure to add missing color column and fix other issues

-- 1. Add the missing color column to cart_list table
ALTER TABLE `cart_list` ADD COLUMN `color` VARCHAR(50) NULL AFTER `product_id`;

-- 2. Add AUTO_INCREMENT to the id field if it doesn't exist
ALTER TABLE `cart_list` MODIFY COLUMN `id` int(30) NOT NULL AUTO_INCREMENT;

-- 3. Add primary key if it doesn't exist
ALTER TABLE `cart_list` ADD PRIMARY KEY (`id`);

-- 4. Add indexes for better performance
ALTER TABLE `cart_list` ADD INDEX `idx_client_id` (`client_id`);
ALTER TABLE `cart_list` ADD INDEX `idx_product_id` (`product_id`);
ALTER TABLE `cart_list` ADD INDEX `idx_client_product` (`client_id`, `product_id`);

-- 5. Ensure proper constraints
ALTER TABLE `cart_list` MODIFY COLUMN `quantity` float NOT NULL DEFAULT 1;
ALTER TABLE `cart_list` MODIFY COLUMN `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- 6. Remove any existing items with ID 0 (just in case)
DELETE FROM `cart_list` WHERE `id` = 0;

-- 7. Reset AUTO_INCREMENT to start from 1
ALTER TABLE `cart_list` AUTO_INCREMENT = 1;

-- 8. Clean up any invalid cart items
DELETE FROM `cart_list` WHERE `product_id` = 0 OR `product_id` IS NULL;
DELETE FROM `cart_list` WHERE `client_id` = 0 OR `client_id` IS NULL;

-- 9. Remove cart items that reference non-existent products
DELETE c FROM `cart_list` c 
LEFT JOIN `product_list` p ON c.product_id = p.id 
WHERE p.id IS NULL;

-- 10. Remove cart items that reference non-existent clients
DELETE c FROM `cart_list` c 
LEFT JOIN `client_list` cl ON c.client_id = cl.id 
WHERE cl.id IS NULL;
