-- Database Synchronization Fixes for BPSMS
-- This script ensures all tables and constraints are properly aligned between client and admin

-- 1. Create or_cr_documents table if it doesn't exist
CREATE TABLE IF NOT EXISTS `or_cr_documents` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `client_id` int(30) NOT NULL,
  `document_type` enum('or','cr') NOT NULL,
  `document_number` varchar(100) NOT NULL,
  `plate_number` varchar(20) DEFAULT NULL,
  `vehicle_model` varchar(100) DEFAULT NULL,
  `vehicle_brand` varchar(100) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('pending','released','expired') NOT NULL DEFAULT 'pending',
  `file_path` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_or_cr_documents_client_id` (`client_id`),
  KEY `idx_or_cr_documents_status` (`status`),
  CONSTRAINT `fk_or_cr_documents_client` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Add missing columns to client_list if they don't exist
ALTER TABLE `client_list` 
ADD COLUMN IF NOT EXISTS `avatar` text DEFAULT NULL AFTER `password`,
ADD COLUMN IF NOT EXISTS `credit_application_completed` tinyint(1) DEFAULT 0 AFTER `avatar`;

-- 3. Ensure proper indexes exist for performance
CREATE INDEX IF NOT EXISTS `idx_order_list_client_id` ON `order_list` (`client_id`);
CREATE INDEX IF NOT EXISTS `idx_order_list_status` ON `order_list` (`status`);
CREATE INDEX IF NOT EXISTS `idx_order_list_date_created` ON `order_list` (`date_created`);

CREATE INDEX IF NOT EXISTS `idx_service_requests_client_id` ON `service_requests` (`client_id`);
CREATE INDEX IF NOT EXISTS `idx_service_requests_status` ON `service_requests` (`status`);
CREATE INDEX IF NOT EXISTS `idx_service_requests_date_created` ON `service_requests` (`date_created`);

CREATE INDEX IF NOT EXISTS `idx_order_items_order_id` ON `order_items` (`order_id`);
CREATE INDEX IF NOT EXISTS `idx_order_items_product_id` ON `order_items` (`product_id`);

CREATE INDEX IF NOT EXISTS `idx_cart_list_client_id` ON `cart_list` (`client_id`);
CREATE INDEX IF NOT EXISTS `idx_cart_list_product_id` ON `cart_list` (`product_id`);

-- 4. Update foreign key constraints to ensure data integrity
-- Note: These will only add constraints if they don't already exist
ALTER TABLE `order_list` 
ADD CONSTRAINT IF NOT EXISTS `fk_order_list_client` 
FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

ALTER TABLE `service_requests` 
ADD CONSTRAINT IF NOT EXISTS `fk_service_requests_client` 
FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

ALTER TABLE `service_requests` 
ADD CONSTRAINT IF NOT EXISTS `fk_service_requests_mechanic` 
FOREIGN KEY (`mechanic_id`) REFERENCES `mechanics_list` (`id`) ON DELETE SET NULL;

ALTER TABLE `order_items` 
ADD CONSTRAINT IF NOT EXISTS `fk_order_items_order` 
FOREIGN KEY (`order_id`) REFERENCES `order_list` (`id`) ON DELETE CASCADE;

ALTER TABLE `order_items` 
ADD CONSTRAINT IF NOT EXISTS `fk_order_items_product` 
FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE;

ALTER TABLE `cart_list` 
ADD CONSTRAINT IF NOT EXISTS `fk_cart_list_client` 
FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

ALTER TABLE `cart_list` 
ADD CONSTRAINT IF NOT EXISTS `fk_cart_list_product` 
FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE;

-- 5. Optimize tables for better performance
OPTIMIZE TABLE `client_list`, `order_list`, `service_requests`, `order_items`, `cart_list`, `product_list`, `or_cr_documents`;

-- 6. Create a view for consistent order status reporting
CREATE OR REPLACE VIEW `order_status_summary` AS
SELECT 
    o.id,
    o.ref_code,
    o.client_id,
    CONCAT(c.lastname, ', ', c.firstname, ' ', COALESCE(c.middlename, '')) as client_name,
    o.total_amount,
    o.status,
    CASE 
        WHEN o.status = 0 THEN 'Pending'
        WHEN o.status = 1 THEN 'Ready for Pickup'
        WHEN o.status = 2 THEN 'For Delivery'
        WHEN o.status = 3 THEN 'On the Way'
        WHEN o.status = 4 THEN 'Delivered'
        WHEN o.status = 6 THEN 'Claimed'
        WHEN o.status = 5 THEN 'Cancelled'
        ELSE 'Unknown'
    END as status_text,
    o.date_created,
    o.date_updated
FROM `order_list` o
INNER JOIN `client_list` c ON o.client_id = c.id
WHERE c.delete_flag = 0;

-- 7. Create a view for consistent service request reporting
CREATE OR REPLACE VIEW `service_request_summary` AS
SELECT 
    s.id,
    s.client_id,
    CONCAT(c.lastname, ', ', c.firstname, ' ', COALESCE(c.middlename, '')) as client_name,
    s.service_type,
    s.status,
    CASE 
        WHEN s.status = 0 THEN 'Pending'
        WHEN s.status = 1 THEN 'Confirmed'
        WHEN s.status = 2 THEN 'On-progress'
        WHEN s.status = 3 THEN 'Done'
        WHEN s.status = 4 THEN 'Cancelled'
        ELSE 'Unknown'
    END as status_text,
    s.date_created,
    m.name as mechanic_name
FROM `service_requests` s
INNER JOIN `client_list` c ON s.client_id = c.id
LEFT JOIN `mechanics_list` m ON s.mechanic_id = m.id
WHERE c.delete_flag = 0;
