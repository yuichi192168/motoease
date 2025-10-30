-- Regenerate & Align Invoice Management Structure
-- Adds computed support for balances, late fees, payment date, and consistent statuses

-- 1) Ensure base tables exist (no-op if already created by create_invoicing_tables.sql)
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(30) NOT NULL,
  `service_request_id` int(30) DEFAULT NULL,
  `invoice_number` varchar(50) NOT NULL UNIQUE,
  `customer_id` int(30) NOT NULL,
  `transaction_type` enum('motorcycle_purchase','service','parts') NOT NULL DEFAULT 'motorcycle_purchase',
  `payment_type` enum('cash','installment') NOT NULL DEFAULT 'cash',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `vat_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('pending','paid','partial','late') NOT NULL DEFAULT 'pending',
  `pickup_location` varchar(255) NOT NULL,
  `pickup_instructions` text DEFAULT NULL,
  `payment_instructions` text DEFAULT NULL,
  `generated_by` int(30) NOT NULL,
  `generated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `customer_id` (`customer_id`),
  KEY `invoice_number` (`invoice_number`),
  KEY `payment_status` (`payment_status`),
  KEY `generated_at` (`generated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `item_type` enum('motorcycle','part','service','accessory') NOT NULL,
  `item_id` int(30) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `receipt_number` varchar(50) NOT NULL UNIQUE,
  `customer_id` int(30) NOT NULL,
  `amount_paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','card','bank_transfer','check') NOT NULL DEFAULT 'cash',
  `payment_reference` varchar(100) DEFAULT NULL,
  `received_by` int(30) NOT NULL,
  `issued_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `acknowledgment_note` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `customer_id` (`customer_id`),
  KEY `issued_at` (`issued_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `invoice_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2) Backfill/ensure new settings for late fee computation
INSERT INTO `invoice_settings` (`setting_key`, `setting_value`, `description`) VALUES
('late_fee_daily_rate', '0.50', 'Late fee daily rate in percent (e.g., 0.50 = 0.5%/day)')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- 3) Align columns on existing tables
ALTER TABLE `invoices`
  MODIFY `payment_status` enum('pending','paid','partial','late') NOT NULL DEFAULT 'pending',
  ADD COLUMN IF NOT EXISTS `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 4) Computed financials view
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
  (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) AS balance_remaining,
  CASE 
    WHEN COALESCE(SUM(r.amount_paid),0) >= i.total_amount THEN 'paid'
    WHEN i.due_date IS NOT NULL AND CURDATE() > i.due_date AND (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) > 0 THEN 'late'
    WHEN (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) > 0 THEN 'pending'
    ELSE 'paid'
  END AS computed_status,
  CASE 
    WHEN i.due_date IS NOT NULL AND CURDATE() > i.due_date AND (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) > 0 THEN 
      GREATEST(DATEDIFF(CURDATE(), i.due_date), 0) * (
        i.total_amount * (
          (SELECT CAST(setting_value AS DECIMAL(10,4)) / 100 FROM invoice_settings WHERE setting_key = 'late_fee_daily_rate' LIMIT 1)
        )
      )
    ELSE 0
  END AS late_fee_amount
FROM invoices i
LEFT JOIN receipts r ON r.invoice_id = i.id
GROUP BY i.id;

-- 5) Triggers to keep invoice updated_at fresh on related changes
DROP TRIGGER IF EXISTS trg_receipts_after_ins;
DELIMITER $$
CREATE TRIGGER trg_receipts_after_ins AFTER INSERT ON receipts
FOR EACH ROW
BEGIN
  UPDATE invoices SET updated_at = NOW() WHERE id = NEW.invoice_id;
  -- Sync customer account balance from financials
  UPDATE client_list c SET c.account_balance = (
    SELECT COALESCE(SUM(balance_remaining),0) FROM invoice_financials fin WHERE fin.customer_id = c.id
  )
  WHERE c.id = (SELECT customer_id FROM invoices WHERE id = NEW.invoice_id);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS trg_receipts_after_upd;
