-- ====================================================================
-- Add Foreign Key Constraints to Installment System
-- Run this AFTER update_installment_system.sql
-- ====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;

-- ====================================================================
-- DROP EXISTING FOREIGN KEYS (IF ANY)
-- ====================================================================

ALTER TABLE `installment_contracts` DROP FOREIGN KEY IF EXISTS `fk_installment_contracts_invoice`;
ALTER TABLE `installment_contracts` DROP FOREIGN KEY IF EXISTS `fk_installment_contracts_customer`;
ALTER TABLE `installment_contracts` DROP FOREIGN KEY IF EXISTS `fk_installment_contracts_plan`;
ALTER TABLE `installment_schedule` DROP FOREIGN KEY IF EXISTS `fk_installment_schedule_contract`;
ALTER TABLE `installment_payments` DROP FOREIGN KEY IF EXISTS `fk_installment_payments_schedule`;
ALTER TABLE `installment_payments` DROP FOREIGN KEY IF EXISTS `fk_installment_payments_contract`;
ALTER TABLE `installment_payments` DROP FOREIGN KEY IF EXISTS `fk_installment_payments_created_by`;

SET FOREIGN_KEY_CHECKS = 1;

-- ====================================================================
-- ADD FOREIGN KEY CONSTRAINTS FOR INSTALLMENT_CONTRACTS
-- ====================================================================

ALTER TABLE `installment_contracts`
    ADD CONSTRAINT `fk_installment_contracts_invoice` 
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `installment_contracts`
    ADD CONSTRAINT `fk_installment_contracts_customer` 
    FOREIGN KEY (`customer_id`) REFERENCES `client_list` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `installment_contracts`
    ADD CONSTRAINT `fk_installment_contracts_plan` 
    FOREIGN KEY (`installment_plan_id`) REFERENCES `installment_plans` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- ====================================================================
-- ADD FOREIGN KEY CONSTRAINTS FOR INSTALLMENT_SCHEDULE
-- ====================================================================

ALTER TABLE `installment_schedule`
    ADD CONSTRAINT `fk_installment_schedule_contract` 
    FOREIGN KEY (`contract_id`) REFERENCES `installment_contracts` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

-- ====================================================================
-- ADD FOREIGN KEY CONSTRAINTS FOR INSTALLMENT_PAYMENTS
-- ====================================================================

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
-- FOREIGN KEY CONSTRAINTS ADDED SUCCESSFULLY
-- ====================================================================
-- 
-- Summary:
-- - Added 3 foreign keys to installment_contracts
-- - Added 1 foreign key to installment_schedule  
-- - Added 3 foreign keys to installment_payments
-- - Total: 7 foreign key constraints
-- 
-- Relationships:
-- installment_contracts -> invoices (invoice_id)
-- installment_contracts -> client_list (customer_id)
-- installment_contracts -> installment_plans (installment_plan_id)
-- installment_schedule -> installment_contracts (contract_id)
-- installment_payments -> installment_schedule (schedule_id)
-- installment_payments -> installment_contracts (contract_id)
-- installment_payments -> users (created_by)
-- ====================================================================

