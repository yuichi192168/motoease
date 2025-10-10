-- Invoicing Module Database Tables for Star Honda Calamba
-- Motorcycle Management System

-- Create invoices table
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(30) NOT NULL,
  `service_request_id` int(30) DEFAULT NULL COMMENT 'For service invoices',
  `invoice_number` varchar(50) NOT NULL UNIQUE,
  `customer_id` int(30) NOT NULL,
  `transaction_type` enum('motorcycle_purchase','service','parts') NOT NULL DEFAULT 'motorcycle_purchase',
  `payment_type` enum('cash','installment') NOT NULL DEFAULT 'cash',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `vat_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('unpaid','paid','partial') NOT NULL DEFAULT 'unpaid',
  `pickup_location` varchar(255) NOT NULL DEFAULT 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna',
  `pickup_instructions` text DEFAULT NULL,
  `payment_instructions` text DEFAULT 'Payment must be completed in-store. No online payment available.',
  `generated_by` int(30) NOT NULL COMMENT 'Staff who generated the invoice',
  `generated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `customer_id` (`customer_id`),
  KEY `invoice_number` (`invoice_number`),
  KEY `payment_status` (`payment_status`),
  KEY `generated_at` (`generated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create invoice_items table for detailed line items
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `item_type` enum('motorcycle','part','service','accessory') NOT NULL,
  `item_id` int(30) NOT NULL COMMENT 'Reference to product_list, service_list, etc.',
  `item_name` varchar(255) NOT NULL,
  `item_description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `item_type` (`item_type`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create receipts table
CREATE TABLE IF NOT EXISTS `receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `receipt_number` varchar(50) NOT NULL UNIQUE,
  `customer_id` int(30) NOT NULL,
  `amount_paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','card','bank_transfer','check') NOT NULL DEFAULT 'cash',
  `payment_reference` varchar(100) DEFAULT NULL COMMENT 'Transaction reference, check number, etc.',
  `received_by` int(30) NOT NULL COMMENT 'Staff who received payment',
  `issued_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `acknowledgment_note` text DEFAULT 'Thank you for your purchase at Star Honda Calamba!',
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `customer_id` (`customer_id`),
  KEY `receipt_number` (`receipt_number`),
  KEY `issued_at` (`issued_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create invoice_settings table for system configuration
CREATE TABLE IF NOT EXISTS `invoice_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default invoice settings
INSERT INTO `invoice_settings` (`setting_key`, `setting_value`, `description`) VALUES
('invoice_prefix', 'INV', 'Prefix for invoice numbers'),
('receipt_prefix', 'RCPT', 'Prefix for receipt numbers'),
('vat_rate', '12', 'VAT rate percentage'),
('company_name', 'Star Honda Calamba', 'Company name for invoices'),
('company_address', 'National Highway Brgy. Parian, Calamba City, Laguna', 'Company address'),
('company_phone', '0948-235-3207', 'Company phone number'),
('company_email', 'starhondacalamba55@gmail.com', 'Company email'),
('pickup_location', 'Star Honda Calamba - National Highway Brgy. Parian, Calamba City, Laguna', 'Default pickup location'),
('payment_instructions', 'Payment must be completed in-store. No online payment available. Please bring valid ID and payment method.', 'Default payment instructions'),
('acknowledgment_note', 'Thank you for your purchase at Star Honda Calamba! We appreciate your business and look forward to serving you again.', 'Default acknowledgment note');

-- Create indexes for better performance
CREATE INDEX idx_invoices_customer_status ON invoices(customer_id, payment_status);
CREATE INDEX idx_invoices_generated_date ON invoices(generated_at);
CREATE INDEX idx_receipts_customer_date ON receipts(customer_id, issued_at);
CREATE INDEX idx_invoice_items_invoice ON invoice_items(invoice_id);

-- Add foreign key constraints (optional, for data integrity)
-- ALTER TABLE invoices ADD CONSTRAINT fk_invoices_order FOREIGN KEY (order_id) REFERENCES order_list(id) ON DELETE CASCADE;
-- ALTER TABLE invoices ADD CONSTRAINT fk_invoices_customer FOREIGN KEY (customer_id) REFERENCES client_list(id) ON DELETE CASCADE;
-- ALTER TABLE invoice_items ADD CONSTRAINT fk_invoice_items_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE;
-- ALTER TABLE receipts ADD CONSTRAINT fk_receipts_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE;
-- ALTER TABLE receipts ADD CONSTRAINT fk_receipts_customer FOREIGN KEY (customer_id) REFERENCES client_list(id) ON DELETE CASCADE;



