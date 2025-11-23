-- Fix Receipt Creation - Align Tables
-- This script ensures all tables are properly aligned for receipt creation

-- 1. Ensure receipts table has all required columns with proper defaults
ALTER TABLE `receipts`
  MODIFY COLUMN `archive_flag` TINYINT(1) NOT NULL DEFAULT 0;

-- 2. Ensure receipt_number has UNIQUE constraint
ALTER TABLE `receipts`
  ADD UNIQUE INDEX IF NOT EXISTS `unique_receipt_number` (`receipt_number`);

-- 3. Ensure acknowledgment_note has a default value
ALTER TABLE `receipts`
  MODIFY COLUMN `acknowledgment_note` TEXT DEFAULT 'Thank you for your purchase at Star Honda Calamba!';

-- 4. Ensure notes can be NULL
ALTER TABLE `receipts`
  MODIFY COLUMN `notes` TEXT DEFAULT NULL;

-- 5. Verify foreign key constraints exist (if needed)
-- Note: Add these if they don't exist and you want referential integrity
-- ALTER TABLE `receipts`
--   ADD CONSTRAINT `fk_receipts_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE,
--   ADD CONSTRAINT `fk_receipts_customer` FOREIGN KEY (`customer_id`) REFERENCES `client_list`(`id`) ON DELETE CASCADE,
--   ADD CONSTRAINT `fk_receipts_received_by` FOREIGN KEY (`received_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT;

-- 6. Create index for faster lookups
CREATE INDEX IF NOT EXISTS `idx_receipts_invoice_customer` ON `receipts`(`invoice_id`, `customer_id`);
CREATE INDEX IF NOT EXISTS `idx_receipts_issued_at` ON `receipts`(`issued_at`);

SELECT 'Receipts table alignment complete!' AS message;