DELIMITER $$
CREATE TRIGGER trg_receipts_after_upd AFTER UPDATE ON receipts
FOR EACH ROW
BEGIN
  UPDATE invoices SET updated_at = NOW() WHERE id = NEW.invoice_id;
  UPDATE client_list c SET c.account_balance = (
    SELECT COALESCE(SUM(balance_remaining),0) FROM invoice_financials fin WHERE fin.customer_id = c.id
  )
  WHERE c.id = (SELECT customer_id FROM invoices WHERE id = NEW.invoice_id);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS trg_receipts_after_del;
DELIMITER $$
CREATE TRIGGER trg_receipts_after_del AFTER DELETE ON receipts
FOR EACH ROW
BEGIN
  UPDATE invoices SET updated_at = NOW() WHERE id = OLD.invoice_id;
  UPDATE client_list c SET c.account_balance = (
    SELECT COALESCE(SUM(balance_remaining),0) FROM invoice_financials fin WHERE fin.customer_id = c.id
  )
  WHERE c.id = (SELECT customer_id FROM invoices WHERE id = OLD.invoice_id);
END$$
DELIMITER ;

-- Optional: if order totals change, reflect on invoice timestamp
DROP TRIGGER IF EXISTS trg_order_list_after_upd_invoice_touch;
DELIMITER $$
CREATE TRIGGER trg_order_list_after_upd_invoice_touch AFTER UPDATE ON order_list
FOR EACH ROW
BEGIN
  UPDATE invoices SET updated_at = NOW() WHERE order_id = NEW.id;
  -- also refresh customer balance for affected customer
  UPDATE client_list c SET c.account_balance = (
    SELECT COALESCE(SUM(balance_remaining),0) FROM invoice_financials fin WHERE fin.customer_id = c.id
  )
  WHERE c.id = NEW.client_id;
END$$
DELIMITER ;

-- Keep balances in sync when invoice rows change
DROP TRIGGER IF EXISTS trg_invoices_after_ins_balance;
DELIMITER $$
CREATE TRIGGER trg_invoices_after_ins_balance AFTER INSERT ON invoices
FOR EACH ROW
BEGIN
  UPDATE client_list c SET c.account_balance = (
    SELECT COALESCE(SUM(balance_remaining),0) FROM invoice_financials fin WHERE fin.customer_id = c.id
  )
  WHERE c.id = NEW.customer_id;
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS trg_invoices_after_upd_balance;
DELIMITER $$
CREATE TRIGGER trg_invoices_after_upd_balance AFTER UPDATE ON invoices
FOR EACH ROW
BEGIN
  UPDATE client_list c SET c.account_balance = (
    SELECT COALESCE(SUM(balance_remaining),0) FROM invoice_financials fin WHERE fin.customer_id = c.id
  )
  WHERE c.id = NEW.customer_id;
END$$
DELIMITER ;

-- 6) Helpful indexes
CREATE INDEX IF NOT EXISTS idx_invoices_customer_status ON invoices(customer_id, payment_status);
CREATE INDEX IF NOT EXISTS idx_invoices_dates ON invoices(generated_at, due_date);
CREATE INDEX IF NOT EXISTS idx_receipts_invoice ON receipts(invoice_id, issued_at);

