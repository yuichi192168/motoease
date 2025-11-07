-- Add penalty_amount to installment_payments to record late payment penalty per month
-- Safe on MySQL 8+/MariaDB: IF NOT EXISTS; remove if unsupported.

ALTER TABLE `installment_payments`
  ADD COLUMN IF NOT EXISTS `penalty_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `amount_paid`;

-- Optional: quick check
-- DESC `installment_payments`;
-- SELECT id, contract_id, installment_number, amount_paid, penalty_amount, payment_date FROM installment_payments ORDER BY id DESC LIMIT 10;



