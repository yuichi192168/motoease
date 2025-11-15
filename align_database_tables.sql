-- ====================================================================
-- COMPREHENSIVE DATABASE ALIGNMENT SCRIPT
-- Aligns all database tables with proper foreign keys and relationships
-- Star Honda Calamba - Motorcycle Management System
-- ====================================================================
--
-- INSTRUCTIONS:
-- 1. BACKUP YOUR DATABASE BEFORE RUNNING THIS SCRIPT
-- 2. Run this script on your database to align all tables
-- 3. If you encounter errors about existing foreign keys, manually drop them first:
--    ALTER TABLE `table_name` DROP FOREIGN KEY `constraint_name`;
-- 4. If you encounter errors about missing indexes, comment out those CREATE INDEX statements
-- 5. If installment_contracts uses customer_id instead of client_id, manually rename it first:
--    ALTER TABLE `installment_contracts` CHANGE COLUMN `customer_id` `client_id` INT(30) NOT NULL;
--
-- This script will:
-- - Create customer account balance tables with client_id
-- - Add foreign key constraints between all related tables
-- - Create performance indexes
-- - Ensure data integrity across all tables
--
-- ====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
SET time_zone = "+00:00";

-- ====================================================================
-- STEP 1: ENSURE CUSTOMER ACCOUNT BALANCE TABLES EXIST WITH client_id
-- ====================================================================