-- 7) Sample data (optional) - five example invoices
-- Note: Adjust customer_id/order_id to existing IDs in your DB if needed
INSERT INTO invoices (
  order_id, service_request_id, invoice_number, customer_id, transaction_type, payment_type,
  subtotal, vat_amount, total_amount, payment_status, pickup_location, payment_instructions,
  generated_by, generated_at, due_date, notes
) VALUES
  (0, NULL, 'INV-2025-9001', 8,  'motorcycle_purchase', 'cash',        120000.00, 14400.00, 134400.00, 'pending', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', 'Payment must be completed in-store. No online payment available.', 1, DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'Pending payment'),
  (0, NULL, 'INV-2025-9002', 8,  'motorcycle_purchase', 'cash',        155000.00, 18600.00, 173600.00, 'late',    'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', 'Payment must be completed in-store. No online payment available.', 1, DATE_SUB(NOW(), INTERVAL 40 DAY), DATE_SUB(CURDATE(), INTERVAL 30 DAY), 'Overdue invoice'),
  (0, NULL, 'INV-2025-9003', 9,  'parts',               'cash',         10000.00,  1200.00,  11200.00,  'partial', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', 'Payment must be completed in-store. No online payment available.', 1, DATE_SUB(NOW(), INTERVAL 5 DAY),  DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'Partially paid'),
  (0, NULL, 'INV-2025-9004', 10, 'service',             'cash',          3500.00,   420.00,   3920.00,  'paid',    'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', 'Payment must be completed in-store. No online payment available.', 1, DATE_SUB(NOW(), INTERVAL 1 DAY),  DATE_ADD(CURDATE(), INTERVAL 6 DAY), 'Fully paid service'),
  (0, NULL, 'INV-2025-9005', 8,  'motorcycle_purchase', 'installment',  90000.00, 10800.00, 100800.00, 'pending', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', 'Payment must be completed in-store. No online payment available.', 1, NOW(),                          DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'Installment plan');

-- Add minimal invoice_items for samples
INSERT INTO invoice_items (invoice_id, item_type, item_id, item_name, item_description, quantity, unit_price, total_price)
SELECT i.id, 'motorcycle', 0, 'Sample Motorcycle', 'Sample item', 1, i.subtotal, i.subtotal FROM invoices i WHERE i.invoice_number IN ('INV-2025-9001','INV-2025-9002','INV-2025-9005');

INSERT INTO invoice_items (invoice_id, item_type, item_id, item_name, item_description, quantity, unit_price, total_price)
SELECT i.id, 'part', 0, 'Sample Parts', 'Sample parts item', 2, 5000.00, 10000.00 FROM invoices i WHERE i.invoice_number = 'INV-2025-9003';

INSERT INTO invoice_items (invoice_id, item_type, item_id, item_name, item_description, quantity, unit_price, total_price)
SELECT i.id, 'service', 0, 'General Service', 'Sample service job', 1, 3500.00, 3500.00 FROM invoices i WHERE i.invoice_number = 'INV-2025-9004';

-- Receipts for partial/paid examples
-- Partial payment for INV-2025-9003 (pay 5,000 of 11,200)
INSERT INTO receipts (invoice_id, receipt_number, customer_id, amount_paid, payment_method, payment_reference, received_by, issued_at, acknowledgment_note)
SELECT i.id, 'RCPT-2025-9003A', i.customer_id, 5000.00, 'cash', '', 1, DATE_SUB(NOW(), INTERVAL 3 DAY), 'Thank you' FROM invoices i WHERE i.invoice_number = 'INV-2025-9003';

-- Full payment for INV-2025-9004
INSERT INTO receipts (invoice_id, receipt_number, customer_id, amount_paid, payment_method, payment_reference, received_by, issued_at, acknowledgment_note)
SELECT i.id, 'RCPT-2025-9004A', i.customer_id, i.total_amount, 'cash', '', 1, NOW(), 'Thank you' FROM invoices i WHERE i.invoice_number = 'INV-2025-9004';

-- 8) Align service_requests with order_list via order_id
-- Add column if missing
ALTER TABLE `service_requests` 
  ADD COLUMN IF NOT EXISTS `order_id` int(30) NULL AFTER `id`;

-- Backfill order_id: pick the most recent order by same client not after the request date
UPDATE service_requests sr
LEFT JOIN (
    SELECT ol1.* FROM order_list ol1
) ol ON ol.client_id = sr.client_id
SET sr.order_id = (
    SELECT ol2.id FROM order_list ol2
    WHERE ol2.client_id = sr.client_id
      AND (ol2.date_created IS NULL OR ol2.date_created <= sr.date_created)
    ORDER BY ol2.date_created DESC
    LIMIT 1
)
WHERE (sr.order_id IS NULL OR sr.order_id = 0);

-- Add FK and index
ALTER TABLE `service_requests` 
  ADD INDEX IF NOT EXISTS `idx_sr_order` (`order_id`);
ALTER TABLE `service_requests`
  ADD CONSTRAINT `fk_service_requests_order_list`
  FOREIGN KEY (`order_id`) REFERENCES `order_list`(`id`) ON DELETE CASCADE;

-- After backfill, enforce NOT NULL if feasible
-- Note: If some rows still lack a matching order, keep it NULL and fix manually
-- ALTER TABLE `service_requests` MODIFY `order_id` int(30) NOT NULL;


