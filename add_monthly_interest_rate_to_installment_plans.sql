-- Add monthly_interest_rate to installment_plans for per-plan monthly interest
-- Safe on MySQL 8+/MariaDB: uses IF NOT EXISTS. For older versions, remove the IF NOT EXISTS.

ALTER TABLE `installment_plans`
  ADD COLUMN IF NOT EXISTS `monthly_interest_rate` DECIMAL(6,4) NOT NULL DEFAULT 0.0000 AFTER `number_of_installments`;

-- Optional: seed a default interest rate for existing plans (adjust as needed)
-- UPDATE `installment_plans` SET `monthly_interest_rate` = 0.0200 WHERE `monthly_interest_rate` = 0.0000;

-- Verification query
-- SELECT id, plan_name, number_of_installments, monthly_interest_rate FROM `installment_plans`;


