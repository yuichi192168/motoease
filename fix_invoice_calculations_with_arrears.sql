-- Fix Invoice Calculations with Arrears Computation
-- This script updates the invoice_financials view to properly calculate:
-- 1. Interest amounts from installment plans
-- 2. Late fees/penalties for overdue invoices
-- 3. Arrears (accumulated penalties from overdue installment schedules)
-- 4. Total balance including all charges

-- First, ensure invoice_settings has penalty configuration
INSERT INTO `invoice_settings` (`setting_key`, `setting_value`, `description`) VALUES
('late_fee_daily_rate', '0.50', 'Late fee daily rate in percent (e.g., 0.50 = 0.5%/day)'),
('penalty_rate_monthly', '3.00', 'Penalty rate per month for overdue installments (e.g., 3.00 = 3%/month)'),
('penalty_grace_period_days', '7', 'Grace period in days before penalty applies')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Add penalty_amount column to installment_schedule if it doesn't exist
-- Check if column exists first (MySQL 5.7 compatible)
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'installment_schedule' 
  AND COLUMN_NAME = 'penalty_amount';

SET @sql = IF(@col_exists = 0,
  'ALTER TABLE `installment_schedule` ADD COLUMN `penalty_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `late_fee`',
  'SELECT "Column penalty_amount already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update the invoice_financials view with comprehensive calculations
DROP VIEW IF EXISTS `invoice_financials`;

CREATE VIEW `invoice_financials` AS
SELECT 
  i.id,
  i.invoice_number,
  i.order_id,
  i.customer_id,
  i.transaction_type,
  i.payment_type,
  i.subtotal,
  i.vat_amount,
  i.total_amount,
  i.generated_at,
  i.due_date,
  i.payment_status AS stored_status,
  COALESCE(SUM(r.amount_paid), 0) AS total_paid,
  MAX(r.issued_at) AS payment_date,
  
  -- Calculate interest amount from installment schedules
  COALESCE((
    SELECT SUM(isch.interest_amount)
    FROM installment_contracts ic
    INNER JOIN installment_schedule isch ON ic.id = isch.contract_id
    WHERE ic.invoice_id = i.id
      AND isch.status IN ('pending', 'overdue', 'partial')
  ), 0) AS interest_amount,
  
  -- Calculate late fee for overdue invoices (non-installment)
  CASE 
    WHEN i.payment_type != 'installment' 
      AND i.due_date IS NOT NULL 
      AND CURDATE() > i.due_date 
      AND (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) > 0 THEN 
      GREATEST(DATEDIFF(CURDATE(), i.due_date), 0) * (
        (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) * (
          (SELECT CAST(setting_value AS DECIMAL(10,4)) / 100 FROM invoice_settings WHERE setting_key = 'late_fee_daily_rate' LIMIT 1)
        )
      )
    ELSE 0
  END AS late_fee_amount,
  
  -- Calculate arrears (accumulated penalties from overdue installment schedules)
  COALESCE((
    SELECT SUM(
      CASE 
        WHEN isch.due_date < CURDATE() 
          AND isch.status IN ('pending', 'overdue', 'partial')
          AND DATEDIFF(CURDATE(), isch.due_date) >= (
            SELECT CAST(setting_value AS UNSIGNED) FROM invoice_settings WHERE setting_key = 'penalty_grace_period_days' LIMIT 1
          ) THEN
          -- Calculate penalty: 3% per month (or as configured) of the remaining amount due
          (isch.amount_due - COALESCE(isch.paid_amount, 0)) * 
          (SELECT CAST(setting_value AS DECIMAL(10,4)) / 100 FROM invoice_settings WHERE setting_key = 'penalty_rate_monthly' LIMIT 1) *
          GREATEST(CEIL(DATEDIFF(CURDATE(), isch.due_date) / 30.0), 1)
        ELSE 0
      END
    )
    FROM installment_contracts ic
    INNER JOIN installment_schedule isch ON ic.id = isch.contract_id
    WHERE ic.invoice_id = i.id
      AND ic.status = 'active'
  ), 0) AS arrears_amount,
  
  -- Balance remaining (original amount - payments)
  (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) AS balance_remaining,
  
  -- Total balance due (including interest, late fees, and arrears)
  (
    (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) +
    COALESCE((
      SELECT SUM(isch.interest_amount)
      FROM installment_contracts ic
      INNER JOIN installment_schedule isch ON ic.id = isch.contract_id
      WHERE ic.invoice_id = i.id
        AND isch.status IN ('pending', 'overdue', 'partial')
    ), 0) +
    CASE 
      WHEN i.payment_type != 'installment' 
        AND i.due_date IS NOT NULL 
        AND CURDATE() > i.due_date 
        AND (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) > 0 THEN 
        GREATEST(DATEDIFF(CURDATE(), i.due_date), 0) * (
          (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) * (
            (SELECT CAST(setting_value AS DECIMAL(10,4)) / 100 FROM invoice_settings WHERE setting_key = 'late_fee_daily_rate' LIMIT 1)
          )
        )
      ELSE 0
    END +
    COALESCE((
      SELECT SUM(
        CASE 
          WHEN isch.due_date < CURDATE() 
            AND isch.status IN ('pending', 'overdue', 'partial')
            AND DATEDIFF(CURDATE(), isch.due_date) >= (
              SELECT CAST(setting_value AS UNSIGNED) FROM invoice_settings WHERE setting_key = 'penalty_grace_period_days' LIMIT 1
            ) THEN
            (isch.amount_due - COALESCE(isch.paid_amount, 0)) * 
            (SELECT CAST(setting_value AS DECIMAL(10,4)) / 100 FROM invoice_settings WHERE setting_key = 'penalty_rate_monthly' LIMIT 1) *
            GREATEST(CEIL(DATEDIFF(CURDATE(), isch.due_date) / 30.0), 1)
          ELSE 0
        END
      )
      FROM installment_contracts ic
      INNER JOIN installment_schedule isch ON ic.id = isch.contract_id
      WHERE ic.invoice_id = i.id
        AND ic.status = 'active'
    ), 0)
  ) AS total_balance_due,
  
  -- Computed status
  CASE 
    WHEN COALESCE(SUM(r.amount_paid),0) >= i.total_amount THEN 'paid'
    WHEN i.due_date IS NOT NULL AND CURDATE() > i.due_date AND (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) > 0 THEN 'late'
    WHEN (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) > 0 THEN 'pending'
    ELSE 'paid'
  END AS computed_status

FROM invoices i
LEFT JOIN receipts r ON r.invoice_id = i.id
GROUP BY i.id;

-- Create a function to update penalty amounts in installment_schedule
-- This will be called periodically or when payments are made
DROP PROCEDURE IF EXISTS `update_installment_penalties`;

DELIMITER $$

CREATE PROCEDURE `update_installment_penalties`()
BEGIN
  DECLARE penalty_rate DECIMAL(10,4);
  DECLARE grace_period INT;
  
  -- Get settings
  SELECT CAST(setting_value AS DECIMAL(10,4)) / 100 INTO penalty_rate
  FROM invoice_settings WHERE setting_key = 'penalty_rate_monthly' LIMIT 1;
  
  SELECT CAST(setting_value AS UNSIGNED) INTO grace_period
  FROM invoice_settings WHERE setting_key = 'penalty_grace_period_days' LIMIT 1;
  
  -- Set defaults if not found
  IF penalty_rate IS NULL THEN SET penalty_rate = 0.03; END IF; -- 3% default
  IF grace_period IS NULL THEN SET grace_period = 7; END IF; -- 7 days default
  
  -- Update penalties for overdue installments
  UPDATE installment_schedule isch
  INNER JOIN installment_contracts ic ON isch.contract_id = ic.id
  SET 
    isch.penalty_amount = CASE 
      WHEN isch.due_date < CURDATE() 
        AND isch.status IN ('pending', 'overdue', 'partial')
        AND DATEDIFF(CURDATE(), isch.due_date) >= grace_period THEN
        (isch.amount_due - COALESCE(isch.paid_amount, 0)) * penalty_rate * GREATEST(CEIL(DATEDIFF(CURDATE(), isch.due_date) / 30.0), 1)
      ELSE 0
    END,
    isch.status = CASE
      WHEN isch.due_date < CURDATE() AND isch.status = 'pending' AND (isch.amount_due - COALESCE(isch.paid_amount, 0)) > 0 THEN 'overdue'
      WHEN isch.amount_due - COALESCE(isch.paid_amount, 0) <= 0.01 AND isch.status != 'paid' THEN 'paid'
      WHEN isch.paid_amount > 0 AND isch.paid_amount < isch.amount_due THEN 'partial'
      ELSE isch.status
    END
  WHERE ic.status = 'active';
END$$

DELIMITER ;

-- Create a trigger to automatically update penalties when installment_schedule is updated
DROP TRIGGER IF EXISTS `trg_installment_schedule_update_penalties`;

DELIMITER $$

CREATE TRIGGER `trg_installment_schedule_update_penalties`
BEFORE UPDATE ON `installment_schedule`
FOR EACH ROW
BEGIN
  DECLARE penalty_rate DECIMAL(10,4);
  DECLARE grace_period INT;
  
  -- Get settings
  SELECT CAST(setting_value AS DECIMAL(10,4)) / 100 INTO penalty_rate
  FROM invoice_settings WHERE setting_key = 'penalty_rate_monthly' LIMIT 1;
  
  SELECT CAST(setting_value AS UNSIGNED) INTO grace_period
  FROM invoice_settings WHERE setting_key = 'penalty_grace_period_days' LIMIT 1;
  
  -- Set defaults if not found
  IF penalty_rate IS NULL THEN SET penalty_rate = 0.03; END IF;
  IF grace_period IS NULL THEN SET grace_period = 7; END IF;
  
  -- Auto-calculate penalty if overdue
  IF NEW.due_date < CURDATE() 
    AND NEW.status IN ('pending', 'overdue', 'partial')
    AND DATEDIFF(CURDATE(), NEW.due_date) >= grace_period THEN
    SET NEW.penalty_amount = (NEW.amount_due - COALESCE(NEW.paid_amount, 0)) * penalty_rate * GREATEST(CEIL(DATEDIFF(CURDATE(), NEW.due_date) / 30.0), 1);
  END IF;
  
  -- Auto-update status to overdue if past due date
  IF NEW.due_date < CURDATE() 
    AND NEW.status = 'pending' 
    AND (NEW.amount_due - COALESCE(NEW.paid_amount, 0)) > 0 THEN
    SET NEW.status = 'overdue';
  END IF;
END$$

DELIMITER ;

-- Create a scheduled event to update penalties daily (optional - requires event scheduler enabled)
-- Uncomment if you want automatic daily updates
/*
SET GLOBAL event_scheduler = ON;

DROP EVENT IF EXISTS `daily_penalty_update`;

CREATE EVENT `daily_penalty_update`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY
DO
  CALL update_installment_penalties();
*/

-- Verify the view was created correctly
SELECT 'Invoice financials view updated successfully. Columns:' AS message;
SHOW COLUMNS FROM invoice_financials;

