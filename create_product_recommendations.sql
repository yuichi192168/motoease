-- Create product recommendations table
CREATE TABLE IF NOT EXISTS `product_recommendations` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `product_id` int(30) NOT NULL,
  `recommended_product_id` int(30) NOT NULL,
  `recommendation_type` enum('alternative','similar','upgrade','cross_sell') NOT NULL DEFAULT 'alternative',
  `priority` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `recommended_product_id` (`recommended_product_id`),
  KEY `recommendation_type` (`recommendation_type`),
  KEY `is_active` (`is_active`),
  CONSTRAINT `product_recommendations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_recommendations_ibfk_2` FOREIGN KEY (`recommended_product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create product availability notifications table
CREATE TABLE IF NOT EXISTS `product_availability_notifications` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `client_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `is_notified` tinyint(1) DEFAULT 0,
  `date_requested` datetime NOT NULL DEFAULT current_timestamp(),
  `date_notified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `product_id` (`product_id`),
  KEY `is_notified` (`is_notified`),
  UNIQUE KEY `unique_notification` (`client_id`, `product_id`),
  CONSTRAINT `product_availability_notifications_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_availability_notifications_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

