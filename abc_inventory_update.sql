-- ABC Inventory Modeling Database Update
-- This script adds ABC classification and enhanced inventory management features

-- 1. Add ABC classification fields to product_list table
ALTER TABLE `product_list` 
ADD COLUMN `abc_category` ENUM('A', 'B', 'C') DEFAULT 'C' AFTER `price`,
ADD COLUMN `reorder_point` int(11) DEFAULT 0 AFTER `abc_category`,
ADD COLUMN `max_stock` int(11) DEFAULT 0 AFTER `reorder_point`,
ADD COLUMN `min_stock` int(11) DEFAULT 0 AFTER `max_stock`,
ADD COLUMN `unit_cost` decimal(10,2) DEFAULT 0.00 AFTER `min_stock`,
ADD COLUMN `supplier_id` int(11) DEFAULT NULL AFTER `unit_cost`,
ADD COLUMN `lead_time_days` int(11) DEFAULT 7 AFTER `supplier_id`;

-- 2. Create stock_movements table for detailed tracking
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `movement_type` ENUM('IN', 'OUT', 'ADJUSTMENT', 'RETURN', 'DAMAGE') NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `previous_stock` decimal(10,2) NOT NULL,
  `new_stock` decimal(10,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `reference_id` varchar(50) DEFAULT NULL,
  `reference_type` ENUM('ORDER', 'PURCHASE', 'ADJUSTMENT', 'RETURN', 'DAMAGE') DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `movement_type` (`movement_type`),
  KEY `date_created` (`date_created`),
  KEY `reference_id` (`reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create product_recommendations table
CREATE TABLE IF NOT EXISTS `product_recommendations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `recommended_product_id` int(11) NOT NULL,
  `recommendation_type` ENUM('SUBSTITUTE', 'COMPLEMENTARY', 'UPGRADE', 'CROSS_SELL') NOT NULL,
  `priority` int(11) DEFAULT 1,
  `reason` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_recommendation` (`product_id`, `recommended_product_id`),
  KEY `product_id` (`product_id`),
  KEY `recommended_product_id` (`recommended_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Create suppliers table
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `delete_flag` (`delete_flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Create inventory_alerts table
CREATE TABLE IF NOT EXISTS `inventory_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `alert_type` ENUM('LOW_STOCK', 'OUT_OF_STOCK', 'REORDER_POINT', 'OVERSTOCK') NOT NULL,
  `current_stock` decimal(10,2) NOT NULL,
  `threshold_value` decimal(10,2) NOT NULL,
  `message` text NOT NULL,
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0,
  `resolved_by` int(11) DEFAULT NULL,
  `resolved_date` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `alert_type` (`alert_type`),
  KEY `is_resolved` (`is_resolved`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Create inventory_settings table
CREATE TABLE IF NOT EXISTS `inventory_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Insert default inventory settings
INSERT IGNORE INTO `inventory_settings` (`setting_key`, `setting_value`, `description`) VALUES
('abc_category_a_threshold', '80', 'Percentage threshold for Category A items (top 80% of value)'),
('abc_category_b_threshold', '95', 'Percentage threshold for Category B items (80-95% of value)'),
('abc_category_c_threshold', '100', 'Percentage threshold for Category C items (remaining 5%)'),
('low_stock_alert_percentage', '20', 'Alert when stock is below this percentage of reorder point'),
('overstock_alert_percentage', '150', 'Alert when stock is above this percentage of max stock'),
('auto_abc_classification', '1', 'Enable automatic ABC classification based on sales value'),
('stock_movement_tracking', '1', 'Enable detailed stock movement tracking'),
('product_recommendations', '1', 'Enable product recommendation system');

-- 8. Add indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_product_list_abc_category` ON `product_list` (`abc_category`);
CREATE INDEX IF NOT EXISTS `idx_product_list_reorder_point` ON `product_list` (`reorder_point`);
CREATE INDEX IF NOT EXISTS `idx_stock_list_product_type` ON `stock_list` (`product_id`, `type`);
CREATE INDEX IF NOT EXISTS `idx_order_items_product` ON `order_items` (`product_id`);

-- 9. Insert sample suppliers
INSERT IGNORE INTO `suppliers` (`name`, `contact_person`, `email`, `phone`, `address`) VALUES
('Yamaha Philippines', 'John Smith', 'john.smith@yamaha.ph', '+63 912 345 6789', 'Makati City, Philippines'),
('Kawasaki Philippines', 'Maria Garcia', 'maria.garcia@kawasaki.ph', '+63 923 456 7890', 'Quezon City, Philippines'),
('BMW Motorrad Philippines', 'Robert Johnson', 'robert.johnson@bmw.ph', '+63 934 567 8901', 'Taguig City, Philippines'),
('Generic Parts Supplier', 'Generic Contact', 'contact@genericparts.ph', '+63 945 678 9012', 'Manila, Philippines');

-- 10. Update existing products with default ABC categories based on price
UPDATE `product_list` SET 
  `abc_category` = CASE 
    WHEN `price` >= 10000 THEN 'A'
    WHEN `price` >= 2000 THEN 'B'
    ELSE 'C'
  END,
  `reorder_point` = CASE 
    WHEN `price` >= 10000 THEN 5
    WHEN `price` >= 2000 THEN 10
    ELSE 20
  END,
  `max_stock` = CASE 
    WHEN `price` >= 10000 THEN 20
    WHEN `price` >= 2000 THEN 50
    ELSE 100
  END,
  `min_stock` = CASE 
    WHEN `price` >= 10000 THEN 2
    WHEN `price` >= 2000 THEN 5
    ELSE 10
  END
WHERE `delete_flag` = 0;

-- 11. Insert sample product recommendations
INSERT IGNORE INTO `product_recommendations` (`product_id`, `recommended_product_id`, `recommendation_type`, `priority`, `reason`) VALUES
(1, 3, 'COMPLEMENTARY', 1, 'Oil is often needed with crash guard installation'),
(3, 4, 'SUBSTITUTE', 2, 'Alternative oil brand'),
(4, 1, 'CROSS_SELL', 1, 'Crash guard often purchased with new tires');

-- 12. Create view for ABC analysis
CREATE OR REPLACE VIEW `abc_analysis_view` AS
SELECT 
    p.id,
    p.name,
    p.abc_category,
    p.price,
    p.reorder_point,
    p.max_stock,
    p.min_stock,
    COALESCE(s.total_stock, 0) as current_stock,
    COALESCE(o.total_ordered, 0) as total_ordered,
    (COALESCE(s.total_stock, 0) - COALESCE(o.total_ordered, 0)) as available_stock,
    CASE 
        WHEN (COALESCE(s.total_stock, 0) - COALESCE(o.total_ordered, 0)) <= p.reorder_point THEN 'LOW_STOCK'
        WHEN (COALESCE(s.total_stock, 0) - COALESCE(o.total_ordered, 0)) >= p.max_stock THEN 'OVERSTOCK'
        ELSE 'NORMAL'
    END as stock_status
FROM product_list p
LEFT JOIN (
    SELECT product_id, SUM(quantity) as total_stock 
    FROM stock_list 
    WHERE type = 1 
    GROUP BY product_id
) s ON p.id = s.product_id
LEFT JOIN (
    SELECT oi.product_id, SUM(oi.quantity) as total_ordered
    FROM order_items oi
    JOIN order_list ol ON oi.order_id = ol.id
    WHERE ol.status != 5
    GROUP BY oi.product_id
) o ON p.id = o.product_id
WHERE p.delete_flag = 0;
