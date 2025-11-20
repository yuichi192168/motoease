-- ============================================
-- Complete Fix for Payment Submission Tables
-- This script drops old tables and recreates them with correct structure
-- WARNING: This will DELETE ALL DATA in these tables!
-- Make sure to backup your data before running this script
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- STEP 1: Drop Foreign Key Constraints (if they exist)
-- ============================================

-- Drop foreign keys from customer_account_transactions
ALTER TABLE `customer_account_transactions` 
DROP FOREIGN KEY IF EXISTS `fk_cat_account`;

ALTER TABLE `customer_account_transactions` 
DROP FOREIGN KEY IF EXISTS `fk_cat_schedule`;

ALTER TABLE `customer_account_transactions` 
DROP FOREIGN KEY IF EXISTS `fk_cat_processed_by`;

-- Drop foreign keys from customer_account_schedule
ALTER TABLE `customer_account_schedule` 
DROP FOREIGN KEY IF EXISTS `fk_cas_account`;

-- Drop foreign keys from customer_account_notifications
ALTER TABLE `customer_account_notifications` 
DROP FOREIGN KEY IF EXISTS `fk_can_account`;

ALTER TABLE `customer_account_notifications` 
DROP FOREIGN KEY IF EXISTS `fk_can_schedule`;

ALTER TABLE `customer_account_notifications` 
DROP FOREIGN KEY IF EXISTS `fk_can_client`;

-- Drop foreign keys from customer_account_balances
ALTER TABLE `customer_account_balances` 
DROP FOREIGN KEY IF EXISTS `fk_cab_client`;

ALTER TABLE `customer_account_balances` 
DROP FOREIGN KEY IF EXISTS `fk_cab_order`;

ALTER TABLE `customer_account_balances` 
DROP FOREIGN KEY IF EXISTS `fk_cab_invoice`;

ALTER TABLE `customer_account_balances` 
DROP FOREIGN KEY IF EXISTS `fk_cab_contract`;

-- ============================================
-- STEP 2: Drop Tables (in correct order due to dependencies)
-- ============================================

DROP TABLE IF EXISTS `customer_account_transactions`;
DROP TABLE IF EXISTS `customer_account_notifications`;
DROP TABLE IF EXISTS `customer_account_schedule`;
DROP TABLE IF EXISTS `customer_account_balances`;
DROP TABLE IF EXISTS `admin_activity_log`;

-- ============================================
-- STEP 3: Create Tables with Correct Structure
-- ============================================