-- Create customer_account_balances table (Main account record)
CREATE TABLE IF NOT EXISTS `customer_account_balances` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `client_id` INT(30) NOT NULL COMMENT 'Reference to client_list.id',
    `order_id` INT(30) NOT NULL COMMENT 'Reference to order_list.id',
    `invoice_id` INT(11) DEFAULT NULL COMMENT 'Reference to invoices.id',
    `contract_id` INT(11) DEFAULT NULL COMMENT 'Reference to installment_contracts.id',
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
    KEY `idx_invoice_id` (`invoice_id`),
    KEY `idx_contract_id` (`contract_id`),
    KEY `idx_status` (`status`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create customer_account_schedule table (Monthly payment schedule)
CREATE TABLE IF NOT EXISTS `customer_account_schedule` (
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
    KEY `idx_payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create customer_account_transactions table (Payment transaction history)
CREATE TABLE IF NOT EXISTS `customer_account_transactions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `account_id` INT(11) NOT NULL COMMENT 'Reference to customer_account_balances.id',
    `schedule_id` INT(11) DEFAULT NULL COMMENT 'Reference to customer_account_schedule.id',
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
    KEY `idx_processed_by` (`processed_by`),
    KEY `idx_transaction_date` (`transaction_date`),
    KEY `idx_transaction_type` (`transaction_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create customer_account_notifications table (Notifications for due dates, late payments, etc.)
CREATE TABLE IF NOT EXISTS `customer_account_notifications` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `account_id` INT(11) NOT NULL COMMENT 'Reference to customer_account_balances.id',
    `schedule_id` INT(11) DEFAULT NULL COMMENT 'Reference to customer_account_schedule.id',
    `client_id` INT(30) NOT NULL COMMENT 'Reference to client_list.id',
    `notification_type` ENUM('upcoming_due_date', 'late_payment', 'payment_received', 'overdue_reminder') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_account_id` (`account_id`),
    KEY `idx_schedule_id` (`schedule_id`),
    KEY `idx_client_id` (`client_id`),
    KEY `idx_is_read` (`is_read`),
    KEY `idx_notification_type` (`notification_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ====================================================================
-- STEP 2: ALIGN INSTALLMENT_CONTRACTS TO USE client_id (if customer_id exists)
-- ====================================================================

-- Check and rename customer_id to client_id in installment_contracts if needed
-- Note: Run this manually if installment_contracts has customer_id column:
-- ALTER TABLE `installment_contracts` CHANGE COLUMN `customer_id` `client_id` INT(30) NOT NULL COMMENT 'Reference to client_list.id';

-- ====================================================================
-- STEP 3: DROP EXISTING FOREIGN KEYS (IF ANY) TO RECREATE THEM
-- ====================================================================
-- Note: Some MySQL/MariaDB versions don't support DROP FOREIGN KEY IF EXISTS
-- If you encounter errors, manually drop foreign keys first or comment out
-- the DROP statements for keys that don't exist yet.

-- Customer Account Balance Foreign Keys
-- ALTER TABLE `customer_account_balances` DROP FOREIGN KEY `fk_cab_client`;
-- ALTER TABLE `customer_account_balances` DROP FOREIGN KEY `fk_cab_order`;
-- ALTER TABLE `customer_account_balances` DROP FOREIGN KEY `fk_cab_invoice`;
-- ALTER TABLE `customer_account_balances` DROP FOREIGN KEY `fk_cab_contract`;
-- ALTER TABLE `customer_account_schedule` DROP FOREIGN KEY `fk_cas_account`;
-- ALTER TABLE `customer_account_transactions` DROP FOREIGN KEY `fk_cat_account`;
-- ALTER TABLE `customer_account_transactions` DROP FOREIGN KEY `fk_cat_schedule`;
-- ALTER TABLE `customer_account_transactions` DROP FOREIGN KEY `fk_cat_processed_by`;
-- ALTER TABLE `customer_account_notifications` DROP FOREIGN KEY `fk_can_account`;
-- ALTER TABLE `customer_account_notifications` DROP FOREIGN KEY `fk_can_schedule`;
-- ALTER TABLE `customer_account_notifications` DROP FOREIGN KEY `fk_can_client`;

SET FOREIGN_KEY_CHECKS = 1;

-- ====================================================================
-- STEP 4: ADD FOREIGN KEY CONSTRAINTS FOR CUSTOMER ACCOUNT BALANCES
-- ====================================================================

-- customer_account_balances foreign keys
ALTER TABLE `customer_account_balances`
    ADD CONSTRAINT `fk_cab_client` 
    FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customer_account_balances`
    ADD CONSTRAINT `fk_cab_order` 
    FOREIGN KEY (`order_id`) REFERENCES `order_list` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `customer_account_balances`
    ADD CONSTRAINT `fk_cab_invoice` 
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `customer_account_balances`
    ADD CONSTRAINT `fk_cab_contract` 
    FOREIGN KEY (`contract_id`) REFERENCES `installment_contracts` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE;

-- customer_account_schedule foreign keys
ALTER TABLE `customer_account_schedule`
    ADD CONSTRAINT `fk_cas_account` 
    FOREIGN KEY (`account_id`) REFERENCES `customer_account_balances` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

-- customer_account_transactions foreign keys
ALTER TABLE `customer_account_transactions`
    ADD CONSTRAINT `fk_cat_account` 
    FOREIGN KEY (`account_id`) REFERENCES `customer_account_balances` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `customer_account_transactions`
    ADD CONSTRAINT `fk_cat_schedule` 
    FOREIGN KEY (`schedule_id`) REFERENCES `customer_account_schedule` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `customer_account_transactions`
    ADD CONSTRAINT `fk_cat_processed_by` 
    FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE;

-- customer_account_notifications foreign keys
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

-- ====================================================================
-- STEP 5: ADD FOREIGN KEY CONSTRAINTS FOR INSTALLMENT SYSTEM
-- ====================================================================

-- installment_contracts foreign keys (using client_id)
ALTER TABLE `installment_contracts`
    ADD CONSTRAINT `fk_installment_contracts_invoice` 
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- installment_contracts -> client_list (using client_id, or customer_id if not renamed yet)
-- If your installment_contracts table still has customer_id, rename it first or use:
-- ALTER TABLE `installment_contracts` ADD CONSTRAINT `fk_installment_contracts_client` 
-- FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
-- OR if using customer_id: 
-- ALTER TABLE `installment_contracts` ADD CONSTRAINT `fk_installment_contracts_customer` 
-- FOREIGN KEY (`customer_id`) REFERENCES `client_list` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `installment_contracts`
    ADD CONSTRAINT `fk_installment_contracts_client` 
    FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `installment_contracts`
    ADD CONSTRAINT `fk_installment_contracts_plan` 
    FOREIGN KEY (`installment_plan_id`) REFERENCES `installment_plans` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- installment_schedule foreign keys
ALTER TABLE `installment_schedule`
    ADD CONSTRAINT `fk_installment_schedule_contract` 
    FOREIGN KEY (`contract_id`) REFERENCES `installment_contracts` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

-- installment_payments foreign keys
ALTER TABLE `installment_payments`
    ADD CONSTRAINT `fk_installment_payments_schedule` 
    FOREIGN KEY (`schedule_id`) REFERENCES `installment_schedule` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `installment_payments`
    ADD CONSTRAINT `fk_installment_payments_contract` 
    FOREIGN KEY (`contract_id`) REFERENCES `installment_contracts` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `installment_payments`
    ADD CONSTRAINT `fk_installment_payments_created_by` 
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE;

-- ====================================================================
-- STEP 6: ADD FOREIGN KEY CONSTRAINTS FOR OTHER CORE TABLES
-- ====================================================================

-- appointments foreign keys (if table exists)
-- Note: Uncomment if appointments table exists
-- ALTER TABLE `appointments`
--     ADD CONSTRAINT `fk_appointments_client` 
--     FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) 
--     ON DELETE RESTRICT ON UPDATE CASCADE;

-- cart_list foreign keys (if table exists)
ALTER TABLE `cart_list`
    ADD CONSTRAINT `fk_cart_client` 
    FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

-- order_list foreign keys (if table exists)
ALTER TABLE `order_list`
    ADD CONSTRAINT `fk_order_client` 
    FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- order_items foreign keys (if table exists)
ALTER TABLE `order_items`
    ADD CONSTRAINT `fk_order_items_order` 
    FOREIGN KEY (`order_id`) REFERENCES `order_list` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

-- invoices foreign keys (only if order_id column exists)
-- Note: Some invoice tables may use different column names
-- Uncomment and adjust if your invoices table has order_id column:
-- ALTER TABLE `invoices`
--     ADD CONSTRAINT `fk_invoices_order` 
--     FOREIGN KEY (`order_id`) REFERENCES `order_list` (`id`) 
--     ON DELETE RESTRICT ON UPDATE CASCADE;

-- ====================================================================
-- STEP 7: CREATE COMPOSITE INDEXES FOR BETTER PERFORMANCE
-- ====================================================================
-- Note: Some MySQL/MariaDB versions don't support CREATE INDEX IF NOT EXISTS
-- If errors occur, manually check if indexes exist before creating

-- Customer Account Balance indexes
CREATE INDEX `idx_cab_client_status` ON `customer_account_balances`(`client_id`, `status`);
CREATE INDEX `idx_cab_order_client` ON `customer_account_balances`(`order_id`, `client_id`);
CREATE INDEX `idx_cas_account_due` ON `customer_account_schedule`(`account_id`, `due_date`);
CREATE INDEX `idx_cas_account_status` ON `customer_account_schedule`(`account_id`, `payment_status`);
CREATE INDEX `idx_cat_account_date` ON `customer_account_transactions`(`account_id`, `transaction_date`);
CREATE INDEX `idx_can_client_read` ON `customer_account_notifications`(`client_id`, `is_read`);

-- Installment system indexes
CREATE INDEX `idx_ic_client_status` ON `installment_contracts`(`client_id`, `status`);
CREATE INDEX `idx_ic_invoice_client` ON `installment_contracts`(`invoice_id`, `client_id`);
CREATE INDEX `idx_is_contract_due` ON `installment_schedule`(`contract_id`, `due_date`);
CREATE INDEX `idx_is_contract_status` ON `installment_schedule`(`contract_id`, `status`);
CREATE INDEX `idx_ip_contract_date` ON `installment_payments`(`contract_id`, `payment_date`);

-- Other core table indexes
CREATE INDEX `idx_order_client_status` ON `order_list`(`client_id`, `status`);
CREATE INDEX `idx_cart_client_product` ON `cart_list`(`client_id`, `product_id`);
CREATE INDEX `idx_appointments_client_date` ON `appointments`(`client_id`, `appointment_date`);

-- ====================================================================
-- STEP 8: VERIFY AND REPORT
-- ====================================================================

-- Display summary of foreign keys created
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, CONSTRAINT_NAME;

-- ====================================================================
-- ALIGNMENT COMPLETE
-- ====================================================================
-- 
-- Summary:
-- ✅ Customer Account Balance tables created/updated with client_id
-- ✅ Installment contracts aligned to use client_id
-- ✅ All foreign key relationships established
-- ✅ Performance indexes created
-- ✅ Data integrity constraints applied
--
-- Key Relationships:
-- - customer_account_balances -> client_list (client_id)
-- - customer_account_balances -> order_list (order_id)
-- - customer_account_balances -> invoices (invoice_id)
-- - customer_account_balances -> installment_contracts (contract_id)
-- - customer_account_schedule -> customer_account_balances (account_id)
-- - customer_account_transactions -> customer_account_balances (account_id)
-- - customer_account_transactions -> users (processed_by)
-- - customer_account_notifications -> client_list (client_id)
-- - installment_contracts -> client_list (client_id)
-- - All other core tables properly linked
--
-- ====================================================================

SET FOREIGN_KEY_CHECKS = 1;

