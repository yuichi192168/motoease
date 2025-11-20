-- Add archive/delete_flag columns to tables that need soft delete functionality
-- This allows records to be archived instead of permanently deleted

-- Orders table
ALTER TABLE `order_list` 
ADD COLUMN IF NOT EXISTS `delete_flag` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=active, 1=archived';

-- Invoices table
ALTER TABLE `invoices` 
ADD COLUMN IF NOT EXISTS `delete_flag` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=active, 1=archived';

-- Service requests table
ALTER TABLE `service_requests` 
ADD COLUMN IF NOT EXISTS `delete_flag` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=active, 1=archived';

-- Appointments table
ALTER TABLE `appointments` 
ADD COLUMN IF NOT EXISTS `delete_flag` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=active, 1=archived';

-- OR/CR Documents table
ALTER TABLE `or_cr_documents` 
ADD COLUMN IF NOT EXISTS `delete_flag` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=active, 1=archived';

-- Stock list table
ALTER TABLE `stock_list` 
ADD COLUMN IF NOT EXISTS `delete_flag` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=active, 1=archived';

-- Promo images table
ALTER TABLE `promo_images` 
ADD COLUMN IF NOT EXISTS `delete_flag` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=active, 1=archived';

-- Customer purchase images table
ALTER TABLE `customer_purchase_images` 
ADD COLUMN IF NOT EXISTS `delete_flag` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=active, 1=archived';

-- Add indexes for better performance on delete_flag columns
ALTER TABLE `order_list` ADD INDEX IF NOT EXISTS `idx_delete_flag` (`delete_flag`);
ALTER TABLE `invoices` ADD INDEX IF NOT EXISTS `idx_delete_flag` (`delete_flag`);
ALTER TABLE `service_requests` ADD INDEX IF NOT EXISTS `idx_delete_flag` (`delete_flag`);
ALTER TABLE `appointments` ADD INDEX IF NOT EXISTS `idx_delete_flag` (`delete_flag`);
ALTER TABLE `or_cr_documents` ADD INDEX IF NOT EXISTS `idx_delete_flag` (`delete_flag`);
ALTER TABLE `stock_list` ADD INDEX IF NOT EXISTS `idx_delete_flag` (`delete_flag`);
ALTER TABLE `promo_images` ADD INDEX IF NOT EXISTS `idx_delete_flag` (`delete_flag`);
ALTER TABLE `customer_purchase_images` ADD INDEX IF NOT EXISTS `idx_delete_flag` (`delete_flag`);