-- 1. Customer Account Balances Table
CREATE TABLE `customer_account_balances` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `client_id` INT(30) NOT NULL COMMENT 'Reference to client_list.id',
    `order_id` INT(30) NOT NULL,
    `invoice_id` INT(11) DEFAULT NULL COMMENT 'Reference to invoice if exists',
    `contract_id` INT(11) DEFAULT NULL COMMENT 'Reference to installment_contracts if exists',
    `item_purchased` VARCHAR(255) NOT NULL COMMENT 'Motorcycle name/model',
    `total_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total price without VAT',
    `downpayment_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `paid_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Cumulative paid amount',
    `remaining_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `installment_plan_months` INT(11) DEFAULT NULL COMMENT 'Number of months in installment plan',
    `monthly_payment_amount` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Monthly payment amount',
    `status` ENUM('active', 'paid', 'closed', 'defaulted') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_client_id` (`client_id`),
    KEY `idx_order_id` (`order_id`),
    KEY `idx_status` (`status`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_account_balances_client_status` (`client_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Customer Account Schedule Table
CREATE TABLE `customer_account_schedule` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `account_id` INT(11) NOT NULL COMMENT 'Reference to customer_account_balances.id',
    `installment_number` INT(11) NOT NULL COMMENT 'Month number (1, 2, 3, etc.)',
    `due_date` DATE NOT NULL,
    `amount_due` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Monthly payment amount',
    `paid_amount` DECIMAL(15,2) DEFAULT 0.00,
    `late_fee` DECIMAL(15,2) DEFAULT 0.00 COMMENT '3% late fee if payment is >7 days late',
    `remaining_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `payment_status` ENUM('Unpaid', 'Paid', 'Late', 'Partial') DEFAULT 'Unpaid',
    `paid_date` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_account_id` (`account_id`),
    KEY `idx_due_date` (`due_date`),
    KEY `idx_payment_status` (`payment_status`),
    KEY `idx_account_due_status` (`account_id`, `due_date`, `payment_status`),
    KEY `idx_account_schedule_account_due` (`account_id`, `due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Customer Account Transactions Table
CREATE TABLE `customer_account_transactions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `account_id` INT(11) NOT NULL COMMENT 'Reference to customer_account_balances.id',
    `schedule_id` INT(11) DEFAULT NULL COMMENT 'Reference to customer_account_schedule.id if payment is for specific month',
    `transaction_type` ENUM('downpayment', 'monthly_payment', 'late_fee', 'adjustment') DEFAULT 'monthly_payment',
    `amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `payment_method` ENUM('cash', 'card', 'bank_transfer', 'check') DEFAULT 'cash',
    `receipt_number` VARCHAR(50) NULL,
    `notes` TEXT NULL,
    `processed_by` INT(30) NULL COMMENT 'Admin/staff who processed the payment (users.id)',
    `transaction_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_account_id` (`account_id`),
    KEY `idx_schedule_id` (`schedule_id`),
    KEY `idx_transaction_date` (`transaction_date`),
    KEY `idx_transaction_type` (`transaction_type`),
    KEY `idx_processed_by` (`processed_by`),
    KEY `idx_transactions_account_date` (`account_id`, `transaction_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Customer Account Notifications Table
CREATE TABLE `customer_account_notifications` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `account_id` INT(11) NOT NULL COMMENT 'Reference to customer_account_balances.id',
    `schedule_id` INT(11) DEFAULT NULL COMMENT 'Reference to customer_account_schedule.id',
    `client_id` INT(30) NOT NULL COMMENT 'Reference to client_list.id',
    `notification_type` ENUM('upcoming_due_date', 'late_payment', 'payment_received', 'overdue_reminder', 'account_status') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_account_id` (`account_id`),
    KEY `idx_client_id` (`client_id`),
    KEY `idx_is_read` (`is_read`),
    KEY `idx_notification_type` (`notification_type`),
    KEY `idx_notifications_client_read` (`client_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Admin Activity Log Table (for ActivityLogger)
CREATE TABLE `admin_activity_log` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL COMMENT 'Reference to users.id',
    `role` VARCHAR(50) DEFAULT 'admin',
    `action` TEXT NOT NULL,
    `module` VARCHAR(100) DEFAULT NULL,
    `reference_id` INT(11) DEFAULT NULL,
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_timestamp` (`timestamp`),
    KEY `idx_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- STEP 4: Optional - Add Foreign Key Constraints
-- Uncomment these if you want to enforce referential integrity
-- Make sure the referenced tables (client_list, order_list, users, etc.) exist first
-- ============================================

/*
-- Foreign keys for customer_account_balances
ALTER TABLE `customer_account_balances`
    ADD CONSTRAINT `fk_cab_client` 
    FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customer_account_balances`
    ADD CONSTRAINT `fk_cab_order` 
    FOREIGN KEY (`order_id`) REFERENCES `order_list` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- Foreign keys for customer_account_schedule
ALTER TABLE `customer_account_schedule`
    ADD CONSTRAINT `fk_cas_account` 
    FOREIGN KEY (`account_id`) REFERENCES `customer_account_balances` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

-- Foreign keys for customer_account_transactions
ALTER TABLE `customer_account_transactions`
    ADD CONSTRAINT `fk_cat_account` 
    FOREIGN KEY (`account_id`) REFERENCES `customer_account_balances` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `customer_account_transactions`
    ADD CONSTRAINT `fk_cat_schedule` 
    FOREIGN KEY (`schedule_id`) REFERENCES `customer_account_schedule` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE;

-- Foreign keys for customer_account_notifications
ALTER TABLE `customer_account_notifications`
    ADD CONSTRAINT `fk_can_account` 
    FOREIGN KEY (`account_id`) REFERENCES `customer_account_balances` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `customer_account_notifications`
    ADD CONSTRAINT `fk_can_schedule` 
    FOREIGN KEY (`schedule_id`) REFERENCES `customer_account_schedule` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `customer_account_notifications`
    ADD CONSTRAINT `fk_can_client` 
    FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;
*/

-- ============================================
-- Verification
-- ============================================
SELECT 'Payment tables recreated successfully!' AS status;
SELECT COUNT(*) AS table_count FROM information_schema.tables 
WHERE table_schema = DATABASE() 
AND table_name IN ('customer_account_balances', 'customer_account_schedule', 'customer_account_transactions', 'customer_account_notifications', 'admin_activity_log');

