-- Fix Payment Submission Tables
-- This script ensures all required tables exist and are properly structured
-- Run this if you encounter errors when submitting payments

-- ============================================
-- 1. Customer Account Balances Table
-- ============================================
CREATE TABLE IF NOT EXISTS `customer_account_balances` (
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
    KEY `client_id` (`client_id`),
    KEY `order_id` (`order_id`),
    KEY `status` (`status`),
    KEY `created_at` (`created_at`),
    KEY `idx_account_balances_client_status` (`client_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- 2. Customer Account Schedule Table
-- ============================================
CREATE TABLE IF NOT EXISTS `customer_account_schedule` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `account_id` INT(11) NOT NULL COMMENT 'Reference to customer_account_balances',
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
    KEY `account_id` (`account_id`),
    KEY `due_date` (`due_date`),
    KEY `payment_status` (`payment_status`),
    KEY `account_due_status` (`account_id`, `due_date`, `payment_status`),
    KEY `idx_account_schedule_account_due` (`account_id`, `due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- 3. Customer Account Transactions Table
-- ============================================
CREATE TABLE IF NOT EXISTS `customer_account_transactions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `account_id` INT(11) NOT NULL,
    `schedule_id` INT(11) DEFAULT NULL COMMENT 'Reference to customer_account_schedule if payment is for specific month',
    `transaction_type` ENUM('downpayment', 'monthly_payment', 'late_fee', 'adjustment') DEFAULT 'monthly_payment',
    `amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `payment_method` ENUM('cash', 'card', 'bank_transfer', 'check') DEFAULT 'cash',
    `receipt_number` VARCHAR(50) NULL,
    `notes` TEXT NULL,
    `processed_by` INT(30) NULL COMMENT 'Admin/staff who processed the payment',
    `transaction_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `account_id` (`account_id`),
    KEY `schedule_id` (`schedule_id`),
    KEY `transaction_date` (`transaction_date`),
    KEY `transaction_type` (`transaction_type`),
    KEY `idx_transactions_account_date` (`account_id`, `transaction_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- 4. Customer Account Notifications Table
-- ============================================
CREATE TABLE IF NOT EXISTS `customer_account_notifications` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `account_id` INT(11) NOT NULL,
    `schedule_id` INT(11) DEFAULT NULL,
    `client_id` INT(30) NOT NULL COMMENT 'Reference to client_list.id',
    `notification_type` ENUM('upcoming_due_date', 'late_payment', 'payment_received', 'overdue_reminder', 'account_status') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `account_id` (`account_id`),
    KEY `client_id` (`client_id`),
    KEY `is_read` (`is_read`),
    KEY `notification_type` (`notification_type`),
    KEY `idx_notifications_client_read` (`client_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- 5. Admin Activity Log Table (for ActivityLogger)
-- ============================================
CREATE TABLE IF NOT EXISTS `admin_activity_log` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL COMMENT 'Reference to users.id',
    `role` VARCHAR(50) DEFAULT 'admin',
    `action` TEXT NOT NULL,
    `module` VARCHAR(100) DEFAULT NULL,
    `reference_id` INT(11) DEFAULT NULL,
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `timestamp` (`timestamp`),
    KEY `module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- 6. Add missing columns if they don't exist
-- ============================================

-- Add notification_type 'account_status' if it doesn't exist
ALTER TABLE `customer_account_notifications` 
MODIFY COLUMN `notification_type` ENUM('upcoming_due_date', 'late_payment', 'payment_received', 'overdue_reminder', 'account_status') NOT NULL;

-- ============================================
-- 7. Verify tables were created
-- ============================================
SELECT 'Tables created/verified successfully!' AS status;


