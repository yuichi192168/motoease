-- Installment Invoice System Database Tables for MotoEase
-- Star Honda Calamba - Motorcycle Management System

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
    `contract_number` VARCHAR(50) UNIQUE NOT NULL,
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
    `payment_reference` VARCHAR(50) UNIQUE NOT NULL,
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
    KEY `schedule_id` (`schedule_id`),
    KEY `contract_id` (`contract_id`),
    KEY `receipt_number` (`receipt_number`),
    KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default installment plans
INSERT IGNORE INTO `installment_plans` (`plan_name`, `description`, `number_of_installments`, `interest_rate`, `down_payment_percentage`, `status`) VALUES
('3 Months - No Interest', '3 monthly installments with no interest', 3, 0.00, 30.00, 'active'),
('6 Months - No Interest', '6 monthly installments with no interest', 6, 0.00, 30.00, 'active'),
('12 Months - Low Interest', '12 monthly installments with 2% interest', 12, 2.00, 20.00, 'active'),
('18 Months - Standard', '18 monthly installments with 5% interest', 18, 5.00, 20.00, 'active'),
('24 Months - Standard', '24 monthly installments with 5% interest', 24, 5.00, 20.00, 'active');

