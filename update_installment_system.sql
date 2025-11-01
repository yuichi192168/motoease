-- ====================================================================
-- MotoEase Installment System Update Script
-- Star Honda Calamba - Motorcycle Management System
-- ====================================================================
-- This script adds the installment system to existing MotoEase database
-- Run this AFTER your main database structure is in place
-- ====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ====================================================================
-- INSTALLMENT SYSTEM TABLES
-- ====================================================================

-- Create installment_plans table
CREATE TABLE IF NOT EXISTS `installment_plans` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `plan_name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `number_of_installments` INT(11) NOT NULL,
    `interest_rate` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Interest rate in percentage',
    `down_payment_percentage` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Down payment percentage',
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create installment_contracts table
CREATE TABLE IF NOT EXISTS `installment_contracts` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `contract_number` VARCHAR(50) NOT NULL,
    `invoice_id` INT(11) NOT NULL,
    `customer_id` INT(30) NOT NULL,
    `installment_plan_id` INT(11) NOT NULL,
    `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `down_payment_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `remaining_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `status` ENUM('active', 'completed', 'defaulted', 'cancelled') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `contract_number` (`contract_number`),
    KEY `invoice_id` (`invoice_id`),
    KEY `customer_id` (`customer_id`),
    KEY `installment_plan_id` (`installment_plan_id`),
    KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create installment_schedule table
CREATE TABLE IF NOT EXISTS `installment_schedule` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `contract_id` INT(11) NOT NULL,
    `installment_number` INT(11) NOT NULL,
    `due_date` DATE NOT NULL,
    `amount_due` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `principal_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `interest_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('pending', 'paid', 'overdue', 'partial') DEFAULT 'pending',
    `paid_amount` DECIMAL(15,2) DEFAULT 0.00,
    `paid_date` DATETIME NULL,
    `late_fee` DECIMAL(15,2) DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `contract_id` (`contract_id`),
    KEY `due_date` (`due_date`),
    KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create installment_payments table
CREATE TABLE IF NOT EXISTS `installment_payments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `payment_reference` VARCHAR(50) NOT NULL,
    `schedule_id` INT(11) NOT NULL,
    `contract_id` INT(11) NOT NULL,
    `amount_paid` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `payment_date` DATETIME NOT NULL,
    `payment_method` ENUM('cash', 'card', 'bank_transfer', 'check', 'online') NOT NULL DEFAULT 'cash',
    `receipt_number` VARCHAR(50) NULL,
    `notes` TEXT,
    `created_by` INT(30) NULL COMMENT 'Staff who processed the payment',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `payment_reference` (`payment_reference`),
    KEY `schedule_id` (`schedule_id`),
    KEY `contract_id` (`contract_id`),
    KEY `receipt_number` (`receipt_number`),
    KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ====================================================================
-- INSERT DEFAULT DATA
-- ====================================================================

-- Insert default installment plans
INSERT IGNORE INTO `installment_plans` (`plan_name`, `description`, `number_of_installments`, `interest_rate`, `down_payment_percentage`, `status`) VALUES
('3 Months - No Interest', '3 monthly installments with no interest', 3, 0.00, 30.00, 'active'),
('6 Months - No Interest', '6 monthly installments with no interest', 6, 0.00, 30.00, 'active'),
('12 Months - Low Interest', '12 monthly installments with 2% interest', 12, 2.00, 20.00, 'active'),
('18 Months - Standard', '18 monthly installments with 5% interest', 18, 5.00, 20.00, 'active'),
('24 Months - Standard', '24 monthly installments with 5% interest', 24, 5.00, 20.00, 'active');

-- ====================================================================
-- ADD FOREIGN KEY CONSTRAINTS
-- Note: These will only work if parent tables exist and have data
-- ====================================================================

-- Check if tables exist before adding foreign keys
SET @tables_exist = (
    SELECT COUNT(*) 
    FROM information_schema.tables 
    WHERE table_schema = DATABASE() 
    AND table_name IN ('invoices', 'client_list', 'users')
    HAVING COUNT(*) = 3
);

-- Add foreign keys only if all parent tables exist
SET @sql = IF(@tables_exist = 1, '
    -- Drop existing foreign keys if they exist
    SET @drop_fk = (
        SELECT GROUP_CONCAT(CONCAT("ALTER TABLE ", table_name, " DROP FOREIGN KEY ", constraint_name) SEPARATOR ";")
        FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
        AND table_name IN ("installment_contracts", "installment_schedule", "installment_payments")
        AND constraint_type = "FOREIGN KEY"
    );
    
    SET @drop_fk = IFNULL(@drop_fk, "SELECT 1");
    SET @sql_exec = CONCAT("SET FOREIGN_KEY_CHECKS = 0; ", @drop_fk, "; SET FOREIGN_KEY_CHECKS = 1;");
    PREPARE stmt FROM @sql_exec;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    -- Add foreign keys for installment_contracts
    ALTER TABLE `installment_contracts`
        ADD CONSTRAINT `fk_installment_contracts_invoice` 
        FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
        
        ADD CONSTRAINT `fk_installment_contracts_customer` 
        FOREIGN KEY (`customer_id`) REFERENCES `client_list` (`id`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
        
        ADD CONSTRAINT `fk_installment_contracts_plan` 
        FOREIGN KEY (`installment_plan_id`) REFERENCES `installment_plans` (`id`) 
        ON DELETE RESTRICT ON UPDATE CASCADE;
    
    -- Add foreign keys for installment_schedule
    ALTER TABLE `installment_schedule`
        ADD CONSTRAINT `fk_installment_schedule_contract` 
        FOREIGN KEY (`contract_id`) REFERENCES `installment_contracts` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE;
    
    -- Add foreign keys for installment_payments
    ALTER TABLE `installment_payments`
        ADD CONSTRAINT `fk_installment_payments_schedule` 
        FOREIGN KEY (`schedule_id`) REFERENCES `installment_schedule` (`id`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
        
        ADD CONSTRAINT `fk_installment_payments_contract` 
        FOREIGN KEY (`contract_id`) REFERENCES `installment_contracts` (`id`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
        
        ADD CONSTRAINT `fk_installment_payments_created_by` 
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) 
        ON DELETE SET NULL ON UPDATE CASCADE;
', 'SELECT "Parent tables not found. Foreign keys skipped." AS message;');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ====================================================================
-- INSTALLMENT SYSTEM INSTALLED
-- ====================================================================

