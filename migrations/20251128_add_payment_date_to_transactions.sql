-- Migration: Add payment_date to customer_account_transactions and backfill
-- Run in your MySQL environment: mysql -u user -p database_name < this_file.sql

SET @db_name = DATABASE();

-- 1. Add column if it doesn't exist
ALTER TABLE `customer_account_transactions`
ADD COLUMN IF NOT EXISTS `payment_date` DATETIME DEFAULT NULL;

-- 2. Backfill payment_date from transaction_date where present
UPDATE `customer_account_transactions`
SET payment_date = transaction_date
WHERE payment_date IS NULL AND transaction_date IS NOT NULL;

-- 3. For any transactions still missing payment_date, try to backfill from schedule paid_date
UPDATE `customer_account_transactions` t
LEFT JOIN `customer_account_schedule` s ON t.schedule_id = s.id
SET t.payment_date = s.paid_date
WHERE t.payment_date IS NULL AND s.paid_date IS NOT NULL;

-- 4. Optional: index for faster lookups
ALTER TABLE `customer_account_transactions`
ADD INDEX IF NOT EXISTS idx_transactions_payment_date (`payment_date`);

-- 5. Report counts (for manual verification)
SELECT COUNT(*) AS total_transactions FROM customer_account_transactions;
SELECT COUNT(*) AS filled_payment_date FROM customer_account_transactions WHERE payment_date IS NOT NULL;
SELECT COUNT(*) AS null_payment_date FROM customer_account_transactions WHERE payment_date IS NULL;

-- End of migration
