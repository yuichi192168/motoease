-- Customer Account Balance System Database Tables
-- Star Honda Calamba - Motorcycle Management System
-- Final Version with Late Fee Logic

-- Create customer_account_balances table (Main account record)
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
    KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create customer_account_schedule table (Monthly payment schedule)
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
    KEY `account_due_status` (`account_id`, `due_date`, `payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create customer_account_transactions table (Payment transaction history)
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
    KEY `transaction_type` (`transaction_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create customer_account_notifications table (Notifications for due dates, late payments, etc.)
CREATE TABLE IF NOT EXISTS `customer_account_notifications` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `account_id` INT(11) NOT NULL,
    `schedule_id` INT(11) DEFAULT NULL,
    `client_id` INT(30) NOT NULL COMMENT 'Reference to client_list.id',
    `notification_type` ENUM('upcoming_due_date', 'late_payment', 'payment_received', 'overdue_reminder') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `account_id` (`account_id`),
    KEY `client_id` (`client_id`),
    KEY `is_read` (`is_read`),
    KEY `notification_type` (`notification_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add indexes for better performance
CREATE INDEX idx_account_balances_client_status ON customer_account_balances(client_id, status);
CREATE INDEX idx_account_schedule_account_due ON customer_account_schedule(account_id, due_date);
CREATE INDEX idx_transactions_account_date ON customer_account_transactions(account_id, transaction_date);
CREATE INDEX idx_notifications_client_read ON customer_account_notifications(client_id, is_read);


