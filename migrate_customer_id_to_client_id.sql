-- Migration Script: Rename customer_id to client_id in Customer Account Balance Tables
-- This script aligns the customer account balance system with the existing database schema
-- Run this script if you already have the customer account balance tables created

-- Rename customer_id to client_id in customer_account_balances table
ALTER TABLE `customer_account_balances` 
CHANGE COLUMN `customer_id` `client_id` INT(30) NOT NULL COMMENT 'Reference to client_list.id';

-- Rename customer_id to client_id in customer_account_notifications table
ALTER TABLE `customer_account_notifications` 
CHANGE COLUMN `customer_id` `client_id` INT(30) NOT NULL COMMENT 'Reference to client_list.id';

-- Drop old indexes if they exist
DROP INDEX IF EXISTS `idx_account_balances_customer_status` ON `customer_account_balances`;
DROP INDEX IF EXISTS `idx_notifications_customer_read` ON `customer_account_notifications`;

-- Create new indexes with client_id
CREATE INDEX `idx_account_balances_client_status` ON `customer_account_balances`(`client_id`, `status`);
CREATE INDEX `idx_notifications_client_read` ON `customer_account_notifications`(`client_id`, `is_read`);

-- Note: The indexes on individual columns (KEY `customer_id` ...) will be automatically 
-- renamed by MySQL/MariaDB when the column is renamed


